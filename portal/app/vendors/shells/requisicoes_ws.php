<?php
App::import('Component', 'Soap');

class RequisicoesWsShell extends Shell {
	
	var $soapComponent = null;
	
	function main() {
	}
    
	function run() {
	    if (!$this->im_running())
		    $this->processaTodos(300);
		else
		    $this->out('Já está rodando. Saindo.');
	}
	
	function limpaHistorico() {
		$this->WsOutbox = ClassRegistry::init('WsOutbox');
		$this->WsOutbox->limpaHistorico();
	}
	
	function processaTodos($count) {
        $maxRetries = $count;
        $this->WsOutbox = ClassRegistry::init('WsOutbox');
        $this->WsConfiguracao = ClassRegistry::init('WsConfiguracao');

        $eventos = $this->WsOutbox->proximosNaoEnviados($count);
        foreach($eventos as $evento) {
            $sucesso = false;
            while (!$sucesso && ($maxRetries-- > 0)) {
               	$sucesso = $this->processa($evento, $this->WsConfiguracao->localizaConfiguracao($evento['WsOutbox']['codigo_documento'], $evento['WsOutbox']['tipo_mensagem']));
			}
        }
  }

	function processa($evento, $config) {
		if ($config){
			if($evento['WsOutbox']['codigo_documento'] == '47508411083264'){
				return $this->enviaGpa($evento, $config);
			}else{
				if($evento['WsOutbox']['codigo_documento'] == '03094658000793'){
					return $this->enviaGefco($evento, $config);
				}else{
					return $this->envia($evento, $config);				
				}
			}
		}else{
			return $this->exclui($evento);
		}
	}

	function enviaGpa($evento, $config) {
		$envelope = $this->WsOutbox->asEnvelope($evento);
		try {
			$options = array(
				'local_cert' => CERTIFICATE_FILE_PATH,
			);
			$tentativas = $this->WsOutbox->adicionarTentativa($evento['WsOutbox']['codigo']);
			$response = $this->soapComponent()->request($config['WsConfiguracao']['soap_url'], $config['WsConfiguracao']['soap_funcao'], $envelope, $options);
			if ($response->EVENTO->CD_ERRO == SoapComponent::SUCESSO) {
				$this->gravaLogEnvio($evento['WsOutbox']['codigo_documento'],$envelope,$response->EVENTO,'S');
				return $this->WsOutbox->marcarEnviado($evento['WsOutbox']['codigo']);
			} elseif ($response->EVENTO->CD_ERRO == SoapComponent::DESCARTADO_CLIENTE) {
				$this->gravaLogEnvio($evento['WsOutbox']['codigo_documento'],$envelope,$response->EVENTO,'N');
			  return $this->WsOutbox->marcarEnviado($evento['WsOutbox']['codigo']);
			} else {
				$ret = false;
				if ($tentativas>=5) {
					$ret = $this->WsOutbox->marcarNaoEnviado($evento['WsOutbox']['codigo']);
					$this->gravaLogEnvio($evento['WsOutbox']['codigo_documento'],$envelope,$response->EVENTO,'N');
				}
				$this->log($envelope, 'ws_errors');
				$this->log($response, 'ws_errors');
				return $ret;
			}
		} catch (Exception $ex) {
			$ret = false;
			if ($tentativas>=5) {
				$ret = $this->WsOutbox->marcarNaoEnviado($evento['WsOutbox']['codigo']);
				$this->gravaLogEnvio($evento['WsOutbox']['codigo_documento'],$envelope,Array('erro'=>$ex->getMessage()),'N');
			}
			$this->log($envelope, 'ws_errors');
			$this->log($ex->getMessage(), 'ws_errors');
			return $ret;
		}
	}

	function enviaGefco($evento, $config) {
		$envelope = $this->WsOutbox->converteMensagemEmEnvelope($evento, true);
		try {
			$tentativas = $this->WsOutbox->adicionarTentativa($evento['WsOutbox']['codigo']);
			$response = $this->soapComponent()->request($config['WsConfiguracao']['soap_url'], $config['WsConfiguracao']['soap_funcao'], $envelope);
			if ($response->RECEBEREVENTORESULT->SUCESSO == SoapComponent::SUCESSO){
				$this->gravaLogEnvio($evento['WsOutbox']['codigo_documento'],$envelope,$response->RECEBEREVENTORESULT,'S');
				return $this->WsOutbox->marcarEnviado($evento['WsOutbox']['codigo']);
			}else{
				$ret = false;
				if ($tentativas>=5) {
					$ret = $this->WsOutbox->marcarNaoEnviado($evento['WsOutbox']['codigo']);
					$this->gravaLogEnvio($evento['WsOutbox']['codigo_documento'],$envelope,$response->RECEBEREVENTORESULT,'N');
				}
				$this->log($envelope, 'ws_errors');
				$this->log($response, 'ws_errors');
				return $ret;
			}
		} catch (Exception $ex) {
			$ret = false;
			if ($tentativas>=5) {
				$ret = $this->WsOutbox->marcarNaoEnviado($evento['WsOutbox']['codigo']);
				$this->gravaLogEnvio($evento['WsOutbox']['codigo_documento'],$envelope,Array('erro'=>$ex->getMessage()),'N');
			}
			$this->log($envelope, 'ws_errors');
			$this->log($ex->getMessage(), 'ws_errors');
			return $ret;
		}
	}

