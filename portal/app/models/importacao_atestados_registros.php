<?php
class ImportacaoAtestadosRegistros extends AppModel {
    var $name  			= 'ImportacaoAtestadosRegistros';
    var $tableSchema	= 'dbo';
    var $databaseTable	= 'RHHealth';
    var $useTable		= 'importacao_atestados_registros';
    var $primaryKey		= 'codigo';
    var $actsAs			= array('Secure');

    /**
     * Função responsável pela paginação dos registros para importação dos atestados
     * @param array $coditions Condições de filtragem de dados da query da paginação
     * @param array $fields Campos da query da paginação
     * @param char $order Campos de ordenação da query da paginação
     * @param number $limit Quantidade de registros exibidos por página
     * @param number $page Informação página atual da paginação
     * @param boolean|null $recursive
     * @param array|array $extra 
     * @return array Dados de retorno query de dados dos registros importados para validação
     */    
    public function paginate( $conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array() ) {
		$joins = null;
		if (isset($extra['joins']))
			$joins = $extra['joins'];
		if (isset($extra['group']))
			$group = $extra['group'];
		if( isset( $extra['extra']['importacao'] ) && $extra['extra']['importacao'] ){
			// pr($this->findImportacao('sql', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins')));exit;
			return $this->findImportacao('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
		}
		return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
	}

	/**
	 * Função responsável pela contagem dos registros para geração da paginação de registros importados
	 * @param array|null $coditions Condições de filtragem de dados da query da paginação
	 * @param number $recursive 
	 * @param array|array $extra 
	 * @return array retorna a quantidade de registros da query de dados da paginação
	 */
	public function paginateCount( $conditions = null, $recursive = 0, $extra = array() ) {
		$joins = null;
		if (isset($extra['joins']))
			$joins = $extra['joins'];		
		if( isset( $extra['extra']['importacao'] ) && $extra['extra']['importacao'] ){
			return $this->findImportacao('count', compact('conditions', 'recursive', 'joins'));
		}
		return $this->find('count', compact('conditions', 'recursive', 'joins'));
	}

	/**
	 * Método de Geração de Query para Busca de Dados para validação dos dados importados do arquivo para geração dos registros de atestados
	 * @param string $findType Tipo de Consulta que será feita para a listagem de Arquivos Importados
	 * @param array $options 
	 * @return array Dados de retorno query de dados dos registros importados para validação
	 */
	public function findImportacao($findType, $options) {
		$ClienteFuncionario 	=& ClassRegistry::init('ClienteFuncionario');
		$Funcionario 			=& ClassRegistry::init('Funcionario');
		$FuncionarioSetorCargo 	=& ClassRegistry::init('FuncionarioSetorCargo');
		$Setor 					=& ClassRegistry::init('Setor');
		$Cargo 					=& ClassRegistry::init('Cargo');
		$StatusImportacao 		=& ClassRegistry::init('StatusImportacao');
		$ImportacaoAtestados 	=& ClassRegistry::init('ImportacaoAtestados');
		$this->bindModel(array('belongsTo' => array(
			'ImportacaoAtestados' => array('foreignKey' => 'codigo_importacao_atestados'),
			'GrupoEconomico' => array('foreignKey' => false, 'conditions' => 'GrupoEconomico.codigo = ImportacaoAtestados.codigo_grupo_economico'),
			'ClienteUnidade' => array('className' => 'Cliente', 'foreignKey' => false, 'conditions' => array(
				'ClienteUnidade.codigo_empresa = ImportacaoAtestados.codigo_empresa',
				"ClienteUnidade.razao_social LIKE ImportacaoAtestadosRegistros.nome_empresa",
				"ClienteUnidade.nome_fantasia LIKE ImportacaoAtestadosRegistros.nome_unidade"
			)),
			'Funcionario' => array('className' => 'Funcionario', 'foreignKey' => false, 'conditions' => array(
				'Funcionario.cpf = ImportacaoAtestadosRegistros.cpf'
			)),
			'ClienteFuncionario' => array('className' => 'ClienteFuncionario', 'foreignKey' => false, 'conditions' => array(
				'ClienteFuncionario.codigo_empresa = ImportacaoAtestados.codigo_empresa',
				'ClienteFuncionario.codigo_cliente_matricula = GrupoEconomico.codigo_cliente',
				'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
			)),
			'FuncionarioSetorCargo' => array('className' => 'FuncionarioSetorCargo', 'foreignKey' => false, 'conditions' => array(
				'FuncionarioSetorCargo.codigo_empresa = ImportacaoAtestados.codigo_empresa',
								
				"FuncionarioSetorCargo.codigo = (SELECT TOP 1 FuncionarioSetorCargo2.codigo 
														 FROM [funcionario_setores_cargos] AS FuncionarioSetorCargo2
														 WHERE codigo_cliente_funcionario = (SELECT TOP 1 ClienteFuncionario2.codigo 
				FROM cliente_funcionario AS ClienteFuncionario2
					INNER JOIN funcionarios AS Funcionario2 ON Funcionario2.codigo = ClienteFuncionario2.codigo_funcionario AND Funcionario2.cpf = [ImportacaoAtestadosRegistros].[cpf]
				WHERE ClienteFuncionario2.codigo_cliente_matricula = GrupoEconomico.codigo_cliente
				  AND ClienteFuncionario2.codigo_empresa = GrupoEconomico.codigo_empresa
				  AND (ClienteFuncionario2.codigo = 
				  		ClienteFuncionario.codigo
					--CASE WHEN LTRIM(RTRIM([ImportacaoAtestadosRegistros].[matricula]))<>'' THEN cast(replace([ImportacaoAtestadosRegistros].[matricula], '.', '') as int) ELSE ClienteFuncionario.codigo END
						OR ClienteFuncionario2.matricula = [ImportacaoAtestadosRegistros].matricula)
			ORDER BY ClienteFuncionario2.codigo DESC) 
ORDER BY FuncionarioSetorCargo2.codigo DESC)"



				// 'ClienteFuncionario.codigo_cliente_matricula = ClienteUnidade.codigo',
				// 'ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario'
			)),
			'StatusImportacao' => array('className' => 'StatusImportacao', 'foreignKey' => false, 'conditions' => array(
				'StatusImportacao.codigo = ImportacaoAtestadosRegistros.codigo_status_importacao'
			))
		)));
		
		$fields = $this->findImportacaoBaseFields();
		$conditions = $options['conditions'];
		$query_base = $this->find('sql', compact('fields', 'conditions'));
		$dbo = $this->getDataSource();

		$cte = "WITH Base AS ($query_base)";

		$offset = (isset($options['page']) && $options['page'] > 1 ? (($options['page'] -1) * $options['limit']) : null);
		$query = $dbo->buildStatement(array(
			'fields' => $this->findImportacaoFields(),
			'table' => "Base",
			'alias' => 'ImportacaoAtestadosRegistros',
			'joins' => array(
				array(
					'table' => $ClienteFuncionario->databaseTable.".".$ClienteFuncionario->tableSchema.".".$ClienteFuncionario->useTable,
					'alias' => 'ClienteFuncionario',
					'conditions' => array(
						'ClienteFuncionario.codigo_empresa = ImportacaoAtestadosRegistros.codigo_empresa',
						'ClienteFuncionario.codigo = ImportacaoAtestadosRegistros.codigo_cliente_funcionario'
					),
					'type' => 'LEFT'
				),
				array(
					'table' => $Funcionario->databaseTable.".".$Funcionario->tableSchema.".".$Funcionario->useTable,
					'alias' => 'Funcionario',
					'conditions' => 'Funcionario.cpf = ImportacaoAtestadosRegistros.cpf',
					'type' => 'LEFT'
				),
				array(
					'table' => $FuncionarioSetorCargo->databaseTable.".".$FuncionarioSetorCargo->tableSchema.".".$FuncionarioSetorCargo->useTable,
					'alias' => 'FuncionarioSetorCargo',
					'conditions' => 'FuncionarioSetorCargo.codigo = ImportacaoAtestadosRegistros.codigo_func_setor_cargo',
					'type' => 'LEFT'
				),
				array(
					'table' => $Setor->databaseTable.".".$Setor->tableSchema.".".$Setor->useTable,
					'alias' => 'Setor',
					'conditions' => array(
						'Setor.codigo_empresa = FuncionarioSetorCargo.codigo_empresa',
						'Setor.codigo = FuncionarioSetorCargo.codigo_setor'
					),
					'type' => 'LEFT'
				),
				array(
					'table' => $Cargo->databaseTable.".".$Cargo->tableSchema.".".$Cargo->useTable,
					'alias' => 'Cargo',
					'conditions' => array(
						'Cargo.codigo_empresa = FuncionarioSetorCargo.codigo_empresa',
						'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo'
					),
					'type' => 'LEFT'
				)
			),
			'limit' => (isset($options['limit']) ? $options['limit'] : null),
			'offset' => $offset,
			'conditions' => null,
			'order' => (isset($options['order']) ? $options['order'] : null),
			'group' => null,
		), $this);

		if ($findType == 'sql') {
			return array('cte' => $cte, 'query' => $query);
		} elseif ($findType == 'count') {
			$result = $this->query("{$cte} SELECT COUNT(codigo) AS qtd FROM ({$query}) AS base");
			return $result[0][0]['qtd'];
		}

		// pr($cte.$query);exit;

		return $this->query($cte.$query);
	}

