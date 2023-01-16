<?php
class DetalhesGruposExamesController extends appController {
	var $name = 'DetalhesGruposExames';
	var $uses = array('DetalheGrupoExame');

/*    public function beforeFilter(){
        parent::beforeFilter();
        $this->BAuth->allow(array('busca_por_cliente','index','listagem','incluir','trocar_status','lista_servicos_grupo'));
    }*/

    function busca_por_cliente(){
    	$Cliente = ClassRegistry::init('Cliente');
    	$this->pageTitle = 'Detalhes dos Grupos de Exames';
    	if ($this->RequestHandler->isPost()) {
    		$codigo_cliente = $Cliente->find('first',array('conditions' => array('codigo' => $this->data['DetalheGrupoExame']['codigo_cliente']),'fields' => array('codigo')));
    		if(!empty($codigo_cliente)){
    			$this->redirect(array('controller' => 'DetalhesGruposExames', 'action' => 'index',$codigo_cliente['Cliente']['codigo']));
    		} else {
    			$this->DetalheGrupoExame->invalidate('codigo_cliente','Cliente não encontrado!');
    		}
    	}
    }

    function index($codigo_cliente = null){
    	$Cliente = ClassRegistry::init('Cliente');
    	$GruposEconomicosClientes = ClassRegistry::init('GruposEconomicosClientes');
    	$this->pageTitle = 'Detalhes dos Grupos de Exames';
    	$dados_cliente = $Cliente->find('first',array('conditions' => array('codigo' => $codigo_cliente),'fields' => array('codigo','razao_social')));
    	$dados_grupo_economico_cliente = $GruposEconomicosClientes->find('first',array('conditions' => array('codigo_cliente' => $codigo_cliente),'fields' => array('codigo_grupo_economico')));
    	$this->data['DetalheGrupoExame'] = $dados_grupo_economico_cliente['GruposEconomicosClientes'];
    	$this->set(compact('dados_cliente','dados_grupo_economico_cliente'));
  	}

  	function listagem($codigo_cliente){
		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, 'DetalheGrupoExame');
		$conditions = $this->DetalheGrupoExame->converteFiltrosEmConditions($filtros);
		$this->paginate['DetalheGrupoExame'] = array(
			'recursive' => 1,
			'conditions' => $conditions,
			'limit' => 50,
			'order' => 'DetalheGrupoExame.codigo',
		);
		$detalhes_grupos_exames = $this->paginate('DetalheGrupoExame');
		$this->set(compact('codigo_cliente','detalhes_grupos_exames'));
	}

	function incluir($codigo_grupo_economico = null, $codigo_cliente = null){
		$this->pageTitle = 'Incluir Grupo de Exames';
		if ($this->RequestHandler->isPost()) {
			$this->data['DetalheGrupoExame']['codigo_grupo_economico'] = $codigo_grupo_economico;
			if ($this->DetalheGrupoExame->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect('/DetalhesGruposExames/index/'.$codigo_cliente);
			} else {
				$this->BSession->setFlash('save_error');
			}
		}
		$this->set(compact('codigo_grupo_economico','codigo_cliente'));
	}

	function trocar_status($codigo_detalhe_grupo_exame = null, $codigo_cliente = null){
		$this->data = $this->DetalheGrupoExame->read(null, $codigo_detalhe_grupo_exame);
		$this->data['DetalheGrupoExame']['ativo'] = ($this->data['DetalheGrupoExame']['ativo'] == 0 ? 1 : 0);
		if ($this->DetalheGrupoExame->atualizar($this->data)) {
			$this->BSession->setFlash('save_success');
		} else {
			$this->BSession->setFlash('save_error');
		}
		$this->redirect('/DetalhesGruposExames/index/'.$codigo_cliente);
	}

	function lista_servicos_grupo(){
		$fields = array('Exame.codigo_servico','Exame.descricao');
		$conditions = array('DetalheGrupoExame.codigo' => $this->params['form']['grupos']);
		$joins = array(
			array(
				'table' => 'grupos_exames',
				'alias' => 'GrupoExame',
				'type' => 'INNER',
				'conditions' => array(
					'DetalheGrupoExame.codigo = GrupoExame.codigo_detalhe_grupo_exame'
				)
			),
			array(
				'table' => 'exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoExame.codigo_exame = Exame.codigo'
				)
			)
		);
		$dados = $this->DetalheGrupoExame->find('list',array('conditions' => $conditions,'fields' => $fields,'joins' => $joins));
		echo json_encode($dados);
		exit;
	}

}
?>