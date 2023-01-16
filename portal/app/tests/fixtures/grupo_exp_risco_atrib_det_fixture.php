<?php
class GrupoExpRiscoAtribDetFixture extends CakeTestFixture {
	
	var $name = 'GrupoExpRiscoAtribDet';
	var $table = 'grupo_exposicao_riscos_atributos_detalhes';

	var $fields = array(
		'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_riscos_atributos_detalhes' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_grupos_exposicao_risco' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
	);

	var $records = array(
		array(
		  'codigo' => 1,
		  'codigo_riscos_atributos_detalhes' => 7,
		  'codigo_grupos_exposicao_risco' => 4342,
		  'codigo_usuario_inclusao' => 63045,
		  'data_inclusao' => '29/05/2017 09:50:00',
		), 
		array(
		  'codigo' => 6,
		  'codigo_riscos_atributos_detalhes' => 8,
		  'codigo_grupos_exposicao_risco' => 8083,
		  'codigo_usuario_inclusao' => 61650,
		  'data_inclusao' => '03/07/2017 08:33:11',
		), 
		array(
		  'codigo' => 7,
		  'codigo_riscos_atributos_detalhes' => 8,
		  'codigo_grupos_exposicao_risco' => 8084,
		  'codigo_usuario_inclusao' => 61650,
		  'data_inclusao' => '03/07/2017 08:33:11',
		), 
		array(
		  'codigo' => 8,
		  'codigo_riscos_atributos_detalhes' => 8,
		  'codigo_grupos_exposicao_risco' => 8085,
		  'codigo_usuario_inclusao' => 61650,
		  'data_inclusao' => '03/07/2017 08:33:11',
		), 
		array(
		  'codigo' => 9,
		  'codigo_riscos_atributos_detalhes' => 7,
		  'codigo_grupos_exposicao_risco' => 8086,
		  'codigo_usuario_inclusao' => 61650,
		  'data_inclusao' => '03/07/2017 08:33:11',
		), 
		array(
		  'codigo' => 10,
		  'codigo_riscos_atributos_detalhes' => 8,
		  'codigo_grupos_exposicao_risco' => 8087,
		  'codigo_usuario_inclusao' => 61650,
		  'data_inclusao' => '03/07/2017 08:36:19',
		), 
		array(
		  'codigo' => 11,
		  'codigo_riscos_atributos_detalhes' => 8,
		  'codigo_grupos_exposicao_risco' => 8089,
		  'codigo_usuario_inclusao' => 61650,
		  'data_inclusao' => '03/07/2017 08:39:41',
		), 
		array(
		  'codigo' => 12,
		  'codigo_riscos_atributos_detalhes' => 8,
		  'codigo_grupos_exposicao_risco' => 8091,
		  'codigo_usuario_inclusao' => 61650,
		  'data_inclusao' => '03/07/2017 08:39:42',
		), 
		array(
		  'codigo' => 13,
		  'codigo_riscos_atributos_detalhes' => 8,
		  'codigo_grupos_exposicao_risco' => 8093,
		  'codigo_usuario_inclusao' => 61650,
		  'data_inclusao' => '03/07/2017 08:39:42',
		), 
		array(
		  'codigo' => 14,
		  'codigo_riscos_atributos_detalhes' => 8,
		  'codigo_grupos_exposicao_risco' => 8095,
		  'codigo_usuario_inclusao' => 61650,
		  'data_inclusao' => '03/07/2017 08:39:42',
		),
		array(
		  'codigo' => 15,
		  'codigo_riscos_atributos_detalhes' => 8,
		  'codigo_grupos_exposicao_risco' => 21339,
		  'codigo_usuario_inclusao' => 67111,
		  'data_inclusao' => '01/01/2018 00:00:00',
		), 
	);

}