<?php
class ReguladoresRegioesController extends AppController {
    public $name = 'ReguladoresRegioes';
    var $uses = array(
        'ReguladorRegiao',
    );

    function regioes_por_regulador($codigo_regulador){
        $this->layout = 'ajax';
        $regioes = $this->ReguladorRegiao->regioes_regulador($codigo_regulador);
        $this->set(compact('regioes'));
    }

    function incluir($codigo_regulador) {
       $this->layout = 'new_window';
       if($this->data['ReguladorRegiao']){
            if (!empty($this->data)) {
                $this->data['ReguladorRegiao']['codigo_regulador'] = $codigo_regulador;
                if ($this->ReguladorRegiao->incluir($this->data)) {
                    $this->BSession->setFlash('save_success');                    
                } else {
                  $this->BSession->setFlash('save_error');
                }
            }              
        }
    }

    function excluir($codigo_regulador_regiao) {
        if ($this->RequestHandler->isPost()) {
            if ($this->ReguladorRegiao->excluir($codigo_regulador_regiao))
                die();                
        }
    }

    function editar($codigo){
        $this->layout = 'new_window';
        if (!empty($this->data)) {            
            if ($this->ReguladorRegiao->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');                
            } else {
                $this->BSession->setFlash('save_error');                
            }
        } else {
            $this->data = $this->ReguladorRegiao->read(null, $codigo);
        }         
    }

    function visualizar($codigo){
        $this->layout = 'new_window';
        $this->data = $this->ReguladorRegiao->read(null, $codigo);
    }

}
?>