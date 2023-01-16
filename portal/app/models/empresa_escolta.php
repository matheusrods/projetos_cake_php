<?php

class EmpresaEscolta extends AppModel {
    
    var $name = 'EmpresaEscolta';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'empresa_escoltas';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure');
}
