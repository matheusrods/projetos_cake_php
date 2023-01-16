<?php
class Consulta extends AppModel
{
    var $name = 'Consulta';
    public $useTable = false;
    var $actsAs = array('Secure');

    function converteFiltrosEmConditions($filtros, $acao)
    {

        $conditions = array();
        if ($acao == 'consulta_documentos_pendentes') {
            if (isset($filtros['codigo_proposta_credenciamento']) && !empty($filtros['codigo_proposta_credenciamento'])) {
                $conditions['PropostaCredenciamento.codigo'] = $filtros['codigo_proposta_credenciamento'];
            }

            if (isset($filtros['codigo_estado_endereco']) && !empty($filtros['codigo_estado_endereco'])) {
                $conditions['PropostaCredEndereco.codigo_estado_endereco'] = $filtros['codigo_estado_endereco'];
            }

            if (isset($filtros['codigo_cidade_endereco']) && !empty($filtros['codigo_cidade_endereco'])) {
                $conditions['PropostaCredEndereco.codigo_cidade_endereco'] = $filtros['codigo_cidade_endereco'];
            }

            if (isset($filtros['codigo_documento']) && !empty($filtros['codigo_documento'])) {
                $conditions['PropostaCredEndereco.codigo_documento'] = str_replace(array('-', '.', '/'), array('', '', ''), $filtros['codigo_documento']);
            }
        } else if ($acao == 'consulta_propostas') {
            if (isset($filtros['razao_social']) && !empty($filtros['razao_social'])) {
                $conditions['OR']['PropostaCredenciamento.razao_social LIKE'] = '%' . $filtros['razao_social'] . '%';
                $conditions['OR']['PropostaCredenciamento.nome_fantasia LIKE'] = '%' . $filtros['razao_social'] . '%';
            }

            if (isset($filtros['usuario']) && !empty($filtros['usuario'])) {
                $conditions['PropostaCredenciamento.codigo_usuario_inclusao'] = $filtros['usuario'];
                // $conditions['AND'][1]['OR']['PropostaCredenciamento.codigo_usuario_alteracao'] = $filtros['usuario'];
            }

            if (isset($filtros['codigo_estado_endereco']) && !empty($filtros['codigo_estado_endereco'])) {
                $conditions['PropostaCredEndereco.codigo_estado_endereco'] = $filtros['codigo_estado_endereco'];
            }

            if (isset($filtros['codigo_cidade_endereco']) && !empty($filtros['codigo_cidade_endereco'])) {
                $conditions['PropostaCredEndereco.codigo_cidade_endereco'] = $filtros['codigo_cidade_endereco'];
            }

            if (isset($filtros['motivo']) && !empty($filtros['motivo'])) {
                $conditions['PropostaCredenciamento.codigo_motivo_recusa'] = $filtros['motivo'];
            }

            if (isset($filtros['data_inicial_cancelamento']) && !empty($filtros['data_inicial_cancelamento'])) {
                $conditions["PropostaCredHistorico.data_inclusao >= "] = Comum::dateToDb($filtros['data_inicial_cancelamento']) . " 23:59:59";
            }
            if (isset($filtros['data_final_cancelamento']) && !empty($filtros['data_final_cancelamento'])) {
                $conditions["PropostaCredHistorico.data_inclusao <= "] = Comum::dateToDb($filtros['data_final_cancelamento']) . " 23:59:59";
            }

            if (isset($filtros['motivo']) && !empty($filtros['motivo'])) {
                $conditions['PropostaCredenciamento.codigo_motivo_recusa'] = $filtros['motivo'];
            }

            if (isset($filtros['data_inicial']) && !empty($filtros['data_inicial'])) {
                $conditions["PropostaCredenciamento.data_inclusao >= "] = Comum::dateToDb($filtros['data_inicial']) . " 23:59:59";
            }

            if (isset($filtros['data_final']) && !empty($filtros['data_final'])) {
                $conditions["PropostaCredenciamento.data_inclusao <= "] = Comum::dateToDb($filtros['data_final']) . " 00:00:00";
            }

            if (!empty($filtros['codigo_status_proposta_credenciamento'])) {
                $conditions['PropostaCredenciamento.codigo_status_proposta_credenciamento'] = $filtros['codigo_status_proposta_credenciamento'];
            }
        }

        if (!empty($filtros['codigo_cliente'])) {
            $conditions['PropostaCredenciamento.codigo_status_proposta_credenciamento'] = $filtros['codigo_status_proposta_credenciamento'];
        }

        return $conditions;
    }

    /*
	trecho comentado por que o modo de busca agora serao varios status, nao somente vencidos.

	function converteFiltEmCond_FornecedorDocsVencidos($data) {
 		
 		unset($data['codigo_fornecedorCodigo']);
		unset($data['codigo_produto']);
		unset($data['codigo_servico']);
		unset($data['tipo_servico']);
		
        $conditions = array();
 		
        if (!empty($data['estado']))
            $conditions['EnderecoEstado.codigo'] = $data['estado'];
		
		if (! empty ( $data ['cidade'] ))
			$conditions['EnderecoCidade.codigo'] = $data['cidade'];

		if (!empty($data['codigo_fornecedor']))
            $conditions['Fornecedor.codigo'] = $data['codigo_fornecedor'];

		if (!empty($data['documento']))
            $conditions['TipoDocumento.codigo'] = $data['documento'];

        if((!empty($data['data_inicial'])) && (!empty($data['data_final']))) {         
            $inicio = $data['data_inicial'] .' 00:00:00';
            $final = $data['data_final'] .' 23:59:59';
            $inicial = AppModel::dateToDbDate2($inicio);
            $final = AppModel::dateToDbDate2($final);
            $conditions['FornecedorDocumento.data_validade BETWEEN ? AND ?'] = array($inicial,$final);
        }  

        return $conditions;
    }
    */

    function converteFiltEmCond_ProdutosServicos($data)
    {

        unset($data['codigo_fornecedorCódigo']);
        $conditions = array();

        if (!empty($data['estado'])) {
            $conditions['FornecedorEndereco.estado_descricao '] = $data['estado'];
        }

        if (!empty($data['cidade'])) {
            $encoding = mb_internal_encoding();
            // $conditions['EnderecoCidade.codigo'] = $data['cidade'];
            $conditions[] = array("(FornecedorEndereco.cidade LIKE '%" . mb_strtolower($data['cidade'], $encoding) . "%' COLLATE Latin1_General_CI_AI OR FornecedorEndereco.cidade LIKE '%" . mb_strtoupper($data['cidade'], $encoding) . "%' COLLATE Latin1_General_CI_AI)");
        }

        if (!empty($data['codigo_fornecedor'])) {
            $conditions['Fornecedor.codigo'] = $data['codigo_fornecedor'];
        }

        if (!empty($data['codigo_produto'])) {
            $conditions['Produto.codigo'] = $data['codigo_produto'];
        }

        if (!empty($data['codigo_servico'])) {
            $conditions['Servico.codigo'] = $data['codigo_servico'];
        }

        if (!empty($data['tipo_servico'])) {
            $conditions['Servico.tipo_servico'] = $data['tipo_servico'];
        }

        if (isset($data['ativo'])) {
            if ($data['ativo'] === '0') {
                $conditions[] = '(Fornecedor.ativo = ' . $data['ativo'] . ' OR Fornecedor.ativo IS NULL)';
            } else if ($data['ativo'] == '1') {
                $conditions['Fornecedor.ativo'] = $data['ativo'];
            }
        }

        $codigo_prestadores_testes = $this->GetPrestadoresTestes();

        if (!empty($codigo_prestadores_testes)) {
            $conditions[] = array('Fornecedor.codigo NOT IN (' . $codigo_prestadores_testes . ')'); //nao pode conter na lista dos prestadores testes	
        }

        /// tratamento feito para o cdct-265, estamos buscando aqui todos os fornecedores que estao como ambulatorios ou prestador particular, os que tiverem com essa condicao nao podem aparecer na lista

        $this->Fornecedor = &ClassRegistry::init('Fornecedor');
        $get_prestadores_amb_part = $this->Fornecedor->find('list', array('fields' => array('codigo'), 'conditions' => array('ambulatorio' => 1, 'prestador_particular' => 1)));

        if (!empty($get_prestadores_amb_part)) {
            $fornecedores_ambulatorios_particulares = implode(',', $get_prestadores_amb_part);
            $conditions[] = array('ListaDePreco.codigo_fornecedor NOT IN (' . $fornecedores_ambulatorios_particulares . ')');
        } //fim

        return $conditions;
    }

    function converteFiltrosEmConditionsPendencia($filtro, $codigo_cliente, $tipo)
    {
        $conditions = array();

        if ($filtro) {

            $filtro = array_filter($filtro);

            // Regras para filtros
            $converteFiltrosEmConditions = array(
                'codigo_cargo' => 'ClientesSetoresCargos.codigo_cargo',
                'codigo_setor' => 'ClientesSetoresCargos.codigo_setor',
                'status' => 'status'
            );

            // Converte Filtros
            foreach ($filtro as $index => $value) {
                if (!array_key_exists($index, $converteFiltrosEmConditions)) continue;
                $conditions[$converteFiltrosEmConditions[$index]] = $value;
            }
        }

        $conditions['GrupoEconomicoCliente.codigo_cliente'] = $codigo_cliente;
        $conditions['Setores.ativo']                         = 1;
        $conditions['Cargos.ativo']                         = 1;

        if (!empty($filtro['codigo_funcionario'])) {

            if ($tipo == 'ppra') {
                $conditions['GrupoExposicao.codigo_funcionario'] = $filtro['codigo_funcionario'];
            } else if ($tipo == 'pcmso') {
                $conditions['AplicacaoExame.codigo_funcionario'] = $filtro['codigo_funcionario'];
            }
        }

        /* incluido condições de status */
        if (!empty($conditions['status']) && isset($conditions['status'])) {

            if ($conditions['status'] == 1) {
                if ($tipo == 'ppra') {
                    $conditions[] = "GrupoExposicaoRisco.codigo_grupo_exposicao IS NULL";
                } else if ($tipo == 'pcmso') {
                    $conditions[] = "AplicacaoExame.codigo_cliente_alocacao IS NULL";
                }
            } else if ($conditions['status'] == 2) {

                if ($tipo == 'ppra') {
                    $conditions[] = "GrupoExposicaoRisco.codigo_grupo_exposicao IS NOT NULL";
                } else if ($tipo == 'pcmso') {
                    $conditions[] = "AplicacaoExame.codigo_cliente_alocacao IS NOT NULL";
                }
            } else if ($conditions['status'] == 3) {
                $conditions[] = "ValidacaoPPRA.status_validacao = 0";
            }
            unset($conditions['status']);
        }

        return $conditions;
    }


