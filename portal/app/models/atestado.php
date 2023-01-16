<?php
App::import('model', 'TipoContato');
class Atestado extends AppModel {

	public $name		   	= 'Atestado';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'atestados';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_atestado'));

	public $validate = array(
		// 'codigo_medico' => array(
		// 	'rule' => 'notEmpty',
		// 	'message' => 'Informe o Médico!'
		// 	), //nao é mais obrigatorio por causa do afastamento
		'codigo_cliente_funcionario' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Funcionario / Cliente!'
			),
		'codigo_motivo_licenca' => array(
			'rule' => 'notEmpty',
			'message' => 'Informar o Motivo da Licença'
			),
		'data_afastamento_periodo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informar o Data de Inicio do Afastamento'
			),
		'data_retorno_periodo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informar a Data de Retorno'
			)			
		);

	const AGRP_UNIDADE = 1;
	const AGRP_SETOR = 2;
	const AGRP_CARGO = 3;
	const AGRP_CID = 4;
	const AGRP_MEDICO = 5;

	public function analitico($type, $options) {
		$this->bindModel(array(
			'belongsTo' => array(
				'TipoLocalAtendimento' => array('foreignKey' => 'codigo_tipo_local_atendimento'),
				'ClienteFuncionario' => array('foreignKey' => 'codigo_cliente_funcionario', 'conditions' => array('Atestado.data_afastamento_periodo BETWEEN ClienteFuncionario.admissao AND COALESCE(ClienteFuncionario.data_demissao, GETDATE())')),
				'Funcionario' => array('foreignKey' => false, 'conditions' => array('Funcionario.codigo = ClienteFuncionario.codigo_funcionario')),
				'FuncionarioEndereco' => array('foreignKey' => false, 'conditions' => array('FuncionarioEndereco.codigo_funcionario = Funcionario.codigo', 'FuncionarioEndereco.codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL)),
				'FuncionarioSetorCargo' => array('foreignKey' => false, 'conditions' => array('FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo', 'Atestado.data_afastamento_periodo BETWEEN FuncionarioSetorCargo.data_inicio AND COALESCE(FuncionarioSetorCargo.data_fim, GETDATE())')),
				'Setor' => array('foreignKey' => false, 'conditions' => array('Setor.codigo = FuncionarioSetorCargo.codigo_setor')),
				'Cargo' => array('foreignKey' => false, 'conditions' => array('Cargo.codigo = FuncionarioSetorCargo.codigo_cargo')),
				'Unidade' => array('className' => 'Cliente', 'foreignKey' => false, 'conditions' => array('Unidade.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao')),
				'CnaeUnidade' => array('className' => 'Cnae', 'foreignKey' => false, 'conditions' => array('CnaeUnidade.cnae = Unidade.cnae')),
				'UnidadeEndereco' => array('className' => 'ClienteEndereco', 'foreignKey' => false, 'conditions' => array('UnidadeEndereco.codigo_cliente = Unidade.codigo', 'UnidadeEndereco.codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL)),
				'VUnidadeEndereco' => array('className' => 'VEndereco', 'foreignKey' => false, 'conditions' => array('VUnidadeEndereco.endereco_codigo = UnidadeEndereco.codigo_endereco')),
				'GrupoEconomicoCliente' => array('foreignKey' => false, 'conditions' => array('GrupoEconomicoCliente.codigo_cliente = Unidade.codigo')),
				'GrupoEconomico' => array('foreignKey' => false, 'conditions' => array('GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico')),
				'Cliente' => array('foreignKey' => false, 'conditions' => array('Cliente.codigo = GrupoEconomico.codigo_cliente')),
				'Medico' => array('foreignKey' => 'codigo_medico'),
				'MotivoAfastamento' => array('foreignKey' => 'codigo_motivo_licenca'),
				'Esocial' => array('foreignKey' => false, 'conditions' => array('Esocial.codigo = Atestado.codigo_motivo_esocial', 'Esocial.tabela' => 18)),
			),
			'hasOne' => array(
				'AtestadoCid' => array('foreignKey' => 'codigo_atestado'),
				'Cid' => array('foreignKey' => false, 'conditions' => array('Cid.codigo = AtestadoCid.codigo_cid')),
				'CidCnae' => array('foreignKey' => false, 'conditions' => array('CidCnae.codigo_cid = Cid.codigo', 'CidCnae.codigo_cnae = CnaeUnidade.codigo')),
			),
		));
		$fields = array(
			"Atestado.afastamento_em_dias AS atestado_afastamento_em_dias",
			"Atestado.afastamento_em_horas AS atestado_afastamento_em_horas",
			"Atestado.cep AS atestado_cep",
			"Atestado.codigo AS atestado_codigo",
			"CONVERT(VARCHAR, Atestado.data_afastamento_periodo, 120) AS atestado_afastamento_periodo",
			"CONVERT(VARCHAR, Atestado.data_inclusao, 120) AS atestado_data_inclusao",
			"CONVERT(VARCHAR, Atestado.data_retorno_periodo, 120) AS atestado_data_retorno_periodo",
			"Atestado.endereco AS atestado_endereco",
			"CONVERT(VARCHAR, Atestado.hora_afastamento, 108)  AS atestado_hora_afastamento",
			"CONVERT(VARCHAR, Atestado.hora_retorno, 108)  AS atestado_hora_retorno",
			"Atestado.restricao AS atestado_restricao",
			"Cargo.codigo AS cargo_codigo",
			"Cargo.descricao AS cargo_descricao",
			"Cid.codigo AS cid_codigo",
			"Cid.codigo_cid10 AS cid_codigo_cid10",
			"Cid.descricao AS cid_descricao",
			"Unidade.cnae AS unidade_cnae",
			"ClienteFuncionario.matricula AS cliente_funcionario_matricula",
			"Cliente.razao_social AS cliente_razao_social",
			"CnaeUnidade.descricao AS cnae_unidade_descricao",
			"DATEPART(weekday,Atestado.data_afastamento_periodo) AS dia_semana",
			"(DATEDIFF(day,Atestado.data_afastamento_periodo, Atestado.data_retorno_periodo) + 1) AS dias_afastado",
			"RHHealth.publico.distancia_dois_pontos(Atestado.latitude,Atestado.longitude,FuncionarioEndereco.latitude,FuncionarioEndereco.longitude) AS distancia_funcionario",
			"RHHealth.publico.distancia_dois_pontos(Atestado.latitude,Atestado.longitude,UnidadeEndereco.latitude,UnidadeEndereco.longitude) AS distancia_unidade",
			"Esocial.descricao AS esocial_descricao",
			"Funcionario.cpf AS funcionario_cpf",
			"FuncionarioEndereco.logradouro AS funcionario_endereco",
			"FuncionarioEndereco.complemento AS funcionario_end_complemento",
			"FuncionarioEndereco.numero AS funcionario_endereco_numero",
			"Funcionario.nome AS funcionario_nome",
			"Funcionario.rg AS funcionario_rg",
			"Medico.codigo AS medico_codigo",
			// "Medico.conselho_uf AS medico_conselho_uf",
			"CASE WHEN Atestado.sem_profissional = 1 THEN ''
			ELSE Medico.conselho_uf END AS medico_conselho_uf",
			// "Medico.nome AS medico_nome",
			"CASE WHEN Atestado.sem_profissional = 1 THEN ''
			ELSE Medico.nome END AS medico_nome",
			// "Medico.numero_conselho AS medico_numero_conselho",
			"CASE WHEN Atestado.sem_profissional = 1 THEN ''
			ELSE Medico.numero_conselho END AS medico_numero_conselho",
			"DATEDIFF(minute,Atestado.hora_afastamento, Atestado.hora_retorno) AS minutos_afastado",
			"MotivoAfastamento.descricao AS motivo_afastamento_descricao",
			"CASE WHEN CidCnae.codigo IS NOT NULL THEN 'S' ELSE 'N' END AS nexo",
			"Setor.codigo AS setor_codigo",
			"Setor.descricao AS setor_descricao",
			"TipoLocalAtendimento.descricao AS tipo_local_atend_descricao",
			"Unidade.codigo AS unidade_codigo",
			"(CASE WHEN Unidade.codigo_documento_real IS NULL THEN Unidade.codigo_documento WHEN Unidade.codigo_documento_real = '' THEN Unidade.codigo_documento ELSE Unidade.codigo_documento_real END) AS unidade_codigo_documento",
			"VUnidadeEndereco.endereco_tipo + ' ' + VUnidadeEndereco.endereco_logradouro AS unidade_endereco",
			"UnidadeEndereco.complemento AS unidade_endereco_complemento",
			"UnidadeEndereco.numero AS unidade_endereco_numero",
			"Unidade.nome_fantasia AS unidade_nome_fantasia",
			"CASE WHEN Atestado.tipo_atestado = 1 OR Atestado.tipo_atestado IS NULL THEN 'Atestado Médico'
			WHEN Atestado.tipo_atestado = 2 THEN 'Afastamento' END AS tipo_atestado",
			"CASE WHEN Atestado.motivo_afastamento = 'S' THEN 'Sim'
			WHEN Atestado.motivo_afastamento = 'N' THEN 'Não' END AS motivo_afastamento",			
			"CASE 
				WHEN Atestado.tipo_acidente_transito = 1 THEN 'Atropelamento'
				WHEN Atestado.tipo_acidente_transito = 2 THEN 'Colisão'
				WHEN Atestado.tipo_acidente_transito = 3 THEN 'Outros'
			END AS tipo_acidente_transito",
			"Atestado.obs_afastamento as observacao",
			"CASE 
				WHEN Atestado.onus_requisicao = 1 THEN 'Ônus do Cedente'
				WHEN Atestado.onus_requisicao = 2 THEN 'Ônus do Cessionário'
				WHEN Atestado.onus_requisicao = 3 THEN 'Ônus do Cedente e Cessionário'
			END AS onus_requisicao",
			"CASE 
				WHEN Atestado.onus_remuneracao = 1 THEN 'Apenas do Empregador'
				WHEN Atestado.onus_remuneracao = 2 THEN 'Apenas do Sindicato'
				WHEN Atestado.onus_remuneracao = 3 THEN 'Parte do Empregador, sendo a diferença e/ou complementação salarial paga pelo Sindicato'
			END AS onus_remuneracao",
			"CASE 
				WHEN Atestado.renumeracao_cargo = 0 OR Atestado.renumeracao_cargo IS NULL THEN 'Não'
				WHEN Atestado.renumeracao_cargo = 1 THEN 'Sim' 
			END AS renumeracao_cargo",
			"Atestado.codigo_documento_entidade as cnpj",
			"CASE 
				WHEN Atestado.origem_retificacao = 1 THEN 'Por iniciativa do empregador'
				WHEN Atestado.origem_retificacao = 2 THEN 'Revisão Administrativa'
				WHEN Atestado.origem_retificacao = 3 THEN 'Determinação Judicial'
			END AS origem_retificacao",
			"CASE 
				WHEN Atestado.tipo_processo = 1 THEN 'Administrativo'
				WHEN Atestado.tipo_processo = 2 THEN 'Judicial'
				WHEN Atestado.tipo_processo = 3 THEN 'Número de Benefício (NB) do INSS'
			END AS tipo_processo",
			"Atestado.numero_processo as numero_processo",
		);
		$conditions = $options['conditions'];		
		return $this->find($type, compact('fields', 'conditions'));
	}

	public function tiposAgrupamento() {
		return array(
			self::AGRP_UNIDADE => "por Unidade",
			self::AGRP_SETOR => "por Setor",
			self::AGRP_CARGO => "por Cargo",
			self::AGRP_CID => "por CID",
			self::AGRP_MEDICO => "por Médico",
		);
	}

	public function sintetico($agrupamento, $conditions) {
		$query_analitica = $this->analitico('sql', compact('conditions'));
		
		switch ($agrupamento) {
			case self::AGRP_UNIDADE:
				$fields = array(
					'unidade_codigo AS codigo',
					'unidade_nome_fantasia AS descricao',
					'COUNT(DISTINCT atestado_codigo) AS quantidade',
				);
				$group = array(
					'unidade_codigo',
					'unidade_nome_fantasia',
				);
				break;
			case self::AGRP_SETOR:
				$fields = array(
					'setor_codigo AS codigo',
					'setor_descricao AS descricao',
					'COUNT(DISTINCT atestado_codigo) AS quantidade',
				);
				$group = array(
					'setor_codigo',
					'setor_descricao',
				);
				break;
			case self::AGRP_CARGO:
				$fields = array(
					'cargo_codigo AS codigo',
					'cargo_descricao AS descricao',
					'COUNT(DISTINCT atestado_codigo) AS quantidade',
				);
				$group = array(
					'cargo_codigo',
					'cargo_descricao',
				);
				break;
			case self::AGRP_CID:
				$fields = array(
					'cid_codigo AS codigo',
					'cid_descricao AS descricao',
					'COUNT(DISTINCT atestado_codigo) AS quantidade',
				);
				$group = array(
					'cid_codigo',
					'cid_descricao',
				);
				break;
			case self::AGRP_MEDICO:
				$fields = array(
					'medico_codigo AS codigo',
					'medico_nome AS descricao',
					'COUNT(DISTINCT atestado_codigo) AS quantidade',
				);
				$group = array(
					'medico_codigo',
					'medico_nome',
				);
				break;
		}
		$order = array('COUNT(DISTINCT atestado_codigo) DESC');
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
				), $this
			);

		return $this->query($query);
	}


	function converteFiltrosEmConditions($data) {
		$conditions = array();

		if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente'])) {
			$GrupoEconomicoCliente =& ClassRegistry::init('GrupoEconomicoCliente');
			$codigo_cliente_principal = $GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $data['codigo_cliente'])));
			$codigo_cliente_principal = $codigo_cliente_principal['GrupoEconomico']['codigo_cliente'];
			$conditions['Cliente.codigo'] = $codigo_cliente_principal;
		}
	
		if (isset($data['codigo_cliente_alocacao']) && !empty($data['codigo_cliente_alocacao']))
			$conditions['FuncionarioSetorCargo.codigo_cliente_alocacao'] = $data['codigo_cliente_alocacao'] == -1 ? null : $data['codigo_cliente_alocacao'];
	
		if (isset($data['codigo_funcionario']) && !empty($data['codigo_funcionario']))
			$conditions['ClienteFuncionario.codigo_funcionario'] = $data['codigo_funcionario'] == -1 ? null : $data['codigo_funcionario'];
	
		if (isset($data['codigo_setor']) && !empty($data['codigo_setor']))
			$conditions['FuncionarioSetorCargo.codigo_setor'] = $data['codigo_setor'] == -1 ? null : $data['codigo_setor'];
	
		if (isset($data['codigo_cargo']) && !empty($data['codigo_cargo']))
			$conditions['FuncionarioSetorCargo.codigo_cargo'] = $data['codigo_cargo'] == -1 ? null : $data['codigo_cargo'];

		if (isset($data['codigo_cid']) && !empty($data['codigo_cid']))
			$conditions['AtestadoCid.codigo_cid'] = $data['codigo_cid'] == -1 ? null : $data['codigo_cid'];

		if (isset($data['descricao_cid']) && !empty($data['descricao_cid']))
			$conditions['Cid.descricao LIKE'] = '%'.$data['descricao_cid'].'%';

		if (isset($data['codigo_medico']) && !empty($data['codigo_medico']))
			$conditions['codigo_medico'] = $data['codigo_medico'] == -1 ? null : $data['codigo_medico'];

		if (isset($data['data_inicio']) && !empty($data['data_inicio'])) {
			if($data['tipo_periodo'] == 'A'){
				$tipo = 'Atestado.data_afastamento_periodo';
			} elseif($data['tipo_periodo'] == 'R') {
				$tipo = 'Atestado.data_retorno_periodo';
			} else{
				$tipo = 'Atestado.data_inclusao';
			}
			$conditions[$tipo.' >='] = AppModel::dateToDbDate($data['data_inicio']);
		}

		if (isset($data['data_fim']) && !empty($data['data_fim'])) {
			if($data['tipo_periodo'] == 'A'){
				$tipo = 'Atestado.data_afastamento_periodo';
			} elseif($data['tipo_periodo'] == 'R') {
				$tipo = 'Atestado.data_retorno_periodo';
			} else{
				$tipo = 'Atestado.data_inclusao';
			}
			$conditions[$tipo.' <='] = AppModel::dateToDbDate($data['data_fim']);
		}

		if(isset($data['ativo'])) {
			$conditions['Atestado.ativo'] = $data['ativo'];
		}
		else {
			$conditions['Atestado.ativo'] = 1;
		}

		if (isset($data['tipo_atestado']) && !empty($data['tipo_atestado'])){			
			if($data['tipo_atestado'] == '2') {
				$conditions ['Atestado.tipo_atestado'] = $data['tipo_atestado'];
			} else if ($data['tipo_atestado'] == '1'){
				$conditions[] = '(Atestado.tipo_atestado = '.$data['tipo_atestado'].' OR Atestado.tipo_atestado IS NULL)';
			}
		}
		return $conditions;
	}

	function subquery_converteFiltroEmCondition($data, $conditions = "") {
	
		if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente']))
			$conditions .= " AND CF.codigo_cliente = " . $data['codigo_cliente'];
	
		if (isset($data['codigo_funcionario']) && !empty($data['codigo_funcionario']))
			$conditions .= " AND CF.codigo_funcionario = " . $data['codigo_funcionario'];
	
		if (isset($data['codigo_setor']) && !empty($data['codigo_setor']))
			$conditions .= " AND FSC.codigo_setor = " . $data['codigo_setor'];
	
		if (isset($data['codigo_cargo']) && !empty($data['codigo_cargo']))
			$conditions .= " AND FSC.codigo_cargo = " . $data['codigo_cargo'];


		return $conditions;
	}
	
	
	public function retornaEstrutura($cliente_funcionario) {

		if(is_numeric($cliente_funcionario)) {
			$model_ClienteFuncionario = & ClassRegistry::init('ClienteFuncionario');

			$options['fields'] = array(
				'Cliente.codigo',
				'Cliente.razao_social',
				'Cliente.nome_fantasia',
				'Funcionario.nome',
				// 'Cargo.codigo',
				// 'Cargo.descricao',
				// 'Setor.codigo',
				// 'Setor.descricao',
				'setor', 
				'cargo'
				);

			$options['joins'] = array(
				array(
					'table' => 'cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente',
					),				
				array(
					'table' => 'funcionarios',
					'alias' => 'Funcionario',
					'type' => 'INNER',
					'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
					),
				// array(
				// 		'table' => 'cargos',
				// 		'alias' => 'Cargo',
				// 		'type' => 'INNER',
				// 		'conditions' => 'Cargo.codigo = ClienteFuncionario.codigo_cargo',
				// ),
				// array(
				// 		'table' => 'setores',
				// 		'alias' => 'Setor',
				// 		'type' => 'INNER',
				// 		'conditions' => 'Setor.codigo = ClienteFuncionario.codigo_setor',
				// )
				);

			$options['conditions'] = array("ClienteFuncionario.codigo = {$cliente_funcionario}");
			
			$model_ClienteFuncionario->virtualFields = array(
				'setor' => "(SELECT descricao FROM RHHealth.dbo.setores where codigo = (SELECT TOP 1 codigo_setor FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo AND (data_fim = '' OR data_fim IS NULL )  ORDER BY 1 DESC))",
				'cargo' => "(SELECT descricao FROM RHHealth.dbo.cargos where codigo = (SELECT TOP 1 codigo_cargo FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo  AND (data_fim = '' OR data_fim IS NULL ) ORDER BY 1 DESC))"
				);			

			return $model_ClienteFuncionario->find('first', $options);
		} else {
			return false;
		}

	}//FINAL FUNCTION retornaEstrutura

	
	function incluir($dados) {
		
		try {

			$this->query('begin transaction');
			
			/*** tratamento data_afastamento_periodo & data_retorno_periodo por que na feedback o behavior nao esta funcionando corretamente */

			if($dados['Atestado']['data_afastamento_periodo'] != ''){
				$dados['Atestado']['data_afastamento_periodo'] = AppModel::dateToDbDate2($dados['Atestado']['data_afastamento_periodo']);
			}

			if($dados['Atestado']['data_retorno_periodo'] != ''){
				$dados['Atestado']['data_retorno_periodo'] = AppModel::dateToDbDate2($dados['Atestado']['data_retorno_periodo']);
			}

			/*** fim tratamento */

			if(strpos($dados['Atestado']['data_afastamento_periodo'],"/")) {
				$data_afastamento = (int) (substr(str_replace("/", "", $dados['Atestado']['data_afastamento_periodo']), 4, 4) . substr(str_replace("/", "", $dados['Atestado']['data_afastamento_periodo']), 2, 2) . substr(str_replace("/", "", $dados['Atestado']['data_afastamento_periodo']), 0, 2));
			}
			else if(strpos($dados['Atestado']['data_afastamento_periodo'],"-")) {
				$data_afastamento = (int) (substr(str_replace("-", "", $dados['Atestado']['data_afastamento_periodo']), 0, 4) . substr(str_replace("-", "", $dados['Atestado']['data_afastamento_periodo']), 4, 2) . substr(str_replace("-", "", $dados['Atestado']['data_afastamento_periodo']), 6, 2));
			}

			if(strpos($dados['Atestado']['data_retorno_periodo'],"/")) {
				$data_retorno = (int) (substr(str_replace("/", "", $dados['Atestado']['data_retorno_periodo']), 4, 4) . substr(str_replace("/", "", $dados['Atestado']['data_retorno_periodo']), 2, 2) . substr(str_replace("/", "", $dados['Atestado']['data_retorno_periodo']), 0, 2));
			}
			else if(strpos($dados['Atestado']['data_retorno_periodo'],"-")) {
				$data_retorno = (int) (substr(str_replace("-", "", $dados['Atestado']['data_retorno_periodo']), 0, 4) . substr(str_replace("-", "", $dados['Atestado']['data_retorno_periodo']), 4, 2) . substr(str_replace("-", "", $dados['Atestado']['data_retorno_periodo']), 6, 2));				
			}			

			if($dados['Atestado']['habilita_afastamento_em_horas']) {
				if(empty($dados['Atestado']['hora_afastamento'])) {					
					$this->validationErrors = array_merge($this->validationErrors, array('hora_afastamento' => 'Hora inicial obrigatória'));
				}
				if(empty($dados['Atestado']['hora_retorno'])) {				
					$this->validationErrors = array_merge($this->validationErrors, array('hora_retorno' => 'Hora final obrigatória'));
				}
			} else if($data_afastamento > $data_retorno) {				
				$this->validationErrors = array_merge($this->validationErrors, array('data_retorno_periodo' => 'Data deve ser (maior ou igual) a Data de Afastamento'));
			}

			if(empty($dados['Atestado']['codigo_motivo_licenca'])){				
				$this->validationErrors = array_merge($this->validationErrors, array('codigo_motivo_licenca' => 'Informar o Motivo da Licença'));
			}

			if(!count($this->validationErrors)) {				
				if (!parent::incluir($dados['Atestado'])){					
					throw new Exception();
				}				
			} else {
				return false;
			}

			$this->commit();
			return true;
		} catch (Exception $ex) {
			$this->rollback();
			return false;
		}
	}
	
	
	public function atualizar($dados) {

		//die( debug($dados) );

		if (!isset($dados['Atestado']['codigo']) || empty($dados['Atestado']['codigo']))
			return false;
		
		try {
			$this->query('begin transaction');
			
			$data_afastamento = (int) (substr(str_replace("/", "", $dados['Atestado']['data_afastamento_periodo']), 4, 4) . substr(str_replace("/", "", $dados['Atestado']['data_afastamento_periodo']), 2, 2) . substr(str_replace("/", "", $dados['Atestado']['data_afastamento_periodo']), 0, 2));
			$data_retorno = (int) (substr(str_replace("/", "", $dados['Atestado']['data_retorno_periodo']), 4, 4) . substr(str_replace("/", "", $dados['Atestado']['data_retorno_periodo']), 2, 2) . substr(str_replace("/", "", $dados['Atestado']['data_retorno_periodo']), 0, 2));

			if($dados['Atestado']['habilita_afastamento_em_horas']) {
				if(empty($dados['Atestado']['hora_afastamento'])) {
					$this->validationErrors = array_merge($this->validationErrors, array('hora_afastamento' => 'Hora inicial obrigatória'));
				}
				if(empty($dados['Atestado']['hora_retorno'])) {
					$this->validationErrors = array_merge($this->validationErrors, array('hora_retorno' => 'Hora final obrigatória'));
				}
			} 
			
			if($data_afastamento > $data_retorno) {
				$this->validationErrors = array_merge($this->validationErrors, array('data_retorno_periodo' => 'Data deve ser (maior ou igual) a Data de Afastamento'));
			}
			
			if(!count($this->validationErrors)) {
				
				//debug($dados);
				
				if (!parent::atualizar($dados))
					throw new Exception('Não atualizou fornecedor');
				
				$this->commit();
				return true;

			} else {
				return false;
			}
			
		} catch (Exception $ex) {
			$this->rollback();
			//die( debug( $ex->getMessage() ) );
			return false;
		}
		
	}

	public function paginateCount($conditions = null, $recursive = -1, $extra = array(), $fields = array()) {
		$extra['conditions'] = $conditions;
		$extra['recursive'] = $recursive;
		$extra['fields'] = array($this->name.'.codigo');
		$extra['group'] = array($this->name.'.codigo');
		return count($this->find('all', $extra));
	}


	/**
	 * beforeSave callback
	 *
	 * @param $options array
	 * @return boolean
	 */
	// public function beforeSave($options) {
	// 	function geraTimestamp($data) {
	// 		$partes = explode('-', $data);
	// 		return mktime(0, 0, 0, $partes[1], $partes[2], $partes[0]);
	// 	}
	// 	$time_inicial = geraTimestamp(date('Y-m-d', strtotime($this->data[$this->name]['data_afastamento_periodo'])));
	// 	if(((int)array_shift(explode(':', $this->data[$this->name]['hora_afastamento'])) == 0) !=  0) {
	// 		$time_inicial = geraTimestamp(date('Y-m-d', strtotime($this->data[$this->name]['data_afastamento_periodo'])));
	// 	}
	// 	$time_final = geraTimestamp(date('Y-m-d', strtotime($this->data[$this->name]['data_retorno_periodo'])));
	// 	$diferenca = ($time_final - $time_inicial);
	// 	$hora_afastamento =  (int)array_shift(explode(':', $this->data[$this->name]['hora_afastamento']));
	// 	$hora_retorno = (int)array_shift(explode(':', $this->data[$this->name]['hora_retorno']));
	// 	$this->data[$this->name]['afastamento_em_horas'] = ((int)floor( $diferenca / (60 * 60 * 24)) * 24) - $hora_afastamento + $hora_retorno;
	// 	return true;
	// }

	function excluir($codigo) {

		$atestados = array(
			'Atestado' => array('codigo' => $codigo, 'ativo' => 0)
		);

		if(!parent::atualizar($atestados)) {
			return false;
		}

		return true;

		// return $this->delete($codigo);
	}

	public function getAtestado($codigo){

		$conditions_am 	= array('Atestado.codigo' => $codigo);

		$retorno = $this->find('first', array('conditions' => $conditions_am) );

		return Set::extract('Atestado', $retorno);
	}//FINAL fUNCTION getAtestado


	/**
	 * [notificacao_atestado_sem_cid description]
	 * 
	 * metodo para registrar na alerta caso o atestado nao tenha cid
	 * 
	 * @param  [type] $codigo_atestado [description]
	 * @return [type]                  [description]
	 */
	public function notificacao_atestado_sem_cid($codigo_atestado)
    {
     	$this->AtestadoCid =& ClassRegistry::init('AtestadoCid');

        //verifica se o atestado criado deverá ser notificado
        //pega o valor do atestado
        $atestado = $this->AtestadoCid->find('first', array('conditions' => array('AtestadoCid.codigo_atestado' => $codigo_atestado)));

        //verifica os usuarios que devem ser notificados.
        if(count($atestado['AtestadoCid']) < 1) {

            //dados do cliente, funcionario
            $join = array(
                array(
                    'table' => 'Rhhealth.dbo.cliente_funcionario',
                    'alias' => 'ClienteFuncionario',
                    'type' => 'INNER',
                    'conditions' => 'ClienteFuncionario.codigo = Atestado.codigo_cliente_funcionario'
                ),
                array(
                    'table' => 'Rhhealth.dbo.funcionarios',
                    'alias' => 'Funcionario',
                    'type' => 'INNER',
                    'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
                ),
                array(
                    'table' => 'Rhhealth.dbo.funcionario_setores_cargos',
                    'alias' => 'FuncionarioSetorCargo',
                    'type' => 'INNER',
                    'conditions' => 'FuncionarioSetorCargo.codigo = Atestado.codigo_func_setor_cargo'
                ),
                array(
                    'table' => 'Rhhealth.dbo.cliente',
                    'alias' => 'Cliente',
                    'type' => 'INNER',
                    'conditions' => 'FuncionarioSetorCargo.codigo_cliente_alocacao = Cliente.codigo'
                ),
            );

            $dados = $this->find('first', array('fields' => array('Atestado.codigo','Cliente.codigo','Cliente.razao_social', 'Funcionario.nome'), 'joins' => $join, 'conditions' => array('Atestado.codigo' => $codigo_atestado)));

            // pr($dados);exit;

            App::import('Component', array('StringView'));

            $this->StringView = new StringViewComponent();
            $this->StringView->set('dados', $dados);
            $content = $this->StringView->renderMail('email_atestados_sem_CID');
                    
            $alerta = array(
                'Alerta' => array(
                    'codigo_cliente'     => $dados['Cliente']['codigo'],
                    'descricao'          => "Atestados sem CID",
                    'assunto'            => "Atestados sem CID",
                    'descricao_email'    => $content,
                    'codigo_alerta_tipo' => '30',
                    'model'              => 'Atestado',
                    'foreign_key'        => NULL,
                    'email_agendados'    => false,
                    'sms_agendados'      => false
                ),
            );

            //seta a model de alertas
            $this->Alerta =& ClassRegistry::init('Alerta');            
            $this->Alerta->incluir($alerta);

        } //fim atestado cid vazio

    }//fim notificacao_atestado_sem_cid

     public function buscar_cnpj_alocado($codigo_funcionario_setor_cargo, $param_cnpj_alocado = null)
    {
    	$this->FuncionarioSetorCargo =& ClassRegistry::init('FuncionarioSetorCargo');

        $this->FuncionarioSetorCargo->unbindModel(array('belongsTo' => array('ClienteFuncionario','Setor','Cargo','Cliente')));
        
        $joins = array(        
            array(
            'table' => 'RHHealth.dbo.cliente',
            'alias' => 'Cliente',
            'type' =>   'INNER',
            'conditions' => 'FuncionarioSetorCargo.codigo_cliente_alocacao = Cliente.codigo'
            ));

        $fields = array('ISNULL(Cliente.codigo_documento_real, Cliente.codigo_documento) as codigo_documento');
        $conditions = array('FuncionarioSetorCargo.codigo' => $codigo_funcionario_setor_cargo);

        $cnpj_alocado = $this->FuncionarioSetorCargo->find('first', array('fields' => $fields, 'joins' => $joins, 'conditions' => $conditions));

        $retorno = 0;

        if($cnpj_alocado[0]['codigo_documento'] == $param_cnpj_alocado){
            $retorno = 1;
        }
        return $retorno;
    }
