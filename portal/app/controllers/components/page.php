<?php
class PageComponent extends Object{
	var $name = 'Page';
	var $components = array('BAuth','Filtros', 'RequestHandler', 'Session');

    private $_options = array();
    private $_requestToken = null;
    private $_controller = null;
    private $_action = null;
    private $_session = array();
    private $_filtros = array();
    private $_filtrosAction = 'index';
    private $_modelName = null;

    function initialize($controller, $settings = array()) 
    {
        // salvando a referência do controller para uso posterior
        $this->_controller= $controller;
        $this->_action= $controller->action;
    }


    public function config($options = array())
    {

        $this->_requestToken = "{$this->_controller->viewPath}_{$this->_action}";
        
        $model = (isset($options['model'])) ? $options['model'] : null;
        $this->_modelName = (isset($options['modelName'])) ? $options['modelName'] : (!empty($model)) ? $model->name : $this->_controller->name;

        $this->_options = array( 
            'model' => $model,
            'modelName' => $this->_modelName,
            'controllerName' => $this->_controller->viewPath,
            'actionName' => $this->_action,
            'controllerIndexToken' => "{$this->_controller->viewPath}_{$this->_filtrosAction}",
            'page' => array('name'=> null),
            'filtros'=> $this->_filtros
        );
        
        $this->salvarConfig();
    }


	public function controla_sessao($data, $modelName = null) {
		
        if(empty($modelName)){
            $modelName = $this->_modelName;
        }

        $tokenSessao = 'Filtros' . $modelName;

        $sessao = $this->Session->read($tokenSessao);
	
        if (is_array($sessao)) {
			if ( isset($data[$modelName]) && is_array($data[$modelName])) {
				$filtros = array_merge($sessao, $data[$modelName]);
			} else {
				$filtros = $sessao;
			}
		} else {
            $filtros = !empty($data[$modelName]) ? $data[$modelName] : null;
        }
		
		$this->Session->write($tokenSessao, $filtros);
		
		return $filtros;
	}

	public function limpar() {
		// $this->layout = 'ajax_placeholder';
		// $this->element_name = $this->passedArgs['element_name'];
		// $this->model_name = $this->passedArgs['model'];		
		// $filtros = $this->Filtros->limpa_sessao($this->model_name);
		// $this->data[$this->model_name] = $filtros;
		// $this->set('filterValidated', false);
		// $this->carrega_combos();
		// $this->render('/elements/filtros/' . $this->element_name);
	}

    public function before() {

        $dataParams = array();

        $params = $this->RequestHandler->params;
        
        $this->log('pageComponent before $params > '. print_r($params, 1), 'debug');

        $queryParams = $params['url'];

        if(isset($queryParams['limpar'])){
            $this->_filtros = array();
            $this->salvarConfig();
        }
        
        if ($this->RequestHandler->isPost()) {

            if(isset($params['data'])){
                $dataParams = $params['data'];
            }

            $this->log('=================== isPost  ', 'debug');
            // $this->log('pageComponent isPost $queryParams > '. print_r($queryParams, 1), 'debug');
            $this->log('pageComponent isPost $dataParams > '. print_r($dataParams, 1), 'debug');
            // $this->log('pageComponent isPost $params > '. print_r($params, 1), 'debug');

            if($this->_action == $this->_filtrosAction){

                if(isset($dataParams[$this->_modelName]))
                {
                    $this->_filtros = $dataParams[$this->_modelName];

                    //if(isset($dataParams[$this->_modelName]['codigo_cliente'])){
                        // $dataParams[$this->_modelName]['codigo_cliente'] = $queryParams['codigo_cliente'];
                    //}
                }
            
            }

            
            $this->log('pageComponent isPost $this->_filtros > '. print_r($this->_filtros, 1), 'debug');

            $this->salvarConfig();
        }
        
        if ($this->RequestHandler->isAjax()) {
            
            $this->_filtros = $this->obterFiltros();
            $this->log('=================== isAjax  ', 'debug');
            $this->log('pageComponent isAjax $this->_filtros > '. print_r($this->_filtros, 1), 'debug');
            // $this->log('pageComponent isAjax $queryParams > '. print_r($queryParams, 1), 'debug');
            // $this->log('pageComponent isAjax $dataParams > '. print_r($dataParams, 1), 'debug');
            // $this->log('pageComponent isAjax $params > '. print_r($params, 1), 'debug');
            // pr( $queryParams ); exit;

            // $this->element_name = $this->passedArgs['element_name'];
            // $this->model_name = $this->passedArgs['model'];
            // $filterValidated = $this->validates();
            // if ($filterValidated) {
            //     if (isset($this->data['Filtro']['salvar_filtro']) && $this->data['Filtro']['salvar_filtro'])
            //         $this->salvar_filtro();
    
            //     $this->controla_sessao($this->data, $this->model_name);
            //     $this->set('filtrado', true);
            // }
            // $this->set(compact('filterValidated'));
            // $this->carrega_combos();
            // $this->render('/elements/filtros/' . $this->element_name);
            // $this->_controller->render('/elements/filtros/pos_categorias');
    
        }
        



        // atualiza sessão se for metodo index e conter o primeiro argumento
        // if($this->_action == 'index'){
            
        //     if(isset($queryParams['codigo_cliente'])){
        //         $this->_filtros['codigo_cliente'] = $queryParams['codigo_cliente'];
        //     }
        // }

        // if($this->_action == 'listagem'){
        //     $this->_filtros = $this->obterFiltros();
        // }

        
        //$this->Session->write($this->_passed_token, $this->_filtros);
        // $this->viewVars[$this->name]['filtros'] = $this->_filtros;
        // $this->data = $this->Filtros->controla_sessao($this->_filtros, $this->_modelName);
        //$this->_filtros = $this->Filtros->controla_sessao($this->data, $this->_modelName);

        // $this->log('index $this->passedArgs > '. print_r($this->passedArgs, 1), 'debug');
        // $this->log($this->action.' $filtros > '. print_r($this->_filtros, 1), 'debug');
        // $this->log($this->action.' $this->data > '. print_r($this->data, 1), 'debug');
        // $this->log('index $_SESSION > '. print_r($_SESSION, 1), 'debug');
        //$this->log('index $codigo_cliente > '. print_r($codigo_cliente, 1), 'debug');
        // $filtros = $this->Filtros->controla_sessao($this->_filtros, $this->_modelName);
        // $this->log($this->action.' $filtros > '. print_r($filtros, 1), 'debug');
    }

