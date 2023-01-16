<?php
class GrupoEconomicoLog extends AppModel {
    var $name = 'GrupoEconomicoLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'grupos_economicos_log';
    var $primaryKey = 'codigo';
    public $foreignKeyLog = 'codigo_grupos_economicos';
    var $displayField = 'descricao';
    var $actsAs = array('Secure', 'Containable');
    var $validate = array(
        'codigo_grupos_economicos' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );


}