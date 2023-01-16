<?php
class MaplinkComponent {
	var $name = 'Maplink';
	private $options = array(
		'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
		'connection_timeout' => 1,
	);
	private $login = array('user' => 'buonnynova', 'pwd' => 'ccb513d05a0145728728');
	public $maplinkAuth = null;
	private $url = 'http://services.maplink.com.br/webservices/v3';
	private $token = null;
	public $maplinkRoute = null;
	public $maplinkAddress = null;

	var $ambiente = null;	
	
    const VELOCIDADE_VIA = 'Velocidade das Vias';
    const VELOCIDADE_MEDIA = 'Velocidade Média Calculada';

    const URL_ROUTE = '/Route/Route.asmx?WSDL';
    const URL_ADRESS = '/AddressFinder/AddressFinder.asmx?WSDL';
    const URL_AUTH = '/authentication/authentication.asmx?WSDL';

    const TIPO_CALCULO_PREVISTO = 'Valores Previstos de Viagem';
    const TIPO_CALCULO_REALIZADO = 'Valores da Viagem Realizada';

    const ORIGEM_CHAMADA_TTIME = 'Transit Time';
    const ORIGEM_CHAMADA_API = 'API acompanhamento_sms_ws';
    const ORIGEM_CHAMADA_ANALITICO = 'Relatório Analítico de Viagens';

    public function __construct(){
    	$this->ambiente = new Ambiente();
    }

    public static function listTipoCalculo() {
        return array(
            self::VELOCIDADE_VIA => 'Velocidade das Vias',
            self::VELOCIDADE_MEDIA => 'Velocidade Média Calculada',
        );
    }

	function initialize(&$controller, $settings = array()) {		
		// saving the controller reference for later use		
		$this->controller =& $controller;	
	}

	function setup() {
// 		if (Ambiente::getServidor() == Ambiente::SERVIDOR_DESENVOLVIMENTO) {
// 			$this->options = array_merge($this->options, array('proxy_host' => '172.16.23.102', 'proxy_port' => '8080', 'proxy_login' => 'sistemas', 'proxy_password' => 'SisBuonny15'));
// 		}
		try {
			if (empty($this->maplinkAuth)) $this->maplinkAuth = @new SoapClient($this->url.'/authentication/authentication.asmx?WSDL', $this->options);			
			$this->token = $this->maplinkAuth->getToken($this->login);
			if (empty($this->maplinkRoute)) $this->maplinkRoute   = @new SoapClient($this->url.'/Route/Route.asmx?WSDL', $this->options);
			if (empty($this->maplinkAddress)) $this->maplinkAddress = @new SoapClient($this->url.'/AddressFinder/AddressFinder.asmx?WSDL', $this->options);
		} catch (Exception $ex) {			
			return false;
		}
		return true;
	}

	public function calcula_tempo_de_varios_alvos($alvos) {
    	$TRefeReferencia 		=& ClassRegistry::init('TRefeReferencia');
    	$TPcomPrecoCombustivel		=& ClassRegistry::init('TPcomPrecoCombustivel');
	    $origem = array(
	        'refe_codigo_destino' => $alvos['refe_codigo_origem'],
	        'tipo_parada' => 1
	    );
	    array_unshift($alvos['Itinerario'], $origem);
		$valores =array();
		$key2 = 0;
		$total_combustivel = 0;
		$total_pedagio = 0;
		$total_distancia = 0;
		$quantia_total_de_alvos =(count($alvos['Itinerario']))-1;
		$valor_gasolina = $TPcomPrecoCombustivel->buscarPorReferencia($alvos['refe_codigo_origem']);
		$valor_gasolina['TPcomPrecoCombustivel']['pcom_valor'] = !empty($valor_gasolina['TPcomPrecoCombustivel']['pcom_valor']) ? $valor_gasolina['TPcomPrecoCombustivel']['pcom_valor'] : 1;
		App::Import('Component',array('Maplink'));
		$this->Maplink = new MaplinkComponent();
		foreach ($alvos['Itinerario'] as $key => $alvo) {
			$key2 = $key+1;
			if($key2 <= $quantia_total_de_alvos) {
				$coordenadas_destino_1 = $TRefeReferencia->retorna_latitude_longitude($alvo['refe_codigo_destino']);
				$coordenadas_destino_2 = $TRefeReferencia->retorna_latitude_longitude($alvos['Itinerario'][$key2]['refe_codigo_destino']);
				$valores = $this->calcula_valores_viagens($coordenadas_destino_1['TRefeReferencia']['refe_latitude'],$coordenadas_destino_1['TRefeReferencia']['refe_longitude'], $coordenadas_destino_2['TRefeReferencia']['refe_latitude'], $coordenadas_destino_2['TRefeReferencia']['refe_longitude']);
				$total_combustivel += isset($valores['valor_combustivel']) ? $valores['valor_combustivel']*$valor_gasolina['TPcomPrecoCombustivel']['pcom_valor'] : 0;
				$total_pedagio += isset($valores['valor_pedagio']) ? $valores['valor_pedagio'] : 0;
				$total_distancia += isset($valores['distancia']) ? $valores['distancia'] : 0;
			}
		}
		$retorno = array('total_combustivel' => $total_combustivel,
						 'total_pedagio' => $total_pedagio,
						 'total_distancia' => $total_distancia,
						 'data_atualizacao' => date('d/m/Y H:i:s'));
		return $retorno;
    }

