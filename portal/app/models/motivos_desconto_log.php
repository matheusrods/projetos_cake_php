<?php
class MotivosDescontoLog extends AppModel {

    public $databaseTable = 'RHHealth';
    public $tableSchema = 'dbo';
    public $name = 'MotivosDescontoLog';
    public $useTable = 'motivos_desconto_log';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');
    public $foreignKeyLog = 'codigo_motivos_desconto';
	public $displayField = 'codigo_motivos_desconto';
    public $validate = array(
        'codigo_motivos_desconto' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	
}
