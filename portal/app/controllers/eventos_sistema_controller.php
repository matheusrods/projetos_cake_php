<?php
class EventosSistemaController extends AppController {
    public $name = 'EventosSistema';
    var $uses = array('TEsisEventoSistema');    

    public function eventos_logisticos() {
        $placa = $this->data['TEsisEventoSistema']['placa'];
        $data_inicial = $this->data['TEsisEventoSistema']['data_inicial'];
        $data_final = $this->data['TEsisEventoSistema']['data_final'];
        $eventos_logisticos = $this->TEsisEventoSistema->eventosLogisticosSistemaPeriferico($placa,$data_inicial,$data_final);
        $this->set(compact('eventos_logisticos'));
    }    
}