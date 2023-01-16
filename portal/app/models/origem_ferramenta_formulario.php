<?php
class OrigemFerramentaFormulario extends AppModel
{
    public $name = 'OrigemFerramentaFormulario';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'origem_ferramenta_formulario';
    public $primaryKey = 'codigo';

    public $actsAs = array('Secure');

    public $validate = array(
        'descricao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o descrição.',
            'required' => true
        ),
        'campo_tipo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o campo_tipo',
            'required' => true
        ),
        'codigo_produto' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo_produto.',
            'required' => true
        ),
    );

}
