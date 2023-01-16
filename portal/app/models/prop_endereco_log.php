<?php

class PropEnderecoLog extends AppModel {

    var $name = 'PropEnderecoLog';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'proprietario_endereco_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    function incluir($dados) {
        unset($dados[$this->name]['codigo']);
        unset($dados[$this->name]['data_inclusao']);
        $this->create();
        return $this->save($dados);
    }

    public function duplicar($codigo) {
        try {
            if (empty($codigo)) {
                throw new Exception();
            }

            $model_data = $this->find('first', array(
                'conditions' => array(
                    "{$this->name}.codigo" => $codigo
                    )));

            $result = $this->incluir($model_data);

            if ($result) {
                return $this->id;
            } else {
                throw new Exception();
            }
        } catch (Exception $e) {
            return false;
        }
    }

}