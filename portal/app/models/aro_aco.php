<?php
class AroAco extends AppModel {
    var $name = 'AroAco';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'aros_acos';
    var $actsAs = array('Secure', 'Containable','Loggable' => array('foreign_key' => 'aros_acos_id'));

    function clearByAro($aro_id) {
    	return $this->deleteAll(array('aro_id' => $aro_id));
    }
}
?>