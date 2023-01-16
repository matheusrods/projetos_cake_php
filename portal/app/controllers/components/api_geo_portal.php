<?php
class ApiGeoPortalComponent {
	var $name = 'ApiGeoPortal';

	private function _gravaLog($nome_chamada, $url_chamada, $retorno) {
		$this->logApigoogle = ClassRegistry::init('LogApigoogle');
	
		$this->logApigoogle->incluir(array(
			'nome_chamada' => $nome_chamada,
			'url_chamada' => $url_chamada,
			'retorno' => trim($retorno) ? json_encode($retorno) : ""
		));
	}
	
	/**
	 * [retornaDistanciaEntrePontos description]
	 * 
	 * retorna a distancia entre os pontos 
	 * 
	 * @param  [type] $origem   [description]
	 * @param  [type] $destinos [description]
	 * @return [type]           [description]
	 */
	public function retornaDistanciaEntrePontos($origem, $destinos) {
		
		$limite_enderecos = 20;
		$enderecos_array = explode("|", $destinos);

		// debug($enderecos_array);
		
		if(!empty($enderecos_array)) {

			//seta os parametros de entrada
			$parametros['ticket'] = Ambiente::getGeoPortalKey(0);
			$parametros['nome'] = "distancia";
			$parametros['tipo'] = "distancia";
			$parametros['resumido'] = "true";
			$parametros['exibeCaminhoDetalhado'] = "false"; /* Tras informações detalhadas, tais como Caminho, (vire a esqueda na rua X, vire a direita na rua Y  */
	        $parametros['executaRotaIncompleta'] = "true"; /* Caso não for possivel efetuar a rota, é ligado uma reta de um ponto ao outro */
	        $parametros['evitaAreaDeRisco'] = "false"; /* Evita Áreas de Risco */
	        $parametros['evitaCondominio'] = "true"; /* Evita Condomonio */
	        $parametros['evitaPedagio'] = "false"; /* Evita Pedagio */
	        $parametros['evitaponto'] = ""; /* Coleção de Pontos para a Rota Evitar  */
	        $parametros['evitapoligono'] = ""; /* Coleção de Poligonos para a Rota Evitar */
	        $parametros['ordenada'] = "false";

	        $retorno_endereco = array();

			//varre todos os endereços
	        foreach ($enderecos_array as $key => $dest) {

	        	if(empty($dest)) {
	        		continue;
	        	}

	        	//verifica se existe na tabela origem_destino as coordenadas
	        	$dados_origem_destino = $this->getOrigemDestino($origem,$dest);
	        	//verifica se existe dados retornados
	        	if(!empty($dados_origem_destino)) {

	        		//monta o array com os dados da distancia
			        $retorno_endereco['rows'][0]['elements'][$key]['distance']['text'] = $dados_origem_destino['OrigemDestino']['distancia_km'];
			        $retorno_endereco['rows'][0]['elements'][$key]['distance']['value'] = $dados_origem_destino['OrigemDestino']['distancia_metros'];
			        $retorno_endereco['status'] = "OK";

			        continue;

	        	}//fim if dados origem_destino

	        	//parametros coordenadas
				$parametros['coordenadas'] = $origem.";".$dest;
		        $parametros_json = json_encode($parametros);

		        // debug(APP);
		        
		        // $dados = "antes curl:".date('H:i:s')."\n";
		        // file_put_contents(APP."tmp/logs/teste_coord.txt", $dados,FILE_APPEND);

		        //manda a resquisicao para saber a distancia
		        $data = $this->execCurlPost($parametros_json);

		        // $dados = "depois curl:".date('H:i:s')."\n";
		        // file_put_contents(APP."tmp/logs/teste_coord.txt", $dados,FILE_APPEND);

		        //verifica se existe valor
		        $distancia_km = "-";
		        $distancia_metros = 0;
		        $status = '';
		        if(is_object($data)) {
		        	//se nao existir erro então seta os valores
		        	if(empty($data->erro)) {
				        //pega o valor da distancia em km
				        $distancia_km = round(($data->itens[0]->distancia / 1000),1);
				        $distancia_metros = $data->itens[0]->distancia;
				        $status = 'OK';
		        	}

		        }//fim object

		        //monta o array com os dados da distancia
		        $retorno_endereco['rows'][0]['elements'][$key]['distance']['text'] = $distancia_km." km";
		        $retorno_endereco['rows'][0]['elements'][$key]['distance']['value'] = $distancia_metros;

		        $km = $distancia_km.' km';
		        $this->gravaOrigemDestino($origem,$dest,$distancia_metros,$km);

		        // $dados = "fim loop:".date('H:i:s')."\n";
		        // file_put_contents(APP."tmp/logs/teste_coord.txt", $dados,FILE_APPEND);

	        }//fim foreach
		}// fim enderecos_array

		if(!isset($retorno_endereco['status'])) {
			$retorno_endereco['status'] = $status;
		}

		// debug($retorno_endereco);
		// exit;

		return $retorno_endereco;

	}// fim retornaDistanciaEntrePontos

