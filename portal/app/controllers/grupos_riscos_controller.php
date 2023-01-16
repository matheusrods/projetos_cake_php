<?php
class GruposRiscosController extends AppController {
    public $name = 'GruposRiscos';
    var $uses = array('GrupoRisco','GrupoRiscoExterno');

    public function beforeFilter() {
        parent::beforeFilter();
        //$this->BAuth->allow('index_externo','listagem_externo','editar_externo');
    }
    
    function index() {
        $this->pageTitle = 'Grupos de Riscos';
    }
    
    function listagem() {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->GrupoRisco->name);
        
        $conditions = $this->GrupoRisco->converteFiltroEmCondition($filtros);
        $fields = array('GrupoRisco.codigo', 'GrupoRisco.descricao', 'GrupoRisco.ativo');
        $order = 'GrupoRisco.descricao';

        $this->paginate['GrupoRisco'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
        );
       
        $grupos_riscos = $this->paginate('GrupoRisco');
        $this->set(compact('grupos_riscos'));
    }
    
    function incluir() {
        $this->pageTitle = 'Incluir Grupo de Risco';

        if($this->RequestHandler->isPost()) {
            if($this->GrupoRisco->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'grupos_riscos'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }
    }
    
    function editar() {
        $this->pageTitle = 'Grupo de Risco'; 
        
         if($this->RequestHandler->isPost()) {

        
            if($this->GrupoRisco->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'grupos_riscos'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        } 

        if (isset($this->passedArgs[0])) {            
            $this->data = $this->GrupoRisco->carregar( $this->passedArgs[0] );
        }
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['GrupoRisco']['codigo'] = $codigo;
        $this->data['GrupoRisco']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->GrupoRisco->atualizar($this->data, false)) {   
            print 1;
        } else {
            print 0;
        }
        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }

    function index_externo() {
        $this->pageTitle = 'Grupos de Riscos Externos';
        $this->data[$this->GrupoRiscoExterno->name] = $this->Filtros->controla_sessao($this->data, $this->GrupoRiscoExterno->name);
    }

    function listagem_externo() {
        $this->layout = 'ajax';
        $grupos_riscos = array();
        $listagem = false;

        $filtros = $this->Filtros->controla_sessao($this->data, $this->GrupoRiscoExterno->name);

        $this->loadModel('GrupoEconomico');        
        $codigo_cliente_filial = $filtros['codigo_cliente'];
        $codigo_cliente_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente_filial);

        if(!empty($filtros['codigo_cliente'])){
            $filtros['codigo_cliente'] = $codigo_cliente_matriz;
            $conditions = $this->GrupoRiscoExterno->converteFiltroEmCondition($filtros);
            
            $fields = array('GrupoRisco.codigo', 'GrupoRiscoExterno.codigo', 'GrupoRisco.descricao', 'GrupoRisco.ativo', 'GrupoRiscoExterno.codigo_externo','GrupoRiscoExterno.codigo_cliente');
 
            $order = 'GrupoRisco.codigo';

            $this->GrupoRisco->bindModel(
                    array('hasOne' => array(
                        'GrupoRiscoExterno' => array(
                        'foreignKey' => 'codigo_grupos_riscos', 
                        'conditions' => array('GrupoRiscoExterno.codigo_cliente' => $codigo_cliente_matriz)
                        )
                    )
                ), false);


            $this->paginate['GrupoRisco'] = array(
                    'fields' => $fields,
                    'conditions' => $conditions,
                    'limit' => 50,
                    'order' => $order,
            );
           
            $grupos_riscos = $this->paginate('GrupoRisco');
            $listagem = true;        
        }       
        $this->set(compact('grupos_riscos','listagem'));
        $this->set('codigo_cliente_filtro', $codigo_cliente_matriz);
    }

    function editar_externo() {
        $this->pageTitle = 'Grupos de Riscos Externos'; 

        $codigoGrupo = $this->RequestHandler->params['pass'][1];
        $codigo_cliente = $this->RequestHandler->params['pass'][0];
        if (isset($this->RequestHandler->params['pass'][2])) {
            $codigoGrupoExterno = $this->RequestHandler->params['pass'][2];
        }

        $dadosGrupoRisco = $this->GrupoRisco->carregar($codigoGrupo);

        if($this->RequestHandler->isPost()) {  
            if($this->GrupoRiscoExterno->save($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index_externo', 'controller' => 'grupos_riscos'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        } 

        if (isset($this->passedArgs[2])) {
            $this->data = $this->GrupoRiscoExterno->find('first',array('conditions' => array('GrupoRiscoExterno.codigo' => $this->passedArgs[2])));
        } else {
            $this->data = $dadosGrupoRisco;
        }
        $this->set('codigo_cliente', $codigo_cliente);
    }
    
}