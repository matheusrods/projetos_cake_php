<?php
class ProdutosServicosController extends AppController {
    public $name = 'ProdutosServicos';
    var $uses = array('ProdutoServico');


    function servicos_por_produto($codigo_produto) {
        $this->layout = 'ajax';
        $this->set('servicos', $this->ProdutoServico->servicosPorProduto($codigo_produto));
    }

    function excluir($codigo_produto_servico) {
    	if($this->ProdutoServico->excluir($codigo_produto_servico)) {
            $this->BSession->setFlash('delete_success');
        } else {
            $this->BSession->setFlash('delete_error');
    	}

    	$this->redirect($this->referer());
    }
    function editar_status_produto_servico($codigo, $status){
        $this->layout = 'ajax';
        if(!is_numeric($codigo)){
            print 0;
            exit;
        }
        $codigo = trim($codigo);
        $status= ($status == 0) ? $status = 1 : $status = 0;        
        $this->data['ProdutoServico']['codigo'] = $codigo;        
        $this->data['ProdutoServico']['ativo'] = $status;
        if ($this->ProdutoServico->atualizar($this->data)) {            
            $this->render(false,false);
            print 1;
        } else {
            $this->render(false,false);
            print 0;
        }
        // 0 -> ERRO | 1 -> SUCESSO
    }

}