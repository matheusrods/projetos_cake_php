<?php
class ClienteSetorCargo extends AppModel {
	public $name 			= 'ClienteSetorCargo';
	public $tableSchema 	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable 		= 'clientes_setores_cargos';
	public $primaryKey 		= 'codigo';
	public $actsAs          = array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_cliente_setor_cargo'));

	public function incluir($data)
	{
		$result =  parent::incluir($data);
		
		if($result){
			/*
				Se um usuário de cliente gera uma nova hierarquia
				Um alerta será gerado caso exista PPRA ou PCMSO pendente
				Ao finalizar o PPRA e PCMSO desta hierarquia, o usuário será alertado
			*/
			if(!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])) {
				$AlertaHierarquiaPendente =& ClassRegistry::init('AlertaHierarquiaPendente');
				$hierarquia_pendente = array('AlertaHierarquiaPendente' => array(
					'codigo_cliente_alocacao' => $data['ClienteSetorCargo']['codigo_cliente_alocacao'],
					'codigo_setor' => $data['ClienteSetorCargo']['codigo_setor'],
					'codigo_cargo' => $data['ClienteSetorCargo']['codigo_cargo'],
					'origem' => 'NOVA_HIERARQUIA'
				));
				
				$AlertaHierarquiaPendente->incluir($hierarquia_pendente);
									
			}
		}
		return $result;
	}

	public function atualizar($data)
	{
		return parent::atualizar($data);
	}

	public function exluir($data)
	{
		return parent::excluir($data);
	}

	/**
	 * Metodo para trabalhar os filtros da tela
	 */
	function converteFiltroEmCondition($data) {
		//variavel auxiliar
		$conditions = array();

		//verifica se existe o codigo do setor
		if (!empty($data['codigo_setor'])) {
			//seta o filtro pelo setor
			$conditions['ClienteSetorCargo.codigo_setor'] = $data['codigo_setor'];
		}

		//verifica se tem o codigo de cargo
		if (!empty($data['codigo_cargo'])) {
			//seta o filtr de cargo
			$conditions['ClienteSetorCargo.codigo_cargo'] = $data['codigo_cargo'];
		}

		//verifica se tem filtro da unidade
		if (!empty($data['codigo_unidade'])) {
			//seta o filtro da unidade
			$conditions['ClienteSetorCargo.codigo_cliente_alocacao'] = $data['codigo_unidade'];
		}

		$conditions['Setor.ativo'] = 1;
		$conditions['Cargo.ativo'] = 1;

		if (isset($data['ativo'])){
			if($data['ativo'] == '0') {				
				$conditions ['ClienteSetorCargo.ativo'] = $data['ativo'];				
			} else if ($data['ativo'] == '1') {
				$conditions[] = '(ClienteSetorCargo.ativo = '.$data['ativo'].' OR ClienteSetorCargo.ativo IS NULL)';
			}
		}

		//retorna os filtros
		return $conditions;
	}

	public function getHierarquias($conditions = null, $pagination = false, $codigo_cliente, $getDados = false){

		$joins = array(
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => array(
					'Cliente.codigo = ClienteSetorCargo.codigo_cliente_alocacao'
					)
				),
			array(
				'table' => 'setores',
				'alias' => 'Setor',
				'type' => 'INNER',
				'conditions' => array(
					'Setor.codigo = ClienteSetorCargo.codigo_setor'
					)
				),
			array(
				'table' => 'cargos',
				'alias' => 'Cargo',
				'type' => 'INNER',
				'conditions' => array(
					'Cargo.codigo = ClienteSetorCargo.codigo_cargo'
					)
				),
			array(
				'table' 		=> 'grupos_economicos_clientes',
				'alias' 		=> 'GrupoEconomicoCliente',
				'type'			=> 'INNER',
				'conditions'	=> array(
					'ClienteSetorCargo.codigo_cliente_alocacao = GrupoEconomicoCliente.codigo_cliente'
					)
				),
			array(
				'table' 		=> 'grupos_economicos',
				'alias' 		=> 'GrupoEconomico',
				'type'			=> 'INNER',
				'conditions'	=> array(
					'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
					)
				)
		);

		$fields = array(
			'ClienteSetorCargo.codigo', 
			'ClienteSetorCargo.bloqueado', 
			'ClienteSetorCargo.data_inclusao',
			'ClienteSetorCargo.codigo_usuario_inclusao',
			'ClienteSetorCargo.ativo',
			'Cliente.codigo', 
			'(CONCAT(Cliente.codigo, \' - \', Cliente.nome_fantasia)) AS nome_fantasia', 
			'Setor.codigo', 
			'Setor.descricao', 
			'Cargo.codigo', 
			'Cargo.descricao', 
			'(
				select 
					count(cf.codigo)
				from cliente_funcionario cf 
					INNER join funcionario_setores_cargos fsc on fsc.codigo_cliente_funcionario = cf.codigo AND fsc.[codigo] = (
						SELECT TOP 1
						codigo
						FROM [RHHealth].[dbo].funcionario_setores_cargos
						WHERE codigo_cliente_funcionario = cf.[codigo]
						ORDER BY codigo DESC
					)
					inner join setores s on s.codigo = fsc.codigo_setor
					inner join cargos c on c.codigo = fsc.codigo_cargo
					inner join funcionarios f on f.codigo = cf.codigo_funcionario
				where fsc.codigo_cliente_alocacao = ClienteSetorCargo.codigo_cliente_alocacao 
					and fsc.codigo_cargo = ClienteSetorCargo.codigo_cargo 
					and fsc.codigo_setor = ClienteSetorCargo.codigo_setor
					and cf.ativo = 1
				) as qtd_funcionarios'
		);
		
		$order = 'ClienteSetorCargo.codigo DESC';

		if($conditions){
			$conditions[] = array_merge($conditions, array('GrupoEconomico.codigo_cliente' => $codigo_cliente));
		} else {
			$conditions[] = array('GrupoEconomico.codigo_cliente' => $codigo_cliente);
		}


        if($pagination){
            $paginate = array(
				'conditions' => $conditions,
				'joins' => $joins,
				'fields' => $fields,
				'limit' => 50,
				'order' => $order
            );

            return $paginate;
        } else if($getDados){
        	return $this->find('all', array('joins' => $joins, 'fields' => $fields, 'conditions' => $conditions, 'order' => $order));
        } else {
        	//para uma possivel exportacao           
            return $this->find('sql', array('joins' => $joins, 'fields' => $fields, 'conditions' => $conditions, 'order' => $order));
        }
	}
}

?>