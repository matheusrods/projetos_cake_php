<?php
class ItemPedidoExame extends AppModel {

	public $name		   	= 'ItemPedidoExame';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'itens_pedidos_exames';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_itens_pedidos_exames'));
	public $recursive 		= -1;

	public $belongsTo = array(
		'PedidoExame' => array(
			'className'    => 'PedidoExame',
			'foreignKey'    => 'codigo_pedidos_exames'
			),
		'Fornecedor' => array(
			'className'    => 'Fornecedor',
			'foreignKey'    => 'codigo_fornecedor'
			),
		);


	/**
	 * [getFornecedorCusto description]
	 * 
	 * metodo para buscar o codigo do custo do exame que está sendo pesquisado para o fornecedor especifico
	 * 
	 * @param  [type] $codigo_fornecedor [description]
	 * @param  [type] $codigo_exame      [description]
	 * @return [type]                    [description]
	 */
	public function getFornecedorCusto($codigo_fornecedor,$codigo_exame) 
	{
		//instancia as models necessárias
		$this->Fornecedor 	=& ClassRegistry::init('Fornecedor');

		//monta os fields
		$fields = array('ListaDePrecoProdutoServico.valor AS valor_custo');

		//monta os joins
		$joins = array(
			array(
                'table' => 'Rhhealth.dbo.listas_de_preco',
                'alias' => 'ListaDePreco',
                'type' => 'INNER',
                'conditions' => 'ListaDePreco.codigo_fornecedor = Fornecedor.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.listas_de_preco_produto',
                'alias' => 'ListaDePrecoProduto',
                'type' => 'INNER',
                'conditions' => 'ListaDePrecoProduto.codigo_lista_de_preco = ListaDePreco.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.listas_de_preco_produto_servico',
                'alias' => 'ListaDePrecoProdutoServico',
                'type' => 'INNER',
                'conditions' => 'ListaDePrecoProdutoServico.codigo_lista_de_preco_produto = ListaDePrecoProduto.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.exames',
                'alias' => 'Exame',
                'type' => 'INNER',
                'conditions' => 'Exame.codigo_servico = ListaDePrecoProdutoServico.codigo_servico',
            ),
		);

		//executa a query
		$dados = $this->Fornecedor->find('first', 
			array(
				'fields' 		=> $fields,
				'joins' 		=> $joins,
				'conditions' 	=> array (
					'Fornecedor.codigo' => $codigo_fornecedor,
					'Exame.codigo' => $codigo_exame
				),
				'recursive' => -1
			));

		//variavel auxiliar
		$valor_custo = '0.00';
		if(!empty($dados)) {
			$valor_custo = $dados[0]['valor_custo'];
		}

		return $valor_custo;

	}//fim get_fornecedor

	/**
	 * [getCodigoServico description]
	 * 
	 * metodo para buscar o codigo do exame que está sendo pesquisado
	 * 
	 * @param  [type] $codigo_exame      [description]
	 * @return [type]                    [description]
	 */
	public function getCodigoServico($codigo_exame) 
	{
		//instancia as models necessárias
		$this->Exame 	=& ClassRegistry::init('Exame');

		//monta os fields
		$fields = array('Exame.codigo_servico');

		//executa a query
		$dados = $this->Exame->find('first', 
			array(
				'fields' 		=> $fields,
				'conditions' 	=> array (
					'Exame.codigo' => $codigo_exame
				),
				'recursive' => -1
			));

		$codigo_servico = null;
		if(!empty($dados)) {
			$codigo_servico = $dados['Exame']['codigo_servico'];
		}

		return $codigo_servico;

	}//fim get_fornecedor

