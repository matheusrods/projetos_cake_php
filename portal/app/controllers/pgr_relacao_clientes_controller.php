<?php
class PgrRelacaoClientesController extends AppController {
    public $name = 'PgrRelacaoClientes';
    public $uses = array(
        'TPrclPgrRelacaoCliente',
        'TPgpgPg',
        'TTtraTipoTransporte'
    );
    var $components = array('DbbuonnyGuardian');
  
    function carregaCombos(){
    	$pgr = $this->TPgpgPg->find('list',array('fields' => array('pgpg_codigo'),'conditions' => array('pgpg_estatus' => 'A')));
    	$tipo_transporte = $this->TTtraTipoTransporte->find('list');
    	$this->set(compact('pgr','tipo_transporte'));
    }

    function index(){
    	$this->pageTitle ='PGR por Embarcador e Transportador';
    	$this->carregaCombos();
        $this->data['TPrclPgrRelacaoCliente'] = $this->Filtros->controla_sessao($this->data, "TPrclPgrRelacaoCliente");
    }

    function listagem(){
    	$this->data['TPrclPgrRelacaoCliente'] = $this->Filtros->controla_sessao($this->data, "TPrclPgrRelacaoCliente");
    	$this->TPrclPgrRelacaoCliente->bindEmbTransTtra();
    	$conditions = $this->TPrclPgrRelacaoCliente->convertFiltrosEmConditions($this->data['TPrclPgrRelacaoCliente']);
		$this->paginate = array(
			'conditions' => $conditions,
			'limit' => 50,
			'order' => 'prcl_codigo'
		);
    	$listagem = $this->paginate('TPrclPgrRelacaoCliente');

    	$this->set(compact('listagem'));
    }

    function incluir(){
        $this->pageTitle = 'Incluir PGR por Embarcador e Transportador';
        $this->carregaCombos();

        if (!empty($this->data)) {
        	$dados = $this->TPrclPgrRelacaoCliente->conveter_dados_inclusao($this->data);
            if ($this->TPrclPgrRelacaoCliente->incluir($dados)) {              
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
         }
    }

    function excluir($codigo){
        if($codigo){
            if($this->TPrclPgrRelacaoCliente->excluir($codigo)){
                $this->BSession->setFlash('save_success');
            }else{
                $this->BSession->setFlash('delete_error');
            }
            $this->redirect(array('action' => 'index'));
        }
    }

}
?>
