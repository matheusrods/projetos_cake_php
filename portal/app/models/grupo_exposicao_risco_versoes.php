<?php
class GrupoExposicaoRiscoVersoes extends AppModel {

    var $name = 'GrupoExposicaoRiscoVersoes';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'grupo_exposicao_risco_versoes';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

}//FINAL CLASS GrupoExposicaoRiscoVersoes