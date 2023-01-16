<?php
class AplicacaoExamesController extends AppController {
	public $name = 'AplicacaoExames';
	public $helpers = array('BForm', 'Html', 'Ajax');
	var $uses = array('AplicacaoExame', 
		'Exame',
		'Setor',
		'Cargo',
		'Cliente',
		'Funcionario',
		'GrupoEconomico',
		'GrupoEconomicoCliente',
		'OrdemServico',
		'OrdemServicoItem',
		'GrupoExposicao',
		'GrupoExposicaoRisco',
		'Risco',
		'ClienteSetor',
		'GrupoHomogeneo',
		'GrupoHomDetalhe',
		'GrupoHomogeneoExame',
		'TipoExame',
		'ValidacaoPpra',
		'Configuracao'		);

	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow('excluir_exame_por_ajax', 'preenche_com_exame_clinico', 'concluir','modal_ppra_pendente');
	}
	
	function index($codigo_unidade, $codigo_cliente = NULL) {

		if( !$codigo_cliente ) $codigo_cliente = $codigo_unidade;

		//correção no redirect para o metodo gerenciar_pcmso, mandando codigo_cliente quando deveria estar mandadndo codigo da matriz
		$codigo_grupo_economico = $this->GrupoEconomicoCliente->find('first',array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));

		$codigo_matriz = $codigo_grupo_economico['GrupoEconomico']['codigo_cliente'];

		$fields = array('OrdemServico.status_ordem_servico');

		$joins = array(
			array(
				'table' => 'ordem_servico_item',
				'alias' => 'OrdemServicoItem',
				'type' => 'INNER',
				'conditions' => array('OrdemServicoItem.codigo_ordem_servico = OrdemServico.codigo ')
			)
		);

		$codigo_servico_pcmso = $this->OrdemServico->getPCMSOByCodigoCliente($codigo_unidade);

		$conditions = array(
			'OrdemServico.codigo_cliente' => $codigo_unidade, 
			'OrdemServicoItem.codigo_servico' => $codigo_servico_pcmso
		);		

		$status = $this->OrdemServico->find('first', array('fields'=>$fields, 'conditions' => $conditions, 'joins' => $joins));
		
		if($status['OrdemServico']['status_ordem_servico'] != 3){

			$this->pageTitle = 'Aplicação de Exames';
			
			if(!$codigo_unidade) {
				$this->redirect(array('controller' => 'clientes_implantacao'));
			}
			
			$cargos = $this->Cargo->lista_por_cliente($codigo_unidade);
			$setores = $this->Setor->lista_por_cliente($codigo_unidade);
			$dados_cliente = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_unidade);
			$this->data['AplicacaoExame'] = $this->Filtros->controla_sessao($this->data, $this->AplicacaoExame->name);
			
			$this->data['Matriz'] = $dados_cliente['Matriz'];
			$this->data['Unidade'] = $dados_cliente['Unidade'];

			$this->set(compact('cargos', 'setores', 'codigo_unidade', 'dados_cliente'));
		}else{
			$this->BSession->setFlash(array('alert alert-success', 'Ordem de serviço finalizado.'));
			$this->redirect(array('controller' => 'clientes_implantacao', 'action' => 'gerenciar_pcmso', $codigo_matriz));
		}//FINAL IF STATUS DIFERENTE DE 3
	}//FINAL FUNCTION index
	
	public function visualizar_gae($codigo_unidade){
		$this->pageTitle = 'Aplicação de Exames - Visualização';
		$visualizar_gae = true;

		$cargos = $this->Cargo->lista_por_cliente($codigo_unidade);
		$setores = $this->Setor->lista_por_cliente($codigo_unidade);
		$dados_cliente = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_unidade);
		$this->data['AplicacaoExame'] = $this->Filtros->controla_sessao($this->data, $this->AplicacaoExame->name);
		
		$this->data['Matriz'] = $dados_cliente['Matriz'];
		$this->data['Unidade'] = $dados_cliente['Unidade'];

		$this->set(compact('cargos', 'setores', 'codigo_unidade', 'dados_cliente', 'visualizar_gae'));
	}

	function listagem($codigo_cliente_alocacao) {

		$dados = $this->OrdemServico->busca_status($codigo_cliente_alocacao, 'PCMSO');
		$visualizar_gae = ($dados[0]['OrdemServico_status'] == 3 ? true : false);

		$this->layout = 'ajax';
		$dados_cliente = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente_alocacao);
		
		$filtros = $this->Filtros->controla_sessao($this->data, $this->AplicacaoExame->name);
		
		$conditions = $this->AplicacaoExame->converteFiltroEmCondition($filtros);
		$conditions = array_merge($conditions, array('AplicacaoExame.codigo_cliente_alocacao' => $codigo_cliente_alocacao));
		
		$fields = array(
			'AplicacaoExame.codigo_cliente_alocacao',
			'AplicacaoExame.codigo_setor',
			'AplicacaoExame.codigo_cargo',
			'Setor.codigo', 
			'Setor.descricao', 
			'Cargo.codigo',
			'Cargo.descricao',
			'GrupoHomogeneo.codigo', 
			'GrupoHomogeneo.descricao',
			'Funcionario.nome',
			'AplicacaoExame.codigo_funcionario',
			'AplicacaoExame.codigo_grupo_homogeneo_exame'
			);

		$order = 'Setor.descricao ASC, Cargo.descricao ASC';
		
		$joins  = array(  
			array(
				'table' => $this->Setor->databaseTable.'.'.$this->Setor->tableSchema.'.'.$this->Setor->useTable,
				'alias' => 'Setor',
				'type' => 'LEFT',
				'conditions' => 'Setor.codigo = AplicacaoExame.codigo_setor',
				),
			array(
				'table' => $this->Cargo->databaseTable.'.'.$this->Cargo->tableSchema.'.'.$this->Cargo->useTable,
				'alias' => 'Cargo',
				'type' => 'LEFT',
				'conditions' => 'Cargo.codigo = AplicacaoExame.codigo_cargo'
				),
			array(
				'table' => $this->GrupoHomDetalhe->databaseTable.'.'.$this->GrupoHomDetalhe->tableSchema.'.'.$this->GrupoHomDetalhe->useTable,
				'alias' => 'GrupoHomDetalhe',
				'type' => 'LEFT OUTER',
				'conditions' => 'GrupoHomDetalhe.codigo_setor = Setor.codigo AND GrupoHomDetalhe.codigo_cargo = Cargo.codigo',
				),
			array(
				'table' => $this->GrupoHomogeneo->databaseTable.'.'.$this->GrupoHomogeneo->tableSchema.'.'.$this->GrupoHomogeneo->useTable,
				'alias' => 'GrupoHomogeneo',
				'type' => 'LEFT OUTER',
				'conditions' => 'GrupoHomogeneo.codigo_cliente = AplicacaoExame.codigo_cliente_alocacao AND GrupoHomDetalhe.codigo_grupo_homogeneo = GrupoHomogeneo.codigo',
				),
			array(
				'table' => $this->Funcionario->databaseTable.'.'.$this->Funcionario->tableSchema.'.'.$this->Funcionario->useTable,
				'alias' => 'Funcionario',
				'type' => 'LEFT',
				'conditions' => 'Funcionario.codigo = AplicacaoExame.codigo_funcionario',
				),
			array(
	                'table' => 'RHHealth.dbo.clientes_setores_cargos',
	                'alias' => 'ClienteSetorCargo',
	                'type' => 'INNER',
	                'conditions' => array('ClienteSetorCargo.codigo_setor = Setor.codigo AND ClienteSetorCargo.codigo_cargo = Cargo.codigo AND AplicacaoExame.codigo_cliente_alocacao = ClienteSetorCargo.codigo_cliente_alocacao AND (ClienteSetorCargo.ativo = 1 OR ClienteSetorCargo.ativo IS NULL)')//ajuste para o chamado CDCT-428, trazer somente hierarquias ativas
            	),
			);

		$group = array(
			'AplicacaoExame.codigo_cliente_alocacao',
			'AplicacaoExame.codigo_setor',
			'AplicacaoExame.codigo_cargo',
			'Setor.codigo', 
			'Setor.descricao', 
			'Cargo.codigo',
			'Cargo.descricao',
			'GrupoHomogeneo.codigo', 
			'GrupoHomogeneo.descricao',
			'Funcionario.nome',
			'AplicacaoExame.codigo_funcionario',
			'AplicacaoExame.codigo_grupo_homogeneo_exame'
			);

		$this->paginate['AplicacaoExame'] = array(
			'fields' => $fields,
			'conditions' => $conditions,
			'limit' => 50,
			'joins' => $joins,
			'group' => $group,
			'order' => $order
			);
		
		$aplicacao_exame = $this->paginate('AplicacaoExame');

		$this->set(compact('aplicacao_exame', 'codigo_cliente_alocacao', 'dados_cliente'));

		$this->loadModel('OrdemServico');

		$codigo_servico_pcmso = $this->OrdemServico->getPCMSOByCodigoCliente($codigo_cliente_alocacao);

		$ordemServico = $this->OrdemServico->find('first', array(
			'conditions' => array(
				'OrdemServico.codigo_cliente' => $codigo_cliente_alocacao,
				'OrdemServicoItem.codigo_servico' => $codigo_servico_pcmso
				),
			'joins' => array(
				array(
					'table' => 'ordem_servico_item',
					'alias' => 'OrdemServicoItem',
					'type' => 'INNER',
					'conditions' => array('OrdemServico.codigo = OrdemServicoItem.codigo_ordem_servico')
					)
				)
			)
		);
		$this->set(compact('ordemServico', 'visualizar_gae'));
	}//FINAL FUNCTION listagem
	
	function carrega_combo(){
		$conditions = array('ativo'=> 1);
		$fields = array('codigo', 'descricao');
		$order = 'descricao'; 

		$exames = $this->Exame->find('list', array('conditions' => $conditions, 'order' => $order, 'fields' => $fields));
		$tipos_exames = $this->TipoExame->find('list', array('order' => $order, 'fields' => $fields));

		$this->set(compact('exames', 'tipos_exames'));
	}//FINAL FUNCTION carrega_combo

	function editar($codigo_cliente_alocacao, $codigo_setor, $codigo_cargo, $codigo_funcionario=null, $referencia = null, $codigo_ghe = null) {
		
		$this->loadModel('ValidacaoPpra');
		
		$cargo_selected = $codigo_cargo;
		$setor_selected = $codigo_setor;

		//tratamento do parametro para nao dar erro de parser alfa para int
		if($codigo_funcionario == 'null') {
			$codigo_funcionario = null;
		}
		
			$codigo_ghe = null;

		$dados = $this->OrdemServico->busca_status($codigo_cliente_alocacao, 'PCMSO');

		$visualizar_gae = false;

		if(!empty($dados)) {
			$visualizar_gae = ($dados[0]['OrdemServico_status'] == 3 ? true : false);
		}

		$this->pageTitle = 'Vincular Aplicação de Exames' . ($visualizar_gae ? ' - Vizualização' : '');
		$erro_save = array();
		
		if($this->RequestHandler->isPost()) {			
			
			unset($this->data['AplicacaoExame']['X']);

			if ($this->AplicacaoExame->editar($this->data)) {
				//troca o status da validacao do pcmso
				if(!$this->ValidacaoPpra->valida_pcmso($this->data['AplicacaoExame']['codigo_cliente_alocacao'], $this->data['AplicacaoExame']['codigo_setor'], $this->data['AplicacaoExame']['codigo_cargo'], $this->data['AplicacaoExame']['codigo_funcionario'])) {
					$this->BSession->setFlash('save_error');
				}

				$this->BSession->setFlash('save_success');
				
				if($referencia == 'validar_pcmso'){					
					$this->redirect(array('controller' => 'consultas', 'action' => 'ppra_pcmso_pendente_sc_terceiros', $codigo_cliente_alocacao, 'pcmso'));	
				} else {
					$this->redirect(array( 'controller' => 'aplicacao_exames', 'action' => 'index', $codigo_cliente_alocacao));
				}
			} 
			else{

				// debug($this->AplicacaoExame->validationErrors);exit;
				foreach ($this->AplicacaoExame->validationErrors as $linha => $erros) {

					if(is_numeric($linha)){
						
						foreach ($erros as $campo => $erro) {
							$this->AplicacaoExame->invalidate($campo, $erro);
						}
					}
					else{
						if($linha == "ordem_servico"){
							$this->AplicacaoExame->invalidate('codigo_setor', $this->AplicacaoExame->validationErrors['ordem_servico']);       
							$this->AplicacaoExame->invalidate('codigo_cargo', $this->AplicacaoExame->validationErrors['ordem_servico']);       
						}
						else{
							$this->AplicacaoExame->invalidate($linha, $erros);       
							$this->BSession->setFlash('save_error');
						}
					}
					$erro_save[$linha] = true;
				}

				//verifica se o codigo funcionario existe
				if(empty($codigo_funcionario) && !empty($this->data['AplicacaoExame']['codigo_funcionario'])) {
					$codigo_funcionario = $this->data['AplicacaoExame']['codigo_funcionario'];
				}

				$this->BSession->setFlash(array(MSGT_ERROR,'Verificar os dados de configuração da Grade de Exames.'));
			}        		
		} 
		
		$conditions = array('ativo'=> 1);
		$fields = array('codigo', 'descricao');
		$order = 'descricao'; 

		$exames = $this->Exame->find('list', array('conditions' => $conditions, 'order' => $order, 'fields' => $fields));
		$tipos_exames = $this->TipoExame->find('list', array('order' => $order, 'fields' => $fields));

		$aplicacao_exame = $this->AplicacaoExame->find('all', 
			array(
				'conditions' => array(
					'codigo_cliente_alocacao' => $codigo_cliente_alocacao,
					'codigo_setor' => $codigo_setor,
					'codigo_cargo' => $codigo_cargo,
					'codigo_funcionario' => $codigo_funcionario
				)
			)
		);

		foreach ($aplicacao_exame as $key => $dados) {
			//tratamento para os exames inativos
			$dados['AplicacaoExame']['exame_ativo'] = 1;
			//verifica se o exame existe na variavel
			if(!array_key_exists($dados['AplicacaoExame']['codigo_exame'], $exames)) {
				//seta a variavel como inativo
				$dados['AplicacaoExame']['exame_ativo'] = 0;
				//busca o exame inativo para listar na tela 
				$exameAdd = $this->Exame->find('first', array('conditions' => array('codigo' => $dados['AplicacaoExame']['codigo_exame']), 'fields' => $fields));

				$exames[$exameAdd['Exame']['codigo']] = $exameAdd['Exame']['descricao'];
			}
			$this->data['AplicacaoExame'][$key] = $dados['AplicacaoExame'];
		}
		$this->data['AplicacaoExame']['codigo_cliente_alocacao'] 	= $this->data['AplicacaoExame'][$key]['codigo_cliente_alocacao'];
		$this->data['AplicacaoExame']['codigo_setor'] 				= $this->data['AplicacaoExame'][$key]['codigo_setor'];
		$this->data['AplicacaoExame']['codigo_cargo'] 				= $this->data['AplicacaoExame'][$key]['codigo_cargo'];

		$dados_cliente = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente_alocacao);
		$this->data['Matriz'] = $dados_cliente['Matriz'];
		$this->data['Unidade'] = $dados_cliente['Unidade'];
		
		$funcionario_selected = "";
		$funcionarios = "";
		if(!empty($codigo_funcionario)){
			$funcionario_selected = $codigo_funcionario;
			$cond_funcionario['Funcionario.codigo'] = $codigo_funcionario;
			$funcionarios = $this->Funcionario->find('list', array('conditions' => $cond_funcionario, 'fields' => array('codigo', 'nome'), 'order' => array('nome')));
		} 

		$ghe_selected = '';
		if(!is_null($codigo_ghe)){
			$ghe_selected = $codigo_ghe;
		}

		$conditions_setor = array(
			'codigo_cliente' => $dados_cliente['Matriz']['codigo'], 
			'ativo'=> 1, 
			'codigo' => $codigo_setor
		);

		$conditions_cargo = array(
			'codigo_cliente' => $dados_cliente['Matriz']['codigo'], 
			'ativo'=> 1, 
			'codigo' => $codigo_cargo
		);

		$setores = $this->Setor->find('list', array('conditions' => $conditions_setor, 'fields' => array('codigo', 'descricao'), 'order' => array('descricao')));
		$cargos = $this->Cargo->find('list', array('conditions' => $conditions_cargo, 'fields' => array('codigo', 'descricao'), 'order' => array('descricao')));
		
		$ghes = array();

		// debug($this->data);

		$this->set(compact('setores','cargos','dados_cliente', 'consulta_aplicacao','codigo_cliente_alocacao', 'codigo_setor', 'codigo_cargo','setor_selected', 'cargo_selected','funcionarios','funcionario_selected', 'visualizar_gae', 'ghes', 'ghe_selected','exames', 'tipos_exames','referencia','erro_save'));	
	}//FINAL FUNCTION editar
	
	function atualiza_status($codigo, $status) {
		$this->layout = 'ajax';

		$this->data['AplicacaoExame']['codigo'] = $codigo;
		$this->data['AplicacaoExame']['ativo'] = ($status == 0) ? 1 : 0;

		if ($this->AplicacaoExame->atualizar($this->data, false)) {
			print 1;
		} else {
			print 0;
		}
		$this->render(false, false);
		// 0 -> ERRO | 1 -> SUCESSO
	}//FINAL FUNCTION atualiza_status

	public function preenche_com_exame_clinico($codigo_cliente, $back_auto = false, $hierarquia = false)
	{
		$usuario = $this->BAuth->user();
		if($this->AplicacaoExame->preenche_com_exame_clinico($codigo_cliente, $usuario, $hierarquia)) {
			$this->BSession->setFlash('save_success');
		} else {
			$this->BSession->setFlash('save_error');
		}
		
		if( $back_auto ){
			$this->redirect( Comum::UrlOrigem()->data );	
		} else {
			return $this->redirect(array('action' => 'index', $codigo_cliente));
		}

	}//FINAL FUNCTION preenche_com_exame_clinico
	
	public function excluir_exame_por_ajax()
	{
		$this->autoRender = false;
		$return = -1;
		if(!empty($this->params['form'])) {
			if($this->params['form']['codigo_funcionario'] == 0){
				$this->params['form']['codigo_funcionario'] = null;
			} 

			$return = $this->AplicacaoExame->deleteAll(array(
				'AplicacaoExame.codigo_cliente_alocacao' 	=>  $this->params['form']['codigo_cliente_alocacao'],
				'AplicacaoExame.codigo_setor' 				=>  $this->params['form']['codigo_setor'],
				'AplicacaoExame.codigo_cargo' 				=>  $this->params['form']['codigo_cargo'],
				'AplicacaoExame.codigo_funcionario' 		=>  $this->params['form']['codigo_funcionario'],
				), false
			);
		}
		return $return;
	}//FINAL FUNCTION excluir_exame_por_ajax
	
	public function remove_exame() {
		$codigo = $this->params['form']['codigo_aplicacao_exame'];
		$return = $this->AplicacaoExame->excluir($codigo);
		exit($return);
	}//FINAL FUNCTION remove_exame

	function incluir($codigo_cliente_alocacao, $setor = null, $cargo = null, $funcionario = null) {

		//tratamento do parametro para nao dar erro de parser alfa para int
		if($funcionario == 'null') {
			$funcionario = null;
		}

		$this->pageTitle = 'Incluir Aplicação de Exames';
		if($this->RequestHandler->isPost()) {

			unset($this->data['AplicacaoExame']['X']);
			
			if ($this->AplicacaoExame->incluir($this->data)) {

				//troca o status da validacao do pcmso
				if(!$this->ValidacaoPpra->valida_pcmso($this->data['AplicacaoExame']['codigo_cliente_alocacao'], $this->data['AplicacaoExame']['codigo_setor'], $this->data['AplicacaoExame']['codigo_cargo'], $this->data['AplicacaoExame']['codigo_funcionario'])) {
					$this->BSession->setFlash('save_error');
				}

				$this->BSession->setFlash('save_success');

				$uo = Comum::UrlOrigem();
				if( $uo ){
					$this->redirect( Comum::UrlOrigem()->data );						
				} else {
					$this->redirect(array('controller' => 'aplicacao_exames', 'action' => 'index', $codigo_cliente_alocacao));
				}
			}
			else {
				foreach ($this->AplicacaoExame->validationErrors as $linha => $erros) {

					if(is_numeric($linha)){
						foreach ($erros as $campo => $erro) {
							$this->AplicacaoExame->invalidate($campo, $erro);       
						}
					}
					else{
						if($linha == "ordem_servico"){
							$this->AplicacaoExame->invalidate('codigo_setor', $this->AplicacaoExame->validationErrors['ordem_servico']);       
							$this->AplicacaoExame->invalidate('codigo_cargo', $this->AplicacaoExame->validationErrors['ordem_servico']);       
						}
						else{
							$this->AplicacaoExame->invalidate($linha, $erros);       
							$this->BSession->setFlash('save_error');
						}
					}
				}
			}
		}

		$this->carrega_combo();        
		$dados_cliente = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente_alocacao);
		$conditions = array(
			'codigo_cliente' => $dados_cliente['Matriz']['codigo'],
			'ativo'=> 1
		);
		$conditions_ghe = array(
			'codigo_cliente' => $dados_cliente['Matriz']['codigo'],
			'ativo'=> 1,
		);

		$cond_setores = $cond_cargos = $conditions;

		$setor_selected = $cargo_selected = $ghe_selected = '';
		if( $setor ){
			$setor_selected = $setor;
			$cond_setores['Setor.codigo'] = $setor;	
		} 
		if( $cargo ){
			$cargo_selected = $cargo;
			$cond_cargos['Cargo.codigo'] = $cargo;	
		}

		$funcionario_selected = "";
		$funcionarios = "";
		if($funcionario){
			$funcionario_selected = $funcionario;
			$cond_funcionario['Funcionario.codigo'] = $funcionario;
			$funcionarios = $this->Funcionario->find('list', array('conditions' => $cond_funcionario, 'fields' => array('codigo', 'nome'), 'order' => array('nome')));
		} 

		//tratamento para os exames inativos
		$this->data['AplicacaoExame'][0]['codigo_exame'] = $this->Configuracao->getChave('INSERE_EXAME_CLINICO');
		$this->data['AplicacaoExame'][0]['exame_ativo'] = 1;

		$this->data['Matriz'] = $dados_cliente['Matriz'];
		$this->data['Unidade'] = $dados_cliente['Unidade'];

		//pega os dados do exame que já vem pre selecionado
		$exame = $this->Exame->find('first',array('conditions' => array('codigo' => $this->data['AplicacaoExame'][0]['codigo_exame'])));
		//seta as variaveis para aparecer em tela
		//aplicavel em
		$this->data['AplicacaoExame'][0]['periodo_meses'] 				= $exame['Exame']['periodo_meses'];
		$this->data['AplicacaoExame'][0]['periodo_apos_demissao'] 		= $exame['Exame']['periodo_apos_demissao'];
		$this->data['AplicacaoExame'][0]['exame_admissional'] 			= $exame['Exame']['exame_admissional'];
		$this->data['AplicacaoExame'][0]['exame_periodico'] 			= $exame['Exame']['exame_periodico'];
		$this->data['AplicacaoExame'][0]['exame_demissional'] 			= $exame['Exame']['exame_demissional'];
		$this->data['AplicacaoExame'][0]['exame_retorno'] 				= $exame['Exame']['exame_retorno'];
		$this->data['AplicacaoExame'][0]['exame_mudanca'] 				= $exame['Exame']['exame_mudanca'];
		$this->data['AplicacaoExame'][0]['exame_monitoracao'] 			= $exame['Exame']['exame_monitoracao'];
		//a partir de qual idade
		$this->data['AplicacaoExame'][0]['periodo_idade'] 				= $exame['Exame']['periodo_idade'];
		$this->data['AplicacaoExame'][0]['qtd_periodo_idade'] 			= $exame['Exame']['qtd_periodo_idade'];
		$this->data['AplicacaoExame'][0]['periodo_idade_2'] 			= $exame['Exame']['periodo_idade_2'];
		$this->data['AplicacaoExame'][0]['qtd_periodo_idade_2'] 		= $exame['Exame']['qtd_periodo_idade_2'];
		$this->data['AplicacaoExame'][0]['periodo_idade_3'] 			= $exame['Exame']['periodo_idade_3'];
		$this->data['AplicacaoExame'][0]['qtd_periodo_idade_3'] 		= $exame['Exame']['qtd_periodo_idade_3'];
		$this->data['AplicacaoExame'][0]['periodo_idade_4'] 			= $exame['Exame']['periodo_idade_4'];
		$this->data['AplicacaoExame'][0]['qtd_periodo_idade_4'] 		= $exame['Exame']['qtd_periodo_idade_4'];
		//tipos de exames
		$this->data['AplicacaoExame'][0]['exame_excluido_convocacao']	= $exame['Exame']['exame_excluido_convocacao'];
		$this->data['AplicacaoExame'][0]['exame_excluido_ppp'] 			= $exame['Exame']['exame_excluido_ppp'];
		$this->data['AplicacaoExame'][0]['exame_excluido_aso'] 			= $exame['Exame']['exame_excluido_aso'];
		$this->data['AplicacaoExame'][0]['exame_excluido_pcmso'] 		= $exame['Exame']['exame_excluido_pcmso'];
		$this->data['AplicacaoExame'][0]['exame_excluido_anual'] 		= $exame['Exame']['exame_excluido_anual'];
		// pr($this->data);exit;

		$setores = $this->Setor->find('list', array('conditions' => $cond_setores, 'fields' => array('codigo', 'descricao'), 'order' => array('descricao')));
		$cargos = $this->Cargo->find('list', array('conditions' => $cond_cargos, 'fields' => array('codigo', 'descricao'), 'order' => array('descricao')));
		$ghes = $this->GrupoHomogeneoExame->find('list', array('conditions' => $conditions_ghe, 'fields' => array('codigo', 'descricao'), 'order' => array('descricao')));

		$this->set(compact(	'setores',
							'cargos',
							'codigo_cliente_alocacao',
							'dados_cliente',
							'setor_selected',
							'cargo_selected',
							'funcionario_selected',
							'funcionarios',
							'ghes',
							'ghe_selected'
						));
	}//FINAL FUNCTION incluir

	public function concluir($codigo){
		// Método passado para a model //
		$this->autoRender = false;	

		$retorno = $this->AplicacaoExame->concluir($codigo);

		if ($retorno){
			$json = array('ret' => 'ok', 'msg' => 'Concluido com sucesso');
		} else {
			$json = array('ret' => 'error', 'msg' => 'Problema ao alterar status');
		}

		return json_encode($json);
	}//FINAL FUNCTION concluir

	/**
	 * [vigencia_ppra_pcmso description]
	 * 
	 * metodo para gerar os filtros
	 * 
	 * @param  [type] $unidade [description]
	 * @return [type]          [description]
	 */
	public function vigencia_ppra_pcmso($unidade=null) 
	{
		//seta o titulo da pagina
    	$this->pageTitle = 'Vigência PGR - PCMSO';
		
		// PD-154
		$Configuracao = &ClassRegistry::init('Configuracao');
		$codigo_servico_ppra = $Configuracao->getChave('CODIGO_ORDEM_SERVICO_PPRA');
		$codigo_servico_pcmso = $Configuracao->getChave('CODIGO_ORDEM_SERVICO_PCMSO');

		//guarda em sessao os filtros
		$filtros = $this->Filtros->controla_sessao($this->data, 'OrdemServico');

		if(!isset($filtros['codigo_cliente'])) {
			$filtros['codigo_cliente'] = null;
		}

		if(!isset($filtros['status'])) {
			$filtros['status'] = array('VI');
		}

		if(empty($filtros['data_inicio'])) {
			$filtros['data_inicio'] = '01/'.date('m/Y');
			$filtros['data_fim'] = date('d/m/Y');
		}

		//pega o usuario que esta logda para saber se tem um cliente relacionado nao deixarndo filtrar outro cliente
		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
			$filtros['codigo_empresa'] = $_SESSION['Auth']['Usuario']['codigo_empresa'];
		}

		//seta os dados do filtro para o array
		$this->data['OrdemServico'] = $filtros;

		$this->carrega_combos_grupo_economico('OrdemServico');
		$this->carrega_combo_status();
		$this->carrega_combo_ordem();

		$produtos = array($codigo_servico_ppra => 'PGR', $codigo_servico_pcmso => 'PCMSO');
		$this->set(compact('produtos'));

    } // fim vigencia_ppra_pcmso
    
    /**
     * [vigencia_ppra_pcmso_listagem description]
     * 
     * metodo para listar os dados da vigencia do pcmso e ppra
     * 
     * 
     * @return [type] [description]
     */
    public function vigencia_ppra_pcmso_listagem($export=null) 
    {
		//pega o filtro realizado
       	$filtros = $this->Filtros->controla_sessao($this->data, 'OrdemServico');
       	//verifica se o usuario é um cliente
       	if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
       		//seta o filtro do usuario cliente
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
			$filtros['codigo_empresa'] = $_SESSION['Auth']['Usuario']['codigo_empresa'];
		}

		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 300); // 5min

		// PD-154
		$Configuracao = &ClassRegistry::init('Configuracao');
		$codigo_servico_ppra = $Configuracao->getChave('CODIGO_ORDEM_SERVICO_PPRA');
		$codigo_servico_pcmso = $Configuracao->getChave('CODIGO_ORDEM_SERVICO_PCMSO');

		$this->set('codigo_servico_ppra', $codigo_servico_ppra);
		$this->set('codigo_servico_pcmso', $codigo_servico_pcmso);

		//variavel auxiliar
		$dados = array();
		//verifica se tem um cliente selecionado para pegar seus dados
		if (!empty($filtros)) {

			// pr($filtros);

			//seta as condições dos filtros
			$conditions = $this->OrdemServico->converteFiltroEmCondition_Vigencia($filtros);

			//caso seja export gera a query e direciona para o metodo que irá exportar os dados em csv.
			if($export){
				
				//query das vigencias
				$query = $this->OrdemServico->vigencia_ppra_pcmso('sql', compact('conditions','filtros'));
				
				//exporta os dados
				$this->exportBaixarVigencia($query);

			}//fim if export

			//pega os dados das vigencias
			$dados = $this->OrdemServico->vigencia_ppra_pcmso('all', compact('conditions','filtros'));
			// $dados = $this->OrdemServico->vigencia_ppra_pcmso('sql', compact('conditions','filtros'));
			// pr($dados);exit;

		}//fim if codigo cliente		
		
		$this->set(compact('dados'));
    
    }//fim vigencia_ppra_pcmso_listagem

    /**
     * Metodo para montar os arrays de carregamento dos combos
     */ 
	public function carrega_combos_grupo_economico($model) 
	{
		
		//pega as unidades
		$unidades = $this->GrupoEconomicoCliente->lista($this->data[$model]['codigo_cliente']);
		//seta os valores para recuperar na view
		$this->set(compact('unidades'));

    } //fim carrega_combos_grupo_economico

    /**
     * metodo para pegar os tipos de pesquisa que ira existir
     */ 
    public function carrega_combo_status() 
    {
    	//tipos de periodo
		$status = array(
			'VI' => 'Vigente',
			'VE' => 'Vencido',
			'SV' => 'Sem Vigência',
			'AV' => 'À Vencer'
		);

		$this->set(compact('status'));
    } //fim carrega_combo_periodo

    /**
     * [exportBaixarVigencia description]
     * 
     * METODO PARA GERA OS DADOS EM CSV E REALIZAR O DOWNLOAD
     * 
     * 
     * @param  [type] $query [description]
     * @return [type]        [description]
     */
    public function exportBaixarVigencia($query)
    {
    	//executa a query
    	$dbo = $this->OrdemServico->getDataSource();
		$dbo->results   = $dbo->rawQuery($query);

		// pr($dbo->results);

		ob_clean();//limpa o cache dos dados

		$relatorio_padrao_encoding =  'UTF-8';

		//seta os headers
		header('Content-Encoding: '.$relatorio_padrao_encoding);
		header("Content-Type: application/force-download;charset=".$relatorio_padrao_encoding);
		header('Content-Disposition: attachment; filename="Vigencia_ppra_pcmso'.date('YmdHis').'.csv"');
		header('Pragma: no-cache');
		
		//gera o titulo do csv
		echo Comum::converterEncodingPara('"Código do Cliente";"Razão Social";"Nome fantasia";"CNPJ";"Cidade";"Estado";"Produto";"Funcionários alocados";"Início Vigência";"Período Vigência(em meses)";"Vencimento";"Status(Vencido,Vigente,À Vencer)";"Logradouro";"Numero";"Complemento";"Bairro";', $relatorio_padrao_encoding)."\n";

		$texto = "-";

		//varre os dados para montar a planilha
		while ($value = $dbo->fetchRow()) {

			//seta corretamente o status
			$status = ucwords(strtolower($value[0]['status']));

			//verifica as datas
			$inicio_vigencia = ((!empty($value['OrdemServico']['inicio_vigencia_pcmso'])) ? AppModel::dbDateToDate($value['OrdemServico']['inicio_vigencia_pcmso']) : $texto);
			$vigencia_em_meses = ((!empty($value['OrdemServico']['vigencia_em_meses'])) ? $value['OrdemServico']['vigencia_em_meses'] : $texto);
			$final_vigencia = ((!empty($value[0]['final_vigencia'])) ? AppModel::dbDateToDate($value[0]['final_vigencia']) : $texto);

			//monta os dados
			$linha  = $value['Cliente']['codigo'].';';			
			$linha .= Comum::converterEncodingPara($value['Cliente']['razao_social'], $relatorio_padrao_encoding).';';
			$linha .= Comum::converterEncodingPara($value['Cliente']['nome_fantasia'], $relatorio_padrao_encoding).';';
			$linha .= $value[0]['cnpj'].';';
			$linha .= $value['ClienteEndereco']['cidade'].';';
			$linha .= $value['ClienteEndereco']['estado_descricao'].';';
			$linha .= Comum::converterEncodingPara($value['Servico']['descricao'], $relatorio_padrao_encoding).';';//2 utf8_encode
			$linha .= $value[0]['total_funcionario'].';';
			$linha .= $inicio_vigencia.';';
			$linha .= $vigencia_em_meses.';';
			$linha .= $final_vigencia.';';
			$linha .= $status.';';
            $linha .= $value['ClienteEndereco']['logradouro'].';';
            $linha .= $value['ClienteEndereco']['numero'].';';
            $linha .= Comum::converterEncodingPara($value['ClienteEndereco']['complemento'], $relatorio_padrao_encoding).';';
            $linha .= $value['ClienteEndereco']['bairro'].';';
			
			$linha = utf8_decode($linha)."\n";
			
			// debug($value);
			echo $linha;
			
		}//fim loop

		//finaliza o metodo
		exit;

    } //fim export

    public function modal_ppra_pendente($codigo_unidade,$codigo_setor,$codigo_cargo,$codigo_funcionario){

    	$dados_modal = $this->AplicacaoExame->dados_modal_ppra_pendente($codigo_unidade,$codigo_setor,$codigo_cargo,$codigo_funcionario);

		$this->set(compact('dados_modal'));
	}

	public function carrega_combo_ordem() 
    {
    	//tipos de periodo
		$ordenacao = array(
			'1' => 'Alfabética Crescente',
			'2' => 'Alfabética Decrescente'
		);

		$this->set(compact('ordenacao'));
    } //fim carrega_combo_periodo


}//FINAL CLASS AplicacaoExamesController