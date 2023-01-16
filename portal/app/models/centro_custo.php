<?php
class CentroCusto extends AppModel {
    var $name = 'CentroCusto';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbNavegarqNatec';
    var $useTable = 'ccusto';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
}
?>