<?php

class ExameFuncao extends AppModel {

	var $name = 'ExameFuncao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'exames_funcoes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $belongsTo = array(
		'Exame' => array(
			'className' => 'Exame',
			'foreignKey' => 'codigo_exame'
		),
		'Funcao' => array(
			'className' => 'Funcao',
			'foreignKey' => 'codigo_funcao'
		)
	);


	var $validate = array(
		'codigo_exame' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Exame',
			'required' => true
		),
	'codigo_funcao' => array(
		array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Função',
			'required' => true
			),
		array(	
			'rule' => 'registro_unico',
			'message' => 'Este exame já foi cadastrado para essa função'
			)
		)
	);

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if (!empty($data['codigo']))
            $conditions['ExameFuncao.codigo'] = $data['codigo'];

       	if (!empty ( $data['codigo_exame']))
			$conditions['ExameFuncao.codigo_exame'] = $data['codigo_exame'];

		if (!empty ( $data['codigo_funcao']))
			$conditions['ExameFuncao.codigo_funcao'] = $data['codigo_funcao'];	

		if (isset( $data['ativo'] ) && $data['ativo'] != "" ) 
			$conditions ['ExameFuncao.ativo'] = $data['ativo'];
       
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
						'codigo_exame' =>  $this->data[$this->name]['codigo_exame'], 
						'codigo_funcao' => $this->data[$this->name]['codigo_funcao'] 
				) 
		) );

		if($dados > 0){
			return false;
		}

		return true;
	}

}

?>