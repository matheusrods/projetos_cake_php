<?php
class EmbarcadoresTransportadoresLogController extends AppController {
    public $name = 'EmbarcadoresTransportadoresLog';
    public $uses = array('EmbarcadorTransportadorLog');
    
    function index(){
        $this->pageTitle = 'Log Embarcadores Transportadores';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->EmbarcadorTransportadorLog->name);
        $this->data[$this->EmbarcadorTransportadorLog->name] = $filtros;
    }

    function listagem(){
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->EmbarcadorTransportadorLog->name);
        if(!empty($filtros)){
            $conditions = $this->EmbarcadorTransportadorLog->converteFiltrosEmConditions($filtros);
            if(!empty($conditions)){
                $embarcadores_transportadores_log = $this->EmbarcadorTransportadorLog->listar($conditions);
                $this->set(compact('embarcadores_transportadores_log'));
            }else{
                $preencher = true;
                $this->set(compact('preencher'));
            }
        }else{
            $preencher = true;
            $this->set(compact('preencher'));
        }
    }
    
    
}
?>