	/**
	 * [getOrigemDestino description]
	 * 
	 * metodo para verificar se existe origem destino
	 * 
	 * @param  [type] $origem [description]
	 * @param  [type] $dest   [description]
	 * @return [type]         [description]
	 */
	public function getOrigemDestino($origem,$destino)
	{
		//instancia origem destino
		$this->OrigemDestino = ClassRegistry::init('OrigemDestino');
		
		//monta a condicao
		$ori = explode(";", $origem);
		$dest = explode(";", $destino);

		$conditions = array(
			'longitude_origem' => $ori[0],
			'latitude_origem' => $ori[1],
			'longitude_destino' => $dest[0],
			'latitude_destino' => $dest[1]
		);

		$resultado = $this->OrigemDestino->find('first',array('conditions' => $conditions));
		// file_put_contents(APP."/tmp/log.log",$this->OrigemDestino->find('sql',array('conditions' => $conditions))."\n\n",FILE_APPEND);

		return $resultado;

	}//fim getOrigemDestino($origem,$dest)

	/**
	 * [gravaOrigemDestino description]
	 * 
	 * metodo para gravar os dados na tabela de origem_destino
	 * @param  [type] $origem           [description]
	 * @param  [type] $dest             [description]
	 * @param  [type] $retorno_endereco [description]
	 * @return [type]                   [description]
	 */
	public function gravaOrigemDestino($origem,$destino,$distancia_metros,$distancia_km)
	{
		//instancia origem destino
		$this->OrigemDestino = ClassRegistry::init('OrigemDestino');
		
		//monta a condicao
		$ori = explode(";", $origem);
		$dest = explode(";", $destino);

		$dados = array('OrigemDestino' => array(
				'longitude_origem' => $ori[0],
				'latitude_origem' => $ori[1],
				'longitude_destino' => $dest[0],
				'latitude_destino' => $dest[1],
				'distancia_metros' => $distancia_metros,
				'distancia_km' => $distancia_km
			)
		);

		if (!isset($_SESSION['Auth']['Usuario']['codigo'])) {
			$dados['OrigemDestino']['codigo_usuario_inclusao'] = 1;
		}

		if(!$this->OrigemDestino->incluir($dados)) {
			// debug('erro ao incluir origem destino');
			return false;
		}//fim origem destino incluir

		return true;

	}//fim gravaOrigemDestino

	
	function carregarPorLatLgn($coordenadas){
		return $this->carregaUrl("https://maps.googleapis.com/maps/api/geocode/json?latlng={$coordenadas['lat']},{$coordenadas['lgn']}&sensor=false&key=" . Ambiente::getGoogleKey(0), 'retorna_lat_lgn');
	}
	
	public function carregarEndereco($endereco,$qtd=1) {
		//troca o - para virgula pois o geoportal trabalha com virgula
		$endereco = $this->tirarAcentos($endereco);
		$endereco = str_replace("-", ',', utf8_encode($endereco));

		// print $endereco;

		//faz a consulta 
		$dados = $this->carregaUrl("https://www.geoportal.com.br/xgeocoder/xGeocodeAddress.aspx?Ticket=".Ambiente::getGeoPortalKey(0)."&formatado=true&endereco=".urlencode($endereco)."&latim=true&qtde=".$qtd, 'carrega_endereco');

		// debug($dados);

		//retorna o objeto de resposta
		return $dados;

	}//fim metodo carregaEndereco
	
