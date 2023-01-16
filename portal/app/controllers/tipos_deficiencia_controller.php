<?php
class TiposDeficienciaController extends AppController {
    public $name = 'TiposDeficiencia';
    var $uses = array('TipoDeficiencia');
    
    
    function index() {
        $this->pageTitle = 'Tipos de Deficiência';
        $this->carrega_combos();
    }
    
    function carrega_combos(){

        $classificacao = array('AUDITIVA' => TipoDeficiencia::AUDITIVA, 'FISICA' =>TipoDeficiencia::FISICA, 'INTELECTUAL' =>TipoDeficiencia::INTELECTUAL, 'MENTAL' => TipoDeficiencia::MENTAL, 'MULTIPLA' => TipoDeficiencia::MULTIPLA, 'VISUAL' => TipoDeficiencia::VISUAL, 'REABILITACAO' => TipoDeficiencia::REABILITACAO);
        $this->set(compact('classificacao'));
    }
    function listagem() {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->TipoDeficiencia->name);
        
        $conditions = $this->TipoDeficiencia->converteFiltroEmCondition($filtros);
        $fields = array('TipoDeficiencia.codigo', 'TipoDeficiencia.descricao', 'TipoDeficiencia.classificacao','TipoDeficiencia.ativo');
        $order = 'TipoDeficiencia.descricao';

        $this->paginate['TipoDeficiencia'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
        );
       
        $tipos = $this->paginate('TipoDeficiencia');
        $this->set(compact('tipos'));
    }
    
    function incluir() {
        $this->pageTitle = 'Incluir Tipos de Deficiência';
        $this->carrega_combos();

        if($this->RequestHandler->isPost()) {
             $this->data ['TipoDeficiencia'] ['descricao'] = strtoupper ( $this->data ['TipoDeficiencia'] ['descricao'] );

            if ($this->TipoDeficiencia->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'tipos_deficiencia'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }
    }
    
    function editar() {
        $this->pageTitle = 'Editar  Tipos de Deficiência'; 
        $this->carrega_combos();       
         if($this->RequestHandler->isPost()) {

            $this->data ['TipoDeficiencia'] ['descricao'] = strtoupper ( $this->data ['TipoDeficiencia'] ['descricao'] );
           
            if ($this->TipoDeficiencia->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'tipos_deficiencia'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        } 

        if (isset($this->passedArgs[0])) {            
            $this->data = $this->TipoDeficiencia->carregar($this->passedArgs[0]);
        }
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['TipoDeficiencia']['codigo'] = $codigo;
        $this->data['TipoDeficiencia']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->TipoDeficiencia->atualizar($this->data, false)) {   
            print 1;
        } else {
            print 0;
        }
        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }
}