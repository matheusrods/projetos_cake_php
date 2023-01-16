<?php
class TipoUso extends AppModel {
    public $name = 'TipoUso';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'tipo_uso';
    public $primaryKey = 'codigo';
    public $displayField = 'descricao';

}