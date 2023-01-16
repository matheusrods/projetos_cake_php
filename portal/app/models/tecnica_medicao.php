<?php

class TecnicaMedicao extends AppModel {

	var $name = 'TecnicaMedicao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'tecnicas_medicao';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');


	var $validate = array(
		'nome' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Nome.',
				'required' => true
			 ),
		),
	);

	function converteFiltroEmCondition($data) {
        $conditions = array();

		//verifica se tem o codigo cliente
        if(!empty($data['codigo_cliente'])){
			$conditions['TecnicaMedicao.codigo_cliente'] = $data['codigo_cliente'];		
		}

        if (! empty ( $data ['nome'] ))
			$conditions ['TecnicaMedicao.nome LIKE'] = '%' . $data ['nome'] . '%';

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

	function retorna_tecnicas(){
		$dados = $this->find('list', array('conditions' => array('TecnicaMedicao.ativo' => 1, 'TecnicaMedicao.codigo_esocial IS NOT NULL'), 'fields' => array('TecnicaMedicao.codigo_esocial', 'TecnicaMedicao.abreviacao'),'order' => array('TecnicaMedicao.codigo_esocial ASC')));
		return $dados;
	}
	
	public function getUltimaTecnicaMedicaoRiscoMatriz($codigo_risco, $codigo_cliente_matriz) {

		$queryOptions = array(
			'fields' => array(
				'Cliente.codigo',
				'Cliente.nome_fantasia',
				'Cliente.razao_social',
				'ClienteMatriz.codigo',
				'ClienteMatriz.nome_fantasia',
				'ClienteMatriz.razao_social',				
				'Risco.codigo',
				'Risco.nome_agente',
				'GrupoExposicaoRisco.codigo',
				'GrupoExposicaoRisco.codigo_risco',
				'GrupoExposicaoRisco.codigo_grupo_exposicao',
				'ClienteSetor.codigo',
				'ClienteSetor.codigo_cliente',
				'ClienteSetor.codigo_setor',				
				'TecnicaMedicao.codigo',
				'TecnicaMedicao.nome'
			),
			'joins' => array(
				array(
					'table' => 'grupos_exposicao_risco',
					'alias' => 'GrupoExposicaoRisco',
					'conditions' => 'GrupoExposicaoRisco.codigo_tecnica_medicao = TecnicaMedicao.codigo',
					'type' => 'INNER',							
				),
				array(
					'table' => 'riscos',
					'alias' => 'Risco',
					'conditions' => 'Risco.codigo = GrupoExposicaoRisco.codigo_risco',
					'type' => 'INNER',							
				),		
				array(
					'table' => 'grupo_exposicao',
					'alias' => 'GrupoExposicao',
					'conditions' => 'GrupoExposicao.codigo = GrupoExposicaoRisco.codigo_grupo_exposicao',
				),		
				array(
					'table' => 'clientes_setores',
					'alias' => 'ClienteSetor',
					'conditions' => 'ClienteSetor.codigo = GrupoExposicao.codigo_cliente_setor',
					'type' => 'INNER',									
				),	
				array(
					'table' => 'cliente',
					'alias' => 'Cliente',
					'conditions' => 'Cliente.codigo = ClienteSetor.codigo_cliente',
					'type' => 'INNER',																
				),	
				array(
					'table' => 'grupos_economicos_clientes',
					'alias' => 'GrupoEconomicoCliente',
					'conditions' => 'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo',
					'type' => 'INNER',																
				),		
				array(
					'table' => 'grupos_economicos',
					'alias' => 'GrupoEconomico',
					'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
					'type' => 'INNER',																
				),		
				array(
					'table' => 'cliente',
					'alias' => 'ClienteMatriz',
					'conditions' => 'ClienteMatriz.codigo = GrupoEconomico.codigo_cliente',
					'type' => 'INNER',								
				)										
			),
			'conditions' => array(
				'ClienteMatriz.codigo' => $codigo_cliente_matriz,				
				'TecnicaMedicao.ativo' => 1
			),
			'order' => array(
				'TecnicaMedicao.data_inclusao' => 'DESC'
			),
			'recursive' => -1
		);

		$query = $this->find(
			'first',
			$queryOptions
		);	
		
		return $query;
	}

	public function getUltimaTecnicaMedicaoCliente($codigo_cliente) {

		$queryOptions = array(
			'fields' => array(
				'Cliente.codigo',
				'Cliente.nome_fantasia',
				'Cliente.razao_social',
				'ClienteMatriz.codigo',
				'ClienteMatriz.nome_fantasia',
				'ClienteMatriz.razao_social',				
				'Risco.codigo',
				'Risco.nome_agente',
				'GrupoExposicaoRisco.codigo',
				'GrupoExposicaoRisco.codigo_risco',
				'GrupoExposicaoRisco.codigo_grupo_exposicao',
				'ClienteSetor.codigo',
				'ClienteSetor.codigo_cliente',
				'ClienteSetor.codigo_setor',				
				'TecnicaMedicao.codigo',
				'TecnicaMedicao.nome'
			),
			'joins' => array(
				array(
					'table' => 'grupos_exposicao_risco',
					'alias' => 'GrupoExposicaoRisco',
					'conditions' => 'GrupoExposicaoRisco.codigo_tecnica_medicao = TecnicaMedicao.codigo',
					'type' => 'INNER',							
				),
				array(
					'table' => 'riscos',
					'alias' => 'Risco',
					'conditions' => 'Risco.codigo = GrupoExposicaoRisco.codigo_risco',
					'type' => 'INNER',							
				),		
				array(
					'table' => 'grupo_exposicao',
					'alias' => 'GrupoExposicao',
					'conditions' => 'GrupoExposicao.codigo = GrupoExposicaoRisco.codigo_grupo_exposicao',
				),		
				array(
					'table' => 'clientes_setores',
					'alias' => 'ClienteSetor',
					'conditions' => 'ClienteSetor.codigo = GrupoExposicao.codigo_cliente_setor',
					'type' => 'INNER',									
				),	
				array(
					'table' => 'cliente',
					'alias' => 'Cliente',
					'conditions' => 'Cliente.codigo = ClienteSetor.codigo_cliente',
					'type' => 'INNER',																
				),	
				array(
					'table' => 'grupos_economicos_clientes',
					'alias' => 'GrupoEconomicoCliente',
					'conditions' => 'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo',
					'type' => 'INNER',																
				),		
				array(
					'table' => 'grupos_economicos',
					'alias' => 'GrupoEconomico',
					'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
					'type' => 'INNER',																
				),		
				array(
					'table' => 'cliente',
					'alias' => 'ClienteMatriz',
					'conditions' => 'ClienteMatriz.codigo = GrupoEconomico.codigo_cliente',
					'type' => 'INNER',								
				)										
			),
			'conditions' => array(
				'ClienteMatriz.codigo' => $codigo_cliente,				
				'TecnicaMedicao.ativo' => 1
			),
			'order' => array(
				'TecnicaMedicao.data_inclusao' => 'DESC'
			),
			'recursive' => -1
		);

		$query = $this->find(
			'first',
			$queryOptions
		);	
		
		return $query;
	}	

}

?>