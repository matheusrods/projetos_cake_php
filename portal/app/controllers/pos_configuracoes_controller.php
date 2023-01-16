<?php
class PosConfiguracoesController extends AppController {

    public $name = 'PosConfiguracoes';
    public $uses = array(
        'PosConfiguracoes',
        'Cliente'
    );
    
    private $_modelName = null;

    public function beforeFilter() 
    {
        parent::beforeFilter();
       
        $this->_modelName = $this->PosConfiguracoes->name;

        $this->BAuth->allow();
    }   

    public function _filtros() {}
    public function _filtros_clientes() {}

    public function index() {
        
        $this->render(false, false);

        if(empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $this->redirect(array('action' => 'busca_clientes'));
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

        $this->pageTitle = 'Configurações POS';
        $this->data[$this->_modelName] = $this->Filtros->controla_sessao($this->data, $this->_modelName);
    }

    function busca_clientes() {
        $this->pageTitle = 'Configurações POS - Busca Clientes';
        $this->data['Clientes'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
    }

    function listagem_clientes() {
        $this->layout = 'ajax';
        
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
        $conditions = $this->Cliente->converteFiltroEmCondition($filtros);
        
        $fields = array('Cliente.codigo', 'Cliente.razao_social','Cliente.codigo_documento', 'Cliente.nome_fantasia', 'Cliente.ativo');
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


    public function listagem() {

        $this->layout = 'ajax';

        $registros = array();
        
        $filtros = $this->Filtros->controla_sessao($this->data, $this->_modelName);

        if(isset($filtros['codigo_cliente'])){
            $this->recupera_dados_matriz($filtros['codigo_cliente']);
        }
        
        $modelData = $this->PosConfiguracoes->obterListagem($filtros);
        
        if(!empty($modelData)){
            $this->paginate['Cliente'] = $modelData;

            $registros = $this->paginate('Cliente');
        }
        
        $this->data[$this->_modelName] = $filtros;

        $this->set(compact('registros'));
    }


    public function incluir($codigo_cliente = null) {

        $this->pageTitle = 'Incluir Configurações POS';
        
        if($this->RequestHandler->isPost()) {

            $paramsData = $this->RequestHandler->params['data'];
            $paramsData = isset($paramsData['PosConfiguracoes']) ? $paramsData['PosConfiguracoes'] : $paramsData;

            if(empty($paramsData['codigo_pos_ferramenta']) 
                || empty($paramsData['descricao'])
                || empty($paramsData['chave'])
                || empty($paramsData['valor'])
                || empty($paramsData['observacao'])){
                $this->BSession->setFlash('save_error');
                $this->redirect(array('action' => 'incluir', $paramsData['codigo_cliente']));
            }

            $dados = array(
                'codigo_cliente' => $paramsData['codigo_cliente'],
                'codigo_empresa' => 1,
                'codigo_pos_ferramenta' => $paramsData['codigo_pos_ferramenta'],
                'descricao' => $paramsData['descricao'],
                'chave' => strtoupper($paramsData['chave']),
                'valor' => $paramsData['valor'],
                'observacao' => $paramsData['observacao'],
                'ativo' => $paramsData['ativo']
            );
            
            if(!$this->salvar( $codigo = null, $dados ))
            {
                $this->BSession->setFlash('save_error');
            }
            
            $this->BSession->setFlash('save_success');
            $this->redirect(array('action' => 'gerenciar', $paramsData['codigo_cliente']));
        }
        
        if(empty($codigo_cliente)){
            $this->BSession->setFlash('save_error');
            $this->redirect(array('action' => 'index'));
        }

        $this->recupera_dados_matriz($codigo_cliente);

        $lista_ferramentas = array(
            '1'=> 'Plano de ação',
            '2'=> 'Safety walk & talk',
            '3'=> 'Observador EHS',
        );

        $this->set('lista_ferramentas');
    }


    public function editar($codigo = null, $codigo_cliente = null) {

        $this->pageTitle = 'Editar Configurações POS';
        
        if($this->RequestHandler->isPost()) {

            $paramsData = $this->RequestHandler->params['data'];
            $paramsData = isset($paramsData['PosConfiguracoes']) ? $paramsData['PosConfiguracoes'] : $paramsData;
            
            if(empty($paramsData['codigo_pos_ferramenta']) || empty($paramsData['descricao'])){
                $this->BSession->setFlash('save_error');
                $this->redirect(array('action' => 'incluir', $paramsData['codigo_cliente']));
            }
            
            $dados = array(
                'codigo_pos_ferramenta' => $paramsData['codigo_pos_ferramenta'],
                'descricao' => $paramsData['descricao'],
                'ativo' => $paramsData['ativo']
            );
            
            if(!$this->salvar( $codigo, $dados ))
            {
                $this->BSession->setFlash('save_error');
            }
            
            $this->BSession->setFlash('save_success');
            $this->redirect(array('action' => 'gerenciar', $paramsData['codigo_cliente']));
        }

        if(empty($codigo) || empty($codigo_cliente)){
            $this->BSession->setFlash('save_error');
            $this->redirect(array('action' => 'index'));
        }

        $this->recupera_dados_matriz($codigo_cliente);

        $modelData = $this->PosConfiguracoes->carregar($codigo);

        $this->data['PosConfiguracoes'] = $modelData['PosConfiguracoes'];

        
        $lista_ferramentas = array(
            '1'=> 'Plano de ação',
            '2'=> 'Safety walk & talk',
            '3'=> 'Observador EHS',
        );

        $this->set('lista_ferramentas');
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
            return $this->PosConfiguracoes->atualizar($dados);
        }

        return $this->PosConfiguracoes->incluir($dados);
   }
    
}
