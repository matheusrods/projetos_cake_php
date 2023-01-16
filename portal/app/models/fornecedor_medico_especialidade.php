<?php
class FornecedorMedicoEspecialidade extends AppModel {

    var $name = 'FornecedorMedicoEspecialidade';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'fornecedores_medico_especialidades';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

	var $validate = array(
        'codigo_fornecedor' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código de Fornecedor!'
		),	
        'codigo_medico' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código do Médico!'
		),
		'codigo_especialidade' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código de Especialidade!'
		)

	);      
}
