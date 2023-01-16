<?php class PagamentosTransacoesController extends AppController {

    public $name = 'PagamentosTransacoes';
    public $layout = 'default';
    public $components = array('Filtros', 'RequestHandler', 'EmailAlteracaoCliente');        
    public $helpers = array('Html', 'Ajax', 'Highcharts');
    public $uses = array('Tranpcc', 'GrupoEmpresa');

    function index(){}
    
    function listar_codigos_de_conta($grupo_empresa, $empresa, $ccusto, $sub_codigo){
        $this->layout    = 'ajax';
        $this->pageTitle = 'Sub Códigos';
        $this->LojaNaveg = ClassRegistry::init('LojaNaveg');
        $dados           = array();
        $filtros         = array();
        
        if(isset($empresa) && !empty($empresa) || $empresa != 0)
            $filtros['Tranpag']['empresa'] = $empresa;
        
        if(isset($ccusto) && !empty($ccusto) || $ccusto != 0)
            $filtros['Tranpag']['ccusto'] = $ccusto;
        
        if(isset($sub_codigo) && !empty($sub_codigo) || $sub_codigo != 0)
            $filtros['Tranpag']['sub_codigo'] = $sub_codigo;
        
        if(isset($grupo_empresa) && !empty($grupo_empresa))
            $dados   = $this->Tranpcc->listaCodigosDeContas($filtros,$grupo_empresa);
        
        $this->set(compact('dados'));
    }
    
    function listar_centros_de_custos_sub_codigos($grupo_empresa, $empresa, $ccusto){
        $this->layout    = 'ajax';
        $this->pageTitle = 'Sub Códigos';
        $this->LojaNaveg = ClassRegistry::init('LojaNaveg');
        $dados           = array();
        $filtros         = array();
        
        if(isset($empresa) && !empty($empresa) || $empresa != 0)
            $filtros['Tranpag']['empresa'] = $empresa;
        
        if(isset($ccusto) && !empty($ccusto) || $ccusto != 0)
            $filtros['Tranpag']['ccusto'] = $ccusto;
        
        if(isset($grupo_empresa) && !empty($grupo_empresa))
            $dados   = $this->Tranpcc->listaSubCodigosCentroDeCusto($filtros,$grupo_empresa);
        
        $this->set(compact('dados'));
    }
    
    function listar_centros_de_custos($grupo_empresa, $empresa){
        $this->layout    = 'ajax';
        $this->pageTitle = 'Centros de Custos';
        $this->LojaNaveg = ClassRegistry::init('LojaNaveg');
        $dados           = array();
        $filtros         = array();
        
        if(isset($empresa) && !empty($empresa) || $empresa != 0)
            $filtros['Tranpag']['empresa'] = $empresa;
        
        if(isset($grupo_empresa) && !empty($grupo_empresa))
            $dados   = $this->Tranpcc->listaCentrosDeCustos($filtros,$grupo_empresa);
        
        $this->set(compact('dados'));
    }
    
    function listar_titulos_pagos(){
        $this->pageTitle = 'Títulos Pagos por Centro de Custo';
        $this->LojaNaveg = ClassRegistry::init('LojaNaveg');
        $grupo_empresa   = LojaNaveg::GRUPO_RHHEALTH;
        $dados           = array();
        
        if(!empty($this->data)){
            $this->data['Tranpag']['grupo_empresa'] = LojaNaveg::GRUPO_RHHEALTH;
            $filtros = $this->data;
            
            if(isset($this->data['Tranpag']['grupo_empresa']) && !empty($this->data['Tranpag']['grupo_empresa']))
                $grupo_empresa  = $this->data['Tranpag']['grupo_empresa'];
            
            if(isset($this->data['Tranpag']['empresa']) && !empty($this->data['Tranpag']['empresa']))
                $filtros['Tranpag']['empresa'] = $this->data['Tranpag']['empresa'];
            
            $dados = $this->Tranpcc->listaTitulosPagos($filtros, $grupo_empresa);
            
        }else{
            $this->data['Tranpag']['grupo_empresa'] = $grupo_empresa;
            $this->data['Tranpag']['data_inicial']  = date('01/m/Y');
            $this->data['Tranpag']['data_final']    = date('d/m/Y');
            $this->data['Tranpag']['grupo_empresa']  = LojaNaveg::GRUPO_RHHEALTH;
        }
        $empresas = $this->LojaNaveg->listEmpresas($grupo_empresa);
        //$grupos_empresas = $this->LojaNaveg->listGrupos();
        $grupos_empresas = array();
        $nome_grupo = $this->LojaNaveg->nomeGrupoPorId($grupo_empresa);
        
        $this->set(compact('dados','grupos_empresas','empresas','nome_grupo'));
    }

    function listar_titulos_pagos_solen(){
        $this->pageTitle = 'Demonstrativo de Orçamento';
        $this->LojaNaveg = ClassRegistry::init('LojaNaveg');
        $grupo_empresa   = GrupoEmpresa::SOLEN;
        $dados           = array();
        $this->data['Tranpag'] = $this->Filtros->controla_sessao($this->data, 'Tranpag');
        if(!empty($this->data)){
            $filtros = $this->data;
            if(isset($this->data['Tranpag']['empresa']) && !empty($this->data['Tranpag']['empresa']))
                $filtros['Tranpag']['empresa'] = $this->data['Tranpag']['empresa'];

            if(isset($this->data['Tranpag']['centro_custo_descricao']) && !empty($this->data['Tranpag']['centro_custo_descricao'])){
                $filtros['Tranpag']['centro_custo_descricao'] = $this->data['Tranpag']['centro_custo_descricao'];
            }
            $dados = $this->Tranpcc->listaTitulosPagos($filtros, $grupo_empresa);
        }else{
            $this->data['Tranpag']['grupo_empresa'] = $grupo_empresa;
           // $this->data['Tranpag']['ano']  = date('Y');
           // $this->data['Tranpag']['mes']  = date('m');
        }

        $empresas = $this->LojaNaveg->listEmpresas($grupo_empresa);
        $grupos_empresas = $this->LojaNaveg->listGrupos();
        $nome_grupo = $this->LojaNaveg->nomeGrupoPorId($grupo_empresa);

        $this->set(compact('dados','grupos_empresas','empresas','nome_grupo'));
    }
    
    function listar_titulos_pagos_por_centro_custo(){
        $this->pageTitle = 'Títulos Pagos por Centro de Custo';
        $this->LojaNaveg = ClassRegistry::init('LojaNaveg');
        $grupo_empresa   = GrupoEmpresa::RHHEALTH;
        //$grupos_empresas = $this->LojaNaveg->listGrupos();
        $grupos_empresas = array();
        $dados           = array();
        $usuario = $this->BAuth->user();
        if ( $usuario['Usuario']['codigo_uperfil'] == 148 ){
            $grupo_empresa   = GrupoEmpresa::SOLEN;
            $grupos_empresas = array($grupo_empresa => $grupos_empresas[$grupo_empresa] );
            $this->data['Tranpag']['grupo_empresa'] = $grupo_empresa;
        }
        $this->data['Tranpag'] = $this->Filtros->controla_sessao($this->data, 'Tranpag');
        if(!empty($this->data)){
            $filtros = $this->data;
            if(isset($this->data['Tranpag']['grupo_empresa']) && !empty($this->data['Tranpag']['grupo_empresa']))
                $grupo_empresa  = $this->data['Tranpag']['grupo_empresa'];
            
            if(isset($this->data['Tranpag']['empresa']) && !empty($this->data['Tranpag']['empresa']))
                $filtros['Tranpag']['empresa'] = $this->data['Tranpag']['empresa'];
            
            if(isset($this->data['Tranpag']['ccusto']) && !empty($this->data['Tranpag']['ccusto']))
                $filtros['Tranpag']['ccusto'] = $this->data['Tranpag']['ccusto'];

            if(isset($this->data['Tranpag']['centro_custo_desc']) && !empty($this->data['Tranpag']['centro_custo_desc']))
                $filtros['Tranpag']['centro_custo_desc'] = $this->data['Tranpag']['centro_custo_desc'];
            
            if(isset($this->data['Tranpag']['centro_custo_descricao']) && !empty($this->data['Tranpag']['centro_custo_descricao']))
                $filtros['Tranpag']['centro_custo_descricao'] = $this->data['Tranpag']['centro_custo_descricao'];  

            $dados = $this->Tranpcc->listaTitulosPagosPorCentroDeCusto($filtros, $grupo_empresa);
            
        }else{
            $this->data['Tranpag']['grupo_empresa'] = $grupo_empresa;
            $this->data['Tranpag']['ano']  = date('Y');
            $this->data['Tranpag']['mes']  = date('m');            
        }

        $empresas = $this->LojaNaveg->listEmpresas($grupo_empresa);
        
        $nome_grupo = $this->LojaNaveg->nomeGrupoPorId($grupo_empresa);
        
        $this->set(compact('dados','grupos_empresas','empresas','nome_grupo'));
    }
    
    function listar_titulos_pagos_por_centro_custo_sub_codigo(){
        $this->pageTitle = 'Títulos Pagos por Centro de Custo';
        $this->LojaNaveg = ClassRegistry::init('LojaNaveg');
        $grupo_empresa   = LojaNaveg::GRUPO_RHHEALTH;
        $dados           = array();
        
        if(!empty($this->data)){
            $filtros = $this->data;
            
            if(isset($this->data['Tranpag']['grupo_empresa']) && !empty($this->data['Tranpag']['grupo_empresa']))
                $grupo_empresa  = $this->data['Tranpag']['grupo_empresa'];
            
            if(isset($this->data['Tranpag']['empresa']) && !empty($this->data['Tranpag']['empresa']))
                $filtros['Tranpag']['empresa'] = $this->data['Tranpag']['empresa'];
            
            if(isset($this->data['Tranpag']['ccusto']) && !empty($this->data['Tranpag']['ccusto']))
                $filtros['Tranpag']['ccusto'] = $this->data['Tranpag']['ccusto'];
            
            if(isset($this->data['Tranpag']['sub_codigo']) && !empty($this->data['Tranpag']['sub_codigo']))
                $filtros['Tranpag']['sub_codigo'] = $this->data['Tranpag']['sub_codigo'];
                
            $dados = $this->Tranpcc->listaTitulosPagosPorCentroDeCustoSubCodigo($filtros, $grupo_empresa);
            
        }else{
            $this->data['Tranpag']['grupo_empresa'] = $grupo_empresa;
            $this->data['Tranpag']['data_inicial']  = date('01/m/Y');
            $this->data['Tranpag']['data_final']    = date('d/m/Y');
        }
        
        $empresas = $this->LojaNaveg->listEmpresas($grupo_empresa);
        //$grupos_empresas = $this->LojaNaveg->listGrupos();
        $grupos_empresas = array();
        $nome_grupo = $this->LojaNaveg->nomeGrupoPorId($grupo_empresa);
        
        $this->set(compact('dados','grupos_empresas','empresas','nome_grupo'));
    }
    
    function listar_titulos_pagos_por_centro_custo_sub_codigo_conta(){
        $this->pageTitle = 'Títulos Pagos por Centro de Custo';
        $this->LojaNaveg = ClassRegistry::init('LojaNaveg');
        $grupo_empresa   = LojaNaveg::GRUPO_RHHEALTH;
        $dados           = array();
        
        if(!empty($this->data)){
            $filtros = $this->data;
            
            if(isset($this->data['Tranpag']['grupo_empresa']) && !empty($this->data['Tranpag']['grupo_empresa']))
                $grupo_empresa  = $this->data['Tranpag']['grupo_empresa'];
            
            if(isset($this->data['Tranpag']['empresa']) && !empty($this->data['Tranpag']['empresa']))
                $filtros['Tranpag']['empresa'] = $this->data['Tranpag']['empresa'];
            
            if(isset($this->data['Tranpag']['ccusto']) && !empty($this->data['Tranpag']['ccusto']))
                $filtros['Tranpag']['ccusto'] = $this->data['Tranpag']['ccusto'];
            
            if(isset($this->data['Tranpag']['sub_codigo']) && !empty($this->data['Tranpag']['sub_codigo']))
                $filtros['Tranpag']['sub_codigo'] = $this->data['Tranpag']['sub_codigo'];
            
            if(isset($this->data['Tranpag']['codigo_conta']) && !empty($this->data['Tranpag']['codigo_conta']))
                $filtros['Tranpag']['codigo_conta'] = $this->data['Tranpag']['codigo_conta'];
                
            $dados = $this->Tranpcc->listaTitulosPagosPorCentroDeCustoSubCodigoConta($filtros, $grupo_empresa);
            
        }else{
            $this->data['Tranpag']['grupo_empresa'] = $grupo_empresa;
            $this->data['Tranpag']['data_inicial']  = date('01/m/Y');
            $this->data['Tranpag']['data_final']    = date('d/m/Y');
        }
        
        $empresas = $this->LojaNaveg->listEmpresas($grupo_empresa);
        //$grupos_empresas = $this->LojaNaveg->listGrupos();
        $grupos_empresas = array();
        $nome_grupo = $this->LojaNaveg->nomeGrupoPorId($grupo_empresa);
        
        $this->set(compact('dados','grupos_empresas','empresas','nome_grupo'));
    }

}
