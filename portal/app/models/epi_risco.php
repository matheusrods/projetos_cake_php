<?php
class EpiRisco extends AppModel {

    var $name = 'EpiRisco';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'epi_riscos';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

	var $validate = array(
		'codigo_risco' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a descrição.',
				'required' => true
			 ),
			'UnicaporEpi' => array(
				'rule' => 'validaEpiRisco',
				'message' => 'Risco já existe para esta Epi.',
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
            $conditions['EpiRisco.codigo'] = $data['codigo'];

        if (! empty ( $data ['nome_agente'] ))
			$conditions ['EpiRisco.nome_agente LIKE'] = '%' . $data ['nome_agente'] . '%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(EpiRisco.ativo = ' . $data ['ativo'] . ' OR EpiRisco.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['EpiRisco.ativo'] = $data ['ativo'];
	        }
        
        return $conditions;
    }

    function validaEpiRisco(){
		
		$conditions = array(
			'codigo_epi' => $this->data['EpiRisco']['codigo_epi'],
			'codigo_risco' =>  $this->data['EpiRisco']['codigo_risco']
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
