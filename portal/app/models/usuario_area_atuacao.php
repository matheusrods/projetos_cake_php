<?php
class UsuarioAreaAtuacao extends AppModel
{
    public $name = 'UsuarioAreaAtuacao';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'usuario_area_atuacao';
    public $primaryKey = 'codigo';

    public $actsAs = array('Secure');

    public $validate = array(
        'codigo_area_atuacao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo_area_atuacao.',
            'required' => true
        ),
        'codigo_usuario' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo_usuario',
            'required' => true
        )
    );
}
