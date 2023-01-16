<?php
class EsocialController extends AppController 
{
	public $name = 'Esocial';
	public $helpers = array('BForm', 'Html', 'Ajax');

	const TIPO_ARQUIVO_S2220 = 1;//codigo do tipo correspondente na base de dados
	const TIPO_ARQUIVO_S2221 = 2;//codigo do tipo correspondente na base de dados
	const TIPO_ARQUIVO_S2210 = 3;//codigo do tipo correspondente na base de dados
	const TIPO_ARQUIVO_S2240 = 4;//codigo do tipo correspondente na base de dados
	const TIPO_ARQUIVO_S2230 = 5;//codigo do tipo correspondente na base de dados

	/**
	 * [$uses description]
	 * 
	 * atributo para instanciar as classes models
	 * 
	 * @var array
	 */
	var $uses = array(
		'Esocial',
		'PedidoExame', 
		'Exame',
		'Setor',
		'Cargo',
		'Cliente',
		'Funcionario',
		'ClienteFuncionario',
		'GrupoEconomico',
		'GrupoEconomicoCliente',
		'ClienteEndereco',
		'ItemPedidoExame',
		'TipoNotificacao',
		'PedidoNotificacao',
		'ClienteProduto',
		'ClienteProdutoServico2',
		'EnderecoEstado',
		'ClienteEndereco',
		'EnderecoCidade',
		'TipoExamePedido',
		'FornecedorGradeAgenda',
		'AgendamentoExame',
		'StatusPedidoExame',
		'MailerOutbox',
		'FornecedorEndereco',
		'FornecedorContato',
		'AgendamentoSugestao',
		'ListaDePrecoProdutoServico',
		'ListaDePreco',
		'TipoNotificacaoValor', 
		'PedidoLote',
		'FuncionarioContato',
		'ClienteContato',
		'MotivoCancelamento',
		'FuncionarioSetorCargo',
		'ItemPedidoExameBaixa',
		'Configuracao',
		'OrdemServico',
		'Processamento',
		'ProcessamentoPedidoExame',
		'ProcessamentoStatus',
		'Cat',
		'ProcessamentoCat',
		'GrupoExposicao',
		'GrupoExpoProcessamento',
		'Atestado',
		'AtestadoProcessamento',
		'MensageriaEsocial',
		'IntEsocialEventos',
		'IntEsocialTipoEvento',
		'IntEsocialCertificado'
	);

	/**
	 * [beforeFilter description]
	 * 
	 * liberando os metodo para acessar precisar estar logado
	 * 
	 * @return [type] [description]
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array('*'));
	}//FINAL FUNCTION beforeFilter
    /**
     * TABELA S2220
    */
	/**
	 * [s2220 description]
	 * 
	 * metodo para montar a tela do s2220
	 * 
	 * @return [type] [description]
	 */
	public function s2220()
	{

		$this->pageTitle = 'Tabela S-2220';
		$filtros = $this->Filtros->controla_sessao($this->data, 'Esocial');
		
		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}
		
		//verifica para seta a data do começo do mes padrao
		if(empty($this->data['Esocial']['data_inicio'])) {
			//seta as datas
			$filtros['data_inicio'] = '01/'.date('m/Y');
			$filtros['data_fim'] = date('d/m/Y');
			$filtros['tipo_periodo'] = 'I';
		}

		$this->data['Esocial'] = $filtros;

