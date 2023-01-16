<?php
class Funcionario extends AppModel
{

	var $name = 'Funcionario';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'funcionarios';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_funcionarios'));

	const ESTADO_CIVIL_SOLTEIRO = 1;
	const ESTADO_CIVIL_CASADO = 2;
	const ESTADO_CIVIL_SEPARADO = 3;
	const ESTADO_CIVIL_DIVORCIADO = 4;
	const ESTADO_CIVIL_VIUVO = 5;
	const ESTADO_CIVIL_OUTROS = 6;

	public $belongsTo = array(
		'IdentidadesGenero' => array(
			'foreignKey' => 'codigo_identidade_genero'
		)
	);

	var $validate = array(
		'nome' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Nome!',
			'required' => true
		),
		'data_nascimento' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Data de Nascimento!',
			'required' => true
		),
		'cpf' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o CPF!',
				'required' => true
			),
			'documentoValido' => array(
				'rule' => 'documentoValido',
				'message' => 'CPF inválido, verifique!',
				'required' => true
			)
		),
		// 'rg' => array(
		// 	'rule' => 'notEmpty',
		// 	'message' => 'Informe o RG!',
		// 	'required' => true
		// ),
		//       'rg_orgao' => array(
		// 	'rule' => 'notEmpty',
		// 	'message' => 'Informe o Orgão Expedidor!',
		//           'required' => true
		// ),
		'sexo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Sexo!',
			'required' => true
		)
	);


	public $virtualFields = array('qtd_funcionarios' => 'SELECT count(*) from funcionarios
		LEFT JOIN cliente_funcionario on cliente_funcionario.codigo_funcionario = funcionarios.codigo');
	//WHERE cliente_funcionario.codigo_cliente = ' $codigo_cliente);

	function documentoValido()
	{
		$model_documento = &ClassRegistry::init('Documento');
		$codigo_documento = $this->data[$this->name]['cpf'];
		if ($model_documento->isCPF($codigo_documento) == false)
			return false;
		else
			return true;
	}

	function atualizarStatus($dados, $novo_status = 0)
	{

		try {
			$this->query('begin transaction');

			$dados['Funcionario']['status'] = $novo_status;

			if (!parent::atualizar($dados, false, array('codigo', 'status')))
				throw new Exception('Não atualizou status!');

			// confirma transacao!!!
			$this->commit();
			return true;
		} catch (Exception $ex) {
			$this->rollback();
			return false;
		}
	}

	function converteFiltroEmCondition($data)
	{

		$conditions = array();
		if (!empty($data['codigo']))
			$conditions['Funcionario.codigo'] = $data['codigo'];

		if (!empty($data['nome'])) {
			$conditions['Funcionario.nome'] = $data['nome'];
			$conditions['Funcionario.nome_social'] = $data['nome'];
		}


		// if (!empty($data['nome']))
		// 	$conditions['Funcionario.nome'] = array(
		// 		'Funcionario.nome LIKE' => '%' . $data['nome'] . '%',
		// 		'Funcionario.nome_social LIKE' => '%' . $data['nome'] . '%'
		// 	);

		if (!empty($data['rg']))
			$conditions['Funcionario.rg'] = $data['rg'];

		if (!empty($data['cpf']))
			$conditions['Funcionario.cpf'] = Comum::soNumero($data['cpf']);

		if (!empty($data['sexo']))
			$conditions['Funcionario.sexo'] = $data['sexo'];

		if (!empty($data['matricula']))
			$conditions['ClienteFuncionario.matricula'] = $data['matricula'];

		if (!empty($data['codigo_setor']))
			$conditions['FuncionarioSetorCargo.codigo_setor']  = $data['codigo_setor'];

		if (!empty($data['codigo_cargo']))
			$conditions['FuncionarioSetorCargo.codigo_cargo']  = $data['codigo_cargo'];

		if (!empty($data['codigo_unidade']))
			$conditions['FuncionarioSetorCargo.codigo_cliente_alocacao'] = $data['codigo_unidade'];

		if (isset($data['status']) && $data['status'] != '') {
			if ($data['status'] != 'todos') {
				$conditions['ClienteFuncionario.ativo'] = $data['status'];
			}
		}

		if (!empty($data['pre_admissional'])) {
			if ($data['pre_admissional'] == '0') {
				$conditions[] = '(ClienteFuncionario.pre_admissional = ' . $data['pre_admissional'] . ' OR ClienteFuncionario.pre_admissional IS NULL)';
			} else if ($data['pre_admissional'] == '1') {
				$conditions['ClienteFuncionario.pre_admissional'] = $data['pre_admissional'];
			}
		}

		return $conditions;
	}

	public function converteFiltroEmConditionPercapita($data)
	{

		$conditions = $this->converteFiltroEmCondition($data);

		if (!empty($data['codigo_pagador'])) {
			$conditions['ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador)'] = $data['codigo_pagador'];
		}

		if (!empty($data['matricula'])) {
			$conditions['ClienteFuncionario.matricula'] = $data['matricula'];
		}

		if (!empty($data['codigo_matricula'])) {
			$conditions['ClienteFuncionario.codigo'] = $data['codigo_matricula'];
		}

		return $conditions;
	}

	function retornar($codigo)
	{
		return $this->find('first', array(
			'conditions' => array('Funcionario.codigo' => $codigo),
			'fields' => array('*'),
		));
	}

	function listaPorCliente($codigo_cliente, $somente_ativos = 0, $options = array())
	{

		$conditions = array($this->name . '.codigo_cliente' => $codigo_cliente);

		if (isset($options['conditions'])) {
			$conditions += $options['conditions'];
		}

		if ($somente_ativos)
			$conditions[$this->name . '.status'] = 1;

		return $this->find('all', compact('conditions'));
	}

	function lista_por_cliente($codigo_cliente)
	{
		$ClienteFuncionario = &ClassRegistry::Init('ClienteFuncionario');

		$conditions = array('ativo' => 1, 'ClienteFuncionario.codigo_cliente' => $codigo_cliente);
		$fields = array('Funcionario.codigo', 'Funcionario.nome');
		$order = array('Funcionario.nome ASC');
		$joins  = array(
			array(
				'table' => $ClienteFuncionario->databaseTable . '.' . $ClienteFuncionario->tableSchema . '.' . $ClienteFuncionario->useTable,
				'alias' => 'ClienteFuncionario',
				'type' => 'LEFT',
				'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
			)
		);

		$dados = $this->find('list', compact('conditions', 'fields', 'order', 'joins'));

		return $dados;
	}

	function lista_por_cliente_setor_cargo($tipo, $codigo_cliente, $codigo_setor, $codigo_cargo)
	{
		$ClienteFuncionario = &ClassRegistry::Init('ClienteFuncionario');


		if (!empty($codigo_cliente)) {
			$conditions['FuncionarioSetorCargo.codigo_cliente'] = $codigo_cliente;
		}
		if (!empty($codigo_setor)) {
			$conditions['FuncionarioSetorCargo.codigo_setor'] = $codigo_setor;
		}
		if (!empty($codigo_cargo)) {
			$conditions['FuncionarioSetorCargo.codigo_cargo'] = $codigo_cargo;
		}

		$fields = array(
			'Funcionario.codigo', 'Funcionario.nome', 'Funcionario.flg_nome_social', 'Funcionario.nome_social', 'Funcionario.data_nascimento', 'Funcionario.rg', 'Funcionario.rg_orgao', 'Funcionario.cpf',
			'Funcionario.sexo', 'Funcionario.status', 'Funcionario.ctps', 'Funcionario.ctps_data_emissao', 'Funcionario.gfip', 'Funcionario.rg_data_emissao',
			'Funcionario.nit', 'Funcionario.ctps_serie', 'Funcionario.cns', 'Funcionario.ctps_uf',
			'ClienteFuncionario.codigo', 'FuncionarioSetorCargo.codigo_cliente', 'ClienteFuncionario.codigo_funcionario', 'FuncionarioSetorCargo.codigo_setor',
			'FuncionarioSetorCargo.codigo_cargo', 'ClienteFuncionario.admissao', 'ClienteFuncionario.ativo'
		);

		$order = array('Funcionario.nome ASC');
		$joins  = array(
			array(
				'table' => $ClienteFuncionario->databaseTable . '.' . $ClienteFuncionario->tableSchema . '.' . $ClienteFuncionario->useTable,
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
			),
			array(
				'table' => $ClienteFuncionario->FuncionarioSetorCargo->databaseTable . '.' . $ClienteFuncionario->FuncionarioSetorCargo->tableSchema . '.' . $ClienteFuncionario->FuncionarioSetorCargo->useTable,
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => 'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
			)
		);

		$dados = $this->find($tipo, compact('conditions', 'fields', 'order', 'joins'));
		return $dados;
	}

	function valida_campo_importacao($data)
	{
		$retorno = '';

		if ($data['Funcionario']['cpf'] && empty($data['Funcionario']['cpf'])) { //VERIFICA SE O CPF ESTA EM BRANCO
			$retorno['Funcionario']['cpf'] = 'CPF inválido!';
		}

		if ($this->Documento->isCPF($data['Funcionario']['cpf']) == false) { //VERIFICA SE É UM CPF VALIDO
			$retorno['Funcionario']['cpf'] = 'CPF inválido!';
		}

		if (Comum::isDate($data['Funcionario']['data_nascimento']) != 1) {
			$retorno['Funcionario']['data_nascimento'] = 'Data de Nascimento inválida!';
		}

		if (!empty($data['ClienteFuncionario']['admissao'])) {
			if (Comum::isDate($data['ClienteFuncionario']['admissao']) != 1) {
				$retorno['ClienteFuncionario']['admissao'] = 'Data de Admissão inválida!';
			}
		}

		if (!empty($data['ClienteFuncionario']['data_demissao'])) {
			if (Comum::isDate($data['ClienteFuncionario']['data_demissao']) != 1) {
				$retorno['ClienteFuncionario']['data_demissao'] = 'Data de Demissão inválida!';
			}
		}

		if (!empty($data['ClienteFuncionario']['data_ultima_aso'])) {
			if (Comum::isDate($data['ClienteFuncionario']['data_ultima_aso']) != 1) {
				$retorno['ClienteFuncionario']['data_ultima_aso'] = 'Data da Ultima Aso inválida!';
			}
		}

		return $retorno;
	}

	function importacao_funcionario($data)
	{
		$this->ClienteFuncionario 		= &ClassRegistry::init('ClienteFuncionario');
		$this->FuncionarioSetorCargo 	= &ClassRegistry::init('FuncionarioSetorCargo');
		$this->Documento 				= &ClassRegistry::init('Documento');

		$retorno = '';

		$retorno_valida =  $this->valida_campo_importacao($data);

		if (empty($retorno_valida)) {
			$data['Funcionario']['ativo'] = 1;
			$data['Funcionario']['nome'] = strtoupper(comum::trata_nome($data['Funcionario']['nome']));

			if (!isset($data['Funcionario']['codigo']) && empty($data['Funcionario']['codigo'])) {
				if (!parent::incluir($data)) {
					$erro_funcionario = '';
					foreach ($this->validationErrors as $key => $value) {
						$erro_funcionario .= utf8_decode($value) . '|';
						$this->validationErrors[$key] = $erro_funcionario;
					}
					$retorno['Funcionario'] = $this->validationErrors;
				} else {
					$data['ClienteFuncionario']['codigo_funcionario'] = $this->id;

					if (!$this->ClienteFuncionario->incluir($data)) {
						$erro_cliente_funcionario = '';
						foreach ($this->ClienteFuncionario->validationErrors as $key => $value) {
							$erro_cliente_funcionario .= utf8_decode($value) . '|';
							$this->ClienteFuncionario->validationErrors[$key] = $erro_cliente_funcionario;
						}
						$retorno['ClienteFuncionario'] = $this->ClienteFuncionario->validationErrors;
					} else {
						$data['FuncionarioSetorCargo']['codigo_cliente_funcionario'] = $this->ClienteFuncionario->id;
						if (!$this->FuncionarioSetorCargo->incluir($data)) {
							$erro_cliente_funcionario = '';
							foreach ($this->FuncionarioSetorCargo->validationErrors as $key => $value) {
								$erro_funcionario_setor_cargo .= utf8_decode($value) . '|';
								$this->FuncionarioSetorCargo->validationErrors[$key] = $erro_funcionario_setor_cargo;
							}
							$retorno['FuncionarioSetorCargo'] = $this->FuncionarioSetorCargo->validationErrors;
						}
					}
				}
			} else {
				if (!parent::atualizar($data)) {
					$erro_funcionario = '';
					foreach ($this->validationErrors as $key => $value) {
						$erro_funcionario .= utf8_decode($value) . '|';
						$this->validationErrors[$key] = $erro_funcionario;
					}
					$retorno['Funcionario'] = $this->validationErrors;
				} else {
					$data['ClienteFuncionario']['codigo_funcionario'] = $this->id;
					if (!isset($data['ClienteFuncionario']['codigo']) && empty($data['ClienteFuncionario']['codigo'])) {
						if (!$this->ClienteFuncionario->incluir($data)) {
							$erro_cliente_funcionario = '';
							foreach ($this->ClienteFuncionario->validationErrors as $key => $value) {
								$erro_cliente_funcionario .= utf8_decode($value) . '|';
								$this->ClienteFuncionario->validationErrors[$key] = $erro_cliente_funcionario;
							}
							$retorno['ClienteFuncionario'] = $this->ClienteFuncionario->validationErrors;
						}
					} else {
						if (!$this->ClienteFuncionario->atualizar($data)) {
							$erro_cliente_funcionario = '';
							foreach ($this->ClienteFuncionario->validationErrors as $key => $value) {
								$erro_cliente_funcionario .= utf8_decode($value) . '|';
								$this->ClienteFuncionario->validationErrors[$key] = $erro_cliente_funcionario;
							}
							$retorno['ClienteFuncionario'] = $this->ClienteFuncionario->validationErrors;
						} else {
							if (!isset($data['FuncionarioSetorCargo']['codigo']) && empty($data['FuncionarioSetorCargo']['codigo'])) {
								$data['FuncionarioSetorCargo']['codigo_cliente_funcionario'] = $this->ClienteFuncionario->id;
								if (!$this->FuncionarioSetorCargo->incluir($data)) {
									$erro_cliente_funcionario = '';
									foreach ($this->FuncionarioSetorCargo->validationErrors as $key => $value) {
										$erro_funcionario_setor_cargo .= utf8_decode($value) . '|';
										$this->FuncionarioSetorCargo->validationErrors[$key] = $erro_funcionario_setor_cargo;
									}
									$retorno['FuncionarioSetorCargo'] = $this->FuncionarioSetorCargo->validationErrors;
								}
							} else {
								if (!$this->FuncionarioSetorCargo->atualizar($data)) {
									$erro_cliente_funcionario = '';
									foreach ($this->FuncionarioSetorCargo->validationErrors as $key => $value) {
										$erro_funcionario_setor_cargo .= utf8_decode($value) . '|';
										$this->FuncionarioSetorCargo->validationErrors[$key] = $erro_funcionario_setor_cargo;
									}
									$retorno['FuncionarioSetorCargo'] = $this->FuncionarioSetorCargo->validationErrors;
								}
							}
						}
					}
				}
			}
		} else {
			$erro_funcionario = '';
			if (isset($retorno_valida['Funcionario'])) {
				foreach ($retorno_valida['Funcionario'] as $key => $value) {
					$erro_funcionario .= utf8_decode($value) . '|';
					$retorno['Funcionario'][$key] = $erro_funcionario;
				}
			}
		}

		return $retorno;
	}

	public function conta_funcionarios_por_matriz($codigo_cliente_matriz)
	{
		$this->ClienteFuncionario = ClassRegistry::init('ClienteFuncionario');
		$count = $this->ClienteFuncionario->find(
			'first',
			array(
				'joins' => array(
					array(
						'table' => 'funcionarios',
						'alias' => 'Funcionario',
						'type' => 'INNER',
						'conditions' => array(
							'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
						)
					),
					array(
						'table' => 'grupos_economicos_clientes',
						'alias' => 'GrupoEconomicoCliente',
						'type' => 'INNER',
						'conditions' => array(
							'GrupoEconomicoCliente.codigo_cliente = ClienteFuncionario.codigo_cliente'
						)
					),
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
					'GrupoEconomico.codigo_cliente' => $codigo_cliente_matriz
				),
				'fields' => array(
					'count(distinct(Funcionario.codigo)) as count'
				)
			)
		);
		return $count[0]['count'];
	}

	public function obtemDadosPorCodigoClienteFuncionario($codigo_cliente_funcionario = null)
	{
		$this->ClienteFuncionario = ClassRegistry::init('ClienteFuncionario');
		$joins = array(
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array(
					'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
				)
			),
			array(
				'table' => 'grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomicoCliente.codigo_cliente = ClienteFuncionario.codigo_cliente'
				)
			),
			array(
				'table' => 'grupos_economicos',
				'alias' => 'GrupoEconomico',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
				)
			),
			array(
				'table' => 'cliente',
				'alias' => 'Empresa',
				'type' => 'INNER',
				'conditions' => array(
					'Empresa.codigo = GrupoEconomico.codigo_cliente'
				)
			),
			array(
				'table' => 'cliente',
				'alias' => 'Unidade',
				'type' => 'INNER',
				'conditions' => array(
					'Unidade.codigo = GrupoEconomicoCliente.codigo_cliente'
				)
			)
		);
		$fields = array(
			'Funcionario.nome as nome',
			'CONVERT(VARCHAR(10), Funcionario.data_nascimento, 103) as data_nascimento',
			'Funcionario.sexo as sexo',
			'Funcionario.estado_civil as estado_civil',
			'Empresa.nome_fantasia as empresa_nome_fantasia',
			'Unidade.nome_fantasia as unidade_nome_fantasia',
			'(SELECT FLOOR(DATEDIFF(DAY, Funcionario.data_nascimento, GETDATE()) / 365.25)) as idade',
			'(SELECT descricao FROM setores Setor WHERE Setor.codigo = (SELECT TOP 1 codigo_setor FROM funcionario_setores_cargos FuncionarioSetorCargo WHERE FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo AND (FuncionarioSetorCargo.data_fim IS NULL OR FuncionarioSetorCargo.data_fim = "") ORDER BY 1 DESC)) as setor',
			'(SELECT descricao FROM cargos Cargo WHERE Cargo.codigo = (SELECT TOP 1 codigo_cargo FROM funcionario_setores_cargos FuncionarioSetorCargo WHERE FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo AND (FuncionarioSetorCargo.data_fim IS NULL OR FuncionarioSetorCargo.data_fim = "") ORDER BY 1 DESC)) as cargo',
		);
		$funcionario = $this->ClienteFuncionario->find(
			'first',
			array(
				'joins' => $joins,
				'fields' => $fields,
				'conditions' => array(
					'ClienteFuncionario.codigo' => $codigo_cliente_funcionario
				)
			)
		);
		return $funcionario;
	}

	public function listaVisitasAoMedico($codigo_cliente_funcionario = null)
	{
		$this->PedidoExame = ClassRegistry::init('PedidoExame');

		$joins = array(
			array(
				'table' => 'itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => array(
					'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo'
				)
			),
			array(
				'table' => 'itens_pedidos_exames_baixa',
				'alias' => 'ItemPedidoExameBaixa',
				'type' => 'LEFT',
				'conditions' => array(
					'ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo'
				)
			),
			array(
				'table' => 'fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'LEFT',
				'conditions' => array(
					'Fornecedor.codigo = ItemPedidoExame.codigo_fornecedor'
				)
			),
		);

		$fields = array(
			'CONVERT(VARCHAR(10), ItemPedidoExame.data_realizacao_exame, 103) AS data_realizacao_exame',
			'CONVERT(VARCHAR(10), ItemPedidoExameBaixa.data_realizacao_exame, 103) AS data_resultado_exame',
			'Fornecedor.nome AS nome_fornecedor',
			'CASE
			WHEN PedidoExame.exame_admissional > 0 THEN "admissional"
			WHEN PedidoExame.exame_periodico > 0 THEN "periodico"
			WHEN PedidoExame.exame_demissional > 0 THEN "demissional"
			WHEN PedidoExame.exame_retorno > 0 THEN "retorno"
			WHEN PedidoExame.exame_mudanca > 0 THEN "mudança"
			ELSE ""
			END AS tipo_exame',
			'ItemPedidoExameBaixa.data_validade AS data_validade',
			'Fornecedor.responsavel_tecnico AS responsavel_tecnico'
		);

		$visitas = $this->PedidoExame->find(
			'all',
			array(
				'joins' => $joins,
				'fields' => $fields,
				'conditions' => array(
					'PedidoExame.codigo_cliente_funcionario' => $codigo_cliente_funcionario,
					'PedidoExame.codigo_status_pedidos_exames <> 5' // retirar os pedidos cancelados cdct-204
				)
			)
		);

		return $visitas;
	}

	public function listaExamesOcupacionais($codigo_cliente_funcionario = null)
	{
		$this->PedidoExame = ClassRegistry::init('PedidoExame');
		$joins = array(
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => array(
					'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario'
				)
			),
			array(
				'table' => 'aplicacao_exames',
				'alias' => 'AplicacaoExame',
				'type' => 'INNER',
				'conditions' => array(
					'AplicacaoExame.codigo_cargo = (SELECT TOP 1 codigo_cargo FROM funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = PedidoExame.codigo_cliente_funcionario AND (fsc.data_fim IS NULL OR fsc.data_fim = "") ORDER BY 1 DESC)',
					'AplicacaoExame.codigo_setor = (SELECT TOP 1 codigo_setor FROM funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = PedidoExame.codigo_cliente_funcionario AND (fsc.data_fim IS NULL OR fsc.data_fim = "") ORDER BY 1 DESC)',
					'AplicacaoExame.codigo_cliente = ClienteFuncionario.codigo_cliente'
				)
			),
			array(
				'table' => 'itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => array(
					'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo'
				)
			),
			array(
				'table' => 'itens_pedidos_exames_baixa',
				'alias' => 'ItemPedidoExameBaixa',
				'type' => 'LEFT',
				'conditions' => array(
					'ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo'
				)
			),
			array(
				'table' => 'fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'LEFT',
				'conditions' => array(
					'Fornecedor.codigo = ItemPedidoExame.codigo_fornecedor'
				)
			),
			array(
				'table' => 'exames',
				'alias' => 'Exame',
				'type' => 'LEFT',
				'conditions' => array(
					'Exame.codigo = ItemPedidoExame.codigo_exame'
				)
			)
		);
		$fields = array(
			'Exame.descricao AS descricao',
			'Fornecedor.nome AS nome_fornecedor',
			'CONVERT(VARCHAR(10), ItemPedidoExameBaixa.data_realizacao_exame, 103) AS data_realizacao_exame',
			'CONVERT(VARCHAR(10), ItemPedidoExameBaixa.data_validade, 103) AS data_validade',
		);
		$group = array(
			'Exame.descricao',
			'Fornecedor.nome',
			'ItemPedidoExameBaixa.data_realizacao_exame',
			'ItemPedidoExameBaixa.data_validade'
		);
		$exames_ocupacionais = $this->PedidoExame->find(
			'all',
			array(
				'joins' => $joins,
				'fields' => $fields,
				'group' => $group,
				'conditions' => array(
					'ClienteFuncionario.codigo' => $codigo_cliente_funcionario
				)
			)
		);

		return $exames_ocupacionais;
	}

	public function listaPlanosDeSaude($codigo_cliente_funcionario = null)
	{
		$this->ClienteFuncionario = ClassRegistry::init('ClienteFuncionario');
		$joins = array(
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array(
					'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
				)
			),
			array(
				'table' => 'usuarios_dados',
				'alias' => 'UsuariosDados',
				'type' => 'INNER',
				'conditions' => array(
					'UsuariosDados.cpf = Funcionario.cpf'
				)
			),
			array(
				'table' => 'usuarios_planos_saude',
				'alias' => 'UsuarioPlanoSaude',
				'type' => 'INNER',
				'conditions' => array(
					'UsuarioPlanoSaude.codigo_usuario = UsuariosDados.codigo_usuario'
				)
			),
		);
		$fields = array(
			'UsuarioPlanoSaude.descricao',
		);
		$planos_saude = $this->ClienteFuncionario->find(
			'all',
			array(
				'joins' => $joins,
				'fields' => $fields,
				'conditions' => array(
					'ClienteFuncionario.codigo' => $codigo_cliente_funcionario
				)
			)
		);
		return $planos_saude;
	}

	public function listaAtestados($codigo_cliente_funcionario = null)
	{
		$this->Atestado = ClassRegistry::init('Atestado');
		$joins = array(
			array(
				'table' => 'medicos',
				'alias' => 'Medico',
				'type' => 'INNER',
				'conditions' => array(
					'Medico.codigo = Atestado.codigo_medico'
				)
			),
		);
		$fields = array(
			'CONVERT(VARCHAR(10), Atestado.data_afastamento_periodo, 103) AS data_afastamento',
			'Atestado.endereco AS local',
			'Atestado.afastamento_em_horas AS afastamento_em_horas',
			'Medico.nome as nome_medico'
		);
		$group = array(
			'Atestado.data_afastamento_periodo',
			'Atestado.endereco',
			'Atestado.afastamento_em_horas',
			'Medico.nome'
		);
		$atestados = $this->Atestado->find(
			'all',
			array(
				'joins' => $joins,
				'fields' => $fields,
				'conditions' => array(
					'Atestado.ativo' => 1,
					'Atestado.codigo_cliente_funcionario' => $codigo_cliente_funcionario
				)
			)
		);
		return $atestados;
	}

	public function qntQuestPreenchidos($codigo_cliente_funcionario = null)
	{
		$this->Questionario = ClassRegistry::init('Questionario');
		$conditions['UsuarioQuestionario.finalizado'] = 1;
		$conditions['UsuarioQuestionario.concluido !='] = null;
		$conditions['Questionario.status'] = 1;
		$conditions['ClienteFuncionario.codigo'] = $codigo_cliente_funcionario;
		$joins = array(
			array(
				'table' => 'usuarios_questionarios',
				'alias' => 'UsuarioQuestionario',
				'type' => 'INNER',
				'conditions' => array(
					'UsuarioQuestionario.codigo_questionario = Questionario.codigo'
				)
			),
			array(
				'table' => 'usuarios_dados',
				'alias' => 'UsuariosDados',
				'type' => 'INNER',
				'conditions' => array(
					'UsuariosDados.codigo_usuario = UsuarioQuestionario.codigo_usuario'
				)
			),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array(
					'Funcionario.cpf = UsuariosDados.cpf'
				)
			),
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => array(
					'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
				)
			),
		);
		$fields = array(
			'Questionario.codigo'
		);
		$group = array(
			'Questionario.codigo'
		);
		$questionarios_preenchidos = $this->Questionario->find(
			'all',
			array(
				'joins' => $joins,
				'fields' => $fields,
				'group' => $group,
				'conditions' => $conditions
			)
		);

		$qnt_questionarios = $this->Questionario->find('count', array('conditions' => array('status' => 1)));

		$retorno['questionarios_preenchidos'] = count($questionarios_preenchidos);
		$retorno['qnt_questionarios'] = $qnt_questionarios;
		$retorno['percentual_preenchido'] = ceil($retorno['questionarios_preenchidos'] / $retorno['qnt_questionarios'] * 100);
		return $retorno;
	}

	public function obtem_codigo_usuario($codigo_cliente_funcionario = null)
	{
		$funcionario = $this->ClienteFuncionario->find(
			'first',
			array(
				'joins' => array(
					array(
						'table' => 'funcionarios',
						'alias' => 'Funcionario',
						'type' => 'INNER',
						'conditions' => array(
							'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
						)
					),
					array(
						'table' => 'usuarios_dados',
						'alias' => 'UsuariosDados',
						'type' => 'INNER',
						'conditions' => array(
							'UsuariosDados.cpf = Funcionario.cpf'
						)
					)
				),
				'conditions' => array(
					'ClienteFuncionario.codigo' => $codigo_cliente_funcionario
				),
				'fields' => array(
					'UsuariosDados.codigo_usuario'
				)
			)
		);

		return $funcionario['UsuariosDados']['codigo_usuario'];
	}

	// metodo retorna o contato de e-mail mais recente, por codigo do funcionário.
	public function retorna_contato_email_funcionario($codigo_funcionario)
	{
		$options = array(
			'conditions' => array(
				'Funcionario.codigo' => $codigo_funcionario,
				'FuncionarioContato.codigo_tipo_retorno' => 2 //TIPO EMAIL
			),
			'joins' => array(
				array(
					'table' => 'funcionarios_contatos',
					'alias' => 'FuncionarioContato',
					'type' => 'INNER',
					'conditions' => array('Funcionario.codigo = FuncionarioContato.codigo_funcionario')
				),
			),
			'fields' => array(
				'FuncionarioContato.descricao'
			),
			'order' => array(
				'FuncionarioContato.data_inclusao DESC'
			)
		);

		$dados = $this->find('first', $options);

		return $dados;
	}

	public function getFuncionariosF($codigo)
	{

		$this->UsuariosDados = &ClassRegistry::init('UsuariosDados');
		$this->Usuario = &ClassRegistry::init('Usuario');

		$conditions = array('Funcionario.codigo' => $codigo);

		$joins  = array(
			// array(
			// 	'table' => $this->ClienteFuncionario->databaseTable.'.'.$this->ClienteFuncionario->tableSchema.'.'.$this->ClienteFuncionario->useTable,
			// 	'alias' => 'ClienteFuncionario',
			// 	'type' => 'LEFT',
			// 	'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
			// 	),
			array(
				'table' => $this->UsuariosDados->databaseTable . '.' . $this->UsuariosDados->tableSchema . '.' . $this->UsuariosDados->useTable,
				'alias' => 'UsuariosDados',
				'type' => 'LEFT',
				'conditions' => 'Funcionario.cpf = UsuariosDados.cpf'
			),
			array(
				'table' => $this->Usuario->databaseTable . '.' . $this->Usuario->tableSchema . '.' . $this->Usuario->useTable,
				'alias' => 'Usuario',
				'type' => 'LEFT',
				'conditions' => 'UsuariosDados.codigo_usuario = Usuario.codigo'
			),
			array(
				'table' => 'usuario',
				'alias' => 'UsuarioInclusao',
				'type' => 'LEFT',
				'conditions' => 'Funcionario.codigo_usuario_inclusao = UsuarioInclusao.codigo'
			),
			array(
				'table' => 'usuario',
				'alias' => 'UsuarioAlteracao',
				'type' => 'LEFT',
				'conditions' => 'Funcionario.codigo_usuario_alteracao = UsuarioAlteracao.codigo'
			),
		);

		$fields = array(
			'Funcionario.codigo',
			'Funcionario.nome',
			'Funcionario.nome_social',
			'Funcionario.data_nascimento',
			'Funcionario.rg',
			'Funcionario.rg_orgao',
			'Funcionario.cpf',
			'Funcionario.sexo',
			'Funcionario.status',
			'Funcionario.ctps',
			'Funcionario.ctps_data_emissao',
			'Funcionario.gfip',
			'Funcionario.rg_data_emissao',
			'Funcionario.nit',
			'Funcionario.ctps_serie',
			'Funcionario.cns',
			'Funcionario.ctps_uf',
			'Funcionario.estado_civil',
			'Funcionario.deficiencia',
			'Funcionario.flg_nome_social',
			'Funcionario.nome_social',
			'Funcionario.codigo_identidade_genero',
			// 'ClienteFuncionario.codigo',
			// 'ClienteFuncionario.codigo_cliente', 
			// 'ClienteFuncionario.codigo_funcionario', 
			// 'ClienteFuncionario.codigo_setor',
			// 'ClienteFuncionario.codigo_cargo',
			// 'ClienteFuncionario.admissao',
			// 'ClienteFuncionario.data_demissao',
			// 'ClienteFuncionario.matricula',
			// 'ClienteFuncionario.aptidao',
			// 'ClienteFuncionario.ativo',
			'Usuario.codigo',
			'Usuario.apelido',
			'UsuarioInclusao.apelido',
			'Funcionario.data_inclusao',
			'UsuarioAlteracao.apelido',
			'Funcionario.data_alteracao'
		);

		$funcionarios = $this->find('first', array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields));

		return $funcionarios;
	}

	public function buscaDadosFuncionario($conditions)
	{
		$dados = $this->find(
			'first',
			array(
				'joins' => array(
					array(
						'table' => 'cliente_funcionario',
						'alias' => 'ClienteFuncionario',
						'type' => 'INNER',
						'conditions' => array(
							'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
						)
					),
					array(
						'table' => 'cliente',
						'alias' => 'Cliente',
						'type' => 'INNER',
						'conditions' => array(
							'Cliente.codigo = ClienteFuncionario.codigo_cliente'
						)
					),
					array(
						'table' => 'grupos_economicos_clientes',
						'alias' => 'GrupoEconomicoCliente',
						'type' => 'INNER',
						'conditions' => array(
							'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo'
						)
					),
					array(
						'table' => 'grupos_economicos',
						'alias' => 'GrupoEconomico',
						'type' => 'INNER',
						'conditions' => array(
							'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
						)
					),
				),
				'conditions' => $conditions,
				'fields' => array(
					'Funcionario.codigo',
					'Funcionario.nome',
					'Cliente.razao_social'
				)
			)
		);

		return $dados;
	}

	public function getUltimosFuncionarios($codigo_funcionario, $codigo_unidade = null, $import = null)
	{

		$options = array(
			'joins' => array(
				array(
					'table' => 'cliente_funcionario',
					'alias' => 'ClienteFuncionario',
					'type' => 'INNER',
					'conditions' => array('ClienteFuncionario.codigo_funcionario = Funcionario.codigo')
				),
				array(
					'table' => 'funcionario_setores_cargos',
					'alias' => 'FuncionarioSetorCargo',
					'type' => 'INNER',
					'conditions' => array('FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo')
				),
				array(
					'table' => 'setores',
					'alias' => 'Setor',
					'type' => 'INNER',
					'conditions' => array('Setor.codigo = FuncionarioSetorCargo.codigo_setor')
				),
				array(
					'table' => 'cargos',
					'alias' => 'Cargo',
					'type' => 'INNER',
					'conditions' => array('Cargo.codigo = FuncionarioSetorCargo.codigo_cargo')
				),
				array(
					'table' => 'cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => array('Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao')
				),
			),
			'fields' => array(
				'Funcionario.nome as nome_funcionario',
				'Cliente.nome_fantasia as unidade',
				'Setor.descricao as setor',
				'Cargo.descricao as cargo',
				'ClienteFuncionario.matricula as matricula',
			),
			'order' => array(
				'ClienteFuncionario.data_inclusao DESC'
			)
		);

		if (isset($import)) {

			$options['conditions'][] = 'Funcionario.codigo IN(' . $codigo_funcionario . ')';

			$dados = $this->find('all', $options);

			foreach ($dados as $key => $dado_import) {
				$dados[$key] = $dado_import[0];
			}
		} else {

			$options['conditions'] = array(
				'Funcionario.codigo' => $codigo_funcionario,
				'FuncionarioSetorCargo.codigo_cliente_alocacao' => $codigo_unidade
			);

			$dados = $this->find('first', $options);
		}

		return $dados;
	}

	public function alertaInclusaoFuncionario($codigo_cliente, $codigos_funcionario, $codigo_cliente_alocacao)
	{
		//monta o email para ser enviado
		App::import('Component', array('StringView', 'Mailer.Scheduler'));
		$this->Alerta = &ClassRegistry::Init('Alerta');

		$this->StringView = new StringViewComponent();

		//seta os dados para o email
		$this->StringView->reset();

		//monta o host
		$host = Ambiente::getUrl();

		$link = "{$host}/portal/funcionarios/csv_inclusao_funcionarios/import/{$codigos_funcionario}";

		$this->StringView->set('link', $link);
		# Montagem do link #

		$content = $this->StringView->renderMail('email_alerta_inclusao_funcionario', 'default');

		$assunto = "Inclusão de Funcionário";

		//dados para gravar no alerta
		$alerta_dados_funcionario['Alerta'] = array(
			'codigo_cliente' => $codigo_cliente_alocacao,
			'descricao' => $assunto,
			'email_agendados' => '0',
			'sms_agendados' => '0',
			'codigo_alerta_tipo' => '53',
			'descricao_email' => $content,
			'model' => 'Funcionario',
			'foreign_key' => $codigo_cliente,
			'assunto' => $assunto,
		);

		// debug($alerta_dados_funcionario);exit;

		if (!$this->Alerta->incluir($alerta_dados_funcionario)) {
			return false;
		}
	} //fim alertaInclusaoFuncionario
}
