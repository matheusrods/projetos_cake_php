<?php
class Tranpcc extends AppModel {
    var $name = 'Tranpcc';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbNavegarqNatec';
    var $useTable = 'tranpcc';
    var $primaryKey = null;
    var $actsAs = array('Secure');
    
    function listaCodigosDeContas($filtros, $grupo_empresa = 1){
        App::import('Model', 'LojaNaveg');
        $this->Notafis = ClassRegistry::init('Notafis');
        $this->Tranpag = ClassRegistry::init('Tranpag');
        
        if ($this->useDbConfig != 'test_suite') {
            $this->GrupoEmpresa             = ClassRegistry::init('GrupoEmpresa');
            $this->databaseTable            = $this->GrupoEmpresa->getDataBase( $grupo_empresa );
            $this->Notafis->databaseTable   = $this->databaseTable;
            $this->Tranpag->databaseTable   = $this->databaseTable;
        }
        
        $joins = array(
            array(
                'table' => $this->Notafis->databaseTable . '.' . $this->Notafis->tableSchema . '.tranpag',
                'alias' => 'Tranpag',
                'type' => 'LEFT',
                'conditions' => 'Tranpcc.numero = Tranpag.numero',
            ),
            array(
                'table' => $this->Notafis->databaseTable . '.' . $this->Notafis->tableSchema . '.grflux',
                'alias' => 'Grflux',
                'type' => 'LEFT',
                'conditions' => 'Tranpcc.ccusto = Tranpag.ccusto and Tranpag.grflux = Grflux.codigo',
            ),
            array(
                'table' => $this->Notafis->databaseTable . '.' . $this->Notafis->tableSchema . '.Sbflux',
                'alias' => 'Sbflux',
                'type' => 'LEFT',
                'conditions' => 'Tranpag.sbflux = Sbflux.codigo and Sbflux.grflux = Grflux.codigo',
            )
        );
        
        $conditions = array();
        if ( isset($filtros['Tranpag']['empresa']) && !empty($filtros['Tranpag']['empresa']) )
            $conditions[] = 'Tranpag.empresa = ' . (int)$filtros['Tranpag']['empresa'];
        
        if ( isset($filtros['Tranpag']['ccusto']) && !empty($filtros['Tranpag']['ccusto']) )
            $conditions['Tranpag.ccusto'] =  $filtros['Tranpag']['ccusto'];
        
        if ( isset($filtros['Tranpag']['sub_codigo']) && !empty($filtros['Tranpag']['sub_codigo']) )
            $conditions['Tranpag.grflux'] =  $filtros['Tranpag']['sub_codigo'];
        
        $resultado = $this->find( 'all', array(
                'fields' => array(
                     'Sbflux.codigo'
                    ,'Sbflux.descricao'
                ),
                'group' => 'Sbflux.codigo,Sbflux.descricao',
                'conditions' => $conditions,
                'joins' => $joins,
                'order' => 'Sbflux.codigo'
            )
        );
        return $resultado;
    }
    
    function listaSubCodigosCentroDeCusto($filtros, $grupo_empresa = 1){
        App::import('Model', 'LojaNaveg');
        $this->Notafis = ClassRegistry::init('Notafis');
        $this->Tranpag = ClassRegistry::init('Tranpag');
        if ($this->useDbConfig != 'test_suite') {
            $this->GrupoEmpresa             = ClassRegistry::init('GrupoEmpresa');
            $this->databaseTable            = $this->GrupoEmpresa->getDataBase( $grupo_empresa );
            $this->Notafis->databaseTable   = $this->databaseTable;
            $this->Tranpag->databaseTable   = $this->databaseTable;
        }
        
        $joins = array(
            array(
                'table' => $this->Notafis->databaseTable . '.' . $this->Notafis->tableSchema . '.tranpag',
                'alias' => 'Tranpag',
                'type' => 'LEFT',
                'conditions' => 'Tranpcc.numero = Tranpag.numero',
            ),
            array(
                'table' => $this->Notafis->databaseTable . '.' . $this->Notafis->tableSchema . '.grflux',
                'alias' => 'Grflux',
                'type' => 'LEFT',
                'conditions' => 'Tranpcc.ccusto = Tranpag.ccusto and Tranpag.grflux = Grflux.codigo',
            )
        );
        
        $conditions = array();
        if ( isset($filtros['Tranpag']['empresa']) && !empty($filtros['Tranpag']['empresa']) )
            $conditions[] = 'Tranpag.empresa = ' . (int)$filtros['Tranpag']['empresa'];
        
        if ( isset($filtros['Tranpag']['ccusto']) && !empty($filtros['Tranpag']['ccusto']) )
            $conditions['Tranpag.ccusto'] =  $filtros['Tranpag']['ccusto'];
        
        $resultado = $this->find( 'all', array(
                'fields' => array(
                     'Grflux.codigo'
                    ,'Grflux.descricao'
                ),
                'group' => 'Grflux.codigo,Grflux.descricao',
                'conditions' => $conditions,
                'joins' => $joins,
                'order' => 'Grflux.codigo'
            )
        );
        return $resultado;
    }
    
