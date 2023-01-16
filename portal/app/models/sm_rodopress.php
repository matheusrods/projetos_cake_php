<?php

class SmRodopress extends AppModel {

	var $name 			= 'SmRodopress';
	var $useTable 		= false;
	var $cliente_portal   = 773;
	var $cliente_monitora = '000200';
	var $cliente_guardian = 118919;

	public function convertArquivoCsv($arquivo){
		$conteudo 	= utf8_encode(file_get_contents($arquivo));

		if(stristr($conteudo,"\n")){
			$lista 		= explode("\n",$conteudo);
		} else if(stristr($conteudo,"\r")) {
			$lista 	= explode("\r",$conteudo);
		}

		array_pop($lista);		

		$itinerario = array();
		$cabecalho  = array();

		$nota 	= 0;
		$linha 	= null;

		foreach ($lista as $key => $row) {

			if(substr($row,0,10) == '"Romaneio"' && $linha){
				$this->criarDados(explode(';', $linha),$cabecalho,$itinerario);
				$linha = null;
			}

			$linha .= $row;
		}

		if($linha) $this->criarDados(explode(';', $linha),$cabecalho,$itinerario);

		return compact('cabecalho','itinerario');
	}

	function criarDados($dados,&$cabecalho,&$itinerario){
		if(!$cabecalho){
			$cabecalho['caminhao']['placa'] = str_replace(array(' ','"'),'',$dados[7]);
			$cabecalho['motorista']['nome'] = str_replace(array('"'),'',$dados[11]);
			$cabecalho['motorista']['cpf'] 	= str_replace(array('"'),'',$dados[13]);
			$cabecalho['pedido_cliente'] 	= str_replace(array('"'),'',$dados[5]);
			$cabecalho['data_inicio'] 		= str_replace(array('"'),'',$dados[2]).' '.str_replace(array('"'),'',$dados[3]);
		}

		$nota 							   = count($itinerario);
		$numero_nf 						   = explode('#',str_replace(array("\\",'/','-'),'#/',str_replace(array('"'),'',$dados[33])));
		$itinerario[$nota]['numero_nf']    = $numero_nf;
		$itinerario[$nota]['volume_nf']    = (int)str_replace(array('"'),'',$dados[40]);
		$itinerario[$nota]['peso_nf']      = (int)str_replace(array('"'),'',$dados[41]);
		$itinerario[$nota]['valor_nf'] 	   = str_replace(',','.',str_replace('.','',str_replace(array('"'),'',$dados[43])));
		$itinerario[$nota]['razao']    	   = str_replace(array('"'),'',$dados[35]);
		$itinerario[$nota]['endereco']     = str_replace(array('"'),'',$dados[36]);
		$itinerario[$nota]['cidade']       = str_replace(array('"'),'',$dados[37]);
		$itinerario[$nota]['estado']       = str_replace(array('"'),'',$dados[38]);
		$itinerario[$nota]['bairro']       = str_replace(array('"'),'',$dados[39]);
		$itinerario[$nota]['cep']          = NULL;
		$itinerario[$nota]['dataFinal']    = str_replace(array('"'),'',$dados[2]).' 23:59:59';
		
	}

