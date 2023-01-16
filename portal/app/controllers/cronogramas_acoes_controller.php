<?php

class CronogramasAcoesController extends AppController {
	public $name = 'CronogramasAcoes';
	public $layout = 'cronograma_acao';
	public $components = array('Filtros', 'BSession');
	public $uses = array('CronogramaAcao', 'Setor', 'TipoAcao') ;

	function beforeFilter() {
		parent::beforeFilter();
		//$this->BAuth->allow(array('*'));
	}

	public function index(){
        $this->pageTitle = 'Cronograma de Ações';
        $this->Filtros->limpa_sessao($this->CronogramaAcao->name);
        $this->data['TipoAcao'] = $this->Filtros->controla_sessao($this->data, $this->CronogramaAcao->name);
    }

    public function listagem(){
        $this->layout = 'ajax';
        $filters = $this->Filtros->controla_sessao($this->data, $this->CronogramaAcao->name);
        $filters = (is_array($filters) ? $filters : array());
        $parameters = $this->CronogramaAcao->get_parametros_para_consulta($filters);
        $this->paginate['CronogramaAcao'] = $parameters;
        $data = $this->paginate('CronogramaAcao');

        $this->set(compact('data'));
    }

    public function editar($codigo_cliente_matriz, $codigo_cliente_unidade){
        $this->pageTitle = 'Cronograma de Ações';

        if($this->RequestHandler->isPost()){
            $return = $this->CronogramaAcao->submeter($this->data);
            if($return['status'] == 'success'){
                $this->BSession->setFlash(array(MSGT_SUCCESS, $return['message']));
            }else{
                $this->BSession->setFlash(array(MSGT_ERROR, $return['message']));
                $this->redirect($this->referer());
            }
            $this->redirect(array('controller' => 'clientes_implantacao', 'action' => 'gerenciar_pcmso', $codigo_cliente_matriz));
        }

        $this->data = $this->CronogramaAcao->get_all($codigo_cliente_matriz, $codigo_cliente_unidade);
        $data_cliente = $this->CronogramaAcao->get_cliente_informacoes($codigo_cliente_unidade);
        $data_setores = $this->Setor->lista_por_cliente($codigo_cliente_unidade);
        $data_tipo_acoes = $this->TipoAcao->get_all_pcmso_list();

        $this->set(compact('codigo_cliente_matriz', 'codigo_cliente_unidade', 'data_cliente', 'data_setores', 'data_tipo_acoes'));
    }

}