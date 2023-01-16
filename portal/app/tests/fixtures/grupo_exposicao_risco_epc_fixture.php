<?php
class GrupoExposicaoRiscoEpcFixture extends CakeTestFixture {

	var $name = 'GrupoExposicaoRiscoEpc';
	var $table = 'grupos_exposicao_risco_epc';

	var $fields = array( 
		'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_epc' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_grupos_exposicao_risco' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo_usuario_inclusao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'controle' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 1, ),
		'epi_eficaz' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
	);

	var $records = array(
		array(
			'codigo' => 1620,
			'codigo_epc' => 1,
			'codigo_grupos_exposicao_risco' => 1291,
			'data_inclusao' => '2016-07-27 14:13:57',
			'codigo_usuario_inclusao' => 61608,
			'controle' => NULL,
			'epi_eficaz' => NULL,
		),
		array(
			'codigo' => 3573,
			'codigo_epc' => 3,
			'codigo_grupos_exposicao_risco' => 2357,
			'data_inclusao' => '2016-08-08 09:57:48',
			'codigo_usuario_inclusao' => 2,
			'controle' => '1',
			'epi_eficaz' => NULL,
		),
		array(
			'codigo' => 3696,
			'codigo_epc' => 1,
			'codigo_grupos_exposicao_risco' => 2587,
			'data_inclusao' => '2016-08-17 09:05:26',
			'codigo_usuario_inclusao' => 61648,
			'controle' => NULL,
			'epi_eficaz' => NULL,
		),
		array(
			'codigo' => 3697,
			'codigo_epc' => 3,
			'codigo_grupos_exposicao_risco' => 2588,
			'data_inclusao' => '2016-08-17 09:07:01',
			'codigo_usuario_inclusao' => 61648,
			'controle' => NULL,
			'epi_eficaz' => NULL,
		),
		array(
			'codigo' => 3698,
			'codigo_epc' => 3,
			'codigo_grupos_exposicao_risco' => 2589,
			'data_inclusao' => '2016-08-17 09:07:01',
			'codigo_usuario_inclusao' => 61648,
			'controle' => NULL,
			'epi_eficaz' => NULL,
		),
		array(
			'codigo' => 3699,
			'codigo_epc' => 2,
			'codigo_grupos_exposicao_risco' => 2590,
			'data_inclusao' => '2016-08-17 09:09:03',
			'codigo_usuario_inclusao' => 61648,
			'controle' => '1',
			'epi_eficaz' => NULL,
		),
		array(
			'codigo' => 3700,
			'codigo_epc' => 2,
			'codigo_grupos_exposicao_risco' => 2591,
			'data_inclusao' => '2016-08-17 13:17:27',
			'codigo_usuario_inclusao' => 61608,
			'controle' => NULL,
			'epi_eficaz' => NULL,
		),
		array(
			'codigo' => 3701,
			'codigo_epc' => 4,
			'codigo_grupos_exposicao_risco' => 2593,
			'data_inclusao' => '2016-08-25 16:02:39',
			'codigo_usuario_inclusao' => 61608,
			'controle' => NULL,
			'epi_eficaz' => NULL,
		),
		array(
			'codigo' => 3703,
			'codigo_epc' => 3,
			'codigo_grupos_exposicao_risco' => 2600,
			'data_inclusao' => '2016-08-26 12:31:43',
			'codigo_usuario_inclusao' => 66984,
			'controle' => '1',
			'epi_eficaz' => NULL,
		),
		array(
			'codigo' => 3777,
			'codigo_epc' => 3,
			'codigo_grupos_exposicao_risco' => 2631,
			'data_inclusao' => '2016-08-31 11:51:28',
			'codigo_usuario_inclusao' => 2,
			'controle' => NULL,
			'epi_eficaz' => NULL,
		),
		array(
			'codigo' => 3778,
			'codigo_epc' => 7,
			'codigo_grupos_exposicao_risco' => 21339,
			'data_inclusao' => '2018-01-01 00:00:00',
			'codigo_usuario_inclusao' => 67111,
			'controle' => NULL,
			'epi_eficaz' => NULL,
		),
	);

}