<?php
class CorretorasController extends AppController {
    public $name = 'Corretoras';
    var $uses = array('Corretora', 'VEndereco', 'CorretoraEndereco');
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('listagem_visualizar','buscar_codigo','auto_completar','criar_acesso'));
    }

    function index() {
        $this->data['Corretora'] = $this->Filtros->controla_sessao($this->data, $this->Corretora->name);
    }
    
    function listagem($destino) {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Corretora->name);
        $conditions = $this->Corretora->converteFiltroEmCondition($filtros);
        $this->paginate['Corretora'] = array(
            'fields' => array(  'codigo',
                                'codigo_documento',
                                'nome',
                                '( SELECT 1 FROM RHHealth_vendas.dbo.empresa WHERE cnpj COLLATE Latin1_General_CI_AS = Corretora.codigo_documento ) AS acesso ' ) ,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'Corretora.nome',
        );

        //pr( $this->Corretora->find('sql', $this->paginate['Corretora'] ) );

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
        $input_id = $this->passedArgs['searcher'];
        $input_display = $this->passedArgs['display'];
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

    function criar_acesso( $codigo_corretora ){

        $dados = $this->Corretora->find('first', array(
            'conditions' => array( 'codigo' => $codigo_corretora )
        ));

        $success = '';

        if( isset( $this->data ) ) {
            $this->loadModel('CorretorUsuarioVendas');
            $post = $this->data['Corretora'];

            if ($this->CorretorUsuarioVendas->exportarCorretor( $post['nome'], $post['user'], $dados['Corretora']['codigo_documento'], $post['email'] )) {
                $this->BSession->setFlash(array('alert alert-success', $this->CorretorUsuarioVendas->message));
                //$this->redirect(array('action' => 'editar', $this->Corretora->id));
                $success = 'yes';
            } else {

                $errors = $this->CorretorUsuarioVendas->errorValidation;
                $this->Corretora->validationErrors = $errors;

                $this->trata_invalidation();
                //pr( $this->CorretorUsuarioVendas->validationErrors );
                $this->BSession->setFlash('save_error');
                //$this->BSession->setFlash(array('alert alert-error', $this->CorretorUsuarioVendas->message));
            }                       
        }
        
           
        $this->set( compact('dados', 'success') );
    }

}