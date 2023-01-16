<?php
class TransacoesDeRecebimentoController extends AppController {

    public $name = 'TransacoesDeRecebimento';
    public $layout = 'cliente';
    public $uses = array('Tranrec','LojaNaveg', 'Cliente');
    public $components = array('AutorizacoesFiltros');  

    function prazo_medio_recebimento() {
        $this->pageTitle = 'Prazo Médio Emissão e Pagamento';
        if($this->RequestHandler->isPost()) {
            $data = $this->data['Tranrec'];
            $dados = $this->Tranrec->prazoMedioRecebimento($data);

            if (!empty($data['codigo_cliente']))
                $cliente = $this->Cliente->find('first', array('fields'=>array('Cliente.razao_social','Cliente.codigo'), 'conditions'=>'Cliente.codigo = '.$data['codigo_cliente']));
            if (!empty($this->data['Tranrec']))
                $empresa = $this->LojaNaveg->carregar($this->data['Tranrec']['empresa']);
        } else {
            $this->data['Tranrec']['ano'] = Date('Y');
            $this->data['Tranrec']['grupo_empresa'] = 1;
        }
        $anos = Comum::listAnos();
        $empresas = $this->LojaNaveg->listEmpresas($this->data['Tranrec']['grupo_empresa']);
        $grupos_empresas = $this->LojaNaveg->listGrupos();
        $nome_grupo = $this->LojaNaveg->nomeGrupoPorId( $this->data['Tranrec']['grupo_empresa'] );
        
        $this->set(compact('dados','cliente', 'anos', 'grupos_empresas', 'empresas', 'empresa', 'nome_grupo'));     
    }

    function estatisca_inadimplentes(){
        $this->loadModel('Notafis');
        $this->pageTitle    = 'Estatística Inadimplência ';
        $data               = null;
        $grupos_empresas    = null ;
        $this->data['Tranrec'] = $this->Filtros->controla_sessao($this->data, 'Tranrec');
                
        if($this->RequestHandler->isPost()){
            $this->Filtros->limpa_sessao('Alerta');
            $data   = $this->data['Tranrec'];
            $dados  = $this->Tranrec->listaEstatiscaInadimplentes($data);
            $vlmerc = $this->Notafis->listaFaturamentoTotal($data);
                         
            foreach ( $dados as $key => $valor) {
                $dados[$key][0]['ano_mes']          = $vlmerc[$key][0]['ano_mes'];
                $dados[$key][0]['valor_total_merc'] = $vlmerc[$key][0]['valor_total_merc'];
            } 
             
            if (!empty($data['empresa']))
                $empresa = $this->LojaNaveg->carregar($data['empresa']);
                $this->set(compact('empresa'));
        }
        
        $ano_atual          = Date('Y');  
        $anos               = Comum::listAnos();
        $ano                = $data['ano'];
        $nome_grupo         = $this->LojaNaveg->nomeGrupoPorId( $data['grupo_empresa']);
        $grupos_empresas    = $this->LojaNaveg->listGrupos();
        $empresas           = $this->LojaNaveg->listEmpresas($data['grupo_empresa']);
        $this->set(compact('anos','grupos_empresas','dados','empresas','nome_grupo','ano','vlmerc','ano_atual'));
    }

