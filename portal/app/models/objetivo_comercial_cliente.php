<?php
class ObjetivoComercialCliente extends AppModel {
	var $name          = 'ObjetivoComercialCliente';
	var $tableSchema   = 'vendas';
	var $databaseTable = 'dbBuonny';
	var $useTable      = 'objetivos_comerciais_clientes';
	var $primaryKey    = 'codigo';
  	var $actsAs        = array('Secure');

  	function bindObjetivoComercialCliente(){
        $this->bindModel(
            array(
                'hasOne'=>array(
                	'Cliente' => array(
                        'className'  =>  'Cliente',
                        'foreignKey' => false,
                        'conditions' => array("Cliente.codigo = ObjetivoComercialCliente.codigo_cliente"),
                    ),
                    'EnderecoRegiao' => array(
                        'className'  =>  'EnderecoRegiao',
                        'foreignKey' => false,
                        'conditions' => array("EnderecoRegiao.codigo = ObjetivoComercialCliente.codigo_endereco_regiao"),
                    ),
                    'Usuario' => array(
                        'className'  =>  'Usuario',
                        'foreignKey' => false,
                        'conditions' => array('Usuario.codigo = ObjetivoComercialCliente.codigo_gestor'),
                    ),                    
                    'Produto' => array(
                        'className'  =>  'Produto',
                        'foreignKey' => false,
                        'conditions' => array('Produto.codigo = ObjetivoComercialCliente.codigo_produto'),
                    ),                      
            )), false
        ); 
        
    }

  	function bindObjetivoComercial(){
        $this->bindModel(
            array(
                'hasOne'=>array(
                    'EnderecoRegiao' => array(
                        'className'  =>  'EnderecoRegiao',
                        'foreignKey' => false,
                        'conditions' => array("EnderecoRegiao.codigo = ObjetivoComercialCliente.codigo_endereco_regiao"),
                    ),
                    'Usuario' => array(
                        'className'  =>  'Usuario',
                        'foreignKey' => false,
                        'conditions' => array('Usuario.codigo = ObjetivoComercialCliente.codigo_gestor'),
                    ),
                    'Diretoria' => array(
                        'className'  =>  'Diretoria',
                        'foreignKey' => false,
                        'conditions' => array('Diretoria.codigo = Usuario.codigo_diretoria'),
                    ),
                    'Produto' => array(
                        'className'  =>  'Produto',
                        'foreignKey' => false,
                        'conditions' => array('Produto.codigo = ObjetivoComercialCliente.codigo_produto'),
                    ),

            )), false
        ); 
        
    }

    function converteFiltrosEmConditions($dados){        
        $conditions = array();
		if(!empty($dados['mes'])){
			$conditions['ObjetivoComercialCliente.mes'] = $dados['mes'];
		}
		if(!empty($dados['ano'])){
			$conditions['ObjetivoComercialCliente.ano'] = $dados['ano'];
		}
		if(!empty($dados['codigo_endereco_regiao'])){
			$conditions['ObjetivoComercialCliente.codigo_endereco_regiao'] = $dados['codigo_endereco_regiao'];
		}
		if(!empty($dados['codigo_gestor'])){
			$conditions['ObjetivoComercialCliente.codigo_gestor'] = $dados['codigo_gestor'];
		}
		if(!empty($dados['codigo_produto'])){
			$conditions['ObjetivoComercialCliente.codigo_produto'] = $dados['codigo_produto'];
		}

        if(!empty($dados['codigo_diretoria'])){
            $conditions['Usuario.codigo_diretoria'] = $dados['codigo_diretoria'];
        }

		if(!empty($dados['tipoVisualizacao'])){
			if($dados['visualizacao'] == 1){
				$conditions['ObjetivoComercialCliente.visitas >'] = 0;				
			}elseif($dados['visualizacao'] == 2){
				$conditions['ObjetivoComercialCliente.cliente_novo'] = '1';				
			}elseif($dados['visualizacao'] == 3){
				$conditions['ObjetivoComercialCliente.faturamento_realizado >'] = 0;				
			}
		}

		return $conditions;
	}

