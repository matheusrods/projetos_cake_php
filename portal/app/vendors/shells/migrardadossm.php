<?php

class MigrarDadossmShell extends Shell
{

	function main()
	{
		echo "**********************************************\n";
		echo "$ \n";
		echo "$ Estatísticas SM PgSQL \n";
		echo "$ \n";
		echo "**********************************************\n\n";
		echo "sincroniza: \n\n";
		echo "# importar_dados() \n";

		echo "\n";
	}

	function importar_dados()
	{

		$date = new DateTime();
		$this->last_year = $date->sub(date_interval_create_from_date_string('1 years'))->format("Y-m-d");

		$this->last_time_to_cleanup = $date->sub(date_interval_create_from_date_string('ago 15 minutes'))->format("Y-m-d H:i");

		$models = array(
						'TEviaEstaViagem',
						'TEvgdEstaViagemGeralDia',
						'TEvghEstaViagemGeralHora',
						'TEvsdEstaViagSeguDia',
						'TEvshEstaViagSeguHora',
						'TEvcdEstaViagCorrDia',
						'TEvchEstaViagCorrHora',
						'TEvedEstaViagEmbaDia',
						'TEvehEstaViagEmbaHora',
						'TEvtdEstaViagTranDia',
						'TEvthEstaViagTranHora',
						'TEvtdEstaViagTecnoDia',
						'TEvthEstaViagTecnoHora',
						'TEvodEstaViagOperaDia',
						'TEvohEstaViagOperaHora',
						);
		
		$this->TVusuViagemUsuario = ClassRegistry::init("TVusuViagemUsuario");
		if( $this->TVusuViagemUsuario->preparar() ){
			foreach($models as $model){
				$this->{$model} = ClassRegistry::init($model);
				call_user_func(array($this, 'limpar' . $model), $model);
				call_user_func(array($this, 'importar' . $model), $model);
			}
		}
	}

	// ####################################################################################
	// TEvthEstaViagTecnoHora Functions 
	// ####################################################################################		

	function limparTEvthEstaViagTecnoHora($model)
	{
		$sql = "DELETE FROM evth_estatistica_viagem_tecnologia_hora WHERE evth_data >= '" . date("Y-m-d H") . ":00'";
		$this->execute($sql, $model);
	}

	function importarTEvthEstaViagTecnoHora($model)
	{
		$sql = "INSERT INTO evth_estatistica_viagem_tecnologia_hora
				SELECT
				fato.evia_tecn_codigo,
				tecno.tecn_descricao,
				COUNT(DISTINCT evia_tran_pess_oras_codigo),
				COUNT(DISTINCT evia_emba_pjur_pess_oras_codigo),
				COUNT(DISTINCT evia_corr_codigo),
				COUNT(DISTINCT evia_usua_oras_codigo),
				COUNT(DISTINCT evia_segu_codigo),
				(SELECT COUNT(evia_viag_andamento)
					FROM evia_estatistica_viagem abertas
					WHERE evia_viag_andamento = B'0'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND abertas.evia_tecn_codigo = fato.evia_tecn_codigo
				),
				(SELECT COUNT(evia_viag_andamento) 
					FROM evia_estatistica_viagem andamento
					WHERE evia_viag_andamento = B'1'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND andamento.evia_tecn_codigo = fato.evia_tecn_codigo
				),
				'" . date("Y-m-d H:i") . "'
				FROM evia_estatistica_viagem fato
					LEFT JOIN tecn_tecnologia tecno
					ON tecno.tecn_codigo = evia_tecn_codigo
				WHERE evia_data >= '" . date("Y-m-d H") . ":00'
				GROUP BY fato.evia_tecn_codigo, tecno.tecn_descricao
				";

		$this->execute($sql, $model);		
	}


	// ####################################################################################
	// TEvtdEstaViagTecnoDia Functions 
	// ####################################################################################		

