<?php 

/**
 * Client responsável por integração com ambiente remoto da nexo através do protocolo SOAP
 * @author Rodrigo Ruotolo Barbosa <roderickruotolo@gmail.com>
 */
class NexoClientComponent {


	public $name = 'NexoClient';

	/**
	 * @var string $user
	 */
	public $user = "NEXOINT";
	
	/**
	 * @var string $pass
	 */
	public $pass = "1234";
	
	/**
	 * Homologação 
	 * @var string $wsdlUrl
	 */
	// public $wsdlUrl = 'http://homologacao.login.nexoweb.com/Siemens_wsNexoCSLoaders?wsdl'; //homologacao
	public $wsdlUrl_prod = 'https://portal.nexoweb.com/Siemens_wsNexoCSLoaders?wsdl'; //producao
	public $wsdlUrl_homol = 'https://homologacaoportal.nexoweb.com/Siemens_wsNexoCSLoaders?wsdl'; //homol

	public $wsdlUrl_energy_prod = 'https://portal.nexoweb.com/SiemensEnergy_wsNexoCSLoaders?wsdl'; //producao
	public $wsdlUrl_energy_homol = 'https://homologacaoportal.nexoweb.com/SiemensEnergy_wsNexoCSLoaders?wsdl'; //homol
	
	/**
	 * @var array $clientOptions Armazena opções para o objeto SoapClient
	 */
	public $clientOptions = array();
	
	/**
	 * @var SoapClient $client Instância de SoapClient
	 */
	public $client = null;
	
	/**
	 * @var string $lastRequestHeaders Cabeçalho da última requisição SOAP feita
	 */
	public $lastRequestHeaders = null;
	
	/**
	 * @var string $lastRequestBody Conteúdo do corpo da última requisição SOAP feita
	 */
	public $lastRequestBody = null;
	
	/**
	 * @var string $lastResponse
	 */
	public $lastResponse = null;

	public function initialize(&$controller, $settings = array()) {        
		// saving the controller reference for later use
		$this->controller =& $controller;
	}

	/**
	 * [soapInit envia os dados para o sistema nexo]
	 * @param  [type] $ambiente [por padrao é enviado para o ambiente atual, e quando passado o paramentro da energy enviado para outra link]
	 * @return [type]           [description]
	 */
	public function soapInit($ambiente='atual')
	{
		$this->loadOptions();

		$wsdl = '';
		$wsdl_energy = '';

		//verifica qual ambiente esta processando o arquivo é producao
		if(Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO){
			//seta as variavels para onde deve apontar
			$wsdl = $this->wsdlUrl_prod;
			$wsdl_energy = $this->wsdlUrl_energy_prod;
		}
		else {
			//seta as variavels para onde deve apontar
			$wsdl = $this->wsdlUrl_homol;
			$wsdl_energy = $this->wsdlUrl_energy_homol;
		}

		// $wsdl = $this->wsdlUrl_prod;
		// $wsdl_energy = $this->wsdlUrl_energy_prod;

		//verifica qual ambiente esta enviando
		$this->wsdlUrl = $wsdl;
		if($ambiente == 'energy') {
			$this->wsdlUrl = $wsdl_energy;
		}

		$this->client = new SoapClient($this->wsdlUrl, $this->clientOptions);
	}

	public function loadOptions() {
		$this->setClientOptions('login', $this->user);
		$this->setClientOptions('password', $this->pass);
		$this->setClientOptions('encoding', 'UTF-8');
		$this->setClientOptions('cache_wsdl', WSDL_CACHE_NONE);
		$this->setClientOptions('soap_version', SOAP_1_1);		
		$this->setClientOptions('trace', true); 		
		$this->setClientOptions('exceptions', true);
		$this->setClientOptions('connection_timeout', 180);
		$this->setClientOptions('authentication', SOAP_AUTHENTICATION_BASIC);
		// $this->setClientOptions('stream_context', $this->getStreamContextCreate()); Funciona apenas para versão mais recentes do PHP
	}

	public function getOptions() {
		return $this->clientOptions;
	}

	/**
	 * @see ClientIntegracaoNexo::clientOptions 
	 * @param $key Nome da opção do SoapClient
	 * @param $value Valor da opção do SoapClient
	 */
	public function setClientOptions($key, $value) {
		$this->clientOptions[$key] = $value;
	}

	/**
	 * @return string
	 */
	public function getLastRequestHeaders() {
		return $this->lastRequestHeaders;		
	}

	/**
	 * @return string
	 */
	public function getLastRequestBody() {
		return $this->lastRequestBody;
	}

	/**
	 * @return string
	 */
	public function getLastResponse() {
		return $this->lastResponse;
	}

	/**
	 * Atualiza dados sobre a última requisição
	 * @return void
	 */
	public function updateDataRequest() {
		$this->lastRequestHeaders = $this->client->__getLastRequestHeaders();
		$this->lastRequestBody = $this->client->__getLastRequest();
	}

	public function enviarClinica($clinica) {
		$this->lastResponse = $this->client->EnviarClinicaLoader(
			array('clinicaLoader' => $clinica)
		);
		$this->updateDataRequest();
	}

	public function enviarAso($aso) {
		$this->lastResponse = $this->client->EnviarAsoLoader(
			array('asoLoader' => $aso)
		);
		$this->updateDataRequest();
	}

	public function enviarExameFuncionario($exameFuncionario) {
		$this->lastResponse = $this->client->EnviarExameFuncionarioLoader(
			array('exameFuncionarioLoader' => $exameFuncionario)
		);
		$this->updateDataRequest();
	}

	public function enviarProfissional($profissional) {
		$this->lastResponse = $this->client->EnviarProfissionalLoader(
			array('profissionalLoader' => $profissional)
		);
		$this->updateDataRequest();
	}

	public function enviarAbsenteismo($absenteismo) {
		$this->lastResponse = $this->client->EnviarAbsenteismoLoader(
			array('absenteismoLoader' => $absenteismo)
		);
		$this->updateDataRequest();
	}

	public function getWsdlUrl()
	{
		return $this->wsdlUrl;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function getPass()
	{
		return $this->pass;
	}

}