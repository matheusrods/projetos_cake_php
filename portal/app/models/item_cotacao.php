<?php
class ItemCotacao extends AppModel {
	public $name = 'ItemCotacao';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'itens_cotacoes';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

	public $belongsTo = array(
		'Cotacao' => array(
			'className' => 'Cotacao',
			'foreignKey' => 'codigo_cotacao'
			),
		'Servico' => array(
			'className' => 'Servico',
			'foreignKey' => 'codigo_servico'
			)
		);

	public $validate = array(
		'codigo_servico' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o serviço.',
			'required' => true,	
			),
		'quantidade' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a quantidade.',
			'required' => true,	
			),
		'valor_unitario' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o valor.',
			'required' => true,	
			)
		);
}