    function total_titulos_clientes($mes_ano=null,$page=null){
        $this->layout       = 'new_window';
        
        $this->pageTitle    = FALSE;        
        $mes_ano            = str_replace("-","/",$mes_ano);
        $filtros            = $this->Filtros->controla_sessao($this->data, 'Tranrec');
        $filtros['ano_mes'] = $mes_ano;
        $empresa            = $this->LojaNaveg->carregar($filtros['empresa']);
        $nome_grupo         = $this->LojaNaveg->nomeGrupoPorId($filtros['grupo_empresa']);
        $dados_lista        = $this->Tranrec->listaTotalTitulosClientes($filtros);
        $total_valor        = 0;
        $total_titulos      = 0;
        $conditions         = $this->Tranrec->conditionsTotalTitulosClientes($filtros);
        $joins              = $this->Tranrec->joinsTotalTitulosClientes($filtros);
        $fields             = $this->Tranrec->fieldsTotalTitulosClientes();
        
        $this->paginate['Tranrec'] = array(
            'conditions' => $conditions, 
            'extra'      => array(
                'joins'=>$joins,
                'group'=>array(
                    'NCliente.razaosocia',
                    'notafis.cliente',
                    'SUBSTRING(CONVERT(VARCHAR,Tranrec.dtemiss, 103), 4, 7)',                   
                ),
                'countTitulosTotalCliente' => true
            ),
           'fields' => $fields,
           'order'  => 'valor_total DESC',
           'limit'  => 50,
        );
                
        $dados = $this->paginate('Tranrec');
        
        foreach ($dados_lista as $key => $valor) {
            $total_valor    = $total_valor + $valor[0]['valor_total'];
            $total_titulos  = $total_titulos + $valor[0]['Titulos'] ;
        }
        
        
        
        $this->set(compact('dados','empresa','nome_grupo','total_titulos','total_valor'));
    }

    function titulos_clientes($codigo=null,$mes_ano=null){
        $this->layout       = 'new_window';
        $this->pageTitle    = False;
        $mes_ano            = str_replace("-","/",$mes_ano);
        $filtros            = $this->Filtros->controla_sessao($this->data, 'Tranrec');
        $filtros['ano_mes'] = $mes_ano;
        $filtros['codigo']  = $codigo;
        $empresa            = $this->LojaNaveg->carregar($filtros['empresa']);
        $nome_grupo         = $this->LojaNaveg->nomeGrupoPorId($filtros['grupo_empresa']);
        $dados_lista        = $this->Tranrec->listaTitulosClientes($filtros);
        $joins              = $this->Tranrec->joinsTitulosClientes($filtros);
        $conditions         = $this->Tranrec->conditionsTitulosClientes($filtros);
        $fields             = $this->Tranrec->fieldsTitulosClientes();
        $total_valor        = 0;
        $total_titulos      = 0;
        $i = 01;
        
        foreach ($dados_lista as $key => $valor) {
            $total_valor    = $total_valor + $valor[0]['valor'];
            $total_titulos  = $i++ ;
        }
        
        $this->paginate['Tranrec'] = array(
            'conditions' => $conditions, 
            'extra'      => array(
            'joins'=>$joins,
            'group'=>array(
                    'SUBSTRING(CONVERT(VARCHAR,Tranrec.dtemiss, 103), 4, 7)',
                    'Tranrec.dtemiss',
                    'Tranrec.dtvencto',
                    'Tranrec.ordem',
                    'Notafis.cliente',
                    'Tranrec.empresa',
                    'Tranrec.numero',
                    'Tranrec.razao',
                    'Tranrec.seq',
                    'Tranrec.valor',
                    'Tranrec.dtvencto',
                    'Tranrec.obs',
                )
            ),
            'countTitulosCliente' => true,
            'fields' => $fields,
            'order'  => 'dias_venc DESC',
            'limit'  => 50,
        );

        $dados = $this->paginate('Tranrec'); 
        $this->set(compact('dados','empresa','nome_grupo','total_titulos','total_valor'));
    }

    public function comissoes_analitico() {
        $this->layout = 'new_window';
        $this->pageTitle = 'Analítico de Comissões';
        $this->carregarCombos();
        $this->data['Tranrec'] = $this->Filtros->controla_sessao($this->data, 'Tranrec');
        $this->AutorizacoesFiltros->defineVisualizacaoFiltroConfiguracao();
    }

