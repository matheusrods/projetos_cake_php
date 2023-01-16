<?php
class LimpezaBaseTlcShell extends Shell {
	var $uses = array('Ficha', 'FichaPesquisa', 'FichaCt');

	private function im_running() {
		$data_atual_strtotime = strtotime(date("d-m-Y H:i:s"));		
		if( ( date("N", $data_atual_strtotime ) > 5 ) || (date("H", $data_atual_strtotime) > 18 ) ) {
			echo "Esse script só deve ser executado em dias de Semana e no horario comercial";
			return true;
		}
		$cmd = `ps aux | grep 'limpeza_base_tlc'`;
		return substr_count($cmd, 'cake.php -working') > 1;
	}
    
    public function carregarFichas(){
		$limit  = 1000;
		$fields = array('Ficha.codigo', 'Ficha.data_inclusao');
		$ano	= (date('Y')-2);
		$conditions = array('Ficha.data_inclusao <' => $ano );
		return $this->Ficha->find('all', compact('conditions','limit', 'fields' ));
    }

	public function run(){
		if( !$this->im_running() ) {
			echo 'INICIO: '. date("d/m/Y H:i:s")."\n";
			$fichas = $this->carregarFichas();
			foreach ($fichas AS $key => $dados_ficha ) {
				$this->Ficha->query('begin transaction');
				try{
					$codigo_ficha = $dados_ficha['Ficha']['codigo'];
					if( !$codigo_ficha )
						throw new Exception('Ficha nao Identificada');
					$data_inclusao 	= $dados_ficha['Ficha']['data_inclusao'];

					echo "Ficha: $codigo_ficha (".$data_inclusao." )\n";
					$msg_erro = 'Nao foi possivel remover LiberacaoFicha da ficha: '.$codigo_ficha;
					if( !$this->removeFichaLiberacao( $codigo_ficha ) )
						throw new Exception($msg_erro);
					
					$msg_erro = 'Nao foi possivel remover FichaPesquisa da ficha: '.$codigo_ficha;
					if( !$this->removeFichaPesquisa( $codigo_ficha ) )
						throw new Exception($msg_erro);
					
					$msg_erro = 'Nao foi possivel remover FichaProfissionalContatoLog da ficha: '.$codigo_ficha;
					if( !$this->removeProfissionalContatoLog( $codigo_ficha ) )
						throw new Exception($msg_erro);

					$msg_erro = 'Nao foi possivel remover ProprietarioContatoLog da ficha: '.$codigo_ficha;
					if( !$this->removeProprietarioContatoLog( $codigo_ficha ) )
						throw new Exception($msg_erro);

					$msg_erro = 'Nao foi possivel remover ProfissionalEnderecoLog da ficha: '.$codigo_ficha;
					if( !$this->removeProfissionalEnderecoLog( $codigo_ficha ) )
						throw new Exception($msg_erro);

					$msg_erro = 'Nao foi possivel remover ProprietarioEnderecoLog da ficha: '.$codigo_ficha;
					if( !$this->removeProprietarioEnderecoLog( $codigo_ficha ) )
						throw new Exception($msg_erro);

					$msg_erro = 'Nao foi possivel remover QuestaoResposta da ficha: '.$codigo_ficha;
					if( !$this->removeQuestaoResposta( $codigo_ficha ) )
						throw new Exception($msg_erro);

					$msg_erro = 'Nao foi possivel remover FichaRetorno da ficha: '.$codigo_ficha;
					if( !$this->removeFichaRetorno( $codigo_ficha ) )
						throw new Exception($msg_erro);

					$msg_erro = 'Nao foi possivel remover FichaVeiculo da ficha: '.$codigo_ficha;
					if( !$this->removeFichaVeiculo( $codigo_ficha ) )
						throw new Exception($msg_erro);

					$msg_erro = 'Nao foi possivel remover FichaCt da ficha: '.$codigo_ficha;
					if( !$this->removeFichaCt( $codigo_ficha ) )
						throw new Exception($msg_erro);

					$msg_erro = 'Nao foi possivel remover LogFaturamento da ficha: '.$codigo_ficha;
					if( !$this->removeLogFaturamento( $codigo_ficha ) )
						throw new Exception($msg_erro);

					$msg_erro = 'Nao foi possivel remover FichaForense da ficha: '.$codigo_ficha;
					if( !$this->removeFichaForense( $codigo_ficha ) )
						throw new Exception($msg_erro);

					$msg_erro = 'Nao foi possivel remover a ficha: '.$codigo_ficha;
					if( !$this->Ficha->delete($codigo_ficha))
						throw new Exception($msg_erro);
					
					$this->Ficha->commit();
				} catch(Exception $e) {
					print_r( $e->getMessage() );
					$this->Ficha->rollback();
				}	
			}
			echo "\n\n";
			echo 'FIM: '. date("d/m/Y H:i:s");		
		}
	}

