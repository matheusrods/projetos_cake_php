<?php
class OrigemFerramentaSelecionado extends AppModel
{
    public $name = 'OrigemFerramentaSelecionado';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'origem_ferramenta_selecionado';
    public $primaryKey = 'codigo';

    public $actsAs = array('Secure');

    public $validate = array(
        'codigo_origem_ferramenta' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo_origem_ferramenta.',
            'required' => true
        ),
        'codigo_origem_ferramenta_formulario' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo_origem_ferramenta_formulario',
            'required' => true
        ),
        'codigo_produto' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo_produto.',
            'required' => true
        ),
    );

}
