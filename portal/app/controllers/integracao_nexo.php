<?php 

class IntegracaoNexoController extends AppController {

	public $name = '';

	private $NexoClient;

	public function beforeFilter () {
		parent::beforeFilter();
		$this->BAuth->allow(array('*'));

		App::import('Component', 'NexoClient');
		$this->NexoClient = new NexoClientComponent();
	}

	public function enviosPedidosExames () {
		//pr($_POST);
		//die('post!!!');
	}

}