	function limparTEvtdEstaViagTecnoDia($model)
	{
		$sql = "DELETE FROM evtd_estatistica_viagem_tecnologia_dia WHERE evtd_data >= '" . date("Y-m-d") . "'";
		$this->execute($sql, $model);

		$sql = "DELETE FROM evtd_estatistica_viagem_tecnologia_dia WHERE evtd_data < '" . $this->last_year . "'";
		$this->execute($sql, $model);			
	}

	function importarTEvtdEstaViagTecnoDia($model)
	{
		$sql = "INSERT INTO evtd_estatistica_viagem_tecnologia_dia
				SELECT
				fato.evia_tecn_codigo,
				tecno.tecn_descricao,
				COUNT(DISTINCT evia_tran_pess_oras_codigo),
				COUNT(DISTINCT evia_emba_pjur_pess_oras_codigo),
				COUNT(DISTINCT evia_corr_codigo),
				COUNT(DISTINCT evia_usua_oras_codigo),
				COUNT(DISTINCT evia_segu_codigo),
				(SELECT COUNT(evia_viag_andamento)
					FROM evia_estatistica_viagem abertas
					WHERE evia_viag_andamento = B'0'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND abertas.evia_tecn_codigo = fato.evia_tecn_codigo
				),
				(SELECT COUNT(evia_viag_andamento) 
					FROM evia_estatistica_viagem andamento
					WHERE evia_viag_andamento = B'1'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND andamento.evia_tecn_codigo = fato.evia_tecn_codigo
				),
				'" . date("Y-m-d H:i") . "'
				FROM evia_estatistica_viagem fato
					LEFT JOIN tecn_tecnologia tecno
					ON tecno.tecn_codigo = evia_tecn_codigo
				WHERE evia_data >= '" . date("Y-m-d H") . ":00'
				GROUP BY fato.evia_tecn_codigo, tecno.tecn_descricao
				";

		$this->execute($sql, $model);		
	}



	// ####################################################################################
	// TEvtdEstaViagTranDia Functions 
	// ####################################################################################		

	function limparTEvtdEstaViagTranDia($model)
	{
		$sql = "DELETE FROM evtd_estatistica_viagem_transportadora_dia WHERE evtd_data >= '" . date("Y-m-d") . "'";
		$this->execute($sql, $model);

		$sql = "DELETE FROM evtd_estatistica_viagem_transportadora_dia WHERE evtd_data < '" . $this->last_year . "'";
		$this->execute($sql, $model);	

	}

	function importarTEvtdEstaViagTranDia($model)
	{
		$sql = "INSERT INTO evtd_estatistica_viagem_transportadora_dia
				SELECT
				evia_tran_pess_oras_codigo,
				trans.pjur_razao_social,
				COUNT(DISTINCT evia_emba_pjur_pess_oras_codigo),
				COUNT(DISTINCT evia_corr_codigo),
				COUNT(DISTINCT evia_usua_oras_codigo),
				COUNT(DISTINCT evia_segu_codigo),
				COUNT(DISTINCT evia_tecn_codigo),
				(SELECT COUNT(evia_viag_andamento)
					FROM evia_estatistica_viagem abertas
					WHERE evia_viag_andamento = B'0'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND abertas.evia_tran_pess_oras_codigo = fato.evia_tran_pess_oras_codigo
				),
				(SELECT COUNT(evia_viag_andamento) 
					FROM evia_estatistica_viagem andamento
					WHERE evia_viag_andamento = B'1'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND andamento.evia_tran_pess_oras_codigo = fato.evia_tran_pess_oras_codigo
				),
				'" . date("Y-m-d H:i") . "'
				FROM evia_estatistica_viagem fato
					LEFT JOIN pjur_pessoa_juridica trans
					ON trans.pjur_pess_oras_codigo = evia_tran_pess_oras_codigo
				WHERE evia_data >= '" . date("Y-m-d H") . ":00'
				GROUP BY evia_tran_pess_oras_codigo, trans.pjur_razao_social
				";

		$this->execute($sql, $model);		
	}

	// ####################################################################################
	// TEvthEstaViagTranHora Functions 
	// ####################################################################################		

