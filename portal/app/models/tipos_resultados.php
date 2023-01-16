<?php
class TiposResultados extends AppModel
{
    public $name = 'TiposResultados';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'tipos_resultados';
    public $primaryKey = 'codigo';
    // public $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_chamados'));
    public $actsAs = array('Secure');

    public $validate = array();

}
