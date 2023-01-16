<?php

class TipoServicosNfsLog extends AppModel {

    public $databaseTable = 'RHHealth';
    public $tableSchema = 'dbo';
    public $name = 'TipoServicosNfsLog';
    public $useTable = 'tipo_servicos_nfs_log';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');
    public $foreignKeyLog = 'codigo_tipo_servicos_nfs';
	public $displayField = 'codigo_tipo_servicos_nfs';
    public $validate = array(
        'codigo_tipo_servicos_nfs' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

}