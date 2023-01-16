<?php
class RelatoriosBsatController extends AppController {

    public $name = 'RelatoriosBsat';
    public $components = array('Filtros');
    public $uses = array('Cliente', 'Recebsm', 'ClienteProdutoServico2');

    function relatorio_bsat_placas($export = false) {
    	if($export) {
    		$this->layout = 'ajax';
    	} else {
            $this->layout = 'new_window';
        }
    	$this->pageTitle = 'Demonstrativo de Placas Avulsas';
    	if(!empty($this->data)) {
	    	$cliente 	  = $this->Cliente->carregar($this->data['RelatorioBsat']['codigo_cliente']);
            $dados        = $this->Recebsm->placasAvulsasPorCliente($this->data['RelatorioBsat'], false, true);
	    	$this->set(compact('cliente','dados'));
	    }
    }

    function relatorio_bsat_frota($export = false) {
    	if($export) {
    		$this->layout = 'ajax';
    	} else {
            $this->layout = 'new_window';
        }
    	$this->pageTitle = 'Demonstrativo de Frota';
    	if(!empty($this->data)) {
	    	$cliente 	  = $this->Cliente->carregar($this->data['RelatorioBsat']['codigo_cliente']);
			$dados 		  = $this->ClienteProdutoServico2->frotaPorPagador($this->data['RelatorioBsat'], false, true);
	    	$this->set(compact('cliente','dados'));
	    }
    }

    function relatorio_bsat_frota_historico($export = false) {
        if($export) {
            $this->layout = 'ajax';
        } else {
            $this->layout = 'new_window';
        }
        $this->pageTitle = 'Demonstrativo de Frota';
        if(!empty($this->data)) {
            $this->loadModel('Pedido');
            $this->loadModel('FrotaPedido');
            $pedido       = $this->Pedido->carregar($this->data['RelatorioBsat']['codigo_pedido']);
            $cliente      = $this->Cliente->carregar($pedido['Pedido']['codigo_cliente_pagador']);
            $dados        = $this->FrotaPedido->listarPorPedido($pedido['Pedido']['codigo']);
            $this->set(compact('cliente','dados'));
        }
    }

    function relatorio_bsat_placas_historico($export = false) {
        if($export) {
            $this->layout = 'ajax';
        } else {
            $this->layout = 'new_window';
        }
        $this->pageTitle = 'Demonstrativo de Placas Avulsas';
        if(!empty($this->data)) {
            $this->loadModel('Pedido');
            $this->loadModel('AvulsoPedido');
            $pedido       = $this->Pedido->carregar($this->data['RelatorioBsat']['codigo_pedido']);
            $cliente      = $this->Cliente->carregar($pedido['Pedido']['codigo_cliente_pagador']);
            $dados        = $this->AvulsoPedido->listarPorPedido($pedido['Pedido']['codigo']);
            $this->set(compact('cliente','dados'));
        }
    }

	function relatorio_bsat_sm($export = false) {
    	if($export) {
            $this->layout = 'ajax';
        } else {
            $this->layout = 'new_window';
        }
        $cliente        = $this->Cliente->carregar($this->data['RelatorioBsat']['cliente_pagador']);
        $this->pageTitle = 'SMs por Pagador';
        $this->data['RelatorioBsat']['status'] = array(Recebsm::STATUS_ENCERRADA);
        $this->data['RelatorioBsat']['frota'] = Recebsm::FORA_DA_FROTA;
        $this->data['RelatorioBsat']['faturamento'] = true;
        $conditions = $this->Recebsm->converteFiltrosEmConditions($this->data['RelatorioBsat']);
        $dados = $this->Recebsm->listar($conditions);
        $this->set(compact('cliente', 'dados'));
    }
}