<?php

/**
 * Controller responsável pela rota da API /api/pcmso/sincronizar
 * @author Rodrigo Ruotolo Barbosa <roderickruotolo@gmail.com>
 */
class ApiPcmsoController extends AppController
{

	/**
	 * @var string $name
	 */
	public $name = '';

	/**
	 * @var ApiAutorizacao $ApiAutorizacao
	 */
	private $ApiAutorizacao;

	/**
	 * @var ApiDataFormat $ApiDataFormat
	 */
	private $ApiDataFormat;

	/**
	 * @var ApiFields $ApiFields
	 */
	private $fields;

	/**
	 * @var array $dados
	 */
	private $dados = array();

	var $helpers = array('XML');

	var $uses = array();
	var $components = array('RequestHandler');

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->BAuth->allow(array('*'));

		App::import('Component', 'ApiAutorizacao');
		$this->ApiAutorizacao = new ApiAutorizacaoComponent();

		App::import('Component', 'ApiDataFormat');
		$this->ApiDataFormat = new ApiDataFormatComponent();

		App::import('Component', 'ApiFields');
		$this->fields = new ApiFieldsComponent();

		$this->ApiDataFormat->setData(file_get_contents('php://input'));
	}

	/**
	 * 
	 * Metodo para sincronizar Exames
	 * Codigos de status:
	 * 0 => sucesso
	 * 1 => erro: não foi passado o cnpj e/ou token
	 * 2 => erro: token e/ou cnpj vazio
	 * 3 => erro: tolen e/ou cnpj inválido
	 * 4 => campos obrigatorios
	 * 5 => erros ou cpf ja existente na base de dados
	 * @return string JSON contendo status e mensagem. 
	 * 
	 */
	public function sincronizar()
	{

		$this->render = array(false, false);
		$this->autoRender = false;
		$dadosRecebidos = '';
		$this->ApiDataFormat->setContentType();
		// Pega os campos via json ou Form url-encoded
		$dadosRecebidos = $this->ApiDataFormat->getDataRequest();

		//verifica se existe os gets obrigatorios
		if (isset($dadosRecebidos->token) && isset($dadosRecebidos->cnpj)) {

			//valida o usuario + cnpj
			$cnpj   = $dadosRecebidos->cnpj;
			$token  = $dadosRecebidos->token;

			// Verifica se esta validado a autorizacao
			if ($this->ApiAutorizacao->validaAutorizacao($token, $cnpj)) {

				// Instancia a Model AplicacaoExame
				$this->loadModel('AplicacaoExame');

				// Valida os campos obrigatorios
				if ($this->validaCamposObrigatorios($dadosRecebidos)) {

					/** 
					 * @var array @inputData Este array associativo tem como função armazenar 
					 * de forma organizada os dados recebidos exatamente no formato que
					 * a Model AplicacaoExame espera. 
					 */
					$inputData = array();
					$operacao = strtoupper($dadosRecebidos->operacao);
					$operacao = isset($operacao) && trim($operacao) !== '' ? ($operacao == "I" || $operacao == "A" ? "A" : $operacao) : "A";

					// Carrega a Model Cliente
					$this->loadModel('Cliente');
					$this->loadModel('ClienteExterno');
					$this->loadModel('GrupoEconomico');
					$this->loadModel('GrupoEconomicoCliente');
					$this->loadModel('Setor');
					$this->loadModel('Cargo');

					try {
						//verifica a matriz através do cliente do token
						$matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($this->ApiAutorizacao->cod_cliente);
						$join = array(
							array(
								'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
								'alias' => 'Cliente',
								'conditions' => 'Cliente.codigo = GrupoEconomico.codigo_cliente',
								'type' => 'INNER',
							)
						);
						$conditions = array();
						$conditions['Cliente.codigo'] = $matriz;
						$grupo_economico = $this->GrupoEconomico->find('first', array('fields' => array('GrupoEconomico.codigo as codigo'), 'conditions' => $conditions, 'joins' => $join));
						//Grupo economico da matriz
						$grupo_economico = $grupo_economico[0]['codigo'];

						// Obtém Códigos Externos de Unidade, Setor e Cargo
						// Verifica codigo_unidade
						if (isset($dadosRecebidos->codigo_unidade_alocacao) && !empty($dadosRecebidos->codigo_unidade_alocacao)) {

							$conditions_unidade = array();
							$conditions_unidade['GrupoEconomicoCliente.codigo_cliente'] = $dadosRecebidos->codigo_unidade_alocacao;

							$gr_economico_cliente_alocacao = $this->GrupoEconomicoCliente->find('first', array('fields' => array('GrupoEconomicoCliente.codigo_grupo_economico'), 'conditions' => $conditions_unidade, 'recursive' => -1));

							if (!empty($gr_economico_cliente_alocacao)) {
								$codigo_grp_economico_aloc = $gr_economico_cliente_alocacao['GrupoEconomicoCliente']['codigo_grupo_economico'];
							} else {
								$codigo_grp_economico_aloc = null;
							}


							if ($codigo_grp_economico_aloc !== $grupo_economico) {
								throw new Exception("codigo_unidade_alocacao não compativel com o grupo economico.");
							}

							$inputData['AplicacaoExame']['codigo_cliente_alocacao'] = $dadosRecebidos->codigo_unidade_alocacao;


							//Verifica codigo_externo_unidade
						} else {
							// Obtém o código_cliente_alocacao real em nosso sitema interno e seta no array que será fornecido para a model
							$grupo_economico_cliente_alocacao = $this->ClienteExterno->buscarCodigoClientePorCodigoExternoECodigoMatriz($dadosRecebidos->codigo_externo_unidade_alocacao, $matriz);

							if (empty($grupo_economico_cliente_alocacao)) {
								throw new Exception("codigo_externo_unidade_alocacao não compativel com o grupo economico.");
							}
							$inputData['AplicacaoExame']['codigo_cliente_alocacao'] = $grupo_economico_cliente_alocacao[0][0]['codigo_cliente'];
						}
						// Setor 
						if (isset($dadosRecebidos->codigo_setor) && !empty($dadosRecebidos->codigo_setor)) {

							$setor = $this->Setor->find(
								'first',
								array(
									'conditions' => array(
										'Setor.codigo' => $dadosRecebidos->codigo_setor,
										'Setor.codigo_cliente' => $matriz
									),
									'fields' => 'Setor.codigo'
								)
							);
							//caso nao exista o codigo do setor retorna o erro
							if (!empty($setor)) {
								$inputData['AplicacaoExame']['codigo_setor'] = $dadosRecebidos->codigo_setor;
							} else {
								throw new Exception("codigo_setor não encontrado");
							}
						} else {
							$this->loadModel('SetorExterno');
							$result = $this->SetorExterno->find('first', array('conditions' => array('SetorExterno.codigo_externo' => $dadosRecebidos->codigo_externo_setor, 'SetorExterno.codigo_cliente' => $matriz), 'fields' => 'SetorExterno.codigo_setor'));

							//verifica se existe o relacionamento do codigo externo com o codigo setor para gravar na aplicacao de exames
							if (!empty($result['SetorExterno']['codigo_setor'])) {
								$inputData['AplicacaoExame']['codigo_setor'] = $result['SetorExterno']['codigo_setor'];
							} else {
								//cadastra o cargo
								$inputData['AplicacaoExame']['codigo_setor'] = $this->fields->verifica_inclui_setor($dadosRecebidos->codigo_externo_setor, $matriz);
							}
						}
						// Cargo
						if (isset($dadosRecebidos->codigo_cargo) && !empty($dadosRecebidos->codigo_cargo)) {
							$cargo = $this->Cargo->find(
								'first',
								array(
									'conditions' => array(
										'Cargo.codigo' => $dadosRecebidos->codigo_cargo
									),
									'fields' => 'Cargo.codigo'
								)
							);

							if (!empty($cargo)) {
								$inputData['AplicacaoExame']['codigo_cargo'] = $dadosRecebidos->codigo_cargo;
							} else {
								throw new Exception("codigo_cargo não encontrado.");
							}
						} else {
							$this->loadModel('CargoExterno');
							$result = $this->CargoExterno->find('first', array('conditions' => array('CargoExterno.codigo_externo' => $dadosRecebidos->codigo_externo_cargo, 'CargoExterno.codigo_cliente' => $matriz), 'fields' => 'CargoExterno.codigo_cargo'));

							//verifica se existe o relacionamento do codigo externo com o codigo cargo para gravar na aplicacao de exames
							if (!empty($result['CargoExterno']['codigo_cargo'])) {
								$inputData['AplicacaoExame']['codigo_cargo'] = $result['CargoExterno']['codigo_cargo'];
							} else {
								//cadastra o cargo
								$inputData['AplicacaoExame']['codigo_cargo'] = $this->fields->verifica_inclui_cargo($dadosRecebidos->codigo_externo_cargo, $matriz);
							}
						}

						$inputData['AplicacaoExame']['codigo_funcionario'] = null;
						//Se o cpf do funcionário foi enviado
						if (isset($dadosRecebidos->cpf_funcionario) && trim($dadosRecebidos->cpf_funcionario) !== '') {
							$this->loadModel('ClienteFuncionario');
							$this->loadModel('FuncionarioSetorCargo');
							$this->loadModel('Funcionario');

							//verifica se funcionário existe
							$funcionario = $this->Funcionario->findByCpf($dadosRecebidos->cpf_funcionario, 'Funcionario.codigo');

							if (empty($funcionario)) {
								throw new Exception("Erro: funcionario_cpf não foi encontrado.");
							}

							//Verifica se a matricula esta relacionada com a matriz do token
							$cliente_funcionario = $this->ClienteFuncionario->find('first', array('conditions' => array('codigo_cliente_matricula' => $matriz, 'codigo_funcionario' => $funcionario['Funcionario']['codigo']), 'recursive' => -1));

							if (empty($cliente_funcionario)) {
								throw new Exception("Erro: matrícula do funcionário não corresponde a este grupo econômico");
							}


							/*//Se existe registro deste funcionário para esta função (unid + setor + cargo)
							$funcionario_setor_cargo = $this->FuncionarioSetorCargo->find('first',array('conditions' => array('codigo_cliente_alocacao' => $inputData['AplicacaoExame']['codigo_cliente_alocacao'],'codigo_setor' => $inputData['AplicacaoExame']['codigo_setor'], 'codigo_cargo' => $inputData['AplicacaoExame']['codigo_cargo'],'codigo_cliente_funcionario' => $cliente_funcionario['ClienteFuncionario']['codigo']),'recursive' => -1));


	               			if(empty($funcionario_setor_cargo)){
								throw new Exception("Erro: funcionário não possui registro desta unidade + setor + cargo");
		                   	}*/

							$inputData['AplicacaoExame']['codigo_funcionario'] = $funcionario['Funcionario']['codigo'];
						}


						$registroPreexiste = false;
						$qtd_exames_remover = 0;
						$qtd_exames = 0;
						$exames_remover = array();

						$this->loadModel("ExameExterno");
						$this->loadModel("Exame");

						// Varre cada item do conjunto de Exames Aplicados
						if (isset($dadosRecebidos->aplicacao_exame_itens)) {
							foreach ($dadosRecebidos->aplicacao_exame_itens as $k => $item) {

								if (is_array($item)) {
									$item = (object) $item;
								}

								if (isset($item->codigo_exame) && !empty($item->codigo_exame)) {
									$exame = $this->Exame->find('first', array('conditions' => array('codigo' => $item->codigo_exame), 'fields' => array('Exame.codigo', 'Exame.ativo')));

									if (!empty($exame)) {

										//verifica se o exame esta ativo em nossa base de dados
										if ($exame['Exame']['ativo'] == 1) {
											$inputData['AplicacaoExame'][$k]['codigo_exame'] = $item->codigo_exame;
										} else {
											throw new Exception("codigo exame inativo em nossa base de dados (" . $item->codigo_exame . ")");
										}
									} else {
										throw new Exception("codigo exame não encontrado (" . $item->codigo_exame . ")");
									}
								} //fim verificacao do codigo exame
								else {
									//busca na tabela de codigo externo o exame
									$result = $this->ExameExterno->find('first', array('conditions' => array('ExameExterno.codigo_externo' => $item->codigo_externo_exame, 'ExameExterno.codigo_cliente' => $matriz), 'fields' => 'ExameExterno.codigo_exame'));
									//verifica se existe relacionamento do codigo externo
									if (!empty($result)) {
										//busca os dados do exame
										$exame = $this->Exame->find('first', array('conditions' => array('codigo' => $result['ExameExterno']['codigo_exame']), 'fields' => array('Exame.codigo', 'Exame.ativo')));
										//verifica se existe o exame
										if (!empty($exame)) {

											//verifica se o exame esta ativo em nossa base de dados
											if ($exame['Exame']['ativo'] == 1) {
												$inputData['AplicacaoExame'][$k]['codigo_exame'] = $exame['Exame']['codigo'];
											} else {
												throw new Exception("codigo exame externo inativo em nossa base de dados (" . $item->codigo_externo_exame . ")");
											}
										} else {
											throw new Exception("codigo exame externo não encontrado (" . $item->codigo_externo_exame . ")");
										}
									} else {
										throw new Exception("codigo exame externo enviado não relacionado (" . $item->codigo_externo_exame . ")");
									}
								} //fim else do exame externo

								//verificacao para não erra na hora de inserir os dados de aplicacao de exames.
								if (empty($inputData['AplicacaoExame'][$k]['codigo_exame'])) {
									throw new Exception("exame não econtrado em nossa base de dados (" . $item->codigo_externo_exame . ")");
								}


								/** 
								 * Caso se trate de uma Alteração ou Exclusão, é necessário o ID ou Código do Exame Aplicado. 
								 * Para casos de inclusão verifica se o código de Aplicação Exame já existe
								 */
								$codigoExameAplicado = $this->AplicacaoExame->find(
									'first',
									array(
										'fields' => 'AplicacaoExame.codigo',
										'conditions' => array(
											'AplicacaoExame.codigo_cliente_alocacao' => $inputData['AplicacaoExame']['codigo_cliente_alocacao'],
											'AplicacaoExame.codigo_setor' 	=> $inputData['AplicacaoExame']['codigo_setor'],
											'AplicacaoExame.codigo_cargo' 	=> $inputData['AplicacaoExame']['codigo_cargo'],
											'AplicacaoExame.codigo_exame' 	=> $inputData['AplicacaoExame'][$k]['codigo_exame'],
											'AplicacaoExame.codigo_funcionario' => $inputData['AplicacaoExame']['codigo_funcionario']

										)
									)
								);

								$inputData['AplicacaoExame'][$k]['codigo'] = $codigoExameAplicado['AplicacaoExame']['codigo'];
								$exame_removido = false;
								if (!empty($codigoExameAplicado['AplicacaoExame']['codigo'])) {
									$registroPreexiste = true;

									if (isset($item->operacao)) {
										$operacao_exame = strtoupper($item->operacao);
										if ($operacao_exame == "E") {
											/*if(!$this->AplicacaoExame->delete($inputData['AplicacaoExame'][$k]['codigo'])){
												throw new Exception("Erro: Erro ao remover exame (".isset($item->codigo_externo_exame) ? $item->codigo_externo_exame : $item->codigo_exame.")");
											} else {*/
											$exame_removido = true;
											$qtd_exames_remover++;
											$exames_remover[] = $inputData['AplicacaoExame'][$k]['codigo'];
											unset($inputData['AplicacaoExame'][$k]);
											// }
										}
									}
									//Se o exame não é encontrado
								} else {
									//Verifica se existe item->operacao
									if (isset($item->operacao)) {
										$operacao_exame = strtoupper($item->operacao);
										//Se foi solicitado a exclusao, não inclui
										if ($operacao_exame == "E") {
											$qtd_exames_remover++;
											$exame_removido = true;
											unset($inputData['AplicacaoExame'][$k]);
										}
									}
								}

								//valida o valor do $item->aplicavel_em
								if (count($item->aplicavel_em) < 1 && !$exame_removido) {
									throw new Exception("Error: aplicavel_em não existe valores.");
								}

								/**
								 * Apenas para operações do tipo Inclusão e Alteração
								 */
								if ($operacao == "A" && !$exame_removido) {

									$inputData['AplicacaoExame'][$k]['periodo_meses'] 			= isset($item->frequencia)  ? $item->frequencia : null;
									$inputData['AplicacaoExame'][$k]['periodo_apos_demissao'] 	= isset($item->apos_admissao) ? $item->apos_admissao : null;

									$inputData['AplicacaoExame'][$k]['exame_admissional'] 		= (in_array(1, $item->aplicavel_em)) ? 1 : 0;
									$inputData['AplicacaoExame'][$k]['exame_periodico'] 		= (in_array(2, $item->aplicavel_em)) ? 1 : 0;
									$inputData['AplicacaoExame'][$k]['exame_demissional'] 		= (in_array(3, $item->aplicavel_em)) ? 1 : 0;
									$inputData['AplicacaoExame'][$k]['exame_retorno'] 			= (in_array(4, $item->aplicavel_em)) ? 1 : 0;
									$inputData['AplicacaoExame'][$k]['exame_mudanca'] 			= (in_array(5, $item->aplicavel_em)) ? 1 : 0;
									$inputData['AplicacaoExame'][$k]['exame_monitoracao'] 		= (in_array(6, $item->aplicavel_em)) ? 1 : 0;

									$item->idade_minima_periodicidade_itens = !empty($item->idade_minima_periodicidade_itens) ? $item->idade_minima_periodicidade_itens : array();
									$idadeMinPeriodItensLength = count($item->idade_minima_periodicidade_itens);

									$inputData['AplicacaoExame'][$k]['periodo_idade'] 		= null;
									$inputData['AplicacaoExame'][$k]['qtd_periodo_idade'] 	= null;

									//varivel aux se existe algum valor de idade minima
									$boll_idade = false;

									if ($idadeMinPeriodItensLength >= 1) {
										$iteracao = 1;
										foreach ($item->idade_minima_periodicidade_itens as $periodicidade_item) {
											if (is_array($periodicidade_item)) {
												//$periodicidade_item = (object) $periodicidade_item;
											}
											if ($iteracao == 1) {
												$idadeMinima = isset($periodicidade_item->idade_minima) ? $periodicidade_item->idade_minima : null;
												$periodicidade = isset($periodicidade_item->periodicidade) ? $periodicidade_item->periodicidade : null;
												$inputData['AplicacaoExame'][$k]['periodo_idade'] 		= ($idadeMinPeriodItensLength >= 1) ? $idadeMinima : null;
												$inputData['AplicacaoExame'][$k]['qtd_periodo_idade'] 	= ($idadeMinPeriodItensLength >= 1) ? $periodicidade : null;

												break;
											}
										}
									}

									$inputData['AplicacaoExame'][$k]['periodo_idade_2'] 			= ($idadeMinPeriodItensLength >= 2) ? @$item->idade_minima_periodicidade_itens[1]->idade_minima : null;
									$inputData['AplicacaoExame'][$k]['qtd_periodo_idade_2'] 		= ($idadeMinPeriodItensLength >= 2) ? @$item->idade_minima_periodicidade_itens[1]->periodicidade : null;
									$inputData['AplicacaoExame'][$k]['periodo_idade_3'] 			= ($idadeMinPeriodItensLength >= 3) ? @$item->idade_minima_periodicidade_itens[2]->idade_minima : null;
									$inputData['AplicacaoExame'][$k]['qtd_periodo_idade_3'] 		= ($idadeMinPeriodItensLength >= 3) ? @$item->idade_minima_periodicidade_itens[2]->periodicidade : null;
									$inputData['AplicacaoExame'][$k]['periodo_idade_4'] 			= ($idadeMinPeriodItensLength >= 4) ? @$item->idade_minima_periodicidade_itens[3]->idade_minima : null;
									$inputData['AplicacaoExame'][$k]['qtd_periodo_idade_4'] 		= ($idadeMinPeriodItensLength >= 4) ? @$item->idade_minima_periodicidade_itens[3]->periodicidade : null;

									//valida a idade
									if (!is_null($inputData['AplicacaoExame'][$k]['periodo_idade']) && !is_null($inputData['AplicacaoExame'][$k]['qtd_periodo_idade'])) {
										$boll_idade = true;
									}
									if (!is_null($inputData['AplicacaoExame'][$k]['periodo_idade_2']) && !is_null($inputData['AplicacaoExame'][$k]['qtd_periodo_idade_2'])) {
										$boll_idade = true;
									}
									if (!is_null($inputData['AplicacaoExame'][$k]['periodo_idade_3']) && !is_null($inputData['AplicacaoExame'][$k]['qtd_periodo_idade_3'])) {
										$boll_idade = true;
									}
									if (!is_null($inputData['AplicacaoExame'][$k]['periodo_idade_4']) && !is_null($inputData['AplicacaoExame'][$k]['qtd_periodo_idade_4'])) {
										$boll_idade = true;
									}

									if (!$boll_idade) {
										if (empty($inputData['AplicacaoExame'][$k]['periodo_meses'])) {
											throw new Exception("Error: É necessário frequencia ou periodo_idade");
										}
									}

									$inputData['AplicacaoExame'][$k]['codigo_tipo_exame'] 			= $item->objetivo_exame;

									$inputData['AplicacaoExame'][$k]['exame_excluido_convocacao'] 	= (in_array(1, $item->tipos_exames)) ? 1 : 0;
									$inputData['AplicacaoExame'][$k]['exame_excluido_ppp']			= (in_array(2, $item->tipos_exames)) ? 1 : 0;
									$inputData['AplicacaoExame'][$k]['exame_excluido_aso']			= (in_array(3, $item->tipos_exames)) ? 1 : 0;
									$inputData['AplicacaoExame'][$k]['exame_excluido_pcmso']		= (in_array(4, $item->tipos_exames)) ? 1 : 0;
									$inputData['AplicacaoExame'][$k]['exame_excluido_anual']		= (in_array(5, $item->tipos_exames)) ? 1 : 0;
								}
							}
							//Quantidade de exames - (Adiciona 1 devido ao índice iniciar no 0)
							$qtd_exames = $k + 1;
						}


						// pr($inputData);exit;

						// $this->dados["dados_recebidos"] = $inputData;
						// $this->dados["outros_valores_debug"] = $outrosValoresADebugar;

						// Instancia as demais models necessarias
						$this->loadModel('Usuario');

						// Pega o usuario inclusao                                        
						$usuario = $this->Usuario->find('first', array('fields' => array('Usuario.codigo'), 'conditions' => array('Usuario.token' => $token)));
						// Seta o codigo do usuario inclusao
						$_SESSION['Auth']['Usuario']['codigo'] = $usuario['Usuario']['codigo'];

						// Pega o cliente para saber qual a empresa que esta trabalhando rhhealth, profit
						$cliente_alocacao = $this->Cliente->find('first', array('fields' => array('Cliente.codigo_empresa'), 'conditions' => array('Cliente.codigo' => $inputData['AplicacaoExame']['codigo_cliente_alocacao'])));
						// Seta o codigo da empresa
						$_SESSION['Auth']['Usuario']['codigo_empresa'] = $cliente_alocacao['Cliente']['codigo_empresa'];

						/**
						 * Tenta finalmente Incluir, Alterar, Excluir ou Consultar registros (I, A, E, C)
						 * O begin transaction, commit e rollback já são efetuados no método "incluir" e "editar"
						 * da model AplicacaoExame, portanto, não são necessários nas operações "I" e "A"
						 */
						if ($operacao == "A") {

							//Se existe registro de PCMSO
							if ($registroPreexiste) {

								//Se existe registro para remover, inicia transação
								if (count($exames_remover) > 0) {

									$this->AplicacaoExame->query('begin transaction');
									$erro_atualiza = false;

									$conditions_excluir = array('AplicacaoExame.codigo' => $exames_remover);

									if (!$this->AplicacaoExame->deleteAll($conditions_excluir)) {

										$erro = $this->getErrorRecursive($this->AplicacaoExame->validationErrors);
										$erro_atualiza = true;
									} else {
										$this->dados["status"] = "0";
										$this->dados["msg"] = 'Processo de exclusão realizado com sucesso!';
									}
									//Se restaram exames para atualizar
									if ($qtd_exames > $qtd_exames_remover && !$erro_atualiza) {
										if ($this->AplicacaoExame->editar($inputData)) {
											$this->dados["status"] = "0";
											$this->dados["msg"] = 'Processo de atualização realizado com sucesso!';
										} else {
											$erro = $this->getErrorRecursive($this->AplicacaoExame->validationErrors);
											$erro_atualiza = true;
										}
									}

									//Se algum erro foi gerado
									if ($erro_atualiza) {
										$this->AplicacaoExame->rollback();
										throw new Exception("Nao foi possível realizar o processo de alteração. " . $erro);
									} else {
										$this->AplicacaoExame->commit();
									}
									//Se não tem nenhum item para remover, não precisa de transação
								} else {
									if ($this->AplicacaoExame->editar($inputData)) {

										$this->dados["status"] = "0";
										$this->dados["msg"] = 'Processo de atualização realizado com sucesso!';
									} else {
										$erro = $this->getErrorRecursive($this->AplicacaoExame->validationErrors);
										$erro_atualiza = true;
									}
								}

								//Se não existe registro de PCMSO
							} else {
								if ($qtd_exames > $qtd_exames_remover) {
									if ($this->AplicacaoExame->incluir($inputData)) {
										$this->dados["status"] = "0";
										$this->dados["msg"] = 'Processo de inclusão realizado com sucesso!';
									} else {
										$erro = $this->getErrorRecursive($this->AplicacaoExame->validationErrors);
										// $this->AplicacaoExame->rollback();
										throw new Exception("Nao foi possível realizar o processo de insercao. " . $erro);
									}
								} else {
									throw new Exception("Nao foi possível realizar o processo de insercao. Nenhum registro informado");
								}
							}
						} else if ($operacao == "E") {
							$ids = array();
							//Se existem exames para remover, marcados com a tag interna de operacao
							if (count($exames_remover) > 0) {
								$ids = $exames_remover;
							}

							if ($qtd_exames > $qtd_exames_remover) {
								foreach ($inputData['AplicacaoExame'] as $k => $v) {
									if (is_numeric($k))
										$ids[] = $v['codigo'];
								}
							}

							$ids = array_filter($ids); // Remove possíveis valores vazios
							$conditions = array("AplicacaoExame.codigo IN (" . implode(', ', $ids) . ")");
							//Se existe registro para remover
							if (count($ids) > 0) {

								$this->AplicacaoExame->query('begin transaction');
								if ($this->AplicacaoExame->deleteAll($conditions)) {
									$this->AplicacaoExame->commit();
									$this->dados["status"] = "0";
									$this->dados["msg"] = 'Processo de exclusão realizado com sucesso!';
								} else {
									$this->AplicacaoExame->rollback();
									$msgErro = "Nao foi possível realizar o processo de exclusão. ";
									$modelErro = $this->getErrorRecursive($this->AplicacaoExame->validationErrors);
									if (count($ids) == 0) {
										$msgErro .= "Registros não encontrados. ";
									}
									throw new Exception($msgErro . $modelErro);
								}
							} else {
								throw new Exception("Nao foi possível realizar o processo de exclusão. Nenhum registro encontrado ");
							}
						} else if ($operacao == "C") {

							$conditions = $this->AplicacaoExame->converteFiltroEmCondition($inputData['AplicacaoExame']);
							if (!empty($inputData['AplicacaoExame']['codigo_funcionario'])) {
								$conditions['AplicacaoExame.codigo_funcionario'] = $inputData['AplicacaoExame']['codigo_funcionario'];
							} else {
								$conditions['AplicacaoExame.codigo_funcionario'] = NULL;
							}


							/*
                			$joins = array(
	                			array(
	                				'table' => 'exames',
	                				'alias' => 'Exame',
	                				'conditions' => 'AplicacaoExame.codigo_exame = Exame.codigo'
                				)
                			);*/

							$retornoExames = $this->AplicacaoExame->find('all', array(
								'conditions' => $conditions,
								//'joins' => $joins,
								'fields' => array(
									'AplicacaoExame.codigo',
									'AplicacaoExame.periodo_meses', // AS frequencia,
									'AplicacaoExame.periodo_apos_demissao', // AS apos_admissao',
									'AplicacaoExame.exame_admissional',
									'AplicacaoExame.exame_periodico',
									'AplicacaoExame.exame_demissional',
									'AplicacaoExame.exame_retorno',
									'AplicacaoExame.exame_mudanca',
									'AplicacaoExame.exame_monitoracao',
									'AplicacaoExame.periodo_idade',
									'AplicacaoExame.qtd_periodo_idade',
									'AplicacaoExame.periodo_idade_2',
									'AplicacaoExame.qtd_periodo_idade_2',
									'AplicacaoExame.periodo_idade_3',
									'AplicacaoExame.qtd_periodo_idade_3',
									'AplicacaoExame.periodo_idade_4',
									'AplicacaoExame.qtd_periodo_idade_4',
									'AplicacaoExame.codigo_tipo_exame',
									'AplicacaoExame.exame_excluido_convocacao',
									'AplicacaoExame.exame_excluido_ppp',
									'AplicacaoExame.exame_excluido_aso',
									'AplicacaoExame.exame_excluido_pcmso',
									'AplicacaoExame.exame_excluido_anual'
								)
							));

							$aplicacaoExameItens = array();

							foreach ($retornoExames as $k => $item) {
								if ($item['AplicacaoExame']) {

									$item = $item['AplicacaoExame'];

									$aplicacaoExameItens[$k]['codigo'] = $item['codigo'];
									$aplicacaoExameItens[$k]['frequencia'] = $item['periodo_meses'];
									$aplicacaoExameItens[$k]['apos_admissao'] = $item['periodo_apos_demissao'];

									$aplicacaoExameItens[$k]['aplicavel_em'] = array();
									if ($item['exame_admissional'] != null)
										$aplicacaoExameItens[$k]['aplicavel_em'][] = 1;
									if ($item['exame_periodico'] != null)
										$aplicacaoExameItens[$k]['aplicavel_em'][] = 2;
									if ($item['exame_demissional'] != null)
										$aplicacaoExameItens[$k]['aplicavel_em'][] = 3;
									if ($item['exame_retorno'] != null)
										$aplicacaoExameItens[$k]['aplicavel_em'][] = 4;
									if ($item['exame_mudanca'] != null)
										$aplicacaoExameItens[$k]['aplicavel_em'][] = 5;
									if ($item['exame_monitoracao'] != null)
										$aplicacaoExameItens[$k]['aplicavel_em'][] = 6;

									$aplicacaoExameItens[$k]['idade_minima_periodicidade_itens'] = array();

									$periodicidadeItem = $this->addItemPeriodicidade($item['periodo_idade'], $item['qtd_periodo_idade']);
									if ($periodicidadeItem != null) {
										$aplicacaoExameItens[$k]['idade_minima_periodicidade_itens'][] = $periodicidadeItem;
									}

									for ($i = 2; $i < 5; $i++) {
										$columName1 = 'periodo_idade_' . $i;
										$columName2 = 'qtd_periodo_idade_' . $i;
										$periodicidadeItem = $this->addItemPeriodicidade($item[$columName1], $item[$columName2]);
										if ($periodicidadeItem != null) {
											$aplicacaoExameItens[$k]['idade_minima_periodicidade_itens'][] = $periodicidadeItem;
										}
									}

									$aplicacaoExameItens[$k]['tipos_exames'] = array();
									if ($item['exame_excluido_convocacao'] != null)
										$aplicacaoExameItens[$k]['tipos_exames'][] = 1;
									if ($item['exame_excluido_ppp'] != null)
										$aplicacaoExameItens[$k]['tipos_exames'][] = 2;
									if ($item['exame_excluido_aso'] != null)
										$aplicacaoExameItens[$k]['tipos_exames'][] = 3;
									if ($item['exame_excluido_pcmso'] != null)
										$aplicacaoExameItens[$k]['tipos_exames'][] = 4;
									if ($item['exame_excluido_anual'] != null)
										$aplicacaoExameItens[$k]['tipos_exames'][] = 5;
								}
							}

							$AplicacaoExame = new stdClass();
							$AplicacaoExame->operacao = $operacao;
							// $AplicacaoExame->codigo_empresa = $dadosRecebidos->codigo_empresa;
							$AplicacaoExame->codigo_externo_unidade_alocacao = $dadosRecebidos->codigo_unidade_alocacao;
							$AplicacaoExame->codigo_externo_setor = $dadosRecebidos->codigo_setor;
							$AplicacaoExame->codigo_externo_cargo = $dadosRecebidos->codigo_cargo;
							$AplicacaoExame->aplicacaoExameItens = $aplicacaoExameItens;


							if (count($retornoExames > 0)) {
								$this->dados["status"] = "0";
								$this->dados["msg"] = 'Consulta realizada com sucesso!';
								$this->dados["retorno"] = $AplicacaoExame;
							} else {
								$msgErro = "Nao foi possível realizar o processo de consulta. ";
								$modelErro = $this->getErrorRecursive($this->AplicacaoExame->validationErrors);
								throw new Exception($msgErro . $modelErro);
							}
						} else {

							$this->dados["status"] = "5";
							$this->dados["msg"] = 'Operacao invalida.';
						}
					} catch (Exception $e) {
						// Erro do codigo do cliente alocacao (5)
						$this->log('erro: ' . $e->getMessage(), 'debug');
						$this->dados["status"] = "5";
						$this->dados['msg']     = $e->getMessage();
					}
				} else {
					// Msg de erro
					$this->dados["status"] = "4";
					$campos_obrigatorios = "";
					if (!empty($this->fields->campos_obrigatorios)) {
						$campos_obrigatorios = implode(", ", $this->fields->campos_obrigatorios);
					}
					$this->dados['msg'] = 'Foram encontrados os seguintes erros: ' . $campos_obrigatorios;
				} // Fim valida campos obrigatorios
				// Fim valida_autorizacao else
			} else {
				$this->dados = $this->ApiAutorizacao->getStatus();
			}
		} else {
			// Não foi passado o get de cnpj e token, seta o erro com codigo 1 
			$this->dados["status"] = "1";
			$this->dados["msg"] = "Não foi passado o parâmetro CNPJ ou TOKEN";
		}

		$retorno = json_encode($this->dados);

		// Para gerar o log quando houver consulta        
		$ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO O PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno

		$this->ApiAutorizacao->log_api(
			$this->ApiAutorizacao->conteudoLog($_GET, $dadosRecebidos),
			$retorno,
			$this->dados['status'],
			$ret_mensagem,
			"API_PCMSO_SINCRONIZAR_EXAME"
		);

		/**
		 * REGISTRO DE ALERTA
		 *
		 * Inserir apenas se o status for diferente de sucesso
		 */
		if ($this->dados['status'] != '0') {
			$mail_data_content = array(
				'tipo_integracao' => 'API_PCMSO_SINCRONIZAR_EXAME',
				'conteudo' => $this->ApiAutorizacao->conteudoLog($_GET, $dadosRecebidos),
				'retorno' => $retorno,
				'descricao' => $ret_mensagem,
				'status' => $this->dados['status'],
				'data' => date("Y-m-d H:i:s")
			);
			$this->ApiAutorizacao->alerta_integracao($mail_data_content, array('model' => 'Usuario'));
		}
		/**
		 * FIM REGISTRO DE ALERTA
		 */

		// Retorna sucesso ou erro de acordo com o tipo de conteudo usado para consumir a API
		if ($this->ApiDataFormat->getContentType() == 'json') {
			// Retorna finalmente o JSON        
			header('Content-type: application/json; charset=UTF-8');
			echo $retorno;
		} else if ($this->ApiDataFormat->getContentType() == 'xml') {
			// Retorna finalmente o XML
			App::import('Helper', 'Xml');
			$xml = new XmlHelper();
			$xmlStr = $xml->header(array('version' => '1.1'));
			$xmlStr .= $xml->serialize(
				json_decode($retorno),
				array(
					'root' => 'retorno',
					'format' => 'tags',
					'cdata' => false/*, 'whitespace' => true*/
				)
			);
			header('Content-type: application/xml; charset=UTF-8');
			echo $xmlStr;
		}

		exit;
	}

	/**
	 * Valida os campos para todas operações no método sincronizar: 
	 * I (Inclusão), A (Atualização), E (Exclusão)
	 * @param int|string|array $dados
	 * @return boolean 
	 */
	private function validaCamposObrigatorios($dados)
	{


		$this->fields->verificaPreenchimentoObrigatorio(isset($dados->operacao) ? $dados->operacao : null, "campo operacao obrigatorio");

		//$operacao = isset($dados->operacao) && trim($dados->operacao) !== '' ? ($dados->operacao == "I" || $dados->operacao == "A" ? "A" : $dados->operacao ) : "A";
		if (isset($dados->operacao)) {
			$operacao = strtoupper($dados->operacao);
		} else {
			$operacao = null;
		}
		$operacao = isset($operacao) && trim($operacao) !== '' ? ($operacao == "I" || $operacao == "A" ? "A" : $operacao) : null;
		/**
		 * valida caso vier as duas tags codigo unidade alocacao ou codigo externo unidade alocacao
		 * nao pode vir as duas
		 */
		if (isset($dados->codigo_unidade_alocacao) && isset($dados->codigo_externo_unidade_alocacao)) {
			$this->fields->setCamposObrigatorios("Favor setar uma das tags codigo_unidade_alocacao ou codigo_externo_unidade_alocacao, retirar uma delas.");
		}

		if (isset($dados->codigo_setor) && isset($dados->codigo_externo_setor)) {
			$this->fields->setCamposObrigatorios("Favor setar uma das tags codigo_setor ou codigo_externo_setor, retirar uma delas.");
		}

		if (isset($dados->codigo_cargo) && isset($dados->codigo_externo_cargo)) {
			$this->fields->setCamposObrigatorios("Favor setar uma das tags codigo_cargo ou codigo_externo_cargo, retirar uma delas.");
		}

		if (isset($dados->cpf_funcionario) && trim($dados->cpf_funcionario) !== '') {
			if (strlen($dados->cpf_funcionario) != 11) {
				$this->fields->setCamposObrigatorios("O campo cpf_funcionario deve conter 11 dígitos");
			}
			$this->fields->verificaInteiro($dados->cpf_funcionario, 'cpf_funcionario deve ser valor inteiro');
		}


		// Inicializa propriedades para prevenir Warnings e Notices, facilitando assim o debug
		$dados->codigo_setor = (isset($dados->codigo_setor) ? $dados->codigo_setor : null);
		$dados->codigo_externo_setor = (isset($dados->codigo_externo_setor) ? $dados->codigo_externo_setor : null);
		$dados->codigo_cargo = (isset($dados->codigo_cargo) ? $dados->codigo_cargo : null);
		$dados->codigo_externo_cargo = (isset($dados->codigo_externo_cargo) ? $dados->codigo_externo_cargo : null);
		$dados->codigo_exame = (isset($dados->codigo_exame) ? $dados->codigo_exame : null);
		$dados->codigo_externo_exame = (isset($dados->codigo_externo_exame) ? $dados->codigo_externo_exame : null);
		$dados->codigo_unidade_alocacao = (isset($dados->codigo_unidade_alocacao) ? $dados->codigo_unidade_alocacao : null);
		$dados->codigo_externo_unidade_alocacao = (isset($dados->codigo_externo_unidade_alocacao) ? $dados->codigo_externo_unidade_alocacao : null);

		/**
		 * Campos obrigatorios genéricos 
		 */
		// $this->fields->verificaPreenchimentoObrigatorio($dados->codigo_empresa, "campo codigo_empresa obrigatorio");

		$this->fields->verificaCodigoExterno(
			$dados->codigo_unidade_alocacao,
			$dados->codigo_externo_unidade_alocacao,
			"campo codigo_unidade_alocacao ou codigo_externo_unidade_alocacao obrigatorio"
		);
		$this->fields->verificaCodigoExterno(
			$dados->codigo_setor,
			$dados->codigo_externo_setor,
			"campo codigo_setor ou codigo_externo_setor obrigatorio"
		);
		$this->fields->verificaCodigoExterno(
			$dados->codigo_cargo,
			$dados->codigo_externo_cargo,
			"campo codigo_cargo ou codigo_externo_cargo obrigatorio"
		);

		//verifica se o valor é inteiro
		if (isset($dados->codigo_unidade_alocacao)) {
			$this->fields->verificaInteiro($dados->codigo_unidade_alocacao, 'codigo_unidade_alocacao deve ser inteiro');
		}

		if (isset($dados->codigo_setor)) {
			$this->fields->verificaInteiro($dados->codigo_setor, 'codigo_setor deve ser inteiro');
		}

		if (isset($dados->codigo_cargo)) {
			$this->fields->verificaInteiro($dados->codigo_cargo, 'codigo_cargo deve ser inteiro');
		}

		if ($operacao == 'A' || $operacao == 'E') {

			$this->fields->verificaPreenchimentoObrigatorioArray(isset($dados->aplicacao_exame_itens) ? $dados->aplicacao_exame_itens : array(), "campo aplicacao_exame_itens obrigatorio e deve ter ao menos um item");

			if (isset($dados->aplicacao_exame_itens)) {
				// Verifica se cada Exame do PCMSO possui código preenchido
				foreach ($dados->aplicacao_exame_itens as $item) {

					if (is_array($item)) {
						$item = (object) $item;
					}

					$item->codigo_exame = (isset($item->codigo_exame) ? $item->codigo_exame : null);
					$item->codigo_externo_exame = (isset($item->codigo_externo_exame) ? $item->codigo_externo_exame : '');
					$this->fields->verificaCodigoExterno(
						$item->codigo_exame,
						$item->codigo_externo_exame,
						"campo codigo_exame ou codigo_externo_exame obrigatorio"
					);

					if (isset($item->codigo_exame)) {
						if (!preg_match('/^\d*$/', trim($item->codigo_exame))) {
							$this->fields->setCamposObrigatorios('aplicacao_exame_itens:codigo_exame deve ser inteiro. (codigo_exame: ' . $item->codigo_exame . ')');
						}
					}

					if (isset($item->frequencia)) {
						if (!preg_match('/^\d*$/', trim($item->frequencia))) {
							$this->fields->setCamposObrigatorios('aplicacao_exame_itens:frequencia deve ser inteiro. (frequencia: ' . $item->frequencia . ')');
						}
					}

					if (isset($item->apos_admissao)) {
						if (!preg_match('/^\d*$/', trim($item->apos_admissao))) {
							$this->fields->setCamposObrigatorios('aplicacao_exame_itens:apos_admissao deve ser inteiro. (apos_admissao: ' . $item->apos_admissao . ')');
						}
					}
				}
			}
		}

		/**
		 * Campos obrigatórios específicos, apenas para as 
		 * operações de Inclusão e Alteração
		 */
		if ($operacao == 'A') {
			if (isset($dados->aplicacao_exame_itens)) {
				foreach ($dados->aplicacao_exame_itens as $item) {

					if (is_array($item)) {
						$item = (object) $item;
					}


					if (isset($item->idade_minima_periodicidade_itens)) {
						$idadeMinPeriodItensLength = count($item->idade_minima_periodicidade_itens);
					} else {
						$idadeMinPeriodItensLength = 0;
					}

					if (!isset($item->idade_minima_periodicidade_itens)) {
						$item->idade_minima_periodicidade_itens = null;
					}

					//Se a frequencia não foi preenchida e a idade_minima_periodicidade_itens não foi preenchida
					if (!isset($item->frequencia) && empty($item->idade_minima_periodicidade_itens)) {
						$this->fields->setCamposObrigatorios('aplicacao_exames_itens: necessário preencher os campos de frequencia ou idade_minima_periodicidade_itens');
					}

					// Permitir apenas o preenchimento de 
					// frequencia de exames apos_admissao OU 
					// periodicidade por idade minima
					if (
						$this->fields->estaPreenchido($item->idade_minima_periodicidade_itens) &&
						$idadeMinPeriodItensLength >= 1
					) {
						// Periodicidade por idade mínima					
						foreach ($item->idade_minima_periodicidade_itens as $periodicidade) {
							if (is_array($periodicidade)) {
								$periodicidade = (object) $periodicidade;
							}
							if (isset($periodicidade->idade_minima)) {
								if (!is_null($periodicidade->idade_minima) || !is_null($periodicidade->periodicidade)) {
									//Se os campos de periodicidade por idade estão preenchidos e o campo de frequencia
									if (isset($item->frequencia) && trim($item->frequencia) !== '') {
										$this->fields->setCamposObrigatorios("nem todos os campos relativos a frequencia de exames devem ser preenchidos - " .
											"Ou frequencia ou idade_minima_periodicidade_itens, não ambos. " . "(frequencia: " . $item->frequencia . " - idade_minima: " . $periodicidade->idade_minima . " - periodicidade: " . $periodicidade->periodicidade . ")");
										break;
									}
								}
							}

							if (isset($periodicidade->idade_minima)) {
								if (!preg_match('/^\d*$/', trim($periodicidade->idade_minima))) {
									$this->fields->setCamposObrigatorios('aplicacao_exame_itens:idade_minima deve ser inteiro. (idade_minima:' . $periodicidade->idade_minima . ')');
								}
							}

							if (isset($periodicidade->periodicidade)) {
								if (!preg_match('/^\d*$/', trim($periodicidade->periodicidade))) {
									$this->fields->setCamposObrigatorios('aplicacao_exame_itens:periodicidade deve ser inteiro. (periodicidade: ' . $periodicidade->periodicidade . ')');
								}
							}
						}
					}

					// aplicavel_em
					$item->aplicavel_em = (isset($item->aplicavel_em) ? $item->aplicavel_em : null);
					$this->fields->verificaPreenchimentoObrigatorioArray($item->aplicavel_em, "campo aplicavel_em obrigatorio");
					//verifica se existe para varre o array com os valores e verificar se são inteiros
					if (!is_null($item->aplicavel_em)) {
						$opcoes_aplicavel_em = array(1 => 'Admissional', 2 => 'Periódico', 3 => 'Demissional', 4 => 'Retorno Trabalho', 5 => 'Mudança de Riscos Ocupacionais', 6 => 'Monitoração Pontual');

						foreach ($item->aplicavel_em as $aplicavel) {
							if (isset($aplicavel)) {
								$this->fields->validaSoNumeros($aplicavel, 'aplicacao_exame_itens:aplicavel_em o valor deve ser inteiro (' . $aplicavel . ')');
								//Se o valor não foi encontrado entre as opções válidas
								if (!isset($opcoes_aplicavel_em[$aplicavel])) {
									$this->fields->setCamposObrigatorios("aplicacao_exame_itens: valor informado não é uma opção válida para o campo aplicavel_em (" . $aplicavel . ")");
								}
							}
						}
					}

					// objetivo_exame
					$item->objetivo_exame = (isset($item->objetivo_exame) ? $item->objetivo_exame : null);
					$this->fields->verificaPreenchimentoObrigatorioArray($item->objetivo_exame, "campo objetivo_exame obrigatorio");
					//verifica se existe para varre o array com os valores e verificar se são inteiros
					if (!is_null($item->objetivo_exame)) {
						$this->fields->verificaInteiro($item->objetivo_exame, 'aplicacao_exame_itens:objetivo_exame os valores devem ser inteiro');
					}

					// tipos_exames
					$item->tipos_exames = (isset($item->tipos_exames) ? $item->tipos_exames : null);
					$this->fields->verificaPreenchimentoObrigatorioArray($item->tipos_exames, "campo tipos_exames obrigatorio");
					//verifica se existe para varre o array com os valores e verificar se são inteiros
					if (!is_null($item->tipos_exames)) {
						foreach ($item->tipos_exames as $tipo_ex) {
							if (isset($tipo_ex)) {
								$this->fields->verificaInteiro($tipo_ex, 'aplicacao_exame_itens:tipos_exames os valores devem ser inteiro');
							}
						}
					}
				}
			}
		}

		// Retorna que os campos obrigatórios estao incorretos.
		if (!empty($this->fields->campos_obrigatorios)) {
			return false;
		}

		return true;
	}

	private function addItemPeriodicidade($idade_minima, $periodicidade)
	{
		if (is_null($idade_minima) && is_null($periodicidade)) {
			return false;
		} else {
			$periodicidadeObj = new stdClass();
			if ($idade_minima != null) {
				$periodicidadeObj->idade_minima = $idade_minima;
			}
			if ($periodicidade != null) {
				$periodicidadeObj->periodicidade = $periodicidade;
			}
		}
		return $periodicidadeObj;
	}

	/**
	 * Obtém mensagem de erro de forma recursiva
	 * @param array $errorArr
	 * @return string
	 */
	private function getErrorRecursive($errorArr)
	{
		foreach ($errorArr as $k => $v) {
			if (is_array($v)) {
				return $this->getErrorRecursive($v);
			} else {
				return $v;
			}
		}
	}
}
