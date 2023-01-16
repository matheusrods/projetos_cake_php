<?php
class FornecedorGradeAgenda extends AppModel {

	var $name = 'FornecedorGradeAgenda';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'fornecedores_grade_agenda';
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
		'capacidade_simultanea' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a Capacidade Simultânea de Atendimento.'
			)
		),
		'tempo_consulta' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Tempo de Consulta'
			)
		),
		'codigo_fornecedor' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Fornecedor'
			)
		)
	);
	
	function retorna_grade_especifica($codigo_fornecedor, $codigo_servico) {
	
		$options['fields'] = array(
			'ListaDePrecoProdutoServico.codigo_servico',
			'ListaDePrecoProdutoServico.codigo'
		);
		
		$options['joins']  = array(
			array(
				'table' => 'listas_de_preco_produto_servico',
				'alias' => 'ListaDePrecoProdutoServico',
				'type' => 'LEFT',
				'conditions' => 'ListaDePrecoProdutoServico.codigo = FornecedorGradeAgenda.codigo_lista_de_preco_produto_servico',
			),
		);
		
		$options['conditions'] = array(
			'FornecedorGradeAgenda.codigo_fornecedor' => $codigo_fornecedor,
			'ListaDePrecoProdutoServico.codigo_servico' => $codigo_servico
		);		
		
		$options['order'] = array('FornecedorGradeAgenda.codigo_lista_de_preco_produto_servico DESC');
		
		return $this->find('first', $options);
	}
	
	function retorna_agenda_especifica($codigo_fornecedor, $codigo_servico) {
		
		$options['fields'] = array(
			'FornecedorGradeAgenda.dia_semana',
			'FornecedorGradeAgenda.hora',
			'FornecedorGradeAgenda.capacidade_simultanea',
			'FornecedorGradeAgenda.tempo_consulta',
			'FornecedorGradeAgenda.codigo_fornecedor',
			'FornecedorGradeAgenda.codigo_lista_de_preco_produto_servico'
		);
		 
		$options['joins']  = array(
			array(
				'table' => 'listas_de_preco',
				'alias' => 'ListaDePreco',
				'type' => 'INNER',
				'conditions' => 'ListaDePreco.codigo_fornecedor = FornecedorGradeAgenda.codigo_fornecedor',
			),
			array(
				'table' => 'listas_de_preco_produto',
				'alias' => 'ListaDePrecoProduto',
				'type' => 'INNER',
				'conditions' => 'ListaDePrecoProduto.codigo_lista_de_preco = ListaDePreco.codigo',
			),				
			array(
				'table' => 'listas_de_preco_produto_servico',
				'alias' => 'ListaDePrecoProdutoServico',
				'type' => 'INNER',
				'conditions' => 'ListaDePrecoProdutoServico.codigo_lista_de_preco_produto = ListaDePrecoProduto.codigo AND ListaDePrecoProdutoServico.codigo = FornecedorGradeAgenda.codigo_lista_de_preco_produto_servico',
			),
		);
		
		$options['order'] = array('FornecedorGradeAgenda.dia_semana ASC', 'FornecedorGradeAgenda.hora ASC');
		$options['group'] = array(	
			'FornecedorGradeAgenda.dia_semana',
			'FornecedorGradeAgenda.hora',
			'FornecedorGradeAgenda.capacidade_simultanea',
			'FornecedorGradeAgenda.tempo_consulta',
			'FornecedorGradeAgenda.codigo_fornecedor',
			'FornecedorGradeAgenda.codigo_lista_de_preco_produto_servico'
		);
	 
		$options['conditions'] = array(
			'FornecedorGradeAgenda.codigo_fornecedor' => $codigo_fornecedor,
			'ListaDePrecoProdutoServico.codigo_servico' => $codigo_servico
		);			
		
		return $this->find('all', $options);
	}
	
	function retorna_agenda_padrao($codigo_fornecedor, $codigo_agenda) {
	
		$options['fields'] = array(
				'FornecedorGradeAgenda.dia_semana',
				'FornecedorGradeAgenda.hora',
				'FornecedorGradeAgenda.capacidade_simultanea',
				'FornecedorGradeAgenda.tempo_consulta',
				'FornecedorGradeAgenda.codigo_fornecedor',
				'FornecedorGradeAgenda.codigo_lista_de_preco_produto_servico'
		);
			
		$options['joins']  = array(
				array(
						'table' => 'listas_de_preco',
						'alias' => 'ListaDePreco',
						'type' => 'LEFT',
						'conditions' => 'ListaDePreco.codigo_fornecedor = FornecedorGradeAgenda.codigo_fornecedor',
				),
				array(
						'table' => 'listas_de_preco_produto',
						'alias' => 'ListaDePrecoProduto',
						'type' => 'LEFT',
						'conditions' => 'ListaDePrecoProduto.codigo_lista_de_preco = ListaDePreco.codigo',
				),
				array(
						'table' => 'listas_de_preco_produto_servico',
						'alias' => 'ListaDePrecoProdutoServico',
						'type' => 'LEFT',
						'conditions' => 'ListaDePrecoProdutoServico.codigo_lista_de_preco_produto = ListaDePrecoProduto.codigo AND ListaDePrecoProdutoServico.codigo = FornecedorGradeAgenda.codigo_lista_de_preco_produto_servico',
				),
		);
	
		$options['order'] = array('FornecedorGradeAgenda.dia_semana ASC', 'FornecedorGradeAgenda.hora ASC');
		$options['group'] = array(
				'FornecedorGradeAgenda.dia_semana',
				'FornecedorGradeAgenda.hora',
				'FornecedorGradeAgenda.capacidade_simultanea',
				'FornecedorGradeAgenda.tempo_consulta',
				'FornecedorGradeAgenda.codigo_fornecedor',
				'FornecedorGradeAgenda.codigo_lista_de_preco_produto_servico'
		);
	
		$options['conditions'] = array(
				'FornecedorGradeAgenda.codigo_fornecedor' => $codigo_fornecedor,
				'FornecedorGradeAgenda.codigo_lista_de_preco_produto_servico' => $codigo_agenda
		);
	
		return $this->find('all', $options);
	}	
}

?>