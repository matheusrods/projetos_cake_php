<?php
class PosicaoUltima extends AppModel {

    var $name = 'PosicaoUltima';
    var $tableSchema = 'monitoramento';
    var $databaseTable = 'dbBuonnysat';
    var $useTable = 'posicao_ultima';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

}
?>
