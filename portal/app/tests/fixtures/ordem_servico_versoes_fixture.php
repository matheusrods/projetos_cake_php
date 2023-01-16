<?php
class OrdemServicoVersoesFixture extends CakeTestFixture {
	var $name = 'OrdemServicoVersoes';
	var $table = 'ordem_servico_versoes';

	var $fields = array( 
		'codigo' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_ordem_servico' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_ppra_versoes' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_pcmso_versoes' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_grupo_economico' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_cliente' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_fornecedor' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 2, ),
		'status_ordem_servico' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo_usuario_inclusao' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'inicio_vigencia_pcmso' => array('type' => 'date', 'null' => true, 'default' => '', 'length' => NULL, ),
		'vigencia_em_meses' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	);
	
}