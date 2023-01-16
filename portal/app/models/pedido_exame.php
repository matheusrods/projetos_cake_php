<?php
class PedidoExame extends AppModel
{

	public $name		   	= 'PedidoExame';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'pedidos_exames';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_pedidos_exames'));
	public $recursive 		= -1;

	public $hasMany = array(
		'FichaClinica' => array(
			'className'    => 'FichaClinica',
			'foreignKey'    => 'codigo_pedido_exame'
		),
		'FichaAssistencial' => array(
			'className'    => 'FichaAssistencial',
			'foreignKey'    => 'codigo_pedido_exame'
		),
		'ItemPedidoExame' => array(
			'className'    => 'ItemPedidoExame',
			'foreignKey'    => 'codigo_pedidos_exames'
		)
	);

	public $belongsTo = array(
		'ClienteFuncionario' => array(
			'className'    => 'ClienteFuncionario',
			'foreignKey'    => 'codigo_cliente_funcionario'
		),
		'MultiEmpresa' => array(
			'className'    => 'MultiEmpresa',
			'foreignKey'    => 'codigo_empresa'
		),
	);

	/**
	 * Lista od pedidos de exames do funcionário
	 * @param int $funcionarioCliente
	 */
	public function listaPedidos($funcionarioCliente)
	{

		$saida = false;
		// confirma recebimento do código do funcionário para o cliente
		if (isset($funcionarioCliente) && is_numeric($funcionarioCliente) && (int) $funcionarioCliente > 0) {

			// levanta os pedidos para o funcionário
			$rst = $this->find('all', array(
				'conditions' => array('PedidoExame.codigo_cliente_funcionario' => $funcionarioCliente, 'PedidoExame.ativo' => '1'),
				'fields'     => array(
					'PedidoExame.codigo',
					'PedidoExame.codigo_cliente_funcionario',
					'PedidoExame.codigo_empresa',
					'PedidoExame.codigo_usuario_inclusao'
				),
			));

			// resultado válido
			$saida = (isset($rst)) ? $rst : false;
		}

		return $saida;
	}

