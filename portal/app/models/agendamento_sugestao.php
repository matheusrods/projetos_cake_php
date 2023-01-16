<?php
class AgendamentoSugestao extends AppModel {

	var $name = 'AgendamentoSugestao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'agendamento_sugestoes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $validate = array(
		'codigo_itens_pedidos_exames' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Item'
			)
		)
	);
	
	function converteFiltroEmCondition($data) {
		$conditions = array();
		
		if (!empty($data['codigo_fornecedor']))
			$conditions['Fornecedor.codigo'] = $data['codigo_fornecedor'];
		
		if (!empty($data['codigo_cliente']))
			$conditions['Cliente.codigo'] = $data['codigo_cliente'];
	
		if (!empty($data['codigo_funcionario']))
			$conditions['Funcionario.codigo'] = $data['codigo_funcionario'];
						
		if (!empty($data['nome_funcionario']))
			$conditions["Funcionario.nome LIKE "] = '%' . $data['nome_funcionario'] . '%';
		

		if (isset($data['notificado']) && $data['notificado'] == '1') {
			$conditions[] = "(PedidoExame.data_notificacao IS NOT NULL)";
		} else {
			$conditions[] = "(PedidoExame.data_notificacao IS NULL)";
		}
		
		if ((isset($data['pendente_agendamento']) && $data['pendente_agendamento'] == '1') || !isset($data['pendente_agendamento'])) {
			$conditions[] = "(ItemPedidoExame.data_agendamento IS NULL)";
		} else {
			$conditions[] = "(ItemPedidoExame.data_agendamento IS NOT NULL)";
		}		
	
		return $conditions;
	}	
	
}

?>