	public function convertArquivo($arquivo){
		$conteudo 	= explode("\n",utf8_encode(file_get_contents($arquivo)));
		$itinerario = array();
		$cabecalho  = array();

		if(trim($conteudo[2]) != 'Romaneio')
			return FALSE;

		$cabecalho['caminhao']['placa'] = str_replace(' ','',substr($conteudo[6],17,8));
		$cabecalho['motorista']['nome'] = substr($conteudo[8],16,50);
		$cabecalho['motorista']['cpf'] 	= substr($conteudo[8],90,11);
		$cabecalho['pedido_cliente'] 	= trim(substr($conteudo[3],118,10));
		$data   						= explode('#',str_replace('  ','#',trim(substr($conteudo[1],116,18))));
		$data[0] 						= date('d/m/Y',strToTime(str_replace('/', '-', $data[0])));
		$cabecalho['data_inicio'] 		= $data[0].' '.$data[1];
		
		$inicio = FALSE;
		$nota = 0;
		for ($index = 13; isset($conteudo[$index]); $index++) {
			$row = $conteudo[$index];

			if(substr($row,1,5) == 'Total')break;
			
			if($inicio){
				if(trim($row)){
					if(trim(substr($row,1,4)) !== ''){
						$nota++;
						$itinerario[$nota]['numero_nf'][]  = trim(substr($row,5,7));
						$itinerario[$nota]['volume_nf']    = trim(substr($row,114,3));
						$itinerario[$nota]['peso_nf']      = (int)trim(substr($row,117,6));
						$itinerario[$nota]['valor_nf'] 	   = str_replace(',','.',str_replace('.','',trim(substr($row,130,9))));
						$itinerario[$nota]['razao']    	   = trim(substr($row,24,12));
						$itinerario[$nota]['endereco']     = trim(substr($row,36,47));
						$itinerario[$nota]['cidade']       = trim(substr($row,83,11));
						$itinerario[$nota]['estado']       = trim(substr($row,94,2));
						$itinerario[$nota]['bairro']       = trim(substr($row,96,18));
						$itinerario[$nota]['cep']          = NULL;
						$itinerario[$nota]['dataFinal']    = $data[0].' 23:59:59';


					} else {
						$itinerario[$nota]['numero_nf'][] = trim(substr($row,5,7));
					}
				}
			}

			if(substr($row,1,3) == 'Doc')$inicio = TRUE;
		}

		return compact('cabecalho','itinerario');
	}

	public function validacaoDeInclusao(&$dados){
		$this->TRefeReferencia =& ClassRegistry::init('TRefeReferencia');
		$this->MCaminhao =& ClassRegistry::init('MCaminhao');
		$this->TPjurPessoaJuridica =& ClassRegistry::init('TPjurPessoaJuridica');

		try{
			$caminhao = $this->MCaminhao->buscaPorPlaca($dados['cabecalho']['caminhao']['placa'], array('Placa_Cam','Codigo','Cod_Equip','Tipo_Equip'));
			if(!$caminhao)
				throw new Exception("Veículo Placa {$dados['cabecalho']['caminhao']['placa']} não encontrado.");

			$dados['cabecalho']['caminhao'][0] = $caminhao;

			$cliente_pjur =& $this->TPjurPessoaJuridica->carregar($this->cliente_guardian);

			$itinerarios['SM_ITINERARIO'] =& $dados['itinerario'];
			
			if(!$this->TRefeReferencia->verificaAlvosDescricao($itinerarios,$cliente_pjur))
				throw new Exception($this->TRefeReferencia->validationErrors['refe_codigo']);

		}catch(Exception $e){
			$this->invalidate('erro',$e->getMessage());
			return false;
		}
		return true;
	}

