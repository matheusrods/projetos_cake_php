<?php
class ListasDePrecoProdutoServicoController extends AppController {
    public $name = 'ListasDePrecoProdutoServico';
    var $uses = array('ListaDePreco', 'ListaDePrecoProdutoServico', 'Produto', 'Servico', 'ProdutoServico');

    public function incluir() {
        $this->pageTitle = 'Incluir Produtos';
        $produtos = $this->Produto->find('list', array('conditions' => array('Produto.ativo = 1')));        
        $servicos = array();
        $tem_controle_de_volume = false;
        if (!empty($this->data)) {
            $this->data['ListaDePrecoProdutoServico']['codigo_lista_de_preco'] = $this->passedArgs[0];
            if ($this->ListaDePrecoProdutoServico->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'listas_de_preco_produto', 'action' => 'index', $this->passedArgs[0]));
            } else {
                $this->BSession->setFlash('save_error');
            }
            $servicos = $this->ProdutoServico->servicosPorProduto($this->data['ListaDePrecoProdutoServico']['codigo_produto']);
            $produto = $this->Produto->carregar($this->data['ListaDePrecoProdutoServico']['codigo_produto']);
            $tem_controle_de_volume = $produto['Produto']['controla_volume'];
        } else {
            $this->data['ListaDePrecoProdutoServico']['tipo_premio_minimo']  = 1;
            $this->data['ListaDePrecoProdutoServico']['valor'] 				 = 0;
            $this->data['ListaDePrecoProdutoServico']['valor_premio_minimo'] = 0;
            $this->data['ListaDePrecoProdutoServico']['qtd_premio_minimo']   = 0;
        }

        $lista_de_preco = $this->ListaDePreco->carregar($this->passedArgs[0]);

        $this->set(compact('produtos', 'servicos', 'tem_controle_de_volume', 'lista_de_preco'));
    }

    public function editar($codigo_lista_de_preco, $codigo) {
        $tem_controle_de_volume = false;

        if (!empty($this->data)) {
            if ($this->ListaDePrecoProdutoServico->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'listas_de_preco_produto', 'action' => 'index', $this->passedArgs[0]));
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->ListaDePrecoProdutoServico->carregar($codigo);
            $this->data['Produto'] 											= $this->data['ListaDePrecoProduto']['Produto'];
            $this->data['ListaDePrecoProdutoServico']['tipo_premio_minimo'] = ($this->data['ListaDePrecoProduto']['valor_premio_minimo'] > 0 ? 1 : 2);
            if ($this->data['ListaDePrecoProdutoServico']['tipo_premio_minimo'] == 1) {
                $this->data['ListaDePrecoProdutoServico']['valor_premio_minimo'] = $this->data['ListaDePrecoProduto']['valor_premio_minimo'];
                $this->data['ListaDePrecoProdutoServico']['qtd_premio_minimo'] 	 = $this->data['ListaDePrecoProduto']['qtd_premio_minimo'];
            }
            $this->data['ListaDePrecoProdutoServico']['tem_controle_de_volume'] = $this->data['Produto']['controla_volume'];
        }

        $lista_de_preco = $this->ListaDePreco->carregar($codigo_lista_de_preco);

        $this->set(compact('lista_de_preco'));

    }

    public function excluir($codigo_lista_de_preco, $codigo_lista_de_preco_produto_servico) {
        if ($this->ListaDePrecoProdutoServico->excluir($codigo_lista_de_preco_produto_servico)) {
            $this->BSession->setFlash('delete_success');
        } else {
            $this->BSession->setFlash('delete_error');
        }
        $this->redirect(array('controller' => 'listas_de_preco_produto', 'action' => 'index', $codigo_lista_de_preco));
    }
}