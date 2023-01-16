<?php
class RenovacoesAutomaticasController extends AppController {    
    var $name = 'RenovacoesAutomaticas';
    var $uses = array('RenovacaoAutomatica', 'FichaScorecard', 'Produto', 'Cliente');
    
    public function index(){
        $authUsuario     = $this->BAuth->user();  
        if(!empty($authUsuario['Usuario']['codigo_cliente'])){
            $this->data['RenovacaoAutomatica']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
        }
        $this->data['RenovacaoAutomatica'] = $this->Filtros->controla_sessao($this->data, 'RenovacaoAutomatica');
        $ja_renovou = $this->RenovacaoAutomatica->verificaRenovacaoMes( $this->data['RenovacaoAutomatica']['codigo_cliente'], Produto::SCORECARD );
        if( $ja_renovou )
            $this->redirect(array('action' => 'profissionais_renovacao'));
        $this->redirect(array('action' => 'fichas_a_renovar'));
    }

    public function fichas_a_renovar( ){
		$this->pageTitle = 'Profissinais a Renovar Scorecard';
    	$authUsuario 	 = $this->BAuth->user();  
		if(!empty($authUsuario['Usuario']['codigo_cliente'])){
			$this->data['RenovacaoAutomatica']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
		}
    	$this->data['RenovacaoAutomatica'] = $this->Filtros->controla_sessao($this->data, 'RenovacaoAutomatica');
        $dados_cliente  = $this->Cliente->carregar( $this->data['RenovacaoAutomatica']['codigo_cliente'] );
        $this->data['Cliente'] = $dados_cliente['Cliente'];
		$this->set(compact('authUsuario'));
    }

    public function listar_fichas_a_renovar(  ){
		$this->data['RenovacaoAutomatica'] = $this->Filtros->controla_sessao($this->data, 'RenovacaoAutomatica');
		$listagem 	= array();
		$ja_renovou = $this->RenovacaoAutomatica->verificaRenovacaoMes( $this->data['RenovacaoAutomatica']['codigo_cliente'], Produto::SCORECARD );
        $authUsuario= $this->BAuth->user();		
        if( $ja_renovou )        
            $this->redirect(array('action' => 'listagem'));
    	if( $this->data['RenovacaoAutomatica']['codigo_cliente'] && (!empty($this->data['RenovacaoAutomatica']['dias_renovacao']) || !empty($authUsuario['Usuario']['codigo_cliente']))) {
    		$options = array(
    			'codigo_cliente' => $this->data['RenovacaoAutomatica']['codigo_cliente'],
    			'dias_renovacao' => isset($this->data['RenovacaoAutomatica']['dias_renovacao']) ? $this->data['RenovacaoAutomatica']['dias_renovacao'] : NULL,
    			'interno'		 => empty( $authUsuario['Usuario']['codigo_cliente'] )
			);
    		$query_ficha_a_renovar  = $this->FichaScorecard->listarFichasARenovar( $options );
            $listagem = $this->FichaScorecard->query($query_ficha_a_renovar);
    	}
    	$this->set(compact('listagem', 'ja_renovou'));

    }


    public function incluir( $codigo_cliente = NULL ){    	
        $authUsuario= $this->BAuth->user();  
        $this->data['RenovacaoAutomatica']['interno'] = empty( $authUsuario['Usuario']['codigo_cliente'] );        
        if( $this->RenovacaoAutomatica->salvarRenovacoesAutomaticas($this->data) === false ){
        	$this->BSession->setFlash('save_error');
        } else {
        	$this->BSession->setFlash('save_success');
        }
        $this->redirect(array('action' => 'index'));
    }

    public function profissionais_renovacao( ){
		$this->pageTitle = 'Profissinais a Renovar Scorecard';
    	$authUsuario 	 = $this->BAuth->user();  
		if(!empty($authUsuario['Usuario']['codigo_cliente'])){
			$this->data['RenovacaoAutomatica']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];            
		}
    	$this->data['RenovacaoAutomatica'] = $this->Filtros->controla_sessao($this->data, 'RenovacaoAutomatica');
        $dados_cliente  = $this->Cliente->carregar( $this->data['RenovacaoAutomatica']['codigo_cliente'] );
        $this->data['Cliente'] = $dados_cliente['Cliente'];
		$this->set(compact('authUsuario'));
    }

    public function listagem(  ){
    	$this->data['RenovacaoAutomatica'] = $this->Filtros->controla_sessao($this->data, 'RenovacaoAutomatica');
    	$this->data['RenovacaoAutomatica']['codigo_produto'] = Produto::SCORECARD;
    	$conditions = $this->RenovacaoAutomatica->converteFiltroEmCondition( $this->data['RenovacaoAutomatica'] );
        $conditions['RenovacaoAutomatica.processado'] = 0;
    	$listagem   = array();
    	if( !empty($this->data['RenovacaoAutomatica']['codigo_cliente']) ){ //&& !empty($this->data['RenovacaoAutomatica']['dias_renovacao'])) {
	    	$fields = array(
	    			'RenovacaoAutomatica.codigo', 'RenovacaoAutomatica.data_validade_ficha','RenovacaoAutomatica.data_inclusao',
	    			'RenovacaoAutomatica.renovar','RenovacaoAutomatica.processado','Usuario.apelido',
	    			'ProfissionalLog.nome', 'ProfissionalLog.codigo_documento', 'ProfissionalTipo.descricao');
	        $this->RenovacaoAutomatica->bindModel(
	            array(
	                'hasOne'=>array(
	                    'Usuario' => array(
	                        'className'  =>  'Usuario',
	                        'foreignKey' => false,
	                        'conditions' => array("Usuario.codigo = RenovacaoAutomatica.codigo_usuario_inclusao"),
	                    ),	                	
	                    'ProfissionalLog' => array(
	                        'className'  =>  'ProfissionalLog',
	                        'foreignKey' => false,
	                        'conditions' => array("ProfissionalLog.codigo_profissional = RenovacaoAutomatica.codigo_profissional"),
	                    ),
	                  'ProfissionalTipo' => array(
	                        'className'  =>  'ProfissionalTipo',
	                        'foreignKey' => false,
	                        'conditions' => array('ProfissionalTipo.codigo = RenovacaoAutomatica.codigo_profissional_tipo'),   
	                    )
	            )), false
	        );
	    	$listagem = $this->RenovacaoAutomatica->find('all', array(
	    		'fields' 		=> $fields,
	    		'conditions' 	=> $conditions,
	    		'group' 		=> $fields,
			));
	    }
    	$this->set(compact('listagem'));
    }

    public function atualizar( ){
    	if($this->RequestHandler->isPost()) {
    		foreach ( $this->data['RenovacaoAutomatica']['codigo'] as $codigo => $renovar ) {
				$data['RenovacaoAutomatica']['codigo']  = $codigo;
				$data['RenovacaoAutomatica']['renovar'] = ($renovar == 0 ? '1' : '0' );
    			if( !$this->RenovacaoAutomatica->atualizar( $data ) ){
					$this->BSession->setFlash('save_error');
					$this->redirect(array('action' => 'profissionais_renovacao'));
    			}
    		}
    		$this->BSession->setFlash('save_success');
    	}
    	$this->redirect(array('action' => 'profissionais_renovacao'));
    }

}
