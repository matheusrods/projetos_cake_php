<?php
class PosCategoriasLog extends AppModel {

    public $databaseTable = 'RHHealth';
    public $tableSchema = 'dbo';
    public $name = 'PosCategoriasLog';
    public $useTable = 'pos_categorias_log';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');
    public $foreignKeyLog = 'codigo_pos_categoria';
	public $displayField = 'codigo_pos_categoria';
    public $validate = array(
        'codigo_pos_categoria' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	
}
