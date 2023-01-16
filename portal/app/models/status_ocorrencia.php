<?php
class StatusOcorrencia extends AppModel {
    
    var $name = 'StatusOcorrencia';
    var $tableSchema = 'monitora';
    var $databaseTable = 'dbMonitora';
    var $useTable = 'status_ocorrencia';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $displayField = 'descricao';
    const STATUS_INICIAL = 3;
}
