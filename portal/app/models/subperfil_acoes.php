<?php
class SubperfilAcoes extends AppModel
{
    public $name = 'SubperfilAcoes';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'subperfil_acoes';
    public $primaryKey = 'codigo';

    public $actsAs = array('Secure');

    public $validate = array();
}
