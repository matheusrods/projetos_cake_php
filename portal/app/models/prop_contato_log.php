<?php
class PropContatoLog extends AppModel {
    var $name = 'PropContatoLog';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'proprietario_contato_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    
    /**
     * Inclui um novo PropContatoLog
     * 
     * @param int $dados
     * @return mixed
     */
    function incluir($dados){ 
        if (!isset($dados[$this->name])) {
            return false;
        }
        unset($dados[$this->name]['codigo']);
        unset($dados[$this->name]['data_inclusao']);
        $this->create();
        return $this->save($dados);
    }
    
    /**
     * Duplica um PropContatoLog
     * 
     * @param int $codigo
     * 
     * @return int|boolean
     */
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
