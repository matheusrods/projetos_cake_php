<?php
class AtestadoProcessamento extends AppModel {
    public $name            = 'AtestadoProcessamento';
    public $tableSchema     = 'dbo';
    public $databaseTable   = 'RHHealth';
    public $useTable        = 'atestados_processamento';
    public $primaryKey      = 'codigo';
    public $actsAs          = array('Secure', 'Containable');
    public $recursive       = -1;

}