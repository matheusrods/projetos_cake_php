<?php

class Importar extends AppModel {

	var $name = 'Importar';
	public $useTable = false;
	var $actsAs = array('Secure');

	function importar_funcionario($data){

		if(empty($data['Importar']['arquivo']['name'])){
			$this->invalidate('arquivo','Selecione um Arquivo.');
			$dados_retorno = array();	
		}
		else{
			$destino = APP.'tmp'.DS.'importacao_dados'.DS;
			if(!is_dir($destino))
				mkdir($destino); 

			$arquivo_destino = $destino.$data['Importar']['arquivo']['name'];

			if(move_uploaded_file($data['Importar']['arquivo']['tmp_name'], $arquivo_destino )){
				$arquivo = fopen($arquivo_destino, "r");
				
				
				$data['Importar']['arquivo_destino'] = $arquivo_destino;
				$dados_retorno = $this->ler_arquivo($arquivo, $data);
			}
			else{
				$dados_retorno = array();	
			}
		}
		
		return $dados_retorno;
	}

	function ler_arquivo($arquivo, $data, $limite = null){
		$dados_retorno = array();
		$arquivo_erro = array();
		$arquivo_sucesso = array();
		$retorno_arquivo = array();
		$retorno_linha_invalida = array();

		set_time_limit(0);

		if ($arquivo) {
			$c = 0;
			if(empty($limite)){
				$total = count(file($data['Importar']['arquivo_destino']));
			}
			else{
				$total = 2;
			}

			while (!feof($arquivo)) {
				$linha = utf8_encode(trim(fgets($arquivo)));
				$linha = trim(str_replace("'" , "", str_replace('"' , '', $linha)));
				
				// $linha = trim(str_replace('  ' , ' ', $linha));

				if( $c > 0 && $c < $total && !empty($linha)){
				// if( $c > 0 && $c < 5 && !empty($linha)){

					if((strlen(str_replace(';', '', $linha))) >0 ){

						$dados = explode(';', $linha );
						
						$dados_arquivo['Unidade']['nome_fantasia'] = isset($dados[0])? utf8_decode($dados[0]):'';
						$dados_arquivo['Unidade']['setor_descricao'] = isset($dados[1])? $dados[1]:'';
						$dados_arquivo['Unidade']['cargo_descricao'] = isset($dados[2])? $dados[2]:'';
						$dados_arquivo['Funcionario']['matricula'] = isset($dados[3])? utf8_decode($dados[3]):'';
						$dados_arquivo['Funcionario']['nome'] = isset($dados[4])? utf8_decode($dados[4]):'';
						$dados_arquivo['Funcionario']['data_nascimento'] = isset($dados[5])? $dados[5]:'';
						$dados_arquivo['Funcionario']['sexo'] = isset($dados[6])?((empty($dados[6]))? '' : ($dados[6] == 'F')? $dados[6] : ($dados[6] == 'M')? $dados[6]: ''):'';
						if(isset($dados[7])){
							switch ($dados[7]){
								case 'S':
								$status = 1;
								break;
								case 'F':
								$status = 2;
								break;
								case 'A':
								$status = 3;
								break;
								case 'I':
								$status = 0;
								break;
								default:
								$status = 0;
								break;
							}
						}
						else{
							$status='';
						}

						$dados_arquivo['Funcionario']['status'] = $status;
						$dados_arquivo['Funcionario']['data_admissao'] = isset($dados[8])? $dados[8]:'';
						$dados_arquivo['Funcionario']['data_demissao'] = isset($dados[9])? $dados[9]:'';
						$dados_arquivo['Funcionario']['estado_civil'] = isset($dados[10])? Comum::soNumero($dados[10]):'';
						$dados_arquivo['Funcionario']['nit'] = isset($dados[11])? Comum::soNumero($dados[11]):'';
						$dados_arquivo['Funcionario']['rg'] = isset($dados[12])? Comum::soNumero($dados[12]):'';
						$dados_arquivo['Funcionario']['uf_rg'] = isset($dados[13])? utf8_decode($dados[13]):'';
						$dados_arquivo['Funcionario']['cpf'] = isset($dados[14])? empty($dados[14])? '': str_pad(Comum::soNumero($dados[14]), 11, 0, STR_PAD_LEFT):'';
						$dados_arquivo['Funcionario']['ctps'] = isset($dados[15])? utf8_decode($dados[15]):'';
						$dados_arquivo['Funcionario']['serie_ctps'] = isset($dados[16])? utf8_decode($dados[16]):'';
						$dados_arquivo['Funcionario']['endereco_completo_funcionario'] = isset($dados[17])? utf8_decode($dados[17]):'';
						$dados_arquivo['Funcionario']['numero_funcionario'] = isset($dados[18])? Comum::soNumero($dados[18]):'';
						$dados_arquivo['Funcionario']['complemento_funcionario'] = isset($dados[19])? utf8_decode($dados[19]):'';

						$dados_arquivo['Funcionario']['bairro_funcionario'] = isset($dados[20])? utf8_decode($dados[20]):'';
						$dados_arquivo['Funcionario']['cidade_funcionario'] = isset($dados[21])? utf8_decode($dados[21]):'';
						$dados_arquivo['Funcionario']['estado_funcionario'] = isset($dados[22])? utf8_decode($dados[22]):'';
						$dados_arquivo['Funcionario']['cep_funcionario'] = isset($dados[23])? Comum::soNumero($dados[23]):'';

						$dados_arquivo['Funcionario']['deficiencia'] = isset($dados[24])? (empty($dados[24])? '' : ($dados[24] == 'S')? 1 : ($dados[24] == 'N')? 0: ''): '';

						$dados_arquivo['Funcionario']['cbo'] = isset($dados[25])? Comum::soNumero($dados[25]):'';
						$dados_arquivo['Funcionario']['codigo_gfip'] = isset($dados[26])? Comum::soNumero($dados[26]):'';
						$dados_arquivo['Funcionario']['centro_custo'] = isset($dados[27])? Comum::soNumero($dados[27]):'';
						$dados_arquivo['Funcionario']['data_ultima_aso'] = isset($dados[28])? $dados[28]:'';

						$dados_arquivo['Funcionario']['aptidao'] = isset($dados[29])? (empty($dados[29])? '' : ( (strtoupper($dados[29]) == 'A')? 1 : ( (strtoupper($dados[29]) == 'I')? 0: ''))):'';

						$dados_arquivo['Funcionario']['turno'] = isset($dados[30])? utf8_decode($dados[30]):'';
						$dados_arquivo['Funcionario']['descricao_cargo'] = isset($dados[31])? utf8_decode($dados[31]):'';
						$dados_arquivo['Funcionario']['telefone_funcionario'] = isset($dados[32])? Comum::soNumero($dados[32]):'';
						$dados_arquivo['Funcionario']['autoriza_envio_sms'] = isset($dados[33])? utf8_decode($dados[33]):'';
						$dados_arquivo['Funcionario']['email_funcionario'] = isset($dados[34])? strtolower($dados[34]):'';
						$dados_arquivo['Funcionario']['autoriza_envio_email'] = isset($dados[35])? utf8_decode($dados[35]):'';

						$dados_arquivo['Unidade']['contato_responsavel'] = isset($dados[36])? utf8_decode($dados[36]):'';
						$dados_arquivo['Unidade']['telefone_responsavel'] = isset($dados[37])? Comum::soNumero($dados[37]):'';
						$dados_arquivo['Unidade']['email_responsavel'] = isset($dados[38])? Comum::trata_nome(strtolower($dados[38])):'';
						$dados_arquivo['Unidade']['endereco_completo_unidade'] = isset($dados[39])? utf8_decode($dados[39]):'';
						$dados_arquivo['Unidade']['numero_unidade'] = isset($dados[40])? $dados[40]:'';
						$dados_arquivo['Unidade']['complemento_unidade'] = isset($dados[41])? utf8_decode($dados[41]):'';
						$dados_arquivo['Unidade']['bairro_unidade'] = isset($dados[42])? utf8_decode($dados[42]):'';
						$dados_arquivo['Unidade']['cidade_unidade'] = isset($dados[43])? utf8_decode($dados[43]):'';
						$dados_arquivo['Unidade']['estado_unidade'] = isset($dados[44])? utf8_decode($dados[44]):'';
						$dados_arquivo['Unidade']['cep_unidade'] = isset($dados[45])? Comum::soNumero($dados[45]):'';
						$dados_arquivo['Unidade']['cnpj'] = isset($dados[46])? empty($dados[46])? '': str_pad(Comum::soNumero($dados[46]), 14, 0, STR_PAD_LEFT):'';

						$dados_arquivo['Unidade']['inscricao_estadual'] =  isset($dados[47])? (empty($dados[47])? 'ISENTO': ((strtoupper($dados[47]) == 'ISENTO') || (strtoupper($dados[47]) == 'ISENTA'))? 'ISENTO': Comum::soNumero($dados[47])):'';
						$dados_arquivo['Unidade']['inscricao_municipal'] = isset($dados[48])? (empty($dados[48])? 'ISENTO': ((strtoupper($dados[48]) == 'ISENTO') || (strtoupper($dados[48]) == 'ISENTA'))? 'ISENTO': Comum::soNumero($dados[48])):'';
						$dados_arquivo['Unidade']['cnae'] = isset($dados[49])? Comum::soNumero($dados[49]):'';
						$dados_arquivo['Unidade']['grau_risco'] = isset($dados[50])? utf8_decode($dados[50]):'';
						$dados_arquivo['Unidade']['razao_social'] = isset($dados[51])? utf8_decode($dados[51]):'';
						$dados_arquivo['Funcionario']['unidade_negocio'] = isset($dados[52])? utf8_decode($dados[52]):'';
						$dados_arquivo['Unidade']['regime_tributario'] = isset($dados[53])? Comum::soNumero($dados[53]):'';
						$dados_arquivo['Unidade']['codigo_externo'] = isset($dados[54])? $dados[54]:'';
						$dados_arquivo['Unidade']['tipo_unidade'] = isset($dados[55])? $dados[55]:'';

						$dados_arquivo['Unidade']['codigo_cliente_grupo_economico'] = $data['Importar']['codigo_cliente'];

						$dados_arquivo['Funcionario']['setor_descricao'] = $dados_arquivo['Unidade']['setor_descricao'];
						$dados_arquivo['Funcionario']['cargo_descricao'] = $dados_arquivo['Unidade']['cargo_descricao'];
						$dados_arquivo['Funcionario']['cnpj'] = $dados_arquivo['Unidade']['cnpj'];
						$dados_arquivo['Funcionario']['codigo_cliente_grupo_economico'] = $dados_arquivo['Unidade']['codigo_cliente_grupo_economico'];
						$dados_arquivo['Funcionario']['codigo_externo'] = $dados_arquivo['Unidade']['codigo_externo'];

						if(count($dados) >= 56){
							$valida_unidade = $this->valida_unidade($dados_arquivo['Unidade'], $c);
							$retorno_arquivo = array($c => $dados_arquivo);

							if(!empty($valida_unidade)){
								unset($dados_arquivo['Unidade']['codigo_cliente_grupo_economico']);
								unset($dados_arquivo['Funcionario']['setor_descricao']);
								unset($dados_arquivo['Funcionario']['cargo_descricao']);
								unset($dados_arquivo['Funcionario']['cnpj']);
								
								$arquivo_erro['Erro'][$c]['erros'] = $valida_unidade[$c];
								$arquivo_erro['Erro'][$c]['dados'] = $retorno_arquivo[$c];
							} else {
								$valida_funcionario = $this->valida_funcionario($dados_arquivo['Funcionario'], $c);
								unset($dados_arquivo['Unidade']['codigo_cliente_grupo_economico']);
								unset($dados_arquivo['Funcionario']['setor_descricao']);
								unset($dados_arquivo['Funcionario']['cargo_descricao']);
								unset($dados_arquivo['Funcionario']['cnpj']);
								unset($dados_arquivo['Funcionario']['codigo_cliente_grupo_economico']);


								if(!empty($valida_funcionario)){
									$arquivo_erro['Erro'][$c]['erros'] = $valida_funcionario[$c];
									$arquivo_erro['Erro'][$c]['dados'] = $retorno_arquivo[$c];
								}
								else{
									$arquivo_sucesso['Sucesso'][$c]['dados'] = $retorno_arquivo[$c];
								}
							}//fim valida unidade

							$retorno_arquivo = array_merge($arquivo_erro, $arquivo_sucesso);
						}
						else{
							$retorno_linha_invalida['Erro'][$c]['erros'] = array('Unidade' => array( 0 => utf8_decode("Linha invalida. Quantidade de colunas invalidas.")));
							$retorno_linha_invalida['Erro'][$c]['dados'] = $dados_arquivo;
						} //fim count linha 56
					}
				}
				$c++;
			}
		}
		else{
			$retorno_arquivo[0]['Arquivo'] = "Arquivo nao encontrado";
		}

		$erros = array();
		
		if(isset($retorno_arquivo['Erro']) && !empty($retorno_arquivo['Erro'])){
			foreach ($retorno_arquivo['Erro'] as $key => $value) {
				$erros[$key] = $value;
			}
		}

		if(isset($retorno_linha_invalida['Erro']) && !empty($retorno_linha_invalida['Erro'])){
			foreach ($retorno_linha_invalida['Erro'] as $key => $value) {
				$erros[$key] = $value;
			}
		}

		if (!empty($erros)) {
			ksort($erros);
			$dados_retorno['Erro'] = $erros;
		}

		if(isset($retorno_arquivo['Sucesso']) && !empty($retorno_arquivo['Sucesso'])){
			$dados_retorno['Sucesso'] = $retorno_arquivo['Sucesso'];
		}

		return $dados_retorno;		
	}//FUNCTION

