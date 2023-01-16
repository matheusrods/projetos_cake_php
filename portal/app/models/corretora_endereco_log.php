<?php
class CorretoraEnderecoLog extends AppModel {
    var $name = 'CorretoraEnderecoLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'corretora_endereco_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $belongsTo = array(
        'TipoContato' => array(
            'className' => 'TipoContato',
            'foreignKey' => 'codigo_tipo_contato'
        ),
    );
    var $validate = array(
        'codigo_corretora' => array(
            'rule' => 'notEmpty',
            'message' => 'Corretora não informada',
            'required' => true
        ),
        'numero' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o numero'
        ),
        'codigo_tipo_contato' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o tipo de contato'
        ),
        'endereco_cep' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe um CEP'
        )
    );       
}
?>