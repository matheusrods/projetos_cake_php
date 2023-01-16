<?php
class Remessa extends AppModel {
    var $name = 'Remessa';
    var $tableSchema = 'dicem';
    var $databaseTable = 'dbDicem';
    var $useTable = 'remessa';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    function nova_remessa(){
        $data_inclusao = array($this->name => array('data_inclusao' => date('Ymd', time())));
        $this->create();
        $this->save($data_inclusao);
    }
    
    function ultima_remessa() {
        $remessa = $this->find('first', array('fields' => array('codigo'), 'order' => array('codigo' => 'DESC')));
        return $remessa['Remessa']['codigo'];
    }
}