	function valida_unidade($data_unidade, $linha){
		$this->Cliente 				=& ClassRegistry::Init('Cliente');
		$this->Documento 			=& ClassRegistry::Init('Documento');
		$this->VEndereco 			=& ClassRegistry::Init('VEndereco');
		$this->ClienteEndereco 		=& ClassRegistry::Init('ClienteEndereco');
		$this->ClienteContato  		=& ClassRegistry::Init('ClienteContato');
		$this->Setor  				=& ClassRegistry::Init('Setor');
		$this->Cargo  				=& ClassRegistry::Init('Cargo');
		$this->GrupoEconomico  		=& ClassRegistry::Init('GrupoEconomico');
		$this->GrupoEconomicoCliente=& ClassRegistry::Init('GrupoEconomicoCliente');
		
		set_time_limit(0);

		$data = array();
		$retorno = array();

		if(strlen($data_unidade['cep_unidade']) == 0){
			return array($linha => array('Unidade' => array(0 => utf8_decode('CEP da Unidade inválido!'))));
		} 
		if(empty($data_unidade['cep_unidade'])){
			return array($linha => array('Unidade' =>  array('endereco' => utf8_decode('CEP inválido!'))));
		} 

		$data_unidade['cep_unidade'] = str_pad(Comum::soNumero($data_unidade['cep_unidade']), 8, 0, STR_PAD_LEFT);

		//VERIFICA SE EXISTE O CEP NA BASE DE ENDERECO;
		$consulta_endereco = $this->VEndereco->find("first", array('conditions' => array('VEndereco.endereco_cep' => $data_unidade['cep_unidade']))); 

		// SE NAO EXISTIR O CEP, EXIBE MSG DE ERRO.
		if(empty($consulta_endereco)){ 
			return array($linha => array('Unidade' =>  array('endereco' => utf8_decode('Endereço da Unidade não encontrado!'))));
		}

		//VERIFICA SE É CEP UNICO
		$consulta_cep_unico = $this->VEndereco->find("first", array(
			'conditions' => array(
				'VEndereco.endereco_logradouro LIKE "%'.Comum::retiraTipoLogradouro(utf8_encode($data_unidade['endereco_completo_unidade'])).'%"'.
				'OR VEndereco.endereco_logradouro LIKE "%'.Comum::retiraTipoLogradouro(Comum::trata_nome(utf8_encode($data_unidade['endereco_completo_unidade']))).'%"'
				)
			)
		); 

		//VERIFICA SE EXISTE O ENDERECO DO CEP UNICO;
		if($consulta_endereco['VEndereco']['endereco_cidade_cep_unico'] == 1 && empty($consulta_cep_unico)){ //VERIFICA CEP UNICO
			return array($linha => array('Unidade' =>  array('cidade_cep_unico' => utf8_decode('CEP Único inválido - Endereço Unidade'))));
		}

		// VERIFICA SE O CNPJ É VALIDO
		if(!$this->Documento->isCNPJ($data_unidade['cnpj'])){
			return array($linha => array('Unidade' =>  array(0 => utf8_decode('CNPJ inválido!'))));
		}

		//VERIFICA SE O CODIGO EXTERNO ESTA PREENCHIDO
		if(empty($data_unidade['codigo_externo'])){
			return array($linha => array('Unidade' =>  array(0 => utf8_decode('Informe o Código Externo'))));
		} 

		// VALIDA CODIGO EXTERNO DA UNIDADE	
		$conditions = array(
			'codigo_empresa' => $_SESSION['Auth']['Usuario']['codigo_empresa'],
			'codigo_externo' => $data_unidade['codigo_externo'],
			'ativo' => 1,
			);

		if($data_unidade['tipo_unidade'] == 'O') {
			$conditions['codigo_documento_real !='] = $data_unidade['cnpj'];
		} else {
			$conditions['codigo_documento !='] = $data_unidade['cnpj'];
		}

		$count = $this->Cliente->find('count', array(
			'conditions' => $conditions
			)
		);

		// VERIFICA SE EXISTE NA BASE O CODIGO EXTERNO
		if($count > 0) {
			return array($linha => array('Unidade' =>  array(0 => utf8_decode('Código Externo já existente'))));
		} 

		// VERIFICA SE O TIPO DE UNIDADE ESTÁ PREENCHIDO
		if(empty($data_unidade['tipo_unidade'])){
			return array($linha => array('Unidade' =>  array(0 => utf8_decode('Informe o Tipo da Unidade'))));
		} 

		//VERIFICA SE O TIPO DE 
		if(!in_array($data_unidade['tipo_unidade'], array('F', 'O'))) {
			return array($linha => array('Unidade' =>  array(0 => utf8_decode('Tipo da Unidade inválido'))));
		}

		//CASO NÃO TENHA ERRO, MONTA O ARRAY DE DADOS PARA INSERIR OU ATUALIZAR.					
		$data_unidade['cnpj'] = str_pad(Comum::soNumero($data_unidade['cnpj']), 14, 0, STR_PAD_LEFT);
		$dados_unidade = array(
			'Cliente' => array(
				'nome_fantasia' => $data_unidade['nome_fantasia'],
				'razao_social' => $data_unidade['razao_social'],
				'inscricao_estadual' => $data_unidade['inscricao_estadual'],
				'codigo_documento' => $data_unidade['cnpj'],
				'ccm' => $data_unidade['inscricao_municipal'],
				'codigo_regime_tributario' => $data_unidade['regime_tributario'],
				'cnae' => $data_unidade['cnae'],
				'codigo_externo' => substr($data_unidade['codigo_externo'], 0,50),
				'tipo_unidade' => strtoupper($data_unidade['tipo_unidade']),
				'ativo' => 1  

				),
			'GrupoEconomico' => array(
				'codigo_cliente' => $data_unidade['codigo_cliente_grupo_economico']
				)
			);
		$data_unidade['tipo_unidade'] = strtoupper($data_unidade['tipo_unidade']);

		//VERIFICA SE EXISTE O CLIENTE CADASTRADO NO BD.
		if($data_unidade['tipo_unidade'] == 'F') { // CASO SEJA UM CLIENTE DO TIPO FISCAL
			$conditions = array(
				'Cliente.codigo_documento' => $data_unidade['cnpj'],
				'Cliente.ativo' => 1
				);

			$consulta = $this->Cliente->find("first", compact('conditions'));
			if(!empty($consulta)){
				$dados_unidade['Cliente']['codigo'] = $consulta['Cliente']['codigo'];
			}
		} elseif($data_unidade['tipo_unidade'] == 'O') { // CASO SEJA UM CLIENTE DO TIPO OPERACIONAL

			$grupo_economico = $this->GrupoEconomico->find('first', array('conditions' => array('codigo_cliente' => $data_unidade['codigo_cliente_grupo_economico'])));

			$conditions = array(
				'Cliente.codigo_externo' => substr($data_unidade['codigo_externo'], 0,50),
				'GrupoEconomicoCliente.codigo_grupo_economico' => $grupo_economico['GrupoEconomico']['codigo'],
				'Cliente.ativo' => 1
				);

			$joins = array(
				array(
					'table' => $this->GrupoEconomicoCliente->databaseTable.'.'.$this->GrupoEconomicoCliente->tableSchema.'.'.$this->GrupoEconomicoCliente->useTable, 
					'alias' => 'GrupoEconomicoCliente',
					'type' => 'LEFT',
					'conditions' => 'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo',
					),
				);

			$consulta = $this->Cliente->find("first", compact('conditions','joins'));

			if(empty($consulta)){
				$qtd_unidades = $this->GrupoEconomicoCliente->find('count', array('conditions' => array('codigo_grupo_economico' => $grupo_economico['GrupoEconomico']['codigo'])));

				$dados_unidade['Cliente']['codigo_documento'] = $this->Cliente->geraCnpjFicticioUnico($data_unidade['cnpj'], $qtd_unidades);
				$dados_unidade['Cliente']['codigo_documento_real'] = $data_unidade['cnpj'];
			} else{
				$dados_unidade['Cliente']['codigo'] = $consulta['Cliente']['codigo'];
				$dados_unidade['Cliente']['codigo_documento'] = $consulta['Cliente']['codigo_documento'];
			}
		}

		$retorno_unidade = $this->Cliente->importacao_cliente_unidade($dados_unidade);

		//ENCONTROU OS DADOS DO CLIENTE.
		if(empty($retorno_unidade)){

			if(strlen($data_unidade['numero_unidade']) > 0){
				if(strtoupper($data_unidade['numero_unidade']) == 'S/N'){
					$data_unidade['numero_unidade'] = 0;
				}
				else{
					$data_unidade['numero_unidade'] = Comum::soNumero($data_unidade['numero_unidade']);
				}
			} else {
				$data_unidade['numero_unidade'] = 0;
			}

			$dados_unidade_endereco = array(					
				'ClienteEndereco' => array(
					'codigo_endereco' => $consulta_endereco['VEndereco']['endereco_codigo'],
					'numero' => $data_unidade['numero_unidade'], 
					'complemento' => $data_unidade['complemento_unidade'],
					)
				);

			$consulta_endereco = $this->ClienteEndereco->find('first', array('conditions' => array('codigo_cliente' => $this->Cliente->id)));

			if(!empty($consulta_endereco)){
				$dados_unidade_endereco['ClienteEndereco']['codigo'] = $consulta_endereco['ClienteEndereco']['codigo'];
			}

			$dados_unidade_endereco['ClienteEndereco']['codigo_cliente'] = $this->Cliente->id;
			$dados_unidade_endereco['ClienteEndereco']['codigo_cliente'] = $this->Cliente->id;

 			//ClienteEndereco ------------------------------------------------------------------------------
			$retorno_unidade_endereco = $this->ClienteEndereco->importacao_endereco_comercial($dados_unidade_endereco);

			if(!empty($data_unidade['telefone_responsavel']) && !empty($data_unidade['email_responsavel']) && !empty($data_unidade['contato_responsavel'])) {
				$ddd = substr($data_unidade['telefone_responsavel'], 0,2);
				$telefone_responsavel = substr($data_unidade['telefone_responsavel'], 2,strlen($data_unidade['telefone_responsavel']));

				$dados_contatos = array(
					0 => array( //TELEFONE
						'ClienteContato' => array(
							'codigo_cliente' => $this->Cliente->id,
							'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_TELEFONE,
							'ddd' => $ddd,
							'descricao' => $telefone_responsavel,
							'nome' => $data_unidade['contato_responsavel']
							)
						),
					1 => array( //EMAIL
						'ClienteContato' => array(
							'codigo_cliente' => $this->Cliente->id,
							'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL,
							'descricao' => $data_unidade['email_responsavel'],
							'nome' => $data_unidade['contato_responsavel']
							)
						)
					);

				//ClienteContato ------------------------------------------------------------------------------
				$retorno_unidade_contato = $this->ClienteContato->importacao_contato_unidade($dados_contatos);
			} else {
				$retorno_unidade_contato ='';
			} 

			//SETOR ------------------------------------------------------------------------------
			$retorno_unidade_setor = $this->Setor->localiza_setor_importacao($data_unidade);

			//CARGOS ------------------------------------------------------------------------------
			$retorno_unidade_cargo = $this->Cargo->localiza_cargo_importacao($data_unidade);


		}

		//RESULTADO --------------------------------------------
		if(!empty($retorno_unidade)){
			$data = array_merge($data, $retorno_unidade);
		}

		if(!empty($retorno_unidade_endereco)){
			$data = array_merge($data, $retorno_unidade_endereco);
		}

		if(!empty($retorno_unidade_contato)){
			$data = array_merge($data, $retorno_unidade_contato);
		}

		if(isset($retorno_unidade_setor['Erro']) && !empty($retorno_unidade_setor['Erro'])){
			$erro_setor = '';
			$erros_setor = array();

			foreach ($retorno_unidade_setor['Erro']['Setor'] as $campo => $msg_erro_setor) {
				$erro_setor.= ($msg_erro_setor).'|';
			}

			$erros_setor['Setor']['codigo_setor'] = $erro_setor;                
			$data = array_merge($data, $erros_setor);

		}

		if(isset($retorno_unidade_cargo['Erro']) && !empty($retorno_unidade_cargo['Erro'])){
			$erro_cargo = '';
			$erros_cargo = array();

			foreach ($retorno_unidade_cargo['Erro']['Cargo'] as $campo => $msg_erro_cargo) {
				$erro_cargo.= ($msg_erro_cargo).'|';
			}

			$erros_cargo['Cargo']['codigo_cargo'] = $erro_cargo;
			$data = array_merge($data, $erros_cargo);
		}
		
		if(!empty($data))
			$retorno[$linha] = $data;

		return $retorno;
	} //fim valida unidade

	function valida_funcionario($data_funcionario, $linha){

		$this->Funcionario =& ClassRegistry::Init('Funcionario');
		$this->Documento =& ClassRegistry::Init('Documento');
		$this->Endereco =& ClassRegistry::Init('Endereco');
		$this->VEndereco =& ClassRegistry::Init('VEndereco');
		$this->FuncionarioEndereco =& ClassRegistry::Init('FuncionarioEndereco');
		$this->Cliente =& ClassRegistry::Init('Cliente');
		$this->Setor =& ClassRegistry::Init('Setor');
		$this->Cargo =& ClassRegistry::Init('Cargo');
		$this->GrupoEconomico =& ClassRegistry::Init('GrupoEconomico');
		$this->GrupoEconomicoCliente =& ClassRegistry::Init('GrupoEconomicoCliente');
		$this->ClienteFuncionario =& ClassRegistry::Init('ClienteFuncionario');
		$this->FuncionarioSetorCargo =& ClassRegistry::Init('FuncionarioSetorCargo');
		$this->TipoRetorno =& ClassRegistry::Init('TipoRetorno');
		$this->FuncionarioContato =& ClassRegistry::Init('FuncionarioContato');

		set_time_limit(0);

        // SE NAO ENCONTROU ERROS NA VALIDAÇÃO DE CPF FAÇA
		if(!isset($this->validationErrors[$linha]['Funcionario']) && empty($this->validationErrors[$linha]['Funcionario'])){ 
			$data = array();

			$data_funcionario['cep_funcionario'] = str_pad(Comum::soNumero($data_funcionario['cep_funcionario']), 8, 0, STR_PAD_LEFT);

			$conditions_cep = array(
				'VEndereco.endereco_cep' => $data_funcionario['cep_funcionario']
				);

			//VERIFICA SE EXISTE O CEP NA BASE DE ENDERECO;
			$consulta_endereco = $this->VEndereco->find("first", array('conditions' => $conditions_cep)); 

			// SE NAO EXISTIR O CEP, EXIBE MSG DE ERRO.
			if(empty($consulta_endereco)){
				return array($linha => array('Funcionario' => array('endereco' => utf8_decode('Endereço inválido - Funcionário!'))));
			}

			//VERIFICA SE É CEP UNICO
			if($consulta_endereco['VEndereco']['endereco_cidade_cep_unico'] == 1){ 
				return array($linha => array('Funcionario' => array('cep' => utf8_decode('CEP Único inválido - Endereço Funcionário'))));
			}

			//VERIFICA SE A DATA DE ADMISSAO FOI PREENCHIDA
			if(empty($data_funcionario['data_admissao'])){ 
				return array($linha => array('ClienteFuncionario' => array('data_admissao' => utf8_decode('Data de admissão inexistente - Cliente Funcionário'))));
			}

			$unidade = $this->Cliente->find("first", array(
				'conditions' => array(
					'codigo_externo' => substr($data_funcionario['codigo_externo'], 0,50),
					'ativo' => 1
					),
				'joins' => array(
					array(
						'table' => $this->GrupoEconomicoCliente->databaseTable.'.'.$this->GrupoEconomicoCliente->tableSchema.'.'.$this->GrupoEconomicoCliente->useTable,
						'alias' => 'GrupoEconomicoCliente',
						'type' => 'INNER',
						'conditions' => array(
								'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo'
							)
						),
					array(
						'table' => $this->GrupoEconomico->databaseTable.'.'.$this->GrupoEconomico->tableSchema.'.'.$this->GrupoEconomico->useTable,
						'alias' => 'GrupoEconomico',
						'type' => 'INNER',
						'conditions' => array(
								'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
							)
						),
					),
				'fields' => array(
						'Cliente.*',
						'GrupoEconomico.codigo_cliente'
					)
				)
			);

			if(!empty($unidade)){
				$codigo_cliente_unidade = $unidade['Cliente']['codigo'];
				$codigo_cliente_matriz = $unidade['GrupoEconomico']['codigo_cliente'];

				$setor = $this->Setor->find("first", array(
					'conditions' => array(
						"codigo_cliente" => $data_funcionario['codigo_cliente_grupo_economico'], 
						"(descricao = '".(substr(trim($data_funcionario['setor_descricao']), 0, 50))."' OR descricao = '".Comum::trata_nome((substr(trim($data_funcionario['setor_descricao']), 0, 50)))."')",
						),
					'recursive' => -1
					)
				);

				$codigo_setor = (empty($setor))? '' : $setor['Setor']['codigo'];

				$cargo = $this->Cargo->find("first", array(
					'conditions' => array(
						'codigo_cliente' => $data_funcionario['codigo_cliente_grupo_economico'], 
						'(descricao = "'.(substr(trim($data_funcionario['cargo_descricao']), 0, 50)).'" OR descricao = "'.Comum::trata_nome((substr(trim($data_funcionario['cargo_descricao']), 0, 50))).'")',
						),
					'recursive' => -1
					)
				);	

				$codigo_cargo = (empty($cargo))? '' : $cargo['Cargo']['codigo'];

				$dados_funcionario = array(
					'Funcionario' => array(
						'nome' => $data_funcionario['nome'],
						'data_nascimento' => $data_funcionario['data_nascimento'],
						'sexo' => $data_funcionario['sexo'],
						'estado_civil' => $data_funcionario['estado_civil'],
						'pis' => $data_funcionario['nit'],
						'rg' => $data_funcionario['rg'],
						'rg_orgao' => $data_funcionario['uf_rg'],
						'cpf' => $data_funcionario['cpf'],
						'ctps' => $data_funcionario['ctps'],
						'ctps_serie' => $data_funcionario['serie_ctps'],
						'deficiencia' => $data_funcionario['deficiencia'],
						),
					'ClienteFuncionario' => array(
						//'codigo_cliente' => $codigo_cliente_unidade,
						'codigo_cliente_matricula' =>  $codigo_cliente_matriz,
						'admissao' => $data_funcionario['data_admissao'],
						'ativo' => $data_funcionario['status'],
						'matricula' => $data_funcionario['matricula'],
						'data_demissao' => $data_funcionario['data_demissao'],
						'centro_custo' => $data_funcionario['centro_custo'],
						'data_ultima_aso' => $data_funcionario['data_ultima_aso'],
						'aptidao' => $data_funcionario['aptidao'],
						'turno' =>  $data_funcionario['aptidao'],
						),
					'FuncionarioSetorCargo' => array(
						'data_inicio' => $data_funcionario['data_admissao'],
						'codigo_cliente_alocacao' => $codigo_cliente_unidade,
						'codigo_setor' => $codigo_setor,
						'codigo_cargo' => $codigo_cargo
						)
					);

				$dados_funcionario_endereco = array(					
					'FuncionarioEndereco' => array(
						'codigo_endereco' => $consulta_endereco['VEndereco']['endereco_codigo'],
						'numero' => $data_funcionario['numero_funcionario'],
						'complemento' => $data_funcionario['complemento_funcionario']
						)
					);

				$ddd = substr($data_funcionario['telefone_funcionario'], 0,2);
				$telefone_funcionario = substr($data_funcionario['telefone_funcionario'], 2,strlen($data_funcionario['telefone_funcionario']));

				$dados_contatos = array(
					    	0 => array( //TELEFONE
					    		'FuncionarioContato' => array(
					    			'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_TELEFONE,
					    			'ddd' => $ddd,
					    			'descricao' => $telefone_funcionario,
					    			'nome' => $data_funcionario['nome'],
					    			'autoriza_envio_sms' => (empty($data_funcionario['autoriza_envio_sms'])? '' : ($data_funcionario['autoriza_envio_sms'] == 'S'? 1 : 0))
					    			)
					    		),
					    	1 => array( //EMAIL
					    		'FuncionarioContato' => array(
					    			'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL,
					    			'descricao' => $data_funcionario['email_funcionario'],
					    			'autoriza_envio_email' => (empty($data_funcionario['autoriza_envio_email'])? '' : ($data_funcionario['autoriza_envio_email'] == 'S'? 1 : 0))
					    			)
					    		)
					    	);


				//VERIFICA SE EXISTE O FUNCIONARIO CADASTRADO NO BD.
				$conditions = array(
					'Funcionario.cpf' => $data_funcionario['cpf'],
					);

				$joins = array(
					array(
						'table' => $this->FuncionarioEndereco->databaseTable.'.'.$this->FuncionarioEndereco->tableSchema.'.'.$this->FuncionarioEndereco->useTable, 
						'alias' => 'FuncionarioEndereco',
						'type' => 'LEFT',
						'conditions' => 'FuncionarioEndereco.codigo_funcionario = Funcionario.codigo',
						),
					array(
						'table' => $this->ClienteFuncionario->databaseTable.'.'.$this->ClienteFuncionario->tableSchema.'.'.$this->ClienteFuncionario->useTable,
						'alias' => 'ClienteFuncionario',
						'type' => 'LEFT',
						'conditions' => array(
							'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
							'ClienteFuncionario.codigo_cliente_matricula = '.$codigo_cliente_matriz
							)
						),
					array(
						'table' => $this->FuncionarioSetorCargo->databaseTable.'.'.$this->FuncionarioSetorCargo->tableSchema.'.'.$this->FuncionarioSetorCargo->useTable,
						'alias' => 'FuncionarioSetorCargo',
						'type' => 'LEFT',
						'conditions' => array(
							'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
							'FuncionarioSetorCargo.codigo_cliente_alocacao = '.$codigo_cliente_unidade,
							'FuncionarioSetorCargo.codigo_setor = '.$codigo_setor,
							'FuncionarioSetorCargo.codigo_cargo = '.$codigo_cargo,
							)
						),
					);

				$fields = array(
					'Funcionario.codigo', 
					'Funcionario.nome', 
					'Funcionario.data_nascimento',
					'Funcionario.rg',
					'Funcionario.rg_orgao',
					'Funcionario.cpf',
					'Funcionario.sexo',
					'Funcionario.ctps',
					'Funcionario.ctps_data_emissao',
					'Funcionario.gfip',
					'Funcionario.rg_data_emissao',
					'Funcionario.nit',
					'Funcionario.ctps_serie',
					'Funcionario.cns',
					'Funcionario.ctps_uf',
					'Funcionario.email',
					'Funcionario.estado_civil',
					'Funcionario.deficiencia',
					'FuncionarioEndereco.codigo',
					'FuncionarioEndereco.codigo_funcionario',
					'FuncionarioEndereco.codigo_tipo_contato',
					'FuncionarioEndereco.codigo_endereco',
					'FuncionarioEndereco.complemento',
					'FuncionarioEndereco.numero',
					'FuncionarioEndereco.latitude',
					'FuncionarioEndereco.longitude',
					'ClienteFuncionario.codigo',
					'FuncionarioSetorCargo.codigo'
					);

				$recursive = -1;
				$consulta = $this->Funcionario->find("first", compact('conditions', 'joins', 'fields', 'recursive'));

				//ENCONTROU OS DADOS DO FUNCIONARIO.
				if(!empty($consulta)){	
					$dados_funcionario['Funcionario']['codigo'] = $consulta['Funcionario']['codigo'];

					if(!empty($consulta['ClienteFuncionario']['codigo'])){
						$dados_funcionario['ClienteFuncionario']['codigo'] = $consulta['ClienteFuncionario']['codigo'];

						if(!empty($consulta['FuncionarioSetorCargo']['codigo'])){
							$dados_funcionario['FuncionarioSetorCargo']['codigo'] = $consulta['FuncionarioSetorCargo']['codigo'];
						}
					}

				}

				$dados_funcionario_endereco['FuncionarioEndereco']['codigo_funcionario'] = $consulta['Funcionario']['codigo'];

				//FUNCIONARIO ------------------------------------------------------------------------------
				$retorno_funcionario = $this->Funcionario->importacao_funcionario($dados_funcionario);

				if(empty($retorno_funcionario)){

					$dados_funcionario_endereco['FuncionarioEndereco']['codigo_funcionario'] = $this->Funcionario->id;
					$dados_funcionario_endereco['FuncionarioEndereco']['codigo'] = $consulta['FuncionarioEndereco']['codigo'];

					$consulta_contato = $this->FuncionarioContato->find('all', array('conditions' => array('codigo_funcionario' => $this->Funcionario->id)));
					if(!empty($consulta_contato)){
						foreach ($consulta_contato as $key => $data_contato) {
							$dados_contatos[$key]['FuncionarioContato']['codigo'] = $data_contato['FuncionarioContato']['codigo'];
						}
					}

					$dados_contatos[0]['FuncionarioContato']['codigo_funcionario'] = $this->Funcionario->id;
					$dados_contatos[1]['FuncionarioContato']['codigo_funcionario'] = $this->Funcionario->id;

					//ENDERECO FUNCIONARIO ------------------------------------------------------------------------------
					$retorno_funcionario_endereco = $this->FuncionarioEndereco->importacao_endereco_comercial_funcionario($dados_funcionario_endereco);

					//CONTATO FUNCIONARIO ------------------------------------------------------------------------------
					$retorno_funcionario_contato = $this->FuncionarioContato->importacao_contato_funcionario($dados_contatos);
				}

				//RESULTADO --------------------------------------------
				if(!empty($retorno_funcionario))
					$data[$linha] = array_merge($data, $retorno_funcionario);

				if(!empty($retorno_funcionario_endereco))
					$data[$linha] = array_merge($data, $retorno_funcionario_endereco);

				if(!empty($retorno_funcionario_contato))
					$data[$linha] = array_merge($data, $retorno_funcionario_contato);

			}//funcionario enviado

		} //fim validacao de erros

		return $data;
	}

