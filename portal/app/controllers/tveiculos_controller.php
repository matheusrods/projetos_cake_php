<?php
class TveiculosController extends AppController {
    public $name = 'Tveiculos';
    public $layout = 'default';    
    public $helpers = array('Paginator', 'Highcharts');
    public $components = array('Filtros', 'Session'); 
    public $uses = array('Tveiculos');   

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('*'));
    }

    function index(){
        $authUsuario = $this->BAuth->user();    
        $this->data['Tveiculos'] = $this->Filtros->controla_sessao($this->data, $this->Tveiculos->name);
        if(!empty($authUsuario['Usuario']['codigo_cliente']))
            $this->data['Tveiculos']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];        
        $this->pageTitle = 'Veículos';        
    }
    
    function lista(){
        $filtros = $this->Filtros->controla_sessao($this->data, 'Tveiculos');
        $this->layout = 'ajax';        
        
        $listar = $this->Tveiculos->pesquisaTveiculos($filtros);
        $this->set(compact('listar', 'filtros'));
         
    }
   
    function sintetico(){
        $authUsuario = $this->BAuth->user();    
        $this->data['Tveiculos'] = $this->Filtros->controla_sessao($this->data, $this->Tveiculos->name);
        if(!empty($authUsuario['Usuario']['codigo_cliente']))
            $this->data['Tveiculos']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];        
        $this->pageTitle = 'Veículos Sintético';
        $agrupamento = $this->Tveiculos->listaAgrupamento(); 
        $this->set(compact('agrupamento'));
    }
    function listagem(){    
        $filtros = $this->Filtros->controla_sessao($this->data, 'Tveiculos');
        $this->layout = 'ajax';
        $agrupamento = $this->Tveiculos->listaAgrupamento(); 
        $agrupamento_label = $agrupamento[$filtros['agrupamento']];
        $agrupamento_campo = $filtros['agrupamento'];
        $tipo_pesquisa = array(
            'veiculo_avaria' => 'tipo',
            'veiculo_local'  => 'local',
            'veiculo_total'  => 'total'
        );
        if(array_key_exists($filtros['agrupamento'], $tipo_pesquisa)){
            $tipo = $tipo_pesquisa[$filtros['agrupamento']];
            $listar = $this->Tveiculos->pesquisaVeiculoAvaria($tipo,$filtros);            
            $this->set(compact('listar','agrupamento', 'agrupamento_label', 'agrupamento_campo', 'grafico', 'filtros', 'tipo'));      
            $this->render('listagem_veiculo');
        }else{  


            // $this->paginate['Tveiculos'] = array(
            //     'conditions' => $filtros,
            //     'limit'      => null,
            //     'order'      => $agrupamento_campo,
            //     'extra'      => array('tveiculos_sintetico' => true )
            // );
            // $listar = $this->paginate('Tveiculos');  

            $listar = $this->Tveiculos->pesquisaTveiculos($filtros);
            $this->set(compact('listar','agrupamento', 'agrupamento_label', 'agrupamento_campo', 'grafico', 'filtros'));
        } 
    }    
    function listagem_analitico($tipo_view = null, $valor = null, $agrupamento = 'null', $valor2 = 'null', $agrupamento2 = 'null', $avaria = 'null'){        
        $this->Cliente = ClassRegistry::init('Cliente');
        $this->pageTitle = 'Veículos Analítico';        
        $this->data['Tveiculos'] = $this->Filtros->controla_sessao($this->data, "Tveiculos");
        $this->data['Tveiculos']['valor'] = $agrupamento;  
        $conditions = array();
        $cliente = null;
        if(!empty($this->data['Tveiculos']['codigo_cliente'])){            
            $conditions['Tveiculos.codigo_cliente'] = $this->data['Tveiculos']['codigo_cliente'];
            $cliente = $this->Cliente->carregar($this->data['Tveiculos']['codigo_cliente']);            
        }        
        if(!empty($this->data['Tveiculos']['chassi']))
            $conditions['Tveiculos.chassi'] = $this->data['Tveiculos']['chassi'];
        if(!empty($this->data['Tveiculos']['local']))
            $conditions['Tveiculos.local'] = $this->data['Tveiculos']['local'];
        if(!empty($this->data['Tveiculos']['data_inicial']))
            $conditions['Tveiculos.data >='] = date('Ymd 00:00:00', Comum::dateToTimestamp($this->data['Tveiculos']['data_inicial']));      
        if(!empty($this->data['Tveiculos']['data_final']))
            $conditions['Tveiculos.data <='] = date('Ymd 23:59:59', Comum::dateToTimestamp($this->data['Tveiculos']['data_final']));      
        if(!empty($this->data['Tveiculos']['agrupamento']) && $this->data['Tveiculos']['agrupamento'] == 'avaria_tipo' || $this->data['Tveiculos']['agrupamento'] == 'avaria_local')
            $conditions['Tveiculos.avaria_tipo <>'] = 'Sem Avaria';
        
        if($agrupamento!=="null")            
                $conditions[] = $valor=='null' ? 'Tveiculos.'.$agrupamento.' = \'\'' : 'Tveiculos.'.$agrupamento.' like \'%' .$valor.'%\'';
        
        if($agrupamento2!=="null")            
                $conditions[] = $valor2=='null' ? 'Tveiculos.'.$agrupamento2.' = \'\'' : 'Tveiculos.'.$agrupamento2.' like \'%' .$valor2.'%\'';
        
        if($avaria!=='null'){
            if($avaria=='com_avaria')
                $conditions[] = '(Tveiculos.avaria_tipo <> \'Sem Avaria\' AND Tveiculos.avaria_tipo <> \'\' AND Tveiculos.avaria_tipo IS NOT NULL)';
            else if($avaria=='sem_avaria')
                $conditions[] = '(Tveiculos.avaria_tipo = \'Sem Avaria\' OR Tveiculos.avaria_tipo = \'\' OR Tveiculos.avaria_tipo IS NULL)';            
        }
        if(!empty($this->data['Tveiculos']['agrupamento']) && $this->data['Tveiculos']['agrupamento'] == 'veiculo_local'){
            $conditions[] = 'Tveiculos.avaria_tipo <> \'Sem Avaria\'';
            $conditions[] = 'Tveiculos.avaria_tipo <> \' \'';
            $conditions[] = 'Tveiculos.avaria_tipo IS NOT NULL';
        }      
        if($tipo_view == 'popup') 
            $this->layout = 'new_window';
        
        if($tipo_view == 'export')
            $this->listagem_analitico_export($conditions);
        
        $this->paginate['Tveiculos'] = array(
            'conditions' => $conditions,
            'limit'      => 20,
            'order'      => 'Tveiculos.codigo' ,
            'extra'      => array('tveiculos_analitico' => true)
        );
        $listar = $this->paginate('Tveiculos');          
        $this->set(compact('listar', 'cliente',  'valor', 'agrupamento', 'tipo_view'));
    }

    function listagem_analitico_export($conditions) {
        $registros= $this->Tveiculos->listagem_analitico($conditions);

        header('Content-type: application/vnd.ms-excel');
        header(sprintf('Content-Disposition: attachment; filename="%s"', basename('tveiculos.csv')));
        header('Pragma: no-cache');
        echo iconv('UTF-8', 'ISO-8859-1', 'Local Vistoria;"Entrada/Saida";"Transportador";"Chassi";"Tipo";"Cor";"Tipo Avaria";"Local Avaria";"Fronte";"Lateral";"Data"');
       
        foreach($registros as $registro){
            $registro = $registro['Tveiculos'];
            $linha = "";
            $linha .= '"'. $registro['local'] . '";';
            $linha .= '"'. $registro['entrada_saida'] . '";';            
            $linha .= '"'. iconv('ISO-8859-1', 'UTF-8', $registro['transportador']) . '";';
            $linha .= '"'. $registro['chassi'] . '";';     
            $linha .= '"'. $registro['veiculo_tipo'] . '";';     
            $linha .= '"'. $registro['veiculo_cor'] . '";';     
            $linha .= '"'. $registro['avaria_tipo'] . '";';     
            $linha .= '"'. $registro['avaria_local'] . '";';     
            $linha .= '"'. $registro['fronte'] . '";';     
            $linha .= '"'. $registro['lateral'] . '";';     
            $linha .= '"'. AppModel::dbDateToDate($registro['data']) . '";';
            echo "\n".iconv('UTF-8', 'ISO-8859-1', $linha);
        }
        exit;
    }

    function sintetico_tveiculos_agrupamento_grafico(){
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'Tveiculos');        
        $relatorioListagem = $this->Tveiculos->pesquisaTveiculos($filtros, null, null, null, false, true); 
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

    function sintetico_tveiculos_total_grafico(){
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'Tveiculos');        
        $relatorioListagem = $this->Tveiculos->pesquisaTveiculos($filtros, null, null, null, true, false); 
        $descricao = false;
        $total_com = 0;
        $total_sem = 0;
        foreach ($relatorioListagem as $relatorio) {
            $total_com += $relatorio[0]['com_avaria'];
            $total_sem += $relatorio[0]['sem_avaria'];
        }
        $valor = array($total_com, $total_sem);
        $descricao =  array(
                array('name' => '\'Com Avaria\'','values' => $total_com),
                array('name' => '\'Sem Avaria\'','values' => $total_sem)
            );
        if($descricao){
            $qtd_registros_label = count($descricao);
            $rotate_angle = ($qtd_registros_label < 15 ? -10 : ($qtd_registros_label < 25 ? -45 : -90));
            $dadosGrafico['eixo_x'] = $valor;
            $dadosGrafico['series'] =  $descricao;
            $this->set(compact('dadosGrafico'));
        }        
    }

    function tveiculos_avaria_grafico(){
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'Tveiculos');  

        $tipo_grafico = array(
                'veiculo_avaria' => 'tipo',
                'veiculo_local'  => 'local',
                'veiculo_total'  => 'total',
                'local'          => 'local_vistoria',
                'transportador'  => 'transportadora'
            );

        $dadosGrafico = $this->Tveiculos->pesquisaVeiculoAvaria($tipo_grafico[$filtros['agrupamento']], $filtros, true);        
        $agrupamento = $tipo_grafico[$filtros['agrupamento']];
        $this->set(compact('dadosGrafico', 'agrupamento'));    
    }
    function upload_arquivo(){
        $destino = APP.'webroot'.DS.'files'.DS.'importacao_transyseg'.DS;        
        if (!empty($this->data['Tveiculos']['filename_pic']['name'])) {
            $foto = strtolower($this->data['Tveiculos']['filename_pic']['name']);
            if (strpos($foto, ".jpg") > 0 || strpos($foto, ".gif") || strpos($foto, ".png") > 0 || strpos($foto, ".jpeg") > 0) {
                preg_match("/(\..*){1}$/i", $this->data['Tveiculos']['filename_pic']['name'], $ext);
                $nome_arquivo = date('YmdHis') . '_' .  $this->data['Tveiculos']['codigo_cliente'].$ext[0];
                $destino .= $nome_arquivo;                

                if (!move_uploaded_file($_FILES['data']['tmp_name']['Tveiculos']['filename_pic'], $destino)) {                    
                    $this->Tveiculos->invalidate('filename_pic', 'Informe arquivo .jpg ou .gif ou .png ou .jpeg de até 10MB');
                }else{
                    $this->data['Tveiculos']['filename_pic'] = $nome_arquivo;
                    return true;
                }
            } else {                
                $this->Tveiculos->invalidate('filename_pic', 'Informe arquivo .jpg ou .gif ou .png ou .jpeg de até 10MB');
            }
        }

        if($this->data['Tveiculos']['tem_foto'] === '0'){
            $this->data['Tveiculos']['filename_pic'] = "";
        }else{
            unset($this->data['Tveiculos']['filename_pic']);
        }
        unset($this->data['Tveiculos']['tem_foto']);
    }
    function incluir() {
        $this->pageTitle = 'Incluir Veículo';
        if($this->RequestHandler->isPost()) {
            $this->data['Tveiculos']['filename'] = '';
            $this->upload_arquivo();
            if ($this->Tveiculos->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
    }
    function editar($codigo_tveiculo) {
        $this->pageTitle = 'Atualizar Veículo';
        if (!empty($this->data)) {            
            $this->upload_arquivo();
            if ($this->Tveiculos->atualizar($this->data)) {
                $this->BSession->setFlash('update_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('update_error');
            }
        } else {
            $this->data = $this->Tveiculos->carregar($codigo_tveiculo);            
        }
    }
    function excluir($codigo_tveiculo) {
        if ( $codigo_tveiculo) {
            if ($this->Tveiculos->delete($codigo_tveiculo)) {
                $this->BSession->setFlash('delete_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('delete_error');
            }
        }
    }
}