<?php
class ModelosViagensController extends AppController {
	var $name 			= 'ModelosViagens';
	public $components 	= array('DbbuonnyGuardian');
	var $uses 			= array(
		'TMviaModeloViagem',
		'TMvloModeloViagemLocal',
		'TMvveModeloViagemVeiculo',
		'Cliente'

	);
	
 	function adicionar(){
 		
 		$retorno['sucesso'] = FALSE;

 		if($this->RequestHandler->isPost()){
 			$this->data['Recebsm']['usuario'] 	=& $this->authUsuario['Usuario']['apelido'];
			$retorno['sucesso'] = $this->TMviaModeloViagem->incluirModelo($this->data);
			
			if(isset($this->TMviaModeloViagem->validationErrors['mvia_descricao']))
				$retorno['msg'] = $this->TMviaModeloViagem->validationErrors['mvia_descricao'];

			echo json_encode($retorno);
 		}
 		exit;
 	}

 	function listar_modelos($codigo_cliente){
 		$this->loadModel('Cliente');
 		$this->loadModel('TPjurPessoaJuridica');

 		try{
	 		$cliente 		= $this->Cliente->carregar($codigo_cliente);
	 		$cliente_pjur	= $this->TPjurPessoaJuridica->carregarPorCNPJ($cliente['Cliente']['codigo_documento']);

	 		if(!$cliente_pjur)
	 			throw new Exception("Cliente não localizado");

	 		$modelos = $this->TMviaModeloViagem->listarPorCliente($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);

	 		if(!$modelos)
	 			throw new Exception("Nenhum modelo localizado");

 		} catch( Exception $ex ) {
 			$modelos = array();
 		}
 		
 		$this->set(compact('modelos'));
 	}

 	function carregar($codigo_modelo, $remonta = false,$codigo_cliente = null){
 		$this->loadModel('TTtraTipoTransporte');
 		$this->loadModel('TTparTipoParada');
 		$this->loadModel('TProdProduto');
 		$this->loadModel('Cliente');
 		$this->loadModel('TPjprPjurProd');
		$this->loadModel('TRacsRegraAceiteSm');
		$this->loadModel('TViagViagem');
 		$this->data 	=& $this->TMviaModeloViagem->converterCamposBdParaFormulario($codigo_modelo);
 		$data 			= $this->Session->read('RecebsmNew');
 		$this->data['Recebsm'] = array_merge($data['Recebsm'],$this->data['Recebsm']);
 		$tipo_transporte= $this->TTtraTipoTransporte->listarParaFormulario();
		$tipo_parada 	= $this->TTparTipoParada->listarParaFormulario();	
		if( $codigo_cliente ){
			App::Import('Component',array('DbbuonnyGuardian'));
			$tipo_carga = $this->TRacsRegraAceiteSm->produtoPorCliente(DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($codigo_cliente));
		}		
		if(empty($tipo_carga))
			$tipo_carga = $this->TProdProduto->listar();

		$this->incluir_rotas_combo(
			$this->Cliente->carregar($this->data['Recebsm']['embarcador']),
			$this->Cliente->carregar($this->data['Recebsm']['transportador']));

		$nao_permitir_gerar_rota_vpp_rota_sm = $this->TViagViagem->retornaConfiguracaoDeBloqueiRota($this->data); 
		$this->set(compact('nao_permitir_gerar_rota_vpp_rota_sm'));

 		$this->set(compact(
 			'dados',
 			'tipo_carga',
 			'tipo_parada',
 			'tipo_transporte',
 			'remonta'));
 	}

