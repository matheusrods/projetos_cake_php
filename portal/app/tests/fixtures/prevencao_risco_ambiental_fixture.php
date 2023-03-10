<?php
class PrevencaoRiscoAmbientalFixture extends CakeTestFixture {
var $name = 'PrevencaoRiscoAmbiental';
var $table = 'prevencao_riscos_ambientais';

	var $fields = array(
		'status' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
		'codigo' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_setor' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_grupo_prevencao_risco_ambiental' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'data_inicial' => array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'data_final' => array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'acao' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
		'periodos_previsao_conclusao' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 26, ),
		'responsavel' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
	);

	var $records = array(
		array(
		  'status' => 1,
		  'codigo' => 3,
		  'codigo_setor' => 4,
		  'codigo_usuario_inclusao' => 61923,
		  'codigo_empresa' => 1,
		  'codigo_grupo_prevencao_risco_ambiental' => 3,
		  'data_inclusao' => '03/03/2017 09:03:19',
		  'data_inicial' => '01/03/2017 00:00:00',
		  'data_final' => '04/03/2017 00:00:00',
		  'acao' => 'teste',
		  'periodos_previsao_conclusao' => NULL,
		  'responsavel' => 'Teste',
		), 
		array(
		  'status' => 1,
		  'codigo' => 4,
		  'codigo_setor' => 226,
		  'codigo_usuario_inclusao' => 61650,
		  'codigo_empresa' => 1,
		  'codigo_grupo_prevencao_risco_ambiental' => 2,
		  'data_inclusao' => '03/03/2017 15:11:18',
		  'data_inicial' => '01/03/2017 00:00:00',
		  'data_final' => '31/03/2017 00:00:00',
		  'acao' => 'Revis??o global do PPRA',
		  'periodos_previsao_conclusao' => NULL,
		  'responsavel' => 'GERAL',
		), 
		array(
		  'status' => 1,
		  'codigo' => 5,
		  'codigo_setor' => 226,
		  'codigo_usuario_inclusao' => 61650,
		  'codigo_empresa' => 1,
		  'codigo_grupo_prevencao_risco_ambiental' => 2,
		  'data_inclusao' => '03/03/2017 15:11:18',
		  'data_inicial' => '01/02/2017 00:00:00',
		  'data_final' => '31/01/2018 00:00:00',
		  'acao' => 'Divulga????o dos dados do PPRA aos colaboradores',
		  'periodos_previsao_conclusao' => NULL,
		  'responsavel' => 'GERAL',
		), 
		array(
		  'status' => 1,
		  'codigo' => 6,
		  'codigo_setor' => 335,
		  'codigo_usuario_inclusao' => 61650,
		  'codigo_empresa' => 1,
		  'codigo_grupo_prevencao_risco_ambiental' => 4,
		  'data_inclusao' => '23/03/2017 18:11:55',
		  'data_inicial' => '01/05/2017 00:00:00',
		  'data_final' => '31/05/2017 00:00:00',
		  'acao' => 'A????o Teste',
		  'periodos_previsao_conclusao' => NULL,
		  'responsavel' => 'RH',
		), 
		array(
		  'status' => 1,
		  'codigo' => 7,
		  'codigo_setor' => 336,
		  'codigo_usuario_inclusao' => 61650,
		  'codigo_empresa' => 1,
		  'codigo_grupo_prevencao_risco_ambiental' => 4,
		  'data_inclusao' => '23/03/2017 18:11:55',
		  'data_inicial' => '16/06/2017 00:00:00',
		  'data_final' => '31/08/2017 00:00:00',
		  'acao' => ' ',
		  'periodos_previsao_conclusao' => NULL,
		  'responsavel' => 'RH',
		), 
		array(
		  'status' => 1,
		  'codigo' => 17,
		  'codigo_setor' => 685,
		  'codigo_usuario_inclusao' => 61923,
		  'codigo_empresa' => 1,
		  'codigo_grupo_prevencao_risco_ambiental' => 12,
		  'data_inclusao' => '15/05/2017 12:01:57',
		  'data_inicial' => '15/05/2017 00:00:00',
		  'data_final' => '31/05/2017 00:00:00',
		  'acao' => 'teste',
		  'periodos_previsao_conclusao' => NULL,
		  'responsavel' => 'teste',
		), 
		array(
		  'status' => 1,
		  'codigo' => 33,
		  'codigo_setor' => 2895,
		  'codigo_usuario_inclusao' => 61802,
		  'codigo_empresa' => 1,
		  'codigo_grupo_prevencao_risco_ambiental' => 13,
		  'data_inclusao' => '16/05/2017 16:46:14',
		  'data_inicial' => '13/05/2017 00:00:00',
		  'data_final' => '13/07/2017 00:00:00',
		  'acao' => 'Divulga????o dos dados do PPRA aos colaboradores',
		  'periodos_previsao_conclusao' => NULL,
		  'responsavel' => 'Respons??vel da unidade',
		), 
		array(
		  'status' => 1,
		  'codigo' => 34,
		  'codigo_setor' => 2895,
		  'codigo_usuario_inclusao' => 61802,
		  'codigo_empresa' => 1,
		  'codigo_grupo_prevencao_risco_ambiental' => 13,
		  'data_inclusao' => '16/05/2017 16:46:14',
		  'data_inicial' => '13/06/2017 00:00:00',
		  'data_final' => '12/07/2017 00:00:00',
		  'acao' => 'Promover treinamento anual para designado da CIPA de acordo com item 5.32.2 da NR - 05.',
		  'periodos_previsao_conclusao' => NULL,
		  'responsavel' => 'Respons??vel da unidade',
		), 
		array(
		  'status' => 1,
		  'codigo' => 35,
		  'codigo_setor' => 2895,
		  'codigo_usuario_inclusao' => 61802,
		  'codigo_empresa' => 1,
		  'codigo_grupo_prevencao_risco_ambiental' => 13,
		  'data_inclusao' => '16/05/2017 16:46:14',
		  'data_inicial' => '13/09/2017 00:00:00',
		  'data_final' => '12/10/2017 00:00:00',
		  'acao' => 'Realizar avalia????o ergonomica dos postos de trabalho',
		  'periodos_previsao_conclusao' => NULL,
		  'responsavel' => 'Respons??vel da unidade',
		), 
		array(
		  'status' => 1,
		  'codigo' => 36,
		  'codigo_setor' => 2895,
		  'codigo_usuario_inclusao' => 61802,
		  'codigo_empresa' => 1,
		  'codigo_grupo_prevencao_risco_ambiental' => 13,
		  'data_inclusao' => '16/05/2017 16:46:14',
		  'data_inicial' => '13/04/2018 00:00:00',
		  'data_final' => '12/05/2018 00:00:00',
		  'acao' => 'Reavalia????o do PPRA.',
		  'periodos_previsao_conclusao' => NULL,
		  'responsavel' => 'Respons??vel da unidade',
		), 
	);

}