<?php
class ClienteSetorVersoes extends AppModel {

    var $name = 'ClienteSetorVersoes';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'clientes_setores_versoes';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

}//FINAL CLASS ClienteSetorVersoes