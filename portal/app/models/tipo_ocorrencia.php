<?php
class TipoOcorrencia extends AppModel {

    var $name = 'TipoOcorrencia';
    var $tableSchema = 'monitora';
    var $databaseTable = 'dbMonitora';
    var $useTable = 'tipos_ocorrencia';
    var $displayField = 'descricao';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    function listaTipoOcorrencia() {
        return $this->find('list');
    }

}