<?php

class Rota extends AppModel {

    var $name 			= 'Rota';
    var $tableSchema 	= 'dbo';
    var $databaseTable 	= 'Monitora';
    var $useTable 		= 'rota';
    var $primaryKey 	= 'Codigo';    
   
    var $validate 		= array(                
        
        'cidade_origem' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe a cidade de origem!'
            ),              
        ),
        'cidade_destino' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe a cidade de destino!'
            ),              
        ),
        'KM' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe a quilometragem!'
            ),               
        ),                       
    ); 


    public function converterFiltrosEmConditions($filtro){

        $conditions = array();        

        if( isset($filtro['Rota']['codigo']) && !empty($filtro['Rota']['codigo']) )
            $conditions['Rota.Codigo'] = $filtro['Rota']['codigo'];

        if( isset($filtro['Rota']['descricao']) && !empty($filtro['Rota']['descricao']) )
            $conditions['Rota.descricao LIKE'] = "%" . $filtro['Rota']['descricao'] . "%";

        if( isset($filtro['Rota']['origem']) && !empty($filtro['Rota']['origem']) )
            $conditions['Rota.Origem'] = $filtro['Rota']['origem'];

        if( isset($filtro['Rota']['destino']) && !empty($filtro['Rota']['destino']) )
            $conditions['Rota.Destino'] = $filtro['Rota']['destino'];

        return $conditions;
    }


    public function joinsListagemRotas(){        

        $Cidade =& ClassRegistry::Init('Cidade');

        $joins = array(                        
            array(
                'table' => "{$Cidade->databaseTable}.{$Cidade->tableSchema}.{$Cidade->useTable}",
                'alias' => 'CidadeOrigem',
                'conditions' => array('Rota.Origem = CidadeOrigem.codigo','CidadeOrigem.status' => 'S')
            ),
            array(
                'table' => "{$Cidade->databaseTable}.{$Cidade->tableSchema}.{$Cidade->useTable}",
                'alias' => 'CidadeDestino',
                'conditions' => array('Rota.Destino = CidadeDestino.codigo','CidadeDestino.status' => 'S')
            ),
        );

        return $joins;       
    }

    function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = 1, $extra = array()) {        
        if( isset($extra['extra']['joins']) ){
            $joins = $extra['extra']['joins'];            
            return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'joins'));
        }

        return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'extra'));
    }

    public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
        if( isset($extra['extra']['joins']) ){
            $joins = $extra['extra']['joins'];            
            return $this->find('count', compact('conditions', 'recursive', 'extra', 'joins'));
        }

        return $this->find('count', compact('conditions', 'recursive', 'extra'));
    }

    public function nextValRota(){
        $result = $this->find('first',array('fields'=>'(MAX(Codigo) + 1) AS codigo'));
        return $result[0]['codigo'];
    }
    
}