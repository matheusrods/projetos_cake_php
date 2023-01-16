<?php

class HierarquiaUsuario extends AppModel {

    var $name = 'HierarquiaUsuario';
    var $tableSchema = 'dbo';
    var $databaseTable = 'Monitora';
    var $useTable = 'hierarquia_usuarios';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    function filhos($codigo_usuario, $data_verificacao) {
        $filhos = array($codigo_usuario);

        $condicoes = array('convert(int,codigo_usuario_pai)' => $codigo_usuario,
                           'validade >= ' => $data_verificacao);

        $resultado = $this->find('list', array('fields' => 'codigo_usuario_filho',
                                               'conditions' => $condicoes));

        return array_merge($filhos, $resultado);
    }

}

?>