<?php
class TecnicasMedicaoController extends AppController {
    public $name = 'TecnicasMedicao';
    var $uses = array('TecnicaMedicao', 'TecnicaMedicaoPpra');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('atualiza_status'));
    }//FINAL FUNCTION beforeFilter 
    
    function index() {
        $this->pageTitle = 'Unidades de Medida';
    }
    
    function listagem() {
    	
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->TecnicaMedicao->name);
        
        $conditions = $this->TecnicaMedicao->converteFiltroEmCondition($filtros);
        $conditions[] = 'TecnicaMedicao.codigo_esocial IS NOT NULL';
        $fields = array('TecnicaMedicao.codigo', 'TecnicaMedicao.nome', 'TecnicaMedicao.ativo', 'TecnicaMedicao.abreviacao');
        $order = 'TecnicaMedicao.nome';

        $this->paginate['TecnicaMedicao'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
        );
       
        $tecnicas_medicao = $this->paginate('TecnicaMedicao');
        $this->set(compact('tecnicas_medicao'));
    }
   
    function incluir() {
        $this->pageTitle = 'Incluir Unidades de Medida';

        if($this->RequestHandler->isPost()) {
			if ($this->TecnicaMedicao->incluir($this->data)) {
            	$this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'tecnicas_medicao'));
			} else {
				$this->BSession->setFlash('save_error');
			}
        }
    }
    
     function editar($codigo) {
        $this->pageTitle = 'Editar Unidades de Medida'; 
        
         if($this->RequestHandler->isPost()) {
         	$this->data['TecnicaMedicao']['codigo'] = $codigo; 
         	
			if ($this->TecnicaMedicao->atualizar($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index', 'controller' => 'tecnicas_medicao'));
			} else {
				$this->BSession->setFlash('save_error');
			}
        } 

        if (isset($this->passedArgs[0])) {            
            $this->data = $this->TecnicaMedicao->carregar( $this->passedArgs[0] );
        }
    }

    /**
     * [atualiza_status description]
     * 
     * metodo para atualizar o status das unidades
     * 
     * @param  [type] $codigo [description]
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    public function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['TecnicaMedicao']['codigo'] = $codigo;
        $this->data['TecnicaMedicao']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->TecnicaMedicao->save($this->data, false)) {   
            print 1;
        } else {
            print 0;
        }
        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }//fim atuliza_status

    public function index_terceiros(){//tela para os clientes poderem cadastrar suas tecnicas de medicao
        $this->pageTitle = 'Técnicas de Medição';
    }//fim

    public function lista_terceiros() {
        
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->TecnicaMedicaoPpra->name);
        if(empty($filtros['codigo_cliente']) && !empty($_SESSION['Auth']['Usuario']['codigo_cliente'])){
            $filtros['codigo_cliente'] = $_SESSION['Auth']['Usuario']['codigo_cliente'];
        }
        //verifica se o codigo cliente para fazer a buscar
        if(!empty($filtros['codigo_cliente'])){
            
            $conditions = $this->TecnicaMedicaoPpra->converteFiltroEmCondition($filtros);        
            $fields = array('TecnicaMedicaoPpra.codigo', 'TecnicaMedicaoPpra.nome', 'TecnicaMedicaoPpra.ativo', 'TecnicaMedicaoPpra.abreviacao', 'TecnicaMedicaoPpra.codigo_cliente');
            $order = 'TecnicaMedicaoPpra.nome';

            $this->paginate['TecnicaMedicaoPpra'] = array(
                    'fields' => $fields,
                    'conditions' => $conditions,
                    'limit' => 50,
                    'order' => $order,
            );            

            $tecnicas_medicao = $this->paginate('TecnicaMedicaoPpra');
            $this->set(compact('tecnicas_medicao'));
        }
    }//fim lista terceiros

    public function incluir_terceiros($codigo_cliente) {
        $this->pageTitle = 'Incluir Técnicas de Medição';

        if($this->RequestHandler->isPost()) {             
            if ($this->TecnicaMedicaoPpra->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index_terceiros', 'controller' => 'tecnicas_medicao'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->set(compact('codigo_cliente'));
    }//fim incluir terceiros

    public function editar_terceiros($codigo, $codigo_cliente) {
        $this->pageTitle = 'Editar Técnicas de Medição'; 
        
        if($this->RequestHandler->isPost()) {
            $this->data['TecnicaMedicaoPpra']['codigo'] = $codigo; 
            $this->data['TecnicaMedicaoPpra']['codigo_cliente'] = $codigo_cliente; 
            
            if ($this->TecnicaMedicaoPpra->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index_terceiros', 'controller' => 'tecnicas_medicao'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        } 

        if (isset($this->passedArgs[0])) {            
            $this->data = $this->TecnicaMedicaoPpra->carregar( $this->passedArgs[0] );
        }

        $this->set(compact('codigo_cliente', 'codigo'));
    }//fim editar terceiros

    private function update_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['TecnicaMedicaoPpra']['codigo'] = $codigo;
        $this->data['TecnicaMedicaoPpra']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->TecnicaMedicaoPpra->save($this->data, false)) {   
            print 1;
        } else {
            print 0;
        }
        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }//fim atuliza_status
}