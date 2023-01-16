<?php
class UsuarioMultiEmpresa extends AppModel {

    var $name = 'UsuarioMultiEmpresa';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'usuario_multi_empresa';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    
}

?>