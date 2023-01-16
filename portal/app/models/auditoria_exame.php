<?php
class AuditoriaExame extends AppModel {
	public $name = 'AuditoriaExame';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'auditoria_exames';
	public $primaryKey = 'codigo';	
	public $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_auditoria_exames'), 'Containable');

	//metodo para converter os dados do filtro em condições
	public function converteFiltroEmCondition($filtros)
	{
		$conditions = array();
		if(!empty($filtros['codigo'])) {
			$conditions[]['OR'] = array('Fornecedor.codigo' => $filtros['codigo'], 'Fornecedor.codigo_fornecedor_recebedor' => $filtros['codigo']);
		}

		if(!empty($filtros['codigo_fornecedor'])) {
			$conditions['Fornecedor.codigo'] = $filtros['codigo_fornecedor'];
		} else {
			if (!empty($filtros['codigo_documento_fornecedor'])) {
				$conditions['Fornecedor.codigo_documento LIKE'] = '%'.$filtros['codigo_documento_fornecedor'].'%';
			}
			
			if (!empty($filtros['razao_social_fornecedor'])) {
				$conditions['Fornecedor.razao_social LIKE'] = '%'.$filtros['razao_social_fornecedor'].'%';
			}
	
			if (!empty($filtros['nome_fantasia_fornecedor'])) {
				$conditions['Fornecedor.nome LIKE'] = '%'.$filtros['nome_fantasia_fornecedor'].'%';
			}
		}

		if(!empty($filtros['codigo_pedido_exame'])) {
			$conditions['PedidoExame.codigo'] = $filtros['codigo_pedido_exame'];
		}

		if(!empty($filtros['nota_fiscal'])) {
			$conditions['NotaFiscalServico.numero_nota_fiscal'] = $filtros['nota_fiscal'];
			
		}

		if(!empty($filtros['codigo_item_pedido_exame'])) {
			$conditions['ItemPedidoExame.codigo'] = $filtros['codigo_item_pedido_exame'];
		}

		if(isset($filtros['status']) && !empty($filtros['status'])) {
			if($filtros['status'] > '1') {
				$conditions['AuditoriaExame.codigo_status_auditoria_imagem'] = $filtros['status'];
			}
			else {
				$conditions['OR'] = array('AuditoriaExame.codigo_status_auditoria_imagem' => $filtros['status'],'AuditoriaExame.codigo_status_auditoria_imagem IS NULL');
			}
		}

		if(!empty($filtros['codigo_cliente'])) {
			$conditions['PedidoExame.codigo_cliente'] = $filtros['codigo_cliente'];
		} else {
			if (!empty($filtros['razao_social_cliente'])) {
				$conditions['Cliente.razao_social LIKE'] = '%'.$filtros['razao_social_cliente'].'%';
			}
	
			if (!empty($filtros['nome_fantasia_cliente'])) {
				$conditions['Cliente.nome_fantasia LIKE'] = '%'.$filtros['nome_fantasia_cliente'].'%';
			}
			
			if (!empty($filtros['codigo_documento_cliente'])) {
				$conditions['Cliente.codigo_documento LIKE'] = '%'.$filtros['codigo_documento_cliente'].'%';
			}
		}

		if(!empty($filtros["data_baixa"])) {
			$conditions['ItemPedidoExameBaixa.data_inclusao >='] = AppModel::dateToDbDate2($filtros['data_baixa'].' 00:00:00' );
			$conditions['ItemPedidoExameBaixa.data_inclusao <='] = AppModel::dateToDbDate2($filtros['data_baixa'].' 23:59:59' );
        }
		
		if (!empty($filtros['nome_usuario_baixa'])) {
            $conditions['UsuarioBaixa.nome LIKE'] = '%'.$filtros['nome_usuario_baixa'].'%';
		}
		
		if(!empty($filtros["data_realizacao"])) {
			$conditions['ItemPedidoExameBaixa.data_realizacao_exame'] = Comum::formataData($filtros['data_realizacao'],'dmy','ymd' );
		}

		if (!empty($filtros['nome_funcionario'])) {
            $conditions['Funcionario.nome LIKE'] = '%'.$filtros['nome_funcionario'].'%';
        }
		
		if (!empty($filtros['cpf'])) {
            $conditions['Funcionario.cpf LIKE'] = '%'.Comum::soNumero($filtros['cpf']).'%';
		}
		
		if (!empty($filtros['prestador_qualificado'])) {
			
			switch ($filtros["prestador_qualificado"]) {
                case 'S':
                    $conditions['Fornecedor.prestador_qualificado'] = 1;
                break;
                case 'N':
                    $conditions['Fornecedor.prestador_qualificado'] = 0;
                break;
            }
		
		}
		
		if (!empty($filtros['tipo_exame'])) {
            $conditions['TiposExame.codigo'] = $filtros['tipo_exame'];
		}

		if(!empty($filtros["data_inicio"])) {
			$data_inicio = AppModel::dateToDbDate($filtros["data_inicio"].' 00:00:00');
			$data_fim = AppModel::dateToDbDate($filtros["data_fim"].' 23:59:59');
			switch ($filtros["tipo_periodo"]) {
				case 'B'://data de baixa
					$conditions['ItemPedidoExameBaixa.data_inclusao >= '] = $data_inicio;	
					break;
				case 'R'://data de resultado
					$conditions['ItemPedidoExameBaixa.data_realizacao_exame >= '] = $data_inicio;	
					break;
			}//switch
				
		}


		if(!empty($filtros["data_fim"])) {
			switch ($filtros["tipo_periodo"]) {
				case 'B'://data de baixa
					$conditions['ItemPedidoExameBaixa.data_inclusao <= '] = $data_fim;	
					break;
				case 'R'://data de resultado
					$conditions['ItemPedidoExameBaixa.data_realizacao_exame <= '] = $data_fim;	
					break;
			}//switch
		}

        if(!empty($filtros['tipo_usuario'])){

            $codigo_tipo_perfil_interno = 5;

            switch ($filtros["tipo_usuario"]) {
                case 'I':
                    $conditions['TipoPerfil.codigo'] = $codigo_tipo_perfil_interno;
                break;
                case 'E':
                    $conditions [] = "TipoPerfil.codigo != '". $codigo_tipo_perfil_interno . "'";
                break;
            }
        }

		return $conditions;

	}//fim converteFiltroEmCondition($filtros)

