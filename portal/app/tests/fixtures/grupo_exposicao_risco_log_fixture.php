<?php
class GrupoExposicaoRiscoLogFixture extends CakeTestFixture {

	var $name = 'GrupoExposicaoRiscoLog';
	var $table = 'grupos_exposicao_risco_log';

	var $fields = array( 
		'medidas_controle' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'medidas_controle_recomendada' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'dosimetria' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
		'avaliacao_instantanea' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
		'descanso_no_local' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
		'carga_solar' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
		'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_grupos_exposicao_risco' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_grupo_exposicao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_risco' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'descanso_tbs' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'descanso_tg' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'trabalho_tbn' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'trabalho_tbs' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'trabalho_tg' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_risco_atributo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_tecnica_medicao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'minutos_tempo_exposicao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'jornada_tempo_exposicao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'descanso_tempo_exposicao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_efeito_critico' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'descanso_tbn' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'tempo_exposicao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'intensidade' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'resultante' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'dano' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'grau_risco' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_tipo_medicao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'meio_propagacao' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 25, ),
		'valor_maximo' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 25, ),
		'valor_medido' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 25, ),
	);
	
}