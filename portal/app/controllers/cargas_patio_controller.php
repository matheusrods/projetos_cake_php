<?php
class CargasPatioController extends AppController {
	var $name = 'cargas_patio';
	var $uses = array('TCpatCargasPatio');

	public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('buscar_loadplan'));
    }

	function index(){
		$this->loadModel('RelatorioSm');
		$this->pageTitle = 'Cargas em Pátio';
		if (!empty($this->authUsuario['Usuario']['codigo_cliente']))
			$this->data['TCpatCargasPatio']['cpat_pjur_pess_oras_codigo'] = $this->authUsuario['Usuario']['codigo_cliente'];

		$alvos_bandeiras_regioes = $this->RelatorioSm->carregaCombosAlvosBandeirasRegioes($this->data['TCpatCargasPatio']['cpat_pjur_pess_oras_codigo'], true, true);
		$this->data['TCpatCargasPatio'] = $this->Filtros->controla_sessao($this->data, "TCpatCargasPatio");
		$this->set(compact('alvos_bandeiras_regioes'));
	}

	function listagem(){
		App::Import('Component',array('DbbuonnyGuardian'));
		$filtros = $this->Filtros->controla_sessao($this->data, $this->TCpatCargasPatio->name);
        $filtros['cpat_pjur_pess_oras_codigo'] = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($filtros['cpat_pjur_pess_oras_codigo']);
		$pjur_codigo = $filtros['cpat_pjur_pess_oras_codigo'];
		$conditions = $this->TCpatCargasPatio->converteFiltrosEmConditions($filtros);
	
		$this->paginate['TCpatCargasPatio']  = array(
	        'conditions'    => $conditions,
			'limit'         => 50,
  		);

		$listagem = $this->paginate('TCpatCargasPatio');
		$this->set(compact('listagem','pjur_codigo'));
	}

	function carregaCombos($pjur_codigo){
		$this->loadModel('TRefeReferencia');
		$cds = $this->TRefeReferencia->listaCds($pjur_codigo);
		$this->set(compact('cds'));
	}

	function incluir($codigo = NULL) {
		$this->loadModel('TPjurPessoaJuridica');
		$this->pageTitle = 'Incluir Carga em Pátio';
		
		if(isset($codigo)){
			$filtros['TCpatCargasPatio'] = $this->Filtros->controla_sessao($this->data['cpat_pjur_pess_oras_codigo'], "TCpatCargasPatio");
			$this->data['TCpatCargasPatio']['cpat_pjur_pess_oras_codigo'] = $codigo;
		}
		if(empty($this->data['TCpatCargasPatio']['cpat_data_carregamento'])){
			$this->data['TCpatCargasPatio']['cpat_data_carregamento'] = date('d-m-Y');
		}

		$cliente = $this->TPjurPessoaJuridica->buscaPorCodigo($this->data['TCpatCargasPatio']['cpat_pjur_pess_oras_codigo']);
		$this->carregaCombos($this->data['TCpatCargasPatio']['cpat_pjur_pess_oras_codigo']);
		if($this->RequestHandler->isPost()) {
			if($this->TCpatCargasPatio->validaInclusao($this->data)){
				if($this->TCpatCargasPatio->incluir($this->data)){   				      
					$this->BSession->setFlash('save_success');
					$this->redirect(array('action' => 'index'));
				} else {
					$this->BSession->setFlash('save_error');
				}
			}else{
				$this->BSession->setFlash('save_error');
			}	
		}

		$this->set(compact('cliente'));
	}

	function editar($codigo= null) {
		$this->loadModel('TPjurPessoaJuridica');
        $this->pageTitle = 'Atualizar Carga em Pátio';        
        
        if (!empty($codigo) && !empty($this->data)) {
        	$this->data['TCpatCargasPatio']['cpat_codigo'] = $codigo;
        	if($this->TCpatCargasPatio->validaInclusao($this->data)){
	            if ($this->TCpatCargasPatio->atualizar($this->data)) {
	                $this->BSession->setFlash('save_success');
	                $this->redirect(array('action' => 'index'));
	            } else {
	                $this->BSession->setFlash('save_error');
	            }
		    }
		    $this->carregaCombos($this->data['TCpatCargasPatio']['cpat_pjur_pess_oras_codigo']);
		    $cliente = $this->TPjurPessoaJuridica->buscaPorCodigo($this->data['TCpatCargasPatio']['cpat_pjur_pess_oras_codigo']);

        }else{
        	$this->data = $this->TCpatCargasPatio->read(null, $codigo);
        	$cliente = $this->TPjurPessoaJuridica->buscaPorCodigo($this->data['TCpatCargasPatio']['cpat_pjur_pess_oras_codigo']);
        	$this->carregaCombos($this->data['TCpatCargasPatio']['cpat_pjur_pess_oras_codigo']);
        }

        $this->set(compact('cliente'));
    }

    public function buscar_loadplan($loadplan) {
    	$this->loadModel('TViagViagem');
    	$this->loadModel('TLoadLoadplan');
        $this->layout = 'ajax';
      //  $result = $this->TViagViagem->carregarUltimaSmLoadplanPorLoad($loadplan);
        $result = $this->TLoadLoadplan->carregarUltimaViagemPorLoadplan($loadplan);
        $retorno = new stdClass();
        $retorno->sucesso = false;
       
        if(!$result){
        	$result = $this->TLoadLoadplan->carregarPorLoadplan($loadplan);
        }

        if($result) {
            $retorno->sucesso = true;
            if($result['TViagViagem']['viag_valor_carga'] > 0){
            	$retorno->dados = $result['TViagViagem'];
        	}else{
        		$retorno->dados = false;
        	}
        }

        echo json_encode($retorno);
        die();
    }

}
?>
