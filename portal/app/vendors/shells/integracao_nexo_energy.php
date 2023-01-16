<?php 

class IntegracaoNexoEnergyShell extends Shell {

	private $NexoClient;
	private $ApiAutorizacao;

	public $name = '';
	public $uses = array();
	public $components = array('RequestHandler');
	public $codigos_clientes = array('79928','79930','79667','79934','79933','79931','71758','79926');
	public $codigos_usuarios_not = array('70847','70848','70849','70850','70851','70852','70853','70854','70855','70856','70857');

	public function initialize() {
		echo "cake\console\cake IntegracaoNexoEnergy \n";		
		
		App::import('Component', 'ApiAutorizacao');
		$this->ApiAutorizacao = new ApiAutorizacaoComponent();

		App::import('Component', 'NexoClient');
		$this->NexoClient = new NexoClientComponent();
		$this->NexoClient->soapInit('energy');
	}

	public function main()
	{
		$this->enviosPedidosExames();
	}

	public function enviaPedidoUnitario()
	{
		$param = $this->args;

		if(!isset($param[0])) {
			echo "Favor informar o codigo do pedido.\n";
			exit;
		}

		$codigo_pedido = $param[0];

		$this->autoRender = false;
		$PedidoExame = ClassRegistry::init('PedidoExame');
		$GrupoEconomico = ClassRegistry::init('GrupoEconomico');

		$horas = 2;
		// Vai chamar a query principal passando o argumento $horas
		// que irá retornar um array de codigos de pedidos que foram dados baixa		
		//empresa teste 10011
		// $codigos_clientes = array('10011');
		
		//codigos dos grupos economicos
		$grupo_economico = $GrupoEconomico->find('list', array('fields' => array('GrupoEconomico.codigo'),'conditions' => array('GrupoEconomico.codigo_cliente' => $this->codigos_clientes)));

		//codigos grupos economicos
		$codigos_grupos_economicos = implode(',',$grupo_economico);
		$codigos_clientes = implode(',', $this->codigos_clientes);
		
		//codigo do grupo economico da simens
		$pedidos = $PedidoExame->pedidos_exportacao_nexo($horas, $codigos_grupos_economicos, $codigo_pedido); // Segundo param teste(10011) ou siemens()
		
		$clienteID = array();
		$medicoID = array();

		if(!empty($pedidos)) {
			
			$this->enviaPedidos($pedidos);
			
		}
		else {
			echo "Não foram encontrados pedidos.\n";
		}//fim pedidos
		
		echo json_encode(
			array(
				"Msg" => "Processo de atualização finalizado.",
				"Pedidos" => $pedidos
			)
		)."\n";

	}//fim enviaPedidoUnitario


	public function enviosPedidosExames () {
		
		$this->autoRender = false;
		$PedidoExame = ClassRegistry::init('PedidoExame');
		$GrupoEconomico = ClassRegistry::init('GrupoEconomico');

		$horas = 2;
		
		// Vai chamar a query principal passando o argumento $horas
		// que irá retornar um array de codigos de pedidos que foram dados baixa
		
		//empresa teste 10011
		// $codigos_clientes = array('10011');
		
		//codigos dos grupos economicos
		$grupo_economico = $GrupoEconomico->find('list', array('fields' => array('GrupoEconomico.codigo'),'conditions' => array('GrupoEconomico.codigo_cliente' => $this->codigos_clientes)));

		//codigos grupos economicos
		$codigos_grupos_economicos = implode(',',$grupo_economico);
		
		//codigo do grupo economico da simens
		$pedidos = $PedidoExame->pedidos_exportacao_nexo($horas, $codigos_grupos_economicos); // Segundo param teste(10011) ou siemens()
		
		$clienteID = array();
		$medicoID = array();

		if(!empty($pedidos)) {
			$this->enviaPedidos($pedidos);
		}
		else {
			echo "Não foram encontrados pedidos.\n";
		}//fim pedidos
		
		echo json_encode(
			array(
				"Msg" => "Processo de atualização finalizado.",
				"Pedidos" => $pedidos
			)
		)."\n";

	}

