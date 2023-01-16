<?php
class GruposExamesController extends appController {
	var $name = 'GruposExames';
	var $uses = array('GrupoExame');

/*    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('index','listagem','lista_exames_grupo','exclui_exames_grupo'));
    }*/

    function index($codigo_detalhe_grupo_exame = null, $codigo_cliente = null) {
    	$DetalheGrupoExame = ClassRegistry::init('DetalheGrupoExame');
    	$ClienteProduto = ClassRegistry::init('ClienteProduto');
    	$dados_grupo = $DetalheGrupoExame->find('all',array('conditions' => array('codigo' => $codigo_detalhe_grupo_exame)));
    	$this->pageTitle = 'Grupo de Exame - '.$dados_grupo[0]['DetalheGrupoExame']['descricao'];
    	$filtros = $this->Filtros->controla_sessao($this->data, 'GrupoExame');
    	$this->data['GrupoExame'] = $filtros;
    	$produtos_servicos = $ClienteProduto->listarPorCodigoCliente($codigo_cliente, false, false, true);
    	$this->data['GrupoExame']['codigo_detalhe_grupo_exame'] = $codigo_detalhe_grupo_exame;
    	$this->set(compact('codigo_detalhe_grupo_exame','dados_grupo','codigo_cliente','produtos_servicos'));
  	}

  	function listagem() {
		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, 'GrupoExame');
		$joins = array(
			array(
				'table' => 'exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoExame.codigo_exame = Exame.codigo'
				)
			)
		);
		$conditions = $this->GrupoExame->converteFiltrosEmConditions($filtros);
		$grupos_exames_find = $this->GrupoExame->find('all',array('conditions' => $conditions,'fields' => array('GrupoExame.codigo_detalhe_grupo_exame','GrupoExame.codigo_exame','Exame.descricao'),'joins' => $joins));
		foreach($grupos_exames_find as $grupo){
			$grupos_exames[$grupo['GrupoExame']['codigo_exame']] = array(
				'codigo_detalhe_grupo_exame' => $grupo['GrupoExame']['codigo_detalhe_grupo_exame'],
				'codigo_exame' => $grupo['GrupoExame']['codigo_exame'],
				'descricao' => $grupo['Exame']['descricao']
			);
		}
		$this->set(compact('grupos_exames'));
	}

	function lista_exames_grupo($codigo_detalhe_grupo_exame = null) {
		$Exames = ClassRegistry::init('Exames');
		$lista_exame = $Exames->find('list',array('fields' => array('codigo_servico','codigo')));
		$exames = explode(',',$this->params['form']['exames']);
		$dados['codigo_detalhe_grupo_exame'] = $codigo_detalhe_grupo_exame;
		foreach ($exames as $codigo_exame) {
			$dados['codigo_exame'] = $lista_exame[$codigo_exame];
			$conditions = array(
				'codigo_detalhe_grupo_exame' => $dados['codigo_detalhe_grupo_exame'],
				'codigo_exame' => $dados['codigo_exame']
			);
			$busca_exame_no_grupo = $this->GrupoExame->find('first',array('conditions' => $conditions));
			if (!$busca_exame_no_grupo){
				$this->GrupoExame->incluir($dados);
			}
		}
	}

	function exclui_exames_grupo($codigo_detalhe_grupo_exame = null, $codigo_exame = null,$codigo_cliente = null) {
		$conditions = array(
			'codigo_detalhe_grupo_exame' => $codigo_detalhe_grupo_exame,
			'codigo_exame' => $codigo_exame
		);
		$busca_exame_no_grupo = $this->GrupoExame->find('first',array('conditions' => $conditions));
		if (!empty($busca_exame_no_grupo)){
			if($this->GrupoExame->excluir($busca_exame_no_grupo['GrupoExame']['codigo'])){
				$this->BSession->setFlash('save_success');
            }else{
                $this->BSession->setFlash('delete_error');
            }
            $this->redirect(array('action' => 'index',$codigo_detalhe_grupo_exame,$codigo_cliente));
		}          
	}

}
?>