    function listaCentrosDeCustos($filtros, $grupo_empresa = 1){
        App::import('Model', 'LojaNaveg');
        $this->Notafis = ClassRegistry::init('Notafis');
        $this->Tranpag = ClassRegistry::init('Tranpag');
        if ($this->useDbConfig != 'test_suite') {
            $this->GrupoEmpresa             = ClassRegistry::init('GrupoEmpresa');
            $this->databaseTable            = $this->GrupoEmpresa->getDataBase( $grupo_empresa );
            $this->Notafis->databaseTable   = $this->databaseTable;
            $this->Tranpag->databaseTable   = $this->databaseTable;
        }
        
        $conditions = array();
        if ( isset($filtros['Tranpag']['empresa']) && !empty($filtros['Tranpag']['empresa']) )
            $conditions[] = 'Tranpag.empresa = ' . (int)$filtros['Tranpag']['empresa'];
        
        $joins = array(
            array(
                'table' => $this->Notafis->databaseTable . '.' . $this->Notafis->tableSchema . '.tranpag',
                'alias' => 'Tranpag',
                'type' => 'LEFT',
                'conditions' => 'Tranpcc.numero = Tranpag.numero',
            ),
            array(
                'table' => $this->Notafis->databaseTable . '.' . $this->Notafis->tableSchema . '.ccusto',
                'alias' => 'CentroCusto',
                'type' => 'LEFT',
                'conditions' => 'Tranpag.ccusto = CentroCusto.codigo',
            )
        );
        
        $resultado = $this->find( 'all', array(
                'fields' => array(
                     'CentroCusto.codigo'
                    ,'CentroCusto.descricao'
                ),
                'group' => 'CentroCusto.codigo,CentroCusto.descricao',
                'conditions' => $conditions,
                'joins' => $joins,
                'order' => 'CentroCusto.codigo'
            )
        );
        return $resultado;
    }
    
