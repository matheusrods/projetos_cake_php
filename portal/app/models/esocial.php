<?php

class Esocial extends AppModel {

	var $name = 'Esocial';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'esocial';
	var $primaryKey = 'codigo';

	public $virtualFields = array('cod_desc' => 'CONCAT(Esocial.codigo_descricao, \' - \', Esocial.descricao)');

	/**
	 * Pega os dados e converte nos filtros das tela de esocial
	 */
	public function converteFiltroEmCondition($data) 
	{
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
			$conditions['Cargo.codigo'] = $data['codigo_cargo'];
		}

		if (!empty($data['codigo_setor'])) {
			$conditions['Setor.codigo'] = $data['codigo_setor'];
		}

		if (!empty($data['codigo_funcionario'])) {
			$conditions["Funcionario.codigo"] = $data['codigo_funcionario'];
		}

		if (!empty($data['nome_funcionario'])) {
			$conditions["Funcionario.nome LIKE"] = '%'. $data['nome_funcionario'] . '%';
		}

		if (!empty($data['cpf_funcionario'])) {
			$conditions["Funcionario.cpf"] = Comum::soNumero($data['cpf_funcionario']);
		}

		if (!empty($data['codigo_pedido_exame'])) {
			$conditions['PedidoExame.codigo'] = $data['codigo_pedido_exame'];
		}
		
		//logica para as datas de filtros
		$data_inicio = date('Y-m-').'01 00:00:00';
		$data_fim = date('Y-m-d').' 23:59:59';
		if(!empty($data["data_inicio"])) {			
			$data_inicio = AppModel::dateToDbDate($data["data_inicio"].' 00:00:00');
			$data_fim = AppModel::dateToDbDate($data["data_fim"].' 23:59:59');			
		}//fim if
		else if(!empty($data["data_fim"])) {			
			$data_inicio = date('Y-m-').'01 00:00:00';
			$data_fim = AppModel::dateToDbDate($data["data_fim"].' 23:59:59');			
		}//fim if

		if(!isset($data['tipo_periodo'])){
			$data['tipo_periodo'] = 'I';
		}

		if($data['tipo_periodo'] == 'I'){// se for data da baixa
			$conditions['ItemPedidoExameBaixa.data_inclusao >= '] = $data_inicio;
			$conditions['ItemPedidoExameBaixa.data_inclusao <= '] = $data_fim;
		} else if ($data['tipo_periodo'] == 'C') { // se for a data de conclusao
			$conditions['ItemPedidoExameBaixa.data_realizacao_exame >= '] = $data_inicio;
			$conditions['ItemPedidoExameBaixa.data_realizacao_exame <= '] = $data_fim;
		}

