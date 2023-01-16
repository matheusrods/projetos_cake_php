<?php
class ItensNotasFiscaisController extends AppController {
    public $name = 'ItensNotasFiscais';
    public $helpers = array('Highcharts');
    var $uses = array('Notaite', 'Cliente', 'NProduto', 'LojaNaveg');

    function por_produto() {
        if(!empty($this->data['Notaite']['data_final'])){
            $mespesquisa = explode("/",$this->data['Notaite']['data_final']);
            $anopesquisa = (int)$mespesquisa[2];
            $mespesquisa = (int)$mespesquisa[1];            
        }else{
            $mespesquisa = date('m');
            $anopesquisa = date('Y');
        }
        unset($this->data['Notaite']['data_inicial']);
        unset($this->data['Notaite']['data_final']);
        $this->data['Notaite']['mes'] = $mespesquisa;
        $this->data['Notaite']['ano'] = $anopesquisa;
        $this->data['Notaite']['level'] = 0;
        $this->data['Notaite']['agrupamento'] = Notaite::AGRP_PRODUTOS;
        $this->Filtros->controla_sessao($this->data, 'Notaite');
        $this->redirect(array('action' => 'ranking_faturamento'));
    }

    function por_produto_solen() {
        if(!empty($this->data['Notaite']['data_final'])){
            $mespesquisa = explode("/",$this->data['Notaite']['data_final']);
            $anopesquisa = (int)$mespesquisa[2];
            $mespesquisa = (int)$mespesquisa[1];
        }else{
            $mespesquisa = date('m');
            $anopesquisa = date('Y');
        }
        $this->data['Notaite']['mes'] = $mespesquisa;
        $this->data['Notaite']['ano'] = $anopesquisa;
        $this->data['Notaite']['level'] = 0;
        $this->data['Notaite']['agrupamento'] = Notaite::AGRP_PRODUTOS;
        $this->Filtros->controla_sessao($this->data, 'Notaite');
        $this->redirect(array('action' => 'ranking_faturamento_solen'));
    }

    function comparativo_anual($new_window = false) {
        if ($new_window) {
            $this->layout = 'new_window';
        }
        $anos = Comum::listAnos();
        $grupos_empresas = $this->LojaNaveg->listGrupos();        
        $this->pageTitle = 'Comparativo Anual';
        if($this->RequestHandler->isPost()) {
            $filtros = $this->data;
            $dados2 = $this->Notaite->faturamentoAnual($filtros);
            $filtros['Notaite']['ano']--;
            $dados = $this->Notaite->faturamentoAnual($filtros);
            if (!empty($this->data['Notaite']['empresa']))
                $empresa = $this->LojaNaveg->find('first', array('fields' => array('codigo', 'razaosocia'), 'conditions' => array("codigo = '{$filtros['Notaite']['empresa']}'")));
            if (!empty($this->data['Notaite']['codigo_produto']))
                $produto = $this->NProduto->find('first', array('fields' => array('codigo', 'descricao'), 'conditions' => array("codigo = '{$filtros['Notaite']['codigo_produto']}'")));
            if (!empty($this->data['Notaite']['codigo_cliente']))
                $cliente = $this->Cliente->carregar($this->data['Notaite']['codigo_cliente']);

            if (isset($this->data['Notaite']['codigo_gestor'])) {
                $Gestor = ClassRegistry::init('Gestor');
                $this->set( 'gestor', $Gestor->carregar( $this->data['Notaite']['codigo_gestor'] ) );
            } else if (isset($this->data['Notaite']['codigo_corretora'])) {
                $Corretora = ClassRegistry::init('Corretora');
                $this->set( 'corretora', $Corretora->carregar( $this->data['Notaite']['codigo_corretora'] ) );
            } else if (isset($this->data['Notaite']['codigo_seguradora'])) {
                $Seguradora = ClassRegistry::init('Seguradora');
                $this->set( 'seguradora', $Seguradora->carregar( $this->data['Notaite']['codigo_seguradora'] ) );
            }
        } else {
            $this->data['Notaite']['ano'] = Date('Y');
            $this->data['Notaite']['grupo_empresa'] = LojaNaveg::GRUPO_BUONNY;
        }
        $usuario = $this->BAuth->user();
        if ( $usuario['Usuario']['codigo_uperfil'] == 148 ){
            $grupo_empresa   = LojaNaveg::GRUPO_SOLEN;
            $grupos_empresas = array($grupo_empresa => $grupos_empresas[$grupo_empresa] );
            $this->data['Notaite']['grupo_empresa'] = $grupo_empresa;
        }
        $empresas = $this->LojaNaveg->listEmpresas($this->data['Notaite']['grupo_empresa']);
        $nome_grupo = $this->LojaNaveg->nomeGrupoPorId( $this->data['Notaite']['grupo_empresa'] );
        $this->set(compact('dados','dados2','cliente', 'anos', 'produto', 'empresas', 'grupos_empresas', 'nome_grupo'));
    }

