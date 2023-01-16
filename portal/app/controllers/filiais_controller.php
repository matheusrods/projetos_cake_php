<?php
class FiliaisController extends AppController {
    public $name = 'Filiais';
    public $uses = array('EnderecoRegiao');

	private function carregarCombos(){
		$lista_filiais = $this->EnderecoRegiao->find('list');
		$this->set(compact('lista_filiais'));
	}

	function listagem() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->EnderecoRegiao->name);
        $conditions = $this->EnderecoRegiao->converteFiltroEmCondition($filtros);

        $filiais = $this->EnderecoRegiao->find('all', compact('conditions'));

        $this->set(compact('filiais'));
    }

    function usuarios(){
        $this->pageTitle = 'Usuários por Filiais';
        $this->data['EnderecoRegiao'] = $this->Filtros->controla_sessao($this->data, $this->EnderecoRegiao->name);
    }
	
}

?>
