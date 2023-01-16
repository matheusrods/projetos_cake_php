<?php
class DistribuidorShell extends Shell {

	var $uses 	= array('TViagViagem','TCdisCriterioDistribuicao','TUsuaUsuario','TVusuViagemUsuario','TLdisLogDistribuicao','TConfConfiguracao');

	function main() {
		echo "==================================================\n";
		echo "* Distribuidor \n";
		echo "* \n";
		echo "* Distribui viagens não finalizadas entre usuarios(operadores) logados ao Guardian e com o Monitor GR aberto\n";
		echo "==================================================\n\n";

		echo "=> is_alive: verifica se o escript esta sendo executado\n";
		echo "=> sincronizar: sincroniza as viagens que não tenham registro de monitoramento\n";
		echo "=> liberar: verifica os operadores deslogados e para redistribuir suas viagens\n";
		echo "=> recuperar: recupera vigens de operadores que retornam ao Monitor GR\n";
		echo "=> distribuir: distribui viagens entre operadores, respeitando criterios de distribuicao e area de atuacao\n";
		echo "=> inicializar: executa sincronamente os processos de liberar, recuperar e distribuir \n";
		echo "=> reinicializar: libera temporariamente todas as viagens e chama o inicializar\n";
		echo "=> redistribuir: libera definitivamente todas as viagens e chama o inicializar\n";
		echo "=> deslogar [codigo_usuario]: libera temporariamente as viagens de um operador e chama o inicializar\n";
		echo "=> usuario [codigo_usuario]: libera definitivamente as viagens de um operador e chama o inicializar\n";

		echo "\n";
	}

	function is_alive(){
			$retorno = shell_exec("ps -ef | grep \"distribuidor \" | wc -l");
			return ($retorno > 3);
	}

	function atualizaOperador(){
		$operador = $this->TUsuaUsuario->carregar(TUsuaUsuario::operadorPadrao());
		$operador['TUsuaUsuario']['usua_heart_beat'] = date('Ymd H:i:s');
		$this->TUsuaUsuario->atualizar($operador);

		$operador = $this->TUsuaUsuario->carregar(TUsuaUsuario::naoMonitorado());
		$operador['TUsuaUsuario']['usua_heart_beat'] = date('Ymd H:i:s');
		$this->TUsuaUsuario->atualizar($operador);
	}

	function sincronizar(){
		$viagens = $this->TViagViagem->listarVusuInlcuir();

		if($viagens){
			foreach ($viagens as $viagem) {
				
				$vusu = array(
					'TVusuViagemUsuario' => array(
						'vusu_viag_codigo' 				=> $viagem['TViagViagem']['viag_codigo'],
						'vusu_usua_oras_codigo'			=> TUsuaUsuario::naoMonitorado(),
						'vusu_ultimo_usua_oras_codigo'	=> TUsuaUsuario::naoMonitorado(),
					),
				);

				$this->TVusuViagemUsuario->save($vusu);
			}
		}

		echo "- FIM DA SINCRONIZACAO\n";
	}

	function liberar(){
		$this->atualizaOperador();
		$this->TVusuViagemUsuario->liberadorDeSmParaDistribuidor();
		echo "- FIM DA LIBERACAO\n";
	}

	function recuperar(){
		$this->TVusuViagemUsuario->recuperarSmPerdida();
		echo "- FIM DA RECUPERACAO\n";
	}

	function operadorPadrao($viagem){
		$operador = $this->TUsuaUsuario->carregar(TUsuaUsuario::operadorPadrao());
		$ldis['TLdisLogDistribuicao']['ldis_usua_oras_codigo'] = $operador['TUsuaUsuario']['usua_pfis_pess_oras_codigo'];
		if(!$this->atualizar_vusu($viagem,$operador))
			return FALSE;

		return TRUE;
	}

