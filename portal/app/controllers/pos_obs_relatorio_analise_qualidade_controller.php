<?php
class PosObsRelatorioAnaliseQualidadeController extends AppController {

    public $name = 'PosObsRelatorioAnaliseQualidade';
    public $components = array('Filtros', 'RequestHandler');
    public $uses = array(
        'Cliente',
        'PosObsObservacoes',
        'ClienteOpco',
        'ClienteBu',
        'AcoesMelhorias',
        'AcoesMelhoriasStatus',
        'PosCategorias',
        'GrupoEconomico',
        'GrupoEconomicoCliente',
        'Setor'
    );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow();
    }

    public function index() {
        
        $this->pageTitle = 'Relatório de Análises de Qualidade';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->PosObsObservacoes->name);
        
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if(empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        $filtros['codigo_cliente'] = (isset($this->authUsuario['Usuario']['multicliente'])) ? $this->normalizaCodigoCliente($filtros['codigo_cliente']) : $filtros['codigo_cliente'];

        $this->data['PosObsObservacoes'] = $filtros;

        $this->carrega_dados_filtros('PosObsObservacoes');
        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
    }
    
    public function listagem($destino, $export = false) {
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->PosObsObservacoes->name);

        $authUsuario = $this->BAuth->user();
            if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            if(empty($filtros['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        $registros = array();

        if(!empty($filtros['codigo_cliente'])){

            $conditions = $this->PosObsObservacoes->filtrosConditions($filtros);           

            if($export){
                $query = $this->PosObsObservacoes->obterRelatorioAnaliseQualidade($conditions, $exportTrue = 1);
                $this->export_listagem($query, $filtros);
            } else {
                $this->paginate['PosObsObservacoes'] = $this->PosObsObservacoes->obterRelatorioAnaliseQualidade($conditions);
                // pr($this->PosObsObservacoes->find('sql', $this->paginate['PosObsObservacoes']));
                $registros = $this->paginate('PosObsObservacoes');                
            }
        }

        $this->set(compact('registros'));
    }

    function recupera_dados_matriz($codigo_cliente) {
        $this->loadModel('GrupoEconomicoCliente');
        $dados_cliente_matriz =  $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);
        $this->data = array_merge( $dados_cliente_matriz, array($this->_modelName => array('codigo_cliente' => $dados_cliente_matriz["Matriz"]["codigo"])));
    }

    private function carrega_dados_filtros($model){
        $unidades = array();
        $setores  = array();
        $cargos   = array();

        $codigo_cliente = (isset($this->data[$model]['codigo_cliente'])) ? $this->data[$model]['codigo_cliente'] : array();

        if (!empty($codigo_cliente)) {
            $codigo_cliente = (is_array($codigo_cliente)) ? $codigo_cliente : $codigo_cliente;
            $codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

            $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
            $setores  = $this->Setor->lista($codigo_cliente);
        }

        $codigo_cliente_alocacao = null;

        if (!empty($this->data[$model]['codigo_cliente_alocacao'])) {
            $codigo_cliente_alocacao = $this->data[$model]['codigo_cliente_alocacao'];
        }

        $cliente_opco      = $this->ClienteOpco->find('list', array('fields' => array('codigo', 'descricao'), 'conditions' => array('ativo' => 1, 'codigo_cliente' => $codigo_cliente_alocacao)));
        $cliente_bu        = $this->ClienteBu->find('list', array('fields' => array('codigo', 'descricao'), 'conditions' => array('ativo' => 1, 'codigo_cliente' => $codigo_cliente_alocacao)));
        
        $observador        = $this->PosObsObservacoes->obterTodosObservadores();
        $status_observacao = $this->AcoesMelhoriasStatus->find('list', array('fields' => array('codigo', 'descricao'), 'conditions' => array('codigo IN (5, 6)')));
        $categorias        = $this->PosCategorias->find('list', array('fields' => array('codigo', 'descricao'), 'conditions' => array('ativo' => 1)));

        $this->set(compact('unidades', 'setores', 'cliente_opco', 'cliente_bu', 'observador', 'status_observacao', 'categorias'));
    }

    private function export_listagem($dados){
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300); // 5min

        // headers
        ob_clean();
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="relatorio_analises_qualidades_'.date('YmdHis').'.csv"');
        header('Pragma: no-cache');

         //cabecalho do arquivo
        $cabecalho = utf8_decode('"Código Cliente";"Razão Social";"Nome Fantasia";"Setor";"Opco";"Business Unit";"ID Observação";"Tipo de Observação";"Observador";"Local da Observação";"Data";"Hora";"Descrição";"O que eu observei";"O que eu fiz a respeito";"Ação Complementar Sugerida";"Status da Observação";"Criticidade";"ID Ação de Melhoria";"Tipo da Ação";"Status da Ação";"Criticidade da Ação";"Descreva o desvio";"Descreva a ação";"Local da Ação";"Responsável da Ação";"Prazo de Conclusão da Ação";"Origem";"Avaliação da Observação";"Complemento da Avaliação";"Pessoas Participantes da Tratativa";');

        //concatena o cabecalho
        echo $cabecalho."\n";

        foreach ($dados as $value) {

            if (empty($value['AcoesMelhoriasAssociadas'])) {
                $linha =  $value['Cliente']['codigo'] . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", $value['Cliente']['razao_social'])) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", $value['Cliente']['nome_fantasia'])) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['Setor']['descricao']))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", $value['PosObsLocais']['codigo_cliente_opco'])) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", $value['PosObsLocais']['codigo_cliente_bu'])) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", $value['PosObsObservacoes']['codigo'])) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['PosCategorias']['descricao'], 'ISO-8859-1'))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", $value['Usuario']['nome'])) . ';';
                $linha .=  $value['Local']['descricao'] ?  Comum::converterEncodingPara($value['Local']['descricao']) . ';' : $value['Cliente']['nome_fantasia'] . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::formataData($value[0]['dt_obs'], 'ymd', 'dmy'))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", substr($value[0]['hr_obs'], 0, 5))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['PosObsObservacoes']['descricao'], 'ISO-8859-1'))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['PosObsObservacoes']['descricao_usuario_observou'], 'ISO-8859-1'))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['PosObsObservacoes']['descricao_usuario_acao'], 'ISO-8859-1'))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['PosObsObservacoes']['descricao_usuario_sugestao'], 'ISO-8859-1'))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['AcoesMelhoriasStatus']['descricao'], 'ISO-8859-1'))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['PosCriticidade']['descricao'], 'ISO-8859-1'))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", '')) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", '')) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", '')) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", '')) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", '')) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", '')) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", '')) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", '')) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", '')) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", '')) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", $value['PosObsObservacoes']['qualidade_avaliacao'])) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['PosObsObservacoes']['qualidade_descricao_complemento'], 'ISO-8859-1'))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['PosObsObservacoes']['qualidade_descricao_participantes_tratativa'], 'ISO-8859-1'))) . ';';

                $linha .= "\n";

                echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));

                continue;
            }

            $acoesMelhorias = $this->AcoesMelhorias->getListaAcoesMelhoriasObs($value['PosObsObservacoes']['codigo']);

            foreach ($acoesMelhorias as $acao) {

                $linha =  $value['Cliente']['codigo'] . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", $value['Cliente']['razao_social'])) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", $value['Cliente']['nome_fantasia'])) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['Setor']['descricao'], 'ISO-8859-1'))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", $value['PosObsLocais']['codigo_cliente_opco'])) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", $value['PosObsLocais']['codigo_cliente_bu'])) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", $value['PosObsObservacoes']['codigo'])) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['PosCategorias']['descricao'], 'ISO-8859-1'))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", $value['Usuario']['nome'])) . ';';
                $linha .=  $value['Local']['descricao'] ?  Comum::converterEncodingPara($value['Local']['descricao']) . ';' : $value['Cliente']['nome_fantasia'] . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::formataData($value[0]['dt_obs'], 'ymd', 'dmy'))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", substr($value[0]['hr_obs'], 0, 5))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['PosObsObservacoes']['descricao'], 'ISO-8859-1'))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['PosObsObservacoes']['descricao_usuario_observou'], 'ISO-8859-1'))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['PosObsObservacoes']['descricao_usuario_acao'], 'ISO-8859-1'))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['PosObsObservacoes']['descricao_usuario_sugestao'], 'ISO-8859-1'))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['AcoesMelhoriasStatus']['descricao'], 'ISO-8859-1'))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['PosCriticidade']['descricao'], 'ISO-8859-1'))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", $acao['AcoesMelhorias']['codigo'])) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($acao['AcoesMelhoriasTipo']['descricao']))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($acao['AcoesMelhoriasStatus']['descricao']))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($acao['PosCriticidade']['descricao']))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($acao['AcoesMelhorias']['descricao_desvio']))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($acao['AcoesMelhorias']['descricao_acao']))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($acao['AcoesMelhorias']['descricao_local_acao']))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", $acao['Responsavel']['nome'])) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", $acao['AcoesMelhorias']['prazo'])) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($acao['OrigemFerramenta']['descricao']))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", $value['PosObsObservacoes']['qualidade_avaliacao'])) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['PosObsObservacoes']['qualidade_descricao_complemento'], 'ISO-8859-1'))) . ';';
                $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::converterEncodingPara($value['PosObsObservacoes']['qualidade_descricao_participantes_tratativa'], 'ISO-8859-1'))) . ';';

                $linha .= "\n";

                echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
            }
        }

        die();
    }    
}
