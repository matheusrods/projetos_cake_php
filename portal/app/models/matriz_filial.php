<?php
class MatrizFilial extends AppModel {
	var $name = 'MatrizFilial';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'matrizes_filiais';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_matrizes_filiais'));
	
	var $validate = array(
		'codigo_cliente_matriz' => array(
			'notEmpty' 		=> array(
				'rule' 		=> 'notEmpty',
				'message' 	=> 'Informe a Matriz',
			 ),
			'validaCombinacaoUnica' => array(
				'rule' 		=> 'validaCombinacaoUnica',
				'message' 	=> 'Esta combinação matriz/filial já foi cadastrada',
				'required' 	=> true,
			),
		),
		'codigo_cliente_filial' => array(
			'notEmpty' => array(
				'rule' 		=> 'notEmpty',
				'message' 	=> 'Informe a Filial',
			),
			'validaFilial' => array(
				'rule' 		=> 'validaFilial',
				'message' 	=> 'Este código de filial já foi utilizado',
				'required' 	=> true,
			),
		),
	);

	var $belongsTo = array(
		'ClienteMatriz' 	=> array('className' => 'Cliente', 'foreignKey' => 'codigo_cliente_matriz' ),
		'ClienteFilial' 	=> array('className' => 'Cliente', 'foreignKey' => 'codigo_cliente_filial' )
	
	);

	function validaCombinacaoUnica(){
		$conditions = array(
			'codigo_cliente_filial' => $this->data[$this->name]['codigo_cliente_filial'],
			'codigo_cliente_matriz' => $this->data[$this->name]['codigo_cliente_matriz']
		);
		if (isset($this->data[$this->name]['codigo'])) $conditions['codigo !='] = $this->data[$this->name]['codigo'];
		return ($this->find('count',compact('conditions')) > 0)?false:true;
	}

	function validaFilial(){
		$conditions = array('codigo_cliente_filial' => $this->data[$this->name]['codigo_cliente_filial']);
		if (isset($this->data[$this->name]['codigo'])) $conditions['codigo !='] = $this->data[$this->name]['codigo'];
		return ($this->find('count',compact('conditions')) > 0)?false:true;
	}

	function converteFiltrosEmConditions($filtros) {
		
		$conditions = array();

		if (isset($filtros['codigo_cliente_matriz']) && !empty($filtros['codigo_cliente_matriz']))
			$conditions[$this->name.'.codigo_cliente_matriz'] 	= $filtros['codigo_cliente_matriz'];

		if (isset($filtros['codigo_cliente_filial']) && !empty($filtros['codigo_cliente_filial']))
			$conditions[$this->name.'.codigo_cliente_filial'] = $filtros['codigo_cliente_filial'];

		if (isset($filtros['codigo_cliente_pagador']) && !empty($filtros['codigo_cliente_pagador']))
			$conditions['MatrizProdutoPagador.codigo_cliente_pagador'] = $filtros['codigo_cliente_pagador'];

		if (isset($filtros['codigo_produto']) && !empty($filtros['codigo_produto']))
			$conditions['MatrizProdutoPagador.codigo_produto'] = $filtros['codigo_produto'];
		
		return $conditions;
	}


	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
		
		if( isset($extra['extra']['method']) && $extra['extra']['method'] = 'listarMatrizFilialProdutoPagador' )			
			return $this->listarMatrizFilialProdutoPagador($conditions, $limit, $page, $order);

		$joins = null;

		if (isset($extra['joins']))
			$joins = $extra['joins'];

