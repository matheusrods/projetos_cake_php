<?php

class RiscoExame extends AppModel {

	var $name = 'RiscoExame';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'riscos_exames';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $belongsTo = array(
		'Exame' => array(
			'className' => 'Exame',
			'foreignKey' => 'codigo_exame'
		),
		'Risco' => array(
			'className' => 'Risco',
			'foreignKey' => 'codigo_risco'
		)
	);


	var $validate = array(
		'codigo_risco' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Agente',
			'required' => true
		),
		'codigo_exame' => array(
			array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Exame',
			'required' => true
			),
			array(
			'rule' => 'registro_unico',
			'message' => 'Cliente já possui esse exame e risco cadastrados.'
			)
		),
		'codigo_cliente' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Cliente',
			'required' => true
		)
	);

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if (!empty($data['codigo']))
            $conditions['RiscoExame.codigo'] = $data['codigo'];

       	if (!empty ( $data['codigo_exame']))
			$conditions['RiscoExame.codigo_exame'] = $data['codigo_exame'];

		if (!empty ( $data['codigo_risco']))
			$conditions['RiscoExame.codigo_risco'] = $data['codigo_risco'];	

		if (!empty( $data['codigo_cliente']))
			$conditions['RiscoExame.codigo_cliente'] = $data['codigo_cliente'];

		if (isset( $data['ativo'] ) && $data['ativo'] != "") 
			$conditions ['RiscoExame.ativo'] = $data['ativo'];
       
        
        return $conditions;
    }

	function carregar($codigo) {
		$dados = $this->find ( 'first', array (
				'conditions' => array (
						$this->name . '.codigo' => $codigo 
				) 
		) );
		return $dados;
	}	

	function registro_unico() {
		$dados = $this->find('count', array (
				'conditions' => array (
						'codigo_risco' => $this->data[$this->name]['codigo_risco'], 
						'codigo_exame' =>  $this->data[$this->name]['codigo_exame'] , 
						'codigo_cliente' => $this->data[$this->name]['codigo_cliente'] 
				) 
		) );

		if($dados > 0){
			return false;
		}

		return true;
	}

}

?>
