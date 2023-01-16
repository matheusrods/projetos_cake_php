<?php
class ExameGrupoEconomico extends AppModel {
    var $name = 'ExameGrupoEconomico';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'exames_grupos_economicos';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure', 'Containable');

    var $validate = array(
        'codigo_grupo_economico' => array(
            'rule' => array('notEmpty'),
            'message' => 'Informe o grupo economico',
            'required' => true,
            'allowEmpty' => false,
        ),
        'codigo_exame' => array(
            'rule' => array('notEmpty'),
            'message' => 'Informe o exame',
            'required' => true,
            'allowEmpty' => false,
        ),
        'codigo_medico' => array(
            'rule' => array('notEmpty'),
            'message' => 'Informe o médico',
            'required' => true,
            'allowEmpty' => false,
        ),
    );
}

?>
