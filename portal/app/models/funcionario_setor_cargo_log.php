<?php
class FuncionarioSetorCargoLog extends AppModel {

	public $name = 'FuncionarioSetorCargoLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'funcionario_setores_cargos_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_funcionario_setores_cargos';
	var $actsAs = array('Secure');
    var $validate = array(
        'codigo_funcionario_setores_cargos' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

}