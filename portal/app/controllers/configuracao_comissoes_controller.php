<?php
 
class ConfiguracaoComissoesController extends AppController {

	public $name = 'ConfiguracaoComissoes';
	public $uses = array('ConfiguracaoComissao','ConfiguracaoComissaoLog');

	function index(){
		$this->pageTitle = "Configuração de Comissões por Filial";
		$this->data['ConfiguracaoComissao'] = $this->Filtros->controla_sessao($this->data, $this->ConfiguracaoComissao->name);
		$this->carregarCombos();
	}

	function por_corretora(){
		$this->loadModel('ConfiguracaoComissaoCorre');
		$this->pageTitle = "Configuração de Comissões por Corretora";

		$this->data['ConfiguracaoComissaoCorre'] = $this->Filtros->controla_sessao($this->data, $this->ConfiguracaoComissaoCorre->name);
		$this->carregarCombosCorretora();
	}

	private function carregarCombos(){
		$this->loadModel('EnderecoRegiao');
		$this->loadModel('NProduto');

		$filiais 	= $this->EnderecoRegiao->find('list');
		$produtos 	= $this->NProduto->listVinculadoPortal();

		$this->set(compact('filiais','produtos','servicos'));
	}

	private function carregarCombosCorretora(){
		$this->loadModel('Corretora');
		$this->loadModel('Produto');
		$this->loadModel('ProdutoServico');

		$corretoras = $this->Corretora->listarCorretorasAtivas();
		$produtos = $this->Produto->listar('list',array('codigo_naveg IS NOT NULL'),'descricao ASC');
		$servicos = array();
		if(isset($this->data['ConfiguracaoComissaoCorre']['codigo_produto'])){
			$servicos = $this->ProdutoServico->servicosPorProduto($this->data['ConfiguracaoComissaoCorre']['codigo_produto']);
		}
		$this->set(compact('corretoras','produtos','servicos'));
	}

	function listagem(){
		$filtro = $this->Filtros->controla_sessao($this->data, $this->ConfiguracaoComissao->name);
		$this->paginate['ConfiguracaoComissao'] 	= $this->ConfiguracaoComissao->convertFiltroEmParametros($filtro);
		$listagem  									= $this->paginate('ConfiguracaoComissao');
		$this->set(compact('listagem'));
	}

	function listagem_por_corretora(){
		$this->loadModel('ConfiguracaoComissaoCorre');
		
		$filtro = $this->Filtros->controla_sessao($this->data, $this->ConfiguracaoComissaoCorre->name);
		
		$this->paginate['ConfiguracaoComissaoCorre'] = $this->ConfiguracaoComissaoCorre->convertFiltroEmParametros($filtro);
		$listagem = $this->paginate('ConfiguracaoComissaoCorre');
		$this->set(compact('listagem'));
	}
	
	function incluir(){
		$this->pageTitle = "Incluir Configuração por Filial";
		if($this->RequestHandler->isPost()) {
			if($this->ConfiguracaoComissao->incluir($this->data)){
				$this->BSession->setFlash('save_success');
				$this->redirect(array('controller' => 'ConfiguracaoComissoes', 'action' => 'index'));
			} else {
				$this->BSession->setFlash('save_error');
			}
		}

		$this->carregarCombos();
	}

	function incluir_por_corretora(){
		$this->loadModel('ConfiguracaoComissaoCorre');
		$this->loadModel('ProdutoServico');
		$this->pageTitle = "Incluir Configuração por Corretora";
		if(!empty($this->data)) {
			$this->data['ConfiguracaoComissaoCorre']['codigo_corretora'] = $this->data['ConfiguracaoComissaoCorre']['codigo_corretora_dialog'];
			$this->data['ConfiguracaoComissaoCorre']['codigo_corretora_visual'] = $this->data['ConfiguracaoComissaoCorre']['codigo_corretora_dialog_visual'];
			$this->Filtros->controla_sessao($this->data, $this->ConfiguracaoComissaoCorre->name);
			if($this->ConfiguracaoComissaoCorre->incluir($this->data)){
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
				if(isset($this->ConfiguracaoComissaoCorre->validationErrors['codigo_corretora']))
					$this->ConfiguracaoComissaoCorre->validationErrors['codigo_corretora_dialog_visual'] = $this->ConfiguracaoComissaoCorre->validationErrors['codigo_corretora'];
				if(isset($this->ConfiguracaoComissaoCorre->validationErrors['preco_de']))
					$this->ConfiguracaoComissaoCorre->invalidate('preco_ate','');
			}
		}else{
			$this->data['ConfiguracaoComissaoCorre'] = $this->Filtros->controla_sessao($this->data, $this->ConfiguracaoComissaoCorre->name);
			if(isset($this->data['ConfiguracaoComissaoCorre']['codigo_corretora']))
				$this->data['ConfiguracaoComissaoCorre']['codigo_corretora_dialog'] = $this->data['ConfiguracaoComissaoCorre']['codigo_corretora'];
			if(isset($this->data['ConfiguracaoComissaoCorre']['codigo_corretora_visual']))
				$this->data['ConfiguracaoComissaoCorre']['codigo_corretora_dialog_visual'] = $this->data['ConfiguracaoComissaoCorre']['codigo_corretora_visual'];
			unset($this->data['ConfiguracaoComissaoCorre']['verificar_preco_unitario']);
			unset($this->data['ConfiguracaoComissaoCorre']['preco_de']);
			unset($this->data['ConfiguracaoComissaoCorre']['preco_ate']);
			unset($this->data['ConfiguracaoComissaoCorre']['percentual_impostos']);
			unset($this->data['ConfiguracaoComissaoCorre']['percentual_comissao']);
		}
		$servicos = $this->ProdutoServico->servicosPorProduto($this->data['ConfiguracaoComissaoCorre']['codigo_produto']);
		$this->set(compact('servicos'));

		$this->carregarCombosCorretora();
	}