	/**
	 * [getDadosFornecedorExame description]
	 * 
	 * metodo para montar a query e gera os dados dos exames, pedidos do fornecedor escolhido
	 * 
	 * @return [type] [description]
	 */
	public function getDadosFornecedorExame($filtros,$tipo=null)
	{

		//monta as conditions
		$conditions = $this->converteFiltroEmCondition($filtros);

		// $conditions[] = array('AnexoExame.codigo IS NOT NULL');

		//monta o que irá retornar do select
		$fields = array(
			'Fornecedor.codigo as codigo_fornecedor',
			'Fornecedor.nome AS fornecedor_nome',
			'Fornecedor.codigo_documento as fornecedor_cnpj',
			'Fornecedor.codigo_fornecedor_fiscal as fornecedor_codigo_o',
			'Fornecedor.tipo_unidade as fornecedor_tipo_unidade',
			'Cliente.codigo AS codigo_cliente',
			'Cliente.nome_fantasia AS cliente_nome',
			'Cliente.codigo_documento as cliente_cnpj',
			'Funcionario.nome AS funcionario_nome',
			'Funcionario.cpf as funcionario_cpf',
			"(CASE WHEN StatusAuditoriaImagem.codigo IS NOT NULL THEN StatusAuditoriaImagem.descricao else 'Pendente' END) AS status",
			'PedidoExame.codigo AS codigo_pedido_exame',
			'Exame.codigo AS codigo_exame',
			'Exame.descricao AS exame',
			'CONVERT(VARCHAR, ItemPedidoExameBaixa.data_inclusao, 103) AS data_baixa',
			'ItemPedidoExame.codigo',
			'ItemPedidoExame.valor_custo as valor',
			'ItemPedidoExame.codigo as codigo_item_pedido_exame',
			'AnexoExame.codigo AS codigo_anexo_exame',
			'AnexoExame.caminho_arquivo AS caminho_arquivo_exame',
			'AnexoExame.data_inclusao AS data_inclusao_anexo',
			'AnexoFichaClinica.codigo AS codigo_anexo_ficha_clinica',
			'AnexoFichaClinica.caminho_arquivo AS caminho_arquivo_ficha_clinica',
			'AnexoFichaClinica.data_inclusao AS data_inclusao_ficha',
			'AuditoriaExame.motivo as motivo',
			'StatusAuditoriaExame.codigo as codigo_status_auditoria',
			'StatusAuditoriaImagem.codigo as codigo_status_imagem',
			'AuditoriaExame.codigo_status_auditoria_imagem as codigo_status_auditoria_imagem',
			'AuditoriaExame.codigo_motivos_aprovado_ajuste as codigo_motivos_aprovado_ajuste',
			'Usuario.nome as auditoria_usuario_nome',
			'AuditoriaExame.data_alteracao as auditoria_data',
			'AuditoriaExame.codigo_nota_fiscal_servico as codigo_nota_fiscal',
			'AuditoriaExame.recebimento_fisico as recebimento_fisico',
			'AuditoriaExame.motivo as auditoria_motivo',
			'AuditoriaExame.libera_anexo_exame as libera_anexo_exame',
			'AuditoriaExame.libera_anexo_ficha as libera_anexo_ficha',
			//"(CASE WHEN StatusAuditoriaExame.codigo IN (2,3) THEN AuditoriaExame.codigo_usuario_alteracao ELSE '' END) AS auditoria_usuario_nome",
			//"(CASE WHEN StatusAuditoriaExame.codigo = 2 OR StatusAuditoriaExame.codigo = 3 THEN AuditoriaExame.data_alteracao ELSE '' END) AS auditoria_data",
			'NotaFiscalServico.numero_nota_fiscal as nota_fiscal',
			'NotaFiscalServico.data_vencimento as data_vencimento_nfs',
			'NotaFiscalServico.codigo_nota_fiscal_status as nota_fiscal_status',
			'NotaFiscalServico.data_pagamento as data_pagamento_nfs',
			'Fornecedor.prestador_qualificado as prestador_qualificado',
			'ItemPedidoExameBaixa.codigo_usuario_inclusao as codigo_usuario_inclusao_baixa',
			'UsuarioBaixa.nome as usuario_baixa',
			'ItemPedidoExame.data_realizacao_exame as data_realizacao_exame',
			'(CASE WHEN TipoPerfil.codigo = 5 THEN \'Interno\' else \'Externo\' END) AS tipo_usuario', 
			'TiposExame.codigo as codigo_tipo_exame',
			'TiposExame.descricao as tipo_exame',
		);

		//monta o join da query
		$joins = array(
			array(
				'table' => 'RHHealth.dbo.itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_fornecedor = Fornecedor.codigo',
			),
			array(
                'table' => 'Rhhealth.dbo.agendamento_exames',
                'alias' => 'AgendamentoExame',
                'type' => 'LEFT',
                'conditions' => 'ItemPedidoExame.codigo = AgendamentoExame.codigo_itens_pedidos_exames',
            ),
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
				'table' => 'RHHealth.dbo.exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.anexos_exames',
				'alias' => 'AnexoExame',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExame.codigo = AnexoExame.codigo_item_pedido_exame',
			),
			array(
				'table' => 'RHHealth.dbo.fichas_clinicas',
				'alias' => 'FichaClinica',
				'type' => 'LEFT',
				'conditions' => 'PedidoExame.codigo = FichaClinica.codigo_pedido_exame',
			),
			array(
				'table' => 'RHHealth.dbo.anexos_fichas_clinicas',
				'alias' => 'AnexoFichaClinica',
				'type' => 'LEFT',
				'conditions' => 'AnexoFichaClinica.codigo_ficha_clinica = FichaClinica.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.auditoria_exames',
				'alias' => 'AuditoriaExame',
				'type' => 'LEFT',
				'conditions' => 'AuditoriaExame.codigo_fornecedor = Fornecedor.codigo AND AuditoriaExame.codigo_item_pedido_exame = ItemPedidoExame.codigo AND AuditoriaExame.codigo_pedido_exame = PedidoExame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.status_auditoria_exames',
				'alias' => 'StatusAuditoriaExame',
				'type' => 'LEFT',
				'conditions' => 'StatusAuditoriaExame.codigo = AuditoriaExame.codigo_status_auditoria_exames',
			),
			array(
				'table' => 'RHHealth.dbo.cliente',
				'alias' => 'Cliente',
				'type' => 'LEFT',
				'conditions' => 'Cliente.codigo = PedidoExame.codigo_cliente',
			),
			array(
				'table' => 'RHHealth.dbo.cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'LEFT',
				'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario',
			),
			array(
				'table' => 'RHHealth.dbo.funcionarios',
				'alias' => 'Funcionario',
				'type' => 'LEFT',
				'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.usuario',
				'alias' => 'Usuario',
				'type' => 'LEFT',
				'conditions' => 'AuditoriaExame.codigo_usuario_alteracao = Usuario.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.usuario',
				'alias' => 'UsuarioBaixa',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExameBaixa.codigo_usuario_inclusao = UsuarioBaixa.codigo',
			),			
			array(
				'table' => 'RHHealth.dbo.consolidado_nfs_exame',
				'alias' => 'ConsolidadoNfsExame',
				'type' => 'LEFT',
				'conditions' => 'ConsolidadoNfsExame.codigo_item_pedido_exame = AnexoExame.codigo_item_pedido_exame',
			),			
			array(
				'table' => 'RHHealth.dbo.nota_fiscal_servico',
				'alias' => 'NotaFiscalServico',
				'type' => 'LEFT',
				'conditions' => 'NotaFiscalServico.codigo = ConsolidadoNfsExame.codigo_nota_fiscal_servico',
			),
			array(
                'table' => 'RHHealth.dbo.uperfis',
                'alias' => 'Uperfil',
                'type' => 'LEFT',
                'conditions' => 'Uperfil.codigo = UsuarioBaixa.codigo_uperfil'        
            ),
            array(
                'table' => 'RHHealth.dbo.tipos_perfis',
                'alias' => 'TipoPerfil',
                'type' => 'LEFT',
                'conditions' => 'TipoPerfil.codigo = Uperfil.codigo_tipo_perfil'        
			),
			array(
			    'table'      => 'RHHealth.dbo.tipos_exames',
			    'alias'      => 'TiposExame',
			    'type'       => 'LEFT',
			    'conditions' => 'TiposExame.codigo = ItemPedidoExame.codigo_tipos_exames_pedidos'
			),
			array(
			    'table'      => 'RHHealth.dbo.status_auditoria_imagem',
			    'alias'      => 'StatusAuditoriaImagem',
			    'type'       => 'LEFT',
			    'conditions' => 'StatusAuditoriaImagem.codigo = AuditoriaExame.codigo_status_auditoria_imagem'
			)

		);

