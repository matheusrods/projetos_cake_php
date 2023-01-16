<?php
class PdaTemaAcoes extends AppModel {
	var $name = 'PdaTemaAcoes';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pda_tema_acoes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Containable');

	public function getTemaAcoes($codigo_tema=null)
	{

		$fields = array('codigo','descricao');
		$joins = array(
			array(
                'table' => 'pda_tema_pda_tema_acoes',
                'alias' => 'PTPTA',
                'type' => 'INNER',
                'conditions' => array('PdaTemaAcoes.codigo = PTPTA.codigo_pda_tema_acoes')
            ),
		);

		$conditions = array();
		if(!is_null($codigo_tema)) {
			$conditions['PTPTA.codigo_pda_tema'] = $codigo_tema;
		}

		$result = $this->find('list',array('fields' => $fields,'joins'=>$joins,'conditions'=>$conditions));

		return $result;

	}//fim getTemaAcoes

	
}
