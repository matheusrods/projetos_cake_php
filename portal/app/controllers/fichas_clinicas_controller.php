<?php
class FichasClinicasController extends AppController {

	public $name = 'FichasClinicas';  
	public $helpers = array('BForm');
	public $uses = array(
		'FichaClinica', 
		'PedidoExame',
		'HistoricoFichaClinica', 
		'Configuracao', 
		'ItemPedidoExame', 
		'FichaClinicaLog',
		'FichaClinicaRespostaLog',
		'GrupoEconomico',
		'GrupoEconomicoCliente',
		'ItemPedidoExameBaixa',
		'Configuracao'
	);

	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array(
			'editar',
			'imprimir_relatorio',
			'calcula_imc',
			'listagem_historico',
			'lista_log_ficha_clinica',
			'get_log_ficha_clinica',
			'get_respostas_log_ficha_clinica'
		));
	}

	public function listagem() {
		$this->layout = 'ajax';
		
		$filtros = $this->Filtros->controla_sessao($this->data, $this->FichaClinica->name);
		$conditions = $this->FichaClinica->converteFiltroEmCondition($filtros);
		
		if(!is_null($this->BAuth->user('codigo_cliente'))) {
			$conditions['ClienteFuncionario.codigo_cliente_matricula'] = $this->BAuth->user('codigo_cliente');
		}

		$codigo_exame_aso = $this->Configuracao->getChave('INSERE_EXAME_CLINICO');
		
		if($codigo_exame_aso){
			$conditions['ItemPedidoExame.codigo_exame'] = $codigo_exame_aso;
		}// condicao colocada para procurar o exame relacionado a ficha clinica, que é o aso, sem isso esta duplicando os dados
		
		$order = 'FichaClinica.codigo DESC';
		$joins = array(
			array(
				'table' => 'RHHealth.dbo.pedidos_exames',
				'alias' => 'PedidoExame',
				'type' => 'INNER',
				'conditions' => 'PedidoExame.codigo = FichaClinica.codigo_pedido_exame'		
			),
			array(
				'table' => 'RHHealth.dbo.cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario'		
			),
			array(
				'table' => 'RHHealth.dbo.cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente_matricula'		
			),
			array(
				'table' => 'RHHealth.dbo.funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'		
			),
			array(
				'table' => 'RHHealth.dbo.medicos',
				'alias' => 'Medico',
				'type' => 'INNER',
				'conditions' => 'Medico.codigo = FichaClinica.codigo_medico'		
			),
			array(
				'table' => 'RHHealth.dbo.itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo'		
			),
		);

		$fields = array(
			'FichaClinica.*',
			'Cliente.razao_social',
			'Funcionario.nome',
			'Funcionario.codigo',
			'Medico.nome',
			'PedidoExame.codigo',
			'ItemPedidoExame.codigo'
		);

		$this->paginate['FichaClinica'] = array(
			'conditions' => $conditions,
			'fields' => $fields,
			'limit' => 50,
			'joins' => $joins,
			'order' => $order
		);		

		$fichas_clinicas = $this->paginate('FichaClinica');

		$this->set(compact('fichas_clinicas'));
		//$this->Filtros->limpa_sessao($this->FichaClinica->name);
	}

	public function listagemPedidoDeExame() {
		
		$this->layout = 'ajax'; 
		$filtros = $this->Filtros->controla_sessao($this->data, $this->FichaClinica->name);
		
		if(!is_null($this->BAuth->user('codigo_fornecedor'))) {
			$filtros['codigo_fornecedor'] = $this->BAuth->user('codigo_fornecedor');
		}

		/***************************************************
		 * validacao adicionado para evitar o cliente de
		 * burlar o acesso e ver dados de outros clientes;
		 ***************************************************/
		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			if(empty($filtros['codigo_cliente'])) {
				$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
			}
		}
		
		$conditions = $this->FichaClinica->converteFiltroPedidoExameEmCondition($filtros);
		//Não retorna os pedidos cancelados
		$conditions['PedidoExame.codigo_status_pedidos_exames <>'] = 5;
		$conditions[] = 'FichasClinicas.codigo IS NULL';

		$codigo_exame_aso = $this->Configuracao->getChave('INSERE_EXAME_CLINICO');
		if(is_null($codigo_exame_aso)){
            $this->BSession->setFlash(array('alert alert-error','Configuração Inválida para a chave INSERE_EXAME_CLINICO em Administrativo > Cadastros > Configurações de Sistema!'));
            $this->redirect(array('controller' => 'fichas_clinicas', 'action' => 'index'));
        }
        $conditions['ItemPedidoExame.codigo_exame'] = $codigo_exame_aso;

		$order = 'PedidoExame.codigo';
		$joins = array(
			array(
				'table' => 'RHHealth.dbo.cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario'
			),
			array(
				'table' => 'RHHealth.dbo.cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente_matricula'
			),
			array(
				'table' => 'RHHealth.dbo.funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
			),
			array(
				'table' => 'RHHealth.dbo.itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo'
			),
			array(
				'table' => 'RHHealth.dbo.fichas_clinicas',
				'alias' => 'FichasClinicas',
				'type' => 'LEFT',
				'conditions' => 'FichasClinicas.codigo_pedido_exame = PedidoExame.codigo'
			)
		);
		$fields = array(
			'PedidoExame.codigo',
			'Cliente.razao_social',
			'Funcionario.nome',
		);

		$this->paginate['PedidoExame'] = array(		
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields,
			'group' => $fields,
			'limit' => 50,
			'order' => $order,
		);

		$pedidosExames = $this->paginate('PedidoExame');
		$this->set(compact('pedidosExames'));
		$this->Filtros->limpa_sessao($this->FichaClinica->name);
	}

	public function index(){
		$this->pageTitle = 'Cadastro de Fichas Clínicas';
	}

	public function incluir($codigoPedidoExame = null, $redir = null) {
		$INFO = array();
		
        $INFO['cn'] = ($this->BAuth->user('cn') ? $this->BAuth->user('cn') : $this->BAuth->user('nome'));

		/***************************************************
		 * validacao adicionado para evitar o cliente de
		 * burlar o acesso e ver dados de outros clientes;
		 ***************************************************/
		if(!is_null($this->BAuth->user('codigo_cliente'))) {
			$codigo_cliente = $this->BAuth->user('codigo_cliente');
			$dados_pedido = $this->PedidoExame->retornaPedido($codigoPedidoExame);
			
			// se for array é multicliente
			if(is_array($codigo_cliente)  ){
				$matricula_valida = in_array($dados_pedido['ClienteFuncionario']['codigo_cliente_matricula'], $codigo_cliente);
			} else {
				$matricula_valida = ($dados_pedido['ClienteFuncionario']['codigo_cliente_matricula'] == $codigo_cliente);
			} 

			if(!$matricula_valida) {
				$this->BSession->setFlash('acesso_nao_permitido');
				$this->redirect(array('controller' => 'fichas_clinicas', 'action' => 'selecionarPedidoDeExame'));
			}
		}
		
		//valida se existe o pedido de exame selecionado, senao retorna a index e exibe erro
		$ficha_clinica = $this->FichaClinica->find('first', array('conditions' => array('codigo_pedido_exame' => $codigoPedidoExame)));
		if(!empty($ficha_clinica)) {
			$this->BSession->setFlash('erro_pedido_exame');			
			if($redir == 'agenda') {
				$this->redirect(array('controller' => 'consultas_agendas','action' => 'index2'));
			}

			$this->redirect(array('action' => 'index'));
		}

		//valida se o pedido de exame não está cancelado
		$pedido_exame = $this->FichaClinica->PedidoExame->read();
		if($pedido_exame['PedidoExame']['codigo_status_pedidos_exames'] == 5) {
			$this->BSession->setFlash(array('alert alert-error','O pedido de exame selecionado foi cancelado.'));
			$this->redirect(array('action' => 'index'));
		}

		$this->pageTitle = 'Incluir Ficha Clínica';
		
		
		if($this->RequestHandler->isPost()) {

			//valida o formulário
			$this->data['FichaClinica']['codigo_pedido_exame'] = $codigoPedidoExame; // reatribui por seguranca

			//perfil medico(cliente) / medico(prestador) para preenchimento automatico
			if($this->BAuth->user('codigo_uperfil') == 19 || $this->BAuth->user('codigo_uperfil') == 11) {
				if(empty($this->data['FichaClinica']['hora_fim_atendimento'])) {
					$this->data['FichaClinica']['hora_fim_atendimento'] = date("H:i");
				}
			}//fim perfil

			//interrompe se o resultado da baixa vim vazio.
			foreach ($this->data['ItemPedidoExameBaixa'] as $key => $value) {
				# code...
				if( $value['codigo_exame'] == $this->Configuracao->getChave('INSERE_EXAME_CLINICO') ) {//codigo do exame ASO

					if(empty($value['resultado'])){		
						$this->BSession->setFlash(array('alert alert-error', 'Selecione o Resultado do ASO - EXAME CLINICO!'));
						$this->redirect(Router::url($this->referer(), true));       
					}

					if(empty($value['data_realizacao_exame']) || $value['data_realizacao_exame'] == '__/__/____' || trim($value['data_realizacao_exame'] == '')){		
						$this->BSession->setFlash(array('alert alert-error', 'Preencha a data da realização do exame'));
						$this->redirect(Router::url($this->referer(), true));       
					}
				}

				if(empty($this->data['ItemPedidoExameBaixa'][$key]['data_realizacao_exame']) || $this->data['ItemPedidoExameBaixa'][$key]['data_realizacao_exame'] == '__/__/____' || trim($this->data['ItemPedidoExameBaixa'][$key]['data_realizacao_exame']) == ''){
					unset($this->data['ItemPedidoExameBaixa'][$key]);
				}
			}

			$this->FichaClinica->set($this->data);
			$this->FichaClinica->FichaClinicaResposta->set($this->data);
			$this->FichaClinica->FichaClinicaResposta->validates();


			//se validacao estiver ok, entao salve
			if($this->FichaClinica->FichaClinicaResposta->validates()) {
				if($this->FichaClinica->incluir($this->data)) {
					$this->BSession->setFlash('save_success');
					
					if(!is_null($this->data['FichaClinica']['redir'])) {
						if($this->data['FichaClinica']['redir'] == 'agenda') {
							$this->redirect(array('controller' => 'consultas_agendas','action' => 'index2'));
						}
						else{
							$this->redirect(array('action' => 'index'));
						}
					}
					else {
						$this->redirect(array('action' => 'index'));
					}

				} else {
					$this->BSession->setFlash('save_error');
				}
			} else {
				$this->BSession->setFlash('save_error');
			}
		}	

		$dados = $this->FichaClinica->obtemDadosComplementares($codigoPedidoExame);

		$this->data['FichaClinica']['incluido_por'] = $INFO['cn'];

		$this->data['FichaClinica']['msg_imc'] = $this->get_mensagem_imc('0');

		$this->data['hora_automatica'] = 0;
		//perfil medico(cliente) / medico(prestador)
		if($this->BAuth->user('codigo_uperfil') == 19 || $this->BAuth->user('codigo_uperfil') == 11) {
            $this->data['hora_automatica'] = 1;
            $this->data['FichaClinica']['hora_inicio_atendimento'] = date("H:i");
        }

		list($dados_medicoes,$dados_depara) = $this->carrega_dados_questionario($dados['Funcionario']['cpf']);

		$this->data['FichaClinica']['pressao_sis'] = $dados_medicoes[0]['pressao_sis'];
		$this->data['FichaClinica']['pressao_dia'] = $dados_medicoes[0]['pressao_dia']; 
		$this->data['FichaClinica']['peso_kg'] = $dados_medicoes[0]['peso_kg'];
		$this->data['FichaClinica']['peso_g'] = $dados_medicoes[0]['peso_g'];
		$this->data['FichaClinica']['altura_m'] = $dados_medicoes[0]['altura_m'];
		$this->data['FichaClinica']['altura_cm'] = $dados_medicoes[0]['altura_cm'];
		$this->data['FichaClinica']['circ_abdom'] = $dados_medicoes[0]['circ_abdom'];
		$this->data['FichaClinica']['circ_quadril'] = $dados_medicoes[0]['circ_quadril'];

		//busca todos os exames do pcmso do funcionario do pedido
		$this->busca_exames_fc($codigoPedidoExame);

		//pega os dados de historico pelo pedido de exames
		$historico = $this->getHistorico($codigoPedidoExame);

		$this->set('verificaParecer', $this->FichaClinica->verificaParecer($codigoPedidoExame));
		$this->set(compact('dados','redir'));
		$this->set('questoes', $this->FichaClinica->montaQuestoes($dados['Funcionario']));
		$this->set(compact('dados_medicoes','dados_depara', 'INFO','historico', 'codigoPedidoExame'));
	}

	public function selecionarPedidoDeExame() {
		$this->pageTitle = 'Selecionar pedido de exame';
	}

	public function editar($codigo = null , $redir = null) {
		$this->pageTitle = 'Editar Ficha Clínica';

		$this->loadModel('ItemPedidoExameBaixa');

		$fichaClinica = $this->FichaClinica->findByCodigo($codigo);
		$codigoPedidoExame = $fichaClinica['FichaClinica']['codigo_pedido_exame'];

		if($this->RequestHandler->isPost()) {
			//define o codigo pois se trata de edicao
			$this->data['FichaClinica']['codigo'] = $codigo;
			
			//valida o formulário
			$this->data['FichaClinica']['codigo_pedido_exame'] = $codigoPedidoExame; // reatribui por seguranca

			//interrompe se o resultado da baixa vim vazio.
			foreach ($this->data['ItemPedidoExameBaixa'] as $key => $value) {
				# code...
				if( $value['codigo_exame'] == $this->Configuracao->getChave('INSERE_EXAME_CLINICO') ){//codigo do exame ASO

					if(empty($value['resultado'])){		
						$this->BSession->setFlash(array('alert alert-error', 'Selecione o Resultado do ASO - EXAME CLINICO!'));
						$this->redirect(Router::url($this->referer(), true));       
					}

					if(empty($value['data_realizacao_exame']) || $value['data_realizacao_exame'] == '__/__/____' || trim($value['data_realizacao_exame'] == '')){		
						$this->BSession->setFlash(array('alert alert-error', 'Preencha a data da realização do exame'));
						$this->redirect(Router::url($this->referer(), true));       
					}
				}

				if(empty($this->data['ItemPedidoExameBaixa'][$key]['data_realizacao_exame']) || $this->data['ItemPedidoExameBaixa'][$key]['data_realizacao_exame'] == '__/__/____' || trim($this->data['ItemPedidoExameBaixa'][$key]['data_realizacao_exame']) == ''){
					unset($this->data['ItemPedidoExameBaixa'][$key]);
				}
			}

			$this->FichaClinica->set($this->data);
			$this->FichaClinica->FichaClinicaResposta->set($this->data);
			$this->FichaClinica->FichaClinicaResposta->validates();

			//se validacao estiver ok, entao salve
			if($this->FichaClinica->FichaClinicaResposta->validates()) {

				if($this->FichaClinica->editar($this->data)) {
					$this->BSession->setFlash('save_success');

					if(!is_null($this->data['FichaClinica']['redir'])) {
						if($this->data['FichaClinica']['redir'] == 'agenda') {
							$this->redirect(array('controller' => 'consultas_agendas','action' => 'index2'));
						}
						else{
							$this->redirect(array('action' => 'index'));
						}
					}
					else {
						$this->redirect(array('action' => 'index'));
					}

					
				} else {
					$this->BSession->setFlash('save_error');
				}
			} else {
				$this->BSession->setFlash('save_error');
			}
		}

		$this->data = $this->FichaClinica->montaRespostas($codigo);

		//trabalha os dados de farmaco
		if(isset($this->data['FichaClinicaResposta']['campo_livre'])) {
			//varre os campos livres
			foreach($this->data['FichaClinicaResposta']['campo_livre'] AS $key_cl => $campo_livre_resp) {
				
				if(!empty($campo_livre_resp)) {
					switch ($key_cl) {
						case '26':
						case '161':
							$this->data['FichaClinicaResposta']['campo_livre'][$key_cl] = $campo_livre_resp[0];
							break;
					}
				}

			}//fim foreach
		}//fim campo_livre

        // debug($this->data); exit;

		$this->data['FichaClinica'] = $fichaClinica['FichaClinica'];
		$dados = $this->FichaClinica->obtemDadosComplementares($fichaClinica['FichaClinica']['codigo_pedido_exame']);

		//Se o médico não foi encontrado é porque está inativo
		if( !empty($this->data['FichaClinica']['codigo_medico']) && !isset($dados['Medico'][$this->data['FichaClinica']['codigo_medico']])){
			$this->loadModel('Medico');
			$medico_ficha = $this->Medico->find('first', array('fields'=> array('codigo','nome'),
				'conditions' => array('codigo' => $this->data['FichaClinica']['codigo_medico'] ),'recursive' => -1 ));
			if(!empty($medico_ficha)){
				$dados['Medico'][$medico_ficha['Medico']['codigo']] = $medico_ficha['Medico']['nome'];
			}
		}
		
		//calcula o imc caso nao esteja calculado
		if(empty($this->data['FichaClinica']['imc']) 
			&& (!empty($this->data['FichaClinica']['peso_kg']) || !empty($this->data['FichaClinica']['peso_gr']))
			&& (!empty($this->data['FichaClinica']['altura_mt']) || !empty($this->data['FichaClinica']['altura_cm']))) {
			//pega o peso
			$peso = $this->data['FichaClinica']['peso_kg'].".".$this->data['FichaClinica']['peso_gr'];
			//pega a altura
			$altura = $this->data['FichaClinica']['altura_mt'].".".$this->data['FichaClinica']['altura_cm'];
			//calcula o imc
			$this->data['FichaClinica']['imc'] = number_format($peso / ($altura*$altura),1);
		}

		$this->data['FichaClinica']['msg_imc'] = $this->get_mensagem_imc($this->data['FichaClinica']['imc']);
		
		$this->data['hora_automatica'] = 0;
		//perfil medico(cliente) / medico(prestador)
		if($this->BAuth->user('codigo_uperfil') == 19 || $this->BAuth->user('codigo_uperfil') == 11) {
			$this->data['hora_automatica'] = 1;
		}

		$questoes = $this->FichaClinica->montaQuestoes($dados['Funcionario']);
		
		// debug($questoes);exit;

		$verificaParecer = $this->FichaClinica->verificaParecer($fichaClinica['FichaClinica']['codigo_pedido_exame']);
		$dados_depara = array();

		//pega os dados de historico pelo pedido de exames
		$historico = $this->getHistorico($fichaClinica['FichaClinica']['codigo_pedido_exame']);

		$this->busca_exames_fc($fichaClinica['FichaClinica']['codigo_pedido_exame']);

		$this->set(compact('codigo', 'verificaParecer', 'dados','redir', 'questoes','dados_depara','historico', 'codigoPedidoExame'));
	}//fim editar

	public function get_mensagem_imc($imc)
	{
		//nao informado
		$msg_imc = 'Não informado!';

		//verifica qual msg
		if(($imc > 0.0) && ($imc < 18.5)){
			$msg_imc = 'Magro ou baixo peso';
		}
		elseif(($imc >= 18.5) && ($imc < 24.99)){
			$msg_imc = 'Normal ou eutrófico';
		}
		elseif(($imc >= 25) && ($imc < 29.99)){
			$msg_imc = 'Sobrepeso ou pré-obeso';
		}
		elseif(($imc >= 30) && ($imc < 34.99)){
			$msg_imc = 'Obesidade';
		}
		elseif(($imc >= 35) && ($imc < 39.99)){
			$msg_imc = 'Obesidade';
		}
		elseif(($imc >= 40)){
			$msg_imc = 'Obesidade (grave)';
		}

		return $msg_imc;
	}

	public function imprimir_relatorio($codigo_ficha_clinica, $codigo_pedido_exame, $codigo_funcionario)
	{
		$this->autoRender = false;
		
		//SALVA NA TABELA TEMPORÁRIA OS DADOS SERIALIZADOS PARA A CONSTRUÇÃOI DO RELATORIO PDF
		$this->FichaClinica->criaTabelaTemporaria($codigo_ficha_clinica);

		$this->FichaClinica->temp_table_riscos($codigo_ficha_clinica);

		// GERA O RELATORIO PDF
		$this->__jasperConsulta($codigo_ficha_clinica, $codigo_pedido_exame, $codigo_funcionario);
	}

	private function isJson($json) {
		json_decode($json);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	private function jsonToArray($data = null)
	{
		if(!is_null($data)) {
			$json = (array)json_decode($data);
			foreach ($json as $key => $value) {
				if(is_object($value)) {
					$json[$key] = (array)$value;
				} else {
					$json[$key] = $value;
				}
			}
			$data = $json;
		}
		return $data;
	}

	private function __jasperConsulta( $codigo_ficha_clinica, $codigo_pedido_exame, $codigo_funcionario) {
        // opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME'=>'/reports/RHHealth/relatorio_ficha_clinica', // especificar qual relatório
			'FILE_NAME'=> basename( 'relatorio_ficha_clinica.pdf' ) // nome do relatório para saida
		);

		// parametros do relatorio
		$parametros = array(
			'CODIGO_FICHA_CLINICA' => $codigo_ficha_clinica, 
			'CODIGO_PEDIDO_EXAME' => $codigo_pedido_exame, 
			'CODIGO_FUNCIONARIO' => $codigo_funcionario
		);

		$this->loadModel('Cliente');
		$parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);
		$this->loadModel('MultiEmpresa');
		//codigo empresa emulada
		$codigo_empresa = $this->authUsuario['Usuario']['codigo_empresa'];
		//url logo da multiempresa
		$parametros['URL_LOGO_MULTI_EMPRESA'] = $this->MultiEmpresa->urlLogomarca($codigo_empresa);	

		try {
		
			// envia dados ao componente para gerar
			$url = $this->Jasper->generate( $parametros, $opcoes );	

			if($url){
				// se obter retorno apresenta usando cabeçalho apropriado
				header(sprintf('Content-Disposition: attachment; filename="%s"', $opcoes['FILE_NAME']));
				header('Pragma: no-cache');
				header('Content-type: application/pdf');
				echo $url; exit;
			}

		} catch (Exception $e) {
			// se ocorreu erro
			debug($e); exit;
		}		

		exit;		
		
	}

	function carrega_dados_questionario($cpf_funcionario){
		$this->DeparaQuestoes = ClassRegistry::init('DeparaQuestoes');
		$this->DeparaQuestoesRespostas = ClassRegistry::init('DeparaQuestoesRespostas');
		$this->UsuariosDados = ClassRegistry::init('UsuariosDados');

		$depara['questoes'] = $this->DeparaQuestoes->find('list',array('fields' => array('codigo_questao_ficha_clinica','codigo_questao_questionario'),'conditions' => array('codigo_questao_ficha_clinica <> 0')));

		///////
		$options['fields'] = array(
			'Respostas.codigo_questao as codigo_questao',
			'MAX(Respostas.codigo) as maior_codigo'
		);

		$options['joins'] = array(
			array(
				'table' => 'usuario',
				'alias' => 'Usuario',
				'type' => 'LEFT',
				'conditions' => 'UsuariosDados.codigo_usuario = Usuario.codigo'
			),
			array(
				'table' => 'respostas',
				'alias' => 'Respostas',
				'type' => 'LEFT',
				'conditions' => 'Usuario.codigo = Respostas.codigo_usuario'
			),
		);

		$options['conditions'] = array(
			'UsuariosDados.cpf' => $cpf_funcionario
		);

		$options['group'] = 'codigo_questao';

		$query_cte = $this->UsuariosDados->find('sql',$options);
		$cte = "WITH CTE AS (".$query_cte.")";

		$query = $cte.' SELECT codigo_questao, ( SELECT codigo_resposta FROM respostas WHERE codigo = maior_codigo ) AS codigo_resposta FROM CTE ORDER BY codigo_questao';
		$dados_respostas = $this->UsuariosDados->query($query);

		foreach ($dados_respostas as $value) {
			$depara['respostas'][ $value[0]['codigo_questao'] ] = $value[0]['codigo_resposta'];
		}

		$dados = $this->DeparaQuestoesRespostas->find('all',array('conditions' => array('resposta_ficha_clinica IS NOT NULL')));
		foreach ($dados as $value) {
			$respostas_questoes[ $value['DeparaQuestoesRespostas']['codigo_questao_questionario'] ][ $value['DeparaQuestoesRespostas']['codigo_resposta_questionario'] ] = $value['DeparaQuestoesRespostas']['resposta_ficha_clinica'];
		}

		/////////
		$dados_depara = array();
		foreach ($depara['questoes'] as $key => $questao) {
			if ( isset($depara['respostas'][$questao]) && isset($respostas_questoes[$questao]) ){
				$dados_depara[] = array($key , $respostas_questoes[$questao][ $depara['respostas'][$questao] ]);
			}
		}
		/////////

		///////
		$options2['fields'] = array(
			'UsuarioPressaoArterial.pressao_arterial_sistolica AS pressao_sis',
			'UsuarioPressaoArterial.pressao_arterial_diastolica AS pressao_dia',
			'FLOOR(UsuarioImc.peso) AS peso_kg',
			'RIGHT(UsuarioImc.peso,2) AS peso_g',
			'UsuarioImc.altura / 100 AS altura_m',
			'UsuarioImc.altura % 100 AS altura_cm',
			'UsuarioAbdominal.circ_abdom AS circ_abdom',
			'UsuarioAbdominal.circ_quadril AS circ_quadril'
		);

		$options2['joins'] = array(
			array(
				'table' => 'usuario',
				'alias' => 'Usuario',
				'type' => 'LEFT',
				'conditions' => 'UsuariosDados.codigo_usuario = Usuario.codigo'
			),
			array(
				'table' => 'usuarios_pressao_arterial',
				'alias' => 'UsuarioPressaoArterial',
				'type' => 'LEFT',
				'conditions' => 'UsuarioPressaoArterial.codigo = (SELECT TOP 1 codigo FROM usuarios_pressao_arterial WHERE codigo_usuario = Usuario.codigo ORDER BY data_inclusao DESC)'
			),
			array(
				'table' => 'usuarios_imc',
				'alias' => 'UsuarioImc',
				'type' => 'LEFT',
				'conditions' => 'UsuarioImc.codigo = (SELECT TOP 1 codigo FROM usuarios_imc WHERE codigo_usuario = Usuario.codigo ORDER BY data_inclusao DESC)'
			),
			array(
				'table' => 'usuarios_abdominal',
				'alias' => 'UsuarioAbdominal',
				'type' => 'LEFT',
				'conditions' => 'UsuarioAbdominal.codigo = (SELECT TOP 1 codigo FROM usuarios_abdominal WHERE codigo_usuario = Usuario.codigo ORDER BY data_inclusao DESC)'
			),
		);

		$options2['conditions'] = array(
			'UsuariosDados.cpf' => $cpf_funcionario
		);

		$dados_medicoes = $this->UsuariosDados->find('first',$options2);
		///////

		return array($dados_medicoes,json_encode($dados_depara));
	}

	/**
	 * [listagem_historico description]
	 * 
	 * busca pelo pedido de exame para chegar no funcionario pegar o cpf e listar o historico
	 * 
	 * @param  [type] $codigo_pedido_exame [description]
	 * @return [type]                      [description]
	 */
	public function listagem_historico($codigo_pedido_exame)
	{

		$this->layout = 'ajax'; 
		//pega os dados de historico pelo pedido de exames
		$dados = $this->HistoricoFichaClinica->getDadosPedidoExames($codigo_pedido_exame);

		//pagina o historico
        $this->paginate['PedidoExame'] = array(
            'conditions' => $dados['conditions'],
            'limit'  => 250,
            'order'  => 'HistoricoFichaClinica.data_atendimento DESC',
            'fields' => $dados['fields'],
            'joins' => $dados['joins']
        );
        
        $historico = $this->paginate('PedidoExame');
        $this->set(compact('historico'));

	}//fim listagem_historico

	/**
	 * [getHistorico description]
	 * 
	 * pega os dados de historico caso exista
	 * 
	 * @param  [type] $codigo_pedido_exame [description]
	 * @return [type]                      [description]
	 */
	public function getHistorico($codigo_pedido_exame = null)
	{
		
		$historico = false;
		//verifica se tem codigo de pedido de exames
		if(!empty($codigo_pedido_exame)) {
			//pega a montagem da query
			$dadosHistorico = $this->HistoricoFichaClinica->getDadosPedidoExames($codigo_pedido_exame);
			//pega os dados
			$historico = $this->PedidoExame->find('first',array('conditions' => $dadosHistorico['conditions'],'fields' => $dadosHistorico['fields'], 'joins' => $dadosHistorico['joins']));

			if($historico) {
				$historico = true;
			}

		}//fim codigo_pedido_exames

		return $historico;

	}//fim getHistorico

	public function lista_log_ficha_clinica($codigo_pedido_exame, $codigo_ficha){

        $this->pageTitle = 'Log Ficha Clinica';
        $this->layout    = 'new_window';

        //para acessar o log da ficha clinica teremos que buscar o codigo do item do pedido do exame
        //fields 
        $fields_ipe = array(
			'ItemPedidoExame.codigo',
			'PedidoExame.codigo'
		);
		//where
		$conditions_ipe = array(
			'PedidoExame.codigo' => $codigo_pedido_exame
		);
		//joins
		$joins_ipe = array(
			array(
				'table' => 'RHHealth.dbo.itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo and ItemPedidoExame.codigo_exame = '.$this->Configuracao->getChave('INSERE_EXAME_CLINICO')		
			),
		);
		//buscar o codigo item pedido exame
		$cod_item_pedido = $this->PedidoExame->find('first', array('conditions' => $conditions_ipe, 'fields' => $fields_ipe, 'joins' => $joins_ipe));

		// debug($cod_item_pedido);

		//buscar o item pedido do exame
		$fields = array('ItemPedidoExame.codigo','ItemPedidoExame.codigo_exame','ItemPedidoExameBaixa.codigo','FichaClinica.codigo');

        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.itens_pedidos_exames_baixa',
                'alias' => 'ItemPedidoExameBaixa',
                'type' => 'LEFT',
                'conditions' => 'ItemPedidoExame.codigo = ItemPedidoExameBaixa.codigo_itens_pedidos_exames',
            ),
            array(
                'table' => 'Rhhealth.dbo.fichas_clinicas',
                'alias' => 'FichaClinica',
                'type' => 'LEFT',
                'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = FichaClinica.codigo_pedido_exame',
            ),
        );

        $conditions = array('ItemPedidoExame.codigo' => $cod_item_pedido['ItemPedidoExame']['codigo']);

        $dados = $this->ItemPedidoExame->find('first',array('fields' => $fields, 'conditions' => $conditions, 'joins' => $joins));

        // debug($dados);exit;

        $this->set(compact('dados','cod_item_pedido', 'codigo_ficha'));
    }

    public function get_log_ficha_clinica($codigo_item_pedido, $tabela){

	    if($tabela == 'dadosFichaClinica'){

            $FichaClinicaLog = ClassRegistry::init('FichaClinicaLog');

            $fields = array(
            	'FichaClinicaLog.codigo_fichas_clinicas',
            	'FichaClinicaLog.codigo_pedido_exame',
            	'Medico.codigo',
            	'Medico.nome',
            	'Medico.numero_conselho',
            	'ConselhoProfissional.descricao',
            	'Medico.conselho_uf',
            	'FichaClinicaLog.incluido_por',
            	'substring(cast(hora_inicio_atendimento as varchar), 0,6) as hora_inicio',
				'substring(cast(hora_fim_atendimento as varchar), 0,6) as hora_fim',
            	'FichaClinicaLog.pa_sistolica',
            	'FichaClinicaLog.pa_diastolica',
            	'FichaClinicaLog.pulso',
            	'FichaClinicaLog.circunferencia_abdominal',
            	'FichaClinicaLog.circunferencia_quadril',
            	'(CASE
            		WHEN(FichaClinicaLog.peso_kg is null) THEN \'\'
            		ELSE
            			CONCAT(FichaClinicaLog.peso_kg, \' Kg\')END) AS peso',
            	'(CASE 
            		WHEN(FichaClinicaLog.altura_mt is null AND FichaClinicaLog.altura_cm is null) THEN \'\'
            	  ELSE
            		CONCAT(FichaClinicaLog.altura_mt, \' m \', FichaClinicaLog.altura_cm, \' cm\') END) as altura',
            	'FichaClinicaLog.imc',
            	'FichaClinicaLog.observacao',
            	'FichaClinicaLog.parecer_altura',
            	'FichaClinicaLog.parecer_espaco_confinado',
            	'FichaClinicaLog.ativo',
                'FichaClinicaLog.parecer',
                'FichaClinicaLog.data_alteracao',
                'FichaClinicaLog.acao_sistema',
                'UsuarioAlteracao.nome AS usuario_alteracao',
                'UsuarioInclusao.nome AS usuario_inclusao'
            );

            $conditions = array('ItemPedidoExame.codigo' => $codigo_item_pedido);

            $joins = array(
                array(
                    'table' => 'Rhhealth.dbo.usuario',
                    'alias' => 'UsuarioAlteracao',
                    'type' => 'LEFT',
                    'conditions' => 'FichaClinicaLog.codigo_usuario_alteracao = UsuarioAlteracao.codigo',
                ),
                array(
                    'table' => 'Rhhealth.dbo.usuario',
                    'alias' => 'UsuarioInclusao',
                    'type' => 'LEFT',
                    'conditions' => 'FichaClinicaLog.codigo_usuario_inclusao = UsuarioInclusao.codigo',
                ),
                array(
                    'table' => 'Rhhealth.dbo.pedidos_exames',
                    'alias' => 'PedidoExame',
                    'type' => 'LEFT',
                    'conditions' => 'FichaClinicaLog.codigo_pedido_exame = PedidoExame.codigo',
                ),
                array(
                    'table' => 'Rhhealth.dbo.itens_pedidos_exames',
                    'alias' => 'ItemPedidoExame',
                    'type' => 'LEFT',
                    'conditions' => 'PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames',
                ),
                array(
                    'table' => 'Rhhealth.dbo.medicos',
                    'alias' => 'Medico',
                    'type' => 'LEFT',
                    'conditions' => 'Medico.codigo = FichaClinicaLog.codigo_medico',
                ),
                 array(
                    'table' => 'Rhhealth.dbo.conselho_profissional',
                    'alias' => 'ConselhoProfissional',
                    'type' => 'LEFT',
                    'conditions' => 'ConselhoProfissional.codigo = Medico.codigo_conselho_profissional',
                ),
            );

            $order = array('FichaClinicaLog.data_alteracao DESC');

            $dados = $FichaClinicaLog->find('all',array('fields' => $fields,'conditions' => $conditions,'joins' => $joins,'order' => $order));

            // debug($dados);exit;

            foreach ($dados as $key => $dado) {
                $dados[$key]['FichaClinicaLog']['parecer'] = ($dado['FichaClinicaLog']['parecer'] == '1' ? 'Apto' : 'Inapto');
                $dados[$key]['FichaClinicaLog']['parecer_altura'] = ($dado['FichaClinicaLog']['parecer_altura'] == '1' ? 'Apto' : 'Inapto');
                $dados[$key]['FichaClinicaLog']['parecer_espaco_confinado'] = ($dado['FichaClinicaLog']['parecer_espaco_confinado'] == '1' ? 'Apto' : 'Inapto');

                $dados[$key]['FichaClinicaLog']['codigo_medico'] = $dado['Medico']['codigo'];
                $dados[$key]['FichaClinicaLog']['nome_medico'] = $dado['Medico']['nome'];
                $dados[$key]['FichaClinicaLog']['numero_conselho'] = $dado['Medico']['numero_conselho'];
                $dados[$key]['FichaClinicaLog']['tipo_conselho'] = $dado['ConselhoProfissional']['descricao'];
                $dados[$key]['FichaClinicaLog']['conselho_uf'] = $dado['Medico']['conselho_uf'];
                $dados[$key]['FichaClinicaLog']['altura'] = $dado[0]['altura'];
                $dados[$key]['FichaClinicaLog']['peso'] = $dado[0]['peso'];
                $dados[$key]['FichaClinicaLog']['usuario_alteracao'] = $dado[0]['usuario_alteracao'];
                $dados[$key]['FichaClinicaLog']['usuario_inclusao'] = $dado[0]['usuario_inclusao'];
                $dados[$key]['FichaClinicaLog']['hora_inicio'] = $dado[0]['hora_inicio'];
                $dados[$key]['FichaClinicaLog']['hora_fim'] = $dado[0]['hora_fim'];
                unset($dados[$key]['Medico']);
                unset($dados[$key]['ConselhoProfissional']);
                unset($dados[$key][0]);

                switch ($dado['FichaClinicaLog']['acao_sistema']) {
                    case 0:
                        $dados[$key]['FichaClinicaLog']['acao_sistema'] = 'Inclusão';
                        break;
                    case 1:
                        $dados[$key]['FichaClinicaLog']['acao_sistema'] = 'Atualização';
                        break;
                    case 2:
                        $dados[$key]['FichaClinicaLog']['acao_sistema'] = 'Exclusão';
                        break;
                }

                switch ($dado['FichaClinicaLog']['ativo']) {
                    case 0:
                        $dados[$key]['FichaClinicaLog']['ativo'] = 'Inativo';
                        break;
                    case 1:
                        $dados[$key]['FichaClinicaLog']['ativo'] = 'Ativo';
                        break;
                }
            }

            foreach ($dados as $key1 => $dadoLog) {
                foreach ($dadoLog['FichaClinicaLog'] as $key2 => $value) {
                    if(empty($value))
                        $dados[$key1]['FichaClinicaLog'][$key2] = '';
                }
            }
	    }

        // debug($dados);exit;

        //varre os dados para transformar em json
        $retorno = json_encode("erro");
        if( isset($dados) && !empty($dados) ) {
            $retorno = json_encode($dados);
        }

        // $this->log($retorno,'debug');

        echo $retorno;
        exit;
    }

    public function get_respostas_log_ficha_clinica($codigo_ficha, $tabela){

    	//buscar a data de inclusao para ajudar nas conditions da table das respostas
    	$buscar_data_inclusao = $this->FichaClinica->find('first', array('conditions' => array('FichaClinica.codigo' => $codigo_ficha), 'fields' => 'FichaClinica.data_inclusao'));

    	$data_inclusao = $buscar_data_inclusao['FichaClinica']['data_inclusao'];

    	$data_inclusao_maior = $buscar_data_inclusao['FichaClinica']['data_inclusao'];
    	$novaData = explode("/", $data_inclusao_maior);
    	$hora = explode(" ",end($novaData));
    	$data_transformacao_maior = $hora[0].'-'.$novaData[1].'-'.$novaData[0].' '.$hora[1];

    	$data_inclusao_maior = date('Y-m-d H:i:s', strtotime("+10 seconds", strtotime($data_transformacao_maior)));

	    if($tabela == 'RespostasFichaClinica'){

            $FichaClinicaRespostaLog = ClassRegistry::init('FichaClinicaRespostaLog');

            $fields = array(
            	'FichaClinicaRespostaLog.codigo_ficha_clinica_questao',
            	'FichaClinicaQuestao.label',
            	'case 
					when FichaClinicaRespostaLog.resposta = \'0\' then \'Não\'
					when FichaClinicaRespostaLog.resposta = \'1\' then \'Sim\'
					when FichaClinicaRespostaLog.resposta != \'0\' then FichaClinicaRespostaLog.resposta
					when FichaClinicaRespostaLog.resposta != \'1\' then FichaClinicaRespostaLog.resposta
					end as resposta',
            	'FichaClinicaRespostaLog.codigo_ficha_clinica',
            	'FichaClinicaRespostaLog.parentesco',
            	'FichaClinicaRespostaLog.campo_livre',
            	'FichaClinicaRespostaLog.acao_sistema',
            	'FichaClinicaRespostaLog.data_inclusao',
            	'FichaClinicaRespostaLog.data_alteracao',
            	'UsuarioAlteracao.nome as usuario_alteracao',
            	'UsuarioInclusao.nome as usuario_inclusao'
            );

            $conditions = array(
            	'FichaClinicaRespostaLog.codigo_ficha_clinica' => $codigo_ficha,
            	// "CONVERT(VARCHAR(20), FichaClinicaRespostaLog.data_inclusao,20) >= '".$data_inclusao."'",
            	// "CONVERT(VARCHAR(20), FichaClinicaRespostaLog.data_inclusao,20) <= '".$data_inclusao_maior."'",
            );

            $joins = array(
                array(
                    'table' => 'Rhhealth.dbo.usuario',
                    'alias' => 'UsuarioAlteracao',
                    'type' => 'LEFT',
                    'conditions' => 'FichaClinicaRespostaLog.codigo_usuario_alteracao = UsuarioAlteracao.codigo',
                ),
                array(
                    'table' => 'Rhhealth.dbo.usuario',
                    'alias' => 'UsuarioInclusao',
                    'type' => 'LEFT',
                    'conditions' => 'FichaClinicaRespostaLog.codigo_usuario_inclusao = UsuarioInclusao.codigo',
                ),
                array(
                    'table' => 'Rhhealth.dbo.fichas_clinicas_questoes',
                    'alias' => 'FichaClinicaQuestao',
                    'type' => 'LEFT',
                    'conditions' => 'FichaClinicaQuestao.codigo = FichaClinicaRespostaLog.codigo_ficha_clinica_questao',
                ),
            );

            $order = array('FichaClinicaRespostaLog.data_alteracao DESC');

            $dados = $FichaClinicaRespostaLog->find('all',array('fields' => $fields,'conditions' => $conditions,'joins' => $joins,'order' => $order));

            // debug($dados);exit;

            foreach ($dados as $key => $dado) {
                $dados[$key]['FichaClinicaRespostaLog']['pergunta'] = $dado['FichaClinicaQuestao']['label'];
                $dados[$key]['FichaClinicaRespostaLog']['usuario_alteracao'] = $dado[0]['usuario_alteracao'];
                $dados[$key]['FichaClinicaRespostaLog']['usuario_inclusao'] = $dado[0]['usuario_inclusao'];
                $dados[$key]['FichaClinicaRespostaLog']['resposta'] = $dado[0]['resposta'];
                unset($dados[$key]['FichaClinicaQuestao']);
                unset($dados[$key][0]);

                switch ($dado['FichaClinicaRespostaLog']['acao_sistema']) {
                    case 0:
                        $dados[$key]['FichaClinicaRespostaLog']['acao_sistema'] = 'Inclusão';
                        break;
                    case 1:
                        $dados[$key]['FichaClinicaRespostaLog']['acao_sistema'] = 'Atualização';
                        break;
                    case 2:
                        $dados[$key]['FichaClinicaRespostaLog']['acao_sistema'] = 'Exclusão';
                        break;
                }
            }

            foreach ($dados as $key1 => $dadoLog) {
                foreach ($dadoLog['FichaClinicaRespostaLog'] as $key2 => $value) {
                    if(empty($value))
                        $dados[$key1]['FichaClinicaRespostaLog'][$key2] = '';
                }
            }
	    }

        // debug($dados);exit;

        //varre os dados para transformar em json
        $retorno = json_encode("erro");
        if( isset($dados) && !empty($dados) ) {
            $retorno = json_encode($dados);
        }

        // $this->log($retorno,'debug');

        echo $retorno;
        exit;
    }

    public function fichas_clinicas_terceiros(){
		$this->pageTitle = 'Relatório Ficha Clínica';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->FichaClinica->name);

        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

	 	if(empty($filtros['data_inicio'])) {
		 	$filtros['tipo_periodo'] = 'I';     
            $filtros['data_inicio'] = '01/'.date('/m/Y');            
            $filtros['data_fim']    = date('d/m/Y');
        }

        $this->data['FichaClinica'] = $filtros;

        $tipos_periodo = array('I' => 'Inclusão');
        $this->set(compact('tipos_periodo'));
		$this->carrega_combo_unidades('FichaClinica');
	}

	public function lista_fichas_clinicas_terceiros($destino, $export = false) {
		//seta que é um layout em ajax
		$this->layout = 'ajax';
		//pega os filtros que estão em sessao
		$filtros = $this->Filtros->controla_sessao($this->data, $this->FichaClinica->name);

		if (!empty($filtros['data_inicio']) && !empty($filtros['data_inicio'])) {
			$data_final = strtotime(AppModel::dateToDbDate2($filtros['data_fim']));
			$data_inicial = strtotime(AppModel::dateToDbDate2($filtros['data_inicio']));

			$seconds_diff = $data_final - $data_inicial;
			$dias = floor($seconds_diff/3600/24);

			if ($dias <= 365) {
				$exportar_planilha = "";
				if(!empty($filtros['codigo_cliente'])){
					if(empty($filtros['data_inicio'])) {
					 	$filtros['tipo_periodo'] = 'I';     
			            $filtros['data_inicio'] = '01'.date('/m/Y');            
			            $filtros['data_fim']    = date('d/m/Y');
			        }
			
					$conditions = $this->FichaClinica->FiltroEmCondition($filtros);
					$conditions['FichaClinica.ficha_digitada'] = 1;
					//verifica se é usuario de cliente
					if(!is_null($this->BAuth->user('codigo_cliente'))) {
						$conditions['ClienteFuncionario.codigo_cliente_matricula'] = $this->BAuth->user('codigo_cliente');
					}
					//monta a query
					$dados = $this->FichaClinica->get_ficha_clinica_terceiros($conditions);
					//monta o paginate
					$this->paginate['FichaClinica'] = array(
						'conditions' => $dados['conditions'],
						'fields' => $dados['fields'],
						'limit' => 50,
						'joins' => $dados['joins'],
						'order' => $dados['order']
					);

					// pr($this->FichaClinica->find('sql',$this->paginate['FichaClinica']));
			
			        if($export){
			        	//gera a query
        				$query = $this->FichaClinica->get_relatorio_fc($filtros);
        				// debug($query);exit;
			            //direciona pro metodo para exportar a planilha
			            $this->export_lista_ficha_clinica_terceiros($query);
			        } else {
			        	//senao monta a lista normal
			        	$fichas_clinicas = $this->paginate('FichaClinica');          
			        }
	    		}
			}
		}

		$this->set(compact('fichas_clinicas'));
	}

   	public function carrega_combo_unidades($model) {
        $unidades = array();

        $codigo_cliente = (isset($this->data[$model]['codigo_cliente'])) ? $this->data[$model]['codigo_cliente'] : array();

        if(!empty($codigo_cliente)){
            $codigo_cliente = (is_array($codigo_cliente)) ? $codigo_cliente : $codigo_cliente;
            $codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

            $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
        }

        $this->set(compact('unidades'));
    }

    public function export_lista_ficha_clinica_terceiros($query){
    	
        ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 300); // 5min
        
		$dados_fc = $this->FichaClinica->query($query);

       	//headers
        ob_clean();
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="fichas_clinicas_'.date('YmdHis').'.csv"');
        header('Pragma: no-cache');

        //cabecalho do arquivo
        $cabecalho = utf8_decode('"CÓDIGO FICHA";"CÓDIGO PEDIDO";"CLIENTE";"UNIDADE";"FUNCIONÁRIO";"IDADE";"SEXO";"CPF";"DATA DE INCLUSÃO";"DATA DA BAIXA DO EXAME";"CÓDIGO MÉDICO";"MÉDICO";"ALTURA (cm)";"PESO (kg)";"HORA INÍCIO ATENDIMENTO";"HORA FIM ATENDIMENTO";"CIRCUNFERENCIA ABDOMINAL";"CIRCUNFERENCIA QUADRIL";"IMC";"PA DIASTOLICA";"PA SISTOLICA";"PARECER";"PARECER ALTURA";"PARECER ESPAÇO CONFINADO";"PULSO";');
       
        $dados_questoes = $this->FichaClinica->getLabelFichaClinicaQuestoes();
        $questao = array();

        //varre o foreach
        foreach($dados_questoes AS $questoes_agrupadas) {

        	foreach($questoes_agrupadas AS $q) {

	        	//seta o label corretamente
	        	$label = $q['label_questao'];

	        	//reescreve as questoes
	        	$questao[] = $q['codigo'];

	        	//verifica se tem a label null para colocar o outros
	        	if(is_null($label)) {
	        		$label = 'OUTROS';
	        	}

	        	//insere o cabecalho
	        	$cabecalho .= '"'.strtoupper(trim($label)).'";';
	        	// $cabecalho .= '"'.$q['codigo'] ." - ". strtoupper(trim($label)).'";';

	        	switch ($q['codigo']) {
	        		case '9'://cancer
	        		case '35': // doenca do coracao
	        		case '49': // PROBLEMAS RESPIRATÓRIOS ?
	        		case '61': // doencas nos rins
	        		case '70': // doencas no figado
	        		case '109': // doencas no estomago
	        		case '117': //problemas de visao
	        		case '122': //problemas de audicao
	        		case '126': //doencas psiquiatricas
	        		case '137': //cancer?
	        		case '143': //alguma doenca nao mencionada
	        		case '148': //ja sofreu alguma internacao
	        		case '150': //ja sofreu alguma cirurgia
	        		case '195': //O EXAMINADO APRESENTA CARACTERÍSTICAS QUE O ENQUADREM NA CONDIÇÃO DE PCD ?
	        			$questao[] = $q['codigo']."_outros";
	        			$cabecalho .= '"'. strtoupper(trim($label)). " - OUTROS" .'";';
	        			break;
	        		
	        	}

        	}


        }//fim foreach

        //concatena o cabecalho
        echo $cabecalho."\n";

        foreach($dados_fc AS $value) {

        	$value = $value[0];

            # code...
        	$linha =  $value['codigo_ficha_clinica'].';';
            $linha .= $value['codigo_pedido_exame'].';';
            $linha .= Comum::converterEncodingPara($value['razao_social_cliente'], 'ISO-8859-1').';';
            $linha .= Comum::converterEncodingPara($value['nome_fantasia'], 'ISO-8859-1').';';
            $linha .= Comum::converterEncodingPara($value['nome_funcionario'], 'ISO-8859-1').';';
            $linha .= AppModel::calcularIdade($value['nascimento_funcionario']).';';
            $linha .= AppModel::defineSexo($value['sexo_funcionario']).';';
            $linha .= AppModel::formataCpf($value['cpf_funcionario']).';';
            $linha .= AppModel::formataData($value['fc_data_inclusao']).';';
            $linha .= AppModel::formataData($value['data_baixa']).';';
            $linha .= $value['codigo_medico'].';';
            $linha .= Comum::converterEncodingPara($value['medico'], 'ISO-8859-1').';';

            if(!is_null($value['altura_mt']) && !is_null($value['altura_cm'])){
            	$linha .= $value['altura_mt'].','. $value['altura_cm'].';';	
            } else {
            	$linha .= '"'.''.'";';
            }

            if(!is_null($value['peso_kg']) && !is_null($value['peso_gr'])){
            	$linha .= $value['peso_kg']. ','. $value['peso_gr'].';';	
            } else if (!is_null($value['peso_kg']) && is_null($value['peso_gr'])){
            	$linha .= $value['peso_kg'].';';	
            } else if (is_null($value['peso_kg']) && !is_null($value['peso_gr'])){
            	$linha .= '"'.''.'";';
            } else if (is_null($value['peso_kg']) && is_null($value['peso_gr'])){
            	$linha .= '"'.''.'";';
            }

            $linha .= Comum::formataHora($value['hora_inicio_atendimento']).';';
            $linha .= Comum::formataHora($value['hora_fim_atendimento']).';';
            $linha .= $value['circunferencia_abdominal'].';';
            $linha .= $value['circunferencia_quadril'].';';
            $linha .= $value['imc'].';';
            $linha .= $value['pa_diastolica'].';';
            $linha .= $value['pa_sistolica'].';';

            if($value['parecer'] == 1){
            	$linha .= '"'.'APTO'.'";';
            } else if($value['parecer'] === 0){
            	$linha .= '"'.'INAPTO'.'";';
            } else {
            	$linha .= ''.';';
            }

            if($value['parecer_altura'] == 1){
            	$linha .= '"'.'APTO'.'";';
            } else if($value['parecer_altura'] === 0){
            	$linha .= '"'.'INAPTO'.'";';
            } else {
            	$linha .= ''.';';
            }

            if($value['parecer_espaco_confinado'] == 1){
            	$linha .= '"'.'APTO'.'";';
            } else if($value['parecer_espaco_confinado'] === 0){
            	$linha .= '"'.'INAPTO'.'";';
            } else {
            	$linha .= ''.';';
            }
           
            $linha .= $value['pulso'].';';


            //busca as respostas da ficha clinica
            $respostas = $this->FichaClinica->getFichasClinicasRespostas($value['codigo_ficha_clinica']);
            $valor_questao = array();

        	//varre as questoes para ordernar corretamente 
        	foreach($questao AS $key_questao => $d_questao) {
        		$valor_questao[$key_questao] = "";
            	if(isset($respostas[$d_questao])) {
            		$valor_questao[$key_questao] = $respostas[$d_questao];
            	}

        	}//fim varre as queestoes
	        
	        $linha .= implode(";",$valor_questao);

            $linha .= "\n";
            
            unset($value);

            echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
        }//fim while
        
        //mata o metodo
        die();
    }//fim

    private function busca_exames_fc($codigo_pedido_exame){
    	$get_exames = $this->PedidoExame->exames_pcmso_fc($codigo_pedido_exame);
    	$this->set(compact('get_exames'));
    }

    public function get_itens_sem_comparecimento($codigo_pedido_exame) {
    	$this->autoRender = false;

    	//busca dentro do pedido de exame, o item pedido exame ASO, e se o comparecimento esta zero
    	$busca_itens_pedidos = $this->ItemPedidoExame->find('first', array('conditions' => array('codigo_pedidos_exames' => $codigo_pedido_exame, 'codigo_exame' => $this->Configuracao->getChave('INSERE_EXAME_CLINICO'))));

    	$return = 0;

    	if($busca_itens_pedidos){

    		if($busca_itens_pedidos['ItemPedidoExame']['compareceu'] == 0){//verifica senao houve comparecimento
    			$return = 1;//exame sem comparecimento    		
    			$msg = 'Olá, O exame que você está salvando está com o status de não comparecimento, ao clicar em Sim, o exame será atualizado para "COMPARECEU" e status realizado. Deseja continuar?';    		
    		}

    		if($busca_itens_pedidos['ItemPedidoExame']['compareceu'] == 1){

    			if(empty($busca_itens_pedidos['ItemPedidoExame']['data_realizacao_exame'])){
    				$return = 2;//exame pendente    		
    				$msg = 'Olá, O exame que você está salvando está com o status PENDENTE, ao clicar em Sim, o exame será atualizado status REALIZADO. Deseja continuar?'; 
    			}
    		}
    	}

    	return json_encode(array('return' => $return, 'msg' => !isset($msg) ? '0' : $msg));
    }
}
