<?php
App::import('Component', 'Mailer.Scheduler');
class ImportacaoLayoutsShell extends Shell
{
	public $uses = array(
		'IntUploadCliente',
		'IntClienteCargos',
		'IntClienteSetores',
		'IntClienteEmpresa',
		'IntClienteCentroResultado',
		'IntClienteFuncionarios',
		'IntClienteFuncionariosEmpresa',
		'MapLayout',
		'MapLayoutDetalhe'
	);
	public $layouts = array();
	private $queue = array();
	public $mailTo = "tid@rhhealth.com.br";
	public $codigo_arquivo = null;

	public function main()
	{
		echo "**********************************************\n";
		echo "$ \n";
		echo "$ Importação de layouts\n";
		echo "$ \n";
		echo "**********************************************\n\n";
		echo "=> run: Inicia a fila de processamento\n";
	}
	public function is_alive()
	{
		$retorno = shell_exec("ps -ef | grep \"importacao_layouts \" | wc -l");
		return ($retorno > 3);
	}
	public function alloc()
	{
		ini_set('memory_limit', '5G');
		ini_set('max_execution_time', '0');
		set_time_limit(0);
	}
	/**
	 * Retorna um array com todas as integrações
	 * 
	 * @param array $integrations
	 * @return boolean
	 */
	private function getIntegrations()
	{
		$integrationsModels = array(
			$this->IntClienteCargos,
			$this->IntClienteSetores,
			$this->IntClienteEmpresa,
			$this->IntClienteCentroResultado,
			$this->IntClienteFuncionarios,
			$this->IntClienteFuncionariosEmpresa
		);

		return $integrationsModels;
	}
	/**
	 * Inicia o processamento da fila
	 */
	public function run()
	{
		$this->log("Script de importação iniciado", 'debug');
		try {
			$this->alloc();
			$this->__load();
			if ($this->is_alive() == true || empty($this->queue)) {
				$this->log("Fila vazia ou processo já rodando", 'debug');
				return false;
			}
			$this->log("Processamento iniciado", 'debug');
			$this->process();
		}catch(Exception $e) {
			$this->log($e->getMessage(), 'debug');
		}
	}

	/**
	 * Inicia o processamento do arquivo carregado
	 */
	public function run_arquivo()
	{
		$this->log("Script de importação por arquivo iniciado", 'debug');
		
		try {
			if(!isset($this->args[1])) {
				$msg_erro = "Obrigatório passar o código do arquivo";
				print $msg_erro."\n";
				throw new Exception($msg_erro);
			}
			$this->codigo_arquivo = $this->args[1];
			
			$this->alloc();
			$this->__load();
			
			// if ($this->is_alive() == true || empty($this->queue)) {
			// 	$this->log("Fila vazia ou processo já rodando", 'debug');
			// 	return false;
			// }
			$this->log("Processamento iniciado", 'debug');
			$this->process();

		}catch(Exception $e) {
			$this->log($e->getMessage(), 'debug');
		}
	}


	/**
	 * Get authenticated user
	 * 
	 * @return array
	 */
	public function userCode()
	{
		return $this->args[0];
	}

	private function percent($value, $amount) {
		return round((($amount / 100) * $value), 0);
	}

