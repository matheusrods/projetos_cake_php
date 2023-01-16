<?php
class MedicaoController extends AppController {
    public $name = 'Medicao';
    var $uses = array('Medicao', 'Cargo', 'Risco', 'Setor', 'Cliente');
    
    function index() {
        $this->pageTitle = 'Cadastro de Medição';
        
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Medicao->name);
		$this->data['Medicao'] = $filtros;

		$this->set('array_risco', $this->Risco->find('list', array('fields' => array('codigo', 'nome_agente'), 'order' => array('nome_agente ASC'))));
        $this->set('array_cargo', $this->Cargo->find('list', array('conditions' => array('ativo' => true), 'fields' => array('codigo', 'descricao'), 'order' => array('descricao ASC'))));
        $this->set('array_setor', $this->Setor->find('list', array('conditions' => array('ativo' => true), 'fields' => array('codigo', 'descricao'), 'order' => array('descricao ASC'))));
    }
    
    function listagem() {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Medicao->name);
        
        $conditions = $this->Medicao->converteFiltroEmCondition($filtros);
        $joins = array(
			array(
            	'table'      => 'cliente',
                'alias'      => 'Cliente',
                'conditions' => 'Cliente.codigo = Medicao.unidade',
                'type'       => 'inner'
			),
			array(
            	'table'      => 'cargos',
                'alias'      => 'Cargo',
                'conditions' => 'Cargo.codigo = Medicao.codigo_cargo',
                'type'       => 'inner'
			),
			array(
            	'table'      => 'setores',
                'alias'      => 'Setor',
                'conditions' => 'Setor.codigo = Medicao.codigo_setor',
                'type'       => 'inner'
			),
			array(
            	'table'      => 'riscos',
                'alias'      => 'Risco',
                'conditions' => 'Risco.codigo = Medicao.codigo_risco',
                'type'       => 'inner'
			),											
		);
		
        $fields = array('Medicao.codigo', 'Medicao.unidade', 'Medicao.codigo_setor', 'Medicao.codigo_cargo', 'Medicao.codigo_risco', 'Cliente.razao_social', 'Setor.descricao', 'Cargo.descricao', 'Risco.nome_agente');
        $order = 'Risco.nome_agente ASC, Cliente.razao_social ASC, Setor.descricao ASC, Cargo.descricao ASC';

        $this->paginate['Medicao'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
        		'joins' => $joins,
                'limit' => 50,
                'order' => $order,
        );
        
        $this->set('array_risco', $this->Risco->find('list', array('fields' => array('codigo', 'nome_agente'), 'order' => array('nome_agente ASC'))));
        $this->set('array_cargo', $this->Cargo->find('list', array('conditions' => array('ativo' => true), 'fields' => array('codigo', 'descricao'), 'order' => array('descricao ASC'))));
        $this->set('array_setor', $this->Setor->find('list', array('conditions' => array('ativo' => true), 'fields' => array('codigo', 'descricao'), 'order' => array('descricao ASC'))));
        $this->set('array_cliente', $this->Cliente->find('list', array(
        	'conditions' => array('ativo' => true),
        	'fields' => array('Cliente.codigo', 'Cliente.razao_social'), 
        	'order' => array('Cliente.razao_social ASC')
        )));

        $medicao = $this->paginate('Medicao');
        $this->set(compact('medicao'));
    }
   
    function incluir() {
        $this->pageTitle = 'Incluir Medição';

        if($this->RequestHandler->isPost()) {
        	
	        if ($this->Medicao->incluir($this->data)) {
    	        $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'medicao'));
			} else {
				$this->BSession->setFlash('save_error');
			}
        }
        
        $this->set('array_risco', $this->Risco->find('list', array('conditions' => array('ativo' => 1),'fields' => array('codigo', 'nome_agente'), 'order' => array('nome_agente ASC'))));
       	$this->set('array_cargo', $this->Cargo->find('list', array('conditions' => array('ativo' => true), 'fields' => array('codigo', 'descricao'), 'order' => array('descricao ASC'))));
        $this->set('array_setor', $this->Setor->find('list', array('conditions' => array('ativo' => true), 'fields' => array('codigo', 'descricao'), 'order' => array('descricao ASC'))));
    }
    
     function editar($codigo) {
        $this->pageTitle = 'Editar Medição'; 
        
        if($this->RequestHandler->isPost()) {
        	
        	$this->data['Medicao']['codigo'] = $codigo;
			if ($this->Medicao->atualizar($this->data)) {
            	$this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'medicao'));
			} else {
				$this->BSession->setFlash('save_error');
			}
        } 

		if (isset($this->passedArgs[0])) {
            $this->data = $this->Medicao->carregar( $this->passedArgs[0] );
            $conditions['OR'] = array('ativo' => 1,'codigo' => $this->data['Medicao']['codigo_risco']);
            $this->set('array_risco', $this->Risco->find('list', array('conditions' => $conditions,'fields' => array('codigo', 'nome_agente'), 'order' => array('nome_agente ASC'))));
	        $this->set('array_cargo', $this->Cargo->find('list', array('conditions' => array('ativo' => true), 'fields' => array('codigo', 'descricao'), 'order' => array('descricao ASC'))));
	        $this->set('array_setor', $this->Setor->find('list', array('conditions' => array('ativo' => true), 'fields' => array('codigo', 'descricao'), 'order' => array('descricao ASC'))));
        }
    }
}