<?php
class ResultadoExame extends AppModel {

	public $name		   	= 'ResultadoExame';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'itens_pedidos_exames_baixa';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure');
	
	var $validate = array(
		'data_realizacao_exame' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a data de realização do exame',
			 ),
		),
		'codigo_itens_pedidos_exames' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Código de Item'
			 )
		),
		'resultado' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Resultado do Exame'
			)
		)
	);

		public function converteFiltroEmConditionBaixa($data) {
		
		$conditions = array();
		if (!empty($data['codigo_pedido']))
			$conditions['PedidoExame.codigo'] = $data['codigo_pedido'];
		
		if (!empty($data['codigo_cliente']))
			$conditions['Cliente.codigo'] = $data['codigo_cliente'];
		
		if (!empty($data['codigo_fornecedor']))
			$conditions['Fornecedor.codigo'] = $data['codigo_fornecedor'];
						
		if (!empty($data['nome_funcionario']))
			$conditions["Funcionario.nome LIKE "] = '%' . $data['nome_funcionario'] . '%';
		
		if (!empty($data['codigo_status_pedidos_exames']))
			$conditions['StatusPedidoExame.codigo'] = $data['codigo_status_pedidos_exames'];

		return $conditions;		
	}
}