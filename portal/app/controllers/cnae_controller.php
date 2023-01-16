<?php
class CnaeController extends AppController {
    public $name = 'Cnae';
    var $uses = array('Cnae','CnaeSecao');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array("listagem_visualizar"));
    }

    function index() {
        $this->pageTitle = 'CNAE';
        $this->carrega_combos();
        $this->data['Cnae'] = $this->Filtros->controla_sessao($this->data, $this->Cnae->name);

    }

    function carrega_combos(){
        $secao = $this->CnaeSecao->find('list', array(
          'fields' => array('secao','secao'), 
          'order' => 'secao')
        );

        $this->set(compact('secao'));   
    }

    function listagem() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Cnae->name);
        $conditions = $this->Cnae->converteFiltroEmCondition($filtros);

        $fields = array('Cnae.codigo','Cnae.cnae', 'Cnae.secao', 'Cnae.descricao','Cnae.grau_risco');
        $order = 'Cnae.cnae';

        $this->paginate['Cnae'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order
        );

        $cnae = $this->paginate('Cnae');

        $this->set(compact('cnae'));    
    }

    function incluir() {
        $this->pageTitle = 'Incluir CNAE';
        $this->carrega_combos();

        if($this->RequestHandler->isPost()) {
            if ($this->Cnae->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'cnae'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }
    }

    function editar() {
        $this->pageTitle = 'Editar CNAE'; 
        $this->carrega_combos();

        if($this->RequestHandler->isPost()) {

            if ($this->Cnae->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'cnae'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        if (isset($this->passedArgs[0])) {            
            $this->data = $this->Cnae->carregar($this->passedArgs[0]);
        }        
    }

    public function excluir($codigo) {
        if($this->Cnae->excluir($codigo)) {
            $this->BSession->setFlash('delete_success');
        } else {
            $this->BSession->setFlash('delete_error');
        }
        $this->redirect(array('action' => 'index'));
    }

    function busca_cnae() {
    	$this->layout = 'ajax_placeholder';
    	$searcher = !empty($this->passedArgs['searcher']) ? $this->passedArgs['searcher'] : '';
    	$display = !empty($this->passedArgs['display']) ? $this->passedArgs['display'] : $this->data['Cnae']['display'];
    
    	$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, $this->Cnae->name);
    
    	$this->set(compact('searcher', 'display'));
    }
    
    function listagem_visualizar($destino) {
    	
    	$this->layout = 'ajax';
    	$filtros = $this->Filtros->controla_sessao($this->data, $this->Cnae->name);
    	$conditions = $this->Cnae->converteFiltroEmCondition($filtros);
    	$this->paginate['Cnae'] = array(
    			'recursive' => 1,
    			'joins' => null,
    			'conditions' => $conditions,
    			'limit' => 10,
    			'order' => 'Cnae.cnae',
    	);
    	
    	$cnaes = $this->paginate('Cnae');
    	$this->set(compact('cnaes', 'destino'));
    	
    	if (isset($this->passedArgs['searcher']))
    		$this->set('input_id', str_replace('-search', '', $this->passedArgs['searcher']));
    	
    	if (isset($this->passedArgs['display']))
    		$this->set('input_display', str_replace('-search', '', $this->passedArgs['display']));    	
    	
    }
}
