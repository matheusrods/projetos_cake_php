<?php
class OrdemServicoVersoes extends AppModel {

    var $name = 'OrdemServicoVersoes';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'ordem_servico_versoes';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

}//FINAL CLASS OrdemServicoVersoes