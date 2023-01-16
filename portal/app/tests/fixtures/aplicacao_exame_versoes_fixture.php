<?php
class AplicacaoExameVersoesFixture extends CakeTestFixture {
	var $name = 'AplicacaoExameVersoes';
	var $table = 'aplicacao_exames_versoes';

	var $fields = array(
		'codigo' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_aplicacao_exames' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_cliente' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_setor' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_cargo' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_exame' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'periodo_meses' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 5, ),
		'periodo_apos_demissao' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 5, ),
		'exame_admissional' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'exame_periodico' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'exame_demissional' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'exame_retorno' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'exame_mudanca' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'periodo_idade' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 5, ),
		'qtd_periodo_idade' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 5, ),
		'exame_excluido_convocacao' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'exame_excluido_ppp' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'exame_excluido_aso' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'exame_excluido_pcmso' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'exame_excluido_anual' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo_usuario_inclusao' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'ativo' => array('type' => 'boolean', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo_empresa' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_grupo_exposicao_risco' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_grupo_exposicao' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'qualidade_vida' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_tipo_exame' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'pontual' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'periodo_idade_2' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 1, ),
		'qtd_periodo_idade_2' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 1, ),
		'periodo_idade_3' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 5, ),
		'qtd_periodo_idade_3' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 5, ),
		'periodo_idade_4' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 5, ),
		'qtd_periodo_idade_4' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 5, ),		
		'codigo_cliente_alocacao' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_pcmso_versoes' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	);

}