	function limparTEvthEstaViagTranHora($model)
	{
		$sql = "DELETE FROM evth_estatistica_viagem_transportadora_hora WHERE evth_data >= '" . date("Y-m-d H") . ":00'";
		$this->execute($sql, $model);
	}

	function importarTEvthEstaViagTranHora($model)
	{
		$sql = "INSERT INTO evth_estatistica_viagem_transportadora_hora
				SELECT
				evia_tran_pess_oras_codigo,
				trans.pjur_razao_social,
				COUNT(DISTINCT evia_emba_pjur_pess_oras_codigo),
				COUNT(DISTINCT evia_corr_codigo),
				COUNT(DISTINCT evia_usua_oras_codigo),
				COUNT(DISTINCT evia_segu_codigo),
				COUNT(DISTINCT evia_tecn_codigo),
				(SELECT COUNT(evia_viag_andamento)
					FROM evia_estatistica_viagem abertas
					WHERE evia_viag_andamento = B'0'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND abertas.evia_tran_pess_oras_codigo = fato.evia_tran_pess_oras_codigo
				),
				(SELECT COUNT(evia_viag_andamento) 
					FROM evia_estatistica_viagem andamento
					WHERE evia_viag_andamento = B'1'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND andamento.evia_tran_pess_oras_codigo = fato.evia_tran_pess_oras_codigo
				),
				'" . date("Y-m-d H:i") . "'
				FROM evia_estatistica_viagem fato
					LEFT JOIN pjur_pessoa_juridica trans
					ON trans.pjur_pess_oras_codigo = evia_tran_pess_oras_codigo
				WHERE evia_data >= '" . date("Y-m-d H") . ":00'
				GROUP BY evia_tran_pess_oras_codigo, trans.pjur_razao_social
				";

		$this->execute($sql, $model);		
	}

	// ####################################################################################
	// TEvohEstaViagOperaHora Functions 
	// ####################################################################################		
	// Todo: Verificar relacionamento de operador

	function limparTEvohEstaViagOperaHora($model)
	{
		$sql = "DELETE FROM evoh_estatistica_viagem_operador_hora WHERE evoh_data >= '" . date("Y-m-d H") . ":00'";
		$this->execute($sql, $model);
	}

	function importarTEvohEstaViagOperaHora($model)
	{
		$sql = "INSERT INTO evoh_estatistica_viagem_operador_hora
				SELECT
				evia_usua_oras_codigo,
				usua_login,
				COUNT(DISTINCT evia_tran_pess_oras_codigo),
				COUNT(DISTINCT evia_emba_pjur_pess_oras_codigo),
				COUNT(DISTINCT evia_corr_codigo),
				COUNT(DISTINCT evia_tecn_codigo),
				COUNT(DISTINCT evia_segu_codigo),				
				(SELECT COUNT(evia_viag_andamento)
					FROM evia_estatistica_viagem abertas
					WHERE evia_viag_andamento = B'0'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND abertas.evia_usua_oras_codigo = fato.evia_usua_oras_codigo
				),
				(SELECT COUNT(evia_viag_andamento) 
					FROM evia_estatistica_viagem andamento
					WHERE evia_viag_andamento = B'1'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND andamento.evia_usua_oras_codigo = fato.evia_usua_oras_codigo
				),
				'" . date("Y-m-d H:i") . "'
				FROM evia_estatistica_viagem fato
					LEFT JOIN usua_usuario usuario
					ON usuario.usua_pfis_pess_oras_codigo = evia_usua_oras_codigo
				WHERE evia_data >= '" . date("Y-m-d H") . ":00'
				GROUP BY evia_usua_oras_codigo, usua_login
				";

		$this->execute($sql, $model);		
	}	

	// ####################################################################################
	// TEvodEstaViagOperaDia Functions 
	// ####################################################################################		
	// Todo: Verificar relacionamento de operador

	function limparTEvodEstaViagOperaDia($model)
	{
		$sql = "DELETE FROM evod_estatistica_viagem_operador_dia WHERE evod_data >= '" . date("Y-m-d") . "'";
		$this->execute($sql, $model);

		$sql = "DELETE FROM evod_estatistica_viagem_operador_dia WHERE evod_data < '" . $this->last_year . "'";
		$this->execute($sql, $model);				
	}

