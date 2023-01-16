<?php
class ProcessamentoCat extends AppModel {
    public $name            = 'ProcessamentoCat';
    public $tableSchema     = 'dbo';
    public $databaseTable   = 'RHHealth';
    public $useTable        = 'processamentos_cat';
    public $primaryKey      = 'codigo';
    public $actsAs          = array('Secure', 'Containable');
    public $recursive       = -1;

}