    public function comissoes_analitico_listagem($export = false) {
        $this->data['Tranrec'] = $this->Filtros->controla_sessao($this->data, 'Tranrec');
        if (empty($this->data['Tranrec']['mes_faturamento']) || empty($this->data['Tranrec']['ano_faturamento'])) {
            $conditions = array('Tranrec.empresa' => null);
        } else {
            $conditions = $this->Tranrec->converteFiltrosEmConditions($this->data['Tranrec']);
        }
        $conditions['NotafisComplemento.numero >='] = 531079; // Mudança de conceito. RPS anteriores já foram pagas

        $order = 'dtpagto';
        if($export){
            $query = $this->Tranrec->analiticoComissoes('sql',compact('conditions','order'));
            $totais = $this->Tranrec->sinteticoComissoes($conditions, true);
            $this->exportAnaliticoListagem($query,$totais);
        }

        $this->paginate['Tranrec'] = array(
            'conditions' => $conditions,
            'limit' => 200,
            'order' => $order,
            'method' => 'comissoes',
        );
        $totais = $this->Tranrec->sinteticoComissoes($conditions, true);
        $dados = $this->paginate('Tranrec');
        $this->set(compact('dados', 'totais'));
    }

    public function comissoes_por_corretora_analitico() {
        $this->layout = 'new_window';
        $this->pageTitle = 'Analítico de Comissões por Corretora';
        $this->carregarCombosPorCorretora();
        $this->data['Tranrec'] = $this->Filtros->controla_sessao($this->data, 'Tranrec');
        $this->AutorizacoesFiltros->defineVisualizacaoFiltroConfiguracao();
    }

    public function comissoes_por_corretora_analitico_listagem($export = false) {
        $this->data['Tranrec'] = $this->Filtros->controla_sessao($this->data, 'Tranrec');
        if (empty($this->data['Tranrec']['mes_faturamento']) || empty($this->data['Tranrec']['ano_faturamento'])) {
            $conditions = array('Tranrec.empresa' => null);
        } else {
            $conditions = $this->Tranrec->converteFiltrosEmConditionsPorCorretora($this->data['Tranrec']);
        }

        $order = 'cliente_nome ASC, valor_comissao DESC';

        if($export){
            $query = $this->Tranrec->analiticoComissoesPorCorretoraAgrupado($conditions,null,$order,1,true);
            $totais = $this->Tranrec->sinteticoComissoesPorCorretora($conditions,null,null,1,true);
            $this->exportAnaliticoPorCorretoraListagem($query,$totais);
        }

        $this->paginate['Tranrec'] = array(
            'conditions' => $conditions,
            'method'=>'comissoes_por_corretora_analitico',
            'limit' => 50,
            'order' => $order,
            'page' => 1, 
            'recursive' => 1, 
        );

        $dados = $this->paginate('Tranrec');

        $totais = $this->Tranrec->sinteticoComissoesPorCorretora($conditions,null,null,1,true);
        
        $this->set(compact('dados','totais'));
    }

    public function carregarCombos(){
        $this->loadModel('EnderecoRegiao');
        $this->loadModel('Corretora');
        $this->loadModel('Seguradora');
        $this->loadModel('Gestor');
        $meses = Comum::listMeses();
        $mes_atual = Date('m');
        $anos = Comum::listAnos();
        $ano_atual = Date('Y'); 
        $regioes = $this->EnderecoRegiao->listarRegioes();
        $corretoras = $this->Corretora->listarCorretorasAtivas();
        $seguradoras = $this->Seguradora->listarSeguradorasAtivas();
        $gestores = $this->Gestor->listarNomesGestoresAtivos();

        $this->set(compact('regioes','corretoras','seguradoras','gestores','meses','anos','ano_atual','mes_atual'));
    }

