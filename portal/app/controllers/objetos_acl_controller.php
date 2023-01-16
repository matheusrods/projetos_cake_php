<?php
class ObjetosAclController extends AppController {
    public $name = 'ObjetosAcl';
    public $uses=array('ObjetoAcl');
    public $helpers = array('Tree');
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array());
    }

    function index() {
        $this->loadModel('Uperfil');
        $authUsuario = $this->BAuth->user();
        $codigo_tipo_perfil = $this->Uperfil->codigoTipoPerfil($authUsuario['Usuario']['codigo_uperfil']);
        $this->set('objetos', $this->ObjetoAcl->listaObjetos($codigo_tipo_perfil,null,null,TRUE));
    }   
    
    function incluir() {
        $this->loadModel('TipoPerfil');
        $this->pageTitle = 'Incluir Objeto';
        if ($this->RequestHandler->isPost()) {
            if ($this->ObjetoAcl->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            }else{
                $this->BSession->setFlash('save_error');
            }    
        }
        $this->carregarTarefas();
        $lista_perfis = $this->TipoPerfil->find('list',array('fields' => 'descricao'));
        $this->set('objetos',$this->ObjetoAcl->find('threaded', array('order' => $this->ObjetoAcl->name.'.descricao')));
        $this->set(compact('lista_perfis'));
    }
    
    function editar($id) {
        $this->loadModel('TipoPerfil');
        $this->loadModel('ObjetoAclTipoPerfil');
        $this->pageTitle = 'Editar Objeto';
        if ($this->RequestHandler->isPut()) {
            if ($this->ObjetoAcl->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else
                $this->BSession->setFlash('save_error');
        } else {
            $this->data = $this->ObjetoAcl->read(null, $id);
        }
        $this->carregarTarefas($this->data['ObjetoAcl']['codigo_tarefa_desenvolvimento']);
        $lista_perfis = $this->TipoPerfil->find('list',array('fields' => 'descricao'));
        $this->set('objetos', $this->ObjetoAcl->find('threaded'));
        $conditions['objeto_id'] = $this->data['ObjetoAcl']['id'];
        $listaObjetoTipoPerfil = $this->ObjetoAclTipoPerfil->find('list',array('fields' => 'codigo_tipo_perfil','conditions' => $conditions));
        $this->data['ObjetoAcl']['codigo_tipo_perfil'] = $listaObjetoTipoPerfil;
        $this->set(compact('lista_perfis'));
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
        if ($this->ObjetoAcl->excluir($id))
            $this->BSession->setFlash('delete_success');
        else
            $this->BSession->setFlash('delete_error');
        $this->redirect(array('action' => 'index'));
    }

    function mudar_status($id,$status){
        $this->loadModel('ObjetoAcl');

        $objeto = $this->ObjetoAcl->carregar($id);

        try{
            $this->ObjetoAcl->query("BEGIN TRANSACTION");

            if($status == 0){
                $childrens = $this->ObjetoAcl->getChildrens($objeto);
                foreach($childrens as $child){
                    $child['ObjetoAcl']['homologado'] = 0;
                    if(!$this->ObjetoAcl->atualizar($child)){
                        throw new Exception();
                    }
                }

                $objeto['ObjetoAcl']['homologado'] = 0;
                if(!$this->ObjetoAcl->atualizar($objeto)){
                    throw new Exception();
                }
            }elseif($status == 1){
                $parents = $this->ObjetoAcl->getParents($objeto);
                foreach($parents as $parent){
                    $parent['ObjetoAcl']['homologado'] = 1;
                    if(!$this->ObjetoAcl->atualizar($parent)){
                        throw new Exception();
                    }
                }

                $objeto['ObjetoAcl']['homologado'] = 1;
                if(!$this->ObjetoAcl->atualizar($objeto)){
                    throw new Exception();
                }
            }
            
            $this->ObjetoAcl->commit();
            $this->BSession->setFlash('save_success');
            $this->redirect(array('action' => 'index'));
        }catch(Exception $e){
            $this->ObjetoAcl->rollback();
            $this->BSession->setFlash('save_error');
        }
    }

    function ver_perfis($id){
        $this->pageTitle = 'Perfis por Objeto';
        $this->Acl = new CachedAclComponent();
        $this->AroAco = ClassRegistry::init('AroAco');
        $this->loadModel('ObjetoAcl');
        $this->loadModel('Uperfil');

        $objeto = $this->ObjetoAcl->carregar($id);

        $aros = $this->Acl->Aro->find('all',array(
            'recursive' => -1,
            'joins' => array(
                array(
                    'table' => "{$this->Uperfil->databaseTable}.{$this->Uperfil->tableSchema}.{$this->Uperfil->useTable}",
                    'alias' => 'Uperfil',
                    'type' => 'INNER',
                    'conditions' => array('Uperfil.codigo = Aro.foreign_key')
                ),
            ),
            'fields' => array(
                'Aro.id',
                'Aro.model',
                'Aro.foreign_key',
                'Uperfil.codigo',
                'Uperfil.descricao',
                'Uperfil.codigo_cliente',
            ),
        ));
        $permitidos = array();
        foreach($aros as $aro){
            if($this->Acl->check($aro['Aro'],'buonny/'.str_replace('__','/',$objeto['ObjetoAcl']['aco_string']))){
                $permitidos[] = $aro;
            }
        }

        $this->set(compact('objeto','permitidos'));
    }
}
 