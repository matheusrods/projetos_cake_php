<?php
class FichasAssistenciaisController extends AppController {

	public $name = 'FichasAssistenciais';  
	public $helpers = array('BForm');
	public $uses = array('FichaAssistencial', 'PedidoExame','Atestado','AtestadoCid');
	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array('imprimir_ficha_assistencial','imprimir_receita_medica','imprimir_atestado_medico'));
	}//FINAL FUNCTION beforeFilter

	public function listagem() {
		$this->layout = 'ajax';
		
		$filtros = $this->Filtros->controla_sessao($this->data, $this->FichaAssistencial->name);
		$conditions = $this->FichaAssistencial->converteFiltroEmCondition($filtros);
		
		if(!is_null($this->BAuth->user('codigo_cliente'))) {
			$conditions['ClienteFuncionario.codigo_cliente_matricula'] = $this->BAuth->user('codigo_cliente');
		}
		
		$this->PedidoExame 					= ClassRegistry::init('PedidoExame');
		$this->ClienteFuncionario 			= ClassRegistry::init('ClienteFuncionario');
		$this->Cliente 						= ClassRegistry::init('Cliente');
		$this->Funcionario 					= ClassRegistry::init('Funcionario');
		$this->Medico 						= ClassRegistry::init('Medico');
		$this->Atestado 					= ClassRegistry::init('Atestado');
		$this->FichaAssistencialResposta 	= ClassRegistry::init('FichaAssistencialResposta');

		$order = 'FichaAssistencial.codigo';
		$joins = array(
			array(
				'table' => "{$this->PedidoExame->databaseTable}.{$this->PedidoExame->tableSchema}.{$this->PedidoExame->useTable}",
				'alias' => 'PedidoExame',
				'type' => 'INNER',
				'conditions' => 'PedidoExame.codigo = FichaAssistencial.codigo_pedido_exame'		
				),
			array(
				'table' => "{$this->ClienteFuncionario->databaseTable}.{$this->ClienteFuncionario->tableSchema}.{$this->ClienteFuncionario->useTable}",
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario'		
				),
			array(
				'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente_matricula'		
				),
			array(
				'table' => "{$this->Funcionario->databaseTable}.{$this->Funcionario->tableSchema}.{$this->Funcionario->useTable}",
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'		
				),
			array(
				'table' => "{$this->Medico->databaseTable}.{$this->Medico->tableSchema}.{$this->Medico->useTable}",
				'alias' => 'Medico',
				'type' => 'INNER',
				'conditions' => 'Medico.codigo = FichaAssistencial.codigo_medico'		
				),
			array(
				'table' => "{$this->Atestado->databaseTable}.{$this->Atestado->tableSchema}.{$this->Atestado->useTable}",
				'alias' => 'Atestado',
				'type' => 'LEFT',
				'conditions' => 'Atestado.codigo = FichaAssistencial.codigo_atestado'		
				),
			array(
				'table' => "{$this->FichaAssistencialResposta->databaseTable}.{$this->FichaAssistencialResposta->tableSchema}.{$this->FichaAssistencialResposta->useTable}",
				'alias' => 'FichaAssistencialResposta',
				'type' => 'INNER',
				'conditions' => 'FichaAssistencialResposta.codigo_ficha_assistencial = FichaAssistencial.codigo AND FichaAssistencialResposta.codigo_ficha_assistencial_questao = 177'		
				),
			);
		$fields = array(
			'FichaAssistencial.*',
			'Cliente.razao_social',
			'Funcionario.nome',
			'Funcionario.codigo',
			'Medico.nome',
			'PedidoExame.codigo',
			'Atestado.exibir_ficha_assistencial',
			'FichaAssistencialResposta.resposta'
			);

		$this->paginate['FichaAssistencial'] = array(
			'conditions' => $conditions,
			'fields' => $fields,
			'limit' => 50,
			'joins' => $joins,
			'order' => $order
			);

		$fichas_assistenciais = $this->paginate('FichaAssistencial');

		$this->set(compact('fichas_assistenciais'));
		//$this->Filtros->limpa_sessao($this->FichaAssistencial->name);
	}//FINAL FUNCTION listagem

	public function listagemPedidoDeExame() {
		
		$this->layout = 'ajax'; 
		$filtros = $this->Filtros->controla_sessao($this->data, $this->FichaAssistencial->name);
		
		if(!is_null($this->BAuth->user('codigo_fornecedor'))) {
			$filtros['codigo_fornecedor'] = $this->BAuth->user('codigo_fornecedor');
		}

		/***************************************************
		 * validacao adicionado para evitar o cliente de
		 * burlar o acesso e ver dados de outros clientes;
		 ***************************************************/
		if(!is_null($this->BAuth->user('codigo_cliente'))) {
			$filtros['codigo_cliente'] = $this->BAuth->user('codigo_cliente');
		}
		
		$conditions = $this->FichaAssistencial->converteFiltroPedidoExameEmCondition($filtros);
		//Não retorna os pedidos cancelados
		$conditions['PedidoExame.codigo_status_pedidos_exames <>'] = 5;

		$codigo_empresa = $this->Session->read('Auth.Usuario.codigo_empresa');

		$this->Configuracao = ClassRegistry::init('Configuracao');
		$conditions_config_examne = array('chave' => 'FICHA_ASSISTENCIAL', 'codigo_empresa' => $codigo_empresa);
		$configuracao_exame = $this->Configuracao->find("first", array('conditions' => $conditions_config_examne));

		$CODIGO_EXAME = $configuracao_exame['Configuracao']['valor'];

		$conditions[] = array("ItemPedidoExame.codigo_exame IN ({$CODIGO_EXAME})");

		$order = 'PedidoExame.codigo';

		$this->ClienteFuncionario 	= ClassRegistry::init('ClienteFuncionario');
		$this->Cliente 				= ClassRegistry::init('Cliente');
		$this->Funcionario 			= ClassRegistry::init('Funcionario');
		$this->ItemPedidoExame 		= ClassRegistry::init('ItemPedidoExame');
		$this->FichaAssistencial 	= ClassRegistry::init('FichaAssistencial');
	
		$joins = array(
			array(
				'table' => "{$this->ClienteFuncionario->databaseTable}.{$this->ClienteFuncionario->tableSchema}.{$this->ClienteFuncionario->useTable}",
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario'
			),
			array(
				'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente_matricula'
			),
			array(
				'table' => "{$this->Funcionario->databaseTable}.{$this->Funcionario->tableSchema}.{$this->Funcionario->useTable}",
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
			),
			array(
				'table' => "{$this->ItemPedidoExame->databaseTable}.{$this->ItemPedidoExame->tableSchema}.{$this->ItemPedidoExame->useTable}",
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo'
			),
			array(
				'table' => "{$this->FichaAssistencial->databaseTable}.{$this->FichaAssistencial->tableSchema}.{$this->FichaAssistencial->useTable}",
				'alias' => 'FichaAssistencial',
				'type' => 'LEFT',
				'conditions' => 'FichaAssistencial.codigo_pedido_exame = PedidoExame.codigo'
			),
		);
		
		$conditions[] = array("FichaAssistencial.codigo_pedido_exame Is NULL");

		$fields = array(
			'PedidoExame.codigo',
			'Cliente.razao_social',
			'Funcionario.nome',
			);

		$this->paginate['PedidoExame'] = array(		
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields,
			'group' => $fields,
			'limit' => 50,
			'order' => $order,
			);

		// $resultado = $this->PedidoExame->find('sql', array(		
		// 	'conditions' => $conditions,
		// 	'joins' => $joins,
		// 	'fields' => $fields,
		// 	'group' => $fields,
		// 	'limit' => 50,
		// 	'order' => $order,
		// ));

		// die(debug($resultado));

		$pedidosExames = $this->paginate('PedidoExame');
		$this->set(compact('pedidosExames'));
		$this->Filtros->limpa_sessao($this->FichaAssistencial->name);
	}//FINAL FUNCTION listagemPedidoDeExame

	public function index(){
		$this->pageTitle = 'Cadastro de Fichas Assistenciais';
	}//FINAL FUNCTION index

	public function incluir($codigoPedidoExame = null, $redir = null) {
		
		/***************************************************
		 * validacao adicionado para evitar o cliente de
		 * burlar o acesso e ver dados de outros clientes;
		 ***************************************************/
		if(!is_null($this->BAuth->user('codigo_cliente'))) {
			
			$codigo_cliente = $this->BAuth->user('codigo_cliente');
			$dados_pedido = $this->PedidoExame->retornaPedido($codigoPedidoExame);

			//verifica se é multicliente
			if(is_array($codigo_cliente)) {
				//verifica se existe no multi cliente o cliente matricula que esta querendo incluir
				if(!in_array($dados_pedido['ClienteFuncionario']['codigo_cliente_matricula'], $codigo_cliente)) {
					$this->BSession->setFlash('acesso_nao_permitido');
					$this->redirect(array('controller' => 'fichas_assistenciais', 'action' => 'selecionarPedidoDeExameAssistencial'));
				}//fim cliente matricula
			}//fim holding
			else {
				if($dados_pedido['ClienteFuncionario']['codigo_cliente_matricula'] != $codigo_cliente) {
					$this->BSession->setFlash('acesso_nao_permitido');
					$this->redirect(array('controller' => 'fichas_assistenciais', 'action' => 'selecionarPedidoDeExameAssistencial'));
				}

			}


		}
		
		
		//valida se existe o pedido de exame selecionado, senao retorna a index e exibe erro
		$this->FichaAssistencial->PedidoExame->id = $codigoPedidoExame;
		if(!$this->FichaAssistencial->PedidoExame->exists()) {
			$this->BSession->setFlash('erro_pedido_exame');
			$this->redirect(array('action' => 'index'));
		}

		//valida se o pedido de exame não está cancelado
		$pedido_exame = $this->FichaAssistencial->PedidoExame->read();
		if($pedido_exame['PedidoExame']['codigo_status_pedidos_exames'] == 5) {
			$this->BSession->setFlash(array('alert alert-error','O pedido de exame selecionado foi cancelado.'));
			$this->redirect(array('action' => 'index'));
		}

		$this->pageTitle = 'Incluir Ficha Assistencial';
		
		if($this->RequestHandler->isPost()) {

			//valida o formulário
			$this->data['FichaAssistencial']['codigo_pedido_exame'] = $codigoPedidoExame; // reatribui por seguranca

			//perfil medico(cliente) / medico(prestador) para preenchimento automatico
			if($this->BAuth->user('codigo_uperfil') == 19 || $this->BAuth->user('codigo_uperfil') == 11) {
				if(empty($this->data['FichaAssistencial']['hora_fim_atendimento'])) {
					$this->data['FichaAssistencial']['hora_fim_atendimento'] = date("H:i");
				}
			}//fim perfil

			$this->FichaAssistencial->set($this->data);
			$this->FichaAssistencial->FichaAssistencialResposta->set($this->data);
			$this->FichaAssistencial->FichaAssistencialResposta->validates();
			
			//se validacao estiver ok, entao salve
			if($this->FichaAssistencial->FichaAssistencialResposta->validates()) {
				try{
					$this->FichaAssistencial->query('begin transaction');

					if($this->FichaAssistencial->incluir($this->data)) {

						$atestadoMedico = $this->data['FichaAssistencial']['AtestadoMedico'];
						unset($this->data['FichaAssistencial']['AtestadoMedico']);

						// $atestadoMedico['exibir_ficha_assistencial'] = 1;
						
						if($atestadoMedico['exibir_ficha_assistencial'] == 1){

							$endereco_atestado = self::getMontaEnderecoFornecedor($codigoPedidoExame);

							$info_funcionario = self::getInfoFuncionario( $codigoPedidoExame );

							$cids10 = $atestadoMedico['cid10'];						
							unset($atestadoMedico['cid10']);	

							$dados['Atestado'] = array_merge($endereco_atestado, $atestadoMedico, $info_funcionario);
							$dados['Atestado']['codigo_medico'] = $this->data['FichaAssistencial']['codigo_medico'];
										
							$this->Atestado = ClassRegistry::init('Atestado');

							if($this->Atestado->incluir($dados)){

								$cids = self::montaInserirAtestadosCid($cids10, $this->Atestado->id);
								
								if(count($cids) > 0){
									$this->AtestadoCid = ClassRegistry::init('AtestadoCid');
									
									foreach($cids as $cid){
										if(!$this->AtestadoCid->incluir($cid)){
											throw new Exception('Problema ao incluir em Atestado Cid');
										}//FINAL INCLUIR ATESTADO CID
									}//FINAL FOREACH $cids
								}//FINAL COUNT $cids MAIOR QUE ZERO

								$this->data['FichaAssistencial']['codigo'] 			= $this->FichaAssistencial->id;
								$this->data['FichaAssistencial']['codigo_atestado'] = $this->Atestado->id;

								if(!$this->FichaAssistencial->atualizar($this->data)){
									throw new Exception('Problema ao atualizar ficha assistencial');
								}
							}else{
								self::validaCamposAtestado($atestadoMedico);
								throw new Exception('Problema ao incluir atestado');
							}//FINAL IF incluir ATESTADO
						}//FINAL SE $atestadoMedico['exibir_ficha_assistencial']
							
						$this->FichaAssistencial->commit();

						$this->BSession->setFlash('save_success');
						
						if(!is_null($this->data['FichaAssistencial']['redir'])){
							if($this->data['FichaAssistencial']['redir'] == 'agenda') {
								$this->redirect(array('controller' => 'consultas_agendas','action' => 'index2'));
							}else{
								$this->redirect(array('action' => 'index'));
							}
						}else{
							$this->redirect(array('action' => 'index'));
						}
					}else{
						throw new Exception('Problema ao incluir Ficha Assistencial');
					}//FINAL INCLUIR FichaAssistencial
				} catch (Exception $e) {
					$this->BSession->setFlash('save_error');
					$this->FichaAssistencial->rollback();
					//die( debug( $e->getMessage() ) );
				}
			}else{
				$this->BSession->setFlash('save_error');
			}
		}//FINAL $this->RequestHandler->isPost()

		$this->set('verificaParecer', $this->FichaAssistencial->verificaParecer($codigoPedidoExame));
		$dados = $this->FichaAssistencial->obtemDadosComplementares($codigoPedidoExame);

		$this->data['FichaAssistencial']['msg_imc'] = $this->get_mensagem_imc('0');

		$this->data['hora_automatica'] = 0;
		//perfil medico(cliente) / medico(prestador)
		if($this->BAuth->user('codigo_uperfil') == 19 || $this->BAuth->user('codigo_uperfil') == 11) {
			$this->data['hora_automatica'] = 1;
			$this->data['FichaAssistencial']['hora_inicio_atendimento'] = date("H:i");
		}
		//debug($dados);
		
		$this->FichaAssistencialTipoUso =& ClassRegistry::init('FichaAssistencialTipoUso');
		$order = array('descricao' => 'ASC');
		$opcoes_tipo_uso = $this->FichaAssistencialTipoUso->find('list', array('fields'=> array('codigo','descricao'),
																			  'order' => $order,
																			  'recursive' => -1 
																			)
																);

		$this->data['FichaAssistencial']['AtestadoMedico']['habilita_afastamento_em_horas'] = '';
		$this->data['FichaAssistencial']['AtestadoMedico']['data_retorno_periodo'] = '';
		

		$this->carrega_combos();

		$this->set(compact('dados','redir', 'opcoes_tipo_uso'));

		$this->set('questoes', $this->FichaAssistencial->montaQuestoes($dados['Funcionario']));
	}//FINAL FUNCTION incluir

	/**
	 * [Método para montar o endereço do fornecedor baseado em item pedido]
	 * @param  [int] $codigoPedidoExame [Código do Pedido de Exame]
	 * @return [array]                  [Retorna um array com os dados obrigatorios para serem incluindo junto ao atestdo]
	 */
	private function getMontaEnderecoFornecedor($codigoPedidoExame){

		$fields_ipe = array(
			"FornecedoresEndereco.logradouro as endereco",
			'FornecedoresEndereco.numero',
			'FornecedoresEndereco.complemento',
			'FornecedoresEndereco.bairro as bairro',
			'FornecedoresEndereco.cep as cep',
			'FornecedoresEndereco.estado_descricao as estado',
			'FornecedoresEndereco.cidade AS cidade',
			'FornecedoresEndereco.longitude',
			'FornecedoresEndereco.latitude'
		);

		$joins_ipe = array(
			array(
				'table' => 'fornecedores_endereco',
				'alias' => 'FornecedoresEndereco',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_fornecedor = FornecedoresEndereco.codigo_fornecedor'
			)
		);

		$conditions_ipe = array('ItemPedidoExame.codigo_pedidos_exames' => $codigoPedidoExame);

		$this->ItemPedidoExame = ClassRegistry::init('ItemPedidoExame');

		$retorno_endereco_fornecedor = $this->ItemPedidoExame->find('all', array('fields' => $fields_ipe, 
																				 'joins' => $joins_ipe,
																				 'conditions' => $conditions_ipe)
																			);

		$endereco_atestado = array();

		if(count($retorno_endereco_fornecedor) < 2){
			$endereco 			 = $retorno_endereco_fornecedor['0']['0'];
			$endereco_fornecedor = $retorno_endereco_fornecedor['0']['FornecedoresEndereco'];

			$endereco_atestado 	 = array_merge($endereco,$endereco_fornecedor);
		}

		return $endereco_atestado;
	}//FINAL FUNCTION getMontaEnderecoFornecedor

	/**
	 * [Metódo para retornar o codigo_cliente_funcionario e codigo_func_setor_cargo, para inserir em atestado]
	 * @param  [int] $codigoPedidoExame [Código do Pedido de Exame]
	 * @return [array]                  [retorna um array com os dados citados acima]
	 */
	private function getInfoFuncionario( $codigoPedidoExame ){
		$fields_info_funcionario	 = array('codigo_cliente_funcionario', 'codigo_func_setor_cargo');
		$conditions_info_funcionario = array('codigo' => $codigoPedidoExame);


		$retorno_info_funcionario = $this->PedidoExame->find('first', array(
				'fields' => $fields_info_funcionario,
				'conditions' => $conditions_info_funcionario)
		);

		$info_funcionario = array();

		if($info_funcionario > 0){
			$info_funcionario = $retorno_info_funcionario['PedidoExame'];
		}

		return $info_funcionario;
	}//FINAL FUNCTION getInfoFuncionario

	/**
	 * [montaInserirAtestadosCid Monta array para inserir em atestados Cid]
	 * @param  [array] $cids10 [array com a(s) descrições de cids]
	 * @param  [int]   $atestado_id [id do ultimo atestado incluindo]
	 * @return [array]         [retorna array para inserir em atestados Cid]
	 */
	private function montaInserirAtestadosCid($cids10, $atestado_id){
		$fields_cid = array('codigo');

		$conditions_cid['descricao'] = Set::extract('{n}.doenca',$cids10);

		$this->Cid = ClassRegistry::init('Cid');
		$retorno_cid = $this->Cid->find('all', array('fields' => $fields_cid,
													 'conditions' => $conditions_cid)
										);


		$codigos_cid = Set::extract('{n}.Cid', $retorno_cid);


		$cids = array();
		foreach($codigos_cid as $codigo_cid){
			$cids[]['AtestadoCid']		= array('codigo_cid' => $codigo_cid['codigo'], 
									'codigo_atestado' => $this->Atestado->id);
		}

		return $cids;

	}//FINAL FUNCTION montaInserirAtestadosCid

	public function carrega_combos() {

		$this->loadModel('MotivoAfastamento');
		$this->loadModel('TipoLocalAtendimento');
		$this->loadModel('EnderecoEstado');
		$this->loadModel('Esocial');

        $MotivoAfastamento = $this->MotivoAfastamento->find('list', array('fields' => 'descricao','order' => 'descricao'));
        
        $TipoLocalAtendimento = $this->TipoLocalAtendimento->find('list', array('fields' => 'descricao', 'order' => 'descricao'));
        
        $estados = $this->EnderecoEstado->find('list', array('conditions' => array('codigo_endereco_pais' => 1), 'fields' => array('codigo', 'descricao')));
        
        $estados[''] = 'UF';
        ksort($estados);
        
		$motivo_afastamento_esocial = $this->Esocial->carrega_motivo_afastamento_esocial();
		
		$refactory = array();

		foreach ($motivo_afastamento_esocial as $dados) {	
      		$refactory[$dados['Esocial']['codigo']] = $dados[0]['descricao'];
    	}
    	$motivo_afastamento_esocial = $refactory;
		
		$this->set(compact('MotivoAfastamento','TipoLocalAtendimento', 'estados', 'motivo_afastamento_esocial'));    
  	}//FINAL FUNCTION carrega_combos

	public function selecionarPedidoDeExameAssistencial() {
		$this->pageTitle = 'Selecionar pedido de exame Assistencial';
	}//FINAL FUNCTION selecionarPedidoDeExameAssistencial

	public function editar($codigo = null , $redir = null) {
		$this->pageTitle = 'Editar Ficha Assistencial';
		$this->set(compact('codigo'));
		
		$fichaAssistencial = $this->FichaAssistencial->findByCodigo($codigo);
		$codigoPedidoExame = $fichaAssistencial['FichaAssistencial']['codigo_pedido_exame'];

		//VALIDAR SE TEM codigo na base

		if($this->RequestHandler->isPost()) {

			//define o codigo pois se trata de edicao
			$this->data['FichaAssistencial']['codigo'] = $codigo;
			
			//valida o formulário
			$this->data['FichaAssistencial']['codigo_pedido_exame'] = $codigoPedidoExame; // reatribui por seguranca

			$this->FichaAssistencial->set($this->data);
			$this->FichaAssistencial->FichaAssistencialResposta->set($this->data);
			$this->FichaAssistencial->FichaAssistencialResposta->validates();

			//se validacao estiver ok, entao salve
			if($this->FichaAssistencial->FichaAssistencialResposta->validates()) {

				try{
					$this->FichaAssistencial->query('begin transaction');
					
					if($this->FichaAssistencial->editar($this->data)) {
						
						//$this->data['FichaAssistencial']['Atestado'] = $this->data['FichaAssistencial']['AtestadoMedico'];

						$atestadoMedico = $this->data['FichaAssistencial']['AtestadoMedico'];
						unset($this->data['FichaAssistencial']['AtestadoMedico']);

						
						//na atualizacao do atestado medico para ficar false o campo pois não estava atualizando
						if($atestadoMedico['exibir_ficha_assistencial'] == 0) {

							//verifica se existe o codigo do atestado
							if(!empty($this->data['FichaAssistencial']['codigo_atestado'])) {
								//seta os dados basicos para atualizacao
								// $dados['Atestado']['codigo'] 						= $this->data['FichaAssistencial']['codigo_atestado'];
								
								if(!$this->AtestadoCid->deleteAll(array('codigo_atestado' => $this->data['FichaAssistencial']['codigo_atestado']))){
									throw new Exception('Problema ao excluir Atestado(s) Cid(s)');
								}//fim delete cids
								else {
									//tenta atualziar o atestado medico
									if($this->Atestado->delete($this->data['FichaAssistencial']['codigo_atestado'])) {
										//limpa a tabela de relacionamento dos cids
									}//fim atualizar
									else{
										self::validaCamposAtestado($atestadoMedico);
										throw new Exception('Problema ao editar atestado');
									}//FINAL IF incluir ATESTADO									
								}

							}//fim codigo atestado

						}//fim if exibir ficha assistencial false
						else if($atestadoMedico['exibir_ficha_assistencial'] == 1) {

							$dados = array();

							$endereco_atestado = self::getMontaEnderecoFornecedor($codigoPedidoExame);

							$info_funcionario = self::getInfoFuncionario( $codigoPedidoExame );

							$cids10 = $atestadoMedico['cid10'];
							unset($atestadoMedico['cid10']);

							$dados['Atestado'] = array_merge($endereco_atestado, $atestadoMedico, $info_funcionario);
											
							$dados['Atestado']['codigo_medico'] 	= $this->data['FichaAssistencial']['codigo_medico'];
							$dados['Atestado']['codigo'] 			= $this->data['FichaAssistencial']['codigo_atestado'];
							//debug($dados);

							if(!empty($dados['Atestado']['codigo'])){

								if($this->Atestado->atualizar($dados)){

									$cids = self::montaInserirAtestadosCid($cids10, $this->Atestado->id);
									
									if(count($cids) > 0) {

										if($this->AtestadoCid->deleteAll(array('codigo_atestado' => $dados['Atestado']['codigo']))){
											foreach($cids as $cid){
												if(!$this->AtestadoCid->incluir($cid)){
													throw new Exception('Problema ao incluir em Atestado Cid');
												}//FINAL INCLUIR ATESTADO CID
											}//FINAL FOREACH $cids
										}else{
											throw new Exception('Problema ao excluir Atestado(s) Cid(s)');
										}
									}//FINAL COUNT $cids MAIOR QUE ZERO
								}else{
									self::validaCamposAtestado($atestadoMedico);
									throw new Exception('Problema ao editar atestado');
								}//FINAL IF incluir ATESTADO
							}
							else{

								unset($dados['Atestado']['codigo']);

								if($this->Atestado->incluir($dados)){

									$cids = self::montaInserirAtestadosCid($cids10, $this->Atestado->id);
									
									if(count($cids) > 0){
										$this->AtestadoCid = ClassRegistry::init('AtestadoCid');
										
										foreach($cids as $cid){
											if(!$this->AtestadoCid->incluir($cid)){
												throw new Exception('Problema ao incluir em Atestado Cid');
											}//FINAL INCLUIR ATESTADO CID
										}//FINAL FOREACH $cids
									}//FINAL COUNT $cids MAIOR QUE ZERO

									$this->data['FichaAssistencial']['codigo'] 			= $this->FichaAssistencial->id;
									$this->data['FichaAssistencial']['codigo_atestado'] = $this->Atestado->id;

									if(!$this->FichaAssistencial->atualizar($this->data)){
										self::validaCamposAtestado($atestadoMedico);
										throw new Exception('Problema ao atualizar ficha assistencial');
									}
								}else{
									self::validaCamposAtestado($atestadoMedico);
									throw new Exception('Problema ao incluir atestado ao editar ficha assistencial');
									
								}//FINAL IF incluir ATESTADO
							}//FINAL SE $dados['Atestado']['codigo'] É DIFERENTE DE VAZIO
						}//FINAL SE $atestadoMedico['exibir_ficha_assistencial']

						$this->FichaAssistencial->commit();

						$this->BSession->setFlash('save_success');

						if(!is_null($this->data['FichaAssistencial']['redir'])) {
							if($this->data['FichaAssistencial']['redir'] == 'agenda') {
								$this->redirect(array('controller' => 'consultas_agendas','action' => 'index2'));
							}
							else{
								$this->redirect(array('action' => 'index'));
							}
						}
						else {
							$this->redirect(array('action' => 'index'));
						}
					} else {
						throw new Exception('Problema ao editar Ficha Assistencial');
					}//FINAL EDITAR FichaAssistencial
				} catch (Exception $e) {
					$this->BSession->setFlash('save_error');
					$this->FichaAssistencial->rollback();
					//debug( $e->getMessage() );
				}	

			} else {
				$this->BSession->setFlash('save_error');
			}
		}//FINAL SE $this->RequestHandler->isPost()

		$this->data = $this->FichaAssistencial->montaRespostas($codigo);
		$this->data['FichaAssistencial'] = $fichaAssistencial['FichaAssistencial'];
		$this->set('verificaParecer', $this->FichaAssistencial->verificaParecer($fichaAssistencial['FichaAssistencial']['codigo_pedido_exame']));
		$dados = $this->FichaAssistencial->obtemDadosComplementares($fichaAssistencial['FichaAssistencial']['codigo_pedido_exame']);

		//Se o médico não foi encontrado é porque está inativo
		if( !empty($this->data['FichaAssistencial']['codigo_medico']) && !isset($dados['Medico'][$this->data['FichaAssistencial']['codigo_medico']])){
			$this->loadModel('Medico');
			$medico_ficha = $this->Medico->find('first', array('fields'=> array('codigo','nome'),
				'conditions' => array('codigo' => $this->data['FichaAssistencial']['codigo_medico'] ),'recursive' => -1 ));
			if(!empty($medico_ficha)){
				$dados['Medico'][$medico_ficha['Medico']['codigo']] = $medico_ficha['Medico']['nome'];
			}
		}
		
		//calcula o imc caso nao esteja calculado
		if(empty($this->data['FichaAssistencial']['imc'])) {
			//pega o peso
			$peso = $this->data['FichaAssistencial']['peso_kg'].".".$this->data['FichaAssistencial']['peso_gr'];
			//pega a altura
			$altura = $this->data['FichaAssistencial']['altura_mt'].".".$this->data['FichaAssistencial']['altura_cm'];
			//calcula o imc
			if($altura <= 0){
				$this->data['FichaAssistencial']['imc'] = number_format(0,1);
			}else{
				$this->data['FichaAssistencial']['imc'] = number_format($peso / ($altura*$altura),1);
			}
		}

		$this->data['FichaAssistencial']['msg_imc'] = $this->get_mensagem_imc($this->data['FichaAssistencial']['imc']);

		$this->data['hora_automatica'] = 0;
		//perfil medico(cliente) / medico(prestador)
		if($this->BAuth->user('codigo_uperfil') == 19 || $this->BAuth->user('codigo_uperfil') == 11) {
			$this->data['hora_automatica'] = 1;
		}

		$this->FichaAssistencialTipoUso =& ClassRegistry::init('FichaAssistencialTipoUso');
		$order = array('descricao' => 'ASC');
		$opcoes_tipo_uso = $this->FichaAssistencialTipoUso->find('list', array('fields'=> array('codigo','descricao'),
																			  'order' => $order,
																			  'recursive' => -1 
																			)
																);
		$this->carrega_combos();
		
		$this->Atestado = ClassRegistry::init('Atestado');

		$retorno_atestado = $this->Atestado->getAtestado($this->data['FichaAssistencial']['codigo_atestado']);

		$this->data['FichaAssistencial']['AtestadoMedico'] = $retorno_atestado;

		$this->AtestadoCid = ClassRegistry::init('AtestadoCid');

		$retorno_atestado_cid = $this->AtestadoCid->getCID($this->data['FichaAssistencial']['codigo_atestado']);

		$this->data['FichaAssistencial']['AtestadoMedico']['cid10'] = $retorno_atestado_cid;

		//debug($this->data['FichaAssistencial']['AtestadoMedico']['cid10']);
		
		//die( debug($this->data) );

		$this->set(compact('dados','redir', 'opcoes_tipo_uso', 'atestado_medico'));
		$this->set('questoes', $this->FichaAssistencial->montaQuestoes($dados['Funcionario']));
	}//FINAL FUNCTION editar

	private function validaCamposAtestado($atestadoMedico){
		$validationErrors = $this->Atestado->invalidFields();

		foreach ($validationErrors as $key => $value) {
			$this->FichaAssistencial->validationErrors['AtestadoMedico'][$key] = $value;
		}

		$dados_atestado = $atestadoMedico;
		$this->set(compact('dados_atestado'));
	}//FINAL FUNCTION validaCamposAtestado

	public function get_mensagem_imc($imc)
	{
		//nao informado
		$msg_imc = 'Não informado!';

		//verifica qual msg
		if(($imc > 0.0) && ($imc < 18.5)){
			$msg_imc = 'Magro ou baixo peso';
		}
		elseif(($imc >= 18.5) && ($imc < 24.99)){
			$msg_imc = 'Normal ou eutrófico';
		}
		elseif(($imc >= 25) && ($imc < 29.99)){
			$msg_imc = 'Sobrepeso ou pré-obeso';
		}
		elseif(($imc >= 30) && ($imc < 34.99)){
			$msg_imc = 'Obesidade';
		}
		elseif(($imc >= 35) && ($imc < 39.99)){
			$msg_imc = 'Obesidade';
		}
		elseif(($imc >= 40)){
			$msg_imc = 'Obesidade (grave)';
		}

		return $msg_imc;
	}//FINAL FUNCTION get_mensagem_imc

	/**
	 * [imprimir_ficha_assistencial description] para impressao da ficha assistencial
	 * @param  [type] $codigo_ficha_assistencial [description]
	 * @param  [type] $codigo_pedido_exame       [description]
	 * @param  [type] $codigo_funcionario        [description]
	 * @return [type]                            [description]
	 */
	public function imprimir_ficha_assistencial($codigo_ficha_assistencial, $codigo_pedido_exame, $codigo_funcionario){
		
		$this->autoRender = false;
		
		//SALVA NA TABELA TEMPORÁRIA OS DADOS SERIALIZADOS PARA A CONSTRUÇÃOI DO RELATORIO PDF
		$this->FichaAssistencial->criaTabelaTemporariaFichaAssistencial($codigo_ficha_assistencial);


		// GERA O RELATORIO PDF
		$this->__jasperConsulta('ficha_assitencial',$codigo_ficha_assistencial, $codigo_pedido_exame, $codigo_funcionario);

	}//fim imprimir ficha assistencial

	/**
	 * [imprimir_atestado_medico description] imprimir o atestado médico
	 * @param  [type] $codigo_ficha_assistencial [description]
	 * @param  [type] $codigo_pedido_exame       [description]
	 * @param  [type] $codigo_funcionario        [description]
	 * @return [type]                            [description]
	 */
	public function imprimir_atestado_medico($codigo_ficha_assistencial, $codigo_pedido_exame, $codigo_funcionario)
	{
		$this->autoRender = false;
		
		// GERA O RELATORIO PDF
		$this->__jasperConsulta('ficha_assistencial_atestado_medico',$codigo_ficha_assistencial, $codigo_pedido_exame, $codigo_funcionario);

	}//fim imprimir_atestado_medico

	/**
	 * [imprimir_receita_medica description] receita medica
	 * @param  [type] $codigo_ficha_assistencial [description]
	 * @param  [type] $codigo_pedido_exame       [description]
	 * @param  [type] $codigo_funcionario        [description]
	 * @return [type]                            [description]
	 */
	public function imprimir_receita_medica($codigo_ficha_assistencial, $codigo_pedido_exame, $codigo_funcionario)
	{
		$this->autoRender = false;

		//SALVA NA TABELA TEMPORÁRIA OS DADOS SERIALIZADOS PARA A CONSTRUÇÃOI DO RELATORIO PDF
		$this->FichaAssistencial->criaTabelaTemporariaReceitaMedica($codigo_ficha_assistencial);
		
		// GERA O RELATORIO PDF
		$this->__jasperConsulta("ficha_assitencial_receita_medica",$codigo_ficha_assistencial, $codigo_pedido_exame, $codigo_funcionario);

	}//fim imprimir receita medica

	private function isJson($json) {
		json_decode($json);
		return (json_last_error() == JSON_ERROR_NONE);
	}//FINAL FUNCTION isJson

	private function jsonToArray($data = null){
		if(!is_null($data)) {
			$json = (array)json_decode($data);
			foreach ($json as $key => $value) {
				if(is_object($value)) {
					$json[$key] = (array)$value;
				} else {
					$json[$key] = $value;
				}
			}
			$data = $json;
		}
		return $data;
	}//FINAL function jsonToArray

	/**
	 * chamada do jasper
	 * @param  [type] $codigo_ficha_assistencial [description]
	 * @param  [type] $codigo_pedido_exame       [description]
	 * @param  [type] $codigo_funcionario        [description]
	 * @return [type]                            [description]
	 */
	private function __jasperConsulta($tipo, $codigo_ficha_assistencial, $codigo_pedido_exame, $codigo_funcionario) {
		
        // opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME'=>'/reports/RHHealth/'.$tipo, // especificar qual relatório
			'FILE_NAME'=> basename( 'relatorio_ficha_assistencial.pdf' ) // nome do relatório para saida
		);

		// parametros do relatorio
		$parametros = array(
			'CODIGO_FICHA_ASSISTENCIAL' => $codigo_ficha_assistencial, 
			'CODIGO_PEDIDO_EXAME' => $codigo_pedido_exame, 
			'CODIGO_FUNCIONARIO' => $codigo_funcionario
		);

		$this->loadModel('Cliente');
		$parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);
		$this->loadModel('MultiEmpresa');
		//codigo empresa emulada
		$codigo_empresa = $this->authUsuario['Usuario']['codigo_empresa'];
		//url logo da multiempresa
		$parametros['URL_LOGO_MULTI_EMPRESA'] = $this->MultiEmpresa->urlLogomarca($codigo_empresa);	
		
		try {
			
			// envia dados ao componente para gerar
			$url = $this->Jasper->generate( $parametros, $opcoes );	

			if($url){
				// se obter retorno apresenta usando cabeçalho apropriado
				header(sprintf('Content-Disposition: attachment; filename="%s"', $opcoes['FILE_NAME']));
				header('Pragma: no-cache');
				header('Content-type: application/pdf');
				echo $url; exit;
			}

		} catch (Exception $e) {
			// se ocorreu erro
			debug($e); exit;
		}		

		exit;
				
	}//FINAL FUNCTION __jasperConsulta

}//FINAL CLASS FichasAssistenciaisController