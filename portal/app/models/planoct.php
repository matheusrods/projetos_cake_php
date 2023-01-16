<?php
class Planoct extends AppModel {
    var $name = 'Planoct';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbNavegarqNatec';
    var $useTable = 'planoct';
    var $primaryKey = null;
    var $actsAs = array('Secure');
}
?>