    function calcula_valores_viagens($latitude_origem, $longitude_origem, $latitude_destino, $longitude_destino, $consumo_medio = 3, $qtd_eixos = 2, $codigo_sm = null, $tipo_calculo = self::TIPO_CALCULO_REALIZADO) {
		$dados = array();
		$this->TRodeRotaDetalhe = ClassRegistry::init('TRodeRotaDetalhe');

		if (!empty($latitude_origem) && !empty($longitude_origem) && !empty($latitude_destino) && !empty($longitude_destino)) {
			try {
				if (!$this->setup()) throw new Exception();

				$ro = new RouteOptions();
				$ro->language = 'portuguese';
				$ro->routeDetails = new RouteDetails();
				$ro->vehicle = new Caminhao($consumo_medio, $qtd_eixos);

				$rs = array();

				$routeStop = new RouteStop();
				$routeStop->description = 'Origem';
				$routeStop->point = new StdClass();
				$routeStop->point->y = (float)$latitude_origem;
				$routeStop->point->x = (float)$longitude_origem;
				$rs[] = $routeStop;
				$routeStop = new RouteStop();
				$routeStop->description = 'Destino';
				$routeStop->point = new Point();
				$routeStop->point->y = (float)$latitude_destino;
				$routeStop->point->x = (float)$longitude_destino;
				$rs[] = $routeStop;

				$parametros1 = array(
					'token' => $this->token->getTokenResult,
					'rs' => $rs,
					'ro' => $ro,
				);
				$inicio_chamada = date('Ymd H:i:s');
				$routeTotal = $this->maplinkRoute->getRouteTotals($parametros1);
				$fim_chamada = date('Ymd H:i:s');				
				$dados['valor_combustivel'] = $routeTotal->getRouteTotalsResult->totalfuelCost;
				$dados['valor_pedagio'] = $routeTotal->getRouteTotalsResult->totaltollFeeCost;
				$dados['distancia'] = $routeTotal->getRouteTotalsResult->totalDistance;
				$dados['quantia_combustivel'] = $routeTotal->getRouteTotalsResult->totalFuelUsed;
				//$dados['valor_total'] = $routeTotal->getRouteTotalsResult->totalCost;
				// Gravação do Log da Operação
				$descricao_operacao = 'Calculo de '.$tipo_calculo." da SM ".$codigo_sm;
				$this->gravarLog('calcula_valores_viagens',self::URL_ROUTE,'getRouteTotals',$parametros1,$dados,$descricao_operacao,compact('codigo_sm', 'inicio_chamada', 'fim_chamada'));
				if(!$this->TRodeRotaDetalhe->verifica_existe($latitude_origem, $longitude_origem, $latitude_destino, $longitude_destino)){
					$tempo = new DateInterval($routeTotal->getRouteTotalsResult->totalTime);
					$this->TRodeRotaDetalhe->incluir(
						array(
							'TRodeRotaDetalhe' => array(
								'rode_latitude_origem'   => $latitude_origem,
								'rode_longitude_origem'  => $longitude_origem,
								'rode_latitude_destino'  => $latitude_destino,
								'rode_longitude_destino' => $longitude_destino,
								'rode_tempo'             => (float)$tempo->format('%i')+($tempo->format('%H')*60)+($tempo->format('%d')*1440),
								'rode_distancia'         => $routeTotal->getRouteTotalsResult->totalDistance,
								'rode_data_cadastro'     => date('Ymd H:i:s')
							)
						)
					);
				}

				return $dados; 
			} catch (Exception $ex) {
			}
		}
		return $dados;
	}

    function calcula_valores_viagens_com_waypoints($ponto_origem, $ponto_destino, $waypoints, $consumo_medio = 3, $qtd_eixos = 2, $codigo_sm = null, $tipo_calculo = self::TIPO_CALCULO_REALIZADO) {
		$dados = array();
		if (empty($ponto_origem) || empty($ponto_destino)) return $dados;
		if (empty($ponto_origem['latitude']) || empty($ponto_origem['longitude'])) return $dados;
		if (empty($ponto_destino['latitude']) || empty($ponto_destino['longitude'])) return $dados;
		try {
			if (!$this->setup()) throw new Exception();

			$ro = new RouteOptions();
			$ro->language = 'portuguese';
			$ro->routeDetails = new RouteDetails();
			$ro->vehicle = new Caminhao($consumo_medio, $qtd_eixos);

			$rs = array();
			$routeStop = new RouteStop();
			$routeStop->description = 'Origem';
			$routeStop->point = new StdClass();
			$routeStop->point->y = (float)($ponto_origem['latitude']);
			$routeStop->point->x = (float)($ponto_origem['longitude']);
			$rs[] = $routeStop;
			foreach ($waypoints as $key => $waypoint) {
				$routeStop = new RouteStop();
				$routeStop->description = 'Parada';
				$routeStop->point = new StdClass();
				$routeStop->point->y = (float)($waypoint['latitude']);
				$routeStop->point->x = (float)($waypoint['longitude']);
				$rs[] = $routeStop;
			}

			$routeStop = new RouteStop();
			$routeStop->description = 'Destino';
			$routeStop->point = new Point();
			$routeStop->point->y = (float)($ponto_destino['latitude']);
			$routeStop->point->x = (float)($ponto_destino['longitude']);
			$rs[] = $routeStop;

			$parametros1 = array(
				'token' => $this->token->getTokenResult,
				'rs' => $rs,
				'ro' => $ro,
			);
			//$MaplinkRoute = @new SoapClient($this->url.'/Route/Route.asmx?WSDL', $this->options);
			$inicio_chamada = date('Ymd H:i:s');
			$routeTotal = $this->maplinkRoute->getRouteTotals($parametros1);
			$fim_chamada = date('Ymd H:i:s');
			$dados['valor_combustivel'] = $routeTotal->getRouteTotalsResult->totalfuelCost;
			$dados['valor_pedagio'] = $routeTotal->getRouteTotalsResult->totaltollFeeCost;
			$dados['distancia'] = $routeTotal->getRouteTotalsResult->totalDistance;
			$dados['quantia_combustivel'] = $routeTotal->getRouteTotalsResult->totalFuelUsed;
			//$dados['valor_total'] = $routeTotal->getRouteTotalsResult->totalCost;
			// Gravação do Log da Operação
			$descricao_operacao = 'Calculo de '.$tipo_calculo." da SM ".$codigo_sm;
			$this->gravarLog('calcula_valores_viagens',self::URL_ROUTE,'getRouteTotals',$parametros1,$dados,$descricao_operacao,compact('codigo_sm', 'inicio_chamada', 'fim_chamada'));			

			return $dados; 
		} catch (Exception $ex) {
			debug($ex->getMessage());
		}
		return $dados;
	}
		
