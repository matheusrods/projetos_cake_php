<?php
class ClienteProdutoServico2Log extends AppModel {
	var $name = 'ClienteProdutoServico2Log';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHhealth';
	var $useTable = 'cliente_produto_servico2_log';
	var $primaryKey = 'codigo';
	var $foreignKeyLog = 'codigo_cliente_produto_servico2';
	var $actsAs = array('Secure');

	function bindUsuarioInclusao() {
        $this->bindModel(array(
            'belongsTo' => array(
                'Usuario' => array(
                    'class' => 'Usuario',
                    'foreignKey' => 'codigo_usuario_inclusao'
                )
            )
        ));
    }

    function bindUsuarioAlteracao() {
        $this->bindModel(array(
            'belongsTo' => array(
                'UsuarioAlteracao' => array(
                    'className' => 'Usuario',
                    'foreignKey' => 'codigo_usuario_alteracao'
                )
            )
        ));
    }

    function bindServico() {
        $this->bindModel(array(
            'belongsTo' => array(
                'Servico' => array(
                    'className' => 'Servico',
                    'foreignKey' => 'codigo_servico'
                )
            )
        ));
    }

    function bindClienteProduto() {
        $this->bindModel(array(
            'belongsTo' => array(
                'ClienteProduto' => array(
                    'class' => 'ClienteProduto',
                    'foreignKey' => 'codigo_cliente_produto',
                ),
                'Produto' => array(
                    'className' => 'Produto',
                    'foreignKey' => false,
                    'conditions' => array(
                    	'ClienteProduto.codigo_produto = Produto.codigo'
                    ),
                ),
                'Cliente' => array(
                    'className' => 'Cliente',
                    'foreignKey' => false,
                    'conditions' => array(
                    	'ClienteProduto.codigo_cliente = Cliente.codigo'
                    ),
                ),
            )
        ));
    }

	function incluirLogsParaContrato($conditions) {
		
		$this->ClienteProduto		  =& ClassRegistry::init('ClienteProduto');
		$this->ClienteProdutoServico2  =& ClassRegistry::init('ClienteProdutoServico2');
		$this->ClienteProdutoContrato =& ClassRegistry::init('ClienteProdutoContrato');
		
		$fields = array(
			'ClienteProdutoServico2.codigo',
			'ClienteProdutoServico2.codigo_cliente_produto',
			'ClienteProdutoServico2.codigo_servico',
			'ClienteProdutoServico2.valor',
			'ClienteProdutoServico2.codigo_cliente_pagador',
			'ClienteProdutoServico2.data_inclusao',
			'ClienteProdutoServico2.codigo_usuario_inclusao',
			'ClienteProdutoServico2.qtd_premio_minimo',
			'ClienteProdutoServico2.valor_premio_minimo',
			'ClienteProdutoServico2.valor_maximo',
			1
		);
		
		$joins = array(
			array(
				'table' => "{$this->ClienteProdutoServico2->databaseTable}.{$this->ClienteProdutoServico2->tableSchema}.{$this->ClienteProdutoServico2->useTable}",
				'alias' => 'ClienteProdutoServico2',
				'type' => 'INNER',
				'conditions' => array('ClienteProdutoServico2.codigo_cliente_produto = ClienteProduto.codigo')
			),
			array(
				'table' => "{$this->ClienteProdutoContrato->databaseTable}.{$this->ClienteProdutoContrato->tableSchema}.{$this->ClienteProdutoContrato->useTable}",
				'alias' => 'ClienteProdutoContrato',
				'type' => 'INNER',
				'conditions' => array('ClienteProdutoContrato.codigo_cliente_produto = ClienteProduto.codigo')
			)
		);

		$conditions['ClienteProduto.codigo_motivo_bloqueio <> '] = 3;
		
		$dbo = $this->getDataSource();
		$query_logs = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "{$this->ClienteProduto->databaseTable}.{$this->ClienteProduto->tableSchema}.{$this->ClienteProduto->useTable}",
				'alias' => 'ClienteProduto',
				'limit' => null,
				'offset' => null,
				'joins' => $joins,
				'conditions' => $conditions,
				'order' => null,
				'group' => null,
			), $this
		);
				
		$query = "INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} (
			codigo_cliente_produto_servico2,
			codigo_cliente_produto,
			codigo_servico,
			valor,
			codigo_cliente_pagador,
			data_inclusao,
			codigo_usuario_inclusao,
			qtd_premio_minimo,
			valor_premio_minimo,
			valor_maximo,
			acao_sistema			
		) {$query_logs}";
		
		return ($this->query($query) !== false);
	}

	function listarParaEnvioEmailJuridico(){
        return $this->find('all',array(
            'conditions' => array(
                'OR' => array('ClienteProdutoServico2Log.enviado_juridico IS NULL','ClienteProdutoServico2Log.enviado_juridico' => false,),
            ),
            'order' => 'ClienteProdutoServico2Log.data_inclusao DESC',
        ));
    }

    /*Verifica se houve bloqueio de produto por cliente nos ultimos 30 dias*/
    function verificaClienteInativoLog( $codigo_cliente, $qtde_dias=30 ){
        return $this->find('count',array(
            'conditions' => array(
                'codigo_cliente'  => $codigo_cliente,
                'codigo_servico' => 4,//Renovacao Automatica
                'DATEDIFF( day, data_inclusao, GETDATE() ) >=' => $qtde_dias
            )
        ));        
    }    

}