	function importarTEvodEstaViagOperaDia($model)
	{
		$sql = "INSERT INTO evod_estatistica_viagem_operador_dia
				SELECT
				evia_usua_oras_codigo,
				usua_login,
				COUNT(DISTINCT evia_tran_pess_oras_codigo),
				COUNT(DISTINCT evia_emba_pjur_pess_oras_codigo),
				COUNT(DISTINCT evia_corr_codigo),
				COUNT(DISTINCT evia_tecn_codigo),
				COUNT(DISTINCT evia_segu_codigo),				
				(SELECT COUNT(evia_viag_andamento)
					FROM evia_estatistica_viagem abertas
					WHERE evia_viag_andamento = B'0'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND abertas.evia_usua_oras_codigo = fato.evia_usua_oras_codigo
				),
				(SELECT COUNT(evia_viag_andamento) 
					FROM evia_estatistica_viagem andamento
					WHERE evia_viag_andamento = B'1'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND andamento.evia_usua_oras_codigo = fato.evia_usua_oras_codigo
				),
				'" . date("Y-m-d H:i") . "'
				FROM evia_estatistica_viagem fato
					LEFT JOIN usua_usuario usuario
					ON usuario.usua_pfis_pess_oras_codigo = evia_usua_oras_codigo
				WHERE evia_data >= '" . date("Y-m-d H") . ":00'
				GROUP BY evia_usua_oras_codigo, usua_login
				";

		$this->execute($sql, $model);		
	}

	// ####################################################################################
	// TEvedEstaViagEmbaHora Functions 
	// ####################################################################################		

	function limparTEvehEstaViagEmbaHora($model)
	{
		$sql = "DELETE FROM eveh_estatistica_viagem_embarcador_hora WHERE eveh_data >= '" . date("Y-m-d H") . ":00'";
		$this->execute($sql, $model);
	}

	function importarTEvehEstaViagEmbaHora($model)
	{
		$sql = "INSERT INTO eveh_estatistica_viagem_embarcador_hora
				SELECT
				evia_emba_pjur_pess_oras_codigo,
				emba.pjur_razao_social,
				COUNT(DISTINCT evia_tran_pess_oras_codigo),
				COUNT(DISTINCT evia_corr_codigo),
				COUNT(DISTINCT evia_usua_oras_codigo),
				COUNT(DISTINCT evia_segu_codigo),
				COUNT(DISTINCT evia_tecn_codigo),
				(SELECT COUNT(evia_viag_andamento)
					FROM evia_estatistica_viagem abertas
					WHERE evia_viag_andamento = B'0'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND abertas.evia_emba_pjur_pess_oras_codigo = fato.evia_emba_pjur_pess_oras_codigo
				),
				(SELECT COUNT(evia_viag_andamento) 
					FROM evia_estatistica_viagem andamento
					WHERE evia_viag_andamento = B'1'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND andamento.evia_emba_pjur_pess_oras_codigo = fato.evia_emba_pjur_pess_oras_codigo
				),
				'" . date("Y-m-d H:i") . "'
				FROM evia_estatistica_viagem fato
					LEFT JOIN pjur_pessoa_juridica emba
					ON emba.pjur_pess_oras_codigo = evia_emba_pjur_pess_oras_codigo
				WHERE evia_data >= '" . date("Y-m-d H") . ":00'
				GROUP BY evia_emba_pjur_pess_oras_codigo, emba.pjur_razao_social
				";

		$this->execute($sql, $model);		
	}

	// ####################################################################################
	// TEvedEstaViagEmbaDia Functions 
	// ####################################################################################		

	function limparTEvedEstaViagEmbaDia($model)
	{
		$sql = "DELETE FROM eved_estatistica_viagem_embarcador_dia WHERE eved_data >= '" . date("Y-m-d") . "'";
		$this->execute($sql, $model);

		$sql = "DELETE FROM eved_estatistica_viagem_embarcador_dia WHERE eved_data < '" . $this->last_year . "'";
		$this->execute($sql, $model);				
	}