    function por_empresa() {
        $this->pageTitle = 'Faturamento por Empresa';
        if (!empty($this->data)) {
            $mes = str_pad($this->data['Notaite']['mes'], 2, '0', STR_PAD_LEFT);
            $ano = $this->data['Notaite']['ano'];
            $dias_do_mes = str_pad(date("t", mktime(1, 1, 1, $mes, 1, $ano)), 2, '0', STR_PAD_LEFT);
            $filtros = $this->data;
            unset($filtros['Notaite']['ano']);
            $filtros['Notaite']['data_inicial'] = '01/'.$mes.'/'.$ano;
            $filtros['Notaite']['data_final'] = $dias_do_mes.'/'.$mes.'/'.$ano;
            $natec_do_mes = $this->_por_empresa_prepara_dados($filtros, LojaNaveg::GRUPO_NATEC);
            $filtros = $this->data;
            unset($filtros['Notaite']['mes']);
            $natec_do_ano = $this->_por_empresa_prepara_dados($filtros, LojaNaveg::GRUPO_NATEC);

            $empresas = $this->LojaNaveg->listEmpresas(0);
        } else {
            $this->data['Notaite']['mes'] = Date('m');
            $this->data['Notaite']['ano'] = Date('Y');
        }
        $meses = Comum::listMeses();
        $anos = Comum::listAnos();
        $usuario = $this->BAuth->user();
        if ( $usuario['Usuario']['codigo_uperfil'] == 148 ){
            $buonny_do_mes = null;
            $lider_do_mes  = null;
            $natec_do_mes  = null;
            $buonny_do_ano = null;
            $lider_do_ano  = null;
            $natec_do_ano  = null;
        }
        $this->set(compact('loja', 'meses', 'anos', 'empresas', 'natec_do_mes', 'natec_do_ano'));            
    }

    function por_empresa_solen() {
        $this->pageTitle = 'Faturamento por Empresa Solen';
        if (!empty($this->data)) {
            $mes = str_pad($this->data['Notaite']['mes'], 2, '0', STR_PAD_LEFT);
            $ano = $this->data['Notaite']['ano'];
            $dias_do_mes = str_pad(date("t", mktime(1, 1, 1, $mes, 1, $ano)), 2, '0', STR_PAD_LEFT);
            $filtros = $this->data;
            unset($filtros['Notaite']['ano']);
            $filtros['Notaite']['data_inicial'] = '01/'.$mes.'/'.$ano;
            $filtros['Notaite']['data_final'] = $dias_do_mes.'/'.$mes.'/'.$ano;

            $solen_do_mes = $this->_por_empresa_prepara_dados($filtros, LojaNaveg::GRUPO_SOLEN);
            $filtros = $this->data;
            unset($filtros['Notaite']['mes']);
            $solen_do_ano = $this->_por_empresa_prepara_dados($filtros, LojaNaveg::GRUPO_SOLEN);

            $empresas = $this->LojaNaveg->listEmpresas(0);
        } else {
            $this->data['Notaite']['mes'] = Date('m');
            $this->data['Notaite']['ano'] = Date('Y');
        }
        $meses = Comum::listMeses();
        $anos = Comum::listAnos();
        $this->set(compact('loja', 'meses', 'anos', 'empresas', 'buonny_do_mes', 'buonny_do_ano', 'lider_do_mes', 'lider_do_ano', 'natec_do_mes', 'natec_do_ano','solen_do_mes','solen_do_ano'));
    }

