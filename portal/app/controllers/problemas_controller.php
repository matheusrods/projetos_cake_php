<?php
class ProblemasController extends AppController {
	var $name = 'Problemas';
	var $uses = array('ViagemProblema','TProbProblema');

	function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(
			array(
				'lista_problemas',
				'listar_por_tipo',
			)
		);
	}

	function lista_problemas($tipo_problema){
		$this->loadModel('ViagemProblema');
		
		$conditions 	= array('codigo_tipo_problema' => $tipo_problema);
		$order 			= array('descricao');
		$lista  = $this->ViagemProblema->find('list',compact('conditions','order'));

		echo '<option value="">Selecione um Problema</option>';
		foreach ($lista as $key => $value) {
			echo '<option value="'.$key.'">'.$value.'</option>';
		}

		exit();	
	}

	function listar_por_tipo($tpro_codigo){
		$lista = $this->TProbProblema->listarPorTipo($tpro_codigo);
		$this->set(compact('lista'));
	}

}
?>