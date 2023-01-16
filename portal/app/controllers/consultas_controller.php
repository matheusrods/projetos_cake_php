<?php

class ConsultasController extends AppController
{
    public $name = 'Consultas';
    public $components = array('Filtros', 'RequestHandler');
    public $uses = array(
        'Consulta',
        'PropostaCredenciamento',
        'PropostaCredDocumento',
        'PropostaCredEndereco',
        'Servico',
        'FornecedorContato',
        'ListaDePreco',
        'TipoDocumento',
        'EnderecoEstado',
        'StatusPropostaCred',
        'Produto',
        'Usuario',
        'MotivoRecusa',
        'FornecedorDocumento',
        'Fornecedor',
        'FornecedorEndereco',
        'VEndereco',
        'ListaDePrecoProdutoServico',
        'ListaDePrecoProduto',
        'Endereco',
        'EnderecoCidade',
        'EnderecoEstado',
        'Uperfil',
        'Cliente',
        'ClienteSetor',
        'ClienteSetorCargo',
        'AplicacaoExame',
        'GrupoEconomicoCliente',
        'GrupoEconomico',
        'OrdemServico',
        'PcmsoVersoes',
        'PpraVersoes',
        'ProdutoServico',
        'Configuracao'
    );

    function beforeFilter()
    {
        parent::beforeFilter();

        $this->BAuth->allow(array('*'));
    } //FINAL FUNCTION beforeFilter

    function retorna_produto()
    {
        $produtos = $this->Produto->listar('list', array('ativo' => true));
        $this->set(compact('produtos'));
    }

    public function consulta_documentos_pendentes()
    {
        $this->pageTitle = 'Documentação Pendente';
        $this->data['Consulta'] = $this->Filtros->controla_sessao($this->data, 'Consulta');

        $this->set('list_estados', array('' => 'UF') + $this->EnderecoEstado->find('list', array('conditions' => array('codigo_endereco_pais' => '1'), 'fields' => array('codigo', 'descricao'))));

        if (isset($this->data['Consulta']['codigo_estado_endereco']) && $this->data['Consulta']['codigo_estado_endereco']) {
            $this->set('list_cidades', array('' => 'Selecione o Estado Primeiro') + $this->EnderecoCidade->find('list', array('conditions' => array('codigo_endereco_estado' => $this->data['Consulta']['codigo_estado_endereco']), 'fields' => array('codigo', 'descricao'))));
        } else {
            $this->set('list_cidades', array('' => 'Selecione o Estado Primeiro'));
        }

        $this->set('list_documentos', array('' => 'Todos os Documentos') + $this->TipoDocumento->find('list', array('conditions' => array('obrigatorio' => '1', 'status' => '1'), 'fields' => array('codigo', 'descricao'))));
    }

    public function documentos_pendentes($destino, $export = false)
    {

        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'Consulta');

        $conditions = $this->Consulta->converteFiltrosEmConditions($filtros, 'consulta_documentos_pendentes');
        $conditions[] = 'PropostaCredenciamento.codigo_status_proposta_credenciamento in (select status_proposta_credenciamento.codigo from status_proposta_credenciamento where status_proposta_credenciamento.codigo = 7 OR status_proposta_credenciamento.codigo = 13) and PropostaCredenciamento.ativo is not null';