	/*
	*LIBERAÇÃO ITEM & LIBERAÇÃO
	*/	
	public function removeFichaLiberacao( $codigo_ficha ){
		$query  = "DELETE FROM dbTeleconsult.informacoes.ficha_liberacao_item WHERE codigo_ficha_liberacao IN ( ";
		$query .= "  SELECT codigo FROM dbTeleconsult.informacoes.ficha_liberacao WHERE codigo_ficha IN ( $codigo_ficha ) ";
		$query .= ");";
		$this->FichaPesquisa->query($query);
		echo "$query\n";
		$query = "DELETE FROM dbTeleconsult.informacoes.ficha_liberacao WHERE codigo_ficha IN( $codigo_ficha )";		
		$this->FichaPesquisa->query($query);
		echo "$query\n";
		return true;
	}

	/* 
	*QUESTÃO RESPOSTA & FICHA PESQUISA
	*/

	public function removeFichaPesquisa( $codigo_ficha ){
		$this->FichaPesquisa = ClassRegistry::init('FichaPesquisa');
		$fields 		= array('FichaPesquisa.codigo');
		$conditions 	= array('FichaPesquisa.codigo_ficha' => $codigo_ficha );
		$ficha_pesquisa = $this->FichaPesquisa->find('all', compact('conditions', 'fields' ));
		if( $ficha_pesquisa ){
			foreach ($ficha_pesquisa as $key => $dados ) {
				if( $dados['FichaPesquisa']['codigo'] ){
					$codigo_ficha_pesquisa = $dados['FichaPesquisa']['codigo'];
					// QUESTÃO RESPOSTA 
					$query = "DELETE FROM dbTeleconsult.informacoes.ficha_pesquisa_questao_resposta WHERE codigo_ficha_pesquisa IN( $codigo_ficha_pesquisa )";
					$this->FichaPesquisa->query($query);
					echo "$query\n";
					// ARTIGO CRIMINAL
					$query = "DELETE FROM dbTeleconsult.informacoes.ficha_pesquisa_artigo_criminal WHERE codigo_ficha_pesquisa IN( $codigo_ficha_pesquisa )";
					$this->FichaPesquisa->query($query);
					echo "$query\n";
					// FICHA INFORMAÇÃO INSUFICIENTE
					$query = "DELETE FROM dbTeleconsult.informacoes.ficha_informacao_insuficiente WHERE codigo_ficha_pesquisa IN( $codigo_ficha_pesquisa )";
					$this->FichaPesquisa->query($query);
					echo "$query\n";
					// FICHA PESQUISA
					if( !$this->FichaPesquisa->delete( $codigo_ficha_pesquisa ) )
						return false;
				}
			}
		}
		return true;
	}

	public function removeProfissionalContatoLog( $codigo_ficha ){
		$query = "DELETE FROM dbTeleconsult.informacoes.ficha_profissional_contato_log WHERE codigo_ficha IN( $codigo_ficha )";
		$this->FichaPesquisa->query($query);
		echo "$query\n";
		return true;
	}

