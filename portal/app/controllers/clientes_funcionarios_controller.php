<?php
class ClientesFuncionariosController extends AppController {
	public $name = 'ClientesFuncionarios';
	public $helpers = array('BForm', 'Html', 'Ajax', 'Highcharts');
	var $uses = array(  'ClienteFuncionario', 'GrupoEconomicoCliente', 'PedidoExame', 'Cliente', 'FuncionarioSetorCargo' , 'Esocial', 'Funcionario');
	
	
	public function beforeFilter() {
		parent::beforeFilter();
		// $this->BAuth->allow(array('atualiza_status', 'selecao_funcionarios', 'listagem', 'sub_filtro_cliente_funcionario', 'retorna_codigo_grupo_economico', 'listagem_matriculas', 'salva_matricula', 'edita_matricula', 'insere_setor_cargo', 'edita_setor_cargo'));
		$this->BAuth->allow(array('autocomplete_funcionario','vidas_listagem','existe_alerta_hierarquia_pendente', 'listagem_matriculas','get_tomador'));
	}

	function atualiza_status($codigo, $status){
		$this->layout = 'ajax';

		$this->data['ClienteFuncionario']['codigo'] = $codigo;
		$this->data['ClienteFuncionario']['ativo'] = ($status == 0) ? 1 : 0;

		if ($this->ClienteFuncionario->atualizar($this->data, false)) {
			print 1;
		} else {
			print 0;
		}
		
		$this->render(false,false);
		// 0 -> ERRO | 1 -> SUCESSO
	}

	public function selecao_funcionarios($codigo_unidade =  null) {
		$this->pageTitle = 'Emissão de Pedidos';
		
		$filtros = $this->Filtros->controla_sessao($this->data, 'ClienteFuncionario');		

		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			if(empty($filtros['codigo_cliente'])) {
				$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
			}
		}

		$filtros['codigo_cliente'] = (isset($this->authUsuario['Usuario']['multicliente'])) ? $this->normalizaCodigoCliente($filtros['codigo_cliente']) : $filtros['codigo_cliente'];
		$this->data['ClienteFuncionario'] = $filtros;
 
		// die(debug($this->data['ClienteFuncionario']));
		// limpa e ajusta campo cpf sem traços e pontos
		if(isset($filtros['cpf'])){	
			$filtros['cpf_funcionario'] = $filtros['cpf'];	  
		}

		if(isset($filtros['cpf_funcionario'])){	
			$filtros['cpf'] = $filtros['cpf_funcionario'];	  
		}

