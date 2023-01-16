<?php
class FichaClinica extends AppModel
{

	public $name            = 'FichaClinica';
	public $tableSchema     = 'dbo';
	public $databaseTable   = 'RHHealth';
	public $useTable        = 'fichas_clinicas';
	public $primaryKey      = 'codigo';
	public $actsAs          = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_fichas_clinicas'));
	public $recursive       = -1;

	public $validate = array(
		'codigo_medico' => array(
			'rule' => 'notEmpty',
			'message' => 'Este campo é obrigatório',
			'required' => true
		),
		'incluido_por' => array(
			'rule' => 'notEmpty',
			'message' => 'Este campo é obrigatório',
			'required' => true
		),
		'hora_inicio_atendimento' => array(
			'rule' => 'notEmpty',
			'message' => 'Este campo é obrigatório',
			'required' => true
		),
		'hora_fim_atendimento' => array(
			'rule' => 'notEmpty',
			'message' => 'Este campo é obrigatório',
			'required' => true
		)
	);

	public $hasMany = array(
		'FichaClinicaResposta' => array(
			'className'    => 'FichaClinicaResposta',
			'foreignKey'    => 'codigo_ficha_clinica'
		),
		'FichaClinicaFarmaco' => array(
			'className' => 'FichaClinicaFarmaco',
			'foreignKey' => 'codigo_ficha_clinica'
		)
	);

	public $belongsTo = array(
		'PedidoExame' => array(
			'ClassName' => 'PedidoExame',
			'foreignKey' => 'codigo_pedido_exame'
		),
		'Medico' => array(
			'ClassName' => 'Medico',
			'foreignKey' => 'codigo_medico'
		)
	);

	public function bindListagemFichaPcd()
	{
		$this->bindModel(array(
			'hasOne' => array(
				'PedidoExame' => array(
					'type' => 'LEFT',
					'foreignKey' => false,
					'conditions' => 'FichaClinica.codigo_pedido_exame = PedidoExame.codigo'
				),
				'Medico' => array(
					'type' => 'LEFT',
					'foreignKey' => false,
					'conditions' => 'FichaClinica.codigo_medico = Medico.codigo'
				),
				'FichaClinicaResposta' => array(
					'type' => 'INNER',
					'foreignKey' => false,
					'conditions' => 'FichaClinica.codigo = FichaClinicaResposta.codigo_ficha_clinica 
					AND FichaClinicaResposta.codigo_ficha_clinica_questao = 195 AND FichaClinicaResposta.resposta = 1'
				),
				'ItemPedidoExame' => array(
					'type' => 'LEFT',
					'foreignKey' => false,
					'conditions' => 'ItemPedidoExame.codigo = 
									( Select top 1 codigo from RHHealth.dbo.itens_pedidos_exames where codigo_pedidos_exames = PedidoExame.codigo ORDER BY 1 DESC )'
				),
				'ClienteFuncionario' => array(
					'type' => 'LEFT',
					'foreignKey' => false,
					'conditions' => 'PedidoExame.codigo_cliente_funcionario = ClienteFuncionario.codigo'
				),
				'Cliente' => array(
					'type' => 'LEFT',
					'foreignKey' => false,
					'conditions' => 'PedidoExame.codigo_cliente = Cliente.codigo'
				),
				'Funcionario' => array(
					'type' => 'LEFT',
					'foreignKey' => false,
					'conditions' => 'PedidoExame.codigo_funcionario = Funcionario.codigo'
				),
			)
		), false);
	}

	public function converteFiltroEmCondition($data)
	{
		$conditions = array();

		if (!empty($data['codigo_cliente']))
			$conditions['PedidoExame.codigo_cliente'] = $data['codigo_cliente'];

		if (!empty($data['codigo']))
			$conditions['FichaClinica.codigo'] = $data['codigo'];

		if (!empty($data['codigo_pedido_exame']))
			$conditions['FichaClinica.codigo_pedido_exame'] = $data['codigo_pedido_exame'];

		if (!empty($data['nome_funcionario']))
			$conditions['Funcionario.nome LIKE'] = '%' . $data['nome_funcionario'] . '%';

		if (!empty($data['nome_medico']))
			$conditions['Medico.nome LIKE'] = '%' . $data['nome_medico'] . '%';

		if (empty($conditions['PedidoExame.codigo_empresa']))
			$conditions['PedidoExame.codigo_empresa'] = $_SESSION['Auth']['Usuario']['codigo_empresa'];

		return $conditions;
	}

	public function converteFiltroPedidoExameEmCondition($data)
	{
		$conditions = array();

		if (!empty($data['codigo_fornecedor']))
			$conditions['ItemPedidoExame.codigo_fornecedor'] = $data['codigo_fornecedor'];

		if (!empty($data['codigo']))
			$conditions['PedidoExame.codigo'] = $data['codigo'];

		if (!empty($data['codigo_cliente']))
			$conditions['Cliente.codigo'] = $data['codigo_cliente'];

		if (!empty($data['codigo_funcionario']))
			$conditions['Funcionario.nome LIKE'] = '%' . $data['codigo_funcionario'] . '%';

		return $conditions;
	}

	public function carregar($codigo)
	{
		$dados = $this->find('first', array(
			'conditions' => array(
				$this->name . '.codigo' => $codigo
			)
		));
		return $dados;
	}

	public function montaQuestoes($dados_funcionario = array())
	{
		$options['conditions']['FichaClinicaGrupoQuestao.ativo'] = 1;

		$containConditions['FichaClinicaQuestao.codigo_ficha_clinica_questao'] =  array(NULL, '', 0);
		$containConditions['FichaClinicaQuestao.ativo'] =  1;
		//a pedido da demanda PC-1115, nao é para mostrar a pergunta 274, referente ao resultado de exames
		// $containConditions['FichaClinicaQuestao.codigo <> '] =  274;

		if (!empty($dados_funcionario['sexo'])) {
			$containConditions['FichaClinicaQuestao.exibir_se_sexo'] = array($dados_funcionario['sexo'], NULL, '');
		}

		if (!empty($dados_funcionario['data_nascimento'])) {
			//Data do nascimento
			list($dia, $mes, $ano) = explode('/', $dados_funcionario['data_nascimento']);

			//Data atual
			$hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

			//calcula idade
			$nascimento = mktime(0, 0, 0, $mes, $dia, $ano);
			$idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);

			$containConditions['OR']['FichaClinicaQuestao.exibir_se_idade_maior_que <'] = $idade;
			$containConditions['OR']['AND']['FichaClinicaQuestao.exibir_se_idade_maior_que'] = NULL;

			$containConditions['OR']['FichaClinicaQuestao.exibir_se_idade_menor_que >'] = $idade;
			$containConditions['OR']['AND']['FichaClinicaQuestao.exibir_se_idade_menor_que'] = NULL;
		}

		$options['fields'] = array(
			'FichaClinicaGrupoQuestao.descricao',
		);

		// faz um select de forma recursiva
		$options['contain'] = array(
			'FichaClinicaQuestao' => array(
				'conditions' => $containConditions,
				'fields' => array(
					'FichaClinicaQuestao.codigo',
					'FichaClinicaQuestao.tipo',
					'FichaClinicaQuestao.campo_livre_label',
					'FichaClinicaQuestao.observacao',
					'FichaClinicaQuestao.obrigatorio',
					'FichaClinicaQuestao.ajuda',
					'FichaClinicaQuestao.span',
					'FichaClinicaQuestao.label',
					'FichaClinicaQuestao.conteudo',
					'FichaClinicaQuestao.parentesco_ativo',
					'FichaClinicaQuestao.quebra_linha',
					'FichaClinicaQuestao.opcao_selecionada',
					'FichaClinicaQuestao.opcao_abre_menu_escondido',
					'FichaClinicaQuestao.farmaco_ativo',
					'FichaClinicaQuestao.opcao_exibe_label',
					'FichaClinicaQuestao.multiplas_cids_ativo',
					'FichaClinicaQuestao.ativo',
					'FichaClinicaQuestao.multiplas_cids_exibe_parentesco',
					'FichaClinicaQuestao.farmaco_campo_exibir',
					'FichaClinicaQuestao.multiplas_cids_esconde_outros',
					'FichaClinicaQuestao.riscos_ativo',
				)
			),
			'FichaClinicaQuestao.FichaClinicaSubQuestao'
		);

		$questoes = $this->FichaClinicaResposta->FichaClinicaQuestao->FichaClinicaGrupoQuestao->find('all', $options);

		//$query = $this->FichaClinicaResposta->FichaClinicaQuestao->FichaClinicaGrupoQuestao->getDataSource()->getLog(false, false);
		// $this->log($query,'debug');

