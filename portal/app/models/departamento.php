<?php
class Departamento extends AppModel {
    var $name 			= 'Departamento';
    var $tableSchema 	= 'dbo';
    var $databaseTable 	= 'RHHealth';
    var $useTable 		= 'departamento';
    var $displayField 	= 'descricao';
    var $primaryKey 	= 'codigo';
        
    const CADASTRO 		= 1;
    const COMERCIAL 	= 2;
    const FINANCEIRO 	= 3;
    const OUTROS        = 4;

}