    private function _por_empresa_prepara_dados($filtros, $grupo_empresa) {
        $this->Notaite->grupo_empresa = $grupo_empresa;
        $dados = $this->Notaite->porLoja($filtros, $grupo_empresa);
        $tratados = array();
        foreach($dados as $dado)
            $tratados[$dado['Notaite']['empresa']] = $dado;
        return $tratados;
    }

    function gg_faturamento_por_mes() {
        $filtros = urldecode(Comum::descriptografarLink($this->data['Cliente']['hash']));
        $filtros = explode('|', $filtros);
        if ($filtros[2])
            $filtros[1] = $this->Cliente->codigosMesmaBaseCNPJ($filtros[1]);
        $filtros = array('Notaite' => array('ano' => $filtros[0], 'codigo_cliente' => $filtros[1]));
        $eixo_x = array();
        foreach (Comum::listMeses() as $mes)
            $eixo_x[] = "'".substr($mes,0,3)."'";
        $meses = $this->Notaite->faturamentoAnual($filtros);
        $series = array();
        $series[] = array('name' => "'" . $filtros['Notaite']['ano'] . "'");
        foreach($meses as $mes) {
            $series[count($series)-1]['values'][] = $mes[0]['preco'];
        }

        $filtros['Notaite']['ano'] = ((int)$filtros['Notaite']['ano'] - 1);
        $meses = $this->Notaite->faturamentoAnual($filtros);
        $series[] = array('name' => "'" . $filtros['Notaite']['ano'] . "'");
        foreach($meses as $mes) {
            $series[count($series)-1]['values'][] = $mes[0]['preco'];
        }
        $this->set(compact('eixo_x', 'series'));
    }

    function gg_faturamento_por_mes_seguradora_corretora() {
        $this->loadModel('Seguradora');
        $filtros = urldecode(Comum::descriptografarLink($this->data['Seguradora']['hash']));
        $filtros = explode('|', $filtros);

        $filtros = array('Notaite' => array(
            'ano' => $filtros[0],
            'codigo_seguradora' => $filtros[1],
            'codigo_corretora' => $filtros[2],
        ));
        $eixo_x = array();
        foreach (Comum::listMeses() as $mes)
            $eixo_x[] = "'".substr($mes,0,3)."'";
        $meses = $this->Notaite->faturamentoAnual($filtros);
        $series = array();
        $series[] = array('name' => "'" . $filtros['Notaite']['ano'] . "'");
        foreach($meses as $mes) {
            $series[count($series)-1]['values'][] = $mes[0]['preco'];
        }

        $filtros['Notaite']['ano'] = ((int)$filtros['Notaite']['ano'] - 1);
        $meses = $this->Notaite->faturamentoAnual($filtros);
        $series[] = array('name' => "'" . $filtros['Notaite']['ano'] . "'");
        foreach($meses as $mes) {
            $series[count($series)-1]['values'][] = $mes[0]['preco'];
        }
        $this->set(compact('eixo_x', 'series'));
    }