	function importar_ppra($data){
		$this->Cliente =& ClassRegistry::Init('Cliente');
		$this->Setor =& ClassRegistry::Init('Setor');
		$this->Cargo =& ClassRegistry::Init('Cargo');
		$this->ClienteFuncionario =& ClassRegistry::Init('ClienteFuncionario');
		$this->GrupoHomogeneo =& ClassRegistry::Init('GrupoHomogeneo');
		$this->ClienteSetor =& ClassRegistry::Init('ClienteSetor');
		$this->SetorCaracteristica =& ClassRegistry::Init('SetorCaracteristica');
		$this->SetorCaracteristicaAtributo =& ClassRegistry::Init('SetorCaracteristicaAtributo');
		$this->GrupoExposicao =& ClassRegistry::Init('GrupoExposicao');
		$this->GrupoExposicaoRisco =& ClassRegistry::Init('GrupoExposicaoRisco');
		$this->Risco =& ClassRegistry::Init('Risco');
		$this->ExposicaoOcupacional =& ClassRegistry::Init('ExposicaoOcupacional');
		$this->ExposicaoOcupAtributo =& ClassRegistry::Init('ExposicaoOcupAtributo');
		$this->RiscoAtributo =& ClassRegistry::Init('RiscoAtributo');
		$this->RiscoAtributoDetalhe =& ClassRegistry::Init('RiscoAtributoDetalhe');
		$this->TecnicaMedicao =& ClassRegistry::Init('TecnicaMedicao');
		$this->FonteGeradora =& ClassRegistry::Init('FonteGeradora');
		$this->GrupoExpRiscoFonteGera =& ClassRegistry::Init('GrupoExpRiscoFonteGera');
		$this->Epc =& ClassRegistry::Init('Epc');
		$this->GrupoExposicaoRiscoEpc =& ClassRegistry::Init('GrupoExposicaoRiscoEpc');
		$this->Epi =& ClassRegistry::Init('Epi');
		$this->GrupoExposicaoRiscoEpi =& ClassRegistry::Init('GrupoExposicaoRiscoEpi');
		$this->LogIntegracao =& ClassRegistry::Init('LogIntegracao');
		$this->Funcionario =& ClassRegistry::Init('Funcionario');
		$this->Medico =& ClassRegistry::Init('Medico');
		$this->ClienteImplantacao =& ClassRegistry::Init('ClienteImplantacao');
		$this->Fornecedor = ClassRegistry::Init('Fornecedor');
		$this->OrdemServico = ClassRegistry::Init('OrdemServico');
		$this->AlertaHierarquiaPendente = ClassRegistry::Init('AlertaHierarquiaPendente');
		$this->array_importacao_ppra = array();
		$array_versoes_ppra = array();

		$dados_retorno = array();	
		$hierarquias_enviadas = array();
		$destino = APP.'tmp'.DS.'importacao_ppra'.DS;

		if(!is_dir($destino)){
			mkdir($destino, 0777, true);
		}

		$arquivo_erro = array();
		$arquivo_sucesso = array();

		$arquivo_destino = $destino.$data['Importar']['arquivo']['name'];

		if( move_uploaded_file($data['Importar']['arquivo']['tmp_name'], $arquivo_destino) || ($this->useDbConfig == 'test_suite') ){
			$arquivo = fopen($arquivo_destino, "r");

			if ($arquivo) {
				$c = 0;
				$total = count(file($arquivo_destino));

				while (!feof($arquivo)) {

					$linha = utf8_encode(trim(fgets($arquivo)));
					
					if( $c > 0 && $c < $total && !empty($linha)){
						// if( $c > 41 && $c < 45 && !empty($linha)){
						$data_arquivo = explode(';', $linha );
						$dados_arquivo['codigo_cliente_grupo_economico'] = $data['Importar']['codigo_cliente'];
						$dados_arquivo['razao_social'] = $data_arquivo[0];
						$dados_arquivo['nome_fantasia'] = $data_arquivo[1];
						$dados_arquivo['codigo_externo'] = $data_arquivo[2];
						$dados_arquivo['setor_descricao'] = $data_arquivo[3];
						$dados_arquivo['cargo_descricao'] = $data_arquivo[4];
						$dados_arquivo['nome_funcionario'] = $data_arquivo[5];
						$dados_arquivo['cpf_funcionario'] = (strlen($data_arquivo[6]>0))? str_pad(Comum::soNumero($data_arquivo[6]), 11, 0, STR_PAD_LEFT):'';
						$dados_arquivo['tipo_ppra'] = Comum::soNumero($data_arquivo[7]);
						$dados_arquivo['nome_ghe'] = $data_arquivo[8];
						$dados_arquivo['data_vistoria'] = $data_arquivo[9];
						$dados_arquivo['pe_direito_setor'] = $data_arquivo[10];
						$dados_arquivo['iluminacao_setor'] = $data_arquivo[11];
						$dados_arquivo['cobertura_setor'] = $data_arquivo[12];								
						$dados_arquivo['estrutura_setor'] = $data_arquivo[13];
						$dados_arquivo['ventilacao_setor'] = $data_arquivo[14];
						$dados_arquivo['piso_setor'] = $data_arquivo[15];
						$dados_arquivo['observacao'] = $data_arquivo[16];
						$dados_arquivo['descricao_cargo'] = $data_arquivo[17];
						$dados_arquivo['medidas_controle'] = $data_arquivo[18];
						$dados_arquivo['funcionario_entrevistado'] = $data_arquivo[19];
						$dados_arquivo['funcionario_entrevistado_terceiro'] = $data_arquivo[20];
						$dados_arquivo['data_inicio_vigencia'] = $data_arquivo[21];
						$dados_arquivo['risco'] = $data_arquivo[22];
						$dados_arquivo['fonte_geradora'] = $data_arquivo[23];
						$dados_arquivo['efeito_critico'] = $data_arquivo[24];
						$dados_arquivo['meio_exposicao'] = $data_arquivo[25];
						$dados_arquivo['tipo_tempo'] = $data_arquivo[26];
						$dados_arquivo['minutos'] = Comum::soNumero($data_arquivo[27]);
						$dados_arquivo['jornada'] = Comum::soNumero($data_arquivo[28]);
						$dados_arquivo['descanso'] = Comum::soNumero($data_arquivo[29]);
						$dados_arquivo['intensidade'] = $data_arquivo[30];
						$dados_arquivo['resultante'] = $data_arquivo[31];
						$dados_arquivo['dano'] = $data_arquivo[32];
						$dados_arquivo['grau_risco'] = $data_arquivo[33];
						$dados_arquivo['codigo_tipo_medicao'] = Comum::soNumero($data_arquivo[34]);
						$dados_arquivo['dosimetria'] = Comum::soNumero($data_arquivo[35]);
						$dados_arquivo['avaliacao_instantanea'] = Comum::soNumero($data_arquivo[36]);
						$dados_arquivo['codigo_tecnica_medicao'] = utf8_decode($data_arquivo[37]);
						$dados_arquivo['valor_maximo'] = $data_arquivo[38];
						$dados_arquivo['valor_medido'] = $data_arquivo[39];
						$dados_arquivo['descanso_no_local'] = Comum::soNumero($data_arquivo[40]);
						$dados_arquivo['descanso_tbn'] = Comum::soNumero($data_arquivo[41]);
						$dados_arquivo['descanso_tbs'] = Comum::soNumero($data_arquivo[42]);
						$dados_arquivo['descanso_tbg'] = Comum::soNumero($data_arquivo[43]);
						$dados_arquivo['carga_solar'] = Comum::soNumero($data_arquivo[44]);
						$dados_arquivo['trabalho_tbn'] = Comum::soNumero($data_arquivo[45]);
						$dados_arquivo['trabalho_tbs'] = Comum::soNumero($data_arquivo[46]);
						$dados_arquivo['trabalho_tbg'] = Comum::soNumero($data_arquivo[47]);
						$dados_arquivo['epi'] = $data_arquivo[48];
						$dados_arquivo['epc'] = $data_arquivo[49];
						$dados_arquivo['documento_fornecedor'] = $data_arquivo[50];
						$dados_arquivo['data_inicio_vigencia_contrato'] = $data_arquivo[51];
						$dados_arquivo['vigencia_contrato'] = $data_arquivo[52];
						$dados_arquivo['numero_conselho_medico_contrato'] = $data_arquivo[53];
						$dados_arquivo['conselho_medico_contrato'] = $data_arquivo[54];
						$dados_arquivo['uf_conselho_medico_contrato'] = $data_arquivo[55];

						$dados['Dados']['DadoArquivo'] = $dados_arquivo;

						// debug($dados_arquivo);

						$codigo_unidade = $this->Cliente->localiza_cliente_importacao($dados['Dados']['DadoArquivo']);
						$codigo_unidade = $codigo_unidade['Dados']['Cliente']['codigo'];

						$codigo_servico_ppra = $this->OrdemServico->getPPRAByCodigoCliente($codigo_unidade);

						if(!isset($array_versoes_ppra[$codigo_unidade])){
							// debug('entrou');
							$validacao_versao = $this->valida_versao_ppra($dados['Dados']['DadoArquivo'],$codigo_unidade,$codigo_servico_ppra);
							$array_versoes_ppra[$codigo_unidade] = $validacao_versao;
						}

						// debug('nao entrou');exit;

						if(!isset($array_versoes_ppra[$codigo_unidade]['Erro'])){

	                        $this->query('begin transaction');

							$retorno_valida_dados_principais = $this->valida_dados_ppra($dados['Dados']['DadoArquivo'], $c);

					    	//CASO NAO ENCONTRE ERROS NA UNIDADE, SETOR, CARGO, FUNCIONARIO OU GHE, PASSA PARA O PROXIMO PASSO;
							if(empty($retorno_valida_dados_principais[$c]['Erro'])){
								$dados['Dados'] = array_merge($dados['Dados'], $retorno_valida_dados_principais[$c]['Dados']);

								$retorno_valida_setor = $this->valida_dados_setor($dados['Dados'], $c);

								if(empty($retorno_valida_setor[$c]['Erro'])){
									$dados['Dados'] = array_merge($dados['Dados'], $retorno_valida_setor[$c]['Dados']);

									$retorno_grupo_exposicao = $this->valida_grupo_exposicao($dados['Dados'], $c);
									if(empty($retorno_grupo_exposicao[$c]['Erro'])){
										$dados['Dados'] = array_merge($dados['Dados'], $retorno_grupo_exposicao[$c]['Dados']);

										$retorno_grupo_exposicao_risco = $this->valida_grupo_exposicao_riscos($dados['Dados'], $c);
										if(empty($retorno_grupo_exposicao_risco[$c]['Erro'])){
											$dados['Dados'] = array_merge($dados['Dados'], $retorno_grupo_exposicao_risco[$c]['Dados']);
											if(empty($dados['Dados']['DadoArquivo']['fonte_geradora'])){
												$retorno_fonte_geradora[$c]['Erro'] = array();
												$retorno_fonte_geradora[$c]['Dados'] = array();

											} else {
												$retorno_fonte_geradora = $this->valida_fontes_geradoras($dados['Dados'], $c);
											}

											if(empty($retorno_fonte_geradora[$c]['Erro']['GrupoExpRiscoFonteGera'])){

												$dados['Dados'] = array_merge($dados['Dados'], $retorno_fonte_geradora[$c]['Dados']);

												if(empty($dados['Dados']['DadoArquivo']['epi'])){
													$retorno_epi[$c]['Erro'] = array();
													$retorno_epi[$c]['Dados'] = array();
												} else {
													$retorno_epi = $this->valida_epi($dados['Dados'], $c);
												}

												if(empty($retorno_epi[$c]['Erro'])){
													$dados['Dados'] = array_merge($dados['Dados'], $retorno_epi[$c]['Dados']);

													if(empty($dados['Dados']['DadoArquivo']['epc'])){
														$retorno_epc[$c]['Erro'] = array();
														$retorno_epc[$c]['Dados'] = array();
													} else {
														$retorno_epc = $this->valida_epc($dados['Dados'], $c);
													}

													if(empty($retorno_epc[$c]['Erro'])){
														$arquivo_sucesso['Sucesso'][$c]['dados'] = $dados['Dados']['DadoArquivo'];
													} else {
														$arquivo_erro['Erro'][$c]['erros'] = $retorno_epc[$c]['Erro'];
														$arquivo_erro['Erro'][$c]['dados'] = $dados_arquivo;
														$this->rollback();
													}
												} else {
													$arquivo_erro['Erro'][$c]['erros'] = $retorno_epi[$c]['Erro'];
													$arquivo_erro['Erro'][$c]['dados'] = $dados_arquivo;
													$this->rollback();
												}
											} else {
												$arquivo_erro['Erro'][$c]['erros'] = $retorno_fonte_geradora[$c]['Erro'];
												$arquivo_erro['Erro'][$c]['dados'] = $dados_arquivo;
												$this->rollback();
											}
										} else {
											$arquivo_erro['Erro'][$c]['erros'] = $retorno_grupo_exposicao_risco[$c]['Erro'];
											$arquivo_erro['Erro'][$c]['dados'] = $dados_arquivo;
											$this->rollback();
										}
									} else {
										$arquivo_erro['Erro'][$c]['erros'] = $retorno_grupo_exposicao[$c]['Erro'];
										$arquivo_erro['Erro'][$c]['dados'] = $dados_arquivo;
										$this->rollback();
									}
								} else {
									$arquivo_erro['Erro'][$c]['erros'] = $retorno_valida_setor[$c]['Erro'];
									$arquivo_erro['Erro'][$c]['dados'] = $dados_arquivo;
									$this->rollback();
								}
							} else {
								$arquivo_erro['Erro'][$c]['erros'] = $retorno_valida_dados_principais[$c]['Erro'];
								$arquivo_erro['Erro'][$c]['dados'] = $dados_arquivo;
								$this->rollback();
							}
							if(empty($arquivo_erro['Erro'][$c]['dados'])){
								$this->commit();

								$dados_ppra = Set::extract($retorno_valida_dados_principais,'{n}.Dados');
		
								
								$cliente_ppra = $dados_ppra[0]['Cliente']['codigo'];
								$setor_ppra = $dados_ppra[0]['Setor']['codigo'];
								$cargo_ppra = $dados_ppra[0]['Cargo']['codigo'];
								

								$array_hierarquia = array(
									'codigo_cliente_alocacao' => $cliente_ppra,
									'codigo_setor' => $setor_ppra,
									'codigo_cargo' => $cargo_ppra
								);

								
								//Se o alerta de pendência concluída ainda não foi enviado para esta hierarquia
								if(array_search($array_hierarquia, $hierarquias_enviadas) === false) {

									//Armazena a hierarquia no array de alertas enviados
									$hierarquias_enviadas[] = $array_hierarquia;

									//verifica se existe alerta para esta hierarquia pendente e notifica clientes caso já exista o PPRA
									$this->AlertaHierarquiaPendente->envia_alerta_hierarquia($cliente_ppra, $setor_ppra, $cargo_ppra,'PCMSO');
								}



							}
						} else {
							$erro_secundario = false;
							//verifica se, dentro do array de erro, existe um erro de versão para determinada unidade
							if ( isset($arquivo_erro['Erro']) ){
								foreach ($arquivo_erro['Erro'] as $dados_erros) {
									if( isset($dados_erros['erros']['OrdemServico'][$codigo_unidade]) ){
										$erro_secundario = true;
										break;
									}
								}
							}
							//se já existir um erro de versão para a unidade, todos os registros dessa unidade serão invalidados pelo primeiro erro
							$arquivo_erro['Erro'][$c]['erros'] = ($erro_secundario ? array('OrdemServico' => array($codigo_unidade => 'Os registros dessa unidade foram invalidados, ja que nao foi possivel abrir uma nova versao.')) : $array_versoes_ppra[$codigo_unidade]['Erro']);
							$arquivo_erro['Erro'][$c]['dados'] = $dados['Dados']['DadoArquivo'];
						}
					}
					$dados_retorno = array_merge($arquivo_erro, $arquivo_sucesso);
					$c++;
				}
			} else {
				$dados_retorno[0]['Arquivo'] = "Arquivo nao encontrado";
			}
		}

		$this->deleta_riscos_nao_importados();

		$dados_log = array(
			'LogIntegracao' => array(
				'codigo_cliente' => $data['Importar']['codigo_cliente'],
				'arquivo' => $data['Importar']['arquivo']['name'],
				'conteudo' => $this->monta_conteudo_log($arquivo_destino),
				'retorno' => $this->monta_retorno_log($dados_retorno),
				'sistema_origem' => 'IMPORTACAO_PPRA',
				'status' => ( empty($dados_retorno['Erro']) ? '1' : '0' ),
				'descricao' => ( empty($dados_retorno['Erro']) ? 'SUCESSO' : 'REGISTROS COM ERROS DETECTADOS!' ),
				'tipo_operacao' => 'I',
				'data_arquivo' => date('Y-m-d H:i:s'),
			)
		);

		$this->LogIntegracao->incluir($dados_log);

		return $dados_retorno;
	}

	function valida_dados_ppra($data, $linha){
		$this->Cliente =& ClassRegistry::Init('Cliente');
		$retorno[$linha]['Dados'] = array();
		$retorno[$linha]['Erro'] = array();
		if(!empty($data['codigo_cliente_grupo_economico'])){

			// verifica se não estão sendo passados dados de fluxos alternativos
			if( $data['codigo_tipo_medicao'] == 0 || $data['codigo_tipo_medicao'] == 1 || ( $data['codigo_tipo_medicao'] == 2 && (empty($data['codigo_tecnica_medicao']) && empty($data['valor_maximo']) && empty($data['valor_medido'])) ) ){

				// verifica a data_vigencia
				if( !isset($data['data_vigencia']) || empty($data['data_vigencia']) ){

					// verifica se o cliente já está cadastrado na base
					$retorno_unidade = $this->Cliente->localiza_cliente_importacao($data);
					if(!isset($retorno_unidade['Erro']) && empty($retorno_unidade['Erro'])){

						$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorno_unidade['Dados']);

						//verifica se o setor está cadastrado na base, se não estiver, cadastra caso unidade se encontra desbloqueada
						$retorno_setor = $this->Setor->localiza_setor_importacao($data,$retorno_unidade['Dados']['GrupoEconomicoCliente']['bloqueado']);
						if(isset($retorno_setor['Erro']) && !empty($retorno_setor['Erro'])){
							$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorno_setor['Erro']);
						} else {
							$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorno_setor['Dados']);
						}

						//verifica se o cargo está cadastrado na base, se não estiver, cadastra caso unidade se encontra desbloqueada
						$retorno_cargo = $this->Cargo->localiza_cargo_importacao($data,$retorno_unidade['Dados']['GrupoEconomicoCliente']['bloqueado']);
						if(isset($retorno_cargo['Erro']) && !empty($retorno_cargo['Erro'])){
							$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorno_cargo['Erro']);
						} else {
							$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorno_cargo['Dados']);
						}

