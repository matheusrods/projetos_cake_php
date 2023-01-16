<?php
class UsuarioResponsavel extends AppModel
{
    public $name = 'UsuarioResponsavel';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'usuarios_responsaveis';
    public $primaryKey = 'codigo';

    public $actsAs = array('Secure');

    public $validate = array(
        'codigo_usuario' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o código usuário.',
            'required' => true
        ),
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o código cliente',
            'required' => true
        )
    );

}
