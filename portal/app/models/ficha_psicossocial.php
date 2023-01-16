<?php
App::import('model');
class FichaPsicossocial extends AppModel
{

	public $name		   	= 'FichaPsicossocial';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'ficha_psicossocial';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_ficha_psicossocial'));

	public $validate = array(
		'codigo_medico' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Médico!'
		),
		'codigo_pedido_exame' => array(
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Já existe uma ficha psicossocial para este pedido!',
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o codigo do pedido de exame!',
			)
		),
	);

	public function converteFiltroEmCondition($data)
	{
		$conditions = array();

		if (!empty($data['codigo_cliente']))
			$conditions['PedidoExame.codigo_cliente'] = $data['codigo_cliente'];

		if (!empty($data['codigo']))
			$conditions['FichaPsicossocial.codigo'] = $data['codigo'];

		if (!empty($data['codigo_pedido_exame']))
			$conditions['FichaPsicossocial.codigo_pedido_exame'] = $data['codigo_pedido_exame'];

		if (!empty($data['nome_funcionario']))
			$conditions['Funcionario.nome LIKE'] = '%' . $data['nome_funcionario'] . '%';

		if (!empty($data['nome_medico']))
			$conditions['Medico.nome LIKE'] = '%' . $data['nome_medico'] . '%';

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

	function converteFiltrosEmConditionsTerceiros($filtros)
	{
		$conditions = array();

		if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {

			$GrupoEconomico = &ClassRegistry::init('GrupoEconomico');

			$GrupoEconomicoCliente = &ClassRegistry::init('GrupoEconomicoCliente');

			$codigo_cliente = $filtros['codigo_cliente'];

			//verifica se é multicliente para passar o array, senão ele irá pesquisar a matriz do cliente pesquisado
			if (isset($_SESSION['Auth']['Usuario']['multicliente'])) {
				$codigo_matriz = $codigo_cliente;
			} else {
				$codigo_matriz = $GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
			}

			$codigos_unidades = $GrupoEconomicoCliente->lista($codigo_matriz);

			$conditions[] = array('FuncionarioSetorCargo.codigo_cliente_alocacao IN (
			select codigo_cliente from grupos_economicos_clientes where codigo_grupo_economico IN (select codigo from grupos_economicos where codigo_cliente IN(' . implode(",", array_keys($codigos_unidades)) . ')))');
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
		//pega a data de inicio setada nos filtros
		if (isset($filtros['data_inicio']) && !empty($filtros['data_inicio'])) {
			$tipo = 'CAST(FichaPsicossocial.data_inclusao AS DATE)';
			$conditions[$tipo . ' >='] = AppModel::dateToDbDate($filtros['data_inicio']);
		}

		//pega a data de fim setada nos filtros
		if (isset($filtros['data_fim']) && !empty($filtros['data_fim'])) {
			$tipo = 'CAST(FichaPsicossocial.data_inclusao AS DATE)';
			$conditions[$tipo . ' <='] = AppModel::dateToDbDate($filtros['data_fim']);
		}

		return $conditions;
	}

	public function obtemDadosComplementaresFPS($codigoPedidoExame)
	{

		$this->PedidoExame = &ClassRegistry::init('PedidoExame');

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
			WHERE ItemPedidoExame.codigo_pedidos_exames = ' . $codigoPedidoExame . '
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
			'setor' => "(SELECT descricao FROM RHHealth.dbo.setores where codigo = (SELECT TOP 1 codigo_setor FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo AND (data_fim = '' OR data_fim IS NULL )  ORDER BY 1 DESC))",
			'cargo' => "(SELECT descricao FROM RHHealth.dbo.cargos where codigo = (SELECT TOP 1 codigo_cargo FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo  AND (data_fim = '' OR data_fim IS NULL ) ORDER BY 1 DESC))"
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
			'setor',
			'cargo'
		);

		$dados = $this->PedidoExame->find('first', $options);
		$dados['Medico'] = $values;
		unset($values);

		return $dados;
	}

	public function getByCodigoPedidoExame($codigo_pedido_exame)
	{
		return $this->find('first', array('conditions' => array('codigo_pedido_exame' => $codigo_pedido_exame), 'order' => array('codigo DESC')));
	}
}//FINAL CLASS Ficha Psicossocial