<?php
App::import('Model', 'EstatisticaSm');
App::import('Model', 'EstatisticaSmOperacao');
class EstatisticaSmOperacaoHora extends EstatisticaSmOperacao {
    var $name = 'EstatisticaSmOperacaoHora';
    var $tableSchema = 'monitora';
    var $databaseTable = 'dbMonitora';
    var $useTable = 'estatistica_sm_operacao_hora';
    var $primaryKey = 'codigo';
    var $tipo = EstatisticaSm::TIPO_HORA;
}
