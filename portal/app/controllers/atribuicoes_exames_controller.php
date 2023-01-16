<?php
class AtribuicoesExamesController extends AppController {
    public $name = 'AtribuicoesExames';
    var $uses = array( 'AtribuicaoExame',
        'Exame',
        'Atribuicao',
        'Cliente');
    

    function beforeFilter() {
        parent::beforeFilter();
    }

    function index() {

        $this->render(false, false);

        if(empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $this->redirect(array('action' => 'busca_cliente', 'controller' => 'atribuicoes_exames'));
        } else {
            //Recupera os dados da matriz do cliente
            $this->recupera_dados_matriz($this->authUsuario['Usuario']['codigo_cliente']);
            $this->redirect(array('action' => 'gerenciar', 'controller' => 'atribuicoes_exames', $this->data['AtribuicaoExame']['codigo_cliente']));
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

        //$this->data = array('RiscoExame' => array('codigo_cliente' => $codigo_cliente));
        $this->recupera_dados_matriz($codigo_cliente);

        $this->pageTitle = 'Atribuições - Exames';
        $this->carrega_combos();
        $this->data['AtribuicaoExame'] = $this->Filtros->controla_sessao($this->data, $this->Atribuicao->name);
        
    }

    function listagem($codigo_cliente) {

        $this->layout = 'ajax'; 

        $filtros = $this->Filtros->controla_sessao($this->data, $this->AtribuicaoExame->name);
        
        $conditions = $this->AtribuicaoExame->converteFiltroEmCondition($filtros);
        $conditions = array_merge($conditions, array("AtribuicaoExame.codigo_cliente" => $codigo_cliente));
        $this->data = array('AtribuicaoExame' => array('codigo_cliente' =>  $codigo_cliente));

        $fields = array('AtribuicaoExame.codigo', 'AtribuicaoExame.codigo_cliente', 
                        'AtribuicaoExame.codigo_atribuicao','Atribuicao.descricao',
                        'AtribuicaoExame.codigo_exame','Exame.descricao',
                        'AtribuicaoExame.ativo');
        
        $order = 'AtribuicaoExame.codigo';

        $this->paginate['AtribuicaoExame'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => $order
            );

        $atribuicoes_exames = $this->paginate('AtribuicaoExame');
        
        $this->set(compact('atribuicoes_exames'));
    }

    function busca_cliente() {
        $this->pageTitle = 'Atribuições - Exames - Busca Clientes';
        $this->carrega_combos();
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

    function carrega_combos(){
        $conditions = array('ativo'=> 1);
        $fields = array('codigo', 'descricao');
        $order = 'descricao';

        $atribuicoes = $this->Atribuicao->find('list', array('conditions' => $conditions, 
            'order' => $order, 'fields' => $fields));  

        $exames = $this->Exame->find('list', array('conditions' => $conditions, 
            'order' => $order, 'fields' => $fields));  
        
        $this->set(compact('exames', 'atribuicoes'));
    }

    function incluir($codigo_cliente) {
        
        if(empty($codigo_cliente)){
            $this->BSession->setFlash('save_error');
            $this->redirect($this->referer());
        }
    
        $this->pageTitle = 'Incluir Atribuição - Exame';

        $this->carrega_combos();

        if($this->RequestHandler->isPost()) {
            $this->data['AtribuicaoExame']['codigo_cliente'] = $codigo_cliente;

            if ($this->AtribuicaoExame->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'gerenciar', 'controller' => 'atribuicoes_exames', $codigo_cliente));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->data = array('AtribuicaoExame' => array('codigo_cliente' =>  $codigo_cliente));
        
    }
    
    function editar() {

        $this->pageTitle = 'Editar Atribuição - Exame'; 

        $this->carrega_combos();
        
        if($this->RequestHandler->isPost()) {

            if ($this->AtribuicaoExame->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'gerenciar', 'controller' => 'atribuicoes_exames', $this->data['AtribuicaoExame']['codigo_cliente']));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        if (isset($this->passedArgs[0])) {            
            $this->data = $this->AtribuicaoExame->carregar($this->passedArgs[0]);
        }      

    }


    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';

        $this->data['AtribuicaoExame']['codigo'] = $codigo;
        $this->data['AtribuicaoExame']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->AtribuicaoExame->atualizar($this->data, false)) {   
            echo 1;
        } else {
            echo 0;
        }
        $this->render(false,false);
    }


}