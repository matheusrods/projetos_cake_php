<?php
class PrevencaoRiscoAmbientalVersoesFixture extends CakeTestFixture {
	var $name = 'PrevencaoRiscoAmbientalVersoes';
	var $table = 'prevencao_riscos_ambientais_versoes';

	var $fields = array(
		'codigo' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_prevencao_riscos_ambientais' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_setor' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo_usuario_inclusao' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'acao' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 1, ),
		'periodos_previsao_conclusao' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 1, ),
		'responsavel' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 1, ),
		'status' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
		'data_inicial' => array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'data_final' => array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo_grupo_prevencao_risco_ambiental' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_ppra_versoes' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	);

}