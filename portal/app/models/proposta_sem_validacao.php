<?php
class PropostaSemValidacao extends AppModel {

    var $name = 'PropostaSemValidacao';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'propostas_credenciamento';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
	var $validate = array(
        'nome_fantasia' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Nome Fantasia!'
		),
        'telefone' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Numero de Telefone com DDD!'
		),		
        'codigo_documento' => array(
			'isUnique' => array(
                'rule' => 'unicoCNPJ',
                'message' => 'CNPJ já existente na base!',
            ),            
            'documentoValido' => array(
                'rule' => 'documentoValido',
                'message' => 'CNPJ inválido, verifique!',
            )
        ),		
        'email' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o E-mail!',
            )
        )	
	);

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
    					$this->name . '.codigo_status_proposta_credenciamento = StatusPropostaCred.codigo'
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
    
    function incluir($dados, $etapa = null) {
    	
    	// MODELS
    	$model_PropostaCredEndereco = & ClassRegistry::init('PropostaCredEndereco2');
    	$model_Servico = & ClassRegistry::init('Servico');
    	$model_PropostaCredExame = & ClassRegistry::init('PropostaCredExame');
    	$model_Medico = & ClassRegistry::init('Medico');
    	$model_PropostaCredMedico = & ClassRegistry::init('PropostaCredMedico');
    	$model_Horario = & ClassRegistry::init('Horario');
    	$model_StatusPropostaCred = & ClassRegistry::init('StatusPropostaCred');
    	$model_PropostaCredProduto = & ClassRegistry::init('PropostaCredProduto');
    	
   		$dados['PropostaSemValidacao']['codigo_status_proposta_credenciamento'] = StatusPropostaCred::PRECADASTRO;
       	$dados['PropostaSemValidacao']['codigo_documento'] = Comum::soNumero($dados['PropostaSemValidacao']['codigo_documento']);
        $dados['PropostaSemValidacao']['telefone'] = Comum::soNumero($dados['PropostaSemValidacao']['telefone']);
        $dados['PropostaSemValidacao']['fax'] = Comum::soNumero($dados['PropostaSemValidacao']['fax']);
        $dados['PropostaSemValidacao']['celular'] = Comum::soNumero($dados['PropostaSemValidacao']['celular']);
        $dados['PropostaSemValidacao']['ativo'] = 1;
        
        $erros = array();
		try {
			
            $this->query('begin transaction');

            // inclui proposta
            parent::incluir($dados['PropostaSemValidacao']);

			// formata campos: PropostaCredEndereco
			$invalidadeFields = array();
			
			if(isset($dados['PropostaCredEndereco2'])) {
				foreach($dados['PropostaCredEndereco2'] as $key => $endereco) {
		        
					if($key == 0) {
						$dados['PropostaCredEndereco2'][$key]['matriz'] = '1';
						$dados['PropostaCredEndereco2'][$key]['codigo_documento'] = Comum::soNumero($dados['PropostaSemValidacao']['codigo_documento']);
					} else {
						$dados['PropostaCredEndereco2'][$key]['matriz'] = '0';
						$dados['PropostaCredEndereco2'][$key]['codigo_documento'] = Comum::soNumero($endereco['codigo_documento']);
					}
	
					// inclui no array de endereco a ser inserido, o id da proposta!
			        $dados['PropostaCredEndereco2'][$key]['cep'] = Comum::soNumero($endereco['cep']);
			        $dados['PropostaCredEndereco2'][$key]['codigo_proposta_credenciamento'] = ($etapa != '2' ? $this->getInsertID() : $dados['PropostaSemValidacao']['codigo']);
			        
		        	// inclui endereco
		            if (!$model_PropostaCredEndereco->incluir($dados['PropostaCredEndereco2'][$key])) {
						$invalidadeFields += $this->trata_erros('PropostaCredEndereco2', $model_PropostaCredEndereco->validationErrors, $key);
		            }
				}				
			}
			
			if(count($invalidadeFields))
				$model_PropostaCredEndereco->validationErrors = $invalidadeFields;		

			// inclui exames
			$invalidadeFields = array();
			if(isset($dados['PropostaCredProduto']['59']) && ($dados['PropostaCredProduto']['59'] == '1')) {
				
	        	foreach($dados['PropostaCredExame'] as $key => $campos) {
					if(!empty($campos['codigo_exame'])) {
						
						if(! $model_PropostaCredExame->find('first', array('conditions' => array('codigo_exame' => $campos['codigo_exame'], 'codigo_proposta_credenciamento' => $this->getInsertID())))) {
							
			        		// insere relacao medico / proposta
				            if (!$model_PropostaCredExame->incluir(array(
			        				'codigo_proposta_credenciamento' => ($etapa != '2' ? $this->getInsertID() : $dados['PropostaSemValidacao']['codigo']),
			        				'codigo_exame' => $campos['codigo_exame'],
				            		'valor' => !empty($campos['valor']) ? $campos['valor'] : '0,00',
				            		'valor_contra_proposta' => NULL,
									'liberacao' => $campos['liberacao']
			        			))) 
				            	$invalidadeFields += $this->trata_erros('PropostaCredExame', $model_PropostaCredExame->validationErrors, $key);							
						}

					} else {
						$invalidadeFields[$key] = array('valor' => 'Deve ter ao menos um exame!');	
					}    		
	        	}
			}
			
			$invalidadeFields = array();
			if(isset($dados['PropostaCredProduto']['60']) && ($dados['PropostaCredProduto']['60'] == '1')) {
				
				foreach($dados['PropostaCredEngenharia'] as $key => $campos) {		
					if(!empty($campos['codigo_exame'])) {
						
						if(! $model_PropostaCredExame->find('first', array('conditions' => array('codigo_exame' => $campos['codigo_exame'], 'codigo_proposta_credenciamento' => $this->getInsertID())))) {
			        		// insere relacao medico / proposta
				            if (!$model_PropostaCredExame->incluir(array(
			        				'codigo_proposta_credenciamento' => ($etapa != '2' ? $this->getInsertID() : $dados['PropostaSemValidacao']['codigo']),
			        				'codigo_exame' => $campos['codigo_exame']
			        			))) 
				            	$invalidadeFields += $this->trata_erros('PropostaCredEngenharia', $model_PropostaCredExame->validationErrors, $key);							
						}
					} else {
						$invalidadeFields[$key] = array('valor' => 'Deve ter ao menos um exame!');	
					}    		
	        	}				
			}			
        	
			// insere medicos
			$invalidadeFields = array();
			$invalidadeFields_Medico = array();
			
			if(isset($dados['PropostaCredProduto']['59']) && ($dados['PropostaCredProduto']['59'] == '1')) {
				
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
								
							if (!$model_Medico->atualizar($dadosMedico)) {
								$invalidadeFields_Medico = $this->trata_erros('Medico', $model_Medico->validationErrors, $key);
							} else {
								$idMedico = $dadosMedico['Medico']['codigo'];
							}
								
						} else {
								
							if (!$model_Medico->incluir($campos)) {
								$invalidadeFields_Medico = $this->trata_erros('Medico', $model_Medico->validationErrors, $key);
							} else {
								$idMedico = $model_Medico->getInsertID();
							}
								
						}
						
						if(isset($idMedico) && $idMedico) {
							
			        		// insere relacao medico / proposta
				            if (!$model_PropostaCredMedico->incluir( array(
			        				'codigo_proposta_credenciamento' => ($etapa != '2' ? $this->getInsertID() : $dados['PropostaSemValidacao']['codigo']),
			        				'codigo_medico' => $idMedico,
			        			))) 
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
        	
		    // horarios
		    $invalidadeFields = array();
		    if(isset($dados['PropostaCredProduto']['59']) && ($dados['PropostaCredProduto']['59'] == '1')) {
				foreach($dados['Horario'] as $key => $periodo) {
	        		$dias_selecionados = "";
	        		foreach($periodo['dias_semana'] as $k => $dia) {
	        			if($dia)
	        				 $dias_selecionados .= $k . ",";         					
	        		}
	        		
	        		if($dias_selecionados) {
	        			$periodo['dias_semana'] = substr($dias_selecionados,0,-1);
						$periodo['de_hora'] = Comum::soNumero($periodo['de_hora']);
						$periodo['ate_hora'] = Comum::soNumero($periodo['ate_hora']);
						$periodo['codigo_proposta_credenciamento'] = ($etapa != '2' ? $this->getInsertID() : $dados['PropostaSemValidacao']['codigo']);
	
			            if (!$model_Horario->incluir($periodo))
							$invalidadeFields += $this->trata_erros('Horario', $model_Horario->validationErrors, $key);		
	        		} else {
						$invalidadeFields[$key] = array('ate_hora' => 'Deve ter ao menos um periodo!');
	        		}
	        	}		    	
		    }

			$invalidadeFields = array();
			foreach($dados['PropostaCredProduto'] as $key => $campo) {
				if($campo == '1') {
					if($this->getInsertID()) {
						if(! $model_PropostaCredProduto->find('first', array('conditions' => array("codigo_proposta_credenciamento" => $this->getInsertID(), 'codigo_produto' => $key)))) {
							if(! $model_PropostaCredProduto->incluir( array( 'codigo_proposta_credenciamento' => (int) $this->getInsertID(), 'codigo_produto' => (int) $key ) )) {
								$invalidadeFields += $this->trata_erros('PropostaCredProduto', $model_PropostaCredProduto->validationErrors, $key);
							}							
						}						
					}
				}
			}
			
        	if(count($invalidadeFields))
        		$model_PropostaCredProduto->validationErrors = $invalidadeFields;		    

        	
			if(!(
				count($this->validationErrors) ||
				count($model_PropostaCredEndereco->validationErrors)
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
    
}
