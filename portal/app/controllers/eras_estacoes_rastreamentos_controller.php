<?php
class ErasEstacoesRastreamentosController extends AppController {
    public $name = 'ErasEstacoesRastreamentos';
    public $uses = array('TErasEstacaoRastreamento');
    
    function listar_estacoes_rastreamento(){
        $this->pageTitle = 'Estações de Rastreamento';
        $this->loadmodel('TErasEstacaoRastreamento');
        $fields = array('TErasEstacaoRastreamento.eras_codigo',
                        'TErasEstacaoRastreamento.eras_descricao',
                        'TErasEstacaoRastreamento.eras_ramal',
                        '"TErasEstacaoRastreamento2"."eras_descricao"');


        
        $joins = array(
            array(
                'table' => 'trafegus.public.eras_estacao_rastreamento',
                'alias' => 'TErasEstacaoRastreamento2',
                'conditions' => 'TErasEstacaoRastreamento2.eras_codigo = TErasEstacaoRastreamento.eras_logistico',
                'type' => 'left',
            ),
        );

        $this->paginate['TErasEstacaoRastreamento'] = array(
            'fields' => $fields,
            'joins' => $joins,
            'order' => '"TErasEstacaoRastreamento"."eras_descricao"',
        );

        $lista = $this->paginate('TErasEstacaoRastreamento');
        $this->set(compact('lista'));
    }

    function editar($codigo){
        $this->pageTitle = 'Atualizar Estações de Rastreamento';
        $conditions = array(
                        array('TErasEstacaoRastreamento.eras_codigo !=' => $codigo),
                               'TErasEstacaoRastreamento.eras_descricao NOT LIKE' => 'INATIVO'); 
        $estacoes = $this->TErasEstacaoRastreamento->find('list', compact('conditions'));
        if($this->RequestHandler->isPost()) {
            if ($this->TErasEstacaoRastreamento->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'listar_estacoes_rastreamento'));
            } else {
                $this->BSession->setFlash('save_error');
            }
            $this->data['TErasEstacaoRastreamento']['eras_codigo'] = $dado[0]['TErasEstacaoRastreamento']['eras_codigo'];
            $this->data['TErasEstacaoRastreamento']['eras_descricao'] = $dado[0]['TErasEstacaoRastreamento']['eras_descricao'];
            $this->data['TErasEstacaoRastreamento']['eras_ramal'] = $dado[0]['TErasEstacaoRastreamento']['eras_ramal'];
            $this->data['TErasEstacaoRastreamento']['eras_logistico'] = $dado[0]['TErasEstacaoRastreamento']['eras_logistico'];
        }else{
            $alertasTipos = $this->TErasEstacaoRastreamento->carregar($codigo);
            $this->data = $alertasTipos;
        }
        $this->set(compact('estacoes'));
    }
}