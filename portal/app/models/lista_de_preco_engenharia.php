<?php
class ListaDePrecoEngenharia extends AppModel {
	var $name = 'ListaDePrecoProdutoServico';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'listas_de_preco_produto_servico';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $belongsTo = array(
		'ListaDePrecoProduto' => array('foreignKey' => 'codigo_lista_de_preco_produto'),
		'Servico' => array('foreignKey' => 'codigo_servico'),
	);
	var $validate = array(
		'codigo_lista_de_preco_produto' => array(
			'rule' => 'notEmpty',
			'message' => 'Produto não informado',
			'required' => true,
			'allowEmpty' => false,
		),
		'codigo_servico' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Serviço não informado',
				'required' => true,
				'allowEmpty' => false,
			),
			array(
				'rule' => 'unico',
				'message' => 'Já existe este serviço para o produto',
				'required' => true,
				'allowEmpty' => false,
			),
		)
	);
}
?>