<?php
class ClienteOperacao extends AppModel {
	var $name = 'ClienteOperacao';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
	var $useTable = 'cliente_operacao';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $belongsTo = array(
	   'Operacao' => array(
	       'className' => 'Operacao',
	       'foreignKey' => 'codigo_operacao'
	   )
	);
	var $validate = array(
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o cliente',
	        'required' => true
        ),
        'codigo_operacao' => array( 
            'vazio' => array(
                    'rule' => 'notEmpty',
                    'message' => 'Informe a operação',
                    'required' => true
            ),
            'duplicado' => array(
                    'rule' => 'verificarDuplicidade',
                    'message' => 'Operação já cadastrada',
                    'required' => true,
                    'on' => 'create'
            )
         )
	);
	
	
    function verificarDuplicidade() {
        $conditions = array(
            'conditions' => array(
                'codigo_cliente' => $this->data[$this->name]['codigo_cliente'],
                'codigo_operacao' => $this->data[$this->name]['codigo_operacao'],
                'data_exclusao' => NULL
            )
        );
        return $this->find('count', $conditions) <= 0;
    }
		
	function excluir($codigo) {
	    $cliente_operacao = $this->read(null, $codigo);
	    $cliente_operacao['ClienteOperacao']['data_exclusao'] = date('Y-m-d H:i:s');
	    $cliente_operacao['ClienteOperacao']['codigo_usuario_exclusao'] = $_SESSION['Auth']['Usuario']['codigo'];
	    return $this->save($cliente_operacao);
	}
	
    function operacoesDoCliente($codigo_cliente) {
       return $this->find('all', array('conditions' => array('codigo_cliente' => $codigo_cliente, 'data_exclusao' => NULL)));
    }
	
}
?>