	function envia($evento, $config) {
		//$envia_pedido = ($evento['WsOutbox']['codigo_documento']=='02012862000917');
		$envelope = $this->WsOutbox->converteMensagemEmEnvelope($evento,false);
		try {
			$tentativas = $this->WsOutbox->adicionarTentativa($evento['WsOutbox']['codigo']);
			$response = $this->soapComponent()->request($config['WsConfiguracao']['soap_url'], $config['WsConfiguracao']['soap_funcao'], $envelope,Array(),$config['WsConfiguracao']['soap_param']);
			if ($response->evento_result->sucesso == SoapComponent::SUCESSO){
				$this->gravaLogEnvio($evento['WsOutbox']['codigo_documento'],$envelope,$response->evento_result,'S');
				return $this->WsOutbox->marcarEnviado($evento['WsOutbox']['codigo']);
			}else{
				$ret = false;
				if ($tentativas>=5) {
					$ret = $this->WsOutbox->marcarNaoEnviado($evento['WsOutbox']['codigo']);
					$this->gravaLogEnvio($evento['WsOutbox']['codigo_documento'],$envelope,$response->evento_result,'N');
				}
				$this->log($envelope, 'ws_errors');
				$this->log($response, 'ws_errors');
				return $ret;
			}
		} catch (Exception $ex) {
			$ret = false;
			if ($tentativas>=5) {
				$ret = $this->WsOutbox->marcarNaoEnviado($evento['WsOutbox']['codigo']);
				$this->gravaLogEnvio($evento['WsOutbox']['codigo_documento'],$envelope,Array('erro'=>$ex->getMessage()),'N');
			}
			$this->log($envelope, 'ws_errors');
			$this->log($ex->getMessage(), 'ws_errors');
			return $ret;
		}
	}
	
	function gravaLogEnvio($codigo_documento, $envelope, $response = null, $enviado = 'S') {
		$this->LogIntegracaoOutbox = ClassRegistry::init('LogIntegracaoOutbox');
		$this->Cliente = ClassRegistry::init('Cliente');
		$xml = new SimpleXMLElement("<?xml version=\"1.0\"?><INTEGRACAO_EVENTO></INTEGRACAO_EVENTO>");
		
		$dados = $this->Cliente->carregarPorDocumento($codigo_documento,Array('codigo'));
		$codigo_cliente = $dados['Cliente']['codigo'];

		$codigo_sm = (isset($envelope->EVENTO->sm) ? $envelope->EVENTO->sm : $envelope->EVENTO->SM);
		
		$xml_envio = $xml->addChild('ENVIO');
		$xml_envio_evento = $xml_envio->addChild('EVENTO');
		foreach ($envelope->EVENTO as $key => $value) {
			$xml_envio_evento->addChild($key,(!empty($value) ? $value : null));
		}

		if (!empty($response)) {
			$xml_response = $xml->addChild('RESPOSTA');
			foreach ($response as $key => $value) {
				if (is_string($value)) {
					$xml_response->addChild($key,(!empty($value) ? $value : null));
				} 
			}
		}
		
		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($xml->asXML());
		
		$xml_gravar = $dom->saveXML();

		$log_envio = Array(
			'codigo_sm' => $codigo_sm,
			'loadplan' => '',
			'sucesso' => $enviado,
		);

		return $this->LogIntegracaoOutbox->incluirLog($xml_gravar,'Eventos SM',$log_envio,$codigo_cliente);
	}

	function exclui($evento) {
		$this->WsOutbox->create();
		$this->WsOutbox->delete($evento['WsOutbox']['codigo']);
		return true;
	}


	function soapComponent() {
			if ($this->soapComponent == null)
				$this->soapComponent = new SoapComponent();
			return $this->soapComponent;
	}
	
	private function im_running() {
		if (PHP_OS!='WINNT') {
			$cmd = `ps aux | grep 'requisicoes_ws'`;
			$ret = substr_count($cmd, 'cake.php -working') > 1;
		} else {
			$cmd = `tasklist /v | findstr /R /C:"requisicoes_ws"`;
			$ret = substr_count($cmd, 'cake\console\cake') > 1;
		}

		// 1 execução é a execução atual
		return $ret ;
	}
    
}
?>