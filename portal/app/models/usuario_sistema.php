<?php
class UsuarioSistema extends AppModel {
    var $name = 'UsuarioSistema';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'usuario_sistema';
    var $primaryKey = 'codigo';    
    var $actsAs = array('Secure');

}
?>