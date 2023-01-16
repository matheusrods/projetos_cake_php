<?php
class LiberacoesProvisoriasController extends AppController {
	var $name = 'LiberacoesProvisorias';
    var $components = array('Filtros', 'RequestHandler','Session');
    var $helpers = array('Html', 'Ajax', 'Paginator');
	var $uses = array('LiberacaoProvisoria', 'Produto');
	var $paginate = array(
	   'limit' => 25,
	   'order' => 'LiberacaoProvisoria.data_liberacao DESC',
	   'conditions' => array(
	       'LiberacaoProvisoria.ativo' => 1
	   )
	);
    
    function index() {
        $this->pageTitle = 'Perfil Adequado por Prazo';
        $this->data['LiberacaoProvisoria']['data_inicio'] = date('d/m/Y');
      	$this->data['LiberacaoProvisoria']['data_fim']    = date('d/m/Y');
        $this->data['LiberacaoProvisoria'] = $this->Filtros->controla_sessao($this->data, 'LiberacaoProvisoria');
        $_SESSION['FiltrosLiberacaoProvisoria']['index']  = 's';
        $produtos = $this->LiberacaoProvisoria->Produto->find('list', array('conditions' => array('Produto.codigo' => array(1, 2, 134))));
        $this->set('isAjax', $this->RequestHandler->isAjax());
        $this->set(compact('produtos'));
    }

	function incluir() {
	    $this->pageTitle = 'Perfil Adequado por Prazo'; 
	    $retornoLiberacoes = array('error' => array(), 'success' => array());	    
		if (!empty($this->data)) {
		    $this->LiberacaoProvisoria->create();
		    $retornoLiberacoes = $this->LiberacaoProvisoria->salvarLiberacoesPorProduto($this->data);
		    if( $retornoLiberacoes ){
		    	$this->redirect(array('action' => 'index'));
		    } else {
		    	$this->BSession->setFlash('save_error');
		    }
		}
        $produtos = $this->LiberacaoProvisoria->Produto->find('list', array('conditions' => array('Produto.codigo' => array(1, 2, 134))));
        $this->set(compact('produtos', 'retornoLiberacoes'));
	}

	function cancelar($id = null) {
		if (!$id) {
			$this->BSession->setFlash('codigo_invalido');
			$this->redirect(array('action'=>'index'));
		}
		if ($this->LiberacaoProvisoria->excluir($id)) {
			$this->BSession->setFlash('delete_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->BSession->setFlash('delete_error');
		$this->redirect(array('action'=>'index'));
	}
	
    function listagem($destino = 'liberacoes_provisorias') {
    	$_SESSION['FiltrosLiberacaoProvisoria']['listagem'] = 's';

    	if ( isset($_SESSION['FiltrosLiberacaoProvisoria']['index']) &&  $_SESSION['FiltrosLiberacaoProvisoria']['index'] == 's'){
             $filtros = $this->Filtros->controla_sessao($this->data, 'LiberacaoProvisoria');
             $_SESSION['FiltrosLiberacaoProvisoria']['index']='n';
        }else{
             $filtros = $this->Filtros->controla_sessao($this->data,$this->LiberacaoProvisoria->name);
        }
        $this->layout = 'ajax';
        $conditions = $this->LiberacaoProvisoria->converteFiltroEmCondition($filtros);
        array_push($this->paginate['conditions'], $conditions);

        $liberacoes = $this->LiberacaoProvisoria->formataDados($this->paginate());
        $this->set(compact('liberacoes', 'destino'));
    }
	
}
