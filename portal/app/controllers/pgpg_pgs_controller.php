<?php
class PgpgPgsController extends AppController {
	var $name = 'PgpgPgs';
	var $uses = array('TPgpgPg', 'TPtitPgTipoItem', 'TPgaiPgAssociaItem');

	function beforeFilter() {
   		parent::beforeFilter();
        $this->BAuth->allow('mostra_mensagem_pgr_logistico');
    }

	function consulta_pgr() {
		$this->loadModel('TRacsRegraAceiteSm');
		$this->layout = 'new_window';
		$this->pageTitle = 'Consulta PGR';
		if (!empty($this->data)) {
			$TPaipPgAssociaItemParam = ClassRegistry::init('TPaipPgAssociaItemParam');
			$TPaiaPgAssociaItemAcao = ClassRegistry::init('TPaiaPgAssociaItemAcao');
			$pgr = $this->TPgpgPg->carregar($this->data['TPgpgPg']['pgpg_codigo']);
			$exibir_racs = false;
			if (!empty($this->data['TRacsRegraAceiteSm']['racs_codigo'])) {
				$racs = $this->TRacsRegraAceiteSm->carregar($this->data['TRacsRegraAceiteSm']['racs_codigo']);
				$exibir_racs = true;
			}

			$conditions = array('pgai_pgpg_codigo' => $this->data['TPgpgPg']['pgpg_codigo']);
			$fields = array(
				'pgai_pite_codigo', 
				'TPitePgItem.pite_codigo', 
				'TPitePgItem.pite_descricao', 
				'TPitePgItem.pite_ptit_codigo',
				"(SELECT COUNT(1/1) FROM {$TPaipPgAssociaItemParam->useTable} WHERE paip_pgai_codigo = pgai_codigo) AS qtd_parametros",
				"(SELECT COUNT(1/1) FROM {$TPaiaPgAssociaItemAcao->useTable} WHERE paia_pgai_codigo = pgai_codigo) AS qtd_acoes",
			);
			$order = array('ptit_descricao');
			$pgai_pg_associa_items = $this->TPgaiPgAssociaItem->find('all', compact('conditions', 'fields', 'order'));
			$ptit_pg_tipo_items = $this->TPtitPgTipoItem->find('list', compact('order'));

		} else {
			$this->data = array('TPgpgPg' => array('data_inicial' => date('d/m/Y'), 'data_final' => date('d/m/Y')));
		}
		$this->set(compact('pgr', 'pgai_pg_associa_items', 'ptit_pg_tipo_items', 'racs','exibir_racs'));
	}

	function lista_clientes() {
		$this->layout = 'ajax';
		if (isset($this->params['named']['codigo_pgr'])) $this->data['TPgpgPg']['pgpg_codigo'] = $this->params['named']['codigo_pgr'];		
		$paginado = (isset($this->params['named']['page']));

		$this->paginate['TPgpgPg'] = array(
		   'limit' => 10,
		   'method' => 'lista_clientes_pgr',
		   'pgpg_codigo'=>$this->data['TPgpgPg']['pgpg_codigo'],
		);
		$clientes_pgr = $this->paginate('TPgpgPg');
		$arrayEmbarcadorTransportador = Array('E'=>'Embarcador','T'=>'Transportador');

		$this->set(compact('clientes_pgr','arrayEmbarcadorTransportador','paginado'));
	}

	function mostra_mensagem_pgr_logistico($transportador,$embarcador = null){
		if(empty($embarcador) || $embarcador == 'false'){
			$embarcador = 0;
		}

		$retorno = $this->TPgpgPg->verica_tipo_pgr($transportador,$embarcador);	    
		echo json_encode( $retorno );
		die();
	}
}