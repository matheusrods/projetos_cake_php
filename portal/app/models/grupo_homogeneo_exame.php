<?php

class GrupoHomogeneoExame extends AppModel {

	var $name = 'GrupoHomogeneoExame';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'grupos_homogeneos_exames';
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
            $conditions['GrupoHomogeneoExame.codigo'] = $data['codigo'];

        if (! empty ( $data ['descricao'] ))
			$conditions ['GrupoHomogeneoExame.descricao LIKE'] = '%' . $data ['descricao'] . '%';

		if (!empty($data['codigo_cliente']))
            $conditions['GrupoHomogeneoExame.codigo_cliente'] = $data['codigo_cliente'];

		if (!empty($data['codigo_setor']))
            $conditions['GrupoHomogeneoExame.codigo_setor'] = $data['codigo_setor'];

        if (!empty($data['codigo_cargo']))
            $conditions['GrupoHomogeneoExame.codigo_cargo'] = $data['codigo_cargo'];

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] == '0')
				$conditions [] = '(GrupoHomogeneoExame.ativo = ' . $data ['ativo'] . ' OR GrupoHomogeneoExame.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['GrupoHomogeneoExame.ativo'] = $data ['ativo'];
        }
        
        return $conditions;
    }

    function lista_por_cliente($codigo_cliente){

		$conditions = array('GrupoHomogeneoExame.ativo' => 1, 'GrupoHomogeneoExame.codigo_cliente' => $codigo_cliente);
		$fields = array('GrupoHomogeneoExame.codigo', 'GrupoHomogeneoExame.descricao');
		$order = array('GrupoHomogeneoExame.descricao ASC');

		$dados = $this->find('list', compact('conditions', 'fields','order'));
		
		return $dados;

	}

	function lista_por_grupo_homogeneo($codigo){
		$GrupoHomogeneoExameDetalhe =& ClassRegistry::Init('GrupoHomogeneoExameDetalhe');
		$Setor =& ClassRegistry::Init('Setor');
		$Cargo =& ClassRegistry::Init('Cargo');
		
		$joins  = array(
                    array(
                      'table' => $GrupoHomogeneoExameDetalhe->databaseTable.'.'.$GrupoHomogeneoExameDetalhe->tableSchema.'.'.$GrupoHomogeneoExameDetalhe->useTable,
                      'alias' => 'GrupoHomogeneoExameDetalhe',
                      'type' => 'LEFT',
                      'conditions' => 'GrupoHomogeneoExame.codigo = GrupoHomogeneoExameDetalhe.codigo_grupo_homogeneo_exame',
                    ),
                    array(
                      'table' => $Setor->databaseTable.'.'.$Setor->tableSchema.'.'.$Setor->useTable,
                      'alias' => 'Setor',
                      'type' => 'LEFT',
                      'conditions' => 'GrupoHomogeneoExameDetalhe.codigo_setor = Setor.codigo',
                    ),
                    array(
                      'table' => $Cargo->databaseTable.'.'.$Cargo->tableSchema.'.'.$Cargo->useTable,
                      'alias' => 'Cargo',
                      'type' => 'LEFT',
                      'conditions' => 'GrupoHomogeneoExameDetalhe.codigo_cargo = Cargo.codigo',
                    )
                );

		$conditions = array('GrupoHomogeneoExame.ativo' => 1, 'GrupoHomogeneoExame.codigo' => $codigo);
		$fields = array(
					'GrupoHomogeneoExame.codigo', 'GrupoHomogeneoExame.descricao', 'GrupoHomogeneoExame.codigo_cliente',
					'GrupoHomogeneoExameDetalhe.codigo', 'GrupoHomogeneoExameDetalhe.codigo_grupo_homogeneo_exame', 'GrupoHomogeneoExameDetalhe.codigo_setor', 'GrupoHomogeneoExameDetalhe.codigo_cargo',
					'Setor.codigo', 'Setor.descricao',
					'Cargo.codigo', 'Cargo.descricao'
				);
 
		$order = array('GrupoHomogeneoExameDetalhe.descricao ASC');

		$dados = $this->find('all', array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields, 'order' => $order));
		
		return $dados;

	}

	function localiza_ghe_importacao($data){
		$this->GrupoHomogeneoExameDetalhe =& ClassRegistry::Init('GrupoHomogeneoExameDetalhe');

		$nome_ghe = $data['nome_ghe'];

		$consulta_ghe = $this->find("first", array('conditions' => array('descricao' => $nome_ghe)));
		if(isset($consulta_ghe) && !empty($consulta_ghe)){
			$retorno['Dados'] = $consulta_ghe;
		}
		else{
			$dados = array(
				'GrupoHomogeneoExame' => array(
					'descricao' => $nome_ghe,
					'codigo_cliente' => $data['codigo_cliente_unidade'],
					'ativo' => 1
				)
			);
			if(!$this->incluir($dados)){
				$retorno['Erro']['GrupoHomogeneoExame'] = array('codigo_grupo_homogeneo_exame' => 'Nao e possivel inserir o Grupo Homogeneo!');
			}
			else{
				$retorno_ghe = $this->find('first', array('conditions' => array('codigo' => $this->id)));
				$retorno['Dados'] = $retorno_ghe;
				
				$dados_detalhes = array(
					'GrupoHomogeneoExameDetalhe' => array(
						'codigo_grupo_homogeneo_exame' => $this->id,
						'codigo_setor' => $data['codigo_setor'],
						'codigo_cargo' => $data['codigo_cargo']
					)
				);

				if(!$this->GrupoHomogeneoExameDetalhe->incluir($dados_detalhes)){
					$retorno['Erro']['GrupoHomogeneoExameDetalhe'] = array('codigo_grupo_homogeneo_exame' => 'Nao e possivel inserir os detalhes do Grupo Homogeneo!');
				}
				else{
					$retorno_ghe_detalhes = $this->GrupoHomogeneoExameDetalhe->find('first', array('conditions' => array('codigo' => $this->GrupoHomogeneoExameDetalhe->id)));
					$retorno['Dados'] = $retorno_ghe_detalhes;
				}
			}
		}
        return $retorno;
	}

}