    function gg_faturamento_produtos() {
        $filtros = urldecode(Comum::descriptografarLink($this->data['Cliente']['hash']));
        $filtros = explode('|', $filtros);
        if ($filtros[2])
            $filtros[1] = $this->Cliente->codigosMesmaBaseCNPJ($filtros[1]);
        $filtros = array('Notaite' => array('ano' => $filtros[0], 'codigo_cliente' => $filtros[1]));
        $eixo_x = array();
        foreach (Comum::listMeses() as $mes)
            $eixo_x[] = "'".substr($mes,0,3)."'";
        $series = array();
        $codigos_faturados = $this->Notaite->produtosFaturadosPorCliente($filtros['Notaite']['codigo_cliente']);
        $conditions = null;
        $produtos = null;
        if ($codigos_faturados) {
            foreach ($codigos_faturados as $key => $codigo) $codigos_faturados[$key] = "'".$codigo."'";
            $conditions = array('NProduto.codigo IN ('.implode(',', $codigos_faturados).')');
        }
        if ($conditions)
            $produtos = $this->NProduto->find('list', array('conditions' => $conditions));

        if ($produtos) {
            foreach ($produtos as $codigo_produto => $produto) {
                $filtros['Notaite']['codigo_produto'] = $codigo_produto;
                $meses = $this->Notaite->faturamentoAnual($filtros);
                $series[] = array('name' => "'" . str_replace("'", "\'", $produto) . "'");
                foreach($meses as $mes) {
                    $series[count($series)-1]['values'][] = $mes[0]['preco'];
                }
            }
        }
        $this->set(compact('eixo_x', 'series'));
    }

     function gg_faturamento_produtos_seguradora_corretora() {
        $this->loadModel('Seguradora');
        $filtros = urldecode(Comum::descriptografarLink($this->data['Seguradora']['hash']));
        $filtros = explode('|', $filtros);
        $filtros = array('Notaite' => array(
            'ano' => $filtros[0],
            'codigo_seguradora' => $filtros[1],
            'codigo_corretora' => $filtros[2],
        ));
        $eixo_x = array();
        foreach (Comum::listMeses() as $mes)
            $eixo_x[] = "'".substr($mes,0,3)."'";
        $series = array();
        $codigos_faturados = $this->Notaite->produtosFaturadosPorSeguradoraCorretora($filtros['Notaite']['codigo_seguradora'],$filtros['Notaite']['codigo_corretora']);
        $conditions = null;
        $produtos = null;
        if ($codigos_faturados) {
            foreach ($codigos_faturados as $key => $codigo) $codigos_faturados[$key] = "'".$codigo."'";
            $conditions = array('NProduto.codigo IN ('.implode(',', $codigos_faturados).')');
        }
        if ($conditions)
            $produtos = $this->NProduto->find('list', array('conditions' => $conditions));
        if ($produtos) {
            foreach ($produtos as $codigo_produto => $produto) {
                $filtros['Notaite']['codigo_produto'] = $codigo_produto;
                $meses = $this->Notaite->faturamentoAnual($filtros);
                $series[] = array('name' => "'" . str_replace("'", "\'", $produto) . "'");
                foreach($meses as $mes) {
                    $series[count($series)-1]['values'][] = $mes[0]['preco'];
                }
            }
        }
        $this->set(compact('eixo_x', 'series'));
    }

    function gg_qtd_clientes_faturados_seguradora_corretora(){
        $this->loadModel('Seguradora');
        $filtros = urldecode(Comum::descriptografarLink($this->data['Seguradora']['hash']));
        $filtros = explode('|', $filtros);
        $filtros = array(
            'ano' => $filtros[0],
            'codigo_seguradora' => $filtros[1],
            'codigo_corretora' => $filtros[2],
        );
        $series = array();

        $dados = $this->Cliente->qtdClientesFaturadosPorAno($filtros);
        foreach ($dados as $dado) {
            $converterMes = explode("/",$dado[0]['mes']);
            $mes = $converterMes[0] - 1;
            $resultado[$mes] = $dado[0]['qtd_clientes'];
        }
        $meses = array();
        for($mes = 0; $mes <= 11; $mes++){
            if(isset($resultado[$mes])){
                $meses[$mes] = $resultado[$mes];
            }else{
                $meses[$mes] = 0;
            }
        }
        $series[] = array(
            'name' => $filtros['ano'],
            'values' => $meses
        );

        $filtros['ano'] = $filtros['ano'] - 1;
        $dados = $this->Cliente->qtdClientesFaturadosPorAno($filtros);
        foreach ($dados as $dado) {
            $converterMes = explode("/",$dado[0]['mes']);
            $mes = $converterMes[0] - 1;
            $resultadoAnoAnterior[$mes] = $dado[0]['qtd_clientes'];
        }
        $mesesAnoAnterior = array();
        for($mes = 0; $mes <= 11; $mes++){
            if(isset($resultadoAnoAnterior[$mes])){
                $mesesAnoAnterior[$mes] = $resultadoAnoAnterior[$mes];
            }else{
                $mesesAnoAnterior[$mes] = 0;
            }
        }
        $series[] = array(
            'name' => $filtros['ano'],
            'values' => $mesesAnoAnterior
        );

        $eixo_x = array();
        foreach (Comum::listMeses() as $mes){
            $eixo_x[] = "'".substr($mes,0,3)."'";
        }
        $this->set(compact('eixo_x', 'series'));
    }

