<?php

class QuestionarioAbuonnyPergunta extends AppModel {

    var $name = 'QuestionarioAbuonnyPergunta';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'questionario_abuonny_pergunta';
    var $primaryKey = 'id';      
    

    function incluir($dados) {

        if(parent::incluir($dados))               
            return true;
        else
            return false;
    }    
}