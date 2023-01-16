<?php
class EscoltasController extends AppController {
	public $name 	= 'Escoltas';
	var $uses 		= array('TEescEmpresaEscolta'); 
	
	function auto_completar() {
		$lista		= $this->TEescEmpresaEscolta->autoCompletar(strtoupper($_GET['term']));
		$retorno	= array();
		if($lista){
			foreach ($lista as $key => $escolta)
				$retorno[] 	= array(
					'label' => $escolta['TPessPessoa']['pess_nome'], 
					'value' => $escolta['TEescEmpresaEscolta']['eesc_oras_pess_pesj_codigo']);
		}
		
		echo json_encode($retorno);
		exit;
	}

	function buscar_codigo(){
		$this->display = 'ajax';
	}
	function listagem(){
		$filtros = $this->Filtros->controla_sessao($this->data, 'Escolta');
        $listagem = $this->TEescEmpresaEscolta->listarEscolta($filtros['descricao']);
        $this->set(compact('listagem'));
	}
}