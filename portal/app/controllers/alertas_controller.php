<?php
class AlertasController extends AppController {
    public $name = 'Alertas';
    
    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow('contar_alertas_pendentes', 'listar_alertas_pendentes', 'tratar', 'parar_de_tratar','alertas_por_perfil');
    }
    
    function index() {
        $this->pageTitle = 'Alertas';
        $this->loadModel('Usuario');
        
        if ($this->RequestHandler->isPost())
        	$this->Filtros->limpa_sessao('Alerta');
        
        if (!empty($this->authUsuario['Usuario']['codigo_cliente']))
			$this->data['Alerta']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
			
        $this->data['Alerta'] = $this->Filtros->controla_sessao($this->data, "Alerta");
        
        $usuarios = array();
		if (!empty($this->data['Alerta']['codigo_cliente']))
		    $usuarios = $this->Usuario->listaPorClienteList($this->data['Alerta']['codigo_cliente']);
        
        $this->set(compact('usuarios'));
    }
    
    function listagem() {
        $this->layout = 'ajax';
        
        $filtros['Alerta'] = $this->Filtros->controla_sessao($this->data, "Alerta");
        $conditions = $this->montarConditions($filtros);
        
        $this->paginate = array(
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'Alerta.data_inclusao DESC',
        );

        $alertas = $this->paginate();

        $this->set(compact('alertas'));
    }
    
    private function montarConditions($filtros) {
        $conditions = array('codigo_cliente' => $filtros['Alerta']['codigo_cliente']);
        
        if (isset($filtros['Alerta']['nao_tratados']) && $filtros['Alerta']['nao_tratados'] == '1')
            $conditions['data_tratamento'] = null;
        if (!empty($filtros['Alerta']['data_inclusao_inicial']))
            $conditions['data_inclusao >='] = $this->Alerta->dateToDbDate2($filtros['Alerta']['data_inclusao_inicial'] . ' 00:00:00');
        if (!empty($filtros['Alerta']['data_inclusao_final']))
            $conditions['data_inclusao <='] = $this->Alerta->dateToDbDate2($filtros['Alerta']['data_inclusao_final'] . ' 23:59:59');
        if (!empty($filtros['Alerta']['data_tratamento_inicial']))
            $conditions['data_tratamento >='] = $this->Alerta->dateToDbDate2($filtros['Alerta']['data_tratamento_inicial'] . ' 00:00:00');
        if (!empty($filtros['Alerta']['data_tratamento_final']))
            $conditions['data_tratamento <='] = $this->Alerta->dateToDbDate2($filtros['Alerta']['data_tratamento_final'] . ' 23:59:59');
        if (!empty($filtros['Alerta']['codigo_usuario_tratamento']))
            $conditions['codigo_usuario_tratamento'] = $filtros['Alerta']['codigo_usuario_tratamento'];
        
        return $conditions;
    }
    
    function contar_alertas_pendentes() {
        if (!empty($this->authUsuario['Usuario']['codigo_cliente']))
    	   echo $this->Alerta->contarAlertasPendentes($this->authUsuario['Usuario']['codigo_cliente']);
    	exit;
    }

    function listar_alertas_pendentes($pagina) {
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])){
            $dados = $this->Alerta->listarAlertasPendentes($this->authUsuario['Usuario']['codigo_cliente'], $pagina);
            foreach($dados as $key=>$alerta){
                $dados[$key]['Alerta']['descricao'] = utf8_encode($alerta['Alerta']['descricao']);
            
            }
            echo json_encode($dados);    
        } else {
            echo 'Não há alertas pendentes';
        }
    	exit;
    }

    function tratar($codigo_alerta = null) {
    	$this->layout = 'ajax';
    	$alerta = $this->Alerta->findByCodigo($codigo_alerta);
    	if (!empty($alerta['Alerta']['codigo_usuario_tratamento']) && $alerta['Alerta']['codigo_usuario_tratamento'] != $this->authUsuario['Usuario']['codigo'])
    	    die('erro');
    	if (!empty($this->data)) {
    		if ($this->Alerta->tratar($this->data))
    			$this->BSession->setFlash('save_success');
            else
    			$this->BSession->setFlash('save_error');
    	} else {
    		if (!empty($this->authUsuario['Usuario']['codigo'])) {
    			$this->Alerta->atribuir($codigo_alerta);
    			$this->data = $this->Alerta->findByCodigo($codigo_alerta);
    		}
    	}
    }
    
    function parar_de_tratar($codigo_alerta) {
        $this->autoRender = false;
        if (!empty($this->authUsuario['Usuario']['codigo']))
			$this->Alerta->desatribuir($codigo_alerta);
    }

    function alertas_por_perfil($perfil = false,$codigo_usuario = false){        
        $this->loadModel('Usuario');
        $this->loadModel('AlertaTipo');
        $this->loadModel('AlertaAgrupamento');
        $this->loadModel('UsuarioAlertaTipo');

        $this->data['Usuario'] = $this->Filtros->controla_sessao($this->data, "Usuario");

        $this->layout = 'ajax';
        $alertas_agrupados = null;

        if($perfil){
            $listar_tipos_alertas = $this->AlertaAgrupamento->verifica_existencia_agrupamento();
           
            //jogada para conseguir pegar os usuarios administradores porque eles não tem empresa no cadastro no banco de dados está como nulo
            //e sim é setado na session com a empresa que ele está logado.
            $codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];
            $_SESSION['Auth']['Usuario']['codigo_empresa'] = "";

            $verifica_usuario_interno = $this->Usuario->verifica_usuario_interno($codigo_usuario);            
            $interno = ($verifica_usuario_interno) ? 'S' : array(null,'N');    
               
            $filtros_alerta = array('AlertaTipo'=>array('interno'=>$interno));       
            $alertasTiposLista = $this->AlertaTipo->listarTipoAlerta($filtros_alerta,$perfil,$interno);
            $alertas_agrupados = $this->AlertaTipo->lista_alertas_cliente($alertasTiposLista,$listar_tipos_alertas);

            //devolve o codigo da empresa
            $_SESSION['Auth']['Usuario']['codigo_empresa'] = $codigo_empresa;
        }
        $this->set(compact('alertas_agrupados'));
     
    }
}