	/**
	 * Método de definição de quais campos serão utilizados para validação dos dados da importação de atestados.
	 * @return array Campos que serão utilizados na query de busca de dados para validação dos dados da importação de atestados.
	 */
	private function findImportacaoFields() {
		return array(
			'ImportacaoAtestadosRegistros.codigo',
			'ImportacaoAtestadosRegistros.nome_empresa',
			'ImportacaoAtestadosRegistros.nome_unidade',
			'ImportacaoAtestadosRegistros.nome_setor',
			'ImportacaoAtestadosRegistros.nome_cargo',
			'ImportacaoAtestadosRegistros.matricula',
			'ImportacaoAtestadosRegistros.cpf',
			'ImportacaoAtestadosRegistros.tipo_atestado',
			'ImportacaoAtestadosRegistros.sem_profissional',
			'ImportacaoAtestadosRegistros.codigo_medico',
			'ImportacaoAtestadosRegistros.medico_solicitante',
			'ImportacaoAtestadosRegistros.conselho_classe',
			'ImportacaoAtestadosRegistros.UF',
			'ImportacaoAtestadosRegistros.sigla_conselho',
			'ImportacaoAtestadosRegistros.especialidade',
			// 'ImportacaoAtestadosRegistros.especialidade2',
			'CONVERT(VARCHAR, ImportacaoAtestadosRegistros.data_inicio_afastamento, 103) AS data_inicio_afastamento',
			'CONVERT(VARCHAR, ImportacaoAtestadosRegistros.data_retorno_afastamento, 103) AS data_retorno_afastamento',
			'ImportacaoAtestadosRegistros.dias AS dias',
			'ImportacaoAtestadosRegistros.hora_inicio_afastamento',
			'ImportacaoAtestadosRegistros.hora_termino_afastamento',
			'ImportacaoAtestadosRegistros.horas AS horas',
			'ImportacaoAtestadosRegistros.codigo_cid',
			'ImportacaoAtestadosRegistros.nome_cid',
			'ImportacaoAtestadosRegistros.restricao_retorno',
			'ImportacaoAtestadosRegistros.motivo_licenca',
			'ImportacaoAtestadosRegistros.tipo_licenca',
			'ImportacaoAtestadosRegistros.tabela_18_esocial',
			'ImportacaoAtestadosRegistros.motivo_afastamento',
			'ImportacaoAtestadosRegistros.origem_retificacao',
			'ImportacaoAtestadosRegistros.tipo_acidente_transito',
			'ImportacaoAtestadosRegistros.tipo_processo',
			'ImportacaoAtestadosRegistros.numero_processo',
			'ImportacaoAtestadosRegistros.codigo_documento_entidade',
			'ImportacaoAtestadosRegistros.onus_remuneracao',
			'ImportacaoAtestadosRegistros.onus_requisicao',
			'Funcionario.nome AS nome_funcionario',
			'Setor.descricao AS setor_descricao',
			'Cargo.descricao AS cargo_descricao',
			'FuncionarioSetorCargo.data_inicio AS inicio_periodo_matricula',
			'FuncionarioSetorCargo.data_fim AS termino_periodo_matricula',
			'ImportacaoAtestadosRegistros.status_importacao AS status_importacao',
			'ImportacaoAtestadosRegistros.codigo_status_importacao AS codigo_status_importacao',
			"ImportacaoAtestadosRegistros.codigo_importacao_atestados AS codigo_importacao_atestados",
			"FuncionarioSetorCargo.codigo AS codigo_func_setor_cargo",
			"ClienteFuncionario.codigo AS codigo_cliente_funcionario",
			"ImportacaoAtestadosRegistros.tp_acid_transito",
			"ImportacaoAtestadosRegistros.obs_afastamento",
			"ImportacaoAtestadosRegistros.renumeracao_cargo",
			"ImportacaoAtestadosRegistros.data_inicio_p_aquisitivo",
			"ImportacaoAtestadosRegistros.data_fim_p_aquisitivo",
			"ImportacaoAtestadosRegistros.observacao AS observacao",
		);
	}

