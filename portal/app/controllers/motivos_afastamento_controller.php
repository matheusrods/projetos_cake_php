<?php
class MotivosAfastamentoController extends AppController {
    public $name = 'MotivosAfastamento';
    var $uses = array(
        'MotivoAfastamento', 
        'TipoAfastamento',
        'Esocial',
        'MotivoAfastamentoExterno',
        'GrupoEconomico'
    );
    /**
     * beforeFilter callback
     *
     * @return void
     */
	function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(
            array( 'index_externo', 'listagem_externo', 'editar_externo') // <-- TODO remover
        );
    }

    function index() {
        $this->pageTitle = 'Motivos de Afastamento';

        $tipos_afastamento = $this->TipoAfastamento->find ('list', array('fields' => array('codigo','descricao'), 'order' => 'descricao'));
        $this->set(compact('tipos_afastamento'));
    }
    
    function listagem() {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->MotivoAfastamento->name);       
        $conditions = $this->MotivoAfastamento->converteFiltroEmCondition($filtros);    


        $this->MotivoAfastamento->bindModel( 
            array(
                'belongsTo' => array(
                    'TipoAfastamento' => array(
                        'foreignKey' => false, 
                        'conditions' => array('TipoAfastamento.codigo = MotivoAfastamento.codigo_tipo_afastamento')
                    ),
                    'Esocial' => array(
                        'foreignKey' => false, 
                        'conditions' => array('Esocial.codigo = MotivoAfastamento.codigo_esocial')
                    ),
                )
            ), false
        );

        $fields = array('MotivoAfastamento.codigo', 'MotivoAfastamento.descricao','MotivoAfastamento.ativo', 'TipoAfastamento.descricao', 'Esocial.descricao');
        $order = 'MotivoAfastamento.descricao';

        $this->paginate['MotivoAfastamento'] = array(
            'conditions' => $conditions,
            'limit' => 50,
            'fields' => $fields, 
            'order' => $order
            );

        $motivos = $this->paginate('MotivoAfastamento');
        
        $this->set(compact('motivos'));

    }
   
    function incluir() {
        $this->pageTitle = 'Incluir Motivo de Afastamento';

        if($this->RequestHandler->isPost()) {

             $this->data ['MotivoAfastamento'] ['descricao'] = strtoupper ( $this->data['MotivoAfastamento']['descricao'] );             
             

            if ($this->MotivoAfastamento->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'motivos_afastamento'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        $tipos_afastamento = $this->TipoAfastamento->find ('list', array('fields' => array('codigo','descricao'), 'order' => 'descricao', 'conditions' => array('TipoAfastamento.ativo' => 1)));
        $esocial = $this->Esocial->find ('list', array('conditions' => array('Esocial.tabela' => 18), 'fields' => array('codigo','descricao'), 'order' => 'CAST(descricao as VARCHAR(1000))'));
        
        $this->set(compact('tipos_afastamento','esocial'));

    }
    
     function editar() {
        $this->pageTitle = 'Editar Motivo de Afastamento'; 

        if($this->RequestHandler->isPost()) {

            $this->data ['MotivoAfastamento'] ['descricao'] = strtoupper ( $this->data ['MotivoAfastamento'] ['descricao'] );

            if ($this->MotivoAfastamento->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'motivos_afastamento'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        if (isset($this->passedArgs[0])) {            
            $this->data = $this->MotivoAfastamento->carregar( $this->passedArgs[0] );
            
            $tipos_afastamento = $this->TipoAfastamento->find ('list', array('fields' => array('codigo', 'descricao'), 'order' => array('descricao ASC'), 'conditions' => array('TipoAfastamento.ativo' => 1)));

            $esocial = $this->Esocial->find ('list', array('conditions' => array('Esocial.tabela' => 18),'fields' => array('codigo','descricao'), 'order' => 'CAST(descricao as VARCHAR(1000))'));
 
            $this->set(compact('tipos_afastamento','esocial'));
        }

        
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['MotivoAfastamento']['codigo'] = $codigo;
        $this->data['MotivoAfastamento']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->MotivoAfastamento->save($this->data, false)) {   // 0 -> ERRO | 1 -> SUCESSO  
            print 1;
        } else {
            print 0;
        }

        $this->render(false,false);
              
    }

    public function motivos_afastamento_externo_filtros($thisData = null){

        // converte com $this->normalizaCodigoCliente pois codigo_cliente pode estar vindo do form como string ou da sessão como array
        if(isset($thisData['codigo_cliente']) && !empty($thisData['codigo_cliente'])){
            // $codigo_cliente = $this->normalizaCodigoCliente($thisData['codigo_cliente']);
            // $thisData['codigo_cliente'] = $codigo_cliente;

            $this->data['MotivosAfastamentoExterno']['codigo_cliente'] = $thisData['codigo_cliente'];
        }

        if(!isset($thisData['ativo']) || empty($thisData['ativo'])){
            // $thisData['ativo'] = 1;
            $this->data['MotivosAfastamentoExterno']['ativo'] = 1;
        }
        
        // configura no $this->data
        // $this->data['MotivosAfastamentoExterno'] = $thisData;
        
        $this->Filtros->controla_sessao($this->data, 'MotivosAfastamentoExterno');

        $listagem = array();

        //$tipos_afastamento = $this->TipoAfastamento->find ('list', array('fields' => array('codigo','descricao'), 'order' => 'descricao'));

        $this->set(compact('listagem'));
        //$this->set(compact('tipos_afastamento'));
    }

    public function index_externo() {
        $this->pageTitle = 'Motivos de Licenças Externo';
        
        $thisData = $this->Filtros->controla_sessao($this->data, 'MotivosAfastamentoExterno');
        
        $this->motivos_afastamento_externo_filtros($thisData);
        
    }


    public function listagem_externo() {
       
		$this->layout = 'ajax';
		$clientes = array();
        $listagem = false;
        $codigo_cliente_matriz = null;

        //recupera os filtros da sessão
        $filtros = $this->Filtros->controla_sessao($this->data, 'MotivosAfastamentoExterno');
        
		if(!empty($filtros['codigo_cliente'])){

            $codigo_cliente = $filtros['codigo_cliente'];
    
            //monta os filtros
            $conditions = $this->MotivoAfastamentoExterno->converteFiltroEmCondition($filtros);

            //monta os campos para apresentação
            $fields = array(
                'MotivoAfastamento.codigo', 
                'MotivoAfastamento.descricao', 
                'MotivoAfastamento.ativo', 
                'MotivoAfastamentoExterno.codigo_externo', 
                'MotivoAfastamentoExterno.codigo', 
                'MotivoAfastamentoExterno.codigo_cliente'
            );
            //ordena pelo nome
            $order = 'MotivoAfastamento.descricao';            
            //faz o join como bind
            $this->MotivoAfastamento->bindModel(
                array('hasOne' => array(
                    'MotivoAfastamentoExterno' => array(
                        'foreignKey' => 'codigo_motivos_afastamento', 
                        'conditions'   => 'MotivoAfastamentoExterno.codigo_cliente = '.$codigo_cliente,
                    )
                )
                ), false
            );

            //monta a paginação
			$this->paginate['MotivoAfastamento'] = array(
				'fields' => $fields,
				'conditions' => $conditions,
				'limit' => 50,
				'order' => $order
			);
            // debug($this->MotivoAfastamento->find('sql',$this->paginate['MotivoAfastamento']));exit;

            //executa a paginação
			$listagem = $this->paginate('MotivoAfastamento');

            // debug($listagem);exit;
		}

		$this->set(compact('listagem','codigo_cliente'));
    }

    public function editar_externo($codigo_cliente, $codigo_motivos_afastamento=null, $codigo_motivos_afastamento_externo=null) {
        $this->pageTitle = 'Alterar Motivos de Licenças Externo';

        $dadosMotivos = $this->MotivoAfastamento->carregar($codigo_motivos_afastamento);
        
        if($this->RequestHandler->isPost()) {

            if($this->MotivoAfastamentoExterno->save($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index_externo', 'controller' => 'motivos_afastamento'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        } 

        if (isset($codigo_motivos_afastamento_externo)) {
            $this->data = $this->MotivoAfastamentoExterno->find('first',array('conditions' => array('MotivoAfastamentoExterno.codigo' => $codigo_motivos_afastamento_externo)));
        } else {
            $this->data = $dadosMotivos;
        }

        $this->set('codigo_cliente', $codigo_cliente);        
    
    }//fim editar_externo

}