		$status_matricula = array( 'todos' => 'Todos', '1' => 'Ativos', '0' => 'Inativos', '2' => 'Férias', '3' => 'Afastado');
		$this->set(compact('status_matricula'));
		$this->carrega_combos_grupo_economico('ClienteFuncionario');

	}
	
	public function listagem() {
		$this->layout = 'ajax';

		$this->loadModel('FuncionarioSetorCargo');		
		$filtros = $this->Filtros->controla_sessao($this->data, 'ClienteFuncionario');
		$authUsuario = $this->BAuth->user();

		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			if(empty($filtros['codigo_cliente'])) {
				$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
			}
		}

		$listagem = array();
		if (!empty($filtros['codigo_cliente'])) {
			
			$filtros['codigo_cliente'] = $this->normalizaCodigoCliente($filtros['codigo_cliente']);
			$dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $filtros['codigo_cliente']), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
			
			if(isset($dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'])) {
				$codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];
			}
			// PC-3191 - limpa e ajusta campo cpf sem traços e pontos
			if(!empty($filtros['cpf'])){
				$cpf1 = trim($filtros['cpf']);
				$cpf2 = str_replace(".", "", $cpf1);
				$cpf3 = str_replace("-", "", $cpf2);		  
				$filtros['cpf'] = $cpf3;		  
			  }

			if(!empty($filtros['cpf_funcionario'])){	
				$filtros['cpf'] = $filtros['cpf_funcionario'];	  
			}

			$conditions = $this->FuncionarioSetorCargo->converteFiltrosEmConditions($filtros);
			// debug($filtros);
			// debug($conditions);
			if($conditions === false){
				$this->set(compact('listagem')); 
			}else{

				ini_set('memory_limit', '-1');
				ini_set('max_execution_time', 300); // 5min

				//condicao incluida para nao deixar apresentar um setor e cargo que esteja com a data fim do setor/cargo preenchida
				// $conditions['OR'] = array('FuncionarioSetorCargo.data_fim IS NULL','ClienteFuncionario.ativo'=>'0');
				$conditions[] = array('FuncionarioSetorCargo.data_fim IS NULL');

				$order = array('Cliente.razao_social', 'Setor.descricao', 'Cargo.descricao', 'Funcionario.nome');
				//$listagem = $this->FuncionarioSetorCargo->find('all', compact('conditions', 'order'));

				$joins = array(
					array(
						'table' => 'cliente_funcionario',
						'alias' => 'ClienteFuncionario',
						'type' => 'INNER',
						'conditions' => 'ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario',
						),
					array(
						'table' => 'cliente',
						'alias' => 'Cliente',
						'type' => 'INNER',
						'conditions' => 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao',
						),
					array(
						'table' => 'funcionarios',
						'alias' => 'Funcionario',
						'type' => 'INNER',
						'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
						),
					array(
						'table' => 'setores',
						'alias' => 'Setor',
						'type' => 'INNER',
						'conditions' => 'Setor.codigo = FuncionarioSetorCargo.codigo_setor',
						),
					array(
						'table' => 'cargos',
						'alias' => 'Cargo',
						'type' => 'INNER',
						'conditions' => 'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo',
						),

					);

				$fields = array('Funcionario.nome','Funcionario.codigo','Cliente.codigo', 'Cliente.razao_social', 'Cliente.nome_fantasia','Cargo.codigo', 'Cargo.descricao','Setor.codigo', 'Setor.descricao', 'FuncionarioSetorCargo.codigo', 'FuncionarioSetorCargo.codigo_cliente_alocacao', 'FuncionarioSetorCargo.codigo_cliente_funcionario', 'ClienteFuncionario.ativo');

				$this->paginate['FuncionarioSetorCargo'] = array(
					'recursive' => -1,	
					'fields' => $fields,
					'joins' => $joins,
					'conditions' => $conditions,
					// 'limit' => 50,
					'order' => $order
					);

				// // pr($this->FuncionarioSetorCargo->find('sql', $this->paginate['FuncionarioSetorCargo']));

				// $listagem = $this->paginate('FuncionarioSetorCargo');
				
				$query = $this->FuncionarioSetorCargo->find('sql', $this->paginate['FuncionarioSetorCargo']);
				$listagem = $this->FuncionarioSetorCargo->query($query);				
				// debug($listagem);exit;

				$this->set(compact('listagem'));
				$this->set('selecao_em_massa', array('' => 'Selecionar Ação em Massa', '1' => 'Inclusão em Massa'));
				$this->set('codigo_grupo_economico', (isset($codigo_grupo_economico) ? $codigo_grupo_economico : ''));

				$this->Filtros->limpa_sessao($this->FuncionarioSetorCargo->name);
			}

		}

	}
	
	public function listagem_matriculas($codigo_cliente, $codigo_funcionario = null) {

		$this->layout = 'ajax';

		$conditions['ClienteFuncionario.codigo_funcionario'] = $codigo_funcionario;
		$conditions['ClienteFuncionario.codigo_cliente_matricula'] = $codigo_cliente;

		$this->paginate['ClienteFuncionario'] = array(
			'contain' => array(
				'FuncionarioSetorCargo',
				'FuncionarioSetorCargo.Setor',
				'FuncionarioSetorCargo.Cargo',
				'FuncionarioSetorCargo.Cliente'
				),
			'conditions'=> $conditions,
			'limit' => 50
			);
		
		// pr($this->ClienteFuncionario->find('sql', $this->paginate['ClienteFuncionario']));exit;		
		$funcionario_matriculas = $this->ClienteFuncionario->find('all', $this->paginate['ClienteFuncionario']);

		// pr($funcionario_matriculas);exit;

		// PREENCHE COM OS SETORES E CARGOS CONFORME HIERARQUIA
		foreach ($funcionario_matriculas as $key => $funcionario_matricula) {

			//verifica se existe funcionario setores cargos
			if(isset($funcionario_matricula['FuncionarioSetorCargo'])) {

				foreach ($funcionario_matricula['FuncionarioSetorCargo'] as $key2 => $funcionario_setor_cargo) {
					$bloqueado = $this->GrupoEconomicoCliente->findByCodigoCliente($funcionario_setor_cargo['codigo_cliente_alocacao'], array('bloqueado'));					
					
					//carrega o setor
					$setores = $this->ClienteFuncionario->Setor->lista_por_cliente($funcionario_setor_cargo['codigo_cliente_alocacao'], $bloqueado['GrupoEconomicoCliente']['bloqueado'], $funcionario_setor_cargo['codigo_setor']);
					$funcionario_matriculas[$key]['FuncionarioSetorCargo'][$key2]['Setores'] = $setores;
					
					$cargos = $this->ClienteFuncionario->Cargo->lista_cargo_por_cliente_setor($funcionario_setor_cargo['codigo_cliente_alocacao'], $funcionario_setor_cargo['codigo_setor'], $bloqueado['GrupoEconomicoCliente']['bloqueado'],$funcionario_setor_cargo['codigo_cargo']);
					$funcionario_matriculas[$key]['FuncionarioSetorCargo'][$key2]['Cargos'] = $cargos;
					 					
					//join do pedido
					$joins = array(
						array(
							'table' => 'itens_pedidos_exames',
							'alias' => 'ItemPedidoExame',
							'type' => 'INNER',
							'conditions' => array('ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo')
						),
						array(
							'table' => 'itens_pedidos_exames_baixa',
							'alias' => 'ItemPedidoExameBaixa',
							'type' => 'INNER',
							'conditions' => array('ItemPedidoExame.codigo = ItemPedidoExameBaixa.codigo_itens_pedidos_exames')	
						),
					);

					//pega os pedidos de exames
					$pedidos = $this->PedidoExame->find('first',array('joins' =>$joins,'conditions' => array('codigo_func_setor_cargo' => $funcionario_setor_cargo['codigo'])));	
					//seta o pedido
					$funcionario_matriculas[$key]['FuncionarioSetorCargo'][$key2]['PedidoExame'] = $pedidos['PedidoExame'];

				}//fim foreach fsc
			}//fim funcionario matricula
		}//fim foreach funcionario_matricula

		// pr($funcionario_matriculas);

		$this->set(compact('funcionario_matriculas'));

		$this->loadModel('GrupoEconomicoCliente');
		$bloqueado = $this->GrupoEconomicoCliente->findByCodigoCliente($codigo_cliente, array('bloqueado'));
		$bloqueado = $bloqueado['GrupoEconomicoCliente']['bloqueado'];
		$this->set(compact('bloqueado'));

		$matriz = $this->GrupoEconomicoCliente->find('first', array(
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => 'grupos_economicos',
					'alias' => 'GrupoEconomico',
					'type' => 'INNER',
					'conditions' => array(
						'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
						)	
					)
				),
			'conditions' => array(
				'GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente
				),
			'fields' => array(
				'GrupoEconomico.codigo_cliente'
				)
			)
		);

		$unidades = $this->ClienteFuncionario->Cliente->lista_por_cliente($matriz['GrupoEconomico']['codigo_cliente'], $bloqueado);

		//pega todas as unidades fiscais
		$unidades_fiscais = $this->ClienteFuncionario->Cliente->lista_por_cliente($matriz['GrupoEconomico']['codigo_cliente'], $bloqueado,1);

		$categoria_colaborador = $this->Esocial->getTabela01();//carregamento do campo Categoria de Colaborador

		$this->set(compact('bloqueado', 'unidades', 'codigo_funcionario', 'codigo_cliente','unidades_fiscais', 'categoria_colaborador'));
	}

	public function salva_matricula() {
		$this->autoRender = false;
		$retorno = -1;
		if($this->params['form']) {
			$this->loadModel('FuncionarioSetorCargo');
			$dados = $this->params['form']['dados'];

			if(!isset($dados[$this->ClienteFuncionario->name]['codigo'])) {
				$dados[$this->ClienteFuncionario->name]['ativo'] = 1;
			}
			$dados[$this->ClienteFuncionario->name]['matricula'] = trim($dados[$this->ClienteFuncionario->name]['matricula']);			
			if(!empty($dados[$this->ClienteFuncionario->name]['admissao'])) {
				$dados[$this->FuncionarioSetorCargo->name][0]['data_inicio'] = $dados[$this->ClienteFuncionario->name]['admissao'];
			}
			
			$codigo_funcionario = $dados['ClienteFuncionario']['codigo_funcionario'];

			if( $this->ClienteFuncionario->verificaMatriculaVazia($codigo_funcionario) ) {
				
				if( $this->ClienteFuncionario->salva_matricula($dados) ) {
					$retorno = array('type' => 'success', 'title' => 'Sucesso', 'text' => 'A matrícula foi incluída com sucesso.');
				} else {
					$msg_erro = "Não foi possível incluir a matrícula";
					if(!empty($this->ClienteFuncionario->validationErrors)){
						$msg_erro .= ": ".implode(',',$this->ClienteFuncionario->validationErrors);
					}
					$retorno = array('type' => 'warning', 'title' => 'Atenção', 'text' => $msg_erro);
				}
			} else {
				$retorno = array('type' => 'warning', 'title' => 'Atenção', 'text' => 'Não foi possível incluir nova matrícula, pois existe uma matrícula vazia.');
			}				
	    }//FINAL if $this->params['form']
		echo json_encode($retorno);
	}//FINAL FUNCTION salva_matricula

	public function edita_matricula() {
		$this->autoRender = false;
		$retorno = -1;

		if($this->params['form']) {
			$dados[$this->ClienteFuncionario->name] = $this->params['form']['dados'];
			$dados[$this->ClienteFuncionario->name]['matricula'] = trim($dados[$this->ClienteFuncionario->name]['matricula']);
			$this->ClienteFuncionario->set( $dados[$this->ClienteFuncionario->name] );

			if(!$this->ClienteFuncionario->validates()){
				$validationErrors = $this->ClienteFuncionario->invalidFields();

				if(isset($validationErrors['data_demissao'])){
					$retorno = array('text' => $this->ClienteFuncionario->validationErrors['data_demissao']);
				}elseif(isset($validationErrors['ativo'])){
					$retorno = array('text' => $this->ClienteFuncionario->validationErrors['ativo']);
				}elseif(isset($validationErrors['matricula'])){
					$retorno = array('text' => $this->ClienteFuncionario->validationErrors['matricula']);
				}

				$retorno['type'] 	= 'warning';
				$retorno['title'] 	= 'Atenção';
				
			}else{
				if($retorno = $this->ClienteFuncionario->atualizar($dados)) {
					if(!empty($dados[$this->ClienteFuncionario->name]['data_demissao'])) {
						$date = DateTime::createFromFormat('d/m/Y', $dados[$this->ClienteFuncionario->name]['data_demissao']);

						// FINALIZA OS SETORES E CARGOS ATRELADOS A ESTA MATRICULA
						$this->ClienteFuncionario->FuncionarioSetorCargo->updateAll(
							array(
								$this->ClienteFuncionario->FuncionarioSetorCargo->name.'.data_fim' => '\''.$date->format('Y-m-d').'\''
								), 
							array(
								'AND' => array(
									$this->ClienteFuncionario->FuncionarioSetorCargo->name.'.codigo_cliente_funcionario' => $dados[$this->ClienteFuncionario->name]['codigo'],
									$this->ClienteFuncionario->FuncionarioSetorCargo->name.'.data_fim =' => null
									)
								)
							);
					}
				}
			}
		}//FINAL IF $this->params['form']
		echo json_encode($retorno);
	}//FINAL FUNCTION edita_matricula

	public function insere_setor_cargo() {
		$this->autoRender = false;
		$retorno = -1;
		if($this->params['form']) {
			//$this->ClienteFuncionario->FuncionarioSetorCargo->finaliza_setores_cargos_em_aberto($this->params['form']['dados']['codigo_cliente_funcionario']);
			$dados[$this->ClienteFuncionario->FuncionarioSetorCargo->name] = $this->params['form']['dados'];
			$retorno = ($this->ClienteFuncionario->FuncionarioSetorCargo->incluir($dados));
		}
		echo json_encode($retorno);
	}

	public function edita_setor_cargo() {
		$this->autoRender = false;
		$retorno = -1;
		if($this->params['form']) {
			$dados[$this->ClienteFuncionario->FuncionarioSetorCargo->name] = $this->params['form']['dados'];
			

			$retorno = ($this->ClienteFuncionario->FuncionarioSetorCargo->atualizar($dados));
			if(!$retorno) {
				$retorno['error'] = true;
				$retorno['message'] = $this->ClienteFuncionario->FuncionarioSetorCargo->validationErrors['data_fim'];
			} else {
				$retorno['error'] = false;
			}
		}
		echo json_encode($retorno);
	}

	public function sub_filtro_cliente_funcionario($codigo_grupo_economico, $codigo_unidade) {

		if(isset($codigo_grupo_economico) && $codigo_grupo_economico) {
			$lista_unidades = $this->GrupoEconomicoCliente->retorna_lista_de_unidades_de_um_grupo_economico($codigo_grupo_economico);
			$lista_cargos = $this->GrupoEconomicoCliente->listaCargos($codigo_grupo_economico);
			$lista_setores = $this->GrupoEconomicoCliente->listaSetores($codigo_grupo_economico);
			$lista_funcionarios = $this->GrupoEconomicoCliente->listaFuncionarios($codigo_grupo_economico);			
			$lista_status = array(
				'' => 'Todos',
				'1' => 'Ativos',
				'2' => 'Férias',
				'3' => 'Afastado',
				'0' => 'Inativos'
				);
		} else {
			$lista_unidades = array();
			$lista_cargos = array();
			$lista_setores = array();
			$lista_funcionarios = array();
			$lista_status = array();
		}

		$codigo_funcionario = isset($this->data['ClienteFuncionario']['codigo_funcionario']) ? $this->data['ClienteFuncionario']['codigo_funcionario'] : '';
		$codigo_setor = isset($this->data['ClienteFuncionario']['codigo_setor']) ? $this->data['ClienteFuncionario']['codigo_setor'] : '';
		$codigo_cargo = isset($this->data['ClienteFuncionario']['codigo_cargo']) ? $this->data['ClienteFuncionario']['codigo_cargo'] : '';
		$codigo_cliente = isset($this->data['ClienteFuncionario']['codigo_cliente']) ? $this->data['ClienteFuncionario']['codigo_cliente'] : '';

		$ativo = isset($this->data['ClienteFuncionario']['ativo']) ? $this->data['ClienteFuncionario']['ativo'] : '';
		$this->set(compact('lista_funcionarios', 'lista_setores', 'lista_cargos', 'lista_unidades', 'lista_status', 'codigo_unidade', 'codigo_cliente', 'codigo_cargo', 'codigo_setor', 'codigo_funcionario', 'codigo_grupo_economico', 'ativo'));
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
		
		$this->GrupoEconomicoCliente->virtualFields = false;
		$dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_unidade), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
		echo json_encode(array('codigo_grupo_economico' => $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico']));
		exit;
	}

	public function autocomplete_funcionario() {
		$this->loadModel('GrupoEconomico');
		$this->loadModel('GrupoEconomicoCliente');

		$codigo_cliente = $this->passedArgs['codigo'];
		
		// se vir com virgula pode ser uma chamada multicliente
		// pega o primeiro para buscar a matriz
		$pos = strpos($codigo_cliente, ',');
		if($pos > 0){
			// $codigo_cliente_exp = explode(',', $codigo_cliente);			
			// $codigo_cliente = $codigo_cliente_exp[0];
			
			$codigos_unidades = $this->normalizaCodigoCliente($codigo_cliente);
		}
		else {

			$codigo_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
			$codigos_unidades = $this->GrupoEconomicoCliente->lista($codigo_matriz);

			$codigos_unidades = array_keys($codigos_unidades);

		}
		
		$result = array();
		$nome_funcionario = trim($_GET['term']);
		
		if(empty($nome_funcionario)){
			echo json_encode($result);
			die();
		}

		// $this->log($codigo_cliente, 'debug');

		// $this->log("|||||||||||||||||||||||", 'debug');

		

		$conditions = array('ClienteFuncionario.codigo_cliente' => $codigos_unidades, 'Funcionario.nome LIKE' => '%'. $_GET['term'].'%');
		$fields = array('Funcionario.codigo', 'Funcionario.nome');
		$recursive = 1;
		$order = array('Funcionario.nome');

		// $this->log($this->ClienteFuncionario->find('sql', compact('conditions', 'fields', 'recursive', 'order')),'debug');

		$list = $this->ClienteFuncionario->find('list', compact('conditions', 'fields', 'recursive', 'order'));
		$result = array();
		foreach ($list as $key => $value) {
			$result[] = array('value' => $key, 'label' => $value);
		}
		echo json_encode($result);
		die();
	}

	public function carrega_combos_grupo_economico($model) {
		$this->loadModel('Cargo');
		$this->loadModel('Setor');
		$this->loadModel('GrupoEconomico');

		$codigo_cliente = $this->data[$model]['codigo_cliente'];

    	if(!empty($codigo_cliente)){
			$codigo_cliente = (is_array($codigo_cliente)) ? $codigo_cliente : $codigo_cliente;
			$codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
    	}

		$unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
		$setores = $this->Setor->lista($codigo_cliente);
		$cargos = $this->Cargo->lista($codigo_cliente);
		$this->set(compact('unidades', 'setores', 'cargos'));
	}

	public function excluir_matricula()
	{
		$this->autoRender = false;
		if(!empty($this->params['form']['codigo'])) {

			$this->loadModel('FuncionarioSetorCargo');
			$count = $this->FuncionarioSetorCargo->find('count', array('conditions' => array('codigo_cliente_funcionario' => $this->params['form']['codigo'])));
			if($count > 0) return json_encode(array('error' => true, 'message' => 'Existem Setores/cargos vinculados a esta matrícula, por isso não pôde ser removida.'));

			if($this->ClienteFuncionario->excluir($this->params['form']['codigo'])) {
				return json_encode(array('error' => false));
			}
		}
		return json_encode(array('error' => false));
	}


	public function consulta_vidas_filtros($thisDataClienteFuncionario = null)
	{
		// carrega dependencias		
        $this->loadModel('ClienteFuncionario');
        $this->loadModel('GrupoEconomicoCliente');
        $this->loadModel('Setor');
		$this->loadModel('Cargo');  
		
		// converte com $this->normalizaCodigoCliente pois codigo_cliente pode estar vindo do form como string ou da sessão como array
		$codigo_cliente = $this->normalizaCodigoCliente($thisDataClienteFuncionario['codigo_cliente']);

		$thisDataClienteFuncionario['codigo_cliente'] = $codigo_cliente;
		
		// $this->data['ClienteFuncionario'] = $this->Filtros->controla_sessao($this->data, $this->ClienteFuncionario->name);
		// if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
		// 	$this->data['ClienteFuncionario']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		// }
        // $usuario = $this->BAuth->user();                       
        // if(!empty($usuario['Usuario']['codigo_cliente'])) {
        //     $codigo_cliente = $usuario['Usuario']['codigo_cliente'];
        // } else {
        //     $codigo_cliente = null;
        // }
        // $this->data['ClienteFuncionario']['codigo_cliente'] = isset($this->data['ClienteFuncionario']['codigo_cliente']) ? $this->data['ClienteFuncionario']['codigo_cliente'] : null;        


        $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
        $setores = $this->Setor->lista($codigo_cliente);
        $cargos = $this->Cargo->lista($codigo_cliente);
			
        //Preenche a data do campo período da situação "exames a vencer"
		if(empty($thisDataClienteFuncionario['data_inicial']) || !isset($thisDataClienteFuncionario['data_inicial'])){
			$thisDataClienteFuncionario['data_inicial'] = date('d/m/Y');
		}
		if(empty($thisDataClienteFuncionario['data_final']) || !isset($thisDataClienteFuncionario['data_final'])){
			$thisDataClienteFuncionario['data_final'] = date('d/m/Y');
		}
		// configura no $this->data
		$this->data['ClienteFuncionario'] = $thisDataClienteFuncionario;
		// converte codigo
		$codigo_cliente = is_array($codigo_cliente) ? implode(',', $codigo_cliente) : $codigo_cliente;

		// CDCT-653
		$this->loadModel('Usuario');		
		$interno = $this->Usuario->verifica_usuario_interno($this->authUsuario['Usuario']['codigo']);

        $this->set(compact('codigo_cliente', 'unidades', 'setores', 'cargos','interno'));
	}


    function consulta_vidas($unidade=null, $ativo = null) {
		$this->pageTitle = "Consultar Vidas";
		
		// recupera filtros
		$filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteFuncionario->name);
		
		// se tem dados na sessao então preencha o codigo cliente e se tem codigo_cliente em $filtros usuario deve estar pesquisando
		if(!empty($this->authUsuario['Usuario']['codigo_cliente']) && empty($filtros['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}
		
		if(isset($unidade)){        		        
        	$filtros['codigo_cliente_alocacao'] = $unidade;		        	
        }
        if(isset($ativo) && $ativo != ""){
        	$filtros['ativo'] = $ativo;			
		}
		
		// atualiza $this->data
		$this->data[$this->ClienteFuncionario->name] = $filtros;

		// alimenta os formularios
		$this->consulta_vidas_filtros($this->data[$this->ClienteFuncionario->name]);

    }
    
    function consulta_vidas_listagem() {
        $this->layout = 'ajax';        
        $filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteFuncionario->name);
		$GrupoEconomicoCliente =& ClassRegistry::Init('GrupoEconomicoCliente');   
		
		// CDCT-653
		$this->loadModel('Usuario');		
		$interno = $this->Usuario->verifica_usuario_interno($this->authUsuario['Usuario']['codigo']);

		if(empty($interno)){
			$interno = $_SESSION["Auth"]['Usuario']['interno'];
		}

		// debug($_SESSION["Auth"]['Usuario']['interno']);
		// debug($interno);
		// debug($this->authUsuario['Usuario']['codigo']);
		// debug($_SESSION);
		if(isset($this->passedArgs[0]) && $this->passedArgs[0] =='export' && $interno ==1){			
			ini_set('memory_limit', '1G');			
			$dadosVidas = $this->ClienteFuncionario->exportar_clientesAtivosEQuantidadeVidas();
			exit;		
		}

		// $filtros = $this->Filtros->controla_sessao($this->data['ClienteFuncionarioAnalitico'], 'ClienteFuncionarioAnalitico');		
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		} 
        $conditions = $this->ClienteFuncionario->converteFiltroEmConditionConsultaVidas($filtros);
        $usuario = $this->BAuth->user();

        if(!empty($usuario['Usuario']['codigo_cliente'])) {
			if (isset($usuario['Usuario']['codigo_cliente']) && !empty($usuario['Usuario']['codigo_cliente']))
			{
				$dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $usuario['Usuario']['codigo_cliente']), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));		

				if(isset($dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'])) {
					$codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];
					$conditions['GrupoEconomicoCliente.codigo_grupo_economico'] = $codigo_grupo_economico;				

				}else{
					$conditions['GrupoEconomico.codigo_cliente'] = $data['codigo_cliente'];
				}		
			}
        }

		if(!empty($filtros['codigo_cliente'])){
			$filtros['codigo_cliente'] = $this->normalizaCodigoCliente($filtros['codigo_cliente']);
		}
		
		// if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
		// 	$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		// }        
		// if(isset($filtros['ativo'])): unset($filtros['ativo']); endif;
	
		// $conditions = $this->ClienteFuncionario->converteFiltroEmConditionConsultaVidas($filtros);		

        $dados = array();
        // $usuario = $this->BAuth->user();

        if(!empty($filtros['codigo_cliente'])) {        	
			// if (isset($usuario['Usuario']['codigo_cliente']) && !empty($usuario['Usuario']['codigo_cliente']))
			// {
				$dados_grupo_economico = $GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $filtros['codigo_cliente']), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));		
				
				if(isset($dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'])) {
					$codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];
					$conditions['GrupoEconomicoCliente.codigo_grupo_economico'] = $codigo_grupo_economico;				

				}else{
					$conditions['GrupoEconomico.codigo_cliente'] = $filtros['codigo_cliente'];
				}		
			// }            
        }
        //if (!empty($filtros['codigo_cliente'])) {
        	$dados = $this->ClienteFuncionario->consultaVidassintetico($conditions);
        //}	

		
        // $codigo_cliente = empty($filtros['codigo_cliente_alocacao']) ? $filtros['codigo_cliente'] : $filtros['codigo_cliente_alocacao'];	

        // if(isset($filtros['codigo_cliente_alocacao']) && !empty($filtros['codigo_cliente_alocacao']))
        // {
        // 	$codigo_alocacao_total = $filtros['codigo_cliente_alocacao'];
        // }else{
        $codigo_alocacao_total = null;	
		// }
		$codigo_total = null;
		// if(is_array($filtros['codigo_cliente']) && count($filtros['codigo_cliente']) == 1){
        // 	$codigo_total = $filtros['codigo_cliente'][0];
		// }
        $this->set(compact('dados', 'codigo_total','codigo_alocacao_total','interno'));
    }

    function consulta_vidas_analitico($unidade, $ativo = null, $codigo_cliente_alocacao=null) {
    	$this->layout = 'new_window';
    	$this->pageTitle = "Consultar Vidas Analitico";
        $this->loadModel('ClienteFuncionario');
        $this->loadModel('GrupoEconomicoCliente');
        $this->loadModel('Setor');
        $this->loadModel('Cargo');        
        $this->data['ClienteFuncionario'] = $this->Filtros->controla_sessao($this->data, $this->ClienteFuncionario->name);

		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$this->data['ClienteFuncionario']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}
        if(isset($unidade)){        		        
        	$this->data['ClienteFuncionario']['codigo_cliente'] = $unidade;		        	
        }
        if(isset($codigo_cliente_alocacao)){        		        
        	$this->data['ClienteFuncionario']['codigo_cliente_alocacao'] = $codigo_cliente_alocacao;		        	
        }else{
        	$this->data['ClienteFuncionario']['codigo_cliente_alocacao'] = null;
        }        

        if(isset($ativo) && $ativo != ""){
        	$this->data['ClienteFuncionario']['ativo'] = $ativo;
			$this->data['ClienteFuncionarioAnalitico']['ativo'] = $ativo;        	        	
        }else{
        	if(isset($this->data['ClienteFuncionario']['ativo']))
        	{
        		$this->data['ClienteFuncionario']['ativo'] = NULL;
        		$this->data['ClienteFuncionarioAnalitico']['ativo'] = NULL;
        	}
        }
		$this->data['ClienteFuncionario']['codigo_cliente'] = isset($this->data['ClienteFuncionario']['codigo_cliente']) ? $this->data['ClienteFuncionario']['codigo_cliente'] : null;                
        
        $this->data['ClienteFuncionarioAnalitico'] = $this->Filtros->controla_sessao($this->data, $this->ClienteFuncionario->name);
        $usuario = $this->BAuth->user();
        
        if(!empty($usuario['Usuario']['codigo_cliente'])) {
            $codigo_cliente = $usuario['Usuario']['codigo_cliente'];
        } else {
            $codigo_cliente = null;
        }

        $unidades = $this->GrupoEconomicoCliente->lista($this->data['ClienteFuncionario']['codigo_cliente']);
        $setores = $this->Setor->lista($this->data['ClienteFuncionario']['codigo_cliente']);
        $cargos = $this->Cargo->lista($this->data['ClienteFuncionario']['codigo_cliente']);
        
        //Preenche a data do campo período da situação "exames a vencer"
		if(empty($this->data['ClienteFuncionario']['data_inicial']) || !isset($this->data['ClienteFuncionario']['data_inicial'])){
			$this->data['ClienteFuncionario']['data_inicial'] = date('d/m/Y');
		}
		if(empty($this->data['ClienteFuncionario']['data_final']) || !isset($this->data['ClienteFuncionario']['data_final'])){
			$this->data['ClienteFuncionario']['data_final'] = date('d/m/Y');
		}

        $this->set(compact('codigo_cliente', 'unidades', 'setores', 'cargos'));
    }

	public function consulta_vidas_analitico_listagem($export = false) {
		$this->layout = 'ajax';
		$this->loadModel('GrupoEconomicoCliente');		        
        
		// $filtros = $this->Filtros->controla_sessao($this->data['ClienteFuncionarioAnalitico'], 'ClienteFuncionarioAnalitico');         	
		$filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteFuncionario->name);
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		} 
        $conditions = $this->ClienteFuncionario->converteFiltroEmConditionConsultaVidas($filtros);		        
        $usuario = $this->BAuth->user();

        if(!empty($usuario['Usuario']['codigo_cliente'])) {
			if (isset($usuario['Usuario']['codigo_cliente']) && !empty($usuario['Usuario']['codigo_cliente']))
			{
				$dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $usuario['Usuario']['codigo_cliente']), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));		

				if(isset($dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'])) {
					$codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];
					$conditions['GrupoEconomicoCliente.codigo_grupo_economico'] = $codigo_grupo_economico;				

				}else{
					$conditions['GrupoEconomico.codigo_cliente'] = $data['codigo_cliente'];
				}		
			}
        }

        //verifica se tem o ativo = 9 para não filtrar por este codigo pois nao existe
        if($conditions['ClienteFuncionario.ativo'] == '9') {
        	unset($conditions['ClienteFuncionario.ativo']);
        }//fim if ativo=9

        $listagem = array();
		$codigo_cliente = null;
        if (!empty($filtros['codigo_cliente'])) {			

			$joins = $this->ClienteFuncionario->bindModelConsultaAnalitico();
			$fields = array(
				"Funcionario.nome AS nome",
				"Funcionario.cpf AS cpf",
				"Cliente.nome_fantasia AS nome_fantasia",
				"Setor.descricao as descricao",
				"Cargo.descricao as cargo",
				"GrupoEconomico.codigo_cliente as codigo_cliente",
				"CASE WHEN ClienteFuncionario.ativo = 1 THEN 1 ELSE 0 END as ativo",
				"CASE WHEN ClienteFuncionario.ativo = 0 THEN 1 ELSE 0 END as inativo",
				"CASE WHEN ClienteFuncionario.ativo = 2 THEN 1 ELSE 0 END as ferias",
				"CASE WHEN ClienteFuncionario.ativo = 3 THEN 1 ELSE 0 END as afastado",
			);		
			$order = array('nome ASC');
			$limit = 50;
			$recursive = -1;
	        if($export){
	            $query = $this->GrupoEconomicoCliente->find('sql', compact('fields','conditions','joins','order', 'recursive'));
	            $this->exportConsultaVidas($query);
	        }			
			$this->paginate['GrupoEconomicoCliente'] = array(				
				'fields' => $fields,				
				'conditions' => $conditions,
				'joins' => $joins,
				'limit' => 50,
				'order' => $order,
				'recursive' => $recursive
				);
			$listagem = $this->paginate('GrupoEconomicoCliente');											
        }
        $this->set(compact('listagem'));
	}   

    public function exportConsultaVidas($query) {
      $this->loadModel('GrupoEconomicoCliente');	
      $dbo = $this->GrupoEconomicoCliente->getDataSource();
      $dbo->results   = $dbo->rawQuery($query);
      ob_clean();
      header('Content-Encoding: UTF-8');
      header("Content-Type: application/force-download;charset=utf-8");
      header('Content-Disposition: attachment; filename="vidas'.date('YmdHis').'.csv"');
      echo utf8_decode('"Nome";"CPF";"Unidade";"Setor";"Cargo";"Status"')."\n";

      while ($value = $dbo->fetchRow()) {
        if($value[0]['ativo'] > 0){ $status = "ATIVO";}
        if($value[0]['inativo'] > 0){ $status = "INATIVO";}
        if($value[0]['ferias'] > 0){ $status = "FERIAS";}            
        if($value[0]['afastado'] > 0){ $status = "AFASTADO";}      	
        $linha = $value[0]['nome'].';';
        $linha .= $value[0]['cpf'].';';
        $linha .= $value[0]['nome_fantasia'].';';
        $linha .= $value[0]['descricao'].';';
        $linha .= $value[0]['cargo'].';';
        $linha .= $status.';';
        echo utf8_decode($linha)."\n";
      }
      die();
    }

    public function vidas()
    {
    	$this->pageTitle = "Consulta Vidas";
        $this->loadModel('ClienteFuncionario');

        $this->data['ClienteFuncionarioVidas'] = $this->Filtros->controla_sessao($this->data['ClienteFuncionarioVidas'], 'ClienteFuncionarioVidas');

		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$this->data['ClienteFuncionario']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

        $usuario = $this->BAuth->user();                                       
    }

    public function vidas_listagem()
    {
    	$this->layout = 'ajax';                

        $filtros = $this->Filtros->controla_sessao($this->data['ClienteFuncionarioVidas'], 'ClienteFuncionarioVidas');        	

		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

        $conditions = $this->ClienteFuncionario->converteFiltroEmConditionConsultaVidas($filtros);		        

        $dados = array();

        $usuario = $this->BAuth->user();

       	$dados = $this->ClienteFuncionario->Vidas($conditions);       	
        $this->set(compact('dados'));
    }

    /*
      Verifica se existe alerta de hierarquia pendente com origem 'nova_hierarquia' para este usuário de cliente
      Durante inclusão ou alteração de funcionario_setor_cargo, se ele criou esta hierarquia e se existir alerta pendente
      para este usuário, o mesmo visualizará alerta
    */
	public function existe_alerta_hierarquia_pendente(){
		$this->autoRender = false;
		$retorno = 0;

		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {

			if($this->params['form']) {
				$parametros = $this->params['form']['dados'];
				$dados = Set::extract('/FuncionarioSetorCargo', $parametros);


				$this->loadModel('AlertaHierarquiaPendente');
				$cod_cliente_alocacao = $dados[0]['FuncionarioSetorCargo']['codigo_cliente_alocacao'];
				$cod_setor = $dados[0]['FuncionarioSetorCargo']['codigo_setor'];
				$cod_cargo = $dados[0]['FuncionarioSetorCargo']['codigo_cargo'];
				$cod_usuario = $this->authUsuario['Usuario']['codigo'];
				//Somente se ele gerou esta hierarquia
				$origem = 'NOVA_HIERARQUIA';
				
				//Verifica se existe alerta pendente desta hierarquia para o usuário
				if($this->AlertaHierarquiaPendente->existe_alerta($cod_cliente_alocacao,$cod_setor, $cod_cargo,$cod_usuario,$origem)){
					$retorno = 1;
				}
			}
		}

		echo json_encode($retorno);	
	}

	/**
	 * [get_tomador description]
	 * 
	 * metodo ajax para validação se é um tomador ou nao
	 * 
	 * @return [type] [description]
	 */
	public function get_tomador()
    {

    	$this->autoRender = false;
    	$this->layout = 'ajax';

    	$dados = array('retorno' => false);

    	//verifica se veio o codigo do cliente selecionado
    	if($this->params['form']['codigo_cliente']) {

    		//monta as conditions
    		$conditions = array(
    			'Cliente.codigo' => $this->params['form']['codigo_cliente'],
    			'Cliente.e_tomador' => 1
    		);

    		//pega os dados do cliente
    		$cliente = $this->Cliente->find('first',array('conditions' => $conditions));

    		//verifica se tem dados, caso tenho é tomador
    		if(!empty($cliente)) {

    			//para retornar que é um tomador
    			$dados = array('retorno' => true);

    			$codigo_cliente_referencia = '';

    			//verifica se tem o codigo_fsc para alteracao
    			if(!empty($this->params['form']['codigo_fsc'])) {

    				$codigo_fsc = $this->params['form']['codigo_fsc'];

    				//pega os dados do ultima alocacao para trazer selecionada
	    			$fsc = $this->FuncionarioSetorCargo->find('first', array('conditions' => array('FuncionarioSetorCargo.codigo' => $codigo_fsc)));

    				//verifica se existe um codigo referencia pois é quando ocorre a edição do cargo
    				if(!empty($fsc['FuncionarioSetorCargo']['codigo_cliente_referencia'])) {
    					$codigo_cliente_referencia = $fsc['FuncionarioSetorCargo']['codigo_cliente_referencia'];
    					//pega os dados para retornar
	    				$dados = array('retorno' => true, 'codigo_cliente_referencia' => $codigo_cliente_referencia, 'codigo_setor' => $fsc['FuncionarioSetorCargo']['codigo_setor'], 'codigo_cargo' => $fsc['FuncionarioSetorCargo']['codigo_cargo']);
    				}

    			}
    			
    			//verifica se tem o codigo_cliente_funcionario	para inclusao
    			if(isset($this->params['form']['codigo_cliente_funcionario']) && $codigo_cliente_referencia == '') {

    				//pega o codigo_cliente_funcionario
	    			$codigo_cliente_funcionario = $this->params['form']['codigo_cliente_funcionario'];
	    			//relacionamento
	    			$join = array(
	    				array(
							'table' => 'RHHealth.dbo.cliente',
							'alias' => 'Cliente',
							'type' => 'INNER',
							'conditions' => 'FuncionarioSetorCargo.codigo_cliente_alocacao = Cliente.codigo AND Cliente.e_tomador <> 1',
						),
	    			);

	    			//pega os dados do ultima alocacao para trazer selecionada
	    			$fsc = $this->FuncionarioSetorCargo->find('first', array('joins' => $join, 'conditions' => array('FuncionarioSetorCargo.codigo_cliente_funcionario' => $codigo_cliente_funcionario), 'order' => array('FuncionarioSetorCargo.codigo DESC'), 'recursive' => -1));
	    			
	    			//verifica se existe valor.
	    			if(!empty($fsc)) {
	    				//pega os dados para retornar
	    				$dados = array('retorno' => true, 'codigo_cliente_referencia' => $fsc['FuncionarioSetorCargo']['codigo_cliente_alocacao'], 'codigo_setor' => $fsc['FuncionarioSetorCargo']['codigo_setor'], 'codigo_cargo' => $fsc['FuncionarioSetorCargo']['codigo_cargo']);
	    			}
    			}//fim if codigo_cliente_funcionario


    		}//fim cliente

    	}//fim verificacao

    	//retorna os dados para consulta
    	return json_encode($dados);

    }//fim get_tomador

	

}//FINAL CLASS ClientesFuncionariosController