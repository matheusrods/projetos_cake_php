<?php
class GrupoExpRiscoFonteGeraLogFixture extends CakeTestFixture {

	var $name = 'GrupoExpRiscoFonteGeraLog';
	var $table = 'grupos_exposicao_risco_fontes_geradoras_log';

	var $fields = array( 
		'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_grupos_exposicao_risco_fontes_geradoras' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_fontes_geradoras' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_grupos_exposicao_risco' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
	);

	var $records = array( 
		array(
		  'codigo' => 1,
		  'codigo_grupos_exposicao_risco_fontes_geradoras' => 88,
		  'codigo_fontes_geradoras' => 3,
		  'codigo_grupos_exposicao_risco' => 11198,
		  'codigo_usuario_inclusao' => 63045,
		  'data_inclusao' => '22/03/2018 14:07:47',
		),
		array(
		  'codigo' => 2,
		  'codigo_grupos_exposicao_risco_fontes_geradoras' => 89,
		  'codigo_fontes_geradoras' => 4,
		  'codigo_grupos_exposicao_risco' => 11199,
		  'codigo_usuario_inclusao' => 63045,
		  'data_inclusao' => '22/03/2018 14:07:49',
		),
		array(
		  'codigo' => 3,
		  'codigo_grupos_exposicao_risco_fontes_geradoras' => 90,
		  'codigo_fontes_geradoras' => 7,
		  'codigo_grupos_exposicao_risco' => 21630,
		  'codigo_usuario_inclusao' => 61650,
		  'data_inclusao' => '16/04/2018 15:13:33',
		),
		array(
		  'codigo' => 4,
		  'codigo_grupos_exposicao_risco_fontes_geradoras' => 91,
		  'codigo_fontes_geradoras' => 3,
		  'codigo_grupos_exposicao_risco' => 21936,
		  'codigo_usuario_inclusao' => 65964,
		  'data_inclusao' => '07/05/2018 10:11:11',
		),
		array(
		  'codigo' => 5,
		  'codigo_grupos_exposicao_risco_fontes_geradoras' => 92,
		  'codigo_fontes_geradoras' => 3,
		  'codigo_grupos_exposicao_risco' => 21939,
		  'codigo_usuario_inclusao' => 65964,
		  'data_inclusao' => '07/05/2018 10:11:11',
		),
		array(
		  'codigo' => 6,
		  'codigo_grupos_exposicao_risco_fontes_geradoras' => 93,
		  'codigo_fontes_geradoras' => 3,
		  'codigo_grupos_exposicao_risco' => 21976,
		  'codigo_usuario_inclusao' => 65964,
		  'data_inclusao' => '07/05/2018 10:33:19',
		),
		array(
		  'codigo' => 7,
		  'codigo_grupos_exposicao_risco_fontes_geradoras' => 94,
		  'codigo_fontes_geradoras' => 3,
		  'codigo_grupos_exposicao_risco' => 21979,
		  'codigo_usuario_inclusao' => 65964,
		  'data_inclusao' => '07/05/2018 10:33:19',
		),
		array(
		  'codigo' => 8,
		  'codigo_grupos_exposicao_risco_fontes_geradoras' => 95,
		  'codigo_fontes_geradoras' => 2,
		  'codigo_grupos_exposicao_risco' => 8714,
		  'codigo_usuario_inclusao' => 61650,
		  'data_inclusao' => '18/06/2018 13:34:09',
		),
		array(
		  'codigo' => 9,
		  'codigo_grupos_exposicao_risco_fontes_geradoras' => 96,
		  'codigo_fontes_geradoras' => 2,
		  'codigo_grupos_exposicao_risco' => 8087,
		  'codigo_usuario_inclusao' => 67117,
		  'data_inclusao' => '22/08/2018 16:51:02',
		),
		array(
		  'codigo' => 10,
		  'codigo_grupos_exposicao_risco_fontes_geradoras' => 97,
		  'codigo_fontes_geradoras' => 2,
		  'codigo_grupos_exposicao_risco' => 8714,
		  'codigo_usuario_inclusao' => 61802,
		  'data_inclusao' => '03/09/2018 09:35:00',
		), 
	);

}
?>