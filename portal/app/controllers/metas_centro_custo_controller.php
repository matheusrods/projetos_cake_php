<?php
class MetasCentroCustoController extends AppController {
    public $name = 'MetasCentroCusto';
    public $uses = array( 'MetaCentroCusto', 'CentroCusto', 'Grflux', 'Sbflux', 'LojaNaveg', 'GrupoEmpresa' );

    function index(){
        $this->pageTitle = 'Metas por Centro de Custo';
        $this->_carrega_combos();
        $this->data['MetaCentroCusto'] = $this->Filtros->controla_sessao($this->data, 'MetaCentroCusto');
    }

    function _carrega_combos( $grflux=NULL, $filtros = TRUE ){
        
        if($filtros)
            $this->data['MetaCentroCusto'] = $this->Filtros->controla_sessao($this->data, 'MetaCentroCusto');
        $meses = Comum::listMeses();
        $ano   = date('Y', strtotime('-1 year'));
        $anos  = Comum::listAnos($ano);
        array_push($anos, date('Y', strtotime('+1 year')));
        $dados_centro_custo = $this->CentroCusto->find('all', array( 'fields'=>array('codigo', 'descricao'), 'conditions'=>array('descricao <>'=>NULL,'descricao <>'=>'' )));
        $centro_custo = array();
        foreach ($dados_centro_custo as $key => $value ) {
            $centro_custo[$value['CentroCusto']['codigo']] = $value['CentroCusto']['codigo'].' '.$value['CentroCusto']['descricao'];
        }
        $fluxo = $this->Grflux->listar();
        $sub_fluxo  = array();
        if( $grflux )
            $sub_fluxo = $this->Sbflux->listar( $grflux);
        $this->LojaNaveg = ClassRegistry::init('LojaNaveg');    
        $grupo = isset($this->data['MetaCentroCusto']['grupo_empresa']) ? $this->data['MetaCentroCusto']['grupo_empresa'] : '1';
        $empresas = $this->LojaNaveg->listEmpresas($grupo); 
        $grupos_empresas = $this->LojaNaveg->listGrupos();
        $this->set(compact('centro_custo', 'fluxo', 'sub_fluxo', 'anos', 'meses','empresas','grupos_empresas'));
    }

