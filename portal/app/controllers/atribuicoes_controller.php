<?php
class AtribuicoesController extends AppController {
    public $name = 'Atribuicoes';
    var $uses = array('Atribuicao', 'Cliente');
    

    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow('index_externo','listagem_externo','editar_externo');
    }

	function index() {

	    $this->render(false, false);

	    if(empty($this->authUsuario['Usuario']['codigo_cliente'])) {
	        $this->redirect(array('action' => 'busca_cliente', 'controller' => 'atribuicoes'));
	    } else {
	        //Recupera os dados da matriz do cliente
	        $this->recupera_dados_matriz($this->authUsuario['Usuario']['codigo_cliente']);
	        $this->redirect(array('action' => 'gerenciar', 'controller' => 'atribuicoes', $this->data['Atribuicao']['codigo_cliente']));
	    }
	}

	function recupera_dados_matriz($codigo_cliente) {
	    $this->loadModel('GrupoEconomicoCliente');
	    $dados_cliente_matriz =  $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);
	    $this->data = array('Atribuicao' => array('codigo_cliente' => $dados_cliente_matriz["Matriz"]["codigo"]));
	}

    function gerenciar($codigo_cliente) {

        if(empty($codigo_cliente)){
            $this->BSession->setFlash('save_error');
            $this->redirect($this->referer());
        }

        $this->recupera_dados_matriz($codigo_cliente);

        $this->pageTitle = 'Atribuições';
        $this->data['Atribuicao'] = $this->Filtros->controla_sessao($this->data, $this->Atribuicao->name);
    }

    function listagem($codigo_cliente) {

        $this->layout = 'ajax'; 

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Atribuicao->name);
        
        $conditions = $this->Atribuicao->converteFiltroEmCondition($filtros);
        $conditions = array_merge($conditions, array("Atribuicao.codigo_cliente" => $codigo_cliente));
        $this->data = array('Atribuicao' => array('codigo_cliente' =>  $codigo_cliente));

        $fields = array('Atribuicao.codigo', 'Atribuicao.codigo_cliente',
                        'Atribuicao.descricao','Atribuicao.ativo', 'Atribuicao.codigo_externo');
        $order = 'Atribuicao.codigo';

        $this->paginate['Atribuicao'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => $order
            );

        $atribuicoes = $this->paginate('Atribuicao');
        
        $this->set(compact('atribuicoes'));
    }

    function busca_cliente() {
        $this->pageTitle = 'Atribuições - Busca Clientes';
        $this->data['Clientes'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
    }

    function listagem_clientes() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
        $conditions = $this->Cliente->converteFiltroEmCondition($filtros);

        $fields = array('Cliente.codigo', 'Cliente.razao_social', 'Cliente.nome_fantasia', 'Cliente.ativo');
        $order = 'Cliente.codigo';

         $this->paginate['Cliente'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => $order
            );

        $clientes = $this->paginate('Cliente');
        $this->set(compact('clientes'));
    }


    function incluir($codigo_cliente) {
        
        if(empty($codigo_cliente)){
            $this->BSession->setFlash('save_error');
            $this->redirect($this->referer());
        }
    
        $this->pageTitle = 'Incluir Atribuição';


        if($this->RequestHandler->isPost()) {
            $this->data['Atribuicao']['codigo_cliente'] = $codigo_cliente;

            if ($this->Atribuicao->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'gerenciar', 'controller' => 'atribuicoes',
            			$codigo_cliente));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->data = array('Atribuicao' => array('codigo_cliente' =>  $codigo_cliente));
        
    }
    
    function editar() {

        $this->pageTitle = 'Editar Atribuição'; 

        if($this->RequestHandler->isPost()) {

            if ($this->Atribuicao->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'gerenciar', 'controller' => 'atribuicoes', $this->data['Atribuicao']['codigo_cliente']));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        if (isset($this->passedArgs[0])) {            
            $this->data = $this->Atribuicao->carregar($this->passedArgs[0]);
        }      

    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';

        $this->data['Atribuicao']['codigo'] = $codigo;
        $this->data['Atribuicao']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->Atribuicao->atualizar($this->data, false)) {   
            echo 1;
        } else {
            echo 0;
        }
        $this->render(false,false);
    }

}