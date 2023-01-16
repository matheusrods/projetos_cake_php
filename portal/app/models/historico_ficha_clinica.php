<?php
class HistoricoFichaClinica extends AppModel {

	public $name            = 'HistoricoFichaClinica';
	public $tableSchema     = 'dbo';
	public $databaseTable   = 'RHHealth';
	public $useTable        = 'historico_ficha_clinica';
	public $primaryKey      = 'codigo';
	public $actsAs          = array('Secure', 'Containable');
	public $recursive       = -1;



	public function getDadosPedidoExames($codigo_pedido_exame)
	{

		//monta a query para pegar os dados de historico
		$dados['fields'] = array(
			'HistoricoFichaClinica.codigo',
			'Funcionario.cpf',
			'Funcionario.nome',
			'HistoricoFichaClinica.cnpj_unidade',
			'Cliente.razao_social',
			'Cliente.nome_fantasia',
			'HistoricoFichaClinica.setor',
			'HistoricoFichaClinica.cargo',
			'HistoricoFichaClinica.data_atendimento',
			'HistoricoFichaClinica.observacoes',
		);

		//faz o join do historico
		$dados['joins'] = array(
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => '(PedidoExame.codigo_funcionario = Funcionario.codigo)'
			),
			array(
				'table' => 'historico_ficha_clinica',
				'alias' => 'HistoricoFichaClinica',
				'type' => 'INNER',
				'conditions' => '(Funcionario.cpf = HistoricoFichaClinica.cpf)'
			),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => "(RIGHT(replicate('0',14) + CONVERT(VARCHAR,HistoricoFichaClinica.cnpj_unidade),14) = Cliente.codigo_documento)"
			),
		);

		$dados['conditions']['PedidoExame.codigo'] = $codigo_pedido_exame;

		return $dados;

	}//fim getDadosPedidosExames

}