/**
 *
 * @param [type] $codigos_clientes
 * @return void
 */
	/**
	* Seleciona todos atestados.
	* Utilizado na integração nexo. 
	* @param int codigo_pedido
	* @return CodigoEmpresa | CodigoFuncionario | DataPedido | CodigoClinicaRealizadoExame | CodigoExameAmbulatorial | CodigoTipoExame | Validade | Resultado | Observacao | DataRealizacaoExame | CodigoMedico
	*/
	public function busca_itens_atestados_nexo($codigos_clientes, $hora = 2, $codigo_usuarios_not_in = null){

		$clienteFuncionario = ClassRegistry::init('ClienteFuncionario');
		$cliente = ClassRegistry::init('Cliente');
		$clienteExterno = ClassRegistry::init('ClienteExterno');
		$funcionarioSetorCargo = ClassRegistry::init('FuncionarioSetorCargo');
		$motivosAfastamento = ClassRegistry::init('MotivoAfastamento');
		$motivosAfastamentoExterno = ClassRegistry::init('MotivoAfastamentoExterno');

	    $joins  = array(
	        array(
	            'table' => "{$clienteFuncionario->databaseTable}.{$clienteFuncionario->tableSchema}.{$clienteFuncionario->useTable}",
	            'alias' => 'ClienteFuncionario',
	            'type' => 'INNER',
	            'conditions' => 'ClienteFuncionario.codigo = Atestado.codigo_cliente_funcionario'
	        ),
	        array(
	            'table' => "{$cliente->databaseTable}.{$cliente->tableSchema}.{$cliente->useTable}",
	            'alias' => 'Cliente',
	            'type' => 'INNER',
	            'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente_matricula AND Cliente.codigo '.$this->rawsql_codigo_cliente($codigos_clientes)
	            
	        ),
	        array(
	            'table' => "{$clienteExterno->databaseTable}.{$clienteExterno->tableSchema}.{$clienteExterno->useTable}",
	            'alias' => 'ClienteExterno',
	            'type' => 'LEFT',
	            'conditions' => 'Cliente.codigo = ClienteExterno.codigo_cliente'
	        ),
	        array(
	            'table' => "{$funcionarioSetorCargo->databaseTable}.{$funcionarioSetorCargo->tableSchema}.{$funcionarioSetorCargo->useTable}",
	            'alias' => 'FuncionarioSetorCargo',
	            'type' => 'INNER',
	            'conditions' => 'ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario AND [FuncionarioSetorCargo].data_fim IS NULL'
	        ),
	        array(
	            'table' => "RHHealth.dbo.cargos",
	            'alias' => 'Cargo',
	            'type' => 'INNER',
	            'conditions' => 'FuncionarioSetorCargo.codigo_cargo = Cargo.codigo'
	        ),
	        array(
	            'table' => "RHHealth.dbo.cargos_externo",
	            'alias' => 'CargoExterno',
	            'type' => 'LEFT',
	            'conditions' => 'CargoExterno.codigo_cargo = Cargo.codigo'
	        ),
	        array(
	            'table' => "RHHealth.dbo.setores",
	            'alias' => 'Setor',
	            'type' => 'INNER',
	            'conditions' => 'FuncionarioSetorCargo.codigo_setor = Setor.codigo'
	        ),
	        array(
	            'table' => "{$cliente->databaseTable}.{$cliente->tableSchema}.{$cliente->useTable}",
	            'alias' => 'ClienteAlocacao',
	            'type' => 'INNER',
	            'conditions' => 'ClienteAlocacao.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao'
	        ),
	        array(
	            'table' => "{$clienteExterno->databaseTable}.{$clienteExterno->tableSchema}.{$clienteExterno->useTable}",
	            'alias' => 'ClienteExternoAlocacao',
	            'type' => 'LEFT',
	            'conditions' => 'ClienteAlocacao.codigo = ClienteExternoAlocacao.codigo_cliente'
	        ),
	        array(
	            'table' => "RHHealth.dbo.setores_externo",
	            'alias' => 'SetorExterno',
	            'type' => 'LEFT',
	            'conditions' => 'SetorExterno.codigo_setor = Setor.codigo'
	        ),	        
	        array(
	            'table' => "{$motivosAfastamento->databaseTable}.{$motivosAfastamento->tableSchema}.{$motivosAfastamento->useTable}",
	            'alias' => 'MotivoAfastamento',
	            'type' => 'INNER',
	            'conditions' => 'Atestado.codigo_motivo_licenca = MotivoAfastamento.codigo'
	        ),
	        array(
	            'table' => "{$motivosAfastamentoExterno->databaseTable}.{$motivosAfastamentoExterno->tableSchema}.{$motivosAfastamentoExterno->useTable}",
	            'alias' => 'MotivoAfastamentoExterno',
	            'type' => 'LEFT',
	            'conditions' => 'MotivoAfastamento.codigo = MotivoAfastamentoExterno.codigo_motivos_afastamento AND MotivoAfastamentoExterno.codigo_cliente = [ClienteFuncionario].codigo_cliente_matricula AND (MotivoAfastamentoExterno.codigo = (SELECT TOP 1 codigo FROM motivos_afastamento_externo WHERE codigo_motivos_afastamento = MotivoAfastamento.codigo))'
	        ),
	        array(
	            'table' => "RHHealth.dbo.atestados_cid",
	            'alias' => 'AtestadoCid',
	            'type' => 'LEFT',
	            'conditions' => 'Atestado.codigo = AtestadoCid.codigo_atestado'
	        ),
	        array(
	            'table' => "RHHealth.dbo.cid",
	            'alias' => 'Cid',
	            'type' => 'LEFT',
	            'conditions' => 'AtestadoCid.codigo_cid = Cid.codigo'
	        ),
	        array(
	            'table' => "RHHealth.dbo.medicos",
	            'alias' => 'Medico',
	            'type' => 'LEFT',
	            'conditions' => 'Atestado.codigo_medico = Medico.codigo'
	        ),

	    );
		//$hora = 300; // <----- REMOVER TESTE
		
		// TODO -- AJUSTE CAMPOS
	    $fields = array(
	    	'YEAR(Atestado.data_inclusao) AS AnoEmissaoAtestado',
	    	"(CASE WHEN Atestado.ativo = 1 THEN '0' else '1' END) AS Cancelado",
	    	"CargoExterno.codigo_externo AS Cargo",
			"ISNULL(MotivoAfastamentoExterno.codigo_externo,MotivoAfastamento.codigo) as CodigoTipoAbsenteismo",
			"'' AS Classificador",
			"Cid.codigo_cid10 AS CodigoDoenca",
			"'' AS CodigoEmpresa", //<--- verificando com a nexo
			"'' AS CodigoEspecialidadeDoenca",
			"'' AS CodigoEspecialidadeEmissor",
			"ClienteExternoAlocacao.codigo_externo AS CodigoLotacao",
			"'' AS CodigoMedicoEmissor",
			"Medico.numero_conselho AS CodigoMedico",
			"'' AS CodigoParecer",
			"ClienteFuncionario.matricula AS CodigoFuncionario",
			"'' AS CNPJCessao",
			"'' AS CNPJSindicato",
			"'0' AS Definitivo",
			"CONVERT(VARCHAR(10), Atestado.data_alteracao, 112) AS DataAlteracao",
			"'' AS DataAtestado",
			"'' AS DataCancelamentoAtestado",
			"'' AS DataEfetivaRetorno",
			"CONVERT(VARCHAR(10), Atestado.data_inclusao,112) AS DataInclusao",
			"'' AS DataLicenca",
			"'' AS DataMemorando",
			"'' AS DataRetornoAfastamento",
			"Atestado.data_afastamento_periodo AS DataInicio",
			"Atestado.data_retorno_periodo AS DataRetorno",
			"'0' AS Externo",
			"'' AS TotalHorasTrabalhadas",
			"CONVERT(VARCHAR(8), Atestado.hora_afastamento, 8) AS HoraSaida",
			"CONVERT(VARCHAR(8), Atestado.hora_retorno, 8) AS HoraVolta",
			"Atestado.onus_requisicao AS InfoOnus",
			"Atestado.codigo AS NumeroAtestado",
			"Atestado.codigo AS NumeroAusencia",
			"'' AS NumeroMemorando",
			"'' AS NumeroPortaria",
			"Atestado.observacao AS ObservacaoAfastamento",
			"Atestado.onus_remuneracao AS OnusRemuneracao",
			"'' AS SemInterrupcao",
			"SetorExterno.codigo_externo AS Setor",
			"Atestado.afastamento_em_dias AS TotalDiasAfastamento",
			"'' AS TotalDiasCheio",
			"Atestado.afastamento_em_horas AS TotalHorasAfastamento",
			"'RHHEALTH' AS AtributoValor1",
			"ClienteExternoAlocacao.codigo_cliente AS codigo_cliente"
		);

		//pega a data atual e a hora atual e busca na base com diferença das horas informadas
		$start_date = date("Y-m-d H:m:i", strtotime( "-".$hora." hours" ));
		$end_date = date("Y-m-d H:i:s");
		// $end_date = date("Y-m-d", strtotime( "now" )) . ' 23:59:59';

		$conditions = array(
			'ClienteExternoAlocacao.codigo_cliente IS NOT NULL',		
			'OR' => array(
				'Atestado.data_inclusao BETWEEN ? and ?' => array($start_date, $end_date),
				'Atestado.data_alteracao BETWEEN ? and ?' => array($start_date, $end_date),
			)
			
		);

		// debug($codigo_usuarios_not_in);

		//verifica quais codigo não devem trazer
		if(!empty($codigo_usuarios_not_in)) {
			$conditions['Atestado.codigo_usuario_inclusao NOT'] = $codigo_usuarios_not_in;
		}//insere um filtro
		
	    $dados = $this->find('all', array('fields' => $fields,'joins' => $joins, 'conditions' => $conditions));

	    // debug($dados);exit;

	    return $dados;

	}// fim busca_itens_pedidos_exames_nexo


	/**
	* Seleciona todos os medicos de acordo com o codigo do atestado
	* Utilizado na integração nexo.
	* @param int codigo_pedido
	* @return CodigoProfissional | NomeProfissional | Funcao | Ativo
	*/
	public function busca_medico_atestado_nexo($codigo_atestado) {
	    
	    //monta os joins
	    $joins  = array(
	        array(
                'table' => "Rhhealth.dbo.medicos",
                'alias' => 'Medico',
                'type' => 'INNER',
                'conditions' => 'Atestado.codigo_medico = Medico.codigo',
	        ),	        
	        array(
                'table' => "Rhhealth.dbo.conselho_profissional",
                'alias' => 'ConselhoProfissional',
                'type' => 'INNER',
                'conditions' => 'Medico.codigo_conselho_profissional = ConselhoProfissional.codigo',
	        )
	    );
	    //monta os fields para enviar para enviar para nexo
	    $fields = array(
	    	'Medico.codigo AS CodigoProfissional', 
            'Medico.nome AS NomeProfissional', 			
			'CONCAT(Medico.numero_conselho,\'-\',Medico.conselho_uf) AS CRM',
			'Medico.especialidade AS Funcao',
			"'M' AS TipoProfissional",
			'Medico.ativo AS Ativo'
       	);

	    $conditions = array('Atestado.codigo' => $codigo_atestado);


	    //executa a query montada do orm
	    $dados = $this->find('first', array('fields' => $fields,'joins' => $joins, 'conditions' => $conditions));

	    // debug($dados); exit;

	    return $dados;
	} //fim busca_medico_atestado_nexo

	public function ConditionXmlS2230($data) {
        //seta a variavel para inicio do metodo
        $conditions = array();

        //verifica se tem valores nos filtros
        if (!empty($data['codigo_cliente'])) {
            $conditions['ClienteFuncionario.codigo_cliente_matricula'] = $data['codigo_cliente'];
        }

        if (!empty($data['codigo_cliente_alocacao'])) {
            $conditions['FuncionarioSetorCargo.codigo_cliente_alocacao'] = $data['codigo_cliente_alocacao'];
        }

        if (!empty($data['codigo_cargo'])) {
            $conditions['Cargo.codigo_cargo'] = $data['codigo_cargo'];
        }

        if (!empty($data['codigo_setor'])) {
            $conditions['Setor.codigo_setor'] = $data['codigo_setor'];
        }

        if (!empty($data['codigo_funcionario'])) {
            $conditions["Funcionario.codigo"] = $data['codigo_funcionario'];
        }

        if (!empty($data['nome_funcionario'])) {
            $conditions["Funcionario.nome LIKE"] = '%'. $data['codigo_funcionario'] . '%';
        }

        if (!empty($data['cpf'])) {
            $conditions["Funcionario.cpf"] = Comum::soNumero($data['cpf']);
        }

        if (!empty($data['codigo_atestado'])) {
            $conditions['Atestado.codigo'] = $data['codigo_atestado'];
        }
        
        //logica para as datas de filtros
        if(!empty($data["data_inicio"])) {
            $data_inicio = AppModel::dateToDbDate($data["data_inicio"].' 00:00:00');
            $data_fim = AppModel::dateToDbDate($data["data_fim"].' 23:59:59');
            $conditions [] = "(Atestado.data_inclusao >= '". $data_inicio . "'";
        }//fim if

        if(!empty($data["data_fim"])) {
            $conditions [] = "Atestado.data_inclusao <= '" . $data_fim . "')";
        }

        $conditions['Setor.ativo'] = 1;
        $conditions['Cargo.ativo'] = 1;

        
        // die(debug($conditions));
        return $conditions;
        
    } //fim ConditionXmlS2240

    public function returnJoinsEsocial(){
        $joins = array(
            array(
                'table' => 'RHHealth.dbo.cliente_funcionario',
                'alias' => 'ClienteFuncionario',
                'type' => 'LEFT',
                'conditions' => 'ClienteFuncionario.codigo = Atestado.codigo_cliente_funcionario',
            )
        );

        return $joins;
    }

}//FINAL CLASS Atestado