<?php
class FichaScPropContatoLog extends AppModel {
    var $name = 'FichaScPropContatoLog';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'ficha_scorecard_proprietario_contato_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

}