	function calcula_tempo_restante(&$dados, $latitude_origem, $longitude_origem, $latitude_destino, $longitude_destino, $codigo_sm = null) {		
		$this->TRodeRotaDetalhe = ClassRegistry::init('TRodeRotaDetalhe');
		if (!empty($latitude_origem) && !empty($longitude_origem) && !empty($latitude_destino) && !empty($longitude_destino)) {
			try {
				$retorno = $this->TRodeRotaDetalhe->verifica_existe($latitude_origem, $longitude_origem, $latitude_destino, $longitude_destino);
				
				if(!$retorno){					
					if (!$this->setup()) 
						throw new Exception();					
					$ro = new RouteOptions();
					$ro->language = 'portuguese';
					$ro->routeDetails = new RouteDetails();
					$ro->vehicle = new Vehicle();

					$rs = array();

					$routeStop = new RouteStop();
					$routeStop->description = 'Origem';
					$routeStop->point = new StdClass();
					$routeStop->point->y = (float)$latitude_origem;
					$routeStop->point->x = (float)$longitude_origem;
					$rs[] = $routeStop;
					$routeStop = new RouteStop();
					$routeStop->description = 'Destino';
					$routeStop->point = new Point();
					$routeStop->point->y = (float)$latitude_destino;
					$routeStop->point->x = (float)$longitude_destino;
					$rs[] = $routeStop;

					$parametros1 = array(
						'token' => $this->token->getTokenResult,
						'rs' => $rs,
						'ro' => $ro,
					);
					//$this->log('Iniciar consulta '.date('Y-m-d H:i:s'), 'maplink');
					//$MaplinkRoute = @new SoapClient($this->url.'/Route/Route.asmx?WSDL', $this->options);
					$inicio_chamada = date('Ymd H:i:s');
					$routeTotal = $this->maplinkRoute->getRouteTotals($parametros1);
					$fim_chamada = date('Ymd H:i:s');
					//$this->log('Retorno Consulta '.date('Y-m-d H:i:s'), 'maplink');
					$tem_numero = Comum::soNumero($routeTotal->getRouteTotalsResult->totalTime);
					if (strlen($tem_numero) > 0 && class_exists('DateInterval')) {						
						$tempo = new DateInterval($routeTotal->getRouteTotalsResult->totalTime);
						$tempo_string = $tempo->format('%d Dias %H Horas %i Minutos');
						$dados['distancia'] = $routeTotal->getRouteTotalsResult->totalDistance;
						$dados['tempo'] = $tempo_string;			
						$tempo_total = $tempo->format('%i')+($tempo->format('%H')*60)+($tempo->format('%d')*1440);
						if(isset($dados['tempo_em_minutos'])){
							$dados['tempo_em_minutos'] = $tempo_total;
						}
						$this->TRodeRotaDetalhe->incluir(
							array(
								'TRodeRotaDetalhe' => array(
									'rode_latitude_origem'   => $latitude_origem,
									'rode_longitude_origem'  => $longitude_origem,
									'rode_latitude_destino'  => $latitude_destino,
									'rode_longitude_destino' => $longitude_destino,
									'rode_tempo'             => (float)$tempo_total,
									'rode_distancia'         => $dados['distancia'],
									'rode_data_cadastro'     => date('Ymd H:i:s')
								)
							)
						);
					}
					if (empty($codigo_sm)) {
						$descricao_operacao = 'Calculo de Previsão de Datas para Itinerario de SM em digitação.';
					} else {
						$descricao_operacao = 'Calculo de Tempo Restante da SM '.$codigo_sm;
					}
					$this->gravarLog('calcula_tempo_restante',self::URL_ROUTE,'getRouteTotals',$parametros1,$dados,$descricao_operacao,compact('codigo_sm', 'inicio_chamada', 'fim_chamada'));				
				}else{	
					$dados['distancia'] = $retorno['distancia'];
					$dados['tempo'] = $retorno['tempo_string'];
					$dados['tempo_em_minutos'] = $retorno['tempo_em_minutos'];
					$parametros1 = NULL;
				}
				
			} catch (Exception $ex) {				
			}
		}
	}

