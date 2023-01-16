<?php
class GruposExposicaoController extends AppController
{
	public $name = 'GruposExposicao';
	public $helpers = array('BForm', 'Html', 'Ajax');

	var $components = array('RequestHandler', 'Session');
	var $uses = array(
		'GrupoExposicao',
		'Cliente',
		'Setor',
		'Cargo',
		'GrupoEconomico',
		'GrupoEconomicoCliente',
		'OrdemServico',
		'Risco',
		'OrdemServicoItem',
		'ClienteSetor',
		'GrupoExposicaoRisco',
		'SetorCaracteristica',
		'ExposicaoOcupacional',
		'GrupoExpRiscoFonteGera',
		'GrupoExposicaoRiscoEpi',
		'GrupoExposicaoRiscoEpc',
		'GrupoRisco',
		'RiscoAtributo',
		'ExposicaoOcupAtributo',
		'GrupoHomogeneo',
		'GrupoHomDetalhe',
		'TecnicaMedicao',
		'Funcionario',
		'FonteGeradora',
		'Epi',
		'Epc',
		'ClienteFuncionario',
		'Funcionario',
		'Medico',
		'Atribuicao',
		'AtribuicaoGrupoExpo',
		'GrupoExpRiscoAtribDet',
		'RiscoAtributoDetalhe',
		'FuncionarioSetorCargo',
		'ValidacaoPpra',
		'TecnicaMedicaoPpra'
	);


