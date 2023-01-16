<?php

App::import('Core', 'Sanitize');
App::import('Core', 'Validation');

class FornecedoresController extends AppController {
	public $name = 'Fornecedores';
	public $helpers = array('Ithealth');
	public $components = array('Filtros', 'RequestHandler','ExportCsv', 'Upload');
	var $uses = array(  'Fornecedor',
		'VEndereco',
		'FornecedorEndereco',
		'RhBanco',
		'ListaDePreco',
		'ListaDePrecoProduto',
		'FornecedorHorario',
		'FornecedorHorarioDiferenciado',
		'FornecedorMedico',
		'ConselhoProfissional',
		'EnderecoEstado',
		'FornecedorDocumento',
		'PropostaCredenciamento',
		'PropostaCredDocumento',
		'TipoDocumento',
		'Usuario',
		'Endereco',
		'EnderecoCidade',
		'FornecedorLog',
		'StatusAuditoriaExame',
		'StatusAuditoriaImagem',
		'AuditoriaExame',
		'AuditoriaExameLog',
		'AnexoFichaClinica',
        'AnexoFichaClinicaLog',
		'ClienteFornecedor',
		'ListaDePrecoProdutoServico',
		'NotaFiscalServico',
		'ItemPedidoExame',
		'FornecedorContato',
		'TempoLiberacaoServico',
		'AnexoExame',
        'AnexoExameLog',
        'Configuracao'
		);

	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array(
			'script_importacao', 
			'script_atualiza_latitude_e_longitude', 
			'importa_fornecedores_tiny', 
			'scriptImportaFornecedoresTiny',
			'listagem_log', 
			'ajax_get_por_codigo_cliente',
			'trata_erros',
			'obter_credenciado',
			'log_anexos',
			'upload_exame',
			'upload_ficha_clinica',
			'aprovacaoAuditoriaAnexoExame',
			'aprovacaoAuditoriaAnexoFichaClinica',
			'verificador_exame',
			'verificador_ficha',
		));
	}

	public function importa_fornecedores_tiny()
	{
		if($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {
			if($this->data['Fornecedor']['documento']['error'] == 0) {
				if($this->Fornecedor->scriptImportaFornecedoresTiny($this->data['Fornecedor']['documento'])) {
					$this->BSession->setFlash(array('alert alert-error', 'Falha no processamento, tente novamente.'));
				}
			} else {
				$this->BSession->setFlash(array('alert alert-error', 'Falha ao caregar arquivo, tente novamente.'));
			}		
		}
	}

	/**
	 * [retorna_estados description]
	 * 
	 * metodo para montar os registros do filtro com estado e cidade.
	 * 
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function retorna_estados($data){
		$estados = $this->EnderecoEstado->find('list', array('conditions' => array('codigo_endereco_pais' => '1'), 'fields' => array('abreviacao', 'abreviacao')));
		
		// if(isset($this->data['Fornecedor']['estado']) && $this->data['Fornecedor']['estado']) {
		// 	//codigo estado
		// 	$est = $this->EnderecoEstado->find('first', array('conditions' => array('abreviacao' => $this->data['Fornecedor']['estado'])));
		// 	//cidades
		// 	$cidades = array('' => 'Selecione o Estado Primeiro') + $this->EnderecoCidade->find('list', array('conditions' => array('codigo_endereco_estado' => $est['EnderecoEstado']['codigo']), 'fields' => array('descricao', 'descricao'),'order' => 'descricao'));
		// } else {
		// 	$cidades = array('' => 'Selecione o Estado Primeiro');
		// }

		$this->set(compact('estados'));
	}

	function index() {
		
		$this->pageTitle = 'Prestadores';

		$this->data['Fornecedor'] = $this->Filtros->controla_sessao($this->data, $this->Fornecedor->name);
		$this->retorna_estados($this->data);
	}

	function listagem($destino=null) {
		
		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Fornecedor->name);
		$conditions = $this->Fornecedor->converteFiltroEmCondition($filtros,null);

		// $this->Fornecedor->virtualFields['qtd_docs_proposta'] = '
		// (SELECT count(*) 
		// FROM '.$this->PropostaCredDocumento->databaseTable.'.'.$this->PropostaCredDocumento->tableSchema.'.'.$this->PropostaCredDocumento->useTable.' AS [PropostaCredDocumento]
		// WHERE codigo_proposta_credenciamento = PropostaCredenciamento.codigo 
		// AND codigo_tipo_documento IN (
		// SELECT codigo 
		// FROM '.$this->TipoDocumento->databaseTable.'.'.$this->TipoDocumento->tableSchema.'.'.$this->TipoDocumento->useTable.' AS [TipoDocumento] 
		// WHERE status = 1 AND obrigatorio = 1
		// )
		// )';

		$this->Fornecedor->virtualFields['qtd_docs_proposta'] = '
			(	SELECT count(*)
				FROM ' . $this->Usuario->databaseTable.'.'.$this->Usuario->tableSchema.'.'.$this->Usuario->useTable . ' AS [Usuario]
				INNER JOIN '.$this->PropostaCredenciamento->databaseTable.'.'.$this->PropostaCredenciamento->tableSchema.'.'.$this->PropostaCredenciamento->useTable.' AS [PropostaCredenciamento]
					ON Usuario.codigo_proposta_credenciamento = PropostaCredenciamento.codigo
				INNER JOIN '.$this->PropostaCredDocumento->databaseTable.'.'.$this->PropostaCredDocumento->tableSchema.'.'.$this->PropostaCredDocumento->useTable.' AS [PropostaCredDocumento]
					on PropostaCredenciamento.codigo = PropostaCredDocumento.codigo_proposta_credenciamento
				WHERE Usuario.codigo_fornecedor = Fornecedor.codigo
					AND PropostaCredDocumento.codigo_tipo_documento IN (SELECT codigo 
																		FROM '.$this->TipoDocumento->databaseTable.'.'.$this->TipoDocumento->tableSchema.'.'.$this->TipoDocumento->useTable.' AS [TipoDocumento] 
																		WHERE status = 1 AND obrigatorio = 1)
			)';

		$fields = array(
			'Fornecedor.codigo',
			'Fornecedor.nome',
			'Fornecedor.cnes',
			'Fornecedor.razao_social',
			'Fornecedor.codigo_documento',
			'Fornecedor.ativo',
			'Fornecedor.codigo_status_contrato_fornecedor',
			//'PropostaCredenciamento.codigo',
			'Fornecedor.qtd_docs_proposta',
			'FornecedorEndereco.cidade',
			'FornecedorEndereco.bairro',
			'FornecedorEndereco.estado_descricao',
			);

		$joins  = array(
			// array(
			// 	'table' => $this->Usuario->databaseTable.'.'.$this->Usuario->tableSchema.'.'.$this->Usuario->useTable,
			// 	'alias' => 'Usuario',
			// 	'type' => 'LEFT',
			// 	'conditions' => 'Usuario.codigo_fornecedor = Fornecedor.codigo',
			// 	),
			// array(
			// 	'table' => $this->PropostaCredenciamento->databaseTable.'.'.$this->PropostaCredenciamento->tableSchema.'.'.$this->PropostaCredenciamento->useTable,
			// 	'alias' => 'PropostaCredenciamento',
			// 	'type' => 'LEFT',
			// 	'conditions' => 'PropostaCredenciamento.codigo = Usuario.codigo_proposta_credenciamento',
			// 	),
			array(
				'table' => $this->FornecedorEndereco->databaseTable.'.'.$this->FornecedorEndereco->tableSchema.'.'.$this->FornecedorEndereco->useTable,
				'alias' => 'FornecedorEndereco',
				'type' => 'LEFT',
				'conditions' => 'FornecedorEndereco.codigo_fornecedor = Fornecedor.codigo',
				)
			);  

		$order = array('Fornecedor.codigo DESC','Fornecedor.razao_social ASC');

		$this->paginate['Fornecedor'] = array(
			'fields' => $fields,
			'conditions' => $conditions,
			'joins' => $joins,
			'limit' => 50,
			'order' => $order,
			'recursive' => -1,
			);
		
        // pr($this->Fornecedor->find('sql', $this->paginate['Fornecedor']));

		$fornecedores = $this->paginate('Fornecedor');

		$docs_obrigatorios = $this->TipoDocumento->find('count', array('conditions' => array('status' => 1, 'obrigatorio' => 1)));

		$this->set(compact('fornecedores','destino', 'docs_obrigatorios'));
	}

	function incluir() {
		$this->pageTitle = 'Incluir Prestador';

		if($this->RequestHandler->isPost()) {
			if ($this->Fornecedor->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
				$codigo_fornecedor = $this->Fornecedor->id;
				$this->redirect(array('action' => 'editar', $codigo_fornecedor));
			} else {
				$this->trata_invalidation();
				$this->BSession->setFlash('save_error');
			}
		}
		$this->carrega_combos_formulario();
	}

	private function trata_invalidation() {
		$validationErrors = $this->Fornecedor->invalidFields();

		foreach( $validationErrors AS $index => $ve ){
			// identifica Model
			if( preg_match("/([a-z]+)\.([a-z]+)/i", $index, $m ) ){
				$model = $m[1];
				$input = $m[2];
				$this->$model->invalidate( $input, $ve );
				break;
			}  
		}

	}

	function carrega_combos_formulario() {
		//$enderecos = (!empty($this->data['VEndereco']['endereco_cep']) ? $this->VEndereco->listarParaComboPorCep($this->data['VEndereco']['endereco_cep']) : array());
		//$this->set(compact('enderecos'));

		$comum = new Comum;
		$estados = $comum->estados();
		array_unshift( $estados , "");
		$this->set(compact('estados'));		

	}

	function editar($codigo_fornecedor, $bloquear = false) {

		$this->pageTitle = 'Atualizar Prestador';		

		if($this->RequestHandler->isPost()) {

			ini_set('memory_limit', '-1');
			ini_set('max_execution_time', 300); // 5min

			//pr($this->data);exit;
			$erro_editar = 0;
			
			$sql = "select * from  fornecedores_contato where codigo_fornecedor = {$codigo_fornecedor}";
			$fornecedor_contatos = $this->FornecedorContato->query($sql);

			//$fornecedor_contatos = $this->FornecedorContato->find("all", array("conditions", array("FornecedorContato.codigo_fornecedor" => $codigo_fornecedor)));
				//pr($fornecedor_contatos);exit;

			if (!empty($fornecedor_contatos)) {
			
				foreach ($fornecedor_contatos as $key => $contato) {//Remove o checado de todos os contatos do fornecedor
				
					$atualizar_dados = array();

					$fornecedor_contatos[$key][0]['checado'] = 0;	
					unset($fornecedor_contatos[$key][0]['data_inclusao']);

					$atualizar_dados['FornecedorContato'] = $fornecedor_contatos[$key][0];

					if (! $this->FornecedorContato->atualizar($atualizar_dados)) {
						$erro_editar++;
					}				
				}
			}

			if(isset($this->data['FornecedorContato']['checado']) && !empty($this->data['FornecedorContato']['checado']) ){
				
				$fornecedor_contato_codigo = $this->data['FornecedorContato']['checado'];
				

					if (!empty($fornecedor_contato_codigo)) {

						foreach ($fornecedor_contato_codigo as $codigo => $on) { // Atibriu o checado true aos contatos que foram checados
						
							$fornecedor_contato = $this->FornecedorContato->find("first", array("conditions" => array("FornecedorContato.codigo" => $codigo)));
								
							$fornecedor_contato['FornecedorContato']['checado'] = 1;	
								
							$atualizar_dados['FornecedorContato'] = $fornecedor_contato['FornecedorContato'];
		
					
							if (! $this->FornecedorContato->atualizar($atualizar_dados)) {
								$erro_editar++;
							}				
						}			
		
						if ($erro_editar > 0) {
							$this->BSession->setFlash('save_error');
							$this->redirect(array('controller' => 'fornecedores', 'action' => 'editar', $codigo_fornecedor));
							return;
						}	
					}
					
										
			} 

		
			// flag se deve atualizar endereco
			$atualizaEndereco = isset($this->data['FornecedorEndereco']) && !empty($this->data['FornecedorEndereco']);

			if( !isset($this->data['Fornecedor']['faturamento_dias']) ){
				$this->data['Fornecedor']['faturamento_dias'] = null;
			}

			if(empty($this->data['Fornecedor']['faturamento_detalhes'])){
				$this->data['Fornecedor']['faturamento_detalhes'] = null;
			}


			if(empty($this->data['Fornecedor']['caminho_arquivo']['name'])){
				unset($this->data['Fornecedor']['caminho_arquivo']);
			}

			if(!empty($this->data['Fornecedor']['caminho_arquivo']['name'])){

				$post_params = isset($this->data['Fornecedor']['caminho_arquivo']) && !empty($this->data['Fornecedor']['caminho_arquivo']) ? $this->data['Fornecedor']['caminho_arquivo'] : null ;

				if(empty($post_params)){
	                $this->BSession->setFlash('save_error');
	                return;
	            }

	            $this->Upload->setOption('field_name', 'caminho_arquivo');            
	            $this->Upload->setOption('accept_extensions', array('pdf','jpg','jpeg', 'png'));
	            $this->Upload->setOption('accept_extensions_message', 'Arquivo inválido! Favor escolher arquivo Pdf, jpg, jpeg ou png');
	            $this->Upload->setOption('size_max', 5242880);
	            $this->Upload->setOption('size_max_message', 'Tamanho máximo excedido! Só é permitido arquivos de até 5MB');

	            $retorno = $this->Upload->fileServer($this->data['Fornecedor']);

	            // se ocorreu algum erro de comunicação com o fileserver
	            if (isset($retorno['error']) && !empty($retorno['error']) ){
	                $chave = key($retorno['error']);
	                $this->BSession->setFlash(array(MSGT_ERROR, $retorno['error'][$chave]));
	            } else {

	            	$nome_arquivo = $this->data['Fornecedor']['caminho_arquivo']['name'];

	                unset($this->data['Fornecedor']['caminho_arquivo']);

	                $this->data['Fornecedor']['caminho_arquivo'] = $retorno['data'][$nome_arquivo]['path'];

	                if(isset($this->data['FornecedorHorarioDiferenciado']['X'])) {
            			unset($this->data['FornecedorHorarioDiferenciado']['X']);
            		}
				}
			}// fim if caminho arquivo

			if($this->data['FornecedorHorario']['horario_atendimento_diferenciado'] == 0){
				unset($this->data['FornecedorHorarioDiferenciado']);
			}

			//variavel com os fields invalidos
			$invalidadeFieldsHDiferenciado = '';
			// verifica se é credenciado de saúde para funcionar 
			if(isset($this->data['FornecedorHorarioDiferenciado']) && count($this->data['FornecedorHorarioDiferenciado'])) {
				$periodoHorarioDiferenciado = array();

				//deletar todos os registros de horaio diferenciado
				//pega o codigo dofonecedor
				$codigo_fornecedor = $this->data['FornecedorHorarioDiferenciado'][0]['codigo_fornecedor'];
				$queryFHD = "DELETE FROM RHHealth.dbo.fornecedores_horario_diferenciado WHERE codigo_fornecedor = ".$codigo_fornecedor;

				$this->FornecedorHorarioDiferenciado->query($queryFHD);
				
		    	foreach($this->data['FornecedorHorarioDiferenciado'] as $key => $periodoHD) {

		    		if(!isset($this->data['FornecedorHorarioDiferenciado'][$key]['codigo_servico'])) {
		    			continue;
		    		}
		    		
    				$dias_selecionados = "";
    				
    				if(isset($this->data['FornecedorHorarioDiferenciado'][$key]['dias_semana'])) {
    					foreach($this->data['FornecedorHorarioDiferenciado'][$key]['dias_semana'] as $k => $dia) {
    						if($dia) {
    							$dias_selecionados .= $k . ",";
    						}
    					}    					
    				}

					if($dias_selecionados) {
    				    //verifica se tenho cadastro de um hr diferenciado
    				    $periodoHorarioDiferenciado = $this->FornecedorHorarioDiferenciado->find('first',array('conditions' => array('codigo_servico' => $this->data['FornecedorHorarioDiferenciado'][$key]['codigo_servico'],'codigo_fornecedor' => $this->data['FornecedorHorarioDiferenciado'][$key]['codigo_fornecedor'])));

    					$periodoHorarioDiferenciado['FornecedorHorarioDiferenciado']['dias_semana'] = substr($dias_selecionados,0,-1);
    				    $periodoHorarioDiferenciado['FornecedorHorarioDiferenciado']['de_hora'] = Comum::soNumero($this->data['FornecedorHorarioDiferenciado'][$key]['de_hora']);
    				    $periodoHorarioDiferenciado['FornecedorHorarioDiferenciado']['ate_hora'] = Comum::soNumero($this->data['FornecedorHorarioDiferenciado'][$key]['ate_hora']);
    				    $periodoHorarioDiferenciado['FornecedorHorarioDiferenciado']['codigo_fornecedor'] = $this->data['FornecedorHorarioDiferenciado'][$key]['codigo_fornecedor'];
    				    $periodoHorarioDiferenciado['FornecedorHorarioDiferenciado']['codigo_servico'] = $this->data['FornecedorHorarioDiferenciado'][$key]['codigo_servico'];
    				    $periodoHorarioDiferenciado['FornecedorHorarioDiferenciado']['ativo'] = 1;

    				    // debug($periodoHorarioDiferenciado);exit;
    				    
    				    //verifica se tem que atualizar ou incluir
    				    if(!isset($periodoHorarioDiferenciado['FornecedorHorarioDiferenciado']['codigo'])) {
    				    	//verifica se atualizou corretamente
	    				    if(!$this->FornecedorHorarioDiferenciado->incluir($periodoHorarioDiferenciado)) {
	    				    	$invalidadeFieldsHDiferenciado += $this->FornecedorHorarioDiferenciado->trata_erros('FornecedorHorarioDiferenciado', $this->FornecedorHorarioDiferenciado->validationErrors, $key);
	    				    }
    				    }
    				    else {
    				    	//verifica se atualizou corretamente
	    				    if(!$this->FornecedorHorarioDiferenciado->atualizar($periodoHorarioDiferenciado)) {
	    				    	$invalidadeFieldsHDiferenciado += $this->FornecedorHorarioDiferenciado->trata_erros('FornecedorHorarioDiferenciado', $this->FornecedorHorarioDiferenciado->validationErrors, $key);
	    				    }
    				    }//fim verificacao se existe codigo
					}
				}//fim foreach
			}

			if(!empty($invalidadeFieldsHDiferenciado)) {
				$this->FornecedorHorarioDiferenciado->validationErrors = $invalidadeFieldsHDiferenciado;
			}

			//tratando os servicos
			$params = $this->data['ListaDePrecoProdutoServico'];
			unset($this->data['ListaDePrecoProdutoServico']);

			//tratamento para individualizar cada servico por tipo de atendimento que o usuario queria escolher individualmente
			foreach($params as $key => $list) {
				if(!empty($list['tipo_atendimento']) AND $list['tipo_atendimento'] == 0 OR $list['tipo_atendimento'] == 1){					
					$this->ListaDePrecoProdutoServico->updateAll(array('ListaDePrecoProdutoServico.tipo_atendimento' => $list['tipo_atendimento']), array('ListaDePrecoProdutoServico.codigo' => $list['cod_list_prod_servico']));
				}
			}			

			$tempo_liberacao_servico = $this->data['TempoLiberacaoServico'];

			foreach ($tempo_liberacao_servico as $key => $dados) {

				$liberacao = $this->TempoLiberacaoServico->find("first", array("conditions" => array("codigo_servico" => $dados['codigo_servico'], 'codigo_fornecedor' => $codigo_fornecedor)));

				if (!empty($liberacao)) {

					$liberacao['TempoLiberacaoServico']['codigo_tempo_liberacao'] = $dados['codigo_tempo_liberacao'];

					$this->TempoLiberacaoServico->atualizar($liberacao);

				} else {

					$arr_liberacao['TempoLiberacaoServico'] = array(
						"codigo_tempo_liberacao" => $dados['codigo_tempo_liberacao'],
						"codigo_servico" => $dados['codigo_servico'],
						"codigo_fornecedor" => $codigo_fornecedor,
						"codigo_usuario_inclusao" => $_SESSION['Auth']['Usuario']['codigo']
					);

					if (!$this->TempoLiberacaoServico->incluir($arr_liberacao)) {					
						$this->BSession->setFlash('save_error');
						$this->redirect(array('controller' => 'fornecedores', 'action' => 'editar', $codigo_fornecedor, 'true'));
					}

				}
			}


			if ($this->Fornecedor->atualizar($this->data, $atualizaEndereco)) {				
				$this->BSession->setFlash('save_success');

				if ($bloquear == true) {
					$this->redirect(array('controller' => 'fornecedores', 'action' => 'editar', $codigo_fornecedor, 'true'));
				}else{
					$this->redirect(array('action' => 'index'));
				}
			} else {
				$this->trata_invalidation();
				$this->BSession->setFlash('save_error');
				$this->redirect(array('controller' => 'fornecedores', 'action' => 'editar', $codigo_fornecedor));
			}
		}
		
		if (empty($this->data)) {
			$this->data = $this->Fornecedor->carregarParaEdicao($codigo_fornecedor);
			// $this->data['Fornecedor']['data_contratacao'] = Comum::formataData($this->data['Fornecedor']['data_contratacao'], 'dmy' ,'ymd');
		}

		$lista_preco = $this->retornaListaPreco($codigo_fornecedor,null,null);//pega agora todo os produtos 		

		$saude = 0;
		$seguranca = 0;
		$dados_saude = array();
		foreach ($lista_preco as $key => $dados) {
				$saude++;

				$tempo_liberacao = $this->TempoLiberacaoServico->find("first", array("fields" => array("codigo_tempo_liberacao"), "conditions" => array("codigo_servico" => $dados['Servico']['codigo'], 'codigo_fornecedor' => $codigo_fornecedor)));
				
				//tratamento para a lista dos servicos
				$dados_saude[$dados['ListaDePrecoProduto']['codigo_produto']]['ListaDePrecoProdutoServico'][$key] = $dados['ListaDePrecoProdutoServico'];
				$dados_saude[$dados['ListaDePrecoProduto']['codigo_produto']]['Produto'] = $dados['Produto'];
				$dados_saude[$dados['ListaDePrecoProduto']['codigo_produto']]['ListaDePrecoProdutoServico'][$key]['Servico'] = $dados['Servico'];
				$dados_saude[$dados['ListaDePrecoProduto']['codigo_produto']]['ListaDePrecoProdutoServico'][$key]['TempoLiberacaoServico'] = $tempo_liberacao['TempoLiberacaoServico'];

				$this->set('dados_horario', $this->FornecedorHorario->find('all', array('conditions' => array('codigo_fornecedor' => $codigo_fornecedor))));

				$this->set('dados_corpo_clinico', $this->retornaCorpoClinico($codigo_fornecedor));

				$this->set('lista_conselhos', $this->ConselhoProfissional->find('list', array('fields' => array('codigo', 'descricao'))));
				$this->set('estados', $this->EnderecoEstado->find('list', array('conditions' => array('codigo_endereco_pais' => 1), 'fields' => array('abreviacao', 'descricao'))));
			//se vier os servicos de segurança, habilita a aba segurança	
			if($dados['Servico']['tipo_servico'] == 'G'){
				$seguranca++;
				$dados_seguranca[$key]['Servico'] = $dados['Servico'];
			}
		}

		//pr($dados_saude);
		$bancos = $this->RhBanco->find('list', array('fields' => array('codigo', 'banco_descricao'), 'order' => 'descricao'));

		$documentos_enviados = $this->FornecedorDocumento->retorna_documentos_enviados($codigo_fornecedor);
		$documentos_pendentes = $this->FornecedorDocumento->retorna_documentos_pendentes($codigo_fornecedor);
		
		$this->loadModel('FornecedorFoto');
		$fotos = $this->FornecedorFoto->find('all', array('conditions' => array('codigo_fornecedor' => $codigo_fornecedor)));

		$dias_pagamento = array(
            '10' => '10', 
            '20' => '20', 
            '30' => '30'
        );

        $servico_fornecedor = array();

        foreach ($lista_preco as $dado_saude) {
			# code...
			$servico_fornecedor[$dado_saude['Servico']['codigo']] = $dado_saude['Servico']['codigo'] ." - ". $dado_saude['Servico']['descricao'];
		}

		$fields_hdf = array(
			'FornecedorHorarioDiferenciado.codigo AS codigo',
			'FornecedorHorarioDiferenciado.codigo_fornecedor AS codigo_fornecedor',
			'FornecedorHorarioDiferenciado.codigo_servico AS codigo_servico',
			'FornecedorHorarioDiferenciado.de_hora AS de_hora',
			'FornecedorHorarioDiferenciado.ate_hora AS ate_hora',
			'FornecedorHorarioDiferenciado.dias_semana AS dias_semana',
		);

		//vai buscar os horarios diferenciado do fornecedor
		$fornecedor_hdf['FornecedorHorarioDiferenciado'] = $this->FornecedorHorarioDiferenciado->find('all', array('conditions' => array('codigo_fornecedor' => $codigo_fornecedor), 'fields' => $fields_hdf));

		$fornecedor_hDiferenciado = array();

		if(!empty($fornecedor_hdf)) {
			foreach($fornecedor_hdf['FornecedorHorarioDiferenciado'] as $fhd) {
				$fornecedor_hDiferenciado['FornecedorHorarioDiferenciado'][] = $fhd[0];
			}
		}

		if(!isset($this->data['Fornecedor']['ambulatorio'])) {
			$this->data['Fornecedor']['ambulatorio'] = '0';
		}
		else {
			if(empty($this->data['Fornecedor']['ambulatorio'])) {
				$this->data['Fornecedor']['ambulatorio'] = '0';
			}
		}

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

		if ($bloquear == true) {
			$bloquear = true;
		}
		$this->set('tempo_liberacao', $tempo_liberacao);
		$this->carrega_combos_formulario();
		$this->set(compact('codigo_fornecedor','bancos', 'saude', 'seguranca','dados_seguranca', 'dados_saude', 'documentos_enviados', 'documentos_pendentes', 'tipo_documento', 'fotos', 'dias_pagamento','servico_fornecedor','fornecedor_hDiferenciado', 'bloquear'));
	}

	function usuarios(){
		$this->pageTitle = 'Usuários por Fornecedor';
        //$this->carrega_combos();
		$this->data['Fornecedor'] = $this->Filtros->controla_sessao($this->data, $this->Fornecedor->name);
	}

	function buscar_codigo() {
		$this->layout = 'ajax_placeholder';
		$searcher = !empty($this->passedArgs['searcher']) ? $this->passedArgs['searcher'] : '';
		$display = !empty($this->passedArgs['display']) ? $this->passedArgs['display'] : $this->data['Fornecedor']['display'];

		$this->data['Fornecedor'] = $this->Filtros->controla_sessao($this->data, $this->Fornecedor->name);

		$this->set(compact('searcher','display'));
	}

	function listagem_visualizar($destino) {
		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Fornecedor->name);
		$usuario = $this->BAuth->user();

		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 300); // 5min

		//se ele tive cliente identificado no seu usuario as conditions serao diferentes para mostrar somente os prestadores do cliente
        if(!empty($usuario['Usuario']['codigo_cliente'])) {
			//codigo cliente vindo da sessao do usuario
			$codigo_cliente = $_SESSION['Auth']['Usuario']['codigo_cliente'];
			//conditions vindas do filtros
			$conditions = $this->ClienteFornecedor->converteFiltroClienteFornecedor($filtros);
			//tratamento para filtrar para o cliente da sessao
			$conditions[] = array('ClienteFornecedor.codigo_cliente' => $codigo_cliente);
		} else {
			//variavel implementada para identificar a tela fornecedores_codigo, a modal para poder buscar os prestadores ativos e inativos, chamado CDCT-179
			$conditions = $this->Fornecedor->converteFiltroEmCondition($filtros,$destino);			
		}

		if(!empty($usuario['Usuario']['codigo_cliente'])) {
			//fields
			$fields = array(
				'Fornecedor.codigo',
				'Fornecedor.razao_social',
				'Fornecedor.nome',
				'Fornecedor.codigo_documento',
				'ClienteFornecedor.codigo'
			);

			//joins
			$joins = array(
	            array(
	                'table' => 'fornecedores',
	                'alias' => 'Fornecedor',
	                'type' => 'LEFT',
	                'conditions' => 'ClienteFornecedor.codigo_fornecedor = Fornecedor.codigo',
	            )
        	);

        	$this->paginate['ClienteFornecedor'] = array(
				'recursive' => 1,
				'fields' => $fields,
				'joins' => $joins,
				'conditions' => $conditions,
				'limit' => 10,
				'order' => 'Fornecedor.razao_social',
			);

			$fornecedores = $this->paginate('ClienteFornecedor');
		} else {
			$this->paginate['Fornecedor'] = array(
				'recursive' => 1,
				'joins' => null,
				'conditions' => $conditions,
				'limit' => 10,
				'order' => 'Fornecedor.razao_social',
			);

			// pr($this->Fornecedor->find('sql', $this->paginate['Fornecedor']));

			$fornecedores = $this->paginate('Fornecedor');
		}

		// $query_teste = $this->ClienteFornecedor->find('sql', $this->paginate['ClienteFornecedor']);
		// debug($query_teste);exit;

		$this->set(compact('fornecedores', 'destino'));
		if (isset($this->passedArgs['searcher']))
			$this->set('input_id', str_replace('-search', '', $this->passedArgs['searcher']));
		if (isset($this->passedArgs['display']))
			$this->set('input_display', str_replace('-search', '', $this->passedArgs['display']));
	}

	function auto_completar() {
		$lista      = $this->Fornecedor->buscaFornecedorJson(strtoupper($_GET['term']),null,5);
		$retorno    = array();
		if($lista){
			foreach ($lista as $key => $fornecedor)
				$retorno[]  = array(
					'label' => $fornecedor['nome'], 
					'value' => $fornecedor['codigo']);
		}

		echo json_encode($retorno);
		exit;
	}

	function buscar($codigo) {
		$this->layout = 'ajax_placeholder';
		$retorno = $this->Fornecedor->carregar($codigo);
		echo json_encode($retorno);
		exit;
	}

	function script_importacao() {
		$this->Fornecedor->scriptImportaFornecedores();
	}

	function script_atualiza_latitude_e_longitude() {
		App::import('Component', 'Maplink');
		$this->Maplink   = new MaplinkComponent();
		
		// if(Ambiente::TIPO_MAPA == 1) {
            App::import('Component',array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();
        // }
        // else if(Ambiente::TIPO_MAPA == 2) {
        //     App::import('Component',array('ApiGeoPortal'));
        //     $this->ApiMaps = new ApiGeoPortalComponent();
        // }

        // retorna enderecos
		$lista_enderecos = $this->FornecedorEndereco->find(	'all' );

		try {

			$this->FornecedorEndereco->query('begin transaction');

			foreach($lista_enderecos as $key => $dados) {

				$end = $dados['FornecedorEndereco']['logradouro'] . ', ' . $dados['FornecedorEndereco']['numero'] . ' - ' . $dados['FornecedorEndereco']['bairro'] . ' - ' . $dados['FornecedorEndereco']['cidade'] . ' / ' . $dados['FornecedorEndereco']['estado_descricao'];
				list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco( $end );
				
				echo $end . "<br />";
				echo $latitude . " - " . $longitude . "<br /><br /><br />==============================================================<br /><br />";
				
				$dados['FornecedorEndereco']['latitude'] = $latitude;
				$dados['FornecedorEndereco']['longitude'] = $longitude;
				
				if(!empty($latitude) && !empty($longitude)) {
					if(!$this->FornecedorEndereco->atualizar($dados)) {
						debug($this->FornecedorEndereco->validationErrors); //show validationErrors
						debug($this->FornecedorEndereco->getDataSource()->getLog(false, false));
						
						exit('deu merda!');
					}					
				} else {
					echo "faltou lat e long";
					
					pr($dados);
					exit;
				}
			}

			$this->FornecedorEndereco->commit();
		} catch (Exception $e) {
			$this->FornecedorEndereco->rollback();
			pr($e); exit;
		}

		exit('script finalizado!');
	}

	function retornaListaPreco($codigo_fornecedor, $codigo_produto = null,$tipo_servico = null){

		$this->ListaDePreco->bindModel(array(
			'belongsTo' => array(
				'ListaDePrecoProduto' => array(
					'alias' => 'ListaDePrecoProduto',
					'foreignKey' => FALSE,
					'type' => 'LEFT',
					'conditions' => 'ListaDePreco.codigo = ListaDePrecoProduto.codigo_lista_de_preco'
					),
				'ListaDePrecoProdutoServico' => array(
					'alias' => 'ListaDePrecoProdutoServico',
					'foreignKey' => FALSE,
					'type' => 'LEFT',
					'conditions' => 'ListaDePrecoProduto.codigo = ListaDePrecoProdutoServico.codigo_lista_de_preco_produto'
					),
				'Servico' => array(
					'alias' => 'Servico',
					'foreignKey' => FALSE,
					'type' => 'LEFT',
					'conditions' => 'Servico.codigo = ListaDePrecoProdutoServico.codigo_servico'
					),
				'TempoLiberacaoServico' => array(
					'alias' => 'TempoLiberacaoServico',
					'foreignKey' => FALSE,
					'type' => 'LEFT',
					'conditions' => 'TempoLiberacaoServico.codigo_servico = Servico.codigo AND TempoLiberacaoServico.codigo_fornecedor = ListaDePreco.codigo_fornecedor'
					),

				'Produto' => array(
					'alias' => 'Produto',
					'foreignKey' => FALSE,
					'type' => 'LEFT',
					'conditions' => 'ListaDePrecoProduto.codigo_produto = Produto.codigo'
					),
				)
			));

		$conditions = array(
			'ListaDePreco.codigo_fornecedor' => $codigo_fornecedor,
		);

		$order = array('Produto.descricao','Servico.descricao');

		//verifica se o parametro esta sendo passado
		if(!is_null($codigo_produto)) {
			//seta o codigo do produto para filtrar
			$conditions['codigo_produto'] = $codigo_produto;
		}//fim if

		//verifica se o parametro esta sendo passado
		if(!is_null($tipo_servico)) {
			//seta o codigo do produto para filtrar
			$conditions['Servico.tipo_servico'] = $tipo_servico;
		}//fim if

		// debug($this->ListaDePreco->find('sql', array('conditions' => $conditions)));exit;
		$lista_preco = $this->ListaDePreco->find('all', array('conditions' => $conditions, 'order' => $order));

		return $lista_preco;
	}

	function retornaCorpoClinico($codigo_fornecedor){
		$this->FornecedorMedico->bindModel(array(
			'belongsTo' => array(
				'Medico' => array(
					'alias' => 'Medico',
					'foreignKey' => FALSE,
					'type' => 'LEFT',
					'conditions' => 'Medico.codigo = FornecedorMedico.codigo_medico'
					),
				'ConselhoProfissional' => array(
                   'alias' => 'ConselhoProfissional',
                   'foreignKey' => FALSE,
                   'type' => 'LEFT',
                   'conditions' => 'ConselhoProfissional.codigo = Medico.codigo_conselho_profissional'
               	)
				)
			));
		$corpo_clinico = $this->FornecedorMedico->find('all', array('conditions' => array('FornecedorMedico.codigo_fornecedor' => $codigo_fornecedor)));

		return $corpo_clinico;
	}

	/**
	 * [listagem_log_item description]
	 * 
	 * metodo para pegar os dados de alteração do fornecedor
	 * 
	 * @param  [type] $codigo_pedido      [description]
	 * @param  [type] $codigo_item_pedido [description]
	 * @return [type]                     [description]
	 */
	public function listagem_log($codigo_fornecedor)
	{
        //titulo da pagina
        $this->pageTitle = 'Log de Fornecedor';
        $this->layout = 'new_window';
        
        //campos
        $fields = array(
        	'FornecedorLog.codigo_fornecedor',
        	'FornecedorLog.codigo_documento',
        	'FornecedorLog.nome',
        	'FornecedorLog.razao_social',
        	'FornecedorLog.ativo',
        	'FornecedorLog.responsavel_administrativo',
        	'FornecedorLog.tipo_atendimento',
        	'FornecedorLog.numero_banco',
        	'FornecedorLog.favorecido',
        	'FornecedorLog.agencia',
        	'FornecedorLog.numero_conta',
        	'FornecedorLog.atendente',
        	'FornecedorLog.data_contratacao',
        	'FornecedorLog.data_cancelamento',
        	'FornecedorLog.codigo_soc',
        	'FornecedorLog.responsavel_tecnico',
        	'FornecedorLog.codigo_conselho_profissional',
        	'FornecedorLog.responsavel_tecnico_conselho_numero',
        	'FornecedorLog.responsavel_tecnico_conselho_uf',
        	'FornecedorLog.utiliza_sistema_agendamento',
        	'FornecedorLog.tipo_unidade',
        	'FornecedorLog.codigo_fornecedor_fiscal',
        	'FornecedorLog.codigo_documento_real',
        	'FornecedorLog.data_inclusao',
        	'FornecedorLog.acesso_portal',
        	'FornecedorLog.exames_local_unico',
        	'FornecedorLog.tipo_conta',
        	'FornecedorLog.interno',
        	'FornecedorLog.contrato_ativo',
        	'FornecedorLog.dia_do_pagamento',
        	'FornecedorLog.disponivel_para_todas_as_empresas',
        	'FornecedorLog.especialidades',
        	'FornecedorLog.tipo_de_pagamento',
        	'FornecedorLog.texto_livre',
        	'FornecedorLog.codigo_status_contrato_fornecedor',
        	'UsuarioInclusao.nome',
        	'FornecedorLog.data_alteracao',
        	'UsuarioAlteracao.nome',
        	'FornecedorLog.acao_sistema',
        	'Banco.codigo_banco',
        	'Banco.descricao');

        //relacionamentos
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.usuario',
                'alias' => 'UsuarioInclusao',
                'type' => 'LEFT',
                'conditions' => 'FornecedorLog.codigo_usuario_inclusao = UsuarioInclusao.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.usuario',
                'alias' => 'UsuarioAlteracao',
                'type' => 'LEFT',
                'conditions' => 'FornecedorLog.codigo_usuario_alteracao = UsuarioAlteracao.codigo',
            ),
            array(
            	'table' => 'Rhhealth.dbo.bancos',
                'alias' => 'Banco',
                'type' => 'LEFT',
                'conditions' => 'FornecedorLog.numero_banco = Banco.codigo',
            ),
        );

        //dados do log
        $dados = $this->FornecedorLog->find('all',array('fields' => $fields, 'conditions' => array('FornecedorLog.codigo_fornecedor' => $codigo_fornecedor), 'joins' => $joins));

        //tipos de acoes
        $acoes = array('0' => "Inclusão", "1" => "Atualização", "2" => "Exclusão");

        // pr($dados);exit;

        $this->set(compact('dados','codigo_fornecedor','acoes'));

    } //metodo para apresentar o log dos fornecedores


	public function _filtros( $thisData ){
		
			//seta os status
			$this->loadmodel('StatusAuditoriaImagem');
			$status = $this->StatusAuditoriaImagem->find('list',array('fields' => array('codigo','descricao')));
			$this->loadmodel('TipoExame');
			$tipos_exames = $this->TipoExame->find('list', array('order' => 'descricao', 'fields' =>array('codigo','descricao' )));

			$this->data['AuditoriaExame']['codigo_fornecedor'] = isset($thisData['AuditoriaExame']['codigo_fornecedor']) ? $thisData['AuditoriaExame']['codigo_fornecedor'] : null;
			$this->data['AuditoriaExame']['codigo_cliente'] = isset($thisData['AuditoriaExame']['codigo_cliente']) ? $thisData['AuditoriaExame']['codigo_cliente'] : null;

			//seta os tipos de periodos que tem
			$tipo_periodo = array(
				'B' => 'Baixa',
				'R' => 'Resultado',
			);

			$this->set(compact('status','meses','tipos_exames','tipo_periodo'));
	}

    /**
     * [auditoria_exames description]
     * 
     * 
     * metodo para criar os filtros da auditoria de exames
     * 
     * @return [type] [description]
     */
    public function auditoria_exames()
    {
		$this->layout = 'ithealth';

    	//titulo da pagia
    	$this->pageTitle = 'Auditoria Exames';

    	//filtros setados
		$filtros = $this->Filtros->controla_sessao($this->data, 'AuditoriaExame');
		if(empty($filtros['data_inicio'])) {
			$filtros['tipo_periodo'] = null;
			$filtros['data_inicio'] = '01/'.date('/m/Y');
			$filtros['data_fim'] = date('d/m/Y');
		}
		$this->data['AuditoriaExame'] = $filtros;

		$this->_filtros($this->data);		

    }//fim auditoria_exames

    /**
     * [auditoria_exames_listagem description]
     * 
     * metodo para listagem dos exames
     * 
     * @return [type] [description]
     */
    public function auditoria_exames_listagem( $export = false )
    {
    	//executado por ajax este metodo
    	$this->layout = 'ajax';
    	//filtra o resultado
		$filtros = $this->Filtros->controla_sessao($this->data, 'AuditoriaExame');	

		$dados = array();
		if (isset($filtros['nota_fiscal'])) {
			//gera a query para pegar os dados dos exames do fornecedor
			$query = $this->AuditoriaExame->auditoriaListagem($filtros);

			$query['order'] = array('AnexoExame.data_inclusao');
			$query['recursive'] = -1;

			//para quando acionar o botao de exportar os dados
			if($export) {
				$this->auditoria_exames_export($query);
			}
			
			//monta a query com paginacao
			$query['limit'] = 50;


			$this->paginate['ItemPedidoExame'] = $query;
			
			//executa com paginacao
			 $dados = $this->paginate('ItemPedidoExame');
			 
			//debug($this->Fornecedor->find('sql', $query));
			//debug($dados);
			//seta os dados para a listagem
		}	
	
	$this->set(compact('dados'));
		

    }//fim auditoria_exames_listagem



	public function auditoria_exames_export($query)
    {

        $dbo = $this->ItemPedidoExame->getDataSource();

		$host = Ambiente::getUrl()."/portal/";//url do host do ambiente

		$rawQueryData = $this->ItemPedidoExame->find('sql', $query);

        $dbo->results = $dbo->rawQuery($rawQueryData);

        ob_clean();

        //$relatorio_padrao_encoding =  'UTF-8';   // UTF funciona, mas exigiu conversão UTF pelo programa usado LibreOffice
        $relatorio_padrao_encoding =  'ISO-8859-1'; // conforme importação padrão sugerida no LibreOffice ISO-8859-1 funcionou bem para 
                                                    // Windows 1252/WinLatin 1 
                                                    // Windows 1250/WinLatin 2
                                                    // ISO-8859-15/EURO
                                                    // ISO-8859-14
                                                    // ASCII/Inglês Norte Americano
                                                    // Europa oriental ISO 8859-2
                                                    // Turco (ISO 8859-9)
                                                    // Turco (Windows-1254)
                                                    // Vietnamita (Windows-1258)
                                                    // Sistema, Caso o sistema operacional seja Português Brasil


        header('Content-Encoding: '.$relatorio_padrao_encoding);
        header("Content-Type: application/force-download;charset=".$relatorio_padrao_encoding);
        header('Content-Disposition: attachment; filename="auditoria_exames'.date('YmdHis').'.csv"');
        header('Pragma: no-cache');

        //cabecalho do arquivo
        echo Comum::converterEncodingPara('"Código Credenciado";"CNPJ Prestador";"Nome Fantasia";"Numero Nota Fiscal";"Nome Fantasia Cliente";"CNPJ Cliente";"Pedido Exame";"Exame";"Tipo Exame";"Data Realização Exame";"Data Baixa";"Usuário Baixa";"CPF Funcionário";"Nome Funcionário";"Data Auditoria";"Usuário Auditoria";"Data Consolidação";"Usuário Consolidação";"Tipo Usuário";"Anexo Exame";"Anexo Ficha Clínica";"Prestador Qualificado";"Valor";"Status";"Recebimento Físico";"Glosa Motivo";"Glosa Observação";"Liberar o anexo Exame";"Liberar o anexo da Ficha Clinica"', $relatorio_padrao_encoding)."\n";
        
        while ($value = $dbo->fetchRow()) {
			$linha  = '';

            $linha .= $value[0]['codigo_fornecedor'].';';
			$linha .= Comum::formatarDocumento($value[0]['fornecedor_cnpj']).';';
			$linha .= Comum::converterEncodingPara(trim($value[0]['fornecedor_nome']), $relatorio_padrao_encoding).';';
			$linha .= $value[0]['nota_fiscal'].';';
			$linha .= Comum::converterEncodingPara(trim($value[0]['cliente_nome']), $relatorio_padrao_encoding).';';
			$linha .= Comum::formatarDocumento($value[0]['cliente_cnpj']).';';
			$linha .= $value[0]['codigo_pedido_exame'].';';
			$linha .= Comum::converterEncodingPara(trim($value[0]['exame']), $relatorio_padrao_encoding).';';
			$linha .= Comum::converterEncodingPara(trim($value[0]['tipo_exame']), $relatorio_padrao_encoding).';';
			$linha .= Comum::formataData($value[0]['data_realizacao_exame'],'ymd','dmy').' ;';  
			$linha .= $value[0]['data_baixa'].';';
			$linha .= Comum::converterEncodingPara(trim($value[0]['usuario_baixa']), $relatorio_padrao_encoding).';';
			$linha .= Comum::formatarDocumento($value[0]['funcionario_cpf']).';';
			$linha .= Comum::converterEncodingPara(trim($value[0]['funcionario_nome']), $relatorio_padrao_encoding).';';

			if(isset($value[0]['codigo_status_auditoria_imagem']) && $value[0]['codigo_status_auditoria_imagem'] != 1) { 
				$linha .= Comum::formataData($value[0]['auditoria_data'],'mssql','dmy').' ;';
				if($value[0]['fornecedor_ambulatorio'] == 1 || $value[0]['fornecedor_prestador_p'] == 1){
					$linha .= Comum::converterEncodingPara(trim('Aprovação Automática'), $relatorio_padrao_encoding).';';
				}else{
					$linha .= Comum::converterEncodingPara(trim($value[0]['auditoria_usuario_nome']), $relatorio_padrao_encoding).';';
				}  
			} else {
				$linha .= '- ;';
				$linha .= '- ;';
			}

			$linha .= isset($value[0]['data_consolidacao'])     	? Comum::formataData($value[0]['data_consolidacao'],'mssql','dmy').';'     : '-;';
			$linha .= isset($value[0]['nome_usuario_consolidacao']) ? Comum::converterEncodingPara(trim($value[0]['nome_usuario_consolidacao']), $relatorio_padrao_encoding).';'     : '-;';


			
			$linha .= Comum::converterEncodingPara(trim($value[0]['tipo_usuario']), $relatorio_padrao_encoding).';';
			
			$temAnexoExame = (isset($value[0]['codigo_anexo_exame']) && !empty($value[0]['codigo_anexo_exame'])) ? 'Sim' : '-';
			$linha .= Comum::converterEncodingPara(trim($temAnexoExame), $relatorio_padrao_encoding).';';
			
			$temAnexoFichaClinica = (isset($value[0]['codigo_anexo_ficha_clinica']) && !empty($value[0]['codigo_anexo_ficha_clinica'])) ? 'Sim' : '-';
			$prestadorQualificado = (isset($dado[0]['prestador_qualificado']) && !empty($dado[0]['prestador_qualificado'] ) && $dado[0]['prestador_qualificado'] == 1) 
				? 'Sim' 
				: (!empty($dado[0]['prestador_qualificado']) && $dado[0]['prestador_qualificado'] == 0 ? 'Não' 
				: '-');
                            
			$linha .= Comum::converterEncodingPara(trim($temAnexoFichaClinica), $relatorio_padrao_encoding).';';
			$linha .= Comum::converterEncodingPara(trim($prestadorQualificado), $relatorio_padrao_encoding).';';
			$linha .= 'R$ '.Comum::moeda($value[0]['valor']).';';
			$linha .= Comum::converterEncodingPara(trim($value[0]['status']), $relatorio_padrao_encoding).';';
			$linha .= Comum::converterEncodingPara(isset($value[0]['recebimento_fisico']) && $value[0]['recebimento_fisico'] == 1 ? 'Sim' : 
					(isset($value[0]['recebimento_fisico']) && $value[0]['recebimento_fisico'] == 0 ? 'Não' : '-'), $relatorio_padrao_encoding).';';
			
			$linha .= isset($value[0]['glosa_motivo'])     ? Comum::converterEncodingPara(trim($value[0]['glosa_motivo']), $relatorio_padrao_encoding).';'     : '- ;';
			
			$value[0]['glosa_observacao'] = preg_replace( "/\r|\n/", " ", $value[0]['glosa_observacao'] );
			$linha .= isset($value[0]['glosa_observacao']) ? Comum::converterEncodingPara(trim($value[0]['glosa_observacao']), $relatorio_padrao_encoding).';' : '- ;';
			$linha .= Comum::converterEncodingPara(trim($value[0]['libera_anexo_exame']), $relatorio_padrao_encoding).';';
			$linha .= Comum::converterEncodingPara(trim($value[0]['libera_anexo_ficha']), $relatorio_padrao_encoding).';';

			$linha .= "\n";
            
            echo $linha;
            
        }
        die();
	}
	

    /**
     * modal_auditar
     * 
     * metodo para apresetar os dados do pedido de exame, exames com o valor para o auditor, 
     * 	onde irá tomar a decisão de auditar ou não o exame realizado pelo fornecedor
     * 
     * @param  int|string   $codigo_item_pedido_exame
     * @return responseJson              retorna json  
     */
   	public function modal_auditar($codigo_item_pedido_exame)
   	{
		$data = array();

   		//aplica o filtro
   		$filtros = array( 'codigo_item_pedido_exame' => $codigo_item_pedido_exame);

   		//pega os dados para apresentar na modal do pedido de exames
   		$dados_query = $this->AuditoriaExame->getDadosFornecedorExame($filtros);

   		//pega os dados do item
   		$dados = $this->Fornecedor->find('first',array('fields' => $dados_query['fields'],'joins'=>$dados_query['joins'],'conditions'=>$dados_query['conditions'],'recursive' => -1));

    	//verificando se o fornecedor é operacional, se sim ele irá listar notas fiscais da filial e da matriz(PC-2647 - Matheus Brum)
		$array_codigo_fornecedores = array();
		array_push($array_codigo_fornecedores,$dados[0]['codigo_fornecedor']);
		if($dados[0]['fornecedor_tipo_unidade'] == 'O'){
			//atribuindo o codigo do fornecedor matriz ao array
			array_push($array_codigo_fornecedores, $dados[0]['fornecedor_codigo_o']);
		}

		//pega os status da imagem da auditoria
		$this->loadModel('StatusAuditoriaImagem');
		$status_auditoria_imagem = $this->StatusAuditoriaImagem->find('list', array('fields' => array('StatusAuditoriaImagem.codigo','StatusAuditoriaImagem.descricao'),'conditions' => array('StatusAuditoriaImagem.codigo not in (5,6)')));
		

        $this->loadModel('TipoGlosas');
		$tipo_glosas = $this->TipoGlosas->find('all', array('fields' => array('TipoGlosas.codigo','TipoGlosas.descricao'),'order' => 'descricao','conditions'=> array('ativo'=>1,'codigo <>'=> 8)));

		$this->loadModel('Glosas');
		$glosas_dados = $this->Glosas->find('first', array('conditions' => array('Glosas.codigo_itens_pedidos_exames' => $codigo_item_pedido_exame,'Glosas.ativo' => 1),'order' => array('Glosas.codigo' => 'DESC')));

		$this->loadModel('MotivosAprovadoAjuste');
		$motivo_aprovado_ajuste = $this->MotivosAprovadoAjuste->find('all', array('fields' => array('MotivosAprovadoAjuste.codigo','MotivosAprovadoAjuste.descricao'),'order' => 'descricao','conditions'=> array('ativo'=>1)));


		//verificando se o fornecedor é operacional, se sim ele irá listar notas fiscais da filial e da matriz(PC-2647)
		//carregando notas fiscais relativas ao fornecedor
		$this->loadModel('NotaFiscalServico');
		$notas_fiscais = $this->NotaFiscalServico->find('list',array('fields' => array('NotaFiscalServico.codigo','NotaFiscalServico.numero_nota_fiscal'),'conditions' => array('NotaFiscalServico.codigo_fornecedor' => $array_codigo_fornecedores,'NotaFiscalServico.codigo_nota_fiscal_status'=> array(1,2))));


		if($glosas_dados && !empty($glosas_dados['Glosas']['codigo'])){
			
			$glosas = array( 
				'motivo_glosa'=>$glosas_dados['Glosas']['motivo_glosa'],
				'data_glosa'=> AppModel::dbDateToDate($glosas_dados['Glosas']['data_glosa']),
				'codigo_tipo_glosa'=>$glosas_dados['Glosas']['codigo_tipo_glosa'],
				'data_vencimento'=>AppModel::dbDateToDate($glosas_dados['Glosas']['data_vencimento']),
				'data_pagamento'=>AppModel::dbDateToDate($glosas_dados['Glosas']['data_pagamento']),
				'valor'=>$glosas_dados['Glosas']['valor']
			);
		} else {
			$glosas = array( 
				'motivo_glosa'=>'',
				'data_glosa'=>'',
				'codigo_tipo_glosa'=>'',
				'data_vencimento'=>'',
				'data_pagamento'=>'',
				'valor'=>''
			);
		}

		$data['dados']  = $dados;
		$data['status_auditoria']  = $status_auditoria_imagem;
		$data['codigo_item_pedido_exame']  = $codigo_item_pedido_exame;
		$data['tipo_glosas']  = $tipo_glosas;
		$data['motivo_aprovado_ajuste'] = $motivo_aprovado_ajuste;
		$data['glosas']  = $glosas;
		$data['notas_fiscais'] = $notas_fiscais;

		return $this->responseJson($data);
		// $this->set(compact('dados','status_auditoria','codigo_item_pedido_exame', 'tipo_glosas', 'glosas','notas_fiscais'));
   	}//fim modal_auditar

   	/**
   	 * [salvar_auditoria description]
   	 * 
   	 * metodo para gravar os dados da auditoria
   	 * 
   	 * @return [type] [description]
   	 */
   	public function salvar_auditoria()
   	{
		   
		$usuario = $this->BAuth->user();
		
   		//pega os dados passados
		$codigo_item_pedido_exame 		= $this->params['form']['codigo_item_pedido'];   
		$codigo_pedido_exame 			= $this->params['form']['codigo_pedido_exame'];
		$codigo_nota_fiscal_servico 	= $this->params['form']['codigo_nota_fiscal_servico'];
		$numero_nota_fiscal 			= $this->params['form']['numero_nota_fiscal'];
   		$status 						= $this->params['form']['status'];
		$motivo 						= $this->params['form']['motivo'];
		$motivo_auditoria 				= $this->params['form']['motivo_auditoria'];
		$fisico 						= $this->params['form']['fisico'];
		$exame_auditado 				= $this->params['form']['exame_auditado'];
		$ficha_auditada 				= $this->params['form']['ficha_auditada'];
		$motivo_aprovado_ajuste 		= $this->params['form']['motivo_aprovado_ajuste'];
		$libera_anexo_exame 			= isset($this->params['form']['libera_anexo_exame']) ? $this->params['form']['libera_anexo_exame'] : null;
		$libera_anexo_ficha 			= isset($this->params['form']['libera_anexo_ficha']) ? $this->params['form']['libera_anexo_ficha'] : null;
		$codigo_ficha_clinica 			= isset($this->params['form']['codigo_ficha_clinica']) ? $this->params['form']['codigo_ficha_clinica'] : null;
		
		//pega os dados, gera a query para pegar os dados dos exames do fornecedor
		$filtros = array('codigo_item_pedido_exame' => $codigo_item_pedido_exame);
		$dados_query = $this->AuditoriaExame->getDadosFornecedorExame($filtros);
		//executa a query pegando o primeiro valor
		$dados_auditoria = $this->Fornecedor->find('first',array('fields' => $dados_query['fields'],'joins'=>$dados_query['joins'],'conditions'=>$dados_query['conditions'],'recursive' => -1));
		$exame = $dados_auditoria[0]['codigo_exame'];

		$codigo_fornecedor 	= $dados_auditoria[0]['codigo_fornecedor'];
		$codigo_glosa 	   	= null;
		$codigo_tipo_glosa 	= $this->params['form']['codigo_tipo_glosa'];
		$data_glosa 		= date("Y-m-d");
		$valor 				= isset($this->params['form']['valor']) ? $this->params['form']['valor'] : null ;
		
		/*Este trecho é para validar se o aso está com o exame e a ficha clinica anexados, caso 1 dos dois esteja 
		incorreto, ele muda o status automaticamente para aprovado parcial, além de validar no tabela de anexos que ele foi auditado*/
		if($status == 3 || $status == 4){
			$dados = $this->aprovacaoAuditoriaAnexoExame($codigo_item_pedido_exame);
			$aprovado_exame = $dados['retorno'] == true ? true : false;
			
			if ($exame == $this->Configuracao->getChave('INSERE_EXAME_CLINICO')) {
				$dados = $this->aprovacaoAuditoriaAnexoFichaClinica($codigo_ficha_clinica);
				$aprovado_ficha_clinica = $dados['retorno'] == true ? true : false;
				if(!$aprovado_exame || !$aprovado_ficha_clinica){
					$status = 6;
				}
			}
		}

		//Este trecho de código é para confirmar a auditoria da imagem, caso tenha algum ajuste ou seja aprovado parcialmente
		if($exame_auditado == 'true'){
			$dados = $this->aprovacaoAuditoriaAnexoExame($codigo_item_pedido_exame);
		}

		if($ficha_auditada == 'true'){
			$dados = $this->aprovacaoAuditoriaAnexoFichaClinica($codigo_ficha_clinica);
		}

		/* 
		se a imagem for liberada ou voltar para pendente e já existir uma glosa de imagem essa parte irá inativar a glosa existente
		(imaginando um cenário em que a auditoria saia do status 2 para o status 3 ou do status 2 para o 1)
		*/
		$this->loadmodel('Glosas');
		if($status == 3 || $status == 1 || $status == 4){
			$glosas = $this->Glosas->find('first', array('conditions' => array('Glosas.codigo_itens_pedidos_exames' => $codigo_item_pedido_exame, 'Glosas.ativo' => 1,'Glosas.codigo_classificacao_glosa' => 2)));
		
			$dados_glosa = array(
				'Glosas' => array(
					'ativo' => 0
				)
			);

			if(!empty($glosas)) {
				$codigo_glosa = $glosas['Glosas']['codigo'];
				$dados_glosa['Glosas']['codigo'] = $codigo_glosa;

				//atualiza os dados
				if(!$this->Glosas->atualizar($dados_glosa)) {
					$dados['retorno'] = false;
					$dados['mensagem'] = "Erro ao atualizar os dados de Glosa para auditoria de exames, favor entrar em contato com o administrador.";
					echo json_encode($dados);
					exit;		
				}
				
			} 
		}

		// se bloqueado gravar na tabela glosas
		if($status == 2){

			//verifica se existe este item na tabela de glosas 
			$glosas = $this->Glosas->find('first', array('conditions' => array('Glosas.codigo_itens_pedidos_exames' => $codigo_item_pedido_exame, 'Glosas.ativo' => 1,'Glosas.codigo_classificacao_glosa' => 2)));

			$dados_glosa = array(
				'Glosas' => array(
					'codigo_pedidos_exames' => $codigo_pedido_exame,
					'codigo_itens_pedidos_exames' => $codigo_item_pedido_exame,
					'motivo_glosa' => $motivo,
					'valor' => $dados_auditoria[0]['valor'],
					'codigo_tipo_glosa' => $codigo_tipo_glosa,
					'data_glosa' => $data_glosa,
					'data_alteracao' => date("Y-m-d H:i:s"),
					'codigo_fornecedor' => $codigo_fornecedor,
					'ativo' => 1,
					'codigo_classificacao_glosa' => 2,
					'codigo_nota_fiscal_servico' => $codigo_nota_fiscal_servico
				)
			);

			if(!empty($glosas)) {
				$dados_glosa['Glosas']['codigo'] = $glosas['Glosas']['codigo'];

				//atualiza os dados
				if(!$this->Glosas->atualizar($dados_glosa)) {
					$dados['retorno'] = false;
					$dados['mensagem'] = "Erro ao atualizar os dados de Glosa para auditoria de exames, favor entrar em contato com o administrador.";
					echo json_encode($dados);
					exit;		
				}
				
			} else {

				//inserir os dados
				if(!$this->Glosas->incluir($dados_glosa)) {
					$dados['retorno'] = false;
					$dados['mensagem'] = "Erro ao incluir os dados de Glosa para auditoria de exames, favor entrar em contato com o administrador.";
					echo json_encode($dados);
					exit;	
				}
			}
		}

		$this->loadModel('ConsolidadoNfsExame');
		if($codigo_nota_fiscal_servico){
			$exame_existe = $this->ConsolidadoNfsExame->find('first',array('conditions' =>array('codigo_item_pedido_exame' => $codigo_item_pedido_exame)));

			$exame_consolidar = array();

			//Incluir o dado na tabela de consolidacoes
			if($exame_existe){
				$exame_consolidar['codigo']                      = $exame_existe['ConsolidadoNfsExame']['codigo'];  
			}

			$exame_consolidar['codigo_empresa']              = 1;    
			$exame_consolidar['codigo_nota_fiscal_servico']  = $codigo_nota_fiscal_servico;                              
			$exame_consolidar['codigo_pedido_exame']         = $codigo_pedido_exame;                      
			$exame_consolidar['codigo_fornecedor']           = $dados_auditoria[0]['codigo_fornecedor'];                  
			$exame_consolidar['codigo_item_pedido_exame']    = $codigo_item_pedido_exame;                          
			$exame_consolidar['codigo_exame']                = $dados_auditoria[0]['codigo_exame'];              
			$exame_consolidar['valor']                       = $dados_auditoria[0]['valor'];     
			$exame_consolidar['valor_corrigido']             = isset($dados_auditoria[0]['valor_corrigido']) ? $dados_auditoria[0]['valor_corrigido'] : null;    
			$exame_consolidar['data_vencimento']             = isset($exame[0]['data_vencimento_nfs']) ? Comum::formataData($exame[0]['data_vencimento_nfs'],'dmy','ymd') : null;                  
			$exame_consolidar['data_pagamento']              = isset($exame[0]['data_pagamento_nfs']) ? Comum::formataData($exame[0]['data_pagamento_nfs'],'dmy','ymd') : null;                          
			$exame_consolidar['status']                      = 1;
			$exame_consolidar['ativo']                       = 1;      
			$exame_consolidar['codigo_usuario_inclusao']     = $usuario['Usuario']['codigo'];                          
			$exame_consolidar['codigo_usuario_alteracao']    = $usuario['Usuario']['codigo'];                          
			$exame_consolidar['data_alteracao']              = date('Y-m-d h:i:s');                 
			if($this->ConsolidadoNfsExame->save($exame_consolidar)){
				$retorno = true;
				//sucesso
			}else{
				$retorno = false;
				//erro
			}
		//Caso o usuário queira voltar atrás e não vincular uma nota no momento da auditoria, esse trecho tratará isso
		}else{
			$exame_existe = $this->ConsolidadoNfsExame->find('first',array('conditions' =>array('codigo_item_pedido_exame' => $codigo_item_pedido_exame)));

			$exame_consolidar = array();

			//Incluir o dado na tabela de consolidacoes
			if($exame_existe){
				$exame_consolidar['codigo']                      = $exame_existe['ConsolidadoNfsExame']['codigo'];  
				$exame_consolidar['codigo_empresa']              = 1;    
				$exame_consolidar['codigo_nota_fiscal_servico']  = $codigo_nota_fiscal_servico;                              
				$exame_consolidar['codigo_pedido_exame']         = $codigo_pedido_exame;                      
				$exame_consolidar['codigo_fornecedor']           = $dados_auditoria[0]['codigo_fornecedor'];                  
				$exame_consolidar['codigo_item_pedido_exame']    = $codigo_item_pedido_exame;                          
				$exame_consolidar['codigo_exame']                = $dados_auditoria[0]['codigo_exame'];              
				$exame_consolidar['valor']                       = $dados_auditoria[0]['valor'];     
				$exame_consolidar['valor_corrigido']             = isset($dados_auditoria[0]['valor_corrigido']) ? $dados_auditoria[0]['valor_corrigido'] : null;    
				$exame_consolidar['data_vencimento']             = isset($exame[0]['data_vencimento_nfs']) ? Comum::formataData($exame[0]['data_vencimento_nfs'],'dmy','ymd') : null;                  
				$exame_consolidar['data_pagamento']              = isset($exame[0]['data_pagamento_nfs']) ? Comum::formataData($exame[0]['data_pagamento_nfs'],'dmy','ymd') : null;                          
				$exame_consolidar['status']                      = 2;
				$exame_consolidar['ativo']                       = 1;      
				$exame_consolidar['codigo_usuario_inclusao']     = $usuario['Usuario']['codigo'];                          
				$exame_consolidar['codigo_usuario_alteracao']    = $usuario['Usuario']['codigo'];                          
				$exame_consolidar['data_alteracao']              = date('Y-m-d h:i:s');        
				
				
				if($this->ConsolidadoNfsExame->save($exame_consolidar)){
					$retorno = true;
					//sucesso
				}else{
					$retorno = false;
					//erro
				}
			}	     
			
		}
		
		//verifica se existe este item na tabela de auditoria exames
		$auditoria_exames = $this->AuditoriaExame->find('first', array('conditions' => array('AuditoriaExame.codigo_item_pedido_exame' => $codigo_item_pedido_exame)));

		//verifica se existe dados
		if(!empty($auditoria_exames)) {
			//seta os dados para atualizar
			$dados_auditoria = array(
				'AuditoriaExame' => array(
					'codigo' => $auditoria_exames['AuditoriaExame']['codigo'],
					'codigo_status_auditoria_exames' => (($status == 4) ? 3 : $status),
					'codigo_nota_fiscal_servico' => $codigo_nota_fiscal_servico,
					'numero_nota_fiscal' => $numero_nota_fiscal,
					'motivo' => $motivo_auditoria,
					'codigo_status_auditoria_imagem' => $status,
					'codigo_motivos_aprovado_ajuste' => $motivo_aprovado_ajuste,
					'data_alteracao' => date("Y-m-d H:i:s"),
					'recebimento_fisico' => $fisico,
					'libera_anexo_exame'=>$libera_anexo_exame,
					'libera_anexo_ficha'=>$libera_anexo_ficha
				)
			);

			//atualiza os dados
			if(!$this->AuditoriaExame->atualizar($dados_auditoria)) {
				$dados['retorno'] = false;
				$dados['mensagem'] = "Erro ao atualizar os dados de auditoria de exames, favor entar em contato com o administrador.";
				echo json_encode($dados);
				exit;		
			}
			else {
				$dados['retorno'] = true;
			}
		}
		else {

			//pega os dados, gera a query para pegar os dados dos exames do fornecedor
			$filtros['codigo_item_pedido_exame'] = $codigo_item_pedido_exame;
			$dados_query = $this->AuditoriaExame->getDadosFornecedorExame($filtros);

			//executa a query pegando o primeiro valor
			$dados = $this->Fornecedor->find('first',array('fields' => $dados_query['fields'],'joins'=>$dados_query['joins'],'conditions'=>$dados_query['conditions'],'recursive' => -1));
			//dados para inserção				
			$dados_auditoria = array(
				'AuditoriaExame' => array(
					'codigo_fornecedor' => $codigo_fornecedor,
					'codigo_pedido_exame' => $dados[0]['codigo_pedido_exame'],
					'codigo_item_pedido_exame' => $codigo_item_pedido_exame,
					'codigo_exame' => $dados[0]['codigo_exame'],
					'codigo_nota_fiscal_servico' => $codigo_nota_fiscal_servico,
					'numero_nota_fiscal' => $numero_nota_fiscal,
					'codigo_status_auditoria_exames' => (($status == 4) ? 3 : $status),				
					'valor' => $dados[0]['valor'],
					'motivo' => $motivo_auditoria,
					'codigo_status_auditoria_imagem' => $status,
					'codigo_motivos_aprovado_ajuste' => $motivo_aprovado_ajuste,
					'ativo' => 1,
					'data_alteracao' => date("Y-m-d H:i:s"),
					'recebimento_fisico' => $fisico,
					'libera_anexo_exame'=>$libera_anexo_exame,
					'libera_anexo_ficha'=>$libera_anexo_ficha
				)
			);

			//inserir os dados
			if(!$this->AuditoriaExame->incluir($dados_auditoria)) {
				$dados['retorno'] = false;
				$dados['mensagem'] = "Erro ao incluir os dados de auditoria de exames, favor entar em contato com o administrador.";		   		
			}
			else {
				$dados['retorno'] = true;
			}

		}//fim auditoria exames

		//altera o status da nota fiscal para em análise
		if($codigo_nota_fiscal_servico){
			$nota_fiscal = $this->NotaFiscalServico->find('first',array('conditions'=> array('codigo' => $codigo_nota_fiscal_servico)));
			//setando o status no array
			$nota_fiscal_update = array(
				'NotaFiscalServico' => array(
				'codigo' => $nota_fiscal['NotaFiscalServico']['codigo'],
				'codigo_nota_fiscal_status' => 2,
				)
			);

			if(!$this->NotaFiscalServico->atualizar($nota_fiscal_update)) {
				$dados['retorno'] = false;
				$dados['mensagem'] = "Erro ao atualizar a nota fiscal de serviço, favor entar em contato com o administrador.";		   		
			}
			else {
				$dados['retorno'] = true;
			}
		}

		echo json_encode($dados);
   		exit;
   	}//fim salvar_auditoria

   	/**
   	 * [relatorio_faturamento_credenciado description]
   	 * 
   	 * relatorio para o credenciado verificar qual os exames que irá faturar.
   	 * 
   	 * @return [type] [description]
   	 */
   	public function relatorio_fat_cred()
   	{

   		//titulo da pagia
    	$this->pageTitle = 'Relatório Faturamento Credenciado';

    	//filtros setados
		$this->data['AuditoriaExames'] = $this->Filtros->controla_sessao($this->data, 'AuditoriaExames');

		//seta os status
		$status = $this->StatusAuditoriaExame->find('list',array('fields' => array('codigo','descricao'), 'order' => array('codigo desc')));

		//pega todos os meses
		$meses = Comum::anoMes(null, true);
		//seta o mes passado selecionado
		$this->data['AuditoriaExames']['mes'] = isset($this->data['AuditoriaExames']['mes']) ? $this->data['AuditoriaExames']['mes'] : date('m', strtotime('-1 months', strtotime(date('Y-m-d'))));
		//pega o ano corrent
		$this->data['AuditoriaExames']['ano'] = isset($this->data['AuditoriaExames']['ano']) ? $this->data['AuditoriaExames']['ano'] : date('Y');

		$this->set(compact('status','meses'));

   	}//fim relatorio_faturamento_credenciado

   	/**
   	 * [relatorio_fat_cred_listagem description]
   	 * 
   	 * metodo para listar os exames que o credenciado irá faturar
   	 * 
   	 * @return [type] [description]
   	 */
   	public function relatorio_fat_cred_listagem($export=null)
   	{

   		//executado por ajax este metodo
    	$this->layout = 'ajax';

    	//filtra o resultado
		$filtros = $this->Filtros->controla_sessao($this->data, 'AuditoriaExames');

		//verifica se existe o codigo do fornecendor para pesquisar
		$dados = array();
		if(!empty($filtros['codigo_fornecedor'])) {

			//tipo para saber se é o relatorio ou não para acrescentar nos jois da consulta e nos fields
			$tipo = 'relatorio';
			//gera a query para pegar os dados dos exames do fornecedor
			$dados_query = $this->AuditoriaExame->getDadosFornecedorExame($filtros,$tipo);

			//para quando acionar o botao de exportar os dados
			if($export) {
				$query = $this->Fornecedor->find('sql',  array(
						'fields' => $dados_query['fields'],
						'conditions' => $dados_query['conditions'],
						'joins' => $dados_query['joins'],
						'recursive' => -1
					)
				);
				$this->export_fat_cred($query);
			}
			
			//monta a query com paginacao
			$this->paginate['Fornecedor'] = array(
				'fields' => $dados_query['fields'],
				'conditions' => $dados_query['conditions'],
				'joins' => $dados_query['joins'],
				'limit' => 50,
				'recursive' => -1
				);
			
	        // pr($this->Fornecedor->find('sql', $this->paginate['Fornecedor']));exit;

			//executa com paginacao
			$dados = $this->paginate('Fornecedor');

			// debug($dados);exit;

		}//fim verificacao

		//seta os dados para a listagem
		$this->set(compact('dados'));


   	}//fim relatorio_fat_cred_listagem()

   	/**
   	 * [export_fat_cred description]
   	 * 
   	 * metodo para exportar os dados do relatorio
   	 * 
   	 * @return [type] [description]
   	 */
   	public function export_fat_cred($query)
   	{

   		$dbo = $this->Fornecedor->getDataSource();
		$dbo->results = $dbo->rawQuery($query);

		ob_clean();
		
		header('Content-Encoding: UTF-8');
		header("Content-Type: application/force-download;charset=utf-8");
		header('Content-Disposition: attachment; filename="faturamento_credenciado_'.date('YmdHis').'.csv"');

		echo utf8_decode('"Cód. Credenciado";"Nome Credenciado";"Cód. Cliente";"Cliente";"Setor";"Cargo";"Funcionário";"Matrícula";"Pedido de Exame";"Data Pedido Exame";"Exame";"Data Realização";"Data Baixa";"Status";"Motivo";"Valor"'."\n");

		while ($value = $dbo->fetchRow()) {
			
			$linha = $value[0]['codigo_fornecedor'].';';
            $linha .= $value[0]['fornecedor_nome'].';';
            $linha .= $value[0]['codigo_cliente'].';';
            $linha .= $value[0]['nome_cliente'].';';
            $linha .= $value[0]['setor_descricao'].';';
            $linha .= $value[0]['cargo_descricao'].';';
            $linha .= $value[0]['nome_funcionario'].';';
            $linha .= $value[0]['matricula'].';';
            $linha .= $value[0]['codigo_pedido_exame'].';';
            $linha .= $value[0]['data_pedido_exame'].';';
            $linha .= $value[0]['exame'].';';
            $linha .= AppModel::dbDateToDate($value[0]['data_realizacao']).';';
            $linha .= AppModel::dbDateToDate($value[0]['data_baixa']).';';
            $linha .= $value[0]['status'].';';
            $linha .= $value[0]['motivo'].';';

            //quando o pagamento for liberado apresenta o valor            
            if($value[0]['codigo_status_auditoria'] == 3) {                
                $linha .= $value[0]['valor'].';';
            }
            else {
            	$linha .= ';';
            }

			echo utf8_decode($linha)."\n";
		}
		die();

   	}//fim export_fat_cred

   	/**
   	 * [info_credenciado description]
   	 * 
   	 * metodo para apresentação da tela de informações do credenciado para exportacao
   	 * 
   	 * @return [type] [description]
   	 */
   	public function info_credenciado ()
   	{

   		$this->pageTitle = 'Relatório de informações do Credenciado';

		//tipo de geração do documento
		$visualizacao = array(
			'tela' => 'Em tela',
			'excel' => 'Excel'
			);

		$campos = $this->getCampos();

		$this->set(compact('tipos_exames', 'situacoes', 'visualizacao', 'campos'));	

   	}//fim info_credenciado

   	/**
   	 * [getCampos description]
   	 * 
   	 * metodo para reaproveitar os dados em outros metodos
   	 * 
   	 * @return [type] [description]
   	 */
   	public function getCampos()
   	{
   		//campos para extração do relatorio
		$campos = array(
			'agencia' => 'Agência ',
			'bairro' => 'Bairro',
			'banco' => 'Banco',
			'cep' => 'CEP',
			'cnes' => 'CNES',
			'crm_uf' => 'CRM + UF do responsável tecnico',
			'cidade' => 'Cidade',
			'complemento' => 'Complemento',
			'corpo_clinico' => 'Corpo clínico',
			'codigo_externo' => 'Código Externo',
			'codigo_cliente_vinculado' => 'Códigos clientes vinculados',
			'email' => 'E-Mails',
			'favorecido' => 'Favorecido',
			'fornecedor_interno' => 'Fornecedor Interno',
			'horario_atendimento' => 'Horário de atendimento',
			'logradouro' => 'Logradouro',
			'numero' => 'Número',
			'numero_conta' => 'Número da Conta',
			'responsavel_adm' => 'Responsável Administrativo',
			'responsavel_tecnico' => 'Responsável Tecnico',
			'servico' => 'Serviços',
			'status' => 'Status',
			'telefone' => 'Telefones',
			'tipo_atendimento' => 'Tipo Atendimento',
			'tipo_filial' => 'Tipo Filial',
			'tipo_conta' => 'Tipo da Conta',
			'exames_unico_local' => 'Todos exames feitos em um único local?',
			'uf' => 'UF',
			// 'utiliza_sistema' => 'Utiliza IT Health',
			'sistema_agendamento' => 'Utiliza nosso sistema de agendamento?',
			'prestador_qualificado' => 'É um credenciado qualificado?',
			'modalidade_atendimento' => 'Modalidade de atendimento',
			'data_contratacao' => 'Data de contratação',
			'modalidade_pagamento' => 'Modalidade de pagamento',
			'faturamento_dias' => 'Quantidade de dias da Modalidade de pagamento',
			'dia_pagamento' => 'Outra data de pagamento',
			'tipo_pagamento' => 'Tipo de pagamento',
			'horario_diferenciado' => 'Horário diferenciado',
			'historico' => 'Histórico',
			'exames' => 'Exames',
			'valor_custo' => 'Valor Custo',
			'tempo_liberacao' => "Tempo liberação"
		);

		return $campos;
   	}


   	/**
   	 * [exportar_informacao_credenciado description]
   	 * 
   	 * metodo para pegar os dados e saber se vai apresentar em tela ou em arquivo
   	 * 
   	 * @return [type] [description]
   	 */
   	public function exportar_informacao_credenciado()
	{		
		//verifica se é um post
		if($this->RequestHandler->isPost()) {

			ini_set('max_execution_time', '999999');
			ini_set('memory_limit', '4G');
			set_time_limit(120);
			
			//mata o indice last
			unset($this->data['Last']);

			if(!isset($this->data['Fornecedor']['codigo_fornecedor'])) $this->data['Fornecedor']['codigo_fornecedor'] = null;

			if($this->data['Fornecedor']['exibicao'] == 'excel') {
				$this->exportar_informacao_credenciado_excel($this->data['Fornecedor']['to'], $this->data['Fornecedor']['codigo_fornecedor']);
			} else {
				$this->exportar_informacao_credenciado_tela($this->data['Fornecedor']['to'], $this->data['Fornecedor']['codigo_fornecedor']);
			}
		}//fim post

	}// fim exportar_informacao_credenciado

	/**
	 * [exportar_informacao_credenciado_tela description]
	 * 
	 * metodo para imprimir o resultado em tela
	 * 
	 * @param  [type] $campos         [description]
	 * @param  [type] $codigo_cliente [description]
	 * @return [type]                 [description]
	 */
	private function exportar_informacao_credenciado_tela($campos, $codigo_fornecedor = null) {
		$this->pageTitle = 'Relatório de informações da empresa';

		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 300); // 5min

		//trata os campos
		if(!empty($campos)) {

			$todas_colunas = $this->getCampos();
			$campos_corretos = array();
			foreach($campos AS $colunas) {
				$campos_corretos[$colunas] = $todas_colunas[$colunas];
			}
			unset($campos);
			$campos['codigo_credenciado'] = "Cód. Credenciado";
			$campos['razao_social'] = "Razão Social";
			$campos['nome_fantasia'] = "Nome Fantasia";
			$campos['cnpj'] = "CNPJ";
			$campos['exames_tela'] = "Exames - Valor Custo";
			$campos['tempo_liberacao'] = "Tempo Liberacao";
			$campos = array_merge($campos,$campos_corretos);
		}
		else {
			if($campos == '') $campos = array();
		}

		unset($campos["exames"]);
		unset($campos["valor_custo"]);

		$dados = $this->Fornecedor->dados_informacoes_credenciado($codigo_fornecedor, $campos);

		$this->set(compact('campos', 'dados'));
		$this->render('exportar_informacao_credenciado_tela');
	
	}//fim exportar_informacao_credenciado_tela

	/**
	 * [exportar_informacao_credenciado_excel description]
	 * 
	 * metodo para imprimir o resultado em csv
	 * 
	 * @param  [type] $campos         [description]
	 * @param  [type] $codigo_cliente [description]
	 * @return [type]                 [description]
	 */
	private function exportar_informacao_credenciado_excel($campos, $codigo_fornecedor = null)
	{
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 300); // 5min

		$this->layout = false;

		//trata os campos
		if(!empty($campos)) {

			$todas_colunas = $this->getCampos();

			$campos_corretos = array();

			foreach($campos AS $colunas) {
				$campos_corretos[$colunas] = utf8_decode($todas_colunas[$colunas]);
			}

			unset($campos);

			$campos['codigo_credenciado'] = utf8_decode("Cód. Credenciado");
			$campos['razao_social'] = utf8_decode("Razão Social");
			$campos['nome_fantasia'] = "Nome Fantasia";
			$campos['cnpj'] = "CNPJ";
			$campos = array_merge($campos,$campos_corretos);
		}
		else {
			if($campos == '') $campos = array();
		}

		$dados = $this->Fornecedor->dados_informacoes_credenciado($codigo_fornecedor, $campos,'excel');


		ob_clean();
		
		header('Content-Encoding: UTF-8');
		header("Content-Type: application/force-download;charset=utf-8");
		header('Content-Disposition: attachment; filename="relatorio_infos_credenciados_'.date('YmdHis').'.csv"');
		
		//monta o cabecalho
		$cabecalho = implode(";", $campos);	
		echo $cabecalho."\n";

		//varre os dados
		foreach ($dados as $key => $dado) { 
			$linha = '';
			unset($dado[0]["exames_tela"]);
			//monta as colunas
			foreach ($campos as $index_col => $desc_coluna) {
				$linha .= '"'.utf8_decode(strtr($dado[0][$index_col], ';', ':')).'";';
			}//fim foreach colunas

			$linha .= "\n";
			echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
		}//fim dados
		die();
	
	}//fim exportar_informacao_credenciado_excel

    function ajax_get_por_codigo_cliente($codigo_cliente){
        $this->layout = 'ajax';
        $this->autoLayout = false;
        $this->autoRender = false;

        $data = $this->Fornecedor->get_lista_por_codigo_cliente($codigo_cliente);

        return $this->responseJson($data);
    }

    //metodo criado para auxiliar na interacao por ajax
    public function atualiza_tipo_atendimento($codigo_fornecedor, $tipo) {
    	//nao rodar ctp
    	$this->autoRender = false;
    	//carrega model
    	$this->loadModel('ListaDePrecoProdutoServico');
    	//retorno ja definido como true
		$retorno = 1;
		//retorna as listas de preco produto e servicos
		$lista_de_preco = $this->ListaDePreco->retorna_lista_preco($codigo_fornecedor);
		//variavel vazia
		$dados = array();
		$fornecedor = array();
		//verifica se vem ordem de chegada ou hora marcada
		if($tipo == 0){
			//tratamento
			foreach ($lista_de_preco as $key => $value) {
				# code...
				if($value){
					//monsta a query
					$queryTipo = "UPDATE RHHealth.dbo.listas_de_preco_produto_servico SET tipo_atendimento = 0 WHERE codigo_lista_de_preco_produto = ".$value['ListaDePrecoProduto']['codigo'];
					//atualizar todos os servicos para ordem de chegada
					if(!$this->ListaDePrecoProdutoServico->query($queryTipo)){
						$retorno = 0;
					}
				} else {
					$retorno = 0;
				}
			}
			//verifica se deu erro para nao efetuar rotina
			if($retorno != 0){
				//montei a query na mao por que o metodo atualizar da model Fornecedor estava pedindo outras validacoes de outros campos que nao precisam no momento de atualizacao, para nao perder tempo, preferir aplicar assim
				//query
				$queryFornecedor = "UPDATE RHHealth.dbo.fornecedores SET tipo_atendimento = 0 WHERE codigo = ".$codigo_fornecedor;
				//atualiza o tipo atendimento do fornecedor
				$this->Fornecedor->query($queryFornecedor);				
			}
		} else if($tipo == 1) {
			//tratamento
			foreach ($lista_de_preco as $key => $value) {
				# code...
				if($value){
					//monsta a query
					$queryTipo = "UPDATE RHHealth.dbo.listas_de_preco_produto_servico SET tipo_atendimento = 1 WHERE codigo_lista_de_preco_produto = ".$value['ListaDePrecoProduto']['codigo'];
					//atualizar todos os servicos para ordem de chegada
					if(!$this->ListaDePrecoProdutoServico->query($queryTipo)){
						$retorno = 0;
					}
				} else {
					$retorno = 0;
				}
			}
			//verifica se deu erro para nao efetuar rotina
			if($retorno != 0){
				//query
				$queryFornecedor = "UPDATE RHHealth.dbo.fornecedores SET tipo_atendimento = 1 WHERE codigo = ".$codigo_fornecedor;
				//atualiza o tipo atendimento do fornecedor
				$this->Fornecedor->query($queryFornecedor);				
			}
		}
		//retorno para a view
		return $retorno;
    }

	public function log_anexos($codigo_item_pedido_exame, $tabela)
    {
        $this->pageTitle = 'Log Anexo Exame';
        $this->layout    = 'new_window';

		$AnexoExameLog = ClassRegistry::init('AnexoExameLog');
            $fields = array(
                'AnexoExameLog.caminho_arquivo',
                'AnexoExameLog.data_inclusao',
                'AnexoExameLog.data_alteracao',
                'AnexoExameLog.acao_sistema',
				'AnexoExameLog.status',
                'UsuarioInclusao.nome',
                'UsuarioAlteracao.nome'
            );
            $conditions = array('AnexoExameLog.codigo_item_pedido_exame' => $codigo_item_pedido_exame);
            $joins = array(
                array(
                    'table' => 'Rhhealth.dbo.usuario',
                    'alias' => 'UsuarioInclusao',
                    'type' => 'LEFT',
                    'conditions' => 'AnexoExameLog.codigo_usuario_inclusao = UsuarioInclusao.codigo',
                ),
				array(
                    'table' => 'Rhhealth.dbo.usuario',
                    'alias' => 'UsuarioAlteracao',
                    'type' => 'LEFT',
                    'conditions' => 'AnexoExameLog.codigo_usuario_alteracao = UsuarioAlteracao.codigo',
                ),
            );
            $order = array('AnexoExameLog.data_inclusao DESC');
            $dados = $AnexoExameLog->find('all',array('fields' => $fields,'conditions' => $conditions,'joins' => $joins,'order' => $order));
            foreach ($dados as $key => $dado) {
                $dados[$key]['AnexoExameLog']['nome_usuario_inclusao'] = $dado['UsuarioInclusao']['nome'];
                unset($dados[$key]['UsuarioInclusao']);
				$dados[$key]['AnexoExameLog']['nome_usuario_alteracao'] = $dado['UsuarioAlteracao']['nome'];
                unset($dados[$key]['UsuarioAlteracao']);
                switch ($dado['AnexoExameLog']['acao_sistema']) {
                    case 0:
                        $dados[$key]['AnexoExameLog']['acao_sistema'] = 'Inclusão';
                        break;
                    case 1:
                        $dados[$key]['AnexoExameLog']['acao_sistema'] = 'Atualização';
                        break;
                    case 2:
                        $dados[$key]['AnexoExameLog']['acao_sistema'] = 'Exclusão';
                        break;
                }
				switch($dado['AnexoExameLog']['status']) {
					case 0:
                        $dados[$key]['AnexoExameLog']['status'] = 'Inativo';
                        break;
                    case 1:
                        $dados[$key]['AnexoExameLog']['status'] = 'Ativo';
                        break;
				}
            }
            foreach ($dados as $key1 => $dadoLog) {
                foreach ($dadoLog['AnexoExameLog'] as $key2 => $value) {
                    if(empty($value))
                        $dados[$key1]['AnexoExameLog'][$key2] = '';
                }
            }
			$retorno = json_encode($dados);

			$this->set(compact('dados'));
		
    }
	
	

	/**
	 * Obter dados de credenciado
	 *
	 * @return responseJson
	 */
	public function obter_credenciado()
    {
        $this->layout = 'ajax';
        $this->autoLayout = false;
		$this->autoRender = false;
		
		if(!isset($this->RequestHandler->params['url']) ){
			throw new Exception("Request inválido", 1); exit;
		}

		$params = $this->RequestHandler->params['url'];
		
		$data = array('data'=>null, 'pagination'=>null);
		$conditions = array();
		
		$page = (isset($params['page']) && !empty($params['page'])) ? $params['page'] : 1;
		$limit = (isset($params['limit']) && !empty($params['limit'])) ? $params['limit'] : 50;
		
		
		$this->loadModel('Credenciado');

		// Pesquisa por documento
		if(isset($params['codigo_documento']))
		{

			$codigo_documento  = Comum::soNumero($params['codigo_documento']);

			if(empty($codigo_documento)){
				$data['error'] = 'Documento inválido';
				return $this->responseJson($data);
			}
		
			if(strlen($codigo_documento) <= 2){
				$data['error'] = 'Quantidade de caracteres insuficientes';
				return $this->responseJson($data);
			}

			$conditions['codigo_documento LIKE'] = $codigo_documento . '%';
		}

		// Pesquisa por codigo credenciado/fornecedor
		if(isset($params['codigo_credenciado']))
		{
			
			$codigo_credenciado  = Comum::soNumero($params['codigo_credenciado']);

			if(empty($codigo_credenciado)){
				$data['error'] = 'Documento inválido';
				return $this->responseJson($data);
			}
	
			if(strlen($codigo_credenciado) <= 1){
				$data['error'] = 'Quantidade de caracteres insuficientes';
				return $this->responseJson($data);
			}

			$conditions['codigo LIKE'] = $codigo_credenciado . '%';
			//Adicionada a condição para buscar apenas credenciados do tipo fiscal(PC-2647 Matheus Brum)
			$conditions['tipo_unidade '] = 'F';
		}

		// Pesquisa por Razao Social
		if(isset($params['razao_social']) && Validation::alphaNumeric($params['razao_social']))
		{
			$razao_social  = $params['razao_social'];

			if(empty($razao_social)){
				$data['error'] = 'Razão Social inválido';
				return $this->responseJson($data);
			}

			if(strlen($razao_social) <= 3){
				$data['error'] = 'Quantidade de caracteres insuficientes';
				return $this->responseJson($data);
			}

			$razao_social = Sanitize::clean($razao_social);
			$conditions['razao_social LIKE'] = '%' . $razao_social . '%';
		}		

		
		$fields = array('codigo','codigo_documento','nome','razao_social', 'ativo', 'data_inclusao', 'data_alteracao');
		
		$order = array('Credenciado.ativo DESC', 'Credenciado.data_alteracao DESC');

		$this->paginate['Credenciado'] = array(
			'fields' => $fields,
			'conditions' => $conditions,
			'limit' => $limit,
			'order' =>  $order
		);


		$credenciadoData = $this->paginate('Credenciado');
		
		$total = $this->Credenciado->find('count', compact('conditions'));

		if(!empty($credenciadoData))
		{

			$tmp = array();

			foreach ($credenciadoData as $key => $value) {
				$tmp['codigo'] = $value['Credenciado']['codigo'];
				$tmp['codigo_documento'] = Comum::formatarDocumento($value['Credenciado']['codigo_documento']);
				$tmp['nome'] = $value['Credenciado']['nome'];
				$tmp['razao_social'] = $value['Credenciado']['razao_social'];
				$tmp['ativo'] = $value['Credenciado']['ativo'];
				$tmp['data_inclusao'] = $value['Credenciado']['data_inclusao'];
				$tmp['data_alteracao'] = $value['Credenciado']['data_alteracao'];
				
				$data['data'][] = $tmp;
				
			}

			$pagina_atual = ($page > 0) ? intval($page) : 1;
			$offset = !empty($pagina_atual) ? ($pagina_atual - 1) * $limit : 0;
			$more = !(($offset + $limit) > $total);	
			
			$data['pagination'] = array('offset'=>$offset, 'total'=>$total, 'more'=>$more);

		}

		return $this->responseJson($data);
	}

	
	public function upload_exame($codigo_item_pedido, $exibe_switch = null){

		if($this->RequestHandler->isPost()) {
			
            $nome_arquivo =  strtolower($_FILES['data']['name']['ItemPedidoExame']['anexo_exame']);
		

            preg_match("/(\..*){1}$/i", $nome_arquivo, $ext);
            if (strpos($nome_arquivo, ".pdf") > 0 || strpos($nome_arquivo, ".jpg") > 0 || strpos($nome_arquivo, ".png") > 0) {
                $nome_arquivo = $this->data['ItemPedidoExame']['anexo_exame']['name'];                
                $this->Upload->setOption('field_name', 'anexo_exame');            
                $this->Upload->setOption('accept_extensions', array('pdf','jpg','jpeg', 'png'));
                $this->Upload->setOption('accept_extensions_message', 'Arquivo inválido! Favor escolher arquivo Pdf, jpg, jpeg ou png');
                $this->Upload->setOption('size_max', 5242880);
                $this->Upload->setOption('size_max_message', 'Tamanho máximo excedido! Só é permitido arquivos de até 5MB');
                $retorno = $this->Upload->fileServer($this->data['ItemPedidoExame']);
                // se ocorreu algum erro de comunicação com o fileserver
                if (isset($retorno['error']) && !empty($retorno['error']) ){                    
                    $chave = key($retorno['error']);
                    // $this->BSession->setFlash(array(MSGT_ERROR, $retorno['error'][$chave]));   /////////////////////////////////                 
                    $this->redirect(false);
                }
                
                else {
                    $anexo = $this->AnexoExame->find('first',array('conditions' => array('codigo_item_pedido_exame' => $codigo_item_pedido)));
                    $status = 1;
                    if(!empty($this->authUsuario['Usuario']['codigo_fornecedor'])){
                        $status = 2;
                    }
                    if(empty($anexo)){
                        $dados['AnexoExame'] = array(
                            'codigo_item_pedido_exame' => $codigo_item_pedido,
                            'caminho_arquivo' =>  $retorno['data'][$nome_arquivo]['path_url'], //$codigo_item_pedido.DS.'anexo_item_exame_'.$codigo_item_pedido. $ext[0],
                            'status' => $status,
							'codigo_verificador' => $_POST['codigo_verificador'],

                        );
                        if($this->AnexoExame->incluir($dados)){
                            // $this->BSession->setFlash('save_success');
                            // $this->redirect(array('action' => 'index2'));
							$this->redirect(false);
                        } else {
                            // $this->BSession->setFlash('save_error');
                            // $this->redirect(array('action' => 'index2'));
							$this->redirect(false);
                        }
                    } else {
                        $anexo['AnexoExame']['caminho_arquivo'] =  $retorno['data'][$nome_arquivo]['path_url']; // $codigo_item_pedido.DS.'anexo_item_exame_'.$codigo_item_pedido. $ext[0];
                        $anexo['AnexoExame']['codigo_usuario_inclusao'] = $_SESSION['Auth']['Usuario']['codigo'];
                        $anexo['AnexoExame']['data_inclusao'] = date("Y-m-d H:i:s");
                        $anexo['AnexoExame']['status'] = $status;
						$anexo['AnexoExame']['codigo_verificador'] = $_POST['codigo_verificador'];
						
                        if($this->AnexoExame->atualizar($anexo)){
                            // $this->BSession->setFlash('save_success');
                            // $this->redirect(array('action' => 'index2'));
							$this->redirect(false);
                        } else {
                            // $this->BSession->setFlash('save_error');
                            // $this->redirect(array('action' => 'index2'));
							$this->redirect(false);
                        }
                    }
                }
            } else {
                // $this->BSession->setFlash(array(MSGT_ERROR, 'O anexo de exames só aceita arquivos nas extensões .JPG, .PNG ou .PDF! Tente Novamente.'));
                // $this->redirect(array('action' => 'index2'));
				$this->redirect(false);
            }
        }
        $fields = array(
            'PedidoExame.codigo',
            'ItemPedidoExame.codigo',
            'Exame.descricao',
            'Cliente.razao_social',
            'AnexoExame.caminho_arquivo',
            'AnexoExame.aprovado_auditoria'
        );
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.pedidos_exames',
                'alias' => 'PedidoExame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.exames',
                'alias' => 'Exame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'PedidoExame.codigo_cliente = Cliente.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.anexos_exames',
                'alias' => 'AnexoExame',
                'type' => 'LEFT',
                'conditions' => 'AnexoExame.codigo_item_pedido_exame = ItemPedidoExame.codigo',
            )
        );
        $pedido = $this->ItemPedidoExame->find('first', 
            array('conditions' => array('ItemPedidoExame.codigo' => $codigo_item_pedido),'joins' => $joins,'fields' => $fields)
        );
        
		$this->set(compact('pedido', 'codigo_item_pedido'));
        $Uperfil = ClassRegistry::init('Uperfil');
        $fields = array('codigo');
        $conditions = array();
        // $conditions = array('codigo_tipo_perfil = 5 OR codigo_empresa IS NULL');
        $conditions = array('codigo IN (20,15,21,16,19,11,13) OR codigo_tipo_perfil = 5 OR codigo_empresa IS NULL');
        $permissoes_acoes['deletar_anexo'] = array($Uperfil->find('list',array('fields' => $fields,'conditions' => $conditions)));
		
		if (isset($exibe_switch) && !empty($exibe_switch) && $exibe_switch == 'false') {
			$exibe_switch = false;
		} else {
			$exibe_switch = true;
		}
        $this->set(compact('permissoes_acoes', 'exibe_switch'));
    }

	public function upload_ficha_clinica($codigo_item_pedido,$codigo_ficha_clinica = null, $exibe_switch = null){
		$this->loadModel('ItemPedidoExame');

        if($this->RequestHandler->isPost()) {
            $nome_arquivo =  strtolower($_FILES['data']['name']['ItemPedidoExame']['ficha_clinica']);
            preg_match("/(\..*){1}$/i", $nome_arquivo, $ext);
            if (strpos($nome_arquivo, ".pdf") > 0 || strpos($nome_arquivo, ".jpg") > 0 || strpos($nome_arquivo, ".png") > 0){
                if(!is_dir(DIR_ANEXOS.$codigo_item_pedido.DS))
                    mkdir(DIR_ANEXOS.$codigo_item_pedido.DS);
                $destino = DIR_ANEXOS.DS.$codigo_item_pedido.DS.'anexo_ficha_clinica_'.$codigo_item_pedido. $ext[0];
                $caminho_completo = end(glob(DIR_ANEXOS.$codigo_item_pedido.DS.'anexo_ficha_clinica_'.$codigo_item_pedido.'.*'));
                if (is_file($caminho_completo))
                    unlink($caminho_completo);
                if(!move_uploaded_file($_FILES['data']['tmp_name']['ItemPedidoExame']['ficha_clinica'],$destino)){
                    $this->BSession->setFlash('save_error');
                    $this->redirect(array('action' => 'index2'));
                } else {
                    $anexo = $this->AnexoFichaClinica->find('first',array('conditions' => array('codigo_ficha_clinica' => $codigo_ficha_clinica)));
                    if(empty($anexo)){
                        $dados['AnexoFichaClinica'] = array(
                            'codigo_ficha_clinica' => $codigo_ficha_clinica,
                            'caminho_arquivo' => $codigo_item_pedido.DS.'anexo_ficha_clinica_'.$codigo_item_pedido. $ext[0],
							'codigo_verificador' => $_POST['codigo_verificador'],
                        );
                        if($this->AnexoFichaClinica->incluir($dados)){
                            // $this->BSession->setFlash('save_success');
                            // $this->redirect(array('action' => 'index2'));
                        } else {
                            // $this->BSession->setFlash('save_error');
                            // $this->redirect(array('action' => 'index2'));
                        }
                    } else {
                        $anexo['AnexoFichaClinica']['caminho_arquivo'] = $codigo_item_pedido.DS.'anexo_ficha_clinica_'.$codigo_item_pedido. $ext[0];
                        $anexo['AnexoFichaClinica']['codigo_usuario_inclusao'] = $_SESSION['Auth']['Usuario']['codigo'];
                        $anexo['AnexoFichaClinica']['data_inclusao'] = date("Y-m-d H:i:s");
						$anexo['AnexoFichaClinica']['codigo_verificador'] = $_POST['codigo_verificador'];
                        if($this->AnexoFichaClinica->atualizar($anexo)){
                            // $this->BSession->setFlash('save_success');
                            // $this->redirect(array('action' => 'index2'));
                        } else {
                            // $this->BSession->setFlash('save_error');
                            // $this->redirect(array('action' => 'index2'));
                        }
                    }
                }
            } else {
                // $this->BSession->setFlash(array(MSGT_ERROR, 'O anexo de ficha clínica só aceita arquivos nas extensões .JPG, .PNG ou .PDF! Tente Novamente.'));
                // $this->redirect(array('action' => 'index2'));
            }
        }
        $fields = array(
            'PedidoExame.codigo',
            'ItemPedidoExame.codigo',
            'Exame.descricao',
            'FichaClinica.codigo',
            'Cliente.razao_social',
			'AnexoFichaClinica.aprovado_auditoria'
        );
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.pedidos_exames',
                'alias' => 'PedidoExame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.exames',
                'alias' => 'Exame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'PedidoExame.codigo_cliente = Cliente.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.fichas_clinicas',
                'alias' => 'FichaClinica',
                'type' => 'LEFT',
                'conditions' => 'PedidoExame.codigo = FichaClinica.codigo_pedido_exame',
            ),
			array(
                'table' => 'Rhhealth.dbo.anexos_fichas_clinicas',
                'alias' => 'AnexoFichaClinica',
                'type' => 'LEFT',
                'conditions' => 'FichaClinica.codigo = AnexoFichaClinica.codigo_ficha_clinica',
            ),
        );
        $pedido = $this->ItemPedidoExame->find('first', 
            array('conditions' => array('ItemPedidoExame.codigo' => $codigo_item_pedido),'joins' => $joins,'fields' => $fields)
        );
        $this->set(compact('pedido', 'codigo_item_pedido'));
        $this->set('codigo_ficha_clinica', $pedido['FichaClinica']['codigo']);
        $Uperfil = ClassRegistry::init('Uperfil');
        $fields = array('codigo');
        $conditions = array();
        // $conditions = array('codigo_tipo_perfil = 5 OR codigo_empresa IS NULL');
        $conditions = array('codigo IN (20,15,21,16,19,11,13) OR codigo_tipo_perfil = 5 OR codigo_empresa IS NULL');
        $permissoes_acoes['deletar_anexo'] = array($Uperfil->find('list',array('fields' => $fields,'conditions' => $conditions)));

		if (isset($exibe_switch) && !empty($exibe_switch) && $exibe_switch == 'false') {
			$exibe_switch = false;
		} else {
			$exibe_switch = true;
		}

        $this->set(compact('permissoes_acoes', 'exibe_switch'));
    }

	private function aprovacaoAuditoriaAnexoExame($codigo_item_pedido_exame){
		$anexo_exame = $this->AnexoExame->find('first',array('conditions'=> array('codigo_item_pedido_exame' => $codigo_item_pedido_exame)));
		
		if($anexo_exame){
			$anexo_update = array(
				'AnexoExame' => array(
					'codigo' => $anexo_exame['AnexoExame']['codigo'],
					'aprovado_auditoria' => 1
				)
			);
			
			if(!$this->AnexoExame->atualizar($anexo_update)) {
				$dados['retorno'] = false;
				$dados['mensagem'] = "Erro ao atualizar o anexo da auditoria, favor entar em contato com o administrador.";		   		
			}
			else {
				$dados['retorno'] = true;
			}
			return $dados;
		}else{
			$dados['retorno'] = false;
			return $dados;
		}
		
	}

	private function aprovacaoAuditoriaAnexoFichaClinica($codigo_ficha_clinica)
	{
		$anexo_ficha_clinica = $this->AnexoFichaClinica->find('first',array('conditions' => array('codigo_ficha_clinica' => $codigo_ficha_clinica)));
		
		if($anexo_ficha_clinica){
			
			$anexo_update = array(
				'AnexoFichaClinica' => array(
					'codigo' => $anexo_ficha_clinica['AnexoFichaClinica']['codigo'],
					'aprovado_auditoria' => 1
				)
			);
			
			if(!$this->AnexoFichaClinica->atualizar($anexo_update)) {
				$dados['retorno'] = false;
				$dados['mensagem'] = "Erro ao atualizar o anexo da ficha, favor entar em contato com o administrador.";		   		
			}
			else {
				$dados['retorno'] = true;
			}
	
			return $dados;
		
		}else{
			$dados['retorno'] = false;
			return $dados;
		}

	}

	public function verificador_ficha($codigo_pedido_exame, $codigo_verificador){
		
		
		$this->layout = 'ajax';

		$joins = array(
            array(
                'table' => 'Rhhealth.dbo.anexos_fichas_clinicas',
                'alias' => 'AnexoFichaClinica',
                'type' => 'INNER',
                'conditions' => 'FichaClinica.codigo = AnexoFichaClinica.codigo_ficha_clinica',
            ),
		);

		$this->loadModel('FichaClinica');
		$anexo_ficha_clinica = $this->FichaClinica->find('first',
				array(
						'conditions' => array('FichaClinica.codigo_pedido_exame' => $codigo_pedido_exame), 
						'joins' => $joins,
						'fields' => array(
							'AnexoFichaClinica.codigo',
							'AnexoFichaClinica.codigo_verificador'
						)
					)
				);

		if ($codigo_verificador == $anexo_ficha_clinica['AnexoFichaClinica']['codigo_verificador']){
			echo 1;
		}else{
			echo 0;
		}
	}

	public function verificador_exame($codigo_item_pedido_exame, $codigo_verificador)
	{
		$this->layout = 'ajax';

		$anexo_exame = $this->AnexoExame->find('first',array('conditions'=> array('codigo_item_pedido_exame' => $codigo_item_pedido_exame, 'codigo_verificador' => $codigo_verificador)));

		if ($codigo_verificador == $anexo_exame['AnexoExame']['codigo_verificador']){
			echo 1;
		}else{
			echo 0;
		}
	}

}