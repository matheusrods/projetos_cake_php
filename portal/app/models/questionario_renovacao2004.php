<?php

class QuestionarioRenovacao2004 extends AppModel {

    var $name = 'QuestionarioRenovacao2004';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'questionario_renovacao2004';
    var $primaryKey = 'id';      
    

    function incluir($dados) {

        if(parent::incluir($dados))               
            return true;
        else
            return false;
    }    
}