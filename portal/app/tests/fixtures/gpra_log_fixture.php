<?
class GpraLogFixture extends CakeTestFixture {
	var $name = 'GpraLog';
	var $table = 'grupos_prevencao_riscos_ambientais_log';

	var $fields = array( 
		'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_grupos_prevencao_riscos_ambientais' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_medico' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_cliente' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inicio_vigencia' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'periodo_vigencia' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 2, ),
	);

}