	// APAGAR ASSIM QUE CRIAR MIGRATION
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->BAuth->allow('preenche_com_ausencia_risco', 'modal_pcmso_pendente');
	}


	function index($codigo_cliente)
	{

		$dados = $this->OrdemServico->busca_status($codigo_cliente, 'PPRA');
		$visualizar_gge = ($dados[0]['OrdemServico_status'] == 3 ? true : false);

		$this->pageTitle = 'Grupos de Exposição' . ($visualizar_gge ? ' - Visualização' : '');
		$this->data = $this->Filtros->controla_sessao($this->data, $this->GrupoExposicao->name);
		$this->retorna_dados_cliente($codigo_cliente);


		$cargo = $this->Cargo->lista_por_cliente($codigo_cliente);
		$setor = $this->Setor->lista_por_cliente($codigo_cliente);
		$grupo_homogeneo = $this->GrupoHomogeneo->lista_por_cliente($codigo_cliente);

		$this->set(compact('cargo', 'setor', 'grupo_homogeneo', 'visualizar_gge'));
	}

	function retorna_dados_cliente($codigo_cliente)
	{

		$cliente = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);

		if (empty($this->data)) {
			$this->data = array();
		}

		$this->data = array_merge($this->data, $cliente);

		$this->set(compact('codigo_cliente'));
	}

	function retornar_dados_grupo_exposicao($codigo_cliente)
	{
		$joins =  array(
			array(
				'table' => 'clientes_setores',
				'alias' => 'ClientesSetores',
				'type' => 'INNER',
				'conditions' => 'ClientesSetores.codigo_cliente = ' . $codigo_cliente,
			),
		);
		$fields = array('funcionario_entrevistado', 'funcionario_entrevistado_terceiro', 'codigo_medico', 'codigo_funcionario');
		$conditions = array('GrupoExposicao.codigo_cliente_setor = ClientesSetores.codigo');
		$order = 'GrupoExposicao.data_inclusao DESC';

		$dados = $this->GrupoExposicao->find('first', array('fields' => $fields, 'joins' => $joins, 'conditions' => $conditions, 'order' => $order));
		if (!empty($dados['GrupoExposicao']['funcionario_entrevistado_terceiro'])) {
			$dados['GrupoExposicao']['funcionario_entrevistado'] = 0;
		}

		return $dados;
	}

	function listagem($codigo_cliente)
	{

		$dados = $this->OrdemServico->busca_status($codigo_cliente, 'PPRA');
		$visualizar_gge = ($dados[0]['OrdemServico_status'] == 3 ? true : false);

		$this->layout = 'ajax';
		$this->retorna_dados_cliente($codigo_cliente);

		$filtros = $this->Filtros->controla_sessao($this->data, $this->GrupoExposicao->name);

		$conditions = $this->GrupoExposicao->converteFiltroEmCondition($filtros);

		$conditions = array_merge($conditions, array('ClienteSetor.codigo_cliente_alocacao' => $codigo_cliente));

		$funcionario = "";

		$fields = array(
			'DISTINCT GrupoExposicao.codigo',
			'GrupoExposicao.codigo_cargo',
			'ClienteSetor.codigo_cliente',
			'ClienteSetor.codigo_cliente_alocacao',
			'ClienteSetor.codigo_setor',
			'Setor.descricao',
			'Cargo.descricao',
			'GrupoHomogeneo.codigo',
			'GrupoHomogeneo.descricao',
			// 'ClienteFuncionario.codigo_funcionario',
			'Funcionario.codigo',
			'Funcionario.nome'
		);

		$order = 'Setor.descricao ASC, Cargo.descricao ASC';

		$joins  = array(
			array(
				'table' => $this->ClienteSetor->databaseTable . '.' . $this->ClienteSetor->tableSchema . '.' . $this->ClienteSetor->useTable,
				'alias' => 'ClienteSetor',
				'type' => 'LEFT',
				'conditions' => 'ClienteSetor.codigo = GrupoExposicao.codigo_cliente_setor',
			),
			array(
				'table' => $this->Cargo->databaseTable . '.' . $this->Cargo->tableSchema . '.' . $this->Cargo->useTable,
				'alias' => 'Cargo',
				'type' => 'LEFT',
				'conditions' => 'Cargo.codigo = GrupoExposicao.codigo_cargo',
			),
			array(
				'table' => $this->Setor->databaseTable . '.' . $this->Setor->tableSchema . '.' . $this->Setor->useTable,
				'alias' => 'Setor',
				'type' => 'LEFT',
				'conditions' => 'Setor.codigo = ClienteSetor.codigo_setor',
			),
			array(
				'table' => $this->GrupoHomogeneo->databaseTable . '.' . $this->GrupoHomogeneo->tableSchema . '.' . $this->GrupoHomogeneo->useTable,
				'alias' => 'GrupoHomogeneo',
				'type' => 'LEFT OUTER',
				'conditions' => 'GrupoHomogeneo.codigo = GrupoExposicao.codigo_grupo_homogeneo AND ClienteSetor.codigo_cliente_alocacao = GrupoHomogeneo.codigo_cliente',
			),
			array(
				'table' => $this->GrupoHomDetalhe->databaseTable . '.' . $this->GrupoHomDetalhe->tableSchema . '.' . $this->GrupoHomDetalhe->useTable,
				'alias' => 'GrupoHomDetalhe',
				'type' => 'LEFT OUTER',
				'conditions' => 'GrupoHomDetalhe.codigo_setor = Setor.codigo AND GrupoHomDetalhe.codigo_cargo = Cargo.codigo and GrupoHomogeneo.codigo = GrupoHomDetalhe.codigo_grupo_homogeneo',
			),
			/*array(
				'table' => $this->FuncionarioSetorCargo->databaseTable.'.'.$this->FuncionarioSetorCargo->tableSchema.'.'.$this->FuncionarioSetorCargo->useTable,
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'LEFT OUTER',
				'conditions' => array(
					'FuncionarioSetorCargo.codigo_cliente_alocacao = ClienteSetor.codigo_cliente_alocacao', 
					'FuncionarioSetorCargo.codigo_cargo = GrupoExposicao.codigo_cargo',
  					'FuncionarioSetorCargo.codigo_setor = ClienteSetor.codigo_setor',
					"(FuncionarioSetorCargo.data_fim is null OR FuncionarioSetorCargo.data_fim = '')"
					)
				),
			array(
				'table' => $this->ClienteFuncionario->databaseTable.'.'.$this->ClienteFuncionario->tableSchema.'.'.$this->ClienteFuncionario->useTable,
				'alias' => 'ClienteFuncionario',
				'type' => 'LEFT OUTER',
				'conditions' => array(
					'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',					
// 					'ClienteFuncionario.codigo_setor = ClienteSetor.codigo_setor', 
					'ClienteFuncionario.codigo_funcionario = GrupoExposicao.codigo_funcionario' 
// 					'ClienteFuncionario.codigo_cargo = GrupoExposicao.codigo_cargo'
					
					) 
				),*/
			array(
				'table' => $this->Funcionario->databaseTable . '.' . $this->Funcionario->tableSchema . '.' . $this->Funcionario->useTable,
				'alias' => 'Funcionario',
				'type' => 'LEFT OUTER',
				'conditions' => 'GrupoExposicao.codigo_funcionario = Funcionario.codigo'
			),
			array(
				'table' => 'RHHealth.dbo.clientes_setores_cargos',
				'alias' => 'ClienteSetorCargo',
				'type' => 'INNER',
				'conditions' => array('ClienteSetorCargo.codigo_setor = Setor.codigo AND ClienteSetorCargo.codigo_cargo = Cargo.codigo AND ClienteSetor.codigo_cliente_alocacao = ClienteSetorCargo.codigo_cliente_alocacao AND (ClienteSetorCargo.ativo =1  OR ClienteSetorCargo.ativo IS NULL)') //ajuste para o chamado CDCT-428, trazer somente hierarquias ativas
			),
		);

		$this->paginate['GrupoExposicao'] = array(
			'fields' => $fields,
			'conditions' => $conditions,
			'limit' => 50,
			'joins' => $joins,
			'order' => $order
		);

		// pr($this->GrupoExposicao->find('sql', $this->paginate['GrupoExposicao']));
		// debug($this->GrupoExposicao->find('sql', $this->paginate['GrupoExposicao']));
		// die;

		$grupos_exposicao = $this->paginate('GrupoExposicao');

		$this->set(compact('grupos_exposicao', 'codigo_cliente'));

		//$this->loadModel('OrdemServico');
		$codigo_servico_ppra = $this->OrdemServico->getPPRAByCodigoCliente($codigo_cliente);

		$ordemServico = $this->OrdemServico->find(
			'first',
			array(
				'conditions' => array(
					'OrdemServico.codigo_cliente' => $codigo_cliente,
					'OrdemServicoItem.codigo_servico' => $codigo_servico_ppra, //OrdemServico::PPRA
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

		$profissionais = $this->Medico->lista_somente_engenhgeiros_por_cliente($codigo_cliente);

		$this->set(compact('ordemServico', 'profissionais', 'visualizar_gge'));
	}

	/**
	 * Metodo para incluir os dados nas tabelas correspondentes
	 */
	function incluir($codigo_cliente, $setor_ = null, $cargo_ = null, $returnPoint = null)
	{

		if (empty($codigo_cliente)) {
			$this->BSession->setFlash('save_error');
			$this->redirect($this->referer());
		}

		$this->pageTitle = 'Incluir Grupo de Exposição';

		if ($this->RequestHandler->isPost()) {

			//valida as obrigatoriedades principais do formulario
			if (!empty($this->data['GrupoExposicao']['codigo_grupo_homogeneo'])) {
				unset($this->GrupoExposicao->validate['codigo_setor']);
				unset($this->GrupoExposicao->validate['codigo_cargo']);
			}
			if (!empty($this->data['GrupoExposicao']['codigo_cargo'])) {
				unset($this->GrupoExposicao->validate['codigo_grupo_homogeneo']);
			}

			if (isset($this->data['GrupoExposicao']['Outros']) && !empty($this->data['GrupoExposicao']['Outros']) && ($this->data['GrupoExposicao']['funcionario_entrevistado'] == 0)) {
				$this->data['GrupoExposicao']['funcionario_entrevistado'] = NULL;
				$this->data['GrupoExposicao']['funcionario_entrevistado_terceiro'] = $this->data['GrupoExposicao']['Outros'];
				unset($this->data['GrupoExposicao']['Outros']);
			}

			$this->GrupoExposicao->set($this->data);
			$this->GrupoExposicao->validates();
			$this->ClienteSetor->set($this->data);
			$this->ClienteSetor->validates();
			$this->GrupoExposicaoRisco->set($this->data);
			$this->GrupoExposicaoRisco->validates();

			if (isset($this->data['GrupoExposicao']['codigo_grupo_homogeneo']) && !empty($this->data['GrupoExposicao']['codigo_grupo_homogeneo'])) {
				//GRUPO EXPOSICAO GHE. 
				$verifica_grupo_homogeneo = $this->GrupoHomDetalhe->find('all', array('conditions' => array('codigo_grupo_homogeneo' => $this->data['GrupoExposicao']['codigo_grupo_homogeneo'])));

				//VERIFICA SE EXISTE O GHE
				if (!empty($verifica_grupo_homogeneo)) {

					$inserted_data = 0;

					foreach ($verifica_grupo_homogeneo as $key => $grupo_homogeneo) {
						$this->data['ClienteSetor']['codigo_setor'] = $grupo_homogeneo['GrupoHomDetalhe']['codigo_setor'];
						$this->data['GrupoExposicao']['codigo_cargo'] = $grupo_homogeneo['GrupoHomDetalhe']['codigo_cargo'];

						if ($this->GrupoExposicao->incluir($this->data)) {
							$inserted_data++;
						}
					}

					if ($inserted_data != count($verifica_grupo_homogeneo)) {

						if (isset($this->ClienteSetor->validationErrors)) {

							foreach ($this->ClienteSetor->validationErrors as $campo => $erro) {
								if ($campo == "codigo_setor") {
									$campo = "descricao_tipo_setor_cargo";
								}
								$this->ClienteSetor->invalidate($campo, $erro);
							}
						}

						if (isset($this->GrupoExposicao->validationErrors)) {

							foreach ($this->GrupoExposicao->validationErrors as $campo => $erro) {
								if ($campo == "codigo_cliente_setor") {
									$campo = "descricao_tipo_setor_cargo";
								}
								$this->GrupoExposicao->invalidate($campo, $erro);
							}
						}

						if (isset($this->GrupoExposicaoRisco->validationErrors)) {
							$erros = array();
							foreach ($this->GrupoExposicaoRisco->validationErrors as $linha => $erro) {

								// $campo = array_keys($erro);
								if (isset($erro['GrupoExposicaoRisco'])) {
									foreach ($erro['GrupoExposicaoRisco'] as $campo => $valor) {
										if ($campo == "codigo_risco") {
											$erros['codigo_grupo_risco'] = $valor;
											$erros[$campo] = $valor;
										}
									}
									$this->GrupoExposicaoRisco->validationErrors[$linha] = $erros;
								}
							}
						}

						//verifica se tem erros no metodo
						if (isset($this->GrupoExpRiscoFonteGera->validationErrors)) {

							foreach ($this->GrupoExpRiscoFonteGera->validationErrors as $linha => $erro) {
								if (isset($erro['GrupoExpRiscoFonteGera'])) {

									foreach ($erro['GrupoExpRiscoFonteGera'] as $linha_fonte_geradora => $valor) {

										foreach ($valor as $campo => $erros) {
											if ($campo == "codigo_fontes_geradoras") {
												$this->GrupoExposicaoRisco->validationErrors[$linha]['GrupoExpRiscoFonteGera'][$linha_fonte_geradora]['nome'] = $erros;
											}
										}
									}
								}
							}
						}

						//verifica se tem algum erro na efeito critico
						if (isset($this->GrupoExpRiscoAtribDet->validationErrors)) {
							//varre os efeitos criticos
							foreach ($this->GrupoExpRiscoAtribDet->validationErrors as $linha => $erro) {

								if (isset($erro['GrupoExpEfeitoCritico'])) {

									foreach ($erro['GrupoExpEfeitoCritico'] as $linha_efeito_critico => $valor) {

										foreach ($valor as $campo => $erros) {
											if ($campo == "codigo_efeito_critico") {
												$this->GrupoExposicaoRisco->validationErrors[$linha]['GrupoExpEfeitoCritico'][$linha_efeito_critico]['nome'] = $erros;
											}
										}
									}
								}
							}
						}

						if (isset($this->GrupoExposicaoRiscoEpi->validationErrors)) {

							foreach ($this->GrupoExposicaoRiscoEpi->validationErrors as $linha => $erro) {

								if (isset($erro['GrupoExposicaoRiscoEpi'])) {
									foreach ($erro['GrupoExposicaoRiscoEpi'] as $linha_epi => $valor) {

										foreach ($valor as $campo => $erros) {

											if ($campo == "codigo_epi") {
												$this->GrupoExposicaoRisco->validationErrors[$linha]['GrupoExposicaoRiscoEpi'][$linha_epi]['nome'] = $erros;
											}
										}
									}
								}
							}
						}

						if (isset($this->GrupoExposicaoRiscoEpc->validationErrors)) {

							foreach ($this->GrupoExposicaoRiscoEpc->validationErrors as $linha => $erro) {

								if (isset($erro['GrupoExposicaoRiscoEpc'])) {
									foreach ($erro['GrupoExposicaoRiscoEpc'] as $linha_epc => $valor) {

										foreach ($valor as $campo => $erros) {

											if ($campo == "codigo_epc") {
												$this->GrupoExposicaoRisco->validationErrors[$linha]['GrupoExposicaoRiscoEpc'][$linha_epc]['nome'] = $erros;
											}
										}
									}
								}
							}
						}

						$this->BSession->setFlash('save_error');
					} else {
						$this->BSession->setFlash('save_success');
						if (!empty($returnPoint)) {
							$this->redirect(array('controller' => 'consultas', 'action' => 'consulta_ppra_pcmso_pendente_sc/' . $codigo_cliente . '/ppra'));
						} else {
							$this->redirect(array('controller' => 'grupos_exposicao', 'action' => 'index/' . $codigo_cliente));
						}
						// $ref = Comum::UrlOrigem();
						// if( preg_match('/consulta_ppra_pcmso_pendente_sc/', $ref->url) ){
						// 	$this->redirect( $ref->data );	
						// } else {
						// 	$this->redirect($this->referer());	
						// }						
					}
				} else {
					//GRUPO EXPOSICAO GHE. ERRO POIS NÃO ENCONTROU OS GHE.
					$this->GrupoExposicao->validationErrors['codigo_grupo_homogeneo'] = "Setores/Cargos não cadastrados";
					$this->BSession->setFlash('save_error');
				}
			} else {

				//GRUPO EXPOSICAO INDIVIDUAL. 
				// debug($this->data);exit;
				if (!$this->GrupoExposicao->incluir($this->data)) {

					if (isset($this->ClienteSetor->validationErrors)) {

						foreach ($this->ClienteSetor->validationErrors as $campo => $erro) {
							if ($campo == "codigo_setor") {
								$campo = "descricao_tipo_setor_cargo";
							}
							$this->ClienteSetor->invalidate($campo, $erro);
						}
					}

					if (isset($this->GrupoExposicao->validationErrors)) {

						foreach ($this->GrupoExposicao->validationErrors as $campo => $erro) {
							if ($campo == "codigo_cliente_setor") {
								$campo = "descricao_tipo_setor_cargo";
							}
							$this->GrupoExposicao->invalidate($campo, $erro);
						}
					}

					if (isset($this->GrupoExposicaoRisco->validationErrors)) {
						$erros = array();
						foreach ($this->GrupoExposicaoRisco->validationErrors as $linha => $erro) {

							// $campo = array_keys($erro);
							if (isset($erro['GrupoExposicaoRisco'])) {
								foreach ($erro['GrupoExposicaoRisco'] as $campo => $valor) {
									if ($campo == "codigo_risco") {
										$erros['codigo_grupo_risco'] = $valor;
										$erros[$campo] = $valor;
									}
								}
								$this->GrupoExposicaoRisco->validationErrors[$linha] = $erros;
							}
						}
					}

					if (isset($this->GrupoExpRiscoFonteGera->validationErrors)) {

						foreach ($this->GrupoExpRiscoFonteGera->validationErrors as $linha => $erro) {
							if (isset($erro['GrupoExpRiscoFonteGera'])) {
								foreach ($erro['GrupoExpRiscoFonteGera'] as $linha_fonte_geradora => $erro) {

									foreach ($erro as $campo => $valor) {

										if ($campo == "codigo_fontes_geradoras") {
											$this->GrupoExposicaoRisco->validationErrors[$linha]['GrupoExpRiscoFonteGera'][$linha_fonte_geradora]['nome'] = $valor;
										}
									}
								}
							}
						}
					}

					//efeito crítico
					if (isset($this->GrupoExpRiscoAtribDet->validationErrors)) {

						foreach ($this->GrupoExpRiscoAtribDet->validationErrors as $linha => $erro) {
							if (isset($erro['GrupoExpEfeitoCritico'])) {
								foreach ($erro['GrupoExpEfeitoCritico'] as $linha_efeito_critico => $erro) {

									foreach ($erro as $campo => $valor) {

										if ($campo == "codigo_efeito_critico") {
											$this->GrupoExposicaoRisco->validationErrors[$linha]['GrupoExpEfeitoCritico'][$linha_fonte_geradora]['nome'] = $valor;
										}
									}
								}
							}
						}
					}

					if (isset($this->GrupoExposicaoRiscoEpi->validationErrors)) {

						foreach ($this->GrupoExposicaoRiscoEpi->validationErrors as $linha => $erro) {
							if (isset($erro['GrupoExposicaoRiscoEpi'])) {
								foreach ($erro['GrupoExposicaoRiscoEpi'] as $linha_epi => $valor) {

									foreach ($valor as $campo => $erros) {

										if ($campo == "codigo_epi") {
											$this->GrupoExposicaoRisco->validationErrors[$linha]['GrupoExposicaoRiscoEpi'][$linha_epi]['nome'] = $erros;
										}
									}
								}
							}
						}
					}

					if (isset($this->GrupoExposicaoRiscoEpc->validationErrors)) {

						foreach ($this->GrupoExposicaoRiscoEpc->validationErrors as $linha => $erro) {
							if (isset($erro['GrupoExposicaoRiscoEpc'])) {
								foreach ($erro['GrupoExposicaoRiscoEpc'] as $linha_epc => $valor) {

									foreach ($valor as $campo => $erros) {

										if ($campo == "codigo_epc") {
											$this->GrupoExposicaoRisco->validationErrors[$linha]['GrupoExposicaoRiscoEpc'][$linha_epc]['nome'] = $erros;
										}
									}
								}
							}
						}
					}


					$this->BSession->setFlash('save_error');
				} else {


					//monta os dados para gravar na tabela
					$validar = array(
						'codigo_grupo_exposicao' => $this->GrupoExposicao->id,
						'codigo_funcionario'     => $this->data['GrupoExposicao']['codigo_funcionario'],
						'codigo_cliente_alocacao' => $this->data['ClienteSetor']['codigo_cliente_alocacao'],
						'codigo_setor'           => $this->data['ClienteSetor']['codigo_setor'],
						'codigo_cargo' 			 => $this->data['GrupoExposicao']['codigo_cargo'],
						'status_validacao'		 => 0
					);
					// debug($validar);exit;

					//verifica se houve algum erro
					if (!$this->ValidacaoPpra->inserir_validacao_ppra($validar)) {
						$this->BSession->setFlash('save_error');
					}


					$this->BSession->setFlash('save_success');
					if (!empty($returnPoint)) {
						$this->redirect(array('controller' => 'consultas', 'action' => 'consulta_ppra_pcmso_pendente_sc/' . $codigo_cliente . '/ppra'));
					} else {
						$this->redirect(array('controller' => 'grupos_exposicao', 'action' => 'index/' . $codigo_cliente));
					}
					// $ref = Comum::UrlOrigem();
					// if( preg_match('/consulta_ppra_pcmso_pendente_sc/', $ref->url) ){
					// 	$this->redirect( $ref->data );	
					// } else {
					// 	$this->redirect($this->referer());	
					// }
				}
			}
		}

		if (isset($this->data['GrupoExposicao']['codigo_funcionario']) && !empty($this->data['GrupoExposicao']['codigo_funcionario'])) {
			$dados_funcionario = $this->Funcionario->find('first', array('conditions' => array('codigo' => $this->data['GrupoExposicao']['codigo_funcionario'])));
			if (!empty($dados_funcionario)) {
				$this->data = array_merge($this->data, $dados_funcionario);
			}
		}

		if ((isset($this->data['ClienteSetor']['codigo_setor']) && !empty($this->data['ClienteSetor']['codigo_setor'])) && (isset($this->data['GrupoExposicao']['codigo_cargo']) && !empty($this->data['GrupoExposicao']['codigo_cargo']))) {
			$funcionario = $this->Funcionario->lista_por_cliente_setor_cargo('list', $this->data['ClienteSetor']['codigo_cliente_alocacao'], $this->data['ClienteSetor']['codigo_setor'], $this->data['GrupoExposicao']['codigo_cargo']);
			$this->set(compact('funcionario'));
		}

		if (isset($this->data['GrupoHomDetalhe']) && !empty($this->data['GrupoHomDetalhe'])) {
			foreach ($this->data['GrupoHomDetalhe'] as $linha => $dados) {

				if (isset($dados['codigo_setor_ghe']) && !empty($dados['codigo_setor_ghe'])) {
					$setor = $this->Setor->find('first', array('conditions' => array('codigo' => $dados['codigo_setor_ghe'])));
					$this->data['GrupoHomDetalhe'][$linha]['Setor']['codigo'] = $setor['Setor']['codigo'];
					$this->data['GrupoHomDetalhe'][$linha]['Setor']['descricao'] = $setor['Setor']['descricao'];
				}

				if (isset($dados['codigo_cargo_ghe']) && !empty($dados['codigo_cargo_ghe'])) {
					$setor = $this->Cargo->find('first', array('conditions' => array('codigo' => $dados['codigo_cargo_ghe'])));
					$this->data['GrupoHomDetalhe'][$linha]['Cargo']['codigo'] = $setor['Cargo']['codigo'];
					$this->data['GrupoHomDetalhe'][$linha]['Cargo']['descricao'] = $setor['Cargo']['descricao'];
				}
			}
		}

		if (isset($this->data['GrupoExposicaoRisco'])) {

			unset($this->data['GrupoExposicaoRisco']['k']);

			if (!isset($this->data['GrupoExposicao']['descricao_tipo_setor_cargo'])) {
				$this->data['GrupoExposicao']['descricao_tipo_setor_cargo'] = null;
			}

			if (count($this->data['GrupoExposicaoRisco'])) {
				foreach ($this->data['GrupoExposicaoRisco'] as $key => $dados_risco) {

					if (isset($this->data['GrupoExposicaoRisco'][$key]) && !empty($this->data['GrupoExposicaoRisco'][$key])) {

						if (isset($this->data['GrupoExposicaoRisco'][$key]['codigo_grupo_risco']) && !empty($this->data['GrupoExposicaoRisco'][$key]['codigo_grupo_risco'])) {
							$grupo_risco_dados = $this->GrupoRisco->find('first', array('conditions' => array('codigo' => $this->data['GrupoExposicaoRisco'][$key]['codigo_grupo_risco']), 'fields' => array('codigo', 'descricao')));
							$this->data['GrupoExposicaoRisco'][$key]['GrupoRisco'] = $grupo_risco_dados['GrupoRisco'];
						}

						if (isset($this->data['GrupoExposicaoRisco'][$key]['codigo_risco']) && !empty($this->data['GrupoExposicaoRisco'][$key]['codigo_risco'])) {

							$risco_dados = $this->Risco->find('first', array('conditions' => array('codigo' => $this->data['GrupoExposicaoRisco'][$key]['codigo_risco']), 'fields' => array('codigo', 'nome_agente', 'risco_caracterizado_por_ruido', 'risco_caracterizado_por_calor')));
							$this->data['GrupoExposicaoRisco'][$key]['Risco'] = $risco_dados['Risco'];
						}

						if (isset($this->data['GrupoExposicaoRisco'][$key]['resultante']) && !empty($this->data['GrupoExposicaoRisco'][$key]['resultante'])) {
							$resultante = $this->ExposicaoOcupAtributo->find('first', array('conditions' => array('codigo' => $this->data['GrupoExposicaoRisco'][$key]['resultante'], 'codigo_exposicao_ocupacional' => ExposicaoOcupacional::RESULTANTE), 'fields' => array('codigo', 'descricao')));
							$this->data['GrupoExposicaoRisco'][$key]['resultante'] = array('Resultante' => $resultante['ExposicaoOcupAtributo']);
						}

						if (isset($this->data['GrupoExposicaoRisco'][$key]['grau_risco']) && !empty($this->data['GrupoExposicaoRisco'][$key]['grau_risco'])) {
							$grau_risco = $this->ExposicaoOcupAtributo->find('first', array('conditions' => array('codigo' => $this->data['GrupoExposicaoRisco'][$key]['grau_risco'], 'codigo_exposicao_ocupacional' => ExposicaoOcupacional::GRAU_RISCO), 'fields' => array('codigo', 'descricao')));
							$this->data['GrupoExposicaoRisco'][$key]['grau_risco'] = array('GrauRisco' => $grau_risco['ExposicaoOcupAtributo']);
						}

						if (!isset($this->data['GrupoExposicaoRisco'][$key]['descanso_no_local'])) {
							$this->data['GrupoExposicaoRisco'][$key]['descanso_no_local'] = null;
						}

						if (!isset($this->data['GrupoExposicaoRisco'][$key]['carga_solar'])) {
							$this->data['GrupoExposicaoRisco'][$key]['carga_solar'] = null;
						}

						if (isset($this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'])) {

							foreach ($this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'] as $linha_epi => $dados_epi) {

								if (!isset($dados_epi['epi_eficaz'])) $dados_epi['epi_eficaz'] = null;
								$epi = $this->Epi->find('all', array('conditions' => array('codigo' => $dados_epi['codigo_epi']), 'fields' => array('codigo', 'nome', 'numero_ca', 'data_validade_ca', 'atenuacao_qtd')));

								foreach ($epi as $chave => $valor_epi) {
									$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$linha_epi]['codigo'] = $valor_epi['Epi']['codigo'];
									$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$linha_epi]['nome'] = $valor_epi['Epi']['nome'];
									$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$linha_epi]['numero_ca'] = $valor_epi['Epi']['numero_ca'];
									$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$linha_epi]['data_validade_ca'] = $valor_epi['Epi']['data_validade_ca'];
									$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$linha_epi]['epi_eficaz'] = $dados_epi['epi_eficaz'];
									$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$linha_epi]['atenuacao'] = $dados_epi['atenuacao'];
								}
							}
						}

						if (isset($this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpc'])) {
							foreach ($this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpc'] as $linha_epc => $dados_epc) {

								$epc = $this->Epc->find('all', array('conditions' => array('codigo' => $dados_epc['codigo_epc']), 'fields' => array('codigo', 'nome')));

								foreach ($epc as $chave => $valor_epc) {
									$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpc'][$linha_epc]['codigo'] = $valor_epc['Epc']['codigo'];
									$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpc'][$linha_epc]['nome'] = $valor_epc['Epc']['nome'];
								}
							}
						}
					}
				}

				$option_subs_carregado = isset($this->data['GrupoExposicaoRisco']) ? ($this->Risco->find('list', array('conditions' => array('codigo_grupo' => $this->data['GrupoExposicaoRisco'][$key]['GrupoRisco']['codigo']), 'fields' => array('codigo', 'nome_agente')))) : (array());
			}
		}

		$bloqueado = $this->GrupoEconomicoCliente->hierarquia_bloqueada($codigo_cliente);

		$this->retorna_dados_cliente($codigo_cliente);
		$dados_grupo_exposicao = $this->retornar_dados_grupo_exposicao($codigo_cliente);
		$lista_cargo = $this->Cargo->lista_por_cliente($codigo_cliente, $bloqueado);
		$lista_setor = $this->Setor->lista_por_cliente($codigo_cliente, $bloqueado, null, true);
		$ghe = $this->GrupoHomogeneo->lista_por_cliente($this->data['Unidade']['codigo']);
		$grupo_risco = $this->GrupoRisco->retorna_grupo();
		$profissionais = $this->Medico->lista_somente_engenhgeiros_por_cliente($codigo_cliente);
		$pe_direito = $this->SetorCaracteristica->retorna_caracteristica(SetorCaracteristica::PE_DIREITO);
		$iluminacao = $this->SetorCaracteristica->retorna_caracteristica(SetorCaracteristica::ILUMINACAO);
		$ventilacao = $this->SetorCaracteristica->retorna_caracteristica(SetorCaracteristica::VENTILACAO);
		$cobertura = $this->SetorCaracteristica->retorna_caracteristica(SetorCaracteristica::COBERTURA);
		$estrutura = $this->SetorCaracteristica->retorna_caracteristica(SetorCaracteristica::ESTRUTURA);
		$piso = $this->SetorCaracteristica->retorna_caracteristica(SetorCaracteristica::PISO);

		$tempo_exposicao = $this->ExposicaoOcupacional->retorna_exposicao(ExposicaoOcupacional::TEMPO_EXPOSICAO);
		$intensidade = $this->ExposicaoOcupacional->retorna_exposicao(ExposicaoOcupacional::INTENSIDADE);
		$dano = $this->ExposicaoOcupacional->retorna_exposicao(ExposicaoOcupacional::DANO);

		$unidades_medida = $this->TecnicaMedicao->retorna_tecnicas();
		// PC-3177 - Força pegar dados da matriz
		$matriz = $this->GrupoEconomicoCliente->getMatriz($codigo_cliente);
		$forca_matriz = $matriz['GrupoEconomico']['codigo_cliente'];		
		$tecnicas_medicao = $this->TecnicaMedicaoPpra->get_tecnicas_medicao($forca_matriz);

		$meios_exposicao = $this->RiscoAtributo->retorna_exposicao(RiscoAtributo::MEIO_EXPOSICAO);
		if (empty($meios_exposicao))
			$meios_exposicao = array();

		$array_efeito = $this->RiscoAtributo->retorna_exposicao(RiscoAtributo::CLASSIFICACAO_EFEITO_CRITICO);
		if (empty($array_efeito))
			$array_efeito = array();


		$joins  = array(
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
			)
		);

		$conditions = array(
			'ClienteFuncionario.codigo_cliente' => $codigo_cliente
		);


		$funcionarios = $this->Funcionario->find('list', array('joins' => $joins, 'conditions' => $conditions, 'fields' => array('Funcionario.codigo', 'Funcionario.nome')));
		$funcionarios[0] = "Outro";
		//Recupera as atribuições vinculadas ao cliente (matriz)
		$atribuicoes = $this->Atribuicao->find('list', array('conditions' => array('codigo_cliente' => $this->data['Matriz']['codigo'], 'ativo' => '1'), 'fields' => array('codigo', 'descricao'), 'order' => 'descricao'));
		$grupo_risco_inclusao = $this->GrupoRisco->retorna_grupo();

		$this->set(compact(
			'codigo_cliente',
			'cargo',
			'setor',
			'ghe',
			'grupo_risco',
			'pe_direito',
			'iluminacao',
			'ventilacao',
			'cobertura',
			'estrutura',
			'piso',
			'tempo_exposicao',
			'intensidade',
			'dano',
			'unidades_medida',
			'array_efeito',
			'meios_exposicao',
			'funcionarios',
			'profissionais',
			'atribuicoes',
			'dados_grupo_exposicao',
			'option_subs_carregado',
			'setor_',
			'cargo_',
			'lista_setor',
			'lista_cargo',
			'grupo_risco_inclusao',
			'tecnicas_medicao'
		));
	} //fim metodo incluir

	function editar($codigo_cliente, $codigo)
	{

		$dados = $this->OrdemServico->busca_status($codigo_cliente, 'PPRA');
		$visualizar_gge = ($dados[0]['OrdemServico_status'] == 3 ? true : false);

		if (empty($codigo_cliente)) {
			$this->BSession->setFlash('save_error');
			$this->redirect($this->referer());
		}

		$this->pageTitle = ($visualizar_gge ? 'Grupo de Exposição - Visualização' : 'Editar Grupo de Exposição');

		if ($this->RequestHandler->isPost()) {
			
			// remove index 'k' array
			if(isset($this->data['GrupoExposicaoRisco']['k'])){
				unset($this->data['GrupoExposicaoRisco']['k']);
			}

			
			// debug($this->data);die;
			$this->GrupoExposicao->set($this->data);
			$this->GrupoExposicao->validates();
			$this->ClienteSetor->set($this->data);
			$this->ClienteSetor->validates();
			$this->GrupoExposicaoRisco->set($this->data);
			$this->GrupoExposicaoRisco->validates();

			//valida as obrigatoriedades principais do formulario
			if (!empty($this->data['GrupoExposicao']['codigo_grupo_homogeneo'])) {
				unset($this->GrupoExposicao->validate['codigo_setor']);
				unset($this->GrupoExposicao->validate['codigo_cargo']);
			}
			if (!empty($this->data['GrupoExposicao']['codigo_cargo'])) {
				unset($this->GrupoExposicao->validate['codigo_grupo_homogeneo']);
			}

			$erro = 0;
			// Verifica Obrigatoriedade "Técnicas de Medição"
			if (count($this->data['GrupoExposicaoRisco']) > 0) {

				$verifica_grupo_exposicao_risco = $this->data['GrupoExposicaoRisco'];

				foreach ($verifica_grupo_exposicao_risco as $key => $value) {

					if (@$verifica_grupo_exposicao_risco[$key]['codigo_tipo_medicao'] == 1) {

						if (empty($verifica_grupo_exposicao_risco[$key]['codigo_tec_med_ppra'])) {

							$erro = 1;
							//GRUPO EXPOSICAO RISCO. ERRO POIS NÃO SELECIONOU "Técnicas de Medição".							
							$this->BSession->setFlash(array('alert alert-error', 'Campo Quantitativo/Técnicas de Medição não foi selecionado.'));
							$this->redirect($this->referer());
						}
					}
				}
			}
			//================

			if (isset($this->data['GrupoExposicao']['codigo_grupo_homogeneo']) && !empty($this->data['GrupoExposicao']['codigo_grupo_homogeneo'])) {
				//GRUPO EXPOSICAO GHE. 
				$verifica_grupo_homogeneo = $this->GrupoHomogeneo->lista_por_grupo_homogeneo($this->data['GrupoExposicao']['codigo_grupo_homogeneo']);

				// debug($verifica_grupo_homogeneo);exit;

				//$verifica_grupo_homogeneo = $this->GrupoHomDetalhe->find('all', array('conditions' => array('codigo_grupo_homogeneo' => $this->data['GrupoExposicao']['codigo_grupo_homogeneo'])));

				//VERIFICA SE EXISTE O GHE
				if (!empty($verifica_grupo_homogeneo)) {
					$updated_data = 0;

					foreach ($verifica_grupo_homogeneo as $key => $grupo_homogeneo) {

						$this->data['GrupoHomogeneo'] = $grupo_homogeneo['GrupoHomogeneo'];

						if (isset($this->data['GrupoHomDetalhe']) && !empty($this->data['GrupoHomDetalhe'])) {

							if (isset($this->data['GrupoHomDetalhe'][$key])) {
								$this->data['GrupoHomDetalhe']['codigo_setor'] = $this->data['GrupoHomDetalhe'][$key]['codigo_setor_ghe'];
								$this->data['GrupoHomDetalhe']['codigo_cargo'] = $this->data['GrupoHomDetalhe'][$key]['codigo_cargo_ghe'];
								$this->data['GrupoExposicao']['descricao_atividade'] = $this->data['GrupoHomDetalhe'][$key]['descricao_atividade_ghe'];
							}
						} else {
							$this->data['GrupoHomDetalhe'] = $grupo_homogeneo['GrupoHomDetalhe'];
						}

						if (isset($this->data['GrupoExposicao']['Outros']) && !empty($this->data['GrupoExposicao']['Outros'])) {
							$this->data['GrupoExposicao']['funcionario_entrevistado'] = NULL;
							$this->data['GrupoExposicao']['funcionario_entrevistado_terceiro'] = $this->data['GrupoExposicao']['Outros'];
							unset($this->data['GrupoExposicao']['Outros']);
						} else {
							$this->data['GrupoExposicao']['funcionario_entrevistado_terceiro'] = NULL;
						}

						//pega os dados da cliente setor
						if ($this->data['ClienteSetor']['codigo_setor'] != $grupo_homogeneo['GrupoHomDetalhe']['codigo_setor']) {
							$this->data['ClienteSetor']['codigo_setor'] = $grupo_homogeneo['GrupoHomDetalhe']['codigo_setor'];
						}

						//pega os dados do grupo exposicao
						$gr_exposicao = $this->GrupoExposicao->find('first', array('conditions' => array('codigo_cliente_setor' => $this->data['ClienteSetor']['codigo'], 'codigo_cargo' => $grupo_homogeneo['GrupoHomDetalhe']['codigo_cargo'], 'codigo_grupo_homogeneo' => $grupo_homogeneo['GrupoHomogeneo']['codigo'])));

						// debug($gr_exposicao);

						//seta os dados para atualizar o grupo_exposicao
						if (!empty($gr_exposicao)) {

							//verifica se é diferente do codigo que esta passando
							if ($this->data['GrupoExposicao']['codigo'] != $gr_exposicao['GrupoExposicao']['codigo']) {
								//para atualziar o grupo exposicao
								$this->data['GrupoExposicao']['codigo'] = $gr_exposicao['GrupoExposicao']['codigo'];

								//verifica se esse grupo exposicao tem riscos
								$grupo_exposicao_risco = $this->GrupoExposicaoRisco->get_grupo_exposicao_risco($this->data['GrupoExposicaoRisco'], $this->data['GrupoExposicao']['codigo']);

								//verifica se nao esta vazio
								if (!empty($grupo_exposicao_risco)) {
									unset($this->data['GrupoExposicaoRisco']);
									$this->data['GrupoExposicaoRisco'] = $grupo_exposicao_risco;
								}
							}
						}

						// debug($this->data);

						if ($this->GrupoExposicao->atualizar($this->data)) {
							$updated_data++;
						}
					}

					// debug($updated_data."--".count($verifica_grupo_homogeneo));
					// exit;

					if ($updated_data != count($verifica_grupo_homogeneo)) {

						if (isset($this->data['GrupoHomDetalhe']) && !empty($this->data['GrupoHomDetalhe'])) {
							unset($this->data['GrupoHomDetalhe']['codigo_setor']);
							unset($this->data['GrupoHomDetalhe']['codigo_cargo']);
						}


						if (isset($this->ClienteSetor->validationErrors)) {

							foreach ($this->ClienteSetor->validationErrors as $campo => $erro) {
								if ($campo == "codigo_setor") {
									$campo = "descricao_tipo_setor_cargo";
								}
								$this->ClienteSetor->invalidate($campo, $erro);
							}
						}

						if (isset($this->GrupoExposicao->validationErrors)) {

							foreach ($this->GrupoExposicao->validationErrors as $campo => $erro) {
								if ($campo == "codigo_cliente_setor") {
									$campo = "descricao_tipo_setor_cargo";
								}
								$this->GrupoExposicao->invalidate($campo, $erro);
							}
						}


						if (isset($this->GrupoExposicaoRisco->validationErrors)) {
							$erros = array();
							foreach ($this->GrupoExposicaoRisco->validationErrors as $linha => $erro) {

								// $campo = array_keys($erro);
								if (isset($erro['GrupoExposicaoRisco'])) {
									foreach ($erro['GrupoExposicaoRisco'] as $campo => $valor) {
										if ($campo == "codigo_risco") {
											$erros['codigo_grupo_risco'] = $valor;
											$erros[$campo] = $valor;
										}
									}
									$this->GrupoExposicaoRisco->validationErrors[$linha] = $erros;
								}
							}
						}

						if (isset($this->GrupoExpRiscoFonteGera->validationErrors)) {

							foreach ($this->GrupoExpRiscoFonteGera->validationErrors as $linha => $erro) {
								if (isset($erro['GrupoExpRiscoFonteGera'])) {
									foreach ($erro['GrupoExpRiscoFonteGera'] as $linha_fonte_geradora => $valor) {

										foreach ($valor as $campo => $erros) {
											if ($campo == "codigo_fontes_geradoras") {
												$this->GrupoExposicaoRisco->validationErrors[$linha]['GrupoExpRiscoFonteGera'][$linha_fonte_geradora]['nome'] = $erros;
											}
										}
									}
								}
							}
						}

						//efeito critico
						if (isset($this->GrupoExpRiscoAtribDet->validationErrors)) {

							foreach ($this->GrupoExpRiscoAtribDet->validationErrors as $linha => $erro) {
								if (isset($erro['GrupoExpEfeitoCritico'])) {
									foreach ($erro['GrupoExpEfeitoCritico'] as $linha_efeito_critico => $valor) {

										foreach ($valor as $campo => $erros) {
											if ($campo == "codigo_efeito_critico") {
												$this->GrupoExposicaoRisco->validationErrors[$linha]['GrupoExpEfeitoCritico'][$linha_efeito_critico]['nome'] = $erros;
											}
										}
									}
								}
							}
						}

						if (isset($this->GrupoExposicaoRiscoEpi->validationErrors)) {

							foreach ($this->GrupoExposicaoRiscoEpi->validationErrors as $linha => $erro) {
								if (isset($erro['GrupoExposicaoRiscoEpi'])) {
									foreach ($erro['GrupoExposicaoRiscoEpi'] as $linha_epi => $valor) {
										foreach ($valor as $campo => $erros) {
											if ($campo == "codigo_epi") {
												$this->GrupoExposicaoRisco->validationErrors[$linha]['GrupoExposicaoRiscoEpi'][$linha_epi]['nome'] = $erros;
											}
										}
									}
								}
							}
						}


						if (isset($this->GrupoExposicaoRiscoEpc->validationErrors)) {

							foreach ($this->GrupoExposicaoRiscoEpc->validationErrors as $linha => $erro) {
								if (isset($erro['GrupoExposicaoRiscoEpc'])) {
									foreach ($erro['GrupoExposicaoRiscoEpc'] as $linha_epc => $valor) {

										foreach ($valor as $campo => $erros) {

											if ($campo == "codigo_epc") {
												$this->GrupoExposicaoRisco->validationErrors[$linha]['GrupoExposicaoRiscoEpc'][$linha_epc]['nome'] = $erros;
											}
										}
									}
								}
							}
						}

						// debug($this->ClienteSetor->validationErrors);
						// debug($this->GrupoExposicao->validationErrors);
						// debug($this->GrupoExposicaoRisco->validationErrors);

						$this->BSession->setFlash('save_error');
					} else {
						$this->BSession->setFlash('save_success');
						$this->redirect(array('action' => 'index', $codigo_cliente));
					}
				} else {

					// debug($this->GrupoExposicaoRisco->validationErrors);

					$this->BSession->setFlash('save_error');
				}
			} else {
				// debug($this->data);exit;

				//GRUPO EXPOSICAO INDIVIDUAL. 
				if ($this->GrupoExposicao->atualizar($this->data)) {

					//monta os dados para gravar na tabela
					$validar = array(
						'codigo_grupo_exposicao' => $this->data['GrupoExposicao']['codigo'],
						'codigo_funcionario'     => $this->data['GrupoExposicao']['codigo_funcionario_hidden'],
						'codigo_cliente_alocacao' => $this->data['ClienteSetor']['codigo_cliente_alocacao'],
						'codigo_setor'           => $this->data['ClienteSetor']['codigo_setor'],
						'codigo_cargo' 			 => $this->data['GrupoExposicao']['codigo_cargo'],
						'status_validacao'		 => 0
					);
					// debug($validar);exit;

					//verifica se houve algum erro
					if (!$this->ValidacaoPpra->inserir_validacao_ppra($validar)) {
						$this->BSession->setFlash('save_error');
					}

					$this->BSession->setFlash('save_success');
					$this->redirect(array('action' => 'index', $codigo_cliente));
				} //fim if ok atualizacao


				if (isset($this->ClienteSetor->validationErrors)) {

					foreach ($this->ClienteSetor->validationErrors as $campo => $erro) {
						if ($campo == "codigo_setor") {
							$campo = "descricao_tipo_setor_cargo";
						}
						$this->ClienteSetor->invalidate($campo, $erro);
					}
				}

				if (isset($this->GrupoExposicao->validationErrors)) {

					foreach ($this->GrupoExposicao->validationErrors as $campo => $erro) {
						if ($campo == "codigo_cliente_setor") {
							$campo = "descricao_tipo_setor_cargo";
						}
						$this->GrupoExposicao->invalidate($campo, $erro);
					}
				}

				if (isset($this->GrupoExposicaoRisco->validationErrors)) {
					$erros = array();
					foreach ($this->GrupoExposicaoRisco->validationErrors as $linha => $erro) {

						// $campo = array_keys($erro);
						if (isset($erro['GrupoExposicaoRisco'])) {
							foreach ($erro['GrupoExposicaoRisco'] as $campo => $valor) {
								if ($campo == "codigo_risco") {
									$erros['codigo_grupo_risco'] = $valor;
									$erros[$campo] = $valor;
								}
							}
							$this->GrupoExposicaoRisco->validationErrors[$linha] = $erros;
						}
					}
				}

				if (isset($this->GrupoExpRiscoFonteGera->validationErrors)) {

					foreach ($this->GrupoExpRiscoFonteGera->validationErrors as $linha => $erro) {
						if (isset($erro['GrupoExpRiscoFonteGera'])) {
							foreach ($erro['GrupoExpRiscoFonteGera'] as $linha_fonte_geradora => $valor) {

								foreach ($valor as $campo => $erros) {
									if ($campo == "codigo_fontes_geradoras") {
										$this->GrupoExposicaoRisco->validationErrors[$linha]['GrupoExpRiscoFonteGera'][$linha_fonte_geradora]['nome'] = $erros;
									}
								}
							}
						}
					}
				}

				//efeito critico
				if (isset($this->GrupoExpRiscoAtribDet->validationErrors)) {

					foreach ($this->GrupoExpRiscoAtribDet->validationErrors as $linha => $erro) {
						if (isset($erro['GrupoExpEfeitoCritico'])) {
							foreach ($erro['GrupoExpEfeitoCritico'] as $linha_efeito_critico => $valor) {

								foreach ($valor as $campo => $erros) {
									if ($campo == "codigo_efeito_critico") {
										$this->GrupoExposicaoRisco->validationErrors[$linha]['GrupoExpEfeitoCritico'][$linha_efeito_critico]['nome'] = $erros;
									}
								}
							}
						}
					}
				}


				if (isset($this->GrupoExposicaoRiscoEpi->validationErrors)) {

					foreach ($this->GrupoExposicaoRiscoEpi->validationErrors as $linha => $erro) {
						if (isset($erro['GrupoExposicaoRiscoEpi'])) {
							foreach ($erro['GrupoExposicaoRiscoEpi'] as $linha_epi => $valor) {

								foreach ($valor as $campo => $erros) {

									if ($campo == "codigo_epi") {
										$this->GrupoExposicaoRisco->validationErrors[$linha]['GrupoExposicaoRiscoEpi'][$linha_epi]['nome'] = $erros;
									}
								}
							}
						}
					}
				}


				if (isset($this->GrupoExposicaoRiscoEpc->validationErrors)) {

					foreach ($this->GrupoExposicaoRiscoEpc->validationErrors as $linha => $erro) {
						if (isset($erro['GrupoExposicaoRiscoEpc'])) {
							foreach ($erro['GrupoExposicaoRiscoEpc'] as $linha_epc => $valor) {

								foreach ($valor as $campo => $erros) {

									if ($campo == "codigo_epc") {
										$this->GrupoExposicaoRisco->validationErrors[$linha]['GrupoExposicaoRiscoEpc'][$linha_epc]['nome'] = $erros;
									}
								}
							}
						}
					}
				}


				$this->BSession->setFlash('save_error');
			}
		} //fim post 

		$joins  = array(
			array(
				'table' => $this->ClienteSetor->databaseTable . '.' . $this->ClienteSetor->tableSchema . '.' . $this->ClienteSetor->useTable,
				'alias' => 'ClienteSetor',
				'type' => 'LEFT',
				'conditions' => 'ClienteSetor.codigo = GrupoExposicao.codigo_cliente_setor',
			),
			array(
				'table' => $this->Funcionario->databaseTable . '.' . $this->Funcionario->tableSchema . '.' . $this->Funcionario->useTable,
				'alias' => 'Funcionario',
				'type' => 'LEFT OUTER',
				'conditions' => 'Funcionario.codigo = GrupoExposicao.codigo_funcionario',
			),
		);

		$conditions = array(
			'ClienteSetor.codigo_cliente_alocacao' => $codigo_cliente,
			'GrupoExposicao.codigo' => $codigo
		);

		$fields = array(
			'GrupoExposicao.codigo', 'GrupoExposicao.codigo_cargo', 'GrupoExposicao.codigo_cliente_setor', 'GrupoExposicao.data_documento', 'GrupoExposicao.descricao_atividade', 'GrupoExposicao.observacao', 'GrupoExposicao.codigo_grupo_homogeneo', 'GrupoExposicao.codigo_funcionario', 'GrupoExposicao.funcionario_entrevistado', 'GrupoExposicao.data_inicio_vigencia', 'GrupoExposicao.codigo_medico',
			'ClienteSetor.codigo', 'ClienteSetor.codigo_setor', 'ClienteSetor.codigo_cliente', 'ClienteSetor.codigo_cliente_alocacao',
			'ClienteSetor.pe_direito', 'ClienteSetor.cobertura', 'ClienteSetor.iluminacao', 'ClienteSetor.ventilacao', 'ClienteSetor.piso', 'ClienteSetor.estrutura', 'Funcionario.codigo', 'Funcionario.nome', 'GrupoExposicao.funcionario_entrevistado_terceiro'
		);

		$this->retorna_dados_cliente($codigo_cliente);
		$options = array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields);
		$dados = $this->GrupoExposicao->find('first', $options);
		if (isset($dados['GrupoExposicao']['funcionario_entrevistado_terceiro']) && !empty($dados['GrupoExposicao']['funcionario_entrevistado_terceiro'])) {
			$dados['GrupoExposicao']['funcionario_entrevistado'] = 0;
		}
		$this->data = array_merge($this->data, $dados);

		if (!isset($this->data['GrupoExposicaoRisco'])) {

			$joins_risco  = array(
				array(
					'table' => $this->Risco->databaseTable . '.' . $this->Risco->tableSchema . '.' . $this->Risco->useTable,
					'alias' => 'Risco',
					'type' => 'LEFT',
					'conditions' => 'Risco.codigo = GrupoExposicaoRisco.codigo_risco',
				),
				array(
					'table' => $this->GrupoRisco->databaseTable . '.' . $this->GrupoRisco->tableSchema . '.' . $this->GrupoRisco->useTable,
					'alias' => 'GrupoRisco',
					'type' => 'LEFT',
					'conditions' => 'GrupoRisco.codigo = Risco.codigo_grupo',
				),
				array(
					'table' => $this->ExposicaoOcupAtributo->databaseTable . '.' . $this->ExposicaoOcupAtributo->tableSchema . '.' . $this->ExposicaoOcupAtributo->useTable,
					'alias' => 'Resultante',
					'type' => 'LEFT',
					'conditions' => 'Resultante.codigo = GrupoExposicaoRisco.resultante',
				),
				array(
					'table' => $this->ExposicaoOcupAtributo->databaseTable . '.' . $this->ExposicaoOcupAtributo->tableSchema . '.' . $this->ExposicaoOcupAtributo->useTable,
					'alias' => 'GrauRisco',
					'type' => 'LEFT',
					'conditions' => 'GrauRisco.codigo = GrupoExposicaoRisco.grau_risco',
				),
			);

			$fields_risco = array(
				'GrupoExposicaoRisco.codigo',
				'GrupoExposicaoRisco.codigo_grupo_exposicao',
				'GrupoExposicaoRisco.codigo_risco',
				'GrupoExposicaoRisco.tempo_exposicao',
				'GrupoExposicaoRisco.intensidade',
				'GrupoExposicaoRisco.resultante',
				'GrupoExposicaoRisco.dano',
				'GrupoExposicaoRisco.grau_risco',
				'Risco.codigo',
				'Risco.nome_agente',
				'Risco.codigo_meio_propagacao',
				'Risco.classificacao_efeito',
				'Risco.codigo_grupo',
				'Risco.risco_caracterizado_por_ruido',
				'Risco.risco_caracterizado_por_calor',
				'GrupoRisco.codigo',
				'GrupoRisco.descricao',
				'Resultante.codigo',
				'Resultante.descricao',
				'GrauRisco.codigo',
				'GrauRisco.descricao',
				'GrupoExposicaoRisco.codigo_tipo_medicao',
				'GrupoExposicaoRisco.codigo_tecnica_medicao',
				'GrupoExposicaoRisco.valor_maximo',
				'GrupoExposicaoRisco.valor_medido',
				'GrupoExposicaoRisco.minutos_tempo_exposicao',
				'GrupoExposicaoRisco.jornada_tempo_exposicao',
				'GrupoExposicaoRisco.descanso_tempo_exposicao',
				'GrupoExposicaoRisco.meio_propagacao',
				'GrupoExposicaoRisco.codigo_efeito_critico',
				'GrupoExposicaoRisco.codigo_risco_atributo',
				'GrupoExposicaoRisco.dosimetria',
				'GrupoExposicaoRisco.avaliacao_instantanea',
				'GrupoExposicaoRisco.descanso_tbn',
				'GrupoExposicaoRisco.descanso_tbs',
				'GrupoExposicaoRisco.descanso_tg',
				'GrupoExposicaoRisco.descanso_no_local',
				'GrupoExposicaoRisco.trabalho_tbn',
				'GrupoExposicaoRisco.trabalho_tbs',
				'GrupoExposicaoRisco.trabalho_tg',
				'GrupoExposicaoRisco.carga_solar',
				'GrupoExposicaoRisco.medidas_controle',
				'GrupoExposicaoRisco.medidas_controle_recomendada',
				'GrupoExposicaoRisco.codigo_tec_med_ppra',
			);

			$conditions_risco = array('codigo_grupo_exposicao' => $dados['GrupoExposicao']['codigo']);

			$dados_risco = $this->GrupoExposicaoRisco->find('all', array('conditions' => $conditions_risco, 'joins' => $joins_risco, 'fields' => $fields_risco));

			//dados_risco-> dados passados do grid montado, key-> linha que esta lendo, $dados-> valores passados
			foreach ($dados_risco as $key => $dados) {

				$this->data['GrupoExposicaoRisco'][$key] = $dados_risco[$key]['GrupoExposicaoRisco'];
				$this->data['GrupoExposicaoRisco'][$key]['codigo_grupo_risco'] = $dados_risco[$key]['Risco']['codigo_grupo'];

				$meio_exposicao = $this->RiscoAtributo->retorna_detalhe_exposicao(RiscoAtributo::MEIO_EXPOSICAO, $dados_risco[$key]['Risco']['codigo_meio_propagacao']);
				// $efeito_critico = $this->RiscoAtributo->retorna_detalhe_exposicao(RiscoAtributo::CLASSIFICACAO_EFEITO_CRITICO, $dados_risco[$key]['Risco']['classificacao_efeito']);

				$this->data['GrupoExposicaoRisco'][$key]['meio_exposicao'] = $meio_exposicao['RiscoAtributoDetalhe']['descricao'];
				// $this->data['GrupoExposicaoRisco'][$key]['codigo_efeito_critico'] = $efeito_critico['RiscoAtributoDetalhe']['descricao'];

				$this->data['GrupoExposicaoRisco'][$key]['Resultante'] = $dados_risco[$key]['Resultante'];

				$this->data['GrupoExposicaoRisco'][$key]['Risco'] = $dados_risco[$key]['Risco'];
				$this->data['GrupoExposicaoRisco'][$key]['GrupoRisco'] = $dados_risco[$key]['GrupoRisco'];
				$this->data['GrupoExposicaoRisco'][$key]['GrauRisco'] = $dados_risco[$key]['GrauRisco'];

				//FONTES GERADORAS, busca os dados para popular na view
				$fonte_geradora = $this->GrupoExpRiscoFonteGera->retorna_fonte_geradora($dados_risco[$key]['GrupoExposicaoRisco']['codigo']);
				foreach ($fonte_geradora as $key_fonte_geradora => $value_fonte_geradora) {
					$this->data['GrupoExposicaoRisco'][$key]['FonteGeradora'][$key_fonte_geradora] = $fonte_geradora[$key_fonte_geradora]['FonteGeradora'];
					$this->data['GrupoExposicaoRisco'][$key]['GrupoExpRiscoFonteGera'][$key_fonte_geradora] = $fonte_geradora[$key_fonte_geradora]['GrupoExpRiscoFonteGera'];
					$this->data['GrupoExposicaoRisco'][$key]['GrupoExpRiscoFonteGera'][$key_fonte_geradora]['nome'] = $fonte_geradora[$key_fonte_geradora]['FonteGeradora']['nome'];
				} //fim foreach

				//EFEITOS CRITICOS, busca os dados para popular na view
				$efeito_critico = $this->GrupoExpRiscoAtribDet->retorna_grupo_exposicao_risco($dados_risco[$key]['GrupoExposicaoRisco']['codigo']);
				foreach ($efeito_critico as $key_efeito_critico => $value_efeito_critico) {
					$this->data['GrupoExposicaoRisco'][$key]['EfeitoCritico'][$key_efeito_critico] 						= $value_efeito_critico['EfeitoCritico'];
					// $this->data['GrupoExposicaoRisco'][$key]['GrupoExpEfeitoCritico'][$key_efeito_critico] 				= $efeito_critico[$key_efeito_critico][0]['GrupoExposicaoRiscoAtributoDet'];
					$this->data['GrupoExposicaoRisco'][$key]['GrupoExpEfeitoCritico'][$key_efeito_critico]['descricao']	= $value_efeito_critico['EfeitoCritico']['descricao'];
				} //fim foreach

				$epi = $this->GrupoExposicaoRiscoEpi->retorna_epi($dados_risco[$key]['GrupoExposicaoRisco']['codigo']);

				foreach ($epi as $key_epi => $value_epi) {
					$this->data['GrupoExposicaoRisco'][$key]['Epi'][$key_epi] = $epi[$key_epi]['Epi'];
					$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$key_epi] = $epi[$key_epi]['GrupoExposicaoRiscoEpi'];
					$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$key_epi]['nome'] = $epi[$key_epi]['Epi']['nome'];
					$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$key_epi]['numero_ca'] = $epi[$key_epi]['GrupoExposicaoRiscoEpi']['numero_ca'];
					$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$key_epi]['data_validade_ca'] = $epi[$key_epi]['GrupoExposicaoRiscoEpi']['data_validade_ca'];
					$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$key_epi]['epi_eficaz'] = $epi[$key_epi]['GrupoExposicaoRiscoEpi']['epi_eficaz'];
					$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$key_epi]['atenuacao'] = $epi[$key_epi]['GrupoExposicaoRiscoEpi']['atenuacao'];
				}

				$epc = $this->GrupoExposicaoRiscoEpc->retorna_epc($dados_risco[$key]['GrupoExposicaoRisco']['codigo']);
				foreach ($epc as $key_epc => $value_epc) {
					$this->data['GrupoExposicaoRisco'][$key]['Epc'][$key_epc] = $epc[$key_epc]['Epc'];
					$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpc'][$key_epc] = $epc[$key_epc]['GrupoExposicaoRiscoEpc'];
					$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpc'][$key_epc]['nome'] = $epc[$key_epc]['Epc']['nome'];
				}
			}
		} else {
			//EDITAR TELA COM ERRO
			unset($this->data['GrupoExposicaoRisco']['k']);
			foreach ($this->data['GrupoExposicaoRisco'] as $key => $dados) {

				//ERRO RISCO JA CADASTRADO.
				$grupo_risco_dados = $this->GrupoRisco->find('first', array('conditions' => array('codigo' => $this->data['GrupoExposicaoRisco'][$key]['codigo_grupo_risco']), 'fields' => array('codigo', 'descricao')));
				$this->data['GrupoExposicaoRisco'][$key]['GrupoRisco'] = $grupo_risco_dados['GrupoRisco'];

				$risco_dados = $this->Risco->find('first', array('conditions' => array('codigo' => $this->data['GrupoExposicaoRisco'][$key]['codigo_risco']), 'fields' => array('codigo', 'nome_agente', 'risco_caracterizado_por_ruido', 'risco_caracterizado_por_calor')));
				$this->data['GrupoExposicaoRisco'][$key]['Risco'] = $risco_dados['Risco'];

				//fonte geradora
				if (isset($this->data['GrupoExposicaoRisco'][$key]['GrupoExpRiscoFonteGera'])) {
					foreach ($this->data['GrupoExposicaoRisco'][$key]['GrupoExpRiscoFonteGera'] as $linha_fonte_geradora => $dados_fonte_geradora) {
						$fonte_geradora = $this->FonteGeradora->find('list', array('conditions' => array('codigo' => $dados_fonte_geradora['codigo_fontes_geradoras']), 'fields' => array('codigo', 'nome')));
						$this->data['GrupoExposicaoRisco'][$key]['GrupoExpRiscoFonteGera'][$linha_fonte_geradora]['nome'] = $fonte_geradora[$dados_fonte_geradora['codigo_fontes_geradoras']];
					}
				}

				//efeito critico
				if (isset($this->data['GrupoExposicaoRisco'][$key]['GrupoExpEfeitoCritico'])) {
					//varre os dados do input
					foreach ($this->data['GrupoExposicaoRisco'][$key]['GrupoExpEfeitoCritico'] as $linha_efeito_critico => $dados_efeito_critico) {
						$efeito_critico = $this->RiscoAtributoDetalhe->find('list', array('conditions' => array('codigo' => $dados_efeito_critico['codigo_efeito_critico']), 'fields' => array('codigo', 'descricao')));
						$this->data['GrupoExposicaoRisco'][$key]['GrupoExpEfeitoCritico'][$linha_efeito_critico]['descricao'] = $efeito_critico[$dados_efeito_critico['codigo_efeito_critico']];
					} //fim foreach efeito critico

				} //fim if efeito critico

				if (!isset($this->data['GrupoExposicaoRisco'][$key]['descanso_no_local']) && empty($this->data['GrupoExposicaoRisco'][$key]['descanso_no_local'])) {
					$this->data['GrupoExposicaoRisco'][$key]['descanso_no_local'] = null;
				}
				if (!isset($this->data['GrupoExposicaoRisco'][$key]['carga_solar']) && empty($this->data['GrupoExposicaoRisco'][$key]['carga_solar'])) {
					$this->data['GrupoExposicaoRisco'][$key]['carga_solar'] = null;
				}


				if (isset($this->data['GrupoHomDetalhe']) && !empty($this->data['GrupoHomDetalhe'])) {
					foreach ($this->data['GrupoHomDetalhe'] as $linha => $dados_ghe) {
						if (isset($dados_ghe['codigo_setor_ghe']) && !empty($dados_ghe['codigo_setor_ghe'])) {
							$setor = $this->Setor->find('first', array('conditions' => array('codigo' => $dados_ghe['codigo_setor_ghe'])));
							$this->data['GrupoHomDetalhe'][$linha]['Setor']['codigo'] = $setor['Setor']['codigo'];
							$this->data['GrupoHomDetalhe'][$linha]['Setor']['descricao'] = $setor['Setor']['descricao'];
						}

						if (isset($dados_ghe['codigo_cargo_ghe']) && !empty($dados_ghe['codigo_cargo_ghe'])) {
							$setor = $this->Cargo->find('first', array('conditions' => array('codigo' => $dados_ghe['codigo_cargo_ghe'])));
							$this->data['GrupoHomDetalhe'][$linha]['Cargo']['codigo'] = $setor['Cargo']['codigo'];
							$this->data['GrupoHomDetalhe'][$linha]['Cargo']['descricao'] = $setor['Cargo']['descricao'];
						}
					}
				}

				if (isset($this->data['GrupoExposicaoRisco'][$key]['resultante']) && !empty($this->data['GrupoExposicaoRisco'][$key]['resultante'])) {
					$resultante = $this->ExposicaoOcupAtributo->find('first', array('conditions' => array('codigo' => $this->data['GrupoExposicaoRisco'][$key]['resultante'], 'codigo_exposicao_ocupacional' => ExposicaoOcupacional::RESULTANTE), 'fields' => array('codigo', 'descricao')));
					$this->data['GrupoExposicaoRisco'][$key]['resultante'] = array('Resultante' => $resultante['ExposicaoOcupAtributo']);
				}

				if (isset($this->data['GrupoExposicaoRisco'][$key]['grau_risco']) && !empty($this->data['GrupoExposicaoRisco'][$key]['grau_risco'])) {
					$grau_risco = $this->ExposicaoOcupAtributo->find('first', array('conditions' => array('codigo' => $this->data['GrupoExposicaoRisco'][$key]['grau_risco'], 'codigo_exposicao_ocupacional' => ExposicaoOcupacional::GRAU_RISCO), 'fields' => array('codigo', 'descricao')));
					$this->data['GrupoExposicaoRisco'][$key]['grau_risco'] = array('GrauRisco' => $grau_risco['ExposicaoOcupAtributo']);
				}

				if (isset($this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'])) {
					foreach ($this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'] as $linha_epi => $dados_epi) {

						if (!isset($dados_epi['epi_eficaz'])) $dados_epi['epi_eficaz'] = null;
						$epi = $this->Epi->find('all', array('conditions' => array('codigo' => $dados_epi['codigo_epi']), 'fields' => array('codigo', 'nome', 'numero_ca', 'data_validade_ca', 'atenuacao_qtd')));

						foreach ($epi as $chave => $valor_epi) {
							$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$linha_epi]['codigo'] = $valor_epi['Epi']['codigo'];
							$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$linha_epi]['nome'] = $valor_epi['Epi']['nome'];
							$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$linha_epi]['numero_ca'] = $valor_epi['Epi']['numero_ca'];
							$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$linha_epi]['data_validade_ca'] = $valor_epi['Epi']['data_validade_ca'];
							$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$linha_epi]['epi_eficaz'] = $dados_epi['epi_eficaz'];
							$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$linha_epi]['atenuacao'] = $dados_epi['atenuacao'];
						}
					}
				}

				if (isset($this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpc'])) {
					foreach ($this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpc'] as $linha_epc => $dados_epc) {

						$epc = $this->Epc->find('all', array('conditions' => array('codigo' => $dados_epc['codigo_epc']), 'fields' => array('codigo', 'nome')));

						foreach ($epc as $chave => $valor_epc) {
							$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpc'][$linha_epc]['codigo'] = $valor_epc['Epc']['codigo'];
							$this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpc'][$linha_epc]['nome'] = $valor_epc['Epc']['nome'];
						}
					}
				}
			}
		}

		$funcionario = $this->Funcionario->lista_por_cliente_setor_cargo('list', $this->data['ClienteSetor']['codigo_cliente_alocacao'], $this->data['ClienteSetor']['codigo_setor'], $this->data['GrupoExposicao']['codigo_cargo']);
		$lista_cargo = $this->Cargo->lista_por_cliente($codigo_cliente);
		$lista_setor = $this->Setor->lista_por_cliente($codigo_cliente);
		$ghe = $this->GrupoHomogeneo->lista_por_cliente($this->data['Unidade']['codigo']);
		$grupo_homogeneo = $this->GrupoExposicao->retornaDescricaoGrupoHomogeneo($this->data['GrupoExposicao']['codigo_grupo_homogeneo']);

		foreach ($grupo_homogeneo as $linha => $dados_ghe) {
			$grupo_homogeneo[$linha]['GrupoHomDetalhe']['descricao_atividade_ghe'] = $dados_ghe['GrupoExposicao']['descricao_atividade'];
		}


		$profissionais = $this->Medico->lista_somente_engenhgeiros_por_cliente($codigo_cliente);
		$pe_direito = $this->SetorCaracteristica->retorna_caracteristica(SetorCaracteristica::PE_DIREITO);
		$iluminacao = $this->SetorCaracteristica->retorna_caracteristica(SetorCaracteristica::ILUMINACAO);
		$ventilacao = $this->SetorCaracteristica->retorna_caracteristica(SetorCaracteristica::VENTILACAO);
		$cobertura = $this->SetorCaracteristica->retorna_caracteristica(SetorCaracteristica::COBERTURA);
		$estrutura = $this->SetorCaracteristica->retorna_caracteristica(SetorCaracteristica::ESTRUTURA);
		$piso = $this->SetorCaracteristica->retorna_caracteristica(SetorCaracteristica::PISO);
		$array_efeito = $this->RiscoAtributo->retorna_exposicao(RiscoAtributo::CLASSIFICACAO_EFEITO_CRITICO);
		$meios_exposicao = $this->RiscoAtributo->retorna_exposicao(RiscoAtributo::MEIO_EXPOSICAO);

		$tempo_exposicao = $this->ExposicaoOcupacional->retorna_exposicao(ExposicaoOcupacional::TEMPO_EXPOSICAO);
		$intensidade = $this->ExposicaoOcupacional->retorna_exposicao(ExposicaoOcupacional::INTENSIDADE);
		$dano = $this->ExposicaoOcupacional->retorna_exposicao(ExposicaoOcupacional::DANO);
		$unidades_medida = $this->TecnicaMedicao->retorna_tecnicas();
		// PC-3177 - Força pegar dados da matriz
		$matriz = $this->GrupoEconomicoCliente->getMatriz($codigo_cliente);
		$forca_matriz = $matriz['GrupoEconomico']['codigo_cliente'];

		$tecnicas_medicao = $this->TecnicaMedicaoPpra->get_tecnicas_medicao($forca_matriz);		
		// $tecnicas_medicao = $this->TecnicaMedicaoPpra->get_last_tecnicas_medicao_save_matriz($forca_matriz);

		// PC-3177 - Busca último registro salvo com cod. da matriz, tabela [tecnicas_medicao_ppra]		
		$tecnicas_medicao_bd = $this->GrupoExposicaoRisco->getLastSaveTecMedGrupoExposicaoRisco($this->data['GrupoExposicao']['codigo']);
		
		if(isset($tecnicas_medicao_bd)){
			$last_tecnicas_medicao = $tecnicas_medicao_bd['GrupoExposicaoRisco']['codigo_tec_med_ppra'];
		}

		$joins  = array(
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
			)
		);

		$conditions = array(
			'ClienteFuncionario.codigo_cliente' => $codigo_cliente
		);

		$funcionarios = $this->Funcionario->find('list', array('joins' => $joins, 'conditions' => $conditions, 'fields' => array('Funcionario.codigo', 'Funcionario.nome')));
		$funcionarios[0] = "Outro";


		$joins  = array(
			array(
				'table' => 'atribuicoes_grupos_expo',
				'alias' => 'AtribuicaoGrupoExpo',
				'type' => 'INNER',
				'conditions' => 'GrupoExposicao.codigo = AtribuicaoGrupoExpo.codigo_grupo_exposicao'
			),
			array(
				'table' => 'atribuicao',
				'alias' => 'Atribuicao',
				'type' => 'INNER',
				'conditions' => 'Atribuicao.codigo = AtribuicaoGrupoExpo.codigo_atribuicao',
			)
		);

		//Recupera as atribuicoes vinculadas ao grupo
		$atribuicoes_grupo = $this->GrupoExposicao->find('list', array(
			'joins' => $joins,
			'fields' => array('Atribuicao.codigo', 'Atribuicao.descricao'), 'conditions' =>
			array('GrupoExposicao.codigo' => $codigo)
		));


		//Recupera as atribuições vinculadas ao cliente (matriz)
		$atribuicoes = $this->Atribuicao->find('list', array('conditions' => array('codigo_cliente' => $this->data['Matriz']['codigo'], 'ativo' => '1'), 'fields' => array('codigo', 'descricao'), 'order' => 'descricao'));

		//Se o médico não foi encontrado é porque está inativo
		if (!empty($this->data['GrupoExposicao']['codigo_medico']) && !isset($profissionais[$this->data['GrupoExposicao']['codigo_medico']])) {

			$medico_grupo = $this->Medico->find('first', array(
				'fields' => array('codigo', 'nome'),
				'conditions' => array('Medico.codigo' => $this->data['GrupoExposicao']['codigo_medico'])
			));

			if (!empty($medico_grupo)) {
				$profissionais[$medico_grupo['Medico']['codigo']] = $medico_grupo['Medico']['nome'];
			}
		}

		$dados_grupo_exposicao['GrupoExposicao']['funcionario_entrevistado'] = $this->data['GrupoExposicao']['funcionario_entrevistado'];
		$dados_grupo_exposicao['GrupoExposicao']['funcionario_entrevistado_terceiro'] = $this->data['GrupoExposicao']['funcionario_entrevistado_terceiro'];
		$dados_grupo_exposicao['GrupoExposicao']['codigo_medico'] = $this->data['GrupoExposicao']['codigo_medico'];

		$option_subs_carregado = array();
		$conjuntoRiscos = array();
		if (isset($this->data['GrupoExposicaoRisco'])) {
			for ($i = 0; $i <= $key; $i++) {
				// $conditions = array(
				// 	'codigo_grupo' => $this->data['GrupoExposicaoRisco'][$i]['GrupoRisco']['codigo'],
				// );
				// $conditions['OR'] = array('ativo' => 1, 'codigo' => $this->data['GrupoExposicaoRisco'][$i]['Risco']['codigo']);
				// $option_subs_carregado[$i] = $this->Risco->find('list', array('conditions' => $conditions,'fields' => array('codigo','nome_agente')));
				$grupo_risco[$i] = $this->GrupoRisco->retorna_grupo($this->data['GrupoExposicaoRisco'][$i]['GrupoRisco']['codigo']);
				$conjuntoRiscos[] = $this->data['GrupoExposicaoRisco'][$i]['Risco']['codigo'];
			}
			$get_riscos_grupo = $this->GrupoRisco->get_riscos_grupo($conjuntoRiscos);

			$option_subs_carregado = $get_riscos_grupo;
		}


		$grupo_risco_inclusao = $this->GrupoRisco->retorna_grupo();

		$this->set(compact(
			'codigo_cliente',
			'codigo',
			'cargo',
			'setor',
			'ghe',
			'grupo_risco',
			'pe_direito',
			'iluminacao',
			'ventilacao',
			'cobertura',
			'estrutura',
			'piso',
			'tempo_exposicao',
			'intensidade',
			'dano',
			'unidades_medida',
			'funcionario',
			'grupo_homogeneo',
			'array_efeito',
			'meios_exposicao',
			'funcionarios',
			'profissionais',
			'atribuicoes',
			'atribuicoes_grupo',
			'dados_grupo_exposicao',
			'option_subs_carregado',
			'grupo_risco_inclusao',
			'lista_setor',
			'lista_cargo',
			'visualizar_gge',
			'tecnicas_medicao',
			'last_tecnicas_medicao'
		));
	} //fim metodo editar

	function buscar_grupo_exposicao()
	{
		$this->layout = 'ajax_placeholder';

		$input_id = !empty($this->passedArgs['input_id']) ? $this->passedArgs['input_id'] : '';
		$codigo_cliente = !empty($this->passedArgs['codigo_cliente']) ? $this->passedArgs['codigo_cliente'] : '';

		$this->data['GrupoExposicao'] = $this->Filtros->controla_sessao($this->data, $this->GrupoExposicao->name);

		$this->retorna_dados_cliente($codigo_cliente);
		$cargo = $this->Cargo->lista_por_cliente($this->data['GrupoEconomicoCliente']['matriz']);
		$setor = $this->Setor->lista_por_cliente($this->data['GrupoEconomicoCliente']['matriz']);
		$risco = $this->Risco->lista_por_cliente($codigo_cliente);

		$this->set(compact('input_id', 'cargo', 'setor', 'risco', 'codigo_cliente'));
	}
	function buscar_listagem($codigo_cliente)
	{
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->GrupoExposicao->name);
		$conditions = $this->GrupoExposicao->converteFiltroEmCondition($filtros);

		$conditions = array_merge($conditions, array('GrupoExposicao.unidade' => $codigo_cliente));
		$joins  = array(
			array(
				'table' => $this->Cargo->databaseTable . '.' . $this->Cargo->tableSchema . '.' . $this->Cargo->useTable,
				'alias' => 'Cargo',
				'type' => 'INNER',
				'conditions' => 'GrupoExposicao.codigo_cargo = Cargo.codigo',
			),
			array(
				'table' => $this->Setor->databaseTable . '.' . $this->Setor->tableSchema . '.' . $this->Setor->useTable,
				'alias' => 'Setor',
				'type' => 'INNER',
				'conditions' => 'GrupoExposicao.codigo_setor = Setor.codigo',
			),
			array(
				'table' => $this->Risco->databaseTable . '.' . $this->Risco->tableSchema . '.' . $this->Risco->useTable,
				'alias' => 'Risco',
				'type' => 'LEFT OUTER',
				'conditions' => 'GrupoExposicao.codigo_risco = Risco.codigo',
			)
		);

		$fields = array(
			'GrupoExposicao.codigo',
			'Cargo.codigo',
			'Cargo.descricao',
			'Setor.codigo',
			'Setor.descricao',
			'Risco.codigo',
			'Risco.nome_agente'
		);

		$order = array('GrupoExposicao.codigo DESC');

		$this->paginate['GrupoExposicao'] = array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields,
			'order' => $order,
			'limit' => 10
		);


		$grupos_exposicao = $this->paginate('GrupoExposicao');
		$this->set(compact('grupos_exposicao', 'codigo_cliente'));
	}

	public function imprimir_relatorio($codigo_cliente, $imp_setor_cargo_vazio)
	{

		$this->__jasperConsulta($codigo_cliente, $imp_setor_cargo_vazio);
	}

	function carrega_resultante()
	{
		$this->layout = 'ajax';
		$this->render(false, false);

		$tempo = $_POST['tempo'];
		$intensidade = $_POST['intensidade'];

		switch ($intensidade) {
			case 4: //INTENSIDADE BAIXA
				if ($tempo == 1) { //TEMPO PERMANENTE
					$resultante = 9; //RESULTANTE DE ATENÇÃO
				} elseif ($tempo == 2 || $tempo = 3) { //TEMPO INTERMITENTE OU //TEMPO OCASIONAL
					$resultante = 8; //RESULTANTE IRRELEVANTE
				}
				break;
			case 5: //INTENSIDADE MEDIA
				if ($tempo == 1 || $tempo == 2) { //TEMPO PERMANENTE OU TEMPO INTERMITENTE
					$resultante = 9; //RESULTANTE DE ATENÇÃO
				} elseif ($tempo = 3) {  //TEMPO OCASIONAL
					$resultante = 8; //RESULTANTE IRRELEVANTE
				}
				break;
			case 6: //INTENSIDADE ALTA
				if ($tempo == 1 || $tempo == 2) { //TEMPO PERMANENTE OU TEMPO INTERMITENTE
					$resultante = 19; //RESULTANTE INCERTA
				} elseif ($tempo = 3) {
					$resultante = 9; //RESULTANTE DE ATENÇÃO
				}
				break;
			case 7: //INTENSIDADE MUITO ALTA
				$resultante = 10; //RESULTANTE CRÍTICA
				break;
		}

		$conditions = array('ExposicaoOcupAtributo.codigo' => $resultante, 'ExposicaoOcupAtributo.codigo_exposicao_ocupacional' => ExposicaoOcupacional::RESULTANTE);
		$joins  = array(
			array(
				'table' => $this->ExposicaoOcupAtributo->databaseTable . '.' . $this->ExposicaoOcupAtributo->tableSchema . '.' . $this->ExposicaoOcupAtributo->useTable,
				'alias' => 'ExposicaoOcupAtributo',
				'type' => 'LEFT',
				'conditions' => 'ExposicaoOcupAtributo.codigo_exposicao_ocupacional = ExposicaoOcupacional.codigo',
			)
		);

		$fields = array('codigo', 'descricao');

		$resultante_dados = $this->ExposicaoOcupAtributo->find('first', array('conditions' => $conditions, 'fields' => $fields));

		echo json_encode($resultante_dados);
	}

	function carrega_dano()
	{
		$this->layout = 'ajax';
		$this->render(false, false);

		$dano = $_POST['dano'];
		$resultante = $_POST['resultante'];

		switch ($resultante) {
			case 8:
				if ($dano == 15) {
					$grau_risco = 22;
				} elseif ($dano == 13 || $dano = 14) {
					$grau_risco = 21;
				} elseif ($dano == 11 || $dano = 12) {
					$grau_risco = 20;
				}
				break;
			case 9:
				if ($dano == 14 || $dano == 15) {
					$grau_risco = 22;
				} elseif ($dano == 12 || $dano == 13) {
					$grau_risco = 21;
				} elseif ($dano == 11) {
					$grau_risco = 20;
				}
				break;
			case 10:
				if ($dano == 14 || $dano == 15) {
					$grau_risco = 23;
				} elseif ($dano == 12 || $dano == 13) {
					$grau_risco = 22;
				} elseif ($dano = 11) {
					$grau_risco = 21;
				}
				break;
			case 19:
				if ($dano == 15) {
					$grau_risco = 23;
				} elseif ($dano == 13 || $dano == 14) {
					$grau_risco = 22;
				} elseif ($dano == 11 || $dano == 12) {
					$grau_risco = 21;
				}
				break;
		}



		$conditions = array('ExposicaoOcupAtributo.codigo' => $grau_risco, 'ExposicaoOcupAtributo.codigo_exposicao_ocupacional' => ExposicaoOcupacional::GRAU_RISCO);
		$joins  = array(
			array(
				'table' => $this->ExposicaoOcupAtributo->databaseTable . '.' . $this->ExposicaoOcupAtributo->tableSchema . '.' . $this->ExposicaoOcupAtributo->useTable,
				'alias' => 'ExposicaoOcupAtributo',
				'type' => 'LEFT',
				'conditions' => 'ExposicaoOcupAtributo.codigo_exposicao_ocupacional = ExposicaoOcupacional.codigo',
			)
		);

		$fields = array('codigo', 'descricao');

		$resultante_dados = $this->ExposicaoOcupAtributo->find('first', array('conditions' => $conditions, 'fields' => $fields));
		echo json_encode($resultante_dados);
	}

	function excluir($codigo)
	{
		$this->layout = 'ajax';
		$this->render(false, false);

		$buscar_grupo_exposicao = $this->GrupoExposicao->find("first", array('conditions' => array('codigo' => $codigo)));

		if (!empty($buscar_grupo_exposicao)) {

			if (!empty($buscar_grupo_exposicao['GrupoExposicao']['codigo_grupo_homogeneo'])) {
				//GHE
				$buscar_grupo_homogeneo = $this->GrupoExposicao->find("all", array('conditions' => array('codigo_grupo_homogeneo' => $buscar_grupo_exposicao['GrupoExposicao']['codigo_grupo_homogeneo'])));

				$deleted_data = 0;
				if (!empty($buscar_grupo_homogeneo)) {
					foreach ($buscar_grupo_homogeneo as $key => $dados) {
						if ($this->GrupoExposicao->excluir($dados['GrupoExposicao']['codigo'])) {
							$deleted_data++;
						}
					}

					if ($deleted_data != count($buscar_grupo_homogeneo)) {
						echo 0;
					} else {
						echo 1;
					}
				} else {
					echo 0;
				}
			} else { //INDIVIDUAL
				if ($this->GrupoExposicao->excluir($codigo)) {
					echo 1;
				} else {
					echo 0;
				}
			}
		} else {
			echo 0;
		}

		exit;
	}

	public function preenche_com_ausencia_risco($codigo_cliente, $back_auto = false, $hierarquia = false)
	{
		$usuario = $this->BAuth->user();
		$return = $this->GrupoExposicao->preenche_com_ausencia_risco($codigo_cliente, $usuario, $hierarquia);
		if (!$return['erro']) {
			$this->BSession->setFlash('save_success');
		} else {
			$this->BSession->setFlash(array('alert alert-error', $return['mensagem']));
		}

		if ($back_auto) {
			$this->redirect(Comum::UrlOrigem()->data);
		} else {
			return $this->redirect(array('action' => 'index', $codigo_cliente));
		}
	}

	/**
	 * [__jasperConsulta description]
	 * 
	 * metodo privado para executar o relatorio de ppra no jasper
	 * 
	 * @param  [type] $codigo_cliente        [description] codigo do cliente que irá imprimir o relatorio
	 * @param  [type] $imp_setor_cargo_vazio [description] flag se irá imprimir os setores e cargos que não tenha funcionario 0-> irá imprimir os setores e cargos, 1-> não irá imprimir os setores e cargos sem funcionarios 
	 * @return [type]                        [description]
	 */
	private function __jasperConsulta($codigo_cliente, $imp_setor_cargo_vazio)
	{

		// opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME' => '/reports/RHHealth/relatorio_ppra', // especificar qual relatório
			'FILE_NAME' => basename('relatorio_pgr.pdf') // nome do relatório para saida
		);

		// parametros do relatorio
		$parametros = array(
			'CODIGO_CLIENTE' => $codigo_cliente,
			'IMP_SETOR_CARGO_VAZIO' => $imp_setor_cargo_vazio
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
			$url = $this->Jasper->generate($parametros, $opcoes);

			if ($url) {
				// se obter retorno apresenta usando cabeçalho apropriado
				header(sprintf('Content-Disposition: attachment; filename="%s"', $opcoes['FILE_NAME']));
				header('Pragma: no-cache');
				header('Content-type: application/pdf');
				echo $url;
				exit;
			}
		} catch (Exception $e) {
			// se ocorreu erro
			debug($e);
			exit;
		}

		exit;
	} //fim __jasperConsulta

	public function modal_pcmso_pendente($codigo_unidade, $codigo_setor, $codigo_cargo, $codigo_funcionario)
	{

		$dados_modal = $this->GrupoExposicao->dados_modal_pcmso_pendente($codigo_unidade, $codigo_setor, $codigo_cargo, $codigo_funcionario);

		$this->set(compact('dados_modal'));
	}
}//FINAL CLASS GruposExposicaoController
