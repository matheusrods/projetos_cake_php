<?php
class EpcController extends AppController {
    public $name = 'Epc';
    var $uses = array('Epc', 'EpcExterno', 'EpcRisco', 'Risco');    

    function index() {
        $this->pageTitle = 'Cadastro de Epc';
    }
    
    function listagem() {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Epc->name);
        // PD-138
        $codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];

        $conditions = $this->Epc->converteFiltroEmCondition($filtros);
        $fields = array('Epc.codigo', 'Epc.nome', 'Epc.ativo', 'Epc.codigo_empresa');
        $order = 'Epc.nome';
        // PD-138
        if(isset($codigo_empresa)){
            $conditions = array('Epc.codigo_empresa' => $codigo_empresa);
		}

        $this->paginate['Epc'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order
        );
       
        $epc = $this->paginate('Epc');
        // debug($epc);
        $this->set(compact('epc'));
    }
   
    function incluir() {
        $this->pageTitle = 'Incluir Epc';

        if($this->RequestHandler->isPost()) {            
        	if ($this->Epc->incluir($this->data)) {
            	$this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'editar', 'controller' => 'epc', $this->Epc->id));
			} else { 
				
				$lista_riscos = $this->Risco->find('list', array('fields' => array('codigo', 'nome_agente'), 'order' => array('nome_agente ASC')));
				if(count($this->data['Epc']['riscos_selecionados'])) {
					foreach($this->data['Epc']['riscos_selecionados'] as $key => $campo) {
						$lista_selecionados[$campo] = $lista_riscos[$campo];
						unset($lista_riscos[$campo]);
					}
				}
				$this->set('array_opcoes', $lista_riscos);
				$this->set('array_selecionados', $lista_selecionados);	
								
				$this->BSession->setFlash('save_error');
			}
        } else {
			$this->set('array_selecionados', array()); 
        	$this->set('array_opcoes', $this->Risco->find('list', array('fields' => array('codigo', 'nome_agente'), 'order' => array('nome_agente ASC'))));        	
        }
    }
    
     function editar($codigo) {
        $this->pageTitle = 'Editar Epc'; 
        
         if($this->RequestHandler->isPost()) {
         	
         	$this->data['Epc']['codigo'] = $codigo;
			if($this->Epc->atualizar($this->data)) {
            	$this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'epc'));
			} else {
				$lista_riscos = $this->Risco->find('list', array('fields' => array('codigo', 'nome_agente'), 'order' => array('nome_agente ASC')));
				if(count($this->data['Epc']['riscos_selecionados'])) {
					foreach($this->data['Epc']['riscos_selecionados'] as $key => $campo) {
						$lista_selecionados[$campo] = $lista_riscos[$campo];
						unset($lista_riscos[$campo]);
					}
				}
				
				$this->set('array_opcoes', $lista_riscos);
				$this->set('array_selecionados', $lista_selecionados);				
				$this->BSession->setFlash('save_error');
			}
        } 
        
        if (isset($this->passedArgs[0])) {
        	$this->data = $this->Epc->find('first', array('conditions' => array('codigo' => $this->passedArgs[0])));
        	$riscos =  $this->EpcRisco->find('first', array('conditions' => array('codigo_epc' => $this->passedArgs[0])));
        	$this->set(compact('riscos'));
        }
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['Epc']['codigo'] = $codigo;
        $this->data['Epc']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->Epc->save($this->data, false)) {   
            print 1;
        } else {
            print 0;
        }
        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }

    function buscar_epc($linha, $codigo_risco){
        $this->layout = 'ajax_placeholder';
        $this->data = $this->Filtros->controla_sessao($this->data, $this->Epc->name);

        $this->set(compact('codigo_risco','linha'));
    }

    function listagem_buscar_epc($linha, $codigo_risco){
        
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Epc->name);
        $conditions = $this->Epc->converteFiltroEmCondition($filtros);
        $conditions = array_merge($conditions,array('codigo_risco' => $codigo_risco));
        
        $fields = array('Epc.codigo', 'Epc.nome', 'EpcRisco.codigo_risco');
        $order = array('Epc.nome');

        $joins  = array(
            array(
              'table' => $this->EpcRisco->databaseTable.'.'.$this->EpcRisco->tableSchema.'.'.$this->EpcRisco->useTable,
              'alias' => 'EpcRisco',
              'type' => 'LEFT',
              'conditions' => 'EpcRisco.codigo_epc = Epc.codigo',
            ),
        );

        $this->paginate['Epc'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'joins' => $joins,  
                'limit' => 10,
                'order' => $order,
        );

        $dados_epc = $this->paginate('Epc');        
        $this->set(compact('dados_epc', 'codigo_risco','linha'));
    }

    function index_externo() {
        $this->pageTitle = "EPC Externos";
        $this->data[$this->EpcExterno->name] = $this->Filtros->controla_sessao($this->data, $this->EpcExterno->name);
    }

    function listagem_externo() {
        $this->layout = 'ajax';
        $epcs = array();
        $listagem = false;

        $filtros = $this->Filtros->controla_sessao($this->data, $this->EpcExterno->name);

        $this->loadModel('GrupoEconomico');        
        $codigo_cliente_filial = $filtros['codigo_cliente'];
        $codigo_cliente_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente_filial);

        if(!empty($filtros['codigo_cliente'])){
            
            $conditions = $this->EpcExterno->converteFiltroEmCondition($filtros);

            $fields = array(
                'Epc.codigo', 
                'EpcExterno.codigo', 
                'Epc.nome', 
                'Epc.ativo', 
                'EpcExterno.codigo_externo',
                'EpcExterno.codigo_cliente'
            );
            
            $order = 'Epc.nome';

            $this->Epc->bindModel(
                array('hasOne' => array(
                        'EpcExterno' => array(
                            'foreignKey' => 'codigo_epc', 
                            'conditions' => array('EpcExterno.codigo_cliente' => $codigo_cliente_matriz)
                        )
                    )
                ), false
            );

            $this->paginate['Epc'] = array(
                    'fields' => $fields,
                    'conditions' => $conditions,
                    'limit' => 50,
                    'order' => $order,
            );
           
            $epcs = $this->paginate('Epc');
            $listagem = true;
        }

        $this->set(compact('epcs','listagem'));
        $this->set('codigo_cliente', $codigo_cliente_matriz);
    }

    function editar_externo() {
        $this->pageTitle = 'Epcs Externos'; 

        $codigoEpc = $this->RequestHandler->params['pass'][1];
        $codigo_cliente = $this->RequestHandler->params['pass'][0];
        if (isset($this->RequestHandler->params['pass'][2])) {
            $codigoEpcExterno = $this->RequestHandler->params['pass'][2];
        }

        $dadosEpc = $this->Epc->carregar($codigoEpc);
        
        if($this->RequestHandler->isPost()) {

            if($this->EpcExterno->save($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index_externo', 'controller' => 'epc'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        } 

        if (isset($this->passedArgs[2])) {
            $this->data = $this->EpcExterno->find('first',array('conditions' => array('EpcExterno.codigo' => $this->passedArgs[2])));
        } else {
            $this->data = $dadosEpc;
        }
        $this->set('codigo_cliente', $codigo_cliente);
    }

}