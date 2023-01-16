<?php

class FichaPropContatoLog extends AppModel {

    public $name = 'FichaPropContatoLog';
    public $tableSchema = 'informacoes';
    public $databaseTable = 'dbTeleconsult';
    public $useTable = 'ficha_proprietario_contato_log';
    public $primaryKey = 'codigo_ficha';
    public $actsAs = array('Secure');
    public $belongsTo = array(
        'PropContatoLog' => array(
            'className' => 'PropContatoLog',
            'foreignKey' => 'codigo_proprietario_contato_log'
        )
    );

    public function incluir($dados) {
        try {
            $this->query("INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} (codigo_ficha, codigo_proprietario_contato_log) values({$dados[$this->name]['codigo_ficha']}, {$dados[$this->name]['codigo_proprietario_contato_log']})");
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function duplicar($codigo_ficha, $novo_codigo_ficha) {
        $this->PropContatoLog =& ClassRegistry::init('PropContatoLog');
        
        $codigos = $this->find('all', array(
            'fields' => 'codigo_proprietario_contato_log', 
            'conditions' => array(
                'codigo_ficha' => $codigo_ficha
        )));

        try {
            foreach ($codigos as $codigo) {
                $codigo = $codigo[$this->name]['codigo_proprietario_contato_log'];
                $novo_codigo = $this->PropContatoLog->duplicar($codigo);
                $dados = array(
                    'FichaPropContatoLog' => array(
                        'codigo_ficha' => $novo_codigo_ficha,
                        'codigo_proprietario_contato_log' => $novo_codigo
                    )
                );
                $this->incluir($dados);
            }
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function salvarTodosFicha($proprietario_contato_logs, $codigo_ficha){
        $this->primaryKey = 'codigo_ficha'; //Para funcionar o delete por causa da chave composta da tabela
        $this->delete($codigo_ficha);
        $this->primaryKey = null;
        $logs = array();
        foreach($proprietario_contato_logs as $value){
            $logs[] = array(
                'codigo_proprietario_contato_log' => $value,
                'codigo_ficha' => $codigo_ficha
            );
        }
        return @$this->saveAll($logs, array('validate' => false));
    }    

}