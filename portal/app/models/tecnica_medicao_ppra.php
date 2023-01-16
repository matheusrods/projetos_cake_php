<?php

class TecnicaMedicaoPpra extends AppModel {

	var $name = 'TecnicaMedicaoPpra';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'tecnicas_medicao_ppra';
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
		'abreviacao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a abreviacao.',
				'required' => true
			 ),
		),
	);

	function converteFiltroEmCondition($data) {
        $conditions = array();

		//verifica se tem o codigo cliente
        if(!empty($data['codigo_cliente'])){
			$conditions['TecnicaMedicaoPpra.codigo_cliente'] = $data['codigo_cliente'];		
		}

        if (! empty ( $data ['nome'] ))
			$conditions ['TecnicaMedicaoPpra.nome LIKE'] = '%' . $data ['nome'] . '%';

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

	function get_tecnicas_medicao($codigo_cliente){
		$dados = $this->find('list', array('conditions' => array('TecnicaMedicaoPpra.ativo' => 1, 'TecnicaMedicaoPpra.codigo_cliente' => $codigo_cliente), 'fields' => array('TecnicaMedicaoPpra.codigo', 'TecnicaMedicaoPpra.abreviacao'),'order' => array('TecnicaMedicaoPpra.codigo ASC')));
		return $dados;
	}

	public function verificarObterUnicaTecnicaClienteMatriz($codigo_cliente_matriz) {

		$queryOptions = 			array(
			'fields' => array(
				'Cliente.codigo',
				'Cliente.nome_fantasia',
				'Cliente.razao_social',					
				'TecnicaMedicaoPpra.codigo',
				'TecnicaMedicaoPpra.nome',
				'TecnicaMedicaoPpra.descricao'
			),			
			'joins' => array(
				array(
					'table' => 'cliente',
					'alias' => 'Cliente',
					'conditions' => 'Cliente.codigo = TecnicaMedicaoPpra.codigo_cliente',
					'type' => 'INNER',																
				),	
			),
			'conditions' => array(
				'Cliente.codigo' => $codigo_cliente_matriz,				
				'TecnicaMedicaoPpra.ativo' => 1,
				'TecnicaMedicaoPpra.codigo_empresa' => 1
			),
			'order' => array(
				'TecnicaMedicaoPpra.data_inclusao' => 'DESC'
			),
			'recursive' => -1				
		);

		// echo '<pre>';
		// print_r($this->find(
		// 	'sql',
		// 	$queryOptions
		// ));
		// echo '</pre>';

		$arrTecnicasMedicaoClienteMatriz = $this->find('all', $queryOptions);

		if(!empty($arrTecnicasMedicaoClienteMatriz) && count($arrTecnicasMedicaoClienteMatriz) === 1) {

			return $arrTecnicasMedicaoClienteMatriz[0];
		}

		return false;
	}

	public function getClienteMatriz($codigo_cliente_matriz) {
		
		$queryOptions = $this->getQueryOptionsClienteMatriz($codigo_cliente_matriz);

		$query = $this->find('all', $queryOptions);

		return $query;
	}

	private function getQueryOptionsClienteMatriz($codigo_cliente_matriz) {

		return array(
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
				'TecnicaMedicaoPpra.codigo',
				'TecnicaMedicaoPpra.nome',
				'TecnicaMedicaoPpra.descricao'
			),
			'joins' => array(
				array(
					'table' => 'grupos_exposicao_risco',
					'alias' => 'GrupoExposicaoRisco',
					'conditions' => 'GrupoExposicaoRisco.codigo_tecnica_medicao = TecnicaMedicaoPpra.codigo',
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
				'TecnicaMedicaoPpra.ativo' => 1,
				'TecnicaMedicaoPpra.codigo_empresa' => 1
			),
			'order' => array(
				'TecnicaMedicaoPpra.data_inclusao' => 'DESC'
			),
			'recursive' => -1
		);
	}

	public function getUltimaClienteMatriz($codigo_cliente_matriz) {

		$queryOptions = $this->getQueryOptionsClienteMatriz($codigo_cliente_matriz);

		// echo '<pre>';
		// print_r($this->find(
		// 	'sql',
		// 	$queryOptions
		// ));
		// echo '</pre>';

		$query = $this->find(
			'first',
			$queryOptions
		);	
		
		return $query;
	}	
}

?>