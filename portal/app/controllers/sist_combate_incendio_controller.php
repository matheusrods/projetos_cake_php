<?php
class SistCombateIncendioController extends AppController {
    public $name = 'SistCombateIncendio';
    var $uses = array('SistCombateIncendio', 'TipoSistIncendio', 'Setor', 'Cliente');
    
    
    function index() {
        $this->pageTitle = 'Cadastro de Sistemas de Combate de Incêndio';
        
		$filtros = $this->Filtros->controla_sessao($this->data, $this->SistCombateIncendio->name);
		$this->data['SistCombateIncendio'] = $filtros;

		$this->set('array_tipo', $this->TipoSistIncendio->find('list', array('fields' => array('codigo', 'nome'), 'order' => array('nome ASC'))));
        $this->set('array_setor', $this->Setor->find('list', array('conditions' => array('ativo' => true), 'fields' => array('codigo', 'descricao'), 'order' => array('descricao ASC'))));
        $this->set('array_cliente', $this->Cliente->find('list', array(
        	'conditions' => array('ativo' => true),
        	'fields' => array('Cliente.codigo', 'Cliente.razao_social'), 
        	'order' => array('Cliente.razao_social ASC')
        )));    
    }
    
    function listagem() {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->SistCombateIncendio->name);
        
        $conditions = $this->SistCombateIncendio->converteFiltroEmCondition($filtros);
        $joins = array(
			array(
            	'table'      => 'cliente',
                'alias'      => 'Cliente',
                'conditions' => 'Cliente.codigo = SistCombateIncendio.codigo_unidade',
                'type'       => 'inner'
			),
			array(
            	'table'      => 'setores',
                'alias'      => 'Setor',
                'conditions' => 'Setor.codigo = SistCombateIncendio.codigo_setor',
                'type'       => 'inner'
			),
			array(
            	'table'      => 'tipos_sist_incendio',
                'alias'      => 'TipoSistIncendio',
                'conditions' => 'TipoSistIncendio.codigo = SistCombateIncendio.tipo',
                'type'       => 'inner'
			),														
		);
		
        $fields = array('SistCombateIncendio.ativo', 'SistCombateIncendio.revisor', 'SistCombateIncendio.codigo', 'SistCombateIncendio.codigo_unidade', 'SistCombateIncendio.codigo_setor', 'SistCombateIncendio.tipo', 'Cliente.razao_social', 'Setor.descricao', 'TipoSistIncendio.nome');
        $order = 'TipoSistIncendio.nome ASC, Cliente.razao_social ASC, Setor.descricao ASC';
        
        $this->paginate['SistCombateIncendio'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
        		'joins' => $joins,
                'limit' => 50,
                'order' => $order,
        );
       
        $sist_combate_incendio = $this->paginate('SistCombateIncendio');
        
        $this->set('array_tipo', $this->TipoSistIncendio->find('list', array('fields' => array('codigo', 'nome'), 'order' => array('nome ASC'))));
        $this->set('array_setor', $this->Setor->find('list', array('conditions' => array('ativo' => true), 'fields' => array('codigo', 'descricao'), 'order' => array('descricao ASC'))));
        $this->set('array_cliente', $this->Cliente->find('list', array(
        	'conditions' => array('ativo' => true),
        	'fields' => array('Cliente.codigo', 'Cliente.razao_social'), 
        	'order' => array('Cliente.razao_social ASC')
        )));

        
        $this->set(compact('sist_combate_incendio'));
    }
   
    function incluir() {
        $this->pageTitle = 'Incluir Sistemas de Combate Incendio';

        if($this->RequestHandler->isPost()) {
			if ($this->SistCombateIncendio->incluir($this->data)) {
            	$this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'sist_combate_incendio'));
			} else {
				$this->BSession->setFlash('save_error');
			}
        }
        
		$this->set('array_tipo', $this->TipoSistIncendio->find('list', array('fields' => array('codigo', 'nome'), 'order' => array('nome ASC'))));
        $this->set('array_setor', $this->Setor->find('list', array('conditions' => array('ativo' => true), 'fields' => array('codigo', 'descricao'), 'order' => array('descricao ASC'))));
    }
    
     function editar($codigo) {
        $this->pageTitle = 'Editar Sistemas de Combate de Incêncio'; 
        
        if($this->RequestHandler->isPost()) {
        	$this->data['SistCombateIncendio']['codigo'] = $codigo;
        	
			if ($this->SistCombateIncendio->atualizar($this->data)) {
            	$this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'sist_combate_incendio'));
			} else {
				$this->BSession->setFlash('save_error');
			}
        } else {
            if (isset($this->passedArgs[0])) {
 	           $this->data = $this->SistCombateIncendio->carregar($this->passedArgs[0]);
 	           
 	           foreach(explode(",", $this->data['SistCombateIncendio']['classe_fogo']) as $key => $campo) {
 	           		$this->data['classe_fogo'][$campo]['classe'] = 1;
 	           }
    	    }   
        }

        if (isset($this->passedArgs[0])) {            
			$this->set('array_tipo', $this->TipoSistIncendio->find('list', array('fields' => array('codigo', 'nome'), 'order' => array('nome ASC'))));
	        $this->set('array_setor', $this->Setor->find('list', array('conditions' => array('ativo' => true), 'fields' => array('codigo', 'descricao'), 'order' => array('descricao ASC'))));
        }
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['SistCombateIncendio']['codigo'] = $codigo;
        $this->data['SistCombateIncendio']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->SistCombateIncendio->save($this->data, false)) {   
            print 1;
        } else {
            print 0;
        }
        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }
}