<?php
App::import('Model', 'ClienteProdutoDesconto');
class ClientesProdutosDescontosController extends AppController {

	
    public $name = 'ClientesProdutosDescontos';
    public $layout = 'cliente';
    public $uses = array('Cliente', 'Produto', 'ClienteProdutoDesconto', 'Pedido');

    public function index() {
        $this->pageTitle = "Clientes Produtos Descontos Mês Operação";
        $this->data['ClienteProdutoDesconto'] = $this->Filtros->controla_sessao($this->data, $this->ClienteProdutoDesconto->name);
        $this->set(compact('produtos', 'status_contrato', 'status_produto'));
    }

    public function gerenciar($codigo_cliente) {
        $this->pageTitle = 'Gerenciar Descontos';

        $descontos_modificado = '';
        $descontos = array();

        $cliente    = $this->Cliente->carregar($codigo_cliente);
        $descontos  = $this->ClienteProdutoDesconto->descontosDoCliente($codigo_cliente);
        $pedidos    = $this->Pedido->getPedidosClientePagador($codigo_cliente);

        foreach($descontos as $desconto){
            $desconto['Pedido'] = $pedidos;    

            $descontos_modificado[] = $desconto;
        }
        
        if( $descontos_modificado ) $descontos = $descontos_modificado;

        $this->set(compact('codigo_cliente', 'descontos'));
    }
    
    function incluir() {
        $this->pageTitle = "Clientes Produtos Descontos Mês Operação";
        $Produto = ClassRegistry::init('Produto');
        if(!empty($this->data)) {
            if($this->ClienteProdutoDesconto->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'gerenciar', $this->data['ClienteProdutoDesconto']['codigo_cliente']));
            } else {
                $this->BSession->setFlash('save_error');
            }
            $codigo_cliente = $this->data['ClienteProdutoDesconto']['codigo_cliente'];
        } else {
            $codigo_cliente = $this->passedArgs[0];
        }
        $conditions= array('Produto.codigo_naveg <>' => '', 'Produto.codigo_naveg NOT' => null, 'Produto.ativo' => 1);
        $produtos = $Produto->listar('list', $conditions);
        $this->set('meses', Comum::listMeses());
        $this->set(compact('produtos', 'codigo_cliente'));
    }
    
    function excluir($codigo) {
        if ($this->ClienteProdutoDesconto->excluir($codigo))
            $this->BSession->setFlash('delete_success');
        else
            $this->BSession->setFlash('delete_error');
        $this->redirect(array('action' => 'index'));
    }

    function consulta(){
        $this->pageTitle = 'Descontos Concedidos';
        $this->data['ClienteProdutoDesconto'] = $this->Filtros->controla_sessao($this->data, $this->ClienteProdutoDesconto->name);
    }

    function consulta_listagem(){
        
        $filtros['ClienteProdutoDesconto'] = $this->Filtros->controla_sessao($this->data, $this->ClienteProdutoDesconto->name);
        $conditions = $this->ClienteProdutoDesconto->converterFiltrosEmConditions($filtros);

        if( !empty($filtros['ClienteProdutoDesconto']) ) {
            $dados = $this->ClienteProdutoDesconto->carregarDescontoPorPeriodo($conditions);
            $this->set(compact('dados'));
        }

    }

}