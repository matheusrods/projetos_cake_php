<?php
class Acoes extends AppModel {
	public $name = 'Acoes';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'acoes';
	public $primaryKey = 'codigo';

	public $validate = array(
		'codigo_subperfil' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a descrição',
			'required' => true,
		),
		'codigo_cliente' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o codigo_cliente',
			)
		)
	);


}
