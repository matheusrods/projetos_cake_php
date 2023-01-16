<?php
App::import('Model', 'EstatisticaSm');
App::import('Model', 'EstatisticaSmOperacao');
class EstatisticaSmOperacaoDia extends EstatisticaSmOperacao {
    var $name = 'EstatisticaSmOperacaoDia';
    var $tableSchema = 'monitora';
    var $databaseTable = 'dbMonitora';
    var $useTable = 'estatistica_sm_operacao_dia';
    var $primaryKey = 'codigo';
    var $tipo = EstatisticaSm::TIPO_DIA;
}