	function importarTEvedEstaViagEmbaDia($model)
	{
		$sql = "INSERT INTO eved_estatistica_viagem_embarcador_dia
				SELECT
				evia_emba_pjur_pess_oras_codigo,
				emba.pjur_razao_social,
				COUNT(DISTINCT evia_tran_pess_oras_codigo),
				COUNT(DISTINCT evia_corr_codigo),
				COUNT(DISTINCT evia_usua_oras_codigo),
				COUNT(DISTINCT evia_segu_codigo),
				COUNT(DISTINCT evia_tecn_codigo),
				(SELECT COUNT(evia_viag_andamento)
					FROM evia_estatistica_viagem abertas
					WHERE evia_viag_andamento = B'0'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND abertas.evia_emba_pjur_pess_oras_codigo = fato.evia_emba_pjur_pess_oras_codigo
				),
				(SELECT COUNT(evia_viag_andamento) 
					FROM evia_estatistica_viagem andamento
					WHERE evia_viag_andamento = B'1'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND andamento.evia_emba_pjur_pess_oras_codigo = fato.evia_emba_pjur_pess_oras_codigo
				),
				'" . date("Y-m-d H:i") . "'
				FROM evia_estatistica_viagem fato
					LEFT JOIN pjur_pessoa_juridica emba
					ON emba.pjur_pess_oras_codigo = evia_emba_pjur_pess_oras_codigo
				WHERE evia_data >= '" . date("Y-m-d H") . ":00'
				GROUP BY evia_emba_pjur_pess_oras_codigo, emba.pjur_razao_social
				";

		$this->execute($sql, $model);		
	}


	// ####################################################################################
	// TEvchEstaViagCorrDia Functions 
	// ####################################################################################		

	function limparTEvcdEstaViagCorrDia($model)
	{
		$sql = "DELETE FROM evcd_estatistica_viagem_corretora_dia WHERE evcd_data >= '" . date("Y-m-d") . "'";
		$this->execute($sql, $model);

		$sql = "DELETE FROM evcd_estatistica_viagem_corretora_dia WHERE evcd_data < '" . $this->last_year . "'";
		$this->execute($sql, $model);		

	}

	function importarTEvcdEstaViagCorrDia($model)
	{
		$sql = "INSERT INTO evcd_estatistica_viagem_corretora_dia
				SELECT
				evia_corr_codigo,
				evia_corr_nome,
				COUNT(DISTINCT evia_tran_pess_oras_codigo),
				COUNT(DISTINCT evia_emba_pjur_pess_oras_codigo),
				COUNT(DISTINCT evia_usua_oras_codigo),
				COUNT(DISTINCT evia_segu_codigo),
				COUNT(DISTINCT evia_tecn_codigo),
				(SELECT COUNT(evia_viag_andamento)
					FROM evia_estatistica_viagem abertas
					WHERE evia_viag_andamento = B'0'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND abertas.evia_corr_codigo = fato.evia_corr_codigo
				),
				(SELECT COUNT(evia_viag_andamento) 
					FROM evia_estatistica_viagem andamento
					WHERE evia_viag_andamento = B'1'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND andamento.evia_corr_codigo = fato.evia_corr_codigo
				),
				'" . date("Y-m-d H:i") . "'
				FROM evia_estatistica_viagem fato
				WHERE evia_data >= '" . date("Y-m-d H") . ":00'
				GROUP BY evia_corr_codigo, evia_corr_nome
				";

		$this->execute($sql, $model);		
	}

	// ####################################################################################
	// TEvchEstaViagCorrHora Functions 
	// ####################################################################################		

	function limparTEvchEstaViagCorrHora($model)
	{
		$sql = "DELETE FROM evch_estatistica_viagem_corretora_hora WHERE evch_data >= '" . date("Y-m-d H") . ":00'";
		$this->execute($sql, $model);
	}

