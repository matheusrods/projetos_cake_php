<?php
class DadosSaudeConsultasController extends AppController {
	public $name = 'DadosSaudeConsultas';
	public $helpers = array('BForm', 'Html', 'Ajax', 'Highcharts');

	public $uses = array(
		'DadosSaudeConsulta',
		'Usuario',
		'UsuariosDados',
		'UsuariosImc',
		'Etnia',
		'GrauEscolaridade',
		'Questionario',
		'Medicamento',
		'Especialidade',
		'Resultado',
		'UsuariosQuestionario',
		'RelatoriosCockpit',
		'ClienteFuncionario'
		);

	public $components = array('RequestHandler', 'Session');

	// declaração de gadgets identificados por objeto e tamaho do canvas para a pagina dados gerais
	public $gadgets_dados_gerais = array(
		array('gadget' => 'colaboradores', 'span' => '3'),
		array('gadget' => 'genero', 'span' => '3'),
		array('gadget' => 'vencimento_exames', 'span' => '6'),
		array('gadget' => 'faixa_etaria', 'span' => '12'),
		array('gadget' => 'questionarios', 'span' => '12'),
		);

	// declaração de gadgets identificados por objeto e tamaho do canvas para a pagina perfil_saude
	public $gadgets_perfil_saude = array(
		array('gadget' => 'estatistica_saude', 'span' => '12')
		);

