<?
class SetorLogFixture extends CakeTestFixture {
	var $name = 'SetorLog';
	var $table = 'setores_log';

	var $fields = array( 
		'descricao_setor' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'observacao_aso' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'acao_sistema' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
		'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_setores' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_cliente' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'ativo' => array ( 'type' => 'boolean', 'null' => true, 'default' => '', 'length' => NULL, ),
		'descricao' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 50, ),
		'codigo_rh' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 50, ),
	);

}