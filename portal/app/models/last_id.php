<?php
class LastId extends AppModel {

    var $name = 'LastId';
    var $databaseTable = 'RHHEALTH';
    var $tableSchema = 'dbo';
    var $useTable = 'last_id';
    var $primaryKey = 'codigo';
    var $displayField = 'last_id';
    var $actsAs = array('Secure');

    public function last_id($tabela){
    	$conditions = array('tabela' => $tabela);

    	$sql = "UPDATE {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} SET last_id = last_id+1 
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

			return $retorno[$this->name]['last_id'];
		} catch( Exception $e ) {
			$this->rollback();
		}

    	return false;

    }

}

?>