    public function salvarConfig(){
        
        $filtros = $this->_filtros;
        $this->log('pageComponent salvarConfig $filtros > '. print_r($filtros, 1), 'debug');
        $this->_options['filtros'] = $filtros;
        $this->Session->write($this->_requestToken, $this->_options);
        $this->_controller->viewVars[$this->name] = $this->_options;
        $this->Filtros->controla_sessao($filtros, $this->_modelName);
        
        // deixar "filtros" disponivel nas "CTPs"
        $this->_controller->set(compact('filtros'));
    }

    public function configTituloBase($titulo){
        $this->_options['page']['name'] = $titulo;
    }

    public function obterConfig(){
        return $this->Session->read($this->_requestToken);
    }

    public function obterTituloPagina(){
        $options = $this->Session->read($this->_requestToken);
        return $options['page']['name'];
    }

    public function obterFiltros(){
        $options = $this->Session->read("{$this->_controller->viewPath}_{$this->_filtrosAction}");
        return $options['filtros'];
    }















	/**
     * Normalização do [ codigo_cliente ] com avaliação centralizada de
     *  dados que trafegam nas controllers e injeção no $this->data
     * 
     * ex.
     *  Ao autenticar o usuario deverá ter um parametro [ codigo_cliente ] em $this_->BAuth->user() 
     * 
     *  cenario 1) Se ele for um usuario Admin o 
     *              parâmetro [ codigo_cliente ] esperado é 
     *              vazio ou nulo.
     * 
     *  cenario 2) Se for um Admin, mas digitar no 
     *              formulário de pesquisa [ codigo_cliente ] o 
     *              parametro será preenchido.
     * 
     *  cenario 3) Se for um Multicliente, o codigo_cliente será 
     *              preenchido com array de empresas/Matriz no qual 
     *              pertencem ao usuario autenticado
     * 
	 *  cenario 4) Se for um Multicliente, mas escolher formulário 
     *              de pesquisa [ codigo_cliente ] o parametro 
     *              será preenchido apenas com o escolhido
     * 
	 * @return array
	 */
	public function avaliarRequisicao( $BAuth_user = null ){
        
       
        if(!isset($BAuth_user['Usuario']['multicliente'])){
            return null;
        }

        if(is_null($BAuth_user) 
            && !isset($BAuth_user['Usuario'])
            && !isset($BAuth_user['Usuario']['codigo_cliente'])){
			return null;
		}

        // se a Action/requisição do form for para url com controller FiltrosController e 
        // Action Filtrar sera interceptada e auto Filtrada alimentando a sessao
        // $form_procurar = (isset($params['controller']) 
        //                         && $params['controller'] == 'filtros'
        //                         && isset($params['action']) 
        //                         && $params['action'] == 'filtrar'
        //                         ) ? true : false;

        // retorna lista multiclientes
        $codigo_cliente = $this->obterCodigoClienteUsuarioAutenticado(); 
        
        // caso for formulario
        if( $this->RequestHandler->isPost()) {
            
            if(isset($this->RequestHandler->params['data'])) {
                $form_post = $this->RequestHandler->params['data'];
                // buscar o parametro codigo_cliente no post recebido
                $codigo_cliente = $this->recursiveFind($form_post, 'codigo_cliente');
                // normaliza o codigo ex receber Todos assim "10011,20,71758" ou array 0 => "10011,20,71758"
                $codigo_cliente = $this->normalizaCodigoCliente($codigo_cliente);
            }

            
        } else {
            // não aceita codigo_cliente em branco então inicializa com todos multiclientes
            if(empty($BAuth_user['Usuario']['codigo_cliente'])){
                
            } else {
                // senao use o codigo_cliente que ja existe na sessao
                $codigo_cliente = $BAuth_user['Usuario']['codigo_cliente'];
            }
        }

        if(!empty($codigo_cliente)){
            $this->Session->write('Auth.Usuario.codigo_cliente', $codigo_cliente);
        }

        return $codigo_cliente;

    }
    
