<?php
class HorarioDiferenciado extends AppModel {
    var $name = 'HorarioDiferenciado';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHhealth';
    var $useTable = 'horario_diferenciado';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');   
}

?>