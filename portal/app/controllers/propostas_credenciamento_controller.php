<?php
define('COOKIE_LOCAL', $_SERVER['DOCUMENT_ROOT'].'/portal/app/webroot/cookies/cnpj_receita/');
define('HTTP_COOKIE_LOCAL', 'http://'.$_SERVER['SERVER_NAME'].'/portal/cookies/cnpj_receita/');
class PropostasCredenciamentoController extends AppController {
    public $name = 'PropostasCredenciamento';
    public $helpers = array('BForm', 'Html', 'Ajax');
    var $uses = array(
    	'PropostaCredenciamento',
    	'PropostaCredEndereco',
    	'Servico',
    	'PropostaCredMedico',
    	'PropostaCredExame',
    	'PropostaCredEngenharia',
    	'PropostaCredFoto',
    	'EnderecoEstado',
    	'RhBanco',
    	'Horario',
    	'HorarioDiferenciado', 
    	'Usuario',
    	'Fornecedor',
    	'VEndereco',
    	'FornecedorContato',
    	'FornecedorUnidade',
    	'StatusPropostaCred',
    	'ProdutoServico',
    	'ListaDePreco',
    	'ListaDePrecoProduto',
    	'ListaDePrecoProdutoServico',
    	'Produto',
    	'PropostaSemValidacao',
    	'Documento',
    	'TipoDocumento',
    	'PropostaCredProduto',
    	'ConselhoProfissional',
	    'FornecedorHorario',
	    'FornecedorMedico',
	    'FornecedorFoto',
    	'MotivoRecusa',
    	'EnderecoCidade',
    	'PropostaCredDocumento',
    	'FornecedorDocumento',
    	'PropostaCredHistorico',
    	'Medico',
    	'PropostaCredExameLog',
		'MultiEmpresa',
    	'Uperfil'
   	);
   	
