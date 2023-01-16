<?php
App::import('Component', 'PivotTable');
class RelatoriosDreController extends AppController {

    public $name = 'RelatoriosDre';
    public $components = array('Filtros', 'DbbuonnyMonitora');
    public $uses = array('RelatorioDre', 'DreTopico');
    public $helpers = array('Highcharts', 'Js');


    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(
            array(
                'listagem_dre2'
            )
        );
    }   

   
    function listagem(){
    	$this->pageTitle = 'DRE';
        $tipo = 'dre1';
        
        if (empty($this->data)) {
            $this->data['RelatorioDre']['ano'] = Date('Y');
        }
        
        $codigo_cliente = isset($this->data['RelatorioDre']['codigo_cliente']) ? $this->data['RelatorioDre']['codigo_cliente'] : null;
        $produto = isset($this->data['RelatorioDre']['produto']) ? $this->data['RelatorioDre']['produto'] : null;
        
        $anos = Comum::listAnos();
        $this->loadModel('Produto');
        $produtos = $this->Produto->listarProdutosNavegarq();
        
        $dados_receita = $this->RelatorioDre->consolidar($this->data['RelatorioDre']['ano'], $codigo_cliente, $produto);
        $this->PivotTable = new PivotTableComponent('Mes', array('Tipo'), 'Valor');
        $dados_receita = $this->PivotTable->transforma($dados_receita);
        
        $dados_despesa = $this->DreTopico->consolidarDespesas($this->data['RelatorioDre']['ano'], $tipo);
        $this->PivotTable = new PivotTableComponent('Mes', array('Topico'), 'Valor');
        $dados_despesa = $this->PivotTable->transforma($dados_despesa);
        
        $topicos = $this->DreTopico->topicosOrdenadosParaVisualizacao();
        
        $this->set(compact('dados_receita', 'dados_despesa', 'anos', 'produtos', 'topicos'));
    }

    function listagem_dre2() {
        $this->pageTitle = 'Fluxo de caixa';
        $tipo = 'dre2';
        
        if (empty($this->data)) {
            $this->data['RelatorioDre']['ano'] = Date('Y');
        }
        
        $codigo_cliente = isset($this->data['RelatorioDre']['codigo_cliente']) ? $this->data['RelatorioDre']['codigo_cliente'] : null;
        $produto = isset($this->data['RelatorioDre']['produto']) ? $this->data['RelatorioDre']['produto'] : null;
        
        $anos = Comum::listAnos();
        $this->loadModel('Produto');
        $produtos = $this->Produto->listarProdutosNavegarq();
        
        $dados_receita = $this->RelatorioDre->consolidar($this->data['RelatorioDre']['ano'], $codigo_cliente, $produto);
        $this->PivotTable = new PivotTableComponent('Mes', array('Tipo'), 'Valor');
        $dados_receita = $this->PivotTable->transforma($dados_receita);
        
        $dados_despesa = $this->DreTopico->consolidarDespesas($this->data['RelatorioDre']['ano'], $tipo);
        $this->PivotTable = new PivotTableComponent('Mes', array('Topico'), 'Valor');
        $dados_despesa = $this->PivotTable->transforma($dados_despesa);
        
        $topicos = $this->DreTopico->topicosOrdenadosParaVisualizacao();
        
        $this->set(compact('dados_receita', 'dados_despesa', 'anos', 'produtos', 'topicos'));

    }
}
