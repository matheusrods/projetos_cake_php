<?php
class ListaDePreco extends AppModel {
	var $name = 'ListaDePreco';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'listas_de_preco';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_lista_de_preco'));
	var $belongsTo = array(
		'Fornecedor' => array('foreignKey' => false,'conditions' => 'ListaDePreco.codigo_fornecedor = Fornecedor.codigo'),
	);
	var $validate = array(
		'descricao' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Nome da lista não informado',
				'required' => true,
				'allowEmpty' => false,
			),
			array(
				'rule' => 'unico',
				'message' => 'Já existe lista para este fornecdor',
				'required' => true,
				'allowEmpty' => false,
			),
		)
	);

	protected function unico() {
		$conditions = array('codigo_fornecedor' => $this->data[$this->name]['codigo_fornecedor']);
		if (isset($this->data[$this->name]['codigo']) && !empty($this->data[$this->name]['codigo']))
			$conditions['not'] = array('codigo' => $this->data[$this->name]['codigo']);
		return ($this->find('count', compact('conditions')) == 0);
	}

	function porFornecedor($codigo_fornecedor = NULL) {
		$conditions = array($this->name.'.codigo_fornecedor' => $codigo_fornecedor);
		$lista = $this->find('first', compact('conditions'));
		
		if (!$lista) {
			$conditions = array($this->name.'.codigo_fornecedor' => $codigo_fornecedor);
			$lista = $this->find('first', compact('conditions'));
		}
		if (!$lista) {
			$conditions = array($this->name.'.codigo_fornecedor' => null, $this->name.'.codigo_fornecedor' => null);
			$lista = $this->find('first', compact('conditions'));
		}
		if ($lista) 
			return $lista;
		return null;
	}
	
	function retornaMediaCidade($codigo_proposta) {
		
		$lista = $this->query("
			SELECT 
			    propostas_credenciamento_exames.codigo_exame as codigo_exame,
			    servico.descricao as descricao_exame,
			    (
			        SELECT 
			            (SUM(listas_de_preco_produto_servico.valor) / count(1))
			        FROM 
			            listas_de_preco_produto_servico
			            inner join listas_de_preco_produto on (listas_de_preco_produto.codigo = listas_de_preco_produto_servico.codigo_lista_de_preco_produto)
			            inner join listas_de_preco on (listas_de_preco.codigo = listas_de_preco_produto.codigo_lista_de_preco)
			            inner join fornecedores on (fornecedores.codigo = listas_de_preco.codigo_fornecedor)
			            inner join fornecedores_endereco on (fornecedores_endereco.codigo_fornecedor = fornecedores.codigo)
			        WHERE
			            listas_de_preco_produto_servico.codigo_servico = propostas_credenciamento_exames.codigo_exame
			            and fornecedores_endereco.estado_descricao = propostas_credenciamento_endereco.estado
						and fornecedores_endereco.cidade = propostas_credenciamento_endereco.cidade
			            and listas_de_preco.codigo_fornecedor is not null
			    ) as media_cidade
			FROM
			    propostas_credenciamento
			    inner join propostas_credenciamento_endereco on (propostas_credenciamento_endereco.codigo_proposta_credenciamento = propostas_credenciamento.codigo)
			    inner join propostas_credenciamento_exames on (propostas_credenciamento_exames.codigo_proposta_credenciamento = propostas_credenciamento.codigo)
			    inner join servico on (servico.codigo = propostas_credenciamento_exames.codigo_exame)
			WHERE
			    propostas_credenciamento.codigo = '{$codigo_proposta}'	
		");

		$retorno = array();
		if(count($lista)) {
			foreach($lista as $key => $campo) {
				if(isset($campo[0])) {
					$retorno[$campo[0]['codigo_exame']] = number_format(round($campo[0]['media_cidade'], 2), 2, ',', '.');
				}
			}
		}

		return $retorno;
	} 
	
	function converteFiltroEmCondition($data) {
		$conditions = array();
	
		if (!empty($data['codigo_fornecedor']))
			$conditions['ListaDepreco.codigo_fornecedor'] = $data['codigo_fornecedor'];
	
		return $conditions;
	}

	function retorna_lista_preco($codigo_fornecedor){
		//carrega models
		$this->ListaDePrecoProduto =& ClassRegistry::init('ListaDePrecoProduto');
		$this->ListaDePrecoProdutoServico =& ClassRegistry::init('ListaDePrecoProdutoServico');
		$this->Servico =& ClassRegistry::init('Servico');
		//joins
		$joins = array(
			array(
				'table' => 'listas_de_preco_produto',
				'alias' => 'ListaDePrecoProduto',
				'type' => 'LEFT',
				'conditions' => 'ListaDePreco.codigo = ListaDePrecoProduto.codigo_lista_de_preco',
				),
			array(
				'table' => 'listas_de_preco_produto_servico',
				'alias' => 'ListaDePrecoProdutoServico',
				'type' => 'LEFT',
				'conditions' => 'ListaDePrecoProduto.codigo = ListaDePrecoProdutoServico.codigo_lista_de_preco_produto',
				),
			array(
				'table' => 'servico',
				'alias' => 'Servico',
				'type' => 'LEFT',
				'conditions' => 'Servico.codigo = ListaDePrecoProdutoServico.codigo_servico',
			)
		);
		//where
		$conditions = array(
			'codigo_fornecedor' => $codigo_fornecedor,
		);
		//order by
		$order = array('Servico.descricao ASC');
		//fields
		$fields = array(
			'ListaDePreco.*',
			'ListaDePrecoProduto.*',
			'ListaDePrecoProdutoServico.*',
			'Servico.*'
		);
		//consulta
		$lista_preco = $this->find('all', array('fields' => $fields, 'conditions' => $conditions, 'joins' => $joins, 'order' => $order));
		//retorno
		return $lista_preco;
	}
}
?>