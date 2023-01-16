<?php
App::import('Lib', 'AppShell');
class DisparaSmsOutboxShell extends AppShell {
	var $uses = array('SmsOutbox', 'Modem');

	function main() {
		echo "==================================================\n\n";
		echo "=> dispara_sms => Dispara Sms Outbox. \n\n";
	}//FINAL FUNCTION main

	function run() {
		if (!$this->im_running('dispara_sms'))
        	$this->dispara_sms();
    }//FINAL FUNCTION run

	private function im_running($tipo) {
		$cmd = shell_exec("ps aux | grep '{$tipo}'");
		// 1 execução é a execução atual
		return substr_count($cmd, 'cake.php -working') > 1;
	}//FINAL FUNCTION im_running

	//Defini as origens que podem enviar sms como short
	private static $origemPermitidasParaEnviarSms = array(
		"sompodriver",
		"sompoLog",
		"buonnydriver",
		"gestaoentrega",
		"buonnydelivery",
		"MUNDOFRETE",
		"gestaoentregascba",
	);

	/**
	 * verificaQualTipoSmsEnvia
	 *
	 * @param string $origem
	 *
	 * @return bool
	 */
	public function verificaQualTipoSmsEnvia($origem){
		if(in_array($origem, self::$origemPermitidasParaEnviarSms)){
			//Se o sistema origem for app, envia pelo SHORT
			$tipoSms = 'SHORT';
		} else {
			//Se o sistema origem for qualquer outro envia pelo LONG
			$tipoSms = 'LONG';
		}
		return $tipoSms;
	}//FINAL FUNCTION verificaQualTipoSmsEnvia

	/**
	 * dispara_sms - Rotina usada para fazer o envio do sms
	 * Pega todos os sms que ainda não foram enviados na tabela de sms
	 *
	 * /home/sistema/portal/portal/cake/console/cake -app /home/fernandosouza/portal/portal/app dispara_sms_outbox run
	 */
	function dispara_sms(){
		//Pega todos os sms da tabela dbComunicacao.dbo.sms_outbox que não foram enviados
		$conditions = array(
			'data_envio' => NULL
		);
		$lista = $this->SmsOutbox->find(
			'all',
			array(
				'conditions' => $conditions,
				'limit' => 1000,
				'order' => 'data_inclusao ASC'
			)
		);
		//debug($lista); exit;
		foreach ($lista as $dados) {
			if(empty($dados['SmsOutbox']['liberar_envio_em']) || $dados['SmsOutbox']['liberar_envio_em'] <= date('d/m/Y H:i:s')){
				$base_url 	= false;
				$telefone 	= $dados['SmsOutbox']['fone_para'];
				$msg      	= urlencode($dados['SmsOutbox']['mensagem']);
				$modem   	= false;
				$vonex		= true;
				$tipoSms	= $this->verificaQualTipoSmsEnvia($dados['SmsOutbox']['sistema_origem']);
				$log_msg	= '';

				if(!empty($dados['SmsOutbox']['fone_de'])){
					switch ($dados['SmsOutbox']['fone_de']) {
						case Modem::MODEM_1:
							//if ($dados['SmsOutbox']['sistema_origem']=='buonnydriver') {
								$modem = '25%231';
								$vonex = false;
							//}
							break;
				// 		case Modem::MODEM_2:
				// 			$modem = '25%232';
				// 			break;
				// 		//Pausa solicitada pelo Sr Nelson Ota.
				// 		case Modem::MODEM_3:
				// 		 	$modem = '25%233';
				// 			 break;
				// 		case Modem::MODEM_4:
				// 			$modem = '25%234';
				// 			break;
						case Modem::VONEX:
							$vonex = true;
							break;
						default:
							$vonex = true;
							//$modem = '25%23'.rand(1,2);
					}
				// }else{
				// 	$modem = '25%23'.rand(1,2);
				}

				if ($modem || $vonex) {
					$dados['SmsOutbox']['data_envio'] = date('Y-m-d H:i:s');
					if($modem){
						$retorno = $this->carregaUrl($msg, $telefone, $modem);
						$log_msg = !$retorno ? 'Erro ao enviar o SMS de Código'.$dados['SmsOutbox']['codigo'].'dispara_sms' : null;
					} else {
						$retorno = $this->SmsOutbox->enviarVonex(urldecode($msg), $telefone, $tipoSms);
						if(!$retorno->success){
							$log_msg = "Erro ao enviar pelo servico Vonex: \n".$retorno->mensagem. " do tipo ". $tipoSms." \n";
						}else{
							echo "Mensagem enviada com sucesso (Vonex)". " do tipo ". $tipoSms." \n";
						}
						$retorno = $retorno->success;
					}
				} else {
					$dados['SmsOutbox']['data_envio'] = '2000-02-01 00:00:00';
					$retorno = true;
				}

				if ($retorno) {
					$this->SmsOutbox->atualizar($dados);
				} else {
					$dados['SmsOutbox']['data_envio'] = '2000-02-01 00:00:00';
					$this->SmsOutbox->atualizar($dados);
					$this->log($log_msg, 'sms_erro_envio');
				}
			}//end if empty liberar_envio_em
		}//end foreach
	}//FINAL FUNCTION dispara_sms

	function carregaUrl($msg, $telefone, $modem) {
		$url	= "http://10.10.70.66/cb/sms_http.php?server_password=admin&msg={$msg}&number={$telefone}&send_to_sim={$modem}";
        if (function_exists('curl_init')) {
            $cURL = curl_init($url);
            curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, FALSE);
            $resultado = curl_exec($cURL);
            $this->log(var_export($resultado,true),'sms_chile');
            curl_close($cURL);
        } else {
        	$aContext = array(
			    'https' => array(
			        'proxy' => 'tcp://siena.local.buonny:3128',
			        'request_fulluri' => true,
			    )
			);
			$cxContext = stream_context_create($aContext);
			$resultado = file_get_contents($url, False, $cxContext);
        }

        if (!$resultado) {
            return false;
        } else {
            return true;
        }
    }//FINAL FUNCTION carregaUrl

}//FINAL CLASS DisparaSmsOutboxShell