<?php

class EnderecoBairro extends AppModel {

    var $name = 'EnderecoBairro';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHhealth';
    var $useTable = 'endereco_bairro';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure');

   public function combo($codigo_endereco_cidade) {
        $bairros = $this->find('list', array(//'order' => 'EnderecoBairro.descricao',
            'conditions' => array('EnderecoBairro.codigo_endereco_cidade' => $codigo_endereco_cidade)));

        return $bairros;
   }

}

?>