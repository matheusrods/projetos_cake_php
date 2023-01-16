<?php
class GrupoExposicaoRiscoEpiLogFixture extends CakeTestFixture {

	var $name = 'GrupoExposicaoRiscoEpiLog';
	var $table = 'grupos_exposicao_risco_epi_log';

	var $fields = array( 
		'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_grupos_exposicao_risco_epi' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_epi' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_grupos_exposicao_risco' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'controle' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 1, ),
		'epi_eficaz' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
		'numero_ca' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_validade_ca' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'atenuacao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	);

	var $records = array( 
		array(
		  'codigo' => 1,
		  'codigo_grupos_exposicao_risco_epi' => 614,
		  'codigo_epi' => 20,
		  'codigo_grupos_exposicao_risco' => 11198,
		  'codigo_usuario_inclusao' => 63045,
		  'data_inclusao' => '22/03/2018 14:07:49',
		  'controle' => '1',
		  'epi_eficaz' => 1,
		  'numero_ca' => 6659,
		  'data_validade_ca' => '30/09/2019 00:00:00',
		  'atenuacao' => NULL,
		),
		array(
		  'codigo' => 2,
		  'codigo_grupos_exposicao_risco_epi' => 615,
		  'codigo_epi' => 20,
		  'codigo_grupos_exposicao_risco' => 11199,
		  'codigo_usuario_inclusao' => 63045,
		  'data_inclusao' => '22/03/2018 14:07:49',
		  'controle' => '1',
		  'epi_eficaz' => 1,
		  'numero_ca' => 6659,
		  'data_validade_ca' => '30/09/2019 00:00:00',
		  'atenuacao' => NULL,
		),
		array(
		  'codigo' => 3,
		  'codigo_grupos_exposicao_risco_epi' => 616,
		  'codigo_epi' => 21,
		  'codigo_grupos_exposicao_risco' => 20603,
		  'codigo_usuario_inclusao' => 65964,
		  'data_inclusao' => '13/04/2018 07:59:27',
		  'controle' => '1',
		  'epi_eficaz' => 1,
		  'numero_ca' => 5745,
		  'data_validade_ca' => '06/11/2022 00:00:00',
		  'atenuacao' => 18,
		),
		array(
		  'codigo' => 4,
		  'codigo_grupos_exposicao_risco_epi' => 617,
		  'codigo_epi' => 21,
		  'codigo_grupos_exposicao_risco' => 20603,
		  'codigo_usuario_inclusao' => 65964,
		  'data_inclusao' => '13/04/2018 08:03:26',
		  'controle' => '1',
		  'epi_eficaz' => 1,
		  'numero_ca' => 5745,
		  'data_validade_ca' => '06/11/2022 00:00:00',
		  'atenuacao' => 18,
		),
		array(
		  'codigo' => 5,
		  'codigo_grupos_exposicao_risco_epi' => 618,
		  'codigo_epi' => 21,
		  'codigo_grupos_exposicao_risco' => 20603,
		  'codigo_usuario_inclusao' => 65964,
		  'data_inclusao' => '13/04/2018 08:09:26',
		  'controle' => '1',
		  'epi_eficaz' => 1,
		  'numero_ca' => 5745,
		  'data_validade_ca' => '06/11/2022 00:00:00',
		  'atenuacao' => 18,
		),
		array(
		  'codigo' => 6,
		  'codigo_grupos_exposicao_risco_epi' => 619,
		  'codigo_epi' => 19,
		  'codigo_grupos_exposicao_risco' => 20604,
		  'codigo_usuario_inclusao' => 65964,
		  'data_inclusao' => '13/04/2018 08:09:26',
		  'controle' => '1',
		  'epi_eficaz' => 1,
		  'numero_ca' => 6110,
		  'data_validade_ca' => '24/03/2020 00:00:00',
		  'atenuacao' => NULL,
		),
		array(
		  'codigo' => 7,
		  'codigo_grupos_exposicao_risco_epi' => 620,
		  'codigo_epi' => 21,
		  'codigo_grupos_exposicao_risco' => 20603,
		  'codigo_usuario_inclusao' => 65964,
		  'data_inclusao' => '13/04/2018 08:10:40',
		  'controle' => '1',
		  'epi_eficaz' => 1,
		  'numero_ca' => 5745,
		  'data_validade_ca' => '06/11/2022 00:00:00',
		  'atenuacao' => 18,
		),
		array(
		  'codigo' => 8,
		  'codigo_grupos_exposicao_risco_epi' => 621,
		  'codigo_epi' => 19,
		  'codigo_grupos_exposicao_risco' => 20604,
		  'codigo_usuario_inclusao' => 65964,
		  'data_inclusao' => '13/04/2018 08:10:40',
		  'controle' => '1',
		  'epi_eficaz' => 1,
		  'numero_ca' => 6110,
		  'data_validade_ca' => '24/03/2020 00:00:00',
		  'atenuacao' => NULL,
		),
		array(
		  'codigo' => 9,
		  'codigo_grupos_exposicao_risco_epi' => 622,
		  'codigo_epi' => 21,
		  'codigo_grupos_exposicao_risco' => 20729,
		  'codigo_usuario_inclusao' => 65964,
		  'data_inclusao' => '13/04/2018 09:40:48',
		  'controle' => '1',
		  'epi_eficaz' => 1,
		  'numero_ca' => 5745,
		  'data_validade_ca' => '06/11/2022 00:00:00',
		  'atenuacao' => 18,
		),
		array(
		  'codigo' => 10,
		  'codigo_grupos_exposicao_risco_epi' => 623,
		  'codigo_epi' => 21,
		  'codigo_grupos_exposicao_risco' => 20731,
		  'codigo_usuario_inclusao' => 65964,
		  'data_inclusao' => '13/04/2018 09:40:48',
		  'controle' => '1',
		  'epi_eficaz' => 1,
		  'numero_ca' => 5745,
		  'data_validade_ca' => '06/11/2022 00:00:00',
		  'atenuacao' => 18,
		), 
	);

}
?>