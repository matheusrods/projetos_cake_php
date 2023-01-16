<?php
class DependenciasObjAclController extends AppController {
    public $name = 'DependenciasObjAcl';
    public $uses=array('ObjetoAcl', 'DependenciaObjAcl');
    
    function index($objeto_id) {
        $this->set('objeto', $this->ObjetoAcl->read(null, $objeto_id));
        $this->set('dependencias', $this->DependenciaObjAcl->listaPorObjeto($objeto_id));
    }   
    
    function incluir($objeto_id) {
        $this->pageTitle = 'Incluir Dependência';
        if ($this->RequestHandler->isPost()) {
            if ($this->DependenciaObjAcl->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', $objeto_id));
            } else
                $this->BSession->setFlash('save_error');
        } else {
            $this->data['DependenciaObjAcl']['objeto_id'] = $objeto_id;
        }
        $this->carregarTarefas();
        $this->set('objeto', $this->ObjetoAcl->read(null, $objeto_id));
    }
    
    function editar($id) {
        $this->pageTitle = 'Editar Dependência';
        if ($this->RequestHandler->isPut()) {
            if ($this->DependenciaObjAcl->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', $this->data['DependenciaObjAcl']['objeto_id']));
            } else
                $this->BSession->setFlash('save_error');
        } else {
            $this->data = $this->DependenciaObjAcl->read(null, $id);
        }
        $this->carregarTarefas($this->data['DependenciaObjAcl']['codigo_tarefa_desenvolvimento']);
        $this->set('objeto', $this->ObjetoAcl->read(null, $this->data['DependenciaObjAcl']['objeto_id']));
    }

    function carregarTarefas($tarefa_codigo = null){
        $this->loadModel('TarefaDesenvolvimento');

        
        $conditions = array(
            'status <> ' => 3,
            'tipo' => 1
        );

        $fields = array(
            'codigo',
            'titulo'
        );

        $order = array(
            'titulo'
        );

        $tarefas = $this->TarefaDesenvolvimento->find('list', compact('conditions', 'fields', 'order'));

        if($tarefa_codigo){
            if(!isset($tarefas[$tarefa_codigo])){
                $tarefa = $this->TarefaDesenvolvimento->carregar($tarefa_codigo);
                $tarefas[$tarefa_codigo] = $tarefa['TarefaDesenvolvimento']['titulo'];
            }
        }

        $this->set(compact('tarefas'));
    }
    
    function excluir($id) {
        $dependencia = $this->DependenciaObjAcl->read(null, $id);
        if ($this->DependenciaObjAcl->excluir($id))
            $this->BSession->setFlash('delete_success');
        else
            $this->BSession->setFlash('delete_error');
        $this->redirect(array('action' => 'index', $dependencia['DependenciaObjAcl']['objeto_id']));
    }
}
 