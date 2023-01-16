<?php
class AplicacaoExame extends AppModel {

	var $name = 'AplicacaoExame';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'aplicacao_exames';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure','Containable', 'Loggable' => array('foreign_key' => 'codigo_aplicacao_exames'));

	var $validate = array(
		// 'codigo_cliente' => array(
		// 	'rule' => 'notEmpty',
		// 	'message' => 'Informe o Cliente',
		// 	'required' => true
		// 	),
		'codigo_setor' => array(
			// 'rule' => 'notEmpty',
			// 'message' => 'Informe o Setor',
			// 'required' => true
			),
		'codigo_cargo' => array(
			// 'rule' => 'notEmpty',
			// 'message' => 'Informe o Cargo',
			// 'required' => true
			),
		'codigo_exame' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Exame',
			'required' => true
			),
		'codigo_tipo_exame' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Objetivo do Exame',
			'required' => true
			),
		'codigo_cliente_alocacao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Cliente',
			'required' => true
			),
		// 'periodo_idade' => array(
		// 	'rule' => 'numeric'
		// ),
		// 'periodo_idade_2' => array(
		// 	'rule' => 'numeric'
		// ),
		// 'periodo_idade_3' => array(
		// 	'rule' => 'numeric'
		// ),
		// 'periodo_idade_4' => array(
		// 	'rule' => 'numeric'
		// ),

		// 'periodo_meses' => array(
		// 	'rule' => 'numeric'
		// ),
		// 'qtd_periodo_idade' => array(
		// 	'rule' => 'numeric'
		// ),
		// 'qtd_periodo_idade_2' => array(
		// 	'rule' => 'numeric'
		// ),
		// 'qtd_periodo_idade_3' => array(
		// 	'rule' => 'numeric'
		// ),
		// 'qtd_periodo_idade_4' => array(
		// 	'rule' => 'numeric'
		// ),

	);

	function converteFiltroEmCondition($data) {
		$conditions = array();

		if(!empty($data['codigo']))
			$conditions['AplicacaoExame.codigo'] = $data['codigo'];

		if(!empty($data['codigo_exame']))
			$conditions['AplicacaoExame.codigo_exame'] = $data['codigo_exame'];

		if(!empty($data['codigo_setor']))
			$conditions['AplicacaoExame.codigo_setor'] = $data['codigo_setor'];

		if(!empty($data['codigo_cargo']))
			$conditions['AplicacaoExame.codigo_cargo'] = $data['codigo_cargo'];

		if(isset($data ['ativo'])) {
			if($data ['ativo'] == '0')
				$conditions [] = '(AplicacaoExame.ativo = ' . $data ['ativo'] . ' OR AplicacaoExame.ativo IS NULL)';
			else if($data ['ativo'] == '1')
				$conditions ['AplicacaoExame.ativo'] = $data ['ativo'];
		}

		if(!empty($data['codigo_cliente_alocacao']))
			$conditions['AplicacaoExame.codigo_cliente_alocacao'] = $data['codigo_cliente_alocacao'];

		$conditions['Setor.ativo'] = 1;
		$conditions['Cargo.ativo'] = 1;

		if(!empty($data['codigo_funcionario']))
			$conditions['AplicacaoExame.codigo_funcionario'] = $data['codigo_funcionario'];

		return $conditions;
	}
	
	/**
	 * [converteFiltroEmCondition_ClientesSemExames description]
	 * 
	 * CONDITIONS PARA PESQUISAR PELA EMPRESA APLICANDO A REGRA SE O EXAME NÃO EXISTIR NO CLIENTE QUE ESTÁ BUSCANDO IRÁ BUSCAR NA MATRIZ DELE.
	 * 
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function converteFiltroEmCondition_ClientesSemExames($data) 
	{
		$conditions = array();
		
		//verifica se tem codigo_cleinte
		if(!empty($data['codigo_cliente'])) {
			//monta a conditions
			$conditions[] = array("AplicacaoExame.codigo_cliente = {$data['codigo_cliente']}");
		}
		
		//filtro do exame pesquisado
		if(!empty($data['codigo_exame'])) {
			$conditions['AplicacaoExame.codigo_exame'] = $data['codigo_exame'];
		} 
		
		return $conditions;

	}//fim  converteFiltroEmCondition_ClientesSemExames


	function carregar($codigo) {
		$dados = $this->find ( 'first', array (
			'conditions' => array (
				$this->name . '.codigo' => $codigo 
				) 
			) );
		return $dados;
	}

	function aplicacao_exame_importacao($dados){
		$retorno = array();
		if (isset($dados['AplicacaoExame']['codigo_cliente_alocacao']) && !empty($dados['AplicacaoExame']['codigo_cliente_alocacao'])){
			if(isset($dados['AplicacaoExame']['codigo_setor']) && !empty($dados['AplicacaoExame']['codigo_setor'])){
				if(isset($dados['AplicacaoExame']['codigo_cargo']) && !empty($dados['AplicacaoExame']['codigo_cargo'])){
					if(isset($dados['AplicacaoExame']['codigo_exame']) && !empty($dados['AplicacaoExame']['codigo_exame'])){

						$consulta_aplicacao_exame = $this->find('first', array(
							'conditions' => array(
								'codigo_cliente_alocacao'	 => $dados['AplicacaoExame']['codigo_cliente_alocacao'],
								'codigo_setor'				 => $dados['AplicacaoExame']['codigo_setor'],
								'codigo_cargo'				 => $dados['AplicacaoExame']['codigo_cargo'],
								'codigo_exame'				 => $dados['AplicacaoExame']['codigo_exame'],
								'codigo_funcionario'		 => $dados['AplicacaoExame']['codigo_funcionario']
								)
							)
						);

						if(empty($consulta_aplicacao_exame)){
							if(!parent::incluir($dados, false)){
								$retorno['Erro']['AplicacaoExame'] = array('codigo_aplicacao_exame' => utf8_decode('Não foi possível cadastrar os dados da Aplicação de Exames.'));
							}
							else{ 
								if(!empty($this->id)){
									$consulta_dados = $this->find("first", array('conditions' => array('codigo' => $this->id)));
									if(empty($consulta_dados)){
										$retorno['Erro']['AplicacaoExame'] = array('codigo_aplicacao_exame' => utf8_decode('Aplicação Exame não encontrado!'));
									}
									else{
										$retorno['Dados'] = $consulta_dados;
									}
								}
							}
						}
						else{
							$dados['AplicacaoExame']['codigo'] = $consulta_aplicacao_exame['AplicacaoExame']['codigo'];

							if(!parent::atualizar($dados, false)){
								$retorno['Erro']['AplicacaoExame'] = array('codigo_aplicacao_exame' => utf8_decode('Não foi possível atualizar os dados da Aplicação de Exames.'));
							}
							else{ 
								if(!empty($this->id)){
									$consulta_dados = $this->find("first", array('conditions' => array('codigo' => $this->id)));
									if(empty($consulta_dados)){
										$retorno['Erro']['AplicacaoExame'] = array('codigo_aplicacao_exame' => utf8_decode('Aplicação Exame não encontrado!'));
									}
									else{
										$retorno['Dados'] = $consulta_dados;
									}
								}
							}

							// $retorno['Erro']['AplicacaoExame'] = array('codigo_aplicacao_exame' => utf8_decode('Exame já cadastrado para o PCMSO!'));	
						}
					}
					else{
						$retorno['Erro']['AplicacaoExame'] = array('codigo_exame_aplicacao_exame_' => utf8_decode('Exame não encontrado, Aplicação Exame!'));
					}
				}
				else{
					$retorno['Erro']['AplicacaoExame'] = array('codigo_cargo_aplicacao_exame_' => utf8_decode('Cargo não encontrado, Aplicação Exame!'));
				}
			}
			else{
				$retorno['Erro']['AplicacaoExame'] = array('codigo_setor_aplicacao_exame_' => utf8_decode('Setor não encontrado, Aplicação Exame!'));
			}
		}
		else{
			$retorno['Erro']['AplicacaoExame'] = array('codigo_cliente_aplicacao_exame_' => utf8_decode('Cliente não encontrado, Aplicação Exame!'));
		}

		return $retorno;
	}

	//inclui aplicacao de exames
	private function inclui_ae(array $data){


		foreach($data['AplicacaoExame'] as $key => $item) {

			//se for aplicar via ghe
			if(!empty($data['AplicacaoExame']['codigo_grupo_homogeneo_exame']) && is_numeric($data['AplicacaoExame']['codigo_grupo_homogeneo_exame'])){
				$sql = "SELECT 
							c.codigo as codigo_cliente, c.nome_fantasia as cliente,
							ghe.codigo as codigo_ghe, ghe.descricao as ghe,
							s.codigo as codigo_setor, s.descricao as setor,
							ca.codigo as codigo_cargo, ca.descricao as cargo
						FROM grupos_homogeneos_exames ghe
						INNER JOIN grupos_homogeneos_exames_detalhes ghed
						ON ghe.codigo = ghed.codigo_grupo_homogeneo_exame
						INNER JOIN cliente c
						ON c.codigo = ghe.codigo_cliente
						INNER JOIN setores s
						ON s.codigo = ghed.codigo_setor
						INNER JOIN cargos ca
						ON ca.codigo = ghed.codigo_cargo
						WHERE ghe.codigo = {$data['AplicacaoExame']['codigo_grupo_homogeneo_exame']}";
				$setores_cargos = $this->query($sql);
			}else{//se for aplicar via setor/cargo
				$setores_cargos = array(
					array(
						array(
							'codigo_cliente' => $data['AplicacaoExame']['codigo_cliente_alocacao'],
							'codigo_setor' => $data['AplicacaoExame']['codigo_setor'],
							'codigo_cargo' => $data['AplicacaoExame']['codigo_cargo'],
						)
					)
				);
			}

			foreach($setores_cargos as $setor_cargo){
				if(is_numeric($key)) {
					//ADICIONAR EXAME CLINICO
					$exame_clinico = array(
						'codigo_unidade' => $setor_cargo[0]['codigo_cliente'], 
						'codigo_setor' => $setor_cargo[0]['codigo_setor'], 
						'codigo_cargo' => $setor_cargo[0]['codigo_cargo'], 
						'codigo_exame' => $item['codigo_exame'],
						'codigo_funcionario' => $data['AplicacaoExame']['codigo_funcionario'],
					);

					if( isset($item['codigo_exame']) && !empty($item['codigo_exame']) ){

						$conditions = array(
							'codigo_cliente_alocacao'	=> $setor_cargo[0]['codigo_cliente'],
							'codigo_setor' 				=> $setor_cargo[0]['codigo_setor'],
							'codigo_cargo'				=> $setor_cargo[0]['codigo_cargo'],
							'codigo_exame'				=> $item['codigo_exame'],
							'codigo_funcionario'		=> $data['AplicacaoExame']['codigo_funcionario']
						);

						$consulta_exame = $this->find('first', compact('conditions'));

						if(!empty($consulta_exame)) {
							$this->validationErrors[$key]['aplicacao_exames'] = "Existe mais de um exame com o mesmo código na configuração Unidade/Setor/Cargo/Exame/Funcionario !";
							throw new Exception("Ocorreu ao inserir AplicacaoExame");
						}
						
						if(!empty($data['AplicacaoExame'][$key]['periodo_meses']) && !empty($data['AplicacaoExame'][$key]['periodo_apos_demissao'])){
							if($data['AplicacaoExame'][$key]['periodo_meses'] < $data['AplicacaoExame'][$key]['periodo_apos_demissao']) {
								$this->validationErrors[$key]['periodo_apos_demissao'] = '"Frequência (em Meses)" deve ser maior que "Após admissão"';
							}
						}

						if(!empty($data['AplicacaoExame'][$key]['periodo_meses']) && !empty($data['AplicacaoExame'][$key]['qtd_periodo_idade'])){
							if($data['AplicacaoExame'][$key]['periodo_meses'] == $data['AplicacaoExame'][$key]['qtd_periodo_idade']) {
								$this->validationErrors[$key]['qtd_periodo_idade'] = '"Periodo Idade" não pode ser igual "Qtd Periodo Idade"';
							} 
						}
						$dados_aplicacao_exame = array(
							'codigo_cliente_alocacao' => $setor_cargo[0]['codigo_cliente'],
							'codigo_setor' => $setor_cargo[0]['codigo_setor'],
							'codigo_cargo' => $setor_cargo[0]['codigo_cargo'],
							'codigo_exame' => $item['codigo_exame'],
							'codigo_funcionario' => (trim($data['AplicacaoExame']['codigo_funcionario']) == true ? $data['AplicacaoExame']['codigo_funcionario'] : null),
							'periodo_meses' => (trim($item['periodo_meses']) == true ? $item['periodo_meses'] : null),
							'periodo_apos_demissao' => (trim($item['periodo_apos_demissao']) == true ? $item['periodo_apos_demissao'] : null),
							'exame_admissional' => (trim($item['exame_admissional']) == true ? $item['exame_admissional'] : null),
							'exame_periodico' => (trim($item['exame_periodico']) == true ? $item['exame_periodico'] : null),
							'exame_demissional' => (trim($item['exame_demissional']) == true ? $item['exame_demissional'] : null),
							'exame_retorno' => (trim($item['exame_retorno']) == true ? $item['exame_retorno'] : null),
							'exame_mudanca' => (trim($item['exame_mudanca']) == true ? $item['exame_mudanca'] : null),
							'exame_monitoracao' => (trim($item['exame_monitoracao']) == true ? $item['exame_monitoracao'] : null),
							'periodo_idade' => (trim($item['periodo_idade']) == true? $item['periodo_idade'] : null),
							'qtd_periodo_idade' => (trim($item['qtd_periodo_idade']) == true ? $item['qtd_periodo_idade'] : null),
							'periodo_idade_2' => isset($item['periodo_idade_2']) ? $item['periodo_idade_2'] : NULL,
							'qtd_periodo_idade_2' => isset($item['qtd_periodo_idade_2']) ? $item['qtd_periodo_idade_2'] : NULL,
							'periodo_idade_3' => isset($item['periodo_idade_3']) ? $item['periodo_idade_3'] : NULL,
							'qtd_periodo_idade_3' => isset($item['qtd_periodo_idade_3']) ? $item['qtd_periodo_idade_3'] : NULL,
							'periodo_idade_4' => isset($item['periodo_idade_4']) ? $item['periodo_idade_4'] : NULL,
							'qtd_periodo_idade_4' => isset($item['qtd_periodo_idade_4']) ? $item['qtd_periodo_idade_4'] : NULL,											
							'exame_excluido_convocacao' => (trim($item['exame_excluido_convocacao']) == true ? $item['exame_excluido_convocacao'] : null),
							'exame_excluido_ppp' => (trim($item['exame_excluido_ppp']) == true ? $item['exame_excluido_ppp'] : null),
							'exame_excluido_aso' => (trim($item['exame_excluido_aso']) == true ? $item['exame_excluido_aso'] : null),
							'exame_excluido_pcmso' => (trim($item['exame_excluido_pcmso']) == true ? $item['exame_excluido_pcmso'] : null),
							'exame_excluido_anual' => (trim($item['exame_excluido_anual']) == true ? $item['exame_excluido_anual'] : null),
							'ativo' => 1,
							'codigo_tipo_exame' => (trim($item['codigo_tipo_exame']) == true ? $item['codigo_tipo_exame'] : null),
							'codigo_grupo_homogeneo_exame' => (!empty($item['codigo_grupo_homogeneo_exame']) && is_numeric($item['codigo_grupo_homogeneo_exame']) ? $item['codigo_grupo_homogeneo_exame'] : null),
						);

						if(parent::incluir($dados_aplicacao_exame)) {
							
							//verifica se existe alerta para esta hierarquia pendente e notifica clientes
							$this->AlertaHierarquiaPendente->envia_alerta_hierarquia($dados_aplicacao_exame['codigo_cliente_alocacao'], $dados_aplicacao_exame['codigo_setor'], $dados_aplicacao_exame['codigo_cargo'], 'PPRA');

							$consulta_ordem_servico = $this->OrdemServico->find('first', array('conditions' => array('codigo_cliente' => $dados_aplicacao_exame['codigo_cliente_alocacao'])));

								//JA POSSUI PPRA. PREENCHER SOMENTE O FORMULARIO.
							if(empty($consulta_ordem_servico)){
								$matriz = $this->GrupoEconomicoCliente->retorna_dados_cliente($data['AplicacaoExame']['codigo_cliente']);
								$dados_ordem_servico = array(
									'OrdemServico'=> array(
										'codigo_grupo_economico' => $matriz['GrupoEconomicoCliente']['codigo_grupo_economico'],
										'codigo_cliente' => $dados_aplicacao_exame['codigo_cliente_alocacao'],
										'codigo_fornecedor' => 0,
										'status_ordem_servico' => 3,
									)
								);

								$codigo_servico_pcmso = $this->OrdemServico->getPCMSOByCodigoCliente($setor_cargo[0]['codigo_cliente']);

								if($this->OrdemServico->incluir($dados_ordem_servico)) {
									$dadosItem = array(
										'OrdemServicoItem'=> array(
											'codigo_ordem_servico' => $this->OrdemServico->id,
											'codigo_servico' => $codigo_servico_pcmso
										)
									);

									if(!$this->OrdemServicoItem->incluir($dadosItem)){
										$this->validationErrors['ordem_servico'] = "Não é possivel criar Ordem de Serviço";
									}                           
								}else{
									$this->validationErrors['ordem_servico'] = "Não é possivel criar Ordem de Serviço";
								}
							}else {
								//NAO POSSUI PCMSO EXISTENTE. CADASTRO EFETUADO ATRAVES DA RHHEALTH
								if(!$this->OrdemServico->atualiza_status($consulta_ordem_servico['OrdemServico']['codigo'], 2)){
									$this->validationErrors['ordem_servico'] = "Não é possivel criar Ordem de Serviço";
								}
							}
						}else{
							if(empty($erros_exames))
								$erros_exames = array();

							foreach ($this->validationErrors as $campo => $erro) {
								if($campo == 'codigo_tipo_exame' || $campo == 'codigo_exame'){
									$erros_exames[$key][$campo] = $erro;
								}else{
									$erros =  array($campo => $erro);
								}
							}

							if(isset($erros)) {
								$this->validationErrors = array_merge($erros, $erros_exames);
							} else {
								$this->validationErrors = $erros_exames;
							}
						}
					}else{
						$this->validationErrors[$key]['codigo_exame'] = "Exame já cadastrado!";
					}
				}

			}	
		}
	}

	public function incluir($data){
		
		$this->GrupoEconomicoCliente =& ClassRegistry::Init('GrupoEconomicoCliente');
		$this->OrdemServico =& ClassRegistry::Init('OrdemServico');
		$this->OrdemServicoItem =& ClassRegistry::Init('OrdemServicoItem');
		$this->Exame =& ClassRegistry::Init('Exame');
		$this->AlertaHierarquiaPendente =& ClassRegistry::Init('AlertaHierarquiaPendente');

		try {
			$this->query('begin transaction');
			
			$erros = array();
			$erros_exames = array();

			if(isset($data['AplicacaoExame']) && !empty($data['AplicacaoExame'])) {
				//verifica se caso o funcionario existir, o GHE foi selecionado
				if( !empty($data['AplicacaoExame']['codigo_funcionario']) && !empty($data['AplicacaoExame']['codigo_grupo_homogeneo_exame'])){
					$this->validationErrors['codigo_funcionario'] = "A.E. para funcionário só individual sem GHE!";
					throw new Exception($this->validationErrors['codigo_funcionario']);
				}elseif( (!empty($data['AplicacaoExame']['codigo_setor']) || !empty($data['AplicacaoExame']['codigo_cargo'])) && !empty($data['AplicacaoExame']['codigo_grupo_homogeneo_exame']) ){
					//verifica se setor/cargo ou o GHE está selecionado: ou um ou outro!
					$this->validationErrors['codigo_setor'] = "Selecione o Setor/Cargo ou GHE!";
					throw new Exception($this->validationErrors['codigo_setor']);
				}else {
					self::inclui_ae($data);
				}   
			}
		
			if(empty($this->validationErrors)){
				if(isset($this->OrdemServico->validationErrors) && !empty($this->OrdemServico->validationErrors)){
					throw new Exception("Ocorreu ao inserir OrdemServico");
				}
				else{
					if(isset($this->OrdemServicoItem->validationErrors) && !empty($this->OrdemServicoItem->validationErrors)){
						throw new Exception("Ocorreu ao inserir OrdemServicoItem");					
					}
				}
				$this->commit();

				return true;
			}
			else{
				throw new Exception("Ocorreu ao inserir AplicacaoExame");
			}

		} 
		catch (Exception $ex) {
			$this->rollback();
			return false;
		}

	}

	private function edita_ae(array $data){
		$UPDATE_IDS = array();
		foreach($data['AplicacaoExame'] as $key => $item) {
			//se for um ghe
			if(!empty($data['AplicacaoExame']['codigo_grupo_homogeneo_exame']) && is_numeric($data['AplicacaoExame']['codigo_grupo_homogeneo_exame'])){
				if(!empty($item['codigo']) && is_numeric($item['codigo'])){
					//atualiza todos do ghe
					$update_conditions = array(
						'codigo_cliente_alocacao' => $data['AplicacaoExame']['codigo_cliente_alocacao'],
						'codigo_grupo_homogeneo_exame' => $data['AplicacaoExame']['codigo_grupo_homogeneo_exame'],
						'codigo_exame' => $item['codigo_exame'],
					);
					if(is_numeric($key))
						$UPDATE_IDS[] = $key;
				}else{//inserir o novo exame
					continue;
					//self::incluir($data);
				}
			}else{//se não for ghe
				if(!empty($item['codigo']) && is_numeric($item['codigo'])){
					//atualiza o registro especifico
					$update_conditions = array(
						'codigo' => $item['codigo'],
					);
					$UPDATE_IDS[] = $key;
				}else{//inserir novo exame
					continue;
					//self::incluir($data);
				}
			}

			if(is_numeric($key)){

				$item['periodo_meses'] = trim($item['periodo_meses']);
				$item['periodo_apos_demissao'] = trim($item['periodo_apos_demissao']);
				$item['qtd_periodo_idade'] = trim($item['qtd_periodo_idade']);

				if(!empty($item['periodo_meses']) && !empty($item['periodo_apos_demissao'])){
					if($item['periodo_meses'] < $item['periodo_apos_demissao']) {
						$this->validationErrors[$key]['periodo_apos_demissao'] = '"Frequência (em Meses)" deve ser maior que "Após admissão"';
						throw new Exception($this->validationErrors[$key]['periodo_apos_demissao']);
					}
				}
				if(!empty($item['periodo_meses']) && !empty($item['qtd_periodo_idade'])){
					if($item['periodo_meses'] == $item['qtd_periodo_idade']) {
						$this->validationErrors[$key]['qtd_periodo_idade'] = '"Periodo Idade" não pode ser igual "Qtd Periodo Idade"';
						throw new Exception($this->validationErrors[$key]['qtd_periodo_idade']);
					}  
				}

				$dados_aplicacao_exame = array(
					'AplicacaoExame'=> array(
						'periodo_meses' => (trim($item['periodo_meses']) == true ? $item['periodo_meses'] : null),
						'periodo_apos_demissao' => (trim($item['periodo_apos_demissao']) == true ? $item['periodo_apos_demissao'] : null),
						'exame_admissional' => (trim($item['exame_admissional']) == true ? $item['exame_admissional'] : null),
						'exame_periodico' => (trim($item['exame_periodico']) == true ? $item['exame_periodico'] : null),
						'exame_demissional' => (trim($item['exame_demissional']) == true ? $item['exame_demissional'] : null),
						'exame_retorno' => (trim($item['exame_retorno']) == true ? $item['exame_retorno'] : null),
						'exame_mudanca' => (trim($item['exame_mudanca']) == true ? $item['exame_mudanca'] : null),
						'exame_monitoracao' => (trim($item['exame_monitoracao']) == true ? $item['exame_monitoracao'] : null),
						'periodo_idade' => (trim($item['periodo_idade']) == true ? $item['periodo_idade'] : null),
						'qtd_periodo_idade' => (trim($item['qtd_periodo_idade']) == true ? $item['qtd_periodo_idade'] : null),
						'periodo_idade_2' => (trim($item['periodo_idade_2']) == true ? $item['periodo_idade_2'] : null),
						'qtd_periodo_idade_2' => (trim($item['qtd_periodo_idade_2']) == true ? $item['qtd_periodo_idade_2'] : null),
						'periodo_idade_3' => (trim($item['periodo_idade_3']) == true ? $item['periodo_idade_3'] : null),
						'qtd_periodo_idade_3' => (trim($item['qtd_periodo_idade_3']) == true ? $item['qtd_periodo_idade_3'] : null),
						'periodo_idade_4' => (trim($item['periodo_idade_4']) == true ? $item['periodo_idade_4'] : null),
						'qtd_periodo_idade_4' => (trim($item['qtd_periodo_idade_4']) == true ? $item['qtd_periodo_idade_4'] : null),
						'exame_excluido_convocacao' => (trim($item['exame_excluido_convocacao']) == true ? $item['exame_excluido_convocacao'] : null),
						'exame_excluido_ppp' => (trim($item['exame_excluido_ppp']) == true ? $item['exame_excluido_ppp'] : null),
						'exame_excluido_aso' => (trim($item['exame_excluido_aso']) == true ? $item['exame_excluido_aso'] : null),
						'exame_excluido_pcmso' => (trim($item['exame_excluido_pcmso']) == true ? $item['exame_excluido_pcmso'] : null),
						'exame_excluido_anual' => (trim($item['exame_excluido_anual']) == true ? $item['exame_excluido_anual'] : null),
						'ativo' => 1,
						'codigo_tipo_exame' => (trim($item['codigo_tipo_exame']) == true ? $item['codigo_tipo_exame'] : null),
					)
				);

				if(!$this->updateAll($dados_aplicacao_exame['AplicacaoExame'], $update_conditions)) {
					$erros_exames = $erros = array();
					foreach ($this->validationErrors as $campo => $erro) {
						if($campo == 'codigo_tipo_exame' || $campo == 'codigo_exame'){
							$erros_exames[$key][$campo] = $erro;
						}else{
							$erros =  array($campo => $erro);
						}
					}
					$this->validationErrors = array_merge($erros, $erros_exames);
				}
			}
		}
		//inserir exames que não estão inseridos
		//verificando se existe exames para serem inseridos
		if(count($UPDATE_IDS) >= 1){
			foreach($UPDATE_IDS as $ids){
				if(is_numeric($ids)){
					unset($data['AplicacaoExame'][$ids]);//removendo exames ja atualizados.
				}
			}
			self::incluir($data);//enviando exames novos para inserção
		}
	}

	function editar($data){
		$this->GrupoEconomicoCliente =& ClassRegistry::Init('GrupoEconomicoCliente');
		$this->OrdemServico =& ClassRegistry::Init('OrdemServico');
		$this->OrdemServicoItem =& ClassRegistry::Init('OrdemServicoItem');
		$this->Exame =& ClassRegistry::Init('Exame');
		try {
			$this->query('begin transaction');

			$erros = array();
			$erros_campos = array();
			$erros_exames = array();

			self::edita_ae($data);

			if(isset($this->validationErrors) && empty($this->validationErrors)){
				$this->commit();
				return true;
			}elseif(isset($this->validationErrors) && !empty($this->validationErrors)){
				throw new Exception("Ocorreu ao inserir AplicacaoExame");
				return false;
			}
		} 
		catch (Exception $ex) {
			$this->rollback();
			return false;
		}
	}

	function incluiExameClinico($data){
			
			$this->Configuracao  =& ClassRegistry::Init('Configuracao');

			$codigo_cliente_alocacao  = $data['codigo_unidade'];
			$codigo_setor  = $data['codigo_setor'];
			$codigo_cargo = $data['codigo_cargo'];
			$codigo_exame = (empty($data['codigo_exame']))? '' :$data['codigo_exame'];
			$codigo_funcionario = $data['codigo_funcionario'];

			$retorno = '';

		if(!empty($codigo_cliente_alocacao) && !empty($codigo_setor) && !empty($codigo_cargo)){ //ESTRUTURA CRIADA
			
			//	VERFICA NA TABELA DE CONFIGURACAO QUAL O CODIGO DO EXAME CLINICO
			$consulta_configuracao_exame = $this->Configuracao->find("first", array('conditions' => array('chave' => 'INSERE_EXAME_CLINICO')));
			
			if(!empty($consulta_configuracao_exame)) {
				
				//ACHOU OS DADOS DE CONFIGURACAO, PROCURA O EXAME SE EXISTE NA BASE
				$consulta_exame = $this->Exame->find("first", array('conditions' => array('codigo' => $consulta_configuracao_exame['Configuracao']['valor'])));

				if(!empty($consulta_exame)) {
					
					if($consulta_exame['Exame']['codigo'] != $codigo_exame) {

						$consulta_aplicacao_exame = $this->find('first', 
							array(
								'conditions' => 
								array(
									'codigo_cliente_alocacao'	 => $codigo_cliente_alocacao,
									'codigo_setor'				 => $codigo_setor,
									'codigo_cargo'				 => $codigo_cargo,
									'codigo_exame'				 => $consulta_exame['Exame']['codigo'],
									'codigo_funcionario'		 => $codigo_funcionario
									)
								)
							);


						if(empty($consulta_aplicacao_exame)){

							$dados_aplicacao_exame = array(
								'AplicacaoExame' => array(
									'codigo_cliente_alocacao' => $codigo_cliente_alocacao,
									'codigo_setor' => $codigo_setor,
									'codigo_cargo' => $codigo_cargo,
									'codigo_exame' => $consulta_exame['Exame']['codigo'],
									'codigo_funcionario' => $codigo_funcionario,
									'ativo' => 1, 
									'codigo_tipo_exame' => 1
									)
								);

							$retorno_aplicacao_exame = $this->aplicacao_exame_importacao($dados_aplicacao_exame);
							
							if(isset($retorno_aplicacao_exame['erro']) && !empty($retorno_aplicacao_exame['erro'])){
								$retorno = $retorno_aplicacao_exame['Erro'];
							}
						}
					}
				}
				else{
					$retorno = utf8_decode('Exame não encontrado!');
				}
			}
			else{
				$retorno = utf8_decode('Exame Clínico não encontrado!');
			}	
		}
		else{
			$retorno = utf8_decode('Unidade/Setor/Cargo não enviado corretamente!');
		}

		return $retorno;	
	}

	public function excluir($codigo_ae){
		$ae = $this->find('first', array('conditions' => array('codigo' => $codigo_ae)));

		if(count($ae) == 0){
			return '0';
		}else{
			if(is_null($ae['AplicacaoExame']['codigo_grupo_homogeneo_exame'])){//se não for um GHE
				if($this->delete($codigo_ae)){
					return '1';//deletou
				}else{
					return '0';//não deletou
				}
			}else{
				$where = array(
					'codigo_grupo_homogeneo_exame' => $ae['AplicacaoExame']['codigo_grupo_homogeneo_exame'],
					'codigo_cliente_alocacao' => $ae['AplicacaoExame']['codigo_cliente_alocacao'],
					'codigo_exame' => $ae['AplicacaoExame']['codigo_exame'],
				);

				if($this->deleteAll($where)){//deleta todos os ghes dentro da condicao
					return '1';//deletou
				}else{
					return '0';//não deletou
				}
			}
		}
	}

	public function paginateCount($conditions = array(), $recursive = -1, $extra = array())
	{
		$return = $this->find('all', array(
			'conditions' => $conditions,
			'joins' => !empty($extra['joins'])? $extra['joins'] : array(),
			'group' => !empty($extra['group'])? $extra['group'] : array(),
			'fields' => !empty($extra['group'])? $extra['group'] : array() 		
			) 
		);
		return count($return);
	}

	public function preenche_com_exame_clinico($codigo_cliente_alocacao = null, $usuario = null, $hierarquia = false)
	{
		if(is_null($codigo_cliente_alocacao) || is_null($usuario)) return false;
		set_time_limit(0);

		//pega o codigo do exame clinico na configuracao
		$this->bindModel(array('belongsTo' => array('Configuracao' => array('foreignKey' => false))));
		$config = $this->Configuracao->find('first', array('conditions' => array('chave' => 'INSERE_EXAME_CLINICO')));
		//verifica se existe conteudo
		if(empty($config)) {
			return false;
		}
		
		$query = "DECLARE
		@codigo_setor INT, @codigo_cargo INT, @codigo_empresa INT, @codigo_cliente_alocacao INT, @ativo INT, @codigo_tipo_exame INT, @codigo_exame INT, @codigo_usuario_inclusao INT,
        @periodo_idade VARCHAR(5), @qtd_periodo_idade VARCHAR(5),
        @periodo_idade_2 VARCHAR(5), @qtd_periodo_idade_2 VARCHAR(5),
        @periodo_idade_3 VARCHAR(5), @qtd_periodo_idade_3 VARCHAR(5),
        @periodo_idade_4 VARCHAR(5), @qtd_periodo_idade_4 VARCHAR(5),
        @exame_admissional INT, @exame_periodico INT, @exame_demissional INT, @exame_retorno INT, @exame_mudanca INT, @exame_monitoracao INT,
        @periodo_meses VARCHAR(5), @periodo_apos_demissao VARCHAR(5),
        @exame_excluido_convocacao INT, @exame_excluido_ppp INT, @exame_excluido_aso INT, @exame_excluido_pcmso INT, @exame_excluido_anual INT;

        SELECT
        @codigo_exame = codigo,
        @periodo_idade = periodo_idade, @qtd_periodo_idade = qtd_periodo_idade, 
        @periodo_idade_2 = periodo_idade_2, @qtd_periodo_idade_2 = qtd_periodo_idade_2,
        @periodo_idade_3 = periodo_idade_3, @qtd_periodo_idade_3 = qtd_periodo_idade_3, 
        @periodo_idade_4 = periodo_idade_4, @qtd_periodo_idade_4 = qtd_periodo_idade_4,
        @exame_admissional = exame_admissional,
        @exame_periodico = exame_periodico,
        @exame_demissional = exame_demissional,
        @exame_retorno = exame_retorno,
        @exame_mudanca = exame_mudanca,
        @exame_monitoracao = exame_monitoracao,
        @exame_excluido_convocacao = exame_excluido_convocacao,
        @exame_excluido_ppp =exame_excluido_ppp,
        @exame_excluido_aso = exame_excluido_aso,
        @exame_excluido_pcmso = exame_excluido_pcmso,
        @exame_excluido_anual = exame_excluido_anual,
        @periodo_meses = periodo_meses,
        @periodo_apos_demissao = periodo_apos_demissao
        -- FROM exames WHERE descricao LIKE '%EXAME CLINICO%'
        FROM exames WHERE codigo = ".$config['Configuracao']['valor']."

		--INSERE NO CURSOR OS SETORES E CARGOS QUE TERAO EXAME CLINICO APLICADO
		";

		if( $hierarquia ){

			$query .=  "
				DECLARE  
				cur_dados CURSOR FOR
				SELECT 
				csc.codigo_setor,
				csc.codigo_cargo,
				csc.codigo_empresa,
				csc.codigo_cliente_alocacao,
				csc.codigo_usuario_inclusao,
				1 AS ativo,
				1 AS codigo_tipo_exame
				FROM clientes_setores_cargos csc
				WHERE (csc.codigo_cliente_alocacao = ".$codigo_cliente_alocacao.") 
				AND (SELECT COUNT(*) FROM aplicacao_exames WHERE codigo_cliente_alocacao = csc.codigo_cliente_alocacao AND codigo_setor = csc.codigo_setor AND codigo_cargo = csc.codigo_cargo AND codigo_exame = ".$config['Configuracao']['valor']." ) = 0";

		        if(!empty($usuario['Usuario']['codigo_empresa'])) {
		            $query .= " AND csc.codigo_empresa = ".$usuario['Usuario']['codigo_empresa'];
		        }

				$query .= " GROUP BY csc.codigo_setor, csc.codigo_cargo, csc.codigo_empresa, csc.codigo_usuario_inclusao, csc.codigo_cliente_alocacao;
				";

		} else {

			$query .=  "
				DECLARE  
				cur_dados CURSOR FOR
				SELECT 
				fsc.codigo_setor,
				fsc.codigo_cargo,
				fsc.codigo_empresa,
				fsc.codigo_cliente_alocacao,
				fsc.codigo_usuario_inclusao,
				--(SELECT TOP 1 codigo_usuario_inclusao FROM funcionario_setores_cargos where codigo_cliente = cf.codigo_cliente AND codigo_setor = fsc.codigo_setor AND codigo_cargo = fsc.codigo_cargo) codigo_usuario_inclusao,
				1 AS ativo,
				1 AS codigo_tipo_exame
				FROM funcionario_setores_cargos fsc
				INNER JOIN cliente_funcionario cf
				ON(cf.codigo = fsc.codigo_cliente_funcionario) 
				WHERE (fsc.codigo_cliente_alocacao = ".$codigo_cliente_alocacao.") 
				AND (SELECT COUNT(*) FROM aplicacao_exames WHERE codigo_cliente_alocacao = fsc.codigo_cliente_alocacao AND codigo_setor = fsc.codigo_setor AND codigo_cargo = fsc.codigo_cargo AND codigo_exame = ".$config['Configuracao']['valor']." ) = 0";

		        if(!empty($usuario['Usuario']['codigo_empresa'])) {
		            $query .= " AND cf.codigo_empresa = ".$usuario['Usuario']['codigo_empresa'];
		        }

				$query .= " GROUP BY fsc.codigo_setor, fsc.codigo_cargo, fsc.codigo_empresa, fsc.codigo_usuario_inclusao, fsc.codigo_cliente_alocacao;
				";

		}


		$query .= "
		OPEN cur_dados
		FETCH NEXT FROM cur_dados 
		INTO @codigo_setor, @codigo_cargo, @codigo_empresa, @codigo_cliente_alocacao, @codigo_usuario_inclusao, @ativo, @codigo_tipo_exame;
		
		-- FAZ O LAÇO E INSERE A APLICAÇÃO DE RISCO PARA CADA SETOR / CARGO
		WHILE @@FETCH_STATUS = 0   
		BEGIN  

		INSERT INTO aplicacao_exames (codigo_cliente_alocacao, codigo_setor, codigo_cargo, codigo_empresa, codigo_exame, ativo, codigo_tipo_exame, codigo_usuario_inclusao, periodo_idade, qtd_periodo_idade, periodo_idade_2, qtd_periodo_idade_2, periodo_idade_3, qtd_periodo_idade_3, periodo_idade_4, qtd_periodo_idade_4, exame_admissional, exame_periodico, exame_demissional, exame_retorno, exame_mudanca, exame_monitoracao, exame_excluido_convocacao, exame_excluido_ppp, exame_excluido_aso, exame_excluido_pcmso, exame_excluido_anual, periodo_meses, periodo_apos_demissao)
		VALUES (@codigo_cliente_alocacao, @codigo_setor, @codigo_cargo, @codigo_empresa, @codigo_exame, @ativo, @codigo_tipo_exame, @codigo_usuario_inclusao, @periodo_idade, @qtd_periodo_idade, @periodo_idade_2, @qtd_periodo_idade_2, @periodo_idade_3, @qtd_periodo_idade_3, @periodo_idade_4, @qtd_periodo_idade_4, @exame_admissional, @exame_periodico, @exame_demissional, @exame_retorno, @exame_mudanca, @exame_monitoracao, @exame_excluido_convocacao, @exame_excluido_ppp, @exame_excluido_aso, @exame_excluido_pcmso, @exame_excluido_anual, @periodo_meses, @periodo_apos_demissao);


		FETCH NEXT FROM cur_dados 
		INTO @codigo_setor, @codigo_cargo, @codigo_empresa, @codigo_cliente_alocacao, @codigo_usuario_inclusao, @ativo, @codigo_tipo_exame;
		END

		CLOSE cur_dados;
		DEALLOCATE cur_dados;";
		
		return $this->query($query);
	}

	public function concluir($codigo){
		$this->OrdemServico =& ClassRegistry::Init('OrdemServico');

		$codigo_servico_pcmso = $this->OrdemServico->getPCMSOByCodigoCliente($codigo);

		$retorno = array();
		$conditions = array(
			'codigo_cliente' => $codigo, 
			'OrdemServicoItem.codigo_servico = ' . $codigo_servico_pcmso
		);
		$joins = array(
			array(
				'table' => 'ordem_servico_item',
				'alias' => 'OrdemServicoItem',
				'type' => 'INNER',
				'conditions' => array('OrdemServico.codigo = OrdemServicoItem.codigo_ordem_servico')
			)
		);
		$dadosOrdemServico = $this->OrdemServico->find('first', array('conditions' => $conditions, 'fields' => array('codigo'),	'joins' => $joins));
		if($this->OrdemServico->atualiza_status($dadosOrdemServico['OrdemServico']['codigo'], 5, $codigo_servico_pcmso)){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * [getExamesSemAssinatura description]
	 * 
	 * Metodo para buscar os exames que não tem assinatura mesmo na matriz
	 * 
	 * @return [type] [description]
	 */
	public function getExamesSemAssinatura($codigo_cliente=null, $codigo_exame=null)
	{
		//instancia as models que irá usar neste metodo
    	$this->GrupoEconomico	= ClassRegistry::init('GrupoEconomico');    	

    	$whereServico = "";
    	//realiza a busca dos exames aplicados
    	if(!empty($codigo_cliente)) {
    		$whereServico .= " AND ae.codigo_cliente = " . $codigo_cliente;
    	}

    	//verifica se aplica o codigo do exame como filtro
    	if(!empty($codigo_exame)) {    		
    		$whereServico .= " AND ae.codigo_exame = " . $codigo_exame;
    	}//fim if codigo exame
    	
    	//busca os servicos que foram aplicados sem repetir
    	$servicos_aplicados = $this->query("SELECT ae.codigo_cliente,	e.codigo_servico
											FROM RHHealth.dbo.aplicacao_exames ae
												INNER JOIN RHHealth.dbo.exames e on ae.codigo_exame = e.codigo
											WHERE 1=1 " . $whereServico . "
											GROUP BY ae.codigo_cliente,e.codigo_servico");
    	//variavel auxiliar para apresentar
	    $exames_sem_assinatura = array();

    	//verifica se existe servicos/exames    	
    	if(!empty($servicos_aplicados)) {

	    	//busca assinatura da unidade
	    	//query para pegar o servico
    		$query_assinatura = "SELECT TOP 1 *
    							FROM RHHealth.dbo.cliente_produto_servico2 cps 
    								INNER JOIN RHHealth.dbo.cliente_produto cp ON cps.codigo_cliente_produto = cp.codigo
    							WHERE 1=1";

	    	//varre os exames aplicados olhando para a assinatura da unidade 
	    	foreach($servicos_aplicados as $servicos) {

	    		//seta o codigo do servico
	    		$codigo_servico = $servicos[0]['codigo_servico'];
	    		$codigo_cliente = $servicos[0]['codigo_cliente'];

	    		############### SEM ASSINATURA ##############
	    		##################################################################################################
	    		//busca a assinatura do cliente
	    		$assinatura_unidade = $this->query($query_assinatura." AND cp.codigo_cliente = " . $codigo_cliente . " AND cps.codigo_servico = " . $codigo_servico);

	    		//caso não encontre na assinatura do cliente busca os exames na matriz
	    		if(empty($assinatura_unidade)) {

			    	//busca o codigo da matriz			    	
			    	$gr = $this->query("SELECT TOP 1 ge.codigo_cliente
			    						FROM RHHealth.dbo.grupos_economicos ge 
			    							INNER JOIN RHHealth.dbo.grupos_economicos_clientes gec on ge.codigo = gec.codigo_grupo_economico
			    						WHERE gec.codigo_cliente = " . $codigo_cliente);

			    	// pr($gr);exit;
			    	
			    	//seta o codigo da matriz
			    	$codigo_cliente_matriz = $gr[0][0]['codigo_cliente'];
	    		
	    			//caso não encotre na matriz armazena para ir no corpo do email	    			 
	    			$assinatura_matriz = $this->query($query_assinatura." AND cp.codigo_cliente = " . $codigo_cliente_matriz . " AND cps.codigo_servico = " . $codigo_servico);

	    			//verifica se o servico existe, caso nao exista irá ser disparado o email
	    			if(empty($assinatura_matriz)) {
	    				//seta o nome dos exames	    				
	    				$exames_sem_assinatura[$codigo_cliente][] = $codigo_servico;
	    			} //fim assinatura matriz

	    		}//fim if assinatura_unidade
	    		##################################################################################################

	    	}//fim foreach servicos aplicados

    		
	   	}//fim if empty exames_aplicados

	   	return $exames_sem_assinatura;

	}//fim  getExamesSemAssinatura

	public function dados_modal_ppra_pendente($codigo_unidade,$codigo_setor,$codigo_cargo,$codigo_funcionario = null){
		$this->Cliente = ClassRegistry::Init('Cliente');
		$this->Setor   = ClassRegistry::Init('Setor');
		$this->Cargo   = ClassRegistry::Init('Cargo');
		$this->ClienteSetor   = ClassRegistry::Init('ClienteSetor');
		$this->Funcionario   = ClassRegistry::Init('Funcionario');

		$dados_cliente = $this->Cliente->findbyCodigo($codigo_unidade);
		$dados_setor = $this->Setor->findbyCodigo($codigo_setor);
		$dados_cargo = $this->Cargo->findbyCodigo($codigo_cargo);

		$dados_funcionario = array();

		$conditions = array(
			'ClienteSetor.codigo_cliente_alocacao' => $codigo_unidade,
			'ClienteSetor.codigo_setor' => $codigo_setor,
			'GrupoExposicao.codigo_cargo' => $codigo_cargo
		);

		if( empty($codigo_funcionario) ){
            $conditions[] = 'GrupoExposicao.codigo_funcionario IS NULL';
        } else {
            $conditions[] = ' (GrupoExposicao.codigo_funcionario IS NULL OR GrupoExposicao.codigo_funcionario = '.$codigo_funcionario.') ';
        }

        $fields_riscos = array(
        	'GruposRisco.descricao AS grupo',
        	'Risco.nome_agente AS nome_agente'
       	);

		$joins = array(
			array(
				'table' => 'RHHealth.dbo.grupo_exposicao',
				'alias' => 'GrupoExposicao',
				'type' => 'LEFT',
				'conditions' => 'ClienteSetor.codigo = GrupoExposicao.codigo_cliente_setor'
			),
			array(
				'table' => 'RHHealth.dbo.grupos_exposicao_risco',
				'alias' => 'GrupoExposicaoRisco',
				'type' => 'LEFT',
				'conditions' => 'GrupoExposicao.codigo = GrupoExposicaoRisco.codigo_grupo_exposicao'
			),
			array(
				'table' => 'RHHealth.dbo.riscos',
				'alias' => 'Risco',
				'type' => 'LEFT',
				'conditions' => 'GrupoExposicaoRisco.codigo_risco = Risco.codigo'
			),
			array(
				'table' => 'RHHealth.dbo.grupos_riscos',
				'alias' => 'GruposRisco',
				'type' => 'LEFT',
				'conditions' => 'Risco.codigo_grupo = GruposRisco.codigo'
			),			
		);

		$dados_riscos = $this->ClienteSetor->find('all',array('conditions' => $conditions,'joins' => $joins, 'fields' => $fields_riscos, 'recursive' => -1));

		// debug($dados_riscos);
		// exit;
		
		//pega os dados de atribuicao
		$join_atribuicao = array(
			array(
				'table' => 'RHHealth.dbo.grupo_exposicao',
				'alias' => 'GrupoExposicao',
				'type' => 'LEFT',
				'conditions' => 'ClienteSetor.codigo = GrupoExposicao.codigo_cliente_setor'
			),			
			array(
				'table' => 'RHHealth.dbo.atribuicoes_grupos_expo',
				'alias' => 'AtribuicaoGrupoExpo',
				'type' => 'LEFT',
				'conditions' => 'AtribuicaoGrupoExpo.codigo_grupo_exposicao = GrupoExposicao.codigo'
			),
			array(
				'table' => 'RHHealth.dbo.atribuicao',
				'alias' => 'Atribuicao',
				'type' => 'LEFT',
				'conditions' => 'AtribuicaoGrupoExpo.codigo_atribuicao = Atribuicao.codigo'
			),
		);

		//pega os dados de atribuicao
		$dados_atribuicao = $this->ClienteSetor->find('list',array('conditions' => $conditions,'joins' => $join_atribuicao,'fields' => array('Atribuicao.codigo','Atribuicao.descricao'),'recursive' => -1));

		// debug($dados_atribuicao);
		// exit;

		$dados = array(
			'codigo_unidade' => $dados_cliente['Cliente']['codigo'],
			'nome_fantasia' => $dados_cliente['Cliente']['nome_fantasia'],
			'codigo_setor' => $dados_setor['Setor']['codigo'],
			'setor' => $dados_setor['Setor']['descricao'],
			'codigo_cargo' => $dados_cargo['Cargo']['codigo'],
			'cargo' => $dados_cargo['Cargo']['descricao'],
			'riscos' => $dados_riscos,
			'atribuicao' => $dados_atribuicao,
		);

		if( !empty($codigo_funcionario) ){
            $dados_funcionario = $this->Funcionario->findbyCodigo($codigo_funcionario);
            $dados['funcionario'] = $dados_funcionario['Funcionario']['nome'];
        }

		return $dados;
	}

	public function monta_array_query_pcmso(){

		$fields = array(
        	"Setores.descricao AS Setor",
        	"Cargos.descricao AS Cargo",
        	"GrupoEconomicoCliente.codigo_cliente",
        	"Setores.codigo AS CodigoSetor",
        	"Cargos.codigo AS CodigoCargo",
			"GrupoEconomicoCliente.codigo_cliente as codigo_cliente",
			"ValidacaoPPRA.codigo",
			"ValidacaoPPRA.status_validacao",
			"AplicacaoExame.codigo_cliente_alocacao",
			"Funcionario.nome",
			"Funcionario2.nome",
			"Funcionario2.codigo",
			"AplicacaoExame.codigo_funcionario",
			"AplicacaoExame.codigo_cliente_alocacao",
			"AplicacaoExame.codigo_funcionario",
			"ValidacaoPPRA.codigo_funcionario",
	    );

		// popula varivel para FROM
		$joins = array(
			array(
				"table"      => "RHHealth.dbo.grupos_economicos_clientes",
				"alias"      => "GrupoEconomicoCliente",
				"conditions" => "GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo"
			),
			array(
				"table"      => "RHHealth.dbo.clientes_setores_cargos",
				"alias"      => "ClientesSetoresCargos",
				"conditions" => "GrupoEconomicoCliente.codigo_cliente = ClientesSetoresCargos.codigo_cliente_alocacao "
			),
			array(
				"table"      => "RHHealth.dbo.setores",
				"alias"      => "Setores",
				"conditions" => "ClientesSetoresCargos.codigo_setor = Setores.codigo"
			),
			array(
				"table"      => "RHHealth.dbo.cargos",
				"alias"      => "Cargos",
				"conditions" => "ClientesSetoresCargos.codigo_cargo = Cargos.codigo"
			),
			array(
				"table"      => "RHHealth.dbo.funcionario_setores_cargos",
				"alias"      => "FuncionarioSetorCargo",
				"type"       => "LEFT",
				"conditions" => "ClientesSetoresCargos.codigo_cargo = FuncionarioSetorCargo.codigo_cargo and ClientesSetoresCargos.codigo_setor = FuncionarioSetorCargo.codigo_setor and ClientesSetoresCargos.codigo_cliente_alocacao = FuncionarioSetorCargo.codigo_cliente_alocacao ",
			),
			array(
				"table"      => "RHHealth.dbo.cliente_funcionario",
				"alias"      => "ClienteFuncionario",
				"type"       => "LEFT",
				"conditions" => "ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario",
			),
			array(
				"table"      => "RHHealth.dbo.aplicacao_exames",
				"alias"      => "AplicacaoExame",
				"type"       => "LEFT",
				"conditions" => "ClientesSetoresCargos.codigo_cargo = AplicacaoExame.codigo_cargo and ClientesSetoresCargos.codigo_setor = AplicacaoExame.codigo_setor and ClientesSetoresCargos.codigo_cliente_alocacao = AplicacaoExame.codigo_cliente_alocacao ",
			),
			array(
				"table"      => "RHHealth.dbo.funcionarios",
				"alias"      => "Funcionario",
				"type"       => "LEFT",
				"conditions" => "Funcionario.codigo = AplicacaoExame.codigo_funcionario",
			),
			array(
				"table"      => "RHHealth.dbo.validacao_ppra",
				"alias"      => "ValidacaoPPRA",
				"type"       => "LEFT",
				"conditions" => "ValidacaoPPRA.codigo_cliente_alocacao = FuncionarioSetorCargo.codigo_cliente_alocacao and ValidacaoPPRA.codigo_setor = FuncionarioSetorCargo.codigo_setor and ValidacaoPPRA.codigo_cargo = FuncionarioSetorCargo.codigo_cargo AND (ValidacaoPPRA.codigo_funcionario = ClienteFuncionario.codigo_funcionario OR ValidacaoPPRA.codigo_funcionario = AplicacaoExame.codigo_funcionario OR ValidacaoPPRA.codigo_funcionario IS NULL) AND ValidacaoPPRA.status_validacao = 0",
			),
			array(
				"table"      => "RHHealth.dbo.funcionarios",
				"alias"      => "Funcionario2",
				"type"       => "LEFT",
				"conditions" => "Funcionario2.codigo = ValidacaoPPRA.codigo_funcionario",
			),
			array(
				"table"      => "RHHealth.dbo.validacao_ppra",
				"alias"      => "ValidacaoPPRA2",
				"type"       => "LEFT",
				"conditions" => "ValidacaoPPRA2.codigo_cliente_alocacao = AplicacaoExame.codigo_cliente_alocacao and ValidacaoPPRA2.codigo_setor = AplicacaoExame.codigo_setor and ValidacaoPPRA2.codigo_cargo = AplicacaoExame.codigo_cargo AND ValidacaoPPRA2.codigo_funcionario = AplicacaoExame.codigo_funcionario AND ValidacaoPPRA2.status_validacao = 0",
			)
		);

		// popula varivel para GROUP BY
		$group  = array(
			'Cargos.codigo',
			'Cargos.descricao',
			'Setores.codigo',
			'Setores.descricao',
			'GrupoEconomicoCliente.codigo_cliente',
			"ValidacaoPPRA.codigo",
			"ValidacaoPPRA.status_validacao",
			"AplicacaoExame.codigo_cliente_alocacao",
			"Funcionario2.nome",
			"Funcionario2.codigo",
			"AplicacaoExame.codigo_funcionario",
			"AplicacaoExame.codigo_cliente_alocacao",
			"AplicacaoExame.codigo_funcionario",
			"ValidacaoPPRA.codigo_funcionario",
			"Funcionario.nome",
		);

		// popula varivel para ORDER BY
		$order = array(
			"Setores.descricao", 
			"Cargos.descricao"
		); /**/

		$dados = array(
	        'fields'    => $fields,
	        'joins'     => $joins,
	        'group'     => $group,
	        'order'     => $order,
	    );

		return $dados;
	}

}

?>