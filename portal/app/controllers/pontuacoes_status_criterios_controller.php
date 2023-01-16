<?php
class PontuacoesStatusCriteriosController extends AppController {
	var $name = 'PontuacoesStatusCriterios';
	// $uses('model'); carrega  models 
	public $uses = array('PontuacoesStatusCriterio','Criterio','StatusCriterio','Cliente',
						 'Seguradora','CriterioOpcional','ProfissionalTipo', 'PontuacaoSCProfissional');
	var $helpers = array('Paginator');
		
    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array( 
           'verifica_campo_divergente'
       )); 
    }
	function index() {
		
		$this->Filtros->limpa_sessao('PontuacoesStatusCriterio');
		// envia titulo para página 
		$this->pageTitle = 'Critérios para Grid Motorista por Cliente';
		// lista os criterios e seguradoras 
		$criterios = $this->Criterio->find('list');
		$seguradora = $this->Seguradora->find('list');
		//carrega dados para sessão através da model PontuacoesStatusCriterio
		//$this->data=$this->Filtros->controla_sessao($this->data, 'PontuacoesStatusCriterio');
		$this->data['PontuacoesStatusCriterio'];
		$this->set(compact('criterios','seguradora'));
		
	}

	function incluir(){
		
		$this->pageTitle = ' Incluir Critérios para Grid Motorista por Cliente';
		$filtros = $this->Filtros->controla_sessao($this->data, 'PontuacoesStatusCriterio');		
		$list_seguradora= $this->Seguradora->find('list');
		$this->set(compact('list_seguradora'));
		$status = array();
		if (!empty($this->data)){
			$conditions = array('codigo'=>$this->data['PontuacoesStatusCriterio']['codigo_criterio']);
			$fields =array('controla_qtd','aceita_texto');
			$lista = $this->Criterio->find('all', compact('conditions','fields'));
			foreach ($lista as $verifica){ 
				if($this->data['PontuacoesStatusCriterio']){
		           	if($verifica['Criterio']['controla_qtd']==1){	
		           		if($this->data['PontuacoesStatusCriterio']['qtd_ate']== NULL ){
		           			$this->data['PontuacoesStatusCriterio']['qtd_ate']= 0 ;
		            	}elseif($this->data['PontuacoesStatusCriterio']['qtd_ate']!= NULL ){
	            		  
	            		}
	            	}
				}	
			}
			if ($ok=$this->PontuacoesStatusCriterio->incluir($this->data)) {
				$codigo_pontuacao_status_criterio = $this->PontuacoesStatusCriterio->id;
				$dataPSCP = isset($this->data['PontuacaoSCProfissional']['codigo_profissional_tipo']) ? $this->data['PontuacaoSCProfissional']['codigo_profissional_tipo'] : NULL;
				$this->PontuacaoSCProfissional->insereStatusCriterioProfissional($codigo_pontuacao_status_criterio,  $dataPSCP );
				$this->CriterioOpcional->atualizarCriterio(   
					$this->data['PontuacoesStatusCriterio']['opcional'], 
					$this->data['PontuacoesStatusCriterio']['codigo_criterio'], 
					$this->data['PontuacoesStatusCriterio']['codigo_cliente'], 
					$this->data['PontuacoesStatusCriterio']['codigo_seguradora']
				);
				$this->BSession->setFlash('save_success');
				$this->Filtros->limpa_sessao('PontuacoesStatusCriterio');
				$this->redirect(array('action' => 'index'));
				// exit;			
			} else {
				$this->BSession->setFlash('save_error');
			}							
			$conditions = array('codigo_criterio' => $this->data['PontuacoesStatusCriterio']['codigo_criterio']);
			$status 	= $this->StatusCriterio->find('list', compact('conditions'));
		}		
		$conditions = array('codigo_criterio' => $this->data['PontuacoesStatusCriterio']['codigo_criterio']);
		$status 	= $this->StatusCriterio->find('list', compact('conditions'));
		$criterios = $this->Criterio->find('list');
		$profissionais_tipo = $this->ProfissionalTipo->find('list');
		$profissionais_tipos = array();
		foreach( $profissionais_tipo as $codigo => $descricao){
			$descricao = Inflector::humanize( mb_strtolower ( $descricao, "UTF-8" ));
			$profissionais_tipos[$codigo] = $descricao;
		}
		$this->set(compact('criterios','status','seguradora','cliente', 'profissionais_tipos'));
	}


	function editar($codigo = null){

		$this->pageTitle = ' Editar Critérios para Grid Motorista por Cliente';
		$filtros = $this->PontuacoesStatusCriterio->carregar($codigo);		
		
		foreach ($filtros as $filtros);
		$seguradora = NULL;		
		$cliente = NULL;
		$codigo_seguradora = NULL;		

		if(isset($filtros['codigo_seguradora'])){
			$seguradora = $this->Seguradora->carregar($filtros['codigo_seguradora']);
			$codigo_seguradora = $filtros['codigo_seguradora'];
			$this->set(compact('seguradora','codigo_seguradora'));
		}
		
		if(isset($filtros['codigo_cliente'])  ){			
			$cliente = $this->Cliente->carregar($filtros['codigo_cliente']);
			$this->set(compact('cliente'));
		}
			
		if(isset($filtros['codigo_seguradora'])&& isset($filtros['codigo_cliente'])){
			$cliente = $this->Cliente->carregar($filtros['codigo_cliente']);
			$seguradora = $this->Seguradora->carregar($filtros['codigo_seguradora']);
			$this->set(compact('cliente','seguradora'));
		}

		if($filtros['codigo_seguradora'] == NULL && $filtros['codigo_cliente']==NULL){
			$cliente = $this->Cliente->carregar($filtros['codigo_cliente']);
			$seguradora = $this->Seguradora->carregar($filtros['codigo_seguradora']);
			//$this->Filtros->controla_sessao($this->data, 'PontuacoesStatusCriterio');		
			$this->set(compact('cliente','seguradora'));
		}		
		if (!empty($this->data)){		
         	if ($this->PontuacoesStatusCriterio->atualizar($this->data)) {
				
				$dataPSCP = isset($this->data['PontuacaoSCProfissional']['codigo_profissional_tipo']) ? $this->data['PontuacaoSCProfissional']['codigo_profissional_tipo'] : NULL;
				$this->PontuacaoSCProfissional->insereStatusCriterioProfissional($codigo,  $dataPSCP );

		 		$this->CriterioOpcional->atualizarCriterio(
					$this->data['PontuacoesStatusCriterio']['opcional'], 
					$this->data['PontuacoesStatusCriterio']['codigo_criterio'], 
					$this->data['PontuacoesStatusCriterio']['codigo_cliente'], 
					$this->data['PontuacoesStatusCriterio']['codigo_seguradora']
				);
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index'));
				// exit;
			} 
			$this->BSession->setFlash('save_error');
			$codigo_criterio = $this->data['PontuacoesStatusCriterio']['codigo_criterio'];					
		} else {
			$this->PontuacoesStatusCriterio->bindStatusCriterio();
			$this->data = $this->PontuacoesStatusCriterio->carregar($codigo, 2);			
			$this->data['PontuacoesStatusCriterio']['opcional'] = $this->CriterioOpcional->ehOpcional(
				$this->data['StatusCriterio']['codigo_criterio'],
				$this->data['PontuacoesStatusCriterio']['codigo_cliente'],
				$this->data['PontuacoesStatusCriterio']['codigo_seguradora']
			);			
			$codigo_criterio = $this->data['StatusCriterio']['codigo_criterio'];			
		}		 

		$tipo_profissionalchecked = $this->PontuacaoSCProfissional->find('all', array( 'conditions' => array( 'codigo_pontuacao_status_criterio' => $codigo ),
		'fields'=>array('codigo_tipo_profissional')));
		if( $tipo_profissionalchecked ){
			$cked = NULL;
			foreach ($tipo_profissionalchecked as $key => $value) {
				$cked[] = $value[$this->PontuacaoSCProfissional->name]['codigo_tipo_profissional'];
			}
			$this->data['PontuacaoSCProfissional']['codigo_profissional_tipo'] = $cked;
		}
		$list_seguradora = $this->Seguradora->find('list');
		$criterios 	     = $this->Criterio->lista_criterio();
		$conditions      = array('codigo_criterio' => $codigo_criterio);
		$status 	     = $this->StatusCriterio->find('list', compact('conditions'));
		$profissionais_tipo  = $this->ProfissionalTipo->find('list');
		$profissionais_tipos = array();
		foreach( $profissionais_tipo as $codigo => $descricao){
			$descricao = Inflector::humanize( mb_strtolower ( $descricao, "UTF-8" ));
			$profissionais_tipos[$codigo] = $descricao;
		}
		$this->set(compact('criterios','status','codigo','codigo_criterio','seguradora','cliente','list_seguradora','codigo_cliente','codigo_seguradora', 'profissionais_tipos'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for criterio', true));
			$this->redirect(array('action'=>'index'));
		}
		
		if ($this->PontuacoesStatusCriterio->delete($id)) {
			
			$this->BSession->setFlash('delete_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->BSession->setFlash('delete_error');
		$this->redirect(array('action' => 'index'));
	}



	function listar_pontuacoes() {		
		$this->layout = 'ajax';
		$filtros 	= $this->Filtros->controla_sessao($this->data, 'PontuacoesStatusCriterio');
		$cliente 	= NULL;
		$seguradora = NULL;
		$mensagem 	= NULL;
		$listagem 	= array();
		
		/*// SE o USUARIO FOR UM CLIENTE, CARREGA O codigo_cliente COM O VALOR DA SESSÃO
		if(!empty($this->authUsuario['Usuario']['codigo_cliente']))
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];*/		
		
		if($filtros['codigo_cliente'] && $filtros['codigo_seguradora']) {
			$seguradora = $this->Seguradora->carregar($filtros['codigo_seguradora']);
			$cliente 	= $this->Cliente->carregar($filtros['codigo_cliente']);
			$listagem 	= $this->PontuacoesStatusCriterio->listar_criterios($filtros);
			$this->set(compact('cliente','listagem','seguradora'));
		} 			
		if(isset($filtros['codigo_seguradora']) && $filtros['codigo_seguradora']){
			$seguradora = $this->Seguradora->carregar($filtros['codigo_seguradora']);
			$listagem 	= $this->PontuacoesStatusCriterio->listar_criterios($filtros);
		}
		if(isset($filtros['codigo_cliente']) && $filtros['codigo_cliente']) {
	       $listagem 	= $this->PontuacoesStatusCriterio->listar_criterios($filtros);
					
		}		
		if (isset($filtros['codigo_criterio'])&& $filtros['codigo_criterio']) {				
			$listagem 	= $this->PontuacoesStatusCriterio->listar_criterios($filtros);				
		}		
		
		$listagem = $this->PontuacoesStatusCriterio->listar_criterios($filtros);		
		$this->set(compact('cliente','listagem','seguradora','filtros'));	
	}

	public function verifica_campo_insuficiente( $codigo_status_criterio, $codigo_criterio  ){		
		$retorno = $this->PontuacoesStatusCriterio->verificaCampoInsuficiente( $codigo_status_criterio, $codigo_criterio );
    	echo json_encode($retorno);
    	exit;
	}

	public function verifica_campo_divergente( $codigo_status_criterio, $codigo_criterio  ){		
		$retorno = $this->PontuacoesStatusCriterio->verificaCampoDivergente( $codigo_status_criterio, $codigo_criterio );
    	echo json_encode($retorno);
    	exit;
	}

}