	function calcula_tempo_restante_sms(&$sms, $tipo_calculo = null, $tipo_chamada = ORIGEM_CHAMADA_TTIME) {
		$this->setup();
		$limitador = 0;
		$multi_request_route = array();

		foreach ($sms as $key => $sm) {
			$sms[$key]['distancia'] = '';
			$sms[$key]['tempo'] = '';
			$sms[$key]['tempo_em_minutos'] = '';
			$codigo_sm = (isset($sm['TViagViagem']) && !empty($sm['TViagViagem']['viag_codigo_sm']) ?  $sm['TViagViagem']['viag_codigo_sm'] : '');
			if (empty($codigo_sm)) {
				$codigo_sm = (isset($sm['Recebsm']) && !empty($sm['Recebsm']['SM']) ?  $sm['Recebsm']['SM'] : '');
			}

			if ($this->validaInformacoesNecessarias($sm)) {
				$limitador++;
				$MRSOrigin = new MRouteStop();
				$MRSOrigin->description = $key.'_Origem';
				$MRSOrigin->point = new MPoint();
				$MRSOrigin->point->y = isset($sm['ViagemLocal']) ? $sm['ViagemLocal']['upos_latitude'] : $sm['TUposUltimaPosicao']['upos_latitude'];
				$MRSOrigin->point->x = isset($sm['ViagemLocal']) ? $sm['ViagemLocal']['upos_longitude'] : $sm['TUposUltimaPosicao']['upos_longitude'];
				$MRSDestination = new MRouteStop();
				$MRSDestination->description = $key.'_Destino';
				$MRSDestination->point = new MPoint();
				if($tipo_calculo == self::VELOCIDADE_MEDIA){
					$tempo_viagem_segundos = strtotime(AppModel::dateToDbDate($sm['TViagViagem']['viag_previsao_fim'])) - strtotime(AppModel::dateToDbDate($sm['TViagViagem']['viag_previsao_inicio']));
					$tempo_viagem_horas = ($tempo_viagem_segundos /3600);
					$velocidade_media = number_format(($sm['TViagViagem']['viag_distancia'] / $tempo_viagem_horas), 2);
				}

				$MRSDestination->point->y = isset($sm['ViagemLocal']) ? $sm['ViagemLocal']['refe_latitude'] : $sm['Destino']['refe_latitude'];
				$MRSDestination->point->x = isset($sm['ViagemLocal']) ? $sm['ViagemLocal']['refe_longitude'] : $sm['Destino']['refe_longitude'];
				$multi_request_route[] = array(
					'origin' => $MRSOrigin,
					'destination' => $MRSDestination,
				);

				if(isset($velocidade_media))
					$this->solicitaDados($sms, $multi_request_route, $velocidade_media, $codigo_sm, $tipo_chamada);
				else
					$this->solicitaDados($sms, $multi_request_route, false, $codigo_sm, $tipo_chamada);
				
				$multi_request_route = array();
			}
		}
	}

	function validaInformacoesNecessarias($sm) {
		//$sm['TUposUltimaPosicao']['upos_latitude'] = $sm['Destino']['refe_latitude'];
		if (isset($sm['ViagemLocal']))
			return ($sm['Recebsm']['encerrada'] != 'S' && !empty($sm['ViagemLocal']['refe_latitude']) && !empty($sm['ViagemLocal']['refe_latitude']));
		if (isset($sm['TUposUltimaPosicao']['upos_latitude']) && isset($sm['Destino']['refe_latitude'])) {
			return (empty($sm['TViagViagem']['viag_data_fim']) && !empty($sm['Destino']['refe_latitude']) && !empty($sm['TUposUltimaPosicao']['upos_latitude']));
		}
	}
	
	function solicitaDados(&$sms, $multi_request_route, $velocidade_media = false, $codigo_sm = null, $tipo_chamada = null) {
		$ro = new RouteOptions();
		$this->TRodeRotaDetalhe = ClassRegistry::init('TRodeRotaDetalhe');
		$ro->language = 'portuguese';
		$ro->routeDetails = new RouteDetails();
		$ro->vehicle = new Vehicle();
		try {
			if (!$this->setup()) throw new Exception();
			$parametros = array(
				'token' => $this->token->getTokenResult,
				'request' => null,
				'ro' => $ro,
			);			
			$parametros['request'] = $multi_request_route;			
			$inicio_chamada = date('Ymd H:i:s');
			// $retorno = $this->TRodeRotaDetalhe->verifica_existe(
			// 	$multi_request_route[0]['origin']->point->y, 
			// 	$multi_request_route[0]['origin']->point->x, 
			// 	$multi_request_route[0]['destination']->point->y, 
			// 	$multi_request_route[0]['destination']->point->x
			// );


			// if(!$retorno){
				$multiRouteTotal = $this->maplinkRoute->getMultiRoute($parametros);				
				$fim_chamada = date('Ymd H:i:s');
				$alvos = $multiRouteTotal->getMultiRouteResult->singleRouteTotals->SingleRouteTotals;				
				// foreach($alvos as $key => $alvo){
				// 	$tempo = new DateInterval($alvo->routeTotals->totalTime);
				// 	$this->TRodeRotaDetalhe->incluir(
				// 		array(
				// 			'TRodeRotaDetalhe' => array(
				// 				'rode_latitude_origem'   => $multi_request_route[0]['origin']->point->y,
				// 				'rode_longitude_origem'  => $multi_request_route[0]['origin']->point->x,
				// 				'rode_latitude_destino'  => $multi_request_route[0]['destination']->point->y,
				// 				'rode_longitude_destino' => $multi_request_route[0]['destination']->point->x,
				// 				'rode_tempo'             => (float)$tempo->format('%i')+($tempo->format('%H')*60)+($tempo->format('%d')*1440),
				// 				'rode_distancia'         => $alvo->routeTotals->totalDistance,
				// 				'rode_data_cadastro'     => date('Ymd H:i:s')
				// 			)
				// 		)
				// 	);
				// }
			// }else{
			// 	$alvos = array();
			// 	foreach($multi_request_route as $key => $valor){
			// 		$aux = (object)$valor;
			// 		$aux->routeTotals->totalDistance = $retorno['distancia'];
			// 		$aux->routeTotals->totalTime = $retorno['interval_spec'];
			// 		$multiRouteTotal->getMultiRouteResult->singleRouteTotals->SingleRouteTotals = $aux;
			// 		$alvos[$key] = (object)$aux;
			// 	}	
			// }

			foreach ($alvos as $key => $singleRouteTotals) {
				// $keys = split('_', $singleRouteTotals->origin->description);
				// $key = $keys[0];
				$sms[$key]['distancia']  = $singleRouteTotals->routeTotals->totalDistance;
				$sms[$key][0]['KmRestante'] = $sms[$key]['distancia'];
				if ($velocidade_media){
					$hour_decimal = ($sms[$key]['distancia'] / $velocidade_media);
					$hour = floor($hour_decimal);
					$minut_decimal = ($hour_decimal - $hour) * 60;
					$minut = floor($minut_decimal);
					$tempo_em_minutos = ($hour * 60) + $minut;
					$second = floor(($minut_decimal - $minut)*60);
					$sms[$key]['tempo'] = '+'.$hour.'hour +'.$minut.'minute +'.$second.'second';
					$sms[$key]['tempo_em_minutos'] = $tempo_em_minutos;
					//Preenchendo os valores de tempo pois os valores nao estavam sendo carregador na posicao correta do array
					$sms[$key][0]['TempoRestante']        = $sms[$key]['tempo']; 
					$sms[$key][0]['TempoMinutosRestante'] = $sms[$key]['tempo_em_minutos'];
				} else {
					$tem_numero = Comum::soNumero($singleRouteTotals->routeTotals->totalTime);
					if (count($tem_numero) > 0 && class_exists('DateInterval')) {
						$tempo = new DateInterval($singleRouteTotals->routeTotals->totalTime);
						$sms[$key]['tempo_em_minutos'] = $tempo->format('%i')+($tempo->format('%H')*60)+($tempo->format('%d')*1440);
						$tempo = $tempo->format('%d Dias %H Horas %i Minutos');
						$sms[$key]['tempo'] = $tempo;
						//Preenchendo os valores de tempo pois os valores nao estavam sendo carregador na posicao correta do array
						$sms[$key][0]['TempoRestante']        = $sms[$key]['tempo']; 
						$sms[$key][0]['TempoMinutosRestante'] = $sms[$key]['tempo_em_minutos'];
					}
				}
			}
			$descricao_operacao = 'Calculo de Tempo Restante da SM '.$codigo_sm.' - Chamada a partir de '.$tipo_chamada;
			$this->gravarLog('solicitaDados',self::URL_ROUTE,'getMultiRoute',$parametros,$multiRouteTotal->getMultiRouteResult->singleRouteTotals->SingleRouteTotals,$descricao_operacao,compact('codigo_sm', 'inicio_chamada', 'fim_chamada'));
		} catch (Exception $ex) {
			debug($ex->getMessage());
		}

	}
	