    var $components = array('RequestHandler', 'Session');
    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('etapa1', 'etapa2', '_enviaSenha','verifica_cnpj', 'verificacnpj',  'getcaptcha', 'retorno_receita', '_transforma_html_em_array', 'limpa_cookie',  '_pega_o_que_interessa',  '_retorno_html_receita', 'minha_proposta', 'remove_exame', 'verifica_definicao_exames', 'status_exame', 'verifica_exames_proposta', 'volta_status_exame', 'envia_retorno_de_valores', 'termo','regerar_lista'));
    }  
    
	function index() {
		
		$this->pageTitle = 'Propostas Credenciamento';
		$this->data['PropostaCredenciamento'] = $this->Filtros->controla_sessao($this->data, 'PropostaCredenciamento');
		
		$this->StatusPropostaCred->virtualFields = array('ordenada' => 'CONCAT(StatusPropostaCred.ordenacao, " - ", StatusPropostaCred.descricao)');
		
        $this->set('array_status', array('' => 'Todos os Status do Processo') + $this->StatusPropostaCred->find('list', array(
        	'fields' => array('StatusPropostaCred.codigo', 'ordenada'),
        	'order' => array('ordenacao ASC')
        )));
        $this->set('array_cadastro', array('' => 'Todos os Tipos', '1' => 'Cadastramento Ativo', '0' => 'Cadastramento Passivo'));
        $this->set('array_polaridade', array('' => 'Todos os Status', '1' => 'Propostas Ativas', '0' => 'Propostas Inativas'));
	}
	function listagem($destino = null, $codigo = null) {
		
		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, 'PropostaCredenciamento');
		
		$conditions = $this->PropostaCredenciamento->converteFiltrosEmConditions($filtros);
		
		$this->paginate['PropostaCredenciamento'] = array (
				'recursive' => 1,
				'conditions' => $conditions,
				'fields' => array (
						'PropostaCredEndereco.cidade as cidade',
						'PropostaCredEndereco.estado as estado',
						'PropostaCredenciamento.codigo',
						'PropostaCredenciamento.razao_social',
						'PropostaCredenciamento.nome_fantasia',
						'PropostaCredenciamento.data_inclusao',
						'PropostaCredenciamento.codigo_usuario_inclusao',
						'Usuario.nome',
						'PropostaCredenciamento.ativo',
						'PropostaCredenciamento.codigo_status_proposta_credenciamento',
						'(select s.descricao from status_proposta_credenciamento s where s.codigo=PropostaCredenciamento.codigo_status_proposta_credenciamento) as status',
						'(select count(1) from tipos_documentos T where T.obrigatorio = 1 AND T.status = 1) AS qtd_documento',
						'(	SELECT 
								count(1) 
							FROM 
								propostas_credenciamento_documentos E 
								INNER JOIN tipos_documentos TIPO ON (E.codigo_tipo_documento = TIPO.codigo)
							WHERE 
								E.codigo_proposta_credenciamento = PropostaCredenciamento.codigo AND
								TIPO.obrigatorio = 1 AND
								TIPO.status = 1
						) AS qtd_enviado'
				),
				'joins' => array(
					array (
						'table' => 'propostas_credenciamento_endereco',
						'alias' => 'PropostaCredEndereco',
						'type' => 'LEFT',
						'conditions' => array (
							'PropostaCredEndereco.codigo_proposta_credenciamento = PropostaCredenciamento.codigo',
							'PropostaCredEndereco.matriz = 1'
						),
					),
					array (
						'table' => 'status_proposta_credenciamento',
						'alias' => 'StatusPropostaCred',
						'type' => 'INNER',
						'conditions' => array (
							'StatusPropostaCred.codigo = PropostaCredenciamento.codigo_status_proposta_credenciamento'
						),
					),	
					array (
							'table' => 'usuario',
							'alias' => 'Usuario',
							'type' => 'LEFT',
							'conditions' => array (
									'Usuario.codigo = PropostaCredenciamento.codigo_usuario_inclusao'
							)
					),						
				),
				'limit' => 50,
				'order' => 'PropostaCredenciamento.data_inclusao DESC'
		);
		
		$this->set('propostas_credenciamento', $this->paginate('PropostaCredenciamento'));
	}	
	
	/**
	 * Ação que inclui os dados da Empresa e Endereço (cadastro rapido)
	 * 
	 * @author: Danilo Borges Pereira
	 */
	public function etapa1($codigo = false) {
	    $this->pageTitle = 'Cadastro de Proposta para Credenciamento';
    	$this->layout = 'default';
    	
    	// verifica se o formulario foi submetido!
        if ($this->RequestHandler->isPost()) {
        	
        	// verifica hash (se representa alguma multi empresa cadastrada)
        	$dados_multi_empresa = $this->MultiEmpresa->find('first', array('conditions' => array('hash' => $this->data['MultiEmpresa']['hash'])));
        	
        	// empresa existe ?
        	if(isset($dados_multi_empresa['MultiEmpresa']['codigo']) && $dados_multi_empresa['MultiEmpresa']['codigo']) {
        		
        		// Adiciona codigo da empresa nas tabelas que vao ser salvas (!!!)
        		$this->data['PropostaCredenciamento']['codigo_empresa'] = $dados_multi_empresa['MultiEmpresa']['codigo'];
        		$this->data['PropostaCredEndereco']['0']['codigo_empresa'] = $dados_multi_empresa['MultiEmpresa']['codigo'];
        		
        	if($this->PropostaCredenciamento->incluir($this->data, $etapa = 1)) {
        		$this->BSession->setFlash('proposta_etapa1');
				$this->redirect(array('action' => 'etapa2', base64_encode($this->PropostaCredenciamento->id)));
        	} else {
        		$this->BSession->setFlash('save_error');
        		}
        	} else {
        		$this->BSession->setFlash('empresa_nao_encontrada');        		
        	}
        		
        } 
        $comum = new Comum;
		$lista_estados = $comum->estados();
		$lista_estados[''] = 'UF';
		ksort($lista_estados);	
        $this->set('menu', false);
        $this->set('estados', $lista_estados);
	}
	
	/**
	 * Ação que inclui o restante das informações da proposta (completa cadastro, inclui medicos, exames, endereÃ§os filiais)
	 * 
	 * @author: Danilo Borges Pereira
	 */	
	public function etapa2($codigo = false) {
		
		ini_set('max_execution_time', '999999');
		ini_set('memory_limit', '1G');
		
		$codigo = base64_decode($codigo);
		$infoProposta = $this->PropostaCredenciamento->find('first', array('conditions' => array('codigo' => $codigo, 'codigo_status_proposta_credenciamento' => 1)));
		if($infoProposta) {
			
	    	// verifica se o formulario foi submetido!
	        if ($this->RequestHandler->isPost()) {
	        	
	        	$this->data['PropostaCredenciamento']['codigo'] = $codigo;
	        	
	        	if($retorno = $this->PropostaCredenciamento->incluir($this->data, $etapa = 2)) {
	        		
	        		if(!count($this->Usuario->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo))))) {
	        			// $this->data['PropostaCredenciamento']['email'] = $infoProposta['PropostaCredenciamento']['email'];
	        			
	        			/*** Foi definido em 29/04 que nao é mais para enviar senha nesta etapa!!!
	        			if(isset($infoProposta['PropostaCredenciamento']['ativo']) && $infoProposta['PropostaCredenciamento']['ativo'] == '1') {
	        				$this->_enviaSenha($this->data['PropostaCredenciamento'], 'ativo', $infoProposta['PropostaCredenciamento']['codigo']);
	        			} else {
	        				$this->_enviaSenha($this->data['PropostaCredenciamento'], 'passivo', $this->PropostaCredenciamento->id);
	        			}
	        			***/
	        			
	        		} 
	        		$this->BSession->setFlash('proposta_salva');
					// $this->redirect(isset($this->authUsuario['Usuario']) ? array('action' => 'index') : '/');
					$this->redirect('/');
					
	        	} else {
	        		
	        		// valida se tem algum tipo de servico selecionado! se não tiver tiver, set msg na mão!!!
	        		if(isset($this->data['PropostaCredProduto'][60]) && isset($this->data['PropostaCredProduto'][59])) {
	        			if(($this->data['PropostaCredProduto'][60] != '1') && ($this->data['PropostaCredProduto'][59] != '1')) {
	        				$this->set('msg_tipo_servico', 'Você deve escolher pelo menos um tipo de serviço!');
	        			}
	        		}
	        		
	        		$this->BSession->setFlash('save_error');
	        	}
	        } else {
	        	foreach($this->retorna_tabela_exames($codigo) as $key => $field) {
					if($field['tipo_servico'] == 'E') {
						$this->data['PropostaCredExame'][] = $field;
					} else if($field['tipo_servico'] == 'G') {
						$this->data['PropostaCredEngenharia'][] = $field;
					}
				}	    
	        }
	        
	    	$this->layout = 'default';        
	        
			$retorno_bancos = $this->RhBanco->find('all', array('fields' => array('codigo', 'codigo_banco', 'descricao')));
			$lista_bancos = array();
	    	foreach($retorno_bancos as $key => $campo)
				$lista_bancos[$campo['RhBanco']['codigo']] = $campo['RhBanco']['codigo_banco'] . " - " . $campo['RhBanco']['descricao'];
				
			$lista_bancos[0] = 'Selecione um Banco';
			ksort($lista_bancos);
		
			$comum = new Comum;
			$lista_estados = $comum->estados();
			$lista_estados[''] = 'UF';
			ksort($lista_estados);
			$lista_estados_medicos = $comum->estados();
			$lista_estados_medicos[''] = 'UF';
			ksort($lista_estados_medicos);
			
			$lista_servicos = $this->Servico->find('all', array(
				'conditions' => array('Servico.tipo_servico' => array('E', 'G', 'S'), 'Servico.ativo' => true),
				'joins' => array(array('table' => 'produto_servico', 'alias' => 'ProdutoServico', 'type' => 'INNER', 'conditions' => ('ProdutoServico.codigo_servico = Servico.codigo'))),
				'fields' => array('Servico.codigo', 'Servico.descricao', 'Servico.tipo_servico'), 
				'order' => 'Servico.descricao ASC')
			);
			
			$lista_exames[0] = 'Selecionar';
			$lista_engenharia[0] = 'Selecionar';
			foreach($lista_servicos as $key => $campo) {
				if($campo['Servico']['tipo_servico'] == 'E' || $campo['Servico']['tipo_servico'] == 'S')
					$lista_exames[$campo['Servico']['codigo']] = $campo['Servico']['descricao'];
				else if($campo['Servico']['tipo_servico'] == 'G')
					$lista_engenharia[$campo['Servico']['codigo']] = $campo['Servico']['descricao'];
			}
			
			$tabela_padrao = $this->retorna_tabela_padrao();
				
			// add na listagem os produtos da lista de preco que nao estao na listagem;
			foreach($tabela_padrao as $key => $campo) {
				if(!array_key_exists($campo['codigo'], $lista_exames)) {
					$lista_exames[$campo['codigo']] = $campo['nome'];
				}
			}
				
			// ordena!!!
			asort($lista_exames);			
			
			if(!isset($this->data['Medico']) || !count($this->data['Medico'])) {
				$options['conditions'] = array("PropostaCredMedico.codigo_proposta_credenciamento = {$codigo}");
				$options['joins'] = array (
						array (
							'table' => 'medicos',
							'alias' => 'Medico',
							'type' => 'INNER',
							'conditions' => array (
									'Medico.codigo = PropostaCredMedico.codigo_medico'
							)
						)
				);
				$options ['fields'] = array ( 'Medico.codigo', 'Medico.nome', 'Medico.codigo_conselho_profissional', 'Medico.numero_conselho', 'Medico.conselho_uf');
				$dadosMedico = $this->PropostaCredMedico->find('all', $options);
				if(count($dadosMedico)) {
					foreach($dadosMedico as $key => $item) {
						$this->data['Medico'][] = $item['Medico'];
					}
				}
			}
			
			// $dias = array();
			// for($i = 1; $i <= 30; $i++) {
			// 	$dias[$i] = sprintf("%02s", $i);
			// }

			$tempo_liberacao = array(
				'' => "Selecionar",
				1 => "Liberação imediata",
				2 => "1h",
				3 => "2h",
				4 => "3h",
				5 => "4h",
				6 => "5h",
				7 => "6h",
				8 => "7h",
				9 => "8h",
				10 => "9h",
				11 => "10h",
				12 => "11h",
				13 => "12h",
				14 => "13h",
				15 => "14h",
				16 => "15h",
				17 => "16h",
				17 => "17h",
				19 => "18h",
				20 => "19h",
				21 => "20h",
				22 => "21h",
				23 => "22h",
				24 => "23h",
				25 => "24h",
				26 => "1 dias",
				27 => "2 dias",
				28 => "3 dias",
				29 => "4 dias",
				30 => "5 dias",
				31 => "6 dias",
				32 => "7 dias",
				33 => "8 dias",
				34 => "9 dias",
				35 => "10 dias",
				36 => "11 dias",
				37 => "12 dias",
				37 => "13 dias",
				39 => "14 dias",
				40 => "15 dias",
				41 => "16 dias",
				42 => "17 dias",
				43 => "18 dias",
				44 => "19 dias",
				45 => "20 dias",
				46 => "21 dias",
				47 => "22 dias",
				48 => "23 dias",
				49 => "24 dias",
				50 => "25 dias",
				51 => "26 dias",
				52 => "27 dias",
				53 => "28 dias",
				54 => "29 dias",
				55 => "30 dias",
				56 => "31 dias",
				57 => "32 dias",
				58 => "33 dias",
				59 => "34 dias",
				60 => "35 dias",
				61 => "36 dias",
				62 => "37 dias",
				63 => "38 dias",
				64 => "39 dias",
				65 => "40 dias",
				66 => "41 dias",
				67 => "42 dias",
				68 => "43 dias",
				69 => "44 dias",
				70 => "45 dias",
				71 => "46 dias",
				72 => "47 dias",
				73 => "48 dias",
				74 => "49 dias",
				75 => "50 dias",
				76 => "51 dias",
				77 => "52 dias",
				78 => "53 dias",
				79 => "54 dias",
				80 => "55 dias",
				81 => "56 dias",
				82 => "57 dias",
				83 => "58 dias",
				84 => "59 dias",			
				85 => "60 dias"			
			);
					
			$dias = array(
            	'10' => '10', 
            	'20' => '20', 
            	'30' => '30'
        	);

			$this->set('tempo_liberacao', $tempo_liberacao);			
			$this->set('dias', $dias);
	        $this->set('menu', false);
	        $this->set('estados', $lista_estados);
	        $this->set('estados_medico', $lista_estados_medicos);
	        $this->set('exames', array('0' => 'Selecionar') + $lista_exames);
	        $this->set('engenharias', $lista_engenharia);
	        $this->set('bancos', $lista_bancos);
	        $this->set('list_conselhos', $this->ConselhoProfissional->find('list', array('fields' => array('codigo', 'descricao'))));
	        
	        if($this->RequestHandler->isPost()) {
	        	$this->data['PropostaCredenciamento']['email'] = $infoProposta['PropostaCredenciamento']['email'];
	        	$this->set('infoProposta', $this->data);
	        	
	        	// if(isset($this->data['PropostaCredEndereco']) && count($this->data['PropostaCredEndereco'])) {
	        		
	        	// 	$lista_das_cidades = array();
	        	// 	foreach($this->data['PropostaCredEndereco'] as $key => $info_endereco) {
	        	// 		if(isset($this->data['PropostaCredEndereco'][$key]['estado']) && $this->data['PropostaCredEndereco'][$key]['estado']) {
	        	// 			$lista_das_cidades[$key] = $this->EnderecoCidade->find('list', array('conditions' => array('estado' => $this->data['PropostaCredEndereco'][$key]['estado']), 'fields' => array('codigo', 'descricao')));
	        	// 		} else {
	        	// 			$lista_das_cidades[$key] = array('' => 'Selecione o Estado Primeiro!');
	        	// 		}	
	        	// 	}
	        	// }
	        	
	        } else {
	        	
		        $tipos_produto = $this->PropostaCredProduto->find('list',  array(
						'conditions' => array('PropostaCredProduto.codigo_proposta_credenciamento' => $codigo),
						'fields' => array('PropostaCredProduto.codigo_produto', 'PropostaCredProduto.codigo_proposta_credenciamento')
		        	)
		        );
		        
		        foreach($tipos_produto as $key => $campo) {
		        	$tipos_produto[$key] = 1;
		        }
		        
		        $this->data['Horario'] = $this->Horario->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo)));
		        //vai buscar os horarios diferenciado da proposta
				$horario_diferenciado_table = $this->HorarioDiferenciado->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo)));
				$horario_diferenciado = array();
		
				if(!empty($horario_diferenciado_table)) {
					foreach($horario_diferenciado_table as $hr) {
						$horario_diferenciado['HorarioDiferenciado'][] = $hr['HorarioDiferenciado'];
					}
				}
				//fields do exame configurado
				$fields_pce = array('Servico.descricao', 'Servico.codigo');
				//joins
				$joins_pce  = array(
					array(
						'table' => 'servico',
						'alias' => 'Servico',
						'type' => 'INNER',
						'conditions' => array('Servico.codigo = PropostaCredExame.codigo_exame')        			
		        	),
				);
				//where
				// $conditions_pce = array('PropostaCredExame.codigo_proposta_credenciamento' => $codigo);
				//busca na proposta credenciamento os exames configurados
				$exames_credenciado = $this->PropostaCredExame->find('all', 
					array(
						'fields' => $fields_pce, 
						'joins' => $joins_pce 
						// 'conditions' => $conditions_pce
					)
				);
				$exames_credenciado_combo = array();
				foreach ($exames_credenciado as $exame_cred) {
					# code...
					$exames_credenciado_combo[$exame_cred['Servico']['codigo']] = $exame_cred['Servico']['descricao'];
				}
			
				$this->set('exames_credenciado', $exames_credenciado);
				$this->set('exames_credenciado_combo', $exames_credenciado_combo);
				$this->set('horario_diferenciado', $horario_diferenciado);
	        	$this->set('infoProposta', $infoProposta + array('PropostaCredProduto' => $tipos_produto));
	        	$infoPropostaEndereco = $this->PropostaCredEndereco->find('first', array('conditions' => array('codigo_proposta_credenciamento' => $codigo)));
	        	$this->set('infoPropostaEndereco', $infoPropostaEndereco);
	        	//$this->set('cidades', array('0' => $this->EnderecoCidade->find('list', array('conditions' => array('codigo' => $infoPropostaEndereco['PropostaCredEndereco']['codigo_cidade_endereco']), 'fields' => array('codigo', 'descricao')))));
	        }
	        
	        $this->set('codigo', $codigo);
	        
		} else {
			$this->redirect(array('action' => 'etapa1'));
		}
	}
    /**
     * Ação que inclui uma nova proposta de credenciamento
     * 
     * @author: Danilo Borges Pereira
     */
    public function incluir($codigo = false) {
    	$this->pageTitle = 'Cadastro de Proposta para Credenciamento';
    	$this->layout = 'default';
    	
    	// verifica se o formulario foi submetido!
        if ($this->RequestHandler->isPost()) {

        	//debug($this->data);exit;
        	
        	if(isset($this->data['PropostaCredenciamento']['etapa']) && $this->data['PropostaCredenciamento']['etapa'] == '2') {
        		
        		$this->data['PropostaCredenciamento']['codigo'] = $codigo;        		
        		$this->data['PropostaCredenciamento']['codigo_empresa'] = base64_decode($this->data['PropostaCredenciamento']['codigo_empresa']);        		
        		$resultado = $this->PropostaCredenciamento->incluir($this->data, '2');
        	} else {
        		try {
		            $this->PropostaSemValidacao->query('begin transaction');
		            
		            $this->data['PropostaSemValidacao']['codigo_empresa'] = base64_decode($this->data['PropostaSemValidacao']['codigo_empresa']);
		            $this->data['PropostaSemValidacao']['email'] = trim($this->data['PropostaSemValidacao']['email']);

		            // debug($this->data);exit;
	        		if($resultado = $this->PropostaSemValidacao->incluir($this->data)) {
	        			$this->PropostaCredenciamento->disparaEmail($this->data['PropostaSemValidacao'], $this->data['PropostaSemValidacao']['nome_fantasia'] . ' - Proposta de Parceria RHHealth', 'email_link_etapa2', $this->data['PropostaSemValidacao']['email'], $this->PropostaSemValidacao->id);
	        		}
					$this->PropostaSemValidacao->commit();
				} catch(Exception $e) {
					$this->PropostaSemValidacao->rollback();
				}
        	}
        	if($resultado) {
        		$this->BSession->setFlash('save_success');
				$this->redirect(isset($this->authUsuario['Usuario']) ? array('action' => 'index') : '/');
        	} else {
        		$this->BSession->setFlash('save_error');
        	}
        }
        
		$lista_bancos = array();
        $retorno_bancos = $this->RhBanco->find('all', array('fields' => array('codigo', 'codigo_banco', 'descricao')));
    	foreach($retorno_bancos as $key => $campo)
			$lista_bancos[$campo['RhBanco']['codigo']] = $campo['RhBanco']['codigo_banco'] . " - " . $campo['RhBanco']['descricao'];
			
		$lista_bancos[0] = 'Selecione um Banco';
		ksort($lista_bancos);
		$comum = new Comum;
	
		$lista_estados = $comum->estados();
		$lista_estados[''] = 'UF';
		ksort($lista_estados);
		$lista_estados_medicos = $lista_estados;
		$lista_estados_medicos[''] = 'UF';
		ksort($lista_estados_medicos);

		$options_servico['conditions'] = array(
			"Servico.tipo_servico in ('E','G','S')",
			"Servico.ativo = 1",
			"Servico.codigo not in (4382,4383)" //A Duda pediu que nao apareça o servico H e o HE, ela disse que estao errados..
		);

		$options_servico['fields'] = array(
			'Servico.codigo',
			'Servico.descricao',
			'Servico.tipo_servico'
		);

		$options_servico['order'] = array('Servico.descricao');
		
   		$lista_servicos = $this->Servico->find('all', $options_servico);

   		// debug($lista_servicos);

		$lista_exames[0] = 'Selecionar';
		$lista_engenharia[0] = 'Selecionar';
		foreach($lista_servicos as $key => $campo) {
			if($campo['Servico']['tipo_servico'] == 'E' || $campo['Servico']['tipo_servico'] == 'S') {
				$lista_exames[$campo['Servico']['codigo']] = $campo['Servico']['descricao'];
			} else if($campo['Servico']['tipo_servico'] == 'G') {
				$lista_engenharia[$campo['Servico']['codigo']] = $campo['Servico']['descricao'];
			}
		}
		
		$tabela_padrao = $this->retorna_tabela_padrao();
		// add na listagem os produtos da lista de preco que nao estao na listagem;
		foreach($tabela_padrao as $key => $campo) {
			if(!array_key_exists($campo['codigo'], $lista_exames)) {
				$lista_exames[$campo['codigo']] = $campo['nome'];
			}
		}
		
		// ordena!!!
		asort($lista_exames);
		
		if(isset($this->data['PropostaCredenciamento']['etapa']) && ($this->data['PropostaCredenciamento']['etapa'] == '2') && $codigo) {
			if(!isset($this->data['Medico']) || !count($this->data['Medico'])) {
				$options['conditions'] = array("PropostaCredMedico.codigo_proposta_credenciamento = {$codigo}");
				$options['joins'] = array (
						array (
								'table' => 'medicos',
								'alias' => 'Medico',
								'type' => 'INNER',
								'conditions' => array (
										'Medico.codigo = PropostaCredMedico.codigo_medico'
								)
						)
				);
				$options ['fields'] = array ( 'Medico.codigo', 'Medico.nome', 'Medico.codigo_conselho_profissional', 'Medico.numero_conselho', 'Medico.conselho_uf');
			
				$dadosMedico = $this->PropostaCredMedico->find('all', $options);
			
				if(count($dadosMedico)) {
			
					foreach($dadosMedico as $key => $item) {
						$this->data['Medico'][] = $item['Medico'];
					}
				}
			}			
		}
		$this->set('tabela_padrao', $this->retorna_tabela_padrao());
        $this->set('menu', false);
        $this->set('estados', $lista_estados);
        $this->set('estados_medicos', $lista_estados_medicos);
     
        // $dias = array();
        // for($i = 1; $i <= 30; $i++) {
        // 	$dias[$i] = sprintf("%02s", $i);
        // }

        $dias = array(
        	'10' => '10', 
        	'20' => '20', 
        	'30' => '30'
        );

		$tempo_liberacao = array(
			'' => "Selecionar",
			1 => "Liberação imediata",
			2 => "1h",
			3 => "2h",
			4 => "3h",
			5 => "4h",
			6 => "5h",
			7 => "6h",
			8 => "7h",
			9 => "8h",
			10 => "9h",
			11 => "10h",
			12 => "11h",
			13 => "12h",
			14 => "13h",
			15 => "14h",
			16 => "15h",
			17 => "16h",
			17 => "17h",
			19 => "18h",
			20 => "19h",
			21 => "20h",
			22 => "21h",
			23 => "22h",
			24 => "23h",
			25 => "24h",
			26 => "1 dias",
			27 => "2 dias",
			28 => "3 dias",
			29 => "4 dias",
			30 => "5 dias",
			31 => "6 dias",
			32 => "7 dias",
			33 => "8 dias",
			34 => "9 dias",
			35 => "10 dias",
			36 => "11 dias",
			37 => "12 dias",
			37 => "13 dias",
			39 => "14 dias",
			40 => "15 dias",
			41 => "16 dias",
			42 => "17 dias",
			43 => "18 dias",
			44 => "19 dias",
			45 => "20 dias",
			46 => "21 dias",
			47 => "22 dias",
			48 => "23 dias",
			49 => "24 dias",
			50 => "25 dias",
			51 => "26 dias",
			52 => "27 dias",
			53 => "28 dias",
			54 => "29 dias",
			55 => "30 dias",
			56 => "31 dias",
			57 => "32 dias",
			58 => "33 dias",
			59 => "34 dias",
			60 => "35 dias",
			61 => "36 dias",
			62 => "37 dias",
			63 => "38 dias",
			64 => "39 dias",
			65 => "40 dias",
			66 => "41 dias",
			67 => "42 dias",
			68 => "43 dias",
			69 => "44 dias",
			70 => "45 dias",
			71 => "46 dias",
			72 => "47 dias",
			73 => "48 dias",
			74 => "49 dias",
			75 => "50 dias",
			76 => "51 dias",
			77 => "52 dias",
			78 => "53 dias",
			79 => "54 dias",
			80 => "55 dias",
			81 => "56 dias",
			82 => "57 dias",
			83 => "58 dias",
			84 => "59 dias",			
			85 => "60 dias"			
		);
        
		$this->set('tempo_liberacao', $tempo_liberacao);
        $this->set('dias', $dias);
        $this->set('exames', array('0' => 'Selecionar') + $lista_exames);
        $this->set('engenharias', $lista_engenharia);
        $this->set('bancos', $lista_bancos);
        $this->set('list_conselhos', $this->ConselhoProfissional->find('list', array('fields' => array('codigo', 'descricao'))));
    }
    
    public function remove_exame() {
		$codigo_servico = $this->params['form']['codigo'];
		$codigo_proposta = $this->params['form']['codigo_proposta'];
    	
		$info = $this->PropostaCredExame->find('first', array('conditions' => array('codigo_exame' => $codigo_servico, 'codigo_proposta_credenciamento' => $codigo_proposta)));
    	if(isset($info['PropostaCredExame']['codigo']) && $info['PropostaCredExame']['codigo']) {
	        if ($this->PropostaCredExame->delete($info['PropostaCredExame']['codigo']))
	            exit('1');
	        else
	        	exit('0');
    	} else {
    		exit('0');
    	}
    }
    
    public function remove_servico() {
		$codigo = $this->params['form']['codigo'];
    	
    	if($codigo) {
	        if ($this->PropostaCredExame->delete($codigo))
	            exit('1');
	        else
	        	exit('0');    		
    	} else {
    		exit('0');
    	}
    }    
    
    public function retorna_tabela_exames($id_proposta) {
    	$this->loadModel("PropostaCredExame");
    	//busca o codigo do produto para filtrar os servicos
    	$dados_pro_cred_produto = $this->get_produto_credenciamento($id_proposta);

    	if($dados_pro_cred_produto){
	    	//busca os servicos
	    	$resultado = $this->get_proposta_cred_exame($dados_pro_cred_produto, $id_proposta);
    	}

    	//variavel vazia
    	$r = array();

    	if(isset($resultado)){
	    	//tratamento
	    	foreach($resultado as $key => $campo) {
	    		// debug($campo);
	    		$campo['PropostaCredExame']['valor'] = number_format($campo['PropostaCredExame']['valor'], 2, ',', '.');
	    		$campo['PropostaCredExame']['valor_contra_proposta'] = number_format($campo['PropostaCredExame']['valor_contra_proposta'], 2, ',', '.');
	    		$campo['PropostaCredExame']['tipo_servico'] = $campo['Servico']['tipo_servico'];
				$r[] = current($campo);
	    	}
    	}
   		
   		return $r;
    }
    
    public function retorna_tabela_exames_aprovados($id_proposta) {
    	$this->loadModel("PropostaCredExame");
    	$resultado = $this->PropostaCredExame->query("
    		select 
			    PropostaCredExame.codigo,
			    PropostaCredExame.codigo_exame,
			    PropostaCredExame.aceito,
			    PropostaCredExame.valor,
			    PropostaCredExame.valor_contra_proposta,
			    PropostaCredExame.valor_minimo,
				PropostaCredExame.liberacao,
			    Servico.descricao,
			    Servico.tipo_servico
			from 
			    propostas_credenciamento_exames as PropostaCredExame
			    inner join servico Servico on Servico.codigo = PropostaCredExame.codigo_exame
			where 
				PropostaCredExame.codigo_proposta_credenciamento = '{$id_proposta}'
			  and
				Servico.tipo_servico = 'E'
			ORDER BY Servico.descricao
		");
    	
    	$r = array();
    	foreach($resultado as $key => $campo) {
    		$campo[0]['valor_proposto'] = $campo[0]['valor'];
    		if($campo[0]['aceito'] == '1') {
				if(isset($campo[0]['valor_minimo']) && !empty($campo[0]['valor_minimo'])) {
					$campo[0]['valor'] = $campo[0]['valor_minimo'];
				} else if(isset($campo[0]['valor_contra_proposta']) && !empty($campo[0]['valor_contra_proposta'])) {
					$campo[0]['valor'] = $campo[0]['valor_contra_proposta'];
				}
				
// 				unset($campo[0]['valor_minimo']);
// 				unset($campo[0]['valor_contra_proposta']);
	
				$campo[0]['valor'] = number_format($campo[0]['valor'], 2, ',', '.');
				$r[] = $campo[0];
			}
    	}
    	
   		return $r;
    }    
    
    
    public function retorna_tabela_padrao() {
    
    	$sql = "select
				    s.codigo, s.descricao as nome, listas_de_preco_produto_servico.valor, s.tipo_servico
				from
				    listas_de_preco
				    inner join listas_de_preco_produto ON (listas_de_preco_produto.codigo_lista_de_preco = listas_de_preco.codigo)
				    inner join listas_de_preco_produto_servico ON (listas_de_preco_produto_servico.codigo_lista_de_preco_produto = listas_de_preco_produto.codigo)
				    inner join servico s ON (s.codigo = listas_de_preco_produto_servico.codigo_servico)
				where
				    listas_de_preco.codigo_fornecedor is null AND
				    listas_de_preco_produto.codigo_produto in (59) AND
    				s.ativo = 1
		";
    
    	if(isset($this->params['form']['exames']) && $this->params['form']['exames']) {
    		$sql .= " AND s.codigo IN ({$this->params['form']['exames']})";
    	}
    
    	$sql .= "ORDER BY s.descricao ASC";
    
    	$resultado = $this->ListaDePreco->query($sql);
    	 
    	$r = array();
    	foreach($resultado as $key => $campo) {
    		$campo[0]['valor'] = number_format($campo[0]['valor'],2,',','.');
    		$campo[0]['nome'] = Comum::trata_nome($campo[0]['nome']);
    		$r[] = current($campo);
    	}
    
    	if(isset($this->params['form']['json']) && $this->params['form']['json']) {
    		echo json_encode($r);
    		exit;
    	} else {
    		return $r;
    	}
    }    
    
    
    public function add_exames() {
    	
    	$this->loadModel("ListaDePreco");
    	 
    	$sucesso = 0;
	 	if(isset($this->params['form']['exames']) && $this->params['form']['exames']) {
			$exames_add = explode(',',$this->params['form']['exames']);
     	} else {
	  		print $sucesso;
			exit;
	   	}	
		$resultado = $this->ListaDePreco->find('all', array(
                'recursive' => -1,
                'joins' => array(
                    array(
                        'table' => 'listas_de_preco_produto',
                        'alias' => 'ListaDePrecoProduto',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ListaDePrecoProduto.codigo_lista_de_preco = ListaDePreco.codigo'
                        )
                    ),
                    array(
                        'table' => 'listas_de_preco_produto_servico',
                        'alias' => 'ListaDePrecoProdutoServico',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ListaDePrecoProdutoServico.codigo_lista_de_preco_produto = ListaDePrecoProduto.codigo'
                        )
                    ),
                    array(
                        'table' => 'servico',
                        'alias' => 'Servico',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Servico.codigo = ListaDePrecoProdutoServico.codigo_servico'
                        )
                    ),
            
                ),
                'conditions' => array(
                    'ListaDePrecoProduto.codigo_produto' => '59',
                    'ListaDePreco.codigo_fornecedor' => NULL,
                    'Servico.ativo' => '1',
                    'Servico.codigo' => $exames_add
                ),
                'fields' => array(
                	'Servico.codigo as codigo',
                    'Servico.descricao as nome',
                    'ListaDePrecoProdutoServico.valor as valor'
                ),
            )
        );
		if(!empty($resultado)){
	    	foreach($resultado as $k => $campo) {
	    		$verifica_exame = $this->PropostaCredExame->find('first', array('conditions' => array('codigo_exame' => $campo[0]['codigo'], 'codigo_proposta_credenciamento' => $this->params['form']['codigo_proposta_credenciamento'])));
	    		if(empty($verifica_exame)){
		    		$insere['PropostaCredExame'] = array(
		    			'codigo_proposta_credenciamento' => $this->params['form']['codigo_proposta_credenciamento'],
		    			'codigo_exame' => $campo[0]['codigo'],
		    			'valor' => 0,
		    			'valor_contra_proposta' => $campo[0]['valor']
		    		);
		    		if(!empty($this->authUsuario['Usuario']['codigo_empresa'])) {
		    			$insere['PropostaCredExame']['codigo_empresa'] = $this->authUsuario['Usuario']['codigo_empresa'];
		    		}
		    		
		    		if($this->PropostaCredExame->incluir($insere)) {
		    			$sucesso = 1;
		    		}
		    		
	    		unset($insere);
	    		}
	    	}
   		}
    	
   		print $sucesso;
   		exit;
    }
    
    public function visualizar($codigo) {
    	
		$tipos_produto = $this->PropostaCredProduto->find('list',  array(
	        	'joins' => array (
        			array(
						'table' => 'produto',
						'alias' => 'Produto',
						'type' => 'INNER',
						'conditions' => array (
							'Produto.codigo = PropostaCredProduto.codigo_produto'
						)        			
        			)
				),
				'conditions' => array('PropostaCredProduto.codigo_proposta_credenciamento' => $codigo),
				'fields' => array('PropostaCredProduto.codigo_produto', 'Produto.descricao')
        	)
        );
        
        $this->data = $this->PropostaCredenciamento->read(null, $codigo);
        
		$enderecos = $this->PropostaCredEndereco->find ('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo), 'order' => 'PropostaCredEndereco.matriz DESC'));
		foreach($enderecos as $key => $campo)
			$this->data['PropostaCredEndereco'][$key] = $campo['PropostaCredEndereco'];
		$estados = $this->EnderecoEstado->find('list', array('conditions' => array('codigo_endereco_pais' => 1), 'fields' => array('codigo', 'descricao')));
		array_unshift($estados, 'Selecione um Estado');
		
		$options['conditions'] = array("PropostaCredMedico.codigo_proposta_credenciamento = {$codigo}");
		$options['joins'] = array (
			array (
				'table' => 'medicos',
				'alias' => 'medico',
				'type' => 'LEFT',
				'conditions' => array (
					'medico.codigo = PropostaCredMedico.codigo_medico' 
				) 
			) 
		);
		$options ['fields'] = array ( 'medico.codigo', 'medico.nome', 'medico.codigo_conselho_profissional', 'medico.numero_conselho', 'medico.conselho_uf', 'PropostaCredMedico.codigo_proposta_credenciamento' );
		$medicos = $this->PropostaCredMedico->find('all', $options);
		
		$exames = $this->PropostaCredExame->query("
			SELECT
				propostas_credenciamento_exames.codigo, 
			    propostas_credenciamento_exames.codigo_proposta_credenciamento,
			    propostas_credenciamento_exames.codigo_exame,
			    propostas_credenciamento_exames.valor,
			    propostas_credenciamento_exames.valor_contra_proposta,
			    propostas_credenciamento_exames.aceito,
			    propostas_credenciamento_exames.valor_minimo,
			    propostas_credenciamento_exames.usuario_aprovou,
			    usuario.nome,
			    servico.descricao,
			    servico.tipo_servico,
			    (   SELECT 
			            listas_de_preco_produto_servico.valor_maximo
			        FROM 
			            listas_de_preco_produto_servico
			            INNER JOIN listas_de_preco_produto ON (listas_de_preco_produto.codigo = listas_de_preco_produto_servico.codigo_lista_de_preco_produto)
			            INNER JOIN listas_de_preco ON (listas_de_preco.codigo = listas_de_preco_produto.codigo_lista_de_preco)
			        WHERE
			            listas_de_preco_produto_servico.codigo_servico = propostas_credenciamento_exames.codigo_exame AND
			            listas_de_preco.codigo_fornecedor is null
			    ) as valor_base
			FROM
			    propostas_credenciamento_exames
			    INNER JOIN propostas_credenciamento ON (propostas_credenciamento.codigo = propostas_credenciamento_exames.codigo_proposta_credenciamento)
			    INNER JOIN servico ON (servico.codigo = propostas_credenciamento_exames.codigo_exame)
			    LEFT JOIN usuario ON (usuario.codigo = propostas_credenciamento_exames.usuario_aprovou)
			WHERE
			    propostas_credenciamento_exames.codigo_proposta_credenciamento = '{$codigo}'
			    		
		");
		
		$organiza_exames = array();
		foreach($exames as $key => $item) {
			$organiza_exame[$key]['PropostaCredExame'] = array(
				'codigo' => $item[0]['codigo'],
				'codigo_proposta_credenciamento' => $item[0]['codigo_proposta_credenciamento'],
				'codigo_exame' => $item[0]['codigo_exame'],
				'valor' => $item[0]['valor'],
				'aceito' => $item[0]['aceito'],
				'valor_minimo' => $item[0]['valor_minimo'],
				'valor_contra_proposta' => $item[0]['valor_contra_proposta'],
				'usuario_aprovou' => $item[0]['usuario_aprovou']
			);
			$organiza_exame[$key]['Usuario'] = array('nome' => $item[0]['nome']);
			$organiza_exame[$key]['Servico'] = array('descricao' => $item[0]['descricao'], 'tipo_servico' => $item[0]['tipo_servico']);
			$organiza_exame[$key]['ListaDePrecoProdutoServico'] = array('valor_base' => $item[0]['valor_base']);
		}
		
		$engenharias = array();
		
		if(isset($organiza_exame) && count($organiza_exame)) {
			foreach($organiza_exame as $key => $field) {
				if($field['Servico']['tipo_servico'] == 'G') {
					$engenharias[] = $organiza_exame[$key];
					unset($organiza_exame[$key]);
				}
			}			
		}
		
		$horarios = $this->Horario->find( 'all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo)) );
		$status = $this->data['PropostaCredenciamento']['codigo_status_proposta_credenciamento'];
		
		$banco = $this->RhBanco->find('first', array('conditions' => array('codigo' => $this->data['PropostaCredenciamento']['numero_banco']), 'fields' => array('codigo_banco', 'descricao')));
		if($status == StatusPropostaCred::PROPOSTA_ACEITA) {
			$resultado_documentos = $this->TipoDocumento->_retornaDocsEnviados($codigo);
			
			$this->set('qtd_documentos', $resultado_documentos['qtd_documentos']);
			$this->set('qtd_enviados', $resultado_documentos['qtd_enviados']);	
		}
		
		$this->set('codigo', $codigo);
		$this->set('tipos_produto', $tipos_produto);
		$this->set('status', $status);
		$this->set('banco', $banco);
		$this->set('medicos', $medicos);		
		$this->set('exames', isset($organiza_exame) && $organiza_exame ? $this->PropostaCredExame->_organizaExamesCoresCampos($organiza_exame) : array());
		$this->set('engenharias', $engenharias);
		$this->set('horarios', $horarios);
		$this->set('fotos', $this->PropostaCredFoto->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo))));
		$this->set('media', $this->ListaDePreco->retornaMediaCidade($codigo));
		$this->set('list_conselhos', $this->ConselhoProfissional->find('list', array('fields' => array('codigo', 'descricao'))));
		
		$this->StatusPropostaCred->virtualFields = array(
				'ordenada' => 'CONCAT(StatusPropostaCred.ordenacao, " - ", StatusPropostaCred.descricao)'
		);
		
        $this->set('array_status', array('' => 'Todos os Status') + $this->StatusPropostaCred->find('list', array(
        	'fields' => array('StatusPropostaCred.codigo', 'ordenada'),
        	'order' => array('ordenacao ASC')
        )));
        $this->layout = false;
    }
    
    public function reenviar_proposta($codigo) {
    	$dadosProposta = $this->PropostaCredenciamento->read(null, $codigo);
    	
    	if($dadosProposta['PropostaCredenciamento']['email'] != $this->data['PropostaCredenciamento']['email']) {
    		$dadosProposta['PropostaCredenciamento']['email'] = $this->data['PropostaCredenciamento']['email'];
    		
    		$this->PropostaCredenciamento->atualizar($dadosProposta['PropostaCredenciamento']);
    	}
    	
    	if($this->PropostaCredenciamento->disparaEmail($dadosProposta['PropostaCredenciamento'], $dadosProposta['PropostaCredenciamento']['nome_fantasia'] . ' - Proposta de Parceria RHHealth', 'email_link_etapa2', $dadosProposta['PropostaCredenciamento']['email'], $codigo)) {
    		$this->BSession->setFlash('save_success');
    	} else {
    		$this->BSession->setFlash('save_error');
    	}
    	
    	$this->redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/propostas_credenciamento/minha_proposta/' . $codigo);
    }
    
    public function ativar_proposta($codigo) {
    	
    	$this->data = $this->PropostaCredenciamento->read(null, $codigo);
    	
    	if(!trim($this->data['PropostaCredenciamento']['codigo_documento']) || $this->PropostaCredenciamento->verificaCNPJAtivo($codigo, $this->data['PropostaCredenciamento']['codigo_documento'])) {
    		
    		$dadosHistorico = $this->PropostaCredHistorico->find('list', array('conditions' => array('codigo_proposta_credenciamento' => $codigo), 'fields' => array('codigo_status_proposta_credenciamento', 'data_inclusao'), 'order' => array('codigo DESC')));
    		unset($dadosHistorico[key($dadosHistorico)]);
    		
			if(count($dadosHistorico)) {
				if(!$this->PropostaCredenciamento->atualizarStatus($this->data, key($dadosHistorico))) {
					$this->BSession->setFlash('save_error');
				} else {
					$this->BSession->setFlash('save_success');
				}				
			} else {
				$this->BSession->setFlash('save_error');
			}	
    		 
    		$this->redirect(array('action' => 'editar', $codigo));
    		
    	} else {
    		$this->BSession->setFlash('cnpj_cadastrado');
    		$this->redirect(array('action' => 'editar', $codigo));
    	}
    }
    
    public function inativar_proposta($codigo) {
    	
    	$this->data = $this->PropostaCredenciamento->read(null, $codigo);
    	
   		if(!$this->PropostaCredenciamento->atualizarStatus($this->data, StatusPropostaCred::REPROVADO)) {
   			$this->BSession->setFlash('save_error');
   		} else {
   			$this->BSession->setFlash('save_success');
   		}
    	 
    	$this->redirect(array('action' => 'editar', $codigo));
    }
    /**
     * Edita Status da Proposta, e faz seus respectivos processos!
     * 
     * @author: Danilo Borges Pereira
     */    
	function editar($codigo, $aba = null) {
		
		$this->pageTitle = 'Editar Proposta Credenciamento';
		$this->loadModel("StatusPropostaCred");
		
		if(isset($this->data['PropostaCredenciamento']['novo_status']))
			$novo_status = $this->data['PropostaCredenciamento']['novo_status'];
		
		$resultado_documentos = $this->TipoDocumento->_retornaDocsEnviados($codigo);
		// $this->log($resultado_documentos,'debug');
        if ($this->RequestHandler->isPost()) {
        	
            try {
            	//verifica se tem este indice
            	if(isset($this->data['HorarioDiferenciado']['X'])) {
            		unset($this->data['HorarioDiferenciado']['X']);
            	}
            	if((isset($this->data['PropostaCredExame']['acao']) && $this->data['PropostaCredExame']['acao'] == 'contra_proposta') && !isset($this->data['PropostaCredenciamento']['novo_status'])) {
            		$this->data = $this->data + $this->PropostaCredenciamento->read(null, $codigo);
            	}
            	$this->PropostaCredenciamento->query('begin transaction');
            	
            	// flag status
            	$muda_status = false;
            	
            	if(isset($novo_status)) {
				// $this->log('existe novo status','debug');
	        		if($novo_status == StatusPropostaCred::DOCUMENTACAO_SOLICITADA) { //7
	        			// GERA FORNECEDOR E LISTA DE PREÇO!!!
	        			// $this->log('documentacao solicitada','debug');
						$retorno = $this->_salvaFornecedor($codigo, false);
						
	        			if((($resultado_documentos['qtd_documentos'] == $resultado_documentos['qtd_enviados']) && ($resultado_documentos['qtd_enviados'] != 0))) {
	        				$novo_status = StatusPropostaCred::APROVADO;
	        				// $this->log($novo_status,'debug');
	        			} else {
		        			// $this->log('deu merda, dispara email','debug');
		        			$this->PropostaCredenciamento->disparaEmail($this->PropostaCredenciamento->read(null, $codigo), $this->data['PropostaCredenciamento']['nome_fantasia'] . ' - Documentação Solicitada.', 'envio_documentacao', $this->data['PropostaCredenciamento']['email'], null);
							
							$muda_status = true;				
	        			}
	        		}
	        		
	        		if($novo_status == StatusPropostaCred::APROVADO) {
	        			$Configuracao = $this->loadModel("Configuracao");
	        			$chave_config = array('chave' => 'EMAIL_CONTRATO_CREDENCIAMENTO');						
						$configuracao_email= $this->Configuracao->find("first", array('conditions' => $chave_config));
						$email_config = $configuracao_email['Configuracao']['valor'];
	        			$dadosProposta = $this->PropostaCredenciamento->read(null, $codigo);
						if(!empty($email_config)){
							$this->PropostaCredenciamento->disparaEmail($dadosProposta, $dadosProposta['PropostaCredenciamento']['nome_fantasia'] . ' - Seu Contrato está disponível.', 'contrato_disponivel', $email_config, $codigo);
						}
	       				$muda_status = true;	       				
	        		}
	        		
	        		if($novo_status == StatusPropostaCred::REPROVADO) {//10
	        			$muda_status = true;
	        		}	        		
            		if($novo_status == StatusPropostaCred::VALORES_APROVADOS) { //13
            			// $this->log('valores aprovados','debug');
            			$this->data = $this->PropostaCredenciamento->read(null, $codigo);
            			
            			$this->_enviaSenha($this->data['PropostaCredenciamento'], $codigo);
	        			
	        			$this->PropostaCredenciamento->disparaEmail($this->data['PropostaCredenciamento'], 'Proposta de Credenciamento Disponível.', 'termo_faturamento_disponivel', $this->data['PropostaCredenciamento']['email'], $codigo);
						
						$muda_status = true;
	        		}	
            	}
            	
	        	// verifica se ta pedindo reenvio de senha!
            	if(isset($this->data['PropostaCredenciamento']['acao']) && ($this->data['PropostaCredenciamento']['acao'] == 'reenviar_senha')) {
            		
            		$dadosProposta = $this->PropostaCredenciamento->read(null, $codigo);
	        		$dadosProposta['PropostaCredenciamento']['email'] = ($this->data['PropostaCredenciamento']['email_confirmacao'] != $dadosProposta['PropostaCredenciamento']['email']) ? $this->data['PropostaCredenciamento']['email_confirmacao'] : false;
	        		
	        		// atualiza e envia email com nova senha!
	        		if(!$this->PropostaCredenciamento->atualizarEmail($dadosProposta, $dadosProposta['PropostaCredenciamento']['email'])) {
	        			//pega os validadores
						if(count($this->PropostaCredenciamento->validationErrors)) {
							// debug($this->PropostaCredenciamento->validationErrors);exit;
							$msg_erro = implode(",", $this->PropostaCredenciamento->validationErrors);
							$this->BSession->setFlash(array(MSGT_ERROR, $msg_erro));
						}
						else {
	        				$this->BSession->setFlash('save_error');
						}//fim invalidades	
	        		}
	        	}
	        	if(isset($this->data['PropostaCredExame']['acao']) && ($this->data['PropostaCredExame']['acao'] == 'contra_proposta')) {
	        		$novo_status = StatusPropostaCred::AGUARDANDO_AVALIACAO_CONTRA_PROPOSTA;
	        		
	        		if(!$this->_salvaContraProposta($codigo, $this->data['PropostaCredExame'], $this->data['PropostaCredenciamento']['email'])) {
	        			throw new Exception('Não atualizou contra proposta!');
	        		}
	        		$muda_status = true;
	        	}
	        	
	        	// atualiza dados da proposta (EDITAR)
	        	if(isset($this->data['PropostaCredenciamento']['acao']) && ($this->data['PropostaCredenciamento']['acao'] == 'atualiza_dados')) {
	        		
	        		if(!$this->PropostaCredenciamento->atualizarDados($this->data)) {
	        			
	        			//pega os validadores
						if(count($this->PropostaCredenciamento->validationErrors)) {
							// debug($this->PropostaCredenciamento->validationErrors);exit;
							$msg_erro = implode(",", $this->PropostaCredenciamento->validationErrors);
							$this->BSession->setFlash(array(MSGT_ERROR, $msg_erro));
						}
						else {
	        				$this->BSession->setFlash('save_error');
						}//fim invalidades
	        		} else {
	        			$this->BSession->setFlash('save_success');
	        		}
	        	}
	        	
	            if(isset($muda_status) && $muda_status) {				
	            	// se é contra proposta e não tem dono na proposta, insere a pessoa que esta salvando a contra proposta.
	            	if(($novo_status == StatusPropostaCred::AGUARDANDO_AVALIACAO_CONTRA_PROPOSTA) && isset($this->data['PropostaCredenciamento']['codigo_inclusao_usuario'])) {
						if(is_null($this->data['PropostaCredenciamento']['codigo_inclusao_usuario'])) {
            				$this->data['PropostaCredenciamento']['codigo_usuario_inclusao'] = $this->authUsuario['Usuario']['codigo'];
						}
	            	}
	            	//para atualizar o status
	            	$dados = $this->data;
	            	$dados['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] = $novo_status;
					unset($dados['PropostaCredenciamento']['novo_status']);
		    		if(!$this->PropostaCredenciamento->atualizar($dados)) {
		    		//if(!$this->PropostaCredenciamento->atualizar($dados['PropostaCredenciamento'])) {
					// if(!$this->PropostaCredenciamento->atualizarStatus($this->data, $novo_status)) {
						//pega os validadores
						if(count($this->PropostaCredenciamento->validationErrors)) {
							// debug($this->PropostaCredenciamento->validationErrors);exit;
							$msg_erro = implode(",", $this->PropostaCredenciamento->validationErrors);
							$this->BSession->setFlash(array(MSGT_ERROR, $msg_erro));
						}
						else {
	        				$this->BSession->setFlash('save_error');
						}//fim invalidades
						
		        	} else {
		        		$this->BSession->setFlash('save_success');
		        	}
	            }
        		$this->PropostaCredenciamento->commit();
        		$this->redirect(array('action' => 'editar', $codigo));
					
            } catch (Exception $e) {
            	
            	// $this->log(print_r($e->getmessage(),1),'debug');
                $this->PropostaCredenciamento->rollback();
                $this->BSession->setFlash('save_error');
            }
            
            $infoProposta = $this->PropostaCredenciamento->read(null, $codigo);
            $this->data['PropostaCredenciamento']['email'] = $infoProposta['PropostaCredenciamento']['email'];
            $this->data['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] = $infoProposta['PropostaCredenciamento']['codigo_status_proposta_credenciamento'];
        } else {
        	$this->data = $this->PropostaCredenciamento->read(null, $codigo);
        }
        
        $tipos_produto = $this->PropostaCredProduto->find('list',  array(
	        	'joins' => array (
        			array(
						'table' => 'produto',
						'alias' => 'Produto',
						'type' => 'INNER',
						'conditions' => array (
							'Produto.codigo = PropostaCredProduto.codigo_produto'
						)        			
        			)
				),
				'conditions' => array('PropostaCredProduto.codigo_proposta_credenciamento' => $codigo),
				'fields' => array('PropostaCredProduto.codigo_produto', 'Produto.descricao')
        	)
        );
        
		$enderecos = $this->PropostaCredEndereco->find ('all', 
			array(
				'conditions' => array('codigo_proposta_credenciamento' => $codigo),
				'fields' => array('PropostaCredEndereco.*'),
				'order' => 'PropostaCredEndereco.matriz DESC'
			)
		);
		
		foreach($enderecos as $key => $campo) {
			$this->data['PropostaCredEndereco'][$key] = $campo['PropostaCredEndereco'];
		}
		
		$comum = new Comum;
		$lista_estados = $comum->estados();
		$lista_estados[''] = 'UF';
		ksort($lista_estados);
		
		$lista_estados_medicos = $lista_estados;
		$lista_estados_medicos[''] = 'UF';
		ksort($lista_estados_medicos);
		
		$options['conditions'] = array("PropostaCredMedico.codigo_proposta_credenciamento = {$codigo}");
		$options['joins'] = array (
			array (
				'table' => 'medicos',
				'alias' => 'medico',
				'type' => 'LEFT',
				'conditions' => array (
					'medico.codigo = PropostaCredMedico.codigo_medico' 
				) 
			) 
		);
		$options ['fields'] = array ( 'medico.codigo', 'medico.nome', 'medico.codigo_conselho_profissional', 'medico.numero_conselho', 'medico.conselho_uf', 'PropostaCredMedico.codigo_proposta_credenciamento' );
		$medicos = $this->PropostaCredMedico->find('all', $options);
		
		$exames = $this->PropostaCredExame->query("
			SELECT
				propostas_credenciamento_exames.codigo, 
			    propostas_credenciamento_exames.codigo_proposta_credenciamento,
			    propostas_credenciamento_exames.codigo_exame,
			    propostas_credenciamento_exames.valor,
			    propostas_credenciamento_exames.valor_contra_proposta,
			    propostas_credenciamento_exames.aceito,
			    propostas_credenciamento_exames.valor_minimo,
			    propostas_credenciamento_exames.usuario_aprovou,
			    usuario.nome,
			    servico.descricao,
			    servico.tipo_servico,
			    (   SELECT 
						top 1
			            listas_de_preco_produto_servico.valor_maximo
			        FROM 
			            listas_de_preco_produto_servico
			            INNER JOIN listas_de_preco_produto ON (listas_de_preco_produto.codigo = listas_de_preco_produto_servico.codigo_lista_de_preco_produto)
			            INNER JOIN listas_de_preco ON (listas_de_preco.codigo = listas_de_preco_produto.codigo_lista_de_preco)
			        WHERE
			            listas_de_preco_produto_servico.codigo_servico = propostas_credenciamento_exames.codigo_exame AND
			            listas_de_preco.codigo_fornecedor is null
			    ) as valor_base
			FROM
			    propostas_credenciamento_exames
			    INNER JOIN propostas_credenciamento ON (propostas_credenciamento.codigo = propostas_credenciamento_exames.codigo_proposta_credenciamento)
			    INNER JOIN servico ON (servico.codigo = propostas_credenciamento_exames.codigo_exame)
			    LEFT JOIN usuario ON (usuario.codigo = propostas_credenciamento_exames.usuario_aprovou)
			WHERE
			    propostas_credenciamento_exames.codigo_proposta_credenciamento = '{$codigo}'
			    		
		");
		
		$organiza_exames = array();
		foreach($exames as $key => $item) {
			
			$organiza_exame[$item[0]['codigo']]['PropostaCredExame'] = array(
				'codigo' => $item[0]['codigo'],
				'codigo_proposta_credenciamento' => $item[0]['codigo_proposta_credenciamento'],
				'codigo_exame' => $item[0]['codigo_exame'],
				'valor' => $item[0]['valor'],
				'aceito' => $item[0]['aceito'],
				'valor_minimo' => $item[0]['valor_minimo'],
				'valor_contra_proposta' => $item[0]['valor_contra_proposta'],
				'usuario_aprovou' => $item[0]['usuario_aprovou'],
				'valor_base' => ($item[0]['valor_base'] ? number_format($item[0]['valor_base'],2,',','.') : '')
			);
			$organiza_exame[$item[0]['codigo']]['Usuario'] = array('nome' => $item[0]['nome']);
			$organiza_exame[$item[0]['codigo']]['Servico'] = array('descricao' => strtoupper(mb_detect_encoding($item[0]['descricao']) == 'utf-8' ? $item[0]['descricao'] : utf8_encode(Comum::tirarAcentos($item[0]['descricao']))), 'tipo_servico' => $item[0]['tipo_servico']);
		}
		
		$lista_exames = "";
		$engenharias = array();
		if(isset($organiza_exame) && count($organiza_exame)) {
			foreach($organiza_exame as $key => $field) {
				if($field['Servico']['tipo_servico'] == 'G') {
					$engenharias[] = $organiza_exame[$key];
					unset($organiza_exame[$key]);
				} else {
					$lista_exames .= $field['PropostaCredExame']['codigo_exame'] . ",";
				}
			}			
		}
		
		$horarios = $this->Horario->find( 'all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo)) );
		//vai buscar os horarios diferenciado da proposta
		$horario_diferenciado_table = $this->HorarioDiferenciado->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo)));
		$horario_diferenciado = array();
		if(!empty($horario_diferenciado_table)) {
			foreach($horario_diferenciado_table as $hr) {
				$horario_diferenciado['HorarioDiferenciado'][] = $hr['HorarioDiferenciado'];
			}
		}
		//fields do exame configurado
		$fields_pce = array('Servico.descricao', 'Servico.codigo');
		//joins
		$joins_pce  = array(
			array(
				'table' => 'servico',
				'alias' => 'Servico',
				'type' => 'INNER',
				'conditions' => array('Servico.codigo = PropostaCredExame.codigo_exame')        			
        	),
		);
		//where
		$conditions_pce = array('PropostaCredExame.codigo_proposta_credenciamento' => $codigo);
		//busca na proposta credenciamento os exames configurados
		$exames_credenciado = $this->PropostaCredExame->find('all', array('fields' => $fields_pce, 'joins' => $joins_pce, 'conditions' => $conditions_pce));
		$exames_credenciado_combo = array();
		foreach ($exames_credenciado as $exame_cred) {
			# code...
			$exames_credenciado_combo[$exame_cred['Servico']['codigo']] = $exame_cred['Servico']['codigo'] ." - ". $exame_cred['Servico']['descricao'];
		}
		
		$retorno_bancos = $this->RhBanco->find('all', array('fields' => array('codigo', 'codigo_banco', 'descricao')));
		$lista_bancos = array();
		foreach($retorno_bancos as $key => $campo)
			$lista_bancos[$campo['RhBanco']['codigo']] = $campo['RhBanco']['codigo_banco'] . " - " . $campo['RhBanco']['descricao'];
		
		$lista_bancos[0] = 'Selecione um Banco';
		ksort($lista_bancos);
		
		if($this->data['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::PROPOSTA_ACEITA) {
			$this->set('qtd_documentos', $resultado_documentos['qtd_documentos']);
			$this->set('qtd_enviados', $resultado_documentos['qtd_enviados']);	
		}
		
		
		$options_historico['conditions'] = array('PropostaCredHistorico.codigo_proposta_credenciamento' => $codigo);
		$options_historico['fields'] = array('PropostaCredHistorico.codigo_status_proposta_credenciamento', 'PropostaCredHistorico.data_inclusao', 'PropostaCredHistorico.codigo_usuario_inclusao', 'Usuario.nome', 'StatusPropostaCred.descricao');
		$options_historico['order'] = array('PropostaCredHistorico.data_inclusao DESC');
		$options_historico['joins'] = array (
			array (
				'table' => 'usuario',
				'alias' => 'Usuario',
				'type' => 'LEFT',
				'conditions' => array (
					'PropostaCredHistorico.codigo_usuario_inclusao = Usuario.codigo'
				)
			),
			array (
				'table' => 'status_proposta_credenciamento',
				'alias' => 'StatusPropostaCred',
				'type' => 'LEFT',
				'conditions' => array (
						'StatusPropostaCred.codigo = PropostaCredHistorico.codigo_status_proposta_credenciamento'
				)
			)				
		);
		
		$dadosHistorico = $this->PropostaCredHistorico->find('all', $options_historico);
		
		// $dias = array();
		// for($i = 1; $i <= 30; $i++) {
		// 	$dias[$i] = sprintf("%02s", $i);
		// }

		$dias = array(
        	'10' => '10', 
        	'20' => '20', 
        	'30' => '30'
        );
		
		$this->set('tabela_padrao', $this->retorna_tabela_padrao());
		$this->set('dias', $dias);
		$this->set('codigo', $codigo);
		$this->set('estados', $lista_estados);
		$this->set('estados_medico', $lista_estados_medicos);
		$this->set('dados_historico', $dadosHistorico);
		$this->set('tipos_produto', $tipos_produto);
		$this->set('status', $this->data['PropostaCredenciamento']['codigo_status_proposta_credenciamento']);
		$this->set('bancos', $lista_bancos);
		$this->set('aba', !is_null($aba) ? $aba : 'dados_proposta');
		$this->set('medicos', $medicos);		
		$this->set('exames', isset($organiza_exame) && $organiza_exame ? $this->PropostaCredExame->_organizaExamesCoresCampos($organiza_exame) : array());
		$this->set('engenharias', $engenharias);
		$this->set('horarios', $horarios);
		$this->set('horario_diferenciado', $horario_diferenciado);
		$this->set('exames_credenciado', $exames_credenciado);
		$this->set('exames_credenciado_combo', $exames_credenciado_combo);
		$this->set('fotos', $this->PropostaCredFoto->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo))));
		$this->set('media', $this->ListaDePreco->retornaMediaCidade($codigo));
		$this->set('list_conselhos', $this->ConselhoProfissional->find('list', array('fields' => array('codigo', 'descricao'))));
		$this->set('motivos_recusa', $this->MotivoRecusa->find('list', array('conditions' => array('ativo' => '1'), 'fields' => array('codigo', 'descricao'))));
		
		$this->StatusPropostaCred->virtualFields = array(
			'ordenada' => 'CONCAT(StatusPropostaCred.ordenacao, " - ", StatusPropostaCred.descricao)'
		);
		
        $this->set('array_status', array('' => 'Todos os Status') + $this->StatusPropostaCred->find('list', array(
        	'fields' => array('StatusPropostaCred.codigo', 'ordenada'),
        	'order' => array('ordenacao ASC')
        )));
	}
	
	public function alteracao_valores_exames() {
		
		$this->pageTitle = 'Manutenção de Valores de Exames';
		$this->data['PropostaCredenciamento'] = $this->Filtros->controla_sessao($this->data, 'PropostaCredenciamento');
		
		$this->StatusPropostaCred->virtualFields = array('ordenada' => 'CONCAT(StatusPropostaCred.ordenacao, " - ", StatusPropostaCred.descricao)');
		
		$this->set('array_status', array('' => 'Todos os Status do Processo') + $this->StatusPropostaCred->find('list', array(
			'fields' => array('StatusPropostaCred.codigo', 'ordenada'),
			'order' => array('ordenacao ASC')
		)));
		
		$this->set('array_cadastro', array('' => 'Todos os Tipos', '1' => 'Cadastramento Ativo', '0' => 'Cadastramento Passivo'));
		$this->set('array_polaridade', array('' => 'Todos os Status', '1' => 'Propostas Ativas', '0' => 'Propostas Inativas'));
		
	}
	
	public function listagem_alteracao_valores_exames() {
		
		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, 'PropostaCredenciamento');
		
		$conditions = $this->PropostaCredenciamento->converteFiltrosEmConditions($filtros);
		
		$this->paginate['PropostaCredenciamento'] = array (
				'recursive' => 1,
				'conditions' => $conditions,
				'fields' => array (
						'PropostaCredEndereco.cidade as cidade',
						'PropostaCredEndereco.estado as estado',
						'PropostaCredenciamento.codigo',
						'PropostaCredenciamento.razao_social',
						'PropostaCredenciamento.nome_fantasia',
						'PropostaCredenciamento.data_inclusao',
						'PropostaCredenciamento.codigo_usuario_inclusao',
						'Usuario.nome',
						'PropostaCredenciamento.ativo',
						'PropostaCredenciamento.codigo_status_proposta_credenciamento',
						'(select s.descricao from status_proposta_credenciamento s where s.codigo=PropostaCredenciamento.codigo_status_proposta_credenciamento) as status',
						'(select count(1) from tipos_documentos T where T.obrigatorio = 1 AND T.status = 1) AS qtd_documento',
						'(	SELECT
								count(1)
							FROM
								propostas_credenciamento_documentos E
								INNER JOIN tipos_documentos TIPO ON (E.codigo_tipo_documento = TIPO.codigo)
							WHERE
								E.codigo_proposta_credenciamento = PropostaCredenciamento.codigo AND
								TIPO.obrigatorio = 1 AND
								TIPO.status = 1
						) AS qtd_enviado'
				),
				'joins' => array(
						array (
								'table' => 'propostas_credenciamento_endereco',
								'alias' => 'PropostaCredEndereco',
								'type' => 'LEFT',
								'conditions' => array (
										'PropostaCredEndereco.codigo_proposta_credenciamento = PropostaCredenciamento.codigo',
										'PropostaCredEndereco.matriz = 1'
								),
						),
						array (
								'table' => 'status_proposta_credenciamento',
								'alias' => 'StatusPropostaCred',
								'type' => 'INNER',
								'conditions' => array (
										'StatusPropostaCred.codigo = PropostaCredenciamento.codigo_status_proposta_credenciamento'
								),
						),
						array (
								'table' => 'usuario',
								'alias' => 'Usuario',
								'type' => 'LEFT',
								'conditions' => array (
										'Usuario.codigo = PropostaCredenciamento.codigo_usuario_inclusao'
								)
						),
				),
				'limit' => 50,
				'order' => 'PropostaCredenciamento.data_inclusao DESC'
		);
		
		$this->set('propostas_credenciamento', $this->paginate('PropostaCredenciamento'));
				
	}	
	
	public function manutencao_valores_servicos($codigo) {
		
		$this->pageTitle = 'Manutenção de Valores do Credenciamento';
		$this->loadModel("StatusPropostaCred");
		
		if(isset($this->data['PropostaCredenciamento']['novo_status']))
			$novo_status = $this->data['PropostaCredenciamento']['novo_status'];
		
		$resultado_documentos = $this->TipoDocumento->_retornaDocsEnviados($codigo);
		
        if ($this->RequestHandler->isPost()) {
        	
            try {
            	
            	$this->PropostaCredExame->query('begin transaction');
            	$recupera_sessao = $this->Session->read('exames');
            	
            	$lista_de_exames = array();
				foreach($recupera_sessao as $key => $item) {
					$lista_de_exames[$item['PropostaCredExame']['codigo']]['valor'] = str_replace(".", ",", $item['PropostaCredExame']['valor']); 
					$lista_de_exames[$item['PropostaCredExame']['codigo']]['valor_contra_proposta'] = str_replace(".", ",", $item['PropostaCredExame']['valor_contra_proposta']);
					$lista_de_exames[$item['PropostaCredExame']['codigo']]['valor_minimo'] = str_replace(".", ",", $item['PropostaCredExame']['valor_minimo']);
				}            	
            	
				foreach($lista_de_exames as $k => $campo) {
					
					if(
							(isset($this->data['PropostaCredExame'][$k]['valor']) && ($campo['valor'] != $this->data['PropostaCredExame'][$k]['valor'])) || 
							(isset($this->data['PropostaCredExame'][$k]['valor_contra_proposta']) && ($campo['valor_contra_proposta'] != $this->data['PropostaCredExame'][$k]['valor_contra_proposta'])) || 
							(isset($this->data['PropostaCredExame'][$k]['valor_minimo']) && ($campo['valor_minimo'] != $this->data['PropostaCredExame'][$k]['valor_minimo']))
						) {
						
						$array_atualizacao['PropostaCredExame']['codigo'] = $k;
						$array_atualizacao['PropostaCredExame']['codigo_proposta_credenciamento'] = $codigo;
						
						if(isset($this->data['PropostaCredExame'][$k]['valor']) && ($this->data['PropostaCredExame'][$k]['valor'] != $campo['valor']))
							$array_atualizacao['PropostaCredExame']['valor'] = $this->data['PropostaCredExame'][$k]['valor'];
						
						if(isset($this->data['PropostaCredExame'][$k]['valor_contra_proposta']) && $this->data['PropostaCredExame'][$k]['valor_contra_proposta'] != $campo['valor_contra_proposta'])							
							$array_atualizacao['PropostaCredExame']['valor_contra_proposta'] = $this->data['PropostaCredExame'][$k]['valor_contra_proposta'];
						
						if(isset($this->data['PropostaCredExame'][$k]['valor_minimo']) && $this->data['PropostaCredExame'][$k]['valor_minimo'] != $campo['valor_minimo'])
							$array_atualizacao['PropostaCredExame']['valor_minimo'] = $this->data['PropostaCredExame'][$k]['valor_minimo'];
						// grava atualizacao!!!
						if(!$this->PropostaCredExame->atualizar($array_atualizacao)) {
							$deu_erro  = true;	
						}
						
						// limpa array!
						unset($array_atualizacao);
					}
				}
				
				if(isset($deu_erro) && $deu_erro) {
					$this->BSession->setFlash('save_error');
				} else {
					$this->BSession->setFlash('save_success');
				}
	            
        		$this->PropostaCredExame->commit();
				$this->redirect(array('action' => 'alteracao_valores_exames'));
				
            } catch (Exception $e) {
                $this->PropostaCredExame->rollback();
                $this->BSession->setFlash('save_error');
            }
        }
        
		$exames = $this->PropostaCredExame->query("
			SELECT
				propostas_credenciamento_exames.codigo, 
			    propostas_credenciamento_exames.codigo_proposta_credenciamento,
			    propostas_credenciamento_exames.codigo_exame,
			    propostas_credenciamento_exames.valor,
			    propostas_credenciamento_exames.valor_contra_proposta,
			    propostas_credenciamento_exames.aceito,
			    propostas_credenciamento_exames.valor_minimo,
			    propostas_credenciamento_exames.usuario_aprovou,
			    usuario.nome,
			    servico.descricao,
			    servico.tipo_servico,
			    (   SELECT TOP 1
			            listas_de_preco_produto_servico.valor_maximo
			        FROM 
			            listas_de_preco_produto_servico
			            INNER JOIN listas_de_preco_produto ON (listas_de_preco_produto.codigo = listas_de_preco_produto_servico.codigo_lista_de_preco_produto)
			            INNER JOIN listas_de_preco ON (listas_de_preco.codigo = listas_de_preco_produto.codigo_lista_de_preco)
			        WHERE
			            listas_de_preco_produto_servico.codigo_servico = propostas_credenciamento_exames.codigo_exame AND
			            listas_de_preco.codigo_fornecedor is null
			    ) as valor_base
			FROM
			    propostas_credenciamento_exames
			    INNER JOIN propostas_credenciamento ON (propostas_credenciamento.codigo = propostas_credenciamento_exames.codigo_proposta_credenciamento)
			    INNER JOIN servico ON (servico.codigo = propostas_credenciamento_exames.codigo_exame)
			    LEFT JOIN usuario ON (usuario.codigo = propostas_credenciamento_exames.usuario_aprovou)
			WHERE
			    propostas_credenciamento_exames.codigo_proposta_credenciamento = '{$codigo}'
			    		
		");
		
		$organiza_exames = array();
		foreach($exames as $key => $item) {
			$organiza_exame[$key]['PropostaCredExame'] = array(
				'codigo' => $item[0]['codigo'],
				'codigo_proposta_credenciamento' => $item[0]['codigo_proposta_credenciamento'],
				'codigo_exame' => $item[0]['codigo_exame'],
				'valor' => $item[0]['valor'],
				'aceito' => $item[0]['aceito'],
				'valor_minimo' => $item[0]['valor_minimo'],
				'valor_contra_proposta' => $item[0]['valor_contra_proposta'],
				'usuario_aprovou' => $item[0]['usuario_aprovou'],
				'valor_base' => ($item[0]['valor_base'] ? number_format($item[0]['valor_base'],2,',','.') : '')
			);
			$organiza_exame[$key]['Usuario'] = array('nome' => $item[0]['nome']);
			$organiza_exame[$key]['Servico'] = array('descricao' => strtoupper(mb_detect_encoding($item[0]['descricao']) == 'utf-8' ? $item[0]['descricao'] : utf8_encode(Comum::tirarAcentos($item[0]['descricao']))), 'tipo_servico' => $item[0]['tipo_servico']);
		}
		
		$lista_exames = "";
		$engenharias = array();
		if(isset($organiza_exame) && count($organiza_exame)) {
			foreach($organiza_exame as $key => $field) {
				if($field['Servico']['tipo_servico'] == 'G') {
					$engenharias[] = $organiza_exame[$key];
					unset($organiza_exame[$key]);
				} else {
					$lista_exames .= $field['PropostaCredExame']['codigo_exame'] . ",";
				}
			}			
		}
		if(!isset($organiza_exame) || !count($organiza_exame)) {
			$this->BSession->setFlash('sem_exame');
			$this->redirect(array('action' => 'alteracao_valores_exames'));
		}
		$options['joins'] = array (
				array (
						'table' => 'usuario',
						'alias' => 'Usuario',
						'type' => 'LEFT',
						'conditions' => array (
							'Usuario.codigo = PropostaCredExameLog.codigo_usuario_alteracao'
						)
				)
		);
		
		foreach($organiza_exame as $k => $item) {
			$options['conditions'] = array('codigo_propostas_credenciamento_exames' => $item['PropostaCredExame']['codigo']);
			$options['fields'] = array('Usuario.nome', 'PropostaCredExameLog.*');
			$options['order'] = array('PropostaCredExameLog.codigo ASC');
			
			$organiza_exame[$k]['logs'] = $this->PropostaCredExameLog->find('all', $options);
		}
		
		// grava sessao
		$this->Session->write('exames', $organiza_exame);
				
		$this->set('lista_status', $this->StatusPropostaCred->find('list', array('fields' => array('codigo', 'descricao'))));
		$this->set('dadosProposta', $this->PropostaCredenciamento->read(null, $codigo));
		$this->set('codigo', $codigo);
		$this->set('exames', isset($organiza_exame) && $organiza_exame ? $this->PropostaCredExame->_organizaExamesCoresCampos($organiza_exame) : array());
	}
	
	/**
	 * Ação que altera o status da proposta p/ Aceito ou Recusado
	 * Após Ler e Aprovar Termo de Instrução de Faturamento!
	 * 
	 * AO APROVAR PROPOSTA -->> GERA LISTA DE PREÇO E FORNECEDOR!!!
	 * 
     * @author: Danilo Borges Pereira
	 */
	
	public function aceita_termo() {
		// retorna dados da proposta
		$dadosProposta = $this->PropostaCredenciamento->read(null, $this->params['pass'][0]);
		
		try {
			$this->PropostaCredenciamento->query('begin transaction');	
			
			// verifica (aprovado ou recusado)
			if($this->data['Termo']['aprovado'] == '1') {
				$status = StatusPropostaCred::AGUARDANDO_ENVIO_TERMO;
				$dadosProposta['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] = $status;
			} else {
				$status = StatusPropostaCred::TERMO_RECUSADO;
				$dadosProposta['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] = $status;
				$dadosProposta['PropostaCredenciamento']['codigo_motivo_recusa'] = $this->params['data']['PropostaCredenciamento']['codigo_motivo_recusa'];
			}
					
			// atualiza na base
			if(! $this->PropostaCredenciamento->atualizarStatus($dadosProposta, $status)) {
				$this->BSession->setFlash('save_error');	
			} else {
				$this->PropostaCredenciamento->query('commit');
				$this->BSession->setFlash('save_success');
			}
			
		} catch(Exception $e) {
			$this->PropostaCredenciamento->query('rollback');
			$this->BSession->setFlash('save_error');
		}			
		
		// redireciona
		$this->redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/propostas_credenciamento/minha_proposta/' . $this->params['pass'][0]);
	}
	
	public function contrato($codigo) {
		//join da query de proposta credenciamento
		$joins = array(
			array(
			    'table' => "propostas_credenciamento_endereco",
				'alias' => 'PropostaCredEndereco',
				'type' => 'INNER',
				'conditions' => 'PropostaCredenciamento.codigo = PropostaCredEndereco.codigo_proposta_credenciamento',
			)
		);
		//fields da view contrato
		$fields = array(
			'PropostaCredEndereco.codigo',
			'PropostaCredEndereco.logradouro',
			'PropostaCredEndereco.numero',
			'PropostaCredEndereco.complemento',
			'PropostaCredEndereco.bairro',
			'PropostaCredEndereco.cidade',
			'PropostaCredEndereco.estado',
			'PropostaCredenciamento.codigo_documento',
			'PropostaCredenciamento.nome_fantasia',
			'PropostaCredenciamento.telefone',
			'PropostaCredenciamento.razao_social',
			'PropostaCredenciamento.agencia',
			'PropostaCredenciamento.numero_conta',
			'PropostaCredenciamento.email',
			'PropostaCredenciamento.numero_banco'		
		);
		// retorna dados da proposta
		$dadosProposta = $this->PropostaCredenciamento->find('first', array(
				'fields' => $fields,
				'joins' => $joins,
				'conditions' => array('PropostaCredenciamento.codigo' => $codigo),				
			)
		);
		// anexo II do contrato impresso
		$dadosBanco = $this->RhBanco->find('first',array('conditions' => array('codigo' => $dadosProposta['PropostaCredenciamento']['numero_banco'])));
		if(!isset($dadosBanco['RhBanco']['codigo_banco']) && empty($dadosBanco['RhBanco']['codigo_banco'])){
			$dadosBanco['RhBanco'] = array(
				'descricao' => '',
				'codigo_banco' => ''
			);
		}
		$options['joins'] = array( );
		
		$options['conditions'] = array('codigo_proposta_credenciamento' => $codigo, 'matriz' => '1');
		$options['fields'] = array('PropostaCredEndereco.*');
		$dadosPropostaEndereco = $this->PropostaCredEndereco->find('first', $options);
		
		$exames = $this->retorna_tabela_exames_aprovados($codigo);
		
		$array_servicos_por_produto = array();
		foreach($exames as $key => $campo) {
			$array_servicos_por_produto[$campo['tipo_servico']][] = $campo;
		}
		
		$this->set('servicos', $array_servicos_por_produto);
		$this->set('proposta_credenciamento', $dadosProposta);
		$this->set('proposta_credenciamento_banco', $dadosBanco);
		$this->set('proposta_credenciamento_endereco', $dadosPropostaEndereco);
		$this->set('proposta_credenciamento_endereco', $dadosProposta);
		$this->layout = false;
	}
	
	public function termo($codigo) {
		
	    if($this->authUsuario['Usuario']['codigo_proposta_credenciamento'] && ($this->authUsuario['Usuario']['codigo_proposta_credenciamento'] != $codigo)) {
    		$this->redirect(array('controller' => 'propostas_credenciamento', 'action' => 'termo', $this->authUsuario['Usuario']['codigo_proposta_credenciamento']));
    	} else if(!$this->authUsuario['Usuario']['codigo_proposta_credenciamento']) {
    		$this->redirect('/');
    	}
		
		// retorna dados da proposta
		$dadosProposta = $this->PropostaCredenciamento->read(null, $codigo);
		
		$dadosPropostaEndereco = $this->PropostaCredEndereco->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo), 'order' => 'PropostaCredEndereco.matriz DESC', 'joins' => array(
			
		), 'fields' => array('PropostaCredEndereco.*')));
		
		$exames = $this->retorna_tabela_exames_aprovados($codigo);
		
		$array_servicos_por_produto = array();
		foreach($exames as $key => $campo) {
			$array_servicos_por_produto[$campo['tipo_servico']][] = $campo;
		}
		
		$options['conditions'] = array("PropostaCredMedico.codigo_proposta_credenciamento = {$codigo}");
		$options['joins'] = array (
			array (
				'table' => 'medicos',
				'alias' => 'medico',
				'type' => 'LEFT',
				'conditions' => array (
						'medico.codigo = PropostaCredMedico.codigo_medico'
				)
			)
		);
		$options ['fields'] = array ( 'medico.codigo', 'medico.nome', 'medico.codigo_conselho_profissional', 'medico.numero_conselho', 'medico.conselho_uf', 'PropostaCredMedico.codigo_proposta_credenciamento' );
		$medicos = $this->PropostaCredMedico->find('all', $options);
		
		$horarios = $this->Horario->find( 'all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo)) );
		
		$this->set('proposta_credenciamento', $dadosProposta);
		$this->set('proposta_endereco', $dadosPropostaEndereco);
		$this->set('servicos', $array_servicos_por_produto);
		$this->set('medicos', $medicos);
		$this->set('horarios', $horarios);
		$this->set('documentos', $this->TipoDocumento->find('all', array('conditions' => array('status' => '1', 'codigo_status_proposta_credenciamento' => '7'), 'fields' => array('codigo', 'descricao', 'obrigatorio'))));
		$this->set('bancos', $this->RhBanco->find('list', array('conditions' => array('codigo' => $dadosProposta['PropostaCredenciamento']['numero_banco']), 'fields' => array('codigo_banco', 'descricao'))));
		
		$this->set('list_conselhos', $this->ConselhoProfissional->find('list', array('fields' => array('codigo', 'descricao'))));
		$this->layout = false;
	}	
	
	
    /**
     * Atualiza Status do Exame! Após analisado Valor
     * 
     * NULL - Nao analisado 
     * 0 - Reprovado 
     * 1 - Aprovado
     * 
     * @author: Danilo Borges Pereira
     */	
	public function status_exame() {
		$valor_minimo = (isset($this->params['form']['valor_minimo']) && $this->params['form']['valor_minimo']) ? $this->params['form']['valor_minimo'] : NULL;
		
		$array_atualiza = $this->PropostaCredExame->read(null, $this->params['form']['codigo']);
		$array_atualiza['PropostaCredExame']['aceito'] = $valor_minimo ? NULL : $this->params['form']['status'];
		$array_atualiza['PropostaCredExame']['usuario_aprovou'] = $this->authUsuario['Usuario']['codigo'] ? $this->authUsuario['Usuario']['codigo'] : NULL;
		$array_atualiza['PropostaCredExame']['valor_minimo'] = $valor_minimo;
		
		echo ($this->PropostaCredExame->atualizar($array_atualiza)) ? '1' : '0';
		exit;
	}
        
    /**
     * Volta Status do Exame p/ Inicial - Em caso de ter clicado errado :/
     * 
     * 
     * @author: Danilo Borges Pereira
     */	
	public function volta_status_exame() {
		
		$array_atualiza = $this->PropostaCredExame->read(null, $this->params['form']['codigo']);
		$array_atualiza['PropostaCredExame']['aceito'] = NULL;
		$array_atualiza['PropostaCredExame']['usuario_aprovou'] = NULL;
		$array_atualiza['PropostaCredExame']['valor_minimo'] = NULL;
		
		echo ($this->PropostaCredExame->atualizar($array_atualiza)) ? '1' : '0';
		exit;
	}        
	
	/**
	 * Ação verifica se todos os servicos foram analisados.
	 * 
     * @author: Danilo Borges Pereira
	 */	
	public function verifica_engenharias_proposta() {
		$codigo = $this->params['form']['proposta'];
		$lista_exames = $this->PropostaCredExame->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo, 'valor' => null)));
		
		$aceitos = 0;
		$nao_aceitos = 0;
		$nao_avaliados = 0;
				
		if(count($lista_exames)) {
			foreach($lista_exames as $key => $campo) {
				if($campo['PropostaCredExame']['aceito'] == '1') {
					$aceitos++;
				} else if($campo['PropostaCredExame']['aceito'] == '0') {
					$nao_aceitos++;
				} else {
					$nao_avaliados++;
				}
			}
			
			if($nao_avaliados > 0) {
				echo "0";
			} else {
				echo "1";
			}
		} else {
			echo "1";
		}
		
		exit;
	}
	
	
	public function verifica_valores_minimos_nao_negociados() {
		
		$codigo = $this->params['form']['proposta'];
		$lista_exames = $this->PropostaCredExame->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo)));
		
		$in_definido = 0;
		// Percorre servicos e verifica posição de cada um.
		foreach($lista_exames as $key => $campo) {
			
			if(!is_null($campo['PropostaCredExame']['valor_minimo']) && $campo['PropostaCredExame']['aceito'] == '2') {
				$in_definido++;
			}
		}
		
		if($in_definido) {
			print "1";
		} else {
			print "0";
		}
		
		exit;
	}
	
	/**
	 * Ação verifica se todos os exames foram analisados, e estao status "concluídos" (aprovados ou nao).
	 * 
     * @author: Danilo Borges Pereira
     * <daniloborgespereira@gmail.com>
	 */
	public function verifica_exames_proposta() {
		$codigo = $this->params['form']['proposta'];
		$fornecedor = isset($this->params['form']['fornecedor']) && $this->params['form']['fornecedor'] ? 1 : 0;
		$lista_exames = $this->PropostaCredExame->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo)));
		
		$aceitos = 0;
		$nao_aceitos = 0;
		$nao_avaliados = 0;
		$valor_minimo_pendente = 0;
		$count = count($lista_exames);
		
		
		// Percorre servicos e verifica posição de cada um.	
		foreach($lista_exames as $key => $campo) {
			if($campo['PropostaCredExame']['aceito'] == '1') {
				$aceitos++;
			} else if($campo['PropostaCredExame']['aceito'] == '0') {
				$nao_aceitos++;
			} else if(!is_null($campo['PropostaCredExame']['valor_minimo']) && is_null($campo['PropostaCredExame']['aceito'])) {
				$valor_minimo_pendente++;
			} else {
				$nao_avaliados++;
			}
		}
		
		// verifica se todos valores estao reprovados
		if(($nao_aceitos > 1) && ($nao_aceitos == $count)) {
			$resultado = 2;
			$this->PropostaCredenciamento->atualizar(array('PropostaCredenciamento' => array('codigo' => $codigo, 'codigo_status_proposta_credenciamento' => StatusPropostaCred::REPROVADO)));
			
		} else {
			
			// valores definidos? (aceitos e nao aceitos)
			$resultado = (($count == ($aceitos + $nao_aceitos)) && ($nao_avaliados == 0) && ($valor_minimo_pendente == 0)) ? 1 : 0;
			
			if($resultado && $fornecedor) {
				$dados_proposta = $this->PropostaCredenciamento->read(null, $codigo);
				
				if($this->PropostaCredenciamento->atualizar(array('PropostaCredenciamento' => array('codigo' => $codigo, 'codigo_status_proposta_credenciamento' => StatusPropostaCred::VALORES_APROVADOS)))) {
					$this->PropostaCredenciamento->disparaEmail(
						$dados_proposta['PropostaCredenciamento'],
						'Proposta de Credenciamento está disponível',
						'termo_faturamento_disponivel',
						$dados_proposta['PropostaCredenciamento']['email'],
						$codigo
					);					
				}
				
			} else if($valor_minimo_pendente >= 1) {
				// $this->PropostaCredenciamento->atualizar(array('PropostaCredenciamento' => array('codigo' => $codigo, 'codigo_status_proposta_credenciamento' => StatusPropostaCred::AGUARDANDO_RETORNO_CONTRA_PROPOSTA)));
			}
		}
		# 0 - Ainda tem exame pendente
		# 1 - Exames Definidos
		
		echo $valor_minimo_pendente ? 0 : $resultado;
		exit;
	}
	
	
	public function verifica_definicao_exames() {
		$codigo = $this->params['form']['proposta'];
		$lista_exames = $this->PropostaCredExame->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo)));
		
		$aceitos = 0;
		$nao_aceitos = 0;
		$nao_avaliados = 0;
		$valor_minimo_pendente = 0;
		$count = count($lista_exames);
		
		foreach($lista_exames as $key => $campo) {
			if($campo['PropostaCredExame']['aceito'] == '1') {
				$aceitos++;
			} else if($campo['PropostaCredExame']['aceito'] == '0') {
				$nao_aceitos++;
			} else if(!is_null($campo['PropostaCredExame']['valor_minimo']) && is_null($campo['PropostaCredExame']['aceito'])) {
				$valor_minimo_pendente++;
			} else {
				$nao_avaliados++;
			}
		}
		
		echo ($nao_avaliados == 0) ? '1' : '0';
		exit;
	}
	
	
    /**
     * Ação mostra CTP de Contra Proposta de Exames na Tela do Credenciando!
     * OBS: Não retorna dados do formulÃ¡rio, os campos são atualizados com ajax na CTP.
     * 
     * @author: Danilo Borges Pereira
     * <daniloborgespereira@gmail.com>
     */	
	function contraproposta($codigo = null) {
		
		// retorna info proposta
		$dadosProposta = $this->PropostaCredenciamento->read(null, $codigo);
		
		$options['conditions'] = array("PropostaCredExame.codigo_proposta_credenciamento = {$codigo}");
		$options['joins'] = array (
			array (
				'table' => 'servico',
				'alias' => 'servico',
				'type' => 'LEFT',
				'conditions' => array (
					'servico.codigo = PropostaCredExame.codigo_exame' 
				) 
			) 
		);
		$options['fields'] = array ('PropostaCredExame.codigo', 'PropostaCredExame.usuario_aprovou', 'PropostaCredExame.codigo_proposta_credenciamento', 'PropostaCredExame.codigo_exame', 'PropostaCredExame.valor', 'PropostaCredExame.valor_contra_proposta', 'PropostaCredExame.valor_minimo', 'PropostaCredExame.aceito', 'servico.descricao', 'servico.tipo_servico');
		$options['order'] = array('servico.tipo_servico ASC');
		$exames = $this->PropostaCredExame->find('all', $options);
		$exames = $this->PropostaCredExame->_organizaExamesCoresCampos($exames);
		
		$this->set('exames', $exames);
		$this->set('dadosProposta', $dadosProposta);
	}	
	
	
	/**
	 * Ação para fornecedor enviar definição dos exames p/ RHHealth analisar.
	 * 
     * @author: Danilo Borges Pereira
     * <daniloborgespereira@gmail.com>
	 */
	public function envia_retorno_de_valores() {
		
		$lista_servicos = $this->PropostaCredExame->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $this->data['PropostaCredenciamento']['codigo'])));
		
		$resultado['aceitos'] = 0;
		$resultado['nao_aceitos'] = 0;
		$resultado['valor_minimo_pendente'] = 0;
		$resultado['nao_avaliados'] = 0;
		$resultado['total'] = count($lista_servicos);
		
		foreach($lista_servicos as $key => $campo) {
			if($campo['PropostaCredExame']['aceito'] == '1') {
				$resultado['aceitos']++;
			} else if($campo['PropostaCredExame']['aceito'] == '0') {
				$resultado['nao_aceitos']++;
			} else if(!is_null($campo['PropostaCredExame']['valor_minimo']) && is_null($campo['PropostaCredExame']['aceito'])) {
				$resultado['valor_minimo_pendente']++;
			} else {
				$resultado['nao_avaliados']++;
			}
		}
		
		if(($resultado['aceitos'] + $resultado['nao_aceitos']) == $resultado['total']) {
			$array_atualiza['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] = StatusPropostaCred::VALORES_APROVADOS;	
		} else if($resultado['valor_minimo_pendente'] >= 1) {
			$array_atualiza['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] = StatusPropostaCred::AGUARDANDO_RETORNO_CONTRA_PROPOSTA;
		}
		$array_atualiza['PropostaCredenciamento']['codigo'] = $this->data['PropostaCredenciamento']['codigo'];
		$this->PropostaCredenciamento->atualizar($array_atualiza);
		$this->redirect($_SERVER['HTTP_REFERER']);
	}
	
	
    /**
     * Atualiza Status do Exame! Após analisado Valor
     * 
     * NULL - Nao analisado 
     * 0 - Reprovado 
     * 1 - Aprovado
     * 
     * @author: Danilo Borges Pereira
     * <daniloborgespereira@gmail.com>
     */	
	public function valida_valor_minimo() {
		$valor_minimo = (isset($this->params['form']['valor']) && $this->params['form']['valor']) ? $this->params['form']['valor'] : NULL;
		
		if($this->params['form']['status'] == 'null') {
			$this->params['form']['status'] = null;
		}
		
		echo ($this->PropostaCredExame->atualizar(array('PropostaCredExame' => array('codigo' => $this->params['form']['codigo'], 'aceito' => $this->params['form']['status'], 'valor_minimo' => $valor_minimo)))) ? '1' : '0';
		exit;
	}
	
	public function atualiza_valor_minimo() {
		
		$valor_minimo = (isset($this->params['form']['valor']) && $this->params['form']['valor']) ? $this->params['form']['valor'] : NULL;
	
		if($valor_minimo) {
			
			echo ($this->PropostaCredExame->atualizar(array('PropostaCredExame' => array('codigo' => $this->params['form']['codigo'], 'aceito' => NULL, 'valor_minimo' => $valor_minimo)))) ? '1' : '0';
			exit;
			
		} else {
			print "0";
			exit;
		}
	
	}	
	
	public function atualiza_status_renegocia_valor_minimo() {
		$id_proposta = $this->params['form']['codigo'];
		
		$proposta_info = $this->PropostaCredenciamento->read(null, $id_proposta);
		if(! $this->PropostaCredenciamento->atualizarStatus($proposta_info, StatusPropostaCred::RENEGOCIAR_VALOR_MINIMO) ) {
			print "1";
		} else {
			print "0";
		}
		exit;
	} 
	
	public function atualiza_status_valor_minimo_renegociado() {
		$id_proposta = $this->params['form']['codigo'];
	
		$proposta_info = $this->PropostaCredenciamento->read(null, $id_proposta);
		
		if(! $this->PropostaCredenciamento->atualizarStatus($proposta_info, StatusPropostaCred::VALOR_MINIMO_NEGOCIADO) ) {
			print "1";
		} else {
			print "0";
		}
		exit;
	}	
	
	
	/**
	 * Ação volta o valor validado para valor minimo de servico
     * @author: Danilo Borges Pereira
     * <daniloborgespereira@gmail.com>	 
	 */
	public function voltar_valida_valor_minimo() {
		echo ($this->PropostaCredExame->atualizar(array('PropostaCredExame' => array('codigo' => $this->params['form']['codigo'], 'aceito' => null)))) ? '1' : '0';
		exit;
	}	
	
		
    /**
     * Ação que salva os enderecos (matriz e filial) da prospota, em novos fornecedores.
     * Após serem atualizados p/ Status: Aprovado!
     * 
     * @author: Danilo Borges Pereira
     * <daniloborgespereira@gmail.com>
     */
	public function _salvaFornecedor($codigo, $with_transaction = true ) {
		
		$this->Fornecedor->with_transaction = $with_transaction;
		// retorna info proposta
		$this->data = $this->PropostaCredenciamento->read(null, $codigo);
		
		// retorna info endereco da proposta
        $endereco_info = $this->PropostaCredEndereco->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo), 'fields' => array('*')));
        foreach($endereco_info as $key => $endereco) {
        	
	        $fornecedor[$key] = array(
		    	'Fornecedor' => array(
		            'codigo_documento' => ($key == 0) ? $this->data['PropostaCredenciamento']['codigo_documento'] : $endereco['PropostaCredEndereco']['codigo_documento'],
		            'ativo' => 1,
					'razao_social' => trim($this->data['PropostaCredenciamento']['razao_social']),
	        		'nome' => $this->data['PropostaCredenciamento']['nome_fantasia'],
					'responsavel_administrativo' => trim($this->data['PropostaCredenciamento']['responsavel_administrativo']),
					'tipo_atendimento' => $this->data['PropostaCredenciamento']['tipo_atendimento'],
					'acesso_portal' => $this->data['PropostaCredenciamento']['acesso_portal'],
					'exames_local_unico' => $this->data['PropostaCredenciamento']['exames_local_unico'],
					'numero_banco' => $this->data['PropostaCredenciamento']['numero_banco'],
					'tipo_conta' => $this->data['PropostaCredenciamento']['tipo_conta'],
					'favorecido' => trim($this->data['PropostaCredenciamento']['favorecido']),
					'agencia' => trim($this->data['PropostaCredenciamento']['agencia']),
					'numero_conta' => trim($this->data['PropostaCredenciamento']['numero_conta']),
					'interno' => NULL,
					'data_contratacao' => date('d/m/Y'),
					'data_cancelamento' => NULL,
					'contrato_ativo' => 1,
					'codigo_soc' => NULL,
					'dia_do_pagamento' => NULL,
					'disponivel_para_todas_as_empresas' => NULL,
					'especialidades' => NULL,
					'tipo_de_pagamento' => NULL,
					'responsavel_tecnico' => trim($this->data['PropostaCredenciamento']['responsavel_tecnico_nome']),
					'codigo_conselho_profissional' => trim($this->data['PropostaCredenciamento']['codigo_conselho_profissional']),
					'responsavel_tecnico_conselho_numero' => trim($this->data['PropostaCredenciamento']['responsavel_tecnico_numero_conselho']),
					'responsavel_tecnico_conselho_uf' => trim($this->data['PropostaCredenciamento']['responsavel_tecnico_conselho_uf']),
					'texto_livre' => NULL
		        ),
			    'FornecedorEndereco' => array(
		            'numero' => $endereco['PropostaCredEndereco']['numero'],
		            'complemento' => $endereco['PropostaCredEndereco']['complemento'],
		            'estado' => $endereco['PropostaCredEndereco']['estado'],
		            'cidade' => $endereco['PropostaCredEndereco']['cidade'],
		            'bairro' => $endereco['PropostaCredEndereco']['bairro'],
		            'logradouro' => $endereco['PropostaCredEndereco']['logradouro'],
		            'cep' => $endereco['PropostaCredEndereco']['cep']
		        ),
		        'Matriz' => $endereco['PropostaCredEndereco']['matriz']       			
	        );	        
        }
        
        try {
			
			$lista_horarios = $this->Horario->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo)));
			$lista_medicos = $this->PropostaCredMedico->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo)));
			$lista_fotos = $this->PropostaCredFoto->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo)));
			$lista_documentos = $this->PropostaCredDocumento->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo, 'validado' => '1')));
			
            if( $with_transaction ) $this->Fornecedor->query('begin transaction');
            
            $filial = array();
            
            foreach($fornecedor as $key => $unidade) {
            	
            	$unidade['FornecedorEndereco']['estado_descricao'] = $unidade['FornecedorEndereco']['estado'];
            	unset( $unidade['FornecedorEndereco']['estado'] );
		        if(!$this->Fornecedor->incluir($unidade)) {
		        	throw new Exception('Não gravou o fornecedor!');
		        } else {
		        	
		        	// retorna informacoes do usuario
		        	$infoUsuario = $this->Usuario->find('first', array('conditions' => array('codigo_proposta_credenciamento' => $codigo), 'fields' => array('codigo')));
		        	
		        	if(! $this->Usuario->atualizar(array('Usuario' => array('codigo' => $infoUsuario['Usuario']['codigo'], 'codigo_fornecedor' => $this->Fornecedor->id, 'codigo_uperfil' => Uperfil::PRESTADOR)))) {
		        		throw new Exception('Não gravou o fornecedor na tabela de usuario!');
		        	}
		        }
		        
				if($unidade['Matriz'] == 1)
	        		$matriz[] = $this->Fornecedor->id;
	        	else
	        		$filial[] = $this->Fornecedor->id;
        		if(!$this->_salvaListaPrecoProposta($codigo, $this->Fornecedor->id, $this->data['PropostaCredenciamento']['razao_social'])) {
        			throw new Exception('Não gravou Lista de Preço para o Fornecedor!');
        		}
	        		
	        	// transfere ao forncedor os arquivos já enviados e validados na proposta de credenciamento
	        	foreach($lista_documentos as $chave =>$campo) {
	        		
	        		if(! $this->FornecedorDocumento->incluir(
	        			array(
	        				'FornecedorDocumento' => array(
			        			'codigo_fornecedor' => $this->Fornecedor->id,
			        			'codigo_tipo_documento' => $campo['PropostaCredDocumento']['codigo_tipo_documento'],
			        			'caminho_arquivo' => $campo['PropostaCredDocumento']['caminho_arquivo'],
			        			'validado' => '1',
								'data_validade' => $campo['PropostaCredDocumento']['data_validade'],
	        				)
	        			)
	        		)) {
	        			throw new Exception('Não gravou o documento noi fornecedor na tabela de usuario!');
	        		}
	        	}
	        	
	        	// transfere ao forncedor os horario de atendimento definidos na proposta de credenciamento
				foreach($lista_horarios as $chave => $campo) {
					foreach($campo as $key => $item) {
						unset($item['codigo_proposta_credenciamento']);
						$item['codigo_fornecedor'] = $this->Fornecedor->id;
						$this->FornecedorHorario->incluir(array('FornecedorHorario' => $item));	
					}
				}
				
				// tranferido ao forncedor os medicos cadastrados na proposta de credenciamento
            	foreach($lista_medicos as $chave => $campo) {
					unset($campo['PropostaCredMedico']['codigo_proposta_credenciamento']);
					$campo['PropostaCredMedico']['codigo_fornecedor'] = $this->Fornecedor->id;
					            			
					$this->FornecedorMedico->incluir(array('FornecedorMedico' => $campo['PropostaCredMedico']));
				}
				
            	foreach($lista_fotos as $chave => $campo) {
					unset($item['PropostaCredFoto']['codigo_proposta_credenciamento']);
					$item['PropostaCredFoto']['codigo_fornecedor'] = $this->Fornecedor->id;
					            	    	
					$this->FornecedorFoto->incluir(array('FornecedorFoto' => $item['PropostaCredFoto']));	
				}				
		        $array_contato = array();
				if(trim($this->data['PropostaCredenciamento']['telefone']) != '') {
			        $array_contato[] = array(
		        		'FornecedorContato' => array(
			        		'codigo_fornecedor' => $this->Fornecedor->id,
			        		'codigo_tipo_contato' => 6, #tipo: representante
			        		'nome' => $this->data['PropostaCredenciamento']['responsavel_administrativo'],
			        		'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo'],
			        		'codigo_tipo_retorno' => 1, #telefone
			        		'descricao' => trim($this->data['PropostaCredenciamento']['telefone'])
		        		)
		        	);
		        }
	        	
	        	if(trim($this->data['PropostaCredenciamento']['email']) != '') {
					foreach(explode(";", $this->data['PropostaCredenciamento']['email']) as $key => $email) {
						$array_contato[] = array(
							'FornecedorContato' => array(
								'codigo_fornecedor' => $this->Fornecedor->id,
								'codigo_tipo_contato' => 6, #tipo: representante
								'nome' => $this->data['PropostaCredenciamento']['responsavel_administrativo'],
								'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo'],
								'codigo_tipo_retorno' => 2, #email
								'descricao' => trim($email)
							)
						);						
					}	        		
	        	}	
	
	        	if(trim($this->data['PropostaCredenciamento']['celular']) != '') {
		        	$array_contato[] = array(
		        		'FornecedorContato' => array(
			        		'codigo_fornecedor' => $this->Fornecedor->id,
			        		'codigo_tipo_contato' => 6, #tipo: representante
			        		'nome' => $this->data['PropostaCredenciamento']['responsavel_administrativo'],
			        		'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo'],
			        		'codigo_tipo_retorno' => 7, #celular
			        		'descricao' => trim($this->data['PropostaCredenciamento']['celular'])        	        	
		        		)       	
		        	);
	        	}
	        	
            	if(trim($this->data['PropostaCredenciamento']['fax']) != '') {
		        	$array_contato[] = array(
		        		'FornecedorContato' => array(
			        		'codigo_fornecedor' => $this->Fornecedor->id,
			        		'codigo_tipo_contato' => 6, #tipo: representante
			        		'nome' => $this->data['PropostaCredenciamento']['responsavel_administrativo'],
			        		'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo'],
			        		'codigo_tipo_retorno' => 3, #celular
			        		'descricao' => trim($this->data['PropostaCredenciamento']['fax'])        	        	
		        		)       	
		        	);
	        	}	        	
	        	
               	foreach($array_contato as $campo) {
					if(!$this->FornecedorContato->incluir($campo)) {
						throw new Exception();
					}
        		}
            }
            
			// verifica se existe matriz e filial            
            if(count($matriz) && count($filial)) {
            	foreach($matriz as $id_matriz) {
            		foreach($filial as $id_filial) {
            			
            			// insere matriz filial
						$this->FornecedorUnidade->incluir(array(
            				'FornecedorUnidade' => array(
	            				'codigo_fornecedor_matriz' => $id_matriz,
	            				'codigo_fornecedor_unidade' => $id_filial,
	            				'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo']            			
            				)
            			));
            		}
            	}
            }
            
			if( $with_transaction ) $this->Fornecedor->query('commit');
			return array('matriz' => $matriz, 'filial' => $filial);
		} catch (Exception $e) {
			if( $with_transaction ) $this->Fornecedor->query('rollback');
			return false;
		}
	}
	
	/**
	 * Função cria (login e senha) e envia p/ e-mail do credenciado!
     * 
     * @author: Danilo Borges Pereira
     * <daniloborgespereira@gmail.com>
	 */
	
	public function _enviaSenha($dados, $codigo) {
		
		
		// verifica se usuario ja existe
		if(! $this->Usuario->find('first', array('conditions' => array('codigo_proposta_credenciamento' => $codigo)))) {
			
			$dados_user['Usuario']['senha'] = str_pad ( ( string ) mt_rand ( 0, 999999 ), 6, '0', STR_PAD_LEFT );
			$dados_user['Usuario']['nome'] = trim($dados['responsavel_administrativo']) ? $dados['responsavel_administrativo'] : $dados['nome_fantasia'];
			
			$apelido = !empty($dados['email']) ? explode('@', $dados['email']) : explode(' ', $dados['responsavel_administrativo']);
			
			$dados_user['Usuario']['apelido'] = $apelido[0] . "." .  $codigo;
			$dados_user['Usuario']['email'] = $dados['email'];
			$dados_user['Usuario']['ativo'] = true;
			$dados_user['Usuario']['codigo_uperfil'] = Uperfil::CREDENCIANDO;
			$dados_user['Usuario']['codigo_departamento'] = 1;
			$dados_user['Usuario']['codigo_proposta_credenciamento'] = $codigo;
			$dados_user['Usuario']['codigo_usuario_inclusao'] = isset($this->authUsuario['Usuario']['codigo']) ? $this->authUsuario['Usuario']['codigo'] : 0;
			
			try {
	            $this->Usuario->query('BEGIN TRANSACTION');
	            
	            // pega somente o primeiro email cadastrado no campo email
	            $dados_user['Usuario']['email'] = explode(";", $dados_user['Usuario']['email']);
	            $dados_user['Usuario']['email'] = trim(current($dados_user['Usuario']['email']));
	            
	            if ($this->Usuario->incluir($dados_user)) {
					$this->PropostaCredenciamento->disparaEmail(($dados + $dados_user), 'Dados de Acesso ao Portal RHhealth','envio_usuario_senha_email',$dados['email'],$codigo);	            	
	            }
				$this->Usuario->commit();
				return true;
				
			} catch(Exception $e) {
				// $this->log($e->getMessage(),'debug');
				$this->Usuario->rollback();
				return false;
			}
		} else {
			return true;
		}		
	}
	
	/**
	 * Grava a Contra Proposta e dispara e-mail p/ Credenciando!
     * 
     * @author: Danilo Borges Pereira
     * <daniloborgespereira@gmail.com>
	 */
	public function _salvaContraProposta($codigo, $dados, $email) {
		unset($dados['acao']);
		$dadosProposta = $this->PropostaCredenciamento->read(null, $codigo);
		
		try {
            $this->PropostaCredExame->query('begin transaction');
            		
	        foreach($dados as $key => $campo) {
	        	if(isset($campo['valor_contra_proposta'])) {
	        		$this->PropostaCredExame->atualizar(array('PropostaCredExame' => $campo + array('codigo' => $key)));	        		
	        	}
	        }
	        
	        if($this->_enviaSenha($dadosProposta['PropostaCredenciamento'], $codigo)) {
	        	$this->PropostaCredenciamento->disparaEmail($dadosProposta, $dadosProposta['PropostaCredenciamento']['nome_fantasia'] . '. Temos uma contra-proposta para seus exames.', 'envio_contra_proposta', $email, null);
	        }
	        else {
	        	return false;
	        }
	        
			$this->PropostaCredExame->commit();
			return true;
		} catch(Exception $e) {
			// $this->log($e->getMessage(),'debug');
			$this->PropostaCredExame->rollback();
			return false;
		}
	}
	/**
	 * Ação chamada via Ajax (p/ verificar se CNPJ ja esta cadastrado na Base)
	 * 
	 * @author: Danilo Borges Pereira
	 * <daniloborgespereira@gmail.com>
	 */
    public function verifica_cnpj() {
    	
        $model_documento = & ClassRegistry::init('Documento');
        
        if($model_documento->isCNPJ($this->params['form']['cnpj']) == false) {
        	echo json_encode(array('resultado' => 0, 'valido' => 0));
        } else {
        	
	    	$options = array(
	    		'fields' => array('StatusPropostaCred.descricao', 'StatusPropostaCred.polaridade', 'PropostaCredenciamento.codigo', 'PropostaCredenciamento.codigo_status_proposta_credenciamento', 'PropostaCredenciamento.nome_fantasia'),
	    		'joins' => array(
	    			array(
						'table' => 'status_proposta_credenciamento',
						'alias' => 'StatusPropostaCred',
						'type' => 'INNER',
						'conditions' => array (
							'StatusPropostaCred.codigo = PropostaCredenciamento.codigo_status_proposta_credenciamento' 
						)    			
	    			)
				)
	    	);
	    	
	    	// tem codigo da proposta?
	    	if(!empty($this->params['form']['codigo'])) {
	    		$options['conditions'] = array(
	    			'PropostaCredenciamento.codigo_documento' => Comum::soNumero($this->params['form']['cnpj']),
	    			'PropostaCredenciamento.codigo <>' => base64_decode($this->params['form']['codigo']),
	    			'StatusPropostaCred.polaridade' => '1'
	    		);	
	    	} else {
	    		$options['conditions'] = array(
	    			'PropostaCredenciamento.codigo_documento' => Comum::soNumero($this->params['form']['cnpj']),
	    			'StatusPropostaCred.polaridade' => '1'
	    		);
	    	}
	    	
	    	// retorna info proposta
	    	$infoProposta = $this->PropostaCredenciamento->find('first', $options);
	    	
		    if(!$infoProposta) {
	    		echo json_encode(array('resultado' => 0, 'valido' => 1));
	    	} else {
	    		
	    		echo json_encode(array(
		    		'resultado' => 1,
	    			'valido' => 1,
		    		'codigo' => base64_encode($infoProposta['PropostaCredenciamento']['codigo']),
		    		'codigo_status' => $infoProposta['PropostaCredenciamento']['codigo_status_proposta_credenciamento'],
	    			'nome_fantasia' => $infoProposta['PropostaCredenciamento']['nome_fantasia'],	
		    		'status_descricao' => $infoProposta['StatusPropostaCred']['descricao']
		    	));
	    	}
        }
    	exit;
    }
    
	/**
	 * Ação que exibe e controla as chamadas para todas as abas 
	 * proposta, na visão do fornecedor (credenciado).
	 * 
	 * @author: Danilo Borges Pereira
	 * <daniloborgespereira@gmail.com>
	 */
        
    public function minha_proposta($codigo_proposta = null, $aba = null) {
    	
    	if($this->authUsuario['Usuario']['codigo_proposta_credenciamento'] && ($this->authUsuario['Usuario']['codigo_proposta_credenciamento'] != $codigo_proposta)) {
    		$this->redirect(array('controller' => 'propostas_credenciamento', 'action' => 'minha_proposta', $this->authUsuario['Usuario']['codigo_proposta_credenciamento'], 'valores_exames'));
    	} else if(!$this->authUsuario['Usuario']['codigo_proposta_credenciamento']) {
    		$this->redirect('/');
    	}
    	
    	$this->set('tem_contra_proposta', $this->_temContraProposta($this->authUsuario['Usuario']['codigo_proposta_credenciamento']));
    	$this->set('aba', $aba ? $aba : 'documentacao');
    	$this->set('codigo_proposta_credenciamento', $this->authUsuario['Usuario']['codigo_proposta_credenciamento']);
    	$this->set('dados', $this->PropostaCredenciamento->read(null, $this->authUsuario['Usuario']['codigo_proposta_credenciamento']));
    	$this->set('motivos', $this->MotivoRecusa->find('list', array('conditions' => array('ativo' => '1'), 'fields' => array('codigo', 'descricao'))));
    }
    
	/**
	 * Função retorna se existe contra proposta ativa para a proposta 
	 * Util p/ actions: minha_proposta()
	 * 
	 * @author: Danilo Borges Pereira
	 */    
    public function _temContraProposta($codigo) {
        $tem_contra_proposta = 0;
    	$exames = $this->PropostaCredExame->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo)));
    	foreach($exames as $campo) {
    		if(!($tem_contra_proposta))
	    		$tem_contra_proposta = !is_null($campo['PropostaCredExame']['valor_contra_proposta']);
    	}    	
    }
    
	/**
	 * Ação busca codigo captcha em consulta de cnpj na receita e salva em cookie
	 * 
	 * @author: Danilo Borges Pereira
	 */    
    
    public function getcaptcha() {
    	
	    if(!ini_get('date.timezone')) 
	    	date_default_timezone_set('GMT');
		
		$cookieFile = COOKIE_LOCAL.session_id();
		
		// cria arquivo onde sera salva a sessão com a receita
		if(!file_exists($cookieFile)) {
			$file = fopen($cookieFile, 'w');
			fclose($file);
		}
			
		$ch = curl_init('http://www.receita.fazenda.gov.br/pessoajuridica/cnpj/cnpjreva/captcha/gerarCaptcha.asp');
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 5000);
		
		// não utilizar returntransfer , este script replica imagem captcha da receita
		$imgsource = curl_exec($ch);
		curl_close($ch);
		if(!empty($imgsource)) {
			$img = @imagecreatefromstring($imgsource);
			header('Content-type: image/jpg');
			imagejpeg($img);
			imagedestroy($img);
		}
		
		$this->autoRender = false;
    }
    
	/**
	 * Ação (via ajax) retorna HTML da consulta de CNPJ na Receita e retorna array com informações da emprsa
	 * 
	 * @author: Danilo Borges Pereira
	 */
        
    public function retorno_receita() {
    	$this->autoRender = false;
    	$html = $this->_retorno_html_receita($this->params);
    	
    	if($html) {
    		$resultado = $this->_transforma_html_em_array($html);
    	} else {
    		$resultado = array('status' => 'CNPJ ou Imagem não encontrado, tente novamente!');
    	}
    	
    	echo json_encode($resultado);
    }
    
    /**
	 * Ação limpa cookies gerado na busca de cnpj na receita (via AJAX)
	 * 
	 * @author: Danilo Borges Pereira
     */
    
    public function limpa_cookie() {
    	$cookieFile = COOKIE_LOCAL . session_id();
    	
    	echo @unlink($cookieFile);
    	exit;
    }
    
	/**
	 * Ação transforma html de retorna da tela de informAção da empresa (CNPJ busca Receita)
	 * 
	 * @author: Danilo Borges Pereira
	 */
        
    public function _transforma_html_em_array($html) {
		// respostas que interessam
		$campos = array (
			'NÚMERO DE INSCRIÇÃO',
			'DATA DE ABERTURA',
			'NOME EMPRESARIAL',
			'TÍTULO DO ESTABELECIMENTO (NOME DE FANTASIA)',
			'CÓDIGO E DESCRIÇÃO DA ATIVIDADE ECONÔMICA PRINCIPAL',
			'CÓDIGO E DESCRIÇÃO DAS ATIVIDADES ECONÔMICAS SECUNDÁRIAS',
			'CÓDIGO E DESCRIÇÃO DA NATUREZA JURÍDICA',
			'LOGRADOURO',
			'NÚMERO',
			'COMPLEMENTO',
			'CEP',
			'BAIRRO/DISTRITO',
			'MUNICÍPIO',
			'UF',
			'ENDEREÇO ELETRÔNICO',
			'TELEFONE',
			'ENTE FEDERATIVO RESPONSÁVEL (EFR)',
			'SITUAÇÃO CADASTRAL',
			'DATA DA SITUAÇÃO CADASTRAL',
			'MOTIVO DE SITUAÇÃO CADASTRAL',
			'SITUAÇÃO ESPECIAL',
			'DATA DA SITUAÇÃO ESPECIAL'
		);
	
		// caracteres que devem ser eliminados da resposta
		$caract_especiais = array(
			chr(9),
			chr(10),
			chr(13),
			'&nbsp;',
			'</b>',
			'  ',
			'<b>MATRIZ<br>',
			'<b>FILIAL<br>'
		 );
			
			// prepara a resposta para extrair os dados
		$html = str_replace ( '<br><b>', '<b>', str_replace ( $caract_especiais, '', strip_tags ( $html, '<b><br>' ) ) );
		
		$html3 = $html;
		
		// faz a extrAção
		for($i = 0; $i < count($campos); $i++) {		
			$html2 = strstr($html,$campos[$i]);
			$resultado[] = trim($this->_pega_o_que_interessa($campos[$i].'<b>','<br>',$html2));
			$html = $html2;
		}
	
		// extrai os CNAEs secundarios , quando forem mais de um
		if(strstr($resultado[5],'<b>')) {
			$cnae_secundarios = explode('<b>',$resultado[5]);
			$resultado[5] = $cnae_secundarios;
			unset($cnae_secundarios);
		}
		
		// devolve STATUS da consulta correto
		if(!$resultado[0]) {
			if(strstr($html3, utf8_decode('O número do CNPJ não é válido')))
				$resultado['status'] = 'CNPJ incorreto ou não existe';
			else
				$resultado['status'] = 'Imagem digitada incorretamente';
		} else {
			$resultado['status'] = 'OK';
		}
		
		if(isset($resultado[10])) {
			$resultado[10] = Comum::soNumero($resultado[10]);
		}
		
		return $resultado;
    }
    
	// função para pegar o que interessa
	public function _pega_o_que_interessa($inicio,$fim,$total) {
		$interesse = str_replace($inicio, '', str_replace(strstr(strstr($total,$inicio),$fim),'',strstr($total,$inicio)));
		return($interesse);
	}    
    
	/**
	 * Ação transforma retorna html da tela de informação da consulta de empresa (CNPJ busca na Receita)
	 * 
	 * @author: Danilo Borges Pereira
	 */
    
    public function _retorno_html_receita($params) {
    	
	    $cookieFile = COOKIE_LOCAL.session_id();
		$cookieFile_fopen = HTTP_COOKIE_LOCAL.session_id();
		
	    if(!file_exists($cookieFile)) {
	        return false;
	    } else {
	    	
			// pega os dados de sessão gerados na visualizAção do captcha dentro do cookie
			$file = fopen($cookieFile, 'r');
			$conteudo = "";
			while (!feof($file)) {
				$conteudo .= fread($file, 1024);
			}
			fclose ($file);
	
			$explodir = explode(chr(9),$conteudo);
			
			$sessionName = trim($explodir[count($explodir)-2]);
			$sessionId = trim($explodir[count($explodir)-1]);
		}
		
		// dados que serão submetidos a consulta por post
	    $post = array (
			'submit1'						=> 'Consultar',
			'origem'						=> 'comprovante',
			'cnpj' 							=> Comum::soNumero($params['form']['cnpj']), 
			'txtTexto_captcha_serpro_gov_br'=> $params['form']['captcha'],
			'search_type'					=> 'cnpj'
	    );
	    
	    $post = http_build_query($post, NULL, '&');
		
		// prepara a variavel de session
		$cookie = $sessionName.'='.$sessionId;	
	
		$cookie_post = http_build_query(array ( 'flag' => 1, $sessionName => $sessionId ), NULL, '&');
		
	    $ch = curl_init('http://www.receita.fazenda.gov.br/pessoajuridica/cnpj/cnpjreva/valida.asp');
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);		// aqui estão os campos de formulário
	    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);	// dados do arquivo de cookie
	    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);	// dados do arquivo de cookie
	    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:8.0) Gecko/20100101 Firefox/8.0');
	    curl_setopt($ch, CURLOPT_COOKIE, $cookie_post);	    // dados de sessão e flag=1
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
	    // curl_setopt($ch, CURLOPT_REFERER, 'http://www.receita.fazenda.gov.br/pessoajuridica/cnpj/cnpjreva/Cnpjreva_Solicitacao2.asp?cnpj=' . $post['cnpj']);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 30000);
	    $html = curl_exec($ch);
	    
	    curl_close($ch);
	    return utf8_encode($html);
    }
    /**
     * [regerar_lista description]
     * 
     * regera a lista de preço do fornecedor
     * 
     * @param  [type] $codigo_proposta_credenciamento [description]
     * @param  [type] $codigo_fornecedor              [description]
     * @return [type]                                 [description]
     */
    public function regerar_lista($codigo_proposta_credenciamento,$codigo_fornecedor)
    {
    	//pega os dados do fornecedor
    	$fornecedor = $this->Fornecedor->find('first',array('conditions'=>array('codigo' => $codigo_fornecedor)));
    	$msg = "nao gerou";
    	if($this->_salvaListaPrecoProposta($codigo_proposta_credenciamento,$codigo_fornecedor,$fornecedor['Fornecedor']['razao_social'])) {
    		$msg = "gerou";
    	}
    	
    	print $msg;
    	exit;
    }//fim regerar_lista
    
	public function _salvaListaPrecoProposta($codigo_proposta_credenciamento, $codigo_fornecedor, $razao_social) {
		try {
			$this->ListaDePreco->query('begin transaction');
			
			$codigo_usuario_inclusao = $this->authUsuario['Usuario']['codigo'];
			$dados_lista_preco = array(
				'ListaDePreco' => array(
					'codigo_fornecedor' => $codigo_fornecedor, 
					'descricao' => 'Fornecedor: '.$razao_social, 
					'codigo_usuario_inclusao' => $codigo_usuario_inclusao
				)
			);
			
			if ($this->ListaDePreco->incluir($dados_lista_preco)) {
				
				//RECUPERA O ID DA LISTA DE PRECO CRIADA.	
				$codigo_lista_preco = $this->ListaDePreco->id;
				
				$lista_exames = $this->PropostaCredExame->find('all', array(
					'conditions' => array(
						'codigo_proposta_credenciamento' => $codigo_proposta_credenciamento, 
						'aceito' => '1',
						'ProdutoServico.codigo_produto' => 59 //exames complementares
					),
					'joins' => array(
						array(
							'table' => 'produto_servico',
							'alias' => 'ProdutoServico',
							'type' => 'INNER',
							'conditions' => array(
								'ProdutoServico.codigo_servico = PropostaCredExame.codigo_exame'
							)
						)
					),
					'fields' => array('*', 'ProdutoServico.codigo_produto')
				));
				
				$array_organizado = array();
				foreach($lista_exames as $key => $item) {
					$array_organizado[$item['ProdutoServico']['codigo_produto']][] = $item['PropostaCredExame'];
				}
				
				foreach($array_organizado as $key_tipo_produto => $produto) {
					
					$lista_preco_produto = array(
						'ListaDePrecoProduto' => array(
							'codigo_lista_de_preco' => $codigo_lista_preco,
							'codigo_produto' => $key_tipo_produto,
							'codigo_usuario_inclusao' => $codigo_usuario_inclusao,
							'valor_premio_minimo' => 0,
							'qtd_premio_minimo' => 0
						)
					);	
					$this->ListaDePrecoProduto->create();
					if($this->ListaDePrecoProduto->save($lista_preco_produto)) {
						$codigo_lista_de_preco_produto = $this->ListaDePrecoProduto->id;
						
						foreach($produto as $key => $servico) {
							
							$lista_preco_produto_servico = array(
								'ListaDePrecoProdutoServico' => array(
									'codigo_lista_de_preco_produto' => $codigo_lista_de_preco_produto,
									'codigo_servico' => $servico['codigo_exame'],
									'codigo_usuario_inclusao'=> $codigo_usuario_inclusao,
									'valor_premio_minimo' => 0,
									'qtd_premio_minimo' => 0,
								)
							);
							
							if($servico['valor_minimo'] && $servico['aceito'] == '1') {
								$lista_preco_produto_servico['ListaDePrecoProdutoServico']['valor'] = $servico['valor_minimo'];	
							} else if($servico['valor_contra_proposta'] && $servico['aceito'] == '1') {
								$lista_preco_produto_servico['ListaDePrecoProdutoServico']['valor'] = $servico['valor_contra_proposta'];
							} else if($servico['valor'] && $servico['aceito'] == '1') {
								$lista_preco_produto_servico['ListaDePrecoProdutoServico']['valor'] = $servico['valor'];								
							} else {
								$lista_preco_produto_servico['ListaDePrecoProdutoServico']['valor'] = NULL;
							}
							 
							$this->ListaDePrecoProdutoServico->create();
							if(!$this->ListaDePrecoProdutoServico->save($lista_preco_produto_servico)) {
								return false;
							};
						}
					} else {
						return false;
					}
				}
			}
				
			$this->ListaDePreco->commit();
			return true;
		} catch(Exception $e) {
			$this->ListaDePreco->rollback();
			return false;
		}
	}
	
    function buscar_codigo() {
        $this->layout = 'ajax_placeholder';
        $input_id = !empty($this->passedArgs['searcher']) ? $this->passedArgs['searcher'] : '';
        $input_display = !empty($this->passedArgs['display']) ? $this->passedArgs['display'] : $this->data['PropostaCredenciamento']['input_display'];
        $this->data['PropostaCredenciamento'] = $this->Filtros->controla_sessao($this->data, $this->PropostaCredenciamento->name);
        $this->set(compact('input_id','input_display'));
    }	
    
    function listagem_visualizar($destino) {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->PropostaCredenciamento->name);
        $conditions = $this->PropostaCredenciamento->converteFiltrosEmConditions($filtros);
        $this->paginate['PropostaCredenciamento'] = array(
            'recursive' => 1,
            'joins' => null,
            'conditions' => $conditions,
            'limit' => 10,
            'order' => 'PropostaCredenciamento.razao_social',
        );
        $propostas_credenciamento = $this->paginate('PropostaCredenciamento');
        $this->set(compact('propostas_credenciamento', 'destino'));
        
        if (isset($this->passedArgs['searcher']))
            $this->set('input_id', str_replace('-search', '', $this->passedArgs['searcher']));
            
        if (isset($this->passedArgs['display']))
            $this->set('input_display', str_replace('-search', '', $this->passedArgs['display']));
    }
    public function atualiza_status_contra_proposta() {
		$id_proposta = $this->params['form']['codigo'];
		
		$proposta_info = $this->PropostaCredenciamento->read(null, $id_proposta);
		if(! $this->PropostaCredenciamento->atualizarStatus($proposta_info, StatusPropostaCred::AGUARDANDO_AVALIACAO_CONTRA_PROPOSTA) ) {
			print "0";//ERRO
		} else {
			print "1";
		}
			
		exit;
	}  
	public function limpa_valor_minimo() {
		$codigo_proposta = $this->params['form']['codigo'];
		if($codigo_proposta) {	
			echo ($this->PropostaCredExame->atualizar(array('PropostaCredExame' => array('codigo' => $codigo_proposta, 'aceito' => NULL, 'valor_minimo' => NULL)))) ? '1' : '0';		
		} 
		else {
			echo "0";
		}
		exit;
	}

	private function get_produto_credenciamento($codigo){

		$this->loadModel("PropostaCredProduto");

		$joins = array(
    		array(
				'table' => 'propostas_credenciamento',
				'alias' => 'PropostaCredenciamento',
				'type' => 'INNER',
				'conditions' => array('PropostaCredenciamento.codigo = PropostaCredProduto.codigo_proposta_credenciamento')        			
        	),
    	);

    	$pro_cred_produto = $this->PropostaCredProduto->find('list', array('conditions' => array('codigo_proposta_credenciamento' => $codigo),  'fields' => array('codigo_produto'), 'joins' => $joins));

    	return $pro_cred_produto;
	}

	private function get_proposta_cred_exame($dados, $id_proposta){

		$fields = array(
			'PropostaCredExame.codigo',
			'PropostaCredExame.codigo_exame',
			'PropostaCredExame.valor',
			'PropostaCredExame.valor_contra_proposta',
			'PropostaCredExame.liberacao',
			'Servico.descricao',
			'Servico.tipo_servico'
		);

		$joins = array(
			array(
				'table' => 'servico',
				'alias' => 'Servico',
				'type' => 'INNER',
				'conditions' => array('Servico.codigo = PropostaCredExame.codigo_exame')        			
        	),
        	array(
				'table' => 'produto_servico',
				'alias' => 'ProdutoServico',
				'type' => 'LEFT',
				'conditions' => array('ProdutoServico.codigo_servico = Servico.codigo')        			
        	),
		);

		$conditions = array(
			'PropostaCredExame.codigo_proposta_credenciamento' => $id_proposta 
		);

		$codigo_produtos = implode(',',$dados);
		$conditions[] = array('ProdutoServico.codigo_produto IN ('.$codigo_produtos.')'	);	

		$resultado = $this->PropostaCredExame->find('all', array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields));

		return $resultado;
	}	   
}
?>