    function ranking_faturamento() {
        $this->loadModel('TipoPerfil');
        if(Controller::referer() != '/itens_notas_fiscais/por_empresa') {
            $this->data['Notaite']['data_inicial'] = null;
            $this->data['Notaite']['data_final'] = null;
        }
        $this->data['Notaite'] = $this->Filtros->controla_sessao($this->data, 'Notaite');
        $this->TipoPerfil->carregaFiltrosPorTipoPerfil($this->data['Notaite'],$this->BAuth->user());
        if ($this->data['Notaite']['agrupamento'] == Notaite::AGRP_CLIENTES) {
            $this->pageTitle = 'Ranking de Clientes';
        } elseif ($this->data['Notaite']['agrupamento'] == Notaite::AGRP_CORRETORAS) {
            $this->pageTitle = 'Ranking de Corretoras';
        } elseif ($this->data['Notaite']['agrupamento'] == Notaite::AGRP_GRUPOS_ECONOMICOS) {
            $this->pageTitle = 'Ranking de Grupos Econômicos';
        } elseif ($this->data['Notaite']['agrupamento'] == Notaite::AGRP_PRODUTOS) {
            $this->pageTitle = 'Ranking de Produtos';
        } elseif ($this->data['Notaite']['agrupamento'] == Notaite::AGRP_SEGURADORAS) {
            $this->pageTitle = 'Ranking de Seguradoras';
        } elseif ($this->data['Notaite']['agrupamento'] == Notaite::AGRP_GESTORES) {
            $this->pageTitle = 'Ranking de Gestores';
        }
        $this->carregarCombos();
        $this->set('agrupamento', $this->Notaite->listarAgrupamentos());
    }

    function ranking_faturamento_listagem() {
		$conditions = $this->Filtros->controla_sessao($this->data, 'Notaite');
        $authUsuario = $this->BAuth->user();
        if(!empty($authUsuario['Usuario']['codigo_seguradora']) && $authUsuario['Usuario']['codigo_seguradora']){
            $conditions['codigo_seguradora'] = $authUsuario['Usuario']['codigo_seguradora'];
            $conditions['level'] = 1;
        }elseif(!empty($authUsuario['Usuario']['codigo_corretora']) && $authUsuario['Usuario']['codigo_corretora']){
            $conditions['codigo_corretora'] = $authUsuario['Usuario']['codigo_corretora'];
            $conditions['level'] = 1;
        }elseif(!empty($authUsuario['Usuario']['codigo_filial']) && $authUsuario['Usuario']['codigo_filial']){
            $conditions['codigo_filial'] = $authUsuario['Usuario']['codigo_filial'];
            $conditions['level'] = 1;
        }
        if (empty($conditions['grupo_empresa'])){
            $conditions['grupo_empresa'] = LojaNaveg::GRUPO_BUONNY;
        }
        // if (empty($conditions['empresa'])){
        //     $conditions['empresa'] = '03';
        // }
        $this->data['Notaite'] = $conditions;
        $conditionsTotal = $this->Notaite->rankingFaturamentoConditions($conditions);
        $totalNotas = $this->Notaite->totalRankingFaturamento($conditionsTotal);
        $limit = 100;
        $tipo_ranking = 'clientes';
        $this->paginate = compact('conditions', 'limit', 'clientes', 'tipo_ranking');
		$dados = $this->paginate('Notaite');
		$this->set(compact('dados', 'totalNotas'));
    }

