<?php
class RelatorioEmail extends AppModel {
	var $name 			= 'RelatorioEmail';
	var $tableSchema 	= 'publico';
	var $databaseTable 	= 'dbBuonny';
	var $useTable 		= 'relatorio_email';
	var $primaryKey 	= 'codigo';
	var $actsAs 		= array('Secure');	

	var $validate = array(
		'anexo_nome' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => 'Informe um nome para o anexo'
		),
		'email' => array(
			'rule' => 'email',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Email não foi identificado'
		),		
		'metodo' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => 'Erro ao identificar Relatório'
		),        
		'conditions' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => 'Erro ao identificar parametros para a pesquisa'
		),        
	);

	public function incluir( $data ){
		if( !empty($data['RelatorioEmail']['conditions']) )
			$data['RelatorioEmail']['conditions'] = serialize($data['RelatorioEmail']['conditions']);
		return parent::incluir( $data );
	}
}
?>