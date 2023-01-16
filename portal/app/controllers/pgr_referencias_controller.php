<?php
class PgrReferenciasController extends AppController {
	var $name = 'PgrReferencias';
	var $uses = array('TPrefPgrReferencia');

	public function beforeFilter() {
		//$this->BAuth->allow(array('*'));
		parent::beforeFilter();
	}

	public function index() {
		$this->loadModel('TPrefPgrReferencia');
		$this->carrega_combos();
		$this->pageTitle = 'PGR por Alvo Origem';
		$this->data['TPrefPgrReferencia'] 	=  $this->Filtros->controla_sessao($this->data, 'TPrefPgrReferencia');
	}	

	public function listagem() {
		$this->loadModel('TPrefPgrReferencia');
		
		$filtros 	= $this->Filtros->controla_sessao(array('TPrefPgrReferencia' => array()), 'TPrefPgrReferencia');
		
		$authUsuario=& $this->authUsuario;
		if($authUsuario['Usuario']['codigo_cliente']) {
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];		
		}

		$pgrsAlvo = null;
		if (!empty($filtros['codigo_cliente'])) {
			$this->paginate['TPrefPgrReferencia'] = Array(
				'limit' => 50,
				'conditions' => $filtros,
				'method' => 'listagem'
			);
			$pgrsAlvo = $this->paginate('TPrefPgrReferencia');
		}

		$this->set(compact('pgrsAlvo'));

	}

    public function incluir() {
        $this->pageTitle = 'Incluir PGR por Alvo Origem';
        $this->loadModel('Cliente');
        $this->carrega_combos();
        
        $cliente = null;

        $filtros 	= $this->Filtros->controla_sessao(array('TPrefPgrReferencia' => array()), 'TPrefPgrReferencia');

		$authUsuario=& $this->authUsuario;
		if($authUsuario['Usuario']['codigo_cliente']) {
			$codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];		
		} else {
			if (!empty($filtros['codigo_cliente'])) {
				$codigo_cliente = $filtros['codigo_cliente'];
				$this->data['TPrefPgrReferencia']['codigo_cliente'] = $codigo_cliente;
			}
		}
		//if (isset($this->data['TPrefPgrReferencia']['bvre_pjur_pess_oras_codigo']))
		if (!empty($codigo_cliente)) {
			$cliente = $this->Cliente->carregar($codigo_cliente);
		}

        //$this->carregaCombos();
        if($this->RequestHandler->isPost()) {
            
            if ($this->TPrefPgrReferencia->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
                exit;
            }
            $this->BSession->setFlash('save_error');
        }

        $readonly = false;
        $this->set(compact('readonly','cliente'));
    }

    public function inativar($codigo) {
    	$this->layout = false;
    	if (empty($codigo)) {
    		$this->BSession->setFlash('','PGR por Alvo não encontrado');
    		exit;
    	}
    	$dados = $this->TPrefPgrReferencia->read(null, $codigo);
    	if (empty($dados['TPrefPgrReferencia']['pref_codigo'])) {
    		$this->BSession->setFlash('','PGR por Alvo já está excluído');
    		exit;
    	}

    	if (!$this->TPrefPgrReferencia->inativar($codigo)) {
    		$this->BSession->setFlash('','Erro ao inativar o PGR por Alvo');
    		exit;
    	}

    	echo true;
    	exit;
    }
    
    public function carrega_combos(){
		$this->loadModel('TPgpgPg');
		$fields = Array("pgpg_codigo");
		$order = Array("pgpg_codigo");
		$conditions = Array("pgpg_estatus" =>"A");
		$pgrs = $this->TPgpgPg->find('list', compact("fields","conditions", "order"));
		$this->set(compact("pgrs"));
    }

}