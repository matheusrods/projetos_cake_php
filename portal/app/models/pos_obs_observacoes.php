<?php
class PosObsObservacoes extends AppModel {

	public $name          = 'PosObsObservacoes';
	var    $tableSchema   = 'dbo';
	var    $databaseTable = 'RHHealth';
	var    $useTable      = 'pos_obs_observacao';
	var    $primaryKey    = 'codigo';
	var    $recursive     = 2;

	public $hasMany = array(
        'AcoesMelhoriasAssociadas' => array(
			'className'  => 'PosObsObservacaoAcaoMelhoria',
			'joinTable'  => 'pos_obs_observacao_acao_melhoria',
			'foreignKey' => 'obs_observacao_id',
		)
    );
							   
	public function obterTodosObservadores()
	{
		$this->Usuario = &ClassRegistry::init('Usuario');

		if (!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])) {
			$conditions[] = "Usuario.codigo_cliente IN (select codigo_cliente from grupos_economicos_clientes where codigo_grupo_economico IN (select codigo from grupos_economicos where codigo_cliente " . $this->rawsql_codigo_cliente($_SESSION['Auth']['Usuario']['codigo_cliente']) . " ))";
		}

		$conditions[] = "Usuario.ativo = 1"; //usuario tem q estar ativo
		$conditions[] = "Uperfil.codigo = 50"; //perfil Pos

		$joins = array(
			array(
				'table'      => 'uperfis',
				'alias'      => 'Uperfil',
				'type'       => 'INNER',
				'conditions' => array('Uperfil.codigo = Usuario.codigo_uperfil')
			),
		);

		$observador = $this->Usuario->find('list', array('fields' => array('Usuario.codigo', 'Usuario.nome'), 'conditions' => $conditions, 'joins' => $joins));

		return $observador;
	}

	public function obterParticipantes($codigo_observacao)
	{
		$this->PosObsParticipantes = &ClassRegistry::init('PosObsParticipantes');
		$participantes = $this->PosObsParticipantes->obterParticipantes($codigo_observacao);
		return $participantes;
	}

	public function obterLocais($codigo_observacao)
	{
		$this->PosObsLocais = &ClassRegistry::init('PosObsLocais');
		$locais = $this->PosObsLocais->obterLocais($codigo_observacao);
		return $locais;
	}

	public function obterAnexos($codigo_observacao)
	{
		$this->PosObsAnexos = &ClassRegistry::init('PosObsAnexos');
		$anexos = $this->PosObsAnexos->obterAnexos($codigo_observacao);
		return $anexos;
	}

	public function obterTodosRiscos()
	{
		$this->PosObsRiscos = &ClassRegistry::init('PosObsRiscos');

		$fields = array(
			'PosObsRiscos.codigo_pos_obs_observacao AS  codigo_observacao',
			'RiscosTipo.descricao AS risco_tipo_descricao',
			'RiscosTipo.codigo AS risco_tipo_codigo',
			'RiscosImpactos.descricao AS risco_impacto_descricao',
			'RiscosImpactos.codigo AS risco_impacto_codigo',
			'PerigosAspectos.descricao AS perigo_aspecto_descricao',
			'PerigosAspectos.codigo AS perigo_aspecto_codigo'
		);

		$this->PosObsRiscos->bindRiscos();
		$dados_riscos = $this->PosObsRiscos->find('all', array(
			'fields' => $fields,
		));
		$this->PosObsRiscos->unbindRiscos();

		$dados_riscos_limpo = array();

		foreach ($dados_riscos as $key => $risco) {
			unset(
				$risco['RiscosImpactos'],
				$risco['PerigosAspectos'],
				$risco['RiscosTipo']
			);

			$dados_riscos_limpo[$key] = $risco;
		}

		return $dados_riscos_limpo;
	}

	public function obterRiscos($codigo_observacao)
	{
		$this->PosObsRiscos = &ClassRegistry::init('PosObsRiscos');
		$riscos = $this->PosObsRiscos->obterRiscos($codigo_observacao);
		return $riscos;
	}

	public function obterRelatorioRealizadas($filtros = array(), $usuarioResponsavel = null, $deveExportar = null)
	{
		$conditions = $this->converteFiltrosEmConditions($filtros);
		//$conditions['PosObsObservacoes.status'] = 1; //Apenas não classificadas

		$fields = array(
			'Cliente.codigo',
			'Cliente.razao_social',
			'Cliente.nome_fantasia',
			'Local.descricao',
			'Setor.codigo',
			'Setor.descricao',
			'ClienteOpco.codigo',
			'ClienteOpco.descricao',
			'ClienteBu.codigo',
			'ClienteBu.descricao',
			'PosObsObservacoes.codigo',
			'Categoria.descricao',
			'Usuario.codigo',
			'Usuario.nome',
			'CAST(PosObsObservacoes.data_observacao AS DATE) as dt_obs',
			'CAST(PosObsObservacoes.data_observacao AS TIME) as hr_obs',
			'PosObsObservacoes.descricao',
			'PosObsObservacoes.descricao_usuario_observou',
			'PosObsObservacoes.descricao_usuario_sugestao',
			'PosObsObservacoes.descricao_usuario_acao',
			$usuarioResponsavel ? 'AcoesMelhoriasStatusResponsavel.descricao'  : 'AcoesMelhoriasStatus.descricao',
		);

		$joins = array(
			array(
				'table'      => 'pos_obs_locais',
				'alias'      => 'Locais',
				'type'       => 'INNER',
				'conditions' => array('PosObsObservacoes.codigo = Locais.codigo_pos_obs_observacao')
			),
			array(
				'table'      => 'pos_obs_local',
				'alias'      => 'Local',
				'type'       => 'LEFT',
				'conditions' => array('PosObsObservacoes.codigo_pos_obs_local = Local.codigo')
			),
			array(
				'table'      => 'cliente',
				'alias'      => 'Cliente',
				'type'       => 'INNER',
				'conditions' => array('PosObsObservacoes.codigo_unidade = Cliente.codigo')
			),
			array(
				'table'      => 'pos_categorias',
				'alias'      => 'Categoria',
				'type'       => 'INNER',
				'conditions' => array('Categoria.codigo = PosObsObservacoes.codigo_pos_categoria')
			),
			array(
				'table'      => 'pos_obs_participantes',
				'alias'      => 'Participantes',
				'type'       => 'INNER',
				'conditions' => array('Participantes.codigo_pos_obs_observacao = PosObsObservacoes.codigo')
			),
			array(
				'table'      => 'usuario',
				'alias'      => 'Usuario',
				'type'       => 'INNER',
				'conditions' => array('Participantes.codigo_usuario = Usuario.codigo')
			),
			array(
				'table'      => 'acoes_melhorias_status',
				'alias'      => 'AcoesMelhoriasStatus',
				'type'       => 'INNER',
				'conditions' => array('PosObsObservacoes.codigo_status = AcoesMelhoriasStatus.codigo')
			),
			array(
				'table'      => 'acoes_melhorias_status',
				'alias'      => 'AcoesMelhoriasStatusResponsavel',
				'type'       => 'LEFT',
				'conditions' => 'AcoesMelhoriasStatusResponsavel.codigo = PosObsObservacoes.codigo_status_responsavel',
			),
			array(
				'table'      => 'funcionarios',
				'alias'      => 'Funcionarios',
				'type'       => 'LEFT',
				'conditions' => array('Funcionarios.cpf = Participantes.cpf')
			),
			array(
				'table'      => 'cliente_funcionario',
				'alias'      => 'ClienteFuncionario',
				'type'       => 'LEFT',
				'conditions' => array('ClienteFuncionario.codigo_funcionario =  Funcionarios.codigo AND ClienteFuncionario.codigo_cliente = PosObsObservacoes.codigo_cliente')
			),
			array(
				'table'      => 'funcionario_setores_cargos',
				'alias'      => 'FuncionarioSetorCargo',
				'type'       => 'LEFT',
				'conditions' => array('FuncionarioSetorCargo.codigo = (SELECT TOP 1 fs.codigo FROM funcionario_setores_cargos fs INNER JOIN cliente c ON fs.codigo_cliente_alocacao = c.codigo AND c.e_tomador <> 1 WHERE fs.codigo_cliente_funcionario = ClienteFuncionario.codigo AND fs.data_fim IS NULL order by fs.codigo DESC)')
			),
			array(
				'table'      => 'setores',
				'alias'      => 'Setor',
				'type'       => 'LEFT',
				'conditions' => array('Setor.codigo = FuncionarioSetorCargo.codigo_setor')
			),
			array(
				'table'      => 'cliente_opco',
				'alias'      => 'ClienteOpco',
				'type'       => 'LEFT',
				'conditions' => array('Locais.codigo_cliente_opco = ClienteOpco.codigo')
			),
			array(
				'table'      => 'cliente_bu',
				'alias'      => 'ClienteBu',
				'type'       => 'LEFT',
				'conditions' => array('Locais.codigo_cliente_bu = ClienteBu.codigo')
			)
		);

		$pos_obs_observacao = array(
			'fields'     => $fields,
			'joins'      => $joins,
			'conditions' => $conditions,
			'limit'      => 20,
			'order'      => "Cliente.nome_fantasia",
			'recursive'  => 2
		);

		if ($deveExportar) { //Caso seja query para xls

			$fields[] = 'Cliente.nome_fantasia';

			return $this->find(
				'sql',
				array(
					'joins'      => $joins,
					'fields'     => $fields,
					'conditions' => $conditions,
					'order'      => array("Cliente.nome_fantasia", 'PosObsObservacoes.codigo')
				)
			);
		}

		return $pos_obs_observacao;
	}

	public function obterRelatorioAnaliseQualidade( $conditions = array(), $export = null ){

		$conditions[] = 'PosObsObservacoes.status IN (0, 2)';

        $fields = array(
            'Cliente.codigo',
            'Cliente.razao_social',
            'Cliente.nome_fantasia' ,
            'Cliente.codigo_documento',
            'PosObsObservacoes.codigo',
            'CAST(PosObsObservacoes.data_observacao AS DATE) as dt_obs',
            'CAST(PosObsObservacoes.data_observacao AS TIME) as hr_obs',
            'PosObsObservacoes.codigo_pos_categoria',
            'PosObsObservacoes.codigo_unidade',
            'PosObsObservacoes.descricao_usuario_observou',
            'PosObsObservacoes.descricao_usuario_acao',
            'PosObsObservacoes.descricao_usuario_sugestao',
            'PosObsObservacoes.codigo_local_descricao',
            'PosObsObservacoes.descricao',
            'PosObsObservacoes.status',
            'PosObsObservacoes.codigo_usuario_status',
            'PosObsObservacoes.data_status',
            'PosObsObservacoes.descricao_status',
            'PosObsObservacoes.qualidade_avaliacao',
            'PosObsObservacoes.qualidade_descricao_complemento',
            'PosObsObservacoes.qualidade_descricao_participantes_tratativa',
			'PosObsObservacoes.observacao_criticidade',
			'PosCriticidade.descricao',
            'PosCategorias.descricao',
            'PosObsObservacoes.data_observacao',
            'PosObsObservacoes.codigo_usuario_inclusao',
            'PosObsObservacoes.codigo_usuario_alteracao',
            'PosObsObservacoes.ativo',
            'PosObsLocais.codigo_cliente_opco',
            'PosObsLocais.codigo_cliente_bu',
            'PosObsLocais.codigo_localidade',
            'PosObsLocais.codigo_local_empresa',
			'ClienteOpco.codigo',
			'ClienteOpco.descricao',
			'ClienteBu.codigo',
			'ClienteBu.descricao',
            'Setor.descricao',
            'Usuario.nome',
			'AcoesMelhoriasStatus.descricao',
			'Local.descricao',
        );

        $joins = array(
            array(
                'table' => 'pos_obs_locais',
                'alias' => 'PosObsLocais',
                'type' => 'INNER',
                'conditions' => array('PosObsObservacoes.codigo = PosObsLocais.codigo_pos_obs_observacao')
            ),
            array(
				'table' => 'grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array('GrupoEconomicoCliente.codigo_cliente = PosObsObservacoes.codigo_unidade')
			),
			array(
				'table' => 'grupos_economicos',
				'alias' => 'GrupoEconomico',
				'type' => 'INNER',
				'conditions' => array('GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo')
			),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => array('GrupoEconomico.codigo_cliente = Cliente.codigo')
			),
            array(
                'table' => 'pos_categorias',
                'alias' => 'PosCategorias',
                'type' => 'INNER',
                'conditions' => array('PosCategorias.codigo = PosObsObservacoes.codigo_pos_categoria')
            ),
            array(
                'table' => 'pos_obs_participantes',
                'alias' => 'Participantes',
                'type' => 'INNER',
                'conditions' => array('Participantes.codigo_pos_obs_observacao = PosObsObservacoes.codigo')
            ),
            array(
                'table' => 'usuario',
                'alias' => 'Usuario',
                'type' => 'INNER',
                'conditions' => array('Participantes.codigo_usuario = Usuario.codigo')
            ),
			array(
				'table'      => 'acoes_melhorias_status',
				'alias'      => 'AcoesMelhoriasStatus',
				'type'       => 'INNER',
				'conditions' => array('PosObsObservacoes.codigo_status = AcoesMelhoriasStatus.codigo')
			),
            array(
                'table' => 'funcionarios',
                'alias' => 'Funcionarios',
                'type' => 'LEFT',
                'conditions' => array('Funcionarios.cpf = Participantes.cpf')
            ),
            array(
                'table' => 'cliente_funcionario',
                'alias' => 'ClienteFuncionario',
                'type' => 'LEFT',
                'conditions' => array('ClienteFuncionario.codigo_funcionario =  Funcionarios.codigo AND ClienteFuncionario.codigo_cliente = PosObsObservacoes.codigo_cliente')
            ),
            array(
                'table' => 'funcionario_setores_cargos',
                'alias' => 'FuncionarioSetorCargo',
                'type' => 'LEFT',
                'conditions' => array('FuncionarioSetorCargo.codigo = (SELECT TOP 1 fs.codigo FROM funcionario_setores_cargos fs INNER JOIN cliente c ON fs.codigo_cliente_alocacao = c.codigo AND c.e_tomador <> 1 WHERE fs.codigo_cliente_funcionario = ClienteFuncionario.codigo AND fs.data_fim IS NULL order by fs.codigo DESC)')
            ),
            array(
                'table' => 'setores',
                'alias' => 'Setor',
                'type' => 'LEFT',
                'conditions' => array('Setor.codigo = FuncionarioSetorCargo.codigo_setor')
            ),

            array(
                'table' => 'cliente_opco',
                'alias' => 'ClienteOpco',
                'type' => 'LEFT',
                'conditions' => array('PosObsLocais.codigo_cliente_opco = ClienteOpco.codigo')
            ),
            array(
                'table' => 'cliente_bu',
                'alias' => 'ClienteBu',
                'type' => 'LEFT',
                'conditions' => array('PosObsLocais.codigo_cliente_bu = ClienteBu.codigo')
            ),
            array(
				'table'      => 'pos_criticidade',
                'alias'      => 'PosCriticidade',
                'type'       => 'LEFT',
                'conditions' => array('PosObsObservacoes.observacao_criticidade = PosCriticidade.codigo'),
			),
			array(
				'table'      => 'pos_obs_local',
				'alias'      => 'Local',
				'type'       => 'LEFT',
				'conditions' => array('PosObsObservacoes.codigo_pos_obs_local = Local.codigo')
			),
        );

        $pos_obs_observacao = array(
            'fields'     => $fields,
            'joins'      => $joins,
            'conditions' => $conditions,
            'limit'      => 20,
            'order'      => "Cliente.nome_fantasia"
        );
		
        if($export){
			unset($pos_obs_observacao['order']);
			unset($pos_obs_observacao['limit']);
        	return  $this->find('all',$pos_obs_observacao);
        }

        return $pos_obs_observacao;
	}

	public function converteFiltrosEmConditions($filtros = array())
	{
		$conditions = array();

		if (isset($filtros['unidades']) && Comum::validarCodigoClean($filtros['unidades']))
			$conditions['Locais.codigo_localidade'] = Comum::clean($filtros['unidades']);

		if (isset($filtros['codigo_setor']) && Comum::validarCodigoClean($filtros['codigo_setor']))
			$conditions['Setor.codigo'] = Comum::clean($filtros['codigo_setor']);

		if (isset($filtros['categoria']) && Comum::validarCodigoClean($filtros['categoria']))
			$conditions['PosObsObservacoes.codigo_pos_categoria'] = Comum::clean($filtros['categoria']);

		if (isset($filtros['id_observacao']) && Comum::validarTextoClean($filtros['id_observacao']))
			$conditions['PosObsObservacoes.codigo'] = Comum::validarTextoClean($filtros['id_observacao']);

		if (isset($filtros['status']) && Comum::validarCodigoClean($filtros['status']))
			$conditions['AcoesMelhoriasStatus.codigo'] = Comum::validarCodigoClean($filtros['status']);

		if (isset($filtros['observador']) && Comum::validarCodigoClean($filtros['observador']))
			$conditions['Participantes.codigo_usuario'] = Comum::validarCodigoClean($filtros['observador']);

		if (isset($filtros['codigo_cliente'])) {
			if (is_array($filtros['codigo_cliente'])) {
				$filtros['codigo_cliente'] = $this->rawsql_codigo_cliente($filtros['codigo_cliente']);
			}
			if (strpos($filtros['codigo_cliente'], 'IN')) {
				$conditions[] = "Cliente.codigo " . $filtros['codigo_cliente'];
			}
			if (strpos($filtros['codigo_cliente'], ',') && !strpos($filtros['codigo_cliente'], 'IN')) {
				$conditions[] = "Cliente.codigo IN (" . $filtros['codigo_cliente'] . ")";
			} else {
				$conditions['Cliente.codigo'] = Comum::validarCodigoClean($filtros['codigo_cliente']);
			}
		}

		if (isset($filtros['descricao']) && Comum::validarTextoClean($filtros['descricao']))
			$conditions['PosObsObservacoes.descricao LIKE'] = '%' . Comum::clean($filtros['descricao']) . '%';

		if (isset($filtros['codigo_documento_cliente']) && Comum::validarCodigoClean($filtros['codigo_documento_cliente']))
			$conditions['Cliente.codigo_documento'] = $filtros['codigo_documento_cliente'];

		if (isset($filtros['razao_social_cliente']) && Comum::validarTextoClean($filtros['razao_social_cliente']))
			$conditions['Cliente.razao_social LIKE'] = '%' . Comum::clean($filtros['razao_social_cliente']) . '%';

		if (isset($filtros['nome_fantasia_cliente']) && Comum::validarTextoClean($filtros['nome_fantasia_cliente']))
			$conditions['Cliente.nome_fantasia LIKE'] = '%' . Comum::clean($filtros['nome_fantasia_cliente']) . '%';

		if (isset($filtros['ativo']) && Comum::validarBoolClean($filtros['ativo']))
			$conditions['PosObsObservacoes.ativo'] = Comum::clean($filtros['ativo']);

		if(isset($filtros['cliente_opco']) && Comum::validarTextoClean($filtros['cliente_opco'])){
			$conditions['ClienteOpco.codigo'] = Comum::clean($filtros['cliente_opco']);
		}

		if(isset($filtros['cliente_bu']) && Comum::validarTextoClean($filtros['cliente_bu'])){
			$conditions['ClienteBu.codigo'] = Comum::clean($filtros['cliente_bu']);
		}

		return $conditions;
	}

    public function filtrosConditions($data){
    	$conditions = array();	      

		if (isset($data['codigo_cliente'])) {
			if (is_array($data['codigo_cliente'])) {
				$data['codigo_cliente'] = $this->rawsql_codigo_cliente($data['codigo_cliente']);
			}
			if (strpos($data['codigo_cliente'], 'IN')) {
				$conditions[] = "Cliente.codigo " . $data['codigo_cliente'];
			}
			if (strpos($data['codigo_cliente'], ',') && !strpos($data['codigo_cliente'], 'IN')) {
				$conditions[] = "Cliente.codigo IN (" . $data['codigo_cliente'] . ")";
			} else {
				$conditions['Cliente.codigo'] = Comum::validarCodigoClean($data['codigo_cliente']);
			}
		}

		if (!empty($data['codigo_cliente_alocacao'])) {			
			$conditions['GrupoEconomicoCliente.codigo_cliente'] = $data['codigo_cliente_alocacao']; 
		}

        if(!empty($data['codigo_setor'])) {
            $conditions['Setor.codigo'] = $data['codigo_setor'];
        }

		if(!empty($data['cliente_opco'])) {
			$conditions['ClienteOpco.codigo'] = $data['cliente_opco'];
		}

		if(!empty($data['cliente_bu'])) {
			$conditions['ClienteBu.codigo'] = $data['cliente_bu'];
		}

		if (!empty($data['categoria'])){
			$conditions['PosObsObservacoes.codigo_pos_categoria'] = $data['categoria'];
		}

		if (!empty($data['id_observacao'])){
			$conditions['PosObsObservacoes.codigo'] = $data['id_observacao'];
		}

		if(!empty($data['observador'])) {
			$conditions['Participantes.codigo_usuario'] = $data['observador'];
		}

		if (isset($data['status']) && Comum::validarCodigoClean($data['status'])) {
			$conditions['AcoesMelhoriasStatus.codigo'] = Comum::clean($data['status']);
		}

		return $conditions;
    }
}
