<?php
class ProcessamentoStatus extends AppModel {

    public $name            = 'ProcessamentoStatus';
    public $tableSchema     = 'dbo';
    public $databaseTable   = 'RHHealth';
    public $useTable        = 'processamentos_status';
    public $primaryKey      = 'codigo';
    public $recursive       = -1;

    public $hasMany = array(
        'Processamento' => array(
            'className'    => 'Processamento',
            'foreignKey'    => 'codigo_processamento_status'
        )
    );

}