<?php

class AtribuicaoExame extends AppModel {

	var $name = 'AtribuicaoExame';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'atribuicoes_exames';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $belongsTo = array(
		'Atribuicao' => array(
			'className' => 'Atribuicao',
			'foreignKey' => 'codigo_atribuicao'
		),
		'Exame' => array(
			'className' => 'Exame',
			'foreignKey' => 'codigo_exame'
		)
	);


	var $validate = array(
		'codigo_atribuicao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Atribuição',
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
			'message' => 'Cliente já possui esse exame e atribuição cadastrados.'
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
            $conditions['AtribuicaoExame.codigo'] = $data['codigo'];

		if (!empty ( $data['codigo_atribuicao']))
			$conditions['AtribuicaoExame.codigo_atribuicao'] = $data['codigo_atribuicao'];	

       	if (!empty ( $data['codigo_exame']))
			$conditions['AtribuicaoExame.codigo_exame'] = $data['codigo_exame'];

		if (!empty( $data['codigo_cliente']))
			$conditions['AtribuicaoExame.codigo_cliente'] = $data['codigo_cliente'];

		if (isset( $data['ativo'] ) && $data['ativo'] != "") 
			$conditions ['AtribuicaoExame.ativo'] = $data['ativo'];
       
        
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
						'codigo_atribuicao' => $this->data[$this->name]['codigo_atribuicao'], 
						'codigo_exame' =>  $this->data[$this->name]['codigo_exame'] , 
						'AtribuicaoExame.codigo_cliente' => $this->data[$this->name]['codigo_cliente'] 
				) 
		) );

		if($dados > 0){
			return false;
		}

		return true;
	}

}

?>