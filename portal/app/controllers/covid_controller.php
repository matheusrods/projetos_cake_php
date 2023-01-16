<?php
class CovidController extends AppController {
	
	public $name = 'Covid';
	public $helpers = array('BForm', 'Html', 'Ajax', 'Highcharts');
    
    /**
	 * [$uses description]
	 * 
	 * atributo para instanciar as classes models
	 * 
	 * @var array
	 */
	var $uses = array(
		'Covid',
		'Usuario',
		'PedidoExame',
		'GrupoEconomicoCliente',
		'UsuarioGca',
		'ClienteEndereco',
		'FornecedorEndereco',
	);

    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(
        	'brasil_io_usuario'
        	,'resultado_exame_sintetico_listagem'
        	,'resultado_exame_analitico'
        	,'resultado_exame_analitico_listagem'
        ); // TODO - RMM Remover
    }

	public function index()
	{
		$this->pageTitle = 'Covid';
	}

	private function getTokenPWBI()
	{

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://login.microsoftonline.com/d87506ea-36fa-43b0-bd0f-4631c99d847a/oauth2/v2.0/token",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_POSTFIELDS => "grant_type=client_credentials&scope=https%3A//analysis.windows.net/powerbi/api/.default&client_id=b5410605-8cce-4ea0-bcad-9f8ac7290350&client_secret=3cEz2bUR.ijQgmThm-km0Ij076%7E%7EwctE9B",
		  CURLOPT_HTTPHEADER => array(
		    "Content-Type: application/x-www-form-urlencoded",
		    "Cookie: fpc=AkGpFmeiNmZEqbhgBekNdWVEsycAAQAAAP6CptYOAAAA; x-ms-gateway-slice=prod; stsservicecookie=ests"
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		
		$dados = json_decode($response);

		return $dados->access_token;
		
	}//fim getToken

	private function getChavesPW($url,$dados_post)
	{

		$token = $this->getTokenPWBI();

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS =>$dados_post,
		  CURLOPT_HTTPHEADER => array(
		    "Authorization: Bearer ".$token,
		    "Content-Type: application/json"
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		$dados = json_decode($response);

		return $dados;
	}

	public function lyn()
	{
		$this->pageTitle = '';

		
	    $reportId = "bc025f65-d1e4-434c-8505-8e796812791d";
		$url = "https://api.powerbi.com/v1.0/myorg/groups/22715f02-063f-4c43-b11d-278afa3e3e3f/reports/{$reportId}/GenerateToken";
		// print $url;exit;
		
		//no username deve ser trocado para os codigos codigo_usuario_logado; 
		// pega o codigo do usuario logado
		$dados_usuario = $this->BAuth->user();
		$codigo_usuario_logado = $dados_usuario['Usuario']['codigo'];

		if(empty($codigo_usuario_logado)) {
			$this->BSession->setFlash('acesso_nao_permitido');
			$this->redirect(array('controller' => 'painel', 'action' => 'modulo_comercial'));
		}

		$roles = "gestor_empresa";
		if(empty($dados_usuario['Usuario']['codigo_cliente'])) {
			$roles = "usuario_interno";
		}

		// debug($codigo_usuario_logado);exit;

		//seta o post
		$dados_post = '{
						  "accessLevel": "Edit",
						  "identities": [
						    {
						      "username": "'.$codigo_usuario_logado.'",
						      "roles": [
						        "'.$roles.'"
						      ],
						      "datasets": [
						        "8882605d-2c65-4f70-ad18-9243c1ae5d22"
						      ]
						    }
						  ]
						}';
		$dados = $this->getChavesPW($url,$dados_post);

		$erro = '';
		$access_token = '';
		$report_id = '';
		if(isset($dados->error)) {
			$erro = "Não foi possivel gerar o dashboardo do lyn.";
		}
		else {
			$access_token = $dados->token;
		}

		// TODO - colocar aqui, valores vindos na url
	    $grafico = 'lyn'; 
	    $accessToken = $access_token;
	    
	    $groupId = "6e793889-5cb8-474d-9479-e05d650f0214";


		$this->set(compact('erro', 'access_token','accessToken','reportId','groupId','grafico'));

	}

	public function lyn_rh()
	{
		$this->pageTitle = 'Lyn Covid-19';

		$reportId = "ea13e1f1-b4e1-4f62-8b26-b5ee49157e26";
		$url = "https://api.powerbi.com/v1.0/myorg/groups/22715f02-063f-4c43-b11d-278afa3e3e3f/reports/{$reportId}/GenerateToken";
		// print $url;exit;
		
		//no username deve ser trocado para os codigos codigo_usuario_logado; 
		// pega o codigo do usuario logado
		$dados_usuario = $this->BAuth->user();
		$codigo_usuario_logado = $dados_usuario['Usuario']['codigo'];

		if(empty($codigo_usuario_logado)) {
			$this->BSession->setFlash('acesso_nao_permitido');
			$this->redirect(array('controller' => 'painel', 'action' => 'modulo_comercial'));
		}

		$roles = "gestor_empresa";
		if(empty($dados_usuario['Usuario']['codigo_cliente'])) {
			$roles = "usuario_interno";
		}

		// debug($codigo_usuario_logado);exit;

		//seta o post
		$dados_post = '{
						  "accessLevel": "Edit",
						  "identities": [
						    {
						      "username": "'.$codigo_usuario_logado.'",
						      "roles": [
						        "'.$roles.'"
						      ],
						      "datasets": [
						        "9f931bd0-1cc5-4eaf-90a8-ff1923138c9c"
						      ]
						    }
						  ]
						}';
		$dados = $this->getChavesPW($url,$dados_post);

		$erro = '';
		$access_token = '';
		$report_id = '';
		if(isset($dados->error)) {
			$erro = "Não foi possivel gerar o dashboardo do lyn.";
		}
		else {
			$access_token = $dados->token;
		}

		// TODO - colocar aqui, valores vindos na url
	    $grafico = 'lyn'; 
	    $accessToken = $access_token;
	    
	    
	    $groupId = "6e793889-5cb8-474d-9479-e05d650f0214";


		$this->set(compact('erro', 'access_token','accessToken','reportId','groupId','grafico'));

	}


	public function brasil_io()
	{
		$this->pageTitle = 'Mapa Covid-19 Brasil';

		//seta o post
		$url = "https://api.powerbi.com/v1.0/myorg/groups/22715f02-063f-4c43-b11d-278afa3e3e3f/reports/a361e2b4-b8cb-4444-a52a-68dcb90dfd1f/GenerateToken";
		

		//no username deve ser trocado para os codigos codigo_usuario_logado; 
		// pega o codigo do usuario logado
		$dados_usuario = $this->BAuth->user();
		$codigo_usuario_logado = $dados_usuario['Usuario']['codigo'];

		if(empty($codigo_usuario_logado)) {
			$this->BSession->setFlash('acesso_nao_permitido');
			$this->redirect(array('controller' => 'painel', 'action' => 'modulo_comercial'));
		}

		$roles = "gestor_empresa";
		if(empty($dados_usuario['Usuario']['codigo_cliente'])) {
			$roles = "usuario_interno";
		}
		
		// $dados_post = '{"accessLevel": "Edit"}';
		//seta o post
		$dados_post = '{
						  "accessLevel": "Edit",
						  "identities": [
						    {
						      "username": "'.$codigo_usuario_logado.'",
						      "roles": [
						        "'.$roles.'"
						      ],
						      "datasets": [
						        "37bfd30a-2d06-4710-b4ee-05c5ed5ff1df"
						      ]
						    }
						  ]
						}';



		$dados = $this->getChavesPW($url,$dados_post);

		$erro = '';
		$access_token = '';
		$report_id = '';
		if(isset($dados->error)) {
			$erro = "Não foi possivel gerar o dashboardo do brasil io.";
		}
		else {
			$access_token = $dados->token;
		}

	    $grafico = 'lyn'; 
	    $accessToken = $access_token;
	    
	    $reportId = "a361e2b4-b8cb-4444-a52a-68dcb90dfd1f";
	    $groupId = "efb5dda9-9728-4ea8-8173-ff726233258b";

		$this->set(compact('erro', 'access_token','accessToken','reportId','groupId','grafico'));

	}
	
	public function brasil_io_usuario2($codigo_usuario=null)
	{

		if(empty($codigo_usuario)) {
			print "Não foi possivel gerar o relatorio Covid.";
			exit;
		}

		//busca o usuario logado para pegar o endereco do funcionario e se nao tiver o endereco do usuario
		
		$query = "
					SELECT TOP 1 ISNULL(fe.cidade,ud.cidade) AS cidade,
						ISNULL(fe.estado_descricao,ud.estado) AS estado
					FROM usuario u
					    INNER JOIN usuarios_dados ud on u.codigo = ud.codigo_usuario
					    LEFT JOIN funcionarios f on ud.cpf = f.cpf
					    LEFT JOIN funcionarios_enderecos fe on f.codigo = fe.codigo_funcionario
					WHERE u.codigo = ".$codigo_usuario.";";
		$dados = $this->Usuario->query($query);
		$cidade = $dados[0][0]['cidade'];
		$estado = $dados[0][0]['estado'];

		// debug($cidade);exit;

		//tratamento se a cidade for null
		if(empty($cidade)) {
			$cidade = 'Sao Paulo';
		}

		if(empty($estado)) {
			$estado = 'SP';
		}
		
		$this->pageTitle = 'Brasil I.O';

		$reportId = "7b746182-bf24-41b3-8227-f3e45b331a34";
		// $reportId = "5a536a03-767c-401f-9596-1c35190eb67c";
    	$groupId = "efb5dda9-9728-4ea8-8173-ff726233258b";

		//seta o post
		$url = "https://api.powerbi.com/v1.0/myorg/groups/22715f02-063f-4c43-b11d-278afa3e3e3f/reports/{$reportId}/GenerateToken";
		$dados_post = '{"accessLevel": "Edit"}';

		$dados = $this->getChavesPW($url,$dados_post);

		$erro = '';
		$access_token = '';
		$report_id = '';
		if(isset($dados->error)) {
			$erro = "Não foi possivel gerar o dashboardo do brasil io.";
		}
		else {
			$access_token = $dados->token;
		}

		$this->set(compact('erro', 'access_token','groupId','reportId','cidade','estado'));  

	}


	public function brasil_io_usuario($codigo_usuario=null)
	{

		if(empty($codigo_usuario)) {
			print "Não foi possivel gerar o relatorio Covid.";
			exit;
		}

		//busca o usuario logado para pegar o endereco do funcionario e se nao tiver o endereco do usuario
		
		$query = "
					SELECT TOP 1 ISNULL(fe.cidade,ud.cidade) AS cidade,
						ISNULL(fe.estado_descricao,ud.estado) AS estado
					FROM usuario u
					    INNER JOIN usuarios_dados ud on u.codigo = ud.codigo_usuario
					    LEFT JOIN funcionarios f on ud.cpf = f.cpf
					    LEFT JOIN funcionarios_enderecos fe on f.codigo = fe.codigo_funcionario
					WHERE u.codigo = ".$codigo_usuario.";";
		$dados = $this->Usuario->query($query);
		$cidade = $dados[0][0]['cidade'];
		$estado = $dados[0][0]['estado'];

		// debug($cidade);exit;

		//tratamento se a cidade for null
		if(empty($cidade)) {
			$cidade = 'Sao Paulo';
		}

		if(empty($estado)) {
			$estado = 'SP';
		}
		
		$this->pageTitle = 'Brasil I.O';

		$reportId = "867fb8fd-d902-482d-807d-c635762742d4";
		// $reportId = "5a536a03-767c-401f-9596-1c35190eb67c";
    	$groupId = "efb5dda9-9728-4ea8-8173-ff726233258b";

		//seta o post
		$url = "https://api.powerbi.com/v1.0/myorg/groups/22715f02-063f-4c43-b11d-278afa3e3e3f/reports/{$reportId}/GenerateToken";
		$dados_post = '{"accessLevel": "Edit"}';

		$dados = $this->getChavesPW($url,$dados_post);

		$erro = '';
		$access_token = '';
		$report_id = '';
		if(isset($dados->error)) {
			$erro = "Não foi possivel gerar o dashboardo do brasil io.";
		}
		else {
			$access_token = $dados->token;
		}

		$this->set(compact('erro', 'access_token','groupId','reportId','cidade','estado'));  

	}

	/**
	 * [resultado_exame_sintetico ]
	 * @return [type] [description]
	 */
	public function resultado_exame_sintetico()
	{
		//titulo da pagina
		$this->pageTitle = 'Resultado de Exame Sintético';
		
		//inserido na filtros controller
		$filtros = $this->Filtros->controla_sessao($this->data, 'UsuarioGca');
		
		if(!isset($filtros['codigo_cliente'])) {
			$filtros['codigo_cliente'] = null;
		}

		if(!isset($filtros['tipo_periodo'])) {
			$filtros['tipo_periodo'] = '';
		}

		if(!isset($filtros['agrupamento'])) {
			$filtros['agrupamento'] = 4;
		}

		if(empty($filtros['data_inicio'])) {
			$filtros['data_inicio'] = '01/'.date('m/Y');
			$filtros['data_fim'] = date('d/m/Y');
		}
		
		//pega o usuario que esta logdao
		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}
		
		//pega os filtros setados que estao em sessao
		$this->data['UsuarioGca'] = $filtros;

		// debug($this->data);

		$tipos_agrupamento = $this->UsuarioGca->tiposAgrupamento();

		$this->set(compact('tipos_agrupamento'));
		$this->carrega_combos_grupo_economico('UsuarioGca');
		$this->carrega_combo_periodo();

	}

	/**
	 * [resultado_exame_sintetico_listagem ]
	 * @return [type] [description]
	 */
	public function resultado_exame_sintetico_listagem()
	{
		//pega os filtros da sessão
		$filtros = $this->Filtros->controla_sessao($this->data, 'UsuarioGca');
		//verifica o usuario logado
		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}
		//pega os filtros da sessao e seta em um array
		$this->data['UsuarioGca'] = $filtros;
		//varialve auxiliar
		$dados = array();
		$agrupamento = $filtros['agrupamento'];

		$resultado = "'#327EEB'";
		if($agrupamento == 4) {
			$resultado = "'#D00000','#44AA11'";
			if($filtros['tipo_periodo'] == 1) { //vermelho = positivo
				$resultado = "'#D00000'";
			}
			elseif($filtros['tipo_periodo'] == 2) { //vermelho = positivo
				$resultado = "'#44AA11'";
			}
		}

		$erro = false;
		$data_final = strtotime(AppModel::dateToDbDate2($filtros['data_fim']));
		$data_inicial = strtotime(AppModel::dateToDbDate2($filtros['data_inicio']));
		if ($data_inicial > $data_final){
			$erro = true;
		}

		$seconds_diff = $data_final - $data_inicial;
		$dias = floor($seconds_diff/3600/24);
		if ($dias > 365) {
			$erro = true;
		}

		// debug($resultado);

		if (!empty($filtros['codigo_cliente']) && !$erro) {
			$conditions = $this->UsuarioGca->converteFiltrosEmConditions($filtros);
			$dados = $this->UsuarioGca->resultado_exame_sintetico($agrupamento, $conditions);
			// debug($dados);exit;
		}

		//seta para pegar os dados na view
		$this->set(compact('dados', 'agrupamento','resultado'));

	}

	/**
     * Metodo para montar os arrays de carregamento dos combos
     */ 
	public function carrega_combos_grupo_economico($model) 
	{
		//instancia as models
		$this->loadModel('Cargo');
		$this->loadModel('Setor');
		//pega as unidades
		$unidades = $this->GrupoEconomicoCliente->lista($this->data[$model]['codigo_cliente']);
		//pega os setores
		$setores = $this->Setor->lista($this->data[$model]['codigo_cliente']);
		//pega os cargos
		$cargos = $this->Cargo->lista($this->data[$model]['codigo_cliente']);


		//filtros cidades unidades
		//monta a cidade unidade
		$cidade_unidade = array();
		$estado_unidade = array();
		$cidade_credenciado = array();
		$estado_credenciado = array();
		
		//seta os valores para recuperar na view
		$this->set(compact('unidades', 'setores', 'cargos', 'cidade_unidade','estado_unidade','cidade_credenciado','estado_credenciado'));

    } //fim carrega_combos_grupo_economico

    /**
     * metodo para pegar os tipos de pesquisa que ira existir
     */ 
    public function carrega_combo_periodo() 
    {
    	//tipos de periodo
		$tipos_periodo = array(
			''  => 'Todos',
			'1' => 'Positivo',
			'2' => 'Negativo',
		);

		$this->set(compact('tipos_periodo'));
    } //fim carrega_combo_periodo

	/**
	 * [resultado_exame_analitico ]
	 * @return [type] [description]
	 */
	public function resultado_exame_analitico()
	{
		
		$this->pageTitle = 'Resultado de Exame Analítico';
		$this->layout = 'new_window';
		
		$filtros = $this->Filtros->controla_sessao($this->data, 'UsuarioGca');

		// debug($filtros);exit;
		
		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$this->data['UsuarioGca'] = $filtros;


		$this->carrega_combos_grupo_economico('UsuarioGca');
		$this->carrega_combo_periodo();
		

	}

	/**
	 * [resultado_exame_analitico_listagem ]
	 * @return [type] [description]
	 */
	public function resultado_exame_analitico_listagem()
	{
		// debug('opa');exit;

		$filtros = $this->Filtros->controla_sessao($this->data, 'UsuarioGca');

		// debug($filtros);

		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$dados = array();
		if (!empty($filtros['codigo_cliente'])) {
			$conditions = $this->UsuarioGca->converteFiltrosEmConditions($filtros);
			
			// $dados = $this->UsuarioGca->baixa_exames_analitico('all', compact('conditions'));
			//monsta a query na model, por que estava dando problema de memoria, pra resolver é necessario termos que colocar a paginação na tela
			$dado = $this->UsuarioGca->resultado_exame_analitico(null, $conditions);
			// debug($dado);exit;
			//paginate
			$this->paginate['UsuarioGca'] = array(
				'recursive' => -1,	
				'fields' => $dado['fields'],
				'joins' => $dado['joins'],
				'conditions' => $dado['conditions'],
				'group' => $dado['group'],
				'limit' => 50
			);
			//printa o paginate pra ctp
			$dados = $this->paginate('UsuarioGca'); 

			// debug($dados);exit;
		}

		// pr($dados);exit;

		$this->set(compact('dados'));

	}


}
