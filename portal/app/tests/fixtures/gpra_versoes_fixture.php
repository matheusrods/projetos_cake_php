<?php
class GpraVersoesFixture extends CakeTestFixture {
	var $name = 'GpraVersoes';
	var $table = 'grupos_prevencao_riscos_ambientais_versoes';

	var $fields = array(
		'codigo' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_grupos_prevencao_riscos_ambientais' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_medico' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_cliente' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_ppra_versoes' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'data_inicio_vigencia' => array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'periodo_vigencia' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 1, ),
	);

}