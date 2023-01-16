<?php
class EpiController extends AppController {
    public $name = 'epi';
    var $uses = array(
        'Epi', 
        'Risco',
        'EpiRisco',
        'EpiExterno'
        );
    

    public function beforeFilter() {
        parent::beforeFilter();
        //$this->BAuth->allow(array('index_externo','listagem_externo','editar_externo'));
    }

    function index() {
        $this->pageTitle = 'Cadastro de EPI';
    }
    
    function listagem() {
         // PD-139
         $codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];

        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Epi->name);
        $conditions = $this->Epi->converteFiltroEmCondition($filtros);
        
        $fields = array('Epi.codigo', 'Epi.nome', 'Epi.numero_ca', 'Epi.data_validade_ca', 'Epi.ativo', 'Epi.codigo_empresa');
        $order = 'Epi.nome';
        // PD-139
        if(isset($codigo_empresa)){
            $conditions = array('Epi.codigo_empresa' => $codigo_empresa);
        }

        $this->paginate['Epi'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
        );
       
        $epi = $this->paginate('Epi');

        $this->set(compact('epi'));
    }
   
    function incluir() {
        $this->pageTitle = 'Incluir Epi';

        if($this->RequestHandler->isPost()) {           
            if ($this->Epi->incluir($this->data)) {
                $codigo_epi = $this->Epi->id;
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'editar', 'controller' => 'epi',$codigo_epi));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }
    }
    
     function editar($codigo) {
        $this->pageTitle = 'Editar Epi'; 
        
         if($this->RequestHandler->isPost()) {

         	$this->data['Epi']['codigo'] = $codigo;

			if ($this->Epi->atualizar($this->data)) {
            	$this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'epi'));
			}
            else {
				$this->BSession->setFlash('save_error');
			}
        }
        
        
       	if (isset($this->passedArgs[0])) {
            $this->data = $this->Epi->find('first', array('conditions' => array('codigo' => $this->passedArgs[0])));
        	
            $riscos =  $this->EpiRisco->find('first', array('conditions' => array('codigo_epi' => $this->passedArgs[0])));      	
            $this->set(compact('riscos'));
    	}
    }
    
    function atualiza_status($codigo, $status) {
        $this->layout = 'ajax';
        
        $this->data['Epi']['codigo'] = $codigo;
        $this->data['Epi']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->Epi->save($this->data, false)) {   
            print 1;
        } else {
            print 0;
        }
        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }    

    function buscar_epi($linha, $codigo_risco){
        $this->layout = 'ajax_placeholder';
        $this->data = $this->Filtros->controla_sessao($this->data, $this->Epi->name);

        $this->set(compact('codigo_risco', 'linha'));
    }

    function listagem_buscar_epi($linha, $codigo_risco){
        
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Epi->name);
        $conditions = $this->Epi->converteFiltroEmCondition($filtros);
        $conditions = array_merge($conditions,array('codigo_risco' => $codigo_risco));
        
        $fields = array('Epi.codigo', 'Epi.nome', 'Epi.numero_ca', 'Epi.data_validade_ca', 'Epi.atenuacao_qtd', 'EpiRisco.codigo_risco');
        $order = array('Epi.nome');

        $joins  = array(
            array(
              'table' => $this->EpiRisco->databaseTable.'.'.$this->EpiRisco->tableSchema.'.'.$this->EpiRisco->useTable,
              'alias' => 'EpiRisco',
              'type' => 'LEFT',
              'conditions' => 'EpiRisco.codigo_epi = Epi.codigo',
            ),
        );

        $this->paginate['Epi'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'joins' => $joins,  
                'limit' => 10,
                'order' => $order,
        );

        $dados_epi = $this->paginate('Epi'); 
      
        $this->set(compact('dados_epi', 'codigo_risco', 'linha'));
    }

    function index_externo() {
        $this->pageTitle = 'EPI Externos';
        $this->data[$this->EpiExterno->name] = $this->Filtros->controla_sessao($this->data, $this->EpiExterno->name);
    }

     function listagem_externo() {
        $this->layout = 'ajax';
        $epi = array();
        $listagem = false;
        $filtros = $this->Filtros->controla_sessao($this->data, $this->EpiExterno->name);

        $this->loadModel('GrupoEconomico');        
        $codigo_cliente_filial = $filtros['codigo_cliente'];
        $codigo_cliente_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente_filial);

        if(!empty($filtros['codigo_cliente'])){

            $conditions = $this->EpiExterno->converteFiltroEmCondition($filtros);

            $fields = array('Epi.codigo', 'EpiExterno.codigo', 'Epi.nome', 'Epi.ativo', 'EpiExterno.codigo_externo', 'Epi.numero_ca', 'Epi.data_validade_ca');

            $order = 'Epi.nome';

            $this->Epi->bindModel(
                array('hasOne' => array(
                    'EpiExterno' => array(
                    'foreignKey' => 'codigo_epi', 
                    'conditions' => array('EpiExterno.codigo_cliente' => $codigo_cliente_matriz)
                    )
                )
            ), false);

            $this->paginate['Epi'] = array(
                    'fields' => $fields,
                    'conditions' => $conditions,
                    'limit' => 50,
                    'order' => $order,
            );
           
            $epi = $this->paginate('Epi');
            $listagem = true;
        }
        $this->set(compact('epi','listagem'));
        $this->set('codigo_cliente_filtro', $codigo_cliente_matriz);
    }

    function editar_externo() {
        $this->pageTitle = 'EPI Externos'; 

        $codigoEpi = $this->RequestHandler->params['pass'][1];
        $codigo_cliente = $this->RequestHandler->params['pass'][0];
        if (isset($this->RequestHandler->params['pass'][2])) {
            $codigoepiExterno = $this->RequestHandler->params['pass'][2];
        }

        $dadosEpi = $this->Epi->carregar($codigoEpi);
        
         if($this->RequestHandler->isPost()) {
            if($this->EpiExterno->save($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index_externo', 'controller' => 'epi'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        } 

       if (isset($this->passedArgs[2])) {
            $this->data = $this->EpiExterno->find('first',array('conditions' => array('EpiExterno.codigo' => $this->passedArgs[2])));
        } else {
            $this->data = $dadosEpi;
        }
        $this->set('codigo_cliente', $codigo_cliente);
    }    
}