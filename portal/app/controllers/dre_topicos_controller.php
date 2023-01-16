<?php
class DreTopicosController extends AppController {

    public $name = 'DreTopicos';
//    public $components = array('Filtros', 'DbbuonnyMonitora');
    public $uses = array('DreTopico', 'DreTopicoRegra', 'Ccusto', 'Grflux', 'Sbflux');
    public $helpers = array('Js');

    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('*'));
    }
    
    function index(){
    	$this->pageTitle = 'Tópicos DRE';
    	
    	$topicos = $this->DreTopico->topicosOrdenadosParaVisualizacao();
    	
    	$this->Grflux->bindModel(array('hasMany'=>array('Sbflux'=>array('class'=>'Sbflux', 'foreignKey'=>'grflux'))));
    	$this->Grflux->Behaviors->attach('Containable');
    	$grflux = $this->Grflux->find('all', array('conditions'=>array('codigo'=>array_unique(Set::extract($topicos, '/DreTopicoRegra/grflux'))), 'fields'=>array('Grflux.codigo', 'Grflux.descricao'), 'contain'=>array('Sbflux.codigo', 'Sbflux.descricao')));
		$this->set(compact('topicos', 'grflux'));
    }

    function incluir() {
        $this->pageTitle = 'Incluir Tópico DRE';
        if($this->RequestHandler->isPost()) {
            if ($this->DreTopico->incluir($this->data)) {
            	$this->DreTopico->atualizaOrdenacao($this->DreTopico->id);
            	$this->DreTopicoRegra->atualizar($this->DreTopico->id, $this->data['DreTopicoRegra']);
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else
                $this->BSession->setFlash('save_error');
        }
        $ccusto = $this->Ccusto->listar();
        $grflux = $this->Grflux->listar();
        
        $this->set(compact('ccusto', 'grflux'));
    	$this->render('editar');
    }
    
    function editar($codigo) {
        $this->pageTitle = 'Atualizar Tópico DRE';
        if (!$codigo && empty($this->data)) {
            $this->BSession->setFlash('codigo_invalido');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->DreTopico->atualizar($this->data)) {
            	$this->DreTopicoRegra->atualizar($this->DreTopico->id, $this->data['DreTopicoRegra']);
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else
                $this->BSession->setFlash('save_error');
        }
        if (empty($this->data))
            $this->data = $this->DreTopico->find('first', array('conditions'=>array('codigo'=>$codigo), 'recursive'=>1));
        
        $ccusto = $this->Ccusto->listar();
        $grflux = $this->Grflux->listar();
        
        $this->set(compact('ccusto', 'grflux'));
    }
    
    function excluir($codigo) {
    	if (!$codigo) {
    		$this->BSession->setFlash('codigo_invalido');
    	} else{
    		$this->DreTopico->atualizaOrdenacao($codigo);
    		if ($this->DreTopico->excluir($codigo)) {
    			$this->BSession->setFlash('save_success');
	    	}
    	}
    	
    	$this->redirect(array('action' => 'index'));
    }
    
    function atualizar_ordenacao($codigo, $posicao_nova){
    	$this->render = false;
    	
    	$this->DreTopico->atualizaOrdenacao($codigo, $posicao_nova);
    }
    
    public function lista_subgrupos($grflux) {
    	$this->layout = "ajax";
    	$sbflux = $this->Sbflux->listar($grflux);
    	$this->set(compact('sbflux'));
    }
}