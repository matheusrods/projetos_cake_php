<?php

class UperfilLog extends AppModel
{
    public $name = 'UperfilLog';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'uperfis_log';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');
}
