<?php
class PropostaCredenciamentoLogFixture extends CakeTestFixture {
	var $name = 'PropostaCredenciamentoLog';
	var $table = 'propostas_credenciamento_log';
	public $fields = array( 
	  'codigo_status_proposta_credenciamento' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 2, ),
	  'codigo' =>  array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11,   'key' => 'primary',),
	  'codigo_proposta_credenciamento' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	  'tipo_atendimento' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	  'acesso_portal' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	  'exames_local_unico' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	  'ativo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	  'codigo_conselho_profissional' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	  'codigo_motivo_recusa' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	  'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	  'codigo_usuario_alteracao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	  'codigo_empresa' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	  'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
	  'razao_social' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
	  'nome_fantasia' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
	  'codigo_documento' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 18, ),
	  'responsavel_tecnico_nome' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
	  'responsavel_tecnico_numero_conselho' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 25, ),
	  'responsavel_administrativo' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
	  'favorecido' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
	  'agencia' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
	  'numero_conta' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
	  'telefone' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 12, ),
	  'fax' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 12, ),
	  'celular' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 13, ),
	  'email' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
	  'numero_banco' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
	  'tipo_conta' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
	  'responsavel_tecnico_conselho_uf' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 2, )
	);

	public $records = array();
}
?> 