<?php

class TiposAcoesController extends AppController {
	public $name = 'TiposAcoes';
	public $layout = 'tipo_acao';
	public $components = array('Filtros', 'BSession');
	public $uses = array('TipoAcao') ;

	function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array('status'));
	}

	public function index(){
        $this->pageTitle = 'Tipos de Ações';
        $this->Filtros->limpa_sessao($this->TipoAcao->name);
        $this->data['TipoAcao'] = $this->Filtros->controla_sessao($this->data, $this->TipoAcao->name);
    }

    public function listagem(){
        $this->layout = 'ajax';
        $filters = $this->Filtros->controla_sessao($this->data, $this->TipoAcao->name);
        $filters = (is_array($filters) ? $filters : array());
        $parameters = $this->TipoAcao->get_parametros_para_consulta($filters);
        $this->paginate['TipoAcao'] = $parameters;
        $data = $this->paginate('TipoAcao');

        $this->set(compact('data'));
    }

    public function incluir(){
        $this->pageTitle = 'Tipos de Ações - Incluir';
    }

    public function store(){
	    if(!empty($this->data)){
            $return = $this->TipoAcao->submeter($this->data);
            if($return['status'] == 'success'){
                $this->BSession->setFlash(array(MSGT_SUCCESS, $return['message']));
            }else{
                $this->BSession->setFlash(array(MSGT_ERROR, $return['message']));
                $this->redirect($this->referer());
            }
        }
        $this->redirect(array('controller' => 'tipos_acoes', 'action' => 'index'));
    }

    public function editar($codigo){
        $this->pageTitle = 'Tipos de Ações - Editar';
        $this->data = $this->TipoAcao->find('first', array('conditions' => array('TipoAcao.codigo' => $codigo)));
    }

    public function status($codigo, $status){
        $this->layout = 'ajax';
        $this->autoLayout = false;
        $this->autoRender = false;

	    $data = array('codigo' => $codigo, 'status' => $status);
	    $return = $this->TipoAcao->status($data);
	    return $this->responseJson($return);
    }

}