    function ranking_faturamento_listagem_solen() {
        $this->ranking_faturamento_listagem();
        $this->render('ranking_faturamento_listagem_solen');
    }
    

    function comparativo_anual2($new_window = true) {
        if (empty($this->data['Notaite']['grupo_empresa'])) {
            $this->data['Notaite']['grupo_empresa'] = LojaNaveg::GRUPO_BUONNY;
        }
        if ($this->data['Notaite']['grupo_empresa'] == LojaNaveg::GRUPO_LIDER) {
                $this->databaseTable = 'dbNavegarqLider';
                $this->LojaNaveg->databaseTable = 'dbNavegarqLider';
                $this->Notafis->databaseTable = 'dbNavegarqLider';
                $this->NProduto->databaseTable = 'dbNavegarqLider';
        }
        if ($this->data['Notaite']['grupo_empresa'] == LojaNaveg::GRUPO_NATEC) {
                $this->databaseTable = 'dbNavegarqNatec';
                $this->LojaNaveg->databaseTable = 'dbNavegarqNatec';
                $this->Notafis->databaseTable = 'dbNavegarqNatec';
                $this->NProduto->databaseTable = 'dbNavegarqNatec';
        }
        if ($this->data['Notaite']['grupo_empresa'] == LojaNaveg::GRUPO_SOLEN) {
                $this->databaseTable = 'dbNavegarqSolen';
                $this->Notafis->databaseTable = 'dbNavegarqSolen';
                $this->LojaNaveg->databaseTable = 'dbNavegarqSolen';
                $this->NProduto->databaseTable = 'dbNavegarqSolen';

        }
        $this->loadModel('TipoPerfil');

        $this->pageTitle = 'Comparativo Anual';

        if ($new_window) $this->layout = 'new_window';

        $authUsuario = $this->BAuth->user();
        if($authUsuario['Usuario']['codigo_seguradora']){
            $this->data['Notaite']['codigo_seguradora'] = $authUsuario['Usuario']['codigo_seguradora'];
        }elseif($authUsuario['Usuario']['codigo_corretora']){
            $this->data['Notaite']['codigo_corretora'] = $authUsuario['Usuario']['codigo_corretora'];
        }elseif($authUsuario['Usuario']['codigo_filial']){
            $this->data['Notaite']['codigo_filial'] = $authUsuario['Usuario']['codigo_filial'];
        }

        $this->TipoPerfil->carregaFiltrosPorTipoPerfil($this->data['Notaite'],$authUsuario);
        $this->carregarCombos();
        if (!empty($this->data)) {
            $this->loadModel('Mes');
            $dados = $this->Mes->comparativoFaturamentoAnual($this->data['Notaite']);
            $this->set(compact('dados'));
        }
    }

    private function carregarCombos() {
        $this->loadModel('Gestor');
        $this->loadModel('Corretora');
        $this->loadModel('Seguradora');
        $this->loadModel('EnderecoRegiao');
        $this->loadModel('GrupoEconomico');
        $this->set('gestores', $this->Gestor->listarNomesGestoresAtivos());
        $this->set('corretoras', $this->Corretora->find('list', array('order' => 'nome')));
        $this->set('seguradoras', $this->Seguradora->find('list', array('order' => 'nome')));
        $this->set('filiais', $this->EnderecoRegiao->listarRegioes());
        $this->set('produtos', $this->NProduto->listar());
        $this->set('empresas', $this->LojaNaveg->listEmpresas(isset($this->data['Notaite']['grupo_empresa']) ? $this->data['Notaite']['grupo_empresa'] : 1 ));
        $this->set('grupos_economicos', $this->GrupoEconomico->find('list'));
        $this->set('anos', Comum::listAnos());
        $this->set('meses', Comum::listMeses());
        $this->set('grupos_empresas', $this->LojaNaveg->listGrupos());
        $this->set('authUsuario', $this->BAuth->user());
    }
}
