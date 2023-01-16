<?php
class ListaDePrecoProduto extends AppModel {
	var $name = 'ListaDePrecoProduto';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'listas_de_preco_produto';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_lista_de_preco_produto'));
	var $belongsTo = array(
		'Produto' => array('foreignKey' => 'codigo_produto'),
	);

	var $validate = array(
		'codigo_lista_de_preco' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Lista de preços não informada',
				'required' => true,
				'allowEmpty' => false,
			),
		),
	);

	function gravar($dados) {

		$conditions = array('codigo_lista_de_preco' => $dados[$this->name]['codigo_lista_de_preco'], 'codigo_produto' => $dados[$this->name]['codigo_produto']);
		$lista_de_preco_produto = $this->find('first', compact('conditions'));
		if ($lista_de_preco_produto) {
			$dados[$this->name]['codigo'] = $lista_de_preco_produto[$this->name]['codigo'];
			return $this->atualizar($dados);
		} else {
			return $this->incluir($dados);
		}
	}

	function listarPorCodigoListaDePreco($codigo_lista_de_preco) {
		//monta a query corretamente
		$this->bindModel(array(
			'belongsTo' => array( 
				'ListaDePrecoProdutoServico' => array(
				'foreignKey' => false, 
				'type'       => 'INNER',
				'conditions' => array('ListaDePrecoProduto.codigo = ListaDePrecoProdutoServico.codigo_lista_de_preco_produto',)
				),

				'Servico' => array(
				'foreignKey' => false, 
				'type'       => 'LEFT',
				'conditions' => array('Servico.codigo = ListaDePrecoProdutoServico.codigo_servico',)
				),
			),
		));

		$conditions = array(
			'ListaDePrecoProduto.codigo_lista_de_preco' => $codigo_lista_de_preco,
			'Produto.ativo' => 1,
			//a pedido da demanda PC-709, foi pedido que mostre todos os produtos do servico do credenciado, nao somente o tipo servico E
			// "Servico.tipo_servico = 'E'"
		);

		$order = array('Produto.descricao', 'ListaDePrecoProduto.codigo_produto', 'Servico.descricao');
		
		$linhas = $this->find('all', array(
			//'joins' 			=> $joins,
			'conditions' 	=> $conditions,
			'order'			=> $order
		));
	
		$lista_preco_produto_servico = array();
		
		foreach($linhas as $key => $linha){
			//monta o array
			$lista_preco_produto_servico[$linha['ListaDePrecoProduto']['codigo_produto']]['Produto'] = $linha['Produto'];
			$lista_preco_produto_servico[$linha['ListaDePrecoProduto']['codigo_produto']]['ListaDePrecoProdutoServico'][$key] = $linha['ListaDePrecoProdutoServico'];
			$lista_preco_produto_servico[$linha['ListaDePrecoProduto']['codigo_produto']]['ListaDePrecoProdutoServico'][$key]['Servico'] = $linha['Servico'];
		}

		//pr($lista_preco_produto_servico);exit;

		return $lista_preco_produto_servico;
	}

	function listarPorCodigoListaDePrecoPadrao($codigo_lista_de_preco) {
		$this->ListaDePrecoProdutoServico = ClassRegistry::init('ListaDePrecoProdutoServico');
		$conditions = array('ListaDePrecoProduto.codigo_lista_de_preco' => $codigo_lista_de_preco,
			'Produto.ativo' => 1);
		$order = array('ListaDePrecoProduto.codigo_produto');
		$recursive = 2;
		$linhas = $this->find('all', compact('conditions', 'recursive', 'order'));
		
		foreach($linhas as $key => $linha){
			$this->ListaDePrecoProdutoServico->bindModel(array('belongsTo' => 
				array(
				'Servico' => array('foreignKey' => 'codigo_servico'),
				'ProdutoServico' => array(
					'foreignKey' => false, 
					'type' => 'INNER',
					'conditions' => 'ProdutoServico.codigo_produto = '.$linha['ListaDePrecoProduto']['codigo_produto'].' And 
					 ProdutoServico.codigo_servico = Servico.codigo And 
					 ProdutoServico.ativo = 1'),
			)));		
			$conditions = array(
					'ListaDePrecoProdutoServico.codigo_lista_de_preco_produto' => $linha['ListaDePrecoProduto']['codigo'],
					'Servico.ativo' => true
				);
			
			$retornos = $this->ListaDePrecoProdutoServico->find('all', compact('conditions'));			
			if(count($retornos) > 0){
				$lista_preco_produto_servico = array();
				foreach($retornos as $posicao => $retorno){
					$lista_preco_produto_servico[$posicao] = $retorno['ListaDePrecoProdutoServico'];
					$lista_preco_produto_servico[$posicao]['Servico'] = $retorno['Servico'];
					$lista_preco_produto_servico[$posicao]['ProdutoServico'] = $retorno['ProdutoServico'];
				}
				$linhas[$key]['ListaDePrecoProdutoServico'] = $lista_preco_produto_servico;
			}else{
				unset($linhas[$key]);
			}
		}		
		return $linhas;

	}


	function listarPorCodigoProduto($codigo_produto, $codigo_lista_de_preco = null, $codigo_corretora = null, $codigo_seguradora = null) {
		if (empty($codigo_lista_de_preco)) {
			$this->ListaDePreco =& ClassRegistry::init('ListaDePreco');
			$dados_lista = $this->ListaDePreco->porSeguradoraCorretora($codigo_corretora, $codigo_seguradora);
			if ($dados_lista) $codigo_lista_de_preco = $dados_lista['ListaDePreco']['codigo'];
		}

		$conditions = array(
			'ListaDePrecoProduto.codigo_lista_de_preco' => $codigo_lista_de_preco,
			'ListaDePrecoProduto.codigo_produto' => $codigo_produto
		);

		$order = array('ListaDePrecoProduto.codigo_produto');

		$this->bindModel(array('hasMany' => array(
			'ListaDePrecoProdutoServico' => array('foreignKey' => 'codigo_lista_de_preco_produto'),
		)));
		$recursive = 2;
		return $this->find('first', compact('conditions', 'recursive', 'order'));
	}	
}
?>