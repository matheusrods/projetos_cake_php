<?php
class PassoAtendimento extends AppModel {
    var $name = 'PassoAtendimento';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'passos_atendimentos';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure');
}