<?php
class AtribuicoesCargosController extends AppController {
    public $name = 'AtribuicoesCargos';
    var $uses = array(
        'AtribuicaoCargo', 
        'Cliente', 
        'Cargo'
    );
    

    function beforeFilter() {
        parent::beforeFilter();
    }

	function index() {

	    $this->render(false, false);

	    //Recupera os dados da matriz do cliente
	    // $this->recupera_dados_matriz($this->authUsuario['Usuario']['codigo_cliente']);
	    $this->redirect(array('action' => 'gerenciar', 'controller' => 'atribuicoes_cargos'));
	}

	function recupera_dados_matriz($codigo_cliente) {
	    $this->loadModel('GrupoEconomicoCliente');
	    $dados_cliente_matriz =  $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);
	    $this->data = array('Atribuicao' => array('codigo_cliente' => $dados_cliente_matriz["Matriz"]["codigo"]));
	}

    function gerenciar($codigo_cliente=null) {

        // if(empty($codigo_cliente)){
        //     $this->BSession->setFlash('save_error');
        //     $this->redirect($this->referer());
        // }

        // $this->recupera_dados_matriz($codigo_cliente);

        $this->pageTitle = 'Atribuições de Cargos';
        $this->data['AtribuicaoCargo'] = $this->Filtros->controla_sessao($this->data, $this->AtribuicaoCargo->name);
    }

    function listagem($codigo_cliente = null) {

        $this->layout = 'ajax'; 
        
        // $this->data = array('AtribuicaoCargo' => array('codigo_cliente' =>  $codigo_cliente));

        $filtros = $this->Filtros->controla_sessao($this->data, $this->AtribuicaoCargo->name);
        
        $conditions = $this->AtribuicaoCargo->converteFiltroEmCondition($filtros);

        $fields = array('AtribuicaoCargo.codigo', 
                        'AtribuicaoCargo.codigo_cliente',
                        'AtribuicaoCargo.descricao',
                        'AtribuicaoCargo.ativo');

        $order = 'AtribuicaoCargo.codigo';

        $this->paginate['AtribuicaoCargo'] = array(
            'recursive' => 1,
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => $order
            );

        // pr($this->AtribuicaoCargo->find('sql', $this->paginate['AtribuicaoCargo']));

        $atribuicoes_cargos = $this->paginate('AtribuicaoCargo');
        
        $this->set(compact('atribuicoes_cargos'));
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


    function incluir($codigo_cliente=null) {
        
        // if(empty($codigo_cliente)){
        //     $this->BSession->setFlash('save_error');
        //     $this->redirect($this->referer());
        // }
    
        $this->pageTitle = 'Incluir Atribuição de Cargos';


        if($this->RequestHandler->isPost()) {
            // $this->data['AtribuicaoCargo']['codigo_cliente'] = $codigo_cliente;
            $this->data['AtribuicaoCargo']['ativo'] = 1;

            if ($this->AtribuicaoCargo->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'gerenciar', 'controller' => 'atribuicoes_cargos',
            			$codigo_cliente));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->data = array('AtribuicaoCargo' => array('codigo_cliente' =>  $codigo_cliente));
        
    }
    
    function editar() {

        $this->pageTitle = 'Editar Atribuição de Cargos'; 

        if($this->RequestHandler->isPost()) {

            if ($this->AtribuicaoCargo->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'gerenciar', 'controller' => 'atribuicoes_cargos'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        if (isset($this->passedArgs[0])) {            
            $this->data = $this->AtribuicaoCargo->carregar($this->passedArgs[0]);
        }
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';

        $this->data['AtribuicaoCargo']['codigo'] = $codigo;
        $this->data['AtribuicaoCargo']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->AtribuicaoCargo->atualizar($this->data, false)) {   
            echo 1;
        } else {
            echo 0;
        }
        $this->render(false,false);
    }

}