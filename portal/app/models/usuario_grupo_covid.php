<?php 
class UsuarioGrupoCovid extends AppModel {

    public $name = 'UsuarioGrupoCovid';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'usuario_grupo_covid';
	//public $foreignKeyLog = 'codigo_cliente_questionario';
	public $primaryKey = 'codigo';	
	public $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_usuario_grupo_covid'));

	/**
	 * [converteFiltroEmCondition description]
	 * 
	 * metodo para fazer os filtros do where na query
	 * 
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function converteFiltroEmCondition($data) 
	{
        $conditions = array();

		if (!empty( $data['codigo_cliente'])) {
			$conditions['GrupoEconomico.codigo_cliente'] = $data['codigo_cliente'];
        }

        if (!empty( $data['codigo_cliente_alocacao'])) {
			$conditions['GruposEconomicosClientes.codigo_cliente'] = $data['codigo_cliente_alocacao'];
        }

		if(!empty($data['codigo_setor'])){
			$conditions['Setor.codigo'] = $data['codigo_setor'];
		}

		if(!empty($data['codigo_cargo'])){
			$conditions['Cargo.codigo'] = $data['codigo_cargo'];
		}

		//if(!empty($data['tomadores'])){			
			$conditions['Cliente.e_tomador <>'] = 1;
			$conditions['ClienteAlocacao.e_tomador <>'] = 1;
		//}
		/*
		if(!empty($data['codigo_funcionario'])){
			$conditions['Funcionario.codigo'] = $data['codigo_funcionario'];
		}*/

		if(!empty($data['nome_funcionario'])){
			$conditions['Funcionarios.nome LIKE'] = $data['nome_funcionario'];
		}

		if(!empty($data['matricula'])){
			$conditions['ClienteFuncionario.matricula'] = $data['matricula'];
		}

		if(!empty($data['grupo'])){
			$conditions['UsuarioGrupoCovid.codigo_grupo_covid'] = $data['grupo'];
		}

		if(!empty($data['cpf'])){
			$conditions['UsuarioGrupoCovid.cpf'] = $data['cpf'];
		}

		if(!empty($data['passaporte'])){
			
			if($data['passaporte'] == 2) {
				$data['passaporte'] = '0';
			}

			$conditions['ResultadoCovid.passaporte'] = $data['passaporte'];
			$conditions['ResultadoCovid.data_inclusao >= '] = date('Y-m-d 00:00:00');
		}