		return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
	}

	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		
		if( isset($extra['extra']['method']) && $extra['extra']['method'] == 'listarMatrizFilialProdutoPagador' )			
			return $this->listarMatrizFilialProdutoPagador($conditions, null, null, null, 'count');
		
		$joins = null;

		if (isset($extra['joins']))
			$joins = $extra['joins'];

		return $this->find('count', compact('conditions', 'recursive', 'joins'));
	}


	function listarMatrizFilialProdutoPagador($conditions, $limit = null, $page = 1, $order = null, $tipo_find = 'all') {

		$Cliente 			   	= ClassRegistry::init('Cliente');        
		$MatrizProdutoPagador 	= ClassRegistry::init('MatrizProdutoPagador');
		$Produto               	= ClassRegistry::init('Produto');
		$fields 			    = null;

        $joins = array(            
            
            array(
                'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
                'alias' => 'ClienteMatriz',
                'conditions' => 'MatrizFilial.codigo_cliente_matriz = ClienteMatriz.codigo'
            ),
            array(
                'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
                'alias' => 'ClienteFilial',
                'conditions' => 'MatrizFilial.codigo_cliente_filial = ClienteFilial.codigo'
            ),
            array(
                'table' => "{$MatrizProdutoPagador->databaseTable}.{$MatrizProdutoPagador->tableSchema}.{$MatrizProdutoPagador->useTable}",
                'alias' => 'MatrizProdutoPagador',
                'conditions' => 'MatrizFilial.codigo = MatrizProdutoPagador.codigo_matriz_filial',
                'type' => 'LEFT',
            ),
            array(
                'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
                'alias' => 'ClientePagador',
                'conditions' => 'MatrizProdutoPagador.codigo_cliente_pagador = ClientePagador.codigo',
                'type' => 'LEFT',
            ),
            array(
                'table' => "{$Produto->databaseTable}.{$Produto->tableSchema}.{$Produto->useTable}",
                'alias' => 'Produto',
                'conditions' => 'MatrizProdutoPagador.codigo_produto = Produto.codigo',
                'type' => 'LEFT',
            ),
        );

		
		if( $tipo_find == 'all'  )
			$fields = array(
				'MatrizFilial.codigo',

				'ClienteMatriz.codigo',
				'ClienteMatriz.razao_social',

				'MatrizProdutoPagador.codigo',

				'ClienteFilial.codigo',
				'ClienteFilial.razao_social',

				'Produto.codigo',
				'Produto.descricao',

				'ClientePagador.codigo',
				'ClientePagador.razao_social',
			);
		
		$recursive = -1;

		return $this->find($tipo_find, compact('conditions', 'joins', 'fields', 'recursive', 'limit', 'page', 'order'));
	}

	public function pagadorPorProduto($codigo_cliente_filial, $codigo_produto, $somente_ativo = true) {
		$this->bindModel(array('hasOne' => array(
			'MatrizProdutoPagador' => array('foreignKey' => 'codigo_matriz_filial'),
			'ClienteProduto' => array(
				'foreignKey' => false,
				'conditions' => array(
					'ClienteProduto.codigo_cliente = MatrizProdutoPagador.codigo_cliente_pagador',
					'ClienteProduto.codigo_produto = MatrizProdutoPagador.codigo_produto',
				),
			),
		)));
		$fields = array(
			'MatrizProdutoPagador.codigo_cliente_pagador AS codigo_cliente_pagador',
		);
		$conditions = array(
			'codigo_cliente_filial' => $codigo_cliente_filial,
			'ClienteProduto.codigo_produto' => $codigo_produto,
		);
		if ($somente_ativo) {
			$conditions['ClienteProduto.codigo_motivo_bloqueio'] = MotivoBloqueio::MOTIVO_OK;
		}
		return $this->find('first', compact('conditions', 'fields'));
	}

	function verificaExiste($conditions) {
		return ($this->find('count', compact('conditions')) > 0);
	}


	function excluir($codigo) {
		$MatrizProdutoPagador  	= ClassRegistry::init('MatrizProdutoPagador');

		try {
			$this->query('BEGIN TRANSACTION');
			$dados = $this->carregar($codigo);

			$cliente_produto_pagador 	= $MatrizProdutoPagador->find('all', array('conditions' => array('codigo_matriz_filial' => $codigo)));

			foreach($cliente_produto_pagador as $dado) {
				if (!$MatrizProdutoPagador->excluir($dado['MatrizProdutoPagador']['codigo']))
					throw new Exception('Produto e pagador não excluídos');
			}

			if (!parent::excluir($codigo)) throw new Exception('Matriz Filial não excluído');
			$this->commit();
			return true;
		} catch (Exception $ex) {
			$this->rollback();
			return false;
		}
	}

	public function listarAssinaturas($codigo_cliente) {
        $MatrizProdutoPagador = ClassRegistry::init('MatrizProdutoPagador');
        $ClienteProduto 	  = ClassRegistry::init('ClienteProduto');
        $Produto 			  = ClassRegistry::init('Produto');
        $MotivoBloqueio 	  = ClassRegistry::init('MotivoBloqueio');
        $Cliente        	  = ClassRegistry::init('Cliente');

        $joins = array(
            array(
                'table' => "{$MatrizProdutoPagador->databaseTable}.{$MatrizProdutoPagador->tableSchema}.{$MatrizProdutoPagador->useTable}",
                'alias' => 'MatrizProdutoPagador',
                'conditions' => 'MatrizProdutoPagador.codigo_matriz_filial = MatrizFilial.codigo'
            ),
            array(
                'table' => "{$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable}",
                'alias' => 'ClienteProduto',
                'conditions' => array(
                	'ClienteProduto.codigo_cliente = MatrizProdutoPagador.codigo_cliente_pagador',
                 	'ClienteProduto.codigo_produto = MatrizProdutoPagador.codigo_produto'
             	)
            ),
            array(
                'table' => "{$Produto->databaseTable}.{$Produto->tableSchema}.{$Produto->useTable}",
                'alias' => 'Produto',
                'conditions' => 'Produto.codigo = MatrizProdutoPagador.codigo_produto'
            ),
            array(
                'table' => "{$MotivoBloqueio->databaseTable}.{$MotivoBloqueio->tableSchema}.{$MotivoBloqueio->useTable}",
                'alias' => 'MotivoBloqueio',
                'conditions' => 'ClienteProduto.codigo_motivo_bloqueio = MotivoBloqueio.codigo'
            ),
            array(
            	'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
            	'type' => "LEFT",
                'alias' => 'ClienteMatriz',
                'conditions' => 'ClienteMatriz.codigo = MatrizFilial.codigo_cliente_matriz'
        	),
        	array(
            	'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
            	'type' => "LEFT",
                'alias' => 'ClienteFilial',
                'conditions' => 'ClienteFilial.codigo = MatrizFilial.codigo_cliente_filial'
        	),
        	array(
            	'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
            	'type' => "LEFT",
                'alias' => 'ClientePagador',
                'conditions' => 'ClientePagador.codigo = MatrizProdutoPagador.codigo_cliente_pagador'
        	),
        );

        $conditions = array(
            'codigo_cliente_filial' => $codigo_cliente,
            'MatrizProdutoPagador.codigo_cliente_pagador <> MatrizFilial.codigo_cliente_filial',
            'NOT' => array('ClienteProduto.codigo_produto' => array(1,2))
        );

        $fields = array(
            'MatrizFilial.codigo_cliente_matriz',
    		'MatrizFilial.codigo_cliente_filial',
    		'Produto.descricao',
    		'MatrizProdutoPagador.codigo_cliente_pagador',
    		'MotivoBloqueio.descricao',
			'ClienteMatriz.razao_social as nome_matriz',
            'ClienteFilial.razao_social as nome_filial',
            'ClientePagador.razao_social as nome_pagador'
        );

        $recursive = -1;
        return $this->find('all', compact('joins', 'conditions', 'fields', 'recursive'));
    }

    public function carregarMatrizFilial($codigo_cliente_matriz, $codigo_cliente_filial) {
    	return $this->find('first', array('conditions' => array('codigo_cliente_filial' => $codigo_cliente_filial, 'codigo_cliente_matriz' => $codigo_cliente_matriz)));
    }

    public function carregarClientePagador($codigo_cliente_logado, $codigo_produto){
		$this->bindModel(array('hasOne' => array(
			'MatrizProdutoPagador' => array('foreignKey' => 'codigo_matriz_filial'),
			'Cliente' => array('foreignKey' => false, 'conditions' => array('MatrizProdutoPagador.codigo_cliente_pagador = Cliente.codigo')),
		)));
		
		$fields = array('Cliente.*');
		$conditions = array(
			'codigo_cliente_filial' => $codigo_cliente_logado,
			'MatrizProdutoPagador.codigo_produto' => $codigo_produto,
		);
		
		return $this->find('first', compact('conditions', 'fields'));
	}
}

?>