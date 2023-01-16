<?php

class MedicoCalendario extends AppModel {

    var $name = 'MedicoCalendario';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'medico_calendario';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

}

?>
