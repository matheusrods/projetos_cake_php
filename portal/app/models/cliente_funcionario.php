<?php

class ClienteFuncionario extends AppModel
{

	public $name = 'ClienteFuncionario';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'cliente_funcionario';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_cliente_funcionario'));
	public $recursive = -1;

	const STATUS_INATIVO = 0;
	const STATUS_ATIVO = 1;
	const STATUS_FERIAS = 2;
	const STATUS_AFASTADO = 3;

	public $hasMany = array(
		'PedidoExame' => array(
			'className'    => 'PedidoExame',
			'foreignKey'    => 'codigo_cliente_funcionario'
		),
		'FuncionarioSetorCargo' => array(
			'classname' => 'FuncionarioSetorCargo',
			'foreignKey' => 'codigo_cliente_funcionario'
		)
	);

	public $belongsTo = array(
		'Cliente' => array(
			'className'    => 'Cliente',
			'foreignKey'    => 'codigo_cliente'
		),
		'Funcionario' => array(
			'className'    => 'Funcionario',
			'foreignKey'    => 'codigo_funcionario'
		),
		'Setor' => array(
			'className'    => 'Setor',
			'foreignKey'    => 'codigo_setor'
		),
		'Cargo' => array(
			'className'    => 'Cargo',
			'foreignKey'    => 'codigo_cargo'
		)
	);

