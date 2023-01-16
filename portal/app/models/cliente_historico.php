<?php
class ClienteHistorico extends AppModel {
	var $name = 'ClienteHistorico';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cliente_historico';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $validate = array(
	    'codigo_cliente' => array(
	        'rule' => 'notEmpty',
	        'message' => 'O cliente não foi informado'
	    ),
	    'observacao' => array(
	        'rule' => 'notEmpty',
	        'message' => 'Você precisa preencher a informação'
	    ),
        'codigo_tipo_historico' => array(
            'rule' => 'notEmpty',
            'message' => 'Escolha um tipo'
        )
	);
	var $belongsTo = array(
	    'Usuario' => array(
	        'className' => 'Usuario',
	        'foreignKey' => 'codigo_usuario_inclusao'
	    ),
	);    
	
	function bindTipoHistorico() {
		$this->bindModel(array(
			'belongsTo' => array(
				'TipoHistorico' => array(
					'className' => 'TipoHistorico',
					'foreignKey' => 'codigo_tipo_historico'
				)
			)
		));
	}

	function listaHistorico($codigo_cliente){
		$this->bindTipoHistorico();

	    $result = $this->find('all', array('conditions' => array('ClienteHistorico.codigo_cliente' => $codigo_cliente), 'order' => array('ClienteHistorico.data_inclusao desc') ));

	    return $result;
	}
}
?>