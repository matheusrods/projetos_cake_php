<?php

class ContratoModelo extends AppModel {

    var $name = 'ContratoModelo';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'contrato_modelo';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_contrato_modelo'));

    public function listar($type = 'all', array $conditions = null) {
        $result = $this->find($type, array(
            'conditions' => $conditions
                ));
        return $result;
    }

    public function mostrarcontrato($codigo) {
        $result = $this->find('first', array('fields' => array('ContratoModelo.modelo'),
            'conditions' => array('ContratoModelo.codigo' => $codigo)));

        return $result;
    }
}
