<?php
class RestViagensController extends AppController {
	var $name = 'RestViagens';
	public $components = array('DbbuonnyGuardian');
	var $uses = array(
		'Cliente',
		'ClientEmpresa',
		'MCaminhao',
		'MCarreta',
		'MWebsm',
		'ProdutoServico',
		'Profissional',
		'Recebsm',
		'SmIntegracao',
		'TBandBandeira',
		'TCidaCidade',
		'TCrefClasseReferencia',
		'TEescEmpresaEscolta',
		'TEstaEstado',
		'TGrisGerenciadoraRisco',
		'TPjurPessoaJuridica',
		'TRefeReferencia',
		'TTlocTipoLocal',
		'TTveiTipoVeiculo',
		'TVeicVeiculo',
		'TViagViagem',
		'TVtecVersaoTecnologia',
		'Usuario',
		'TTparTipoParada',
		'TPessPessoa',
	);

	public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('add','get_posicao', 'nao_finalizadas'));
    }

    private function carregaDados(){
    	$dados = array();

        // first of all, pull the GET vars
        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $dados);
        }
        $body = file_get_contents("php://input");
        //$this->log(print_r($body, true), 'rest');
        $content_type = false;
        if(isset($_SERVER['CONTENT_TYPE'])) {
            $content_type = explode(';', $_SERVER['CONTENT_TYPE']);
        }
        if(in_array("application/x-www-form-urlencoded", $content_type)){
            parse_str($body, $postvars);
            foreach($postvars as $field => $value) {
                $dados['dados'][$field] = $value;

            }
            $dados['formato'] = "html";
        } else {
        	$dados['dados_entrada'] = $body;
        	$body_params = json_decode($body,true);
            if($body_params) {
                foreach($body_params as $param_name => $param_value) {
                    $dados['dados'][$param_name] = $param_value;
                }
            }
            $dados['formato'] = "json";
        }

		return $dados;
    }

	public function add(){
		$this->layout = false;

		App::import('Component', 'DbbuonnyMonitora');
		$this->DbbuonnyMonitora = new DbbuonnyMonitoraComponent();

		$sucesso = null;
		$erros = array();
		//$this->log(print_r($_SERVER, true), 'rest');
		$dados_sm = $this->carregaDados();
	
		$dados_entrada = isset($dados_sm['dados_entrada']) ? $dados_sm['dados_entrada'] : NULL;
		unset($dados_sm['dados_entrada']);
		if(!isset($dados_sm['dados']) || count($dados_sm['dados']) < 2){
			$erros[] = 'Dados incorretos';
		}

		if(empty($erros)){
			$dados_sm = $dados_sm['dados'];

			if(empty($dados_sm['autenticacao']) || empty($dados_sm['autenticacao']['token'])){
				$erros[] = 'Informe o token do usuário';
			}

			if(empty($dados_sm['cnpj_cliente'])){
				$erros[] = 'Informe o cnpj do cliente';
			}

			if(empty($erros)){

				$usuario = $this->Usuario->autenticarToken($dados_sm['autenticacao']['token'], $dados_sm['cnpj_cliente']);
				if ($usuario) {
					unset($usuario['Usuario']['senha']);
					$_SESSION['Auth']['Usuario'] = $usuario['Usuario'];

					$sistema_origem = 'REST';
					$cliente = $this->Cliente->porCNPJ($dados_sm['cnpj_cliente'], 'first');
					$cliente_codigo = $cliente['Cliente']['codigo'];
					$cliente_monitora = $this->DbbuonnyMonitora->clientesMonitoraPorBaseCnpjETipoClienteBuonny($cliente_codigo);
					$cliente_tipo = current(array_keys($cliente_monitora['clientes_tipos']));

					$gerenciadora = $this->TGrisGerenciadoraRisco->carregarPorCNPJ($dados_sm['cnpj_gerenciadora_de_risco']);

					if(!Comum::validarCNPJ($dados_sm['cnpj_transportador'])){
						$erros[] = 'Informe um cnpj de transportador válido';
					}else{
						$empresa = $this->ClientEmpresa->carregarPorCnpjCpf($dados_sm['cnpj_transportador'],'first',array('Codigo'));
						$codigo_transportador = $empresa['ClientEmpresa']['Codigo'];
						if(!$codigo_transportador){
							$erros[] = 'Transportador não encontrado';
						}else{
							$codigo_cliente_transportador = $this->Cliente->carregarPorDocumento($dados_sm['cnpj_transportador'],array('Cliente.codigo'));
						}
					}

					$codigo_embarcador = NULL;
					if(!empty($dados_sm['cnpj_embarcador'])){
						if(!Comum::validarCNPJ($dados_sm['cnpj_embarcador'])){
							$erros[] = 'O CNPJ do embarcador é inválido';
						}else{
							$empresa = $this->ClientEmpresa->carregarPorCnpjCpf($dados_sm['cnpj_embarcador'],'first',array('Codigo'));
							$codigo_embarcador = $empresa['ClientEmpresa']['Codigo'];
							if(!$codigo_embarcador){
								$erros[] = 'Embarcador não encontrado';
							}else{
								$codigo_cliente_embarcador = $this->Cliente->carregarPorDocumento($dados_sm['cnpj_embarcador'],array('Cliente.codigo'));
							}
						}
					}

					if(!empty($dados_sm['veiculos']['placa'])){
						$placa_cavalo = NULL;
						$placa_carreta = array();
						if(!is_array($dados_sm['veiculos']['placa']))
							$dados_sm['veiculos']['placa'] = array($dados_sm['veiculos']['placa']);

						foreach ($dados_sm['veiculos']['placa'] as $placa){
							$veiculo = $this->TVeicVeiculo->buscaPorPlaca($placa,array('veic_tvei_codigo'));
							if(!empty($veiculo)){
								if($veiculo['TVeicVeiculo']['veic_tvei_codigo'] != TTveiTipoVeiculo::CARRETA){
									if(empty($placa_cavalo)){
										$placa_cavalo = $placa;
									}else{
										$erros[] = 'Informe apenas um Cavalo';
									}
								}else{
									$placa_carreta[] = $placa;
								}
							}else{
								$erros[] = 'Placa '.$placa.' não cadastrada';
							}
						}

						if(empty($placa_cavalo)){
							$erros[] = 'Favor informar a placa de um Cavalo';
						}

						if(empty($erros)){
							$fields = array('Codigo','Placa_Cam','Tipo_Equip','Equip_Serie','Cod_Equip','Chassi','Fabricante','Modelo','Ano_Fab','Cor','TIP_Codigo','TIP_Carroceria');
							$caminhao = $this->MCaminhao->buscaPorPlaca($placa_cavalo, $fields);

							$carreta = array();
							$fields		= array('Codigo','Placa_Carreta','Local_Emplaca','Ano','TIP_Codigo','Cor');
							foreach($placa_carreta as $placa){
								$carreta[] = $this->MCarreta->listarPorPlaca($placa, $fields);
							}
						}
					}else{
						$erros[] = 'Placa não informada';
					}

					if(empty($dados_sm['motorista']['cpf']) || empty($dados_sm['motorista']['nome'])){
						$erros[] = 'Motorista não informado';
					}

					if(!empty($dados_sm['iscas'])){
						$iscas = array();
						foreach($dados_sm['iscas'] as $isca){
							foreach ($isca as  $tecnologia) {
								if(empty($tecnologia['tecnologia'])){
									$erros[] = 'Informe a tecnologia da isca';
								}

								if(empty($tecnologia['numero_terminal'])){
									$erros[] = 'Informe o numero do terminal da isca';
								}

								if(empty($erros)){
									$iscas[] = array(
										'tecn_codigo' => $tecnologia['tecnologia'],
										'term_numero_terminal' => $tecnologia['numero_terminal'],
									);
								}
							}
						}
								
					}
				
					if(!empty($dados_sm['escolta'])){
						$escoltas = array();						
						$empresa_escolta = $dados_sm['escolta']['empresa'];
						foreach ($empresa_escolta as $empresa) {	
							$escolta_cadastrada  = $this->TEescEmpresaEscolta->carregarEscolta( array('codigo_documento' => $empresa['cnpj_empresa'] ) );
				
							if(empty($escolta_cadastrada)){
								$this->TPessPessoa->incluirSeguradoraCorretora(array(
									'pjur_cnpj' 				=> $empresa['cnpj_empresa'],
									'pjur_razao_social'			=> $empresa['nome_empresa']
								),TRUE);
								$pjur = $this->TPjurPessoaJuridica->carregarPorCNPJ($empresa['cnpj_empresa']);
								$data['eesc_oras_pess_pesj_codigo'] = $pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
								$this->TEescEmpresaEscolta->incluir($data);
								$empresa_escolta  = $this->TEescEmpresaEscolta->carregarEscolta( array('codigo_documento' => $empresa['cnpj_empresa'] ) );
							}
							$veiculos_escolta = array();
							$veiculos = $empresa['veiculos']['veiculo'];	
						
							foreach($veiculos as $veiculo){	
								$veiculos_escolta[] = array(
									'nome' => $veiculo['equipe'],
									'telefone' => $veiculo['telefone'],
									'placa' => $veiculo['placa'],
									'TVescViagemEscolta' => array(
										'vesc_vtec_codigo' => $veiculo['tecnologia'],
										'vesc_numero_terminal' => $veiculo['numero_terminal'],
										'vesc_armada' 			=> (isset($veiculo['armada']) && !empty($veiculo['armada']) ? $veiculo['armada'] : 0),
										'vesc_velada' 			=> (isset($veiculo['velada']) && !empty($veiculo['velada']) ? $veiculo['velada'] : 0),
									)
								);
							}
						
							$escoltas[] = array(
								'eesc_codigo_visual'=> $escolta_cadastrada['TPjurPessoaJuridica']['pjur_razao_social'],
								'eesc_codigo' 		=> $escolta_cadastrada['TEescEmpresaEscolta']['eesc_oras_pess_pesj_codigo'],
								'RecebsmEquipes'	=> $veiculos_escolta
							);
						}
									
						
					}

					// Origem
					$RecebsmAlvoOrigem = array();
					$refe_origem = $this->recupera_refe_referencia($dados_sm['origem'], $cliente_codigo, TRUE);
					if($refe_origem && !isset($refe_origem['sucesso'])){
						$RecebsmAlvoOrigem[] = array(
							'refe_codigo_visual' => $refe_origem['TRefeReferencia']['refe_descricao'],
							'refe_codigo' => $refe_origem['TRefeReferencia']['refe_codigo'],
						);
					}elseif(isset($refe_origem['erro'])){
						$erros[] = $refe_origem['erro'];
					}

					//Itinerário
					$RecebsmAlvoDestino = array();
					if(!is_array(reset($dados_sm['itinerario']['alvo'])))
						$dados_sm['itinerario']['alvo'] = array($dados_sm['itinerario']['alvo']);

					foreach ($dados_sm['itinerario']['alvo'] as $alvo) {

						$refe_destino = $this->recupera_refe_referencia($alvo, $cliente_codigo);

						if ($refe_destino && !isset($refe_destino['sucesso'])) {
							if(!$alvo['previsao_de_chegada']){
								$erros[] = 'Previsão de chegada no alvo não informada';
								break;
							}
						}elseif(isset($refe_destino['erro'])){
							$erros[] = $refe_destino['erro'];
							break;
						}
				
						$alvo['previsao_de_chegada'] = str_replace('T', ' ', $alvo['previsao_de_chegada']);
						list($dta_previsao, $hora_previsao) = explode(' ', $alvo['previsao_de_chegada']);
						if(isset($alvo['dados_da_carga']['carga'])){
							if(!is_array(reset($alvo['dados_da_carga']['carga'])))
								$alvo['dados_da_carga']['carga'] = array($alvo['dados_da_carga']['carga']);

							$RecebsmNota = array();
				            foreach($alvo['dados_da_carga']['carga'] as $carga){
				            	if($alvo['tipo_parada'] == TTparTipoParada::ENTREGA && (!isset($carga['tipo_produto']) || empty($carga['tipo_produto']) || !is_numeric($carga['tipo_produto']))){
									$erros[] = 'Alvo: '.$alvo['descricao'].", NF: ".$carga['nf']." - Informe o código do tipo de produto";
									break;
								}
								$carga['peso'] = isset($carga['peso']) ? round($carga['peso']) : NULL;
					            $RecebsmNota[] = array(
					                'notaLoadplan'  => (isset($carga['loadplan_chassi']) ? $carga['loadplan_chassi'] : NULL ),
					                'notaNumero'  => (isset($carga['nf']) ? $carga['nf'] : NULL),
					                'notaSerie'   => (isset($carga['serie_nf']) ? $carga['serie_nf'] : NULL),
					                'carga'     => (isset($carga['tipo_produto']) ? $carga['tipo_produto'] : NULL),
					                'notaValor'   => (isset($carga['valor_total_nf']) ? str_replace('.', ',',$carga['valor_total_nf']) : NULL),
					                'notaVolume'  => (isset($carga['volume']) ? $carga['volume'] : NULL),
					                'notaPeso'    => (isset($carga['peso']) ? $carga['peso'] : NULL),
					            );
				    	    }
					    }
					    $RecebsmAlvoDestino[] = array(
							'refe_codigo' 			=> $refe_destino['TRefeReferencia']['refe_codigo'],
							'refe_codigo_visual' 	=> $refe_destino['TRefeReferencia']['refe_descricao'],
							'dataFinal' 			=> $dta_previsao." ".$hora_previsao,
							'horaFinal' 			=> $hora_previsao,
							'tipo_parada' 			=> $alvo['tipo_parada'],
							'janela_inicio' 		=> (isset($alvo->janela_inicio) ? $dta_previsao.' '.$alvo['janela_inicio'] : $dta_previsao.' 00:00:00'),
							'janela_fim' 			=> (isset($alvo->janela_fim) ? $dta_previsao.' '.$alvo['janela_fim'] : $dta_previsao.' 00:00:00'),
							'RecebsmNota' 			=> $RecebsmNota
						);
					}

					$dados_sm['data_previsao_fim'] = str_replace('T', ' ', $dados_sm['data_previsao_fim']);
					list($dta_inc, $hora_inc)= explode(' ', $dados_sm['data_previsao_inicio']);
					list($dta_fim, $hora_fim)= explode(' ', $dados_sm['data_previsao_fim']);

					if(empty($erros) && $RecebsmAlvoDestino){
						$destinos = array();
						foreach ($RecebsmAlvoDestino as $key => $dest){
							$time = strtotime(AppModel::dateTimeToDbDateTime2($dest['dataFinal'])).$key;
							$destinos[$time] 	= $dest;
						}

						ksort($destinos);
						$RecebsmAlvoDestino = array_values($destinos);

						$alvo_destino = array();
						if($dados_sm['monitorar_retorno']){
							$alvo_destino = $RecebsmAlvoOrigem[0];
							$alvo_destino['tipo_parada'] = TTparTipoParada::ORIGEM;
							list($dataFinal, $horaFinal)= explode(' ', $dados_sm['data_previsao_fim']);
							$alvo_destino['dataFinal'] = $dados_sm['data_previsao_fim'];
							$alvo_destino['horaFinal'] = $horaFinal;
						}else{
							$alvo_destino = end($RecebsmAlvoDestino);
						}

						if($alvo_destino && $alvo_destino['tipo_parada'] != TTparTipoParada::DESTINO){
							$RecebsmAlvoDestino[] = array(
								'refe_codigo'		=> $alvo_destino['refe_codigo'],
								'refe_codigo_visual'=> $alvo_destino['refe_codigo_visual'],
								'dataFinal'			=> $alvo_destino['dataFinal'],
								'horaFinal'			=> $alvo_destino['horaFinal'],
								'tipo_parada'		=> TTparTipoParada::DESTINO,
								'janela_inicio'		=> NULL,
								'janela_fim'		=> NULL,
								'RecebsmNota'		=> array()
							);
						}
					}

					$this->SmIntegracao->cliente_portal = $cliente_codigo;
					$this->SmIntegracao->conteudo = ($dados_entrada ? $dados_entrada : serialize($dados_sm));
					$this->SmIntegracao->name = $sistema_origem;
					$parametros['placa_cavalo'] = isset($caminhao['MCaminhao']['Placa_Cam']) ? $caminhao['MCaminhao']['Placa_Cam'] : NULL;
					$parametros['placa_carreta'] = isset($carreta[0][0]['MCarreta']['Placa_Carreta']) ? $carreta[0][0]['MCarreta']['Placa_Carreta'] : NULL;

					if(empty($erros)){
						$sm = array(
							'codigo_cliente' 		=> $cliente_codigo,
							'cliente_tipo' 			=> $cliente_tipo,
							'placa'					=> $dados_sm['veiculos'],
							'tipo_pgr'				=> isset($dados_sm['tipo_pgr']) ? $dados_sm['tipo_pgr'] : 'G',
							'caminhao' 				=> $caminhao,
							'placa_caminhao'		=> str_replace('-', '', $caminhao['MCaminhao']['Placa_Cam']),
							'carreta' 				=> $carreta,
							'transportador'			=> $codigo_transportador,
							'embarcador' 			=> $codigo_embarcador,
							'codigo_transportador'	=> ($codigo_cliente_transportador ? $codigo_cliente_transportador['Cliente']['codigo'] : NULL),
							'codigo_embarcador'		=> (isset($codigo_cliente_embarcador) ? $codigo_cliente_embarcador['Cliente']['codigo'] : NULL),
							'informacao' 			=> 'Viagem liberada',
							'motorista_cpf' 		=> $dados_sm['motorista']['cpf'],
							'motorista_nome' 		=> html_entity_decode($dados_sm['motorista']['nome']),
							'telefone' 				=> (isset($dados_sm['motorista']['telefone']) ? $dados_sm['motorista']['telefone'] : NULL),
							'radio' 				=> (isset($dados_sm['motorista']['radio']) ? $dados_sm['motorista']['radio'] : NULL),
							'gerenciadora' 			=> $gerenciadora['TGrisGerenciadoraRisco']['gris_pjur_pess_oras_codigo'],
							'liberacao' 			=> isset($dados_sm['numero_liberacao']) ? $dados_sm['numero_liberacao'] : NULL,
							'dta_inc' 				=> $dados_sm['data_previsao_inicio'],
							'hora_inc' 				=> $hora_inc,
							'dta_fim' 				=> $dados_sm['data_previsao_fim'],
							'hora_fim' 				=> $hora_fim,
							'operacao' 				=> $dados_sm['tipo_de_transporte'],
							'temperatura' 			=> (isset($dados_sm['controle_temperatura']) ? $dados_sm['controle_temperatura']['de'] : NULL),
							'temperatura2' 			=> (isset($dados_sm['controle_temperatura']) ? $dados_sm['controle_temperatura']['ate'] : NULL),
							'pedido_cliente' 		=> $dados_sm['pedido_cliente'],
							'monitorar_retorno' 	=> $dados_sm['monitorar_retorno'],
							'sistema_origem' 		=> $sistema_origem,
							'RecebsmAlvoOrigem' 	=> $RecebsmAlvoOrigem,
							'sm_reprogramada'		=> '',
							'RecebsmAlvoDestino' 	=> $RecebsmAlvoDestino,							
							'RecebsmEscolta' 		=> isset($escoltas) ? $escoltas : NULL,
							'RecebsmIsca' 			=> isset($iscas) ? $iscas : NULL,
							'observacao' 			=> (isset($dados_sm['observacao']) ? html_entity_decode($dados_sm['observacao']) : NULL),
							'nome_usuario' 			=> $cliente_codigo,
						);

						$retorno = $this->TViagViagem->incluir_viagem($sm);
						$sucesso = isset($retorno['sucesso']) ? $retorno['sucesso'] : NULL;
						$erro 	 = isset($retorno['erro']) ? $retorno['erro'] : NULL;
						$erro = str_replace('<BR>', '&#10;', $erro);

						if($sucesso){
							$parametros = array_merge($parametros,array(
								'mensagem'		=> $sucesso,
								'status'		=> SmIntegracao::SUCESSO,
								'descricao'		=> $sucesso,
							));

							$this->SmIntegracao->cadastrarLog($parametros);
						} else {
							$erros[] = $erro;
							$erro = implode("\n", $erros);
							if($this->SmIntegracao->cliente_portal){
								$parametros = array_merge($parametros,array(
									'mensagem'		=> $erro,
									'status'		=> SmIntegracao::ERRO,
									'descricao'		=> $erro,
								));

								$this->SmIntegracao->cadastrarLog($parametros);
							}
						}
					}else{
						$erro = implode("\n", $erros);
						if($this->SmIntegracao->cliente_portal){
							$parametros = array_merge($parametros,array(
								'mensagem'		=> $erro,
								'status'		=> SmIntegracao::ERRO,
								'descricao'		=> $erro,
							));

							$this->SmIntegracao->cadastrarLog($parametros);
						}
					}

				}else{
					$erros[] = 'O token informado não confere com o cnpj do cliente';
				}
			}
		}
		//$this->log(json_encode(array('sucesso' => $sucesso, 'erros' => $erros)), 'rest');
		$this->set(compact('sucesso','erros'));
	}

	private function recupera_refe_referencia($dados_sm, $cliente_codigo, $eh_cd = false){
		if(empty($dados_sm['codigo_externo'])){
			return array('sucesso' => false, 'erro' => 'Codigo externo não informado');
		}

		$cliente_pjur 	 = $this->TPjurPessoaJuridica->buscaClienteCentralizador($cliente_codigo);
		$refe_referencia = $this->TRefeReferencia->buscaPorDePara($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],$dados_sm['codigo_externo']);

		$refe_band_codigo = null;
		if(isset($dados_sm['bandeira'])){
			$bandeiras = $this->TBandBandeira->lista($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
			$refe_band_codigo = array_search($dados_sm['bandeira'], $bandeiras);
			if(!$refe_band_codigo){
				if(!$this->TBandBandeira->incluir(array('band_descricao' => $dados_sm['bandeira'],'band_pjur_pess_oras_codigo' => $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'])))
					return array('sucesso' => FALSE, 'erro' => 'Erro ao incluir Bandeira');

				$refe_band_codigo = $this->TBandBandeira->id;


			}
		}

		if (!$refe_referencia) {

			if (empty($dados_sm['latitude']) || empty($dados_sm['longitude'])) {
				$local = array();
				if(!empty($dados_sm['logradouro']))	$local['endereco'] = html_entity_decode($dados_sm['logradouro']);
				if(!empty($dados_sm['bairro'])) 		$local['bairro'] = html_entity_decode($dados_sm['bairro']);
				if(!empty($dados_sm['numero'])) 		$local['numero'] = $dados_sm['numero'];
				if(!empty($dados_sm['cep']))			$local['cep'] = $dados_sm['cep'];
				if(!empty($dados_sm['cidade'])) 		$local['cidade']['nome'] = html_entity_decode($dados_sm['cidade']);
				if(!empty($dados_sm['estado'])) 		$local['cidade']['estado'] = $dados_sm['estado'];

				$xy = $this->TRefeReferencia->maplinkLocaliza($local);

				if(!empty($xy)){
					$dados_sm['latitude'] = $xy->getXYResult->y;
					$dados_sm['longitude'] = $xy->getXYResult->x;
				}
			}

			$cida_cidade = $this->TCidaCidade->buscaPorDescricao(html_entity_decode($dados_sm['cidade']), $dados_sm['estado']);
			if(!$cida_cidade)
				$cida_cidade = $this->TCidaCidade->carregar(TCidaCidade::CIDADE_DEFAULT);

			if ($cliente_pjur) {
				$refe_referencia = array('TRefeReferencia' => array(
						'refe_pess_oras_codigo_local' 	=> $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],
						'refe_utilizado_sistema' 		=> 'N',
						'refe_usuario_adicionou' 		=> 'REST',
						'refe_descricao' 				=> html_entity_decode($dados_sm['descricao']),
						'refe_cnpj_empresa_terceiro' 	=> NULL,
						'refe_cep' 						=> $dados_sm['cep'],
						'refe_endereco_empresa_terceiro'=> html_entity_decode($dados_sm['logradouro']),
						'refe_numero' 					=> $dados_sm['numero'],
						'refe_bairro_empresa_terceiro' 	=> html_entity_decode($dados_sm['bairro']),
						'refe_estado' 					=> $cida_cidade['TCidaCidade']['cida_esta_codigo'],
						'refe_cida_codigo' 				=> $cida_cidade['TCidaCidade']['cida_codigo'],
						'refe_latitude' 				=> $dados_sm['latitude'],
						'refe_longitude' 				=> $dados_sm['longitude'],
						'refe_cref_codigo' 				=> $eh_cd ? TCrefClasseReferencia::CD : TCrefClasseReferencia::CLIENTE,
						'refe_band_codigo' 				=> $refe_band_codigo,
						'refe_regi_codigo' 				=> NULL,
						'refe_depara'					=> $dados_sm['codigo_externo'],
						'refe_critico'					=> 0,
						'refe_permanente'				=> 0,
						'tloc_tloc_codigo' 				=> $eh_cd ? TTlocTipoLocal::ORIGEM : TTlocTipoLocal::ENTREGA,
						'refe_raio' 					=> 150,
					));

				if (!$this->TRefeReferencia->incluirReferencia($refe_referencia['TRefeReferencia']))
					return array('sucesso' => FALSE, 'erro' => 'Erro ao incluir Alvo');

				$refe_referencia['TRefeReferencia']['refe_codigo'] = $this->TRefeReferencia->id;

			}

		}

		$refe_referencia['TRefeReferencia']['cliente'] = $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];

		return $refe_referencia;
	}

	function get_posicao(){
		$this->loadModel('TVlocViagemLocal');
		$this->loadModel('TElocEmbarcadorLocal');
		$this->loadModel('TTlocTransportadorLocal');
		$this->loadModel('TVlevViagemLocalEvento');
		$this->loadModel('TVnfiViagemNotaFiscal');
		$this->loadModel('TVproViagemProduto');
		$this->loadModel('TProdProduto');
		$this->layout = false;
		$cnpj = (isset($this->params['url']['cnpj']) ? $this->params['url']['cnpj'] : NULL);
		$token = (isset($this->params['url']['token']) ? $this->params['url']['token'] : NULL);
		$status_viagem = (isset($this->params['url']['status_viagem']) ? strtoupper($this->params['url']['status_viagem']) : NULL);

		$sucesso = NULL;
		$erros = array();

		if(empty($cnpj)){
			$erros[] = 'Informe o Cnpj';
		}

		if(empty($token)){
			$erros[] = 'Informe o Token';
		}

		if(empty($erros)){
			$usuario = $this->Usuario->autenticarToken($token,$cnpj);
			if($usuario){
				$cliente_pjur = $this->TPjurPessoaJuridica->carregarPorCNPJ($cnpj);
				switch ($status_viagem) {
					case 'V':
						$this->TViagViagem->bindTVeicPrincipal();
						$this->TViagViagem->bindTTermPrincipal();
						$this->TViagViagem->bindModel(array(
							'hasOne' => array(
								'TUposUltimaPosicao' => array(
									'foreignKey' => false,
									'conditions' => array(
										'TTermTerminal.term_vtec_codigo = TUposUltimaPosicao.upos_vtec_codigo',
										'TTermTerminal.term_numero_terminal = TUposUltimaPosicao.upos_term_numero_terminal',
									),
									'type'		 => 'INNER'
								),
							)
						));

						$viagens 	= $this->TViagViagem->listarViagensEmAndamentoPorCliente($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
						foreach ($viagens as $viagem) {
							$alvos = $this->TVlocViagemLocal->find('all',array(
								'conditions'=> array(
									'vloc_viag_codigo' => $viagem['TViagViagem']['viag_codigo'],
								),
								'joins' => array(
									array(
										'table' => "{$this->TRefeReferencia->databaseTable}.{$this->TRefeReferencia->tableSchema}.{$this->TRefeReferencia->useTable}",
										'alias' => 'TRefeReferencia',
										'type' => 'INNER',
										'conditions' => 'TVlocViagemLocal.vloc_refe_codigo = TRefeReferencia.refe_codigo',
									),
									array(
										'table' => "{$this->TVlevViagemLocalEvento->databaseTable}.{$this->TVlevViagemLocalEvento->tableSchema}.{$this->TVlevViagemLocalEvento->useTable}",
										'alias' => 'TVlevViagemLocalEventoEntrada',
										'type' => 'INNER',
										'conditions' => 'TVlocViagemLocal.vloc_codigo = TVlevViagemLocalEventoEntrada.vlev_vloc_codigo AND TVlevViagemLocalEventoEntrada.vlev_tlev_codigo = 1',
									),
									array(
										'table' => "{$this->TVlevViagemLocalEvento->databaseTable}.{$this->TVlevViagemLocalEvento->tableSchema}.{$this->TVlevViagemLocalEvento->useTable}",
										'alias' => 'TVlevViagemLocalEventoSaida',
										'type' => 'INNER',
										'conditions' => 'TVlocViagemLocal.vloc_codigo = TVlevViagemLocalEventoSaida.vlev_vloc_codigo AND TVlevViagemLocalEventoSaida.vlev_tlev_codigo = 8',
									),
									array(
										'table' => "{$this->TTparTipoParada->databaseTable}.{$this->TTparTipoParada->tableSchema}.{$this->TTparTipoParada->useTable}",
										'alias' => 'TTparTipoParada',
										'type' => 'INNER',
										'conditions' => 'TTparTipoParada.tpar_codigo = TVlocViagemLocal.vloc_tpar_codigo',
									),
									array(
										'table' => "{$this->TCidaCidade->databaseTable}.{$this->TCidaCidade->tableSchema}.{$this->TCidaCidade->useTable}",
										'alias' => 'TCidaCidade',
										'type' => 'INNER',
										'conditions' => 'TCidaCidade.cida_codigo = TRefeReferencia.refe_cida_codigo',
									),
									array(
										'table' => "{$this->TEstaEstado->databaseTable}.{$this->TEstaEstado->tableSchema}.{$this->TEstaEstado->useTable}",
										'alias' => 'TEstaEstado',
										'type' => 'INNER',
										'conditions' => 'TEstaEstado.esta_codigo = TCidaCidade.cida_esta_codigo',
									),
									array(
										'table' => "{$this->TElocEmbarcadorLocal->databaseTable}.{$this->TElocEmbarcadorLocal->tableSchema}.{$this->TElocEmbarcadorLocal->useTable}",
										'alias' => 'TElocEmbarcadorLocal',
										'type' => 'LEFT',
										'conditions' => 'TRefeReferencia.refe_codigo = TElocEmbarcadorLocal.eloc_refe_codigo',
									),
									array(
										'table' => "{$this->TTlocTransportadorLocal->databaseTable}.{$this->TTlocTransportadorLocal->tableSchema}.{$this->TTlocTransportadorLocal->useTable}",
										'alias' => 'TTlocTransportadorLocal',
										'type' => 'LEFT',
										'conditions' => 'TRefeReferencia.refe_codigo = TTlocTransportadorLocal.tloc_refe_codigo',
									),
								),
								'fields' => array(
									'TVlocViagemLocal.vloc_codigo',
									'TTlocTransportadorLocal.tloc_refe_depara',
									'TElocEmbarcadorLocal.eloc_refe_depara',
									'TEstaEstado.esta_sigla',
									'TCidaCidade.cida_descricao',
									'TTparTipoParada.tpar_descricao',
									'TRefeReferencia.refe_descricao',
									'TRefeReferencia.refe_latitude',
									'TRefeReferencia.refe_longitude',
									'TRefeReferencia.refe_endereco_empresa_terceiro',
									'TRefeReferencia.refe_bairro_empresa_terceiro',
									'TRefeReferencia.refe_numero',
									'TRefeReferencia.refe_cep',
									'TVlevViagemLocalEventoEntrada.vlev_data',
									'TVlevViagemLocalEventoSaida.vlev_data',
									'TVlevViagemLocalEventoEntrada.vlev_data_previsao',
								)
							));
							foreach ($alvos as $key => $alvo) {
								$notas = $this->TVnfiViagemNotaFiscal->find('all',array(
									'conditions'=> array(
										'vnfi_vloc_codigo' => $alvo['TVlocViagemLocal']['vloc_codigo'],
									),


									'joins' => array(
										array(
											'table' => "{$this->TVproViagemProduto->databaseTable}.{$this->TVproViagemProduto->tableSchema}.{$this->TVproViagemProduto->useTable}",
											'alias' => 'TVproViagemProduto',
											'type' => 'INNER',
											'conditions' => 'TVproViagemProduto.vpro_vnfi_codigo = TVnfiViagemNotaFiscal.vnfi_codigo',
										),
										array(
											'table' => "{$this->TProdProduto->databaseTable}.{$this->TProdProduto->tableSchema}.{$this->TProdProduto->useTable}",
											'alias' => 'TProdProduto',
											'type' => 'INNER',
											'conditions' => 'TProdProduto.prod_codigo = TVproViagemProduto.vpro_prod_codigo',
										),
									),
									'fields' => array(
										'TVnfiViagemNotaFiscal.vnfi_codigo',
										'TVnfiViagemNotaFiscal.vnfi_pedido',
										'TVnfiViagemNotaFiscal.vnfi_numero',
										'TVnfiViagemNotaFiscal.vnfi_serie',
										'TVnfiViagemNotaFiscal.vnfi_valor',
										'TVnfiViagemNotaFiscal.vnfi_volume',
										'TVnfiViagemNotaFiscal.vnfi_peso',
										'TProdProduto.prod_descricao',

									),
								));
								$alvos[$key]['notas'] = $notas;
							};
							$viagem_retorno = array(
								'sm' => $viagem['TViagViagem']['viag_codigo_sm'],
								'placa' => $viagem['TVeicVeiculo']['veic_placa'],
								'latitude' => $viagem['TUposUltimaPosicao']['upos_latitude'],
								'longitude' => $viagem['TUposUltimaPosicao']['upos_longitude'],
								'descricao' => $viagem['TUposUltimaPosicao']['upos_descricao_sistema'],
								'data_hora' => $viagem['TUposUltimaPosicao']['upos_data_comp_bordo'],
								'alvos' => array()
							);

							foreach ($alvos as $key => $alvo) {
								$viagem_retorno['alvos'][$key] = array(
									'descricao' => $alvo['TRefeReferencia']['refe_descricao'],
									'entrada_alvo' => $alvo['TVlevViagemLocalEventoEntrada']['vlev_data'],
									'saida_alvo' => $alvo['TVlevViagemLocalEventoSaida']['vlev_data'],
									'endereco' => $alvo['TRefeReferencia']['refe_endereco_empresa_terceiro'],
									'cep' => $alvo['TRefeReferencia']['refe_cep'],
									'bairro' => $alvo['TRefeReferencia']['refe_bairro_empresa_terceiro'],
									'cidade' => $alvo['TCidaCidade']['cida_descricao'],
									'estado' => $alvo['TEstaEstado']['esta_sigla'],
									'latitude' => $alvo['TRefeReferencia']['refe_latitude'],
									'longitude' => $alvo['TRefeReferencia']['refe_longitude'],
									'data_previsao' => $alvo['TVlevViagemLocalEventoEntrada']['vlev_data_previsao'],
									'codigo_externo' => (!empty($alvo['TElocEmbarcadorLocal']['eloc_refe_depara']) ? $alvo['TElocEmbarcadorLocal']['eloc_refe_depara'] : $alvo['TTlocTransportadorLocal']['tloc_refe_depara']),
									'tipo_parada' => $alvo['TTparTipoParada']['tpar_descricao'],
									'notas' => array(),
								);
								foreach ($alvo['notas'] as $key2 => $nota) {
									$viagem_retorno['alvos'][$key]['notas'][$key2] = array(
										'pedido' => $nota['TVnfiViagemNotaFiscal']['vnfi_pedido'],
										'numero' => $nota['TVnfiViagemNotaFiscal']['vnfi_numero'],
										'serie' => $nota['TVnfiViagemNotaFiscal']['vnfi_serie'],
										'valor' => $nota['TVnfiViagemNotaFiscal']['vnfi_valor'],
										'volume' => $nota['TVnfiViagemNotaFiscal']['vnfi_volume'],
										'peso' => $nota['TVnfiViagemNotaFiscal']['vnfi_peso'],
										'produto' => $nota['TProdProduto']['prod_descricao'],
									);

								}
							}
							$sucesso[] = $viagem_retorno;
						}
						break;
					default:

						break;
				}
			}else{
				$erros[] = 'O token informado não confere com o cnpj do cliente';
			}
		}
		$this->set(compact('sucesso','erros'));
	}

	public function nao_finalizadas() {
		$this->layout = false;
    	if (!empty($this->params['url']['cpf'])) {
	    	$this->TViagViagem->bindModel(array('hasOne' => array(
	    		'TVveiViagemVeiculo' => array('foreignKey' => 'vvei_viag_codigo', 'conditions' => array('vvei_precedencia' => '1')),
	    		'TPfisPessoaFisica' => array('foreignKey' => false, 'conditions' => 'pfis_pess_oras_codigo = vvei_moto_pfis_pess_oras_codigo'),
	    	)));
    		$cpf = $this->params['url']['cpf'];
	    	$conditions = array(
	    		'pfis_cpf' => $cpf,
	    		'viag_data_fim' => null,
	    	);
	    	$fields = array(
	    		'viag_codigo_sm',
	    		'viag_data_cadastro',
	    		'viag_previsao_inicio',
	    		'viag_data_inicio',
	    	);
	    	$viagens = $this->TViagViagem->find('all', compact('conditions', 'fields'));
	    	if ($viagens) {
	    		$viagens = $this->parseViagens($viagens);
	    	}
    	}
	    $this->set(compact('viagens'));
	}

	private function parseViagens($data) {
		$viagens = array();
		foreach ($data as $key => $viagem) {
			$viagens[$key]['sm'] = $viagem['TViagViagem']['viag_codigo_sm'];
			$viagens[$key]['data_cadastro'] = AppModel::dateToDbDate($viagem['TViagViagem']['viag_data_cadastro']);
			$viagens[$key]['previsao_inicio'] = AppModel::dateToDbDate($viagem['TViagViagem']['viag_previsao_inicio']);
			$viagens[$key]['data_inicio'] = AppModel::dateToDbDate($viagem['TViagViagem']['viag_data_inicio']);
			$viagens[$key]['origem'] = '';
			$viagens[$key]['destino'] = '';
			$viagens[$key]['status'] = 1;
		}
		return $viagens;
	}

}
?>