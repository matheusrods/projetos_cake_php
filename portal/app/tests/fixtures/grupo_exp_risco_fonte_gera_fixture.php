<?php
class GrupoExpRiscoFonteGeraFixture extends CakeTestFixture {

	var $name = 'GrupoExpRiscoFonteGera';
	var $table = 'grupos_exposicao_risco_fontes_geradoras';

	var $fields = array( 
		'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_fontes_geradoras' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_grupos_exposicao_risco' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
	);

	var $records = array(
		array(
			'codigo' => 5233,
			'codigo_fontes_geradoras' => 6,
			'codigo_grupos_exposicao_risco' => 2450,
			'codigo_usuario_inclusao' => 61648,
			'data_inclusao' => '2016-08-15 17:58:45',
		),
		array(
			'codigo' => 5234,
			'codigo_fontes_geradoras' => 6,
			'codigo_grupos_exposicao_risco' => 2587,
			'codigo_usuario_inclusao' => 61648,
			'data_inclusao' => '2016-08-17 09:05:26',
		),
		array(
			'codigo' => 5235,
			'codigo_fontes_geradoras' => 2007,
			'codigo_grupos_exposicao_risco' => 2588,
			'codigo_usuario_inclusao' => 61648,
			'data_inclusao' => '2016-08-17 09:07:01',
		),
		array(
			'codigo' => 5236,
			'codigo_fontes_geradoras' => 2007,
			'codigo_grupos_exposicao_risco' => 2589,
			'codigo_usuario_inclusao' => 61648,
			'data_inclusao' => '2016-08-17 09:07:01',
		),
		array(
			'codigo' => 5237,
			'codigo_fontes_geradoras' => 2005,
			'codigo_grupos_exposicao_risco' => 2590,
			'codigo_usuario_inclusao' => 61648,
			'data_inclusao' => '2016-08-17 09:09:03',
		),
		array(
			'codigo' => 5238,
			'codigo_fontes_geradoras' => 2005,
			'codigo_grupos_exposicao_risco' => 2591,
			'codigo_usuario_inclusao' => 61608,
			'data_inclusao' => '2016-08-17 13:17:27',
		),
		array(
			'codigo' => 5239,
			'codigo_fontes_geradoras' => 6,
			'codigo_grupos_exposicao_risco' => 2591,
			'codigo_usuario_inclusao' => 61608,
			'data_inclusao' => '2016-08-17 13:17:27',
		),
		array(
			'codigo' => 5241,
			'codigo_fontes_geradoras' => 1,
			'codigo_grupos_exposicao_risco' => 2593,
			'codigo_usuario_inclusao' => 61608,
			'data_inclusao' => '2016-08-25 16:02:39',
		),
		array(
			'codigo' => 5243,
			'codigo_fontes_geradoras' => 2005,
			'codigo_grupos_exposicao_risco' => 2600,
			'codigo_usuario_inclusao' => 66984,
			'data_inclusao' => '2016-08-26 12:31:43',
		),
		array(
			'codigo' => 5246,
			'codigo_fontes_geradoras' => 2005,
			'codigo_grupos_exposicao_risco' => 2592,
			'codigo_usuario_inclusao' => 2,
			'data_inclusao' => '2016-08-31 11:51:28',
		),
		array(
			'codigo' => 5247,
			'codigo_fontes_geradoras' => 12,
			'codigo_grupos_exposicao_risco' => 21339,
			'codigo_usuario_inclusao' => 67111,
			'data_inclusao' => '2018-01-01 00:00:00',
		),
	);

}