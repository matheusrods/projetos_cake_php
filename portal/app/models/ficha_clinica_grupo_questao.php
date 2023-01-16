<?php

class FichaClinicaGrupoQuestao extends AppModel {

    public $name = 'FichaClinicaGrupoQuestao';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'fichas_clinicas_grupo_questoes';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure', 'Containable');   

    public $hasMany = array(
        'FichaClinicaQuestao' => array(
            'className' => 'FichaClinicaQuestao',
            'foreignKey' => 'codigo_ficha_clinica_grupo_questao',
            )
        );

}