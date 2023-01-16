<?php
class CancelamentoClienteVeiculo extends AppModel {

    var $name = 'CancelamentoClienteVeiculo';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'cancelamento_cliente_veiculo';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

	function bindCliente() {
		$this->bindModel(array(
			'belongsTo' => array(
				'Cliente' => array(
					'foreignKey' => 'codigo_cliente'
				)
			)
		));
	}

	function bindVeiculo() {
		$this->bindModel(array(
			'belongsTo' => array(
				'Veiculo' => array(
					'foreignKey' => 'codigo_veiculo'
				)
			)
		));
	}

	function cancelar_agendamento($data){
		$Cliente 					=& classRegistry::init('Cliente');
		$Veiculo 					=& classRegistry::init('Veiculo');
		$TPjurPessoaJuridica		=& classRegistry::init('TPjurPessoaJuridica');
		$TVeicVeiculo				=& classRegistry::init('TVeicVeiculo');
		$TVembVeiculoEmbarcador		=& classRegistry::init('TVembVeiculoEmbarcador');
		$TVtraVeiculoTransportador	=& classRegistry::init('TVtraVeiculoTransportador');

		$simbolos 	= array('-','.','/');
		$documento	= str_replace($simbolos, '', $data['documento']);
		$placa		= str_replace($simbolos, '', $data['placa']);

		try{
			$this->query('BEGIN TRANSACTION');
			if($this->useDbConfig != 'test_suite')$TVeicVeiculo->query('BEGIN TRANSACTION');

			// AGENDAR EXCLUSÃO NO PORTAL
			$cliente_portal = $Cliente->carregarPorDocumento($documento);
			if(!$cliente_portal)
				throw new Exception("Cliente Portal não localizado");
			
			$veiculo 		= $Veiculo->buscaCodigodaPlaca($placa);
			if(!$veiculo)
				throw new Exception("Veiculo Portal não localizado");

			$conditions = array(
						'codigo_veiculo'	=> $veiculo,
						'codigo_cliente'	=> $cliente_portal['Cliente']['codigo'],
						'data_exclusao' 	=> NULL,
					);

			$lista 		= $this->find('all',compact('conditions'));
			if(!$lista)
				throw new Exception("Nenhum agendamento foi localizado");

			foreach ($lista as $key => $agenda) {
				if(!$this->excluir($agenda[$this->name][$this->primaryKey]))
					throw new Exception("Falha ao cancelar o agendamento");
			}
			
			// AGENDAR EXCLUSÃO NO PORTAL [FIM]

			//------------------------------------------------------------------

			// MUDAR FLAG PARA SINALIZAR AGENDAMENTO PARA EXCLUSÃO

			$cliente_pjur 	= $TPjurPessoaJuridica->carregarPorCNPJ($documento);
			if(!$cliente_pjur)
				throw new Exception("Cliente Guardian não localizado");

			$veiculo_veic 	= $TVeicVeiculo->buscaPorPlaca($placa);
			if(!$veiculo_veic)
				throw new Exception("Veiculo Guardian não localizado");

			$vemb_veiculo = $TVembVeiculoEmbarcador->buscaVembarcador(
															$veiculo_veic['TVeicVeiculo']['veic_oras_codigo'],
															$cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']
															);

			$vtra_veiculo = $TVtraVeiculoTransportador->buscaVtransportador(
															$veiculo_veic['TVeicVeiculo']['veic_oras_codigo'],
															$cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']
															);


			if($vemb_veiculo){
				$vemb_veiculo['TVembVeiculoEmbarcador']['vemb_cancelado'] = 0;
				if(!$TVembVeiculoEmbarcador->save($vemb_veiculo))
					throw new Exception("Erro ao salvar a alteração do vinculo para embarcador");
			}

			if ($vtra_veiculo) {
				$vtra_veiculo['TVtraVeiculoTransportador']['vtra_cancelado'] = 0;
				if(!$TVtraVeiculoTransportador->save($vtra_veiculo))
					throw new Exception("Erro ao salvar a alteração do vinculo para transportador");
			}

			// MUDAR FLAG PARA SINALIZAR AGENDAMENTO PARA EXCLUSÃO [FIM]

			if($this->useDbConfig != 'test_suite')$TVeicVeiculo->commit();
			$this->commit();
			
			return TRUE;

		} catch( Exception $ex ) {

			if($this->useDbConfig != 'test_suite')$TVeicVeiculo->rollback();
			$this->rollback();
			
			$this->invalidate($this->primaryKey,$ex->getMessage());
			
			return FALSE;
		}

	}

