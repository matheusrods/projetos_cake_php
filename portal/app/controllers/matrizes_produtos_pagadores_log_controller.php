<?php
class MatrizesProdutosPagadoresLogController extends AppController {
	public $name = 'MatrizesProdutosPagadoresLog';
	public $uses = array('MatrizProdutoPagadorLog');
	
	function listagem(){
		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, 'MatrizFilialLog');
		if(!empty($filtros)){
			$conditions = $this->MatrizProdutoPagadorLog->converteFiltrosEmConditions($filtros);
			if(!empty($conditions)){
				$matrizes_produtos_pagadores = $this->MatrizProdutoPagadorLog->listar($conditions);
				$this->set(compact('matrizes_produtos_pagadores'));
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
