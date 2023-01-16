<?php

class ProfissionalTelecheque extends AppModel {

    var $name = 'ProfissionalTelecheque';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbteleconsult';
    var $useTable = 'profissional_telecheque';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

}

?>
