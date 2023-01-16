<?php
class PosCategoriasController extends AppController {

    public $name = 'PosCategorias';
    public $uses = array(
        'PosCategorias',
        'Cliente',
        'ClienteOpco',
        'ClienteBu',
        'GrupoEconomico'
    );
    
    private $_modelName = null;

    public function beforeFilter() 
    {
        parent::beforeFilter();
       
        $this->_modelName = $this->PosCategorias->name;

        $this->BAuth->allow();
    }   

    
    public function _filtros() {}
    
    public function _filtros_clientes($thisData = null){
        
        $this->loadModel('Cliente');
        $this->loadModel('ClienteOpco');
        $this->loadModel('ClienteBu');
        
        $opco = array();
        $bu = array();

        if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1 ) {

            $codigo_cliente =  $this->authUsuario['Usuario']['codigo_cliente'];

            $nome_fantasia = $this->Cliente->find('first', array(
                'fields' => array(
                    'nome_fantasia'
                ),
                'conditions' => array(
                    'codigo' => $codigo_cliente
                )
            ));

            $is_admin = 0;
        } else {
            //Filtro para usuario admin
            $codigo_cliente = null;
            $is_admin = 1;
            $nome_fantasia = null;
        }
        
        $codigo_empresa = $this->authUsuario['Usuario']['codigo_empresa'];

        if (isset($thisData['codigo_cliente'])) {
            $opco = $this->ClienteOpco->find('list', array(
                'fields' => array(
                    "ClienteOpco.descricao",
                ),
                'conditions' => array(
                    'ClienteOpco.codigo_cliente' => $thisData['codigo_cliente'],
                    'ClienteOpco.codigo_empresa' => $codigo_empresa,
                )
            ));

            $bu = $this->ClienteBu->find('list', array(
                'fields' => array(
                    "ClienteBu.descricao",
                ),
                'conditions' => array(
                    'ClienteBu.codigo_cliente' => $thisData['codigo_cliente'],
                    'ClienteBu.codigo_empresa' => $codigo_empresa,
                )
            ));
        }
        
