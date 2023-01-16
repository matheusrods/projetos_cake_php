<?php
class MensageriaEsocial extends AppModel {
	var $name = 'MensageriaEsocial';
	public $useTable = false;
	var $actsAs = array('Secure');

	// public $tecnospeed_cnpj = '20183726000114';
	// public $tecnospeed_token = '505112519281e7cea0d5f4ca66b3b0aa';

	//token do ithealth
	public $tecnospeed_cnpj = '20966646000135';
	public $tecnospeed_token = 'ea6590bc357905668e7cfe9b7459b39f';

	public $tecnospeed_url_certificado = 'https://api.tecnospeed.com.br/reinf/v1/certificados';
	public $tecnospeed_url_esocial_txt2 = 'https://api.tecnospeed.com.br/esocial/v1/evento/enviar/tx2';
	public $tecnospeed_url_esocial = 'https://api.tecnospeed.com.br/esocial/v1/evento/';

	public $cod_cliente, $cod_usuario;


	/**
	 * [get_servico_assinatura verifica se tem assinatura configurada para mensageria]
	 * @return [type] [description]
	 */
	public function get_servico_assinatura($codigo_cliente)
	{
		$this->Configuracao =& ClassRegistry::init('Configuracao');
		$this->ClienteProdutoServico2 =& ClassRegistry::init('ClienteProdutoServico2');

		//retorno do metodo
		$return = false;

		//verifica se tem o codigo do cliente
		if(empty($codigo_cliente)) {
			return $return;
		}

		//chave para mensageria do esocial
		$chave = "ESOCIAL_MENSAGERIA";

		//pega o codigo do servico configurado
		$codigo_servico = $this->Configuracao->getChave($chave);

		//verfica se tem codigo configurado
		if(!empty($codigo_servico)) {

			$codigo_servico = explode(",",$codigo_servico);			

			//verifica se na assintura do cliente tem esse servico configurado
			$servico_cliente = $this->ClienteProdutoServico2->verificaServicoCliente($codigo_cliente, $codigo_servico);

			//verifica se tem o servico na assinatura do cliente
			if($servico_cliente) {
				$return = true;
			}

		}//fim codigo_servico

		return $return;

	}//fim get_servico_assinatura


	/**
     * [log_api description]
     * 
     * METODO PARA GERAR O LOG INTEGRACOES
     * 
     * @param  [type] $status  [description]
     * @param  [type] $entrada [description]
     * @param  [type] $saida   [description]
     * @return [type]          [description]
     */
    public function log_api($entrada,$saida,$status="0",$msg="SUCESSO", $arquivo="API_TECNOSPEED", $model=null, $foreign_key=null)
    {
        //instancia a model
        $this->LogIntegracao = ClassRegistry::init('LogIntegracao');

        //seta os valores
        $log_integracao['LogIntegracao']['codigo_cliente']          = $this->cod_cliente;
        $log_integracao['LogIntegracao']['codigo_usuario_inclusao'] = $this->cod_usuario;
        $log_integracao['LogIntegracao']['descricao']               = $msg;
        $log_integracao['LogIntegracao']['arquivo']                 = $arquivo;
        $log_integracao['LogIntegracao']['conteudo']                = $entrada;
        $log_integracao['LogIntegracao']['retorno']                 = $saida;
        $log_integracao['LogIntegracao']['sistema_origem']          = $arquivo;
        $log_integracao['LogIntegracao']['data_arquivo']            = date('Y-m-d H:i:s');
        $log_integracao['LogIntegracao']['status']                  = $status; 
        $log_integracao['LogIntegracao']['tipo_operacao']           = 'I'; //inserido
        $log_integracao['LogIntegracao']['model']                   = $model;
        $log_integracao['LogIntegracao']['foreign_key']             = $foreign_key;

        //inclui na tabela
        $this->LogIntegracao->incluir($log_integracao);

    } //fim log_api


	/**
	 * [tecnospeed_envia_certificado metodo para cadastrar o certificado digital na tecnospeed e relacionar os cnpjs que podem transacionar por ele
	 *  segue doc: https://atendimento.tecnospeed.com.br/hc/pt-br/articles/1500008358782-Cadastrar-ceritificado-digital
	 * ]
	 * @param  [array] $params [array com os dados que vamos enviar para a tecnospeed]
	 * @return [type]         [description]
	 */
	public function tecnospeed_envia_certificado($codigo_cliente, $codigo_usuario, $codigo_certificado, $params)
	{

		//variavel de retorno		
		$retorno = true;

		//validação se tem os parametros
		if(empty($params)) {
			$this->log("MensageriaEsocial: Paramentros necessários para envio do certificado digital",'debug');
			$retorno = false;
			return $retorno;
		}

		// NÃO FOI POSSIVEL FAZER A COMUNICAÇÃO 
		// monta o json para comunicar com o endpoint que vai fazer o envio para a tecnospeed do certificado
		$dados_json = json_encode(array(
			'codigo_cliente' => $codigo_cliente,
			'codigo_usuario' => $codigo_usuario,
			'codigo_certificado' => $codigo_certificado,
			'params' => $params
		));

		//producao
		$host = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? "https://api.rhhealth.com.br/api" : (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO ? "https://tstapi.rhhealth.com.br/api" : "https://tstapi.rhhealth.com.br/api"));

