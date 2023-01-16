<?php

class ConfiguracaoComissaoCorre extends AppModel {

	var $name = 'ConfiguracaoComissaoCorre';
	var $tableSchema = 'vendas';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'configuracao_comissoes_corretora';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure','Loggable'=>array('foreign_key'=>'codigo_conf_comissoes_corretora'));
	var $validate = array(
		'codigo_corretora' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe uma corretora'
		),
		'codigo_produto' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Informe um produto',
			),
		),
		'codigo_servico' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Informe um serviço',
			),
			'configExiste' => array(
				'rule' => 'configExiste',
				'message' => 'A configuração já existe'
			),
		),
		'percentual_impostos' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o percentual de impostos'
			),
			'impostosMax' => array(
				'rule' => 'impostosMax',
				'message' => 'Percentual de impostos inválido'
			),
		),
		'percentual_comissao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o percentual de comissão'
			),
			'comissaoMax' => array(
				'rule' => 'comissaoMax',
				'message' => 'Percentual de comissão inválido'
			),
		),
		'preco_de' => array(
			'maiorPrecoDe' => array(
				'rule' => 'maiorPrecoDe',
				'message' => 'O preço Até deve ser maior que o preço De'
			),
			'intervaloInvalido' => array(
				'rule' => 'intervaloInvalido',
				'message' => 'O intervalo de preço já existe'
			),
			'maxLength' => array(
		        'rule' => array('maxLength', 13),
		        'message' => 'O valor máximo é 99.999.999,99',
	        )
		),
		'preco_ate' => array(
			'maxLength' => array(
		        'rule' => array('maxLength', 13),
		        'message' => 'O valor máximo é 99.999.999,99',
	        )
		),
	);

	function impostosMax(){
		return (($this->data[$this->name]['percentual_impostos'] <= 100) && ($this->data[$this->name]['percentual_impostos'] >= 0));
	}

	function comissaoMax(){
		return (($this->data[$this->name]['percentual_comissao'] <= 100) && ($this->data[$this->name]['percentual_comissao'] >= 0));
	}

	function maiorPrecoDe(){
		if($this->data[$this->name]['verificar_preco_unitario'])
			return ($this->data[$this->name]['preco_ate'] > $this->data[$this->name]['preco_de']);
		return true;
	}

	function intervaloInvalido(){
		$conditions = array(
			'codigo_corretora' => $this->data[$this->name]['codigo_corretora'],
			'codigo_produto' => $this->data[$this->name]['codigo_produto'],
			'codigo_servico' => $this->data[$this->name]['codigo_servico'],
			'OR' => array(
				"preco_de <= {$this->data[$this->name]['preco_de']} AND preco_ate >= {$this->data[$this->name]['preco_de']}",
				"preco_de <= {$this->data[$this->name]['preco_ate']} AND preco_ate >= {$this->data[$this->name]['preco_ate']}",
			),
		);
		if(isset($this->data[$this->name]['codigo']) && !empty($this->data[$this->name]['codigo']))
			$conditions[] = 'codigo <> '.$this->data[$this->name]['codigo'];

		if($this->data[$this->name]['verificar_preco_unitario']){
			$retorno = $this->find('count',array(
				'conditions' => $conditions,
			));
			return $retorno == 0;
		}
		return true;
	}

	function configExiste(){
		$conditions = array(
			'codigo_corretora' => $this->data[$this->name]['codigo_corretora'],
			'codigo_produto' => $this->data[$this->name]['codigo_produto'],
			'codigo_servico' => $this->data[$this->name]['codigo_servico'],
		);
		if(isset($this->data[$this->name]['codigo']) && !empty($this->data[$this->name]['codigo']))
			$conditions[] = 'codigo <> '.$this->data[$this->name]['codigo'];

		if(!$this->data[$this->name]['verificar_preco_unitario']){
			$retorno = $this->find('count',array(
				'conditions' => $conditions,
			));
			return $retorno == 0;
		}
		return true;
	}

	public function bindCorretora(){
		$this->bindModel(array(
			'belongsTo' => array(
				'Corretora' => array(
					'class' 	=> 'Corretora',
					'foreignKey'=> 'codigo_corretora'
				),
			),
		));
	}

	public function convertFiltroEmParametros($filtro){
		$Corretora =& ClassRegistry::Init('Corretora');
		$Produto =& ClassRegistry::Init('Produto');
		$Servico =& ClassRegistry::Init('Servico');

		$joins = array(
			array(
				'table' 	=> $Corretora->databaseTable.'.'.$Corretora->tableSchema.'.'.$Corretora->useTable,
				'alias'		=> 'Corretora',
				'conditions'=> 'Corretora.codigo = ConfiguracaoComissaoCorre.codigo_corretora',
			),
			array(
				'table' 	=> $Produto->databaseTable.'.'.$Produto->tableSchema.'.'.$Produto->useTable,
				'alias'		=> 'Produto',
				'conditions'=> 'Produto.codigo = ConfiguracaoComissaoCorre.codigo_produto',
			),
			array(
				'table' 	=> $Servico->databaseTable.'.'.$Servico->tableSchema.'.'.$Servico->useTable,
				'alias'		=> 'Servico',
				'conditions'=> 'Servico.codigo = ConfiguracaoComissaoCorre.codigo_servico',
			),
		);

		if(!empty($filtro['codigo_corretora']))$conditions['codigo_corretora'] 	= $filtro['codigo_corretora'];
		if(!empty($filtro['codigo_produto']))$conditions['codigo_produto'] = $filtro['codigo_produto'];
		if(!empty($filtro['codigo_servico']))$conditions['codigo_servico'] = $filtro['codigo_servico'];
		if(!empty($filtro['verificar_preco_unitario']))$conditions['verificar_preco_unitario'] = ($filtro['verificar_preco_unitario'] == '2' ? '0' : '1');

		$limit 		= 50;
		$order 		= array('Corretora.nome','Produto.descricao','Servico.descricao','ConfiguracaoComissaoCorre.preco_de');
		$fields 	= array(
			'ConfiguracaoComissaoCorre.*',
			'Corretora.nome',
			'Produto.descricao',
			'Servico.descricao',
		);

		return compact('conditions','joins','limit','order','fields');
	}

	function incluir($data){
		if(isset($data[$this->name]['percentual_impostos']) && $data[$this->name]['percentual_impostos'])
			$data[$this->name]['percentual_impostos'] = str_replace(',', '.', str_replace('.', '', $data[$this->name]['percentual_impostos']));
		if(isset($data[$this->name]['percentual_comissao']) && $data[$this->name]['percentual_comissao'])
			$data[$this->name]['percentual_comissao'] = str_replace(',', '.', str_replace('.', '', $data[$this->name]['percentual_comissao']));
		if(isset($data[$this->name]['verificar_preco_unitario']) && $data[$this->name]['verificar_preco_unitario']){
			if(isset($data[$this->name]['preco_de']) && $data[$this->name]['preco_de'])
				$data[$this->name]['preco_de'] = str_replace(',', '.', str_replace('.', '', $data[$this->name]['preco_de']));
			else
				$data[$this->name]['preco_de'] = 0;
			if(isset($data[$this->name]['preco_ate']) && $data[$this->name]['preco_ate'])
				$data[$this->name]['preco_ate'] = str_replace(',', '.', str_replace('.', '', $data[$this->name]['preco_ate']));
			else
				$data[$this->name]['preco_ate'] = 0;
		}else{
			$data[$this->name]['preco_de'] = 0;
			$data[$this->name]['preco_ate'] = 0;
		}
		
		return parent::incluir($data);
	}

	function atualizar($data){
		if(isset($data[$this->name]['percentual_impostos']) && $data[$this->name]['percentual_impostos'])
			$data[$this->name]['percentual_impostos'] = str_replace(',', '.', str_replace('.', '', $data[$this->name]['percentual_impostos']));
		if(isset($data[$this->name]['percentual_comissao']) && $data[$this->name]['percentual_comissao'])
			$data[$this->name]['percentual_comissao'] = str_replace(',', '.', str_replace('.', '', $data[$this->name]['percentual_comissao']));
		if(isset($data[$this->name]['verificar_preco_unitario']) && $data[$this->name]['verificar_preco_unitario']){
			if(isset($data[$this->name]['preco_de']) && $data[$this->name]['preco_de'])
				$data[$this->name]['preco_de'] = str_replace(',', '.', str_replace('.', '', $data[$this->name]['preco_de']));
			if(isset($data[$this->name]['preco_ate']) && $data[$this->name]['preco_ate'])
				$data[$this->name]['preco_ate'] = str_replace(',', '.', str_replace('.', '', $data[$this->name]['preco_ate']));
		}else{
			$data[$this->name]['preco_de'] = 0;
			$data[$this->name]['preco_ate'] = 0;
		}

		return parent::atualizar($data);
	}
	
}

?>
