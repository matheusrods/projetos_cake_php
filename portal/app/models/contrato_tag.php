<?php
class ContratoTag extends AppModel {
    var $name = 'ContratoTag';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'contrato_tag';
    var $displayField = 'descricao';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

   public function listar($type = 'all', array $conditions=null) {
       $result = $this->find($type, array(
           'conditions' => $conditions
       ));
       return $result;
   }

   public function combo() {
        $motivos = $this->find('all');
        foreach($motivos as $motivo) {
            $lista[$motivo['ContratoTag']['codigo']] = $motivo['ContratoTag']['descricao'];
        }

        return $lista;
   }
}
