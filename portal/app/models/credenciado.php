<?php
class Credenciado extends AppModel {
	
	public $name = 'Credenciado';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'fornecedores';
	public $primaryKey = 'codigo';
	public $displayField = 'nome';

	const PENDENTE = 'Pendente';
	const PAGAMENTO_BLOQUEADO = 'Pagamento Bloqueado';
	const PAGAMENTO_LIBERADO = 'Liberado para Pagamento';


	public function listaStatusDeAuditoriaExames(){
		return array(
			Credenciado::PENDENTE,
			Credenciado::PAGAMENTO_BLOQUEADO,
			Credenciado::PAGAMENTO_LIBERADO,
		);
	}	
	
	public function paginate( $conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array() ) {

		$joins = null;
		if (isset($extra['joins'])) {
			$joins = $extra['joins'];
		}
		if (isset($extra['group'])) {
			$group = $extra['group'];
		}
		
		return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
	}


	function obterLista( $fileds, $conditions ) {
		
		return $this->find('all', array('fields' => $fields,'conditions' => $conditions));
		

		$query = "select codigo, codigo_documento, nome, razao_social, ativo from fornecedores where codigo_documento LIKE '".$q."%'";
		
		$dados = $this->query($query);
		
		return $dados;
	}

	function buscaFornecedorPorDocumento( $q, $limit = null ) {
		// query feita assim por esta model trazer pedidos		
		$query = "select codigo, codigo_documento, nome, razao_social, ativo from fornecedores where codigo_documento LIKE '".$q."%'";
		
		$dados = $this->query($query);
		
		return $dados;
	}

	function buscaFornecedorPorCodigo( $q, $limit = null ) {
		// query feita assim por esta model trazer pedidos		
		$query = "select codigo, codigo_documento, nome, razao_social, ativo from fornecedores where codigo LIKE '".$q."%'";
		
		$dados = $this->query($query);
		
		return $dados;
	}

	function buscaFornecedorPorRazaoSocial( $q, $limit = null ) {


		// Vamos remover o hasMany...
		$this->Fornecedor->unbindModel(
			array('hasMany' => array('ItemPedidoExame'))
		);

		$this->Fornecedor->unbindModel(
			array('hasAndBelongsToMany' => array('Medico'))
		);

		$this->paginate['Fornecedor'] = array(
            'fields' => array(  'codigo',
                                'codigo_documento',
                                'nome',
                                '( SELECT 1 FROM RHHealth_vendas.dbo.empresa WHERE cnpj COLLATE Latin1_General_CI_AS = Corretora.codigo_documento ) AS acesso ' ) ,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'Corretora.nome',
        );

        //pr( $this->Corretora->find('sql', $this->paginate['Corretora'] ) );

        $corretoras = $this->paginate('Corretora');


		// query feita assim por esta model trazer pedidos		
		$query = "select codigo, codigo_documento, nome, razao_social, ativo from fornecedores where razao_social LIKE '%".$q."%'";
		
		$dados = $this->query($query);
		
		return $dados;
	}
}
