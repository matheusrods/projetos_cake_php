<?php
class AcaoMelhoriaSolicitacao extends AppModel
{
    public $name = 'AcaoMelhoriaSolicitacao';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'acoes_melhorias_solicitacoes';
    public $primaryKey = 'codigo';

    public $actsAs = array('Secure');

    public $validate = array(
        'codigo_acao_melhoria_solicitacao_tipo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo_acao_melhoria_solicitacao_tipo.',
            'required' => true
        ),
        'status' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o status',
            'required' => true
        ),
        'codigo_usuario_inclusao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo_usuario_inclusao',
            'required' => true
        ),
        'data_inclusao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o data_inclusao',
            'required' => true
        ),
        'codigo_acao_melhoria' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo_acao_melhoria',
            'required' => true
        ),
        'codigo_usuario_solicitado' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo_usuario_solicitado',
            'required' => true
        )
    );

}
