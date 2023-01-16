<?php
class ConsolidadoNfsExame extends AppModel
{

	public $name		   	= 'ConsolidadoNfsExame';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'consolidado_nfs_exame';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Loggable' => array('foreign_key' => 'codigo_consolidado_nfs_exame'), 'Containable');

	const CONCLUIDO = 1;
	const PENDENTE = 2;

	// metodo para converter os dados do filtro em condições
	public function converteFiltroEmCondition($filtros)
	{

		$conditions = array();
		if (!empty($filtros['codigo_fornecedor'])) {
			$conditions['AuditoriaExame.codigo_fornecedor'] = $filtros['codigo_fornecedor'];
		}

		if (!empty($filtros['codigo_consolidacao'])) {
			$conditions['ConsolidadoNfsExame.codigo'] = $filtros['codigo_consolidacao'];
		}

		if (!empty($filtros['cnpj_fornecedor'])) {
			$conditions['Fornecedor.codigo_documento'] = Comum::soNumero($filtros['cnpj_fornecedor']);
		}

		if (!empty($filtros['numero_nota_fiscal'])) {
			$conditions['AuditoriaExame.numero_nota_fiscal'] = $filtros['numero_nota_fiscal'];
		}

		if (!empty($filtros['razao_social'])) {
			$conditions['Fornecedor.razao_social LIKE'] = '%' . $filtros['razao_social'] . '%';
		}

		if (!empty($filtros['nome_fantasia'])) {
			$conditions['Fornecedor.nome LIKE'] = '%' . $filtros['nome_fantasia'] . '%';
		}


		if (!empty($filtros['codigo_pedido_exame'])) {
			$conditions['PedidoExame.codigo'] = $filtros['codigo_pedido_exame'];
		}

		if (!empty($filtros['codigo_item_pedido_exame'])) {
			$conditions['ItemPedidoExame.codigo'] = $filtros['codigo_item_pedido_exame'];
		}

		if (!empty($filtros["data_inicio"])) {
			$data_inicio = AppModel::dateToDbDate($filtros["data_inicio"] . ' 00:00:00');
			$data_fim = AppModel::dateToDbDate($filtros["data_fim"] . ' 23:59:59');
			$conditions[] = "(ItemPedidoExameBaixa.data_inclusao >= '" . $data_inicio . "'";
		}

		if (!empty($filtros["data_fim"])) {
			$conditions[] = "ItemPedidoExameBaixa.data_inclusao <= '" . $data_fim . "')";
		}

		if (!empty($filtros['consolidado'])) {
			//quando o status for igual a nao
			if ($filtros['consolidado'] == self::PENDENTE) {
				$conditions[] = array('OR' => array(
					'ConsolidadoNfsExame.status' => $filtros['consolidado'], "ConsolidadoNfsExame.status IS NULL"
				));
			} else {
				$conditions['ConsolidadoNfsExame.status'] = $filtros['consolidado'];
			}
		}

		if (!empty($filtros['funcionario'])) {
			$conditions['Funcionario.nome LIKE'] = '%' . $filtros['funcionario'] . '%';
		}

		if (!empty($filtros['cpf_funcionario'])) {
			$conditions['Funcionario.cpf'] = Comum::soNumero($filtros['cpf_funcionario']);
		}

		return $conditions;
	}

