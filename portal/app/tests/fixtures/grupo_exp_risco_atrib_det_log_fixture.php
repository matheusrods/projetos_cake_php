<?php
class GrupoExpRiscoAtribDetLogFixture extends CakeTestFixture {

	var $name = 'GrupoExpRiscoAtribDetLog';
	var $table = 'grupo_exposicao_riscos_atributos_detalhes_log';

	var $fields = array( 
		'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_grupo_exposicao_riscos_atributos_detalhes' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_riscos_atributos_detalhes' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_grupos_exposicao_risco' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
	);

	var $records = array( 
		array(
		  'codigo' => 1,
		  'codigo_grupo_exposicao_riscos_atributos_detalhes' => 106,
		  'codigo_riscos_atributos_detalhes' => 7,
		  'codigo_grupos_exposicao_risco' => 39010,
		  'codigo_usuario_inclusao' => 65964,
		  'data_inclusao' => '08/06/2018 08:01:05',
		), 
		array(
		  'codigo' => 2,
		  'codigo_grupo_exposicao_riscos_atributos_detalhes' => 107,
		  'codigo_riscos_atributos_detalhes' => 8,
		  'codigo_grupos_exposicao_risco' => 49468,
		  'codigo_usuario_inclusao' => 61802,
		  'data_inclusao' => '09/08/2018 16:45:06',
		), 
		array(
		  'codigo' => 3,
		  'codigo_grupo_exposicao_riscos_atributos_detalhes' => 108,
		  'codigo_riscos_atributos_detalhes' => 8,
		  'codigo_grupos_exposicao_risco' => 8087,
		  'codigo_usuario_inclusao' => 67117,
		  'data_inclusao' => '22/08/2018 16:51:02',
		), 
	);

}
?>