<?php

class TipoProblema extends AppModel {

    var $name = 'TipoProblema';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'tipo_problemas';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure');
    
}

?>