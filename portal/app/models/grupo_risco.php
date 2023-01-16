<?php

class GrupoRisco extends AppModel {

	var $name = 'GrupoRisco';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'grupos_riscos';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

var $validate = array(
		'descricao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a descrição.',
				'required' => true
			 ),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Descrição já existe.',
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
            $conditions['GrupoRisco.codigo'] = $data['codigo'];

        if (! empty ( $data ['descricao'] ))
			$conditions ['GrupoRisco.descricao LIKE'] = '%' . $data ['descricao'] . '%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(GrupoRisco.ativo = ' . $data ['ativo'] . ' OR GrupoRisco.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['GrupoRisco.ativo'] = $data ['ativo'];
	        }
        
        return $conditions;
    }

	function retorna_grupo($codigo_grupo = null){
		$conditions = array();
		if(!is_null($codigo_grupo)){
			$conditions['OR'] = array('ativo' => 1,'codigo' => $codigo_grupo);
		} else {
			$conditions = array('ativo' => 1);
		}
		$dados = $this->find('list', array('conditions' => $conditions,'fields' => array('codigo', 'descricao')));
	 	return $dados;
	}

	public function get_riscos_grupo($incluirInativos = array()){

		$joins  = array(
			array(
				'table' => 'riscos',
				'alias' => 'Risco',
				'type' => 'INNER',
				'conditions' => 'Risco.codigo_grupo = GrupoRisco.codigo'
				)
		);

		$conditions = array(
			'GrupoRisco.ativo' => 1
		);

		$order = array('GrupoRisco.codigo');

		$fields = array('GrupoRisco.codigo', 'GrupoRisco.descricao', 'Risco.codigo', 'Risco.nome_agente');

        $this->bindModel(array(
            'hasMany' => array(
                'Risco' => array(            
                		'foreignKey' => 'codigo_grupo', 
                		'conditions' => array('OR'=>array('ativo' => 1,'codigo'=>$incluirInativos)), 
                		'fields' => array('codigo', 'nome_agente')           
                )
            )
        ));

		$dados = $this->find('all', array('conditions' => $conditions, 'order' => $order));
		$riscos_por_grupo = array();
		foreach($dados as $key => $dado) {
			foreach($dado['Risco'] as $key1 => $dado_risco){
				// $riscos_por_grupo[$dado['GrupoRisco']['codigo']][$dado_risco['codigo']] = $dado_risco['nome_agente'];					
				$riscos_por_grupo[$dado['GrupoRisco']['codigo']][$dado_risco['codigo']] = $dado_risco['nome_agente'];					
			}

			// $riscos_por_grupo
		}
		
		return $riscos_por_grupo;
	}
}

?>