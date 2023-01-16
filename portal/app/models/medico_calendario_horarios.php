<?php

class MedicoCalendarioHorarios extends AppModel {

    var $name = 'MedicoCalendarioHorarios';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'medico_calendario_horarios';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

}

?>