	function tipo_agrupamento($agrupamento,$realizado = FALSE){
		if($agrupamento == ObjetivoComercial::FILIAL){
            $descricao = 'EnderecoRegiao.descricao';
            $agrupamentoDescricao = 'Filial';
            $codigo_descricao = 'codigo_endereco_regiao';
        }elseif($agrupamento == ObjetivoComercial::PRODUTO){
            $descricao = 'Produto.descricao';
            $agrupamentoDescricao = 'Produto';
            $codigo_descricao = 'codigo_produto';
        }elseif($agrupamento == ObjetivoComercial::GESTOR){
            $descricao = 'Usuario.nome';
            $agrupamentoDescricao = 'Gestor';
            $codigo_descricao = 'codigo_gestor';
        }elseif($agrupamento == ObjetivoComercial::DIRETORIA){    
            $descricao = 'Diretoria.descricao';
            $agrupamentoDescricao = 'Diretoria';
            $codigo_descricao = 'codigo_diretoria';
        }
        return array('descricao' => $descricao,'agrupamentoDescricao' => $agrupamentoDescricao,'codigo_descricao' => $codigo_descricao);
	}

	function sintetico_analitico($conditions,$agrupamento){	
		$this->bindObjetivoComercial();		
		$agrupamentos = $this->tipo_agrupamento($agrupamento,TRUE);
		$descricao = $agrupamentos['descricao'];
		$codigo_descricao = $agrupamentos['codigo_descricao'];
		$agrupamentoDescricao = $agrupamentos['agrupamentoDescricao'];
        $fields = array(
        	"$descricao as descricao",
			"$codigo_descricao as codigo_descricao",
			'0 as visitas_objetivo',
			'0 as faturamento_objetivo',
			'0 as novos_clientes_objetivo',
			'sum(visitas) as visitas_realizadas',
			'sum(faturamento_realizado) as faturamento_realizado',
			'sum(case 
                WHEN cliente_novo = 1 THEN 1
                ELSE 0
                end) as cliente_novo',
            '0 as objetivo'
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

    function exclui_gestores_com_excecao($dados){
        if(isset($dados['ObjetivoComercialCliente'])){
            $conditions = array(
                'codigo_gestor_origem' => $dados['ObjetivoComercialCliente']['codigo_gestor'],
                'codigo_cliente' => $dados['ObjetivoComercialCliente']['cliente'],
                'mes' => $dados['ObjetivoComercialCliente']['mes'],
                'ano' => $dados['ObjetivoComercialCliente']['ano'],
                'codigo_produto' => $dados['ObjetivoComercialCliente']['codigo_produto'],
                'excecao' => 1
            );
            $count = $this->find('count',array(
                'conditions' => $conditions,
                )
            );
            $dados_alteracao = $this->find('all',array(
                'conditions' => $conditions,
                )
            );            
            if($count > 0){
                foreach ($dados_alteracao as $dado) {
                   $this->excluir($dado['ObjetivoComercialCliente']['codigo']);
                }
            }
        }   
    }

    function exclui_gestores_sem_excecao($dados){
        if(isset($dados['ObjetivoComercialCliente'])){
            $conditions = array(
                'codigo_gestor' => $dados['ObjetivoComercialCliente']['codigo_gestor'],
                'codigo_cliente' => $dados['ObjetivoComercialCliente']['cliente'],
                'mes' => $dados['ObjetivoComercialCliente']['mes'],
                'ano' => $dados['ObjetivoComercialCliente']['ano'],
                'codigo_produto' => $dados['ObjetivoComercialCliente']['codigo_produto'],
                'excecao' => 0
            );
            $dados_alteracao = $this->find('all',array(
                'conditions' => $conditions,
                )
            );
            if(isset($dados_alteracao[0])){            
                $this->excluir($dados_alteracao[0]['ObjetivoComercialCliente']['codigo']); 
            }        
        }   
    }

    function verifica_visitas_cliente($method_find = 'sql'){
        $this->Cliente = ClassRegistry::init("Cliente");
        $this->VtigerCrmentity = ClassRegistry::init("VtigerCrmentity");
        $this->VtigerSeactivityrel = ClassRegistry::init("VtigerSeactivityrel");
        $this->VtigerAccount = ClassRegistry::init("VtigerAccount");
        $this->VtigerActivity = ClassRegistry::init("VtigerActivity");
        $this->VtigerAccountscf = ClassRegistry::init("VtigerAccountscf");
        $this->VtigerUser = ClassRegistry::init("VtigerUser");
        $fields = array(
            'Cliente.codigo as cliente',
            '0 as cliente_novo',
            '0 as faturamento_realizado',
            'COUNT(Cliente.codigo_documento) as visitas_realizado',
            'Usuario.codigo as gestor',
            'SUBSTRING(CONVERT(VARCHAR(20), VtigerCrmentity.date_start, 105),4,9) AS data_inclusao',
            '30    AS produto',
            'Cliente.codigo_endereco_regiao as filial',
        );
        $group = array(
            'Cliente.codigo',           
            'SUBSTRING(CONVERT(VARCHAR(20), VtigerCrmentity.date_start, 105),4,9)',
            'Cliente.codigo_documento',
            'ClienteProduto.codigo_produto',
            'Usuario.codigo',
            'Cliente.codigo_endereco_regiao',
        );
        
        $held = "''Held''";
        $open_query = "SELECT * FROM openquery(LK_MYSQL,'SELECT";
        $fim_open_query = "')";
        $VtigerCrmentity = "{$this->VtigerCrmentity->databaseTable}.{$this->VtigerCrmentity->useTable}";
        $VtigerSeactivityrel = "{$this->VtigerSeactivityrel->databaseTable}.{$this->VtigerSeactivityrel->useTable}";
        $VtigerAccount = "{$this->VtigerAccount->databaseTable}.{$this->VtigerAccount->useTable}";
        $VtigerActivity = "{$this->VtigerActivity->databaseTable}.{$this->VtigerActivity->useTable}";
        $VtigerAccountscf = "{$this->VtigerAccountscf->databaseTable}.{$this->VtigerAccountscf->useTable}";
        $VtigerUser = "{$this->VtigerUser->databaseTable}.{$this->VtigerUser->useTable}";

        if($this->useDbConfig == 'test_suite'){
            $held = "'Held'";
            $open_query = "SELECT";
            $fim_open_query = "SELECT";
            $fim_open_query = '';
            $VtigerCrmentity = "{$this->VtigerCrmentity->databaseTable}.dbo.{$this->VtigerCrmentity->useTable}";
            $VtigerSeactivityrel = "{$this->VtigerSeactivityrel->databaseTable}.dbo.{$this->VtigerSeactivityrel->useTable}";
            $VtigerAccount = "{$this->VtigerAccount->databaseTable}.dbo.{$this->VtigerAccount->useTable}";
            $VtigerActivity = "{$this->VtigerActivity->databaseTable}.dbo.{$this->VtigerActivity->useTable}";
            $VtigerAccountscf = "{$this->VtigerAccountscf->databaseTable}.dbo.{$this->VtigerAccountscf->useTable}";
            $VtigerUser = "{$this->VtigerUser->databaseTable}.dbo.{$this->VtigerUser->useTable}";
        }

        $joins = array(
            array(
                'table' => "($open_query DISTINCT INFORMACOES_EMPRESA.cf_905,VISITA.date_start,USUARIO.user_name
                 FROM $VtigerCrmentity CRM 
                 LEFT JOIN $VtigerSeactivityrel CRM_CONTA ON(CRM.crmid = CRM_CONTA.activityid)
                 LEFT JOIN $VtigerAccount CONTA ON(CRM_CONTA.crmid = CONTA.accountid)
                 LEFT JOIN $VtigerActivity VISITA ON(CRM.crmid = VISITA.activityid)
                 LEFT JOIN $VtigerAccountscf INFORMACOES_EMPRESA ON (CONTA.accountid = INFORMACOES_EMPRESA.accountid)
                 INNER JOIN $VtigerUser USUARIO ON (CRM.smownerid = USUARIO.id)
                 WHERE VISITA.eventstatus = $held
                 AND INFORMACOES_EMPRESA.cf_905 IS NOT NULL
                 AND INFORMACOES_EMPRESA.cf_905 <> 0 $fim_open_query)",
                'alias' => 'VtigerCrmentity',
                'type'  => 'INNER',
                'conditions' => array('CONVERT(Numeric(20),VtigerCrmentity.cf_905) = Cliente.codigo_documento'),
            ),
        );

        $this->Cliente->bindModel(array(
            'hasOne' => array(
                'ClienteProduto' => array(
                    'foreignKey' => false,
                    'conditions' => array("ClienteProduto.codigo_cliente = Cliente.codigo"),
                    'type' => 'INNER',
                ),
                'Usuario' => array(
                    'foreignKey' => false,
                    'conditions' => array("[VtigerCrmentity].[user_name] = [usuario].[apelido]  COLLATE DATABASE_DEFAULT"),
                    'type' => 'INNER',
                ),          
            ),
        ));

        $this->Cliente->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");

        $visitas = $this->Cliente->find($method_find,array(
            'fields' => $fields,
            'joins' => $joins,
            'group' => $group
        ));
    
        return $visitas;
    }

