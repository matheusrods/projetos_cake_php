<?php
class BlqVeicReferenciasController extends AppController {
	var $name = 'BlqVeicReferencias';
	var $uses = array('TBvreBlqVeicReferencia');

	public function beforeFilter() {
		//$this->BAuth->allow(array('*'));
		parent::beforeFilter();
	}

	public function index() {
		$this->loadModel('TBvreBlqVeicReferencia');
		$this->pageTitle = 'Bloqueio de Veículos por Alvo Origem';
		$this->data['TBvreBlqVeicReferencia'] 	=  $this->Filtros->controla_sessao($this->data, 'TBvreBlqVeicReferencia');
	}	

	public function listagem() {
		$this->loadModel('TBvreBlqVeicReferencia');
		
		$filtros 	= $this->Filtros->controla_sessao(array('TBvreBlqVeicReferencia' => array()), 'TBvreBlqVeicReferencia');
		
		$authUsuario=& $this->authUsuario;
		if($authUsuario['Usuario']['codigo_cliente']) {
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];		
		}

		$bloqueios = null;
		if (!empty($filtros['codigo_cliente'])) {
			$this->paginate['TBvreBlqVeicReferencia'] = Array(
				'limit' => 50,
				'conditions' => $filtros,
				'method' => 'listagem'
			);
			$bloqueios = $this->paginate('TBvreBlqVeicReferencia');
		} else {
			//exit;
		}

		$this->set(compact('bloqueios'));

	}

    public function incluir() {
        $this->pageTitle = 'Incluir Bloqueio de Veículo por Alvo Origem';
        $this->loadModel('Cliente');
        
        $cliente = null;
		
		$filtros 	= $this->Filtros->controla_sessao(array('TBvreBlqVeicReferencia' => array()), 'TBvreBlqVeicReferencia');

		$authUsuario=& $this->authUsuario;
		if($authUsuario['Usuario']['codigo_cliente']) {
			$codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];		
		} else {
			if (!empty($filtros['codigo_cliente'])) {
				$codigo_cliente = $filtros['codigo_cliente'];
				$this->data['TBvreBlqVeicReferencia']['codigo_cliente'] = $codigo_cliente;
			}
		}
		//if (isset($this->data['TBvreBlqVeicReferencia']['bvre_pjur_pess_oras_codigo']))
		if (!empty($codigo_cliente)) {
			$cliente = $this->Cliente->carregar($codigo_cliente);
		}

        //$this->carregaCombos();
        if($this->RequestHandler->isPost()) {
            
            if ($this->TBvreBlqVeicReferencia->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
                exit;
            }
            $this->BSession->setFlash('save_error');
        } else {

        }

        $readonly = false;
        $this->set(compact('readonly','cliente'));
    }

    public function inativar($codigo) {
    	$this->layout = false;
    	if (empty($codigo)) {
    		$this->BSession->setFlash('','Bloqueio não encontrado');
    		exit;
    	}
    	$dados = $this->TBvreBlqVeicReferencia->read(null, $codigo);
    	if (empty($dados['TBvreBlqVeicReferencia']['bvre_codigo'])) {
    		$this->BSession->setFlash('','Bloqueio já está excluído');
    		exit;
    	}

    	if (!$this->TBvreBlqVeicReferencia->inativar($codigo)) {
    		$this->BSession->setFlash('','Erro ao inativar o Bloqueio');
    		exit;
    	}

    	echo true;
    	exit;
    }


}