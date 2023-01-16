<?php
class LoadplansController extends AppController {

    public $name = 'Loadplans';
    public $uses = array('TLoadLoadplan');    
    public $helpers = array('Highcharts','Buonny', 'Paginator', 'Html');

    function analitico($status_loadplan = NULL){
        $this->layout = 'new_window';
        $this->pageTitle = "Loadplan Analítico";
        if($status_loadplan)
        $this->data['TLoadLoadplan']['status_loadplan'] = $status_loadplan;
        $this->data['TLoadLoadplan'] = $this->Filtros->controla_sessao($this->data, $this->TLoadLoadplan->name);
        $this->carregarCombos();
    }

    function carregarCombos(){
        $this->loadModel('StatusViagem');
        $status_viagens = $this->StatusViagem->listarParaLoadplan();
        $this->set( compact('status_viagens') );
    }

    function analitico_listagem($export = false){
        $this->layout = 'ajax';
        $filtro     = $this->Filtros->controla_sessao($this->data, $this->TLoadLoadplan->name);
        $conditions = $this->TLoadLoadplan->convertFiltroEmConditionsAnalitico($filtro);
        
        if($export){
            $this->paginate['TLoadLoadplan'] = array(
            'conditions' => $conditions,
            'recursive'  => 1, 
            'extra' => array('method'=>'export'),
            );  
            $dados = $this->paginate('TLoadLoadplan');
            $this->exportAnaliticoListagem($dados);
        }
        $this->paginate['TLoadLoadplan'] = array(
        'conditions' => $conditions,
        'recursive'  => 1, 
        'extra'      => array('method'=>'listar_analitico'),
        'limit' => 50,
        );

        $listagem  = $this->paginate('TLoadLoadplan');        
        $this->set(compact('listagem'));
    }

    function loadplan_sintetico() {
        $this->carregarCombos();
        $this->pageTitle = 'Loadplan - Sintético';
        $authUsuario = $this->BAuth->user();
        if (!empty($authUsuario['Usuario']['codigo_cliente']))
            $this->data['TLoadLoadplan']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
        $filtros = $this->Filtros->controla_sessao($this->data, "TLoadLoadplan");
        $this->data['TLoadLoadplan'] = $filtros;
        $this->set(compact('authUsuario'));
    }
    
    function sintetico_listagem() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, "TLoadLoadplan");
        if(isset($filtros['status_loadplan']))
            unset($filtros['status_loadplan']);
        $conditions = $this->TLoadLoadplan->convertFiltroEmConditionsAnalitico($filtros);
        if(!$conditions)
            $conditions  = 'WHERE 1 = 0';

        $dados = $this->TLoadLoadplan->listarConsolidado($conditions);
        $loadplan_utilizado = TLoadLoadplan::UTILIZADOS;
        $loadplan_nao_utilizado = TLoadLoadplan::NAO_UTILIZADOS;
        $loadplan_parcialmente_utilizado = TLoadLoadplan::PARCIALMENTE_UTILIZADOS;
        $this->set(compact(
            'filtros', 
            'dados', 
            'loadplan_utilizado',
            'loadplan_nao_utilizado',
            'loadplan_parcialmente_utilizado',
            'conditions'
            ));
    }

    function sintetico_sm_listagem() {
        App::Import('Component',array('DbbuonnyGuardian'));
        $this->loadModel('TViagViagem');
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, "TLoadLoadplan");
        $conditionsSm = array();
        if(!$filtros['load_loadplan']){
            if(isset($filtros['codigo_cliente']) && $filtros['codigo_cliente']){
                $pjur_codigo = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($filtros['codigo_cliente'],$filtros['base_cnpj']);
                $conditionsSm['OR'] = array('TViagViagem.viag_tran_pess_oras_codigo' => $pjur_codigo,'TViagViagem.viag_emba_pjur_pess_oras_codigo' => $pjur_codigo);
            }

            if ((isset($filtros['data_inicial_load']) && !empty($filtros['data_inicial_load'])) && (isset($filtros['data_final_load']) && !empty($filtros['data_final_load'])))
                $conditionsSm['viag_previsao_inicio BETWEEN ? AND ?'] = array(AppModel::dateTimeToDbDateTime2($filtros['data_inicial_load'].' 00:00:00'), AppModel::dateTimeToDbDateTime2($filtros['data_final_load'].' 23:59:59'));
        }
        if(!$conditionsSm || empty($filtros['data_inicial_load']) || empty($filtros['data_final_load']))
            $conditionsSm  = 'WHERE 1 = 0';

        $dadosSm = $this->TViagViagem->listarSmPorTipoTransporte($conditionsSm);
        $this->set(compact('filtros','dadosSm','conditionsSm'));
    }

    function exportAnaliticoListagem($query){
        $dbo = $this->TLoadLoadplan->getDataSource();
        $dbo->results = $dbo->_execute($query);
        header('Content-type: application/vnd.ms-excel');
        header(sprintf('Content-Disposition: attachment; filename="%s"', basename('listagem_analitica_loadplan.csv')));
        header('Pragma: no-cache');
        echo iconv('UTF-8', 'ISO-8859-1', '"Loadplan";"Load Origem";"Load Destino";"Load Data Cadastro";"Load Data Finalização";"Transportadora";"SM";"SM Origem";"SM Destino";"SM Data Início";"SM Data Fim";')."\n";
        while ($dado = $dbo->fetchRow()) { 
                $linha  = '"'.$dado[0]['load_loadplan'].'";'; 
                $linha .= '"'.$dado[0]['origem_refe_descricao'].'";';
                $linha .= '"'.$dado[0]['destino_refe_descricao'].'";';
                $linha .= '"'.AppModel::dbDatetoDate($dado[0]['load_data_cadastro']).'";';
                $linha .= '"'.AppModel::dbDatetoDate($dado[0]['load_data_finalizado']).'";';
                $linha .= '"'.$dado[0]['loadplan_pjur_razao_social'].'";'; 
                $linha .= '"'.$dado[0]['viag_codigo_sm'].'";'; 
                $linha .= '"'.$dado[0]['origem_sm'] .'";'; 
                $linha .= '"'.$dado[0]['destino_sm'].'";'; 
                $linha .= '"'.AppModel::dbDatetoDate($dado[0]['viag_data_inicio']).'";'; 
                $linha .= '"'.AppModel::dbDatetoDate($dado[0]['viag_data_fim']).'";'; 
                $linha .= "\n";
                echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
        }
        die();

    }
}
