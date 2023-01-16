<?php
class ObjetivoComercial extends AppModel {
	var $name          = 'ObjetivoComercial';
	var $tableSchema   = 'vendas';
	var $databaseTable = 'dbBuonny';
	var $useTable      = 'objetivos_comerciais';
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
		),
		'codigo_endereco_regiao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a Filial',
			),
			'combinacao_unica' => array(
				'rule' => 'combinacao_unica',
				'message' => '',
			),			
		),
		'codigo_gestor' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Gestor',
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
				'message' => 'Este vínculo de mês,ano,filial,gestor e produto já existe.',
			),
		),
		'visitas_objetivo' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe as Visitas',
			),
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Formato Inválido',
			),			
		),
		'faturamento_objetivo' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe as Faturamento',
			),
			'money' => array(
				'rule' => 'money',
				'message' => 'Formato Inválido',
			),
		),
		'novos_clientes_objetivo' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe os novos cliente',
			),
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Formato Inválido',
			),
		),
	);
	
	//Agrupamento
	CONST FILIAL = 1;
	CONST PRODUTO = 2;
	CONST GESTOR = 3;
	CONST DIRETORIA = 4;

	//Visualizar Gráfico por
	CONST VISITAS = 1;
	CONST NOVOS_CLIENTES = 2;
	CONST FATURAMENTO = 3;

	function listarAgrupamentos() {
        return array(
        	self::FILIAL => 'Filial',
        	self::PRODUTO => 'Produto',
        	self::GESTOR => 'Gestor',        
        	self::DIRETORIA => 'Diretoria',        
        );
    }

    function listarTipoVisualizacao() {
        return array(
        	self::VISITAS => 'Visitas',
        	self::NOVOS_CLIENTES => 'Novos Clientes',
        	self::FATURAMENTO => 'Faturamento',        
        );
    }

    function TipoVisualizacaoDescricao($codigo) {
        switch ($codigo) {
        	case self::VISITAS:
        		$descricao = 'Visitas';
        	break;
        	case self::NOVOS_CLIENTES:
        		$descricao = 'Novos Clientes';
        	break;
        	case self::FATURAMENTO:
        		$descricao = 'Faturamento';
        	break;
        	default:
        		$descricao = 'Código não localizado';
        	break;
        }

        return $descricao;
    }

	function combinacao_unica() {
		$conditions = array(
			'mes' => $this->data[$this->name]['mes'],
			'ano' => $this->data[$this->name]['ano'],
			'codigo_endereco_regiao' => $this->data[$this->name]['codigo_endereco_regiao'],
			'codigo_gestor' => $this->data[$this->name]['codigo_gestor'],
			'codigo_produto' => $this->data[$this->name]['codigo_produto'],
		);
		$combinacao = $this->find('first', compact('conditions'));
		$combinacao_unica = $combinacao['ObjetivoComercial']['codigo'];
		$codigo_selecionado = (isset($this->data['ObjetivoComercial']['codigo']) ? $this->data['ObjetivoComercial']['codigo'] : 0);

		if($combinacao_unica == $codigo_selecionado){
			return TRUE;
		}
		return $this->find('count', compact('conditions')) == 0;
	}
	function bindObjetivoComercial(){
        $this->bindModel(
            array(
                'hasOne'=>array(                	
                    'EnderecoRegiao' => array(
                        'className'  =>  'EnderecoRegiao',
                        'foreignKey' => false,
                        'conditions' => array("EnderecoRegiao.codigo = ObjetivoComercial.codigo_endereco_regiao"),
                    ),
                    'Usuario' => array(
                        'className'  =>  'Usuario',
                        'foreignKey' => false,
                        'conditions' => array('Usuario.codigo = ObjetivoComercial.codigo_gestor'),
                    ),
                    'Diretoria' => array(
                        'className'  =>  'Diretoria',
                        'foreignKey' => false,
                        'conditions' => array('Diretoria.codigo = Usuario.codigo_diretoria'),
                    ),
                    'Produto' => array(
                        'className'  =>  'Produto',
                        'foreignKey' => false,
                        'conditions' => array('Produto.codigo = ObjetivoComercial.codigo_produto'),
                    ),                      
            )), false
        ); 
        
    }



	function bindEnderecoRegiao($reset = TRUE){
		$this->bindModel(array(
			'hasOne' => array(
				'EnderecoRegiao' => array(
					'foreignKey' => false,
                    'conditions' => array("EnderecoRegiao.codigo = ObjetivoComercial.codigo_endereco_regiao"),
				),
			),			
		),$reset);
	}

	function bindProduto($reset = TRUE){
		$this->bindModel(array(
			'hasOne' => array(
				'Produto' => array(
					'foreignKey' => false,
                    'conditions' => array('Produto.codigo = ObjetivoComercial.codigo_produto'),
				),
			),			
		),$reset);
	}
	

	function bindUsuario($reset = TRUE){
		$this->bindModel(array(
			'hasOne' => array(
				'Usuario' => array(
					'foreignKey' => false,
                    'conditions' => array('Produto.codigo = ObjetivoComercial.codigo_produto'),
				),
			),			
		),$reset);
	}
	
	function converteFiltrosEmConditions($dados){
		$conditions = array();
		if(!empty($dados['mes'])){
			$conditions['mes'] = $dados['mes'];
		}
		if(!empty($dados['ano'])){
			$conditions['ano'] = $dados['ano'];
		}
		if(!empty($dados['codigo_endereco_regiao'])){
			$conditions['codigo_endereco_regiao'] = $dados['codigo_endereco_regiao'];
		}
		if(!empty($dados['codigo_gestor'])){
			$conditions['codigo_gestor'] = $dados['codigo_gestor'];
		}
		if(!empty($dados['codigo_produto'])){
			$conditions['codigo_produto'] = $dados['codigo_produto'];
		}

		if(!empty($dados['codigo_diretoria'])){
			$conditions['Usuario.codigo_diretoria'] = $dados['codigo_diretoria'];
		}

		return $conditions;
	}

	function sintetico_sintetico($conditions,$agrupamento){	
		$this->bindObjetivoComercial();
		$agrupamentos = $this->ObjetivoComercialCliente->tipo_agrupamento($agrupamento);
		$descricao = $agrupamentos['descricao'];
		$codigo_descricao = $agrupamentos['codigo_descricao'];
		$agrupamentoDescricao = $agrupamentos['agrupamentoDescricao'];
        $fields = array(
        	"$descricao as descricao",
			"$codigo_descricao as codigo_descricao",
			'SUM(visitas_objetivo) as visitas_objetivo',
			'SUM(faturamento_objetivo) as faturamento_objetivo',
			'SUM(novos_clientes_objetivo) as novos_clientes_objetivo',
			'0 as visitas_realizadas',
			'0 as faturamento_realizado',
			'0 as cliente_novo',
			'1 as objetivo'
		);

		$group = array(
			"$descricao",
			"$codigo_descricao"	
		);

		$listagem = $this->find('sql',array(
			'fields' => $fields,
			'group' => $group,
			'conditions' => $conditions
		));

		return $this->set(compact('listagem','descricao','agrupamentoDescricao','codigo_descricao'));
	}

	function sintetico($conditions,$agrupamento,$produto_buonny = FALSE){
		$this->ObjetivoComercialCliente = classRegistry::init('ObjetivoComercialCliente');
		$analitico = $this->ObjetivoComercialCliente->sintetico_analitico($conditions,$agrupamento);
		$sintetico = $this->sintetico_sintetico($conditions,$agrupamento);
		$analitico_sql = $analitico['ObjetivoComercialCliente']['listagem'];		
		$sintetico_sql = $sintetico['ObjetivoComercial']['listagem'];		
		
		if(isset($produto_buonny) && !empty($produto_buonny)){
			$condicao = "WHERE agrupado.codigo_descricao <> $produto_buonny";
		}else{
			$condicao = 'WHERE 1 = 1';
		}	

		$sql = "SELECT descricao,codigo_descricao,SUM(visitas_objetivo) as visitas_objetivo,
			SUM(faturamento_objetivo) as faturamento_objetivo,
			SUM(novos_clientes_objetivo) as novos_clientes_objetivo,
			SUM(visitas_realizadas) as visitas_realizadas,
			SUM(faturamento_realizado) as faturamento_realizado,
			SUM(cliente_novo) as cliente_novo,
			SUM(objetivo) as objetivo
		FROM(
			$analitico_sql UNION $sintetico_sql
		) as agrupado $condicao Group by agrupado.descricao,agrupado.codigo_descricao HAVING sum(agrupado.objetivo) = 1";
		$listagem = $this->query($sql);
		return $listagem;
	}

}
?>