	function atualizar($codigo){
		$this->pageTitle = "Atualizar Configuração por Filial";
		if($this->RequestHandler->isPost()) {
			if($this->ConfiguracaoComissao->atualizar($this->data)){
				$this->BSession->setFlash('save_success');
				$this->redirect(array('controller' => 'ConfiguracaoComissoes', 'action' => 'index'));
			} else {
				$this->BSession->setFlash('save_error');
			}
		} else {			
			$this->data = $this->ConfiguracaoComissao->carregar($codigo);
			$this->data['ConfiguracaoComissao']['percentual'] = number_format ( $this->data['ConfiguracaoComissao']['percentual'] , 2 , ',' , '' );
		}
		$this->carregarCombos();		
		$ConfiguracaoComissao = $this->data;
		$filtro = array(
			'codigo_configuracao_comissoes' => $codigo,
			'codigo_endereco_regiao' 		=> $ConfiguracaoComissao['ConfiguracaoComissao']['codigo_endereco_regiao'],
			'codigo_produto_naveg' 			=> $ConfiguracaoComissao['ConfiguracaoComissao']['codigo_produto_naveg'],
		);		
		$EnderecoRegiao		=& ClassRegistry::Init('EnderecoRegiao');
		$NProduto			=& ClassRegistry::Init('NProduto');
		$Usuario			=& ClassRegistry::Init('Usuario');
		$conditions = $this->ConfiguracaoComissaoLog->convertFiltrosEmConditions( $filtro );
		$fields 	= array(
			"CONVERT(VARCHAR,ConfiguracaoComissaoLog.data_inclusao,103) AS data_inclusao",
			"ConfiguracaoComissaoLog.data_alteracao",
		    "UsuarioInclusao.apelido",
		    "UsuarioAlteracao.apelido",
		    "ConfiguracaoComissaoLog.codigo_endereco_regiao",
		    "ConfiguracaoComissaoLog.codigo_produto_naveg",
		    "CASE	
		      WHEN  ConfiguracaoComissaoLog.acao_sistema = 0 THEN 'Inclusão'
		      WHEN  ConfiguracaoComissaoLog.acao_sistema = 1 THEN 'Altualização'
		      WHEN  ConfiguracaoComissaoLog.acao_sistema = 2 THEN 'Exclusão'
		      End AS acao",		    
		    "EnderecoRegiao.descricao",
		    "NProduto.descricao", 
		    "CASE	 
		     	WHEN ConfiguracaoComissaoLog.regiao_tipo_faturamento = 0 THEN 'Parcial'
		     	WHEN ConfiguracaoComissaoLog.regiao_tipo_faturamento = 1 THEN 'Total'
		    END  AS Faturamento",
		    "ConfiguracaoComissaoLog.percentual AS Comissao"
		);
		$this->paginate['ConfiguracaoComissaoLog'] = array(
			'joins' => array(
				array(
					'table' 	=> $EnderecoRegiao->databaseTable.'.'.$EnderecoRegiao->tableSchema.'.'.$EnderecoRegiao->useTable,
					'alias'		=> 'EnderecoRegiao',
					'type'		=> 'LEFT',
					'conditions'=> 'EnderecoRegiao.codigo = ConfiguracaoComissaoLog.codigo_endereco_regiao',
				),
				array(
					'table' 	=> $NProduto->databaseTable.'.'.$NProduto->tableSchema.'.'.$NProduto->useTable,
					'alias'		=> 'NProduto',
					'type'		=> 'LEFT',
					'conditions'=> 'NProduto.codigo = ConfiguracaoComissaoLog.codigo_produto_naveg',
				),
				array(
					'table' 	=> $Usuario->databaseTable.'.'.$Usuario->tableSchema.'.'.$Usuario->useTable,
					'alias'		=> 'UsuarioInclusao',
					'type'		=> 'LEFT',
					'conditions'=> 'UsuarioInclusao.codigo = ConfiguracaoComissaoLog.codigo_usuario_inclusao',
				),
				array(
					'table' 	=> $Usuario->databaseTable.'.'.$Usuario->tableSchema.'.'.$Usuario->useTable,
					'alias'		=> 'UsuarioAlteracao',
					'type'		=> 'LEFT',
					'conditions'=> 'UsuarioAlteracao.codigo = ConfiguracaoComissaoLog.codigo_usuario_alteracao',
				),
			),
			'fields' 	 => $fields,
			'conditions' => $conditions,
			'limit'      => 50,
			'order'      => array('EnderecoRegiao.descricao' )
		);
		$listagem = $this->paginate('ConfiguracaoComissaoLog');
		$this->set(compact('listagem'));
	}

