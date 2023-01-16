<?php
class GrupoExposicaoVersoesFixture extends CakeTestFixture {
	var $name = 'GrupoExposicaoVersoes';
	var $table = 'grupo_exposicao_versoes';

	var $fields = array(
		'codigo' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_grupo_exposicao' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_cargo' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo_empresa' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'descricao_atividade' => array('type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'data_documento' => array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'observacao' => array('type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo_cliente_setor' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_grupo_homogeneo' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_funcionario' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'medidas_controle' => array('type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'funcionario_entrevistado' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inicio_vigencia' => array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo_medico' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_ppra_versoes' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'funcionario_entrevistado_terceiro' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
	);

}