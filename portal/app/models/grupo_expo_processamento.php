<?php
class GrupoExpoProcessamento extends AppModel {
    public $name            = 'GrupoExpoProcessamento';
    public $tableSchema     = 'dbo';
    public $databaseTable   = 'RHHealth';
    public $useTable        = 'grupo_expo_processamento';
    public $primaryKey      = 'codigo';
    public $actsAs          = array('Secure', 'Containable');
    public $recursive       = -1;

}