	function atualizar_por_corretora($codigo){
		$this->loadModel('ConfiguracaoComissaoCorre');
		$this->loadModel('ProdutoServico');
		$this->pageTitle = "Atualizar Configuração por Corretora";

		if(!empty($this->data)) {
			$this->data['ConfiguracaoComissaoCorre']['codigo_corretora'] = $this->data['ConfiguracaoComissaoCorre']['codigo_corretora_dialog'];
			if($this->ConfiguracaoComissaoCorre->atualizar($this->data)){
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
				if(isset($this->ConfiguracaoComissaoCorre->validationErrors['codigo_corretora']))
					$this->ConfiguracaoComissaoCorre->validationErrors['codigo_corretora_dialog_visual'] = $this->ConfiguracaoComissaoCorre->validationErrors['codigo_corretora'];
			}
		} else {
			$this->ConfiguracaoComissaoCorre->bindCorretora();
			$this->data = $this->ConfiguracaoComissaoCorre->carregar($codigo);
			$this->data['ConfiguracaoComissaoCorre']['codigo_corretora_dialog'] = $this->data['ConfiguracaoComissaoCorre']['codigo_corretora'];
			$this->data['ConfiguracaoComissaoCorre']['codigo_corretora_dialog_visual'] = $this->data['Corretora']['nome'];
			$this->data['ConfiguracaoComissaoCorre']['percentual_impostos'] = number_format ( $this->data['ConfiguracaoComissaoCorre']['percentual_impostos'] , 2 , ',' , '' );
			$this->data['ConfiguracaoComissaoCorre']['percentual_comissao'] = number_format ( $this->data['ConfiguracaoComissaoCorre']['percentual_comissao'] , 2 , ',' , '' );
			$this->data['ConfiguracaoComissaoCorre']['preco_de'] = number_format ( $this->data['ConfiguracaoComissaoCorre']['preco_de'] , 2 , ',' , '' );
			$this->data['ConfiguracaoComissaoCorre']['preco_ate'] = number_format ( $this->data['ConfiguracaoComissaoCorre']['preco_ate'] , 2 , ',' , '' );
		}
		$servicos = $this->ProdutoServico->servicosPorProduto($this->data['ConfiguracaoComissaoCorre']['codigo_produto']);
 
		$this->set(compact('servicos'));
		$this->carregarCombosCorretora();
	}

	function historico_config_comissao_corre($codigo){
		$this->loadModel('ConfiguracaoComissaoCorre');
		$this->loadModel('ConfigComissaoCorreLog');
		$configComissaoCorre = $this->ConfiguracaoComissaoCorre->carregar($codigo);
		$filtro = array(
			'codigo_conf_comissoes_corretora' => $configComissaoCorre['ConfiguracaoComissaoCorre']['codigo'],
			'codigo_produto' => $configComissaoCorre['ConfiguracaoComissaoCorre']['codigo_produto'],
			'codigo_servico' => $configComissaoCorre['ConfiguracaoComissaoCorre']['codigo_servico']			
		);
		
		$this->paginate['ConfigComissaoCorreLog'] = $this->ConfigComissaoCorreLog->listaPorCorretoraLog($filtro);
		$listagem = $this->paginate('ConfigComissaoCorreLog');
		$this->set(compact('listagem'));
	}
	
	function excluir($codigo){
		if($this->ConfiguracaoComissao->delete($codigo))
			$this->BSession->setFlash('delete_success');
		else
			$this->BSession->setFlash('delete_error');

		$this->redirect(array('controller' => 'ConfiguracaoComissoes', 'action' => 'index'));
	}

	function excluir_por_corretora($codigo){
		$this->loadModel('ConfiguracaoComissaoCorre');
		if($this->ConfiguracaoComissaoCorre->delete($codigo))
			$this->BSession->setFlash('delete_success');
		else
			$this->BSession->setFlash('delete_error');

		$this->redirect(array('controller' => 'ConfiguracaoComissoes', 'action' => 'por_corretora'));
	}
	
}