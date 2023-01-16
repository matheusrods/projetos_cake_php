<?php
class ApiGoogleComponent {
	var $name = 'ApiGoogle';

	private function _gravaLog($nome_chamada, $url_chamada, $retorno) {
		$this->logApigoogle = ClassRegistry::init('LogApigoogle');
	
		$this->logApigoogle->incluir(array(
			'nome_chamada' => $nome_chamada,
			'url_chamada' => $url_chamada,
			'retorno' => trim($retorno) ? json_encode($retorno) : ""
		));
	}
	
	function retornaDistanciaEntrePontos($origem, $destinos) {
		
		$limite_enderecos = 20;
		$enderecos_array = explode("|", $destinos);
		
		if(count($enderecos_array) > $limite_enderecos) {
			$array_fatiado = array_chunk($enderecos_array, $limite_enderecos);

			foreach($array_fatiado as $key => $lote) {
				
				$text_endereco = "";
				foreach($lote as $k => $info_endereco) {
					$text_endereco .= $info_endereco . "|";	
				}
				
				$resultado[$key] = $this->carregaUrl('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . urlencode(Comum::trata_nome($origem)) . '&destinations=' . urlencode(Comum::trata_nome($text_endereco)) . '&mode=driving&language=pt-BR&sensor=false&key=' . Ambiente::getGoogleKey(0), 'distancia_entre_pontos');
			}

			foreach($resultado as $key => $lote) {
				
				if($lote) {
					foreach($lote->destination_addresses as $key => $item_destination_addresses) {
						$retorno['destination_addresses'][] = $item_destination_addresses;
					}
					
					if(isset($lote->rows[0]->elements)) {
						foreach($lote->rows[0]->elements as $key => $item_row) {
							$retorno['rows'][0]['elements'][] = $item_row;
						}						
					} else {
						$retorno['rows'][0]['elements'][] = array();
					}
				
				}
			}			
			
			return $retorno;
			
		} else {
			return $this->carregaUrl('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . urlencode(Comum::trata_nome($origem)) . '&destinations=' . urlencode(Comum::trata_nome($destinos)) . '&mode=driving&language=pt-BR&sensor=false&key=' . Ambiente::getGoogleKey(0), 'distancia_entre_pontos');
		}
	}
	
	function carregarPorLatLgn($coordenadas){
		return $this->carregaUrl("https://maps.googleapis.com/maps/api/geocode/json?latlng={$coordenadas['lat']},{$coordenadas['lgn']}&sensor=false&key=" . Ambiente::getGoogleKey(0), 'retorna_lat_lgn');
	}
	
	function carregarEndereco($endereco) {		
		return $this->carregaUrl("https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($endereco) . "&sensor=false&key=" . Ambiente::getGoogleKey(0), 'carrega_endereco');		
	}	

	function carregarEnderecoAutoComplete($endereco) {

		// https://maps.googleapis.com/maps/api/place/autocomplete/json?input=Rua+Zequinha&key=AIzaSyBEea8ePfWIxg0t3prI96OVgaGfR0YtUWw
		return $this->carregaUrl("https://maps.googleapis.com/maps/api/place/autocomplete/json?input=" . urlencode($endereco) . "&sensor=false&key=" . Ambiente::getGoogleKey(0), 'carrega_endereco');		
	}	
	
	function retornaLatitudeLongitudeDoEndereco($logradouro, &$exceeded = false){
		
		$resultado = $this->carregarEndereco($logradouro);
		
		if(isset($resultado->error_message) && $resultado->error_message == "You have exceeded your daily request quota for this API."){
			$this->log('A API do google retornou um erro pois foi utilizada muito em um curto período de tempo.','debug');
			$exceeded = true; return false;
		}

		if(isset($resultado->status) && $resultado->status == "OK"){
			if($resultado->results[0]->geometry->location->lng && $resultado->results[0]->geometry->location->lat){
				$latitude = $resultado->results[0]->geometry->location->lat;
				$longitude = $resultado->results[0]->geometry->location->lng;
				return $retorno = array($latitude,$longitude);
			}
		}else{
			return false;
		}
	}
	
	function retornaEnderecoPorLatLgn($coordenadas){
		$resultado = $this->carregarPorLatLgn($coordenadas);
		if(isset($resultado->error_message) && $resultado->error_message == "You have exceeded your daily request quota for this API."){
			return false;
		}

		if($resultado->status == "OK"){
			return $resultado;
		}else{
			return false;
		}
	}

	private function carregaUrl($url, $nome_chamada) {
		
		$this->logApigoogle = ClassRegistry::init('LogApigoogle');
		$resultado = $this->logApigoogle->verificaLog($url);
		
		if(!$resultado || empty($resultado)) {
			
// 			if (!function_exists('curl_init')) {
				
// 				$cURL = curl_init($url);
// 				if (Ambiente::getServidor() == Ambiente::SERVIDOR_DESENVOLVIMENTO) {
// 					curl_setopt($cURL, CURLOPT_PROXY, 'http://172.16.23.102');
// 					curl_setopt($cURL, CURLOPT_PROXYPORT, 8080);
// 					curl_setopt($cURL, CURLOPT_PROXYUSERPWD,"sistemas:SisBuonny15");
// 				}
// 				curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
// 				curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, true);
// 				curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, FALSE);
				
// 				$resultado = curl_exec($cURL);
				
// 				curl_close($cURL);
// 			} else {
				$aContext = array(
					'https' => array(
						'proxy' => 'tcp://siena.local.buonny:3128',
						'request_fulluri' => true,
					),
				);
				$cxContext = stream_context_create($aContext);
				$resultado = @file_get_contents($url, False, $cxContext);
// 			}
			
			if(!empty($resultado)) {
				$resultado_json = json_decode($resultado);
				
				// debug($resultado_json->status);
				// exit;
				if($resultado_json->status == 'OK'){
					$this->_gravaLog($nome_chamada, $url, $resultado);
				}
			}				
		}
       
        if (!$resultado) {
            return false;
        } else {
            return $resultado = json_decode($resultado);
        }
    }

    function verificaLatitudeLongitude($local){
    	$lat_long = false;

    	if(isset($local['endereco']) && !empty($local['endereco'])){
	    	$lat_long = $this->retornaLatitudeLongitudeDoEndereco($local['endereco']);
	    	if($lat_long){
	    		return $lat_long;
	    	}
    	}

    	if(isset($local['bairro']) && !empty($local['bairro'])){
	    	$lat_long = $this->retornaLatitudeLongitudeDoEndereco($local['bairro']);
	    	if($lat_long){
	    		return $lat_long;
	    	}
    	}

    	if(isset($local['cep']) && !empty($local['cep']))
		$lat_long = $this->retornaLatitudeLongitudeDoEndereco($local['cep']);
		if($lat_long){
			return $lat_long;
		}
  		

  		if(isset($local['cidade']['nome']) && !empty($local['cidade']['nome']) && isset($local['cidade']['estado']) && !empty($local['cidade']['estado'])){
			$endereco = $local['cidade']['nome'].', '.$local['cidade']['estado'];
			
			$lat_long = $this->retornaLatitudeLongitudeDoEndereco($endereco);
			if($lat_long){
				return $lat_long;
			}
    	}
  		
  		if(isset($local['cidade']['nome']) && !empty($local['cidade']['nome'])){
			$lat_long = $this->retornaLatitudeLongitudeDoEndereco($local['cidade']['nome']);
	    	if($lat_long){
	    		return $lat_long;
	    	}
    	}

    	if(isset($local['cidade']['estado']) && !empty($local['cidade']['estado'])){
	    	$lat_long = $this->retornaLatitudeLongitudeDoEndereco($local['cidade']['estado']);
	    	if($lat_long){
	    		return $lat_long;
	    	}
    	}

    	return false;
    }
}