    public function carregarCombosPorCorretora(){
        $this->loadModel('Corretora');
        $this->loadModel('Produto');
        $this->loadModel('ProdutoServico');
        $meses = Comum::listMeses();
        $mes_atual = Date('m');
        $anos = Comum::listAnos();
        $ano_atual = Date('Y'); 
        $corretoras = $this->Corretora->listarCorretorasAtivas();
        $produtos = $this->Produto->listar('list',array('codigo_naveg IS NOT NULL'),'descricao ASC');
        $servicos = array();
        if(isset($this->data['Tranrec']['codigo_produto'])){
            $servicos = $this->ProdutoServico->servicosPorProduto($this->data['Tranrec']['codigo_produto']);
        }
        $this->set(compact('corretoras','meses','anos','ano_atual','mes_atual','produtos','servicos'));
    }

    public function comissoes_sintetico() {
        $this->pageTitle = 'Sintético de Comissões';
        $this->carregarCombos();
        $this->data['Tranrec'] = $this->Filtros->controla_sessao($this->data, 'Tranrec');
        $this->AutorizacoesFiltros->defineVisualizacaoFiltroConfiguracao();
    }

    public function comissoes_sintetico_listagem() {
        $this->data['Tranrec'] = $this->Filtros->controla_sessao($this->data, 'Tranrec');
        if (empty($this->data['Tranrec']['mes_faturamento']) || empty($this->data['Tranrec']['ano_faturamento'])) {
            $conditions = array('Tranrec.empresa' => null);
        } else {
            if (!empty($authUsuario['Usuario']['codigo_filial'])) {
                $this->data['Tranrec']['codigo_endereco_regiao'] = $authUsuario['Usuario']['codigo_filial'];
            }
            $conditions = $this->Tranrec->converteFiltrosEmConditions($this->data['Tranrec']);
        }
        $conditions['NotafisComplemento.numero >='] = 531079; // Mudança de conceito. RPS anteriores já foram pagas
        $this->set('dados', $this->Tranrec->sinteticoComissoes($conditions));
    }

    public function comissoes_por_corretora_sintetico() {
        $this->pageTitle = 'Sintético de Comissões por Corretora';
        $this->carregarCombosPorCorretora();
        $this->data['Tranrec'] = $this->Filtros->controla_sessao($this->data, 'Tranrec');
        $this->AutorizacoesFiltros->defineVisualizacaoFiltroConfiguracao();
        $is_post = $this->RequestHandler->isPost();
        $this->set(compact('is_post'));
    }

    public function comissoes_por_corretora_sintetico_listagem() {
        $this->data['Tranrec'] = $this->Filtros->controla_sessao($this->data, 'Tranrec');
        
        if (empty($this->data['Tranrec']['mes_faturamento']) || empty($this->data['Tranrec']['ano_faturamento'])) {
            $conditions = array('Tranrec.empresa' => null);
        } else {
            $conditions = $this->Tranrec->converteFiltrosEmConditionsPorCorretora($this->data['Tranrec']);
        }

        $this->paginate['Tranrec'] = array(
            'conditions' => $conditions,//array('Tranrec.seq' => '02'), 
            'method'=>'comissoes_por_corretora_sintetico',
            'limit' => 50,
            'order' => 'valor_comissao DESC, valor_servico DESC, corretora_nome ASC',
            'page' => 1, 
            'recursive' => 1, 
        );
        $dados = $this->paginate('Tranrec');

        $totais = $this->Tranrec->sinteticoComissoesPorCorretora($conditions,null,null,1,true);

        $this->set(compact('dados','totais'));
    }

