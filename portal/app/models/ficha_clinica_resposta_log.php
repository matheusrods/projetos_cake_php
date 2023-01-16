<?php

class FichaClinicaRespostaLog extends AppModel {

    public $name = 'FichaClinicaRespostaLog';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
   	public $useTable = 'fichas_clinicas_respostas_log';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');
    
}