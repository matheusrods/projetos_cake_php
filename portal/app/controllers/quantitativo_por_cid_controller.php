<?php
class QuantitativoPorCidController extends AppController {
    public $name = 'QuantitativoPorCid';
    public $helpers = array('BForm', 'Html', 'Ajax', 'Highcharts');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('retorna_codigo_grupo_economico', 'sub_filtro_cliente_funcionario', 'grafico_setor', 'grafico_cargo', 'exportar'));
    }
    
    var $uses = array(
    	'Atestado', 
    	'AtestadoCid',
    	'Medico',
    	'GrupoEconomicoCliente',
    	'ClienteFuncionario'
    );
    
    public function index() {
    	$this->pageTitle = 'Atestados - Quantitativo por Cid';
    	$this->data['Atestado'] = $this->Filtros->controla_sessao($this->data, 'Atestado');
    	
   		$lista_unidades = array();
   		$lista_cargos = array();
   		$lista_setores = array();
   		$lista_funcionarios = array();
    	
    	$codigo_unidade = isset($this->data['Atestado']['codigo_unidade']) ? $this->data['Atestado']['codigo_unidade'] : '';
    	
    	if($this->BAuth->user('codigo_cliente')) {
    		$codigo_unidade = $this->BAuth->user('codigo_cliente');
    	}
    	
    	$codigo_funcionario = isset($this->data['Atestado']['codigo_funcionario']) ? $this->data['Atestado']['codigo_funcionario'] : '';
    	$codigo_setor = isset($this->data['Atestado']['codigo_setor']) ? $this->data['Atestado']['codigo_setor'] : '';
    	$codigo_cargo = isset($this->data['Atestado']['codigo_cargo']) ? $this->data['Atestado']['codigo_cargo'] : '';
    	$codigo_cliente = isset($this->data['Atestado']['codigo_cliente']) ? $this->data['Atestado']['codigo_cliente'] : '';
    	 
    	$this->set(compact('lista_funcionarios', 'lista_setores', 'lista_cargos', 'lista_unidades', 'codigo_unidade', 'codigo_cliente', 'codigo_cargo', 'codigo_setor', 'codigo_funcionario'));
    	$this->set('codigo_grupo_economico', (isset($codigo_grupo_economico) ? $codigo_grupo_economico : ''));
    }
    
    
  	public function listagem($codigo_grupo_economico = null) {
  		
  		/***************************************************
  		 * validacao adicionado para evitar o cliente de
  		 * burlar o acesso e ver dados de outros clientes;
  		 ***************************************************/
  		
  		if(!is_null($this->BAuth->user('codigo_cliente'))) {
  			$dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $this->BAuth->user('codigo_cliente')), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
  			$codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];
  		}
  		
  		$this->layout = 'ajax';
  		
  		$filtros = $this->Filtros->controla_sessao($this->data, 'Atestado');
      if (!empty($filtros['codigo_unidade']))
      {

    		$sub_query = "
    			select 
    				count(1)
    			from 
    				atestados A
    				inner join cliente_funcionario CF ON (CF.codigo = A.codigo_cliente_funcionario)
    				inner join grupos_economicos_clientes GEC ON (GEC.codigo_cliente = CF.codigo_cliente)
      				inner join funcionario_setores_cargos FSC ON (FSC.codigo_cliente_funcionario = CF.codigo AND (FSC.data_fim is null OR FSC.data_fim = ''))
    				inner join atestados_cid AC ON (AC.codigo_atestado = A.codigo)
    				inner join cid C ON (C.codigo = AC.codigo_cid)
    			where
    				AC.codigo_cid = Cid.codigo AND
    				GEC.codigo_grupo_economico = GrupoEconomicoCliente.codigo_grupo_economico
    		";
    		
    		$sub_query .= $this->Atestado->subquery_converteFiltroEmCondition($filtros);
    		
    		if(is_numeric($codigo_grupo_economico)) {
    			
    			$options['recursive'] = '-1';
    			$options['conditions'] = $this->Atestado->converteFiltroEmCondition($filtros);
    			$options['conditions'] = $options['conditions'] + array("GrupoEconomicoCliente.codigo_grupo_economico = {$codigo_grupo_economico}");
    		
    			$options['fields'] = array(
    				'DISTINCT Cid.codigo_cid10',
            'Cid.descricao',
    				'('.$sub_query.') as qtd'
    			);
    			 
    			$options['joins'] = array(
    				array(
    					'table' => 'atestados_cid',
    					'alias' => 'AtestadoCid',
    					'type' => 'inner',
    					'conditions' => 'AtestadoCid.codigo_atestado = Atestado.codigo'
    				),
  				array(
  					'table' => 'cid',
  					'alias' => 'Cid',
  					'type' => 'inner',
  					'conditions' => 'Cid.codigo = AtestadoCid.codigo_cid'
  				),
    				array(
    					'table' => 'cliente_funcionario',
    					'alias' => 'ClienteFuncionario',
    					'type' => 'inner',
    					'conditions' => 'ClienteFuncionario.codigo = Atestado.codigo_cliente_funcionario'
    				),
    				array(
    					'table' => 'cliente',
    					'alias' => 'Cliente',
    					'type' => 'inner',
    					'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente'
    				),
    				array(
    					'table' => 'grupos_economicos_clientes',
    					'alias' => 'GrupoEconomicoCliente',
    					'type' => 'inner',
    					'conditions' => 'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo'
    				),
    				array(
    					'table' => 'funcionario_setores_cargos',
    					'alias' => 'FuncionarioSetorCargo',
    					'type' => 'inner',
    					'conditions' => array(
    						'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
    						"(FuncionarioSetorCargo.data_fim is null OR FuncionarioSetorCargo.data_fim = '')"
    					)
    				),
    				array(
    					'table' => 'funcionarios',
    					'alias' => 'Funcionario',
    					'type' => 'inner',
    					'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
    				)
    			);
    			
    			$options['order'] = 'qtd DESC';
    			
    			$listagem = $this->Atestado->find('all', $options);
    			
    			####################### monta grafico pizza #################################
    			foreach ($listagem as $k => $item) {
    				$cod_cid10 = trim($item['Cid']['codigo_cid10']);
    				$series_pizza[] = array('name' => "'{$cod_cid10}'", 'values' => $item[0]['qtd']);
    			}
    			##############################################################################
    			
    			$this->set(compact('listagem', 'series_pizza'));
    		} else {
    			$this->set('listagem', array());
    			$this->set('series_pizza', array());
    		}
    		
      }

  		$this->set(compact('codigo_grupo_economico'));
  	}
  	
  	public function grafico_setor($codigo_grupo_economico) {
  		$this->layout = 'ajax';
  		$filtros = $this->Filtros->controla_sessao($this->data, 'Atestado');
  		
  		$sub_query = "
			select
				count(1)
			from
				atestados A
				inner join cliente_funcionario CF ON (CF.codigo = A.codigo_cliente_funcionario)
				inner join grupos_economicos_clientes GEC ON (GEC.codigo_cliente = CF.codigo_cliente)
  				inner join funcionario_setores_cargos FSC ON (FSC.codigo_cliente_funcionario = CF.codigo AND (FSC.data_fim is null OR FSC.data_fim = ''))
				inner join atestados_cid AC ON (AC.codigo_atestado = A.codigo)
				inner join cid C ON (C.codigo = AC.codigo_cid)
			where
				C.codigo_cid10 = Cid.codigo_cid10 AND
				FSC.codigo_setor = FuncionarioSetorCargo.codigo_setor AND
				GEC.codigo_grupo_economico = GrupoEconomicoCliente.codigo_grupo_economico
  		";
  		
  		$sub_query .= $this->Atestado->subquery_converteFiltroEmCondition($filtros);
  		
  		if(is_numeric($codigo_grupo_economico)) {
  				
  			$options['recursive'] = '-1';
  			$options['conditions'] = $this->Atestado->converteFiltroEmCondition($filtros);
  			$options['conditions'] = $options['conditions'] + array("GrupoEconomicoCliente.codigo_grupo_economico = {$codigo_grupo_economico}");
  		
  			$options['fields'] = array(
  					'DISTINCT Cid.codigo_cid10',
  					'Setor.descricao',
  					'('.$sub_query.') as qtd'
  			);
  		
  			$options['joins'] = array(
  					array(
  							'table' => 'atestados_cid',
  							'alias' => 'AtestadoCid',
  							'type' => 'inner',
  							'conditions' => 'AtestadoCid.codigo_atestado = Atestado.codigo'
  					),
  					array(
  							'table' => 'cid',
  							'alias' => 'Cid',
  							'type' => 'inner',
  							'conditions' => 'Cid.codigo = AtestadoCid.codigo_cid'
  					),
  					array(
  							'table' => 'cliente_funcionario',
  							'alias' => 'ClienteFuncionario',
  							'type' => 'inner',
  							'conditions' => 'ClienteFuncionario.codigo = Atestado.codigo_cliente_funcionario'
  					),
  					array(
  							'table' => 'cliente',
  							'alias' => 'Cliente',
  							'type' => 'inner',
  							'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente'
  					),
  					array(
  							'table' => 'grupos_economicos_clientes',
  							'alias' => 'GrupoEconomicoCliente',
  							'type' => 'inner',
  							'conditions' => 'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo'
  					),
  					array(
  							'table' => 'funcionario_setores_cargos',
  							'alias' => 'FuncionarioSetorCargo',
  							'type' => 'inner',
  							'conditions' => array(
  									'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
  									"(FuncionarioSetorCargo.data_fim is null OR FuncionarioSetorCargo.data_fim = '')"
  							)
  					),
  					array(
  							'table' => 'setores',
  							'alias' => 'Setor',
  							'type' => 'inner',
  							'conditions' => 'Setor.codigo = FuncionarioSetorCargo.codigo_setor'
  					)  					
  			);
  			
  			$options['order'] = 'qtd DESC';
  			$options['limit'] = '3';
  			
  			$listagem = $this->Atestado->find('all', $options);
  			
  			if(count($listagem)) {
  				foreach($listagem as $k => $campo) {
  					$qtd[$campo['Setor']['descricao']][$k] = $campo[0]['qtd'];
  						
  					$cid = trim($campo['Cid']['codigo_cid10']);
  					$descricao[] = "'{$cid}'";
  				}
  				
  				if(isset($descricao) && count($descricao)) {
  					for($i = 0; $i < count($descricao); $i++) {
  						foreach($qtd as $k_setor => $indices) {
  					
  							ksort($indices);
  							$qtd[$k_setor] = $indices;
  					
  							if(!isset($qtd[$k_setor][$i])) {
  								$qtd[$k_setor][$i] = 0;
  							}
  						}
  					}
  					
  					$dadosGrafico['eixo_x'] = $descricao;
  					foreach($qtd as $nome => $valores) {
  						$dadosGrafico['series'][] = array('name' => "'{$nome}'", 'values' => $valores);
  					}  					
  				} else {
  					$dadosGrafico = array();
  				}

  			} else {
  				$dadosGrafico = array();
  			}
  			
  			$this->set(compact('listagem', 'dadosGrafico'));
  		} else {
  			$this->set('listagem', array());
  		}
  	}
  	
  	public function grafico_cargo($codigo_grupo_economico) {
  		$this->layout = 'ajax';
  		$filtros = $this->Filtros->controla_sessao($this->data, 'Atestado');
  		
  		$sub_query = "
			select
				count(1)
			from
				atestados A
				inner join cliente_funcionario CF ON (CF.codigo = A.codigo_cliente_funcionario)
				inner join grupos_economicos_clientes GEC ON (GEC.codigo_cliente = CF.codigo_cliente)
  				inner join funcionario_setores_cargos FSC ON (FSC.codigo_cliente_funcionario = CF.codigo AND (FSC.data_fim is null OR FSC.data_fim = ''))
				inner join atestados_cid AC ON (AC.codigo_atestado = A.codigo)
				inner join cid C ON (C.codigo = AC.codigo_cid)
			where
				C.codigo_cid10 = Cid.codigo_cid10 AND
				FSC.codigo_cargo = FuncionarioSetorCargo.codigo_cargo AND
				GEC.codigo_grupo_economico = GrupoEconomicoCliente.codigo_grupo_economico
  		";
  		
  		$sub_query .= $this->Atestado->subquery_converteFiltroEmCondition($filtros);
  		
  		if(is_numeric($codigo_grupo_economico)) {
  				
  			$options['recursive'] = '-1';
  			$options['conditions'] = $this->Atestado->converteFiltroEmCondition($filtros);
  			$options['conditions'] = $options['conditions'] + array("GrupoEconomicoCliente.codigo_grupo_economico = {$codigo_grupo_economico}");
  		
  			$options['fields'] = array(
  					'DISTINCT Cid.codigo_cid10',
  					'Cargo.descricao',
  					'('.$sub_query.') as qtd'
  			);
  		
  			$options['joins'] = array(
  					array(
  							'table' => 'atestados_cid',
  							'alias' => 'AtestadoCid',
  							'type' => 'inner',
  							'conditions' => 'AtestadoCid.codigo_atestado = Atestado.codigo'
  					),
  					array(
  							'table' => 'cid',
  							'alias' => 'Cid',
  							'type' => 'inner',
  							'conditions' => 'Cid.codigo = AtestadoCid.codigo_cid'
  					),
  					array(
  							'table' => 'cliente_funcionario',
  							'alias' => 'ClienteFuncionario',
  							'type' => 'inner',
  							'conditions' => 'ClienteFuncionario.codigo = Atestado.codigo_cliente_funcionario'
  					),
  					array(
  							'table' => 'cliente',
  							'alias' => 'Cliente',
  							'type' => 'inner',
  							'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente'
  					),
  					array(
  							'table' => 'grupos_economicos_clientes',
  							'alias' => 'GrupoEconomicoCliente',
  							'type' => 'inner',
  							'conditions' => 'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo'
  					),
  					array(
  							'table' => 'funcionario_setores_cargos',
  							'alias' => 'FuncionarioSetorCargo',
  							'type' => 'inner',
  							'conditions' => array(
  									'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
  									"(FuncionarioSetorCargo.data_fim is null OR FuncionarioSetorCargo.data_fim = '')"
  							)
  					),
  					array(
  							'table' => 'cargos',
  							'alias' => 'Cargo',
  							'type' => 'inner',
  							'conditions' => 'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo'
  					)  					
  			);
  			
  			$options['order'] = 'qtd DESC';
  			$options['limit'] = '3';
  			
  			$listagem = $this->Atestado->find('all', $options);
  			
  			if(count($listagem)) {
  				foreach($listagem as $k => $campo) {
  					$qtd[$campo['Cargo']['descricao']][$k] = $campo[0]['qtd'];
  						
  					$cid = trim($campo['Cid']['codigo_cid10']);
  					$descricao[] = "'{$cid}'";
  				}
  				
  				if(isset($descricao) && count($descricao)) {
  					for($i = 0; $i < count($descricao); $i++) {
  						foreach($qtd as $k_cargo => $indices) {
  				
  							ksort($indices);
  							$qtd[$k_cargo] = $indices;
  				
  							if(!isset($qtd[$k_cargo][$i])) {
  								$qtd[$k_cargo][$i] = 0;
  							}
  						}
  					}
  				
  					$dadosGrafico['eixo_x'] = $descricao;
  					foreach($qtd as $nome => $valores) {
  						$dadosGrafico['series'][] = array('name' => "'{$nome}'", 'values' => $valores);
  					}
  				} else {
  					$dadosGrafico = array();
  				}  				
  			} else {
  				$dadosGrafico = array();
  			}
  			
  			$this->set(compact('listagem', 'dadosGrafico'));
  		} else {
  			$this->set('listagem', array());
  		}
  	}
  	
  	public function exportar($codigo_grupo_economico) {
  		
  		$filtros = $this->Filtros->controla_sessao($this->data, 'Atestado');
  		
  		$sub_query = "
			select
				count(1)
			from
				atestados A
				inner join cliente_funcionario CF ON (CF.codigo = A.codigo_cliente_funcionario)
				inner join grupos_economicos_clientes GEC ON (GEC.codigo_cliente = CF.codigo_cliente)
  				inner join funcionario_setores_cargos FSC ON (FSC.codigo_cliente_funcionario = CF.codigo AND (FSC.data_fim is null OR FSC.data_fim = ''))
				inner join atestados_cid AC ON (AC.codigo_atestado = A.codigo)
				inner join cid C ON (C.codigo = AC.codigo_cid)
			where
				AC.codigo_cid = Cid.codigo AND
				GEC.codigo_grupo_economico = GrupoEconomicoCliente.codigo_grupo_economico
  		";
  		
  		$sub_query .= $this->Atestado->subquery_converteFiltroEmCondition($filtros);
  		
  		$options['recursive'] = '-1';
  		$options['conditions'] = $this->Atestado->converteFiltroEmCondition($filtros);
  		$options['conditions'] = $options['conditions'] + array("GrupoEconomicoCliente.codigo_grupo_economico = {$codigo_grupo_economico}");
  		
  		$options['fields'] = array(
  			'DISTINCT Cid.codigo_cid10',
  			'('.$sub_query.') as qtd'
  		);
  		
  		$options['joins'] = array(
  			array(
  				'table' => 'atestados_cid',
  				'alias' => 'AtestadoCid',
  				'type' => 'inner',
  				'conditions' => 'AtestadoCid.codigo_atestado = Atestado.codigo'
  			),
  			array(
  				'table' => 'cid',
  				'alias' => 'Cid',
  				'type' => 'inner',
  				'conditions' => 'Cid.codigo = AtestadoCid.codigo_cid'
  			),
  			array(
  				'table' => 'cliente_funcionario',
  				'alias' => 'ClienteFuncionario',
  				'type' => 'inner',
  				'conditions' => 'ClienteFuncionario.codigo = Atestado.codigo_cliente_funcionario'
  			),
  			array(
  				'table' => 'cliente',
  				'alias' => 'Cliente',
  				'type' => 'inner',
  				'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente'
  			),
  			array(
  				'table' => 'grupos_economicos_clientes',
  				'alias' => 'GrupoEconomicoCliente',
  				'type' => 'inner',
  				'conditions' => 'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo'
  			),
  			array(
  				'table' => 'funcionario_setores_cargos',
  				'alias' => 'FuncionarioSetorCargo',
  				'type' => 'inner',
  				'conditions' => array(
  					'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
  					"(FuncionarioSetorCargo.data_fim is null OR FuncionarioSetorCargo.data_fim = '')"
  				)
  			),
  			array(
  				'table' => 'funcionarios',
  				'alias' => 'Funcionario',
  				'type' => 'inner',
  				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
  			)
  		);
  				
  		$listagem = $this->Atestado->find('all', $options);
  		
  		$nome_arquivo = date('YmdHis').'_cid.csv';
  			
  		ob_clean();
  		header('Content-Encoding: UTF-8');
  		header('Content-type: text/csv; charset=UTF-8');
  		header(sprintf('Content-Disposition: attachment; filename="%s"', $nome_arquivo));
  		header('Pragma: no-cache');
  		echo '"CID";"Quantidade";'."\n";
  			
  		if(!empty($listagem)) {
  			foreach ($listagem as $key => $dado) {
  					
  				$linha = $dado['Cid']['codigo_cid10'].';';
  				$linha .= $dado[0]['qtd'].';';
  					
  				$linha .= "\n";
  				echo $linha;
  			}
  		}
  		
  		exit;
  		
  	}
  	
  	public function retorna_codigo_grupo_economico() {
  		
		/***************************************************
		 * validacao adicionado para evitar o cliente de 
		 * burlar o acesso e ver dados de outros clientes;
		 ***************************************************/
  		if(!is_null($this->BAuth->user('codigo_cliente'))) {
  			$codigo_unidade = $this->BAuth->user('codigo_cliente');
  		} else {
  			$codigo_unidade = $this->params['form']['codigo_unidade'];
  		}
  		
  		$dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_unidade), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
  		 
  		echo json_encode(array('codigo_grupo_economico' => $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico']));
  		exit;
  	}
  	
  	public function sub_filtro_cliente_funcionario($codigo_grupo_economico, $codigo_unidade) {
  		
  		/***************************************************
  		 * validacao adicionado para evitar o cliente de
  		 * burlar o acesso e ver dados de outros clientes;
  		 ***************************************************/
  		if(!is_null($this->BAuth->user('codigo_cliente'))) {
  			$codigo_unidade = $this->BAuth->user('codigo_cliente');
  			
  			$dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_unidade), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
  			$codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];
  		}
  		
  		
    	$this->data['Atestado'] = $this->Filtros->controla_sessao($this->data, 'Atestado');
    	
		if(isset($codigo_grupo_economico) && $codigo_grupo_economico) {
			$lista_unidades = $this->GrupoEconomicoCliente->retorna_lista_de_unidades_de_um_grupo_economico($codigo_grupo_economico);
			$lista_cargos = $this->GrupoEconomicoCliente->listaCargos($codigo_grupo_economico);
			$lista_setores = $this->GrupoEconomicoCliente->listaSetores($codigo_grupo_economico);
			$lista_funcionarios = $this->GrupoEconomicoCliente->listaFuncionarios($codigo_grupo_economico);
			
    	} else {
    		$lista_unidades = array();
    		$lista_cargos = array();
    		$lista_setores = array();
    		$lista_funcionarios = array();
    	}
    	
    	$codigo_funcionario = isset($this->data['Atestado']['codigo_funcionario']) ? $this->data['Atestado']['codigo_funcionario'] : '';
    	$codigo_setor = isset($this->data['Atestado']['codigo_setor']) ? $this->data['Atestado']['codigo_setor'] : '';
    	$codigo_cargo = isset($this->data['Atestado']['codigo_cargo']) ? $this->data['Atestado']['codigo_cargo'] : '';
    	$codigo_cliente = isset($this->data['Atestado']['codigo_cliente']) ? $this->data['Atestado']['codigo_cliente'] : '';
    	
    	$this->set(compact('lista_funcionarios', 'lista_setores', 'lista_cargos', 'lista_unidades', 'codigo_unidade', 'codigo_cliente', 'codigo_cargo', 'codigo_setor', 'codigo_funcionario', 'codigo_grupo_economico'));
    }  	
    
}