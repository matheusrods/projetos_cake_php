<?php
class MotivoBloqueio extends AppModel {
    var $name = 'MotivoBloqueio';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'motivo_bloqueio';
    var $displayField = 'descricao';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    const MOTIVO_OK = 1;
    
   /**
    * Metodo generico para listar
    * 
    * @param array $conditions
    * @return array 
    */
   public function listar($type = 'all', array $conditions=null) {
       $result = $this->find($type, array(
           'conditions' => $conditions
       ));       
       return $result;
   }
   
   /**
    * Retorna um combo chave-valor com todos motivos
    * 
    * @return array
    */
   public function combo() {
        $motivos = $this->find('all');
        foreach($motivos as $motivo) {
            $lista[$motivo['MotivoBloqueio']['codigo']] = $motivo['MotivoBloqueio']['descricao'];
        }   
        
        return $lista;
   }
}
