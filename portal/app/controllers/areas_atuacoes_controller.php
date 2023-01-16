<?php
class AreasAtuacoesController extends AppController {
    public $name = 'AreasAtuacoes';
    var $uses = array('TAatuAreaAtuacao');    

    function index() {
        $this->pageTitle = 'Áreas de Atuações';
        $this->data['TAatuAreaAtuacao'] = $this->Filtros->controla_sessao($this->data, $this->TAatuAreaAtuacao->name);
    }
    
    function listagem() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->TAatuAreaAtuacao->name);
        $conditions = $this->TAatuAreaAtuacao->converteFiltroEmCondition($filtros);
        $this->paginate['TAatuAreaAtuacao'] = array(
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'TAatuAreaAtuacao.aatu_descricao',
        );

        $areas_atuacoes = $this->paginate('TAatuAreaAtuacao');

        $this->set(compact('areas_atuacoes'));
    }
    
    function incluir() {
        $this->pageTitle = 'Incluir Área de Atuação';
        if($this->RequestHandler->isPost()) {
            if ($this->TAatuAreaAtuacao->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
    }
    
    function editar($codigo_area_atuacao = null) {
        $this->pageTitle = 'Atualizar Área de Atuação';
        if (!$codigo_area_atuacao && empty($this->data)) {
            $this->BSession->setFlash('codigo_invalido');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->TAatuAreaAtuacao->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->TAatuAreaAtuacao->read(null, $codigo_area_atuacao);
        }
    }

    function excluir($codigo_area_atuacao) {
        if ($this->TAatuAreaAtuacao->excluir($codigo_area_atuacao)) {
            $this->BSession->setFlash('delete_success');
			$this->redirect(array('action' => 'index'));
        } else {
			$this->BSession->setFlash('delete_error');
			$this->redirect(array('action' => 'index'));
		}
    }

    function estatistica_distribuidor_automatico(){

        $this->pageTitle = 'Estatística Distribuidor Automático';
        $this->data['TAatuAreaAtuacao'] = $this->Filtros->controla_sessao($this->data, $this->TAatuAreaAtuacao->name);
    }

    function estatistica_distribuidor_automatico_listagem(){
        
        $filtros['TAatuAreaAtuacao'] = $this->Filtros->controla_sessao($this->data, $this->TAatuAreaAtuacao->name);

        if( $filtros['TAatuAreaAtuacao'] ) {        
            
            $dados      = array();
            $conditions = $this->TAatuAreaAtuacao->converterFiltrosEmConditions($filtros);
            $dados      = $this->TAatuAreaAtuacao->estatisticaDistribuidorAutomatico($conditions);

            $this->set(compact('dados'));
        }
    }
	
}