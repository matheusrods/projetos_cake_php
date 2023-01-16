<?php
class Gfip extends AppModel {

    var $name = 'Gfip';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'gfip';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    public $virtualFields = array('descricao_gfip' => 'CONCAT(Gfip.codigo, \' - \', Gfip.descricao)');
}
