<?php
class FonteGeradoraRisco extends AppModel {

    var $name = 'FonteGeradoraRisco';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'fontes_geradoras_riscos';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

	var $validate = array(
		'codigo_risco' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a descrição.',
				'required' => true
			 ),
			'UnicaporFonte' => array(
				'rule' => 'validaFonteGeradoraRisco',
				'message' => 'Risco já existe para esta Fonte Geradora.',
			),
		),
		'ativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Status',
			'required' => true
		)
	);

	function converteFiltroEmCondition($data) {
        $conditions = array();
        
        if (!empty($data['codigo']))
            $conditions['FonteGeradoraRisco.codigo'] = $data['codigo'];

        if (! empty ( $data ['nome_agente'] ))
			$conditions ['FonteGeradoraRisco.nome_agente LIKE'] = '%' . $data ['nome_agente'] . '%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(FonteGeradoraRisco.ativo = ' . $data ['ativo'] . ' OR FonteGeradoraRisco.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['FonteGeradoraRisco.ativo'] = $data ['ativo'];
	        }
        
        return $conditions;
    }

    function validaFonteGeradoraRisco(){
		
		$conditions = array(
			'codigo_fonte_geradora' => $this->data['FonteGeradoraRisco']['codigo_fonte_geradora'],
			'codigo_risco' =>  $this->data['FonteGeradoraRisco']['codigo_risco']
			);
		
		$dados = $this->find('first', array('conditions' =>  $conditions));
		
		if(empty($dados)){
			return true;
		}
		else{
			return false;
		}
	}

}
