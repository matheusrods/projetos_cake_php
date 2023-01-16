<?php
class ClienteIp extends AppModel {
	var $name = 'ClienteIp';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
	var $useTable = 'cliente_ip';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	
	var $validate = array(
        'descricao' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'message' => 'Informe o numero IP',
            ),
            'regExp' => array(
              'rule' => '/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/',
              'message' => 'Informe um numero IP válido',
            ),
        ),
        'codigo_cliente' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'message' => 'Informe o código do cliente',
            )
        )
	);
	
	function carregarIp($codigo_cliente,$descricao){
        $conditions = array('codigo_cliente' => $codigo_cliente);
        $fields     = array(
            'codigo_cliente'
            ,'COUNT(1) AS total'
            ,"(
                SELECT count(1) FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} AS CIP 
                WHERE 
                    CIP.codigo_cliente = ClienteIp.codigo_cliente 
                    AND CIP.descricao = '{$descricao}'
            ) AS ip"
        );
        $group      = array('codigo_cliente');
        return $this->find('first',compact('fields','conditions','group'));
    }

}
?>