	function calcula_tempo_restante_parametrizado(&$dados, $options = array()) {
	    $options_default = array(
	       'resposta_model' => '0',
	       'resposta_distancia_key' => 'KmRestante',
	       'resposta_tempo_key' => 'TempoRestante',
	       'resposta_tempo_minutos_key' => 'TempoMinutosRestante',
	       'origem_model'=> '0',
	       'origem_latitude' => 'UltimaPosicaoLatitude',
	       'origem_longitude' => 'UltimaPosicaoLongitude',
	       'destino_model' => '0',
	       'destino_latitude' => 'ProximoAlvoLatitude',
	       'destino_longitude' => 'ProximoAlvoLongitude',
	    );
	    $options = array_merge($options_default, $options);	    
	    $this->setup();
		$limitador = 0;
		$multi_request_route = array();
		foreach ($dados as $key => $dado) {
		    $dados[$key][$options['resposta_model']][$options['resposta_distancia_key']] = '';
			$dados[$key][$options['resposta_model']][$options['resposta_tempo_key']] = '';
			$dados[$key][$options['resposta_model']][$options['resposta_tempo_minutos_key']] = '';
			$codigo_sm = (!empty($dado[$options['resposta_model']]['SM']) ? $dado[$options['resposta_model']]['SM'] : '');
			if ($this->validaInformacoesNecessariasParametrizado($dado, $options)) {
				$limitador++;
				$MRSOrigin = new MRouteStop();
				$MRSOrigin->description = $key.'_Origem';
				$MRSOrigin->point = new MPoint();
				$MRSOrigin->point->y = $dado[$options['origem_model']][$options['origem_latitude']];
				$MRSOrigin->point->x = $dado[$options['origem_model']][$options['origem_longitude']];
				$MRSDestination = new MRouteStop();
				$MRSDestination->description = $key.'_Destino';
				$MRSDestination->point = new MPoint();
				$MRSDestination->point->y = $dado[$options['destino_model']][$options['destino_latitude']];
				$MRSDestination->point->x = $dado[$options['destino_model']][$options['destino_longitude']];
				$multi_request_route[] = array(
					'origin' => $MRSOrigin,
					'destination' => $MRSDestination,
				);
			}
			if ($limitador == 5) {
				$limitador = 0;
			$this->solicitaDadosParametrizado($dados, $options, $multi_request_route ,$codigo_sm,self::ORIGEM_CHAMADA_ANALITICO);
				$multi_request_route = array();
			}
		}
		if ($limitador > 0) {
			$this->solicitaDados($dados, $multi_request_route,null,$codigo_sm,self::ORIGEM_CHAMADA_ANALITICO);
		}
	}

	function validaInformacoesNecessariasParametrizado($dado, $options) {
		if (isset($dado['ViagemLocal']))
			return ($dado['Recebdado']['encerrada'] != 'S' && !empty($dado['ViagemLocal']['refe_latitude']) && !empty($dado['ViagemLocal']['refe_latitude']));
		if (isset($dado['TUposUltimaPosicao']['upos_latitude']) && isset($dado['Destino']['refe_latitude']))
			return (empty($dado['TViagViagem']['viag_data_fim']) && !empty($dado['Destino']['refe_latitude']) && !empty($dado['TUposUltimaPosicao']['upos_latitude']));
		return !empty($dado[$options['origem_model']][$options['origem_latitude']]) && !empty($dado[$options['origem_model']][$options['origem_longitude']])
		    && !empty($dado[$options['destino_model']][$options['destino_latitude']]) && !empty($dado[$options['destino_model']][$options['destino_longitude']]);
	}
	