        $fields = array(
            'PropostaCredEndereco.estado',
            'PropostaCredEndereco.cidade',
            'PropostaCredenciamento.codigo',
            'PropostaCredenciamento.nome_fantasia',
            'PropostaCredenciamento.razao_social',
            'PropostaCredenciamento.codigo_documento',
            'PropostaCredenciamento.telefone',
            'PropostaCredenciamento.email',
            'PropostaCredenciamento.codigo_status_proposta_credenciamento',
            'CASE 
				WHEN PropostaCredenciamento.ativo  = 1 THEN \'Ativo\'
				WHEN PropostaCredenciamento.ativo  = 0 THEN \'Inativo\'
				ELSE \'\' END as status',
            '(
				SELECT     
			        count(1)
			    FROM 
			        propostas_credenciamento_documentos PropostaCredDocumento
			        INNER JOIN tipos_documentos TipoDocumento ON TipoDocumento.codigo = PropostaCredDocumento.codigo_tipo_documento
			    WHERE         
			        codigo_proposta_credenciamento = PropostaCredenciamento.codigo AND
			        TipoDocumento.obrigatorio = 1 AND
			        TipoDocumento.status = 1
			  ) as qtd_enviado',
            '(
				SELECT 
					count(1) 
				FROM tipos_documentos 
				where tipos_documentos.obrigatorio = \'1\' 
					AND tipos_documentos.status = \'1\')  as qtd_obrigatorio',
            'StatusPropostaCred.descricao'
        );

        $joins = array(
            array(
                'table' => 'propostas_credenciamento_endereco',
                'alias' => 'PropostaCredEndereco',
                'type' => 'INNER',
                'conditions' => 'PropostaCredEndereco.codigo_proposta_credenciamento = PropostaCredenciamento.codigo'
            ),
            array(
                'table' => 'status_proposta_credenciamento',
                'alias' => 'StatusPropostaCred',
                'type' => 'INNER',
                'conditions' => 'StatusPropostaCred.codigo = PropostaCredenciamento.codigo_status_proposta_credenciamento'
            )
        );


        $lista_propostas_status_pendencia = $this->PropostaCredenciamento->find('all', array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields));

        $listagem_documentacao_obrigatoria_7 = $this->TipoDocumento->find('list', array('conditions' => array('obrigatorio' => '1', 'status' => '1', 'codigo_status_proposta_credenciamento' => '7'), 'fields' => array('codigo', 'descricao')));

        $listagem_documentacao_obrigatoria_13 = $this->TipoDocumento->find('list', array('conditions' => array('obrigatorio' => '1', 'status' => '1', 'codigo_status_proposta_credenciamento' => '13'), 'fields' => array('codigo', 'descricao')));

        $listagem_documentacao_obrigatoria_7 = $this->TipoDocumento->find('list', array('conditions' => array('obrigatorio' => '1', 'status' => '1', 'codigo_status_proposta_credenciamento' => '7'), 'fields' => array('codigo', 'descricao')));

        if (isset($filtros['documento']) && !empty($filtros['documento']) && array_key_exists($filtros['documento'], $listagem_documentacao_obrigatoria_7)) {

            $listagem_documentacao_obrigatoria_7 = array($filtros['documento'] => $listagem_documentacao_obrigatoria_7[$filtros['documento']]);
            $listagem_documentacao_obrigatoria_13 = array();
        } elseif (isset($filtros['documento']) && !empty($filtros['documento']) && array_key_exists($filtros['documento'], $listagem_documentacao_obrigatoria_13)) {

            $listagem_documentacao_obrigatoria_7 = array();
            $listagem_documentacao_obrigatoria_13 = array($filtros['documento'] => $listagem_documentacao_obrigatoria_13[$filtros['documento']]);
        }

        $joins = array(
            array(
                'table' => 'tipos_documentos',
                'alias' => 'TipoDocumento',
                'type' => 'INNER',
                'conditions' => 'TipoDocumento.codigo = PropostaCredDocumento.codigo_tipo_documento'
            )
        );

        foreach ($lista_propostas_status_pendencia as $key => $proposta) {

            // verifica se status é de proposta digitalizado pendente
            if ($proposta['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::AGUARDANDO_ENVIO_TERMO) {

                // verifica se foi enviado (proposta digitalizada)
                $documentos_enviados = $this->PropostaCredDocumento->find('first', array('conditions' => array('codigo_proposta_credenciamento' => $proposta['PropostaCredenciamento']['codigo'], 'TipoDocumento.codigo_status_proposta_credenciamento' => StatusPropostaCred::AGUARDANDO_ENVIO_TERMO), 'joins' => $joins, 'fields' => array('TipoDocumento.codigo', 'TipoDocumento.descricao')));

                if ($documentos_enviados) {

                    unset($lista_propostas_status_pendencia[$key]);
                } else {

                    $lista_propostas_status_pendencia[$key]['DocumentosPendentes'] = $listagem_documentacao_obrigatoria_13;
                }
            } else if ($proposta['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::DOCUMENTACAO_SOLICITADA) {

                if ((int) $proposta[0]['qtd_obrigatorio'] > (int) $proposta[0]['qtd_enviado']) {

                    if ((int) $proposta[0]['qtd_enviado'] > 0) {

                        $documentos_enviados = $this->PropostaCredDocumento->find('list', array('conditions' => array('codigo_proposta_credenciamento' => $proposta['PropostaCredenciamento']['codigo']), 'joins' => $joins, 'fields' => array('TipoDocumento.codigo', 'TipoDocumento.descricao')));

                        $lista_propostas_status_pendencia[$key]['DocumentosPendentes'] = array();

                        $lista_propostas_status_pendencia[$key]['DocumentosEnviados'] = array();

                        foreach ($listagem_documentacao_obrigatoria_7 as $k => $doc_obrigatorio) {

                            if (!array_key_exists($k, $documentos_enviados)) {

                                $lista_propostas_status_pendencia[$key]['DocumentosPendentes'][$k] = $doc_obrigatorio;
                            } else {

                                $lista_propostas_status_pendencia[$key]['DocumentosEnviados'][$k] = $doc_obrigatorio;
                            }
                        }
                    } else {

                        $lista_propostas_status_pendencia[$key]['DocumentosPendentes'] = $listagem_documentacao_obrigatoria_7;
                    }
                } else {

                    unset($lista_propostas_status_pendencia[$key]);
                }
            }

            if (isset($lista_propostas_status_pendencia[$key]['DocumentosPendentes']) && !count($lista_propostas_status_pendencia[$key]['DocumentosPendentes'])) {

                unset($lista_propostas_status_pendencia[$key]);
            }
        }

        //se ele flegar na tela para exportar a lista ele vai montar o csv
        if ($export) {

            $nome_arquivo = date('YmdHis') . '_documentos_pendentes.csv';

            //headers
            ob_clean();
            header('Content-Encoding: UTF-8');
            header("Content-Type: application/force-download;charset=utf-8");
            header(sprintf('Content-Disposition: attachment; filename="%s"', $nome_arquivo));
            header('Pragma: no-cache');

            //cabecalho do arquivo
            echo utf8_decode('"Código Prestador";"Nome Prestador";"Razão Social Prestador";"Nome fantasia";"CNPJ";"Estado";"Cidade";"Status";"Documentação Pendente";') . "\n";

            if (!empty($lista_propostas_status_pendencia)) {

                foreach ($lista_propostas_status_pendencia as $lista) {
                    $linha  = $lista['PropostaCredenciamento']['codigo'] . ';';
                    $linha .= $lista['PropostaCredenciamento']['nome_fantasia'] . ';';
                    $linha .= $lista['PropostaCredenciamento']['razao_social'] . ';';
                    $linha .= $lista['PropostaCredenciamento']['nome_fantasia'] . ';';
                    $linha .= $lista['PropostaCredenciamento']['codigo_documento'] . ';';
                    $linha .= $lista['PropostaCredEndereco']['estado'] . ';';
                    $linha .= $lista['PropostaCredEndereco']['cidade'] . ';';
                    $linha .= $lista[0]['status'] . ';';

                    //ele converte e separa em virgulas a linha dos documentos pendentes
                    $linha_docs = implode(', ', $lista['DocumentosPendentes']);
                    $linha .= $linha_docs . ';';

                    echo utf8_decode($linha) . "\n";
                }
            }

            exit;
        } else {

            // pr($lista_propostas_status_pendencia);exit;
            $this->set('lista_propostas', $lista_propostas_status_pendencia);
        }
    }

    public function consulta_propostas()
    {
        $this->pageTitle = 'Propostas de Credenciamento';
        $this->data['ConsultaProposta'] = $this->Filtros->controla_sessao($this->data, 'ConsultaProposta');

        $this->StatusPropostaCred->virtualFields = array(
            'ordenada' => 'CONCAT(StatusPropostaCred.ordenacao, " - ", StatusPropostaCred.descricao)'
        );

        $this->set('array_status', array('' => 'Todos os Status') + $this->StatusPropostaCred->find('list', array(
            'fields' => array('StatusPropostaCred.codigo', 'ordenada'),
            'order' => array('StatusPropostaCred.ordenacao ASC')
        )));

        $lista_estados = $this->EnderecoEstado->find('list', array('conditions' => array('codigo_endereco_pais' => '1'), 'fields' => array('codigo', 'descricao')));
        $this->set('list_estados', array('' => 'UF') + $lista_estados);

        if (isset($this->data['ConsultaProposta']['codigo_estado_endereco']) && $this->data['ConsultaProposta']['codigo_estado_endereco']) {
            $this->set('list_cidades', array('' => 'Selecione o Estado Primeiro') + $this->EnderecoCidade->find('list', array('conditions' => array('codigo_endereco_estado' => $this->data['ConsultaProposta']['codigo_estado_endereco']), 'fields' => array('codigo', 'descricao'))));
        } else {
            $this->set('list_cidades', array('' => 'Selecione o Estado Primeiro'));
        }

        $this->set('list_usuarios', array('' => 'Todos') + $this->Usuario->find('list', array('conditions' => array('codigo_uperfil <>' => Uperfil::CREDENCIANDO))));
        $this->set('list_motivos', array('' => 'Todos') + $this->MotivoRecusa->find('list', array('conditions' => array('ativo' => '1'), 'fields' => array('codigo', 'descricao'))));
    }

    public function propostas()
    {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'ConsultaProposta');

        $conditions = $this->Consulta->converteFiltrosEmConditions($filtros, 'consulta_propostas');

        $this->paginate['PropostaCredenciamento'] = array(
            'recursive' => -1,
            'conditions' => $conditions,
            'fields' => array(
                'DISTINCT PropostaCredenciamento.codigo',
                'PropostaCredEndereco.cidade',
                'PropostaCredEndereco.estado',
                'PropostaCredHistorico.data_inclusao',
                'PropostaCredenciamento.codigo',
                'PropostaCredenciamento.razao_social',
                'PropostaCredenciamento.nome_fantasia',
                'PropostaCredenciamento.telefone',
                'PropostaCredenciamento.email',
                'PropostaCredenciamento.codigo_usuario_inclusao',
                'PropostaCredenciamento.codigo_usuario_alteracao',
                'PropostaCredenciamento.codigo_motivo_recusa',
                'PropostaCredenciamento.data_inclusao',
                'PropostaCredenciamento.ativo',
                'PropostaCredenciamento.codigo_status_proposta_credenciamento',
                '(select top 1 s.descricao from status_proposta_credenciamento s where s.codigo=PropostaCredenciamento.codigo_status_proposta_credenciamento) as status',
                '(select top 1 CONVERT(VARCHAR(10),data_inclusao,3) from propostas_credenciamento_historico pch where pch.codigo_proposta_credenciamento = PropostaCredenciamento.codigo AND (pch.codigo_status_proposta_credenciamento = 10 OR pch.codigo_status_proposta_credenciamento = 11) ORDER BY pch.data_inclusao DESC) as data_reprovado',
                '(select top 1 CONVERT(VARCHAR(10),data_inclusao,3) from propostas_credenciamento_historico pch where pch.codigo_proposta_credenciamento = PropostaCredenciamento.codigo AND (pch.codigo_status_proposta_credenciamento = 7) ORDER BY pch.data_inclusao ASC) as data_aprovado',
                '(select count(1) from tipos_documentos T where T.obrigatorio = 1 AND T.status = 1) AS qtd_documento',
                '(	SELECT
								count(1)
							FROM
								propostas_credenciamento_documentos E
								INNER JOIN tipos_documentos TIPO ON (E.codigo_tipo_documento = TIPO.codigo)
							WHERE
								E.codigo_proposta_credenciamento = PropostaCredenciamento.codigo AND
								TIPO.obrigatorio = 1 AND
								TIPO.status = 1
						) AS qtd_enviado'
            ),
            'joins' => array(
                array(
                    'table' => 'propostas_credenciamento_endereco',
                    'alias' => 'PropostaCredEndereco',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PropostaCredEndereco.codigo_proposta_credenciamento = PropostaCredenciamento.codigo',
                        'PropostaCredEndereco.matriz = 1'
                    )
                ),
                array(
                    'table' => 'propostas_credenciamento_historico',
                    'alias' => 'PropostaCredHistorico',
                    'type' => 'LEFT OUTER',
                    'conditions' => array(
                        'PropostaCredHistorico.codigo_proposta_credenciamento = PropostaCredenciamento.codigo',
                        'PropostaCredHistorico.codigo_status_proposta_credenciamento = PropostaCredenciamento.codigo_status_proposta_credenciamento'
                    )
                )
            ),
            'limit' => 25,
            'order' => 'PropostaCredenciamento.codigo DESC'
        );

        $this->set('propostas_credenciamento', $this->paginate('PropostaCredenciamento'));
        $this->set('motivos_recusa', $this->MotivoRecusa->find('list', array('conditions' => array('ativo' => '1'), 'fields' => array('codigo', 'descricao'))));
        $this->set('usuarios', $this->Usuario->find('list', array('conditions' => array('codigo_uperfil <>' => Uperfil::CREDENCIANDO), 'fields' => array('codigo', 'nome'))));
    }

    public function consulta_produtos_servicos()
    {
        $this->pageTitle = 'Produtos e Serviços';

        $this->data[$this->Consulta->name] = $this->Filtros->controla_sessao($this->data, $this->Consulta->name);
        //alimenta o combo servicos se tiver produto selecionado
        if (!empty($this->data['Consulta']['codigo_produto'])) {
            $this->set('servicos', $this->ProdutoServico->servicosPorProduto($this->data['Consulta']['codigo_produto']));
        }

        $estados = $this->EnderecoEstado->retorna_estados();

        if (isset($this->data['Consulta']['estado']) && $this->data['Consulta']['estado']) {
            $cidades = array('' => 'Selecione o Estado Primeiro') + $this->EnderecoCidade->find('list', array('conditions' => array('codigo_endereco_estado' => $this->data['Consulta']['estado'],  'invalido' => 0), 'fields' => array('codigo', 'descricao')));
        } else {
            $cidades = array('' => 'Selecione o Estado Primeiro');
        }

        $this->retorna_produto();
        $this->set(compact('estados', 'cidades'));
    }

    public function listagem_produtos_servicos()
    {
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Consulta->name);

        //pega a descricao do estado_abreviado
        if (!empty($filtros['estado'])) {
            $estado = $this->EnderecoEstado->find('first', array('conditions' => array('EnderecoEstado.codigo' => $filtros['estado'])));
            $filtros['estado'] = $estado['EnderecoEstado']['abreviacao'];
        }
        if (!empty($filtros['cidade'])) {
            $cidade = $this->EnderecoCidade->find('first', array('conditions' => array('EnderecoCidade.codigo' => $filtros['cidade'])));
            $filtros['cidade'] = $cidade['EnderecoCidade']['descricao'];
        }

        $conditions = $this->Consulta->converteFiltEmCond_ProdutosServicos($filtros);
        $conditions = array_merge($conditions, array('Produto.ativo' => 1, 'Servico.ativo' => 1));
        //monta a query
        $produtos_servicos = $this->Consulta->get_produtos_servicos($conditions, 'list');
        //paginacao
        $this->paginate['ListaDePreco'] = array(
            'conditions' => $produtos_servicos['conditions'],
            'limit' => 50,
            'joins' => $produtos_servicos['joins'],
            'fields' => $produtos_servicos['fields'],
            'order' => $produtos_servicos['order'],
            'recursive' => -1
        );
        // pr($this->ListaDePreco->find('sql',$this->paginate['ListaDePreco']));
        $codigos_servicos = array();
        $codigos_lpp = array();
        $cods_lpp = array();
        $cod_services = array();
        $listagem = $this->paginate('ListaDePreco');
        if (!empty($listagem)) {
            foreach ($listagem as $key => $dadoPServicos) {

                $codigos_servicos[$key]['codigos_servicos'] = $dadoPServicos['Servico']['codigo'];

                foreach ($codigos_servicos as $key => $value) {
                    $cod_services[$key] = $value['codigos_servicos'];
                }

                $codigos_lpp[$key]['codigos_lpp'] = $dadoPServicos['ListaDePrecoProdutoServico']['codigo_lista_de_preco_produto'];

                foreach ($codigos_lpp as $key => $value) {
                    $cods_lpp[$key] = $value['codigos_lpp'];
                }
            }
            if (!empty($cod_services)) {
                $cods_Servicos = implode(', ', $cod_services);
                $lista_codigos['Codigos_servicos'] = $cods_Servicos;
            }

            if (!empty($cods_lpp)) {
                $cods_lpp = implode(', ', $cods_lpp);
                $list_cods_lpp['Codigos_lpp'] = $cods_lpp;
            }
        }
        $this->set(compact('listagem', 'lista_codigos', 'list_cods_lpp'));
    }

    public function consulta_documentos_vencidos_fornecedor()
    {
        //titulo da page
        $this->pageTitle = 'Documentos do Prestador';
        //filtros da sessao
        $this->data[$this->Consulta->name] = $this->Filtros->controla_sessao($this->data, $this->Consulta->name);
        //carrega os combos
        $this->carregaCombosDocsPrestador($this->data);
    }

    public function listagem_documentos_vencidos_fornecedor($export = null)
    {
        $this->layout = 'ajax';
        //filtros da sessao
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Consulta->name);
        //verifica se o usuario é um cliente
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            //seta o filtro do usuario cliente
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        if (!isset($filtros['situacao'])) {
            $filtros['situacao'] = array('VI');
        }

        //verifica se tem a data de inicio
        if (empty($filtros['data_inicio'])) {
            $filtros['data_inicio'] = date('d/m/Y');
            $filtros['data_fim'] = date('d/m/Y', strtotime('+ 30 days'));
        }

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300); // 5min

        //variavel auxiliar
        $dados_lista = array();
        if (!empty($filtros)) {
            //monta as conditions com base no filtro
            $conditions = $this->Consulta->ConditionsDocsPrestadores($filtros);
            //caso seja export gera a query e direciona para o metodo que irá exportar os dados em csv.
            if ($export) {
                //query dos documentos
                $query = $this->Consulta->get_docs_prestadores('sql', compact('conditions', 'filtros'));
                //exporta os dados
                $this->exportDocsPrestadores($query);
            } //fim if export
            //monta a query
            $dados_lista = $this->Consulta->get_docs_prestadores('all', compact('conditions', 'filtros'));
            $this->set(compact('dados_lista'));
        }
    }

    public function consulta_ppra_pcmso_pendente($return = false)
    {

        //redireciona para a tela de terceiros onde deveria estar
        if (!is_null($this->BAuth->user('codigo_cliente'))) {
            $this->redirect(array('action' => 'ppra_pcmso_pendente_terceiros'));
        }

        Comum::returnPoint();

        $this->pageTitle = 'PGR - PCMSO Pendentes';

        $this->data['Consulta'] = $this->Filtros->controla_sessao($this->data, 'Consulta');

        $filtros = $this->data['Consulta'];

        // $filtros = $this->Filtros->controla_sessao($this->data, 'Consulta');
        // Pega código do cliente para setar input
        $codigo_cliente = null;
        if (isset($filtros)) {
            foreach ($filtros as $key => $value) {
                $$key = $value;
            }
        }

        // Unidades para Select da filtro
        $unidades = array();
        if (!empty($codigo_cliente)) {
            $unidades = $this->Cliente->lista_por_cliente($codigo_cliente);
        }

        // Status para Select do filtro
        $status = array('ppra' => 'PGR', 'pcmso' => 'PCMSO');

        // Retorna 
        if ($return) return array('unidades' => $unidades, 'status' => $status);

        $options_matriz = $this->GrupoEconomicoCliente->monta_lista_matriz_pendente();

        // Render da View
        $this->set(compact('unidades', 'status', 'options_matriz'));
    }

    public function listagem_ppra_pcmso_pendente()
    {

        $this->layout = 'ajax';

        // Pega somente os filtros com valor
        $filtros = $this->Filtros->controla_sessao($this->data, 'Consulta');

        $listagem = array();

        $codigo_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $filtros['codigo_cliente'])));

        if ($filtros['codigo_cliente'] != '' && !empty($codigo_grupo_economico)) {

            $options = $this->Consulta->getListaPpraPcmsoPendente($filtros);

            $this->paginate['GrupoEconomicoCliente'] = $options;

            // pr($this->GrupoEconomicoCliente->find('sql',$this->paginate['GrupoEconomicoCliente']));

            $listagem = $this->paginate('GrupoEconomicoCliente');
        }

        $action = 'consulta_ppra_pcmso_pendente_sc';
        $StatusStyle = array(
            '0' => array('title' => ' ... ', 'class' => 'default', 'link' => null),
            '1' => array('title' => 'Pendente', 'class' => 'important', 'link' => array('action' => $action)),
            '2' => array('title' => 'Concluído', 'class' => 'success', 'link' => null)
        );

        $this->set(compact('listagem', 'StatusStyle'));
    }

    public function consulta_ppra_pcmso_pendente_sc($codigo_cliente, $tipo, $return = false)
    {

        Comum::returnPoint();

        $this->data['Consulta'] = $this->Filtros->controla_sessao($this->data, 'Consulta');

        if ($tipo == 'pcmso') {
            $this->pageTitle = "PCMSO ";
        } else {
            $this->pageTitle = "PGR ";
        }

        $this->pageTitle .= ' Pendentes / Setor e Cargo';

        $rsCliente = $this->Cliente->carregar($codigo_cliente);

        $CodigoCliente = $codigo_cliente;
        $NomeCliente =  $rsCliente['Cliente']['razao_social'];
        // Retorna 
        if ($return) return array(
            'codigo_cliente' => $codigo_cliente,
            'tipo' => $tipo,
            'CodigoCliente' => $CodigoCliente,
            'NomeCliente' => $NomeCliente
        );

        // Montando option do filtro Status //
        $options_status = array(
            '0' => 'Todos',
            '1' => 'Pendentes',
            '2' => 'OK'
        );

        // Render da View
        $this->set(compact('codigo_cliente', 'tipo', 'CodigoCliente', 'NomeCliente', 'options_status'));
    }

    public function listagem_ppra_pcmso_pendente_sc($codigo_cliente, $tipo)
    {
        $this->layout = 'ajax';

        $filtroNome = 'Consulta';
        // Pega somente os filtros com valor
        $filtros = $this->Filtros->controla_sessao($this->data, $filtroNome);

        list($controller_link, $botao_finalizar_processo, $options) = $this->Consulta->dados_listagem_ppra_pcmso_pendente_sc($codigo_cliente, $tipo, $filtros);

        $this->paginate['GrupoEconomicoCliente'] = $options;

        // debug($this->GrupoEconomicoCliente->find('sql',$this->paginate['GrupoEconomicoCliente']));

        $listagemPendentes = $this->paginate('GrupoEconomicoCliente');

        $this->set(compact('controller_link', 'botao_finalizar_processo', 'tipo', 'codigo_cliente', 'listagemPendentes'));
    }

    public function finalizar_processo_pendente($codigo_cliente, $tipo)
    {

        switch ($tipo) {
            case 'ppra':
                // url / codigo_cliente / status / clonar_versao / redirect_automatico
                $url = 'clientes_implantacao/atualiza_status_ppra_versionamento';
                $this->requestAction($url . '/' . $codigo_cliente . '/3/1/1', array('return' => 1));
                break;
            case 'pcmso':
                $url = 'clientes_implantacao/atualiza_status_pcmso_ult_versao';
                $this->requestAction($url . '/' . $codigo_cliente, array('return' => 1));
                break;
        }
    }

    public function ausencia_risco($codigo_cliente, $tipo)
    {

        switch ($tipo) {

            case 'pcmso':
                $url = 'aplicacao_exames/preenche_com_exame_clinico';
                $this->requestAction($url . '/' . $codigo_cliente . "/1/1", array('return' => 1));
                break;

            case 'ppra':
                $url = 'grupos_exposicao/preenche_com_ausencia_risco';
                $this->requestAction($url . '/' . $codigo_cliente . "/1/1", array('return' => 1));
                break;
        }
    }


    public function gera_arquivo_pendencia_ppra_pcmso()
    {

        $this->layout = false;

        //qual tipo de pendencia, seja ela ppra ou pcmso
        $link = $this->params['url']['key'];

        //popular o codigo da empresa
        $link_ce = (empty($this->params['url']['ce']) ? null : $this->params['url']['ce']);

        //descriptografa a chave da url        
        $link = Comum::descriptografarLink($link);

        if (!is_null($link_ce)) {
            $link_ce = Comum::descriptografarLink($link_ce);
        }

        $link_ce = ($link_ce == 'null' ? null : $link_ce);

        //separa os dados
        //all -> para usuarios que não tem cliente relacionado (interno)
        //20 -> codigo do cliente
        $code = str_replace("'", "", $link);

        ob_clean(); //limpa o cache dos dados

        //seta os headers
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="Pendencia_ppra_pcmso' . date('YmdHis') . '.csv"');

        //pega o arquivo
        $dados = $this->Consulta->gerar_arquivo_pendencia_ppra_pcmso($code, $link_ce);

        echo $dados;
        die;
    }

    public function ppra_pcmso_pendente_terceiros($return = false)
    {

        Comum::returnPoint();
        $this->loadModel('Cargo');
        $this->loadModel('Setor');

        $this->pageTitle = 'PGR - PCMSO Pendentes';

        $filtros = $this->Filtros->controla_sessao($this->data, 'Consulta');

        $this->data['Consulta'] = $filtros;

        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if (empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        // $filtros['codigo_cliente'] = (isset($this->authUsuario['Usuario']['multicliente'])) ? $this->normalizaCodigoCliente($filtros['codigo_cliente']) : $filtros['codigo_cliente'];

        // Status para Select do filtro
        $status = array('ppra' => 'PGR', 'pcmso' => 'PCMSO');

        // Retorna 
        if ($return) return array('status' => $status);

        $opcoes_matriz = $this->GrupoEconomicoCliente->monta_lista_matriz_pendente();

        // Render da View
        $this->set(compact(
            'status',
            'opcoes_matriz'
        ));


        $this->ppra_pcmso_pendente_filtros($filtros);
    }

    public function ppra_pcmso_pendente_filtros($thisData = null)
    {
        // carrega dependencias		
        $this->loadModel('GrupoEconomicoCliente');
        $this->loadModel('Setor');
        $this->loadModel('Cargo');
        $this->loadmodel('Consulta');

        $unidades = array();
        $setores = array();
        $cargos = array();

        // converte com $this->normalizaCodigoCliente pois codigo_cliente pode estar vindo do form como string ou da sessão como array
        if (isset($thisData['codigo_cliente']) && !empty($thisData['codigo_cliente'])) {
            $codigo_cliente = $this->normalizaCodigoCliente($thisData['codigo_cliente']);
            $thisData['codigo_cliente'] = $codigo_cliente;
            $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
            $setores = $this->Setor->lista($codigo_cliente);
            $cargos = $this->Cargo->lista($codigo_cliente);
        }

        // configura no $this->data
        $this->data['Consulta'] = $thisData;

        $listagem = array();

        $this->set(compact('unidades', 'setores', 'cargos', 'listagem'));
    }

    public function listagem_ppra_pcmso_pendente_terceiros()
    {

        $this->layout = 'ajax';
        $having = $conditions = null;

        ###########################################################################
        ###########################################################################
        ####################TRATAMENTO PARA A HOLDING MULTICLIENTE#################
        ###########################################################################
        ###########################################################################
        // if(!is_null($this->BAuth->user('codigo_cliente'))) {
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {

            $codigo_cliente = $this->authUsuario['Usuario']['codigo_cliente'];
            $this->data['Consulta']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            // $codigo_cliente = $codigo_cliente[0]; 
        }


        // Pega somente os filtros com valor
        $filtros = $this->Filtros->controla_sessao($this->data, 'Consulta');

        // popula varivel para WHERE
        $conditions = array(
            //"GrupoEconomicoCliente.codigo_empresa = 1",
            "Cliente.ativo = 1"
        );

        if ($filtros) {

            $filtros = array_filter($filtros);

            // Regras para filtros
            $converteFiltrosEmConditions = array(
                'codigo_cliente_alocacao'    => 'cliente.codigo',
                'codigo_cliente'            => 'GrupoEconomico.codigo_cliente'
            );

            if (empty($filtros) && !empty($codigo_cliente)) {
                $filtros['codigo_cliente'] = $codigo_cliente;
            }

            if (!empty($filtros['codigo_cliente'])) {
                $conditions[] = "GrupoEconomicoCliente.codigo_grupo_economico IN (SELECT codigo_grupo_economico FROM RHHealth.dbo.grupos_economicos_clientes WHERE codigo_cliente " . $this->Consulta->rawsql_codigo_cliente($filtros['codigo_cliente']) . " )";
            }

            if (array_key_exists('pendencia', $filtros)) {
                // PENDENTE PGR 
                if ($filtros['pendencia'] == 'ppra') $conditions[] = "PPRA.STATUS_PPRA = 1";
                // PENDENTE PCMSO
                if ($filtros['pendencia'] == 'pcmso') $conditions[] = "PCMSO.STATUS_PCMSO = 1";
            } else {
                $conditions[] = "(PPRA.STATUS_PPRA = 1 OR PCMSO.STATUS_PCMSO = 1)";
            }
        } else {

            if (!empty($codigo_cliente)) {
                $conditions[] = "GrupoEconomicoCliente.codigo_grupo_economico IN (SELECT codigo_grupo_economico FROM RHHealth.dbo.grupos_economicos_clientes WHERE codigo_cliente " . $this->Consulta->rawsql_codigo_cliente($codigo_cliente) . " )";
            }

            $conditions[] = "(PPRA.STATUS_PPRA = 1 OR PCMSO.STATUS_PCMSO = 1)";
        }

        // popula varivel para SELECT
        $fields = array(
            "Cliente.codigo",
            "Cliente.nome_fantasia",
            "PPRA.STATUS_PPRA",
            "PCMSO.STATUS_PCMSO"
        );

        //se vier setor
        $condition_setor = '';
        $condition_cargo = '';
        $condition_funcionario_pcmso = '';
        $condition_funcionario_ppra = '';

        if (!empty($filtros['codigo_setor'])) {
            $condition_setor = " AND Setor.codigo = " . $filtros['codigo_setor'] . "";
        }

        if (!empty($filtros['codigo_cargo'])) {
            $condition_cargo = " AND Cargo.codigo = " . $filtros['codigo_cargo'] . "";
        }

        if (isset($filtros['codigo_funcionario']) && !empty($filtros['codigo_funcionario'])) {
            $condition_funcionario_pcmso = "AND AplicacaoExame.codigo_funcionario = " . $filtros['codigo_funcionario'];
        }

        if (isset($filtros['codigo_funcionario']) && !empty($filtros['codigo_funcionario'])) {
            $condition_funcionario_ppra = "AND GrupoExposicao.codigo_funcionario = " . $filtros['codigo_funcionario'];
        }

        // debug($conditions);

        // popula varivel para FROM
        $joins = array(
            array(
                "table"      => "RHHealth.dbo.grupos_economicos",
                "alias"      => "GrupoEconomico",
                'type'         => 'LEFT',
                "conditions" => "(GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo)"
            ),
            array(
                "table"      => "RHHealth.dbo.cliente",
                "alias"      => "Cliente",
                'type'         => 'LEFT',
                "conditions" => "(Cliente.codigo = GrupoEconomicoCliente.codigo_cliente)"
            ),
            array(
                "table" => "( SELECT COUNT(GrupoExposicao.codigo) AS TOTAL_GrupoExposicao, COUNT(ClientesSetoresCargos.codigo) AS TOTAL, ClientesSetoresCargos.codigo_cliente_alocacao, (CASE WHEN COUNT(GrupoExposicao.codigo) < COUNT(ClientesSetoresCargos.codigo) THEN 1 ELSE 2 END) AS STATUS_PPRA FROM clientes_setores_cargos AS ClientesSetoresCargos INNER JOIN cargos AS Cargo ON Cargo.codigo = ClientesSetoresCargos.codigo_cargo AND Cargo.ativo = 1 INNER JOIN setores Setor ON Setor.codigo = ClientesSetoresCargos.codigo_setor AND Setor.ativo = 1 LEFT JOIN clientes_setores AS ClientesSetores ON (ClientesSetoresCargos.codigo_setor = ClientesSetores.codigo_setor and ClientesSetores.codigo_cliente = ClientesSetoresCargos.codigo_cliente_alocacao) LEFT JOIN grupo_exposicao AS GrupoExposicao ON (ClientesSetores.codigo = GrupoExposicao.codigo_cliente_setor AND ClientesSetoresCargos.codigo_cargo = GrupoExposicao.codigo_cargo AND (SELECT COUNT(*) FROM grupos_exposicao_risco GrupoExposicaoRisco WHERE GrupoExposicaoRisco.codigo_grupo_exposicao = GrupoExposicao.codigo ) > 0) 
		        	WHERE ([ClientesSetoresCargos].[ativo] = 1 OR [ClientesSetoresCargos].[ativo] IS NULL) " . $condition_setor . " " . $condition_cargo . " " . $condition_funcionario_ppra . "
		        	GROUP BY ClientesSetoresCargos.codigo_cliente_alocacao )",
                "alias"      => "PPRA",
                "conditions" => "PPRA.codigo_cliente_alocacao = Cliente.codigo"
            ),
            array(
                "table" => "( SELECT COUNT(AplicacaoExame.codigo) AS TOTAL_AplicacaoExame, COUNT(ClientesSetoresCargos.codigo) AS TOTAL, ClientesSetoresCargos.codigo_cliente_alocacao, (CASE WHEN COUNT(AplicacaoExame.codigo) < COUNT(ClientesSetoresCargos.codigo) THEN 1 ELSE 2 END) AS STATUS_PCMSO FROM clientes_setores_cargos AS ClientesSetoresCargos INNER JOIN cargos AS Cargo ON Cargo.codigo = ClientesSetoresCargos.codigo_cargo AND Cargo.ativo = 1 INNER JOIN setores Setor ON Setor.codigo = ClientesSetoresCargos.codigo_setor AND Setor.ativo = 1 LEFT JOIN clientes_setores AS ClientesSetores ON (ClientesSetoresCargos.codigo_setor = ClientesSetores.codigo_setor and ClientesSetores.codigo_cliente = ClientesSetoresCargos.codigo_cliente_alocacao) LEFT JOIN aplicacao_exames AS AplicacaoExame ON (ClientesSetoresCargos.codigo_cargo = AplicacaoExame.codigo_cargo and ClientesSetoresCargos.codigo_setor = AplicacaoExame.codigo_setor and ClientesSetoresCargos.codigo_cliente_alocacao = AplicacaoExame.codigo_cliente_alocacao) 
		        	WHERE ([ClientesSetoresCargos].[ativo] = 1 OR [ClientesSetoresCargos].[ativo] IS NULL) " . $condition_setor . " " . $condition_cargo . " " . $condition_funcionario_pcmso . "
		        	GROUP BY ClientesSetoresCargos.codigo_cliente_alocacao )",
                "alias"      => "PCMSO",
                "conditions" => "PCMSO.codigo_cliente_alocacao = Cliente.codigo"
            )
        );

        // popula varivel para ORDER BY
        $order = "Cliente.codigo ASC, Cliente.nome_fantasia ASC";

        // define options para ORM
        $options = array(
            "fields"     => $fields,
            "joins"         => $joins,
            "conditions" => $conditions,
            "order"      => $order,
            "limit"      => 20,
            "recursive"  => -1
        );

        // pr($this->GrupoEconomicoCliente->find('sql', $options));

        $this->paginate['GrupoEconomicoCliente'] = $options;

        $action = 'ppra_pcmso_pendente_sc_terceiros';
        $StatusStyle = array(
            '0' => array('title' => ' ... ', 'class'     => 'default', 'link'   => null),
            '1' => array('title' => 'Pendente', 'class'  => 'important', 'link' => array('action' => $action)),
            '2' => array('title' => 'Concluído', 'class' => 'success', 'link'   => null),
            '3' => array('title' => 'Validar', 'class'      => 'warning', 'link'   => array('action' => $action))
        );

        $listagem = $this->paginate('GrupoEconomicoCliente');

        $this->set(compact('listagem', 'StatusStyle'));
    }

    function retorna_dados_cliente($codigo_cliente)
    {
        $cliente = $this->Cliente->find('first', array('conditions' => array('codigo' => $codigo_cliente)));
        if (empty($this->data)) {
            $this->data = $cliente;
        } else {
            $this->data = array_merge($this->data, $cliente);
        }
        $codigo_cliente = is_array($codigo_cliente) ? implode(',', $codigo_cliente) : $codigo_cliente;
        $this->set(compact('codigo_cliente'));
    }

    public function carrega_combos_grupo_economico($model)
    {
        $this->loadModel('Cargo');
        $this->loadModel('Setor');
        $this->loadModel('GrupoEconomico');
        $this->loadModel('GrupoEconomicoCliente');

        $codigo_cliente = $this->data[$model]['codigo_cliente'];

        if (!empty($codigo_cliente)) {
            $codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
        }

        $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
        $setores = $this->Setor->lista($codigo_cliente);
        $cargos = $this->Cargo->lista($codigo_cliente);
        $this->set(compact('unidades', 'setores', 'cargos'));
    }

    public function retorna_codigo_grupo_economico()
    {

        /***************************************************
         * validacao adicionado para evitar o cliente de
         * burlar o acesso e ver dados de outros clientes;
         ***************************************************/
        if (!is_null($this->BAuth->user('codigo_cliente'))) {
            $codigo_unidade = $this->BAuth->user('codigo_cliente');
        } else {
            $codigo_unidade = $this->params['form']['codigo_unidade'];
        }

        $this->GrupoEconomicoCliente->virtualFields = false;

        $dados_grupo_economico = $this->GrupoEconomicoCliente->find(
            'first',
            array(
                'conditions' => array(
                    'GrupoEconomicoCliente.codigo_cliente' => $codigo_unidade
                ),
                'recursive' => '-1',
                'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'
            )
        );

        echo json_encode(
            array('codigo_grupo_economico' => $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'])
        );
        exit;
    }

    public function autocomplete_funcionario()
    {
        $this->loadModel('GrupoEconomico');
        $this->loadModel('GrupoEconomicoCliente');

        $codigo_cliente = $this->passedArgs['codigo'];

        $codigo_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
        $codigos_unidades = $this->GrupoEconomicoCliente->lista($codigo_matriz);

        $conditions = array(
            'ClienteFuncionario.codigo_cliente' => array_keys($codigos_unidades),
            'Funcionario.nome LIKE' => $_GET['term'] . '%'
        );

        $fields = array('Funcionario.codigo', 'Funcionario.nome');
        $recursive = 1;
        $order = array('Funcionario.nome');

        $list = $this->ClienteFuncionario->find('list', compact('conditions', 'fields', 'recursive', 'order'));

        $result = array();
        foreach ($list as $key => $value) {
            $result[] = array('value' => $key, 'label' => $value);
        }
        echo json_encode($result);
        die();
    }

    public function ppra_pcmso_pendente_sc_terceiros($codigo_cliente, $tipo, $return = false)
    {

        Comum::returnPoint();

        $this->data['Consulta'] = $this->Filtros->controla_sessao($this->data, 'Consulta');
        $this->data['Consulta']['codigo_cliente'] = $codigo_cliente;

        if ($tipo == 'pcmso') {
            $this->pageTitle = "PCMSO ";
        } else {
            $this->pageTitle = "PGR";
        }

        $this->pageTitle .= ' Pendentes / Setor e Cargo';

        $rsCliente = $this->Cliente->carregar($codigo_cliente);

        $CodigoCliente     = $codigo_cliente;
        $NomeCliente     = $rsCliente['Cliente']['razao_social'];
        // Retorna 
        if ($return)
            return array(
                'codigo_cliente' => $codigo_cliente,
                'tipo'             => $tipo,
                'CodigoCliente' => $CodigoCliente,
                'NomeCliente'    => $NomeCliente
            );

        // Montando option do filtro Status //
        $options_status = array(
            '0' => 'Todos',
            '1' => 'Pendentes',
            '2' => 'OK'
        );
        if ($tipo == "pcmso") {
            $options_status = array(
                '0' => 'Todos',
                '1' => 'Pendentes',
                '2' => 'OK',
                '3' => 'Validação',
            );
        }
        // Render da View
        $this->set(compact('codigo_cliente', 'tipo', 'CodigoCliente', 'NomeCliente', 'options_status'));
    }

    public function listagem_ppra_pcmso_pendente_sc_terceiros($codigo_cliente, $tipo)
    {
        $this->layout = 'ajax';

        $filtroNome = 'Consulta';
        // Pega somente os filtros com valor
        $filtros = $this->Filtros->controla_sessao($this->data, $filtroNome);

        list($controller_link, $botao_finalizar_processo, $options) = $this->Consulta->listagem_ppra_pcmso_pendente_sc_terceiros($codigo_cliente, $tipo, $filtros);

        // debug($this->GrupoEconomico->find('sql', $options));exit;
        $this->paginate['GrupoEconomico'] = $options;


        $listagemPendentes = $this->paginate('GrupoEconomico');

        if ($listagemPendentes && $tipo == 'pcmso') {

            foreach ($listagemPendentes as $key => $dados) {

                if (!empty($dados['ValidacaoPPRA']['codigo'])) {
                    $listagemPendentes[$key][0]['FuncionarioNome'] = $dados['Funcionario2']['nome'];
                    $listagemPendentes[$key][0]['CodigoFuncionario'] = $dados['Funcionario2']['codigo'];
                    $listagemPendentes[$key][0]['CodigoClienteAlocacao'] = "";
                } else {
                    $listagemPendentes[$key][0]['FuncionarioNome'] = $dados['Funcionario']['nome'];
                    $listagemPendentes[$key][0]['CodigoFuncionario'] = $dados['AplicacaoExame']['codigo_funcionario'];
                    $listagemPendentes[$key][0]['CodigoClienteAlocacao'] = $dados['AplicacaoExame']['codigo_cliente_alocacao'];
                }

                if ($dados['ValidacaoPPRA']['status_validacao'] == "0" && empty($dados['AplicacaoExame']['codigo_cliente_alocacao'])) {
                    $listagemPendentes[$key][0]['status'] = 1;
                } else if (
                    $dados['ValidacaoPPRA']['status_validacao'] == "0"
                    && empty($dados['AplicacaoExame']['codigo_funcionario']) && !empty($dados['ValidacaoPPRA']['codigo_funcionario'])
                ) {
                    $listagemPendentes[$key][0]['status'] = 1;
                } else {
                    if ($dados['ValidacaoPPRA']['status_validacao'] == "0") {
                        $listagemPendentes[$key][0]['status'] = 3;
                    } else if ($dados['ValidacaoPPRA']['status_validacao'] == "1") {
                        $listagemPendentes[$key][0]['status'] = 2;
                    } else {
                        if (!empty($dados['AplicacaoExame']['codigo_cliente_alocacao'])) {
                            $listagemPendentes[$key][0]['status'] = 2;
                        } else {
                            $listagemPendentes[$key][0]['status'] = 1;
                        }
                    }
                }
            }
        }

        $this->set(compact('controller_link', 'botao_finalizar_processo', 'tipo', 'codigo_cliente', 'listagemPendentes'));
    }

    public function modal_validar_pcmso($codigo_cliente_alocacao, $codigo_cliente, $codigo_setor, $codigo_cargo, $codigo_funcionario = null)
    {
        $this->loadModel('Setor');
        $this->loadModel('Cargo');
        $this->loadModel('Funcionario');
        $this->loadModel('Cliente');
        $this->loadModel('TipoExame');

        if ($codigo_funcionario != 'null') {
            $dados_funcionario = $this->Funcionario->findbyCodigo($codigo_funcionario); //dados funcionario    		
        }

        $dados_setor = $this->Setor->findbyCodigo($codigo_setor); //dados setor
        $dados_cargo = $this->Cargo->findbyCodigo($codigo_cargo); //dados cargo  

        $dados_cliente = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente); //dados cliente
        $this->data['Matriz'] = $dados_cliente['Matriz'];
        $this->data['Unidade'] = $dados_cliente['Unidade'];

        $fields = array(
            'Funcionario.nome',
            'Funcionario.codigo',
            'AplicacaoExame.codigo_cliente_alocacao',
            'AplicacaoExame.codigo',
            'AplicacaoExame.codigo_funcionario',
            'AplicacaoExame.codigo_exame',
            'Exame.descricao',
            'AplicacaoExame.periodo_meses',
            'AplicacaoExame.exame_admissional',
            'AplicacaoExame.exame_demissional',
            'AplicacaoExame.exame_periodico',
            'AplicacaoExame.exame_retorno',
            'AplicacaoExame.exame_mudanca',
            'AplicacaoExame.periodo_apos_demissao',
            'AplicacaoExame.periodo_idade',
            'AplicacaoExame.qtd_periodo_idade',
            'AplicacaoExame.exame_excluido_convocacao',
            'AplicacaoExame.exame_excluido_ppp',
            'AplicacaoExame.exame_excluido_aso',
            'AplicacaoExame.exame_excluido_pcmso',
            'AplicacaoExame.exame_excluido_anual',
            'AplicacaoExame.qualidade_vida',
            'AplicacaoExame.codigo_tipo_exame',
            'AplicacaoExame.pontual',
            'AplicacaoExame.periodo_idade_2',
            'AplicacaoExame.qtd_periodo_idade_2',
            'AplicacaoExame.periodo_idade_3',
            'AplicacaoExame.qtd_periodo_idade_3',
            'AplicacaoExame.periodo_idade_4',
            'AplicacaoExame.qtd_periodo_idade_4',
            'AplicacaoExame.codigo_funcionario',
            'AplicacaoExame.exame_monitoracao',
            'AplicacaoExame.qtd_periodo_idade',
            'ClientesSetoresCargos.codigo_setor',
            'ClientesSetoresCargos.codigo_cargo',
            'TiposExame.codigo',
            'TiposExame.descricao'
        );

        $joins = array(
            array(
                "table"      => "clientes_setores_cargos",
                "alias"      => "ClientesSetoresCargos",
                "conditions" => "GrupoEconomicoCliente.codigo_cliente = ClientesSetoresCargos.codigo_cliente_alocacao"
            ),
            array(
                "table"      => "aplicacao_exames",
                "alias"      => "AplicacaoExame",
                "type"       => "LEFT",
                "conditions" => "ClientesSetoresCargos.codigo_cargo = AplicacaoExame.codigo_cargo and ClientesSetoresCargos.codigo_setor = AplicacaoExame.codigo_setor and ClientesSetoresCargos.codigo_cliente_alocacao = AplicacaoExame.codigo_cliente_alocacao ",
            ),
            array(
                "table"      => "funcionarios",
                "alias"      => "Funcionario",
                "type"       => "LEFT",
                "conditions" => "Funcionario.codigo = AplicacaoExame.codigo_funcionario"
            ),
            array(
                "table"      => "exames",
                "alias"      => "Exame",
                "type"       => "LEFT",
                "conditions" => "Exame.codigo = AplicacaoExame.codigo_exame"
            ),
            array(
                "table"      => "tipos_exames",
                "alias"      => "TiposExame",
                "type"       => "LEFT",
                "conditions" => "TiposExame.codigo = AplicacaoExame.codigo_tipo_exame"
            )
        );

        if ($codigo_funcionario == 'null') {
            $conditions[] = 'AplicacaoExame.codigo_funcionario IS NULL';
            $codigo_funcionario = null;
        } else {
            $conditions[] = 'AplicacaoExame.codigo_funcionario IS NULL OR AplicacaoExame.codigo_funcionario = ' . $codigo_funcionario;
        }

        $conditions = array(
            'AplicacaoExame.codigo_cliente_alocacao' => $codigo_cliente_alocacao,
            'AplicacaoExame.codigo_setor' => $codigo_setor,
            'AplicacaoExame.codigo_cargo' => $codigo_cargo,
            'AplicacaoExame.codigo_funcionario' => $codigo_funcionario
        );

        $field_tipo_exame = array('codigo', 'descricao');
        $order_tipo_exame = 'descricao';
        $tipos_exames = $this->TipoExame->find('list', array('order' => $order_tipo_exame, 'fields' => $field_tipo_exame)); //trazer a descricao do tipo exame

        // debug($this->GrupoEconomicoCliente->find('sql', array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields)));exit;

        $aplicacaoExames = $this->GrupoEconomicoCliente->find('all', array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields)); //achar os exames do funcionario

        // debug($aplicacaoExames);exit;

        $this->set(compact(
            'dados_cliente',
            'dados_setor',
            'dados_cargo',
            'dados_funcionario',
            'aplicacaoExames',
            'tipos_exames',
            'codigo_setor',
            'codigo_cargo',
            'codigo_cliente_alocacao',
            'codigo_funcionario'
        ));
    }

    /**
     * [valida_pcmso description]
     * 
     * metodo para validar o pcmso via ajax
     * 
     * @param  [type] $codigo_cliente_alocacao [description]
     * @param  [type] $codigo_setor            [description]
     * @param  [type] $codigo_cargo            [description]
     * @param  [type] $codigo_funcionario      [description]
     * @return [type]                          [description]
     */
    public function valida_pcmso($codigo_cliente_alocacao, $codigo_setor, $codigo_cargo, $codigo_funcionario)
    {
        $this->autoRender = false;
        $this->pageTitle = 'Validar Pcmso';

        $this->loadModel('ValidacaoPpra');

        $retorno = 1;
        if (!$this->ValidacaoPpra->valida_pcmso($codigo_cliente_alocacao, $codigo_setor, $codigo_cargo, $codigo_funcionario)) {
            $retorno = 0;
        }

        return $retorno;
    } //fim valida_pcmso

    public function carregaCombosDocsPrestador($data = null)
    {

        $tipos_documentos = $this->TipoDocumento->retorna_tipos_documentos();

        $estados = $this->EnderecoEstado->retorna_estados();

        if (isset($this->data['Consulta']['estado']) && $this->data['Consulta']['estado']) {
            $cidades = array('' => 'Selecione o Estado Primeiro') + $this->EnderecoCidade->find('list', array('conditions' => array('codigo_endereco_estado' => $this->data['Consulta']['estado']), 'fields' => array('descricao')));
        } else {
            $cidades = array('' => 'Selecione o Estado Primeiro');
        }

        $situacao = array(
            'VI' => 'Vigentes',
            'P' => 'Pendentes',
            'V' => 'Vencidos',
            'AV' => 'À Vencer'
        );

        if (empty($filtros['Consulta']['data_inicio'])) {
            $filtros['Consulta']['data_inicio'] = date('d/m/Y');
            $filtros['Consulta']['data_fim'] = date('d/m/Y', strtotime('+ 30 days'));
        }

        $this->set(compact('tipos_documentos', 'estados', 'cidades', 'situacao'));
    }

    public function get_fornecedores($codigo_fornecedor)
    {
        $this->autoLayout = false;
        $this->autoRender = false;

        $result = $this->Fornecedor->carregarParaEdicao($codigo_fornecedor);

        $retorno = new stdClass();
        $retorno->sucesso = false;

        if ($result) {
            $retorno->sucesso = true;
            $retorno->dados = $result['Fornecedor'];
        }

        return $this->responseJson($retorno);
    }

    public function exportDocsPrestadores($query)
    {
        //executa a query
        $dbo = $this->Fornecedor->getDataSource();
        $dbo->results   = $dbo->rawQuery($query);

        ob_clean(); //limpa o cache dos dados

        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="documentos_prestador_' . date('YmdHis') . '.csv"');
        header('Pragma: no-cache');

        echo utf8_decode('"Código do Fornecedor";"Razão Social";"Nome Fantasia";"CNPJ";"Telefone";"E-mail";"Estado";"Cidade";"Situação";"Documento";"Data de validade";') . "\n";

        //varre os dados para montar a planilha
        while ($value = $dbo->fetchRow()) {
            //monta os dados
            $linha  = $value['Fornecedor']['codigo'] . ';';
            $linha .= $value['Fornecedor']['razao_social'] . ';';
            $linha .= $value['Fornecedor']['nome'] . ';';
            $linha .= $value[0]['cnpj'] . ';';
            $linha .= Comum::formatarTelefone($value[0]['telefone']) . ';';
            $linha .= $value[0]['email'] . ';';
            $linha .= $value['FornecedorEndereco']['estado_descricao'] . ';';
            $linha .= $value['FornecedorEndereco']['cidade'] . ';';
            $linha .= $value[0]['status'] . ';';
            $linha .= $value[0]['Fornecedor__documento'] . ';';

            if (!is_null($value[0]['Fornecedor__data_validade'])) {
                $linha .= AppModel::formataData($value[0]['Fornecedor__data_validade']) . ';';
            } else {
                $linha .= '"' . '' . '";';
            }

            echo utf8_decode($linha) . "\n";
        } //fim loop

        //finaliza o metodo
        die();
    }
}
