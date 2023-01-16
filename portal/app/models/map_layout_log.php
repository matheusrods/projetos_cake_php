<?php
class MapLayoutLog extends AppModel
{
    public $name          = 'MapLayoutLog';
    public $tableSchema   = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable      = 'map_layout_log';
    public $primaryKey    = 'codigo';
    public $actsAs        = array('Secure');
    public $foreignKeyLog = 'codigo_map_layout';
}