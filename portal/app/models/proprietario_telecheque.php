<?php

class ProprietarioTelecheque extends AppModel {

    var $name = 'ProprietarioTelecheque';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbteleconsult';
    var $useTable = 'proprietario_telecheque';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

}

?>