		//retorn os dados para a metodo que chamou
        return $conditions;

    }//fim convertFiltrosConditions

	function carregarCombos($codigo_cliente = null) 
	{
		$retorno = array();

		//instancia as models
		$this->Cliente = & ClassRegistry::init('Cliente');
		$this->Cargo = & ClassRegistry::init('Cargo');
		$this->Setor = & ClassRegistry::init('Setor');

		$retorno['unidades'] = $this->Cliente->lista_por_cliente($codigo_cliente);
		$retorno['cargos'] = $this->Cargo->lista_por_cliente($codigo_cliente);
		$retorno['setores'] = $this->Setor->lista_por_cliente($codigo_cliente);

		return $retorno;
	}

	public function paginate( $conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array() ) 
	{
		$joins = null;
		if (isset($extra['joins']))
			$joins = $extra['joins'];
		if (isset($extra['group']))
			$group = $extra['group'];
		return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
	}

	/**
	 * [getFuncionarios description]
	 * 
	 * query para buscar os funcionários que responderam os questionários covid
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function getFuncionarios($conditions, $limit = true)
	{
		
		$fields = array(
			'Cliente.codigo as codigo_cliente',
            'UsuarioGrupoCovid.codigo as codigo',
            'UsuarioGrupoCovid.codigo_usuario as codigo_usuario',
            'FuncionarioSetoresCargos.codigo as codigo_funcionario_setor_cargos',
            "GrupoEconomico.descricao as empresa",
            "Cliente.razao_social as unidade_nome_fantasia",
			"Setor.descricao as setor_descricao",
			"Cargo.descricao as cargo_descricao",
			"Funcionarios.nome as funcionario_nome",
			"RHHealth.publico.ufn_formata_cpf(Funcionarios.cpf) as funcionario_cpf",
			"ClienteFuncionario.codigo as codigo_cliente_funcionario",
			"ClienteFuncionario.matricula as funcionario_matricula",
			// "UsuarioGrupoCovid.data_inclusao as data_respondeu",
			"ResultadoCovid.data_inclusao as data_respondeu",
			'GrupoCovid.descricao as grupo',
			// 'UsuarioGrupoCovid.data_fim_quarentena as fim_quarentena',
			'ResultadoCovid.passaporte as passaporte',
			'UsuariosDados.telefone as telefone',
			'Usuario.email as email',
			'UsuarioContatoEmergencia.nome',
			'UsuarioContatoEmergencia.email',
			'UsuarioContatoEmergencia.telefone',
			'UsuarioContatoEmergencia.grau_parentesco',
			
        );

		$joins = array(
			array(
                'table' => 'RHHealth.dbo.usuarios_dados',
                'alias' => 'UsuariosDados',
                'type' => 'INNER',
                'conditions' => array('UsuariosDados.codigo_usuario = UsuarioGrupoCovid.codigo_usuario')
			),
			array(
                'table' => 'RHHealth.dbo.usuario',
                'alias' => 'Usuario',
                'type' => 'INNER',
                'conditions' => array('UsuariosDados.codigo_usuario = Usuario.codigo')
			),
            array(
                'table' => 'RHHealth.dbo.funcionarios',
                'alias' => 'Funcionarios',
                'type' => 'INNER',
                'conditions' => array('Funcionarios.cpf = UsuariosDados.cpf')
			),
			array(
                'table' => 'RHHealth.dbo.cliente_funcionario',
                'alias' => 'ClienteFuncionario',
                'type' => 'INNER',
                'conditions' => array('ClienteFuncionario.codigo_funcionario = Funcionarios.codigo')
            ),
            array(
                'table' => 'RHHealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => array('Cliente.codigo = ClienteFuncionario.codigo_cliente_matricula')
            ),
            array(
                'table' => 'RHHealth.dbo.grupos_economicos_clientes',
                'alias' => 'GruposEconomicosClientes',
                'type' => 'INNER',
                'conditions' => array('GruposEconomicosClientes.codigo_cliente = Cliente.codigo')
            ),
            array(
                'table' => 'RHHealth.dbo.grupos_economicos',
                'alias' => 'GrupoEconomico',
                'type' => 'INNER',
                'conditions' => array('GruposEconomicosClientes.codigo_grupo_economico = GrupoEconomico.codigo ')
            ),
                       
            array(
                'table' => 'RHHealth.dbo.funcionario_setores_cargos',
                'alias' => 'FuncionarioSetoresCargos',
                'type' => 'INNER',
                'conditions' => array('FuncionarioSetoresCargos.codigo_cliente_funcionario = ClienteFuncionario.codigo
                	AND FuncionarioSetoresCargos.data_fim IS NULL')
            ),
			array(
                'table' => 'RHHealth.dbo.cliente',
                'alias' => 'ClienteAlocacao',
                'type' => 'INNER',
                'conditions' => array('ClienteAlocacao.codigo = FuncionarioSetoresCargos.codigo_cliente_alocacao')
            ),
            array(
                'table' => 'RHHealth.dbo.setores',
                'alias' => 'Setor',
                'type' => 'INNER',
                'conditions' => array('Setor.codigo = FuncionarioSetoresCargos.codigo_setor')
            ),
            
            array(
                'table' => 'RHHealth.dbo.cargos',
                'alias' => 'Cargo',
                'type' => 'INNER',
                'conditions' => array('Cargo.codigo = FuncionarioSetoresCargos.codigo_cargo')
            ),
            array(
                'table' => 'RHHealth.dbo.grupo_covid',
                'alias' => 'GrupoCovid',
                'type' => 'INNER',
                'conditions' => array('GrupoCovid.codigo = UsuarioGrupoCovid.codigo_grupo_covid')
            ),
            array(
                'table' => 'RHHealth.dbo.resultado_covid',
                'alias' => 'ResultadoCovid',
                'type' => 'LEFT',
                'conditions' => array("UsuarioGrupoCovid.codigo_usuario = ResultadoCovid.codigo_usuario 
                	AND ResultadoCovid.codigo = (SELECT TOP 1 codigo FROM resultado_covid WHERE codigo_usuario = UsuarioGrupoCovid.codigo_usuario AND data_inclusao >= '".date('Y-m-d')."' ORDER BY codigo DESC)")
            ),
            array(
                'table' => 'RHHealth.dbo.usuario_contato_emergencia',
                'alias' => 'UsuarioContatoEmergencia',
                'type' => 'LEFT',
                'conditions' => array('UsuarioGrupoCovid.codigo_usuario = UsuarioContatoEmergencia.codigo_usuario')
            ),           
            
        );
		

		// CDCT-678
		$codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];
		
		if(isset($codigo_empresa)){
			$joins[4]['conditions'][0] .= ' AND Cliente.codigo_empresa = '.$codigo_empresa;
		}
		
        $group = array(
			'Cliente.codigo',
            'UsuarioGrupoCovid.codigo ',
            'UsuarioGrupoCovid.codigo_usuario ',
            'FuncionarioSetoresCargos.codigo ',
            'GrupoEconomico.descricao ',
            'Cliente.razao_social ',
			'Setor.descricao ',
			'Cargo.descricao ',
			'Funcionarios.nome ',
			'Funcionarios.cpf ',
			'ClienteFuncionario.codigo ',
			'ClienteFuncionario.matricula ',
			// 'UsuarioGrupoCovid.data_inclusao ',
			'ResultadoCovid.data_inclusao',
			'GrupoCovid.descricao ',
			'UsuarioGrupoCovid.data_fim_quarentena ',
			'ResultadoCovid.passaporte',
			'UsuariosDados.telefone',
			'Usuario.email',
			'UsuarioContatoEmergencia.nome',
			'UsuarioContatoEmergencia.email',
			'UsuarioContatoEmergencia.telefone',
			'UsuarioContatoEmergencia.grau_parentesco',
        );
        // debug($conditions);exit;

        $order = 'Cliente.codigo';

        $dados = array(
                    'conditions' => $conditions,
                    'joins' => $joins,
                    'fields' => $fields,
                    // 'limit' => 50,
                    'group' => $group,
                    'order' => $order
                );

        if(!$limit) {
        	unset($dados['limit']);
        }        

        // debug( $this->find('sql',$dados) );

		return $dados;
	}//fim query

	/**
	 * [voltaUsuarioGrupoCovid metodo para voltar o usuario passado para o grupo anterior que estava]
	 * @param  [type] $codigo_usuario_grupo_covid [description]
	 * @return [type]                             [description]
	 */
	public function voltaUsuarioGrupoCovid($codigo_usuario_grupo_covid)
	{

		$this->UsuarioGrupoCovidLog =& ClassRegistry::init('UsuarioGrupoCovidLog');

		//pega o codigo do grupo que estava no log
		$codigo_grupo_covid = $this->UsuarioGrupoCovidLog->find('first',array('fields' => array('codigo_grupo_covid'),'conditions' => array('codigo_usuario_grupo_covid' => $codigo_usuario_grupo_covid, 'codigo_grupo_covid NOT IN (4,5,6)'),'order' => 'codigo DESC'));

		// debug($codigo_grupo_covid);exit;
		$dados = array();
		if(!empty($codigo_grupo_covid)) {

			$dados = array(
				'UsuarioGrupoCovid' => array(
					'codigo' => $codigo_usuario_grupo_covid,
					'codigo_grupo_covid' => $codigo_grupo_covid['UsuarioGrupoCovidLog']['codigo_grupo_covid']
				)
			);

		}
		else {
			//pega o dados do usuario grupo para saber se esta no grupo vermelho e jogar ele para o grupo azul
			$codigo_grupo_covid = $this->find('first',array('fields' => array('codigo_grupo_covid'),'conditions' => array('codigo' => $codigo_usuario_grupo_covid, 'codigo_grupo_covid' => '4')));
			//verifica se encontrou registro
			if(!empty($codigo_grupo_covid)) {
				//joga o funcionario para o grupo azul
				$dados = array(
					'UsuarioGrupoCovid' => array(
						'codigo' => $codigo_usuario_grupo_covid,
						'codigo_grupo_covid' => 3
					)
				);
			}//fim grupo vermelho
		}//fim else

		if(!empty($dados)) {
			$this->atualizar($dados);
			return $dados['UsuarioGrupoCovid']['codigo_grupo_covid'];
		}

		return false;
	}

	/**
	 * [get_usuario_fim_afastamento metodo para buscar os usuarios que estao com a data de fim afastamento no dia de hoje]
	 * @return [type] [description]
	 */
	public function get_usuario_fim_afastamento($codigo_usuario_grupo_covid = null)
	{

		$this->UsuarioGca =& ClassRegistry::init('UsuarioGca');

		//busca os usuarios que estao com afastamento ativo pela tela de gestao covid
		$usuario_gca = $this->UsuarioGca->find('all', array('conditions' => 
			array(
				'ativo' => 1,
				'afastamento_sintomas' => 1, 
				'controle_data_afastamento' => 1,
				'DATEADD(DAY,1,data_fim_afastamento)' => date('Y-m-d')
				// 'data_fim_afastamento' => '2021-01-11'
			)
		));

		// debug($usuario_gca);exit;

		//verifica se tem registros
		if(!empty($usuario_gca)) {

			//varre os usuarios que precisa atualizar
			foreach($usuario_gca AS $dado) {
				
				//volta o usuario ao grupo que estava
				$codigo_grupo_covid = $this->voltaUsuarioGrupoCovid($dado['UsuarioGca']['codigo_usuario_grupo_covid']);

				//atualiza para não buscar mais este registro
				$dado['UsuarioGca']['controle_data_afastamento'] = 0;
				$this->UsuarioGca->atualizar($dado);

			}//fim foreach

		}//fim $usuario_gca
		
	}//fim get_usuario_fim_afastamento

}