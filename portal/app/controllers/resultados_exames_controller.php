<?php
class ResultadosExamesController extends AppController {
	public $name = 'ResultadosExames';

	public function index() {
		$this->pageTitle = 'Resultado de exames';

		$this->data['PedidoExame'] = $this->Filtros->controla_sessao($this->data, $this->PedidoExame->name);
	}

	var $uses = array(
		'PedidoExame',
		'ItemPedidoExame',
		'StatusPedidoExame',
		'ItemPedidoExameBaixa',
		'ClienteFuncionario',
		'Cliente',
		'Funcionario',
		'TipoExamePedido',
		'Exame',
		'Fornecedor',
		'ResultadoExame'
		);


	public function listagem() {

		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, $this->ResultadoExame->name);
		$conditions = $this->ResultadoExame->converteFiltroEmConditionBaixa($filtros);

		$this->PedidoExame->virtualFields = array(
			'count' => '
			SELECT count(pe.codigo) count
			FROM pedidos_exames pe, cliente_funcionario cf, cliente cli,
			funcionarios fun, setores s, cargos c,
			grupos_economicos_clientes gec, grupos_economicos ge,
			itens_pedidos_exames ipe, itens_pedidos_exames_baixa ipeb,
			aparelhos_audiometricos aa
			WHERE pe.codigo = PedidoExame.codigo AND
			pe.codigo_cliente_funcionario=cf.codigo AND
			cf.codigo_cliente = cli.codigo AND
			cf.codigo_funcionario=fun.codigo AND
			cf.codigo_setor=s.codigo AND
			cf.codigo_cargo=c.codigo AND
			cf.codigo_cliente=gec.codigo_cliente AND
			gec.codigo_grupo_economico = ge.codigo AND
			pe.codigo = ipe.codigo_pedidos_exames AND
			ipe.codigo = ipeb.codigo_itens_pedidos_exames AND
			ipeb.codigo_aparelho_audiometrico = aa.codigo'
			);

		$fields = array(
			'PedidoExame.codigo',
			'PedidoExame.count',
			'StatusPedidoExame.codigo',
			'StatusPedidoExame.descricao',
			'Cliente.razao_social',
			'Funcionario.nome',      	
			);

		$joins  = array(
			array(
				'table' => 'status_pedidos_exames',
				'alias' => 'StatusPedidoExame',
				'type' => 'LEFT',
				'conditions' => 'StatusPedidoExame.codigo = PedidoExame.codigo_status_pedidos_exames',
				),
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'LEFT',
				'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario',
				),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'LEFT',
				'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente',
				),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'LEFT',
				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
				),       	
			);  

		$order = array('PedidoExame.codigo DESC');

		$this->paginate['PedidoExame'] = array(
			'fields' => $fields,
			'conditions' => $conditions,
			'joins' => $joins,
			'limit' => 50,
			'order' => $order,
			);
		$resultado_exames = $this->paginate('PedidoExame');

		$this->set(compact('resultado_exames'));
	}

	public function imprimir_relatorio($codigo_pedido_exame) {
		$this->__jasperConsulta( $codigo_pedido_exame );
	}
	
	private function __jasperConsulta( $codigo_pedido_exame ) {
		
		// opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME'=>'/reports/RHHealth/audiometria', // especificar qual relatório
			'FILE_NAME'=> basename( 'relatorio_audiometria.pdf' ) // nome do relatório para saida
		);

		// parametros do relatorio
		$parametros = array( 'CODIGO_PEDIDO_EXAME' => $codigo_pedido_exame );

		$this->loadModel('Cliente');
		$parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);	

		try {
			
			// envia dados ao componente para gerar
			$url = $this->Jasper->generate( $parametros, $opcoes );	

			if($url){
				// se obter retorno apresenta usando cabeçalho apropriado
				header(sprintf('Content-Disposition: attachment; filename="%s"', $opcoes['FILE_NAME']));
				header('Pragma: no-cache');
				header('Content-type: application/pdf');
				echo $url; exit;
			}

		} catch (Exception $e) {
			// se ocorreu erro
			debug($e); exit;
		}		

		exit;
		
	}

}