    public function envia_arquivo_pendencia_ppra_pcmso()
    {
        //tira o tempo de limit para processamento
        set_time_limit(0);

        $verif = $this->verifica_pendencia_ppra_pcmso();

        // debug($verif);exit;

        if (!empty($verif)) { // Tem alguma pendencia ?

            // Verifica condições Pendentes
            $verifPend = $verif[0][0];
            $PPRA = $verifPend['PPRA'];
            $TOTAL_PPRA = $verifPend['TOTAL_PPRA'];
            $PCMSO = $verifPend['PCMSO'];
            $TOTAL_PCMSO = $verifPend['TOTAL_PCMSO'];

            // Para quem enviar ?
            $to_ppra = false; // Sem pendencias
            if ($PPRA < $TOTAL_PPRA) {
                $to_ppra = true;
            }

            $to_pcmso = false;
            if ($PCMSO < $TOTAL_PCMSO) {
                $to_pcmso = true;
            }

            // Sem pendencias
            if (!$to_ppra && !$to_pcmso) {
                $msgErro = "Sem pendencias para enviar";
                return false;
            }


            $usuarios = $this->getUsersRecePendenciaPppraPcmso($to_ppra, $to_pcmso);

            $msgErro = "";

            //verifica se existe uruaios
            if (!empty($usuarios)) {

                //declara o array com os registros
                $reg = array();
                $contador = 0;
                $codigo_empresa = array();

                //varre os usuarios
                foreach ($usuarios as $usuario) {

                    //Se não possui e-mail de contato, não gera o arquivo
                    if (empty($usuario['Usuario']['email'])) {
                        $msgErro = "Usuário não possui e-mail para enviar o arquivo de Vigência";
                    }

                    $send = '';

                    if ($usuario['UsuarioAlertaTipo']['codigo_alerta_tipo'] == 13) { // PPRA

                        //template do e-mail utilizado no envio do arquivo
                        $template = 'envio_arquivo_pendencia_ppra';
                        $assunto = 'Pendencia PPRA';

                        $send = 'ppra';
                    } else { // PCMSO

                        $template = 'envio_arquivo_pendencia_pcsmo';
                        $assunto = 'Pendencia PCMSO';

                        $send = 'pcmso';
                    }

                    $codigo_empresa = (!empty($usuario['Usuario']['codigo_empresa']) ? $usuario['Usuario']['codigo_empresa'] : 'null');

                    // Enviar ?
                    if ($send) {

                        //monta os registros do alerta
                        $reg['Alerta']['codigo_cliente'] = null;
                        $reg['Alerta']['descricao'] = $assunto;
                        $reg['Alerta']['assunto'] = $assunto;
                        $reg['Alerta']['data_inclusao'] = date('Y-m-d H:i:s');
                        $reg['Alerta']['codigo_alerta_tipo'] = $usuario['UsuarioAlertaTipo']['codigo_alerta_tipo']; //codigo para o alerta para vigencia ppra pcmso
                        $reg['Alerta']['descricao_email'] = $this->montaEmail($send, $codigo_empresa);
                        $reg['Alerta']['model'] = "Usuario"; //para processamento ao realizar o alerta
                        $reg['Alerta']['foreign_key'] = $usuario['Usuario']['codigo']; //codigo para buscar qual é o registro que vai ser processado

                        // debug($reg);exit;
                        //realiza o insert na tabela dos alertas
                        if ($this->insereAlerta($reg)) {
                            $contador++;
                        } //fim inseriu na alerta


                    } //fim dados vigencia

                    if ($msgErro != "") {
                        $this->log('Usuario:' . $usuario['Usuario']['nome'], 'debug');
                        $this->log($ex->getMessage(), 'debug');

                        $msgErro = "";
                    }
                } //fim foreach

            } else {
                $msgErro = "Nenhum usuario para enviar e-mail!";
                return false;
            } //fim if empty usuarios



            return true;
        } else {

            $msgErro = "Sem pendencias para enviar";
            return false;
        }
    }

    public function getUsersRecePendenciaPppraPcmso($to_ppra, $to_pcmso)
    {

        $Usuario = ClassRegistry::init('Usuario');

        // popula varivel para SELECT
        $fields = array(
            'Usuario.codigo',
            'Usuario.email',
            'Usuario.nome',
            'Usuario.codigo_cliente',
            'UsuarioAlertaTipo.codigo_alerta_tipo',
            'Usuario.codigo_empresa'
        );

        $conditions = array(
            'Usuario.email IS NOT NULL',
            'Usuario.alerta_email' => 1
        );

        if ($to_ppra && !$to_pcmso) {
            $conditions[] = "UsuarioAlertaTipo.codigo_alerta_tipo = 13";
        } else if (!$to_ppra && $to_pcmso) {
            $conditions[] = "UsuarioAlertaTipo.codigo_alerta_tipo = 14";
        } else {
            $conditions[] = "UsuarioAlertaTipo.codigo_alerta_tipo IN (13,14)";
        }

        // popula varivel para FROM
        $joins = array(
            array(
                "table" => "usuarios_alertas_tipos",
                "alias" => "UsuarioAlertaTipo",
                "conditions" => "Usuario.codigo = UsuarioAlertaTipo.codigo_usuario"
            )
        );

        // define options para ORM
        $options = array(
            "fields" => $fields,
            "joins" => $joins,
            "conditions" => $conditions,
            "recursive" => -1
        );

        // pr($Usuario->find('sql', $options));exit;

        return $Usuario->find('all', $options);
    }

