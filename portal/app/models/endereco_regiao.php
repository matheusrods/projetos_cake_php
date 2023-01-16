<?php

class EnderecoRegiao extends AppModel {

    var $name = 'EnderecoRegiao';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'endereco_regiao';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure');

    function listarRegioes() {
        return $this->find('list', array(
            'fields' => array('codigo', 'descricao'),
            'order' => array('descricao'),
        ));
    }
    
    function listarRegioesJson( $filial = null ){
        
        if( is_null( $filial ) )        
            $regioes = $this->find( 'all', array( 'fields' => array(  'codigo', 'descricao' ) ) );
        else
            $regioes = $this->find( 'all', array( 'conditions' => array( 'codigo' => $filial ), 'fields' => array(  'descricao' ) ) );
            
        return $this->retiraModel( $regioes );
    }
    function converteFiltroEmCondition($data) {
        $conditions = array();
        if (!empty($data['codigo']))
            $conditions['EnderecoRegiao.codigo'] = $data['codigo'];
        if (!empty($data['descricao']))
            $conditions['EnderecoRegiao.descricao like'] = '%' . $data['descricao'] . '%';

        return $conditions;
    }
}

?>