	function solicitaDadosParametrizado(&$dados, $options, $multi_request_route, $codigo_sm = null, $tipo_chamada = null) {
		$ro = new RouteOptions();
		$ro->language = 'portuguese';
		$ro->routeDetails = new RouteDetails();
		$ro->vehicle = new Vehicle();
		$parametros = array(
			'token' => $this->token->getTokenResult,
			'request' => null,
			'ro' => $ro,
		);
		$parametros['request'] = $multi_request_route;		
		try {
			$inicio_chamada = date('Ymd H:i:s');
			$multiRouteTotal = $this->maplinkRoute->getMultiRoute($parametros);
			$fim_chamada = date('Ymd H:i:s');
			foreach ($multiRouteTotal->getMultiRouteResult->singleRouteTotals->SingleRouteTotals as $singleRouteTotals) {
				$key = split('_', $singleRouteTotals->origin->description);
				$key = $key[0];
				$dados[$key][$options['resposta_model']][$options['resposta_distancia_key']] = $singleRouteTotals->routeTotals->totalDistance;
				$tem_numero = Comum::soNumero($singleRouteTotals->routeTotals->totalTime);
				if (count($tem_numero) > 0 && class_exists('DateInterval')) {
					$tempo = new DateInterval($singleRouteTotals->routeTotals->totalTime);
					$dados[$key][$options['resposta_model']][$options['resposta_tempo_key']] = $tempo->format('%d Dias %H Horas %i Minutos');
					$dados[$key][$options['resposta_model']][$options['resposta_tempo_minutos_key']] = $tempo->format('%i')+($tempo->format('%H')*60)+($tempo->format('%d')*1440);
				}
			}
			$descricao_operacao = 'Calculo de Tempo Restante da SM '.$codigo_sm.' - Chamada a partir de '.$tipo_chamada;
			$this->gravarLog('solicitaDadosParametrizado',self::URL_ROUTE,'getMultiRoute',$parametros,$multiRouteTotal->getMultiRouteResult->singleRouteTotals->SingleRouteTotals,$descricao_operacao,compact('codigo_sm', 'inicio_chamada', 'fim_chamada'));
		} catch (Exception $ex) {

		}
	}

	/*
	** Para descobrir XY a partir de endereço completo ou parcial
	** MODELO DE PARAMETRO DE ENDEREÇO
	**
	** $new_local = array(
	**					'endereco' 	=> 'Alameda dos Guatás',
	**					'bairro' 	=> 'Saúde',
	**					'numero' 	=> '102',
	**					'cep' 		=> '04053040',
	**					'cidade'	=> array(
	**									'nome'	=> 'São Paulo',
	**									'estado'=> 'SP')
	**				);
	*/

	function busca_xy(&$new_local) {
		if(!isset($this->maplinkAddress)){			
			if(!$this->setup()) {
				return FALSE;
			}
		}	
		$simbolos= array('-','.','\'','\"','~');
		$address = new Address();
		$city 	 = new City();
		
		if(isset($new_local['endereco']) && !empty($new_local['endereco'])){
			$address->street = str_replace($simbolos, ' ', $new_local['endereco']);
		}

		if(isset($new_local['bairro']) && !empty($new_local['bairro'])){
			$address->district = $new_local['bairro'];
		}

		if(isset($new_local['numero']) && !empty($new_local['numero'])){
			$address->houseNumber = $new_local['numero'];
		}

		if(isset($new_local['cep']) && !empty($new_local['cep'])){
			$address->zip = $new_local['cep'];
		}

		if(isset($new_local['cidade']['nome']) && !empty($new_local['cidade']['nome'])){
			$city->name = str_replace($simbolos, ' ', $new_local['cidade']['nome']);
		}

		if(isset($new_local['cidade']['estado']) && !empty($new_local['cidade']['estado'])){
			$city->state = $new_local['cidade']['estado'];
		}

		$address->city 	= $city;
		$ao 			= new Ao();
		$local 			= array(
			'token' => $this->token->getTokenResult, 
			'address' => $address, 
			'ao' => $ao
		);

		try{
			$inicio_chamada = date('Ymd H:i:s');
			$retorno = $this->maplinkAddress->getXY($local);			
			$fim_chamada = date('Ymd H:i:s');

			$descricao_operacao = 'Pesquisa de Latitude / Longitude de Endereco';
			$this->gravarLog('busca_xy',self::URL_ADRESS,'getXY',$local,$retorno,$descricao_operacao, compact('inicio_chamada', 'fim_chamada'));

			if (!($retorno->getXYResult->x !=  -52.200165 && $retorno->getXYResult->y != -12.924042)) {
				unset($local['address']->zip);
				$inicio_chamada = date('Ymd H:i:s');
				$retorno = $this->maplinkAddress->getXY($local);
				$fim_chamada = date('Ymd H:i:s');

				$descricao_operacao = 'Pesquisa de Latitude / Longitude de Endereco';
				$this->gravarLog('busca_xy',self::URL_ADRESS,'getXY',$local,$retorno,$descricao_operacao, compact('inicio_chamada', 'fim_chamada'));
			}
			return $retorno;

		} catch (Exception $ex) {
			return FALSE;
		}
	}

