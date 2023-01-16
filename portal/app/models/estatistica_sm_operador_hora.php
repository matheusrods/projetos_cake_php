<?php
App::import('Model', 'EstatisticaSm');
App::import('Model', 'EstatisticaSmOperador');
class EstatisticaSmOperadorHora extends EstatisticaSmOperador {
    var $name = 'EstatisticaSmOperadorHora';
    var $tableSchema = 'monitora';
    var $databaseTable = 'dbMonitora';
    var $useTable = 'estatistica_sm_operador_hora';
    var $primaryKey = 'codigo';
    var $tipo = EstatisticaSm::TIPO_HORA;
}