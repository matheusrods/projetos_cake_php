<?php
class ScorecardImportacaoShell extends Shell {
	var $uses = array('FichaScorecard');
	
    private function im_running($tipo) {
		$retorno = shell_exec("ps -ef | grep \"".$tipo."\" | wc -l");
		return $retorno > 3;
	}


	public function main() {
		echo "\nuse scorecard_importacao processar [dt_ini] [dt_fim]\n\n\n";
	}

	public function processar() {
		if(!$this->im_running('scorecard_importacao')) {
			$dt_ini = (isset($this->args[0]) ? $this->args[0] :  null);
	    	$dt_fim = (isset($this->args[1]) ? $this->args[1] :  null);

	    	if($dt_ini != null || $dt_fim != null) {
	    		$sqlAux = "data_inclusao between '".$dt_ini."' and '".$dt_fim."'";
	    		$topAux = "";
	    	}
	    	else {
	    		$sqlAux = "1=1";
	    		$topAux = "TOP 10000";
	    	}

	    	$this->out("Carregando Dados...");

			$sql = "SELECT ".$topAux."
						codigo
					FROM
						dbTeleconsult.informacoes.ficha
					WHERE
						".$sqlAux."
						AND codigo not in (select codigo_ficha_teleconsult from dbTeleconsult.informacoes.ficha_scorecard where data_inclusao between '".$dt_ini."' and '".$dt_fim."')
					ORDER BY
						data_inclusao DESC
				";
			$fichas = $this->FichaScorecard->query($sql);
			$contador = 0;
			$dt_inicio = date('d/m/Y H:i:s');
			$this->out('Fichas a Renovar: ' . count($fichas));
			$this->out('Iniciando processo...');
			sleep(3);
						
			echo `clear`;
			$this->out("===================================");
			$this->out('Fichas Renovadas');
			$this->out('Periodo Ini: ' . date('d/m/Y H:i:s',strtotime($dt_ini)) );
			$this->out('Periodo Fim: ' . date('d/m/Y H:i:s',strtotime($dt_fim)) );
			$this->out("===================================");
			$this->out("Ficha Antiga\t\tFicha Nova");
			if(count($fichas) == 0) {
				$this->out("===================================");
				$this->out("      Sem fichas para renovar");
			}
			$erros = array();
			foreach($fichas as $f){
				$this->FichaScorecard->query('BEGIN TRANSACTION');
				try {
					$ficha = $f[0];
					unset($f);
					$codigo_ficha_anterior = $ficha['codigo'];
					$codigo_ficha_nova = $this->inserirFicha($codigo_ficha_anterior);

					$this->inserirRelacionados($codigo_ficha_anterior,$codigo_ficha_nova);
					$this->out($codigo_ficha_anterior . "\t\t\t" . $codigo_ficha_nova);
					$contador++;
				} catch (Exception $e) {
					$this->FichaScorecard->rollback();
					$erros[] = $ficha['codigo'];
				}
				$this->FichaScorecard->commit();
			}
			$this->out("===================================");
			$this->out('Iniciada   ' . $dt_inicio);
			$this->out('Finalizada ' . date('d/m/Y H:i:s'));
			$this->out('Fichas importadas: ' . $contador);
			$this->out('Fichas nao importadas: ' . count($erros));
			$this->out("===================================");


		} else {
			$this->out("Ja tem uma instancia rodando. Tente novamente mais tarde.");
		}
	}
	public function inserirRelacionados($codigo_ficha_anterior, $codigo_ficha_nova) {
		// Profissional Contato Log
		$sql = "INSERT INTO dbTeleconsult.informacoes.ficha_scorecard_profissional_contato_log
				(codigo_ficha_scorecard, codigo_profissional_contato_log)
				select
					'" . $codigo_ficha_nova . "' as codigo_ficha, codigo_profissional_contato_log
				from
					dbTeleconsult.informacoes.ficha_profissional_contato_log
				where
					codigo_ficha = '".$codigo_ficha_anterior."'
				";
		$this->FichaScorecard->query($sql);

		// Proprietario Contato Log
		$sql = "INSERT INTO dbTeleconsult.informacoes.ficha_scorecard_proprietario_contato_log
				(codigo_ficha_scorecard, codigo_proprietario_contato_log)
				select
					'" . $codigo_ficha_nova . "' as codigo_ficha, codigo_proprietario_contato_log
				from
					dbTeleconsult.informacoes.ficha_proprietario_contato_log
				where
					codigo_ficha = '".$codigo_ficha_anterior."'
				";
		$this->FichaScorecard->query($sql);

		// Questão Resposta
		$sql = "INSERT INTO dbTeleconsult.informacoes.ficha_scorecard_questao_resposta
				(codigo_ficha_scorecard, codigo_questao_resposta, observacao)
				select
					'" . $codigo_ficha_nova . "' as codigo_ficha, codigo_questao_resposta, observacao 
				from
					dbTeleconsult.informacoes.ficha_questao_resposta
				where
					codigo_ficha = '".$codigo_ficha_anterior."'
				";
		$this->FichaScorecard->query($sql);

		// Retorno
		$sql = "INSERT INTO dbTeleconsult.informacoes.ficha_scorecard_retorno
				(codigo_ficha_scorecard, codigo_tipo_contato, codigo_tipo_retorno, descricao, nome)
				select
					'" . $codigo_ficha_nova . "' as codigo_ficha, codigo_tipo_contato, codigo_tipo_retorno, descricao, nome 
				from
					dbTeleconsult.informacoes.ficha_retorno
				where
					codigo_ficha = '".$codigo_ficha_anterior."'
				";
		$this->FichaScorecard->query($sql);

		// Veículo
		$sql = "INSERT INTO dbTeleconsult.informacoes.ficha_scorecard_veiculo
				(codigo_ficha_scorecard, codigo_veiculo_log, tipo, codigo_tecnologia)
				select
					'" . $codigo_ficha_nova . "' as codigo_ficha, codigo_veiculo_log, tipo, codigo_tecnologia 
				from
					dbTeleconsult.informacoes.ficha_veiculo 
				where
					codigo_ficha = '".$codigo_ficha_anterior."'
				";
		$this->FichaScorecard->query($sql);

		// ficha_scorecard_veiculo_proprietario_contato_log
		$sql = "INSERT INTO dbTeleconsult.informacoes.ficha_scorecard_veiculo_proprietario_contato_log
				(codigo_ficha_scorecard_veiculo, codigo_proprietario_contato_log)
				select
					fv.codigo, (select top 1 codigo_proprietario_log from dbTeleconsult.informacoes.ficha where codigo = '".$codigo_ficha_anterior."')
				from
					dbTeleconsult.informacoes.ficha_scorecard_veiculo as fv
                inner join dbTeleconsult.informacoes.ficha_scorecard as f
                    on (f.codigo = fv.codigo_ficha_scorecard)
				where
					fv.codigo_ficha_scorecard = '".$codigo_ficha_nova."'
				";
		$this->FichaScorecard->query($sql);
	}

