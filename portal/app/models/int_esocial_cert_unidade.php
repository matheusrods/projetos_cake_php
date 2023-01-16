<?php
class IntEsocialCertUnidade extends AppModel {

	var $name		   	= 'IntEsocialCertUnidade';
	var $tableSchema   	= 'dbo';
	var $databaseTable 	= 'RHHealth';
	var $useTable	   	= 'int_esocial_certificado_unidade';
	var $primaryKey	   	= 'codigo';
	var $actsAs		   	= array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_int_esocial_certificado_unidade'));

	var $validate = array(
		'codigo_empresa' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Empresa!'
		),
		'codigo_cliente' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Cliente!'
		),
		'codigo_int_esocial_certificado' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o certificado que ele está relacionado!'
		),
		'ativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe se está ativo!'
		),
	);


	public function deleteTodosRelUnidades($codigo_certificado)
	{

		$query = "DELETE FROM RHHealth.dbo.int_esocial_certificado_unidade WHERE codigo_int_esocial_certificado = {$codigo_certificado};";

		return  $this->query($query);

	}// fim deleteTodosRelacionaemtnsoUni

	/**
	 * [getCertificadosUnidades pega os dados do cliente]
	 * @param  [type] $codigo_certificado [description]
	 * @return [type]                     [description]
	 */
	public function getCertificadosUnidades($codigo_certificado)
	{

		$fields = array(
			'IntEsocialCertUnidade.codigo',
			'IntEsocialCertUnidade.codigo_cliente',
			'IntEsocialCertUnidade.codigo_int_esocial_certificado',
			'Cliente.codigo_documento',
			'Cliente.codigo_documento_real',
			'Cliente.razao_social',
			'Cliente.nome_fantasia'
		);

		$joins = array(			
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => array('Cliente.codigo = IntEsocialCertUnidade.codigo_cliente')
			)
		);

		$dados = $this->find('all',array('fields' => $fields,'joins' => $joins,'conditions' => array('IntEsocialCertUnidade.codigo_int_esocial_certificado' => $codigo_certificado,'IntEsocialCertUnidade.ativo' => 1)));
		// debug($dados);exit;
		return $dados;

	}//fim getCertificadosUnidades($codigo_certificado)


}//FINAL CLASS IntEsocialCertUnidade