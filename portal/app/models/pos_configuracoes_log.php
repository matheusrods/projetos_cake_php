<?php
class PosConfiguracoesLog extends AppModel {

    public $databaseTable = 'RHHealth';
    public $tableSchema = 'dbo';
    public $name = 'PosConfiguracoesLog';
    public $useTable = 'pos_configuracoes_log';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');
    public $foreignKeyLog = 'codigo_pos_configuracao';
	public $displayField = 'codigo_pos_configuracao';
    public $validate = array(
        'codigo_pos_configuracao' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	
}
