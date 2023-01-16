<?php
class PerifericosSimilaresController extends AppController {
	var $name = 'PerifericosSimilares';
	var $uses = array('TPesiPerifericoSimilar', 'TPpadPerifericoPadrao');

	function index() {
		$this->pageTitle = 'Periféricos Similares';
		$this->TPesiPerifericoSimilar->bindModel(array('belongsTo' => array(
			'TPpadPerifericoPadrao' => array('foreignKey' => 'pesi_ppad_codigo'),
			'TPpadPerifericoPadraoSimilar' => array('className' => 'TPpadPerifericoPadrao', 'foreignKey' => 'pesi_ppad_codigo_similar'),
		)));
		$perifericos = $this->TPesiPerifericoSimilar->find('all', array('order' => array('TPpadPerifericoPadrao.ppad_descricao')));
		$this->set(compact('perifericos'));
	}

	function incluir() {
		$this->pageTitle = "Incluir nova similaridade";
		$this->carregar_combos();
		if (!empty($this->data)) {
			if ($this->TPesiPerifericoSimilar->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            }else{
                $this->BSession->setFlash('save_error');
            }   
		}
	}

	function excluir($id) {
		if ($this->TPesiPerifericoSimilar->excluir($id)) {
			$this->BSession->setFlash('delete_success');
        }else{
            $this->BSession->setFlash('save_error');
        }   
        $this->redirect(array('action' => 'index'));
	}

	function carregar_combos() {
		$ppad_codigos = $this->TPpadPerifericoPadrao->find('list', array('conditions' => array('ppad_ativo' => 'S')));
		$this->set(compact('ppad_codigos'));
	}

}
?>