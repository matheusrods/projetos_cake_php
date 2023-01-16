<?php
App::import('Model', 'Produto');
App::import('Model', 'TipoFrota');
App::import('Model', 'VeiculoClassificacao');
class ClienteProdutoServico2 extends AppModel {
	var $name = 'ClienteProdutoServico2';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cliente_produto_servico2';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_cliente_produto_servico2'));


	var $validate = array(
		'codigo_servico' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Serviço não informado',
				'required' => true,
				'allowEmpty' => false,
			),
			array(
				'rule' => 'unico',
				'message' => 'Já existe este serviço para este cliente',
				'required' => true,
				'allowEmpty' => false,
			),			
		),
		'quantidade' => array(
			array(
				'rule' => 'quantidade',
				'message' => 'Quantidade precisa ser maior que 0',				
			),
		),
		'valor' => array(
			array(
				'rule' => 'valor',
				'message' => 'Valor não pode ser vazio para esse Serviço'
			)
		)
	);

	protected function unico() {
		$conditions = array(
			'codigo_cliente_produto' => $this->data[$this->name]['codigo_cliente_produto'], 
			'codigo_servico' => $this->data[$this->name]['codigo_servico']
		);
		if (isset($this->data[$this->name]['codigo']) && !empty($this->data[$this->name]['codigo']))
			$conditions['NOT'] = array($this->name.'.codigo' => $this->data[$this->name]['codigo']);
		return ($this->find('count', compact('conditions')) == 0);
	}
	protected function quantidade() {		
		if(!empty($this->data[$this->name]['codigo_produto'])){
			$Produto = ClassRegistry::init('Produto');			
			if(!in_array($this->data[$this->name]['codigo_produto'], $Produto->produtos_quantitativos())){
				return $this->data[$this->name]['quantidade'] > 0;
			}
		}
		return true;
	}

	protected function valor(){
		if($this->data[$this->name]['codigo_produto'] == 59){
			if(empty($this->data[$this->name]['valor'])){
				return false;
			}else if($this->data[$this->name]['valor'] == '0,00'){
				return false;
			}
		}
		return true;
	}

	function excluir_servico_assinatura($codigo_produto_servico) {
		$dados = $this->find('first', array('conditions' => array('codigo' => $codigo_produto_servico)));
		$codigo_cliente_produto = $dados['ClienteProdutoServico2']['codigo_cliente_produto'];
		$codigo_servico = $dados['ClienteProdutoServico2']['codigo_servico'];
		
		try {
			$this->query('BEGIN TRANSACTION');

			if (!$this->excluir($codigo_produto_servico)) throw new Exception("Erro ao excluir servico em ClienteProdutoServico2");
			if ($codigo_cliente_produto && $codigo_servico) {
				if (!$this->excluirPorServicoEProduto($codigo_cliente_produto, $codigo_servico)) 
					throw new Exception("Erro ao excluir servico em ClienteProdutoServico2");
			}

			$this->commit();
			return true;
		} catch (Exception $ex) {
			$this->rollback();
			return false;
		}
	}

	public function carregarClienteProdutoServico($codigo_cliente, $codigo_produto, $codigo_servico) {
		$this->bindModel(array('belongsTo' => array(
			'ClienteProduto' => array('foreignKey' => 'codigo_cliente_produto')
		)));
		$conditions = array(
			'Clienteproduto.codigo_cliente' => $codigo_cliente,
			'Clienteproduto.codigo_produto' => $codigo_produto,
			'codigo_servico' => $codigo_servico
		);
		return $this->find('first', compact('conditions'));
	}

	public function getByCodigoClienteProdutoEServico($codigo_cliente_produto, $codigo_servico) {
        try {
            if(empty($codigo_cliente_produto) || empty($codigo_servico)) {
                throw new Exception('Códigos obrigatórios!');
            }
            
            $result = $this->find('first', array(
                'conditions' => array(
                    'ClienteProdutoServico2.codigo_cliente_produto' => $codigo_cliente_produto,
                    'ClienteProdutoServico2.codigo_servico' => $codigo_servico
            )));
            
            if($result) {
                return $result;
            } else {
                throw new Exception('Error');
            }
            
        } catch(Exception $e) {
            return false;
        }
    }

	public function excluirPorServicoEProduto($codigo_cliente_produto, $codigo_servico) {
        try {
            if(empty($codigo_cliente_produto) || empty($codigo_servico)) {
                throw new Exception('Codigo serviço e produto são obrigatórios!');
            }

            return $this->deleteAll(array(
                'codigo_cliente_produto' => $codigo_cliente_produto,
                'codigo_servico' => $codigo_servico
            ));
        } catch(Exception $e) {
            return false;
        }
    }

	function listarProdutoServicoClientePagador($cliente_codigo_pagador, $codigo_produto){
           
        $ClienteProduto             = ClassRegistry::init('ClienteProduto');
        $Cliente                    = ClassRegistry::init('Cliente');
        $Produto                    = ClassRegistry::init('Produto');
        $Servico                    = ClassRegistry::init('Servico');

        $fields = array(
            'Cliente.razao_social as cliente_pagador',            
            'Servico.descricao',
            'Produto.descricao',
            'ClienteProduto.codigo_cliente',
            'ClienteProdutoServico2.valor',
        );        

        $joins = array(
                array(
                    'table' => "{$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable}",
                    'alias' => 'ClienteProduto',
                    'conditions' => array(
                    	'ClienteProduto.codigo = ClienteProdutoServico2.codigo_cliente_produto',
                    	'ClienteProduto.codigo_cliente = ClienteProdutoServico2.codigo_cliente_pagador',
                    ),
                    'type'  => 'INNER'
                ),
                array(
	                'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
	                'alias' => 'Cliente',
	                'conditions' => 'Cliente.codigo = ClienteProduto.codigo_cliente',
	                'type' => 'LEFT',
                ),
                array(
                    'table' => "{$Produto->databaseTable}.{$Produto->tableSchema}.{$Produto->useTable}",
                    'alias' => 'Produto',
                    'conditions' => 'Produto.codigo = ClienteProduto.codigo_produto',
                    'type' => 'INNER',
                ),
                array(
                    'table' => "{$Servico->databaseTable}.{$Servico->tableSchema}.{$Servico->useTable}",
                    'alias' => 'Servico',
                    'conditions' => 'Servico.codigo = ClienteProdutoServico2.codigo_servico',
                    'type'  => 'INNER'
                ),
        );         

        $conditions['ClienteProduto.codigo_cliente'] = $cliente_codigo_pagador;
        $conditions['ClienteProduto.codigo_produto'] = $codigo_produto;

        return $this->find('all', compact('conditions','joins', 'fields'));   
    }

    function atualizarValores($filtros) {
        $this->ClienteProduto		  =& ClassRegistry::init('ClienteProduto');
        $this->ClienteProdutoContrato =& ClassRegistry::init('ClienteProdutoContrato');
		
		$igpm = str_replace(',','.',$filtros['igpm']);
		if(substr_count($igpm,'.') > 1)
			return false;

        $query_atualizacao = "UPDATE {$this->databaseTable}.{$this->tableSchema}.{$this->useTable}
							  SET
									valor = round(valor * (1 + (CAST({$igpm } AS FLOAT) / 100)),2)
							  FROM
									{$this->databaseTable}.{$this->tableSchema}.{$this->useTable} AS ClienteProdutoServico
									INNER JOIN {$this->ClienteProduto->databaseTable}.{$this->ClienteProduto->tableSchema}.{$this->ClienteProduto->useTable} AS ClienteProduto
									ON ClienteProdutoServico.codigo_cliente_produto = ClienteProduto.codigo 
									INNER JOIN {$this->ClienteProdutoContrato->databaseTable}.{$this->ClienteProdutoContrato->tableSchema}.{$this->ClienteProdutoContrato->useTable} AS ClienteProdutoContrato
									ON ClienteProdutoContrato.codigo_cliente_produto = ClienteProduto.codigo
							  WHERE
									ClienteProdutoContrato.data_vigencia BETWEEN '{$filtros['data_inicial']}' AND '{$filtros['data_final']}' AND
									ClienteProduto.codigo_motivo_bloqueio <> 3";

		if (isset($filtros['codigo_produto']) && $filtros['codigo_produto'])
			$query_atualizacao = $query_atualizacao." AND ClienteProduto.codigo_produto = {$filtros['codigo_produto']}";
		
		return ($this->query($query_atualizacao) !== false);
	}

	private function bindFrotaPagador($filtros) {
		$this->bindModel(
			array(
				'belongsTo' => array(
					'ClienteProduto' => array(
						'foreignKey' => 'codigo_cliente_produto', 
						'type' => 'INNER',
						'conditions' => array(
							'ClienteProduto.codigo_produto' => Produto::BUONNYSAT,
							array(
								'OR' => array(
									'ClienteProduto.codigo_motivo_bloqueio' => 1,
									"ClienteProduto.data_inativacao >=" => AppModel::dateToDbDate($filtros['data_inicial']),
								)
							)
						)
					),
					'Cliente' => array(
						'foreignKey' => false, 
						'type' => 'INNER',
						'conditions' => array(
							'Cliente.codigo = ClienteProduto.codigo_cliente', 
							'Cliente.data_inclusao <=' => AppModel::dateToDbDate($filtros['data_final'])
						),
					),
				),
				'hasOne' => array(
					'ClienteVeiculo' => array(
						'foreignKey' => false, 
						'type' => 'INNER',
						'conditions' => array(
							'ClienteVeiculo.codigo_cliente = Cliente.codigo',
							'ClienteVeiculo.codigo_tipo_frota' => TipoFrota::FIXO,
							'ClienteVeiculo.codigo_sistema' => 12,
						)
					),
					'Veiculo' => array(
						'foreignKey' => false, 
						'type' => 'INNER',
						'conditions' => array(
							'Veiculo.codigo = ClienteVeiculo.codigo_veiculo',
							'OR' => array(
								'Veiculo.codigo_veiculo_classe' => NULL,
								'Veiculo.codigo_veiculo_classe !=' => VeiculoClassificacao::CARRETA,
							),
						),
					),
				),
			)
		);
	}

	private function bindFrotaFiliais($filtros) {
		$this->bindModel(
			array(
				'belongsTo' => array(
					'ClienteProduto' => array(
						'foreignKey' => 'codigo_cliente_produto', 
						'type' => 'INNER',
						'conditions' => array(
							'ClienteProduto.codigo_produto' => Produto::BUONNYSAT,
							array(
								'OR' => array(
									'ClienteProduto.codigo_motivo_bloqueio' => 1,
									"ClienteProduto.data_inativacao >=" => AppModel::dateToDbDate($filtros['data_inicial']),
								)
							)
						)
					),
					'Cliente' => array(
						'foreignKey' => false, 
						'type' => 'INNER',
						'conditions' => array(
							'Cliente.codigo = ClienteProduto.codigo_cliente', 
							'Cliente.data_inclusao <=' => AppModel::dateToDbDate($filtros['data_final'])
						),
					),
				),
				'hasOne' => array(
					'MatrizProdutoPagador' => array(
						'foreignKey' => false,
						'type' => 'INNER',
						'conditions' => 'MatrizProdutoPagador.codigo_cliente_pagador = Cliente.codigo',
					),
					'MatrizFilial' => array(
						'foreignKey' => false,
						'type' => 'INNER',
						'conditions' => 'MatrizFilial.codigo = MatrizProdutoPagador.codigo_matriz_filial',
					),
					'ClienteVeiculo' => array(
						'foreignKey' => false, 
						'type' => 'INNER',
						'conditions' => array(
							'ClienteVeiculo.codigo_cliente = MatrizFilial.codigo_cliente_filial',
							'ClienteVeiculo.codigo_tipo_frota' => TipoFrota::FIXO,
							'ClienteVeiculo.codigo_sistema' => 12,
						)
					),
					'Veiculo' => array(
						'foreignKey' => false, 
						'type' => 'INNER',
						'conditions' => array(
							'Veiculo.codigo = ClienteVeiculo.codigo_veiculo',
							'OR' => array(
								'Veiculo.codigo_veiculo_classe' => NULL,
								'Veiculo.codigo_veiculo_classe !=' => VeiculoClassificacao::CARRETA,
							),
						),
					),
				),
			)
		);
	}

	private function queryFrotaPorPagador($filtros, $tipo) {
		if ($tipo == 'pagador') {
			$this->bindFrotaPagador($filtros);
		} else {
			$this->bindFrotaFiliais($filtros);
		}
		$conditions = array(
			$this->name.'.codigo_servico' => Servico::PLACA_FROTA,
			$this->name.'.codigo_cliente_pagador = ClienteProduto.codigo_cliente',
		);
		if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {
			$conditions['ClienteProduto.codigo_cliente'] = $filtros['codigo_cliente'];
		}
		$data_inicial = AppModel::dateToDbDate($filtros['data_inicial']);
		$dias_do_mes = "DATEPART(DAY, DATEADD(s,-1,DATEADD(mm, DATEDIFF(m,0,'{$data_inicial}')+1,0)))";
		$dias_utilizados = "(CASE WHEN ClienteProduto.codigo_motivo_bloqueio = 1 THEN {$dias_do_mes} ELSE DATEPART(DAY, ClienteProduto.data_inativacao) END)";
		$valor_proporcional = "CONVERT(DECIMAL(14,2),({$this->name}.valor / {$dias_do_mes} ) * {$dias_utilizados})";
		$fields = array(
			'ClienteVeiculo.codigo AS codigo',
			'Cliente.codigo AS codigo_cliente',
			'Veiculo.codigo AS codigo_veiculo',
			'Veiculo.placa AS placa',
			"{$valor_proporcional} AS valor",
		);
		$group = array(
			'ClienteVeiculo.codigo',
			'Cliente.codigo',
			'Veiculo.codigo',
			'Veiculo.placa',
			"{$valor_proporcional}",
		);
		return $this->find('sql', compact('conditions', 'fields', 'group'));
	}

	function frotaPorPagador($filtros, $returnQuery = false, $detalhar = false) {
		$dbo = $this->getDataSource();
		$queryFiliais = $this->queryFrotaPorPagador($filtros, 'pagador');
		$queryPagador = $this->queryFrotaPorPagador($filtros, 'filiais');
		$query = $queryPagador.' UNION '.$queryFiliais;
		$fields = array(
			'codigo_cliente',
			'codigo_veiculo',
			'placa',
			'valor',
		);
		$group = $fields;
		if (!$detalhar) {
			$query = $dbo->buildStatement(
			array(
				'table' => "({$query})",
				'alias' => 'Frota',
				'joins' => array(),
				'fields' => $fields,
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'group' => $group,
			)
			, $this);
			$group = array(
				'codigo_cliente',
				'valor',
			);
			$fields = array(
				'codigo_cliente AS cliente_pagador',
				'valor AS valor_unitario_frota',
				'COUNT(distinct codigo_veiculo) AS qtd_frota',
			);
		}
		$query = $dbo->buildStatement(
			array(
				'table' => "({$query})",
				'alias' => 'Frota',
				'joins' => array(),
				'fields' => $fields,
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'group' => $group,
			)
		, $this);
		if ($returnQuery) {
			return $query;
		} else {
			return $this->query($query);
		}
	}

	function verificaServicoCliente( $codigo_cliente, $codigo_servico ){

		$this->bindModel(array(
			'belongsTo' => array(
				'ClienteProduto' => array(
						'foreignKey' => 'codigo_cliente_produto',	
						'conditions' => array(
							'codigo_cliente '.$this->rawsql_codigo_cliente($codigo_cliente).'',
						),
						'type' => 'INNER',
					),
				)
			)
		);
		$conditions = array('codigo_servico' => $codigo_servico);
		return $this->find('count', compact('conditions'));   
	}
	function verificaDataInclusaoServico( $codigo_cliente, $codigo_servico ){
		$this->bindModel(array(
			'belongsTo' => array(
				'ClienteProduto' => array(
						'foreignKey' => 'codigo_cliente_produto',	
						'conditions' => array(
							'codigo_cliente = '.$codigo_cliente.'',
						),
						'type' => 'INNER',
					),
				)
			)
		);
		$fields = array(
			'ClienteProdutoServico2.data_inclusao',
		);
		$conditions = array('codigo_servico' => $codigo_servico);
		return $this->find('first', compact('conditions', 'fields'));   
	}

	function produtosEServicos($codigo_cliente, $codigo_produto = null, $codigo_servico = null) {
        $this->bindModel(array('belongsTo' => array(
            'ClienteProduto' => array('foreignKey' => 'codigo_cliente_produto', 'conditions' => array('codigo_motivo_bloqueio' => array(1,8))),
            'Produto' => array('foreignKey' => false, 'conditions' => array('ClienteProduto.codigo_produto = Produto.codigo')),
            'Servico' => array('foreignKey' => 'codigo_servico'),
        )));
        $conditions = array(
        	'ClienteProduto.codigo_cliente' => $codigo_cliente,
        	// '1 = (CASE WHEN codigo_produto IN (1,2) THEN 1 ELSE (CASE WHEN codigo_cliente = codigo_cliente_pagador THEN 1 else 0 END) END)'
        );
        if (!empty($codigo_produto))
            $conditions['Produto.codigo'] = $codigo_produto;
        if (!empty($codigo_servico))
            $conditions['Servico.codigo'] = $codigo_servico;

        return $this->find('all', compact('fields','conditions'));
    }

    function verificaValorCobrancaConsultaMotorista( $codigo_cliente, $codigo_tipo_operacao, $codigo_produto, $codigo_servico ){
    	$TipoOperacao = ClassRegistry::init('TipoOperacao');
    	$sub_query = $TipoOperacao->find('sql', array('fields'=> array('codigo'), 'conditions'=>array('cobrado'=>0) ) );
		$this->bindModel(array(
			'belongsTo' => array(
				'ClienteProduto' => array(
					'foreignKey' => 'codigo_cliente_produto',	
					'conditions' => array( 'codigo_cliente = '.$codigo_cliente.''),
					'type' => 'INNER',
				),			
				'Cliente' => array(
					'foreignKey' => false,	
					'conditions' => 'Cliente.codigo = ClienteProduto.codigo_cliente',
					'type' => 'INNER',
				),
			)
		));
		$conditions = array(
			'ClienteProduto.codigo_produto' => $codigo_produto,
			'ClienteProduto.codigo_motivo_bloqueio' => 1,
			'ClienteProdutoServico2.codigo_servico' => $codigo_servico
		);
		$fields = array(
			'ClienteProdutoServico2.codigo_cliente_pagador','ClienteProdutoServico2.codigo_servico',
			"CASE WHEN {$codigo_tipo_operacao} IN ({$sub_query}) THEN 0.00 ELSE ClienteProdutoServico2.valor END AS valor"
		);
		return $this->find('first', compact('fields','conditions'));
    }

    function verifica_exibicao_numerco_consulta_embarcador($codigo_cliente,$codigo_produto){
    	$this->Servico = ClassRegistry::init('Servico');
    	$this->ClienteProduto = ClassRegistry::init('ClienteProduto');
    	$this->Cliente = ClassRegistry::init('Cliente');
    	$this->ClienteSubTipo = ClassRegistry::init('ClienteSubTipo');
    	$tipo_operacao = Servico::CONSULTA_MOTORISTA;
    	$sub_tipo = $this->Cliente->carregar($codigo_cliente);
		$tipo_cliente = $this->ClienteSubTipo->subTipo($sub_tipo['Cliente']['codigo_cliente_sub_tipo']);
		if($tipo_cliente != ClienteSubTipo::SUBTIPO_EMBARCADOR){
			return FALSE;
		}
		$query = "SELECT consulta_embarcador FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} 
		WHERE codigo_servico = $tipo_operacao AND consulta_embarcador = 1 AND codigo_cliente_produto = (
			SELECT codigo FROM {$this->ClienteProduto->databaseTable}.{$this->ClienteProduto->tableSchema}.{$this->ClienteProduto->useTable}
			WHERE codigo_cliente = $codigo_cliente AND codigo_produto = $codigo_produto
		)";
    	$retorno = $this->query($query);
    	if(!empty($retorno)){
    		return TRUE;
    	}
    	return FALSE;

    }

    /**
     * [verificaExameAssinatura description]
     * 
     * METODO PARA VERIFICAR SE A UNIDADE TEM A ASSINATURA DO EXAME APLICADO NO PCMSO. CASO NÃO TENHA PROCURA O EXAME NA MATRIZ
     *  
     * MESMO ASSIM SE NÃO ENCONTRAR O EXAME NEM NA UNIDADE OU MATRIZ, IRÁ SER ENVIADO UM EMAIL PARA OS USUÁRIOS QUE ESTIVEREM CONFIGURADO A NOTIFICAÇÃO 
     * DE EXAME APLICADO SEM CREDENCIADO (CONFIGURADO PARA USUÁRIO INTERNOS DO SISTEMA)
     * 
     * 
     * @param  [int] $codigo_cliente 	[codigo da unidade do grupo economico]
     * @return [type]                   [description]
     */
    public function verificaExameAssinaturaCredenciado($codigo_cliente)
    {

    	//instancia as models que irá usar neste metodo
    	$GrupoEconomico			= ClassRegistry::init('GrupoEconomico');
    	$AplicacaoExame			= ClassRegistry::init('AplicacaoExame');

    	//join aplicacao
    	$joinAplicacao = array(
    		array(
    			'table' => 'RHHealth.dbo.exames',
    			'alias' => 'Exame',
    			'type' => 'INNER',
    			'conditions' => array('AplicacaoExame.codigo_exame = Exame.codigo'),
    		));

    	//realiza a busca dos exames aplicados    	
    	$servicos_aplicados = $AplicacaoExame->find('all', array('fields' => array('Exame.codigo','Exame.descricao','Exame.codigo_servico'), 'joins' => $joinAplicacao, 'conditions' => array('codigo_cliente' => $codigo_cliente), 'group' => array('Exame.codigo','Exame.descricao','Exame.codigo_servico')));
    	
    	//verifica se existe exames    	
    	if(!empty($servicos_aplicados)) {

	    	//busca o codigo da matriz do grupo economico que o cliente está relacionado
	    	$joinGE = array(
	    			array(
						'table' => 'RHHealth.dbo.grupos_economicos_clientes',
						'alias' => 'GrupoEconomicoCliente',
						'type' => 'INNER',
						'conditions' => array('GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico')
					));
	    	
	    	//busca o codigo da matriz
	    	$gr = $GrupoEconomico->find('first', array('fields' => array('GrupoEconomico.codigo_cliente'),'joins' => $joinGE,'conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));
	    	
	    	//seta o codigo da matriz
	    	$codigo_cliente_matriz = $gr['GrupoEconomico']['codigo_cliente'];

			//busca assinatura da unidade
	    	$joinAssinatura = array(
	    			array(
						'table' => 'RHHealth.dbo.cliente_produto',
						'alias' => 'ClienteProduto',
						'type' => 'INNER',
						'conditions' => array('ClienteProdutoServico2.codigo_cliente_produto = ClienteProduto.codigo')
					));
	    	
	    	//variavel auxiliar para disparar o email
	    	$exames_sem_assinatura = array();
	    	$exames_sem_credenciado = array();
	    	
	    	//varre os exames aplicados olhando para a assinatura da unidade 
	    	foreach($servicos_aplicados as $servicos) {

	    		//seta o codigo do servico
	    		$codigo_servico = $servicos['Exame']['codigo_servico'];

	    		############### SEM ASSINATURA ##############
	    		##################################################################################################
	    		//busca a assinatura do cliente
	    		$assinatura_unidade = $this->find('first', array('joins' => $joinAssinatura,'conditions' => array('ClienteProduto.codigo_cliente' => $codigo_cliente, 'ClienteProdutoServico2.codigo_servico' => $codigo_servico)));

	    		//caso não encontre na assinatura do cliente busca os exames na matriz
	    		if(empty($assinatura_unidade)){
	    		
	    			//caso não encotre na matriz armazena para ir no corpo do email
	    			$assinatura_matriz = $this->find('first', array('joins' => $joinAssinatura,'conditions' => array('ClienteProduto.codigo_cliente' => $codigo_cliente_matriz, 'ClienteProdutoServico2.codigo_servico' => $codigo_servico)));

	    			//verifica se o servico existe, caso nao exista irá ser disparado o email
	    			if(empty($assinatura_matriz)) {
	    				//seta o nome dos exames
	    				$exames_sem_assinatura[$servicos['Exame']['codigo']] = $servicos['Exame']['descricao'];
	    			} //fim assinatura matriz

	    		}//fim if assinatura_unidade
	    		##################################################################################################
	    		
	    		############### SEM CREDENCIADO ##############
	    		##################################################################################################
	    		//monta a query para buscar os credenciados que estão relacionados para o cliente
	    		$queryCredenciado = 'SELECT count(*) as total
	    							FROM RHHealth.dbo.listas_de_preco_produto_servico LPPS
										INNER JOIN RHHealth.dbo.listas_de_preco_produto LPP ON (LPP.codigo = LPPS.codigo_lista_de_preco_produto)
										INNER JOIN RHHealth.dbo.listas_de_preco LP ON (LP.codigo = LPP.codigo_lista_de_preco)
										INNER JOIN RHHealth.dbo.clientes_fornecedores CF ON (CF.codigo_fornecedor = LP.codigo_fornecedor AND CF.ativo = 1)
									WHERE LPPS.codigo_servico = '.$codigo_servico.' AND CF.codigo_cliente = ' . $codigo_cliente;

				//executa a query para saber se tem credenciado para aquele serviço
				$credenciado = $this->query($queryCredenciado);

				//verifica se existe credenciado relacionado do resultado retornado
				if(empty($credenciado[0][0]['total'])) {

					//seta os nomes dos exames que estão sem credenciado
					$exames_sem_credenciado[$servicos['Exame']['codigo']] = $servicos['Exame']['descricao'];

				} //fim if credenciado
	    		##################################################################################################

	    	}//fim foreach servicos aplicados

    		//instancia o cliente
    		$Cliente = ClassRegistry::init('Cliente');
    		//pega os dados da unidade
    		$unidade = $Cliente->find('first',array('fields' => array('Cliente.codigo AS codigo','Cliente.nome_fantasia AS nome_fantasia'),'conditions' => array('codigo' => $codigo_cliente)));

    		//monta o email para ser enviado
    		App::import('Component', array('StringView', 'Mailer.Scheduler'));
			$this->StringView = new StringViewComponent();

	    	//verifica se tem exames sem assinatura
	    	if(!empty($exames_sem_assinatura)) {
	    		//seta os dados para o email
				$this->StringView->reset();
				$this->StringView->set('Exames', $exames_sem_assinatura);
				$this->StringView->set('Unidade', $unidade);
				$content = $this->StringView->renderMail('email_exames_sem_assinatura', 'default');

				//assunto
				$assunto = "Exame aplicado sem assinatura";
	    		
		    	//instancia a model alerta
				$Alerta = ClassRegistry::init('Alerta');
		    	
				//dados para gravar no alerta
	    		$alerta_dados['Alerta']['codigo_cliente'] 		= $codigo_cliente;
	    		$alerta_dados['Alerta']['descricao'] 			= $assunto;
	    		$alerta_dados['Alerta']['email_agendados'] 		= '0';
	    		$alerta_dados['Alerta']['sms_agendados'] 		= '0';
	    		$alerta_dados['Alerta']['codigo_alerta_tipo'] 	= '28';
	    		$alerta_dados['Alerta']['descricao_email'] 		= $content;
	    		$alerta_dados['Alerta']['model'] 				= 'GrupoEconomico';
	    		$alerta_dados['Alerta']['foreign_key']			= $codigo_cliente_matriz;
	    		$alerta_dados['Alerta']['assunto'] 				= $assunto;

	    		if(!$Alerta->incluir($alerta_dados)){
	    			return false;
	    		}

	    	}//fim exames_sem_assinatura


	    	//verifica se tem exames sem assinatura
	    	if(!empty($exames_sem_credenciado)) {
	    		//seta os dados para o email
				$this->StringView->reset();
				$this->StringView->set('Exames', $exames_sem_credenciado);
				$this->StringView->set('Unidade', $unidade);
				$content = $this->StringView->renderMail('email_exames_sem_credenciado', 'default');

				//assunto
				$assunto = "Exame aplicado sem credenciado";
	    		
		    	//instancia a model alerta
				$Alerta = ClassRegistry::init('Alerta');
		    	
				//dados para gravar no alerta
	    		$alerta_dados['Alerta']['codigo_cliente'] 		= $codigo_cliente;
	    		$alerta_dados['Alerta']['descricao'] 			= $assunto;
	    		$alerta_dados['Alerta']['email_agendados'] 		= '0';
	    		$alerta_dados['Alerta']['sms_agendados'] 		= '0';
	    		$alerta_dados['Alerta']['codigo_alerta_tipo'] 	= '27';
	    		$alerta_dados['Alerta']['descricao_email'] 		= $content;
	    		$alerta_dados['Alerta']['model'] 				= 'GrupoEconomico';
	    		$alerta_dados['Alerta']['foreign_key']			= $codigo_cliente_matriz;
	    		$alerta_dados['Alerta']['assunto'] 				= $assunto;

	    		if(!$Alerta->incluir($alerta_dados)){
	    			return false;
	    		}

	    	}//fim exames_sem_assinatura

	   	}//fim if empty exames_aplicados
	   	
	   	return true;

    } //fim verificaExameAssinatura


}