		//quando o tipo for relatorio deve acrescentar os filtros e joins
		if(!is_null($tipo)) {

			$fields_relatorio = array(
				'Cliente.codigo as codigo_cliente',
				'Cliente.razao_social as nome_cliente',
				'Setor.descricao as setor_descricao',
				'Cargo.descricao as cargo_descricao',
				'Funcionario.nome as nome_funcionario',
				'ClienteFuncionario.matricula as matricula',
				'CONVERT(VARCHAR, PedidoExame.data_inclusao, 103) as data_pedido_exame',
				'ItemPedidoExame.data_realizacao_exame as data_realizacao'
			);

			//joins para pegar as infos add
			$joins_relatorio = array(
				array(
					'table' => 'RHHealth.dbo.cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => 'Cliente.codigo = PedidoExame.codigo_cliente',
				),
				array(
					'table' => 'RHHealth.dbo.cliente_funcionario',
					'alias' => 'ClienteFuncionario',
					'type' => 'INNER',
					'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario',
				),
				array(
					'table' => 'RHHealth.dbo.funcionarios',
					'alias' => 'Funcionario',
					'type' => 'INNER',
					'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
				),
				array(
					'table' => 'RHHealth.dbo.funcionario_setores_cargos',
					'alias' => 'FuncionarioSetorCargo',
					'type' => 'INNER',
					'conditions' => 'FuncionarioSetorCargo.codigo = PedidoExame.codigo_func_setor_cargo',
				),
				array(
					'table' => 'RHHealth.dbo.setores',
					'alias' => 'Setor',
					'type' => 'INNER',
					'conditions' => 'FuncionarioSetorCargo.codigo_setor = Setor.codigo',
				),
				array(
					'table' => 'RHHealth.dbo.cargos',
					'alias' => 'Cargo',
					'type' => 'INNER',
					'conditions' => 'FuncionarioSetorCargo.codigo_cargo = Cargo.codigo',
				),
			);

			//mergeia os arrays para montar a consulta corretamente
			$joins = array_merge($joins,$joins_relatorio);
			$fields = array_merge($fields,$fields_relatorio);
		}

