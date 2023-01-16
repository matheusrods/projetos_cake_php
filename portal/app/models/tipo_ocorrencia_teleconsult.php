<?php
class TipoOcorrenciaTeleconsult extends AppModel {

    var $name = 'TipoOcorrenciaTeleconsult';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'ocorrencia';
    var $displayField = 'descricao';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    function listaTipoOcorrencia() {
        return $this->find('list');
    }

}