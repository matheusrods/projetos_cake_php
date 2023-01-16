<?php
class ListasDePrecoProdutoController extends AppController {
    public $name = 'ListasDePrecoProduto';
    var $uses = array('ListaDePreco', 'ListaDePrecoProduto', 'Produto', 'Servico', 'ProdutoServico','ListaDePrecoProdutoServico');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('index_salvar','index'));
    }

    public function index($codigo_lista_de_preco) {
        $this->pageTitle = 'Produtos da Lista de Preço';
        $lista_de_preco = $this->ListaDePreco->carregar($codigo_lista_de_preco);
        $produtos = $this->ListaDePrecoProduto->listarPorCodigoListaDePreco($codigo_lista_de_preco);
        $this->set(compact('lista_de_preco', 'produtos','codigo_lista_de_preco'));
    }

    public function listar_servicos_por_produto($codigo_produto, $codigo_corretora = null, $codigo_seguradora = null) {
        $this->loadModel('ListaDePrecoProduto');
        $listaDePreco = $this->ListaDePrecoProduto->listarPorCodigoProduto($codigo_produto, null, $codigo_corretora, $codigo_seguradora);
        $servicos = $listaDePreco['ListaDePrecoProdutoServico'];
        $this->set(compact('servicos'));
    }

    public function index_salvar($codigo_lista_de_preco){
        //seta que não vai ter layout ctp
        $this->layout = false;
        //post
        if($this->RequestHandler->isPost()){

            $dados = $this->data;

            foreach ($dados as $lista) {
                # code...
                //buscar o servicos
                $find = $this->ListaDePrecoProdutoServico->carregar($lista['codigo']);

                //carrega campos para atualizar
                $find['Produto']                                          = $find['ListaDePrecoProduto']['Produto'];
                $find['ListaDePrecoProdutoServico']['tipo_premio_minimo'] = ($find['ListaDePrecoProduto']['valor_premio_minimo'] > 0 ? 1 : 2);
                
                if ($find['ListaDePrecoProdutoServico']['tipo_premio_minimo'] == 1) {
                    $find['ListaDePrecoProdutoServico']['valor_premio_minimo'] = $find['ListaDePrecoProduto']['valor_premio_minimo'];
                    $find['ListaDePrecoProdutoServico']['qtd_premio_minimo']   = $find['ListaDePrecoProduto']['qtd_premio_minimo'];
                }

                if($find['ListaDePrecoProdutoServico']['tipo_premio_minimo'] == ListaDePrecoProdutoServico::TIPO_PREMIO_MINIMO_PRODUTO) {
                    $find['ListaDePrecoProduto']['valor_premio_minimo'] = $find['ListaDePrecoProdutoServico']['valor_premio_minimo'];
                    $find['ListaDePrecoProduto']['qtd_premio_minimo'] = $find['ListaDePrecoProdutoServico']['qtd_premio_minimo'];
                    $find['ListaDePrecoProdutoServico']['valor_premio_minimo'] = 0;
                    $find['ListaDePrecoProdutoServico']['qtd_premio_minimo']   = 0;
                }
                
                $find['ListaDePrecoProdutoServico']['tem_controle_de_volume'] = $find['Produto']['controla_volume'];
                $find['ListaDePrecoProdutoServico']['valor'] = $lista['valor'];
                $find['ListaDePrecoProdutoServico']['valor_maximo'] = $lista['valor_maximo'];
                $find['ListaDePrecoProdutoServico']['valor_venda'] = $lista['valor_venda'];
                $find['ListaDePrecoProdutoServico']['tipo_atendimento'] = $lista['tipo_atendimento'];

                $ldps = array();
                $ldp = array();

                $ldps['ListaDePrecoProdutoServico'] = $find['ListaDePrecoProdutoServico'];
                $ldp['ListaDePrecoProduto'] = $find['ListaDePrecoProduto'];

                $this->ListaDePrecoProdutoServico->validate = array();// validate esta vazio pq estava atrapalhando na atualizacao, porem ele ja é feito na inclusao.

                // //se existir registro faz a atualizacao
                if(!empty($find)){
                    if(!$this->ListaDePrecoProdutoServico->atualizar($ldps)){
                        $this->BSession->setFlash(array('alert alert-error', 'Error ao gravar serviço.'));
                        $this->redirect(array('controller' => 'listas_de_preco_produto', 'action' => 'index', $codigo_lista_de_preco));
                    } else if(!$this->ListaDePrecoProduto->atualizar($ldp)){
                        $this->BSession->setFlash(array('alert alert-error', 'Error ao gravar produto.'));
                        $this->redirect(array('controller' => 'listas_de_preco_produto', 'action' => 'index', $codigo_lista_de_preco));
                    } else {
                        $this->BSession->setFlash('save_success');
                    } 
                }            
            }
            
            $this->redirect(array('controller' => 'listas_de_preco', 'action' => 'index'));
        }
    }        
}