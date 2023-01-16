<?php
class TipoEvento extends AppModel {
    var $name = 'TipoEvento';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'tipos_eventos';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure');
    
    function listarEventos() {
        return $this->find('list');
    }
}