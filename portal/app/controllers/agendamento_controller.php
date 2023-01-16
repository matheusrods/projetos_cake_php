<?php
class AgendamentoController extends AppController
{
	public $name = 'Agendamento';
	public $helpers = array('BForm', 'Html', 'Ajax');

	var $uses = array(
		'PedidoExame',
		'Cliente',
		'Funcionario',
		'ClienteFuncionario',
		'Fornecedor',
		'ItemPedidoExame',
		'AgendamentoSugestao',
		'AgendamentoExame',
		'FornecedorContato',
		'PedidoLote'
	);

	public function beforeFilter()
	{
		parent::beforeFilter();
		// $this->BAuth->allow(array('consulta_disponibilidade_notificacao', 'box_dados_cliente_funcionario', 'box_itens', 'busca_telefone', 'mostra_notificacao'));
	}

	public function index()
	{
		$this->pageTitle = 'Agendamento de Exames - Sugestões do Cliente';
		$this->data['AgendamentoSugestao'] = $this->Filtros->controla_sessao($this->data, 'AgendamentoSugestao');
	}

	public function fila()
	{
		$this->pageTitle = 'Agendamento de Sugestões de Exames';
		$this->data['AgendamentoSugestao'] = $this->Filtros->controla_sessao($this->data, 'AgendamentoSugestao');

		$pendente_agendamento = isset($this->data['AgendamentoSugestao']['agendamento']) && !$this->data['AgendamentoSugestao']['agendamento'] ? false : true;
		$this->set(compact('pendente_agendamento'));
	}

	public function listagem()
	{

		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, 'AgendamentoSugestao');

		$conditions = $this->AgendamentoSugestao->converteFiltroEmCondition($filtros);

		$conditions[] = array(' ItemPedidoExame.codigo IN (SELECT
						            FILA.codigo_itens_pedidos_exames
						        FROM
						            agendamento_sugestoes AS FILA
						        WHERE
						            FILA.codigo_itens_pedidos_exames = ItemPedidoExame.codigo)');

		$conditions[] = array('PedidoExame.em_emissao IS NULL');

		$fields = array(
			'Cliente.razao_social',
			'Cliente.nome_fantasia',
			'ClienteFuncionario.codigo',
			'Funcionario.nome',
			'PedidoExame.data_inclusao',
			'PedidoExame.codigo',
			'PedidoExame.codigo_func_setor_cargo',
			'ItemPedidoExame.codigo',
			'ItemPedidoExame.data_agendamento',
			'ItemPedidoExame.hora_agendamento',
			'Exame.codigo',
			'Exame.codigo_servico',
			'Exame.descricao',
			'Exame.codigo_servico',
			'Fornecedor.codigo',
			'Fornecedor.razao_social',
			'Fornecedor.utiliza_sistema_agendamento',
			'CASE
               	 	WHEN PedidoExame.exame_admissional > 0 THEN \'Exame admissional\'
                	WHEN PedidoExame.exame_periodico > 0 THEN \'Exame periódico\'
                	WHEN PedidoExame.exame_demissional > 0 THEN \'Exame demissional\'
                	WHEN PedidoExame.exame_retorno > 0 THEN \'Retorno ao trabalho\'
                    WHEN PedidoExame.exame_mudanca > 0 THEN \'Mudança de riscos ocupacionais\'
                	WHEN PedidoExame.exame_monitoracao > 0 THEN \'Monitoração pontual\'
                	ELSE \'\'
            	END AS tipo_exame',
			'ItemPedidoExame.sugestoes',
			'(select count(1) from agendamento_sugestoes ASU where ASU.codigo_itens_pedidos_exames = ItemPedidoExame.codigo) as qtd_sugestoes'
		);

