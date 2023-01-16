<?php
class GrupoExpRiscoFonteGeraVersoesFixture extends CakeTestFixture {
	var $name = 'GrupoExpRiscoFonteGeraVersoes';
	var $table = 'grupos_exposicao_risco_fontes_geradoras_versoes';

	var $fields = array(
		'codigo' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_grupos_exposicao_risco_fontes_geradoras' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_fontes_geradoras' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_grupos_exposicao_risco' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_ppra_versoes' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
	);

}