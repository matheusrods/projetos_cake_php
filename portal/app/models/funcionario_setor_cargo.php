<?php
class FuncionarioSetorCargo extends AppModel {

	public $name = 'FuncionarioSetorCargo';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'funcionario_setores_cargos';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_funcionario_setores_cargos'));

	public $validate = array(
		'data_fim' => array(
			'valida_data_fim' => array(
				'rule' => 'valida_data_fim',
				'message' => 'Primeiro crie um novo "setor e cargo" antes de finalizar este.',
				'on' => 'update',
				)
			)
		);

	public $belongsTo = array(
		'ClienteFuncionario' => array(
			'className' => 'ClienteFuncionario',
			'foreignKey' => 'codigo_cliente_funcionario'
		),
		'Setor' => array(
			'className' => 'Setor',
			'foreignKey' => 'codigo_setor',
			'fields' => array('Setor.codigo', 'Setor.descricao')
			),
		'Cargo' => array(
			'className' => 'Cargo',
			'foreignKey' => 'codigo_cargo',
			'fields' => array('Cargo.codigo', 'Cargo.descricao')
			),
		'Cliente' => array(
			'className' => 'Cliente',
			'foreignKey' => 'codigo_cliente_alocacao',
			'fields' => array('Cliente.codigo', 'Cliente.razao_social', 'Cliente.nome_fantasia')
			)
		);

	protected function valida_data_fim() {
		
			$ClienteFuncionario =& ClassRegistry::init('ClienteFuncionario');
			$cliente_funcionario = $ClienteFuncionario->read('ativo', $this->data[$this->name]['codigo_cliente_funcionario']);

			if(!empty($this->data[$this->name]['data_fim']) && $this->find('count', array(
			'conditions' => array(
				'FuncionarioSetorCargo.codigo_cliente_funcionario' => $this->data[$this->name]['codigo_cliente_funcionario'],
				'FuncionarioSetorCargo.data_fim' => null,
				'FuncionarioSetorCargo.codigo !=' => $this->data[$this->name]['codigo']
				)
			)
		) == 0 && $cliente_funcionario['ClienteFuncionario']['ativo'] > 0 ) {
			return false;
		} else {
			return true;
		}
	}

	public function finaliza_setores_cargos_em_aberto($codigo_cliente_funcionario)
	{
		return $this->updateAll(array('data_fim' => '"'.date('Y-m-d').'"'), array('codigo_cliente_funcionario' => $codigo_cliente_funcionario));
	}

	/**
	 * beforeSave callback
	 *
	 * @param $options array
	 * @return boolean
	 */
	/*public function beforeSave($options) {

		// CASO NÃO EXISTA DADOS, NAO SALVE
		if(!empty($this->data[$this->name])) {
			foreach ($this->data[$this->name] as $key => $data) {
				if(in_array($key, array('codigo_cliente', 'codigo_setor', 'codigo_cargo', 'data_inicio'))) {
					if(!empty($data)) return true;
				}
			}
		}
		unset($this->data[$this->name]);
		return true;
	}*/

	/**
	 * afterFind callback
	 *
	 * @param $results array
	 * @param $primary boolean
	 * @return mixed
	 */
	/*public function afterFind($results, $primary = false) {
		foreach ($results as $key => $result) {
			if(!empty($result['FuncionarioSetorCargo']['data_inicio'])) {
				$results[$key]['FuncionarioSetorCargo']['data_inicio'] = date('d/m/Y', strtotime($result['FuncionarioSetorCargo']['data_inicio']));
			}
			if(!empty($result['FuncionarioSetorCargo']['data_fim'])) {
				$results[$key]['FuncionarioSetorCargo']['data_fim'] = date('d/m/Y', strtotime($result['FuncionarioSetorCargo']['data_fim']));
			}
		}
		return $results;
	}*/

	public function incluir($data)
	{
		$result = parent::incluir($data);

		//Se o registro foi inserido
		if($result){
	       
            /* 	verifica se não existe uma hierarquia criada com este setor e cargo para este cliente
            	se a hierarquia não está bloqueada cria uma nova */
 			$cod_cliente_alocacao = $result['FuncionarioSetorCargo']['codigo_cliente_alocacao'];
	        $cod_setor = $result['FuncionarioSetorCargo']['codigo_setor'];
	        $cod_cargo = $result['FuncionarioSetorCargo']['codigo_cargo'];
           
	        $this->gerar_hierarquia($cod_cliente_alocacao, $cod_setor, $cod_cargo);
		}

		return $result;
	}

