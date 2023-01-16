<?php
class MatrizProdutoPagador extends AppModel {

	var $name 		   = 'MatrizProdutoPagador';
	var $primaryKey    = 'codigo';	
	var $databaseTable = 'RHHealth';
	var $tableSchema   = 'dbo';
	var $useTable      = 'matrizes_produtos_pagadores';
	var $actsAs        = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_matrizes_produtos_pagadores'));	

	var $validate = array(
		'codigo_cliente_pagador' => array(
			'notEmpty' 		=> array(
				'rule' 		=> 'notEmpty',
				'message' 	=> 'Informe o cliente pagador',
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
			),
		),
	);

 	public $belongsTo = array(
		'MatrizFilial' 		=> array('className' => 'MatrizFilial', 'foreignKey' => 'codigo_matriz_filial' ),
		'ClientePagador'	=> array('className' => 'Cliente',                 'foreignKey' => 'codigo_cliente_pagador' 		),
		'Produto' 			=> array('className' => 'Produto', 				   'foreignKey' => 'codigo_produto' 				)
	);  

	function validaCombinacao(){
		$conditions = array('MatrizProdutoPagador.codigo_matriz_filial' => $this->data['MatrizProdutoPagador']['codigo_matriz_filial'],
							'MatrizProdutoPagador.codigo_produto' 		=> $this->data['MatrizProdutoPagador']['codigo_produto']);
		if (isset($this->data[$this->name]['codigo'])) $conditions[] = array($this->name.'.codigo !=' => $this->data[$this->name]['codigo']);
		return ($this->find('count',compact('conditions')) ==0 );
	}
}