	/**
	 * [enviaPedidos metodo para enviar os pedidos achados]
	 * @param  [type] $pedidos [description]
	 * @return [type]          [description]
	 */
	private function enviaPedidos($pedidos)
	{
		$PedidoExame = ClassRegistry::init('PedidoExame');
		$GrupoEconomico = ClassRegistry::init('GrupoEconomico');
		
		$codigos_clientes = implode(',', $this->codigos_clientes);

		$clienteID = array();
		$medicoID = array();

		// Através destes códigos iremos obter e enviar:
		foreach ($pedidos as $pedido) {

			$this->ApiAutorizacao->cod_cliente = $pedido[0]['cod_cliente'];

			// clinicas
			$clinicas = $PedidoExame->busca_clinica_por_pedido_exame_nexo($pedido[0]['cod_pedido']);				
			//varre as clinicas
			foreach ($clinicas as $clinica) {

				// Verificando se a clinica já foi enviada.
				if (array_search($clinica[0]['CodigoClinica'], $clienteID) === false) {
					$clienteID[] = $clinica[0]['CodigoClinica'];
					$this->enviaClinica((Object) $clinica[0]);
				}
			}//fim foreach

			// profissinais
			$profissinais = $PedidoExame->busca_medico_pedido_exame_nexo($pedido[0]['cod_pedido']);
			foreach ($profissinais as $profissional) {
				// Verificando se o medico já foi enviado.
				if (array_search($profissional[0]['CodigoProfissional'], $medicoID) === false) {
					$medicoID[] = $profissional[0]['CodigoProfissional'];
					$this->enviaProfissional((Object) $profissional[0]);
				}
			}//fim foreach

			// asos
			$asos = $PedidoExame->busca_aso_pedido_exame($pedido[0]['cod_pedido'], $codigos_clientes);				
			foreach ($asos as $aso) {
				//pega o item pedido exame baixa
				$codigo_ipeb = $aso[0]['codigo_ipeb'];
				
				//retira o codigo da baixa para não enviar para a nexo
				unset($aso[0]['codigo_ipeb']);

				//envia a aso para a nexo
				$this->enviaAso((Object) $aso[0],$codigo_ipeb);

			}//fim foreach da aso

			// exames
			$exames = $PedidoExame->busca_itens_pedidos_exames_nexo($pedido[0]['cod_pedido'], $codigos_clientes);				
			foreach ($exames as $exame) {
				//pega o item pedido exame baixa
				$codigo_ipeb = $exame[0]['codigo_ipeb'];
				
				//retira o codigo da baixa para não enviar para a nexo
				unset($exame[0]['codigo_ipeb']);

				$this->enviaExameFuncionario((Object) $exame[0], $codigo_ipeb);
			}//fim foreach exames			

		}//fim foreach

	}//fim enviaPedidos

	private function enviaClinica($clinica) {
		
		$this->envioPadrao($clinica, 'enviarClinica', 'ENVIO_NEXO_CLINICA');

	}

	private function enviaAso($aso, $codigo_ipeb) {
		//verifica se foi enviado corretamente os dados para a nexo
		if($this->envioPadrao($aso, 'enviarAso', 'ENVIO_NEXO_ASO')) {			
			//atualiza os dados da itens pedidos exames baixa
			$this->atualiza_ipeb($codigo_ipeb);

		}//fim envio padrao
		
	}//fim enviaAso

	private function enviaExameFuncionario($exame, $codigo_ipeb) {
		
		//verifica se foi enviado corretamente os exames para nexo
		if($this->envioPadrao($exame, 'enviarExameFuncionario', 'ENVIO_NEXO_EXAME')) {			
			//atualiza os dados da itens pedidos exames baixa
			$this->atualiza_ipeb($codigo_ipeb);

		}//fim envio padrao
		
	}//fim enviaExameFuncionario

