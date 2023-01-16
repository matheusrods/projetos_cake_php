<?php
class EnderecoTipo extends AppModel {

    var $name = 'EnderecoTipo';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'endereco_tipo';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure');

   public function combo() {
        $estados = $this->find('list', array('order' => 'EnderecoTipo.descricao'));

        return $estados;
   }

}

?>