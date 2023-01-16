<?php
class ClientesTecnologiasSmController extends AppController {
	public $name = 'ClientesTecnologiasSm';
	var $uses = array('TPjtePjurTecn', 'TTecnTecnologia');

	function por_cliente($pjur_pess_oras_codigo){
		$tecnologias = $this->TPjtePjurTecn->find('all',array('conditions' => array('pjte_pjur_pess_oras_codigo' => $pjur_pess_oras_codigo), 'order' => 'tecn_descricao'));
		$this->set(compact('tecnologias'));
	}

	function incluir($pjur_pess_oras_codigo){
		$this->pageTitle = 'Adicionar Tecnologia';
		$this->layout = 'ajax';
		if (!empty($this->data)){
			$this->data['TPjtePjurTecn']['pjte_pjur_pess_oras_codigo'] = $pjur_pess_oras_codigo;
			if ($this->TPjtePjurTecn->incluir($this->data)) {		
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
			}
		}
		$tecnologias = $this->TPjtePjurTecn->tecnologiasNaoVinculadas($pjur_pess_oras_codigo);
		$this->set(compact('tecnologias'));
	}

	function excluir($pjte_codigo) {
		die($this->TPjtePjurTecn->excluir($pjte_codigo));
	}
}