	public function removeProprietarioContatoLog( $codigo_ficha ){
		$query = "DELETE FROM dbTeleconsult.informacoes.ficha_proprietario_contato_log WHERE codigo_ficha IN( $codigo_ficha )";
		$this->FichaPesquisa->query($query);
		echo "$query\n";
		return true;
	}

	public function removeProfissionalEnderecoLog( $codigo_ficha ){
		$query = "DELETE FROM dbTeleconsult.informacoes.ficha_profissional_endereco_log WHERE codigo_ficha IN( $codigo_ficha )";
		$this->FichaPesquisa->query($query);
		echo "$query\n";
		return true;
	}

	public function removeProprietarioEnderecoLog( $codigo_ficha ){
		$query = "DELETE FROM dbTeleconsult.informacoes.ficha_proprietario_endereco_log WHERE codigo_ficha IN( $codigo_ficha )";
		$this->FichaPesquisa->query($query);
		echo "$query\n";
		return true;
	}


	public function removeQuestaoResposta( $codigo_ficha ){
		$query = "DELETE FROM dbTeleconsult.informacoes.ficha_questao_resposta WHERE codigo_ficha IN( $codigo_ficha )";
		$this->FichaPesquisa->query($query);
		echo "$query\n";
		return true;
	}

	public function removeFichaRetorno( $codigo_ficha ){
		$query = "DELETE FROM dbTeleconsult.informacoes.ficha_retorno WHERE codigo_ficha IN( $codigo_ficha )";
		$this->FichaPesquisa->query($query);
		echo "$query\n";
		return true;
	}

	public function removeFichaVeiculo( $codigo_ficha ){
		$query = "DELETE FROM dbTeleconsult.informacoes.ficha_veiculo WHERE codigo_ficha IN( $codigo_ficha )";
		$this->FichaPesquisa->query($query);
		echo "$query\n";
		return true;
	}

	public function removeFichaCt( $codigo_ficha ){
		$query  = "DELETE FROM dbTeleconsult.informacoes.ficha_ct_log WHERE codigo_ct IN ( ";
		$query .= "  SELECT codigo FROM dbTeleconsult.informacoes.ficha_ct WHERE codigo_ficha IN ( $codigo_ficha ) ";
		$query .= ");";
		echo "$query\n";
		$this->FichaPesquisa->query($query);
		$query = "DELETE FROM dbTeleconsult.informacoes.ficha_ct WHERE codigo_ficha IN( $codigo_ficha )";		
		echo "$query\n";
		$this->FichaPesquisa->query($query);		
		return true;
	}


	public function removeLogFaturamento( $codigo_ficha ){
		$query  = "DELETE FROM dbTeleconsult.informacoes.log_consulta_status_profissional WHERE codigo_log_faturamento IN ( ";
		$query .= "  SELECT codigo FROM dbTeleconsult.informacoes.log_faturamento WHERE codigo_ficha IN ( $codigo_ficha ) ";
		$query .= ");";
		$this->FichaPesquisa->query($query);
		echo "$query\n";
		$query = "DELETE FROM dbTeleconsult.informacoes.log_faturamento WHERE codigo_ficha IN( $codigo_ficha )";		
		$this->FichaPesquisa->query($query);
		echo "$query\n";
		return true;
	}

	public function removeFichaForense( $codigo_ficha ){
		$query = "DELETE FROM dbTeleconsult.informacoes.ficha_forense WHERE codigo_ficha IN( $codigo_ficha )";
		$this->FichaPesquisa->query($query);
		echo "$query\n";
		return true;
	}

// /* LOG ATENDIMENTO */
// DELETE FROM informacoes.log_atendimento WHERE data_inclusao BETWEEN DATEADD(DAY, -2 , DATEADD(YEAR, -3 , GETDATE())) AND DATEADD(YEAR, -3 , GETDATE())

// /* RENOVAÇÃO AUTOMÁTICA */
// DELETE FROM informacoes.renovacao_automatica WHERE data_inclusao BETWEEN DATEADD(DAY, -2 , DATEADD(YEAR, -3 , GETDATE())) AND DATEADD(YEAR, -3 , GETDATE())


}
?>