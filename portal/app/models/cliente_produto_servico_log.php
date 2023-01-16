<?php
class ClienteProdutoServicoLog extends AppModel {
    var $name = 'ClienteProdutoServicoLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHhealth';
    var $useTable = 'cliente_produto_servico_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $validate = array(
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'o campo código não deve ser deixa em branco'
        )
    );
    var $belongsTo = array(
        'ClienteProduto' => array(
            'className' => 'ClienteProduto',
            'foreignKey' => 'codigo_cliente_produto'
        ),
        'Servico' => array(
            'className' => 'Servico',
            'foreignKey' => 'codigo_servico'
        ),
        'ProfissionalTipo' => array(
            'className' => 'ProfissionalTipo',
            'foreignKey' => 'codigo_profissional_tipo'
        )
    );

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
    
    function unbindUsuarioInclusao() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'Usuario'
            )
        ));
    }
 
    function listar($conditions = null) {
        $this->bindUsuarioInclusao();

        $order = array('ClienteProdutoServicoLog.data_inclusao desc');
        $result = $this->find('all', array('limit' => 10, 'conditions' => $conditions, 'order' => $order, 'recursive' => 2));
        $this->unbindUsuarioInclusao();
        return $result;
    }
    
    function converteFiltroEmCondition($data, $condition_vazia_bloqueada = true) {
        $conditions = array();
        if (isset($data['codigo']) && !empty($data['codigo']))
            $conditions['ClienteProdutoServicoLog.codigo'] = $data['codigo'];
        if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente']))
            $conditions['ClienteProduto.codigo_cliente'] = $data['codigo_cliente'];
        if (isset($data['usuario']) && !empty($data['usuario']))
            $conditions['Usuario.apelido like'] = '%' . $data['usuario'] . '%';
        if (isset($data['data_inicio']) && !empty($data['data_inicio'])) {
            $conditions[$this->name.'.data_inclusao >'] = AppModel::dateToDbDate($data['data_inicio']) . ' 00:00:00.0';
        }
        if (isset($data['data_fim']) && !empty($data['data_fim'])) {
            $conditions[$this->name.'.data_inclusao <'] = AppModel::dateToDbDate($data['data_fim']) . ' 23:59:59.997';
        }
        
        if (count($conditions) == 0) {
            if ($condition_vazia_bloqueada)
                $conditions = array('ClienteProdutoServicoLog.codigo' => null);
        }
        
        return $conditions;
    }
	
	function incluirLogsParaContrato($conditions) {
		
		$this->ClienteProduto		  =& ClassRegistry::init('ClienteProduto');
		$this->ClienteProdutoServico2  =& ClassRegistry::init('ClienteProdutoServico2');
		$this->ClienteProdutoContrato =& ClassRegistry::init('ClienteProdutoContrato');
		
		$fields = array(
			'ClienteProdutoServico2.codigo',
			'ClienteProdutoServico2.codigo_cliente_produto',
			'ClienteProdutoServico2.codigo_servico',
			'ClienteProdutoServico2.codigo_profissional_tipo',
			'ClienteProdutoServico2.valor',
			'ClienteProdutoServico2.codigo_cliente_pagador',
			'ClienteProdutoServico2.consistencia_motorista',
			'ClienteProdutoServico2.consistencia_veiculo',
			'ClienteProdutoServico2.consulta_embarcador',
			'ClienteProdutoServico2.tempo_pesquisa',
			'ClienteProdutoServico2.validade',
			'ClienteProdutoServico2.data_inclusao',
			'ClienteProdutoServico2.codigo_usuario_inclusao',
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
			codigo_cliente_produto_servico,
			codigo_cliente_produto,
			codigo_servico,
			codigo_profissional_tipo,
			valor,
			codigo_cliente_pagador,
			consistencia_motorista,
			consistencia_veiculo,
			consulta_embarcador,
			tempo_pesquisa,
			validade,
			data_inclusao,
			codigo_usuario_inclusao,
			acao_sistema
		) {$query_logs}";
		
		return ($this->query($query) !== false);
	}
    
}
