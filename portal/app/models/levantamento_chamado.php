<?php
class LevantamentoChamado extends AppModel
{
    public $name = 'LevantamentoChamado';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'levantamentos_chamados';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');

    public $validate = array(
        'codigo_chamado' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o chamado',
            'required' => true
        ),
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o cliente',
            'required' => true
        ),
        'codigo_levantamento_chamado_status' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o status do levantamento do chamado',
            'required' => true
        ),
    );
}