    function exportAnaliticoPorCorretoraListagem($query,$totais){
        $dbo = $this->Tranrec->getDataSource();
        $dbo->results = $dbo->_execute($query);
        header('Content-type: application/vnd.ms-excel');
        header(sprintf('Content-Disposition: attachment; filename="%s"', basename('comissoes_analitica_por_corretora.csv')));
        header('Pragma: no-cache');
        echo iconv('UTF-8', 'ISO-8859-1', '"Código";"Cliente";"Valor Unitário";"Quantidade";"Valor";"Impostos (%)";"Valor Líquido";"Comissão (%)";"Valor Comissão";"De";"Até";"Produto";"Serviço";')."\n";
        while ($dado = $dbo->fetchRow()) {
            $linha  = '"'.$dado[0]['cliente_codigo'].'";'; 
            $linha .= '"'.$dado[0]['cliente_nome'].'";';
            $linha .= '"'.number_format($dado[0]['valor_unitario'], 2).'";';
            $linha .= '"'.$dado[0]['quantidade'].'";'; 
            $linha .= '"'.number_format($dado[0]['valor_servico'], 2).'";'; 
            $linha .= '"'.$dado[0]['percentual_impostos'].'";'; 
            $linha .= '"'.number_format($dado[0]['valor_servico_liquido'], 2).'";'; 
            $linha .= '"'.$dado[0]['percentual_comissao'].'";'; 
            $linha .= '"'.number_format($dado[0]['valor_comissao'], 2).'";'; 
            $linha .= '"'.number_format($dado[0]['preco_de'], 2).'";'; 
            $linha .= '"'.number_format($dado[0]['preco_ate'], 2).'";'; 
            $linha .= '"'.$dado[0]['produto_nome'].'";'; 
            $linha .= '"'.$dado[0]['servico_nome'].'";'; 
            $linha .= "\n";
            echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
        }
        $linha  = '"";'; 
        $linha .= '"";';
        $linha .= '"";';
        $linha .= '"";'; 
        $linha .= '"'.number_format($totais[0][0]['valor_servico'], 2).'";'; 
        $linha .= '"";'; 
        $linha .= '"'.number_format($totais[0][0]['valor_servico_liquido'], 2).'";'; 
        $linha .= '"";'; 
        $linha .= '"'.number_format($totais[0][0]['valor_comissao'], 2).'";'; 
        $linha .= '"";'; 
        $linha .= '"";'; 
        $linha .= '"";'; 
        $linha .= '"";'; 
        $linha .= "\n";
        echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
        die();
    }

    function exportAnaliticoListagem($query,$totais){
        $dbo = $this->Tranrec->getDataSource();
        $dbo->results = $dbo->_execute($query);
        header('Content-type: application/vnd.ms-excel');
        header(sprintf('Content-Disposition: attachment; filename="%s"', basename('comissoes_analitica_por_filial.csv')));
        header('Pragma: no-cache');
        echo iconv('UTF-8', 'ISO-8859-1', '"Código";"Cliente";"Documento";"Valor";"%";"Comissão";"Tipo Faturamento";"Produto";"Filial";"Gestor";"Corretora";"Seguradora";')."\n";
        while ($dado = $dbo->fetchRow()) {
            $linha  = '"'.$dado[0]['cliente_codigo'].'";'; 
            $linha .= '"'.$dado[0]['cliente_nome'].'";';
            $linha .= '"'.$dado[0]['numero'].'";';
            $linha .= '"'.number_format($dado[0]['vlmerc'], 2).'";';
            $linha .= '"'.number_format($dado[0]['percentual'], 2).'";';
            $linha .= '"'.number_format($dado[0]['percentual'] / 100 * $dado[0]['vlmerc'], 2).'";';
            $linha .= '"'.($dado[0]['tipo_faturamento'] == 1 ? 'Total' : 'Parcial').'";';
            $linha .= '"'.$dado[0]['produto_nome'].'";';
            $linha .= '"'.$dado[0]['filial_nome'].'";';
            $linha .= '"'.$dado[0]['gestor_nome'].'";';
            $linha .= '"'.$dado[0]['corretora_nome'].'";';
            $linha .= '"'.$dado[0]['seguradora_nome'].'";'; 
            $linha .= "\n";
            echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
        }
        $linha  = '"";'; 
        $linha .= '"";';
        $linha .= '"";';
        $linha .= '"'.number_format($totais[0][0]['valor'], 2).'";'; 
        $linha .= '"";'; 
        $linha .= '"'.number_format($totais[0][0]['valor_comissao'], 2).'";'; 
        $linha .= '"";'; 
        $linha .= '"";'; 
        $linha .= '"";'; 
        $linha .= '"";'; 
        $linha .= '"";'; 
        $linha .= '"";'; 
        $linha .= '"";'; 
        $linha .= "\n";
        echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
        die();
    }

