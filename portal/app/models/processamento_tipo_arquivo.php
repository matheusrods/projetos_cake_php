<?php
class ProcessamentoTipoArquivo extends AppModel {

    public $name            = 'ProcessamentoTipoArquivo';
    public $tableSchema     = 'dbo';
    public $databaseTable   = 'RHHealth';
    public $useTable        = 'processamentos_tipos_arquivos';
    public $primaryKey      = 'codigo';
    public $recursive       = -1;

    public $hasMany = array(
        'Processamento' => array(
            'className'    => 'Processamento',
            'foreignKey'    => 'codigo_processamento_tipo_arquivo'
        )
    );

}