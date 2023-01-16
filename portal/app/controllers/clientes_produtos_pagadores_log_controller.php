<?php
class ClientesProdutosPagadoresLogController extends AppController {
	public $name = 'ClientesProdutosPagadoresLog';
	public $uses = array('ClienteProdutoPagadorLog');
	

	function listagem(){
		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, "EmbarcadorTransportadorLog");
		if(!empty($filtros)){
			$conditions = $this->ClienteProdutoPagadorLog->converteFiltrosEmConditions($filtros);
			if(!empty($conditions)){
				$clientes_produtos_pagadores_log = $this->ClienteProdutoPagadorLog->listar($conditions);
				$this->set(compact('clientes_produtos_pagadores_log')); 
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
