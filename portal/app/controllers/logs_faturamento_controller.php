<?php
class LogsFaturamentoController extends AppController {
  public $name = 'LogsFaturamento';
  public $components = array('Filtros');
  public $helpers = array('Html', 'Ajax','Buonny');
  var $uses = array('LogFaturamentoTeleconsult','Seguradora', 'Corretora', 'Gestor', 'EnderecoRegiao', 'Produto', 'Servico');

  function carrega_combos() {
    $seguradoras = $this->Seguradora->find('list', array('order' => 'nome'));
    $corretoras = $this->Corretora->find('list', array('order' => 'nome'));
    $gestores = $this->Gestor->listarNomesGestoresAtivos();
    $filiais = $this->EnderecoRegiao->listarRegioes();
    $produtos = $this->Produto->find('list', array('fields' => array('descricao')));
    $servicos = $this->Servico->find('list', array('fields' => array('descricao')));
    $this->set(compact('seguradoras', 'corretoras', 'gestores', 'filiais', 'produtos', 'servicos'));
  }

  function utilizacao_servicos() {
    $this->pageTitle = 'Utilização de Serviço';
    $this->carrega_combos();
    if(!empty($this->data['LogFaturamentoTeleconsult'])) {
      $this->data['LogFaturamentoTeleconsult'] = $this->Filtros->controla_sessao($this->data, 'LogFaturamentoTeleconsult');
    } else {
      $this->data['LogFaturamentoTeleconsult']['data_inclusao_inicio'] = date('d/m/Y');
      $this->data['LogFaturamentoTeleconsult']['data_inclusao_fim']   = date('d/m/Y');
    }
  }

  function listagem_utilizacao_servicos( $exportar_excel = FALSE ){    
    $this->layout = 'ajax';
    $filtros = $this->Filtros->controla_sessao($this->data, 'LogFaturamentoTeleconsult');
    if(!empty($filtros['data_inclusao_inicio']) && !empty($filtros['data_inclusao_fim'])){
      $data_inclusao_inicio = strtotime(AppModel::dateToDbDate($filtros['data_inclusao_inicio']));
      $data_inclusao_fim    = strtotime(AppModel::dateToDbDate($filtros['data_inclusao_fim']));
      if (floor(($data_inclusao_fim - $data_inclusao_inicio)/(60*60*24)) > 31){
        die();
      }
    }
    if( isset($filtros['codigo_documento']) )
      unset($filtros['codigo_documento']);

    $conditions = $this->LogFaturamentoTeleconsult->converteFiltroEmCondition( $filtros );    
    if( $exportar_excel == FALSE ){
      $this->paginate['LogFaturamentoTeleconsult']  = array(
        'conditions'    => $conditions,
        'limit'         => 50,
        'method'        => 'listagem_utilizacao_servicos'
      );      
      $ultilizacao_servicos = $this->paginate('LogFaturamentoTeleconsult');
    } else {
      $this->exportarListagemUtilizacaoServicos( $filtros );
    }
    $this->set(compact('ultilizacao_servicos'));
  }

  private function exportarListagemUtilizacaoServicos( $filtros ) {
    $conditions = $this->LogFaturamentoTeleconsult->converteFiltroEmCondition( $filtros );
    $query = $this->LogFaturamentoTeleconsult->listarUltilizacaoServicos('sql', compact('conditions'));
    $dbo   = $this->LogFaturamentoTeleconsult->getDataSource();
    $dbo->results = $dbo->_execute( $query );
    header('Content-type: application/vnd.ms-excel');
    header(sprintf('Content-Disposition: attachment; filename="%s"', basename('utilizacao_servicos.csv')));
    header('Pragma: no-cache');
    echo iconv('UTF-8', 'ISO-8859-1','"Código Pagador";"Pagador";"Codigo Ultilizador";"Ultilizador";"Produto";"Serviço";"Cobrado";SM "Online";"Quantidade";"Valor";"Data Utilização";' )."\n";
    set_time_limit(0);
    ini_set('max_execution_time', 0);
    ini_set('max_input_time', 0);    
    $registros = $dbo->fetchAll($query);
    // while ( $servicos = $dbo->fetchRow() ) {
    foreach ($registros as $servicos ) {        
      $linha  = $servicos[0]['codigo_cliente_pagador'].";";
      $linha .= $servicos[0]['razao_social_pagador'].";";
      $linha .= $servicos[0]['codigo_cliente_utilizador'].";";
      $linha .= $servicos[0]['razao_social_utilizador'].";";
      $linha .= $servicos[0]['produto_descricao'].";";
      $linha .= (iconv('ISO-8859-1', 'UTF-8', $servicos[0]['servico_descricao'])) .";";
      $linha .= (!empty($servicos[0]['cobrado']) ? 'Sim' : 'Nao').";";
      $linha .= (!empty($servicos[0]['online']) ? 'Sim' : 'Nao').";";
      $linha .= ($servicos[0]['total'] == 0  ? null : $servicos[0]['total']).";";
      $linha .= ($servicos[0]['precoSomado'] == 0  ? null : $servicos[0]['precoSomado']).";";
      $linha .= AppModel::dbDateToDate( substr($servicos['LogFaturamentoTeleconsult']['data_inclusao'], 0, 10 ) );
      $linha .= "\n";
      echo $linha;
    }
    die();
  }

  function historico_profissional($codigo_documento){
    $this->data['Profissional']['codigo_documento'] = $codigo_documento;
    $this->loadModel('LogFaturamentoTeleconsult');
    $filtros['data_inicial'] = '01/01/2000'; //data inicial travada da consulta 
    $filtros['data_final'] = date('d/m/Y');
    $filtros['codigo_status'] = array(2,3,4);
    $filtros['cpf']  = $this->data['Profissional']['codigo_documento'];
    $filtros['codigo_cliente'] = ''; 
    $filtros['codigo_embarcador'] = '';
    $filtros['codigo_transportador'] =''; 
    $conditions = $this->LogFaturamentoTeleconsult->logFaturamentoScorecard( $filtros, true );
    if( !empty($this->params['named']) )
      $conditions['page'] = !empty($this->params['named']) ? $this->params['named'] : NULL;        
    $this->paginate['LogFaturamentoTeleconsult'] = $conditions;
    $log_faturamento = $this->paginate('LogFaturamentoTeleconsult');  
    $this->set(compact('log_faturamento')); 
  }
}