<?php

class ConfiguracaoComissao extends AppModel {

	var $name = 'ConfiguracaoComissao';
	var $tableSchema = 'vendas';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'configuracao_comissoes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_configuracao_comissoes'));
	var $validate = array(
		'codigo_endereco_regiao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe uma filial'
		),
		'codigo_produto' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Informe um produto',
			),
			array(
				'rule' => 'combinacaoUnica',
				'message' => 'Já está configurado nesta filial',
			),
		),
		'percentual' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o percentual de comissão'
			),
			'comissaoMax' => array(
				'rule' => 'comissaoMax',
				'message' => 'Percentual de comissão invalida'
			),
		),
	);

	protected function combinacaoUnica() {
		$conditions = array(
			'codigo_endereco_regiao' => $this->data[$this->name]['codigo_endereco_regiao'],
			'codigo_produto' => $this->data[$this->name]['codigo_produto'],
			'regiao_tipo_faturamento' => $this->data[$this->name]['regiao_tipo_faturamento'],
		);
		if (isset($this->data[$this->name]['codigo']) && !empty($this->data[$this->name]['codigo'])) {
			$conditions['not'] = array('codigo' => $this->data[$this->name]['codigo']);
		}
		return $this->find('count', compact('conditions')) == 0;
	}

	function comissaoMax(){
		return (($this->data[$this->name]['percentual'] <= 100) && ($this->data[$this->name]['percentual'] >= 0));
	}

	public function convertFiltroEmParametros($filtro){
		$EnderecoRegiao		=& ClassRegistry::Init('EnderecoRegiao');
		$NProduto			=& ClassRegistry::Init('NProduto');

		$joins = array(
			array(
				'table' 	=> $EnderecoRegiao->databaseTable.'.'.$EnderecoRegiao->tableSchema.'.'.$EnderecoRegiao->useTable,
				'alias'		=> 'EnderecoRegiao',
				'conditions'=> 'EnderecoRegiao.codigo = ConfiguracaoComissao.codigo_endereco_regiao',
			),
			array(
				'table' 	=> $NProduto->databaseTable.'.'.$NProduto->tableSchema.'.'.$NProduto->useTable,
				'alias'		=> 'NProduto',
				'conditions'=> 'NProduto.codigo = ConfiguracaoComissao.codigo_produto_naveg',
			),

		);

		if(!empty($filtro['codigo_endereco_regiao']))$conditions['codigo_endereco_regiao'] 	= $filtro['codigo_endereco_regiao'];
		if(!empty($filtro['codigo_produto_naveg']))$conditions['codigo_produto_naveg'] = $filtro['codigo_produto_naveg'];

		$limit 		= 50;
		$order 		= array('EnderecoRegiao.descricao');
		$fields 	= array(
			'ConfiguracaoComissao.*',
			'EnderecoRegiao.descricao',
			'NProduto.descricao',
		);

		return compact('conditions','joins','limit','order','fields');
	}

	function incluir($data){
		if(isset($data[$this->name]['percentual']) && $data[$this->name]['percentual'])
			$data[$this->name]['percentual'] = str_replace(',', '.', str_replace('.', '', $data[$this->name]['percentual']));

		return parent::incluir($data);
	}

	function atualizar($data){
		if(isset($data[$this->name]['percentual']) && $data[$this->name]['percentual'])
			$data[$this->name]['percentual'] = str_replace(',', '.', str_replace('.', '', $data[$this->name]['percentual']));
			
		return parent::atualizar($data);
	}
	
}

?>
