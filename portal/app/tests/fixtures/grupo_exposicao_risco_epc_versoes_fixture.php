<?php
class GrupoExposicaoRiscoEpcVersoesFixture extends CakeTestFixture {
	var $name = 'GrupoExposicaoRiscoEpcVersoes';
	var $table = 'grupo_exposicao_risco_epc_versoes';

	var $fields = array(
		'codigo' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_grupo_exposicao_risco_epc' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_epc' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_grupos_exposicao_risco' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo_usuario_inclusao' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'controle' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 1, ),
		'epi_eficaz' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
		'codigo_ppra_versoes' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	);

}