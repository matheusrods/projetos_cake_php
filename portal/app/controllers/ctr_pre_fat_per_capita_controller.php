<?php

class CtrPreFatPerCapitaController extends AppController {
	public $name = 'CtrPreFatPerCapita';
	public $uses = array( 
		'Cliente', 
		'CtrPreFatPerCapita',
	);

	public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('listagem_pagadores', 'pagadores'));
    }   

	public function index() {
		$this->pageTitle = 'Controle Pre Faturamento Per Capita';
		$this->Filtros->limpa_sessao($this->Cliente->name);
		
		$this->data['CtrPreFatPerCapita'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
		
		$meses = Comum::listMeses();
		$this->set(compact('meses'));
	}//FINAL FUNCTION index

	public function listagem() {
		$this->layout = 'ajax';

		$filtros 	= $this->Filtros->controla_sessao($this->data, $this->CtrPreFatPerCapita->name);
		$conditions = $this->CtrPreFatPerCapita->converteFiltrosEmConditions($filtros);
		
		$this->CtrPreFatPerCapita->virtualFields = array(
			'mes_ano' => "CONCAT(CtrPreFatPerCapita.mes_referencia, '/',CtrPreFatPerCapita.ano_referencia)",
		);

		$fields = array(
			'CtrPreFatPerCapita.codigo_cliente_matricula',
			'mes_ano',
			'CtrPreFatPerCapita.qtd_total_email',
			'CtrPreFatPerCapita.qtd_processado',
			'CtrPreFatPerCapita.qtd_a_faturar',
			'CtrPreFatPerCapita.data_inclusao',
			'CtrPreFatPerCapita.data_alteracao',
			'CtrPreFatPerCapita.codigo_importacao_estrutura',
			'Cliente.razao_social'
		);
		
		$joins = array(
			array(
				'table' => $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'CtrPreFatPerCapita.codigo_cliente_matricula = Cliente.codigo',
				),
			);
		
		$order = array(
			'Cliente.razao_social ASC'
		);

		$this->paginate['CtrPreFatPerCapita'] = array(
			'fields' => $fields,
			'joins' => $joins,
			'conditions' => $conditions,
			'order' => $order,
			'limit' => 50,
		);

		$clientes = $this->paginate('CtrPreFatPerCapita');
		
		//debug($clientes);
		$meses = Comum::listMeses();
		$this->set(compact('clientes', 'meses'));
	}//FINAL FUNCTION listagem

	public function listagem_pagadores($codigo){
		
		$this->pageTitle = 'Listagem Pagadores';
		
		$base_periodo = strtotime('-1 month', strtotime(date('Y-m-01')));
		
		$dados = array();
        $dados['mes']               = date('m', $base_periodo);
        $dados['ano']               = date('Y', $base_periodo);

        //seta a data de inicio
        $dados['data_inicial']  	= Date('Ym01', $base_periodo);
        $dados['data_final']    	= Date('Ymt', $base_periodo);

		$this->GrupoEconomicoCliente 	= ClassRegistry::init('GrupoEconomicoCliente');
		$dados['codigo_cliente']    	= $this->GrupoEconomicoCliente->getCodigoClientesByCodigoMatriz($codigo);

		$this->Pedido = ClassRegistry::init('Pedido');

        $clientes_pagadores = Set::extract($this->Pedido->calculaPercapitaByClientePagador($dados), '{n}.0');
       	
       	$this->Cliente = ClassRegistry::init('Cliente');

        $dados_matriz = $this->Cliente->find('first', array('fields'=>'codigo, razao_social', 'conditions' => array('codigo' => $codigo)));
        
		$this->set(compact('clientes_pagadores', 'dados', 'dados_matriz'));
	}//FINAL FUNCTION listagem_pagadores
	
}//FINAL CLASS CtrPreFatPerCapitaController