<?php
class TipoTransporteController extends AppController {
    public $name = 'TipoTransporte';
    var $uses = array('TTtraTipoTransporte');

    function index() {
        
        $this->pageTitle ='Tipos de Transporte';
        $this->data['TTtraTipoTransporte'] = $this->Filtros->controla_sessao($this->data,'TTtraTipoTransporte');
        
    }

    function listagem() {
        $filtros    = $this->Filtros->controla_sessao($this->data, 'TTtraTipoTransporte');
        $conditions = $this->TTtraTipoTransporte->converteFiltroEmCondition($filtros); 
        $produtos   = $this->TTtraTipoTransporte->listarTipoTransporte($conditions);
        $this->set(compact('produtos'));
    }

    function incluir() {
        $this->pageTitle = 'Incluir Tipo de Transporte';
        
        if ($this->data){            
           $this->data['TTtraTipoTransporte']['ttra_descricao'] = $this->TTtraTipoTransporte->converteMaiusculo($this->data['TTtraTipoTransporte']['ttra_descricao']);
                        
            if ($this->TTtraTipoTransporte->incluir($this->data)) {

                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'TipoTransporte','action' => 'index'));

            } else {
               
                $this->BSession->setFlash('save_error');

            }
        }
    }

    
    function editar($codigo_tipo) {
        $this->pageTitle = 'Editar Tipo de Transporte'; 
            
        if (!empty($this->data)) {
             $this->data['TTtraTipoTransporte']['ttra_descricao'] = $this->TTtraTipoTransporte->converteMaiusculo($this->data['TTtraTipoTransporte']['ttra_descricao']);
            if ($this->TTtraTipoTransporte->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'tipo_transporte', 'action' => 'index'));
            } else {
               $produto = null;
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->TTtraTipoTransporte->carregar($codigo_tipo);
        }

        $this->set(compact('codigo_tipo'));
    }
}