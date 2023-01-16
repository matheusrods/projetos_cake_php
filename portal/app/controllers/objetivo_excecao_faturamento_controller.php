<?php
class ObjetivoExcecaoFaturamentoController extends AppController {
    public $name = 'ObjetivoExcecaoFaturamento';
    public $uses = array(
        'ObjetivoExcecaoFaturamento',
        'LogObjetivoExcFat',
    );
    
    function index() {
        $filtrado = true;
        $this->pageTitle = 'Exceções Por Faturamento Médio';
        $this->carregaCombos();
        $this->data['ObjetivoExcecaoFaturamento'] = $this->Filtros->controla_sessao($this->data, "ObjetivoExcecaoFaturamento");
        $this->set(compact('filtrado'));
    } 
    
    function listagem(){
        $this->data['ObjetivoExcecaoFaturamento'] = $this->Filtros->controla_sessao($this->data, "ObjetivoExcecaoFaturamento");
        $this->ObjetivoExcecaoFaturamento->bindObjetivoExcecaoFaturamento();
        $conditions = $this->ObjetivoExcecaoFaturamento->converteFiltrosEmConditions($this->data['ObjetivoExcecaoFaturamento']);
        
        $order = array('ObjetivoExcecaoFaturamento.ano','ObjetivoExcecaoFaturamento.mes');   
        $this->paginate['ObjetivoExcecaoFaturamento'] = array(
            'limit' => 50,
            'conditions' => $conditions,
            'order' => $order
        );
        $listagem = $this->paginate('ObjetivoExcecaoFaturamento');
        $this->set(compact('listagem'));
    }

    function carregaCombos(){
        $this->loadModel('Produto');
        $meses = Comum::listMeses();       
        $anos = Comum::listAnos(2014);
        array_push($anos, date('Y', strtotime('+1 year')));
        $produtos = $this->Produto->listarProdutosNavegarqCodigoBuonny();
        unset($produtos[30]);        
        $this->set(compact('meses', 'anos','produtos'));
    }


    function incluir(){
        $this->pageTitle = 'Incluir Exceção Por Faturamento Médio';
        $this->carregaCombos();

        if (!empty($this->data)) {
            if ($this->ObjetivoExcecaoFaturamento->incluir($this->data)) {
                $id = $this->ObjetivoExcecaoFaturamento->getLastInsertID(); 
                $dados = $this->ObjetivoExcecaoFaturamento->carregar($id);
                $this->LogObjetivoExcFat->incluir_log($dados,'I');
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
         }
    }

    function editar($codigo){
        $this->pageTitle = 'Editar Exceção Por Faturamento Médio';
        $this->carregaCombos();
        if($this->RequestHandler->isPost()) {
            $this->data['ObjetivoExcecaoFaturamento']['codigo'] = $codigo;
            if ($this->ObjetivoExcecaoFaturamento->atualizar($this->data)) {
                $dados = $this->ObjetivoExcecaoFaturamento->carregar($codigo);
                $this->LogObjetivoExcFat->incluir_log($dados,'U');
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }    
        $this->data = $this->ObjetivoExcecaoFaturamento->carregar($codigo);
        $this->data['ObjetivoExcecaoFaturamento']['faturamento_medio']  = number_format ( $this->data['ObjetivoExcecaoFaturamento']['faturamento_medio'] , 2 , ',' , '' );
    }

    function excluir($codigo){
        if($codigo){
            $dados = $this->ObjetivoExcecaoFaturamento->carregar($codigo);
            if($this->ObjetivoExcecaoFaturamento->excluir($codigo)){
                $this->LogObjetivoExcFat->incluir_log($dados,'D');
                $this->BSession->setFlash('save_success');
            }else{
                $this->BSession->setFlash('delete_error');
            }
            $this->redirect(array('action' => 'index'));
        }
    }

   
}
?> 