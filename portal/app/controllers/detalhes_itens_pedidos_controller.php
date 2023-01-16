<?php
class DetalhesItensPedidosController extends AppController {
	
	public $name = 'DetalhesItensPedidos';
	
	var $uses = array(
		'Pedido',
		'ItemPedido',
		'Cliente',
		'DetalheItemPedido',
		'DetalheItemPedidoManual',
		'Gestor',
		'Corretora',
		'Seguradora',
		'AvulsoPedido',
		'FrotaPedido',
		'EnderecoRegiao'
	);

    public function index(){
		$this->pageTitle = 'Faturamento Analítico BuonnySat';
		$this->data['DetalheItemPedido'] = $this->Filtros->controla_sessao($this->data, 'DetalheItemPedido');
		$this->carregarCombos();
	}

	public function carregarCombos(){
		$meses = Comum::listMeses();
		$mes_atual = Date('m');
		$anos = Comum::listAnos();
		$ano_atual = Date('Y'); 
		$regioes = $this->EnderecoRegiao->listarRegioes();
		$corretoras = $this->Corretora->listarCorretorasAtivas();
		$seguradoras = $this->Seguradora->listarSeguradorasAtivas();
		$gestores = $this->Gestor->listarNomesGestoresAtivos();

		$this->set(compact('regioes','corretoras','seguradoras','gestores','meses','anos','ano_atual','mes_atual'));
	}

	public function listagem(){
		$this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'DetalheItemPedido');
        $conditions = $this->DetalheItemPedido->converteFiltrosEmConditions($filtros);
        $detalhesItensPedidos = $this->DetalheItemPedido->listarFaturamentoAnalitico($conditions);
        $this->set(compact('detalhesItensPedidos'));
	}   
}
?>