						//verifica se os dados para incluir hierarquia existem
						$this->GrupoEconomicoCliente = ClassRegistry::Init('GrupoEconomicoCliente');
						$conditions = array(
							'codigo' => $retorno_unidade['Dados']['GrupoEconomicoCliente']['codigo'],
							"EXISTS(SELECT TOP 1 codigo FROM setores WHERE codigo_cliente = ". $data['codigo_cliente_grupo_economico']." AND (descricao = '".$data['setor_descricao']."' OR descricao = '".Comum::trata_nome($data['setor_descricao'])."') AND ativo = 1)",
							"EXISTS(SELECT TOP 1 codigo FROM cargos WHERE codigo_cliente = ". $data['codigo_cliente_grupo_economico']." AND (descricao = '".$data['cargo_descricao']."' OR  descricao = '".Comum::trata_nome($data['cargo_descricao'])."') AND ativo = 1)"
						);
						$fields = array(
							'codigo','bloqueado'
						);
						$hierarquia = $this->GrupoEconomicoCliente->find('first', array('conditions' => $conditions,'fields' => $fields,'recursive' => -1));

						//cria hierarquia SE o setor e cargo se encontram na base e se a unidade se encontra desbloqueada
						if(!empty($hierarquia)){

							$dados_hierarquia = array(
								'codigo_cliente' => $retorno_unidade['Dados']['Cliente']['codigo'],
								'bloqueado' => $retorno_unidade['Dados']['GrupoEconomicoCliente']['bloqueado'],
								'codigo_cargo' => $retorno_cargo['Dados']['Cargo']['codigo'],
								'codigo_setor' => $retorno_setor['Dados']['Setor']['codigo']
							);
							$retorno_hierarquia = $this->incluiHierarquiaImportacao($dados_hierarquia);
							if(!empty($retorno_hierarquia)){
								if(isset($retorno_hierarquia['Erro']) && !empty($retorno_hierarquia['Erro'])){
									$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorno_hierarquia['Erro']);
								} else {
									$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorno_hierarquia['Dados']);
								}
							}
						}

						if(isset($data['tipo_ppra']) && !empty($data['tipo_ppra'])){
							if($data['tipo_ppra'] == 1){ 
								if(empty($retorno_setor['Dados']) && empty($retorno_cargo['Dados'])){
									if (!$retorno_unidade['Dados']['GrupoEconomicoCliente']['bloqueado']) {
										// $retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], array('GrupoExposicao' => array('tipo_ppra' => utf8_decode('Setor/Cargo nao enviados!'))));
									}		
								}
							}

							elseif($data['tipo_ppra'] == 2){ 
								if(!empty($retorno_setor['Dados']) && !empty($retorno_cargo['Dados'])){
									if(!empty($data['cpf_funcionario'])){
										$data['codigo_cliente_unidade'] = $retorno_unidade['Dados']['Cliente']['codigo'];
										$data['codigo_setor'] = $retorno_setor['Dados']['Setor']['codigo'];
										$data['codigo_cargo'] = $retorno_cargo['Dados']['Cargo']['codigo'];
										$retorno_funcionario = $this->ClienteFuncionario->localiza_funcionario_importacao($data);
										
										// debug($retorno);exit;
										
										if(isset($retorno_funcionario['Erro']) && !empty($retorno_funcionario['Erro'])){
											$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorno_funcionario['Erro']);
										} else {
											$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorno_funcionario['Dados']);
										}

									} else {
										$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], array('cpf_funcionario' => 'CPF do funcionario nao enviado!'));		
									}
								}
								// else{
								// 	$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], array('GrupoExposicao' => array('tipo_ppra' => utf8_decode('Setor/Cargo nao enviados!'))));		
								// }
							} elseif ($data['tipo_ppra'] == 3){ 
								$data['codigo_cliente_unidade'] = $retorno_unidade['Dados']['Cliente']['codigo'];
								$data['codigo_setor'] = $retorno_setor['Dados']['Setor']['codigo'];
								$data['codigo_cargo'] = $retorno_cargo['Dados']['Cargo']['codigo'];

								$retorno_ghe = $this->GrupoHomogeneo->localiza_ghe_importacao($data);

								$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorno_ghe['Dados']);

							} else {
								$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], array('GrupoExposicao' => array('tipo_ppra' => utf8_decode('Tipo do PPRA nao encontrado'))));
							}			
						} else {		
							$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], array('GrupoExposicao' => array('tipo_ppra' => utf8_decode('Tipo do PPRA nao enviado'))));
						}
					} else {
						$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorno_unidade['Erro']);
					}
				} else {
					$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], array('GrupoExposicao' => array('data_inicio_vigencia' => utf8_decode('O início da data de vigência não foi enviado'))));
				}
			} else {
				$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], array('GrupoExposicaoRisco' => array('codigo_tipo_medicao' => utf8_decode('Dados enviados para tipos de medicao diferentes do esperado.'))));
			}
		} else {		
			$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], array('codigo_cliente_grupo_economico' => 'Codigo do Grupo Economico nao enviado!'));
		}

		return $retorno;
	}

	function valida_dados_setor($data, $linha){
		$retorno[$linha]['Dados'] = array();
		$retorno[$linha]['Erro'] = array();
		$erro_setor_caracteristica = array();

		if(!empty($data['DadoArquivo']['pe_direito_setor'])){
			$consulta_pe_direito = $this->SetorCaracteristicaAtributo->busca_atributo(SetorCaracteristica::PE_DIREITO, trim($data['DadoArquivo']['pe_direito_setor']));
			if(!empty($consulta_pe_direito)){
				$pe_direito_setor = $consulta_pe_direito['SetorCaracteristicaAtributo']['codigo'];			
			}
			else{
				$erro_setor_caracteristica = array_merge($erro_setor_caracteristica, array('pe_direito' => 'Pé direito não encontrado!'));
			}
		}
		else{
			$pe_direito_setor = null;
		}

		if(!empty($data['DadoArquivo']['iluminacao_setor'])){
			$consulta_iluminacao = $this->SetorCaracteristicaAtributo->busca_atributo(SetorCaracteristica::ILUMINACAO, trim($data['DadoArquivo']['iluminacao_setor']));
			if(!empty($consulta_iluminacao)){
				$iluminacao_setor = $consulta_iluminacao['SetorCaracteristicaAtributo']['codigo'];
			}
			else{
				$erro_setor_caracteristica = array_merge($erro_setor_caracteristica, array('iluminacao_setor' => 'Iluminação não encontrado!'));
			}
		}
		else{
			$iluminacao_setor = null;
		}

		if(!empty($data['DadoArquivo']['estrutura_setor'])){
			$consulta_ventilacao = $this->SetorCaracteristicaAtributo->busca_atributo(SetorCaracteristica::VENTILACAO, trim($data['DadoArquivo']['ventilacao_setor']));
			if(!empty($consulta_ventilacao)){
				$ventilacao_setor = $consulta_ventilacao['SetorCaracteristicaAtributo']['codigo'];
			}
			else{
				$erro_setor_caracteristica = array_merge($erro_setor_caracteristica, array('ventilacao_setor' => 'Ventilação não encontrado!'));
			}
		}
		else{
			$ventilacao_setor = null;
		}
		
		if(!empty($data['DadoArquivo']['estrutura_setor'])){
			$consulta_estrutura = $this->SetorCaracteristicaAtributo->busca_atributo(SetorCaracteristica::ESTRUTURA, trim($data['DadoArquivo']['estrutura_setor']));
			if(!empty($consulta_estrutura)){
				$estrutura_setor = $consulta_estrutura['SetorCaracteristicaAtributo']['codigo'];
			}
			else{
				$erro_setor_caracteristica = array_merge($erro_setor_caracteristica, array('estrutura_setor' => 'Estrutura não encontrado!'));
			}
		}
		else{
			$estrutura_setor = null;
		}
		
		if(!empty($data['DadoArquivo']['cobertura_setor'])){
			$consulta_cobertura = $this->SetorCaracteristicaAtributo->busca_atributo(SetorCaracteristica::COBERTURA, trim($data['DadoArquivo']['cobertura_setor']));
			if(!empty($consulta_cobertura)){
				$cobertura_setor = $consulta_cobertura['SetorCaracteristicaAtributo']['codigo'];
			}
			else{
				$erro_setor_caracteristica = array_merge($erro_setor_caracteristica, array('cobertura_setor' => 'Cobertura não encontrada!'.$data['DadoArquivo']['cobertura_setor']));
			}
		}
		else{
			$cobertura_setor = null;
		}

		if(!empty($data['DadoArquivo']['piso_setor'])){
			$consulta_piso = $this->SetorCaracteristicaAtributo->busca_atributo(SetorCaracteristica::PISO, trim($data['DadoArquivo']['piso_setor']));
			if(!empty($consulta_piso)){
				$piso_setor = $consulta_piso['SetorCaracteristicaAtributo']['codigo'];
			}
			else{
				$erro_setor_caracteristica = array_merge($erro_setor_caracteristica, array('piso_setor' => 'Piso não encontrado!'));
			}
		}
		else{
			$piso_setor = null;
		}
		if(empty($erro_setor_caracteristica)){
			$dados = array(
				'ClienteSetor' => array(
					'codigo_cliente_alocacao' => $data['Cliente']['codigo'],
					'codigo_setor' =>$data['Setor']['codigo'],
					'pe_direito' => $pe_direito_setor,
					'cobertura' => $cobertura_setor,
					'iluminacao' => $iluminacao_setor,
					'ventilacao' => $ventilacao_setor,
					'piso' => $piso_setor,
					'estrutura' => $estrutura_setor,
					)
				);

			$conditions = array(
				'codigo_cliente_alocacao' => $data['Cliente']['codigo'],
				'codigo_setor' => $data['Setor']['codigo']
				);
			
			$consulta_cliente_setor = $this->ClienteSetor->find("first", array('conditions' => $conditions));

			if(!empty($consulta_cliente_setor)){
				$dados['ClienteSetor']['codigo'] = $consulta_cliente_setor['ClienteSetor']['codigo'];
			}

			$retorna_cliente_setor = $this->ClienteSetor->cliente_setor_importacao($dados);

			if(isset($retorna_cliente_setor['Erro']) && !empty($retorna_cliente_setor['Erro'])){
				$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorna_cliente_setor['Erro']);
			}
			else{
				$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorna_cliente_setor['Dados']);
			}
		}
		else{
			$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], array('ClienteSetor' => $erro_setor_caracteristica));
		}

		return $retorno;
	}

	function valida_grupo_exposicao($data, $linha){
		$retorno[$linha]['Dados'] = array();
		$retorno[$linha]['Erro'] = array();

		if(isset($data['ClienteFuncionario']['codigo_funcionario']) && !empty($data['ClienteFuncionario']['codigo_funcionario'])){
			$codigo_funcionario = $data['ClienteFuncionario']['codigo_funcionario'];
		} else {
			$codigo_funcionario = '';
		}

		if(isset($data['GrupoHomDetalhe']['codigo_grupo_homogeneo']) && !empty($data['GrupoHomDetalhe']['codigo_grupo_homogeneo'])){
			$codigo_grupo_homogeneo = $data['GrupoHomDetalhe']['codigo_grupo_homogeneo'];
		} else {
			$codigo_grupo_homogeneo = '';
		}

		// verifica funcionario_entrevistado
		if( isset($data['DadoArquivo']['funcionario_entrevistado']) && !empty($data['DadoArquivo']['funcionario_entrevistado']) ){
			$codigo_funcionario_entrevistado = $this->retorna_funcionario($data['DadoArquivo']['funcionario_entrevistado']);
			if( !$codigo_funcionario_entrevistado ){
				$retorno[$linha]['Erro']['GrupoExposicao']['funcionario_entrevistado'] = utf8_decode('O funcionario enviado nao foi encontrado no sistema!');
			}
		} else {
			$codigo_funcionario_entrevistado = NULL;
		}

		if( empty($retorno[$linha]['Erro']) ){

			//verifica se existe o profissional cadastrado
			$codigo_profissional = $this->valida_medico_versao($data['DadoArquivo']['numero_conselho_medico_contrato'],$data['DadoArquivo']['conselho_medico_contrato'],$data['DadoArquivo']['uf_conselho_medico_contrato']);
			//verifica se existe o profissional cadastrado
			if(empty($codigo_profissional)) {
				$codigo_profissional = null;
			}

			$dados = array(
				'GrupoExposicao' => array(	
					'codigo_cargo' => $data['Cargo']['codigo'],
					'descricao_atividade' => $data['DadoArquivo']['descricao_cargo'],
					'data_documento' => $data['DadoArquivo']['data_vistoria'],
					'observacao' => $data['DadoArquivo']['observacao'],
					'codigo_cliente_setor' => $data['ClienteSetor']['codigo'],
					'codigo_grupo_homogeneo' => $codigo_grupo_homogeneo,
					'codigo_funcionario' => $codigo_funcionario,
					'medidas_controle' => $data['DadoArquivo']['medidas_controle'],
					'funcionario_entrevistado' => $codigo_funcionario_entrevistado,
					'data_inicio_vigencia' => $data['DadoArquivo']['data_inicio_vigencia'],
					'funcionario_entrevistado_terceiro' => $data['DadoArquivo']['funcionario_entrevistado_terceiro'],
					'codigo_medico' => $codigo_profissional
				)
			);
			
			$conditions = array(
				'GrupoExposicao.codigo_cargo' => $data['Cargo']['codigo'],
				'GrupoExposicao.codigo_cliente_setor' => $data['ClienteSetor']['codigo'],
				);

			//verifica se tem codigo do funcionario para pesquisar por ele tb caso tenha atualiza o grupo de exposicao senha inclui por funcionario
			if(!empty($codigo_funcionario)) {
				$conditions['GrupoExposicao.codigo_funcionario'] = $codigo_funcionario;
			}
			$consulta_grupo_exposicao = $this->GrupoExposicao->find("first", compact('conditions'));
			
			if(isset($consulta_grupo_exposicao) && !empty($consulta_grupo_exposicao)){
				$dados['GrupoExposicao']['codigo'] = $consulta_grupo_exposicao['GrupoExposicao']['codigo'];
			}

			$retorna_grupo_exposicao = $this->GrupoExposicao->grupo_exposicao_importacao($dados, $data);

			if(isset($retorna_grupo_exposicao['Erro']) && !empty($retorna_grupo_exposicao['Erro'])){
				$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorna_grupo_exposicao['Erro']);
			}
			else{
				$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorna_grupo_exposicao['Dados']);
			}
		}

		return $retorno;
	}

	function valida_grupo_exposicao_riscos($data, $linha){
		$this->Configuracao  =& ClassRegistry::Init('Configuracao');
		$retorno[$linha]['Dados'] = array();
		$retorno[$linha]['Erro'] = array();
		$erros_grupos_exposicao_riscos = array();

		$risco = ($data['DadoArquivo']['risco']);

		if(!empty($risco)){
			$consulta_risco = $this->Risco->carregaragente($risco);	

			if(!empty($consulta_risco)){
				$codigo_risco = $consulta_risco['Risco']['codigo'];
				$codigo_grupo_exposicao = $data['GrupoExposicao']['codigo'];
				$minutos = $data['DadoArquivo']['minutos'];
				$jornada = $data['DadoArquivo']['jornada'];
				$codigo_tipo_medicao = $data['DadoArquivo']['codigo_tipo_medicao'];

				if($consulta_risco['Risco']['risco_caracterizado_por_calor'] == 1){//RISCO: CALOR
					$descanso = (!empty($data['DadoArquivo']['descanso']))?	$data['DadoArquivo']['descanso'] : '';
					$descanso_no_local = (!empty($data['DadoArquivo']['descanso_no_local']))? $data['DadoArquivo']['descanso_no_local'] : '';
					$descanso_tbn = (!empty($data['DadoArquivo']['descanso_tbn']))? $data['DadoArquivo']['descanso_tbn'] : '';
					$descanso_tbs = (!empty($data['DadoArquivo']['descanso_tbs']))? $data['DadoArquivo']['descanso_tbs'] : '' ;
					$descanso_tbg = (!empty($data['DadoArquivo']['descanso_tbg']))? $data['DadoArquivo']['descanso_tbg'] : '' ;
					$carga_solar  = (!empty($data['DadoArquivo']['carga_solar']))? $data['DadoArquivo']['carga_solar'] : '' ;
					$trabalho_tbn = (!empty($data['DadoArquivo']['trabalho_tbn']))? $data['DadoArquivo']['trabalho_tbn'] : '' ;
					$trabalho_tbs = (!empty($data['DadoArquivo']['trabalho_tbs']))? $data['DadoArquivo']['trabalho_tbs'] : '' ;
					$trabalho_tbg = (!empty($data['DadoArquivo']['trabalho_tbg']))? $data['DadoArquivo']['trabalho_tbg'] : '' ;

					$tecnica_medicao = "";
					$valor_maximo = "";
					$valor_medido = "";
				} else {
					$descanso = "";
					$descanso_no_local = '';
					$descanso_tbn =  '';
					$descanso_tbs = '' ;
					$descanso_tbg = '' ;
					$carga_solar = '' ;
					$trabalho_tbn = '' ;
					$trabalho_tbs = '' ;
					$trabalho_tbg = '' ;

					if(!empty($data['DadoArquivo']['codigo_tecnica_medicao'])){
						$consulta_tecnica_medicao = $this->TecnicaMedicao->find("first", array('conditions' => array('abreviacao' => $data['DadoArquivo']['codigo_tecnica_medicao'])));

						if(!empty($consulta_tecnica_medicao)){
							$tecnica_medicao = $consulta_tecnica_medicao['TecnicaMedicao']['codigo'];
						} else {
							$erros_grupos_exposicao_riscos = array_merge($erros_grupos_exposicao_riscos, array('codigo_tecnica_medicao' => 'Tecnica de Medicao nao encontrada!'));
						}
					} else {
						$tecnica_medicao="";
					}
					$valor_maximo = $data['DadoArquivo']['valor_maximo'];
					$valor_medido = $data['DadoArquivo']['valor_medido'];
				}

				if($consulta_risco['Risco']['risco_caracterizado_por_ruido'] == 1){ //RISCO: RUIDO
					if(!empty($data['DadoArquivo']['dosimetria'])){
						$dosimetria = $data['DadoArquivo']['dosimetria'];
					} else {
						$dosimetria = "";
					}
					
					if(!empty($data['DadoArquivo']['dosimetria'])){
						$avaliacao_instantanea = $data['DadoArquivo']['avaliacao_instantanea'];
					} else {
						$avaliacao_instantanea = "";
					}
				} else {
					$dosimetria = "";
					$avaliacao_instantanea = "";
				}

				if(!empty($data['DadoArquivo']['efeito_critico'])){
					$consulta_efeito_critico = $this->RiscoAtributoDetalhe->busca_atributo(RiscoAtributo::CLASSIFICACAO_EFEITO_CRITICO, utf8_encode(trim($data['DadoArquivo']['efeito_critico'])));
					if(!empty($consulta_efeito_critico)){
						$efeito_critico = $consulta_efeito_critico['RiscoAtributoDetalhe']['codigo'];
					} else {
						$efeito_critico = '';
						$erros_grupos_exposicao_riscos = array_merge($erros_grupos_exposicao_riscos, array('efeito_critico' => utf8_decode('Efeito Critico nao encontrado!')));
					}
				} else {
					$efeito_critico = '';
				}

				if(!empty($data['DadoArquivo']['meio_exposicao'])){
					$consulta_meio_exposicao = $this->RiscoAtributoDetalhe->busca_atributo(RiscoAtributo::MEIO_EXPOSICAO, utf8_encode(trim($data['DadoArquivo']['meio_exposicao'])));
					if(!empty($consulta_meio_exposicao)){
						$meio_exposicao = $consulta_meio_exposicao['RiscoAtributoDetalhe']['codigo'];
					} else {
						$meio_exposicao ='';
						$erros_grupos_exposicao_riscos = array_merge($erros_grupos_exposicao_riscos, array('meio_exposicao' => utf8_decode('Meio de Exposicao nao encontrado!')));
					}
				} else {
					$meio_exposicao = '';
				}

				if(!empty($data['DadoArquivo']['tipo_tempo'])){
					$consulta_tempo_exposicao = $this->ExposicaoOcupAtributo->busca_atributo(ExposicaoOcupacional::TEMPO_EXPOSICAO, utf8_encode(trim($data['DadoArquivo']['tipo_tempo'])));
					if(!empty($consulta_tempo_exposicao)){
						$tempo_exposicao = $consulta_tempo_exposicao['ExposicaoOcupAtributo']['codigo'];
					} else {
						$tempo_exposicao ='';
						$erros_grupos_exposicao_riscos = array_merge($erros_grupos_exposicao_riscos, array('tempo_exposicao' => utf8_decode('Tempo de Exposiçao nao encontrado!')));
					}
				} else {
					$tempo_exposicao = '';
				}

				if(!empty($data['DadoArquivo']['intensidade'])){
					$consulta_intensidade = $this->ExposicaoOcupAtributo->busca_atributo(ExposicaoOcupacional::INTENSIDADE, utf8_encode(trim($data['DadoArquivo']['intensidade'])));
					if(!empty($consulta_intensidade)){
						$intensidade = $consulta_intensidade['ExposicaoOcupAtributo']['codigo'];
					} else {
						$intensidade='';
						$erros_grupos_exposicao_riscos = array_merge($erros_grupos_exposicao_riscos, array('intensidade' => utf8_decode('Intensidade nao encontrada!')));
					}
				} else {
					$intensidade='';
				}

				if(!empty($data['DadoArquivo']['resultante'])){
					$consulta_resultante = $this->ExposicaoOcupAtributo->busca_atributo(ExposicaoOcupacional::RESULTANTE, utf8_encode(trim($data['DadoArquivo']['resultante'])));
					if(!empty($consulta_resultante)){
						$resultante = $consulta_resultante['ExposicaoOcupAtributo']['codigo'];
					} else {
						$resultante='';
						$erros_grupos_exposicao_riscos = array_merge($erros_grupos_exposicao_riscos, array('resultante' => utf8_decode('Resultante nao encontrada!')));
					}
				} else {
					$resultante='';
				}

				if(!empty($data['DadoArquivo']['dano'])){
					$consulta_dano = $this->ExposicaoOcupAtributo->busca_atributo(ExposicaoOcupacional::DANO, utf8_encode(trim($data['DadoArquivo']['dano'])));
					if(!empty($consulta_dano)){
						$dano = $consulta_dano['ExposicaoOcupAtributo']['codigo'];
					} else {
						$dano = '';
						$erros_grupos_exposicao_riscos = array_merge($erros_grupos_exposicao_riscos, array('dano' => utf8_decode('Dano nao encontrado!')));
					}
				} else {
					$dano = '';
				}

				if(!empty($data['DadoArquivo']['grau_risco'])){
					$consulta_grau_risco = $this->ExposicaoOcupAtributo->busca_atributo(ExposicaoOcupacional::GRAU_RISCO, utf8_encode(trim($data['DadoArquivo']['grau_risco'])));
					if(!empty($consulta_grau_risco)){
						$grau_risco = $consulta_grau_risco['ExposicaoOcupAtributo']['codigo'];
					} else {
						$grau_risco='';
						$erros_grupos_exposicao_riscos = array_merge($erros_grupos_exposicao_riscos, array('grau_risco' => utf8_decode('Grau de risco nao encontrado!')));
					}	
				} else {
					$grau_risco='';
				}

				if(empty($erros_grupos_exposicao_riscos)){

					$valida_tempo_intensidade = $this->valida_tempo_intensidade($tempo_exposicao, $intensidade, $resultante);
					$valida_dano_resultante = $this->valida_dano_resultante($dano, $resultante, $grau_risco);

					if(($valida_tempo_intensidade) && ($valida_dano_resultante)){

						if( !isset($this->array_importacao_ppra[$data['GrupoExposicao']['codigo_cliente_setor']][$data['GrupoExposicao']['codigo_cargo']]) && empty($this->array_importacao_ppra[$data['GrupoExposicao']['codigo_cliente_setor']][$data['GrupoExposicao']['codigo_cargo']]) ){
							$dados_cliente_setor_cargo = $this->GrupoExposicaoRisco->find('all',array('conditions' => array('codigo_grupo_exposicao' => $data['GrupoExposicao']['codigo'])));
							foreach ($dados_cliente_setor_cargo as $dado) {
								$this->array_importacao_ppra[ $data['GrupoExposicao']['codigo_cliente_setor'] ][ $data['GrupoExposicao']['codigo_cargo'] ][ $dado['GrupoExposicaoRisco']['codigo_risco'] ] = $dado['GrupoExposicaoRisco'];
							}
						}

						$dados_grupo_exposicao_risco = array(
							'GrupoExposicaoRisco' => array(				
								'codigo_grupo_exposicao' => $codigo_grupo_exposicao,
								'codigo_risco' => $codigo_risco,
								'tempo_exposicao' => $tempo_exposicao,
								'intensidade' => $intensidade,
								'resultante' => $resultante,
								'dano' => $dano,
								'grau_risco' => $grau_risco,
								'codigo_tipo_medicao' => $codigo_tipo_medicao,
								'codigo_tecnica_medicao' => $tecnica_medicao,
								'valor_maximo' => $valor_maximo,
								'valor_medido' => $valor_medido,
								'minutos_tempo_exposicao' => $minutos,
								'jornada_tempo_exposicao' => $jornada,
								'descanso_tempo_exposicao' => $descanso,
								'codigo_efeito_critico' => $efeito_critico,
								'dosimetria' => $valor_maximo,
								'avaliacao_instantanea' => $avaliacao_instantanea,
								'descanso_tbn' => $descanso_tbn,
								'descanso_tbs' => $descanso_tbs,
								'descanso_tg' => $descanso_tbg,
								'descanso_no_local' => $descanso_no_local,
								'trabalho_tbn' => $trabalho_tbn,
								'trabalho_tbs' => $trabalho_tbs,
								'trabalho_tg' => $trabalho_tbg,
								'carga_solar' => $carga_solar,
								'codigo_risco_atributo' => $meio_exposicao,
							)
						);
							
				    	// $joins  = array(
				    	// 	array(
		    			// 		'table' => 'RHHealth.dbo.grupo_exposicao',
		    			// 		'alias' => 'GrupoExposicao',
		    			// 		'type' => 'INNER',
		    			// 		'conditions' => 'GrupoExposicao.codigo = GrupoExposicaoRisco.codigo_grupo_exposicao',
				    	// 	)
				    	// );

						$consulta_grupo_exposicao_risco = $this->GrupoExposicaoRisco->find("first", array('conditions' => array('codigo_grupo_exposicao' => $codigo_grupo_exposicao, 'codigo_risco' => $codigo_risco)));
						if(isset($consulta_grupo_exposicao_risco['GrupoExposicaoRisco']['codigo']) && !empty($consulta_grupo_exposicao_risco['GrupoExposicaoRisco']['codigo'])){
							$dados_grupo_exposicao_risco['GrupoExposicaoRisco']['codigo'] = $consulta_grupo_exposicao_risco['GrupoExposicaoRisco']['codigo'];
						}

						$retorno_grupo_exposicao_risco = $this->GrupoExposicaoRisco->grupo_exposicao_risco_importacao($dados_grupo_exposicao_risco);
						$this->array_importacao_ppra[ $data['GrupoExposicao']['codigo_cliente_setor'] ][ $data['GrupoExposicao']['codigo_cargo'] ][ $retorno_grupo_exposicao_risco['Dados']['GrupoExposicaoRisco']['codigo_risco'] ] = $retorno_grupo_exposicao_risco['Dados']['GrupoExposicaoRisco'];
						$this->array_importacao_ppra[ $data['GrupoExposicao']['codigo_cliente_setor'] ][ $data['GrupoExposicao']['codigo_cargo'] ][ $retorno_grupo_exposicao_risco['Dados']['GrupoExposicaoRisco']['codigo_risco'] ]['importado'] = 1;

						if(isset($retorna_grupo_exposicao['Erro']) && !empty($retorna_grupo_exposicao['Erro'])){
							$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorno_grupo_exposicao_risco['Erro']);
						} else {
							$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorno_grupo_exposicao_risco['Dados']);
						}

					} else {
						$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], array('GrupoExposicaoRisco' => array('tempo_intensidade' => 'Resultante e Grau de Risco invalido!')));
					}
				} else {
					$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], array('GrupoExposicaoRisco' => $erros_grupos_exposicao_riscos));
				}
			} else {
				$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], array('Erro' => array('Risco' => array('codigo_risco' => utf8_decode('Risco nao encontrado!')))));
			}
		} else {
			$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], array('Erro' => array('Risco' => array('codigo_risco' => utf8_decode('Risco nao enviado!')))));
		}
		return $retorno;
	}

	function valida_tempo_intensidade($tempo, $intensidade, $resultante){
		if(!empty($tempo) && !empty($intensidade) && !empty($resultante)){
			switch ($intensidade) {
			case 4://INTENSIDADE BAIXA
				if($tempo == 1){//TEMPO PERMANENTE
					if($resultante == 9) //RESULTANTE DE ATENÇÃO
					return true;
					else
						return false;
				}
				elseif($tempo == 2 || $tempo = 3){//TEMPO INTERMITENTE OU //TEMPO OCASIONAL
					if($resultante == 8) //RESULTANTE IRRELEVANTE
					return true;
					else
						return false;
				}
				break;
			case 5://INTENSIDADE MEDIA
				if($tempo == 1 ||$tempo == 2 ){//TEMPO PERMANENTE OU TEMPO INTERMITENTE
					if($resultante == 9) //RESULTANTE DE ATENÇÃO
					return true;
					else
						return false;
				}
				elseif($tempo = 3){  //TEMPO OCASIONAL
					if($resultante == 8) //RESULTANTE IRRELEVANTE
					return true;
					else
						return false;
				}
				break;
			case 6://INTENSIDADE ALTA
				if($tempo == 1 ||$tempo == 2){//TEMPO PERMANENTE OU TEMPO INTERMITENTE
					if($resultante == 19)//RESULTANTE INCERTA
					return true;
					else
						return false;
				}
				elseif($tempo = 3){
					if($resultante == 9) //RESULTANTE DE ATENÇÃO
					return true;
					else
						return false;
				}
				break;
			case 7://INTENSIDADE MUITO ALTA
				if($resultante == 10) //RESULTANTE CRÍTICA
				return true;
				else
					return false;
				break;
			}
		}
		else{
			return true;
		}
	}

	function valida_dano_resultante($dano, $resultante, $grau_risco){
		if(!empty($dano) && !empty($resultante) && !empty($grau_risco)){
			switch ($resultante) {
				case 8:
				if($dano == 15){
					if($grau_risco == 22)
						return true;
					else
						return false;
				}
				elseif($dano == 13 || $dano = 14){
					if($grau_risco == 21)
						return true;
					else
						return false;
				}
				elseif($dano == 11 || $dano = 12){
					if($grau_risco == 20)
						return true;
					else
						return false;
				}
				break;
				case 9:
				if($dano == 14 ||$dano == 15 ){
					if($grau_risco == 22)
						return true;
					else
						return false;
				}
				elseif($dano == 12 ||$dano == 13){
					if($grau_risco == 21)
						return true;
					else
						return false;
				}
				elseif($dano == 11){
					if($grau_risco == 20)
						return true;
					else
						return false;
				}
				break;
				case 10:
				if($dano == 14 ||$dano == 15){
					if($grau_risco == 23)
						return true;
					else
						return false;
				}
				elseif($dano == 12 ||$dano == 13){
					if($grau_risco == 22)
						return true;
					else
						return false;
				}
				elseif($dano = 11){
					if($grau_risco == 21)
						return true;
					else
						return false;
				}
				break;
				case 19:
				if($dano == 15 ){
					if($grau_risco == 23)
						return true;
					else
						return false;
				}
				elseif($dano == 13 || $dano == 14 ){
					if($grau_risco == 22)
						return true;
					else
						return false;
				}
				elseif($dano == 11 || $dano == 12){
					if($grau_risco == 21)
						return true;
					else
						return false;
				}
				break;
			}
		}
		else{
			return true;
		}
	}

	function valida_fontes_geradoras($data, $linha){
		$retorno[$linha]['Dados'] = array();
		$retorno[$linha]['Erro'] = array();

		$codigo_grupo_exposicao_risco = $data['GrupoExposicaoRisco']['codigo'];
		if(!empty($data['DadoArquivo']['fonte_geradora'])){
			$dados_fontes = explode('|', $data['DadoArquivo']['fonte_geradora']);

			$retorno_grupo_exposicao_risco_fonte_geradora = $this->GrupoExpRiscoFonteGera->grupo_exposicao_risco_fonte_geradora_importacao($dados_fontes, $data);

			if(isset($retorno_grupo_exposicao_risco_fonte_geradora['Erro']) && !empty($retorno_grupo_exposicao_risco_fonte_geradora['Erro'])){
				$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorno_grupo_exposicao_risco_fonte_geradora['Erro']);
			}
			if(isset($retorno_grupo_exposicao_risco_fonte_geradora['Dados']) && !empty($retorno_grupo_exposicao_risco_fonte_geradora['Dados'])){
				$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorno_grupo_exposicao_risco_fonte_geradora['Dados']);
			}
		}
		else{
			$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorno_grupo_exposicao_risco_fonte_geradora['Dados']);
		}
		return $retorno;
	}

	function valida_epi($data, $linha){
		$retorno[$linha]['Dados'] = array();
		$retorno[$linha]['Erro'] = array();

		if(!empty($data['DadoArquivo']['epi'])){
			$retorno_grupo_exposicao_risco_epi = $this->GrupoExposicaoRiscoEpi->grupo_exposicao_risco_epi_importacao($data);

			if(isset($retorno_grupo_exposicao_risco_epi['Erro']) && !empty($retorno_grupo_exposicao_risco_epi['Erro'])){
				$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorno_grupo_exposicao_risco_epi['Erro']);
			}
			if(isset($retorno_grupo_exposicao_risco_epi['Dados']) && !empty($retorno_grupo_exposicao_risco_epi['Dados'])){
				$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorno_grupo_exposicao_risco_epi['Dados']);
			}
		}
		else{
			$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorno_grupo_exposicao_risco_epi['Dados']);
		}
		return $retorno;
	}

	function valida_epc($data, $linha){
		$retorno[$linha]['Dados'] = array();
		$retorno[$linha]['Erro'] = array();

		if(!empty($data['DadoArquivo']['epc'])){
			$retorno_grupo_exposicao_risco_epc = $this->GrupoExposicaoRiscoEpc->grupo_exposicao_risco_epc_importacao($data);

			if(isset($retorno_grupo_exposicao_risco_epc['Erro']) && !empty($retorno_grupo_exposicao_risco_epc['Erro'])){
				$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorno_grupo_exposicao_risco_epc['Erro']);
			}
			if(isset($retorno_grupo_exposicao_risco_epc['Dados']) && !empty($retorno_grupo_exposicao_risco_epc['Dados'])){
				$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorno_grupo_exposicao_risco_epc['Dados']);
			}
		}
		else{
			$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorno_grupo_exposicao_risco_epc['Dados']);
		}
		return $retorno;
	}


	function importar_pcmso($data){
		$this->Cliente =& ClassRegistry::Init('Cliente');
		$this->Setor =& ClassRegistry::Init('Setor');
		$this->Cargo =& ClassRegistry::Init('Cargo');
		$this->ClienteFuncionario =& ClassRegistry::Init('ClienteFuncionario');
		$this->Funcionario =& ClassRegistry::Init('Funcionario');
		$this->GrupoHomogeneo =& ClassRegistry::Init('GrupoHomogeneo');
		$this->Exame  =& ClassRegistry::Init('Exame');
		$this->AplicacaoExame  =& ClassRegistry::Init('AplicacaoExame');
		$this->ClienteImplantacao  =& ClassRegistry::Init('ClienteImplantacao');
		$this->Medico =& ClassRegistry::Init('Medico');
		$this->OrdemServico =& ClassRegistry::Init('OrdemServico');
		$this->Fornecedor =& ClassRegistry::Init('Fornecedor');
		$this->LogIntegracao =& ClassRegistry::Init('LogIntegracao');
		$this->AlertaHierarquiaPendente =& ClassRegistry::Init('AlertaHierarquiaPendente');
		$this->array_importacao_pcmso = array();

		$dados_retorno = array();
		$alertas_hierarquia = array();

		$destino = APP.'tmp'.DS.'importacao_pcmso'.DS;
		if(!is_dir($destino)){
			mkdir($destino, 0777, true);
		}

		$arquivo_erro = array();
		$arquivo_sucesso = array();

		$arquivo_destino = $destino.$data['Importar']['arquivo']['name'];

		if( move_uploaded_file($data['Importar']['arquivo']['tmp_name'], $arquivo_destino ) || ($this->useDbConfig == 'test_suite') ){
			$arquivo = fopen($arquivo_destino, "r");

			if ($arquivo) {
				$c = 0;
				$total = count(file($arquivo_destino));

				while (!feof($arquivo)) {
					
					$linha = trim(fgets($arquivo));
					// $linha = trim(str_replace('  ' , ' ', $linha));

					//Converte para UTF-8 se o arquivo está em ISO-8859-1
					if(mb_detect_encoding($linha, array('UTF-8','ISO-8859-1')) == 'ISO-8859-1' ){
						$linha = iconv('ISO-8859-1', 'UTF-8//TRANSLIT',$linha);
					}


					if( $c > 0 && $c < $total && !empty($linha)){
					// if( $c > 0 && $c < 5 && !empty($linha)){

						$data_arquivo = explode(';', $linha );

						$dados_arquivo['codigo_cliente_grupo_economico'] = $data['Importar']['codigo_cliente'];
						
						$dados_arquivo['codigo_externo'] = $data_arquivo[0];
						$dados_arquivo['setor_descricao'] = $data_arquivo[1];
						$dados_arquivo['cargo_descricao'] = $data_arquivo[2];
						$dados_arquivo['exame'] = $data_arquivo[3];
						$dados_arquivo['periodo_frequencia'] = $data_arquivo[4];
						$dados_arquivo['periodo_apos_admissao'] = $data_arquivo[5];
						$dados_arquivo['momento_exame'] = $data_arquivo[6];

						$dados_arquivo['idade'] = $data_arquivo[7];
						$dados_arquivo['tempo'] = $data_arquivo[8];

						$dados_arquivo['idade_2'] = $data_arquivo[9];
						$dados_arquivo['tempo_2'] = $data_arquivo[10];

						$dados_arquivo['idade_3'] = $data_arquivo[11];
						$dados_arquivo['tempo_3'] = $data_arquivo[12];

						$dados_arquivo['idade_4'] = $data_arquivo[13];
						$dados_arquivo['tempo_4'] = $data_arquivo[14];

						$dados_arquivo['objetivo'] = $data_arquivo[15];
						$dados_arquivo['tipo_exame'] = $data_arquivo[16];

						$dados_arquivo['documento_fornecedor'] = $data_arquivo[17];
						$dados_arquivo['cpf_funcionario'] = $data_arquivo[18];
						
						$dados['Dados']['DadoArquivo'] = $dados_arquivo;

						$codigo_unidade = $this->Cliente->localiza_cliente_importacao($dados['Dados']['DadoArquivo']);
						$codigo_unidade = $codigo_unidade['Dados']['Cliente']['codigo'];

						$codigo_servico_pcmso = $this->OrdemServico->getPCMSOByCodigoCliente($codigo_unidade);

						if(!isset($array_importacao_pcmso[$codigo_unidade])){
							$validacao_versao = $this->valida_versao_pcmso($codigo_unidade,$dados['Dados']['DadoArquivo']['documento_fornecedor'],$codigo_servico_pcmso);
							$array_importacao_pcmso[$codigo_unidade] = $validacao_versao;
						}

						if(!isset($array_importacao_pcmso[$codigo_unidade]['Erro'])){

							$this->query('begin transaction');

							$retorno_valida_dados_principais = $this->valida_dados_pcmso($dados['Dados']['DadoArquivo'], $c);

							if(empty($retorno_valida_dados_principais[$c]['Erro'])){
								$arquivo_sucesso['Sucesso'][$c]['dados'] = $dados['Dados']['DadoArquivo'];
								$this->commit();
								$dados_pcmso = Set::extract($retorno_valida_dados_principais,'{n}.Dados.AplicacaoExame');
								$cliente_pcmso = $dados_pcmso[0]['codigo_cliente_alocacao'];
								$setor_pcmso = $dados_pcmso[0]['codigo_setor'];
								$cargo_pcmso = $dados_pcmso[0]['codigo_cargo'];

								$array_alerta = array(
									'codigo_cliente_alocacao' => $cliente_pcmso,
									'codigo_setor' => $setor_pcmso,
									'codigo_cargo' => $cargo_pcmso
								);

								
								//Se o alerta de pendência concluída ainda não foi enviado para esta hierarquia
								if(array_search($array_alerta, $alertas_hierarquia) === false) {

									//Armazena a hierarquia no array de alertas enviados
									$alertas_hierarquia[] = $array_alerta;

									//verifica se existe alerta para esta hierarquia pendente e notifica clientes caso já exista o PPRA
									$this->AlertaHierarquiaPendente->envia_alerta_hierarquia($cliente_pcmso, $setor_pcmso, $cargo_pcmso,'PPRA');
								}

							} else {
								$arquivo_erro['Erro'][$c]['erros'] = $retorno_valida_dados_principais[$c]['Erro'];
								$arquivo_erro['Erro'][$c]['dados'] = $dados_arquivo;
								$this->rollback();
							}

						} else {
							$erro_secundario = false;
							//verifica se, dentro do array de erro, existe um erro de versão para determinada unidade
							if ( isset($arquivo_erro['Erro']) ){
								foreach ($arquivo_erro['Erro'] as $dados_erros) {
									if( isset($dados_erros['erros']['OrdemServico'][$codigo_unidade]) ){
										$erro_secundario = true;
										break;
									}
								}
							}
							//se já existir um erro de versão para a unidade, todos os registros dessa unidade serão invalidados pelo primeiro erro
							$arquivo_erro['Erro'][$c]['erros'] = ($erro_secundario ? array('OrdemServico' => array($codigo_unidade => 'Os registros dessa unidade foram invalidados, ja que nao foi possivel abrir uma nova versao.')) : $array_importacao_pcmso[$codigo_unidade]['Erro']);
							$arquivo_erro['Erro'][$c]['dados'] = $dados_arquivo;
						}

						$dados_retorno = array_merge($arquivo_erro, $arquivo_sucesso);
					}
					$c++;
				}
			}
		}

		$this->deleta_aplicacao_nao_importados();

		$dados_log = array(
			'LogIntegracao' => array(
				'codigo_cliente' => $data['Importar']['codigo_cliente'],
				'arquivo' => $data['Importar']['arquivo']['name'],
				'conteudo' => $this->monta_conteudo_log($arquivo_destino),
				'retorno' => $this->monta_retorno_log($dados_retorno),
				'sistema_origem' => 'IMPORTACAO_PCMSO',
				'status' => ( empty($dados_retorno['Erro']) ? '1' : '0' ),
				'descricao' => ( empty($dados_retorno['Erro']) ? 'SUCESSO' : 'REGISTROS COM ERROS DETECTADOS!' ),
				'tipo_operacao' => 'I',
				'data_arquivo' => date('Y-m-d H:i:s'),
			)
		);

		$this->LogIntegracao->incluir($dados_log);
		
		return $dados_retorno;			
	}

	function valida_dados_pcmso($data, $linha){
		$this->Cliente =& ClassRegistry::Init('Cliente');
		$retorno[$linha]['Dados'] = array();
		$retorno[$linha]['Erro'] = array();
		if(!empty($data['codigo_cliente_grupo_economico'])){

			// verifica se o cliente já está cadastrado na base
			$retorno_unidade = $this->Cliente->localiza_cliente_importacao($data);
			if(!isset($retorno_unidade['Erro']) && empty($retorno_unidade['Erro'])){

				$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorno_unidade['Dados']);

				// verifica se o setor está cadastrado na base, se não estiver, cadastra caso unidade se encontra desbloqueada
				$retorno_setor = $this->Setor->localiza_setor_importacao($data,$retorno_unidade['Dados']['GrupoEconomicoCliente']['bloqueado']);
				if(isset($retorno_setor['Erro']) && !empty($retorno_setor['Erro'])){
					$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorno_setor['Erro']);
				} else {
					$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorno_setor['Dados']);
				}
					
				// verifica se o cargo está cadastrado na base, se não estiver, cadastra caso unidade se encontra desbloqueada
				$retorno_cargo = $this->Cargo->localiza_cargo_importacao($data,$retorno_unidade['Dados']['GrupoEconomicoCliente']['bloqueado']);
				if(isset($retorno_cargo['Erro']) && !empty($retorno_cargo['Erro'])){
					$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorno_cargo['Erro']);
				} else {
					$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorno_cargo['Dados']);
				}

				// verifica se os dados para incluir hierarquia existem
				$this->GrupoEconomicoCliente = ClassRegistry::Init('GrupoEconomicoCliente');
				$conditions = array(
					'codigo' => $retorno_unidade['Dados']['GrupoEconomicoCliente']['codigo'],
					"EXISTS(SELECT TOP 1 codigo FROM setores WHERE codigo_cliente = ". $data['codigo_cliente_grupo_economico']." AND (descricao = '".$data['setor_descricao']."' OR descricao = '".Comum::trata_nome($data['setor_descricao'])."') AND ativo = 1)",
					"EXISTS(SELECT TOP 1 codigo FROM cargos WHERE codigo_cliente = ". $data['codigo_cliente_grupo_economico']." AND (descricao = '".$data['cargo_descricao']."' OR  descricao = '".Comum::trata_nome($data['cargo_descricao'])."') AND ativo = 1)"
				);
				$fields = array(
					'codigo','bloqueado'
				);
				$hierarquia = $this->GrupoEconomicoCliente->find('first', array('conditions' => $conditions,'fields' => $fields,'recursive' => -1));

				//Cria hierarquia para o novo setor e cargo
				if(!empty($hierarquia)){
					$dados_hierarquia = array(
						'codigo_cliente' => $retorno_unidade['Dados']['Cliente']['codigo'],
						'bloqueado' => $retorno_unidade['Dados']['GrupoEconomicoCliente']['bloqueado'],
						'codigo_cargo' => $retorno_cargo['Dados']['Cargo']['codigo'],
						'codigo_setor' => $retorno_setor['Dados']['Setor']['codigo']
					);
					$retorno_hierarquia = $this->incluiHierarquiaImportacao($dados_hierarquia);
					if(!empty($retorno_hierarquia)){
						if(isset($retorno_hierarquia['Erro']) && !empty($retorno_hierarquia['Erro'])){
							$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorno_hierarquia['Erro']);
						} else {
							$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorno_hierarquia['Dados']);
						}
					}
				}

				$codigo_funcionario = null;
				$retorno_funcionario['Erro'] = null;
				if(!empty($retorno_unidade['Dados']) && !empty($retorno_setor['Dados']) && !empty($retorno_cargo['Dados'])){

					//Se o cpf do funcionário foi enviado
					if(isset($data['cpf_funcionario']) && trim($data['cpf_funcionario']) !== ''){

						if(!(int)$data['cpf_funcionario']){
							//Erro de que não é inteiro
							//$retorno_funcionario['Erro']['cpf_funcionario'] = 'Erro: cpf deve conter somente números';
							$retorno_funcionario['Erro']['cpf'] = array('cpf' => utf8_decode('cpf deve conter somente números'));
						}	

						if(strlen($data['cpf_funcionario']) != 11 ){
							//Erro cpf não possui 11 dígitos
							//$retorno_funcionario['Erro']['cpf_funcionario'] = 'Erro: cpf não possui 11 dígitos';
							$retorno_funcionario['Erro']['cpf'] = array('cpf' => utf8_decode('cpf não possui 11 dígitos'));

						}
						
						if(empty($retorno_funcionario['Erro'])){
							//verifica se funcionário existe
	                   		$funcionario = $this->Funcionario->findByCpf($data['cpf_funcionario'],'Funcionario.codigo');
	                   	
		                   	if(empty($funcionario)){
								//Erro funcionário não foi encontrado
	               				//$retorno_funcionario['Erro']['cpf_funcionario'] = 'Erro: cpf não foi encontrado';
	               				$retorno_funcionario['Erro']['cpf'] = array('cpf' => utf8_decode('cpf não foi encontrado'));


		                   	}
	                   	}

	                   	if(!empty($funcionario)){

		                   	//Verifica se a matricula esta relacionada com a matriz do token
		                   	$cliente_funcionario = $this->ClienteFuncionario->find('first', array('conditions' => array('codigo_cliente_matricula' => $data['codigo_cliente_grupo_economico'], 'codigo_funcionario' => $funcionario['Funcionario']['codigo']),'recursive' => -1));

	               			if(empty($cliente_funcionario)){
	               				//$retorno_funcionario['Erro']['cpf_funcionario']  = 'Erro: matrícula do funcionário não corresponde a este grupo econômico';
	               				$retorno_funcionario['Erro']['cpf'] = array('cpf' => utf8_decode('matrícula do funcionário não corresponde a este grupo econômico'));

	               				
		                   	}

		                   	/*
		                  	//Se existe registro deste funcionário para esta função (unid + setor + cargo)
							$funcionario_setor_cargo = $this->FuncionarioSetorCargo->find('first',array('conditions' => array('codigo_cliente_alocacao' => $retorno_unidade['Dados']['Cliente']['codigo'],'codigo_setor' => $retorno_setor['Dados']['Setor']['codigo'], 'codigo_cargo' =>$retorno_cargo['Dados']['Cargo']['codigo'],'codigo_cliente_funcionario' => $cliente_funcionario['ClienteFuncionario']['codigo']),'recursive' => -1));


	               			if(empty($funcionario_setor_cargo)){
	               				$retorno_funcionario['Erro'] = 'Funcionário não possui registro desta unidade + setor + cargo';
	               			}*/
		                   	$codigo_funcionario = $funcionario['Funcionario']['codigo'];
	                   }
					}
				
				}	

				if(!empty($retorno_unidade['Dados']) && !empty($retorno_setor['Dados']) && !empty($retorno_cargo['Dados']) && empty($retorno_funcionario['Erro'])){

				    //ADICIONAR EXAME CLINICO
					$exame_clinico = array(
						'codigo_unidade' => $retorno_unidade['Dados']['Cliente']['codigo'], 
						'codigo_setor' => $retorno_setor['Dados']['Setor']['codigo'], 
						'codigo_cargo' => $retorno_cargo['Dados']['Cargo']['codigo'],
						'codigo_funcionario' =>  $codigo_funcionario,
						'descricao_exame' => $data['exame']
					);

					// $retorno_exame_clinico = $this->incluiExameClinicoImportacao($exame_clinico);

					// if(empty($retorno_exame_clinico)){
						if(!empty($data['exame'])){
							$retorna_exame = $this->Exame->retorna_exame_importacao($data);

							if(isset($retorna_exame['Erro']) && !empty($retorna_exame['Erro'])){
								$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorna_exame['Erro']);
							} else {
								$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorna_exame['Dados']);
								$momento_exame = explode('|', $data['momento_exame'] );

								if(!empty($momento_exame)){
									foreach ($momento_exame as $key => $momento) {
										switch ($momento) {
											case 'A':
												$exame_admissional  = 1;
												break;
											case 'P':
												$exame_periodico = 1;
												break;
											case 'D':
												$exame_demissional  = 1;
												break;
											case 'R':
												$exame_retorno = 1;
												break;
											case 'M':
												$exame_mudanca = 1;
												break;
											case 'Q':
												$qualidade_vida = 1;
												break;
											case 'T':
												$exame_monitoracao = 1;
												break;
										}
									}
								} else {
									$momento_exame ='';
									$exame_admissional ='';
									$exame_periodico ='';
									$exame_demissional ='';
									$exame_retorno ='';
									$exame_mudanca ='';
									$exame_monitoracao = '';
									$qualidade_vida = '';
								}

								$tipo_exame = explode('|', $data['tipo_exame'] );

								if(!empty($tipo_exame)){
									foreach ($tipo_exame as $key => $tipo) {
										switch ($tipo) {
											case 'CE':
												$exame_excluido_convocacao  = 1;
												break;
											case 'PP':
												$exame_excluido_ppp = 1;
												break;
											case 'AS':
												$exame_excluido_aso  = 1;
												break;
											case 'PC':
												$exame_excluido_pcmso = 1;
												break;
											case 'RA':
												$exame_excluido_anual = 1;
												break;
										}

									}
								} else {
									$exame_excluido_convocacao  = '';
									$exame_excluido_ppp = '';
									$exame_excluido_aso  = '';
									$exame_excluido_pcmso = '';
									$exame_excluido_anual = '';
								}

								$exame_excluido_convocacao  = (empty($exame_excluido_convocacao)? '': $exame_excluido_convocacao);
								$exame_excluido_ppp = (empty($exame_excluido_ppp)? '': $exame_excluido_ppp);
								$exame_excluido_aso  = (empty($exame_excluido_aso)? '': $exame_excluido_aso);
								$exame_excluido_pcmso = (empty($exame_excluido_pcmso)? '': $exame_excluido_pcmso);
								$exame_excluido_anual = (empty($exame_excluido_anual)? '': $exame_excluido_anual);

								if(!empty($data['objetivo'])) {

									if(strtoupper($data['objetivo']) == "O") {
										$codigo_tipo_exame = 1;
									} elseif (strtoupper($data['objetivo']) == "Q") {
										$codigo_tipo_exame = 2;
									}

									if ( !isset($this->array_importacao_pcmso[$retorno_unidade['Dados']['Cliente']['codigo']][$retorno_setor['Dados']['Setor']['codigo']][$retorno_cargo['Dados']['Cargo']['codigo']]) && empty($this->array_importacao_pcmso[$retorno_unidade['Dados']['Cliente']['codigo']][$retorno_setor['Dados']['Setor']['codigo']][$retorno_cargo['Dados']['Cargo']['codigo']]) ){
										$dados_cliente_setor_cargo = $this->AplicacaoExame->find('all',array('conditions' => array('codigo_cliente_alocacao' => $retorno_unidade['Dados']['Cliente']['codigo'],'codigo_setor' => $retorno_setor['Dados']['Setor']['codigo'], 'codigo_cargo' => $retorno_cargo['Dados']['Cargo']['codigo'])));
										foreach ($dados_cliente_setor_cargo as $dado) {
											$this->array_importacao_pcmso[ $retorno_unidade['Dados']['Cliente']['codigo'] ][ $retorno_setor['Dados']['Setor']['codigo'] ][ $retorno_cargo['Dados']['Cargo']['codigo'] ][ $dado['AplicacaoExame']['codigo_exame'] ] = $dado['AplicacaoExame'];
										}
									}

									$dados_aplicacao_exame = array(
										'AplicacaoExame' => array(
											'codigo_exame' => $retorna_exame['Dados']['Exame']['codigo'],
											'periodo_meses' => $data['periodo_frequencia'],
											'periodo_apos_demissao' => $data['periodo_apos_admissao'],
											'exame_admissional' => (!empty($exame_admissional))? $exame_admissional : '',
											'exame_periodico' => (!empty($exame_periodico))? $exame_periodico : '',
											'exame_demissional' => (!empty($exame_demissional))? $exame_demissional : '',
											'exame_retorno' => (!empty($exame_retorno))? $exame_retorno : '',
											'exame_mudanca' => (!empty($exame_mudanca))? $exame_mudanca : '',
											'exame_monitoracao' => (!empty($exame_monitoracao))? $exame_monitoracao : '',
											'periodo_idade' => $data['idade'],
											'qtd_periodo_idade' => $data['tempo'],
											'periodo_idade_2' => $data['idade_2'],
											'qtd_periodo_idade_2' => $data['tempo_2'],
											'periodo_idade_3' => $data['idade_3'],
											'qtd_periodo_idade_3' => $data['tempo_3'],
											'periodo_idade_4' => $data['idade_4'],
											'qtd_periodo_idade_4' => $data['tempo_4'],
											'exame_excluido_convocacao' => (!empty($exame_excluido_convocacao))? $exame_excluido_convocacao : '',
											'exame_excluido_ppp' => (!empty($exame_excluido_ppp))? $exame_excluido_ppp : '',
											'exame_excluido_aso' => (!empty($exame_excluido_aso))? $exame_excluido_aso : '',
											'exame_excluido_pcmso' => (!empty($exame_excluido_pcmso))? $exame_excluido_pcmso : '',
											'exame_excluido_anual' => (!empty($exame_excluido_anual))? $exame_excluido_anual : '',
											'ativo' => 1,
											'codigo_setor' => $retorno_setor['Dados']['Setor']['codigo'],
											'codigo_cargo' => $retorno_cargo['Dados']['Cargo']['codigo'],
											'codigo_cliente_alocacao' => $retorno_unidade['Dados']['Cliente']['codigo'],
											'codigo_funcionario' => $codigo_funcionario,
											'codigo_tipo_exame' => $codigo_tipo_exame,
										)
									);

									$retorno_aplicacao_exame = $this->AplicacaoExame->aplicacao_exame_importacao($dados_aplicacao_exame);
									$this->array_importacao_pcmso[ $retorno_unidade['Dados']['Cliente']['codigo'] ][ $retorno_setor['Dados']['Setor']['codigo'] ][ $retorno_cargo['Dados']['Cargo']['codigo'] ][ $retorna_exame['Dados']['Exame']['codigo'] ] = $retorno_aplicacao_exame['Dados']['AplicacaoExame'];
									$this->array_importacao_pcmso[ $retorno_unidade['Dados']['Cliente']['codigo'] ][ $retorno_setor['Dados']['Setor']['codigo'] ][ $retorno_cargo['Dados']['Cargo']['codigo'] ][ $retorna_exame['Dados']['Exame']['codigo'] ]['importado'] = 1;
									if(isset($retorno_aplicacao_exame['Erro']) && !empty($retorno_aplicacao_exame['Erro'])){
										$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorno_aplicacao_exame['Erro']);
									} else {
										$retorno[$linha]['Dados'] = array_merge($retorno[$linha]['Dados'], $retorno_aplicacao_exame['Dados']);
									}

								} else {
									$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], array('AplicacaoExame' => array('codigo_tipo_exame' => utf8_decode('Objetivo do Exame nao enviado!'))));
								}
							}
						} else {
							$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], array('AplicacaoExame' => array('codigo_exame' => utf8_decode('Exame nao enviado!'))));
						}
					// } else {
					// 	$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], array('Exame' => array('codigo_exame_clinico' => utf8_decode($retorno_exame_clinico))));
					// }
				} else {
					if(isset($retorno_unidade['Erro']) && !empty($retorno_unidade['Erro'])){
						$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorno_unidade['Erro']);
					}
					if(isset($retorno_setor['Erro']) && !empty($retorno_setor['Erro'])){
						$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorno_setor['Erro']);
					}
					if(isset($retorno_cargo['Erro']) && !empty($retorno_cargo['Erro'])){
						$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorno_cargo['Erro']);
					}					
					if(isset($retorno_funcionario['Erro']) && !empty($retorno_funcionario['Erro'])){
						$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorno_funcionario['Erro']);
					}
				}
			} else {
				$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], $retorno_unidade['Erro']);
			}

		} else {		
			$retorno[$linha]['Erro'] = array_merge($retorno[$linha]['Erro'], array('GrupoEconomico' => array('codigo_cliente_grupo_economico' => 'Código do Grupo Economico não enviado!')));
		}

		return $retorno;
	}

	function incluiExameClinicoImportacao($data){
		$this->Configuracao  =& ClassRegistry::Init('Configuracao');

		$codigo_cliente_alocacao  = $data['codigo_unidade'];
		$codigo_setor  = $data['codigo_setor'];
		$codigo_cargo = $data['codigo_cargo'];
		$descricao_exame = (empty($data['descricao_exame']))? '' :$data['descricao_exame'];

		$retorno = '';

		if(!empty($codigo_cliente_alocacao) && !empty($codigo_setor) && !empty($codigo_cargo)){ //ESTRUTURA CRIADA
			//	VERFICA NA TABELA DE CONFIGURACAO QUAL O CODIGO DO EXAME CLINICO
			$consulta_configuracao_exame = $this->Configuracao->find("first", array('conditions' => array('chave' => 'INSERE_EXAME_CLINICO')));
			
			if(!empty($consulta_configuracao_exame)){
				//ACHOU OS DADOS DE CONFIGURACAO, PROCURA O EXAME SE EXISTE NA BASE
				$consulta_exame = $this->Exame->find("first", array('conditions' => array('codigo' => $consulta_configuracao_exame['Configuracao']['valor'])));

				if(!empty($consulta_exame)){
					
					if($consulta_exame['Exame']['descricao'] != $descricao_exame){

						$consulta_aplicacao_exame = $this->AplicacaoExame->find('first', 
							array( 'conditions' => 
								array(
									'codigo_cliente_alocacao' 	=> $codigo_cliente_alocacao,
									'codigo_setor' 				=> $codigo_setor,
									'codigo_cargo' 				=> $codigo_cargo,
									'codigo_exame' 				=> $consulta_exame['Exame']['codigo'],
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
									'periodo_meses' => $consulta_exame['Exame']['periodo_meses'],
									'periodo_apos_demissao' => $consulta_exame['Exame']['periodo_apos_demissao'],
									'exame_admissional' => $consulta_exame['Exame']['exame_admissional'],
									'exame_periodico' => $consulta_exame['Exame']['exame_periodico'],
									'exame_demissional' => $consulta_exame['Exame']['exame_demissional'],
									'exame_retorno' => $consulta_exame['Exame']['exame_retorno'],
									'exame_mudanca' => $consulta_exame['Exame']['exame_mudanca'],
									'periodo_idade' => $consulta_exame['Exame']['periodo_idade'],
									'qtd_periodo_idade' => $consulta_exame['Exame']['qtd_periodo_idade'],
									'periodo_idade_2' => $consulta_exame['Exame']['periodo_idade_2'],
									'qtd_periodo_idade_2' => $consulta_exame['Exame']['qtd_periodo_idade_2'],
									'periodo_idade_3' => $consulta_exame['Exame']['periodo_idade_3'],
									'qtd_periodo_idade_3' => $consulta_exame['Exame']['qtd_periodo_idade_3'],
									'periodo_idade_4' => $consulta_exame['Exame']['periodo_idade_4'],
									'qtd_periodo_idade_4' => $consulta_exame['Exame']['qtd_periodo_idade_4'],
									'exame_excluido_convocacao' => $consulta_exame['Exame']['exame_excluido_convocacao'],
									'exame_excluido_ppp' => $consulta_exame['Exame']['exame_excluido_ppp'],
									'exame_excluido_aso' => $consulta_exame['Exame']['exame_excluido_aso'],
									'exame_excluido_pcmso' => $consulta_exame['Exame']['exame_excluido_pcmso'],
									'exame_excluido_anual' => $consulta_exame['Exame']['exame_excluido_anual'],
									'ativo' => 1,
									'codigo_tipo_exame' => 1
								)
							);

							$retorno_aplicacao_exame = $this->AplicacaoExame->aplicacao_exame_importacao($dados_aplicacao_exame);
							
							if(isset($retorno_aplicacao_exame['erro']) && !empty($retorno_aplicacao_exame['erro'])){
								$retorno = $retorno_aplicacao_exame['Erro'];
							}
						}///encontrou exame clinico aplicacao exame
						else{
							// $retorno['dados'] = array_merge($retorno['dados'], $retorno_aplicacao_exame['Dados']);
						}
					}//exame a inserir igual ao exame da confiuguracao
				}//encontrou o exame
				else{
					$retorno = utf8_decode('Exame nao encontrado!');
				}
			}//encontrou a confuiguiracao edo exame
			else{
				$retorno = utf8_decode('Exame Clinico nao encontrado!');
			}	
		}//cargo enviado
		else{
			$retorno = utf8_decode('Unidade/Setor/Cargo nao enviado corretamente!');
		}
	return $retorno;
	}

	function incluiHierarquiaImportacao($data){
		$this->ClienteSetorCargo =& ClassRegistry::init('ClienteSetorCargo');

		$retorno = array();
		if(!empty($data['codigo_cliente']) && !empty($data['codigo_cargo']) && !empty($data['codigo_setor'])){
			
			//Verifica se já existe esta hierarquia
			$existe_hierarquia = $this->ClienteSetorCargo->find('first', array('conditions' => 
				array('codigo_cliente' => $data['codigo_cliente'],
					'codigo_setor' => $data['codigo_setor'],
					'codigo_cargo' => $data['codigo_cargo'] ),
				'fields' => array('codigo'),
				'recursive' => -1)
			);
			//Se não existe hierarquia
			if(empty($existe_hierarquia)){
				if (!$data['bloqueado']) {

					$dados = array(
						'ClienteSetorCargo' => array(
							'codigo_cliente' => $data['codigo_cliente'],
							'codigo_cargo' =>  $data['codigo_cargo'],
							'codigo_setor' => $data['codigo_setor'],
							'codigo_cliente_alocacao' => $data['codigo_cliente']
						)
					);

					if(!$this->ClienteSetorCargo->incluir($dados)){
						$retorno['Erro'] = array('ClienteSetorCargo' => array('codigo' => utf8_decode('Nao foi possivel incluir uma nova hierarquia.')));
					}
				} else {
					$retorno['Erro'] = array('ClienteSetorCargo' => array('codigo' => utf8_decode('A unidade encontra-se bloqueada, logo nao foi possivel incluir a nova hierarquia.')));
				}
			}
		}

		return $retorno;
	}

	function valida_versao_ppra($data,$codigo_unidade,$codigo_servico){
		// na regra inicial, PCMSO também entraria nesse fluxo. Pelas modificações de status ( ao invez de finalizado, colocar como processando )
		// PCMSO não entrará mais no fluxo.

		$codigo_fornecedor_versao = $this->valida_codigo_fornecedor($data['documento_fornecedor'],$codigo_servico);

		$codigo_medico_versao = $this->valida_medico_versao($data['numero_conselho_medico_contrato'], $data['conselho_medico_contrato'],$data['uf_conselho_medico_contrato']);

		if( !empty($codigo_fornecedor_versao) && !empty($data['data_inicio_vigencia_contrato']) && ($data['vigencia_contrato'] == 3 || $data['vigencia_contrato'] == 6 || $data['vigencia_contrato'] == 9 || $data['vigencia_contrato'] == 12) && !empty($codigo_medico_versao) ){

			$conditions = array(
				'codigo_cliente' => $codigo_unidade,
				'OrdemServicoItem.codigo_servico' => $codigo_servico
			);

			$joins = array(
				array(
					'table' => 'ordem_servico_item',
					'alias' => 'OrdemServicoItem',
					'type' => 'INNER',
					'conditions' => array('OrdemServico.codigo = OrdemServicoItem.codigo_ordem_servico')
				)
			);

			$dadosOrdemServico = $this->OrdemServico->find('first', array('conditions' => $conditions, 'fields' => array('codigo'), 'joins' => $joins));
			// caso não exista ordem_servico, cria para depois atualizar

			$codigo_servico_ppra = $this->OrdemServico->getPPRAByCodigoCliente($codigo_unidade);

			if( empty($dadosOrdemServico) ){

				$dados['OrdemServico'] = array(
					'codigo_fornecedor' => $codigo_fornecedor_versao,
					'codigo_cliente' => $codigo_unidade,
					'var_aux' => 'ppra',
					'codigo_servico' => $codigo_servico
				);
				$this->ClienteImplantacao->enviar_ordem_servico($dados);
				$dadosOrdemServico = $this->OrdemServico->find('first', array('conditions' => $conditions, 'fields' => array('codigo'), 'joins' => $joins));
			}

			if( !empty($dadosOrdemServico) ){

				if($codigo_servico == $codigo_servico_ppra) {

					$retorno_status = $this->ClienteImplantacao->atualiza_status_ppra_concluido($codigo_unidade,'5',str_replace('/','-',$data['data_inicio_vigencia_contrato']),$data['vigencia_contrato'],$codigo_medico_versao,$codigo_fornecedor_versao);
					if( empty($retorno_status['Erro']) && !isset($retorno_status['Erro']) ){
						$retorno['Dados'] = 'Nova versao criada para a unidade '.$codigo_unidade;
					} else {
						$retorno['Erro'] = array('OrdemServico' => array($codigo_unidade => 'Ocorreu um erro na conclusao da versao da unidade '.$codigo_unidade.' - '. $retorno_status['Erro']));
					}

				} else {
					$retorno['Erro'] = array('OrdemServicoItem' => array('codigo_servico' => 'Servico invalido!'));
				}

			} else {
				$retorno['Erro']['OrdemServico'][$codigo_unidade] = 'Ocorreu um erro ao incluir Ordem de Servico.';
			}

		} else {

			$retorno['Erro']['OrdemServico'][$codigo_unidade] = 'Ocorreu um erro ao abrir a nova versão - ';

			if ( empty($codigo_fornecedor_versao) )
				$retorno['Erro']['OrdemServico'][$codigo_unidade] .= 'CNPJ do Fornecedor nao encontrado.';

			if ( empty($data['data_inicio_vigencia_contrato']) )
				$retorno['Erro']['OrdemServico'][$codigo_unidade] .= 'Data inicio vigencia invalida.';

			if ( empty($codigo_medico_versao) )
				$retorno['Erro']['OrdemServico'][$codigo_unidade] .= 'Numero do conselho do profissional nao encontrado.';

			if ( !($data['vigencia_contrato'] == 3 || $data['vigencia_contrato'] == 6 || $data['vigencia_contrato'] == 9 || $data['vigencia_contrato'] == 12) )
				$retorno['Erro']['OrdemServico'][$codigo_unidade] .= 'Duracao da vigencia invalida.';
		}

		return $retorno;
	}

	function valida_versao_pcmso($codigo_unidade,$documento_fornecedor,$codigo_servico){

		$codigo_fornecedor_versao = $this->valida_codigo_fornecedor($documento_fornecedor,$codigo_servico);

		if( !empty($codigo_fornecedor_versao) ){

			$conditions = array(
				'codigo_cliente' => $codigo_unidade,
				'OrdemServicoItem.codigo_servico' => $codigo_servico
			);

			$joins = array(
				array(
					'table' => 'ordem_servico_item',
					'alias' => 'OrdemServicoItem',
					'type' => 'INNER',
					'conditions' => array('OrdemServico.codigo = OrdemServicoItem.codigo_ordem_servico')
				)
			);

			$dadosOrdemServico = $this->OrdemServico->find('first', array('conditions' => $conditions, 'fields' => array('codigo'), 'joins' => $joins));

			// caso não exista ordem_servico, cria para depois atualizar

			if( empty($dadosOrdemServico) ){

				$dados['OrdemServico'] = array(
					'codigo_fornecedor' => $codigo_fornecedor_versao,
					'codigo_cliente' => $codigo_unidade,
					'var_aux' => 'pcmso',
					'codigo_servico' => $codigo_servico
				);
				$this->ClienteImplantacao->enviar_ordem_servico($dados);
				$dadosOrdemServico = $this->OrdemServico->find('first', array('conditions' => $conditions, 'fields' => array('codigo'), 'joins' => $joins));
			}

			$codigo_servico_pcmso = $this->OrdemServico->getPCMSOByCodigoCliente($codigo_unidade);

			if( !empty($dadosOrdemServico) ){

				if($codigo_servico == $codigo_servico_pcmso) {

					if($this->AplicacaoExame->concluir($codigo_unidade)){
						$retorno['Dados'] = 'Nova versao criada para a unidade '.$codigo_unidade;
					} else {
						$retorno['Erro'] = array('OrdemServico' => array($codigo_unidade => 'Ocorreu um erro na atualização da Ordem de Serviço da unidade '.$codigo_unidade));
					}

				} else {
					$retorno['Erro'] = array('OrdemServicoItem' => array('codigo_servico' => 'Servico invalido!'));
				}

			} else {
				$retorno['Erro']['OrdemServico'][$codigo_unidade] = 'Ocorreu um erro ao incluir Ordem de Servico.';
			}

		} else {
			$retorno['Erro']['OrdemServico'][$codigo_unidade] = 'Ocorreu um erro ao abrir a nova versão - CNPJ do Fornecedor nao encontrado.';
		}

		return $retorno;
	}

	function valida_medico_versao($numero_conselho,$conselho,$uf_conselho){
		// se o numero_conselho do medico responsável foi enviado, verifica se o código do mesmo é valido ( versao )
        if( !empty($numero_conselho) ){
        	
        	$conditions = array(
        		'Medico.ativo' => 1,
        		'ConselhoProfissional.descricao' => $conselho,
        		'Medico.conselho_uf' => $uf_conselho,
        		'Medico.numero_conselho like' => $numero_conselho
        	);

        	$joins = array(
        		array(
					'table' => 'conselho_profissional',
					'alias' => 'ConselhoProfissional',
					'type' => 'INNER',
					'conditions' => array(
						'ConselhoProfissional.codigo = Medico.codigo_conselho_profissional AND (ConselhoProfissional.descricao LIKE \'crea\' OR ConselhoProfissional.descricao LIKE \'mte\')'
					)
				)
			);

        	$codigo_medico_contrato = $this->Medico->find('first',array('conditions' => $conditions,'joins' => $joins,'recursive' => -1));

        	if($codigo_medico_contrato){
        		return $codigo_medico_contrato['Medico']['codigo'];
        	} else {
        		return false;
        	}

        }
        return NULL;
	}

	function monta_conteudo_log($destino_arquivo){
		$array_registros = file($destino_arquivo);
		$conteudo = implode('|',$array_registros);
		return $conteudo;
	}

	function monta_retorno_log($retorno) {
		if( !empty($retorno['Erro']) ){
			$retorno_log = 'Planilha com erros! ';
			foreach ($retorno['Erro'] as $linha => $erros) {
				$retorno_log .= 'Erros da linha '.$linha.': [ ';
				foreach(array_values($erros['erros']) as $model){
					foreach($model as $string){
						$retorno_log .= $string.' ';
					}
				}
				$retorno_log .= '] ';
			}
		} else {
			$retorno_log = 'Planilha sem erros.';
		}
		return $retorno_log;
	}

	function retorna_funcionario($cpf_funcionario){
		$codigo_funcionario = $this->Funcionario->find('first',array('conditions' => array('CPF' => $cpf_funcionario),'recursive' => -1));
		if ( empty($codigo_funcionario) ) {
			return false;
		} else {
			return $codigo_funcionario['Funcionario']['codigo'];
		}
	}

	function valida_codigo_fornecedor($documento_fornecedor,$codigo_servico){
		if ( !empty($documento_fornecedor) ){

	    	$conditions = array(
	    		'Fornecedor.codigo_documento' => str_replace(array('.', '/', '-'), '', $documento_fornecedor),
	    		'OR' => array(
					array('Fornecedor.interno' => 1,'ListaDePrecoProdutoServico.codigo_servico' => $codigo_servico),
					array('Fornecedor.ativo' => 1,'ListaDePrecoProdutoServico.codigo_servico' => $codigo_servico)
				)
	    	);

	    	$joins = array(
	    		array(
	    			'table' => 'listas_de_preco',
	    			'alias' => 'ListaDePreco',
	    			'type' => 'INNER',
	    			'conditions' => 'ListaDePreco.codigo_fornecedor = Fornecedor.codigo'
	    		),
	    		array(
	    			'table' => 'listas_de_preco_produto',
	    			'alias' => 'ListaDePrecoProduto',
	    			'type' => 'LEFT',
	    			'conditions' => 'ListaDePrecoProduto.codigo_lista_de_preco = ListaDePreco.codigo'
	    		),
	    		array(
	    			'table' => 'listas_de_preco_produto_servico',
	    			'alias' => 'ListaDePrecoProdutoServico',
	    			'type' => 'LEFT',
	    			'conditions' => 'ListaDePrecoProdutoServico.codigo_lista_de_preco_produto = ListaDePrecoProduto.codigo'
	    		)
	    	);

	    	$valida_fornecedor = $this->Fornecedor->find('first',array('conditions' => $conditions,'joins' => $joins,'recursive' => -1));

	    	if ($valida_fornecedor){
	    		return $valida_fornecedor['Fornecedor']['codigo'];
	    	} else {
	    		return false;
	    	}

	    } else {
	    	// nao passou documento do fornecedor na planilha
	    	return false;
	    }
	}

	/*
	 *
	 * função deleta_aplicacao_nao_importados - varre o array de grupo_exposicao_risco montado, verifica quais foram importados (incluídos ou editados) e deleta o resto.
	 * modificação : deletar as ligações.
	 *
	 */
	function deleta_riscos_nao_importados(){
		foreach ($this->array_importacao_ppra as $cliente_setor) {
			foreach ($cliente_setor as $cargo) {
				foreach ($cargo as $risco) {
					if( !isset($risco['importado']) && empty($risco['importado']) ){
						$this->deleta_parents_risco($risco['codigo']);
						$this->GrupoExposicaoRisco->delete($risco['codigo']);
					}
				}
			}
		}
	}

	function deleta_parents_risco($codigo_risco){
		$this->GrupoExpRiscoAtribDet = ClassRegistry::Init('GrupoExpRiscoAtribDet');
		$this->GrupoExposicaoRiscoEpi = ClassRegistry::Init('GrupoExposicaoRiscoEpi');
		$this->GrupoExposicaoRiscoEpc = ClassRegistry::Init('GrupoExposicaoRiscoEpc');
		$this->GrupoExpRiscoFonteGera = ClassRegistry::Init('GrupoExpRiscoFonteGera');
		$dados_excluir = array();

		$deleteGrupoExpRiscoAtribDet = $this->GrupoExpRiscoAtribDet->find('all',array('conditions' => array('codigo_grupos_exposicao_risco' => $codigo_risco),'fields' => array('codigo'),'recursive' => -1));
		$deleteGrupoExposicaoRiscoEpi = $this->GrupoExposicaoRiscoEpi->find('all',array('conditions' => array('codigo_grupos_exposicao_risco' => $codigo_risco),'fields' => array('codigo'),'recursive' => -1));
		$deleteGrupoExposicaoRiscoEpc = $this->GrupoExposicaoRiscoEpc->find('all',array('conditions' => array('codigo_grupos_exposicao_risco' => $codigo_risco),'fields' => array('codigo'),'recursive' => -1));
		$deleteGrupoExpRiscoFonteGera = $this->GrupoExpRiscoFonteGera->find('all',array('conditions' => array('codigo_grupos_exposicao_risco' => $codigo_risco),'fields' => array('codigo'),'recursive' => -1));

		if( !empty($deleteGrupoExpRiscoAtribDet) ){
			foreach ($deleteGrupoExpRiscoAtribDet AS $dados_delete) {
				$this->GrupoExpRiscoAtribDet->delete($dados_delete['GrupoExpRiscoAtribDet']['codigo']);
			}			
		}

		if( !empty($deleteGrupoExposicaoRiscoEpi) ){
			foreach ($deleteGrupoExposicaoRiscoEpi AS $dados_delete) {
				$this->GrupoExposicaoRiscoEpi->delete($dados_delete['GrupoExposicaoRiscoEpi']['codigo']);
			}			
		}

		if( !empty($deleteGrupoExposicaoRiscoEpc) ){
			foreach ($deleteGrupoExposicaoRiscoEpc AS $dados_delete) {
				$this->GrupoExposicaoRiscoEpc->delete($dados_delete['GrupoExposicaoRiscoEpc']['codigo']);
			}			
		}

		if( !empty($deleteGrupoExpRiscoFonteGera) ){
			foreach ($deleteGrupoExpRiscoFonteGera AS $dados_delete) {
				$this->GrupoExpRiscoFonteGera->delete($dados_delete['GrupoExpRiscoFonteGera']['codigo']);
			}
		}
	}

	/*
	 *
	 * função deleta_aplicacao_nao_importados - varre o array de aplicacao_exame montado, verifica quais foram importados (incluídos ou editados) e deleta o resto.
	 *
	 */
	function deleta_aplicacao_nao_importados(){
		$Configuracao = &ClassRegistry::init('Configuracao');
		foreach ($this->array_importacao_pcmso as $cliente_alocacao) {
			foreach ($cliente_alocacao as $setor) {
				foreach ($setor as $cargo) {
					foreach ($cargo as $exame) {
						if( ( !isset($exame['importado']) && empty($exame['importado']) ) && $exame['codigo_exame'] != $Configuracao->getChave('INSERE_EXAME_CLINICO') ){
							$this->AplicacaoExame->delete($exame['codigo']);
						}
					}
				}
			}
		}
	}


	/**
	 * [importar_usuario_unidade description]
	 * 
	 * metodo para gravar no banco de dados de importacao os dados
	 * 
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function importar_usuario_unidade($data){
		
		$this->Cliente =& ClassRegistry::Init('Cliente');
		$this->Usuario =& ClassRegistry::Init('Usuario');
		$this->UsuarioUnidade =& ClassRegistry::Init('UsuarioUnidade');
		$this->GrupoEconomico =& ClassRegistry::Init('GrupoEconomico');
		$this->GrupoEconomicoCliente =& ClassRegistry::Init('GrupoEconomicoCliente');
		$this->LogIntegracao =& ClassRegistry::Init('LogIntegracao');

		// $this->array_importacao_usuario_unidade = array();

		$dados_retorno = array();
		$alertas_hierarquia = array();

		$destino = APP.'tmp'.DS.'importacao_usuario_unidade'.DS;
		if(!is_dir($destino)){
			mkdir($destino, 0777, true);
		}

		$arquivo_erro = array();
		$arquivo_sucesso = array();

		$arquivo_destino = $destino.$data['Importar']['arquivo']['name'];

		//pega o codigo do cliente matriz
		$codigo_cliente_matriz = $data['Importar']['codigo_cliente'];

		//pega os dados do grupo economico
		$grupo_economico = $this->GrupoEconomico->find('first',array('conditions' => array('GrupoEconomico.codigo_cliente' => $codigo_cliente_matriz)));

		//move o arquivo para o diretorio setado
		if( move_uploaded_file($data['Importar']['arquivo']['tmp_name'], $arquivo_destino ) || ($this->useDbConfig == 'test_suite') ){
			
			$arquivo = fopen($arquivo_destino, "r");
			//verifica se existe o arquivo
			if ($arquivo) {
				$c = 0;
				$total = count(file($arquivo_destino));

				$var_aux = array();


				//varre as linhas do arquivo
				while (!feof($arquivo)) {
					//pega a linha do arquivo
					$linha = trim(fgets($arquivo));
					// $linha = trim(str_replace('  ' , ' ', $linha));

					//verifica as linhas do arquivo
					if( $c > 0 && $c < $total && !empty($linha)){
					
						//seta os dados do arquivo
						$data_arquivo = explode(';', $linha );

						//pega os dados e separa eles
						$dados_arquivo['login'] 		= trim($data_arquivo[0]);
						$dados_arquivo['cnpj_unidade'] 	= trim($data_arquivo[1]);

						//pega o login do usuario
						$usuario = $this->Usuario->find('first', array('fields' => array('Usuario.codigo'),'conditions' => array('apelido' => $dados_arquivo['login'])));
						$codigo_usuario = $usuario['Usuario']['codigo'];

						if(!isset($var_aux[$codigo_usuario])) {
							$var_aux[$codigo_usuario] = true;
						}

						// $this->query('begin transaction');

						//verifica se existe este usuario na base de dados
						if(!empty($codigo_usuario)) {

							//verifica se irá deletar os dados ou nao
							if($var_aux[$codigo_usuario]) {

								$var_aux[$codigo_usuario] = false;

								//verifica se existe relacionamentos com usuario
								$usuario_unidades = $this->UsuarioUnidade->find('first', array('conditions' => array('codigo_usuario' => $codigo_usuario)));

								//verifica se existe relacionamentos
								if(!empty($usuario_unidades)) {
									//deleta os relacionamentos do usuario com as unidades
									$query_delete = "DELETE FROM RHHealth.dbo.usuario_unidades WHERE codigo_usuario = " . $codigo_usuario;
									if(!$this->UsuarioUnidade->query($query_delete)) {
										//tratamento de erros									
										$arquivo_erro['Erro'][$c]['erros']['UsuarioUnidade'][] = "Não foi possivel excluir os relacionamentos das Unidades já existentes na Base de Dados!";
										$arquivo_erro['Erro'][$c]['dados'] = $dados_arquivo;
									}//fim delete registros
								}//fim verificacao de relacionamentos de usuario_unidade

							}//fim var_aux
							
							//pega o codigo da unidade (cliente)
							$cliente = $this->Cliente->find('first', array('conditions'=>array('Cliente.codigo_documento' => $dados_arquivo['cnpj_unidade'])));

							//verifica se o cliente é do mesmo grupo economico
							$grupo_economico_cliente = $this->GrupoEconomicoCliente->find('first', array('conditions'=>array('GrupoEconomicoCliente.codigo_cliente' => $cliente['Cliente']['codigo'])));
							
							//verifica se é do mesmo grupo economico o cnpj passado
							if($grupo_economico['GrupoEconomico']['codigo'] == $grupo_economico_cliente['GrupoEconomicoCliente']['codigo_grupo_economico']) {

								//monta o insert na tabela
								$dados_unidades = array(
									'codigo_usuario' => $codigo_usuario,
									'codigo_cliente' => $cliente['Cliente']['codigo']
								);

								if(!$this->UsuarioUnidade->incluir($dados_unidades)) {
									$arquivo_erro['Erro'][$c]['erros']['UsuarioUnidade'][] = "Erro ao relacionar o Usuario a Unidade!";
									$arquivo_erro['Erro'][$c]['dados'] = $dados_arquivo;
								}

							}//fim codigo grupo economico
							else {
								$arquivo_erro['Erro'][$c]['erros']['UsuarioUnidade'][] = "CNPJ passado na planilha de importação não corresponde ao grupo economico do mesmo!";
								$arquivo_erro['Erro'][$c]['dados'] = $dados_arquivo;
							}//fim codigo grupo
						}
						else {
							$arquivo_erro['Erro'][$c]['erros']['UsuarioUnidade'][] = "Login do Usuario não foi encontrado em nossa base de dados!";
							$arquivo_erro['Erro'][$c]['dados'] = $dados_arquivo;
						}//fim codigo usuario

						//verifica se existe erros 
						if(!empty($arquivo_erro)) {
						
							//desfaz os deletes e inserts
							// $this->rollback();

						}//fim verificacao arquivo erro
						else {
						
							$arquivo_sucesso['Sucesso'][$c]['dados'] = $dados_arquivo;
							// $this->commit();
						
						}//fim else de sucesso
						
						//junta os dois arrays para deixar unificado
						$dados_retorno = array_merge($arquivo_erro, $arquivo_sucesso);

					}//fim verificacao quantidade linhas

					$c++;

				}//fim while do arquivo
			}//fim if arquivo
		}//fim name move_uploaded_file($data['Importar']['arquivo']['tmp_name'], $arquivo_destino ) || ($this->useDbConfig == 'test_suite')

		// pr($dados_retorno);exit;

		$dados_log = array(
			'LogIntegracao' => array(
				'codigo_cliente' => $data['Importar']['codigo_cliente'],
				'arquivo' => $data['Importar']['arquivo']['name'],
				'conteudo' => $this->monta_conteudo_log($arquivo_destino),
				'retorno' => $this->monta_retorno_log($dados_retorno),
				'sistema_origem' => 'IMPORTACAO_USUARIO_UNIDADE',
				'status' => ( empty($dados_retorno['Erro']) ? '1' : '0' ),
				'descricao' => ( empty($dados_retorno['Erro']) ? 'SUCESSO' : 'REGISTROS COM ERROS DETECTADOS!' ),
				'tipo_operacao' => 'I',
				'data_arquivo' => date('Y-m-d H:i:s'),
			)
		);

		$this->LogIntegracao->incluir($dados_log);
		
		return $dados_retorno;			
	}//fim importar_usuario_unidade

	/**
	 * [importar_usuario description]
	 * 
	 * metodo para gravar no banco de dados de importacao os dados
	 * 
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function importar_usuario($codigo_cliente,$data)
	{
		
		$this->Cliente =& ClassRegistry::Init('Cliente');
		$this->Usuario =& ClassRegistry::Init('Usuario');
		$this->Uperfil =& ClassRegistry::Init('Uperfil');
		$this->LogIntegracao =& ClassRegistry::Init('LogIntegracao');

		// $this->array_importacao_usuario_unidade = array();

		$dados_retorno = array();
		$alertas_hierarquia = array();

		$destino = APP.'tmp'.DS.'importacao_usuario'.DS;
		if(!is_dir($destino)){
			mkdir($destino, 0777, true);
		}

		$arquivo_erro = array();
		$arquivo_sucesso = array();

		$arquivo_destino = $destino.$data['Importar']['arquivo']['name'];

		//pega os dados do grupo economico
		$cliente = $this->Cliente->find('first',array('conditions' => array('Cliente.codigo' => $codigo_cliente)));

		//move o arquivo para o diretorio setado
		if( move_uploaded_file($data['Importar']['arquivo']['tmp_name'], $arquivo_destino ) || ($this->useDbConfig == 'test_suite') ){
			
			$arquivo = fopen($arquivo_destino, "r");
			//verifica se existe o arquivo
			if ($arquivo) {
				$c = 0;
				$total = count(file($arquivo_destino));

				$var_aux = array();

				//varre as linhas do arquivo
				while (!feof($arquivo)) {
					//pega a linha do arquivo
					$linha = trim(fgets($arquivo));
					// $linha = trim(str_replace('  ' , ' ', $linha));

					//verifica as linhas do arquivo
					if( $c > 0 && $c < $total && !empty($linha)){
					
						//seta os dados do arquivo
						$data_arquivo = explode(';', $linha );

						//pega os dados e separa eles
						$dados_arquivo['login'] 		= trim($data_arquivo[0]);
						$dados_arquivo['nome'] 			= trim($data_arquivo[1]);
						$dados_arquivo['perfil']		= trim($data_arquivo[2]);
						$dados_arquivo['email']			= trim($data_arquivo[3]);
						$dados_arquivo['status']		= trim($data_arquivo[4]);

						//valida o status do usuario passado
						if($dados_arquivo['status'] == "A") {
							$dados_status = '1';
						}
						else if($dados_arquivo['status'] == "I") {
							$dados_status = '0';
						}
						else {
							$arquivo_erro['Erro'][$c]['erros']['Usuario'][] = "Status com valor não aceito, favor corrigir colocando A->Ativo, I->Inativo!";
							$arquivo_erro['Erro'][$c]['dados'] = $dados_arquivo;

							$c++;

							continue;

						}//fim validacao status

						//busca o perfil passado
						$perfil = $this->Uperfil->find('first', array('conditions' => array('Uperfil.descricao' => $dados_arquivo['perfil'])));
						
						//verifica se encontrou o perfil enviado na planilha para cadastro/edição do usuario
						if(empty($perfil)) {
							
							$arquivo_erro['Erro'][$c]['erros']['Usuario'][] = "Perfil enviado na planilha de importação não foi encontrado!";
							$arquivo_erro['Erro'][$c]['dados'] = $dados_arquivo;

							$c++;

							//junta os dois arrays para deixar unificado
							$dados_retorno = array_merge($arquivo_erro, $arquivo_sucesso);

							continue;
						}//fim validacao perfil

						//pega o login do usuario
						$usuario = $this->Usuario->find('first', array('fields' => array('Usuario.codigo', 'Usuario.codigo_cliente'),'conditions' => array('apelido' => $dados_arquivo['login'])));
						$codigo_usuario = $usuario['Usuario']['codigo'];

						//verifica se existe este usuario na base de dados para alteração
						if(!empty($codigo_usuario)) {

							//verifica se o usuario existente está em outra empresa
							if($usuario['Usuario']['codigo_cliente'] != $codigo_cliente) {
								//mensagem de erro
								$arquivo_erro['Erro'][$c]['erros']['Usuario'][] = "Erro ao incluir usuário: login já existe";
								$arquivo_erro['Erro'][$c]['dados'] = $dados_arquivo;

								$c++;

								//junta os dois arrays para deixar unificado
								$dados_retorno = array_merge($arquivo_erro, $arquivo_sucesso);

								continue;
							}//fim usuario codigo_cliente

							//monta o array com os dados para cadastrar o usuario
							$dados_usuario = array(
								'Usuario' => array(
									"codigo"				=> $codigo_usuario, 
									"nome" 					=> $dados_arquivo['nome'],
									"codigo_uperfil" 		=> $perfil['Uperfil']['codigo'],
									"email" 				=> $dados_arquivo['email'],
									"ativo"					=> $dados_status,
								)
							);
							
							//verifica se incluiu corretamente o usuário na base de dados
							if(!$this->Usuario->atualizar($dados_usuario)){

								//variavel de eros
								$arquivo_erro['Erro'][$c]['erros']['Usuario'][] = "Usuario ao editar usuario!";
								$arquivo_erro['Erro'][$c]['dados'] = $dados_arquivo;

								// throw new Exception();

							}//fim atualizar usuario
							else {
								$arquivo_sucesso['Sucesso'][$c]['dados'] = $dados_arquivo;
							}

						}
						else { //para inclusao								

							$senha = rand('100000', '999999');

							//monta o array com os dados para cadastrar o usuario
							$dados_usuario = array(
								'Usuario' => array(
									// [codigo] => 
									"codigo_documento" 		=> $cliente['Cliente']['codigo_documento'], //cnpj do cliente
									"codigo_departamento" 	=> 4,
									"codigo_cliente" 		=> $codigo_cliente,
									"apelido" 				=> $dados_arquivo['login'],
									"nome" 					=> $dados_arquivo['nome'],
									"senha" 				=> "{$senha}",
									"codigo_uperfil" 		=> $perfil['Uperfil']['codigo'],
									"token" 				=> $this->Usuario->gerarToken(),
									"email" 				=> $dados_arquivo['email'],
									"ativo"					=> $dados_status,
									"admin" 				=> 0,
									"restringe_base_cnpj" 	=> 0,										
									"alerta_portal" 		=> 0,
									"alerta_email" 			=> 0,
									"alerta_sms" 			=> 0,
								)
							);
							
							//verifica se incluiu corretamente o usuário na base de dados
							if(!$this->Usuario->incluir($dados_usuario)) {

								//variavel de eros
								$arquivo_erro['Erro'][$c]['erros']['Usuario'][] = "Usuario ao incluir usuario!";
								$arquivo_erro['Erro'][$c]['dados'] = $dados_arquivo;

								// throw new Exception();
							}
							else{
								$arquivo_sucesso['Sucesso'][$c]['dados'] = $dados_arquivo;

								//enviar login e senha para o novo usuário
								$codigo_usuario = $this->Usuario->getLastInsertId();
								$this->envia_acesso_cliente($codigo_usuario);

							}//fim inclusao usuario

						}//fim codigo usuario
						
					}//fim verificacao quantidade linhas

					$c++;

				}//fim while do arquivo

				//junta os dois arrays para deixar unificado
				$dados_retorno = array_merge($arquivo_erro, $arquivo_sucesso);

			}//fim if arquivo
		}//fim name move_uploaded_file($data['Importar']['arquivo']['tmp_name'], $arquivo_destino ) || ($this->useDbConfig == 'test_suite')

		// pr($dados_retorno);exit;

		$dados_log = array(
			'LogIntegracao' => array(
				'codigo_cliente' => $data['Importar']['codigo_cliente'],
				'arquivo' => $data['Importar']['arquivo']['name'],
				'conteudo' => $this->monta_conteudo_log($arquivo_destino),
				'retorno' => $this->monta_retorno_log($dados_retorno),
				'sistema_origem' => 'IMPORTACAO_USUARIO',
				'status' => ( empty($dados_retorno['Erro']) ? '1' : '0' ),
				'descricao' => ( empty($dados_retorno['Erro']) ? 'SUCESSO' : 'REGISTROS COM ERROS DETECTADOS!' ),
				'tipo_operacao' => 'I',
				'data_arquivo' => date('Y-m-d H:i:s'),
			)
		);

		$this->LogIntegracao->incluir($dados_log);
		
		return $dados_retorno;			
	}//fim importar usuario


	/**
	 * [envia_acesso_cliente description]
	 * 
	 * metodo para enviar acesso ao cliente
	 * 
	 * @param  [type] $codigo_usuario [description]
	 * @return [type]                 [description]
	 */
	public function envia_acesso_cliente($codigo_usuario) 
	{
		require_once(ROOT . DS . 'app' . DS . 'vendors' . DS . 'buonny' . DS . 'encriptacao.php');
		$Encriptador = new Buonny_Encriptacao();

		$dados = $this->Usuario->find('first', array('fields' => array('senha','email','apelido'),'conditions' => array('Usuario.codigo' => $codigo_usuario)));
		
		if (!$dados){
			return false;
		}

		$senha = $Encriptador->desencriptar($dados['Usuario']['senha']);
		$nome_usuario = $dados['Usuario']['apelido'];
		$mensagens = array('Senha: '.$senha);

		App::import('Component', array('StringView', 'Mailer.Scheduler'));
		$this->StringView = new StringViewComponent();
		$this->Scheduler = new SchedulerComponent();

		$this->StringView->set(compact('nome_usuario','mensagens', 'cliente','dados'));

		$content = $this->StringView->renderMail('envio_senha_email', 'default');

		$options = array(
			'from' => 'portal@rhhealth.com.br',
			'sent' => null,
			'to' => $dados['Usuario']['email'],
			'subject' => 'Sua Senha de Acesso ao Sistema!',
			);
		if($this->Scheduler->schedule($content, $options)) {
			return true;
		} else {
			return false;
		}
		
	}//fim envia_acesso_clientes

}

?>