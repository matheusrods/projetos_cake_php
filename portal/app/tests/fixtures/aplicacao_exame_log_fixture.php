<?php
class AplicacaoExameLogFixture extends CakeTestFixture {
  
  var $name = 'AplicacaoExameLog';
  var $table = 'aplicacao_exames_log';

  var $fields = array( 
    'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
    'codigo_aplicacao_exames' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'codigo_cliente' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'codigo_setor' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'codigo_cargo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'codigo_exame' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'periodo_meses' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 5, ),
    'periodo_apos_demissao' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 5, ),
    'exame_admissional' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'exame_periodico' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'exame_demissional' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'exame_retorno' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'exame_mudanca' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'periodo_idade' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 5, ),
    'qtd_periodo_idade' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 5, ),
    'exame_excluido_convocacao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'exame_excluido_ppp' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'exame_excluido_aso' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'exame_excluido_pcmso' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'exame_excluido_anual' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
    'codigo_usuario_inclusao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'ativo' => array( 'type' => 'boolean', 'null' => true, 'default' => '', 'length' => NULL, ),
    'codigo_empresa' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'codigo_grupo_exposicao_risco' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'codigo_grupo_exposicao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'qualidade_vida' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'codigo_tipo_exame' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'pontual' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'periodo_idade_2' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 5, ),
    'qtd_periodo_idade_2' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 5, ),
    'periodo_idade_3' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 5, ),
    'qtd_periodo_idade_3' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 5, ),
    'periodo_idade_4' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 5, ),
    'qtd_periodo_idade_4' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 5, ),
    'codigo_cliente_alocacao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'codigo_usuario_alteracao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'data_alteracao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
    'acao_sistema' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
  );

  var $records = array( 
    array(
      'codigo' => 1,
      'codigo_aplicacao_exames' => 20495,
      'codigo_cliente' => 51227,
      'codigo_setor' => 4307,
      'codigo_cargo' => 5302,
      'codigo_exame' => 11,
      'periodo_meses' => '12',
      'periodo_apos_demissao' => ' ',
      'exame_admissional' => 1,
      'exame_periodico' => 1,
      'exame_demissional' => 1,
      'exame_retorno' => 1,
      'exame_mudanca' => 1,
      'periodo_idade' => ' ',
      'qtd_periodo_idade' => ' ',
      'exame_excluido_convocacao' => 1,
      'exame_excluido_ppp' => 0,
      'exame_excluido_aso' => 1,
      'exame_excluido_pcmso' => 1,
      'exame_excluido_anual' => 1,
      'data_inclusao' => '26/02/2018 14:04:44',
      'codigo_usuario_inclusao' => 63033,
      'ativo' => 1,
      'codigo_empresa' => 1,
      'codigo_grupo_exposicao_risco' => NULL,
      'codigo_grupo_exposicao' => NULL,
      'qualidade_vida' => NULL,
      'codigo_tipo_exame' => 1,
      'pontual' => NULL,
      'periodo_idade_2' => ' ',
      'qtd_periodo_idade_2' => ' ',
      'periodo_idade_3' => ' ',
      'qtd_periodo_idade_3' => ' ',
      'periodo_idade_4' => ' ',
      'qtd_periodo_idade_4' => ' ',
      'codigo_cliente_alocacao' => 51227,
      'codigo_usuario_alteracao' => NULL,
      'data_alteracao' => NULL,
      'acao_sistema' => NULL,
    ),
    array(
      'codigo' => 2,
      'codigo_aplicacao_exames' => 20494,
      'codigo_cliente' => 51227,
      'codigo_setor' => 4307,
      'codigo_cargo' => 5302,
      'codigo_exame' => 52,
      'periodo_meses' => '12',
      'periodo_apos_demissao' => ' ',
      'exame_admissional' => 1,
      'exame_periodico' => 1,
      'exame_demissional' => 1,
      'exame_retorno' => 1,
      'exame_mudanca' => 1,
      'periodo_idade' => ' ',
      'qtd_periodo_idade' => ' ',
      'exame_excluido_convocacao' => 1,
      'exame_excluido_ppp' => 0,
      'exame_excluido_aso' => 1,
      'exame_excluido_pcmso' => 1,
      'exame_excluido_anual' => 1,
      'data_inclusao' => '26/02/2018 14:04:44',
      'codigo_usuario_inclusao' => 63033,
      'ativo' => 1,
      'codigo_empresa' => 1,
      'codigo_grupo_exposicao_risco' => NULL,
      'codigo_grupo_exposicao' => NULL,
      'qualidade_vida' => NULL,
      'codigo_tipo_exame' => 1,
      'pontual' => NULL,
      'periodo_idade_2' => ' ',
      'qtd_periodo_idade_2' => ' ',
      'periodo_idade_3' => ' ',
      'qtd_periodo_idade_3' => ' ',
      'periodo_idade_4' => ' ',
      'qtd_periodo_idade_4' => ' ',
      'codigo_cliente_alocacao' => 51227,
      'codigo_usuario_alteracao' => NULL,
      'data_alteracao' => NULL,
      'acao_sistema' => NULL,
    ),
    array(
      'codigo' => 3,
      'codigo_aplicacao_exames' => 20497,
      'codigo_cliente' => 51227,
      'codigo_setor' => 4307,
      'codigo_cargo' => 5301,
      'codigo_exame' => 11,
      'periodo_meses' => '12',
      'periodo_apos_demissao' => ' ',
      'exame_admissional' => 1,
      'exame_periodico' => 1,
      'exame_demissional' => 1,
      'exame_retorno' => 1,
      'exame_mudanca' => 1,
      'periodo_idade' => ' ',
      'qtd_periodo_idade' => ' ',
      'exame_excluido_convocacao' => 1,
      'exame_excluido_ppp' => 0,
      'exame_excluido_aso' => 1,
      'exame_excluido_pcmso' => 1,
      'exame_excluido_anual' => 1,
      'data_inclusao' => '26/02/2018 14:04:57',
      'codigo_usuario_inclusao' => 63033,
      'ativo' => 1,
      'codigo_empresa' => 1,
      'codigo_grupo_exposicao_risco' => NULL,
      'codigo_grupo_exposicao' => NULL,
      'qualidade_vida' => NULL,
      'codigo_tipo_exame' => 1,
      'pontual' => NULL,
      'periodo_idade_2' => ' ',
      'qtd_periodo_idade_2' => ' ',
      'periodo_idade_3' => ' ',
      'qtd_periodo_idade_3' => ' ',
      'periodo_idade_4' => ' ',
      'qtd_periodo_idade_4' => ' ',
      'codigo_cliente_alocacao' => 51227,
      'codigo_usuario_alteracao' => NULL,
      'data_alteracao' => NULL,
      'acao_sistema' => NULL,
    ),
    array(
      'codigo' => 4,
      'codigo_aplicacao_exames' => 20496,
      'codigo_cliente' => 51227,
      'codigo_setor' => 4307,
      'codigo_cargo' => 5301,
      'codigo_exame' => 52,
      'periodo_meses' => '12',
      'periodo_apos_demissao' => ' ',
      'exame_admissional' => 1,
      'exame_periodico' => 1,
      'exame_demissional' => 1,
      'exame_retorno' => 1,
      'exame_mudanca' => 1,
      'periodo_idade' => ' ',
      'qtd_periodo_idade' => ' ',
      'exame_excluido_convocacao' => 1,
      'exame_excluido_ppp' => 0,
      'exame_excluido_aso' => 1,
      'exame_excluido_pcmso' => 1,
      'exame_excluido_anual' => 1,
      'data_inclusao' => '26/02/2018 14:04:57',
      'codigo_usuario_inclusao' => 63033,
      'ativo' => 1,
      'codigo_empresa' => 1,
      'codigo_grupo_exposicao_risco' => NULL,
      'codigo_grupo_exposicao' => NULL,
      'qualidade_vida' => NULL,
      'codigo_tipo_exame' => 1,
      'pontual' => NULL,
      'periodo_idade_2' => ' ',
      'qtd_periodo_idade_2' => ' ',
      'periodo_idade_3' => ' ',
      'qtd_periodo_idade_3' => ' ',
      'periodo_idade_4' => ' ',
      'qtd_periodo_idade_4' => ' ',
      'codigo_cliente_alocacao' => 51227,
      'codigo_usuario_alteracao' => NULL,
      'data_alteracao' => NULL,
      'acao_sistema' => NULL,
    ),
    array(
      'codigo' => 5,
      'codigo_aplicacao_exames' => 20498,
      'codigo_cliente' => 51227,
      'codigo_setor' => 4307,
      'codigo_cargo' => 5303,
      'codigo_exame' => 52,
      'periodo_meses' => ' ',
      'periodo_apos_demissao' => ' ',
      'exame_admissional' => 1,
      'exame_periodico' => 1,
      'exame_demissional' => 1,
      'exame_retorno' => 1,
      'exame_mudanca' => 1,
      'periodo_idade' => '1',
      'qtd_periodo_idade' => '12',
      'exame_excluido_convocacao' => 1,
      'exame_excluido_ppp' => 0,
      'exame_excluido_aso' => 1,
      'exame_excluido_pcmso' => 1,
      'exame_excluido_anual' => 1,
      'data_inclusao' => '26/02/2018 14:05:16',
      'codigo_usuario_inclusao' => 63033,
      'ativo' => 1,
      'codigo_empresa' => 1,
      'codigo_grupo_exposicao_risco' => NULL,
      'codigo_grupo_exposicao' => NULL,
      'qualidade_vida' => NULL,
      'codigo_tipo_exame' => 1,
      'pontual' => NULL,
      'periodo_idade_2' => '19',
      'qtd_periodo_idade_2' => '24',
      'periodo_idade_3' => '45',
      'qtd_periodo_idade_3' => '12',
      'periodo_idade_4' => ' ',
      'qtd_periodo_idade_4' => ' ',
      'codigo_cliente_alocacao' => 51227,
      'codigo_usuario_alteracao' => NULL,
      'data_alteracao' => NULL,
      'acao_sistema' => NULL,
    ),
    array(
      'codigo' => 6,
      'codigo_aplicacao_exames' => 20500,
      'codigo_cliente' => 51227,
      'codigo_setor' => 4308,
      'codigo_cargo' => 5313,
      'codigo_exame' => 11,
      'periodo_meses' => '12',
      'periodo_apos_demissao' => ' ',
      'exame_admissional' => 1,
      'exame_periodico' => 1,
      'exame_demissional' => 1,
      'exame_retorno' => 1,
      'exame_mudanca' => 1,
      'periodo_idade' => ' ',
      'qtd_periodo_idade' => ' ',
      'exame_excluido_convocacao' => 1,
      'exame_excluido_ppp' => 0,
      'exame_excluido_aso' => 1,
      'exame_excluido_pcmso' => 1,
      'exame_excluido_anual' => 1,
      'data_inclusao' => '26/02/2018 14:06:24',
      'codigo_usuario_inclusao' => 63033,
      'ativo' => 1,
      'codigo_empresa' => 1,
      'codigo_grupo_exposicao_risco' => NULL,
      'codigo_grupo_exposicao' => NULL,
      'qualidade_vida' => NULL,
      'codigo_tipo_exame' => 1,
      'pontual' => NULL,
      'periodo_idade_2' => ' ',
      'qtd_periodo_idade_2' => ' ',
      'periodo_idade_3' => ' ',
      'qtd_periodo_idade_3' => ' ',
      'periodo_idade_4' => ' ',
      'qtd_periodo_idade_4' => ' ',
      'codigo_cliente_alocacao' => 51227,
      'codigo_usuario_alteracao' => NULL,
      'data_alteracao' => NULL,
      'acao_sistema' => NULL,
    ),
    array(
      'codigo' => 7,
      'codigo_aplicacao_exames' => 20499,
      'codigo_cliente' => 51227,
      'codigo_setor' => 4308,
      'codigo_cargo' => 5313,
      'codigo_exame' => 52,
      'periodo_meses' => '12',
      'periodo_apos_demissao' => ' ',
      'exame_admissional' => 1,
      'exame_periodico' => 1,
      'exame_demissional' => 1,
      'exame_retorno' => 1,
      'exame_mudanca' => 1,
      'periodo_idade' => ' ',
      'qtd_periodo_idade' => ' ',
      'exame_excluido_convocacao' => 1,
      'exame_excluido_ppp' => 0,
      'exame_excluido_aso' => 1,
      'exame_excluido_pcmso' => 1,
      'exame_excluido_anual' => 1,
      'data_inclusao' => '26/02/2018 14:06:24',
      'codigo_usuario_inclusao' => 63033,
      'ativo' => 1,
      'codigo_empresa' => 1,
      'codigo_grupo_exposicao_risco' => NULL,
      'codigo_grupo_exposicao' => NULL,
      'qualidade_vida' => NULL,
      'codigo_tipo_exame' => 1,
      'pontual' => NULL,
      'periodo_idade_2' => ' ',
      'qtd_periodo_idade_2' => ' ',
      'periodo_idade_3' => ' ',
      'qtd_periodo_idade_3' => ' ',
      'periodo_idade_4' => ' ',
      'qtd_periodo_idade_4' => ' ',
      'codigo_cliente_alocacao' => 51227,
      'codigo_usuario_alteracao' => NULL,
      'data_alteracao' => NULL,
      'acao_sistema' => NULL,
    ),
    array(
      'codigo' => 8,
      'codigo_aplicacao_exames' => 20502,
      'codigo_cliente' => 51227,
      'codigo_setor' => 4308,
      'codigo_cargo' => 5312,
      'codigo_exame' => 11,
      'periodo_meses' => '12',
      'periodo_apos_demissao' => ' ',
      'exame_admissional' => 1,
      'exame_periodico' => 1,
      'exame_demissional' => 1,
      'exame_retorno' => 1,
      'exame_mudanca' => 1,
      'periodo_idade' => ' ',
      'qtd_periodo_idade' => ' ',
      'exame_excluido_convocacao' => 1,
      'exame_excluido_ppp' => 0,
      'exame_excluido_aso' => 1,
      'exame_excluido_pcmso' => 1,
      'exame_excluido_anual' => 1,
      'data_inclusao' => '26/02/2018 14:06:51',
      'codigo_usuario_inclusao' => 63033,
      'ativo' => 1,
      'codigo_empresa' => 1,
      'codigo_grupo_exposicao_risco' => NULL,
      'codigo_grupo_exposicao' => NULL,
      'qualidade_vida' => NULL,
      'codigo_tipo_exame' => 1,
      'pontual' => NULL,
      'periodo_idade_2' => ' ',
      'qtd_periodo_idade_2' => ' ',
      'periodo_idade_3' => ' ',
      'qtd_periodo_idade_3' => ' ',
      'periodo_idade_4' => ' ',
      'qtd_periodo_idade_4' => ' ',
      'codigo_cliente_alocacao' => 51227,
      'codigo_usuario_alteracao' => NULL,
      'data_alteracao' => NULL,
      'acao_sistema' => NULL,
    ),
    array(
      'codigo' => 9,
      'codigo_aplicacao_exames' => 20501,
      'codigo_cliente' => 51227,
      'codigo_setor' => 4308,
      'codigo_cargo' => 5312,
      'codigo_exame' => 52,
      'periodo_meses' => '12',
      'periodo_apos_demissao' => ' ',
      'exame_admissional' => 1,
      'exame_periodico' => 1,
      'exame_demissional' => 1,
      'exame_retorno' => 1,
      'exame_mudanca' => 1,
      'periodo_idade' => ' ',
      'qtd_periodo_idade' => ' ',
      'exame_excluido_convocacao' => 1,
      'exame_excluido_ppp' => 0,
      'exame_excluido_aso' => 1,
      'exame_excluido_pcmso' => 1,
      'exame_excluido_anual' => 1,
      'data_inclusao' => '26/02/2018 14:06:51',
      'codigo_usuario_inclusao' => 63033,
      'ativo' => 1,
      'codigo_empresa' => 1,
      'codigo_grupo_exposicao_risco' => NULL,
      'codigo_grupo_exposicao' => NULL,
      'qualidade_vida' => NULL,
      'codigo_tipo_exame' => 1,
      'pontual' => NULL,
      'periodo_idade_2' => ' ',
      'qtd_periodo_idade_2' => ' ',
      'periodo_idade_3' => ' ',
      'qtd_periodo_idade_3' => ' ',
      'periodo_idade_4' => ' ',
      'qtd_periodo_idade_4' => ' ',
      'codigo_cliente_alocacao' => 51227,
      'codigo_usuario_alteracao' => NULL,
      'data_alteracao' => NULL,
      'acao_sistema' => NULL,
    ),
    array(
      'codigo' => 10,
      'codigo_aplicacao_exames' => 20504,
      'codigo_cliente' => 51227,
      'codigo_setor' => 4308,
      'codigo_cargo' => 5316,
      'codigo_exame' => 11,
      'periodo_meses' => '12',
      'periodo_apos_demissao' => ' ',
      'exame_admissional' => 1,
      'exame_periodico' => 1,
      'exame_demissional' => 1,
      'exame_retorno' => 1,
      'exame_mudanca' => 1,
      'periodo_idade' => ' ',
      'qtd_periodo_idade' => ' ',
      'exame_excluido_convocacao' => 1,
      'exame_excluido_ppp' => 0,
      'exame_excluido_aso' => 1,
      'exame_excluido_pcmso' => 1,
      'exame_excluido_anual' => 1,
      'data_inclusao' => '26/02/2018 14:07:30',
      'codigo_usuario_inclusao' => 63033,
      'ativo' => 1,
      'codigo_empresa' => 1,
      'codigo_grupo_exposicao_risco' => NULL,
      'codigo_grupo_exposicao' => NULL,
      'qualidade_vida' => NULL,
      'codigo_tipo_exame' => 1,
      'pontual' => NULL,
      'periodo_idade_2' => ' ',
      'qtd_periodo_idade_2' => ' ',
      'periodo_idade_3' => ' ',
      'qtd_periodo_idade_3' => ' ',
      'periodo_idade_4' => ' ',
      'qtd_periodo_idade_4' => ' ',
      'codigo_cliente_alocacao' => 51227,
      'codigo_usuario_alteracao' => NULL,
      'data_alteracao' => NULL,
      'acao_sistema' => NULL,
    ),
  );

}
?>