	function importarTEvchEstaViagCorrHora($model)
	{
		$sql = "INSERT INTO evch_estatistica_viagem_corretora_hora
				SELECT
				evia_corr_codigo,
				evia_corr_nome,
				COUNT(DISTINCT evia_tran_pess_oras_codigo),
				COUNT(DISTINCT evia_emba_pjur_pess_oras_codigo),
				COUNT(DISTINCT evia_usua_oras_codigo),
				COUNT(DISTINCT evia_segu_codigo),
				COUNT(DISTINCT evia_tecn_codigo),
				(SELECT COUNT(evia_viag_andamento)
					FROM evia_estatistica_viagem abertas
					WHERE evia_viag_andamento = B'0'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND abertas.evia_corr_codigo = fato.evia_corr_codigo
				),
				(SELECT COUNT(evia_viag_andamento) 
					FROM evia_estatistica_viagem andamento
					WHERE evia_viag_andamento = B'1'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND andamento.evia_corr_codigo = fato.evia_corr_codigo
				),
				'" . date("Y-m-d H:i") . "'
				FROM evia_estatistica_viagem fato
				WHERE evia_data >= '" . date("Y-m-d H") . ":00'
				GROUP BY evia_corr_codigo, evia_corr_nome
				";

		$this->execute($sql, $model);		
	}


	// ####################################################################################
	// TEvshEstaViagSeguHora Functions 
	// ####################################################################################		

	function limparTEvshEstaViagSeguHora($model)
	{
		$sql = "DELETE FROM evsh_estatistica_viagem_seguradora_hora WHERE evsh_data >= '" . date("Y-m-d H") . ":00'";
		$this->execute($sql, $model);
	}

	function importarTEvshEstaViagSeguHora($model)
	{
		$sql = "INSERT INTO evsh_estatistica_viagem_seguradora_hora
				SELECT
				evia_segu_codigo,
				evia_segu_nome,
				COUNT(DISTINCT evia_tran_pess_oras_codigo),
				COUNT(DISTINCT evia_emba_pjur_pess_oras_codigo),
				COUNT(DISTINCT evia_usua_oras_codigo),
				COUNT(DISTINCT evia_corr_codigo),
				COUNT(DISTINCT evia_tecn_codigo),
				(SELECT COUNT(evia_viag_andamento)
					FROM evia_estatistica_viagem abertas
					WHERE evia_viag_andamento = B'0'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND abertas.evia_segu_codigo = fato.evia_segu_codigo
				),
				(SELECT COUNT(evia_viag_andamento) 
					FROM evia_estatistica_viagem andamento
					WHERE evia_viag_andamento = B'1'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND andamento.evia_segu_codigo = fato.evia_segu_codigo
				),
				'" . date("Y-m-d H:i") . "'
				FROM evia_estatistica_viagem fato
				WHERE evia_data >= '" . date("Y-m-d H") . ":00'
				GROUP BY evia_segu_codigo, evia_segu_nome
				";

		$this->execute($sql, $model);		
	}

	// ####################################################################################
	// TEvsdEstaViagSeguDia Functions 
	// ####################################################################################		

	function limparTEvsdEstaViagSeguDia($model) 
	{
		$sql = "DELETE FROM evsd_estatistica_viagem_seguradora_dia WHERE evsd_data >= '" . date("Y-m-d") . "'";
		$this->execute($sql, $model);

		$sql = "DELETE FROM evsd_estatistica_viagem_seguradora_dia WHERE evsd_data < '" . $this->last_year . "'";
		$this->execute($sql, $model);		

	}