	function atualizar_vusu(&$vusu,&$operador){
	
		$vusu['TVusuViagemUsuario']['vusu_usua_oras_codigo'] = $operador['TUsuaUsuario']['usua_pfis_pess_oras_codigo'];
		$vusu['TVusuViagemUsuario']['vusu_data_cadastro'] = date('Ymd H:i:s');
		if($vusu['TVusuViagemUsuario']['vusu_ultimo_usua_oras_codigo'] == TUsuaUsuario::naoMonitorado())
			$vusu['TVusuViagemUsuario']['vusu_ultimo_usua_oras_codigo'] = $operador['TUsuaUsuario']['usua_pfis_pess_oras_codigo'];

		return $this->TVusuViagemUsuario->atualizar($vusu);
			
	}

	function enviar_email($datas){
		if (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO) {
			$email_addess = $this->TConfConfiguracao->emailDistribuidor();

			if($email_addess && $datas){
				App::import('Component', array('StringView', 'Mailer.Scheduler'));
				$this->StringView = new StringViewComponent();
				$this->Scheduler  = new SchedulerComponent();

				foreach ($datas as $data) {
					
					$this->StringView->reset();
					$this->StringView->set(compact('data'));
					$content = $this->StringView->renderMail('email_distribuidor', 'default');
					$options = array(
						'from' 		=> 'portal@buonny.com.br',
						'sent' 		=> null,
						'to'   		=> $email_addess,
						'subject' 	=> 'Problemas com distribuidor automatico',
					);

					$this->Scheduler->schedule($content, $options);
				}
				
			}
		}

	}