        $this->data['Clientes'] = $thisData;

        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia', 'opco', 'bu'));
    }

    public function index() {
        
        $this->render(false, false);

        if(empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $this->redirect(array('action' => 'buscar_clientes'));
        } else {
            //Recupera os dados da matriz do cliente
            $this->recupera_dados_matriz($this->authUsuario['Usuario']['codigo_cliente']);
            $this->redirect(array('action' => 'gerenciar', $this->data[$this->_modelName]['codigo_cliente']));
        }
    
    }

	function recupera_dados_matriz($codigo_cliente) {
	    $this->loadModel('GrupoEconomicoCliente');
	    $dados_cliente_matriz =  $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);
        $this->data = array_merge( $dados_cliente_matriz, array($this->_modelName => array('codigo_cliente' => $dados_cliente_matriz["Matriz"]["codigo"])));
	}

    
    function gerenciar($codigo_cliente) {

        if(empty($codigo_cliente)){
            $this->BSession->setFlash('save_error');
            $this->redirect($this->referer());
        }

        $this->recupera_dados_matriz($codigo_cliente);

        $this->pageTitle = 'Tipos de Observação';
        $this->data[$this->_modelName] = $this->Filtros->controla_sessao($this->data, $this->_modelName);

        $nome_fantasia = $this->cliente_nome($codigo_cliente);
        $razao_social = $this->cliente_razao_social($codigo_cliente);

        if($_SESSION['Auth']['Usuario']['codigo_uperfil'] == 1){
            $is_admin = 1;
        } else {
            $is_admin = 0;
        }

        $this->set(compact( 'is_admin', 'nome_fantasia', 'codigo_cliente', 'razao_social'));

    }
    

    function buscar_clientes() {
        $this->pageTitle = 'Tipos de Observação - Busca Clientes';

        //pega os filtros da sessão
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

        $is_admin = 1;
        $nome_fantasia = null;
        if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {

            //Filtro para usuario não admin
            $codigo_cliente =  $this->authUsuario['Usuario']['codigo_cliente'];

            $nome_fantasia = $this->cliente_nome($codigo_cliente);

            $is_admin = 0;
        }

        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
    }

    function listagem_clientes()
    {
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

        // INICIO - filtrar por usuário logado
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if (empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
                $codigo_cliente =  $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        if(empty($codigo_cliente)) {
            $codigo_cliente =  $this->authUsuario['Usuario']['codigo_cliente'];

            if(isset($this->authUsuario['Usuario']['multicliente'])) {
                $codigo_cliente = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1 ) {
            //Filtro para usuario não admin
            $nome_fantasia = $this->Cliente->find('first', array(
                'fields' => array(
                    'nome_fantasia'
                ),
                'conditions' => array(
                    'codigo' => $codigo_cliente
                )
            ));

            $is_admin = 0;
        } else {
            //Filtro para usuario admin
            $is_admin = 1;
            $nome_fantasia = null;
        }

        $assinaturas = $this->Cliente->getAssinaturaPDASWTOBS($filtros['codigo_cliente'],'OBSERVADOR_EHS');

        $clientes = array();

        if(!empty($assinaturas)) {

            if (is_array($assinaturas)) {
                $assinaturas = implode(",", $assinaturas);
            }

            $clientes = $this->Cliente->find('all', array(
                'conditions' => array(
                    "codigo IN ({$assinaturas})"
                )
            ));
        }


        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia', 'clientes'));
    }


    public function listagem($codigo_cliente) {

        $this->layout = 'ajax';

        $registros = array();

        $filtros = $this->Filtros->controla_sessao($this->data, $this->_modelName);

        $filtros['codigo_cliente'] = $codigo_cliente;
        
        $modelData = $this->PosCategorias->obterListagem($filtros);
        
        if(!empty($modelData)){
            $this->paginate['Cliente'] = $modelData;

            $registros = $this->paginate('Cliente');
        }
        
        $this->data[$this->_modelName] = $filtros;

        $this->set(compact('registros', 'codigo_cliente'));
    }


    public function incluir($codigo_cliente) {

        $this->pageTitle = 'Incluir Tipos de Observação';
        
        if($this->RequestHandler->isPost()) {

            //Declaro para começar inserir na tabela SubperfilAcoes
            $this->PosCategorias->query('begin transaction');

            try {

                if ($this->PosCategorias->incluir($this->data)) {

                    $this->PosCategorias->commit();
                    $this->BSession->setFlash('save_success');
                    $this->redirect(array('controller' => 'pos_categorias', 'action' => 'gerenciar', $codigo_cliente));
                } else {
                    $this->PosCategorias->rollback();
                    $this->BSession->setFlash('save_error');
                }

            } catch(Exception $e) {
                // debug($e->getmessage());
                $msg = $e->getmessage();
                $this->AcoesMelhoriasTipo->rollback();
                $this->BSession->setFlash(array(MSGT_ERROR, $msg));
            }

        }
        
        if(empty($codigo_cliente)){
            $this->redirect(array('controller' => 'pos_categorias', 'action' => 'gerenciar', $codigo_cliente));
        }

        $this->set('codigo_cliente');
    }


    public function editar($codigo, $codigo_cliente) {

        $this->pageTitle = 'Editar Tipos de Observação';

        if ($this->RequestHandler->isPut()) {
            $this->data['PosCategorias']['codigo_empresa'] = 1;
            $this->data['PosCategorias']['cor'] = "FFFFFF";

            if ($this->PosCategorias->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'pos_categorias', 'action' => 'gerenciar', $codigo_cliente));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->data = $this->PosCategorias->carregar($codigo);

        if (empty($this->data)) {
            $this->redirect(array('controller' => 'pos_categorias', 'action' => 'gerenciar', $codigo_cliente));
        }

        $codigo_cliente =  $this->data['PosCategorias']['codigo_cliente'];

        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
    }


    public function atualiza_status($codigo, $status) {

        $this->layout = 'ajax';

        $this->data[$this->_modelName]['codigo'] = $codigo;
        $this->data[$this->_modelName]['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->PosCategorias->atualizar($this->data, false)) {   
            echo 1;
        } else {
            echo 0;
        }
        $this->render(false,false);
   }


   private function salvar($codigo = null, $dados = array()){

        if(!empty($codigo)){
            return $this->PosCategorias->atualizar($dados);
        }

        return $this->PosCategorias->incluir($dados);
   }

    public function cliente_nome($codigo_cliente)
    {
        $this->loadModel("Cliente");

        if (!empty($codigo_cliente)) {
            $nome_fantasia = $this->Cliente->find('first', array(
                'fields' => array(
                    'nome_fantasia'
                ),
                'conditions' => array(
                    'codigo' => $codigo_cliente
                )
            ));
            return $nome_fantasia['Cliente']['nome_fantasia'];
        } else {
            return '';
        }

    }

    public function cliente_razao_social($codigo_cliente)
    {
        $this->loadModel("Cliente");

        if (!empty($codigo_cliente)) {
            $nome_fantasia = $this->Cliente->find('first', array(
                'fields' => array(
                    'razao_social'
                ),
                'conditions' => array(
                    'codigo' => $codigo_cliente
                )
            ));
            return $nome_fantasia['Cliente']['razao_social'];
        } else {
            return '';
        }

    }
}
