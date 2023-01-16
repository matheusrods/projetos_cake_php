<?php
class ListaDePrecoProdutoServico extends AppModel {
	var $name = 'ListaDePrecoProdutoServico';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'listas_de_preco_produto_servico';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_lista_de_preco_produto_servico'));
	var $belongsTo = array(
		'ListaDePrecoProduto' => array('foreignKey' => 'codigo_lista_de_preco_produto'),
		'Servico' => array('foreignKey' => 'codigo_servico'),
	);
	var $validate = array(
		'codigo_lista_de_preco_produto' => array(
			'rule' => 'notEmpty',
			'message' => 'Produto não informado',
			'required' => true,
			'allowEmpty' => false,
		),
		'codigo_servico' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Serviço não informado',
				'required' => true,
				'allowEmpty' => false,
			),
			array(
				'rule' => 'unico',
				'message' => 'Já existe este serviço para o produto',
				'required' => true,
				'allowEmpty' => false,
			),
		)
	);

	const TIPO_PREMIO_MINIMO_PRODUTO = 1;
	const TIPO_PREMIO_MINIMO_SERVICO = 2;

	public function bindPropostaLimiteDesconto() {
		$this->bindModel(
			Array(
				'hasOne' => Array(
					'PropostaLimiteDesconto' => Array(
						'foreignKey' => false,
						'conditions' => Array(
							'ListaDePrecoProduto.codigo_produto = PropostaLimiteDesconto.codigo_produto',
							'ListaDePrecoProdutoServico.codigo_servico = PropostaLimiteDesconto.codigo_servico',
							"PropostaLimiteDesconto.ativo = 'S' "
						)
					)
				)
			)
		);
	}

	protected function unico() {
		$conditions = array(
			'codigo_lista_de_preco_produto' => $this->data[$this->name]['codigo_lista_de_preco_produto'], 
			'codigo_servico' => $this->data[$this->name]['codigo_servico']
		);
		if (isset($this->data[$this->name]['codigo']) && !empty($this->data[$this->name]['codigo']))
			$conditions['NOT'] = array($this->name.'.codigo' => $this->data[$this->name]['codigo']);
		return ($this->find('count', compact('conditions')) == 0);
	}

    function valida_valor_maior_que_zero($check){
    	return($this->ajustaFormatacao($check['valor'])>0);
    }

    function ajustaFormatacao($valor){
		if (strpos($valor, '.')>0 && strpos($valor, ',')>0) {
    		$valor = str_replace('.', '', $valor);
    	}
		return str_replace(',', '.', $valor);
    }


	function ajustarValoresMinimosServicos($dados) {

		$dados =$this->find('all',array('conditions'=> array('codigo_lista_de_preco' => $dados['ListaDePrecoProdutoServico']['codigo_lista_de_preco'],'codigo_produto' => $dados['ListaDePrecoProdutoServico']['codigo_produto'])));

		foreach($dados as $lista_de_preco_ps) {
			$lista_de_preco_ps['ListaDePrecoProdutoServico']['valor_premio_minimo'] = 0;
			$lista_de_preco_ps['ListaDePrecoProdutoServico']['qtd_premio_minimo'] = 0;
			$lista_de_preco_ps['ListaDePrecoProdutoServico']['tipo_premio_minimo'] = self::TIPO_PREMIO_MINIMO_PRODUTO;
			unset($lista_de_preco_ps['ListaDePrecoProduto']);
			unset($lista_de_preco_ps['Servico']);
			$this->atualizar($lista_de_preco_ps);
			
		}


	}

	function incluir($dados) {
		try {
			$this->ajustaDados($dados);
			$this->query('BEGIN TRANSACTION');
			$lista_de_preco_produto = array('ListaDePrecoProduto' => array(
				'codigo_lista_de_preco' => $dados['ListaDePrecoProdutoServico']['codigo_lista_de_preco'],
				'codigo_produto' => $dados['ListaDePrecoProdutoServico']['codigo_produto'],
				'valor_premio_minimo' => 0,
				'qtd_premio_minimo' => 0,
			));

			if ($dados['ListaDePrecoProdutoServico']['tipo_premio_minimo'] == self::TIPO_PREMIO_MINIMO_PRODUTO) {
				$lista_de_preco_produto['ListaDePrecoProduto']['valor_premio_minimo'] = $dados['ListaDePrecoProdutoServico']['valor_premio_minimo'];
				$lista_de_preco_produto['ListaDePrecoProduto']['qtd_premio_minimo'] = $dados['ListaDePrecoProdutoServico']['qtd_premio_minimo'];
				$dados['ListaDePrecoProdutoServico']['valor_premio_minimo'] = 0;
				$dados['ListaDePrecoProdutoServico']['qtd_premio_minimo'] = 0;

				$this->ajustarValoresMinimosServicos($dados);
			}

			if(!$this->ListaDePrecoProduto->gravar($lista_de_preco_produto)) {
				$this->ListaDePrecoProduto->find('first',array('conditions' => array('codigo_produto' => $lista_de_preco_produto['ListaDePrecoProduto']['codigo_produto'], 'codigo_lista_de_preco' => $lista_de_preco_produto['ListaDePrecoProduto']['codigo_lista_de_preco'])));
			
			}
			$dados['ListaDePrecoProdutoServico']['codigo_lista_de_preco_produto'] = $this->ListaDePrecoProduto->id;
			
			if (!parent::incluir($dados)) throw new Exception("Error gravando Servico", 1);
			$this->commit();
			return true;
		} catch (Exception $ex) {
			$this->rollback();
			return false;
		}
	}

	private function ajustaDados(&$dados) {
		if ($dados[$this->name]['valor'] == null) $dados[$this->name]['valor'] = 0;
		if ($dados[$this->name]['valor_maximo'] == null) $dados[$this->name]['valor_maximo'] = 0;
		if ($dados[$this->name]['valor_venda'] == null) $dados[$this->name]['valor_venda'] = 0;
		if ($dados[$this->name]['valor_premio_minimo'] == null) $dados[$this->name]['valor_premio_minimo'] = 0;
		if ($dados[$this->name]['qtd_premio_minimo'] == null) $dados[$this->name]['qtd_premio_minimo'] = 0;
	}

	function excluir($codigo) {
		try {
			$this->query('BEGIN TRANSACTION');
			$lista_de_preco_produto_servico = $this->carregar($codigo);
			$codigo_lista_de_preco_produto = $lista_de_preco_produto_servico[$this->name]['codigo_lista_de_preco_produto'];
			if (!parent::excluir($codigo)) throw new Exception("Error ao excluir", 1);
			$conditions = array('codigo_lista_de_preco_produto' => $codigo_lista_de_preco_produto);
			if ($this->find('count', compact('conditions')) == 0) {
				if (!$this->ListaDePrecoProduto->excluir($codigo_lista_de_preco_produto)) throw new Exception("Error ao excluir produto", 1);				
			}
			$this->commit();
			return true;
		} catch (Exception $ex) {
			$this->rollback();
			return false;
		}
	}

	function carregar($codigo) {
		return $this->find('first', array('conditions' => array($this->name.'.codigo' => $codigo), 'recursive' => 2));
	}

	function carregarPorListaProdutoServico($codigo_lista_de_preco, $codigo_produto, $codigo_servico) {
		$this->bindModel(array('belongsTo' => array(
			'ListaDePrecoProduto' => array('foreignKey' => 'codigo_lista_de_preco_produto')
		)));
		$conditions = array(
			'ListaDePrecoProduto.codigo_lista_de_preco' => $codigo_lista_de_preco,
			'ListaDePrecoProduto.codigo_produto' => $codigo_produto,
			'codigo_servico' => $codigo_servico
		);
		return $this->find('first', compact('conditions'));
	}

	//metodo comentado para que a atualizacao seja tratada pela controller.. este metodo era utilizado somente na tela de edicao, porem essa tela nao existe mais. ajuste para o chamado CDCT-231
	
	// function atualizar($dados) {

	// 	try {

	// 		$this->query('BEGIN TRANSACTION');

	// 		$lista_de_preco_produto_servico = $this->carregar($dados[$this->name]['codigo']);
	// 		$lista_de_preco_produto = $this->ListaDePrecoProduto->carregar($lista_de_preco_produto_servico[$this->name]['codigo_lista_de_preco_produto']);
	// 		$lista_de_preco_produto_servico[$this->name]['valor'] = $dados[$this->name]['valor'];
	// 		$lista_de_preco_produto_servico[$this->name]['tipo_atendimento'] = $dados[$this->name]['tipo_atendimento'];
	// 		$lista_de_preco_produto_servico[$this->name]['valor_maximo'] = $dados[$this->name]['valor_maximo'];
	// 		$lista_de_preco_produto_servico[$this->name]['valor_venda'] = $dados[$this->name]['valor_venda'];			

	// 		if (isset($dados[$this->name]['tipo_premio_minimo']) && $dados[$this->name]['tipo_premio_minimo'] == self::TIPO_PREMIO_MINIMO_PRODUTO) {			
	// 			$lista_de_preco_produto['ListaDePrecoProduto']['valor_premio_minimo'] = $dados[$this->name]['valor_premio_minimo'];
	// 			$lista_de_preco_produto['ListaDePrecoProduto']['qtd_premio_minimo'] = $dados[$this->name]['qtd_premio_minimo'];
	// 			$lista_de_preco_produto_servico['ListaDePrecoProdutoServico']['valor_premio_minimo'] = 0;
	// 			$lista_de_preco_produto_servico['ListaDePrecoProdutoServico']['qtd_premio_minimo'] = 0;
	// 		} else {		
	// 			$lista_de_preco_produto_servico['ListaDePrecoProdutoServico']['valor_premio_minimo'] = $dados[$this->name]['valor_premio_minimo'];
	// 			$lista_de_preco_produto_servico['ListaDePrecoProdutoServico']['qtd_premio_minimo'] = $dados[$this->name]['qtd_premio_minimo'];
	// 		}
			
	// 		if (!$this->atualizar($lista_de_preco_produto_servico)) {
	// 			throw new Exception("Error ao gravar serviço", 1);
	// 		}
	// 		if (!$this->ListaDePrecoProduto->atualizar($lista_de_preco_produto)) {
	// 			throw new Exception("Error ao gravar produto", 1);	
	// 		}

	// 		$this->commit();
			
	// 		return true;
	// 	} catch (Exception $ex) {
			
	// 		$this->rollback();
			
	// 		return false;
	// 	}
	// }

	function servicosPorProdutoEListaDePreco( $lista_de_preco, $produto ){

        $fields = array(
        	'ListaDePrecoProdutoServico.codigo',
    		'Servico.codigo',
    		'Servico.descricao',
    		'ListaDePrecoProdutoServico.valor',
    	);

    	$conditions	= array(
    		'ListaDePrecoProduto.codigo_lista_de_preco' => $lista_de_preco,
    		'ListaDePrecoProduto.codigo_produto' => $produto,
    	);
    	$this->bindModel(array(
    			'belongsTo' => array(
    					'ProdutoServico' => array(
    							'class' => 'ProdutoServico',
    							'type' => 'INNER',
    							'foreignKey' => false,
    							'conditions' => array(
    									'ProdutoServico.codigo_produto = ListaDePrecoProduto.codigo_produto',
    									'ProdutoServico.codigo_servico = Servico.codigo',
    									'ProdutoServico.ativo' => 1,
    								)
    						)
    				)
    		)
    	);
        $result = $this->find('all',compact('conditions','fields'));

        return $result;
	}

	function servicosPorProduto( $produto,  $codigo_corretora = null, $codigo_seguradora = null, $limites_desconto = false ){
		$this->ListaDePreco =& ClassRegistry::init('ListaDePreco');
		$dados_lista = $this->ListaDePreco->porSeguradoraCorretora($codigo_corretora, $codigo_seguradora);
		if ($dados_lista) $codigo_lista_de_preco = $dados_lista['ListaDePreco']['codigo'];

        /*$fields = array(
        	'ListaDePrecoProdutoServico.codigo',
    		'Servico.codigo',
    		'Servico.descricao',
    		'ListaDePrecoProdutoServico.valor',
    	);*/
		if ($limites_desconto) {
			$this->bindPropostaLimiteDesconto();
		}


    	$conditions	= array(
    		'ListaDePrecoProduto.codigo_lista_de_preco' => $codigo_lista_de_preco,
    		'ListaDePrecoProduto.codigo_produto' => $produto,
    	);

        $result = $this->find('all',compact('conditions','fields'));

        return $result;
	}	

}
?>