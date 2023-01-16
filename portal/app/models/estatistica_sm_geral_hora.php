<?php
App::import('Model', 'EstatisticaSm');
App::import('Model', 'EstatisticaSmGeral');
class EstatisticaSmGeralHora extends EstatisticaSmGeral {
    var $name = 'EstatisticaSmGeralHora';
    var $tableSchema = 'monitora';
    var $databaseTable = 'dbMonitora';
    var $useTable = 'estatistica_sm_geral_hora';
    var $primaryKey = 'codigo';
    var $tipo = EstatisticaSm::TIPO_HORA;
}