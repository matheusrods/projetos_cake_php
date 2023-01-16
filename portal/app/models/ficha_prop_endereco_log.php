<?php

class FichaPropEnderecoLog extends AppModel {

    public $name = 'FichaPropEnderecoLog';
    public $tableSchema = 'informacoes';
    public $databaseTable = 'dbTeleconsult';
    public $useTable = 'ficha_proprietario_endereco_log';
    public $primaryKey = 'codigo_ficha';
    public $actsAs = array('Secure');
    public $belongsTo = array(
        'PropEnderecoLog' => array(
            'className' => 'PropEnderecoLog',
            'foreignKey' => 'codigo_proprietario_endereco_log'
        )
    );

    public function incluir($dados) {
        try {
            $this->query("INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} (codigo_ficha, codigo_proprietario_endereco_log) values({$dados[$this->name]['codigo_ficha']}, {$dados[$this->name]['codigo_proprietario_endereco_log']})");
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function duplicar($codigo_ficha, $novo_codigo_ficha) {
        $this->PropEnderecoLog =& ClassRegistry::init('PropEnderecoLog');
        
        $codigos = $this->find('all', array('fields' => 'codigo_proprietario_endereco_log', 'conditions' => array('codigo_ficha' => $codigo_ficha)));
        try {
            foreach ($codigos as $codigo) {
                $codigo = $codigo[$this->name]['codigo_proprietario_endereco_log'];
                $novo_codigo = $this->PropEnderecoLog->duplicar($codigo);
                $dados = array(
                    'FichaPropEnderecoLog' => array(
                        'codigo_ficha' => $novo_codigo_ficha,
                        'codigo_proprietario_endereco_log' => $novo_codigo
                    )
                );
                $this->incluir($dados);
            }
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function salvarDaFicha($proprietario_endereco_log, $codigo_ficha){
        $this->delete($codigo_ficha);
        $data = array(
            'codigo_proprietario_endereco_log' => $proprietario_endereco_log,
            'codigo_ficha' => $codigo_ficha
        );
        return $this->save($data, array('validate' => false));
    }    

}