	var $validate = array(
		// 'ativo' => array(
		// 	'rule' => 'notEmpty',
		// 	'message' => 'Informe o Status',
		// 	'required' => true
		// ),
		// 'codigo_cliente' => array(
		// 	'rule' => 'notEmpty',
		// 	'message' => 'Informe o Cliente',
		// 	'required' => true
		// ),
		'codigo_cliente_matricula' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Cliente',
			'required' => true
		),
		'matricula' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a Matrícula',
				'required' => true
			),
			'validaMatriculaUnica' => array(
				'rule' => 'validaMatriculaUnica',
				'message' => 'Matrícula já utilizada por este funcionário',
				'required' => true
			)
		),
		'codigo_funcionario' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Funcionário',
				'required' => true,
			),
		),
		'admissao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Data de Admissão!',
			'required' => true
		),
		'data_demissao' => array(
			'rule' => 'validaDataDemissao',
			'message' => 'Status deve ser igual a Inativo',
			//'required' => true
		),
		'ativo' => array(
			'rule' => 'validaStatus',
			'message' => 'Data de demissão deve ser preenchido',
			//'required' => true
		)
	);
	function validaUnicoFuncionarioAtivo()
	{
		$codigo_funcionario = $this->data[$this->name]['codigo_funcionario'];
		return $this->find('first', array('conditions' => array('codigo_funcionario' => $codigo_funcionario, 'ativo' => '1'))) ? false : true;
	}

	function converteFiltroEmCondition($data)
	{
		$conditions = array();

		if (isset($data['codigo']) && !empty($data['codigo']))
			$conditions['ClienteFuncionario.codigo'] = $data['codigo'];

		if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente']))
			$conditions['FuncionarioSetorCargo.codigo_cliente'] = $data['codigo_cliente'];

		if (isset($data['codigo_funcionario']) && !empty($data['codigo_funcionario']))
			$conditions['ClienteFuncionario.codigo_funcionario'] = $data['codigo_funcionario'];

		if (isset($data['codigo_setor']) && !empty($data['codigo_setor']))
			$conditions['FuncionarioSetorCargo.codigo_setor'] = $data['codigo_setor'];

		if (isset($data['codigo_cargo']) && !empty($data['codigo_cargo']))
			$conditions['FuncionarioSetorCargo.codigo_cargo'] = $data['codigo_cargo'];

		if (isset($data['ativo']) && $data['ativo'] != '') {
			$conditions['ClienteFuncionario.ativo'] = $data['ativo'];
		}

		return $conditions;
	}

	function validaFuncionarioSetorCargo()
	{

		$conditions = array(
			'codigo_cliente' => $this->data['ClienteFuncionario']['codigo_cliente'],
			'codigo_funcionario' => $this->data['ClienteFuncionario']['codigo_funcionario'],
			'codigo_setor' => $this->data['ClienteFuncionario']['codigo_setor'],
			'codigo_cargo' => $this->data['ClienteFuncionario']['codigo_cargo']
		);
		$validar = $this->find('first', array('conditions' => $conditions));

		if (empty($validar)) {
			return true;
		} else {
			$this->invalidate('codigo_cargo', 'Setor e Cargo já cadastrado para este funcionário!');
			return false;
		}
	}

	function lista_por_cliente($codigo_cliente)
	{
		$GrupoEconomico = &ClassRegistry::Init('GrupoEconomico');
		$GrupoEconomicoCliente = &ClassRegistry::Init('GrupoEconomicoCliente');

		$conditions = array('ativo' => 1, 'GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente);
		$fields = array('Setor.codigo', 'Setor.descricao');
		$order = array('Setor.descricao ASC');
		$joins 	= array(
			array(
				'table'	=> $GrupoEconomico->databaseTable . '.' . $GrupoEconomico->tableSchema . '.' . $GrupoEconomico->useTable,
				'alias'	=> 'GrupoEconomico',
				'conditions' => 'GrupoEconomico.codigo_cliente = Setor.codigo_cliente',
			),
			array(
				'table'	=> $GrupoEconomicoCliente->databaseTable . '.' . $GrupoEconomicoCliente->tableSchema . '.' . $GrupoEconomicoCliente->useTable,
				'alias'	=> 'GrupoEconomicoCliente',
				'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
			),
		);

		$dados = $this->find('list', compact('conditions', 'fields', 'order', 'joins'));

		return $dados;
	}

	function localiza_funcionario_importacao($data)
	{
		$this->Funcionario = &ClassRegistry::Init('Funcionario');
		$this->GrupoEconomico = &ClassRegistry::Init('GrupoEconomico');
		$this->GrupoEconomicoCliente = &ClassRegistry::Init('GrupoEconomicoCliente');
		$this->FuncionarioSetorCargo = &ClassRegistry::Init('FuncionarioSetorCargo');

		$retorno = '';

		$codigo_cliente_unidade = $data['codigo_cliente_unidade'];
		$codigo_setor = $data['codigo_setor'];
		$codigo_cargo = $data['codigo_cargo'];
		$cpf_funcionario = $data['cpf_funcionario'];

		if (empty($cpf_funcionario)) {
			$retorno['Erro']['Funcionario'] = array('cpf_funcionario' => utf8_decode('CPF do Funcionário não enviado!!'));
		} else {
			$consulta_dados_funcionario = $this->Funcionario->find("first", array('conditions' => array('Funcionario.cpf' => $cpf_funcionario)));
			if (empty($consulta_dados_funcionario)) {
				$retorno['Erro']['Funcionario'] = array('codigo_funcionario' => utf8_decode('Funcionário não encontrado!'));
			} else {
				$retorno['Dados'] = $consulta_dados_funcionario;

				$conditions = array(
					'FuncionarioSetorCargo.codigo_cliente_alocacao' => $codigo_cliente_unidade,
					'ClienteFuncionario.codigo_funcionario' => $consulta_dados_funcionario['Funcionario']['codigo'],
					'FuncionarioSetorCargo.codigo_setor' => $codigo_setor,
					'FuncionarioSetorCargo.codigo_cargo' => $codigo_cargo,
					'ClienteFuncionario.ativo <> 0'
				);

				$fields = array(
					'ClienteFuncionario.codigo',
					'ClienteFuncionario.codigo_cliente',
					'ClienteFuncionario.codigo_funcionario',
					'FuncionarioSetorCargo.codigo_setor',
					'FuncionarioSetorCargo.codigo_cargo',
					'ClienteFuncionario.admissao',
					'ClienteFuncionario.ativo'
				);

				$joins = array(
					array(
						'table' => 'funcionario_setores_cargos',
						'alias' => 'FuncionarioSetorCargo',
						'type' => 'INNER',
						'conditions' => array(
							'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
							"(FuncionarioSetorCargo.data_fim is null OR FuncionarioSetorCargo.data_fim = '')"
						)
					)
				);

				$dados = $this->find('first', compact('conditions', 'fields', 'joins'));

				if (empty($dados)) {
					$retorno['Erro']['ClienteFuncionario'] = array('codigo_cliente_funcionario' => utf8_decode('Funcionário não encontrado nesta Unidade!'));
				} else {
					$retorno['Dados'] = $dados;
				}
			}
		}
		return $retorno;
	}

	public function paginateCount($conditions = array(), $recursive = 0, $extra = array())
	{
		$extra += array('conditions' => $conditions);
		$extra += array('recursive' => $recursive);

		//pr($extra);
		if (!empty($extra['group']) && in_array('Funcionario.codigo', $extra['group'])) {
			$extra['fields'] = 'Funcionario.codigo';
		}

		//Tratativa para a tela de listtagem de Metas SWT
		if (!empty($extra['group']) && in_array('MetasCustom.codigo', $extra['group'])) {

			$fields = array(
				"Cliente.codigo",
				"Cliente.razao_social",
				"Cliente.nome_fantasia",
				"Setor.codigo",
				"Setor.descricao",
				"ClienteFuncionario.codigo_centro_resultado",
				"ClienteFuncionario.codigo_cliente_bu",
				"ClienteOpco.codigo",
				"ClienteOpco.descricao",
				"ClienteBu.codigo",
				"ClienteBu.descricao",
				"MetasCustom.codigo",
				"MetasCustom.valor",
				"MetasCustom.dia_follow_up",
				"MetasCustom.ativo",
				"MetasPadrao.codigo",
				"MetasPadrao.valor",
				"MetasPadrao.dia_follow_up",
				"MetasPadrao.ativo"
			);

			$joins = array(
				array(
					"table" => "funcionario_setores_cargos",
					"alias" => "fsc",
					"type" => "INNER",
					"conditions" => array("fsc.codigo = (select top 1 codigo
					from funcionario_setores_cargos fsc
					where codigo_cliente_funcionario = ClienteFuncionario.codigo
					and data_fim IS NOT NULL
					order by codigo desc
					)")
				),
				array(
					"table" => "setores",
					"alias" => "Setor",
					"type" => "INNER",
					"conditions" => array("Setor.codigo = fsc.codigo_setor")
				),
				array(
					"table" => "cliente",
					"alias" => "Cliente",
					"type" => "INNER",
					"conditions" => array("ClienteFuncionario.codigo_cliente = Cliente.codigo")
				),
				array(
					"table" => "clientes_setores_cargos",
					"alias" => "ClientesSetoresCargos",
					"type" => "INNER",
					"conditions" => array("ClientesSetoresCargos.codigo_cliente = Cliente.codigo AND Setor.codigo = ClientesSetoresCargos.codigo_setor")
				),
				array(
					"table" => "cliente_bu",
					"alias" => "ClienteBu",
					"type" => "LEFT",
					"conditions" => array("ClienteBu.codigo = ClienteFuncionario.codigo_cliente_bu")
				),
				array(
					"table" => "cliente_opco",
					"alias" => "ClienteOpco",
					"type" => "LEFT",
					"conditions" => array("ClienteOpco.codigo = ClienteFuncionario.codigo_cliente_opco")
				),
				array(
					"table" => "pos_metas",
					"alias" => "MetasCustom",
					"type" => "LEFT",
					"conditions" => array(
						"MetasCustom.codigo_cliente = Cliente.codigo",
						"MetasCustom.codigo_setor = Setor.codigo",
						"MetasCustom.codigo_cliente_opco = ClienteOpco.codigo",
						"MetasCustom.codigo_cliente_bu = ClienteBu.codigo",
						"MetasCustom.codigo_pos_ferramenta = 2"
					)
				),
				array(
					"table" => "pos_metas",
					"alias" => "MetasPadrao",
					"type" => "LEFT",
					"conditions" => array(
						"MetasPadrao.codigo_cliente = Cliente.codigo",
						"MetasPadrao.codigo_setor = Setor.codigo",
						"MetasPadrao.codigo_cliente_opco IS NULL",
						"MetasPadrao.codigo_cliente_bu IS NULL",
						"ClienteFuncionario.codigo_cliente_opco IS NULL",
						"ClienteFuncionario.codigo_cliente_bu IS NULL",
						"MetasCustom.codigo IS NULL",
						"MetasPadrao.codigo_pos_ferramenta = 2"
					)
				)
			);

			$group = array(
				"Cliente.codigo",
				"Cliente.razao_social",
				"Cliente.nome_fantasia",
				"Setor.codigo",
				"Setor.descricao",
				"ClienteFuncionario.codigo_centro_resultado",
				"ClienteFuncionario.codigo_cliente_bu",
				"ClienteOpco.codigo",
				"ClienteOpco.descricao",
				"ClienteBu.codigo",
				"ClienteBu.descricao",
				"MetasCustom.codigo",
				"MetasCustom.valor",
				"MetasCustom.dia_follow_up",
				"MetasCustom.ativo",
				"MetasPadrao.codigo",
				"MetasPadrao.valor",
				"MetasPadrao.dia_follow_up",
				"MetasPadrao.ativo"
			);

			$conditions = $extra['conditions'];

			$count = $this->find('all', array(
				"fields" => $fields,
				"conditions" => $conditions,
				"joins" => $joins,
				"group" => $group
			));

			return count($count);
		}

		if (isset($extra['extra']['clientes_funcionarios_listagem']) && $extra['extra']['clientes_funcionarios_listagem']) {
			return $this->listagem_funcionario('count', compact('conditions', 'recursive', 'joins'));
		}

		$count = $this->find('all', $extra);
		return count($count);
	}

	public function bindModelConsultaAnalitico()
	{
		$joins = array(
			array(
				'table' => 'grupos_economicos',
				'alias' => 'GrupoEconomico',
				'type' => 'INNER',
				'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
			),
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo_cliente_matricula = GrupoEconomicoCliente.codigo_cliente',
			),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
			),
			array(
				'table' => 'funcionario_setores_cargos',
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => array(
					'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
					"FuncionarioSetorCargo.codigo = (SELECT TOP 1 codigo FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY codigo DESC)"
				),
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
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao',
			),
		);


		return $joins;
	}

	public function consultaVidasAnalitico($type, $options = array())
	{
		$this->GrupoEconomicoCliente = &ClassRegistry::Init('GrupoEconomicoCliente');
		$joins = $this->bindModelConsultaAnalitico();
		$fields = array(
			"Funcionario.nome AS nome",
			"Funcionario.cpf AS cpf",
			"Cliente.nome_fantasia AS nome_fantasia",
			"Setor.descricao as descricao",
			"Cargo.descricao as cargo",
			"FuncionarioSetorCargo.codigo_cliente_alocacao as codigo_cliente_alocacao",
			"GrupoEconomico.codigo_cliente as codigo_cliente",
			"GrupoEconomico.descricao AS razao_social",
			"CASE WHEN ClienteFuncionario.ativo > 0 THEN 1 ELSE 0 END as ativo",
			"CASE WHEN ClienteFuncionario.ativo = 0 THEN 1 ELSE 0 END as inativo",
			"CASE WHEN ClienteFuncionario.ativo = 2 THEN 1 ELSE 0 END as ferias",
			"CASE WHEN ClienteFuncionario.ativo = 3 THEN 1 ELSE 0 END as afastado",
		);

		$conditions = $options;
		$recursive = -1;
		return $this->GrupoEconomicoCliente->find($type, compact('fields', 'conditions', 'joins', 'recursive'));
	}

	public function consultaVidassintetico($conditions = array(), $type = null)
	{
		$query_analitica = $this->consultaVidasAnalitico('sql', $conditions);
		$fields = array(
			'nome_fantasia',
			'SUM(ativo) as ativo',
			'SUM(inativo) as inativo',
			'codigo_cliente',
			'codigo_cliente_alocacao',
			'SUM((ativo + inativo)) as total_funcionario',
			'razao_social'
		);
		$group = array(
			'nome_fantasia',
			'codigo_cliente',
			'codigo_cliente_alocacao',
			'razao_social'
		);

		if (!$type == "sql") {
			$order = array('total_funcionario DESC');
		} else {
			$order = null;
		}

		$dbo = $this->getDataSource();
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "({$query_analitica})",
				'alias' => 'analitico',
				'schema' => null,
				'alias' => 'sintetico',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => null,
				'order' => $order,
				'group' => $group
			),
			$this
		);

		if ($type == "sql") {
			return $query;
		} else {
			return $this->query($query);
		}
	}

	function converteFiltroEmConditionConsultaVidas($data)
	{
		$GrupoEconomicoCliente = &ClassRegistry::Init('GrupoEconomicoCliente');
		$conditions = array();

		if (isset($data['codigo']) && !empty($data['codigo']))
			$conditions['ClienteFuncionario.codigo'] = $data['codigo'];

		if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente'])) {
			$dados_grupo_economico = $GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $data['codigo_cliente']), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));

			if (isset($dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'])) {
				$codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];
				$conditions['GrupoEconomicoCliente.codigo_grupo_economico'] = $codigo_grupo_economico;
			} else {
				$conditions['GrupoEconomico.codigo_cliente'] = $data['codigo_cliente'];
			}
		}
		if (isset($data['codigo_cliente_alocacao']) && !empty($data['codigo_cliente_alocacao']) && trim($data['codigo_cliente_alocacao']) != '')
			$conditions['FuncionarioSetorCargo.codigo_cliente_alocacao'] = $data['codigo_cliente_alocacao'];

		if (isset($data['codigo_funcionario']) && !empty($data['codigo_funcionario']) && trim($data['codigo_funcionario']) != '')
			$conditions['ClienteFuncionario.codigo_funcionario'] = $data['codigo_funcionario'];

		if (isset($data['codigo_setor']) && !empty($data['codigo_setor']) && trim($data['codigo_setor']) != '')
			$conditions['FuncionarioSetorCargo.codigo_setor'] = $data['codigo_setor'];

		if (isset($data['codigo_cargo']) && !empty($data['codigo_cargo']) && trim($data['codigo_cargo']) != '')
			$conditions['FuncionarioSetorCargo.codigo_cargo'] = $data['codigo_cargo'];

		if (isset($data['ativo']) && $data['ativo'] != '') {
			if ($data['ativo'] == "1") {
				$conditions['ClienteFuncionario.ativo >'] = "0";
			} else {
				$conditions['ClienteFuncionario.ativo'] = $data['ativo'];
			}
		}

		//filtro para pegar as matriculas com candidatos
		if (!empty($data['candidato'])) {

			//Se a data de início e fim estão vazias, preenche com a data atual
			$data_inicio = !empty($data['data_inicial']) ? AppModel::dateToDbDate($data['data_inicial']) : date('Y-m-d');
			$data_fim = !empty($data['data_final']) ? AppModel::dateToDbDate($data['data_final']) : date('Y-m-d');

			if ($data_inicio > $data_fim) {
				return false;
			}

			$conditions['ClienteFuncionario.matricula_candidato'] = 1;
			$conditions['ClienteFuncionario.data_inclusao >='] = $data_inicio . " 00:00:00";
			$conditions['ClienteFuncionario.data_inclusao <='] = $data_fim . " 23:59:59";
		} //fim candidatos

		return $conditions;
	}

	public function Vidas($conditions = array())
	{
		$query_sintetico = $this->consultaVidassintetico($conditions, 'sql');

		$fields = array(
			'codigo_cliente',
			'SUM(ativo) as total_ativo',
			'SUM(inativo) as total_inativo',
			'SUM((ativo + inativo)) as total_geral',
			'razao_social'
		);
		$group = array(
			'codigo_cliente, razao_social'
		);
		$order = array('total_geral DESC');
		$dbo = $this->getDataSource();
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "({$query_sintetico})",
				'alias' => 'sintetico',
				'schema' => null,
				'alias' => 'vidas',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => null,
				'order' => $order,
				'group' => $group
			),
			$this
		);
		return $this->query($query);
	}

	public function validaDataDemissao()
	{

		$data_demissao 	= $this->data[$this->name]['data_demissao'];
		$status 		= $this->data[$this->name]['ativo'];

		return (!empty($data_demissao) && $status != 0) ? false : true;
	} //FINAL FUNCTION validaDataDemissao

	public function validaStatus()
	{
		if (isset($this->data[$this->name]['data_demissao'])) {
			$data_demissao 	= $this->data[$this->name]['data_demissao'];
			$status 		= $this->data[$this->name]['ativo'];

			return ($status == 0 && empty($data_demissao)) ? false : true;
		}

		return true;
	} //FINAL FUNCTION validaStatus

	/**
	 * [verificaMatriculaVazia função para verificar se existe matricula vazia]
	 * @param  [int] 		$codigo [codigo do cliente funcionario]
	 * @return [boolean]         	[description]
	 */
	public function verificaMatriculaVazia($codigo)
	{

		$joins = array(
			array(
				'table' => "funcionario_setores_cargos",
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'LEFT',
				'conditions' => "{$this->name}.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario",
			),
		);

		$conditions[] = array("ClienteFuncionario.codigo_funcionario" => $codigo);
		$conditions[] = array("FuncionarioSetorCargo.codigo_cliente" => NULL);
		$conditions[] = array("FuncionarioSetorCargo.codigo_setor" => NULL);
		$conditions[] = array("FuncionarioSetorCargo.codigo_cargo" => NULL);
		$conditions[] = array("ClienteFuncionario.ativo" => 1);

		/* Verifica se funcionario codigo_cliente, codigo_setor ou codigo_cargo são igual a NULL*/
		if ($this->find('count', compact('joins', 'conditions')) > 0) {
			return false;
		} else {
			return true;
		}
	} //FINAL function verificaMatriculaVazia

	/**
	 * [convertFiltroSaldoInicial description]
	 * 
	 * metodo para gerar os novos filtros quando o botao for clicado
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function convertFiltroSaldoInicial($conditions_saldo)
	{

		// print "<pre>";print_r($conditions_saldo);exit;

		//pega o mes retrasado
		$base_periodo_2 = strtotime('-2 month', strtotime(date('Y-m-01')));

		$dados_2['mes'] = date('m', $base_periodo_2);
		$dados_2['ano'] = date('Y', $base_periodo_2);

		//seta a data de inicio		
		$dados_2['dt_fim'] = Date('Ymt', $base_periodo_2);

		$conditions_saldo['ClienteFuncionario.data_inclusao <= '] 				= $dados_2['dt_fim'];
		$conditions_saldo['AND']['OR']['ClienteFuncionario.data_inclusao <= '] 	= $dados_2['dt_fim'] . " 23:59:59";

		// unset($conditions_saldo['OR'][1]['ClienteFuncionario.data_demissao > ']);
		$conditions_saldo['ClienteFuncionario.ativo > '] 				= 0;
		unset($conditions_saldo['OR']);
		// $conditions_saldo['OR'][1]['ClienteFuncionario.data_demissao > '] 		= $dados_2['dt_fim'];
		// $conditions_saldo['OR'][2]['MONTH(ClienteFuncionario.data_demissao) '] 	= $dados_2['mes'];
		// $conditions_saldo['OR'][2]['YEAR(ClienteFuncionario.data_demissao) '] 	= $dados_2['ano'];

		// print_r($conditions_saldo);exit;

		// print "<pre>";print_r($conditions_saldo);exit;

		return $conditions_saldo;
	} //fim convertFiltroSaldoInicial


	/**
	 * [convertFiltroInclusosPeriodo description]
	 * 
	 * metodo para gerar os novos filtros quando o botao for clicado
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function convertFiltroInclusosPeriodo($conditions_inclusos, $dados)
	{

		//inclui os filtros
		$conditions_inclusos['MONTH(ClienteFuncionario.data_inclusao)'] = $dados['mes'];
		$conditions_inclusos['YEAR(ClienteFuncionario.data_inclusao)']  = $dados['ano'];
		$conditions_inclusos['ClienteFuncionario.ativo >'] = '0';
		//elimina os filtros
		unset($conditions_inclusos['AND']);
		unset($conditions_inclusos['OR']);

		return $conditions_inclusos;
	} //fim convertFiltroInclusosPeriodo

	/**
	 * [convertFiltroDemitidoPeriodo description]
	 * 
	 * metodo para gerar o filtro corretamente
	 * 
	 * @return [type] [description]
	 */
	public function convertFiltroDemitidoPeriodo($conditions_demitido)
	{

		// print "<pre>";print_r($conditions_demitido);

		//elimina os filtros
		unset($conditions_demitido['AND']);
		unset($conditions_demitido['OR'][0]);
		unset($conditions_demitido['OR'][1]['ClienteFuncionario.data_demissao > ']);
		unset($conditions_demitido['ClienteFuncionario.data_inclusao <= ']);

		$conditions_demitido['AND'] = $conditions_demitido['OR'];
		unset($conditions_demitido['OR']);

		// print_r($conditions_demitido);	exit;

		return $conditions_demitido;
	} //fim convertFiltroDemitidoPeriodo


	/**
	 * [convertFiltroSaldoFinal description]
	 * 
	 * metodo para gerar o filtro quando clicar no botao saldo final
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function convertFiltroSaldoFinal($conditions_saldo)
	{
		// print "<pre>";print_r($conditions_saldo);

		//insere novo filtro
		$conditions_saldo['ClienteFuncionario.ativo >'] = '0';

		unset($conditions_saldo['AND']);
		unset($conditions_saldo['OR']);
		// unset($conditions_saldo['ClienteFuncionario.data_inclusao <= ']);

		return $conditions_saldo;
	} //fim convertFiltroSaldoFinal

	//Método utilizado na inclusão manual de nova matrícula
	//O parâmetro dados é um array com os registros de ClienteFuncionario e FuncionarioSetorCargo
	public function salva_matricula($dados)
	{
		$retorno = 0;

		if (!empty($dados)) {
			$FuncionarioSetorCargo = &ClassRegistry::Init('FuncionarioSetorCargo');

			$this->query('begin transaction');

			if ($matricula = $this->incluir($dados['ClienteFuncionario'])) {

				$dados['FuncionarioSetorCargo'][0]['codigo_cliente_funcionario'] = $this->id;

				//Cadastra regitro de funcionario_setor_cargo e gera hierarquia
				if ($FuncionarioSetorCargo->incluir($dados['FuncionarioSetorCargo'][0])) {
					$retorno = 1;
				}
			}

			if ($retorno == 1) {
				$this->commit();
			} else {
				$this->rollback();
			}
		}
		return $retorno;
	}


	//Valida se o funcionário já possui matrícula com este valor
	public function validaMatriculaUnica()
	{

		$matricula = $this->find("all", array(
			'fields' => array(
				'ClienteFuncionario.*',
				'Funcionario.cpf',
				'Funcionario.nome',
			),
			'joins' => array(
				array(
					'table' => 'funcionarios',
					'alias' => 'Funcionario',
					'type' => 'INNER',
					'conditions' => array(
						'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
					)
				)
			),
			'conditions' => array(
				'codigo_funcionario' => $this->data['ClienteFuncionario']['codigo_funcionario'],
				'matricula' => $this->data['ClienteFuncionario']['matricula'],
				'codigo_cliente_matricula' => $this->data['ClienteFuncionario']['codigo_cliente_matricula']
			),
			'recursive' => -1
		));

		if (!empty($matricula)) {

			$matricula[0]['Funcionario']['cpf'] = AppModel::aplicarMascaraEmCpf($matricula[0]['Funcionario']['cpf']);
			//aplicar máscara em CPF

			$msg = 'Matrícula já em uso pelo funcionário(a) de nome ' . $matricula[0]['Funcionario']['nome'] . ', CPF ' . $matricula[0]['Funcionario']['cpf'];

			//Se existe mais de uma matrícula para este funcionário com esse valor
			if (count($matricula) > 1) {
				return $msg;
			} else {
				//se o código foi passado, então esse registro corresponde a uma atualização
				if (!empty($this->data['ClienteFuncionario']['codigo'])) {
					//Se o código passado é diferente do código encontrado, não corresponde ao mesmo registro
					if ($matricula[0]['ClienteFuncionario']['codigo'] != $this->data['ClienteFuncionario']['codigo']) {
						return $msg;
					}
					//Se o código não foi passado mas já existe matrícula com este valor
				} else {
					return $msg;
				}
			}
			//Nenhuma matrícula com este valor foi encontrada
		}
		return true;
	}


	/**
	 * [paginate description]
	 * 
	 * reescrita do paginate para ter performace da consulta
	 * 
	 * @param  [type]  $conditions [description]
	 * @param  [type]  $fields     [description]
	 * @param  [type]  $order      [description]
	 * @param  [type]  $limit      [description]
	 * @param  integer $page       [description]
	 * @param  [type]  $recursive  [description]
	 * @param  array   $extra      [description]
	 * @return [type]              [description]
	 */
	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array())
	{

		$joins = null;
		if (isset($extra['joins'])) {
			$joins = $extra['joins'];
		}
		if (isset($extra['group'])) {
			$group = $extra['group'];
		}

		if (isset($extra['extra']['clientes_funcionarios_listagem']) && $extra['extra']['clientes_funcionarios_listagem']) {
			return $this->listagem_funcionario('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
		}

		return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
	}


	/**
	 * [listagem_funcionario description]
	 * 
	 * metodo para buscar a listagem para a tela de funcionarios
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function listagem_funcionario($type, $conditions = array())
	{
		// debug($conditions);

		//query para executar
		$query = "SELECT
				  [Funcionario].[codigo] AS codigo_funcionario,
				  [Funcionario].[nome],
					[Funcionario].[nome_social],
					[Funcionario].[flg_nome_social],
				  [Funcionario].[data_nascimento],
				  [Funcionario].[rg],
				  [Funcionario].[rg_orgao],
				  [Funcionario].[cpf],
				  [Funcionario].[sexo],
				  [Cliente].[codigo] AS codigo_cliente,
				  [Cliente].[nome_fantasia],
				  [ClienteFuncionario].[admissao],
				  [ClienteFuncionario].[codigo_cliente_matricula],
				  [ClienteFuncionario].[sem_matricula],
				  [ClienteFuncionario].[pre_admissional],
				  [ClienteFuncionario].[ativo],
				  [ClienteFuncionario].[matricula],
				  [FuncionarioSetorCargo].[codigo_setor],
				  [FuncionarioSetorCargo].[codigo_cargo],
				  [FuncionarioSetorCargo].[codigo_cliente_alocacao]
				FROM RHHealth.dbo.[cliente_funcionario] AS [ClienteFuncionario]
					INNER JOIN [funcionarios] AS [Funcionario] ON ([Funcionario].[codigo] = [ClienteFuncionario].[codigo_funcionario])
					LEFT JOIN [funcionario_setores_cargos] AS [FuncionarioSetorCargo] ON ([FuncionarioSetorCargo].[codigo_cliente_funcionario] = [ClienteFuncionario].[codigo]
					  AND [FuncionarioSetorCargo].[codigo] = (SELECT TOP 1
																	codigo
																  FROM [RHHealth].[dbo].funcionario_setores_cargos
																  WHERE codigo_cliente_funcionario = [ClienteFuncionario].[codigo]
																  ORDER BY codigo DESC)
																  )
					LEFT JOIN [cliente] AS [Cliente] ON ([Cliente].[codigo] = [FuncionarioSetorCargo].[codigo_cliente_alocacao])";

		$offset = (isset($conditions['page']) && $conditions['page'] > 1 ? (($conditions['page'] - 1) * $conditions['limit']) : null);

		$dbo = $this->getDataSource();
		$query_exec = $dbo->buildStatement(
			array(
				'fields' => array('*'),
				'table' => "({$query})",
				'alias' => 'ClienteFuncionario',
				'schema' => null,
				'limit' => (isset($conditions['limit']) ? $conditions['limit'] : null),
				'offset' => $offset,
				'joins' => null,
				'conditions' => $conditions['conditions'],
				'order' => (isset($conditions['order']) ? $conditions['order'] : null),
				'group' => null
			),
			$this
		);


		// pr($query_exec);

		//executa e devolve para a tela
		if ($type == 'count') {
			$result = $this->query($query_exec);
			return count($result);
		} else {
			return $this->query($query_exec);
		}
	} //fim listagem_funcionario


	public function getAllFuncionarios($conditions, $order, $pagination = false)
	{

		$fields = array(
			'DISTINCT Funcionario.codigo',
			'Funcionario.nome',
			'Funcionario.data_nascimento',
			'Funcionario.rg',
			'Funcionario.rg_orgao',
			'Funcionario.cpf',
			'Funcionario.sexo',
			'Cliente.codigo',
			'Cliente.nome_fantasia',
			'(SELECT top 1 cf.admissao from cliente_funcionario cf where cf.codigo_funcionario = Funcionario.codigo and cf.ativo = 1 order by cf.data_inclusao desc) as admissao',
			'(select top 1 cf.codigo_cliente_matricula from cliente_funcionario cf where cf.codigo_funcionario = Funcionario.codigo and cf.ativo = 1 order by cf.data_inclusao desc) as codigo_cliente_matricula'
		);

		$joins = array(
			array(
				'table' => 'RHHealth.dbo.funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
			),
			array(
				'table' => 'RHHealth.dbo.funcionario_setores_cargos',
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'LEFT',
				'conditions' => 'FuncionarioSetorCargo.codigo = (SELECT TOP 1 codigo FROM [RHHealth].[dbo].funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY codigo DESC)',
			),
			array(
				'table' => 'RHHealth.dbo.grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => 'GrupoEconomicoCliente.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao',
			),
			array(
				'table' => 'RHHealth.dbo.grupos_economicos',
				'alias' => 'GrupoEconomico',
				'type' => 'INNER',
				'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
			),
			array(
				'table' => 'RHHealth.dbo.cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao',
			),
		);

		if ($pagination) {
			$paginate = array(
				'fields' => $fields,
				'joins' => $joins,
				'conditions' => $conditions,
				'limit' => 20,
				'order' => $order
			);
			return $paginate;
		} else {
			return $this->find('sql', array('joins' => $joins, 'fields' => $fields, 'conditions' => $conditions, 'order' => $order));
		}
	}

	// CDCT-653
	public function clientesAtivosEQuantidadeVidas(){

		$sql = "
		SELECT DISTINCT clie.codigo, clie.nome_fantasia AS nome_fantasia, clie.razao_social AS razao_social,
				COUNT(clieFunc.codigo_cliente) AS vidas
		FROM cliente_funcionario AS clieFunc
		INNER JOIN cliente  AS clie
		ON clieFunc.codigo_cliente = clie.codigo
		WHERE clieFunc.ativo > 0 And clie.ativo > 0
		group by clie.codigo, clieFunc.codigo_cliente, clie.nome_fantasia, clie.razao_social
		order by clie.codigo Desc;
		";

		$query = $this->query($sql);
		return $query;
	}

	function exportar_clientesAtivosEQuantidadeVidas(){ 
        
            
			//para pegar todos os funcionarios até demitidos deve ser passar GrupoEconomico->queryEstrutura($codigo_cliente, false);
            $linhasExportaVidas = $this->clientesAtivosEQuantidadeVidas();

            $nome_arquivo = date('YmdHis') . 'ex.csv';
		

             //headers
            ob_clean();
            header('Content-Encoding: ISO-8859-1');
            // header('Content-type: text/csv; charset=ISO-8859-1');
			header("Content-Type: application/force-download;charset=ISO-8859-1");
            header(sprintf('Content-Disposition: attachment; filename="%s"', $nome_arquivo));
            // header('Pragma: no-cache');

            echo utf8_decode('"Código Unidade";"Nome Fantasia";"Razão Social";"Nº Vidas"'."\n");

            foreach ($linhasExportaVidas as $indiceLinha => $dado) {                
				// debug($dado[0]);                              

                $linha = $dado[0]['codigo'].';';
                $linha .= '"'.$dado[0]['nome_fantasia'].'";';
                $linha .= '"'.trim($dado[0]['razao_social']).'";';
                $linha .= trim($dado[0]['vidas']);
                

                $linha .= PHP_EOL;
                echo utf8_decode($linha);
                //echo ($linha);
            }
        
        // exit;
    }//FINAL FUNCTION exportar_clientesAtivosEQuantidadeVidas

}//FINAL CLASS ClienteFuncionario