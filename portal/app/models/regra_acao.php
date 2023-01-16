<?php
class RegraAcao extends AppModel
{
    public $name = 'RegraAcao';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'regra_acao';
    public $primaryKey = 'codigo';

    public $actsAs = array('Secure');

    public $validate = array(
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o código cliente.',
            'required' => true
        ),
        'dias_encaminhar' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a quantidade de dias',
            'required' => true
        ),
        'dias_prazo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a quantidade de dias',
            'required' => true
        ),
        'status_acao_sem_prazo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a quantidade de dias',
            'required' => true
        ),
        'dias_analise_implementacao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a quantidade de dias',
            'required' => true
        ),
        'dias_analise_eficacia' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a quantidade de dias',
            'required' => true
        ),
        'dias_analise_abrangencia' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a quantidade de dias',
            'required' => true
        ),
        'dias_analise_cancelamento' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a quantidade de dias',
            'required' => true
        ),
        'dias_a_vencer' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a quantidade de dias',
            'required' => true
        ),
        'dias_a_aceitar' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a quantidade de dias',
            'required' => true
        ),
    );

}
