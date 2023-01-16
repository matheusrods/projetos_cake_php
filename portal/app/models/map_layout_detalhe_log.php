<?php
class MapLayoutDetalheLog extends AppModel
{
    public $name          = 'MapLayoutDetalheLog';
    public $tableSchema   = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable      = 'map_layout_detalhe_log';
    public $primaryKey    = 'codigo';
    public $actsAs        = array('Secure');
    public $foreignKeyLog = 'codigo_map_layout_detalhe';
}