		// $this->set(compact('incio','fim'));
		$this->carrega_combos_grupo_economico();

	}//fim s2220

	/**
	 * [carrega_combos_grupo_economico description]
	 * 
	 * metodo para carregar os combos 
	 * 
	 * @param  [type] $model [description]
	 * @return [type]        [description]
	 */
	public function carrega_combos_grupo_economico() 
	{

		$codigo_cliente = "";
		$cargos 		= "";
		$unidades 		= "";
		$setores 		= "";

		if(isset($this->data['Esocial']['codigo_cliente'])) {

			$this->loadModel('Cargo');
			$this->loadModel('Setor');
			$this->loadModel('GrupoEconomico');

			$codigo_cliente = $this->data['Esocial']['codigo_cliente'];

	    	if(!empty($codigo_cliente)){
				$codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
	    	}

			$unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
			$setores = $this->Setor->lista($codigo_cliente);
			$cargos = $this->Cargo->lista($codigo_cliente);

		}

		$this->set(compact('unidades', 'setores', 'cargos'));

	}//fim carrega_combos_grupo_economico

	/**
	 * [s2220_listagem description]
	 * 
	 * metodo para realizar a listagem dos dados que foram filtrados no metodo s2220
	 * 
	 * @return [type] [description]
	 */
	public function s2220_listagem()
	{

		$this->layout = 'ajax';


		$filtros = $this->Filtros->controla_sessao($this->data, 'Esocial');
		$authUsuario = $this->BAuth->user();
		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$mensageria = false;
		$listagem = array();
		if (!empty($filtros['codigo_cliente'])) {
			
			//verifica se tem permissao para mensageria
			$mensageria = $this->MensageriaEsocial->get_servico_assinatura($filtros['codigo_cliente']);

			// debug($filtros);exit;

			//monta os filtros
			$conditions = $this->Esocial->converteFiltroEmCondition($filtros);
			//sempre tem que pegar os pedidos que foram todo baixado
			$conditions['PedidoExame.codigo_status_pedidos_exames'] = 3;

			$dados_s2220 = $this->Esocial->getAllS2220ForXml($conditions);

			//varre os dados para aplicar a validação dos campos obrigatorios
			if(!empty($dados_s2220)) {

				foreach($dados_s2220 AS $dados) {

					//monta o status da integracao
					$integracao = array();
					$integracao = array(
						'codigo_evento' => $dados[0]['codigo_int_esocial_evento'],
						'codigo_esocial_status' => $dados[0]['codigo_int_esocial_status'],
						'descricao_esocial_status' => $dados[0]['descricao_esocial_status'],
					);

					//valida a regra do layout s2220
					$validacao = array();
					$validacao = $this->Esocial->valida_regra_campos_s2220($dados);
					
					//monta a listagem
					$listagem[] = array(
						"PedidoExame" => array(
							"codigo" => $dados[0]['codigo'],
							"codigo_cliente" => $dados[0]['codigo_cliente'],
						),
						"Funcionario" => array(
							"nome" => $dados[0]['nome'],
							"cpf" => $dados[0]['cpf'],
						),
						"ClienteFuncionario" => array("matricula" => $dados[0]['matricula']),
						0 => array('data_baixa' => $dados[0]['data_baixa']),
						"validacao" => $validacao,
						"ItemPedidoExameBaixa" => $dados[0]['data_realizacao_exame'],
						"integracao" => $integracao,
					);
				}//fim foreeach de validacao

				// debug($listagem);exit;

			}//fim empty dados_s2220

			if(isset($filtros['status_xml'])) {
				if($filtros['status_xml'] == 'X'){//se tiver inconsistencias

					foreach($listagem as $key_list => $dado_listagem){
						if(empty($dado_listagem['validacao'])){
							unset($listagem[$key_list]);
						}
					}
				} else if($filtros['status_xml'] == 'D'){//se tiver disponivel para download
					foreach($listagem as $key_list => $dado_listagem){
						if(!empty($dado_listagem['validacao'])){
							unset($listagem[$key_list]);
						}
					}
				}
			}
			
			$this->Filtros->limpa_sessao($this->Esocial->name);

		} //fim if codigo_cliente

		//seta para a view
		$this->set(compact('listagem','mensageria'));
		
	}//fim s2220

	/**
	 * [s2220_gerar description]
	 * 
	 * metodo para gerar o xml passado unitario
	 * 
	 * @return [type] [description]
	 */
	public function s2220_gerar($codigo_pedido_exame)
	{
        $this->layout = false;
        self::gerar_xml('s2220', $codigo_pedido_exame);
        exit;
	}

	/**
	 * [s2220_gerar_zip description]
	 * 
	 * metodo para gerar os xmls e zipar
	 * 
	 * @return [type] [description]
	 */
	public function s2220_gerar_zip(){
	    $this->layout = false;	 
	    self::gerar_zip('s2220', $this->data['Esocial']);
        $this->redirect(array('controller' => 'esocial','action' => 's2220'));
    }
    /**
     * FIM TABELA S2220
     */

    /**
     * COMMON METHODS
    */
	public function gerar_zip($tab_id, array $esocial_data)
	{		
        $codigo_processamento_tipo_arquivo = 0;
        if(strtolower($tab_id) == 's2220')
            $codigo_processamento_tipo_arquivo = self::TIPO_ARQUIVO_S2220;
        if(strtolower($tab_id) == 's2221')
            $codigo_processamento_tipo_arquivo = self::TIPO_ARQUIVO_S2221;
        if(strtolower($tab_id) == 's2210')
            $codigo_processamento_tipo_arquivo = self::TIPO_ARQUIVO_S2210; 
        if(strtolower($tab_id) == 's2240')
            $codigo_processamento_tipo_arquivo = self::TIPO_ARQUIVO_S2240;
        if(strtolower($tab_id) == 's2230')
            $codigo_processamento_tipo_arquivo = self::TIPO_ARQUIVO_S2230;
        if($codigo_processamento_tipo_arquivo == 0)
            throw new Exception("Especifique um codigo de tabela do e-social válida!");

		//popula a tabela para saber quais pedidos irá gerar os xmls
		$dados = $esocial_data;

		if(strtolower($tab_id) == 's2240'){
			//tratamento sobre o que esta vindo da view	
			foreach ($dados as $key => $value) {
				
				if( !isset($dados[$key]['codigo']) && empty($dados[$key]['codigo']) ){//os arrays que nao vierem com o codigo grupo exposicao devem ser excluidos				
					unset($dados[$key]['codigo_funcionario']);
					unset($dados[$key]);
				}
			}					
		}

		//verifica se existe dados selecionados
		if(!empty($dados)) {

			if(strtolower($tab_id) == 's2210') {

				$joins = $this->Cat->returnJoinsCat();
			
			} else if(strtolower($tab_id) == 's2240') {		
				
				$joins = $this->GrupoExposicao->returnJoinsEsocial();
			
			} else if(strtolower($tab_id) == 's2230') {		
				
				$joins = $this->Atestado->returnJoinsEsocial();
			
			} else {
				//pega o codigo da matriz pelo codigo do pedido
				$joins = array(
					array(
						'table' => 'RHHealth.dbo.grupos_economicos_clientes',
						'alias' => 'GrupoEconomicoCliente',
						'type' => 'INNER',
						'conditions' => 'PedidoExame.codigo_cliente = GrupoEconomicoCliente.codigo_cliente',
					),
					array(
						'table' => 'RHHealth.dbo.grupos_economicos',
						'alias' => 'GrupoEconomico',
						'type' => 'INNER',
						'conditions' => 'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo',
					),
				);				
			}


			if(strtolower($tab_id) == 's2230') {
				$fields = array('ClienteFuncionario.codigo_cliente_matricula');
			} else {
				$fields = array('GrupoEconomico.codigo_cliente');
			}

			if(strtolower($tab_id) == 's2210'){//busca do CAT
				//executa a query
				$cliente = $this->Cat->find('first', array('fields' => $fields,'joins' => $joins,'conditions' => array('Cat.codigo' => $dados[key($dados)]['codigo'])));
			
			} else if(strtolower($tab_id) == 's2240'){//busca do Grupo Exposicao/PGR
				//executa a query				
				$cliente = $this->GrupoExposicao->find('first', array('fields' => $fields,'joins' => $joins,'conditions' => array('GrupoExposicao.codigo' => $dados[key($dados)]['codigo'])));
			} else if(strtolower($tab_id) == 's2230'){//busca do Atestado
				//executa a query				
				$cliente = $this->Atestado->find('first', array('fields' => $fields,'joins' => $joins,'conditions' => array('Atestado.codigo' => $dados[key($dados)]['codigo'])));
				debug($cliente);
			} else {
				//executa a query
				$cliente = $this->PedidoExame->find('first', array('fields' => $fields,'joins' => $joins,'conditions' => array('PedidoExame.codigo' => $dados[key($dados)]['codigo'])));
			}

			if(strtolower($tab_id) == 's2230') {
				$codigo_cliente = $cliente['ClienteFuncionario']['codigo_cliente_matricula'];
			} else {
				$codigo_cliente = $cliente['GrupoEconomico']['codigo_cliente'];
			}
					
			//monta o array para inserir na tabela de processamento
			$processamento = array(
				'Processamento' => array(
					'codigo_cliente' => $codigo_cliente,
					'codigo_processamento_status' => 1, //aguardando
					'codigo_processamento_tipo_arquivo' => $codigo_processamento_tipo_arquivo,
					'baixado' => 0,
					'codigo_empresa' => $this->authUsuario['Usuario']['codigo_empresa'],				
					'deletado' => null,				
					'caminho' => null				
				)
			);

			// verifica se incluiu na tabela de processamento
			if(!$this->Processamento->incluir($processamento)) {	
				return false;
			}

			//seta o codigo do processamento incluido
			$codigo_processamento = $this->Processamento->id;

			//variavel de erro
			$erro = array();

			//varre os pedidos que devem ser gerados os xmls
			foreach ($dados as $dado) {

				if(strtolower($tab_id) == 's2210'){
					$codigo_cat = $dado['codigo'];
					$proc_model = array(
						'ProcessamentoCat' => array(
							'codigo_processamento' => $codigo_processamento,
							'codigo_cat' => $codigo_cat,
							'codigo_cliente' => $codigo_cliente,
							'xml_gerado' => 0, //campo para indicar se o xml foi gerado ou nao
							'codigo_empresa' => $this->authUsuario['Usuario']['codigo_empresa']
						)
					);
					$Process = $this->ProcessamentoCat;
				} else if(strtolower($tab_id) == 's2240'){
					$codigo_grupo_exposicao = $dado['codigo'];
					$proc_model = array(
						'GrupoExpoProcessamento' => array(
							'codigo_processamento' => $codigo_processamento,
							'codigo_grupo_exposicao' => $codigo_grupo_exposicao,
							'codigo_cliente' => $codigo_cliente,
							'xml_gerado' => 0, //campo para indicar se o xml foi gerado ou nao
							'codigo_empresa' => $this->authUsuario['Usuario']['codigo_empresa'],
							'codigo_funcionario' => $dado['codigo_funcionario']
						)
					);
					$Process = $this->GrupoExpoProcessamento;
				} else if(strtolower($tab_id) == 's2230'){
					$codigo_atestado = $dado['codigo'];		
					$proc_model = array(
						'AtestadoProcessamento' => array(
							'codigo_processamento' => $codigo_processamento,
							'codigo_atestado' => $dado['codigo'],
							'codigo_cliente' => $codigo_cliente,
							'xml_gerado' => 0, //campo para indicar se o xml foi gerado ou nao
							'codigo_empresa' => $this->authUsuario['Usuario']['codigo_empresa']							
						)
					);
					$Process = $this->AtestadoProcessamento;
				} else {
					//pega os codigos selecionados
					$codigo_pedido_exame = $dado['codigo'];
					//monta o que vai inserir na tabela de processamento pedidos exames
					$proc_model = array(
						'ProcessamentoPedidoExame' => array(
							'codigo_processamento' => $codigo_processamento,
							'codigo_pedido_exame' => $codigo_pedido_exame,
							'codigo_cliente' => $codigo_cliente,
							'xml_gerado' => 0, //campo para indicar se o xml foi gerado ou nao
							'codigo_empresa' => $this->authUsuario['Usuario']['codigo_empresa']
						)
					);

					$Process = $this->ProcessamentoPedidoExame;
				}

				//verifica se inseriu
				if(!$Process->incluir($proc_model)) {

					$erro['status'] = 'false';
					
					if(strtolower($tab_id) == 's2210'){
						$erro['descricao'] = 'Erro ao inserir na tabela de processamento Cat';
						$erro['codigo_cat'] = $codigo_cat;
					} else if(strtolower($tab_id) == 's2240'){
						$erro['descricao'] = 'Erro ao inserir na tabela de processamento Grupo Exposicao';
						$erro['codigo_grupo_exposicao'] = $codigo_grupo_exposicao;
					} else if(strtolower($tab_id) == 's2230'){
						$erro['descricao'] = 'Erro ao inserir na tabela de processamento Atestados';
						$erro['codigo_atestado'] = $codigo_atestado;
					} else {
						$erro['descricao'] = 'Erro ao inserir na tabela de processamento pedido exame';
						$erro['codigo_pedido_exame'] = $codigo_pedido_exame;		
					}


					//atualiza a tabela de processamento colocando com o status 4 suspenso
					$this->Processamento->codigo_processamento_status = 4;
					$processamento = array(
						'Processamento' => array(
							'codigo' => $codigo_processamento,
							'codigo_processamento_status' => 4
						)
					);
					
					$this->Processamento->atualizar($processamento);
				}
				
			}//fim foreach
						
			// verifica se nao ocorreu nenhum erro para executar o processo de geracao do zip
			if(empty($erro)) {

				//dados do usuario
				$usuario = $this->BAuth->user();
				//executa em segundo plano o comando

				Comum::execInBackground(ROOT . DS . 'cake' . DS . 'console' . DS . 'cake -app ' . APP . ' esocial gerar_zip '.$codigo_processamento);
			}
		}//fim dados

        $this->BSession->setFlash(array('alert alert-success', 'Olá estamos preparando o(s) arquivo(s) solicitados(s), você poderá consulta-los(s) clicando <a href="/portal/processamentos">AQUI</a>!'));
		//$this->redirect(array('controller' => 'esocial','action' => 's2220'));

	}//fim gerar_zip
	//parametro codigo pode ser codigo pedido exame, codigo_cat ou outros futuros
    private function gerar_xml($tab_id, $codigo, $codigo_funcionario = null){
        //$tab_id = s2220, s2221 e etc
        $method = "gerar_".$tab_id;

        if(!empty($codigo_funcionario)){
        	$xml = $this->Esocial->{$method}($codigo, $codigo_funcionario);
        } else {
        	$xml = $this->Esocial->{$method}($codigo);
        }

        //pega o xml gravado
		$dado_xml = $xml;
		//lendo xml
		$read_xml = simplexml_load_string($dado_xml);

		//switch para direcionamento da leitura e montagem do txt2 a partir do xml corretamente
		switch ($tab_id) {
			
			case 's2210': //s2210
				$xmlns = 'http://www.esocial.gov.br/schema/evt/evtCAT/v_S_01_00_00';
				//pegando o atributo ID
				$attributeID = $this->MensageriaEsocial->xml_attribute($read_xml->evtCAT, 'Id');
				break;
			case 's2220': //s2220
				$xmlns = 'http://www.esocial.gov.br/schema/evt/evtMonit/v_S_01_00_00';
				//pegando o atributo ID
				$attributeID = $this->MensageriaEsocial->xml_attribute($read_xml->evtMonit, 'Id');
				break;
			case 's2230': //s2230
				$xmlns = 'http://www.esocial.gov.br/schema/evt/evtAfastTemp/v_S_01_00_00';
				//pegando o atributo ID
				$attributeID = $this->MensageriaEsocial->xml_attribute($read_xml->evtAfastTemp, 'Id');
				break;
			case 's2240': //s2240
				$xmlns = 'http://www.esocial.gov.br/schema/evt/evtExpRisco/v_S_01_00_00';
				//pegando o atributo ID
				$attributeID = $this->MensageriaEsocial->xml_attribute($read_xml->evtExpRisco, 'Id');
				break;
			case 's3000': //s3000
				$xmlns = 'http://www.esocial.gov.br/schema/evt/evtExclusao/v_S_01_00_00';
				//pegando o atributo ID
				$attributeID = $this->MensageriaEsocial->xml_attribute($read_xml->evtExclusao, 'Id');
				break;
		}//fim geracao do txt2 por evento

		//add o xmlns na tag esocial
		$obj_xml = new SimpleXMLElement($dado_xml);
		$obj_xml->addAttribute('xmlns', $xmlns);
		
		//formata o xml que vai ser enviado para a tecnospeed
		$dado_xml_envio = $obj_xml->asXML();
		$dado_xml_envio = str_replace("\n", "", $dado_xml_envio);
		
		$xml = str_replace('<?xml version="1.0"?>', '', $dado_xml_envio);

        ob_clean();
        header("Content-type: text/xml");
        header("Content-Type: application/force-download;charset=UTF-8");
        header('Content-Disposition: attachment; filename="esocial_'.$tab_id.'_'.date('YmdHis').'.xml"');
        header('Pragma: no-cache');

        //download o xml
        echo $xml;
    }
    /**
     * FIM COMMON METHODS
     */

    /**
     * TABELA S2221
    */
    public function s2221()
    {
        $this->s2220();
        $this->pageTitle = "Tabela S-2221";
    }

    public function s2221_gerar($codigo_pedido_exame){
        $this->layout = false;
        self::gerar_xml('s2221', $codigo_pedido_exame);
        exit;
    }

    public function s2221_listagem(){
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, 'Esocial');
        $authUsuario = $this->BAuth->user();
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $listagem = array();
        if (!empty($filtros['codigo_cliente'])) {
            $conditions = $this->Esocial->converteFiltroEmCondition($filtros);
            $this->paginate['PedidoExame'] = $this->Esocial->getAllS2221ForXml($conditions, true);

            $listagem = $this->paginate('PedidoExame');

            $this->Filtros->limpa_sessao($this->Esocial->name);
        }
        $this->set(compact('listagem'));
    }

    public function s2221_gerar_zip(){
        $this->layout = false;
        self::gerar_zip('s2221', $this->data['Esocial']);
        $this->redirect(array('controller' => 'esocial','action' => 's2221'));
    }

    /**
     * FIM TABELA S2221
     */

    public function s2210(){
    	$this->s2220();
        $this->pageTitle = "Tabela S-2210";
    }

	public function s2210_listagem(){
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, 'Esocial');
		$authUsuario = $this->BAuth->user();
		
		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$mensageria = false;
		$listagem = array();
		if (!empty($filtros['codigo_cliente'])) {

			//verifica se tem permissao para mensageria
			$mensageria = $this->MensageriaEsocial->get_servico_assinatura($filtros['codigo_cliente']);

			$conditions = $this->Cat->FiltroEmConditionCat($filtros);
			// $this->paginate['Cat'] = $this->Esocial->getAllS2210ForXml($conditions, true);
			// $listagem = $this->paginate('Cat');
			// pr($this->Cat->find('sql', $this->paginate['Cat']));exit;

			$dados_s2210 = $this->Esocial->getAllS2210ForXml($conditions);
			// debug($dados_s2210);exit;

			//varre os dados para aplicar a validação dos campos obrigatorios
			if(!empty($dados_s2210)) {

				foreach($dados_s2210 AS $dados) {

					//monta o status da integracao
					$integracao = array();
					$integracao = array(
						'codigo_evento' => $dados[0]['codigo_int_esocial_evento'],
						'codigo_esocial_status' => $dados[0]['codigo_int_esocial_status'],
						'descricao_esocial_status' => $dados[0]['descricao_esocial_status'],
					);

					//valida a regra do layout s2210
					$validacao = array();
					$validacao = $this->Esocial->valida_regra_campos_s2210($dados);
					// debug($validacao);
					
					//monta a listagem
					$listagem[] = array(
						"Cat" => array(
							"codigo" => $dados[0]['codigo_cat'],
							"codigo_cliente" => $dados[0]['codigo_cliente'],
						),
						"Funcionario" => array(
							"nome" => $dados[0]['nome_funcionario'],
							"cpf" => $dados[0]['cpf_funcionario'],
							"codigo" => $dados[0]['codigo_funcionario'],
						),
						"ClienteFuncionario" => array(
							"matricula" => $dados[0]['matricula'],
						),
						"validacao" => $validacao,
						"integracao" => $integracao,
					);
				}//fim foreeach de validacao

				// debug($listagem);exit;

			}//fim empty dados_s2210

			if(isset($filtros['status_xml'])) {
				if($filtros['status_xml'] == 'X'){//se tiver inconsistencias

					foreach($listagem as $key_list => $dado_listagem){
						if(empty($dado_listagem['validacao'])){
							unset($listagem[$key_list]);
						}
					}
				} else if($filtros['status_xml'] == 'D'){//se tiver disponivel para download
					foreach($listagem as $key_list => $dado_listagem){
						if(!empty($dado_listagem['validacao'])){
							unset($listagem[$key_list]);
						}
					}
				}
			}

		}
		$this->set(compact('listagem','mensageria'));
	}

	public function s2210_gerar_zip(){
        $this->layout = false;        
        self::gerar_zip('s2210', $this->data['Esocial']);
        $this->redirect(array('controller' => 'esocial','action' => 's2210'));
    }

    public function s2210_gerar($codigo_cat){
        $this->layout = false;
        self::gerar_xml('s2210', $codigo_cat);
        exit;
    }

    public function s2240(){
    	$this->s2220();
        $this->pageTitle = "Tabela S-2240";
    }

    public function s2240_listagem(){
    	$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, 'Esocial');
		$authUsuario = $this->BAuth->user();
		
		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		// debug($filtros);exit;

		$mensageria = false;
		$listagem = array();
		if (!empty($filtros['codigo_cliente'])) {
			
			//verifica se tem permissao para mensageria
			$mensageria = $this->MensageriaEsocial->get_servico_assinatura($filtros['codigo_cliente']);

			$conditions = $this->GrupoExposicao->ConditionXmlS2240($filtros);

			$dados_s2240 = $this->Esocial->getAllS2240ForXml($conditions,null, $filtros);
			// debug($dados_s2240);exit;

			//varre os dados para aplicar a validação dos campos obrigatorios
			if(!empty($dados_s2240)) {

				foreach($dados_s2240 AS $dados) {

					//monta o status da integracao
					$integracao = array();
					$integracao = array(
						'codigo_evento' => $dados[0]['codigo_int_esocial_evento'],
						'codigo_esocial_status' => $dados[0]['codigo_int_esocial_status'],
						'descricao_esocial_status' => $dados[0]['descricao_esocial_status'],
					);

					//valida a regra do layout s2220
					$validacao = array();
					$validacao = $this->Esocial->valida_regra_campos_s2240($dados);
					// debug($validacao);
					
					//monta a listagem
					$listagem[] = array(
						"GrupoExposicao" => array(
							"codigo" => $dados[0]['codigo'],
							"codigo_cliente" => $dados[0]['codigo_cliente'],
						),
						"ClienteSetor" => array(
							"codigo_cliente_alocacao" => $dados[0]['codigo_cliente_alocacao'],
						),
						"Setor" => array(
							"descricao" => $dados[0]['setor'],
						),
						"Cargo" => array(
							"descricao" => $dados[0]['cargo'],
						),

						"Funcionario" => array(
							"codigo" => $dados[0]['codigo_funcionario'],
							"nome" => $dados[0]['nome'],
							"cpf" => $dados[0]['cpf'],
						),
						"ClienteFuncionario" => array(
							"matricula" => $dados[0]['matricula'],
						),
						0 => array('data_vigencia' => $dados[0]['data_vigencia']),
						"validacao" => $validacao,
						"integracao" => $integracao,
					);
				}//fim foreeach de validacao

				// debug($listagem);exit;

			}//fim empty dados_s2240

			if(isset($filtros['status_xml'])) {
				if($filtros['status_xml'] == 'X'){//se tiver inconsistencias

					foreach($listagem as $key_list => $dado_listagem){
						if(empty($dado_listagem['validacao'])){
							unset($listagem[$key_list]);
						}
					}
				} else if($filtros['status_xml'] == 'D'){//se tiver disponivel para download
					foreach($listagem as $key_list => $dado_listagem){
						if(!empty($dado_listagem['validacao'])){
							unset($listagem[$key_list]);
						}
					}
				}
			}
		}
		$this->set(compact('listagem','mensageria'));
    }

    public function s2240_gerar_zip(){
        $this->layout = false;        
        self::gerar_zip('s2240', $this->data['Esocial']);
        $this->redirect(array('controller' => 'esocial','action' => 's2240'));
    }

    public function s2240_gerar($codigo_grupo_exposicao, $codigo_funcionario = null){
        $this->layout = false;
        self::gerar_xml('s2240', $codigo_grupo_exposicao, $codigo_funcionario);
        exit;
    }

    public function busca_grupo_sem_atividades($codigo_grupo_exposicao, $codigo_funcionario = null) {
    	$this->autoRender = false;
    	// debug($codigo_grupo_exposicao);	
    	$buscar_grupo_exposicao = $this->GrupoExposicao->find('list', array('fields' => 'codigo', 'conditions' => array('GrupoExposicao.codigo IN ('.$codigo_grupo_exposicao.')', 'CONVERT(VARCHAR, GrupoExposicao.descricao_atividade) = \'\'')));

    	if($buscar_grupo_exposicao){
    		$return = 1;
    		$codigos_grupo_exposicao = implode(',',$buscar_grupo_exposicao);
    		
    		if(!empty($codigo_funcionario)){
    			$msg = 'Este Código de Grupo de Exposicao esta com a Descrição das atividades não preenchidas, por favor, recomendamos antes de gerar o Xml, ajustar este campo. Deseja Continuar mesmo assim?';
    		} else {
    			$msg = 'Existem Códigos de Grupo de Exposicao no seu PGR que estão com a Descrição das atividades não preenchidas, por favor, recomendamos antes de gerar o Xml, ajustar este campo. Deseja Continuar mesmo assim? Senão, estes são codigos para você atualizar: '.$codigos_grupo_exposicao;
    		}
    	} else {
    		$return = 0;
    	}
    	return json_encode(array('return' => $return, 'msg' => !isset($msg) ? '0' : $msg));
    }

    public function s2230(){

    	$this->s2220();
        
        $this->pageTitle = "Tabela S-2230";
    }

    public function s2230_listagem(){
    	
    	$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, 'Esocial');
		
		$authUsuario = $this->BAuth->user();
		
		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$mensageria = false;
		$listagem = array();
		if (!empty($filtros['codigo_cliente'])) {
			
			//verifica se tem permissao para mensageria
			$mensageria = $this->MensageriaEsocial->get_servico_assinatura($filtros['codigo_cliente']);
			
			$conditions = $this->Atestado->ConditionXmlS2230($filtros);
			
			$dados_s2230 = $this->Esocial->getAllS2230ForXml($conditions);
			// debug($dados_s2230);exit;

			if(!empty($dados_s2230)) {
			
				//valida a regra do layout s2230
				$validacao = array();

				foreach($dados_s2230 as $dados) {

					//monta o status da integracao
					$integracao = array();
					$integracao = array(
						'codigo_evento' => $dados[0]['codigo_int_esocial_evento'],
						'codigo_esocial_status' => $dados[0]['codigo_int_esocial_status'],
						'descricao_esocial_status' => $dados[0]['descricao_esocial_status'],
					);

					$dados = $dados[0];
					// valida a regra do layout s2220
					$validacao = array();
					$validacao = $this->Esocial->valida_regra_campos_s2230($dados);
					
					//monta a listagem
					$listagem[] = array(
						"FuncionarioSetorCargo" => array(
							"codigo_cliente_alocacao" => $dados['codigo_cliente_alocacao'],
						),
						"Cliente" => array(
							"nome_fantasia" => $dados['nome_fantasia'],							
						),
						"Setor" => array(
							"descricao" => $dados['setor']
						),

						"Cargo" => array(
							"descricao" => $dados['cargo']
						),
						"Funcionario" => array(
							"nome" => $dados['nome_funcionario'],
							"cpf" => $dados['cpf_funcionario']
						),
						"ClienteFuncionario" => array(
							"matricula" => $dados['matricula']
						),

						"Atestado" => array(
							"codigo" => $dados['codigo_atestado']
						),

						"validacao" => $validacao,
						"integracao" => $integracao,
					);
				}//fim foreeach de validacao

				if($filtros['status_xml'] == 'X'){//se tiver inconsistencias

					foreach($listagem as $key_list => $dado_listagem){
						if(empty($dado_listagem['validacao'])){
							unset($listagem[$key_list]);
						}
					}
				} else if($filtros['status_xml'] == 'D'){//se tiver disponivel para download
					foreach($listagem as $key_list => $dado_listagem){
						if(!empty($dado_listagem['validacao'])){
							unset($listagem[$key_list]);
						}
					}
				}

			}
		}
		
		$this->set(compact('listagem','mensageria'));
    }

    public function s2230_gerar_zip(){
        $this->layout = false;        
        self::gerar_zip('s2230', $this->data['Esocial']);
        $this->redirect(array('controller' => 'esocial','action' => 's2230'));
    }

    public function s2230_gerar($codigo_atestado){
        $this->layout = false;
        self::gerar_xml('s2230', $codigo_atestado);
        exit;
    }

    /**
     * [setMensageriaEsocial metodo para gravar os indices passados para integracao da mensageria]
     */
    public function setIntegMensageriaEsocial()
    {
    	$this->layout = 'ajax';

    	//verifica se tem os dados necessários para integracao
    	$tipo_evento = $this->params['form']['tipo_evento'];
    	
    	//param dados indice 0 = codigo_cliente, 1 = codigo que vai ser integrado campo codigo_registro_sistema
    	$dados = $this->params['form']['dados']; //array com os indices de registros que gostaria de integrar

    	if(empty($dados)) {
    		$retorno = array('retorno' => 'false', 'mensagem' => "Dados enviados inválidos");

    		echo json_encode($retorno);
    		exit;
    	}

    	// debug($dados);exit;

    	//variaveis de retorno
    	$retorno = 'true';
    	$mensagem = '';

    	//pega o codigo do tipo de evendo
    	$int_esocial_tipo_evento = $this->IntEsocialTipoEvento->find('first',array('fields' => array('codigo'),'conditions' => array('apelido_descricao' => $tipo_evento) ));
    	
    	//verifica se tem o dado cadastrado
    	if(!empty($int_esocial_tipo_evento)) {
    		$codigo_tipo_evento = $int_esocial_tipo_evento['IntEsocialTipoEvento']['codigo'];

    		//varre os dados
    		foreach($dados AS $d) {
    			//pega os valores do array passado
    			$codigo_cliente = $d[0];
    			$codigo_registro_sistema = $d[1];

    			//status que não podem reenviar o evento
    			// 2 -> aguardando retorno do esocial
    			// 3 -> concluido (obteve o recibo)
    			$esocial_status = array(2,3);

    			$conditions_intEsocialEventos = array('codigo_int_esocial_tipo_evento' => $codigo_tipo_evento,'codigo_registro_sistema' => $codigo_registro_sistema, 'codigo_int_esocial_status' => $esocial_status,"(select codigo from int_esocial_eventos where codigo_int_esocial_eventos_s3000 = [IntEsocialEventos].codigo) IS NULL");

	    		$codigo_funcionario = '';
	    		if($codigo_tipo_evento == 4) { //s2240 pgr por funcionario
	    			$codigo_funcionario = $d[2];

	    			//implementado para correção do erro para o pgr que pode usar o mesmo codigo do grupo exposicaç
	    			$conditions_intEsocialEventos['codigo_funcionario'] = $codigo_funcionario;
	    		}

		        //verifica se é atualizacao ou inclusao
		        $evento = $this->IntEsocialEventos->find('first',array('conditions' => $conditions_intEsocialEventos));

		        // debug($codigo_registro_sistema);
		        // debug($evento);exit;
		        
		        //verifica se ja existe o registro no banco
		        if(empty($evento)) {
		    		

		    		$dados_compl_evento = array('codigo_cliente_funcionario' => null,'codigo_funcionario_setor_cargo' => null,'codigo_funcionario' => null,'codigo_setor' => null,'codigo_cargo' => null,'codigo_cliente_matriz' => null,'codigo_cliente_alocacao' => null,'matricula' => null);

	    			$dados_compl_evento = $this->Esocial->getDadosComplementaresIntegracao($codigo_registro_sistema,$codigo_tipo_evento,$codigo_funcionario);
	    			$dados_compl_evento = $dados_compl_evento[0][0];

	    			##TODO para testes
	    			// $codigo_cliente = "79";
	    			
	    			//pelo codigo do cliente pegar o codigo do certificado ativo
	    			$int_esocial_certificao = $this->IntEsocialCertificado->getCertificadosCliente($codigo_cliente);

	    			// debug($int_esocial_certificao);exit;
	    			if(empty($int_esocial_certificao)) {
	    				$retorno = 'false';
	    				$mensagem .= "O codigo: ". $codigo_registro_sistema." não tem certificado configurado corretamente. Favor verificar com o administrador!\n";
	    				continue;
	    			}

	    			//seta o codigo do certificado
	    			$codigo_int_esocial_certificado = $int_esocial_certificao['IntEsocialCertificado']['codigo'];
	    			$codigo_int_esocial_status = 1; //pendente de envio para o esocial
	    			$ambiente = $int_esocial_certificao['IntEsocialCertificado']['ambiente_esocial'];//pega o ambiente configurado

	    			//pega os dados do evento
	    			$method = "gerar_".$tipo_evento;

	    			if(!empty($codigo_funcionario)){
			        	$dados_evento = $this->Esocial->{$method}($codigo_registro_sistema, $codigo_funcionario,$ambiente);
			        } else {
	    				$dados_evento = $this->Esocial->{$method}($codigo_registro_sistema,$ambiente);
			        }
			        
			        $dados_evento = str_replace("<?xml version='1.0' encoding='UTF-8'?>", "", $dados_evento);
			        // debug($dados_evento);exit;

			        //monta o array para inserir os eventos
			        $dados_int_esocial_eventos = array(
			        	'IntEsocialEventos' => array(
			        		'codigo_cliente' => $codigo_cliente,
			        		'codigo_int_esocial_certificado' => $codigo_int_esocial_certificado,
			        		'codigo_int_esocial_tipo_evento' => $codigo_tipo_evento,
			        		'codigo_int_esocial_status' => $codigo_int_esocial_status,
			        		'codigo_registro_sistema' => $codigo_registro_sistema,
			        		'dados_evento' => $dados_evento,
			        		'ativo' => 1,
			        		'codigo_cliente_funcionario' => $dados_compl_evento['codigo_cliente_funcionario'],
			        		'codigo_funcionario_setor_cargo' => $dados_compl_evento['codigo_funcionario_setor_cargo'],
			        		'codigo_funcionario' => $dados_compl_evento['codigo_funcionario'],
			        		'codigo_setor' => $dados_compl_evento['codigo_setor'],
			        		'codigo_cargo' => $dados_compl_evento['codigo_cargo'],
			        		'codigo_cliente_matriz' => $dados_compl_evento['codigo_cliente_matriz'],
			        		'codigo_cliente_alocacao' => $dados_compl_evento['codigo_cliente_alocacao'],			        		
			        		'matricula' => $dados_compl_evento['matricula']
			        	)
			        );
		        	
		        	// $this->log(print_r($dados_int_esocial_eventos,1),"debug");
		        	// continue;
			        
			        if(!$this->IntEsocialEventos->incluir($dados_int_esocial_eventos)) {
						
						// debug("erro ".$codigo_registro_sistema);

			        	//seta o erro da insercao
			      //   	$retorno = 'false';
	    				// $mensagem .= "Ocorreu um erro ao gravar o codigo: ". $codigo_registro_sistema.". Favor verificar com o administrador!\n";

	    				$retorno[$codigo_registro_sistema] = 'false';
						// $retorno = 'false';
						$mensagem[$codigo_registro_sistema] = "Ocorreu um erro ao gravar o codigo: ". $codigo_registro_sistema.". Favor verificar com o administrador!";

	    				continue;
					}
					$codigo_int_esocial = $this->IntEsocialEventos->id;
		        	
					//envia para a tecnospeed
					$mensageria = $this->MensageriaEsocial->tecnospeed_evento_enviar_xml($codigo_int_esocial);

					// debug($mensageria);exit;
					if(empty($mensageria)) {
						$retorno[$codigo_registro_sistema] = 'false';
						// $retorno = 'false';
						$mensagem[$codigo_registro_sistema] = "Ocorreu um erro na integração com o E-Social!";
					}
					else {
						$retorno[$codigo_registro_sistema] = 'true';
						// $retorno = 'true';
						$mensagem[$codigo_registro_sistema] = $mensageria;
					}
					
		        }//fim empty evento

    		}//fim foreach
    		
    	}//fim verificacao do tipo de evento

    	// debug($retorno);
    	// debug($mensagem);
    	// exit;

    	// debug($this->params['form']);exit;

		$return = array('retorno' => $retorno, 'mensagem' => $mensagem);
		echo json_encode($return);
		exit;


    }//fim setMensageriaEsocial

    public function relatorio_inconsistencias(){

    	$this->pageTitle = 'Relatório Inconsistências';

		$this->data['Esocial'] = array(
			'data_inicio' => '01'.date('m/Y'),
			'data_fim' => date('d/m/Y')
		);

		//campos para extração do relatorio
		$campos = array(	
			's2210' => 'Tabela S-2210',
			's2220' => 'Tabela S-2220',
			's2230' => 'Tabela S-2230',
			's2240' => 'Tabela S-2240',
		);

		$this->carrega_combos_grupo_economico();
		$this->set(compact('campos'));
    }

    public function relatorio_inconsistencias_exportar(){
    	if($this->RequestHandler->isPost()) {

    		$campos = $this->data['Esocial']['to'];

    		if(empty($campos)) {
				$this->BSession->setFlash(array('alert alert-error', 'Favor selecionar um campo para apresentar.'));
				$this->redirect(array('action' => 'relatorio_inconsistencias'));
			}

			$filtros = array(
				'codigo_cliente' 			=> $this->data['Esocial']['codigo_cliente'],
				'codigo_cliente_alocacao' 	=> $this->data['Esocial']['codigo_cliente_alocacao'],
				'codigo_setor' 				=> $this->data['Esocial']['codigo_setor'],
            	'data_inicio' 				=> AppModel::dateToDbDate2($this->data['Esocial']['data_inicio']),
            	'data_fim' 					=> AppModel::dateToDbDate2($this->data['Esocial']['data_fim']),
            	'tipo_periodo' 				=> 'I',
			);

			$this->relatorio_inconsistencias_exportar_excel($campos, $filtros);
    	}
    }

    private function relatorio_inconsistencias_exportar_excel($camposSelecionados, $filtros){

    	$c = array();

    	$campos = array(	
			's2210' => 'Tabela S-2210',
			's2220' => 'Tabela S-2220',
			's2230' => 'Tabela S-2230',
			's2240' => 'Tabela S-2240',
		);

		foreach ($camposSelecionados as $item ) {
			if (array_key_exists($item , $campos)) {
				$c[$item] = utf8_decode($campos[$item]);
			}
		}

		unset($camposSelecionados);
		
		$campos = $c;

		$dados_s2220 = array();
		$dados_s2240 = array();
		$dados_s2210 = array();
		$dados_s2230 = array();
		
		$lista_s2220 = array();
		$lista_s2240 = array();
		$lista_s2210 = array();
		$lista_s2230 = array();

		$lista_s2220_up = array();
		$lista_s2240_up = array();
		$lista_s2210_up = array();
		$lista_s2230_up = array();

		foreach($campos as $key => $campo_selecionado){

			if($key == 's2220'){
				
				$conditions_pe = $this->Esocial->converteFiltroEmCondition($filtros);
				//sempre tem que pegar os pedidos que foram todo baixado
				$conditions_pe['PedidoExame.codigo_status_pedidos_exames'] = 3;

				$dados_s2220 = $this->Esocial->getAllS2220ForXml($conditions_pe);

				if(!empty($dados_s2220)) {

					foreach($dados_s2220 AS $dados) {

						//valida a regra do layout s2220
						$validacao = array();
						$validacao = $this->Esocial->valida_regra_campos_s2220($dados);
						
						//monta a listagem
						$lista_s2220[] = array(
							"evento" => 'S2220',
							"PedidoExame" => array(
								"codigo" => $dados[0]['codigo'],
								"codigo_cliente" => $dados[0]['codigo_cliente'],
							),
							"Cliente" => array(
								'nome_fantasia' => $dados[0]['nome_fantasia']
							),
							"Funcionario" => array(
								"nome" => $dados[0]['nome'],
								"cpf" => $dados[0]['cpf'],
							),
							"validacao" => $validacao
						);
					}//fim foreeach de validacao

					foreach($lista_s2220 as $key_lista => $dados_lista){

						if(empty($dados_lista['validacao'])){//retira os registros que nao tem inconsistencias
							unset($lista_s2220[$key_lista]);
						}
					}// fim lista

					foreach($lista_s2220 as $key1 => $dado_list){

						if(is_array($lista_s2220[$key1]['validacao']) && count($lista_s2220[$key1]['validacao']) > 0){
							
							foreach($dado_list['validacao'] as $key_valid => $dado_validacao){
								$dado_validacao[] = $dado_list;
								$lista_s2220_up[] = $dado_validacao;
							}
						}
					}


				}//fim empty dados_s2220
			}

			if($key == 's2240'){

				$conditions_ge = $this->GrupoExposicao->ConditionXmlS2240($filtros);

				$dados_s2240 = $this->Esocial->getAllS2240ForXml($conditions_ge);

					if(!empty($dados_s2240)) {

						foreach($dados_s2240 AS $dados) {

							//valida a regra do layout s2220
							$validacao = array();
							$validacao = $this->Esocial->valida_regra_campos_s2240($dados);
							
							//monta a listagem
							$lista_s2240[] = array(
								"evento" => 'S2240',
								"GrupoExposicao" => array(
									"codigo" => $dados[0]['codigo'],
									"codigo_cliente" => $dados[0]['codigo_cliente'],
								),
								"ClienteSetor" => array(
									"codigo_cliente_alocacao" => $dados[0]['codigo_cliente_alocacao'],
								),
								"Cliente" => array(
									'nome_fantasia' => $dados[0]['nome_fantasia']
								),
								"Funcionario" => array(
									"codigo" => $dados[0]['codigo_funcionario'],
									"nome" => $dados[0]['nome'],
									"cpf" => $dados[0]['cpf'],
								),
								"validacao" => $validacao
							);
						}//fim foreeach de validacao

						foreach($lista_s2240 as $key_lista => $dados_lista_2240){

							if(empty($dados_lista_2240['validacao'])){//retira os registros que nao tem inconsistencias
								unset($lista_s2240[$key_lista]);
							}
						}// fim lista

						foreach($lista_s2240 as $key1 => $dado_list_s2240){

							if(is_array($lista_s2240[$key1]['validacao']) && count($lista_s2240[$key1]['validacao']) > 0){
								
								foreach($dado_list_s2240['validacao'] as $key_valid => $dado_validacao_2240){
									$dado_validacao_2240[] = $dado_list_s2240;
									$lista_s2240_up[] = $dado_validacao_2240;
								}
							}
						}
					}//fim empty dados_s2240
			}

			if($key == 's2210'){

				$conditions_cat = $this->Cat->FiltroEmConditionCat($filtros);

				$dados_s2210 = $this->Esocial->getAllS2210ForXml($conditions_cat);

				if(!empty($dados_s2210)) {

					foreach($dados_s2210 AS $dados) {

						//valida a regra do layout s2210
						$validacao = array();
						$validacao = $this->Esocial->valida_regra_campos_s2210($dados);
						
						//monta a listagem
						$lista_s2210[] = array(
							"evento" => 'S2210',
							"Cat" => array(
								"codigo" => $dados[0]['codigo_cat'],
								"codigo_cliente" => $dados[0]['codigo_cliente'],
							),
							"Cliente" => array(
								'nome_fantasia' => $dados[0]['nome_fantasia']
							),
							"Funcionario" => array(
								"nome" => $dados[0]['nome_funcionario'],
								"cpf" => $dados[0]['cpf_funcionario'],
								"codigo" => $dados[0]['codigo_funcionario'],
							),
							"ClienteFuncionario" => array(
								"matricula" => $dados[0]['matricula'],
							),
							"validacao" => $validacao
						);
					}//fim foreeach de validacao

					foreach($lista_s2210 as $key_lista => $dados_lista_2210){

						if(empty($dados_lista_2210['validacao'])){//retira os registros que nao tem inconsistencias
							unset($lista_s2210[$key_lista]);
						}
					}// fim lista

					foreach($lista_s2210 as $key1 => $dado_list_s2210){

						if(is_array($lista_s2210[$key1]['validacao']) && count($lista_s2210[$key1]['validacao']) > 0){
							
							foreach($dado_list_s2210['validacao'] as $key_valid => $dado_validacao_2210){
								$dado_validacao_2210[] = $dado_list_s2210;
								$lista_s2210_up[] = $dado_validacao_2210;
							}
						}
					}

				}//fim empty dados_s2210

			}

			if($key == 's2230'){
				$conditions = $this->Atestado->ConditionXmlS2230($filtros);
			
				$dados_s2230 = $this->Esocial->getAllS2230ForXml($conditions);

				if(!empty($dados_s2230)) {
					//valida a regra do layout s2230
					$validacao = array();

					foreach($dados_s2230 as $dados) {

						$dados = $dados[0];

						// valida a regra do layout s2220
						$validacao = array();
						$validacao = $this->Esocial->valida_regra_campos_s2230($dados);
						
						//monta a listagem
						$lista_s2230[] = array(
							"evento" => 'S2230',
							"FuncionarioSetorCargo" => array(
								"codigo_cliente_alocacao" => $dados['codigo_cliente_alocacao'],
							),
							"Cliente" => array(
								"nome_fantasia" => $dados['nome_fantasia'],							
							),
							"Setor" => array(
								"descricao" => $dados['setor']
							),

							"Cargo" => array(
								"descricao" => $dados['cargo']
							),
							"Funcionario" => array(
								"nome" => $dados['nome_funcionario'],
								"cpf" => $dados['cpf_funcionario']
							),
							"ClienteFuncionario" => array(
								"matricula" => $dados['matricula']
							),

							"Atestado" => array(
								"codigo" => $dados['codigo_atestado']
							),

							"validacao" => $validacao
						);
					}//fim foreeach de validacao

					foreach($lista_s2230 as $key_lista => $dados_lista_2230){

						if(empty($dados_lista_2230['validacao'])){//retira os registros que nao tem inconsistencias
							unset($lista_s2230[$key_lista]);
						}
					}// fim lista

					foreach($lista_s2230 as $key1 => $dado_list_s2230){

						if(is_array($lista_s2230[$key1]['validacao']) && count($lista_s2230[$key1]['validacao']) > 0){
							
							foreach($dado_list_s2230['validacao'] as $key_valid => $dado_validacao_2230){
								$dado_validacao_2230[] = $dado_list_s2230;
								$lista_s2230_up[] = $dado_validacao_2230;
							}
						}
					}
				}
			}

		}

       	//headers
        ob_clean();
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="relatorio_inconsistencias_'.date('YmdHis').'.csv"');
        header('Pragma: no-cache');

		//cabecalho do arquivo
		echo utf8_decode('"Evento";"Código";"CPF";"Nome";"Unidade Alocacao";"Titulo";"Validação";')."\n";
		$linha = '';

		if(!empty($lista_s2220_up)){

			foreach($lista_s2220_up AS $value) {

				$linha .= $value[0]['evento'].';';
				$linha .= $value[0]['PedidoExame']['codigo'].';';
				$linha .= Comum::formatarDocumento($value[0]['Funcionario']['cpf']).';';
				$linha .= $value[0]['Funcionario']['nome'].';';
				$linha .= $value[0]['Cliente']['nome_fantasia'].';';
				$linha .= $value['titulo'].';';
				$linha .= $value['descricao'].';';

				$linha .= "\n";


			}
		}

		if(!empty($lista_s2240_up)){

			foreach($lista_s2240_up AS $value) {

				$linha .= $value[0]['evento'].';';
				$linha .= $value[0]['GrupoExposicao']['codigo'].';';
				$linha .= Comum::formatarDocumento($value[0]['Funcionario']['cpf']).';';
				$linha .= $value[0]['Funcionario']['nome'].';';
				$linha .= $value[0]['Cliente']['nome_fantasia'].';';
				$linha .= $value['titulo'].';';
				$linha .= $value['descricao'].';';

				$linha .= "\n";


			}
		}

		if(!empty($lista_s2210_up)){

			foreach($lista_s2210_up AS $value) {

				$linha .= $value[0]['evento'].';';
				$linha .= $value[0]['Cat']['codigo'].';';
				$linha .= Comum::formatarDocumento($value[0]['Funcionario']['cpf']).';';
				$linha .= $value[0]['Funcionario']['nome'].';';
				$linha .= $value[0]['Cliente']['nome_fantasia'].';';
				$linha .= $value['titulo'].';';
				$linha .= $value['descricao'].';';

				$linha .= "\n";


			}
		}

		if(!empty($lista_s2230_up)){

			foreach($lista_s2230_up AS $value) {

				$linha .= $value[0]['evento'].';';
				$linha .= $value[0]['Atestado']['codigo'].';';
				$linha .= Comum::formatarDocumento($value[0]['Funcionario']['cpf']).';';
				$linha .= $value[0]['Funcionario']['nome'].';';
				$linha .= $value[0]['Cliente']['nome_fantasia'].';';
				$linha .= $value['titulo'].';';
				$linha .= $value['descricao'].';';

				$linha .= "\n";


			}
		}

		echo Comum::converterEncodingPara($linha, 'ISO-8859-1');
		//mata o metodo
        die();
    }
}// FINAL CLASS EsocialController