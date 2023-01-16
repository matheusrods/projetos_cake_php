<?php
class SimuladorPgrController extends AppController {
    public $name = 'SimuladorPgr';
    public $uses = array(
        'TPgpgPg',
    );
    
    function carregaCombos(){
        $this->loadModel("TTtraTipoTransporte");
        $tipo_transporte = $this->TTtraTipoTransporte->find('list');
        $this->set(compact('tipo_transporte'));
    }
    function index() {
        $this->carregaCombos();
        $this->pageTitle = 'Simulador de PGR';
        $this->data['TPgpgPg'] = $this->Filtros->controla_sessao($this->data, "TPgpgPg");
        $this->data['TPgpgPg']['filtro'] = false;
    } 
    
    function listagem(){
        $this->data['TPgpgPg'] = $this->Filtros->controla_sessao($this->data, "TPgpgPg");
        $listagem = $this->TPgpgPg->busca_pgr($this->data['TPgpgPg']);
        $this->set(compact('listagem'));        
    }
     

}
?> 