	private function process()
	{
		$integrations = $this->getIntegrations();
		$userCode = $this->userCode();
		$IntUploadClienteModel = $this->IntUploadCliente;
		if (!$userCode) {
			throw new Exception(
				"O código de usuário informado é invalido"
			);
		}

		// $this->log("Teste fila" . json_encode($this->queue), 'debug');
		// debug($this->queue);exit;

		// while (is_array($this->queue) == true && empty($this->queue) == false) {
		foreach($this->queue AS $queue) {

			
			// $intUploadCliente = $this->__top();
			$intUploadCliente = $queue;

			$this->log("Processamento o arquivo {$intUploadCliente['IntUploadCliente']['nome_arquivo']}", 'debug');
			try {
				$qtdLinhas = (int) $intUploadCliente['IntUploadCliente']['qtd_linhas'];
				$lineTap = $this->percent($qtdLinhas, 15);
				$this->IntUploadCliente->configure(5000);
				
				$this->IntUploadCliente->troca_status(9, $intUploadCliente); //carregando o arquivo

				$mapLayout = $this->processFile($intUploadCliente['IntUploadCliente']['nome_arquivo'],$intUploadCliente['IntUploadCliente']['codigo_cliente']);
				
				$stageTablesSaved  = $this->IntUploadCliente->open(
					$intUploadCliente['IntUploadCliente']['caminho_arquivo'],
					$mapLayout['MapLayout']['ignora_primeira_linha'] == 1,
					$mapLayout['MapLayoutDetalhe'],
					$intUploadCliente['IntUploadCliente']['codigo_cliente'],
					$mapLayout['MapLayout']['codigo_empresa'],
					$userCode,
					$integrations,
					$intUploadCliente['IntUploadCliente']['codigo'],
					$qtdLinhas,
					function ($proceedLines) use (&$intUploadCliente, &$IntUploadClienteModel) {
						$IntUploadClienteModel->atualiza_atributo($intUploadCliente, 'qtd_linhas_processadas', $proceedLines);
					}
				);
				if (!$stageTablesSaved) {
					$this->log("Falha ao salvar arquivo {$intUploadCliente['nome_arquivo']}",'debug');
					throw new Exception(
						"Falha ao salvar arquivo {$intUploadCliente['nome_arquivo']}"
					);
				}

				$this->IntUploadCliente->troca_status(3, $intUploadCliente);
				$this->out("Processamento finalizado");

				//conta

				//chama o script para fazer a importacao do processamento
				// $this->log(print_r($intUploadCliente,1),'debug');

				$metodoProcessamento = (!empty($intUploadCliente['IntUploadCliente']['tabela_referencia'])) ? $intUploadCliente['IntUploadCliente']['tabela_referencia'] : '';
				if(!empty($metodoProcessamento)) {
					$this->log('Iniciando o processamento de folha: ' . $metodoProcessamento . " codigo_arquivo: " . $intUploadCliente['IntUploadCliente']['codigo'],'debug');
					// $this->IntUploadCliente->$metodoProcessamento($intUploadCliente['IntUploadCliente']['codigo']);
					Comum::execInBackground(ROOT . DS . 'cake' . DS . 'console' . DS . 'cake -app ' . APP . ' importacao_folha_pagto ' . $metodoProcessamento . ' ' . $intUploadCliente['IntUploadCliente']['codigo']);
				}

			} catch (Exception $e) {
				$this->log($e->getMessage(), 'debug');
				$this->IntUploadCliente->troca_status(13, $intUploadCliente);
				$this->out(
					$e->getMessage()
				);
				$this->sendMail("Falha na importação do arquivo", "
					O arquivo {$intUploadCliente['IntUploadCliente']['nome_arquivo']} falhou em ser importado.
					Motivo: {$e->getMessage()}
				");
			}

			// $this->__pop();
			// if (count($this->queue) <= 1) {
			// 	$this->__load();
			// }

		}//fim foeach/while
	}

	/**
	 * Obtêm a extensão de um arquivo
	 * 
	 * @return string
	 */
	protected function getFileExtension($file)
	{
		return strtolower(end((explode(".", $file))));
	}

	protected function sendMail($subject, $body) {
		$this->Scheduler = new SchedulerComponent();
		$this->out("Enviando email para {$subject}");
		$options = array(
				'from' => 'portal@rhhealth.com.br',
				'sent' => null,
				'subject' => $subject,
				'to' => $this->mailTo
		);

		$enviado = $this->Scheduler->schedule($body, $options);
		if(!$enviado) {
			$message = "Falha ao enviar email";
			$this->out($message);
			$this->log($message, 'debug');
		}else {
			$message = "Email enviado com sucesso!";
			$this->out($message);
			$this->log($message, 'debug');
		}
		
		return $enviado;
	}

	/**
	 * Processa arquivo, verificando sua validade
	 * 
	 * @throws Exception
	 * @return array
	 */
	protected function processFile($fileName,$codigo_cliente)
	{
		$layouts         = $this->layouts;
		$valido          = false;
		$valorComparacao = (explode("_", $fileName));
		$valorComparacao = $valorComparacao[0];
		$mapLayout       = array();
		foreach ($layouts as $layout) {
			if($layout['MapLayout']['codigo_cliente'] == $codigo_cliente) {
				if (strtoupper($valorComparacao) == strtoupper($layout['MapLayout']['dsname'])) {
					$valido    = true;
					$mapLayout = $layout;
					break;
				}
			}
		}

		if ($valido == false) {
			throw new Exception(
				"Não existe um layout definido para o valor: {$valorComparacao}"
			);
		}

		return $mapLayout;
	}


	private function __pop()
	{
		unset($this->queue[count($this->queue) - 1]);
	}

	private function __load()
	{

		$conditions_upload = array('ativo' => 1, 'codigo_status_transferencia' => 1);
		if(!is_null($this->codigo_arquivo)) {
			$conditions_upload['codigo'] = $this->codigo_arquivo;
		}

		$data = $this->IntUploadCliente->find('all', array('conditions' => $conditions_upload));
		
		$this->queue = is_array($data) == true ? $data : array();
		if(empty($this->queue)) {
			$this->log("Fila de importação vazia", 'debug');
		}
		
		$codigo_clientes = array();
		if(!empty($data)) {
			foreach($data AS $up) {
				$codigo_clientes[$up['IntUploadCliente']['codigo_cliente']] = $up['IntUploadCliente']['codigo_cliente'];
			}
			// $array_cliente =
			// debug($data);exit;
			$this->layouts = $this->MapLayout->with_all_binds($codigo_clientes);
			// $this->log(print_r($this->layouts,true),'debug');exit;
		}
	}
	private function __top()
	{
		return $this->queue[0];
	}
}