		// valida os campos obrigatorios ===============
		foreach ($questoes as $key => $grupoQuestao) {
			foreach ($grupoQuestao['FichaClinicaQuestao'] as $key => $questao) {
				if ($questao['obrigatorio']) {
					$this->FichaClinicaResposta->validate[$questao['codigo'] . '_resposta'] = array(
						'rule' => 'notEmpty',
						'message' => 'Este campo é obrigatório',
						'required' => true
					);
				}
				if (!empty($questao['FichaClinicaSubQuestao'])) {
					foreach ($questao['FichaClinicaSubQuestao'] as $key => $subquestao) {
						if ($subquestao['obrigatorio']) {
							$this->FichaClinicaResposta->validate[$subquestao['codigo'] . '_resposta'] = array(
								'rule' => 'notEmpty',
								'message' => 'Este campo é obrigatório',
								'required' => true
							);
						}
					}
				}
			}
		}
		//===============================================

		return $questoes;
	}

	public function incluir($data)
	{
		// organiza a variavel para salvar recursivamente
		$ficha_clinica_resposta = $data['FichaClinicaResposta'];
		if ($retorno = $this->organizaVariavelRecursiva($data)) {
			$data['FichaClinicaResposta'] = $retorno;
		} else {
			unset($data['FichaClinicaResposta']);
		}

		//mata a validação antes de salvar (neste ponto todos os campos já foram validados)
		$this->FichaClinicaResposta->validate = array();

		// salva de forma recursiva
		if (parent::incluirTodos($data)) {
			$cid10 = $ficha_clinica_resposta['cid10'];
			unset($ficha_clinica_resposta['cid10']);
			unset($ficha_clinica_resposta['campo_livre']);
			unset($ficha_clinica_resposta['parentesco']);
			unset($ficha_clinica_resposta['riscos']);

			if (!empty($data['ItemPedidoExameBaixa'])) {
				//realiza baixa dos exames
				$dadosBaixa['ItemPedidoExameBaixa'] = $data['ItemPedidoExameBaixa'];
				$this->baixa_exames_fc($dadosBaixa, $data['FichaClinica']['codigo_pedido_exame']);
			}

			if (isset($data['FichaClinica']['ficha_digitada'])) {
				if ($data['FichaClinica']['ficha_digitada'] == 1) {
					if (!empty($data['ItemPedidoExame'])) {
						$this->atualizar_item_pedido_exame($data['FichaClinica']['codigo_pedido'], $data['FichaClinica']['codigo_item_exame_aso']);
					}
				}
			}

			self::insereFarmacos($ficha_clinica_resposta, $cid10, $this->getLastInsertID());
			return true;
		} else {
			return false;
		}
	}

	public function editar($data)
	{
		//carrega model
		$this->ItemPedidoExameBaixa = &ClassRegistry::init('ItemPedidoExameBaixa');
		// obtem os ids das questoes antigas para serem excluidas se o salvamento das novas retornar sucesso
		$codigoQuestoesExcluir = $this->FichaClinicaResposta->find(
			'list',
			array(
				'conditions' => array(
					'FichaClinicaResposta.codigo_ficha_clinica' => $data['FichaClinica']['codigo']
				)
			)
		);

		// organiza a variavel para salvar recursivamente
		$ficha_clinica_resposta = $data['FichaClinicaResposta'];
		$codigo_ficha_clinica = $data['FichaClinica']['codigo'];
		if ($retorno = $this->organizaVariavelRecursiva($data)) {
			$data['FichaClinicaResposta'] = $retorno;
		} else {
			unset($data['FichaClinicaResposta']);
		}

		//mata a validação antes de salvar (neste ponto todos os campos já foram validados)

		// salva de forma recursiva
		if (parent::atualizarTodos($data)) {
			$cid10 = $ficha_clinica_resposta['cid10'];
			unset($ficha_clinica_resposta['cid10']);
			unset($ficha_clinica_resposta['campo_livre']);
			unset($ficha_clinica_resposta['parentesco']);
			unset($ficha_clinica_resposta['riscos']);

			self::insereFarmacos($ficha_clinica_resposta, $cid10, $codigo_ficha_clinica);

			if (!empty($data['ItemPedidoExameBaixa'])) {
				//realiza baixa dos exames
				$dadosBaixa['ItemPedidoExameBaixa'] = $data['ItemPedidoExameBaixa'];
				$this->baixa_exames_fc($dadosBaixa, $data['FichaClinica']['codigo_pedido_exame']);
			}

			if ($data['FichaClinica']['ficha_digitada'] == 1) {
				if (!empty($data['ItemPedidoExame'])) {
					$this->atualizar_item_pedido_exame($data['FichaClinica']['codigo_pedido'], $data['FichaClinica']['codigo_item_exame_aso']);
				}
			}


			// se salvamento for sucesso exclua as antigas respostas
			if (!empty($codigoQuestoesExcluir)) {
				if (!$this->FichaClinicaResposta->deleteAll(array('FichaClinicaResposta.codigo' => $codigoQuestoesExcluir))) {
					return false;
				}
			}

			return true;
		} else {
			$this->validate;
			$this->FichaClinicaResposta->validate;

			return false;
		}
	}

	public function jsonRemoveUnicodeSequences($struct)
	{
		return preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", json_encode($struct));
	}

	public function organizaVariavelRecursiva($data)
	{
		// separa os campos livres
		$camposLivres = $data['FichaClinicaResposta']['campo_livre'];
		unset($data['FichaClinicaResposta']['campo_livre']);

		// separa os campos de multiplas doenças
		$cid10 = $data['FichaClinicaResposta']['cid10'];
		unset($data['FichaClinicaResposta']['cid10']);

		$parentescos = $data['FichaClinicaResposta']['parentesco'];
		unset($data['FichaClinicaResposta']['parentesco']);

		$riscos = isset($data['FichaClinicaResposta']['riscos']) ? $data['FichaClinicaResposta']['riscos'] : null;
		unset($data['FichaClinicaResposta']['riscos']);

		// por causa da demanda PC 1115, que nao é preciso preencher a resposta 274 em tela, é preciso trata-la aqui. Entao se ela nao existir, é preciso cria-la, por que é esperado a resposta dela pela regra. E se for o exame que for dado baixa o ASO CLINICO, ele pegue o resultado que for escolhido pelo usuario, 'normal' ou 'alterado' e gravará. Não problema de dar erro, por que a ficha clinica precisa que tenha o exame ASO.
		if (!isset($data['FichaClinicaResposta']['274_resposta'])) {
			$Configuracao = &ClassRegistry::init('Configuracao');
			if (!empty($data['ItemPedidoExameBaixa'][0]['codigo_exame']) && $data['ItemPedidoExameBaixa'][0]['codigo_exame'] == $Configuracao->getChave('INSERE_EXAME_CLINICO')) {
				if ($data['ItemPedidoExameBaixa'][0]['resultado'] == 1) {
					$resultado = 'Normal';
				} else if ($data['ItemPedidoExameBaixa'][0]['resultado'] == 2) {
					$resultado = 'Alterado';
				}
				$data['FichaClinicaResposta']['274_resposta'] = $resultado;
			}
		}

		$c = 0;
		//organiza a variavel para poder salvar recursivamente
		$valores = array();
		foreach ($data['FichaClinicaResposta'] as $key => $resposta) {
			$codigoResposta = substr($key, 0, strpos($key, "_"));

			if ($resposta != '') {
				$valores[$c]['codigo_ficha_clinica_questao'] = $codigoResposta;
				// preenche os "campos livres" do form na variavel
				if (!empty($camposLivres)) {
					$data_campo_livre = (isset($camposLivres[$codigoResposta]) ? $camposLivres[$codigoResposta] : null);
					if (!is_null($data_campo_livre)) {
						$data_campo_livre = (is_array($data_campo_livre) ? $data_campo_livre : ($this->isJson($data_campo_livre) ? json_decode($data_campo_livre, true) : array($data_campo_livre)));

						if (is_array($data_campo_livre)) {
							$fully = false;
							foreach (array_values($data_campo_livre) as $value) {
								if (!empty($value)) {
									$fully = true;
									break;
								}
							}

							if ($fully)
								$valores[$c]['campo_livre'] = json_encode($data_campo_livre);
						}
					}
				}

				// preenche as multiplas doenças
				if (!empty($cid10)) {
					foreach ($cid10 as $key3 => $val) {
						if ($key3 == $codigoResposta) {
							if (!empty($valores[$c]['campo_livre'])) {
								$data_free_field_values = array_values($val);
								$data_free_field_values[] = json_decode($valores[$c]['campo_livre'], true);

								//verificando se todos os campos de uma linha estao vazios
								foreach ($data_free_field_values as $i => $v_empty_data) {
									$fully = false;
									foreach (array_values($v_empty_data) as $value) {
										if (!empty($value)) {
											$fully = true;
											break;
										}
									}

									if (!$fully) {
										unset($data_free_field_values[$i]);
									}
								}
								//FIM verificando se todos os campos de uma linha estao vazios
								if (count($data_free_field_values) > 0) {
									$valores[$c]['campo_livre'] = json_encode(array_values($data_free_field_values));
								}
							} else {
								if (array_key_exists('xx', $val))
									unset($val['xx']);

								if (count(array_values($val)) > 0) {

									//verificando se todos os campos de uma linha estao vazios
									foreach (array_values($val) as $i => $data_value) {
										$fully = false;
										foreach (array_values($data_value) as $value) {
											if (!empty($value)) {
												$fully = true;
												break;
											}
										}
										if (!$fully) {
											unset($val[$i]);
										}
									}
									//FIM verificando se todos os campos de uma linha estao vazios
									$valores[$c]['campo_livre'] = json_encode(array_values($val));
								}
							}
							break;
						} //FINAL IF $key == $codigoResposta
					} //FINAL FOREACH $cid10
				} //FINAL IF NOT EMPTY $cid10

				// preenche os parentescos
				if (!empty($parentescos)) {
					foreach ($parentescos as $key4 => $val) {
						if ($key4 == $codigoResposta) {
							if (!empty($val)) {
								$valores[$c]['parentesco'] = $val;
								break;
							}
						}
					}
				}

				// trata as variaveis covertendo-as em json caso necessario
				if (is_array($resposta)) {
					if (count($resposta) < 2) {
						$valores[$c]['resposta'] = $resposta[key($resposta)];
					} else {
						$valores[$c]['resposta'] = stripslashes(self::jsonRemoveUnicodeSequences($resposta));
					}
				} else {
					$valores[$c]['resposta'] = $resposta;
				}
			}
			$c++;
		} //FINAL FOREACH $data['FichaClinicaResposta']

		if (is_array($riscos)) {
			foreach ($valores as $k => $resp) {
				if (isset($riscos[$resp['codigo_ficha_clinica_questao']])) {
					$RISCOS_RESPOSTA = array();
					foreach ($riscos[$resp['codigo_ficha_clinica_questao']] as $risco) {
						$RISCOS_RESPOSTA[] = $risco;
					}
					$free_field = array();

					if (!empty($resp['campo_livre']))
						$free_field = json_decode($resp['campo_livre']);

					$valores[$k]['campo_livre'] = json_encode(array_merge($free_field, $RISCOS_RESPOSTA));
				}
			}
		}

		//limpa campos para geração do relatorio pdf corretamente
		//verifica se está ativo os itens doenças do coração: 15, problemas respiratorios: 49, doenças nos rins: 61, doenças no figado: 70, doenças psiquiatricas: 126
		$parentes_codigos = array();
		foreach ($valores as $chaveVal => $valArray) {

			//verifica se existe algum desses indices com a resposta 0
			switch ($valArray["codigo_ficha_clinica_questao"]) {
				case '35': //doenças do coração
				case '49': //problemas respiratorios
				case '61': //doenças nos rins
				case '70': //doenças no figado
				case '81': //eplilepsia
				case '109': //doenças do estomago
				case '117': //problemas de visão
				case '122': //problemas de audição
				case '122': //problemas de audição
				case '126': //doenças psiquiatricas
				case '137': //câncer

					$parentes_codigos = array();

					//verifica se a resposta esta como nao=0
					if ($valArray['resposta'] == '0') {
						//pega os filhos/parentes
						$parentes_codigos = $this->FichaClinicaResposta->FichaClinicaQuestao->find('list', array('conditions' => array('FichaClinicaQuestao.codigo_ficha_clinica_questao' => $valArray["codigo_ficha_clinica_questao"])));
					} //fim verificacao da resposta

					break;
			} //fim switch
			//verifica se existe parentes codigos
			if (!empty($parentes_codigos)) {
				//verifica se existe o codigo que esta varrendo dentro do parentes codigos
				if (in_array($valArray["codigo_ficha_clinica_questao"], $parentes_codigos)) {
					//mata a chave inteira
					unset($valores[$chaveVal]);
				}
			}
		} //fim foreach

		if (!empty($valores)) {
			return $valores;
		} else {
			return false;
		}
	}

	protected function insereFarmacos(array $ficha_clinica_resposta, array $cid10, $codigo_ficha_clinica)
	{
		foreach ($ficha_clinica_resposta as $k => $resposta) {
			$codigo_resposta = substr($k, 0, strpos($k, "_"));
			$where = array(
				'FichaClinicaFarmaco.codigo_ficha_clinica' => $codigo_ficha_clinica,
				'FichaClinicaFarmaco.codigo_ficha_clinica_questao' => $codigo_resposta
			);
			$this->FichaClinicaFarmaco->deleteAll($where);

			if ($resposta == '') {
				continue;
			} else {
				foreach ($cid10 as $key3 => $val) {
					if ($key3 == $codigo_resposta) {
						foreach ($val as $farmacos) {
							if (!empty($farmacos['doenca'])) {

								if (empty($farmacos['doenca']) && empty($farmacos['farmaco']) && empty($farmacos['posologia']) && empty($farmacos['aprazamento']) && empty($farmacos['dose_diaria']))
									continue;

								$farmacos['codigo_ficha_clinica'] 			= $codigo_ficha_clinica;
								$farmacos['codigo_ficha_clinica_resposta']  = 0;
								$farmacos['codigo_ficha_clinica_questao'] 	= $codigo_resposta;
								if (!$this->FichaClinicaFarmaco->incluir($farmacos)) {
									debug($farmacos);
								}
							}
						}
						break;
					}
				}
			}
		}
	}

	public function obtemDadosComplementares($codigoPedidoExame)
	{
		$Configuracao = &ClassRegistry::init('Configuracao');
		$codigo_exame_aso = $Configuracao->getChave('INSERE_EXAME_CLINICO');

		if (is_null($codigo_exame_aso))
			throw new Exception("Não existe uma configuração válida para a chave INSERE_EXAME_CLINICO em Administrativo > Cadastro > Configurações de Sistema!");

		$options['conditions'] = array(
			'PedidoExame.codigo' => $codigoPedidoExame
		);

		//esta query obtem todos os medicos disponiveis de todos os fornecedores utilizados no pedido de exame formando um unico grupo
		$medicos = $this->query('
			SELECT Medico.codigo, Medico.nome 
			FROM medicos Medico 
			WHERE Medico.ativo = 1 AND Medico.codigo IN (
			SELECT FornecedorMedico.codigo_medico 
			FROM fornecedores_medicos FornecedorMedico 
			WHERE FornecedorMedico.codigo_fornecedor IN (
			SELECT ItemPedidoExame.codigo_fornecedor 
			FROM itens_pedidos_exames ItemPedidoExame 
			WHERE ItemPedidoExame.codigo_exame = ' . $codigo_exame_aso . ' AND ItemPedidoExame.codigo_pedidos_exames = ' . $codigoPedidoExame . '
			)
			) 
			');

		$values = array();
		foreach ($medicos as $key => $medico) {
			$values[$medico[0]['codigo']] = $medico[0]['nome'];
		}
		//===================================================

		$this->PedidoExame->virtualFields = array(
			'tipo_pedido_exame' => 'CASE 
			WHEN exame_admissional = 1 THEN \'Exame admissional\'
			WHEN exame_periodico = 1 THEN \'Exame pediódico\'
			WHEN exame_demissional = 1 THEN \'Exame demissional\'
			WHEN exame_retorno = 1 THEN \'Retorno\'
			WHEN exame_mudanca = 1 THEN \'Mudança de riscos ocupacionais\'
			WHEN exame_monitoracao = 1 THEN \'Monitoração Pontual\'
			WHEN qualidade_vida = 1 THEN \'Qualidade de vida\'
			END',
			'setor' => "(SELECT descricao FROM RHHealth.dbo.setores where codigo = (SELECT TOP 1 codigo_setor FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo AND PedidoExame.codigo_func_setor_cargo = codigo  ORDER BY 1 DESC))",
			'cargo' => "(SELECT descricao FROM RHHealth.dbo.cargos where codigo = (SELECT TOP 1 codigo_cargo FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo  AND PedidoExame.codigo_func_setor_cargo = codigo ORDER BY 1 DESC))"
		);

		$options['joins'][] = array(
			'table' => 'cliente_funcionario',
			'alias' => 'ClienteFuncionario',
			'type' => 'INNER',
			'conditions' => array(
				'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario'
			)
		);
		$options['joins'][] = array(
			'table' => 'grupos_economicos_clientes',
			'alias' => 'GrupoEconomicoCliente',
			'type' => 'INNER',
			'conditions' => array(
				'GrupoEconomicoCliente.codigo_cliente = ClienteFuncionario.codigo_cliente_matricula'
			)
		);
		$options['joins'][] = array(
			'table' => 'cliente',
			'alias' => 'Unidade',
			'type' => 'INNER',
			'conditions' => array(
				'Unidade.codigo = GrupoEconomicoCliente.codigo_cliente'
			)
		);
		$options['joins'][] = array(
			'table' => 'grupos_economicos',
			'alias' => 'GrupoEconomico',
			'type' => 'INNER',
			'conditions' => array(
				'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
			)
		);
		$options['joins'][] = array(
			'table' => 'cliente',
			'alias' => 'Empresa',
			'type' => 'INNER',
			'conditions' => array(
				'Empresa.codigo = GrupoEconomico.codigo_cliente'
			)
		);
		$options['joins'][] = array(
			'table' => 'funcionarios',
			'alias' => 'Funcionario',
			'type' => 'INNER',
			'conditions' => array(
				'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
			)
		);
		// 		$options['joins'][] = array(
		// 			'table' => 'setores',
		// 			'alias' => 'Setor',
		// 			'type' => 'INNER',
		// 			'conditions' => array(
		// 				'Setor.codigo = ClienteFuncionario.codigo_setor'
		// 				)
		// 			);
		// 		$options['joins'][] = array(
		// 			'table' => 'cargos',
		// 			'alias' => 'Cargo',
		// 			'type' => 'INNER',
		// 			'conditions' => array(
		// 				'Cargo.codigo = ClienteFuncionario.codigo_cargo'
		// 				)
		// 			);

		$options['fields'] = array(
			'PedidoExame.codigo',
			'PedidoExame.tipo_pedido_exame',
			'(SELECT FLOOR(DATEDIFF(DAY, Funcionario.data_nascimento, GETDATE()) / 365.25)) AS idade',
			'(CASE Funcionario.sexo WHEN \'F\' THEN \'Feminino\' ELSE \'Masculino\' END) AS sexo',
			'Funcionario.sexo',
			'Funcionario.nome',
			'Funcionario.cpf',
			'Funcionario.data_nascimento',
			'Funcionario.codigo',
			'ClienteFuncionario.codigo',
			'ClienteFuncionario.codigo_cliente_matricula',
			'ClienteFuncionario.admissao',
			'GrupoEconomicoCliente.codigo',
			'GrupoEconomicoCliente.codigo_cliente',
			'Empresa.razao_social',
			'GrupoEconomico.codigo',
			'GrupoEconomico.codigo_cliente',
			'Unidade.razao_social',
			// 			'Setor.descricao',
			// 			'Cargo.descricao'
			'setor',
			'cargo'
		);

		$dados = $this->PedidoExame->find('first', $options);

		//debug($this->PedidoExame->find('sql', $options));exit;


		$dados['Medico'] = $values;
		unset($values);

		return $dados;
	}

	public function montaRespostas($codigo = null)
	{


		// organiza as respostas em um array no padrão que a view necessita para se relacionar com $this->data
		$respostas = $this->FichaClinicaResposta->find(
			'all',
			array(
				'conditions' => array(
					'FichaClinicaResposta.codigo_ficha_clinica' => $codigo
				)
			)
		);

		$dados = array();
		foreach ($respostas as $key => $value) {
			if ($this->isJson($value['FichaClinicaResposta']['resposta'])) {
				$value['FichaClinicaResposta']['resposta'] = (array)json_decode($value['FichaClinicaResposta']['resposta']);
				if (count($value['FichaClinicaResposta']['resposta']) == 1) {
					$value['FichaClinicaResposta']['resposta'] = $value['FichaClinicaResposta']['resposta'][key($value['FichaClinicaResposta']['resposta'])];
				}
			}
			$dados['FichaClinicaResposta'][$value['FichaClinicaResposta']['codigo_ficha_clinica_questao'] . '_resposta'] = $value['FichaClinicaResposta']['resposta'];

			if (!empty($value['FichaClinicaResposta']['campo_livre'])) {
				if ($this->isJson($value['FichaClinicaResposta']['campo_livre'])) {
					$dados['FichaClinicaResposta']['campo_livre'][$value['FichaClinicaResposta']['codigo_ficha_clinica_questao']] = (array)$this->jsonToArray($value['FichaClinicaResposta']['campo_livre']);
				} else {
					$dados['FichaClinicaResposta']['campo_livre'][$value['FichaClinicaResposta']['codigo_ficha_clinica_questao']] = $value['FichaClinicaResposta']['campo_livre'];
				}
			}

			if (!empty($value['FichaClinicaResposta']['parentesco'])) {

				$dados['FichaClinicaResposta']['parentesco'][$value['FichaClinicaResposta']['codigo_ficha_clinica_questao']] = $value['FichaClinicaResposta']['parentesco'];
			}
		}

		return $dados;
	}

	public function verificaParecer($codigo_pedido_exame = null)
	{
		$return = 0;
		if (!is_null($codigo_pedido_exame)) {
			$query = '
				SELECT
				CASE WHEN 
				(
				SELECT count(pe.codigo) FROM pedidos_exames pe
				INNER JOIN itens_pedidos_exames ipe
				ON (ipe.codigo_pedidos_exames = pe.codigo)
				WHERE pe.codigo = ' . $codigo_pedido_exame . '
				) > ( 
				SELECT count(pe.codigo) FROM pedidos_exames pe
				INNER JOIN itens_pedidos_exames ipe
				ON (ipe.codigo_pedidos_exames = pe.codigo)
				INNER JOIN itens_pedidos_exames_baixa ipeb
				ON (ipeb.codigo_itens_pedidos_exames = ipe.codigo)
				WHERE pe.codigo = ' . $codigo_pedido_exame . '
				)
				THEN 0
				WHEN 
				(
				SELECT count(pe.codigo) FROM pedidos_exames pe
				INNER JOIN itens_pedidos_exames ipe
				ON (ipe.codigo_pedidos_exames = pe.codigo)
				WHERE pe.codigo = ' . $codigo_pedido_exame . '
				) = ( 
				SELECT count(pe.codigo) FROM pedidos_exames pe
				INNER JOIN itens_pedidos_exames ipe
				ON (ipe.codigo_pedidos_exames = pe.codigo)
				INNER JOIN itens_pedidos_exames_baixa ipeb
				ON (ipeb.codigo_itens_pedidos_exames = ipe.codigo)
				WHERE pe.codigo = ' . $codigo_pedido_exame . '
				)
				THEN 1
				END
				AS todos_pedidos_baixados,

				CASE WHEN (SELECT	ri.risco_caracterizado_por_altura
						FROM rhhealth.dbo.pedidos_exames pe
							INNER JOIN rhhealth.dbo.funcionario_setores_cargos fsc ON fsc.codigo = pe.codigo_func_setor_cargo
							INNER JOIN rhhealth.dbo.cliente_funcionario cf ON cf.codigo = fsc.codigo_cliente_funcionario
							INNER JOIN RHHealth.dbo.cliente c ON c.codigo = cf.codigo_cliente_matricula
							INNER JOIN RHHealth.dbo.cargos cg ON cg.codigo = fsc.codigo_cargo
							INNER JOIN RHHealth.dbo.setores st ON st.codigo = fsc.codigo_setor
							/*
							INNER JOIN RHHealth.dbo.clientes_setores cs ON (cs.codigo_setor = st.codigo AND cs.codigo_cliente_alocacao = fsc.codigo_cliente_alocacao)
							INNER JOIN RHHealth.dbo.grupo_exposicao ge  ON (ge.codigo_cargo = cg.codigo AND ge.codigo_cliente_setor = cs.codigo)
							INNER JOIN RHHealth.dbo.grupos_exposicao_risco ger ON (ger.codigo_grupo_exposicao = ge.codigo)
							*/
							INNER JOIN RHHealth.dbo.pedidos_exames_ppra_aso pepa ON pepa.codigo_pedidos_exames = pe.codigo
							INNER JOIN RHHealth.dbo.riscos ri ON (ri.codigo = pepa.codigo_risco)
						WHERE pe.codigo = ' . $codigo_pedido_exame . ' and ri.risco_caracterizado_por_altura is not null and ri.risco_caracterizado_por_altura <> 0
						GROUP BY ri.risco_caracterizado_por_altura) = 1 THEN \'S\' ELSE \'N\' END AS risco_por_altura,
					
				CASE WHEN (SELECT	ri.risco_caracterizado_por_trabalho_confinado 
						FROM rhhealth.dbo.pedidos_exames pe
							INNER JOIN rhhealth.dbo.funcionario_setores_cargos fsc ON fsc.codigo = pe.codigo_func_setor_cargo
							INNER JOIN rhhealth.dbo.cliente_funcionario cf ON cf.codigo = fsc.codigo_cliente_funcionario
							INNER JOIN RHHealth.dbo.cliente c ON c.codigo = cf.codigo_cliente_matricula
							INNER JOIN RHHealth.dbo.cargos cg ON cg.codigo = fsc.codigo_cargo
							INNER JOIN RHHealth.dbo.setores st ON st.codigo = fsc.codigo_setor
							/*
							INNER JOIN RHHealth.dbo.clientes_setores cs ON (cs.codigo_setor = st.codigo AND cs.codigo_cliente_alocacao = fsc.codigo_cliente_alocacao)
							INNER JOIN RHHealth.dbo.grupo_exposicao ge  ON (ge.codigo_cargo = cg.codigo AND ge.codigo_cliente_setor = cs.codigo)
							INNER JOIN RHHealth.dbo.grupos_exposicao_risco ger ON (ger.codigo_grupo_exposicao = ge.codigo)
							*/
							INNER JOIN RHHealth.dbo.pedidos_exames_ppra_aso pepa ON pepa.codigo_pedidos_exames = pe.codigo
							INNER JOIN RHHealth.dbo.riscos ri ON (ri.codigo = pepa.codigo_risco)
						WHERE pe.codigo = ' . $codigo_pedido_exame . ' and ri.risco_caracterizado_por_trabalho_confinado is not null and ri.risco_caracterizado_por_trabalho_confinado <> 0
						GROUP BY ri.risco_caracterizado_por_trabalho_confinado) = 1 then \'S\' ELSE \'N\' END  AS risco_por_confinamento
				';

			$return = $this->query($query);
			$return = $return[0][0];
		}
		return $return;
	}

	private function isJson($json)
	{
		json_decode($json);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	private function jsonToArray($data = null)
	{
		if (!is_null($data)) {
			$json = (array)json_decode($data);
			foreach ($json as $key => $value) {
				if (is_object($value)) {
					$json[$key] = (array)$value;
				} else {
					$json[$key] = $value;
				}
			}
			$data = $json;
		}
		return $data;
	}

	public function criaTabelaTemporaria($codigo_ficha_clinica)
	{
		//verifica se tem usuario logado

		$ficha = $this->find('first', array('conditions' => array('codigo' => $codigo_ficha_clinica)));
		$codigo_usuario = (!empty($ficha['FichaClinica']['codigo_usuario_inclusao'])) ? $ficha['FichaClinica']['codigo_usuario_inclusao'] : 1;

		//EXCLUI DADOS ANTIGOS
		//$this->query('DELETE FROM fichas_clinicas_farmacos WHERE data_inclusao <= \' '.date('Y-m-d H:i:s', strtotime('-1 minutes')) .'\' OR codigo_ficha_clinica = '.$codigo_ficha_clinica );
		$this->query('DELETE FROM fichas_clinicas_farmacos WHERE ((doenca = NULL OR doenca = "") OR codigo_ficha_clinica_resposta != 0) AND codigo_ficha_clinica = ' . $codigo_ficha_clinica);

		// OBTEM OS DADOS PARA SALVAR NA TABELA TEMPORÁRIA, O CONTEUO SERIALIZADO
		// $dados = $this->FichaClinicaResposta->find('all', array(
		// 	'recursive' => -1,
		// 	'joins' => array(
		// 		array(
		// 			'table' => 'fichas_clinicas_questoes',
		// 			'alias' => 'FichaClinicaQuestao',
		// 			'type' => 'INNER',
		// 			'conditions' => array(
		// 				'FichaClinicaQuestao.codigo = FichaClinicaResposta.codigo_ficha_clinica_questao'
		// 				) 
		// 			)
		// 		),
		// 	'conditions' => array(
		// 		'FichaClinicaResposta.codigo_ficha_clinica' => $codigo_ficha_clinica,
		// 		'OR' => array(
		// 			'FichaClinicaResposta.campo_livre NOT' => null,
		// 			'AND' => array(
		// 				array(
		// 					'FichaClinicaResposta.resposta LIKE ? ESCAPE ?' => array('\\[%', '\\')
		// 					),
		// 				array(
		// 					'FichaClinicaResposta.resposta LIKE ? ESCAPE ?' => array('%\\]', '\\')
		// 					)
		// 				)
		// 			)
		// 		),
		// 	'fields' => array(
		// 		'FichaClinicaQuestao.codigo',
		// 		'FichaClinicaResposta.codigo',
		// 		'FichaClinicaResposta.codigo_ficha_clinica',
		// 		'FichaClinicaResposta.campo_livre',
		// 		'FichaClinicaResposta.resposta'
		// 		)
		// 	)
		// );

		$query = "SELECT [FichaClinicaQuestao].[codigo] AS codigo_ficha_clinica_questao
				    , [FichaClinicaResposta].[codigo] AS codigo_ficha_clinica_resposta
				    , [FichaClinicaResposta].[codigo_ficha_clinica] AS codigo_ficha_clinica
				    , (CONVERT(TEXT, [FichaClinicaResposta].[campo_livre])) AS campo_livre
				    , (CONVERT(TEXT, resposta)) AS resposta
				FROM RHHealth.dbo.[fichas_clinicas_respostas] AS [FichaClinicaResposta]
				INNER JOIN [fichas_clinicas_questoes] AS [FichaClinicaQuestao]
				    ON ([FichaClinicaQuestao].[codigo] = [FichaClinicaResposta].[codigo_ficha_clinica_questao])
				WHERE [FichaClinicaResposta].[codigo_ficha_clinica] = {$codigo_ficha_clinica}
				    AND (
				        (NOT ([FichaClinicaResposta].[campo_livre] IS NULL))
				        OR (
				            (
				                (CONVERT(TEXT, resposta) LIKE '\[%' ESCAPE '\')
				                AND (CONVERT(TEXT, resposta) LIKE '%\]' ESCAPE '\')
				                )
				            )
				        );";
		$dados = $this->query($query);

		// debug($dados);exit;

		// MONTA OS DADOS PARA SALVAR NA TABELA TEMPORÁRIA
		$inserir = array();
		foreach ($dados as $key => $dado) {

			// VERIFICA SE O CONTEÚDO É SERIALIZADO
			if (!is_null($dado[0]['campo_livre']) && $this->isJson($dado[0]['campo_livre'])) {
				$jsonToArray = $this->jsonToArray($dado[0]['campo_livre']);

				if (is_int(key($jsonToArray))) { // VERIFICA SE O CONTEÚDO É UM ARRAY COM MULTIPLOS DADOS (FAZ UM LAÇO)
					foreach ($jsonToArray as $key => $value) {

						if (empty($value['doenca']) && empty($value['farmaco']) && empty($value['posologia']) && empty($value['aprazamento']) && empty($value['dose_diaria']))
							continue;

						if (!empty($value['doenca'])) {
							$where = array(
								'FichaClinicaFarmaco.doenca' => $value['doenca'],
								'FichaClinicaFarmaco.codigo_ficha_clinica' => $codigo_ficha_clinica,
								'FichaClinicaFarmaco.codigo_ficha_clinica_questao' => $dado[0]['codigo_ficha_clinica_questao']
							);
							$doenca = $this->FichaClinicaFarmaco->find('first', array('conditions' => $where));
							if (is_array($doenca))
								continue;
						}

						// debug($dado);
						// debug($value);

						$inserir[]['FichaClinicaFarmaco'] = array(
							'codigo_ficha_clinica' 			=> $dado[0]['codigo_ficha_clinica'],
							'codigo_ficha_clinica_resposta' => $dado[0]['codigo_ficha_clinica_resposta'],
							'doenca' 						=> (!empty($value['doenca']) ? $value['doenca'] : null),
							'farmaco'						=> (!empty($value['farmaco']) ? $value['farmaco'] : null),
							'posologia'						=> (!empty($value['posologia']) ? $value['posologia'] : null),
							'dose_diaria'					=> (!empty($value['dose_diaria']) ? $value['dose_diaria'] : null),
							'codigo_ficha_clinica_questao'	=> $dado[0]['codigo_ficha_clinica_questao'],
							'aprazamento'                   => (!empty($value['aprazamento']) ? $value['aprazamento'] : null),
							'parentesco'                    => (!empty($value['parentesco']) ? $value['parentesco'] : null),
							'codigo_usuario_inclusao'		=> $codigo_usuario,
						);
					}
				} else { // SE FOR UM ARRAY COM DADO ÚNICO
					$value = $jsonToArray; //especificando valor

					if (empty($value['farmaco']) && empty($value['posologia']) && empty($value['aprazamento']) && empty($value['dose_diaria']))
						continue;

					$inserir[]['FichaClinicaFarmaco'] = array(
						'codigo_ficha_clinica' 			=> $dado[0]['codigo_ficha_clinica'],
						'codigo_ficha_clinica_resposta' => $dado[0]['codigo_ficha_clinica_resposta'],
						'doenca' 						=> null,
						'farmaco'						=> (!empty($jsonToArray['farmaco']) ? $jsonToArray['farmaco'] : null),
						'posologia'						=> (!empty($jsonToArray['posologia']) ? $jsonToArray['posologia'] : null),
						'dose_diaria'					=> (!empty($jsonToArray['dose_diaria']) ? $jsonToArray['dose_diaria'] : null),
						'codigo_ficha_clinica_questao'	=> $dado[0]['codigo_ficha_clinica_questao'],
						'aprazamento'                   => (!empty($jsonToArray['aprazamento']) ? $jsonToArray['aprazamento'] : null),
						'parentesco'                    => (!empty($jsonToArray['parentesco']) ? $jsonToArray['parentesco'] : null),
						'codigo_usuario_inclusao'		=> $codigo_usuario,
					);
				}
			} elseif (!is_null($dado[0]['resposta']) && $this->isJson($dado[0]['resposta'])) {
				$jsonToArray = $this->jsonToArray($dado[0]['resposta']);

				if (is_int(key($jsonToArray))) { // VERIFICA SE O CONTEÚDO É UM ARRAY COM MULTIPLOS DADOS (FAZ UM LAÇO)
					foreach ($jsonToArray as $key => $value) {
						$inserir[]['FichaClinicaFarmaco'] = array(
							'codigo_ficha_clinica' 			=> $dado[0]['codigo_ficha_clinica'],
							'codigo_ficha_clinica_resposta' => $dado[0]['codigo_ficha_clinica_resposta'],
							'resposta'   					=> $value,
							'codigo_ficha_clinica_questao'	=> $dado[0]['codigo_ficha_clinica_questao'],
							'codigo_usuario_inclusao'		=> $codigo_usuario,
						);
					}
				} else { // SE FOR UM ARRAY COM DADO ÚNICO
					$value = $jsonToArray; //especificando dados da resposta
					$inserir[]['FichaClinicaFarmaco'] = array(
						'codigo_ficha_clinica' 			=> $dado[0]['codigo_ficha_clinica'],
						'codigo_ficha_clinica_resposta' => $dado[0]['codigo_ficha_clinica_resposta'],
						'resposta'						=> $value,
						'codigo_ficha_clinica_questao'	=> $dado[0]['codigo_ficha_clinica_questao'],
						'codigo_usuario_inclusao'		=> $codigo_usuario,
					);
				}
			}
		}

		// debug($inserir);exit;


		// SALVA OS DADOS NA TABELA TEMPORÁRIA
		return $this->FichaClinicaFarmaco->incluirTodos($inserir);
	}

	public function temp_table_riscos($codigo_ficha_clinica)
	{
		$table = "IF OBJECT_ID('RHHealth.dbo.temp_table_riscos', 'U') IS NOT NULL 
                        DELETE FROM RHHealth.dbo.temp_table_riscos WHERE codigo_ficha_clinica = {$codigo_ficha_clinica}
                    ELSE
                    BEGIN
                        CREATE TABLE RHHealth.dbo.temp_table_riscos (
                            codigo_ficha_clinica INT NOT NULL,
                            codigo_ficha_clinica_questao INT NOT NULL,
                            funcao VARCHAR(255) NULL,
                            risco VARCHAR(255) NULL,
                            inicio VARCHAR(255) NULL,
                            termino VARCHAR(255) NULL,
                            risco_outros VARCHAR(1000) NULL
                        );
                    END";
		$opa = $this->query($table);

		// $this->log('create: '.$opa,'debug');
		// $ops = $this->query('select * from RHHealth.dbo.temp_table_riscos');
		// $this->log('select: '.print_r($ops,1),'debug');

		$where = array('FichaClinicaResposta.codigo_ficha_clinica' => $codigo_ficha_clinica, 'FichaClinicaResposta.codigo_ficha_clinica_questao' => 174);
		$data = $this->FichaClinicaResposta->find('first', array('fields' => array('campo_livre'), 'conditions' => $where));

		if (count($data) >= 1) {
			if ($this->isJson($data['FichaClinicaResposta']['campo_livre'])) {
				$riscos = $this->jsonToArray($data['FichaClinicaResposta']['campo_livre']);

				$insert = "";
				if (!empty($riscos)) {
					foreach ($riscos as $risco) {
						$insert .= "INSERT INTO RHHealth.dbo.temp_table_riscos VALUES ({$codigo_ficha_clinica}, 174, '{$risco['funcao']}', '{$risco['risco']}', '{$risco['inicio']}', '{$risco['termino']}', '{$risco['risco_outros']}');";
					}
					$this->query($insert);
				}
			}
		}
	}

	public function get_ficha_clinica_terceiros($conditions)
	{
		//order
		$order = 'FichaClinica.codigo';
		$joins = array(
			array(
				'table' => 'RHHealth.dbo.pedidos_exames',
				'alias' => 'PedidoExame',
				'type' => 'INNER',
				'conditions' => 'PedidoExame.codigo = FichaClinica.codigo_pedido_exame'
			),
			array(
				'table' => 'RHHealth.dbo.cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario'
			),
			array(
				'table' => 'RHHealth.dbo.cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente_matricula'
			),
			array(
				'table' => 'RHHealth.dbo.funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
			),
			array(
				'table' => 'RHHealth.dbo.medicos',
				'alias' => 'Medico',
				'type' => 'INNER',
				'conditions' => 'Medico.codigo = FichaClinica.codigo_medico'
			),
			array(
				'table' => 'RHHealth.dbo.cliente',
				'alias' => 'Unidade',
				'type' => 'INNER',
				'conditions' => 'PedidoExame.codigo_cliente = Unidade.codigo'
			),
		);

		$fields = array(
			'FichaClinica.*',
			'Cliente.razao_social',
			'Unidade.nome_fantasia',
			'RhHealth.publico.ufn_formata_cpf(Funcionario.cpf) as cpf',
			'Funcionario.nome',
			'Funcionario.codigo',
			'Medico.nome',
			'Medico.codigo',
			'PedidoExame.codigo',
		);

		$dados = array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields,
			'order' => $order
		);

		// debug($dados);exit;
		// pr( $this->find('sql',$dados) );exit;

		return $dados;
	}

	public function FiltroEmCondition($data)
	{
		$conditions = array();

		if (!empty($data['codigo_cliente'])) {
			$conditions['ClienteFuncionario.codigo_cliente'] = $data['codigo_cliente'];
		}

		if (!empty($data['codigo_unidade'])) {
			$conditions['PedidoExame.codigo_cliente'] = $data['codigo_unidade'];
		}

		if (!empty($data['codigo']))
			$conditions['FichaClinica.codigo'] = $data['codigo'];

		if (!empty($data['codigo_pedido_exame']))
			$conditions['FichaClinica.codigo_pedido_exame'] = $data['codigo_pedido_exame'];

		if (!empty($data['nome_funcionario']))
			$conditions['Funcionario.nome LIKE'] = '%' . $data['nome_funcionario'] . '%';

		if (!empty($data['nome_medico']))
			$conditions['Medico.nome LIKE'] = '%' . $data['nome_medico'] . '%';

		if (empty($conditions['PedidoExame.codigo_empresa']))
			$conditions['PedidoExame.codigo_empresa'] = $_SESSION['Auth']['Usuario']['codigo_empresa'];

		//seta automaticamente
		if (!isset($data["tipo_periodo"]) && empty($data['tipo_periodo'])) {
			$data["tipo_periodo"] = 'I';
		}

		if (!empty($data["data_inicio"])) {

			$data_inicio = AppModel::dateToDbDate($data["data_inicio"] . ' 00:00:00');
			$data_fim = AppModel::dateToDbDate($data["data_fim"] . ' 23:59:59');

			switch ($data["tipo_periodo"]) {
				case 'I': //data de inclusao
					$conditions[] = "(FichaClinica.data_inclusao >= '" . $data_inicio . "'";
					// $conditions['FichaClinica.data_inclusao >= '] = $data_inicio;	
					break;
				case 'V': //data de validade
					// $conditions['FichaClinica.validade >= '] = $data_inicio;
					$conditions[] = "(FichaClinica.validade >= '" . $data_inicio . "'";
					break;
			} //switch
		} //fim if

		if (!empty($data["data_fim"])) {
			switch ($data["tipo_periodo"]) {
				case 'I': //data de inclusao
					// $conditions['FichaClinica.data_inclusao <= '] = $data_fim;
					$conditions[] = "FichaClinica.data_inclusao <= '" . $data_fim . "')";
					break;
				case 'V': //data de validade
					// $conditions['FichaClinica.validade <= '] = $data_fim;	
					$conditions[] = "FichaClinica.validade <= '" . $data_fim . "')";
					break;
			} //switch
		}

		$conditions[] = "PedidoExame.codigo_status_pedidos_exames <> 5";

		return $conditions;
	}

	public function get_relatorio_fc($filtros = array())
	{

		// ini_set('memory_limit', '4G');
		// set_time_limit(500);

		//variavel vazia pra ser preenchida
		$where = '';

		if (isset($_SESSION['Auth']['Usuario']['codigo_empresa']) && $_SESSION['Auth']['Usuario']['codigo_empresa']) {
			$where .= " pe.codigo_empresa = " . $_SESSION['Auth']['Usuario']['codigo_empresa'];
		}

		if (!empty($filtros['codigo_cliente'])) {
			$where .= " AND cf.codigo_cliente = '{$filtros['codigo_cliente']}' ";
		}

		if (!empty($filtros['codigo_unidade'])) {
			$where .= " AND pe.codigo_cliente = '{$filtros['codigo_unidade']}' ";
		}

		if (!empty($filtros['codigo'])) {
			$where .= " AND fc.codigo = '{$filtros['codigo']}' ";
		}

		if (!empty($filtros['codigo_pedido_exame'])) {
			$where .= " AND fc.codigo_pedido_exame = '{$filtros['codigo_pedido_exame']}' ";
		}

		if (!empty($filtros['nome_funcionario'])) {
			$where .= " AND f.nome like '%{$filtros['nome_funcionario']}%'";
		}

		if (!empty($filtros['nome_medico'])) {
			$where .= " AND m.nome like '%{$filtros['nome_medico']}%'";
		}

		//seta automaticamente
		if (!isset($filtros["tipo_periodo"]) && empty($filtros['tipo_periodo'])) {
			$filtros["tipo_periodo"] = 'I';
		}

		if (!empty($filtros['tipo_periodo']) && $filtros['tipo_periodo'] == 'I') {

			$data_inicio = AppModel::dateToDbDate($filtros["data_inicio"] . ' 00:00:00');
			$data_fim = AppModel::dateToDbDate($filtros["data_fim"] . ' 23:59:59');

			if (!empty($filtros["data_inicio"])) {
				$where .= " AND (fc.data_inclusao >= '{$data_inicio}' ";
			}

			if (!empty($filtros["data_fim"])) {
				$where .= " AND fc.data_inclusao <= '{$data_fim}')";
			}
		}

		$where .= " AND pe.codigo_status_pedidos_exames <> 5"; //somente pedidos exames que nao estao cancelados
		$where .= " AND fc.ficha_digitada = 1"; //somente fichas clinicas digitadas
		$Configuracao = &ClassRegistry::init('Configuracao');
		$sql = "

			select 
				fc.codigo as codigo_ficha_clinica,
				fc.data_inclusao as fc_data_inclusao,
				fc.incluido_por as incluido_por,
				fc.hora_inicio_atendimento as hora_inicio_atendimento ,
				fc.hora_fim_atendimento as hora_fim_atendimento,
				fc.ativo as ativo,
				fc.codigo_pedido_exame as codigo_pedido_exame,
				fc.codigo_medico as codigo_medico,
				fc.pa_sistolica as pa_sistolica,
				fc.pa_diastolica as pa_diastolica,
				fc.pulso as pulso,
				fc.circunferencia_abdominal as circunferencia_abdominal,
				fc.peso_kg as peso_kg,
				fc.peso_gr as peso_gr,
				fc.altura_mt as altura_mt,
				fc.altura_cm as altura_cm,
				fc.circunferencia_quadril as circunferencia_quadril,
				fc.parecer as parecer,
				fc.parecer_altura as parecer_altura,
				fc.parecer_espaco_confinado as parecer_espaco_confinado,
				fc.imc as imc,
				fc.codigo_usuario_inclusao as codigo_usuario_inclusao,
				fc.codigo_usuario_alteracao as codigo_usuario_alteracao,
				fc.data_alteracao as fc_data_alteracao,
				fc.observacao as observacao,
				c.razao_social as razao_social_cliente,
				Unidade.nome_fantasia as nome_fantasia,
				f.cpf as cpf_funcionario,
				f.nome as nome_funcionario,
				f.codigo as codigo_funcionario,
				f.sexo as sexo_funcionario,
				f.data_nascimento as nascimento_funcionario,
				m.nome as medico,
				m.codigo as codigo_medico,

				pe.codigo as codigo_pedido_exame,

				(
					select ipeb.data_inclusao 
					from itens_pedidos_exames ipe
						inner join itens_pedidos_exames_baixa ipeb on ipeb.codigo_itens_pedidos_exames = ipe.codigo
					where ipe.codigo_pedidos_exames = fc.codigo_pedido_exame 
						and ipe.codigo_exame = ".$Configuracao->getChave('INSERE_EXAME_CLINICO')." -- exame aso
				) as data_baixa

				/*,

				fcq.codigo as codigo_questao,
				fcq.label as label_questao,

				fcr.codigo as codigo_resposta,
				fcr.resposta as resposta,
				fcr.campo_livre as campo_livre*/

			from fichas_clinicas fc
				inner join pedidos_exames pe on pe.codigo = fc.codigo_pedido_exame
				inner join cliente_funcionario cf on cf.codigo = pe.codigo_cliente_funcionario
				inner join cliente c on c.codigo = cf.codigo_cliente_matricula
				inner join funcionarios f on f.codigo = cf.codigo_funcionario
				inner join medicos m on m.codigo = fc.codigo_medico
				inner join cliente Unidade on pe.codigo_cliente = Unidade.codigo
				/*inner join fichas_clinicas_respostas fcr on fc.codigo = fcr.codigo_ficha_clinica
				inner join fichas_clinicas_questoes fcq on fcr.codigo_ficha_clinica_questao = fcq.codigo*/
			where
				{$where}
			order by fc.codigo asc
			;
		";

		// debug($sql);exit;

		return $sql;
	}

	/**
	 * [getLabelFichaClinicaQuestoes pega as labels da ficha clinica]
	 * @return [type] [description]
	 */
	public function getLabelFichaClinicaQuestoes()
	{
		$query = "
			SELECT 
				fcq.codigo, 
				(CASE WHEN fcq.label <> '' then fcq.label 
					else 
						(CASE WHEN (ISJSON(fcq.conteudo) > 0) THEN 
							ISNULL(JSON_VALUE(fcq.conteudo,'$.Sim'),JSON_VALUE(fcq.conteudo,'$.subquestion_exibe_outra_alteracao'))
						ELSE fcq2.label 
						END)
				end) as label_questao,
				fcq.codigo_ficha_clinica_questao,
				fcq.exibir_se_sexo


			FROM RHHealth.dbo.fichas_clinicas_questoes fcq 
				LEFT JOIN RHHealth.dbo.fichas_clinicas_questoes fcq2 ON fcq.codigo_ficha_clinica_questao = fcq2.codigo
			WHERE fcq.ativo = 1
			ORDER BY fcq.codigo_ficha_clinica_grupo_questao, fcq.codigo, fcq.codigo_ficha_clinica_questao
		";

		$dados_questoes = $this->query($query);
		// debug($dados_questoes);exit;

		//organiza as questoes
		$dados = array();
		foreach ($dados_questoes as $key => $dq) {
			$questao = $dq[0];
			if (!empty($questao['codigo_ficha_clinica_questao'])) {
				$dados[$questao['codigo_ficha_clinica_questao']][] = $questao;
			} else {
				$dados[$questao['codigo']][] = $questao;
			}
		} //fim foreach que organiza as questoes

		// debug($dados);exit;

		return $dados;
	} //fim getLabelFichaClinicaQuestoes

	/**
	 * [getFichasClinicasRespostas busca as respostas da ficha clinica]
	 * @param  [type] $codigo_ficha_clinica [description]
	 * @return [type]                       [description]
	 */
	public function getFichasClinicasRespostas($codigo_ficha_clinica, $shell = null)
	{
		//pega as respostas
		$query = "SELECT 
					codigo_ficha_clinica_questao, 
					-- RHHealth.dbo.ufn_decode_utf8_string(resposta) as resposta, 
					resposta,
					campo_livre 
				FROM RHHealth.dbo.fichas_clinicas_respostas 
				WHERE codigo_ficha_clinica = {$codigo_ficha_clinica}";
		$dados = $this->query($query);
		$resposta = array();

		if (!empty($dados)) {
			//organiza os dados varre as respostas
			foreach ($dados as $resp) {

				//trabalha nas respostas
				$dado_resposta = utf8_decode($resp[0]['resposta']);

				if ($resp[0]['resposta'] == '1') {
					$dado_resposta = "Sim";
				} else if ($resp[0]['resposta'] == '0') {
					$dado_resposta = utf8_decode("Não");
				}

				$resposta[$resp[0]['codigo_ficha_clinica_questao']] = $dado_resposta;

				if (!empty($resp[0]['campo_livre'])) {

					//codigos que devem ser separados o outros da resposta pois ele esta no campo livre
					switch ($resp[0]['codigo_ficha_clinica_questao']) {
						case '9': //cancer
						case '35': // doenca do coracao
						case '49': // PROBLEMAS RESPIRATÓRIOS ?
						case '61': // doencas nos rins
						case '70': // doencas no figado
						case '109': // doencas no estomago
						case '117': //problemas de visao
						case '122': //problemas de audicao
						case '126': //doencas psiquiatricas
						case '137': //cancer?
						case '143': //alguma doenca nao mencionada
						case '148': //ja sofreu alguma internacao
						case '150': //ja sofreu alguma cirurgia
						case '195': //O EXAMINADO APRESENTA CARACTERÍSTICAS QUE O ENQUADREM NA CONDIÇÃO DE PCD ?
							$resposta[$resp[0]['codigo_ficha_clinica_questao'] . "_outros"] = utf8_decode($resp[0]['campo_livre']);
							break;
						default:
							$resposta[$resp[0]['codigo_ficha_clinica_questao']] .= " - " . utf8_decode($resp[0]['campo_livre']);
							break;
					} //fim switch

				}

				//verifica se tem o valor subquestion_
				switch ($dado_resposta) {
					case 'subquestion_exibe_outra_alteracao':
					case 'subquestion_exibe_multiplas_cids':
						$resposta[$resp[0]['codigo_ficha_clinica_questao']] = "";

						if (!empty($resp[0]['campo_livre'])) {
							$resposta[$resp[0]['codigo_ficha_clinica_questao']] .= utf8_decode($resp[0]['campo_livre']);
						}
						break;
				} //fim switch

			} //fim foreach			
		}

		// debug($resposta);exit;

		return $resposta;
	} //fim getFichasClinicasRespostas()

	public function baixa_exames_fc($data, $codigo_pedido_exame)
	{
		$this->ItemPedidoExame = &ClassRegistry::init('ItemPedidoExame');
		$this->ItemPedidoExameBaixa = &ClassRegistry::init('ItemPedidoExameBaixa');
		$this->PedidoExame = &ClassRegistry::init('PedidoExame');
		$dados = $data;
		$erros = array();

		if (!empty($dados)) {
			foreach ($dados as $info) {
				# code...
				foreach ($info as $dado_pedido) {

					if (!empty($dado_pedido['data_realizacao_exame'])) {
						//verifica se existe a baixa dos exames
						$busca_item_pedido = $this->ItemPedidoExameBaixa->find('first', array('conditions' => array('codigo_itens_pedidos_exames' => $dado_pedido['codigo_itens_pedidos_exames'])));
						//carrega os dados
						$dados_baixa['ItemPedidoExameBaixa'] = array(
							'codigo_itens_pedidos_exames' => $dado_pedido['codigo_itens_pedidos_exames'],
							'resultado'  => $dado_pedido['resultado'],
							'data_realizacao_exame' => $dado_pedido['data_realizacao_exame'],
							'descricao' => empty($dado_pedido['descricao']) ? NULL : $dado_pedido['descricao'],
							'data_validade' => NULL,
							'codigo_aparelho_audiometrico' => NULL,
							'fornecedor_particular' => 0,
							'pedido_importado' => 0,
							'integracao_cliente' => 0,
						);

						//busca o item pedido de exame para compareceu = 1
						$get_item_pedido = $this->ItemPedidoExame->find('first', array('conditions' => array('codigo' => $dado_pedido['codigo_itens_pedidos_exames'])));

						$descricao = trim($dado_pedido['descricao']);
						if ($dado_pedido['resultado'] == 2 && empty($descricao)) {
							$erros['Erro'] = 'Para a inclusão de uma baixa com resultado alterado, é necessária a inclusão de uma descrição da anormalidade.';
						} else {

							//senao existir baixa para o exame, vai ser necessario incluir.
							if (!$busca_item_pedido) {
								if ($this->ItemPedidoExameBaixa->incluir($dados_baixa)) {

									if ($get_item_pedido) {
										//realizada a baixa, deverá ter tido comparecimento igual a 1.
										$get_item_pedido['ItemPedidoExame']['compareceu'] = 1;
										$get_item_pedido['ItemPedidoExame']['data_realizacao_exame'] = $dado_pedido['data_realizacao_exame'];
										if (!$this->ItemPedidoExame->atualizar($get_item_pedido)) {
											$erros['Erro'] = 'Erro ao atualizar o item do pedido de exame.';
										}
									}

									if (!empty($codigo_pedido_exame)) {
										$status = $this->PedidoExame->statusBaixasExames($codigo_pedido_exame);
										$dadosPedido['PedidoExame']['codigo'] = $codigo_pedido_exame;
										$dadosPedido['PedidoExame']['codigo_status_pedidos_exames'] = $status;
										//atualiza status do pedido
										if (!$this->PedidoExame->atualizar($dadosPedido)) {
											$erros['Erro'] = 'Erro ao alterar o status do Pedido.';
										}
									}
								} else {
									$erros['Erro'] = 'Erro ao dar baixa ao exame.';
								}
							} else {
								//inserir o codigo da baixa para poder atualizar a baixa
								$dados_baixa['ItemPedidoExameBaixa']['codigo'] = $busca_item_pedido['ItemPedidoExameBaixa']['codigo'];
								//verifica se tem baixa de pedidos
								if (!empty($dados_baixa['ItemPedidoExameBaixa']['codigo'])) {
									//atualiza a baixa de exames
									if ($this->ItemPedidoExameBaixa->atualizar($dados_baixa)) {
										if ($get_item_pedido) {
											//realizada a baixa, deverá ter tido comparecimento igual a 1.
											$get_item_pedido['ItemPedidoExame']['compareceu'] = 1;
											$get_item_pedido['ItemPedidoExame']['data_realizacao_exame'] = $dado_pedido['data_realizacao_exame'];
											if (!$this->ItemPedidoExame->atualizar($get_item_pedido)) {
												$erros['Erro'] = 'Erro ao atualizar o item do pedido de exame.';
											}
										}
									} else {
										$erros['Erro'] = 'Erro ao atualizar baixa do exame.';
									}
								}
							}
						}
					}
				} // fim dados_pedido
			} // fim foreach info
			if (!empty($erros['Erro'])) {
				return false;
			}
		} // fim dados
	} //fim baixa exames fc

	public function atualizar_item_pedido_exame($codigo_pedido_exame, $data_resultado_exame)
	{

		$this->ItemPedidoExame = &ClassRegistry::init('ItemPedidoExame');
		$Configuracao = &ClassRegistry::init('Configuracao');
		$get_itens_pedido = $this->ItemPedidoExame->find('first', array('conditions' => array('codigo_pedidos_exames' => $codigo_pedido_exame, 'codigo_exame' => $Configuracao->getChave('INSERE_EXAME_CLINICO'))));

		if ($get_itens_pedido) {

			if (!empty($data_resultado_exame)) {
				$get_itens_pedido['ItemPedidoExame']['compareceu'] = 1;
				$get_itens_pedido['ItemPedidoExame']['data_realizacao_exame'] = $data_resultado_exame;
				if (!$this->ItemPedidoExame->atualizar($get_itens_pedido)) {
					return false;
				}
			}
		}
	}
}// fim model
