<?php
App::import('Model', 'EstatisticaSm');
App::import('Model', 'EstatisticaSmOperador');
class EstatisticaSmOperadorDia extends EstatisticaSmOperador {
    var $name = 'EstatisticaSmOperadorDia';
    var $tableSchema = 'monitora';
    var $databaseTable = 'dbMonitora';
    var $useTable = 'estatistica_sm_operador_dia';
    var $primaryKey = 'codigo';
    var $tipo = EstatisticaSm::TIPO_DIA;
}