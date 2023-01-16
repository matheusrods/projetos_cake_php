<?php
class MotivosAcrescimoController extends AppController {
    public $name = 'MotivosAcrescimo';
    public $uses = array('MotivosAcrescimo');

    public function beforeFilter() {
        parent::beforeFilter();
        
        $this->_model = $this->MotivosAcrescimo;
        $this->_modelName = $this->MotivosAcrescimo->name;
        $this->_labelName = 'Motivos Acréscimo';
        $this->_controllerName = 'motivos_acrescimo';
        
        $this->data['viewOptions'] = array(
            '_modelName' => $this->_modelName,
            '_labelName' => $this->_labelName,
            '_controllerName' => $this->_controllerName
        );
        
    }   

    public function index() {
        
        $this->pageTitle = $this->_labelName;
        $this->data[$this->_modelName] = $this->Filtros->controla_sessao($this->data, $this->_modelName);
    }

    public function listagem() {
        $this->layout = 'ajax';
        
        // $viewOptions = $this->Session->read('viewOptions'.$this->_controllerName);

        $filtros = $this->Filtros->controla_sessao($this->data, $this->_modelName);
        $conditions = $this->_model->converteFiltroEmCondition($filtros);
        $codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];

        if(isset($codigo_empresa)){
            $conditions = array('MotivosAcrescimo.codigo_empresa' => $codigo_empresa);
		}

        $fields = array(
            $this->_modelName.'.codigo', 
            $this->_modelName.'.descricao', 
            $this->_modelName.'.ativo',
            $this->_modelName.'.codigo_empresa'
        );

        $order = $this->_modelName.'.descricao';

        $this->paginate[$this->_modelName] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order
        );

        $registros = $this->paginate($this->_modelName);

        $this->set(compact('registros'));    
    }

    public function incluir() {

        $this->pageTitle = 'Incluir '.$this->_labelName;
       
        if($this->RequestHandler->isPost()) {

            if ($this->_model->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => $this->_controllerName));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }
    }

    public function editar() {

        $this->pageTitle = 'Editar '.$this->_labelName;

        if($this->RequestHandler->isPost()) {

            if ($this->_model->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => $this->_controllerName));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        if (isset($this->passedArgs[0])) {            
            $this->data = $this->_model->carregar($this->passedArgs[0]);
        }        
    }

   public function atualiza_status($codigo, $status) {
        $this->layout = 'ajax';

        $this->data[$this->_modelName]['codigo'] = $codigo;
        $this->data[$this->_modelName]['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->_model->atualizar($this->data, false)) {   
            echo 1;
        } else {
            echo 0;
        }
        $this->render(false,false);
   }
    
}