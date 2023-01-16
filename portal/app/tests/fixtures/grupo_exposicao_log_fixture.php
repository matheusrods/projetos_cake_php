<?php
class GrupoExposicaoLogFixture extends CakeTestFixture {

	var $name = 'GrupoExposicaoLog';
	var $table = 'grupo_exposicao_log';

	var $fields = array( 
		'descricao_atividade' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'observacao' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'medidas_controle' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_grupo_exposicao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_cargo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_cliente_setor' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_grupo_homogeneo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_funcionario' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'funcionario_entrevistado' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_medico' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'data_documento' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'data_inicio_vigencia' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'funcionario_entrevistado_terceiro' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
	);
	
}