	/**
	 * Método de definição de quais campos serão utilizados para validação dos dados da importação de atestados.
	 * @return array Campos que serão utilizados na query de busca de dados para validação dos dados da importação de atestados.
	 */
	private function findImportacaoBaseFields() {
		return array(
			'ImportacaoAtestados.codigo_empresa AS codigo_empresa',
			'ImportacaoAtestadosRegistros.codigo AS codigo',
			'ImportacaoAtestadosRegistros.nome_empresa AS nome_empresa',
			'ImportacaoAtestadosRegistros.nome_unidade AS nome_unidade',
			'ImportacaoAtestadosRegistros.nome_setor AS nome_setor',
			'ImportacaoAtestadosRegistros.nome_cargo AS nome_cargo',
			'replace(ImportacaoAtestadosRegistros.matricula, \'.\', \'\') AS matricula',
			// 'ImportacaoAtestadosRegistros.matricula AS matricula',
			// 'cast(replace(ImportacaoAtestadosRegistros.matricula, \'.\', \'\') as int) AS matricula',
			'ImportacaoAtestadosRegistros.cpf AS cpf',
			'ImportacaoAtestadosRegistros.tipo_atestado AS tipo_atestado',
			'ImportacaoAtestadosRegistros.sem_profissional AS sem_profissional',
			'ImportacaoAtestadosRegistros.codigo_medico AS codigo_medico',
			'ImportacaoAtestadosRegistros.medico_solicitante AS medico_solicitante',
			'ImportacaoAtestadosRegistros.conselho_classe AS conselho_classe',
			'ImportacaoAtestadosRegistros.UF AS UF',
			'ImportacaoAtestadosRegistros.sigla_conselho AS sigla_conselho',
			'ImportacaoAtestadosRegistros.especialidade AS especialidade',
			// 'ImportacaoAtestadosRegistros.especialidade2 AS especialidade2',
			'ImportacaoAtestadosRegistros.data_inicio_afastamento AS data_inicio_afastamento',
			'ImportacaoAtestadosRegistros.data_retorno_afastamento AS data_retorno_afastamento',
			'ImportacaoAtestadosRegistros.dias AS dias',
			'ImportacaoAtestadosRegistros.hora_inicio_afastamento AS hora_inicio_afastamento',
			'ImportacaoAtestadosRegistros.hora_termino_afastamento AS hora_termino_afastamento',
			'ImportacaoAtestadosRegistros.horas AS horas',
			'ImportacaoAtestadosRegistros.codigo_cid AS codigo_cid',
			'ImportacaoAtestadosRegistros.nome_cid AS nome_cid',
			'ImportacaoAtestadosRegistros.restricao_retorno AS restricao_retorno',
			'ImportacaoAtestadosRegistros.motivo_licenca AS motivo_licenca',
			'ImportacaoAtestadosRegistros.tipo_licenca AS tipo_licenca',
			'ImportacaoAtestadosRegistros.tabela_18_esocial AS tabela_18_esocial',
			'ImportacaoAtestadosRegistros.motivo_afastamento AS motivo_afastamento',
			'ImportacaoAtestadosRegistros.origem_retificacao AS origem_retificacao',
			'ImportacaoAtestadosRegistros.tipo_acidente_transito AS tipo_acidente_transito',
			'ImportacaoAtestadosRegistros.tipo_processo AS tipo_processo',
			'ImportacaoAtestadosRegistros.numero_processo AS numero_processo',
			'ImportacaoAtestadosRegistros.codigo_documento_entidade AS codigo_documento_entidade',
			'ImportacaoAtestadosRegistros.onus_remuneracao AS onus_remuneracao',
			'ImportacaoAtestadosRegistros.onus_requisicao AS onus_requisicao',
			'ClienteUnidade.codigo AS cliente_alocacao_codigo',
			'ClienteUnidade.razao_social AS cli_aloc_razao_social',
			'ClienteUnidade.nome_fantasia AS cli_aloc_nome_fantasia',	
			"ClienteFuncionario.codigo AS codigo_cliente_funcionario",
		    "FuncionarioSetorCargo.codigo AS codigo_func_setor_cargo",
			"Funcionario.nome AS nome_funcionario",
			"StatusImportacao.descricao AS status_importacao",
			"StatusImportacao.codigo AS codigo_status_importacao",
			"ImportacaoAtestadosRegistros.codigo_importacao_atestados AS codigo_importacao_atestados",
			"FuncionarioSetorCargo.codigo AS codigo_func_setor_cargo",
			"ImportacaoAtestadosRegistros.tp_acid_transito AS tp_acid_transito",
			"ImportacaoAtestadosRegistros.obs_afastamento AS obs_afastamento",
			"ImportacaoAtestadosRegistros.renumeracao_cargo AS renumeracao_cargo",
			"ImportacaoAtestadosRegistros.data_inicio_p_aquisitivo AS data_inicio_p_aquisitivo",
			"ImportacaoAtestadosRegistros.data_fim_p_aquisitivo AS data_fim_p_aquisitivo",
			"ImportacaoAtestadosRegistros.observacao AS observacao",
		);
	}

	/**
	 * Método de depara dos campos da planilha para os campos da query de dados de validação dos dados de importação de atestados
	 * @return array Depara de campos
	 */
	function depara() {
		return array(
			'nome_funcionario'			=> 'nome_funcionario',
			'nome_unidade'				=> 'nome_unidade',
			'nome_setor'				=> 'setor_descricao',
			'nome_cargo'				=> 'cargo_descricao',
			'matricula'					=> 'matricula',
			'cpf'						=> 'cpf',
			'tipo_atestado'				=> 'tipo_atestado',
			'sem_profissional'			=> 'sem_profissional',
			'codigo_medico'				=> 'codigo_medico',
			'medico_solicitante' 		=> 'medico_solicitante',
			'conselho_classe' 			=> 'conselho_classe',
			'UF' 						=> 'UF',
			'sigla_conselho' 			=> 'sigla_conselho',
			'especialidade' 			=> 'especialidade',
			// 'especialidade2'			=> 'especialidade2',
			'data_inicio_afastamento'	=> 'data_inicio_afastamento',
			'data_retorno_afastamento'	=> 'data_retorno_afastamento',
			'dias' 						=> 'dias',
			'hora_inicio_afastamento' 	=> 'hora_inicio_afastamento',
			'hora_termino_afastamento' 	=> 'hora_termino_afastamento',
			'horas' 					=> 'horas',
			'codigo_cid' 				=> 'codigo_cid',
			'nome_cid' 					=> 'nome_cid',
			'restricao_retorno'			=> 'restricao_retorno',
			'motivo_licenca' 			=> 'motivo_licenca',
			'tipo_licenca' 				=> 'tipo_licenca',
			'tabela_18_esocial' 		=> 'tabela_18_esocial',
			'tp_acid_transito' 			=> 'tp_acid_transito',
			'tipo_acidente_transito'    => 'tipo_acidente_transito',
			'motivo_afastamento' 		=> 'motivo_afastamento',
			'origem_retificacao'        => 'origem_retificacao',
			'tipo_processo'        		=> 'tipo_processo',
			'numero_processo'        	=> 'numero_processo',
			'codigo_documento_entidade'	=> 'codigo_documento_entidade',
			'onus_remuneracao'        	=> 'onus_remuneracao',
			'onus_requisicao'        	=> 'onus_requisicao',
			'obs_afastamento' 			=> 'obs_afastamento',
			'renumeracao_cargo' 		=> 'renumeracao_cargo',
			'data_inicio_p_aquisitivo'  => 'data_inicio_p_aquisitivo',
			'data_fim_p_aquisitivo' 	=> 'data_fim_p_aquisitivo',
			'observacao'				=> 'observacao',
		);
	}

	/**
	 * Método de depara dos campos das validação para os títulos de exibição do cabeçalho da listagem de registros de importação de atestados.
	 * @return array Depara de campos de validação para os títulos das colunas da listagem de registros de importação de atestados.
	 */
	function titulos() {
		return array(
			'nome_funcionario'			=> 'Funcionário',
			'nome_unidade'				=> 'Nome da Unidade',
			'nome_setor'				=> 'Nome do Setor',
			'nome_cargo'				=> 'Nome do Cargo',
			'matricula'					=> 'Matrícula do Funcionário',
			'cpf'						=> 'CPF',
			'tipo_atestado'				=> 'Tipo',
			'sem_profissional'			=> 'Atestado sem profissional médico?',
			'codigo_medico'				=> 'Código do Profissional',
			'medico_solicitante'		=> 'Nome do Médico',
			'conselho_classe' 			=> 'Número do Conselho',
			'UF' 						=> 'Estado',
			'sigla_conselho'			=> 'Conselho',
			'especialidade' 			=> 'Especialidade do Médico',
			// 'especialidade2' 			=> '2ª Especialidade',
			'data_inicio_afastamento'	=> 'Data Início Afastamento',
			'data_retorno_afastamento'	=> 'Data Retorno Afastamento',
			'dias'						=> 'Dias de Afastamento',
			'hora_inicio_afastamento'	=> 'Hora Início Afastamento',
			'hora_termino_afastamento'	=> 'Hora Fim Afastamento',
			'horas'						=> 'Horas de Afastamento',
			'codigo cid'				=> 'Identificação CID',
			'nome_cid'					=> 'Descrição CID',
			'restricao_retorno'			=> 'Restrição Retorno',
			'motivo_licenca'			=> 'Motivo de Licença',
			'tipo_licenca'				=> 'Tipo de Afastamento',
			'tabela_18_esocial'			=> 'Motivo da Licença (Tabela 18 – eSocial)',
			'tp_acid_transito' 			=> 'Acidente de Transito?',
			'tipo_acidente_transito' 	=> 'Tipo acidente de transito',
			'motivo_afastamento'		=> 'Afastamento decorre de mesmo motivo de afastamento anterior?',
			'origem_retificacao' 		=> 'Origem da Retificação',
			'tipo_processo' 			=> 'Tipo processo',
			'numero_processo' 			=> 'Numero processo',
			'codigo_documento_entidade' => 'CNPJ',
			'onus_remuneracao' 			=> 'Ônus da remuneração',
			'onus_requisicao' 			=> 'Ônus da cessăo/requisição',
			'obs_afastamento' 			=> 'Observaçăo (eSocial)',
			'renumeracao_cargo' 		=> 'Renumeração do Cargo',
			'data_inicio_p_aquisitivo'  => 'Data Início Período Aquisitivo',
			'data_fim_p_aquisitivo' 	=> 'Data Fim Período Aquisitivo',
			'observacao'				=> 'Observação',
		);
	}