    function listagem( ){
        $filtros    = $this->Filtros->controla_sessao($this->data, 'MetaCentroCusto');
        $conditions = $this->MetaCentroCusto->converteFiltroEmCondition( $filtros );

        $joins = array(
            array(
                "table"  => $this->CentroCusto->databaseTable.'.'.$this->CentroCusto->tableSchema.'.'.$this->CentroCusto->useTable,
                "alias" => "CentroCusto",
                "type"  => "LEFT",
                "conditions" => array(
                        "CentroCusto.codigo = MetaCentroCusto.centro_custo", 
                        "CentroCusto.descricao <> ''"
                )
            ),
            array(
                "table"  => $this->Grflux->databaseTable.'.'.$this->Grflux->tableSchema.'.'.$this->Grflux->useTable,
                "alias" => "Grflux",
                "type"  => "LEFT",
                "conditions" => array("Grflux.codigo = MetaCentroCusto.codigo_fluxo")
            ),
            array(
                "table"  => $this->Sbflux->databaseTable.'.'.$this->Sbflux->tableSchema.'.'.$this->Sbflux->useTable,
                "alias" => "Sbflux",
                "type"  => "LEFT",
                "conditions" => array(
                    "Sbflux.codigo = MetaCentroCusto.codigo_sub_fluxo", 
                    "Sbflux.grflux = Grflux.codigo"
                )
            ),
            array(
                "table"  => $this->LojaNaveg->databaseTable.'.'.$this->LojaNaveg->tableSchema.'.'.$this->LojaNaveg->useTable,
                "alias" => "LojaNaveg",
                "type"  => "LEFT",
                "conditions" => array("LojaNaveg.codigo = MetaCentroCusto.empresa")
            ),
            array(
                "table"  => 'dbNavegarqLider.'.$this->LojaNaveg->tableSchema.'.'.$this->LojaNaveg->useTable,
                "alias" => "LojaNavegarqLider",
                "type"  => "LEFT",
                "conditions" => array("LojaNavegarqLider.codigo = MetaCentroCusto.empresa")
            ),
            array(
                "table"  => 'dbNavegarqNatec.'.$this->LojaNaveg->tableSchema.'.'.$this->LojaNaveg->useTable,
                "alias" => "LojaNavegarqNatec",
                "type"  => "LEFT",
                "conditions" => array("LojaNavegarqNatec.codigo = MetaCentroCusto.empresa")
            ),
            array(
                "table"  => 'dbNavegarqSolen.'.$this->LojaNaveg->tableSchema.'.'.$this->LojaNaveg->useTable,
                "alias" => "LojaNavegarqSolen",
                "type"  => "LEFT",
                "conditions" => array("LojaNavegarqSolen.codigo = MetaCentroCusto.empresa")
            ),
        );  

        $this->MetaCentroCusto->virtualFields['nome_empresa'] = 'MetaCentroCusto__nome_empresa ';
        $this->paginate['MetaCentroCusto'] = array(
            'conditions' => $conditions,
            'joins'      => $joins,
            'fields'     => array(
                                'CentroCusto.descricao', 
                                'Grflux.descricao', 
                                'Sbflux.descricao', 
                                'MetaCentroCusto.ano_mes', 
                                'MetaCentroCusto.valor_meta', 
                                'MetaCentroCusto.codigo', 
                                'MetaCentroCusto.centro_custo',
                                'MetaCentroCusto.grupo_empresa',
                                'CASE 
                                    WHEN MetaCentroCusto.grupo_empresa = 1 THEN LojaNaveg.razaosocia
                                    WHEN MetaCentroCusto.grupo_empresa = 2 THEN LojaNavegarqLider.razaosocia
                                    WHEN MetaCentroCusto.grupo_empresa = 3 THEN LojaNavegarqNatec.razaosocia
                                    WHEN MetaCentroCusto.grupo_empresa = 4 THEN LojaNavegarqSolen.razaosocia
                                 END as MetaCentroCusto__nome_empresa'
                                ),
            'limit'      => 50,
            'order'      => array('MetaCentroCusto.ano_mes ASC', 'MetaCentroCusto.centro_custo ASC' )
        );
        $listagem = $this->paginate('MetaCentroCusto');        
        $grupo_empresa = $this->GrupoEmpresa;
        $this->set(compact('listagem','grupo_empresa'));
    }

    function incluir() {
        $this->pageTitle  = 'Incluir Meta'; 
        $codigo_fluxo = (isset($this->data[$this->MetaCentroCusto->name]['codigo_fluxo']) ?  $this->data[$this->MetaCentroCusto->name]['codigo_fluxo'] : NULL);
        if($this->RequestHandler->isPost()) {            
            if ( $this->MetaCentroCusto->incluir( $this->data ) ) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array( 'action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        $this->_carrega_combos( $codigo_fluxo, false );
    } 
    
    function editar( $codigo ) {

        $this->pageTitle = 'Editar Meta';
        if($this->RequestHandler->isPost()) {
            if ( $this->MetaCentroCusto->atualizar( $this->data ) ) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array( 'action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->MetaCentroCusto->carregar( $codigo );            
            $this->data['MetaCentroCusto']['valor_meta']     = number_format ( $this->data['MetaCentroCusto']['valor_meta'] , 2 , ',' , '' );
            $this->data[$this->MetaCentroCusto->name]['ano'] = substr($this->data[$this->MetaCentroCusto->name]['ano_mes'], 0, 4);
            $this->data[$this->MetaCentroCusto->name]['mes'] = substr($this->data[$this->MetaCentroCusto->name]['ano_mes'], 4, 2);
        }
        $codigo_fluxo = (isset($this->data[$this->MetaCentroCusto->name]['codigo_fluxo']) ?  $this->data[$this->MetaCentroCusto->name]['codigo_fluxo'] : NULL);
        $this->_carrega_combos( $codigo_fluxo, false );
    } 


    function excluir( $codigo ) {
        if ($this->MetaCentroCusto->excluir( $codigo )) {
            $this->BSession->setFlash('delete_success');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->BSession->setFlash('delete_error');
        }
    }

    function carrega_sub_fluxo( $grflux ){
        $this->layout = false;
        $dados  = $this->Sbflux->listar( $grflux );
        $return = array();        
        $return['html'] = '<option value="">Selecione um Sub Fluxo</option>';
        if($dados){
            foreach($dados as $key => $value) {
                $return['html'] .= '<option value="'.$key.'">'.$value.'</option>';
            }
        }
        echo json_encode($return);exit;
    }
    

}
?>