	public function inserirFicha($codigo_ficha) {
		$sql = "INSERT INTO 
					dbTeleconsult.informacoes.ficha_scorecard 
				(
					codigo_cliente,
					codigo_profissional_log,
					codigo_profissional_tipo,
					codigo_carga_tipo,
					codigo_endereco_cidade_carga_origem,
					codigo_endereco_cidade_carga_destino,
					codigo_carga_valor,
					codigo_status,
					data_validade,
					observacao,
					ativo,
					data_inclusao,
					codigo_usuario_inclusao,
					data_alteracao,
					codigo_usuario_alteracao,
					codigo_cliente_embarcador,
					codigo_cliente_transportador,
					extracao,
					codigo_profissional_endereco_log,
					observacao_supervisor,
					codigo_usuario_responsavel,
					codigo_usuario,
					codigo_embarcador,
					codigo_transportador,
					codigo_parametro_score,
					percentual_pontos,
					total_pontos,
					justificativa_alteracao,
					resumo,
					codigo_ficha_teleconsult
				)
				SELECT
					codigo_cliente,
					codigo_profissional_log,
					codigo_profissional_tipo,
					codigo_carga_tipo,
					codigo_endereco_cidade_carga_origem,
					codigo_endereco_cidade_carga_destino,
					codigo_carga_valor,
					CASE codigo_status
						WHEN 2 THEN 3
						WHEN 5 THEN 4
						WHEN 3 THEN 5
						WHEN 4 THEN 7
					ELSE
						codigo_status
					END as codigo_status,
					data_validade,
					observacao,
					ativo,
					data_inclusao,
					codigo_usuario_inclusao,
					data_alteracao,
					codigo_usuario_alteracao,
					codigo_cliente_embarcador,
					codigo_cliente_transportador,
					'' as extracao,
					(select top 1 codigo_profissional_endereco_log from dbTeleconsult.informacoes.ficha_profissional_endereco_log where codigo_ficha = '".$codigo_ficha."' order by 1 desc) as codigo_profissional_endereco_log,
					'' as observacao_supervisor,
					codigo_usuario_alteracao,
					codigo_usuario_solicitacao,
					codigo_cliente_embarcador,
					codigo_cliente_transportador,
					CASE WHEN codigo_status in (1,6) THEN 2 ELSE 14 END as codigo_parametro_score,
            		CASE WHEN codigo_status in (1,6) THEN 100 ELSE 0 END as percentual_pontos,
            		CASE WHEN codigo_status in (1,6) THEN 100 ELSE 0 END as total_pontos,
            		'' as justificativa_alteracao,
            		'' as resumo,
            		codigo as codigo_ficha_teleconsult
            	FROM
            		dbTeleconsult.informacoes.ficha
            	WHERE
            		codigo = '".$codigo_ficha."'
            	;SELECT @@IDENTITY;
				";
		$ultimo = $this->FichaScorecard->query($sql);
		return $ultimo[0][0]['computed'];
	}

}
?>
