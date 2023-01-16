<?php
class CorretorasController extends AppController {
    public $name = 'Corretoras';
    var $uses = array('Corretora', 'VEndereco', 'CorretoraEndereco');
    
    public function beforeFilter() {
        parent::beforeFilter();
        //$this->BAuth->allow(array('listagem_visualizar','buscar_codigo','auto_completar'));
        $this->BAuth->allow(array('listagem_visualizar','auto_completar'));
    }

    function index() {
        $this->data['Corretora'] = $this->Filtros->controla_sessao($this->data, $this->Corretora->name);
    }
    
    function listagem($destino) {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Corretora->name);
        $conditions = $this->Corretora->converteFiltroEmCondition($filtros);
        $this->paginate['Corretora'] = array(
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'Corretora.nome',
        );

        $corretoras = $this->paginate('Corretora');

        $this->set(compact('corretoras','destino'));
    }
    
    function incluir() {
        $this->pageTitle = 'Incluir Corretora';
        if($this->RequestHandler->isPost()) {
            if ($this->Corretora->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'editar', $this->Corretora->id));
            } else {
                $this->trata_invalidation();
                $this->BSession->setFlash('save_error');
            }
        }
        $this->carrega_combos_formulario();
    }
    
    private function trata_invalidation() {
        $validationErrors = $this->Corretora->invalidFields();

        if (isset($validationErrors['CorretoraEndereco.cep']))
            $this->CorretoraEndereco->invalidate('cep', $validationErrors['CorretoraEndereco.cep']);
    }
    
    function carrega_combos_formulario() {
        $comum = new Comum;
        $estados = $comum->estados();
        $this->set(compact('estados'));
    }
    
    function editar($codigo_corretora) {
        $this->pageTitle = 'Atualizar Corretora';
        if (!empty($this->data)) {
            if ($this->Corretora->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->trata_invalidation();
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->Corretora->carregarParaEdicao($codigo_corretora);
        }
        $this->carrega_combos_formulario();
    }

     function usuarios(){
        $this->pageTitle = 'Usuários por Corretora';
        //$this->carrega_combos();
        $this->data['Corretora'] = $this->Filtros->controla_sessao($this->data, $this->Corretora->name);
    }

    function buscar_codigo() {       
        $this->layout = 'ajax_placeholder';
        $input_id =  isset($this->passedArgs['searcher']) ? $this->passedArgs['searcher'] : null;
        $input_display = isset($this->passedArgs['display']) ? $this->passedArgs['display'] : null;
        $this->data['Corretora'] = $this->Filtros->controla_sessao($this->data, $this->Corretora->name);
        $this->set(compact('input_id','input_display'));
    }

    function listagem_visualizar($destino) {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Corretora->name);
        $conditions = $this->Corretora->converteFiltroEmCondition($filtros);
        $this->paginate['Corretora'] = array(
            'recursive' => 1,
            'joins' => null,
            'conditions' => $conditions,
            'limit' => 10,
            'order' => 'Corretora.nome',
        );
        $corretoras = $this->paginate('Corretora');
        $this->set(compact('corretoras', 'destino'));
        if (isset($this->passedArgs['searcher']))
            $this->set('input_id', str_replace('-search', '', $this->passedArgs['searcher']));
        if (isset($this->passedArgs['display']))
            $this->set('input_display', str_replace('-search', '', $this->passedArgs['display']));
    }

    function auto_completar() {
        $lista      = $this->Corretora->buscaCorretoraJson(strtoupper($_GET['term']),null,5);
        $retorno    = array();
        if($lista){
            foreach ($lista as $key => $corretora)
                $retorno[]  = array(
                    'label' => $corretora['nome'], 
                    'value' => $corretora['codigo']);
        }
        
        echo json_encode($retorno);
        exit;
    }
}