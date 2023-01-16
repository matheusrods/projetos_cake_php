<?php
class OperadoresController extends AppController {
	public $name = 'Operadores';
	public $uses = array(
		'TUsuaUsuario',
		'TUaatUsuarioAreaAtuacao',
		'TPessPessoa',
		'TAatuAreaAtuacao',
		'TViagViagem',
		'RelatorioSm'
	);


	public $components = array('RequestHandler');
	public $helpers = array('Html', 'Ajax');

	function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array(
				'redistribuir',
				'viagens_sem_operador',
				'viagens_sem_operadores_listagem',
			)
		);
	}
		
	function index() {
		$this->pageTitle = 'Operadores de Monitoramento';
		$this->data['TUsuaUsuario'] = $this->Filtros->controla_sessao($this->data, $this->TUsuaUsuario->name);
		$status = array('1' =>'Ativo', '2'=> 'Inativo');
		$this->set(compact('status'));
	}
	
    function listagem($destino) {
        $this->layout = 'ajax';

        $filtros    = $this->Filtros->controla_sessao($this->data, $this->TUsuaUsuario->name);
        $conditions = $this->TUsuaUsuario->converteFiltroEmCondition($filtros);
        $this->paginate['TUsuaUsuario'] = array(
             'conditions' => $conditions,
             'limit' => 50,
             'order' => 'TUsuaUsuario.usua_login',
             'fields' => array('TUsuaUsuario.usua_pfis_pess_oras_codigo',
							   'TUsuaUsuario.usua_login',
                               'TPessPessoa.pess_nome',
                               'TOrasObjetoRastreado.oras_eobj_codigo'),
             'joins' => array(
             				array(
             					'table' => 'trafegus.public.pess_pessoa',
                                'alias' => 'TPessPessoa',
                                'conditions' => 'TUsuaUsuario.usua_pfis_pess_oras_codigo = TPessPessoa.pess_oras_codigo',
                                'type' => 'left'
                                ),
             					array(
             					'table' => 'trafegus.public.oras_objeto_rastreado',
             					'alias' => 'TOrasObjetoRastreado',
             					'conditions' => 'TOrasObjetoRastreado.oras_codigo = TPessPessoa.pess_oras_codigo',
             					'type' => 'left'
             					)
 							)
             );
        $usuarios = $this->paginate('TUsuaUsuario');
        $this->set(compact('usuarios'));
    }
	
	function gerenciar_areas_atuacoes($usua_oras_codigo) {
		$this->pageTitle = 'Operador Área de Atuação';
		if(!empty($this->data)) {
			$usua_oras_codigo = $this->data['TUaatUsuarioAreaAtuacao']['uaat_usua_oras_codigo'];
			$this->data['TUaatUsuarioAreaAtuacao']['uaat_data_cadastro'] = date('Y-m-d H:i:s');
			$this->TUaatUsuarioAreaAtuacao->create();
			if($this->TUaatUsuarioAreaAtuacao->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('controller' => 'operadores', 'action' => 'gerenciar_areas_atuacoes', $usua_oras_codigo));
			} else {
				$this->BSession->setFlash('save_error');
			}
		}
		
		$dados = $this->TUaatUsuarioAreaAtuacao->carregarAreasDeAtuacao($usua_oras_codigo);
		$descricao = $this->TPessPessoa->find('first', array('conditions' => array('pess_oras_codigo' => $usua_oras_codigo)));
		$areas_atuacoes = $this->TAatuAreaAtuacao->listar();
		$this->set(compact('dados', 'descricao', 'usua_oras_codigo', 'areas_atuacoes'));
	}
	
	function delete($usua_oras_codigo = null, $aatu_codigo = null) {
		if ($this->TUaatUsuarioAreaAtuacao->excluir($usua_oras_codigo, $aatu_codigo)) {
			$this->BSession->setFlash('delete_success');
			$this->redirect(array('action'=>'gerenciar_areas_atuacoes', $usua_oras_codigo));
		}
		$this->BSession->setFlash('delete_error');
		$this->redirect(array('action' => 'gerenciar_areas_atuacoes', $usua_oras_codigo));
	}

	function viagens_operadores($new_window = FALSE){
		$this->pageTitle = 'Viagens por Operador'; 		
		$this->loadModel('TViagViagem');
		$this->loadModel('TAatuAreaAtuacao');
		$this->loadModel('TErasEstacaoRastreamento');		
		$aatu_lista = $this->TAatuAreaAtuacao->listar();
		$estacao 	= $this->TErasEstacaoRastreamento->listaParaCombo();		
		$this->data["TVusuViagemUsuario"] = $this->Filtros->controla_sessao( $this->data, "TVusuViagemUsuario" );
		if( $new_window ){
			$this->layout = 'new_window';
		}
		$this->set(compact('aatu_lista', 'estacao'));
	}

	function viagens_operadores_listagem(){
		$this->loadModel('TViagViagem');
		$filtros 	= $this->Filtros->controla_sessao($this->data, "TVusuViagemUsuario");
		$listagem 	= $this->TViagViagem->listarViagensDistribuidas($filtros);
		$lista 		= array();
		$last_grupo = NULL;
		$coluna 	= 1;
		$linha 		= 0;
		foreach ($listagem as $key => $usuario) {
			$grupo = $usuario['TAatuAreaAtuacao']['aatu_descricao'];
			if($grupo != $last_grupo){
				$coluna = 1;
				$linha 	= 0;
			}

			$linha 		= $coluna?$linha+1:$linha;
			$coluna 	= $coluna?0:1;
			$lista[$grupo][$linha][$coluna] = $usuario;

			$last_grupo = $grupo;
		}
		$listagem 	 = $lista;
        $data_inicio = date("d/m/Y", strtotime( date("Ymd"). " -10 days" ) );
        $data_fim    = date("d/m/Y");
        $filtros['data_inicio'] = $data_inicio;
        $filtros['data_fim']    = $data_fim;
		$this->set(compact('listagem', 'filtros'));
	}

	function viagens_operador($usua_codigo ,$status = FALSE, $new_window = FALSE){
		if($usua_codigo == 'N'){
			$usua_codigo = NULL;
	       	$this->pageTitle = 'Viagens Sem  Operador'; 
		}else{
			$this->pageTitle = 'Viagens por Operador';
		}
		if($new_window){
	       $this->layout = 'new_window';
 		}
		$conditions = array();
		//$status = 1 [AGENDAMENTO]; $status = 2 [EM ANDAMENTO]
		if($status == 1){
			$conditions = array(
				'viag_data_inicio' => NULL
			);
		} elseif($status == 2) {
			$conditions = array(
				'NOT' => array(
					'viag_data_inicio' => NULL
				)
			);
		}
		if( is_numeric($usua_codigo) ){
			$conditions['erus_usua_pfis_pess_oras_codigo'] = $usua_codigo;
			$conditions['viag_data_fim'] = NULL;
			$conditions['join_alvos'] = FALSE;
		}		
		$this->data = $this->TUsuaUsuario->carregarOperador($usua_codigo);
		$listagem   = $this->RelatorioSm->listagem_analitico( $conditions, 1000, 1, FALSE, TRUE);
		$this->set(compact('listagem'));
	}	

	function redistribuir_viagens(){
		$this->loadModel('TVusuViagemUsuario');
		foreach ($this->data['TUsuaUsuario'] as $usua_codigo => $usua) {
			if($usua['usua_pfis_pess_oras_codigo'])
				$this->TVusuViagemUsuario->redistribuirViagens($usua_codigo);
		}
		
		exit;
	}

	function viagens_sem_operador(){
		$this->layout = 'new_window';
		$this->pageTitle = 'Viagens sem operadores';		

	}

	function viagens_sem_operadores_listagem(){
		$this->paginate['TViagViagem'] = array(
			'method' => 'viagens_sem_operador',
			'limit' => 50
		);		

		$listagem = $this->paginate('TViagViagem');
		$this->set(compact('listagem'));
	}

	function listagem_operadores_por_sm( $codigo_sm ){
		$this->loadModel('TViagViagem');
		$filtros['codigo_sm'] = $codigo_sm;
		$listagem 	= $this->TViagViagem->listarViagensDistribuidas($filtros);
		$lista 		= array();
		$last_grupo = NULL;
		$coluna 	= 1;
		$linha 		= 0;
		$estacao    = NULL;
		$total_operadores = 0;
		foreach ($listagem as $key => $usuario) {
			$grupo   = $usuario['TAatuAreaAtuacao']['aatu_descricao'];
			$logado  = $usuario[0]['logado'];
			if($grupo != $last_grupo){
				$coluna = 1;
				$linha 	= 0;
			}
			$linha 	= $coluna?$linha+1:$linha;
			$coluna = $coluna?0:1;
			if( $logado ){
				$estacao = $usuario['TErasEstacaoRastreamento']['eras_descricao'];
				$lista[$grupo][$linha][$coluna] = $usuario;
				$total_operadores +=1;
			}
			$last_grupo = $grupo;
		}		
		$listagem = $lista;		
		$this->set(compact('listagem', 'total_operadores', 'estacao'));
	}
}?>