	function distribuir(){
		if($this->is_alive())
			return FALSE;

		$data_inicio = time();
		echo "=> CONSULTA:\n\n";

		$viagens 	 = $this->TVusuViagemUsuario->listarViagemLivre();
		
		$this->meu_tempo($data_inicio);
		echo "\n\n";

		$data_inicio = time();
		$total_viagem= count($viagens);
		$total_distr = 0;
		$total_erro  = 0;

		$data 		 = array();

		if($viagens){
			$mail 		 = 0;
			
			foreach ($viagens as $key => $viagem) {
				if(!isset($data[$mail]))
					$data[$mail] = array();	

				$data[$mail][$key]['codigo_sm'] = $viagem['TViagViagem']['viag_codigo_sm'];
				$data[$mail][$key]['descricao'] = NULL;

				$ldis = array(
					'TLdisLogDistribuicao' => array(
						'ldis_vusu_viag_codigo'	=> $viagem['TVusuViagemUsuario']['vusu_viag_codigo'],
						'ldis_tipo'				=> 'D',
						'ldis_usuario_adicionou'=> 'DISTRIBUIDOR',
					),
				);

				$criterio = $this->TCdisCriterioDistribuicao->carregarPorViagem($viagem);
				if($criterio){
					
					$ldis['TLdisLogDistribuicao']['ldis_cdis_codigo'] = $criterio['TCdisCriterioDistribuicao']['cdis_codigo'];
					$ldis['TLdisLogDistribuicao']['ldis_aatu_codigo'] = $criterio['TCdisCriterioDistribuicao']['cdis_aatu_codigo'];

					$operador = $this->TUsuaUsuario->carregarOperadorAatu($criterio['TCdisCriterioDistribuicao']['cdis_aatu_codigo']);
					$operador_sec = $this->TUsuaUsuario->carregarOperadorAatu($criterio['TCdisCriterioDistribuicao']['cdis_aatu_codigo_sec']);
					$operador_ter = $this->TUsuaUsuario->carregarOperadorAatu($criterio['TCdisCriterioDistribuicao']['cdis_aatu_codigo_ter']);

					if($operador){

						$operador[0]['qtd']++;
						if($operador[0]['qtd'] > $criterio['TCdisCriterioDistribuicao']['cdis_max_sm']){
							$data[$mail][$key]['descricao'] = "Sobrecarga do operador {$operador['TUsuaUsuario']['usua_login']}. Limite maximo {$criterio['TCdisCriterioDistribuicao']['cdis_max_sm']}, quantidade atual {$operador[0]['qtd']}.";
							$ldis['TLdisLogDistribuicao']['ldis_observacao'] = "Sobrecarga do operador {$operador['TUsuaUsuario']['usua_login']}. Limite maximo {$criterio['TCdisCriterioDistribuicao']['cdis_max_sm']}, quantidade atual {$operador[0]['qtd']}.";
						}

						$ldis['TLdisLogDistribuicao']['ldis_usua_oras_codigo'] = $operador['TUsuaUsuario']['usua_pfis_pess_oras_codigo'];
						if($this->atualizar_vusu($viagem,$operador)){
							$total_distr++;
							if(!isset($data[$mail][$key]['descricao']) && !$data[$mail][$key]['descricao'])
								unset($data[$mail][$key]);
						} else {
							$ldis = FALSE;
							$total_erro++;
						}
							
					} elseif ($operador_sec) {
						$ldis['TLdisLogDistribuicao']['ldis_aatu_codigo'] = $criterio['TCdisCriterioDistribuicao']['cdis_aatu_codigo_sec'];
						$operador_sec[0]['qtd']++;
						if($operador_sec[0]['qtd'] > $criterio['TCdisCriterioDistribuicao']['cdis_max_sm']){
							$data[$mail][$key]['descricao'] = "Sobrecarga do operador {$operador_sec['TUsuaUsuario']['usua_login']}. Limite maximo {$criterio['TCdisCriterioDistribuicao']['cdis_max_sm']}, quantidade atual {$operador_sec[0]['qtd']}.";
							$ldis['TLdisLogDistribuicao']['ldis_observacao'] = "Sobrecarga do operador {$operador_sec['TUsuaUsuario']['usua_login']}. Limite maximo {$criterio['TCdisCriterioDistribuicao']['cdis_max_sm']}, quantidade atual {$operador_sec[0]['qtd']}.";
						}

						$ldis['TLdisLogDistribuicao']['ldis_usua_oras_codigo'] = $operador_sec['TUsuaUsuario']['usua_pfis_pess_oras_codigo'];
						if($this->atualizar_vusu($viagem,$operador_sec)){
							$total_distr++;
							if(!isset($data[$mail][$key]['descricao']) && !$data[$mail][$key]['descricao'])
								unset($data[$mail][$key]);
						} else {
							$ldis = FALSE;
							$total_erro++;
						}
					} elseif ($operador_ter) {
						$ldis['TLdisLogDistribuicao']['ldis_aatu_codigo'] = $criterio['TCdisCriterioDistribuicao']['cdis_aatu_codigo_ter'];
						$operador_ter[0]['qtd']++;
						if($operador_ter[0]['qtd'] > $criterio['TCdisCriterioDistribuicao']['cdis_max_sm']){
							$data[$mail][$key]['descricao'] = "Sobrecarga do operador {$operador_ter['TUsuaUsuario']['usua_login']}. Limite maximo {$criterio['TCdisCriterioDistribuicao']['cdis_max_sm']}, quantidade atual {$operador_ter[0]['qtd']}.";
							$ldis['TLdisLogDistribuicao']['ldis_observacao'] = "Sobrecarga do operador {$operador_ter['TUsuaUsuario']['usua_login']}. Limite maximo {$criterio['TCdisCriterioDistribuicao']['cdis_max_sm']}, quantidade atual {$operador_ter[0]['qtd']}.";
						}

						$ldis['TLdisLogDistribuicao']['ldis_usua_oras_codigo'] = $operador_ter['TUsuaUsuario']['usua_pfis_pess_oras_codigo'];
						if($this->atualizar_vusu($viagem,$operador_ter)){
							$total_distr++;
							if(!isset($data[$mail][$key]['descricao']) && !$data[$mail][$key]['descricao'])
								unset($data[$mail][$key]);
						} else {
							$ldis = FALSE;
							$total_erro++;
						}
					} else {

						$total_erro++;
						$data[$mail][$key]['descricao'] = 'Não há operadores disponíveis para atender à viagem.';
						$ldis['TLdisLogDistribuicao']['ldis_observacao'] = 'Não há operadores disponíveis para atender à viagem.';
					}

				} else {
					if($this->operadorPadrao($viagem)){
						$ldis['TLdisLogDistribuicao']['ldis_usua_oras_codigo'] = TUsuaUsuario::operadorPadrao();
						$total_distr++;
						unset($data[$mail][$key]);
					} else {
						$total_erro++;
						$data[$mail][$key]['descricao'] = 'A viagem não atende a nenhum critério.';
						$ldis['TLdisLogDistribuicao']['ldis_observacao'] = 'A viagem não atende a nenhum critério.';
					}
				}	
				
				if($ldis)$this->TLdisLogDistribuicao->incluir($ldis);

				if(count($data[$mail]) > 499)$mail++;
			}
		}

		$this->enviar_email($data);

		echo "=> DISTRIBUICAO:\n\n";
		$this->meu_tempo($data_inicio);

		echo "\n\n";
		echo "TOTAL VIAGENS: {$total_viagem} - VIAGENS DISTRIBUIDAS: {$total_distr} - FALHA NA DISTRIBUICAO: {$total_erro}";
		echo "\n";
		
	}