	// Para descobrir Endereço Completo a partir de endereço parcial
	function busca_endereco($new_local) {
		if(!isset($this->maplinkAddress)){
			if(!$this->setup()) return FALSE;
		}

		$simbolos= array('-','.','\'','\"','~');
		$address = new Address();
		$city 	 = new City();
		
		if(isset($new_local['endereco']) && !empty($new_local['endereco'])){
			$address->street = str_replace($simbolos, ' ', $new_local['endereco']);
		}

		if(isset($new_local['bairro']) && !empty($new_local['bairro'])){
			$address->district = $new_local['bairro'];
		}

		if(isset($new_local['numero']) && !empty($new_local['numero'])){
			$address->houseNumber = $new_local['numero'];
		}

		if(isset($new_local['cep']) && !empty($new_local['cep'])){
			$address->zip = $new_local['cep'];
		}

		if(isset($new_local['cidade']['nome']) && !empty($new_local['cidade']['nome'])){
			$city->name = str_replace($simbolos, ' ', $new_local['cidade']['nome']);
		}

		if(isset($new_local['cidade']['estado']) && !empty($new_local['cidade']['estado'])){
			$city->state = $new_local['cidade']['estado'];
		}

		if(isset($new_local['point']['lat']) && isset($new_local['point']['long'])){
			$address->point = new Point;
			$address->point->y = $new_local['point']['lat'];
			$address->point->x = $new_local['point']['long'];
		}

		$address->city 	= $city;
		$ao 			= new Ao();
		$local 			= array('token' => $this->token->getTokenResult, 'address' => $address, 'ao' => $ao);

		try{
			$inicio_chamada = date('Ymd H:i:s');
			$retorno = $this->maplinkAddress->findAddress($local);
			$fim_chamada = date('Ymd H:i:s');

			$descricao_operacao = 'Pesquisa de Endereco Completo a partir de endereco parcial';
			$this->gravarLog('busca_endereco',self::URL_ADRESS,'findAddress',$local,$retorno,$descricao_operacao, compact('inicio_chamada', 'fim_chamada'));

			return $retorno;

		} catch (Exception $ex) {
			return FALSE;
		}
	}

	// Para descobrir Endereço Completo a partir das coordenadas
	function busca_endereco_xy($new_local) {
		if(!isset($this->maplinkAddress)){
			if(!$this->setup()) return FALSE;
		}

		$point = new Point;
		
		if(isset($new_local['point']['lat']) && $new_local['point']['lat'])
			$point->y = $new_local['point']['lat'];

		 if(isset($new_local['point']['long']) && $new_local['point']['long'])
			$point->x = $new_local['point']['long'];
		

		$local 			= array('token' => $this->token->getTokenResult, 'point' => $point, 'tolerance' => 500);

		try{
			$inicio_chamada = date('Ymd H:i:s');
			$retorno = $this->maplinkAddress->getAddress($local);
			$fim_chamada = date('Ymd H:i:s');

			$descricao_operacao = 'Pesquisa de Endereco Completo a partir das coordenadas';
			$this->gravarLog('busca_endereco_xy',self::URL_ADRESS,'getAddress',$local,$retorno,$descricao_operacao, compact('inicio_chamada', 'fim_chamada'));

			return $retorno;

		} catch (Exception $ex) {
			return FALSE;
		}
	}

	private function gravarLog($funcao_interna = "", $url_maplink = '',$funcao_maplink = "", $parametros_entrada = "", $resultado = "", $descricao_chamada = "", $extras = Array()) {
		App::import('Vendor', 'xml'.DS.'array2_xml');
		$this->logMaplink = ClassRegistry::init('LogMaplink');

		extract($extras);
		if (!isset($inicio_chamada)) {
			$inicio_chamada = null;
		}
		if (!isset($fim_chamada)) {
			$fim_chamada = null;
		}

		$parametros_entrada = Comum::objectToArray($parametros_entrada);
		$resultado = Comum::objectToArray($resultado);

		foreach ($resultado as $key => $value) {
			if (is_numeric($key)) {
				$resultado['it'.$key] = $resultado[$key];
				unset($resultado[$key]);
			}
		}


		$my_xml = Array2XML::createXML($funcao_maplink,$parametros_entrada);
		$parametros_entrada = $my_xml->saveXml();

		$my_xml = Array2XML::createXML('retorno',$resultado);
		$resultado = $my_xml->saveXml();

		//debug(htmlentities($resultado));

		//die;
		$url_maplink = $this->url.$url_maplink;

		$dados_log = Array(
			'nome_chamada_interna' => $funcao_interna,
			'url_chamada_maplink' => $url_maplink,
			'nome_chamada_maplink' => $funcao_maplink,
			'parametros_entrada' => $parametros_entrada,
			'retorno' => $resultado,
			'descricao_chamada' => $descricao_chamada,
			'codigo_sm' => (isset($codigo_sm) ? $codigo_sm : ''),
			'inicio_chamada' => $inicio_chamada,
			'fim_chamada' => $fim_chamada
		);

		$this->logMaplink->save($dados_log);
	}



