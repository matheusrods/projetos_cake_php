<?php
class EpcRisco extends AppModel {

    var $name = 'EpcRisco';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'epc_riscos';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

	var $validate = array(
		'codigo_risco' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a descrição.',
				'required' => true
			 ),
			'UnicaporEpc' => array(
				'rule' => 'validaEpcRisco',
				'message' => 'Risco já existe para esta Epc.',
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
            $conditions['EpcRisco.codigo'] = $data['codigo'];

        if (! empty ( $data ['nome_agente'] ))
			$conditions ['EpcRisco.nome_agente LIKE'] = '%' . $data ['nome_agente'] . '%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(EpcRisco.ativo = ' . $data ['ativo'] . ' OR EpcRisco.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['EpcRisco.ativo'] = $data ['ativo'];
	        }
        
        return $conditions;
    }

    function validaEpcRisco(){
		
		$conditions = array(
			'codigo_epc' => $this->data['EpcRisco']['codigo_epc'],
			'codigo_risco' =>  $this->data['EpcRisco']['codigo_risco']
		);
		
		$dados = $this->find('first', array('conditions' =>  $conditions));
		
		if(empty($dados)){
			return true;
		} else{
			return false;
		}
	}

}
