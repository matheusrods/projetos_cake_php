<?php
class QuantitativoPorMedicoController extends AppController {
    public $name = 'QuantitativoPorMedico';
    public $helpers = array('BForm', 'Html', 'Ajax');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('retorna_codigo_grupo_economico', 'sub_filtro_cliente_funcionario'));
    }
    
    var $uses = array(
    	'Atestado', 
    	'AtestadoCid',
    	'Medico',
    	'GrupoEconomicoCliente',
    	'ClienteFuncionario'
    );
    
    public function index() {
    	$this->pageTitle = 'Atestados - Quantitativo por Médico';
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
  		
  		$sub_query = "
			select 
				count(1)
			from 
				atestados A
				inner join cliente_funcionario CF ON (CF.codigo = A.codigo_cliente_funcionario)
				inner join grupos_economicos_clientes GEC ON (GEC.codigo_cliente = CF.codigo_cliente)
  				inner join funcionario_setores_cargos FSC ON (FSC.codigo_cliente_funcionario = CF.codigo AND (FSC.data_fim is null OR FSC.data_fim = ''))
			where 
				A.codigo_medico = Medico.codigo AND
				GEC.codigo_grupo_economico = GrupoEconomicoCliente.codigo_grupo_economico
  		";
  		
  		$sub_query .= $this->Atestado->subquery_converteFiltroEmCondition($filtros);
  		
  		pr($filtros);
  		pr($sub_query);
  		
  		if(is_numeric($codigo_grupo_economico)) {
  			
  			$options['recursive'] = '-1';
  			$options['conditions'] = $this->Atestado->converteFiltroEmCondition($filtros);
  			$options['conditions'] = $options['conditions'] + array("GrupoEconomicoCliente.codigo_grupo_economico = {$codigo_grupo_economico}");
  		
  			$options['fields'] = array(
				'DISTINCT Medico.codigo',
				'Medico.codigo',
				'Medico.nome',
  				'ConselhoProfissional.descricao',
				'Medico.numero_conselho',
				'Medico.conselho_uf',
				'('.$sub_query.') as qtd'
  			);
  			 
  			$options['joins'] = array(
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
  				),
  				array(
  					'table' => 'medicos',
  					'alias' => 'Medico',
  					'type' => 'inner',
  					'conditions' => 'Medico.codigo = Atestado.codigo_medico'
  				),
				array(
					'table' => 'conselho_profissional',
  					'alias' => 'ConselhoProfissional',
  					'type' => 'inner',
  					'conditions' => 'ConselhoProfissional.codigo = Medico.codigo_conselho_profissional'
  				)  					
  			);
  			
  			$this->set('listagem', $this->Atestado->find('all', $options));
  		} else {
  			$this->set('listagem', array());
  		}
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