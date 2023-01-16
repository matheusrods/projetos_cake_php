<?php
class PropostaCredenciamentoFixture extends CakeTestFixture {
	var $name = 'PropostaCredenciamento';
	var $table = 'propostas_credenciamento';
	public $fields = array( 
		'motivo_recusa' => array('type' => 'string','null' => true,'default' => '','length' => 16,),
		'codigo_status_proposta_credenciamento' => array('type' => 'integer','null' => true,'default' => '','length' => 2,),
		'codigo' => array('type' => 'integer','null' => true,'default' => '','length' => 11,'key' => 'primary',),
		'tipo_atendimento' => array('type' => 'integer','null' => true,'default' => '','length' => 4,),
		'acesso_portal' => array('type' => 'integer','null' => true,'default' => '','length' => 4,),
		'exames_local_unico' => array('type' => 'integer','null' => true,'default' => '','length' => 4,),
		'ativo' => array('type' => 'integer','null' => true,'default' => '','length' => 4,),
		'codigo_conselho_profissional' => array('type' => 'integer','null' => true,'default' => '','length' => 4,),
		'data_inclusao' => array('type' => 'datetime','null' => true,'default' => '','length' => NULL,),
		'razao_social' => array('type' => 'string','null' => true,'default' => '','length' => 255,),
		'nome_fantasia' => array('type' => 'string','null' => true,'default' => '','length' => 255,),
		'codigo_documento' => array('type' => 'string','null' => true,'default' => '','length' => 18,),
		'responsavel_tecnico_nome' => array('type' => 'string','null' => true,'default' => '','length' => 255,),
		'responsavel_tecnico_numero_conselho' => array('type' => 'string','null' => true,'default' => '','length' => 25,),
		'responsavel_administrativo' => array('type' => 'string','null' => true,'default' => '','length' => 255,),
		'favorecido' => array('type' => 'string','null' => true,'default' => '','length' => 255,),
		'agencia' => array('type' => 'string','null' => true,'default' => '','length' => 255,),
		'numero_conta' => array('type' => 'string','null' => true,'default' => '','length' => 255,),
		'telefone' => array('type' => 'string','null' => true,'default' => '','length' => 12,),
		'fax' => array('type' => 'string','null' => true,'default' => '','length' => 12,),
		'celular' => array('type' => 'string','null' => true,'default' => '','length' => 13,),
		'email' => array('type' => 'string','null' => true,'default' => '','length' => 255,),
		'numero_banco' => array('type' => 'string','null' => true,'default' => '','length' => 255,),
		'tipo_conta' => array('type' => 'string','null' => true,'default' => '','length' => 255,),
		'responsavel_tecnico_conselho_uf' => array('type' => 'string','null' => true,'default' => '','length' => 2,),
	);

	public $records = array(
	    array(
	    	'motivo_recusa' => NULL,
			'codigo_status_proposta_credenciamento' => 9,
			'codigo' => 511,
			'tipo_atendimento' => 1,
			'acesso_portal' => 1,
			'exames_local_unico' => 1,
			'ativo' => 1,
			'codigo_conselho_profissional' => 1,
			'data_inclusao' => '2016-04-25 11:04:33',
			'razao_social' => 'DR CONSULTA CLINICA MEDICA LTDA',
			'nome_fantasia' => 'DR CONSULTA',
			'codigo_documento' => '14245016000179',
			'responsavel_tecnico_nome' => 'Dr. Flavio oliveira',
			'responsavel_tecnico_numero_conselho' => '15.663 6',
			'responsavel_administrativo' => 'Sr. Osnir111 alvez',
			'favorecido' => 'dr consulta',
			'agencia' => '0002',
			'numero_conta' => '5258326-3',
			'telefone' => '(11)6659-866',
			'fax' => ' ',
			'celular' => ' ',
			'email' => 'daniloborgespereira@gmail.com',
			'numero_banco' => '065 - Banco Bracce S.A.',
			'tipo_conta' => '1',
			'responsavel_tecnico_conselho_uf' => 'BA',
		)
	);
}
?> 