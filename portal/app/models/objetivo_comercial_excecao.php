<?php
class ObjetivoComercialExcecao extends AppModel {
	var $name          = 'ObjetivoComercialExcecao';
	var $tableSchema   = 'vendas';
	var $databaseTable = 'dbBuonny';
	var $useTable      = 'objetivos_comerciais_excecoes';
	var $primaryKey    = 'codigo';
  	var $actsAs        = array('Secure');
  	var $validate = array(  		
		'codigo_cliente' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o cliente',
			),
			'combinacao_unica' => array(
				'rule' => 'combinacao_unica',
				'message' => '',
			),	
		),
		'codigo_produto' => array(			
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Produto',
			),
			'combinacao_unica' => array(
				'rule' => 'combinacao_unica',
				'message' => 'Já possui configuração para o este cliente e produto.',
			),
		),
		'codigo_gestor1' => array(			
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Gestor',
			),
		
		),		
		'codigo_gestor2' => array(			
			'combinacao_gestores' => array(
				'rule' => 'combinacao_gestores',
				'message' => 'Gestor já selecionado.',
			),			
			
		),
		'percentagem_gestor1' => array(			
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a porcentagem',
			),
		),
		'percentagem_gestor2' => array(			
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a porcentagem',
			),
		),
	);

	public function combinacao_unica() {
		$conditions = array(
			'codigo_cliente' => $this->data[$this->name]['codigo_cliente'],
			'codigo_produto' => $this->data[$this->name]['codigo_produto'],
		);
		$combinacao = $this->find('count', compact('conditions'));
		
		if($combinacao <= 1){
			return TRUE;
		}

		return $this->find('count', compact('conditions')) == 0;
	}

	public function combinacao_gestores(){
		if($this->data[$this->name]['codigo_gestor1'] == $this->data[$this->name]['codigo_gestor2']){
			return FALSE;
		}		
		return TRUE;
	}
	
	public function retorna_excecoes_por_codigo_pai($codigo_pai,$agrupa_gestores_iguais = TRUE){
		$this->bindModel(array(
            'hasOne' => array(
                'Usuario' => array(
                    'foreignKey' => false,
                    'conditions' => array('codigo_gestor = Usuario.codigo')
            	)
            )	
        ),FALSE);   
		
		$fields = array(
			'ObjetivoComercialExcecao.codigo',
			'ObjetivoComercialExcecao.codigo_produto',
			'ObjetivoComercialExcecao.codigo_cliente',
			'ObjetivoComercialExcecao.codigo_gestor',
			'ObjetivoComercialExcecao.codigo_usuario_inclusao',
			'ObjetivoComercialExcecao.codigo_pai',
			'ObjetivoComercialExcecao.data_inclusao',
			'ObjetivoComercialExcecao.percentagem_gestor',
			'ObjetivoComercialExcecao.gestor_produto',
			'Usuario.nome',
		);
		
		$listagem = $this->find('all',array(
			'conditions' => array(
				'codigo_pai' => $codigo_pai
			),
			'fields' => $fields
		));

		if($agrupa_gestores_iguais){	
			if(!empty($listagem) && count($listagem) < 2){
				$listagem[1]['ObjetivoComercialExcecao'] = $listagem[0]['ObjetivoComercialExcecao'];		
				$listagem[1]['Usuario']['nome'] = $listagem[0]['Usuario']['nome'];
			}
		}
		return $listagem;
	}

	function verifica_gestores_por_codigo_pai($codigo_pai){
		$listagem = $this->retorna_excecoes_por_codigo_pai($codigo_pai,FALSE);		
		foreach ($listagem as $key => $dado) {
			$codigo = $key+1;
			$dados['ObjetivoComercialExcecao']["codigo$codigo"] = $dado['ObjetivoComercialExcecao']["codigo"];
			$dados['ObjetivoComercialExcecao']["codigo_gestor$codigo"] = $dado['ObjetivoComercialExcecao']["codigo_gestor"];
			$dados['ObjetivoComercialExcecao']["percentagem_gestor$codigo"] = $dado['ObjetivoComercialExcecao']["percentagem_gestor"];
			$dados['ObjetivoComercialExcecao']["codigo_cliente"] = $dado['ObjetivoComercialExcecao']["codigo_cliente"];
			$dados['ObjetivoComercialExcecao']["codigo_produto"] = $dado['ObjetivoComercialExcecao']["codigo_produto"];
			$dados['ObjetivoComercialExcecao']["gestor_produto$codigo"] = $dado['ObjetivoComercialExcecao']["gestor_produto"];
			$dados['ObjetivoComercialExcecao']["codigo_pai"] = $codigo_pai;
		}
		return $dados;
	}

	public function converteFiltrosEmConditions($filtros){
		$conditions = array();
        if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])){
            $conditions['ObjetivoComercialExcecao.codigo_cliente'] = $filtros['codigo_cliente'];
        }
        if (isset($filtros['codigo_produto']) && !empty($filtros['codigo_produto'])){
            $conditions['ObjetivoComercialExcecao.codigo_produto'] = $filtros['codigo_produto'];
        }

        return $conditions;
	}
	

	public function calcula_faturamento_gestores($dados_excecao,$faturamento){
		$porcentagem_gestor1 = $dados_excecao[0]['ObjetivoComercialExcecao']['percentagem_gestor'];
		$porcentagem_gestor2 = $dados_excecao[1]['ObjetivoComercialExcecao']['percentagem_gestor'];

		$valor_faturado['faturamento1'] = 0;
		$valor_faturado['faturamento2'] = 0;

		if($porcentagem_gestor1 > 0 || $porcentagem_gestor2 > 0){
			$valor_gestor1 = $faturamento * $porcentagem_gestor1 /100;
			$valor_gestor2 = $faturamento - $valor_gestor1;
			$valor_faturado['faturamento1'] = $valor_gestor1;
			$valor_faturado['faturamento2'] = $valor_gestor2;
		}
		
		return $valor_faturado;
	}

	function verifica_excecoes_clientes($dado){
		$dados['ObjetivoComercialCliente'] = $dado[0];
		$this->ObjetivoComercialCliente = classRegistry::init('ObjetivoComercialCliente');
		$codigo_pai = $dados['ObjetivoComercialCliente']['codigo_cliente'].$dados['ObjetivoComercialCliente']['codigo_produto'];
		$excecao = $this->retorna_excecoes_por_codigo_pai($codigo_pai);
	
		if(!empty($excecao)){
			
			$this->ObjetivoComercialCliente->exclui_gestores_sem_excecao($dados);
			$valor_faturado = $this->calcula_faturamento_gestores($excecao,$dados['ObjetivoComercialCliente']['faturamento_realizado']);
			$novos_dados[0] = $dados['ObjetivoComercialCliente'];	
			$novos_dados[0]['faturamento_realizado'] = $valor_faturado['faturamento1'];
			$novos_dados[0]['nome_gestor'] = $excecao[0]['Usuario']['nome'];
			$novos_dados[0]['codigo_gestor'] = $excecao[0]['ObjetivoComercialExcecao']['codigo_gestor'];
			$novos_dados[0]['gestor_produto'] = $excecao[0]['ObjetivoComercialExcecao']['gestor_produto'];
			if($novos_dados[0]['gestor_produto'] != 1){
				$novos_dados[0]['cliente_novo'] = 0;
			}
			$novos_dados[0]['excecao'] = 1;
			
			unset($novos_dados[0]['gestor']);
			$novos_dados[1] = $dados['ObjetivoComercialCliente'];
			$novos_dados[1]['faturamento_realizado'] = $valor_faturado['faturamento2'];
			$novos_dados[1]['nome_gestor'] = $excecao[1]['Usuario']['nome'];
			$novos_dados[1]['codigo_gestor'] = $excecao[1]['ObjetivoComercialExcecao']['codigo_gestor'];
			$novos_dados[1]['gestor_produto'] = $excecao[1]['ObjetivoComercialExcecao']['gestor_produto'];
			if($novos_dados[1]['gestor_produto'] != 1){
				$novos_dados[1]['cliente_novo'] = 0;
			}
			$novos_dados[1]['excecao'] = 1;
			unset($novos_dados[1]['gestor']);
			unset($dados);
		}
		if(isset($novos_dados)){
			$dados['excecao'] = $novos_dados;
		}
		$novos_dados_convertidos[] = $dados;
		return $novos_dados_convertidos;
	}

	function paginateCount($conditions = null,$recursive = 0, $extra = array()) {
        $count = $this->find('count', compact('conditions', 'recursive'));
        return $count/2;
    }
	
	function agrupa_gestores_iguais($dados){
		if(isset($dados[1]['ObjetivoComercialExcecao']['codigo']) && ($dados[0]['ObjetivoComercialExcecao']['codigo'] == $dados[1]['ObjetivoComercialExcecao']['codigo'])){
			$dados[1]['ObjetivoComercialExcecao']['codigo_gestor'] = $dados[0]['ObjetivoComercialExcecao']['codigo_gestor'];
		}
		return $dados;
	}

	function excluir_objetivos_clientes_sem_excecao($dados){
		foreach ($dados as $key => $dado) {	
			$mesAnos = explode('-',$dado[0]['data_inclusao']);
			$dado[0]['mes'] = $mesAnos[0];
			$dado[0]['ano'] = $mesAnos[1];
			$this->ObjetivoComercialCliente = classRegistry::init('ObjetivoComercialCliente');	
			$filial = $dado['0']['filial'];
			$mes = $dado['0']['mes'];
			$ano = $dado['0']['ano'];
			$codigo = null;
			$codigo = $this->query("
				SELECT * FROM {$this->ObjetivoComercialCliente->databaseTable}.{$this->ObjetivoComercialCliente->tableSchema}.{$this->ObjetivoComercialCliente->useTable} as clientes
					WHERE clientes.excecao = 1  
					and clientes.mes = {$mes}
					and clientes.ano = {$ano}
					and clientes.codigo_endereco_regiao = {$filial}
					and NOT EXISTS (SELECT TOP 1 * FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} as excecoes
					WHERE
					    clientes.codigo_gestor = excecoes.codigo_gestor and clientes.codigo_produto = excecoes.codigo_produto and clientes.codigo_cliente = excecoes.codigo_cliente
					)
			");
			
			if(!empty($codigo)){
			 	if(!$this->ObjetivoComercialCliente->excluir($codigo[0][0]['codigo'])){
			 		return FALSE;
			 	}
			}
		}	
		
		return true;
	}

}
?>