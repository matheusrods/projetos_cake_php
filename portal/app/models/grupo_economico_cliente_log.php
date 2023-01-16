<?php
class GrupoEconomicoClienteLog extends AppModel {
	var $name = 'GrupoEconomicoClienteLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'grupos_economicos_clientes_log';
	var $primaryKey = 'codigo';
	var $displayField = 'descricao';
	var $actsAs = array('Secure');
    var $validate = array(
        'codigo_grupos_economicos_clientes' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

}