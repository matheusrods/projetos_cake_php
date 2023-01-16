<?php
class MatrizesFiliaisLogController extends AppController {
	public $name = 'MatrizesFiliaisLog';
	public $uses = array('MatrizFilialLog');
	
	function index(){
		$this->pageTitle = 'Log Matrizes Filiais';
		$filtros = $this->Filtros->controla_sessao($this->data, $this->MatrizFilialLog->name);
		$this->data[$this->MatrizFilialLog->name] = $filtros;
	}

	function listagem(){
		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, $this->MatrizFilialLog->name);
		if(!empty($filtros)){
			$conditions = $this->MatrizFilialLog->converteFiltrosEmConditions($filtros);
			if(!empty($conditions)){
				$matrizes_filiais = $this->MatrizFilialLog->listar($conditions);
				$this->set(compact('matrizes_filiais'));
			}else{
				$preencher = true;
				$this->set(compact('preencher'));
			}
		}else{
			$preencher = true;
			$this->set(compact('preencher'));
		}
	}
	
}
?>
