<?php
class TpecasController extends AppController {
    public $name = 'Tpecas';
    public $layout = 'default';    
    public $helpers = array('Paginator', 'Highcharts');
    public $components = array('Filtros', 'Session'); 
    public $uses = array('Tpecas');   
    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('*'));
    }
    function index(){
        $authUsuario = $this->BAuth->user();    
        $this->data['Tpecas'] = $this->Filtros->controla_sessao($this->data, $this->Tpecas->name);
        if(!empty($authUsuario['Usuario']['codigo_cliente']))
            $this->data['Tpecas']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];        
        $this->pageTitle = 'Peças';        
    }

    function sintetico(){
        $authUsuario = $this->BAuth->user();    
        $this->data['Tpecas'] = $this->Filtros->controla_sessao($this->data, $this->Tpecas->name);
        if(!empty($authUsuario['Usuario']['codigo_cliente']))
            $this->data['Tpecas']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
        $this->pageTitle = 'Peças Sintético';
        $agrupamento = $this->Tpecas->listaAgrupamento();          
        $this->set(compact('agrupamento'));
    }
    function listagem(){         
        $filtros = $this->Filtros->controla_sessao($this->data, 'Tpecas');
        $this->layout = 'ajax';        
        
        //$grafico = $this->sintetico_tpecas_grafico($listar,  $filtros['agrupamento']);
        $agrupamento = $this->Tpecas->listaAgrupamento(); 
        $agrupamento_label = $agrupamento[$filtros['agrupamento']];
        $agrupamento_campo = $filtros['agrupamento'];

        if($filtros['agrupamento'] == 'peca_avaria'){
            $tipo = 'tipo';
            $listar = $this->Tpecas->pesquisaPecaAvaria($tipo, $filtros, false);              
            $this->set(compact('listar','agrupamento', 'agrupamento_label', 'agrupamento_campo', 'grafico', 'filtros', 'tipo'));      
            $this->render('listagem_peca');
        }else if($filtros['agrupamento'] == 'peca_total'){
            $tipo = 'total';
            $listar = $this->Tpecas->pesquisaPecaAvaria($tipo, $filtros, false);              
            $this->set(compact('listar','agrupamento', 'agrupamento_label', 'agrupamento_campo', 'grafico', 'filtros', 'tipo'));      
            $this->render('listagem_peca');
        }else{            
            $listar = $this->Tpecas->pesquisaTpecas($filtros);        
            $this->set(compact('listar','agrupamento', 'agrupamento_label', 'agrupamento_campo', 'grafico'));
        }       
        
       
    }    
    function listagem_analitico($tipo_view = null, $valor = null, $agrupamento = 'null', $valor2 = 'null', $agrupamento2 = 'null', $avaria = 'null'){        
        $this->Cliente = ClassRegistry::init('Cliente');
        $this->pageTitle = 'Peças Analítico';
        $this->data['Tpecas'] = $this->Filtros->controla_sessao($this->data, "Tpecas");
        $this->data['Tpecas']['valor'] = $agrupamento;
        $conditions = array();
        $cliente = null;
        if(!empty($this->data['Tpecas']['codigo_cliente'])){
            $conditions['Tpecas.codigo_cliente'] = $this->data['Tpecas']['codigo_cliente'];        
           $cliente = $this->Cliente->carregar($this->data['Tpecas']['codigo_cliente']);            
        }
        if(!empty($this->data['Tpecas']['local']))
            $conditions['Tpecas.local'] = $this->data['Tpecas']['local'];
        if(isset($this->data['Tpecas']['data_inicial']) && !empty($this->data['Tpecas']['data_inicial']))
            $conditions['Tpecas.data >='] = date('Ymd 00:00:00', Comum::dateToTimestamp($this->data['Tpecas']['data_inicial']));      
        if(isset($this->data['Tpecas']['data_final']) && !empty($this->data['Tpecas']['data_final']))
            $conditions['Tpecas.data <='] = date('Ymd 23:59:59', Comum::dateToTimestamp($this->data['Tpecas']['data_final']));      
        
        if($agrupamento!=='null'){
            if($valor=='null'){
                $conditions[] = 'Tpecas.'.$agrupamento.' = \'\'';
            }else{ 
                $conditions[] = 'Tpecas.'.$agrupamento.' like \'%' .$valor.'%\'';
            }
        }
        if($agrupamento2!=='null'){
            if($valor2=='null'){
                $conditions[] = 'Tpecas.'.$agrupamento2.' = \'\'';
            }else{ 
                $conditions[] = 'Tpecas.'.$agrupamento2.' like \'%' .$valor2.'%\'';
            }
        }

        if($avaria!=='null'){
            if($avaria=='com_avaria'){
                $conditions[] = '(Tpecas.tipo_peca_avaria <> \'Sem Av.\' AND Tpecas.tipo_peca_avaria <> \'\' AND Tpecas.tipo_peca_avaria IS NOT NULL)';
            }else if($avaria=='sem_avaria'){   
                $conditions[] = '(Tpecas.tipo_peca_avaria = \'Sem Av.\' OR Tpecas.tipo_peca_avaria = \'\' OR Tpecas.tipo_peca_avaria IS NULL)';
            }
        }

        if($tipo_view == 'popup') {
            $this->layout = 'new_window';
        }
        if($tipo_view == 'export'){
            $this->listagem_analitico_export($conditions);
        }
        $this->paginate['Tpecas'] = array(
            'conditions' => $conditions,
            'limit'      => 20,
            'order'      => 'Tpecas.codigo' ,
            'extra'      => 'tpecas_analitico'
        );
        $listar = $this->paginate('Tpecas');  
        $this->set(compact('listar', 'cliente', 'valor', 'agrupamento', 'tipo_view'));
    }
    function listagem_analitico_export($conditions) {
        $registros= $this->Tpecas->listagem_analitico($conditions);
        header('Content-type: application/vnd.ms-excel');
        header(sprintf('Content-Disposition: attachment; filename="%s"', basename('tpecas.csv')));
        header('Pragma: no-cache');
        echo iconv('UTF-8', 'ISO-8859-1', 'Local Vistoria;"DN";"Transportador";"Número Peça";"Tipo Caixa";"Tipo Caixa Avaria";"Tipo Peça";"Tipo Peça Avaria";"Destino";"Data"');
       
        foreach($registros as $registro){
            $registro = $registro['Tpecas'];
            $linha = "";
            $linha .= '"'. $registro['local'] . '";';
            $linha .= '"'. $registro['dn'] . '";';            
            $linha .= '"'. iconv('ISO-8859-1', 'UTF-8', $registro['transportador']) . '";';
            $linha .= '"'. $registro['numero_peca'] . '";';     
            $linha .= '"'. $registro['tipo_caixa'] . '";';     
            $linha .= '"'. $registro['tipo_caixa_avaria'] . '";';     
            $linha .= '"'. $registro['tipo_peca'] . '";';     
            $linha .= '"'. $registro['tipo_peca_avaria'] . '";';     
            $linha .= '"'. $registro['destino'] . '";';                 
            $linha .= '"'. AppModel::dbDateToDate($registro['data']) . '";';
            echo "\n".iconv('UTF-8', 'ISO-8859-1', $linha);
        }
        exit;
    }

     function sintetico_tpecas_agrupamento_grafico(){
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'Tpecas');        
        $relatorioListagem = $this->Tpecas->pesquisaTpecas($filtros); 
        $descricao = false;
        foreach ($relatorioListagem as $relatorio) {
            $valor[] = $relatorio[0]['total'];
            $descricao[] =  array(
                'name' => trim($relatorio[0]['agrupamento']) ? "'".$relatorio[0]['agrupamento']."'" : "'Não definido'",
                'values' => $relatorio[0]['total']
            );
        }
        if($descricao){
            $qtd_registros_label = count($descricao);
            $rotate_angle = ($qtd_registros_label < 15 ? -10 : ($qtd_registros_label < 25 ? -45 : -90));
            $dadosGrafico['eixo_x'] = $valor;
            $dadosGrafico['series'] =  $descricao;
            $this->set(compact('dadosGrafico'));
        }        
    }
    function sintetico_tpecas_total_grafico(){
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'Tpecas');        
        $relatorioListagem = $this->Tpecas->pesquisaTpecas($filtros); 
        $descricao = false;
        $total_com = 0;
        $total_sem = 0;
        foreach ($relatorioListagem as $relatorio) {
            $total_com += $relatorio[0]['com_avaria'];
            $total_sem += $relatorio[0]['sem_avaria'];
        }
        $valor = array($total_com, $total_sem);
        $descricao =  array(
                array(
                    'name' => '\'Com Avaria\'',
                    'values' => $total_com
                ),
                array(
                    'name' => '\'Sem Avaria\'',
                    'values' => $total_sem
                )
            );

        if($descricao){
            $qtd_registros_label = count($descricao);
            $rotate_angle = ($qtd_registros_label < 15 ? -10 : ($qtd_registros_label < 25 ? -45 : -90));
            $dadosGrafico['eixo_x'] = $valor;
            $dadosGrafico['series'] =  $descricao;
            $this->set(compact('dadosGrafico'));
        }        
    }
    function sintetico_pecas_grafico(){
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'Tpecas');        
        $relatorioListagem = $this->Tpecas->pesquisaTpecas($filtros); 
        $descricao = false;
        foreach ($relatorioListagem as $relatorio) {
            $valor[] = $relatorio[0]['total'];
            $descricao[] =  array(
                'name' => trim($relatorio[0]['agrupamento']) ? "'".$relatorio[0]['agrupamento']."'" : "'Não definido'",
                'values' => $relatorio[0]['total']
            );
        }
        if($descricao){
            $qtd_registros_label = count($descricao);
            $rotate_angle = ($qtd_registros_label < 15 ? -10 : ($qtd_registros_label < 25 ? -45 : -90));
            $dadosGrafico['eixo_x'] = $valor;
            $dadosGrafico['series'] =  $descricao;
            $this->set(compact('dadosGrafico'));
        }        
    }

    function tpecas_avaria_grafico(){
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'Tpecas');        
        $dadosGrafico = $this->Tpecas->pesquisaPecaAvaria('tipo', $filtros);           
        $this->set(compact('dadosGrafico'));
    
    }
    function tpecas_total_grafico(){
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'Tpecas');        
        $dadosGrafico = $this->Tpecas->pesquisaPecaAvaria('total', $filtros);           
        $this->set(compact('dadosGrafico'));    
    }
    function upload_arquivo(){
        $destino = APP.'webroot'.DS.'files'.DS.'importacao_transyseg'.DS;
        if (!empty($this->data['Tpecas']['filename_pic']['name'])) {
            $foto = strtolower($this->data['Tpecas']['filename_pic']['name']);
            if (strpos($foto, ".jpg") > 0 || strpos($foto, ".gif") || strpos($foto, ".png") > 0 || strpos($foto, ".jpeg") > 0) {
                preg_match("/(\..*){1}$/i", $this->data['Tpecas']['filename_pic']['name'], $ext);
                $nome_arquivo = date('YmdHis') . '_' .  $this->data['Tpecas']['codigo_cliente'].$ext[0];
                $destino .= $nome_arquivo;                

                if (!move_uploaded_file($_FILES['data']['tmp_name']['Tpecas']['filename_pic'], $destino)) {                    
                    $this->Tpecas->invalidate('filename_pic', 'Informe arquivo .jpg ou .gif ou .png ou .jpeg de até 10MB');
                }else{
                    $this->data['Tpecas']['filename_pic'] = $nome_arquivo;
                    return true;
                }
            } else {                
                $this->Tpecas->invalidate('filename_pic', 'Informe arquivo .jpg ou .gif ou .png ou .jpeg de até 10MB');
            }
        }

        if($this->data['Tpecas']['tem_foto'] === '0'){
            $this->data['Tpecas']['filename_pic'] = "";
        }else{
            unset($this->data['Tpecas']['filename_pic']);
        }
        unset($this->data['Tpecas']['tem_foto']);
    }
    function incluir() {
        $this->pageTitle = 'Incluir Peça';
        if($this->RequestHandler->isPost()) {
            $this->data['Tpecas']['filename'] = '';
            $this->upload_arquivo();
            if ($this->Tpecas->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
    }
    function editar($codigo_tpeca) {
        $this->pageTitle = 'Atualizar Peça';
        if (!empty($this->data)) {            
            $this->upload_arquivo();
            if ($this->Tpecas->atualizar($this->data)) {
                $this->BSession->setFlash('update_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('update_error');
            }
        } else {
            $this->data = $this->Tpecas->carregar($codigo_tpeca);            
        }
    }
    function excluir($codigo_peca) {
        if ( $codigo_peca) {
            if ($this->Tpecas->delete($codigo_peca)) {
                $this->BSession->setFlash('delete_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('delete_error');
            }
        }
    }
}