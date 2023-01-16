<?php

class PmpsController extends AppController {
	public $name = 'Pmps';
	public $layout = 'pmps';
	public $components = array('Filtros', 'BSession');
	public $uses = array('Pmps', 'GrupoEconomico') ;

	function beforeFilter() {
		parent::beforeFilter();
		//$this->BAuth->allow(array('*'));
	}

	public function index(){
        $this->pageTitle = 'Materiais de Pronto Socorro';
        $this->Filtros->limpa_sessao($this->Pmps->name);

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Pmps->name);
        // se tem dados na sessao então preencha o codigo cliente e se tem codigo_cliente em $filtros usuario deve estar pesquisando
        if(!empty($this->authUsuario['Usuario']['codigo_cliente']) && empty($filtros['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $this->data['Pmps'] = $filtros;

        $data_lista_unidades = array();
        $this->set(compact('data_lista_unidades'));
    }

    public function listagem(){
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Pmps->name);

        // se tem dados na sessao então preencha o codigo cliente e se tem codigo_cliente em $filtros usuario deve estar pesquisando
        if(!empty($this->authUsuario['Usuario']['codigo_cliente']) && empty($filtros['codigo_cliente'])) {
          $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }
        
        // debug($filtros);exit;


        $filtros = (is_array($filtros) ? $filtros : array());
        $parameters = $this->Pmps->get_parametros_para_consulta($filtros);
        $this->paginate['GrupoEconomico'] = $parameters;
        $data = $this->paginate('GrupoEconomico');

        $this->set(compact('data'));
    }

    public function store(){
	    if(!empty($this->data)){
            $return = $this->Pmps->submeter($this->data);
            if($return['status'] == 'success'){
                $this->BSession->setFlash(array(MSGT_SUCCESS, $return['message']));
            }else{
                $this->BSession->setFlash(array(MSGT_ERROR, $return['message']));
                $this->redirect($this->referer());
            }
        }
        $this->redirect(array('controller' => 'pmps', 'action' => 'index'));
    }

    public function editar($codigo_cliente_matriz, $codigo_cliente_unidade){
        $this->pageTitle = 'Materiais de Pronto Socorro';
        $this->data = $this->Pmps->get_por_matriz_unidade($codigo_cliente_matriz, $codigo_cliente_unidade);
    }

}