    public function verifica_pendencia_ppra_pcmso()
    {

        //paramentros para processar as querys que tenham mais de 1 min de processamento
        set_time_limit(300);
        ini_set('default_socket_timeout', 1000);
        ini_set('mssql.connect_timeout', 1000);
        ini_set('mssql.timeout', 3000);

        $GrupoEconomicoCliente = ClassRegistry::init('GrupoEconomicoCliente');

        $conditions = array(
            "Cliente.ativo = 1",
            "(PPRA.STATUS_PPRA = 1 OR PCMSO.STATUS_PCMSO = 1)"
        );

        $fields = array(
            'SUM(PPRA.TOTAL_GrupoExposicao) AS PPRA',
            'SUM(PPRA.TOTAL) AS TOTAL_PPRA',
            'SUM(PCMSO.TOTAL_AplicacaoExame) AS PCMSO',
            'SUM(PCMSO.TOTAL) AS TOTAL_PCMSO',
        );

        $joins = array(
            array(
                "table" => "grupos_economicos",
                "alias" => "GrupoEconomico",
                "type" => "LEFT",
                "conditions" => "GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo"
            ),
            array(
                "table" => "cliente",
                "alias" => "Cliente",
                "type" => "LEFT",
                "conditions" => "GrupoEconomicoCliente.codigo_cliente = [Cliente].[codigo]",
            ),
            array(
                "table" => "( SELECT COUNT(GrupoExposicao.codigo) AS TOTAL_GrupoExposicao, COUNT(ClientesSetoresCargos.codigo) AS TOTAL, ClientesSetoresCargos.codigo_cliente_alocacao, (CASE WHEN COUNT(GrupoExposicao.codigo) < COUNT(ClientesSetoresCargos.codigo) THEN 1 ELSE 2 END) AS STATUS_PPRA FROM clientes_setores_cargos AS ClientesSetoresCargos INNER JOIN cargos AS Cargo ON Cargo.codigo = ClientesSetoresCargos.codigo_cargo AND Cargo.ativo = 1 INNER JOIN setores Setor ON Setor.codigo = ClientesSetoresCargos.codigo_setor AND Setor.ativo = 1 LEFT JOIN clientes_setores AS ClientesSetores ON (ClientesSetoresCargos.codigo_setor = ClientesSetores.codigo_setor and ClientesSetores.codigo_cliente = ClientesSetoresCargos.codigo_cliente) LEFT JOIN grupo_exposicao AS GrupoExposicao ON (ClientesSetores.codigo = GrupoExposicao.codigo_cliente_setor AND ClientesSetoresCargos.codigo_cargo = GrupoExposicao.codigo_cargo AND (SELECT COUNT(*) FROM grupos_exposicao_risco GrupoExposicaoRisco WHERE GrupoExposicaoRisco.codigo_grupo_exposicao = GrupoExposicao.codigo ) > 0) WHERE ([ClientesSetoresCargos].[ativo] = 1
          OR [ClientesSetoresCargos].[ativo] IS NULL) GROUP BY ClientesSetoresCargos.codigo_cliente_alocacao )",
                "alias" => "PPRA",
                "conditions" => "PPRA.codigo_cliente_alocacao = Cliente.codigo",
            ),
            array(
                "table" => "( SELECT * FROM ( SELECT COUNT(AplicacaoExame.codigo) AS TOTAL_AplicacaoExame, COUNT(ClientesSetoresCargos.codigo) AS TOTAL, ClientesSetoresCargos.codigo_cliente_alocacao, ClientesSetoresCargos.codigo_setor, ClientesSetoresCargos.codigo_cargo, (CASE WHEN COUNT(AplicacaoExame.codigo) = 0 THEN 1 ELSE 2 END) AS STATUS_PCMSO, (CASE WHEN COUNT(GrupoExposicao.codigo) = 0 THEN 1 ELSE 2 END) AS STATUS_PPRA FROM clientes_setores_cargos AS ClientesSetoresCargos INNER JOIN cargos AS Cargo ON Cargo.codigo = ClientesSetoresCargos.codigo_cargo AND Cargo.ativo = 1 INNER JOIN setores Setor ON Setor.codigo = ClientesSetoresCargos.codigo_setor AND Setor.ativo = 1 LEFT JOIN clientes_setores AS ClientesSetores ON (ClientesSetoresCargos.codigo_setor = ClientesSetores.codigo_setor AND ClientesSetores.codigo_cliente = ClientesSetoresCargos.codigo_cliente) LEFT JOIN aplicacao_exames AS AplicacaoExame ON (ClientesSetoresCargos.codigo_cargo = AplicacaoExame.codigo_cargo AND ClientesSetoresCargos.codigo_setor = AplicacaoExame.codigo_setor AND ClientesSetoresCargos.codigo_cliente_alocacao = AplicacaoExame.codigo_cliente_alocacao) LEFT JOIN grupo_exposicao AS GrupoExposicao ON (ClientesSetores.codigo = GrupoExposicao.codigo_cliente_setor AND ClientesSetoresCargos.codigo_cargo = GrupoExposicao.codigo_cargo AND (SELECT COUNT(*) FROM grupos_exposicao_risco GrupoExposicaoRisco WHERE GrupoExposicaoRisco.codigo_grupo_exposicao = GrupoExposicao.codigo ) > 0) WHERE ([ClientesSetoresCargos].[ativo] = 1
          OR [ClientesSetoresCargos].[ativo] IS NULL) GROUP BY ClientesSetoresCargos.codigo_cliente_alocacao, ClientesSetoresCargos.codigo_setor, ClientesSetoresCargos.codigo_cargo ) AS b WHERE b.STATUS_PCMSO = 1 AND b.STATUS_PPRA = 2)",
                "alias" => "PCMSO",
                "conditions" => "PCMSO.codigo_cliente_alocacao = Cliente.codigo",
            ),
        );

        $options = array(
            "fields" => $fields,
            "joins" => $joins,
            "conditions" => $conditions,
            "recursive" => -1
        );

        //pr($GrupoEconomicoCliente->find('sql', $options));exit;

        return $GrupoEconomicoCliente->find('all', $options);
    }

    public function get_pendencia_ppra_pcmso($send, $codigo_empresa = null)
    {

        $GrupoEconomicoCliente = ClassRegistry::init('GrupoEconomicoCliente');

        $conditions = array("(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)");

        // popula varivel para SELECT
        $fields = array(
            "ClientesSetoresCargos.data_inclusao",
            "Setores.descricao AS Setor",
            "Cargos.descricao AS Cargo",
            "Setores.codigo AS CodigoSetor",
            "Cargos.codigo AS CodigoCargo",
            "Cliente.nome_fantasia",
            "Cliente.codigo",
            "(CASE WHEN AplicacaoExame.codigo_cliente_alocacao IS NULL THEN 'Pendente' ELSE 'Concluido' END) AS status_PCMSO",
            "(CASE WHEN GrupoExposicaoRisco.codigo_grupo_exposicao IS NULL THEN 'Pendente' ELSE 'Concluido' END) AS status_PPRA"
        );

        // popula varivel para FROM
        $joins = array(
            array(
                "table"     => "cliente",
                "alias"     => "Cliente",
                "conditions" => "Cliente.codigo = GrupoEconomicoCliente.codigo_cliente AND Cliente.ativo = 1"
            ),
            array(
                "table"     => "clientes_setores_cargos",
                "alias"     => "ClientesSetoresCargos",
                "conditions" => "GrupoEconomicoCliente.codigo_cliente = ClientesSetoresCargos.codigo_cliente_alocacao "
            ),
            array(
                "table"     => "setores",
                "alias"     => "Setores",
                "conditions" => "ClientesSetoresCargos.codigo_setor = Setores.codigo AND Setores.ativo = 1"
            ),
            array(
                "table"     => "cargos",
                "alias"     => "Cargos",
                "conditions" => "ClientesSetoresCargos.codigo_cargo = Cargos.codigo AND Cargos.ativo = 1"
            ),
            array(
                "table"     => "aplicacao_exames",
                "alias"     => "AplicacaoExame",
                "conditions" => "ClientesSetoresCargos.codigo_cargo = AplicacaoExame.codigo_cargo and ClientesSetoresCargos.codigo_setor = AplicacaoExame.codigo_setor and ClientesSetoresCargos.codigo_cliente_alocacao = AplicacaoExame.codigo_cliente_alocacao  ",
                "type"      => "LEFT"
            ),
            array(
                "table"     => "clientes_setores",
                "alias"     => "ClienteSetor",
                "conditions" => "ClienteSetor.codigo_cliente = ClientesSetoresCargos.codigo_cliente_alocacao AND ClienteSetor.codigo_setor = ClientesSetoresCargos.codigo_setor",
                "type"      => "LEFT"
            ),
            array(
                "table"     => "grupo_exposicao",
                "alias"     => "GrupoExposicao",
                "conditions" => " ClienteSetor.codigo = GrupoExposicao.codigo_cliente_setor AND ClientesSetoresCargos.codigo_cargo = GrupoExposicao.codigo_cargo AND (( SELECT COUNT(*) FROM grupos_exposicao_risco ger WHERE ger.codigo_grupo_exposicao = GrupoExposicao.codigo ) > 0)",
                "type"      => "LEFT"
            ),
            array(
                "table"     => "grupos_exposicao_risco",
                "alias"     => "GrupoExposicaoRisco",
                "type"      => "LEFT",
                "conditions" => "GrupoExposicao.codigo = GrupoExposicaoRisco.codigo_grupo_exposicao",
            )
        );

        switch ($send) {
            case 'ppra':
                $conditions[] = "GrupoExposicaoRisco.codigo_grupo_exposicao IS NULL";
                break;

            case 'pcmso':
                $conditions[] = "AplicacaoExame.codigo_cliente_alocacao IS NULL";
                break;
        }

        $conditions[] = "GrupoEconomicoCliente.codigo_empresa = " . $codigo_empresa;

        // define options para ORM
        $options = array(
            "fields"     => $fields,
            "joins"      => $joins,
            "conditions" => $conditions,
            "recursive"  => -1
        );

        // pr($GrupoEconomicoCliente->find('sql', $options));exit;

        return $GrupoEconomicoCliente->find('all', $options);
    }

    public function montaEmail($send, $codigo_empresa)
    {
        //monta o link para disparar por email
        $link = $this->linkPendenciaPpraPcmso($send, $codigo_empresa);

        //monta o html para disparar o alerta
        $html = utf8_encode(
            '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">

				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				</head>

				<body>					
					<div style="clear:both;">
						<div> <img style="display:block;" src="http://portal.rhhealth.com.br/portal/img/logo-rhhealth.png" style="float:left;">
							<hr style="border:1px solid #EEE; display:block;" /> </div>
						<div style="background: #fff; float:none; height: 10px; margin-top:5px; padding:8px 10px 0 0; width:99%;"></div>
					</div>
					<div style="clear:both;padding-top:50px;padding-left:50px;width:98.4%;min-height:300px;">
						<p>
							Segue link para arquivo de pendências de hierarquias de ' . ($send == 'ppra' ? 'PPRA' : 'PCMSO') . ' para análise.<br /><br />
							<br />
						
							<a href="' . $link . '" target="_blank">Visualizar</a><br />
							<br />
						</p>
						<p>Obrigado pela atenção!</p>
								            
						<p>Um abraço,</p>
						<b>Equipe RH Health</b><br />
						<a href="http://www.rhhealth.com.br" target="_blank">www.rhhealth.com.br</a><br />
					</div>
				</body>
			</html>'
        );
        return $html;
    }

    private function linkPendenciaPpraPcmso($send, $codigo_empresa)
    {

        //monta o hash para colocar no link
        $hash = Comum::encriptarLink($send);
        $hash_codigo_empresa = Comum::encriptarLink((string)$codigo_empresa);

        //monta o host
        $host = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? "portal.rhhealth.com.br" : (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO ? "tstportal.rhhealth.com.br" : 'rhhealth.localhost'));

        //monta o link
        $link_vigencia = "https://{$host}/portal/consultas/gera_arquivo_pendencia_ppra_pcmso?key=" . urlencode($hash) . "&ce=" . urlencode($hash_codigo_empresa);

        //retorno o link a ser acessado
        return $link_vigencia;
    }

    public function gerar_arquivo_pendencia_ppra_pcmso($send, $codigo_empresa = null)
    {

        //pega os dados de vigencia
        $dados = $this->get_pendencia_ppra_pcmso($send, $codigo_empresa);

        //verifica se tem registros para gerar o arquivo
        if (empty($dados)) {
            //não continua o processamento
            return true;
        }

        //gera o titulo do csv
        $planilha = "";
        $planilha .= utf8_decode('"Data Hierarquia";"Código Unidade";"Nome fantasia";"Setor";"Cargo";"PPRA";"PCMSO"') . "\n";

        //varre os dados das vigencias
        foreach ($dados as $key => $dado) {

            //verifica se o campo esta nulo caso esteja pula para o proximo
            if (is_null($dado['Cliente']['codigo'])) {
                continue;
            }

            //monta os dados
            $linha  = AppModel::dbDateToDate($dado['ClientesSetoresCargos']['data_inclusao']) . ';';
            $linha .= $dado['Cliente']['codigo'] . ';';
            $linha .= utf8_decode($dado['Cliente']['nome_fantasia']) . ';';
            $linha .= utf8_decode($dado[0]['Setor']) . ';';
            $linha .= utf8_decode($dado[0]['Cargo']) . ';';
            $linha .= utf8_decode($dado[0]['status_PPRA']) . ';';
            $linha .= utf8_decode($dado[0]['status_PCMSO']) . ';';
            //dados da planilha
            $planilha .= $linha . "\n";
        } //fim foreach            

        return $planilha;
    }

    private function insereAlerta($dados)
    {

        //instancia a tabela de alertas
        $this->alerta = ClassRegistry::init('Alerta');

        //array com os dados a serem inseridos na alerta
        if ($this->alerta->incluir($dados)) {
            return true;
        }

        return false;
    }

    public function dados_listagem_ppra_pcmso_pendente_sc($codigo_cliente, $tipo, $filtros)
    {
        $conditions = null;

        $conditions = $this->converteFiltrosEmConditionsPendencia($filtros, $codigo_cliente, $tipo);
        $conditions[] = '(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)';

        // popula varivel para SELECT
        $fields = array(
            " ( SELECT COUNT(*) AS T FROM funcionario_setores_cargos FuncionarioSetorCargo WHERE FuncionarioSetorCargo.data_fim IS NULL AND Cargos.codigo = FuncionarioSetorCargo.codigo_cargo and Setores.codigo = FuncionarioSetorCargo.codigo_setor and GrupoEconomicoCliente.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao ) AS funcionarios",
            "COUNT( ClientesSetoresCargos.codigo ) AS total",
            "Setores.descricao AS Setor",
            "Cargos.descricao AS Cargo",
            "GrupoEconomicoCliente.codigo_cliente",
            "Setores.codigo AS CodigoSetor",
            "Cargos.codigo AS CodigoCargo"
        );

        // popula varivel para FROM
        $joins = array(
            array(
                "table" => "clientes_setores_cargos",
                "alias" => "ClientesSetoresCargos",
                "conditions" => "GrupoEconomicoCliente.codigo_cliente = ClientesSetoresCargos.codigo_cliente_alocacao "
            ),
            array(
                "table" => "setores",
                "alias" => "Setores",
                "conditions" => "ClientesSetoresCargos.codigo_setor = Setores.codigo"
            ),
            array(
                "table" => "cargos",
                "alias" => "Cargos",
                "conditions" => "ClientesSetoresCargos.codigo_cargo = Cargos.codigo"
            )
        );

        // popula varivel para GROUP BY
        $group  = array('Cargos.codigo', 'Cargos.descricao', 'Setores.codigo', 'Setores.descricao', 'GrupoEconomicoCliente.codigo_cliente');

        switch ($tipo) {

            case 'pcmso':

                $fields[] = "(CASE WHEN AplicacaoExame.codigo_cliente_alocacao IS NOT NULL THEN 2 ELSE 1 END) AS status";

                $joins[] =  array(
                    "table" => "aplicacao_exames",
                    "alias" => "AplicacaoExame",
                    "type" => "LEFT",
                    "conditions" => "ClientesSetoresCargos.codigo_cargo = AplicacaoExame.codigo_cargo and ClientesSetoresCargos.codigo_setor = AplicacaoExame.codigo_setor and ClientesSetoresCargos.codigo_cliente_alocacao = AplicacaoExame.codigo_cliente_alocacao  ",
                );

                $group[] = 'AplicacaoExame.codigo_cliente_alocacao';

                $order = array("AplicacaoExame.codigo_cliente_alocacao", "Setores.descricao", "Cargos.descricao");

                $controller_link = "aplicacao_exames";
                break;

            case 'ppra':

                $fields[] = " (CASE WHEN GrupoExposicaoRisco.codigo_grupo_exposicao IS NOT NULL THEN 2 ELSE 1 END) AS status";

                $joins[] =  array(
                    "table" => "clientes_setores",
                    "alias" => "ClienteSetor",
                    "type" => "LEFT",
                    "conditions" => "ClientesSetoresCargos.codigo_cliente_alocacao = ClienteSetor.codigo_cliente_alocacao AND ClientesSetoresCargos.codigo_setor = ClienteSetor.codigo_setor",
                );

                $joins[] =  array(
                    "table" => "grupo_exposicao",
                    "alias" => "GrupoExposicao",
                    "type" => "LEFT",
                    "conditions" => "ClienteSetor.codigo = GrupoExposicao.codigo_cliente_setor AND GrupoExposicao.codigo_cargo = ClientesSetoresCargos.codigo_cargo",
                );

                $joins[] =  array(
                    "table" => "grupos_exposicao_risco",
                    "alias" => "GrupoExposicaoRisco",
                    "type" => "LEFT",
                    "conditions" => "GrupoExposicao.codigo = GrupoExposicaoRisco.codigo_grupo_exposicao",
                );

                $group[] = 'GrupoExposicaoRisco.codigo_grupo_exposicao';

                $order = array("GrupoExposicaoRisco.codigo_grupo_exposicao", "Setores.descricao", "Cargos.descricao");

                $controller_link = "grupos_exposicao";
                break;
        }

        // define options para ORM
        $options = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'joins' => $joins,
            'group' => $group,
            'order' => $order,
            'recursive' => -1,
        );

        /* Verifica se status = finalizado para unidade - PPRA */
        $botao_finalizar_processo = ($this->status_os_unidade($codigo_cliente, $tipo) == 3);

        return array($controller_link, $botao_finalizar_processo, $options);
    }

    private function status_os_unidade($unidade, $tipo)
    {
        $this->OrdemServico = ClassRegistry::Init('OrdemServico');
        /*Código criado automaticamente pelo ORMBuilder */

        // popula varivel para SELECT
        $fields = array(
            "OrdemServico.status_ordem_servico",
            "OrdemServico.codigo"
        );

        // Tipo de Ordem Serviço > 2340 = PCMSO > 2647 = PPRA
        // PD-154
        $Configuracao = &ClassRegistry::init('Configuracao');
        $codigo_servico_ppra = $Configuracao->getChave('CODIGO_ORDEM_SERVICO_PPRA');
        $codigo_servico_pcmso = $Configuracao->getChave('CODIGO_ORDEM_SERVICO_PCMSO');
        
        $codigo_servico = ($tipo == 'pcmso' ? $codigo_servico_pcmso : $codigo_servico_ppra);

        // popula varivel para WHERE
        $conditions = array(
            "Clientes.codigo = " . $unidade,
            "OrdemServico.codigo_cliente = GruposEconomicos.codigo_cliente",
            "OrdemServico.codigo IN ( SELECT codigo_ordem_servico FROM ordem_servico_item OrdemServicoOrdem WHERE codigo_servico = " . $codigo_servico . " )"
        );

        // popula varivel para FROM
        $joins = array(
            array(
                "table" => "grupos_economicos",
                "alias" => "GruposEconomicos",
                "conditions" => "OrdemServico.codigo_grupo_economico = GruposEconomicos.codigo"
            ),
            array(
                "table" => "grupos_economicos_clientes",
                "alias" => "GrupoEconomicoCliente",
                "conditions" => "GrupoEconomicoCliente.codigo_grupo_economico = GruposEconomicos.codigo"
            ),
            array(
                "table" => "cliente",
                "alias" => "Clientes",
                "conditions" => "Clientes.codigo = GrupoEconomicoCliente.codigo_cliente"
            ),
            array(
                "table" =>  $tipo . "_versoes",
                "alias" =>  ucfirst($tipo) . "Versoes",
                "conditions" => ucfirst($tipo) . "Versoes.codigo_cliente_alocacao = Clientes.codigo"
            )
        );

        // popula varivel para ORDER BY
        $order = " OrdemServico.codigo DESC ";

        // define options para ORM
        $options = array(
            "fields" => $fields,
            "joins" => $joins,
            "conditions" => $conditions,
            "order" => $order,
            "limit" => 1,
            "recursive" => -1
        );

        $res = $this->OrdemServico->find('first', $options);
        return $res['OrdemServico']['status_ordem_servico'];
    }

    public function listagem_ppra_pcmso_pendente_sc_terceiros($codigo_cliente, $tipo, $filtros)
    {

        $this->GrupoExposicao = &ClassRegistry::init('GrupoExposicao');
        $this->AplicacaoExame = &ClassRegistry::init('AplicacaoExame');

        $conditions = null;

        $conditions = $this->converteFiltrosEmConditionsPendencia($filtros, $codigo_cliente, $tipo);

        switch ($tipo) {
            case 'pcmso':
                $options_arrays = $this->AplicacaoExame->monta_array_query_pcmso();
                $controller_link = "aplicacao_exames";
                break;

            case 'ppra':
                $options_arrays = $this->GrupoExposicao->monta_array_query_ppra();
                $controller_link = "grupos_exposicao";
                break;
        }

        // define options para ORM
        $options = array(
            'fields'    => $options_arrays['fields'],
            'conditions' => $conditions,
            'limit'     => 50,
            'joins'     => $options_arrays['joins'],
            'group'     => $options_arrays['group'],
            'order'     => $options_arrays['order'],
            'recursive' => -1,
        );

        /* Verifica se status = finalizado para unidade - PPRA */
        $botao_finalizar_processo = ($this->status_os_unidade($codigo_cliente, $tipo) == 3);

        return array(
            $controller_link,
            $botao_finalizar_processo,
            $options
        );
    }

    public function ConditionsDocsPrestadores($data)
    {

        unset($data['codigo_fornecedorCodigo']);

        $conditions = array_fill(0, 2, null);

        if (!empty($data['estado'])) {
            $conditions['estado'] = $data['estado'];
        }

        if (!empty($data['cidade'])) {
            $conditions['cidade'] = $data['cidade'];
        }

        if (!empty($data['codigo_fornecedor'])) {
            $conditions['codigo_fornecedor'] = $data['codigo_fornecedor'];
        }

        if (!empty($data['documento'])) {
            $conditions['documento'] = $data['documento'];
        }

        if (!empty($data['nome_fantasia'])) {
            $conditions['nome_fantasia'] = '%' . $data['nome_fantasia'] . '%';
        }

        if (!empty($data['razao_social'])) {
            $conditions['razao_social_fornecedor'] = '%' . $data['razao_social'] . '%';
        }

        //trazer os vigentes como default
        if (!isset($data['situacao']))
            $conditions['situacao'] = array('VI');

        // debug($data);


        if (isset($data['situacao']) && is_array($data['situacao']) && count($data['situacao']) > 0) {

            if (in_array("AV", $data['situacao'])) {
                //verifica se tem valor para setar o valor de hoje
                if (empty($data['data_inicio'])) {
                    $data['data_inicio'] = date('d/m/Y');
                }
                //verifica se tem o valor da data final
                if (empty($data['data_fim'])) {
                    $data['data_fim'] = date('d/m/Y', strtotime('+ 30 days'));
                }

                //gera a condição
                $conditions[0] = array(
                    "FornecedorDocumento.data_validade >= '" . AppModel::dateToDbDate2($data['data_inicio']) . "'",
                    "FornecedorDocumento.data_validade <= '" . AppModel::dateToDbDate2($data['data_fim']) . "'"
                );
            } else {

                if (in_array("VI", $data['situacao'])) {
                    //verifica se tem valor para setar o valor de hoje
                    if (empty($data['data_inicio'])) {
                        $data['data_inicio'] = date('d/m/Y');
                    }

                    $conditions[2] = "FornecedorDocumento.data_validade >= '" . AppModel::dateToDbDate2($data['data_inicio']) . "'";

                    // $conditions[2] = array(
                    //     "FornecedorDocumento.data_validade >= '".AppModel::dateToDbDate2($data['data_inicio'])."'",
                    //     "FornecedorDocumento.data_validade <= '".AppModel::dateToDbDate2($data['data_inicio'])."'"
                    // );

                    // if(in_array('AV', $data['situacao'])){
                    //     $conditions[2] = "FornecedorDocumento.data_validade > '".AppModel::dateToDbDate2($data['data_fim'])."'";
                    // }
                    // else{
                    //     
                    // }
                }
            }

            if (in_array("V", $data['situacao'])) {
                $conditions[1] = "FornecedorDocumento.data_validade < '" . AppModel::dateToDbDate2($data['data_inicio']) . "'";
            }


            // if(in_array("P", $data['situacao'])){
            // 	// $conditions[2] = 
            // }

        }

        // debug($conditions);

        return $conditions;
    }

    public function get_docs_prestadores($type, $options)
    {
        //carrega models
        $this->FornecedorDocumento = ClassRegistry::init('FornecedorDocumento');
        $this->TipoDocumento = ClassRegistry::init('TipoDocumento');
        $this->FornecedorEndereco = ClassRegistry::init('FornecedorEndereco');
        $this->Fornecedor = ClassRegistry::init('Fornecedor');
        $this->EnderecoEstado = ClassRegistry::init('EnderecoEstado');
        $this->EnderecoCidade = ClassRegistry::init('EnderecoCidade');

        $conditions = array(
            'TipoDocumento.status' => 1,
            'FornecedorEndereco.codigo_tipo_contato' => 2,
            // 'FornecedorDocumento.data_validade IS NOT NULL',
            // 'FornecedorDocumento.data_validade < ' => date('Y-m-d')
        );

        // debug($options);exit;

        //codigo do fornecedor
        if (isset($options['conditions']['codigo_fornecedor']) && !empty($options['conditions']['codigo_fornecedor'])) {
            $conditions[] = array('Fornecedor.codigo' => $options['conditions']['codigo_fornecedor']);
        }

        if (isset($options['conditions']['estado']) && !empty($options['conditions']['estado'])) {
            //busca estado
            $busca_estado = $this->EnderecoEstado->find('first', array('conditions' => array('codigo' => $options['conditions']['estado'])));
            //abrevia
            $estado = $busca_estado['EnderecoEstado']['abreviacao'];
            $conditions[] = array('FornecedorEndereco.estado_abreviacao like ' => '%' . $estado . '%');
        }

        if (isset($options['conditions']['cidade']) && !empty($options['conditions']['cidade'])) {
            //busca estado
            $busca_cidade = $this->EnderecoCidade->find('first', array('conditions' => array('EnderecoCidade.codigo' => $options['conditions']['cidade'])));
            //abrevia
            $cidade = $busca_cidade['EnderecoCidade']['descricao'];
            $conditions[] = array('FornecedorEndereco.cidade like ' => '%' . $cidade . '%');
        }

        if (isset($options['conditions']['documento']) && !empty($options['conditions']['documento'])) {
            $conditions[] = array('TipoDocumento.codigo' => $options['conditions']['documento']);
        }

        if (isset($options['conditions']['nome_fantasia']) && !empty($options['conditions']['nome_fantasia'])) {
            $conditions[] = array('Fornecedor.nome like ' => $options['conditions']['nome_fantasia']);
        }

        if (isset($options['conditions']['razao_social_fornecedor']) && !empty($options['conditions']['razao_social_fornecedor'])) {
            $conditions[] = array('Fornecedor.razao_social like ' => $options['conditions']['razao_social_fornecedor']);
        }


        $joins  = array(
            array(
                'table' => $this->FornecedorDocumento->databaseTable . '.' . $this->FornecedorDocumento->tableSchema . '.' . $this->FornecedorDocumento->useTable,
                'alias' => 'FornecedorDocumento',
                'conditions' => 'Fornecedor.codigo = FornecedorDocumento.codigo_fornecedor',
            ),
            array(
                'table' => $this->TipoDocumento->databaseTable . '.' . $this->TipoDocumento->tableSchema . '.' . $this->TipoDocumento->useTable,
                'alias' => 'TipoDocumento',
                'conditions' => 'TipoDocumento.codigo = FornecedorDocumento.codigo_tipo_documento',
            ),
            array(
                'table' => $this->FornecedorEndereco->databaseTable . '.' . $this->FornecedorEndereco->tableSchema . '.' . $this->FornecedorEndereco->useTable,
                'alias' => 'FornecedorEndereco',
                'conditions' => 'FornecedorEndereco.codigo_fornecedor = Fornecedor.codigo',
            )
        );

        $fields = array(
            'Fornecedor.codigo',
            'Fornecedor.razao_social',
            'Fornecedor.nome',
            'Rhhealth.publico.ufn_formata_cnpj(Fornecedor.codigo_documento) as cnpj',
            'FornecedorEndereco.cidade',
            'FornecedorEndereco.estado_descricao',
            'TipoDocumento.codigo',
            'TipoDocumento.descricao as Fornecedor__documento',
            'FornecedorDocumento.data_validade as Fornecedor__data_validade',
            '(select top 1 descricao  from fornecedores_contato where codigo_fornecedor = Fornecedor.codigo and codigo_tipo_retorno = 1) as telefone , (select top 1 descricao  from fornecedores_contato where codigo_fornecedor = Fornecedor.codigo and codigo_tipo_retorno = 2) as email',
        );

        $status = array(
            'AV' => 'CASE
                WHEN 
                    FornecedorDocumento.data_validade > \'' . (!empty($options['filtros']['data_inicio']) ? AppModel::dateToDbDate2($options['filtros']['data_inicio']) : null) . '\'
                    AND
                    FornecedorDocumento.data_validade <= \'' . (!empty($options['filtros']['data_fim']) ? AppModel::dateToDbDate2($options['filtros']['data_fim']) : null) . '\'
                    THEN \'A VENCER\'
                WHEN 
                    FornecedorDocumento.data_validade >= \'' . (!empty($options['filtros']['data_inicio']) ? AppModel::dateToDbDate2($options['filtros']['data_inicio']) : null) . '\'
                    AND
                    FornecedorDocumento.data_validade <= \'' . (!empty($options['filtros']['data_inicio']) ? AppModel::dateToDbDate2($options['filtros']['data_inicio']) : null) . '\'
                    THEN \'VIGENTE\'
                ELSE \'DESCONHECIDO\'
                END AS status',
            'V' => 'CASE
            	WHEN
                	FornecedorDocumento.data_validade < \'' . (!empty($options['filtros']['data_inicio']) ? AppModel::dateToDbDate2($options['filtros']['data_inicio']) : null) . '\' THEN \'VENCIDO\'
                ELSE \'DESCONHECIDO\'
                END AS status',
            'VI' => 'CASE
            	WHEN
                	FornecedorDocumento.data_validade >= \'' . (!empty($options['filtros']['data_inicio']) ? AppModel::dateToDbDate2($options['filtros']['data_inicio']) : null) . '\'
    				THEN \'VIGENTE\'            
	            ELSE \'DESCONHECIDO\'
                END AS status',
            'P' => "'PENDENTE' AS status",
        );

        $order = array('FornecedorDocumento.data_validade DESC');


        $fetch_conditions = array();
        $status_conditions = array();
        if (!empty($options['conditions'][0]) && !is_null($options['conditions'][0])) {
            $fetch_conditions[] = $options['conditions'][0];
            $status_conditions[] = "AV";
            unset($options['conditions'][0]);
        }
        if (!empty($options['conditions'][1]) && !is_null($options['conditions'][1])) {
            $fetch_conditions[] = $options['conditions'][1];
            $status_conditions[] = "V";
            unset($options['conditions'][1]);
        }
        if (!empty($options['conditions'][2]) && !is_null($options['conditions'][2])) {
            $fetch_conditions[] = $options['conditions'][2];
            $status_conditions[] = "VI";
            unset($options['conditions'][2]);
        }

        //montando a query para a consulta
        $query = array();
        $interator = new CachingIterator(new ArrayIterator($fetch_conditions));

        foreach ($interator as $k => $item) {

            $conditions[2][] = $item;

            $fields[10] = $status[$status_conditions[$k]];

            $query[] = $this->Fornecedor->find('sql', compact('fields', 'joins', 'conditions'));

            end($conditions[2]);
            unset($conditions[2][key($conditions[2])]);

            if ($interator->hasNext())
                $query[] = "UNION ALL";
        }


        //verifica se esta querendo que traga os pendentes
        if (isset($options['filtros']['situacao'])) {
            //verifica se esta filtrando por pendente
            if (in_array("P", $options['filtros']['situacao'])) {

                $fieldsPendente = $fields;

                //seta os fields
                $fieldsPendente[8] = "NULL as Fornecedor__data_validade";
                $fieldsPendente[10] = $status['P'];

                $joinsPendente = array(
                    array(
                        'table' => $this->FornecedorEndereco->databaseTable . '.' . $this->FornecedorEndereco->tableSchema . '.' . $this->FornecedorEndereco->useTable,
                        'alias' => 'FornecedorEndereco',
                        'conditions' => 'FornecedorEndereco.codigo_fornecedor = Fornecedor.codigo',
                    ),
                    array(
                        'table' => $this->TipoDocumento->databaseTable . '.' . $this->TipoDocumento->tableSchema . '.' . $this->TipoDocumento->useTable,
                        'type' => 'LEFT',
                        'alias' => 'TipoDocumento',
                        'conditions' => 'TipoDocumento.codigo NOT IN (SELECT codigo_tipo_documento FROM [RHHealth].[dbo].fornecedores_documentos WHERE codigo_fornecedor = Fornecedor.codigo)',
                    ),
                );

                //    		if(isset($conditions[2])) {
                // unset($conditions[2]);
                //    		}

                //monta a query
                if (!empty($query)) {
                    $query[] = "UNION ALL";
                }
                $query[] = $this->Fornecedor->find('sql', array(
                    'fields' => $fieldsPendente,
                    'joins' => $joinsPendente,
                    'conditions' => $conditions,
                    'order' => array('Fornecedor.codigo')
                ));
            }
        } //fim situacao pendente

        // debug($conditions);debug($query);exit;

        $sql = join(' ', $query);

        // debug($sql);

        if ($type == 'sql') {
            $results = $sql;
        } else {
            $results = $this->query($sql);
        }

        // $this->log($sql,'debug');
        // debug($sql);
        // debug($results);

        return $results;
    }

    public function get_produtos_servicos($conditions, $list = null)
    {

        $this->Fornecedor = &ClassRegistry::init('Fornecedor');
        $this->ListaDePrecoProduto = &ClassRegistry::init('ListaDePrecoProduto');
        $this->ListaDePrecoProdutoServico = &ClassRegistry::init('ListaDePrecoProdutoServico');
        $this->Produto = &ClassRegistry::init('Produto');
        $this->Servico = &ClassRegistry::init('Servico');
        $this->FornecedorEndereco = &ClassRegistry::init('FornecedorEndereco');
        $this->ListaDePreco = &ClassRegistry::init('ListaDePreco');

        //joins
        $joins  = array(
            array(
                'table' => $this->Fornecedor->databaseTable . '.' . $this->Fornecedor->tableSchema . '.' . $this->Fornecedor->useTable,
                'alias' => 'Fornecedor',
                'type' => 'LEFT',
                'conditions' => 'Fornecedor.codigo = ListaDePreco.codigo_fornecedor',
            ),
            array(
                'table' => $this->ListaDePrecoProduto->databaseTable . '.' . $this->ListaDePrecoProduto->tableSchema . '.' . $this->ListaDePrecoProduto->useTable,
                'alias' => 'ListaDePrecoProduto',
                'type' => 'LEFT',
                'conditions' => 'ListaDePrecoProduto.codigo_lista_de_preco = ListaDePreco.codigo',
            ),
            array(
                'table' => $this->ListaDePrecoProdutoServico->databaseTable . '.' . $this->ListaDePrecoProdutoServico->tableSchema . '.' . $this->ListaDePrecoProdutoServico->useTable,
                'alias' => 'ListaDePrecoProdutoServico',
                'type' => 'LEFT',
                'conditions' => 'ListaDePrecoProdutoServico.codigo_lista_de_preco_produto = ListaDePrecoProduto.codigo',
            ),
            array(
                'table' => $this->Produto->databaseTable . '.' . $this->Produto->tableSchema . '.' . $this->Produto->useTable,
                'alias' => 'Produto',
                'type' => 'LEFT',
                'conditions' => 'Produto.codigo = ListaDePrecoProduto.codigo_produto',
            ),
            array(
                'table' => $this->Servico->databaseTable . '.' . $this->Servico->tableSchema . '.' . $this->Servico->useTable,
                'alias' => 'Servico',
                'type' => 'LEFT',
                'conditions' => 'Servico.codigo = ListaDePrecoProdutoServico.codigo_servico',
            ),
            array(
                'table' => $this->FornecedorEndereco->databaseTable . '.' . $this->FornecedorEndereco->tableSchema . '.' . $this->FornecedorEndereco->useTable,
                'alias' => 'FornecedorEndereco',
                'type' => 'INNER',
                'conditions' => 'FornecedorEndereco.codigo_fornecedor = Fornecedor.codigo',
            )
        );

        /*


		$this->ListaDePreco->virtualFields['valor_medio_brasil'] = 'SELECT  AVG(valor) AS [valor_medio_brasil]
		FROM '.$this->ListaDePrecoProdutoServico->databaseTable.'.'.$this->ListaDePrecoProdutoServico->tableSchema.'.'.$this->ListaDePrecoProdutoServico->useTable.' AS [Listas_De_Preco_Produto_Servico_subquery]
		LEFT JOIN '.$this->ListaDePrecoProduto->databaseTable.'.'.$this->ListaDePrecoProduto->tableSchema.'.'.$this->ListaDePrecoProduto->useTable.' AS [Listas_De_Preco_Produto_subquery] ON [Listas_De_Preco_Produto_Servico_subquery].[codigo_lista_de_preco_produto] = [Listas_De_Preco_Produto_subquery].[codigo]
		LEFT JOIN '.$this->ListaDePreco->databaseTable.'.'.$this->ListaDePreco->tableSchema.'.'.$this->ListaDePreco->useTable.' AS [Listas_De_Preco_subquery] ON [Listas_De_Preco_subquery].[codigo] = [Listas_De_Preco_Produto_subquery].[codigo_lista_de_preco] 
		LEFT JOIN '.$this->Fornecedor->databaseTable.'.'.$this->Fornecedor->tableSchema.'.'.$this->Fornecedor->useTable.'  AS [Fornecedores_subquery] ON [Fornecedores_subquery].[codigo] = [Listas_De_Preco_subquery].[codigo_fornecedor]
		LEFT JOIN '.$this->FornecedorEndereco->databaseTable.'.'.$this->FornecedorEndereco->tableSchema.'.'.$this->FornecedorEndereco->useTable.' AS [Fornecedores_Endereco_subquery] ON  [Fornecedores_subquery].[codigo] = [Fornecedores_Endereco_subquery].[codigo_fornecedor]
		WHERE [Listas_De_Preco_Produto_Servico_subquery].[codigo_servico] = [ListaDePrecoProdutoServico].[codigo_servico] ';

		$this->ListaDePreco->virtualFields['valor_medio_uf'] = 'SELECT  AVG(valor) AS [valor_medio_uf]
		FROM '.$this->ListaDePrecoProdutoServico->databaseTable.'.'.$this->ListaDePrecoProdutoServico->tableSchema.'.'.$this->ListaDePrecoProdutoServico->useTable.' AS [Listas_De_Preco_Produto_Servico_subquery]
		LEFT JOIN '.$this->ListaDePrecoProduto->databaseTable.'.'.$this->ListaDePrecoProduto->tableSchema.'.'.$this->ListaDePrecoProduto->useTable.' AS [Listas_De_Preco_Produto_subquery] ON [Listas_De_Preco_Produto_Servico_subquery].[codigo_lista_de_preco_produto] = [Listas_De_Preco_Produto_subquery].[codigo]
		LEFT JOIN '.$this->ListaDePreco->databaseTable.'.'.$this->ListaDePreco->tableSchema.'.'.$this->ListaDePreco->useTable.' AS [Listas_De_Preco_subquery] ON [Listas_De_Preco_subquery].[codigo] = [Listas_De_Preco_Produto_subquery].[codigo_lista_de_preco]		
		LEFT JOIN '.$this->Fornecedor->databaseTable.'.'.$this->Fornecedor->tableSchema.'.'.$this->Fornecedor->useTable.'  AS [Fornecedores_subquery] ON [Fornecedores_subquery].[codigo] = [Listas_De_Preco_subquery].[codigo_fornecedor]
		LEFT JOIN '.$this->FornecedorEndereco->databaseTable.'.'.$this->FornecedorEndereco->tableSchema.'.'.$this->FornecedorEndereco->useTable.' AS [Fornecedores_Endereco_subquery] ON  [Fornecedores_subquery].[codigo] = [Fornecedores_Endereco_subquery].[codigo_fornecedor] AND [Fornecedores_Endereco_subquery].[estado_abreviacao]= [FornecedorEndereco].[estado_abreviacao] 
		WHERE [Listas_De_Preco_Produto_Servico_subquery].[codigo_servico] = [ListaDePrecoProdutoServico].[codigo_servico]';

		$this->ListaDePreco->virtualFields['valor_minimo_uf'] = 'SELECT  MIN(valor) AS [valor_minimo_uf]
		FROM '.$this->ListaDePrecoProdutoServico->databaseTable.'.'.$this->ListaDePrecoProdutoServico->tableSchema.'.'.$this->ListaDePrecoProdutoServico->useTable.' AS [Listas_De_Preco_Produto_Servico_subquery]
		LEFT JOIN '.$this->ListaDePrecoProduto->databaseTable.'.'.$this->ListaDePrecoProduto->tableSchema.'.'.$this->ListaDePrecoProduto->useTable.' AS [Listas_De_Preco_Produto_subquery] ON [Listas_De_Preco_Produto_Servico_subquery].[codigo_lista_de_preco_produto] = [Listas_De_Preco_Produto_subquery].[codigo]
		LEFT JOIN '.$this->ListaDePreco->databaseTable.'.'.$this->ListaDePreco->tableSchema.'.'.$this->ListaDePreco->useTable.' AS [Listas_De_Preco_subquery] ON [Listas_De_Preco_subquery].[codigo] = [Listas_De_Preco_Produto_subquery].[codigo_lista_de_preco] 
		LEFT JOIN '.$this->Fornecedor->databaseTable.'.'.$this->Fornecedor->tableSchema.'.'.$this->Fornecedor->useTable.'  AS [Fornecedores_subquery] ON [Fornecedores_subquery].[codigo] = [Listas_De_Preco_subquery].[codigo_fornecedor]
		LEFT JOIN '.$this->FornecedorEndereco->databaseTable.'.'.$this->FornecedorEndereco->tableSchema.'.'.$this->FornecedorEndereco->useTable.' AS [Fornecedores_Endereco_subquery] ON  [Fornecedores_subquery].[codigo] = [Fornecedores_Endereco_subquery].[codigo_fornecedor] AND [Fornecedores_Endereco_subquery].[estado_abreviacao]= [FornecedorEndereco].[estado_abreviacao] 
		WHERE [Listas_De_Preco_Produto_Servico_subquery].[codigo_servico] = [ListaDePrecoProdutoServico].[codigo_servico]';

		$this->ListaDePreco->virtualFields['valor_maximo_uf'] = 'SELECT  MAX(valor) AS [valor_maximo_uf]
		FROM '.$this->ListaDePrecoProdutoServico->databaseTable.'.'.$this->ListaDePrecoProdutoServico->tableSchema.'.'.$this->ListaDePrecoProdutoServico->useTable.' AS [Listas_De_Preco_Produto_Servico_subquery]
		LEFT JOIN '.$this->ListaDePrecoProduto->databaseTable.'.'.$this->ListaDePrecoProduto->tableSchema.'.'.$this->ListaDePrecoProduto->useTable.' AS [Listas_De_Preco_Produto_subquery] ON [Listas_De_Preco_Produto_Servico_subquery].[codigo_lista_de_preco_produto] = [Listas_De_Preco_Produto_subquery].[codigo]
		LEFT JOIN '.$this->ListaDePreco->databaseTable.'.'.$this->ListaDePreco->tableSchema.'.'.$this->ListaDePreco->useTable.' AS [Listas_De_Preco_subquery] ON [Listas_De_Preco_subquery].[codigo] = [Listas_De_Preco_Produto_subquery].[codigo_lista_de_preco]		
		LEFT JOIN '.$this->Fornecedor->databaseTable.'.'.$this->Fornecedor->tableSchema.'.'.$this->Fornecedor->useTable.'  AS [Fornecedores_subquery] ON [Fornecedores_subquery].[codigo] = [Listas_De_Preco_subquery].[codigo_fornecedor]
		LEFT JOIN '.$this->FornecedorEndereco->databaseTable.'.'.$this->FornecedorEndereco->tableSchema.'.'.$this->FornecedorEndereco->useTable.' AS [Fornecedores_Endereco_subquery] ON  [Fornecedores_subquery].[codigo] = [Fornecedores_Endereco_subquery].[codigo_fornecedor] AND [Fornecedores_Endereco_subquery].[estado_abreviacao]= [FornecedorEndereco].[estado_abreviacao] 
		WHERE [Listas_De_Preco_Produto_Servico_subquery].[codigo_servico] = [ListaDePrecoProdutoServico].[codigo_servico]';
		*/

        $this->ListaDePreco->virtualFields['valor_medio_cidade'] = 'SELECT  AVG(valor) AS [valor_medio_cidade]
		FROM ' . $this->ListaDePrecoProdutoServico->databaseTable . '.' . $this->ListaDePrecoProdutoServico->tableSchema . '.' . $this->ListaDePrecoProdutoServico->useTable . ' AS [Listas_De_Preco_Produto_Servico_subquery]
		LEFT JOIN ' . $this->ListaDePrecoProduto->databaseTable . '.' . $this->ListaDePrecoProduto->tableSchema . '.' . $this->ListaDePrecoProduto->useTable . ' AS [Listas_De_Preco_Produto_subquery] ON [Listas_De_Preco_Produto_Servico_subquery].[codigo_lista_de_preco_produto] = [Listas_De_Preco_Produto_subquery].[codigo]
		LEFT JOIN ' . $this->ListaDePreco->databaseTable . '.' . $this->ListaDePreco->tableSchema . '.' . $this->ListaDePreco->useTable . ' AS [Listas_De_Preco_subquery] ON [Listas_De_Preco_subquery].[codigo] = [Listas_De_Preco_Produto_subquery].[codigo_lista_de_preco]		
		LEFT JOIN ' . $this->Fornecedor->databaseTable . '.' . $this->Fornecedor->tableSchema . '.' . $this->Fornecedor->useTable . '  AS [Fornecedores_subquery] ON [Fornecedores_subquery].[codigo] = [Listas_De_Preco_subquery].[codigo_fornecedor]
		LEFT JOIN ' . $this->FornecedorEndereco->databaseTable . '.' . $this->FornecedorEndereco->tableSchema . '.' . $this->FornecedorEndereco->useTable . ' AS [Fornecedores_Endereco_subquery] ON  [Fornecedores_subquery].[codigo] = [Fornecedores_Endereco_subquery].[codigo_fornecedor] AND [Fornecedores_Endereco_subquery].[cidade] = [FornecedorEndereco].[cidade]
		WHERE [Listas_De_Preco_Produto_Servico_subquery].[codigo_servico] = [ListaDePrecoProdutoServico].[codigo_servico]';

        /*
		
 
		$this->ListaDePreco->virtualFields['valor_minimo'] = 'SELECT  MIN(valor) AS [valor_minimo]
		FROM '.$this->ListaDePrecoProdutoServico->databaseTable.'.'.$this->ListaDePrecoProdutoServico->tableSchema.'.'.$this->ListaDePrecoProdutoServico->useTable.' AS [Listas_De_Preco_Produto_Servico_subquery]
		LEFT JOIN '.$this->ListaDePrecoProduto->databaseTable.'.'.$this->ListaDePrecoProduto->tableSchema.'.'.$this->ListaDePrecoProduto->useTable.' AS [Listas_De_Preco_Produto_subquery] ON [Listas_De_Preco_Produto_Servico_subquery].[codigo_lista_de_preco_produto] = [Listas_De_Preco_Produto_subquery].[codigo]
		LEFT JOIN '.$this->ListaDePreco->databaseTable.'.'.$this->ListaDePreco->tableSchema.'.'.$this->ListaDePreco->useTable.' AS [Listas_De_Preco_subquery] ON [Listas_De_Preco_subquery].[codigo] = [Listas_De_Preco_Produto_subquery].[codigo_lista_de_preco] 
		LEFT JOIN '.$this->Fornecedor->databaseTable.'.'.$this->Fornecedor->tableSchema.'.'.$this->Fornecedor->useTable.'  AS [Fornecedores_subquery] ON [Fornecedores_subquery].[codigo] = [Listas_De_Preco_subquery].[codigo_fornecedor]
		LEFT JOIN '.$this->FornecedorEndereco->databaseTable.'.'.$this->FornecedorEndereco->tableSchema.'.'.$this->FornecedorEndereco->useTable.' AS [Fornecedores_Endereco_subquery] ON  [Fornecedores_subquery].[codigo] = [Fornecedores_Endereco_subquery].[codigo_fornecedor]
		WHERE [Listas_De_Preco_Produto_Servico_subquery].[codigo_servico] = [ListaDePrecoProdutoServico].[codigo_servico] ';
		*/



        $fields = array(
            'ListaDePreco.codigo',
            'Fornecedor.codigo',
            'Fornecedor.ativo',
            'Fornecedor.razao_social as ListaDePreco__razao_social',
            'FornecedorEndereco.estado_descricao as ListaDePreco__estado',
            'UPPER(FornecedorEndereco.cidade) as ListaDePreco__cidade',
            'Produto.codigo',
            'Produto.descricao as ListaDePreco__produto',
            'Servico.codigo',
            'Servico.descricao as ListaDePreco__servico',
            'ListaDePrecoProdutoServico.valor',
            'ListaDePrecoProdutoServico.codigo_lista_de_preco_produto',
            // 'ListaDePreco.valor_medio_brasil',
            // 'ListaDePreco.valor_medio_uf',
            // 'ListaDePreco.valor_minimo_uf',
            // 'ListaDePreco.valor_maximo_uf',
            'ListaDePreco.valor_medio_cidade'
        );

        $this->ListaDePreco->virtualFields['estado'] = 'ListaDePreco__estado';
        $this->ListaDePreco->virtualFields['cidade'] = 'ListaDePreco__cidade';
        $this->ListaDePreco->virtualFields['razao_social'] = 'ListaDePreco__razao_social';
        $this->ListaDePreco->virtualFields['produto'] = 'ListaDePreco__produto';
        $this->ListaDePreco->virtualFields['servico'] = 'ListaDePreco__servico';

        $order = array('Produto.descricao, Servico.descricao');
        // $conditions[] = "Fornecedor.prestador_particular <> 1 AND Fornecedor.ambulatorio <> 1";

        if ($list != null) {
            $dados = array(
                'conditions' => $conditions,
                'joins' => $joins,
                'fields' => $fields,
                // 'group' => $group,
                'order' => $order
            );
        } else {
            //para futuras consultas que precisarao desse método
            $dados = $this->ListaDePreco->find('all', array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields, 'order' => $order, 'recursive' => -1));
        }

        return $dados;
    }

    public function GetPrestadoresTestes()
    {
        $this->Configuracao = &ClassRegistry::init('Configuracao');

        $configForTeste = $this->Configuracao->getChave('CODIGO_PRESTADOR_TESTE');

        $codigo_prestadores_testes = array();

        if (!empty($configForTeste)) {
            $codigo_prestadores_testes = $configForTeste;
        }

        return $codigo_prestadores_testes;
    }

    public function CalculoMédiaBrasil($codigos_servicos)
    {
        //instancia a model
        $this->ListaDePrecoProdutoServico = &ClassRegistry::init('ListaDePrecoProdutoServico');

        //query
        $sql = "
	    	SELECT
                AVG(cast(NULLIF(valor, 0) AS MONEY)) AS [valor_medio_brasil]
		FROM [RHHealth].[dbo].listas_de_preco_produto_servico AS [Listas_De_Preco_Produto_Servico_subquery]
			LEFT JOIN [RHHealth].[dbo].listas_de_preco_produto AS [Listas_De_Preco_Produto_subquery] ON [Listas_De_Preco_Produto_Servico_subquery].[codigo_lista_de_preco_produto] = [Listas_De_Preco_Produto_subquery].[codigo]
			LEFT JOIN [RHHealth].[dbo].listas_de_preco AS [Listas_De_Preco_subquery] ON [Listas_De_Preco_subquery].[codigo] = [Listas_De_Preco_Produto_subquery].[codigo_lista_de_preco]
			LEFT JOIN [RHHealth].[dbo].fornecedores AS [Fornecedores_subquery] ON [Fornecedores_subquery].[codigo] = [Listas_De_Preco_subquery].[codigo_fornecedor]
			INNER JOIN [RHHealth].[dbo].fornecedores_endereco AS [Fornecedores_Endereco_subquery] ON [Fornecedores_subquery].[codigo] = [Fornecedores_Endereco_subquery].[codigo_fornecedor]		
		WHERE [Listas_De_Preco_Produto_Servico_subquery].[codigo_servico] IN (" . $codigos_servicos['Codigos_servicos'] . ")
        AND NOT (valor = 0 OR valor = '0.00')
    	";
        //consulta
        $consulta_media = $this->ListaDePrecoProdutoServico->query($sql);

        if ($consulta_media) {
            foreach ($consulta_media as $key => $value) {
                $dado_media = $value[0];
            }
        }
        return $dado_media;
    }

    public function CalculosUF($codigos_servicos, $estado)
    {
        if (!empty($estado)) {
            $estate = "'" . $estado . "'";
        }
        //instancia a model
        $this->ListaDePrecoProdutoServico = &ClassRegistry::init('ListaDePrecoProdutoServico');
        $this->Configuracao = &ClassRegistry::init('Configuracao');

        $sql = "
	    	SELECT
                AVG(cast(NULLIF(valor, 0) AS MONEY)) AS [valor_medio_uf],
				min(valor) AS [valor_min_uf],
				max(valor) AS [valor_max_uf]
		FROM [RHHealth].[dbo].listas_de_preco_produto_servico AS [Listas_De_Preco_Produto_Servico_subquery]
			LEFT JOIN [RHHealth].[dbo].listas_de_preco_produto AS [Listas_De_Preco_Produto_subquery] ON [Listas_De_Preco_Produto_Servico_subquery].[codigo_lista_de_preco_produto] = [Listas_De_Preco_Produto_subquery].[codigo]
			LEFT JOIN [RHHealth].[dbo].listas_de_preco AS [Listas_De_Preco_subquery] ON [Listas_De_Preco_subquery].[codigo] = [Listas_De_Preco_Produto_subquery].[codigo_lista_de_preco]
			LEFT JOIN [RHHealth].[dbo].fornecedores AS [Fornecedores_subquery] ON [Fornecedores_subquery].[codigo] = [Listas_De_Preco_subquery].[codigo_fornecedor]
			 INNER JOIN [RHHealth].[dbo].fornecedores_endereco AS [Fornecedores_Endereco_subquery] ON [Fornecedores_subquery].[codigo] = [Fornecedores_Endereco_subquery].[codigo_fornecedor]
		WHERE [Listas_De_Preco_Produto_Servico_subquery].[codigo_servico] IN (" . $codigos_servicos['Codigos_servicos'] . ")
		AND [Fornecedores_Endereco_subquery].[estado_descricao] = " . $estate . "
        AND NOT (valor = 0 OR valor = '0.00')		
    	";

        $configForTeste = $this->Configuracao->getChave('CODIGO_PRESTADOR_TESTE');

        $codigo_prestadores_testes = array();

        if (!empty($configForTeste)) {
            $codigo_prestadores_testes = $configForTeste;
        }

        if (!empty($codigo_prestadores_testes)) {
            $sql .= ' AND [Fornecedores_subquery].[codigo] NOT IN (' . $codigo_prestadores_testes . ')';
        }

        //consulta
        $consulta_uf = $this->ListaDePrecoProdutoServico->query($sql);

        if ($consulta_uf) {
            foreach ($consulta_uf as $key => $value) {
                $dados_uf = $value[0];
            }
        }

        // debug($dados_uf); die;
        return $dados_uf;
    }

    public function MediaCidade($codigos_servicos, $cidade, $list_cods_lpp)
    {
        if (!empty($cidade)) {
            /// tratamento feito para o cdct-265
            //colocado para a iso por causa do banco    		
            $city = "'%" . mb_strtolower($cidade, mb_internal_encoding()) . "%' COLLATE Latin1_General_CI_AI";
            $cityOr = "'%" . mb_strtoupper($cidade, mb_internal_encoding()) . "%' COLLATE Latin1_General_CI_AI";
        }

        //instancia a model
        $this->ListaDePrecoProdutoServico = &ClassRegistry::init('ListaDePrecoProdutoServico');
        $this->Fornecedor = &ClassRegistry::init('Fornecedor');
        $this->Configuracao = &ClassRegistry::init('Configuracao');

        //query        
        $sql = "
	    	SELECT
            AVG(cast(NULLIF(valor, 0) AS MONEY)) AS [valor_media_cidade]            
		FROM [RHHealth].[dbo].listas_de_preco_produto_servico AS [Listas_De_Preco_Produto_Servico_subquery]
			LEFT JOIN [RHHealth].[dbo].listas_de_preco_produto AS [Listas_De_Preco_Produto_subquery] ON [Listas_De_Preco_Produto_Servico_subquery].[codigo_lista_de_preco_produto] = [Listas_De_Preco_Produto_subquery].[codigo]
			LEFT JOIN [RHHealth].[dbo].listas_de_preco AS [Listas_De_Preco_subquery] ON [Listas_De_Preco_subquery].[codigo] = [Listas_De_Preco_Produto_subquery].[codigo_lista_de_preco]
			LEFT JOIN [RHHealth].[dbo].fornecedores AS [Fornecedores_subquery] ON [Fornecedores_subquery].[codigo] = [Listas_De_Preco_subquery].[codigo_fornecedor]
			 INNER JOIN [RHHealth].[dbo].fornecedores_endereco AS [Fornecedores_Endereco_subquery] ON [Fornecedores_subquery].[codigo] = [Fornecedores_Endereco_subquery].[codigo_fornecedor]
		WHERE [Listas_De_Preco_Produto_Servico_subquery].[codigo_servico] IN (" . $codigos_servicos['Codigos_servicos'] . ")
			AND ([Fornecedores_Endereco_subquery].[cidade] LIKE " . $city . " OR [Fornecedores_Endereco_subquery].[cidade] LIKE " . $cityOr . ")
		AND [Listas_De_Preco_Produto_Servico_subquery].[codigo_lista_de_preco_produto] IN (" . $list_cods_lpp['Codigos_lpp'] . ")
        AND NOT (valor = 0 OR valor = '0.00')		
    	";

        $configForTeste = $this->Configuracao->getChave('CODIGO_PRESTADOR_TESTE');

        $codigo_prestadores_testes = array();

        if (!empty($configForTeste)) {
            $codigo_prestadores_testes = $configForTeste;
        }

        if (!empty($codigo_prestadores_testes)) {
            $sql .= ' AND [Fornecedores_subquery].[codigo] NOT IN (' . $codigo_prestadores_testes . ')';
        }

        $get_prestadores_amb_part = $this->Fornecedor->find('list', array('fields' => array('codigo'), 'conditions' => array('ambulatorio' => 1, 'prestador_particular' => 1)));

        if (!empty($get_prestadores_amb_part)) {
            $fornecedores_ambulatorios_particulares = implode(',', $get_prestadores_amb_part);
            $sql .= 'AND [Listas_De_Preco_subquery].[codigo_fornecedor] NOT IN (' . $fornecedores_ambulatorios_particulares . ')';
        } //fim

        //consulta
        $consulta_media_cidade = $this->ListaDePrecoProdutoServico->query($sql);


        if ($consulta_media_cidade) {
            foreach ($consulta_media_cidade as $key => $value) {
                $media_cidade = $value[0];
            }
        }

        return $media_cidade;
    }

    public function getListaPpraPcmsoPendente($filtros)
    {

        $this->GrupoEconomicoCliente = &ClassRegistry::init('GrupoEconomicoCliente');

        $having = $conditions = null;

        // popula varivel para WHERE
        $conditions = array(
            //"GrupoEconomicoCliente.codigo_empresa = 1",
            "Cliente.ativo = 1"
        );

        if ($filtros) {

            $filtros = array_filter($filtros);

            $codigo_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $filtros['codigo_cliente'])));
            $codigo_matriz = $codigo_grupo_economico['GrupoEconomico']['codigo_cliente'];

            // Regras para filtros
            $converteFiltrosEmConditions = array(
                'codigo_cliente_alocacao'    => 'cliente.codigo',
                'codigo_cliente'            => 'GrupoEconomico.codigo_cliente'
            );

            if (!empty($codigo_matriz)) {

                $tmpGrupoEconomicoCliente = $this->GrupoEconomicoCliente->find('first', array(
                    'fields' => array('GrupoEconomicoCliente.codigo_grupo_economico'),
                    'conditions' => array(
                        'GrupoEconomicoCliente.codigo_cliente' => $codigo_matriz
                    )
                ));

                $conditions[] = "GrupoEconomico.codigo = " . $tmpGrupoEconomicoCliente['GrupoEconomicoCliente']['codigo_grupo_economico'];
            }

            if ($codigo_matriz != $codigo_grupo_economico['GrupoEconomicoCliente']['codigo_cliente']) {
                $conditions[] = "GrupoEconomicoCliente.codigo_cliente = " . $codigo_grupo_economico['GrupoEconomicoCliente']['codigo_cliente'];
            }

            if (array_key_exists('pendencia', $filtros)) {
                // PENDENTE PPRA 
                if ($filtros['pendencia'] == 'ppra') $conditions[] = "PPRA.STATUS_PPRA = 1";
                // PENDENTE PCMSO
                if ($filtros['pendencia'] == 'pcmso') $conditions[] = "PCMSO.STATUS_PCMSO = 1";
            } else {
                $conditions[] = "(PPRA.STATUS_PPRA = 1 OR PCMSO.STATUS_PCMSO = 1)";
            }
        } else {
            $conditions[] = "(PPRA.STATUS_PPRA = 1 OR PCMSO.STATUS_PCMSO = 1)";
        }

        // popula varivel para SELECT
        $fields = array(
            "Cliente.codigo",
            "Cliente.nome_fantasia",
            "PPRA.STATUS_PPRA",
            "PCMSO.STATUS_PCMSO"
        );

        // popula varivel para FROM
        $joins = array(
            array(
                "table" => "RHHealth.dbo.grupos_economicos",
                "alias" => "GrupoEconomico",
                'type'    => 'LEFT',
                "conditions" => "(GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo)"
            ),
            array(
                "table" => "RHHealth.dbo.cliente",
                "alias" => "Cliente",
                'type'    => 'LEFT',
                "conditions" => "(Cliente.codigo = GrupoEconomicoCliente.codigo_cliente)"
            ),
            array(
                "table" => "( SELECT COUNT(GrupoExposicao.codigo) AS TOTAL_GrupoExposicao, COUNT(ClientesSetoresCargos.codigo) AS TOTAL, ClientesSetoresCargos.codigo_cliente_alocacao, (CASE WHEN COUNT(GrupoExposicao.codigo) < COUNT(ClientesSetoresCargos.codigo) THEN 1 ELSE 2 END) AS STATUS_PPRA FROM clientes_setores_cargos AS ClientesSetoresCargos INNER JOIN cargos AS Cargo ON Cargo.codigo = ClientesSetoresCargos.codigo_cargo AND Cargo.ativo = 1 INNER JOIN setores Setor ON Setor.codigo = ClientesSetoresCargos.codigo_setor AND Setor.ativo = 1 LEFT JOIN clientes_setores AS ClientesSetores ON (ClientesSetoresCargos.codigo_setor = ClientesSetores.codigo_setor and ClientesSetores.codigo_cliente = ClientesSetoresCargos.codigo_cliente_alocacao) LEFT JOIN grupo_exposicao AS GrupoExposicao ON (ClientesSetores.codigo = GrupoExposicao.codigo_cliente_setor AND ClientesSetoresCargos.codigo_cargo = GrupoExposicao.codigo_cargo AND (SELECT COUNT(*) FROM grupos_exposicao_risco GrupoExposicaoRisco WHERE GrupoExposicaoRisco.codigo_grupo_exposicao = GrupoExposicao.codigo ) > 0) WHERE ([ClientesSetoresCargos].[ativo] = 1
       OR [ClientesSetoresCargos].[ativo] IS NULL) GROUP BY ClientesSetoresCargos.codigo_cliente_alocacao )",
                "alias" => "PPRA",
                "conditions" => "PPRA.codigo_cliente_alocacao = Cliente.codigo"
            ),
            array(
                "table" => "( SELECT COUNT(AplicacaoExame.codigo) AS TOTAL_AplicacaoExame, COUNT(ClientesSetoresCargos.codigo) AS TOTAL, ClientesSetoresCargos.codigo_cliente_alocacao, (CASE WHEN COUNT(AplicacaoExame.codigo) < COUNT(ClientesSetoresCargos.codigo) THEN 1 ELSE 2 END) AS STATUS_PCMSO FROM clientes_setores_cargos AS ClientesSetoresCargos INNER JOIN cargos AS Cargo ON Cargo.codigo = ClientesSetoresCargos.codigo_cargo AND Cargo.ativo = 1 INNER JOIN setores Setor ON Setor.codigo = ClientesSetoresCargos.codigo_setor AND Setor.ativo = 1 LEFT JOIN clientes_setores AS ClientesSetores ON (ClientesSetoresCargos.codigo_setor = ClientesSetores.codigo_setor and ClientesSetores.codigo_cliente = ClientesSetoresCargos.codigo_cliente_alocacao) LEFT JOIN aplicacao_exames AS AplicacaoExame ON (ClientesSetoresCargos.codigo_cargo = AplicacaoExame.codigo_cargo and ClientesSetoresCargos.codigo_setor = AplicacaoExame.codigo_setor and ClientesSetoresCargos.codigo_cliente_alocacao = AplicacaoExame.codigo_cliente_alocacao) WHERE ([ClientesSetoresCargos].[ativo] = 1
       OR [ClientesSetoresCargos].[ativo] IS NULL) GROUP BY ClientesSetoresCargos.codigo_cliente_alocacao )",
                "alias" => "PCMSO",
                "conditions" => "PCMSO.codigo_cliente_alocacao = Cliente.codigo"
            )
        );

        // popula varivel para ORDER BY
        $order = "Cliente.codigo ASC, Cliente.nome_fantasia ASC";

        // define options para ORM
        $options = array(
            "fields" => $fields,
            "joins" => $joins,
            "conditions" => $conditions,
            "order" => $order,
            "limit" => 20,
            "recursive" => -1
        );

        return $options;
    }
}
