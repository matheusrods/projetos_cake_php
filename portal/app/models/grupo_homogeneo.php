<?php

class GrupoHomogeneo extends AppModel {

	var $name = 'GrupoHomogeneo';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'grupos_homogeneos_exposicao';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');


	var $validate = array(
		'descricao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Descrição',
			'required' => true
		),
		'codigo_cliente' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Cliente',
			'required' => true
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
            $conditions['GrupoHomogeneo.codigo'] = $data['codigo'];

        if (! empty ( $data ['descricao'] ))
			$conditions ['GrupoHomogeneo.descricao LIKE'] = '%' . $data ['descricao'] . '%';

		if (!empty($data['codigo_cliente']))
            $conditions['GrupoHomogeneo.codigo_cliente'] = $data['codigo_cliente'];

		if (!empty($data['codigo_setor']))
            $conditions['GrupoHomogeneo.codigo_setor'] = $data['codigo_setor'];

        if (!empty($data['codigo_cargo']))
            $conditions['GrupoHomogeneo.codigo_cargo'] = $data['codigo_cargo'];

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] == '0')
				$conditions [] = '(GrupoHomogeneo.ativo = ' . $data ['ativo'] . ' OR GrupoHomogeneo.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['GrupoHomogeneo.ativo'] = $data ['ativo'];
        }
        
        return $conditions;
    }

    function lista_por_cliente($codigo_cliente){

		$conditions = array('GrupoHomogeneo.ativo' => 1, 'GrupoHomogeneo.codigo_cliente' => $codigo_cliente);
		$fields = array('GrupoHomogeneo.codigo', 'GrupoHomogeneo.descricao');
		$order = array('GrupoHomogeneo.descricao ASC');

		$dados = $this->find('list', compact('conditions', 'fields','order'));
		
		return $dados;

	}

	function lista_por_grupo_homogeneo($codigo){
		$GrupoHomDetalhe =& ClassRegistry::Init('GrupoHomDetalhe');
		$Setor =& ClassRegistry::Init('Setor');
		$Cargo =& ClassRegistry::Init('Cargo');
		
		$joins  = array(
                    array(
                      'table' => $GrupoHomDetalhe->databaseTable.'.'.$GrupoHomDetalhe->tableSchema.'.'.$GrupoHomDetalhe->useTable,
                      'alias' => 'GrupoHomDetalhe',
                      'type' => 'LEFT',
                      'conditions' => 'GrupoHomogeneo.codigo = GrupoHomDetalhe.codigo_grupo_homogeneo',
                    ),
                    array(
                      'table' => $Setor->databaseTable.'.'.$Setor->tableSchema.'.'.$Setor->useTable,
                      'alias' => 'Setor',
                      'type' => 'LEFT',
                      'conditions' => 'GrupoHomDetalhe.codigo_setor = Setor.codigo',
                    ),
                    array(
                      'table' => $Cargo->databaseTable.'.'.$Cargo->tableSchema.'.'.$Cargo->useTable,
                      'alias' => 'Cargo',
                      'type' => 'LEFT',
                      'conditions' => 'GrupoHomDetalhe.codigo_cargo = Cargo.codigo',
                    )
                );

		$conditions = array('GrupoHomogeneo.ativo' => 1, 'GrupoHomogeneo.codigo' => $codigo);
		$fields = array(
					'GrupoHomogeneo.codigo', 'GrupoHomogeneo.descricao', 'GrupoHomogeneo.codigo_cliente',
					'GrupoHomDetalhe.codigo', 'GrupoHomDetalhe.codigo_grupo_homogeneo', 'GrupoHomDetalhe.codigo_setor', 'GrupoHomDetalhe.codigo_cargo',
					'Setor.codigo', 'Setor.descricao',
					'Cargo.codigo', 'Cargo.descricao'
				);
 
		$order = array('GrupoHomogeneo.descricao ASC');

		$dados = $this->find('all', array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields, 'order' => $order));
		
		return $dados;

	}

	function localiza_ghe_importacao($data){
		$this->GrupoHomDetalhe =& ClassRegistry::Init('GrupoHomDetalhe');

		$nome_ghe = $data['nome_ghe'];

		$consulta_ghe = $this->find("first", array('conditions' => array('descricao' => $nome_ghe)));
		if(isset($consulta_ghe) && !empty($consulta_ghe)){
			$retorno['Dados'] = $consulta_ghe;
		}
		else{
			$dados = array(
				'GrupoHomogeneo' => array(
					'descricao' => $nome_ghe,
					'codigo_cliente' => $data['codigo_cliente_unidade'],
					'ativo' => 1
				)
			);
			if(!$this->incluir($dados)){
				$retorno['Erro']['GrupoHomogeneo'] = array('codigo_grupo_homogeneo' => 'Nao e possivel inserir o Grupo Homogeneo!');
			}
			else{
				$retorno_ghe = $this->find('first', array('conditions' => array('codigo' => $this->id)));
				$retorno['Dados'] = $retorno_ghe;
				
				$dados_detalhes = array(
					'GrupoHomDetalhe' => array(
						'codigo_grupo_homogeneo' => $this->id,
						'codigo_setor' => $data['codigo_setor'],
						'codigo_cargo' => $data['codigo_cargo']
					)
				);

				if(!$this->GrupoHomDetalhe->incluir($dados_detalhes)){
					$retorno['Erro']['GrupoHomDetalhe'] = array('codigo_grupo_homogeneo' => 'Nao e possivel inserir os detalhes do Grupo Homogeneo!');
				}
				else{
					$retorno_ghe_detalhes = $this->GrupoHomDetalhe->find('first', array('conditions' => array('codigo' => $this->GrupoHomDetalhe->id)));
					$retorno['Dados'] = $retorno_ghe_detalhes;
				}
			}
		}
        return $retorno;
	}
}

?>