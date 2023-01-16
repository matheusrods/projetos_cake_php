<?php

class Gernfe extends AppModel {
    var $name = 'Gernfe';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbNavegarqNatec';
    var $useTable = 'gernfe';
    var $primaryKey = 'numero';
    var $actsAs = array('Secure');
    
    function notasDoPeriodo($periodo, $select_desconsiderar = null) {
        $fields = array('Gernfe.numnfe', 'Gernfe.protocolo');
        $codigo_empresa = '17,18,19';
        $conditions = array('Gernfe.empresa IN (' . $codigo_empresa . ')', 'Gernfe.dtemissnfe between ? and ?' => $periodo);
        
        if ($select_desconsiderar != null) {
            $conditions = array_merge($conditions, array('Gernfe.numnfe not in ('.$select_desconsiderar.')'));
        }
        return $this->find('all', array('fields' => $fields, 'group' => $fields, 'conditions' => $conditions));
    }
}
?>