<?php
class FichasStatusCriteriosController extends AppController {
	var $name = 'FichasStatusCriterios';
	public $components = array('Session','Fichas');
	public $uses = array(
		'MRmaEstatistica',
		'MRmaOcorrencia',
		'MGeradorOcorrencia',
		'Sinistro',
		'ArtigoCriminal',
		'TipoOcorrenciaTeleconsult',
		'FichaStatusCriterio',
		'Ficha',
		'FichaPesquisa',
		'PontuacoesStatusCriterio',
		'Criterio',
		'StatusCriterio',
		'ParametroScore',
		'TipoRetorno', 
		'Usuario', 
		'FichaScorecard', 
		'FichaScorecardRetorno', 
		'ProfissionalTipo', 
		'EnderecoEstado', 
		'QuestaoResposta',
    	'TipoCnh', 
    	'TipoContato', 
    	'VEndereco', 
    	'Profissional', 
    	'ProfissionalEndereco', 
    	'ProfissionalContato', 
    	'ProfissionalTelecheque',
    	'ProfissionalSerasa',
    	'ProfissionalNegativacao',
    	'Proprietario',  
    	'ProprietarioEndereco',
    	'ProprietarioContato',
    	'ProprietarioTelecheque', 
    	'ProprietarioSerasa',
    	'Tecnologia', 
    	'VeiculoCor', 
    	'VeiculoFabricante', 
    	'VeiculoModelo', 
    	'EnderecoCidade', 
    	'CargaTipo',
    	'CargaValor', 
    	'FichaScorecardQuestao', 
    	'Status', 
    	'Veiculo',
    	'VeiculoOcorrencia', 
    	'FichaScorecardQuestaoResp', 
    	'Cliente', 
    	'Seguradora',
    	'FichaScProfContatoLog', 
    	'FichaScorecardVeiculo', 
    	'FichaScorecardStatus', 
    	'VeiculoLog', 
    	'ProprietarioLog',
    	'EmbarcadorTransportador', 
    	'FichaScVeicPropContatoLog', 
    	'ProfissionalContatoLog', 
    	'ProprietarioContatoLog',
		'FichaScorecardLog',
		'Produto',
		'ProfissionalLog',
		'TipoOperacao',
		'LogAtendimento'		
		//'AcessoScorecard'
	);





