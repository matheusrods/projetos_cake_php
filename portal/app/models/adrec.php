<?php
class Adrec extends AppModel {
    var $name = 'Adrec';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbNavegarqNatec';
    var $useTable = 'adrec';
    var $primaryKey = null;
    var $actsAs = array('Secure');
}
?>