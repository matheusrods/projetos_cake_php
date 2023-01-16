<?php
App::import('Component', 'StringView');

class ApiAutorizacaoComponent {
	var $name = 'ApiAutorizacao';
	
    public $cod_cliente = "10011"; // empresa teste
    public $cod_usuario = "1"; //admin do sistema
    
    private $status = array();

    public function getStatus() {
        return $this->status;
    }

    public function setCodCliente($codigo_cliente) {
        $this->cod_cliente = $codigo_cliente;
    }

	function initialize(&$controller, $settings = array()) {        
		// saving the controller reference for later use        
		$this->controller =& $controller;
	}

    /**
     * Metodo para autorizar o acesso as informações na api
     * 
     * Param:
     *  $token   => token do usuario para validação do mesmo
     *  $cnpj    => cnpj do cliente que tem que estar vinculado com o usuario
     * 
     * Return:
     *  true ou false
     */ 
    public function autoriza($token, $cnpj)
    {
        //registra a classe usuario
        $this->Usuario = ClassRegistry::init('Usuario');
        $this->Cliente = ClassRegistry::init('Cliente');

        
        //monta os joins
        $joins = array(
                    array(
                        'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                        'alias' => 'Cliente',
                        'conditions' => 'Cliente.codigo = Usuario.codigo_cliente',
                        'type' => 'INNER',
                    ),
            );
        //pega o usuario e cnpj para saber se existe
        $usuario = $this->Usuario->find('first', array(
                                        'fields' => array('Usuario.codigo','Cliente.codigo'),
                                        'joins' => $joins,
                                        'conditions' => array('Usuario.token' => $token, 'Cliente.codigo_documento' => $cnpj)
                                        ));
        $ret = false;
        //verifica se tem dados no usuario
        if(!empty($usuario)) {
            //seta os codigos
            $this->cod_cliente   = $usuario['Cliente']['codigo'];
            $this->cod_usuario   = $usuario['Usuario']['codigo'];

            // print $this->cod_cliente."--".$this->cod_usuario;

            $ret = true;
        }

        //retorna os dados que encontrou na base ou em branco caso não encotre
        return $ret;

    } //fim autoriza

    /**
     * Metodo para validar a autorizacao pelo token e cpnj
     * @param string $token     Token de usuário
     * @param string $cnpj      CNPJ do cliente
     * @return boolean
     */ 
    public function validaAutorizacao($token = null, $cnpj =null)
    {
        // verifica se tem os get passados
        if(!empty($token) && !empty($cnpj)) {
            // verifica se pode prosseguir com o processo
            if($this->autoriza($token, $cnpj)) {                
                return true; //foi validado
            } else {
                // Erro 3 é quando o token e o cnpj passado não tem relação ou encontram-se errados
                $this->status['status']  = '3';
                $this->status['msg']     = 'Token ou CNPJ invalido';
                return false;

            } 
        } else {
            /* Get cnpj ou tokem em branco */ 
            $this->status["status"]  = '2';
            $this->status['msg']     = 'Token ou CNPJ invalido';
            return false;
        }

        return false;

    }

    /**
     * [log_api description]
     * 
     * METODO PARA GERAR O LOG INTEGRACOES
     * 
     * @param  [type] $status  [description]
     * @param  [type] $entrada [description]
     * @param  [type] $saida   [description]
     * @return [type]          [description]
     */
    public function log_api($entrada,$saida,$status="0",$msg="SUCESSO", $arquivo="API")
    {
        //instancia a model
        $this->LogIntegracao = ClassRegistry::init('LogIntegracao');

        //seta os valores
        $log_integracao['LogIntegracao']['codigo_cliente']          = $this->cod_cliente;
        $log_integracao['LogIntegracao']['codigo_usuario_inclusao'] = $this->cod_usuario;
        $log_integracao['LogIntegracao']['descricao']               = $msg;
        $log_integracao['LogIntegracao']['arquivo']                 = $arquivo;
        $log_integracao['LogIntegracao']['conteudo']                = $entrada;
        $log_integracao['LogIntegracao']['retorno']                 = $saida;
        $log_integracao['LogIntegracao']['sistema_origem']          = $arquivo;
        $log_integracao['LogIntegracao']['data_arquivo']            = date('Y-m-d H:i:s');
        $log_integracao['LogIntegracao']['status']                  = $status; 
        $log_integracao['LogIntegracao']['tipo_operacao']           = 'I'; //inserido

        //inclui na tabela
        $this->LogIntegracao->incluir($log_integracao);

    } //fim log_api

    /** 
     * Joga na log_integracao/ codigos das ocorrencias
     * @param $get
     * @param $post
     * @return string
     */
    public function conteudoLog($get, $post) {
        $entrada   = "url;";
        $entrada  .= implode(";", array_keys((array) $post));
        $entrada  .= "\n\r";
        $entrada  .= implode(";", $get);
        $entrada  .= ";";
        $entrada  .= json_encode($post);
        return $entrada;
    }

    /**
     * [alerta_integracao description]
     * 
     * metodo para gerar o alerta que irá enviar para os usuarios que quiserem
     * 
     * @param  [type] $dados [description]
     * @return [type]        [description]
     */
    public function alerta_integracao($dados, array $config = array())
    {

        $this->Alerta = ClassRegistry::init('Alerta');

        //gera alerta           
        $dados['codigo_cliente'] = $this->cod_cliente;

        //para enviar o email
        $this->StringView   = new StringViewComponent();
        $this->StringView->reset();                    
        $this->StringView->set('dados', $dados);
        $content = $this->StringView->renderMail('email_falha_integracao');

        //interno e externo
        $codigos_alertas = array(31,32);
        //cadastra os alertas
        foreach($codigos_alertas as $cod_alerta){
            $alerta = array(
                'Alerta' => array(
                    'codigo_cliente'     => $this->cod_cliente,
                    'descricao'          => "Falha na integração",
                    'assunto'            => "Falha na integração",
                    'descricao_email'    => $content,
                    'codigo_alerta_tipo' => $cod_alerta,
                    'model'              => (!empty($config['model']) ? $config['model'] : 'PedidoExame'),
                    'foreign_key'        => $this->cod_usuario,
                    'email_agendados'    => false,
                    'sms_agendados'      => false
                ),
            );
            
            $this->Alerta->incluir($alerta);
            
        }//fim foreach

        
    }//fim alerta_integracao

} //fim ApiAutorizacaoComponent