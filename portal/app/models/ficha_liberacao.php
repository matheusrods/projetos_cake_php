<?php

class FichaLiberacao extends AppModel {

    public $name = 'FichaLiberacao';
    public $tableSchema = 'informacoes';
    public $databaseTable = 'dbTeleconsult';
    public $useTable = 'ficha_liberacao';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');
    
    public function duplicar($codigo_ficha_antiga, $parametros = null) {
        try {
            $this->FichaLiberacaoItem =& ClassRegistry::init('FichaLiberacaoItem');

            if (empty($codigo_ficha_antiga)) {
                throw new Exception();
            }
            
            $dados_liberacao_antigo = $this->find('first', array(
                'conditions' => array(
                    'codigo_ficha' => $codigo_ficha_antiga
            )));

            if (!$dados_liberacao_antigo) {
                return true;
            }
            
            $codigo_ficha_liberacao = $dados_liberacao_antigo[$this->name]['codigo'];
            
            $dados_liberacao_antigo[$this->name] = array_merge($dados_liberacao_antigo[$this->name], (array) $parametros);

            $result_liberacao = $this->incluir($dados_liberacao_antigo);

            $novo_codigo_liberacao = $this->id;
            $this->FichaLiberacaoItem->duplicar($codigo_ficha_liberacao, $novo_codigo_liberacao);
            
            if ($result_liberacao) {
                return $this->id;
            } else {
                throw new Exception();
            }
        } catch (Exception $e) {
            return false;
        }
    }

}