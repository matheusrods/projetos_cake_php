<?php
class VtigerUser extends AppModel {
    var $name = 'VtigerUser';
    var $useDbConfig = 'crm';
    var $databaseTable = 'crm521';
    var $useTable = 'vtiger_users';
    var $primaryKey = 'id';
    
    function retornarUsuarioInclusao($codigo_documento) {
        $VtigerCrmentity = classRegistry::init('VtigerCrmentity');
        $VtigerAccountscf = classRegistry::init('VtigerAccountscf');
        
        $conditions = array(
            'VtigerAccountscf.cf_905' => $codigo_documento
        );
        
        $joins = array (
            array(
               'table' => "{$VtigerCrmentity->databaseTable}.{$VtigerCrmentity->useTable}",
                'alias' => 'VtigerCrmentity',
                'conditions' => 'VtigerCrmentity.smownerid = VtigerUser.id',
                'type' => 'left',
            ),
            array(
                'table' => "{$VtigerAccountscf->databaseTable}.{$VtigerAccountscf->useTable}",
                'alias' => 'VtigerAccountscf',
                'conditions' => 'VtigerCrmentity.crmid = VtigerAccountscf.accountid',
                'type' => 'left',
            ),
        );
        
        $usuario = $this->find( 'first', array(
                'fields' => array(
                    'VtigerUser.user_name',
                ),
                'conditions' => $conditions,
                'joins' => $joins
            )
        );
        
        return $usuario;
    }
}
