<?php
class UsuarioSubperfil extends AppModel
{
    public $name = 'UsuarioSubperfil';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'usuario_subperfil';
    public $primaryKey = 'codigo';

    public $actsAs = array('Secure');

    public $validate = array(
        'codigo_subperfil' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo_subperfil.',
            'required' => true
        ),
        'codigo_usuario' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo_usuario',
            'required' => true
        )
    );

}
