<?php
class ClienteTipo extends AppModel {
	var $name = 'ClienteTipo';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cliente_tipo';
	var $primaryKey = 'codigo';
	var $displayField = 'descricao';
	var $actsAs = array('Secure');
        
        public function listaClienteTipoJson( $tipo = null ) {
            
            if( is_null( $tipo ) )
                $data = $this->find( 'all', array( 'fields' => array( 'codigo', 'descricao' ) ) );
            else
                $data = $this->find( 'all', array( 'conditions' => array( 'codigo' => $tipo ), 'fields' => array( 'descricao' ) ) );
            
            return $this->retiraModel($data);
        }
}
?>