		$joins  = array(
			array(
				'table' => 'pedidos_exames',
				'alias' => 'PedidoExame',
				'type' => 'INNER',
				'conditions' => 'PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames',
			),
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario',
			),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente',
			),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
			),
			array(
				'table' => 'exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'Exame.codigo = ItemPedidoExame.codigo_exame',
			),
			array(
				'table' => 'fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => 'Fornecedor.codigo = ItemPedidoExame.codigo_fornecedor',
			)
		);
		// CDCT-678
		$codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];	
		
		if(isset($codigo_empresa)){
			$joins[2]['conditions'] .= ' AND Cliente.codigo_empresa = '.$codigo_empresa;
		}

		$order = array('PedidoExame.codigo DESC');

		$this->ItemPedidoExame->virtualFields = array(
			'sugestoes' => 'CAST(LTRIM(stuff((SELECT \' | \' + CONCAT(CONVERT(VARCHAR(10), data_sugerida, 103), \' \', hora_sugerida)
					            FROM agendamento_sugestoes ags
            					WHERE ags.codigo_itens_pedidos_exames = ItemPedidoExame.codigo  FOR xml PATH(\'\')),2,2,\'\')) as varchar(60))'
		);

		//         $resultados = $this->ItemPedidoExame->find('all',
		//         	array(
		//         		'fields' => $fields,
		//         		'conditions' => $conditions,
		//         		'joins' => $joins,
		//         		'limit' => 50,
		//         		'order' => $order,
		//         	)
		//         );

		//         pr($resultados);
		//         exit;


		$this->paginate['ItemPedidoExame'] = array(
			'fields' => $fields,
			'conditions' => $conditions,
			'joins' => $joins,
			'limit' => 50,
			'order' => $order,
			'recursive' => -1,
		);

		$sugestoes = $this->paginate('ItemPedidoExame');
		$this->set(compact('sugestoes'));
	}


	public function listagem_old()
	{

		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, 'AgendamentoSugestao');

		pr($filtros);

		$conditions = $this->AgendamentoSugestao->converteFiltroEmCondition($filtros);
		$conditions[] = array(' PedidoExame.codigo IN (select 
								    I2.codigo_pedidos_exames
								FROM 
								    agendamento_sugestoes A2
								    inner join itens_pedidos_exames I2 ON (I2.codigo = A2.codigo_itens_pedidos_exames))');

		$fields = array(
			'Cliente.razao_social',
			'Cliente.nome_fantasia',
			'ClienteFuncionario.codigo',
			'Funcionario.nome',
			'PedidoExame.data_inclusao',
			'PedidoExame.codigo',
			'CASE 
                WHEN PedidoExame.exame_admissional > 0 THEN \'Exame admissional\'
                WHEN PedidoExame.exame_periodico > 0 THEN \'Exame periódico\'
                WHEN PedidoExame.exame_demissional > 0 THEN \'Exame demissional\'
                WHEN PedidoExame.exame_retorno > 0 THEN \'Retorno ao trabalho\'
                WHEN PedidoExame.exame_mudanca > 0 THEN \'Mudança de riscos ocupacionais\'
                ELSE \'\'
            END AS tipo_exame
            '
		);

		$joins  = array(
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario',
			),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente',
			),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
			)
		);

		$order = array('PedidoExame.codigo DESC');

		//         $resultados = $this->PedidoExame->find('all',
		//         	array(
		//         		'fields' => $fields,
		//         		'conditions' => $conditions,
		//         		'joins' => $joins,
		//         		'limit' => 50,
		//         		'order' => $order,
		//         	)
		//         );

		$this->paginate['PedidoExame'] = array(
			'fields' => $fields,
			'conditions' => $conditions,
			'joins' => $joins,
			'limit' => 10,
			'order' => $order,
			'recursive' => -1,
		);

		$sugestoes = $this->paginate('PedidoExame');
		$this->set(compact('sugestoes'));
	}

	public function box_itens($id_pedido)
	{

		$this->layout = false;

		$options['fields'] = array(
			'AgendamentoSugestao.codigo',
			'AgendamentoSugestao.data_sugerida',
			'AgendamentoSugestao.hora_sugerida',
			'Fornecedor.codigo',
			'Fornecedor.razao_social',
			'Fornecedor.nome',
			'Exame.codigo',
			'Exame.codigo_servico',
			'Exame.descricao',
			'ItemPedidoExame.codigo',
			'ItemPedidoExame.data_agendamento',
			'ItemPedidoExame.hora_agendamento',
			'ItemPedidoExame.tipo_agendamento',
			'PedidoExame.codigo',
			'PedidoExame.codigo_cliente_funcionario'
		);


		$options['joins'] = array(
			array(
				'table' => 'itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo = AgendamentoSugestao.codigo_itens_pedidos_exames',
			),
			array(
				'table' => 'exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'Exame.codigo = ItemPedidoExame.codigo_exame',
			),
			array(
				'table' => 'pedidos_exames',
				'alias' => 'PedidoExame',
				'type' => 'INNER',
				'conditions' => 'PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames',
			),
			array(
				'table' => 'fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => 'Fornecedor.codigo = ItemPedidoExame.codigo_fornecedor',
			)
		);

		$options['conditions'][] = array('PedidoExame.codigo' => $id_pedido);
		$itens_pedido = $this->AgendamentoSugestao->find('all', $options);

		$organizze = array();
		$desabilita = 0;
		foreach ($itens_pedido as $key => $item) {

			$contatos = $this->FornecedorContato->find('all', array('conditions' => array('codigo_fornecedor' => $item['Fornecedor']['codigo'], 'codigo_tipo_retorno' => FornecedorContato::TIPO_TELEFONE)));

			if (isset($contatos[0])) {
				$organizze[$item['ItemPedidoExame']['codigo']]['contato'] = array(
					'descricao' => $contatos[0]['TipoRetorno']['descricao'] . " " . $contatos[0]['TipoContato']['descricao'],
					'numero' => ($contatos[0]['FornecedorContato']['ddd'] ? "(" . $contatos[0]['FornecedorContato']['ddd'] . ")" : "") . " " . $contatos[0]['FornecedorContato']['descricao'],
				);
			}

			$organizze[$item['ItemPedidoExame']['codigo']]['dados'] =  $item;
			$organizze[$item['ItemPedidoExame']['codigo']]['sugestoes'][] =  $item['AgendamentoSugestao'];

			if (empty($item['ItemPedidoExame']['data_agendamento']) && $item['ItemPedidoExame']['tipo_agendamento'] == '1') {
				$desabilita = 1;
			}
		}

		$this->set('itens_pedido', $organizze);
		$this->set('desabilita', $desabilita);
	}

	public function box_dados_cliente_funcionario($codigo_funcionario_setor_cargo)
	{
		$dados_cliente_funcionario = $this->PedidoExame->retornaEstrutura($codigo_funcionario_setor_cargo);
		$this->set(compact('dados_cliente_funcionario'));
	}

	public function busca_agenda()
	{
		// Oque seria isso?
	}

	public function grava_agenda()
	{

		$data_agendamento = $this->params['form']['data_agendamento'];
		$hora_agendamento = $this->params['form']['hora_agendamento'];
		$codigo_item_pedido = $this->params['form']['codigo_item_pedido'];

		if ($data_agendamento && $hora_agendamento) {

			$array_data = explode("-", $data_agendamento);
			$data_agendamento = $array_data[2] . "-" . $array_data[1] . "-" . $array_data[0];

			$item = $this->ItemPedidoExame->read(null, $codigo_item_pedido);

			$item['ItemPedidoExame']['data_agendamento'] = $data_agendamento;
			$item['ItemPedidoExame']['hora_agendamento'] = $hora_agendamento;

			$this->ItemPedidoExame->query('begin transaction');
			try {

				if ($this->ItemPedidoExame->atualizar($item)) {
					$agendamento_exame = $this->AgendamentoExame->find("first", array('conditions' => array('codigo_itens_pedidos_exames' => $item['ItemPedidoExame']['codigo'])));

					if ($agendamento_exame) {

						$agendamento_exame['AgendamentoExame']['data'] = $data_agendamento;
						$agendamento_exame['AgendamentoExame']['hora'] = (int) str_replace(":", "", $hora_agendamento);

						$pedido = $this->PedidoExame->read(null, $item['ItemPedidoExame']['codigo_pedidos_exames']);
						$pedido['PedidoExame']['data_notificacao'] = null;

						if ($this->PedidoExame->atualizar($pedido)) {
							if ($this->AgendamentoExame->atualizar($agendamento_exame)) {
								print "1";
							} else {
								print "0";
							}
						} else {
							print "0";
						}
					} else {

						if ($this->AgendamentoExame->incluir(array(
							'data' => $data_agendamento,
							'hora' => (int) str_replace(":", "", $hora_agendamento),
							'codigo_fornecedor' => $item['ItemPedidoExame']['codigo_fornecedor'],
							'codigo_itens_pedidos_exames' => $item['ItemPedidoExame']['codigo'],
							'ativo' => '1',
							'codigo_lista_de_preco_produto_servico' => NULL
						))) {
							print "1";
						} else {
							print "0";
						}
					}
				} else {
					print "0";
				}

				$this->ItemPedidoExame->commit();
			} catch (Exception $e) {
				$this->ItemPedidoExame->rollback();
				print "0";
			}
		} else {
			print "0";
		}

		exit;
	}

	public function muda_status_pedido()
	{
		// Oque seria isso?
	}

	public function consulta_disponibilidade_notificacao()
	{

		$id_pedido = $this->params['form']['codigo_pedido'];
		$flag_todos_agendados = 1;

		$dados_pedido = $this->PedidoExame->read(null, $id_pedido);

		if ($dados_pedido['PedidoExame']['codigo_pedidos_lote']) {
			$dados_pedido_lote = $this->PedidoLote->read(null, $dados_pedido['PedidoExame']['codigo_pedidos_lote']);
			$options['conditions'][] = array('PedidoExame.codigo_pedidos_lote' => $dados_pedido_lote['PedidoLote']['codigo']);
		} else {
			$options['conditions'][] = array('PedidoExame.codigo' => $id_pedido);
		}

		$options['joins'] = array(
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario',
			),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
			),
		);

		$options['fields'] = array('Funcionario.nome', 'PedidoExame.codigo', 'PedidoExame.codigo_cliente_funcionario');

		$lote_pedidos = $this->PedidoExame->find('all', $options);

		foreach ($lote_pedidos as $lote) {
			foreach ($this->ItemPedidoExame->find('all', array('conditions' => array('codigo_pedidos_exames' => $lote['PedidoExame']['codigo'], 'tipo_agendamento' => '1'))) as $key => $item) {
				if ((empty($item['ItemPedidoExame']['data_agendamento']) || is_null($item['ItemPedidoExame']['data_agendamento']))) {
					$flag_todos_agendados = 0;
				}
			}
		}

		print $flag_todos_agendados;
		exit;
	}

	public function mostra_notificacao()
	{

		$codigo_pedido = $this->params['form']['codigo_pedido'];
		$dados_pedido = $this->PedidoExame->read(null, $codigo_pedido);

		if (is_null($dados_pedido['PedidoExame']['codigo_pedidos_lote'])) {
			$options['conditions'][] = array('PedidoExame.codigo' => $codigo_pedido);
		} else {
			$dados_pedido_lote = $this->PedidoLote->read(null, $dados_pedido['PedidoExame']['codigo_pedidos_lote']);
			$options['conditions'][] = array('PedidoExame.codigo_pedidos_lote' => $dados_pedido_lote['PedidoLote']['codigo']);
		}

		$options['joins'] = array(
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario',
			),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
			),
		);

		$options['fields'] = array('Funcionario.nome', 'PedidoExame.codigo', 'PedidoExame.codigo_func_setor_cargo', 'PedidoExame.codigo_cliente_funcionario');
		$lote_pedidos = $this->PedidoExame->find('all', $options);

		$this->log($lote_pedidos, 'debug');
		$this->set(compact('lote_pedidos'));
	}


	public function busca_telefone($codigo_fornecedor)
	{

		$contatos = $this->FornecedorContato->find('all', array('conditions' => array('codigo_fornecedor' => $codigo_fornecedor, 'codigo_tipo_contato' => '2', 'codigo_tipo_retorno' => FornecedorContato::TIPO_TELEFONE)));
		if (isset($contatos[0])) {
			echo "Telefone: (" . $contatos[0]['FornecedorContato']['ddd'] . ") " . $contatos[0]['FornecedorContato']['descricao'];
		}
		exit;
	}
}