	//Retorna os exames do PCMSO aplicados para unidade + setor + cargo de alocação do funcionário
	public function retornaExamesNecessarios($codigo_funcionario_setor_cargo, $tipo_exame)
	{

		if (is_numeric($codigo_funcionario_setor_cargo)) {

			$this->FuncionarioSetorCargo = &ClassRegistry::init('FuncionarioSetorCargo');

			//implementado o CDCT-187 quando tiver exames sem nenhuma configuracao de periodicidade em branco ou nula
			$options['fields'] = array(
				'Cliente.codigo',
				'Cliente.razao_social',
				'Cliente.nome_fantasia',
				'Exame.descricao',
				'Exame.codigo_servico',
				'Exame.codigo',
				'Funcionario.data_nascimento',
				'DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) AS idade',
				'AplicacaoExame.codigo_tipo_exame',
				'AplicacaoExame.exame_periodico',
				'AplicacaoExame.periodo_apos_demissao',
				'AplicacaoExame.periodo_idade',
				'AplicacaoExame.periodo_idade_2',
				'AplicacaoExame.periodo_idade_3',
				'AplicacaoExame.periodo_idade_4',
				"(CASE
					WHEN (
						(AplicacaoExame.periodo_apos_demissao IS NULL OR AplicacaoExame.periodo_apos_demissao = '') 
						AND (AplicacaoExame.periodo_meses IS NULL OR AplicacaoExame.periodo_meses = '')
						AND (AplicacaoExame.periodo_idade IS NULL OR AplicacaoExame.periodo_idade = '0' OR AplicacaoExame.periodo_idade = '')
						AND (AplicacaoExame.periodo_idade_2 IS NULL OR AplicacaoExame.periodo_idade_2 = '0' OR AplicacaoExame.periodo_idade_2 = '')
						AND (AplicacaoExame.periodo_idade_3 IS NULL OR AplicacaoExame.periodo_idade_3 = '0' OR AplicacaoExame.periodo_idade_3 = '')
						AND (AplicacaoExame.periodo_idade_4 IS NULL OR AplicacaoExame.periodo_idade_4 = '0' OR AplicacaoExame.periodo_idade_4 = '')
					) THEN 'true'
					WHEN ((AplicacaoExame.periodo_apos_demissao IS NOT NULL AND AplicacaoExame.periodo_apos_demissao <> '')) THEN 'true'
					WHEN ((AplicacaoExame.periodo_meses IS NOT NULL AND AplicacaoExame.periodo_meses <> '')) THEN 'true'
					ELSE
					   (CASE
							WHEN ((AplicacaoExame.periodo_idade IS NOT NULL AND AplicacaoExame.periodo_idade <> '0' AND AplicacaoExame.periodo_idade <> '') AND
									((DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) >= AplicacaoExame.periodo_idade ) )) THEN 'true'
							WHEN ((AplicacaoExame.periodo_idade_2 IS NOT NULL AND
								AplicacaoExame.periodo_idade_2 <> '0' AND AplicacaoExame.periodo_idade_2 <> '') AND
								((DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) >= AplicacaoExame.periodo_idade_2 ) )) THEN 'true'
							WHEN ((AplicacaoExame.periodo_idade_3 IS NOT NULL AND
								AplicacaoExame.periodo_idade_3 <> '0' AND AplicacaoExame.periodo_idade_3 <> '') AND
								((DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) >= AplicacaoExame.periodo_idade_3 ) )) THEN 'true'
							WHEN (
								(AplicacaoExame.periodo_idade_4 IS NOT NULL AND AplicacaoExame.periodo_idade_4 <> '0' AND AplicacaoExame.periodo_idade_4 <> '') 
									AND (DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) >= AplicacaoExame.periodo_idade_4)) THEN 'true'
							ELSE 'false'
						END) 
					END) AS exame_aplicar"
			);

			$options['joins'] = array(
				array(
					'table' => 'cliente_funcionario',
					'alias' => 'ClienteFuncionario',
					'type' => 'INNER',
					'conditions' => 'ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario',
				),
				array(
					'table' => 'cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao',
				),
				array(
					'table' => 'funcionarios',
					'alias' => 'Funcionario',
					'type' => 'INNER',
					'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
				),
				array(
					'table' => 'aplicacao_exames',
					'alias' => 'AplicacaoExame',
					'type' => 'INNER',
					'conditions' => 'AplicacaoExame.codigo_cliente_alocacao = FuncionarioSetorCargo.codigo_cliente_alocacao 
						AND	AplicacaoExame.codigo_setor = FuncionarioSetorCargo.codigo_setor 
						AND AplicacaoExame.codigo_cargo = FuncionarioSetorCargo.codigo_cargo',
				),
				array(
					'table' => 'exames',
					'alias' => 'Exame',
					'type' => 'INNER',
					'conditions' => 'Exame.codigo = AplicacaoExame.codigo_exame',
				),
				array(
					'table' => 'servico',
					'alias' => 'Servico',
					'type' => 'INNER',
					'conditions' => 'Servico.codigo = Exame.codigo_servico',
				)
			);

			$options['conditions'] = array(
				"FuncionarioSetorCargo.codigo = {$codigo_funcionario_setor_cargo}",
				"AplicacaoExame.{$tipo_exame} = 1",
				"Exame.ativo = 1",
				"AplicacaoExame.codigo IN (select * from RHHealth.dbo.ufn_aplicacao_exames(FuncionarioSetorCargo.codigo_cliente_alocacao,FuncionarioSetorCargo.codigo_setor,FuncionarioSetorCargo.codigo_cargo,ClienteFuncionario.codigo_funcionario))"
			);

			// $options['group'] = array(
			// 	'Cliente.codigo',
			// 	'Cliente.razao_social',
			// 	'Cliente.nome_fantasia',
			// 	'Exame.descricao',
			// 	'Exame.codigo_servico',
			// 	'Exame.codigo',
			// 	'Funcionario.data_nascimento'
			// );

			$options['recursive'] = '-1';

			// debug($this->FuncionarioSetorCargo->find('sql', $options));exit;

			return $this->FuncionarioSetorCargo->find('all', $options);
		} else {
			return false;
		}
	}

	public function retornaExamePcd()
	{

		$model_PedidoExame = &ClassRegistry::init('Exame');

		$Configuracao = ClassRegistry::init('Configuracao');

		$codigo_exame_pcd = $Configuracao->getChave('AVALIACAO_PCD');

		$options['fields'] = array(
			'Exame.codigo',
			'Exame.descricao',
			'Exame.codigo_servico'
		);

		$options['conditions'] = array(
			'Exame.codigo = ' . $codigo_exame_pcd
		);

		$options['recursive'] = '-1';

		$pedido_pcd = $model_PedidoExame->find('first', $options);

		return $pedido_pcd;
	}

	//Verifica se existe assinatura do exame na matriz
	public function verificaExameTemAssinatura($codigo_servico, $codigo_cliente_alocacao, $codigo_matriz)
	{

		$model_ClienteProdutoServico2 = &ClassRegistry::init('ClienteProdutoServico2');

		$options['fields'] = array(
			'ClienteProdutoServico2.codigo',
			'ClienteProdutoServico2.codigo_servico',
			'ClienteProdutoServico2.valor',
			'ClienteProduto.codigo_cliente'
		);

		$options['joins'] = array(
			array(
				'table' => 'cliente_produto',
				'alias' => 'ClienteProduto',
				'type' => 'INNER',
				'conditions' => 'ClienteProduto.codigo = ClienteProdutoServico2.codigo_cliente_produto',
			)
		);
		$options['recursive'] = '-1';


		$options['conditions'] = array(
			"ClienteProdutoServico2.codigo_servico = {$codigo_servico} AND ClienteProduto.codigo_cliente = {$codigo_cliente_alocacao}"
		);

		$assinatura = $model_ClienteProdutoServico2->find('first', $options);

		//Se não encontrou assinatura no cliente de alocação (filial)
		if (empty($assinatura)) {

			$options['conditions'] = array(
				"ClienteProdutoServico2.codigo_servico = {$codigo_servico} AND ClienteProduto.codigo_cliente = {$codigo_matriz}"
			);

			$assinatura = $model_ClienteProdutoServico2->find('first', $options);
		}

		return $assinatura;
	}

	//Verifica se existe fornecedor com o exame na lista de preços para o cliente de alocação do funcionario
	public function verificaExameTemFornecedor($codigo_servico, $codigo_cliente_alocacao)
	{

		$model_ClienteFornecedor = &ClassRegistry::init('ClienteFornecedor');

		$options['fields'] = array(
			'ClienteFornecedor.codigo_fornecedor'
		);

		$options['recursive'] = '-1';


		$options['conditions'] = array(
			"ClienteFornecedor.codigo_cliente = {$codigo_cliente_alocacao} ",
			"ClienteFornecedor.ativo = 1",
			" EXISTS (SELECT top 1 *  
						FROM listas_de_preco lp
						INNER JOIN listas_de_preco_produto lpp on lpp.codigo_lista_de_preco = lp.codigo
						INNER JOIN listas_de_preco_produto_servico lpps on lpps.codigo_lista_de_preco_produto = lpp.codigo
					WHERE lpps.codigo_servico = {$codigo_servico} and lp.codigo_fornecedor = ClienteFornecedor.codigo_fornecedor)"
		);

		return $model_ClienteFornecedor->find('all', $options);
	}

	//retorna estrutura de acordo com o codigo_funcionario_setor_cargo
	//Cliente - corresponde ao cliente de alocação
	//Empresa - corresponde a matriz
	public function retornaEstrutura($codigo_funcionario_setor_cargo)
	{

		if (is_numeric($codigo_funcionario_setor_cargo)) {
			$this->FuncionarioSetorCargo = &ClassRegistry::init('FuncionarioSetorCargo');

			$options['fields'] = array(
				'Cliente.codigo',
				'Cliente.razao_social',
				'Cliente.nome_fantasia',
				'Cliente.codigo_documento',
				'Empresa.codigo',
				'Empresa.razao_social',
				'Empresa.nome_fantasia',
				'Empresa.codigo_documento',
				'ClienteContato.descricao',
				'Funcionario.codigo',
				'Funcionario.nome',
				'Funcionario.cpf',
				'Funcionario.data_nascimento',
				'Cargo.codigo',
				'Cargo.descricao',
				'Setor.codigo',
				'Setor.descricao',
				'ClienteEndereco.codigo',
				'ClienteFuncionario.codigo',
				'ClienteFuncionario.codigo_cliente',
				'ClienteFuncionario.codigo_cliente_matricula',
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
					'table' => 'cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao',
				),
				array(
					'table' => 'grupos_economicos_clientes',
					'alias' => 'GrupoEconomicoCliente',
					'type' => 'INNER',
					'conditions' => 'GrupoEconomicoCliente.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao',
				),
				array(
					'table' => 'grupos_economicos',
					'alias' => 'GrupoEconomico',
					'type' => 'INNER',
					'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
				),
				array(
					'table' => 'cliente',
					'alias' => 'Empresa',
					'type' => 'INNER',
					'conditions' => 'Empresa.codigo = GrupoEconomico.codigo_cliente',
				),
				array(
					'table' => 'cliente_endereco',
					'alias' => 'ClienteEndereco',
					'type' => 'LEFT',
					'conditions' => 'Cliente.codigo = ClienteEndereco.codigo_cliente',
				),
				array(
					'table' => 'cliente_contato',
					'alias' => 'ClienteContato',
					'type' => 'LEFT',
					'conditions' => 'Cliente.codigo = ClienteContato.codigo_cliente AND ClienteContato.codigo_tipo_contato = 2 AND ClienteContato.codigo_tipo_retorno = 2',
				),
				array(
					'table' => 'cargos',
					'alias' => 'Cargo',
					'type' => 'INNER',
					'conditions' => 'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo',
				),
				array(
					'table' => 'setores',
					'alias' => 'Setor',
					'type' => 'INNER',
					'conditions' => 'Setor.codigo = FuncionarioSetorCargo.codigo_setor',
				)
			);


			$options['conditions'] = array("FuncionarioSetorCargo.codigo =  $codigo_funcionario_setor_cargo");
			$options['recursive'] = -1;
			return $this->FuncionarioSetorCargo->find('first', $options);
		} else {
			return false;
		}
	}

	public function retornaFornecedoresParaExamesNecessarios($cliente_funcionario, $parametros = array())
	{

		if (is_numeric($cliente_funcionario)) {
			$model_ClienteFuncionario = &ClassRegistry::init('ClienteFuncionario');

			$model_ClienteFuncionario->virtualFields = array(
				'setor' => "(SELECT descricao FROM RHHealth.dbo.setores where codigo = (SELECT TOP 1 codigo_setor FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo AND ((data_fim = '' OR data_fim IS NULL) OR (data_fim is not null AND ClienteFuncionario.ativo = 0)) ORDER BY 1 DESC))",
				'cargo' => "(SELECT descricao FROM RHHealth.dbo.cargos where codigo = (SELECT TOP 1 codigo_cargo FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo  AND ((data_fim = '' OR data_fim IS NULL) OR (data_fim is not null AND ClienteFuncionario.ativo = 0)) ORDER BY 1 DESC))"
			);

			$options['fields'] = array(
				'Cliente.razao_social',
				'Cliente.nome_fantasia',
				'Funcionario.nome',
				// 				'Cargo.codigo',
				// 				'Cargo.descricao',
				// 				'Setor.codigo',
				// 				'Setor.descricao',
				'setor',
				'cargo',
				'AplicacaoExame.exame_admissional',
				'AplicacaoExame.exame_periodico',
				'AplicacaoExame.exame_demissional',
				'AplicacaoExame.exame_retorno',
				'AplicacaoExame.exame_mudanca',
				'AplicacaoExame.pontual',
				'AplicacaoExame.qualidade_vida',
				'AplicacaoExame.exame_excluido_convocacao',
				'AplicacaoExame.exame_excluido_ppp',
				'AplicacaoExame.exame_excluido_aso',
				'AplicacaoExame.exame_excluido_pcmso',
				'AplicacaoExame.exame_excluido_anual',
				'Exame.codigo',
				'Exame.descricao',
				'Exame.codigo_servico',
				'ListaPrecoProdutoServico.valor',
				'Fornecedor.codigo',
				'Fornecedor.razao_social',
				'FornecedorEndereco.numero',
				'FornecedorEndereco.complemento',
				'EnderecoTipo.descricao',
				'Endereco.descricao',
				'EnderecoCidade.descricao',
				'EnderecoEstado.abreviacao'
			);

			$options['joins'] = array(
				array(
					'table' => 'cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente_matricula',
				),
				array(
					'table' => 'funcionarios',
					'alias' => 'Funcionario',
					'type' => 'INNER',
					'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
				),
				// 				array(
				// 					'table' => 'cargos',
				// 					'alias' => 'Cargo',
				// 					'type' => 'INNER',
				// 					'conditions' => 'Cargo.codigo = ClienteFuncionario.codigo_cargo',
				// 					),
				// 				array(
				// 					'table' => 'setores',
				// 					'alias' => 'Setor',
				// 					'type' => 'INNER',
				// 					'conditions' => 'Setor.codigo = ClienteFuncionario.codigo_setor',
				// 					),

				array(
					'table' => 'funcionario_setores_cargos',
					'alias' => 'FuncionarioSetorCargo',
					'type' => 'INNER',
					'conditions' => array(
						'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
						"FuncionarioSetorCargo.data_fim is null OR FuncionarioSetorCargo.data_fim = ''"
					)
				),
				array(
					'table' => 'aplicacao_exames',
					'alias' => 'AplicacaoExame',
					'type' => 'INNER',
					'conditions' => array(
						'AplicacaoExame.codigo_cliente = ClienteFuncionario.codigo_cliente_matricula',
						'AplicacaoExame.codigo_setor = FuncionarioSetorCargo.codigo_setor',
						'AplicacaoExame.codigo_cargo = FuncionarioSetorCargo.codigo_cargo'
					)
				),
				array(
					'table' => 'exames',
					'alias' => 'Exame',
					'type' => 'INNER',
					'conditions' => 'Exame.codigo = AplicacaoExame.codigo_exame',
				),
				array(
					'table' => 'servico',
					'alias' => 'Servico',
					'type' => 'INNER',
					'conditions' => 'Servico.codigo = Exame.codigo_servico',
				),
				array(
					'table' => 'listas_de_preco_produto_servico',
					'alias' => 'ListaPrecoProdutoServico',
					'type' => 'LEFT',
					'conditions' => 'ListaPrecoProdutoServico.codigo_servico = Servico.codigo',
				),
				array(
					'table' => 'listas_de_preco_produto',
					'alias' => 'ListaPrecoProduto',
					'type' => 'INNER',
					'conditions' => 'ListaPrecoProduto.codigo = ListaPrecoProdutoServico.codigo_lista_de_preco_produto',
				),
				array(
					'table' => 'listas_de_preco',
					'alias' => 'ListaPreco',
					'type' => 'INNER',
					'conditions' => 'ListaPreco.codigo = ListaPrecoProduto.codigo_lista_de_preco',
				),
				array(
					'table' => 'fornecedores',
					'alias' => 'Fornecedor',
					'type' => 'INNER',
					'conditions' => 'Fornecedor.codigo = ListaPreco.codigo_fornecedor',
				),
				array(
					'table' => 'fornecedores_endereco',
					'alias' => 'FornecedorEndereco',
					'type' => 'LEFT',
					'conditions' => 'FornecedorEndereco.codigo_fornecedor = Fornecedor.codigo',
				),
				array(
					'table' => 'endereco',
					'alias' => 'Endereco',
					'type' => 'LEFT',
					'conditions' => 'Endereco.codigo = FornecedorEndereco.codigo_endereco',
				),
				array(
					'table' => 'endereco_tipo',
					'alias' => 'EnderecoTipo',
					'type' => 'LEFT',
					'conditions' => 'EnderecoTipo.codigo = Endereco.codigo_endereco_tipo',
				),
				array(
					'table' => 'endereco_cidade',
					'alias' => 'EnderecoCidade',
					'type' => 'LEFT',
					'conditions' => 'EnderecoCidade.codigo = Endereco.codigo_endereco_cidade',
				),
				array(
					'table' => 'endereco_estado',
					'alias' => 'EnderecoEstado',
					'type' => 'LEFT',
					'conditions' => 'EnderecoEstado.codigo = EnderecoCidade.codigo_endereco_estado',
				)
			);

			$options['recursive'] = '-1';
			$options['conditions'] = array("ClienteFuncionario.codigo = {$cliente_funcionario}");

			if ((isset($parametros['latitude_min']) && !empty($parametros['latitude_min'])) && (isset($parametros['latitude_max']) && !empty($parametros['latitude_max'])) && (isset($parametros['longitude_min']) && !empty($parametros['longitude_min'])) && (isset($parametros['longitude_max']) && !empty($parametros['longitude_max']))) {
				$options['conditions'] = array_merge($options['conditions'], array("FornecedorEndereco.latitude BETWEEN {$parametros['latitude_min']} and {$parametros['latitude_max']}"));
				$options['conditions'] = array_merge($options['conditions'], array("FornecedorEndereco.longitude BETWEEN {$parametros['longitude_min']} and {$parametros['longitude_max']}"));
			}

			return $model_ClienteFuncionario->find('all', $options);
		} else {
			return false;
		}
	}

	public function FornecedorHorarioFornecedorHorario($codigo_fornecedor)
	{
		$this->FornecedorHorario = &ClassRegistry::init('FornecedorHorario');
		return $this->FornecedorHorario->find('all', array(
			'conditions' => array(
				'FornecedorHorario.codigo_fornecedor' => 	$codigo_fornecedor
			),
			'fields' => array(
				'FornecedorHorario.de_hora',
				'FornecedorHorario.ate_hora',
				'FornecedorHorario.dias_semana'
			)
		));
	}

	public function retornaFornecedoresParaExamesListados($exames_lista, $parametros, $codigo_cliente)
	{

		$model_Servico = &ClassRegistry::init('Servico');

		$options['fields'] = array(
			'Servico.codigo',
			'Servico.descricao',
			'Servico.telefone',
			'ListaPrecoProdutoServico.codigo',
			'ListaPrecoProdutoServico.valor',
			'ListaPrecoProdutoServico.codigo_servico',
			'ListaPrecoProdutoServico.tipo_atendimento',
			'ListaPreco.codigo_fornecedor',
			'Fornecedor.codigo',
			'Fornecedor.razao_social',
			'Fornecedor.nome',
			'Fornecedor.utiliza_sistema_agendamento',
			'Fornecedor.tipo_atendimento',
			'FornecedorEndereco.numero',
			'FornecedorEndereco.complemento',
			'FornecedorEndereco.latitude',
			'FornecedorEndereco.longitude',
			'FornecedorEndereco.logradouro',
			'FornecedorEndereco.cidade',
			'FornecedorEndereco.estado_descricao',
			'FornecedorEndereco.bairro',
			'Exame.codigo',
			'Exame.descricao'
		);

		$options['joins'] = array(
			array(
				'table' => 'exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'Exame.codigo_servico = Servico.codigo',
			),
			array(
				'table' => 'listas_de_preco_produto_servico',
				'alias' => 'ListaPrecoProdutoServico',
				'type' => 'INNER',
				'conditions' => 'ListaPrecoProdutoServico.codigo_servico = Servico.codigo',
			),
			array(
				'table' => 'listas_de_preco_produto',
				'alias' => 'ListaPrecoProduto',
				'type' => 'INNER',
				'conditions' => 'ListaPrecoProduto.codigo = ListaPrecoProdutoServico.codigo_lista_de_preco_produto',
			),
			array(
				'table' => 'listas_de_preco',
				'alias' => 'ListaPreco',
				'type' => 'INNER',
				'conditions' => 'ListaPreco.codigo = ListaPrecoProduto.codigo_lista_de_preco',
			),
			array(
				'table' => 'fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => 'Fornecedor.codigo = ListaPreco.codigo_fornecedor',
			),
			array(
				'table' => 'fornecedores_endereco',
				'alias' => 'FornecedorEndereco',
				'type' => 'INNER',
				'conditions' => 'FornecedorEndereco.codigo_fornecedor = Fornecedor.codigo',
			),
			array(
				'table' => 'clientes_fornecedores',
				'alias' => 'ClienteFornecedor',
				'type' => 'INNER',
				'conditions' => 'ClienteFornecedor.codigo_fornecedor = Fornecedor.codigo AND ClienteFornecedor.ativo = 1',
			),
		);

		$options['recursive'] = '-1';
		$options['conditions'] = array("ListaPreco.codigo_fornecedor is not null AND Servico.codigo IN ({$exames_lista}) and Servico.ativo  = 1 and Fornecedor.ativo = 1");

		if ((isset($parametros['latitude_min']) && !empty($parametros['latitude_min'])) && (isset($parametros['latitude_max']) && !empty($parametros['latitude_max'])) && (isset($parametros['longitude_min']) && !empty($parametros['longitude_min'])) && (isset($parametros['longitude_max']) && !empty($parametros['longitude_max']))) {
			$options['conditions'] = array_merge($options['conditions'], array("FornecedorEndereco.latitude BETWEEN {$parametros['latitude_min']} and {$parametros['latitude_max']}"));
			$options['conditions'] = array_merge($options['conditions'], array("FornecedorEndereco.longitude BETWEEN {$parametros['longitude_min']} and {$parametros['longitude_max']}"));
		}

		if (isset($codigo_cliente) && !empty($codigo_cliente)) {
			$joins_cliente[] = array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = ClienteFornecedor.codigo_cliente',
			);
			$options['joins']  = array_merge($options['joins'], $joins_cliente);
			$options['conditions'] = array_merge($options['conditions'], array('Cliente.codigo' => $codigo_cliente));
		}

		$model_Servico->bindModel(array(
			'belongsTo' => array(
				'Fornecedor' => array(
					'alias' => 'Fornecedor',
					'foreignKey' => FALSE,
				)
			)
		));

		$model_Servico->virtualFields = array(
			'telefone' => 'SELECT TOP 1 
			CONCAT( (CASE WHEN ddd IS NOT NULL THEN CONCAT(ddd, \'-\') ELSE \'\' END) , descricao) 
			FROM fornecedores_contato 
			WHERE codigo_fornecedor = Fornecedor.codigo 
			AND codigo_tipo_retorno = 1 
			ORDER BY codigo DESC',
		);

		// debug($model_Servico->find('sql', $options));exit;

		return $model_Servico->find('all', $options);
	}

	public function retornaPedido($codigo_pedido)
	{
		$options['conditions'] = array("PedidoExame.codigo = {$codigo_pedido}");
		$options['fields'] = array('Funcionario.nome', 'Cliente.razao_social', 'PedidoExame.codigo_cliente_funcionario', 'ClienteFuncionario.codigo_cliente_matricula');

		$options['joins'] = array(
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'PedidoExame.codigo_cliente_funcionario = ClienteFuncionario.codigo',
			),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
			),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo_cliente_matricula = Cliente.codigo',
			)
		);

		return $this->find('first', $options);
	}

	public function retornaPedidosFuncionario($codigo_funcionario_setor_cargo = null, $codigo_pedido_exame = null, $tipo = null)
	{

		if (isset($tipo) && $tipo == 'log_pedidos') {
			$options['conditions'] = array("PedidoExame.codigo" => $codigo_pedido_exame);
		} else {
			$options['conditions'][] = array("PedidoExame.em_emissao IS NULL");
			$options['conditions'] = array("FuncionarioSetorCargo.codigo = {$codigo_funcionario_setor_cargo}");
		}

		$this->virtualFields['valor_total'] = '(select sum(valor) from itens_pedidos_exames where codigo_pedidos_exames = PedidoExame.codigo)';
		$this->virtualFields['baixa_ultimo_exame'] = '(select sum(valor) from itens_pedidos_exames where codigo_pedidos_exames = PedidoExame.codigo)';

		$options['fields'] = array(
			'Funcionario.nome',
			'Funcionario.codigo',
			'Cliente.razao_social',
			'Cliente.nome_fantasia',
			'PedidoExame.codigo_status_pedidos_exames',
			'PedidoExame.codigo_cliente_funcionario',
			'PedidoExame.codigo_func_setor_cargo',
			'PedidoExame.codigo',
			'PedidoExame.data_inclusao',
			'PedidoExame.valor_total',
			'PedidoExame.exame_admissional',
			'PedidoExame.exame_periodico',
			'PedidoExame.exame_demissional',
			'PedidoExame.exame_retorno',
			'PedidoExame.exame_mudanca',
			'PedidoExame.exame_monitoracao',
			'PedidoExame.pontual',
			'PedidoExame.qualidade_vida',
			'(SELECT 
			top 1
			CONVERT(VARCHAR(24),IPEB.data_realizacao_exame,103)
			FROM 
			itens_pedidos_exames_baixa IPEB
			INNER JOIN itens_pedidos_exames IPE ON (IPEB.codigo_itens_pedidos_exames = IPE.codigo)
			WHERE 
			IPE.codigo_pedidos_exames = PedidoExame.codigo) as baixa_ultimo_exame',
			'PedidoExame.codigo_status_pedidos_exames',
			'StatusPedidoExame.descricao AS _status_',
			'StatusPedidoExame.codigo AS _codigo_status_',
			'(select top 1
				ipe.codigo
			from pedidos_exames pe
			inner join itens_pedidos_exames ipe on ipe.codigo_pedidos_exames = pe.codigo
			left join itens_pedidos_exames_baixa ipeb on ipeb.codigo_itens_pedidos_exames = ipe.codigo
			where pe.codigo = PedidoExame.codigo
				and ipe.tipo_atendimento = 1
				and ipeb.data_inclusao is null) as exame_baixa'
		);

		$options['order'] = array('PedidoExame.data_inclusao DESC');
		$options['recursive'] = -1;

		//tratamento feito para buscar informacoes para o log
		if (isset($tipo) && $tipo == 'log_pedidos') {
			$options['joins'] = array(
				array(
					'table' => 'cliente_funcionario',
					'alias' => 'ClienteFuncionario',
					'type' => 'INNER',
					'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario'
				),
				array(
					'table' => 'funcionarios',
					'alias' => 'Funcionario',
					'type' => 'INNER',
					'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
				),
				array(
					'table' => 'cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente'
				),
				array(
					'table' => 'status_pedidos_exames',
					'alias' => 'StatusPedidoExame',
					'type' => 'INNER',
					'conditions' => 'StatusPedidoExame.codigo = PedidoExame.codigo_status_pedidos_exames'
				)
			);
		} else {
			$options['joins'] = array(
				array(
					'table' => 'funcionario_setores_cargos',
					'alias' => 'FuncionarioSetorCargo',
					'type' => 'INNER',
					'conditions' => 'PedidoExame.codigo_cliente_funcionario = FuncionarioSetorCargo.codigo_cliente_funcionario'
				),
				array(
					'table' => 'cliente_funcionario',
					'alias' => 'ClienteFuncionario',
					'type' => 'INNER',
					'conditions' => 'ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario'
				),
				array(
					'table' => 'funcionarios',
					'alias' => 'Funcionario',
					'type' => 'INNER',
					'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
				),
				array(
					'table' => 'cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao'
				),
				array(
					'table' => 'status_pedidos_exames',
					'alias' => 'StatusPedidoExame',
					'type' => 'INNER',
					'conditions' => 'StatusPedidoExame.codigo = PedidoExame.codigo_status_pedidos_exames'
				)
			);
		}

		// pr($this->find('sql', $options));

		return $this->find('all', $options);
	}

	public function verificaAnexo($codigo_pedido_exame)
	{
		$Configuracao = &ClassRegistry::init('Configuracao');
		$codigo_aso = $Configuracao->getChave('INSERE_EXAME_CLINICO');
		$fields = array(
			'ItemPedidoExame.codigo',
			'AnexoExame.caminho_arquivo',
			'AnexoExame.aprovado_auditoria',
			'AuditoriaExame.codigo_status_auditoria_imagem',
			'ItemPedidoExame.codigo_exame'
		);
		$conditions = array(
			'ItemPedidoExame.codigo_exame' => $codigo_aso,
			'ItemPedidoExame.codigo_pedidos_exames' => $codigo_pedido_exame
		);

		$joins =  array(
			array(
				'table' => 'Rhhealth.dbo.anexos_exames',
				'alias' => 'AnexoExame',
				'type' => 'INNER',
				'conditions' => 'AnexoExame.codigo_item_pedido_exame = ItemPedidoExame.codigo',
			),
			array(
				'table' => 'auditoria_exames',
				'alias' => 'AuditoriaExame',
				'type' => 'INNER',
				'conditions' => 'AuditoriaExame.codigo_item_pedido_exame = ItemPedidoExame.codigo',
			)
		);

		$ItemPedidoExame = &ClassRegistry::init('ItemPedidoExame');
		$item_anexo = array();
		$item_anexo = $ItemPedidoExame->find('first', array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields));


		return $item_anexo;
	}

	public function retornaItensDoPedidoExame($codigo_pedido, $codigo_fornecedor = null)
	{
		$ItemPedidoExame = &ClassRegistry::init('ItemPedidoExame');

		$options['conditions'] = array("ItemPedidoExame.codigo_pedidos_exames = {$codigo_pedido}");
		$options['fields'] = array(
			'Fornecedor.codigo',
			'Fornecedor.razao_social',
			'Fornecedor.tipo_atendimento',
			'Exame.descricao',
			'Exame.codigo_servico',
			'Exame.exame_audiometria',
			'PedidoExame.codigo',
			'PedidoExame.codigo_cliente_funcionario',
			'CASE WHEN (PedidoExame.exame_admissional = 1) THEN \'ADMISSIONAL\'
				  WHEN (PedidoExame.exame_periodico = 1)  THEN \'PERIÓDICO\' 
				  WHEN (PedidoExame.exame_demissional = 1)  THEN \'DEMISSIONAL\' 
				  WHEN (PedidoExame.exame_retorno = 1)  THEN \'RETORNO AO TRABALHO\' 
				  WHEN (PedidoExame.exame_mudanca = 1)  THEN \'MUDANCA DE FUNCAO\'
				  WHEN (PedidoExame.exame_monitoracao = 1)  THEN \'MONITORAÇÃO PONTUAL\'
			ELSE \'PONTUAL\' END as tipo_ocupacional_pedido',
			'ItemPedidoExame.valor',
			'ItemPedidoExame.*',
			'FornecedorEndereco.logradouro',
			'FornecedorEndereco.numero',
			'FornecedorEndereco.cidade',
			'FornecedorEndereco.estado_descricao',
			'FornecedorEndereco.bairro',
			'FornecedorEndereco.complemento',
			'(SELECT TOP 1 descricao 
			FROM fornecedores_contato 
			WHERE fornecedores_contato.codigo_fornecedor = Fornecedor.codigo AND fornecedores_contato.codigo_tipo_retorno = 2
			) as email_fornecedor',
			'CASE WHEN ListaPrecoProdutoServico.tipo_atendimento = 0 OR ListaPrecoProdutoServico.tipo_atendimento IS NULL THEN 0 ELSE ListaPrecoProdutoServico.tipo_atendimento END AS tipo_atendimento_exame'
		);
		$options['joins'] = array(
			array(
				'table' => 'fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_fornecedor = Fornecedor.codigo',
			),
			array(
				'table' => 'exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'Exame.codigo = ItemPedidoExame.codigo_exame',
			),
			array(
				'table' => 'pedidos_exames',
				'alias' => 'PedidoExame',
				'type' => 'INNER',
				'conditions' => 'PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames',
			),
			array(
				'table' => 'fornecedores_endereco',
				'alias' => 'FornecedorEndereco',
				'type' => 'LEFT',
				'conditions' => array('FornecedorEndereco.codigo_fornecedor = Fornecedor.codigo')
			),
			array(
				'table' => 'listas_de_preco',
				'alias' => 'ListaPreco',
				'type' => 'INNER',
				'conditions' => array('ListaPreco.codigo_fornecedor = Fornecedor.codigo')
			),
			array(
				'table' => 'listas_de_preco_produto',
				'alias' => 'ListaPrecoProduto',
				'type' => 'INNER',
				'conditions' => array('ListaPrecoProduto.codigo_lista_de_preco = ListaPreco.codigo')
			),
			array(
				'table' => 'listas_de_preco_produto_servico',
				'alias' => 'ListaPrecoProdutoServico',
				'type' => 'INNER',
				'conditions' => array('ListaPrecoProdutoServico.codigo_lista_de_preco_produto = ListaPrecoProduto.codigo AND ListaPrecoProdutoServico.codigo_servico = Exame.codigo_servico')
			),
		);

		if (!empty($codigo_fornecedor) && is_numeric($codigo_fornecedor) && $codigo_fornecedor > 0) {

			$options['conditions']['ItemPedidoExame.codigo_fornecedor'] = $codigo_fornecedor;
		}


		// $this->log($ItemPedidoExame->find('sql', $options), 'debug');

		return $ItemPedidoExame->find('all', $options);
	}

	public function retornaDadosClienteFuncionario($id_pedido)
	{

		$options['fields'] = array('Cliente.nome', '');
		$options['conditions'] = array('PedidoExame.codigo' => $id_pedido);
		$options['joins'] = array(
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => array('ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario')
			),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => array('Cliente.codigo = ClienteFuncionario.codigo_cliente_matricula')
			),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array('Funcionario.codigo = PedidoExame.codigo_funcionario')
			),
		);


		return $this->find('all', $options);
	}

	public function retornaContatosClienteFuncionario($codigo_funcionario_setor_cargo)
	{

		$FuncionarioSetorCargo = &ClassRegistry::init('FuncionarioSetorCargo');
		$options['conditions'] = array(
			'FuncionarioSetorCargo.codigo' => $codigo_funcionario_setor_cargo
		);
		$options['joins'] = array(
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => array('ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario')
			),
			array(
				'table' 		=> 'cliente',
				'alias' 		=> 'Cliente',
				'type' 			=> 'INNER',
				'conditions' 	=> 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao'
			),
			array(
				'table' 		=> 'cliente_contato',
				'alias' 		=> 'ClienteContato',
				'type' 			=> 'LEFT',
				'conditions' 	=> 'ClienteContato.codigo_cliente = Cliente.codigo AND ClienteContato.codigo_tipo_retorno = 2'
			),
			array(
				'table' 		=> 'funcionarios',
				'alias' 		=> 'Funcionario',
				'type' 			=> 'INNER',
				'conditions'	=> 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
			),
			array(
				'table' 		=> 'funcionarios_contatos',
				'alias' 		=> 'FuncionarioContato',
				'type' 			=> 'LEFT',
				'conditions' 	=> 'FuncionarioContato.codigo_funcionario = Funcionario.codigo AND FuncionarioContato.codigo_tipo_retorno = 2'
			),
		);

		$options['fields'] = array(
			'Cliente.codigo AS cliente_codigo',
			'Cliente.razao_social AS cliente_razao_social',
			'ClienteContato.descricao AS cliente_email',
			'ClienteFuncionario.codigo_funcionario as funcionario_codigo',
			'Funcionario.nome AS funcionario_nome',
			'FuncionarioContato.descricao AS funcionario_email'
		);
		$options['recursive'] = -1;
		$retorno['FuncionarioSetorCargo'] = $FuncionarioSetorCargo->find('first', $options);
		$retorno['FuncionarioSetorCargo'] = $retorno['FuncionarioSetorCargo'][0];
		return $retorno;
	}

	/**
	 * Pega os dados e converte nos filtros
	 */
	public function converteFiltroEmCondition($data)
	{
		$conditions = array();
		if (!empty($data['codigo_pedido_exame'])) {
			$conditions['PedidoExame.codigo'] = $data['codigo_pedido_exame'];
		}

		if (!empty($data['codigo_cliente'])) {
			$conditions['Cliente.codigo'] = $data['codigo_cliente'];
		}

		if (!empty($data['codigo_fornecedor'])) {
			$conditions['Fornecedor.codigo'] = $data['codigo_fornecedor'];
		}

		if (!empty($data['nome_funcionario'])) {
			$conditions["Funcionario.nome LIKE "] = '%' . $data['nome_funcionario'] . '%';
		}

		if (!empty($data['cpf'])) {
			$conditions["Funcionario.cpf"] = Comum::soNumero($data['cpf']);
		}

		if (!empty($data['codigo_status_pedidos_exames'])) {
			$conditions['StatusPedidoExame.codigo'] = $data['codigo_status_pedidos_exames'];
		}

		if (!empty($data['codigo_funcionario'])) {
			$conditions['Funcionario.codigo'] = $data['codigo_funcionario'];
		}

		//seta automaticamente
		if (!isset($data["tipo_periodo"])) {
			$data["tipo_periodo"] = 'A';
		}

		if (!empty($data['tipos_agendamento'])) {
			switch ($data['tipos_agendamento']) {
				case 'A': //agendado

					$conditions['AgendamentoExame.data <> '] = '';

					break;

				case 'O': //ordem chegada

					$conditions['PedidoExame.data_inclusao <> '] = '';
					break;
			}
		}

		if (!empty($data["data_inicio"])) {
			$data_inicio = AppModel::dateToDbDate($data["data_inicio"] . ' 00:00:00');
			$data_fim = AppModel::dateToDbDate($data["data_fim"] . ' 23:59:59');
			switch ($data["tipo_periodo"]) {
				case 'A': //data de agendamento

					//verifica se tem o tipo de agendamento setado
					if (!empty($data['tipos_agendamento'])) {
						switch ($data['tipos_agendamento']) {
							case 'A': //agendado
								$conditions['AgendamentoExame.data >= '] = $data_inicio;
								break;
							case 'O': //ordem chegada
								$conditions['PedidoExame.data_inclusao >= '] = $data_inicio;
								break;
						} //fim switch tipo de agendamento
					} else {
						if (!empty($data["data_inicio"]) && !empty($data["data_fim"])) {
							$conditions['OR'] = array(
								array("AgendamentoExame.data BETWEEN '" . $data_inicio . "' AND '" . $data_fim . "'"),
								array("PedidoExame.data_inclusao BETWEEN '" . $data_inicio . "' AND '" . $data_fim . "'")
							);
						}
					}

					break;
				case 'B': //data de baixa
					$conditions['ItemPedidoExameBaixa.data_inclusao >= '] = $data_inicio;
					break;
				case 'R': //data de resultado
					$conditions['ItemPedidoExameBaixa.data_realizacao_exame >= '] = $data_inicio;
					break;
				case 'E': //data de emissão
					$conditions['PedidoExame.data_solicitacao >= '] = $data_inicio;
					break;
			} //switch
		} //fim if

		if (!empty($data["data_fim"])) {
			switch ($data["tipo_periodo"]) {
				case 'A': //data de agendamento

					//verifica se tem o tipo de agendamento setado
					if (!empty($data['tipos_agendamento'])) {
						switch ($data['tipos_agendamento']) {
							case 'A': //agendado
								$conditions['AgendamentoExame.data <= '] = $data_fim;
								break;
							case 'O': //ordem chegada
								$conditions['PedidoExame.data_inclusao <= '] = $data_fim;
								break;
						} //fim switch tipo de agendamento
					} //fim if tipo agendamento
					break;
				case 'B': //data de baixa
					$conditions['ItemPedidoExameBaixa.data_inclusao <= '] = $data_fim;
					break;
				case 'R': //data de resultado
					$conditions['ItemPedidoExameBaixa.data_realizacao_exame <= '] = $data_fim;
					break;
				case 'E': //data de emissão
					$conditions['PedidoExame.data_solicitacao <= '] = $data_fim;
					break;
			} //switch
		}

		//verifica o tipo do status
		if (!empty($data['tipos_status'])) {
			switch ($data['tipos_status']) {
				case 'R': //realizado
					$conditions['not'] = array('ItemPedidoExame.data_realizacao_exame' => NULL);
					break;
				case 'N': //não compareceu
					$conditions['ItemPedidoExame.data_realizacao_exame'] = NULL;
					$conditions['ItemPedidoExame.compareceu'] = 0;
					break;
				case 'P': //pendente
					$conditions['ItemPedidoExame.data_realizacao_exame'] = NULL;
					$conditions[] = array('ItemPedidoExame.compareceu' => 1, 'ItemPedidoExame.compareceu' => NULL);
					break;
			} //fim switch
		} //fim valor em branco


		//verifica o filtro com_anexo
		if (!empty($data['com_anexo_aso'])) {
			if ($data['com_anexo_aso'] == 'S') {
				$conditions[] = "AnexoExame.codigo IS NOT NULL";
			} else if ($data['com_anexo_aso'] == 'N') {
				$conditions[] = "AnexoExame.codigo IS NULL";
			}
		} //fim data com anexo

		// //verifica o filtro com_anexo ficha_clinica
		if (!empty($data['com_anexo_ficha_clinica'])) {
			if ($data['com_anexo_ficha_clinica'] == 'S') {
				$conditions[] = "AnexoFichaClinica.codigo IS NOT NULL";
			} else if ($data['com_anexo_ficha_clinica'] == 'N') {
				$conditions[] = "AnexoFichaClinica.codigo IS NULL";
			}
		} //fim data com anexo

		if (!empty($data['exames'])) { //exame
			$conditions['Exame.codigo'] = $data['exames'];
		}

		if (!empty($data['tipo_exames'])) {
			if ($data['tipo_exames'] == 'aec') { //anexo exame complementar

				$conditions['Servico.tipo_servico'] = "E";

				if (!empty($data['anexo'])) { //anexo
					if ($data['anexo'] == 'S') { //com anexo
						$conditions[] = "AnexoExame.codigo IS NOT NULL";
					} else if ($data['anexo'] == 'N') { //sem anexo
						$conditions[] = "AnexoExame.codigo IS NULL";
					}
				}
			} else if ($data['tipo_exames'] == 'afc') { //anexo ficha clinica

				$conditions[] = "FichaClinica.codigo IS NOT NULL";

				if (!empty($data['anexo'])) { //anexo
					if ($data['anexo'] == 'S') { //com anexo
						$conditions[] = "AnexoFichaClinica.codigo IS NOT NULL";
					} else if ($data['anexo'] == 'N') { //sem anexo
						$conditions[] = "AnexoFichaClinica.codigo IS NULL";
					}
				}
			}
		}

		if (!empty($data['anexo']) && empty($data['tipo_exames'])) { //anexo
			if ($data['anexo'] == 'S') { //com anexo
				$conditions[] = "AnexoExame.codigo IS NOT NULL";
				$conditions[] = "AnexoFichaClinica.codigo IS NOT NULL";
			} else if ($data['anexo'] == 'N') { //sem anexo
				$conditions[] = "AnexoExame.codigo IS NULL";
				$conditions[] = "AnexoFichaClinica.codigo IS NULL";
			}
		}

		// die(debug($conditions));
		return $conditions;
	} //fim converteFiltroEmCondition

	public function converteFiltroEmConditionBaixa($data)
	{

		$conditions = array();
		if (!empty($data['codigo_pedido']))
			$conditions['PedidoExame.codigo'] = $data['codigo_pedido'];

		if (!empty($data['codigo_cliente']))
			$conditions['Cliente.codigo'] = $data['codigo_cliente'];

		if (!empty($data['codigo_fornecedor']))
			$conditions['Fornecedor.codigo'] = $data['codigo_fornecedor'];

		if (!empty($data['nome_funcionario']))
			$conditions["Funcionario.nome LIKE "] = '%' . $data['nome_funcionario'] . '%';

		if (!empty($data['codigo_status_pedidos_exames']))
			$conditions['StatusPedidoExame.codigo'] = $data['codigo_status_pedidos_exames'];

		if (!empty($data['codigo_funcionario']))
			$conditions['Funcionario.codigo'] = $data['codigo_funcionario'];

		if (!empty($data['data']))
			$conditions['AgendamentoExame.data'] = AppModel::dateToDbDate($data['data']);

		return $conditions;
	}

	public function converteFiltroEmConditionEmitidos($data)
	{

		$conditions = array();
		if (!empty($data['codigo_pedido']))
			$conditions['PedidoExame.codigo'] = $data['codigo_pedido'];

		if (!empty($data['codigo_cliente']))
			$conditions['Cliente.codigo'] = $data['codigo_cliente'];

		if (!empty($data['nome_funcionario']))
			$conditions["Funcionario.nome LIKE "] = '%' . $data['nome_funcionario'] . '%';


		if (!empty($data['data_inclusao'])) {
			$conditions['CAST(PedidoExame.data_inclusao AS DATE)'] = AppModel::dateToDbDate($data['data_inclusao']);
		}

		return $conditions;
	}

	public function retornaEmailFuncionario($codigo_funcionario)
	{
		$this->FuncionarioContato = &ClassRegistry::init('FuncionarioContato');
		$this->FuncionarioContato->virtualFields = array('codigo' => '\'email\'');

		return $this->FuncionarioContato->find(
			'list',
			array(
				'conditions' => array(
					'FuncionarioContato.codigo_funcionario' => $codigo_funcionario,
					'FuncionarioContato.codigo_tipo_retorno' => 2
				),
				'limit' => 1,
				'order' => 'FuncionarioContato.data_inclusao ASC',
				'fields' => array(
					'FuncionarioContato.codigo',
					'FuncionarioContato.descricao'
				)
			)
		);
	}

	public function retornaEmailFornecedor($codigo_fornecedor)
	{
		$this->FornecedorContato = &ClassRegistry::init('FornecedorContato');

		return $this->FornecedorContato->find(
			'list',
			array(
				'conditions' => array(
					'FornecedorContato.codigo_fornecedor' => $codigo_fornecedor,
					'FornecedorContato.codigo_tipo_retorno' => 2,
					'FornecedorContato.codigo_tipo_contato' => 14
				),
				'order' => 'FornecedorContato.data_inclusao ASC',
				'fields' => array(
					'FornecedorContato.codigo',
					'FornecedorContato.descricao'
				)
			)
		);
	}

	public function retornaTipoExame($codigo_pedido_exame = null)
	{
		$tipo_exame = $this->query("SELECT
			CASE
			WHEN exame_admissional = 1 THEN 'Exame admissional'
			WHEN exame_periodico = 1 THEN 'Exame periódico'
			WHEN exame_demissional = 1 THEN 'Exame demissional'
			WHEN exame_retorno = 1 THEN 'Retorno ao trabalho'
			WHEN exame_mudanca = 1 THEN 'Mudança de riscos ocupacionais'
			WHEN qualidade_vida = 1 THEN 'Qualidade de vida'
			ELSE ''
			END AS tipo_exame
			FROM pedidos_exames
			WHERE CODIGO = " . $codigo_pedido_exame . "
			");
		return $tipo_exame[0][0]['tipo_exame'];
	}

	public function disparaEmail($dados, $assunto, $template, $to, $attachment = null)
	{


		if (Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO) {
			$to = 'tid@ithealth.com.br';
			$cc = null;
		} else {
			$cc = 'agendamento@rhhealth.com.br';
		}

		App::import('Component', array('StringView', 'Mailer.Scheduler'));

		$this->stringView = new StringViewComponent();
		$this->scheduler = new SchedulerComponent();
		$this->stringView->reset();
		$this->stringView->set('dados', $dados);

		$content = $this->stringView->renderMail($template);

		return $this->scheduler->schedule($content, array(
			'from' => 'portal@rhhealth.com.br',
			'to' => $to,
			'cc' => $cc,
			'subject' => $assunto,
			'attachments' => $attachment
		));
	}

	public function verificaFuncionarioTemPpra($codigo_funcionario_setor_cargo)
	{
		$this->ClienteFuncionario = &ClassRegistry::init('ClienteFuncionario');
		$joins = array(
			array(
				'table' 		=> 'funcionario_setores_cargos',
				'alias' 		=> 'FuncionarioSetorCargo',
				'type' 			=> 'INNER',
				'conditions'    => 'ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario'
			),
		);
		$dados_funcionario = $this->ClienteFuncionario->find('first', array('joins' => $joins, 'conditions' => array('FuncionarioSetorCargo.codigo' => $codigo_funcionario_setor_cargo)));
		$codigo_funcionario = $dados_funcionario['ClienteFuncionario']['codigo_funcionario'];


		//solicitado para incluir os riscos para validar se tem ppra
		$this->GrupoExposicao = &ClassRegistry::init('GrupoExposicao');
		$options = array(
			'fields' => array(
				'GrupoExposicao.codigo',
				'GrupoExposicao.codigo_cargo',
				'GrupoExposicao.codigo_funcionario'
			),
			'joins' => array(
				array(
					'table' 		=> 'clientes_setores',
					'alias' 		=> 'ClienteSetor',
					'type' 			=> 'INNER',
					'conditions'    => 'ClienteSetor.codigo = GrupoExposicao.codigo_cliente_setor'
				),
				array(
					'table' 		=> 'grupos_exposicao_risco',
					'alias' 		=> 'GrupoExposicaoRisco',
					'type' 			=> 'INNER',
					'conditions'    => 'GrupoExposicao.codigo = GrupoExposicaoRisco.codigo_grupo_exposicao'
				),
				array(
					'table' 		=> 'funcionario_setores_cargos',
					'alias' 		=> 'FuncionarioSetorCargo',
					'type' 			=> 'INNER',
					'conditions'    => 'FuncionarioSetorCargo.codigo_setor = ClienteSetor.codigo_setor '
						. 'AND FuncionarioSetorCargo.codigo_cargo = GrupoExposicao.codigo_cargo '
						. 'AND FuncionarioSetorCargo.codigo_cliente_alocacao = ClienteSetor.codigo_cliente_alocacao'
				),
				array(
					'table' 		=> 'cliente_funcionario',
					'alias' 		=> 'ClienteFuncionario',
					'type' 			=> 'INNER',
					'conditions'    => 'ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario'
				),
			),
			'conditions' => array(
				'FuncionarioSetorCargo.codigo' => $codigo_funcionario_setor_cargo,
				'(GrupoExposicao.codigo_funcionario = ' . $codigo_funcionario . ' OR GrupoExposicao.codigo_funcionario IS NULL)',
				'GrupoExposicao.codigo IN (select * from dbo.ufn_grupo_exposicao(FuncionarioSetorCargo.codigo_cliente_alocacao,FuncionarioSetorCargo.codigo_setor,FuncionarioSetorCargo.codigo_cargo,ClienteFuncionario.codigo_funcionario))'
			)
		);

		$dados_ppra = $this->GrupoExposicao->find('first', $options);

		// debug($this->GrupoExposicao->find('sql', $options));exit;

		return $dados_ppra;
	}

	//Retorna Funcionario através do codigo_funcionario_setor_cargo
	public function retornaFuncionario($codigo_funcionario_setor_cargo)
	{
		$this->FuncionarioSetorCargo = &ClassRegistry::init('FuncionarioSetorCargo');
		$this->FuncionarioSetorCargo->bindModel(array(
			'belongsTo' => array(
				'ClienteFuncionario' => array('foreignKey' => 'codigo_cliente_funcionario'),
				'Funcionario' => array('foreignKey' => false, 'conditions' => array('Funcionario.codigo = ClienteFuncionario.codigo_funcionario')),
			)
		));

		$options['fields'] = array(
			'Funcionario.codigo',
			'Funcionario.nome'
		);

		$options['group'] = array(
			'Funcionario.codigo',
			'Funcionario.nome'
		);

		$options['conditions']  = array("FuncionarioSetorCargo.codigo" => $codigo_funcionario_setor_cargo);

		return $this->FuncionarioSetorCargo->find('all', $options);
	}

	//Retorna FuncionarioSetorCargo através do código do pedido de exame
	public function retornaFuncionarioSetorCargo($codigo_pedido)
	{
		$options['conditions'] = array(
			'PedidoExame.codigo' => $codigo_pedido
		);
		$options['joins'] = array(
			array(
				'table' => 'funcionario_setores_cargos',
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => array('FuncionarioSetorCargo.codigo = PedidoExame.codigo_func_setor_cargo')
			),
		);

		$options['fields'] = array(
			'FuncionarioSetorCargo.codigo',
			'FuncionarioSetorCargo.codigo_cliente_alocacao',
			'FuncionarioSetorCargo.codigo_cliente_funcionario',
		);
		$options['recursive'] = -1;

		return $this->find('first', $options);
	}


	public function paginateCount($conditions = null, $recursive = -1, $extra = array(), $fields = array())
	{
		$extra['conditions'] = $conditions;
		$extra['recursive'] = $recursive;
		$extra['fields'] = array($this->name . '.codigo');
		return count($this->find('all', $extra));
	}

	/**
	 * Metodo para converter os parametros passados em condições para o banco de dados
	 */
	public function converteFiltrosEmConditions($data)
	{
		$conditions = array();

		if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente'])) {

			$GrupoEconomicoCliente = ClassRegistry::init('GrupoEconomicoCliente');
			//pega o grupo economico
			$gec = $GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $data['codigo_cliente'])));
			//seta a conditions
			$conditions['GrupoEconomicoCliente.codigo_grupo_economico'] = $gec['GrupoEconomicoCliente']['codigo_grupo_economico'];

			// $conditions['OR'] = array(
			// 							'GrupoEconomico.codigo_cliente' => $data['codigo_cliente'],
			// 							'GrupoEconomicoCliente.codigo_cliente' => $data['codigo_cliente']
			// 						);

		}

		if (isset($data['codigo_cliente_alocacao']) && !empty($data['codigo_cliente_alocacao'])) {
			$conditions['FuncionarioSetorCargo.codigo_cliente_alocacao'] = $data['codigo_cliente_alocacao'] == -1 ? null : $data['codigo_cliente_alocacao'];
		}

		if (isset($data['codigo_funcionario']) && !empty($data['codigo_funcionario'])) {
			$conditions['ClienteFuncionario.codigo_funcionario'] = $data['codigo_funcionario'] == -1 ? null : $data['codigo_funcionario'];
		}

		if (isset($data['codigo_setor']) && !empty($data['codigo_setor'])) {
			$conditions['FuncionarioSetorCargo.codigo_setor'] = $data['codigo_setor'] == -1 ? null : $data['codigo_setor'];
		}

		if (isset($data['codigo_cargo']) && !empty($data['codigo_cargo'])) {
			$conditions['FuncionarioSetorCargo.codigo_cargo'] = $data['codigo_cargo'] == -1 ? null : $data['codigo_cargo'];
		}

		if (isset($data['data_inicio']) && !empty($data['data_inicio'])) {
			if ($data['tipo_periodo'] == 'E') { //emissao
				$tipo = 'CAST(PedidoExame.data_solicitacao AS DATE)';
			} elseif ($data['tipo_periodo'] == 'R') { //resultado
				$tipo = 'CAST(ItemPedidoExameBaixa.data_realizacao_exame AS DATE)';
			} else { //baixa
				$tipo = 'ItemPedidoExameBaixa.data_inclusao';
			}
			$conditions[$tipo . ' >='] = AppModel::dateToDbDate($data['data_inicio']) . ' 00:00:00';
		}

		if (isset($data['data_fim']) && !empty($data['data_fim'])) {
			if ($data['tipo_periodo'] == 'E') { //emissao
				$tipo = 'CAST(PedidoExame.data_solicitacao AS DATE)';
			} elseif ($data['tipo_periodo'] == 'R') { //resultado
				$tipo = 'CAST(ItemPedidoExameBaixa.data_realizacao_exame AS DATE)';
			} else { //baixa
				$tipo = 'ItemPedidoExameBaixa.data_inclusao';
			}
			$conditions[$tipo . ' <='] = AppModel::dateToDbDate($data['data_fim']) . ' 23:59:59';
		}

		if (isset($data['tipo_exame']) && !empty($data['tipo_exame'])) {
			switch ($data['tipo_exame']) {
				case '1':
					$conditions['PedidoExame.exame_admissional'] = 1;
					break;

				case '2':
					$conditions['PedidoExame.exame_periodico'] = 1;
					break;

				case '3':
					$conditions['PedidoExame.exame_demissional'] = 1;
					break;

				case '4':
					$conditions['PedidoExame.exame_retorno'] = 1;
					break;

				case '5':
					$conditions['PedidoExame.exame_mudanca'] = 1;
					break;

				case '6':
					$conditions['PedidoExame.exame_monitoracao'] = 1;
					break;

				case '7':
					$conditions['PedidoExame.pontual'] = 1;
					break;
			}
		}

		//filtro de cidade unidade
		if (isset($data['codigo_cidade_unidade']) && !empty($data['codigo_cidade_unidade'])) {
			$conditions['ClienteEndereco.cidade'] = $data['codigo_cidade_unidade'];
		} //fim filtro cidade unidade

		//filtro de estado unidade
		if (isset($data['codigo_estado_unidade']) && !empty($data['codigo_estado_unidade'])) {
			$conditions['ClienteEndereco.estado_abreviacao'] = $data['codigo_estado_unidade'];
		} //fim filtro estado unidade		

		//filtro de cidade fornecedor
		if (isset($data['codigo_cidade_fornecedor']) && !empty($data['codigo_cidade_fornecedor'])) {
			$conditions['FornecedorEndereco.cidade'] = $data['codigo_cidade_fornecedor'];
		} //fim filtro cidade unidade

		//filtro de estado unidade
		if (isset($data['codigo_estado_fornecedor']) && !empty($data['codigo_estado_fornecedor'])) {
			$conditions['FornecedorEndereco.estado_descricao'] = $data['codigo_estado_fornecedor'];
		} //fim filtro estado unidade		

		//filtro de matricula
		if (isset($data['matricula']) && !empty($data['matricula'])) {
			$conditions['ClienteFuncionario.matricula'] = $data['matricula'];
		} //fim filtro estado unidade


		return $conditions;
	} //fim converteFiltrosEmConditions

	/**
	 * CONSTANTES PARA AJUDAR NO RELATORIO DE BAIXA DE EXAMES
	 */
	const AGRP_UNIDADE = 1;
	const AGRP_SETOR = 2;
	const AGRP_CARGO = 3;
	const AGRP_TIPO_EXAME = 4;
	const AGRP_TIPO_RESULTADO = 5;

	/**
	 * metodo para pegar os dados no detalhe da consulta e montar o grid de relatorio analitico da baixa de exames
	 */
	// public function baixa_exames_analitico($type, $options) {
	public function baixa_exames_analitico($type = null, $options)
	{

		//realiza os relacionamentos da para montar a query		
		$joins = array(
			array(
				'table' => 'RHHealth.dbo.itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => array('ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo')
			),
			array(
				'table' => 'RHHealth.dbo.itens_pedidos_exames_baixa',
				'alias' => 'ItemPedidoExameBaixa',
				'type' => 'INNER',
				'conditions' => array('ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo')
			),
			array(
				'table' => 'RHHealth.dbo.exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => array('ItemPedidoExame.codigo_exame = Exame.codigo')
			),
			array(
				'table' => 'RHHealth.dbo.fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => array('ItemPedidoExame.codigo_fornecedor = Fornecedor.codigo')
			),
			array(
				'table' => 'RHHealth.dbo.fornecedores_endereco',
				'alias' => 'FornecedorEndereco',
				'type' => 'INNER',
				'conditions' => array('FornecedorEndereco.codigo_fornecedor = Fornecedor.codigo')
			),
			array(
				'table' => 'RHHealth.dbo.cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => array('ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario')
			),
			array(
				'table' => 'RHHealth.dbo.funcionario_setores_cargos',
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => array('FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo AND PedidoExame.codigo_func_setor_cargo = FuncionarioSetorCargo.codigo')
			),
			array(
				'table' => 'RHHealth.dbo.setores',
				'alias' => 'Setor',
				'type' => 'INNER',
				'conditions' => array('FuncionarioSetorCargo.codigo_setor = Setor.codigo')
			),
			array(
				'table' => 'RHHealth.dbo.cargos',
				'alias' => 'Cargo',
				'type' => 'INNER',
				'conditions' => array('FuncionarioSetorCargo.codigo_cargo = Cargo.codigo')
			),
			array(
				'table' => 'RHHealth.dbo.funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array('Funcionario.codigo = ClienteFuncionario.codigo_funcionario')
			),
			array(
				'table' => 'RHHealth.dbo.cliente',
				'alias' => 'Unidade',
				'type' => 'LEFT',
				// 'conditions' => array('FuncionarioSetorCargo.codigo_cliente = Unidade.codigo AND Unidade.codigo = PedidoExame.codigo_cliente')
				'conditions' => array('Unidade.codigo = PedidoExame.codigo_cliente')
			),
			array(
				'table' => 'RHHealth.dbo.cliente_endereco',
				'alias' => 'ClienteEndereco',
				'type' => 'INNER',
				'conditions' => array('ClienteEndereco.codigo = (SELECT TOP 1 codigo FROM RHHealth.dbo.cliente_endereco WHERE codigo_cliente = Unidade.codigo)')
			),
			array(
				'table' => 'RHHealth.dbo.grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array('GrupoEconomicoCliente.codigo_cliente = PedidoExame.codigo_cliente')
			),
			array(
				'table' => 'RHHealth.dbo.grupos_economicos',
				'alias' => 'GrupoEconomico',
				'type' => 'INNER',
				'conditions' => array('GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo')
			),
			array(
				'table' => 'RHHealth.dbo.tipos_resultados',
				'alias' => 'TiposResultados',
				'type' => 'LEFT', //ajuste feito para o chamado CDCT-294, o resultado é agora opcional ter no pedido
				'conditions' => array('TiposResultados.codigo = ItemPedidoExameBaixa.resultado')
			),
		);

		//campos
		$fields = array(
			'PedidoExame.codigo AS codigo',
			'GrupoEconomico.descricao AS cliente',
			'ItemPedidoExameBaixa.codigo AS codigo_ipeb',
			'ItemPedidoExameBaixa.resultado AS resultado',
			'TiposResultados.codigo AS tipo_resultado_codigo',
			'TiposResultados.descricao AS tipo_resultado',
			'Unidade.codigo AS unidade_codigo',
			'Unidade.nome_fantasia AS unidade_nome_fantasia',
			'ClienteEndereco.cidade AS cliente_cidade',
			'ClienteEndereco.estado_abreviacao AS cliente_estado',
			'Funcionario.nome AS funcionario',
			'Funcionario.cpf AS cpf',
			'Exame.codigo AS exame_codigo',
			'Exame.descricao AS exame_descricao',
			'Setor.codigo AS setor_codigo',
			'Setor.descricao AS setor_descricao',
			'Cargo.codigo AS cargo_codigo',
			'Cargo.descricao AS cargo_descricao',
			'Fornecedor.nome AS credenciado',
			'FornecedorEndereco.cidade AS fornecedor_cidade',
			'FornecedorEndereco.estado_descricao AS fornecedor_estado',
			'ClienteFuncionario.matricula AS matricula',
			'CONVERT(VARCHAR, PedidoExame.data_solicitacao, 120) AS data_emissao',
			'CONVERT(VARCHAR, ItemPedidoExameBaixa.data_realizacao_exame, 120) AS data_resultado',
			'CONVERT(VARCHAR, ItemPedidoExameBaixa.data_inclusao, 120) AS data_baixa',
			"CASE 
				WHEN PedidoExame.exame_admissional = 1 THEN 'Admissional' 
				WHEN PedidoExame.exame_demissional = 1 THEN 'Demissional' 
				WHEN PedidoExame.exame_mudanca = 1 THEN 'Mudança' 
				WHEN PedidoExame.exame_periodico = 1 THEN 'Periódico' 
				WHEN PedidoExame.exame_retorno = 1 THEN 'Retorno' 
				WHEN PedidoExame.exame_monitoracao = 1 THEN 'Monitoração' 
			ELSE 'Pontual' 
			END AS tipo_exame",
			"CASE
			    WHEN PedidoExame.exame_admissional = 1 THEN '1'
			    WHEN PedidoExame.exame_periodico = 1 THEN '2'
			    WHEN PedidoExame.exame_demissional = 1 THEN '3'
			    WHEN PedidoExame.exame_retorno = 1 THEN '4'
			    WHEN PedidoExame.exame_mudanca = 1 THEN '5'
			    WHEN PedidoExame.exame_monitoracao = 1 THEN '6'
			    ELSE '7'
			  END AS tipo_exame_codigo",
			"CASE WHEN ItemPedidoExameBaixa.fornecedor_particular = 1 THEN 'SIM'
			ELSE 'NÃO' END AS fornecedor_particular",
			"(CASE WHEN ItemPedidoExame.respondido_lyn = 1 THEN 'SIM' ELSE 'NÃO' END) AS respondido_lyn"
		);

		if (!empty($type)) {
			//pega as conditions que foi montada
			$conditions = $options['conditions'];
		}

		if (empty($type)) {
			$dados = array(
				'conditions' => $options,
				'joins' => $joins,
				'fields' => $fields
			);
		}

		if (!empty($type)) {
			//retorna o resultado que foi solicitado all, sql, first
			return $this->find($type, compact('fields', 'joins', 'conditions'));
		} else {
			return $dados;
		}
	} //fim analitico

	/**
	 * Metodo para trazer o resultado para a montagem do relatorio sitético com gráfico
	 * 
	 */
	public function baixa_exames_sintetico($agrupamento, $conditions)
	{
		//metodo que prepara a query
		$query_analitica = $this->baixa_exames_analitico('sql', compact('conditions'));

		//verifica qual agrupamento
		switch ($agrupamento) {
			case self::AGRP_UNIDADE: //agrupar por unidade
				$fields = array(
					'unidade_codigo AS codigo',
					'unidade_nome_fantasia AS descricao',
					'COUNT(exame_codigo) AS quantidade',
				);
				$group = array(
					'unidade_codigo',
					'unidade_nome_fantasia',
				);
				break;
			case self::AGRP_SETOR: //agrupado por setor
				$fields = array(
					'setor_codigo AS codigo',
					'setor_descricao AS descricao',
					'COUNT(exame_codigo) AS quantidade',
				);
				$group = array(
					'setor_codigo',
					'setor_descricao',
				);
				break;
			case self::AGRP_CARGO: //agrupado pelo cargo
				$fields = array(
					'cargo_codigo AS codigo',
					'cargo_descricao AS descricao',
					'COUNT(exame_codigo) AS quantidade',
				);
				$group = array(
					'cargo_codigo',
					'cargo_descricao',
				);
				break;
			case self::AGRP_TIPO_EXAME: //tipo de exame
				$fields = array(
					'tipo_exame_codigo as codigo',
					'tipo_exame AS descricao',
					'COUNT(exame_codigo) AS quantidade',
				);
				$group = array(
					'tipo_exame_codigo',
					'tipo_exame',
				);
				break;
			case self::AGRP_TIPO_RESULTADO: //tipo de resultado
				$fields = array(
					'unidade_codigo AS codigo',
					'unidade_nome_fantasia AS descricao',
					'COUNT(exame_codigo) AS quantidade',
				);
				$group = array(
					'unidade_codigo',
					'unidade_nome_fantasia',
				);

				break;
		}

		$order = array('COUNT(exame_codigo) DESC');
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

		// print $query;

		return $this->query($query);
	} //fim sintetico

	/**
	 * Metodo para trazer o resultado para a montagem do relatorio sitético com gráfico
	 * 
	 */
	public function baixa_exames_sintetico2($agrupamento, $conditions)
	{
		//metodo que prepara a query
		$query_analitica = $this->baixa_exames_analitico('sql', compact('conditions'));

		//verifica qual agrupamento
		switch ($agrupamento) {
			case self::AGRP_UNIDADE: //agrupar por unidade
				$fields = array(
					'unidade_codigo AS codigo',
					'unidade_nome_fantasia AS descricao',
					'COUNT(exame_codigo) AS quantidade',
				);
				$group = array(
					'unidade_codigo',
					'unidade_nome_fantasia',
				);
				break;
			case self::AGRP_SETOR: //agrupado por setor
				$fields = array(
					'setor_codigo AS codigo',
					'setor_descricao AS descricao',
					'COUNT(exame_codigo) AS quantidade',
				);
				$group = array(
					'setor_codigo',
					'setor_descricao',
				);
				break;
			case self::AGRP_CARGO: //agrupado pelo cargo
				$fields = array(
					'cargo_codigo AS codigo',
					'cargo_descricao AS descricao',
					'COUNT(exame_codigo) AS quantidade',
				);
				$group = array(
					'cargo_codigo',
					'cargo_descricao',
				);
				break;
			case self::AGRP_TIPO_EXAME: //tipo de exame
				$fields = array(
					'tipo_exame_codigo as codigo',
					'tipo_exame AS descricao',
					'COUNT(exame_codigo) AS quantidade',
				);
				$group = array(
					'tipo_exame_codigo',
					'tipo_exame',
				);
				break;
			case self::AGRP_TIPO_RESULTADO: //tipo de resultado
				$fields = array(
					'tipo_resultado_codigo as codigo',
					'tipo_resultado AS descricao',
					'COUNT(tipo_resultado) AS quantidade',
				);
				$group = array(
					'tipo_resultado_codigo',
					'tipo_resultado',
				);
				break;
		}

		$order = array('COUNT(exame_codigo) DESC');
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

		return $this->query($query);
	} //fim sintetico

	/**
	 * Pega o tipo de agrupamento
	 */
	public function tiposAgrupamento()
	{

		return array(
			self::AGRP_UNIDADE => "por Unidade",
			self::AGRP_SETOR => "por Setor",
			self::AGRP_CARGO => "por Cargo",
			self::AGRP_TIPO_EXAME => "por Tipo Exame"
		);
	}

	/**
	 * Pega o tipo de agrupamento
	 */
	public function tiposAgrupamentoResultadoExames()
	{

		return array(
			self::AGRP_UNIDADE => "por Unidade",
			self::AGRP_SETOR => "por Setor",
			self::AGRP_CARGO => "por Cargo",
			self::AGRP_TIPO_EXAME => "por Tipo Exame",
			self::AGRP_TIPO_RESULTADO => "por Tipo Resultado"
		);
	}

	/**
	 * Metodo identifica qual status do pedido por : QTD_BAIXAS por QTD_EXAMES
	 */
	public function statusBaixasExames($PedidoExame_codigo)
	{

		$ItemPedidoExame = &ClassRegistry::init('ItemPedidoExame');
		$ItemPedidoExameBaixa = &ClassRegistry::init('ItemPedidoExameBaixa');
		$StatusPedidoExame = &ClassRegistry::init('StatusPedidoExame');

		$qtd_itens =  $ItemPedidoExame->find('count', array('conditions' => array('codigo_pedidos_exames' => $PedidoExame_codigo)));

		$conditions = array('ItemPedidoExame.codigo_pedidos_exames' => $PedidoExame_codigo);

		$joins  = array(
			array(
				'table' => $ItemPedidoExameBaixa->databaseTable . '.' . $ItemPedidoExameBaixa->tableSchema . '.' . $ItemPedidoExameBaixa->useTable,
				'alias' => 'ItemPedidoExameBaixa',
				'type' => 'RIGHT',
				'conditions' => 'ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo'
			),
		);
		$qtd_baixado = $ItemPedidoExame->find('count', array('conditions' => $conditions, 'joins' => $joins));

		// Tem algum baixado
		if ($qtd_baixado) {
			// Diferente
			if ($qtd_baixado != $qtd_itens) return $StatusPedidoExame::PARCIALMENTE_BAIXADO;
			// Igual
			if ($qtd_baixado == $qtd_itens) return $StatusPedidoExame::TOTALMENTE_BAIXADO;
		} else {
			// Se não
			return $StatusPedidoExame::PENDENTE_BAIXA;
		}
	}

	/**
	 * Seleciona todos os itens_pedidos_exames_baixa realizados dentro do horario especificado.
	 * Verifica se todos os itens_pedidos_exames do pedidos_exames estão com suas respectivas baixas.
	 * @param int hora | int codigo_grupo_economico
	 * @return cod_pedido | cod_funcionario | qtd_itens_exames_baixa | qtd_itens_exames_baixa | data
	 */
	public function pedidos_exportacao_nexo($hora, $codigo_grupo_economico = null, $codigo_pedido_exame = null)
	{


		$where = '(
						pedidos_exames_nexo.data BETWEEN 
							DATEADD(hour,-' . $hora . ', GETDATE()) AND GETDATE()
					)';

		if (!is_null($codigo_pedido_exame)) {
			$where = "pedidos_exames_nexo.cod_pedido = " . $codigo_pedido_exame;
		}

		$query = 'SELECT * FROM (
					SELECT 
						pe.codigo_cliente as cod_cliente,
						pe.codigo AS cod_pedido,
						pe.codigo_funcionario AS cod_funcionario,
						COUNT(ipe.codigo) AS qtd_itens_exames,
						COUNT(ipeb.codigo) AS qtd_itens_exames_baixa,
						MAX(ipeb.data_inclusao) AS data
					FROM pedidos_exames pe
					INNER JOIN itens_pedidos_exames ipe ON ipe.codigo_pedidos_exames = pe.codigo
					LEFT JOIN itens_pedidos_exames_baixa ipeb ON ipeb.codigo_itens_pedidos_exames = ipe.codigo						
					WHERE 
						pe.codigo_cliente IN (select codigo_cliente from grupos_economicos_clientes where codigo_grupo_economico IN (' . $codigo_grupo_economico . '))
						AND ipeb.integracao_cliente = 0
					GROUP BY pe.codigo, pe.codigo_funcionario, ipe.codigo_fornecedor,pe.codigo_cliente
				) AS pedidos_exames_nexo 
				WHERE 
					pedidos_exames_nexo.data IS NOT NULL AND 
					pedidos_exames_nexo.qtd_itens_exames = pedidos_exames_nexo.qtd_itens_exames_baixa 
					AND ' . $where . '
					';
		// DATEADD(hour,-'.$hora.', GETDATE()) AND GETDATE()
		// print $query."\n";exit;
		return $this->query($query);
	}

	/**
	 * Seleciona os dados da Clinica pelo codigo do pedidos_exames.
	 * Utilizado na integração nexo.
	 * @param int codigo_pedido
	 * @return CodigoClinica | NomeClinica | Endereco | Cidade | UF | EnderecoBairro
	 */
	public function busca_clinica_por_pedido_exame_nexo($codigo_pedido)
	{
		$itemPedidoExame = ClassRegistry::init('ItemPedidoExame');
		$fornecedor = ClassRegistry::init('Fornecedor');
		$fornecedorEndereco = ClassRegistry::init('FornecedorEndereco');
		$endereco = ClassRegistry::init('Endereco');
		$enderecoCidade = ClassRegistry::init('EnderecoCidade');
		$enderecoEstado = ClassRegistry::init('EnderecoEstado');
		$enderecoBairro = ClassRegistry::init('EnderecoBairro');

		$joins  = array(
			array(
				'table' => "{$itemPedidoExame->databaseTable}.{$itemPedidoExame->tableSchema}.{$itemPedidoExame->useTable}",
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo'
			),
			array(
				'table' => "{$fornecedor->databaseTable}.{$fornecedor->tableSchema}.{$fornecedor->useTable}",
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => 'Fornecedor.codigo = ItemPedidoExame.codigo_fornecedor'
			),
			array(
				'table' => "{$fornecedorEndereco->databaseTable}.{$fornecedorEndereco->tableSchema}.{$fornecedorEndereco->useTable}",
				'alias' => 'FornecedorEndereco',
				'type' => 'LEFT',
				'conditions' => 'FornecedorEndereco.codigo_fornecedor = Fornecedor.codigo'
			)
		);

		$Configuracao = &ClassRegistry::init('Configuracao');
		$fields = array(
			'DISTINCT Fornecedor.codigo AS CodigoClinica',
			'Fornecedor.nome AS NomeClinica',
			'FornecedorEndereco.logradouro AS Endereco',
			'FornecedorEndereco.numero AS Numero',
			'FornecedorEndereco.complemento AS Complemento',
			'FornecedorEndereco.cidade AS Cidade',
			'FornecedorEndereco.estado_descricao AS UF',
			'FornecedorEndereco.bairro AS Bairro',
			'(SELECT TOP 1 ddd FROM RHHealth.dbo.fornecedores_contato AS forc WHERE forc.codigo_fornecedor=Fornecedor.codigo AND codigo_tipo_retorno= 1) AS DDD1',
			'(SELECT top 1 descricao FROM fornecedores_contato AS forc WHERE forc.codigo_fornecedor=[Fornecedor].codigo AND codigo_tipo_retorno= 1 ) AS Tel1',
			'(SELECT top 1 ddd FROM fornecedores_contato AS forc WHERE forc.codigo_fornecedor=[Fornecedor].codigo AND codigo_tipo_retorno= 7 ) AS DDD2',
			'(SELECT top 1 descricao FROM fornecedores_contato AS forc WHERE forc.codigo_fornecedor=[Fornecedor].codigo AND codigo_tipo_retorno= 7 ) AS Tel2',
			'FornecedorEndereco.cep AS CEP',
			'Fornecedor.codigo_documento AS CGC',
			'(SELECT top 1 nome FROM fornecedores_contato AS forc WHERE forc.codigo_fornecedor=[Fornecedor].codigo AND codigo_tipo_retorno= 1 ) AS NomeContato',
			'null AS ClinicaExterna',
			'null AS NumeroContrato',
			'(SELECT count(e.codigo) AS total
				FROM listas_de_preco lp 
					INNER JOIN listas_de_preco_produto lpp on lp.codigo = lpp.codigo_lista_de_preco AND lpp.codigo_produto = 59
					INNER JOIN listas_de_preco_produto_servico lpps on lpp.codigo = lpps.codigo_lista_de_preco_produto
					INNER JOIN exames e on lpps.codigo_servico = e.codigo_servico
				WHERE lp.codigo_fornecedor = ItemPedidoExame.codigo_fornecedor AND e.codigo = '. $Configuracao->getChave('INSERE_EXAME_CLINICO').') AS fazAso',
			'Fornecedor.ativo AS Ativo',
			'null AS MotivoDesativa'
		);

		$conditions = array('PedidoExame.codigo' => $codigo_pedido);
		return $this->find('all', array('fields' => $fields, 'joins' => $joins, 'conditions' => array('PedidoExame.codigo' => $codigo_pedido)));
	}

	/**
	 * Seleciona todos os medicos de acordo com o codigo_pedido
	 * Utilizado na integração nexo.
	 * @param int codigo_pedido
	 * @return CodigoProfissional | NomeProfissional | Funcao | Ativo
	 */
	public function busca_medico_pedido_exame_nexo($codigo_pedido)
	{
		$fichaClinica = ClassRegistry::init('FichaClinica');
		$medico = ClassRegistry::init('Medico');
		$conselhoProfissional = ClassRegistry::init('ConselhoProfissional');

		$joins  = array(
			array(
				'table' => "{$fichaClinica->databaseTable}.{$fichaClinica->tableSchema}.{$fichaClinica->useTable}",
				'alias' => 'FichaClinica',
				'type' => 'INNER',
				'conditions' => 'FichaClinica.codigo_pedido_exame = PedidoExame.codigo',
			),
			array(
				'table' => "{$medico->databaseTable}.{$medico->tableSchema}.{$medico->useTable}",
				'alias' => 'Medico',
				'type' => 'INNER',
				'conditions' => 'Medico.codigo = FichaClinica.codigo_medico',
			),
			array(
				'table' => "{$conselhoProfissional->databaseTable}.{$conselhoProfissional->tableSchema}.{$conselhoProfissional->useTable}",
				'alias' => 'ConselhoProfissional',
				'type' => 'INNER',
				'conditions' => 'Medico.codigo_conselho_profissional = ConselhoProfissional.codigo',
			)
		);

		$fields = array(
			'Medico.codigo AS CodigoProfissional',
			'Medico.nome AS NomeProfissional',
			//'ConselhoProfissional.descricao AS CRM',
			"CONCAT(Medico.numero_conselho,'-',Medico.conselho_uf) AS CRM",
			//'Medico.numero_conselho AS NumeroRegistroProfissional',
			'Medico.especialidade AS Funcao',
			"'M' AS TipoProfissional",
			'Medico.ativo AS Ativo'
		);

		$conditions = array('PedidoExame.codigo' => $codigo_pedido);

		// echo $this->find('sql', array('fields' => $fields,'joins' => $joins, 'conditions' => array('PedidoExame.codigo' => $codigo_pedido)));

		return $this->find('all', array('fields' => $fields, 'joins' => $joins, 'conditions' => array('PedidoExame.codigo' => $codigo_pedido)));
	}

	/**
	 * Seleciona todos itens_pedidos_exames de acordo com o codigo_pedido.
	 * Utilizado na integração nexo. 
	 * @param int codigo_pedido
	 * @return CodigoEmpresa | CodigoFuncionario | DataPedido | CodigoClinicaRealizadoExame | CodigoExameAmbulatorial | CodigoTipoExame | Validade | Resultado | Observacao | DataRealizacaoExame | CodigoMedico
	 */
	public function busca_itens_pedidos_exames_nexo($codigo_pedido, $codigos_clientes)
	{
		$itemPedidoExame = ClassRegistry::init('ItemPedidoExame');
		$itemPedidoExameBaixa = ClassRegistry::init('ItemPedidoExameBaixa');
		$fichaClinica = ClassRegistry::init('FichaClinica');
		$funcionario = ClassRegistry::init('Funcionario');
		$clienteFuncionario = ClassRegistry::init('ClienteFuncionario');
		$cliente = ClassRegistry::init('Cliente');
		$clienteExterno = ClassRegistry::init('ClienteExterno');
		$examesExterno = ClassRegistry::init('ExameExterno');
		$funcionarioSetorCargo = ClassRegistry::init('FuncionarioSetorCargo');
		$aplicacaoExames = ClassRegistry::init('AplicacaoExame');
		$Configuracao = &ClassRegistry::init('Configuracao');
		$joins  = array(
			array(
				'table' => "{$itemPedidoExame->databaseTable}.{$itemPedidoExame->tableSchema}.{$itemPedidoExame->useTable}",
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo'
			),
			array(
				'table' => "{$itemPedidoExameBaixa->databaseTable}.{$itemPedidoExameBaixa->tableSchema}.{$itemPedidoExameBaixa->useTable}",
				'alias' => 'ItemPedidoExameBaixa',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo
	            	 AND ItemPedidoExameBaixa.integracao_cliente = 0'
				// 'conditions' => 'ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo'
			),
			array(
				'table' => "{$funcionario->databaseTable}.{$funcionario->tableSchema}.{$funcionario->useTable}",
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = PedidoExame.codigo_funcionario'
			),
			array(
				'table' => "{$clienteFuncionario->databaseTable}.{$clienteFuncionario->tableSchema}.{$clienteFuncionario->useTable}",
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario'
			),
			array(
				'table' => "{$cliente->databaseTable}.{$cliente->tableSchema}.{$cliente->useTable}",
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente_matricula AND Cliente.codigo IN (' . $codigos_clientes . ')'
				// 'conditions' => 'Cliente.codigo = PedidoExame.codigo_cliente AND Cliente.codigo IN (10011)'
			),
			array(
				'table' => "{$clienteExterno->databaseTable}.{$clienteExterno->tableSchema}.{$clienteExterno->useTable}",
				'alias' => 'ClienteExterno',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = ClienteExterno.codigo_cliente'
			),
			array(
				'table' => "{$funcionarioSetorCargo->databaseTable}.{$funcionarioSetorCargo->tableSchema}.{$funcionarioSetorCargo->useTable}",
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => 'FuncionarioSetorCargo.codigo = PedidoExame.codigo_func_setor_cargo'
			),
			array(
				'table' => "{$aplicacaoExames->databaseTable}.{$aplicacaoExames->tableSchema}.{$aplicacaoExames->useTable}",
				'alias' => 'AplicacaoExames',
				'type' => 'INNER',
				'conditions' => 'FuncionarioSetorCargo.codigo_cargo = AplicacaoExames.codigo_cargo 
	and AplicacaoExames.codigo_setor = [FuncionarioSetorCargo].codigo_setor and PedidoExame.codigo_cliente = AplicacaoExames.codigo_cliente and AplicacaoExames.codigo_exame <> '. $Configuracao->getChave('INSERE_EXAME_CLINICO').' AND AplicacaoExames.codigo_exame = ItemPedidoExame.codigo_exame'
			)
		);

		$fields = array(
			'ClienteExterno.codigo_externo AS CodigoEmpresa',
			"(CASE 
            	WHEN ClienteFuncionario.matricula_candidato = 1 THEN CONCAT('PRE-I',ClienteFuncionario.matricula)
            	ELSE ClienteFuncionario.matricula END) AS CodigoFuncionario",
			'PedidoExame.data_solicitacao AS DataPedido',
			'ItemPedidoExame.codigo_exame AS CodigoExameAmbulatorial',
			//ajuste solicitado pela duda 11/01/2022, pois estava no else alterado
			"(CASE WHEN ItemPedidoExameBaixa.resultado = 1 THEN '1' ELSE (CASE WHEN ItemPedidoExameBaixa.resultado = 2 THEN '2' ELSE '' END)  END) AS CodigoParecer",
			'ItemPedidoExame.codigo_fornecedor AS CodigoClinicaRealizadoExame',
			// "(CASE 
			// 	WHEN PedidoExame.exame_admissional = 1 THEN 'admissional'
			// 	WHEN PedidoExame.exame_demissional = 1 THEN 'demissional'
			// 	WHEN PedidoExame.exame_mudanca = 1 THEN 'mudanca'
			// 	WHEN PedidoExame.exame_periodico = 1 THEN 'periodico'
			// 	WHEN PedidoExame.exame_retorno = 1 THEN 'retorno'
			// END) AS CodigoTipoExame",
			"(CASE 
				WHEN PedidoExame.exame_admissional = 1 THEN '0'
				WHEN PedidoExame.exame_demissional = 1 THEN '9'
				WHEN PedidoExame.exame_mudanca = 1 THEN '3'
				WHEN PedidoExame.exame_periodico = 1 THEN '1'
				WHEN PedidoExame.exame_retorno = 1 THEN '2'
				WHEN PedidoExame.exame_monitoracao = 1 THEN '5'
			END) AS CodigoTipoExame",
			"(CASE
				WHEN ((PedidoExame.exame_admissional = 1 OR PedidoExame.codigo IS NULL) AND
					    (AplicacaoExames.periodo_apos_demissao IS NOT NULL AND
					      AplicacaoExames.periodo_apos_demissao <> '')) THEN AplicacaoExames.periodo_apos_demissao
				ELSE (CASE
						WHEN ((AplicacaoExames.periodo_idade IS NOT NULL AND AplicacaoExames.periodo_idade <> '') 
							AND ((DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) >= AplicacaoExames.periodo_idade 
							AND DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) < AplicacaoExames.periodo_idade_2) 
								OR (AplicacaoExames.periodo_idade_2 = ''))) THEN AplicacaoExames.qtd_periodo_idade
						WHEN ((AplicacaoExames.periodo_idade_2 IS NOT NULL AND AplicacaoExames.periodo_idade_2 <> '') 
							AND ((DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) >= AplicacaoExames.periodo_idade_2 
							AND DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) < AplicacaoExames.periodo_idade_3) 
								OR (AplicacaoExames.periodo_idade_3 = ''))) THEN AplicacaoExames.qtd_periodo_idade_2
						WHEN ((AplicacaoExames.periodo_idade_3 IS NOT NULL AND AplicacaoExames.periodo_idade_3 <> '') 
							AND ((DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) >= AplicacaoExames.periodo_idade_3 
							AND	DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) < AplicacaoExames.periodo_idade_4) 
								OR (AplicacaoExames.periodo_idade_4 = ''))) THEN AplicacaoExames.qtd_periodo_idade_3
						WHEN ((AplicacaoExames.periodo_idade_4 IS NOT NULL 
							AND AplicacaoExames.periodo_idade_4 <> '') 
							AND DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) >= AplicacaoExames.periodo_idade_4) THEN AplicacaoExames.qtd_periodo_idade_4
						ELSE AplicacaoExames.periodo_meses
						END)
				END) AS Validade",
			//ajuste solicitado pela duda 11/01/2022, pois estava no else alterado
			"(CASE WHEN ItemPedidoExameBaixa.resultado = 1 THEN 'normal' ELSE (CASE WHEN ItemPedidoExameBaixa.resultado = 2 THEN 'alterado' ELSE '' END)  END) AS Resultado",
			'ItemPedidoExameBaixa.descricao AS Observacao',
			'ItemPedidoExameBaixa.data_realizacao_exame AS DataRealizacaoExame',
			'ItemPedidoExameBaixa.resultado AS ResultadoNormal',
			'"" AS CodigoMedico',
			'"" AS CodigoMedicoParecer',
			'ItemPedidoExameBaixa.codigo AS codigo_ipeb'
		);

		$conditions = array('PedidoExame.codigo' => $codigo_pedido, 'ItemPedidoExame.codigo_exame !=' => $Configuracao->getChave('INSERE_EXAME_CLINICO'));

		return $this->find('all', array('fields' => $fields, 'joins' => $joins, 'conditions' => $conditions));
	} // fim busca_itens_pedidos_exames_nexo

	/**
	 * Seleciona os ASO de acordo com o codigo pedido exame
	 * Utilizado na integração nexo
	 * @param int codigo_pedido
	 * @return ItemPedidoExame | ItemPedidoExameBaixa | FichaClinica | Funcionario | FuncionarioSetorCargo
	 */
	public function busca_aso_pedido_exame($codigo_pedido, $codigos_clientes)
	{
		$itemPedidoExame = ClassRegistry::init('ItemPedidoExame');
		$itemPedidoExameBaixa = ClassRegistry::init('ItemPedidoExameBaixa');
		$fichaClinica = ClassRegistry::init('FichaClinica');
		$funcionario = ClassRegistry::init('Funcionario');
		$funcionarioSetorCargo = ClassRegistry::init('FuncionarioSetorCargo');
		$clienteFuncionario = ClassRegistry::init('ClienteFuncionario');
		$cliente = ClassRegistry::init('Cliente');
		$clienteExterno = ClassRegistry::init('ClienteExterno');
		$setoresExterno = ClassRegistry::init('SetorExterno');
		$examesExterno = ClassRegistry::init('ExameExterno');
		$aplicacaoExames = ClassRegistry::init('AplicacaoExame');
		$Configuracao = &ClassRegistry::init('Configuracao');

		$joins  = array(
			array(
				'table' => "{$itemPedidoExame->databaseTable}.{$itemPedidoExame->tableSchema}.{$itemPedidoExame->useTable}",
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
			),
			array(
				'table' => "{$itemPedidoExameBaixa->databaseTable}.{$itemPedidoExameBaixa->tableSchema}.{$itemPedidoExameBaixa->useTable}",
				'alias' => 'ItemPedidoExameBaixa',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo AND ItemPedidoExameBaixa.integracao_cliente = 0',
				// 'conditions' => 'ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo',
			),
			array(
				'table' => "{$fichaClinica->databaseTable}.{$fichaClinica->tableSchema}.{$fichaClinica->useTable}",
				'alias' => 'FichaClinica',
				'type' => 'INNER',
				'conditions' => 'FichaClinica.codigo_pedido_exame = PedidoExame.codigo',
			),
			array(
				'table' => "{$funcionario->databaseTable}.{$funcionario->tableSchema}.{$funcionario->useTable}",
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = PedidoExame.codigo_funcionario',
			),
			array(
				'table' => "{$funcionarioSetorCargo->databaseTable}.{$funcionarioSetorCargo->tableSchema}.{$funcionarioSetorCargo->useTable}",
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => 'FuncionarioSetorCargo.codigo = PedidoExame.codigo_func_setor_cargo',
			),
			array(
				'table' => "{$setoresExterno->databaseTable}.{$setoresExterno->tableSchema}.{$setoresExterno->useTable}",
				'alias' => 'SetorExterno',
				'type' => 'INNER',
				'conditions' => 'FuncionarioSetorCargo.codigo_setor = SetorExterno.codigo_setor',
			),
			array(
				'table' => "{$clienteFuncionario->databaseTable}.{$clienteFuncionario->tableSchema}.{$clienteFuncionario->useTable}",
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
			),
			array(
				'table' => "{$cliente->databaseTable}.{$cliente->tableSchema}.{$cliente->useTable}",
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo_cliente_matricula = Cliente.codigo AND Cliente.codigo IN (' . $codigos_clientes . ')',
				// 'conditions' => 'ClienteFuncionario.codigo_cliente = Cliente.codigo AND Cliente.codigo IN (10011)',

			),
			array(
				'table' => "{$clienteExterno->databaseTable}.{$clienteExterno->tableSchema}.{$clienteExterno->useTable}",
				'alias' => 'ClienteExterno',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = ClienteExterno.codigo_cliente',
			),
			array(
				'table' => "{$aplicacaoExames->databaseTable}.{$aplicacaoExames->tableSchema}.{$aplicacaoExames->useTable}",
				'alias' => 'AplicacaoExame',
				'type' => 'INNER',
				'conditions' => 'AplicacaoExame.codigo_cargo = FuncionarioSetorCargo.codigo_cargo AND AplicacaoExame.codigo_setor = FuncionarioSetorCargo.codigo_setor AND PedidoExame.codigo_cliente = AplicacaoExame.codigo_cliente AND AplicacaoExame.codigo_exame = '.$Configuracao->getChave('INSERE_EXAME_CLINICO').' AND AplicacaoExame.codigo_exame = ItemPedidoExame.codigo_exame AND AplicacaoExame.codigo_funcionario = Funcionario.codigo',
			)
		);

		$fields = array(
			'CLienteExterno.codigo_externo AS CodigoEmpresa',
			"(CASE 
            	WHEN ClienteFuncionario.matricula_candidato = 1 THEN CONCAT('PRE-I',ClienteFuncionario.matricula)
            	ELSE ClienteFuncionario.matricula END) AS CodigoFuncionario",
			"(CASE
				WHEN ((PedidoExame.exame_admissional = 1 OR PedidoExame.codigo IS NULL) AND
					    (AplicacaoExame.periodo_apos_demissao IS NOT NULL AND
					      AplicacaoExame.periodo_apos_demissao <> '')) THEN AplicacaoExame.periodo_apos_demissao
				ELSE (CASE
						WHEN ((AplicacaoExame.periodo_idade IS NOT NULL AND AplicacaoExame.periodo_idade <> '') 
							AND ((DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) >= AplicacaoExame.periodo_idade 
							AND DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) < AplicacaoExame.periodo_idade_2) 
								OR (AplicacaoExame.periodo_idade_2 = ''))) THEN AplicacaoExame.qtd_periodo_idade
						WHEN ((AplicacaoExame.periodo_idade_2 IS NOT NULL AND AplicacaoExame.periodo_idade_2 <> '') 
							AND ((DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) >= AplicacaoExame.periodo_idade_2 
							AND DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) < AplicacaoExame.periodo_idade_3) 
								OR (AplicacaoExame.periodo_idade_3 = ''))) THEN AplicacaoExame.qtd_periodo_idade_2
						WHEN ((AplicacaoExame.periodo_idade_3 IS NOT NULL AND AplicacaoExame.periodo_idade_3 <> '') 
							AND ((DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) >= AplicacaoExame.periodo_idade_3 
							AND	DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) < AplicacaoExame.periodo_idade_4) 
								OR (AplicacaoExame.periodo_idade_4 = ''))) THEN AplicacaoExame.qtd_periodo_idade_3
						WHEN ((AplicacaoExame.periodo_idade_4 IS NOT NULL 
							AND AplicacaoExame.periodo_idade_4 <> '') 
							AND DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) >= AplicacaoExame.periodo_idade_4) THEN AplicacaoExame.qtd_periodo_idade_4
						ELSE AplicacaoExame.periodo_meses
						END)
				END) AS Validade",
			'ItemPedidoExameBaixa.data_realizacao_exame AS DataUltimoAso',
			'FichaClinica.parecer AS Apto',
			'ItemPedidoExame.codigo_exame AS CodigoExame',
			'ItemPedidoExame.codigo_fornecedor AS CodigoClinica',
			// 'ItemPedidoExame.codigo_tipos_exames_pedidos AS CodigoTipoExame',
			"(CASE 
				WHEN PedidoExame.exame_admissional = 1 THEN '0'
				WHEN PedidoExame.exame_demissional = 1 THEN '9'
				WHEN PedidoExame.exame_mudanca = 1 THEN '3'
				WHEN PedidoExame.exame_periodico = 1 THEN '1'
				WHEN PedidoExame.exame_retorno = 1 THEN '2'
				WHEN PedidoExame.exame_monitoracao = 1 THEN '5'
			END) AS CodigoTipoExame",

			'Funcionario.nome AS NomeFuncionario',
			'FichaClinica.codigo_medico AS CodigoMedico',
			'ItemPedidoExameBaixa.descricao AS Observacao',
			'SetorExterno.codigo_externo AS Setor',
			'ItemPedidoExameBaixa.codigo as codigo_ipeb'
		);

		// ItemPedidoExame.codigo_exame 52, CODIGO EXAME ASO, tabela exames
		$conditions = array(
			'ItemPedidoExame.codigo_exame' =>  $Configuracao->getChave('INSERE_EXAME_CLINICO'),
			// 'PedidoExame.exame_admissional' => 1
			'PedidoExame.codigo' => $codigo_pedido
		);

		// pr($this->find('sql', array('fields' => $fields,'joins' => $joins,'conditions' => $conditions)));exit;

		return $this->find('all', array(
			'fields' => $fields,
			'joins' => $joins,
			'conditions' => $conditions
		));
	} //fim busca_aso_pedido_exame


	/**
	 * [enviaEmailsESocial description]
	 * 
	 * metodo para quais emails será enviado o alerta de esocial
	 * 
	 * @return [type] [description]
	 */
	public function enviaEmailsESocial($codigo_pedido_exame, $tab_id = "s2220", $template = "email_esocial_s2220")
	{

		$this->ItemPedidoExameBaixa = ClassRegistry::Init('ItemPedidoExameBaixa');
		$Configuracao = &ClassRegistry::init('Configuracao');
		//relacionamentos
		$joins = array(
			array(
				'table' => 'itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo = ItemPedidoExameBaixa.codigo_itens_pedidos_exames',
			),
			array(
				'table' => 'pedidos_exames',
				'alias' => 'PedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
			),
		);

		$fields = array(
			'PedidoExame.codigo_cliente',
			'ItemPedidoExame.codigo_exame'
		);

		//pega se existe o tipo contato e-social no cliente
		$itens = $this->ItemPedidoExameBaixa->find('all', array('fields' => $fields, 'joins' => $joins, 'conditions' => array('ItemPedidoExame.codigo_pedidos_exames' => $codigo_pedido_exame, 'PedidoExame.codigo_status_pedidos_exames' => 3)));

		//verifica se o pedido foi baixado por completo
		if (!empty($itens)) {

			//varre os itens
			foreach ($itens as $dados) {
				$Configuracao = &ClassRegistry::init('Configuracao');
				// verifica se é aso que esta sendo baixado para mandar os emails da tabela s-2220
				if ($dados['ItemPedidoExame']['codigo_exame'] == $Configuracao->getChave('INSERE_EXAME_CLINICO')) {

					//envia email para o cliente que tiver contato esocial
					$this->enviaEmailClienteESocial($dados['PedidoExame']['codigo_cliente'], $template);

					//envia alerta para usuario do sistema que tiver habilitado o s2220 para receber alerta
					$this->alerta_esocial($dados['PedidoExame']['codigo_cliente'], $tab_id, $template);
				} //fim validacao


			} //fim foreach
		}
	} //fim enviaEmailsESocial

	/**
	 * [alerta_esocial_2220 description]
	 * 
	 * metodo para gerar alerta quando baixar o exame
	 * 
	 * @param  [type] $codigo_usuario [description]
	 * @return [type]                 [description]
	 */
	public function alerta_esocial($codigo_cliente, $tab_id = "s2220", $template = "email_esocial_s2220")
	{

		//faz a busca na tabela Usuarios_tipos_alertas e verifica se consta os codigos_alerta_tipo 41 e 42 para emissao do alerta
		$codigos_alertas = array();
		if ($tab_id == 's2220')
			$codigos_alertas = array('35', '36');
		if ($tab_id == 's2221')
			$codigos_alertas = array('1035', '1036');
		if ($tab_id == 's2210')
			$codigos_alertas = array('47', '48');

		//para enviar o email
		$this->Alerta = ClassRegistry::Init('Alerta');
		App::import('Component', array('StringView', 'Mailer.Scheduler'));
		$this->stringView = new StringViewComponent();
		//busca a ctp do email
		$content = $this->stringView->renderMail($template);

		foreach ($codigos_alertas as $codigo_alerta_tipo) {

			$alerta = array( //inicio array                
				'Alerta' => array(
					'assunto'            => "E-social Tabela " . strtoupper($tab_id),
					'codigo_cliente'     => $codigo_cliente,
					'descricao'          => "E-social Tabela " . strtoupper($tab_id),
					'data_inclusao'      => date('Y-m-d H:i:s'),
					'email_agendados'    => 0,
					'sms_agendados'      => 0,
					'descricao_email'    => $content,
					'codigo_alerta_tipo' => $codigo_alerta_tipo,
					'model'              => 'Alerta',
				)
			); //fim array

			//gera o alerta
			$this->Alerta->incluir($alerta);
		} //fim foreach       

	} //fim alerta email E-social

	/**
	 * [enviaEmailClienteESocial description]
	 * 
	 * metodo para validar e verificar se envia email para o cliente
	 * 
	 * @param  [type] $codigo_cliente [description]
	 * @return [type]                 [description]
	 */
	public function enviaEmailClienteESocial($codigo_cliente, $template = 'email_esocial_s2220')
	{
		$this->ClienteContato = ClassRegistry::Init('ClienteContato');

		//verifica se tem o contato esocial para disparar o email esta fixo 10 que é o tipo de contato
		$dados_contato = $this->ClienteContato->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente, 'codigo_tipo_contato' => 10)));

		//verifica os dados do contato
		if (!empty($dados_contato)) {

			if (!empty($dados_contato['ClienteContato']['descricao'])) {

				$MailerOutbox = &ClassRegistry::Init('MailerOutbox');

				//email para ser disparado
				$email = $dados_contato['ClienteContato']['descricao'];
				$assunto = "Lembrete falta de arquivos no E-social";

				$d = array();
				$MailerOutbox->enviaEmail($d, $assunto, $template, trim($email));
			}
		} //fim dados contatos

	} //fim enviaEmailClienteESocial

	public function getCodigoCliente($codigo_pedido_exame)
	{
		$codigo_cliente = $this->field('codigo_cliente', array('codigo' => $codigo_pedido_exame));
		if (empty($codigo_cliente) || is_null($codigo_cliente) || $codigo_cliente == 0)
			return null;
		return $codigo_cliente;
	}

	/**
	 * [get_grupo_risco_corona pega os dados formatados das pessoas que são do grupo de risco]
	 * @param  [type] $codigo_grupo_economico [codigo do grupo economico, assim pegando todas as undiades respectivas]
	 * @return [type]                         [description]
	 */
	public function get_grupo_risco_corona($codigo_grupo_economico)
	{

		//   	ini_set('max_execution_time', 0);
		// set_time_limit(0);

		$query = 'SELECT 
				    f.nome as nome_funcionario,
				    f.cpf as cpf,
				    cAlo.razao_social as alocacao_razao_social,
				    cAlo.nome_fantasia as alocacao_nome_fantasia,
				    s.descricao as setor,
				    ca.descricao as cargo,
				    f.data_nascimento,
				    FLOOR(DATEDIFF(DAY, f.data_nascimento, GETDATE()) / 365.25) as idade,
				    (CASE WHEN f.sexo = \'F\' THEN \'Feminino\' else \'Masculino\' END) as sexo,

				    fc.pa_diastolica,
				    fc.pa_sistolica,
				    fc.altura_mt,
				    fc.altura_cm,
				    fc.peso_kg,
				    fc.peso_gr,
				    fc.imc,
				    fc.data_inclusao as data_ficha,

				    (select case when resposta = \'1\' then \'Sim\' else \'Não\' end as resposta
				    from fichas_clinicas_respostas fcr 
				    where fcr.codigo_ficha_clinica = fc.codigo
				        and fcr.codigo_ficha_clinica_questao = 26) as hipertensao,

				    (select case when resposta = \'1\' then \'Sim\' else \'Não\' end as resposta
				    from fichas_clinicas_respostas fcr 
				    where fcr.codigo_ficha_clinica = fc.codigo
				        and fcr.codigo_ficha_clinica_questao = 31) as diabetico,

				    (select case when resposta = \'1\' then \'Sim\' else \'Não\' end as resposta
				    from fichas_clinicas_respostas fcr 
				    where fcr.codigo_ficha_clinica = fc.codigo
				        and fcr.codigo_ficha_clinica_questao = 49) as problema_respiratorio,

				    (select campo_livre
				    from fichas_clinicas_respostas fcr 
				    where fcr.codigo_ficha_clinica = fc.codigo
				        and fcr.codigo_ficha_clinica_questao = 49) as descricao_problema_respiratorio,

				    (select resposta
				    from fichas_clinicas_respostas fcr 
				    where fcr.codigo_ficha_clinica = fc.codigo
				        and fcr.codigo_ficha_clinica_questao = 169) as fumante,

				    CAST(
				        (SELECT fcr.campo_livre
				        FROM fichas_clinicas_respostas fcr
				        where fcr.codigo_ficha_clinica = fc.codigo
				            and fcr.campo_livre like \'%farmaco%\'
						FOR XML PATH(\'\')) AS text) AS medicamento,

				    fc.observacao as observacao
				from pedidos_exames pe
					inner join cliente_funcionario cf on cf.codigo = pe.codigo_cliente_funcionario and cf.ativo <> \'0\'
				    inner join fichas_clinicas fc on pe.codigo = fc.codigo_pedido_exame
				    inner join funcionarios f on pe.codigo_funcionario = f.codigo
				    inner join cliente c on pe.codigo_cliente = c.codigo
				    inner join funcionario_setores_cargos fsc on pe.codigo_func_setor_cargo = fsc.codigo
				    inner join setores s on fsc.codigo_setor = s.codigo
				    inner join cargos ca on fsc.codigo_cargo = ca.codigo
				    inner join cliente cAlo on fsc.codigo_cliente_alocacao = cAlo.codigo
				where pe.codigo_cliente IN (select codigo_cliente from grupos_economicos_clientes where codigo_grupo_economico = ' . $codigo_grupo_economico . ')';

		// debug($query);exit;
		// $dados = $this->query($query);
		// debug($dados);exit;

		//executa a querys
		return $query;
	} //fim get_grupo_risco_coronashow

	/**
	 * [atualziarDadosPedido description]
	 * 
	 * metodo para atualizar os dados de codigo_cliente_funcionario e codigo_cliente
	 * 
	 * usado principalmente na funcionalidade de separação de grupos economicos
	 * 
	 * @return [type] [description]
	 */
	public function atualizarDadosPedido($codigo_cliente, $codigo_cliente_funcionario, $codigo_func_setor_cargo)
	{
		//verifica se tem pedidos de exames para a funcao
		$pedidos_exames = $this->find('first', array('conditions' => array('codigo_func_setor_cargo' => $codigo_func_setor_cargo)));

		//verifica se tem pedidos de exames
		if (!empty($pedidos_exames)) {
			//atualiza os pedidos de exames
			//seta a query update
			$query = "UPDATE RHHealth.dbo.pedidos_exames 
				SET codigo_cliente_funcionario = " . $codigo_cliente_funcionario . ", 
					codigo_cliente = " . $codigo_cliente . " 
				WHERE codigo_func_setor_cargo = " . $codigo_func_setor_cargo . ";";

			$this->query($query);
		} //fim registros pedidos exames 

	} //fim atualizarDadosPedido

	public function relatorioFaturamento($filtros = array())
	{
		$where = '';
		$whereCliPagador = '';
		$group_by = "";

		if (isset($_SESSION['Auth']['Usuario']['codigo_empresa']) && $_SESSION['Auth']['Usuario']['codigo_empresa']) {
			$where .= " AND pe.codigo_empresa = " . $_SESSION['Auth']['Usuario']['codigo_empresa'];
		}

		if (!empty($filtros['codigo_cliente'])) {
			$where .= " AND cf.codigo_cliente = '{$filtros['codigo_cliente']}' ";
		}

		if (!empty($filtros['codigo_cliente_alocacao'])) {
			$where .= " AND pe.codigo_cliente = '{$filtros['codigo_cliente_alocacao']}' ";
		}

		if (!empty($filtros['codigo_pagador'])) {
			$whereCliPagador .= " (ISNULL(AlocacaoCPS.codigo_cliente_pagador,MatrizCPS.codigo_cliente_pagador) = '{$filtros['codigo_pagador']}') AND ";

			if (empty($filtros['codigo_cliente_alocacao'])) {
				$where .= "AND (cf.codigo_cliente IN (SELECT cp.codigo_cliente
				FROM cliente_produto_servico2 cps
					INNER JOIN cliente_produto cp ON cps.codigo_cliente_produto = cp.codigo
				WHERE cps.codigo_cliente_pagador = " . $filtros['codigo_pagador'] . "
				GROUP BY cp.codigo_cliente)
				OR
				fsc.codigo_cliente IN (SELECT cp.codigo_cliente
										FROM cliente_produto_servico2 cps
											INNER JOIN cliente_produto cp ON cps.codigo_cliente_produto = cp.codigo
										WHERE cps.codigo_cliente_pagador = " . $filtros['codigo_pagador'] . "
										GROUP BY cp.codigo_cliente)
				)";
			}
		}

		if (!empty($filtros['data_inicio'])) {
			$where .= " AND (ipeb.data_inclusao >= '{$filtros['data_inicio']} 00:00:00') ";
		}

		if (!empty($filtros['data_fim'])) {
			$where .= " AND (ipeb.data_inclusao <= '{$filtros['data_fim']} 23:59:59') ";
		}

		if ($filtros['exibe_prestadores_particular_ambulatorio'] == 0) {
			$where .= "AND f.ambulatorio = 0 AND f.prestador_particular = 0";
		}

		$sql = "
		WITH 

        ctePedidosExames AS (
            SELECT

                pe.codigo AS cod_pedido_exame,
                cli.codigo AS cod_cliente,
                fsc.codigo_cliente_alocacao AS codigo_cliente_alocacao,
                cf.codigo_cliente AS codigo_cliente_matriz,
                cli.nome_fantasia AS nome_unidade,
                cli.razao_social AS razao_social,
                RHHealth.publico.ufn_formata_cnpj(cli.codigo_documento) AS cnpj_unidade,
                ce.cidade AS cidade_unidade,
                ce.estado_abreviacao AS estado_unidade,
                
                func.nome AS nome_funcionario,
                s.descricao AS setor,
                c.descricao AS cargo,
                RHHealth.publico.ufn_formata_cpf(func.cpf) AS cpf,
                cf.matricula AS matricula,
                cf.centro_custo AS centro_de_custo,
                
                e.codigo_servico AS codigo_servico,
                e.descricao AS exame,
                (CASE WHEN (pe.exame_admissional = 1) THEN 'ADMISSIONAL'
                WHEN (pe.exame_periodico = 1) THEN 'PERIÓDICO'
                WHEN (pe.exame_demissional = 1) THEN 'DEMISSIONAL'
                WHEN (pe.exame_retorno = 1) THEN 'RETORNO AO TRABALHO'
                WHEN (pe.exame_mudanca = 1) THEN 'MUDANCA DE FUNCAO'
                WHEN (pe.exame_monitoracao = 1) THEN 'MONITORAÇÃO PONTUAL'
                WHEN (pe.pontual = 1) THEN 'PONTUAL'
                ELSE '' END) AS tipo_de_exame,
                f.nome AS nome_credenciado,
                fe.cidade AS cidade_credenciado,
                CASE
                WHEN fe.estado_abreviacao is null then fe.estado_descricao
                else fe.estado_abreviacao
                end AS estado_credenciado,
                
                (CASE WHEN ipe.respondido_lyn = 1 THEN 'SIM' ELSE 'NÃO' END) AS respondido_lyn,

                FORMAT(ipe.valor_custo, 'c', 'pt-br') AS valor_custo_exame,
                (CONVERT(VARCHAR, ipe.data_inclusao, 103)) AS data_emissao_pedido,
                (CONVERT(VARCHAR,ipeb.data_realizacao_exame, 103)) AS data_realizacao_do_exame,
                (CONVERT(VARCHAR,ipeb.data_inclusao, 103)) AS data_baixa_exame,
				f.prestador_particular,
				f.ambulatorio,
                --FORMAT((CASE WHEN f.ambulatorio = 1 THEN '0.00'
                --WHEN f.prestador_particular = 1 THEN '0.00'
                --ELSE ipe.valor END),'c','pt-br') AS valor_exame_a_cobrar,
                (CASE WHEN AnexoExame.codigo IS NOT NULL THEN 'SIM' ELSE 'NAO' END) AS imagem_digitalizada,
                (CASE WHEN afc.codigo IS NOT NULL THEN 'SIM' ELSE 'NAO' END) AS imagem_digitalizada_fc,
                (CASE WHEN AnexoExame.codigo IS NOT NULL AND afc.codigo IS NOT NULL THEN '2'  WHEN AnexoExame.codigo IS NOT NULL THEN '1'  WHEN afc.codigo IS NOT NULL THEN '1'  ELSE '0' END) AS total_de_imagens_digitalizada

            FROM pedidos_exames pe
                inner join itens_pedidos_exames ipe ON pe.codigo = ipe.codigo_pedidos_exames
                inner join itens_pedidos_exames_baixa ipeb on ipe.codigo = ipeb.codigo_itens_pedidos_exames
                
                inner join cliente cli on pe.codigo_cliente = cli.codigo -- alocacao
                inner join funcionario_setores_cargos fsc on pe.codigo_func_setor_cargo = fsc.codigo -- alocacao
                inner join setores s ON s.codigo = fsc.codigo_setor
                inner join cargos c ON c.codigo = fsc.codigo_cargo
                inner join cliente_funcionario cf on fsc.codigo_cliente_funcionario = cf.codigo and pe.codigo_cliente_funcionario = cf.codigo -- matricula (matriz)
                inner join fornecedores f on f.codigo = ipe.codigo_fornecedor
                inner join fornecedores_endereco fe on f.codigo = fe.codigo_fornecedor
                inner join exames e on e.codigo = ipe.codigo_exame
                inner join funcionarios func on pe.codigo_funcionario = func.codigo
                left join cliente_endereco ce on ce.codigo_cliente = cli.codigo
                
                left join fichas_clinicas FichaClinica on FichaClinica.codigo_pedido_exame = pe.codigo
                left join anexos_fichas_clinicas afc on FichaClinica.codigo = afc.codigo_ficha_clinica
                left join anexos_exames AnexoExame on ipe.codigo = AnexoExame.codigo_item_pedido_exame
            WHERE 1=1
                   {$where}
            GROUP BY
                pe.codigo,
                cli.codigo,
                fsc.codigo_cliente_alocacao,
                cf.codigo_cliente,
                cli.nome_fantasia,
                cli.razao_social,
                cli.codigo_documento,
                ce.cidade,
                ce.estado_abreviacao,
                func.nome,
                s.descricao,
                c.descricao,
                func.cpf,
                cf.matricula,
                cf.centro_custo,
                e.descricao,
                pe.exame_admissional,
                pe.exame_periodico,
                pe.exame_demissional,
                pe.exame_retorno,
                pe.exame_mudanca,
                pe.exame_monitoracao,
                pe.pontual,
                f.ambulatorio,
                f.prestador_particular,
                f.nome,
                fe.cidade,
                fe.estado_abreviacao,
                ipe.valor_custo,
                ipe.data_inclusao,
                ipeb.data_realizacao_exame,
                ipeb.data_inclusao,
                ipe.valor
                ,AnexoExame.codigo,
                afc.codigo,
                e.codigo_servico,
                fe.estado_descricao
                ,ipe.respondido_lyn


        )
        ,
        cteExamesComplementares AS (
            SELECT
                cpe.*,

                ISNULL(AlocacaoCPS.codigo_cliente_pagador,MatrizCPS.codigo_cliente_pagador) AS codigo_cliente_pagador,

                ISNULL(clientePagadorAlocacao.codigo,clientePagadorMatriz.codigo) AS cod_pagador,

                ISNULL(clientePagadorAlocacao.nome_fantasia,clientePagadorMatriz.nome_fantasia) AS nome_pagador,

                RHHealth.publico.ufn_formata_cnpj(ISNULL(clientePagadorAlocacao.codigo_documento,clientePagadorMatriz.codigo_documento)) AS cnpj_pagador,
                
                'EXAMES COMPLEMENTARES' AS forma_de_cobranca,

				FORMAT((CASE WHEN cpe.ambulatorio = 1 THEN '0.00'
                WHEN cpe.prestador_particular = 1 THEN '0.00'
                ELSE ISNULL(AlocacaoCPS.valor, MatrizCPS.valor) END),'c','pt-br') AS valor_exame_a_cobrar
                
            FROM ctePedidosExames cpe 
                
                left join cliente_produto AlocacaoCP on AlocacaoCP.codigo_cliente = cpe.codigo_cliente_alocacao and AlocacaoCP.codigo_produto = 59
                left join cliente_produto_servico2 AlocacaoCPS on AlocacaoCPS.codigo_cliente_produto = AlocacaoCP.codigo
                    and AlocacaoCPS.codigo_servico = cpe.codigo_servico
                left join cliente clientePagadorAlocacao on clientePagadorAlocacao.codigo = AlocacaoCPS.codigo_cliente_pagador

                left join cliente_produto MatrizCP on MatrizCP.codigo_cliente = cpe.codigo_cliente_matriz and MatrizCP.codigo_produto = 59
                left join cliente_produto_servico2 MatrizCPS on MatrizCPS.codigo_cliente_produto = MatrizCP.codigo
                    and MatrizCPS.codigo_servico = cpe.codigo_servico
                left join cliente clientePagadorMatriz on clientePagadorMatriz.codigo = MatrizCPS.codigo_cliente_pagador

            WHERE {$whereCliPagador}  (ISNULL(AlocacaoCPS.codigo_cliente_pagador,MatrizCPS.codigo_cliente_pagador) IS NOT NULL)
        )

        ,
        ctePercapita AS (
            SELECT
                cpe.*,

                ISNULL(AlocacaoCPS.codigo_cliente_pagador,MatrizCPS.codigo_cliente_pagador) AS codigo_cliente_pagador,

                ISNULL(clientePagadorAlocacao.codigo,clientePagadorMatriz.codigo) AS cod_pagador,

                ISNULL(clientePagadorAlocacao.nome_fantasia,clientePagadorMatriz.nome_fantasia) AS nome_pagador,

                RHHealth.publico.ufn_formata_cnpj(ISNULL(clientePagadorAlocacao.codigo_documento,clientePagadorMatriz.codigo_documento)) AS cnpj_pagador,
                
                'PER CAPITA' AS forma_de_cobranca,

				FORMAT((CASE WHEN cpe.ambulatorio = 1 THEN '0.00'
                WHEN cpe.prestador_particular = 1 THEN '0.00'
                ELSE ISNULL(AlocacaoCPS.valor, MatrizCPS.valor) END),'c','pt-br') AS valor_exame_a_cobrar
                
            FROM ctePedidosExames cpe 
                
                left join cliente_produto AlocacaoCP on AlocacaoCP.codigo_cliente = cpe.codigo_cliente_alocacao and AlocacaoCP.codigo_produto = 117
                left join cliente_produto_servico2 AlocacaoCPS on AlocacaoCPS.codigo_cliente_produto = AlocacaoCP.codigo
                    and AlocacaoCPS.codigo_servico = cpe.codigo_servico
                left join cliente clientePagadorAlocacao on clientePagadorAlocacao.codigo = AlocacaoCPS.codigo_cliente_pagador
                left join cliente_produto MatrizCP on MatrizCP.codigo_cliente = cpe.codigo_cliente_matriz and MatrizCP.codigo_produto = 117
                left join cliente_produto_servico2 MatrizCPS on MatrizCPS.codigo_cliente_produto = MatrizCP.codigo
                    and MatrizCPS.codigo_servico = cpe.codigo_servico
                left join cliente clientePagadorMatriz on clientePagadorMatriz.codigo = MatrizCPS.codigo_cliente_pagador

            WHERE {$whereCliPagador} (ISNULL(AlocacaoCPS.codigo_cliente_pagador,MatrizCPS.codigo_cliente_pagador) IS NOT NULL)            	
		),

		ctePacoteAnual AS (
            SELECT
                cpe.*,                
      
                ISNULL(AlocacaoCPS.codigo_cliente_pagador,MatrizCPS.codigo_cliente_pagador) AS codigo_cliente_pagador,

                ISNULL(clientePagadorAlocacao.codigo,clientePagadorMatriz.codigo) AS cod_pagador,

                ISNULL(clientePagadorAlocacao.nome_fantasia,clientePagadorMatriz.nome_fantasia) AS nome_pagador,

                RHHealth.publico.ufn_formata_cnpj(ISNULL(clientePagadorAlocacao.codigo_documento,clientePagadorMatriz.codigo_documento)) AS cnpj_pagador,
                
                'PACOTE (ANUAL)' AS forma_de_cobranca,

				FORMAT((CASE WHEN cpe.ambulatorio = 1 THEN '0.00'
                WHEN cpe.prestador_particular = 1 THEN '0.00'
                ELSE ISNULL(AlocacaoCPS.valor, MatrizCPS.valor) END),'c','pt-br') AS valor_exame_a_cobrar
                
            FROM ctePedidosExames cpe 
                
                left join cliente_produto AlocacaoCP on AlocacaoCP.codigo_cliente = cpe.codigo_cliente_alocacao and AlocacaoCP.codigo_produto = 111
                left join cliente_produto_servico2 AlocacaoCPS on AlocacaoCPS.codigo_cliente_produto = AlocacaoCP.codigo
                    and AlocacaoCPS.codigo_servico = cpe.codigo_servico
                left join cliente clientePagadorAlocacao on clientePagadorAlocacao.codigo = AlocacaoCPS.codigo_cliente_pagador
                left join cliente_produto MatrizCP on MatrizCP.codigo_cliente = cpe.codigo_cliente_matriz and MatrizCP.codigo_produto = 111
                left join cliente_produto_servico2 MatrizCPS on MatrizCPS.codigo_cliente_produto = MatrizCP.codigo
                    and MatrizCPS.codigo_servico = cpe.codigo_servico
                left join cliente clientePagadorMatriz on clientePagadorMatriz.codigo = MatrizCPS.codigo_cliente_pagador

            WHERE {$whereCliPagador} (ISNULL(AlocacaoCPS.codigo_cliente_pagador,MatrizCPS.codigo_cliente_pagador) IS NOT NULL)            	
        ),

		ctePacoteMensal AS (
            SELECT
                cpe.*,                
      
                ISNULL(AlocacaoCPS.codigo_cliente_pagador,MatrizCPS.codigo_cliente_pagador) AS codigo_cliente_pagador,

                ISNULL(clientePagadorAlocacao.codigo,clientePagadorMatriz.codigo) AS cod_pagador,

                ISNULL(clientePagadorAlocacao.nome_fantasia,clientePagadorMatriz.nome_fantasia) AS nome_pagador,

                RHHealth.publico.ufn_formata_cnpj(ISNULL(clientePagadorAlocacao.codigo_documento,clientePagadorMatriz.codigo_documento)) AS cnpj_pagador,
                
                'PACOTE MENSAL' AS forma_de_cobranca,

				FORMAT((CASE WHEN cpe.ambulatorio = 1 THEN '0.00'
                WHEN cpe.prestador_particular = 1 THEN '0.00'
                ELSE ISNULL(AlocacaoCPS.valor, MatrizCPS.valor) END),'c','pt-br') AS valor_exame_a_cobrar
                
            FROM ctePedidosExames cpe 
                
                left join cliente_produto AlocacaoCP on AlocacaoCP.codigo_cliente = cpe.codigo_cliente_alocacao and AlocacaoCP.codigo_produto = 118
                left join cliente_produto_servico2 AlocacaoCPS on AlocacaoCPS.codigo_cliente_produto = AlocacaoCP.codigo
                    and AlocacaoCPS.codigo_servico = cpe.codigo_servico
                left join cliente clientePagadorAlocacao on clientePagadorAlocacao.codigo = AlocacaoCPS.codigo_cliente_pagador
                left join cliente_produto MatrizCP on MatrizCP.codigo_cliente = cpe.codigo_cliente_matriz and MatrizCP.codigo_produto = 118
                left join cliente_produto_servico2 MatrizCPS on MatrizCPS.codigo_cliente_produto = MatrizCP.codigo
                    and MatrizCPS.codigo_servico = cpe.codigo_servico
                left join cliente clientePagadorMatriz on clientePagadorMatriz.codigo = MatrizCPS.codigo_cliente_pagador

            WHERE {$whereCliPagador} (ISNULL(AlocacaoCPS.codigo_cliente_pagador,MatrizCPS.codigo_cliente_pagador) IS NOT NULL)            	
        )

        select * from cteExamesComplementares
        union all
        select * from ctePercapita
		union all
        select * from ctePacoteAnual
		union all
        select * from ctePacoteMensal
        ;
		
		";

		// debug($sql);exit;

		return $sql;
	}

	public function relatorioPreFaturamento($filtros = array())
	{

		ini_set('memory_limit', '536870912');
		ini_set('max_execution_time', '999999');
		set_time_limit(0);

		if (empty($filtros['codigo_cliente_alocacao'])) {
			$filtros['codigo_cliente_alocacao'] = $filtros['codigo_unidade'];
		}

		if ($filtros['forma_de_cobranca'] == "Per Capita") {
			$sql = $this->pre_faturamento_percapita($filtros);
		} else if ($filtros['forma_de_cobranca'] == "Exames Complementares") {
			$sql = $this->pre_faturamento_exames_complementares($filtros);
		}

		// debug($sql);exit;

		//        --select * from cteExamesComplementares
		//        --union all
		//		--select * from ctePercapita
		//		{$selecionar}
		//        ;
		//
		//		";

		$data = $this->query($sql);
		return $data;
	}

	public function buscar_ped_exame_embarcado($codigo_matriz)
	{

		$GrupoEconomico = &ClassRegistry::init('GrupoEconomico');
		$GrupoEconomicoCliente = &ClassRegistry::init('GrupoEconomicoCliente');

		//auxiliar para montar as conditions da query
		$where = '';

		$codigo_cliente = $codigo_matriz;
		// debug($codigo_cliente);
		//verifica se é multicliente para passar o array, senão ele irá pesquisar a matriz do cliente pesquisado
		if (isset($_SESSION['Auth']['Usuario']['multicliente'])) {
			$cod_matriz = $codigo_cliente;
		} else {
			$cod_matriz = $GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
		}

		$codigos_unidades = $GrupoEconomicoCliente->lista_unidades_embarcados($cod_matriz);

		if (isset($_SESSION['Auth']['Usuario']['codigo_empresa']) && $_SESSION['Auth']['Usuario']['codigo_empresa']) {
			$where .= " AND pe.codigo_empresa = " . $_SESSION['Auth']['Usuario']['codigo_empresa'];
		}

		if (!empty($codigos_unidades)) {
			$filtros_codigos_unidades = implode(',', $codigos_unidades);
			$where .= " AND cf.codigo_cliente_matricula IN ({$filtros_codigos_unidades})";
			$where .= " AND pe.aso_embarcados = 1";
		}

		$sql = "
			SELECT
				pe.aso_embarcados as aso_embarcado,
				pe.codigo AS cod_pedido_exame,
				cli.codigo AS cod_cliente,
				fsc.codigo_cliente_alocacao AS codigo_cliente_alocacao,
				cf.codigo_cliente AS codigo_cliente_matriz,
				cli.nome_fantasia AS nome_unidade,
				cli.razao_social AS razao_social,
				RHHealth.publico.ufn_formata_cnpj(cli.codigo_documento) AS cnpj_unidade,
				ce.cidade AS cidade_unidade,
				ce.estado_abreviacao AS estado_unidade,                
				func.nome AS nome_funcionario
			FROM pedidos_exames pe
			    inner join cliente cli on pe.codigo_cliente = cli.codigo -- alocacao
			    inner join funcionario_setores_cargos fsc on pe.codigo_func_setor_cargo = fsc.codigo -- alocacao
			    inner join setores s ON s.codigo = fsc.codigo_setor
			    inner join cargos c ON c.codigo = fsc.codigo_cargo
			    inner join cliente_funcionario cf on fsc.codigo_cliente_funcionario = cf.codigo and pe.codigo_cliente_funcionario = cf.codigo -- matricula (matriz)
			    inner join funcionarios func on pe.codigo_funcionario = func.codigo
			    left join cliente_endereco ce on ce.codigo_cliente = cli.codigo
			WHERE 1=1
				{$where}
			GROUP BY
			    pe.codigo,
			    cli.codigo,
			    fsc.codigo_cliente_alocacao,
			    cf.codigo_cliente,
			    cli.nome_fantasia,
			    cli.razao_social,
			    cli.codigo_documento,
			    ce.cidade,
			    ce.estado_abreviacao,
			    func.nome,
				pe.aso_embarcados
		";

		// debug($sql);exit;
		$data = $this->query($sql);
		// debug($data);exit;
		return $data;
	} //fim buscar_ped_exame_embarcado

	public function pre_faturamento_percapita($filtros)
	{
		//instacia a model
		$this->GrupoEconomicoCliente = ClassRegistry::init('GrupoEconomicoCliente');
		//variavel vazia
		$where = '';

		if (!empty($filtros['codigo_cliente'])) {
			//buscar o grupo economico da matriz
			$dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $filtros['codigo_cliente']), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
			//se houver sucesso na busca
			if (isset($dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'])) {
				$codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];
				$where .= " AND gec.codigo_grupo_economico = " . $codigo_grupo_economico;
			}
		}

		if (!empty($filtros['codigo_cliente_alocacao'])) {
			$where .= " AND gec.codigo_cliente = '{$filtros['codigo_cliente_alocacao']}' ";
		}

		$pe_and = '';

		if (!empty($filtros['mes'])) {
			$where .= " AND pedido.mes_referencia = '{$filtros['mes']}'";
			$pe_and .= " AND (MONTH(pe.data_inclusao) = '{$filtros['mes']}') ";
		}

		if (!empty($filtros['ano'])) {
			$where .= " AND pedido.ano_referencia = '{$filtros['ano']}'";
			$pe_and .= " AND (YEAR(pe.data_inclusao) = '{$filtros['ano']}') ";
		}

		if (!empty($filtros['codigo_pagador'])) {
			$where .= " AND clientepagador.codigo = '{$filtros['codigo_pagador']}'";
		}


		$sql = "
			SELECT
				clientealocacao.codigo as cod_cliente,
				gec.codigo_cliente as codigo_unidade,
				clientealocacao.razao_social as razao_cliente,
				clientealocacao.nome_fantasia as nome_cliente,
				clientePagador.razao_social as razao_cliente_pagador,
				clientePagador.nome_fantasia as nome_cliente_pagador,
				funcionario.nome AS nome_funcionario,
				funcionario.cpf AS cpf_funcionario,
				cargo.descricao AS descricao_cargo,
				setor.descricao AS descricao_setor,
				'PER CAPITA' AS forma_de_cobranca,
				ClienteFuncionario.codigo AS codigo_matricula,
				COALESCE(ClienteFuncionario.matricula,'') AS matricula,
				COALESCE(CONVERT(VARCHAR(10),ipa.data_inclusao_cliente_funcionario,103),'') AS data_inclusao,
				COALESCE(CONVERT(VARCHAR(10),ipa.admissao,103),'') AS data_admissao,
				COALESCE(CONVERT(VARCHAR(10),ipa.data_demissao,103),'') AS data_demissao,
				COALESCE(ipa.dias_cobrado,ipa.ultimo_dia_mes,'') AS dias_cobrados,
				ipa.valor AS valor,
				clientepagador.codigo AS clientepagador_codigo,
				clientealocacao.codigo AS clientealocacao_codigo,
				funcionario.nome AS funcionario_nome,
				ISNULL(ClienteFuncionario.centro_custo, '') as centro_custo
				/*,
				pe.codigo as codigo_pedido_exame,
				(CONVERT(VARCHAR,ipe.data_realizacao_exame, 103)) AS data_realizacao_do_exame,
				(CONVERT(VARCHAR,ipeb.data_inclusao, 103)) AS data_baixa_exame,
				e.descricao AS exame*/
			from grupos_economicos_clientes gec 
				inner join grupos_economicos ge on ge.codigo = gec.codigo_grupo_economico
				left join itens_pedidos_alocacao ipa on ipa.codigo_cliente_alocacao = gec.codigo_cliente
				INNER JOIN RHHealth.dbo.pedidos pedido ON ipa.codigo_pedido = pedido.codigo
				INNER JOIN RHHealth.dbo.cliente clientepagador ON ipa.codigo_cliente_pagador = clientepagador.codigo
				INNER JOIN RHHealth.dbo.cliente clientealocacao ON ipa.codigo_cliente_alocacao = clientealocacao.codigo
				INNER JOIN RHHealth.dbo.funcionarios funcionario ON ipa.codigo_funcionario = funcionario.codigo
				INNER JOIN RHHealth.dbo.setores setor ON ipa.codigo_setor = setor.codigo
				INNER JOIN RHHealth.dbo.cargos cargo ON ipa.codigo_cargo = cargo.codigo
				LEFT JOIN RHHealth.dbo.cliente_funcionario ClienteFuncionario ON ClienteFuncionario.codigo = ipa.codigo_cliente_funcionario
				/*left join pedidos_exames pe on pe.codigo_cliente_funcionario = ClienteFuncionario.codigo 
				{$pe_and}
				left join itens_pedidos_exames ipe ON pe.codigo = ipe.codigo_pedidos_exames
			    left join itens_pedidos_exames_baixa ipeb on ipe.codigo = ipeb.codigo_itens_pedidos_exames
				left join exames e on e.codigo = ipe.codigo_exame*/
			where 1=1
				{$where}
			group by
				gec.codigo_cliente,
				clientealocacao.razao_social,
				clientealocacao.nome_fantasia,
				clientePagador.razao_social,
				clientePagador.nome_fantasia,
				funcionario.nome,
				funcionario.cpf,
				cargo.descricao,
				setor.descricao,
				ClienteFuncionario.codigo,
				ClienteFuncionario.matricula,
				ipa.data_inclusao_cliente_funcionario,
				ipa.admissao,
				ipa.data_demissao,
				ipa.dias_cobrado,
				ipa.ultimo_dia_mes,
				ipa.valor,
				clientepagador.codigo,
				clientealocacao.codigo,
				ClienteFuncionario.centro_custo,
				funcionario.nome
				/*,
				pe.codigo,
				ipe.data_realizacao_exame,
				ipeb.data_inclusao,
				e.descricao*/
		";

		return $sql;
	}

	public function pre_faturamento_exames_complementares($filtros)
	{

		$where = '';

		if (!empty($filtros['codigo_cliente'])) {
			$where .= " AND cf.codigo_cliente_matricula = '{$filtros['codigo_cliente']}' ";
		}

		if (!empty($filtros['codigo_cliente_alocacao'])) {
			$where .= " AND fsc.codigo_cliente_alocacao = '{$filtros['codigo_cliente_alocacao']}' ";
		}

		$mes_itens = '';
		$ano_itens = '';
		if (!empty($filtros['mes'])) {
			$referencia_mes = $filtros['mes'] - 1;
			//tratamento quando for Janeiro
			if ($referencia_mes == 0) {
				$referencia_mes = 12;
			}
			$where .= " AND [Pedido].[mes_referencia] = '{$referencia_mes}'";
			$mes_itens .= " (MONTH(ipeb.data_inclusao) = '{$referencia_mes}')";
		}

		if (!empty($filtros['ano'])) {
			$referencia_ano = $filtros['ano'];
			if ($referencia_mes == 12) {
				$referencia_ano--;
			}
			$where .= " AND [Pedido].[ano_referencia] = '{$referencia_ano}' ";
			$ano_itens .= " (YEAR(ipeb.data_inclusao) = '{$referencia_ano}')";
		}

		if (!empty($filtros['codigo_pagador'])) {
			$where .= " AND [Pedido].[codigo_cliente_pagador] = '{$filtros['codigo_pagador']}'";
		}

		$sql = "
			SELECT
			  pe.codigo as codigo_pedido_exame,
			  fsc.codigo_cliente_alocacao as codigo_unidade,
			  cf.codigo_cliente_matricula as cod_cliente,
			  cli.nome_fantasia as nome_cliente,
			  cli.razao_social as razao_cliente,
			  fun.nome as nome_funcionario,
			  forn.nome as nome_fornecedor,
			  CONVERT(varchar(10), ipeb.data_realizacao_exame, 103) as data_realizacao_do_exame,
			  e.codigo_servico as codigo_servico,
			  e.descricao as exame,
			      (
			      CASE
			      WHEN forn.ambulatorio = 1 THEN '0.00'
			      WHEN forn.prestador_particular = 1 THEN '0.00'
			      ELSE det.valor
			    END
			  ) AS valor,
			  ISNULL(cf.centro_custo, '-') as centro_custo,
			  clientePagadorAlocacao.razao_social as razao_cliente_pagador,
			  clientePagadorAlocacao.nome_fantasia as nome_cliente_pagador,
			  Pedido.codigo_cliente_pagador as clientepagador_codigo,
				(CONVERT(VARCHAR,ipeb.data_inclusao, 103)) AS data_baixa_exame,
			  'EXAMES COMPLEMENTARES' as forma_de_cobranca
			FROM RHHealth.dbo.[detalhes_itens_pedidos_manuais] AS det
				INNER JOIN RHHealth.dbo.itens_pedidos AS [ItemPedido]  ON ([ItemPedido].[codigo] = det.[codigo_item_pedido])
				INNER JOIN RHHealth.dbo.[pedidos] AS [Pedido]  ON ([Pedido].[codigo] = [ItemPedido].[codigo_pedido])
				INNER JOIN RHHealth.dbo.[produto] AS [Produto]  ON ([Produto].[codigo] = [ItemPedido].[codigo_produto]
				  AND [Produto].[codigo] = 59)
				INNER JOIN RHHealth.dbo.[servico] AS [Servico]  ON ([Servico].[codigo] = det.[codigo_servico])
				INNER JOIN RHHealth.dbo.[cliente] AS cli  ON (cli.[codigo] = det.codigo_cliente_utilizador)

				inner join RHHealth.dbo.exames e on e.codigo_servico = det.codigo_servico
				inner join RHHealth.dbo.itens_pedidos_exames_baixa ipeb on {$mes_itens} AND {$ano_itens} 
				  AND ipeb.fornecedor_particular=0
				  AND ipeb.pedido_importado <> 1
				 AND ipeb.codigo_itens_pedidos_exames IN (select ipes.codigo
							 from RHHealth.dbo.itens_pedidos_exames  ipes
							inner join RHHealth.dbo.pedidos_exames pes on pes.codigo = ipes.codigo_pedidos_exames
							where pes.codigo_cliente = det.codigo_cliente_utilizador
							AND ipes.codigo_exame = e.codigo)
				-- ipe.codigo = ipeb.codigo_itens_pedidos_exames
				inner join RHHealth.dbo.itens_pedidos_exames ipe on ipe.codigo = ipeb.codigo_itens_pedidos_exames AND ipe.codigo_exame = e.codigo
				inner join RHHealth.dbo.pedidos_exames AS pe on pe.codigo_cliente = det.codigo_cliente_utilizador AND pe.codigo = ipe.codigo_pedidos_exames
				inner join RHHealth.dbo.funcionario_setores_cargos fsc on fsc.codigo = pe.codigo_func_setor_cargo
				inner join RHHealth.dbo.cliente_funcionario cf on cf.codigo = pe.codigo_cliente_funcionario
				INNER JOIN RHHealth.dbo.fornecedores forn on forn.codigo = ipe.codigo_fornecedor
				inner join RHHealth.dbo.funcionarios fun on fun.codigo = pe.codigo_funcionario
				left join cliente clientePagadorAlocacao on clientePagadorAlocacao.codigo = Pedido.codigo_cliente_pagador
			WHERE 1=1
				{$where}
			ORDER BY 
				fsc.codigo_cliente_alocacao ASC,  
				fun.nome ASC
		";

		return $sql;
	} // fim

	//metodo para para gerar os exames do pcmso, do pedido de exame da ficha clinica do funcionario, para mostrar na ficha clinica.
	public function exames_pcmso_fc($codigo_pedido_exame)
	{
		//fields
		$fields = array(
			'(CASE
				WHEN GrupoEconomico.codigo_idioma = \'1,2\' then CONCAT(Exame.descricao,\' / \',Exame.descricao_ingles)
				WHEN GrupoEconomico.codigo_idioma = \'1\' then Exame.descricao
				WHEN GrupoEconomico.codigo_idioma = \'2\' then Exame.descricao_ingles
				ELSE Exame.descricao END) as exame_descricao',
			'CONVERT(VARCHAR(10), ItemPedidoExameBaixa.data_realizacao_exame, 103) AS data_agendamento',
			'PedidoExame.codigo_func_setor_cargo as codigo_func_setor_cargo',
			'PedidoExame.codigo_funcionario as codigo_funcionario',
			'ItemPedidoExame.codigo_exame as codigo_exame',
			'ItemPedidoExame.codigo as codigo_item_pedido_exame',
			'CAST(ItemPedidoExameBaixa.descricao AS NVARCHAR(200)) AS anormalidade',
			'ItemPedidoExameBaixa.resultado as resultado'
		);
		//joins
		$joins = array(
			array(
				'table' 		=> 'grupos_economicos_clientes',
				'alias' 		=> 'GrupoEconomicoCliente',
				'type' 			=> 'INNER',
				'conditions'    => 'GrupoEconomicoCliente.codigo_cliente = PedidoExame.codigo_cliente'
			),
			array(
				'table' 		=> 'grupos_economicos',
				'alias' 		=> 'GrupoEconomico',
				'type' 			=> 'INNER',
				'conditions'    => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
			),
			array(
				'table' 		=> 'itens_pedidos_exames',
				'alias' 		=> 'ItemPedidoExame',
				'type' 			=> 'INNER',
				'conditions'    => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo'
			),
			array(
				'table' 		=> 'exames',
				'alias' 		=> 'Exame',
				'type' 			=> 'INNER',
				'conditions'    => 'Exame.codigo = itemPedidoExame.codigo_exame'
			),
			array(
				'table' 		=> 'pedidos_exames_pcmso_aso',
				'alias' 		=> 'PedidoExamePcmsoAso',
				'type' 			=> 'LEFT',
				'conditions'    => 'PedidoExame.codigo = PedidoExamePcmsoAso.codigo_pedidos_exames AND PedidoExame.codigo_func_setor_cargo = PedidoExamePcmsoAso.codigo_func_setor_cargo
		AND ItemPedidoExame.codigo_exame = PedidoExamePcmsoAso.codigo_exame'
			),
			array(
				'table' 		=> 'itens_pedidos_exames_baixa',
				'alias' 		=> 'ItemPedidoExameBaixa',
				'type' 			=> 'LEFT',
				'conditions'    => 'ItemPedidoExameBaixa.codigo_itens_pedidos_exames = itemPedidoExame.codigo'
			),
		);
		//where
		$conditions = array(
			'PedidoExame.codigo' => $codigo_pedido_exame
		);
		//group by
		$group_by = array(
			'Exame.descricao',
			'itemPedidoExameBaixa.data_realizacao_exame',
			'PedidoExame.codigo_func_setor_cargo',
			'PedidoExame.codigo_funcionario',
			'ItemPedidoExame.codigo_exame',
			'Exame.descricao_ingles',
			'GrupoEconomico.codigo_idioma',
			'CAST(itemPedidoExameBaixa.descricao AS NVARCHAR(200))',
			'ItemPedidoExameBaixa.resultado',
			'ItemPedidoExame.codigo'
		);
		//get exames
		$get_exames_pcmso = $this->find('all', array('conditions' => $conditions, 'fields' => $fields, 'joins' => $joins, 'group' => $group_by));

		if (!empty($get_exames_pcmso)) {

			$this->TiposResultados = ClassRegistry::init('TiposResultados');
			$this->TiposResultadosExames = ClassRegistry::init('TiposResultadosExames');

			foreach ($get_exames_pcmso as $key => $exame) {

				$grupo = array();

				$tipos_resultados = $this->TiposResultadosExames->find('list', array(
					'fields' => array(
						'TiposResultados.codigo',
						'TiposResultados.descricao'
					),
					'joins' => array(
						array(
							'table' 		=> 'tipos_resultados',
							'alias' 		=> 'TiposResultados',
							'type' 			=> 'LEFT',
							'conditions'    => 'TiposResultadosExames.codigo_tipo_resultado = TiposResultados.grupo'
						),
					),
					'conditions' => array(
						"TiposResultadosExames.codigo_exame" => $exame[0]['codigo_exame']
					)
				));

				$get_exames_pcmso[$key][0]['tipos_resultados'] = $tipos_resultados;
			}
		}

		//pr($get_exames_pcmso);
		return $get_exames_pcmso;
	}

	public function get_agendas($usuario = null, $conditions = null, $filtros = null, $codigo_empresa = null)
	{
		$Configuracao = &ClassRegistry::init('Configuracao');
		if (!empty($usuario['Usuario']['codigo_cliente'])) {
			$conditions['Cliente.codigo'] = $usuario['Usuario']['codigo_cliente'];
		}

		if (!empty($usuario['Usuario']['codigo_fornecedor'])) {
			$conditions['Fornecedor.codigo'] = $usuario['Usuario']['codigo_fornecedor'];
		}

		// CDCT-678
		if(empty($codigo_empresa)){
			$codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];
		}

		$conditions['PedidoExame.codigo_status_pedidos_exames <>'] = 5;

		$fields = array(
			'Setor.descricao',
			'Cargo.descricao',
			'FuncionarioSetorCargo.codigo_cliente_alocacao',
			'(SELECT top 1 nome_fantasia FROM Rhhealth.dbo.cliente WHERE codigo = FuncionarioSetorCargo.codigo_cliente_alocacao) AS nome_fantasia_unidade',
			'Cliente.nome_fantasia',
			'Funcionario.cpf',
			'ClienteFuncionario.matricula',
			'ClienteFuncionario.ativo',
			'Medico.nome',
			'CASE
                    WHEN [Exame].[codigo] IN ('.$Configuracao->getChave('FICHA_ASSISTENCIAL').') THEN [Medico3].[nome]
                    WHEN [Exame].[codigo] = '.$Configuracao->getChave('FICHA_PSICOSSOCIAL').' THEN [Medico2].[nome]
                    WHEN [Exame].[codigo]  = '.$Configuracao->getChave('INSERE_EXAME_CLINICO').' THEN [Medico].[nome]
                ELSE \'\'
            END as medico',
			'CASE 
                WHEN ClienteFuncionario.ativo = 0 THEN \'Inativo\'
                WHEN ClienteFuncionario.ativo = 1 THEN \'Ativo\'
                WHEN ClienteFuncionario.ativo = 2 THEN \'Ferias\'
                WHEN ClienteFuncionario.ativo = 3 THEN \'Afastado\' 
                ELSE \'\' END AS status_do_funcionario',
			'AgendamentoExame.data',
			'AgendamentoExame.hora',
			'PedidoExame.data_agendamento',
			'PedidoExame.tipo_exame',
			'PedidoExame.usuario_resp',
			'Glosas.codigo',
			'Glosas.motivo_glosa',
			'TipoGlosas.descricao',
			'TipoGlosas.visualizacao_do_cliente',
			'AgendamentoExame.data_inclusao',
			'Exame.codigo',
			'Exame.descricao',
			'Exame.anexo_nao_comparecimento',
			'ItemPedidoExame.codigo',
			'ItemPedidoExame.tipo_atendimento',
			'ItemPedidoExame.tipo_agendamento',
			'ItemPedidoExame.recebimento_digital',
			'ItemPedidoExame.recebimento_enviado',
			'ItemPedidoExame.respondido_lyn',
			'PedidoExame.codigo',
			'PedidoExame.codigo_cliente_funcionario',
			'FichaPsicossocial.codigo',
			'FichaClinica.codigo',
			'FichaAssistencial.codigo',
			'FichaAssistencialResposta.codigo',
			'FichaAssistencialResposta.resposta',
			'Atestado.codigo',
			'Atestado.exibir_ficha_assistencial',
			'Funcionario.codigo',
			'Audiometria.codigo',
			'ClienteFuncionario.codigo',
			'PedidoExame.data_inclusao',
			'CASE 
                WHEN ItemPedidoExame.tipo_atendimento = \'1\' THEN \'Hora Marcada\' 
                    ELSE \'Ordem de Chegada\' 
            END AS PedidoExame_tipo_agendamento',
			'PedidoExame.exame_admissional',
			'PedidoExame.exame_periodico',
			'PedidoExame.exame_demissional',
			'PedidoExame.exame_retorno',
			'PedidoExame.exame_mudanca',
			'PedidoExame.qualidade_vida',
			'PedidoExame.exame_monitoracao',
			'PedidoExame.data_solicitacao',
			'Fornecedor.codigo',
			'Fornecedor.razao_social',
			'Cliente.codigo',
			'Cliente.razao_social',
			'Funcionario.codigo',
			'Funcionario.nome',
			'ItemPedidoExameBaixa.data_realizacao_exame',
			'ItemPedidoExameBaixa.data_inclusao',
			'ItemPedidoExame.data_realizacao_exame',
			'ItemPedidoExame.data_agendamento',
			'Usuario.nome',
			'UsuarioBaixa.apelido',
			'(CASE  WHEN ItemPedidoExame.data_realizacao_exame IS NOT NULL THEN \'Realizado\'
                    ELSE CASE WHEN ItemPedidoExame.compareceu = 0 THEN \'Não Compareceu\'
                         ELSE \'Pendente\' END 
                    END) AS [Exames_status]',
			"(SELECT valor FROM Rhhealth.dbo.configuracao WHERE codigo_empresa = " . $codigo_empresa . " AND chave = 'INSERE_EXAME_CLINICO') AS codigo_aso",
			"(SELECT valor FROM Rhhealth.dbo.configuracao WHERE codigo_empresa = " . $codigo_empresa . " AND chave = 'INSERE_EXAME_AUDIOMETRICO') AS codigo_audiometrico",
			"(SELECT valor FROM Rhhealth.dbo.configuracao WHERE codigo_empresa = " . $codigo_empresa . " AND chave = 'FICHA_ASSISTENCIAL') AS codigos_ficha_assistencial",
			'AnexoExame.codigo',
			'AnexoExame.caminho_arquivo',
			'AnexoFichaClinica.codigo',
			'AnexoExame.status',
			'AnexoExame.aprovado_auditoria',
			'AnexoFichaClinica.status',
			'AuditoriaExame.codigo_status_auditoria_imagem',
			'AuditoriaExame.libera_anexo_exame',
			'AuditoriaExame.libera_anexo_ficha',
			'UsuarioAnexoExame.nome',
			'UsuarioAnexoFichaClinica.nome',
			'AnexoExame.data_inclusao',
			'AnexoFichaClinica.data_inclusao',
			'AnexoFichaClinica.caminho_arquivo',
			'AnexoFichaClinica.aprovado_auditoria',
			'ItemPedidoExameRecusado.codigo',
			'ClienteUnidade.codigo', //unidade
			'ClienteUnidade.nome_fantasia', //unidade
			'ClienteUnidade.razao_social', //unidade
			'FichaPsicossocial.data_inclusao',
			'FichaClinica.data_inclusao',
			'FichaAssistencial.data_inclusao',
			'Audiometria.data_inclusao',
			'UsuarioFichaPsicossocial.nome',
			'UsuarioFichaClinica.nome',
			'UsuarioFichaAssistencial.nome',
			'UsuarioAudiometria.nome',
			'CASE 
				WHEN ItemPedidoExameBaixa.resultado IS NULL OR ItemPedidoExameBaixa.resultado = \'\' THEN \'NÃO\'
				ELSE \'SIM\' end as resultado_exame_digitado',
			"(CASE
            WHEN Exame.codigo IN (".$Configuracao->getChave('INSERE_EXAME_CLINICO').") 
                THEN 
                    CASE
                        WHEN FichaClinica.ficha_digitada = 1 THEN 'SIM'
                    ELSE 'NÃO'
                END                                                                
          
            WHEN Exame.codigo IN (".$Configuracao->getChave('FICHA_PSICOSSOCIAL').") 
                THEN
                    CASE 
                        WHEN FichaPsicossocial.codigo is not null THEN 'SIM'
                    ELSE 'NÃO'
                END                                                               
              
            WHEN Exame.codigo IN (".$Configuracao->getChave('INSERE_EXAME_AUDIOMETRICO').") 
                THEN
                    CASE 
                        WHEN Audiometria.codigo is not null
                            THEN 'SIM'
                        ELSE 'NÃO'
                    END                                                               
              
            WHEN Exame.codigo IN (".$Configuracao->getChave('FICHA_ASSISTENCIAL').") 
                THEN
                    CASE 
                        WHEN FichaAssistencial.codigo is not null
                            THEN 'SIM'
                        ELSE 'NÃO'
                    END

			ELSE '-' 

			END) AS ficha_digitada",

			"(CASE
            WHEN Exame.codigo IN (".$Configuracao->getChave('INSERE_EXAME_CLINICO').") 
                THEN 
                    CASE
                        WHEN FichaClinica.data_inclusao is not null AND FichaClinica.ficha_digitada = 1 THEN FichaClinica.data_inclusao
                    ELSE  '-'
                END                                                                
			
            WHEN Exame.codigo IN (".$Configuracao->getChave('FICHA_PSICOSSOCIAL').") 
                THEN
                    CASE 
                        WHEN FichaPsicossocial.data_inclusao is not null THEN FichaPsicossocial.data_inclusao
					ELSE  '-'
                END                                                               
              
            WHEN Exame.codigo IN (".$Configuracao->getChave('INSERE_EXAME_AUDIOMETRICO').") 
                THEN
                    CASE 
                        WHEN Audiometria.data_inclusao is not null THEN Audiometria.data_inclusao
                        ELSE '-'
                    END                                                               
              
            WHEN Exame.codigo IN (".$Configuracao->getChave('FICHA_ASSISTENCIAL').") 
                THEN
                    CASE 
                        WHEN FichaAssistencial.data_inclusao is not null THEN FichaAssistencial.data_inclusao
                        ELSE '-'
                    END

			ELSE '-'
			END) AS usuario_ficha_data_inclusao",

			"(CASE
            WHEN Exame.codigo IN (".$Configuracao->getChave('INSERE_EXAME_CLINICO').") 
                THEN 
                    CASE
                        WHEN UsuarioFichaClinica.nome is not null AND FichaClinica.ficha_digitada = 1 THEN UsuarioFichaClinica.nome
                    ELSE  '-'
                END                                                                
          
            WHEN Exame.codigo IN (".$Configuracao->getChave('FICHA_PSICOSSOCIAL').") 
                THEN
                    CASE 
                        WHEN UsuarioFichaPsicossocial.nome is not null THEN UsuarioFichaPsicossocial.nome
					ELSE  '-'
                END                                                               
              
            WHEN Exame.codigo IN (".$Configuracao->getChave('INSERE_EXAME_AUDIOMETRICO').") 
                THEN
                    CASE 
                        WHEN UsuarioAudiometria.nome is not null THEN UsuarioAudiometria.nome
                        ELSE '-'
                    END                                                               
              
            WHEN Exame.codigo IN (".$Configuracao->getChave('FICHA_ASSISTENCIAL').") 
                THEN
                    CASE 
                        WHEN UsuarioFichaAssistencial.nome is not null THEN UsuarioFichaAssistencial.nome
                        ELSE '-'
                    END

			ELSE '-'
			END) AS usuario_ficha_nome",
			'(select top 1 codigo_grupo_economico from grupos_economicos_clientes gec
			inner join grupos_economicos ge on ge.codigo = gec.codigo_grupo_economico
			where gec.codigo_cliente = PedidoExame.codigo_cliente) as codigo_grupo_economico'
		);

		//padrao LEFT JOIN para o join da table itens_pedidos_exames_baixa
		$type_baixa = 'LEFT';
		// Quando for escolhido o filtro de baixa ele muda para INNER JOIN para melhorar o tempo de desempenho da query
		if (!empty($filtros['tipo_periodo']) && $filtros['tipo_periodo'] == 'B') {
			$type_baixa = 'INNER';
		}

		$joins  = array(
			array(
				'table' => 'Rhhealth.dbo.itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
			),
			array(
				'table' => 'Rhhealth.dbo.auditoria_exames',
				'alias' => 'AuditoriaExame',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExame.codigo = AuditoriaExame.codigo_item_pedido_exame',
			),
			array(
				'table' => 'Rhhealth.dbo.agendamento_exames',
				'alias' => 'AgendamentoExame',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExame.codigo = AgendamentoExame.codigo_itens_pedidos_exames',
			),
			array(
				'table' => 'Rhhealth.dbo.itens_pedidos_exames_baixa',
				'alias' => 'ItemPedidoExameBaixa',
				'type' => $type_baixa,
				'conditions' => 'ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo',
			),
			array(
				'table' => 'Rhhealth.dbo.exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'Exame.codigo = ItemPedidoExame.codigo_exame',
			),
			array(
				'table' => 'Rhhealth.dbo.fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => 'Fornecedor.codigo = ItemPedidoExame.codigo_fornecedor',
			),
			array(
				'table' => 'Rhhealth.dbo.cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario',
			),
			array(
				'table' => 'Rhhealth.dbo.cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente_matricula',
			),
			array(
				'table' => 'Rhhealth.dbo.funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
			),
			array(
				'table' => 'Rhhealth.dbo.usuario',
				'alias' => 'Usuario',
				'type' => 'INNER',
				'conditions' => 'PedidoExame.codigo_usuario_inclusao = Usuario.codigo'
			),
			array(
				'table' => 'Rhhealth.dbo.usuario',
				'alias' => 'UsuarioResponsavel',
				'type' => 'LEFT',
				'conditions' => 'AgendamentoExame.codigo_usuario_inclusao = UsuarioResponsavel.codigo'
			),
			array(
				'table' => 'Rhhealth.dbo.usuario',
				'alias' => 'UsuarioBaixa',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExameBaixa.codigo_usuario_inclusao = UsuarioBaixa.codigo'
			),
			array(
				'table' => 'Rhhealth.dbo.fichas_clinicas',
				'alias' => 'FichaClinica',
				'type' => 'LEFT',
				'conditions' => 'FichaClinica.codigo_pedido_exame = PedidoExame.codigo'
			),
			array(
				'table' => 'Rhhealth.dbo.audiometrias',
				'alias' => 'Audiometria',
				'type' => 'LEFT',
				'conditions' => 'Audiometria.codigo_itens_pedidos_exames = ItemPedidoExame.codigo'
			),
			array(
				'table' => 'Rhhealth.dbo.fichas_assistenciais',
				'alias' => 'FichaAssistencial',
				'type' => 'LEFT',
				'conditions' => 'FichaAssistencial.codigo_pedido_exame = PedidoExame.codigo'
			),
			array(
				'table' => 'RHHealth.dbo.atestados',
				'alias' => 'Atestado',
				'type' => 'LEFT',
				'conditions' => 'Atestado.codigo = FichaAssistencial.codigo_atestado'
			),
			array(
				'table' => 'RHHealth.dbo.fichas_assistenciais_respostas',
				'alias' => 'FichaAssistencialResposta',
				'type' => 'LEFT',
				'conditions' => 'FichaAssistencialResposta.codigo_ficha_assistencial = FichaAssistencial.codigo AND FichaAssistencialResposta.codigo_ficha_assistencial_questao = 177'
			),
			array(
				'table' => 'RHHealth.dbo.anexos_exames',
				'alias' => 'AnexoExame',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExame.codigo = AnexoExame.codigo_item_pedido_exame'
			),
			array(
				'table' => 'RHHealth.dbo.usuario',
				'alias' => 'UsuarioAnexoExame',
				'type' => 'LEFT',
				'conditions' => 'UsuarioAnexoExame.codigo = AnexoExame.codigo_usuario_inclusao'
			),
			array(
				'table' => 'RHHealth.dbo.anexos_fichas_clinicas',
				'alias' => 'AnexoFichaClinica',
				'type' => 'LEFT',
				'conditions' => 'FichaClinica.codigo = AnexoFichaClinica.codigo_ficha_clinica'
			),
			array(
				'table' => 'RHHealth.dbo.usuario',
				'alias' => 'UsuarioAnexoFichaClinica',
				'type' => 'LEFT',
				'conditions' => 'UsuarioAnexoFichaClinica.codigo = AnexoFichaClinica.codigo_usuario_inclusao'
			),
			array(
				'table' => 'RHHealth.dbo.itens_pedidos_exames_recusados',
				'alias' => 'ItemPedidoExameRecusado',
				'type'  => 'LEFT',
				'conditions' => 'ItemPedidoExame.codigo = ItemPedidoExameRecusado.codigo_item_pedido_exame'
			),
			array(
				'table' => 'RHHealth.dbo.funcionario_setores_cargos',
				'alias' => 'FuncionarioSetorCargo',
				'type'  => 'LEFT',
				'conditions' => 'PedidoExame.codigo_func_setor_cargo = FuncionarioSetorCargo.codigo'
			),
			array(
				'table' => 'RHHealth.dbo.setores',
				'alias' => 'Setor',
				'type'  => 'LEFT',
				'conditions' => 'Setor.codigo = FuncionarioSetorCargo.codigo_setor'
			),
			array(
				'table' => 'RHHealth.dbo.cargos',
				'alias' => 'Cargo',
				'type'  => 'LEFT',
				'conditions' => 'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo'
			),
			array(
				'table' => 'RHHealth.dbo.funcionario_status',
				'alias' => 'FuncionarioStatus',
				'type'  => 'LEFT',
				'conditions' => 'FuncionarioStatus.codigo = ClienteFuncionario.ativo'
			),
			array(
				'table' => 'RHHealth.dbo.ficha_psicossocial',
				'alias' => 'FichaPsicossocial',
				'type'  => 'LEFT',
				'conditions' => 'FichaPsicossocial.codigo_pedido_exame = PedidoExame.codigo'
			),
			array(
				'table' => 'RHHealth.dbo.medicos',
				'alias' => 'Medico',
				'type'  => 'LEFT',
				'conditions' => 'Medico.codigo = FichaClinica.codigo_medico'
			),
			array(
				'table' => 'RHHealth.dbo.medicos',
				'alias' => 'Medico2',
				'type'  => 'LEFT',
				'conditions' => 'Medico2.codigo = FichaPsicossocial.codigo_medico'
			),
			array(
				'table' => 'RHHealth.dbo.medicos',
				'alias' => 'Medico3',
				'type'  => 'LEFT',
				'conditions' => 'Medico3.codigo = FichaAssistencial.codigo_medico'
			),
			array(
				'table' => 'RHHealth.dbo.cliente',
				'alias' => 'ClienteUnidade',
				'type'  => 'LEFT',
				'conditions' => 'ClienteUnidade.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao' //relacionamento para indicar o nome da unidade em que o funcionario esta alocado. a pedido do chamado CDCT-208
			),
			array(
				'table' => 'Rhhealth.dbo.usuario',
				'alias' => 'UsuarioFichaPsicossocial',
				'type' => 'LEFT',
				'conditions' => 'FichaPsicossocial.codigo_usuario_inclusao = UsuarioFichaPsicossocial.codigo'
			),
			array(
				'table' => 'Rhhealth.dbo.usuario',
				'alias' => 'UsuarioFichaClinica',
				'type' => 'LEFT',
				'conditions' => 'FichaClinica.codigo_usuario_inclusao = UsuarioFichaClinica.codigo'
			),
			array(
				'table' => 'Rhhealth.dbo.usuario',
				'alias' => 'UsuarioFichaAssistencial',
				'type' => 'LEFT',
				'conditions' => 'FichaAssistencial.codigo_usuario_inclusao = UsuarioFichaAssistencial.codigo'
			),
			array(
				'table' => 'Rhhealth.dbo.usuario',
				'alias' => 'UsuarioAudiometria',
				'type' => 'LEFT',
				'conditions' => 'Audiometria.codigo_usuario_inclusao = UsuarioAudiometria.codigo'
			),
			array(
				'table' => 'glosas',
				'alias' => 'Glosas',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExame.codigo = Glosas.codigo_itens_pedidos_exames AND Glosas.codigo_classificacao_glosa = 2  AND Glosas.ativo = 1'
			),
			array(
				'table' => 'RHHealth.dbo.tipo_glosas',
				'alias' => 'TipoGlosas',
				'type' => 'LEFT',
				'conditions' => 'Glosas.codigo_tipo_glosa = TipoGlosas.codigo',
			),
		);

		$order = array(
			'AgendamentoExame.data',
			'AgendamentoExame.hora',
			'Cliente.razao_social',
			'Exame.codigo',
			'ItemPedidoExame.codigo'
		);
		$this->virtualFields = array(
			'tipo_exame' =>
			'CASE
                    WHEN PedidoExame.exame_admissional > 0 THEN \'EXAME ADMISSIONAL\'
                    WHEN PedidoExame.exame_periodico > 0 THEN \'EXAME PERIÓDICO\'
                    WHEN PedidoExame.exame_demissional > 0 THEN \'EXAME DEMISSIONAL\'
                    WHEN PedidoExame.exame_retorno > 0 THEN \'RETORNO AO TRABALHO\'
                    WHEN PedidoExame.exame_mudanca > 0 THEN \'MUDANÇA DE RISCOS OCUPACIONAIS\'
                    WHEN PedidoExame.exame_monitoracao > 0 THEN \'MONITORAÇÃO PONTUAL\'
                    WHEN PedidoExame.pontual > 0 THEN \'PONTUAL\'
                ELSE \'\' END',
			'data_agendamento' =>
			'CASE  
                    WHEN AgendamentoExame.data IS NOT NULL THEN CONCAT(AgendamentoExame.data,\' \',AgendamentoExame.hora) 
                ELSE CONVERT(varchar(10), PedidoExame.data_inclusao, 20) END',
			'usuario_resp' =>
			'CASE
                    WHEN AgendamentoExame.codigo IS NOT NULL THEN UsuarioResponsavel.apelido
                ELSE Usuario.apelido END'
		);

		$dados = array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields,
			// 'group' => $group,
			'order' => $order
		);

		return $dados;
	}

	public function getListaAnexoExameImagens($codigo_fornecedor = null, $filtros)
	{

		//pr($filtros);
		//monta os fields
		$fields = array(
			'PedidoExame.codigo',
			'PedidoExame.codigo_cliente',
			'Fornecedor.codigo',
			'Fornecedor.nome',
			'Funcionario.nome',
			'Exame.codigo',
			'Exame.descricao',
			'ItemPedidoExame.data_realizacao_exame',
			'ItemPedidoExame.codigo',
			'Usuario.nome',
			'AuditoriaExame.motivo',
			'AuditoriaExame.data_inclusao',
			'AnexosExames.caminho_arquivo',
			'AuditoriaExame.codigo_status_auditoria_imagem',
			'FichasClinicas.codigo',
			'AnexosFichasClinicas.caminho_arquivo',
			'Clientes.nome_fantasia',
			'Glosas.codigo',
			'Glosas.motivo_glosa',
			'TipoGlosas.descricao'
			// 'TipoGlosas.visualizacao_do_cliente'
		);

		//monta os joins
		$joins = array(
			array(
				'table' => 'itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo'
			),
			array(
				'table' => 'auditoria_exames',
				'alias' => 'AuditoriaExame',
				'type' => 'INNER',
				'conditions' => 'AuditoriaExame.codigo_item_pedido_exame = ItemPedidoExame.codigo AND AuditoriaExame.codigo_pedido_exame = PedidoExame.codigo AND AuditoriaExame.codigo_status_auditoria_imagem = 2'
			),
			array(
				'table' => 'fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => 'AuditoriaExame.codigo_fornecedor = Fornecedor.codigo'
			),
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'PedidoExame.codigo_cliente_funcionario = ClienteFuncionario.codigo'
			),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
			),
			array(
				'table' => 'exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo'
			),
			array(
				'table' => 'usuario',
				'alias' => 'Usuario',
				'type' => 'INNER',
				'conditions' => 'AuditoriaExame.codigo_usuario_inclusao = Usuario.codigo'
			),
			array(
				'table' => 'anexos_exames',
				'alias' => 'AnexosExames',
				'type' => 'INNER',
				'conditions' => 'AnexosExames.codigo_item_pedido_exame = ItemPedidoExame.codigo'
			),
			array(
				'table' => 'fichas_clinicas',
				'alias' => 'FichasClinicas',
				'type' => 'LEFT',
				'conditions' => 'FichasClinicas.codigo_pedido_exame = PedidoExame.codigo'
			),
			array(
				'table' => 'anexos_fichas_clinicas',
				'alias' => 'AnexosFichasClinicas',
				'type' => 'LEFT',
				'conditions' => 'AnexosFichasClinicas.codigo_ficha_clinica = FichasClinicas.codigo'
			),
			array(
				'table' => 'cliente',
				'alias' => 'Clientes',
				'type' => 'INNER',
				'conditions' => 'Clientes.codigo = PedidoExame.codigo_cliente'
			),
			array(
				'table' => 'glosas',
				'alias' => 'Glosas',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo = Glosas.codigo_itens_pedidos_exames AND Glosas.codigo_classificacao_glosa = 2  AND Glosas.ativo = 1'
			),
			array(
				'table' => 'RHHealth.dbo.tipo_glosas',
				'alias' => 'TipoGlosas',
				'type' => 'INNER',
				'conditions' => 'Glosas.codigo_tipo_glosa = TipoGlosas.codigo',
			),

		);

		$conditions = array();

		$conditions[] = $this->converteFiltroEmConditionAnexos($filtros);

		if ($codigo_fornecedor != 'null') {
			$conditions[] = array('ItemPedidoExame.codigo_fornecedor' => $codigo_fornecedor);
		}

		//pr($conditions);
		$imagens = array(
			'fields' => $fields,
			'joins' => $joins,
			'conditions' => $conditions,
		);

		return $imagens;
	}

	public function converteFiltroEmConditionAnexos($data)
	{
		$conditions = array();

		if (!empty($data['codigo_pedido_exame'])) {
			$conditions['PedidoExame.codigo'] = $data['codigo_pedido_exame'];
		}

		if (!empty($data['codigo_item_pedido_exame'])) {
			$conditions['ItemPedidoExame.codigo'] = $data['codigo_item_pedido_exame'];
		}

		if (!empty($data['data_inicio']) && !empty($data['data_fim'])) {
			$conditions[] = array(
				"CONVERT(VARCHAR(24),ItemPedidoExame.data_realizacao_exame,103) >= '{$data['data_inicio']}' AND CONVERT(VARCHAR(24),ItemPedidoExame.data_realizacao_exame,103) <= '{$data['data_fim']}'"
			);
		}

		if (!empty($data['data_inicio']) && empty($data['data_fim'])) {
			$conditions[] = array(
				"CONVERT(VARCHAR(24),ItemPedidoExame.data_realizacao_exame,103) >= '{$data['data_inicio']}' "
			);
		}

		if (!empty($data['data_fim']) && empty($data['data_inicio'])) {
			$conditions[] = array(
				"CONVERT(VARCHAR(24),ItemPedidoExame.data_realizacao_exame,103) <= '{$data['data_fim']}' "
			);
		}


		return $conditions;
	}

	public function getListagemPedidos($conditions, $pagination = false)
	{

		// die(debug("getListagemPedidos"));
		//pega o codigo da empresa
		$codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];
		$Configuracao = &ClassRegistry::init('Configuracao');
		$options['fields'] = array(
			'Setor.descricao',
			'Cargo.descricao',
			'FuncionarioSetorCargo.codigo_cliente_alocacao',
			'(SELECT top 1 nome_fantasia FROM Rhhealth.dbo.cliente WHERE codigo = FuncionarioSetorCargo.codigo_cliente_alocacao) AS nome_fantasia_unidade',
			'Cliente.nome_fantasia',
			'Funcionario.cpf',
			'ClienteFuncionario.matricula',
			'ClienteFuncionario.ativo',
			'CASE
                    WHEN [Exame].[codigo] IN ('.$Configuracao->getChave('FICHA_PSICOSSOCIAL').') THEN [Medico3].[nome]
                    WHEN [Exame].[codigo] = '.$Configuracao->getChave('FICHA_PSICOSSOCIAL').' THEN [Medico2].[nome]
                    WHEN [Exame].[codigo]  = '.$Configuracao->getChave('INSERE_EXAME_CLINICO').' THEN [Medico].[nome]
                ELSE \'\'
            END as medico',
			'CASE 
                WHEN ClienteFuncionario.ativo = 0 THEN \'Inativo\'
                WHEN ClienteFuncionario.ativo = 1 THEN \'Ativo\'
                WHEN ClienteFuncionario.ativo = 2 THEN \'Ferias\'
                WHEN ClienteFuncionario.ativo = 3 THEN \'Afastado\' 
                ELSE \'\' END AS status_do_funcionario',
			'AgendamentoExame.data',
			'AgendamentoExame.hora',
			'PedidoExame.data_agendamento',
			'PedidoExame.tipo_exame',
			'PedidoExame.usuario_resp',
			'Glosas.codigo',
			'Glosas.motivo_glosa',
			'TipoGlosas.descricao',
			'TipoGlosas.visualizacao_do_cliente',
			'AgendamentoExame.data_inclusao',
			'Exame.codigo',
			'Exame.descricao',
			'ItemPedidoExame.codigo',
			'ItemPedidoExame.tipo_atendimento',
			'ItemPedidoExame.tipo_agendamento',
			'ItemPedidoExame.recebimento_enviado',
			'ItemPedidoExame.respondido_lyn',
			'AuditoriaExame.codigo_status_auditoria_imagem', // campo que irá ser parâmetro para o usuário final ver ou não o anexo do exame
			'AuditoriaExame.libera_anexo_exame',
			'AuditoriaExame.libera_anexo_ficha',
			'PedidoExame.codigo',
			'PedidoExame.codigo_cliente_funcionario',
			'FichaClinica.codigo',
			'Funcionario.codigo',
			'Audiometria.codigo',
			'ClienteFuncionario.codigo',
			'PedidoExame.data_inclusao',
			'CASE WHEN AgendamentoExame.codigo IS NOT NULL THEN \'Hora Marcada\' ELSE \'Ordem de Chegada\' END AS PedidoExame_tipo_agendamento',
			'PedidoExame.exame_admissional',
			'PedidoExame.exame_periodico',
			'PedidoExame.exame_demissional',
			'PedidoExame.exame_retorno',
			'PedidoExame.exame_mudanca',
			'PedidoExame.exame_monitoracao',
			'PedidoExame.qualidade_vida',
			'PedidoExame.data_solicitacao',
			'Fornecedor.codigo',
			'Fornecedor.razao_social',
			'Cliente.codigo',
			'Cliente.razao_social',
			'Funcionario.codigo',
			'Funcionario.nome',
			'ItemPedidoExameBaixa.data_realizacao_exame',
			'ItemPedidoExameBaixa.data_inclusao',
			'ItemPedidoExame.data_realizacao_exame',
			'Usuario.nome',
			'UsuarioBaixa.apelido',
			'(CASE  WHEN ItemPedidoExame.data_realizacao_exame IS NOT NULL THEN \'Realizado\'
                    ELSE CASE WHEN ItemPedidoExame.compareceu = 0 THEN \'Não Compareceu\'
                         ELSE \'Pendente\' END 
                    END) AS [Exames_status]',
			"(SELECT valor FROM Rhhealth.dbo.configuracao WHERE codigo_empresa = " . $codigo_empresa . " AND chave = 'INSERE_EXAME_CLINICO') AS codigo_aso",
			"(SELECT valor FROM Rhhealth.dbo.configuracao WHERE codigo_empresa = " . $codigo_empresa . " AND chave = 'INSERE_EXAME_AUDIOMETRICO') AS codigo_audiometrico",
			"(SELECT valor FROM Rhhealth.dbo.configuracao WHERE codigo_empresa = " . $codigo_empresa . " AND chave = 'FICHA_ASSISTENCIAL') AS codigos_ficha_assistencial",
			'AnexoExame.codigo',
			'AnexoExame.caminho_arquivo',
			'AnexoExame.aprovado_auditoria',
			'AnexoFichaClinica.codigo',
			'AnexoExame.status AS ae_status',
			'AnexoFichaClinica.status AS afc_status',
			'AnexoFichaClinica.aprovado_auditoria',
			'ClienteUnidade.codigo', //unidade
			'ClienteUnidade.nome_fantasia', //unidade
			'ClienteUnidade.razao_social', //unidade
			'CASE 
				WHEN ItemPedidoExameBaixa.resultado IS NULL OR ItemPedidoExameBaixa.resultado = \'\' THEN \'NÃO\'
				ELSE \'SIM\' end as resultado_exame_digitado',
			'(CASE
				WHEN Exame.codigo = (SELECT top 1 valor FROM configuracao WHERE codigo_empresa = PedidoExame.codigo_empresa AND chave = \'INSERE_EXAME_CLINICO\')
				THEN CASE 
					WHEN FichaClinica.ficha_digitada = 1 THEN \'SIM\'
				ELSE \'NÃO\' end
			ELSE \'\' end ) AS ficha_digitada',
			'(select top 1 codigo_grupo_economico from grupos_economicos_clientes gec
			inner join grupos_economicos ge on ge.codigo = gec.codigo_grupo_economico
			where gec.codigo_cliente = PedidoExame.codigo_cliente) as codigo_grupo_economico'
		);

		$options['joins'] = array(
			array(
				'table' => 'Rhhealth.dbo.itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
			),
			array(
				'table' => 'Rhhealth.dbo.agendamento_exames',
				'alias' => 'AgendamentoExame',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExame.codigo = AgendamentoExame.codigo_itens_pedidos_exames',
			),
			array(
				'table' => 'Rhhealth.dbo.itens_pedidos_exames_baixa',
				'alias' => 'ItemPedidoExameBaixa',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.auditoria_exames',
				'alias' => 'AuditoriaExame',
				'type' => 'LEFT',
				'conditions' => 'AuditoriaExame.codigo_item_pedido_exame = ItemPedidoExame.codigo',
			),
			array(
				'table' => 'Rhhealth.dbo.exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'Exame.codigo = ItemPedidoExame.codigo_exame',
			),
			array(
				'table' => 'Rhhealth.dbo.fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => 'Fornecedor.codigo = ItemPedidoExame.codigo_fornecedor',
			),
			array(
				'table' => 'Rhhealth.dbo.cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario',
			),
			array(
				'table' => 'Rhhealth.dbo.cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente_matricula',
			),
			array(
				'table' => 'Rhhealth.dbo.funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
			),
			array(
				'table' => 'Rhhealth.dbo.usuario',
				'alias' => 'Usuario',
				'type' => 'INNER',
				'conditions' => 'PedidoExame.codigo_usuario_inclusao = Usuario.codigo'
			),
			array(
				'table' => 'Rhhealth.dbo.usuario',
				'alias' => 'UsuarioResponsavel',
				'type' => 'LEFT',
				'conditions' => 'AgendamentoExame.codigo_usuario_inclusao = UsuarioResponsavel.codigo'
			),
			array(
				'table' => 'Rhhealth.dbo.usuario',
				'alias' => 'UsuarioBaixa',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExameBaixa.codigo_usuario_inclusao = UsuarioBaixa.codigo'
			),
			array(
				'table' => 'Rhhealth.dbo.fichas_clinicas',
				'alias' => 'FichaClinica',
				'type' => 'LEFT',
				'conditions' => 'FichaClinica.codigo_pedido_exame = PedidoExame.codigo'
			),
			array(
				'table' => 'Rhhealth.dbo.audiometrias',
				'alias' => 'Audiometria',
				'type' => 'LEFT',
				'conditions' => 'Audiometria.codigo_itens_pedidos_exames = ItemPedidoExame.codigo'
			),
			array(
				'table' => 'Rhhealth.dbo.fichas_assistenciais',
				'alias' => 'FichaAssistencial',
				'type' => 'LEFT',
				'conditions' => 'FichaAssistencial.codigo_pedido_exame = PedidoExame.codigo'
			),
			array(
				'table' => 'RHHealth.dbo.atestados',
				'alias' => 'Atestado',
				'type' => 'LEFT',
				'conditions' => 'Atestado.codigo = FichaAssistencial.codigo_atestado'
			),
			array(
				'table' => 'RHHealth.dbo.fichas_assistenciais_respostas',
				'alias' => 'FichaAssistencialResposta',
				'type' => 'LEFT',
				'conditions' => 'FichaAssistencialResposta.codigo_ficha_assistencial = FichaAssistencial.codigo AND FichaAssistencialResposta.codigo_ficha_assistencial_questao = 177'
			),
			array(
				'table' => 'RHHealth.dbo.anexos_exames',
				'alias' => 'AnexoExame',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExame.codigo = AnexoExame.codigo_item_pedido_exame'
			),
			array(
				'table' => 'RHHealth.dbo.anexos_fichas_clinicas',
				'alias' => 'AnexoFichaClinica',
				'type' => 'LEFT',
				'conditions' => 'FichaClinica.codigo = AnexoFichaClinica.codigo_ficha_clinica'
			),
			array(
				'table' => 'RHHealth.dbo.funcionario_setores_cargos',
				'alias' => 'FuncionarioSetorCargo',
				'type'  => 'LEFT',
				'conditions' => 'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo AND FuncionarioSetorCargo.codigo = PedidoExame.codigo_func_setor_cargo'
			),
			array(
				'table' => 'RHHealth.dbo.setores',
				'alias' => 'Setor',
				'type'  => 'LEFT',
				'conditions' => 'Setor.codigo = FuncionarioSetorCargo.codigo_setor'
			),
			array(
				'table' => 'RHHealth.dbo.cargos',
				'alias' => 'Cargo',
				'type'  => 'LEFT',
				'conditions' => 'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo'
			),
			array(
				'table' => 'RHHealth.dbo.funcionario_status',
				'alias' => 'FuncionarioStatus',
				'type'  => 'LEFT',
				'conditions' => 'FuncionarioStatus.codigo = ClienteFuncionario.ativo'
			),
			array(
				'table' => 'RHHealth.dbo.ficha_psicossocial',
				'alias' => 'FichaPsicossocial',
				'type'  => 'LEFT',
				'conditions' => 'FichaPsicossocial.codigo_pedido_exame = PedidoExame.codigo'
			),
			array(
				'table' => 'RHHealth.dbo.medicos',
				'alias' => 'Medico',
				'type'  => 'LEFT',
				'conditions' => 'Medico.codigo = FichaClinica.codigo_medico'
			),
			array(
				'table' => 'RHHealth.dbo.medicos',
				'alias' => 'Medico2',
				'type'  => 'LEFT',
				'conditions' => 'Medico2.codigo = FichaPsicossocial.codigo_medico'
			),
			array(
				'table' => 'RHHealth.dbo.medicos',
				'alias' => 'Medico3',
				'type'  => 'LEFT',
				'conditions' => 'Medico3.codigo = FichaAssistencial.codigo_medico'
			),
			array(
				'table' => 'RHHealth.dbo.cliente',
				'alias' => 'ClienteUnidade',
				'type'  => 'LEFT',
				'conditions' => 'ClienteUnidade.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao' //relacionamento para indicar o nome da unidade em que o funcionario esta alocado. a pedido do chamado CDCT-208
			),
			array(
				'table' => 'glosas',
				'alias' => 'Glosas',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExame.codigo = Glosas.codigo_itens_pedidos_exames AND Glosas.codigo_classificacao_glosa = 2  AND Glosas.ativo = 1'
			),
			array(
				'table' => 'RHHealth.dbo.tipo_glosas',
				'alias' => 'TipoGlosas',
				'type' => 'LEFT',
				'conditions' => 'Glosas.codigo_tipo_glosa = TipoGlosas.codigo',
			),			
		);

		$options['order'] = array('AgendamentoExame.data', 'AgendamentoExame.hora', 'Cliente.razao_social', 'Exame.codigo', 'ItemPedidoExame.codigo');
		$this->virtualFields = array(
			'tipo_exame' =>
			'CASE
                    WHEN PedidoExame.exame_admissional > 0 THEN \'EXAME ADMISSIONAL\'
                    WHEN PedidoExame.exame_periodico > 0 THEN \'EXAME PERIÓDICO\'
                    WHEN PedidoExame.exame_demissional > 0 THEN \'EXAME DEMISSIONAL\'
                    WHEN PedidoExame.exame_retorno > 0 THEN \'RETORNO AO TRABALHO\'
                    WHEN PedidoExame.exame_mudanca > 0 THEN \'MUDANÇA DE RISCOS OCUPACIONAIS\'
                    WHEN PedidoExame.exame_monitoracao > 0 THEN \'MONITORAÇÃO PONTUAL\'
                    WHEN PedidoExame.pontual > 0 THEN \'PONTUAL\'
                ELSE \'\' END',
			'data_agendamento' =>
			'CASE  
                WHEN AgendamentoExame.data IS NOT NULL THEN CONCAT(AgendamentoExame.data,\' \',AgendamentoExame.hora) 
                ELSE CONVERT(varchar(10), PedidoExame.data_inclusao, 20) END',
			'usuario_resp' =>
			'CASE
                WHEN AgendamentoExame.codigo IS NOT NULL THEN UsuarioResponsavel.apelido
                ELSE Usuario.apelido END'
		);

		$options['conditions'] = $conditions;

		if ($pagination) {
			$paginate = $options;
			$paginate['limit'] = 50;
			return $paginate;
		} else {
			return $this->find('sql', $options);
		}
	}

	public function getDataUltimoAsoByCodigoClienteFuncionario($codigo_cliente_funcionario){

		$itemPedidoExame = ClassRegistry::init('ItemPedidoExame');
		$PedidoExame = ClassRegistry::init('PedidoExame');
		$itemPedidoExameBaixa = ClassRegistry::init('ItemPedidoExameBaixa');
		
		$sql = "SELECT 
					pe.codigo as codigo_pedido_exame, pe.codigo_cliente_funcionario, ipe.codigo as codigo_item_pedido_exame,
					ipe.codigo_pedidos_exames, ipe.codigo_exame, ipe.data_realizacao_exame,
					 ipeb.codigo, ipeb.codigo_itens_pedidos_exames, ipeb.data_realizacao_exame as data_realizacao_pedido2		
				FROM {$PedidoExame->databaseTable}.{$PedidoExame->tableSchema}.{$PedidoExame->useTable} as pe
				INNER JOIN {$itemPedidoExame->databaseTable}.{$itemPedidoExame->tableSchema}.{$itemPedidoExame->useTable} AS ipe
                ON ipe.codigo_pedidos_exames = pe.codigo
				LEFT JOIN {$itemPedidoExameBaixa->databaseTable}.{$itemPedidoExameBaixa->tableSchema}.{$itemPedidoExameBaixa->useTable} AS ipeb
				ON ipeb.codigo_itens_pedidos_exames = ipe.codigo
				where pe.codigo_cliente_funcionario = {$codigo_cliente_funcionario} AND ipe.codigo_exame = 52";

		return $this->query($sql);
	}

	public function getDataUltimoAsoByMatricula($matricula){

		
		$ClienteFuncionario = ClassRegistry::init('ClienteFuncionario');
		$PedidoExame = ClassRegistry::init('PedidoExame');
		$itemPedidoExame = ClassRegistry::init('ItemPedidoExame');
		$itemPedidoExameBaixa = ClassRegistry::init('ItemPedidoExameBaixa');
		
		$sql = "SELECT top 1
					clieFunc.matricula, pe.codigo_cliente_funcionario,  ipe.codigo_pedidos_exames,
					ipe.codigo_exame as codigo_exame_ipe, ipe.data_realizacao_exame as data_realizacao_exame_ipe,
					ipeb.codigo, ipeb.codigo_itens_pedidos_exames, ipeb.data_realizacao_exame as data_realizacao_exame_ipeb		
				FROM {$ClienteFuncionario->databaseTable}.{$ClienteFuncionario->tableSchema}.{$ClienteFuncionario->useTable} as clieFunc
				INNER JOIN {$PedidoExame->databaseTable}.{$PedidoExame->tableSchema}.{$PedidoExame->useTable} as pe
				ON pe.codigo_cliente_funcionario = clieFunc.codigo
				LEFT JOIN {$itemPedidoExame->databaseTable}.{$itemPedidoExame->tableSchema}.{$itemPedidoExame->useTable} AS ipe
				ON ipe.codigo_pedidos_exames = pe.codigo
				LEFT JOIN {$itemPedidoExameBaixa->databaseTable}.{$itemPedidoExameBaixa->tableSchema}.{$itemPedidoExameBaixa->useTable} AS ipeb
				ON ipeb.codigo_itens_pedidos_exames = ipe.codigo
				where clieFunc.matricula = '$matricula' AND ipe.codigo_exame = 52 
				AND (ipe.data_realizacao_exame IS NOT NULL or ipeb.data_realizacao_exame IS NOT NULL)
				order by ipe.data_realizacao_exame desc";

		$result = $this->query($sql);
		$data_realizacao_exame = !empty($result[0][0]['data_realizacao_exame_ipeb']) ?
			 $result[0][0]['data_realizacao_exame_ipeb'] : '';
		return $data_realizacao_exame;
	}
}//fim pedido_exame
