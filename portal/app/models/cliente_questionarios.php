<?php 
class ClienteQuestionarios extends AppModel {

    public $name = 'ClienteQuestionarios';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'cliente_questionarios';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_cliente_questionario'));
    public $recursive = -1;

}