<?php
class CondPag extends AppModel {
    var $name = 'CondPag';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbNavegarqNatec';
    var $useTable = 'condpag';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $displayField = 'descricao';

    function listar_condicoes($qtde_pgto) {
    	return ($this->find('list',array('conditions' => array('qtdepagto' => $qtde_pgto), 'order' => 'descricao ASC')));
    }

}

?>