    function listaTitulosPagos($filtros, $grupo_empresa = 1) {
        App::import('Model', 'LojaNaveg');
        $this->Notafis         = ClassRegistry::init('Notafis');
        $this->Tranpag         = ClassRegistry::init('Tranpag');
        $this->Tranpcc         = ClassRegistry::init('Tranpcc');
        $this->MetaCentroCusto = ClassRegistry::init('MetaCentroCusto');
        $this->GrupoEmpresa    = ClassRegistry::init('GrupoEmpresa');
        $this->CentroCusto     = ClassRegistry::init('CentroCusto');

        if ($this->useDbConfig != 'test_suite') {            
            $this->databaseTable            = $this->GrupoEmpresa->getDataBase( $grupo_empresa );
            $this->Notafis->databaseTable   = $this->databaseTable;
            $this->Tranpag->databaseTable   = $this->databaseTable;
        }

        $conditions = array();
        $data_inicial = AppModel::dateToDbDate( $filtros['Tranpag']['data_inicial'] );
        $data_final   = AppModel::dateToDbDate( $filtros['Tranpag']['data_final'] );
        $conditions['Tranpag.dtpagto BETWEEN ? AND ?'] = array($data_inicial, $data_final);
        $conditions[] = 'Tranpag.dtpagto IS NOT NULL';
        
        if ( isset($filtros['Tranpag']['empresa']) && !empty($filtros['Tranpag']['empresa']) )
            $conditions[] = 'Tranpag.empresa = ' . (int)$filtros['Tranpag']['empresa'];
        
        $joins = array(
            array(
                'table' => $this->Notafis->databaseTable . '.' . $this->Notafis->tableSchema . '.tranpag',
                'alias' => 'Tranpag',
                'type' => 'LEFT',
                'conditions' => 'Tranpcc.numero         = Tranpag.numero 
                                 and Tranpcc.serie      = Tranpag.serie 
                                 and Tranpcc.tipodoc    = Tranpag.tipodoc 
                                 and Tranpcc.emitente   = Tranpag.emitente 
                                 and Tranpcc.tiptit     = Tranpag.tiptit 
                                 and Tranpcc.ordem      = Tranpag.ordem 
                                 and Tranpcc.tipoemit   = Tranpag.tipoemit',
            ),
            array(
                'table' => $this->Notafis->databaseTable . '.' . $this->Notafis->tableSchema . '.ccusto',
                'alias' => 'CentroCusto',
                'type' => 'LEFT',
                'conditions' => 'Tranpcc.ccusto = CentroCusto.codigo',
            )
        );
        
        $resultado = $this->find( 'all', array(
                'fields' => array(
                     'Tranpcc.ccusto'
                    ,'CentroCusto.descricao'
                    ,'SUM(Tranpcc.valor) as val_final'
                ),
                'group' => 'Tranpcc.ccusto,CentroCusto.descricao',
                'conditions' => $conditions,
                'joins' => $joins,
                'order' => 'Tranpcc.ccusto'
            )
        );
        return $resultado;
    }
    
    function listaTitulosPagosPorCentroDeCusto($filtros, $grupo_empresa = 1){
        App::import('Model', 'LojaNaveg');
        $this->Notafis = ClassRegistry::init('Notafis');
        //$this->Tranpag = ClassRegistry::init('NTranpag');
        
        if ($this->useDbConfig != 'test_suite') {
            $this->databaseTable            = 'dbNavegarqNatec';
           // $this->Notafis->databaseTable   = 'dbNavegarqLider';
           // $this->Tranpag->databaseTable   = 'dbNavegarqLider';
        }
        
        $conditions = array();
        $data_inicial = AppModel::dateToDbDate( $filtros['Tranpag']['data_inicial'] );
        $data_final   = AppModel::dateToDbDate( $filtros['Tranpag']['data_final'] );
        $conditions['Tranpag.dtpagto BETWEEN ? AND ?'] = array($data_inicial, $data_final);
        $conditions[] = 'Tranpag.dtpagto IS NOT NULL';
        
        $joins = array(
            array(
                'table' => $this->Notafis->databaseTable . '.' . $this->Notafis->tableSchema . '.tranpag',
                'alias' => 'Tranpag',
                'type' => 'LEFT',
                'conditions' => 'Tranpcc.numero         = Tranpag.numero 
                                 and Tranpcc.serie      = Tranpag.serie 
                                 and Tranpcc.tipodoc    = Tranpag.tipodoc 
                                 and Tranpcc.emitente   = Tranpag.emitente 
                                 and Tranpcc.tiptit     = Tranpag.tiptit 
                                 and Tranpcc.ordem      = Tranpag.ordem 
                                 and Tranpcc.tipoemit   = Tranpag.tipoemit',
            ),
            array(
                'table' => $this->Notafis->databaseTable . '.' . $this->Notafis->tableSchema . '.grflux',
                'alias' => 'Grflux',
                'type' => 'LEFT',
                'conditions' => 'Tranpag.grflux = Grflux.codigo',
            )
        );
        
        if ( isset($filtros['Tranpag']['empresa']) && !empty($filtros['Tranpag']['empresa']) )
            $conditions[] = 'Tranpag.empresa = ' . (int)$filtros['Tranpag']['empresa'];
        
        if ( isset($filtros['Tranpag']['ccusto']) && !empty($filtros['Tranpag']['ccusto']) )
            $conditions['Tranpcc.ccusto'] =  $filtros['Tranpag']['ccusto'];
        
        $resultado = $this->find( 'all', array(
                'fields' => array(
                     'Tranpcc.ccusto'
                    ,'Grflux.codigo'
                    ,'Grflux.descricao'
                    ,'sum(Tranpcc.valor) as val_final'
                ),
                'group' => 'Tranpcc.ccusto,Grflux.codigo,Grflux.descricao',
                'conditions' => $conditions,
                'joins' => $joins,
                'order' => 'Grflux.codigo'
            )
        );
        return $resultado;
    }
    
