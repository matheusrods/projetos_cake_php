<?php
class GlosasController extends AppController {
    public $name = 'Glosas';
    public $helpers = array('BForm', 'Html', 'Ajax', 'Highcharts', 'Buonny', 'Ithealth');
    public $components = array('Filtros', 'RequestHandler','ExportCsv', 'Upload');
    var $uses = array(
        'Glosas', 
        'GlosasStatus', 
        'ItemPedidoExame', 
        'Exame', 
        'NotaFiscalServico', 
        'NotaFiscalStatus',
        'TipoGlosas'
    );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('exibir_glosas'));
    }

    public function index(){
        //title
        $this->pageTitle = 'Relatório de Glosas';
        //filtros da sessao
        $filtros = $this->Filtros->controla_sessao($this->data, 'Glosas');
        //carrega os combos 
        $this->carregaInfos();
    }

    public function listagem($destino, $export = false){
        //ajax
        $this->layout = 'ajax'; 
        //filtros da sessao
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Glosas->name);

        if(empty($filtros['data_inicio']) && empty($filtros['data_fim'])){
            $filtros['data_fim'] = date('d/m/Y');
            $filtros['data_inicio'] = '01/'.date('m/Y');
        }

        if(!empty($filtros)){
            //monta as condicoes que vem do filtros
            $conditions = $this->Glosas->conditionsGlosas($filtros);
            //monta a query
        $query_notas_fiscais = $this->Glosas->buscaGlosas($conditions);
            
            $this->paginate['NotaFiscalServico'] = array(
                'fields' => $query_notas_fiscais['fields'],
                'conditions' => $query_notas_fiscais['conditions'],
                'limit' => 50,
                'joins' => $query_notas_fiscais['joins'],
            );

            // pr($this->NotaFiscalServico->find('sql', $this->paginate['NotaFiscalServico']));

            if($export){
                //coloca query e metodo export
                $query = $this->NotaFiscalServico->find('sql',array('fields' => $query_notas_fiscais['fields'], 'conditions' => $query_notas_fiscais['conditions'], 'joins' => $query_notas_fiscais['joins']));
                $conditions = $this->Glosas->conditionsGlosas($filtros);
                $this->exportListagem($query,$conditions);
            } else {
                //senao monta a lista normal
                $glosas = $this->paginate('NotaFiscalServico');     
            }
        }
        $this->set(compact('glosas'));
    }

    public function incluir(){

    }

    public function editar(){

    }

    public function carregaInfos(){
        unset($this->data['Last']);
        // se o usuario for de cliente ele carregará as informacoes do cliente onde ele esta inserido
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }
        //seta o periodo
        if(empty($filtros['data_inicio'])){
            $filtros['data_fim'] = date('d/m/Y');
            $filtros['data_inicio'] = '01'.date('m/Y');
        }
        //tipo de glosa
        $tipos_glosas = $this->TipoGlosas->find('list', array('fields' => array('codigo', 'descricao')));
      
        $this->data['Glosas'] = $filtros;
        $this->set(compact('tipos_glosas'));
    }//fim

    public function exportListagem($query = null, $conditions = null){
        //instancia o dbo
        $dbo = $this->NotaFiscalServico->getDataSource();
        $dbo2 = $this->Glosas->getDataSource();
        
        //pega todos os resultados

        $dbo->results = $dbo->rawQuery($query);

        //headers
        ob_clean();
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="relatorio_glosas.csv"');
        header('Pragma: no-cache');
        
        //cabecalho do arquivo
        $lista_glosas = array();
        while($teste = $dbo->fetchRow()){
            array_push($lista_glosas,$teste); 
        }

        echo utf8_decode('"Número Nota Fiscal";"Código Prestador";"Razão Social Prestador";"CNPJ Prestador";"Nome Fantasia Prestador";"Valor Bruto NF";"Valor Glosado";"Código glosa";"Código do Pedido";"Exame";"Funcionário";"Código Cliente";"Nome Fantasia Cliente";"Classificação da glosa";"Valor sistema";"Valor cobrado";"Valor da glosa";"Data da Glosa";"Data de Vencimento";"Data de Pagamento";"Tipo da Glosa";"Observações";"Status";')."\n";
        foreach($lista_glosas as $key => $lista_glosa){
            // varre todos os registros da consulta no banco de dados
            $query_glosas = $this->NotaFiscalServico->lista_glosas_export(null,$lista_glosa['NotaFiscalServico']['codigo']);
            $glosas = $this->Glosas->find('sql',array('fields' => $query_glosas['fields'], 'conditions' => $query_glosas['conditions'], 'joins' => $query_glosas['joins']));
            $dbo2->results = $dbo2->rawQuery($glosas);
            
            while($glosa = $dbo2->fetchRow()){
                $linha  = $lista_glosa['NotaFiscalServico']['numero_nota_fiscal'].';';
                $linha .= $lista_glosa['Fornecedor']['codigo'].';';
                $linha .= utf8_decode($lista_glosa['Fornecedor']['razao_social']).';';
                $linha .= Comum::formatarDocumento($lista_glosa['Fornecedor']['codigo_documento']).';';
                $linha .= utf8_decode($lista_glosa['Fornecedor']['nome']).';';
                $linha .= 'R$ '.Comum::moeda($lista_glosa['NotaFiscalServico']['valor']).';';
                $linha .= (empty($lista_glosa[0]['total_glosado'])                  ? 'R$ 0,00' : 'R$ '.Comum::moeda($lista_glosa[0]['total_glosado'])) .';';
                $linha .= $glosa['Glosas']['codigo'].';';
                $linha .= (isset($glosa['PedidoExame']['codigo'])                   ? $glosa['PedidoExame']['codigo']                                         : "-") .';';
                $linha .= (isset($glosa['Exame']['descricao'])                      ? utf8_decode($glosa['Exame']['descricao'])                               : "-") .';'; 
                $linha .= (isset($glosa['Funcionario']['nome'])                     ? utf8_decode($glosa['Funcionario']['nome'])                              : "-") .';';
                $linha .= (isset($glosa['Cliente']['codigo'])                       ? $glosa['Cliente']['codigo']                                             : "-") .';';
                $linha .= (isset($glosa['Cliente']['razao_social'])                 ? utf8_decode($glosa['Cliente']['razao_social'])                          : "-") .';';
                $linha .= (isset($glosa['ClassificacaoGlosa']['descricao'])         ? utf8_decode($glosa['ClassificacaoGlosa']['descricao'])                  : "-") .';';
                $linha .= (isset($glosa['ConsolidadoNfsExame']['valor'])            ? 'R$ '.Comum::moeda($glosa['ConsolidadoNfsExame']['valor'])              : "-") .';';
                
                if(isset($glosa['ClassificacaoGlosa']['descricao'])){
                    if ($glosa['ClassificacaoGlosa']['descricao'] == 'Imagem' || $glosa['ClassificacaoGlosa']['descricao'] == 'Manual'){
                        $linha .= "-;";
                    }else {
                        $linha .= (isset($glosa['ConsolidadoNfsExame']['valor_corrigido'])  ? 'R$ '.Comum::moeda($glosa['ConsolidadoNfsExame']['valor_corrigido'])    : "-") .';';
                    }                    
                }else{
                    $linha .= "-;";
                }             
                $linha .= (isset($glosa['Glosas']['valor'])                         ? 'R$ '.Comum::moeda($glosa['Glosas']['valor'])                           : "-") .';';
                $linha .= (isset($glosa['Glosas']['data_glosa'])                    ? Comum::formataData($glosa['Glosas']['data_glosa'],"ymd","dmy")          : "-") .';';
                $linha .= (isset($glosa['Glosas']['data_vencimento'])               ? Comum::formataData($glosa['Glosas']['data_vencimento'],"ymd","dmy")     : "-") .';';
                $linha .= (isset($glosa['Glosas']['data_pagamento'])                ? Comum::formataData($glosa['Glosas']['data_pagamento'],"ymd","dmy")      : "-") .';';
                $linha .= (isset($glosa['TipoGlosa']['descricao'])                  ? utf8_decode($glosa['TipoGlosa']['descricao'])                           : "-") .';';
                $linha .= (isset($glosa['Glosas']['motivo_glosa'])                  ? utf8_decode($glosa['Glosas']['motivo_glosa'])                        : "-") .';';
                $linha .= (isset($glosa['GlosasStatus']['descricao'])               ? utf8_decode($glosa['GlosasStatus']['descricao'])                        : "-") .';';
                $linha .= "\n";

                echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));

            }
        }//fim while
  
        //mata o metodo
        die();

    }//fim

    public function exibir_glosas($codigo_nota, $tabela){
        //se vier este indice da view       
        if($tabela == 'dadosGlosas'){
            //monta a query das glosas
            $glosas = $this->NotaFiscalServico->lista_glosas(null,$codigo_nota);
            $glosas['conditions']['Glosas.ativo'] = 1;
            //busca a glosa
            $dados_glosas = $this->Glosas->find('all', 
                array(
                    'conditions' => $glosas['conditions'], 
                    'joins' => $glosas['joins'], 
                    'fields' => $glosas['fields'], 
                    'order' => $glosas['order']
                )
            );
            
            //trata os dados
            $dados = $this->Glosas->trataDados($dados_glosas);
        }//fim if

        //varre os dados para transformar em json
        $retorno = json_encode("erro");
        if( isset($dados) && !empty($dados) ) {
            $retorno = json_encode($dados);
        }

        echo $retorno;
        exit;
    }
}
