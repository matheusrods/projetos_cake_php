<?php
class FispqController extends AppController {
    public $name = 'Fispq';
    var $uses = array('Fispq', 'Risco', 'FornecedorUnidade', 'Cliente');
    

    function index() {
        $this->pageTitle = 'Cadastro de FISPQ';
        
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Fispq->name);
		$this->data['Fispq'] = $filtros;
		        
        $this->set('array_unidade', $this->FornecedorUnidade->find('list', array(
        	'joins' => array(
				array(
	            	'table'      => 'fornecedores',
	                'alias'      => 'Fornecedor',
	                'conditions' => 'Fornecedor.codigo = FornecedorUnidade.codigo_fornecedor_unidade',
	                'type'       => 'inner'
				)
			),
        	'fields' => array('FornecedorUnidade.codigo', 'Fornecedor.nome'),
        	'order' => array('nome ASC')
        )));        
    }
    
    function listagem() {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Fispq->name);
        
        $conditions = $this->Fispq->converteFiltroEmCondition($filtros);
        $joins = array(
			array(
            	'table'      => 'fornecedores_unidades',
                'alias'      => 'FornecedorUnidade',
                'conditions' => 'FornecedorUnidade.codigo = Fispq.codigo_fornecedor',
                'type'       => 'inner'
			),
			array(
            	'table'      => 'fornecedores',
                'alias'      => 'Fornecedor',
                'conditions' => 'Fornecedor.codigo = FornecedorUnidade.codigo_fornecedor_unidade',
                'type'       => 'inner'
			)
		);		        
        $fields = array('Fispq.codigo', 'Fispq.nome_produto','Fispq.codigo_fornecedor', 'Fornecedor.nome');
        $order = 'Fispq.nome_produto';

        $this->paginate['Fispq'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
        		'joins' => $joins,
                'limit' => 50,
                'order' => $order,
        );
       
        $fispq = $this->paginate('Fispq');
        
        $this->set('array_unidade', $this->FornecedorUnidade->find('list', array(
        	'joins' => array(
				array(
	            	'table'      => 'fornecedores',
	                'alias'      => 'Fornecedor',
	                'conditions' => 'Fornecedor.codigo = FornecedorUnidade.codigo_fornecedor_unidade',
	                'type'       => 'inner'
				)
			),
        	'fields' => array('FornecedorUnidade.codigo', 'Fornecedor.nome'), 
        	'order' => array('nome ASC')
        )));
                
        $this->set(compact('fispq'));
    }
   
    function incluir() {
        $this->pageTitle = 'Incluir Fispq';

        if($this->RequestHandler->isPost()) {
        	
	        if ($this->Fispq->incluir($this->data)) {
    	        $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'fispq'));
            } else {
				$lista_riscos = $this->Risco->find('list', array('conditions' => array('ativo' => 1),'fields' => array('codigo', 'nome_agente'), 'order' => array('nome_agente ASC')));
				if(count($this->data['FispqGeradora']['riscos_selecionados'])) {
					foreach($this->data['Fispq']['riscos_selecionados'] as $key => $campo) {
						$lista_selecionados[$campo] = $lista_riscos[$campo];
						unset($lista_riscos[$campo]);
					}
				}
				$this->set('array_opcoes', $lista_riscos);
				$this->set('array_selecionados', $lista_selecionados);
				
				$lista_clientes = $this->Cliente->find('list', array('fields' => array('codigo', 'razao_social'), 'order' => array('razao_social ASC')));
				if(count($this->data['Fispq']['empresas_que_acessam'])) {
					foreach($this->data['Fispq']['empresas_que_acessam'] as $key => $campo) {
						$lista_empresas_selecionados[$campo] = $lista_clientes[$campo];
						unset($lista_clientes[$campo]);
					}
				}
				$this->set('array_empresas', $lista_clientes);
				$this->set('empresas_que_acessam', $lista_empresas_selecionados);
				
				$this->BSession->setFlash('save_error');
			}
        } else {
			$this->set('array_selecionados', array()); 
        	$this->set('array_opcoes', $this->Risco->find('list', array('conditions' => array('ativo' => 1),'fields' => array('codigo', 'nome_agente'), 'order' => array('nome_agente ASC'))));

			$this->set('empresas_que_acessam', array()); 
        	$this->set('array_empresas', $this->Cliente->find('list', array('fields' => array('codigo', 'razao_social'), 'order' => array('razao_social ASC'))));        	
        }
        
		$this->set('array_unidade', $this->FornecedorUnidade->find('list', array(
        	'joins' => array(
				array(
	            	'table'      => 'fornecedores',
	                'alias'      => 'Fornecedor',
	                'conditions' => 'Fornecedor.codigo = FornecedorUnidade.codigo_fornecedor_unidade',
	                'type'       => 'inner'
				)
			),
        	'fields' => array('FornecedorUnidade.codigo', 'Fornecedor.nome'), 
        	'order' => array('nome ASC')
        )));        
    }
    
     function editar($codigo) {
        $this->pageTitle = 'Editar Fispq'; 
        
        if($this->RequestHandler->isPost()) {
        	
        	$this->data['Fispq']['codigo'] = $codigo;
			if ($this->Fispq->atualizar($this->data)) {
        	    $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'fispq'));
            } else {
            	$conditions['OR'] = array('ativo' => 1,'codigo' => $this->data['Fispq']['riscos_selecionados']);
				$lista_riscos = $this->Risco->find('list', array('conditions' => $conditions,'fields' => array('codigo', 'nome_agente'), 'order' => array('nome_agente ASC')));
				if(count($this->data['Fispq']['riscos_selecionados'])) {
					foreach($this->data['Fispq']['riscos_selecionados'] as $key => $campo) {
						$lista_selecionados[$campo] = $lista_riscos[$campo];
						unset($lista_riscos[$campo]);
					}
				}
				
				$this->set('array_opcoes', $lista_riscos);
				$this->set('array_selecionados', $lista_selecionados);
				
				
				$lista_clientes = $this->Cliente->find('list', array('fields' => array('codigo', 'razao_social'), 'order' => array('razao_social ASC')));
				if(count($this->data['Fispq']['empresas_que_acessam'])) {
					foreach($this->data['Fispq']['empresas_que_acessam'] as $key => $campo) {
						$lista_empresas_selecionados[$campo] = $lista_clientes[$campo];
						unset($lista_clientes[$campo]);
					}
				}
				
				$this->set('array_empresas', $lista_clientes);
				$this->set('empresas_que_acessam', $lista_empresas_selecionados);
								

				$this->BSession->setFlash('save_error');
            }
        } 

        if (isset($this->passedArgs[0])) {            
            $this->data = $this->Fispq->carregar( $this->passedArgs[0] );
            $conditions['OR'] = array('ativo' => 1,'codigo' => explode(",", $this->data['Fispq']['riscos_selecionados']));
            $opcoes = $this->Risco->find('list', array('conditions' => $conditions,'fields' => array('codigo', 'nome_agente'), 'order' => array('nome_agente ASC')));
			$array_selecionados = array();
			foreach(explode(",", $this->data['Fispq']['riscos_selecionados']) as $key => $campo) {
				if(isset($opcoes[$campo])) {
					$array_selecionados[$campo] = $opcoes[$campo];
					unset($opcoes[$campo]);
				}
			}
	            
			$this->set('array_opcoes', $opcoes);
			$this->set('array_selecionados', $array_selecionados);

			$opcoes = $this->Cliente->find('list', array('fields' => array('codigo', 'razao_social'), 'order' => array('razao_social ASC')));
			$array_selecionados = array();
			foreach(explode(",", $this->data['Fispq']['empresas_que_acessam']) as $key => $campo) {
				$array_selecionados[$campo] = $opcoes[$campo];
				unset($opcoes[$campo]);
			}
	            
			$this->set('empresas_que_acessam', $array_selecionados); 
        	$this->set('array_empresas', $opcoes);
				            
	        $this->set('array_unidade', $this->FornecedorUnidade->find('list', array(
	        	'joins' => array(
					array(
		            	'table'      => 'fornecedores',
		                'alias'      => 'Fornecedor',
		                'conditions' => 'Fornecedor.codigo = FornecedorUnidade.codigo_fornecedor_unidade',
		                'type'       => 'inner'
					)
				),
	        	'fields' => array('FornecedorUnidade.codigo', 'Fornecedor.nome'), 
	        	'order' => array('nome ASC')
	        )));            
        }
    }
}