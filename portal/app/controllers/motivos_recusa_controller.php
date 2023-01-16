<?php
class MotivosRecusaController extends AppController {
    public $name = 'MotivosRecusa';
    var $uses = array(
        'MotivoRecusa',
        'MotivoRecusaExame',
        'Cliente'
    );
    

    function index() {
        $this->pageTitle = 'Motivos de Recusa';
    }
   
    function listagem() {
        $this->layout = 'ajax'; 

        $filtros = $this->Filtros->controla_sessao($this->data, $this->MotivoRecusa->name);
        
        $conditions = $this->MotivoRecusa->converteFiltroEmCondition($filtros);

        $fields = array('MotivoRecusa.codigo', 'MotivoRecusa.descricao', 'MotivoRecusa.ativo', 'MotivoRecusa.codigo_empresa');
        $order = 'MotivoRecusa.descricao';        

        $codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];

        if(isset($codigo_empresa)){
            $conditions = array( 'MotivoRecusa.codigo_empresa' => $codigo_empresa);
        }

        $this->paginate['MotivoRecusa'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
        );
       
        $motivos_recusa = $this->paginate('MotivoRecusa');

        $this->set(compact('motivos_recusa'));
    }
    
    function incluir() {
        $this->pageTitle = 'Incluir Motivos de Recusa';

        if($this->RequestHandler->isPost()) {
            $this->data['MotivoRecusa']['descricao'] = strtoupper($this->data['MotivoRecusa']['descricao']);

            if ($this->MotivoRecusa->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'motivos_recusa', 'action' => 'index'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }
    }
    
    function editar() {
        $this->pageTitle = 'Editar Motivos de Recusa'; 
        
        if($this->RequestHandler->isPost()) {
            $this->data['MotivoRecusa']['descricao'] = strtoupper($this->data['MotivoRecusa']['descricao']);

            if ($this->MotivoRecusa->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'motivos_recusa', 'action' => 'index'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        } 
        if (isset($this->passedArgs[0])) {   
            $this->data = $this->MotivoRecusa->find('first', array('conditions' => array('codigo' => $this->passedArgs[0])));            
        }
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['MotivoRecusa']['codigo'] = $codigo;
        $this->data['MotivoRecusa']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->MotivoRecusa->atualizar($this->data, false)) {   
            print 1;
        } else {
            print 0;
        }
        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }

    /**
     * EXAMES
    */
    public function exames_index(){
        $this->pageTitle = 'Motivo Recusa Exame';
        $status = array('' => 'Todos', '0' => 'Desativado', '1' => 'Ativado');

        $this->set(compact('status'));
    }

    public function exames_listagem(){
        $filtros = $this->Filtros->controla_sessao($this->data, $this->MotivoRecusaExame->name);

        if(!is_array($filtros))
            $filtros = array();

        $this->paginate['MotivoRecusaExame'] = $this->MotivoRecusaExame->getAll($filtros, true);
        $mrexames = $this->paginate('MotivoRecusaExame');

        $this->set(compact('mrexames'));
    }

    public function exames_status(){
        $this->autoRender = false;

        $r = $this->MotivoRecusaExame->setStatus($this->params['url']['codigo'], $this->params['url']['ativo']);
        if($r){
            $return = array(
                'status' => 'success',
                'message' => 'Status do motivo recusa de exames realizada com sucesso!',
            );
        }else{
            $return = array(
                'status' => 'error',
                'message' => 'Oops, Não conseguimos alterar o status do motivo recusa de exames',
            );
        }

        return json_encode($return);
    }

    public function exames_incluir(){
        $this->pageTitle = 'Incluir Motivos de Recusa Exame';
        if($this->RequestHandler->isPost()){
            $this->data['MotivoRecusaExame']['descricao'] = strtoupper($this->data['MotivoRecusaExame']['descricao']);

            if($this->MotivoRecusaExame->incluir($this->data)){
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'motivos_recusa', 'action' => 'exames_index'));
            }
            else{
                $this->BSession->setFlash('save_error');
            }
        }
    }

    public function exames_editar(){
        $this->pageTitle = 'Editar Motivos de Recusa';

        if($this->RequestHandler->isPost()) {
            $this->data['MotivoRecusaExame']['descricao'] = strtoupper($this->data['MotivoRecusaExame']['descricao']);

            if ($this->MotivoRecusaExame->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'motivos_recusa', 'action' => 'exames_index'));
            }
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        $mrexame = $this->MotivoRecusaExame->find('first', array('conditions' => array('codigo' => $this->passedArgs[0])));

        $this->set(compact('mrexame'));
    }


    /**
     * FIM EXAMES
     */
}