	function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array(
            'alterar_score'
        ));
	}
	var $helpers = array('Paginator','Html','Form');

	function editar($codigo_ficha=null, $abrir_pesquisa = FALSE ){
		$success = false;
		$this->layout = 'new_window';
		$dados_ficha = $this->FichaScorecard->carregar( $codigo_ficha );
		$codigo_profissional   		= $this->FichaScorecard->buscaCodigoProfissional( $codigo_ficha );
		$this->LogAtendimento  		= ClassRegistry::init('LogAtendimento');		
		$authUsuario 			 	= $this->BAuth->user();
		$codigo_usuario_inclusao 	= $authUsuario['Usuario']['codigo'];
		$dados_log_atendimento 	= array(
			'LogAtendimento'   => array(
			'codigo_produto'        => Produto::SCORECARD,
			'codigo_profissional'   => $codigo_profissional,
			'codigo_profissional_tipo' => $dados_ficha['FichaScorecard']['codigo_profissional_tipo']
		));
		$pendente = isset($this->data['FichaStatusCriterio']['botao_selecionado']) ? $this->data['FichaStatusCriterio']['botao_selecionado'] : NULL;
		if( $pendente != 'pendente' ){
			if (empty($codigo_ficha)) {
				// $this->redirect(array('controller'=>'fichas_scorecard', 'action' => 'fichas_a_pesquisar'));          
				$success = true;
				$this->set(compact('success'));
			} else {
				$this->pageTitle = 'Pesquisa Ficha - ScoreCard';				
				$this->carrega_cabecalho_ficha($codigo_ficha);
				if (!empty($this->data)) {
					unset($this->data['FichaStatusCriterio']['ExisteOcorrencia']);
					unset($this->data['FichaStatusCriterio']['ExisteOcorrenciaProf']);
					$this->data['FichaScorecard']['resumo']	= $this->data['FichaStatusCriterio']['resumo'];
					unset($this->data['FichaStatusCriterio']['resumo']);
					$dados = $this->FichaStatusCriterio->formatarDados($this->data, $codigo_ficha, $codigo_usuario_inclusao);
					$permite_salvar = TRUE;
					if( FichaScorecard::ENVIA_EMAIL_SCORECARD === FALSE ) {
						if ( empty($this->data['FichaScorecard']['codigo_parametro_score'] ) ){
							$this->FichaScorecard->invalidate('codigo_parametro_score','Classificação é um campo obrigatório');
							$permite_salvar = FALSE;
						}
					}
					if( $permite_salvar == TRUE ) {
						if ( $this->FichaStatusCriterio->salvarFichaStatusCriterio($codigo_ficha, $dados) ) {
							$this->FichaScorecard->concluirPesquisa( $codigo_ficha, $this->data );
							if( !empty($this->data['FichaScorecard']['codigo_parametro_score']))
								$this->FichaScorecard->alteraScoreManualmente( $codigo_ficha, $this->data['FichaScorecard']['codigo_parametro_score'] );

							//Gravo Log de Atendimento
							$data_inicio = $this->Session->read('data_inicio_log_atendimento');
							$dados_log_atendimento['LogAtendimento']['codigo_tipo_operacao'] = TipoOperacao::PESQUISA_MOTORISTA;
							$dados_log_atendimento['LogAtendimento']['data_inicio'] = !empty($data_inicio) ? $data_inicio : date('Ymd H:i:s');
							$this->LogAtendimento->incluir( $dados_log_atendimento );

							$this->BSession->setFlash('save_success');
							// $this->redirect(array('controller'=>'fichas_scorecard', 'action' => 'fichas_a_pesquisar'));
							$success = true;
						} else {
							$this->BSession->setFlash('save_error');
						}
					} else {
						$this->BSession->setFlash('save_error');
					}
				} else {
					$this->Session->write('data_inicio_log_atendimento', date("Ymd H:i:s") );
					if ( !$this->Session->read('FiltrosFichaScorecard.gerente') ) {
						if( empty( $dados_ficha['FichaScorecard']['codigo_usuario_responsavel'] ) ) {
							$dados_log_atendimento['LogAtendimento']['codigo_tipo_operacao'] = TipoOperacao::ANALISE_PREVIA;
							$dados_log_atendimento['LogAtendimento']['data_inicio'] 		 = date('Ymd H:i:s');
							$this->LogAtendimento->incluir( $dados_log_atendimento );
						}
						$valor = $this->FichaScorecard->atualizaStatus($codigo_ficha, FichaScorecardStatus::EM_PESQUISA, $codigo_usuario_inclusao, 'pesquisando');
						if(!$valor){
							$this->BSession->setFlash(array(MSGT_ERROR, 'A ficha já está em pesquisa!')); 
							$success = true;
							if( $abrir_pesquisa == TRUE )
								$this->redirect(array('controller' => 'fichas_scorecard', 'action' => 'fichas_a_pesquisar'));
						}
						// $this->FichaScorecard->atualizarResumo($codigo_ficha, $this->data['FichaScorecard']['resumo']);
						$this->data = $this->FichaStatusCriterio->listarRespostasFicha($codigo_ficha);
					}
				}
				$this->Fichas->carregarDadosFicha($codigo_ficha);
				$this->data['Profissional']['codigo_seguranca_cnh'] = str_pad($this->data['Profissional']['codigo_seguranca_cnh'], 11,0,STR_PAD_LEFT); 
				$dados_parametros['codigo_embarcador'] = $this->data['FichaScorecard']['codigo_embarcador'];
				$dados_parametros['codigo_transportador'] = $this->data['FichaScorecard']['codigo_transportador'];
				$dados_parametros['codigo_cliente'] = $this->data['FichaScorecard']['codigo_cliente'];
				$dados_parametros['cod_profissional'] =  $this->data['Profissional']['codigo_profissional'];
				$dados_parametros['profissional'] =   (!isset($this->data['Profissional']['codigo_documento']))?'0':$this->data['Profissional']['codigo_documento'];
				$dados_parametros['veiculo'] = (!isset($this->data['FichaScorecardVeiculo'][0]['Veiculo']['codigo_veiculo']))?'-1':$this->data['FichaScorecardVeiculo'][0]['Veiculo']['codigo_veiculo'];
				$dados_parametros['veiculo_placa'] = (!isset($this->data['FichaScorecardVeiculo'][0]['Veiculo']['placa']))?'-1':$this->data['FichaScorecardVeiculo'][0]['Veiculo']['placa'];
				$dados_parametros['carreta'] = (!isset($this->data['FichaScorecardVeiculo'][1]['Veiculo']['codigo_veiculo']))?'-1':$this->data['FichaScorecardVeiculo'][1]['Veiculo']['codigo_veiculo'];
				$dados_parametros['veiculo_carreta'] = (!isset($this->data['FichaScorecardVeiculo'][1]['Veiculo']['placa']))?'-1':$this->data['FichaScorecardVeiculo'][1]['Veiculo']['placa'];
				$dados_parametros['bitrem']  = (!isset($this->data['FichaScorecardVeiculo'][2]['Veiculo']['codigo_veiculo']))?'-1':$this->data['FichaScorecardVeiculo'][2]['Veiculo']['codigo_veiculo'];
				$dados_parametros['veiculo_bitrem'] = (!isset($this->data['FichaScorecardVeiculo'][2]['Veiculo']['placa']))?'-1':$this->data['FichaScorecardVeiculo'][2]['Veiculo']['placa'];
				$dados_parametros['cod_proprietario_veiculo'] = (!isset($this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_proprietario']))?'-1':$this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_proprietario'];
				$dados_parametros['proprietario_veiculo'] =(!isset($this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_documento']))?'-1':$this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_documento'];
				$dados_parametros['cod_proprietario_carreta'] = (!isset($this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_proprietario']))?'-1':$this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_proprietario'];
				$dados_parametros['proprietario_carreta'] =(!isset($this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_documento']))?'':$this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_documento'];
				$dados_parametros['cod_proprietario_bitrem'] = (!isset($this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_proprietario']))?'-1':$this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_proprietario'];
				$dados_parametros['proprietario_bitrem'] = (!isset($this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_documento']))?'-1':$this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_documento'];
				$dados_parametros['profissional_nome'] = $this->data['Profissional']['nome'];
				$this->VeiculoOcorrencia = & ClassRegistry::init('VeiculoOcorrencia');

				//Verificar se existe ocorrencia e gravar para mostrar o alert no javascript
				if ($dados_parametros['veiculo_placa']!='-1'){
					$conditionsVeic['Veiculo.placa'] = $dados_parametros['veiculo_placa'];
					$ocorr_vei = $this->VeiculoOcorrencia->historicoOcorrencia($conditionsVeic);
				}
				if ($dados_parametros['veiculo_carreta']!='-1'){
					$conditionsCar['Veiculo.placa'] = $dados_parametros['veiculo_carreta'];
					$ocorr_car = $this->VeiculoOcorrencia->historicoOcorrencia($conditionsCar);
				}
				if ($dados_parametros['veiculo_bitrem']!='-1'){
					$conditionsBi['Veiculo.placa'] = $dados_parametros['veiculo_bitrem'];
					$ocorr_bi = $this->VeiculoOcorrencia->historicoOcorrencia($conditionsBi);
				}

				if (count(@$ocorr_vei) > 0  or  count(@$ocorr_car) > 0  or count(@$ocorr_bi) >0){
					$dados_parametros['ExisteOcorrencia'] = 'S' ;
				}else{
					$dados_parametros['ExisteOcorrencia'] = 'N' ;
				}
				// Verificar se existe Profissional e Proprietario Negativados para mostrar o alert no javascript
				$this->loadModel('ProfissionalNegativacao');
				if ($dados_parametros['cod_profissional'] !='-1'){
					$prof_neg = $this->ProfissionalNegativacao->historicoOcorrencia(array('Profissional.codigo_documento'=>$dados_parametros['profissional']));
				}
				if ($dados_parametros['cod_proprietario_veiculo']!='-1'){
					$prop_vei_neg = $this->ProfissionalNegativacao->historicoOcorrencia(array('Profissional.codigo_documento'=>$dados_parametros['proprietario_veiculo']));
				}
				if ($dados_parametros['cod_proprietario_carreta']!='-1'){
					$prop_car_neg = $this->ProfissionalNegativacao->historicoOcorrencia(array('Profissional.codigo_documento'=>$dados_parametros['proprietario_carreta']));
				}
				if ($dados_parametros['cod_proprietario_bitrem']!='-1'){
					$prop_bi_neg = $this->ProfissionalNegativacao->historicoOcorrencia(array('Profissional.codigo_documento'=>$dados_parametros['proprietario_bitrem']));
				} 	
				if (count(@$prof_neg) > 0 or count(@$prop_vei_neg) >0 or count(@$prop_car_neg) >0 or  count(@$prop_bi_neg) >0) { 
					$dados_parametros['ExisteOcorrenciaProf'] = 'S' ;
				}else{
					$dados_parametros['ExisteOcorrenciaProf'] = 'N' ;
				}
				$this->data['tab_pane'] = "pesquisa";
				$this->Fichas->carregarCombos();
				// $this->carregarCombos($codigo_ficha);
				$observacao_supervisor = $dados_ficha['FichaScorecard']['observacao_supervisor'];
				$this->set(compact('dados_parametros','observacao_supervisor', 'extracao'));
				// $observacao_supervisor = $this->FichaScorecard->field('observacao_supervisor', array('codigo'=>$codigo_ficha));				
			}	
		} else {
			$data_inicio = $this->Session->read('data_inicio_log_atendimento');
			$dados_log_atendimento['LogAtendimento']['codigo_tipo_operacao'] = TipoOperacao::PESQUISA_PENDENTE;
			$dados_log_atendimento['LogAtendimento']['data_inicio'] = !empty($data_inicio) ? $data_inicio : date('Ymd H:i:s');
			$this->pendente( $codigo_ficha, $this->data );
			$this->LogAtendimento->incluir( $dados_log_atendimento );
			// $this->redirect(array('controller'=>'fichas_scorecard', 'action' => 'fichas_a_pesquisar'));
			$success = true;
		}
		$this->set(compact('success'));
	}
	
	function aprovar( $codigo_ficha = null ) {
		$this->layout = 'new_window';
	   if (empty($codigo_ficha)) {
        	// $this->redirect(array('controller'=>'fichas_scorecard', 'action' => 'fichas_a_aprovar'));
			$success = true;
			$this->set(compact('success'));
        }else{
        	$success = false;
		    $this->pageTitle 		 = 'Aprovar Ficha - ScoreCard';
		  	$authUsuario 			 = $this->BAuth->user();
			$codigo_usuario_inclusao = $authUsuario['Usuario']['codigo'];
			$this->carrega_cabecalho_ficha($codigo_ficha); 
			if (!empty($this->data)) {
				if (isset($this->params['form']['aprovar'])) {
					if( isset($this->data['FichaScorecard']['codigo_parametro_score']) ){
						switch ( $this->data['FichaScorecard']['codigo_parametro_score'] ) {
							case ParametroScore::OURO :
								$codigo_tipo_operacao = TipoOperacao::APROVA_MOTORISTA;
							break;
							case ParametroScore::INSUFICIENTE :
								$codigo_tipo_operacao = TipoOperacao::INSUFICIENCIA_DADOS_MOTORISTA;
							break;
							case ParametroScore::DIVERGENTE :
								$codigo_tipo_operacao = TipoOperacao::REPROVA_MOTORISTA;
							break;
						}
					}
					$this->FichaScorecard->pendenteAprovacaoFicha($codigo_ficha, $this->data['FichaStatusCriterio']['observacao_supervisor'], $this->data );  
					$this->FichaScorecard->atualizaStatus($codigo_ficha, FichaScorecardStatus::FINALIZADA );
					$this->envio_email_resultado($codigo_ficha);
				} elseif (isset($this->params['form']['pendente'])) {
                    $this->FichaScorecard->pendenteAprovacaoFicha($codigo_ficha, $this->data['FichaStatusCriterio']['observacao_supervisor'], $this->data );
				    $this->FichaScorecard->atualizaPendente($codigo_ficha);
					$codigo_tipo_operacao = TipoOperacao::PESQUISA_PENDENTE;
				} elseif (isset($this->params['form']['reprovar'])) { 
					$this->data['FichaScorecard']['observacao_supervisor'] = $this->data['FichaStatusCriterio']['observacao_supervisor'];
					$this->FichaScorecard->reprovarFicha($codigo_ficha, $this->data['FichaScorecard']['observacao_supervisor'], $this->data['FichaScorecard']['codigo_usuario_responsavel']);
					$codigo_tipo_operacao = TipoOperacao::NOVA_PESQUISA;
				} elseif (isset($this->params['form']['recalcular'])) {
					switch ( $this->data['FichaScorecard']['codigo_parametro_score'] ) {
						case ParametroScore::OURO :
							$codigo_tipo_operacao = TipoOperacao::APROVA_MOTORISTA;
						break;
						case ParametroScore::INSUFICIENTE :
							$codigo_tipo_operacao = TipoOperacao::INSUFICIENCIA_DADOS_MOTORISTA;
						break;
						case ParametroScore::DIVERGENTE :
							$codigo_tipo_operacao = TipoOperacao::REPROVA_MOTORISTA;
						break;
					}					
					$this->data['FichaScorecard']['codigo_score_manual'] = $this->data['FichaScorecard']['codigo_parametro_score'];
					$this->ParametroScore = ClassRegistry::init('ParametroScore');
					$parametros_score = $this->ParametroScore->carregar( $this->data['FichaScorecard']['codigo_parametro_score'] );
					$total_pontos 	  = ($parametros_score['ParametroScore']['pontos'] > 0 ? $parametros_score['ParametroScore']['pontos'] : 0);
					$this->data['FichaScorecard']['total_pontos'] = $total_pontos;
					$this->data['FichaScorecard']['percentual_pontos'] = $total_pontos;
					$this->data['FichaScorecard']['resumo']	= $this->data['FichaStatusCriterio']['resumo'];					
					unset($this->data['FichaScorecard']['codigo_parametro_score']);
					unset($this->data['FichaStatusCriterio']['resumo']);
					$this->data['FichaScorecard']['codigo'] = $codigo_ficha;
					$this->data['FichaScorecard']['codigo_status'] = 7;
					$data = $this->data['FichaScorecard'];
					$this->FichaScorecard->save( $data );					
				    
				    unset($this->data['FichaStatusCriterio']['observacao_supervisor']);
				    unset($this->data['FichaStatusCriterio']['BotaoClicado']);				    
				    $dados 			 = $this->FichaStatusCriterio->formatarDados( $this->data, $codigo_ficha, $_SESSION['Auth']['Usuario']['codigo'] );
				    $salva_criterios = $this->FichaStatusCriterio->salvarFichaStatusCriterio( $codigo_ficha, $dados, TRUE );
				    $gravar_pontos   = $this->FichaStatusCriterio->atualizarParaGravarPontosCriterio( $codigo_ficha, TRUE );
				}
		    	//Inclusao do log de atendimento para a aprovação e reprovação profissional
		        $codigo_profissional 	  = $this->FichaScorecard->buscaCodigoProfissional($codigo_ficha);
		        $codigo_profissional_tipo =  $this->FichaScorecard->buscaTipoProfissional($codigo_ficha);				
				$this->LogAtendimento  		= ClassRegistry::init('LogAtendimento');		
				$authUsuario 			 	= $this->BAuth->user();
				$codigo_usuario_inclusao 	= $authUsuario['Usuario']['codigo'];
				$dados_log_atendimento 	= array(
					'LogAtendimento'   => array(
						'codigo_produto'        	=> Produto::SCORECARD,
						'codigo_profissional'   	=> $codigo_profissional,
						'codigo_profissional_tipo' 	=> $codigo_profissional_tipo,
						'codigo_tipo_operacao' 		=> $codigo_tipo_operacao
				)) ;
				//Gravo Log de Atendimento
				$data_inicio = $this->Session->read('data_inicio_log_atendimento');
				$dados_log_atendimento['LogAtendimento']['data_inicio'] = !empty($data_inicio) ? $data_inicio : date('Ymd H:i:s');
				$this->LogAtendimento->incluir( $dados_log_atendimento );
				// $this->redirect(array('controller'=>'fichas_scorecard', 'action' => 'fichas_a_aprovar'));	
				$success = true;				
			} else {
				$this->Session->write('data_inicio_log_atendimento', date("Ymd H:i:s") );
				if( !$this->FichaScorecard->atualizaStatus($codigo_ficha, FichaScorecardStatus::EM_APROVACAO, $codigo_usuario_inclusao, 'aprovar') ){
					$this->BSession->setFlash(array(MSGT_ERROR, 'A ficha já está em aprovação!')); 
					// $this->redirect(array('controller' => 'fichas_scorecard', 'action' => 'fichas_a_aprovar'));					
					$success = true;
				}
				$this->data = $this->FichaStatusCriterio->listarRespostasFicha($codigo_ficha);
		        $insuficiente = $this->FichaStatusCriterio->verificarCamposInsuficientesFicha( $this->data );
			}
			//Retorna um array contendo o criterio que esta insuficiente
			$campos_insuficientes 	= $this->FichaStatusCriterio->retornaCamposInsuficientesFicha( $this->data );
            $campos_divergentes   	= $this->FichaStatusCriterio->retornaCamposDivergentesFicha( $this->data );

			$pontuacao 				= $this->FichaScorecard->buscarPontuacao( $codigo_ficha );
            $alteracao_manual 		= FALSE;
            if (count($campos_divergentes) == 0 && $pontuacao['nivel'] == 'Divergente' )
            	$alteracao_manual 	= TRUE;
            if (count($campos_insuficientes)== 0 && $pontuacao['nivel'] == 'Insuficiente')
            	$alteracao_manual 	= TRUE;
			if( !in_array( $pontuacao['codigo_parametro_score'], array(ParametroScore::INSUFICIENTE, ParametroScore::DIVERGENTE) ) && ( count($campos_divergentes) > 0 || count($campos_insuficientes) > 0)  )
				$alteracao_manual 	= TRUE;
			if( FichaScorecard::ENVIA_EMAIL_SCORECARD ){
				$score_checked = $pontuacao['codigo_parametro_score'];
			} else {
				$dados_ficha   = $this->FichaScorecard->carregar($codigo_ficha);
				$score_checked = ($dados_ficha['FichaScorecard']['codigo_score_manual'] < 7 ? 2 : $dados_ficha['FichaScorecard']['codigo_score_manual'] );
			}			
            $this->set(compact('campos_divergentes','campos_insuficientes','dados_parametros','referer','codigo_ficha','pontuacao', 'score_checked','alteracao_manual'));
			// $this->carregarCombos($codigo_ficha);
			$this->Fichas->carregarDadosFicha($codigo_ficha);
            $dados_parametros['codigo_embarcador'] 			= $this->data['FichaScorecard']['codigo_embarcador'];
            $dados_parametros['codigo_transportador'] 		= $this->data['FichaScorecard']['codigo_transportador'];
			$dados_parametros['codigo_cliente'] 			= $this->data['FichaScorecard']['codigo_cliente'];
			$dados_parametros['cod_profissional'] 			= $this->data['Profissional']['codigo_profissional'];
            $dados_parametros['profissional'] 				= (!isset($this->data['Profissional']['codigo_documento']))?'0':$this->data['Profissional']['codigo_documento'];
            $dados_parametros['veiculo'] 					= (!isset($this->data['FichaScorecardVeiculo'][0]['Veiculo']['codigo_veiculo']))?'-1':$this->data['FichaScorecardVeiculo'][0]['Veiculo']['codigo_veiculo'];
			$dados_parametros['veiculo_placa'] 				= (!isset($this->data['FichaScorecardVeiculo'][0]['Veiculo']['placa']))?'-1':$this->data['FichaScorecardVeiculo'][0]['Veiculo']['placa'];
			$dados_parametros['carreta'] 					= (!isset($this->data['FichaScorecardVeiculo'][1]['Veiculo']['codigo_veiculo']))?'-1':$this->data['FichaScorecardVeiculo'][1]['Veiculo']['codigo_veiculo'];
			$dados_parametros['veiculo_carreta'] 			= (!isset($this->data['FichaScorecardVeiculo'][1]['Veiculo']['placa']))?'-1':$this->data['FichaScorecardVeiculo'][1]['Veiculo']['placa'];
			$dados_parametros['bitrem']  					= (!isset($this->data['FichaScorecardVeiculo'][2]['Veiculo']['codigo_veiculo']))?'-1':$this->data['FichaScorecardVeiculo'][2]['Veiculo']['codigo_veiculo'];
			$dados_parametros['veiculo_bitrem'] 			= (!isset($this->data['FichaScorecardVeiculo'][2]['Veiculo']['placa']))?'-1':$this->data['FichaScorecardVeiculo'][2]['Veiculo']['placa'];
			$dados_parametros['cod_proprietario_veiculo'] 	= (!isset($this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_proprietario']))?'-1':$this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_proprietario'];
			$dados_parametros['proprietario_veiculo'] 		= (!isset($this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_documento']))?'-1':$this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_documento'];
			$dados_parametros['cod_proprietario_carreta'] 	= (!isset($this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_proprietario']))?'-1':$this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_proprietario'];
  		    $dados_parametros['proprietario_carreta'] 		= (!isset($this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_documento']))?'':$this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_documento'];
 		    $dados_parametros['cod_proprietario_bitrem'] 	= (!isset($this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_proprietario']))?'-1':$this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_proprietario'];
		    $dados_parametros['proprietario_bitrem'] 		= (!isset($this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_documento']))?'-1':$this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_documento'];
			$dados_parametros['profissional_nome'] 			= $this->data['Profissional']['nome'];
        	$dados_parametros['observacao_supervisor']    	= $this->data['FichaScorecard']['observacao_supervisor'];
        	$this->set(compact('dados_parametros','codigo_ficha','pontuacao','campos_divergentes','insuficiente', 'campos_insuficientes', 'success'));
			$this->Fichas->carregarCombos();
		}
	}

	function resultado_ficha($codigo_ficha, $nova_janela = null){		
		$this->pageTitle  = 'Resultado da Pesquisa - ScoreCard';
		if($nova_janela)
			$this->layout = 'new_window';
		$this->carrega_cabecalho_ficha($codigo_ficha);
		$this->data = $this->FichaStatusCriterio->listarRespostasFicha($codigo_ficha);
		$pontuacao = $this->FichaScorecard->buscarPontuacao($codigo_ficha);
		$referer = $this->referer();
		$this->Fichas->carregarDadosFicha($codigo_ficha);
		$this->Fichas->carregarCombos();
	    $dados_parametros['codigo_embarcador']        = (isset($this->data['FichaScorecard']['codigo_embarcador'])    ? $this->data['FichaScorecard']['codigo_embarcador']    : NULL);
        $dados_parametros['codigo_transportador']     = (isset($this->data['FichaScorecard']['codigo_transportador']) ? $this->data['FichaScorecard']['codigo_transportador'] : NULL);
		$dados_parametros['codigo_cliente']           = (isset($this->data['FichaScorecard']['codigo_cliente'])       ? $this->data['FichaScorecard']['codigo_cliente']       : NULL);
		$dados_parametros['cod_profissional']         = (isset($this->data['Profissional']['codigo_profissional'])    ? $this->data['Profissional']['codigo_profissional']    : NULL);
        $dados_parametros['profissional']             = (!isset($this->data['Profissional']['codigo_documento']))?'':$this->data['Profissional']['codigo_documento'];
        $dados_parametros['veiculo']                  = (!isset($this->data['FichaScorecardVeiculo'][0]['Veiculo']['codigo_veiculo']))?'':$this->data['FichaScorecardVeiculo'][0]['Veiculo']['codigo_veiculo'];
		$dados_parametros['carreta']                  = (!isset($this->data['FichaScorecardVeiculo'][1]['Veiculo']['codigo_veiculo']))?'':$this->data['FichaScorecardVeiculo'][1]['Veiculo']['codigo_veiculo'];
		$dados_parametros['bitrem']                   = (!isset($this->data['FichaScorecardVeiculo'][2]['Veiculo']['codigo_veiculo']))?'':$this->data['FichaScorecardVeiculo'][2]['Veiculo']['codigo_veiculo'];
		$dados_parametros['cod_proprietario_veiculo'] = (!isset($this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_proprietario']))?'':$this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_proprietario'];
		$dados_parametros['proprietario_veiculo']     = (!isset($this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_documento']))?'':$this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_documento'];
		$dados_parametros['cod_proprietario_carreta'] = (!isset($this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_proprietario']))?'':$this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_proprietario'];
  		$dados_parametros['proprietario_carreta']     = (!isset($this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_documento']))?'':$this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_documento'];
 		$dados_parametros['cod_proprietario_bitrem']  = (!isset($this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_proprietario']))?'':$this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_proprietario'];
		$dados_parametros['proprietario_bitrem']      = (!isset($this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_documento']))?'':$this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_documento'];
		$dados_parametros['profissional_nome']        = (isset($this->data['Profissional']['nome']) ? $this->data['Profissional']['nome'] : NULL);
        $dados_parametros['observacao_supervisor']    = (isset($this->data['FichaScorecard']['observacao_supervisor'])   ? $this->data['FichaScorecard']['observacao_supervisor']   : NULL);
        $dados_parametros['justificativa_alteracao']  = (isset($this->data['FichaScorecard']['justificativa_alteracao']) ? $this->data['FichaScorecard']['justificativa_alteracao'] : NULL);

        /*
        if( empty($this->data['FichaScorecard']['codigo_usuario_em_pesquisa']) || empty($this->data['FichaScorecard']['codigo_usuario_em_aprovacao']) ){
			$this->LogAtendimento =& ClassRegistry::init('LogAtendimento');
			$dados = array(
				'LogAtendimento' => array(
				'codigo_tipo_operacao' 	=> TipoOperacao::PESQUISA_INTERROMPIDA,
				'codigo_produto'        => Produto::SCORECARD,
				'codigo_profissional' 	=> $this->data['Profissional']['codigo_profissional'],
				'codigo_profissional_tipo' 	=> $this->data['FichaScorecard']['codigo_profissional_tipo']
			));
			//Log Atendente
			$this->LogAtendimento->incluir($dados);
        }*/

        //Retorna um array contendo o criterio que esta insuficiente
		$campos_insuficientes = $this->FichaStatusCriterio->retornaCamposInsuficientesFicha($this->data);
        $campos_divergentes   = $this->FichaStatusCriterio->retornaCamposDivergentesFicha($this->data);
        if ( count($campos_insuficientes) == 0 && $pontuacao['codigo_parametro_score'] == ParametroScore::INSUFICIENTE )
           $campos_insuficientes[0] = 'O Status da ficha foi alterada para insuficiente manualmente.';
        if (count($campos_divergentes) == 0 && $pontuacao['codigo_parametro_score'] == ParametroScore::DIVERGENTE)
        	$campos_divergentes[0] = 'O Status da ficha foi alterada para divergente manualmente.';
        $this->set(compact('campos_divergentes','campos_insuficientes','dados_parametros','referer','codigo_ficha','pontuacao', 'nova_janela'));
	}
	
	private function carrega_cabecalho_ficha($codigo_ficha){
		$FichaScorecardVeiculo = ClassRegistry::init('FichaScorecardVeiculo');
		$dados_ficha         = $this->FichaScorecard->carregar($codigo_ficha);
		$codigo_profissional = $this->FichaScorecard->buscaCodigoProfissional($codigo_ficha);
		$profissional 		 = $this->Profissional->carregar($codigo_profissional);		
		$codigo_cliente 	 = $this->FichaScorecard->buscaCodigoCliente($codigo_ficha);
		$cliente 			 = $this->Cliente->carregar($codigo_cliente);		
		$criterios_categoria = $this->PontuacoesStatusCriterio->listarCriteriosCategoria( $dados_ficha );
		$resumo_ficha		 = $this->FichaScorecard->buscaResumo($codigo_ficha);		
		$validade_ult_ficha  = $this->FichaScorecard->buscaValidade($codigo_ficha);
		$embarcador          = $this->FichaScorecard->buscaEmbarcador($codigo_ficha);
		$transportador       = $this->FichaScorecard->buscaTransportador($codigo_ficha);
		//Ultima ficha Independente do CLIENTE
		$ultima_ficha		 = $this->FichaScorecard->carregaFichaAnteriorProfissional( $codigo_profissional, FALSE, $codigo_ficha );
		$ultima_consulta     = isset($ultima_ficha['FichaScorecard']['data_inclusao']) ? $ultima_ficha['FichaScorecard']['data_inclusao'] : NULL;
		$codigo_ultima_ficha = isset($ultima_ficha['FichaScorecard']['codigo']) ? $ultima_ficha['FichaScorecard']['codigo'] : NULL;
		$penultima_ficha     = NULL;
		$criterios           = array();
		$status_anterior_profissional_proprietario = NULL;
		//Ultima ficha Independente do CLIENTE
		if( $codigo_ultima_ficha )
			$penultima_ficha = $this->FichaScorecard->carregaFichaAnteriorProfissional( $codigo_profissional, FALSE, $codigo_ultima_ficha );
		$status_anterior_profissional = $this->retornaScoreProfissional( $ultima_ficha['FichaScorecard']['codigo_parametro_score'], $ultima_ficha['FichaScorecard']['codigo_profissional_tipo'] );

		if( $ultima_ficha ){
			$dados_scorecard_veiculo = $FichaScorecardVeiculo->find('first', array('conditions' => array('FichaScorecardVeiculo.codigo_ficha_scorecard'=> $ultima_ficha['FichaScorecard']['codigo'] )) );
			$codigo_proprietario_log = $dados_scorecard_veiculo['FichaScorecardVeiculo']['codigo_proprietario_log'];
			$dados_proprietario = $this->ProprietarioLog->find('first', array('conditions' => array( 'ProprietarioLog.codigo' => $codigo_proprietario_log) ));
			if( isset($dados_proprietario['Proprietario']['codigo_documento']) ){
				$profissional_proprietario = $this->Profissional->buscaPorCPF( $dados_proprietario['Proprietario']['codigo_documento'] );		
				$codigo_profissional_proprietario = $profissional_proprietario['Profissional']['codigo'];
				$ultima_ficha_proprietario = $this->FichaScorecard->carregaFichaAnteriorProfissional( $codigo_profissional_proprietario, NULL, $codigo_ficha );
				$status_anterior_profissional_proprietario = $this->retornaScoreProfissional( $ultima_ficha_proprietario['FichaScorecard']['codigo_parametro_score'], $ultima_ficha_proprietario['FichaScorecard']['codigo_profissional_tipo'] );
			}
		}
		if( !empty($criterios_categoria) ){
			foreach( $criterios_categoria as $key=> $dados ) {
				$criterios[$dados['codigo']] = $dados;
			}
		}
		$criterios = $this->organiza_criterios_score( $codigo_ficha, $criterios );
		$this->set(compact('penultima_ficha','ultima_consulta','transportador','embarcador','validade_ult_ficha','codigo_cliente','codigo_ficha','dados_ficha',
			'profissional','cliente','criterios','resumo_ficha', 'status_anterior_profissional', 'ultima_ficha', 'status_anterior_profissional_proprietario'));
	}

	function organiza_criterios_score( $codigo_ficha, $criterios ){
		$this->FichaScorecardVeiculo =& ClassRegistry::init('FichaScorecardVeiculo');
		$veiculos = $this->FichaScorecardVeiculo->find('list', array('fields'=>'tipo','conditions'=>array('codigo_ficha_scorecard'=>$codigo_ficha), 'order'=>array('tipo ASC')));
		$tipo_veiculos = array( 0 =>FALSE, 1=>FALSE, 2=>FALSE );
		if( isset($veiculos) && count($veiculos) > 0 ){
			foreach( $veiculos as $codigo_criterio ){
				if( isset($codigo_criterio) ){
					$tipo_veiculos[$codigo_criterio] = TRUE;
				}
			}
			foreach( $tipo_veiculos as $tipo_veiculo => $value ){
				if( $value === FALSE )
					$criterios = $this->remove_criterio_veiculo( $criterios, $tipo_veiculo );
			}
		} else {//Nao tem veiculo
			foreach ( array(5,6,23,25,27,29,21,26,28) as $codigo_criterio ){
				unset($criterios[$codigo_criterio]);
			}
		}
		return $criterios;
	}


	function remove_criterio_veiculo( $criterios, $tipo_veiculo ){
		switch ( $tipo_veiculo ) {
			case 1:
				$criterios_carreta = array(21,26);
				foreach ( $criterios_carreta as $codigo_criterio ){
					unset($criterios[$codigo_criterio]);
				}
				break;			
			case 2:
				$bitrem  = array(25,27,29);
				foreach ( $bitrem as $codigo_criterio ){
					unset($criterios[$codigo_criterio]);
				}
				break;			
		}
		return $criterios;
	}

	function listagem(){
		$this->layout = 'ajax';
		$filtros      = $this->Filtros->controla_sessao($this->data, 'FichaStatusCriterio');
		$dados 		  = $this->FichaStatusCriterio->listarResultados($filtros);
		$this->set(compact('dados'));
	}
		

	function listagem_cliente(){
		$this->layout = 'ajax';
		$dados = array();
		$filtros = $this->Filtros->controla_sessao($this->data, 'FichaStatusCriterio');
		if (isset($_SESSION['action_pesq'])){
			if ($_SESSION['action_pesq']=='resultados_pesquisa_cliente'){
	           $action=$_SESSION['action_pesq'];
	           //unset($_SESSION['action_pesq']);
			}
	    }		
		
		if (strlen($filtros['codigo_documento']) > 10)
		    $dados = $this->FichaStatusCriterio->listarResultados($filtros);
		$this->set(compact('dados','action'));
		$this->render('listagem');
	}
	
    function listagem_cliente_resultado_pesquisa(){
		$this->layout = 'ajax';
		$dados = array();
		$filtros = $this->Filtros->controla_sessao($this->data, 'FichaStatusCriterio');
		if (strlen($filtros['codigo_documento']) > 10)
		    $dados = $this->FichaStatusCriterio->listarResultados($filtros);
		$this->set(compact('dados'));
		$this->render('listagem');
	}


	public function resultados_pesquisa(){
        $this->pageTitle 	= 'Resultados - ScoreCard';
        $classificacao 		= $this->ParametroScore->find('list');
        $lista_seguradora 	= $this->Seguradora->find('list');
        $this->set(compact('classificacao','lista_seguradora'));
    }
    
    public function resultados_pesquisa_cliente(){ 
        $this->pageTitle 	= 'Resultados Profissional - ScoreCard';
        $_SESSION['action_pesq'] = 'resultados_pesquisa_cliente';
    }
	
    public function envio_email_resultado($codigo_ficha) {
    	
    	$this->layout = "ajax";
    	App::import('Component', array('StringView', 'Mailer.Scheduler'));
    	$this->LoadModel('EnderecoCidade');    	
    	$this->StringView = new StringViewComponent();
    	$this->Scheduler  = new SchedulerComponent();
		$dados_email = $this->FichaScorecard->buscarDadosEmailResultado($codigo_ficha);
        $dados = $dados_email[0];
        $dados['Profissional']['nome']    =$dados['ProfissionalLog']['nome'];
        $dados['Profissional']['codigo_documento'] =$dados['ProfissionalLog']['codigo_documento'];
        $dados['Profissional']['rg'] =$dados['ProfissionalLog']['rg'];
        $origem = $this->EnderecoCidade->combo_cidade($dados['FichaScorecard']['codigo_endereco_cidade_carga_origem']);
        if ($dados['FichaScorecard']['codigo_endereco_cidade_carga_origem']==''){
        	$dados['FichaScorecard']['origem'] = 'NÃO INFORMADO';
        }
        $destino = $this->EnderecoCidade->combo_cidade($dados['FichaScorecard']['codigo_endereco_cidade_carga_destino']);
        if ($dados['FichaScorecard']['codigo_endereco_cidade_carga_destino']==''){
        	$dados['FichaScorecard']['destino'] = 'NÃO INFORMADO';
        }
        $dados['FichaScorecard']['origem'] = $origem['EnderecoCidade']['descricao'].' - '.$origem['EnderecoEstado']['abreviacao'];
        $dados['FichaScorecard']['destino'] = $destino['EnderecoCidade']['descricao'].' - '.$destino['EnderecoEstado']['abreviacao'];
        $dados['FichaScorecard']['nivel'] =$dados['ParametroScore']['nivel'];
        $dados['FichaScorecard']['valor'] =$dados['ParametroScore']['valor'];
		
        //Retorna um array contendo o criterio que esta insuficiente
        $this->data = $this->FichaStatusCriterio->listarRespostasFicha($codigo_ficha);
		$campos_insuficientes = $this->FichaStatusCriterio->retornaCamposInsuficientesFicha($this->data);
		$campos_divergentes   = $this->FichaStatusCriterio->retornaCamposDivergentesFicha($this->data);
        $dados['Criterios']['insuficientes'] = $campos_insuficientes;
        $dados['Criterios']['divergentes']   = $campos_divergentes;
		$dados['codigo_faturamento'] = ClassRegistry::init('LogFaturamentoTeleconsult')->obterUltimoCodigoLogFaturamentoPorCliente($dados['Cliente']['codigo'],$dados['ProfissionalLog']['codigo_profissional']);
		$this->CargaTipo = ClassRegistry::init('CargaTipo');
		$tipo_carga = $this->CargaTipo->carregar($dados['FichaScorecard']['codigo_carga_tipo']);
		$dados['TipoCarga'] = $tipo_carga['CargaTipo']['descricao'];
		if (!empty($dados['FichaScorecard']['codigo_transportador'])){
			$dados_transportador = $this->Cliente->carregar( $dados['FichaScorecard']['codigo_transportador'] );    
			$dados['Transportador']['codigo'] = $dados_transportador['Cliente']['codigo'];
			$dados['Transportador']['razao_social'] = $dados_transportador['Cliente']['razao_social'];
		}
        if(!empty($dados['FichaScorecard']['codigo_embarcador'])){
	        $dados_embarcador = $this->Cliente->carregar( $dados['FichaScorecard']['codigo_embarcador'] );    
	        $dados['Embarcador']['codigo'] = $dados_embarcador['Cliente']['codigo'];
	        $dados['Embarcador']['razao_social'] = $dados_embarcador['Cliente']['razao_social'];
	    }
	    if(!empty($dados['FichaScorecard']['codigo_endereco_cidade_carga_origem'])){
          $dados['CidadeOrigem'] = $dados['FichaScorecard']['codigo_endereco_cidade_carga_origem'];
        }
        if(!empty($dados['FichaScorecard']['codigo_endereco_cidade_carga_destino'])){
          $dados['CidadeDestino'] = $dados['FichaScorecard']['codigo_endereco_cidade_carga_destino'];
        } 
		$codigo_log_faturamento = 'XXXXX';
        if (isset($dados['FichaScorecard']['total_pontos']) && $dados['FichaScorecard']['total_pontos'] > 0 ){
	        $codigo_log_faturamento = ClassRegistry::init('LogFaturamentoTeleconsult')->obterUltimoCodigoLogFaturamentoPorCliente($dados['Cliente']['codigo'],$dados['ProfissionalLog']['codigo_profissional']);
        }
        $envio_email_scorecard = FichaScorecard::ENVIA_EMAIL_SCORECARD;
    	// $this->retornaCorpoEmailScorecard( $dados, $codigo_log_faturamento );
     //    die();
        $this->StringView->set(compact('dados','codigo_log_faturamento', 'envio_email_scorecard'));
        $content = $this->StringView->renderMail( 'email_scorecard_resultado', 'default' );
        $options = array(
            'from' => 'portal@buonny.com.br',
            'sent' => null,
            'to'   => 'tiago.lopes@buonny.com.br',
            'subject' => 'Resultado Pesquisa - Ficha ScoreCard',
        );        
        $retorno = $this->Scheduler->schedule($content, $options) ? true: false;
        return $retorno; 
	}

	function alterar_score( $codigo_ficha ) {	   
	    $this->pageTitle 	= 'Aprovar Ficha - ScoreCard';
	  	$authUsuario 		= $this->BAuth->user();
		$this->carrega_cabecalho_ficha($codigo_ficha);
		if (!empty($this->data)) {
			if($this->FichaScorecard->alterarScore($this->data)){
				$this->BSession->setFlash('save_success');
				$this->redirect(array('controller'=>'fichas_scorecard', 'action' => 'index_fichas_finalizadas'));
			} else {
				$this->BSession->setFlash('save_error');
			}			
		}
		$pontuacao  = $this->FichaScorecard->buscarPontuacao( $codigo_ficha );			
		$this->data = $this->FichaStatusCriterio->listarRespostasFicha($codigo_ficha);
		if( FichaScorecard::ENVIA_EMAIL_SCORECARD ){
			$score_checked = $pontuacao['codigo_parametro_score'];
		} else {
			$dados_ficha   = $this->FichaScorecard->carregar($codigo_ficha);
			$score_checked = ($dados_ficha['FichaScorecard']['codigo_score_manual'] < 7 ? 2 : $dados_ficha['FichaScorecard']['codigo_score_manual'] );
		}			
    	$this->set(compact('dados_parametros','codigo_ficha','pontuacao','campos_divergentes','insuficiente', 'campos_insuficientes', 'score_checked'));		    
		$this->Fichas->carregarCombos();		
	}

    function visualizar($codigo_ficha){
    	$this->pageTitle 		= 'Dados da Ficha Log - ScoreCard';
		$this->carrega_cabecalho_ficha($codigo_ficha);
		$this->data = $this->FichaStatusCriterio->listarRespostasFicha( $codigo_ficha );
		$pontuacao  = $this->FichaScorecard->buscarPontuacao($codigo_ficha);		
		$referer    = $this->referer();
		$this->Fichas->carregarDadosFicha($codigo_ficha);
		$this->Fichas->carregarCombos();		
	    $dados_parametros['codigo_embarcador']        = (isset($this->data['FichaScorecard']['codigo_embarcador'])    ? $this->data['FichaScorecard']['codigo_embarcador']    : NULL);
        $dados_parametros['codigo_transportador']     = (isset($this->data['FichaScorecard']['codigo_transportador']) ? $this->data['FichaScorecard']['codigo_transportador'] : NULL);
		$dados_parametros['codigo_cliente']           = (isset($this->data['FichaScorecard']['codigo_cliente'])       ? $this->data['FichaScorecard']['codigo_cliente']       : NULL);
		$dados_parametros['cod_profissional']         = (isset($this->data['Profissional']['codigo_profissional'])    ? $this->data['Profissional']['codigo_profissional']    : NULL);
        $dados_parametros['profissional']             = (!isset($this->data['Profissional']['codigo_documento']))?'':$this->data['Profissional']['codigo_documento'];
        $dados_parametros['veiculo']                  = (!isset($this->data['FichaScorecardVeiculo'][0]['Veiculo']['codigo_veiculo']))?'':$this->data['FichaScorecardVeiculo'][0]['Veiculo']['codigo_veiculo'];
		$dados_parametros['carreta']                  = (!isset($this->data['FichaScorecardVeiculo'][1]['Veiculo']['codigo_veiculo']))?'':$this->data['FichaScorecardVeiculo'][1]['Veiculo']['codigo_veiculo'];
		$dados_parametros['bitrem']                   = (!isset($this->data['FichaScorecardVeiculo'][2]['Veiculo']['codigo_veiculo']))?'':$this->data['FichaScorecardVeiculo'][2]['Veiculo']['codigo_veiculo'];
		$dados_parametros['cod_proprietario_veiculo'] = (!isset($this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_proprietario']))?'':$this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_proprietario'];
		$dados_parametros['proprietario_veiculo']     = (!isset($this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_documento']))?'':$this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_documento'];
		$dados_parametros['cod_proprietario_carreta'] = (!isset($this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_proprietario']))?'':$this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_proprietario'];
  		$dados_parametros['proprietario_carreta']     = (!isset($this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_documento']))?'':$this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_documento'];
 		$dados_parametros['cod_proprietario_bitrem']  = (!isset($this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_proprietario']))?'':$this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_proprietario'];
		$dados_parametros['proprietario_bitrem']      = (!isset($this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_documento']))?'':$this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_documento'];
		$dados_parametros['profissional_nome']        = (isset($this->data['Profissional']['nome']) ? $this->data['Profissional']['nome'] : NULL);
        $dados_parametros['observacao_supervisor']    = (isset($this->data['FichaScorecard']['observacao_supervisor'])   ? $this->data['FichaScorecard']['observacao_supervisor']   : NULL);
        $dados_parametros['justificativa_alteracao']  = (isset($this->data['FichaScorecard']['justificativa_alteracao']) ? $this->data['FichaScorecard']['justificativa_alteracao'] : NULL);
        //Retorna um array contendo o criterio que esta insuficiente		
		$campos_insuficientes = $this->FichaStatusCriterio->retornaCamposInsuficientesFicha($this->data);
        $campos_divergentes   = $this->FichaStatusCriterio->retornaCamposDivergentesFicha($this->data);
        if ( count($campos_insuficientes) == 0 && $pontuacao['codigo_parametro_score'] == ParametroScore::INSUFICIENTE )
           $campos_insuficientes[0] = 'O Status da ficha foi alterada para insuficiente manualmente.';
        if (count($campos_divergentes) == 0 && $pontuacao['codigo_parametro_score'] == ParametroScore::DIVERGENTE)
        	$campos_divergentes[0] = 'O Status da ficha foi alterada para divergente manualmente.';
        $this->set(compact('campos_divergentes','campos_insuficientes','dados_parametros','referer','codigo_ficha','pontuacao'));

    }

    function carregarCombos($codigo_ficha){
		$observacao_supervisor = $this->FichaScorecard->field('observacao_supervisor', array('codigo'=>$codigo_ficha));
		$extracao  = $this->FichaScorecard->field('extracao', array('codigo'=>$codigo_ficha));
		$extracao  = explode(',',$extracao);
		$condition = array('FichaScorecard.codigo'=>$codigo_ficha);
        $fichascor = $this->FichaScorecard->listarFichas($condition);
        $proprietario   = $fichascor[0]['ProprietarioLog']['nome_razao_social'];
        $codigo_proprie = $this->Proprietario->buscaCodigoProprietario( $fichascor[0]['ProprietarioLog']['codigo_documento'] );
        $codigo_prof    = $this->Profissional->listaMotoristaPorCPF( $fichascor[0]['ProfissionalLog']['codigo_documento'] );
		@$prof_negativacao = $this->ProfissionalNegativacao->find('first', 
			array('conditions' => array( 'codigo_profissional' => $codigo_prof['0']['Profissional']['codigo'] ))
		);
		$result_prof_negativacao = $prof_negativacao['ProfissionalNegativacao']['observacao'];
       
        //Pegar código do  Proprietario para telecheque .
        //$proprie_telecheque = $this->ProprietarioTelecheque->find('all');
        $proprie_telecheque = $this->ProprietarioTelecheque->find('first',
        	array(	'conditions' => array( 'codigo_proprietario' => $codigo_proprie ),
				  	'order'      => array( '' =>'codigo desc')
			)
		);
        
        @$prof_telecheque = $this->ProfissionalTelecheque->find('first',
        	array(	'conditions' => array( 'codigo_profissional' =>  $codigo_prof['0']['Profissional']['codigo'] ),
					'order' 	 => array('' =>'codigo desc')
			)
		);
       
        $proprie_serasa = $this->ProprietarioSerasa->find('first',
        	array(	'conditions' => array( 'codigo_proprietario' => $codigo_proprie ),
					'order' => array(''=>'ProprietarioSerasa.codigo desc'))
		);

        if (empty($proprie_serasa[0]['ProprietarioSerasa']['descricao'])){
        	$proprietario_serasa = "NADA CONSTA";
        }elseif ($proprie_serasa[0]['ProprietarioSerasa']['descricao']=='PEFIN/REFIN/Dívidas Vencidas'){
            $proprietario_serasa = "CONSTA : ".$proprie_serasa[0]['ProprietarioSerasa']['descricao']."- Quantidade :".$proprie_serasa[0]['ProprietarioSerasa']['quantidade_ocorrencias']." valor : ".$proprie_serasa[0]['ProprietarioSerasa']['valor_ocorrencias'];
        }elseif ($proprie_serasa[1]['ProprietarioSerasa']['descricao']=='Cheques sem fundo') {
            $proprietario_serasa = "\n"."CONSTA : ".$proprie_serasa[1]['ProprietarioSerasa']['descricao']."- Quantidade :".$proprie_serasa[1]['ProprietarioSerasa']['quantidade_ocorrencias']." valor : ".$proprie_serasa[1]['ProprietarioSerasa']['valor_ocorrencias'];
        }elseif ($proprie_serasa[2]['ProprietarioSerasa']['descricao']=='Cheques sem fundo') {
            $proprietario_serasa = "\n"."CONSTA : ".$proprie_serasa[2]['ProprietarioSerasa']['descricao']."- Quantidade :".$proprie_serasa[2]['ProprietarioSerasa']['quantidade_ocorrencias']." valor : ".$proprie_serasa[2]['ProprietarioSerasa']['valor_ocorrencias'];
        }
       
        if ($prof_telecheque['ProfissionalTelecheque']['nome_emitente']==''){
        	$result_prof_telecheque = "Consulta Profissional Telecheque : NADA CONSTA ";
        }else{
        	$result_prof_telecheque = "Consulta Profissional Telecheque : O profissional ".$prof_telecheque['ProfissionalTelecheque']['nome_emitente']." possui ".$prof_telecheque['ProfissionalTelecheque']['quantidade_ocorrencias']." ocorrências" ;
        }
        
        if ($proprie_telecheque['ProprietarioTelecheque']['nome_emitente']==''){
          $result_prop_telecheque = "Consulta Proprietário Telecheque : NADA CONSTA ";
        }else{
          $result_prop_telecheque = "Consulta Proprietário Telecheque : ".$proprie_telecheque['ProprietarioTelecheque']['nome_emitente'];
        }
        //Pegar código do Motorista para telecheque.
        $result_telecheque = $result_prop_telecheque."\n".$result_prof_telecheque; 
        
        //Profissionais Negativados 
        @$prof_negativacao = $this->ProfissionalNegativacao->find('first',array('conditions' => array( 'codigo_profissional' =>  $codigo_prof['0']['Profissional']['codigo'] )));
        
        if ($prof_negativacao['ProfissionalNegativacao']['observacao']==''){
        	$prof_negativacao['ProfissionalNegativacao']['observacao'] ="Negativação profissional :NADA CONSTA";
        }else{
        	$prof_negativacao['ProfissionalNegativacao']['observacao'] ="Negativação profissional :".$prof_negativacao['ProfissionalNegativacao']['observacao'];
        }
		$classificacao = $this->ParametroScore->find('list');	
        $origem_ficha  = array('F'=>'Fax', 'E'=>'E-mail'); 
		$this->set(compact('result_prof_negativacao','proprietario_serasa','result_telecheque',
			'origem_ficha','ocorrencia_bitrem','ocorrencia_carreta','ocorrencia_placa','placa_bitrem',
			'placa_carreta','placa_veiculo','observacao_supervisor', 'extracao','serasa','ocorrencia_placa', 'classificacao'));

    }

    function recalcularScore( $codigo_ficha ){
    	$authUsuario 			 = $this->BAuth->user();
		$codigo_usuario_inclusao = $authUsuario['Usuario']['codigo'];
		if (!empty($this->data)) {
			unset( $this->data['FichaStatusCriterio']['observacao_supervisor'] );
			$dados = $this->FichaStatusCriterio->formatarDados($this->data, $codigo_ficha, $codigo_usuario_inclusao);
			if (isset($this->params['form']['pendente'])) {
				$this->FichaScorecard->atualizaStatus($codigo_ficha, FichaScorecardStatus::PENDENTE, $codigo_usuario_inclusao); //+++
				$this->redirect(array('controller'=>'fichas_scorecard', 'action' => 'fichas_a_pesquisar'));
			} else {
				if ( $this->FichaStatusCriterio->salvarFichaStatusCriterio($codigo_ficha, $dados) ) {					
					$insuficiente = $this->FichaStatusCriterio->verificarCamposInsuficientesFicha( $this->data );    				
					$divergente   = $this->FichaStatusCriterio->verificarCamposDivergentesFicha( $this->data );
					//$this->FichaScorecard->atualizaStatus($codigo_ficha, FichaScorecardStatus::A_APROVAR);
					//Só grava o score se a pesquisa estiver sem insuficiencias e sem divergencias
					if( $insuficiente === 0 && $divergente === 0 ){
						$this->FichaStatusCriterio->atualizarParaGravarPontosCriterio($codigo_ficha);
					} else {//Grava com valores zerados 
						if( $divergente )
							$pontos_score = -1;
						else
							$pontos_score = 0;
						$this->FichaStatusCriterio->removePontosCriterio( $codigo_ficha, $pontos_score );
					}
					$this->FichaScorecard->atualizarResumo($codigo_ficha, $this->data['FichaScorecard']['resumo'] );
					$this->BSession->setFlash('save_success');					
				} else {
					$this->BSession->setFlash('save_error');
				}
			}
		} else {
			if (!$this->Session->read('FiltrosFichaScorecard.gerente'))
				$this->FichaScorecard->atualizaStatus($codigo_ficha, FichaScorecardStatus::EM_PESQUISA, $codigo_usuario_inclusao); //+++
			$this->data = $this->FichaStatusCriterio->listarRespostasFicha($codigo_ficha);
		}
    }
    
     function recalcularScoreAlt($codigo_ficha, $data ){
        $this->data = $data;
    	$authUsuario 			 = $this->BAuth->user();
		$codigo_usuario_inclusao = $authUsuario['Usuario']['codigo'];
		if (!empty($this->data)) {
			foreach ($this->data['FichaStatusCriterio'] as $key=>$data_array){
                $this->data['FichaStatusCriterio'][$key]['codigo_criterio'] = $key;
			}		    
			$dados = $this->FichaStatusCriterio->formatarDados($this->data, $codigo_ficha, $codigo_usuario_inclusao);
	    	if (isset($this->params['form']['pendente'])) {
				//$this->FichaScorecard->atualizaStatus($codigo_ficha, FichaScorecardStatus::PENDENTE, $codigo_usuario_inclusao); //+++
				//$this->redirect(array('controller'=>'fichas_scorecard', 'action' => 'fichas_a_pesquisar'));
			} else {
				if ( $this->FichaStatusCriterio->salvarFichaStatusCriterioAlt($codigo_ficha, $dados) ) {					
					$this->BSession->setFlash('save_success');					
				} else {
					$this->BSession->setFlash('save_error');
				}
			}
		}
		// else {
			//if (!$this->Session->read('FiltrosFichaScorecard.gerente'))
				//$this->FichaScorecard->atualizaStatus($codigo_ficha, FichaScorecardStatus::EM_PESQUISA, $codigo_usuario_inclusao); //+++
			//$this->data = $this->FichaStatusCriterio->listarRespostasFicha($codigo_ficha);
		//}
    }



 	private function pendente( $codigo_ficha, $data ) {
		$this->layout = 'ajax';
		if (!empty($codigo_ficha)) {
			$authUsuario 			 = $this->BAuth->user();
			$codigo_usuario_inclusao = $authUsuario['Usuario']['codigo'];
			$ficha = $this->FichaScorecard->find('first',array('conditions'=>array('codigo'=>$codigo_ficha)));
            if (!empty($ficha)) { 
				$this->FichaScorecard->atualizaStatus( $codigo_ficha, FichaScorecardStatus::PENDENTE, $codigo_usuario_inclusao,'pendente');
				//Salva Critérios já preenchidos
				if( isset($data['FichaStatusCriterio']['botao_selecionado']) )
					unset($data['FichaStatusCriterio']['botao_selecionado']);
				if( isset($data['FichaStatusCriterio']['resumo']) )
					unset($data['FichaStatusCriterio']['resumo']);
				if(isset($data['FichaStatusCriterio']['ExisteOcorrencia']))
					unset($data['FichaStatusCriterio']['ExisteOcorrencia']);
				if(isset($data['FichaStatusCriterio']['ExisteOcorrenciaProf']))
					unset($data['FichaStatusCriterio']['ExisteOcorrenciaProf']);				
				$dados    = $this->FichaStatusCriterio->formatarDados($data, $codigo_ficha);
				$inclusao = $this->FichaStatusCriterio->salvarFichaStatusCriterio($codigo_ficha, $dados);
			}
		}
	}

	function incluir_artigos_criminais_pesq($codigo_ficha,$codigo){
       
       $this->carrega_cabecalho_ficha($codigo_ficha);
       $veiculosficha = $this->FichaScorecardVeiculo->find('all',array('conditions' => array('FichaScorecardVeiculo.codigo_ficha_scorecard'=>$codigo_ficha)));        
		        
		        if(!isset($veiculosficha[0]['FichaScorecardVeiculo']['codigo_veiculo_log'])){
		           $veiculosficha[0]['FichaScorecardVeiculo']['codigo_veiculo_log']=''; 
		        }
		        if(!isset($veiculosficha[1]['FichaScorecardVeiculo']['codigo_veiculo_log'])){
		           $veiculosficha[1]['FichaScorecardVeiculo']['codigo_veiculo_log']=''; 
		        }
		        if(!isset($veiculosficha[2]['FichaScorecardVeiculo']['codigo_veiculo_log'])){
		           $veiculosficha[2]['FichaScorecardVeiculo']['codigo_veiculo_log']=''; 
		        }

		        $placa_veiculo = $this->VeiculoLog->find('all',array('conditions' => array('VeiculoLog.codigo'=>$veiculosficha[0]['FichaScorecardVeiculo']['codigo_veiculo_log'])));
		        $placa_carreta = $this->VeiculoLog->find('all',array('conditions' => array('VeiculoLog.codigo'=>$veiculosficha[1]['FichaScorecardVeiculo']['codigo_veiculo_log'])));
		        $placa_bitrem  = $this->VeiculoLog->find('all',array('conditions' => array('VeiculoLog.codigo'=>$veiculosficha[2]['FichaScorecardVeiculo']['codigo_veiculo_log'])));
		        
		        

		        $joins = array( 
							array(
								"table" 	=> $this->Veiculo->databaseTable.'.'.$this->Veiculo->tableSchema.'.'.$this->Veiculo->useTable,
				            	"alias" 	=> "Veiculo",
					            "type"  	=> "INNER",
								"conditions"=> array("Veiculo.codigo = VeiculoOcorrencia.codigo_veiculo")
							),
							array(
								"table" 	=> $this->TipoOcorrenciaTeleconsult->databaseTable.'.'.$this->TipoOcorrenciaTeleconsult->tableSchema.'.'.$this->TipoOcorrenciaTeleconsult->useTable,
				            	"alias" 	=> "TipoOcorrencia",
					            "type"  	=> "INNER",
								"conditions"=> array("TipoOcorrencia.codigo = VeiculoOcorrencia.codigo_ocorrencia")
							),
							array(
								"table" 	=> $this->Usuario->databaseTable.'.'.$this->Usuario->tableSchema.'.'.$this->Usuario->useTable,
				            	"alias" 	=> "Usuario",
					            "type"  	=> "INNER",
								"conditions"=> array("Usuario.codigo = VeiculoOcorrencia.codigo_usuario_inclusao")
							)
						);
				
				$fields = array("CONVERT(VARCHAR(20), VeiculoOcorrencia.data_ocorrencia, 103) as data_ocorrencia",
		                        "TipoOcorrencia.descricao as descricao",
		                        "VeiculoOcorrencia.observacao as observacao",                              
			                    "CONVERT(VARCHAR(20), VeiculoOcorrencia.data_inclusao, 20)   AS  data_inclusao",
		                        "Usuario.apelido as usuario");	

		        if(!isset($placa_veiculo[0]['VeiculoLog']['codigo_veiculo'])){
		        	$placa_veiculo[0]['VeiculoLog']['codigo_veiculo']='';
		        }
		        if(!isset($placa_carreta[0]['VeiculoLog']['codigo_veiculo'])){
		        	$placa_carreta[0]['VeiculoLog']['codigo_veiculo']='';
		        }
		        if(!isset($placa_bitrem[0]['VeiculoLog']['codigo_veiculo'])){
		        	$placa_bitrem[0]['VeiculoLog']['codigo_veiculo']='';
		        }

		        $ocorrencia_placa   = $this->VeiculoOcorrencia->find('all',array('fields'=>$fields,'joins'=>$joins,'conditions' => array('VeiculoOcorrencia.codigo_veiculo'=>$placa_veiculo[0]['VeiculoLog']['codigo_veiculo'])));
		        $ocorrencia_carreta = $this->VeiculoOcorrencia->find('all',array('fields'=>$fields,'joins'=>$joins,'conditions' => array('VeiculoOcorrencia.codigo_veiculo'=>$placa_carreta[0]['VeiculoLog']['codigo_veiculo'])));
		        $ocorrencia_bitrem  = $this->VeiculoOcorrencia->find('all',array('fields'=>$fields,'joins'=>$joins,'conditions' => array('VeiculoOcorrencia.codigo_veiculo'=>$placa_bitrem[0]['VeiculoLog']['codigo_veiculo'])));
		         

		        $fields_fichascorecard = array("ProprietarioLog.codigo_documento as CodigoProprietarioLog"); 
		        $fields_fichascorecard_prof = array("ProfissionalLog.codigo_profissional as CodigoProfissional");
		        
		        $joins_fichascorecard = array(array(
												"table" 	=> "dbTeleconsult.informacoes.ficha_scorecard_veiculo",
								            	"alias" 	=> "FichaScorecardVeiculo",
									            "type"  	=> "INNER",
												"conditions"=> array("FichaScorecard.codigo = FichaScorecardVeiculo.codigo_ficha_scorecard")
											),
		                                    array(
												"table" 	=> "dbBuonny.publico.proprietario_log",
								            	"alias" 	=> "ProprietarioLog",
									            "type"  	=> "INNER",
												"conditions"=> array("ProprietarioLog.codigo = FichaScorecardVeiculo.codigo_proprietario_log")
											),

		                                   );

		       $joins_fichascorecard_prof = array(array(
												"table" 	=> "dbBuonny.publico.profissional_log",
								            	"alias" 	=> "ProfissionalLog",
									            "type"  	=> "INNER",
												"conditions"=> array("ProfissionalLog.codigo = FichaScorecard.codigo_profissional_log")
											)

		                                   );
		       

		       $fichaScorecard_vei  = $this->FichaScorecard->find('all',array('fields'=>$fields_fichascorecard,'joins'=>$joins_fichascorecard,'conditions'=>array('FichaScorecard.codigo'=>$codigo_ficha,'FichaScorecardVeiculo.tipo'=>'0')));
			   $fichaScorecard_car  = $this->FichaScorecard->find('all',array('fields'=>$fields_fichascorecard,'joins'=>$joins_fichascorecard,'conditions'=>array('FichaScorecard.codigo'=>$codigo_ficha,'FichaScorecardVeiculo.tipo'=>'1')));
			   $fichaScorecard_bi   = $this->FichaScorecard->find('all',array('fields'=>$fields_fichascorecard,'joins'=>$joins_fichascorecard,'conditions'=>array('FichaScorecard.codigo'=>$codigo_ficha,'FichaScorecardVeiculo.tipo'=>'2')));
			   $fichaScorecard_prof = $this->FichaScorecard->find('all',array('fields'=>$fields_fichascorecard_prof,'joins'=>$joins_fichascorecard_prof,'conditions'=>array('FichaScorecard.codigo'=>$codigo_ficha)));
			    
			  //debug($ficha);die();

			   @$proprietario_vei = $fichaScorecard_vei[0][0]['CodigoProprietarioLog'];
			   @$proprietario_car = $fichaScorecard_car[0][0]['CodigoProprietarioLog'];	
			   @$proprietario_bi  = $fichaScorecard_bi[0][0]['CodigoProprietarioLog'];
			   @$profissional     = $fichaScorecard_prof[0][0]['CodigoProfissional'];
		       
		       
		       $joins_negativacao = array( 
							
							array(
								"table" 	=> "dbTeleconsult.informacoes.negativacao",
				            	"alias" 	=> "TipoNegativacao",
					            "type"  	=> "INNER",
								"conditions"=> array("TipoNegativacao.codigo = ProfissionalNegativacao.codigo_negativacao")
							),
							array(
								"table" 	=> $this->Usuario->databaseTable.'.'.$this->Usuario->tableSchema.'.'.$this->Usuario->useTable,
				            	"alias" 	=> "Usuario",
					            "type"  	=> "INNER",
								"conditions"=> array("Usuario.codigo =  ProfissionalNegativacao.codigo_usuario_inclusao")
							)
						);


		        //debug($this->data);
		        
		        $fields_negativacao = array("CONVERT(VARCHAR(20), ProfissionalNegativacao.data_inclusao, 103) as data_ocorrencia",
		                                    "TipoNegativacao.descricao as descricao",
		                                    "ProfissionalNegativacao.observacao as observacao",                              
			                                "CONVERT(VARCHAR(20), ProfissionalNegativacao.data_inclusao, 20)   AS  data_inclusao",
		                                    "Usuario.apelido as usuario");	
		        
		        $cpf_proprietario_vei = $this->Profissional->find('all',array('conditions'=>array('codigo_documento'=>$proprietario_vei)));
		        $cpf_proprietario_car = $this->Profissional->find('all',array('conditions'=>array('codigo_documento'=>$proprietario_car)));
		        $cpf_proprietario_bi  = $this->Profissional->find('all',array('conditions'=>array('codigo_documento'=>$proprietario_bi)));
		         //debug($cpf_proprietario_vei[0]['Profissional']['codigo_documento']);
		        //debug($cpf_proprietario_car[0]['Profissional']['codigo_documento']);
		       // debug($cpf_proprietario_bi[0]['Profissional']['codigo_documento']); 
		        
		        @$proprietario_vei_negativacao = $this->ProfissionalNegativacao->find('all',array('fields'=>$fields_negativacao,'joins'=>$joins_negativacao,'conditions' => array('ProfissionalNegativacao.codigo_profissional'=>$cpf_proprietario_vei[0]['Profissional']['codigo'])));    
		        @$proprietario_car_negativacao = $this->ProfissionalNegativacao->find('all',array('fields'=>$fields_negativacao,'joins'=>$joins_negativacao,'conditions' => array('ProfissionalNegativacao.codigo_profissional'=>$cpf_proprietario_car[0]['Profissional']['codigo'])));   
		        @$proprietario_bi_negativacao  = $this->ProfissionalNegativacao->find('all',array('fields'=>$fields_negativacao,'joins'=>$joins_negativacao,'conditions' => array('ProfissionalNegativacao.codigo_profissional'=>$cpf_proprietario_bi[0]['Profissional']['codigo'])));   
		        @$cpf_profissional_negativacao = $this->ProfissionalNegativacao->find('all',array('fields'=>$fields_negativacao,'joins'=>$joins_negativacao,'conditions' => array('ProfissionalNegativacao.codigo_profissional'=>$profissional)));
		        
		        //debug($cpf_profissional_negativacao);
                //debug($this->data);die();

                //Traz historico do Profissional 
                
                 $this->LogFaturamentoTeleconsult = ClassRegistry::init('LogFaturamentoTeleconsult');            
		        //$filtros = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');        
		        //debug($filtros);
		        $filtros['data_inicial'] = '01/01/2000'; //data inicial travada da consulta 
		        //debug(date('d/m/Y'));
		        $filtros['data_final']  = date('d/m/Y');
                $filtros['codigo_status']  = array(2,3,4);
                $this->Fichas->carregarDadosFicha($codigo_ficha);
                //debug($this->data['Profissional'['codigo_documento']);die();
                @$filtros['cpf']  = $this->data['Profissional']['codigo_documento'];
                $filtros['codigo_cliente'] = ''; 
				$filtros['codigo_embarcador'] = '';
				$filtros['codigo_transportador'] =''; 
				//debug();die();
				//$filtros['cpf'] => 29485484895
				//$filtros['placa'] => 
				//$filtros['usuario'] => 
				//$filtros['tipos'] => 
				//$filtros['tipo_operacao'] => 
				//$filtros['num_consulta'] =>
		        

		        $conditions = $this->LogFaturamentoTeleconsult->logFaturamentoScorecard( $filtros, true );
		        if( !empty($this->params['named']) )
		            $conditions['page'] = !empty($this->params['named']) ? $this->params['named'] : NULL;        
		        $this->paginate['LogFaturamentoTeleconsult'] = $conditions;
		        $log_faturamento = $this->paginate('LogFaturamentoTeleconsult');   
                
                
                //Sinistro 
                $conditions_sini =array( "REPLACE(REPLACE(Motorista.CPF,'-',''),'.','')" => $this->data['Profissional']['codigo_documento']);
                $sinistro = $this->listagem_sinistro($conditions_sini);  
                $natureza    	= array(
									    	0 => 'Recuperado',
									    	1 => 'Roubo Parcial',
									    	2 => 'Furto Parcial', 
									    	3 => 'Roubo Total', 
									    	4 => 'Furto Total', 
									    	5 => 'Tentativa'
									    );

		        //Historico Consulta Serasa e Telecheque
                //Pegar código do  Proprietario para telecheque .
                //$proprie_telecheque = $this->ProprietarioTelecheque->find('all');
		        $consulta_serasa_socio['Profissional']['codigo']            = @$this->data['Profissional']['codigo'];
		        $consulta_serasa_socio['Profissional']['cpf']               = @$this->data['Profissional']['codigo_documento'];
		        $consulta_serasa_socio['Proprietario']['veiculo']['codigo'] = @$this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_proprietario'];
		        $consulta_serasa_socio['Proprietario']['veiculo']['cpf']    = @$this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_documento'];
		        $consulta_serasa_socio['Proprietario']['carreta']['codigo'] = @$this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_proprietario'];
		        $consulta_serasa_socio['Proprietario']['carreta']['cpf']    = @$this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_documento'];
		        $consulta_serasa_socio['Proprietario']['bitrem']['codigo']  = @$this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_proprietario'];
		        $consulta_serasa_socio['Proprietario']['bitrem']['cpf']     = @$this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_documento'];
		        
		        
		        
                // Telecheque Motorista
		        $consulta_serasa_socio['consulta']['Telecheque']['Profissinal'] = $this->ProfissionalTelecheque->find('all',
																		        	array(	'conditions' => array( 'codigo_profissional' =>  $consulta_serasa_socio['Profissional']['codigo'] ),
																							'order' 	 => array('' =>'codigo desc')
																					)
																				); 

                
                // Telecheque Proprietario Veiculo
		        $consulta_serasa_socio['consulta']['Telecheque']['Proprietario']['Veiculo'] = $this->ProprietarioTelecheque->find('all',
		        	array(	'conditions' => array( 'codigo_proprietario' => $consulta_serasa_socio['Proprietario']['veiculo']['codigo'] ),
						  	'order'      => array( '' =>'codigo desc')
					)
				);
		        // Telecheque Proprietario Carreta
		        $consulta_serasa_socio['consulta']['Telecheque']['Proprietario']['Carreta'] = $this->ProprietarioTelecheque->find('all',
		        	array(	'conditions' => array( 'codigo_proprietario' => $consulta_serasa_socio['Proprietario']['carreta']['codigo'] ),
						  	'order'      => array( '' =>'codigo desc')
					)
				);
		        // Telecheque Proprietario Bitrem
		        $consulta_serasa_socio['consulta']['Telecheque']['Proprietario']['Bitrem'] = $this->ProprietarioTelecheque->find('all',
		        	array(	'conditions' => array( 'codigo_proprietario' => $consulta_serasa_socio['Proprietario']['bitrem']['codigo'] ),
						  	'order'      => array( '' =>'codigo desc')
					)
				);
                
                //Profissional Serasa Historico
                $consulta_serasa_socio['consulta']['Serasa']['Profissional'] = $this->ProfissionalSerasa->find('all',
		        	array(	'conditions' => array( 'codigo_profissional' => $consulta_serasa_socio['Profissional']['codigo'] ),
							'order' => array(''=>'ProfissionalSerasa.codigo desc'))
				);

		        //Proprietario Veiculo Serasa Historico
                $consulta_serasa_socio['consulta']['Serasa']['Proprietario']['Veiculo'] = $this->ProprietarioSerasa->find('all',
        	                   array(	'conditions' => array( 'codigo_proprietario' => $consulta_serasa_socio['Proprietario']['veiculo']['codigo'] ),
					'order' => array(''=>'ProprietarioSerasa.codigo desc'))
		);

                //Proprietario Carreta Serasa Historico
                $consulta_serasa_socio['consulta']['Serasa']['Proprietario']['Carreta'] = $this->ProprietarioSerasa->find('all',
        	                   array(	'conditions' => array( 'codigo_proprietario' => $consulta_serasa_socio['Proprietario']['carreta']['codigo'] ),
					'order' => array(''=>'ProprietarioSerasa.codigo desc'))
		);
		        
                //Proprietario Bitrem Serasa Historico
                $consulta_serasa_socio['consulta']['Serasa']['Proprietario']['Bitrem'] = $this->ProprietarioSerasa->find('all',
        	                   array(	'conditions' => array( 'codigo_proprietario' => $consulta_serasa_socio['Proprietario']['bitrem']['codigo'] ),
					'order' => array(''=>'ProprietarioSerasa.codigo desc'))
		);

		        //debug($consulta_serasa_socio);die();

         //Histórico RMA 
         $geradores_ocorrencia = $this->MGeradorOcorrencia->find('list');      
         $tipos_ocorrencia = $this->MRmaOcorrencia->listRma();
         $agrupamento = $this->MRmaEstatistica->tiposAgrupamento();
		 
         //Apontamentos 
         
         $this->FichaScorPesArtCriminal = & ClassRegistry::init('FichaScorPesArtCriminal'); 
         $ficha_scorecard_artigo_criminal = $this->FichaScorPesArtCriminal->listagem_apontamentos($this->data['Profissional']['codigo_documento']);
         //Artigos Criminais 
         $listatipoartigocriminal = ClassRegistry::init('ArtigoCriminal')->find('all');
         
		 foreach($listatipoartigocriminal as  $artigocriminal){
               //debug($tipoartigocriminal);
               $tipoartigocriminal[$artigocriminal['ArtigoCriminal']['codigo']] =$artigocriminal['ArtigoCriminal']['nome'].' - '.$artigocriminal['ArtigoCriminal']['descricao'];
		 }
         
         $fields_prestadores = array('Prestador.codigo','Prestador.nome','EnderecoEstado.abreviacao');

         $joins_prestadores = array(array(
												"table" 	=> "dbBuonny.publico.endereco_estado",
								            	"alias" 	=> "EnderecoEstado",
									            "type"  	=> "INNER",
												"conditions"=> array("Prestador.codigo_endereco_estado = EnderecoEstado.codigo")
											)

		                                   );         
         //Prestadores
         $listaprestadores =  ClassRegistry::init('IPrestador')->find('all',array('fields'=>$fields_prestadores,'joins'=>$joins_prestadores));
         //debug($listaprestadores);
         foreach($listaprestadores as  $prestadores){
             //debug($prestadores);
             $tipo_prestadores[$prestadores['Prestador']['codigo']] = $prestadores['Prestador']['nome'].' - '.$prestadores['EnderecoEstado']['abreviacao'] ;  
         } 	

         // Instituição -Jurisdicao 
         
         $fields_jurisdicao = array('Instituicao.codigo','Instituicao.descricao','EnderecoCidade.abreviacao');

         $joins_jurisdicao = array(array(
												"table" 	=> "dbBuonny.publico.endereco_cidade",
								            	"alias" 	=> "EnderecoCidade",
									            "type"  	=> "INNER",
												"conditions"=> array("Instituicao.codigo_endereco_cidade = EnderecoCidade.codigo")
											)

		                                   );
         $listajurisdicao = ClassRegistry::init('Instituicao')->find('all',array('fields'=>$fields_jurisdicao,'joins'=>$joins_jurisdicao));
         
         foreach($listajurisdicao as  $jurisdicao){
            //debug($jurisdicao);
            $tipo_jurisdicao[$jurisdicao['Instituicao']['codigo']] = $jurisdicao['Instituicao']['descricao'].' - '.$jurisdicao['EnderecoCidade']['abreviacao'];
         }	

         // Situação Processo 
         $listasituacaoprocesso = ClassRegistry::init('SituacaoProcesso')->find('all');
         foreach ($listasituacaoprocesso as $situacaoprocesso){
         	//debug($situacaoprocesso);
         	$tiposituacaoprocesso[$situacaoprocesso['SituacaoProcesso']['codigo']] = $situacaoprocesso['SituacaoProcesso']['descricao'];
         }
         $this->data['FichaStatusCriterios']['codigo_altera'] ='';
		 $this->set(compact('codigo_ficha','tiposituacaoprocesso','tipo_jurisdicao','tipo_prestadores','tipoartigocriminal','ficha_scorecard_artigo_criminal','agrupamento','tipos_ocorrencia','geradores_ocorrencia','consulta_serasa_socio','natureza','sinistro','log_faturamento','cpf_profissional_negativacao','proprietario_bi_negativacao','proprietario_car_negativacao','proprietario_vei_negativacao','ocorrencia_bitrem','ocorrencia_carreta','ocorrencia_placa','placa_bitrem','placa_carreta','placa_veiculo','observacao_supervisor', 'extracao','serasa','ocorrencia_placa'));
		 $this->Fichas->carregarCombos();
		 $this->carregarCombos($codigo_ficha);

	}
    
    public function excluir_artigo_criminal_pesquisa($codigo,$codigo_ficha){
       
        if(!$this->FichaScorPesArtCriminal->excluir($codigo))
            $this->BSession->setFlash('delete_error');
        //else
        //debug($codigo_ficha);die();
        header('Location:http://'.$this->Session->host.'/portal/fichas_status_criterios/editar/'.$codigo_ficha);
    }  

    public function editar_artigo_criminal_pesq($codigo_ficha,$codigo){
       
       $this->carrega_cabecalho_ficha($codigo_ficha);
       $veiculosficha = $this->FichaScorecardVeiculo->find('all',array('conditions' => array('FichaScorecardVeiculo.codigo_ficha_scorecard'=>$codigo_ficha)));        
		        
		        if(!isset($veiculosficha[0]['FichaScorecardVeiculo']['codigo_veiculo_log'])){
		           $veiculosficha[0]['FichaScorecardVeiculo']['codigo_veiculo_log']=''; 
		        }
		        if(!isset($veiculosficha[1]['FichaScorecardVeiculo']['codigo_veiculo_log'])){
		           $veiculosficha[1]['FichaScorecardVeiculo']['codigo_veiculo_log']=''; 
		        }
		        if(!isset($veiculosficha[2]['FichaScorecardVeiculo']['codigo_veiculo_log'])){
		           $veiculosficha[2]['FichaScorecardVeiculo']['codigo_veiculo_log']=''; 
		        }

		        $placa_veiculo = $this->VeiculoLog->find('all',array('conditions' => array('VeiculoLog.codigo'=>$veiculosficha[0]['FichaScorecardVeiculo']['codigo_veiculo_log'])));
		        $placa_carreta = $this->VeiculoLog->find('all',array('conditions' => array('VeiculoLog.codigo'=>$veiculosficha[1]['FichaScorecardVeiculo']['codigo_veiculo_log'])));
		        $placa_bitrem  = $this->VeiculoLog->find('all',array('conditions' => array('VeiculoLog.codigo'=>$veiculosficha[2]['FichaScorecardVeiculo']['codigo_veiculo_log'])));
		        
		        

		        $joins = array( 
							array(
								"table" 	=> $this->Veiculo->databaseTable.'.'.$this->Veiculo->tableSchema.'.'.$this->Veiculo->useTable,
				            	"alias" 	=> "Veiculo",
					            "type"  	=> "INNER",
								"conditions"=> array("Veiculo.codigo = VeiculoOcorrencia.codigo_veiculo")
							),
							array(
								"table" 	=> $this->TipoOcorrenciaTeleconsult->databaseTable.'.'.$this->TipoOcorrenciaTeleconsult->tableSchema.'.'.$this->TipoOcorrenciaTeleconsult->useTable,
				            	"alias" 	=> "TipoOcorrencia",
					            "type"  	=> "INNER",
								"conditions"=> array("TipoOcorrencia.codigo = VeiculoOcorrencia.codigo_ocorrencia")
							),
							array(
								"table" 	=> $this->Usuario->databaseTable.'.'.$this->Usuario->tableSchema.'.'.$this->Usuario->useTable,
				            	"alias" 	=> "Usuario",
					            "type"  	=> "INNER",
								"conditions"=> array("Usuario.codigo = VeiculoOcorrencia.codigo_usuario_inclusao")
							)
						);
				
				$fields = array("CONVERT(VARCHAR(20), VeiculoOcorrencia.data_ocorrencia, 103) as data_ocorrencia",
		                        "TipoOcorrencia.descricao as descricao",
		                        "VeiculoOcorrencia.observacao as observacao",                              
			                    "CONVERT(VARCHAR(20), VeiculoOcorrencia.data_inclusao, 20)   AS  data_inclusao",
		                        "Usuario.apelido as usuario");	

		        if(!isset($placa_veiculo[0]['VeiculoLog']['codigo_veiculo'])){
		        	$placa_veiculo[0]['VeiculoLog']['codigo_veiculo']='';
		        }
		        if(!isset($placa_carreta[0]['VeiculoLog']['codigo_veiculo'])){
		        	$placa_carreta[0]['VeiculoLog']['codigo_veiculo']='';
		        }
		        if(!isset($placa_bitrem[0]['VeiculoLog']['codigo_veiculo'])){
		        	$placa_bitrem[0]['VeiculoLog']['codigo_veiculo']='';
		        }

		        $ocorrencia_placa   = $this->VeiculoOcorrencia->find('all',array('fields'=>$fields,'joins'=>$joins,'conditions' => array('VeiculoOcorrencia.codigo_veiculo'=>$placa_veiculo[0]['VeiculoLog']['codigo_veiculo'])));
		        $ocorrencia_carreta = $this->VeiculoOcorrencia->find('all',array('fields'=>$fields,'joins'=>$joins,'conditions' => array('VeiculoOcorrencia.codigo_veiculo'=>$placa_carreta[0]['VeiculoLog']['codigo_veiculo'])));
		        $ocorrencia_bitrem  = $this->VeiculoOcorrencia->find('all',array('fields'=>$fields,'joins'=>$joins,'conditions' => array('VeiculoOcorrencia.codigo_veiculo'=>$placa_bitrem[0]['VeiculoLog']['codigo_veiculo'])));
		         

		        $fields_fichascorecard = array("ProprietarioLog.codigo_documento as CodigoProprietarioLog"); 
		        $fields_fichascorecard_prof = array("ProfissionalLog.codigo_profissional as CodigoProfissional");
		        
		        $joins_fichascorecard = array(array(
												"table" 	=> "dbTeleconsult.informacoes.ficha_scorecard_veiculo",
								            	"alias" 	=> "FichaScorecardVeiculo",
									            "type"  	=> "INNER",
												"conditions"=> array("FichaScorecard.codigo = FichaScorecardVeiculo.codigo_ficha_scorecard")
											),
		                                    array(
												"table" 	=> "dbBuonny.publico.proprietario_log",
								            	"alias" 	=> "ProprietarioLog",
									            "type"  	=> "INNER",
												"conditions"=> array("ProprietarioLog.codigo = FichaScorecardVeiculo.codigo_proprietario_log")
											),

		                                   );

		       $joins_fichascorecard_prof = array(array(
												"table" 	=> "dbBuonny.publico.profissional_log",
								            	"alias" 	=> "ProfissionalLog",
									            "type"  	=> "INNER",
												"conditions"=> array("ProfissionalLog.codigo = FichaScorecard.codigo_profissional_log")
											)

		                                   );
		       

		       $fichaScorecard_vei  = $this->FichaScorecard->find('all',array('fields'=>$fields_fichascorecard,'joins'=>$joins_fichascorecard,'conditions'=>array('FichaScorecard.codigo'=>$codigo_ficha,'FichaScorecardVeiculo.tipo'=>'0')));
			   $fichaScorecard_car  = $this->FichaScorecard->find('all',array('fields'=>$fields_fichascorecard,'joins'=>$joins_fichascorecard,'conditions'=>array('FichaScorecard.codigo'=>$codigo_ficha,'FichaScorecardVeiculo.tipo'=>'1')));
			   $fichaScorecard_bi   = $this->FichaScorecard->find('all',array('fields'=>$fields_fichascorecard,'joins'=>$joins_fichascorecard,'conditions'=>array('FichaScorecard.codigo'=>$codigo_ficha,'FichaScorecardVeiculo.tipo'=>'2')));
			   $fichaScorecard_prof = $this->FichaScorecard->find('all',array('fields'=>$fields_fichascorecard_prof,'joins'=>$joins_fichascorecard_prof,'conditions'=>array('FichaScorecard.codigo'=>$codigo_ficha)));
			    
			  //debug($ficha);die();

			   @$proprietario_vei = $fichaScorecard_vei[0][0]['CodigoProprietarioLog'];
			   @$proprietario_car = $fichaScorecard_car[0][0]['CodigoProprietarioLog'];	
			   @$proprietario_bi  = $fichaScorecard_bi[0][0]['CodigoProprietarioLog'];
			   @$profissional     = $fichaScorecard_prof[0][0]['CodigoProfissional'];
		       
		       
		       $joins_negativacao = array( 
							
							array(
								"table" 	=> "dbTeleconsult.informacoes.negativacao",
				            	"alias" 	=> "TipoNegativacao",
					            "type"  	=> "INNER",
								"conditions"=> array("TipoNegativacao.codigo = ProfissionalNegativacao.codigo_negativacao")
							),
							array(
								"table" 	=> $this->Usuario->databaseTable.'.'.$this->Usuario->tableSchema.'.'.$this->Usuario->useTable,
				            	"alias" 	=> "Usuario",
					            "type"  	=> "INNER",
								"conditions"=> array("Usuario.codigo =  ProfissionalNegativacao.codigo_usuario_inclusao")
							)
						);


		        //debug($this->data);
		        
		        $fields_negativacao = array("CONVERT(VARCHAR(20), ProfissionalNegativacao.data_inclusao, 103) as data_ocorrencia",
		                                    "TipoNegativacao.descricao as descricao",
		                                    "ProfissionalNegativacao.observacao as observacao",                              
			                                "CONVERT(VARCHAR(20), ProfissionalNegativacao.data_inclusao, 20)   AS  data_inclusao",
		                                    "Usuario.apelido as usuario");	
		        
		        $cpf_proprietario_vei = $this->Profissional->find('all',array('conditions'=>array('codigo_documento'=>$proprietario_vei)));
		        $cpf_proprietario_car = $this->Profissional->find('all',array('conditions'=>array('codigo_documento'=>$proprietario_car)));
		        $cpf_proprietario_bi  = $this->Profissional->find('all',array('conditions'=>array('codigo_documento'=>$proprietario_bi)));
		         //debug($cpf_proprietario_vei[0]['Profissional']['codigo_documento']);
		        //debug($cpf_proprietario_car[0]['Profissional']['codigo_documento']);
		       // debug($cpf_proprietario_bi[0]['Profissional']['codigo_documento']); 
		        
		        @$proprietario_vei_negativacao = $this->ProfissionalNegativacao->find('all',array('fields'=>$fields_negativacao,'joins'=>$joins_negativacao,'conditions' => array('ProfissionalNegativacao.codigo_profissional'=>$cpf_proprietario_vei[0]['Profissional']['codigo'])));    
		        @$proprietario_car_negativacao = $this->ProfissionalNegativacao->find('all',array('fields'=>$fields_negativacao,'joins'=>$joins_negativacao,'conditions' => array('ProfissionalNegativacao.codigo_profissional'=>$cpf_proprietario_car[0]['Profissional']['codigo'])));   
		        @$proprietario_bi_negativacao  = $this->ProfissionalNegativacao->find('all',array('fields'=>$fields_negativacao,'joins'=>$joins_negativacao,'conditions' => array('ProfissionalNegativacao.codigo_profissional'=>$cpf_proprietario_bi[0]['Profissional']['codigo'])));   
		        @$cpf_profissional_negativacao = $this->ProfissionalNegativacao->find('all',array('fields'=>$fields_negativacao,'joins'=>$joins_negativacao,'conditions' => array('ProfissionalNegativacao.codigo_profissional'=>$profissional)));
		        
		        //debug($cpf_profissional_negativacao);
                //debug($this->data);die();

                //Traz historico do Profissional 
                
                 $this->LogFaturamentoTeleconsult = ClassRegistry::init('LogFaturamentoTeleconsult');            
		        //$filtros = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');        
		        //debug($filtros);
		        $filtros['data_inicial'] = '01/01/2000'; //data inicial travada da consulta 
		        //debug(date('d/m/Y'));
		        $filtros['data_final']  = date('d/m/Y');
                $filtros['codigo_status']  = array(2,3,4);
                $this->Fichas->carregarDadosFicha($codigo_ficha);
                //debug($this->data['Profissional'['codigo_documento']);die();
                @$filtros['cpf']  = $this->data['Profissional']['codigo_documento'];
                $filtros['codigo_cliente'] = ''; 
				$filtros['codigo_embarcador'] = '';
				$filtros['codigo_transportador'] =''; 
				//debug();die();
				//$filtros['cpf'] => 29485484895
				//$filtros['placa'] => 
				//$filtros['usuario'] => 
				//$filtros['tipos'] => 
				//$filtros['tipo_operacao'] => 
				//$filtros['num_consulta'] =>
		        

		        $conditions = $this->LogFaturamentoTeleconsult->logFaturamentoScorecard( $filtros, true );
		        if( !empty($this->params['named']) )
		            $conditions['page'] = !empty($this->params['named']) ? $this->params['named'] : NULL;        
		        $this->paginate['LogFaturamentoTeleconsult'] = $conditions;
		        $log_faturamento = $this->paginate('LogFaturamentoTeleconsult');   
                
                
                //Sinistro 
                $conditions_sini =array( "REPLACE(REPLACE(Motorista.CPF,'-',''),'.','')" => $this->data['Profissional']['codigo_documento']);
                $sinistro = $this->listagem_sinistro($conditions_sini);  
                $natureza    	= array(
									    	0 => 'Recuperado',
									    	1 => 'Roubo Parcial',
									    	2 => 'Furto Parcial', 
									    	3 => 'Roubo Total', 
									    	4 => 'Furto Total', 
									    	5 => 'Tentativa'
									    );

		        //Historico Consulta Serasa e Telecheque
                //Pegar código do  Proprietario para telecheque .
                //$proprie_telecheque = $this->ProprietarioTelecheque->find('all');
		        $consulta_serasa_socio['Profissional']['codigo']            = @$this->data['Profissional']['codigo'];
		        $consulta_serasa_socio['Profissional']['cpf']               = @$this->data['Profissional']['codigo_documento'];
		        $consulta_serasa_socio['Proprietario']['veiculo']['codigo'] = @$this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_proprietario'];
		        $consulta_serasa_socio['Proprietario']['veiculo']['cpf']    = @$this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_documento'];
		        $consulta_serasa_socio['Proprietario']['carreta']['codigo'] = @$this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_proprietario'];
		        $consulta_serasa_socio['Proprietario']['carreta']['cpf']    = @$this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_documento'];
		        $consulta_serasa_socio['Proprietario']['bitrem']['codigo']  = @$this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_proprietario'];
		        $consulta_serasa_socio['Proprietario']['bitrem']['cpf']     = @$this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_documento'];
		        
		        
		        
                // Telecheque Motorista
		        $consulta_serasa_socio['consulta']['Telecheque']['Profissinal'] = $this->ProfissionalTelecheque->find('all',
																		        	array(	'conditions' => array( 'codigo_profissional' =>  $consulta_serasa_socio['Profissional']['codigo'] ),
																							'order' 	 => array('' =>'codigo desc')
																					)
																				); 

                
                // Telecheque Proprietario Veiculo
		        $consulta_serasa_socio['consulta']['Telecheque']['Proprietario']['Veiculo'] = $this->ProprietarioTelecheque->find('all',
		        	array(	'conditions' => array( 'codigo_proprietario' => $consulta_serasa_socio['Proprietario']['veiculo']['codigo'] ),
						  	'order'      => array( '' =>'codigo desc')
					)
				);
		        // Telecheque Proprietario Carreta
		        $consulta_serasa_socio['consulta']['Telecheque']['Proprietario']['Carreta'] = $this->ProprietarioTelecheque->find('all',
		        	array(	'conditions' => array( 'codigo_proprietario' => $consulta_serasa_socio['Proprietario']['carreta']['codigo'] ),
						  	'order'      => array( '' =>'codigo desc')
					)
				);
		        // Telecheque Proprietario Bitrem
		        $consulta_serasa_socio['consulta']['Telecheque']['Proprietario']['Bitrem'] = $this->ProprietarioTelecheque->find('all',
		        	array(	'conditions' => array( 'codigo_proprietario' => $consulta_serasa_socio['Proprietario']['bitrem']['codigo'] ),
						  	'order'      => array( '' =>'codigo desc')
					)
				);
                
                //Profissional Serasa Historico
                $consulta_serasa_socio['consulta']['Serasa']['Profissional'] = $this->ProfissionalSerasa->find('all',
		        	array(	'conditions' => array( 'codigo_profissional' => $consulta_serasa_socio['Profissional']['codigo'] ),
							'order' => array(''=>'ProfissionalSerasa.codigo desc'))
				);

		        //Proprietario Veiculo Serasa Historico
                $consulta_serasa_socio['consulta']['Serasa']['Proprietario']['Veiculo'] = $this->ProprietarioSerasa->find('all',
        	                   array(	'conditions' => array( 'codigo_proprietario' => $consulta_serasa_socio['Proprietario']['veiculo']['codigo'] ),
					'order' => array(''=>'ProprietarioSerasa.codigo desc'))
		);

                //Proprietario Carreta Serasa Historico
                $consulta_serasa_socio['consulta']['Serasa']['Proprietario']['Carreta'] = $this->ProprietarioSerasa->find('all',
        	                   array(	'conditions' => array( 'codigo_proprietario' => $consulta_serasa_socio['Proprietario']['carreta']['codigo'] ),
					'order' => array(''=>'ProprietarioSerasa.codigo desc'))
		);
		        
                //Proprietario Bitrem Serasa Historico
                $consulta_serasa_socio['consulta']['Serasa']['Proprietario']['Bitrem'] = $this->ProprietarioSerasa->find('all',
        	                   array(	'conditions' => array( 'codigo_proprietario' => $consulta_serasa_socio['Proprietario']['bitrem']['codigo'] ),
					'order' => array(''=>'ProprietarioSerasa.codigo desc'))
		);

		        //debug($consulta_serasa_socio);die();

         //Histórico RMA 
         $geradores_ocorrencia = $this->MGeradorOcorrencia->find('list');      
         $tipos_ocorrencia = $this->MRmaOcorrencia->listRma();
         $agrupamento = $this->MRmaEstatistica->tiposAgrupamento();
		 
         //Apontamentos 
         
         $this->FichaScorPesArtCriminal = & ClassRegistry::init('FichaScorPesArtCriminal'); 
         $ficha_scorecard_artigo_criminal = $this->FichaScorPesArtCriminal->listagem_apontamentos($this->data['Profissional']['codigo_documento']);
         //Artigos Criminais 
         $listatipoartigocriminal = ClassRegistry::init('ArtigoCriminal')->find('all');
         
		 foreach($listatipoartigocriminal as  $artigocriminal){
               //debug($tipoartigocriminal);
               $tipoartigocriminal[$artigocriminal['ArtigoCriminal']['codigo']] =$artigocriminal['ArtigoCriminal']['nome'].' - '.$artigocriminal['ArtigoCriminal']['descricao'];
		 }
         
         $fields_prestadores = array('Prestador.codigo','Prestador.nome','EnderecoEstado.abreviacao');

         $joins_prestadores = array(array(
												"table" 	=> "dbBuonny.publico.endereco_estado",
								            	"alias" 	=> "EnderecoEstado",
									            "type"  	=> "INNER",
												"conditions"=> array("Prestador.codigo_endereco_estado = EnderecoEstado.codigo")
											)

		                                   );

         
         //Prestadores
         $listaprestadores =  ClassRegistry::init('IPrestador')->find('all',array('fields'=>$fields_prestadores,'joins'=>$joins_prestadores));
         //debug($listaprestadores);
         foreach($listaprestadores as  $prestadores){
             //debug($prestadores);
             $tipo_prestadores[$prestadores['Prestador']['codigo']] = $prestadores['Prestador']['nome'].' - '.$prestadores['EnderecoEstado']['abreviacao'] ;  

         } 	

         // Instituição -Jurisdicao 
         
         $fields_jurisdicao = array('Instituicao.codigo','Instituicao.descricao','EnderecoCidade.abreviacao');

         $joins_jurisdicao = array(array(
												"table" 	=> "dbBuonny.publico.endereco_cidade",
								            	"alias" 	=> "EnderecoCidade",
									            "type"  	=> "INNER",
												"conditions"=> array("Instituicao.codigo_endereco_cidade = EnderecoCidade.codigo")
											)

		                                   );
         $listajurisdicao = ClassRegistry::init('Instituicao')->find('all',array('fields'=>$fields_jurisdicao,'joins'=>$joins_jurisdicao));
         
         foreach($listajurisdicao as  $jurisdicao){
            //debug($jurisdicao);
            $tipo_jurisdicao[$jurisdicao['Instituicao']['codigo']] = $jurisdicao['Instituicao']['descricao'].' - '.$jurisdicao['EnderecoCidade']['abreviacao'];
         }	

         // Situação Processo 
         $listasituacaoprocesso = ClassRegistry::init('SituacaoProcesso')->find('all');
         foreach ($listasituacaoprocesso as $situacaoprocesso){
         	//debug($situacaoprocesso);
         	$tiposituacaoprocesso[$situacaoprocesso['SituacaoProcesso']['codigo']] = $situacaoprocesso['SituacaoProcesso']['descricao'];
         }

         $dados_ficha_scorecard_artigo_criminal = $this->FichaScorPesArtCriminal->find('all',array('conditions'=>array('codigo'=>$codigo)));
         //debug($dados_ficha_scorecard_artigo_criminal);
         
         $this->data['FichaStatusCriterios']['codigo_altera'] =  $dados_ficha_scorecard_artigo_criminal[0]['FichaScorPesArtCriminal']['codigo'];
         $this->data['FichaStatusCriterios']['numero_artigo'] =$dados_ficha_scorecard_artigo_criminal[0]['FichaScorPesArtCriminal']['codigo_artigo_criminal'];
         $this->data['FichaStatusCriterios']['codigo_instituicao'] =$dados_ficha_scorecard_artigo_criminal[0]['FichaScorPesArtCriminal']['codigo_instituicao'];
         $this->data['FichaStatusCriterios']['data_ocorrencia'] = $dados_ficha_scorecard_artigo_criminal[0]['FichaScorPesArtCriminal']['data_ocorrencia'];
         $this->data['FichaStatusCriterios']['local'] = $dados_ficha_scorecard_artigo_criminal[0]['FichaScorPesArtCriminal']['local_ocorrencia'];
         $this->data['FichaScorecard']['codigo_endereco_cidade_carga_origem'] = $dados_ficha_scorecard_artigo_criminal[0]['FichaScorPesArtCriminal']['codigo_endereco_cidade'];
         $this->data['FichaStatusCriterios']['codigo_prestador'] = $dados_ficha_scorecard_artigo_criminal[0]['FichaScorPesArtCriminal']['codigo_prestador'];
         $this->data['FichaStatusCriterios']['data_inquerito'] = $dados_ficha_scorecard_artigo_criminal[0]['FichaScorPesArtCriminal']['data_inquerito'];
         $this->data['FichaStatusCriterios']['inquerito'] = $dados_ficha_scorecard_artigo_criminal[0]['FichaScorPesArtCriminal']['inquerito'];
         $this->data['FichaStatusCriterios']['data_processo'] = $dados_ficha_scorecard_artigo_criminal[0]['FichaScorPesArtCriminal']['data_processo'];
         $this->data['FichaStatusCriterios']['processo'] = $dados_ficha_scorecard_artigo_criminal[0]['FichaScorPesArtCriminal']['processo'];
         $this->data['ProfissionalNegativacao']['observacao'] = $dados_ficha_scorecard_artigo_criminal[0]['FichaScorPesArtCriminal']['observacao'];
         $this->data['FichaStatusCriterios']['dp'] = $dados_ficha_scorecard_artigo_criminal[0]['FichaScorPesArtCriminal']['numero_dp'];
         $this->data['FichaStatusCriterios']['codigo_situacao'] = $dados_ficha_scorecard_artigo_criminal[0]['FichaScorPesArtCriminal']['codigo_situacao_processo'];
         
         // Buscar nome da cidade 
         $cidade['endereco_codigo_cidade'] = $dados_ficha_scorecard_artigo_criminal[0]['FichaScorPesArtCriminal']['codigo_endereco_cidade'];
         $nome_cid = $this->EnderecoCidade->combo_cidade($cidade['endereco_codigo_cidade']);
        // debug($nome_cid);die();
         $this->data['FichaStatusCriterios']['cidade_origem'] = $nome_cid['EnderecoCidade']['descricao'].' - '.$nome_cid['EnderecoEstado']['abreviacao']; 

		 $this->set(compact('codigo_ficha','tiposituacaoprocesso','tipo_jurisdicao','tipo_prestadores','tipoartigocriminal','ficha_scorecard_artigo_criminal','agrupamento','tipos_ocorrencia','geradores_ocorrencia','consulta_serasa_socio','natureza','sinistro','log_faturamento','cpf_profissional_negativacao','proprietario_bi_negativacao','proprietario_car_negativacao','proprietario_vei_negativacao','ocorrencia_bitrem','ocorrencia_carreta','ocorrencia_placa','placa_bitrem','placa_carreta','placa_veiculo','observacao_supervisor', 'extracao','serasa','ocorrencia_placa'));
		 $this->Fichas->carregarCombos();
		 $this->carregarCombos($codigo_ficha);
         
    }
    

	public function salvar_artigo_criminal_pesquisa(){
         
         $this->autoRender = 'false';
         
         //if(!empty($this->data['FichaStatusCriterios']['cidade_origem'])){
         	$dados_cid = explode('-',$this->data['FichaStatusCriterios']['cidade_origem']);
         	$lista_cids = $this->VEndereco->codigoDescricaoCidade(utf8_decode($dados_cid[0]));
            //debug($lista_cids['VEndereco']['endereco_codigo_cidade']);
            $this->data['FichaScorPesArtCriminal']['codigo_endereco_cidade']      = $lista_cids['VEndereco']['endereco_codigo_cidade'];
         //}else{
          //  $this->data['FichaScorPesArtCriminal']['codigo_endereco_cidade']      = $this->data['FichaStatusCriterios']['cidade_origem'];
         //} 
        $valido = 0;
        //if ($this->data['FichaScorPesArtCriminal']['codigo_endereco_cidade'] ==''){
        //  $this->FichaStatusCriterios->validationErrors['cidade_origem'] = $this->FichaScorPesArtCriminal->invalidate('codigo_endereco_cidade','Cidade Obrigatória');
        //  $valido = 1;
        //}
         $this->data['FichaScorPesArtCriminal']['codigo_ficha_pesquisa']       = $this->data['FichaStatusCriterios']['codigo_ficha']; 
         $this->data['FichaScorPesArtCriminal']['codigo_artigo_criminal']      = $this->data['FichaStatusCriterios']['numero_artigo'];
         $this->data['FichaScorPesArtCriminal']['codigo_instituicao']          = $this->data['FichaStatusCriterios']['codigo_instituicao'];
         $this->data['FichaScorPesArtCriminal']['data_ocorrencia']             = $this->data['FichaStatusCriterios']['data_ocorrencia'];
         $this->data['FichaScorPesArtCriminal']['local_ocorrencia']            = $this->data['FichaStatusCriterios']['local'];
         $this->data['FichaScorPesArtCriminal']['codigo_endereco_cidade']      = $lista_cids['VEndereco']['endereco_codigo_cidade'];
         $this->data['FichaScorPesArtCriminal']['codigo_prestador']            = $this->data['FichaStatusCriterios']['codigo_prestador'];
         $this->data['FichaScorPesArtCriminal']['data_inquerito']              = $this->data['FichaStatusCriterios']['data_inquerito'];
         $this->data['FichaScorPesArtCriminal']['inquerito']                   = $this->data['FichaStatusCriterios']['inquerito'];
         $this->data['FichaScorPesArtCriminal']['data_processo']               = $this->data['FichaStatusCriterios']['data_processo'];
         $this->data['FichaScorPesArtCriminal']['processo']                    = $this->data['FichaStatusCriterios']['processo'];
         $this->data['FichaScorPesArtCriminal']['observacao']                  = $this->data['ProfissionalNegativacao']['observacao'];
         $this->data['FichaScorPesArtCriminal']['data_averiguacao']            = date('d/m/Y') ;
         $this->data['FichaScorPesArtCriminal']['codigo_usuario_averiguacao']  = $this->authUsuario['Usuario']['codigo'];
         $this->data['FichaScorPesArtCriminal']['numero_dp']                   = $this->data['FichaStatusCriterios']['dp'];
         $this->data['FichaScorPesArtCriminal']['codigo_situacao_processo']    = $this->data['FichaStatusCriterios']['codigo_situacao'];
         $this->data['FichaScorPesArtCriminal']['data_inclusao']               = date('d/m/Y');
         $this->data['FichaScorPesArtCriminal']['codigo_usuario_inclusao']     =  $this->authUsuario['Usuario']['codigo'];
         
         //debug($this->data);die();
         
         if($valido != 1){

	         if ($this->data['FichaStatusCriterios']['codigo_altera']!=''){
	            $this->data['FichaScorPesArtCriminal']['codigo'] = $this->data['FichaStatusCriterios']['codigo_altera'];
	            if($this->FichaScorPesArtCriminal->atualizar($this->data)){
	            	$this->BSession->setFlash('save_success');
	                header('Location:http://'.$this->Session->host.'/portal/fichas_status_criterios/editar/'.$this->data['FichaScorPesArtCriminal']['codigo_ficha_pesquisa']);
	          
	            }else{
	                $this->BSession->setFlash('save_error'); 
	            }	
	            //debug('oi');
	         }else{
	            unset($this->data['FichaStatusCriterios']['codigo_altera']);
	            //debug($this->data['FichaScorPesArtCriminal']);die(); 
	            if($this->FichaScorPesArtCriminal->incluir($this->data['FichaScorPesArtCriminal'])){
	               $this->BSession->setFlash('save_success');
	               header('Location:http://'.$this->Session->host.'/portal/fichas_status_criterios/editar/'.$this->data['FichaScorPesArtCriminal']['codigo_ficha_pesquisa']);
	         
	            }else{
	            	$this->BSession->setFlash('save_error'); 
	            }

	         }
	     }else{
	     	$this->BSession->setFlash('save_error'); 
	     	header('Location:http://'.$this->Session->host.'/portal/fichas_status_criterios/editar/'.$this->data['FichaScorPesArtCriminal']['codigo_ficha_pesquisa']);
	     } 	         
	}
	
	function retornaScoreProfissional( $codigo_parametro_score, $codigo_profissional_tipo ){
		$status_anterior_profissional = NULL;
		if( $codigo_parametro_score && $codigo_profissional_tipo ){
			$score_ficha = $this->ParametroScore->carregar( $codigo_parametro_score );
			$pontos      = (isset($score_ficha['ParametroScore']['pontos']) ? $score_ficha['ParametroScore']['pontos'] :  NULL );
			if(  $codigo_profissional_tipo < 5 ){//Profissional que possui veiculo 
				$status_anterior_profissional = isset($score_ficha['ParametroScore']['nivel']) ? $score_ficha['ParametroScore']['nivel'] : NULL;
				if( $pontos && $pontos > 0 )
					$status_anterior_profissional .= " ( R$: ". number_format( $score_ficha['ParametroScore']['valor'], 2, ',', '.') ." )";
			} else {
				$status_anterior_profissional = (isset($score_ficha['ParametroScore']['pontos']) ? ( $score_ficha['ParametroScore']['pontos'] > 0 ? 'Adequado' : 'Divergente' ) : NULL );
			}
		}
		return $status_anterior_profissional;
	}

	function pre_visualizar_score(  ){
		$pre_classificacao = NULL;
		$codigo_ficha = isset($this->params['pass'][0]) ? $this->params['pass'][0] : NULL;
		$data         = $this->params['url']['data'];		
		if( !empty($data['FichaStatusCriterio']) && $codigo_ficha ){
			$pre_classificacao = $this->FichaStatusCriterio->preVisualizarClassificacao( $codigo_ficha, $data );
		}
		$this->set(compact('pre_classificacao'));
	}

	function retornaCorpoEmailScorecard( $dados, $codigo_log_faturamento = NULL ){

		$logotipo = '<img src="http://www.rhhealth.com.br/assets/img/logo-rhhealth.png" /><br />';		
	    $corpo_mail_bruto =
	    'Retorno de Pesquisas - '.$dados['ProfissionalTipo']['descricao'].' <br><br>' .
	    'Cliente: '.$dados['Cliente']['razao_social'].' <br><br>' .
	    'Produto: SCORECARD <br><br>' .
	    'Nome: '.$dados['ProfissionalLog']['nome'].' <br><br>';
	    if( $dados['FichaScorecard']['codigo_profissional_tipo'] < 6 ){
			if( !empty($dados['Veiculo']['placa']) )
		    	$corpo_mail_bruto .= 'Veiculo: '.$dados['Veiculo']['placa'].' <br><br>';
			if( !empty($dados['Carreta']['placa']) )		    
		    	$corpo_mail_bruto .= 'Carreta: '.$dados['Veiculo']['placa'].' <br><br>';
			if( !empty($dados['Bitrem']['placa']) )		    
		    	$corpo_mail_bruto .= 'Bitrem: ' .$dados['Bitrem']['placa'] .' <br><br>';		    
		}
		if( $dados['ParametroScore']['codigo']  == ParametroScore::INSUFICIENTE ){
			$corpo_mail_bruto .= '<b>Status: PERFIL COM INSUFICIÊNCIA DE INFORMAÇÕES - Favor contatar-nos via e-mail controle.pesquisa@buonny.com.br ou via fone 11 5079 2325, observando instrução abaixo:</b><br><br>';
			$informacoesinsuficientes = NULL;
			if( !empty( $dados['Criterios']['insuficientes'] )) {
				foreach( $dados['Criterios']['insuficientes'] as $key => $info ) {
					$informacoesinsuficientes .= "<li>".$info['Criterio']['descricao'].": ".$info['StatusCriterio']['descricao']."</li>";
				}
				if($informacoesinsuficientes)
					$informacoesinsuficientes = '<ul>'.$informacoesinsuficientes.'</ul><br /><br />';
			}
			$corpo_mail_bruto .= $informacoesinsuficientes;
			$corpo_mail_bruto .= 
			'Atenção: É expressamente proibida a exibição desse documento ao consultado ou a terceiros, e a violação acarretará à ' .
			'contratante e ao funcionário infrator, responsabilidade civil e criminal. ' .
			'A contratação ou não do(s) profissional(is), é uma decisão da empresa consultante, não cabendo a Gerenciadora de Riscos ' .
			'qualquer responsabilidade sobre esta decisão. ' .
			'<br /><br /><center>SETOR DE PESQUISAS <br>' .
			'Todos os STATUS podem sofrer alterações. <br><br>' .
			'E-MAIL AUTOMÁTICO. FAVOR NÃO RESPONDER.';
		} elseif ( $dados['ParametroScore']['codigo'] == ParametroScore::DIVERGENTE ){
			$corpo_mail_bruto .='<b>Status: PERFIL DIVERGENTE - Favor contatar-nos pelos fones (11)3443-2580/2581 ou 2381</b><br><br>';
			if( !empty( $dados['Criterios']['divergentes'] )) {
				$informacoes_divergentes = NULL;
				foreach( $dados['Criterios']['divergentes'] as $info ) {
					$informacoes_divergentes .= "<li>". $info['Criterio']['descricao'].": ".$info['StatusCriterio']['descricao'] ."</li>";
				}
				if($informacoes_divergentes)
					$informacoes_divergentes = '<ul>'.$informacoes_divergentes.'</ul><br /><br />';
				$corpo_mail_bruto .= $informacoes_divergentes;
				$corpo_mail_bruto .=
				'Atenção: É expressamente proibida a exibição desse documento ao consultado ou a terceiros, e a violação acarretará à ' .
				'contratante e ao funcionário infrator, responsabilidade civil e criminal. ' .
				'A contratação ou não do(s) profissional(is), é uma decisão da empresa consultante, não cabendo a Gerenciadora de Riscos ' .
				'qualquer responsabilidade sobre esta decisão. ' .
				'<br /><br /><center>SETOR DE PESQUISAS <br>' .
				'Todos os STATUS podem sofrer alterações. <br><br>' .
				'E-MAIL AUTOMÁTICO. FAVOR NÃO RESPONDER. <br>';
			}            
	    } else {
			$corpo_mail_bruto .= '<b>Status: PERFIL ADEQUADO AO RISCO</b><br><br>';
			if( $codigo_log_faturamento )
				$corpo_mail_bruto .= 'Consulta Número: '.$codigo_log_faturamento.'<br><br>';

			$validate_ficha = ($dados['FichaScorecard']['codigo_profissional_tipo'] == ProfissionalTipo::CARRETEIRO ? 'O EMBARQUE' : substr($dados['FichaScorecard']['data_validade'], 0, 10 ) );
			$corpo_mail_bruto .=
			    'Validade: '.$validate_ficha.' <br><br>' .
			    '<b>ATENÇÃO</b><br>' .
			    'DOCUMENTOS SOB RESPONSABILIDADE DO TRANSPORTADOR: ANTES DE EFETUAR O EMBARQUE FAVOR CONFERIR SE DOCUMENTOS ORIGINAIS DO MOTORISTA ESTÃO EM ORDEM: IDENTIDADE, CNH E DOCUMENTOS DE PORTE OBRIGATÓRIO DOS VEÍCULOS).<br>' .
			    'É expressamente proibida a exibição desse documento ao consultado ou a terceiros, e a violação acarretará à<br>' .
			    'contratante e ao funcionário infrator, responsabilidade civil e criminal. <br><br>' .
			    'A contratação ou não do(s) profissional(is), é uma decisão da empresa consultante, não cabendo a ' .
			    'Gerenciadora de Riscos<br>qualquer responsabilidade sobre esta decisão. <br><br>' .
			    '<center>SETOR DE PESQUISAS <br>' .
			    'Todos os STATUS podem sofrer alterações. <br><br>' .
			    'E-MAIL AUTOMÁTICO. FAVOR NÃO RESPONDER. <br>' .
			    'Em caso de dúvida fone: (11) 3443-2325 </center>';		    
	    }	   
		echo $corpo_mail_bruto;
	}
}