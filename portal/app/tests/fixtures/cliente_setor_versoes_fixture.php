<?php
class ClienteSetorVersoesFixture extends CakeTestFixture {
	var $name = 'ClienteSetorVersoes';
	var $table = 'clientes_setores_versoes';

	var $fields = array(
		'codigo' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_clientes_setores' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_cliente' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_setor' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo_usuario_inclusao' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'pe_direito' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'cobertura' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'iluminacao' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'ventilacao' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'piso' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'estrutura' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_cliente_alocacao' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_ppra_versoes' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	);

}