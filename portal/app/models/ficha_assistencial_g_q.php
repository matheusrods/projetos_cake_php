<?php
class FichaAssistencialGQ extends AppModel {

    public $name = 'FichaAssistencialGQ';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'fichas_assistenciais_grupo_questoes';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure', 'Containable');   

    public $hasMany = array(
        'FichaAssistencialQuestao' => array(
            'className' => 'FichaAssistencialQuestao',
            'foreignKey' => 'codigo_ficha_assistencial_grupo_questao',
            )
        );

}//FINAL CLASS FichaAssistencialGrupoQuestao