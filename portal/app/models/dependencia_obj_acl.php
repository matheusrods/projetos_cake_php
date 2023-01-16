<?php
class DependenciaObjAcl extends AppModel {
    var $name = 'DependenciaObjAcl';
    var $useDbConfig = 'dbProducao';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
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
        /*
        'codigo_tarefa_desenvolvimento' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe uma tarefa',
        ),
        */
    );

    function listaPorObjeto($objeto_id) {
        return $this->find('all', array('conditions' => array($this->name.'.objeto_id' => $objeto_id)));
    }

    function listaDependencias($aco_string) {
        $this->bindModel(array('belongsTo' => array('ObjetoAcl')));
        $objeto_acl = $this->ObjetoAcl->findByAcoString($aco_string);
        $this->unbindModel(array('belongsTo' => array('ObjetoAcl')));
        $dependencias = $this->listaPorObjeto($objeto_acl['ObjetoAcl']['id']);
        if (count($dependencias > 0))
            $dependencias = Set::extract('/DependenciaObjAcl/aco_string', $dependencias);
        return $dependencias;
    }
}