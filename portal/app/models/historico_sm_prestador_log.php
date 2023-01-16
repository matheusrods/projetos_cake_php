<?php

class HistoricoSmPrestadorLog extends AppModel {

    var $name = 'HistoricoSmPrestadorLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'historico_sm_prestador_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    function bindUsuarioInclusao() {
        $this->bindModel(array(
            'belongsTo' => array(
                'Usuario' => array(
                    'class' => 'HistoricoSmPrestador',
                    'foreignKey' => 'codigo_historico_sm'
                )
            )
        ));
    }

    function unbindUsuarioInclusao() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'HistoricoSmPrestador'
            )
        ));
    }
}