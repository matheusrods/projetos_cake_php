<?php

class StatusContrato extends AppModel {
    public $name = 'StatusContrato';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'status_contrato';
    public $displayField = 'descricao';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');
}