    function listaTitulosPagosPorCentroDeCustoSubCodigo($filtros, $grupo_empresa = 1){
        App::import('Model', 'LojaNaveg');
        $this->Notafis = ClassRegistry::init('Notafis');
       //$this->Tranpag = ClassRegistry::init('NTranpag');
        
        if ($this->useDbConfig != 'test_suite') {
            $this->databaseTable            = 'dbNavegarqNatec';
            $this->Notafis->databaseTable   = 'dbNavegarqNatec';
           // $this->Tranpag->databaseTable   = 'dbNavegarqNatec';
        }
        
        $conditions = array();
        $data_inicial = AppModel::dateToDbDate( $filtros['Tranpag']['data_inicial'] );
        $data_final   = AppModel::dateToDbDate( $filtros['Tranpag']['data_final'] );
        $conditions['Tranpag.dtpagto BETWEEN ? AND ?'] = array($data_inicial, $data_final);
        $conditions[] = 'Tranpag.dtpagto IS NOT NULL';
        
        $joins = array(
            array(
                'table' => $this->Notafis->databaseTable . '.' . $this->Notafis->tableSchema . '.tranpag',
                'alias' => 'Tranpag',
                'type' => 'LEFT',
                'conditions' => 'Tranpcc.numero         = Tranpag.numero 
                                 and Tranpcc.serie      = Tranpag.serie 
                                 and Tranpcc.tipodoc    = Tranpag.tipodoc 
                                 and Tranpcc.emitente   = Tranpag.emitente 
                                 and Tranpcc.tiptit     = Tranpag.tiptit 
                                 and Tranpcc.ordem      = Tranpag.ordem 
                                 and Tranpcc.tipoemit   = Tranpag.tipoemit',
            ),
            array(
                'table' => $this->Notafis->databaseTable . '.' . $this->Notafis->tableSchema . '.Sbflux',
                'alias' => 'Sbflux',
                'type' => 'LEFT',
                'conditions' => 'Tranpag.grflux = Sbflux.grflux and Tranpag.sbflux = Sbflux.codigo',
            )
        );
        
        if ( isset($filtros['Tranpag']['empresa']) && !empty($filtros['Tranpag']['empresa']) )
            $conditions[] = 'Tranpag.empresa = ' . (int)$filtros['Tranpag']['empresa'];
        
        if ( isset($filtros['Tranpag']['ccusto']) && !empty($filtros['Tranpag']['ccusto']) )
            $conditions['Tranpcc.ccusto'] =  $filtros['Tranpag']['ccusto'];
        
        if ( isset($filtros['Tranpag']['sub_codigo']) && !empty($filtros['Tranpag']['sub_codigo']) )
            $conditions['Tranpag.grflux'] =  $filtros['Tranpag']['sub_codigo'];
        
        $resultado = $this->find( 'all', array(
                'fields' => array(
                     'Sbflux.codigo'
                    ,'Sbflux.descricao'
                    ,'SUM(Tranpcc.valor) AS val_final'
                ),
                'group' => 'Sbflux.codigo,Sbflux.descricao',
                'conditions' => $conditions,
                'joins' => $joins,
                'order' => 'Sbflux.codigo'
            )
        );
        return $resultado;
    }
    