		//retorna o array para executar
		return array('fields' => $fields,'conditions' => $conditions,'joins' => $joins);

	}//fim getDadosFornecedorExame


	public function auditoriaListagem($filtros,$tipo=null)
	{

		$Configuracao = & ClassRegistry::init('Configuracao');
		$codigo_configurado_aso = $Configuracao->getChave('INSERE_EXAME_CLINICO');

		//monta as conditions
		$conditions = $this->converteFiltroEmCondition($filtros);
		$conditions[] = 
		'(
			([AnexoExame].[codigo] IS NULL AND [AnexoFichaClinica].[codigo] IS NOT NULL AND [Exame].[codigo] = ' . $codigo_configurado_aso . ') OR
			([AnexoExame].[codigo] IS NOT NULL AND [AnexoFichaClinica].[codigo] IS NULL) OR
			([AnexoExame].[codigo] IS NOT NULL AND [AnexoFichaClinica].[codigo] IS NOT NULL)
		)';			

		// $conditions[] = array('AnexoExame.codigo IS NOT NULL');

		//monta o que irá retornar do select
		$fields = array(
			'Fornecedor.codigo as codigo_fornecedor',
			'Fornecedor.nome AS fornecedor_nome',
			'Fornecedor.codigo_documento as fornecedor_cnpj',
			'Fornecedor.codigo_fornecedor_fiscal as fornecedor_codigo_o',
			'Fornecedor.tipo_unidade as fornecedor_tipo_unidade',
			'Fornecedor.ambulatorio as fornecedor_ambulatorio',
			'Fornecedor.prestador_particular as fornecedor_prestador_p',
			'Cliente.codigo AS codigo_cliente',
			'Cliente.nome_fantasia AS cliente_nome',
			'Cliente.codigo_documento as cliente_cnpj',
			'Funcionario.nome AS funcionario_nome',
			'Funcionario.cpf as funcionario_cpf',
			"(CASE WHEN StatusAuditoriaImagem.codigo IS NOT NULL THEN StatusAuditoriaImagem.descricao else 'Pendente' END) AS status",
			'PedidoExame.codigo AS codigo_pedido_exame',
			'Exame.codigo AS codigo_exame',
			'Exame.descricao AS exame',
			'CONVERT(VARCHAR, ItemPedidoExameBaixa.data_inclusao, 103) AS data_baixa',
			'ItemPedidoExame.codigo',
			'ItemPedidoExame.valor_custo as valor',
			'ItemPedidoExame.codigo as codigo_item_pedido_exame',
			'AnexoExame.codigo AS codigo_anexo_exame',
			'AnexoExame.aprovado_auditoria AS anexo_aprovado_aud',
			'AnexoExame.caminho_arquivo AS caminho_arquivo_exame',
			'AnexoExame.data_inclusao AS data_inclusao_anexo',
			'AnexoFichaClinica.codigo AS codigo_anexo_ficha_clinica',
			'AnexoFichaClinica.aprovado_auditoria AS ficha_aprovado_aud',
			'AnexoFichaClinica.caminho_arquivo AS caminho_arquivo_ficha_clinica',
			'AnexoFichaClinica.data_inclusao AS data_inclusao_ficha',
			'AuditoriaExame.motivo as motivo',
			'StatusAuditoriaExame.codigo as codigo_status_auditoria',
			'StatusAuditoriaImagem.codigo as codigo_status_imagem',
			'AuditoriaExame.codigo_status_auditoria_imagem as codigo_status_auditoria_imagem',
			'AuditoriaExame.codigo_motivos_aprovado_ajuste as codigo_motivos_aprovado_ajuste',
			'Usuario.nome as auditoria_usuario_nome',
			'AuditoriaExame.data_alteracao as auditoria_data',
			'AuditoriaExame.codigo_nota_fiscal_servico as codigo_nota_fiscal',
			'AuditoriaExame.recebimento_fisico as recebimento_fisico',
			'AuditoriaExame.motivo as auditoria_motivo',
			'NotaFiscalServico.numero_nota_fiscal as nota_fiscal',
			'NotaFiscalServico.data_vencimento as data_vencimento_nfs',
			'NotaFiscalServico.codigo_nota_fiscal_status as nota_fiscal_status',
			'NotaFiscalServico.data_pagamento as data_pagamento_nfs',
			'Fornecedor.prestador_qualificado as prestador_qualificado',
			'ItemPedidoExameBaixa.codigo_usuario_inclusao as codigo_usuario_inclusao_baixa',
			'UsuarioBaixa.nome as usuario_baixa',
			'ItemPedidoExame.data_realizacao_exame as data_realizacao_exame',
			'(CASE WHEN TipoPerfil.codigo = 5 THEN \'Interno\' else \'Externo\' END) AS tipo_usuario', 
			'TiposExame.codigo as codigo_tipo_exame',
			'TiposExame.descricao as tipo_exame',
			'TipoGlosas.descricao as glosa_motivo',
			'Glosa.motivo_glosa as glosa_observacao',
			"(SELECT TOP 1 ConsolidadoNfsExame.data_consolidacao 
				FROM consolidado_nfs_exame ConsolidadoNfsExame
				WHERE ConsolidadoNfsExame.codigo_item_pedido_exame = ItemPedidoExame.codigo
			) AS data_consolidacao",
			"(SELECT TOP 1 Usuario.nome 
				FROM consolidado_nfs_exame ConsolidadoNfsExame
				INNER JOIN usuario Usuario on ConsolidadoNfsExame.codigo_usuario_consolidacao = Usuario.codigo
				WHERE ConsolidadoNfsExame.codigo_item_pedido_exame = ItemPedidoExame.codigo
			) AS nome_usuario_consolidacao",
			"CASE 
				WHEN AuditoriaExame.libera_anexo_exame IS NULL THEN 'Não'
				WHEN AuditoriaExame.libera_anexo_exame = 1 THEN 'Sim'
			ELSE 'Não' END AS libera_anexo_exame",
			"CASE 
				WHEN AuditoriaExame.libera_anexo_ficha IS NULL THEN 'Não'
				WHEN AuditoriaExame.libera_anexo_ficha = 1 THEN 'Sim'
			ELSE 'Não' END AS libera_anexo_ficha"
		);

		//monta o join da query
		$joins = array(
			array(
				'table' => 'RHHealth.dbo.fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_fornecedor = Fornecedor.codigo',
			),
			array(
                'table' => 'Rhhealth.dbo.agendamento_exames',
                'alias' => 'AgendamentoExame',
                'type' => 'LEFT',
                'conditions' => 'ItemPedidoExame.codigo = AgendamentoExame.codigo_itens_pedidos_exames',
            ),
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
				'table' => 'RHHealth.dbo.exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.anexos_exames',
				'alias' => 'AnexoExame',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExame.codigo = AnexoExame.codigo_item_pedido_exame',
			),
			array(
				'table' => 'RHHealth.dbo.fichas_clinicas',
				'alias' => 'FichaClinica',
				'type' => 'LEFT',
				'conditions' => 'PedidoExame.codigo = FichaClinica.codigo_pedido_exame',
			),
			array(
				'table' => 'RHHealth.dbo.anexos_fichas_clinicas',
				'alias' => 'AnexoFichaClinica',
				'type' => 'LEFT',
				'conditions' => 'AnexoFichaClinica.codigo_ficha_clinica = FichaClinica.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.auditoria_exames',
				'alias' => 'AuditoriaExame',
				'type' => 'LEFT',
				'conditions' => 'AuditoriaExame.codigo_fornecedor = Fornecedor.codigo AND AuditoriaExame.codigo_item_pedido_exame = ItemPedidoExame.codigo AND AuditoriaExame.codigo_pedido_exame = PedidoExame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.status_auditoria_exames',
				'alias' => 'StatusAuditoriaExame',
				'type' => 'LEFT',
				'conditions' => 'StatusAuditoriaExame.codigo = AuditoriaExame.codigo_status_auditoria_exames',
			),
			array(
				'table' => 'RHHealth.dbo.cliente',
				'alias' => 'Cliente',
				'type' => 'LEFT',
				'conditions' => 'Cliente.codigo = PedidoExame.codigo_cliente',
			),
			array(
				'table' => 'RHHealth.dbo.cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'LEFT',
				'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario',
			),
			array(
				'table' => 'RHHealth.dbo.funcionarios',
				'alias' => 'Funcionario',
				'type' => 'LEFT',
				'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.usuario',
				'alias' => 'Usuario',
				'type' => 'LEFT',
				'conditions' => 'AuditoriaExame.codigo_usuario_alteracao = Usuario.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.usuario',
				'alias' => 'UsuarioBaixa',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExameBaixa.codigo_usuario_inclusao = UsuarioBaixa.codigo',
			),	
			array(
				'table' => 'RHHealth.dbo.nota_fiscal_servico',
				'alias' => 'NotaFiscalServico',
				'type' => 'LEFT',
				'conditions' => 'NotaFiscalServico.codigo = AuditoriaExame.codigo_nota_fiscal_servico',
			),
			array(
                'table' => 'RHHealth.dbo.uperfis',
                'alias' => 'Uperfil',
                'type' => 'LEFT',
                'conditions' => 'Uperfil.codigo = UsuarioBaixa.codigo_uperfil'        
            ),
            array(
                'table' => 'RHHealth.dbo.tipos_perfis',
                'alias' => 'TipoPerfil',
                'type' => 'LEFT',
                'conditions' => 'TipoPerfil.codigo = Uperfil.codigo_tipo_perfil'        
			),
			array(
			    'table'      => 'RHHealth.dbo.tipos_exames',
			    'alias'      => 'TiposExame',
			    'type'       => 'LEFT',
			    'conditions' => 'TiposExame.codigo = ItemPedidoExame.codigo_tipos_exames_pedidos'
			),
			array(
			    'table'      => 'RHHealth.dbo.status_auditoria_imagem',
			    'alias'      => 'StatusAuditoriaImagem',
			    'type'       => 'LEFT',
			    'conditions' => 'StatusAuditoriaImagem.codigo = AuditoriaExame.codigo_status_auditoria_imagem'
			),
			array(
				'table' 	 => 'RHHealth.dbo.glosas',
				'alias' 	 => 'Glosa',
				'type' 		 => 'LEFT',
				'conditions' => 'Glosa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo AND Glosa.ativo = 1 AND Glosa.codigo_classificacao_glosa = 2 and StatusAuditoriaImagem.codigo = 2',
			),
			array(
				'table' 	 => 'RHHealth.dbo.tipo_glosas',
				'alias' 	 => 'TipoGlosas',
				'type' 		 => 'LEFT',
				'conditions' => 'Glosa.codigo_tipo_glosa = TipoGlosas.codigo',
			)

		);

		// CDCT-678
		$codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];	

		if(isset($codigo_empresa)){
			$joins[2]['conditions'] .= ' AND PedidoExame.codigo_empresa = '.$codigo_empresa;
			$joins[10]['conditions'] .= ' AND Cliente.codigo_empresa = '.$codigo_empresa;
		}

		//quando o tipo for relatorio deve acrescentar os filtros e joins
		if(!is_null($tipo)) {

			$fields_relatorio = array(
				'Cliente.codigo as codigo_cliente',
				'Cliente.razao_social as nome_cliente',
				'Setor.descricao as setor_descricao',
				'Cargo.descricao as cargo_descricao',
				'Funcionario.nome as nome_funcionario',
				'ClienteFuncionario.matricula as matricula',
				'CONVERT(VARCHAR, PedidoExame.data_inclusao, 103) as data_pedido_exame',
				'ItemPedidoExame.data_realizacao_exame as data_realizacao'
			);

			//joins para pegar as infos add
			$joins_relatorio = array(
				array(
					'table' => 'RHHealth.dbo.cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => 'Cliente.codigo = PedidoExame.codigo_cliente',
				),
				array(
					'table' => 'RHHealth.dbo.cliente_funcionario',
					'alias' => 'ClienteFuncionario',
					'type' => 'INNER',
					'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario',
				),
				array(
					'table' => 'RHHealth.dbo.funcionarios',
					'alias' => 'Funcionario',
					'type' => 'INNER',
					'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
				),
				array(
					'table' => 'RHHealth.dbo.funcionario_setores_cargos',
					'alias' => 'FuncionarioSetorCargo',
					'type' => 'INNER',
					'conditions' => 'FuncionarioSetorCargo.codigo = PedidoExame.codigo_func_setor_cargo',
				),
				array(
					'table' => 'RHHealth.dbo.setores',
					'alias' => 'Setor',
					'type' => 'INNER',
					'conditions' => 'FuncionarioSetorCargo.codigo_setor = Setor.codigo',
				),
				array(
					'table' => 'RHHealth.dbo.cargos',
					'alias' => 'Cargo',
					'type' => 'INNER',
					'conditions' => 'FuncionarioSetorCargo.codigo_cargo = Cargo.codigo',
				),
			);

			//mergeia os arrays para montar a consulta corretamente
			$joins = array_merge($joins,$joins_relatorio);
			$fields = array_merge($fields,$fields_relatorio);
		}

		//retorna o array para executar
		return array('fields' => $fields,'conditions' => $conditions,'joins' => $joins);

	}//fim getDadosFornecedorExame
	

}