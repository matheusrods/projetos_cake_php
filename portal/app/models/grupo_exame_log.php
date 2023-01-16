<?php
class GrupoExameLog extends AppModel {
	var $name = 'GrupoExameLog';
    var $databaseTable = 'RHHealth';
    var $tableSchema = 'dbo';
    var $useTable = 'grupos_exames_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure', 'Containable');

}
?>