	/**
	 * Método de validação de todas as linhas do arquivo importadas para geração de atestados médicos
	 * @param array $registros 
	 * @return array Array onde a chave é nome do arquivo e a mensagem de inconsistência gerada na validação
	 */
	function alertasRegistrosCadastros($registros,$codigo_status_importacao) {
		$alertas = array();
		$validacoes = array();
		$retorno = array();
        foreach ($registros as $key => $registro) {
			$retorno = $this->alertasRegistroCadastros($registro,$codigo_status_importacao);
            $alertas[$key] 		= $retorno['alertas'] ;
            $validacoes[$key]	= $retorno['validacoes'] ;
        }
        return array('alertas' => $alertas, 'validacoes' => $validacoes);	;
	
	}

	/**
	 * Método de validação por linha, chamada no método alertasRegistrosCadastros
	 * Nesse método são verificados se as colunas da planilha estão preenchidos com informação para geração do registro de atestado
	 * @param array $registros 
	 * @return array Array onde a chave é nome do arquivo e a mensagem de inconsistência gerada na validação
	 */
	public function alertasRegistroCadastros($registro,$codigo_status_importacao) 
	{
		$CID 		  =& ClassRegistry::init('Cid');
		$Conselho	  =& ClassRegistry::init('ConselhoProfissional');
		$Documento	  =& ClassRegistry::init('Documento');
		$Medico		  =& ClassRegistry::init('Medico');
		$motivo 	  =& ClassRegistry::init('MotivoAfastamento');
		$tipo 		  =& ClassRegistry::init('TipoAfastamento');
		$Atestados 	  =& ClassRegistry::init('Atestado');		
		$alertas 	  = array_keys($registro[0]);
		$alertas	  = array_flip($alertas);
		
		// debug('sem o foreach');
		// debug($registro);
		
		$campos = array();
		foreach ($registro[0] as $key => $value) $registro[0][$key] = trim($registro[0][$key]);

		if (empty($registro[0]['nome_funcionario'])) {
			$campos['nome_unidade'] = 'Funcionário não encontrado';
		}		
			
		if (empty($registro[0]['data_inicio_afastamento'])){
			$campos['data_inicio_afastamento'] = 'Data de início de afastamento do funcionário não informado';			
		} 

		if (empty($registro[0]['data_retorno_afastamento'])){
			$campos['data_retorno_afastamento'] = 'Data de retorno de afastamento do funcionário não informado';
		}

		if (empty($registro[0]['tipo_atestado'])){
			$campos['tipo_atestado'] = 'Tipo de atestado não informado';
		}

		if ($registro[0]['sem_profissional'] == ''){
			$campos['sem_profissional'] = 'Atestado sem profissional médico não informado';
		}

		if (empty($registro[0]['codigo_medico'])){
			$campos['codigo_medico'] = 'Código do Profissional não informado';
		}

		if (empty($registro[0]['especialidade']))	{
			$campos['especialidade'] = 'Especialidade profissional do médico não informado';
		}		

		if(!empty($registro[0]['data_inicio_afastamento']) && !empty($registro[0]['data_retorno_afastamento'])) {

			$data_inicio_afastamento = strtotime(AppModel::dateToDbDate2($registro[0]['data_inicio_afastamento']));
			$data_retorno_afastamento = strtotime(AppModel::dateToDbDate2($registro[0]['data_retorno_afastamento']));

			if($data_inicio_afastamento > $data_retorno_afastamento) {
				$campos['data_inicio_afastamento']	= 'Data Retorno deve ser (maior ou igual) a Data de Afastamento';
				$campos['data_retorno_afastamento']	= 'Data Retorno deve ser (maior ou igual) a Data de Afastamento';
			}
		}
		/**
		 * Caso o afastamento seja menor que 1 dia, as horas são validadas
		 */
		if (!empty($registro[0]['dias']) && $registro[0]['dias'] < 1) {
			
			if (empty($registro[0]['hora_inicio_afastamento'])){
				$campos['hora_inicio_afastamento'] = 'Hora de início de afastamento do funcionário não informado';
			}

			if (empty($registro[0]['hora_termino_afastamento'])){
				$campos['hora_termino_afastamento'] = 'Hora de término de afastamento do funcionário não informado';
			}
		}

		if (empty($registro[0]['motivo_licenca'])) {
			$campos['motivo_licenca'] = 'Motivo de licença não informado';
		}
		
		if (empty($registro[0]['tipo_licenca'])){
			$campos['tipo_licenca'] = 'Tipo de afastamento não informado';
		}			

		if (empty($registro[0]['cpf'])) {
			$campos['cpf'] = 'CPF não informado';
		} elseif (!$Documento->isCPF($registro[0]['cpf'])) {
			$campos['cpf'] = 'CPF inválido';
		}

		if (empty($registro[0]['sigla_conselho'])) {
			$campos['sigla_conselho'] = 'Sigla do Conselho Profissional do médico não informado';
		} else {
			$sigla_conselho = $registro[0]['sigla_conselho'];
		}

		if (empty($registro[0]['codigo_medico'])) {
			$campos['codigo_medico'] = 'Código do Profissional não informado';
			$id_medico_conselho = "";
		} else {
			$id_medico_conselho = $registro[0]['codigo_medico'];
		}

		//tratamento quando vier o numero conselho zero, existem profissionais com numero zero, senao vier ele faz a mesma validacao que fazia
		if($registro[0]['conselho_classe'] != 0) {
			if (empty($registro[0]['conselho_classe'])) {
				$campos['conselho_classe'] = 'Código Identificação do Conselho Profissional do médico não informado';
			}
		}

		if (empty($registro[0]['UF'])) {
			$campos['UF'] = 'Estado do médico solicitante não informado';
		} else {
			$uf = $registro[0]['UF'];
		}
		/*
		 * Acidente/doença do trabalho / Acidente/doença não relacionada ao trabalho
		*/
		if(($registro[0]['tabela_18_esocial'] == 01) || ($registro[0]['tabela_18_esocial'] == 03)){
			
			if(empty($registro[0]['motivo_afastamento'])){
				$campos['motivo_afastamento'] = 'O motivo afastamento não foi informado';
			}

			if($registro[0]['tp_acid_transito'] == ''){
				$campos['tp_acid_transito'] = 'O Acidente de Transito não foi informado';
			}

			if($registro[0]['tp_acid_transito'] == 1) {// se houve acidente, é necessario informar o tipo de acidente
				if($registro[0]['tipo_acidente_transito'] == ''){
					$campos['tipo_acidente_transito'] = 'O tipo de acidente de transito não foi informado';
				} 
			}

			if($registro[0]['origem_retificacao'] == 2 || $registro[0]['origem_retificacao'] == 3) {//Revisão administrativa ou Determinação judicial
				
				if($registro[0]['tipo_processo'] == ''){
					$campos['tipo_processo'] = 'O tipo de processo não foi informado';
				}

				if($registro[0]['numero_processo'] == ''){
					$campos['numero_processo'] = 'O número do processo não foi informado';
				}

				if($registro[0]['tipo_processo'] == 1) {//Se Tipo de processo for selecionado Administrativo deve ser preenchido exatamente com 17 ou 21 algarismos.
					if($registro[0]['numero_processo'] != '') {
						$numero_processo = $registro[0]['numero_processo'];

						if(strlen($numero_processo) != 17 || strlen($numero_processo) != 21) {
							$campos['numero_processo'] = 'O número do processo deve ter 17 ou 21 algarismos';
						}
					}
				}

				if($registro[0]['tipo_processo'] == 2) {

					if($registro[0]['numero_processo'] != '') {
						$numero_processo = $registro[0]['numero_processo'];

						if(strlen($numero_processo) != 20) {
							$campos['numero_processo'] = 'O número do processo deve ter 20 algarismos';
						}
					}

				}

				if($registro[0]['tipo_processo'] == 3) {

					if($registro[0]['numero_processo'] != '') {
						$numero_processo = $registro[0]['numero_processo'];

						if(strlen($numero_processo) != 10) {
							$campos['numero_processo'] = 'O número do processo deve ter 10 algarismos';
						}
					}

				}
			}

			if($registro[0]['onus_requisicao'] != ''){
				$campos['onus_requisicao'] = 'O campo Ônus da cessăo/requisição só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 14';
			}

			if($registro[0]['data_inicio_p_aquisitivo'] != ''){
				$campos['data_inicio_p_aquisitivo'] = 'O campo Data Início Período Aquisitivo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 15';
			}

			if($registro[0]['data_fim_p_aquisitivo'] != ''){
				$campos['data_fim_p_aquisitivo'] = 'O campo Data Fim Período Aquisitivo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 15';
			}

			if($registro[0]['onus_remuneracao'] != ''){
				$campos['onus_remuneracao'] = 'O campo 	Ônus da remuneração só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 24';
			}

			if($registro[0]['renumeracao_cargo'] != ''){
				$campos['renumeracao_cargo'] = 'O campo Renumeração do Cargo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 22';
			}
		}


		if(($registro[0]['tabela_18_esocial'] == 14)){

			if($registro[0]['onus_requisicao'] == ''){
				$campos['onus_requisicao'] = 'O Onus da Requisição não foi informado';
			}
			
			if($registro[0]['codigo_documento_entidade'] == ''){
				$campos['codigo_documento_entidade'] = 'O CNPJ não foi informado';
			}

			if($registro[0]['origem_retificacao'] != '') {
				$campos['origem_retificacao'] = 'O campo Origem Retificação só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['numero_processo'] != '') {
				$campos['numero_processo'] = 'O campo Numero processo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['tipo_processo'] != '') {
				$campos['tipo_processo'] = 'O campo Tipo processo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['tp_acid_transito'] != ''){
				$campos['tp_acid_transito'] = 'O Acidente de Transito só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['tipo_acidente_transito'] != ''){
				$campos['tipo_acidente_transito'] = 'O campo Tipo de acidente de transito só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}
			
			if($registro[0]['data_inicio_p_aquisitivo'] != ''){
				$campos['data_inicio_p_aquisitivo'] = 'O campo Data Início Período Aquisitivo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 15';
			}

			if($registro[0]['data_fim_p_aquisitivo'] != ''){
				$campos['data_fim_p_aquisitivo'] = 'O campo Data Fim Período Aquisitivo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 15';
			}

			if($registro[0]['onus_remuneracao'] != ''){
				$campos['onus_remuneracao'] = 'O campo 	Ônus da remuneração só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 24';
			}

			if($registro[0]['renumeracao_cargo'] != ''){
				$campos['renumeracao_cargo'] = 'O campo Renumeração do Cargo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 22';
			}
		}

		if(($registro[0]['tabela_18_esocial'] == 15)){
			
			if($registro[0]['data_inicio_p_aquisitivo'] == ''){
				$campos['data_inicio_p_aquisitivo'] = 'A data de início do período aquisitivo não foi informada';
			}

			if($registro[0]['data_fim_p_aquisitivo'] == ''){
				$campos['data_fim_p_aquisitivo'] = 'A data de fim do período aquisitivo não foi informada';
			}

			if(!empty($registro[0]['data_inicio_p_aquisitivo']) && !empty($registro[0]['data_fim_p_aquisitivo'])) {

				$data_inicio_p_aquisitivo = strtotime(AppModel::dateToDbDate2($registro[0]['data_inicio_p_aquisitivo']));
				$data_fim_p_aquisitivo = strtotime(AppModel::dateToDbDate2($registro[0]['data_fim_p_aquisitivo']));
	
				if($data_inicio_p_aquisitivo > $data_fim_p_aquisitivo) {
					$campos['data_inicio_p_aquisitivo']	= 'Data Fim Período Aquisitivo deve ser maior à Data Início Período Aquisitivo.';
				}

				if($data_fim_p_aquisitivo < $data_inicio_p_aquisitivo) {
					$campos['data_fim_p_aquisitivo'] = 'Data Fim Período Aquisitivo deve ser maior à Data Início Período Aquisitivo.';
				}
			}

			if($registro[0]['origem_retificacao'] != '') {
				$campos['origem_retificacao'] = 'O campo Origem Retificação só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['numero_processo'] != '') {
				$campos['numero_processo'] = 'O campo Numero processo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['tipo_processo'] != '') {
				$campos['tipo_processo'] = 'O campo Tipo processo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['tp_acid_transito'] != ''){
				$campos['tp_acid_transito'] = 'O Acidente de Transito só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['tipo_acidente_transito'] != ''){
				$campos['tipo_acidente_transito'] = 'O campo Tipo de acidente de transito só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}
			
			if($registro[0]['onus_remuneracao'] != ''){
				$campos['onus_remuneracao'] = 'O campo 	Ônus da remuneração só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 24';
			}

			if($registro[0]['renumeracao_cargo'] != ''){
				$campos['renumeracao_cargo'] = 'O campo Renumeração do Cargo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 22';
			}
		}


		if(($registro[0]['tabela_18_esocial'] == 24)){
			
			if($registro[0]['onus_remuneracao'] == ''){
				$campos['onus_remuneracao'] = 'O Onus da Remuneração não foi informado';
			}

			if($registro[0]['codigo_documento_entidade'] == '') {
				$campos['codigo_documento_entidade'] = 'O CNPJ não foi informado';
			}

			if($registro[0]['origem_retificacao'] != '') {
				$campos['origem_retificacao'] = 'O campo Origem Retificação só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['numero_processo'] != '') {
				$campos['numero_processo'] = 'O campo Numero processo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['tipo_processo'] != '') {
				$campos['tipo_processo'] = 'O campo Tipo processo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['tp_acid_transito'] != ''){
				$campos['tp_acid_transito'] = 'O Acidente de Transito só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['tipo_acidente_transito'] != ''){
				$campos['tipo_acidente_transito'] = 'O campo Tipo de acidente de transito só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			} 

			if($registro[0]['data_inicio_p_aquisitivo'] != ''){
				$campos['data_inicio_p_aquisitivo'] = 'O campo Data Início Período Aquisitivo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 15';
			}

			if($registro[0]['data_fim_p_aquisitivo'] != ''){
				$campos['data_fim_p_aquisitivo'] = 'O campo Data Fim Período Aquisitivo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 15';
			}

			if($registro[0]['renumeracao_cargo'] != ''){
				$campos['renumeracao_cargo'] = 'O campo Renumeração do Cargo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 22';
			}
		}

		if(($registro[0]['tabela_18_esocial'] == 22)) {

			if($registro[0]['renumeracao_cargo'] == ''){
				$campos['renumeracao_cargo'] = 'A Renumeração do Cargo não foi informada';
			}

			if($registro[0]['codigo_documento_entidade'] == '') {
				$campos['codigo_documento_entidade'] = 'O CNPJ não foi informado';
			}

			if($registro[0]['origem_retificacao'] != '') {
				$campos['origem_retificacao'] = 'O campo Origem Retificação só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['numero_processo'] != '') {
				$campos['numero_processo'] = 'O campo Numero processo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['tipo_processo'] != '') {
				$campos['tipo_processo'] = 'O campo Tipo processo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['tp_acid_transito'] != ''){
				$campos['tp_acid_transito'] = 'O Acidente de Transito só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['tipo_acidente_transito'] != ''){
				$campos['tipo_acidente_transito'] = 'O campo Tipo de acidente de transito só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}
			
			if($registro[0]['data_inicio_p_aquisitivo'] != ''){
				$campos['data_inicio_p_aquisitivo'] = 'O campo Data Início Período Aquisitivo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 15';
			}

			if($registro[0]['data_fim_p_aquisitivo'] != ''){
				$campos['data_fim_p_aquisitivo'] = 'O campo Data Fim Período Aquisitivo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 15';
			}

			if($registro[0]['renumeracao_cargo'] != ''){
				$campos['renumeracao_cargo'] = 'O campo Renumeração do Cargo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 22';
			}
		}

		if(($registro[0]['tabela_18_esocial'] == 21)){
			if($registro[0]['obs_afastamento'] == ''){
				$campos['obs_afastamento'] = 'A Observação (eSocial) não foi informada';
			}

			if($registro[0]['origem_retificacao'] != '') {
				$campos['origem_retificacao'] = 'O campo Origem Retificação só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['numero_processo'] != '') {
				$campos['numero_processo'] = 'O campo Numero processo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['tipo_processo'] != '') {
				$campos['tipo_processo'] = 'O campo Tipo processo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['tp_acid_transito'] != ''){
				$campos['tp_acid_transito'] = 'O Acidente de Transito só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}

			if($registro[0]['tipo_acidente_transito'] != ''){
				$campos['tipo_acidente_transito'] = 'O campo Tipo de acidente de transito só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 01 ou 03';
			}
			
			if($registro[0]['data_inicio_p_aquisitivo'] != ''){
				$campos['data_inicio_p_aquisitivo'] = 'O campo Data Início Período Aquisitivo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 15';
			}

			if($registro[0]['data_fim_p_aquisitivo'] != ''){
				$campos['data_fim_p_aquisitivo'] = 'O campo Data Fim Período Aquisitivo só deve ser preenchido quando o Motivo da licença Tabela 18 - eSocial for igual a 15';
			}
		}

		if(!empty($registro[0]['tabela_18_esocial'])) {
			
			$esocial 		=& ClassRegistry::init('Esocial');
			$retorno_tabela18 = $esocial->find('first',  array('conditions' => array('tabela' => 18, 'codigo_descricao' => $registro[0]['tabela_18_esocial'])));

			if(!$retorno_tabela18) {
				$campos['tabela_18_esocial'] = 'Motivo de licença tabela 18 - eSocial está inválido';
			}
		}

		/*
		 * Nesta validacao, faz a busca no banco para ver se o usuario ja tem existe CNPJ alocado, caso contrario é necessario ele
		 inserir um diferente.
		*/
		if(($registro[0]['codigo_documento_entidade'])){
			if($Atestados->buscar_cnpj_alocado($registro[0]['codigo_func_setor_cargo'],$registro[0]['codigo_documento_entidade'])){
				$campos['codigo_documento_entidade'] = ' O CNPJ não pode ser o mesmo de alocação do Funcionário.';
			}
		}
		
		/**
		 * Validação do Médico Solicitante do atestado, onde é verificado se o registro existe na tabela RHHealth.dbo.medico, e se os dados de Conselho Profissional (exemplo: CRM,CRO) existem na plataforma, caso contrário, é gerado um novo registro para o médico do atestado importado.
		 */

		if($id_medico_conselho) {
			$medico_solicitante = $Medico->find('first', array('conditions' => array('numero_conselho' => $id_medico_conselho)));

			if(!$medico_solicitante) {
				$alertas['medico_solicitante']	= 'inclusao';
				$alertas['conselho_classe'] 	= 'inclusao';
			} else {
				if(!empty($id_medico_conselho)) {
					$id_medico_conselho = str_replace('.','',$id_medico_conselho);
					$id_medico_conselho = str_replace('-','',$id_medico_conselho);
					$id_medico_conselho = str_replace('/','',$id_medico_conselho);
					$medico_solicitante['Medico']['numero_conselho'] = str_replace('.','',$medico_solicitante['Medico']['numero_conselho']);
					$medico_solicitante['Medico']['numero_conselho'] = str_replace('-','',$medico_solicitante['Medico']['numero_conselho']);
					$medico_solicitante['Medico']['numero_conselho'] = str_replace('/','',$medico_solicitante['Medico']['numero_conselho']);
					if($id_medico_conselho != $medico_solicitante['Medico']['numero_conselho']) {
						$campos['conselho_classe'] = 'Médico Solicitante sem cadastro com identificação de conselho profissional importado';
						$alertas['conselho_classe'] = 'inclusao';
					}
				}

				if(!empty($sigla_conselho)) {
					$sigla_conselho = $registro[0]['sigla_conselho'];

					// $Conselho->find('sql',  array('conditions' => array('descricao' => $sigla_conselho), 'recursive' => -1));
					// debug($conselho);exit;

					if(!$Conselho->find('first',  array('conditions' => array('descricao' => $sigla_conselho), 'recursive' => -1))) {
						$campos['sigla_conselho'] = 'Conselho Profissional inexistente na plataforma do RHHealth';
						$alertas['sigla_conselho'] = 'inclusao';
					}

					if ($sigla_conselho != $medico_solicitante['ConselhoProfissional']['descricao']) {
						$campos['sigla_conselho'] = 'Médico Solicitante sem relacionamento com o conselho de classe profissional importado';
						$alertas['sigla_conselho'] = 'inclusao';
					}

				}


				if(empty($uf)) {
					if($uf != $medico_solicitante['Medico']['conselho_uf']) {
						$campos['sigla_conselho'] = 'Estado do Conselho Profissional divergente para o Código de Conselho importado';
					}
				}
			}
		} else if (empty($registro[0]['medico_solicitante'])) {
			$campos['medico_solicitante'] = 'Médico solicitante não informado';
		}

		/**
		 * Validação do código de CID (Motivo médico de afastamento), caso não exista, é incluído na tabela RHHealth.dbo.cid
		 */
		if (empty($registro[0]['codigo_cid']) && !empty($registro[0]['nome_cid'])) {
			$campos['codigo_cid'] = 'Descrição do CID informado sem identificação do código do mesmo';
		} elseif(!empty($registro[0]['codigo_cid'])) {
			$codigo_cid = $CID->find('first',  array('conditions' => array('codigo_cid10' => $registro[0]['codigo_cid'])));
			if(!$codigo_cid) {
				$campos['codigo_cid'] = 'Código Inexistente de CID na plataforma RHHealth';
			}
		}

		/**
		 * Validação do código de Tipo de Afastamento e o Motivo de Afastamento do Atestado Funcionário 
		 */
		if (empty($registro[0]['motivo_licenca']) && !empty($registro[0]['tipo_licenca'])) {
			$campos['motivo_licenca']	= 'Motivo de licença não informado';		
		} else if (!empty($registro[0]['motivo_licenca']) && empty($registro[0]['tipo_licenca'])) {
			$campos['motivo_licenca']	= 'Motivo de licença informado sem o tipo de fastamento';
			$campos['tipo_licenca']		= 'Tipo de afastamento não informado';
		} else if(!empty($registro[0]['motivo_licenca']) && !empty($registro[0]['tipo_licenca'])) {
			
			$motivo 		=& ClassRegistry::init('MotivoAfastamento');
			$retorno_motivo = $motivo->find('first',  array('conditions' => array('UPPER(descricao)' => strtoupper($registro[0]['motivo_licenca']))));
			
			if(!$retorno_motivo) {
				$campos['motivo_licenca'] = 'Motivo de licença inexistente na plataforma do RHHealth';
			} else {

				$retorno_tipo = $tipo->find('first',  array('conditions' => array('UPPER(descricao)' => strtoupper($registro[0]['tipo_licenca']))));

				if(!$retorno_tipo) {
					$campos['tipo_licenca'] = 'Tipo de afastamento inexistente na plataforma do RHHealth.';
				} else {

					if($retorno_tipo['TipoAfastamento']['codigo'] != $retorno_motivo['MotivoAfastamento']['codigo_tipo_afastamento']) {
						$campos['motivo_licenca'] = 'Tipo de Afastamento importado divergente do relacionado ao Motivo de Licença do atestado médico importado.';
					}
				}
			}
		}

		/**
		 * Verificação de divergência de cargo e setor
		 */
		if (empty($registro[0]['setor_descricao'])) {
			$campos['nome_setor']	= 'Setor não encontrado para o funcionário informado.';
		}

		if (empty($registro[0]['cargo_descricao'])) {
			$campos['nome_cargo']	= 'Cargo não encontrado para o funcionário informado.';
		}

		$depara = $this->depara();

		/**
		 * Para os campos que não possuem a informação na plataforma do RH Health, o registro será incluído.
		 */
        foreach ($depara as $campo_planilha => $campo_tabela) {
            $registro[0][$campo_tabela] = trim($registro[0][$campo_tabela]);
            $registro[0][$campo_planilha] = trim($registro[0][$campo_planilha]);
            if ($codigo_status_importacao == StatusImportacao::SEM_PROCESSAR && !empty($registro[0][$campo_planilha])) {
            	$matches = array();
            	preg_match('/'.$campo_planilha.'/','nome_cargo|nome_setor|setor_descricao|cargo_descricao',$matches);
            	preg_match('/'.$campo_tabela.'/','nome_cargo|nome_setor|setor_descricao|cargo_descricao',$matches);
                if (empty($registro[0][$campo_tabela]) && count($matches) == 0) {
                    $alertas[$campo_planilha] = 'inclusao';
                }
            } else {
                $alertas[$campo_planilha] = '';
            }
        }

		// debug($campos);exit;

        //debug($registro);exit;

		return array('alertas' => $alertas, 'validacoes' => $campos);	
	} //fim alertasRegistroCadastros

	/**
	 * Método de Verificação de existência da informação de Conselho Profissional na tabela RHHealth.dbo.conselho_profissional do médico solicitante do atestado, caso não exista, um registro de Conselho será gerada na tabela RHHealth.dbo.conselho_profissinal
	 * @param array $data Dados para busca, validação e geração de registro do Conselho Profissional
	 * @return integer Código de identificação do registro de Conselho Profissional existente ou gerado
	 */
	function importarConselhoProfissional($data) {
		$Conselho 			=& ClassRegistry::init('ConselhoProfissional');
		$retorno_conselho	= array("codigo_conselho" => 0, "invalidFields" => "");
		
		$dados = array(
			'conditions' => array(
				'descricao' => strtoupper($data['sigla_conselho'])
			),
			'recursive' => -1
		);
		
		$data = array(
			'descricao'	=> strtoupper($data['sigla_conselho']),
		);

		// $this->log($Conselho->find('sql',$dados),'debug');

		if(!$conselho_id = $Conselho->find('first',$dados)) {
			try {
				$retorno_conselho['codigo_conselho'] = $Conselho->incluir($data);
			} catch(Exception $e) {
				$retorno_conselho['invalidFields'] .= $e->getMessage();
			}
		} else {
			$retorno_conselho['codigo_conselho'] = $conselho_id['ConselhoProfissional']['codigo'];
		}
		return $retorno_conselho;
		
	}

	/**
	 * Método de Verificação de existência da informação de registro de identificação de motivo de afastamento/licença do funcionário existe na tabela RHHealth.dbo.motivos_afastamento
	 * @param type $data Dados para busca, validação e geração de registro do motivo de afastamento/licença
	 * @return integer Código de identificação do motivo de afastamento/licença existente ou gerado
	 */
	function importarMotivoLicenca($data) {
		$motivo =& ClassRegistry::init('MotivoAfastamento');
		$retorno_motivo = array("codigo_motivo_licenca" => 0, "invalidFields" => "");

		$dados = array('conditions' => array(
			'UPPER(descricao)'			=> strtoupper($data['motivo_licenca']),
			'codigo_tipo_afastamento'	=> $data['tipo_afastamento']
			)
		);
		$data = array(
			'descricao'					=> strtoupper($data['motivo_licenca']),
			'codigo_tipo_afastamento'	=> $data['tipo_afastamento'],
			'ativo'						=> 1
		);

		if(!$motivo_id = $motivo->find('first',$dados)) {
			try {
				if(!$motivo->incluir($data,false)) {
					$retorno_motivo['invalidFields'] .= implode('\n',$motivo->validationErrors);
				} else {
					$retorno_motivo['codigo_motivo_licenca'] =  $motivo->getLastInsertId();
				}
			} catch(Exception $e) {
				$retorno_motivo['invalidFields'] .= $e->getMessage();
			}
		} else {
			$retorno_motivo['codigo_motivo_licenca'] = $motivo_id['MotivoAfastamento']['codigo'];
		}
		return $retorno_motivo;
	}

	/**
	 * Método de Verificação de existência da informação de registro de identificação de tipo de afastamento do funcionário existe na tabela RHHealth.dbo.tipos_afastamento
	 * @param type $data Dados para busca, validação e geração de registro do tipo de afastamento
	 * @return integer Código de identificação do motivo de licença existente ou gerado
	 */
	function importarTipoAfastamento($data) {
		$tipo 			=& ClassRegistry::init('TipoAfastamento');
		$retorno_tipo 	= array("codigo_tipo_afastamento" => 0, "invalidFields" => "");

		$dados = array('conditions' => array(
			'UPPER(descricao)' => strtoupper($data['tipo_licenca'])
			)
		);
		$data = array(
			'descricao'					=> strtoupper($data['tipo_licenca']),
			'exibe_relatorio'			=> 0,
			'considera_afastamento'		=> 0,
			'ativo'						=> 1
		);

		if(!$tipo_id = $tipo->find('first',$dados)) {
			try {
				if(!$tipo_id['TipoAfastamento']['codigo'] = $tipo->incluir($data)) {
					$retorno_tipo['invalidFields'] .= implode('\n',$tipo->validationErrors);
				} else {
					$retorno_tipo['codigo_tipo_afastamento'] = $tipo_id['TipoAfastamento']['codigo'];
				}
			} catch(Exception $e) {
				$retorno_tipo['invalidFields'] .= $e->getMessage();
			}
		} else {
			$retorno_tipo['codigo_tipo_afastamento'] = $tipo_id['TipoAfastamento']['codigo'];
		}
		return $retorno_tipo;
	}

	/**
	 * Método de Verificação de existência da informação do médico solicitante do atestado, caso não exista, um registro do médico será gerado na tabela RHHealth.dbo.medico
	 * @param array $data Dados para busca, validação e geração de registro do Médico
	 * @return integer Código de identificação do registro de médico existente ou gerado
	 */
	function importarMedico($data) {
		$medico 		=& ClassRegistry::init('Medico');
		$retorno_medico = array("codigo_medico" => 0, "invalidFields" => "");
		$dados = array('conditions' => array(
			'numero_conselho'	=> $data['conselho_classe'],
			'conselho_uf'		=> $data['UF'],
			'codigo_conselho_profissional'	=> $data['codigo_conselho'],
		));

		$data = array('Medico' => array(
			'nome'							=> strtoupper($data['medico_solicitante']),
			'numero_conselho'				=> $data['conselho_classe'],
			'conselho_uf'					=> $data['UF'],
			'codigo_conselho_profissional'	=> $data['codigo_conselho'],
			'especialidade'					=> $data['especialidade'],
			'ativo'							=> 1
		));	

		if(!$medico_id = $medico->find('first',$dados)) {
			try {
				if($medico->incluir($data,false)) {
					$retorno_medico['codigo_medico'] = $medico->getLastInsertId();
				}
			} catch(Exception $e) {
				$retorno_medico['invalidFields'] .= $e->getMessage();
			}
		} else {
			$retorno_medico['codigo_medico'] = $medico_id['Medico']['codigo'];
		}
		return $retorno_medico;
	}

	/**
	 * Método de Verificação de existência da informação do atestado para o CPF+Data de Início de Afastamento informados de atestado importado, caso não exista, um registro do atestado será gerado na tabela RHHealth.dbo.atestados e de Atestado+CID será gerado na tabela RHHealth.dbo.atestados_cid
	 * @param array $data Dados para busca, validação e geração de registro do Atestado e Atestado CID
	 * @return integer Código de identificação do registro de Atestado gerado
	 */
	function importarAtestado($data) {
		$atestado 			=& ClassRegistry::init('Atestado');
		$atestadoCid 		=& ClassRegistry::init('AtestadoCid');
		$retorno_atestado	= array("codigo_atestado" => 0, "invalidFields" => "");

		$inclusao = $data;

		/**
		 * Se o afastamento for menor que um dia, seta com habilitado o afastamento em horas do atestado
		 */ 
		if($inclusao['Atestado']['dias'] < 1) {
			$inclusao['Atestado']['habilita_afastamento_em_horas'] = 1;
		} else {
			$inclusao['Atestado']['habilita_afastamento_em_horas'] = 0;
		}
 
		$dados = array('conditions' => array(
			'Funcionario.cpf' => $data['Atestado']['cpf'],
			'Atestado.data_afastamento_periodo' => AppModel::dateToDbDate2($data['Atestado']['data_afastamento_periodo'])
			)
		);

		/*$atestado->bindModel(array('belongsTo' => array(
			'ClienteUnidade' => array('className' => 'Cliente', 'foreignKey' => false, 'conditions' => array(
				"ClienteUnidade.codigo_empresa = '" . $data['Atestado']['codigo_empresa'] . "'",
			)),
			'Funcionario' => array('className' => 'Funcionario', 'foreignKey' => false, 'conditions' => array(
				"Funcionario.cpf = '" . $data['Atestado']['cpf'] . "'"
			)),
			'ClienteFuncionario' => array('className' => 'ClienteFuncionario', 'foreignKey' => false, 'conditions' => array(
				"ClienteFuncionario.codigo_empresa = '" . $data['Atestado']['codigo_empresa']  . "'",
				'ClienteFuncionario.codigo_cliente_matricula = ClienteUnidade.codigo',
				'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
			)),
			'FuncionarioSetorCargo' => array('className' => 'FuncionarioSetorCargo', 'foreignKey' => false, 'conditions' => array(
				"FuncionarioSetorCargo.codigo_empresa = '" . $data['Atestado']['codigo_empresa']  . "'",
				'ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario',
				'ClienteFuncionario.codigo = Atestado.codigo_cliente_funcionario',
				'FuncionarioSetorCargo.codigo = Atestado.codigo_func_setor_cargo'
			)),
		)));*/


		$atestado->bindModel(array('belongsTo' => array(
			'ClienteFuncionario' => array('className' => 'ClienteFuncionario', 'foreignKey' => false, 'conditions' => array(
				"ClienteFuncionario.codigo = Atestado.codigo_cliente_funcionario",
				"ClienteFuncionario.codigo_empresa = '" . $data['Atestado']['codigo_empresa']  . "'",
			)),
			'Funcionario' => array('className' => 'Funcionario', 'foreignKey' => false, 'conditions' => array(
				'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
				"Funcionario.cpf = '" . $data['Atestado']['cpf'] . "'"
			)),
			'ClienteUnidade' => array('className' => 'Cliente', 'foreignKey' => false, 'conditions' => array(
				'ClienteFuncionario.codigo_cliente_matricula = ClienteUnidade.codigo',
				"ClienteUnidade.codigo_empresa = '" . $data['Atestado']['codigo_empresa'] . "'",
			)),
			'FuncionarioSetorCargo' => array('className' => 'FuncionarioSetorCargo', 'foreignKey' => false, 'conditions' => array(
				"FuncionarioSetorCargo.codigo_empresa = '" . $data['Atestado']['codigo_empresa']  . "'",
				'ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario',				
				'FuncionarioSetorCargo.codigo = Atestado.codigo_func_setor_cargo'
			)),
		)));

		// debug($atestado->find('sql',$dados));exit;


		if(!$atestado_id = $atestado->find('first',$dados)) {
			try {
				$atestado->validationErrors = array();
				if($atestado->incluir($inclusao)) {
					$retorno_atestado['codigo_atestado'] = $atestado->getLastInsertId();
				} else {
					$retorno_atestado['invalidFields'] .= implode('\n',$atestado->validationErrors);
				}
			} catch(Exception $e) {
				$retorno_atestado['invalidFields'] .= $e->getMessage();
			}
		} else {
			$retorno_atestado['invalidFields'] .= "Atestado já existente para Funcionário e Data de Afastamento importados.";
		}


		if($retorno_atestado['codigo_atestado'] && $data['Atestado']['codigo_cid']) {
			$data_cid_atestado = array(
				'codigo_atestado'	=> $retorno_atestado['codigo_atestado'],
				'codigo_cid'		=> $data['Atestado']['codigo_cid']
			);

			$dados = array('conditions' => array(
				'codigo_atestado'	=> $retorno_atestado['codigo_atestado'],
				'codigo_cid'		=> $data['Atestado']['codigo_cid']
				)
			);

			if(!$atestado_cid = $atestadoCid->find('first',$dados)) {
				try {
					$atestadoCid->incluir($data_cid_atestado);
				} catch(Exception $e) {
					$retorno_atestado['invalidFields'] .= $e->getMessage();
				}
			}
		}
		return $retorno_atestado;
	}
}