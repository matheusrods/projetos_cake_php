<?php
class SeguradorasController extends AppController {
    public $name = 'Seguradoras';
    var $uses = array('Seguradora', 'VEndereco', 'SeguradoraEndereco');

     public function beforeFilter() {
        parent::beforeFilter();

        $class_methods = get_class_methods( $this );
        $this->BAuth->allow(array('incluir', 'editar', 'auto_completar','buscar_codigo'));
    }

    function index() {
        $this->data['Seguradora'] = $this->Filtros->controla_sessao($this->data, $this->Seguradora->name);
    }

    function listagem($destino) {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Seguradora->name);
        $conditions = $this->Seguradora->converteFiltroEmCondition($filtros);
        $this->paginate['Seguradora'] = array(
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'Seguradora.nome',
        );

        $seguradoras = $this->paginate('Seguradora');

        $this->set(compact('seguradoras','destino'));
    }

    function incluir() {
        $this->pageTitle = 'Incluir Seguradora';
        if($this->RequestHandler->isPost()) {
            if ($this->Seguradora->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array( 'action' => 'editar' , $this->Seguradora->id ));
            } else {
                $this->trata_invalidation();
                $this->BSession->setFlash('save_error');
            }
        }
        $this->carrega_combos_formulario();
    }

    private function trata_invalidation() {
        $validationErrors = $this->Seguradora->invalidFields();
    }

    function carrega_combos_formulario() {
        
        $comum = new Comum;
        $estados = $comum->estados();

        $this->set(compact('estados'));
    }

    function editar($codigo_seguradora) {
        $this->pageTitle = 'Atualizar Seguradora';
        if (!empty($this->data)) {
            if ($this->Seguradora->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->trata_invalidation();
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->Seguradora->carregarParaEdicao($codigo_seguradora);
        }
        $this->carrega_combos_formulario();
    }

    function usuarios(){
        $this->pageTitle = 'Usuários por Seguradoras';
        $this->data['Seguradora'] = $this->Filtros->controla_sessao($this->data, $this->Seguradora->name);
    }

    function cockpit() {
        $this->loadModel('Cliente');
        $this->loadModel('Corretora');
        $this->pageTitle = 'Cockpit de Seguradoras e Corretoras';
        $gadgets = array();
        if (!empty($this->data)) {
            if (empty($this->data['Seguradora']['codigo_seguradora']) && empty($this->data['Seguradora']['codigo_corretora'])) {
                $this->Seguradora->invalidate('codigo_seguradora','Por favor informe a Seguradora ou a Corretora');
                $this->Seguradora->invalidate('codigo_corretora_visual','');
            }
            if(empty($this->Seguradora->validationErrors)){
                $hash = urlencode(Comum::encriptarLink($this->data['Seguradora']['ano'] . '|' . $this->data['Seguradora']['codigo_seguradora'] . '|' . $this->data['Seguradora']['codigo_corretora']));
                $gadgets = array(
                    array('titulo' => 'Faturamento Mensal', 'url' => 'itens_notas_fiscais/gg_faturamento_por_mes_seguradora_corretora'),
                    array('titulo' => 'Faturamento Produtos', 'url' => 'itens_notas_fiscais/gg_faturamento_produtos_seguradora_corretora'),
                    array('titulo' => 'Estatísticas Teleconsult', 'url' => 'fichas/gg_servicos_mensais_seguradora_corretora'),
                    array('titulo' => 'Estatísticas SMs', 'url' => 'solicitacoes_monitoramento/gg_encerradas_por_mes_seguradora_corretora'),
                    array('titulo' => 'Clientes Faturados', 'url' => 'itens_notas_fiscais/gg_qtd_clientes_faturados_seguradora_corretora'),
                    array('titulo' => 'Valores Gerenciados', 'url' => 'solicitacoes_monitoramento/gg_valor_gerenciado_por_mes_seguradora_corretora'),
                );
                if(!empty($this->data['Seguradora']['codigo_seguradora'])){
                    $seguradora = $this->Seguradora->carregar($this->data['Seguradora']['codigo_seguradora']);
                    $codigo_seguradora = $this->data['Seguradora']['codigo_seguradora'];
                }
                if(!empty($this->data['Seguradora']['codigo_corretora'])){
                    $corretora = $this->Corretora->carregar($this->data['Seguradora']['codigo_corretora']);
                    $codigo_corretora = $this->data['Seguradora']['codigo_corretora'];
                }

            }
        } else {
            $this->data['Seguradora']['ano'] = Date('Y');
        }
        $anos = Comum::listAnos();
        $seguradoras = $this->Seguradora->listarSeguradorasAtivas();

        $this->set(compact('anos', 'gadgets', 'hash', 'seguradora','corretora','seguradoras','clientes'));
    }
}