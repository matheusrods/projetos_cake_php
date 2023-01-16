<?php
class UltimoId extends AppModel {

    var $name = 'UltimoId';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'ultimos_ids';
    var $primaryKey = 'codigo';
    var $displayField = 'ultimo_id';
    var $actsAs = array('Secure');

    public function ultimo_id($tabela){
    	$conditions = array('tabela' => $tabela);

    	$sql = "UPDATE {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} SET ultimo_id = ultimo_id+1 
    			WHERE 
    				codigo IN (
    					SELECT codigo FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable}
    					WHERE tabela = '{$tabela}' 
    				)";
		
		try{
    		$this->query('BEGIN TRANSACTION');
    		
    		$retorno = $this->query($sql);
			if($retorno === false)
				throw new Exception();

			$retorno = $this->find('first',compact('conditions'));
			if(!$retorno)
				throw new Exception();

			$this->commit();

			return $retorno[$this->name]['ultimo_id'];
		} catch( Exception $e ) {
			$this->rollback();
		}

    	return false;

    }

}

?>