<?php
App::import('Model', 'EstatisticaSm');
App::import('Model', 'EstatisticaSmCliente');
class EstatisticaSmClienteHora extends EstatisticaSmCliente {
    var $name = 'EstatisticaSmClienteHora';
    var $tableSchema = 'monitora';
    var $databaseTable = 'dbMonitora';
    var $useTable = 'estatistica_sm_cliente_hora';
    var $primaryKey = 'codigo';
    var $tipo = EstatisticaSm::TIPO_HORA;
}