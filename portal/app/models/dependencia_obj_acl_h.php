<?php
class DependenciaObjAclH extends AppModel {
    var $name = 'DependenciaObjAclH';
    var $tableSchema = 'portal';
    var $databaseTable = 'dbBuonny';
    var $useDbConfig = 'dbHomolog';
    var $useTable = 'dependencias_obj_acl';
    var $actsAs = array('Secure');
    var $validate = array(
        'objeto_id' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o objeto',
            'required' => true,
        ),
        'aco_string' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a aco string',
            'required' => true,
        ),
        'codigo_tarefa_desenvolvimento' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe uma tarefa',
        ),
    );

    public function bindObjetoAclHomologacao($type = 'LEFT'){
        $this->bindModel(array(
            'belongsTo' => array(
                'ObjetoAclHomologacao' => array(
                    'foreignKey' => 'objeto_id',
                    'type'      => $type
                )
            )
        ));
    }

}