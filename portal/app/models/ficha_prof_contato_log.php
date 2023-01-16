<?php

class FichaProfContatoLog extends AppModel {

    public $name = 'FichaProfContatoLog';
    public $tableSchema = 'informacoes';
    public $databaseTable = 'dbTeleconsult';
    public $useTable = 'ficha_profissional_contato_log';
    public $primaryKey = 'codigo_ficha';
    public $actsAs = array('Secure');
    public $belongsTo = array(
        'ProfContatoLog' => array(
            'className' => 'ProfContatoLog',
            'foreignKey' => 'codigo_profissional_contato_log'
        )
    );

    public function incluir($dados) {
        try {
            $this->query("
                    INSERT INTO 
                        {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} 
                        (codigo_ficha, codigo_profissional_contato_log) 
                    VALUES (
                            {$dados[$this->name]['codigo_ficha']}, 
                            {$dados[$this->name]['codigo_profissional_contato_log']}
                    )
             ");
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function duplicar($codigo_ficha, $novo_codigo_ficha) {
        $this->ProfContatoLog =& ClassRegistry::init('ProfContatoLog');
        
        $codigos = $this->find('all', array('fields' => 'codigo_profissional_contato_log', 'conditions' => array('codigo_ficha' => $codigo_ficha)));
        try {
            foreach ($codigos as $codigo) {
                $codigo = $codigo[$this->name]['codigo_profissional_contato_log'];
                $novo_codigo = $this->ProfContatoLog->duplicar($codigo);
                $dados = array(
                    'FichaProfContatoLog' => array(
                        'codigo_ficha' => $novo_codigo_ficha,
                        'codigo_profissional_contato_log' => $novo_codigo
                    )
                );
                $this->incluir($dados);
            }
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
    
    public function salvarTodosFicha($profissional_contato_logs, $codigo_ficha){
    	$this->primaryKey = 'codigo_ficha'; //Para funcionar o delete por causa da chave composta da tabela
    	$this->delete($codigo_ficha);
    	$this->primaryKey = null;
    	$logs = array();
    	foreach($profissional_contato_logs as $value){
    		$logs[] = array(
    			'codigo_profissional_contato_log' => $value,
    			'codigo_ficha' => $codigo_ficha
    		);
    	}
    	return @$this->saveAll($logs, array('validate' => false));
    }

}



