<?php
class LogConsultaStatusProfSc extends AppModel {
    var $name = 'LogConsultaStatusProfSc';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'log_consulta_status_profissional_scorecard';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
}
?>