	// declaração de gadgets identificados por objeto e tamaho do canvas para a pagina colaboradores_atestados
	public $colaboradores_atestados = array(
		array('gadget' => 'colaboradores_atestados', 'span' => '12')
		);

	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow('colaboradores', 'genero', 'vencimento_exames', 'faixa_etaria', 'questionarios', 'estatistica_saude', 'analitico_vencimento_exames', 'analitico_situacao_questionarios', 'analitico_resultado', 'colaboradores_atestados', 'informacao_adicional_colaborador');
	}  


	public function informacao_adicional_colaborador($codigo_cliente_funcionario)
	{
		$this->pageTitle = 'Informações adicionais';

		$conditions = array();
		if($this->Session->check('filtro')) {
			$conditions = $this->Session->read('filtro');
		}

		$this->loadModel('Funcionario');
		$dados_funcionario = $this->Funcionario->obtemDadosPorCodigoClienteFuncionario($codigo_cliente_funcionario);
		$estados_civis = array('1' => 'Solteiro', '2' => 'Casado', '3' => 'Separado', '4' => 'Divorciado', '5' => 'Viúvo', '6' => 'Outros');
		$this->set(compact('dados_funcionario', 'estados_civis'));

		$visitas_medicos = $this->Funcionario->listaVisitasAoMedico($codigo_cliente_funcionario);
		$this->set(compact('visitas_medicos'));

		$exames_ocupacionais =  $this->Funcionario->listaExamesOcupacionais($codigo_cliente_funcionario);
		$this->set(compact('exames_ocupacionais'));

		$planos_saude = $this->Funcionario->listaPlanosDeSaude($codigo_cliente_funcionario);
		$this->set(compact('planos_saude'));

		$atestados = $this->Funcionario->listaAtestados($codigo_cliente_funcionario);
		$this->set(compact('atestados'));

		$qnt_quest_preenchidos = $this->Funcionario->qntQuestPreenchidos($codigo_cliente_funcionario);
		$this->set(compact('qnt_quest_preenchidos'));

		unset($conditions['having']);

		$sintetico = $this->RelatoriosCockpit->relatorio_sintetico_qtd_funcionarios($conditions);
		$qtd_colaboradores = $sintetico[0][0]['quantidade'];
		unset($sintetico);

		$sintetico = $this->RelatoriosCockpit->relatorio_sintetico_questionarios_preenchidos($conditions);
		$qtd_questionarios_respondidos = $sintetico[0][0]['quantidade'];
		unset($sintetico);

		$codigo_usuario = $this->Funcionario->obtem_codigo_usuario($codigo_cliente_funcionario);
		$questionarios = $this->RelatoriosCockpit->relatorio_prenchido_por_usuario($codigo_usuario);		

		$percentual = 0;
		if($qtd_colaboradores > 0) {
			$percentual = round($qtd_questionarios_respondidos / ($qtd_colaboradores * count($questionarios)) * 100, 0);
		}
		$this->loadModel('UsuarioContatoEmergencia');
		$contato_emergencia = $this->UsuarioContatoEmergencia->get($codigo_usuario);

		$this->set(compact('percentual', 'questionarios', 'contato_emergencia'));
		
	}

	public function dashboard($pagina = null)
	{
		$this->pageTitle = 'Dashboard';
		
		$gadgets = array();

		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$this->data['DashboardRelatorio']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		if($this->RequestHandler->isPost()) {
			if(!empty($this->data['DashboardRelatorio']['codigo_cliente'])){
				//foi necessario trocar o nome do formulario na ctp para DashboardRelatorio, e as conditions estao com o DadosSaudeConsulta
				$this->data[$this->DadosSaudeConsulta->name] = $this->data['DashboardRelatorio'];

				//para pegar o codigo de cliente do usuario que esta logado
				if(empty($this->data[$this->DadosSaudeConsulta->name]['codigo_cliente'])) {
					$usuario = $this->BAuth->user();
					if(!empty($usuario['codigo_cliente'])) {
						$this->data[$this->DadosSaudeConsulta->name]['codigo_cliente'] = $usuario['codigo_cliente'];
					}
				}

				$gadgets['dados_gerais'] = $this->gadgets_dados_gerais;
				$gadgets['perfil_saude'] = $this->gadgets_perfil_saude;
				$gadgets['colaboradores_atestados'] = $this->colaboradores_atestados;

				$conditions = $this->RelatoriosCockpit->converteFiltroEmCondition($this->data);

				// preenche o código_cliente com o codigo da matriz
				if(!empty($conditions['GrupoEconomico.codigo_cliente'])) {
					$this->data[$this->DadosSaudeConsulta->name]['codigo_cliente'] = $conditions['GrupoEconomico.codigo_cliente'];
				}

				//verifica se existe o tipo do sistema para colocar na conditions				
				$tipo_sistema = null;
				if(isset($this->data['DashboardRelatorio']['tipo_sistemas'])) {
					$tipo_sistema = $this->data['DashboardRelatorio']['tipo_sistemas'];
				}

				$this->Session->write('filtro', $conditions);
				$this->Session->write('tipo_sistema', $tipo_sistema);
			}
		}
		$this->carrega_combos('DashboardRelatorio');

		$tipos_sistemas = array('1' => 'LYN', '2' => 'FICHA CLINICA');

		$this->set(compact('gadgets', 'pagina','tipos_sistemas'));

	}

	public function colaboradores()
	{
		$this->layout = 'ajax';
		$this->pageTitle = 'Colaboradores';

		$conditions = array();
		if($this->Session->check('filtro')) {
			$conditions = $this->Session->read('filtro');
		}

		$conditions[] = 'ClienteFuncionario.ativo <> 0';
		$sintetico = $this->RelatoriosCockpit->relatorio_sintetico_qtd_funcionarios($conditions);

		$this->set('qtd_colaboradores', $sintetico[0][0]['quantidade']);
	}

	public function genero()
	{
		$this->layout = 'ajax';
		$this->pageTitle = 'Gênero';

		$conditions = array();
		if($this->Session->check('filtro')) {
			$conditions = $this->Session->read('filtro');
		}

		$conditions[] = 'ClienteFuncionario.ativo <> 0';
		$sintetico = $this->RelatoriosCockpit->relatorio_sintetico_qtd_funcionario_por_genero($conditions);

		$this->set('dados', $sintetico[0][0]);
	}

	public function vencimento_exames()
	{
		$this->layout = 'ajax';
		$this->pageTitle = 'Vencimento de exames';

		$conditions = array();
		if($this->Session->check('filtro')) {
			$conditions = $this->Session->read('filtro');
		}

		$sintetico = $this->RelatoriosCockpit->relatorio_sintetico_vcto_exames($conditions);

		$this->set('dados', $sintetico[0][0]);
		$this->set('conditions', $conditions);
	}

	public function faixa_etaria()
	{
		$this->layout = 'ajax';
		$this->pageTitle = 'Faixa etária';

		$conditions = array();
		if($this->Session->check('filtro')) {
			$conditions = $this->Session->read('filtro');
		}

		$conditions[] = 'ClienteFuncionario.ativo <> 0';
		$sintetico = $this->RelatoriosCockpit->relatorio_sintetico_faixa_etaria($conditions);

		$this->set('dados', $sintetico[0][0]);
	}

	public function questionarios()
	{
		$this->layout = 'ajax';
		$this->pageTitle = 'Questionários';

		$conditions = array();
		if($this->Session->check('filtro')) {
			$conditions = $this->Session->read('filtro');
		}

		//para saber se irá trazer os dados do LYN ou da ficha clinica
		$tipo_sistema = 1;
		if($this->Session->check('tipo_sistema')) {
			$tipo_sistema = $this->Session->read('tipo_sistema');
		}

		//variavel auxiliar
		$dados_gerais = null;

		//verifica qual o tipo sistema do filtro
		if($tipo_sistema == 2) { //ficha clinica

			//pega os dados da ficha clinica 
			$dados_gerais = $this->RelatoriosCockpit->getDadosGeraisFichaClinica($conditions);			
		}
		else { //lyn
			//pega os dados dao lyn
			$dados_gerais = $this->RelatoriosCockpit->getDadosGeraisNina($conditions);
		}

		// debug($dados_gerais);exit;
		
		//barra de percentual
		$percentual = 0;
		$total_respondido = 0;
		$total_funcionario = 0;
		$questionarios_ativos = array();

		if(!empty($dados_gerais)) {

			//varre os dados achados na base de dados
			foreach($dados_gerais as $key => $dados) {

				//verifica se existe funcionarios
				if($dados[0]['funcionario_total'] > 0) {
					//completo
					$percents['percentual_concluido'] = round($dados[0]['total_respondido'] / $dados[0]['funcionario_total'] * 100,1);
					//incompleto
					$percents['percentual_em_andamento'] = round($dados[0]['total_incompleto'] / $dados[0]['funcionario_total'] * 100,1);
					//nao respondido
					$percents['percentual_nao_respondeu'] = round($dados[0]['total_nao_respondido'] / $dados[0]['funcionario_total'] * 100,1);

					//totalizadores
					$total_respondido_var  = (isset($dados[0]['total_respondido'])) ? $dados[0]['total_respondido'] : 0;
					$total_funcionario 	= (isset($dados[0]['funcionario_total'])) ? $dados[0]['funcionario_total'] : 0;
					$total_respondido 	+= $total_respondido_var;
					$total_questionario = (isset($dados[0]['total_questionario'])) ? $dados[0]['total_questionario'] : 0;

				}//fim total funcionario
				else {
					//completo
					$percents['percentual_concluido'] = 0;
					//incompleto
					$percents['percentual_em_andamento'] = 0;
					//nao respondido
					$percents['percentual_nao_respondeu'] = 0;
				}

				//codigo do questionario 
				$codigo_questionario = $dados[0]['codigo_questionario'];

				// debug($codigo_questionario);

				//pega os dados dos questionarios		
				$questionario = $this->RelatoriosCockpit->query($this->RelatoriosCockpit->quantidade_questionarios_ativos($codigo_questionario));
				$questionarios_ativos[$key][0] = array_merge($questionario[0][0],$percents);
				
			}//fim foreach
		}
		else {
			//pega os questionarios
			$questionario = $this->RelatoriosCockpit->query($this->RelatoriosCockpit->quantidade_questionarios_ativos());
			
			//varre os questionarios
			if(!empty($questionario)) {
				//varre os questionarios
				foreach ($questionario as $key => $quest) {
					// debug($quest);exit;
					//completo
					$percents['percentual_concluido'] = 0;
					//incompleto
					$percents['percentual_em_andamento'] = 0;
					//nao respondido
					$percents['percentual_nao_respondeu'] = 0;

					$questionarios_ativos[$key][0] = array_merge($quest[0],$percents);
				}
			}//fim if questionarios
		}//fim else

		//verifica se o percentual esta igual a 0
		if($total_funcionario > 0 && $total_respondido > 0 && $total_questionario > 0) {
			$percentual = round($total_respondido / ($total_funcionario * $total_questionario) * 100,1);
		}//fim percentual 0		

		// debug($questionarios_ativos);

		$this->set(compact('percentual', 'questionarios_ativos'));

	}//fim metodo questionarios

	public function estatistica_saude()
	{
		$this->layout = 'ajax';
		$this->pageTitle = 'Estatísticas do perfil de saúde da população';

		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$this->data['DashboardRelatorio']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$conditions = array();
		if($this->Session->check('filtro')) {
			$conditions = $this->Session->read('filtro');
		}

		//para saber se irá trazer os dados do lyn ou da ficha clinica
		$tipo_sistema = 1;
		if($this->Session->check('tipo_sistema')) {
			$tipo_sistema = $this->Session->read('tipo_sistema');
		}

		//variavel auxiliar
		$questionarios_dados = null;
		$imc_dados = null;
		$fumante_dados = null;

		//verifica qual o tipo sistema do filtro
		if($tipo_sistema == 2) { //ficha clinica

			//pega os dados da ficha clinica 
			$questionarios_dados = $this->RelatoriosCockpit->getEstSaudeQuestFicha($conditions);
			$imc_dados = $this->RelatoriosCockpit->getEstSaudeImcFicha($conditions);
			$fumante_dados = $this->RelatoriosCockpit->getEstSaudeFumanteFicha($conditions);
		}
		else { //lyn
			//pega os dados dao lyn
			$questionarios_dados = $this->RelatoriosCockpit->getEstSaudeQuestNina($conditions);
			$imc_dados = $this->RelatoriosCockpit->getEstSaudeImcNina($conditions);
			$fumante_dados = $this->RelatoriosCockpit->getEstSaudeFumanteNina($conditions);
		}


		// debug($questionarios_dados);
		// exit;
		

		$questionarios = null;
		$codigo_questionario = null;
		$qtd_questionario = array();
		$total = 0;

		//verifica se esta vazio
		if(!empty($questionarios_dados)) {

			// debug($questionarios_dados);

			//varre os questionarios para acrescentar a imagem
			foreach($questionarios_dados as $key => $qd) {

				if($qd[0]['codigo_questionario'] != $codigo_questionario) {

					$total = 0;					
					$chave = 0;
					
					//seta o codigo do questionario
					$codigo_questionario = $qd[0]['codigo_questionario'];

					//remontando o array
					$questionarios[$qd[0]['codigo_questionario']][0]['codigo_questionario'] = $qd[0]['codigo_questionario'];
					$questionarios[$qd[0]['codigo_questionario']][0]['descricao'] = $qd[0]['questionario_descricao'];
					
					$questionarios[$qd[0]['codigo_questionario']][0]['quantidade_questionarios'] = $total;

					//resultados pega os valores padroes por questionarios
					$resultados = $this->RelatoriosCockpit->relatorio_resultados_por_questionario($codigo_questionario);
					$questionarios[$qd[0]['codigo_questionario']]['TodosResultados'] = $resultados;

				}//fim comparacao questionarios
				
				//seta o total de quantidade
				$total += $qd[0]['quantidade_questionarios'];

				//pega o maior numero dentro do questionario
				if(!isset($qtd_questionario[$qd[0]['codigo_questionario']])) {
					$qtd_questionario[$qd[0]['codigo_questionario']] = $qd[0]['quantidade_questionarios'];
				}
				else if($qtd_questionario[$qd[0]['codigo_questionario']] < $qd[0]['quantidade_questionarios']) {
					$qtd_questionario[$qd[0]['codigo_questionario']] = $qd[0]['quantidade_questionarios'];
				}

				
				//verifica qual é o valor para setar a imagem
				if($qtd_questionario[$qd[0]['codigo_questionario']] == $qd[0]['quantidade_questionarios']) {

					//para acrescentar a imagem
					switch ($qd[0]['resultado']) {
						case 'BAIXO RISCO':
							$questionarios[$qd[0]['codigo_questionario']][0]['imagem'] = 'ponteiro-baixo.png';
							break;

						case 'RISCO MODERADO':
							$questionarios[$qd[0]['codigo_questionario']][0]['imagem'] = 'ponteiro-meio.png';
							break;

						case 'ALTO RISCO':
							$questionarios[$qd[0]['codigo_questionario']][0]['imagem'] = 'ponteiro-alto.png';
							break;

						default:
							$questionarios[$qd[0]['codigo_questionario']][0]['imagem'] = 'ponteiro-meio.png';
							break;
					}//fim switch

					//renomeia os dados para maior risco
					$questionarios[$qd[0]['codigo_questionario']][0]['maior_risco'] = $qd[0]['resultado'];

				}//fim if


				//seta os resultados
				$questionarios[$qd[0]['codigo_questionario']]['Resultado'][$chave]['resultado'] = $qd[0]['resultado'];
				$questionarios[$qd[0]['codigo_questionario']]['Resultado'][$chave]['pontos'] = $qd[0]['pontos'];
				$questionarios[$qd[0]['codigo_questionario']]['Resultado'][$chave]['quantidade_questionarios'] = $qd[0]['quantidade_questionarios'];

				$questionarios[$qd[0]['codigo_questionario']][0]['quantidade_total'] = $total;

				$chave++;

			}//fim foreach

		}//fim empty questionarios_dados
		

		//seta as variaveis
		$imc = array();
		$imc['percentual_qtd_abaixo_do_peso'] = 0;
		$imc['percentual_qtd_acima_do_peso'] = 0;
		$imc['percentual_qtd_normal'] = 0;
		$imc['percentual_qtd_sobrepeso'] = 0;
		$imc['total'] = 0;

		if(!empty($imc_dados)) {
			//varre os dados do imc
			foreach($imc_dados as $dimc) {
				//separa em indices para ler na tela
				if($dimc[0]['imc_resultado'] == 'ABAIXO DO PESO') {
					$imc['percentual_qtd_abaixo_do_peso'] = ROUND($dimc[0]['total_imc_resultado'] / $dimc[0]['total'] * 100, 0);
				}

				//separa em indices para ler na tela
				if($dimc[0]['imc_resultado'] == 'ACIMA DO PESO') {
					$imc['percentual_qtd_acima_do_peso'] = ROUND($dimc[0]['total_imc_resultado'] / $dimc[0]['total'] * 100, 0);
				}

				//separa em indices para ler na tela
				if($dimc[0]['imc_resultado'] == 'NORMAL') {
					$imc['percentual_qtd_normal'] = ROUND($dimc[0]['total_imc_resultado'] / $dimc[0]['total'] * 100, 0);
				}

				//separa em indices para ler na tela
				if($dimc[0]['imc_resultado'] == 'SOBREPESO') {
					$imc['percentual_qtd_sobrepeso'] = ROUND($dimc[0]['total_imc_resultado'] / $dimc[0]['total'] * 100, 0);
				}

				$imc['total'] = $dimc[0]['total'];

			}
		}
		//fumantes
		$dependencias = $this->_ordenaDependencias($fumante_dados);

		$classes = array(
			array(
				'background-color-green',
				'background-leafgreen'
				),
			array(
				'background-color-goldenrod',
				'background-color-gold'
				),
			array(
				'background-color-red',
				'background-color-indianred'
				),
			);
		$classes = $classes + $classes;

		$this->set(compact('questionarios', 'classes', 'imc','dependencias'));
	}

	public function analitico_vencimento_exames()
	{
		$this->pageTitle = 'Vencimento de exames';

		$conditions = array();
		if($this->Session->check('filtro')) {
			$conditions = $this->Session->read('filtro');
		}

		$exames = $this->RelatoriosCockpit->relatorio_sintetico_exames_por_vencimento($conditions);

		$this->set(compact('exames'));

	}

	/**
	 * [analitico_situacao_questionarios description]
	 * 
	 * dados de detalhes dos funcionarios que responderam, incompletos e não responderam
	 * 
	 * @return [type] [description]
	 */
	public function analitico_situacao_questionarios()
	{
		$this->pageTitle = 'Situação dos questionários';

		$conditions = array();
		if($this->Session->check('filtro')) {
			$conditions = $this->Session->read('filtro');
		}

		//para saber se irá trazer os dados do lyn ou da ficha clinica
		$tipo_sistema = 1;
		if($this->Session->check('tipo_sistema')) {
			$tipo_sistema = $this->Session->read('tipo_sistema');
		}

		//variavel auxiliar
		$nao_respondidos = array();
		$respondidos = array();
		$incompletos = array();
		
		//verifica qual o tipo sistema do filtro
		if($tipo_sistema == 2) { //ficha clinica

			//pega os dados da ficha clinica 
			$respondidos = $this->RelatoriosCockpit->relatorio_analitico_questionario_ficha($conditions,1);
			$incompletos = $this->RelatoriosCockpit->relatorio_analitico_questionario_ficha($conditions,2);
			$nao_respondidos = $this->RelatoriosCockpit->relatorio_analitico_questionario_ficha($conditions,3);
		}
		else { //lyn
			//pega os dados dao lyn
			$respondidos = $this->RelatoriosCockpit->relatorio_analitico_questionario_nina($conditions,1);
			$incompletos = $this->RelatoriosCockpit->relatorio_analitico_questionario_nina($conditions,2);
			$nao_respondidos = $this->RelatoriosCockpit->relatorio_analitico_questionario_nina($conditions,3);
		}

		$this->set(compact('nao_respondidos', 'respondidos', 'incompletos'));

	}//fim analitico_situacao_questionario

	public function analitico_resultado($codigo_questionario = null)
	{
		$this->pageTitle = 'Resultado';

		$conditions = array();
		if($this->Session->check('filtro')) {
			$conditions = $this->Session->read('filtro');
		}

		if(!is_null($codigo_questionario)) {
			$conditions['Questionario.codigo'] = $codigo_questionario;
		}

		//para saber se irá trazer os dados do lyn ou da ficha clinica
		$tipo_sistema = 1;
		if($this->Session->check('tipo_sistema')) {
			$tipo_sistema = $this->Session->read('tipo_sistema');
		}

		//variavel auxiliar
		$questionarios_dados = null;

		//verifica qual o tipo sistema do filtro
		if($tipo_sistema == 2) { //ficha clinica

			//pega os dados da ficha clinica 
			$questionarios_dados = $this->RelatoriosCockpit->getEstSaudeQuestFicha($conditions);	
			$caracteristicas_populacao = $this->RelatoriosCockpit->analitico_resultado_ficha($conditions);
		}
		else { //lyn
			//pega os dados dao lyn
			$questionarios_dados = $this->RelatoriosCockpit->getEstSaudeQuestNina($conditions);
			$caracteristicas_populacao = $this->RelatoriosCockpit->analitico_resultado_nina($conditions);

		}

		// $questionarios = $this->RelatoriosCockpit->relatorio_sintetico_estatistica_saude($conditions);

		$questionarios = null;		
		$qtd_questionario = array();

		//varre os questionarios para acrescentar a imagem
		foreach($questionarios_dados as $key => $qd) {
			
			$chave = 0;
			//seta o codigo do questionario
			// $codigo_questionario = $qd[0]['codigo_questionario'];

			//remontando o array
			$questionarios[$qd[0]['codigo_questionario']][0]['codigo_questionario'] = $qd[0]['codigo_questionario'];
			$questionarios[$qd[0]['codigo_questionario']][0]['descricao'] = $qd[0]['questionario_descricao'];
			$questionarios[$qd[0]['codigo_questionario']][0]['quantidade_total'] = $qd[0]['quantidade_total'];

			//resultados pega os valores padroes por questionarios
			$resultados = $this->RelatoriosCockpit->relatorio_resultados_por_questionario($codigo_questionario);
			$questionarios[$qd[0]['codigo_questionario']]['TodosResultados'] = $resultados;

			

			//pega o maior numero dentro do questionario
			if(!isset($qtd_questionario[$qd[0]['codigo_questionario']])) {
				$qtd_questionario[$qd[0]['codigo_questionario']] = $qd[0]['quantidade_questionarios'];
			}
			else if($qtd_questionario[$qd[0]['codigo_questionario']] < $qd[0]['quantidade_questionarios']) {
				$qtd_questionario[$qd[0]['codigo_questionario']] = $qd[0]['quantidade_questionarios'];
			}

			
			//verifica qual é o valor para setar a imagem
			if($qtd_questionario[$qd[0]['codigo_questionario']] == $qd[0]['quantidade_questionarios']) {

				//para acrescentar a imagem
				switch ($qd[0]['resultado']) {
					case 'BAIXO RISCO':
						$questionarios[$qd[0]['codigo_questionario']][0]['imagem'] = 'ponteiro-baixo.png';
						break;

					case 'RISCO MODERADO':
						$questionarios[$qd[0]['codigo_questionario']][0]['imagem'] = 'ponteiro-meio.png';
						break;

					case 'ALTO RISCO':
						$questionarios[$qd[0]['codigo_questionario']][0]['imagem'] = 'ponteiro-alto.png';
						break;

					default:
						$questionarios[$qd[0]['codigo_questionario']][0]['imagem'] = 'ponteiro-meio.png';
						break;
				}//fim switch

				//renomeia os dados para maior risco
				$questionarios[$qd[0]['codigo_questionario']][0]['maior_risco'] = $qd[0]['resultado'];

			}//fim if


			//seta os resultados
			$questionarios[$qd[0]['codigo_questionario']]['Resultado'][$chave]['resultado'] = $qd[0]['resultado'];
			$questionarios[$qd[0]['codigo_questionario']]['Resultado'][$chave]['pontos'] = $qd[0]['pontos'];
			$questionarios[$qd[0]['codigo_questionario']]['Resultado'][$chave]['quantidade_questionarios'] = $qd[0]['quantidade_questionarios'];

			$chave++;

		}//fim foreach

		// debug($questionarios);exit;

		$soma = 0;
		$quant = 0;
		$resultado = array();
		foreach ($questionarios[$codigo_questionario]['Resultado'] as $key => $value) {
			$soma += $value['quantidade_questionarios'];
			$quant++;
		}
		if($soma == 0) $soma = 1;

		foreach ($questionarios[$codigo_questionario]['Resultado'] as $key => $value) {
			$resultado[$value['quantidade_questionarios']] = array(
				'resultado' => strtoupper($value['resultado']),
				'quantidade' => $value['quantidade_questionarios'],
				'percentual' => round(($value['quantidade_questionarios'] / $soma * 100), 1)
				);
		}
		if(!empty($resultado)) {	
			ksort($resultado);
			$resultado = array_pop($resultado);
		}

		$this->set(compact('resultado'));

		// $caracteristicas_populacao = $this->RelatoriosCockpit->relatorio_sintetico_resultados_por_massa($conditions);
		// debug($caracteristicas_populacao);exit;
		
		//varre os dados de caracteristicas
		foreach($caracteristicas_populacao as $key => $val){

			//calcula o percentual
			$percentual = round((($val[0]['quantidade'] * 100) / $val[0]['quantidade_usuarios']) ,1);

			//seta os riscos
			$risco = null;
			if($percentual < 20) {
				$risco = 'baixo_risco';
			}
			else if($percentual > 20 && $percentual <= 59) {
				$risco = 'medio_risco';
			}
			else if($percentual > 59) {
				$risco = 'alto_risco';
			}

			//seta os campos que estão faltando da caracteristica
			$caracteristicas_populacao[$key][0]['percentual'] = $percentual;
			$caracteristicas_populacao[$key][0]['risco'] = $risco;
		}//fim foreach
		$this->set(compact('caracteristicas_populacao'));

	}

	public function colaboradores_atestados() {
		$this->pageTitle = 'Dados dos colaboradores e atestados médicos';
		$this->set('title', $this->pageTitle);

		$conditions = array();
		if($this->Session->check('filtro')) {
			$conditions = $this->Session->read('filtro');
		}

		$this->loadModel('Atestado');


		$conditions['OR'][]['ClienteFuncionario.data_demissao'] = NULL;
		$conditions['OR'][]['ClienteFuncionario.data_demissao'] = '';


		$joins = array(
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => array(
					'ClienteFuncionario.codigo = Atestado.codigo_cliente_funcionario'
					)
				),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array(
					'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
					)
				),
            array(
                'table' => 'funcionario_setores_cargos',
                'alias' => 'FuncionarioSetorCargo',
                'type' => 'INNER',
                'conditions' => array (
                    "FuncionarioSetorCargo.codigo = Atestado.codigo_func_setor_cargo"
                    )
                ),
			array(
				'table' => 'grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomicoCliente.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao'
					)
				),
			array(
				'table' => 'grupos_economicos',
				'alias' => 'GrupoEconomico',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
					)
				),
			array(
				'table' => 'cliente',
				'alias' => 'Empresa',
				'type' => 'INNER',
				'conditions' => array(
					'Empresa.codigo = GrupoEconomico.codigo_cliente'
					)
				),
			array(
				'table' => 'cliente',
				'alias' => 'Unidade',
				'type' => 'INNER',
				'conditions' => array(
					'Unidade.codigo = GrupoEconomicoCliente.codigo_cliente'
					)
				)
			);
		$fields = array(
			'Funcionario.nome AS nome_funcionario',
			'Empresa.nome_fantasia AS nome_empresa',
			'Unidade.nome_fantasia AS nome_unidade',
			'ClienteFuncionario.codigo as codigo_cliente_funcionario',
			'Atestado.codigo_func_setor_cargo',
			'(SELECT descricao from setores WHERE codigo = 
			FuncionarioSetorCargo.codigo_setor) AS setor',
			'(SELECT descricao from cargos WHERE codigo = 
			FuncionarioSetorCargo.codigo_cargo) AS cargo',
			'COUNT(Atestado.codigo) AS qnt_atestados',
			'CASE
			WHEN COUNT(Atestado.codigo) < 2 THEN "darkgreen"
			WHEN COUNT(Atestado.codigo) < 9 THEN "gold"	
			WHEN COUNT(Atestado.codigo) >= 9 THEN "red"	
			END AS color',
			'SUM(CAST(REPLACE(afastamento_em_horas,":",".") AS FLOAT)) AS horas_afastamento'
			);
		$group = 'Funcionario.nome, Empresa.nome_fantasia, Unidade.nome_fantasia, ClienteFuncionario.codigo,Atestado.codigo_func_setor_cargo, FuncionarioSetorCargo.codigo_setor, FuncionarioSetorCargo.codigo_cargo ';
		if(!empty($conditions['having']['horas_afastamento'])) {
			$group .= ' HAVING SUM(CAST(REPLACE(afastamento_em_horas,":",".") AS FLOAT)) >= '.$conditions['having']['horas_afastamento'];
			$start = true;
		}
		if(!empty($conditions['having']['qnt_atestados'])) {
			if(isset($start)) { $group .= ' AND '; } else { $group .= 'HAVING '; }
			$group .= ' COUNT(Atestado.codigo) >= '.$conditions['having']['qnt_atestados'];
		}
		unset($conditions['having']);
		$order = array(
			'Funcionario.nome'
			);

		$this->paginate['Atestado'] = array(
			'recursive' => -1,
			'joins' => $joins,
			'conditions' => $conditions,
			'fields' => $fields,
			'group' => $group,
			'order' => $order,
			'limit' => 50
			);
		$atestados = $this->paginate('Atestado');
		$this->set(compact('atestados'));

	}

	public function relatorio_fatores_risco() {
		$this->pageTitle = 'Fatores de Risco';
		
		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$this->data['DashboardRelatorio']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}


		$this->data['DashboardRelatorio'] = $this->Filtros->controla_sessao($this->data, "DashboardRelatorio");

		
		$array_monta_resposta = array();
		$array_nome_formularios = array();
		$array_resultado = array();
		$questionario_aux_ja_foi = array();
		
		$conditions = array();
		$series =  array();
		if(!empty($this->data['DashboardRelatorio']['codigo_cliente'])) {

			$conditions['GrupoEconomico.codigo_cliente'] = $this->data['DashboardRelatorio']['codigo_cliente'];

			//verifica se existe o tipo do sistema para colocar na conditions				
			$tipo_sistema = null;
			if(isset($this->data['DashboardRelatorio']['tipo_sistemas'])) {
				$tipo_sistema = $this->data['DashboardRelatorio']['tipo_sistemas'];
			}

			//variavel auxiliar
			$questionarios_dados = null;
			
			//verifica qual o tipo sistema do filtro
			if($tipo_sistema == 2) { //ficha clinica
				//pega os dados da ficha clinica 
				$questionarios_dados = $this->RelatoriosCockpit->getEstSaudeQuestFicha($conditions);				
			}
			else { //lyn
				$conditions['Questionario.status'] = 1;

				//pega os dados dao lyn
				$questionarios_dados = $this->RelatoriosCockpit->getEstSaudeQuestNina($conditions);				
			}
			//$this->log('$questionarios_dados'.print_r($questionarios_dados, true), 'debug');
			$questionarios = null;
			$codigo_questionario = null;
			$qtd_questionario = array();
			//varre os questionarios para acrescentar a imagem
			foreach($questionarios_dados as $key => $qd) {

				if($qd[0]['codigo_questionario'] != $codigo_questionario) {
					$chave = 0;
					//seta o codigo do questionario
					$codigo_questionario = $qd[0]['codigo_questionario'];

					//remontando o array
					$questionarios[$qd[0]['codigo_questionario']][0]['codigo_questionario'] = $qd[0]['codigo_questionario'];
					$questionarios[$qd[0]['codigo_questionario']][0]['descricao'] = $qd[0]['questionario_descricao'];
					$questionarios[$qd[0]['codigo_questionario']][0]['quantidade_total'] = $qd[0]['quantidade_total'];

					//resultados pega os valores padroes por questionarios
					$resultados = $this->RelatoriosCockpit->relatorio_resultados_por_questionario($codigo_questionario);
					$questionarios[$qd[0]['codigo_questionario']]['TodosResultados'] = $resultados;

				}//fim comparacao questionarios

				//pega o maior numero dentro do questionario
				if(!isset($qtd_questionario[$qd[0]['codigo_questionario']])) {
					$qtd_questionario[$qd[0]['codigo_questionario']] = $qd[0]['quantidade_questionarios'];
				}
				else if($qtd_questionario[$qd[0]['codigo_questionario']] < $qd[0]['quantidade_questionarios']) {
					$qtd_questionario[$qd[0]['codigo_questionario']] = $qd[0]['quantidade_questionarios'];
				}

				
				//verifica qual é o valor para setar a imagem
				if($qtd_questionario[$qd[0]['codigo_questionario']] == $qd[0]['quantidade_questionarios']) {

					//para acrescentar a imagem
					switch ($qd[0]['resultado']) {
						case 'BAIXO RISCO':
							$questionarios[$qd[0]['codigo_questionario']][0]['imagem'] = 'ponteiro-baixo.png';
							break;

						case 'RISCO MODERADO':
							$questionarios[$qd[0]['codigo_questionario']][0]['imagem'] = 'ponteiro-meio.png';
							break;

						case 'ALTO RISCO':
							$questionarios[$qd[0]['codigo_questionario']][0]['imagem'] = 'ponteiro-alto.png';
							break;

						default:
							$questionarios[$qd[0]['codigo_questionario']][0]['imagem'] = 'ponteiro-meio.png';
							break;
					}//fim switch

					//renomeia os dados para maior risco
					$questionarios[$qd[0]['codigo_questionario']][0]['maior_risco'] = $qd[0]['resultado'];

				}//fim if


				//seta os resultados
				$questionarios[$qd[0]['codigo_questionario']]['Resultado'][$chave]['resultado'] = $qd[0]['resultado'];
				$questionarios[$qd[0]['codigo_questionario']]['Resultado'][$chave]['pontos'] = $qd[0]['pontos'];
				$questionarios[$qd[0]['codigo_questionario']]['Resultado'][$chave]['quantidade_questionarios'] = $qd[0]['quantidade_questionarios'];

				$chave++;

			}//fim foreach

			// debug($questionarios);
			// exit;

			// $conditions['GrupoEconomicoCliente.codigo_cliente'] = $this->data['DashboardRelatorio']['codigo_cliente'];
			// $conditions['Questionario.status'] = 1;
			// $questionarios = $this->RelatoriosCockpit->relatorio_sintetico_estatistica_saude($conditions);
			// debug($questionarios);exit;

			$series[0]['name'] = 'BAIXO RISCO';
			$series[1]['name'] = 'RISCO MODERADO';
			$series[2]['name'] = 'ALTO RISCO';
			foreach ($questionarios as $key => $questionario) {
				$array_nome_formularios[$questionario[0]['codigo_questionario']] = $questionario[0]['descricao'];
			}
			foreach ($questionarios as $key => $dados){
				foreach($dados['Resultado'] as $key => $risco){
					switch ($risco['resultado']) {
						case 'BAIXO RISCO':
							$series[0]['values'][$dados[0]['codigo_questionario']] = $risco['quantidade_questionarios'];
							break;
						case 'RISCO MODERADO':
							$series[1]['values'][$dados[0]['codigo_questionario']] = $risco['quantidade_questionarios'];
							break;
						case 'ALTO RISCO':
							$series[2]['values'][$dados[0]['codigo_questionario']] = $risco['quantidade_questionarios'];
							break;
					}
				}
				
				if(!isset($series[0]['values'][$dados[0]['codigo_questionario']])){
					$series[0]['values'][$dados[0]['codigo_questionario']] = 0;
				}
				if(!isset($series[1]['values'][$dados[0]['codigo_questionario']])){
					$series[1]['values'][$dados[0]['codigo_questionario']] = 0;
				}
				if(!isset($series[2]['values'][$dados[0]['codigo_questionario']])){
					$series[2]['values'][$dados[0]['codigo_questionario']] = 0;
				}
			}

		}

		$tipos_sistemas = array('1' => 'LYN', '2' => 'FICHA CLINICA');

		$this->set(compact('array_nome_formularios','series','tipos_sistemas'));
	}	
	
	public function relatorio_imc() {
		$this->pageTitle = 'Índice de Massa Corporal';

		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$this->data['DashboardRelatorio']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$this->data['DashboardRelatorio'] = $this->Filtros->controla_sessao($this->data, "DashboardRelatorio");

		$faixas_imc = array(
			'0' => 'Muito Abaixo do Peso',
			'1' => 'Abaixo do Peso',
			'2' => 'Peso Normal',
			'3' => 'Acima do Peso',
			'4' => 'Obesidade 1',
			'5' => 'Obesidade 2 (severa)',
			'6' => 'Obesidade 3 (mórbida)'
			);

		$conditions = array();
		$array_series = array();
		$array_resultado = array();
		if(!empty($this->data['DashboardRelatorio']['codigo_cliente'])) {
			
			//conditions
			$conditions['GrupoEconomico.codigo_cliente'] = $this->data['DashboardRelatorio']['codigo_cliente'];

			//verifica se existe o tipo do sistema para colocar na conditions				
			$tipo_sistema = 1;
			if(isset($this->data['DashboardRelatorio']['tipo_sistemas'])) {
				$tipo_sistema = $this->data['DashboardRelatorio']['tipo_sistemas'];
			}

			//variavel auxiliar			
			$imc_dados = null;			

			//verifica qual o tipo sistema do filtro
			if($tipo_sistema == 2) { //ficha clinica
				//pega os dados da ficha clinica 
				$imc_dados = $this->RelatoriosCockpit->getRelatorioImcFicha($conditions);
				
			}
			else { //lyn
				//pega os dados dao lyn				
				$imc_dados = $this->RelatoriosCockpit->getRelatorioImcNina($conditions);				
			}

			// debug($imc_dados);exit;

			$array_resultado = array('0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0);
			//varre os dados do imc
			foreach ($imc_dados as $key => $item) {

				//seta o dado do imc
				$imc = $item[0]['imc_resultado'];

				//classifica o imc
				if($imc < 17) {
					$array_resultado[0] = $array_resultado[0] + 1;
				} elseif(($imc > 17) && ($imc < 18.5)) {
					$array_resultado[1] = $array_resultado[1]  + 1;
				} elseif(($imc >= 18.5) && ($imc < 24.99)) {
					$array_resultado[2] = $array_resultado[2] + 1;
				} elseif(($imc >= 25) && ($imc < 29.99)) {
					$array_resultado[3] = $array_resultado[3] + 1;
				} elseif(($imc >= 30) && ($imc < 34.99)) {
					$array_resultado[4] = $array_resultado[4] + 1;
				} elseif(($imc >= 35) && ($imc < 39.99)) {
					$array_resultado[5] = $array_resultado[5] + 1;
				} elseif(($imc >= 40)) {
					$array_resultado[6] = $array_resultado[6] + 1;
				}
			}//fim foreach

			foreach($array_resultado as $k => $item) {
				if($item) {
					$array_series[$k]  = array('name' => "'" . $faixas_imc[$k] . "'", 'values' => $item);
				}
			}
		
			// debug($array_series);exit;
		}

		$tipos_sistemas = array('1' => 'LYN', '2' => 'FICHA CLINICA');
		$this->set(compact('tipos_sistemas'));

		$this->set('series', $array_series);
		$this->set('resultado', $array_resultado);
		$this->set('faixas_imc', $faixas_imc);

		
	}
	
	public function relatorio_genero() {
		$this->pageTitle = 'Percentual (Homens / Mulheres)';

		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$this->data['DashboardRelatorio']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

        $this->data['DashboardRelatorio'] = $this->Filtros->controla_sessao($this->data, "DashboardRelatorio");

		$cont = array('M' => 0, 'F' => 0);

		$conditions = array();
		$series = array();

		if(!empty($this->data['DashboardRelatorio']['codigo_cliente'])) {

			//conditions
			$conditions['GrupoEconomico.codigo_cliente'] = $this->data['DashboardRelatorio']['codigo_cliente'];

			//verifica se existe o tipo do sistema para colocar na conditions				
			$tipo_sistema = 1;
			if(isset($this->data['DashboardRelatorio']['tipo_sistemas'])) {
				$tipo_sistema = $this->data['DashboardRelatorio']['tipo_sistemas'];
			}

			//variavel auxiliar			
			$dados = null;			

			//verifica qual o tipo sistema do filtro
			if($tipo_sistema == 2) { //ficha clinica
				//pega os dados da ficha clinica 
				$dados = $this->RelatoriosCockpit->getRelatorioGeneroFicha($conditions);
			}
			else { //lyn
				//pega os dados dao lyn				
				$dados = $this->RelatoriosCockpit->getRelatorioGeneroNina($conditions);				
			}

			//monta para apresentar os dados
			foreach ($dados as $k => $item) {
				if($item[0]['sexo'] == 'M') {
					$cont['M'] = $item[0]['total_sexo'];
				} else {					
					$cont['F'] = $item[0]['total_sexo'];
				}
			}
			
			$series[] = array('name' => "'Masculino'", 'values' => $cont['M']);
			$series[] = array('name' => "'Feminino'", 'values' => $cont['F']);
		}
		
		$this->set('series', $series);

		$tipos_sistemas = array('1' => 'LYN', '2' => 'FICHA CLINICA');
		$this->set(compact('tipos_sistemas'));

	}
	
	public function relatorio_posicao_questionarios() {
		$this->pageTitle = 'Preenchimento Questionários';
		
		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$this->data['DashboardRelatorio']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

        $this->data['DashboardRelatorio'] = $this->Filtros->controla_sessao($this->data, "DashboardRelatorio");

		$conditions = array();
		$titulos = array();
		$series = array();
		if(!empty($this->data['DashboardRelatorio']['codigo_cliente'])) {

			//conditions
			$conditions['GrupoEconomico.codigo_cliente'] = $this->data['DashboardRelatorio']['codigo_cliente'];

			//verifica se existe o tipo do sistema para colocar na conditions				
			$tipo_sistema = 1;
			if(isset($this->data['DashboardRelatorio']['tipo_sistemas'])) {
				$tipo_sistema = $this->data['DashboardRelatorio']['tipo_sistemas'];
			}

			//variavel auxiliar
			$questionarios_dados = null;
			//verifica qual o tipo sistema do filtro
			if($tipo_sistema == 2) { //ficha clinica

				//pega os dados da ficha clinica 
				$questionarios_dados = $this->RelatoriosCockpit->getEstSaudeQuestFicha($conditions);			
			}
			else { //lyn
				//pega os dados dao lyn
				$questionarios_dados = $this->RelatoriosCockpit->getEstSaudeQuestNina($conditions);				
			}

			$questionarios = null;			
			$codigo_questionario = null;
			$array_conta_preenchimento = array();
			//varre os questionarios para acrescentar a imagem
			foreach($questionarios_dados as $key => $qd) {

				if($qd[0]['codigo_questionario'] != $codigo_questionario) {
					$chave = 0;
					//seta o codigo do questionario
					$codigo_questionario = $qd[0]['codigo_questionario'];

					//remontando o array
					$array_conta_preenchimento[$qd[0]['codigo_questionario']] = (isset($qd[0]['quantidade_total']) ? $qd[0]['quantidade_total'] : 0);
					$titulos[$qd[0]['codigo_questionario']] = $qd[0]['questionario_descricao'];
				}//fim comparacao questionarios

			}//fim foreach	
						
			$series['values'] = $array_conta_preenchimento;
		}
		
		$tipos_sistemas = array('1' => 'LYN', '2' => 'FICHA CLINICA');
		$this->set(compact('titulos', 'series','tipos_sistemas'));
	}
	
	public function relatorio_faixa_etaria() {
		$this->pageTitle = 'Faixa Etária';

		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$this->data['DashboardRelatorio']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$var_faixas_etarias = $this->_retorna_faixa_etaria();
		$conditions = array();
		$faixas_etarias = array();

		if(!empty($this->data['DashboardRelatorio']['codigo_cliente'])) {
					
			//verifica se existe o tipo do sistema para colocar na conditions				
			$tipo_sistema = null;
			if(isset($this->data['DashboardRelatorio']['tipo_sistemas'])) {
				$tipo_sistema = $this->data['DashboardRelatorio']['tipo_sistemas'];
			}

			if($tipo_sistema == 2) { //ficha clinica

				$conditions['ClienteFuncionario.codigo_cliente_matricula'] = $this->data['DashboardRelatorio']['codigo_cliente'];
				$conditions['Funcionario.data_nascimento != '] = NULL;
				$conditions['ClienteFuncionario.ativo <> '] = '0';

				$data_funcionarios = $this->RelatoriosCockpit->buscaFuncionariosFicha($conditions);

				foreach($data_funcionarios as $key => $info) {
					$idade = $this->_retorna_idade($info[0]['data_nascimento'],2);
					
					foreach($var_faixas_etarias as $k => $faixa) {
						if(($idade >= $faixa['min']) && ($idade <= $faixa['max'])) {
							$var_faixas_etarias[$k]['total'] = $var_faixas_etarias[$k]['total'] + 1;
						}
					}
				}
			}
			else { //lyn
				$conditions['GrupoEconomicoCliente.codigo_cliente'] = $this->data['DashboardRelatorio']['codigo_cliente'];
				$conditions['UsuariosDados.data_nascimento != '] = NULL;

				$data_funcionarios = $this->UsuariosDados->buscaFuncionarioPorEmpresa($conditions);

				foreach($data_funcionarios as$key => $info) {
					$idade = $this->_retorna_idade($info['UsuariosDados']['data_nascimento']);
					
					foreach($var_faixas_etarias as $k => $faixa) {
						if(($idade >= $faixa['min']) && ($idade <= $faixa['max'])) {
							$var_faixas_etarias[$k]['total'] = $var_faixas_etarias[$k]['total'] + 1;
						}
					}
				}
			}//fim verifica o tipo de filtro
			
			foreach($var_faixas_etarias as $k => $faixa) {
				$x[$k] = $faixa['min'] . " à " . $faixa['max'];
				$faixas_etarias['values'][$k] = $faixa['total'];
			}
		
		}//fim post filtros

		$tipos_sistemas = array('1' => 'LYN', '2' => 'FICHA CLINICA');

		$this->set(compact('faixas_etarias', 'x', 'tipos_sistemas'));
	}
	
	function _retorna_idade($dt_nascimento, $modelo=1) {

		// calcula idade
		if($dt_nascimento) {

			if($modelo == 1) {

				$data = explode("/", $dt_nascimento);

				$data_nascimento = new DateTime( $data[2] . "-" . $data[1] . "-" . $data[0] );
				$interval = $data_nascimento->diff( new DateTime( date('Y-m-d') ) );
				$idade = $interval->format( '%Y' );
			}
			else if ($modelo == 2) {
				$data = explode("-", $dt_nascimento);

				$data_nascimento = new DateTime( $data[0] . "-" . $data[1] . "-" . $data[2] );
				$interval = $data_nascimento->diff( new DateTime( date('Y-m-d') ) );
				$idade = $interval->format( '%Y' );
			}
		} else {
			$idade = '';
		}

		return $idade;
	}	
	
	function _retorna_faixa_etaria() {
		return  array(
			'0' => array('min' => '1', 'max' => '14', 'total' => 0),
			'1' => array('min' => '15', 'max' => '20', 'total' => 0),
			'2' => array('min' => '21', 'max' => '25', 'total' => 0),
			'3' => array('min' => '26', 'max' => '30', 'total' => 0),
			'4' => array('min' => '31', 'max' => '35', 'total' => 0),
			'5' => array('min' => '36', 'max' => '40', 'total' => 0),
			'6' => array('min' => '41', 'max' => '50', 'total' => 0),				
			'7' => array('min' => '51', 'max' => '60', 'total' => 0),
			'8' => array('min' => '61', 'max' => '70', 'total' => 0),
			'9' => array('min' => '71', 'max' => '80', 'total' => 0)			
			);
	}

	private function _ordenaDependencias($dados)
	{
		
		//seta a variavel como padrao
		$dependencias = array_fill_keys(array('total','SemDepen','BaixaDepen','MediaDepen','AltaDepen'), 0);
		
		//verifica se dados esta vazio
		if(!empty($dados)) {
			//verre os campos
			foreach($dados as $key => $dado) {
				//verifica se existe valor no campo
				if(!empty($dado[0])) {
					//pega o total
					$dependencias['total'] = $dado[0]['total'];
					//verifica se existe valor
					if(!empty($dado[0]['dependencia'])){
						//verifica em qual se enquadra
						switch ($dado[0]['dependencia']) {
							case '1':
								$dependencias['SemDepen'] = $dado[0]['total_nivel'];
								break;
							case '2':
								$dependencias['BaixaDepen'] = $dado[0]['total_nivel'];
								break;
							case '3':
								$dependencias['MediaDepen'] = $dado[0]['total_nivel'];
								break;
							case '4':
								$dependencias['AltaDepen'] = $dado[0]['total_nivel'];
								break;
						}//switch
					}//fim verificacao
				}//fim verificacao
			}//fim foreach
		}//fim empty dados

		
		$dependencias['SemDepen'] = ($dependencias['SemDepen'] == 0 ? 0 : round(($dependencias['SemDepen'] * 100) / $dependencias['total'], 2));
		$dependencias['BaixaDepen'] = ($dependencias['BaixaDepen'] == 0 ? 0 : round(($dependencias['BaixaDepen'] * 100) / $dependencias['total'], 2));
		$dependencias['MediaDepen'] = ($dependencias['MediaDepen'] == 0 ? 0 : round(($dependencias['MediaDepen'] * 100) / $dependencias['total'], 2));
		
		if(!empty($dependencias['total'])){
			$dependencias['AltaDepen'] = round(100 - ($dependencias['SemDepen'] + $dependencias['BaixaDepen'] + $dependencias['MediaDepen'] ),1);
		}

		return $dependencias;
	}

	public function carrega_combos($model) {
		$this->loadModel('Cargo');
		$this->loadModel('Setor');
		$this->loadModel('GrupoEconomico');
		$this->loadModel('GrupoEconomicoCliente');
		
		$codigo_cliente = $this->data[$model]['codigo_cliente'];

		if(!empty($codigo_cliente)) {
			if(!$this->GrupoEconomico->verificaMatriz($codigo_cliente)){
				$codigo_cliente = $this->GrupoEconomicoCliente->getCodigoGrupoEconomico($codigo_cliente);
			}
		}

		$unidades 	= $this->GrupoEconomicoCliente->lista($codigo_cliente);      
		$setores 	= $this->Setor->lista($codigo_cliente);     
		$cargos 	= $this->Cargo->lista($codigo_cliente);

		$this->set(compact('unidades', 'setores', 'cargos'));
	}

}
?>