	/**
	 * [getDadosNfsExame description]
	 * 
	 * metodo para montar a query e gera os dados dos exames para consolidar
	 * 
	 * @return [type] [description]
	 */
	public function getDadosNfsExame($filtros = null, $tipo = null)
	{

		//monta as conditions
		$conditions = $this->converteFiltroEmCondition($filtros);

		//monta o que irá retornar do select
		$fields = array(
			'ConsolidadoNfsExame.codigo as codigo_consolidado_nfs_exame',
			'ConsolidadoNfsExame.codigo_nota_fiscal_servico as codigo_nota_fiscal',
			'ConsolidadoNfsExame.status as status_consolidado',
			'PedidoExame.codigo as codigo_pedido_exame',
			'Exame.descricao as exame',
			'Fornecedor.codigo as codigo_credenciado',
			'Cliente.codigo as codigo_cliente',
			'Cliente.razao_social as nome_cliente',
			'ConsolidadoNfsExame.valor as valor_custo',
			'ConsolidadoNfsExame.valor_corrigido as valor_corrigido',
			'NotaFiscalServico.data_vencimento as data_vencimento_nfs',
			'NotaFiscalServico.data_pagamento as data_pagamento_nfs',
			'ConsolidadoNfsExame.data_vencimento as data_vencimento_cne',
			'ConsolidadoNfsExame.data_pagamento as data_pagamento_cne',
			'ItemPedidoExame.codigo as codigo_item_pedido_exame',
			'Exame.codigo as codigo_exame',
			'NotaFiscalServico.numero_nota_fiscal as numero_nfs',
			"CONVERT(VARCHAR, ItemPedidoExameBaixa.data_realizacao_exame, 120) AS data_realizacao", // data realização ou data_resultado
			'Funcionario.nome as funcionario_nome',
			'Funcionario.cpf as funcionario_cpf',
			'Fornecedor.razao_social as credenciado_razao_social',
			'Fornecedor.codigo_documento as credenciado_cnpj',
			'Fornecedor.nome as credenciado_nome_fantasia',
			"CONVERT(VARCHAR, ItemPedidoExameBaixa.data_inclusao, 120)  AS data_baixa",  // data de inclusão ou considerada baixa
			'CAST(
				(SELECT CONCAT(codigo,\';\',numero_nota_fiscal,\'|\')
				FROM nota_fiscal_servico subNfs
				where subNfs.codigo_fornecedor = Fornecedor.codigo
					and subNfs.ativo = 1
					and subNfs.codigo_nota_fiscal_status IN (1,2,4)
				FOR XML  PATH(\'\')) AS text)                                  AS fornecedor_notas_fiscais'
		);

		//monta o join da query
		//monta o join da query
		$joins = array(
			array(
				'table' => 'RHHealth.dbo.itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.itens_pedidos_exames_baixa',
				'alias' => 'ItemPedidoExameBaixa',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo = ItemPedidoExameBaixa.codigo_itens_pedidos_exames',
			),
			array(
				'table' => 'RHHealth.dbo.fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_fornecedor = Fornecedor.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.auditoria_exames',
				'alias' => 'AuditoriaExame',
				'type' => 'INNER',
				'conditions' => 'AuditoriaExame.codigo_pedido_exame = PedidoExame.codigo AND AuditoriaExame.codigo_item_pedido_exame = ItemPedidoExame.codigo AND AuditoriaExame.codigo_fornecedor = Fornecedor.codigo AND AuditoriaExame.codigo_exame = Exame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = PedidoExame.codigo_cliente',
			),
			array(
				'table' => 'RHHealth.dbo.consolidado_nfs_exame',
				'alias' => 'ConsolidadoNfsExame',
				'type' => 'LEFT',
				'conditions' => 'ConsolidadoNfsExame.codigo_item_pedido_exame = ItemPedidoExame.codigo AND ConsolidadoNfsExame.codigo_pedido_exame = PedidoExame.codigo AND ConsolidadoNfsExame.codigo_fornecedor = Fornecedor.codigo AND ConsolidadoNfsExame.codigo_exame = Exame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.nota_fiscal_servico',
				'alias' => 'NotaFiscalServico',
				'type' => 'LEFT',
				'conditions' => 'NotaFiscalServico.codigo = ConsolidadoNfsExame.codigo_nota_fiscal_servico',
			),
			array(
				'table' => 'RHHealth.dbo.funcionarios',
				'alias' => 'Funcionario',
				'type' => 'LEFT',
				'conditions' => 'PedidoExame.codigo_funcionario = Funcionario.codigo',
			),
		);

		//retorna o array para executar
		return array('fields' => $fields, 'conditions' => $conditions, 'joins' => $joins);
	} //fim getDadosNfsExame

	/**
	 * [getDadosNfsExame description]
	 * 
	 * metodo para montar a query e gera os dados dos exames para consolidar
	 * 
	 * @return [type] [description]
	 */
	public function queryFieldsConditions($filtros, $tipo = null)
	{

		//monta as conditions
		$conditions = $this->converteFiltroEmCondition($filtros);

		//monta o que irá retornar do select
		$fields = array(
			// Pedido
			'PedidoExame.codigo                                                AS pedido_exame_codigo',                   // cod. pedido exame
			'PedidoExame.codigo_status_pedidos_exames                          AS pedido_exame_codigo_status',            // codigo status
			'StatusPedidoExame.descricao                                       AS pedido_exame_descricao_status',         // descricao status
			'CONVERT(VARCHAR, PedidoExame.data_solicitacao, 120)               AS pedido_exame_data_emissao',             // data de emissão ou data solicitação
			'PedidoExame.codigo_funcionario                                    AS funcionario_codigo',                    // codigo funcionário
			"CONVERT(VARCHAR,PedidoExame.data_inclusao, 120)                   AS pedido_exame_data_inclusao",
			'PedidoExameUsuarioInclusao.nome                                   AS pedido_exame_usuario_inclusao_nome',    // nome usuario incluiu pedido
			'PedidoExame.codigo_usuario_inclusao                               AS pedido_exame_codigo_usuario_inclusao',  // codigo usuário inclusão
			'PedidoExameUsuarioAlteracao.nome                                  AS pedido_exame_usuario_alteracao_nome',   // nome usuario alterou o pedido
			'PedidoExame.codigo_usuario_alteracao                              AS pedido_exame_codigo_usuario_alteracao', // codigo usuário alteração			
			// Pedido exame status
			"CASE
			WHEN PedidoExame.exame_admissional > 0 THEN 'Exame admissional'
			WHEN PedidoExame.exame_periodico > 0 THEN 'Exame periódico'
			WHEN PedidoExame.exame_demissional > 0 THEN 'Exame demissional'
			WHEN PedidoExame.exame_retorno > 0 THEN 'Retorno ao trabalho'
			WHEN PedidoExame.exame_mudanca > 0 THEN 'Mudança de riscos ocupacionais'
			WHEN PedidoExame.exame_monitoracao > 0 THEN 'Monitoração pontual'
			WHEN PedidoExame.pontual > 0 THEN 'Pontual'
			ELSE ''
			END                                                                AS pedido_exame_tipo_exame",
			// Consolidação	
			'ConsolidadoNfsExame.codigo                                        AS consolidado_nfs_exame_codigo',               // codigo transação consolidação
			'ConsolidadoNfsExame.codigo_nota_fiscal_servico                    AS consolidado_nfs_exame_codigo_nota_fiscal',   // codigo nota fiscal consolidada
			'ConsolidadoNfsExame.status                                        AS consolidado_nfs_exame_status',               // status consolidação
			'ConsolidadoNfsExame.data_vencimento                               AS consolidado_nfs_exame_data_vencimento',      // data vencimento consolidado
			'ConsolidadoNfsExame.data_pagamento                                AS consolidado_nfs_exame_data_pagamento',       // data pagamento consolidado
			'ConsolidadoNfsExame.ativo                                         AS consolidado_nfs_exame_ativo',                // situação do registro de consolidação
			'ConsolidadoNfsExameUsuarioInclusao.nome                           AS consolidado_nfs_exame_usuario_inclusao_nome',    // nome usuario consolida
			'ConsolidadoNfsExame.codigo_usuario_inclusao                       AS consolidado_nfs_exame_codigo_usuario_inclusao',  // codigo usuário inclusão
			'ConsolidadoNfsExameUsuarioAlteracao.nome                          AS consolidado_nfs_exame_usuario_alteracao_nome',   // nome usuario consolida
			'ConsolidadoNfsExame.codigo_usuario_alteracao                      AS consolidado_nfs_exame_codigo_usuario_alteracao', // codigo usuário alteração
			// Cliente
			'Cliente.codigo                                                    AS cliente_codigo',
			'Cliente.nome_fantasia                                             AS cliente_nome_fantasia',
			'Cliente.razao_social                                              AS cliente_razao_social',
			'Cliente.codigo_documento                                          AS cliente_cnpj',                     // documento cnpj cliente
			'Cliente.ativo                                                     AS cliente_ativo',                    // situação do cliente se ativo
			'Cliente.data_ativacao                                             AS cliente_data_ativacao',            // situação do cliente se ativo
			'Cliente.data_inativacao                                           AS cliente_data_inativacao',          // situação do cliente se ativo
			'ClienteUsuarioInclusao.nome                                       AS cliente_usuario_inclusao_nome',    // nome usuario incluiu pedido
			'Cliente.codigo_usuario_inclusao                                   AS cliente_codigo_usuario_inclusao',  // codigo usuário inclusão
			'ClienteUsuarioAlteracao.nome                                      AS cliente_usuario_alteracao_nome',   // nome usuario alterou o pedido
			'Cliente.codigo_usuario_alteracao                                  AS cliente_codigo_usuario_alteracao', // codigo usuário alteração			
			// Auditoria
			'AuditoriaExame.codigo                                             AS auditoria_codigo',      // codigo auditoria
			'AuditoriaExame.valor                                              AS auditoria_exame_valor', // valor de custo na auditoria
			'AuditoriaExame.motivo                                             AS auditoria_motivo',      // motivo na auditoria
			'AuditoriaExame.ativo                                              AS auditoria_ativo',       // situação da auditoria se ainda ativa
			"(CASE WHEN StatusAuditoriaExame.codigo IS NOT NULL THEN StatusAuditoriaExame.descricao else 'Pendente' END) AS auditoria_status",
			// Agendamento
			'CASE WHEN AgendamentoExame.codigo IS NOT NULL THEN \'Hora Marcada\' ELSE \'Ordem de Chegada\' END AS pedido_exame_tipo_agendamento',
			'AgendamentoExame.data                                             AS agendamento_data',       // Agendamento data
			'AgendamentoExame.hora                                             AS agendamento_hora',       // Agendamento hora
			"(CASE WHEN AgendamentoExame.data IS NOT NULL THEN CONCAT(AgendamentoExame.data,' ',AgendamentoExame.hora) 
                ELSE CONVERT(varchar(10), PedidoExame.data_inclusao, 20) END)  AS agendamento_exame_data_hora",
			'AgendamentoExame.ativo                                            AS agendamento_ativo',       // Agendamento ativo
			// Nota fiscal
			'NotaFiscalServico.data_vencimento                                 AS nota_fiscal_servico_data_vencimento',
			'NotaFiscalServico.data_pagamento                                  AS nota_fiscal_servico_data_pagamento',
			'NotaFiscalServico.numero_nota_fiscal                              AS nota_fiscal_servico_numero_nota_fiscal',  // numero nota fiscal
			// Exame
			'Exame.codigo                                                      AS exame_codigo',              // codigo exame
			'Exame.descricao                                                   AS exame_descricao',           // descrição do exame
			// Item Exame
			'ItemPedidoExame.codigo                                            AS item_pedido_exame_codigo',  // codigo exame x pedido
			'ItemPedidoExame.valor                                             AS item_pedido_exame_valor_venda',
			'ItemPedidoExame.valor_custo                                       AS item_pedido_exame_valor_compra',
			"(CASE  WHEN ItemPedidoExame.data_realizacao_exame IS NOT NULL THEN 'Realizado'
                    ELSE CASE WHEN ItemPedidoExame.compareceu = 0 THEN 'Não Compareceu'
                         ELSE 'Pendente' END 
             END)                                                              AS item_pedido_exame_status",
			// Baixa Item Exame
			"CONVERT(VARCHAR, ItemPedidoExameBaixa.data_realizacao_exame, 120) AS item_pedido_exame_baixa_data_realizacao_exame", // data realização ou data_resultado
			"CONVERT(VARCHAR, ItemPedidoExameBaixa.data_inclusao, 120)         AS item_pedido_exame_baixa_data_inclusao",  // data de inclusão ou considerada baixa
			'ItemPedidoExameBaixa.codigo_usuario_inclusao                      AS item_pedido_exame_baixa_codigo_usuario_inclusao', // codigo usuario baixa
			'ItemPedidoExameBaixaUsuarioInclusao.nome                          AS item_pedido_exame_baixa_usuario_inclusao_nome', // nome usuario responsavel baixa
			// Funcionario			
			'Funcionario.nome                                                  AS funcionario_nome',               // nome do funcionário
			'Funcionario.cpf                                                   AS funcionario_cpf',                // cpf do funcionário
			'Funcionario.data_nascimento                                       AS funcionario_data_nascimento',    // matricula do funcionário
			'ClienteFuncionario.codigo                                         AS cliente_funcionario_codigo',
			'ClienteFuncionario.matricula                                      AS cliente_funcionario_matricula',
			'ClienteFuncionario.ativo                                          AS cliente_funcionario_ativo',
			'CASE 
                WHEN ClienteFuncionario.ativo = 0 THEN \'Inativo\'
                WHEN ClienteFuncionario.ativo = 1 THEN \'Ativo\'
                WHEN ClienteFuncionario.ativo = 2 THEN \'Ferias\'
                WHEN ClienteFuncionario.ativo = 3 THEN \'Afastado\' 
                ELSE \'\' END                                                  AS cliente_funcionario_ativo_descricao',
			// Credenciado
			'Fornecedor.codigo                                                 AS fornecedor_codigo',              // codigo credenciado
			'Fornecedor.razao_social                                           AS fornecedor_razao_social',        // razão social credenciado
			'Fornecedor.codigo_documento                                       AS fornecedor_cnpj',                // cnpj credenciado
			'Fornecedor.nome                                                   AS fornecedor_nome_fantasia',       // nome fantasia credenciado
			'Fornecedor.ativo                                                  AS fornecedor_ativo',               // credenciado ativo no sistema
			// Endereco
			'FornecedorEndereco.logradouro                                     AS fornecedor_logradouro',
			'FornecedorEndereco.numero                                         AS fornecedor_numero',
			'FornecedorEndereco.complemento                                    AS fornecedor_complemento',
			'FornecedorEndereco.bairro                                         AS fornecedor_bairro',
			'FornecedorEndereco.cidade                                         AS fornecedor_cidade',
			'FornecedorEndereco.estado_descricao                               AS fornecedor_estado',
			'CAST(
				(SELECT CONCAT(codigo,\';\',numero_nota_fiscal,\'|\')
				FROM nota_fiscal_servico subNfs
				where subNfs.codigo_fornecedor = Fornecedor.codigo
					and subNfs.ativo = 1
					and subNfs.codigo_nota_fiscal_status IN (1,2,4)
				FOR XML  PATH(\'\')) AS text)                                  AS fornecedor_notas_fiscais'
		);

		//monta o join da query
		$joins = array(
			array(
				'table' => 'RHHealth.dbo.itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.itens_pedidos_exames_baixa',
				'alias' => 'ItemPedidoExameBaixa',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo = ItemPedidoExameBaixa.codigo_itens_pedidos_exames',
			),
			array(
				'table' => 'RHHealth.dbo.fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_fornecedor = Fornecedor.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.auditoria_exames',
				'alias' => 'AuditoriaExame',
				'type' => 'INNER',
				'conditions' => 'AuditoriaExame.codigo_pedido_exame = PedidoExame.codigo AND AuditoriaExame.codigo_item_pedido_exame = ItemPedidoExame.codigo AND AuditoriaExame.codigo_fornecedor = Fornecedor.codigo AND AuditoriaExame.codigo_exame = Exame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = PedidoExame.codigo_cliente',
			),
			array(
				'table' => 'Rhhealth.dbo.cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario',
			),
			array(
				'table' => 'RHHealth.dbo.consolidado_nfs_exame',
				'alias' => 'ConsolidadoNfsExame',
				'type' => 'LEFT',
				'conditions' => 'ConsolidadoNfsExame.codigo_item_pedido_exame = ItemPedidoExame.codigo AND ConsolidadoNfsExame.codigo_pedido_exame = PedidoExame.codigo AND ConsolidadoNfsExame.codigo_fornecedor = Fornecedor.codigo AND ConsolidadoNfsExame.codigo_exame = Exame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.nota_fiscal_servico',
				'alias' => 'NotaFiscalServico',
				'type' => 'LEFT',
				'conditions' => 'NotaFiscalServico.codigo = ConsolidadoNfsExame.codigo_nota_fiscal_servico',
			),
			array(
				'table' => 'RHHealth.dbo.funcionarios',
				'alias' => 'Funcionario',
				'type' => 'LEFT',
				'conditions' => 'PedidoExame.codigo_funcionario = Funcionario.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.status_pedidos_exames',
				'alias' => 'StatusPedidoExame',
				'type' => 'LEFT',
				'conditions' => 'StatusPedidoExame.codigo = PedidoExame.codigo_status_pedidos_exames'
			),
			array(
				'table' => 'RHHealth.dbo.status_auditoria_exames',
				'alias' => 'StatusAuditoriaExame',
				'type' => 'LEFT',
				'conditions' => 'StatusAuditoriaExame.codigo = AuditoriaExame.codigo_status_auditoria_exames',
			),
			array(
				'table' => 'RHHealth.dbo.usuario',
				'alias' => 'ConsolidadoNfsExameUsuarioAlteracao',
				'type' => 'LEFT',
				'conditions' => 'ConsolidadoNfsExame.codigo_usuario_alteracao = ConsolidadoNfsExameUsuarioAlteracao.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.usuario',
				'alias' => 'ConsolidadoNfsExameUsuarioInclusao',
				'type' => 'LEFT',
				'conditions' => 'ConsolidadoNfsExame.codigo_usuario_alteracao = ConsolidadoNfsExameUsuarioAlteracao.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.usuario',
				'alias' => 'PedidoExameUsuarioInclusao',
				'type' => 'LEFT',
				'conditions' => 'PedidoExame.codigo_usuario_inclusao = PedidoExameUsuarioInclusao.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.usuario',
				'alias' => 'PedidoExameUsuarioAlteracao',
				'type' => 'LEFT',
				'conditions' => 'PedidoExame.codigo_usuario_alteracao = PedidoExameUsuarioAlteracao.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.usuario',
				'alias' => 'ClienteUsuarioInclusao',
				'type' => 'LEFT',
				'conditions' => 'Cliente.codigo_usuario_inclusao = ClienteUsuarioInclusao.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.usuario',
				'alias' => 'ClienteUsuarioAlteracao',
				'type' => 'LEFT',
				'conditions' => 'Cliente.codigo_usuario_alteracao = ClienteUsuarioAlteracao.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.usuario',
				'alias' => 'ItemPedidoExameBaixaUsuarioInclusao',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExameBaixa.codigo_usuario_inclusao = ItemPedidoExameBaixaUsuarioInclusao.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.usuario',
				'alias' => 'ItemPedidoExameBaixaUsuarioAlteracao',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExameBaixa.codigo_usuario_alteracao = ItemPedidoExameBaixaUsuarioAlteracao.codigo',
			),
			array(
				'table' => 'Rhhealth.dbo.fornecedores_endereco',
				'alias' => 'FornecedorEndereco',
				'type' => 'LEFT',
				'conditions' => 'FornecedorEndereco.codigo_fornecedor = Fornecedor.codigo',
			),
			array(
				'table' => 'Rhhealth.dbo.agendamento_exames',
				'alias' => 'AgendamentoExame',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExame.codigo = AgendamentoExame.codigo_itens_pedidos_exames',
			),
		);

		$order = 'Fornecedor.codigo';

		//retorna o array para executar
		return array(
			'fields' => $fields,
			'conditions' => $conditions,
			'joins' => $joins,
			'order' => $order,
			'limit' => 50,
		);
	} //fim getDadosNfsExame

	/**
	 * [getDadosConsolidadoNfs description]
	 * 
	 * metodo para pegar os dados consolidado e os dados da nota fiscal
	 * 
	 * @return [type] [description]
	 */
	public function getDadosConsolidadoNfs($codigo_item_pedido_exame, $codigo_nota_fiscal_servico)
	{


		$this->bindModel(
			array(
				'belongsTo' => array(
					'NotaFiscalServico' => array(
						'class' => 'NotaFiscalServico',
						'foreignKey' => 'codigo_nota_fiscal_servico'
					)
				)
			)
		);

		//pega os dados consolidade e nota fiscal
		$dados = $this->find('first', array('conditions' => array('codigo_item_pedido_exame' => $codigo_item_pedido_exame, 'codigo_nota_fiscal_servico' => $codigo_nota_fiscal_servico)));

		return $dados;
	} //fim getDadosConsolidadoNfs

	public function obterItemConsolidado($fields = array(), $conditions = array())
	{

		$_fields = array('codigo', 'codigo_nota_fiscal_servico', 'codigo_fornecedor', 'codigo_pedido_exame');

		$fields = (is_array($fields) && count(($fields) > 0)) ? array_merge($_fields, $fields) : $_fields;

		return $this->find(
			'first',
			array(
				'fields' => $fields,
				'conditions' => $conditions
			)
		);
	}

	/**
	 * Atualizando uma consolidação entre nota fiscal e item pedido exame
	 *
	 * @param array $arrConsolidacao
	 * @return boolean
	 * @return Exception
	 */
	public function atualizarConsolidacao($arrConsolidacao)
	{

		$codigo = isset($arrConsolidacao['codigo']) ? Comum::codigoParamValidator($arrConsolidacao['codigo']) : null;

		if (is_null($codigo)) {
			throw new Exception("Não foi possível encontrar o código para atualizar esta consolidação");
		}

		$codigo_nota_fiscal_servico = isset($arrConsolidacao['codigo_nota_fiscal_servico']) ? Comum::codigoParamValidator($arrConsolidacao['codigo_nota_fiscal_servico']) : null;

		if (is_null($codigo_nota_fiscal_servico)) {
			throw new Exception("Não foi possível encontrar Nota fiscal para atualizar esta consolidação");
		}

		// prepara os dados para atualizar consolidado
		$atualizarDados = array(
			'ConsolidadoNfsExame' => $arrConsolidacao
		);

		// atualiza os dados
		if (!$this->atualizar($atualizarDados)) {
			throw new Exception("Não foi possível atualizar esta consolidação");
		}

		// atualiza a tabela de nota fiscal
		$NotaFiscalServicoDados = array(
			'NotaFiscalServico' => array(
				'codigo' => $codigo_nota_fiscal_servico,
				'codigo_nota_fiscal_status' => NotaFiscalStatus::EM_ANALISE
			)
		);

		$this->NotaFiscalServico = &ClassRegistry::init('NotaFiscalServico');

		if (!$this->NotaFiscalServico->atualizar($NotaFiscalServicoDados)) {
			throw new Exception(sprintf('Não foi possível atualizar esta consolidação com a NFs: "%s"', $codigo_nota_fiscal_servico));
		}

		return true;
	}

	/**
	 * Incluindo uma consolidação entre nota fiscal e item pedido exame
	 *
	 * @param array $arrConsolidacao
	 * @return boolean
	 * @return Exception
	 */
	public function incluirConsolidacao($arrConsolidacao)
	{

		$codigo_nota_fiscal_servico = isset($arrConsolidacao['codigo_nota_fiscal_servico']) ? Comum::codigoParamValidator($arrConsolidacao['codigo_nota_fiscal_servico']) : null;

		if (is_null($codigo_nota_fiscal_servico)) {
			throw new Exception("Não foi possível encontrar Nota fiscal para atualizar esta consolidação");
		}

		$codigo_credenciado = isset($arrConsolidacao['codigo_fornecedor']) ? Comum::codigoParamValidator($arrConsolidacao['codigo_fornecedor']) : null;

		if (is_null($codigo_credenciado)) {
			throw new Exception("Não foi possível encontrar Credenciado para atualizar esta consolidação");
		}

		$codigo_item_pedido_exame = isset($arrConsolidacao['codigo_item_pedido_exame']) ? Comum::codigoParamValidator($arrConsolidacao['codigo_item_pedido_exame']) : null;

		if (is_null($codigo_item_pedido_exame)) {
			throw new Exception("Não foi possível encontrar Item Pedido Exame para atualizar esta consolidação");
		}

		$data_pagamento = isset($arrConsolidacao['data_pagamento'])
			&& !empty($arrConsolidacao['data_pagamento']) ? AppModel::dateToDbDate($arrConsolidacao['data_pagamento']) : null;

		$data_vencimento = isset($arrConsolidacao['data_vencimento'])
			&& !empty($arrConsolidacao['data_vencimento']) ? AppModel::dateToDbDate($arrConsolidacao['data_vencimento']) : null;

		// gera a query para pegar os dados dos exames do fornecedor
		$dados_query = $this->getDadosNfsExame(array('codigo_item_pedido_exame' => $codigo_item_pedido_exame));

		// recupera os dados da consulta
		$this->PedidoExame = &ClassRegistry::init('PedidoExame');

		$pedidoDados = $this->PedidoExame->find(
			'first',
			array(
				'fields' => $dados_query['fields'],
				'conditions' => $dados_query['conditions'],
				'joins' => $dados_query['joins'],
				'recursive' => -1
			)
		);

		// prepara dados para consolidar
		$consolidarDados = array(
			'ConsolidadoNfsExame' => array(
				'codigo_empresa' => 1,
				'codigo_nota_fiscal_servico' => $codigo_nota_fiscal_servico,
				'codigo_fornecedor' => $codigo_credenciado,
				'codigo_item_pedido_exame' => $codigo_item_pedido_exame,
				'codigo_exame' => $pedidoDados[0]['codigo_exame'],
				'codigo_pedido_exame' => $pedidoDados[0]['codigo_pedido_exame'],
				'valor' => $pedidoDados[0]['valor_custo'],
				'data_vencimento' => $data_vencimento,
				'data_pagamento' => $data_pagamento,
				'status' => ConsolidadoNfsExame::PENDENTE,
				'ativo' => 1
			)
		);

		// prepara atualização da tabela de nota fiscal para status 2 - Em Analise                   
		$NotaFiscalServicoDados = array(
			'NotaFiscalServico' => array(
				'codigo' => $codigo_nota_fiscal_servico,
				'codigo_nota_fiscal_status' => NotaFiscalStatus::EM_ANALISE
			)
		);

		try {
			$this->query('BEGIN TRANSACTION');

			// incluir o dado de consolidado
			if (!$this->incluir($consolidarDados)) {
				throw new Exception("Erro ao incluir dados consolidado");
			}

			// atualiza nota fiscal
			$this->NotaFiscalServico = &ClassRegistry::init('NotaFiscalServico');

			if (!$this->NotaFiscalServico->atualizar($NotaFiscalServicoDados)) {
				throw new Exception(sprintf('Não foi possível atualizar esta consolidação com a NFs: "%s"', $codigo_nota_fiscal_servico));
			}

			$this->commit();
		} catch (\Exception $ex) {
			$this->rollback();
			throw new Exception($ex->getMessage());
		}

		return true;
	}

	/**
	 * Excluindo uma consolidação entre nota fiscal e item pedido exame
	 *
	 * @param array $arrConsolidacao
	 * @return boolean
	 * @return Exception
	 */
	public function excluirConsolidacao($arrConsolidacao)
	{

		$codigo = isset($arrConsolidacao['codigo']) ? Comum::codigoParamValidator($arrConsolidacao['codigo']) : null;

		if (is_null($codigo)) {
			throw new Exception(" Não foi possível encontrar o código para excluir esta consolidação");
		}

		$codigo_nota_fiscal_servico = isset($arrConsolidacao['codigo_nota_fiscal_servico']) ? Comum::codigoParamValidator($arrConsolidacao['codigo_nota_fiscal_servico']) : null;

		if (is_null($codigo_nota_fiscal_servico)) {
			throw new Exception("Não foi possível encontrar Nota fiscal para atualizar status");
		}

		try {
			$this->query('BEGIN TRANSACTION');

			if (!$this->excluir($codigo)) {
				throw new Exception(sprintf('Excluir Consolidação - Não foi possível excluir o código: "%s"', $codigo));
			}

			// atualiza a tabela de nota fiscal
			$NotaFiscalServicoDados = array(
				'NotaFiscalServico' => array(
					'codigo' => $codigo_nota_fiscal_servico,
					'codigo_nota_fiscal_status' => NotaFiscalStatus::EM_ANALISE // retorna para processamento parcial para que seja listado novamente
				)
			);

			$this->NotaFiscalServico = &ClassRegistry::init('NotaFiscalServico');

			if (!$this->NotaFiscalServico->atualizar($NotaFiscalServicoDados)) {
				throw new Exception(sprintf('Excluir Consolidação - Não foi possível atualizar a NF de código: "%s"', $codigo_nota_fiscal_servico));
			}

			$this->commit();
		} catch (\Exception $ex) {
			$this->rollback();
			throw new Exception($ex->getMessage());
		}

		return true;
	}

	/**
	 * Obtém vários exames de um Credenciado para tratar a consolidação
	 * onde:
	 * - não foram consolidadas
	 * - não tenha uma nota fiscal já associada
	 *
	 * @param int $codigo_credenciado
	 * @return bool | throw
	 */
	public function obterExamesParaConsolidacaoPorCredenciado($codigo_credenciado = null)
	{

		$codigo_credenciado = Comum::codigoParamValidator($codigo_credenciado);

		if (is_null($codigo_credenciado)) {
			throw new Exception("Não foi possível encontrar Código Credenciado e obter exames");
		}

		// gera a query para pegar os dados dos exames do fornecedor
		$dados_query = $this->getDadosNfsExame(array('codigo_fornecedor' => $codigo_credenciado));

		// recupera os dados da consulta
		$this->PedidoExame = &ClassRegistry::init('PedidoExame');

		$dados_query['conditions'] = array_merge(
			$dados_query['conditions'],
			array(
				'ConsolidadoNfsExame.status NOT IN (1)',       // Não deve ter sido consolidado(status=1)
				'NotaFiscalServico.numero_nota_fiscal IS NULL' // não deveria conter numero de nota já associada
			)
		);

		$pedidoDados = $this->PedidoExame->find(
			'all',
			array(
				'fields' => $dados_query['fields'],
				'conditions' => $dados_query['conditions'],
				'joins' => $dados_query['joins'],
				'recursive' => -1
			)
		);

		if (is_array($pedidoDados) && count($pedidoDados) > 0) {

			$new = array();

			foreach ($pedidoDados as $key => $value) {
				$new[] = $value[0];
			}
			$pedidoDados = $new;
		}

		return $pedidoDados;
	}

	/**
	 * Atualiza vários exames para um Credenciado com uma única Nota Fiscal
	 *
	 * @param int $codigo_credenciado
	 * @param int $codigo_nota_fiscal_servico
	 * @return bool | throw
	 */
	public function atualizarExamesParaConsolidacaoPorCredenciado($codigo_credenciado = null, $codigo_nota_fiscal_servico = null)
	{


		$codigo_credenciado = Comum::codigoParamValidator($codigo_credenciado);

		if (is_null($codigo_credenciado)) {
			throw new Exception("Não foi possível encontrar Código Credenciado e atualizar exames");
		}

		$codigo_nota_fiscal_servico = Comum::codigoParamValidator($codigo_nota_fiscal_servico);

		if (is_null($codigo_nota_fiscal_servico)) {
			throw new Exception("Não foi possível encontrar Código Nota Fiscal e atualizar exames");
		}

		$examesDados = $this->obterExamesParaConsolidacaoPorCredenciado($codigo_credenciado);

		foreach ($examesDados as $key => $value) {

			$consolidacaoDados = array(
				'codigo_nota_fiscal_servico' => $codigo_nota_fiscal_servico,
				'codigo_fornecedor' => $codigo_credenciado,
				'codigo_item_pedido_exame' => $value['codigo_item_pedido_exame'],
				'data_vencimento' => $value['data_vencimento_nfs'],
				'data_pagamento' => $value['data_pagamento_nfs'],
			);

			try {

				$this->incluirConsolidacao($consolidacaoDados);
			} catch (\Exception $e) {
				throw new Exception($e->getMessage());
			}
		}

		return true;
	}

	public function getNotasFiscaisConsolidadas($filtros)
	{
		$conditions = $this->converteFiltroEmConditionConsolidacao($filtros);

		$fields = array(
			//ConsolidadoNfsExame
			'DISTINCT NotaFiscalServico.codigo as codigo_nf',
			//NotaFiscalServico
			'NotaFiscalServico.numero_nota_fiscal as numero_nf',
			'NotaFiscalServico.valor as valor_nota',
			'NotaFiscalServico.data_vencimento as data_vencimento',
			'NotaFiscalServico.codigo_nota_fiscal_status as status_nota',
			//Fornecedor
			'Fornecedor.codigo as codigo_credenciado',
			'Fornecedor.razao_social as razao_credenciado',
			'Fornecedor.codigo_documento as cnpj_credenciado',
			'Fornecedor.nome as nome_credenciado',
		);
		$joins = array(
			array(
				'table' => 'RHHealth.dbo.nota_fiscal_servico',
				'alias' => 'NotaFiscalServico',
				'type' => 'LEFT',
				'conditions' => 'NotaFiscalServico.codigo = ConsolidadoNfsExame.codigo_nota_fiscal_servico',
			),
			array(
				'table' => 'RHHealth.dbo.pedidos_exames',
				'alias' => 'PedidoExame',
				'type' => 'LEFT',
				'conditions' => 'PedidoExame.codigo = ConsolidadoNfsExame.codigo_pedido_exame',
			),
			array(
				'table' => 'RHHealth.dbo.itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => 'NotaFiscalServico.codigo_fornecedor = Fornecedor.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.funcionarios',
				'alias' => 'Funcionario',
				'type' => 'LEFT',
				'conditions' => 'PedidoExame.codigo_funcionario = Funcionario.codigo',
			),
		);
		$order = 'NotaFiscalServico.numero_nota_fiscal';

		//retorna o array para executar
		return array(
			'fields' => $fields,
			'conditions' => $conditions,
			'joins' => $joins,
			'order' => $order,
		);
	}

	public function converteFiltroEmConditionConsolidacao($filtros)
	{
		$conditions = array();

		if (!empty($filtros["data_inicio"])) {
			$data_inicio = AppModel::dateToDbDate($filtros["data_inicio"] . ' 00:00:00');
			$data_fim = AppModel::dateToDbDate($filtros["data_fim"] . ' 23:59:59');
			$conditions[] = "(NotaFiscalServico.data_inclusao >= '" . $data_inicio . "'";
		}

		if (!empty($filtros["data_fim"])) {
			$conditions[] = "NotaFiscalServico.data_inclusao <= '" . $data_fim . "')";
		}

		if (!empty($filtros['codigo_fornecedor'])) {
			$conditions['Fornecedor.codigo'] = $filtros['codigo_fornecedor'];
		}

		if (!empty($filtros['cnpj_fornecedor'])) {
			$conditions['Fornecedor.codigo_documento'] = Comum::soNumero($filtros['cnpj_fornecedor']);
		}

		if (!empty($filtros['numero_nota_fiscal'])) {
			$conditions['NotaFiscalServico.numero_nota_fiscal'] = $filtros['numero_nota_fiscal'];
		}

		if (!empty($filtros['razao_social'])) {
			$conditions['Fornecedor.razao_social LIKE'] = '%' . $filtros['razao_social'] . '%';
		}

		if (!empty($filtros['nome_fantasia'])) {
			$conditions['Fornecedor.nome LIKE'] = '%' . $filtros['nome_fantasia'] . '%';
		}

		if (!empty($filtros['concluida'])) {
			if ($filtros['concluida'] == 1) {
				$conditions['NotaFiscalServico.codigo_nota_fiscal_status'] = 5;
			} else {
				$conditions['NotaFiscalServico.codigo_nota_fiscal_status'] = array(1, 2, 4);
			}
		}


		if (!empty($filtros['codigo_pedido_exame'])) {
			$conditions['ConsolidadoNfsExame.codigo_pedido_exame'] = $filtros['codigo_pedido_exame'];
		}

		if (!empty($filtros['consolidado'])) {
			$conditions['ConsolidadoNfsExame.status'] = $filtros['consolidado'];
		}

		if (!empty($filtros['funcionario'])) {
			$conditions['Funcionario.nome LIKE'] = '%' . $filtros['funcionario'] . '%';
		}

		if (!empty($filtros['cpf_funcionario'])) {
			$conditions['Funcionario.cpf'] = Comum::soNumero($filtros['cpf_funcionario']);
		}

		return $conditions;
	}

	public function getExamesNaoConsolidados($filtros, $tipo = null)
	{
		//monta as conditions
		$conditions = $this->converteFiltroEmCondition($filtros);

		//monta o que irá retornar do select
		$fields = array(
			'ItemPedidoExame.codigo as codigo_item_pedido_exame',
			"CONVERT(VARCHAR, ItemPedidoExameBaixa.data_realizacao_exame, 120) AS data_realizacao", // data realização ou data_resultado
			"CONVERT(VARCHAR, ItemPedidoExameBaixa.data_inclusao, 120)  AS data_baixa",  // data de inclusão ou considerada baixa
			'ConsolidadoNfsExame.codigo as codigo_consolidado_nfs_exame',
			'ConsolidadoNfsExame.status as status_consolidado',
			'PedidoExame.codigo as codigo_pedido_exame',
			'Exame.descricao as exame',
			'Fornecedor.codigo as codigo_credenciado',
			'Cliente.codigo as codigo_cliente',
			'Cliente.razao_social as nome_cliente',
			'AuditoriaExame.valor as valor_custo',
			'AuditoriaExame.numero_nota_fiscal as numero_nota_fiscal',
			'AuditoriaExame.recebimento_fisico as recebimento_fisico',
			'NotaFiscalServico.data_vencimento as data_vencimento_nfs',
			'NotaFiscalServico.data_pagamento as data_pagamento_nfs',
			'ConsolidadoNfsExame.data_vencimento as data_vencimento_cne',
			'ConsolidadoNfsExame.data_pagamento as data_pagamento_cne',
			'Exame.codigo as codigo_exame',
			'Funcionario.nome as funcionario_nome',
			'Funcionario.cpf as funcionario_cpf',
			'Fornecedor.razao_social as credenciado_razao_social',
			'Fornecedor.codigo_documento as credenciado_cnpj',
			'Fornecedor.nome as credenciado_nome_fantasia',
			'CAST(
				(SELECT CONCAT(codigo,\';\',numero_nota_fiscal,\'|\')
				FROM nota_fiscal_servico subNfs
				where subNfs.codigo_fornecedor = Fornecedor.codigo
					and subNfs.ativo = 1
					and subNfs.codigo_nota_fiscal_status IN (1,2,4)
				FOR XML  PATH(\'\')) AS text)                                  AS fornecedor_notas_fiscais'
		);

		//monta o join da query
		$joins = array(
			array(
				'table' => 'RHHealth.dbo.pedidos_exames',
				'alias' => 'PedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.itens_pedidos_exames_baixa',
				'alias' => 'ItemPedidoExameBaixa',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo = ItemPedidoExameBaixa.codigo_itens_pedidos_exames',
			),
			array(
				'table' => 'RHHealth.dbo.fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_fornecedor = Fornecedor.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.auditoria_exames',
				'alias' => 'AuditoriaExame',
				'type' => 'INNER',
				'conditions' => 'AuditoriaExame.codigo_pedido_exame = PedidoExame.codigo AND AuditoriaExame.codigo_item_pedido_exame = ItemPedidoExame.codigo AND AuditoriaExame.codigo_fornecedor = Fornecedor.codigo AND AuditoriaExame.codigo_exame = Exame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = PedidoExame.codigo_cliente',
			),
			array(
				'table' => 'RHHealth.dbo.consolidado_nfs_exame',
				'alias' => 'ConsolidadoNfsExame',
				'type' => 'LEFT',
				'conditions' => 'ConsolidadoNfsExame.codigo_item_pedido_exame = ItemPedidoExame.codigo AND ConsolidadoNfsExame.codigo_pedido_exame = PedidoExame.codigo AND ConsolidadoNfsExame.codigo_fornecedor = Fornecedor.codigo AND ConsolidadoNfsExame.codigo_exame = Exame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.nota_fiscal_servico',
				'alias' => 'NotaFiscalServico',
				'type' => 'LEFT',
				'conditions' => 'NotaFiscalServico.codigo = ConsolidadoNfsExame.codigo_nota_fiscal_servico',
			),
			array(
				'table' => 'RHHealth.dbo.funcionarios',
				'alias' => 'Funcionario',
				'type' => 'LEFT',
				'conditions' => 'PedidoExame.codigo_funcionario = Funcionario.codigo',
			),
		);

		//retorna o array para executar
		return array('fields' => $fields, 'conditions' => $conditions, 'joins' => $joins);
	}

	public function validaReverteExame($codigos_item_pedido_exame)
	{

		$dados = array();

		$exames = implode(',', $codigos_item_pedido_exame);

		$options['fields'] = array(
			'Exames.descricao',
			'NotasFiscaisServico.codigo',
			'ConsolidadoNfsExame.codigo_item_pedido_exame'
		);

		$options['joins'] = array(
			array(
				"table"      => "RHHealth.dbo.nota_fiscal_servico",
				"alias"      => "NotasFiscaisServico",
				"type"       => "INNER",
				"conditions" => "ConsolidadoNfsExame.codigo_nota_fiscal_servico = NotasFiscaisServico.codigo"
			),
			array(
				"table"      => "RHHealth.dbo.exames",
				"alias"      => "Exames",
				"type"       => "INNER",
				"conditions" => "ConsolidadoNfsExame.codigo_exame = Exames.codigo"
			)
		);


		$options['conditions'] =
			array(
				'NotasFiscaisServico.codigo_nota_fiscal_status' => 5,
				'ConsolidadoNfsExame.status' => 1,
			);

		$options['conditions'][] = 'ConsolidadoNfsExame.codigo_item_pedido_exame in (' . $exames . ')';

		$retorno = $this->find('all', $options);

		return $retorno;
	}
}