    function verifica_faturamento_realizado($method_find = 'sql'){
        $this->Cliente = ClassRegistry::init("Cliente");
        $ano_passado = date('Y')-1;
        $data_passada = $ano_passado.'-'.date('m-d 00:00:00');
        $data_atual = date('Y-m-d 23:59:59');

        $fields = array(
            'DISTINCT cliente.codigo AS cliente',
            '0 AS cliente_novo',
            'notaite.preco as faturamento_realizado',
            '0 as visitas_realizado',
            'Cliente.codigo_gestor as gestor',
            'SUBSTRING(CONVERT(VARCHAR(20),notaite.dtemissao, 105),4,9) AS data_inclusao',
            'CASE 
                WHEN ClienteProduto.codigo_produto = 2 THEN 1
                ELSE ClienteProduto.codigo_produto
            END AS produto',
            'Cliente.codigo_endereco_regiao as filial'
        );

        $this->Cliente->bindModel(array(
            'hasOne' => array(              
                'Notaite' => array(
                    'foreignKey' => false,
                    'conditions' => array("Notaite.cliente = Cliente.codigo  AND Notaite.dtemissao >= '2015-01-01 00:00:00'"),
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
                        "ClienteProduto.data_faturamento >= '2014-12-01 00:00:00'",
                        "ClienteProduto.data_faturamento BETWEEN '{$data_passada}' AND '{$data_atual}'",
                        "ClienteProduto.codigo_cliente = Cliente.codigo",
                        "(Produto.codigo = CASE WHEN ClienteProduto.codigo_produto = 1 THEN 1
                            WHEN ClienteProduto.codigo_produto = 2 THEN 1 ELSE ClienteProduto.codigo_produto
                        END)"
                    ),
                    'type' => 'INNER',
                ),
                
            ),
        ));
 
        $faturamento = $this->Cliente->find($method_find,array(
            'fields' => $fields     
        ));
        return $faturamento;        
    }

    function verifica_clientes_novos($method_find = 'sql'){
        $this->Cliente = ClassRegistry::init("Cliente");
        $fields = array(
            'Cliente.codigo as cliente',
            '1 as cliente_novo',
            '0 as faturamento_realizado',
            '0 as visitas_realizado',
            'Cliente.codigo_gestor as gestor',
            'SUBSTRING(CONVERT(VARCHAR(20), ClienteProduto.data_faturamento, 105),4,9) AS data_inclusao',
            'ClienteProduto.codigo_produto AS produto',
            'Cliente.codigo_endereco_regiao as filial',
        );
        $group = array(
            'Cliente.codigo_gestor',
            'Cliente.codigo',
            'SUBSTRING(CONVERT(VARCHAR(20), ClienteProduto.data_faturamento, 105),4,9)',
            'Cliente.codigo_endereco_regiao',
            'ClienteProduto.codigo_produto'
        );
        $this->Cliente->bindModel(array(
            'hasOne' => array(
                'ClienteProduto' => array(
                    'foreignKey' => false,
                    'conditions' => array("ClienteProduto.codigo_cliente = Cliente.codigo"),
                    'type' => 'INNER',
                ),          
            ),
        ));
        $clientes = $this->Cliente->find($method_find,array(
            'fields' => $fields,
            'conditions' => array(
                'Cliente.codigo_gestor <>' => NULL,
                'ClienteProduto.data_faturamento <>' => NULL,
            ),
            'group' => $group,
            
        )); 
        
        return $clientes;   
    }

    function agrupar_faturamento_novos_clientes_visitas($agrupado = false,$trava = false,$mes_ano = false){
        //$mes_ano Ex: 09-2015
        $this->ObjetivoComercial = ClassRegistry::init("ObjetivoComercial");
        $novos_clientes = $this->verifica_clientes_novos();
        $faturamento_clientes = $this->verifica_faturamento_realizado();
        $visitas = $this->verifica_visitas_cliente();
        
        if(!$trava){
            $mes_trava = "'".date('m').'-'.date('Y')."'";
            if($mes_ano){
                $dado = explode('-', $mes_ano);
                $mes = $dado[0];
                $ano = $dado[1];
                $mes_trava = "'".date("$mes").'-'.date("$ano")."'";
            }
            $condicao = "agrupado.data_inclusao = {$mes_trava}";
        }else{
            $condicao = "1 = 1";
        }
        $query = "SELECT produto,gestor,";
        if(!$agrupado){
            $query.='cliente,';
        }   
        $query.= "SUM(cliente_novo) AS cliente_novo,SUM(faturamento_realizado) AS faturamento_realizado,
                    SUM(visitas_realizado) as visitas,data_inclusao,filial 
                    FROM ({$visitas} UNION {$novos_clientes} UNION {$faturamento_clientes} )AS agrupado WHERE {$condicao} ";      
   
        $query.= "GROUP BY
                    agrupado.produto,
                    agrupado.gestor,                    
                    agrupado.data_inclusao,
                    agrupado.filial";
        
        if(!$agrupado){
            $query.=',agrupado.cliente';
        }
        return $this->query($query);
    }

    function inserir_objetivos_clientes($agrupado = false,$trava = false,$mes_ano = false){
        $this->ObjetivoComercialExcecao = ClassRegistry::init("ObjetivoComercialExcecao");
        $this->ObjetivoComercial = ClassRegistry::init("ObjetivoComercial");
        $dadosConsulta = $this->agrupar_faturamento_novos_clientes_visitas($agrupado,$trava,$mes_ano);
        $cont_analitico_inc = 0;
        $cont_analitico_alt = 0;
        $sucesso = true;
        try {           
            $this->query('BEGIN TRANSACTION');
            foreach ($dadosConsulta as $chave => $dado) {
                $dado = $this->formata_dados($dado);
                $conditions = $this->formata_conditions($dado);
         
                $localiza_objetivo = $this->ObjetivoComercial->find('first',array('conditions' => $conditions));
                $dados_excecoes = $this->ObjetivoComercialExcecao->verifica_excecoes_clientes($dado);
                if($localiza_objetivo['ObjetivoComercial']['codigo']){
                    //Insere objetivos com excecao de gestores              
                    if(isset($dados_excecoes[0]['excecao']) && is_array($dados_excecoes[0]['excecao'])){
                        foreach ($dados_excecoes[0]['excecao'] as $key => $dado) {  
                            $conditions_cliente = $this->formata_conditions_cliente($dado);
                            $combinacao_existe = $this->find('first',array('conditions' => $conditions_cliente)); 
                            
                            if($combinacao_existe){
                                $clienteCarregado = $this->carregar($combinacao_existe['ObjetivoComercialCliente']['codigo']);
                                $altera_dados['ObjetivoComercialCliente'] = $dado;
                                $altera_dados['ObjetivoComercialCliente']['codigo'] = $clienteCarregado['ObjetivoComercialCliente']['codigo'];          
                                
                                if(!$this->atualizar($altera_dados)){
                                    throw new Exception("Erro ao atualizar com combinacao existente e excecao de gestor");
                                    $cont_analitico_alt -= 1;
                                }
                                $cont_analitico_alt += 1;                           
                            }else{                          
                                $inclui_dados['ObjetivoComercialCliente'] = $dado;
                                if(!$this->incluir($inclui_dados)){
                                    throw new Exception("Erro ao incluir sem combinacao existente e excecao de gestor");
                                    $cont_analitico_inc -= 1;
                                }
                                $cont_analitico_inc += 1;
                            }
                        }                   
                    }else{
                        $conditions['codigo_cliente'] = $dados_excecoes[0]['ObjetivoComercialCliente']['codigo_cliente'];
                        $conditions['excecao'] = 0;
                        if(!isset($conditions['codigo_produto'])){
                            $conditions['codigo_produto'] = $dados_excecoes[0]['ObjetivoComercialCliente']['produto'];
                        }                   
                        $combinacao_existe = $this->find('first',array('conditions' => $conditions)); 
                        if($combinacao_existe){
                            if(!$this->atualizar($dados_excecoes[0])){
                                throw new Exception("Erro ao atualizar objetivos com combinacao e sem excecao");
                                $cont_analitico_alt -= 1;
                            }   
                            $cont_analitico_alt += 1;
                        }else{
                            $this->exclui_gestores_com_excecao($dados_excecoes[0]);
                  
                            if(!$this->incluir($dados_excecoes[0])){
                                throw new Exception("Erro ao incluir objetivos sem combinacao e sem excecao");
                                $cont_analitico_inc -= 1;
                            }
                            $cont_analitico_inc += 1;
                            
                        }   
                    }
                }
                    
            }
            
            if(!$this->ObjetivoComercialExcecao->excluir_objetivos_clientes_sem_excecao($dadosConsulta)){
                throw new Exception("Erro ao excluir objetivos");                
            }
            
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            $sucesso = false;
        }
 
        return compact('cont_analitico_inc','cont_analitico_alt','sucesso');
    }

    function formata_dados($dado){
        $mesAnos = explode('-',$dado[0]['data_inclusao']);
        $dado[0]['mes'] = $mesAnos[0];
        $dado[0]['ano'] = $mesAnos[1];
        $dado[0]['data_inclusao'] = date('Y-m-d H:i:s');
        $dado[0]['codigo_cliente'] = $dado[0]['cliente'];
        $dado[0]['codigo_endereco_regiao'] = $dado[0]['filial'];
        $dado[0]['codigo_gestor'] = $dado[0]['gestor'];
        $dado[0]['codigo_gestor_origem'] = $dado[0]['gestor'];
        $dado[0]['excecao'] = 0;
        $dado[0]['codigo_produto'] = $dado[0]['produto'];
        $dado[0]['visitas'] = $dado[0]['visitas'];
        return $dado;
    }

    function formata_conditions($dado){
        $conditions = array(                        
            'codigo_endereco_regiao' => $dado[0]['filial'],
            'mes' => $dado[0]['mes'],
            'ano' => $dado[0]['ano'],
            'codigo_gestor' => $dado[0]['gestor'],
        );

        if($dado[0]['codigo_produto'] != 30){
            $conditions['codigo_produto'] = $dado[0]['codigo_produto'];
        }   
        return $conditions;    
    }

    function formata_conditions_cliente($dado){
        return array(
            'codigo_cliente' => $dado['cliente'],
            'codigo_produto' => $dado['produto'],
            'mes' => $dado['mes'],
            'ano' => $dado['ano'],
            'codigo_gestor' => $dado['codigo_gestor'],
            'excecao' => 1
        ); 
    }
	
}
?>