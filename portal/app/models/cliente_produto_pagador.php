<?php
class ClienteProdutoPagador extends AppModel {
	var $name 		   = 'ClienteProdutoPagador';
	var $primaryKey    = 'codigo';	
	var $databaseTable = 'RHHealth';
	var $tableSchema   = 'dbo';
	var $useTable      = 'cliente_produto_pagador';
	var $actsAs        = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_cliente_produto_pagador'));	
	var $validate = array(
		'codigo_cliente_pagador' => array(
			'notEmpty' 		=> array(
				'rule' 		=> 'notEmpty',
				'message' 	=> 'Informe o cliente pagador',
				'required'  => true,
			 ),
			
		),
		'codigo_produto' => array(
			'notEmpty' 		=> array(
				'rule' 		=> 'notEmpty',
				'message' 	=> 'Informe o produto',
			 ),
			'validaCombinacao' => array(
				'rule' 		=> 'validaCombinacao',
				'message' 	=> 'Este Produto já foi cadastrado',
				'required' 	=> true,
				'on'		=> 'create'
				
			),
		),
	);

 	public $belongsTo = array(
		'EmbarcadorTransportador' 	=> array('className' => 'EmbarcadorTransportador', 'foreignKey' => 'codigo_embarcador_transportador' ),
		'ClientePagador'			=> array('className' => 'Cliente',                 'foreignKey' => 'codigo_cliente_pagador' 		),
		'Produto' 					=> array('className' => 'Produto', 				   'foreignKey' => 'codigo_produto' 				)
	);  

	function validaCombinacao(){
		$conditions = array('ClienteProdutoPagador.codigo_embarcador_transportador' => $this->data['ClienteProdutoPagador']['codigo_embarcador_transportador'],
							'ClienteProdutoPagador.codigo_produto' 					=> $this->data['ClienteProdutoPagador']['codigo_produto']);
		

		return ($this->find('count',compact('conditions')) ==0 );
	}


	function carregarClientePagador($codigo_cliente_pagador, $codigo_produto){
		$this->unbindModel(array('belongsTo' => array('EmbarcadorTransportador','ClientePagador','Produto')));

		$this->bindModel(array('belongsTo' => array(
			'ClienteProduto' => array(
				'foreignKey' => false,
				'conditions' => array(
					'ClienteProduto.codigo_cliente = ClienteProdutoPagador.codigo_cliente_pagador',
					'ClienteProduto.codigo_produto = ClienteProdutoPagador.codigo_produto',
				),
			),
			'Cliente' => array('foreignKey' => 'codigo_cliente_pagador'),
		)));
		$conditions = array(
			'ClienteProdutoPagador.codigo_cliente_pagador' 	=> $codigo_cliente_pagador,
			'ClienteProdutoPagador.codigo_produto' 			=> $codigo_produto,
			'ClienteProduto.codigo_motivo_bloqueio' 		=> MotivoBloqueio::MOTIVO_OK
		);

		$fields = array('Cliente.*');
		return $this->find('first', compact('fields', 'conditions'));
	}
}
