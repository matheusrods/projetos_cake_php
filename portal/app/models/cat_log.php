<?php
class CatLog extends AppModel {

	public $name = 'CatLog';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'cat_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_cat';
	public $actsAs = array('Secure');

    public function log_cat($codigo_cat){
        $this->Funcionario = ClassRegistry::init('Funcionario');
        $this->Cliente = ClassRegistry::init('Cliente');
        
        $dados_query = array(
            'fields' => array(
                'CatLog.codigo_cat',
                'CatLog.remuneracao_mensal',
                'case 
                    when CatLog.evento_retificacao = \'1\' then \'Original\'
                    when CatLog.evento_retificacao = \'2\' then \'Retificação\'
                end as id_evento',
                'CatLog.recibo_retificacao as numero_recibo',
                'CatLog.motivo_retificacao as motivo_retificacao',
                'case 
                    when CatLog.acao_sistema = 0 then \'Inclusão\'
                    when CatLog.acao_sistema = 1 then \'Atualizaçao\'
                    when CatLog.acao_sistema = 2 then \'Exclusão\'
                end as acao_sistema',
                'Cliente.razao_social',
                'Funcionario.nome',
                'Funcionario.cpf',
                'case 
                    when CatLog.codigo_emitente = 1 then \'Empregador\'
                    when CatLog.codigo_emitente = 2 then \'Cooperativa\'
                    when CatLog.codigo_emitente = 3 then \'Sindicato de trabalhadores avulsos não portuários\'
                    when CatLog.codigo_emitente = 4 then \'Órgão Gestor de Mão de Obra\'
                    when CatLog.codigo_emitente = 5 then \'Empregado\'
                    when CatLog.codigo_emitente = 6 then \'Dependente do Empregado\'
                    when CatLog.codigo_emitente = 7 then \'Entidade Sindical competente\'
                    when CatLog.codigo_emitente = 8 then \'Médico assistente\'
                    when CatLog.codigo_emitente = 9 then \'Autoridade Pública\' 
                end as emitente',
                'case 
                    when CatLog.tipo_cat_codigo = 1 then \'Inicial\'
                    when CatLog.tipo_cat_codigo = 2 then \'Reabertura\'
                    when CatLog.tipo_cat_codigo = 3 then \'Comunicação de óbito\'
                end as tipo_cat',
                'case 
                    when CatLog.motivo_emissao = 1 then \'Empregador\'
                    when CatLog.motivo_emissao = 2 then \'Ordem Judicial\'
                    when CatLog.motivo_emissao = 3 then \'Determinação de orgão fiscalizador\'
                end as motivo_emissao',
                'case 
                    when CatLog.fil_prev_social_codigo = 1 then \'Empregado\'
                    when CatLog.fil_prev_social_codigo = 2 then \'Tra. Avulso\'
                    when CatLog.fil_prev_social_codigo = 3 then \'Seg. especial\'
                    when CatLog.fil_prev_social_codigo = 3 then \'Médico Resistente\'
                end as filiacao_previdencia',
                'case when CatLog.aposentado = 1 then \'Sim\' else \'Não\' end as aposentado',
                'case when CatLog.area_codigo = 1 then \'Urbana\' else \'Rural\' end as areas',
                'CONVERT(VARCHAR, CatLog.data_acidente,23) as data_acidente',
                'convert(varchar(5), cast(CatLog.hora_acidente as time), 108) as hr_acidente',
                'convert(varchar(5), cast(CatLog.apos_qts_hs_trabalho as time), 108) as apos_horas_trab',
                '(select top 1 descricao from esocial where codigo = CatLog.codigo_esocial_24) as tipo',
                'case when CatLog.houve_afastamento = 1 then \'Sim\' else \'Não\' end as h_afastamento',
                'CatLog.ultimo_dia_trabalhado as ultimo_dia',
                'case 
                    when CatLog.local_acidente = 1 then \'Estabelecimento do empregador no Brasil\'
                    when CatLog.local_acidente = 2 then \'Estabelecimento do empregador no Exterior\'
                    when CatLog.local_acidente = 3 then \'Estabelecimento de terceiros onde o empregador presta serviços\'
                    when CatLog.local_acidente = 4 then \'Via pública\'
                    when CatLog.local_acidente = 5 then \'Área rural\'
                    when CatLog.local_acidente = 6 then \'Embarcação\'
                    when CatLog.local_acidente = 9 then \'Outros\' 
                end as loca_acidente',
                'CatLog.especificacao_local_acidente as espec_lc_acidente',
                'CatLog.codigo_documento as cnpj',
                '(select top 1 abreviacao from endereco_estado where codigo = CatLog.uf_documento) as uf',
                '(select top 1 descricao from esocial where codigo = CatLog.codigo_esocial_13) as parte_corpo',
                'case 
                    when CatLog.lateralidade_corpo = 0 then \'Não aplicável\'
                    when CatLog.lateralidade_corpo = 1 then \'Esquerda\'
                    when CatLog.lateralidade_corpo = 2 then \'Direita\'
                    when CatLog.lateralidade_corpo = 3 then \'Ambas\'
                end as parte_atingida',
                '(select top 1 descricao from esocial where codigo = CatLog.codigo_esocial_14_15) as agente_causador',
                '(select top 1 descricao from esocial where codigo = CatLog.codigo_esocial_16) as descricao_acidente_gerador',
                'case when CatLog.resistro_policial = 1 then \'Sim\' else \'Não\' end as registro_policial',
                'case when CatLog.morte = 1 then \'Sim\' else \'Não\' end as morte',
                'CONVERT(VARCHAR, CatLog.data_obito,23) as data_obito',
                'CatLog.observacao_cat as observacao_cat',
                'CatLog.cep_acidentado as cep_acidentado',
                '(select top 1 descricao from esocial where codigo = CatLog.codigo_pais) as codigo_pais',
                'case 
                    when CatLog.tipo_inscricao  = \'1\' then \'CNPJ\'
                    when CatLog.tipo_inscricao = \'3\' then \'CAEPF\'
                    when CatLog.tipo_inscricao = \'4\' then \'CNO\'
                end as tipo_inscricao',
                'CatLog.codigo_caepf as caepf',
                'CatLog.codigo_cno as cno',
                'CatLog.acidentado_endereco as endereco_acidente',
                'CatLog.acidentado_numero as numero_acidente',
                'CatLog.acidentado_complemento as complemento_acidente',
                'CatLog.acidentado_bairro as bairro_acidente',
                'CatLog.acidentado_cidade as cidade_acidente',
                'CatLog.acidente_estado as estado_acidente',
                'CatLog.t1_nome as nome_test1',
                'CatLog.t1_endereco as endereco_test1',
                'CatLog.t1_numero as numero_test1',
                'CatLog.t1_complemento as complemento_test1',
                'CatLog.t1_bairro as bairro_test1',
                'CatLog.t1_cep as cep_test1',
                'CatLog.t1_cidade as cidade_test1',
                'CatLog.t1_estado as estado_test1',
                'CatLog.t1_telefone as telefone_test1',
                'CatLog.t2_nome as nome_test2',
                'CatLog.t2_endereco as endereco_test2',
                'CatLog.t2_numero as numero_test2',
                'CatLog.t2_complemento as complemento_test2',
                'CatLog.t2_bairro as bairro_test2',
                'CatLog.t2_cep as cep_test2',
                'CatLog.t2_cidade as cidade_test2',
                'CatLog.t2_estado as estado_test2',
                'CatLog.t2_telefone as telefone_test2',
                'CatLog.local as local',
                'CatLog.data as data',
                'CatLog.cnes as cnes',
                'CONVERT(VARCHAR, CatLog.data_atendimento,23) as data_atendimento_atestado',
                'convert(varchar(5), cast(CatLog.hora_atendimento as time), 108) as hora_atendimento',
                'case 
                    when CatLog.indicativo_internacao = \'S\' then \'Sim\' 
                    when CatLog.indicativo_internacao = \'N\' then \'Não\' 
                end as indic_internacao',
                'CatLog.duracao_estimada_tratamento as duracao_tratamento',
                '(select top 1 descricao from esocial where codigo = CatLog.natureza_lesao) as natureza_lesao',
                'CatLog.descricao_complementar_lesao as descricao_complementar_lesao',
                'CatLog.diagnostico_provavel as diagnostico_provavel',
                '(select top 1 descricao from cid where codigo = CatLog.cid10) as cid10',
                'CatLog.observacao as obs_atestado',
                'Medico.codigo as codigo_medico',
                'ConselhoProfissional.descricao as crm',
                'Medico.conselho_uf as uf_medico',
                'Medico.nome as nome_medico',
                'Medico.cpf as cpf_medico',
                'CONVERT(VARCHAR, CatLog.data_cat_origem,23) as data_cat_origem',
                'CatLog.numero_cat_origem as numero_cat_origem',
            ),
            'conditions' => array('CatLog.codigo_cat' => $codigo_cat),
            'joins' => array(
                array(
                    'table' => 'RHHealth.dbo.cat',
                    'alias' => 'Cat',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'CatLog.codigo_cat = Cat.codigo'
                    )  
                ),
                array(
                    'table' => 'RHHealth.dbo.funcionarios',
                    'alias' => 'Funcionario',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'CatLog.codigo_funcionario = Funcionario.codigo'
                    )  
                ),
                array(
                    'table' => 'RHHealth.dbo.cliente',
                    'alias' => 'Cliente',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'CatLog.codigo_cliente = Cliente.codigo'
                    )  
                ),
                array(
                    'table' => 'RHHealth.dbo.medicos',
                    'alias' => 'Medico',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'CatLog.codigo_medico = Medico.codigo'
                    )  
                ),
                array(
                    'table' => 'RHHealth.dbo.conselho_profissional',
                    'alias' => 'ConselhoProfissional',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Medico.codigo_conselho_profissional = ConselhoProfissional.codigo'
                    )  
                ),
            ),
        );

        //dados do log
        $dados = $this->find('all', $dados_query);

        return $dados;
    }

}