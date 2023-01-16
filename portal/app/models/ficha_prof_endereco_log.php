<?php
class FichaProfEnderecoLog extends AppModel {
    public $name = 'FichaProfEnderecoLog';
    public $tableSchema = 'informacoes';
    public $databaseTable = 'dbTeleconsult';
    public $useTable = 'ficha_profissional_endereco_log';
    public $primaryKey = 'codigo_ficha';
    public $actsAs = array('Secure');
    public $belongsTo = array(
        'ProfEnderecoLog' => array(
            'className' => 'ProfEnderecoLog',
            'foreignKey' => 'codigo_profissional_endereco_log'
        )
    );
    
    public function incluir($dados){
        try {
            $this->query("INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} (codigo_ficha, codigo_profissional_endereco_log) values({$dados[$this->name]['codigo_ficha']}, {$dados[$this->name]['codigo_profissional_endereco_log']})");
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
    
    public function duplicar($codigo_ficha, $novo_codigo_ficha) {
        $this->ProfEnderecoLog =& ClassRegistry::init('ProfEnderecoLog');
        
        $codigos = $this->find('all', array('fields' => 'codigo_profissional_endereco_log', 'conditions' => array('codigo_ficha' => $codigo_ficha)));
        try {
            foreach ($codigos as $codigo) {
                $codigo = $codigo[$this->name]['codigo_profissional_endereco_log'];
                $novo_codigo = $this->ProfEnderecoLog->duplicar($codigo);
                $dados = array(
                    'FichaProfEnderecoLog' => array(
                        'codigo_ficha' => $novo_codigo_ficha,
                        'codigo_profissional_endereco_log' => $novo_codigo
                    )
                );
                $this->incluir($dados);
            }
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
    
    public function salvarDaFicha($profissional_endereco_log, $codigo_ficha){
    	$this->delete($codigo_ficha);
		$data = array(
			'codigo_profissional_endereco_log' => $profissional_endereco_log,
			'codigo_ficha' => $codigo_ficha
		);
    	return $this->save($data, array('validate' => false));
    }
    
}

?>
