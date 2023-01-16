<?php
class UsuarioGrupoCovidController extends AppController {
	public $name = 'UsuarioGrupoCovid';
	public $helpers = array('BForm', 'Html', 'Ajax', 'Highcharts', 'Buonny');
	public $components = array('ExportCsv', 'Upload');

	var $uses = array(
		// 'OutroArquivoUsuario',
		'UsuarioExameImagem',
		'UsuarioExames',
		'AnexoAtestado',
		'UsuarioGrupoCovid',
		'UsuarioGrupoCovidLog',
		'Atestado',
		'GrupoCovid',
		'UsuarioGca',
		'UsuarioGcaAnexos',
		'ResultadoCovid'
    ); 

	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow();//TODO
	} 

	public function index() 
	{
		//titulo da page
		$this->pageTitle = 'Gestão Covid';
		
		//pega os filtros da sessão
        $filtros = $this->Filtros->controla_sessao($this->data, 'UsuarioGrupoCovid');

        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			if(empty($filtros['codigo_cliente'])) {
				$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
			}
        }

        $filtros['codigo_cliente'] = (isset($this->authUsuario['Usuario']['multicliente'])) ? $this->normalizaCodigoCliente($filtros['codigo_cliente']) : $filtros['codigo_cliente'];

		// alimenta os formularios
        $this->usuario_grupo_covid_filtros($filtros);
	}

	public function usuario_grupo_covid_filtros($thisData = null) 
	{
		// carrega dependencias		
        $this->loadModel('GrupoEconomicoCliente');
        $this->loadModel('Setor');
		$this->loadModel('Cargo');  
        $this->loadmodel('GrupoCovid');

        $unidades = array();
        $setores = array();
        $cargos = array();

		// converte com $this->normalizaCodigoCliente pois codigo_cliente pode estar vindo do form como string ou da sessão como array
        if(isset($thisData['codigo_cliente']) && !empty($thisData['codigo_cliente'])){
            $codigo_cliente = $this->normalizaCodigoCliente($thisData['codigo_cliente']);
            $thisData['codigo_cliente'] = $codigo_cliente;
            $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
            $setores = $this->Setor->lista($codigo_cliente);
            $cargos = $this->Cargo->lista($codigo_cliente);
        }

        $grupo_covid = $this->GrupoCovid->find('list',array('fields' => array('codigo','descricao')));

        $passaporte = array('1' => 'Verde', '2' => 'Vermelho');

		// configura no $this->data
        $this->data['UsuarioGrupoCovid'] = $thisData;
        
        $listagem = array();

        $this->set(compact('unidades','setores', 'cargos','listagem','grupo_covid','passaporte'));
    }
	
	public function editar($codigo_usuario = null, $codigo_cliente_funcionario = null, $codigo_funcionario_setor_cargo = null)
	{
		$this->pageTitle = 'Editar funcionário';

		$buscar_codigo = $this->UsuarioGrupoCovid->find('first', array('conditions' => array('codigo_usuario' => $codigo_usuario)));
		$codigoUsuarioGrupoCovid = $buscar_codigo['UsuarioGrupoCovid']['codigo'];

		if($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {


			//retira formatacao cpf
			$this->data['UsuarioGrupoCovid']['cpf'] = str_replace('.','',str_replace('-','',$this->data['UsuarioGrupoCovid']['cpf']));

			// debug($this->data);
			
			//insere os dados no atendimento
			$dadosAtendimento['UsuarioGca'] = $this->data['UsuarioGca'];
			$dadosAtendimento['UsuarioGca']['codigo_usuario'] = $this->data['UsuarioGrupoCovid']['codigo_usuario'];
			$dadosAtendimento['UsuarioGca']['codigo_usuario_grupo_covid'] = $this->data['UsuarioGrupoCovid']['codigo'];
			$dadosAtendimento['UsuarioGca']['cpf'] = $this->data['UsuarioGrupoCovid']['cpf'];
			$dadosAtendimento['UsuarioGca']['ativo'] = 1;

			//verifica se deve voltar para o grupo o usuario/funcionario
			if($dadosAtendimento['UsuarioGca']['volta_grupo'] == 'S') {
				$dadosAtendimento['UsuarioGca']['volta_grupo'] = 1;
			}
			else {
				$dadosAtendimento['UsuarioGca']['volta_grupo'] = 0;
			}

			//verifica se deve colocar o usuario no grupo laranja e deixar ele com passaporte vermelho
			if(isset($dadosAtendimento['UsuarioGca']['afastamento_sintomas'])) {
				if($dadosAtendimento['UsuarioGca']['afastamento_sintomas'] == 'S') {
					$dadosAtendimento['UsuarioGca']['afastamento_sintomas'] = 1;
					$dadosAtendimento['UsuarioGca']['controle_data_afastamento'] = 1;
				}
				else {
					$dadosAtendimento['UsuarioGca']['afastamento_sintomas'] = 0;
					$dadosAtendimento['UsuarioGca']['controle_data_afastamento'] = 0;
				}
			}
			else {
				$dadosAtendimento['UsuarioGca']['afastamento_sintomas'] = 0;
				$dadosAtendimento['UsuarioGca']['controle_data_afastamento'] = 0;
			}

			//verifica se deve colocar o usuario no grupo vermelho pois foi positivado com covid
			if(isset($dadosAtendimento['UsuarioGca']['afastamento_positivado'])) {
				if($dadosAtendimento['UsuarioGca']['afastamento_positivado'] == 'S') {
					$dadosAtendimento['UsuarioGca']['afastamento_positivado'] = 1;
				}
				else {
					$dadosAtendimento['UsuarioGca']['afastamento_positivado'] = 0;
				}
			}
			else {
				$dadosAtendimento['UsuarioGca']['afastamento_positivado'] = 0;
			}

			//verifica se deve solicitou exame de teste do covid
			if($dadosAtendimento['UsuarioGca']['solicita_exame'] == 'S') {
				$dadosAtendimento['UsuarioGca']['solicita_exame'] = 1;
			}
			else {
				$dadosAtendimento['UsuarioGca']['solicita_exame'] = 0;
			}

			// debug($dadosAtendimento);exit;

			//verifica se incluiu com sucesso
			if($this->UsuarioGca->incluir($dadosAtendimento)) {

				//pega o codigo usuario_gca --> usuario_grupo_covid_atendimento
				$codigo_usuario_gca = $this->UsuarioGca->id;
				$resultado_exame = $dadosAtendimento['UsuarioGca']['resultado_exame'];

				//verifica se foi negativo o resultado do exame de covid
				if(!empty($resultado_exame)) {
					//se for negativo volta o grupo que estava antes
					if($resultado_exame == 2) {//negativo
						$this->UsuarioGrupoCovid->voltaUsuarioGrupoCovid($dadosAtendimento['UsuarioGca']['codigo_usuario_grupo_covid']);
					}
					else { //resultado positivo muda o usuario para o grupo vermelho
						//coloca o usuario no grupo vermelho
						$this->set_troca_grupo($dadosAtendimento['UsuarioGca']['codigo_usuario_grupo_covid'],4);//grupo vermelho
					}
				}
				
				if($dadosAtendimento['UsuarioGca']['volta_grupo'] == 1) { //quando o usuario do sistema pedir para o funcionario voltar ao grupo que estava
					
					$codigo_grupo_covid = $this->UsuarioGrupoCovid->voltaUsuarioGrupoCovid($dadosAtendimento['UsuarioGca']['codigo_usuario_grupo_covid']);
					if($codigo_grupo_covid) {
						//gerando o passaporte para o usuario coloca ele com o passaporte verde
						$dados_resultado_passaporte = array(
							'ResultadoCovid' => array(
								'codigo_usuario' => $codigo_usuario,
								'codigo_grupo_covid' => $codigo_grupo_covid,
								'passaporte' => 1
							)
						);

						$this->ResultadoCovid->incluir($dados_resultado_passaporte);
					}

					//buscar se tem uma linha com o afastamento ativo coluna controle_data_afastamento na tabela usuario_gca com o codigo_usuario_grupo_covid 
					$controle_usuario_gca = $this->UsuarioGca->find('first', 
						array('conditions' => 
							array(
								'ativo' => 1,
								'codigo_usuario_grupo_covid' => $dadosAtendimento['UsuarioGca']['codigo_usuario_grupo_covid'], 
								'afastamento_sintomas' => 1, 
								'controle_data_afastamento' => 1
							)));

					//deve atualizar o campo controle_data_afastamento
					if(!empty($controle_usuario_gca)) {
						$controle_usuario_gca['UsuarioGca']['controle_data_afastamento'] = 0;
						$this->UsuarioGca->atualizar($controle_usuario_gca);
					}//fim $controle_usuario_gca
					

				}//fim verificacao se volta ao grupo
				else {
					//verifica se ira colocar o usuario como laranja e passaporte vermelho com uma data de afastamento
					if($dadosAtendimento['UsuarioGca']['afastamento_sintomas'] == 1) {

						//colcar o usuario como grupo laranja e passaporte vermelho 
						//e um programa na cron irá monitorar se deve devolver ele para o grupo que estava quando chegar na data estipuada
						$this->set_troca_grupo($dadosAtendimento['UsuarioGca']['codigo_usuario_grupo_covid'], 5); //grupo laranja

						//gerando o passaporte VERMELHO para o usuario coloca ele com o passaporte vermelho
						$this->set_passaporte_vermelho($codigo_usuario,5); //grupo laranja

					}//fim afastamento sintomas

					//verifica se ira colocar o usuario com o grupo vermelho e passaporte vermelho com uma data de afastamento
					if($dadosAtendimento['UsuarioGca']['afastamento_positivado'] == 1) {

						//coloca o usuario no grupo vermelho
						$this->set_troca_grupo($dadosAtendimento['UsuarioGca']['codigo_usuario_grupo_covid'], 4); //grupo vermelho

						//gerando o passaporte VERMELHO para o usuario coloca ele com o passaporte verde
						$this->set_passaporte_vermelho($codigo_usuario,4); //grupo vermelho


					}//fim afastamento_positivado

				}//fim else do volta ao grupo

				//verifica se tem codigo_usuario_gca para importar os anexos
				if(!empty($codigo_usuario_gca)) {
					$url_file_server = "https://api.rhhealth.com.br";

					//se vier dados do upload de resultado de exames
					if(!empty($this->data['UsuarioGcaAnexos']['exame']['name'])){

						$post_params = isset($this->data['UsuarioGcaAnexos']['exame']) && !empty($this->data['UsuarioGcaAnexos']['exame']) ? $this->data['UsuarioGcaAnexos']['exame'] : null ;

						if(empty($post_params)){
			                $this->BSession->setFlash('save_error');
							$this->redirect(array('action' => 'editar', $codigo_usuario, $codigo_cliente_funcionario, $codigo_funcionario_setor_cargo));						
			            }

			            $this->Upload->setOption('field_name', 'exame');            
			            $this->Upload->setOption('accept_extensions', array('pdf','jpg','jpeg', 'png'));
			            $this->Upload->setOption('accept_extensions_message', 'Arquivo inválido! Favor escolher arquivo Pdf, jpg, jpeg ou png');
			            $this->Upload->setOption('size_max', 5242880);
			            $this->Upload->setOption('size_max_message', 'Tamanho máximo excedido! Só é permitido arquivos de até 5MB');

			            $retorno = $this->Upload->fileServer($this->data['UsuarioGcaAnexos']);

			            if (isset($retorno['error']) && !empty($retorno['error']) ){
			                $chave = key($retorno['error']);
			                $this->BSession->setFlash(array(MSGT_ERROR, $retorno['error'][$chave]));
			            } else {

			            	$nome_arquivo = $this->data['UsuarioGcaAnexos']['exame']['name'];

			                unset($this->data['UsuarioGcaAnexos']['exame']);

			                $this->data['UsuarioGcaAnexos']['exame'] = $retorno['data'][$nome_arquivo]['path'];
			                
			                $dados_usuario_imagem['UsuarioGcaAnexos']['anexo'] = $url_file_server.$this->data['UsuarioGcaAnexos']['exame'];
			                $dados_usuario_imagem['UsuarioGcaAnexos']['codigo_usuario_gca'] = $codigo_usuario_gca;

			                $this->UsuarioGcaAnexos->incluir($dados_usuario_imagem);
			            }
					} 
					else {
						unset($this->data['UsuarioGcaAnexos']['exame']['name']);
						unset($this->data['UsuarioGcaAnexos']['exame']['type']);
						unset($this->data['UsuarioGcaAnexos']['exame']['error']);
						unset($this->data['UsuarioGcaAnexos']['exame']['size']);
						unset($this->data['UsuarioGcaAnexos']['exame']);
					}


					//se vier dados do upload de resultado de exames
					if(!empty($this->data['UsuarioGcaAnexos']['exame2']['name'])){

						$post_params2 = isset($this->data['UsuarioGcaAnexos']['exame2']) && !empty($this->data['UsuarioGcaAnexos']['exame2']) ? $this->data['UsuarioGcaAnexos']['exame2'] : null ;

						if(empty($post_params)){
			                $this->BSession->setFlash('save_error');
							$this->redirect(array('action' => 'editar', $codigo_usuario, $codigo_cliente_funcionario, $codigo_funcionario_setor_cargo));						
			            }

			            $this->Upload->setOption('field_name', 'exame2');            
			            $this->Upload->setOption('accept_extensions', array('pdf','jpg','jpeg', 'png'));
			            $this->Upload->setOption('accept_extensions_message', 'Arquivo inválido! Favor escolher arquivo Pdf, jpg, jpeg ou png');
			            $this->Upload->setOption('size_max', 5242880);
			            $this->Upload->setOption('size_max_message', 'Tamanho máximo excedido! Só é permitido arquivos de até 5MB');

			            $retorno = $this->Upload->fileServer($this->data['UsuarioGcaAnexos']);
			            if (isset($retorno['error']) && !empty($retorno['error']) ){
			                $chave = key($retorno['error']);
			                $this->BSession->setFlash(array(MSGT_ERROR, $retorno['error'][$chave]));
			            } else {

			            	$nome_arquivo = $this->data['UsuarioGcaAnexos']['exame2']['name'];

			                unset($this->data['UsuarioGcaAnexos']['exame2']);

			                $this->data['UsuarioGcaAnexos']['exame2'] = $retorno['data'][$nome_arquivo]['path'];

		                	$dados_usuario_imagem2['UsuarioGcaAnexos']['imagem'] = $url_file_server.$this->data['UsuarioGcaAnexos']['exame2'];		                	
		                	$dados_usuario_imagem2['UsuarioGcaAnexos']['codigo_usuario_gca'] = $codigo_usuario_gca;

		                	$this->UsuarioGcaAnexos->incluir($dados_usuario_imagem2);
			            }
					} 
					else {
						unset($this->data['UsuarioGcaAnexos']['exame2']['name']);
						unset($this->data['UsuarioGcaAnexos']['exame2']['type']);
						unset($this->data['UsuarioGcaAnexos']['exame2']['error']);
						unset($this->data['UsuarioGcaAnexos']['exame2']['size']);
						unset($this->data['UsuarioGcaAnexos']['exame2']);
					}
				}
				else {
					//erro ao inserir no grupo_covid_atendimento
					$this->BSession->setFlash('save_error');
					$this->redirect(array('action' => 'editar', $codigo_usuario, $codigo_cliente_funcionario, $codigo_funcionario_setor_cargo));
				}//fim else covido_usuario_gca

				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'editar', $codigo_usuario, $codigo_cliente_funcionario, $codigo_funcionario_setor_cargo));

			}//fim incluir com sucesso
			else {
				$this->BSession->setFlash('save_error');
				$this->redirect(array('action' => 'editar', $codigo_usuario, $codigo_cliente_funcionario, $codigo_funcionario_setor_cargo));
			}

		}//post / put

		//buscar dados do atestados quando o funcionario criar o atestados para poder incluir o anexo do atestado
		// $dados_atestado = $this->Atestado->find('first', array('conditions' => array('codigo_cliente_funcionario' => $codigo_cliente_funcionario, 'codigo_func_setor_cargo' => $codigo_funcionario_setor_cargo)));
        
        //seta os filtros na query
        $conditions = array('UsuarioGrupoCovid.codigo_usuario' => $codigo_usuario);
        $query = $this->UsuarioGrupoCovid->getFuncionarios($conditions);
        $dados = $this->UsuarioGrupoCovid->find('all', $query);
        $dados_contato_emergencia = $dados[0]['UsuarioContatoEmergencia'];
        $dados = $dados[0][0];

		$resultado_covid = array(
			"1" => 'Positivo',
			"2" => 'Negativo'
		);

		$label_grupo_anterior = '';
		//pega o codigo do grupo que estava no log
		$joinGrupoAnterior = array(
			 array(
                'table' => 'RHHealth.dbo.grupo_covid',
                'alias' => 'GrupoCovid',
                'type' => 'INNER',
                'conditions' => array('GrupoCovid.codigo = UsuarioGrupoCovidLog.codigo_grupo_covid')
            )
		);
		$codigo_grupo_covid = $this->UsuarioGrupoCovidLog->find('first',array('fields' => array('UsuarioGrupoCovidLog.codigo_grupo_covid','GrupoCovid.descricao'),'joins' => $joinGrupoAnterior,'conditions' => array('UsuarioGrupoCovidLog.codigo_usuario_grupo_covid' => $codigoUsuarioGrupoCovid, 'UsuarioGrupoCovidLog.codigo_grupo_covid NOT IN (4,5,6)'),'order' => 'UsuarioGrupoCovidLog.codigo DESC'));
		// debug($codigo_grupo_covid);exit;
		if(!empty($codigo_grupo_covid)) {
			$label_grupo_anterior = $codigo_grupo_covid['GrupoCovid']['descricao'];
		}
		else {
			//verifica se o grupo é vermelho codigo 4
			if($dados['grupo'] == 'Vermelho') {
				$label_grupo_anterior = 'Azul';
			}//fim verificacao grupo vermelho covid
		}
		
		//busca os atendimentos feitos para aquele usuario		
		$dados_atendimento = $this->UsuarioGca->lista_atendimento($codigo_usuario);
		
		$lista_atendimento = array();
		$not_afastamento = 0;
		if(!empty($dados_atendimento)) {
			
			$UsuarioGca = array();
			$nome = '';
			$UsuarioGcaAnexos = array();

			foreach($dados_atendimento as $key => $da) {
				$UsuarioGca[$da['UsuarioGca']['codigo']]['UsuarioGca'] = $da['UsuarioGca'];
				$UsuarioGca[$da['UsuarioGca']['codigo']]['UsuarioGca']['nome'] = $da['Usuario']['nome'];

				if(!empty($da['UsuarioGcaAnexos']['anexo'])) {
					$UsuarioGca[$da['UsuarioGca']['codigo']]['UsuarioGca'][] = $da['UsuarioGcaAnexos'];
				}

				//verifica se tem uma afastamento_sintomas + data de afastamento + controle_afastamento para inativar o botão de afastamento por sintomas
				if($da['UsuarioGca']['afastamento_sintomas'] == 1 AND $da['UsuarioGca']['controle_data_afastamento'] == 1 && date('Y-m-d') < $da['0']['dt_fim_afastamento']) {
					$not_afastamento = 1;
				}
				else if($da['UsuarioGca']['afastamento_positivado'] == 1) {
					$not_afastamento = 1;
				}
			}

			$lista_atendimento = $UsuarioGca;
			
			// $lista_atendimento['UsuarioGca']['UsuarioGcaAnexos'] = $UsuarioGcaAnexos;
		}

		// debug($lista_atendimento);exit;

		$this->set(compact('dados', 'lista_atendimento','codigoUsuarioGrupoCovid', 'codigo_usuario', 'codigo_cliente_funcionario','codigo_funcionario_setor_cargo', 'dados_atestado','resultado_covid','label_grupo_anterior','not_afastamento', 'dados_contato_emergencia'));
		
	}//fim editar

	

	public function listagem($export = false) 
	{	
		//não precisa de um ctp
        $this->layout = 'ajax';

        //pega os dados de filtros da sessao
        $filtros = $this->Filtros->controla_sessao($this->data, 'UsuarioGrupoCovid');
        
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			if(empty($filtros['codigo_cliente'])) {
				$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
			}
        }

        $filtros['codigo_cliente'] = (isset($this->authUsuario['Usuario']['multicliente'])) ? $this->normalizaCodigoCliente($filtros['codigo_cliente']) : $filtros['codigo_cliente'];
        
        //seta os filtros na query
        $conditions = $this->UsuarioGrupoCovid->converteFiltroEmCondition($filtros);

        $dados = $this->UsuarioGrupoCovid->getFuncionarios($conditions);

        if($export) {

            ini_set('max_execution_time', '300');
            ini_set('memory_limit', '512M');

            //$query = $this->UsuarioGrupoCovid->find('sql',$dados);
            $this->exportar($dados);
        }

		// $this->paginate['UsuarioGrupoCovid'] = $dados;
		$this->paginate['UsuarioGrupoCovid'] = array(
			'recursive' => -1,	
			'fields' => $dados['fields'],
			'joins' => $dados['joins'],
			'conditions' => $dados['conditions'],
			'limit' => 50,
			'order' => $dados['order']
			);
        // debug($this->UsuarioGrupoCovid->find('all',$this->paginate['UsuarioGrupoCovid']));
        
        $listagem = $this->paginate('UsuarioGrupoCovid'); 
        $this->set(compact('listagem'));
	}

	public function exportar($query){

		/***************************************************
       	* validacao para evitar o cliente de
       	* burlar o acesso e ver dados de outros clientes;
       	***************************************************/
      	if(!is_null($this->BAuth->user('codigo_cliente'))) {
        	$dados_cliente = $this->GrupoEconomicoCliente->retorna_dados_cliente($this->BAuth->user('codigo_cliente'));
        	$codigo_grupo_economico = $dados_cliente['GrupoEconomicoCliente']['codigo_grupo_economico'];
      	}	

      	ob_start();
		$results = $this->UsuarioGrupoCovid->find('all',$query);
		// debug($results);exit;

		ob_clean();

		header('Content-Disposition: attachment; filename="usuarios_grupo_covid_'.date('YmdHis').'.csv"');
		header('Content-type: text/csv; charset=UTF-8');

		$cabecalho = utf8_decode('"Empresa";"Unidade";"Setor";"Cargo";"Funcionario";"Matricula";"CPF";"Telefone";"Email";"Nome Contato de Emergência";"E-mail";"Telefone";"Grau de Parentesco";"Data Resposta Questionario";"Grupo";"Passaporte";');
		
		echo $cabecalho."\n";

        if(!empty($results)){
			foreach($results as $value) {				
				$linha = $value[0]['empresa'].';';
				$linha .= $value[0]['unidade_nome_fantasia'].';';
				$linha .= $value[0]['setor_descricao'].';';
				$linha .= $value[0]['cargo_descricao'].';';
				$linha .= $value[0]['funcionario_nome'].';';
				$linha .= $value[0]['funcionario_matricula'].';';
	            $linha .= $value[0]['funcionario_cpf'].';';
	            $linha .= $value[0]['telefone'].';';
	            $linha .= $value[0]['email'].';';
	            $linha .= $value['UsuarioContatoEmergencia']['nome'].';';
	            $linha .= $value['UsuarioContatoEmergencia']['email'].';';
	            $linha .= $value['UsuarioContatoEmergencia']['telefone'].';';
	            $linha .= $value['UsuarioContatoEmergencia']['grau_parentesco'].';';
				$linha .= ((!is_null($value[0]['data_respondeu'])) ? date("d/m/Y", strtotime($value[0]['data_respondeu'])) : '').';';				
	            $linha .= $value[0]['grupo'].';';
	            $linha .= (!is_null($value[0]['passaporte'])) ? ($value[0]['passaporte'] == 1) ? 'VERDE':'VERMELHO' : 'SEM PASSAPORTE HOJE'.';';	            
	            $linha .=  "\n";
				echo utf8_decode($linha);
			}
		}

		die();

	}//fim exportar

	/**
	 * [modal_retorno metodo para apresentar as resposntas de retorno]
	 * @return [type] [description]
	 */
	public function modal_retorno($cpf)
	{

		$query = "SELECT r.codigo,
					r.label_questao,
					r.label,
					convert(varchar, r.data_inclusao, 103) as dt,
					convert(varchar, r.data_inclusao, 8) as hr
				FROM usuarios_dados ud
					INNER JOIN respostas r ON ud.codigo_usuario = r.codigo_usuario
				WHERE r.codigo_questionario = 13
					AND ud.cpf = '".$cpf."';";

		$dados = $this->UsuarioGrupoCovid->query($query);

		// debug($dados);exit;

		$this->set(compact('cpf','dados'));

	}//fim modal_retorno

	/**
	 * [modal_sintomas metodo para apresentar as resposntas de sintomas]
	 * @return [type] [description]
	 */
	public function modal_sintomas($cpf)
	{

		$query = "SELECT r.codigo,
					r.label_questao,
					r.label,
					convert(varchar, r.data_inclusao, 103) as dt,
					convert(varchar, r.data_inclusao, 8) as hr
				FROM usuarios_dados ud
					INNER JOIN respostas r ON ud.codigo_usuario = r.codigo_usuario
				WHERE r.codigo_questionario = 16
					and r.data_inclusao >= (select top 1 data_inclusao from respostas where codigo_usuario = r.codigo_usuario and codigo_questionario = 16 order by codigo desc)
					AND ud.cpf = '".$cpf."';";

		$dados = $this->UsuarioGrupoCovid->query($query);

		$this->set(compact('cpf','dados'));

	}//fim modal_sintomas

	/**
	 * [set_grupo_vermelho metodo para setar o suaurio como grupo vermelho]
	 * @param [type] $codigo_usuario_grupo_covid [description]
	 */
	private function set_troca_grupo($codigo_usuario_grupo_covid, $codigo_grupo_covid)
	{
		$dados = array(
			'UsuarioGrupoCovid' => array(
				'codigo' => $codigo_usuario_grupo_covid,
				'codigo_grupo_covid' => $codigo_grupo_covid
			)
		);

		$this->UsuarioGrupoCovid->atualizar($dados);
	}//fim set grupo vermelho

	/**
	 * [set_passaporte_vermelho metodo para setar o passaporte vermelho e deixar no grupo passado o passaporte]
	 * @param [type] $codigo_usuario     [description]
	 * @param [type] $codigo_grupo_covid [description]
	 */
	private function set_passaporte_vermelho($codigo_usuario,$codigo_grupo_covid)
	{
		//gerando o passaporte VERMELHO para o usuario coloca ele com o passaporte verde
		$dados_resultado_passaporte = array(
			'ResultadoCovid' => array(
				'codigo_usuario' => $codigo_usuario,
				'codigo_grupo_covid' => $codigo_grupo_covid,
				'passaporte' => 0
			)
		);
		//busca o resultado do passaporte de hoje
		$resultado_passaporte = $this->ResultadoCovid->find('first',array('conditions' => 
			array(
				'codigo_usuario' => $codigo_usuario, 
				'passaporte' => 1, 
				'DAY(data_inclusao)' => date('d'),
                'MONTH(data_inclusao)' => date('m'),
                'YEAR(data_inclusao)' => date('Y') 
            )));
		//verifica se foi gerado um passaporte hoje
		if(!empty($resultado_passaporte)) {
			$dados_resultado_passaporte['ResultadoCovid']['codigo'] = $resultado_passaporte['ResultadoCovid']['codigo'];
			$this->ResultadoCovid->atualizar($dados_resultado_passaporte);
		}
		else {
			$this->ResultadoCovid->incluir($dados_resultado_passaporte);
		}

	}//fim set passaporte vermelho
}