<?php
App::import('model', 'TipoContato');
App::import('model', 'TipoRetorno');
class RegistroImportacao extends AppModel
{
    var $name = 'RegistroImportacao';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'registros_importacao';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array())
    {
        $joins = null;
        if (isset($extra['joins']))
            $joins = $extra['joins'];
        if (isset($extra['group']))
            $group = $extra['group'];
        if (isset($extra['extra']['importacao']) && $extra['extra']['importacao']) {
            return $this->findImportacao('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
        }

        // pr($this->find('sql', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins')));
        return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
    } //FINAL FUNCTION paginate

    public function paginateCount($conditions = null, $recursive = 0, $extra = array())
    {
        $joins = null;
        if (isset($extra['joins']))
            $joins = $extra['joins'];
        if (isset($extra['extra']['importacao']) && $extra['extra']['importacao']) {
            return $this->findImportacao('count', compact('conditions', 'recursive', 'joins'));
        }
        return $this->find('count', compact('conditions', 'recursive', 'joins'));
    } //FINAL FUNCTION paginateCount

    public function findImportacao($findType, $options)
    {
        $ClienteContato = &ClassRegistry::init('ClienteContato');
        $ClienteFuncionario = &ClassRegistry::init('ClienteFuncionario');
        $Funcionario = &ClassRegistry::init('Funcionario');
        $FuncionarioEndereco = &ClassRegistry::init('FuncionarioEndereco');
        $VEndereco = &ClassRegistry::init('VEndereco');
        $FuncionarioContato = &ClassRegistry::init('FuncionarioContato');
        $FuncionarioSetorCargo = &ClassRegistry::init('FuncionarioSetorCargo');
        $Setor = &ClassRegistry::init('Setor');
        $Cargo = &ClassRegistry::init('Cargo');
        $this->bindModel(array('belongsTo' => array(
            'ImportacaoEstrutura' => array('foreignKey' => 'codigo_importacao_estrutura'),
            'GrupoEconomico' => array('foreignKey' => false, 'conditions' => 'GrupoEconomico.codigo = ImportacaoEstrutura.codigo_grupo_economico'),
            'ClienteAlocacao' => array('className' => 'Cliente', 'foreignKey' => false, 'conditions' => array(
                'ClienteAlocacao.codigo_empresa = ImportacaoEstrutura.codigo_empresa',
                'ClienteAlocacao.codigo_externo = RegistroImportacao.codigo_externo_alocacao',
                array(
                    "RegistroImportacao.codigo_externo_alocacao IS NOT NULL",
                    "LTRIM(RTRIM(RegistroImportacao.codigo_externo_alocacao)) <> ''"
                )
            )),
            'ClienteEnderecoAlocacao' => array('className' => 'ClienteEndereco', 'foreignKey' => false, 'conditions' => array('ClienteEnderecoAlocacao.codigo_cliente = ClienteAlocacao.codigo', 'ClienteEnderecoAlocacao.codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL)),
            'VEnderecoAlocacao' => array('className' => 'VEndereco', 'foreignKey' => false, 'conditions' => 'VEnderecoAlocacao.endereco_codigo = ClienteEnderecoAlocacao.codigo_endereco'),
            'Cnae' => array('foreignKey' => false, 'conditions' => 'cnae.codigo = ClienteAlocacao.cnae'),
            'StatusImportacao' => array('foreignKey' => 'codigo_status_importacao'),
            'medicos' => array('className' => 'Medicos', 'foreignKey' => false, 'conditions' => array('ClienteAlocacao.codigo_medico_pcmso = Medicos.codigo')),
            'conselho_profissional' => array('className' => 'ConselhoProfissional', 'foreignKey' => false, 'conditions' => array('Medicos.codigo_conselho_profissional = conselho_profissional.codigo')),
        )));

        $fields = $this->findImportacaoBaseFields();
        $conditions = $options['conditions'];
        $query_base = $this->find('sql', compact('fields', 'conditions'));
        $dbo = $this->getDataSource();
        $cte = "WITH Base AS ($query_base)";

        $offset = (isset($options['page']) && $options['page'] > 1 ? (($options['page'] - 1) * $options['limit']) : null);
        $query = $dbo->buildStatement(array(
            'fields' => $this->findImportacaoFields(),
            'table' => "Base",
            'alias' => 'RegistroImportacao',
            'joins' => array(
                array(
                    'table' => $ClienteContato->databaseTable . "." . $ClienteContato->tableSchema . "." . $ClienteContato->useTable,
                    'alias' => 'AlocacaoContatoTelefone',
                    'conditions' => 'AlocacaoContatoTelefone.codigo = RegistroImportacao.cli_contato_codigo_telefone',
                    'type' => 'LEFT'
                ),
                array(
                    'table' => $ClienteContato->databaseTable . "." . $ClienteContato->tableSchema . "." . $ClienteContato->useTable,
                    'alias' => 'AlocacaoContatoEmail',
                    'conditions' => 'AlocacaoContatoEmail.codigo = RegistroImportacao.cli_contato_codigo_email',
                    'type' => 'LEFT'
                ),
                array(
                    'table' => $ClienteFuncionario->databaseTable . "." . $ClienteFuncionario->tableSchema . "." . $ClienteFuncionario->useTable,
                    'alias' => 'ClienteFuncionario',
                    'conditions' => array(
                        'ClienteFuncionario.codigo_empresa = RegistroImportacao.codigo_empresa',
                        'ClienteFuncionario.codigo = RegistroImportacao.codigo_cliente_funcionario'
                    ),
                    'type' => 'LEFT'
                ),
                array(
                    'table' => $Funcionario->databaseTable . "." . $Funcionario->tableSchema . "." . $Funcionario->useTable,
                    'alias' => 'Funcionario',
                    'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
                    'type' => 'LEFT'
                ),
                array(
                    'table' => $FuncionarioEndereco->databaseTable . "." . $FuncionarioEndereco->tableSchema . "." . $FuncionarioEndereco->useTable,
                    'alias' => 'FuncionarioEndereco',
                    'conditions' => array(
                        'FuncionarioEndereco.codigo_funcionario = Funcionario.codigo',
                        'FuncionarioEndereco.codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL
                    ),
                    'type' => 'LEFT'
                ),
                array(
                    'table' => $FuncionarioSetorCargo->databaseTable . "." . $FuncionarioSetorCargo->tableSchema . "." . $FuncionarioSetorCargo->useTable,
                    'alias' => 'FuncionarioSetorCargo',
                    'conditions' => 'FuncionarioSetorCargo.codigo = RegistroImportacao.codigo_func_setor_cargo',
                    'type' => 'LEFT'
                ),
                array(
                    'table' => $Setor->databaseTable . "." . $Setor->tableSchema . "." . $Setor->useTable,
                    'alias' => 'Setor',
                    'conditions' => array(
                        'Setor.codigo_empresa = FuncionarioSetorCargo.codigo_empresa',
                        'Setor.codigo = FuncionarioSetorCargo.codigo_setor'
                    ),
                    'type' => 'LEFT'
                ),
                array(
                    'table' => $Cargo->databaseTable . "." . $Cargo->tableSchema . "." . $Cargo->useTable,
                    'alias' => 'Cargo',
                    'conditions' => array(
                        'Cargo.codigo_empresa = FuncionarioSetorCargo.codigo_empresa',
                        'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo'
                    ),
                    'type' => 'LEFT'
                ),
            ),
            'limit' => (isset($options['limit']) ? $options['limit'] : null),
            'offset' => $offset,
            'conditions' => null,
            'order' => (isset($options['order']) ? $options['order'] : null),
            'group' => null,
        ), $this);

        if ($findType == 'sql') {
            return array('cte' => $cte, 'query' => $query);
        } elseif ($findType == 'count') {
            $result = $this->query("{$cte} SELECT COUNT(*) AS qtd FROM Base AS base");

            // debug("{$cte} SELECT COUNT(*) AS qtd FROM ({$query}) AS base");exit;

            return $result[0][0]['qtd'];
        }
        // debug($cte.$query);exit;
        return $this->query($cte . $query);
    } //FINAL FUNCTION findImportacao

    private function findImportacaoFields()
    {
        $FuncionarioContato = &ClassRegistry::init('FuncionarioContato');
        return array(
            'RegistroImportacao.codigo',
            'RegistroImportacao.codigo_empresa',
            'RegistroImportacao.acao_funcionario',
            'RegistroImportacao.status_importacao',
            'RegistroImportacao.codigo_alocacao',
            'RegistroImportacao.nome_alocacao',
            'RegistroImportacao.nome_setor',
            'RegistroImportacao.nome_cargo',
            'RegistroImportacao.existe_setor',
            'RegistroImportacao.existe_cargo',
            'RegistroImportacao.codigo_matricula',
            'RegistroImportacao.matricula_funcionario',
            'RegistroImportacao.nome_funcionario',
            'RegistroImportacao.data_nascimento',
            'RegistroImportacao.sexo',
            'RegistroImportacao.situacao_cadastral',
            'RegistroImportacao.data_admissao',
            'RegistroImportacao.data_demissao',
            'RegistroImportacao.data_inicio_cargo',
            'RegistroImportacao.estado_civil',
            'RegistroImportacao.pis_pasep',
            'RegistroImportacao.rg',
            'RegistroImportacao.estado_rg',
            'RegistroImportacao.cpf',
            'RegistroImportacao.ctps',
            'RegistroImportacao.serie_ctps',
            'RegistroImportacao.uf_ctps',
            'RegistroImportacao.endereco_funcionario',
            'RegistroImportacao.numero_funcionario',
            'RegistroImportacao.complemento_funcionario',
            'RegistroImportacao.bairro_funcionario',
            'RegistroImportacao.cidade_funcionario',
            'RegistroImportacao.estado_funcionario',
            'RegistroImportacao.cep_funcionario',
            'RegistroImportacao.possui_deficiencia',
            'RegistroImportacao.codigo_cbo',
            'RegistroImportacao.codigo_gfip',
            'RegistroImportacao.centro_custo',
            // 'RegistroImportacao.data_ultimo_aso',
            // 'RegistroImportacao.aptidao',
            'RegistroImportacao.turno',
            'RegistroImportacao.descricao_detalhada_cargo',
            'RegistroImportacao.celular_funcionario',
            'RegistroImportacao.autoriza_envio_sms_funcionario',
            'RegistroImportacao.email_funcionario',
            'RegistroImportacao.autoriza_envio_email_func',
            'RegistroImportacao.contato_responsavel_alocacao',
            'RegistroImportacao.telefone_responsavel_alocacao',
            'RegistroImportacao.email_responsavel_alocacao',
            'RegistroImportacao.endereco_alocacao',
            'RegistroImportacao.numero_alocacao',
            'RegistroImportacao.complemento_alocacao',
            'RegistroImportacao.bairro_alocacao',
            'RegistroImportacao.cidade_alocacao',
            'RegistroImportacao.estado_alocacao',
            'RegistroImportacao.cep_alocacao',
            'RegistroImportacao.cnpj_alocacao',
            'RegistroImportacao.inscricao_estadual',
            'RegistroImportacao.inscricao_municipal',
            'RegistroImportacao.cnae',
            'RegistroImportacao.grau_risco',
            'RegistroImportacao.razao_social_alocacao',
            'RegistroImportacao.unidade_negocio',
            'RegistroImportacao.regime_tributario',
            'RegistroImportacao.codigo_externo_alocacao',
            'RegistroImportacao.tipo_alocacao',
            'RegistroImportacao.cliente_alocacao_codigo',
            'FuncionarioSetorCargo.codigo_cliente_alocacao as cliente_alocacao_atual',
            'RegistroImportacao.cli_aloc_nome_fantasia',
            'RegistroImportacao.codigo_func_setor_cargo',
            'RegistroImportacao.observacao',
            'RegistroImportacao.chave_externa',
            'RegistroImportacao.codigo_cargo_externo',
            'Setor.descricao AS setor_descricao',
            'Cargo.descricao AS cargo_descricao',
            'RegistroImportacao.codigo_cliente_funcionario',
            'ClienteFuncionario.matricula AS cliente_funcionario_matricula',
            'Funcionario.nome AS funcionario_nome',
            'CONVERT(VARCHAR, Funcionario.data_nascimento, 103) AS funcionario_data_nascimento',
            'Funcionario.sexo AS funcionario_sexo',
            'ClienteFuncionario.ativo AS cliente_funcionario_status',
            'CONVERT(VARCHAR, ClienteFuncionario.admissao, 103) AS cliente_funcionario_admissao',
            'CONVERT(VARCHAR, ClienteFuncionario.data_demissao, 103) AS cliente_funcionario_demissao',
            'CONVERT(VARCHAR, FuncionarioSetorCargo.data_inicio, 103) AS func_setor_cargo_inicio',
            'Funcionario.estado_civil AS funcionario_estado_civil',
            'Funcionario.nit AS funcionario_nit',
            'Funcionario.rg AS funcionario_rg',
            'Funcionario.rg_uf AS funcionario_rg_uf',
            'Funcionario.cpf AS funcionario_cpf',
            'Funcionario.ctps AS funcionario_ctps',
            'Funcionario.ctps_serie AS funcionario_ctps_serie',
            'Funcionario.ctps_uf AS funcionario_ctps_uf',
            "FuncionarioEndereco.logradouro AS funcionario_endereco",
            'FuncionarioEndereco.numero AS funcionario_endereco_numero',
            'FuncionarioEndereco.complemento AS funcionario_endereco_compl',
            'FuncionarioEndereco.bairro AS funcionario_endereco_bairro',
            'FuncionarioEndereco.cidade AS funcionario_endereco_cidade',
            'FuncionarioEndereco.estado_abreviacao AS funcionario_endereco_uf',
            'FuncionarioEndereco.cep AS funcionario_endereco_cep',
            'Funcionario.deficiencia AS funcionario_deficiencia',
            'Cargo.codigo_cbo AS cargo_codigo_cbo',
            'Funcionario.gfip AS funcionario_gfip',
            'ClienteFuncionario.centro_custo AS cliente_func_centro_custo',
            'ClienteFuncionario.data_ultima_aso AS cliente_func_data_aso',
            'ClienteFuncionario.aptidao AS cliente_funcionario_aptidao',
            'ClienteFuncionario.turno AS cliente_funcionario_turno',
            'Cargo.descricao_cargo AS cargo_descricao_detalhada',
            "(SELECT TOP 1 descricao 
			  FROM {$FuncionarioContato->databaseTable}.{$FuncionarioContato->tableSchema}.{$FuncionarioContato->useTable} AS FuncionarioContato
			  WHERE FuncionarioContato.codigo_funcionario = Funcionario.codigo 
			    AND FuncionarioContato.codigo_tipo_retorno=" . TipoRetorno::TIPO_RETORNO_CELULAR_MOTORISTA . "
			    AND FuncionarioContato.codigo_tipo_contato=" . TipoContato::TIPO_CONTATO_COMERCIAL . "
			  ORDER BY FuncionarioContato.codigo
			) AS base_celular_funcionario",
            "(SELECT TOP 1 autoriza_envio_sms 
			  FROM {$FuncionarioContato->databaseTable}.{$FuncionarioContato->tableSchema}.{$FuncionarioContato->useTable} AS FuncionarioContato
			  WHERE FuncionarioContato.codigo_funcionario = Funcionario.codigo 
			    AND FuncionarioContato.codigo_tipo_retorno=" . TipoRetorno::TIPO_RETORNO_CELULAR_MOTORISTA . "
			    AND FuncionarioContato.codigo_tipo_contato=" . TipoContato::TIPO_CONTATO_COMERCIAL . "
			  ORDER BY FuncionarioContato.codigo
			) AS base_autoriza_sms",
            "(SELECT TOP 1 descricao 
			  FROM {$FuncionarioContato->databaseTable}.{$FuncionarioContato->tableSchema}.{$FuncionarioContato->useTable} AS FuncionarioContato
			  WHERE FuncionarioContato.codigo_funcionario = Funcionario.codigo 
			    AND FuncionarioContato.codigo_tipo_retorno=" . TipoRetorno::TIPO_RETORNO_EMAIL . "
			    AND FuncionarioContato.codigo_tipo_contato=" . TipoContato::TIPO_CONTATO_COMERCIAL . "
			  ORDER BY FuncionarioContato.codigo
			) AS base_email_funcionario",
            "(SELECT TOP 1 autoriza_envio_email 
			  FROM {$FuncionarioContato->databaseTable}.{$FuncionarioContato->tableSchema}.{$FuncionarioContato->useTable} AS FuncionarioContato
			  WHERE FuncionarioContato.codigo_funcionario = Funcionario.codigo 
			    AND FuncionarioContato.codigo_tipo_retorno=" . TipoRetorno::TIPO_RETORNO_EMAIL . "
			    AND FuncionarioContato.codigo_tipo_contato=" . TipoContato::TIPO_CONTATO_COMERCIAL . "
			  ORDER BY FuncionarioContato.codigo
			) AS base_autoriza_email",
            'AlocacaoContatoTelefone.nome AS alocacao_nome',
            'AlocacaoContatoTelefone.descricao AS alocacao_telefone',
            'AlocacaoContatoEmail.descricao AS alocacao_email',
            'RegistroImportacao.alocacao_endereco',
            'RegistroImportacao.alocacao_endereco_numero',
            'RegistroImportacao.alocacao_endereco_compl',
            'RegistroImportacao.alocacao_endereco_bairro',
            'RegistroImportacao.alocacao_endereco_cidade',
            'RegistroImportacao.alocacao_endereco_uf',
            'RegistroImportacao.alocacao_endereco_cep',
            'RegistroImportacao.cli_aloc_codigo_documento',
            'RegistroImportacao.cli_aloc_inscricao_estadual',
            'RegistroImportacao.cliente_alocacao_ccm',
            'RegistroImportacao.cliente_alocacao_cnae',
            'RegistroImportacao.cnae_grau_risco',
            'RegistroImportacao.cli_aloc_razao_social',
            'RegistroImportacao.cli_aloc_codigo_externo',
            'RegistroImportacao.cli_aloc_cod_regime_tribut',
            'RegistroImportacao.cli_aloc_tipo_unidade',
            'conselhoprofissional_descricao',
            'numero_conselho',
            'conselho_uf',
            'RegistroImportacao.consprof_descricao',
            'RegistroImportacao.num_cons',
            'RegistroImportacao.cons_uf',
        );
    } //FINAL FUNCTION findImportacaoFields

    private function findImportacaoBaseFields()
    {
        $ClienteContato = &ClassRegistry::init('ClienteContato');
        $ClienteFuncionario = &ClassRegistry::init('ClienteFuncionario');
        $Funcionario = &ClassRegistry::init('Funcionario');
        $FuncionarioSetorCargo = &ClassRegistry::init('FuncionarioSetorCargo');
        return array(
            'ImportacaoEstrutura.codigo_empresa AS codigo_empresa',
            'RegistroImportacao.codigo AS codigo',
            'RegistroImportacao.acao_funcionario AS acao_funcionario',
            'StatusImportacao.descricao AS status_importacao',
            'RegistroImportacao.codigo_alocacao AS codigo_alocacao',
            'RegistroImportacao.nome_alocacao AS nome_alocacao',
            'RegistroImportacao.nome_setor AS nome_setor',
            'RegistroImportacao.nome_cargo AS nome_cargo',
            'RegistroImportacao.codigo_matricula AS codigo_matricula',
            'RegistroImportacao.matricula_funcionario AS matricula_funcionario',
            'RegistroImportacao.nome_funcionario AS nome_funcionario',
            'RegistroImportacao.data_nascimento AS data_nascimento',
            'RegistroImportacao.sexo AS sexo',
            'RegistroImportacao.situacao_cadastral AS situacao_cadastral',
            'RegistroImportacao.data_admissao AS data_admissao',
            'RegistroImportacao.data_demissao AS data_demissao',
            'RegistroImportacao.data_inicio_cargo AS data_inicio_cargo',
            'RegistroImportacao.estado_civil AS estado_civil',
            'RegistroImportacao.pis_pasep AS pis_pasep',
            'RegistroImportacao.rg AS rg',
            'RegistroImportacao.estado_rg AS estado_rg',
            'RegistroImportacao.cpf AS cpf',
            'RegistroImportacao.ctps AS ctps',
            'RegistroImportacao.serie_ctps AS serie_ctps',
            'RegistroImportacao.uf_ctps AS uf_ctps',
            'RegistroImportacao.endereco_funcionario AS endereco_funcionario',
            'RegistroImportacao.numero_funcionario AS numero_funcionario',
            'RegistroImportacao.complemento_funcionario AS complemento_funcionario',
            'RegistroImportacao.bairro_funcionario AS bairro_funcionario',
            'RegistroImportacao.cidade_funcionario AS cidade_funcionario',
            'RegistroImportacao.estado_funcionario AS estado_funcionario',
            'RegistroImportacao.cep_funcionario AS cep_funcionario',
            'RegistroImportacao.possui_deficiencia AS possui_deficiencia',
            'RegistroImportacao.codigo_cbo AS codigo_cbo',
            'RegistroImportacao.codigo_gfip AS codigo_gfip',
            'RegistroImportacao.centro_custo AS centro_custo',
            // 'RegistroImportacao.data_ultimo_aso AS data_ultimo_aso',
            // 'RegistroImportacao.aptidao AS aptidao',
            'RegistroImportacao.turno AS turno',
            'RegistroImportacao.descricao_detalhada_cargo AS descricao_detalhada_cargo',
            'RegistroImportacao.celular_funcionario AS celular_funcionario',
            'RegistroImportacao.autoriza_envio_sms_funcionario AS autoriza_envio_sms_funcionario',
            'RegistroImportacao.email_funcionario AS email_funcionario',
            'RegistroImportacao.autoriza_envio_email_funcionario AS autoriza_envio_email_func',
            'RegistroImportacao.contato_responsavel_alocacao AS contato_responsavel_alocacao',
            'RegistroImportacao.telefone_responsavel_alocacao AS telefone_responsavel_alocacao',
            'RegistroImportacao.email_responsavel_alocacao AS email_responsavel_alocacao',
            'RegistroImportacao.endereco_alocacao AS endereco_alocacao',
            'RegistroImportacao.numero_alocacao AS numero_alocacao',
            'RegistroImportacao.complemento_alocacao AS complemento_alocacao',
            'RegistroImportacao.bairro_alocacao AS bairro_alocacao',
            'RegistroImportacao.cidade_alocacao AS cidade_alocacao',
            'RegistroImportacao.estado_alocacao AS estado_alocacao',
            'RegistroImportacao.cep_alocacao AS cep_alocacao',
            'RegistroImportacao.cnpj_alocacao AS cnpj_alocacao',
            'RegistroImportacao.inscricao_estadual AS inscricao_estadual',
            'RegistroImportacao.inscricao_municipal AS inscricao_municipal',
            'RegistroImportacao.cnae AS cnae',
            'RegistroImportacao.grau_risco AS grau_risco',
            'RegistroImportacao.razao_social_alocacao AS razao_social_alocacao',
            'RegistroImportacao.unidade_negocio AS unidade_negocio',
            'RegistroImportacao.regime_tributario AS regime_tributario',
            'RegistroImportacao.codigo_externo_alocacao AS codigo_externo_alocacao',
            'RegistroImportacao.tipo_alocacao AS tipo_alocacao',
            'RegistroImportacao.observacao AS observacao',
            'RegistroImportacao.chave_externa AS chave_externa',
            'RegistroImportacao.codigo_cargo_externo AS codigo_cargo_externo',
            'RegistroImportacao.conselho_profissional AS consprof_descricao',
            'RegistroImportacao.numero_conselho AS num_cons',
            'RegistroImportacao.conselho_uf AS cons_uf',
            'ClienteAlocacao.codigo AS cliente_alocacao_codigo',
            'ClienteAlocacao.razao_social AS cli_aloc_razao_social',
            'ClienteAlocacao.nome_fantasia AS cli_aloc_nome_fantasia',
            'ClienteAlocacao.codigo_documento AS cli_aloc_codigo_documento',
            'ClienteAlocacao.inscricao_estadual AS cli_aloc_inscricao_estadual',
            'ClienteAlocacao.ccm AS cliente_alocacao_ccm',
            'ClienteAlocacao.cnae AS cliente_alocacao_cnae',
            'ClienteAlocacao.codigo_regime_tributario AS cli_aloc_cod_regime_tribut',
            'ClienteAlocacao.tipo_unidade AS cli_aloc_tipo_unidade',
            'ClienteAlocacao.codigo_externo AS cli_aloc_codigo_externo',
            'conselho_profissional.descricao AS conselhoprofissional_descricao',
            'Medicos.numero_conselho AS numero_conselho',
            'Medicos.conselho_uf AS conselho_uf',
            "(SELECT TOP 1 codigo 
			  FROM {$ClienteContato->databaseTable}.{$ClienteContato->tableSchema}.{$ClienteContato->useTable} AS ClienteContato
			  WHERE ClienteContato.codigo_cliente = ClienteAlocacao.codigo 
			    AND ClienteContato.codigo_tipo_retorno=" . TipoRetorno::TIPO_RETORNO_TELEFONE . "
			    AND ClienteContato.codigo_tipo_contato=" . TipoContato::TIPO_CONTATO_COMERCIAL . "
			  ORDER BY ClienteContato.codigo
			) AS cli_contato_codigo_telefone",
            "(SELECT TOP 1 codigo 
			  FROM {$ClienteContato->databaseTable}.{$ClienteContato->tableSchema}.{$ClienteContato->useTable} AS ClienteContato
			  WHERE ClienteContato.codigo_cliente = ClienteAlocacao.codigo 
			    AND ClienteContato.codigo_tipo_retorno=" . TipoRetorno::TIPO_RETORNO_EMAIL . "
			    AND ClienteContato.codigo_tipo_contato=" . TipoContato::TIPO_CONTATO_COMERCIAL . "
			  ORDER BY ClienteContato.codigo
			) AS cli_contato_codigo_email",
            "VEnderecoAlocacao.endereco_tipo+' '+VEnderecoAlocacao.endereco_logradouro AS alocacao_endereco",
            'ClienteEnderecoAlocacao.numero AS alocacao_endereco_numero',
            'ClienteEnderecoAlocacao.complemento AS alocacao_endereco_compl',
            'VEnderecoAlocacao.endereco_bairro AS alocacao_endereco_bairro',
            'VEnderecoAlocacao.endereco_cidade AS alocacao_endereco_cidade',
            'VEnderecoAlocacao.endereco_estado_abreviacao AS alocacao_endereco_uf',
            'VEnderecoAlocacao.endereco_cep AS alocacao_endereco_cep',
            'Cnae.grau_risco AS cnae_grau_risco',
            "(SELECT TOP 1 ClienteFuncionario.codigo 
			  FROM {$ClienteFuncionario->databaseTable}.{$ClienteFuncionario->tableSchema}.{$ClienteFuncionario->useTable} AS ClienteFuncionario
			  INNER JOIN {$Funcionario->databaseTable}.{$Funcionario->tableSchema}.{$Funcionario->useTable} AS Funcionario ON Funcionario.codigo = ClienteFuncionario.codigo_funcionario AND Funcionario.cpf = RegistroImportacao.cpf
			  WHERE ClienteFuncionario.codigo_cliente_matricula = GrupoEconomico.codigo_cliente
			  	AND ClienteFuncionario.codigo_empresa = GrupoEconomico.codigo_empresa
			  	AND (
			  		ClienteFuncionario.codigo = CASE WHEN LTRIM(RTRIM(RegistroImportacao.codigo_matricula))<>'' THEN TRY_CONVERT(int, RegistroImportacao.codigo_matricula) ELSE ClienteFuncionario.codigo END
			  		OR
			  		ClienteFuncionario.matricula = RegistroImportacao.matricula_funcionario
			  	)
			  ORDER BY ClienteFuncionario.codigo DESC
		     ) AS codigo_cliente_funcionario",
            "(SELECT TOP 1 FuncionarioSetorCargo.codigo 
		     FROM {$FuncionarioSetorCargo->databaseTable}.{$FuncionarioSetorCargo->tableSchema}.{$FuncionarioSetorCargo->useTable} AS FuncionarioSetorCargo
		     WHERE codigo_cliente_funcionario = (
		     		SELECT TOP 1 ClienteFuncionario.codigo 
				  	FROM {$ClienteFuncionario->databaseTable}.{$ClienteFuncionario->tableSchema}.{$ClienteFuncionario->useTable} AS ClienteFuncionario
				  	INNER JOIN {$Funcionario->databaseTable}.{$Funcionario->tableSchema}.{$Funcionario->useTable} AS Funcionario ON Funcionario.codigo = ClienteFuncionario.codigo_funcionario AND Funcionario.cpf = RegistroImportacao.cpf
				  	WHERE ClienteFuncionario.codigo_cliente_matricula = GrupoEconomico.codigo_cliente
				  	  AND ClienteFuncionario.codigo_empresa = GrupoEconomico.codigo_empresa
				  	  AND (
				  	  	ClienteFuncionario.codigo = CASE WHEN LTRIM(RTRIM(RegistroImportacao.codigo_matricula))<>'' THEN TRY_CONVERT(int, RegistroImportacao.codigo_matricula) ELSE ClienteFuncionario.codigo END
				  	  	OR
				  	  	ClienteFuncionario.matricula = RegistroImportacao.matricula_funcionario
				  	  )
				    ORDER BY ClienteFuncionario.codigo DESC
			 ) 
		     ORDER BY FuncionarioSetorCargo.codigo DESC
		    ) AS codigo_func_setor_cargo",
            "(Select TOP 1 descricao from setores where descricao = RegistroImportacao.nome_setor AND codigo_cliente = GrupoEconomico.codigo_cliente) AS existe_setor",
            "(Select TOP 1 descricao from cargos where descricao = RegistroImportacao.nome_cargo AND codigo_cliente = GrupoEconomico.codigo_cliente) AS existe_cargo"
        );
    } //FINAL FUNCTION findImportacaoBaseFields

    function depara()
    {
        return array(
            'codigo_alocacao' => 'cliente_alocacao_codigo',
            'nome_alocacao' => 'cli_aloc_nome_fantasia',
            'nome_setor' => 'setor_descricao',
            'nome_cargo' => 'cargo_descricao',
            'codigo_matricula' => 'codigo_cliente_funcionario',
            'matricula_funcionario' => 'cliente_funcionario_matricula',
            'nome_funcionario' => 'funcionario_nome',
            'data_nascimento' => 'funcionario_data_nascimento',
            'sexo' => 'funcionario_sexo',
            'situacao_cadastral' => 'cliente_funcionario_status',
            'data_admissao' => 'cliente_funcionario_admissao',
            'data_demissao' => 'cliente_funcionario_demissao',
            'data_inicio_cargo' => 'func_setor_cargo_inicio',
            'estado_civil' => 'funcionario_estado_civil',
            'pis_pasep' => 'funcionario_nit',
            'rg' => 'funcionario_rg',
            'estado_rg' => 'funcionario_rg_uf',
            'cpf' => 'funcionario_cpf',
            'ctps' => 'funcionario_ctps',
            'serie_ctps' => 'funcionario_ctps_serie',
            'uf_ctps' => 'funcionario_ctps_uf',
            'endereco_funcionario' => 'funcionario_endereco',
            'numero_funcionario' => 'funcionario_endereco_numero',
            'complemento_funcionario' => 'funcionario_endereco_compl',
            'bairro_funcionario' => 'funcionario_endereco_bairro',
            'cidade_funcionario' => 'funcionario_endereco_cidade',
            'estado_funcionario' => 'funcionario_endereco_uf',
            'cep_funcionario' => 'funcionario_endereco_cep',
            'possui_deficiencia' => 'funcionario_deficiencia',
            'codigo_cbo' => 'cargo_codigo_cbo',
            'codigo_gfip' => 'funcionario_gfip',
            'centro_custo' => 'cliente_func_centro_custo',
            // 'data_ultimo_aso' => 'cliente_func_data_aso',
            // 'aptidao' => 'cliente_funcionario_aptidao',
            'turno' => 'cliente_funcionario_turno',
            'descricao_detalhada_cargo' => 'cargo_descricao_detalhada',
            'celular_funcionario' => 'base_celular_funcionario',
            'autoriza_envio_sms_funcionario' => 'base_autoriza_sms',
            'email_funcionario' => 'base_email_funcionario',
            'autoriza_envio_email_func' => 'base_autoriza_email',
            'contato_responsavel_alocacao' => 'alocacao_nome',
            'telefone_responsavel_alocacao' => 'alocacao_telefone',
            'email_responsavel_alocacao' => 'alocacao_email',
            'endereco_alocacao' => 'alocacao_endereco',
            'numero_alocacao' => 'alocacao_endereco_numero',
            'complemento_alocacao' => 'alocacao_endereco_compl',
            'bairro_alocacao' => 'alocacao_endereco_bairro',
            'cidade_alocacao' => 'alocacao_endereco_cidade',
            'estado_alocacao' => 'alocacao_endereco_uf',
            'cep_alocacao' => 'alocacao_endereco_cep',
            'cnpj_alocacao' => 'cli_aloc_codigo_documento',
            'inscricao_estadual' => 'cli_aloc_inscricao_estadual',
            'inscricao_municipal' => 'cliente_alocacao_ccm',
            'cnae' => 'cliente_alocacao_cnae',
            'grau_risco' => 'cnae_grau_risco',
            'razao_social_alocacao' => 'cli_aloc_razao_social',
            'unidade_negocio' => 'cli_aloc_nome_fantasia',
            'regime_tributario' => 'cli_aloc_cod_regime_tribut',
            'codigo_externo_alocacao' => 'cli_aloc_codigo_externo',
            'tipo_alocacao' => 'cli_aloc_tipo_unidade',
            'consprof_descricao' => 'conselhoprofissional_descricao',
            'num_cons' => 'numero_conselho',
            'cons_uf' => 'conselho_uf',
            'chave_externa' => 'chave_externa',
            'codigo_cargo_externo' => 'codigo_cargo_externo',
            'observacao' => 'observacao'
        );
    } //FINAL FUNCTION depara

    function titulos()
    {
        return array(
            'codigo_alocacao' => 'Código Unidade',
            'nome_alocacao' => 'Nome da Unidade',
            'nome_setor' => 'Nome do Setor',
            'nome_cargo' => 'Nome do Cargo',
            'codigo_matricula' => 'Código Matrícula',
            'matricula_funcionario' => 'Matrícula do Funcionário',
            'nome_funcionario' => 'Nome do Funcionário',
            'data_nascimento' => 'Data de Nascimento(dd/mm/aaaa)',
            'sexo' => 'Sexo(F:Feminino, M:Masculino)',
            'situacao_cadastral' => 'Situação Cadastral(S:Ativo, F:Férias, A:Afastado, I:Inativo)',
            'data_admissao' => 'Data de Admissão(dd/mm/aaaa)',
            'data_demissao' => 'Data de Demissão(dd/mm/aaaa)',
            'data_inicio_cargo' => 'Data Início Cargo(dd/mm/aaaa)',
            'estado_civil' => 'Estado Civil(1:Solteiro, 2:Casado, 3:Separado, 4:Divorciado, 5:Viúvo, 6:Outros)',
            'pis_pasep' => 'Pis/Pasep',
            'rg' => 'Rg',
            'estado_rg' => 'Estado RG',
            'cpf' => 'CPF',
            'ctps' => 'CTPS',
            'serie_ctps' => 'Série CTPS',
            'uf_ctps' => 'UF CTPS',
            'endereco_funcionario' => 'Endereço',
            'numero_funcionario' => 'Número',
            'complemento_funcionario' => 'Complemento',
            'bairro_funcionario' => 'Bairro',
            'cidade_funcionario' => 'Cidade',
            'estado_funcionario' => 'Estado',
            'cep_funcionario' => 'Cep',
            'possui_deficiencia' => 'Possui Deficiência(S:Sim, N:Não)',
            'codigo_cbo' => 'Código CBO',
            'codigo_gfip' => 'Código GFIP',
            'centro_custo' => 'Centro Custo',
            // 'data_ultimo_aso' => 'Data da Último ASO(dd/mm/aaaa)',
            // 'aptidao' => 'Aptidão(A:Apto, I:Inapto)',
            'turno' => 'Turno(D:Diurno, V:Vespertino, N:Noturno)',
            'descricao_detalhada_cargo' => 'Descrição de atividades do cargo',
            'celular_funcionario' => 'Celular do Funcionário((ddd)+número telefone)',
            'autoriza_envio_sms_funcionario' => 'Autoriza envio de SMS ao funcionário',
            'email_funcionario' => 'E-mail do Funcionário',
            'autoriza_envio_email_func' => 'Autoriza envio de e-mail ao funcionário',
            'contato_responsavel_alocacao' => 'Contato do responsável da Unidade',
            'telefone_responsavel_alocacao' => 'Telefone do responsável da Unidade((ddd)+número telefone)',
            'email_responsavel_alocacao' => 'E-maildo responsável da Unidade',
            'endereco_alocacao' => 'Endereço da Unidade',
            'numero_alocacao' => 'Número da Unidade',
            'complemento_alocacao' => 'Complemento da Unidade',
            'bairro_alocacao' => 'Bairro da Unidade',
            'cidade_alocacao' => 'Cidade da Unidade',
            'estado_alocacao' => 'Estado da Unidade',
            'cep_alocacao' => 'Cep da Unidade',
            'cnpj_alocacao' => 'CNPJ da Unidade',
            'inscricao_estadual' => 'Inscrição Estadual',
            'inscricao_municipal' => 'Inscrição Municipal',
            'cnae' => 'Cnae',
            'grau_risco' => 'Grau de risco',
            'razao_social_alocacao' => 'Razão Social Unidade',
            'unidade_negocio' => 'Unidade de Negócio',
            'regime_tributario' => 'Regime Tributário(1:Simples Nacional, 2:Simples Nacional, excesso sublimite de receita bruta, 3:Regime Normal)',
            'codigo_externo_alocacao' => 'Código Externo',
            'tipo_alocacao' => 'Tipo Unidade',
            'conselhoprofissional_descricao' => 'Conselho Profissional',
            'numero_conselho' => 'Número do Conselho',
            'conselho_uf' => 'Conselho Estado(UF)',
            'chave_externa' => 'Chave Externa',
            'codigo_cargo_externo' => 'Código Cargo Externo',
            'observacao' => 'Observação',
        );
    } //FINAL FUNCTION titulos

    function validaRegistros($registros)
    {
        $validacoes = array();
        foreach ($registros as $key => $registro) {
            $campos = $this->validaRegistro($registro);
            if ($campos) $validacoes[$key] = $campos;
        }
        return $validacoes;
    } //FINAL FUNCTION validaRegistros

    function validaRegistro($registro, $options = array())
    {

        $Documento     = &ClassRegistry::init('Documento');
        $Medico     = &ClassRegistry::init('Medico');

        $campos = array();

        foreach ($registro[0] as $key => $value) $registro[0][$key] = trim($registro[0][$key]);
        if (empty($registro[0]['nome_alocacao'])) $campos['nome_alocacao'] = 'Nome da Alocação não informado';
        if (empty($registro[0]['nome_setor'])) $campos['nome_setor'] = 'Nome do Setor não informado';
        if (empty($registro[0]['nome_cargo'])) $campos['nome_cargo'] = 'Nome do Cargo não informado';
        if (empty($registro[0]['matricula_funcionario'])) $campos['matricula_funcionario'] = 'Matrícula do funcionário não informada';
        if (empty($registro[0]['nome_funcionario'])) $campos['nome_funcionario'] = 'Nome do Funcionário não informado';
        if (empty($registro[0]['data_nascimento'])) $campos['data_nascimento'] = 'Data de Nascimento não informada';
        if (empty($registro[0]['sexo'])) $campos['sexo'] = 'Sexo não informado';
        if (empty($registro[0]['data_admissao'])) $campos['data_admissao'] = 'Data de Admissão não informada';
        if (empty($registro[0]['cpf'])) {
            $campos['cpf'] = 'CPF não informado';
        } elseif (!$Documento->isCPF($registro[0]['cpf'])) {
            $campos['cpf'] = 'CPF inválido';
        }

        if (empty($registro[0]['cnae'])) $campos['cnae'] = 'CNAE não informado';

        if (empty($registro[0]['grau_risco'])) $campos['grau_risco'] = 'Grau Risco não informado';

        if (empty($registro[0]['data_inicio_cargo'])) $campos['data_inicio_cargo'] = 'Data inicio do cargo não foi informada!';

        if (!isset($options['ignorar_endereco']) || $options['ignorar_endereco'] == false) {
            if (!is_numeric($registro[0]['cep_funcionario']) || strlen($registro[0]['cep_funcionario']) != 8) {
                $campos['cep_funcionario'] = "CEP do funcionário inválido, campo deve conter somente números e 8 dígitos" . "\n";
            }

            if (preg_match('/^[0-9]{5}(-[0-9]{4})?$/', $registro[0]['cep_funcionario'])) {
                $campos['cep_funcionario'] = "CEP do funcionário inválido, insira um CEP válido" . "\n";
            }
        }

        if (!empty($registro[0]['chave_externa'])) {
            $cpf  = substr($registro[0]['chave_externa'], 0, 11);
            $cnpj = substr($registro[0]['chave_externa'], 11, 14);
            $codigo_cargo_externo = substr($registro[0]['chave_externa'], 25);

            if (empty($registro[0]['codigo_cargo_externo'])) {
                $campos['codigo_cargo_externo'] = "Código Cargo Externo é necessário para a Chave Externa";
            }

            if ($cpf != $registro[0]['cpf'] || $cnpj != $registro[0]['cnpj_alocacao'] || $codigo_cargo_externo != $registro[0]['codigo_cargo_externo']) {
                $campos['chave_externa'] = "Chave externa inválida";
            }
        }

        if (empty($registro[0]['codigo_externo_alocacao'])) {
            $campos['codigo_externo_alocacao'] = "Código Externo não informado" . "\n";
        }

        if (strlen($registro[0]['codigo_externo_alocacao']) > 50) {
            $campos['codigo_externo_alocacao'] = "Código Externo ultrapassa 50 caracteres" . "\n";
        }

        if (!isset($registro[0]['numero_alocacao']) || !is_numeric($registro[0]['numero_alocacao'])) {
            $campos['numero_alocacao'] = 'Número do endereço de alocação inválido';
        }
        if (empty($registro[0]['cnpj_alocacao'])) {
            $campos['cnpj_alocacao'] = 'CNPJ da Alocação não informado';
        } elseif ($registro[0]['tipo_alocacao'] == 'F' && !$Documento->isCNPJ($registro[0]['cnpj_alocacao'])) {
            $campos['cnpj_alocacao'] = 'CNPJ inválido';
        }
        if (empty($registro[0]['inscricao_estadual'])) $campos['inscricao_estadual'] = 'Inscrição Estadual da Alocação não informada';
        if (empty($registro[0]['inscricao_municipal'])) $campos['inscricao_municipal'] = 'Inscrição Municipal da Alocação não informada';
        if (empty($registro[0]['tipo_alocacao'])) $campos['tipo_alocacao'] = 'Tipo da Alocação não informado';

        $tipo_validos = array('F', 'O');
        if (!in_array($registro[0]['tipo_alocacao'], $tipo_validos)) $campos['tipo_alocacao'] = 'Tipo da Alocação não valido, favor informar F ou O';

        if (($registro[0]['situacao_cadastral'] == 'I') && empty($registro[0]['data_demissao'])) {
            $campos['situacao_cadastral'] = 'Data de demissão não informada';
        } //FINAL IF situação_cadastral(status) é igual a 'I' e data demissão igual vazio

        if (!empty($registro[0]['data_demissao']) && ($registro[0]['situacao_cadastral'] != 'I')) {
            $campos['data_demissao'] = 'Situação cadastral deve ser Inativo';
        } //FINAL IF data demissão diferente de vazio e situação_cadastral(status) é diferente de 'I'

        if (empty($registro[0]['num_cons'])) {
            $campos['num_cons'] = 'O número do conselho do profissional deve ser informado';
        }

        if (empty($registro[0]['consprof_descricao'])) {
            $campos['consprof_descricao'] = 'O conselho do profissional deve ser informado';
        }

        if (empty($registro[0]['cons_uf'])) {
            $campos['cons_uf'] = 'O estado conselho do profissional deve ser informado';
        }

        if (!empty($registro[0]['num_cons']) && !empty($registro[0]['consprof_descricao']) && !empty($registro[0]['cons_uf'])) {
            $conditions = array(
                'numero_conselho' => $registro[0]['num_cons'],
                'conselho_uf' => $registro[0]['cons_uf']
            );
            $dados_medico = $Medico->find('first', array('conditions' => $conditions));
            if (!empty($dados_medico)) {
                if ($dados_medico['ConselhoProfissional']['descricao'] != $registro[0]['consprof_descricao']) {
                    $campos['consprof_descricao'] = 'O conselho do profissional informado não está correto';
                }
                if ($dados_medico['Medico']['conselho_uf'] != $registro[0]['cons_uf']) {
                    $campos['cons_uf'] = 'O estado do conselho do profissional informado não está correto';
                }
            } else {
                $campos['num_cons'] = 'Número do conselho inválido';
            }
        }

        if (!empty($registro[0]['codigo_alocacao'])) {
            if (!self::validaAtivoCodigoAlocacao($registro[0]['codigo_alocacao'])) {
                $campos['codigo_alocacao'] = 'Unidade está Inativa no Sistema';
            }
        }

        if (empty($registro[0]['descricao_detalhada_cargo'])) $campos['descricao_detalhada_cargo'] = 'Campo Descrição de atividades do cargo não informado';

        return $campos;
    } //FINAL FUNCTION validaRegistro

    function importarAlocacao($unidade, $codigo_grupo_economico)
    {
        $Cnae = &ClassRegistry::init('Cnae');
        $Cliente = &ClassRegistry::init('Cliente');
        $ClienteEndereco = &ClassRegistry::init('ClienteEndereco');
        $ClienteContato = &ClassRegistry::init('ClienteContato');
        $GrupoEconomicoCliente = &ClassRegistry::init('GrupoEconomicoCliente');
        $Medico = &ClassRegistry::init('Medico');
        $LastId = &ClassRegistry::init('LastId');

        $campos = array();
        $retorno = array();

        if ($unidade['tipo_alocacao'] == 'F') {
            $conditions = array('codigo_documento' => $unidade['cnpj_alocacao'], 'ativo' => 1);
        } elseif ($unidade['tipo_alocacao'] == 'O') {
            if (!empty($unidade['codigo_alocacao'])) {
                $conditions = array(
                    'GrupoEconomicoCliente.codigo_cliente' => $unidade['codigo_alocacao']
                );

                $grupo_economico_cliente = $GrupoEconomicoCliente->find('first', array(
                    'joins' => array(
                        array(
                            'table' => 'cliente',
                            'alias' => 'Cliente',
                            'type' => 'INNER',
                            'conditions' => array(
                                'GrupoEconomico.codigo_cliente = Cliente.codigo'
                            )
                        )
                    ),

                    'conditions' => array(
                        'GrupoEconomicoCliente.codigo_cliente' => $codigo_grupo_economico,
                        'Cliente.ativo' => 1
                    )
                ));

                if (empty($grupo_economico_cliente)) {
                    $conditions = array(
                        'codigo' => $unidade['codigo_alocacao'],
                        'ativo' => 1,
                    );
                } else {
                    if ($grupo_economico_cliente['GrupoEconomicoCliente']['codigo_grupo_economico'] != $codigo_grupo_economico) {
                        $campos[] = 'Código da unidade de alocação informado pertence a outro Grupo Econômico';
                        echo "Código alocação informado pertence a outro Grupo Econômico" . "\n";
                        $retorno['codigo_alocacao'] = false;
                        $retorno['invalidFields'] = implode(',', $campos);
                        return $retorno;
                    }
                    //preenche o conditions para verificar se o cliente existe
                    $conditions = array(
                        'codigo' => $unidade['codigo_alocacao'],
                        'ativo' => 1,
                    );
                }
            } else {
                if (empty($unidade['codigo_externo_alocacao']) || trim($unidade['codigo_externo_alocacao']) === '') {
                    echo "Código Externo Unidade Alocacao Operacional não informado" . "\n";
                    $campos[] = 'Código Externo da Unidade de Alocacao Operacional não informado';
                    $retorno['codigo_alocacao'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }

                if (strlen($unidade['codigo_externo_alocacao']) > 50) {
                    echo "Código Externo Unidade Alocacao ultrapassa 50 caracteres" . "\n";
                    $campos[] = 'Código Externo Unidade Alocacao ultrapassa 50 caracteres';
                    $retorno['codigo_alocacao'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }

                $conditions = array(
                    'codigo_externo' => $unidade['codigo_externo_alocacao'],
                    'ativo' => 1
                );
            }
        } //FINAL SE  $unidade['tipo_alocacao'] É IGUAL A 'F'

        if ((strlen($unidade['cep_alocacao']) != 8) || !is_numeric($unidade['cep_alocacao'])) {
            echo "O cep informado para a alocação é inválido" . "\n";
            $campos[] = 'O cep informado para a alocação é inválido';
            $retorno['cep_alocacao'] = false;
            $retorno['invalidFields'] = implode(',', $campos);
            return $retorno;
        }

        $cliente = $Cliente->find('first', compact('conditions'));

        if (empty($cliente)) {
            echo "Incluir Unidade Alocação" . "\n";
            $cnae = $Cnae->read(null, $unidade['cnae']);
            if ($cnae) {
                echo 'CNAE existente' . "\n";
                if (empty($cnae['Cnae']['grau_risco'])) {
                    echo 'Atualizar Grau de Risco' . "\n";
                    $Cnae->set('grau_risco', $unidade['grau_risco']);
                    if (!$Cnae->save()) {
                        echo "Falha ao atualizar Grau de Risco" . "\n";
                        $campos[] = 'Falha ao atualizar Grau de Risco';
                        $retorno['codigo_alocacao'] = false;
                        $retorno['invalidFields'] = implode(',', $campos);
                        return $retorno;
                    }
                    $cnae = $unidade['cnae'];
                }
            } else {
                echo 'CNAE inexistente' . "\n";
                $cnae = null;
            } //FINAL SE $cnae

            $incluir = true;
            $codigo_documento_real = null;
            if ($unidade['tipo_alocacao'] == 'F') {

                $cnpj_alocacao = $unidade['cnpj_alocacao'];
                $conditions = array('codigo_documento' => $unidade['cnpj_alocacao'], 'ativo' => 1);
                $cliente = $Cliente->find('first', compact('conditions'));
                if (!empty($cliente)) {
                    if (empty($cliente['Cliente']['codigo_externo'])) {
                        echo "CNPJ encontrado sem código externo, atualizando" . "\n";
                        $Cliente->read(null, $cliente['Cliente']['codigo']);
                        $Cliente->set('codigo_externo', $unidade['codigo_externo_alocacao']);
                        $Cliente->set('codigo_naveg', $LastId->last_id('Cliente'));
                        $Cliente->save();
                    }
                    $incluir = false;
                }
            } elseif ($unidade['tipo_alocacao'] == 'O') {
                $conditions = array('codigo_documento' => $unidade['cnpj_alocacao']);
                if ($Cliente->find('count', compact('conditions')) == 0) {
                    echo "Utilizar CNPJ Alocação informado" . "\n";
                    $cnpj_alocacao = $unidade['cnpj_alocacao'];
                } else {
                    $conditions = array('codigo_documento LIKE' => substr($unidade['cnpj_alocacao'], 0, 8) . '9%');
                    $group = 'LEFT(codigo_documento,8)';
                    $fields = array(
                        "MAX(RIGHT(LEFT(Cliente.codigo_documento, 12),3))+1 AS filial"
                    );
                    echo "Gerar CNPJ Alocação" . "\n";
                    $cnpj_alocacao = $Cliente->find('first', compact('conditions', 'fields', 'group'));
                    $cnpj_alocacao = substr($unidade['cnpj_alocacao'], 0, 8) . '9' . str_pad($cnpj_alocacao[0]['filial'], 3, '0', STR_PAD_LEFT) . '00';
                    echo 'Gerar novo CNPJ Alocação ' . $cnpj_alocacao . "\n";

                    //seta o codigo_documento_real com o codigo da matriz, quando a unidade é operacional
                    $codigo_documento_real = $unidade['cnpj_alocacao'];
                }
            } //FINAL SE $unidade['tipo_alocacao'] É IGAL A 'F'

            if ($incluir) {
                $conditions = array(
                    'numero_conselho' => $unidade['num_cons'],
                );
                $codigo_medico_pcmso = $Medico->find('first', array('conditions' => $conditions));
                $codigo_medico_pcmso = (!empty($codigo_medico_pcmso) ? $codigo_medico_pcmso['Medico']['codigo'] : null);
                $dados = array(
                    'Cliente' => array(
                        'codigo_documento' => $cnpj_alocacao,
                        'nome_fantasia' => $unidade['nome_alocacao'],
                        'razao_social' => $unidade['razao_social_alocacao'],
                        'codigo_regime_tributario' => $unidade['regime_tributario'],
                        'codigo_externo' => $unidade['codigo_externo_alocacao'],
                        'tipo_unidade' => $unidade['tipo_alocacao'],
                        'inscricao_estadual' => $unidade['inscricao_estadual'],
                        'ccm' => $unidade['inscricao_municipal'],
                        'cnae' => $cnae,
                        'codigo_medico_pcmso' => $codigo_medico_pcmso,
                        'codigo_documento_real' => $codigo_documento_real,
                    ),
                    'GrupoEconomicoCliente' => array(
                        'codigo_grupo_economico' => $codigo_grupo_economico
                    ),
                    'ClienteEndereco' => array(
                        //'codigo_endereco' => $endereco['VEndereco']['endereco_codigo'],
                        'complemento' => $unidade['complemento_alocacao'],
                        'numero' => $unidade['numero_alocacao'],
                        'cidade' => $unidade['cidade_alocacao'],
                        'estado_descricao' => $unidade['estado_alocacao'],
                        'estado_abreviacao' => $unidade['estado_alocacao'],
                        'bairro' => $unidade['bairro_alocacao'],
                        'cep' => $unidade['cep_alocacao'],
                        'logradouro' => $unidade['endereco_alocacao'],
                    )
                );

                echo 'Incluir' . "\n";
                if (!$Cliente->incluir($dados)) {
                    echo "Falha ao incluir unidade de alocação" . "\n";
                    $campos = $Cliente->validationErrors;
                    $campos[] = 'Falha ao incluir unidade de alocação';
                    $retorno['codigo_alocacao'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }
            } //FINAL SE $incluir É TRUE

            $cliente['Cliente']['codigo'] = $Cliente->id;
            if (!empty($unidade['telefone_responsavel_alocacao'])) {
                $dados = array(
                    'codigo_cliente' => $Cliente->id,
                    'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
                    'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_TELEFONE,
                    'nome' => $unidade['contato_responsavel_alocacao'],
                    'descricao' => $unidade['telefone_responsavel_alocacao'],
                );
                if (!$ClienteContato->incluir($dados)) {
                    echo "Falha ao incluir telefone de contato da alocação" . "\n";
                    $campos[] = 'Falha ao incluir telefone de contato da alocação';
                    $retorno['codigo_alocacao'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }
            } //FINAL SE $unidade['telefone_responsavel_alocacao'] NÃO É VAZIO

            if (!empty($unidade['email_responsavel_alocacao'])) {
                $dados = array(
                    'codigo_cliente' => $Cliente->id,
                    'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
                    'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL,
                    'nome' => $unidade['contato_responsavel_alocacao'],
                    'descricao' => $unidade['email_responsavel_alocacao'],
                );
                if (!$ClienteContato->incluir($dados)) {
                    echo "Falha ao incluir email de contato da alocação" . "\n";
                    $campos[] = 'Falha ao incluir email de contato da alocação';
                    $retorno['codigo_alocacao'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }
            } //FINAL SE $unidade['email_responsavel_alocacao'] NÃO É VAZIO

            $retorno['codigo_externo'] = $unidade['codigo_externo_alocacao'];
            $retorno['codigo_alocacao'] = $cliente['Cliente']['codigo'];
            return $retorno;
        } else {

            /****** ATUALIZACAO CNAE UNIDADE AJUSTE PARA O CHAMADO CDCT-519: https://ithealthbr.atlassian.net/browse/CDCT-519 */
            $cnae = $Cnae->find('first', array('conditions' => array('cnae' => $unidade['cnae'])));
            if ($cnae) {
                echo 'Atualizar Grau de Risco' . "\n";
                $cnae['Cnae']['grau_risco'] = $unidade['grau_risco'];
                if (!$Cnae->atualizar($cnae)) {
                    echo "Falha ao atualizar Grau de Risco" . "\n";
                    $campos[] = 'Falha ao atualizar Grau de Risco';
                    $retorno['codigo_alocacao'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }

                $cnae = $unidade['cnae'];

                if ($cnae) {
                    $cliente['Cliente']['cnae'] = $cnae;

                    $cliente['ClienteEndereco'] = array(
                        //'codigo_endereco' => $endereco['VEndereco']['endereco_codigo'],
                        'complemento' => $unidade['complemento_alocacao'],
                        'numero' => $unidade['numero_alocacao'],
                        'cidade' => $unidade['cidade_alocacao'],
                        'estado_descricao' => $unidade['estado_alocacao'],
                        'estado_abreviacao' => $unidade['estado_alocacao'],
                        'bairro' => $unidade['bairro_alocacao'],
                        'cep' => $unidade['cep_alocacao'],
                        'logradouro' => $unidade['endereco_alocacao'],
                        'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
                    );

                    $ClienteEndereco = &ClassRegistry::init('ClienteEndereco');

                    $antigoEnderecoComercial = $ClienteEndereco->find('first', array(
                        'conditions' => array(
                            'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
                            'codigo_cliente' => $unidade['codigo_alocacao']
                        )
                    ));

                    if (!empty($antigoEnderecoComercial['ClienteEndereco']['codigo'])) {

                        $cliente['ClienteEndereco']['codigo'] = $antigoEnderecoComercial['ClienteEndereco']['codigo'];
                    }

                    /**
                     * Atualizar o código externo do cliente
                     */
                    if(
                        (empty($cliente['Cliente']['codigo_externo']) || trim($cliente['Cliente']['codigo_externo']) === '') &&
                        trim($unidade['codigo_externo_alocacao'])!==''
                    ) {
                        $cliente['Cliente']['codigo_externo'] = $unidade['codigo_externo_alocacao'];
                    } 

                    if (!$Cliente->atualizar($cliente)) {
                        $campos[] = 'Falha em atualizar Cnae do Cliente';
                        $retorno['codigo_alocacao'] = false;
                        $retorno['invalidFields'] = implode(',', $campos);
                        return $retorno;
                    }
                }
            }
            /****** FIM ATUALIZACAO CNAE */


            $conditions = array('GrupoEconomicoCliente.codigo_cliente' => $cliente['Cliente']['codigo']);
            $fields = array('codigo_grupo_economico');
            $grupo_economico_cliente = $GrupoEconomicoCliente->find('first', compact('conditions', 'fields'));
            if (!empty($grupo_economico_cliente)) {
                if ($grupo_economico_cliente['GrupoEconomicoCliente']['codigo_grupo_economico'] != $codigo_grupo_economico) {

                    echo "Unidade de Alocacao vinculado a outro Grupo Econômico" . "\n";

                    $campos[] = 'Unidade de Alocacao vinculado a outro Grupo Econômico';
                    $retorno['codigo_alocacao'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }
            } else {
                $dados = array('GrupoEconomicoCliente' => array('codigo_grupo_economico' => $codigo_grupo_economico, 'codigo_cliente' => $cliente['Cliente']['codigo']));
                if (!$GrupoEconomicoCliente->incluir($dados)) {
                    echo "Falha ao vincular unidade de alocação ao Grupo Econômico" . "\n";
                    $campos[] = 'Falha ao vincular unidade de alocação ao Grupo Econômico';
                    $retorno['codigo_alocacao'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }
            } //FINAL SE $grupo_economico_cliente NÃO É VAZIO
        } //FINAL SE $cliente é VAZIO
        $retorno['codigo_externo'] = $cliente['Cliente']['codigo_externo'];
        $retorno['codigo_alocacao'] = $cliente['Cliente']['codigo'];
        return $retorno;
    } //FINAL FUNCTION importarAlocacao

    private function temDiferencaFuncionario($base_funcionario, $funcionario)
    {
        if ($base_funcionario['Funcionario']['nome'] != $funcionario['nome_funcionario']) return true;
        if ($base_funcionario['Funcionario']['sexo'] != $funcionario['sexo']) return true;
        if ($base_funcionario['Funcionario']['data_nascimento'] != $funcionario['data_nascimento']) return true;
        if ($base_funcionario['Funcionario']['estado_civil'] != $funcionario['estado_civil']) return true;
        if ($base_funcionario['Funcionario']['nit'] != $funcionario['pis_pasep']) return true;
        if ($base_funcionario['Funcionario']['rg'] != $funcionario['rg']) return true;
        if ($base_funcionario['Funcionario']['rg_uf'] != $funcionario['estado_rg']) return true;
        if ($base_funcionario['Funcionario']['rg_orgao'] != '') return true;
        if ($base_funcionario['Funcionario']['ctps'] != $funcionario['ctps']) return true;
        if ($base_funcionario['Funcionario']['ctps_serie'] != $funcionario['serie_ctps']) return true;
        if ($base_funcionario['Funcionario']['ctps_uf'] != $funcionario['uf_ctps']) return true;
        if ($base_funcionario['Funcionario']['gfip'] != $funcionario['codigo_gfip']) return true;
        if ((empty($base_funcionario['Funcionario']['deficiencia']) ? '0' : '1') != strpos($funcionario['possui_deficiencia'], 'NS')) return true;

        return false;
    } //FINAL FUNCTION temDiferencaFuncionario

    private function temDiferencaFuncionarioEndereco($funcionario_endereco, $funcionario, $endereco)
    {
        if ($funcionario_endereco['FuncionarioEndereco']['numero'] != $funcionario['numero_funcionario']) return true;
        if ($funcionario_endereco['FuncionarioEndereco']['complemento'] != $funcionario['complemento_funcionario']) return true;
        if ($funcionario_endereco['FuncionarioEndereco']['logradouro'] != $funcionario['endereco_funcionario']) return true;
        if ($funcionario_endereco['FuncionarioEndereco']['bairro'] != $funcionario['bairro_funcionario']) return true;
        if ($funcionario_endereco['FuncionarioEndereco']['cidade'] != $funcionario['cidade_funcionario']) return true;
        if ($funcionario_endereco['FuncionarioEndereco']['estado_abreviacao'] != $funcionario['estado_funcionario']) return true;
        if ($funcionario_endereco['FuncionarioEndereco']['cep'] != $funcionario['cep_funcionario']) return true;
        return false;
    } //FINAL FUNCTION temDiferencaFuncionarioEndereco

    private function temDiferencaFuncionarioContatoCelular($funcionario_contato, $funcionario)
    {
        if ($funcionario_contato['FuncionarioContato']['descricao'] != $funcionario['celular_funcionario']) return true;
        if ($funcionario_contato['FuncionarioContato']['autoriza_envio_sms'] != ($funcionario['autoriza_envio_sms_funcionario'] == 'S' ? 1 : 0)) return true;
        return false;
    } //FINAL FUNCTION temDiferencaFuncionarioContatoCelular

    private function temDiferencaFuncionarioContatoEmail($funcionario_contato, $funcionario)
    {
        if ($funcionario_contato['FuncionarioContato']['descricao'] != $funcionario['email_funcionario']) return true;
        if ($funcionario_contato['FuncionarioContato']['autoriza_envio_email'] != ($funcionario['autoriza_envio_email_func'] == 'S' ? 1 : 0)) return true;
        return false;
    } //FINAL FUNCTION temDiferencaFuncionarioContatoEmail

    private function temDiferencaClienteFuncionario($cliente_funcionario, $matricula)
    {
        $situacao = array('I' => '0', 'S' =>  '1', 'F' => '2', 'A' => '3');
        $turno = array('D' => '0', 'V' =>  '1', 'N' => '2');
        $aptidao = array('I' => '0', 'A' =>  '1');

        if ($cliente_funcionario['ClienteFuncionario']['ativo'] != $situacao[$matricula['situacao_cadastral']]) return true;
        if ($cliente_funcionario['ClienteFuncionario']['admissao'] != $matricula['data_admissao']) return true;
        if ($cliente_funcionario['ClienteFuncionario']['data_demissao'] != $matricula['data_demissao']) return true;
        if ($cliente_funcionario['ClienteFuncionario']['centro_custo'] != $matricula['centro_custo']) return true;
        if ($cliente_funcionario['ClienteFuncionario']['data_ultima_aso'] != $matricula['data_ultimo_aso']) return true;
        if ($cliente_funcionario['ClienteFuncionario']['aptidao'] != (isset($aptidao[$matricula['aptidao']]) ? $aptidao[$matricula['aptidao']] : '')) return true;
        if ($cliente_funcionario['ClienteFuncionario']['turno'] != (isset($turno[$matricula['turno']]) ? $turno[$matricula['turno']] : '')) return true;
        if ($cliente_funcionario['ClienteFuncionario']['matricula'] != $matricula['matricula_funcionario']) return true;
        if ($cliente_funcionario['ClienteFuncionario']['chave_externa'] != $matricula['chave_externa']) return true;
        if ($cliente_funcionario['ClienteFuncionario']['codigo_cargo_externo'] != $matricula['codigo_cargo_externo']) return true;

        return false;
    } //FINAL FUNCTION temDiferencaClienteFuncionario

    private function temDiferencaFuncionarioSetorCargo($funcionario_setor_cargo, $codigo_alocacao, $codigo_setor, $codigo_cargo, $data_inicio_cargo, $data_demissao)
    {
        if ($funcionario_setor_cargo['FuncionarioSetorCargo']['codigo_cliente_alocacao'] != $codigo_alocacao) return true;
        if ($funcionario_setor_cargo['FuncionarioSetorCargo']['codigo_setor'] != $codigo_setor) return true;
        if ($funcionario_setor_cargo['FuncionarioSetorCargo']['codigo_cargo'] != $codigo_cargo) return true;
        if ($funcionario_setor_cargo['FuncionarioSetorCargo']['data_inicio'] != AppModel::dateToDbDate($data_inicio_cargo)) return true;
        if ($funcionario_setor_cargo['FuncionarioSetorCargo']['data_fim'] != AppModel::dateToDbDate($data_demissao)) return true;
        return false;
    } //FINAL FUNCTION temDiferencaFuncionarioSetorCargo

    private function temDiferencaCargo($cargo, $setor_cargo)
    {

        if ($cargo['Cargo']['descricao_cargo'] != $setor_cargo['descricao_detalhada_cargo']) {
            return true;
        } else {
            return false;
        }

        // return (isset($cargo['Cargo']['descricao_cargo']) && ($cargo['Cargo']['descricao_cargo'] != $setor_cargo['descricao_detalhada_cargo']));
    } //FINAL FUNCTION temDiferencaCargo

    function importarFuncionario($funcionario, $codigo_grupo_economico)
    {

        $Funcionario = &ClassRegistry::init('Funcionario');
        $conditions = array('Funcionario.cpf' => $funcionario['cpf']);
        $base_funcionario = $Funcionario->find('first', compact('conditions'));

        $campos = array();
        $retorno = array();

        if (empty($base_funcionario)) {
            echo "Incluir Funcionário" . "\n";
            $dados = array('Funcionario' => array(
                'cpf' => $funcionario['cpf'],
                'nome' => $funcionario['nome_funcionario'],
                'sexo' => $funcionario['sexo'],
                'data_nascimento' => $funcionario['data_nascimento'],
                'estado_civil' => $funcionario['estado_civil'],
                'nit' => $funcionario['pis_pasep'],
                'rg' => $funcionario['rg'],
                'rg_uf' => $funcionario['estado_rg'],
                'rg_orgao' => '',
                'ctps' => $funcionario['ctps'],
                'ctps_serie' => $funcionario['serie_ctps'],
                'ctps_uf' => $funcionario['uf_ctps'],
                'gfip' => $funcionario['codigo_gfip'],
                'deficiencia' => $funcionario['possui_deficiencia'] == 'S' ? 1 : 0
            ));
            if (!$Funcionario->incluir($dados)) {
                echo "Falha ao incluir funcionário" . "\n";
                $campos[] = 'Falha ao incluir funcionário';
                if (!empty($Funcionario->validationErrors)) {
                    $campos[] = implode(',', $Funcionario->validationErrors);
                }
                $retorno['codigo_funcionario'] = false;
                $retorno['invalidFields'] = implode(',', $campos);
                return $retorno;
            }
            $base_funcionario['Funcionario']['codigo'] = $Funcionario->id;
        } else {
            if ($this->temDiferencaFuncionario($base_funcionario, $funcionario)) {                
                echo "Atualizar Funcionário" . "\n";
                $base_funcionario['Funcionario']['nome'] = $funcionario['nome_funcionario'];
                $base_funcionario['Funcionario']['sexo'] = $funcionario['sexo'];
                $base_funcionario['Funcionario']['data_nascimento'] = $funcionario['data_nascimento'];
                $base_funcionario['Funcionario']['estado_civil'] = $funcionario['estado_civil'];
                $base_funcionario['Funcionario']['nit'] = $funcionario['pis_pasep'];
                $base_funcionario['Funcionario']['rg'] = $funcionario['rg'];
                $base_funcionario['Funcionario']['rg_uf'] = '';// PC-3209 //$funcionario['estado_rg'];
                $base_funcionario['Funcionario']['rg_orgao'] = '';
                $base_funcionario['Funcionario']['ctps'] = $funcionario['ctps'];
                $base_funcionario['Funcionario']['ctps_serie'] = $funcionario['serie_ctps'];
                $base_funcionario['Funcionario']['ctps_uf'] = $funcionario['uf_ctps'];
                $base_funcionario['Funcionario']['gfip'] = $funcionario['codigo_gfip'];
                $base_funcionario['Funcionario']['deficiencia'] = $funcionario['possui_deficiencia'] == 'S' ? 1 : 0;
                if (!$Funcionario->atualizar($base_funcionario)) {
                    echo "Falha ao atualizar funcionário" . "\n";
                    $campos[] = 'Falha ao atualizar funcionário';
                    $retorno['codigo_funcionario'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }
            } else {
                echo "Funcionário sem atualização pendente" . "\n";
            }
        }

        //Se o campo cep do funcionario esta preenchido
        if (!empty($funcionario['cep_funcionario'])) {

            //Verifica se o CEP é valido
            if (preg_match('/^[0-9]{5}(-[0-9]{4})?$/', $funcionario['cep_funcionario'])) {
                echo "Falha ao atualizar endereço do funcionário" . "\n";
                $campos[] = 'Falha ao atualizar endereço do funcionário: CEP inválido';
                $retorno['codigo_funcionario'] = false;
                $retorno['invalidFields'] = implode(',', $campos);
                return $retorno;
            }

            $FuncionarioEndereco = &ClassRegistry::init('FuncionarioEndereco');

            $funcionario_endereco = $FuncionarioEndereco->find('first', array('conditions' => array(
                'FuncionarioEndereco.codigo_empresa' => ((isset($_SESSION['Auth']['Usuario']['codigo_empresa'])) ? $_SESSION['Auth']['Usuario']['codigo_empresa'] : 1),
                'FuncionarioEndereco.codigo_funcionario' => $base_funcionario['Funcionario']['codigo'],
                'FuncionarioEndereco.codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL
            )));


            //Se o funcionario nao possui endereco cadastrado
            if (empty($funcionario_endereco)) {
                echo 'Incluir Endereço do Funcionário' . "\n";

                $dados = array('FuncionarioEndereco' => array(
                    'codigo_endereco' => NULL,
                    'codigo_funcionario' => $base_funcionario['Funcionario']['codigo'],
                    'logradouro' =>  $funcionario['endereco_funcionario'],
                    'numero' => $funcionario['numero_funcionario'],
                    'complemento' => $funcionario['complemento_funcionario'],
                    'bairro' => $funcionario['bairro_funcionario'],
                    'cidade' => $funcionario['cidade_funcionario'],
                    'estado_abreviacao' => $funcionario['estado_funcionario'],
                    'cep' => $funcionario['cep_funcionario'],
                    'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
                ));

                if (!$FuncionarioEndereco->incluir($dados)) {
                    echo "Falha ao incluir endereço do funcionário" . "\n";
                    $campos[] = 'Falha ao incluir endereço do funcionário';
                    $retorno['codigo_funcionario'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }
            } else {
                if ($this->temDiferencaFuncionarioEndereco($funcionario_endereco, $funcionario, $endereco = null)) {
                    echo "Atualizar Funcionário Endereço" . "\n";
                    $funcionario_endereco['FuncionarioEndereco']['numero'] = $funcionario['numero_funcionario'];
                    $funcionario_endereco['FuncionarioEndereco']['complemento'] = $funcionario['complemento_funcionario'];
                    $funcionario_endereco['FuncionarioEndereco']['logradouro'] = $funcionario['endereco_funcionario'];
                    $funcionario_endereco['FuncionarioEndereco']['bairro'] = $funcionario['bairro_funcionario'];
                    $funcionario_endereco['FuncionarioEndereco']['cidade'] = $funcionario['cidade_funcionario'];
                    $funcionario_endereco['FuncionarioEndereco']['estado_abreviacao'] = $funcionario['estado_funcionario'];
                    $funcionario_endereco['FuncionarioEndereco']['cep'] = $funcionario['cep_funcionario'];
                    $funcionario_endereco['FuncionarioEndereco']['codigo_endereco'] = NULL;

                    if (!$FuncionarioEndereco->atualizar($funcionario_endereco)) {
                        echo "Falha ao atualizar endereço do funcionário" . "\n";
                        $campos[] = 'Falha ao atualizar endereço do funcionário';
                        $retorno['codigo_funcionario'] = false;
                        $retorno['invalidFields'] = implode(',', $campos);
                        return $retorno;
                    }
                } else {
                    echo "Funcionário Endereço sem atualização pendente" . "\n";
                }
            } //else atualizar endereco

        } //if cep funcionario preenchido

        if (!empty($funcionario['celular_funcionario'])) {
            $FuncionarioContato = &ClassRegistry::init('FuncionarioContato');
            $conditions = array(
                'FuncionarioContato.codigo_funcionario' => $base_funcionario['Funcionario']['codigo'],
                'FuncionarioContato.codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
                'FuncionarioContato.codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_CELULAR_MOTORISTA,
            );

            $funcionario_contato = $FuncionarioContato->find('first', compact('conditions'));
            if (empty($funcionario_contato)) {
                echo 'Incluir Funcionário Contato Celular' . "\n";
                $dados = array('FuncionarioContato' => array(
                    'codigo_funcionario' => $base_funcionario['Funcionario']['codigo'],
                    'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
                    'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_CELULAR_MOTORISTA,
                    'descricao' => $funcionario['celular_funcionario'],
                    'autoriza_envio_sms' => $funcionario['autoriza_envio_sms_funcionario'] == 'S' ? 1 : 0,
                ));
                if (!$FuncionarioContato->incluir($dados)) {
                    echo "Falha ao incluir celular do funcionário" . "\n";
                    $campos[] = 'Falha ao incluir celular do funcionário';
                    $retorno['codigo_funcionario'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }
            } else {
                if ($this->temDiferencaFuncionarioContatoCelular($funcionario_contato, $funcionario)) {
                    echo 'Atualizar Funcionário Contato Celular' . "\n";
                    $funcionario_contato['FuncionarioContato']['descricao'] = $funcionario['celular_funcionario'];
                    $funcionario_contato['FuncionarioContato']['autoriza_envio_sms'] = $funcionario['autoriza_envio_sms_funcionario'] == 'S' ? 1 : 0;
                    if (!$FuncionarioContato->atualizar($funcionario_contato)) {
                        echo "Falha ao atualizar celular do funcionário" . "\n";
                        $campos[] = 'Falha ao atualizar celular do funcionário';
                        $retorno['codigo_funcionario'] = false;
                        $retorno['invalidFields'] = implode(',', $campos);
                        return $retorno;
                    }
                } else {
                    echo 'Funcionário Contato Celular sem atualização pendente' . "\n";
                }
            }
        }

        if (!empty($funcionario['email_funcionario'])) {
            $FuncionarioContato = &ClassRegistry::init('FuncionarioContato');
            $conditions = array(
                'FuncionarioContato.codigo_funcionario' => $base_funcionario['Funcionario']['codigo'],
                'FuncionarioContato.codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
                'FuncionarioContato.codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL,
            );

            $funcionario_contato = $FuncionarioContato->find('first', compact('conditions'));
            if (empty($funcionario_contato)) {
                echo 'Incluir Funcionário Contato Email' . "\n";
                $dados = array('FuncionarioContato' => array(
                    'codigo_funcionario' => $base_funcionario['Funcionario']['codigo'],
                    'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
                    'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL,
                    'descricao' => $funcionario['email_funcionario'],
                    'autoriza_envio_email' => $funcionario['autoriza_envio_email_func'] == 'S' ? 1 : 0,
                ));
                if (!$FuncionarioContato->incluir($dados)) {
                    echo "Falha ao incluir email do funcionário" . "\n";
                    $campos[] = 'Falha ao incluir email do funcionário';
                    $retorno['codigo_funcionario'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }
            } else {
                if ($this->temDiferencaFuncionarioContatoEmail($funcionario_contato, $funcionario)) {
                    echo 'Atualizar Funcionário Contato Email' . "\n";
                    $funcionario_contato['FuncionarioContato']['descricao'] = $funcionario['email_funcionario'];
                    $funcionario_contato['FuncionarioContato']['autoriza_envio_email'] = $funcionario['autoriza_envio_email_func'] == 'S' ? 1 : 0;

                    if (!$FuncionarioContato->atualizar($funcionario_contato)) {
                        echo "Falha ao atualizar email do funcionário" . "\n";
                        $campos[] = 'Falha ao atualizar email do funcionário';
                        $retorno['codigo_funcionario'] = false;
                        $retorno['invalidFields'] = implode(',', $campos);
                        return $retorno;
                    }
                } else {
                    echo 'Funcionário Contato Email sem atualização pendente' . "\n";
                }
            }
        }
        $retorno['codigo_funcionario'] = $base_funcionario['Funcionario']['codigo'];
        return $retorno;
    } //FINAL FUNCTION importarFuncionario

    function importarMatricula($matricula, $codigo_grupo_economico, $codigo_funcionario)
    {

        $GrupoEconomico = &ClassRegistry::init('GrupoEconomico');
        $ClienteFuncionario = &ClassRegistry::init('ClienteFuncionario');
        $grupo_economico = $GrupoEconomico->find('first', array(
            'joins' => array(
                array(
                    'table' => 'cliente',
                    'alias' => 'Cliente',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GrupoEconomico.codigo_cliente = Cliente.codigo'
                    )
                )
            ),
            'conditions' => array(
                'GrupoEconomico.codigo' => $codigo_grupo_economico,
                'Cliente.ativo' => 1
            )
        ));

        $campos = array();
        $retorno = array();
        $situacao = array('I' => '0', 'S' =>  '1', 'F' => '2', 'A' => '3');
        $turno = array('D' => '0', 'V' =>  '1', 'N' => '2');
        $aptidao = array('I' => '0', 'A' =>  '1');
        $cliente_funcionario = array();

        if (!empty($matricula['codigo_matricula'])) {
            $conditions = array(
                'ClienteFuncionario.codigo_funcionario' => $codigo_funcionario,
                'ClienteFuncionario.codigo' => $matricula['codigo_matricula'],
                'ClienteFuncionario.codigo_cliente_matricula' => $grupo_economico['GrupoEconomico']['codigo_cliente'],
            );
        } else {
            $conditions = array(
                'ClienteFuncionario.codigo_funcionario' => $codigo_funcionario,
                'ClienteFuncionario.codigo_cliente_matricula' => $grupo_economico['GrupoEconomico']['codigo_cliente'],
                'ClienteFuncionario.matricula' => $matricula['matricula_funcionario']
            );
        }

        $cliente_funcionario = $ClienteFuncionario->find('first', compact('conditions'));

        //Se o código da matrícula foi passado e nenhum registro foi encontrado
        if (empty($cliente_funcionario) && !empty($matricula['codigo_matricula'])) {
            echo "Código de matrícula inválido" . "\n";
            $campos[] = 'Código de matrícula não corresponde ao funcionário informado';
            $retorno['codigo_matricula'] = false;
            $retorno['invalidFields'] = implode(',', $campos);
            return $retorno;
        }


        if (empty($cliente_funcionario) && empty($matricula['codigo_matricula'])) {
            echo "\n Incluir Matrícula" . "\n";


            if (isset($matricula['chave_externa']) && !empty($matricula['chave_externa'])) {

                // Antes de inserir nova matricula com chave externa,
                if (strlen($matricula['chave_externa']) < 26) {
                    echo "\n Falha ao incluir matrícula: [1] Campo chave_externa inválida: " . $matricula['chave_externa'] . "\n";
                    $campos[] = '[1] Campo chave_externa inválida quantidade de caracteres abaixo do permitido: ' . $matricula['chave_externa'];
                    $retorno['codigo_matricula'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }

                $cpf  = substr($matricula['chave_externa'], 0, 11);
                $cnpj = substr($matricula['chave_externa'], 11, 14);
                $codigo_cargo_externo = substr($matricula['chave_externa'], 25);

                if ($cpf != $matricula['cpf'] || $cnpj != $matricula['cnpj_alocacao'] || $codigo_cargo_externo != $matricula['codigo_cargo_externo']) {
                    echo "\n Falha ao incluir matrícula: [1] Campo chave_externa inválida: " . $matricula['chave_externa'] . "\n";
                    $campos[] = '[1] Campo chave_externa inválida: ' . $matricula['chave_externa'];
                    $retorno['codigo_matricula'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }

                //verifica se já existe uma chave externa igual cadastrada
                $cliente_funcionario_chave_externa = $ClienteFuncionario->find(
                    'all',
                    array('conditions' => array(
                        'ClienteFuncionario.chave_externa' => $matricula['chave_externa'],
                        'ClienteFuncionario.matricula'     => $matricula['matricula_funcionario'],
                        'ClienteFuncionario.codigo <> '    => $cliente_funcionario['ClienteFuncionario']['codigo']
                    ))
                );

                if (count($cliente_funcionario_chave_externa) > 0) {
                    echo "Falha ao incluir matrícula: [1] Já existe uma chave_externa com este código: " . $matricula['chave_externa'] . "\n";
                    $campos[] = '[1] Já existe uma chave_externa com este código: ' . $matricula['chave_externa'];
                    $retorno['codigo_matricula'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }
            }

            $dados = array(
                'codigo_cliente_matricula' => $grupo_economico['GrupoEconomico']['codigo_cliente'],
                'codigo_funcionario' => $codigo_funcionario,
                'matricula' => $matricula['matricula_funcionario'],
                'ativo' => $situacao[$matricula['situacao_cadastral']],
                'admissao' => $matricula['data_admissao'],
                'data_demissao' => $matricula['data_demissao'],
                'centro_custo' => $matricula['centro_custo'],
                'data_ultima_aso' => $matricula['data_ultimo_aso'],
                'aptidao' => isset($aptidao[$matricula['aptidao']]) ? $aptidao[$matricula['aptidao']] : '',
                'turno' => isset($turno[$matricula['turno']]) ? $turno[$matricula['turno']] : '',
                'chave_externa' => $matricula['chave_externa'],
                'codigo_cargo_externo' => $matricula['codigo_cargo_externo'],
            );

            if (!$ClienteFuncionario->incluir($dados)) {
                $msg_erro_matricula = "";
                if (!empty($ClienteFuncionario->validationErrors)) {
                    $msg_erro_matricula = ": " . implode(",", $ClienteFuncionario->validationErrors);
                }

                echo "[1] Falha ao incluir matrícula" . "\n";
                $campos[] = '[1] Falha ao incluir matrícula' . $msg_erro_matricula;
                $retorno['codigo_matricula'] = false;
                $retorno['invalidFields'] = implode(',', $campos);
                return $retorno;
            }

            $cliente_funcionario['ClienteFuncionario']['codigo'] = $ClienteFuncionario->id;
        } elseif (empty($cliente_funcionario['ClienteFuncionario']['chave_externa']) && !empty($matricula['chave_externa'])) {
            // Antes de inserir nova matricula com chave externa,
            if (isset($matricula['chave_externa']) && !empty($matricula['chave_externa'])) {

                // Antes de inserir nova matricula com chave externa,
                if (strlen($matricula['chave_externa']) < 26) {
                    echo "\n Falha ao incluir matrícula: [2] Campo chave_externa inválida: " . $matricula['chave_externa'] . "\n";
                    $campos[] = 'Campo chave_externa inválida: ' . $matricula['chave_externa'];
                    $retorno['codigo_matricula'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }

                $cpf  = substr($matricula['chave_externa'], 0, 11);
                $cnpj = substr($matricula['chave_externa'], 11, 14);
                $codigo_cargo_externo = substr($matricula['chave_externa'], 25);

                if ($cpf != $matricula['cpf'] || $cnpj != $matricula['cnpj_alocacao'] || $codigo_cargo_externo != $matricula['codigo_cargo_externo']) {
                    echo "\n Falha ao incluir matrícula: Campo chave_externa inválida: " . $matricula['chave_externa'] . "\n";
                    $campos[] = 'Campo chave_externa inválida: ' . $matricula['chave_externa'];
                    $retorno['codigo_matricula'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }

                //verifica se já existe uma chave externa igual cadastrada
                $cliente_funcionario_chave_externa = $ClienteFuncionario->find(
                    'all',
                    array('conditions' => array(
                        'ClienteFuncionario.chave_externa' => $matricula['chave_externa'],
                        'ClienteFuncionario.matricula'     => $matricula['matricula_funcionario'],
                        'ClienteFuncionario.codigo <> '    => $cliente_funcionario['ClienteFuncionario']['codigo']
                    ))
                );

                echo "\n";
                pr($cliente_funcionario['ClienteFuncionario']['codigo']);
                echo "\n";
                pr($cliente_funcionario_chave_externa);
                echo "\n";
                if (count($cliente_funcionario_chave_externa) > 0) {
                    echo "Falha ao incluir matrícula: [2] Já existe uma chave_externa com este código: " . $matricula['chave_externa'] . "\n";
                    $campos[] = '[2] Já existe uma chave_externa com este código: ' . $matricula['chave_externa'];
                    $retorno['codigo_matricula'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }
            }

            //verifica se o funcionario ja existe na base com uma matricula para atualizar o mesmo
            echo "\n VERIFICANDO SE O FUNCIONARIO TEM MATRICULA PARA ATUALIZACAO \n";
            $cf = $ClienteFuncionario->find('first', array('conditions' => array('codigo' => $matricula['codigo_matricula'])));

            if (!empty($cf)) {

                echo "\n Atualizando Matrícula com chave_externa \n";
                $dados = array(
                    'ClienteFuncionario' => array(
                        'codigo' => $cf['ClienteFuncionario']['codigo'],
                        'codigo_cliente_matricula' => $grupo_economico['GrupoEconomico']['codigo_cliente'],
                        'codigo_funcionario' => $codigo_funcionario,
                        'matricula' => $matricula['matricula_funcionario'],
                        'ativo' => $situacao[$matricula['situacao_cadastral']],
                        'admissao' => $matricula['data_admissao'],
                        'data_demissao' => $matricula['data_demissao'],
                        'centro_custo' => $matricula['centro_custo'],
                        'data_ultima_aso' => $matricula['data_ultimo_aso'],
                        'aptidao' => isset($aptidao[$matricula['aptidao']]) ? $aptidao[$matricula['aptidao']] : '',
                        'turno' => isset($turno[$matricula['turno']]) ? $turno[$matricula['turno']] : '',
                        'chave_externa' => $matricula['chave_externa'],
                        'codigo_cargo_externo' => $matricula['codigo_cargo_externo'],
                    )
                );

                if (!$ClienteFuncionario->atualizar($dados)) {
                    $msg_erro_matricula = "";
                    if (!empty($ClienteFuncionario->validationErrors)) {
                        $msg_erro_matricula = ": " . implode(",", $ClienteFuncionario->validationErrors);
                    }
                    echo "[2.1] Falha ao incluir matrícula com chave_externa: " . $msg_erro_matricula . ";\n";
                    $campos[] = '[2.1] Falha ao incluir matrícula com chave_externa' . $msg_erro_matricula;
                    $retorno['codigo_matricula'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }
            } else {

                echo "\n Incluir Matrícula com chave_externa \n";
                $dados = array(
                    'codigo_cliente_matricula' => $grupo_economico['GrupoEconomico']['codigo_cliente'],
                    'codigo_funcionario' => $codigo_funcionario,
                    'matricula' => $matricula['matricula_funcionario'],
                    'ativo' => $situacao[$matricula['situacao_cadastral']],
                    'admissao' => $matricula['data_admissao'],
                    'data_demissao' => $matricula['data_demissao'],
                    'centro_custo' => $matricula['centro_custo'],
                    'data_ultima_aso' => $matricula['data_ultimo_aso'],
                    'aptidao' => isset($aptidao[$matricula['aptidao']]) ? $aptidao[$matricula['aptidao']] : '',
                    'turno' => isset($turno[$matricula['turno']]) ? $turno[$matricula['turno']] : '',
                    'chave_externa' => $matricula['chave_externa'],
                    'codigo_cargo_externo' => $matricula['codigo_cargo_externo'],
                );

                if (!$ClienteFuncionario->incluir($dados)) {
                    $msg_erro_matricula = "";
                    if (!empty($ClienteFuncionario->validationErrors)) {
                        $msg_erro_matricula = ": " . implode(",", $ClienteFuncionario->validationErrors);
                    }
                    echo "[2] Falha ao incluir matrícula com chave_externa" . $msg_erro_matricula . "\n";
                    $campos[] = '[2] Falha ao incluir matrícula com chave_externa' . $msg_erro_matricula;
                    $retorno['codigo_matricula'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }
            }

            $cliente_funcionario['ClienteFuncionario']['codigo'] = $ClienteFuncionario->id;
        } else {

            if ($cliente_funcionario['ClienteFuncionario']['codigo_funcionario'] != $codigo_funcionario) {
                echo "Matrícula de outro funcionário" . "\n";
                $campos[] = 'Matrícula de outro funcionário';
                $retorno['codigo_matricula'] = false;
                $retorno['invalidFields'] = implode(',', $campos);
                return $retorno;
            } elseif ($cliente_funcionario['ClienteFuncionario']['codigo_cliente_matricula'] <> $grupo_economico['GrupoEconomico']['codigo_cliente']) {
                /**
                 * Se o codigo_cliente_matricula da cliente_funcionario for
                 * diferente do codigo_cliente no grupo economico
                 * - não permitir continuar se a empresa for diferente com cnpj diferente
                 * - permitir continuar atualizando os dados do funcionario na cliente_funcionario, se for o mesmo
                 *  codigo_documento entre cliente_funcionario x grupo economico na tabela cliente e uma delas estiver inativa
                 */

                $Cliente = &ClassRegistry::init('Cliente');
                $conditions = array(
                    'Cliente.codigo' => $cliente_funcionario['ClienteFuncionario']['codigo_cliente_matricula'],
                );

                // carrega os dados antigos na tabela cliente
                $cliente_1 = $Cliente->find('first', compact('conditions'));

                $conditions = array(
                    'Cliente.codigo' => $grupo_economico['GrupoEconomico']['codigo_cliente'],
                );

                // carrega dados atuais na tabela cliente
                $cliente_2 = $Cliente->find('first', compact('conditions'));

                /**
                 * ATUALIZA EM CLIENTE_FUNCIONARIO nas condições:
                 * 1) se cliente com dados antigos estiver inativo
                 * 2) se cnpj for igual
                 */
                if (
                    $cliente_1['Cliente']['ativo'] == 0
                    && ($cliente_1['Cliente']['codigo_documento'] == $cliente_2['Cliente']['codigo_documento'])
                ) {

                    $dataCF = array();
                    $dataCF['ClienteFuncionario']['codigo']                   = $cliente_funcionario['ClienteFuncionario']['codigo'];
                    $dataCF['ClienteFuncionario']['codigo_cliente']           = $grupo_economico['GrupoEconomico']['codigo_cliente'];
                    $dataCF['ClienteFuncionario']['codigo_cliente_matricula'] = $grupo_economico['GrupoEconomico']['codigo_cliente'];
                    $dataCF['ClienteFuncionario']['ativo']                    = 1;
                    $dataCF['ClienteFuncionario']['matricula']                = $cliente_funcionario['ClienteFuncionario']['matricula'];
                    $dataCF['ClienteFuncionario']['codigo_funcionario']       = $cliente_funcionario['ClienteFuncionario']['codigo_funcionario'];
                    $dataCF['ClienteFuncionario']['admissao']                 = $cliente_funcionario['ClienteFuncionario']['admissao'];

                    if (!$ClienteFuncionario->atualizar($dataCF)) {
                        // debug($ClienteFuncionario->validationErrors);
                        echo "Não foi possível atualizar" . "\n";
                        echo "Matrícula do funcionário não corresponde a este grupo econômico" . "\n";
                        $campos[] = 'Matrícula do funcionário não corresponde a este grupo econômico';
                        $retorno['codigo_matricula'] = false;
                        $retorno['invalidFields'] = implode(',', $campos);
                        return $retorno;
                    }

                    $conditions = array(
                        'ClienteFuncionario.codigo' => $cliente_funcionario['ClienteFuncionario']['codigo'],
                    );
                    // agora que atualizou os dados
                    // carrega novamente $cliente_funcionario pois lá
                    // embaixo vai retonar e devem ser o que acaba de ser atualizado
                    $cliente_funcionario = $ClienteFuncionario->find('first', compact('conditions'));
                } else {
                    echo "Matrícula do funcionário não corresponde a este grupo econômico" . "\n";
                    $campos[] = 'Matrícula do funcionário não corresponde a este grupo econômico';
                    $retorno['codigo_matricula'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }
            } else {
                if ($this->temDiferencaClienteFuncionario($cliente_funcionario, $matricula)) {

                    // Antes de inserir nuva matrica com chave externa,
                    echo "\n Editando matricula! \n";

                    if (isset($matricula['chave_externa']) && !empty($matricula['chave_externa'])) {

                        if (!empty($cliente_funcionario['ClienteFuncionario']['chave_externa'])) {

                            if (strlen($matricula['chave_externa']) < 26) {
                                echo "\n [3] Falha ao editar matrícula: Campo chave_externa inválida: " . $matricula['chave_externa'] . "\n";
                                $campos[] = '[3] Campo chave_externa inválida quantidade de caracteres abaixo do permitido: ' . $matricula['chave_externa'];
                                $retorno['codigo_matricula'] = false;
                                $retorno['invalidFields'] = implode(',', $campos);
                                return $retorno;
                            }
                        }

                        $cpf  = substr($matricula['chave_externa'], 0, 11);
                        $cnpj = substr($matricula['chave_externa'], 11, 14);
                        $codigo_cargo_externo = substr($matricula['chave_externa'], 25);

                        if ($cpf != $matricula['cpf'] || $cnpj != $matricula['cnpj_alocacao'] || $codigo_cargo_externo != $matricula['codigo_cargo_externo']) {
                            echo "\n Falha ao editar matrícula: Campo chave_externa inválida: " . $matricula['chave_externa'] . "\n";
                            $campos[] = 'Campo chave_externa inválida: ' . $matricula['chave_externa'];
                            $retorno['codigo_matricula'] = false;
                            $retorno['invalidFields'] = implode(',', $campos);
                            return $retorno;
                        }

                        //verifica se já existe uma chave externa igual cadastrada
                        $cliente_funcionario_chave_externa = $ClienteFuncionario->find(
                            'all',
                            array('conditions' => array(
                                'ClienteFuncionario.chave_externa' => $matricula['chave_externa'],
                                'ClienteFuncionario.matricula'     => $matricula['matricula_funcionario'],
                                'ClienteFuncionario.codigo <>'     => $cliente_funcionario['ClienteFuncionario']['codigo']
                            ))
                        );

                        if (count($cliente_funcionario_chave_externa) > 0) {
                            echo "\n Falha ao atualizar matrícula: [3] Já existem " . count($cliente_funcionario_chave_externa) . " chaves externas registradas com esse codigo: " . $matricula['chave_externa'] . "\n";
                            $campos[] = '[3] Já exite uma chave_externa com este código: ' . $matricula['chave_externa'];
                            $retorno['codigo_matricula'] = false;
                            $retorno['invalidFields'] = implode(',', $campos);
                            return $retorno;
                        }
                    }

                    $aptidao_aj = isset($matricula['aptidao']) ? $matricula['aptidao'] : NULL;
                    $aptidao_aj = isset($aptidao[$aptidao_aj]) ? $aptidao[$aptidao_aj] : NULL;

                    $cliente_funcionario['ClienteFuncionario']['ativo'] = $situacao[$matricula['situacao_cadastral']];
                    $cliente_funcionario['ClienteFuncionario']['admissao'] = $matricula['data_admissao'];
                    $cliente_funcionario['ClienteFuncionario']['data_demissao'] = $matricula['data_demissao'];
                    $cliente_funcionario['ClienteFuncionario']['centro_custo'] = $matricula['centro_custo'];
                    $cliente_funcionario['ClienteFuncionario']['data_ultima_aso'] = isset($matricula['data_ultimo_aso']) ? $matricula['data_ultimo_aso'] : null;
                    $cliente_funcionario['ClienteFuncionario']['aptidao'] = $aptidao_aj;
                    $cliente_funcionario['ClienteFuncionario']['turno'] = isset($turno[$matricula['turno']]) ? $turno[$matricula['turno']] : NULL;
                    $cliente_funcionario['ClienteFuncionario']['chave_externa'] = $matricula['chave_externa'];
                    $cliente_funcionario['ClienteFuncionario']['codigo_cargo_externo'] = $matricula['codigo_cargo_externo'];

                    //permite alterar a matrícula somente se o código da matrícula foi passado
                    if (!empty($matricula['codigo_matricula'])) {
                        $cliente_funcionario['ClienteFuncionario']['matricula'] = $matricula['matricula_funcionario'];
                    }

                    if (!$ClienteFuncionario->atualizar($cliente_funcionario)) {

                        $msg_erro_matricula = "";
                        if (!empty($ClienteFuncionario->validationErrors)) {
                            $msg_erro_matricula = ": " . implode(",", $ClienteFuncionario->validationErrors);
                        }

                        echo "Falha ao atualizar matrícula" . "\n";
                        $campos[] = 'Falha ao atualizar matrícula' . $msg_erro_matricula;
                        $retorno['codigo_matricula'] = false;
                        $retorno['invalidFields'] = implode(',', $campos);
                        return $retorno;
                    }
                }
            }
        }

        $retorno['codigo_matricula'] = $cliente_funcionario['ClienteFuncionario']['codigo'];
        return $retorno;
    } //FINAL FUNCTION importarMatricula

    function importarSetorCargo($setor_cargo, $codigo_grupo_economico, $codigo_cliente_funcionario, $codigo_alocacao, $codigo_funcionario)
    {

        $FuncionarioSetorCargo = &ClassRegistry::init('FuncionarioSetorCargo');
        $Setor = &ClassRegistry::init('Setor');
        $Cargo = &ClassRegistry::init('Cargo');
        $GrupoEconomico = &ClassRegistry::init('GrupoEconomico');
        $GrupoEconomicoCliente = &ClassRegistry::init('GrupoEconomicoCliente');
        $ClienteFuncionario = &ClassRegistry::init('ClienteFuncionario');

        $campos = array();
        $retorno = array();

        $grupo_economico = $GrupoEconomico->find('first', array(
            'joins' => array(
                array(
                    'table' => 'cliente',
                    'alias' => 'Cliente',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GrupoEconomico.codigo_cliente = Cliente.codigo'
                    )
                )
            ),

            'conditions' => array(
                'GrupoEconomico.codigo' => $codigo_grupo_economico,
                'Cliente.ativo' => 1
            )
        ));
        $conditions = array(
            'FuncionarioSetorCargo.codigo_cliente_funcionario' => $codigo_cliente_funcionario
        );
        $fields = array(
            'MAX(FuncionarioSetorCargo.codigo) AS codigo',
        );
        $ultimo_setor_cargo = $FuncionarioSetorCargo->find('sql', compact('conditions', 'fields'));
        $conditions = array(
            'FuncionarioSetorCargo.codigo_cliente_funcionario' => $codigo_cliente_funcionario,
            "FuncionarioSetorCargo.codigo = ({$ultimo_setor_cargo})"
        );
        $FuncionarioSetorCargo->bindModel(array('belongsTo' => array(
            'Setor' => array('foreignKey' => 'codigo_setor'),
            'Cargo' => array('foreignKey' => 'codigo_cargo'),
        )));
        $funcionario_setor_cargo = $FuncionarioSetorCargo->find('first', compact('conditions'));

        $incluir = false;
        $why = 0;
        $incluir_setor = false;
        $incluir_cargo = false;
        if (empty($funcionario_setor_cargo)) {
            $incluir = true;
            $conditions = array(
                'codigo_cliente' => $grupo_economico['GrupoEconomico']['codigo_cliente'],
                'descricao' => $setor_cargo['nome_setor']
            );
            $setor = $Setor->find('first', compact('conditions'));
            if (empty($setor)) {
                $incluir_setor = true;
            }
            $conditions = array(
                'codigo_cliente' => $grupo_economico['GrupoEconomico']['codigo_cliente'],
                'descricao' => $setor_cargo['nome_cargo']
            );
            $cargo = $Cargo->find('first', compact('conditions'));
            if (empty($cargo)) {
                $incluir_cargo = true;
            } else {
                $cargo['Cargo']['descricao_cargo'] = $setor_cargo['descricao_detalhada_cargo'];
                if (!$Cargo->atualizar($cargo)) {
                    echo 'Falha ao atualizar cargo' . "\n";
                    $campos[] = 'Falha ao atualizar cargo';
                    $retorno['invalidFields'] = implode(',', $campos);
                }
            }
        } else {

            $setor['Setor'] = $funcionario_setor_cargo['Setor'];
            if (trim($funcionario_setor_cargo['Setor']['descricao']) <> trim($setor_cargo['nome_setor'])) {
                $conditions = array(
                    'codigo_cliente' => $grupo_economico['GrupoEconomico']['codigo_cliente'],
                    'descricao' => $setor_cargo['nome_setor']
                );
                $setor = $Setor->find('first', compact('conditions'));
                if (empty($setor)) {
                    $incluir_setor = true;
                }
                $incluir = true;
            }
            $cargo['Cargo'] = $funcionario_setor_cargo['Cargo'];
            if (trim($funcionario_setor_cargo['Cargo']['descricao']) <> trim($setor_cargo['nome_cargo'])) {
                $conditions = array(
                    'codigo_cliente' => $grupo_economico['GrupoEconomico']['codigo_cliente'],
                    'descricao' => $setor_cargo['nome_cargo'],
                );
                $cargo = $Cargo->find('first', compact('conditions'));
                if (empty($cargo)) {
                    $incluir_cargo = true;
                } else {
                    $cargo['Cargo']['descricao_cargo'] = $setor_cargo['descricao_detalhada_cargo'];
                    if (!$Cargo->atualizar($cargo)) {
                        echo 'Falha ao atualizar cargo' . "\n";
                        $campos[] = 'Falha ao atualizar cargo';
                        $retorno['invalidFields'] = implode(',', $campos);
                    }
                }
                $incluir = true;
            } else {

                //verifica se existe diferença de descrição do cargo
                if ($this->temDiferencaCargo($cargo, $setor_cargo)) {

                    $cargo['Cargo']['descricao_cargo'] = $setor_cargo['descricao_detalhada_cargo'];

                    if (!$Cargo->atualizar($cargo)) {
                        echo 'Falha ao atualizar cargo' . "\n";
                        $campos[] = 'Falha ao atualizar cargo';
                        $retorno['invalidFields'] = implode(',', $campos);
                    }
                }
            }
        }

        if ($incluir_setor) {
            echo "Incluir setor" . "\n";
            $dados = array('Setor' => array(
                'codigo_cliente' => $grupo_economico['GrupoEconomico']['codigo_cliente'],
                'descricao' => $setor_cargo['nome_setor'],
                'ativo' => 1,
            ));
            if (!$Setor->incluir($dados)) {
                echo 'Falha ao cadastrar Setor' . "\n";
                $campos[] = 'Falha ao cadastrar Setor';
                $retorno['codigo_setor_cargo'] = false;
                $retorno['invalidFields'] = implode(',', $campos);
                return $retorno;
            }
            $setor['Setor']['codigo'] = $Setor->id;
        }
        if ($incluir_cargo) {
            echo "Incluir cargo" . "\n";
            $dados = array('Cargo' => array(
                'codigo_cliente' => $grupo_economico['GrupoEconomico']['codigo_cliente'],
                'descricao' => $setor_cargo['nome_cargo'],
                'descricao_cargo' => !empty($setor_cargo['descricao_detalhada_cargo']) ? $setor_cargo['descricao_detalhada_cargo'] : NULL,
                'ativo' => 1,
            ));
            if (!$Cargo->incluir($dados)) {
                echo 'Falha ao cadastrar Cargo' . "\n";
                $campos[] = 'Falha ao cadastrar Cargo';
                $retorno['codigo_setor_cargo'] = false;
                $retorno['invalidFields'] = implode(',', $campos);
                return $retorno;
            }
            $cargo['Cargo']['codigo'] = $Cargo->id;
        }
        if ($incluir) {
            if (!empty($funcionario_setor_cargo)) {

                echo 'Encontrado setor e cargo ativo, finalizar' . "\n";
                $ultimo_fun_setor_cargo = $FuncionarioSetorCargo->read(null, $funcionario_setor_cargo['FuncionarioSetorCargo']['codigo']);
                $data_inicio_cargo_anterior = $ultimo_fun_setor_cargo['FuncionarioSetorCargo']['data_inicio'];
                $data_fim_cargo = AppModel::dateToDbDate2($setor_cargo['data_inicio_cargo']) . ' -1 day';

                //Se a data de início do cargo anterior for maior que a data fim
                if (new DateTime(AppModel::dateToDbDate2($data_inicio_cargo_anterior)) > new DateTime(date('Y-m-d', strtotime($data_fim_cargo)))) {
                    $campos[] = 'Falha ao finalizar cargo anterior - data de início maior que data final';
                    $retorno['codigo_setor_cargo'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }


                $FuncionarioSetorCargo->set('data_fim', date('d/m/Y', strtotime($data_fim_cargo)));

                if (!$FuncionarioSetorCargo->save(null, false)) {
                    echo 'Falha ao finalizar cargo anterior' . "\n";
                    $campos[] = 'Falha ao finalizar cargo anterior';
                    $retorno['codigo_setor_cargo'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }
            }

            // **************************
            // deve existir um codigo na tabela cliente_funcionario
            // aqui deve ser incluido em clientefuncionario por causa da fk_funcionario_setores_cargos__cliente_funcionario
            // if (isset($setor_cargo['codigo_cliente_funcionario']) && empty($setor_cargo['codigo_cliente_funcionario'])){

            // 	$codigo_cliente = $grupo_economico['GrupoEconomico']['codigo_cliente'];

            // 	echo "Incluir na ClienteFuncionario"."\n";
            // 	$dados = array('ClienteFuncionario' => array(
            // 		'codigo_cliente' => $codigo_cliente,
            // 		'codigo_funcionario' => $codigo_funcionario,
            // 		'codigo_cliente_matricula' => $codigo_cliente_funcionario,
            // 		'ativo' => 1,
            // 		'admissao'=> $setor_cargo['data_admissao'],
            // 		'matricula' => $setor_cargo['matricula_funcionario']

            // 	));

            // 	//INSERE NA TABELA DE RELACIONAMENTO CLIENTE X FUNCIONARIO.
            // 	if(!$ClienteFuncionario->incluir($dados)){
            // 		echo 'Falha ao incluir cliente x funcionario'."\n";
            // 		$campos[] = 'Falha ao incluir relação cliente funcionário';
            // 		$retorno['codigo_setor_cargo'] = false;
            // 		$retorno['invalidFields'] = implode(',', $campos);
            // 		return $retorno;
            // 	}

            // }

            echo "Incluir Funcionario Setor Cargo" . "\n";
            $dados = array('FuncionarioSetorCargo' => array(
                'codigo_cliente_funcionario' => $codigo_cliente_funcionario,
                'codigo_cliente_alocacao' => $codigo_alocacao,
                'codigo_setor' => $setor['Setor']['codigo'],
                'codigo_cargo' => $cargo['Cargo']['codigo'],
                'data_inicio' => AppModel::dateToDbDate($setor_cargo['data_inicio_cargo'])
            ));

            if (!$FuncionarioSetorCargo->incluir($dados)) {
                echo 'Falha ao incluir setor e cargo' . "\n";
                $campos[] = 'Falha ao incluir setor e cargo';
                $retorno['codigo_setor_cargo'] = false;
                $retorno['invalidFields'] = implode(',', $campos);
                return $retorno;
            }
            $funcionario_setor_cargo['FuncionarioSetorCargo']['codigo'] = $FuncionarioSetorCargo->id;
        } else {

            if ($this->temDiferencaFuncionarioSetorCargo($funcionario_setor_cargo, $codigo_alocacao, $setor['Setor']['codigo'], $cargo['Cargo']['codigo'], $setor_cargo['data_inicio_cargo'], $setor_cargo['data_demissao'])) {

                echo "Atualizar Funcionario Setor Cargo" . "\n";

                $funcionario_setor_cargo['FuncionarioSetorCargo']['codigo_cliente_alocacao'] = $codigo_alocacao;
                $funcionario_setor_cargo['FuncionarioSetorCargo']['codigo_setor'] = $setor['Setor']['codigo'];
                $funcionario_setor_cargo['FuncionarioSetorCargo']['codigo_cargo'] = $cargo['Cargo']['codigo'];
                $funcionario_setor_cargo['FuncionarioSetorCargo']['data_inicio'] = AppModel::dateToDbDate($setor_cargo['data_inicio_cargo']);
                $funcionario_setor_cargo['FuncionarioSetorCargo']['data_fim'] = AppModel::dateToDbDate($setor_cargo['data_demissao']);

                $verifica_hierarquia_inativa = $this->AtivarHierarquia($funcionario_setor_cargo);

                if (!$FuncionarioSetorCargo->atualizar($funcionario_setor_cargo)) {
                    echo 'Falha ao atualizar setor e cargo' . "\n";
                    $campos[] = 'Falha ao atualizar setor e cargo';
                    $retorno['codigo_setor_cargo'] = false;
                    $retorno['invalidFields'] = implode(',', $campos);
                    return $retorno;
                }
            } else {
                echo "Funcionario Setor Cargo sem atualização pendente" . "\n";
            }
        }

        $retorno['codigo_setor_cargo'] = $funcionario_setor_cargo['FuncionarioSetorCargo']['codigo'];
        return $retorno;
    } //FINAL FUNCTION importarSetorCargo

    function importarMedicoCoord($dados)
    {
        $Medico = &ClassRegistry::init('Medico');
        $Cliente = &ClassRegistry::init('Cliente');

        $base_cliente = $Cliente->read(null, $dados['codigo_alocacao']);
        $base_cliente['Cliente']['ccm'] = (empty($base_cliente['Cliente']['ccm']) ? $base_cliente['Cliente']['inscricao_estadual'] : $base_cliente['Cliente']['ccm']);

        $dados_atualizar = $base_cliente;
        $base_cliente = (!empty($base_cliente['Cliente']['codigo_medico_pcmso']) ? $base_cliente['Cliente']['codigo_medico_pcmso'] : 0);

        $conditions = array('Medico.numero_conselho' => $dados['num_cons'], 'Medico.conselho_uf' => $dados['cons_uf'], 'Medico.ativo' => 1);
        $codigo_medico = $Medico->find('first', array("conditions" => $conditions, 'recursive' => -1));

        // debug($Medico->find('sql', array("conditions" => $conditions)));
        // debug($codigo_medico);

        $codigo_medico = (!empty($codigo_medico['Medico']['codigo']) ? $codigo_medico['Medico']['codigo'] : 0);

        if ($this->temDiferencaMedicoCoord($base_cliente, $codigo_medico)) {
            echo "Atualizar Médico PCMSO \n";
            $dados_atualizar['Cliente']['codigo_medico_pcmso'] = $codigo_medico;
            $Cliente->set('codigo_medico_pcmso', $codigo_medico);
            if (!$Cliente->save(null, false)) {
                echo "Falha ao atualizar medico coordenador" . "\n";
                $campos[] = "Falha ao atualizar medico coordenador";
                $retorno['codigo_medico_pcmso'] = false;
                $retorno['invalidFields'] = implode(',', $campos);
                return $retorno;
            }
        } else {
            echo "Medico coordenador sem atualização pendente" . "\n";
        }
        return $codigo_medico;
    } //FINAL FUNCTION importarMedicoCoord

    function temDiferencaMedicoCoord($base_cliente, $codigo_medico)
    {
        if ($base_cliente == $codigo_medico)
            return false;
        else return true;
    } //FINAL FUNCTION temDiferencaMedicoCoord

    /**
     * [validaAtivoCodigoAlocacao método para validar se o codigo alocação passado por parametro está ativo]
     * @param  [int] 	$codigo_alocacao [codigo do cliente alocação]
     * @return [boolean]
     */
    public function validaAtivoCodigoAlocacao($codigo_alocacao)
    {
        $Cliente = ClassRegistry::init('Cliente');

        $conditions = array('Cliente.codigo' => $codigo_alocacao, 'ativo' => 1);

        $retorno = $Cliente->find('count', compact('conditions'));

        return $retorno > 0 ? true : false;
    } //FINAL FUNCTION validaAtivoCodigoAlocacao

    public function AtivarHierarquia($funcionario_setor_cargo)
    {
        $this->ClienteSetorCargo = ClassRegistry::init('ClienteSetorCargo');

        $conditions = array(
            'ClienteSetorCargo.codigo_cliente_alocacao' => $funcionario_setor_cargo['FuncionarioSetorCargo']['codigo_cliente_alocacao'],
            'ClienteSetorCargo.codigo_setor' => $funcionario_setor_cargo['FuncionarioSetorCargo']['codigo_setor'],
            'ClienteSetorCargo.codigo_cargo' => $funcionario_setor_cargo['FuncionarioSetorCargo']['codigo_cargo']
        );

        $hierarquia = $this->ClienteSetorCargo->getHierarquias($conditions, false, $funcionario_setor_cargo['ClienteFuncionario']['codigo_cliente_matricula'], true);

        if ($hierarquia[0]['ClienteSetorCargo']['ativo'] == 0) {

            $data = array(
                'ClienteSetorCargo' => array(
                    'codigo' => $hierarquia[0]['ClienteSetorCargo']['codigo'],
                    'codigo_cliente' => $funcionario_setor_cargo['ClienteFuncionario']['codigo_cliente_matricula'],
                    'codigo_cliente_alocacao' => $funcionario_setor_cargo['FuncionarioSetorCargo']['codigo_cliente_alocacao'],
                    'codigo_setor' => $funcionario_setor_cargo['FuncionarioSetorCargo']['codigo_setor'],
                    'codigo_cargo' => $funcionario_setor_cargo['FuncionarioSetorCargo']['codigo_cargo'],
                    'ativo' => 1,
                )
            );

            $this->ClienteSetorCargo->atualizar($data);
        }
    }
}//FINAL CLASSRegistroImportacao
