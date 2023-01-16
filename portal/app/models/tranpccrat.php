<?php
class Tranpccrat extends AppModel {
    var $name = 'Tranpccrat';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'tranpccrat';
    var $primaryKey = null;
    var $actsAs = array('Secure');

    public function sumarizaAnoMes($conditions, $tipo='dre1') {

    	if($tipo == 'dre1') {
        return $this->find('all', array('conditions'=>$conditions, 
        								'fields'=>array('YEAR(dtemiss) as Ano', 
        												'MONTH(dtemiss) as Mes', 
        												'SUM(valor) as Valor'), 
        												'group'=>array('YEAR(dtemiss)', 
    													'MONTH(dtemiss)')));
        }
        if($tipo == 'dre2') {
    	return $this->find('all', array('conditions'=>$conditions, 
    								'fields'=>array('YEAR(dtpagto) as Ano', 
    												'MONTH(dtpagto) as Mes', 
    												'SUM(valor) as Valor'), 
    												'group'=>array('YEAR(dtpagto)', 
													'MONTH(dtpagto)')));
        }
    }
    
}
?>