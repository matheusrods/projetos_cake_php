<?php
class Negativacao extends AppModel {
    var $name = 'Negativacao';
    var $tableSchema = 'dicem';
    var $databaseTable = 'dbDicem';
    var $useTable = 'negativacao';
    var $primaryKey = 'codigo';
    
    function bindCliente() {
        $this->bindModel(array(
            'belongsTo' => array(
                'Cliente' => array(
                    'class' => 'Cliente',
                    'foreignKey' => 'codigo_cliente'
                )
            )
        ));
    }
    
    function unbindCliente() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'Cliente'
            )
        ));
    }
    
    function registrosParaInclusao() {
        $this->bindCliente();
        $registros = $this->find('all', array('conditions' => array('codigo_operacao' => 'I', 'negativado' => 0)));
        $this->unbindCliente();
        return $registros;
    }
    
    function registrosParaExclusao() {
        $this->bindCliente();
        $registros = $this->find('all', array('conditions' => array('codigo_operacao' => 'E', 'negativado' => 1)));
        $this->unbindCliente();
        return $registros;
    }
    
    function negativar($dados, $codigo_operacao) {
        try {
            $this->query('begin transaction');
            foreach ($dados as &$dado) {
                $dado[$this->name]['negativado'] = 1;
                if ($codigo_operacao == 'E')
                    $dado[$this->name]['codigo_operacao'] = 'P';
                
                if (!parent::atualizar($dado)) {
                    throw new Exception();
                }
            }
            $this->commit();
            return true;
        } catch (Exception $ex) {
            $this->rollback();
            return false;
        }
    }
    
    function trataDataVencimento($vencimento){
        return AppModel::dateToDbDate($vencimento);
    }
    
}