	function carregarTempoDistanciaPorLatLong( $latitude_origem, $longitude_origem, $latitude_destino, $longitude_destino ) {

		$this->TRodeRotaDetalhe = ClassRegistry::init('TRodeRotaDetalhe');
		$retorno = $this->TRodeRotaDetalhe->verifica_existe($latitude_origem, $longitude_origem, $latitude_destino, $longitude_destino);

		if(!$retorno){

			if (!$this->setup()){
				return false; 
			}
			$dados = array();
			$ro = new RouteOptions();
			$ro->language = 'portuguese';
			$ro->routeDetails = new RouteDetails();
			$ro->vehicle = new Vehicle();
			$rs = array();
			$routeStop = new RouteStop();
			$routeStop->description = 'Origem';
			$routeStop->point = new StdClass();
			$routeStop->point->y = (float)$latitude_origem;
			$routeStop->point->x = (float)$longitude_origem;
			$rs[] = $routeStop;
			$routeStop = new RouteStop();
			$routeStop->description = 'Destino';
			$routeStop->point = new Point();
			$routeStop->point->y = (float)$latitude_destino;
			$routeStop->point->x = (float)$longitude_destino;
			$rs[] = $routeStop;
			$parametros1 	= array('token' => $this->token->getTokenResult, 'rs' => $rs, 'ro' => $ro );
			$routeTotal 	= $this->maplinkRoute->getRouteTotals($parametros1);
			$tem_numero 	= Comum::soNumero($routeTotal->getRouteTotalsResult->totalTime);
			if (strlen($tem_numero) > 0 && class_exists('DateInterval')) {
				$tempo = new DateInterval($routeTotal->getRouteTotalsResult->totalTime);
				$tempo_string = $tempo->format('%d Dias %h Horas %i Minutos');
				$dados['distancia'] = $routeTotal->getRouteTotalsResult->totalDistance;
				$minutos = $tempo->format('%I');
				$horas   = comum::StrZero( (($tempo->format('%d')*24) + $tempo->format('%h')), 2 );
				$dados['tempo'] = "$horas:$minutos";
				// if(isset($dados['tempo_em_minutos'])){
				$dados['tempo_em_minutos'] = $tempo->format('%i')+($tempo->format('%H')*60)+($tempo->format('%d')*1440);
				// }

				$this->TRodeRotaDetalhe->incluir(
					array(
						'TRodeRotaDetalhe' => array(
							'rode_latitude_origem'   => $latitude_origem,
							'rode_longitude_origem'  => $longitude_origem,
							'rode_latitude_destino'  => $latitude_destino,
							'rode_longitude_destino' => $longitude_destino,
							'rode_tempo'             => (float)$dados['tempo_em_minutos'],
							'rode_distancia'         => $routeTotal->getRouteTotalsResult->totalDistance,
							'rode_data_cadastro'     => date('Ymd H:i:s')
						)
					)
				);

				$inicio_chamada = date('Ymd H:i:s');				
				$fim_chamada = date('Ymd H:i:s');

				$descricao_operacao = 'Retorna tempo e distância de dois pontos';
				$this->gravarLog('carregarTempoDistanciaPorLatLong',self::URL_ROUTE,'getRouteTotals',$parametros1,$dados,$descricao_operacao,compact('inicio_chamada', 'fim_chamada'));				

				return $dados;
			}
		}else{
			//$dados['tempo_em_minutos'] = $tempo->format('%i')+($tempo->format('%H')*60)+($tempo->format('%d')*1440);			
			return $retorno;
		}
		return false;
	}
}

class Vehicle {
	var $tankCapacity = 64;
	var $averageConsumption = 10;
	var $fuelPrice = 2.69;
	var $averageSpeed = 60;
	var $tollFeeCat = 10;
}

class RouteDetails {
	var $descriptionType = 1;
	var $optimizeRoute = false;
	var $routeType = 0;
}

class Point {
	var $x = NULL;
	var $y = NULL;
}

class Caminhao {
	var $tankCapacity = 150;
	var $averageConsumption = 3;
	var $fuelPrice = 1; // Setado , para ser alterado na exibição  com o preço atual
	var $averageSpeed = 60;
	var $tollFeeCat = 7; // Para calculo do pedagio

	public function __construct($consumo_medio = 3, $qtd_eixos = 2) {
		$this->averageConsumption = $consumo_medio;
		$de_para_eixos = Array(
			2 => 7,
			3 => 8,
			4 => 9,
			5 => 10,
			6 => 11,
			7 => 12,
			8 => 13,
			9 => 14
		);
		$this->tollFeeCat = (isset($de_para_eixos[$qtd_eixos]) ? $de_para_eixos[$qtd_eixos] : 14);
	}
}
/*
tollFeeCat http://dev.maplink.com.br/javascript-api/guia-referencia/#ReferenceGuideRoute
0 	Não será calculado o valor do pedágio
1 	Motocicletas, motonetas e bicicletas a motor
2 	Automóvel, caminhoneta e furgão (dois eixos simples)
3 	Automóvel, caminhoneta com semi-reboque (três eixos simples)
4 	Automóvel, caminhoneta com reboque (quatro eixos simples)
5 	Ônibus (dois eixos duplos)
6 	Ônibus com reboque (três eixos duplos)
7 	Caminhão leve, furgão e cavalo mecânico (dois eixos duplos)
8 	Caminhão, caminhão trator e cavalo mecânico com semi-reboque (três eixos duplos)
9 	Caminhão com reboque e cavalo mecânico com semi-reboque (quatro eixos duplos)
10 	Caminhão com reboque e cavalo mecânico com semi-reboque (cinco eixos duplos)
11 	Caminhão com reboque e cavalo mecânico com semi-reboque (seis eixos duplos)
12 	Caminhão com reboque e cavalo mecânico com semi-reboque (sete eixos duplos)
13 	Caminhão com reboque e cavalo mecânico com semi-reboque (oito eixos duplos)
14 	Caminhão com reboque e cavalo mecânico com semi-reboque (nove eixos duplos)
*/

class RouteStop {

}

class RouteOptions {

}

class MRouteStop {

}

class MPoint {

}

class City {
	var $name;
	var $state;
}

class Address {
	var $street;
	var $district;
	var $houseNumber;
	var $zip;
	var $city;

	public function __construct()
	{
		$this->city= new City();
	}
}

class ResultRange{
	var $pageIndex = 1;
	var $recordsPerPage = 1;
}

//Classe AddressOptions
class ao{
	var $matchType = 0;
	var $searchType = 2;
	var $usePhonetic = true;
	var $resultRange;
	public function __construct()
	{
		$this->resultRange = new ResultRange();
	}
}