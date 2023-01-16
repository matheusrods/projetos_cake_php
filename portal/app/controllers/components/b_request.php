<?php
class BRequestComponent extends Object{
	var $name = 'BRequest';
	var $components = array('BAuth','Filtros', 'RequestHandler', 'Session');
    var $controller;

    function initialize($controller, $settings = array()) {
        // salvando a referência do controller para uso posterior
        $this->controller = $controller;
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