<?php
class ItensChecklistController extends AppController {
	var $name = 'itens_checklist';
	var $uses = array('TIcheItemChecklist','TPjurPessoaJuridica');

	// function beforeFilter() {
 //    	parent::beforeFilter();
 //    	$this->BAuth->allow('index','incluir','editar','listagem');
 //    }
	
	function index(){
		$this->Filtros->limpa_sessao("AtendimentoSac");
		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])){
			$this->data['TIcheItemChecklist']['iche_pjur_pess_oras_codigo'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}
	
		$this->data['TIcheItemChecklist'] = $this->Filtros->controla_sessao($this->data, "TIcheItemChecklist");
	}

	function listagem(){
		App::Import('Component',array('DbbuonnyGuardian'));
		$filtros = $this->Filtros->controla_sessao($this->data, $this->TIcheItemChecklist->name);
        $filtros['iche_pjur_pess_oras_codigo'] = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($filtros['iche_pjur_pess_oras_codigo']);
		$pjur_codigo = $filtros['iche_pjur_pess_oras_codigo'];
		$conditions = $this->TIcheItemChecklist->converteFiltrosEmConditions($filtros);
		$this->TIcheItemChecklist->invalidate('iche_pjur_pess_oras_codigo','d');
		$this->paginate['TIcheItemChecklist']  = array(
	        'conditions'    => $conditions,
			'limit'         => 50,
  		);

		$listagem = $this->paginate('TIcheItemChecklist');
		$cliente = $this->TPjurPessoaJuridica->buscaPorCodigo($filtros['iche_pjur_pess_oras_codigo']);
		$this->set(compact('listagem','pjur_codigo','cliente'));
	}

	function editar($codigo= null) {
        $this->pageTitle = 'Atualizar Item Checklist';
        if (!empty($codigo) && !empty($this->data)) {
        	$this->data['TIcheItemChecklist']['iche_codigo'] = $codigo;
            if ($this->TIcheItemChecklist->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }else{
            $this->data = $this->TIcheItemChecklist->read(null, $codigo);
        }
        $cliente = $this->TPjurPessoaJuridica->buscaPorCodigo($this->data['TIcheItemChecklist']['iche_pjur_pess_oras_codigo']);
        $this->set(compact('cliente'));
    }

	function incluir() {
		$this->pageTitle = 'Incluir Item Checklist'; 
		if($this->RequestHandler->isPost()) {
			if($this->TIcheItemChecklist->incluir($this->data)){         
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->BSession->setFlash('save_error');
			}
		}
		$cliente = $this->TPjurPessoaJuridica->buscaPorCodigo($this->params['pass'][0]);
        $this->set(compact('cliente'));
    
	}


}
?>