<?php
class GrupoExpRiscoAtribDetVersFixture extends CakeTestFixture {
	var $name = 'GrupoExpRiscoAtribDetVers';
	var $table = 'grupo_exposicao_riscos_atributos_detalhes_versoes';

	var $fields = array(
		'codigo' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_grupo_exposicao_riscos_atributos_detalhes' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_riscos_atributos_detalhes' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_grupos_exposicao_risco' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo_ppra_versoes' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	);

}