<?php

class QuestionarioLog extends AppModel {

    var $databaseTable = 'RHHealth';
    var $tableSchema = 'dbo';
    var $name = 'QuestionarioLog';
    var $useTable = 'questionarios_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

}

?>