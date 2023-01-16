<?php
class GrupoExposicaoRiscoEpcLogFixture extends CakeTestFixture {

	var $name = 'GrupoExposicaoRiscoEpcLog';
	var $table = 'grupos_exposicao_risco_epc_log';

	var $fields = array( 
		'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_grupos_exposicao_risco_epc' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_epc' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_grupos_exposicao_risco' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'controle' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 1, ),
		'epi_eficaz' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
	);

	var $records = array( 
		array(
		  'codigo' => 1,
		  'codigo_grupos_exposicao_risco_epc' => 17,
		  'codigo_epc' => 2,
		  'codigo_grupos_exposicao_risco' => 21630,
		  'codigo_usuario_inclusao' => 61650,
		  'data_inclusao' => '16/04/2018 15:13:35',
		  'controle' => '1',
		  'epi_eficaz' => NULL,
		),
		array(
		  'codigo' => 2,
		  'codigo_grupos_exposicao_risco_epc' => 18,
		  'codigo_epc' => 2,
		  'codigo_grupos_exposicao_risco' => 8714,
		  'codigo_usuario_inclusao' => 67111,
		  'data_inclusao' => '08/05/2018 15:12:23',
		  'controle' => '2',
		  'epi_eficaz' => NULL,
		),
		array(
		  'codigo' => 3,
		  'codigo_grupos_exposicao_risco_epc' => 19,
		  'codigo_epc' => 2,
		  'codigo_grupos_exposicao_risco' => 22024,
		  'codigo_usuario_inclusao' => 67531,
		  'data_inclusao' => '23/05/2018 15:37:23',
		  'controle' => NULL,
		  'epi_eficaz' => NULL,
		),
		array(
		  'codigo' => 4,
		  'codigo_grupos_exposicao_risco_epc' => 20,
		  'codigo_epc' => 2,
		  'codigo_grupos_exposicao_risco' => 22024,
		  'codigo_usuario_inclusao' => 67531,
		  'data_inclusao' => '23/05/2018 15:54:03',
		  'controle' => NULL,
		  'epi_eficaz' => NULL,
		),
		array(
		  'codigo' => 5,
		  'codigo_grupos_exposicao_risco_epc' => 21,
		  'codigo_epc' => 2,
		  'codigo_grupos_exposicao_risco' => 22024,
		  'codigo_usuario_inclusao' => 67531,
		  'data_inclusao' => '23/05/2018 16:42:21',
		  'controle' => NULL,
		  'epi_eficaz' => NULL,
		),
		array(
		  'codigo' => 6,
		  'codigo_grupos_exposicao_risco_epc' => 22,
		  'codigo_epc' => 2,
		  'codigo_grupos_exposicao_risco' => 22024,
		  'codigo_usuario_inclusao' => 67531,
		  'data_inclusao' => '23/05/2018 16:43:33',
		  'controle' => NULL,
		  'epi_eficaz' => NULL,
		),
		array(
		  'codigo' => 7,
		  'codigo_grupos_exposicao_risco_epc' => 23,
		  'codigo_epc' => 2,
		  'codigo_grupos_exposicao_risco' => 22025,
		  'codigo_usuario_inclusao' => 67531,
		  'data_inclusao' => '24/05/2018 08:38:39',
		  'controle' => NULL,
		  'epi_eficaz' => NULL,
		),
		array(
		  'codigo' => 8,
		  'codigo_grupos_exposicao_risco_epc' => 24,
		  'codigo_epc' => 2,
		  'codigo_grupos_exposicao_risco' => 22025,
		  'codigo_usuario_inclusao' => 67531,
		  'data_inclusao' => '24/05/2018 09:18:40',
		  'controle' => NULL,
		  'epi_eficaz' => NULL,
		),
		array(
		  'codigo' => 9,
		  'codigo_grupos_exposicao_risco_epc' => 25,
		  'codigo_epc' => 2,
		  'codigo_grupos_exposicao_risco' => 22025,
		  'codigo_usuario_inclusao' => 67531,
		  'data_inclusao' => '24/05/2018 09:20:04',
		  'controle' => NULL,
		  'epi_eficaz' => NULL,
		),
		array(
		  'codigo' => 10,
		  'codigo_grupos_exposicao_risco_epc' => 26,
		  'codigo_epc' => 2,
		  'codigo_grupos_exposicao_risco' => 22025,
		  'codigo_usuario_inclusao' => 67531,
		  'data_inclusao' => '24/05/2018 09:21:24',
		  'controle' => NULL,
		  'epi_eficaz' => NULL,
		), 
	);

}
?>