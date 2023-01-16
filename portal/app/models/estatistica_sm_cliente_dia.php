<?php
App::import('Model', 'EstatisticaSm');
App::import('Model', 'EstatisticaSmCliente');
class EstatisticaSmClienteDia extends EstatisticaSmCliente {
    var $name = 'EstatisticaSmClienteDia';
    var $tableSchema = 'monitora';
    var $databaseTable = 'dbMonitora';
    var $useTable = 'estatistica_sm_cliente_dia';
    var $primaryKey = 'codigo';
    var $tipo = EstatisticaSm::TIPO_DIA;
}