 	function incluir_rotas_combo($embarcador, $transportador){
		$this->loadModel('TRotaRota');
		$this->loadModel('TPjurPessoaJuridica');

		$embarcador = $embarcador?$this->TPjurPessoaJuridica->carregarPorCNPJ($embarcador['Cliente']['codigo_documento']):NULL;
		$transportador = $transportador?$this->TPjurPessoaJuridica->carregarPorCNPJ($transportador['Cliente']['codigo_documento']):NULL;

		$pess_codigo = array();

		if($embarcador) $pess_codigo[] = $embarcador['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
		if($transportador) $pess_codigo[] = $transportador['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];

		$lista_rotas = $this->TRotaRota->listarPorCliente($pess_codigo);
		$rotas = array();
		foreach ($lista_rotas as $rota) {
			$rotas[$rota['TRotaRota']['rota_codigo']] = $rota['TRotaRota']['rota_descricao'];
		}

		$this->set(compact('rotas'));

	}

 	function carregar_pre_cadastro($codigo_modelo,$codigo_cliente, $remonta){
 		$this->data 	=& $this->TMviaModeloViagem->converterCamposBdParaPreConsulta($codigo_modelo);
 		$this->data['Recebsm']['codigo_cliente'] = $codigo_cliente;
 		
 		$this->set('remonta', !empty($remonta));
 		$this->carregar_pre_cadastro_combos();
 		$this->carregar_pre_cadastro_motorista();
 	}

 	function carregar_pre_cadastro_motorista(){
 		$this->loadModel('Profissional');
 		$this->loadModel('Recebsm');

		$motorista	= $this->Profissional->buscaContatoMotoristaPorCPF($this->data['Recebsm']['codigo_documento']);
		
		if($motorista){
			$this->data['Recebsm'] = array_merge($this->data['Recebsm'],$motorista);
		} else {
			$this->Recebsm->invalidate('codigo_documento','Motorista não localizado');
		}
 	}

 	function carregar_pre_cadastro_combos() {
		$this->loadModel('MClienteGerenciadora');
		$this->loadModel('TTveiTipoVeiculo');
		
		$transportador_read = false;
		$embarcador_read 	= false;

		$gerenciadoras = array();
		$embarcadores = array();
		$transportadores = array();
		$tipos_veiculos = $this->TTveiTipoVeiculo->find('list', array('fields'=>array('tvei_descricao', 'tvei_descricao')));
		
		if (isset($this->data['Recebsm']['codigo_cliente'])) {
			$codigo_cliente = $this->data['Recebsm']['codigo_cliente'];
			
			$cliente = $this->Cliente->carregar($codigo_cliente);
			if($this->Cliente->retornarClienteSubTipo($codigo_cliente) == Cliente::SUBTIPO_TRANSPORTADOR){
				$embarcadores		= $this->Cliente->listaEmbTrans($codigo_cliente,true);
				$transportadores 	= array($cliente['Cliente']['codigo'] => $cliente['Cliente']['razao_social']);
				$transportador_read = true;
			} else {
				$transportadores 	= $this->Cliente->listaEmbTrans($codigo_cliente,true);
				$embarcadores 		= array($cliente['Cliente']['codigo'] => $cliente['Cliente']['razao_social']);
				$embarcador_read = true;
			}

		}

		$this->set(compact(
			'transportadores', 
			'embarcadores', 
			'transportador_read', 
			'embarcador_read',
			'tipos_veiculos'));
	}

 	function excluir($codigo_modelo){
		echo json_encode($this->TMviaModeloViagem->excluirModelo($codigo_modelo));
		exit;
	}

	function visualizar($mvia_codigo){
		$this->loadModel('Profissional');
		
		$this->layout = 'ajax';
		$this->TMviaModeloViagem->bindTMvloModeloViagemLocal();
		$this->TMviaModeloViagem->bindTMvveModeloViagemVeiculo();

		$this->TMvloModeloViagemLocal->bindTRefeReferencia();
		$this->TMvloModeloViagemLocal->bindTTparTipoParada();

		$mvlo 		=& $this->TMvloModeloViagemLocal->listarPorViagem($mvia_codigo);

		$this->TMvveModeloViagemVeiculo->bindTTveiTipoVeiculo();
		$mvve 		=& $this->TMvveModeloViagemVeiculo->listarPorViagem($mvia_codigo);

		$mvia		=& $this->TMviaModeloViagem->carregar($mvia_codigo);
		if($mvve)
			$mvia['TMvveModeloViagemVeiculos'] =& $mvve;

		if($mvlo)
			$mvia['TMvloModeloViagemLocais'] =& $mvlo;

		$motorista	= $this->Profissional->buscaContatoMotoristaPorCPF($mvia['TMviaModeloViagem']['mvia_cpf_motorista']);
		if($motorista){
			$mvia['TMviaModeloViagem'] = array_merge($mvia['TMviaModeloViagem'],$motorista);
		}

		$dados_embarcador 		= $this->Cliente->carregar($mvia['TMviaModeloViagem']['mvia_embarcador']);
		$embarcador 			= $dados_embarcador['Cliente']['razao_social'];
		$dados_transportadora	= $this->Cliente->carregar($mvia['TMviaModeloViagem']['mvia_transportador']);
		$transportadora 		= $dados_transportadora['Cliente']['razao_social'];
		
		
		$this->data 			=& $mvia;

		$this->set(compact('dados','embarcador','transportadora'));
	} 

}

?>