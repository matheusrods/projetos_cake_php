<?php
class ExcecaoFormula extends AppModel {
    var $name = 'ExcecaoFormula';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'excecoes_formulas';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    var $validate = array(
    	'codigo_cliente_pagador' => array(
            'empty' => array(
                'rule' => 'notEmpty',
                'message' => 'Cliente não informado',
            ),
            'unico' => array(
        		'rule' => 'unique_verify',
        		'message' => 'Este Cliente - Produto já existe',
        		'required' => true,
        	),
        ),
        'codigo_produto' => array(
            'rule' => 'notEmpty',
            'message' => 'Produto não informado',
        ),
    );
    var $belongsTo = array(
        'Cliente' => array('foreignKey' => 'codigo_cliente_pagador'),
        'Produto' => array('foreignKey' => 'codigo_produto'),
    );

    function unique_verify() {
    	if (isset($this->data[$this->name]['codigo_cliente_pagador']) && isset($this->data[$this->name]['codigo_produto']) ) {
    		$conditions = array('codigo_cliente_pagador' => $this->data[$this->name]['codigo_cliente_pagador'], 'codigo_produto' => $this->data[$this->name]['codigo_produto']);
    		if (isset($this->data[$this->name]['codigo']) && !empty($this->data[$this->name]['codigo'])) {
    			$conditions['not'] = array('codigo' => $this->data[$this->name]['codigo']);
    		}
	    	$result = $this->find('count', compact('conditions'));
	    	return $result == 0;
    	}
    	return false;
    }
}
?>