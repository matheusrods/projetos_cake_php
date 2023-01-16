<?php
class PosObsRelatorioRealizadasController extends AppController
{
    public $name = 'PosObsRelatorioRealizadas';
    public $components = array('Filtros','RequestHandler');

    public $uses = array(
        'Cliente',
        'PosObsObservacoes',
        'AcoesMelhorias',
        'AcoesMelhoriasStatus',
        'PosObsRiscos',
        'GrupoEconomico',
        'GrupoEconomicoCliente',
        'Setor',
        'PosCategorias',
        'ClienteOpco',
        'ClienteBu'
    );

    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->_modelName = $this->PosObsObservacoes->name;

        $this->BAuth->allow(array('modal_fotos', 'modal_riscos', 'modal_acoes'));
    }

    private function carrega_dados_filtros_selects($model, $codigo_cliente = null)
    {
        $unidades = array();
        $setores  = array();
        $cargos   = array();

        if (!empty($codigo_cliente)) {
            $codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
            $unidades       = $this->GrupoEconomicoCliente->lista($codigo_cliente);
            $setores        = $this->Setor->lista($codigo_cliente);
        }

        $codigo_cliente_alocacao = null;

        if (!empty($this->data[$model]['codigo_cliente_alocacao'])) {
            $codigo_cliente_alocacao = $this->data[$model]['codigo_cliente_alocacao'];
        }

        $cliente_opco      = $this->ClienteOpco->find('list', array('fields' => array('codigo', 'descricao'), 'conditions' => array('ativo' => 1, 'codigo_cliente' => $codigo_cliente_alocacao)));
        $cliente_bu        = $this->ClienteBu->find('list', array('fields' => array('codigo', 'descricao'), 'conditions' => array('ativo' => 1, 'codigo_cliente' => $codigo_cliente_alocacao)));
        $observador        = $this->PosObsObservacoes->obterTodosObservadores();
        $status_observacao = $this->AcoesMelhoriasStatus->find('list', array('fields' => array('codigo', 'descricao'), 'conditions' => array('codigo IN (1, 2, 5, 6)')));
        $categorias        = $this->PosCategorias->find('list', array('fields' => array('codigo', 'descricao'), 'conditions' => array('ativo' => 1)));

        $this->set(compact('unidades', 'setores', 'cliente_opco', 'cliente_bu', 'observador', 'status_observacao', 'categorias'));
    }

    public function index()
    {
        $this->pageTitle = 'Relatório de Observações Realizadas';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->PosObsObservacoes->name);

        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if (empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        $filtros['codigo_cliente'] = (isset($this->authUsuario['Usuario']['multicliente'])) ? $this->normalizaCodigoCliente($filtros['codigo_cliente']) : $filtros['codigo_cliente'];
        $this->data['PosObsObservacoes'] = $filtros;

        $this->carrega_dados_filtros_selects('PosObsObservacoes', $filtros['codigo_cliente']);
        $this->set(compact('codigo_cliente'));
    }

    public function listagem($destino, $export = false)
    {
        $this->layout = 'ajax';
        $registros    = array();
        $filtros      = $this->Filtros->controla_sessao($this->data, $this->_modelName);
        $authUsuario  = $this->BAuth->user();
        $observacoes  = array();

        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if (empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->normalizaCodigoCliente($this->authUsuario['Usuario']['codigo_cliente']);
            }
        }

        if (!empty($filtros['codigo_cliente'])) {
            $observacoes = $this->PosObsObservacoes->obterRelatorioRealizadas($filtros);
        }

        if ($export) {
            $query = $this->PosObsObservacoes->obterRelatorioRealizadas($filtros, false, $export);
            $this->export_listagem_relatorio_obs($query, $filtros);
            return;
        }

        if (!empty($observacoes)) {
            $this->paginate['PosObsObservacoes'] = $observacoes;
            $registros = $this->paginate('PosObsObservacoes');
            
            // CDCT-607
            $registros = array_unique($registros, SORT_REGULAR);
        }

        // debug(count($registros));
        
        $this->set(compact('registros'));
    } //fim listagem

    public function export_listagem_relatorio_obs($query)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300); // 5min
        

        $dados  = $this->PosObsObservacoes->query($query);
        $riscos = $this->PosObsObservacoes->obterTodosRiscos();
        
        // CDCT-607
        $dados = array_unique($dados, SORT_REGULAR);

        ob_clean();
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="relatorio_observacoes' . date('YmdHis') . '.csv"');
        header('Pragma: no-cache');

        //cabecalho do arquivo
        $cabecalho = utf8_decode('"Cód. Cliente";"Razão Social";"Nome Fantasia";"Setor";"Opco";"Business Unit";"ID Observação";"Tipo de Observação";"Observador";"Local da Observação";"Data";"Hora";"Descrição";"O que eu observei";"O que eu fiz a respeito";"Ação Complementar Sugerida";"Tipo do Perigo/Aspecto";"Perigo/Aspecto";"Risco/Impacto";"Status da Observação";');

        echo $cabecalho . "\n";
        
        foreach ($dados as $value) {
            $riscosMapeados = array();

            foreach ($riscos as $elemento) {
                if ($elemento[0]['codigo_observacao'] === $value['PosObsObservacoes']['codigo']) {
                    $riscosMapeados[] = $elemento[0];
                }
            }

            if (empty($riscosMapeados)) {
                $linha =   $value['Cliente']['codigo'] . ';';
                $linha .=  $value['Cliente']['razao_social'] . ';';
                $linha .=  $value['Cliente']['nome_fantasia'] . ';';
                $linha .=  Comum::converterEncodingPara(str_replace(array("\n", ";", ","), " ", $value['Setor']['descricao']), 'ISO-8859-1') . ';';
                $linha .=  $value['ClienteOpco']['descricao'] . ';';
                $linha .=  $value['ClienteBu']['descricao'] . ';';
                $linha .=  $value['PosObsObservacoes']['codigo'] . ';';
                $linha .=  Comum::converterEncodingPara($value['Categoria']['descricao'], 'ISO-8859-1') . ';';
                $linha .=  $value['Usuario']['nome'] . ';';
                $linha .=  $value['Local']['descricao'] ?  $value['Local']['descricao'] . ';' : $value['Cliente']['nome_fantasia'] . ';';
                $linha .=  Comum::formataData($value[0]['dt_obs'], 'ymd', 'dmy') . ';'; //Acessando o índice zero por conta do CASTING no $fields
                $linha .=  substr($value[0]['hr_obs'], 0, 5) . ';';
                $linha .=  Comum::converterEncodingPara(str_replace(array("\n", ";", ","), " ", $value['PosObsObservacoes']['descricao']), 'ISO-8859-1') . ';';
                $linha .=  Comum::converterEncodingPara(str_replace(array("\n", ";", ","), " ", $value['PosObsObservacoes']['descricao_usuario_observou']), 'ISO-8859-1') . ';';
                $linha .=  Comum::converterEncodingPara(str_replace(array("\n", ";", ","), " ", $value['PosObsObservacoes']['descricao_usuario_acao']), 'ISO-8859-1') . ';';
                $linha .=  Comum::converterEncodingPara(str_replace(array("\n", ";", ","), " ", $value['PosObsObservacoes']['descricao_usuario_sugestao']), 'ISO-8859-1') . ';';
                $linha .=  ' ' . ';';
                $linha .=  ' ' . ';';
                $linha .=  ' ' . ';';
                $linha .=  $value['AcoesMelhoriasStatus']['descricao'] . ';';
                $linha .= "\n";

                echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
                continue;
            }

            foreach ($riscosMapeados as $risco) {
                $linha =   $value['Cliente']['codigo'] . ';';
                $linha .=  $value['Cliente']['razao_social'] . ';';
                $linha .=  $value['Cliente']['nome_fantasia'] . ';';
                $linha .=  Comum::converterEncodingPara($value['Setor']['descricao'], 'ISO-8859-1') . ';';
                $linha .=  $value['ClienteOpco']['descricao'] . ';';
                $linha .=  $value['ClienteBu']['descricao'] . ';';
                $linha .=  $value['PosObsObservacoes']['codigo'] . ';';
                $linha .=  Comum::converterEncodingPara($value['Categoria']['descricao'], 'ISO-8859-1') . ';';
                $linha .=  $value['Usuario']['nome'] . ';';
                $linha .=  $value['Local']['descricao'] ?  $value['Local']['descricao'] . ';' : $value['Cliente']['nome_fantasia'] . ';';
                $linha .=  Comum::formataData($value[0]['dt_obs'], 'ymd', 'dmy') . ';'; //Acessando o índice zero por conta do CASTING no $fields
                $linha .=  substr($value[0]['hr_obs'], 0, 5) . ';';
                $linha .=  Comum::converterEncodingPara(str_replace(array("\n", ";", ","), " ", $value['PosObsObservacoes']['descricao']), 'ISO-8859-1') . ';';
                $linha .=  Comum::converterEncodingPara(str_replace(array("\n", ";", ","), " ", $value['PosObsObservacoes']['descricao_usuario_observou']), 'ISO-8859-1') . ';';
                $linha .=  Comum::converterEncodingPara(str_replace(array("\n", ";", ","), " ", $value['PosObsObservacoes']['descricao_usuario_acao']), 'ISO-8859-1') . ';';
                $linha .=  Comum::converterEncodingPara(str_replace(array("\n", ";", ","), " ", $value['PosObsObservacoes']['descricao_usuario_sugestao']), 'ISO-8859-1') . ';';
                $linha .=  Comum::converterEncodingPara(str_replace(array("\n", ";", ","), " ", $risco['risco_tipo_descricao']), 'ISO-8859-1') . ';';
                $linha .=  Comum::converterEncodingPara(str_replace(array("\n", ";", ","), " ", $risco['perigo_aspecto_descricao']), 'ISO-8859-1') . ';';
                $linha .=  Comum::converterEncodingPara(str_replace(array("\n", ";", ","), " ", $risco['risco_impacto_descricao']), 'ISO-8859-1') . ';';
                $linha .=  $value['AcoesMelhoriasStatus']['descricao'] . ';';
                $linha .= "\n";

                echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
            }
        }

        die();
    }

    public function modal_fotos($codigo_obs)
    {

        $query = "SELECT 
                    an.arquivo_url AS foto
                from pos_obs_anexos obAn
                    inner join pos_anexos an on obAn.codigo_pos_anexo = an.codigo
                where obAn.codigo_pos_obs_observacao = '" . $codigo_obs . "';";

        $dados_fotos = $this->PosObsObservacoes->query($query);

        $this->set(compact('codigo_obs', 'dados_fotos'));
    } //fim modal_respondido

    public function modal_riscos($codigo_obs)
    {
        $fields = array(
            'RiscosTipo.descricao AS risco_tipo_descricao',
            'RiscosImpactos.descricao AS risco_impacto_descricao',
            'PerigosAspectos.descricao AS perigo_aspecto_descricao'
        );

        $this->PosObsRiscos->bindRiscos();
        $dados_riscos = $this->PosObsRiscos->find('all', array(
            'fields'     => $fields,
            'conditions' => array('codigo_pos_obs_observacao' => $codigo_obs)
        ));
        $this->PosObsRiscos->unbindRiscos();

        $this->set(compact('codigo_obs', 'dados_riscos'));
    } //fim modal_respondido

    public function modal_acoes($codigo_obs)
    {
        $acoes_melhorias = $this->AcoesMelhorias->getListaAcoesMelhoriasObs($codigo_obs);

        $this->set(compact('codigo_obs', 'acoes_melhorias'));
    } //fim modal_acoes

    function recupera_dados_matriz($codigo_cliente)
    {
        $this->loadModel('GrupoEconomicoCliente');
        $dados_cliente_matriz =  $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);
        $this->data = array_merge($dados_cliente_matriz, array($this->_modelName => array('codigo_cliente' => $dados_cliente_matriz["Matriz"]["codigo"])));
    }
}
