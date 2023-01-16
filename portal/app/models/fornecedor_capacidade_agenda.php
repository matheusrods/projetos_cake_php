<?php
class FornecedorCapacidadeAgenda extends AppModel {

	var $name = 'FornecedorCapacidadeAgenda';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'fornecedores_capacidade_agenda';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $validate = array(
		'dia_semana' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Dia',
			'required' => true
		),
		'hora' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a Hora'
			 )
		),
		'codigo_fornecedor' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Fornecedor'
			)
		)	
	);
	
	function listaServicosPorFornecedor($codigo_fornecedor) {
		$resultado_exames = $this->query("
			SELECT
			    LPPS.codigo,
			    S.descricao
			FROM
			    listas_de_preco_produto_servico LPPS
			    INNER JOIN servico S ON (S.codigo = LPPS.codigo_servico)
			    INNER JOIN listas_de_preco_produto LPP ON (LPP.codigo = LPPS.codigo_lista_de_preco_produto)
			    INNER JOIN listas_de_preco LP ON (LP.codigo = LPP.codigo_lista_de_preco)
			WHERE
			    LP.codigo_fornecedor = '".$codigo_fornecedor."' AND
			    LPPS.codigo NOT IN (SELECT codigo_lista_de_preco_produto_servico FROM fornecedores_capacidade_agenda)");
		
		$exames = array();
		foreach($resultado_exames as $key => $item) {
			$exames[$item[0]['codigo']] = $item[0]['descricao'];
		}
			
		return $exames;		
	}
	
	function retornaServico($codigo) {
		
		if($codigo) {
			
			$resultado_exames = $this->query("
			SELECT
			    LPPS.codigo,
			    S.descricao
			FROM
			    listas_de_preco_produto_servico LPPS
			    INNER JOIN servico S ON (S.codigo = LPPS.codigo_servico)
			    INNER JOIN listas_de_preco_produto LPP ON (LPP.codigo = LPPS.codigo_lista_de_preco_produto)
			    INNER JOIN listas_de_preco LP ON (LP.codigo = LPP.codigo_lista_de_preco)
			WHERE
			    LPPS.codigo = '".$codigo."'");
			
			$exames = array();
			foreach($resultado_exames as $key => $item) {
				$exames[$item[0]['codigo']] = $item[0]['descricao'];
			}			
		} else {
			$exames[0] = 'PADRÃO';
		}
			
		return $exames;
	}	
	

	
	
	function converteFiltroEmCondition($data) {
		$conditions = array();
	
		if (!empty($data['codigo']))
			$conditions['Fornecedor.codigo'] = $data['codigo'];
			 
		if (!empty($data['razao_social']))
			$conditions['Fornecedor.razao_social like'] = '%' . $data['razao_social'] . '%';
	
		if (!empty($data['nome']))
			$conditions['Fornecedor.nome like'] = '%' . $data['nome'] . '%';
	
		if (!empty($data['codigo_documento']))
			$conditions['Fornecedor.codigo_documento like'] = $data['codigo_documento'] . '%';
	
		if (isset($data['ativo'])) {
			if($data['ativo'] == '0')
				$conditions[] = '(Fornecedor.ativo = '.$data['ativo'].' OR Fornecedor.ativo IS NULL)';
			else if ($data['ativo'] == '1')
				$conditions ['Fornecedor.ativo'] = $data['ativo'];
		}
	
		if (!empty($data['estado']))
			$conditions['EnderecoEstado.codigo'] = $data['estado'];
	
		if (! empty ( $data ['cidade'] ))
			$conditions['EnderecoCidade.codigo'] = $data['cidade'];

		return $conditions;
	}	

}

?>