	public function get_itens_pedido_exame($codigo_item){
		//fields
        $fields = array(
            'ItemPedidoExame.codigo',
            'ItemPedidoExame.codigo_exame',
            'ItemPedidoExame.data_realizacao_exame',
            'ItemPedidoExame.compareceu',
            'ItemPedidoExameBaixa.codigo',
            'ItemPedidoExameBaixa.data_realizacao_exame',
            'ItemPedidoExameBaixa.descricao',
            'ItemPedidoExameBaixa.resultado',
            'FichaClinica.codigo',
            'FichaClinica.parecer',
            'FichaClinica.codigo_medico',
            'PedidoExame.codigo',
            'PedidoExame.codigo_status_pedidos_exames',
        );
        //joins
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.itens_pedidos_exames_baixa',
                'alias' => 'ItemPedidoExameBaixa',
                'type' => 'LEFT',
                'conditions' => 'ItemPedidoExame.codigo = ItemPedidoExameBaixa.codigo_itens_pedidos_exames',
            ),
            array(
                'table' => 'Rhhealth.dbo.fichas_clinicas',
                'alias' => 'FichaClinica',
                'type' => 'LEFT',
                'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = FichaClinica.codigo_pedido_exame',
            ),
            array(
                'table' => 'Rhhealth.dbo.pedidos_exames',
                'alias' => 'PedidoExame',
                'type' => 'LEFT',
                'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
            )
        );

        //busca os itens do pedido
        $dados = $this->find('first',array('conditions' => array('ItemPedidoExame.codigo' => $codigo_item),'fields' => $fields,'joins' => $joins));

        return $dados;
	}

	public function get_pedido_modal_pedido_data($codigo_item_pedido){
		$Configuracao = &ClassRegistry::init('Configuracao');
		//fields
        $fields = array(
            'PedidoExame.codigo',
            'Exame.descricao',
            'Exame.codigo',
            'Cliente.razao_social',
            'ItemPedidoExame.data_realizacao_exame',
            'ItemPedidoExame.codigo',
            'ItemPedidoExame.compareceu',
            'ItemPedidoExameBaixa.data_realizacao_exame',
            'ItemPedidoExameBaixa.resultado',
            'ItemPedidoExameBaixa.descricao',
            'FichaClinica.codigo',
            'FichaClinica.parecer',
            'FichaClinica.codigo_medico',
        );
        //joins
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.itens_pedidos_exames_baixa',
                'alias' => 'ItemPedidoExameBaixa',
                'type' => 'LEFT',
                'conditions' => 'ItemPedidoExame.codigo = ItemPedidoExameBaixa.codigo_itens_pedidos_exames',
            ),
            array(
                'table' => 'Rhhealth.dbo.pedidos_exames',
                'alias' => 'PedidoExame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.exames',
                'alias' => 'Exame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'PedidoExame.codigo_cliente = Cliente.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.fichas_clinicas',
                'alias' => 'FichaClinica',
                'type' => 'LEFT',
                'conditions' => 'PedidoExame.codigo = FichaClinica.codigo_pedido_exame AND ItemPedidoExame.codigo_exame = '.$Configuracao->getChave('INSERE_EXAME_CLINICO')
            )
        );
        //busca os itens para a modal
        $get_pedido = $this->find('first', array('conditions' => array('ItemPedidoExame.codigo' => $codigo_item_pedido),'joins' => $joins,'fields' => $fields));
        return $get_pedido;
	}

	public function getItensAgenda($codigo_pedido){


		$fields = array(
			'ItemPedidoExame.codigo',
			'ItemPedidoExame.codigo_exame',
			'ItemPedidoExame.codigo_fornecedor',
			'ItemPedidoExame.tipo_atendimento',
			'AgendamentoExame.codigo',
			'AgendamentoExame.hora',
			'AgendamentoExame.data',
			'AgendamentoExame.codigo_fornecedor',
		);

		$joins = array(
			array(
				'table' => 'Rhhealth.dbo.agendamento_exames',
                'alias' => 'AgendamentoExame',
                'type' => 'LEFT',
                'conditions' => 'AgendamentoExame.codigo_itens_pedidos_exames = ItemPedidoExame.codigo'
			)
		);

		return $this->find('all', array('conditions' => array('codigo_pedidos_exames' => $codigo_pedido), 'fields' =>  $fields, 'joins' => $joins));
	}
	public function getFornecedoresPorCodItem($codigo_item_pedido_exame){
		
		$dados = array();

		$fields = array(
			'ItemPedidoExame.codigo_exame',
			'ItemPedidoExame.codigo_fornecedor',
			'ItemPedidoExame.codigo_pedidos_exames',
			'ItemPedidoExame.codigo_exame',
			'ItemPedidoExame.valor',
			'Fornecedor.ambulatorio', 
			'Fornecedor.prestador_particular',
			'Fornecedor.codigo',
		);

		$joins = array(
			array(
				'table' => 'Rhhealth.dbo.fornecedores',
                'alias' => 'Fornecedor',
                'type' => 'INNER',
                'conditions' => 'Fornecedor.codigo = ItemPedidoExame.codigo_fornecedor'
			)
		);

		$conditions = array(
			'ItemPedidoExame.codigo' => $codigo_item_pedido_exame
		);

		$dados = $this->find('first', array('conditions' => $conditions, 'fields' => $fields, 'joins' => $joins));

		return $dados;
	}
}