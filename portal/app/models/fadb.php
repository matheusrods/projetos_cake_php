<?php

class Fadb extends AppModel {

    public $name = 'Fadb';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'fornecedores_agendas_datas_bloqueadas';
    public $primaryKey = 'codigo';          
    public $actsAs = array('Secure');

}