<?php

class ClienteVeiculoLog extends AppModel {

	var $name = 'ClienteVeiculoLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHhealth';
	var $useTable = 'cliente_veiculo_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $validate = array(
		'codigo_cliente' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o codigo do cliente'
		),
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

    function ultimoLog($codigo_cliente) {
        $maior_data = $this->find('first', array('order' => array('ClienteVeiculoLog.data_inclusao desc'), 'fields' => array('ClienteVeiculoLog.data_inclusao'), 'conditions' => array('ClienteVeiculoLog.codigo_cliente' => $codigo_cliente)));
        if ($maior_data) {
            $this->bindUsuarioInclusao();
            $result = $this->find('first', array('conditions' => array('ClienteVeiculoLog.codigo_cliente' => $codigo_cliente, 'ClienteVeiculoLog.data_inclusao' => AppModel::dateToDbDate($maior_data['ClienteVeiculoLog']['data_inclusao']))));
            $this->unbindUsuarioInclusao();
            return $result;
        }else{
        	return false;
        }
    }	
}

?>
