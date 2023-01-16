<?php
class UsuarioLog extends AppModel {
    var $name = 'UsuarioLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'usuario_log';
    var $primaryKey = 'codigo';    
    var $actsAs = array('Secure');

    function converteFiltroEmCondition( $data ) {
        $conditions = array();
        if (!empty($data['nome']))
            $conditions['UsuarioLog.nome like'] = '%' . $data['nome'] . '%';
        if (!empty($data['apelido']))
            $conditions['UsuarioLog.apelido like'] = '%' . $data['apelido'] . '%';
        if (!empty($data['codigo_cliente']))
            $conditions['UsuarioLog.codigo_cliente'] = $data['codigo_cliente'];
        if (!empty($data['codigo']))
            $conditions['UsuarioLog.codigo'] = $data['codigo'];
        if (!empty($data['codigo_usuario']))
            $conditions['UsuarioLog.codigo_usuario'] = $data['codigo_usuario'];
        return $conditions;
    }


}
?>