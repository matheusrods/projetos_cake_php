<?php
class GrupoEconomico extends AppModel
{
    var $name = 'GrupoEconomico';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'grupos_economicos';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_grupos_economicos'));
    var $validate = array(
        'descricao' => array(
            'rule' => array('notEmpty'),
            'message' => 'Informe a descrição',
            'required' => true,
            'allowEmpty' => false,
        ),
        'codigo_cliente' => array(
            array(
                'rule' => array('notEmpty'),
                'message' => 'Cliente não informado',
                'required' => true,
                'allowEmpty' => false,
            ),
            array(
                'rule' => array('isUnique'),
                'message' => 'Cliente já tem Grupo Econômico',
            ),
        ),
    );

    function converteFiltrosEmConditions($filtros)
    {
        $conditions = array();
        if (isset($filtros['descricao']) && !empty($filtros['descricao'])) {
            $conditions[$this->name . '.descricao LIKE'] = $filtros['descricao'] . '%';
        }
        return $conditions;
    }

    function retornaCodigoMatriz($codigo_grupo_economico)
    {

        return $this->find('first', array('conditions' => array('codigo' => $codigo_grupo_economico), 'fields' => array('codigo_cliente')));
    }

    function codigoMatrizPeloCodigoFilial($codigo_cliente)
    {
        if (empty($codigo_cliente) || $codigo_cliente == 0) {
            return '0';
        }

        if (is_array($codigo_cliente)) {
            $codigo_cliente = implode(",", $codigo_cliente);
        }

        $matriz = $this->find(
            'first',
            array(
                'conditions' => array(
                    "GrupoEconomicoCliente.codigo_cliente IN ({$codigo_cliente}) "
                ),
                'joins' => array(
                    array(
                        'table' => 'grupos_economicos_clientes',
                        'alias' => 'GrupoEconomicoCliente',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
                        )
                    )
                ),
                'fields' => array(
                    'GrupoEconomico.codigo_cliente'
                )
            )
        );
        return $matriz['GrupoEconomico']['codigo_cliente'];
    }

    function incluir($dados)
    {
        $implantacao = &ClassRegistry::init('ClienteImplantacao');
        $grupos_economicos_clientes = &ClassRegistry::init('GrupoEconomicoCliente');


        if (!parent::incluir($dados)) {
            return false;
        }
        // else {

        //Verifica se existe registro na tabela de grupos_economicos_clientes
        $registro_cliente = $grupos_economicos_clientes->find('count', array(
            'conditions' => array('codigo_cliente' => $dados['GrupoEconomico']['codigo_cliente']),
            'recursive' => -1
        ));

        //Se não existe inclui registro
        if ($registro_cliente == 0) {
            $grupos_economicos_clientes->incluir(array(
                'codigo_cliente' => $dados['GrupoEconomico']['codigo_cliente'],
                'codigo_grupo_economico' => $this->id
            ));
        }

        //Verifica se este cliente possui registro na tabela de implantacao
        $registro_implantacao = $implantacao->find('count', array(
            'conditions' => array('codigo_cliente' => $dados['GrupoEconomico']['codigo_cliente'])
        ));

        //Se não existe inclui registro
        if ($registro_implantacao == 0) {
            $implantacao->incluir(array('codigo_cliente' => $dados['GrupoEconomico']['codigo_cliente']));
        }


        return true;
        // }
    }

    /**
     * Metodo para atualizar o grupo economico e cliente implantacao para apresentar na busca do implantacao
     *
     * params:
     * $data = array com os dados a serem atualizados
     *
     */
    public function atualizar($dados)
    {

        //verifica se atualizou corretamente
        if (!parent::atualizar($dados)) {
            return false;
        }

        //chama outras classes
        $implantacao = &ClassRegistry::init('ClienteImplantacao');
        //verifica se o codigo cliente existe na base de cliente_implantacao
        $registro_implantacao = $implantacao->find('first', array(
            'fields' => array('codigo_cliente'),
            'conditions' => array('codigo_cliente' => $dados['GrupoEconomico']['codigo_cliente'])
        ));

        //verifica se existe o registro implantacao
        if (empty($registro_implantacao)) {

            //limpa a base de implantacao
            $grupos_economicos_clientes = &ClassRegistry::init('GrupoEconomicoCliente');

            //Verifica se existe registro na tabela de grupos_economicos_clientes
            $registros_clientes = $grupos_economicos_clientes->find('list', array(
                'fields'        => array('codigo_cliente'),
                'conditions'    => array('codigo_grupo_economico' => $dados['GrupoEconomico']['codigo']),
                'recursive'     => -1
            ));

            //deleta as implantacoes, e relaciona a implantacao correta
            foreach ($registros_clientes as $codigo => $codigo_cliente) {

                //verifica se o codigo cliente existe na base de cliente_implantacao
                $imp = $implantacao->find('first', array(
                    'fields' => array('codigo'),
                    'conditions' => array('codigo_cliente' => $codigo_cliente)
                ));

                if (!empty($imp)) {
                    $implantacao->delete($imp["ClienteImplantacao"]['codigo']);
                }
            } //fim foreach

            //refaz a referencia da implantacao
            $implantacao->incluir(array('codigo_cliente' => $dados['GrupoEconomico']['codigo_cliente']));
        } //fim registro implantacacao

        return true;
    } //fim atualizar

    public function queryEstrutura($codigo_cliente_principal, $somente_ativos = true, $params = null)
    {
        App::import('model', 'TipoContato');
        App::import('model', 'TipoRetorno');
        App::import('model', 'TipoRetorno');

        $Setor = &ClassRegistry::init('Setor');
        $Cargo = &ClassRegistry::init('Cargo');
        $Cliente = &ClassRegistry::init('Cliente');
        $ClienteEndereco = &ClassRegistry::init('ClienteEndereco');
        $FuncionarioContato = &ClassRegistry::init('FuncionarioContato');
        $Cnae = &ClassRegistry::init('Cnae');
        $GrupoEconomicoCliente = &ClassRegistry::init('GrupoEconomicoCliente');

        $FuncionarioSetorCargo = &ClassRegistry::init('FuncionarioSetorCargo');

        $status_matricula = array();

        if (!is_null($params)) {

            $params = explode("|", $params);

            if ($params[0] == 0) {
                $unidades = $GrupoEconomicoCliente->lista2($codigo_cliente_principal);

                $conditionsParams = array(
                    "Alocacao.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao",
                    "FuncionarioSetorCargo.codigo_cliente_alocacao IN ({$unidades})",
                );
            } else {
                $conditionsParams = array(
                    "Alocacao.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao",
                    "FuncionarioSetorCargo.codigo_cliente_alocacao IN ({$params[0]})",
                );
            }

            $status_matricula = array(
                // 'ClienteFuncionario.data_demissao' => null,
                "ClienteFuncionario.ativo IN ({$params[1]})"
            );
        } else {

            if ($somente_ativos) {
                $status_matricula = array(
                    'ClienteFuncionario.data_demissao' => null,
                    'ClienteFuncionario.ativo >' => 0
                );
            }
        }


        // pr($status_matricula);exit;
        /* comentado para pegar todos os funcionarios mesmo os demitidos, junto dos ativos
        else {
            $status_matricula = array('ClienteFuncionario.data_demissao IS NOT NULL');
        }*/
        $this->bindModel(array('hasOne' => array(
            'GrupoEconomicoCliente' => array('foreignKey' => 'codigo_grupo_economico'),
            'ClienteFuncionario' => array('foreignKey' => false, 'conditions' => array(
                'ClienteFuncionario.codigo_cliente_matricula = GrupoEconomicoCliente.codigo_cliente',
                $status_matricula
            )),
            'Funcionario' => array('foreignKey' => false, 'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'),
            'FuncionarioEndereco' => array('foreignKey' => false, 'conditions' => array(
                'FuncionarioEndereco.codigo_funcionario = Funcionario.codigo',
                'FuncionarioEndereco.codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
                'FuncionarioEndereco.codigo = (SELECT TOP 1 FUNCIONARIOENDERECO2.codigo AS codigo FROM RHHealth.dbo.funcionarios_enderecos as FUNCIONARIOENDERECO2 WHERE FUNCIONARIOENDERECO2.codigo_funcionario = Funcionario.codigo)'
            ))
        )));


        $fields = $this->findExportacaoFieldsBase();
        $conditions = array('GrupoEconomico.codigo_cliente' => $codigo_cliente_principal);
        $cte = "WITH Base AS (" . $this->find('sql', compact('conditions', 'fields')) . ")";
        $dbo = $this->getDataSource();

        $query = $dbo->buildStatement(array(
            'fields' => $this->findExportacaoFields(),
            'table' => "Base",
            'alias' => 'Estrutura',
            'limit' => (isset($options['limit']) ? $options['limit'] : null),
            'joins' => array(
                array(
                    'table' => "{$FuncionarioSetorCargo->databaseTable}.{$FuncionarioSetorCargo->tableSchema}.{$FuncionarioSetorCargo->useTable}",
                    'alias' => 'FuncionarioSetorCargo',
                    'type' => 'LEFT',
                    'conditions' => 'FuncionarioSetorCargo.codigo = Estrutura.codigo_func_setor_cargo'
                ),
                array(
                    'table' => "{$Setor->databaseTable}.{$Setor->tableSchema}.{$Setor->useTable}",
                    'alias' => 'Setor',
                    'type' => 'LEFT',
                    'conditions' => 'Setor.codigo = FuncionarioSetorCargo.codigo_setor'
                ),
                array(
                    'table' => "{$Cargo->databaseTable}.{$Cargo->tableSchema}.{$Cargo->useTable}",
                    'alias' => 'Cargo',
                    'type' => 'LEFT',
                    'conditions' => 'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo'
                ),
                array(
                    'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
                    'alias' => 'Alocacao',
                    'type' => 'LEFT',
                    'conditions' => $conditionsParams
                ),
                array(
                    'table' => $FuncionarioContato->databaseTable . "." . $FuncionarioContato->tableSchema . "." . $FuncionarioContato->useTable,
                    'alias' => 'FuncionarioContatoCelular',
                    'conditions' => 'FuncionarioContatoCelular.codigo = Estrutura.func_contato_codigo_celular',
                    'type' => 'LEFT'
                ),
                array(
                    'table' => $FuncionarioContato->databaseTable . "." . $FuncionarioContato->tableSchema . "." . $FuncionarioContato->useTable,
                    'alias' => 'FuncionarioContatoEmail',
                    'conditions' => 'FuncionarioContatoEmail.codigo = Estrutura.func_contato_codigo_email',
                    'type' => 'LEFT'
                ),
                array(
                    'table' => "{$ClienteEndereco->databaseTable}.{$ClienteEndereco->tableSchema}.{$ClienteEndereco->useTable}",
                    'alias' => 'ClienteEndereco',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'ClienteEndereco.codigo_cliente = Alocacao.codigo',
                        'ClienteEndereco.codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
                    )
                ),
                array(
                    'table' => $Cnae->databaseTable . "." . $Cnae->tableSchema . "." . $Cnae->useTable,
                    'alias' => 'Cnae',
                    'conditions' => 'Cnae.cnae = Alocacao.cnae',
                    'type' => 'LEFT'
                ),
                array(
                    'table' => 'medicos',
                    'alias' => 'Medicos',
                    'conditions' => 'Alocacao.codigo_medico_pcmso = Medicos.codigo',
                    'type' => 'LEFT'
                ),
                array(
                    'table' => 'conselho_profissional',
                    'alias' => 'ConselhoProfissional',
                    'conditions' => 'Medicos.codigo_conselho_profissional = ConselhoProfissional.codigo',
                    'type' => 'LEFT'
                ),
            ),
            'offset' => null,
            'conditions' => null,
            'order' => (isset($options['order']) ? $options['order'] : null),
            'group' => null,
        ), $this);

        // pr ($cte.$query)
        return $cte . $query;
    }

    private function findExportacaoFields()
    {
        $ClienteContato = &ClassRegistry::init('ClienteContato');
        return array(
            'Alocacao.codigo AS codigo_alocacao',
            'Alocacao.nome_fantasia AS alocacao_nome_fantasia',
            'Setor.descricao AS nome_setor',
            'Cargo.descricao AS nome_cargo',
            'Estrutura.matricula AS matricula',
            'Estrutura.nome_funcionario AS nome_funcionario',
            'Estrutura.data_nascimento AS data_nascimento',
            'Estrutura.sexo AS sexo',
            'Estrutura.codigo_cliente_funcionario AS codigo_matricula',
            'Estrutura.status_matricula AS status_matricula',
            'Estrutura.data_admissao AS data_admissao',
            'Estrutura.data_demissao AS data_demissao',
            'FuncionarioSetorCargo.data_inicio AS data_inicio_cargo',
            'Estrutura.estado_civil AS estado_civil',
            'Estrutura.pispasep AS pispasep',
            'Estrutura.rg AS rg',
            'Estrutura.rg_orgao AS rg_orgao',
            'Estrutura.cpf AS cpf',
            'Estrutura.ctps AS ctps',
            'Estrutura.ctps_serie AS ctps_serie',
            'Estrutura.ctps_uf AS ctps_uf',
            'Estrutura.endereco',
            'Estrutura.endereco_numero',
            'Estrutura.endereco_complemento',
            'Estrutura.bairro',
            'Estrutura.cidade',
            'Estrutura.uf',
            'Estrutura.cep',
            'Estrutura.funcionario_deficiencia AS funcionario_deficiencia',
            'Cargo.codigo_cbo AS codigo_cbo',
            'Estrutura.funcionario_gfip AS gfip',
            'Estrutura.centro_custo AS centro_custo',
            'Estrutura.data_ultima_aso AS data_ultima_aso',
            'Estrutura.aptidao AS aptidao',
            'Estrutura.turno AS turno',
            'Estrutura.chave_externa AS chave_externa',
            'Estrutura.data_inclusao_funcionario AS data_inclusao_funcionario',
            'Estrutura.codigo_cargo_externo as codigo_cargo_externo',
            'Cargo.descricao_cargo AS cargo_descricao_detalhada',
            'FuncionarioContatoCelular.descricao AS celular_funcionario',
            'FuncionarioContatoCelular.autoriza_envio_sms AS autoriza_envio_sms',
            'FuncionarioContatoEmail.descricao AS email_funcionario',
            'FuncionarioContatoEmail.autoriza_envio_email AS autoriza_envio_email',
            "(SELECT TOP 1 nome 
              FROM {$ClienteContato->databaseTable}.{$ClienteContato->tableSchema}.{$ClienteContato->useTable} AS ClienteContato
              WHERE ClienteContato.codigo_cliente = Alocacao.codigo 
                AND ClienteContato.codigo_tipo_retorno=" . TipoRetorno::TIPO_RETORNO_TELEFONE . "
                AND ClienteContato.codigo_tipo_contato=" . TipoContato::TIPO_CONTATO_COMERCIAL . "
              ORDER BY ClienteContato.codigo
            ) AS contato_alocacao",
            "(SELECT TOP 1 descricao 
              FROM {$ClienteContato->databaseTable}.{$ClienteContato->tableSchema}.{$ClienteContato->useTable} AS ClienteContato
              WHERE ClienteContato.codigo_cliente = Alocacao.codigo 
                AND ClienteContato.codigo_tipo_retorno=" . TipoRetorno::TIPO_RETORNO_TELEFONE . "
                AND ClienteContato.codigo_tipo_contato=" . TipoContato::TIPO_CONTATO_COMERCIAL . "
              ORDER BY ClienteContato.codigo
            ) AS telefone_alocacao",
            "(SELECT TOP 1 descricao 
              FROM {$ClienteContato->databaseTable}.{$ClienteContato->tableSchema}.{$ClienteContato->useTable} AS ClienteContato
              WHERE ClienteContato.codigo_cliente = Alocacao.codigo 
                AND ClienteContato.codigo_tipo_retorno=" . TipoRetorno::TIPO_RETORNO_EMAIL . "
                AND ClienteContato.codigo_tipo_contato=" . TipoContato::TIPO_CONTATO_COMERCIAL . "
              ORDER BY ClienteContato.codigo
            ) AS email_alocacao",
            "ClienteEndereco.logradouro AS alocacao_endereco",
            'ClienteEndereco.numero as alocacao_endereco_numero',
            'ClienteEndereco.complemento as alocacao_endereco_complemento',
            'ClienteEndereco.bairro AS alocacao_bairro',
            'ClienteEndereco.cidade AS alocacao_cidade',
            'ClienteEndereco.estado_abreviacao AS alocacao_uf',
            'ClienteEndereco.cep AS alocacao_cep',
            "(CASE 
                WHEN Alocacao.codigo_documento_real is null THEN Alocacao.codigo_documento
                WHEN Alocacao.codigo_documento_real = '' THEN Alocacao.codigo_documento
                WHEN Alocacao.codigo_documento IS NULL THEN Alocacao.codigo_documento_real
                else Alocacao.codigo_documento_real
            END) AS alocacao_cnpj",
            'Alocacao.inscricao_estadual AS alocacao_inscricao_estadual',
            'Alocacao.ccm AS alocacao_ccm',
            'Alocacao.cnae AS alocacao_cnae',
            'Cnae.grau_risco AS cnae_grau_risco',
            'Alocacao.razao_social AS alocacao_razao_social',
            'Alocacao.codigo_regime_tributario AS alocacao_cod_regime_tribut',
            'Alocacao.codigo_externo AS alocacao_codigo_externo',
            'Alocacao.tipo_unidade AS alocacao_tipo_unidade',
            'ConselhoProfissional.descricao AS conselhoprofissional_descricao',
            'Medicos.numero_conselho AS numero_conselho',
            'Medicos.conselho_uf AS conselho_uf',
        );
    }

    private function findExportacaoFieldsBase()
    {
        $FuncionarioSetorCargo = &ClassRegistry::init('FuncionarioSetorCargo');
        $FuncionarioContato = &ClassRegistry::init('FuncionarioContato');
        $ClienteFuncionario = &ClassRegistry::init('ClienteFuncionario');

        return array(
            "ClienteFuncionario.codigo AS codigo_cliente_funcionario",
            "(SELECT TOP 1 FuncionarioSetorCargo.codigo 
                 FROM {$FuncionarioSetorCargo->databaseTable}.{$FuncionarioSetorCargo->tableSchema}.{$FuncionarioSetorCargo->useTable} AS FuncionarioSetorCargo WHERE FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY FuncionarioSetorCargo.codigo DESC
                ) AS codigo_func_setor_cargo",
            'ClienteFuncionario.matricula AS matricula',
            'ClienteFuncionario.ativo AS status_matricula',
            'ClienteFuncionario.codigo_funcionario AS codigo_funcionario',
            'ClienteFuncionario.admissao AS data_admissao',
            'ClienteFuncionario.data_inclusao AS data_inclusao',
            'ClienteFuncionario.codigo_usuario_inclusao AS codigo_usuario_inclusao',
            'ClienteFuncionario.codigo_empresa AS codigo_empresa',
            'ClienteFuncionario.matricula AS matricula',
            'ClienteFuncionario.data_demissao AS data_demissao',
            'ClienteFuncionario.centro_custo AS centro_custo',
            'ClienteFuncionario.data_ultima_aso AS data_ultima_aso',
            'ClienteFuncionario.aptidao AS aptidao',
            'ClienteFuncionario.turno AS turno',
            'ClienteFuncionario.chave_externa as chave_externa',
            'ClienteFuncionario.codigo_cargo_externo as codigo_cargo_externo',
            'Funcionario.data_inclusao AS data_inclusao_funcionario',
            'Funcionario.nome AS nome_funcionario',
            'Funcionario.data_nascimento AS data_nascimento',
            'Funcionario.sexo AS sexo',
            'Funcionario.estado_civil AS estado_civil',
            'Funcionario.nit AS pispasep',
            'Funcionario.rg AS rg',
            'Funcionario.rg_orgao AS rg_orgao',
            'Funcionario.cpf AS cpf',
            'Funcionario.ctps AS ctps',
            'Funcionario.ctps_serie AS ctps_serie',
            'Funcionario.ctps_uf AS ctps_uf',
            "FuncionarioEndereco.logradouro AS endereco",
            'FuncionarioEndereco.numero as endereco_numero',
            'FuncionarioEndereco.complemento as endereco_complemento',
            'FuncionarioEndereco.bairro AS bairro',
            'FuncionarioEndereco.cidade AS cidade',
            'FuncionarioEndereco.estado_abreviacao AS uf',
            'FuncionarioEndereco.cep AS cep',
            'Funcionario.deficiencia AS funcionario_deficiencia',
            'Funcionario.gfip AS funcionario_gfip',
            "(SELECT TOP 1 codigo 
              FROM {$FuncionarioContato->databaseTable}.{$FuncionarioContato->tableSchema}.{$FuncionarioContato->useTable} AS FuncionarioContato
              WHERE FuncionarioContato.codigo_funcionario = Funcionario.codigo 
                AND FuncionarioContato.codigo_tipo_retorno=" . TipoRetorno::TIPO_RETORNO_CELULAR_MOTORISTA . "
                AND FuncionarioContato.codigo_tipo_contato=" . TipoContato::TIPO_CONTATO_COMERCIAL . "
              ORDER BY FuncionarioContato.codigo
            ) AS func_contato_codigo_celular",
            "(SELECT TOP 1 codigo 
              FROM {$FuncionarioContato->databaseTable}.{$FuncionarioContato->tableSchema}.{$FuncionarioContato->useTable} AS FuncionarioContato
              WHERE FuncionarioContato.codigo_funcionario = Funcionario.codigo 
                AND FuncionarioContato.codigo_tipo_retorno=" . TipoRetorno::TIPO_RETORNO_EMAIL . "
                AND FuncionarioContato.codigo_tipo_contato=" . TipoContato::TIPO_CONTATO_COMERCIAL . "
              ORDER BY FuncionarioContato.codigo
            ) AS func_contato_codigo_email",
        );
    }

    public function retornaGrauRisco($codigo_grupo_economico)
    {

        $options['conditions'][] = array('GrupoEconomico.codigo' => $codigo_grupo_economico);
        $options['fields'] = array('Cnae.grau_risco', 'GrupoEconomico.codigo_cliente');
        $options['joins'] = array(
            array(
                'table' => 'cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => array('GrupoEconomico.codigo_cliente = Cliente.codigo')
            ),
            array(
                'table' => 'cnae',
                'alias' => 'Cnae',
                'type' => 'INNER',
                'conditions' => array('Cnae.cnae = Cliente.cnae')
            )
        );

        $resultado = $this->find('first', $options);
        return isset($resultado['Cnae']['grau_risco']) ? $resultado['Cnae']['grau_risco'] : null;
    }

    public function estrutura($codigo_cliente)
    {
        $codigo_matriz = $this->codigoMatrizPeloCodigoFilial($codigo_cliente);
        $this->bindModel(array(
            'hasMany' => array(
                'GrupoEconomicoCliente' => array('foreignKey' => 'codigo_grupo_economico', 'fields' => array('codigo', 'codigo_cliente'))
            ),
            'belongsTo' => array(
                'Matriz' => array('className' => 'Cliente', 'foreignKey' => 'codigo_cliente'),
            ),
        ));
        $this->GrupoEconomicoCliente->virtualFields = null;
        $this->GrupoEconomicoCliente->unbindModel(array('belongsTo' => array('GrupoEconomico', 'Cliente')));
        $this->GrupoEconomicoCliente->bindModel(array('belongsTo' => array(
            'Unidade' => array(
                'className' => 'Cliente',
                'foreignKey' => 'codigo_cliente',
                'fields' => array('codigo', 'razao_social'),
            )
        )));
        $fields = array(
            'Matriz.codigo',
            'Matriz.razao_social',
        );
        $conditions = array('codigo_cliente' => $codigo_matriz);
        $recursive = 2;
        $estrutura = $this->find('first', compact('conditions', 'recursive', 'fields'));

        $this->Matriz->bindModel(array(
            'hasAndBelongsToMany' => array(
                'Funcionario' => array(
                    'joinTable' => 'cliente_funcionario',
                    'foreignKey' => 'codigo_cliente',
                    'associationForeignKey' => 'codigo_funcionario',
                    'fields' => array('codigo', 'nome'),
                )
            ),
            'hasMany' => array(
                'Setor' => array('foreignKey' => 'codigo_cliente', 'fields' => array('codigo', 'descricao')),
                'Cargo' => array('foreignKey' => 'codigo_cliente', 'fields' => array('codigo', 'descricao')),
            ),
        ));
        $conditions = array('codigo' => $estrutura['Matriz']['codigo']);
        $matriz = $this->Matriz->find('first', compact('conditions'));
        unset($estrutura['GrupoEconomico']);
        foreach ($estrutura['GrupoEconomicoCliente'] as $key => $value) {
            $estrutura['Unidade'][$key] = $value['Unidade'];
        }
        unset($estrutura['GrupoEconomicoCliente']);
        $estrutura['Setor'] = $matriz['Setor'];
        $estrutura['Cargo'] = $matriz['Cargo'];
        foreach ($matriz['Funcionario'] as $key => $value) {
            unset($matriz['Funcionario'][$key]['ClienteFuncionario']);
        }
        $estrutura['Funcionario'] = $matriz['Funcionario'];
        return $estrutura;
    }


    //Retorna os clientes que receberão o arquivo modelo1
    function clientes_envio_modelo1()
    {

        //retirado para mandar para todos os clientes matriz
        // $codigo_produto_percapita = '117';
        // $codigo_servico_percapita = '4338';
        $this->ClienteContato = &ClassRegistry::init('ClienteContato');
        App::import('model', 'TipoContato');


        $joins = array(
            array(
                'table' => 'cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'Cliente.codigo = GrupoEconomico.codigo_cliente'
            ),
        );

        $conditions = array(
            'Cliente.ativo = 1',
            "(SELECT COUNT(*) from cliente_funcionario WHERE codigo_cliente_matricula = Cliente.codigo) > 0",
        );

        $recursive = -1;

        $order = array('Cliente.codigo');

        $fields = array(
            'Cliente.codigo',
            'Cliente.nome_fantasia',
            'GrupoEconomico.codigo'
        );
        //group
        $group = $fields;

        //recupera os clientes que são matrizes do grupo econômico, ativos e que possuem funcionarios matriculados
        $clientes = $this->find('all', compact('conditions', 'joins', 'order', 'fields', 'recursive', 'group'));

        if (!empty($clientes)) {

            foreach ($clientes as $key => $cliente) {
                //Retorna os e-mails de contato do tipo Envio validação de vidas
                $contatos = $this->ClienteContato->find('list', array('fields' => array('descricao'), 'conditions' => array(
                    'codigo_tipo_retorno' => 2,
                    'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_VALIDA_VIDAS,
                    'codigo_cliente' => $cliente['Cliente']['codigo']
                ), 'recursive' => -1));

                if (!empty($contatos)) {
                    $contatos_envio = array_unique($contatos);
                    $clientes[$key]['Cliente']['contato'] = implode(';', $contatos_envio);
                } //fim if
            } //fim foreach
        } //fim if !empty cliente

        return $clientes;
    }

    //Cria o arquivo modelo1 de funcionários
    function gerar_modelo1_funcionarios($codigo_cliente)
    {

        if (!empty($codigo_cliente)) {

            //para recuperar todos os funcionarios até demitidos passar o segundo parâmetro como false
            $query = $this->queryEstrutura($codigo_cliente, true);

            $dados = $this->query($query);

            $valido = true;

            $url =  APP . 'tmp' . DS . 'pdf' . DS . 'email_arquivo_cliente';
            //verifica se existe o diretorio caso nao exista cria
            if (!file_exists($url)) {
                mkdir($url);
            }
            $nome_arquivo = date('YmdHis') . '_' . $codigo_cliente . '.csv';
            $arquivo_funcionarios = $url . DS . $nome_arquivo;

            if ($valido) {
                $cabecalho = utf8_decode('"Código Unidade";"Nome da Unidade";"Nome do Setor";"Nome do Cargo";"Código Matrícula";"Matricula do Funcionario";"Nome do Funcionario";"Data de Nascimento(dd/mm/aaaa)";"Sexo(F:Feminino, M:Masculino)";"Situacao Cadastral(S:Ativo, F:Ferias, A:Afastado, I:Inativo)";"Data de Admissao(dd/mm/aaaa)";"Data de Demissao(dd/mm/aaaa)";"Data Início Cargo(dd/mm/aaaa)";"Estado Civil(1:Solteiro, 2:Casado, 3:Separado, 4:Divorciado, 5:Viuvo, 6:Outros)";"Pis/Pasep";"Rg";"Órgão Expedidor RG";"CPF";"CTPS";"Serie CTPS";"UF CTPS";"Endereco";"Numero";"Complemento";"Bairro";"Cidade";"Estado";"Cep";"Possui Deficiencia(S:Sim, N:Não)";"Codigo CBO";"Codigo GFIP";"Centro Custo";"Data da Último ASO(dd/mm/aaaa)";"Aptidao(A:Apto, I:Inapto)";"Turno";"Descricao Detalhada do Cargo";"Celular do Funcionario((ddd)+numero telefone)";"Autoriza envio de SMS ao funcionario";"E-mail do Funcionario";"Autoriza envio de e-mail ao funcionario";"Contato do responsavel da Unidade";"Telefone do responsavel da Unidade((ddd)+numero telefone)";"E-maildo responsavel da Unidade";"Endereco da Unidade";"Numero da Unidade";"Complemento da Unidade";"Bairro da Unidade";"Cidade da Unidade";"Estado da Unidade";"Cep da Unidade";"CNPJ da Unidade";"Inscricao Estadual";"Inscricao Municipal";"Cnae";"Grau de Risco";"Razao Social Unidade";"Unidade de Negocio";"Regime Tributario(1:Simples Nacional, 2:Simples Nacional, excesso sublimite de receita bruta, 3:Regime Normal)";"Codigo Externo";"Tipo Unidade(F: Fiscal, O: Operacional)";');

                $fp = fopen($arquivo_funcionarios, "w+");

                //Grava cabeçalho
                fwrite($fp, $cabecalho . "\n");
                $qtd_linhas = 0;

                //debug($dados);

                foreach ($dados as $key => $dado) {

                    //verifica se o campo esta nulo caso esteja pula para o proximo
                    if (is_null($dado[0]['codigo_alocacao'])) {
                        continue;
                    }

                    $linha = $dado[0]['codigo_alocacao'] . ';';
                    $linha .= '"' . utf8_decode($dado[0]['alocacao_nome_fantasia']) . '";';
                    $linha .= '"' . utf8_decode(trim($dado[0]['nome_setor'])) . '";';
                    $linha .= '"' . utf8_decode(trim($dado[0]['nome_cargo'])) . '";';
                    $linha .= '="' . $dado[0]['codigo_matricula'] . '";';
                    $linha .= '="' . $dado[0]['matricula'] . '";';
                    $linha .= '"' . utf8_decode(trim($dado[0]['nome_funcionario'])) . '";';
                    $linha .= '"' . $dado[0]['data_nascimento'] . '";';
                    $linha .= '"' . $dado[0]['sexo'] . '";';

                    switch ($dado[0]['status_matricula']) {
                        case 0:
                            $linha .= '"' . 'I' . '";';
                            break;
                        case 1:
                            $linha .= '"' . 'S' . '";';
                            break;
                        case 2:
                            $linha .= '"' . 'F' . '";';
                            break;
                        case 3:
                            $linha .= '"' . 'A' . '";';
                            break;
                        default:
                            $linha .= '"' . '";';
                            break;
                    }

                    $linha .= '"' . $dado[0]['data_admissao'] . '";';
                    $linha .= '"' . $dado[0]['data_demissao'] . '";';
                    $linha .= '"' . $dado[0]['data_inicio_cargo'] . '";';
                    $linha .= '"' . utf8_decode(trim($dado[0]['estado_civil'])) . '";';
                    $linha .= '"' . $dado[0]['pispasep'] . '";';
                    $linha .= '"' . $dado[0]['rg'] . '";';
                    $linha .= '"' . $dado[0]['rg_orgao'] . '";';
                    $linha .= '="' . $dado[0]['cpf'] . '";';
                    $linha .= '="' . $dado[0]['ctps'] . '";';
                    $linha .= '"' . $dado[0]['ctps_serie'] . '";';
                    $linha .= '"' . $dado[0]['ctps_uf'] . '";';
                    $linha .= '"' . $dado[0]['endereco'] . '";';
                    $linha .= '"' . $dado[0]['endereco_numero'] . '";';
                    $linha .= '"' . $dado[0]['endereco_complemento'] . '";';
                    $linha .= '"' . $dado[0]['bairro'] . '";';
                    $linha .= '"' . $dado[0]['cidade'] . '";';
                    $linha .= '"' . $dado[0]['uf'] . '";';
                    $linha .= '="' . $dado[0]['cep'] . '";';

                    switch ($dado[0]['funcionario_deficiencia']) {
                        case 0:
                            $linha .= '"' . 'N' . '";';
                            break;
                        case 1:
                            $linha .= '"' . 'S' . '";';
                            break;
                        default:
                            $linha .= '"' . '";';
                            break;
                    }

                    $linha .= '"' . $dado[0]['codigo_cbo'] . '";';
                    $linha .= '"' . $dado[0]['gfip'] . '";';
                    $linha .= '"' . utf8_decode(trim($dado[0]['centro_custo'])) . '";';
                    $linha .= '"' . $dado[0]['data_ultima_aso'] . '";';
                    $linha .= '"' . $dado[0]['aptidao'] . '";';
                    $linha .= '"' . $dado[0]['turno'] . '";';
                    $linha .= '"' . utf8_decode(trim(str_replace("\r\n", " ", $dado[0]['cargo_descricao_detalhada']))) . '";';
                    $linha .= '"' . $dado[0]['celular_funcionario'] . '";';
                    $linha .= '"' . $dado[0]['autoriza_envio_sms'] . '";';
                    $linha .= '"' . $dado[0]['email_funcionario'] . '";';
                    $linha .= '"' . $dado[0]['autoriza_envio_email'] . '";';
                    $linha .= '"' . $dado[0]['contato_alocacao'] . '";';
                    $linha .= '"' . $dado[0]['telefone_alocacao'] . '";';
                    $linha .= '"' . $dado[0]['email_alocacao'] . '";';
                    $linha .= '"' . $dado[0]['alocacao_endereco'] . '";';
                    $linha .= '"' . $dado[0]['alocacao_endereco_numero'] . '";';
                    $linha .= '"' . $dado[0]['alocacao_endereco_complemento'] . '";';
                    $linha .= '"' . $dado[0]['alocacao_bairro'] . '";';
                    $linha .= '"' . $dado[0]['alocacao_cidade'] . '";';
                    $linha .= '"' . $dado[0]['alocacao_uf'] . '";';
                    $linha .= '="' . $dado[0]['alocacao_cep'] . '";';
                    $linha .= '="' . $dado[0]['alocacao_cnpj'] . '";';
                    $linha .= '="' . $dado[0]['alocacao_inscricao_estadual'] . '";';
                    $linha .= '="' . $dado[0]['alocacao_ccm'] . '";';
                    $linha .= '"' . $dado[0]['alocacao_cnae'] . '";';
                    $linha .= '"' . $dado[0]['cnae_grau_risco'] . '";';
                    $linha .= '"' . utf8_decode(trim($dado[0]['alocacao_razao_social'])) . '";';
                    $linha .= '"' . '";';
                    $linha .= '"' . $dado[0]['alocacao_cod_regime_tribut'] . '";';
                    $linha .= '"' . $dado[0]['alocacao_codigo_externo'] . '";';
                    $linha .= '"' . $dado[0]['alocacao_tipo_unidade'] . '";';

                    fwrite($fp, $linha . "\n");
                    $qtd_linhas++;
                } //fim foreach

                fclose($fp);
                //remove arquivo
                //debug('Qtd envio de e-mail: ' . $codigo_cliente .' | ' . $qtd_linhas);

                self::preFaturamentoPerCapita($codigo_cliente, $qtd_linhas);

                if ($qtd_linhas == 0) {
                    $this->log('apaga arquivo cliente: ' . $codigo_cliente, 'debug');
                    unlink($arquivo_funcionarios);
                    unset($arquivo_funcionarios);
                    return false;
                }
                return array('url' => $url, 'nome_arquivo' => $nome_arquivo);
            } //fim if empty(dados)
        } //fim if empty codigo_cliente

        return false;
    }

    private function preFaturamentoPerCapita($codigo_cliente, $qtd_linhas)
    {

        //pega o mes passado
        $base_periodo = strtotime('-1 month', strtotime(Date('Y-m-01')));

        $mes = date('m', $base_periodo);
        $ano = date('Y', $base_periodo);
        $this->CtrPreFatPerCapita = ClassRegistry::init('CtrPreFatPerCapita');

        $conditions = array('mes_referencia' => $mes, 'ano_referencia' => $ano, 'codigo_cliente_matricula' => $codigo_cliente);

        $retorno_find = $this->CtrPreFatPerCapita->find('first', array('conditions' => $conditions));

        $dados = array();

        $dados['CtrPreFatPerCapita']['mes_referencia']         = $mes;
        $dados['CtrPreFatPerCapita']['ano_referencia']         = $ano;
        $dados['CtrPreFatPerCapita']['codigo_cliente_matricula'] = $codigo_cliente;
        $dados['CtrPreFatPerCapita']['qtd_total_email']        = $qtd_linhas;

        if (!isset($retorno_find['CtrPreFatPerCapita']['codigo'])) {
            $this->CtrPreFatPerCapita->incluir($dados);
            echo ("Inserindo Controle Pre Faturamento Per Capita \n");
        }
    } //FINAL FUNCTION preFaturamentoPerCapita

    //Envia o arquivo modelo 1 para os clientes
    function envia_arquivo_funcionarios()
    {

        set_time_limit(0);

        //recupera os clientes que são a matriz do grupo econômico
        //que estão ativos e possuem funcionarios matriculados
        $dados_envio = $this->clientes_envio_modelo1();
        //template do e-mail utilizado no envio do arquivo
        $template = 'envio_arquivo_funcionarios';
        $assunto = 'Atualização Cadastral';

        $c = 0;

        if (!empty($dados_envio)) {

            $this->Pedido = &ClassRegistry::init('Pedido');

            foreach ($dados_envio as $key => $dado) {

                try {
                    //Se não possui e-mail de contato
                    if (!isset($dado['Cliente']['contato'])) throw new Exception("Cliente não possui e-mail de contato", 1);

                    $dadosExame['codigo_cliente'] = $dado['Cliente']['codigo'];
                    $comlink = false;
                    if ((int) $this->Pedido->monta_arquivo_enviar_funcionarios($dadosExame, true)) {
                        $comlink = true;
                    }

                    $assunto = 'Cliente ' . $dado['Cliente']['codigo'] . ' sem anexo Planilha Modelo 1';
                    if ($this->disparaEmail($dado, $assunto, $template, $dado['Cliente']['contato'], $comlink)) {
                        echo ("enviando e-mail SEM anexo \n");
                    }
                } catch (Exception $ex) {
                    $this->log('Cliente:' . $dado['Cliente']['codigo'], 'debug');
                    $this->log($ex->getMessage(), 'debug');
                }
            } //fim foreach
        } //fim if empty clientes

        return true;
    } //FINAL FUNCTION envia_arquivo_funcionarios

    public function disparaEmail($dados = null, $assunto, $template, $to, $comlink = false)
    {

        if (Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO) {
            $to = 'tid@ithealth.com.br';
            $cc = null;
        } else {
            $cc = 'cadastro@rhhealth.com.br';
        }
        $cc = null;

        App::import('Component', array('StringView', 'Mailer.Scheduler'));

        $this->stringView = new StringViewComponent();
        $this->scheduler = new SchedulerComponent();
        $this->stringView->reset();

        $this->stringView->set('dados', $dados);

        $link = '';
        if ($comlink) $link = $this->link($dados['Cliente']['codigo']);
        $host = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? "portal.rhhealth.com.br" : (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO ? "tstportal.rhhealth.com.br" : "portal.localhost"));

        //grava na tabela de disparos_links
        //pega o link do acesso
        $link_acesso = "http://" . $host . "/portal/funcionarios/index_percapita/" . $dados['Cliente']['codigo'];

        //chama a model
        $this->DisparoLink = &ClassRegistry::init('DisparoLink');
        //seta os dados para gravar na tabela
        $dados_disparos = array(
            'DisparoLink' => array(
                'codigo_cliente' => $dados['Cliente']['codigo'],
                'email'          => $to,
                'link'           => $link_acesso,
                'status_validacao' => '0',
            )
        );
        //inclui os dados do disparo
        $this->DisparoLink->incluir($dados_disparos);

        $this->stringView->set('host', $host);
        $this->stringView->set('link', $link);

        $content = $this->stringView->renderMail($template);

        return $this->scheduler->schedule($content, array(
            'from' => 'portal@rhhealth.com.br',
            'to' => $to,
            'cc' => $cc,
            'subject' => $assunto
            //, 'attachments' => ''
        ));
    }

    public function verificaMatriz($codigo)
    {
        return $this->find('all', array('conditions' => array('codigo_cliente' . $this->rawsql_codigo_cliente($codigo))));
    } //FINAL FUNCTION verificaMatrix

    private function link($codigo_cliente = null)
    {
        //verifica se codigo_cliente está nulo para trazer todas as vigencias a vencer e vencidas.
        if (is_null($codigo_cliente)) {
            $codigo_cliente = 'all';
        } else {
            $codigo_cliente = "'{$codigo_cliente}'";
        }

        //monta o hash para colocar no link
        $hash = Comum::encriptarLink($codigo_cliente);
        //monta o host
        $host = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? "portal.rhhealth.com.br" : (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO ? "tstportal.rhhealth.com.br" : "portal.localhost"));

        //monta o link
        $link = "http://{$host}/portal/grupos_economicos/monta_arquivo_enviar_funcionarios?key=" . urlencode($hash);

        //retorno o link a ser acessado
        return $link;
    }

    /**
     * Retorna o código da Matriz de acordo com o código do Cliente fornecido
     * ex: portal/grupos_economicos/por_cliente/71758,10011
     * resposta
     * {"7639":10011,"13714":71758}
     *
     * @param [array] $codigo_cliente
     * @return array
     */
    public function obterCodigoMatrizPeloCodigoFilial($codigo_cliente = array())
    {
        if (is_null($codigo_cliente) || empty($codigo_cliente) || $codigo_cliente == 0) {
            return array();
        }

        $conditions = array(
            'GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente
        );

        $matriz = $this->find(
            'all',
            array(
                'conditions' => $conditions,
                'joins' => array(
                    array(
                        'table' => 'grupos_economicos_clientes',
                        'alias' => 'GrupoEconomicoCliente',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
                        )
                    )
                ),
                'fields' => array(
                    'GrupoEconomico.codigo as codigo',
                    'GrupoEconomico.codigo_cliente as codigo_cliente_matriz',
                    'GrupoEconomico.descricao as descricao'
                )
            )
        );

        $dados = array();

        if (is_array($matriz)) {
            foreach ($matriz as $key => $value) {
                $dados[] = $matriz[$key][0];
            }
        }

        return $dados;
    }

    public function matrizPeloCodigoFilial($codigo_cliente = null)
    {
        if (is_null($codigo_cliente) || empty($codigo_cliente)) {
            return null;
        }

        $conditions = array(
            'GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente
        );

        $matriz = $this->find(
            'first',
            array(
                'conditions' => $conditions,
                'joins' => array(
                    array(
                        'table' => 'grupos_economicos_clientes',
                        'alias' => 'GrupoEconomicoCliente',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
                        )
                    ),
                    array(
                        'table' => 'cliente',
                        'alias' => 'Cliente',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Cliente.codigo = GrupoEconomico.codigo_cliente'
                        )
                    )
                ),
                'fields' => array(
                    'Cliente.codigo',
                    'Cliente.nome_fantasia'
                )
            )
        );

        return empty($matriz) ? null : $matriz;
    }

    public function getCampoPorCliente($campo = "exibir_nome_fantasia_aso", $codigo_cliente)
    {
        $joins = array(
            array(
                'table' => 'grupos_economicos_clientes',
                'alias' => 'GrupoEconomicoCliente',
                'type' => 'INNER',
                'conditions' => array(
                    'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo',
                ),
            ),
        );
        $fields = array($campo);
        $conditions = array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente);
        $grupo_economico = $this->find('first', array('fields' => $fields, 'joins' => $joins, 'conditions' => $conditions));
        return $grupo_economico['GrupoEconomico'][$campo];
    }

    public function getCampoPorClienteRqe($campo = "exibir_rqe_aso", $codigo_cliente)
    {
        $joins = array(
            array(
                'table' => 'grupos_economicos_clientes',
                'alias' => 'GrupoEconomicoCliente',
                'type' => 'INNER',
                'conditions' => array(
                    'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo',
                ),
            ),
        );
        $fields = array($campo);
        $conditions = array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente);
        $grupo_economico = $this->find('first', array('fields' => $fields, 'joins' => $joins, 'conditions' => $conditions));
        return $grupo_economico['GrupoEconomico'][$campo];
    }

    public function converteFiltroEmConditionGE($data)
    {

        $conditions = array();

        if (!empty($data['codigo_cliente'])) {
            $conditions['GrupoEconomico.codigo_cliente'] = $data['codigo_cliente'];
        }

        if (!empty($data['codigo_unidade'])) {
            $conditions['GrupoEconomicoCliente.codigo_cliente'] = $data['codigo_unidade'];
        }

        return $conditions;
    }

    public function paginateCount($conditions = array(), $recursive = -1, $extra = array())
    {
        $return = $this->find(
            'all',
            array(
                'conditions' => $conditions,
                'joins' => !empty($extra['joins']) ? $extra['joins'] : array(),
                'group' => !empty($extra['group']) ? $extra['group'] : array(),
                'fields' => !empty($extra['group']) ? $extra['group'] : array(),
                'recursive' => $recursive,
            )
        );

        return count($return);
    } //fim paginateCounte
}
