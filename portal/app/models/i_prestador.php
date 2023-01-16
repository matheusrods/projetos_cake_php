<?php
class IPrestador extends AppModel {
    var $name = 'IPrestador';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'prestador';
    var $primaryKey = 'codigo';
    var $displayField = 'nome';
    var $actsAs = array('Secure');
}
?>