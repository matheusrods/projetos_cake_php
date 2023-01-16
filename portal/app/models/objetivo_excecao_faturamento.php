<?php
class ObjetivoExcecaoFaturamento extends AppModel {
	var $name          = 'ObjetivoExcecaoFaturamento';
	var $tableSchema   = 'vendas';
	var $databaseTable = 'dbBuonny';
	var $useTable      = 'objetivo_excecao_faturamento';
	var $primaryKey    = 'codigo';
  	var $actsAs        = array('Secure');

  	var $validate = array(  		
		'mes' => array(			
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Mês',
			),
			'combinacao_unica' => array(
				'rule' => 'combinacao_unica',
				'message' => '',
			),
			'combinacao_anual' => array(
				'rule' => 'combinacao_anual',
				'message' => '',
			),
			
		),
		'ano' => array(			
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Ano',
			),
			'combinacao_unica' => array(
				'rule' => 'combinacao_unica',
				'message' => '',
			),
			'combinacao_anual' => array(
				'rule' => 'combinacao_anual',
				'message' => '',
			),
		),
		'codigo_cliente' => array(			
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Cliente',
			),
			'combinacao_unica' => array(
				'rule' => 'combinacao_unica',
				'message' => '',
			),
			'combinacao_anual' => array(
				'rule' => 'combinacao_anual',
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
				'message' => 'Combinação existente',
			),
			'combinacao_anual' => array(
				'rule' => 'combinacao_anual',
				'message' => 'Já existe configuração para este periodo (1 ano)',
			),
		),
		'faturamento_medio' => array(			
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o faturamento',
			),
		),
	);

	function combinacao_unica() {
		$conditions = array(
			'mes' => $this->data[$this->name]['mes'],
			'ano' => $this->data[$this->name]['ano'],
			'codigo_cliente' => $this->data[$this->name]['codigo_cliente'],
			'codigo_produto' => $this->data[$this->name]['codigo_produto'],
		);
		$combinacao = $this->find('first', compact('conditions'));
		$combinacao_unica = $combinacao['ObjetivoExcecaoFaturamento']['codigo'];
		$codigo_selecionado = (isset($this->data['ObjetivoExcecaoFaturamento']['codigo']) ? $this->data['ObjetivoExcecaoFaturamento']['codigo'] : 0);

		if($combinacao_unica == $codigo_selecionado){
			return TRUE;
		}
		return $this->find('count', compact('conditions')) == 0;
	}

	function combinacao_anual(){
		$this->ObjetivoExcecaoFaturamento = classRegistry::init('ObjetivoExcecaoFaturamento');
		$codigo_cliente = $this->data[$this->name]['codigo_cliente'];
		$codigo_produto = $this->data[$this->name]['codigo_produto'];
		$mes = $this->data[$this->name]['mes'];
		$ano = $this->data[$this->name]['ano'];
		$codigo = isset($this->data[$this->name]['codigo']) ? $this->data[$this->name]['codigo'] : NULL;

		if(strlen($mes) == 1){
			$mes = '0'.$mes;
		}

  		$data_inicio = date("{$ano}-{$mes}-01 00:00:00");
  		$ano_f = $ano + 1;
  		$data_fim = date("{$ano_f}-{$mes}-01 00:00:00");
  		
  		$retorno = $this->query("
  			SELECT * FROM {$this->ObjetivoExcecaoFaturamento->databaseTable}.{$this->ObjetivoExcecaoFaturamento->tableSchema}.{$this->ObjetivoExcecaoFaturamento->useTable} AS ObjetivoExcecaoFaturamento
  			WHERE codigo_cliente = $codigo_cliente 
  			AND codigo_produto = $codigo_produto
  			AND CONVERT(VARCHAR(20),CAST((ObjetivoExcecaoFaturamento.ano+1) AS VARCHAR(10))+'-'+RIGHT('0'+CAST(ObjetivoExcecaoFaturamento.mes AS VARCHAR(10)),2)+'-'+'01 00:00:00',120) >= '$data_inicio'
  			AND (
  				CONVERT(VARCHAR(20),CAST((ObjetivoExcecaoFaturamento.ano) AS VARCHAR(10))+'-'+RIGHT('0'+CAST(ObjetivoExcecaoFaturamento.mes AS VARCHAR(10)),2)+'-'+'01 00:00:00',120) 
  				BETWEEN '$data_inicio' and '$data_fim'

  				OR

  				CONVERT(VARCHAR(20),CAST((ObjetivoExcecaoFaturamento.ano+1) AS VARCHAR(10))+'-'+RIGHT('0'+CAST(ObjetivoExcecaoFaturamento.mes AS VARCHAR(10)),2)+'-'+'01 00:00:00',120)
  				BETWEEN '$data_inicio' and '$data_fim'
  			)
  		");
  		
  		if((count($retorno) == 1) && !empty($codigo)){
  			if($retorno[0][0]['codigo'] == $codigo){
  				return true;
			}
  		}

		if(!empty($retorno)){
			return false;	
		}
		return true;
	}

  	function bindObjetivoExcecaoFaturamento(){
        $this->bindModel(
            array(
                'hasOne'=>array(                	
                    'Cliente' => array(
                        'className'  =>  'Cliente',
                        'foreignKey' => false,
                        'conditions' => array("Cliente.codigo = ObjetivoExcecaoFaturamento.codigo_cliente"),
                    ),
                    'Produto' => array(
                        'className'  =>  'Produto',
                        'foreignKey' => false,
                        'conditions' => array("Produto.codigo = ObjetivoExcecaoFaturamento.codigo_produto"),
                    ),
            )), false
        ); 
        
    }

  	function converteFiltrosEmConditions($data){
  		$conditions = array();
  		if(isset($data['codigo_cliente']) && !empty($data['codigo_cliente'])){
  			$conditions['codigo_cliente'] = $data['codigo_cliente'];
  		}

  		if(isset($data['mes']) && !empty($data['mes'])){
  			$conditions['mes'] = $data['mes'];
  		}

  		if(isset($data['ano']) && !empty($data['ano'])){
  			$conditions['ano'] = $data['ano'];
  		}

  		if(isset($data['codigo_produto']) && !empty($data['codigo_produto'])){
  			$conditions['codigo_produto'] = $data['codigo_produto'];
  		}
  
  		if(isset($data['faturamento_medio']) && !empty($data['faturamento_medio'])){
  			$fat = str_replace('.', '', $data['faturamento_medio']);
  			$fat = str_replace(',', '.', $fat);
  			$conditions['faturamento_medio'] = $fat;
  			
  		}
  		return $conditions;
  	}

  	function verificaFaturamentoExcecaoRealizado($mes = null,$ano = null){
  		$this->Cliente = & ClassRegistry::init('Cliente');
  	
  	  	if(!empty($mes)){
  			$ultimo_dia = date("t", mktime(0,0,0,date("$mes"),'01',date("$ano")));
  			$data_passada = date("Y-$mes-1 00:00:00");
    		$data_atual = date("Y-$mes-{$ultimo_dia} 23:59:59");
  		}else{
			$ultimo_dia = date("t", mktime(0,0,0,date('2'),'01',date('Y')));
  			$data_passada = date('Y-m-1 00:00:00');
    		$data_atual = date("Y-m-{$ultimo_dia} 23:59:59");
  		}
    	
		$fields = array(
			'DISTINCT cliente.codigo AS codigo_cliente',
			'1 AS excecao_faturamento_medio',
			'0 AS cliente_novo',
			'(SUM(Notaite.preco) - ObjetivoExcecaoFaturamento.faturamento_medio) as faturamento_realizado',
			'0 as visitas',
			'Cliente.codigo_gestor as codigo_gestor',
			'SUBSTRING(CONVERT(VARCHAR(20),notaite.dtemissao, 105),4,9) AS data_inclusao',
			'CASE 
        		WHEN ClienteProduto.codigo_produto = 2 THEN 1
        		ELSE ClienteProduto.codigo_produto
    		END AS codigo_produto',
			'Cliente.codigo_endereco_regiao as codigo_endereco_regiao'
		);

    	$this->Cliente->bindModel(array(
			'hasOne' => array(				
				'Notaite' => array(
					'foreignKey' => false,
					'conditions' => array("Notaite.cliente = Cliente.codigo AND Notaite.dtemissao  BETWEEN '{$data_passada}' AND '{$data_atual}'"),
					'type' => 'INNER',
				),					
				'Produto' => array(
					'foreignKey' => false,
					'conditions' => array("Produto.codigo_naveg = Notaite.produto"),
					'type' => 'INNER',
				),
				'ClienteProduto' => array(
					'foreignKey' => false,
					'conditions' => array(
						"CONVERT(VARCHAR(20),DATEPART( year, [Notaite].[dtemissao])) + '-' + CONVERT(VARCHAR(20),DATEPART(MONTH, [Notaite].[dtemissao] )) >=  CONVERT(VARCHAR(20),DATEPART( year, [ClienteProduto].[data_faturamento])) + '-' + CONVERT(VARCHAR(20),DATEPART(MONTH, [ClienteProduto].[data_faturamento]))",
						"ClienteProduto.codigo_cliente = Cliente.codigo",
    					"(Produto.codigo = CASE WHEN ClienteProduto.codigo_produto = 1 THEN 1
      						WHEN ClienteProduto.codigo_produto = 2 THEN 1 ELSE ClienteProduto.codigo_produto
      					END)"
					),
					'type' => 'INNER',
				),
				'ObjetivoExcecaoFaturamento' => array(
					'foreignKey' => false,
					'conditions' => array(
						"ObjetivoExcecaoFaturamento.codigo_cliente = Notaite.cliente",
						"ObjetivoExcecaoFaturamento.codigo_produto = CASE WHEN ClienteProduto.codigo_produto = 2 THEN 1 ELSE ClienteProduto.codigo_produto END",
					),
					'type' => 'INNER',
				),
				
			),
		));
 
		$faturamento = $this->Cliente->find('all',array(
    		'fields' => $fields,
    		'conditions' => array(
    			"CONVERT(VARCHAR(20),Notaite.dtemissao, 120) BETWEEN
    			CONVERT(VARCHAR(20),CAST((ObjetivoExcecaoFaturamento.ano) AS VARCHAR(10))+'-'+
				RIGHT('0'+CAST(ObjetivoExcecaoFaturamento.mes as VARCHAR(10)),2)+'-'+
 				'01 00:00:00',120) 
    			AND
    			CONVERT(VARCHAR(20),
				CAST((ObjetivoExcecaoFaturamento.ano +  1) AS VARCHAR(10))+'-'+
				RIGHT('0'+CAST(ObjetivoExcecaoFaturamento.mes as VARCHAR(10)),2)+'-'+
				'31 23:59:59',120)

    			GROUP BY cliente.codigo,Cliente.codigo_gestor,SUBSTRING(CONVERT(VARCHAR(20),notaite.dtemissao, 105),4,9),
				ClienteProduto.codigo_produto,Cliente.codigo_endereco_regiao ,ObjetivoExcecaoFaturamento.faturamento_medio 
    			
    			HAVING SUM([Notaite].[preco]) - [ObjetivoExcecaoFaturamento].[faturamento_medio] > 0
    			"
    		)
    	));
    	return $faturamento;    	
    }

    function inserirFaturamentoExcecao($dados){
    	$this->ObjetivoComercialCliente = & ClassRegistry::init('ObjetivoComercialCliente');
    	if(!empty($dados)){
	    	try {
		    	$this->ObjetivoComercialCliente->query('BEGIN TRANSACTION');
		    	foreach ($dados as $dado) {
		    		$ano_mes = explode('/', $dado[0]['data_inclusao']);
		    		if(!isset($ano_mes[1]))
		    			$ano_mes = explode('-', $dado[0]['data_inclusao']);

		    		if(strlen($ano_mes[1]) == 4){
		    			$ano_excecao = $ano_mes[1];
		    			$mes_excecao = $ano_mes[0];
		    		}elseif(strlen($ano_mes[0]) == 4){
		    			$ano_excecao = $ano_mes[0];
		    			$mes_excecao = $ano_mes[1];
		    		}else{
		    			throw new Exception("Falha da data de inclusão");
		    		}

		    
		    		$dado[0]['ano'] = $ano_excecao;
		    		$dado[0]['mes'] = $mes_excecao;
		    		$dado[0]['codigo_gestor_origem'] = $dado[0]['codigo_gestor'];
		    		$dado[0]['excecao'] = 0;
		    		$excecao['ObjetivoComercialCliente'] = $dado[0];

		    		if(!$this->excluir_faturamento_excecoes($ano_excecao,$mes_excecao,$dado[0]['codigo_produto'])){
		    			throw new Exception("Falha ao excluir exceções de faturamento");
		    		}	    		

		    		if(!$this->ObjetivoComercialCliente->incluir($excecao)){
		    			throw new Exception("Falha ao incluir exceções de faturamento");
		    		}

	    		}
	    		$this->ObjetivoComercialCliente->commit();
	    		return true;
	    	} catch (Exception $e) {
				$this->ObjetivoComercialCliente->rollback();
				return false;
			}
		}
		return true;
	}

	function excluir_faturamento_excecoes($ano,$mes,$produto){
		$this->ObjetivoComercialCliente = & ClassRegistry::init('ObjetivoComercialCliente');
    	$dados_incluidos = $this->ObjetivoComercialCliente->find('all',
			array(
				'fields' => array('codigo'),
				'conditions' => array(
					'ano' => $ano,
					'mes' => $mes,
					'codigo_produto' => $produto,
					'excecao_faturamento_medio' => 1
				),
			)
		);

		foreach ($dados_incluidos as $excluir) {
			if(!$this->ObjetivoComercialCliente->excluir($excluir['ObjetivoComercialCliente']['codigo'])){
				return false;
			}
		}
		return true;
    }	
  
}
?>