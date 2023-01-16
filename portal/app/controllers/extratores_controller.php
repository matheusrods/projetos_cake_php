<?php
class ExtratoresController extends AppController {
    public $name = 'Extratores';
    public $components = array('Extracao');
    public $uses = array();
	
	public function beforeFilter() {
		$this->BAuth->allow('*');
	}

	public function denatran(){
		$respostas = array();
		if (!empty($this->data['ExtratorCnh']))
			$respostas = $this->Extracao->denatranCnh($this->data['ExtratorCnh']['cpf'], $this->data['ExtratorCnh']['registro'], $this->data['ExtratorCnh']['seguranca']);
		else if (!empty($this->data['ExtratorVeiculo']))
			$respostas = $this->Extracao->denatranVeiculo($this->data['ExtratorVeiculo']['cpf'], $this->data['ExtratorVeiculo']['renavam']);		
		$this->set(compact('respostas'));
	}

	public function stj(){
		$respostas = array();
		if (!empty($this->data['Stj']))
			$respostas = $this->Extracao->stj($this->data['Stj']['nome']);
		$this->set(compact('respostas'));
	}
	
}

?>
