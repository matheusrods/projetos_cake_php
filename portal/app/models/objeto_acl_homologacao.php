<?php
class ObjetoAclHomologacao extends AppModel {
    var $name = 'ObjetoAclHomologacao';
    var $tableSchema = 'dbo';
    var $useDbConfig = 'dbHomolog';
    var $databaseTable = 'RHHealth';
    var $useTable = 'objetos_acl';
    var $displayField = 'descricao';
    var $actsAs = array('Secure');
    var $validate = array(
        'descricao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a descrição do objeto',
        ),
    );

}