	function inicializar(){
		if($this->is_alive())
			return FALSE;

		$data_inicio = time();
		$this->sincronizar();

		$this->liberar();

		$this->recuperar();

		echo "\n------------------------------------------------------------\n";
		$this->distribuir();
		echo "\n------------------------------------------------------------\n";

		echo "=> PROCESSO:\n\n";
		$this->meu_tempo($data_inicio);

		echo "\n\n";
	}

	function reinicializar(){
		if($this->is_alive())
			return FALSE;

		if($this->TVusuViagemUsuario->redistribuirViagens(NULL,FALSE)){
			echo "- FIM DA REINICIALIZACAO\n";
			$this->inicializar();
		} else {
			echo "- FALHA NA REINICIALIZACAO\n";
		}
	}

	function meu_tempo($data_inicio){
		$data_fim = time();
		echo "INICIOU AS: ".date('d/m/Y H:i:s',$data_inicio)." ";
		echo "FINALIZOU AS: ".date('d/m/Y H:i:s',$data_fim)."\n\n";
		$diff = Comum::diffDate($data_inicio,$data_fim);
		echo "DURACAO:";
		echo " {$diff['hora']} horas,";
		echo " {$diff['min']} minutos,";
		echo " {$diff['seg']} segundos";
	}

	function redistribuir(){
		if($this->is_alive())
			return FALSE;

		if($this->TVusuViagemUsuario->redistribuirViagens()){
			echo "- FIM DA REDISTRIBUICAO\n";
			$this->inicializar();
		} else {
			echo "- FALHA NA REDISTRIBUICAO\n";
		}
	}

	function deslogar(){
		if($this->is_alive())
			return FALSE;

		if(isset($this->args[0]) && (int)$this->args[0] != 0){
			$usuaUsuario = $this->TUsuaUsuario->carregar((int)$this->args[0]);
			if($usuaUsuario){
				if($this->TVusuViagemUsuario->redistribuirViagens($usuaUsuario['TUsuaUsuario']['usua_pfis_pess_oras_codigo'],FALSE)){
					echo "- FIM DA REDISTRBUICAO\n";
					$this->inicializar();
				} else {
					echo "- FALHA NA REDISTRBUICAO\n";
				}

			} else {
				echo "- USUARIO NAO LOCALIZADO!\n\n";
			}
		} else {
			echo "- USUARIO NAO INFORMADO!\n\n";
		}
	}

	function usuario(){
		if($this->is_alive())
			return FALSE;

		if(isset($this->args[0]) && (int)$this->args[0] != 0){
			$usuaUsuario = $this->TUsuaUsuario->carregar((int)$this->args[0]);
			if($usuaUsuario){
				if($this->TVusuViagemUsuario->redistribuirViagens($usuaUsuario['TUsuaUsuario']['usua_pfis_pess_oras_codigo'])){
					echo "- FIM DA REDISTRBUICAO DO USUARIO\n";
					$this->inicializar();
				} else {
					echo "- FALHA NA REDISTRBUICAO DO USUARIO\n";
				}

			} else {
				echo "- USUARIO NAO LOCALIZADO!\n\n";
			}
		} else {
			echo "- USUARIO NAO INFORMADO!\n\n";
		}
	}
}
?>
