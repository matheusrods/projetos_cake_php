<?php

class TipoOperacao extends AppModel {
 
    var $name = 'TipoOperacao';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'tipo_operacao';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $field_name = 'descricao';    
    const TIPO_OPERACAO_CONSULTA = '1,2,3,4,6,8,9,10,74,100,108,109,110,111,112,113,114,115,116,120,121,122,123';    
    const TIPO_OPERACAO_CADASTRO = '11';
    const TIPO_OPERACAO_ATUALIZACAO = '21'; 
    const TIPO_OPERACAO_RENOVACAO_AUTOMATICA = '75';        
    const PESQUISA_INT_FUNCIONARIO = '54';
    const COM_CUSTO = '0';
    const SEM_CUSTO = '1';
    const CONSULTA_RECOMENDADO = 1;
    const CONSULTA_NAO_RECOMENDADO = 2;
    const CONSULTA_NAO_RECOMENDADO_INSUF_DADOS = 3;
    const CONSULTA_NAO_RECOMENDADO__INSUFICIENCIA_DE_DADOS = 4;
    const CONSULTA_NAO_RECOMENDADO_NAO_CADASTRADO = 5;
    const CONSULTA_NAO_RECOMENDADO_NAO_PESQUISADO = 6;
    const CONSULTA_RECOMENDADO_NO_PERIODO_DE_6H = 7;
    const CONSULTA_NAO_RECOMENDADO_NO_PERIODO_6H = 8;
    const CONSULTA_NAO_RECOMENDADO_VEICULO_NAO_CADASTRADO = 9;
    const CONSULTA_NAO_RECOMENDADO_VEICULO_COM_OCORRENCIA = 10;
    const CADASTRO_FUNCIONARIO__AGREGADO = 12;
    const CADASTRO_CLIENTE = 13;
    const CADASTRO_CORRETORA = 14;
    const CADASTRO_SEGURADORA = 15;
    const ATUALIZA_FUNCIONARIO__AGREGADO = 22;
    const ALTERA_MOTORISTA = 31;
    const ALTERA_FUNCIONARIO = 32;
    const ALTERA_CLIENTE = 33;
    const ALTERA_CORRETORA = 34;
    const ALTERA_SEGURADORA = 35;
    const DELETA_MOTORISTA = 41;
    const DELETA_FUNCIONARIO = 42;
    const DELETA_CLIENTE = 43;
    const DELETA_CORRETORA = 44;
    const DELETA_SEGURADORA = 45;
    const PESQUISA_MOTORISTA = 51;
    const PESQUISA_FUNCIONARIO = 52;
    const PESQUISA_INTERROMPIDA = 53;
    const ATUALIZA_BAIXA_CRIMINAL = 55;
    const APROVA_MOTORISTA = 61;
    const APROVA_FUNCIONARIO = 62;
    const REPROVA_MOTORISTA = 63;
    const REPROVA_FUNCIONARIO = 64;
    const INSUFICIENCIA_DADOS_MOTORISTA = 65;
    const INSUFICIENCIA_DE_DADOS_FUNC = 66;
    const ATUALIZACAO_SEM_COBRANCA = 67;
    const ALTERA_GRUPO = 68;
    const CADASTRA_GRUPO = 69;
    const DELETA_GRUPO = 70;
    const CADASTRA_MERCADORIA = 71;
    const DELETA_MERCADORIA = 72;
    const ALTERA_MERCADORIA = 73;
    const CONSULTA_NAO_RECOMENDADO_CNH_VENCIDAIRREGULAR = 74;
    const CONSULTA_RECOMENDADO__AGREGADO = 76;
    const CONSULTA_RECOMENDADO__FUNCIONARIO = 77;
    const CONSULTA_NAO_RECOMENDADO__AGREGADO = 78;
    const CONSULTA_NAO_RECOMENDADO__FUNCIONARIO = 79;
    const CONSULTA_NAO_RECOMENDADO_INSUF_DADOS__AGREGADO = 80;
    const CONSULTA_NAO_RECOMENDADO_INSUF_DADOS__FUNCIONARIO = 81;
    const CONSULTA_NAO_RECOMENDADO_VEICULO_NAO_CADASTRADO__AGREGADO = 82;
    const CONSULTA_NAO_RECOMENDADO_VEICULO_NAO_CADASTRADO__FUNCIONARIO = 83;
    const CONSULTA_NAO_RECOMENDADO_VEICULO_COM_OCORRENCIA__AGREGADO = 84;
    const CONSULTA_NAO_RECOMENDADO_VEICULO_COM_OCORRENCIA__FUNCIONARIO = 85;
    const CONSULTA_NAO_RECOMENDADO_CNH_VENCIDAIRREGULAR__AGREGADO = 86;
    const CONSULTA_NAO_RECOMENDADO_CNH_VENCIDAIRREGULAR__FUNCIONARIO = 87;
    const CONSULTA_SCUSTO__AGREGADOFUNCIONARIO = 88;
    const CONSULTA_CARRETEIRO = 89;
    const CONSULTA_BCB_NOVA_CONSULTA = 90;
    const CONSULTA_BCB_PRE_CADASTRADA = 91;
    const EXCLUS??O_DE_PROFISSIONAL_X_ARTIGO_CRIMINAL = 92;
    const EXCLUSAO_DE_PESQUISA_PLUS = 93;
    const EXCLUSAO_DE_BAIXA_STANDARD = 94;
    const EXCLUSAO_DE_BAIXA_PLUS = 95;
    const EXCLUSAO_DE_PROFISSIONAL_X_ARTIGO_CRIMINAL = 96;
    const ALTERAR_CATEGORIA_X_VALIDADE = 97;
    const PESQUISA_PENDENTE = 98;
    const NOVA_PESQUISA = 99;
    const MUDANCA_STATUS_BAIXA_CRIMINAL = 107;
    const INCLUSAO_ARTIGO_CRIMINAL = 92;
    const EXCLUSAO_ARTIGO_CRIMINAL = 96;
    const CONSULTA_NAO_RECOMENDADO_CARRETA_NAO_CADASTRADA = 120;
    const CONSULTA_NAO_RECOMENDADO_CARRETA_COM_OCORRENCIA = 121;
    const CONSULTA_NAO_RECOMENDADO_CARRETA_NAO_CADASTRADA_WEB = 123;
    const CONSULTA_NAO_RECOMENDADO_CARRETA_COM_OCORRENCIA_WEB = 124;
    const GERACAO_CT = 120;
    const EXCLUSAO_CT = 121;
    const EXCLUSAO_OCORRENCIA_VEICULO = 122;
    const INCLUSAO_OCORRENCIA_VEICULO = 123;
    const EDICAO_OCORRENCIA_VEICULO = 133;    
    const ANALISE_PREVIA = 129;


    function listaTiposOperacaoCobrados() {
        return $this->find('list',array('conditions' => array('cobrado' => 1)));
    }

    function listaTodosTiposOperacao() {
        return $this->find('list', array('fields'=>'descricao', 'order' => array('TipoOperacao.descricao asc')));
    } 

    public function listCustoSemCusto() {
        return array(TipoOperacao::SEM_CUSTO=>'SEM CUSTO', TipoOperacao::COM_CUSTO=>'COM CUSTO');
    }


}