		if(!empty($data['bt_filtro'])) {

			switch($data['bt_filtro']) {
				case '1':
					$conditions[] = 'IntEsocialEvento.codigo_int_esocial_status IS NULL';
					break;
				default:
					$conditions['IntEsocialEvento.codigo_int_esocial_status'] = $data['bt_filtro'];
					break;
			}// fim switch

		}//fim bt_filtro

		
		// die(debug($conditions));
		return $conditions;
		
	} //fim converteFiltroEmCondition

	function carregar($codigo) {
		$dados = $this->find ( 'first', array (
				'conditions' => array (
						$this->name . '.codigo' => $codigo 
				) 
		) );
		return $dados;
	}

	//Carrega informaçoes da TABELA 18 ESOCIAL.
	//Parametro $esocial_codigo_editar - Recuperar codigo inativo.
	function carrega_motivo_afastamento_esocial($esocial_codigo_editar = NULL)
	{
	  $conditions['Esocial.tabela'] = 18;
      $conditions['OR'][0]['Esocial.ativo'] = 1;
      $conditions['OR'][1]['Esocial.ativo'] = 0;
      $conditions['OR'][1]['Esocial.codigo'] = $esocial_codigo_editar;
      $fields = array('Esocial.codigo',"CONCAT(Esocial.codigo_descricao,' - ', Esocial.descricao) AS descricao");
      $dados = $this->find('all', compact('conditions', 'fields', 'order'));

		return $dados;
	}

	/**
	 * [gerar_s2220 description]
	 * 
	 * metodo para executar a query e gerar o xml da tabela s-2220
	 * 
	 * @param  [type] $codigo_pedido_exame [description]
	 * @return [type]                      [description]
	 */
	public function gerar_s2220($codigo_pedido_exame, $ambiente = '1')
	{

		sleep(1);

		// debug($ambiente);exit;
		//monta a query
		$Configuracao = &ClassRegistry::init('Configuracao');		
		$PedidoExame =& ClassRegistry::init("PedidoExame");
		$codigo_empresa = $PedidoExame->field('codigo_empresa', array(
			'codigo' => $codigo_pedido_exame
		));		

		$insere_exame_clinico = $Configuracao->getChaveEmpresa('INSERE_EXAME_CLINICO', $codigo_empresa);

		$query = "
			SELECT	CAST(
				REPLACE(REPLACE(REPLACE(			
				(select 	
					(select REPLACE(REPLACE(REPLACE(
						(SELECT TOP(1)
							CONCAT('ID1',
								CONCAT( 
									(CASE UPPER(c.tipo_unidade) WHEN 'F' THEN substring(c.codigo_documento,0,9) WHEN 'O' THEN substring(c.codigo_documento_real,0,9) END), REPLICATE('0', (14 - LEN((CASE UPPER(c.tipo_unidade) WHEN 'F' THEN substring(c.codigo_documento,0,9) WHEN 'O' THEN substring(c.codigo_documento_real,0,9) END) ))) ),
								FORMAT(CURRENT_TIMESTAMP, 'yyyyMMddHHmmss', 'pt-BR'),
								REPLICATE('0', (4)),1) AS \"@Id\"
							,(SELECT 
								'1' as indRetif,
								-- '' as nrRecibo,
								'".$ambiente."' as tpAmb,
								'1' as procEmi,
								'1' as verProc
							FOR XML PATH('')) AS ideEvento
							,(SELECT
								'1' as tpInsc,
								(CASE UPPER(c.tipo_unidade)
									WHEN 'F' THEN substring(c.codigo_documento,0,9)
									WHEN 'O' THEN substring(c.codigo_documento_real,0,9)
								END) as nrInsc
							FROM pedidos_exames pe
								INNER JOIN cliente c ON pe.codigo_cliente = c.codigo
							WHERE pe.codigo = pe_principal.codigo
							FOR XML PATH('')) AS ideEmpregador
							,(SELECT
								f.cpf as cpfTrab
								,(CASE WHEN (select codigo_descricao FROM esocial WHERE codigo = cf.codigo_esocial_01) IS NULL
									THEN (CASE WHEN cf.matricula <> '' THEN TRIM(cf.matricula) END)
								END) as matricula
								,(select codigo_descricao FROM esocial WHERE codigo = cf.codigo_esocial_01) as codCateg
								--,CONCAT( REPLICATE('0', (3 - LEN(select codigo_descricao FROM esocial WHERE codigo = cf.codigo_esocial_01))),select codigo_descricao FROM esocial WHERE codigo = cf.codigo_esocial_01)
							FROM pedidos_exames pe
							INNER JOIN funcionarios f ON pe.codigo_funcionario = f.codigo
							INNER JOIN cliente_funcionario cf ON pe.codigo_cliente_funcionario = cf.codigo
							WHERE pe.codigo = pe_principal.codigo
							FOR XML PATH('')) as ideVinculo
							,(select REPLACE(REPLACE(REPLACE(
								(SELECT
									CASE 
										WHEN exame_admissional = '1' THEN '0'
										WHEN exame_periodico = '1' THEN '1'
										WHEN exame_retorno = '1' THEN '2'
										WHEN exame_mudanca = '1' THEN '3'
										WHEN exame_monitoracao = '1' THEN '4'
										WHEN exame_demissional = '1' THEN '9'
									END AS tpExameOcup
									,(select REPLACE(REPLACE(REPLACE(
										(SELECT
											ipeb.data_realizacao_exame as dtAso

											,(CASE 
												WHEN fc.parecer = 1 THEN '1' 
												WHEN fc.parecer = 0 THEN '2' 
											END) AS resAso
											
											,(SELECT
												ipeb.data_realizacao_exame as dtExm
												,CONCAT( REPLICATE('0', (4 - LEN(Esocial.codigo_descricao))),Esocial.codigo_descricao) as procRealizado												
												,(CASE WHEN TRIM((SUBSTRING(ipeb.descricao,0,999))) <> '' THEN TRIM((SUBSTRING(ipeb.descricao,0,999))) END) as obsProc
												,(CASE WHEN pe.exame_admissional = '1' THEN '1' ELSE '2' END) as ordExame
												,(CASE WHEN ipeb.resultado IS NOT NULL THEN
													CASE WHEN ipeb.resultado >= 1 AND ipeb.resultado <= 4 THEN ipeb.resultado END
												END) as indResult
											FROM pedidos_exames pe
											INNER JOIN itens_pedidos_exames ipe	ON pe.codigo = ipe.codigo_pedidos_exames
											INNER JOIN itens_pedidos_exames_baixa ipeb ON ipe.codigo = ipeb.codigo_itens_pedidos_exames
											INNER JOIN exames e	ON ipe.codigo_exame = e.codigo
											INNER JOIN esocial Esocial ON Esocial.codigo = e.codigo_esocial_27
											WHERE pe.codigo = pe_principal.codigo
												AND FORMAT(ipeb.data_realizacao_exame, 'yyyy-MM-dd', 'en-US') <= FORMAT(CURRENT_TIMESTAMP, 'yyyy-MM-dd', 'en-US')
												AND ipeb.data_realizacao_exame <= (SELECT TOP(1) data_realizacao_exame FROM itens_pedidos_exames_baixa WHERE codigo_itens_pedidos_exames = (SELECT TOP(1) codigo FROM itens_pedidos_exames WHERE codigo_pedidos_exames = pe.codigo AND codigo_exame = ". $insere_exame_clinico."))

											FOR XML PATH('exame'))
											,(SELECT
												m.nome as nmMed,
												m.numero_conselho as nrCRM,
												m.conselho_uf as ufCRM
											FROM pedidos_exames pe
											INNER JOIN fichas_clinicas fc ON pe.codigo = fc.codigo_pedido_exame
											INNER JOIN medicos m ON fc.codigo_medico = m.codigo
											WHERE pe.codigo = pe_principal.codigo
											FOR XML PATH('')) AS medico

										FROM pedidos_exames pe
										INNER JOIN fichas_clinicas fc ON pe.codigo = fc.codigo_pedido_exame
										INNER JOIN itens_pedidos_exames ipe ON pe.codigo = ipe.codigo_pedidos_exames AND ipe.codigo_exame = ". $insere_exame_clinico."
										INNER JOIN itens_pedidos_exames_baixa ipeb ON ipe.codigo = ipeb.codigo_itens_pedidos_exames
										WHERE pe.codigo = pe_principal.codigo
										FOR XML PATH('')) 
									, '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '')) AS aso
									,(SELECT
										m.cpf as cpfResp
										,m.nome as nmResp
										,m.numero_conselho as nrCRM
										,m.conselho_uf as ufCRM
									FROM pedidos_exames pe
									INNER JOIN cliente c ON pe.codigo_cliente = c.codigo
									INNER JOIN medicos m ON c.codigo_medico_pcmso = m.codigo
									WHERE pe.codigo = pe_principal.codigo
									FOR XML PATH('')) AS respMonit
								FROM pedidos_exames
								WHERE codigo = pe_principal.codigo
								FOR XML PATH(''))
							, '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '')) AS exMedOcup
						FROM pedidos_exames pe
						-- INNER JOIN cliente_funcionario cf ON pe.codigo_cliente_funcionario = cf.codigo
						-- INNER JOIN cliente c ON cf.codigo_cliente = c.codigo
						INNER JOIN cliente c ON pe.codigo_cliente = c.codigo
						WHERE pe.codigo = pe_principal.codigo
						FOR XML PATH('evtMonit'))
					, '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', ''))

				from pedidos_exames as pe_principal
				where pe_principal.codigo = ".$codigo_pedido_exame."
				FOR XML PATH('eSocial')), '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '') as text) as val";
		// debug($query);exit;
		$val = $this->query($query);
		// debug($val);exit;
		/* $dados = "<?xml version='1.0' encoding='UTF-8'?>".$val[0][0]['val'];*/
		$dados = "<?xml version='1.0' encoding='UTF-8'?>".utf8_decode(utf8_encode($val[0][0]['val']));
		
		//deve retirar os acentos pois o esocial nao aceita
		$dados = Comum::tirarAcentos($dados);

		// print $dados;exit;
		return $dados;

	}//fim gerar_s2220

    public function gerar_s2221($codigo_pedido_exame)
    {

        //monta a query
        $query = "
            SELECT	CAST(
                REPLACE(REPLACE(			
                (select 	
                    (SELECT TOP(1)
                        CONCAT('ID1',
                            CASE UPPER(c.tipo_unidade)
                                WHEN 'F' THEN CONCAT(c.codigo_documento, REPLICATE('0', (14 - LEN(c.codigo_documento))))
                                WHEN 'O' THEN CONCAT(c.codigo_documento_real, REPLICATE('0', (14 - LEN(c.codigo_documento_real))))
                            END,
                            FORMAT(CURRENT_TIMESTAMP, 'yyyyMMddHHmmss', 'pt-BR'),
                            REPLICATE('0', (4)),1)
                    FROM pedidos_exames pe
                    INNER JOIN cliente_funcionario cf
                    ON pe.codigo_cliente_funcionario = cf.codigo
                    INNER JOIN cliente c
                    ON cf.codigo_cliente = c.codigo
                    WHERE pe.codigo = pe_principal.codigo
                    FOR XML PATH('evtToxic')),
            
                    (SELECT 
                        '1' as indRetif,
                        -- '' as nrRecibo,
                        '1' as tpAmb,
                        '1' as procEmi,
                        '1' as verProc
                    FOR XML PATH('ideEvento')),
            
                    (SELECT
                        CASE UPPER(c.tipo_unidade)
                            WHEN 'F' THEN c.codigo_documento
                            WHEN 'O' THEN c.codigo_documento_real
                        END as tpInsc,
                        c.inscricao_estadual as nrInsc
                    FROM pedidos_exames pe
                    INNER JOIN cliente_funcionario cf ON pe.codigo_cliente_funcionario = cf.codigo
                    INNER JOIN cliente c ON cf.codigo_cliente = c.codigo
                    WHERE pe.codigo = pe_principal.codigo
                    FOR XML PATH('ideEmpregador')),
            
                    (SELECT
                        f.cpf as cpfTrab,
                        ISNULL(f.nit, '') as nisTrab,
                        ISNULL(cf.matricula, '') as matricula,
                        -- '' as codCateg
                    FROM pedidos_exames pe
                    INNER JOIN funcionarios f ON pe.codigo_funcionario = f.codigo
                    INNER JOIN cliente_funcionario cf ON pe.codigo_cliente_funcionario = cf.codigo
                    WHERE pe.codigo = pe_principal.codigo
                    FOR XML PATH('ideVinculo')),
            
                    (SELECT * FROM (SELECT
                            ipeb.data_realizacao_exame as dtExm,
                            ISNULL(f.codigo_documento_real, f.codigo_documento) as cnpjLab,
                            CONCAT('AA', REPLICATE('0', 9 - LEN(ROW_NUMBER() OVER(ORDER BY pe.codigo)) ), ROW_NUMBER() OVER(ORDER BY pe.codigo)) as codSeqExame,
                            m.nome as nmMed,
                            m.numero_conselho as nrCRM,
                            m.conselho_uf as ufCRM,
                            'N' as indRecusa
                        FROM pedidos_exames pe
                        INNER JOIN itens_pedidos_exames ipe    ON pe.codigo = ipe.codigo_pedidos_exames
                        INNER JOIN itens_pedidos_exames_baixa ipeb ON ipe.codigo = ipeb.codigo_itens_pedidos_exames
                        INNER JOIN exames e    ON ipe.codigo_exame = e.codigo
                        INNER JOIN cliente_funcionario cf ON pe.codigo_cliente_funcionario = cf.codigo
                        INNER JOIN fornecedores f ON ipe.codigo_fornecedor = f.codigo
                        INNER JOIN fichas_clinicas fc ON fc.codigo_pedido_exame = pe.codigo
                        INNER JOIN medicos m ON m.codigo = fc.codigo_medico
                        WHERE pe.codigo = pe_principal.codigo AND e.codigo IN (2195, 134, 135)
                        UNION ALL
                        SELECT
                            cf.data_demissao as dtExm,
                            ISNULL(f.codigo_documento_real, f.codigo_documento) as cnpjLab,
                            -- '',
                            ISNULL(m.nome, '') as nmMed,
                            ISNULL(m.numero_conselho, '') as nrCRM,
                            ISNULL(m.conselho_uf, '') as ufCRM,
                            'S' as indRecusa
                        FROM pedidos_exames pe
                        INNER JOIN itens_pedidos_exames ipe    ON pe.codigo = ipe.codigo_pedidos_exames
                        INNER JOIN exames e ON ipe.codigo_exame = e.codigo
                        INNER JOIN cliente_funcionario cf ON pe.codigo_cliente_funcionario = cf.codigo
                        INNER JOIN fornecedores f ON ipe.codigo_fornecedor = f.codigo
                        LEFT JOIN fichas_clinicas fc ON fc.codigo_pedido_exame = pe.codigo
                        LEFT JOIN medicos m ON m.codigo = fc.codigo_medico
                        INNER JOIN itens_pedidos_exames_recusados iper ON iper.codigo_item_pedido_exame = ipe.codigo
                        WHERE pe.codigo = pe_principal.codigo AND e.codigo IN (2195, 134, 135)) A
                        FOR XML PATH('toxicologico'))
            
                from pedidos_exames as pe_principal
                where pe_principal.codigo = ".$codigo_pedido_exame."
                FOR XML PATH('eSocial')), '&lt;', '<'), '&gt;', '>') as text) as val";

        $val = $this->query($query);
        $dados = "<?xml version='1.0' encoding='UTF-8'?>".$val[0][0]['val'];

        return $dados;

    }//fim gerar_s2221

    public function getAllS2221ForXml(array $FILTROS = array(), $pagination = false){
	    $fields = array(
	        "PedidoExame.codigo",
            "PedidoExame.codigo_cliente",
            "Funcionario.nome",
            "Funcionario.cpf",
            "ClienteFuncionario.matricula",
            "CAST(ISNULL(ItemPedidoExameBaixa.data_realizacao_exame, ItemPedidoExameRecusado.data_inclusao) as DATE) as data_baixa",
            "ItemPedidoExameRecusado.codigo",
        );
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
                'type' => 'LEFT',
                'conditions' => 'ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo',
            ),
            array(
                'table' => 'RHHealth.dbo.itens_pedidos_exames_recusados',
                'alias' => 'ItemPedidoExameRecusado',
                'type' => 'LEFT',
                'conditions' => 'ItemPedidoExameRecusado.codigo_item_pedido_exame = ItemPedidoExame.codigo',
            ),
            array(
                'table' => 'RHHealth.dbo.fichas_clinicas',
                'alias' => 'FichaClinica',
                'type' => 'LEFT',
                'conditions' => 'FichaClinica.codigo_pedido_exame = PedidoExame.codigo',
            ),
            array(
                'table' => 'RHHealth.dbo.medicos',
                'alias' => 'Medico',
                'type' => 'LEFT',
                'conditions' => 'FichaClinica.codigo_medico = Medico.codigo',
            ),
            array(
                'table' => 'RHHealth.dbo.funcionarios',
                'alias' => 'Funcionario',
                'type' => 'INNER',
                'conditions' => 'PedidoExame.codigo_funcionario = Funcionario.codigo',
            ),
            array(
                'table' => 'RHHealth.dbo.cliente_funcionario',
                'alias' => 'ClienteFuncionario',
                'type' => 'INNER',
                'conditions' => 'PedidoExame.codigo_cliente_funcionario = ClienteFuncionario.codigo',
            ),
            array(
                'table' => 'RHHealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'ClienteFuncionario.codigo_cliente = Cliente.codigo',
            ),
            array(
                'table' => 'RHHealth.dbo.funcionario_setores_cargos',
                'alias' => 'FuncionarioSetorCargo',
                'type' => 'INNER',
                'conditions' => 'PedidoExame.codigo_func_setor_cargo = FuncionarioSetorCargo.codigo',
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
        $where = array(
            "ItemPedidoExame.codigo_exame IN(2195, 134, 135)",
            "((ItemPedidoExameBaixa.data_inclusao IS NOT NULL AND ItemPedidoExameRecusado.codigo IS NULL AND Medico.codigo IS NOT NULL) OR (ItemPedidoExameBaixa.data_inclusao IS NULL AND ItemPedidoExameRecusado.codigo IS NOT NULL))"
        );

        if($pagination){
            $paginate = array(
                'fields' => $fields,
                'joins' => $joins,
                'conditions' => array_merge($where, $FILTROS),
                'limit' => 50,
                'order' => "PedidoExame.data_inclusao DESC, Funcionario.nome ASC"
            );
            return $paginate;
        }else{
            $this->PedidoExame =& ClassRegistry::init("PedidoExame");
            return $this->PedidoExame->find('sql', array('joins' => $joins, 'fields' => $fields, 'conditions' => array_merge($where, $FILTROS)));
        }
    }

    public function getAllS2210ForXml(array $conditions = array(), $pagination = false){
	    
	    $codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];

		//varre os conditions
    	$conditions['Cat.codigo_empresa'] = $codigo_empresa;
		
    	//monta o where
    	$where = $this-> montaWhereForXML($conditions);

    	// debug($where);exit;

		//monta a query para pegar os dados e podermos validar se tem algum erro conforme o layout do esocial
		$query = "
			SELECT [Cat].[codigo] AS codigo_cat
			       ,[Cat].[codigo_cliente] AS codigo_cliente
			       ,[Funcionario].[nome] AS nome_funcionario
			       ,[Funcionario].[cpf] AS cpf_funcionario
			       ,[Funcionario].[codigo] AS codigo_funcionario
			       ,[ClienteFuncionario].[matricula] AS matricula
			       ,ClienteFuncionario.admissao AS dtAdmissao,
			        Cliente.nome_fantasia

			       	,IntEsocialEvento.codigo AS codigo_int_esocial_evento
					,IntEsocialEvento.codigo_int_esocial_status as codigo_int_esocial_status
					,IntEsocialStatus.descricao AS descricao_esocial_status

			       --ideEmpregador
				   ,'1' AS tpInsc
				   ,CASE UPPER(Cliente.tipo_unidade)
						WHEN 'F' THEN Cliente.codigo_documento
						WHEN 'O' THEN Cliente.codigo_documento_real
					END as nrInsc
					-- ideVinculo
					,Funcionario.cpf as cpfTrab
					,(CASE WHEN ClienteFuncionario.matricula IS NOT NULL THEN ClienteFuncionario.matricula END) AS matricula
					,(CASE WHEN ClienteFuncionario.matricula IS NULL OR ClienteFuncionario.matricula = '' THEN 
						(CASE WHEN ClienteFuncionario.codigo_esocial_01 IS NOT NULL THEN 
							(SELECT codigo_descricao FROM esocial WHERE codigo = ClienteFuncionario.codigo_esocial_01)
						END)
					END) AS codCateg

					--cat
					,Cat.data_acidente as dtAcid
					,e.codigo_descricao as tpAcid
					,REPLACE(convert(varchar(5), cast(Cat.hora_acidente as time), 108),':','') as hrAcid
					,(CASE WHEN Cat.apos_qts_hs_trabalho IS NOT NULL THEN REPLACE(convert(varchar(5), cast(Cat.apos_qts_hs_trabalho as time), 108),':','') END) as hrsTrabAntesAcid
					,Cat.tipo_cat_codigo as tpCat
					,case UPPER(Cat.morte)
						when 1 then 'S'
						when 0 then 'N'
						when null then 'N'
					end as indCatObito
					,Cat.data_obito as dtObito
					,case UPPER(Cat.resistro_policial)
						when 1 then 'S'
						when 0 then 'N'
						when null then 'N'
					end as indComunPolicia
					,(CASE WHEN Cat.codigo_esocial_16 IS NOT NULL THEN (SELECT codigo_descricao FROM esocial WHERE codigo = Cat.codigo_esocial_16)
						WHEN Cat.codigo_esocial_14_15 IS NOT NULL THEN (SELECT codigo_descricao FROM esocial WHERE codigo = Cat.codigo_esocial_14_15)
					END) as codSitGeradora
					,Cat.motivo_emissao as iniciatCAT
					,(CASE WHEN (Cat.observacao_cat IS NOT NULL AND datalength(Cat.observacao_cat) <> 0) THEN Cat.observacao_cat END) as obsCAT

					-- locaAcidente
					,Cat.local_acidente as tpLocal
					,(CASE WHEN Cat.especificacao_local_acidente IS NOT NULL AND Cat.especificacao_local_acidente <> '' THEN Cat.especificacao_local_acidente END ) as dscLocal
					-- ,Cat.local as tpLograd, -- deve ser o campo esocial tabela 20
					,Cat.acidentado_endereco as dscLograd
					,Cat.acidentado_numero as nrLograd
					,(CASE WHEN (Cat.acidentado_complemento IS NOT NULL AND Cat.acidentado_complemento <> '') THEN Cat.acidentado_complemento END) as complemento
					,(CASE WHEN (Cat.acidentado_bairro IS NOT NULL AND Cat.acidentado_bairro <> '') THEN Cat.acidentado_bairro END) as bairro
					,(CASE WHEN (Cat.local_acidente = 1 OR Cat.local_acidente = 3 OR Cat.local_acidente = 5) THEN Cat.cep_acidentado  END) as cep
					,(CASE WHEN (Cat.local_acidente = 1 OR Cat.local_acidente = 3 OR Cat.local_acidente = 4 OR Cat.local_acidente = 5) THEN Cat.acidentado_cidade_ibge END) as codMunic
					,(CASE WHEN Cat.local_acidente = 1 OR Cat.local_acidente = 3 OR Cat.local_acidente = 4 OR Cat.local_acidente = 5 THEN Cat.acidente_estado END) as uf
					,(CASE WHEN Cat.codigo_pais IS NOT NULL AND cat.local_acidente = 2  THEN (SELECT codigo_descricao FROM esocial WHERE codigo = Cat.codigo_pais) END) as pais
					,(CASE WHEN cat.local_acidente = 2 THEN cat.cod_postal END) as codPostal
					-- ideLocalAcid
					,(CASE WHEN Cat.tipo_inscricao IS NOT NULL AND Cat.tipo_inscricao <> '' THEN Cat.tipo_inscricao END) as tpInsc_ideLocalAcid
					,(CASE WHEN Cat.tipo_inscricao IS NOT NULL AND Cat.tipo_inscricao <> '' THEN
						case
							when Cat.tipo_inscricao = 1 then CONCAT(Cliente.codigo_documento, REPLICATE('0', (14 - LEN(Cliente.codigo_documento))))
							when Cat.tipo_inscricao = 3 then Cat.codigo_caepf
							when Cat.tipo_inscricao = 4 then Cat.codigo_cno
						end
					END) as nrInsc_ideLocalAcid
					--parteAtingida
					,ePartAtingida.codigo_descricao as codParteAting
					,Cat.lateralidade_corpo as lateralidade
					--agenteCausador
					,eAgenteCausador.codigo_descricao as codAgntCausador
					--atestado
					,Cat.data_atendimento as dtAtendimento
					,REPLACE(convert(varchar(5), cast(Cat.hora_atendimento as time), 108),':','') AS hrAtendimento
					,ISNULL(Cat.indicativo_internacao, 'N') AS indInternacao
					,Cat.duracao_estimada_tratamento AS durTrat
					,case when Cat.houve_afastamento = '1' then 'S' else 'N' end AS indAfast
					,(select codigo_descricao from esocial where codigo = Cat.natureza_lesao) AS dscLesao
					,TRIM(substring(Cat.descricao_complementar_lesao,0,200)) AS dscCompLesao
					,(CASE WHEN Cat.diagnostico_provavel IS NOT NULL AND Cat.diagnostico_provavel <> '' THEN substring(Cat.diagnostico_provavel,0,100) END) AS diagProvavel
					,(CASE WHEN Cat.cid10 IS NOT NULL THEN (SELECT TOP 1 TRIM(codigo_cid10) FROM RHHealth.dbo.cid WHERE descricao = Cat.cid10) END) AS codCID
					,trim(m.nome) as nmEmit
					,case 
						when cp.descricao like '%CRM%' then 1
						when cp.descricao like '%CRO%' then 2
						when cp.descricao like '%RMS%' then 3
						end as ideOC
					,m.codigo_conselho_profissional as nrOC
					,m.conselho_uf as ufOC
					,Cat.numero_cat_origem as nrRecCatOrig
			FROM RHHealth.dbo.[cat] AS [Cat]
			INNER JOIN [RHHealth].[dbo].[funcionario_setores_cargos] AS [FuncionarioSetorCargo] ON ([FuncionarioSetorCargo].[codigo] = [Cat].[codigo_funcionario_setor_cargo])
			INNER JOIN [RHHealth].[dbo].[cliente_funcionario] AS [ClienteFuncionario] ON ([ClienteFuncionario].[codigo] = [FuncionarioSetorCargo].[codigo_cliente_funcionario])
			INNER JOIN [RHHealth].[dbo].[cliente] AS [Cliente] ON ([Cliente].[codigo] = [FuncionarioSetorCargo].[codigo_cliente_alocacao])
			INNER JOIN [RHHealth].[dbo].[funcionarios] AS [Funcionario] ON ([Funcionario].[codigo] = [ClienteFuncionario].[codigo_funcionario])
			INNER JOIN [RHHealth].[dbo].[setores] AS [Setor] ON ([Setor].[codigo] = [FuncionarioSetorCargo].[codigo_setor])
			INNER JOIN [RHHealth].[dbo].[cargos] AS [Cargo] ON ([Cargo].[codigo] = [FuncionarioSetorCargo].[codigo_cargo])
			LEFT JOIN esocial ePartAtingida on ePartAtingida.codigo = Cat.codigo_esocial_13
			LEFT JOIN esocial eAgenteCausador on eAgenteCausador.codigo = Cat.codigo_esocial_14_15
			LEFT JOIN esocial e on Cat.codigo_esocial_24 = e.codigo

			LEFT JOIN medicos m on m.codigo = Cat.codigo_medico
			LEFT JOIN conselho_profissional cp on cp.codigo = m.codigo_conselho_profissional

			LEFT JOIN [RHHealth].[dbo].[int_esocial_eventos] AS [IntEsocialEvento] ON [IntEsocialEvento].codigo = (select TOP 1 codigo 
																													from int_esocial_eventos iee 
																													where iee.codigo_int_esocial_tipo_evento IN (1,5)
																														AND iee.codigo_registro_sistema = Cat.codigo
																													ORDER BY iee.codigo DESC)
			LEFT JOIN [RHHealth].[dbo].[int_esocial_status] AS [IntEsocialStatus] ON IntEsocialEvento.codigo_int_esocial_status = IntEsocialStatus.codigo


			WHERE {$where}
			ORDER BY [Cat].[data_inclusao] DESC,
			         [Funcionario].[nome] ASC";

		// print $query; 
		$val = $this->query($query);
		// debug($val);exit;

		return $val;

    }//fim s2210ForXML
	   

    public function getAllS2220ForXml(array $conditions = array(), $pagination = false)
    {

    	//pega o codigo do aso
    	$this->Configuracao = ClassRegistry::init("Configuracao");
    	$codigo_aso = $this->Configuracao->getChave("INSERE_EXAME_CLINICO");

    	//varre os conditions
    	$conditions['PedidoExame.codigo_empresa'] = 1;
    	$conditions['ItemPedidoExame.codigo_exame'] = $codigo_aso;
    	
    	//monta o where
    	$where = $this->montaWhereForXML($conditions);

 		$query = "
 			SELECT PedidoExame.codigo_cliente AS codigo_cliente,
 						CONCAT('ID1',
								CONCAT( substring(isnull(Cliente.codigo_documento,Cliente.codigo_documento_real),0,9), REPLICATE('0', (14 - LEN(substring(isnull(Cliente.codigo_documento,Cliente.codigo_documento_real),0,9)))) ),
								FORMAT(CURRENT_TIMESTAMP, 'yyyyMMddHHmmss', 'pt-BR'),
								REPLICATE('0', (4)),1) AS \"@Id\",
 			   CONVERT(VARCHAR(10), ItemPedidoExameBaixa.data_realizacao_exame, 103) as data_realizacao_exame,
	           Funcionario.nome,
	           Funcionario.cpf,
	           ClienteFuncionario.matricula,
	           PedidoExame.codigo,
	           Cliente.nome_fantasia,
	           CONVERT(VARCHAR(10), ItemPedidoExameBaixa.data_inclusao, 103) AS data_baixa
			   
			   	,IntEsocialEvento.codigo AS codigo_int_esocial_evento
				,IntEsocialEvento.codigo_int_esocial_status as codigo_int_esocial_status
				,IntEsocialStatus.descricao AS descricao_esocial_status

			   ,'1' as tpInsc
			   ,isnull(Cliente.codigo_documento,Cliente.codigo_documento_real) as nrInsc
			   ,Funcionario.cpf AS cpfTrab
			   ,ClienteFuncionario.matricula AS matricula
			   ,ClienteFuncionario.codigo_esocial_01 AS codCateg
			   ,(CASE 
					WHEN PedidoExame.exame_admissional = '1' THEN '0'
					WHEN PedidoExame.exame_periodico = '1' THEN '1'
					WHEN PedidoExame.exame_retorno = '1' THEN '2'
					WHEN PedidoExame.exame_mudanca = '1' THEN '3'
					WHEN PedidoExame.exame_monitoracao = '1' THEN '4'
					WHEN PedidoExame.exame_demissional = '1' THEN '9'
				END) AS tpExameOcup
			   ,ItemPedidoExameBaixa.data_realizacao_exame as dtAso

			   ,(CASE 
					WHEN FichaClinica.parecer = 1 THEN '1' 
					WHEN FichaClinica.parecer = 0 THEN '2' 
				END) AS resAso

			    ,Medico.nome as nmMed
				,Medico.numero_conselho as nrCRM
				,Medico.conselho_uf as ufCRM
				
				,MedicoPCMSO.cpf as pcmso_cpfResp
				,MedicoPCMSO.nome as pcmso_nmResp
				,MedicoPCMSO.numero_conselho as pcmso_nrCRM
				,MedicoPCMSO.conselho_uf as pcmso_ufCRM

			   	,(select CAST(REPLACE(REPLACE(REPLACE(
					(select 
						(SELECT
							e.descricao as desc_exame
							,ipeb.data_realizacao_exame as dtExm
							,Esocial.codigo_descricao as procRealizado
							,(CASE WHEN TRIM((SUBSTRING(ipeb.descricao,0,999))) <> '' THEN TRIM((SUBSTRING(ipeb.descricao,0,999))) END) as obsProc
							,(CASE WHEN pe.exame_admissional = '1' THEN '1' ELSE '2' END) as ordExame
							,(CASE WHEN ipeb.resultado >= 1 AND ipeb.resultado <= 4 THEN ipeb.resultado ELSE 2 END) as indResult
						FROM pedidos_exames pe
							INNER JOIN itens_pedidos_exames ipe	ON pe.codigo = ipe.codigo_pedidos_exames
							INNER JOIN itens_pedidos_exames_baixa ipeb ON ipe.codigo = ipeb.codigo_itens_pedidos_exames
							INNER JOIN exames e	ON ipe.codigo_exame = e.codigo
							INNER JOIN esocial Esocial ON Esocial.codigo = e.codigo_esocial_27
						WHERE pe.codigo = PedidoExame.codigo
							AND FORMAT(ipeb.data_realizacao_exame, 'yyyy-MM-dd', 'en-US') <= FORMAT(CURRENT_TIMESTAMP, 'yyyy-MM-dd', 'en-US')
							AND ipeb.data_realizacao_exame <= ItemPedidoExameBaixa.data_realizacao_exame
						FOR XML PATH('exame'))
					FROM pedidos_exames peext
					WHERE peext.codigo = PedidoExame.codigo
					FOR XML PATH('exames')), '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '')
				AS TEXT)) AS exame

			FROM RHHealth.dbo.pedidos_exames AS PedidoExame
				INNER JOIN RHHealth.dbo.itens_pedidos_exames AS ItemPedidoExame ON (PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames)
				INNER JOIN RHHealth.dbo.itens_pedidos_exames_baixa AS ItemPedidoExameBaixa ON (ItemPedidoExame.codigo = ItemPedidoExameBaixa.codigo_itens_pedidos_exames)
				INNER JOIN RHHealth.dbo.funcionarios AS Funcionario ON (PedidoExame.codigo_funcionario = Funcionario.codigo)
				INNER JOIN RHHealth.dbo.cliente_funcionario AS ClienteFuncionario ON (PedidoExame.codigo_cliente_funcionario = ClienteFuncionario.codigo)
				INNER JOIN RHHealth.dbo.funcionario_setores_cargos AS FuncionarioSetorCargo ON (PedidoExame.codigo_func_setor_cargo = FuncionarioSetorCargo.codigo)
				
				INNER JOIN RHHealth.dbo.cliente AS Cliente ON (PedidoExame.codigo_cliente = Cliente.codigo)
				INNER JOIN RHHealth.dbo.medicos MedicoPCMSO ON Cliente.codigo_medico_pcmso = MedicoPCMSO.codigo
				INNER JOIN RHHealth.dbo.fichas_clinicas AS FichaClinica ON (PedidoExame.codigo = FichaClinica.codigo_pedido_exame)
				INNER JOIN RHHealth.dbo.medicos Medico ON FichaClinica.codigo_medico = Medico.codigo

				LEFT JOIN [RHHealth].[dbo].[int_esocial_eventos] AS [IntEsocialEvento] ON [IntEsocialEvento].codigo = (select TOP 1 codigo 
																													from int_esocial_eventos iee 
																													where iee.codigo_int_esocial_tipo_evento IN (2,5)
																														AND iee.codigo_registro_sistema = PedidoExame.codigo
																													ORDER BY iee.codigo DESC)
				LEFT JOIN [RHHealth].[dbo].[int_esocial_status] AS [IntEsocialStatus] ON IntEsocialEvento.codigo_int_esocial_status = IntEsocialStatus.codigo

			WHERE {$where}

			GROUP BY PedidoExame.codigo_cliente,
			         Funcionario.nome,
			         Funcionario.cpf,
			         ClienteFuncionario.matricula,
			         PedidoExame.codigo,
			          CONVERT(VARCHAR(10), ItemPedidoExameBaixa.data_inclusao, 103)
			        
			        ,IntEsocialEvento.codigo
					,IntEsocialEvento.codigo_int_esocial_status
					,IntEsocialStatus.descricao

					,isnull(Cliente.codigo_documento,Cliente.codigo_documento_real)
					,Funcionario.cpf
					,ClienteFuncionario.matricula
					,ClienteFuncionario.codigo_esocial_01
					,PedidoExame.exame_admissional
					,PedidoExame.exame_periodico
					,PedidoExame.exame_retorno
					,PedidoExame.exame_mudanca
					,PedidoExame.exame_monitoracao
					,PedidoExame.exame_demissional
					,ItemPedidoExameBaixa.data_realizacao_exame
					,FichaClinica.parecer
					,Medico.nome
					,Medico.numero_conselho
					,Medico.conselho_uf
					,MedicoPCMSO.cpf
					,MedicoPCMSO.nome
					,MedicoPCMSO.numero_conselho
					,MedicoPCMSO.conselho_uf
					,CONVERT(VARCHAR(10), ItemPedidoExameBaixa.data_realizacao_exame, 103)
					,Cliente.nome_fantasia


			ORDER BY Funcionario.nome ASC;";

		// debug($query);
		$val = $this->query($query);
		// debug($val);exit;

		return $val;
    }//fim getAllS2220ForXml

    public function gerar_s2210($codigo_cat, $ambiente = '1')
	{

		sleep(1);

		//monta a query
		$query = "
			SELECT	CAST(
				REPLACE(REPLACE(REPLACE(
				(select
					(select REPLACE(REPLACE(REPLACE(
						(SELECT TOP(1)
							CONCAT('ID1'
								,CONCAT( substring(isnull(c.codigo_documento,c.codigo_documento_real),0,9), REPLICATE('0', (14 - LEN(substring(isnull(c.codigo_documento,c.codigo_documento_real),0,9)))) )
								,FORMAT(CURRENT_TIMESTAMP, 'yyyyMMddHHmmss', 'pt-BR')
								,REPLICATE('0', (4))
								,1) AS \"@Id\"
								,(SELECT 
									isnull(cat.evento_retificacao,'1') as indRetif,
									(CASE WHEN cat.evento_retificacao = '2' THEN cat.recibo_retificacao END) AS nrRecibo,
									". $ambiente ." as tpAmb,
									'1' as procEmi,
									'1' as verProc
								FROM cat cat
								WHERE cat.codigo = cat_principal.codigo
								FOR XML PATH('')) as ideEvento
								,(SELECT
									'1' AS tpInsc,
									substring(isnull(c.codigo_documento,c.codigo_documento_real),0,9) as nrInsc
								FROM cat cat
									INNER JOIN funcionario_setores_cargos fsc ON fsc.codigo = cat.codigo_funcionario_setor_cargo
									INNER JOIN cliente_funcionario cf ON cf.codigo = fsc.codigo_cliente_funcionario
									INNER JOIN cliente c ON c.codigo = fsc.codigo_cliente_alocacao
								WHERE cat.codigo = cat_principal.codigo
								FOR XML PATH('')) AS ideEmpregador
								,(SELECT									
									f.cpf as cpfTrab

									,(CASE WHEN (select codigo_descricao FROM esocial WHERE codigo = cf.codigo_esocial_01) IS NULL
										THEN (CASE WHEN cf.matricula <> '' THEN TRIM(cf.matricula) END)
									END) as matricula
									,(CASE WHEN cf.matricula IS NULL OR cf.matricula = '' THEN 
										(CASE WHEN cf.codigo_esocial_01 IS NOT NULL THEN 
											(SELECT codigo_descricao FROM esocial WHERE codigo = cf.codigo_esocial_01)
										END)
									END) AS codCateg
								FROM cat cat
									INNER JOIN funcionario_setores_cargos fsc ON fsc.codigo = cat.codigo_funcionario_setor_cargo
									INNER JOIN cliente_funcionario cf ON cf.codigo = fsc.codigo_cliente_funcionario
									INNER JOIN funcionarios f ON cf.codigo_funcionario = f.codigo
								WHERE cat.codigo = cat_principal.codigo
								FOR XML PATH('')) AS ideVinculo
								,(select REPLACE(REPLACE(REPLACE(
									(SELECT
										cat.data_acidente as dtAcid,
										e.codigo_descricao as tpAcid,
										REPLACE(convert(varchar(5), cast(cat.hora_acidente as time), 108),':','') as hrAcid,
										(CASE WHEN cat.apos_qts_hs_trabalho IS NOT NULL THEN REPLACE(convert(varchar(5), cast(cat.apos_qts_hs_trabalho as time), 108),':','') END) as hrsTrabAntesAcid,
										cat.tipo_cat_codigo as tpCat,
										case UPPER(cat.morte)
											when 1 then 'S'
											when 0 then 'N'
											when null then 'N'
										end as indCatObito,
										cat.data_obito as dtObito,
										case UPPER(cat.resistro_policial)
											when 1 then 'S'
											when 0 then 'N'
											when null then 'N'
										end as indComunPolicia,
										(CASE WHEN Cat.codigo_esocial_16 IS NOT NULL THEN (SELECT codigo_descricao FROM esocial WHERE codigo = Cat.codigo_esocial_16)
											WHEN cat.codigo_esocial_14_15 IS NOT NULL THEN (SELECT codigo_descricao FROM esocial WHERE codigo = cat.codigo_esocial_14_15)
										END) as codSitGeradora,
										cat.motivo_emissao as iniciatCAT,
										(CASE WHEN (cat.observacao_cat IS NOT NULL AND datalength(cat.observacao_cat) <> 0) THEN SUBSTRING(cat.observacao_cat,0,998) END) as obsCAT
										,(select REPLACE(REPLACE(REPLACE(
											(SELECT
												cat.local_acidente as tpLocal
												,(CASE WHEN cat.especificacao_local_acidente IS NOT NULL AND cat.especificacao_local_acidente <> '' THEN cat.especificacao_local_acidente END ) as dscLocal
												-- ,cat.local as tpLograd, -- deve ser o campo esocial tabela 20
												,cat.acidentado_endereco as dscLograd
												,cat.acidentado_numero as nrLograd
												,(CASE WHEN (cat.acidentado_complemento IS NOT NULL AND cat.acidentado_complemento <> '') THEN cat.acidentado_complemento END) as complemento
												,(CASE WHEN (cat.acidentado_bairro IS NOT NULL AND cat.acidentado_bairro <> '') THEN cat.acidentado_bairro END) as bairro
												,(CASE WHEN (cat.local_acidente = 1 OR cat.local_acidente = 3 OR cat.local_acidente = 5) THEN REPLICATE('0', 8 - LEN(cat.cep_acidentado)) + RTrim(cat.cep_acidentado)  END) as cep
												,(CASE WHEN (cat.local_acidente = 1 OR cat.local_acidente = 3 OR cat.local_acidente = 4 OR cat.local_acidente = 5) THEN cat.acidentado_cidade_ibge END) as codMunic
												,(CASE WHEN cat.local_acidente = 1 OR cat.local_acidente = 3 OR cat.local_acidente = 4 OR cat.local_acidente = 5 THEN cat.acidente_estado END) as uf
												,(CASE WHEN cat.codigo_pais IS NOT NULL AND cat.local_acidente = 2 THEN (SELECT codigo_descricao FROM esocial WHERE codigo = cat.codigo_pais) END) as pais
												,(CASE WHEN cat.local_acidente = 2 THEN cat.cod_postal END) as codPostal
												,(CASE WHEN cat.tipo_inscricao IS NOT NULL AND cat.tipo_inscricao <> '' THEN
													(SELECT
														cat.tipo_inscricao as tpInsc,
														case
															when cat.tipo_inscricao = 1 then CONCAT(c.codigo_documento, REPLICATE('0', (14 - LEN(c.codigo_documento))))
															when cat.tipo_inscricao = 3 then cat.codigo_caepf
															when cat.tipo_inscricao = 4 then cat.codigo_cno
														end as nrInsc
													FROM cat cat 
														INNER JOIN funcionario_setores_cargos fsc ON fsc.codigo = cat.codigo_funcionario_setor_cargo
														INNER JOIN cliente_funcionario cf ON cf.codigo = fsc.codigo_cliente_funcionario
														INNER JOIN cliente c ON c.codigo = fsc.codigo_cliente_alocacao
													WHERE cat.codigo = cat_principal.codigo
													FOR XML PATH('')) 
												END) AS ideLocalAcid

											FROM cat cat
											WHERE cat.codigo = cat_principal.codigo
											FOR XML PATH('')) 
										, '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '')) AS localAcidente

										,(SELECT
											e.codigo_descricao as codParteAting,
											cat.lateralidade_corpo as lateralidade
										FROM cat cat
											inner join esocial e on e.codigo = codigo_esocial_13
										WHERE cat.codigo = cat_principal.codigo
										FOR XML PATH('')) AS parteAtingida
										,(SELECT
											e.codigo_descricao as codAgntCausador
										FROM cat cat
											inner join esocial e on e.codigo = cat.codigo_esocial_14_15
										WHERE cat.codigo = cat_principal.codigo
										FOR XML PATH('')) AS agenteCausador
										,(select REPLACE(REPLACE(REPLACE(
												(SELECT
													cat.data_atendimento as dtAtendimento,
													REPLACE(convert(varchar(5), cast(cat.hora_atendimento as time), 108),':','') AS hrAtendimento,
													ISNULL(cat.indicativo_internacao, 'N') AS indInternacao,
													cat.duracao_estimada_tratamento AS durTrat,
													case when cat.houve_afastamento = '1' then 'S' else 'N' end AS indAfast,
													(select codigo_descricao from esocial where codigo = cat.natureza_lesao) AS dscLesao,
													TRIM(substring(cat.descricao_complementar_lesao,0,200)) AS dscCompLesao,
													(CASE WHEN cat.diagnostico_provavel IS NOT NULL AND cat.diagnostico_provavel <> '' THEN substring(cat.diagnostico_provavel,0,100) END) AS diagProvavel,
													(CASE WHEN Cat.cid10 IS NOT NULL THEN (SELECT TOP 1 TRIM(codigo_cid10) FROM RHHealth.dbo.cid WHERE descricao = cat.cid10) END) AS codCID
													,(SELECT
															trim(m.nome) as nmEmit,
															case 
																when cp.descricao like '%CRM%' then 1
																when cp.descricao like '%CRO%' then 2
																when cp.descricao like '%RMS%' then 3
																end as ideOC,
															m.codigo_conselho_profissional as nrOC,
															m.conselho_uf as ufOC
														FROM cat cat
															inner join medicos m on m.codigo = cat.codigo_medico
															inner join conselho_profissional cp on cp.codigo = m.codigo_conselho_profissional
														WHERE cat.codigo = cat_principal.codigo
														FOR XML PATH('')) AS emitente
												FROM cat
												WHERE codigo = cat_principal.codigo
												FOR XML PATH(''))
											, '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '')) AS atestado
										,(CASE WHEN cat.numero_cat_origem IS NOT NULL AND cat.numero_cat_origem <> '' THEN
											(SELECT
												cat.numero_cat_origem as nrRecCatOrig
											FROM cat cat
											WHERE cat.codigo = cat_principal.codigo
											FOR XML PATH('')) END) AS catOrigem

									FROM cat cat
										left join esocial e on cat.codigo_esocial_24 = e.codigo
									WHERE cat.codigo = cat_principal.codigo
									FOR XML PATH(''))
								, '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '')) AS cat
						FROM cat cat
							INNER JOIN funcionario_setores_cargos fsc ON fsc.codigo = cat.codigo_funcionario_setor_cargo
							INNER JOIN cliente_funcionario cf ON cf.codigo = fsc.codigo_cliente_funcionario
							INNER JOIN cliente c ON c.codigo = fsc.codigo_cliente_alocacao
						WHERE cat.codigo = cat_principal.codigo
						FOR XML PATH('evtCAT'))
					, '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', ''))
				from cat as cat_principal
				where cat_principal.codigo = ".$codigo_cat."
				FOR XML PATH('eSocial')), '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '') as text) as val";

		// debug($query);exit;

		$val = $this->query($query);
		// debug($val);exit;

		/*$dados = "<?xml version='1.0' encoding='UTF-8'?>".Comum::converterEncodingPara($val[0][0]['val'], 'UTF-8');*/

		$dados = "<?xml version='1.0' encoding='UTF-8'?>".utf8_decode(utf8_encode($val[0][0]['val']));

		//deve retirar os acentos pois o esocial nao aceita
		$dados = Comum::tirarAcentos($dados);

		// print $dados;exit;
		return $dados;

	}//fim gerar_s2210

	public function getAllS2240ForXml(array $conditions = array(), $pagination = false, $filtros = null)
	{
    	//varre os conditions
    	$conditions['GrupoEconomico.codigo_empresa'] = 1;
    	//monta o where
    	$where = $this->montaWhereForXML($conditions);

    	//busca o codigo_grupo_empresa na tabela grupos_economicos
    	$query_grupo_empresa = "SELECT TOP 1 codigo_grupo_empresa,data_corte_grupo_empresa FROM RHHealth.dbo.grupos_economicos WHERE codigo_cliente = " . $conditions['GrupoEconomico.codigo_cliente'];
    	$dados_ge = $this->query($query_grupo_empresa);

    	$data_corte_grupo_empresa = '2021-10-13';
    	if(!empty($dados_ge)) {
    		$data_corte_grupo_empresa = $dados_ge[0][0]['data_corte_grupo_empresa'];
    	}

    	// debug($data_corte_grupo_empresa);exit;

    	$query_data_vigencia = "";
    	$query_dtIniCondicao = "";
    	$joins_ordem_servico = "";
    	$group_ordem_servico = "";
    	$order = "";
    	$join_funcionario_setor_cargo = "";
    	$join_grupo_exposicao = "";

    	if(isset($filtros)){
    		if($filtros['tipo_periodo'] == 'D'){
    			$group_ordem_servico = "Cliente.codigo,
			ClienteFuncionario.data_inclusao,";
 				$query_data_vigencia = "(SELECT top 1 CONVERT(VARCHAR, os.inicio_vigencia_pcmso, 23)  FROM ordem_servico os where os.codigo_cliente = Cliente.codigo)";
 				$query_dtIniCondicao = "(SELECT top 1 (CASE WHEN CONVERT(VARCHAR, os.inicio_vigencia_pcmso, 23) >= '{$data_corte_grupo_empresa}' THEN CONVERT(VARCHAR, os.inicio_vigencia_pcmso, 23) WHEN FuncionarioSetorCargo.data_inicio >= '{$data_corte_grupo_empresa}' THEN FuncionarioSetorCargo.data_inicio ELSE '{$data_corte_grupo_empresa}' END) 
					FROM ordem_servico os where os.codigo_cliente = Cliente.codigo)";
				$order = "ClienteFuncionario.data_inclusao";
    			$join_funcionario_setor_cargo = "INNER JOIN [RHHealth].[dbo].[funcionario_setores_cargos] AS [FuncionarioSetorCargo] ON FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo";
    			$join_grupo_exposicao = "INNER JOIN [RHHealth].[dbo].[grupo_exposicao] AS [GrupoExposicao] ON [GrupoExposicao].codigo =(select TOP 1 codigo 
																											from grupo_exposicao ge
																											where ge.codigo_cliente_setor = [ClienteSetor].[codigo]
																											and ge.codigo_cargo = [Cargo].[codigo]
																											AND ((ge.[codigo_funcionario] = [Funcionario].[codigo])
																										OR (ge.[codigo_funcionario] IS NULL))
																											ORDER BY ge.codigo DESC)";
    		} else {
 				$query_data_vigencia = "CONVERT(VARCHAR, OrdemServico.inicio_vigencia_pcmso, 23)";
 				$query_dtIniCondicao = "(CASE WHEN CONVERT(VARCHAR, OrdemServico.inicio_vigencia_pcmso, 23) >= '{$data_corte_grupo_empresa}' THEN CONVERT(VARCHAR, OrdemServico.inicio_vigencia_pcmso, 23) WHEN FuncionarioSetorCargo.data_inicio >= '{$data_corte_grupo_empresa}' THEN FuncionarioSetorCargo.data_inicio ELSE '{$data_corte_grupo_empresa}' END)";
 				$joins_ordem_servico = "INNER JOIN [RHHealth].[dbo].[ordem_servico] AS [OrdemServico] ON ([OrdemServico].[codigo_cliente] = [Cliente].[codigo]) 
								INNER JOIN [RHHealth].[dbo].[ordem_servico_item] AS [OrdemServicoItem] ON ([OrdemServicoItem].[codigo_ordem_servico] = [OrdemServico].[codigo])";
				$group_ordem_servico = " OrdemServico.inicio_vigencia_pcmso,";
				$order = "OrdemServico.inicio_vigencia_pcmso" ;
    			
    			$join_funcionario_setor_cargo = "INNER JOIN [RHHealth].[dbo].[funcionario_setores_cargos] AS [FuncionarioSetorCargo] ON FuncionarioSetorCargo.codigo = (select TOP 1 fsc.codigo 
																																from funcionario_setores_cargos fsc 
																																WHERE fsc.codigo_cliente_funcionario = ClienteFuncionario.codigo
																																	AND (fsc.data_fim IS NULL OR fsc.data_fim = '')
																																ORDER BY fsc.codigo DESC)";
    			//filtro do funcionario inativado/demitido no sistema
    			if($filtros['tipo_periodo'] == 'F') {
    				$join_funcionario_setor_cargo = "INNER JOIN [RHHealth].[dbo].[funcionario_setores_cargos] AS [FuncionarioSetorCargo] ON FuncionarioSetorCargo.codigo = (select TOP 1 fsc.codigo 
																																from funcionario_setores_cargos fsc 
																																WHERE fsc.codigo_cliente_funcionario = ClienteFuncionario.codigo
																																ORDER BY fsc.codigo DESC)";
    			}

    			$join_grupo_exposicao = "INNER JOIN [RHHealth].[dbo].[grupo_exposicao] AS [GrupoExposicao] ON ([GrupoExposicao].[codigo_cargo] = [Cargo].[codigo]
																					AND [GrupoExposicao].[codigo_cliente_setor] = [ClienteSetor].[codigo]
																					AND (([GrupoExposicao].[codigo_funcionario] = [Funcionario].[codigo])
																						OR ([GrupoExposicao].[codigo_funcionario] IS NULL)))";
    		}
    	}


		//monta a query para pegar os dados e podermos validar se tem algum erro conforme o layout do esocial
		$query = "
			SELECT [GrupoExposicao].[codigo],
		           [GrupoExposicao].[codigo_cargo],
		           [ClienteSetor].[codigo_cliente],
		           [ClienteSetor].[codigo_cliente_alocacao],
		           [ClienteSetor].[codigo_setor],
		           [Setor].[descricao] AS setor,
		           [Cargo].[descricao] AS cargo,
		           [Funcionario].[codigo] AS codigo_funcionario,
		           [Funcionario].[nome],
		           [Funcionario].[cpf],
		           [Cliente].[nome_fantasia],
		           --CONVERT(VARCHAR, OrdemServico.inicio_vigencia_pcmso, 23) AS data_vigencia
		           {$query_data_vigencia} AS data_vigencia

		           ,IntEsocialEvento.codigo AS codigo_int_esocial_evento
		           ,IntEsocialEvento.codigo_int_esocial_status as codigo_int_esocial_status
		           ,IntEsocialStatus.descricao AS descricao_esocial_status

					,'1' as tpInsc
					,ISNULL(Cliente.codigo_documento, Cliente.codigo_documento_real) as nrInsc
					,Funcionario.cpf as cpfTrab
					
					,(CASE WHEN ClienteFuncionario.matricula <> '' THEN TRIM(ClienteFuncionario.matricula) END) as matricula
					,(select codigo_descricao FROM esocial WHERE codigo = ClienteFuncionario.codigo_esocial_01) as codCateg,


					--,(CASE WHEN CONVERT(VARCHAR, OrdemServico.inicio_vigencia_pcmso, 23) >= '2021-10-13' THEN CONVERT(VARCHAR, OrdemServico.inicio_vigencia_pcmso, 23)
					--	WHEN FuncionarioSetorCargo.data_inicio >= '2021-10-13' THEN FuncionarioSetorCargo.data_inicio
					--	ELSE '2021-10-13'
					--END) AS dtIniCondicao
					{$query_dtIniCondicao} AS dtIniCondicao 
					,ClienteFuncionario.admissao AS dtAdmissao
					,(CASE Cliente.e_tomador WHEN 0 THEN '1' WHEN 1 THEN '2'	END) as localAmb
					,(CASE WHEN CAST(Setor.descricao_setor AS VARCHAR(100)) IS NOT NULL AND CAST(Setor.descricao_setor AS VARCHAR(100)) <> '' THEN CAST(Setor.descricao_setor AS VARCHAR(100)) ELSE CAST(Setor.descricao AS VARCHAR(100)) END) as dscSetor
					,'1' as tpInsc_infoAmb
					,(CASE UPPER(Cliente.tipo_unidade) WHEN 'F' THEN Cliente.codigo_documento WHEN 'O' THEN Cliente.codigo_documento_real END) as nrInsc_infoAmb
					,CAST(Cargo.descricao_cargo AS NVARCHAR(999)) AS dscAtivDes
					
					,(CASE WHEN 
						(SELECT COUNT(*) 
						from grupos_exposicao_risco ger INNER join riscos r on r.codigo = ger.codigo_risco INNER JOIN esocial e24 on r.codigo_esocial_24 = e24.codigo AND e24.ativo = 1 
						WHERE ger.codigo_grupo_exposicao = GrupoExposicao.codigo) = 0 
					THEN
						(select CAST(REPLACE(REPLACE(REPLACE(
							(select REPLACE(REPLACE(REPLACE(
								(select TOP 1
									e24AR.descricao AS risco
									,e24AR.codigo_descricao AS codAgNoc
								from grupos_exposicao_risco ger 
									INNER join riscos r on r.codigo = ger.codigo_risco
									LEFT JOIN esocial e24AR on e24AR.codigo_descricao = '09.01.001' and e24AR.ativo = 1
									left join grupos_riscos gr on gr.codigo = r.codigo_grupo
									left join tecnicas_medicao_ppra tmp on tmp.codigo = ger.codigo_tec_med_ppra
								WHERE ger.codigo_grupo_exposicao = GrupoExposicao.codigo
								FOR XML PATH('agNoc')), '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '')

							FROM grupo_exposicao ger_ext
							WHERE ger_ext.codigo = GrupoExposicao.codigo
							FOR XML PATH('agNoc1')), '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '')
						AS TEXT))
					ELSE 
						(select CAST(REPLACE(REPLACE(REPLACE(
							(select REPLACE(REPLACE(REPLACE(
								(select
									e24.descricao AS risco
									,e24.codigo_descricao AS codAgNoc
									,(CASE e24.codigo_descricao 
										WHEN '01.01.001' THEN SUBSTRING(e24.descricao,0,998)
										WHEN '01.02.001' THEN SUBSTRING(e24.descricao,0,998)
										WHEN '01.03.001' THEN SUBSTRING(e24.descricao,0,998)
										WHEN '01.04.001' THEN SUBSTRING(e24.descricao,0,998)
										WHEN '01.05.001' THEN SUBSTRING(e24.descricao,0,998)
										WHEN '01.06.001' THEN SUBSTRING(e24.descricao,0,998)
										WHEN '01.07.001' THEN SUBSTRING(e24.descricao,0,998)
										WHEN '01.08.001' THEN SUBSTRING(e24.descricao,0,998)
										WHEN '01.09.001' THEN SUBSTRING(e24.descricao,0,998)
										WHEN '01.10.001' THEN SUBSTRING(e24.descricao,0,998)
										WHEN '01.12.001' THEN SUBSTRING(e24.descricao,0,998)
										WHEN '01.13.001' THEN SUBSTRING(e24.descricao,0,998)
										WHEN '01.14.001' THEN SUBSTRING(e24.descricao,0,998)
										WHEN '01.15.001' THEN SUBSTRING(e24.descricao,0,998)
										WHEN '01.16.001' THEN SUBSTRING(e24.descricao,0,998)
										WHEN '01.17.001' THEN SUBSTRING(e24.descricao,0,998)
										WHEN '01.18.001' THEN SUBSTRING(e24.descricao,0,998)
										WHEN '05.01.001' THEN SUBSTRING(e24.descricao,0,998) END) AS dscAgNoc
									,(CASE WHEN e24.codigo_descricao <> '09.01.001' THEN 
										(CASE WHEN ger.codigo_tipo_medicao = '1' THEN '1'ELSE '2' END) 
									END)as tpAval
									,(CASE WHEN ger.codigo_tipo_medicao = '1' THEN SUBSTRING(ger.valor_medido,0,9) END) as intConc
									,(CASE WHEN ger.codigo_tipo_medicao = '1' AND (e24.codigo_descricao = '01.18.001' OR e24.codigo_descricao = '02.01.014') THEN SUBSTRING(ger.valor_maximo ,0,9) END) as limTol
									,(CASE WHEN ger.codigo_tipo_medicao = '1' THEN ger.codigo_tecnica_medicao END) as unMed
									,(CASE WHEN ger.codigo_tipo_medicao = '1' THEN SUBSTRING(tmp.abreviacao,0,40) END) as tecMedicao
											
											
									,(CASE WHEN e24.codigo_descricao <> '09.01.001' THEN
										(select REPLACE(REPLACE(REPLACE(
											(select 	
												(case
													when gere.controle is null then 0
													when gere.controle = ''   then 0
													when gere.controle = 1    then 1
													when gere.controle = 2    then 2
													end) as utilizEPC
												,(CASE WHEN gere.controle = 2 THEN 
													case 
														when gere.epc_eficaz = 1 then 'S'
														when gere.epc_eficaz = 0 then 'N'
														when gere.epc_eficaz is null or gere.epc_eficaz = '' then 'N' 
													end
												END) as eficEpc
												,(case	
													when gepi.controle = 1 then 2
													when gepi.controle = 2 then 1
													when gepi.controle is null or gepi.controle = '' then 0 end) as utilizEPI
														
												,(select 
													gepi.numero_ca as docAval
													,(CASE WHEN gepi.numero_ca IS NULL OR gepi.numero_ca <> '' THEN SUBSTRING(e.nome,0,999) END) AS dscEPI
													,case 
														when gepi.epi_eficaz = 1 then 'S'
														when gepi.epi_eficaz = 0 then 'N'
														when gepi.epi_eficaz is null or gepi.epi_eficaz = '' then 'N' 
													end as eficEpi
												from grupos_exposicao_risco ger_epi
													INNER JOIN grupos_exposicao_risco_epi gepi on gepi.codigo_grupos_exposicao_risco = ger_epi.codigo
													INNER JOIN epi e on gepi.codigo_epi = e.codigo
												WHERE ger_epi.codigo_grupo_exposicao = GrupoExposicao.codigo
													and ger_epcEpi.codigo = ger_epi.codigo
												FOR XML PATH('')) AS epi

												,(CASE WHEN gepi.med_protecao IS NOT NULL OR gepi.med_protecao <> '' THEN
													CASE
														when gepi.med_protecao = 1 then 'S'
														when gepi.med_protecao = 0 then 'N'
													end
												END) as 'epiCompl/medProtecao'
												,(CASE WHEN gepi.cond_functo IS NOT NULL OR gepi.cond_functo <> '' THEN
													CASE
														when gepi.cond_functo = 1 then 'S'
														when gepi.cond_functo = 0 then 'N'
													end
												END) as 'epiCompl/condFuncto'
												,(CASE WHEN gepi.uso_epi IS NOT NULL OR gepi.uso_epi <> '' THEN
													CASE
														when gepi.uso_epi = 1 then 'S'
														when gepi.uso_epi = 0 then 'N'
													end
												END) as 'epiCompl/usoInint'
												,(CASE WHEN gepi.prz_valid IS NOT NULL OR gepi.prz_valid <> '' THEN
													CASE
														when gepi.prz_valid = 1 then 'S'
														when gepi.prz_valid = 0 then 'N'
													end
												END) as 'epiCompl/przValid'
												,(CASE WHEN gepi.periodic_troca IS NOT NULL OR gepi.periodic_troca <> '' THEN
													CASE
														when gepi.periodic_troca = 1 then 'S'
														when gepi.periodic_troca = 0 then 'N'
													end
												END) as 'epiCompl/periodicTroca'
												,(CASE WHEN gepi.higienizacao IS NOT NULL OR gepi.higienizacao <> '' THEN
													CASE
														when gepi.higienizacao = 1 then 'S'
														when gepi.higienizacao = 0 then 'N'
													end
												END) as 'epiCompl/higienizacao'
											from grupos_exposicao_risco ger_epcEpi 
												left join grupos_exposicao_risco_epc gere on gere.codigo_grupos_exposicao_risco = ger_epcEpi.codigo
												left join grupos_exposicao_risco_epi gepi on gepi.codigo_grupos_exposicao_risco = ger_epcEpi.codigo
											WHERE ger_epcEpi.codigo_grupo_exposicao = GrupoExposicao.codigo
												and ger_epcEpi.codigo = ger.codigo
											FOR XML PATH(''))
										, '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', ''))
									END) AS epcEpi
											
								from grupos_exposicao_risco ger 
									INNER join riscos r on r.codigo = ger.codigo_risco
									INNER JOIN esocial e24 on r.codigo_esocial_24 = e24.codigo AND e24.ativo = 1
									-- LEFT JOIN esocial e24AR on e24AR.codigo_descricao = '09.01.001' and e24AR.ativo = 1
									left join grupos_riscos gr on gr.codigo = r.codigo_grupo
									left join tecnicas_medicao_ppra tmp on tmp.codigo = ger.codigo_tec_med_ppra
								WHERE ger.codigo_grupo_exposicao = GrupoExposicao.codigo
								FOR XML PATH('agNoc')), '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '')

							FROM grupo_exposicao ger_ext
							WHERE ger_ext.codigo = GrupoExposicao.codigo
							FOR XML PATH('agNoc1')), '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '')
						AS TEXT)) 
					END) AS agNoc_

					,(select CAST(REPLACE(REPLACE(REPLACE(
						(select 
							(select 
								m.cpf as cpfResp
								,replace(CASE 
									when cp.descricao like '%CREA%' then 4
									when cp.descricao like '%CRM%' then 1
								else 9
								end, '0', '') as ideOC
								,replace(CASE 
									when cp.descricao like '%CREA%' then '0'
									when cp.descricao like '%CRM%' then '0' 
									else cp.descricao
								end, '0', '') as dscOC
								,SUBSTRING(m.numero_conselho, 0, 14) as nrOC
								,m.conselho_uf as ufOC
							from grupo_exposicao ge
								INNER JOIN clientes_setores cs on cs.codigo = ge.codigo_cliente_setor
								INNER JOIN grupos_prevencao_riscos_ambientais gpra on gpra.codigo_cliente = cs.codigo_cliente
								INNER join medicos m on m.codigo = gpra.codigo_medico
								INNER join conselho_profissional cp on cp.codigo = m.codigo_conselho_profissional
							WHERE ge.codigo = GrupoExposicao.codigo
							FOR XML PATH('respReg'))		
						FROM grupo_exposicao ger_ext
						WHERE ger_ext.codigo = GrupoExposicao.codigo
						FOR XML PATH('respReg1')), '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '')
					AS TEXT)) AS respReg

					,(CASE WHEN CAST(GrupoExposicao.observacao AS NVARCHAR(999)) <> '' THEN CAST(GrupoExposicao.observacao AS NVARCHAR(999)) ELSE NULL END) AS obsCompl

		FROM RHHealth.dbo.[grupos_economicos] AS [GrupoEconomico]
			INNER JOIN [RHHealth].[dbo].[grupos_economicos_clientes] AS [GrupoEconomicoCliente] ON ([GrupoEconomico].[codigo] = [GrupoEconomicoCliente].[codigo_grupo_economico])
			INNER JOIN [RHHealth].[dbo].[cliente_funcionario] AS [ClienteFuncionario] ON ([ClienteFuncionario].[codigo_cliente_matricula] = [GrupoEconomicoCliente].[codigo_cliente])
			INNER JOIN [RHHealth].[dbo].[funcionarios] AS [Funcionario] ON ([Funcionario].[codigo] = [ClienteFuncionario].[codigo_funcionario])

			{$join_funcionario_setor_cargo}
			
			
			INNER JOIN [RHHealth].[dbo].[cliente] AS [Cliente] ON ([Cliente].[codigo] = [FuncionarioSetorCargo].[codigo_cliente_alocacao])
			INNER JOIN [RHHealth].[dbo].[cargos] AS [Cargo] ON ([Cargo].[codigo] = [FuncionarioSetorCargo].[codigo_cargo])
			INNER JOIN [RHHealth].[dbo].[setores] AS [Setor] ON ([Setor].[codigo] = [FuncionarioSetorCargo].[codigo_setor])
			INNER JOIN [RHHealth].[dbo].[clientes_setores] AS [ClienteSetor] ON ([ClienteSetor].[codigo_setor] = [Setor].[codigo]
																				AND [ClienteSetor].[codigo_cliente_alocacao] = [FuncionarioSetorCargo].[codigo_cliente_alocacao])
			{$join_grupo_exposicao}
			--INNER JOIN [RHHealth].[dbo].[ordem_servico] AS [OrdemServico] ON ([OrdemServico].[codigo_cliente] = [Cliente].[codigo])
			--INNER JOIN [RHHealth].[dbo].[ordem_servico_item] AS [OrdemServicoItem] ON ([OrdemServicoItem].[codigo_ordem_servico] = [OrdemServico].[codigo])
			{$joins_ordem_servico}

			LEFT JOIN [RHHealth].[dbo].[int_esocial_eventos] AS [IntEsocialEvento] ON [IntEsocialEvento].codigo = (select TOP 1 codigo 
																													from int_esocial_eventos iee 
																													where iee.codigo_int_esocial_tipo_evento IN (4,5)
																														AND iee.codigo_registro_sistema = GrupoExposicao.codigo
																														AND iee.codigo_funcionario = Funcionario.codigo
																													ORDER BY iee.codigo DESC)
			LEFT JOIN [RHHealth].[dbo].[int_esocial_status] AS [IntEsocialStatus] ON IntEsocialEvento.codigo_int_esocial_status = IntEsocialStatus.codigo
		
		WHERE {$where}

		GROUP BY GrupoExposicao.codigo,
		         GrupoExposicao.codigo_cargo,
		         ClienteSetor.codigo_cliente,
		         ClienteSetor.codigo_cliente_alocacao,
		         ClienteSetor.codigo_setor,
		         Setor.descricao,
		         Cargo.descricao,
		         Funcionario.codigo,
		         Funcionario.nome,
		         Funcionario.cpf,
		        {$group_ordem_servico}

		        IntEsocialEvento.codigo
				,IntEsocialEvento.codigo_int_esocial_status
				,IntEsocialStatus.descricao

				,Cliente.codigo_documento
				,Cliente.codigo_documento_real
				,ClienteFuncionario.matricula
				,ClienteFuncionario.codigo_esocial_01
				,FuncionarioSetorCargo.data_inicio
				,Cliente.e_tomador
				,CAST(Setor.descricao_setor AS VARCHAR(100))
				,Cliente.tipo_unidade
				,CAST(Cargo.descricao_cargo AS NVARCHAR(999))
				,CAST(GrupoExposicao.observacao AS NVARCHAR(999))
				,ClienteFuncionario.admissao,
				Cliente.nome_fantasia

		ORDER BY {$order} DESC";

		// debug($query);exit;
		$val = $this->query($query);
		// debug($val);exit;

		return $val;

	}

	/**
	 * [valor_medido__nivel_acao metodo para validar se o valor medido é menor que o nivel de acao para determinados riscos]
	 * @param  [int] $codigo_grupo_exposicao [codigo do grupo de exposição que está sendo pesquisado os riscos para validação]
	 * @return [array]                         [retorna os riscos que foram filtrados pelo metodo, caso todos os riscos entrem na regra de validação retornará "ausência de risco"]
	 */
	public function valor_medido__nivel_acao($codigo_grupo_exposicao)
	{
		//variavel para retornar os resultados
		$ret = array();

		//verifica se tem o codigo
		if(empty($codigo_grupo_exposicao)) {
			return $ret;
		}
		$Configuracao = &ClassRegistry::init('Configuracao');

		//query montada para pegar a ausencia de risco
		$query_ausencia = "SELECT TOP(1)
							    ri.codigo,
							    gr.codigo as codigo_grupo_risco,
							    -- RHHealth.dbo.ufn_decode_utf8_string(gr.descricao) as grupos_riscos_descricao,
							    -- RHHealth.dbo.ufn_decode_utf8_string(ri.nome_agente) as risco_descricao,
							    -- ri.nome_agente,
							    CASE WHEN (ri.nivel_acao <> '' AND grer.valor_medido <> '') THEN
							        CASE WHEN CONVERT(money, REPLACE(grer.valor_medido, ',', '.')) > CONVERT(money, REPLACE(ri.nivel_acao, ',', '.')) THEN 'S'
							        ELSE 'N'
							        END
							    ELSE 'S'
							    END as linha,
							    grer.codigo_tipo_medicao
							FROM RHHealth.dbo.grupo_exposicao gre
							INNER JOIN RHHealth.dbo.grupos_exposicao_risco grer  ON (grer.codigo_grupo_exposicao = gre.codigo)
							INNER JOIN RHHealth.dbo.riscos ri ON (ri.codigo = grer.codigo_risco)
							INNER JOIN RHHealth.dbo.grupos_riscos gr  ON (gr.codigo = ri.codigo_grupo)
							WHERE gr.codigo = 4 AND ri.codigo = ".$Configuracao->getChave('AUSENCIA_DE_RISCO').";";

		//query para pegar os riscos
		$query = "
			SELECT
		        DISTINCT(ri.codigo),
				gr.codigo as codigo_grupo_risco,
				-- RHHealth.publico.Ufn_decode_utf8_string(gr.descricao) as grupos_riscos_descricao,
				--RHHealth.publico.Ufn_decode_utf8_string(ri.nome_agente) as risco_descricao,
				--ri.nome_agente,
				case when (ger.valor_medido <> '' AND ri.nivel_acao <> '') THEN
					case when CONVERT(money, REPLACE(ger.valor_medido, ',', '.')) > CONVERT(money, REPLACE(ri.nivel_acao, ',', '.')) THEN 'S'
					ELSE 'N'
					END
				ELSE 'S'
				END as linha,
				ger.codigo_tipo_medicao
		    FROM RHHealth.dbo.grupos_exposicao_risco ger
				INNER JOIN RHHealth.dbo.riscos ri ON (ri.codigo = ger.codigo_risco)
				INNER JOIN RHHealth.dbo.grupos_riscos gr ON (gr.codigo = ri.codigo_grupo)
				INNER JOIN RHHealth.dbo.esocial e24 on ri.codigo_esocial_24 = e24.codigo AND e24.ativo = 1 
		    WHERE ger.codigo_grupo_exposicao = {$codigo_grupo_exposicao};
		";
		//executa a query
		$dados_riscos = $this->query($query);

		// debug($dados_riscos);exit;

		//verifica se tem dadaos
		if(!empty($dados_riscos)) {
			//conta quantos riscos tem no resultado
			$riscos_qnt = count($dados_riscos);

			//variavel auxiliar para contagem dos riscos
			$arr_riscos = array();
			$riscos_ruidos_quantitativo_menor_qnt = 0;
			$riscos_quimicos_quantitativo_menor_qnt = 0;
			$arr_riscos_ruido = array(
				'8'=>'8',
				'9'=>'9',
				'3543'=>'3543',
				'3544'=>'3544',
				'4772'=>'4772',
				'4774'=>'4774'
			);
			
			//varre os riscos
			foreach($dados_riscos AS $dados) {
				//renomeia
				$dado = $dados[0];
				$arr_riscos[$dado['codigo']] = $dado;

				if($dado['codigo_tipo_medicao'] == 1 && $dado['linha'] == 'N') {
					//verifica se o riscos é ruido
					switch($dado['codigo']) {
						case '8':
						case '9':
						case '3543':
						case '3544':
						case '4772':
						case '4774':
							$riscos_ruidos_quantitativo_menor_qnt++;
							break;						
					}// fim switch

					if($dado['codigo_grupo_risco'] == 2) {
						$riscos_quimicos_quantitativo_menor_qnt++;
					}

				}//fim if tipo medicao e linha = N

			}//fim foreach dados_riscos

			//verifica se deletar o risco ruido
			if($riscos_qnt > 1 AND $riscos_ruidos_quantitativo_menor_qnt != 0) {
				//varre os riscos 
				foreach($arr_riscos AS $codigo => $dadoRisco) {
					//verifica se tem o risco para limpar ele da variavel
					if(in_array($codigo,$arr_riscos_ruido) && $dadoRisco['codigo_tipo_medicao'] == 1) {
						unset($arr_riscos[$codigo]);
					}
				}//fim foreach que deleta o risco
			}//fim verificacao se risco ruido
			elseif($riscos_qnt == 1 AND $riscos_ruidos_quantitativo_menor_qnt == $riscos_qnt) {
				//varre os riscos 
				foreach($arr_riscos AS $codigo => $dadoRisco) {
					//verifica se tem o risco para limpar ele da variavel
					if(in_array($codigo,$arr_riscos_ruido) && $dadoRisco['codigo_tipo_medicao'] == 1) {
						unset($arr_riscos[$codigo]);
					}
				}//fim foreach que deleta o risco
			}

			//verifica se deleta o risco quimico
			if($riscos_qnt > 1 AND $riscos_quimicos_quantitativo_menor_qnt != 0) {
				//varre os riscos 
				foreach($arr_riscos AS $codigo => $dadoRisco) {
					//verifica se tem o risco para limpar ele da variavel
					if($dadoRisco['codigo_tipo_medicao'] == 1 && $dadoRisco['linha']) {
						unset($arr_riscos[$codigo]);
					}
				}//fim foreach que deleta o risco
			}//fim verificaoca se risco quimico
			elseif($riscos_qnt == 1 AND $riscos_quimicos_quantitativo_menor_qnt == $riscos_qnt) {
				//varre os riscos 
				foreach($arr_riscos AS $codigo => $dadoRisco) {
					//verifica se tem o risco para limpar ele da variavel
					if($dadoRisco['codigo_tipo_medicao'] == 1 && $dadoRisco['linha']) {
						unset($arr_riscos[$codigo]);
					}
				}//fim foreach que deleta o risco
			}

			//verifica se tem dados no arr_riscos
			if(empty($arr_riscos)) {

				$dados_query_ausencia = $this->query($query_ausencia);
				//seta os dados de ausencia
				$arr_riscos[$dados_query_ausencia[0][0]['codigo']] = $dados_query_ausencia[0][0];

			}//fim $arr_riscos


		} //fim dados_riscos
		else {

			$dados_query_ausencia = $this->query($query_ausencia);
			//seta os dados de ausencia
			$arr_riscos[$dados_query_ausencia[0][0]['codigo']] = $dados_query_ausencia[0][0];

		}
		// debug($arr_riscos);
		if(!empty($arr_riscos)) {
			//instancia a tabela que vai dar subsidio dos riscos a partir do grupo exposicao
			$this->GrupoExposicaoRiscoValidacao =& ClassRegistry::Init('GrupoExposicaoRiscoValidacao');

			//verifica se tem dados na tabela pelo grupo de exposicao
			$dados_gerv = $this->GrupoExposicaoRiscoValidacao->find('first', array('conditions' => array('codigo_grupo_exposicao' => $codigo_grupo_exposicao)));

			if(!empty($dados_gerv)) {
				$dados_del = $this->query("DELETE FROM RHHealth.dbo.grupo_exposicao_risco_validacao WHERE codigo_grupo_exposicao = " . $codigo_grupo_exposicao);
			}

			foreach($arr_riscos AS $codigo => $dRisco){

				$dados_gerv = array(
					'codigo_grupo_exposicao' => $codigo_grupo_exposicao,
					'codigo_risco' => $codigo,
					'codigo_grupo_risco' => $dRisco['codigo_grupo_risco'],
					'linha' => $dRisco['linha'],
					'codigo_tipo_medicao' => $dRisco['codigo_tipo_medicao'],
					'ativo' => 1,
				);

				$insert_dados = $this->GrupoExposicaoRiscoValidacao->incluir($dados_gerv);

				// debug($this->GrupoExposicaoRiscoValidacao->validationErrors);

				$ret[$codigo] = $codigo;
			}
		}//fim arr_riscos

		return $ret;

	}// fim valor_medido__nivel_acao


	public function gerar_s2240($codigo_grupo_exposicao, $codigo_funcionario = null, $ambiente = '1')
	{

		$codigos_riscos = $this->valor_medido__nivel_acao($codigo_grupo_exposicao);
		// debug($codigos_riscos);
		// exit;

		sleep(1);

		//busca o codigo_grupo_empresa na tabela grupos_economicos
    	$query_grupo_empresa = "SELECT 
									TOP 1 ge.codigo_grupo_empresa,ge.data_corte_grupo_empresa,ge.codigo_empresa 
								FROM RHHealth.dbo.grupo_exposicao gexp
									inner join RHHealth.dbo.clientes_setores cs on gexp.codigo_cliente_setor = cs.codigo
									inner join RHHealth.dbo.grupos_economicos_clientes gec  on cs.codigo_cliente = gec.codigo_cliente
									inner join RHHealth.dbo.grupos_economicos ge on ge.codigo = gec.codigo_grupo_economico
								WHERE gexp.codigo = " . $codigo_grupo_exposicao;
    	$dados_ge = $this->query($query_grupo_empresa);


    	$data_corte_grupo_empresa = '2021-10-13';
    	if(!empty($dados_ge)) {
    		$data_corte_grupo_empresa = $dados_ge[0][0]['data_corte_grupo_empresa'];				
    	}

			$codigo_empresa = !empty($dados_ge[0][0]['codigo_empresa']) ?
				$dados_ge[0][0]['codigo_empresa'] : null;

    	// debug($data_corte_grupo_empresa);exit;

		//monta a query
		$Configuracao = &ClassRegistry::init('Configuracao');


		$ausencia_de_risco = !empty($codigo_empresa) ? $Configuracao->getChaveEmpresa('AUSENCIA_DE_RISCO', $codigo_empresa) : $Configuracao->getChave('AUSENCIA_DE_RISCO');		

		$query = "
			SELECT	CAST(
				REPLACE(REPLACE(REPLACE(
				(select 	
					(select REPLACE(REPLACE(REPLACE(
						(SELECT TOP(1)
							CONCAT('ID1',
								CONCAT( 
									(CASE UPPER(c.tipo_unidade) WHEN 'F' THEN substring(c.codigo_documento,0,9) WHEN 'O' THEN substring(c.codigo_documento_real,0,9) END), REPLICATE('0', (14 - LEN((CASE UPPER(c.tipo_unidade) WHEN 'F' THEN substring(c.codigo_documento,0,9) WHEN 'O' THEN substring(c.codigo_documento_real,0,9) END) ))) ),
								FORMAT(CURRENT_TIMESTAMP, 'yyyyMMddHHmmss', 'pt-BR'),
								REPLICATE('0', (4)),1) AS \"@Id\"
							,
							'1' as 'ideEvento/indRetif'
							-- ,'' as nrRecibo
							,'".$ambiente."' as 'ideEvento/tpAmb'
							,'1' as 'ideEvento/procEmi'
							,'1' as 'ideEvento/verProc'
							
							,'1' as 'ideEmpregador/tpInsc'
							,(CASE UPPER(c.tipo_unidade)
								WHEN 'F' THEN substring(c.codigo_documento,0,9)
								WHEN 'O' THEN substring(c.codigo_documento_real,0,9)
							END) AS 'ideEmpregador/nrInsc'
							
							,f.cpf as 'ideVinculo/cpfTrab'
							
							,(CASE WHEN (select codigo_descricao FROM esocial WHERE codigo = cf.codigo_esocial_01) IS NULL
								THEN (CASE WHEN cf.matricula <> '' THEN TRIM(cf.matricula) END)
							END) as 'ideVinculo/matricula'
							,(select codigo_descricao FROM esocial WHERE codigo = cf.codigo_esocial_01) as 'ideVinculo/codCateg'

							,(CASE WHEN fsc.data_inicio >= '{$data_corte_grupo_empresa}' THEN fsc.data_inicio
								ELSE '{$data_corte_grupo_empresa}'
							END) AS 'infoExpRisco/dtIniCondicao'

							,CASE c.e_tomador
								WHEN 0 THEN '1'
								WHEN 1 THEN '2'
							END as 'infoExpRisco/infoAmb/localAmb'
							,(CASE WHEN TRIM(CAST(s.descricao_setor AS VARCHAR(100))) IS NOT NULL AND TRIM(CAST(s.descricao_setor AS VARCHAR(100))) <> '' THEN TRIM(CAST(s.descricao_setor AS VARCHAR(100))) ELSE TRIM(CAST(s.descricao AS VARCHAR(100))) END) as 'infoExpRisco/infoAmb/dscSetor'
							,'1' as 'infoExpRisco/infoAmb/tpInsc'
							,(CASE UPPER(c.tipo_unidade)
								WHEN 'F' THEN c.codigo_documento
								WHEN 'O' THEN c.codigo_documento_real
							END) as 'infoExpRisco/infoAmb/nrInsc'

							,TRIM(CAST(cargo.descricao_cargo AS NVARCHAR(999))) AS 'infoExpRisco/infoAtiv/dscAtivDes'

							,(select REPLACE(REPLACE(REPLACE(
								(select 
									(CASE WHEN 
										(SELECT COUNT(*) 
										from grupos_exposicao_risco ger INNER join riscos r on r.codigo = ger.codigo_risco INNER JOIN esocial e24 on r.codigo_esocial_24 = e24.codigo AND e24.ativo = 1 
										WHERE ger.codigo_grupo_exposicao = ge_principal.codigo) = 0 
									THEN
										(select REPLACE(REPLACE(REPLACE(
											(select TOP 1
												e24AR.codigo_descricao AS codAgNoc
											from esocial e24AR 
											WHERE e24AR.codigo_descricao = '09.01.001' and e24AR.ativo = 1
											FOR XML PATH('agNoc'))
										, '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', ''))
									ELSE
										(CASE WHEN 
											(SELECT count(*) FROM RHHealth.dbo.grupo_exposicao_risco_validacao where codigo_risco = ".$ausencia_de_risco." AND codigo_grupo_exposicao = ge_principal.codigo) = 1 
										THEN
											(select REPLACE(REPLACE(REPLACE(
												(select TOP 1
													e24AR.codigo_descricao AS codAgNoc
												from esocial e24AR 
												WHERE e24AR.codigo_descricao = '09.01.001' and e24AR.ativo = 1
												FOR XML PATH('agNoc'))
											, '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', ''))
										ELSE
											(select REPLACE(REPLACE(REPLACE(
												(select
													e24.codigo_descricao AS codAgNoc
													,(CASE e24.codigo_descricao 
														WHEN '01.01.001' THEN SUBSTRING(e24.descricao,0,998)
														WHEN '01.02.001' THEN SUBSTRING(e24.descricao,0,998)
														WHEN '01.03.001' THEN SUBSTRING(e24.descricao,0,998)
														WHEN '01.04.001' THEN SUBSTRING(e24.descricao,0,998)
														WHEN '01.05.001' THEN SUBSTRING(e24.descricao,0,998)
														WHEN '01.06.001' THEN SUBSTRING(e24.descricao,0,998)
														WHEN '01.07.001' THEN SUBSTRING(e24.descricao,0,998)
														WHEN '01.08.001' THEN SUBSTRING(e24.descricao,0,998)
														WHEN '01.09.001' THEN SUBSTRING(e24.descricao,0,998)
														WHEN '01.10.001' THEN SUBSTRING(e24.descricao,0,998)
														WHEN '01.12.001' THEN SUBSTRING(e24.descricao,0,998)
														WHEN '01.13.001' THEN SUBSTRING(e24.descricao,0,998)
														WHEN '01.14.001' THEN SUBSTRING(e24.descricao,0,998)
														WHEN '01.15.001' THEN SUBSTRING(e24.descricao,0,998)
														WHEN '01.16.001' THEN SUBSTRING(e24.descricao,0,998)
														WHEN '01.17.001' THEN SUBSTRING(e24.descricao,0,998)
														WHEN '01.18.001' THEN SUBSTRING(e24.descricao,0,998)
														WHEN '05.01.001' THEN SUBSTRING(e24.descricao,0,998) END) AS dscAgNoc
													,(CASE WHEN e24.codigo_descricao <> '09.01.001' THEN 
														(CASE WHEN ger.codigo_tipo_medicao = '1' THEN '1'ELSE '2' END) 
													END)as tpAval
													,(CASE WHEN ger.codigo_tipo_medicao = '1' THEN SUBSTRING(ger.valor_medido,0,9) END) as intConc
													,(CASE WHEN ger.codigo_tipo_medicao = '1' AND (e24.codigo_descricao = '01.18.001' OR e24.codigo_descricao = '02.01.014') THEN SUBSTRING(ger.valor_maximo ,0,9) END) as limTol
													,(CASE WHEN ger.codigo_tipo_medicao = '1' THEN ger.codigo_tecnica_medicao END) as unMed
													,(CASE WHEN ger.codigo_tipo_medicao = '1' THEN SUBSTRING(tmp.abreviacao,0,40) END) as tecMedicao
												
												
													,(CASE WHEN e24.codigo_descricao <> '09.01.001' THEN
														(select REPLACE(REPLACE(REPLACE(
															(select 	
																(case
																	when gere.controle is null then 0
																	when gere.controle = ''   then 0
																	when gere.controle = 1    then 1
																	when gere.controle = 2    then 2
																	end) as utilizEPC
																,(CASE WHEN gere.controle = 2 THEN 
																	case 
																		when gere.epc_eficaz = 1 then 'S'
																		when gere.epc_eficaz = 0 then 'N'
																		when gere.epc_eficaz is null or gere.epc_eficaz = '' then 'N' 
																	end
																END) as eficEpc
																,(case	
																	when gepi.controle = 1 then 2
																	when gepi.controle = 2 then 1
																	when gepi.controle is null or gepi.controle = '' then 0 end) as utilizEPI
															
																,(select 
																	gepi.numero_ca as docAval
																	,(CASE WHEN gepi.numero_ca IS NULL OR gepi.numero_ca <> '' THEN SUBSTRING(e.nome,0,999) END) AS dscEPI
																	,case 
																		when gepi.epi_eficaz = 1 then 'S'
																		when gepi.epi_eficaz = 0 then 'N'
																		when gepi.epi_eficaz is null or gepi.epi_eficaz = '' then 'N' 
																	end as eficEpi
																from grupos_exposicao_risco ger_epi
																	INNER JOIN grupos_exposicao_risco_epi gepi on gepi.codigo_grupos_exposicao_risco = ger_epi.codigo
																	INNER JOIN epi e on gepi.codigo_epi = e.codigo
																WHERE ger_epi.codigo_grupo_exposicao = ge_principal.codigo
																	and ger_epcEpi.codigo = ger_epi.codigo
																FOR XML PATH('')) AS epi

																,(CASE WHEN gepi.med_protecao IS NOT NULL OR gepi.med_protecao <> '' THEN
																	CASE
																		when gepi.med_protecao = 1 then 'S'
																		when gepi.med_protecao = 0 then 'N'
																	end
																END) as 'epiCompl/medProtecao'
																,(CASE WHEN gepi.cond_functo IS NOT NULL OR gepi.cond_functo <> '' THEN
																	CASE
																		when gepi.cond_functo = 1 then 'S'
																		when gepi.cond_functo = 0 then 'N'
																	end
																END) as 'epiCompl/condFuncto'
																,(CASE WHEN gepi.uso_epi IS NOT NULL OR gepi.uso_epi <> '' THEN
																	CASE
																		when gepi.uso_epi = 1 then 'S'
																		when gepi.uso_epi = 0 then 'N'
																	end
																END) as 'epiCompl/usoInint'
																,(CASE WHEN gepi.prz_valid IS NOT NULL OR gepi.prz_valid <> '' THEN
																	CASE
																		when gepi.prz_valid = 1 then 'S'
																		when gepi.prz_valid = 0 then 'N'
																	end
																END) as 'epiCompl/przValid'
																,(CASE WHEN gepi.periodic_troca IS NOT NULL OR gepi.periodic_troca <> '' THEN
																	CASE
																		when gepi.periodic_troca = 1 then 'S'
																		when gepi.periodic_troca = 0 then 'N'
																	end
																END) as 'epiCompl/periodicTroca'
																,(CASE WHEN gepi.higienizacao IS NOT NULL OR gepi.higienizacao <> '' THEN
																	CASE
																		when gepi.higienizacao = 1 then 'S'
																		when gepi.higienizacao = 0 then 'N'
																	end
																END) as 'epiCompl/higienizacao'
															from grupos_exposicao_risco ger_epcEpi 
																left join grupos_exposicao_risco_epc gere on gere.codigo_grupos_exposicao_risco = ger_epcEpi.codigo
																left join grupos_exposicao_risco_epi gepi on gepi.codigo_grupos_exposicao_risco = ger_epcEpi.codigo
															WHERE ger_epcEpi.codigo_grupo_exposicao = ge_principal.codigo
																and ger_epcEpi.codigo = ger.codigo
															FOR XML PATH(''))
														, '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '')) 
													END) AS epcEpi
												
												from grupos_exposicao_risco ger 
													INNER join riscos r on r.codigo = ger.codigo_risco
													INNER JOIN esocial e24 on r.codigo_esocial_24 = e24.codigo AND e24.ativo = 1
													-- LEFT JOIN esocial e24AR on e24AR.codigo_descricao = '09.01.001' and e24AR.ativo = 1
													left join grupos_riscos gr on gr.codigo = r.codigo_grupo
													left join tecnicas_medicao_ppra tmp on tmp.codigo = ger.codigo_tec_med_ppra
												WHERE ger.codigo_grupo_exposicao = ge_principal.codigo
													AND ger.codigo_risco IN (SELECT codigo_risco FROM RHHealth.dbo.grupo_exposicao_risco_validacao where codigo_grupo_exposicao = ge_principal.codigo)
												FOR XML PATH('agNoc'))
											, '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', ''))
										END)
									END)
									
									,(select
										m.cpf as cpfResp
										,replace(CASE 
											when cp.descricao like '%CREA%' then 4
											when cp.descricao like '%CRM%' then 1
										else 9
										end, '0', '') as ideOC
										,(CASE 
											WHEN cp.descricao <> 'CREA' AND cp.descricao <> 'CRM' THEN SUBSTRING(cp.descricao,0,20)
										end) as dscOC
										--,SUBSTRING(cp.descricao,0,20) as dscOC
										,SUBSTRING(m.numero_conselho, 0, 14) as nrOC
										,m.conselho_uf as ufOC
									from grupo_exposicao ge
										INNER JOIN clientes_setores cs on cs.codigo = ge.codigo_cliente_setor
										INNER JOIN grupos_prevencao_riscos_ambientais gpra on gpra.codigo_cliente = cs.codigo_cliente
										INNER join medicos m on m.codigo = gpra.codigo_medico
										INNER join conselho_profissional cp on cp.codigo = m.codigo_conselho_profissional
									WHERE ge.codigo = ge_principal.codigo
									FOR XML PATH('')) AS respReg
									
									,(CASE WHEN CAST(ge.observacao AS NVARCHAR(999)) <> '' THEN CAST(ge.observacao AS NVARCHAR(999)) ELSE NULL END) AS 'obs/obsCompl'

								FOR XML PATH('')) 
							, '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '')) AS infoExpRisco

						FROM grupo_exposicao ge 
							inner join clientes_setores cs on cs.codigo = ge.codigo_cliente_setor
							inner join cliente c on c.codigo = cs.codigo_cliente_alocacao
							inner join cargos cargo on cargo.codigo = ge.codigo_cargo
							inner join ordem_servico os on os.codigo_cliente = c.codigo
						WHERE ge.codigo = ge_principal.codigo
						FOR XML PATH('evtExpRisco'))
					, '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', ''))
			FROM grupo_exposicao as ge_principal
				INNER JOIN clientes_setores cs on cs.codigo = ge_principal.codigo_cliente_setor
				INNER JOIN cliente c on c.codigo = cs.codigo_cliente_alocacao

				INNER JOIN funcionario_setores_cargos fsc ON fsc.codigo = (SELECT TOP 1 fscSub.codigo 
																			FROM funcionario_setores_cargos fscSub
																				INNER JOIN cliente_funcionario cfSub on fscSub.codigo_cliente_funcionario = cfSub.codigo
																			WHERE fscSub.codigo_cliente_alocacao = c.codigo
																				AND cfSub.codigo_funcionario = {$codigo_funcionario}
																				AND (fscSub.data_fim IS NULL OR fscSub.data_fim <> '')
																			ORDER BY fscSub.codigo DESC)
				INNER JOIN cliente_funcionario cf ON fsc.codigo_cliente_funcionario = cf.codigo
				INNER JOIN funcionarios f on f.codigo = cf.codigo_funcionario

				INNER JOIN setores s on s.codigo = cs.codigo_setor

			WHERE ge_principal.codigo = {$codigo_grupo_exposicao}
				AND f.codigo = {$codigo_funcionario}
				
			FOR XML PATH('eSocial')), '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '') as text) as val";
		
		// debug($query);
		// exit;
		$val = $this->query($query);
				
		/*$dados = "<?xml version='1.0' encoding='UTF-8'?>".Comum::converterEncodingPara($val[0][0]['val'], 'UTF-8');*/
		
		$dados = "<?xml version='1.0' encoding='UTF-8'?>".utf8_decode(utf8_encode($val[0][0]['val']));

		/*
		 $dadosAgNoc = Comum::tirarAcentos(utf8_encode($val[0][0]['val']));
		 $dados = "<?xml version='1.0' encoding='UTF-8'?>".utf8_decode(utf8_encode($dadosAgNoc));
		*/
		// print $dados;exit;
		
		//deve retirar os acentos pois o esocial nao aceita
		$dados = Comum::tirarAcentos($dados);
		$dados = str_replace("&amp;#x0D;","",$dados);
		$dados = str_replace("\n\r","",$dados);


		// print $dados;exit;
		return $dados;

	}//fim gerar_s2240

	public function getAllS2230ForXml(array $conditions = array()){
		//varre os conditions
    	$conditions['Atestado.codigo_empresa'] = 1;
    	//monta o where
    	$where = $this-> montaWhereForXML($conditions);

    	$query = "
			SELECT
				Atestado.codigo as codigo_atestado,
				FuncionarioSetorCargo.codigo_cliente_alocacao as codigo_cliente_alocacao,
				Cliente.codigo as codigo_cliente,
				Setor.descricao as setor,
				Cargo.descricao as cargo,
				Funcionario.nome as nome_funcionario,
				Funcionario.cpf as cpf_funcionario,
				Funcionario.codigo as codigo_funcionario,
				ClienteFuncionario.matricula as matricula,
				ClienteFuncionario.admissao as dtAdmissao
				,IntEsocialEvento.codigo AS codigo_int_esocial_evento
				,IntEsocialEvento.codigo_int_esocial_status as codigo_int_esocial_status
				,IntEsocialStatus.descricao AS descricao_esocial_status
				,Cliente.nome_fantasia
				,'1' AS tpInsc
				,CASE UPPER(Cliente.tipo_unidade)
					WHEN 'F' THEN Cliente.codigo_documento
					WHEN 'O' THEN Cliente.codigo_documento_real
				END as nrInsc
				,Funcionario.cpf as cpfTrab
				,(CASE WHEN ClienteFuncionario.matricula IS NOT NULL THEN ClienteFuncionario.matricula END) AS matricula
				,(CASE WHEN ClienteFuncionario.matricula IS NULL OR ClienteFuncionario.matricula = '' THEN 
					(CASE WHEN ClienteFuncionario.codigo_esocial_01 IS NOT NULL THEN 
						(SELECT codigo_descricao FROM esocial WHERE codigo = ClienteFuncionario.codigo_esocial_01)
					END)
				END) AS codCateg
				,CONVERT(VARCHAR, Atestado.data_afastamento_periodo,23) AS dtIniAfast
				,Esocial.codigo_descricao as codMotAfast
				,(case 
					when Atestado.codigo_motivo_esocial = 1015 or Atestado.codigo_motivo_esocial = 1017 
					then
						case 
							when Atestado.motivo_afastamento = 'S' then 'S'
							when Atestado.motivo_afastamento = 'N' then 'N'
							when Atestado.motivo_afastamento is null or Atestado.motivo_afastamento = '' then 'N'
						end 
				else 'N' end) as infoMesmoMtv
				,(CASE 
					WHEN Atestado.tipo_acidente_transito <> '' AND (Esocial.codigo_descricao = '01' OR Esocial.codigo_descricao = '03') THEN Atestado.tipo_acidente_transito
					ELSE '' 
				END) AS tpAcidTransito
				,(CASE WHEN CAST(Atestado.obs_afastamento AS VARCHAR) <> '' AND Esocial.codigo_descricao = '21' THEN Atestado.obs_afastamento else '' END) as observacao
				,(case 
					when Atestado.data_inicio_p_aquisitivo IS NOT NULL AND Atestado.codigo_motivo_esocial = 1028 then CONVERT(VARCHAR, Atestado.data_inicio_p_aquisitivo,23) else '' end) AS 'perAquis/dtInicio'	
				,(
					case 
						when Atestado.data_fim_p_aquisitivo IS NOT NULL AND Atestado.codigo_motivo_esocial = 1028 then CONVERT(VARCHAR, Atestado.data_fim_p_aquisitivo,23)  
						else '' end) AS 'perAquis/dtFim'
				,(
					case 
						when Atestado.codigo_documento_entidade <> '' AND Esocial.codigo_descricao = 14 THEN Atestado.codigo_documento_entidade 
					else '' END) as 'infoCessao/cnpjCess'
				,(case 
					when Atestado.onus_requisicao <> '' AND Esocial.codigo_descricao = 14 THEN Atestado.onus_requisicao 
				else '' END) as 'infoCessao/infOnus'
				,(case 
					when Atestado.codigo_documento_entidade <> '' AND Esocial.codigo_descricao = 24 THEN Atestado.codigo_documento_entidade 
					else '' END) as 'infoMandSind/cnpjSind'
				,(case 
					when Atestado.onus_remuneracao <> '' AND Esocial.codigo_descricao = 24 THEN REPLACE(Atestado.onus_remuneracao, 0,'') 
				else '' END) as 'infoMandSind/infOnusRemun'
				,(case when Atestado.codigo_documento_entidade <> '' AND Esocial.codigo_descricao = 22 THEN Atestado.codigo_documento_entidade else '' END) as 'infoMandElet/cnpjSind'
				,(case when Atestado.codigo_documento_entidade <> '' AND Esocial.codigo_descricao = 22 then 
					case	
						when Atestado.renumeracao_cargo is null then 'N'
						when Atestado.renumeracao_cargo = 1 then 'S'
						when Atestado.renumeracao_cargo = 0 then 'N'
					end
				else '' end) as 'infoMandElet/indRemunCargo'

				,(CASE when Atestado.origem_retificacao <> '' AND (Esocial.codigo_descricao = 01 OR Esocial.codigo_descricao = 03) THEN Atestado.origem_retificacao else '' END) as 'infoRetif/origRetif'
				,(CASE WHEN Atestado.tipo_processo <> '' THEN Atestado.tipo_processo else '' END) as 'infoRetif/tpProc'
				,(CASE WHEN Atestado.numero_processo <> '' THEN Atestado.numero_processo else '' END) as 'infoRetif/nrProc'
				,(CASE WHEN Atestado.data_retorno_periodo IS NOT NULL THEN CONVERT(VARCHAR, Atestado.data_retorno_periodo,23) else '' END) AS 'dtTermAfast'
			from atestados Atestado
				inner join funcionario_setores_cargos FuncionarioSetorCargo on FuncionarioSetorCargo.codigo = Atestado.codigo_func_setor_cargo
				inner join cliente_funcionario ClienteFuncionario on ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario
				inner join setores Setor on Setor.codigo = FuncionarioSetorCargo.codigo_setor
				inner join cargos Cargo on Cargo.codigo = FuncionarioSetorCargo.codigo_cargo
				inner join funcionarios Funcionario on Funcionario.codigo = ClienteFuncionario.codigo_funcionario
				inner join cliente Cliente on Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao
				left join esocial Esocial on Esocial.codigo = Atestado.codigo_motivo_esocial
				LEFT JOIN [RHHealth].[dbo].[int_esocial_eventos] AS [IntEsocialEvento] ON [IntEsocialEvento].codigo = (
					select TOP 1 codigo 
					from int_esocial_eventos iee 
					where iee.codigo_int_esocial_tipo_evento IN (3,5)
						AND iee.codigo_registro_sistema = Atestado.codigo
					ORDER BY iee.codigo DESC
				)
				LEFT JOIN [RHHealth].[dbo].[int_esocial_status] AS [IntEsocialStatus] ON IntEsocialEvento.codigo_int_esocial_status = IntEsocialStatus.codigo
			
			where {$where}
			
			ORDER BY Atestado.data_inclusao DESC,
			Funcionario.nome ASC
		";

		// print $query;exit; 
		$val = $this->query($query);
		// debug($val);exit;

		return $val;

	}

	public function gerar_s2230($codigo_atestado, $ambiente = '1') 
	{
		sleep(1);
		
		//monta a query
		$query = "
			SELECT	CAST(
				REPLACE(REPLACE(REPLACE(			
				(select 	
					(select REPLACE(REPLACE(REPLACE(
						(SELECT TOP(1)
							CONCAT('ID1',
								CONCAT( substring(isnull(c.codigo_documento,c.codigo_documento_real),0,9), REPLICATE('0', (14 - LEN(substring(isnull(c.codigo_documento,c.codigo_documento_real),0,9)))) ),
								FORMAT(CURRENT_TIMESTAMP, 'yyyyMMddHHmmss', 'pt-BR'),
								REPLICATE('0', (4)),1) AS \"@Id\"
							,(SELECT 
								'1' as indRetif
								-- ,'' as nrRecibo
								,'1' as tpAmb
								,'1' as procEmi
								,'1' as verProc
							FOR XML PATH('')) AS ideEvento
							,(SELECT
								'1' as tpInsc
								,substring(isnull(c.codigo_documento,c.codigo_documento_real),0,9) as nrInsc
							FROM atestados atestado
								INNER JOIN funcionario_setores_cargos fsc ON fsc.codigo = atestado.codigo_func_setor_cargo
								INNER JOIN cliente_funcionario cf ON cf.codigo = fsc.codigo_cliente_funcionario
								INNER JOIN cliente c ON c.codigo = fsc.codigo_cliente_alocacao
							WHERE atestado.codigo = atestado_principal.codigo
							FOR XML PATH('')) AS ideEmpregador
							,(SELECT
								f.cpf as cpfTrab
								,(CASE WHEN (select codigo_descricao FROM esocial WHERE codigo = cf.codigo_esocial_01) IS NULL
									THEN (CASE WHEN cf.matricula <> '' THEN TRIM(cf.matricula) END)
								END) as matricula
								,(select codigo_descricao FROM esocial WHERE codigo = cf.codigo_esocial_01) as codCateg

							FROM atestados atestado
								INNER JOIN funcionario_setores_cargos fsc ON fsc.codigo = atestado.codigo_func_setor_cargo
								INNER JOIN cliente_funcionario cf ON cf.codigo = fsc.codigo_cliente_funcionario
								INNER JOIN funcionarios f ON cf.codigo_funcionario = f.codigo
							WHERE atestado.codigo = atestado_principal.codigo
							FOR XML PATH('')) AS ideVinculo

							,(select REPLACE(REPLACE(REPLACE(
								(select 
									(select REPLACE(REPLACE(REPLACE(
										(SELECT
											CONVERT(VARCHAR, atestado.data_afastamento_periodo,23) AS dtIniAfast
											,esocial.codigo_descricao as codMotAfast
											,(case 
												when atestado.codigo_motivo_esocial = 1015 or atestado.codigo_motivo_esocial = 1017 
												then
													case 
														when atestado.motivo_afastamento = 'S' then 'S'
														when atestado.motivo_afastamento = 'N' then 'N'
														when atestado.motivo_afastamento is null or atestado.motivo_afastamento = '' then 'N'
													end 
											else 'N' end) as infoMesmoMtv
											,(CASE 
												WHEN atestado.tipo_acidente_transito <> '' AND (esocial.codigo_descricao = '01' OR esocial.codigo_descricao = '03') THEN atestado.tipo_acidente_transito												
											END) AS tpAcidTransito
											,(CASE WHEN CAST(atestado.obs_afastamento AS VARCHAR) <> '' AND esocial.codigo_descricao = '21' THEN atestado.obs_afastamento END) as observacao
											
											,(case when atestado.data_inicio_p_aquisitivo IS NOT NULL AND atestado.codigo_motivo_esocial = 1028 then CONVERT(VARCHAR, atestado.data_inicio_p_aquisitivo,23) end) AS 'perAquis/dtInicio'											
											,(case when atestado.data_fim_p_aquisitivo IS NOT NULL AND atestado.codigo_motivo_esocial = 1028 then CONVERT(VARCHAR, atestado.data_fim_p_aquisitivo,23) end) AS 'perAquis/dtFim'
											
											,(case when atestado.codigo_documento_entidade <> '' AND esocial.codigo_descricao = 14 THEN atestado.codigo_documento_entidade END) as 'infoCessao/cnpjCess'
											,(case when atestado.onus_requisicao <> '' AND esocial.codigo_descricao = 14 THEN atestado.onus_requisicao END) as 'infoCessao/infOnus'

											,(case when atestado.codigo_documento_entidade <> '' AND esocial.codigo_descricao = 24 THEN atestado.codigo_documento_entidade END) as 'infoMandSind/cnpjSind'
											,(case when atestado.onus_remuneracao <> '' AND esocial.codigo_descricao = 24 THEN REPLACE(atestado.onus_remuneracao, 0,'') END) as 'infoMandSind/infOnusRemun'

											,(case when atestado.codigo_documento_entidade <> '' AND esocial.codigo_descricao = 22 THEN atestado.codigo_documento_entidade END) as 'infoMandElet/cnpjSind'
											,(case when atestado.codigo_documento_entidade <> '' AND esocial.codigo_descricao = 22 then 
													case	
														when atestado.renumeracao_cargo is null then 'N'
														when atestado.renumeracao_cargo = 1 then 'S'
														when atestado.renumeracao_cargo = 0 then 'N'
													end
												end) as 'infoMandElet/indRemunCargo'
										FROM atestados atestado
											LEFT JOIN esocial ON esocial.codigo = atestado.codigo_motivo_esocial
										WHERE atestado.codigo = atestado_principal.codigo
										FOR XML PATH('')) 
									, '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '')) AS iniAfastamento

									,(CASE when atestado.origem_retificacao <> '' AND (esocialPrincipal.codigo_descricao = 01 OR esocialPrincipal.codigo_descricao = 03) THEN atestado.origem_retificacao END) as 'infoRetif/origRetif'
									,(CASE WHEN atestado.tipo_processo <> '' THEN atestado.tipo_processo END) as 'infoRetif/tpProc'
									,(CASE WHEN atestado.numero_processo <> '' THEN atestado.numero_processo END) as 'infoRetif/nrProc'

									,(CASE WHEN atestado.data_retorno_periodo IS NOT NULL THEN CONVERT(VARCHAR, atestado.data_retorno_periodo,23) END) AS 'fimAfastamento/dtTermAfast'

								FOR XML PATH('')) 
							, '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '')) AS infoAfastamento


						FROM atestados atestado
							INNER JOIN funcionario_setores_cargos fsc ON fsc.codigo = atestado.codigo_func_setor_cargo
							INNER JOIN cliente_funcionario cf ON cf.codigo = fsc.codigo_cliente_funcionario
							INNER JOIN cliente c ON c.codigo = fsc.codigo_cliente_alocacao
							left join esocial esocialPrincipal on esocialPrincipal.codigo = atestado.codigo_motivo_esocial
						WHERE atestado.codigo = atestado_principal.codigo
						FOR XML PATH('evtAfastTemp')) 
					, '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', ''))
				from atestados as atestado_principal
				where atestado_principal.codigo = ".$codigo_atestado."
				FOR XML PATH('eSocial')), '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '') as text) as val";
		
		// debug($query);exit;
		$val = $this->query($query);
		// debug($val);exit;
		/*$dados = "<?xml version='1.0' encoding='UTF-8'?>".Comum::converterEncodingPara($val[0][0]['val'], 'UTF-8');*/
		$dados = "<?xml version='1.0' encoding='UTF-8'?>".utf8_decode(utf8_encode($val[0][0]['val']));
		
		//deve retirar os acentos pois o esocial nao aceita
		$dados = Comum::tirarAcentos($dados);
		$dados = str_replace("&amp;#x0D;","",$dados);
		$dados = str_replace("\n\r","",$dados);

		// print $dados;exit;
		return $dados;

	}//fim gerar_s2230

	public function getTabela01(){

		$categoria_colabador = array();
		
		$cat_col = $this->find('all',array('conditions' => array('tabela'=> 1)));

		foreach($cat_col as $dado){
			$categoria_colabador[$dado['Esocial']['codigo']] = $dado['Esocial']['codigo_descricao'] . ' - ' . str_replace('', '-', $dado['Esocial']['descricao']);
		}

		return $categoria_colabador;
	}

	public function montaWhereForXML($conditions) 
	{
		
		$where= array();

		// debug($conditions);exit;

		foreach($conditions AS $keys => $vals) {

    		if(strpos($keys, "=")) {
    			$where[] = "{$keys}'{$vals}'";
    		}
    		else if(strpos($keys, "LIKE")) {
    			$where[] = "{$keys} '{$vals}'";
    		}
    		else if(is_integer($keys)) {
    			$where[] = "{$vals}";
    		}
    		else if(is_array($vals)) {
    			$valsIn = implode(",",$vals);
    			$where[] = "{$keys} IN ( {$valsIn} )";
    		}
    		else {
    			$where[] = "{$keys} = '{$vals}'";
    		}
    	}//fim foreach
    	//monta o where
    	$where = implode(' AND ', $where);

    	return $where;
	}

	/**
	 * Metodo para validar o layout com os  valores indicados no mesmo
	 */
	public function valida_regra_campos_s2220($dados)
	{

		$dado = $dados[0];
		$validacao = array();

		//inicio da validação dos campos conforme o layout: https://www.gov.br/esocial/pt-br/documentacao-tecnica/leiautes-esocial-html/index.html#r_2220_eSocial
		###ideEmpregador###
		if(empty($dado['tpInsc'])) {
			$validacao[] = array(
				"titulo" => 'ideEmpregador',
				"descricao" => "Campo ideEmpregador/tpInsc obrigatório, com os valores 1 -> CNPJ ou 2 -> CPF!"
			);
		}

		if(empty($dado['nrInsc'])) {
			$validacao[] = array(
				"titulo" => 'ideEmpregador',
				"descricao" => "Campo ideEmpregador/nrInsc obrigatório: 	Informar o número de inscrição do contribuinte de acordo com o tipo de inscrição indicado no campo ideEmpregador/tpInsc"
			);
		}
		
		if($dado['tpInsc'] == 1 && (strlen($dado['nrInsc']) < 14 || strlen($dado['nrInsc']) > 14) ) {
			$validacao[] = array(
				"titulo" => 'ideEmpregador',
				"descricao" => "Campo ideEmpregador/nrInsc informar o CPNJ do contribuinte"
			);
		}
		###FIM ideEmpregador###
		
		####ideVinculo###
		if(empty($dado['cpfTrab'])) {
			$validacao[] = array(
				"titulo" => 'ideVinculo',
				"descricao" => "Campo ideVinculo/cpfTrab obrigatório: Preencher com o número do CPF do trabalhador."
			);
		}

		if(trim($dado['matricula']) == '' && trim($dado['codCateg']) == '') {
			$validacao[] = array(
				"titulo" => 'ideVinculo',
				"descricao" => "Campo ideVinculo/matricula ou ideVinculo/codCateg obrigatório: Matrícula atribuída ao trabalhador pela empresa / Preencher com o código da categoria do trabalhador."
			);
		}
		
		####FIM ideVinculo###
		
		####exMedOcup###
		if(trim($dado['tpExameOcup']) == '') {
			$validacao[] = array(
				"titulo" => 'exMedOcup',
				"descricao" => "Campo exMedOcup/tpExameOcup obrigatório: Tipo do exame médico ocupacional."
			);
		}
		####FIM exMedOcup###

		####aso###
		if(empty($dado['dtAso'])) {
			$validacao[] = array(
				"titulo" => 'aso',
				"descricao" => "Campo aso/dtAso obrigatório: Data de emissão do ASO."
			);
		}

		if(isset($dado['resAso'])) {
			if($dado['resAso'] == "") {
				$validacao[] = array(
					"titulo" => 'aso',
					"descricao" => "Campo aso/resAso não obrigatório: Resultado do ASO, Valores válidos 1 - Apto, 2 - Inapto."
				);
			}
		}

		####exame###
		if(empty($dado['exame'])) {
			$validacao[] = array(
				"titulo" => 'aso/exame',
				"descricao" => "Grupo de exame é obrigatório: Grupo que detalha as avaliações clínicas e os exames complementares porventura realizados pelo trabalhador em virtude do determinado nos Quadros I e II da NR-07, além de outros solicitados pelo médico e os referentes ao ASO."
			);
		}
		else {
			//leitura do xml transformando em objeto os exames do aso
			$read_xml_exames = simplexml_load_string(utf8_decode(utf8_encode($dado['exame'])));
			$contador = 0;
			foreach($read_xml_exames AS $obj) {
				//parsea o obj para array
				$ex = (array)$obj;
				
				if(empty($ex['dtExm'])) {
					$validacao[] = array(
						"titulo" => 'aso/exame: '.$ex['desc_exame'],
						"descricao" => "Campo aso/exame/dtExm obrigatório: Data do exame realizado."
					);
				}
				else {
					if($ex['dtExm'] > $dado['dtAso']) {
						$validacao[] = array(
							"titulo" => 'aso/exame: '.$ex['desc_exame'],
							"descricao" => "Campo aso/exame/dtExm: Deve ser uma data válida, igual ou anterior à data do ASO informada em dtAso."
						);
					}
				}

				if(empty($ex['procRealizado'])) {
					$validacao[] = array(
						"titulo" => 'aso/exame: '.$ex['desc_exame'],
						"descricao" => "Campo aso/exame/procRealizado obrigatório: Código do procedimento diagnóstico."
					);
				}

				if(empty($ex['procRealizado'])) {
					$validacao[] = array(
						"titulo" => 'aso/exame: '.$ex['desc_exame'],
						"descricao" => "Campo aso/exame/procRealizado obrigatório: Código do procedimento diagnóstico."
					);
				}
				else {
					/*
					ajuste do dia 01/10/2021
					verifica se o resultado da tabela 27
					Observação sobre o procedimento diagnóstico realizado.
					Validação: Preenchimento obrigatório se procRealizado = [0583, 0998, 0999, 1128, 1230, 1992, 1993, 1994, 1995,1996, 1997, 1998, 1999, 9999].
					 */
					$validacao_proc_realizado = array(0583, 0998, 0999, 1128, 1230, 1992, 1993, 1994, 1995,1996, 1997, 1998, 1999, 9999);
					if(in_array($ex['procRealizado'], $validacao_proc_realizado)) {
						if(!isset($ex['obsProc'])) {
							$validacao[] = array(
								"titulo" => 'aso/exame: '.$ex['desc_exame'],
								"descricao" => "Campo aso/exame/obsProc obrigatório: Observação sobre o procedimento diagnóstico realizado.	<b>Validação:</b> Preenchimento obrigatório se procRealizado = [0583, 0998, 0999, 1128, 1230, 1992, 1993, 1994, 1995,1996, 1997, 1998, 1999, 9999]."
							);
						}
						else {
							if(empty($ex['obsProc'])) {
								$validacao[] = array(
									"titulo" => 'aso/exame: '.$ex['desc_exame'],
									"descricao" => "Campo aso/exame/obsProc obrigatório: Observação sobre o procedimento diagnóstico realizado.	<b>Validação:</b> Preenchimento obrigatório se procRealizado = [0583, 0998, 0999, 1128, 1230, 1992, 1993, 1994, 1995,1996, 1997, 1998, 1999, 9999]."
								);
							}// fim verificacao obrigatorio
						}

					}

				}// fim procRealizado

				if(isset($ex['obsProc'])) {
					if(empty($ex['obsProc'])) {
						$validacao[] = array(
							"titulo" => 'aso/exame: '.$ex['desc_exame'],
							"descricao" => "Campo aso/exame/obsProc não obrigatório: Observação sobre o procedimento diagnóstico realizado.	<b>Validação:</b> Preenchimento obrigatório se procRealizado = [0583, 0998, 0999, 1128, 1230, 1992, 1993, 1994, 1995,1996, 1997, 1998, 1999, 9999]."
						);
					}
				}

				if(empty($ex['ordExame'])) {
					$validacao[] = array(
						"titulo" => 'aso/exame: '.$ex['desc_exame'],
						"descricao" => "Campo aso/exame/ordExame obrigatório: Ordem do exame. O campo somente deve ser obrigatório no caso de audiometria tonal ocupacional."
					);
				}

				if(isset($ex['indResult'])) {
					if(empty($ex['indResult'])) {
						$validacao[] = array(
							"titulo" => 'aso/exame: '.$ex['desc_exame'],
							"descricao" => "Campo aso/exame/indResult opcional e não pode estar em branco: Indicação dos resultados. Valores válidos: 1 - Normal 2 - Alterado 3 - Estável 4 - Agravamento"
						);
					}
				}

				$contador++;

			}//fim foreach
		}//fim else		
		####FIM exame###

		####medico###
		if(empty($dado['nmMed'])) {
			$validacao[] = array(
				"titulo" => 'aso/medico',
				"descricao" => "Campo aso/medico/nmMed obrigatório: Preencher com o nome do médico emitente do ASO."
			);
		}

		if(empty($dado['nrCRM'])) {
			$validacao[] = array(
				"titulo" => 'aso/medico',
				"descricao" => "Campo aso/medico/nrCRM obrigatório: Número de inscrição do médico emitente do ASO no Conselho Regional de Medicina - CRM."
			);
		}

		if(empty($dado['ufCRM'])) {
			$validacao[] = array(
				"titulo" => 'aso/medico',
				"descricao" => "Campo aso/medico/ufCRM obrigatório: Preencher com a sigla da Unidade da Federação - UF de expedição do CRM."
			);
		}

		####FIM medico###
		####fim aso###

		####respMonit###
		if(isset($dado['pcmso_cpfResp'])) {
			if(empty($dado['pcmso_cpfResp'])) {
				$validacao[] = array(
					"titulo" => 'respMonit',
					"descricao" => "Campo respMonit/cpfResp opcional e não pode estar em branco: Preencher com o CPF do médico responsável/coordenador do PCMSO."
				);
			}
		}

		if(empty($dado['pcmso_nmResp'])) {
			$validacao[] = array(
				"titulo" => 'respMonit',
				"descricao" => "Campo respMonit/nmMed obrigatório: Preencher com o nome do médico responsável/coordenador do PCMSO."
			);
		}

		if(empty($dado['pcmso_nrCRM'])) {
			$validacao[] = array(
				"titulo" => 'respMonit',
				"descricao" => "Campo respMonit/nrCRM obrigatório: Número de inscrição do médico responsável/coordenador do PCMSO no CRM."
			);
		}

		if(empty($dado['pcmso_ufCRM'])) {
			$validacao[] = array(
				"titulo" => 'respMonit',
				"descricao" => "Campo respMonit/ufCRM obrigatório: Preencher com a sigla da Unidade da Federação - UF de expedição do CRM."
			);
		}

		####FIM respMonit###
		
		// debug($dado);
		// debug($validacao);
		// exit;

		return $validacao;

	}//fim

	/**
	 * Metodo para validar o layout com os  valores indicados no mesmo
	 */
	public function valida_regra_campos_s2230($dados)
	{

		$dado = $dados;
		// debug($dado);
		$validacao = array();

		//inicio da validação dos campos conforme o layout: https://www.gov.br/esocial/pt-br/documentacao-tecnica/leiautes-esocial-html/index.html#evtAfastTemp
		###ideVinculo###
		if(empty($dado['cpfTrab'])) {
			$validacao[] = array(
				"titulo" => 'ideVinculo',
				"descricao" => "Campo ideVinculo/cpfTrab deve preencher com o número do CPF do trabalhador."
			);
		}

		if(empty($dado['matricula'])) {
			$validacao[] = array(
				"titulo" => 'ideVinculo',
				"descricao" => "Campo ideVinculo/matricula ou ideVinculo/codCateg obrigatório: Matrícula atribuída ao trabalhador pela empresa / Preencher com o código da categoria do trabalhador."
			);
		}
		###FIM ideVinculo###

		###iniAfastamento###
		if(empty($dado['dtIniAfast']) || $dado['codMotAfast'] == 15 || $dado['codMotAfast'] == 18) {

			$Date = $dado['dtIniAfast'];
	
			if ($dado['codMotAfast'] == 15) {
				$data15 = date('Y-m-d', strtotime($Date. ' + 60 days'));

				if (date('Y-m-d') < $data15) {
					$validacao[] = array(
						"titulo" => 'iniAfastamento',
						"descricao" => "Campo iniAfastamento/dtIniAfast deve-se obedecer às seguintes regras:
						<br>a) Não pode ser posterior à data atual, exceto se:
						<br>a1) codMotAfast = [15] (férias), situação em que pode ser até 60 dias posterior à data atual;
						<br>b) É necessário que o trabalhador esteja, antes da data de início do afastamento, em atividade, ou seja, não pode existir evento de afastamento anterior a dtIniAfast sem que este tenha sido encerrado."
					);
				}
			}
		
			if ($dado['codMotAfast'] == 18) {
				$data18 = date('Y-m-d', strtotime($Date. ' + 120 days'));
				
				if (date('Y-m-d') < $data18) {
					$validacao[] = array(
						"titulo" => 'iniAfastamento',
						"descricao" => "Campo iniAfastamento/dtIniAfast deve-se obedecer às seguintes regras:
						<br>a) Não pode ser posterior à data atual, exceto se:
						<br>a2) codMotAfast = [18], situação em que pode ser até 120 dias posterior à data atual;
						<br>b) É necessário que o trabalhador esteja, antes da data de início do afastamento, em atividade, ou seja, não pode existir evento de afastamento anterior a dtIniAfast sem que este tenha sido encerrado."
					);
				}
			}

			if (empty($dado['dtIniAfast'])) {
				$validacao[] = array(
					"titulo" => 'iniAfastamento',
					"descricao" => "Campo iniAfastamento/dtIniAfast é obrigatório e deve-se obedecer às seguintes regras:
					<br>a) Não pode ser posterior à data atual, exceto se:
					<br>a1) codMotAfast = [15] (férias), situação em que pode ser até 60 dias posterior à data atual;
					<br>a2) codMotAfast = [18], situação em que pode ser até 120 dias posterior à data atual;
					<br>b) É necessário que o trabalhador esteja, antes da data de início do afastamento, em atividade, ou seja, não pode existir evento de afastamento anterior a dtIniAfast sem que este tenha sido encerrado."
				);
			}
		} else {

			if ($dado['dtIniAfast'] > date('Y-m-d')) {
				$validacao[] = array(
					"titulo" => 'iniAfastamento',
					"descricao" => "Campo iniAfastamento/dtIniAfast é obrigatório e deve-se obedecer às seguintes regras:
					<br>a) Não pode ser posterior à data atual, exceto se:
					<br>a1) codMotAfast = [15] (férias), situação em que pode ser até 60 dias posterior à data atual;
					<br>a2) codMotAfast = [18], situação em que pode ser até 120 dias posterior à data atual;"
				);
			}
		}

		if(empty($dado['codMotAfast'])) {

			$validacao[] = array(
				"titulo" => 'iniAfastamento',
				"descricao" => "Campo iniAfastamento/codMotAfast deve ser um código válido e existente na <a href='https://www.gov.br/esocial/pt-br/documentacao-tecnica/leiautes-esocial-html/tabelas.html#18' target='_blank'>Tabela 18.</a>"
			);
		} else {

			$tabela_18 = $this->find('list', array( 'fields' => array('codigo', 'codigo_descricao'), 'conditions' => array('tabela' => 18, 'ativo' => 1), 'order' => array('codigo_descricao ASC')));

			$errQtd = 0;

			if(!in_array($dado['codMotAfast'], $tabela_18)){
				$errQtd = 1;
			}

			if ($errQtd == 1) {
				$validacao[] = array(
					"titulo" => 'iniAfastamento',
					"descricao" => "Campo iniAfastamento/codMotAfast deve ser um código válido e existente na <a href='https://www.gov.br/esocial/pt-br/documentacao-tecnica/leiautes-esocial-html/tabelas.html#18' target='_blank'>Tabela 18.</a>"
				);
			}
		}

		
		if(trim($dado['infoMesmoMtv']) == '') {
			
			$validacao[] = array(
				"titulo" => 'iniAfastamento',
				"descricao" => "Campo iniAfastamento/infoMesmoMtv valores válidos:
				\nS - Sim
				\nN - Não"
			);
		}

		###FIM iniAfastamento###

		###fimAfastamento###
		if(empty($dado['dtTermAfast'])) {
			$validacao[] = array(
				"titulo" => 'fimAfastamento',
				"descricao" => "Deve ser igual ou posterior à data de início do afastamento do trabalhador."
			);
		}
		###FIM fimAfastamento###

		// debug($dado);
		// debug($validacao);
		// exit;
		// pr($validacao);
		return $validacao;

	}//fim

	/**
	 * Metodo para validar o layout do esocial s-2240
	 * https://www.gov.br/esocial/pt-br/documentacao-tecnica/leiautes-esocial-html/index.html#r_2240_eSocial
	 * 
	 */
	public function valida_regra_campos_s2240($dados)
	{

		$dado = $dados[0];
		$validacao = array();

		// debug($dado);exit;

		###ideEmpregador###
		if(empty($dado['tpInsc'])) {
			$validacao[] = array(
				"titulo" => 'ideEmpregador',
				"descricao" => "Campo evtExpRisco/ideEmpregador/tpInsc obrigatório, com os valores 1 -> CNPJ ou 2 -> CPF!"
			);
		}

		if(empty($dado['nrInsc'])) {
			$validacao[] = array(
				"titulo" => 'ideEmpregador',
				"descricao" => "Campo evtExpRisco/ideEmpregador/nrInsc obrigatório: 	Informar o número de inscrição do contribuinte de acordo com o tipo de inscrição indicado no campo ideEmpregador/tpInsc"
			);
		}
		
		if($dado['tpInsc'] == 1 && (strlen($dado['nrInsc']) < 14 || strlen($dado['nrInsc']) > 14) ) {
			$validacao[] = array(
				"titulo" => 'ideEmpregador',
				"descricao" => "Campo evtExpRisco/ideEmpregador/nrInsc informar o CPNJ do contribuinte"
			);
		}
		###FIM ideEmpregador###
		
		#####ideVinculo###
		if(empty($dado['cpfTrab'])) {
			$validacao[] = array(
				"titulo" => 'ideVinculo',
				"descricao" => "Campo evtExpRisco/ideVinculo/cpfTrab obrigatório: Preencher com o número do CPF do trabalhador."
			);
		}

		if(trim($dado['matricula']) == '' && trim($dado['codCateg']) == '') {
			$validacao[] = array(
				"titulo" => 'ideVinculo',
				"descricao" => "Campo evtExpRisco/ideVinculo/matricula ou ideVinculo/codCateg obrigatório: Matrícula atribuída ao trabalhador pela empresa / Preencher com o código da categoria do trabalhador."
			);
		}
		####FIM ideVinculo###

		####infoExpRisco###
		if(trim($dado['dtIniCondicao']) == '') {
			$validacao[] = array(
				"titulo" => 'infoExpRisco',
				"descricao" => "Campo evtExpRisco/infoExpRisco/dtIniCondicao obrigatório: <b>Informar a data em que o trabalhador iniciou as atividades nas condições descritas ou a data de início da obrigatoriedade deste evento para o empregador no eSocial, a que for mais recente.</b> <b>VALIDAÇÂO:</b> Deve ser uma data válida, igual ou posterior à data de admissão do vínculo a que se refere. Não pode ser anterior à data de início da obrigatoriedade deste evento para o empregador no eSocial, nem pode ser posterior a 30 (trinta) dias da data atual. "
			);
		}
		else {
			
			
			//retirado a criterio do time de negocio data do dia 15/07/2022
			//
			// if($dado['dtAdmissao'] > $dado['dtIniCondicao']) {
			// 	$validacao[] = array(
			// 		"titulo" => 'infoExpRisco',
			// 		"descricao" => "Campo evtExpRisco/infoExpRisco/dtIniCondicao: Informar a data em que o trabalhador iniciou as atividades nas condições descritas ou a data de início da obrigatoriedade deste evento para o empregador no eSocial, a que for mais recente. <b>VALIDAÇÂO:</b> Deve ser uma data válida, <b>igual ou posterior à data de admissão do vínculo a que se refere.</b> Não pode ser anterior à data de início da obrigatoriedade deste evento para o empregador no eSocial, nem pode ser posterior a 30 (trinta) dias da data atual. "
			// 	);
			// }

			/*$data_base_esocial = '2021-10-13';
			$data_corte_grupo_empresas = array(
	    		'1' => '2021-10-13',
	    		'2' => '2022-01-10',
	    		'3' => '2022-01-10',
	    		'4' => '2022-07-11'
	    	);*/

			// Calcula a data daqui 30 dias
			$timestamp_30_dias = strtotime("+30 days");
			$trinta_dias_data_atual = date('Y-m-d', $timestamp_30_dias);

			// if($dado['dtIniCondicao'] <= $data_base_esocial || $dado['dtIniCondicao'] > $trinta_dias_data_atual) {
			if($dado['dtIniCondicao'] > $trinta_dias_data_atual) {
				$validacao[] = array(
					"titulo" => 'infoExpRisco',
					"descricao" => "Campo evtExpRisco/infoExpRisco/dtIniCondicao (calculo dos 30 dias): Informar a data em que o trabalhador iniciou as atividades nas condições descritas ou a data de início da obrigatoriedade deste evento para o empregador no eSocial, a que for mais recente. <b>VALIDAÇÂO:</b> Deve ser uma data válida, igual ou posterior à data de admissão do vínculo a que se refere. <b>Não pode ser anterior à data de início da obrigatoriedade deste evento para o empregador no eSocial, nem pode ser posterior a 30 (trinta) dias da data atual.</b>"
				);
			}

		}//fim dtIniCondicao

		####FIM infoExpRisco###
		
		####infoAmb###
		if(empty($dado['localAmb'])) {
			$validacao[] = array(
				"titulo" => 'infoAmb',
				"descricao" => "Campo evtExpRisco/infoExpRisco/infoAmb/localAmb obrigatório: Informar o tipo de estabelecimento do ambiente de trabalho.<b>Valores válidos:</b> 1 - Estabelecimento do próprio empregador / 2 - Estabelecimento de terceiros"
			);
		}

		if(empty($dado['dscSetor'])) {
			$validacao[] = array(
				"titulo" => 'infoAmb',
				"descricao" => "Campo evtExpRisco/infoExpRisco/infoAmb/dscSetor obrigatório: Descrição do lugar administrativo, na estrutura organizacional da empresa, onde o trabalhador exerce suas atividades laborais."
			);
		}

		if(empty($dado['tpInsc_infoAmb'])) {
			$validacao[] = array(
				"titulo" => 'infoAmb',
				"descricao" => "Campo evtExpRisco/infoExpRisco/infoAmb/tpInsc obrigatório: Preencher com o código correspondente ao tipo de inscrição, conforme Tabela 05.<b>Valores válidos:</b>1 - CNPJ/3 - CAEPF/4 - CNO"
			);
		}

		## todo: verificar se temos mais alguma alteração
		if(empty($dado['nrInsc_infoAmb'])) {
			$validacao[] = array(
				"titulo" => 'infoAmb',
				"descricao" => "Campo evtExpRisco/infoExpRisco/infoAmb/nrInsc obrigatório: Número de inscrição onde está localizado o ambiente. <b>Validação:</b> Deve ser um identificador válido, compatível com o conteúdo do campo infoAmb/tpInsc e: a) Se localAmb = [1], deve ser válido e existente na Tabela de Estabelecimentos (S-1005); b) Se localAmb = [2], deve ser diferente dos estabelecimentos informados na Tabela S-1005 e, se infoAmb/tpInsc = [1] e o empregador for pessoa jurídica, a raiz do CNPJ informado deve ser diferente da constante em S-1000."
			);
		}
		####FIM infoAmb###
		
		####infoAtiv###
		if(empty($dado['dscAtivDes']) || trim($dado['dscAtivDes']) == '') {
			$validacao[] = array(
				"titulo" => 'infoAtiv',
				"descricao" => "Campo evtExpRisco/infoExpRisco/infoAtiv/dscAtivDes obrigatório: Descrição das atividades, físicas ou mentais, realizadas pelo trabalhador, por força do poder de comando a que se submete. As atividades deverão ser escritas com exatidão, e de forma sucinta, com a utilização de verbos no infinitivo impessoal. Ex.: Distribuir panfletos, operar máquina de envase, etc."
			);
		}

		####FIM infoAtiv###
		
		####agNoc###
		$dadosAgNoc = Comum::tirarAcentos(utf8_encode($dado['agNoc_']));
		// debug($dadosAgNoc);exit;

		$read_xml_agNoc = simplexml_load_string($dadosAgNoc);
		if(empty($read_xml_agNoc)) {
			$validacao[] = array(
				"titulo" => 'agNoc',
				"descricao" => "Grupo de evtExpRisco/infoExpRisco/agNoc é obrigatório: Agente(s) nocivo(s) ao(s) qual(is) o trabalhador está exposto."
			);
		}
		else {
			//leitura do xml transformando em objeto array
			// $read_xml = simplexml_load_string(utf8_decode(utf8_encode($dado['agNoc_'])));
			$contador = 1;
			foreach($read_xml_agNoc AS $obj) {

				//parsea o obj para array
				$arr_dado = (array)$obj;

				// debug($arr_dado);

				if(empty($arr_dado['codAgNoc'])) {
					$validacao[] = array(
						"titulo" => 'agNoc/codAgNoc: ' . $arr_dado['risco'],
						"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/codAgNoc é obrigatório: Informar o código do agente nocivo ao qual o trabalhador está exposto. Preencher com números e pontos. Caso não haja exposição, informar o código [09.01.001] (Ausência de agente nocivo ou de atividades previstas no Anexo IV do Decreto 3.048/1999). <b>Validação:</b> Deve ser um código válido e existente na Tabela 24. Não é possível informar nenhum outro código de agente nocivo quando houver o código [09.01.001]."
					);
				}
				else {
					//verifica se tem ausência de risco
					if($arr_dado['codAgNoc'] == '09.01.001' && $contador > 1) {
						$validacao[] = array(
							"titulo" => 'agNoc/codAgNoc: ' . $arr_dado['risco'],
							"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/codAgNoc é obrigatório: Informar o código do agente nocivo ao qual o trabalhador está exposto. Preencher com números e pontos. Caso não haja exposição, informar o código [09.01.001] (Ausência de agente nocivo ou de atividades previstas no Anexo IV do Decreto 3.048/1999). <b>Validação:</b> Deve ser um código válido e existente na Tabela 24. <b>Não é possível informar nenhum outro código de agente nocivo quando houver o código [09.01.001]</b>."
						);
						break;
					}//fim verificacao se tem o codigo ausencia de risco
				}//fim codAgNoc

				if(isset($arr_dado['dscAgNoc'])) {
					if(trim($arr_dado['dscAgNoc']) == '') {
						$validacao[] = array(
							"titulo" => 'agNoc/dscAgNoc: ' . $arr_dado['risco'],
							"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/dscAgNoc é opcional: Descrição do agente nocivo. <b>Validação:</b> Preenchimento obrigatório se codAgNoc = [01.01.001, 01.02.001, 01.03.001, 01.04.001, 01.05.001, 01.06.001, 01.07.001, 01.08.001, 01.09.001, 01.10.001, 01.12.001, 01.13.001, 01.14.001, 01.15.001, 01.16.001, 01.17.001, 01.18.001, 05.01.001]."
						);
					}
				}
				else {
					switch($arr_dado['codAgNoc']) {
						case '01.01.001':
						case '01.02.001':
						case '01.03.001':
						case '01.04.001':
						case '01.05.001':
						case '01.06.001':
						case '01.07.001':
						case '01.08.001':
						case '01.09.001':
						case '01.10.001':
						case '01.12.001':
						case '01.13.001':
						case '01.14.001':
						case '01.15.001':
						case '01.16.001':
						case '01.17.001':
						case '01.18.001':
						case '05.01.001':
							if(!isset($arr_dado['dscAgNoc'])) {
								$validacao[] = array(
									"titulo" => 'agNoc/dscAgNoc: ' . $arr_dado['risco'],
									"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/dscAgNoc se tornou obrigatório: Descrição do agente nocivo. <b>Validação:</b> <b>Preenchimento obrigatório se codAgNoc = [01.01.001, 01.02.001, 01.03.001, 01.04.001, 01.05.001, 01.06.001, 01.07.001, 01.08.001, 01.09.001, 01.10.001, 01.12.001, 01.13.001, 01.14.001, 01.15.001, 01.16.001, 01.17.001, 01.18.001, 05.01.001]</b>."
								);
							}
							else {
								if(trim($arr_dado['dscAgNoc']) == '') {
									$validacao[] = array(
										"titulo" => 'agNoc/dscAgNoc: ' . $arr_dado['risco'],
										"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/dscAgNoc se tornou obrigatório: Descrição do agente nocivo. <b>Validação:</b> <b>Preenchimento obrigatório se codAgNoc = [01.01.001, 01.02.001, 01.03.001, 01.04.001, 01.05.001, 01.06.001, 01.07.001, 01.08.001, 01.09.001, 01.10.001, 01.12.001, 01.13.001, 01.14.001, 01.15.001, 01.16.001, 01.17.001, 01.18.001, 05.01.001]</b>."
									);
								}
							}

							break;
					}//fim switch
				}//fim validacao dscAgNoc

				/*
				O campo não deve ser preenchido no caso 
				de ausência de agente nocivo ou de 
				atividades previstas no Anexo IV do 
				Decreto 3.048/1999.
				 */
				if($arr_dado['codAgNoc'] != '09.01.001') {


					if(empty($arr_dado['tpAval'])) {
						$validacao[] = array(
							"titulo" => 'agNoc/tpAval: ' . $arr_dado['risco'],
							"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/tpAval é obrigatório: Tipo de avaliação do agente nocivo.<b>Valores válidos:</b>1 - Critério quantitativo/2 - Critério qualitativo"
						);
					}
					else if($arr_dado['tpAval'] == 1)  {
							
						if(!isset($arr_dado['intConc'])) {
								$validacao[] = array(
									"titulo" => 'agNoc/intConc: ' . $arr_dado['risco'],
									"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/intConc se tornou obrigatório: Intensidade, concentração ou dose da exposição do trabalhador ao agente nocivo cujo critério de avaliação seja quantitativo.<b>Validação:</b> Preenchimento obrigatório e exclusivo se tpAval = [1]."
							);
						}
						else {
							if(trim($arr_dado['intConc']) == '') {
								$validacao[] = array(
									"titulo" => 'agNoc/intConc: ' . $arr_dado['risco'],
									"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/intConc se tornou obrigatório: Intensidade, concentração ou dose da exposição do trabalhador ao agente nocivo cujo critério de avaliação seja quantitativo.<b>Validação:</b> Preenchimento obrigatório e exclusivo se tpAval = [1]."
								);
							}
						}

						switch($arr_dado['codAgNoc']) {
							case '01.18.001':
							case '02.01.014':
								if(!isset($arr_dado['limTol'])) {
									$validacao[] = array(
										"titulo" => 'agNoc/limTol: ' . $arr_dado['risco'],
										"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/limTol se tornou obrigatório: Limite de tolerância calculado para agentes específicos, conforme técnica de medição exigida na legislação.<b>Validação:</b> Preenchimento obrigatório e exclusivo se tpAval = [1] e codAgNoc = [01.18.001, 02.01.014]."
									);
								}
								else {
									if(trim($arr_dado['limTol']) == '') {
										$validacao[] = array(
											"titulo" => 'agNoc/limTol: ' . $arr_dado['risco'],
											"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/limTol se tornou obrigatório: Limite de tolerância calculado para agentes específicos, conforme técnica de medição exigida na legislação.<b>Validação:</b> Preenchimento obrigatório e exclusivo se tpAval = [1] e codAgNoc = [01.18.001, 02.01.014]."
										);
									}
								}

								break;
						}//fim switch tpAval

						if(!isset($arr_dado['unMed'])) {
								$validacao[] = array(
									"titulo" => 'agNoc/unMed: ' . $arr_dado['risco'],
									"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/unMed se tornou obrigatório: Dose ou unidade de medida da intensidade ou concentração do agente.<b>Valores válidos:</b>1 - dose diária de ruído/2 - decibel linear (dB (linear))/3 - decibel (C) (dB(C))/4 - decibel (A) (dB(A))/5 - metro por segundo ao quadrado (m/s2)/6 - metro por segundo elevado a 1,75 (m/s1,75)/7 - parte de vapor ou gás por milhão de partes de ar contaminado (ppm)/8 - miligrama por metro cúbico de ar (mg/m3)/9 - fibra por centímetro cúbico (f/cm3)/10 - grau Celsius (ºC)/11 - metro por segundo (m/s)/12 - porcentual/13 - lux (lx)/14 - unidade formadora de colônias por metro cúbico (ufc/m3)/15 - dose diária/16 - dose mensal/17 - dose trimestral/18 - dose anual/19 - watt por metro quadrado (W/m2)/20 - ampère por metro (A/m)/21 - militesla (mT)/22 - microtesla (μT)/23 - miliampère (mA)/24 - quilovolt por metro (kV/m)/25 - volt por metro (V/m)/26 - joule por metro quadrado (J/m2)/27 - milijoule por centímetro quadrado (mJ/cm2)/28 - milisievert (mSv)/29 - milhão de partículas por decímetro cúbico (mppdc)/30 - umidade relativa do ar (UR (%)) <b>Validação:</b> Preenchimento obrigatório e exclusivo se tpAval = [1]."
							);
						}
						else {
							if(trim($arr_dado['unMed']) == '') {
								$validacao[] = array(
									"titulo" => 'agNoc/unMed: ' . $arr_dado['risco'],
									"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/unMed se tornou obrigatório: Dose ou unidade de medida da intensidade ou concentração do agente.<b>Valores válidos:</b>1 - dose diária de ruído/2 - decibel linear (dB (linear))/3 - decibel (C) (dB(C))/4 - decibel (A) (dB(A))/5 - metro por segundo ao quadrado (m/s2)/6 - metro por segundo elevado a 1,75 (m/s1,75)/7 - parte de vapor ou gás por milhão de partes de ar contaminado (ppm)/8 - miligrama por metro cúbico de ar (mg/m3)/9 - fibra por centímetro cúbico (f/cm3)/10 - grau Celsius (ºC)/11 - metro por segundo (m/s)/12 - porcentual/13 - lux (lx)/14 - unidade formadora de colônias por metro cúbico (ufc/m3)/15 - dose diária/16 - dose mensal/17 - dose trimestral/18 - dose anual/19 - watt por metro quadrado (W/m2)/20 - ampère por metro (A/m)/21 - militesla (mT)/22 - microtesla (μT)/23 - miliampère (mA)/24 - quilovolt por metro (kV/m)/25 - volt por metro (V/m)/26 - joule por metro quadrado (J/m2)/27 - milijoule por centímetro quadrado (mJ/cm2)/28 - milisievert (mSv)/29 - milhão de partículas por decímetro cúbico (mppdc)/30 - umidade relativa do ar (UR (%)) <b>Validação:</b> Preenchimento obrigatório e exclusivo se tpAval = [1]."
								);
							}
						}

						if(!isset($arr_dado['tecMedicao'])) {
								$validacao[] = array(
									"titulo" => 'agNoc/tecMedicao: ' . $arr_dado['risco'],
									"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/tecMedicao se tornou obrigatório: Técnica utilizada para medição da intensidade ou concentração.<b>Validação:</b> Preenchimento obrigatório e exclusivo se tpAval = [1]."
							);
						}
						else {
							if(trim($arr_dado['tecMedicao']) == '') {
								$validacao[] = array(
									"titulo" => 'agNoc/tecMedicao: ' . $arr_dado['risco'],
									"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/tecMedicao se tornou obrigatório: Técnica utilizada para medição da intensidade ou concentração.<b>Validação:</b> Preenchimento obrigatório e exclusivo se tpAval = [1]."
								);
							}
						}

						
					}// fim tpAval = 1
					else {

						if(isset($arr_dado['intConc'])) {
							if(trim($arr_dado['intConc']) == '') {
								$validacao[] = array(
									"titulo" => 'agNoc/intConc: ' . $arr_dado['risco'],
									"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/intConc é opcional: Intensidade, concentração ou dose da exposição do trabalhador ao agente nocivo cujo critério de avaliação seja quantitativo.<b>Validação:</b> Preenchimento obrigatório e exclusivo se tpAval = [1]."
								);
							}
						}

						
						if(isset($arr_dado['limTol'])) {
							if(trim($arr_dado['limTol']) == '') {
								$validacao[] = array(
									"titulo" => 'agNoc/limTol: ' . $arr_dado['risco'],
									"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/limTol é opcional: Limite de tolerância calculado para agentes específicos, conforme técnica de medição exigida na legislação.<b>Validação:</b> Preenchimento obrigatório e exclusivo se tpAval = [1] e codAgNoc = [01.18.001, 02.01.014]."
								);
							}
						}

						if(isset($arr_dado['unMed'])) {
							if(trim($arr_dado['unMed']) == '') {
								$validacao[] = array(
									"titulo" => 'agNoc/unMed: ' . $arr_dado['risco'],
									"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/unMed é opcional: Dose ou unidade de medida da intensidade ou concentração do agente.<b>Valores válidos:</b>1 - dose diária de ruído/2 - decibel linear (dB (linear))/3 - decibel (C) (dB(C))/4 - decibel (A) (dB(A))/5 - metro por segundo ao quadrado (m/s2)/6 - metro por segundo elevado a 1,75 (m/s1,75)/7 - parte de vapor ou gás por milhão de partes de ar contaminado (ppm)/8 - miligrama por metro cúbico de ar (mg/m3)/9 - fibra por centímetro cúbico (f/cm3)/10 - grau Celsius (ºC)/11 - metro por segundo (m/s)/12 - porcentual/13 - lux (lx)/14 - unidade formadora de colônias por metro cúbico (ufc/m3)/15 - dose diária/16 - dose mensal/17 - dose trimestral/18 - dose anual/19 - watt por metro quadrado (W/m2)/20 - ampère por metro (A/m)/21 - militesla (mT)/22 - microtesla (μT)/23 - miliampère (mA)/24 - quilovolt por metro (kV/m)/25 - volt por metro (V/m)/26 - joule por metro quadrado (J/m2)/27 - milijoule por centímetro quadrado (mJ/cm2)/28 - milisievert (mSv)/29 - milhão de partículas por decímetro cúbico (mppdc)/30 - umidade relativa do ar (UR (%)) <b>Validação:</b> Preenchimento obrigatório e exclusivo se tpAval = [1]."
								);
							}
						}

						if(isset($arr_dado['tecMedicao'])) {
							if(trim($arr_dado['tecMedicao']) == '') {
								$validacao[] = array(
									"titulo" => 'agNoc/tecMedicao: ' . $arr_dado['risco'],
									"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/tecMedicao é opcional: Técnica utilizada para medição da intensidade ou concentração.<b>Validação:</b> Preenchimento obrigatório e exclusivo se tpAval = [1]."
								);
							}
						}
					}//fim tpAval
				}//fim validacao nova ausencia de risco


				####epcEpi###

				/*
				VALIDAÇÃO NOVA
				O grupo não deve ser preenchido no caso De ausência de agente nocivo ou de atividades previstas no Anexo IV do Decreto 3.048/1999
				*/
				if($arr_dado['codAgNoc'] != '09.01.001') {

					if(empty($arr_dado['epcEpi'])) {
						$validacao[] = array(
							"titulo" => 'epcEpi',
							"descricao" => "Grupo de evtExpRisco/infoExpRisco/agNoc/epcEpi é obrigatório: Informações relativas a Equipamentos de Proteção Coletiva - EPC e Equipamentos de Proteção Individual - EPI."
						);
					}
					else {

						$arr_epcEpi = (array)$arr_dado['epcEpi'];

						if(trim($arr_epcEpi['utilizEPC']) == '') {
							$validacao[] = array(
								"titulo" => 'agNoc/epcEpi: ' . $arr_dado['risco'],
								"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/utilizEPC é obrigatório: O empregador implementa medidas de proteção coletiva (EPC) para eliminar ou reduzir a exposição dos trabalhadores ao agente nocivo? <b>Valores válidos:</b> 0 - Não se aplica/1 - Não implementa/2 - Implementa"
							);
						}
						else if($arr_epcEpi['utilizEPC'] == '2') {

							if(!isset($arr_epcEpi['eficEpc'])) {
								$validacao[] = array(
									"titulo" => 'agNoc/epcEpi: ' . $arr_dado['risco'],
									"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/eficEpc se tornou obrigatório: Os EPCs são eficazes na neutralização dos riscos ao trabalhador? <b>Valores válidos:</b> S - Sim/N - Não <b>Validação: Preenchimento obrigatório e exclusivo se utilizEPC = [2]</b>."
								);
							}
							else if(trim($arr_epcEpi['eficEpc']) == '') {
								$validacao[] = array(
									"titulo" => 'agNoc/epcEpi: ' . $arr_dado['risco'],
									"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/eficEpc se tornou obrigatório: Os EPCs são eficazes na neutralização dos riscos ao trabalhador? <b>Valores válidos:</b> S - Sim/N - Não <b>Validação: Preenchimento obrigatório e exclusivo se utilizEPC = [2]</b>."
								);
							}
						}
						else {

							if(isset($arr_epcEpi['eficEpc'])) {
								if(trim($arr_epcEpi['eficEpc']) == '') {
									$validacao[] = array(
										"titulo" => 'agNoc/epcEpi: ' . $arr_dado['risco'],
										"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/eficEpc é opcional: O empregador implementa medidas de proteção coletiva (EPC) para eliminar ou reduzir a exposição dos trabalhadores ao agente nocivo? <b>Valores válidos:</b> 0 - Não se aplica/1 - Não implementa/2 - Implementa"
									);
								}
							}
						}//fim utilizEPC

						if(trim($arr_epcEpi['utilizEPI']) == '') {
							$validacao[] = array(
								"titulo" => 'agNoc/epcEpi: ' . $arr_dado['risco'],
								"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/utilizEPI é opcional: Utilização de EPI.<b>Valores válidos:</b>0 - Não se aplica/1 - Não utilizado/2 - Utilizado"
							);
						}

						//epi
						###EPI###
						if(!empty($arr_epcEpi['epi'])) {
							$arr_epi = (array)$arr_epcEpi['epi'];

							if(isset($arr_epi[0])) {
								
								foreach($arr_epi AS $epis) {

									if(isset($epis['docAval'])) {
										if(trim($epis['docAval']) == '') {
											$validacao[] = array(
												"titulo" => 'agNoc/epcEpi/epi: ' . $arr_dado['risco'],
												"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/epi/docAval é opcional: Certificado de Aprovação - CA ou documento de avaliação do EPI."
											);
										}
										else {
											if(!isset($epis['dscEPI'])) {
												$validacao[] = array(
													"titulo" => 'agNoc/epcEpi/epi: ' . $arr_dado['risco'],
													"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/epi/dscEPI se tornou obrigatório: Descrição do EPI.<b>Validação:</b> Preenchimento obrigatório e exclusivo se docAval não for informado."
												);
											}
											else if(trim($epis['dscEPI']) == '') {
												$validacao[] = array(
													"titulo" => 'agNoc/epcEpi/epi: ' . $arr_dado['risco'],
													"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/epi/dscEPI se tornou obrigatório:Descrição do EPI.<b>Validação:</b> Preenchimento obrigatório e exclusivo se docAval não for informado."
												);
											}
										}
									}//fim docAval

									if(isset($epis['dscEPI'])) {
										if(trim($epis['dscEPI']) == '') {
											$validacao[] = array(
												"titulo" => 'agNoc/epcEpi/epi: ' . $arr_dado['risco'],
												"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/epi/dscEPI é opcional:Descrição do EPI.<b>Validação:</b> Preenchimento obrigatório e exclusivo se docAval não for informado."
											);
										}
									}

									if(empty($epis['eficEpi'])) {
										$validacao[] = array(
											"titulo" => 'agNoc/epcEpi/epi: ' . $arr_dado['risco'],
											"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/epi/eficEpi é obrigatório: O EPI é eficaz na neutralização do risco ao trabalhador? <b>Valores válidos:</b> S - Sim/N - Não"
										);
									}
								}//fim foreach arr_epi
							}
							else {
								if(isset($arr_epi['docAval'])) {
									if(trim($arr_epi['docAval']) == '') {
										$validacao[] = array(
											"titulo" => 'agNoc/epcEpi/epi: ' . $arr_dado['risco'],
											"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/epi/docAval é opcional: Certificado de Aprovação - CA ou documento de avaliação do EPI."
										);
									}
									else {
										if(!isset($arr_epi['dscEPI'])) {
											$validacao[] = array(
												"titulo" => 'agNoc/epcEpi/epi: ' . $arr_dado['risco'],
												"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/epi/dscEPI se tornou obrigatório: Descrição do EPI.<b>Validação:</b> Preenchimento obrigatório e exclusivo se docAval não for informado."
											);
										}
										else if(trim($arr_epi['dscEPI']) == '') {
											$validacao[] = array(
												"titulo" => 'agNoc/epcEpi/epi: ' . $arr_dado['risco'],
												"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/epi/dscEPI se tornou obrigatório:Descrição do EPI.<b>Validação:</b> Preenchimento obrigatório e exclusivo se docAval não for informado."
											);
										}
									}
								}//fim docAval

								if(isset($arr_epi['dscEPI'])) {
									if(trim($arr_epi['dscEPI']) == '') {
										$validacao[] = array(
											"titulo" => 'agNoc/epcEpi/epi: ' . $arr_dado['risco'],
											"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/epi/dscEPI é opcional:Descrição do EPI.<b>Validação:</b> Preenchimento obrigatório e exclusivo se docAval não for informado."
										);
									}
								}

								if(empty($arr_epi['eficEpi'])) {
									$validacao[] = array(
										"titulo" => 'agNoc/epcEpi/epi: ' . $arr_dado['risco'],
										"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/epi/eficEpi é obrigatório: O EPI é eficaz na neutralização do risco ao trabalhador? <b>Valores válidos:</b> S - Sim/N - Não"
									);
								}

							}//fim if se tem mais de uma epi


							// debug(count($arr_epi));exit;	

						}// fim $arr_epcEpi['epi']
						###FIM EPI###
					
						### epiCompl###
						if(!empty($arr_epcEpi['epiCompl'])) {
							$arr_epiCompl = (array)$arr_epcEpi['epiCompl'];

							if(empty($arr_epiCompl['medProtecao'])) {
								$validacao[] = array(
									"titulo" => 'agNoc/epcEpi/epiCompl: ' . $arr_dado['risco'],
									"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/epiCompl/medProtecao é obrigatório: Foi tentada a implementação de medidas de proteção coletiva, de caráter administrativo ou de organização, optando-se pelo EPI por inviabilidade técnica, insuficiência ou interinidade, ou ainda em caráter complementar ou emergencial? <b>Valores válidos:</b> S - Sim/N - Não"
								);
							}

							if(empty($arr_epiCompl['condFuncto'])) {
								$validacao[] = array(
									"titulo" => 'agNoc/epcEpi/epiCompl: ' . $arr_dado['risco'],
									"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/epiCompl/condFuncto é obrigatório: Foram observadas as condições de funcionamento do EPI ao longo do tempo, conforme especificação técnica do fabricante nacional ou importador, ajustadas às condições de campo? <b>Valores válidos:</b> S - Sim/N - Não"
								);
							}

							if(empty($arr_epiCompl['usoInint'])) {
								$validacao[] = array(
									"titulo" => 'agNoc/epcEpi/epiCompl: ' . $arr_dado['risco'],
									"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/epiCompl/usoInint é obrigatório: Foi observado o uso ininterrupto do EPI ao longo do tempo, conforme especificação técnica do fabricante nacional ou importador, ajustadas às condições de campo? <b>Valores válidos:</b> S - Sim/N - Não"
								);
							}

							if(empty($arr_epiCompl['przValid'])) {
								$validacao[] = array(
									"titulo" => 'agNoc/epcEpi/epiCompl: ' . $arr_dado['risco'],
									"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/epiCompl/przValid é obrigatório: Foi observado o prazo de validade do CA no momento da compra do EPI? <b>Valores válidos:</b> S - Sim/N - Não"
								);
							}

							if(empty($arr_epiCompl['periodicTroca'])) {
								$validacao[] = array(
									"titulo" => 'agNoc/epcEpi/epiCompl: ' . $arr_dado['risco'],
									"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/epiCompl/periodicTroca é obrigatório: É observada a periodicidade de troca definida pelo fabricante nacional ou importador e/ou programas ambientais, comprovada mediante recibo assinado pelo usuário em época própria? <b>Valores válidos:</b> S - Sim/N - Não"
								);
							}

							if(empty($arr_epiCompl['higienizacao'])) {
								$validacao[] = array(
									"titulo" => 'agNoc/epcEpi/epiCompl: ' . $arr_dado['risco'],
									"descricao" => "Campo de evtExpRisco/infoExpRisco/agNoc/epcEpi/epiCompl/higienizacao é obrigatório: É observada a higienização conforme orientação do fabricante nacional ou importador? <b>Valores válidos:</b> S - Sim/N - Não"
								);
							}
						}
						### FIM epiCompl###

						// debug($arr_epcEpi);
						
					}//epcEpi
				}//validação nova
				####FIM epcEpi###



				$contador++;

			}//fim foreach read_xml			
		}//fim else
		####FIM agNoc###
		
		####respReg###
		$read_xml_respReg = simplexml_load_string(utf8_decode(utf8_encode($dado['respReg'])));
		// debug($read_xml_respReg);exit;
		if(empty($read_xml_respReg)) {
			$validacao[] = array(
				"titulo" => 'respReg',
				"descricao" => "Grupo de evtExpRisco/infoExpRisco/respReg é obrigatório: Informações relativas ao responsável pelos registros ambientais."
			);
		}
		else {
			//leitura do xml transformando em objeto array
			// $read_xml = simplexml_load_string(utf8_decode(utf8_encode($dado['respReg'])));
			$contador = 1;
			foreach($read_xml_respReg AS $obj) {

				//parsea o obj para array
				$arr_respReg = (array)$obj;

				if(empty($arr_respReg['cpfResp'])) {
					$validacao[] = array(
						"titulo" => 'respReg',
						"descricao" => "Campo de evtExpRisco/infoExpRisco/respReg/cpfResp é obrigatório: Preencher com o CPF do responsável pelos registros ambientais. <b>Validação:</b> Deve ser um CPF válido."
					);
				}

				if(empty($arr_respReg['ideOC'])) {
					$validacao[] = array(
						"titulo" => 'respReg',
						"descricao" => "Campo de evtExpRisco/infoExpRisco/respReg/ideOC é obrigatório: Órgão de classe ao qual o responsável pelos registros ambientais está vinculado.<b>Valores válidos:</b> 1 - Conselho Regional de Medicina - CRM/4 - Conselho Regional de Engenharia e Agronomia - CREA/9 - Outros"
					);
				}
				else if($arr_respReg['ideOC'] == '9') {
					if(empty($arr_respReg['dscOC'])) {
						$validacao[] = array(
							"titulo" => 'respReg',
							"descricao" => "Campo de evtExpRisco/infoExpRisco/respReg/dscOC se tornou obrigatório: Descrição (sigla) do órgão de classe ao qual o responsável pelos registros ambientais está vinculado. <b>Validação:</b> Preenchimento obrigatório e exclusivo se ideOC = [9]."
						);
					}
				}

				// if(isset($arr_respReg['dscOC'])) {
				// 	if($arr_respReg['ideOC'] != '4' || $arr_respReg['ideOC'] != '1'){
				// 		if(trim($arr_respReg['dscOC']) == '') {
				// 			$validacao[] = array(
				// 				"titulo" => 'respReg',
				// 				"descricao" => "Campo de evtExpRisco/infoExpRisco/respReg/dscOC é opcional: Descrição (sigla) do órgão de classe ao qual o responsável pelos registros ambientais está vinculado. <b>Validação:</b> Preenchimento obrigatório e exclusivo se ideOC = [9]."
				// 			);
				// 		}
				// 	}
				// }

				if(empty($arr_respReg['nrOC'])) {
					$validacao[] = array(
						"titulo" => 'respReg',
						"descricao" => "Campo de evtExpRisco/infoExpRisco/respReg/nrOC é obrigatório: Número de inscrição no órgão de classe."
					);
				}

				if(empty($arr_respReg['ufOC'])) {
					$validacao[] = array(
						"titulo" => 'respReg',
						"descricao" => "Campo de evtExpRisco/infoExpRisco/respReg/ufOC é obrigatório: Sigla da Unidade da Federação - UF do órgão de classe. <b>Valores válidos:</b> AC, AL, AP, AM, BA, CE, DF, ES, GO, MA, MT, MS, MG, PA, PB, PR, PE, PI, RJ, RN, RS, RO, RR, SC, SP, SE, TO"
					);
				}

				// debug($arr_respReg);exit;

			}
		}//fim respReg
		
		####FIM respReg###
		
		### obs ###
		if(isset($dado['obsCompl'])) {
			if(trim($dado['obsCompl']) == '') {
				$validacao[] = array(
					"titulo" => 'obsCompl',
					"descricao" => "Campo de evtExpRisco/infoExpRisco/obsCompl é opcional:Observação(ões) complementar(es) referente(s) a registros ambientais."
				);
			}
		}

		### FIM obs ###
		
		//debug($dado);
		// debug($validacao);
		// exit;

		return $validacao;


	}//fim valida_regra_campos_s2240



	/**
	 * Metodo para validar o layout do esocial s-2210
	 * https://www.gov.br/esocial/pt-br/documentacao-tecnica/leiautes-esocial-html/index.html#2210_eSocial
	 * 
	 */
	public function valida_regra_campos_s2210($dados)
	{

		$dado = $dados[0];
		$validacao = array();

		// debug($dados);exit;

		###ideEmpregador###
		if(empty($dado['tpInsc'])) {
			$validacao[] = array(
				"titulo" => 'ideEmpregador',
				"descricao" => "Campo evtCAT/ideEmpregador/tpInsc obrigatório, com os valores 1 -> CNPJ ou 2 -> CPF!"
			);
		}

		if(empty($dado['nrInsc'])) {
			$validacao[] = array(
				"titulo" => 'ideEmpregador',
				"descricao" => "Campo evtCAT/ideEmpregador/nrInsc obrigatório: Informar o número de inscrição do contribuinte de acordo com o tipo de inscrição indicado no campo ideEmpregador/tpInsc"
			);
		}
		
		if($dado['tpInsc'] == 1 && (strlen($dado['nrInsc']) < 14 || strlen($dado['nrInsc']) > 14) ) {
			$validacao[] = array(
				"titulo" => 'ideEmpregador',
				"descricao" => "Campo evtCAT/ideEmpregador/nrInsc informar o CPNJ do contribuinte"
			);
		}
		###FIM ideEmpregador###
		
		#####ideVinculo###
		if(empty($dado['cpfTrab'])) {
			$validacao[] = array(
				"titulo" => 'ideVinculo',
				"descricao" => "Campo evtCAT/ideVinculo/cpfTrab obrigatório: Preencher com o número do CPF do trabalhador."
			);
		}

		if(trim($dado['matricula']) == '' && trim($dado['codCateg']) == '') {
			$validacao[] = array(
				"titulo" => 'ideVinculo',
				"descricao" => "Campo evtCAT/ideVinculo/matricula ou ideVinculo/codCateg obrigatório: Matrícula atribuída ao trabalhador pela empresa / Preencher com o código da categoria do trabalhador."
			);
		}
		####FIM ideVinculo###

		####cat###
		if(trim($dado['dtAcid']) == '') {
			$validacao[] = array(
				"titulo" => 'cat',
				"descricao" => "Campo evtCAT/cat/dtAcid obrigatório: Data do acidente.<b>Validação:</b> Deve ser uma data válida, igual ou anterior à data atual e igual ou posterior à data de admissão do trabalhador e à data de início da obrigatoriedade deste evento para o empregador no eSocial. Se tpCat = [2, 3], deve ser informado valor igual ao preenchido no evento de CAT anterior, quando informado em nrRecCatOrig."
			);
		}
		else {

			if($dado['dtAcid'] < $dado['dtAdmissao']) {
				$validacao[] = array(
					"titulo" => 'cat',
					"descricao" => "Campo evtCAT/cat/dtAcid: Data do acidente.<b>Validação:</b> <b>Deve ser uma data válida, igual ou anterior à data atual e igual ou posterior à data de admissão do trabalhador e à data de início da obrigatoriedade deste evento para o empregador no eSocial.</b> Se tpCat = [2, 3], deve ser informado valor igual ao preenchido no evento de CAT anterior, quando informado em nrRecCatOrig."
				);
			}

			// switch($dado['tpCat']) {
			// 	case '2':
			// 	case '3':
					
			// 		break;
			// }


			// $data_base_esocial = '2021-10-13';

			// // Calcula a data daqui 30 dias
			// $timestamp_30_dias = strtotime("+30 days");
			// $trinta_dias_data_atual = date('Y-m-d', $timestamp_30_dias);

			// if($dado['dtAcid'] <= $data_base_esocial || $dado['dtIniCondicao'] > $trinta_dias_data_atual) {
			// 	$validacao[] = array(
			// 		"titulo" => 'cat',
			// 		"descricao" => "Campo evtCAT/cat/dtIniCondicao: Informar a data em que o trabalhador iniciou as atividades nas condições descritas ou a data de início da obrigatoriedade deste evento para o empregador no eSocial, a que for mais recente. <b>VALIDAÇÂO:</b> Deve ser uma data válida, igual ou posterior à data de admissão do vínculo a que se refere. <b>Não pode ser anterior à data de início da obrigatoriedade deste evento para o empregador no eSocial, nem pode ser posterior a 30 (trinta) dias da data atual.</b>"
			// 	);
			// }

		}//fim dtAcid

		if(empty($dado['tpAcid'])) {
			$validacao[] = array(
				"titulo" => 'cat',
				"descricao" => "Campo evtCAT/cat/tpAcid obrigatório: Tipo de acidente de trabalho. <b>Valores válidos:</b> 1 - Típico/2 - Doença/3 - Trajeto"
			);
		}

		if($dado['tpAcid'] == "1") {
			if($dado['hrAcid'] == "") {
				$validacao[] = array(
					"titulo" => 'cat',
					"descricao" => "Campo evtCAT/cat/hrAcid obrigatório: Hora do acidente, no formato HHMM. <b>Validação: Preenchimento obrigatório e exclusivo se tpAcid = [1].</b> Se informada, deve estar no intervalo entre [0000] e [2359], criticando inclusive a segunda parte do número, que indica os minutos, que deve ser menor ou igual a 59. Se tpCat = [2, 3], deve ser informado valor igual ao preenchido no evento de CAT anterior, quando informado em nrRecCatOrig."
				);
			}

			if($dado['hrsTrabAntesAcid'] == "") {
				$validacao[] = array(
					"titulo" => 'cat',
					"descricao" => "Campo evtCAT/cat/hrsTrabAntesAcid obrigatório: Horas trabalhadas antes da ocorrência do acidente, no formato HHMM.<b>Validação: Preenchimento obrigatório e exclusivo se tpAcid = [1].</b> Se informada, deve estar no intervalo entre [0000] e [9959], criticando inclusive a segunda parte do número, que indica os minutos, que deve ser menor ou igual a 59."
				);
			}
		}

		if(empty($dado['tpCat'])) {
			$validacao[] = array(
				"titulo" => 'cat',
				"descricao" => "Campo evtCAT/cat/tpCat obrigatório: Tipo de CAT. <b>Valores válidos:</b>1 - Inicial/2 - Reabertura/3 - Comunicação de óbito"
			);
		}

		if(empty($dado['indCatObito'])) {
			$validacao[] = array(
				"titulo" => 'cat',
				"descricao" => "Campo evtCAT/cat/indCatObito obrigatório: Houve óbito? <b>Valores válidos:</b> S - Sim / N - Não Validação: Se o tpCat for igual a [3], o campo deverá sempre ser preenchido com [S]. Se o tpCat for igual a [2], o campo deverá sempre ser preenchido com [N]."
			);
		}

		if($dado['indCatObito'] == "S") {

			if($dado['dtObito'] == '') {
				$validacao[] = array(
					"titulo" => 'cat',
					"descricao" => "Campo evtCAT/cat/dtObito obrigatório: Data do óbito. <b>Validação:</b> Deve ser uma data válida, igual ou posterior a dtAcid e igual ou anterior à data atual. Preenchimento obrigatório e exclusivo se indCatObito = [S]."
				);
			}
			else {
				if($dado['dtObito'] < $dado['dtAcid']) {
					$validacao[] = array(
						"titulo" => 'cat',
						"descricao" => "Campo evtCAT/cat/dtObito obrigatório: Data do óbito. <b>Validação:</b> Deve ser uma data válida, igual ou posterior a dtAcid e igual ou anterior à data atual. Preenchimento obrigatório e exclusivo se indCatObito = [S]."
					);
				}
			}
		}

		if(empty($dado['indComunPolicia'])) {
			$validacao[] = array(
				"titulo" => 'cat',
				"descricao" => "Campo evtCAT/cat/indComunPolicia obrigatório: Houve comunicação à autoridade policial? <b>Valores válidos:</b> S - Sim/N - Não"
			);
		}

		if(empty($dado['codSitGeradora'])) {
			$validacao[] = array(
				"titulo" => 'cat',
				"descricao" => "Campo evtCAT/cat/codSitGeradora obrigatório: Preencher com o código da situação geradora do acidente ou da doença profissional. <b>Validação:</b> Deve ser um código válido e existente na Tabela 15."
			);
		}

		if(empty($dado['iniciatCAT'])) {
			$validacao[] = array(
				"titulo" => 'cat',
				"descricao" => "Campo evtCAT/cat/iniciatCAT obrigatório: Iniciativa da CAT. <b>Valores válidos:</b> 1 - Empregador / 2 - Ordem judicial / 3 - Determinação de órgão fiscalizador"
			);
		}
		
	// 	if(empty($dado['obsCAT'])) {
	// 		$validacao[] = array(
	// 			"titulo" => 'cat',
	// 			"descricao" => "Campo evtCAT/cat/obsCAT obrigatório: Observação"
	// 		);
	// 	}
		

		### localAcidente ###
		if(empty($dado['tpLocal'])) {
			$validacao[] = array(
				"titulo" => 'localAcidente',
				"descricao" => "Campo evtCAT/cat/localAcidente/tpLocal obrigatório: Tipo de local do acidente. <b>Valores válidos:</b> 1 - Estabelecimento do empregador no Brasil/ 2 - Estabelecimento do empregador no exterior/ 3 - Estabelecimento de terceiros onde o empregador presta serviços/ 4 - Via pública/ 5 - Área rural/ 6 - Embarcação/ 9 - Outros"
			);
		}
		else {
			switch($dado['tpLocal']) {

				case '1':
				case '3':
				case '5':
					if(empty($dado['cep'])) {
						$validacao[] = array(
							"titulo" => 'localAcidente',
							"descricao" => "Campo evtCAT/cat/localAcidente/cep obrigatório: Código de Endereçamento Postal - CEP. <b>Validação:</b> Preenchimento obrigatório se tpLocal = [1, 3, 5]. Não preencher se tpLocal = [2]. Se preenchido, deve ser informado apenas com números, com 8 (oito) posições."
						);
					}

					if(empty($dado['codMunic'])) {
						$validacao[] = array(
							"titulo" => 'localAcidente',
							"descricao" => "Campo evtCAT/cat/localAcidente/codMunic obrigatório: Preencher com o código do município, conforme tabela do IBGE. <b>Validação:</b> Preenchimento obrigatório se tpLocal = [1, 3, 4, 5]. Não preencher se tpLocal = [2]. Se informado, deve ser um código válido e existente na tabela do IBGE."
						);
					}

					if(empty($dado['uf'])) {
						$validacao[] = array(
							"titulo" => 'localAcidente',
							"descricao" => "Campo evtCAT/cat/localAcidente/uf obrigatório: Preencher com a sigla da Unidade da Federação - UF. <b>Valores válidos:</b> AC, AL, AP, AM, BA, CE, DF, ES, GO, MA, MT, MS, MG, PA, PB, PR, PE, PI, RJ, RN, RS, RO, RR, SC, SP, SE, TO <b>Validação:</b> Preenchimento obrigatório se tpLocal = [1, 3, 4, 5]. Não preencher se tpLocal = [2]."
						);
					}

					break;
				case '4':
					if(empty($dado['codMunic'])) {
						$validacao[] = array(
							"titulo" => 'localAcidente',
							"descricao" => "Campo evtCAT/cat/localAcidente/codMunic obrigatório: Preencher com o código do município, conforme tabela do IBGE. <b>Validação:</b> Preenchimento obrigatório se tpLocal = [1, 3, 4, 5]. Não preencher se tpLocal = [2]. Se informado, deve ser um código válido e existente na tabela do IBGE."
						);
					}

					if(empty($dado['uf'])) {
						$validacao[] = array(
							"titulo" => 'localAcidente',
							"descricao" => "Campo evtCAT/cat/localAcidente/uf obrigatório: Preencher com a sigla da Unidade da Federação - UF. <b>Valores válidos:</b> AC, AL, AP, AM, BA, CE, DF, ES, GO, MA, MT, MS, MG, PA, PB, PR, PE, PI, RJ, RN, RS, RO, RR, SC, SP, SE, TO <b>Validação:</b> Preenchimento obrigatório se tpLocal = [1, 3, 4, 5]. Não preencher se tpLocal = [2]."
						);
					}

					break;
				case '2':
					if(empty($dado['pais'])) {
						$validacao[] = array(
							"titulo" => 'localAcidente',
							"descricao" => "Campo evtCAT/cat/localAcidente/pais obrigatório: Preencher com o código do país. <b>Validação:</b> Deve ser um código de país válido e existente na Tabela 06. Preenchimento obrigatório se tpLocal = [2]. Não preencher nos demais casos."
						);
					}

					if(empty($dado['codPostal'])) {
						$validacao[] = array(
							"titulo" => 'localAcidente',
							"descricao" => "Campo evtCAT/cat/localAcidente/codPostal obrigatório: Código de Endereçamento Postal. <b>Validação:</b> Preenchimento obrigatório se tpLocal = [2]. Não preencher nos demais casos."
						);
					}
					
					break;

			}//fim switch
		}

		if(!empty($dado['dscLocal'])) {

			if($dado['dscLocal'] == '') {
				$validacao[] = array(
					"titulo" => 'localAcidente',
					"descricao" => "Campo evtCAT/cat/localAcidente/dscLocal não obrigatório: Especificação do local do acidente (pátio, rampa de acesso, posto de trabalho, etc.)."
				);
			}
		}

		if(empty($dado['dscLograd'])) {
			$validacao[] = array(
				"titulo" => 'localAcidente',
				"descricao" => "Campo evtCAT/cat/localAcidente/dscLograd obrigatório: Descrição do logradouro."
			);
		}

		if(empty($dado['nrLograd'])) {
			$validacao[] = array(
				"titulo" => 'localAcidente',
				"descricao" => "Campo evtCAT/cat/localAcidente/nrLograd obrigatório: Número do logradouro. Se não houver número a ser informado, preencher com 'S/N'."
			);
		}

		if(!empty($dado['complemento'])) {

			if($dado['complemento'] == '') {
				$validacao[] = array(
					"titulo" => 'localAcidente',
					"descricao" => "Campo evtCAT/cat/localAcidente/complemento não obrigatório: Complemento do logradouro."
				);
			}
		}

		if(!empty($dado['bairro'])) {

			if($dado['bairro'] == '') {
				$validacao[] = array(
					"titulo" => 'localAcidente',
					"descricao" => "Campo evtCAT/cat/localAcidente/bairro não obrigatório: Nome do bairro/distrito."
				);
			}
		}		

		### ideLocalAcid ###
		$ideLocalAcid = false;
		if(!empty($dado['tpInsc_ideLocalAcid']) || !empty($dado['nrInsc_ideLocalAcid']) ) {
			$ideLocalAcid = true;
		}

		if($ideLocalAcid) {
		
			if(empty($dado['tpInsc_ideLocalAcid'])) {
				$validacao[] = array(
					"titulo" => 'ideLocalAcid',
					"descricao" => "Campo evtCAT/cat/localAcidente/ideLocalAcid/tpInsc não obrigatório: Preencher com o código correspondente ao tipo de inscrição do local onde ocorreu o acidente ou a doença ocupacional, conforme Tabela 05. <b>Valores válidos:</b> 1 - CNPJ / 3 - CAEPF / 4 - CNO"
				);
			}

			if(empty($dado['nrInsc_ideLocalAcid'])) {
				$validacao[] = array(
					"titulo" => 'ideLocalAcid',
					"descricao" => "Campo evtCAT/cat/localAcidente/ideLocalAcid/nrInsc obrigatório: Informar o número de inscrição do estabelecimento, de acordo com o tipo de inscrição indicado no campo ideLocalAcid/tpInsc. Se o acidente ou a doença ocupacional ocorreu em local onde o trabalhador presta serviços, deve ser um número de inscrição pertencente à contratante dos serviços. <b>Validação:</b> Deve ser compatível com o conteúdo do campo ideLocalAcid/tpInsc. Deve ser um identificador válido, constante das bases da RFB, e: a) Se tpLocal = [1], deve ser válido e existente na Tabela de Estabelecimentos (S-1005); b) Se tpLocal = [3], deve ser diferente dos estabelecimentos informados na Tabela S-1005 e, se ideLocalAcid/tpInsc = [1], diferente do CNPJ base indicado em S-1000."
				);
			}
		}//fim ideLocaAcid
		### FIM ideLocalAcid ###
		
		### FIM localAcidente ###
		

		### parteAtingida ###
		if(empty($dado['codParteAting'])) {
			$validacao[] = array(
				"titulo" => 'localAcidente',
				"descricao" => "Campo evtCAT/cat/parteAtingida/codParteAting obrigatório: Preencher com o código correspondente à parte atingida. <b>Validação:</b> Deve ser um código válido e existente na Tabela 13."
			);
		}

		if($dado['lateralidade'] == '') {
			$validacao[] = array(
				"titulo" => 'localAcidente',
				"descricao" => "Campo evtCAT/cat/parteAtingida/lateralidade obrigatório: Lateralidade da(s) parte(s) atingida(s). Nos casos de órgãos bilaterais, ou seja, que se situam dos lados do corpo, assinalar o lado (direito ou esquerdo). Ex.: Caso o órgão atingido seja perna, apontar qual foi a atingida (perna direita, perna esquerda ou ambas). Se o órgão atingido é único (como, por exemplo, a cabeça), assinalar este campo como não aplicável. <b>Valores válidos:</b> 0 - Não aplicável/ 1 - Esquerda/ 2 - Direita/ 3 - Ambas"
			);
		}


		### FIM parteAtingida ###
		
		### agenteCausador ###
		if(empty($dado['codAgntCausador'])) {
			$validacao[] = array(
				"titulo" => 'localAcidente',
				"descricao" => "Campo evtCAT/cat/agenteCausador/codAgntCausador obrigatório:Preencher com o código correspondente ao agente causador do acidente. <b>Validação:</b> Deve ser um código válido e existente na Tabela 14 ou na Tabela 15."
			);
		}
		### FIM agenteCausador ###
		// debug($dado);exit;
		### atestado ###
		$atestado = true;
		if(!empty($dado['dtAtendimento'])) { $atestado = true; } 
		if($dado['hrAtendimento'] != '0000') { $atestado = true; } 
		if(isset($dado['indInternacao'])) {
			$indInternacao = trim($dado['indInternacao']);
			if(!empty($indInternacao)) { 
				$atestado = true; 
			}
		}
		if(isset($dado['durTrat'])) {
			if(!empty($dado['durTrat'])) { 
				$atestado = true; 
			}
		}
		if($dado['indAfast'] == 'S') { $atestado = true; } 
		if(!empty($dado['dscLesao'])) { $atestado = true; }
		if(!empty($dado['codCID'])) { $atestado = true; } 

		if($atestado) {

			// if(!empty($dado['dtAtendimento'])) {
				if($dado['dtAtendimento'] == '') {
					$validacao[] = array(
						"titulo" => 'atestado',
						"descricao" => "Campo evtCAT/cat/atestado/dtAtendimento obrigatório: Data do atendimento. <b>Validação:</b> Deve ser uma data válida, igual ou anterior à data atual."
					);
				}
				else {
					if($dado['dtAtendimento'] > date('Y-m-d')) {
						$validacao[] = array(
							"titulo" => 'atestado',
							"descricao" => "Campo evtCAT/cat/atestado/dtAtendimento obrigatório: Data do atendimento. <b>Validação:</b> Deve ser uma data válida, igual ou anterior à data atual."
						);
					}
				}
			// }

			if(empty($dado['hrAtendimento'])) {
				$validacao[] = array(
					"titulo" => 'atestado',
					"descricao" => "Campo evtCAT/cat/atestado/hrAtendimento obrigatório: Hora do atendimento, no formato HHMM. <b>Validação:</b> Deve estar no intervalo entre [0000] e [2359], criticando inclusive a segunda parte do número, que indica os minutos, que deve ser menor ou igual a 59."
				);
			}

			if(empty($dado['indInternacao'])) {
				$validacao[] = array(
					"titulo" => 'atestado',
					"descricao" => "Campo evtCAT/cat/atestado/indInternacao obrigatório: Indicativo de internação. <b>Valores válidos: </b> S - Sim / N - Não"
				);
			}

			if(empty($dado['durTrat'])) {
				$validacao[] = array(
					"titulo" => 'atestado',
					"descricao" => "Campo evtCAT/cat/atestado/durTrat obrigatório: Duração estimada do tratamento, em dias."
				);
			}

			if(empty($dado['indAfast'])) {
				$validacao[] = array(
					"titulo" => 'atestado',
					"descricao" => "Campo evtCAT/cat/atestado/indAfast obrigatório: Indicativo de afastamento do trabalho durante o tratamento. <b>Valores válidos: </b> S - Sim / N - Não <b>Validação:</b> Se o campo indCatObito for igual a [S], o campo deve sempre ser preenchido com [N]."
				);
			}

			if(empty($dado['dscLesao'])) {
				$validacao[] = array(
					"titulo" => 'atestado',
					"descricao" => "Campo evtCAT/cat/atestado/dscLesao obrigatório: Preencher com a descrição da natureza da lesão. <b>Validação:</b> Deve ser um código válido e existente na Tabela 17."
				);
			}

			if(!empty($dado['dscCompLesao'])) {
				if($dado['dscCompLesao'] == '') {
					$validacao[] = array(
						"titulo" => 'atestado',
						"descricao" => "Campo evtCAT/cat/atestado/dscCompLesao obrigatório: Descrição complementar da lesão."
					);
				}
			}

			if(!empty($dado['diagProvavel'])) {
				if($dado['diagProvavel'] == '') {
					$validacao[] = array(
						"titulo" => 'atestado',
						"descricao" => "Campo evtCAT/cat/atestado/diagProvavel obrigatório: Diagnóstico provável."
					);
				}
			}

			if(empty($dado['codCID'])) {
				$validacao[] = array(
					"titulo" => 'atestado',
					"descricao" => "Campo evtCAT/cat/atestado/codCID obrigatório: Informar o código da tabela de Classificação Internacional de Doenças - CID. <b>Validação:</b> Deve ser preenchido com caracteres alfanuméricos, conforme opções constantes na tabela CID."
				);
			}

			### emitente ###
			if(empty($dado['nmEmit'])) {
				$validacao[] = array(
					"titulo" => 'emitente',
					"descricao" => "Campo evtCAT/cat/atestado/emitente/nmEmit obrigatório: Nome do médico/dentista que emitiu o atestado."
				);
			}

			if(empty($dado['ideOC'])) {
				$validacao[] = array(
					"titulo" => 'emitente',
					"descricao" => "Campo evtCAT/cat/atestado/emitente/ideOC obrigatório: Órgão de classe. <b>Valores válidos:</b> 1 - Conselho Regional de Medicina - CRM/ 2 - Conselho Regional de Odontologia - CRO/ 3 - Registro do Ministério da Saúde - RMS"
				);
			}

			if(empty($dado['nrOC'])) {
				$validacao[] = array(
					"titulo" => 'emitente',
					"descricao" => "Campo evtCAT/cat/atestado/emitente/nrOC obrigatório: Número de inscrição no órgão de classe."
				);
			}

			if(empty($dado['ufOC'])) {
				$validacao[] = array(
					"titulo" => 'emitente',
					"descricao" => "Campo evtCAT/cat/atestado/emitente/ufOC obrigatório: Sigla da UF do órgão de classe. <b>Valores válidos:</b> AC, AL, AP, AM, BA, CE, DF, ES, GO, MA, MT, MS, MG, PA, PB, PR, PE, PI, RJ, RN, RS, RO, RR, SC, SP, SE, TO"
				);
			}


			### FIM emitente ###


		}//fim atestado true
		### FIM atestado ###


		### catOrigem ###
		
		if(!empty($dado['nrRecCatOrig'])) {
			if($dado['nrRecCatOrig'] == '') {
				$validacao[] = array(
					"titulo" => 'atestado',
					"descricao" => "Campo evtCAT/cat/catOrigem/nrRecCatOrig obrigatório: Informar o número do recibo da última CAT referente ao mesmo acidente/doença relacionada ao trabalho, nos casos:a) de CAT de reabertura;b) de óbito, quando houver CAT anterior. <B>Validação:</B> Deve corresponder ao número do recibo do arquivo relativo à última CAT informada anteriormente, pertencente ao mesmo contrato, desde que indCatObito da última CAT informada seja igual a [N]. O sistema não efetuará a conferência da informação se dtAcid for anterior a sucessaoVinc/dtTransf, transfDom/dtTransf ou dtAltCPF do evento S-2200. OBS.: Quando a data do acidente for anterior à data de obrigatoriedade do empregador ao envio deste evento, a CAT de reabertura e/ou de óbito não devem ser informadas ao eSocial, mantendo-se o procedimento realizado na emissão da CAT original."
				);
			}
		}
		### FIM catOrigem ###
		
		####FIM cat###
		
		//debug($dado);
		// debug($validacao);
		// exit;

		return $validacao;


	}//fim valida_regra_campos_s2210

	/**
	 * [getDadosComplementaresS2210 metodo para pegar os dados complementares para a integracao]
	 * @param  [type] $codigo [description]
	 * @return [type]         [description]
	 */
	public function getDadosComplementaresIntegracao($codigo,$codigo_tipo,$codigo_funcionario = null)
	{

		switch ($codigo_tipo) {
			case '1'://S2210
				//varre os conditions
		    	$conditions['Cat.codigo_empresa'] = 1;
		    	$conditions['Cat.codigo'] = $codigo;

		    	$from = "RHHealth.dbo.cat AS Cat
				INNER JOIN RHHealth.dbo.funcionario_setores_cargos AS FuncionarioSetorCargo ON (FuncionarioSetorCargo.codigo = Cat.codigo_funcionario_setor_cargo)
				INNER JOIN RHHealth.dbo.cliente_funcionario AS ClienteFuncionario ON (ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario)
				INNER JOIN RHHealth.dbo.funcionarios AS Funcionario ON (Funcionario.codigo = ClienteFuncionario.codigo_funcionario)";

				break;
			
			case '2'://S2220
				
				//pega o codigo do aso
		    	$this->Configuracao = ClassRegistry::init("Configuracao");
		    	$codigo_aso = $this->Configuracao->getChave("INSERE_EXAME_CLINICO");

		    	//varre os conditions
		    	$conditions['PedidoExame.codigo_empresa'] = 1;
		    	$conditions['ItemPedidoExame.codigo_exame'] = $codigo_aso;
		    	$conditions['PedidoExame.codigo'] = $codigo;
    	
		 		$from = "RHHealth.dbo.pedidos_exames AS PedidoExame
				INNER JOIN RHHealth.dbo.itens_pedidos_exames AS ItemPedidoExame ON (PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames)
				INNER JOIN RHHealth.dbo.funcionarios AS Funcionario ON (PedidoExame.codigo_funcionario = Funcionario.codigo)
				INNER JOIN RHHealth.dbo.cliente_funcionario AS ClienteFuncionario ON (PedidoExame.codigo_cliente_funcionario = ClienteFuncionario.codigo)
				INNER JOIN RHHealth.dbo.funcionario_setores_cargos AS FuncionarioSetorCargo ON (PedidoExame.codigo_func_setor_cargo = FuncionarioSetorCargo.codigo)
				";

				break;

			case '3'://S2230

				//varre os conditions
    			$conditions['Atestado.codigo_empresa'] = 1;
    			$conditions['Atestado.codigo'] = $codigo;

	 			$from = "RHHealth.dbo.atestados AS Atestado
				INNER JOIN RHHealth.dbo.funcionario_setores_cargos AS FuncionarioSetorCargo ON (FuncionarioSetorCargo.codigo = Atestado.codigo_func_setor_cargo)
				INNER JOIN RHHealth.dbo.cliente_funcionario AS ClienteFuncionario ON (FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo AND Atestado.codigo_cliente_funcionario = ClienteFuncionario.codigo)
				INNER JOIN RHHealth.dbo.funcionarios AS Funcionario ON (Funcionario.codigo = ClienteFuncionario.codigo_funcionario)
				";
				
				break;
			
			case '4'://S2240
				//varre os conditions
    			$conditions['GrupoExposicao.codigo_empresa'] = 1;
    			$conditions['GrupoExposicao.codigo'] = $codigo;

		    	// debug($where);exit;

				//monta a query para pegar os dados e podermos validar se tem algum erro conforme o layout do esocial
				$from = "grupo_exposicao as GrupoExposicao
				INNER JOIN clientes_setores cs on cs.codigo = GrupoExposicao.codigo_cliente_setor
				INNER JOIN cliente c on c.codigo = cs.codigo_cliente_alocacao
				INNER JOIN funcionario_setores_cargos FuncionarioSetorCargo ON FuncionarioSetorCargo.codigo = (SELECT TOP 1 FuncionarioSetorCargoSub.codigo 
																			FROM funcionario_setores_cargos FuncionarioSetorCargoSub
																				INNER JOIN cliente_funcionario cfSub on FuncionarioSetorCargoSub.codigo_cliente_funcionario = cfSub.codigo
																			WHERE FuncionarioSetorCargoSub.codigo_cliente_alocacao = c.codigo
																				AND cfSub.codigo_funcionario = {$codigo_funcionario}
																				AND (FuncionarioSetorCargoSub.data_fim IS NULL OR FuncionarioSetorCargoSub.data_fim <> '')
																			ORDER BY FuncionarioSetorCargoSub.codigo DESC)
				INNER JOIN cliente_funcionario ClienteFuncionario ON FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo
				INNER JOIN funcionarios Funcionario on Funcionario.codigo = ClienteFuncionario.codigo_funcionario";
				
				break;

		}//fim switch


    	//monta o where
    	$where = $this->montaWhereForXML($conditions);

    	// debug($from);
    	// debug($where);exit;

		//monta a query para pegar os dados e podermos validar se tem algum erro conforme o layout do esocial
		$query = "
			SELECT TOP 1
				ClienteFuncionario.codigo AS codigo_cliente_funcionario
		       ,FuncionarioSetorCargo.codigo AS codigo_funcionario_setor_cargo
		       ,Funcionario.codigo AS codigo_funcionario
		       ,FuncionarioSetorCargo.codigo_setor AS codigo_setor
		       ,FuncionarioSetorCargo.codigo_cargo AS codigo_cargo
		       ,ClienteFuncionario.codigo_cliente AS codigo_cliente_matriz
		       ,FuncionarioSetorCargo.codigo_cliente_alocacao AS codigo_cliente_alocacao
		       ,ClienteFuncionario.matricula AS matricula
			FROM {$from}
			WHERE {$where}";

		// print $query; 
		$val = $this->query($query);
		// debug($val);exit;

		return $val;
	}//fim getDadosComplementaresS2210

	/**
	 * [gerar_s3000 metodo para gerar o evento de s-3000 exclusão de eventos]
	 * @return [type] [description]
	 */
	public function gerar_s3000($codigo_int_esocial_evento)
	{

		sleep(1);
		$query = "
		SELECT	
			CAST(REPLACE(REPLACE(REPLACE(
			(select 	
				(select REPLACE(REPLACE(REPLACE(
					(SELECT
						CONCAT('ID1',
							CONCAT( substring(isnull(c.codigo_documento,c.codigo_documento_real),0,9), REPLICATE('0', (14 - LEN(substring(isnull(c.codigo_documento,c.codigo_documento_real),0,9)))) ),
							FORMAT(CURRENT_TIMESTAMP, 'yyyyMMddHHmmss', 'pt-BR'),
							REPLICATE('0', (4)),1) AS \"@Id\"
								
						,(SELECT 
							evt.ambiente_esocial as tpAmb,
							'1' as procEmi,
							'1' as verProc
						FOR XML PATH('')) AS 'ideEvento'

						,'1' as 'ideEmpregador/tpInsc'
						,substring(isnull(c.codigo_documento,c.codigo_documento_real),0,9) as 'ideEmpregador/nrInsc'
								
						,tp_evt.descricao AS 'infoExclusao/tpEvento'
						,evt_principal.codigo_recibo AS 'infoExclusao/nrRecEvt'

						,f.cpf AS 'infoExclusao/ideTrabalhador/cpfTrab'

					FROM int_esocial_eventos evt
					WHERE evt.codigo = evt_principal.codigo
					FOR XML PATH('evtExclusao'))
				, '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', ''))
						

		FROM int_esocial_eventos as evt_principal
			INNER JOIN cliente c ON evt_principal.codigo_cliente = c.codigo
			INNER JOIN int_esocial_tipo_evento tp_evt  ON evt_principal.codigo_int_esocial_tipo_evento = tp_evt.codigo
			INNER JOIN funcionarios f on evt_principal.codigo_funcionario = f.codigo
		WHERE evt_principal.codigo = {$codigo_int_esocial_evento}
		FOR XML PATH('eSocial')), '&lt;', '<'), '&gt;', '>'), '&amp;#x20;', '') as text) as val
		";

		// debug($query);exit;
		$val = $this->query($query);
		// debug($val);exit;
		/* $dados = "<?xml version='1.0' encoding='UTF-8'?>".$val[0][0]['val'];*/
		$dados = "<?xml version='1.0' encoding='UTF-8'?>".utf8_decode(utf8_encode($val[0][0]['val']));
		
		//deve retirar os acentos pois o esocial nao aceita
		$dados = Comum::tirarAcentos($dados);

		// print $dados;exit;
		return $dados;

	}//fim gerar_s3000

}