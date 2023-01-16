<?php
class PgaiPgAssociaItemsController extends AppController {
	var $name = 'PgaiPgAssociaItems';
	var $uses = array('TPgaiPgAssociaItem', 'TPaipPgAssociaItemParam', 'TPaiaPgAssociaItemAcao', 'TPitePgItem');

	function consulta_acoes() {
		$this->layout = 'new_window';
		$this->pageTitle = 'Consulta Ações';
		$pite_pg_item = $this->TPitePgItem->find('first', array('conditions' => array('pite_codigo' => $this->data['PgaiPgAssociaItem']['pgai_pite_codigo'])));
		$fields = array( 'paia_data_cadastro', 'paia_tempo_espera', 'paia_sequencia', 'paia_rma', 'TPgaiPgAssociaItem.pgai_pgpg_codigo', 'TPgaiPgAssociaItem.pgai_pite_codigo', 'TApadAcaoPadrao.apad_codigo', 'TApadAcaoPadrao.apad_descricao');
		$order = array('paia_sequencia');
		$conditions = array('pgai_pgpg_codigo' => $this->data['PgaiPgAssociaItem']['pgai_pgpg_codigo'], 'pgai_pite_codigo' => $this->data['PgaiPgAssociaItem']['pgai_pite_codigo']);
		$acoes = $this->TPaiaPgAssociaItemAcao->find('all', compact('fields', 'conditions', 'order'));
		$this->set(compact('parametros', 'acoes', 'pite_pg_item'));
	}

	function consulta_parametros() {
		$this->layout = 'new_window';
		$this->pageTitle = 'Consulta Parametros';
		$pite_pg_item = $this->TPitePgItem->find('first', array('conditions' => array('pite_codigo' => $this->data['PgaiPgAssociaItem']['pgai_pite_codigo'])));
		$fields = array( 'paip_sequencia', 'paip_valor', 'TPgaiPgAssociaItem.pgai_pgpg_codigo', 'TPgaiPgAssociaItem.pgai_pite_codigo', 'TPipaPgItemParametro.pipa_descricao', 'TTvalTipoValor.tval_descricao');
		$order = array('paip_sequencia');
		$conditions = array('pgai_pgpg_codigo' => $this->data['PgaiPgAssociaItem']['pgai_pgpg_codigo'], 'pgai_pite_codigo' => $this->data['PgaiPgAssociaItem']['pgai_pite_codigo']);
		$parametros = $this->TPaipPgAssociaItemParam->find('all', compact('fields', 'conditions', 'order'));
		$this->set(compact('parametros', 'acoes', 'pite_pg_item'));
	}
}
