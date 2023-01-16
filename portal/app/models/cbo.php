<?php
class Cbo extends AppModel {

    var $name = 'Cbo';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'Cbo';
    var $primaryKey = 'codigo_cbo';
    var $actsAs = array('Secure');
    
    public $virtualFields = array('descricao_cbo' => 'CONCAT(Cbo.codigo_cbo, \' - \', Cbo.descricao)');
    
    public function buscar_Cbo($codigo_cbo) {        
        $result = $this->find('first', array(
            'conditions' => array(
                'Cbo.codigo_cbo ' => $codigo_cbo 
            ),
            'fields' => array(
                'Cbo.codigo_cbo as codigo_cbo',
                'Cbo.descricao as descricao'
             )
        ));
        return $result[0];
    }

    function converteFiltroEmCondition($data) {
        $conditions = array();

        if (!empty($data['codigo_cbo']))
            $conditions['Cbo.codigo_cbo'] = $data['codigo_cbo'];

        if (! empty ( $data ['descricao_cbo'] ))
            $conditions ['Cbo.descricao LIKE'] = '%' . $data ['descricao_cbo'] . '%';
       
        return $conditions;
    }
}
