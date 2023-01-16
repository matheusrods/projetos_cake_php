<?php
 
class ClientesProdutosSmController extends AppController {
    public $name = 'ClientesProdutosSm';
	var $uses = array('TPjprPjurProd');

	function por_cliente($pjur_pess_oras_codigo){
		$produtos = $this->TPjprPjurProd->find('all',array('conditions' => array('pjpr_pjur_pess_oras_codigo' => $pjur_pess_oras_codigo), 'order' => 'prod_descricao'));
		$this->set(compact('produtos'));
	}

	function incluir($pjur_pess_oras_codigo){
		$this->pageTitle = 'Adicionar Tecnologia';
		$this->layout = 'ajax';
		if (!empty($this->data)){
			$this->data['TPjprPjurProd']['pjpr_pjur_pess_oras_codigo'] = $pjur_pess_oras_codigo;
			if ( !empty($this->data['TPjprPjurProd']['tipo_profissional']) ){
				$carreteiro_selecionado = (in_array( 1, $this->data['TPjprPjurProd']['tipo_profissional'] ) ? '1' : '0' );
				$outros_selecionado =  (in_array( 2, $this->data['TPjprPjurProd']['tipo_profissional'] ) ? '1' : '0' );
				$this->data['TPjprPjurProd']['pjpr_permite_carreteiro'] = $carreteiro_selecionado;
				$this->data['TPjprPjurProd']['pjpr_permite_outros'] = $outros_selecionado;
				unset($this->data['TPjprPjurProd']['tipo_profissional']);
			}
			if ($this->TPjprPjurProd->incluir($this->data)) {		
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
			}
		}
		$produtos = $this->TPjprPjurProd->produtosNaoVinculados($pjur_pess_oras_codigo);
		$profissionais_tipos = array('1' => 'Carreteiro', '2' => 'Outros');
		$this->set(compact('produtos', 'profissionais_tipos'));
	}

	function excluir($pjte_codigo) {
		die($this->TPjprPjurProd->excluir($pjte_codigo));
	}
}