    function analitico() {
        $this->layout = 'new_window';
        $this->pageTitle = 'Recebíveis Analítico';
        $this->loadModel('Corretora');
        $this->loadModel('EnderecoRegiao');
        $this->loadModel('Seguradora');
        $this->data['Tranrec'] = $this->Filtros->controla_sessao($this->data, 'Tranrec');
        if (!empty($this->data['Tranrec']['codigo_corretora'])) {
            $corretora = $this->Corretora->read('nome', $this->data['Tranrec']['codigo_corretora']);
            $this->data['Tranrec']['codigo_corretora_visual'] = $corretora['Corretora']['nome'];
        }
        $seguradoras = $this->Seguradora->listarSeguradorasAtivas();
        $filiais = $this->EnderecoRegiao->listarRegioes();
        $tranrec_status = $this->Tranrec->listStatus();
        $this->set(compact('seguradoras', 'filiais', 'tranrec_status'));
    }

    function analitico_listagem() {
        $this->data['Tranrec'] = $this->Filtros->controla_sessao($this->data, 'Tranrec');
        $this->data['Tranrec']['seq'] = '01';
        unset($this->data['Tranrec']['configuracao_comissao']);
        $conditions = $this->Tranrec->converteFiltrosEmConditions($this->data['Tranrec']);
        $fields = array(
            'Cliente.codigo',
            'Cliente.razao_social',
            'Tranrec.valor',
            'Tranrec.dtemiss',
            'Tranrec.dtvencto',
            "(SELECT TOP 1 CONVERT(VARCHAR,pagamento.dtpagto,120) FROM Navegarq.dbo.tranrec AS pagamento WHERE pagamento.numero = Tranrec.numero AND pagamento.seq = '02') AS dtpgto",
            'Seguradora.nome',
            'EnderecoRegiao.descricao',
            'Corretora.nome',
            'Notafis.numero',
            'Gernfe.numnfe',
        );
        $limit = 50;
        $method = 'analitico';
        $this->paginate['Tranrec'] = compact('conditions','fields','limit','method');
        $titulos_a_receber = $this->paginate('Tranrec');
        $totais = $this->Tranrec->totais($conditions);
        $this->set(compact('titulos_a_receber', 'totais'));
    }

    function sintetico() {
        $this->pageTitle = 'Recebíveis Sintético';
        $this->loadModel('EnderecoRegiao');
        $this->loadModel('Seguradora');
        $this->data['Tranrec'] = $this->Filtros->controla_sessao($this->data, 'Tranrec');
        $seguradoras = $this->Seguradora->listarSeguradorasAtivas();
        $filiais = $this->EnderecoRegiao->listarRegioes();
        $tranrec_status = $this->Tranrec->listStatus();
        $agrupamento = $this->Tranrec->listAgrupamentos();
        $this->set(compact('seguradoras', 'filiais', 'tranrec_status', 'agrupamento'));
    }

    function sintetico_listagem() {
        $this->data['Tranrec'] = $this->Filtros->controla_sessao($this->data, 'Tranrec');
        $this->data['Tranrec']['seq'] = '01';
        unset($this->data['Tranrec']['configuracao_comissao']);
        $conditions = $this->Tranrec->converteFiltrosEmConditions($this->data['Tranrec']);
        $titulos_a_receber = $this->Tranrec->sintetico($conditions, $this->data['Tranrec']['agrupamento']);
        $this->set(compact('titulos_a_receber'));
    }
} 