    function listaTitulosPagosPorCentroDeCustoSubCodigoConta($filtros, $grupo_empresa = 1){
        App::import('Model', 'LojaNaveg');
        $this->Notafis = ClassRegistry::init('Notafis');
        //$this->Tranpag = ClassRegistry::init('NTranpag');
        
        if ($this->useDbConfig != 'test_suite') {
            $this->databaseTable            = 'dbNavegarqNatec';
            $this->Notafis->databaseTable   = 'dbNavegarqNatec';
            //$this->Tranpag->databaseTable   = 'dbNavegarqNatec';
        }
        
        $conditions = array();
        //ve(array($filtros['Tranpag']['data_inicial'], $filtros['Tranpag']['data_final']));
        $data_inicial = DateTime::createFromFormat('d/m/Y',  $filtros['Tranpag']['data_inicial']);
        $data_final = DateTime::createFromFormat('d/m/Y',  $filtros['Tranpag']['data_final']);
        // $data_inicial = AppModel::dateToDbDate( $filtros['Tranpag']['data_inicial'] );
        // $data_final   = AppModel::dateToDbDate( $filtros['Tranpag']['data_final'] );
        //ve(array($data_inicial, $data_final));
        $conditions['Tranpag.dtpagto BETWEEN ? AND ?'] = array($data_inicial->format('Ymd'), $data_final->format('Ymd'));
        $conditions[] = 'Tranpag.dtpagto IS NOT NULL';
        
        $joins = array(
            array(
                'table' => $this->Notafis->databaseTable . '.' . $this->Notafis->tableSchema . '.tranpag',
                'alias' => 'Tranpag',
                'type' => 'LEFT',
                'conditions' => 'Tranpcc.numero         = Tranpag.numero 
                                 and Tranpcc.serie      = Tranpag.serie 
                                 and Tranpcc.tipodoc    = Tranpag.tipodoc 
                                 and Tranpcc.emitente   = Tranpag.emitente 
                                 and Tranpcc.tiptit     = Tranpag.tiptit 
                                 and Tranpcc.ordem      = Tranpag.ordem 
                                 and Tranpcc.tipoemit   = Tranpag.tipoemit',
            ),
            array(
                'table' => $this->Notafis->databaseTable . '.' . $this->Notafis->tableSchema . '.grflux',
                'alias' => 'Grflux',
                'type' => 'LEFT',
                'conditions' => 'Tranpag.grflux = Grflux.codigo',
            )
            
        );
        
        if ( isset($filtros['Tranpag']['empresa']) && !empty($filtros['Tranpag']['empresa']) )
            $conditions[] = 'Tranpag.empresa = ' . (int)$filtros['Tranpag']['empresa'];
        
        if ( isset($filtros['Tranpag']['ccusto']) && !empty($filtros['Tranpag']['ccusto']) )
            $conditions['Tranpcc.ccusto'] =  $filtros['Tranpag']['ccusto'];
        
        if ( isset($filtros['Tranpag']['sub_codigo']) && !empty($filtros['Tranpag']['sub_codigo']) )
            $conditions['Tranpag.grflux'] =  $filtros['Tranpag']['sub_codigo'];
        
        if ( isset($filtros['Tranpag']['codigo_conta']) && !empty($filtros['Tranpag']['codigo_conta']) )
            $conditions['Tranpag.sbflux'] =  $filtros['Tranpag']['codigo_conta'];

       // $this->Tranpag
        
        $resultado = $this->find( 'all', array(
                'fields' => array(
                     'Tranpag.numero'
                    ,'Tranpag.ordem'
                    ,'Tranpag.serie'
                    ,'Tranpag.tipodoc'
                    ,'Tranpag.emitente'
                    ,'Tranpag.razao'
                    ,'CONVERT(varchar(10), Tranpag.dtemiss, 103) as data_emissao'
                    ,'CONVERT(varchar(10), Tranpag.dtvencto, 103) as data_vencimento'
                    ,'Tranpag.historico'
                    ,'Tranpag.ccusto'
                    ,'Tranpcc.valor'
                    ,'Tranpcc.numconta'
                ),
                'group' => 'Tranpag.numero,Tranpag.ordem,Tranpag.serie,Tranpag.tipodoc,Tranpag.emitente,Tranpag.razao,Tranpag.dtemiss,Tranpag.dtvencto,Tranpag.historico,Tranpag.ccusto,Tranpcc.valor,Tranpcc.numconta',
                'conditions' => $conditions,
                'joins' => $joins,
                'order' => 'Tranpag.numero'
            )
        );
        //ve($resultado);
        return $resultado;
    }
}
?>