	/**
	 * [atualizar description]
	 * 
	 * Atualiza os dados da funcionarios setor e cargo, e inclui a hierarquia
	 * 
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function atualizar($data)
	{
		$result = parent::atualizar($data);
		
		//Se o registro foi inserido
		if($result){
	        
            /* 	verifica se não existe uma hierarquia criada com este setor e cargo para este cliente
            	se a hierarquia não está bloqueada cria uma nova */
	        $cod_cliente_alocacao = $data['FuncionarioSetorCargo']['codigo_cliente_alocacao'];
	        $cod_setor = $data['FuncionarioSetorCargo']['codigo_setor'];
	        $cod_cargo = $data['FuncionarioSetorCargo']['codigo_cargo'];

	        $this->gerar_hierarquia($cod_cliente_alocacao, $cod_setor, $cod_cargo);
	        
		} //result
		
		return $result;

	} // fim atualizar


	function converteFiltrosEmConditions($filtros) {
		
		$conditions = array();

		if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {

			$GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
			$GrupoEconomicoCliente =& ClassRegistry::init('GrupoEconomicoCliente');

			$codigo_cliente = $filtros['codigo_cliente'];
			//verifica se é multicliente para passar o array, senão ele irá pesquisar a matriz do cliente pesquisado
			if(isset($_SESSION['Auth']['Usuario']['multicliente'])) {
				$codigo_matriz = $codigo_cliente;
			}
			else {
				$codigo_matriz = $GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
			}

			if(empty($codigo_matriz)){
				return false;
			}

			$codigos_unidades = $GrupoEconomicoCliente->lista($codigo_matriz);			
			
			$conditions['ClienteFuncionario.codigo_cliente_matricula'] = array_keys($codigos_unidades);			

			//carrega as unidades caso ele tenha
			$this->UsuarioUnidade =& ClassRegistry::Init('UsuarioUnidade');			
			$codigo_usuario = $_SESSION['Auth']['Usuario']['codigo'];
			// $codigo_usuario = $this->Bauth
			$usuario_unidade = $this->UsuarioUnidade->find('list',array('fields' => array('UsuarioUnidade.codigo_cliente'),'conditions' => array('UsuarioUnidade.codigo_usuario' => $codigo_usuario)));

			//verifica se existe registros
			if(!empty($usuario_unidade)) {

				//refaz o filtro do codigo_cliente_matricula				
				unset($conditions['ClienteFuncionario']);
				$conditions['ClienteFuncionario.codigo_cliente_matricula'] = $filtros['codigo_cliente'];
				
				//trata os dados retornados
				$filtros_codigos_unidades = implode(',',$usuario_unidade);
				//seta as empresas que ele pode ver
				$conditions[] = array('FuncionarioSetorCargo.codigo_cliente_alocacao IN ('.$filtros_codigos_unidades.')'	);			

			}//fim empty
			

		}
		if (isset($filtros['codigo_cliente_alocacao']) && !empty($filtros['codigo_cliente_alocacao']) && trim($filtros['codigo_cliente_alocacao']) != '') {
			$conditions['FuncionarioSetorCargo.codigo_cliente_alocacao'] = $filtros['codigo_cliente_alocacao'];
		}
		if (isset($filtros['codigo_setor']) && !empty($filtros['codigo_setor']) && trim($filtros['codigo_setor']) != '') {
			$conditions['FuncionarioSetorCargo.codigo_setor'] = $filtros['codigo_setor'];
		}
		if (isset($filtros['codigo_cargo']) && !empty($filtros['codigo_cargo']) && trim($filtros['codigo_cargo']) != '') {
			$conditions['FuncionarioSetorCargo.codigo_cargo'] = $filtros['codigo_cargo'];
		}
		if (isset($filtros['codigo_funcionario']) && !empty($filtros['codigo_funcionario'])  && trim($filtros['codigo_funcionario']) != '') {
			$conditions['ClienteFuncionario.codigo_funcionario'] = $filtros['codigo_funcionario'];
		}
		if (isset($filtros['codigo_pedido_exame']) && !empty($filtros['codigo_pedido_exame'])) {
			$conditions['PedidoExame.codigo'] = $filtros['codigo_pedido_exame'];
		}		
		if (isset($filtros['ativo']) && $filtros['ativo'] != '') {
			if($filtros['ativo'] != 'todos'){
				$conditions['ClienteFuncionario.ativo'] = $filtros['ativo'];
			}
		}
		if (isset($filtros['cpf']) && !empty($filtros['cpf'])  && trim($filtros['cpf']) != '') {

			$conditions['Funcionario.cpf'] = $filtros['cpf'];
		}
		if (isset($filtros['matricula']) && !empty($filtros['matricula'])  && trim($filtros['matricula']) != '') {
			$conditions['ClienteFuncionario.matricula like'] = '%' . $filtros['matricula'] . '%';
		}

		return $conditions;
	}

	//Gera hierarquia somente se a unidade não for bloqueada e a hierarquia não existir
	public function gerar_hierarquia ($codigo_cliente_alocacao, $codigo_setor, $codigo_cargo){

		$this->GrupoEconomicoCliente =& ClassRegistry::init('GrupoEconomicoCliente');
        $dados_hierarquia = $this->GrupoEconomicoCliente->find('first', array('conditions' => array(
	        	'OR' => array(
			        		array('bloqueado' => 0),
			        		array('bloqueado' => NULL)
	        			),
	        	'codigo_cliente' => $codigo_cliente_alocacao,
	        	"NOT EXISTS(SELECT codigo FROM clientes_setores_cargos WHERE".
	        	" codigo_cliente_alocacao = ".$codigo_cliente_alocacao. 
	        	" AND  codigo_setor = ".$codigo_setor.
	        	" AND codigo_cargo = ".$codigo_cargo.")"
	        	), 
	        	'fields' => array('codigo_cliente', 'bloqueado'),
	        	'recursive' => -1));
           
        if(!empty($dados_hierarquia)){

        	$this->ClienteSetorCargo =& ClassRegistry::init('ClienteSetorCargo');
		        $dados = array('ClienteSetorCargo' => array(
		        					'codigo_cliente' =>$codigo_cliente_alocacao,
		        					'codigo_cliente_alocacao' =>$codigo_cliente_alocacao,
	        						'codigo_cargo' => $codigo_cargo,
	        						'codigo_setor' => $codigo_setor
		        				));

	        $this->ClienteSetorCargo->incluir($dados);
        }

		return true;

	}

	//Traz as unidades de trabalho (cliente_alocacao) de um usuário/funcionário pelo CPF
	function unidades_alocacao($cpf) {

		$options['fields'] = array(
			'razao_empresa',
			'Cliente.nome_fantasia',
			'Cliente.codigo_documento',
			'Funcionario.nome',
			'Setor.descricao',
			'Cargo.descricao',		
			);

		$options['joins'] = array(
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.cpf ='	. $cpf
			),  
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo and ClienteFuncionario.ativo = 1'
			),			
			array(
				'table' => 'funcionario_setores_cargos' ,
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => array (
					"FuncionarioSetorCargo.codigo = (Select TOP 1 codigo from funcionario_setores_cargos Where codigo_cliente_funcionario = ClienteFuncionario.codigo AND ((data_fim = '' OR data_fim IS NULL) OR (data_fim is not null AND ClienteFuncionario.ativo = 0)) ORDER by codigo DESC)"
					)
			),  
			array(
				'table' => 'setores',
				'alias' => 'Setor',
				'type' => 'INNER',
				'conditions' => 'Setor.codigo = FuncionarioSetorCargo.codigo_setor' 
			),  
			array(
				'table' => 'cargos',
				'alias' => 'Cargo',
				'type' => 'INNER',
				'conditions' => 'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo'
			),  
			array(
				'table' => 'grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => 'GrupoEconomicoCliente.codigo_cliente = FuncionarioSetorCargo.codigo_cliente' 
			),  
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo'
			), 
				
		);

		$options['conditions'] = array("fsc.codigo_cliente_funcionario = cf.codigo", "fsc.data_fim is null");

		// debug($this->find('sql', $options));
		$dados = $this->find('all', $options);
	}

	public function DadosClienteFuncionarioPedido($codigo_funcionario_setor_cargo){

		if(!empty($codigo_funcionario_setor_cargo)){

			$options['fields'] = array(
				'Funcionario.codigo',
				'Funcionario.nome',
				'Funcionario.cpf',
				'Funcionario.data_nascimento',
				'Cargo.codigo',
				'Cargo.descricao',
				'Setor.codigo',
				'Setor.descricao',
				'ClienteFuncionario.codigo',
				'ClienteFuncionario.codigo_cliente',
				'ClienteFuncionario.codigo_cliente_matricula',
				'FuncionarioSetorCargo.codigo',
			);


			$options['joins'] = array(
				array(
					'table' => 'cliente_funcionario',
					'alias' => 'ClienteFuncionario',
					'type' => 'INNER',
					'conditions' => 'ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario',
				),
				array(
					'table' => 'funcionarios',
					'alias' => 'Funcionario',
					'type' => 'INNER',
					'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
				),
				array(
					'table' => 'cargos',
					'alias' => 'Cargo',
					'type' => 'LEFT',
					'conditions' => 'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo',
				),
				array(
					'table' => 'setores',
					'alias' => 'Setor',
					'type' => 'LEFT',
					'conditions' => 'Setor.codigo = FuncionarioSetorCargo.codigo_setor',
				),
			);

			$options['conditions'] = array(
				'FuncionarioSetorCargo.codigo' => $codigo_funcionario_setor_cargo
			);

			$options['recursive'] = '-1';

			return $this->find('first', $options);
		} //fim

	}
}