	/**
	 * Avaliar código(s) cliente(s) existentes no array, ex. no POST de um FORM
	 *
	 * @return array
	 */
	public function buscarCodigoCliente( $dados = null ){
        
        // se existe codigo_cliente no array informado
        if ( !is_null($dados) && isset($dados['codigo_cliente']) && !empty($dados['codigo_cliente']) ){
            $codigo_cliente = (array)$dados['codigo_cliente'];
        } else {
            $codigo_cliente = $this->obterCodigoClienteUsuarioAutenticado();
        }

       return $codigo_cliente;		
    }
    
    /**
	 * Obter código(s) cliente(s) associados ao usuario autenticado
	 *
	 * @return array
	 */
	public function obterUsuarioLogado(){
        
        $usuario = $this->BAuth->user();

        if(isset($usuario['Usuario']))
            return $usuario['Usuario'];

        return null;
    }

    /**
	 * Obter código(s) cliente(s) associados ao usuario autenticado
	 *
	 * @return array
	 */
	public function obterCodigoClienteUsuarioAutenticado(){

        $usuario = $this->obterUsuarioLogado(); // recuperar usuario logado

        $codigo_cliente = null; // inicializa
        
        // se não existe na sessao Bauth ex. o usuario pode ser do tipo Admin
        if ( (!isset($usuario['codigo_cliente']) 
                || empty($usuario['codigo_cliente']))
                || is_null($usuario['codigo_cliente'] ))
                {
                // $codigo_cliente = null
        } else {
                            
            $clientes = array(); // lista de codigo_cliente
    
            /**
             * Usuario MultiCliente
             * se usuario for multicliente e possuir empresas agregadas
             */
            if(isset($usuario['multicliente'])){
                    $lista = $usuario['multicliente'];
                    foreach ($lista as $key => $value) {
                        array_push($clientes, $key);
                    }
                    
            } else {
                /**
                 * Usuario não multicliente mas possui cliente definido
                 */
                array_push($clientes, $usuario['codigo_cliente']);
            }

            $codigo_cliente = $clientes;
            
        }
        
        return $codigo_cliente;

    }

    /**
     * 
     * Encontrar um valor por chave informada
     * https://stackoverflow.com/questions/3975585/search-for-a-key-in-an-array-recursively
     *
     * @param array $array
     * @param [type] $needle
     * @return void
     */
    function recursiveFind(array $array, $needle) {
        $iterator = new RecursiveArrayIterator($array);
        $recursive = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
        $return = array();
        foreach ($recursive as $key => $value) {
          if ($key === $needle) {
            $return[] = $value;
          }
        } 
        return $return;
    }

    /**
	 * normaliza o retorno de um ou mais codigo_cliente(s)
	 *
	 * @param array $codigo_cliente
	 * @return array
	 */
	public function normalizaCodigoCliente( $codigo_cliente = null ){
        
        $tipo_dado = gettype($codigo_cliente);
        
    	$dados = array();
        
        if($tipo_dado == 'array'){
            // se for array, mas o primeiro indice conter virgula 
            if(isset($codigo_cliente[0]) && strpos($codigo_cliente[0], ',') > 0){
                    $codigo_cliente_exp = explode(',', $codigo_cliente[0]);	
                    if(is_array($codigo_cliente_exp))
                        foreach ($codigo_cliente_exp as $key => $value) {
                            $dados[$key] = (int)$value; // cast pra inteiro para remover possiveis espaços ou caracteres
                        }
            } else {
                // ele é um array então converta os valores para int
                foreach ($codigo_cliente as $key => $value) {
                    $dados[$key] = (int)$value; // cast pra inteiro para remover possiveis espaços ou caracteres
                }
            }
        }

        if($tipo_dado == 'string'){
			$pos = strpos($codigo_cliente, ',');
			if($pos > 0){
				$codigo_cliente_exp = explode(',', $codigo_cliente);	
				if(is_array($codigo_cliente_exp))
					foreach ($codigo_cliente_exp as $key => $value) {
						$dados[$key] = (int)$value; // cast pra inteiro para remover possiveis espaços ou caracteres
					}
			} else {
				$dados = array((int)$codigo_cliente);	
			}
		} 

		if($tipo_dado == 'integer'){
			$dados = array((int)$codigo_cliente);
		}

        return $dados;

	}
}
?>