	function retornaLatitudeLongitudeDoEndereco($logradouro, &$exceeded = false){
		
		// debug($logradouro);

		$resultado = $this->carregarEndereco($logradouro);

		// debug($resultado);
		
		if(!isset($resultado->GEOCODE)){
			// $this->log('A API do geocode retornou um erro.','debug');
			return false;
		}

		$res = $resultado->GEOCODE->RUA->{'@attributes'};
		if(isset($res->Id)){
			if($res->X && $res->Y){
				$latitude = $res->Y;
				$longitude = $res->X;
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

	/**
	 * [carregaUrl description]
	 * 
	 * metodo para carregar a url do mapa
	 * 
	 * 
	 * @param  [type] $url          [description]
	 * @param  [type] $nome_chamada [description]
	 * @return [type]               [description]
	 */
	private function carregaUrl($url, $nome_chamada) {
		
		$this->logApigoogle = ClassRegistry::init('LogApigoogle');
		$resultado = $this->logApigoogle->verificaLog($url);

		// debug($url);
		
		if(!$resultado || empty($resultado)) {

			$aContext = array(
				'https' => array(
					'proxy' => 'tcp://siena.local.buonny:3128',
					'request_fulluri' => true,
				),
			);
			$cxContext = stream_context_create($aContext);
			$resultado = file_get_contents($url, False, $cxContext);

			// print "aff: ".$resultado;
			// debug($url)
			// debug($resultado);

			if(!empty($resultado)) {

				if($resultado['status'] == 'OK'){
					$this->_gravaLog($nome_chamada, $url, $resultado);
				}
			}				
		}
       
        if (!$resultado) {
            return false;
        } else {
        	
        	$resultado = $this->tirarAcentos($resultado);
        	// debug($resultado);
        	$xml = new SimpleXMLElement($resultado);

        	//codifica para json
        	$json = json_encode($xml);
        	//decodifica
			$xml_fixed = json_decode($json);

        	return $xml_fixed;

            // return $resultado = json_decode($resultado);
        }
    }//fim carregarUrl

    /**
     * [execCurl description]
     * 
     * metodo para executar uma consulta via server side
     * 
     * @param  [type] $parametros [description]
     * @param  string $method     [description]
     * @return [type]             [description]
     */
    public function execCurlPost($parametros) 
    {

    	//inicia o curl
		$ch = curl_init();
		//seta 
		curl_setopt_array($ch, array(
		    CURLOPT_URL => 'https://www.geoportal.com.br/WS/service.asmx/rota',
		    CURLOPT_POST => true,
		    CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
		    CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
		    CURLOPT_POSTFIELDS => $parametros,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_PROTOCOLS => CURLPROTO_HTTPS
		));

		//seta o resultado
		$data = curl_exec($ch);
		$err = curl_error($ch);

		curl_close($ch);

		//verifica se houve algum erro 
		if ($err) {
			// debug("cURL Error #:" . $err);
			// exit;
			return false;
		} 
		
		// debug($data);
		// exit;

		//seta decodifica o json retornado
		$res = json_decode($data);
		//transforma o json retornado em objeto
		$response = json_decode($res->d);

		if(empty($response->erro)){
			// $this->_gravaLog($nome_chamada, $url, $resultado);
		}

		//retorna a resposta da chamada
		return $response;

    }//fom execCurl

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

    /**
     * [tirarAcentos description]
     * 
     * retirar acentos
     * 
     * @param  [type] $string [description]
     * @return [type]         [description]
     */
    function tirarAcentos($string)
    {
		return preg_replace(array("/(á|à|ã|â|ä)/", "/(ç)/", "/(Ç)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(º)/"),explode(" ","a c C A e E i I o O u U n N"),$string);
	}//FINAL FUNCTION tirarAcentos
}