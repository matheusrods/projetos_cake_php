<?php

class FichaVeiculo extends AppModel {

    var $name = 'FichaVeiculo';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbteleconsult';
    var $useTable = 'ficha_veiculo';
    //var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    public function duplicar($codigo_ficha_antiga, $codigo_ficha_nova) {
        $this->VeiculoLog =& ClassRegistry::init('VeiculoLog');
        $ficha_veiculos = $this->find('all', array(
            'conditions' => array(
                'codigo_ficha' => $codigo_ficha_antiga
            )
        ));
        
        if (count($ficha_veiculos) == 0) {
            return true;
        }

        try {
            foreach ($ficha_veiculos as $ficha_veiculo) {
                $codigo_veiculo_log = $ficha_veiculo[$this->name]['codigo_veiculo_log'];
                $novo_codigo_veiculo_log = $this->VeiculoLog->duplicar($codigo_veiculo_log);
                $ficha_veiculo[$this->name]['codigo_veiculo_log'] = $novo_codigo_veiculo_log;
                $ficha_veiculo[$this->name]['codigo_ficha'] = $codigo_ficha_nova;
                $this->incluir($ficha_veiculo);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function incluir ($dados) {
        try {
            if (is_null($dados[$this->name]['codigo_tecnologia'])) {
                $dados[$this->name]['codigo_tecnologia'] = 'null';
            }
            
            $this->query("INSERT INTO 
                        {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} 
                        (codigo_ficha, codigo_veiculo_log, tipo, codigo_tecnologia) 
                      VALUES
                        ({$dados[$this->name]['codigo_ficha']}, {$dados[$this->name]['codigo_veiculo_log']}, {$dados[$this->name]['tipo']}, {$dados[$this->name]['codigo_tecnologia']})
                    ");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function salvarTodosFicha($tipo_veiculo, $veiculo_log, $codigo_ficha){
        $this->primaryKey = 'codigo_ficha'; //Para funcionar o delete por causa da chave composta da tabela
        $this->deleteAll(array('tipo'=>$tipo_veiculo, 'codigo_ficha'=>$codigo_ficha));
        $this->primaryKey = null;
    	$data = array(
    		'codigo_veiculo_log' => $veiculo_log['VeiculoLog'],
    		'tipo' => $tipo_veiculo,
    		'codigo_ficha' => $codigo_ficha,
    	);
    	$this->create();
    	if(@$this->save($data, array('validate' => false)))
    		return $this->id;
    	return null;
    }

}