	function agendar_cancelamento($data,$codigo_usuario,$retorno = false){
		$Cliente 					=& classRegistry::init('Cliente');
		$Veiculo 					=& classRegistry::init('Veiculo');
		$TPjurPessoaJuridica		=& classRegistry::init('TPjurPessoaJuridica');
		$TVeicVeiculo				=& classRegistry::init('TVeicVeiculo');
		$TVembVeiculoEmbarcador		=& classRegistry::init('TVembVeiculoEmbarcador');
		$TVtraVeiculoTransportador	=& classRegistry::init('TVtraVeiculoTransportador');

		$simbolos 	= array('-','.','/');
		$documento	= str_replace($simbolos, '', $data['documento']);
		$placa		= str_replace($simbolos, '', $data['placa']);

		try{
			$this->query('BEGIN TRANSACTION');
			$TVeicVeiculo->query('BEGIN TRANSACTION');
			
			// AGENDAR EXCLUSÃO NO PORTAL
			$cliente_portal = $Cliente->carregarPorDocumento($documento);
			if(!$cliente_portal)
				throw new Exception("Cliente Portal não localizado");
			
			$veiculo = $Veiculo->buscaCodigodaPlaca($placa);
			if(!$veiculo)
				throw new Exception("Veiculo Portal não localizado");

			$data = array(
					$this->name => array(
						'codigo_veiculo'			=> $veiculo,
						'codigo_cliente'			=> $cliente_portal['Cliente']['codigo'],
						'data_inclusao'				=> date('Y-m-d H:i:s'),
						'codigo_usuario_inclusao'	=> $codigo_usuario
					)
				);
			if(!$this->incluir($data))
				throw new Exception("Falha ao salvar o agendamento");
			// AGENDAR EXCLUSÃO NO PORTAL [FIM]

			//------------------------------------------------------------------

			// MUDAR FLAG PARA SINALIZAR AGENDAMENTO PARA EXCLUSÃO

			$cliente_pjur 	= $TPjurPessoaJuridica->carregarPorCNPJ($documento);
			if(!$cliente_pjur)
				throw new Exception("Cliente Guardian não localizado");

			$veiculo_veic 	= $TVeicVeiculo->buscaPorPlaca($placa);
			if(!$veiculo_veic)
				throw new Exception("Veiculo Guardian não localizado");

			$vemb_veiculo = $TVembVeiculoEmbarcador->buscaVembarcador(
															$veiculo_veic['TVeicVeiculo']['veic_oras_codigo'],
															$cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']
															);

			$vtra_veiculo = $TVtraVeiculoTransportador->buscaVtransportador(
															$veiculo_veic['TVeicVeiculo']['veic_oras_codigo'],
															$cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']
															);

			if($vemb_veiculo){
				$vemb_veiculo['TVembVeiculoEmbarcador']['vemb_cancelado'] = 1;
				if(!$TVembVeiculoEmbarcador->save($vemb_veiculo))
					throw new Exception("Erro ao salvar a alteração do vinculo para embarcador");
			}

			if ($vtra_veiculo) {
				$vtra_veiculo['TVtraVeiculoTransportador']['vtra_cancelado'] = 1;
				if(!$TVtraVeiculoTransportador->save($vtra_veiculo))
					throw new Exception("Erro ao salvar a alteração do vinculo para transportador");
			}

			// MUDAR FLAG PARA SINALIZAR AGENDAMENTO PARA EXCLUSÃO [FIM]

			if($this->useDbConfig != 'test_suite')
				$TVeicVeiculo->commit();
			
			$this->commit();
			
			if($retorno)
				return array('sucesso' => 'Incluido agendamento com sucesso');
			else
				return true;

		} catch( Exception $ex ) {
			echo $ex->getMessage();
			if($this->useDbConfig != 'test_suite')
				$TVeicVeiculo->rollback();

			$this->rollback();
			
			if($retorno)
				return array('erro' => $ex->getMessage());
			else
				return false;

		}

	}

