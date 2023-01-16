<?php
class Fornecedor extends AppModel {
	public $name = 'Fornecedor';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'fornecedores';
	public $primaryKey = 'codigo';
	public $displayField = 'nome';
	public $actsAs = array('Secure', 'SincronizarCodigoDocumento', 'Loggable' => array('foreign_key' => 'codigo_fornecedor'), 'Containable');

	public $with_transaction = true;
	//public $recursive = -1;

	public $hasMany = array(
		'ItemPedidoExame' => array(
			'className'    => 'ItemPedidoExame',
			'foreignKey'    => 'codigo_fornecedor'
			),
		);

	public $hasAndBelongsToMany = array(
		'Medico' => array(
			'className'    				=> 'Medico',
			'joinTable'    				=> 'fornecedores_medicos',
			'foreignKey'             	=> 'codigo_fornecedor',
			'associationForeignKey'  	=> 'codigo_medico',
			),
		);

	public $validate = array(
		'codigo_documento' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o CNPJ!',
				),
			'documentoValido' => array(
				'rule' => 'documentoValido',
				'message' => 'CNPJ inválido, verifique!',
				'on' => 'create'
				),
			'documentoJaExiste' => array(
				'rule' => 'documentoJaExiste',
				'message' => 'CNPJ já existente na base',
				'on' => 'create'
				),
			),
		'razao_social' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Razão Social!'
			),	
		'nome' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Nome Fantasia!'
			),
		'responsavel_administrativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Responsável Administrativo!'
			),
		'acesso_portal' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe se tem disponibilidade de acesso!'
			),
		
		
		);

	function bindFornecedorContato(){
		$this->bindModel(
			array(
				'hasMany' => array(
					'FornecedorContato' => array(
						'foreignKey' => 'codigo_fornecedor'
						)
					),
				),false
			);
	}

	// function privateIsUnique() {
	// 	$codigo_documento = $this->data['Fornecedor']['codigo_documento'];
	// 	$codigo_empresa = $this->data['Fornecedor']['codigo_empresa'];
	// 	$count = $this->find('count', array('conditions' => array('Fornecedor.codigo_documento' => $codigo_documento, 'Fornecedor.codigo_empresa' => $codigo_empresa)));
	// 	ve($count);
	// 	exit;
	// 	if($count) {
	// 		return true;
	// 	} else {
	// 		return false;
	// 	}
	// }
	
	function converteFiltroEmCondition($data, $destino = null) {
		$conditions = array();
		
		if (!empty($data['codigo']))
			$conditions['Fornecedor.codigo'] = $data['codigo'];
		
		if (!empty($data['razao_social']))
			$conditions['Fornecedor.razao_social like'] = '%' . $data['razao_social'] . '%';

		if (!empty($data['nome']))
			$conditions['Fornecedor.nome like'] = '%' . $data['nome'] . '%';

		if (!empty($data['bairro']))
			$conditions['FornecedorEndereco.bairro like'] = '%' . $data['bairro'] . '%';

		if (!empty($data['codigo_documento']))
			$conditions['Fornecedor.codigo_documento like'] = $data['codigo_documento'] . '%';

		if( !empty($destino) && $destino == 'fornecedores_buscar_codigo')  {
			$conditions[] = 'Fornecedor.ativo in (0,1)';
		} else {
			if (isset($data['ativo'])){
				if($data['ativo'] == '0')
					$conditions[] = '(Fornecedor.ativo = '.$data['ativo'].' OR Fornecedor.ativo IS NULL)';
				else if ($data['ativo'] == '1')
					$conditions ['Fornecedor.ativo'] = $data['ativo'];
			}
		}

		if (!empty($data['estado'])) {
			// $conditions['EnderecoEstado.codigo'] = $data['estado'];
			$conditions['FornecedorEndereco.estado_descricao'] = $data['estado'];
		}

		if (! empty ( $data ['cidade'] )) {
			$encoding = mb_internal_encoding();
			// $conditions['EnderecoCidade.codigo'] = $data['cidade'];
			$conditions[] = array("(FornecedorEndereco.cidade LIKE '%". mb_strtolower($data['cidade'],$encoding) ."%' COLLATE Latin1_General_CI_AI OR FornecedorEndereco.cidade LIKE '%". mb_strtoupper($data['cidade'],$encoding) ."%' COLLATE Latin1_General_CI_AI)");
		}

		return $conditions;
	}

	function converteFiltroEmConditiONNfs($data) {
		$conditions = array();

		if (!empty($data['codigo_fornecedor']))
			$conditions['Fornecedor.codigo'] = $data['codigo_fornecedor'];

		if (!empty($data['numero_nota_fiscal']))
			$conditions['NotaFiscalServico.numero_nota_fiscal'] = $data['numero_nota_fiscal'];

		if (!empty($data['codigo_documento']))
			$conditions['Fornecedor.codigo_documento'] = $data['codigo_documento'];

		if (! empty ( $data ['nome'] ))
			$conditions ['Fornecedor.nome LIKE'] = '%' . $data ['nome'] . '%';
		
		return $conditions;
	}

	public function carregarParaEdicao($codigo_fornecedor) {

		$sql_fornecedor = "SELECT * FROM fornecedores WHERE codigo = ".$codigo_fornecedor;
		$get_fornecedor = $this->query($sql_fornecedor);
		$dados['Fornecedor'] = $get_fornecedor[0][0];

		if(isset($dados['Fornecedor']['codigo_fornecedor_fiscal']) && !empty($dados['Fornecedor']['codigo_fornecedor_fiscal'])){
			$sql = "SELECT * FROM fornecedores WHERE codigo = ".$dados['Fornecedor']['codigo_fornecedor_fiscal'];
			$dados_fornecedor_matriz = $this->query($sql);			
			
			if(!empty($dados_fornecedor_matriz)){
				$dados['Fornecedor']['codigo_fornecedor_fiscal'] = $dados_fornecedor_matriz[0][0]['codigo'];
			}
		}

		$FornecedorEndereco = ClassRegistry::init('FornecedorEndereco');
		$endereco_comercial = $FornecedorEndereco->getByTipoContato($codigo_fornecedor, TipoContato::TIPO_CONTATO_COMERCIAL);

		if ($endereco_comercial)
			$dados = array_merge($dados, $endereco_comercial);		
		return $dados;
	}

	function incluir($dados) {
		$this->FornecedorUnidade = ClassRegistry::init("FornecedorUnidade");

		try {
			if( $this->with_transaction )  $this->query('begin transaction');

			if(isset($dados['Fornecedor']['tipo_unidade']) && !empty($dados['Fornecedor']['tipo_unidade'])){
				if($dados['Fornecedor']['tipo_unidade'] == 'O'){
					if(empty($dados['Fornecedor']['codigo_fornecedor_fiscal'])){
						$this->invalidate('Fornecedor.codigo_fornecedor_fiscal', 'Informe o Fornecedor Fiscal');
						throw new Exception();
					}
					else{
						$dados['Fornecedor']['codigo_fornecedor_fiscal'] = $dados['Fornecedor']['codigo_fornecedor_fiscal'];
						$dados['Fornecedor']['codigo_documento'] = $this->geraCnpjFicticio($dados['Fornecedor']['codigo_fornecedor_fiscal']);

						unset($dados['Fornecedor']['codigo_fornecedor_fiscalCodigo']);
					}
				}
			}

			//verifica se existe cnpj já cadastrado
			$fornecedores = $this->find(
				'first',
				array(
					'conditions' => array(
						'Fornecedor.codigo_documento' => $dados['Fornecedor']['codigo_documento'],
						'Fornecedor.codigo_empresa' => $_SESSION['Auth']['Usuario']['codigo_empresa'],
					)
				)
			);

			//verifica se nao é vazio
			if(!empty($fornecedores)) {				
				$this->invalidate('codigo_documento', 'Já existe fornecedor com esse número de documento em base de dados');
				throw new Exception("Já existe este cnpj cadastrado");
			}

			if (!parent::incluir($dados['Fornecedor'], false)){				
				throw new Exception();
			}			

			$dados['Fornecedor']['codigo'] = $this->id;
			if (!$this->atualizarEnderecoComercial($dados))
				throw new Exception();

			if(isset($dados['Fornecedor']['tipo_unidade']) && !empty($dados['Fornecedor']['tipo_unidade'])){
				if($dados['Fornecedor']['tipo_unidade'] == 'O'){
					$consulta_unidade = $this->FornecedorUnidade->find('first', array('conditions' => array('codigo_fornecedor_unidade' => $dados['Fornecedor']['codigo'])));
					// debug($consulta_unidade);
					if(empty($consulta_unidade)){

						$data['FornecedorUnidade']['codigo_fornecedor_matriz'] = $dados['Fornecedor']['codigo_fornecedor_fiscal'];
						$data['FornecedorUnidade']['codigo_fornecedor_unidade'] = $dados['Fornecedor']['codigo'];
						if(!($this->FornecedorUnidade->incluir($data))){
							throw new Exception('Não é possivel Incluir uma Filial');
						}
					}
					else{
						if($consulta_unidade['FornecedorUnidade']['codigo_fornecedor_matriz'] != $dados['Fornecedor']['codigo_fornecedor_fiscal']){
							$data['FornecedorUnidade']['codigo'] = $consulta_unidade['FornecedorUnidade']['codigo'];
							$data['FornecedorUnidade']['codigo_fornecedor_matriz'] = $dados['Fornecedor']['codigo_fornecedor_fiscal'];
							if(!($this->FornecedorUnidade->atualizar($data))){
								throw new Exception('Não é possivel Incluir uma Filial');
							}
						}
					}
				}
			}

			if( $this->with_transaction ) $this->commit();
			return true;
		} catch (Exception $ex) {
			// $ex->getMessage();
			if( $this->with_transaction ) $this->rollback();
			return false;
		}
	}

	private function getLatLongGoogle( $end ){
		####### DEFINE LAT E LONG #############
		// if(Ambiente::TIPO_MAPA == 1) {
            App::import('Component',array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();
        // }
        // else if(Ambiente::TIPO_MAPA == 2) {
        //     App::import('Component',array('ApiGeoPortal'));
        //     $this->ApiMaps = new ApiGeoPortalComponent();
        // }

		$busca_google = $end['logradouro'] . ', ' . 
						$end['numero'] . ' - ' . 
						$end['bairro'] . ' - ' . 
						$end['cidade'] . ' / ' . 
						$end['estado_descricao'];

		list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco( $busca_google );

		return (Object) array( 'lat' => $latitude, 'long' => $longitude );
		
	}

	function atualizarEnderecoComercial($dados) {

		$validate = true;
		if (empty($dados['FornecedorEndereco']['cep'])) {
			$this->invalidate('FornecedorEndereco.cep', 'Informe o CEP');
			$validate = false;
		}

		if (empty($dados['FornecedorEndereco']['estado_descricao'])) {
			$this->invalidate('FornecedorEndereco.estado_descricao', 'Informe o UF');
			$validate = false;
		}

		if (empty($dados['FornecedorEndereco']['cidade'])) {
			$this->invalidate('FornecedorEndereco.cidade', 'Informe o Cidade');
			$validate = false;
		}

		if (empty($dados['FornecedorEndereco']['bairro'])) {
			$this->invalidate('FornecedorEndereco.bairro', 'Informe o Bairro');
			$validate = false;
		}

		if (empty($dados['FornecedorEndereco']['logradouro'])) {
			$this->invalidate('FornecedorEndereco.logradouro', 'Informe o Logradouro');
			$validate = false;
		}
		
		if($validate == false){
			return false;
		}

		$FornecedorEndereco = ClassRegistry::init('FornecedorEndereco');
		
		$dados_endereco = array('FornecedorEndereco' => $dados['FornecedorEndereco']);
		$dados_endereco['FornecedorEndereco']['codigo_fornecedor'] = $dados['Fornecedor']['codigo'];
		$dados_endereco['FornecedorEndereco']['codigo_tipo_contato'] = TipoContato::TIPO_CONTATO_COMERCIAL;


		if(empty($dados_endereco['FornecedorEndereco']['latitude']) || empty($dados_endereco['FornecedorEndereco']['longitude'])){
			
			$coordenate =   $this->getLatLongGoogle( $dados_endereco['FornecedorEndereco'] );
			$dados_endereco['FornecedorEndereco']['latitude'] = $coordenate->lat;
			$dados_endereco['FornecedorEndereco']['longitude'] = $coordenate->long;
			####### DEFINE LAT E LONG #############
		}

		$dados_endereco['FornecedorEndereco']['codigo_endereco'] = null;
		
		if (!isset($dados_endereco['FornecedorEndereco']['codigo']) || empty($dados_endereco['FornecedorEndereco']['codigo'])) {
			$result = $FornecedorEndereco->incluir($dados_endereco);
			return $result;
		} else {
			
			$dados_antigos = $FornecedorEndereco->carregar($dados_endereco['FornecedorEndereco']['codigo']);
			
			$change = false;

			$compare = array( 'cep','estado_descricao','cidade','bairro','logradouro','numero','complemento' ) ;

			foreach ($compare as $value) {
				//colocado tb se a latitude e longitude estao com alteracao, comparando os dados da base com o da tela, se tiver alteracao ele atualiza, por que encontrei um bug que nao esta autorizando atualizacao da latitude e longitude do fornecedor e dificultando encontra-lo na hora de emitir o pedido - CDCT-160
				if( $dados_antigos['FornecedorEndereco'][$value] != $dados_endereco['FornecedorEndereco'][$value] OR $dados_endereco['FornecedorEndereco']['latitude'] != $dados_antigos['FornecedorEndereco']['latitude'] OR $dados_endereco['FornecedorEndereco']['longitude'] != $dados_antigos['FornecedorEndereco']['longitude']){
					$change = true;
					break;
				}
			}
			
			if ($change == true) {
				$coordenate =   $this->getLatLongGoogle( $dados_endereco['FornecedorEndereco'] );
				$dados_endereco['FornecedorEndereco']['latitude'] = $coordenate->lat;
				$dados_endereco['FornecedorEndereco']['longitude'] = $coordenate->long;
				$dados_endereco = array('FornecedorEndereco' => array_merge($dados_antigos['FornecedorEndereco'], $dados_endereco['FornecedorEndereco']));
				return $FornecedorEndereco->atualizar($dados_endereco);
			} else {
				return true;
			}

		}
}

function atualizar($dados, $endereco = true) {
	$this->FornecedorUnidade = ClassRegistry::init("FornecedorUnidade");
	if (!isset($dados['Fornecedor']['codigo']) || empty($dados['Fornecedor']['codigo']))
		return false;
	try {
		if( $this->with_transaction ) $this->query('begin transaction');

		unset($dados['FornecedorHorario']); // FornecedorHorario nao é usada aqui em nenhuma rotina
		
		if(isset($dados['Fornecedor']['tipo_unidade']) && !empty($dados['Fornecedor']['tipo_unidade'])){
			if($dados['Fornecedor']['tipo_unidade'] == 'O'){

				if($dados['Fornecedor']['codigo'] == $dados['Fornecedor']['codigo_fornecedor_fiscal']){
					$this->invalidate('codigo_fornecedor_fiscal', 'Fornecedor Fiscal inválido');
					throw new Exception('Fornecedor Fiscal inválido');
				}

				if(empty($dados['Fornecedor']['codigo_fornecedor_fiscal'])){					
					$this->invalidate('codigo_fornecedor_fiscal', 'Informe o Fornecedor Fiscal!');
					throw new Exception();
				} else {
					$dados['Fornecedor']['codigo_fornecedor_fiscal'] = $dados['Fornecedor']['codigo_fornecedor_fiscal'];
					$dados['Fornecedor']['codigo_documento'] = $this->geraCnpjFicticio($dados['Fornecedor']['codigo_fornecedor_fiscal']);

					unset($dados['Fornecedor']['codigo_fornecedor_fiscalCodigo']);
				}

			}
		}


		if(isset($dados['Fornecedor']['tipo_unidade']) && !empty($dados['Fornecedor']['tipo_unidade'])){
			if($dados['Fornecedor']['tipo_unidade'] == 'O'){

				$consulta_unidade = $this->FornecedorUnidade->find('first', array('conditions' => array('codigo_fornecedor_unidade' => $dados['Fornecedor']['codigo'])));

				if(empty($consulta_unidade)){

					$data['FornecedorUnidade']['codigo_fornecedor_matriz'] = $dados['Fornecedor']['codigo_fornecedor_fiscal'];
					$data['FornecedorUnidade']['codigo_fornecedor_unidade'] = $dados['Fornecedor']['codigo'];
					$data['FornecedorUnidade']['ativo'] = 1;

					if(!($this->FornecedorUnidade->incluir($data))){
						throw new Exception('Não é possivel Incluir uma Filial');
					}
				}
				else{
					if($consulta_unidade['FornecedorUnidade']['codigo_fornecedor_matriz'] != $dados['Fornecedor']['codigo_fornecedor_fiscal']){
						$data['FornecedorUnidade']['codigo'] = $consulta_unidade['FornecedorUnidade']['codigo'];
						$data['FornecedorUnidade']['codigo_fornecedor_matriz'] = $dados['Fornecedor']['codigo_fornecedor_fiscal'];
						if(!($this->FornecedorUnidade->atualizar($data))){
							throw new Exception('Não é possivel Incluir uma Filial');
						}
					}
				}
			}
		}

		if (!parent::atualizar($dados)){
			throw new Exception('Não atualizou fornecedor');
		}

		if ($endereco && !$this->atualizarEnderecoComercial($dados))
			throw new Exception('Não atualizou endereço');

		if( $this->with_transaction ) $this->commit();
		return true;
	} catch (Exception $ex) {
		if( $this->with_transaction ) $this->rollback();

		return false;
	}
}

function buscaFornecedorJson( $like, $id = null, $limit = null ) {
	if( $like != 'null' && ( empty($id) || is_null( $id ) ) )             
		$resultado = $this->find( 'all', array( 
			'fields' => array( 'codigo',  'nome' ) ,
			'conditions' => array( 'OR' => array('nome LIKE' => '%'.$like.'%','codigo' => (int)preg_replace('/[^\-\d]*(\-?\d*).*/','$1',$like))) ,
			'order' => 'nome',
			'limit' => $limit,
			) );
	else
		$resultado = $this->find( 'all', array( 'conditions' => array( 'codigo' => $id ), 'fields' => array( 'nome' ) ) );
	
	return $this->retiraModel( $resultado );
}

function listarFornecedoresAtivas() {
	$fornecedores = $this->find('list', array(
		'fields' => array('codigo', 'nome'),
		'conditions' => array(
			'nome NOT LIKE' => 'DESATIVAD%'
			),
		'order' => 'nome'
		)
	);

	return $fornecedores;
}

function carregaFornecedorPjur($codigo_fornecedor){
	$TPjurPessoaJuridica = ClassRegistry::init("TPjurPessoaJuridica");
	$fornecedor = $this->carregar($codigo_fornecedor);
	if($fornecedor){
		$fornecedor_pjur = $TPjurPessoaJuridica->find('first',array('conditions' => array('pjur_cnpj' => $fornecedor['Fornecedor']['codigo_documento'])));
		if($fornecedor_pjur)
			return $fornecedor_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
	}
	return false;
}

function carregaFornecedorPorPjurCodigo($pjur_codigo){
	$TPjurPessoaJuridica = ClassRegistry::init("TPjurPessoaJuridica");
	$fornecedor_pjur = $TPjurPessoaJuridica->carregar($pjur_codigo);
	if($fornecedor_pjur){
		$fornecedor = $this->find('first',array('conditions' => array('codigo_documento' => $fornecedor_pjur['TPjurPessoaJuridica']['pjur_cnpj'])));
		if($fornecedor){
			return $fornecedor['Fornecedor']['codigo'];
		}
	}
	return false;
}

function completarTViagViagemCodigosDbBuonny($viag_viagem) {
	if (isset($viag_viagem['Fornecedor']['pjur_cnpj'])) {
		$fornecedor = $this->find('first', array('conditions' => array('codigo_documento' => $viag_viagem['Fornecedor']['pjur_cnpj'])));
		if ($fornecedor)
			$viag_viagem['Fornecedor']['codigo'] = $fornecedor['Fornecedor']['codigo'];
	}
	return $viag_viagem;
}

	/**
	 * ACAO UTILIZADA PARA IMPORTAR ARQUIVO: docs/fornecedores.csv (fornecedores)
	 * 
	 * @author: Danilo Borges Pereira
	 * <daniloborgespereira@gmail.com>
	 */
	
	function scriptImportaFornecedores() {
		
		ini_set("memory_limit","512M");
		ini_set("max_execution_time","600");
		
		$servicoClass = ClassRegistry::init("Servico");
		
		$filename = APP.'../docs'.DS.'fornecedores.csv';
		
		if(!file_exists($filename))
			exit("ARQUIVO NAO LOCALIZADO");

		if ($handle = fopen($filename, "r")) {
			$conteudo = Comum::trata_nome(utf8_encode(fread($handle, filesize($filename))));
			
			$array_organizado = array();
			foreach(explode("\n", $conteudo) as $key => $linha) {
				
				if($key > 0) {

					$temp = explode(";", $linha);

					if(count($temp) > 14) {
						
						$array_organizado[$temp[0]]['Fornecedor'][$temp[0]] = array(
							'nome' => $temp[0],
							'codigo_usuario_inclusao' => '1',
							'ativo' => '1'
							);
						
						$telefones = explode(";", $temp[6]);
						foreach($telefones as $k => $fone) {
							$fone = Comum::soNumero($fone);
							if(!isset($array_organizado[$temp[0]]['FornecedorContato'][$fone])) {
								$array_organizado[$temp[0]]['FornecedorContato'][$fone] = array(
									'codigo_fornecedor' => "this->Fornecedor->id",       ############################## DEFINIR ###############################
									'codigo_tipo_contato' =>  '2', 						
									'codigo_tipo_retorno' => '1',
									'descricao' => $fone,
									'codigo_usuario_inclusao' => '1'
									);								
							}
						}

						preg_match_all("/[A-Za-z0-9\._-]+@(([A-Za-z\.]+){2})[\.](([A-Za-z]+){2})/", $temp[13], $gera_array_emails);
						if(isset($gera_array_emails[0])) {
							foreach($gera_array_emails[0] as $k => $email) {
								if(!isset($array_organizado[$temp[0]]['FornecedorContato'][$email])) {
									$array_organizado[$temp[0]]['FornecedorContato'][$email] = array(
										'codigo_fornecedor' => "this->Fornecedor->id",       ############################## DEFINIR ###############################
										'codigo_tipo_contato' =>  '2', 						
										'codigo_tipo_retorno' => '2',
										'descricao' => $email,
										'codigo_usuario_inclusao' => '1'						
										);
								}
							}							
						}

						$resultado = $servicoClass->find('first', array('conditions' => array('descricao LIKE' => '%' . $temp[9] . '%')));
						
						pr($resultado);
						exit;
						
						pr($array_organizado);
						pr($linha);
						pr($temp);
						exit;						
						
						$fornecedorEndereco = array(
							'codigo_endereco' => '', #### buscar !!!
							'numero' => '',
							'complemento' => ''
							);					

						pr($array_organizado);
						exit;
						
						
						################################ limpa variaveis ################################
						unset($gera_array_emails);
						unset($fone);
						unset($telefones);
						################################ limpa variaveis ################################
						
						

						
						$help = '
						Array
						(
						[0] => 
						[1] => Atendente
						[2] => Aviso
						[3] => Bairro
						[4] => Banco
						[5] => Cabecalho
						[6] => CEP
						[7] => Cidade
						[8] => Cidades atendidas por esse prestador
						[9] => CNPJ
						[10] => Codigo
						[11] => Codigo da Agencia
						[12] => Codigo do Banco
						[13] => Comentarios
						[14] => Complemento
						[15] => Conselho de Classe do Responsavel
						[16] => Conta Corrente
						[17] => Contrato Ativo
						[18] => CPF
						[19] => Data Cancelamento
						[20] => Data Contratacao
						[21] => Data Pagamento
						[22] => Dia do Pagamento
						[23] => Disponivel para todas as empresas
						[24] => Distancia
						[25] => E-mail
						[26] => Endereco
						[27] => Especialidade do responsavel
						[28] => Especialidade do responsavel(2)
						[29] => Especialidades
						[30] => Esporadico
						[31] => Estado
						[32] => Exames Realizados
						[33] => Fax
						[34] => Hora Final do Atendimento
						[35] => Hora Inicial do Atendimento
						[36] => Motivo Cancelamento
						[37] => Nivel de Classificacao
						[38] => Nome
						[39] => Numero
						[40] => Observacao
						[41] => Procedimentos
						[42] => Razao Social
						[43] => Regra Padrao de Pagamentos
						[44] => Representante Legal
						[45] => Responsavel
						[46] => Rodape
						[47] => Status Contrato
						[48] => Telefone
						[49] => Telefone Celular
						[50] => Texto Livre
						[51] => Tipo
						[52] => Tipo de Atendimento
						[53] => Tipo de Pagamento
						[54] => Tipo Pessoa
						[55] => Titular da Conta

						)';
					}
				}
			}
			fclose($handle);
		}

		exit;
		pr($array_organizado);
		exit;
		// ======================================================================

		$listaDePreco = array(
			'codigo_fornecedor' => $codigo_fornecedor, 
			'descricao' => 'Fornecedor: '.$razao_social, 
			'codigo_usuario_inclusao' => $codigo_usuario_inclusao
			);	


		$lista_preco_produto = array(
			'ListaDePrecoProduto' => array(
				'codigo_lista_de_preco' => $codigo_lista_preco,
				'codigo_produto' => $dados[0]['Produto']['codigo'],
				'codigo_usuario_inclusao' => $codigo_usuario_inclusao,
				'valor_premio_minimo' => 0,
				'qtd_premio_minimo' => 0
				));		

		$lista_preco_produto_servico = array(
			'ListaDePrecoProdutoServico' => array(
				'codigo_lista_de_preco_produto' => $codigo_lista_de_preco_produto,
				'codigo_servico' => $dadosProduto['Servico']['codigo'],
				'valor'=> empty($dadosProduto['PropostaCredExame']['valor_contra_proposta']) ? $dadosProduto['PropostaCredExame']['valor'] : $dadosProduto['PropostaCredExame']['valor_contra_proposta'], 
				'codigo_usuario_inclusao'=> $codigo_usuario_inclusao,
				'valor_premio_minimo' => 0,
				'qtd_premio_minimo' => 0,
				));		

		$model_fornecedor = & ClassRegistry::init('Fornecedor');

		try {
			if( $this->with_transaction ) $this->query('begin transaction');

			$array_organizado = array();
			foreach($content as $key => $item) {

				if(trim($item) != '') {
					$linha = explode(";", $item);
					


				}
			}

			pr(count($array_organizado));
			pr($array_organizado);
			exit;


			if( $this->with_transaction ) $this->commit();
		} catch(Exception $e) {
			if( $this->with_transaction ) $this->rollback();
			echo "ESTAMOS EM EXCEPTION!";

			pr($e);
			exit;
		}			
		
		exit('script finalizado com sucesso!');
	}

	function documentoValido() {
		$model_documento = & ClassRegistry::init('Documento');
		$codigo_documento = $this->data[$this->name]['codigo_documento'];

		if($codigo_documento){
			if(strlen($codigo_documento) > 11) {
				if(isset($this->data['Fornecedor']['tipo_unidade']) && !empty($this->data['Fornecedor']['tipo_unidade'])){
					if($this->data['Fornecedor']['tipo_unidade'] == 'F'){
						if($model_documento->isCNPJ($codigo_documento) == false){
							return false;
						}
						else{
							return true;        	
						}
				}//UNIDADE OPERACIONAL; NÃO VALIDA CNPJ;
				else{
					return true;
				}
			}
			else{
				if($model_documento->isCNPJ($codigo_documento) == false){
					return false;
				}
				else{
					return true;        	
				}
			}	
		} else {
			if($model_documento->isCPF($codigo_documento) == false){
				return false;
			}
			else{
				return true;        	
			}
		}
	} else{
		return true;
	}
}    

function geraCnpjFicticio($codigo_fornecedor_matriz){

	$sql = "SELECT * FROM fornecedores WHERE codigo = ".$codigo_fornecedor_matriz;
	$get_fornecedor_matriz = $this->query($sql);
	$consulta['Fornecedor'] = $get_fornecedor_matriz[0][0];	

	if(empty($consulta)){
		$this->invalidate('Fornecedor.codigo_fornecedor_matriz', 'Fornecedor Fiscal não encontrado.');
		return false;
	} else {
		$cnpj = $consulta['Fornecedor']['codigo_documento'];

		$parte1 = substr($cnpj, 0,8);

		$conditions = array('codigo_documento LIKE ' => $parte1.'%');
		$fields = array('MAX(SUBSTRING(codigo_documento,9,4)) as codigo_documento');

		$qtd_cnpj = $this->find('first', compact('conditions', 'fields','joins'));

		$parte2 = $qtd_cnpj[0]['codigo_documento']+1;

		if(strlen($parte2)<4){
			$parte2 = str_pad($parte2, 3, 0, STR_PAD_LEFT);
		}

		if(substr($parte2, 0,1) <> "9"){
			$parte2 = "9".$parte2;
		}

		$digito_verificador = '99';

		$cnpj_ficticio = $parte1.$parte2.$digito_verificador;

		return $cnpj_ficticio;
	}
}


public function scriptImportaFornecedoresTiny($filename = null) {

if(is_null($filename)) return false;

	ini_set("memory_limit","512M");
	ini_set("max_execution_time", 999999);
	set_time_limit(0);

	$servicoClass = ClassRegistry::init("Servico");

	$destino = APP.'tmp'.DS.'importacao_tiny'.DS;
	if(!is_dir($destino))
		mkdir($destino); 

	$nome_arquivo = $destino.md5(date('Y-m-d h:i:s')).'.csv';

	move_uploaded_file($filename['tmp_name'], $nome_arquivo);

	if(!file_exists($nome_arquivo))
		exit("ARQUIVO NAO LOCALIZADO");

	if ($handle = fopen($nome_arquivo, "r")) {
			//ini_set('max_execution_time', '999999');
		$this->Endereco = ClassRegistry::init('Endereco');
		$this->FornecedorContato = ClassRegistry::init('FornecedorContato');

		// if(Ambiente::TIPO_MAPA == 1) {
            App::import('Component',array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();
        // }
        // else if(Ambiente::TIPO_MAPA == 2) {
        //     App::import('Component',array('ApiGeoPortal'));
        //     $this->ApiMaps = new ApiGeoPortalComponent();
        // }

		$conteudo = fread($handle, filesize($nome_arquivo));
		$erros = array();
		$c = 0;
		foreach(explode("\n", $conteudo) as $key => $linha) {
			if($key > 0) {
				$temp = explode(";", $linha);

					// ajuste de quebra de linha	
				if(!empty($temp[35])) $temp[35] = trim($temp[35]);

					// se nao existir endereço, preencher com o da buonny	
				if(empty($temp[8]) && !empty($temp[0])) {
					$temp[8] = '04053-040';
					$temp[5] = '102';
					$temp[6] = '';
					$temp[4] = 'Alameda dos Guatás';
				}

				if(empty($temp[2]) && empty($temp[3])) {
					$erros[$c] = $temp;
					$erros[$c][] = 'Sem nome definido';
					$c++;
					continue;
				}
				if(empty($temp[2])) $temp[2] = $temp[3];
				if(empty($temp[3])) $temp[3] = $temp[2];

				if(empty($temp[18])) {
					$erros[$c] = $temp;
					$erros[$c][] = 'Sem numero de documento definido';
					$c++;
					continue;
				}

				// cria os dados a serem salvos (utiliza o metodo de salvamento do proprio form de incluisao de fornecedor)
				$data['Fornecedor']['razao_social'] = $temp[2];
				$data['Fornecedor']['nome'] = $temp[3];
				$data['Fornecedor']['tipo_unidade'] = 'F';
				$data['Fornecedor']['codigo_documento'] = str_replace('-', '', str_replace('.', '', $temp[18]));
				$data['Fornecedor']['codigo_fornecedor_fiscal'] = '';
				$data['Fornecedor']['codigo_fornecedor_fiscalCodigo'] = '';
				$data['Fornecedor']['ativo'] = 1;
				$data['Fornecedor']['codigo_empresa'] = 2; //campo chumbado por garantia
				$data['Fornecedor']['responsavel_administrativo'] = empty($temp[3])? $temp[2] : $temp[3];
				$data['Fornecedor']['acesso_portal'] = 0;
				$data['Fornecedor']['interno'] = 0;

				// busca o endereco pelo cep
				$endereco = $this->Endereco->buscarEnderecoParaImportacao(trim(str_replace('.', '', str_replace('-', '', $temp[8]))), $temp[4]);

				// busca a latitude e a longtude
				$lat_lng = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($endereco['EnderecoTipo']['descricao'] . " " . $endereco['Endereco']['descricao'] . " - " . $temp[5]. " - " . $endereco['EnderecoCidade']['descricao'] . "  " . $endereco['EnderecoEstado']['descricao']);

				if(empty($lat_lng)) {
					$lat_lng[0] = 0;
					$lat_lng[1] = 0;
				}

				$data['FornecedorEndereco']['codigo_endereco'] = $endereco['Endereco']['codigo'];
				$data['FornecedorEndereco']['numero'] = $temp[5];
				$data['FornecedorEndereco']['complemento'] = $temp[6];
				$data['FornecedorEndereco']['latitude'] = $lat_lng[0];
				$data['FornecedorEndereco']['longitude'] = $lat_lng[1];
				$data['FornecedorEndereco']['raio'] = '150';
				$data['FornecedorEndereco']['poligono'] = '';
				$data['VEndereco']['endereco_cep'] = trim(str_replace('.', '', str_replace('-', '', $temp[8])));

				if($this->incluir($data)) {

					$data_contato['FornecedorContato']['codigo_fornecedor'] = $this->id;
					$data_contato['FornecedorContato']['codigo_tipo_contato'] = 2;
					$data_contato['FornecedorContato']['nome'] = $temp[3];
					$data_contato['FornecedorContato']['codigo_empresa'] = 2; //campo chumbado por garantia

					if(!empty($temp[12])) {	
						$data_contato['FornecedorContato']['codigo_tipo_retorno'] = 1;
						$data_contato['FornecedorContato']['descricao'] = $temp[12];
						$this->FornecedorContato->incluir($data_contato);
					}					

				    if(!empty($temp[13])) {	
						$data_contato['FornecedorContato']['codigo_tipo_retorno'] = 3;
						$data_contato['FornecedorContato']['descricao'] = $temp[13];
						$this->FornecedorContato->incluir($data_contato);
					}					

				    if(!empty($temp[14])) {	
						$data_contato['FornecedorContato']['codigo_tipo_retorno'] = 7;
						$data_contato['FornecedorContato']['descricao'] = $temp[14];
						$this->FornecedorContato->incluir($data_contato);
					}					

				    if(!empty($temp[15])) {	
						$data_contato['FornecedorContato']['codigo_tipo_retorno'] = 2;
						$data_contato['FornecedorContato']['descricao'] = $temp[15];
						$this->FornecedorContato->incluir($data_contato);
					}

				} else {
					$erro = '';
					foreach ($this->validationErrors as $key => $value) {
						$erro .= $value . " | ";
					}
					$erros[$c] = $temp;
					$erros[$c][] = $erro;
					$c++;
					continue;
				}

			} else {
					// cria o topo da planilha com os erros
				$temp = explode(";", $linha);
				foreach ($temp as $key => $value) {
					$temp[$key] = trim($value);
				}
				$temp[] = 'Erros'."\r\n";
				$topo = implode(';', $temp);
			}
		}
	}

	if(!empty($erros)) {
		$this->devolveErroImportacaoTiny($erros, $topo);
	}
	return true;
}

public function devolveErroImportacaoTiny($erros = array(), $topo)
{
	$str = $topo;
	foreach ($erros as $data) {
		$str  .= implode(';', $data).';'."\r\n";
	}
	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=dados.csv");
	header("Pragma: no-cache");
	header("Expires: 0");
	echo $str;
	exit;
}
	
	/**
	 * [dados_informacoes_credenciado description]
	 * 
	 * metodo para pegar todos os dados do credenciado concatenancdo conforme a especificação 21264
	 * 
	 * @param  [type] $codigo_fornecedor [description]
	 * @return [type]                    [description]
	 */
	public function dados_informacoes_credenciado($codigo_fornecedor = null, $campos = null, $tipo = 'tela')
	{		
		$query = "SELECT 
					f.codigo AS codigo_credenciado,
					f.razao_social AS razao_social,
					f.nome AS nome_fantasia,
					UPPER(RHHealth.publico.ufn_formata_cnpj(ISNULL(f.codigo_documento_real,f.codigo_documento))) as cnpj,
					-- ISNULL(f.codigo_documento_real,f.codigo_documento) AS cnpj,
					f.agencia AS agencia,
					fe.bairro AS bairro,
					f.numero_banco AS banco,
					fe.cep AS cep,
					f.cnes AS cnes,
					CONCAT(f.responsavel_tecnico_conselho_numero,' - ', f.responsavel_tecnico_conselho_uf) AS crm_uf,
					fe.cidade AS cidade,
					fe.complemento AS complemento,
					CAST((	SELECT CONCAT(m.nome,' - ', cp.descricao,' ',m.numero_conselho,'/')
							FROM fornecedores_medicos fm
								INNER JOIN medicos m on fm.codigo_medico = m.codigo
								INNER JOIN conselho_profissional cp on m.codigo_conselho_profissional = cp.codigo
							WHERE codigo_fornecedor = f.codigo 
							FOR XML PATH('')) AS TEXT) AS corpo_clinico,
					f.codigo_soc AS codigo_externo,
					CAST(
						(SELECT CONCAT(cf.codigo_cliente,' / ')
						FROM clientes_fornecedores cf
						WHERE cf.codigo_fornecedor = f.codigo
						GROUP BY cf.codigo_cliente
						FOR XML PATH('')) AS TEXT) AS codigo_cliente_vinculado,
					CAST(( 	SELECT CONCAT(descricao,' / ')	
							FROM fornecedores_contato 
							WHERE codigo_fornecedor = f.codigo 
								AND codigo_tipo_retorno = 2
							FOR XML PATH('')) AS TEXT) AS email,
					f.favorecido AS favorecido,
					(CASE WHEN f.interno = '1' THEN 'Sim' ELSE 'Não' END) AS fornecedor_interno,
					-- CONCAT(format(fh.de_hora,'##:##'), ' / ',format(fh.ate_hora,'##:##') ,' / ',fh.dias_semana) AS horario_atendimento,
					CAST((
		 				SELECT CONCAT(format(fh.de_hora,'##:##'), ' / ',format(fh.ate_hora,'##:##') ,' / ',fh.dias_semana, ' / ')
		 				FROM fornecedores_horario fe
							LEFT JOIN fornecedores_horario fh on f.codigo = fh.codigo_fornecedor
		 				WHERE fe.codigo_fornecedor = f.codigo
		 				GROUP BY fh.de_hora, fh.ate_hora, fh.dias_semana
		 					FOR XML PATH('')
		 				) AS TEXT
					) AS horario_atendimento,
					fe.logradouro AS logradouro,
					fe.numero AS numero,
					f.numero_conta AS numero_conta,
					f.responsavel_administrativo AS responsavel_adm,
					f.responsavel_tecnico AS responsavel_tecnico,
					CAST((SELECT CONCAT(s.descricao,'/')
						FROM listas_de_preco lp
							LEFT JOIN listas_de_preco_produto lpp on lp.codigo = lpp.codigo_lista_de_preco
							LEFT JOIN listas_de_preco_produto_servico lpps on lpp.codigo = lpps.codigo_lista_de_preco_produto
							LEFT JOIN servico s on lpps.codigo_servico = s.codigo 
								AND s.tipo_servico = 'E'
						WHERE lp.codigo_fornecedor = f.codigo
						GROUP BY s.descricao
						FOR XML PATH('')) AS TEXT) AS servico,
					(CASE WHEN f.ativo  = '1' THEN 'Sim' ELSE 'Não' END) AS status,
					CAST((	SELECT CONCAT(ddd,' ',descricao,' / ') 
							FROM fornecedores_contato 
							WHERE codigo_fornecedor = f.codigo 
								AND codigo_tipo_retorno = 1
							FOR XML PATH('')) AS TEXT) AS telefone,
					(CASE WHEN f.tipo_atendimento = '1' THEN 'Hora Marcada' ELSE 'Ordem de Chegada' END) AS tipo_atendimento,
					(CASE WHEN f.tipo_unidade = 'F' THEN 'Fiscal' ELSE 'Operacional' END) AS tipo_filial,
					(CASE WHEN f.tipo_conta = 1 then 'Conta Corrente' ELSE 'Conta Poupança' END) AS tipo_conta,
					(CASE WHEN f.exames_local_unico = '1' THEN 'Sim' ELSE 'Não' END) AS exames_unico_local,
					fe.estado_descricao AS uf,
					--(CASE WHEN f.acesso_portal = 1 THEN 'Sim' ELSE 'Não' END) AS utiliza_sistema,
					(CASE WHEN f.utiliza_sistema_agendamento = '1' THEN 'Sim' ELSE 'Não' END) AS sistema_agendamento,
					(CASE WHEN f.prestador_qualificado = '1' THEN 'Sim' ELSE 'Não' END) AS prestador_qualificado,
					(CASE 
						WHEN f.acesso_portal = 1 THEN 'Baixa de exame'
						WHEN f.acesso_portal = 2 THEN 'Digitação Técnica' 
						WHEN f.acesso_portal = 3 THEN 'Atendimento Online' 
					ELSE 'N/A' END) AS modalidade_atendimento,
					f.data_contratacao AS data_contratacao,
					(CASE 
						WHEN f.modalidade_pagamento = 1 THEN 'Pagamento Antecipado'
						WHEN f.modalidade_pagamento = 2 THEN 'Faturamento' 
						WHEN f.modalidade_pagamento = 3 THEN 'Faturamento Diferenciado' 
					ELSE 'N/A' END) AS modalidade_pagamento,
					f.faturamento_dias AS faturamento_dias,
					(CASE WHEN f.dia_pagamento is not null THEN f.dia_pagamento ELSE 'N/A' END) AS dia_pagamento,
					(CASE WHEN f.cobranca_boleto = 0 THEN 'Deposito em conta' ELSE 'Boleto' END) AS tipo_pagamento,
					CAST((
						SELECT CONCAT(s.descricao, ' de ', format(fhd.de_hora,'##:##'), ' até às ',format(fhd.ate_hora,'##:##') ,' / ',fhd.dias_semana, '  |  ')
						FROM fornecedores_horario_diferenciado fhdif
							LEFT JOIN fornecedores_horario_diferenciado fhd on f.codigo = fhd.codigo_fornecedor
							LEFT JOIN servico s on fhd.codigo_servico = s.codigo
						WHERE fhdif.codigo_fornecedor = f.codigo
						GROUP BY fhd.de_hora, fhd.ate_hora, fhd.dias_semana,s.descricao
						 	FOR XML PATH('')
						) AS TEXT
					) AS horario_diferenciado,
					(SELECT TOP 1 observacao FROM fornecedores_historico WHERE codigo_fornecedor = f.codigo ORDER BY codigo DESC) AS historico,
					
					tl.descricao as tempo_liberacao
					";

					if($tipo == "tela") {
						$query .= ",CAST((
							SELECT CONCAT('Exame: ', s.descricao, ' Valor Custo: R$ ', replace(ldps.valor, '.', ','), ', ')
							FROM listas_de_preco ldp
								LEFT JOIN listas_de_preco_produto ldpd on ldp.codigo = ldpd.codigo_lista_de_preco
								LEFT JOIN listas_de_preco_produto_servico ldps on ldpd.codigo = ldps.codigo_lista_de_preco_produto
								LEFT JOIN servico s on s.codigo = ldps.codigo_servico
							WHERE codigo_fornecedor = f.codigo
							GROUP BY s.descricao, ldps.valor
							FOR XML PATH('')
								) AS TEXT	
						) AS exames_tela";

					}

					if(isset($campos['exames'])){
						$query .= ",s.descricao as exames";
					}

					if(isset($campos['valor_custo'])){
						$query .= ",replace(ldps.valor, '.', ',') as valor_custo";
					}
										
			$query .= "	
				FROM fornecedores f
					LEFT JOIN fornecedores_endereco fe on f.codigo = fe.codigo_fornecedor
					LEFT JOIN fornecedores_horario fh on f.codigo = fh.codigo_fornecedor
					LEFT JOIN fornecedores_horario_diferenciado fhd on f.codigo = fhd.codigo_fornecedor
					LEFT JOIN listas_de_preco ldp on f.codigo = ldp.codigo_fornecedor
					LEFT JOIN listas_de_preco_produto ldpd on ldp.codigo = ldpd.codigo_lista_de_preco
					LEFT JOIN listas_de_preco_produto_servico ldps on ldpd.codigo = ldps.codigo_lista_de_preco_produto
					LEFT JOIN servico s on s.codigo = ldps.codigo_servico


					LEFT JOIN tempo_liberacao_servico tls ON s.codigo = tls.codigo_servico and tls.codigo_fornecedor = f.codigo
   					LEFT JOIN tempo_liberacao tl ON tls.codigo_tempo_liberacao = tl.codigo

				WHERE 1=1
			";
		
		if(!is_null($codigo_fornecedor) && $codigo_fornecedor > 0) {
			$query .= ' AND f.codigo = ' . $codigo_fornecedor ;
			
			if(isset($_SESSION['Auth']['Usuario']['codigo_empresa']) && $_SESSION['Auth']['Usuario']['codigo_empresa']) {
				$query .= " AND f.codigo_empresa = " . $_SESSION['Auth']['Usuario']['codigo_empresa'];
			}
		} else {
			if(isset($_SESSION['Auth']['Usuario']['codigo_empresa']) && $_SESSION['Auth']['Usuario']['codigo_empresa']) {
				$query .= " AND f.codigo_empresa = " . $_SESSION['Auth']['Usuario']['codigo_empresa'];
			}			
		}


		$query .= "
				GROUP BY
					f.codigo, 
					f.razao_social, 
					f.nome, 
					f.codigo_documento_real,
					f.codigo_documento, 
					f.tipo_unidade, f.ativo,
					f.codigo_soc, 
					f.responsavel_administrativo, 
					f.acesso_portal,
					f.interno, 
					f.cnes, 
					fe.cep, 
					fe.logradouro, 
					fe.numero,
					fe.complemento, 
					fe.bairro,
					fe.cidade,
					fe.estado_descricao,
					f.numero_banco,
					f.agencia,
					f.numero_conta,
					f.tipo_conta,
					f.favorecido,
					f.responsavel_tecnico,
					f.responsavel_tecnico_conselho_numero,
					f.responsavel_tecnico_conselho_uf,
					f.tipo_atendimento,
					f.exames_local_unico,
					f.utiliza_sistema_agendamento,
					f.prestador_qualificado,
					f.modalidade_pagamento,
					f.data_contratacao,
					f.modalidade_pagamento,
					f.faturamento_dias,
					f.dia_pagamento,
					f.cobranca_boleto,
					tl.descricao
					";

		if(isset($campos['exames'])){
			$query .= ",s.descricao";
		}

		if(isset($campos['valor_custo'])){
			$query .= ",ldps.valor";
		}
	
		// pr($query);exit;

		$dados = $this->query($query);
		return $dados;
	}

	public function get_lista_por_codigo_cliente($codigo_cliente){
        $fields = array(
            'Fornecedor.codigo',
            'Fornecedor.razao_social'
        );
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.clientes_fornecedores',
                'alias' => 'ClienteFornecedor',
                'type' => 'INNER',
                'conditions' => 'ClienteFornecedor.codigo_fornecedor = Fornecedor.codigo AND ClienteFornecedor.ativo = 1',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'ClienteFornecedor.codigo_cliente = Cliente.codigo',
            ),
        );
        $conditions = array('Cliente.codigo' => $codigo_cliente);
        $order = "Fornecedor.razao_social ASC";
        $recursive = -1;

        return $this->find('list', compact('fields', 'joins', 'conditions', 'order', 'recursive'));
    }

		public function documentoJaExiste() {			
			$codigo_documento = $this->data[$this->name]['codigo_documento'];

			$documentoJaExiste = $this->find(
				'first',
				array(
					'conditions' => array(
						'codigo_documento' => $codigo_documento,
						'codigo_empresa' => $_SESSION['Auth']['Usuario']['codigo_empresa'],
					)
				)
			);

			if(empty($documentoJaExiste)) {

				return true;
			}

			return false;
		}  		
}
