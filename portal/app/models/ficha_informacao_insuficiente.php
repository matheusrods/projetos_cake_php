<?php

    class FichaInformacaoInsuficiente extends AppModel {
     
        var $name = 'Corporacao';
        var $tableSchema = 'informacoes';
        var $databaseTable = 'dbTeleconsult';
        var $useTable = 'ficha_informacao_insuficiente';        
        var $primaryKey = 'codigo';
        var $actsAs = array('Secure');

    }

?>