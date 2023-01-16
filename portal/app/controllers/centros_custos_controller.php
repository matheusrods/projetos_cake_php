<?php class CentrosCustosController extends AppController {

    public $name = 'CentrosCustos';
    public $layout = 'default';
    public $components = array('Filtros', 'RequestHandler', 'EmailAlteracaoCliente');
    public $helpers = array('Html', 'Ajax', 'Highcharts');
    public $uses = array('CentroCusto', 'Tranpag');

    function index() {
        //$this->carrega_combos();
        //$this->loadModel('TipoContato');
        //$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
    }
    
    function listar_titulos_pagos(){
        $this->pageTitle = 'Títulos Pagos por Centro de Custo';
        $this->LojaNaveg = ClassRegistry::init('LojaNaveg');
        $grupo_empresa   = LojaNaveg::GRUPO_BUONNY;
        $dados           = array();
        
        if(!empty($this->data)){
            $filtros = $this->data;
            
            if(isset($this->data['CentroCusto']['grupo_empresa']) && !empty($this->data['CentroCusto']['grupo_empresa']))
                $grupo_empresa  = $this->data['CentroCusto']['grupo_empresa'];
            
            if(isset($this->data['CentroCusto']['empresa']) && !empty($this->data['CentroCusto']['empresa']))
                $filtros['CentroCusto']['empresa'] = $this->data['CentroCusto']['empresa'];
                
            $dados = $this->Tranpag->listaTitulosPagosPorCentroDeCusto($filtros, $grupo_empresa);
            
        }else{
            $this->data['CentroCusto']['grupo_empresa'] = $grupo_empresa;
            $this->data['CentroCusto']['data_inicial']  = date('01/m/Y');
            $this->data['CentroCusto']['data_final']    = date('d/m/Y');
        }
        
        $empresas = $this->LojaNaveg->listEmpresas($grupo_empresa);
        $grupos_empresas = $this->LojaNaveg->listGrupos();
        $nome_grupo = $this->LojaNaveg->nomeGrupoPorId($grupo_empresa);
        
        $this->set(compact('dados','grupos_empresas','empresas','nome_grupo'));
    }

}
