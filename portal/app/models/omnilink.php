<?php
class Omnilink extends AppModel {
    var $name = 'Omnilink';
    var $useTable = false;

    function perfis_de_configuracao() {
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$address = "172.16.12.81";
		$service_port = 12000;
		$result = "";
		if ($socket === false) {
			$log[] = "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
		} else {
			//echo "OK.\n";
			//echo "Attempting to connect to '$address' on port '$service_port'...";
			$conexao = socket_connect($socket, $address, $service_port);
			if ($conexao === false) {
				$log[] = "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
			} else {
				$comando = '<?xml version="1.0" ?><Telecomando><CodMsg>-4</CodMsg></Telecomando>';
				for ($tentativas=50; $tentativas > 0; $tentativas--) { 
					socket_write($socket, $comando, strlen($comando));
					$buffer = socket_read($socket, 20480);
					if (strpos($buffer, '<?xml') > -1 && strpos($buffer, '</TeleEvento>') > -1 && strpos($buffer, '-43') > -1) {
						$buffer = "<TeleEvento>".substr($buffer, strpos($buffer, "<CodMsg> -43"));
						$buffer = substr($buffer,0,strpos($buffer, "</TeleEvento>")+13);
						if (strpos($buffer, '<TeleEvento>') > -1 && strpos($buffer, '</TeleEvento>') > -1 && strpos($buffer, '-43') > -1) {
							$result = '<?xml version="1.0" ?>'.$buffer;
							break;	
						}
					} 
				}
				socket_close($socket);
			}
		}
		if ($result) {
			$xml = simplexml_load_string($result);
			return get_object_vars($xml);
		}
		return false;
	}

	function find($findType) {
		if ($findType == 'list') {
			$result = Cache::read('perfis_de_configuracao');
			if (!$result) {
				$perfis = $this->perfis_de_configuracao();
				$result = array();
				if ($perfis) {
					foreach ($perfis['PerfilConfiguracao'] as $perfil) {
						$result[trim($perfil->IdPerfilConfiguracao)] = trim($perfil->NomePerfilConfiguracao);
					}
				}
				if ($result) {
					asort($result);
					Cache::write('perfis_de_configuracao', $result);
				}
			}
			return $result;
		}
	}
}
?>