	function importarTEvsdEstaViagSeguDia($model)
	{

		$sql = "INSERT INTO evsd_estatistica_viagem_seguradora_dia
				SELECT
				evia_segu_codigo,
				evia_segu_nome,
				COUNT(DISTINCT evia_tran_pess_oras_codigo),
				COUNT(DISTINCT evia_emba_pjur_pess_oras_codigo),
				COUNT(DISTINCT evia_usua_oras_codigo),
				COUNT(DISTINCT evia_corr_codigo),
				COUNT(DISTINCT evia_tecn_codigo),
				(SELECT COUNT(evia_viag_andamento)
					FROM evia_estatistica_viagem abertas
					WHERE evia_viag_andamento = B'0'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND abertas.evia_segu_codigo = fato.evia_segu_codigo
				),
				(SELECT COUNT(evia_viag_andamento) 
					FROM evia_estatistica_viagem andamento
					WHERE evia_viag_andamento = B'1'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
					AND andamento.evia_segu_codigo = fato.evia_segu_codigo
				),
				'" . date("Y-m-d H:i") . "'
				FROM evia_estatistica_viagem fato
				WHERE evia_data >= '" . date("Y-m-d H") . ":00'
				GROUP BY evia_segu_codigo, evia_segu_nome
				";

		$this->execute($sql, $model);

	}	

	// ####################################################################################
	// TEvgdEstaViagemGeralDia Functions 
	// ####################################################################################

	function limparTEvgdEstaViagemGeralDia($model)
	{
		$sql = "DELETE FROM evgd_estatistica_viagem_geral_dia WHERE evgd_data >= '" . date("Y-m-d") . "'";
		$this->execute($sql, $model);

		$sql = "DELETE FROM evgd_estatistica_viagem_geral_dia WHERE evgd_data < '" . $this->last_year . "'";
		$this->execute($sql, $model);

	}

	function importarTEvgdEstaViagemGeralDia($model)
	{

		$sql = "INSERT INTO evgd_estatistica_viagem_geral_dia
				SELECT
				COUNT(DISTINCT evia_segu_codigo),
				COUNT(DISTINCT evia_tran_pess_oras_codigo),
				COUNT(DISTINCT evia_emba_pjur_pess_oras_codigo),
				COUNT(DISTINCT evia_usua_oras_codigo),
				COUNT(DISTINCT evia_corr_codigo),
				COUNT(DISTINCT evia_tecn_codigo),
				(SELECT COUNT(evia_viag_andamento)
					FROM evia_estatistica_viagem abertas
					WHERE evia_viag_andamento = B'0'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
				),
				(SELECT COUNT(evia_viag_andamento) 
					FROM evia_estatistica_viagem andamento
					WHERE evia_viag_andamento = B'1'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
				),
				'" . date("Y-m-d H:i") . "'
				FROM evia_estatistica_viagem fato
				WHERE evia_data >= '" . date("Y-m-d H") . ":00'";

		$this->execute($sql, $model);
	}

	// ####################################################################################
	// TEvghEstaViagemGeralHora Functions 
	// ####################################################################################	

	function limparTEvghEstaViagemGeralHora($model)
	{
		$sql = "DELETE FROM evgh_estatistica_viagem_geral_hora WHERE evgh_data >= '" . date("Y-m-d H") . ":00'";
		$this->execute($sql, $model);


	}

	function importarTEvghEstaViagemGeralHora($model)
	{

		$sql = "INSERT INTO evgh_estatistica_viagem_geral_hora
				SELECT
				COUNT(DISTINCT evia_segu_codigo),
				COUNT(DISTINCT evia_tran_pess_oras_codigo),
				COUNT(DISTINCT evia_emba_pjur_pess_oras_codigo),
				COUNT(DISTINCT evia_usua_oras_codigo),
				COUNT(DISTINCT evia_corr_codigo),
				COUNT(DISTINCT evia_tecn_codigo),
				(SELECT COUNT(evia_viag_andamento)
					FROM evia_estatistica_viagem abertas
					WHERE evia_viag_andamento = B'0'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
				),
				(SELECT COUNT(evia_viag_andamento) 
					FROM evia_estatistica_viagem andamento
					WHERE evia_viag_andamento = B'1'
					AND evia_data >= '" . date("Y-m-d H") . ":00'
				),                                    
				'" . date("Y-m-d H:i") . "'
				FROM evia_estatistica_viagem fato
				WHERE evia_data >= '" . date("Y-m-d H") . ":00'";

		$this->execute($sql, $model);
	}

