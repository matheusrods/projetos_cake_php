<?php
class PropostaCredenciamento extends AppModel {

    var $name = 'PropostaCredenciamento';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'propostas_credenciamento';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_proposta_credenciamento'));
    
	var $validate = array(
        'razao_social' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Razão Social!'
		),	
        'nome_fantasia' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Nome Fantasia!'
		),
        'codigo_documento' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o CNPJ!',
            ),
            'documentoValido' => array(
                'rule' => 'documentoValido',
                'message' => 'CNPJ inválido, verifique!',
            ),
        	'unicoCNPJ' => array(
       			'rule' => 'unicoCNPJ',
       			'message' => 'CNPJ já cadastrado no Sistema!',        			
        	),
        	'fornecedorCNPJ' => array(
       			'rule' => 'fornecedorCNPJ',
       			'message' => 'CNPJ já cadastrado no Sistema como Fornecedor!',        			
        	)
        ),		
        'responsavel_tecnico_nome' => array(
			'rule' => 'notEmpty_tipoExame',
			'message' => 'Informe o Responsável Técnico!'
		),
        'responsavel_tecnico_numero_conselho' => array(
			'rule' => 'notEmpty_tipoExame',
			'message' => 'Informe o Número do Conselho!'
		),
        'responsavel_tecnico_conselho_uf' => array(
			'rule' => 'notEmpty_tipoExame',
			'message' => 'Informe a UF do Conselho!'
		),		
        'responsavel_administrativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Responsável Administrativo!'
		),
        'telefone' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Telefone!'
		),
        'email' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o E-mail!',
            )
        ),	
        'tipo_atendimento' => array(
			'rule' => 'notEmpty_tipoExame',
			'message' => 'Escolha uma Opção!'
		),	
					
        'acesso_portal' => array(
			'rule' => 'notEmpty',
			'message' => 'É possível acessar o portal ?'
		),
        'exames_local_unico' => array(
			'rule' => 'notEmpty_tipoExame',
			'message' => 'Escolha uma Opção!'
		)
	);
	
	function notEmpty_tipoExame($param) {
		if(isset($this->data['PropostaCredProduto']['59']) && ($this->data['PropostaCredProduto']['59'] == '1')) {
			if(current($param) == '') {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}

    function documentoValido() {
        $model_documento = & ClassRegistry::init('Documento');
        $codigo_documento = $this->data[$this->name]['codigo_documento'];
        
		if($codigo_documento) {
	        if($model_documento->isCNPJ($codigo_documento) == false)
	            return false;
	        else
	            return true;        	
        } else {
        	return true;
        }
    }
    
    function unicoCNPJ() {
    	
    	$codigo_documento = $this->data[$this->name]['codigo_documento'];
    	$codigo = isset($this->data[$this->name]['codigo']) ? $this->data[$this->name]['codigo'] : NULL;
    	
    	
    	if(!empty($codigo_documento))
    		$conditions[] = array($this->name . '.codigo_documento' => $codigo_documento);
    	
    	if(!empty($codigo))
    		$conditions[] = array($this->name . '.codigo <> ' => $codigo);
    	
    	$conditions[] = array('StatusPropostaCred.polaridade ' => '1');
    	
    	if($codigo_documento || $codigo) {
    		
    		$joins = array(
    			array(
    				'table' => 'status_proposta_credenciamento',
    				'alias' => 'StatusPropostaCred',
    				'type' => 'INNER',
    				'conditions' => array(
    					'PropostaCredenciamento.codigo_status_proposta_credenciamento = StatusPropostaCred.codigo'
    				)
    			)
    		);
    		
   			if($this->find('all', array('conditions' => $conditions, 'joins' => $joins))) {
   				return false;
   			} else {
   				return true;
   			}

    	} else {
    		return true;
    	}
    }    

    function fornecedorCNPJ() {
    	
    	$codigo_documento = $this->data[$this->name]['codigo_documento'];
    	$codigo = isset($this->data[$this->name]['codigo']) ? $this->data[$this->name]['codigo'] : NULL;
    	
    	
    	if(!empty($codigo_documento))
    		$conditions[] = array('fornecedor.codigo_documento' => $codigo_documento);
    	
    	if(!empty($codigo))
    		$conditions[] = array($this->name . '.codigo <> ' => $codigo);
    	
    	if($codigo_documento || $codigo) {
    		
    		$joins = array(
    			array(
    				'table' => 'fornecedores',
    				'alias' => 'fornecedor',
    				'type' => 'RIGHT',
    				'conditions' => array(
    					'PropostaCredenciamento.codigo_documento = fornecedor.codigo_documento'
    				)
    			)
    		);   		
    		
   			if($this->find('all', array('conditions' => $conditions, 'joins' => $joins))) {
   				return false;
   			} else {
   				return true;
   			}

    	} else {
    		return true;
    	}
    }    
    
    public function verificaCNPJAtivo($codigo_proposta, $codigo_documento) {
    	if($codigo_documento)
    		$conditions[] = array('PropostaCredenciamento.codigo_documento' => $codigo_documento);
    	
    	if($codigo_proposta)
    		$conditions[] = array('PropostaCredenciamento.codigo <> ' => $codigo_proposta);
    	
    	$conditions[] = array('StatusPropostaCred.polaridade ' => '1');
    	
    	$joins = array(
    		array(
    			'table' => 'status_proposta_credenciamento',
    			'alias' => 'StatusPropostaCred',
    			'type' => 'INNER',
    			'conditions' => array(
    				'PropostaCredenciamento.codigo_status_proposta_credenciamento = StatusPropostaCred.codigo'
    			)
    		)
    	);
    	
    	if($this->find('all', array('conditions' => $conditions, 'joins' => $joins))) {
    		return false;
    	} else {
    		return true;
    	}
    }
    
    function incluir($dados, $etapa = null) {
    	
    	// MODELS
    	$model_PropostaCredEndereco = & ClassRegistry::init('PropostaCredEndereco');
    	$model_StatusPropostaCred = & ClassRegistry::init('StatusPropostaCred');
    	$model_Servico = & ClassRegistry::init('Servico');
    	$model_PropostaCredExame = & ClassRegistry::init('PropostaCredExame');
    	$model_PropostaCredEngenharia = & ClassRegistry::init('PropostaCredEngenharia');
    	$model_PropostaCredProduto = & ClassRegistry::init('PropostaCredProduto');
    	$model_Medico = & ClassRegistry::init('Medico');
    	$model_MedicoEndereco = & ClassRegistry::init('MedicoEndereco');
    	$model_PropostaCredMedico = & ClassRegistry::init('PropostaCredMedico');
    	$model_Horario = & ClassRegistry::init('Horario');
    	$model_HorarioDiferenciado = & ClassRegistry::init('HorarioDiferenciado');


        // status de cadastro (etapa 1 = pre cadastro / etapa 2 = aguardando analise de valores)
   		$dados['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] = ($etapa == '1') ? StatusPropostaCred::PRECADASTRO : StatusPropostaCred::AGUARDANDO_ANALISE_VALORES;
   		
   		
    	if(isset($dados['PropostaCredenciamento']['numero_banco']) && $dados['PropostaCredenciamento']['numero_banco'] == 0) {
    		$dados['PropostaCredenciamento']['numero_banco'] = null;
    	}
    	
    	// tira maskara formatacao do cnpj
        if(isset($dados['PropostaCredenciamento']['codigo_documento']) && $dados['PropostaCredenciamento']['codigo_documento']) {
        	$dados['PropostaCredenciamento']['codigo_documento'] = Comum::soNumero($dados['PropostaCredenciamento']['codigo_documento']);
        }
        
        // Dados utlizados somente na etapa 2 e no cadastro completo!!!
        if($etapa != '1') {
	        $dados['PropostaCredenciamento']['telefone'] = Comum::soNumero($dados['PropostaCredenciamento']['telefone']);
	        $dados['PropostaCredenciamento']['fax'] = Comum::soNumero($dados['PropostaCredenciamento']['fax']);
	        $dados['PropostaCredenciamento']['celular'] = Comum::soNumero($dados['PropostaCredenciamento']['celular']);
	        
	        
	        // verifica se tem codigo da empresa!!!
	        if(!isset($this->authUsuario['Usuario'])) {
	        	$dadosProposta = $this->find('first', array('conditions' => array('codigo' => $dados['PropostaCredenciamento']['codigo'])));
	        	 
	        	if($dadosProposta['PropostaCredenciamento']['codigo_empresa']) {
	        		$codigo_empresa = $dadosProposta['PropostaCredenciamento']['codigo_empresa'];
        }
	        }
        }
        
		try {
			
			$muda_status = 0;
            $this->query('begin transaction');

            if($etapa != '2') {
	            parent::incluir($dados['PropostaCredenciamento']);
            }
            
			// formata campos: PropostaCredEndereco
			$invalidadeFields = array();
			if(isset($dados['PropostaCredEndereco'])) {
				
				foreach($dados['PropostaCredEndereco'] as $key => $endereco) {
					
					if(isset($dados['PropostaCredenciamento']['codigo']) && $dados['PropostaCredenciamento']['codigo'] && ($key == 0)) {
						$dadosEndereco = $model_PropostaCredEndereco->find('first', array('conditions' => array('codigo_proposta_credenciamento' => $dados['PropostaCredenciamento']['codigo'], 'matriz' => '1')));

						$dadosEndereco['PropostaCredEndereco']['cep'] =  $endereco['cep'];
						$dadosEndereco['PropostaCredEndereco']['logradouro'] =  $endereco['logradouro'];
						$dadosEndereco['PropostaCredEndereco']['numero'] =  $endereco['numero'];
						$dadosEndereco['PropostaCredEndereco']['complemento'] =  $endereco['complemento'];
						$dadosEndereco['PropostaCredEndereco']['bairro'] =  $endereco['bairro'];
						$dadosEndereco['PropostaCredEndereco']['cidade'] =  $endereco['cidade'];
						$dadosEndereco['PropostaCredEndereco']['estado'] =  $endereco['estado'];
						$dadosEndereco['PropostaCredEndereco']['codigo_documento'] = Comum::soNumero($dados['PropostaCredenciamento']['codigo_documento']);

						// atualiza endereco
						if(!$model_PropostaCredEndereco->atualizar($dadosEndereco))
							$invalidadeFields += $this->trata_erros('PropostaCredEndereco', $model_PropostaCredEndereco->validationErrors, $key);

					} else {
						
						if(($key == 0) && ($etapa != '2')) {
							$dados['PropostaCredEndereco'][$key]['matriz'] = '1';
							$dados['PropostaCredEndereco'][$key]['codigo_documento'] = Comum::soNumero($dados['PropostaCredenciamento']['codigo_documento']);
						} else {
							$dados['PropostaCredEndereco'][$key]['matriz'] = '0';
							$dados['PropostaCredEndereco'][$key]['codigo_documento'] = Comum::soNumero($endereco['codigo_documento']);
						}

						if(isset($codigo_empresa) && $codigo_empresa) {
							$dados['PropostaCredEndereco'][$key]['codigo_empresa'] = $codigo_empresa;
						}

						// inclui no array de endereco a ser inserido, o id da proposta!
				        $dados['PropostaCredEndereco'][$key]['cep'] = Comum::soNumero($endereco['cep']);
				        $dados['PropostaCredEndereco'][$key]['codigo_proposta_credenciamento'] = ($etapa != '2' ? $this->getInsertID() : $dados['PropostaCredenciamento']['codigo']);
				        
			        	// inclui endereco
			            if (!$model_PropostaCredEndereco->incluir($dados['PropostaCredEndereco'][$key])) {
							$invalidadeFields += $this->trata_erros('PropostaCredEndereco', $model_PropostaCredEndereco->validationErrors, $key);
			            }						
					}
				}				
			}
			
			if(count($invalidadeFields))
				$model_PropostaCredEndereco->validationErrors = $invalidadeFields;
			
			if($etapa != '1') {
				
				// inclui exames
				$invalidadeFields = array();
				
				// verifica se é credenciado de saúde
				if(isset($dados['PropostaCredProduto']['59']) && ($dados['PropostaCredProduto']['59'] == '1')) {
					
					
					foreach($dados['PropostaCredExame'] as $key => $campos) {
						
						if(!empty($campos['codigo_exame']) && !empty($campos['valor'])) {
							
							if($data_Exame = $model_PropostaCredExame->find('first', array('conditions' => array('codigo_exame' => $campos['codigo_exame'], 'codigo_proposta_credenciamento' => $dados['PropostaCredenciamento']['codigo'])))) {
									$data_Exame['PropostaCredExame']['valor_contra_proposta'] = "";
								$data_Exame['PropostaCredExame']['valor'] = $campos['valor'];
								
								$model_PropostaCredExame->atualizar($data_Exame);
							} else {
								
								$insert_exame = array(
				        				'codigo_proposta_credenciamento' => ($etapa != '2' ? $this->getInsertID() : $dados['PropostaCredenciamento']['codigo']),
				        				'codigo_exame' => $campos['codigo_exame'],
					            		'valor' => $campos['valor']
								);
								
								if(isset($codigo_empresa) && $codigo_empresa)
									$insert_exame['codigo_empresa'] = $codigo_empresa;
								
				        		// insere relacao exame (servico) / proposta
					            if (!$model_PropostaCredExame->incluir($insert_exame)) 
					            		$invalidadeFields += $this->trata_erros('PropostaCredExame', $model_PropostaCredExame->validationErrors, $key);							
							}
						} else {
							$invalidadeFields[$key] = array('valor' => 'Falta informações para compelar a relação de exames!');	
						}    		
		        	}					
				}
				
				if(count($invalidadeFields))
					$model_PropostaCredExame->validationErrors = $invalidadeFields;
									
				// verifica se é credenciado de engenharia
				$invalidadeFields = array();
				
				if(isset($dados['PropostaCredProduto']['60']) && ($dados['PropostaCredProduto']['60'] == '1')) {
					
					if(isset($dados['PropostaCredProduto']['59']) && $dados['PropostaCredProduto']['59'] != '1') {
						$dados['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] = StatusPropostaCred::AGUARDANDO_ANALISE_PROPOSTA;
					}
					
					foreach($dados['PropostaCredEngenharia'] as $key => $campos) {
						if(!empty($campos['codigo_exame']) && ($campos['codigo_exame'] != '0')) {
							
							// verifica se ja não existe!
							if(! $model_PropostaCredEngenharia->find('first', array('conditions' => array('codigo_proposta_credenciamento' => ($etapa != '2' ? $this->getInsertID() : $dados['PropostaCredenciamento']['codigo']), 'codigo_exame' => $campos['codigo_exame'])))) {
								
								$insert_engenharia = array(
				        				'codigo_proposta_credenciamento' => ($etapa != '2' ? $this->getInsertID() : $dados['PropostaCredenciamento']['codigo']),
				        				'codigo_exame' => $campos['codigo_exame']
								);
								
								if(isset($codigo_empresa) && $codigo_empresa)
									$insert_engenharia['codigo_empresa'] = $codigo_empresa;
								
								// insere relacao seguranca / proposta
					            if (!$model_PropostaCredEngenharia->incluir($insert_engenharia))  {
				        				$invalidadeFields += $this->trata_erros('PropostaCredEngenharia', $model_PropostaCredEngenharia->validationErrors, $key);
				        			}							
							}
							
						} else {
							$invalidadeFields[$key] = array('codigo_exame' => 'Deve ter ao menos um serviço!');	
						}    		
		        	}
				}
				
				if(count($invalidadeFields))
					$model_PropostaCredEngenharia->validationErrors = $invalidadeFields;
					
				// insere medicos
				$invalidadeFields = array();
				$invalidadeFields_Medico = array();
				
				// verifica se é credenciado de saúde
				if(isset($dados['PropostaCredProduto']['59']) && ($dados['PropostaCredProduto']['59'] == '1')) {
					
					$model_PropostaCredMedico->deleteAll(array("codigo_proposta_credenciamento" => $dados['PropostaCredenciamento']['codigo']));
					
					foreach($dados['Medico'] as $key => $campos) {
						if(!empty($campos['nome']) && !empty($campos['numero_conselho']) && !empty($campos['conselho_uf']) && !empty($campos['codigo_conselho_profissional'])) {
								$campos['numero_conselho'] = Comum::soNumero($campos['numero_conselho']);

							// verifica se ja existe o medico cadastrado na base !!!
							$dadosMedico = $model_Medico->find('first', array('conditions' =>  array(
								'numero_conselho' => $campos['numero_conselho'],
								'conselho_uf' => $campos['conselho_uf'],
								'codigo_conselho_profissional' => $campos['codigo_conselho_profissional']
							)));
							
							if($dadosMedico) {
							
								// atualiza nome!
								$dadosMedico['Medico']['nome'] = $campos['nome'];
								$dadosMedico['MedicoEndereco'] = $dados['PropostaCredEndereco'][0];
								
								$ja_gravado = $model_MedicoEndereco->find('first', array('conditions' => array('codigo_medico' => $dadosMedico['Medico']['codigo']), 'fields' => array('codigo')));
								$dadosMedico['MedicoEndereco']['codigo'] = $ja_gravado['MedicoEndereco']['codigo'];
								
								if (!$model_Medico->atualizar($dadosMedico)) {
					            	$invalidadeFields_Medico = $this->trata_erros('Medico', $model_Medico->validationErrors, $key);
								} else {
									$idMedico = $dadosMedico['Medico']['codigo'];
								}
								
							} else {
								
								if(isset($codigo_empresa) && $codigo_empresa)
									$campos['codigo_empresa'] = $codigo_empresa;

								if (!$model_Medico->incluir(array('Medico' => $campos, 'MedicoEndereco' => $dados['PropostaCredEndereco'][0]))) {
									$invalidadeFields_Medico = $this->trata_erros('Medico', $model_Medico->validationErrors, $key);
								} else {
									$idMedico = $model_Medico->getInsertID();
								}
							}
							
							if(isset($idMedico) && $idMedico) {
								
								// insere relacao medico / proposta
								$insert_proposta_medico =  array(
			                		'codigo_proposta_credenciamento' => ($etapa != '2' ? $this->getInsertID() : $dados['PropostaCredenciamento']['codigo']),
									'codigo_medico' => $idMedico
				                );
					            
					            if(isset($codigo_empresa) && $codigo_empresa)
					            	$insert_proposta_medico['codigo_empresa'] = $codigo_empresa;
					            
				        		// insere relacao medico / proposta
					            if (!$model_PropostaCredMedico->incluir($insert_proposta_medico)) 
									$invalidadeFields = $this->trata_erros('PropostaCredMedico', $model_PropostaCredMedico->validationErrors, $key);
							}
							
							// limpa variavel
							unset($idMedico);
							unset($dadosMedico);

						} else {
							$invalidadeFields_Medico[$key] = array('conselho_uf' => 'Existem informações sem preencher!');
						}
		        	}
				}
				
	        	if(count($invalidadeFields_Medico))
	        		$model_Medico->validationErrors = $invalidadeFields_Medico;
	        		
	        	if(count($invalidadeFields))
	        		$model_PropostaCredMedico->validationErrors = $invalidadeFields;

			    // horarios
			    $invalidadeFields = array();

			    // debug($dados);exit;
			    
			    // verifica se é credenciado de saúde
			    if(isset($dados['PropostaCredProduto']['59']) && ($dados['PropostaCredProduto']['59'] == '1')) {
			    	$model_Horario->deleteAll(array("codigo_proposta_credenciamento" => $dados['PropostaCredenciamento']['codigo']));
			    	
			    	// foreach($dados['Horario'] as $key => $periodo) {
			    		$periodo = array();
		        		$dias_selecionados = "";
		        		
		        		if(isset($dados['Horario'][0]['dias_semana'])) {
			        		foreach($dados['Horario'][0]['dias_semana'] as $k => $dia) {
			        			if($dia){
    								$dias_selecionados .= $k . ",";
    							}
			        		}
			        	}

			        	// debug($dados['Horario']);
		        		
		        		if($dias_selecionados) {

		        			$periodo = $model_Horario->find('first',array('conditions' => array('codigo_proposta_credenciamento' => $dados['PropostaCredenciamento']['codigo'])));

		        			$periodo['Horario']['dias_semana'] = substr($dias_selecionados,0,-1);
							$periodo['Horario']['de_hora'] = Comum::soNumero($dados['Horario'][0]['de_hora']);
    				    	$periodo['Horario']['ate_hora'] = Comum::soNumero($dados['Horario'][0]['ate_hora']);
							$periodo['Horario']['codigo_proposta_credenciamento'] = ($etapa != '2' ? $this->getInsertID() : $dados['PropostaCredenciamento']['codigo']);
							$periodo['Horario']['horario_atendimento_diferenciado'] = $dados['Horario']['horario_atendimento_diferenciado'];

							// debug($periodo);exit;
		
							if(isset($codigo_empresa) && $codigo_empresa)
								$periodo['Horario']['codigo_empresa'] = $codigo_empresa;
							
				            if (!$model_Horario->incluir($periodo))
								$invalidadeFields += $this->trata_erros('Horario', $model_Horario->validationErrors, $key);	
		        		} else {
							$invalidadeFields[$key] = array('ate_hora' => 'Deve ter ao menos um periodo!');
		        		}
		        	// }
			    }

	        	if(count($invalidadeFields))
	        		$model_Horario->validationErrors = $invalidadeFields;

	        	//variavel com os fields invalidos
				$invalidadeFieldsHDiferenciado = '';

				// verifica se é credenciado de saúde para funcionar 
				if(isset($dados['HorarioDiferenciado']) && count($dados['HorarioDiferenciado'])) {

					$model_HorarioDiferenciado->deleteAll(array("codigo_proposta_credenciamento" => $dados['PropostaCredenciamento']['codigo']));
					
					$periodoHorarioDiferenciado = array();
			    	
			    	foreach($dados['HorarioDiferenciado'] as $key => $periodoHD) {

			    		if(!isset($dados['HorarioDiferenciado'][$key]['codigo_servico'])) {
			    			continue;
			    		}
			    		else if(empty($dados['HorarioDiferenciado'][$key]['codigo_servico'])) {
			    			continue;
			    		}
			    		
	    				$dias_selecionados = "";
	    				
	    				if(isset($dados['HorarioDiferenciado'][$key]['dias_semana'])) {
	    					foreach($dados['HorarioDiferenciado'][$key]['dias_semana'] as $k => $dia) {
	    						if($dia) {
	    							$dias_selecionados .= $k . ",";
	    						}
	    					}    					
	    				}

						if($dias_selecionados) {
	    					$periodoHorarioDiferenciado['HorarioDiferenciado']['dias_semana'] = substr($dias_selecionados,0,-1);
	    				    $periodoHorarioDiferenciado['HorarioDiferenciado']['de_hora'] = Comum::soNumero($dados['HorarioDiferenciado'][$key]['de_hora']);
	    				    $periodoHorarioDiferenciado['HorarioDiferenciado']['ate_hora'] = Comum::soNumero($dados['HorarioDiferenciado'][$key]['ate_hora']);
	    				    $periodoHorarioDiferenciado['HorarioDiferenciado']['codigo_proposta_credenciamento'] = ($etapa != '2' ? $this->getInsertID() : $dados['PropostaCredenciamento']['codigo']);
	    				    $periodoHorarioDiferenciado['HorarioDiferenciado']['codigo_servico'] = $dados['HorarioDiferenciado'][$key]['codigo_servico'];
	    				    $periodoHorarioDiferenciado['HorarioDiferenciado']['ativo'] = 1; 

	    				    if (!$model_HorarioDiferenciado->incluir($periodoHorarioDiferenciado))
								$invalidadeFieldsHDiferenciado += $this->trata_erros('HorarioDiferenciado', $model_HorarioDiferenciado->validationErrors, $key);
						} else {
							$invalidadeFieldsHDiferenciado[$key] = array('ate_hora' => 'Deve ter ao menos um periodo!');
		        		}
					}//fim foreach
				}

				if(!empty($invalidadeFieldsHDiferenciado)) {
					$model_HorarioDiferenciado->validationErrors = $invalidadeFieldsHDiferenciado;
				}
			}
			
			if($etapa == '2') {
				
				$invalidadeFields = array();
				foreach($dados['PropostaCredProduto'] as $key => $campo) {
					if($campo == '1') {
						
						
						if(! $model_PropostaCredProduto->find('first', array('conditions' => array("codigo_proposta_credenciamento" => $dados['PropostaCredenciamento']['codigo'], 'codigo_produto' => $key)))) {
							
							$insert_PropostaCredProduto = array( 
								'codigo_proposta_credenciamento' => (int) $dados['PropostaCredenciamento']['codigo'], 
								'codigo_produto' => (int) $key 
							);
							
							if(isset($codigo_empresa) && $codigo_empresa)
								$insert_PropostaCredProduto['codigo_empresa'] = $codigo_empresa;

							if(! $model_PropostaCredProduto->incluir($insert_PropostaCredProduto)) {
								$invalidadeFields += $this->trata_erros('PropostaCredProduto', $model_PropostaCredProduto->validationErrors, $key);
							}
						}							
					}
				}
				
	        	if(count($invalidadeFields))
	        		$model_PropostaCredProduto->validationErrors = $invalidadeFields;
	        		
	            // atualiza proposta
				$this->atualizar($dados);
			}
			
			// Muda de Status (caso haja divervencia nos valores dos exames)
			if($muda_status) {
				$email = $this->read('email', $dados['PropostaCredenciamento']['codigo']);
				$this->disparaEmail($dados['PropostaCredenciamento'], 'Olá ' . $dados['PropostaCredenciamento']['nome_fantasia'] . ', temos uma contra-proposta em seus exames.', 'envio_contra_proposta', $email['PropostaCredenciamento']['email'], null);
			}
			
			if($etapa == '1') {
				if(!(count($this->validationErrors) || count($model_PropostaCredEndereco->validationErrors))) {
					
					$this->commit();
		            return true;

				} else {
					return false;
				} 
			} else {
				
				if(!(
					count($this->validationErrors) ||
					count($model_PropostaCredEndereco->validationErrors) || 
					count($model_PropostaCredExame->validationErrors) ||
					count($model_PropostaCredEngenharia->validationErrors) ||
					count($model_PropostaCredProduto->validationErrors) ||					
					count($model_Medico->validationErrors) || 
					count($model_PropostaCredMedico->validationErrors) ||
					count($model_Horario->validationErrors) ||
					count($model_HorarioDiferenciado->validationErrors) 
				)) {
					
					$this->commit();
		            return true;
				} else {
					return false;
				} 
			}

        } catch (Exception $ex) {
            $this->rollback();
            return false;
        }
    }
    
    public function atualizarDados($dados) {
    	
    	// MODELS
		$model_PropostaCredEndereco = & ClassRegistry::init('PropostaCredEndereco');
    	$model_Medico = & ClassRegistry::init('Medico');
    	$model_PropostaCredMedico = & ClassRegistry::init('PropostaCredMedico');
    	$model_Horario = & ClassRegistry::init('Horario');
    	$model_HorarioDiferenciado = & ClassRegistry::init('HorarioDiferenciado');
    	
    	// corrigo cadastramento codigo banco, pq tem codigo 000 :/
    	if(isset($dados['PropostaCredenciamento']['numero_banco']) && $dados['PropostaCredenciamento']['numero_banco'] == 0) {
    		$dados['PropostaCredenciamento']['numero_banco'] = null;
    	}
    	 
    	// tira maskara formatacao do cnpj
    	if(isset($dados['PropostaCredenciamento']['codigo_documento']) && $dados['PropostaCredenciamento']['codigo_documento']) {
    		$dados['PropostaCredenciamento']['codigo_documento'] = Comum::soNumero($dados['PropostaCredenciamento']['codigo_documento']);
    	}
    	
   		$dados['PropostaCredenciamento']['telefone'] = Comum::soNumero($dados['PropostaCredenciamento']['telefone']);
   		$dados['PropostaCredenciamento']['fax'] = Comum::soNumero($dados['PropostaCredenciamento']['fax']);
   		$dados['PropostaCredenciamento']['celular'] = Comum::soNumero($dados['PropostaCredenciamento']['celular']);
   		
    	try {
    			
    		$this->query('begin transaction');
    		
    		if(isset($dados['PropostaCredenciamento']['acao'])) {
    			unset($dados['PropostaCredenciamento']['acao']);
    		}
    		
    		// formata campos: PropostaCredEndereco
    		$invalidadeFields = array();
    			
    		if(isset($dados['PropostaCredEndereco'])) {
    			foreach($dados['PropostaCredEndereco'] as $key => $endereco) {

   					$dadosEndereco = $model_PropostaCredEndereco->find('first', array('conditions' => array('codigo' => $endereco['codigo'])));

   					$dadosEndereco['PropostaCredEndereco']['codigo'] =  $endereco['codigo'];
   					$dadosEndereco['PropostaCredEndereco']['cep'] =  $endereco['cep'];
   					$dadosEndereco['PropostaCredEndereco']['logradouro'] =  $endereco['logradouro'];
   					$dadosEndereco['PropostaCredEndereco']['numero'] =  $endereco['numero'];
   					$dadosEndereco['PropostaCredEndereco']['complemento'] =  $endereco['complemento'];
   					$dadosEndereco['PropostaCredEndereco']['bairro'] =  $endereco['bairro'];
   					$dadosEndereco['PropostaCredEndereco']['cidade'] =  $endereco['cidade'];
   					$dadosEndereco['PropostaCredEndereco']['estado'] =  $endereco['estado'];
   					$dadosEndereco['PropostaCredEndereco']['codigo_documento'] = Comum::soNumero($dados['PropostaCredenciamento']['codigo_documento']);
   					$dadosEndereco['PropostaCredEndereco']['codigo_cidade_endereco'] = NULL;
   					$dadosEndereco['PropostaCredEndereco']['codigo_estado_endereco'] = NULL;
   					// atualiza endereco
   					if(!$model_PropostaCredEndereco->atualizar($dadosEndereco))
   						$invalidadeFields += $this->trata_erros('PropostaCredEndereco', $model_PropostaCredEndereco->validationErrors, $key);
    	
    			}
    		}
    		
    		if(count($invalidadeFields))
    			$model_PropostaCredEndereco->validationErrors = $invalidadeFields;
    			
    		$invalidadeFields = array();
			$invalidadeFields_Medico = array();
			
			if(isset($dados['Medico']) && count($dados['Medico'])) {

				// exclui relacionamentos (proposta / medico)
// 				$model_PropostaCredMedico->deleteAll(array("codigo_proposta_credenciamento" => $dados['PropostaCredenciamento']['codigo']));
				
				foreach($dados['Medico'] as $key => $campos) {
					
					if(!empty($campos['nome']) && !empty($campos['numero_conselho']) && !empty($campos['conselho_uf']) && !empty($campos['codigo_conselho_profissional'])) {
						$campos['numero_conselho'] = Comum::soNumero($campos['numero_conselho']);
						
						if(isset($campos['codigo'])) {
							if(!$model_Medico->atualizar(array('Medico' => $campos))) {
								$invalidadeFields_Medico = $this->trata_erros('Medico', $model_Medico->validationErrors, $key);
							}
						} else {
							if (!$model_Medico->incluir(array('Medico' => $campos))) {
								$invalidadeFields_Medico = $this->trata_erros('Medico', $model_Medico->validationErrors, $key);
							}
						}
						
// 						if(!count($invalidadeFields_Medico)) {
// 							// insere relacao medico / proposta
// 							if (!$model_PropostaCredMedico->incluir( array('Medico' =>array(
// 									'codigo_proposta_credenciamento' => $dados['PropostaCredenciamento']['codigo'],
// 									'codigo_medico' => (isset($campos['codigo']) && $campos['codigo']) ? $campos['codigo'] : $model_Medico->getInsertID(),
// 							))))
// 								$invalidadeFields = $this->trata_erros('PropostaCredMedico', $model_PropostaCredMedico->validationErrors, $key);							
// 						}
								 
					} else {
						$invalidadeFields_Medico[$key] = array('conselho_uf' => 'Existem informações sem preencher!');
					}
				}
			}
			
    		if(count($invalidadeFields_Medico))
    			$model_Medico->validationErrors = $invalidadeFields_Medico;
    							 
    		if(count($invalidadeFields))
    			$model_PropostaCredMedico->validationErrors = $invalidadeFields;
    	
			// horarios
    		$invalidadeFields = array();

    		// debug($dados);exit;
    				     
    		// verifica se é credenciado de saúde
			if(isset($dados['Horario']) && count($dados['Horario'])) {
				
		    	// foreach($dados['Horario'] as $key => $periodo) {
		    		$periodo = array();
    				$dias_selecionados = "";
    				
    				if(isset($dados['Horario'][0]['dias_semana'])) {
    					foreach($dados['Horario'][0]['dias_semana'] as $k => $dia) {
    						if($dia) {
    							$dias_selecionados .= $k . ",";
    						}
    					}    					
    				}

					if($dias_selecionados) {

						$periodo = $model_Horario->find('first',array('conditions' => array('codigo_proposta_credenciamento' => $dados['PropostaCredenciamento']['codigo'])));

    					$periodo['Horario']['dias_semana'] = substr($dias_selecionados,0,-1);
    				    $periodo['Horario']['de_hora'] = Comum::soNumero($dados['Horario']['de_hora'][0]);
    				    $periodo['Horario']['ate_hora'] = Comum::soNumero($dados['Horario']['ate_hora'][0]);
    				    $periodo['Horario']['codigo_proposta_credenciamento'] = $dados['PropostaCredenciamento']['codigo'];
    				    $periodo['Horario']['horario_atendimento_diferenciado'] = $dados['Horario']['horario_atendimento_diferenciado'];

    				    // debug($periodo);

    				    if (!$model_Horario->atualizar($periodo)) {
    				    	$invalidadeFields += $this->trata_erros('Horario', $model_Horario->validationErrors, $key);
    				    }
    				    	
					}
				// }
			}
    	
			if(count($invalidadeFields)) {
				$model_Horario->validationErrors = $invalidadeFields;
			}
			//variavel com os fields invalidos
			$invalidadeFieldsHDiferenciado = '';
			// verifica se é credenciado de saúde para funcionar 
			if(isset($dados['HorarioDiferenciado']) && count($dados['HorarioDiferenciado'])) {
				$periodoHorarioDiferenciado = array();
		    	foreach($dados['HorarioDiferenciado'] as $key => $periodoHD) {

		    		if(!isset($dados['HorarioDiferenciado'][$key]['codigo_servico'])) {
		    			continue;
		    		}
		    		
    				$dias_selecionados = "";
    				
    				if(isset($dados['HorarioDiferenciado'][$key]['dias_semana'])) {
    					foreach($dados['HorarioDiferenciado'][$key]['dias_semana'] as $k => $dia) {
    						if($dia) {
    							$dias_selecionados .= $k . ",";
    						}
    					}    					
    				}

					if($dias_selecionados) {
    				    //verifica se tenho cadastro de um hr diferenciado
    				    $periodoHorarioDiferenciado = $model_HorarioDiferenciado->find('first',array('conditions' => array('codigo_servico' => $dados['HorarioDiferenciado'][$key]['codigo_servico'],'codigo_proposta_credenciamento' => $dados['HorarioDiferenciado'][$key]['codigo_proposta_credenciamento'])));

    					$periodoHorarioDiferenciado['HorarioDiferenciado']['dias_semana'] = substr($dias_selecionados,0,-1);
    				    $periodoHorarioDiferenciado['HorarioDiferenciado']['de_hora'] = Comum::soNumero($dados['HorarioDiferenciado'][$key]['de_hora']);
    				    $periodoHorarioDiferenciado['HorarioDiferenciado']['ate_hora'] = Comum::soNumero($dados['HorarioDiferenciado'][$key]['ate_hora']);
    				    $periodoHorarioDiferenciado['HorarioDiferenciado']['codigo_proposta_credenciamento'] = $dados['HorarioDiferenciado'][$key]['codigo_proposta_credenciamento'];
    				    $periodoHorarioDiferenciado['HorarioDiferenciado']['codigo_horario'] = $dados['HorarioDiferenciado'][$key]['codigo_horario'];
    				    $periodoHorarioDiferenciado['HorarioDiferenciado']['codigo_servico'] = $dados['HorarioDiferenciado'][$key]['codigo_servico'];
    				    
    				    //verifica se tem que atualizar ou incluir
    				    if(!isset($periodoHorarioDiferenciado['HorarioDiferenciado']['codigo'])) {
    				    	//verifica se atualizou corretamente
	    				    if(!$model_HorarioDiferenciado->incluir($periodoHorarioDiferenciado)) {
	    				    	$invalidadeFieldsHDiferenciado += $this->trata_erros('HorarioDiferenciado', $model_HorarioDiferenciado->validationErrors, $key);
	    				    }
    				    }
    				    else {
    				    	//verifica se atualizou corretamente
	    				    if(!$model_HorarioDiferenciado->atualizar($periodoHorarioDiferenciado)) {
	    				    	$invalidadeFieldsHDiferenciado += $this->trata_erros('HorarioDiferenciado', $model_HorarioDiferenciado->validationErrors, $key);
	    				    }
    				    }//fim verificacao se existe codigo

					}
				}//fim foreach
			}

			if(!empty($invalidadeFieldsHDiferenciado)) {
				$model_HorarioDiferenciado->validationErrors = $invalidadeFieldsHDiferenciado;
			}
			
			// ve(array($this->validationErrors, $model_PropostaCredEndereco->validationErrors, $model_Medico->validationErrors, $model_PropostaCredMedico->validationErrors, $model_Horario->validationErrors));
			// exit;
			
			// debug($this->validationErrors);
			// debug($model_PropostaCredEndereco->validationErrors);
			// debug($model_Medico->validationErrors);
			// debug($model_PropostaCredMedico->validationErrors);
			// debug($model_Horario->validationErrors);
			// debug($model_HorarioDiferenciado->validationErrors);

			// debug((
   //  			count($this->validationErrors) ||
   //  			count($model_PropostaCredEndereco->validationErrors) ||
   //  			count($model_Medico->validationErrors) ||
   //  			count($model_PropostaCredMedico->validationErrors) ||
   //  			count($model_Horario->validationErrors) ||
   //  			count($model_HorarioDiferenciado->validationErrors)
   //  			));
			// debug(count($this->validationErrors));
   //  		debug(count($model_PropostaCredEndereco->validationErrors));
   //  		debug(count($model_Medico->validationErrors));
   //  		debug(count($model_PropostaCredMedico->validationErrors));
   //  		debug(count($model_Horario->validationErrors));
   //  		debug(count($model_HorarioDiferenciado->validationErrors));
			// exit;

			// atualiza proposta
			$this->atualizar($dados);
			
    		if(!(
    			count($this->validationErrors) ||
    			count($model_PropostaCredEndereco->validationErrors) ||
    			count($model_Medico->validationErrors) ||
    			count($model_PropostaCredMedico->validationErrors) ||
    			count($model_Horario->validationErrors) ||
    			count($model_HorarioDiferenciado->validationErrors)
    			)) {    							
    				$this->commit();
    				return true;
    			} else {
    				return false;
    			}
    	
    	} catch (Exception $ex) {
    		$this->rollback();
    		return false;
    	}
    	
    }
    
    private function trata_erros($model, $erros, $key) {
    	$retorno = array();
    	foreach($erros as $campo => $mensagem)
			$retorno[$key][$campo] = $mensagem;
			
    	return $retorno;
    }
	
	function converteFiltrosEmConditions($filtros) {
		$conditions = array ();
		if (isset ( $filtros ['razao_social'] ) && ! empty ( $filtros ['razao_social'] )) {
			$conditions ['OR'][$this->name . '.razao_social LIKE'] = '%' . $filtros ['razao_social'] . '%';
			$conditions ['OR'][$this->name . '.nome_fantasia LIKE'] = '%' . $filtros ['razao_social'] . '%';
		}
		
		if (isset ( $filtros ['numero'] ) && ! empty ( $filtros ['numero'] )) {
			$conditions [$this->name . '.codigo'] = $filtros ['numero'];
		}		
		
		if (! empty ( $filtros ['codigo_status_proposta_credenciamento'] )) {
			$conditions ['codigo_status_proposta_credenciamento = '] = $filtros ['codigo_status_proposta_credenciamento'];
		}
		
		if (isset ( $filtros ['polaridade'] ) && $filtros ['polaridade'] != '') {
			$conditions ['StatusPropostaCred.polaridade'] = $filtros ['polaridade'];
		}
		
		if (isset($filtros ['ativo'])) {
			if($filtros['ativo'] == '0') {
				$conditions [$this->name . '.ativo'] = null;
			} else if($filtros['ativo'] == '1'){
				$conditions [$this->name . '.ativo'] = '1';
			}
		}
		
		return $conditions;
	}

    function atualizarStatus($dados, $novo_status) {
    	
		try {
			$this->query('begin transaction');

			$dados['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] = $novo_status;
			unset($dados['PropostaCredenciamento']['novo_status']);
			
    		if (!parent::atualizar($dados, false))
    			throw new Exception('Não atualizou proposta!');
			
			// confirma transacao!!!
			$this->commit();
			return true;
        } catch (Exception $ex) {
            $this->rollback();
            return false;
        }
    }
    
    public function atualizarEmail($dados, $mudou_email = false) {
    	require_once(ROOT . DS . 'app' . DS . 'vendors' . DS . 'buonny' . DS . 'encriptacao.php');
    	$Encriptador = new Buonny_Encriptacao();
    	
    	$model_Usuario = & ClassRegistry::init('Usuario');

	    try {
			$this->query('begin transaction');

	       	if($mudou_email) {
	    		if (!parent::atualizar($dados, false, array('codigo', 'email'))) {
	    			throw new Exception('Não atualizou proposta!');
	    		}
	    			
    		}

    		$info_usuario = $model_Usuario->find('first', array('conditions' => array('codigo_proposta_credenciamento' => $dados['PropostaCredenciamento']['codigo'])));
    		
    		if($mudou_email) {
				$apelido = !empty($dados['PropostaCredenciamento']['email']) ? explode('@', $dados['PropostaCredenciamento']['email']) : explode(' ', $dados['PropostaCredenciamento']['responsavel_administrativo']);
				$user_info = $model_Usuario->find('all', array('conditions' => array('apelido' => $apelido[0])));
	
				$info_usuario['Usuario']['apelido'] = $apelido[0] . "." . $dados['PropostaCredenciamento']['codigo'];
				$info_usuario['Usuario']['email'] = $dados['PropostaCredenciamento']['email'];
    		}
    		
   			// $info_usuario['Usuario']['senha'] = str_pad ( ( string ) mt_rand ( 0, 999999 ), 6, '0', STR_PAD_LEFT );
   			$senha_desencriptografada = $Encriptador->desencriptar($info_usuario['Usuario']['senha']);
   			
   			// retira senha
   			unset($info_usuario['Usuario']['senha']);
   			
			if($model_Usuario->atualizar($info_usuario, false)) {
				$info_usuario['Usuario']['senha'] = $senha_desencriptografada;
				$this->disparaEmail($info_usuario, 'Nova Senha', 'envio_usuario_senha_email', $dados['PropostaCredenciamento']['email'], NULL);
			}
			
			// confirma transacao!!!
			$this->commit();					
			return true;			
			    			
	    } catch(Exception $e) {
            $this->rollback();
            return false;	    	
	    }    	
    }
    
    
	public function disparaEmail($dados, $assunto, $template, $to, $codigo = null) {

		
		if(Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO) {
			$to = 'tid@ithealth.com.br';
			$cc = null;
		} else {
			$cc = 'credenciamento@rhhealth.com.br';
		}
		
		App::import('Component', array('StringView', 'Mailer.Scheduler'));
		
		$this->stringView = new StringViewComponent();
		$this->scheduler = new SchedulerComponent();
		$this->stringView->reset();
		$this->stringView->set('dados', $dados);
		
		if($codigo)
			$this->stringView->set('codigo', $codigo);
		
		$content = $this->stringView->renderMail($template);

		return $this->scheduler->schedule($content, array (
			'from' => 'portal@rhhealth.com.br',
			'cc' => $cc,
			'to' => $to,
			'subject' => $assunto
		));
	}
}
