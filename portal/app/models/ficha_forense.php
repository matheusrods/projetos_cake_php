<?php

class FichaForense extends AppModel
{
    public $name          = 'FichaForense';       
    public $databaseTable = 'dbTeleconsult';
    public $tableSchema   = 'informacoes';
    public $useTable      = 'ficha_forense';
    public $primaryKey    = 'codigo';

    
    public function converterFiltrosEmConditions($filtro)
    {

        $conditions = array();
        
        $conditions['FichaForense.status'] = NULL;
        
        if( isset($filtro['codigo_ficha']) && !empty($filtro['codigo_ficha']) )
            $conditions['FichaForense.codigo_ficha'] = $filtro['codigo_ficha'];

        if( isset($filtro['codigo_cliente']) && !empty($filtro['codigo_cliente']) )
            $conditions['Cliente.codigo'] = $filtro['codigo_cliente'];

        if( isset($filtro['codigo_seguradora']) && !empty($filtro['codigo_seguradora']) )
            $conditions['Seguradora.codigo'] = $filtro['codigo_seguradora'];

        if( isset($filtro['codigo_documento']) && !empty($filtro['codigo_documento']) )
            $conditions['ProfissionalLog.codigo_documento'] = $filtro['codigo_documento'];

        if( isset($filtro['status']) && !empty($filtro['status']) )
            $conditions['FichaForense.status'] = $filtro['status'];

        return $conditions;
    }

    public function listarFichasPesquisaJoins()
    {
    	$Ficha           = ClassRegistry::init('Ficha');
    	$Cliente         = ClassRegistry::init('Cliente');
    	$Seguradora      = ClassRegistry::init('Seguradora');
    	$ProfissionalLog = ClassRegistry::init('ProfissionalLog');

        $joins = array(            
            
            array(
                'table' => "{$Ficha->databaseTable}.{$Ficha->tableSchema}.{$Ficha->useTable}",
                'alias' => 'Ficha',
                'conditions' => 'FichaForense.codigo_ficha = Ficha.codigo'
            ),
            array(
                'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
                'alias' => 'Cliente',
                'conditions' => 'Ficha.codigo_cliente = Cliente.codigo'
            ),
            array(
                'table' => "{$Seguradora->databaseTable}.{$Seguradora->tableSchema}.{$Seguradora->useTable}",
                'alias' => 'Seguradora',
                'conditions' => 'Cliente.codigo_seguradora = Seguradora.codigo'
            ),
            array(
                'table' => "{$ProfissionalLog->databaseTable}.{$ProfissionalLog->tableSchema}.{$ProfissionalLog->useTable}",
                'alias' => 'ProfissionalLog',
                'conditions' => 'Ficha.codigo_profissional_log = ProfissionalLog.codigo'
            ),
        );

		return $joins;
    }

    public function listarFichasPesquisaFields()
    {    	    	        
        $fields = array(
        	'FichaForense.codigo',
        	'FichaForense.codigo_ficha',
        	'Ficha.codigo',
        	'Seguradora.nome',
        	'Cliente.razao_social',
        	'ProfissionalLog.nome',
        	'ProfissionalLog.codigo_documento',
        );

        return $fields;
    }

    function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = 1, $extra = array()) {  

    	if( $extra['extra']['joins'] ){
    		$joins = $extra['extra']['joins'];    		
    		return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'joins'));
    	}

        return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive'));
    }

    public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {

        if( $extra['extra']['joins'] ){
        	$joins = $extra['extra']['joins'];
    		return $this->find('count', compact('conditions', 'recursive', 'joins'));
    	}

        return $this->find('count', compact('conditions', 'recursive'));
    }

    public function incluir($codigo_ficha) {
        try {
            $data = array(
                'FichaForense' => array(
                    'codigo_ficha' => $codigo_ficha,
                    'observacao'   => NULL,
                )
            );
            return $this->save($data);   
        } catch(Exception $e) {
            $msg = (!empty($ex) ? $ex->getmessage() : '');
            $this->invalidate('',$msg);
            return false;            
        }
    }
}