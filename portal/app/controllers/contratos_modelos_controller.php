<?php
class ContratosModelosController extends AppController {

	public $name = 'ContratosModelos';
	public $layout = 'contrato_modelo';
	public $components = array('RequestHandler');
	public $helpers = array('Html', 'Ajax');

	function index() {
        $this->pageTitle = 'Contratos - Modelos';
	    $this->set('isAjax', $this->RequestHandler->isAjax());
		$this->set('contratosmodelos', $this->paginate());
	}

	function incluir() {
        $this->pageTitle = 'Incluir Modelo';

		if (!empty($this->data)) {
			if ($this->ContratoModelo->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->BSession->setFlash('save_error');
			}
		}
	}

    function mostrarcontrato($codigo = NULL){
      if (!empty($codigo)){
        $texto = $this->ContratoModelo->mostrarcontrato($codigo);

        $this->set('texto',$texto);
      }
    }
}