	// ####################################################################################
	// TEviaEstaViagem Functions 
	// ####################################################################################	

	function importarTEviaEstaViagem($model)
	{

		$sql = "INSERT INTO evia_estatistica_viagem
				SELECT 
				viag_codigo evia_viag_codigo,
				viag_codigo_sm evia_viag_codigo_sm, 
				usua_pfis_pess_oras_codigo evia_usua_oras_codigo,
				viag_tran_pess_oras_codigo evia_tran_pess_oras_codigo,
				viag_emba_pjur_pess_oras_codigo evia_emba_pjur_pess_oras_codigo,
				viag_segu_pjur_pess_oras_codigo evia_segu_codigo,
				pjur_seguradora.pjur_razao_social evia_segu_nome,
				viag_corr_pjur_pess_oras_codigo evia_corr_codigo,
				pjur_corretora.pjur_razao_social evia_corr_nome,
				case when viag_data_inicio is null then B'0' else B'1' end as evia_viag_andamento,
				tecn_codigo evia_tecn_codigo,
				'" . date("Y-m-d H:i") . "' evia_data
				FROM
				viag_viagem
				LEFT JOIN pjur_pessoa_juridica pjur_embarcador
				ON pjur_embarcador.pjur_pess_oras_codigo = viag_emba_pjur_pess_oras_codigo
				LEFT JOIN pjur_pessoa_juridica pjur_transportador
				ON pjur_transportador.pjur_pess_oras_codigo = viag_tran_pess_oras_codigo
				LEFT JOIN pjur_pessoa_juridica pjur_seguradora
				ON pjur_seguradora.pjur_pess_oras_codigo = viag_segu_pjur_pess_oras_codigo
				LEFT JOIN pjur_pessoa_juridica pjur_corretora
				ON pjur_corretora.pjur_pess_oras_codigo = viag_corr_pjur_pess_oras_codigo				
				LEFT JOIN vvei_viagem_veiculo ON vvei_viag_codigo = viag_codigo AND vvei_precedencia='1'
				LEFT JOIN orte_objeto_rastreado_termina ON orte_oras_codigo = vvei_veic_oras_codigo AND orte_sequencia = 'P'
				LEFT JOIN term_terminal ON term_codigo = orte_term_codigo
				LEFT JOIN vtec_versao_tecnologia ON vtec_codigo = term_vtec_codigo
				LEFT JOIN tecn_tecnologia ON tecn_codigo = vtec_tecn_codigo
				LEFT JOIN vusu_viagem_usuario ON vusu_viag_codigo = viag_codigo
				LEFT JOIN usua_usuario ON usua_pfis_pess_oras_codigo = vusu_usua_oras_codigo
				WHERE
				viag_data_cadastro >= NOW() - interval '1 year'
				AND viag_data_fim is null";
		$this->execute($sql, $model);

/*
		foreach($data as $item)
		{
			echo "\nImportando SM : " . $item[0]['evia_viag_codigo'];

			$_record[$model] = $item[0];

			
			$this->{$model}->save(array($model => array($model => $_record), false));


		}		
		*/

	}

	function limparTEviaEstaViagem($model)
	{
		$sql = "DELETE FROM evia_estatistica_viagem WHERE evia_data >= '" . date("Y-m-d H") . ":00'";
		$this->execute($sql, $model);

		$sql = "DELETE FROM evia_estatistica_viagem WHERE evia_data < '" . $this->last_year . "'";
		$this->execute($sql, $model);

	}

	// ####################################################################################
	// Internal Functions 
	// ####################################################################################		

	function buscar(&$sql, $model)
	{
		echo "\nSQL : " . $sql;
		return $this->{$model}->getDataSource()->fetchAll($sql);
	}

	function execute(&$sql, $model)
	{
		echo "\n\nSQL starting at " . date("Y-m-d H:i:s") . " : " . $sql;
		$data = $this->{$model}->query($sql);
		echo "\n------->Finished at " . date("Y-m-d H:i:s");
		return $data;

	}

}