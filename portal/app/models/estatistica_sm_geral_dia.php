<?php
App::import('Model', 'EstatisticaSmGeral');
class EstatisticaSmGeralDia extends EstatisticaSmGeral {
    var $name = 'EstatisticaSmGeralDia';
    var $tableSchema = 'monitora';
    var $databaseTable = 'dbMonitora';
    var $useTable = 'estatistica_sm_geral_dia';
    var $primaryKey = 'codigo';
    var $tipo = EstatisticaSm::TIPO_DIA;
}