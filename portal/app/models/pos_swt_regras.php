<?php
class PosSwtRegras extends AppModel
{
    public $name = 'PosSwtRegras';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'pos_swt_regras';
    public $primaryKey = 'codigo';

    public $actsAs = array('Secure');

    public $validate = array(
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o código cliente.',
            'required' => true
        ),
        'dias_registro_retroativo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a quantidade de dias',
            'required' => true
        ),

    );

}
