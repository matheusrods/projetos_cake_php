<?php

class ContratoModeloLog extends AppModel {

    var $name = 'ContratoModeloLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'contrato_modelo_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    public function listar($type = 'all', array $conditions = null) {
        $result = $this->find($type, array(
            'conditions' => $conditions
                ));
        return $result;
    }

    public function buscaUltimaOcorrenciaModelo($codigo) {
        $result = $this->find('first', array('conditions' => array('ContratoModeloLog.codigo_contrato_modelo' => $codigo),
            'fields' => 'max(ContratoModeloLog.codigo) as codigo'
                ));

        $result = $result[0]['codigo'];

        return $result;
    }

}
