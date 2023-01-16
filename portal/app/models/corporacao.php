<?php

    class Corporacao extends AppModel {
     
        var $name = 'Corporacao';
        var $tableSchema = 'dbo';
        var $databaseTable = 'RHHealth';
        var $useTable = 'corporacao';
        var $displayField = 'descricao';
        var $primaryKey = 'codigo';
        var $actsAs = array('Secure');
        
        
        public function listaCorporacaoJson( $id = null ) {
            
            if( is_null( $id ) || empty( $id ) )            
                $resultado = $this->find( 'all', array( 'fields' => array( 'codigo', 'descricao' ) ) );
            else
                $resultado = $this->find( 'all', array( 'conditions' => array( 'codigo' => $id ), 'fields' => array( 'descricao' ) ) );
            
            return $this->retiraModel( $resultado );
        }
    }

?>