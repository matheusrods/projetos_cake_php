<?php
class CboController extends AppController {
    public $name = 'Cbo';
    var $uses = array(  'Cbo');
       
    public function carrega_cbo($codigo_cbo) {
        $this->layout = 'ajax';
        $dados_cbo = $this->Cbo->buscar_Cbo($codigo_cbo);
        echo json_encode($dados_cbo);
        $this->autoRender = false;
    }

    public function localiza_cbo() {
        $this->layout = 'ajax_placeholder';
        $input_id = !empty($this->passedArgs['input_id']) ? $this->passedArgs['input_id'] : '';
        $input_display = !empty($this->passedArgs['input_display']) ? $this->passedArgs['input_display'] : $this->data['Cbo']['input_display'];

        $this->data['Cbo'] = $this->Filtros->controla_sessao($this->data, $this->Cbo->name);
        
        $this->set(compact('input_id','input_display'));

    }

    function buscar_listagem($destino) {
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Cbo->name);

        $conditions = $this->Cbo->converteFiltroEmCondition($filtros);
        
        $this->paginate['Cbo'] = array(
            'conditions' => $conditions,
            'limit' => 10,
            'order' => 'codigo_cbo',
        );

        $cbo = $this->paginate('Cbo');
        $this->set(compact('cbo', 'destino'));

        if (isset($this->passedArgs['input_id'])){
            $this->set('input_id', str_replace('-search', '', $this->passedArgs['input_id']));
            }
        
        if (isset($this->passedArgs['input_display']))
            $this->set('input_display', str_replace('-search', '', $this->passedArgs['input_display']));
    
    }

}