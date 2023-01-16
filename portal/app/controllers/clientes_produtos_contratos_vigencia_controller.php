<?php
class ClientesProdutosContratosVigenciaController extends AppController {
	public $name = 'ClientesProdutosContratosVigencia';
    public $components = array('Filtros', 'RequestHandler');
    public $helpers = array('Html', 'Ajax');
    public $uses = array('ClienteProdutoContrato');

    public function index(){
        $this->pageTitle = 'Vigência de Contratos';
        $this->carregar_combos();
        $this->data['ClienteProdutoContrato'] = $this->Filtros->controla_sessao($this->data, $this->ClienteProdutoContrato->name);
    }

    public function carregar_combos(){
        $this->Produto = ClassRegistry::init('Produto');
        $dados         = $this->Produto->find('all',array('fields' => 'Produto.descricao,Produto.codigo','order' => 'Produto.codigo'));
        foreach($dados as $produto){
        	$produtos[$produto['Produto']['codigo']] = $produto['Produto']['descricao'];
        }
        $this->set(compact('produtos'));
    }

    public function listagem(){
    	$this->layout = 'ajax';

    	$this->ClienteProdutoContrato = ClassRegistry::init('ClienteProdutoContrato');
    	$this->ClienteProduto 		  = ClassRegistry::init('ClienteProduto');

    	$this->data['ClienteProdutoContrato'] = $this->Filtros->controla_sessao($this->data, $this->ClienteProdutoContrato->name);
		$filtros 							  = $this->data['ClienteProdutoContrato'];
        $conditions         = $this->ClienteProdutoContrato->converteFiltroEmCondition($filtros);
    	$total = $this->ClienteProdutoContrato->find('all',array('fields' => 'ClienteProdutoContrato.codigo'));

    	$this->paginate['ClienteProdutoContrato'] = array(
            'order'  	 => 'ClienteProduto.codigo_cliente',
            'conditions' => $conditions,
            'limit'	 	 => 50
        );

        $contratos = $this->paginate('ClienteProdutoContrato');
        $this->set(compact('contratos','total'));
    }
}
?>