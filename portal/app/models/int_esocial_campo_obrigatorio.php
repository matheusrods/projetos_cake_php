<?php
class IntEsocialCampoObrigatorio extends AppModel {

	public $name		   	= 'IntEsocialCampoObrigatorio';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'int_esocial_campo_obrigatorio';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_int_esocial_certificado'));

	public $validate = array(
		'codigo_int_esocial_tipo_evento' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o tipo de evento relacionado!'
		),
		'ativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe se está ativo!'
		),
	);

	public function getLayoutTxt2($codigo_tipo)
	{

		$dados = $this->find('all',array('conditions' => array('IntEsocialCampoObrigatorio.ativo' => 1,'IntEsocialCampoObrigatorio.codigo_int_esocial_tipo_evento' => $codigo_tipo)));
		$arr_dados = array();
		foreach($dados AS $val) {
			$arr_dados[$val['IntEsocialCampoObrigatorio']['campo']] = $val['IntEsocialCampoObrigatorio'];
		}

		return $arr_dados;

	}



}//FINAL CLASS IntEsocialCampoObrigatorio