	function efetuar_cancelamentos($codigo_usuario, $retorno = false, $codigo_cliente = NULL){
		$Cliente 					=& classRegistry::init('Cliente');
		$Veiculo 					=& classRegistry::init('Veiculo');

		$ClienteVeiculo 			=& classRegistry::init('ClienteVeiculo');
		$MCarroEmpresa 				=& classRegistry::init('MCarroEmpresa');
		$TPjurPessoaJuridica		=& classRegistry::init('TPjurPessoaJuridica');

		$listagem = $this->cancelamentos_em_aberto(true,$codigo_cliente);

		try{
			$this->query('BEGIN TRANSACTION');
			if($this->useDbConfig != 'test_suite')
				$TPjurPessoaJuridica->query('BEGIN TRANSACTION');

			if($listagem){
				$data_exclusao = date('Y-m-d H:i:s');

				foreach ($listagem as $dados) {
					
					// PORTAL
						if(!$ClienteVeiculo->cancelar($dados['Cliente']['codigo_documento'],$dados['Veiculo']['placa']))
							throw new Exception("Falha na exclusão do vinculo do PORTAL");
					// PORTAL [FIM]

					//-------------------------

					// MONITORA
						if(!$MCarroEmpresa->cancelar($dados['Cliente']['codigo_documento'],$dados['Veiculo']['placa']))
							throw new Exception("Falha na exclusão do vinculo do MONITORA");
					// MONITORA [FIM]

					//-------------------------

					// GUARDIAN
						if(!$TPjurPessoaJuridica->cancelar_veiculo($dados['Cliente']['codigo_documento'],$dados['Veiculo']['placa']))
							throw new Exception("Falha na exclusão do vinculo do GUARDIAN");
					// GUARDIAN [FIM]

					//-------------------------

					// ATUALIZA AGENDAMENTO
						$dados['CancelamentoClienteVeiculo']['data_exclusao'] 				= $data_exclusao;
						$dados['CancelamentoClienteVeiculo']['data_alteracao'] 				= $data_exclusao;
						$dados['CancelamentoClienteVeiculo']['codigo_usuario_alteracao'] 	= $codigo_usuario;
						if(!$this->save($dados))
							throw new Exception("Falha na atualização do agendamento");
					// ATUALIZA AGENDAMENTO [FIM]

				}

			}

			if($this->useDbConfig != 'test_suite')
				$TPjurPessoaJuridica->commit();
			
			$this->commit();
			

			return (!$retorno)?true:array('sucesso' => 'Cancelamentos efetuados com sucesso');

		} catch( Exeception $ex ) {
			
			if($this->useDbConfig != 'test_suite')
				$TPjurPessoaJuridica->rollback();

			$this->rollback();
			
			return (!$retorno)?false:array('erro' => $ex->getMessage());

		}

	}

	public function cancelamentos_em_aberto($completo = false,$codigo_cliente = NULL){
		if($completo){
			$this->bindCliente();
			$this->bindVeiculo();
		}

		$conditions = array('data_exclusao' => NULL);
		if($codigo_cliente)
			$conditions['CancelamentoClienteVeiculo.codigo_cliente'] = $codigo_cliente;
		
		return $this->find('all',compact('conditions'));
	}

