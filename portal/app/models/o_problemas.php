<?php
class OProblemas extends AppModel {
    var $name = 'OProblemas';
    var $useDbConfig = 'ocomon';
    var $databaseTable = 'ocomon_rc6';
    var $useTable = 'problemas';
    var $primaryKey = 'prob_id';
    var $displayField = 'problema';


	public function comboProblemas() 
	{

		$order = array('OProblemas.problema');
		$conditions['OProblemas.prob_id'] = array(181, 68, 176);
		
		return $this->find('list',compact('conditions', 'order'));

	}





}