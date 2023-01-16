<?php

class FichaLiberacaoItem extends AppModel {

    var $name = 'FichaLiberacaoItem';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'ficha_liberacao_item';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    function inserir($data) {
        unset($data[$this->name]['codigo']);
        $this->create();
        return $this->save($data);
    }

    public function duplicar($codigo_antigo, $codigo_novo) {
        $items_antigos = $this->find('all', array(
            'conditions' => array(
                'codigo_ficha_liberacao' => $codigo_antigo
        )));

        foreach ($items_antigos as $item_antigo) {
            $item_antigo['codigo_ficha_liberacao'] = $codigo_novo;
            $this->inserir($item_antigo);
        }
    }
}

?>