	public function sincroniza_cancelamentos_veiculo_cliente( $cnpj=FALSE, $placa=FALSE ) {		
		$Cliente 			=& classRegistry::init('Cliente');
		$Veiculo 			=& classRegistry::init('Veiculo');
		$TVeicVeiculo		=& classRegistry::init('TVeicVeiculo');		
		$ClienteVeiculo 	=& classRegistry::init('ClienteVeiculo');
		$MCarroEmpresa 		=& classRegistry::init('MCarroEmpresa');
		$TPjurPessoaJuridica=& classRegistry::init('TPjurPessoaJuridica');
		$retorno  = array();
		//1) RECUPERA OS VEIUCLOS AGENDADOS NO POSTGRES
		$TVeicVeiculo->bindTVembVeiculoEmbarcador();
		$TVeicVeiculo->bindPjurPessoa();		
		$conditions = array( 'TVembVeiculoEmbarcador.vemb_cancelado' => 1 );
		if( $cnpj )
			array_push( $conditions, array('TPjurPessoaJuridica.pjur_cnpj' => $cnpj ) );
		if( $placa )
			array_push( $conditions, array('TVeicVeiculo.veic_placa' => $placa ) );
		$order  = 'TPjurPessoaJuridica.pjur_cnpj ASC';
		$fields = array(
			'TPjurPessoaJuridica.pjur_cnpj', 'TVembVeiculoEmbarcador.vemb_cancelado', 
			'TVeicVeiculo.veic_placa','TPjurPessoaJuridica.pjur_razao_social',
		);
		$veiculos_cancelar_pg  = $TVeicVeiculo->find('all', compact('conditions', 'fields', 'order'));
		if( !$veiculos_cancelar_pg ){
			array_push( $retorno, array('msg' => 'Não existem veiculos para cancelamento', 'placa'=>$placa, 'cnpj'=>$cnpj, 'tipo'=> FALSE ) );
			return $retorno;
		}
		foreach( $veiculos_cancelar_pg as $key => $dados ){
			//Busca na tabela de agendamento para cancelamento os registros do loop
			$this->bindCliente();
			$this->bindVeiculo();
			$conditions = array( 
				'Cliente.codigo_documento' => $dados['TPjurPessoaJuridica']['pjur_cnpj'],
				'CancelamentoClienteVeiculo.data_exclusao' => NULL, 
				'Veiculo.placa' => $dados['TVeicVeiculo']['veic_placa']
			);
			$cancel_veiculo_agendado_portal = $this->find('count', compact('conditions'));
			//O cancelamento nao esta agendado no Portal Por isso sera removido os vinculos do veiculo com o Cliente
			if( $cancel_veiculo_agendado_portal == 0 ){
				try{
					$this->query('BEGIN TRANSACTION');
					if(!$ClienteVeiculo->cancelar( $dados['TPjurPessoaJuridica']['pjur_cnpj'], $dados['TVeicVeiculo']['veic_placa'] ))
						throw new Exception("Falha na exclusão do vinculo do PORTAL");
					// PORTAL [FIM]
					
					if(!$MCarroEmpresa->cancelar( $dados['TPjurPessoaJuridica']['pjur_cnpj'], $dados['TVeicVeiculo']['veic_placa'] ))
						throw new Exception("Falha na exclusão do vinculo do MONITORA");
					// MONITORA [FIM]
					
					// GUARDIAN
					if(!$TPjurPessoaJuridica->cancelar_veiculo( $dados['TPjurPessoaJuridica']['pjur_cnpj'], $dados['TVeicVeiculo']['veic_placa'] ))
						throw new Exception("Falha na exclusão do vinculo do GUARDIAN");
					// GUARDIAN [FIM]
					$this->commit();					
					array_push( $retorno, array('msg' => 'Cancelamentos efetuados com sucesso', 'placa' => $dados['TVeicVeiculo']['veic_placa'], 'cnpj'=> $dados['TPjurPessoaJuridica']['pjur_cnpj'], 'tipo'=>'sucesso' ) );
				} catch( Exeception $ex ) {
					$this->rollback();
					array_push( $retorno, array('msg' => 'Cancelamentos não efetuado', 'placa' => $dados['TVeicVeiculo']['veic_placa'], 'cnpj'=> $dados['TPjurPessoaJuridica']['pjur_cnpj'], 'tipo'=>'erro' ) );
				}
			}
		}
		return $retorno;
	}

}
?>