		// debug($host);exit;

		$url = $host."/esocial/enviar_certificado";

		$token = "de541d7b846e9580ed43597b74ade660a290bd658a6ffa4b2c0727945a87ded6";
		$auth = array("auth-token: " . $token, 'Content-Type: application/json');

		// debug($url);
		// debug($auth);
		// debug($dados_json);
		// exit;

		//curl para enviar o certificado digital
		$curl = curl_init();

		//chamada no endpoint
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => $dados_json,
		  CURLOPT_HTTPHEADER => $auth,
		));

		$response = curl_exec($curl);
		curl_close($curl);

		// debug($response);exit;

		//transforma o response "retorno da api da tecnospeed" para validacao
		$api_retorno = json_decode($response);
		
		if(isset($api_retorno->result->error)) {
		    $retorno = false;
		}
		else {

		    $retorno = $api_retorno->result->data;
			if(is_null($retorno)) {
				$retorno = false;
			}

		}
		
		return $retorno;

	}//fim tecnospeed

	/**
	 * [tecnospeed_get_certificados metodo para pegar os certificados importados
	 * 
	 * segue documentacao: https://atendimento.tecnospeed.com.br/hc/pt-br/articles/1500008393101-Listar-certificados-digitais
	 * 
	 * ]
	 * @return [type] [description]
	 */
	public function tecnospeed_get_certificados($codigo_usuario)
	{

		//variavel para autenticar
		$auth = array(
		    'cnpj_sh: ' . $this->tecnospeed_cnpj,
		    'token_sh: ' . $this->tecnospeed_token
		  );


		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->tecnospeed_url_certificado,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_HTTPHEADER => $auth,
		));

		$response = curl_exec($curl);
		curl_close($curl);		

		$this->cod_cliente = null;
		$this->cod_usuario = $codigo_usuario;

		//transforma o response "retorno da api da tecnospeed" para validacao
		$api_retorno = json_decode($response);
		$status = 0;
		$msg_retorno = "SUCESSO";

		// debug($api_retorno);
		// exit;

		//seta o log da api
		$this->log_api(
			$codigo_usuario, $response, $status, $msg_retorno, 'API_TECNOSPEED_CERTIFICADO'
		);

		return $api_retorno;

	}// fim tecnospeed_get_certificados

	public function xml_attribute($object, $attribute)
	{
	    if(isset($object[$attribute]))
	        return (string) $object[$attribute];
	}


	/**
	 * [tecnospeed_evento_enviar_xml metodo para integrar o xml com o esocial]
	 * @param  [type] $codigo_int_esocial_evento [description]
	 * @return [type]                            [description]
	 */
	public function tecnospeed_evento_enviar_xml($codigo_int_esocial_evento)
	{

		$this->IntEsocialEventos = ClassRegistry::init('IntEsocialEventos');
		$dados = $this->IntEsocialEventos->find('first', array('conditions' => array('codigo' => $codigo_int_esocial_evento)));
		$retorno = false;

		//verificar se tem valor dados
		if(!empty($dados)) {
			
			//verificar se tem tipo de evento para direcionar a geracao do txt2 corretamente
			if(!empty($dados['IntEsocialEventos']['codigo_int_esocial_tipo_evento'])) {

				//muda o status para processado
				$dados['IntEsocialEventos']['codigo_int_esocial_status'] = 2;//processado
				$this->IntEsocialEventos->atualizar($dados);

				//pega o xml gravado
				$dado_xml = $dados['IntEsocialEventos']['dados_evento'];
				//lendo xml
				$read_xml = simplexml_load_string($dado_xml);

				//switch para direcionamento da leitura e montagem do txt2 a partir do xml corretamente
				switch ($dados['IntEsocialEventos']['codigo_int_esocial_tipo_evento']) {
					
					case '1': //s2210
						$xmlns = 'http://www.esocial.gov.br/schema/evt/evtCAT/v_S_01_00_00';
						//pegando o atributo ID
						$attributeID = $this->xml_attribute($read_xml->evtCAT, 'Id');
						break;
					case '2': //s2220
						$xmlns = 'http://www.esocial.gov.br/schema/evt/evtMonit/v_S_01_00_00';
						//pegando o atributo ID
						$attributeID = $this->xml_attribute($read_xml->evtMonit, 'Id');
						break;
					case '3': //s2230
						$xmlns = 'http://www.esocial.gov.br/schema/evt/evtAfastTemp/v_S_01_00_00';
						//pegando o atributo ID
						$attributeID = $this->xml_attribute($read_xml->evtAfastTemp, 'Id');
						break;
					case '4': //s2240
						$xmlns = 'http://www.esocial.gov.br/schema/evt/evtExpRisco/v_S_01_00_00';
						//pegando o atributo ID
						$attributeID = $this->xml_attribute($read_xml->evtExpRisco, 'Id');
						break;
					case '5': //s3000
						$xmlns = 'http://www.esocial.gov.br/schema/evt/evtExclusao/v_S_01_00_00';
						//pegando o atributo ID
						$attributeID = $this->xml_attribute($read_xml->evtExclusao, 'Id');
						break;
				}//fim geracao do txt2 por evento

				//add o xmlns na tag esocial
				$obj_xml = new SimpleXMLElement($dado_xml);
				$obj_xml->addAttribute('xmlns', $xmlns);
				
				//formata o xml que vai ser enviado para a tecnospeed
				$dado_xml_envio = '<eventos><evento Id="'.$attributeID.'">'.$obj_xml->asXML()."</evento></eventos>";
				$dado_xml_envio = str_replace("\n", "", $dado_xml_envio);
				$dado_xml_envio = str_replace('<?xml version="1.0"?>', '', $dado_xml_envio);
				
				// debug($dado_xml_envio);exit;

				$versaoManual = 'S.01.00.00'; //versao simplificada
				$idgrupoeventos = 2; // id grupo do evento (nao peridico 2 sao os que trafegamos segundo o layout do esocial)

				//pegar o cnpj do empregador (cliente) onde vai ser enviado 
				$this->Cliente = ClassRegistry::init('Cliente');
				$cliente = $this->Cliente->find('first',array('fields' => array('codigo_documento','codigo_documento_real'),'conditions' => array('codigo' => $dados['IntEsocialEventos']['codigo_cliente'])));
				//pega o cnpj do cliente do evento
				$codigo_documento_real = trim($cliente['Cliente']['codigo_documento_real']);

				$cnpj_cliente = ($codigo_documento_real <> '') ? $codigo_documento_real : $cliente['Cliente']['codigo_documento'];
				
				//pegar o ambiente que está configurado na tabela de certificado que vai ser enviado "Id do ambiente (1 producao / 2 pre producao)" 
				$this->IntEsocialCertificado = ClassRegistry::init('IntEsocialCertificado');
				$dados_certificado = $this->IntEsocialCertificado->find('first',array('conditions' => array('codigo' => $dados['IntEsocialEventos']['codigo_int_esocial_certificado'])));
				$ambiente = $dados_certificado['IntEsocialCertificado']['ambiente_esocial'];

				//monta o array com os parametros
				$params_envio = array(
				  		'versaomanual' => $versaoManual,
				  		'empregador' => $cnpj_cliente,
				  		'ambiente' => $ambiente,
				  		'inscricao' => $cnpj_cliente,
				  		'idgrupoeventos' => $idgrupoeventos,
				  		'xml' => $dado_xml_envio
				  	);

				//necessario para o multipart pelo verbo post				
				$boundary = "---". md5(time());

				//monta o dado que vai enviar no formato multipart
				$post_envio = "--{$boundary}\r\nContent-Disposition: form-data; name=\"versaomanual\"\r\n\r\n{$versaoManual}\r\n--{$boundary}\r\nContent-Disposition: form-data; name=\"ambiente\"\r\n\r\n{$ambiente}\r\n--{$boundary}\r\nContent-Disposition: form-data; name=\"idgrupoeventos\"\r\n\r\n{$idgrupoeventos}\r\n--{$boundary}\r\nContent-Disposition: form-data; name=\"empregador\"\r\n\r\n{$cnpj_cliente}\r\n--{$boundary}\r\nContent-Disposition: form-data; name=\"inscricao\"\r\n\r\n{$cnpj_cliente}\r\n--{$boundary}\r\nContent-Disposition: form-data; name=\"xml\"\r\n\r\n".$params_envio['xml']."\r\n--{$boundary}--\r\n";
				// debug($post_envio);exit;

				//monta o cUrl para enviar os dados para a tecnospeed
				$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => $this->tecnospeed_url_esocial."enviar",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  // CURLOPT_VERBOSE => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'POST',
				  CURLOPT_POSTFIELDS => $post_envio,
				  CURLOPT_HTTPHEADER => array(
				    "Content-Type: multipart/form-data; boundary={$boundary}",
				    'cnpj_sh: ' . $this->tecnospeed_cnpj,
				    'token_sh: ' . $this->tecnospeed_token
				  ),
				));

				$response = curl_exec($curl);

				curl_close($curl);
				// echo $response."\n";exit;

				//transforma o response "retorno da api da tecnospeed" para validacao
		        $api_retorno = json_decode($response);
		        $status = 0;

		        // debug($response);
		        // debug($api_retorno);
		        // exit;

		        $msg_retorno = "SUCESSO";
				
		        if(isset($api_retorno->error)) {
		            $status = 1;
		            $msg_retorno = "ERROR";

		            $dados['IntEsocialEventos']['data_integracao'] = date('Y-m-d H:i:s');
		            $dados['IntEsocialEventos']['id_evento'] = $attributeID;
		            $dados['IntEsocialEventos']['ambiente_esocial'] = $ambiente;
					$dados['IntEsocialEventos']['codigo_int_esocial_status'] = 4;//erro
		        }
		        else {
		            
		            if(isset($api_retorno->data->id)) {
		            	$retorno = $api_retorno->message;

			            //pega o id da tecnospeed para gravar no nosso banco para eventuais consultas
			            $dados['IntEsocialEventos']['codigo_integracao'] = $api_retorno->data->id;
			            $dados['IntEsocialEventos']['id_evento'] = $attributeID;
			            $dados['IntEsocialEventos']['ambiente_esocial'] = $ambiente;
			            $dados['IntEsocialEventos']['data_integracao'] = date('Y-m-d H:i:s');
		            }
		            else {
		            	$status = 1;
			            $msg_retorno = "ERROR";
			            $dados['IntEsocialEventos']['data_integracao'] = date('Y-m-d H:i:s');
			            $dados['IntEsocialEventos']['id_evento'] = $attributeID;
			            $dados['IntEsocialEventos']['ambiente_esocial'] = $ambiente;
						$dados['IntEsocialEventos']['codigo_int_esocial_status'] = 4;//erro
		            }

		        }
		        
		        $this->IntEsocialEventos->atualizar($dados);

		        // $retorno = '123';
		        // $api_retorno = '123';

		        $this->cod_cliente = $dados['IntEsocialEventos']['codigo_cliente'];
		        $this->cod_usuario = (!empty($this->authUsuario['Usuario']['codigo_cliente'])) ? $this->authUsuario['Usuario']['codigo_cliente'] : $dados['IntEsocialEventos']['codigo_usuario_inclusao'];

		        //seta o log da api
		        $this->log_api(
		            json_encode($params_envio), json_encode($api_retorno), $status, $msg_retorno, 'API_TECNOSPEED_EVENTO','IntEsocialEventos',$codigo_int_esocial_evento
		        );

			}//fim verificacao tipo de evento

		}//fim verificacao dados

		return $retorno;


	}//fim tecnospeed_evento_enviar_xml

	/**
	 * Metodo para buscar os dados do evento
	 */
	public function tecnospeed_evento_consulta($dados)
	{

		$retorno = false;

		//verificar se tem valor dados
		if(!empty($dados)) {

			$codigo_integracao = $dados['IntEsocialEventos']['codigo_integracao'];

			if(!empty($codigo_integracao)) {

				$codigo_int_esocial_evento = $dados['IntEsocialEventos']['codigo'];

				//pega o cnpj do cliente do evento
				$codigo_documento_real = trim($dados['Cliente']['codigo_documento_real']);
				$cnpj_cliente = ($codigo_documento_real <> '') ? $codigo_documento_real : $dados['Cliente']['codigo_documento'];

				$ambiente = $dados['IntEsocialEventos']['ambiente_esocial'];

				//monta a url para buscar o status do evento
				$url = $this->tecnospeed_url_esocial."consultar/".$codigo_integracao."?versaomanual=S.01.00.00&ambiente=".$ambiente;

				$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => $url,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'GET',
				  CURLOPT_HTTPHEADER => array(
				    'cnpj_sh: ' . $this->tecnospeed_cnpj,
					'token_sh: ' . $this->tecnospeed_token,
				    'empregador: ' . $cnpj_cliente
				  ),
				));

				$response = curl_exec($curl);

				curl_close($curl);
				// echo $response;exit;

				//transforma o response "retorno da api da tecnospeed" para validacao
		        $api_retorno = json_decode($response);
		        $status = 0;

		        // debug($response);
		        // debug($api_retorno);
		        // exit;

		        $msg_retorno = "SUCESSO";
				
		        if(isset($api_retorno->error)) {
		            $status = 1;
		            $msg_retorno = "ERROR";

		            $dados['IntEsocialEventos']['codigo_int_esocial_status'] = 4;//erro
		        }
		        else {
		            
		            if(isset($api_retorno->data->id)) {
		            	$codigo_integradora = $api_retorno->data->status_envio->codigo;
		            	$mensagem_integradora = $api_retorno->data->status_envio->mensagem;

		            	$codigo_recibo = null;
		            	$var_aux_controlle_json_retorno = false;

		            	//varre o codigo do json retorno
		            	if(!empty($api_retorno->data->json_retorno)) {
		            		foreach($api_retorno->data->json_retorno AS $json) {

		            			$var_aux_controlle_json_retorno = true;

				            	$codigo_integradora = $json->status->codigo;
				            	$mensagem_integradora = $json->status->mensagem;

				            	$codigo_recibo = (isset($json->recibo)) ? $json->recibo : null;

				            	if(isset($json->ocorrencias) && !empty($json->ocorrencias)){
				            		
				            		foreach($json->ocorrencias as $ocorrencia){

				            			$dados_ocorrencia['OcorrenciaIntEsocialEvento'] = array(
				            				'codigo_int_esocial_evento' => $codigo_int_esocial_evento,
				            				'codigo_ocorrencia' => $ocorrencia->codigo,
				            				'tipo' => $ocorrencia->tipo,
				            				'descricao_ocorrencia' => $ocorrencia->descricao,
				            				'localizacao' => $ocorrencia->localizacao
				            			);

				            			$this->OcorrenciaIntEsocialEvento = ClassRegistry::init('OcorrenciaIntEsocialEvento');
				            			$this->OcorrenciaIntEsocialEvento->incluir($dados_ocorrencia);				  
				            		}
				            	}				       
		            		}
		            	}

			            //pega o id da tecnospeed para gravar no nosso banco para eventuais consultas
			            
			            $dados['IntEsocialEventos']['codigo_retorno_integradora'] = $codigo_integradora;
			            $dados['IntEsocialEventos']['mensagem_retorno_integradora'] = $mensagem_integradora;
			            $dados['IntEsocialEventos']['data_retorno_integradora'] = date('Y-m-d H:i:s');

			            if(!is_null($codigo_recibo)) {
			            	$var_aux_controlle_json_retorno = false;
			            	$dados['IntEsocialEventos']['codigo_recibo'] = $codigo_recibo;

			            	//verifica se é um evento de exclusão
			            	if($dados['IntEsocialEventos']['codigo_int_esocial_tipo_evento'] == 5) { //s-3000
			            		$dados['IntEsocialEventos']['codigo_int_esocial_status'] = 7;//exclusão de evento
			            	}
			            	else { //os outros eventos
			            		$dados['IntEsocialEventos']['codigo_int_esocial_status'] = 3;//concluido
			            	}
			            }

			            if($var_aux_controlle_json_retorno) {
			            	$dados['IntEsocialEventos']['codigo_int_esocial_status'] = 4;//erro
			            }

		            }
		        }

		        // debug($dados);exit;
		        //para atualizacao
		        $int_esocial_eventos['IntEsocialEventos'] = $dados['IntEsocialEventos'];
		        
		        $this->IntEsocialEventos = ClassRegistry::init('IntEsocialEventos');
		        $this->IntEsocialEventos->atualizar($int_esocial_eventos);

		        // $retorno = '123';
		        // $api_retorno = '123';

		        $this->cod_cliente = $dados['IntEsocialEventos']['codigo_cliente'];
		        $this->cod_usuario = (!empty($this->authUsuario['Usuario']['codigo_cliente'])) ? $this->authUsuario['Usuario']['codigo_cliente'] : $dados['IntEsocialEventos']['codigo_usuario_inclusao'];

		        //seta o log da api
		        $this->log_api(
		            json_encode($url), json_encode($api_retorno), $status, $msg_retorno, 'API_TECNOSPEED_GET_EVENTO','IntEsocialEventos',$codigo_int_esocial_evento
		        );

			}//fim verificacao se tem codigo de integracao
		}//fim dados int_esocial_eventos
	}//fim tecnospeed_evento_consulta


}//fim mensageria esocial