	public function incluirViagem($dados,$origem_refe_codigo){
		$this->TViagViagem =& ClassRegistry::init("TViagViagem");

		APP::Import('Model','TGrisGerenciadoraRisco');
		APP::Import('Model','TTtraTipoTransporte');

		if($this->useDbConfig != 'test_suite')
			$authUsuario 	=& $_SESSION['Auth'];
		else
			$authUsuario 	= array('Usuario' => array('apelido' => 'usuario.teste'));

		$itinerario = $this->montaItinerario($dados['itinerario']);

		$viagem 	= array(
			'codigo_cliente' 	=> $this->cliente_portal, 
			'nome_usuario'		=> $authUsuario['Usuario']['apelido'],
			'cliente_tipo'		=> $this->cliente_monitora,
			'transportador'		=> $this->cliente_monitora,
			'embarcador'		=> NULL,
			'caminhao'			=> $dados['cabecalho']['caminhao'][0],
			'carreta'			=> array(),
			'RecebsmAlvoOrigem' => array(
				array(
					'refe_codigo'	=> $origem_refe_codigo,
				)
			),
			'RecebsmAlvoDestino'=> $itinerario,
			'sistema_origem' 	=> 'UPLOAD RODOPRESS',
			'pedido_cliente'	=> $dados['cabecalho']['pedido_cliente'],
			'dta_inc' 			=> $dados['cabecalho']['data_inicio'],
			'operacao' 			=> TTtraTipoTransporte::DISTRIBUICAO,
			'motorista_cpf'		=> $dados['cabecalho']['motorista']['cpf'],
			'motorista_nome'	=> $dados['cabecalho']['motorista']['nome'],
			'RecebsmEquipamento'=> array(),
			'RecebsmEscolta' 	=> array(),
			'gerenciadora' 		=> TGrisGerenciadoraRisco::BUONNY, 
			'temperatura'		=> NULL,
			'temperatura2'		=> NULL,
			'liberacao' 		=> NULL,
			'observacao'		=> NULL,
			'informacao'		=> NULL,
			'telefone'			=> NULL,
			'radio'				=> NULL,
		);
	
		$resultado = $this->TViagViagem->incluir_viagem($viagem,FALSE);

		if(isset($resultado['erro'])){
			$this->invalidate('erro',$resultado['erro']);
			return FALSE;
		} 

		$this->id = $resultado['sucesso'];
		return TRUE;
	}

	public function montaItinerario($itinerario){
		APP::Import('Model','TTparTipoParada');
		APP::Import('Model','TProdProduto');

		$retorno = array();
		foreach ($itinerario as $key => $entrega) {
			if(count($entrega['numero_nf']) > 1){
				$entrega['numero_nf'] = $this->ajustaNfNumero($entrega['numero_nf']);

				$retorno[$key] = array(
					'refe_codigo' => $entrega['refe_codigo'],
					'dataFinal'   => $entrega['dataFinal'],
					'janela_inicio'=> NULL,
					'janela_fim'  => NULL,
					'RecebsmNota' => array()
				);
				
				foreach ($entrega['numero_nf'] as $numero_nf) {
					$valor_nf = $entrega['valor_nf']/count($entrega['numero_nf']);

					$retorno[$key]['RecebsmNota'][] = array( 
						'notaNumero' 	=> $numero_nf,
						'notaVolume' 	=> $entrega['volume_nf'], 
						'notaPeso' 		=> $entrega['peso_nf'], 
						'notaSerie' 	=> NULL, 
						'notaValor' 	=> number_format($valor_nf,2,',','.'), 
						'notaLoadplan' 	=> NULL, 
						'carga'			=> TProdProduto::DIVERSOS
					);

					$entrega['volume_nf'] = 0;
					$entrega['peso_nf']   = 0;
				}
			} else {
				$retorno[$key] = array(
					'refe_codigo' => $entrega['refe_codigo'],
					'dataFinal'   => $entrega['dataFinal'],
					'janela_inicio'=> NULL,
					'janela_fim'  => NULL,
					'RecebsmNota' => array(
						array( 
							'notaNumero' 	=> $entrega['numero_nf'][0],
							'notaVolume' 	=> $entrega['volume_nf'], 
							'notaPeso' 		=> $entrega['peso_nf'], 
							'notaSerie' 	=> NULL, 
							'notaValor' 	=> number_format($entrega['valor_nf'],2,',','.'), 
							'notaLoadplan' 	=> NULL, 
							'carga'			=> TProdProduto::DIVERSOS,
						),
					),
				);
			}
		}

		$ultimo 				= end($retorno);
		$ultimo['RecebsmNota'] 	= array();
		$retorno[] 				= $ultimo;

		return $retorno;
	}

	function ajustaNfNumero($notas_fiscais){
		$notas_fiscais = implode('', $notas_fiscais);
		return explode('#',str_replace(array('\\','/'), '#', $notas_fiscais));
	}

}