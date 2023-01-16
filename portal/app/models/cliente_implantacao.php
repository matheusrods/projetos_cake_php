<?php
class ClienteImplantacao extends AppModel {

	var $name = 'ClienteImplantacao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cliente_implantacao';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $validate = array(
		'codigo_cliente' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Cliente Principal',
		)
	);

	function converteFiltrosEmConditions($filtros) {
		$this->GrupoEconomico =& ClassRegistry::Init('GrupoEconomico');
		$this->GrupoEconomicoCliente =& ClassRegistry::Init('GrupoEconomicoCliente');
		$this->Cliente =& ClassRegistry::Init('Cliente');

		$conditions = array();
		
		if (isset($filtros['codigo_cliente']) && ! empty($filtros['codigo_cliente'])) {
				$conditions[] = "ClienteImplantacao.codigo_cliente IN (".$filtros['codigo_cliente'].")";
		}
		
		if (isset($filtros['status']) && ! empty($filtros['status'])) {
			
			switch($filtros['status']) {
				
				case 1 :
					$conditions[] = '(ClienteImplantacao.estrutura = "" OR ClienteImplantacao.estrutura is null)';
					break;
					
				case 2 :
					$conditions[] = '(ClienteImplantacao.estrutura is not null)';
					$conditions[] = '(ClienteImplantacao.ppra = "" OR ClienteImplantacao.ppra is null)';
					break;
					
				case 3 :
					$conditions[] = '(ClienteImplantacao.estrutura is not null)';
					$conditions[] = '(ClienteImplantacao.ppra is not null)';					
					$conditions[] = '(ClienteImplantacao.pcmso = "" OR ClienteImplantacao.pcmso is null)';
					break;
					
				case 4 :
					$conditions[] = '(ClienteImplantacao.estrutura is not null)';
					$conditions[] = '(ClienteImplantacao.ppra is not null)';
					$conditions[] = '(ClienteImplantacao.pcmso is not null)';
					$conditions[] = '(ClienteImplantacao.liberado = "" OR ClienteImplantacao.liberado is null)';
					break;
			}
		}		
						
		return $conditions;
	}

	public function enviar_ordem_servico($dados){
		$this->Fornecedor =& ClassRegistry::Init('Fornecedor');
		$this->Cliente =& ClassRegistry::Init('Cliente');
		$this->OrdemServico =& ClassRegistry::Init('OrdemServico');
		$this->GrupoEconomicoCliente =& ClassRegistry::Init('GrupoEconomicoCliente');
		$this->OrdemServicoItem =& ClassRegistry::Init('OrdemServicoItem');
		$this->Servico =& ClassRegistry::Init('Servico');

		$dados_fornecedor = $this->Fornecedor->find('first', array(
			'conditions' => array(
				'Fornecedor.codigo' => $dados['OrdemServico']['codigo_fornecedor'],
				'FornecedorContato.codigo_tipo_retorno' => '2', // e-mail
			),
			'joins' => array(
				array(
					'table' => 'fornecedores_contato',
					'alias' => 'FornecedorContato',
					'type' => 'INNER',
					'conditions' => 'FornecedorContato.codigo_fornecedor = Fornecedor.codigo'
				)
			),
			'fields' => array('Fornecedor.codigo', 'Fornecedor.nome', 'FornecedorContato.descricao')
		));

		$joins = array(
			array(
				'table' => 'cliente_endereco',
				'alias' => 'ClienteEndereco',
				'type' => 'INNER',
				'conditions' => 'ClienteEndereco.codigo_cliente = Cliente.codigo'
			),
			array(
				'table' => 'cliente_contato',
				'alias' => 'ClienteContato',
				'type' => 'LEFT',
				'conditions' => '(ClienteContato.codigo_cliente = Cliente.codigo AND ClienteContato.codigo_tipo_retorno = "1")'
			)        						
		);

		$fields = array(
			'ClienteContato.descricao',
			'ClienteContato.ddd',
			'Cliente.codigo',
			'Cliente.razao_social',
			'Cliente.nome_fantasia',
			'ClienteEndereco.codigo',
			'ClienteEndereco.numero',
			'ClienteEndereco.logradouro',
			'ClienteEndereco.bairro',
			'ClienteEndereco.cidade',
			'ClienteEndereco.estado_descricao'
		);

		$dados_cliente = $this->Cliente->find('first', array(
			'conditions' => array('Cliente.codigo' => $dados['OrdemServico']['codigo_cliente']), 
			'joins' => $joins ,
			'fields' => $fields
		));

		if(!empty($dados_cliente) && !empty($dados_fornecedor)) {

			try {
				$this->OrdemServico->query('begin transaction');
				//codigo PPRA
				$codigo_ppra = $this->OrdemServico->getPPRAByCodigoCliente($dados['OrdemServico']['codigo_cliente']);
				//codigo PCMSO
				$codigo_pcmso = $this->OrdemServico->getPCMSOByCodigoCliente($dados['OrdemServico']['codigo_cliente']);				
				
				$matriz = $this->GrupoEconomicoCliente->retorna_dados_cliente($dados['OrdemServico']['codigo_cliente']);

				//verifica se ira atualizar o status do ppra
				if($dados['OrdemServico']['var_aux'] == 'ppra') {
					$this->atualizar_status_credenciado($matriz['Unidade']['codigo'], '1',$codigo_ppra);
				}//fim var_aux

				//verifica se ira atualizar o status do ppra
				if($dados['OrdemServico']['var_aux'] == 'pcmso') {
					$this->atualizar_status_credenciado($matriz['Unidade']['codigo'], '1',$codigo_pcmso);
				}//fim var_aux

				//verifica se existe uma ordem de serviço para este grupo, cliente,fornecedor e serviço
				$joinsOrdemServico = array(
					array(
						'table' => 'ordem_servico_item',
						'alias' => 'OrdemServicoItem',
						'type' => 'INNER',
						'conditions' => 'OrdemServicoItem.codigo_ordem_servico = OrdemServico.codigo'
					)
				);
				$conditionsOrdemServico = array(
					// 'OrdemServico.codigo_grupo_economico' => $matriz['GrupoEconomicoCliente']['codigo_grupo_economico'],
					'OrdemServico.codigo_cliente' => $dados['OrdemServico']['codigo_cliente'],
					// 'OrdemServico.codigo_fornecedor' => (!empty($this->data['OrdemServico']['codigo_fornecedor'])) ? $this->data['OrdemServico']['codigo_fornecedor'] : null,
					'OrdemServicoItem.codigo_servico' => $dados['OrdemServico']['codigo_servico'],
				);
				$ordem_servico_dados = $this->OrdemServico->find('first',array('joins' => $joinsOrdemServico, 'conditions' => $conditionsOrdemServico));

				//verifica se existe uma ordem de servico cadastrada para grupo,cliente, fornecedor, servico
				if(empty($ordem_servico_dados)) {

					$array_ordem_servico = array(
						'OrdemServico' => array(
							'codigo_grupo_economico' => $matriz['GrupoEconomicoCliente']['codigo_grupo_economico'],
							'codigo_cliente' => $dados['OrdemServico']['codigo_cliente'],
							'codigo_fornecedor' => $dados['OrdemServico']['codigo_fornecedor'],
							'status_ordem_servico' => 1
						)
					);
					//varialvel auxiliar para saber qual é codigo da ordem de servico
					$codigo_ordem_servico = "";

					// grava ordem de servico
					if($this->OrdemServico->incluir($array_ordem_servico)) {
					
						//seta o codigo da nova ordem de servico
						$codigo_ordem_servico = $this->OrdemServico->getInsertID();

						//declar os itens da nova ordem de servico
						$array_inclusao_item['OrdemServicoItem'] = array(
							'codigo_ordem_servico' => $this->OrdemServico->id,
							'codigo_servico' => $dados['OrdemServico']['codigo_servico']
						);
						//inclui um item da ordem de servico
						if(!$this->OrdemServicoItem->incluir($array_inclusao_item)) {							
							throw new Exception('Erro ao incluir ordem servico item.');
						}						
					} 
					else {
						throw new Exception('Erro ao incluir ordem servico.');
					}
				} 
				else {

					//codigo de servico para atualizacao
					$codigo_ordem_servico = $ordem_servico_dados['OrdemServico']['codigo'];

					//seta os dados para atualização da ordem de servico
					$array_ordem_servico = array(
						'OrdemServico' => array(
							'codigo' => $codigo_ordem_servico,
							'codigo_grupo_economico' => $matriz['GrupoEconomicoCliente']['codigo_grupo_economico'],
							'codigo_cliente' => $dados['OrdemServico']['codigo_cliente'],
							'codigo_fornecedor' => $dados['OrdemServico']['codigo_fornecedor'],
							'status_ordem_servico' => 1
						)
					);

					// grava ordem de servico
					if(!$this->OrdemServico->atualizar($array_ordem_servico)) {
						throw new Exception('Erro ao atualizar ordem servico.');
					}//fim if atualizar

				}//fim verificacao se existe ordem de servico para nao duplicar

				//pega os dados da cliente implantação
				$dados_cliente_implantacao = $this->find('first', array('conditions' => array('codigo_cliente' => $matriz['Matriz']['codigo'])));

				if($dados['OrdemServico']['codigo_servico'] == $codigo_ppra) {
					$dados_cliente_implantacao['ClienteImplantacao']['ppra'] = 'A';
				} else if($dados['OrdemServico']['codigo_servico'] == $codigo_pcmso) { 
					$dados_cliente_implantacao['ClienteImplantacao']['pcmso'] = 'A';
				}
				
				if(!$this->atualizar($dados_cliente_implantacao)){
					throw new Exception('Erro ao atualizar cliente implantacao');
				}

				$dadosServico = $this->Servico->find('first', array('conditions' => array('codigo' => $dados['OrdemServico']['codigo_servico'])));
				
				$this->_enviaEmailOrdemServico($codigo_ordem_servico, $dados_fornecedor, $dados_cliente, $dadosServico['Servico']['descricao']);
				
				// $this->BSession->setFlash('save_success');
				$this->OrdemServico->commit();
				return true;
				
				// if(isset($matriz['Matriz']['codigo']) && $matriz['Matriz']['codigo']) {
					
				// 	if($this->data['OrdemServico']['codigo_servico'] == OrdemServico::PPRA) {						
				// 		$this->redirect(array('controller' => 'grupos_exposicao', 'action' => 'index', $matriz['Unidade']['codigo']));
				// 	} else if($this->data['OrdemServico']['codigo_servico'] == OrdemServico::PCMSO) {						
				// 		$this->redirect(array('controller' => 'aplicacao_exames', 'action' => 'index',$matriz['Unidade']['codigo'], $matriz['Matriz']['codigo']));
				// 	} else {
				// 		$this->redirect(array('controller' => 'clientes_implantacao'));
				// 	}
					
				// } else {
				// 	$this->redirect(array('controller' => 'clientes_implantacao'));
				// }
				
			} catch(Exception $e) {
				$this->OrdemServico->rollback();
				return false;
				// $this->BSession->setFlash('save_error');
				// $this->redirect($_SERVER['HTTP_REFERER']);
			}
		} //fim !empty($dados_cliente) && !empty($dados_fornecedor)
		// else {
		// 	$this->BSession->setFlash(array('alert alert-error', 'Favor Verificar se Fornecedor está com o email devidamente cadastrado ou o Cliente está com telefone cadastrado.'));
		// 	$this->redirect(array('controller' => 'clientes_implantacao', 'action' => 'gerenciar_pcmso', $this->data['OrdemServico']['codigo_cliente']));

		// } 
	}

	/**
	 * [atualizar_status_ppra_credenciado description]
	 * 
	 * metodo para atualizar o status da ordem de servico do ppra
	 * 
	 * @param  [type] $codigo_cliente [description]
	 * @param  [type] $status         [description]
	 * @return [type]                 [description]
	 */
	public function atualizar_status_credenciado($codigo_cliente, $status, $codigo_tipo){
		$this->OrdemServico =& ClassRegistry::Init('OrdemServico');

		//verifica se existem os parametros passados
		if(!empty($codigo_cliente) && !empty($status)){
			//pega os dados da ordem de servico
			$dadosOrdemServico = $this->OrdemServico->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente, 'OrdemServicoItem.codigo_servico = ' . $codigo_tipo), 'fields' => array('codigo'), 'joins' => array(
				array(
					'table' => 'ordem_servico_item',
					'alias' => 'OrdemServicoItem',
					'type' => 'INNER',
					'conditions' => array('OrdemServico.codigo = OrdemServicoItem.codigo_ordem_servico')
				)
			)));

			if($this->OrdemServico->atualiza_status($dadosOrdemServico['OrdemServico']['codigo'], $status, $codigo_tipo, null, null)){
				return true;
			} else {
				return false;
			}			
		}

		return false;

	}//fim atualizar_status_ppra_credenciado($codigo_cliente, $status)

	public function _enviaEmailOrdemServico($id_ordem, $dados_fornecedor, $dados_cliente, $servico) {
		
		App::import('Component', array('StringView', 'Mailer.Scheduler'));
		
		$this->stringView = new StringViewComponent();
		$this->scheduler = new SchedulerComponent();
		
		$this->stringView->reset();
		$this->stringView->set('dados_fornecedor', $dados_fornecedor);
		$this->stringView->set('dados_cliente', $dados_cliente);
		$this->stringView->set('id_ordem', $id_ordem);
		$this->stringView->set('servico', $servico);
		
		$content = $this->stringView->renderMail('envio_ordem_servico');
		return $this->scheduler->schedule($content, array (
			'from' => 'portal@rhhealth.com.br',
			'to' => $dados_fornecedor['FornecedorContato']['descricao'],
			'subject' => $servico.' Ordem de Serviço: ' . $id_ordem
		));
	}

	public function atualiza_status_ppra_concluido($codigo_cliente, $status, $data_inicio_vigencia, $vigencia_em_meses, $codigo_medico, $codigo_fornecedor = NULL){
		$this->OrdemServico =& ClassRegistry::Init('OrdemServico');
		$this->GrupoEconomicoCliente =& ClassRegistry::Init('GrupoEconomicoCliente');
		$this->Gpra =& ClassRegistry::Init('Gpra');

		try{

			$this->OrdemServico->query('begin transaction');
		
			$matriz = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);
			
			if(!empty($codigo_cliente) && !empty($status)){
				
				//codigo PPRA
				$codigo_ppra = $this->OrdemServico->getPPRAByCodigoCliente($codigo_cliente);

				$dadosOrdemServico = $this->OrdemServico->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente, 'OrdemServicoItem.codigo_servico = ' . $codigo_ppra), 'fields' => array('codigo'), 'joins' => array(
					array(
						'table' => 'ordem_servico_item',
						'alias' => 'OrdemServicoItem',
						'type' => 'INNER',
						'conditions' => array('OrdemServico.codigo = OrdemServicoItem.codigo_ordem_servico')
						)
					)));

				// debug($dadosOrdemServico);
				if($this->OrdemServico->atualiza_status($dadosOrdemServico['OrdemServico']['codigo'], $status, $codigo_ppra, $data_inicio_vigencia, $vigencia_em_meses, $codigo_fornecedor)){

					//pega o codigo do médico
					$gpra = $this->Gpra->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente)));

					//seta a data de vigencia e previsao nos riscos ambientais
					$dados_ambientais = array('Gpra' => array(
						'codigo_cliente' => $codigo_cliente,
						'codigo_medico' => $codigo_medico,
						'data_inicio_vigencia' => Comum::dateToDb($data_inicio_vigencia),
						'periodo_vigencia' => $vigencia_em_meses
					));

					// pr($dados_ambientais);exit;

					//atualiza os dados
					if(!empty($gpra)) {

						$dados_ambientais['Gpra']['codigo'] = $gpra['Gpra']['codigo'];

						if(!$this->Gpra->atualizar($dados_ambientais)){
							throw new Exception('Erro ao atualizar a data de vigencia na GPRA');
						}
					}
					else {
						if(!$this->Gpra->incluir($dados_ambientais)){
							throw new Exception('Erro ao incluir a data de vigencia na GPRA');
						}
					}// fim gpra

					$this->OrdemServico->commit();
					return true;
					// $this->BSession->setFlash('save_success');				
				}
				else{
					throw new Exception('Erro ao atualizar o status da Ordem de Serviço');
					// $this->BSession->setFlash('save_error');
				}
				
			}
			else{
				throw new Exception('Erro ao retornar cliente da Ordem de Serviço');
				// $this->BSession->setFlash('save_error');			
			}

		} catch(Exception $e) {

			$this->log($e->getMessage(), 'debug');

			$this->OrdemServico->rollback();
			return array('Erro' => $e->getMessage());
			// $this->BSession->setFlash('save_error');
			// $this->redirect($_SERVER['HTTP_REFERER']);

		} //fim catch
	}

	public function atualiza_status_ppra_versionamento($codigo_cliente, $status, $clone_versao){
		$this->OrdemServico =					ClassRegistry::Init('OrdemServico');
		$this->GrupoEconomicoCliente =			ClassRegistry::Init('GrupoEconomicoCliente');
		$this->PpraVersoes =					ClassRegistry::Init('PpraVersoes');
		$this->Gpra =							ClassRegistry::Init('Gpra');
		$this->OrdemServicoVersoes =			ClassRegistry::Init('OrdemServicoVersoes');
		$this->OrdemServicoItem =				ClassRegistry::Init('OrdemServicoItem');
		$this->OrdemServicoItemVersoes =		ClassRegistry::Init('OrdemServicoItemVersoes');
		$this->ClienteSetor =					ClassRegistry::Init('ClienteSetor');
		$this->ClienteSetorVersoes =			ClassRegistry::Init('ClienteSetorVersoes');
		$this->GrupoExposicao =					ClassRegistry::Init('GrupoExposicao');
		$this->GrupoExposicaoVersoes =			ClassRegistry::Init('GrupoExposicaoVersoes');
		$this->GrupoExposicaoRisco =			ClassRegistry::Init('GrupoExposicaoRisco');
		$this->GrupoExposicaoRiscoVersoes =		ClassRegistry::Init('GrupoExposicaoRiscoVersoes');
		$this->GrupoExposicaoRiscoEpc =			ClassRegistry::Init('GrupoExposicaoRiscoEpc');
		$this->GrupoExposicaoRiscoEpcVersoes =	ClassRegistry::Init('GrupoExposicaoRiscoEpcVersoes');
		$this->GrupoExposicaoRiscoEpi =			ClassRegistry::Init('GrupoExposicaoRiscoEpi');
		$this->GrupoExposicaoRiscoEpiVersoes =	ClassRegistry::Init('GrupoExposicaoRiscoEpiVersoes');
		$this->GrupoExpRiscoAtribDet =			ClassRegistry::Init('GrupoExpRiscoAtribDet');
		$this->GrupoExpRiscoAtribDetVers =		ClassRegistry::Init('GrupoExpRiscoAtribDetVers');
		$this->GrupoExpRiscoFonteGera =			ClassRegistry::Init('GrupoExpRiscoFonteGera');
		$this->GrupoExpRiscoFonteGeraVersoes =	ClassRegistry::Init('GrupoExpRiscoFonteGeraVersoes');
		$this->GpraVersoes =					ClassRegistry::Init('GpraVersoes');
		$this->PrevencaoRiscoAmbiental =		ClassRegistry::Init('PrevencaoRiscoAmbiental');
		$this->PrevencaoRiscoAmbientalVersoes =	ClassRegistry::Init('PrevencaoRiscoAmbientalVersoes');

		try {
			$this->OrdemServico->query('begin transaction');

			//pega o grupo economico
			$matriz = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);	
			
			//verifica se existe o cliente e o status passado pela ctp
			if(!empty($codigo_cliente) && !empty($status)){

				//codigo PPRA
				$codigo_ppra = $this->OrdemServico->getPPRAByCodigoCliente($codigo_cliente);

				//pega os dados da ordem de servico para atualizar corretamente
				$dadosOrdemServico = $this->OrdemServico->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente, 'OrdemServicoItem.codigo_servico = ' . $codigo_ppra), 'joins' => array(
					array(
						'table' => 'ordem_servico_item',
						'alias' => 'OrdemServicoItem',
						'type' => 'INNER',
						'conditions' => array('OrdemServico.codigo = OrdemServicoItem.codigo_ordem_servico')
					)
				)));

				// debug($dadosOrdemServico);exit;

				//verfica se atualizou corretamente a ordem de servico
				if($this->OrdemServico->atualiza_status($dadosOrdemServico['OrdemServico']['codigo'], $status, $codigo_ppra, null, null)) {

					/*** INICIO DO VERSIONAMENTE ***/

					/* Se existe uma versão base */ 
					if( $clone_versao ){

						$dataVersao = $this->PegaUltimaVersao( $this->PpraVersoes, $codigo_cliente );

						unset($dataVersao['codigo']);
						unset($dataVersao['data_inclusao']);
						unset($dataVersao['codigo_usuario_inclusao']);
						unset($dataVersao['codigo_usuario_alteracao']);

						$dataVersao['versao'] = date('YmdHis');		
						$dataVersao['inicio_vigencia_ppra'] = Comum::dateToDb($dataVersao['inicio_vigencia_ppra']);
						$dados_PPRA_versoes['PpraVersoes']	= $dataVersao;

					} else {

						//monta a versao
						$versao = date('YmdHis');
						//pega o codigo do médico
						$gpra = $this->Gpra->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente)));
						//seta os valores para gravar na tabela de versoes
						$dados_PPRA_versoes['PpraVersoes'] = array(
							'versao' 					=> $versao,
							'codigo_cliente_alocacao'	=> $codigo_cliente,
							'inicio_vigencia_ppra' 		=> Comum::dateToDb($gpra['Gpra']['data_inicio_vigencia']),
							'periodo_vigencia_ppra' 	=> $gpra['Gpra']['periodo_vigencia'],
							'codigo_medico'				=> $gpra['Gpra']['codigo_medico']
						);

					}

					//insere na tabela de versoes

					if($this->PpraVersoes->incluir($dados_PPRA_versoes)) {

						$codigo_ppra_versoes = $this->PpraVersoes->id;//codigo do registro que acabou de ser inseridos

						/*****ORDEM DE SERVICO ******/
						//gravar os dados da ordem de servico com o codigo_ppra_versoes
						$codigo_ordem_servico = $dadosOrdemServico['OrdemServico']['codigo'];

						//monta os dados corretamente para gravar na ordem_servico_versoes
						$ordem_servico_versoes= $dadosOrdemServico['OrdemServico'];
						unset($ordem_servico_versoes['codigo']);
						$ordem_servico_versoes['codigo_ordem_servico'] = $codigo_ordem_servico;
						$ordem_servico_versoes['codigo_ppra_versoes'] = $codigo_ppra_versoes;
						
						if($this->OrdemServicoVersoes->incluir($ordem_servico_versoes)) {
							//recuperar os dados da ordem de servico item para versionamento
							$ordem_servico_item = $this->OrdemServicoItem->find("all", array('conditions' => array('codigo_ordem_servico' => $codigo_ordem_servico)));
							$codigo_ordem_servico_item = $ordem_servico_item[0]['OrdemServicoItem']['codigo'];

							//gravar os dados da ordem de servico item versionamento com o codigo da ppra_versoes
							$ordem_servico_item_versoes = $ordem_servico_item[0]['OrdemServicoItem'];
							unset($ordem_servico_item_versoes['codigo']);						
							$ordem_servico_item_versoes['codigo_ordem_servico_item'] = $codigo_ordem_servico_item;
							$ordem_servico_item_versoes['codigo_ppra_versoes'] = $codigo_ppra_versoes;
							//verifica se incluiu a ordem_servico_item_versoes
							if(!$this->OrdemServicoItemVersoes->incluir($ordem_servico_item_versoes)){
								throw new Exception('Erro ao inserir ordem servico item versoes');
							}							
						} else {
							throw new Exception('Erro ao inserir ordem de servico versoes');
						}//fim ordem de servico

						/****CLIENTES SETORES***/

						//pega o cliente_setores
						$clientes_setores = $this->ClienteSetor->find('all', array('conditions' => array('codigo_cliente_alocacao' => $codigo_cliente)));
						foreach ($clientes_setores as $key => $cs) {
							$codigo_cliente_setor = $cs['ClienteSetor']['codigo'];

							//monta o array corretamente para inserir na setores
							$cliente_setores_versoes = $cs['ClienteSetor'];
							unset($cliente_setores_versoes['codigo']); //elimina o codigo para criar um novo na tabela
							$cliente_setores_versoes['codigo_clientes_setores'] = $codigo_cliente_setor;
							$cliente_setores_versoes['codigo_ppra_versoes'] = $codigo_ppra_versoes;

							//insere na cliente setor
							if(!$this->ClienteSetorVersoes->incluir($cliente_setores_versoes)){
								throw new Exception('Erro ao inserir cliente setor versoes');
							}//fim if cliente setor

							/***GRUPO EXPOSICAO***/

							//pega o grupo exposicao com o codigo da cliente setor
							$grupo_exposicao = $this->GrupoExposicao->find('all', array('conditions' => array('codigo_cliente_setor' => $codigo_cliente_setor)));

							if(!empty($grupo_exposicao)) {

								//variavel auxiliar para 
								$grupo_exposicao_versoes = array();

								//varre o grupo exposicao
								foreach($grupo_exposicao as $ge){

									//seta os dados do grupo de exposicao
									$grupo_exposicao_versoes = $ge['GrupoExposicao'];
									$codigo_grupo_exposicao = $ge['GrupoExposicao']['codigo'];
									
									unset($grupo_exposicao_versoes['codigo']);//elimina o codigo para inserir um novo
									//seta os codigos
									$grupo_exposicao_versoes['codigo_grupo_exposicao'] = $codigo_grupo_exposicao;
									$grupo_exposicao_versoes['codigo_ppra_versoes'] = $codigo_ppra_versoes;

									//inclui um novo grupo de exposicao
									if(!$this->GrupoExposicaoVersoes->incluir($grupo_exposicao_versoes)){
										throw new Exception('Erro ao inserir grupo exposicao versoes');
									}

									/****GRUPO EXPOSICAO RISCO***/

									//pega o grupos_exposicoes_risco
									$grupo_exposicao_risco = $this->GrupoExposicaoRisco->find('all', array('conditions' => array('codigo_grupo_exposicao' => $codigo_grupo_exposicao)));

									if(!empty($grupo_exposicao_risco)) {

										//variavel auxiliar
										$grupo_exposicao_risco_versoes = array();

										//varre os riscos
										foreach($grupo_exposicao_risco as $ger){

											//monta o que deve ser gravado na versao
											$grupo_exposicao_risco_versoes = $ger['GrupoExposicaoRisco'];
											$codigo_grupo_exposicao_risco  = $ger['GrupoExposicaoRisco']['codigo'];

											unset($grupo_exposicao_risco_versoes['codigo']);//elimina o codigo

											//seta os novos valores para os codigos
											$grupo_exposicao_risco_versoes['codigo_grupo_exposicao_risco'] 	= $codigo_grupo_exposicao_risco;
											$grupo_exposicao_risco_versoes['codigo_ppra_versoes']		= $codigo_ppra_versoes;

											//inclui os valores
											if(!$this->GrupoExposicaoRiscoVersoes->incluir($grupo_exposicao_risco_versoes)){
												throw new Exception('Erro ao inserir grupo exposicao risco versoes');
											}

											/****EPC****/

											//pega os epcs
											$grupo_exposicao_risco_epc = $this->GrupoExposicaoRiscoEpc->find('all', array('conditions' => array('codigo_grupos_exposicao_risco' => $codigo_grupo_exposicao_risco)));

											//verifica se existe registro
											if(!empty($grupo_exposicao_risco_epc)) {

												//variavel auxiliar
												$grupo_exposicao_risco_epc_versoes = array();

												//varre os dados do epc
												foreach($grupo_exposicao_risco_epc as $gere) {
													//monta os dados
													$grupo_exposicao_risco_epc_versoes = $gere['GrupoExposicaoRiscoEpc'];
													$codigo_grupo_exposicao_risco_epc = $gere['GrupoExposicaoRiscoEpc']['codigo'];

													unset($grupo_exposicao_risco_epc_versoes['codigo']);//elimina o codigo da versao

													//seta os ids
													$grupo_exposicao_risco_epc_versoes['codigo_grupo_exposicao_risco_epc'] = $codigo_grupo_exposicao_risco_epc;
													$grupo_exposicao_risco_epc_versoes['codigo_ppra_versoes']				= $codigo_ppra_versoes;

													if(!$this->GrupoExposicaoRiscoEpcVersoes->incluir($grupo_exposicao_risco_epc_versoes)){
														throw new Exception('Erro ao inserir grupo exposicao risco epc');
													}

												}//fim foreach epc

											} //fim grupo exposicao risco epc

											/****EPI****/

											//pega os epcs
											$grupo_exposicao_risco_epi = $this->GrupoExposicaoRiscoEpi->find('all', array('conditions' => array('codigo_grupos_exposicao_risco' => $codigo_grupo_exposicao_risco)));

											//verifica se existe registro
											if(!empty($grupo_exposicao_risco_epi)) {

												//variavel auxiliar
												$grupo_exposicao_risco_epi_versoes = array();

												//varre os dados do epi
												foreach($grupo_exposicao_risco_epi as $gerei) {
													//monta os dados
													$grupo_exposicao_risco_epi_versoes = $gerei['GrupoExposicaoRiscoEpi'];
													$codigo_grupo_exposicao_risco_epi = $gerei['GrupoExposicaoRiscoEpi']['codigo'];

													unset($grupo_exposicao_risco_epi_versoes['codigo']);//elimina o codigo da versao

													//seta os ids
													$grupo_exposicao_risco_epi_versoes['codigo_grupo_exposicao_risco_epi'] = $codigo_grupo_exposicao_risco_epi;
													$grupo_exposicao_risco_epi_versoes['codigo_ppra_versoes']				= $codigo_ppra_versoes;

													if(!$this->GrupoExposicaoRiscoEpiVersoes->incluir($grupo_exposicao_risco_epi_versoes)){
														throw new Exception('Erro ao inserir grupo exposicao risco epi');
													}

												}//fim foreach epi

											} //fim grupo exposicao risco epi


											/****ATRIBUTOS DETALHES****/

											//pega os epcs
											$grupo_exposicao_risco_atributos_detalhes = $this->GrupoExpRiscoAtribDet->find('all', array('conditions' => array('codigo_grupos_exposicao_risco' => $codigo_grupo_exposicao_risco),'recursive' => '-1'));
											
											//verifica se existe registro
											if(!empty($grupo_exposicao_risco_atributos_detalhes)) {

												//variavel auxiliar
												$grupo_exposicao_risco_atributo_detalhe_versoes = array();

												//varre os dados do atributos detalhes
												foreach($grupo_exposicao_risco_atributos_detalhes as $gerad) {
													//monta os dados
													$grupo_exposicao_risco_atributo_detalhe_versoes = $gerad['GrupoExpRiscoAtribDet'];
													$grupo_exposicao_risco_atributo_detalhe_versoes['codigo_grupo_exposicao_riscos_atributos_detalhes'] = $grupo_exposicao_risco_atributo_detalhe_versoes['codigo'];
													unset($grupo_exposicao_risco_atributo_detalhe_versoes['codigo']);//elimina o codigo da versao

													//seta os ids
													$grupo_exposicao_risco_atributo_detalhe_versoes['codigo_ppra_versoes']				= $codigo_ppra_versoes;

													if(!$this->GrupoExpRiscoAtribDetVers->incluir($grupo_exposicao_risco_atributo_detalhe_versoes)){
														throw new Exception('Erro ao inserir grupo exposicao risco detalhes versoes');
													}

												}//fim foreach atributos detalhes

											} //fim grupo exposicao risco atributos detalhes


											/****FONTES GERADORAS****/

											//pega os epcs
											$grupo_exposicao_risco_fontes_geradoras = $this->GrupoExpRiscoFonteGera->find('all', array('conditions' => array('codigo_grupos_exposicao_risco' => $codigo_grupo_exposicao_risco)));

											//verifica se existe registro
											if(!empty($grupo_exposicao_risco_fontes_geradoras)) {

												//variavel auxiliar
												$grupo_exposicao_risco_fontes_geradoras_versoes = array();

												//varre os dados do atributos detalhes
												foreach($grupo_exposicao_risco_fontes_geradoras as $gerfg) {
													//monta os dados
													$grupo_exposicao_risco_fontes_geradoras_versoes = $gerfg['GrupoExpRiscoFonteGera'];
													$codigo_grupo_exposicao_risco_fontes_geradoras = $gerfg['GrupoExpRiscoFonteGera']['codigo'];

													unset($grupo_exposicao_risco_fontes_geradoras_versoes['codigo']);//elimina o codigo da versao

													//seta os ids
													$grupo_exposicao_risco_fontes_geradoras_versoes['codigo_grupos_exposicao_risco_fontes_geradoras'] = $codigo_grupo_exposicao_risco_fontes_geradoras;
													$grupo_exposicao_risco_fontes_geradoras_versoes['codigo_ppra_versoes']				= $codigo_ppra_versoes;

													if(!$this->GrupoExpRiscoFonteGeraVersoes->incluir($grupo_exposicao_risco_fontes_geradoras_versoes)){
														throw new Exception('Erro ao inserir grupo exposicao risco fonte versoes');
													}

												}//fim foreach atributos detalhes

											} //fim grupo exposicao risco atributos detalhes

										}//fim foreach grupo exposicao risco

									}//fim if empty grupo exposicao risco

								}//fim foreach grupo exposicao risco

							}//fim verificacao se existe grupo exposicao

						}//fim foreach grupo exposicao


						/*********PREVENCAO AMBIENTAL***********/
						
						if(!empty($gpra)) {

							/****GPRA***/

							//dados do gpra
							$codigo_gpra = $gpra['Gpra']['codigo'];
							
							//monta o array corretamente para inserir na setores
							$gpra_versoes = $gpra['Gpra'];
							unset($gpra_versoes['codigo']); //elimina o codigo para criar um novo na tabela
							$gpra_versoes['codigo_grupos_prevencao_riscos_ambientais'] 	= $codigo_gpra;
							$gpra_versoes['codigo_ppra_versoes'] 						= $codigo_ppra_versoes;

							//insere na cliente setor
							if(!$this->GpraVersoes->incluir($gpra_versoes)){
								throw new Exception('Erro ao inserir gpra versoes');
							}//fim if cliente setor

							/*****PREVENCAO RISCO AMBIENTAL*****/
							$prevencao_ambiental = $this->PrevencaoRiscoAmbiental->find('all', array('conditions' => array('codigo_grupo_prevencao_risco_ambiental' => $codigo_gpra), 'callbacks' => false) );

							//verifica se existe registro
							if(!empty($prevencao_ambiental)) {

								//variavel auxiliar
								$prevencao_ambiental_versoes = array();

								//varre os dados do prevencao_ambiental
								foreach($prevencao_ambiental as $pa) {
									//monta os dados
									$prevencao_ambiental_versoes = $pa['PrevencaoRiscoAmbiental'];
									$codigo_prevencao_ambiental = $pa['PrevencaoRiscoAmbiental']['codigo'];

									unset($prevencao_ambiental_versoes['codigo']);//elimina o codigo da versao

									//seta os ids
									$prevencao_ambiental_versoes['codigo_prevencao_riscos_ambientais'] 	= $codigo_prevencao_ambiental;
									$prevencao_ambiental_versoes['codigo_ppra_versoes']					= $codigo_ppra_versoes;

									if(!$this->PrevencaoRiscoAmbientalVersoes->incluir($prevencao_ambiental_versoes)){
										throw new Exception('Erro ao inserir prevencao risco ambiental');
									}

								}//fim foreach epc

							} //fim $prevencao_ambiental


						} //fim verificacao gpra


					} else {
						throw new Exception('Erro ao inserir versao');
					}//fim if ppra_versoes

					/**** FIM DO VERSIONAMENTO ****/

					$this->OrdemServico->commit();
					return true;
					// $this->BSession->setFlash('save_success');
				
				} else {
					throw new Exception('Erro ao atualizar o status');
				} //fim atualizar status ordem servico
				
			} else {
				throw new Exception('Erro é necessário o cliente/status');
			}

			// if( $back_auto ){
			// 	$this->redirect( Comum::UrlOrigem()->data );	
			// } else {
			// 	$this->redirect(array('controller' => 'grupos_exposicao', 'action' => 'index', $codigo_cliente));	
			// }

		} catch(Exception $e) {

			$this->log($e->getMessage(), 'debug');

			$this->OrdemServico->rollback();
			return false;
			// $this->BSession->setFlash('save_error');
			// $this->redirect($_SERVER['HTTP_REFERER']);

		} //fim catch
	}

	private function PegaUltimaVersao( $objVersao, $codigo_cliente ){
		// popula varivel para WHERE
		$conditions = array(
		     "codigo_cliente_alocacao = ".$codigo_cliente
		);
		// define options para ORM
		$options = array(
		     "conditions" => $conditions,
		     "recursive" => -1,
		     "order" => " codigo DESC "
		);

		$resVersao = $objVersao->find( 'first', $options );
		if( $resVersao ){
			$keys = array_keys( $resVersao );
			return $resVersao[ $keys[0] ];	
		}

		return false;

	}

	/**
	 * [atualiza_status_pcmso_versionamento description]
	 * 
	 * metodo para ataulizar o status de versionamento do pcmso
	 * 
	 * @param  [type] $codigo_cliente [description]
	 * @param  [type] $status         [description]
	 * @param  [type] $clone_versao   [description]
	 * @return [type]                 [description]
	 */
	public function atualiza_status_pcmso_versionamento($codigo_cliente)
	{
		//metodos incluidos
		$this->PcmsoVersoes =	ClassRegistry::Init('PcmsoVersoes');

		//chama o metodo para pegar a ultima versao do pcmso
		$dataVersao = $this->PegaUltimaVersao( $this->PcmsoVersoes, $codigo_cliente );
		
		//verifica se tem registros 
		if( $dataVersao ){
			//elimina os indices para não tem valor e incluir com os novos valores
			unset($dataVersao['codigo']);
			unset($dataVersao['data_inclusao']);
			unset($dataVersao['codigo_usuario_inclusao']);
			unset($dataVersao['codigo_usuario_alteracao']);

			//seta novos valores nestes indices
			$dataVersao['versao'] = date('YmdHis');		
			$dataVersao['inicio_vigencia_pcmso'] = Comum::dateToDb($dataVersao['inicio_vigencia_pcmso']);
		
		}//fim verificacao
		
		//retorna o array para trabalhar e continuar o metodo
		return $dataVersao;

	}//fim atualiza_status_pcmso_versionamento

	function atualiza_status_pcmso($codigo_cliente, $status, $data_inicio_vigencia = null, $vigencia_em_meses = null, $codigo_cliente_alocacao, $codigo_fornecedor = NULL){
		$this->OrdemServico =	ClassRegistry::Init('OrdemServico');
		$this->Cliente =	ClassRegistry::Init('Cliente');
		$this->PcmsoVersoes =	ClassRegistry::Init('PcmsoVersoes');
		$this->OrdemServicoVersoes =	ClassRegistry::Init('OrdemServicoVersoes');
		$this->OrdemServicoItem =	ClassRegistry::Init('OrdemServicoItem');
		$this->OrdemServicoItemVersoes =	ClassRegistry::Init('OrdemServicoItemVersoes');
		$this->GrupoEconomicoCliente = ClassRegistry::Init('GrupoEconomicoCliente');
		$this->CronogramaAcao = ClassRegistry::Init('CronogramaAcao');
		$this->CronogramaAcaoVersao = ClassRegistry::Init('CronogramaAcaoVersao');

		$matriz = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);

		if(!empty($codigo_cliente) && !empty($status)){

			$codigo_pcmso = $this->OrdemServico->getPCMSOByCodigoCliente($codigo_cliente);

			$dadosOrdemServico = $this->OrdemServico->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente, 'OrdemServicoItem.codigo_servico = ' . $codigo_pcmso), 'joins' => array(
				array(
					'table' => 'ordem_servico_item',
					'alias' => 'OrdemServicoItem',
					'type' => 'INNER',
					'conditions' => array('OrdemServico.codigo = OrdemServicoItem.codigo_ordem_servico')
				)
			)));
			
			//debug($dadosOrdemServico); exit;

			if($dadosOrdemServico) {

				try {
				
					$this->OrdemServico->query('begin transaction');

					if($this->OrdemServico->atualiza_status($dadosOrdemServico['OrdemServico']['codigo'], $status, $codigo_pcmso, $data_inicio_vigencia, $vigencia_em_meses, $codigo_fornecedor)) {
						$dados_cliente = $this->Cliente->find('first', array(
							'fields' => array('Cliente.codigo_medico_pcmso'),
							'conditions' => array('Cliente.codigo' => $codigo_cliente)
							)
						);

						$data_inicio_vigencia = empty($data_inicio_vigencia) ? null : date('Y-m-d', strtotime($data_inicio_vigencia));

						$dados_PCMSO_versoes = array(
							'versao' => date('YmdHis'),
							'inicio_vigencia_pcmso' => $data_inicio_vigencia,
							'periodo_vigencia_pcmso' => $vigencia_em_meses,
							'codigo_medico' => $dados_cliente['Cliente']['codigo_medico_pcmso'],
							'codigo_cliente_alocacao' => $codigo_cliente,
						);

						if($this->PcmsoVersoes->incluir($dados_PCMSO_versoes)){
							$codigo_PCMSO_versoes = $this->PcmsoVersoes->getInsertID();

							/*****ORDEM DE SERVICO ******/
							//gravar os dados da ordem de servico com o codigo_ppra_versoes
							$codigo_ordem_servico = $dadosOrdemServico['OrdemServico']['codigo'];

							//monta os dados corretamente para gravar na ordem_servico_versoes
							$ordem_servico_versoes= $dadosOrdemServico['OrdemServico'];
							unset($ordem_servico_versoes['codigo']);
							$ordem_servico_versoes['codigo_ordem_servico'] = $codigo_ordem_servico;
							$ordem_servico_versoes['codigo_pcmso_versoes'] = $codigo_PCMSO_versoes;
							
							if($this->OrdemServicoVersoes->incluir($ordem_servico_versoes)) {
								//recuperar os dados da ordem de servico item para versionamento
								$ordem_servico_item = $this->OrdemServicoItem->find("all", array('conditions' => array('codigo_ordem_servico' => $codigo_ordem_servico)));
								$codigo_ordem_servico_item = $ordem_servico_item[0]['OrdemServicoItem']['codigo'];

								//gravar os dados da ordem de servico item versionamento com o codigo da ppra_versoes
								$ordem_servico_item_versoes = $ordem_servico_item[0]['OrdemServicoItem'];
								unset($ordem_servico_item_versoes['codigo']);						
								$ordem_servico_item_versoes['codigo_ordem_servico_item'] = $codigo_ordem_servico_item;
								$ordem_servico_item_versoes['codigo_pcmso_versoes'] = $codigo_PCMSO_versoes;
								//verifica se incluiu a ordem_servico_item_versoes
								if(!$this->OrdemServicoItemVersoes->incluir($ordem_servico_item_versoes)){
									throw new Exception('Erro ao inserir ordem servico item versoes');
								}							
							} 
							else {
								throw new Exception('Erro ao inserir ordem de servico versoes');
							}//fim ordem de servico

							/**
							 * CRONOGRAMA DE ACOES
							 */
							$cronograma_acao = $this->CronogramaAcao->find('all', array('conditions' => array('CronogramaAcao.codigo_cliente_matriz' => $matriz['Matriz']['codigo'], 'CronogramaAcao.codigo_cliente_unidade' => $matriz['Unidade']['codigo']), 'recursive' => true));
							foreach($cronograma_acao as $ca){
								$cav_data = $ca['CronogramaAcao'];
								unset($cav_data['codigo']);
								$cav_data['codigo_cronograma_acoes'] = $ca['CronogramaAcao']['codigo'];
								$cav_data['codigo_pcmso_versao'] = $codigo_PCMSO_versoes;
								if(!$this->CronogramaAcaoVersao->incluir($cav_data))
									throw new Exception("ERROR ao tentar inserir versao do cronograma e acao!");
							}
							/**
							 * FIM CRONOGRAMA DE ACOES
							 */

							#####################################################################################
							//verifica se os exames aplicados na unidade não existe na assinatura, para notificar
							$this->ClienteProdutoServico2 =	ClassRegistry::Init('ClienteProdutoServico2');
							$this->ClienteProdutoServico2->verificaExameAssinaturaCredenciado($codigo_cliente);
							#####################################################################################

							if($this->aplica_exames_versoes_pcmso($codigo_cliente, $codigo_PCMSO_versoes)){
								
								$this->OrdemServico->commit();
								return true;
								// $this->BSession->setFlash('save_success');

							}
							else{
								throw new Exception('Problema ao incluir em Aplica Exames Versões PCMSO.');
							}
						}
						else{
							throw new Exception('Problema ao incluir PCMSO Versões.');
						}
					
					} 
					else{
						throw new Exception('Problema ao atualizar a ordem de servico.');
					}

				} 
				catch (Exception $e) {
					//gera o log
					$this->log($e->getMessage(), 'debug');

					$this->OrdemServico->rollback();

					//seta a mensagem de erro
					return false;
					// $this->BSession->setFlash('save_error');
				}//fim catch
			}
		} else{
			return false;
			// $this->BSession->setFlash('save_error');
		}

	}

	public function aplica_exames_versoes_pcmso($codigo_cliente, $codigo_PCMSO_versoes) {
		$this->AplicacaoExame =	ClassRegistry::Init('AplicacaoExame');
		$this->AplicacaoExameVersoes =	ClassRegistry::Init('AplicacaoExameVersoes');
		
		$conditions = array('AplicacaoExame.codigo_cliente' => $codigo_cliente);

		$dados_aplicacao_exame = $this->AplicacaoExame->find('all', array('conditions' => $conditions));	

		//debug($dados_aplicacao_exame);
		
		//variavel auxiliar
		$erro = 0;

		//Verifica cada unidade do cliente
		if(!empty($dados_aplicacao_exame)){

			$this->AplicacaoExameVersoes->query('begin transaction');

			foreach ($dados_aplicacao_exame as $dados) {

				$dados['AplicacaoExame']['codigo_aplicacao_exames'] = $dados['AplicacaoExame']['codigo'];
				unset($dados['AplicacaoExame']['codigo']);
				$dados['AplicacaoExame']['codigo_pcmso_versoes'] 	= $codigo_PCMSO_versoes;


				//debug($dados);
				//Verifica os exames definidos por risco
				if(!$this->AplicacaoExameVersoes->incluir($dados['AplicacaoExame'])){
					$erro = 1;					
					break;
				}
			}//FINAL FOREACH $dados_aplicacao_exame

			if($erro == 1 ){
				$this->AplicacaoExameVersoes->rollback();
				return false;
			} else {
				$this->AplicacaoExameVersoes->commit();
				return true;
			}
		}
	}// FINAL FUNCTION aplica_exames_versoes_pcmso

    public function disparaEmail($host, $assunto, $template, $to, $attachment = null) {

        if(Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO) {
            $to = 'tid@ithealth.com.br';
            $cc = null;
        } else {
            $cc = 'agendamento@rhhealth.com.br';
        }        

        App::import('Component', array('StringView', 'Mailer.Scheduler'));

        $this->stringView = new StringViewComponent();
        $this->scheduler = new SchedulerComponent();
        $this->stringView->reset();
        $this->stringView->set('host', $host);
        
        $content = $this->stringView->renderMail($template);
        
        return $this->scheduler->schedule($content, array (
            'from' => 'portal@rhhealth.com.br',
            'to' => $to,
            'cc' => $cc,
            'subject' => $assunto
            ));
    }

}
?>