	private function enviaProfissional($profissional) {
		
		$this->envioPadrao($profissional, 'enviarProfissional', 'ENVIO_NEXO_PROFISSIONAL');

	}

	private function envioPadrao($dataObject, $method, $logName, $codigo_cliente = null) {

		//seta as variaveis para nao dar notice do php
		$entrada = null;
		$saida = null;

		try {
			
			$client = $this->NexoClient;
			$client->{$method}($dataObject);
			
			$entrada = new StdClass();
			$entrada->headerEnvio = $client->getLastRequestHeaders();
			$entrada->bodyEnvio = $client->getLastRequestBody();
			
			// debug($entrada->bodyEnvio);
			// print "###########################################################################\n";

			$saida = new StdClass();
			$saida->resposta = $client->getLastResponse();

			$status = 0;
			$msg_retorno = $saida->resposta->resultadoExecucao->Mensagem;

		} catch (Exception $e) {
			// $this->log('erro: '. $e->getMessage(), 'debug');
			$status = 5;
			$msg_retorno = $e->getMessage();

			// debug($msg_retorno);exit;

			//gera alerta
			$url = "url: ".$this->NexoClient->getWsdlUrl() ." | user: ". $this->NexoClient->getUser() ." | pass: ". $this->NexoClient->getPass();	
			$dados['tipo_integracao'] = $logName;
			$dados['conteudo'] = $url . "####". json_encode($entrada);
			$dados['retorno'] = json_encode($saida);
			$dados['descricao'] = $msg_retorno;
			$dados['status'] = $status;
			$dados['data'] = date('Y-m-d H:i:s');
			//insere na alerta
			$this->ApiAutorizacao->alerta_integracao($dados);
		}

		$this->ApiAutorizacao->setCodCliente($codigo_cliente);
		$this->ApiAutorizacao->log_api(
			json_encode($entrada), json_encode($saida), $status, $msg_retorno, $logName
		);

		//verifica qual retorno
		if($status == 5) {
			return false;
		}

		return true;
	}

	/**
	 * [setIPEB description]
	 * 
	 * metodo para atualizar os dados da itens pedidos exames baixa
	 * 
	 * @param [type] $codigo_ipeb [description]
	 */
	public function atualiza_ipeb($codigo_ipeb)
	{

		//instancia a model para utilizar
		$this->ItemPedidoExameBaixa = ClassRegistry::init('ItemPedidoExameBaixa');
		
		//seta os dados para serem atualizados
		$dados = array(
			'ItemPedidoExameBaixa' => array(
				'codigo' => $codigo_ipeb,
				'integracao_cliente' => 1
			)
		);

		//atualiza a coluna integracao_cliente
		if($this->ItemPedidoExameBaixa->atualizar($dados)){
			// print "atualizou:".$codigo_ipeb."\n";
			return true;
		}

		return false;

	}//fim setIPEB

	
	public function enviosAtestados() {

		$horas = 2;
		$Atestado = ClassRegistry::init('Atestado');
		$atestados = $Atestado->busca_itens_atestados_nexo($this->codigos_clientes, $horas, $this->codigos_usuarios_not);
		// debug($atestados); exit;
		
		if(!empty($atestados)) {			
			foreach ($atestados as $atestado) {
				//pega o codigo cliente para gravar no log e depois ritra ele
				$codigo_cliente = $atestado[0]['codigo_cliente'];
				unset($atestado[0]['codigo_cliente']);

				//envia o medico padrao  profissinais
				$profissional = $Atestado->busca_medico_atestado_nexo($atestado[0]['NumeroAtestado']);				
				$this->envioPadrao($profissional[0], 'enviarProfissional', 'ENVIO_NEXO_PROFISSIONAL', $codigo_cliente);				
				
				//envia o atestado medico
				$this->envioPadrao($atestado[0], 'enviarAbsenteismo', 'ENVIO_NEXO_ABSENTEISMO', $codigo_cliente);
			}
		}

	}

}