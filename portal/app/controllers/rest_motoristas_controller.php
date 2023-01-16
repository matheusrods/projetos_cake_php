<?php
class RestMotoristasController extends AppController {
	var $name = 'RestMotoristas';
	var $uses = array(
		'TMotoMotorista',
	);

	public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('por_cpf'));
    }

    public function por_cpf() {
	    $this->layout = false;
    	if (!empty($this->params['url']['cpf'])) {
    		$cpf = $this->params['url']['cpf'];
	    	$this->TMotoMotorista->bindModel(array('belongsTo' => array(
	    		'TPfisPessoaFisica' => array('foreignKey' => 'moto_pfis_pess_oras_codigo'),
	    		'TPessPessoa' => array('foreignKey' => false, 'conditions' => 'pess_oras_codigo = pfis_pess_oras_codigo'),
	    	)));
	    	$motorista = $this->TMotoMotorista->carregarPorCpf($cpf);
	    	if ($motorista) {
	    		$motorista = $this->parseMotorista($motorista);
	    	}
    	}
	    $this->set(compact('motorista'));
    }

    private function parseMotorista($motorista) {
    	return array(
    		'cpf' => $motorista['TPfisPessoaFisica']['pfis_cpf'],
    		'nome' => $motorista['TPessPessoa']['pess_nome'],
    	);
    }
}
?>
