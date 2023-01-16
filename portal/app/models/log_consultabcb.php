<?php
class LogConsultaBCB extends AppModel {

    var $name = 'LogConsultaBCB';
    var $tableSchema = 'bcb';
    var $databaseTable = 'dbbcb';
    var $useTable = 'log_consulta';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

}

?>