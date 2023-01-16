<?php
class CatController extends AppController {
	public $name = 'Cat';
	public $components = array('Filtros', 'RequestHandler','ExportCsv');
	public $helpers = array('BForm', 'Html', 'Ajax', 'Highcharts', 'Ithealth');

	public $uses = array(
		'Cat',
		'GrupoEconomicoCliente',
		'ClienteFuncionario',
		'FuncionarioSetorCargo',
		'PedidoExame',
		'Medico',
		'Cliente',
		'EnderecoEstado',
		'Esocial',
		'Funcionario',
		'CatLog'
	);

	public $emitentes = array(
		1 => 'Empregador',
		2 => 'Sindicato',
		3 => 'Médico',
		4 => 'Segurado ou dependente',
		5 => 'Autoridade pública'
		);

	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array());
	}//FINAL FUNCTION beforeFilter

	/**
	 * [index description]
	 * 
	 * metodo para impressão da tela com os filtros
	 * 
	 * @return [type] [description]
	 */
	public function index()
	{
		$this->pageTitle = 'CAT - Comunicação de acidente de trabalho';

		$filtros = $this->Filtros->controla_sessao($this->data, 'Cat');
		
		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$this->data['Cat'] = $filtros;
		
		$this->carrega_combos_grupo_economico('Cat');
	}//fim index

	/**
	 * [carrega_combos_grupo_economico description]
	 * 
	 * metodo para carregar os combos de grupos economicos
	 * 
	 * @param  [type] $model [description]
	 * @return [type]        [description]
	 */
	public function carrega_combos_grupo_economico($model) 
	{
		$this->loadModel('Cargo');
		$this->loadModel('Setor');
		$this->loadModel('GrupoEconomico');

		$codigo_cliente = $this->data[$model]['codigo_cliente'];

    	if(!empty($codigo_cliente)){
			$codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
    	}

		$unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
		$setores = $this->Setor->lista($codigo_cliente);
		$cargos = $this->Cargo->lista($codigo_cliente);

		$this->set(compact('unidades', 'setores', 'cargos'));
	
	}//fim carrega_combos_grupo_economico

	/**
	 * [listagem description]
	 * 
	 * metodo para listagem dos funcionários
	 * 
	 * @return [type] [description]
	 */
	public function listagem() 
	{
		//seta que é um layout em ajax
		$this->layout = 'ajax';
		//pega os filtros que estão em sessao
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cat->name);

		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}
			//verifica se é usuario de cliente
			// if(!is_null($this->BAuth->user('codigo_cliente'))) {
			// 	$conditions['OR']['GrupoEconomico.codigo_cliente'] = $this->BAuth->user('codigo_cliente');
			// 	$conditions['OR']['Cliente.codigo'] = $this->BAuth->user('codigo_cliente');
			// }

		//seta a variavel
		$funcionarios = array();

		//valida se tem filtros setados
		if(!empty($filtros['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->normalizaCodigoCliente($filtros['codigo_cliente']);
			//pega o codigo do grupo economico
			$dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $filtros['codigo_cliente']), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
			//verifica se tem codigo do grupo economico
			if(isset($dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'])) {
				$codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];
			}

			$conditions = $this->FuncionarioSetorCargo->converteFiltrosEmConditions($filtros);

			//condicao incluida para nao deixar apresentar um setor e cargo que esteja com a data fim do setor/cargo preenchida
			$conditions[] = array('FuncionarioSetorCargo.data_fim IS NULL');

			$order = array('Cliente.razao_social', 'Setor.descricao', 'Cargo.descricao', 'Funcionario.nome');

			$joins = array(
				array(
					'table' => 'RHHealth.dbo.cliente_funcionario',
					'alias' => 'ClienteFuncionario',
					'type' => 'INNER',
					'conditions' => 'ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario',
					),
				array(
					'table' => 'RHHealth.dbo.cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao',
					),
				array(
					'table' => 'RHHealth.dbo.funcionarios',
					'alias' => 'Funcionario',
					'type' => 'INNER',
					'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
					),
				array(
					'table' => 'RHHealth.dbo.setores',
					'alias' => 'Setor',
					'type' => 'INNER',
					'conditions' => 'Setor.codigo = FuncionarioSetorCargo.codigo_setor',
					),
				array(
					'table' => 'RHHealth.dbo.cargos',
					'alias' => 'Cargo',
					'type' => 'INNER',
					'conditions' => 'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo',
					),

				);

			$fields = array('Funcionario.nome','Funcionario.codigo','Cliente.codigo', 'Cliente.razao_social', 'Cliente.nome_fantasia','Cargo.codigo', 'Cargo.descricao','Setor.codigo', 'Setor.descricao', 'FuncionarioSetorCargo.codigo', 'FuncionarioSetorCargo.codigo_cliente_alocacao', 'FuncionarioSetorCargo.codigo_cliente_funcionario', 'ClienteFuncionario.ativo');

			$this->paginate['FuncionarioSetorCargo'] = array(
				'recursive' => -1,	
				'fields' => $fields,
				'joins' => $joins,
				'conditions' => $conditions,
				'limit' => 50,
				'order' => $order
				);

			$funcionarios = $this->paginate('FuncionarioSetorCargo');

		}//fim validacao

		$this->set(compact('funcionarios'));

	}//fim listagem


	/**
	 * [lista_cat description]
	 * 
	 * metodo para listar todas as cats do funcionario pela matricula/função (unidade/setor/cargo)
	 * 
	 * @param  [type]  $codigo_funcionario_setor_cargo [description]
	 * @param  integer $id_pedido                      [description]
	 * @return [type]                                  [description]
	 */
	public function lista_cat($codigo_funcionario_setor_cargo, $id_pedido = 0) 
	{
		//seta o titulo da pagina		
		$this->pageTitle = 'Listagem de Cats';
		//recupera os dados de condiguração da função do funcionario
		$dados_consulta = $this->FuncionarioSetorCargo->find('first', array('conditions' => array('FuncionarioSetorCargo.codigo' => $codigo_funcionario_setor_cargo), 'recursive' => -1));

		/***************************************************
		 * validacao adicionado para evitar o cliente de
		 * burlar o acesso e ver dados de outros clientes;
		 ***************************************************/
		if(!is_null($this->BAuth->user('codigo_cliente'))) {
			
			$dados_grupo_economico_cliente = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $this->BAuth->user('codigo_cliente')), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));

			$dados_grupo_economico_solicitado = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $dados_consulta['FuncionarioSetorCargo']['codigo_cliente']), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
			
			if($dados_grupo_economico_cliente['GrupoEconomicoCliente']['codigo_grupo_economico'] != $dados_grupo_economico_solicitado['GrupoEconomicoCliente']['codigo_grupo_economico']) {
				$this->BSession->setFlash('acesso_nao_permitido');
				$this->redirect(array('controller' => 'cat', 'action' => 'index'));
			}
		}//fim validacoa de burlar codigo_cliente
		
		//verifica se tem os dados da consulta do funcionario		
		if($dados_consulta) {
			//pega a lista de cats
			$lista_cats = $this->Cat->retornaCatFuncionario($dados_consulta['FuncionarioSetorCargo']['codigo']);

		} else {
			$lista_cats = array();
		}

		//pega os dados do grupo economico
		$dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $dados_consulta['FuncionarioSetorCargo']['codigo_cliente_alocacao']), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
		
		//seta o codigo do grupo economico
		$codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];
		

		$dados_cliente_funcionario = $this->PedidoExame->retornaEstrutura($codigo_funcionario_setor_cargo);		

		$codigo_funcionario = $dados_cliente_funcionario['Funcionario']['codigo'];
		//ajustado para buscar pela matriz
		$codigo_cliente = $dados_consulta['FuncionarioSetorCargo']['codigo_cliente_alocacao'];		

		$codigo_cliente_funcionario = $dados_consulta['FuncionarioSetorCargo']['codigo_cliente_funcionario'];

		$this->set(compact('lista_cats', 'dados_cliente_funcionario', 'codigo_funcionario_setor_cargo','codigo_cliente_funcionario', 'codigo_grupo_economico','codigo_funcionario','codigo_cliente'));
	
	} //FINAL FUNCTION lista_cat

	/**
	 * [imprimir_relatorio description]
	 * 
	 * metodo para chamar a função do jasper e imprimir o relatorio
	 * 
	 * @param  [type] $codigo_cat [description]
	 * @return [type]             [description]
	 */
	public function imprimir_relatorio($codigo_cat)
	{
		$this->autoRender = false;

		// opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME'=>'/reports/RHHealth/cat', // especificar qual relatório
			'FILE_NAME'=> basename( 'cat.pdf' ) // nome do relatório para saida
		);

		// parametros do relatorio
		$parametros = array('CODIGO_CAT' => $codigo_cat);

		//pega o codigo_cliente
		$cat =  $this->Cat->find('first',array('conditions' => array('codigo' => $codigo_cat)));
		$dados['CODIGO_CLIENTE'] = $cat['Cat']['codigo_cliente'];

		$this->loadModel('Cliente');
		$parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($dados);
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

	}//fim imprimir_relatorio

	public function incluir($codigo_funcionario, $codigo_cliente,$codigo_funcionario_setor_cargo){
		$this->pageTitle = 'CAT - Incluir';	
		
		if($this->RequestHandler->isPost()) {
			if($this->data['Cat']['lateralidade_corpo'] == ''){
				$this->data['Cat']['lateralidade_corpo'] = NULL;
			}

			// PD-192 - remove espaço do campo [observacao_cat]
			$this->data['Cat']['observacao_cat'] = trim($this->data['Cat']['observacao_cat']);			

			$this->data['Cat']['codigo_cliente'] = $codigo_cliente;
			$this->data['Cat']['codigo_funcionario'] = $codigo_funcionario;
			$this->data['Cat']['codigo_funcionario_setor_cargo'] = $codigo_funcionario_setor_cargo;			
			
			//trata o cid10
			if(isset($this->data['cid10'][0])) {
				if(isset($this->data['cid10'][0]['doenca'])) {
					if(!empty($this->data['cid10'][0]['doenca'])) {
						$this->data['Cat']['cid10'] = $this->data['cid10'][0]['doenca'];
					}
				}
			}//fim tratamento cid10

			if($this->Cat->incluir($this->data)) {
				if(!empty($this->data['Cat']['codigo_cliente'])){
					//notifica o cliente que incluiu uma CAT
					$this->PedidoExame->alerta_esocial($this->data['Cat']['codigo_cliente'], 's2210', 'email_esocial_s2210');				
				}
				$this->BSession->setFlash('save_success');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->BSession->setFlash('save_error');
			}
		} 

		$dados = $this->Cat->obtemDadosDoFuncionario($codigo_funcionario,$codigo_cliente, $codigo_funcionario_setor_cargo);

		//dados do funcionario para a view
		if(!empty($dados['Funcionario'])){
			$this->data['Funcionario'] = $dados['Funcionario'];			
		}

		$this->set(compact('dados', 'codigo_funcionario', 'codigo_cliente', 'codigo_funcionario_setor_cargo'));
		$this->loadinginfos();
	}//FINAL FUNCTION INCLUIR

	public function editar($codigo, $retificacao = null)
	{
		$this->pageTitle = 'CAT - editar';
		if($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {
			if($this->data['Cat']['lateralidade_corpo'] == ''){
				$this->data['Cat']['lateralidade_corpo'] = NULL;
			}

			// PD-192 - remove espaço do campo [observacao_cat]
			$this->data['Cat']['observacao_cat'] = trim($this->data['Cat']['observacao_cat']);
			
			//trata o cid10
			if(isset($this->data['cid10'][0])) {
				if(isset($this->data['cid10'][0]['doenca'])) {
					if(!empty($this->data['cid10'][0]['doenca'])) {
						$this->data['Cat']['cid10'] = $this->data['cid10'][0]['doenca'];
					}
				}
			}//fim tratamento cid10

			if($this->Cat->atualizar($this->data)) {
				$this->BSession->setFlash('save_success');
				return $this->redirect(array('action' => 'index'));
			} else {
				// debug($this->Cat->validationErrors);exit;
				$this->BSession->setFlash('save_error');
			}
		}
		
		$this->data = $this->Cat->findByCodigo($codigo);
		$dados = $this->Cat->obtemDadosDoFuncionario($this->data['Cat']['codigo_funcionario'],$this->data['Cat']['codigo_cliente'], $this->data['Cat']['codigo_funcionario_setor_cargo']);

		$codigo_funcionario = $this->data['Cat']['codigo_funcionario'];
		$codigo_cliente = $this->data['Cat']['codigo_cliente'];
		$codigo_funcionario_setor_cargo = $this->data['Cat']['codigo_funcionario_setor_cargo'];

		//dados do funcionario para a view
		if(!empty($dados['Funcionario'])){
			$this->data['Funcionario'] = $dados['Funcionario'];			
		}

		if($retificacao == 'retificar'){
			$this->set('chama_modal','1');
		} else {
			$this->set('chama_modal','0');
		}

		if(!empty($this->data['Cat'])) {
			if(!empty($this->data['Cat']['cep_acidentado'])) {
				$this->data['Cat']['cep_acidentado'] = str_pad($this->data['Cat']['cep_acidentado'],8,'0', STR_PAD_LEFT);
			}
		}

		

		$this->set(compact('dados', 'codigo_funcionario', 'codigo_cliente', 'codigo', 'codigo_funcionario_setor_cargo'));
		$this->loadinginfos();
	}

	////FINAL FUNCTION EDITAR

	//TABELA CAT

	//FUNCTION ESOCIAL_ACIDENTE 
	public function esocial_acidentes(){		
		$this->Esocial->virtualFields = array('codigo_e_descricao' => "CONCAT(codigo_descricao, ' - ', descricao)");
		$fields = array('codigo', 'codigo_e_descricao');
		$conditions = array('tabela'=> 16,);
		$acidentes = $this->Esocial->find('list',array('fields' => $fields, 'conditions' => $conditions));
		$this->set(compact('acidentes'));
	}
	
	//FINAL

	//FUNCTION CODIGO_ESOCIAL_13
	public function parte_corpo(){		
		$this->Esocial->virtualFields = array('codigo_e_descricao' => "CONCAT(codigo_descricao, ' - ', descricao)");
		$fields = array('codigo', 'codigo_e_descricao');
		$conditions = array('tabela'=> 13,);
		$partecorpo = $this->Esocial->find('list',array('fields' => $fields, 'conditions' => $conditions));
		$this->set(compact('partecorpo'));	
	}

	//FINAL

	//FUNCTION CODIGO_ESOCIAL_14_15
	public function agente_causador(){
		$this->Esocial->virtualFields = array('codigo_e_descricao' => "CONCAT(codigo_descricao, ' - ', descricao)");
		$fields = array('codigo', 'codigo_e_descricao');
		$conditions = array('tabela'=> array(14,15));
		$agente_causador = $this->Esocial->find('list',array('fields' => $fields, 'conditions' => $conditions));
		$this->set(compact('agente_causador'));	
	}

	private function loadinginfos(){

		$local_acidente =(
			array(
				'1' => '1 - Estabelecimento do empregador no Brasil',
				'2' => '2 - Estabelecimento do empregador no Exterior',
				'3' => '3 - Estabelecimento de terceiros onde o empregador presta serviços',
				'4' => '4 - Via pública',
				'5' => '5 - Área rural',
				'6' => '6 - Embarcação',
				'9' => '9 - Outros'
			)
		);

		$part_corpo = (
			array(
				'0' => '0 - Não aplicável',
				'1' => '1 - Esquerda',
				'2' => '2 - Direita',
				'3' => '3 - Ambas'
			)
		);

		$tipo_inscricao = (
			array(
				'1' => '1 - CNPJ',
				'3' => '3 - CAEPF',
				'4' => '4 - CNO'
			)
		);
		
		$estados = $this->EnderecoEstado->retorna_estados();
		$emitentes = $this->Cat->emitentes;
		$cats = $this->Cat->cats;
		$estados_civis = $this->Cat->estados_civis;
		$filiacoes = $this->Cat->filiacoes;
		$areas = $this->Cat->areas;
		$tipos = $this->Cat->tipos();
		$natureza_lesao = $this->Cat->natureza_lesao();
		
		$this->esocial_acidentes();
		$this->parte_corpo();
		$this->agente_causador();			
		$this->carrega_paises();			
		$this->set(compact('part_corpo', 'local_acidente', 'estados', 'emitentes', 'cats', 'estados_civis', 'filiacoes', 'areas', 'tipos', 'natureza_lesao', 'tipo_inscricao'));
	}

	private function carrega_paises(){
		$this->Esocial->virtualFields = array('codigo_e_descricao' => "CONCAT(codigo_descricao, ' - ', descricao)");
		$fields = array('codigo', 'codigo_e_descricao');
		$codigos_paises = $this->Esocial->find('list', array('conditions' => array('tabela' => 06), 'fields' => $fields));
		$this->set(compact('codigos_paises'));	
	}

	public function modal_retificacao_cat($codigo_cat){
		$evento_options = (
			array(
				'1' => 'Original',
				'2' => 'Retificação'				
			)
		);
		$this->data = $this->Cat->findByCodigo($codigo_cat);
		$this->log($this->data,'debug');
		$codigo_funcionario = $this->data['Cat']['codigo_funcionario'];
		$codigo_funcionario_setor_cargo = $this->data['Cat']['codigo_funcionario_setor_cargo'];
		$this->log($codigo_funcionario_setor_cargo,'debug');
		//set para a view
		$this->set(compact('codigo_cat', 'evento_options', 'codigo_funcionario', 'codigo_funcionario_setor_cargo'));
	}

	public function salvar_retificacao(){
		 //para nao solicitar um ctp
        $this->autoRender = false;

        $retorno['retorno'] = 'true';

        if(!empty($this->params['form'])){        	
        	//estancia os dados vindos do form para essa variavel
        	$dados_cat['Cat'] = $this->params['form'];
        	//buscar o funcionario para auxiliar no atualizar
        	$buscar_funcionario = $this->Funcionario->find('first', array('conditions' => array('Funcionario.codigo' => $this->params['form']['codigo_funcionario'])));
			$dados_cat['Funcionario'] = $buscar_funcionario['Funcionario'];
        	$dados_cat['Cat']['retirar_validate'] = 1;

        	if($dados_cat['Cat']['evento_retificacao'] == '' || $dados_cat['Cat']['evento_retificacao'] == null){        		
        		$retorno['retorno'] = 'false';
                $retorno['mensagem'] = 'Erro ao registrar Retificação, os dados estão vazios.';
        	} else {
        		if($dados_cat['Cat']['evento_retificacao'] == 2 && $dados_cat['Cat']['recibo_retificacao'] == ''){        			
        			$retorno['retorno'] = 'false';
                	$retorno['mensagem'] = 'Se for Retificação, é obrigatório preenchimento do numero do Recibo.';
        		} else {        			
        			if(!empty($dados_cat['Cat']['codigo'])){        				
        				if(!$this->Cat->atualizar($dados_cat)) {
							$retorno['retorno'] = 'false';
                			$retorno['mensagem'] = 'Erro ao registrar a Retificação.';
						}
        			} else {
        				$retorno['retorno'] = 'false';
                		$retorno['mensagem'] = 'Erro ao registrar a Retificação, precisamos saber qual CAT, para poder podermos registrar a retificação.';
        			}
        		}
        	}

        } else {
        	$retorno['retorno'] = 'false';
            $retorno['mensagem'] = 'Erro ao registrar Retificação, os dados estão vazios.';
        }        
        //retorna os dados com json de sucesso ou falha
        echo json_encode($retorno);
        exit;
	}

	public function cat_log($codigo_cat){
        //titulo da pagina
        $this->pageTitle = 'Log Cat';
        $this->layout = 'new_window';

        $dados = $this->CatLog->log_cat($codigo_cat);

        $this->set(compact('dados'));
    } //metodo para apresentar o log dos funcionarios
}//FINAL