<?php

class Profissional extends AppModel {

	public $name = 'Profissional';
	public $tableSchema = 'publico';
	public $databaseTable = 'dbBuonny';
	public $useTable = 'profissional';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_profissional'),'SincronizarCodigoDocumento');
	var $validate = array(
		'nome' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o nome do profissional'
			),
		'codigo_documento' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o documento do profissional'
				),
			'documentoValido' => array(
				'rule' => 'documentoValido',
				'message' => 'Documento informado invalido'
				),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Documento já cadastrado'
				),
			),

		);

	const NAO_INFORMADO = 2666321;

	public function obtemUltimostatusProfissional($filtros) { 

		if (!isset($filtros['data_inicio']) || empty($filtros['data_inicio']))
			return false;
		if (!isset($filtros['data_fim']) || empty($filtros['data_fim']))
			return false;

		$this->Ficha = ClassRegistry::init('Ficha');
		$this->FichaPesquisa = ClassRegistry::init('FichaPesquisa');
		$this->Status = ClassRegistry::init('Status');
		$this->ProfissionalLog = ClassRegistry::init('ProfissionalLog');

		$fields = array(
			'Status.descricao',
			'FichaPesquisa.data_inclusao',
			'Ficha.data_validade'
			);
		$joins = array(
			array(
				'table' => "{$this->ProfissionalLog->databaseTable}.{$this->ProfissionalLog->tableSchema}.{$this->ProfissionalLog->useTable}",
				'alias' => 'ProfissionalLog',
				'type' => 'LEFT',
				'conditions' => 'Profissional.codigo = ProfissionalLog.codigo_profissional'
				),
			array(
				'table' => "{$this->Ficha->databaseTable}.{$this->Ficha->tableSchema}.{$this->Ficha->useTable}",
				'alias' => 'Ficha',
				'type' => 'LEFT',
				'conditions' => 'Ficha.codigo_profissional_log = ProfissionalLog.codigo'
				),
			array(
				'table' => "{$this->FichaPesquisa->databaseTable}.{$this->FichaPesquisa->tableSchema}.{$this->FichaPesquisa->useTable}",
				'alias' => 'FichaPesquisa',
				'type' => 'LEFT',
				'conditions' => 'Ficha.codigo = FichaPesquisa.codigo_ficha'
				),
			array(
				'table' => "{$this->Status->databaseTable}.{$this->Status->tableSchema}.{$this->Status->useTable}",
				'alias' => 'Status',
				'type' => 'LEFT',
				'conditions' => 'Status.codigo = codigo_status_profissional'
				),
			);
		$conditions = array(
			'Profissional.codigo_documento' => $filtros['pfis_cpf'],
			'Ficha.data_validade >' => date('Ymd H:i:s'),
			'FichaPesquisa.codigo_tipo_pesquisa ' => 2
			);
		$order = array('FichaPesquisa.codigo DESC');
		$resultado = $this->find('first', compact('fields', 'conditions', 'joins', 'order'));
		if(!$resultado) {
			$conditions = array(
				'Profissional.codigo_documento' => $filtros['pfis_cpf'],
			);
			$resultado = $this->find('first', compact('fields', 'conditions', 'joins', 'order'));
			if($resultado['Ficha']['data_validade'] > date('Ymd H:i:s')) {
				$resultado['Status']['descricao'] = 'PERFIL EXPIRADO';
			}else {
				$resultado['Status']['descricao'] = 'PERFIL DIVERGENTE';
			}

		}
		return $resultado;
	}

	public function carregaDadosParaCockpitMotorista($filtros) {
		$this->TipoCnh = ClassRegistry::init('TipoCnh');
		$this->ProfissionalContato = ClassRegistry::init('ProfissionalContato');

		$fields = array(
			'Profissional.data_inclusao',
			'Profissional.estrangeiro',
			'Profissional.codigo_documento',
			'Profissional.nome',
			'Profissional.cnh',
			'Profissional.cnh_vencimento',
			'TipoCnh.descricao',
			'ProfissionalTelefone.descricao',
			'ProfissionalRadio.descricao'
			);
		$joins = array(
			array(
				'table' => "{$this->TipoCnh->databaseTable}.{$this->TipoCnh->tableSchema}.{$this->TipoCnh->useTable}",
				'alias' => 'TipoCnh',
				'type' => 'LEFT',
				'conditions' => 'TipoCnh.codigo = Profissional.codigo_tipo_cnh'
				),
			array(
				'table' => "{$this->ProfissionalContato->databaseTable}.{$this->ProfissionalContato->tableSchema}.{$this->ProfissionalContato->useTable}",
				'alias' => 'ProfissionalTelefone',
				'type' => 'LEFT',
				'conditions' => 'ProfissionalTelefone.codigo_profissional = Profissional.codigo AND ProfissionalTelefone.codigo_tipo_retorno = 1'
				),
			array(
				'table' => "{$this->ProfissionalContato->databaseTable}.{$this->ProfissionalContato->tableSchema}.{$this->ProfissionalContato->useTable}",
				'alias' => 'ProfissionalRadio',
				'type' => 'LEFT',
				'conditions' => 'ProfissionalRadio.codigo_profissional = Profissional.codigo AND ProfissionalRadio.codigo_tipo_retorno = 6'
				),
			);
		$filtros['pfis_cpf'] = str_replace(array('.','-'), '', $filtros['pfis_cpf']);
		$conditions = array(
			'Profissional.codigo_documento' => $filtros['pfis_cpf'],
			);

		return $this->find('first', compact('fields', 'joins', 'conditions'));
	}

	function documentoValido() {
		if(!isset($this->data[$this->name]['estrangeiro'])){
			$Documento  =& ClassRegistry::init('Documento');
			return $Documento->isCPF($this->data[$this->name]['codigo_documento']);
		}

		return TRUE;
	}

	public function carregarModelsMetodoPossuiFichaEmAnalise() {
		$this->ProfissionalLog = & ClassRegistry::init('ProfissionalLog');
		$this->Ficha = & ClassRegistry::init('Ficha');
		$this->FichaPesquisa = & ClassRegistry::init('FichaPesquisa');
		ClassRegistry::init('Status');
	}

	public function unbindLazyFicha() {
		$this->unbindModel(array(
			'belongsTo' => array(
				'ProfissionalLog'
				)
			));
	}

	public function obterCodigoProfissionalTipo($codigo) {
		$conditions = array(
			'recursive' => -1,
			'fields' => 'Profissional.codigo_profissional_tipo',
			'conditions' => array(
				'Profissional.codigo' => $codigo
				),
			);
		$return = Set::extract($this->find('first', $conditions), '/Profissional/codigo_profissional_tipo');
		return $return[0];
	}

	public function verificarProfissional($codigo) {
		if (empty($codigo)) {
			return false;
		}
		return $this->find('count', array(
			'conditions' => array(
				'Profissional.codigo' => $codigo
				)
			)) > 0;
	}

	public function ultimaFichaPossuiPerfilAdequadoAoRisco($codigo_profissional, $codigo_cliente, $codigo_produto) {
		$this->carregarModelsMetodoPossuiFichaEmAnalise();

		$ultimaFicha = $this->find('first', array(
				//'recursive' => -1,
			'fields' => array('Ficha.*'),
			'conditions' => array(
				'Profissional.codigo' => $codigo_profissional,
				'Ficha.codigo_cliente' => $codigo_cliente,
				'Ficha.codigo_produto' => $codigo_produto
				),
			'joins' => array(
				array(
					'table' => "[{$this->ProfissionalLog->databaseTable}].[{$this->ProfissionalLog->tableSchema}].[{$this->ProfissionalLog->useTable}]",
					'alias' => 'ProfissionalLog',
					'type' => 'INNER',
					'conditions' => 'ProfissionalLog.codigo_profissional = Profissional.codigo'
					),
				array(
					'table' => "[{$this->Ficha->databaseTable}].[{$this->Ficha->tableSchema}].[{$this->Ficha->useTable}]",
					'alias' => 'Ficha',
					'type' => 'INNER',
					'conditions' => 'ProfissionalLog.codigo = Ficha.codigo_profissional_log'
					),
				),
			'order' => 'Ficha.codigo desc',
			));

		return $ultimaFicha['Ficha']['codigo_status'] == Status::RECOMENDADO;
	}

	public function emPesquisa($codigo_profissional, $codigo_cliente, $codigo_produto) {
		$this->carregarModelsMetodoPossuiFichaEmAnalise();
		$options = array(
			'recursive' => -1,
			'conditions' => array(
				'Profissional.codigo' => $codigo_profissional,
					// 'Ficha.codigo_cliente' => $codigo_cliente,
				'Ficha.codigo_produto' => $codigo_produto
				),
			'joins' => array(
				array(
					'table' => "[{$this->ProfissionalLog->databaseTable}].[{$this->ProfissionalLog->tableSchema}].[{$this->ProfissionalLog->useTable}]",
					'alias' => 'ProfissionalLog',
					'type' => 'INNER',
					'conditions' => 'ProfissionalLog.codigo_profissional = Profissional.codigo'
					),
				array(
					'table' => "[{$this->Ficha->databaseTable}].[{$this->Ficha->tableSchema}].[{$this->Ficha->useTable}]",
					'alias' => 'Ficha',
					'type' => 'INNER',
					'conditions' => 'ProfissionalLog.codigo = Ficha.codigo_profissional_log'
					),
				array(
					'table' => "[{$this->FichaPesquisa->databaseTable}].[{$this->FichaPesquisa->tableSchema}].[{$this->FichaPesquisa->useTable}]",
					'alias' => 'FichaPesquisa',
					'type' => 'INNER',
					'conditions' => 'Ficha.codigo = FichaPesquisa.codigo_ficha and FichaPesquisa.codigo_tipo_pesquisa in(1, 4, 5, 6)'
					)
				)
		);
		if (!empty($codigo_cliente)) {
			$options['conditions']['Ficha.codigo_cliente'] = $codigo_cliente;
		}
		return $this->find('count', $options) > 0;
	}

	public function possuiFichaEmAnalise($codigo_profissional, $codigo_cliente, $codigo_produto, $retornaCodigo = false) {
		$this->carregarModelsMetodoPossuiFichaEmAnalise();

		$options = array(
			'recursive' => -1,
			'conditions' => array(
				'Profissional.codigo' => $codigo_profissional,
					// 'Ficha.codigo_cliente' => $codigo_cliente,
				'Ficha.codigo_produto' => $codigo_produto,
				'Ficha.codigo_status' => 8
				),
			'joins' => array(
				array(
					'table' => "[{$this->ProfissionalLog->databaseTable}].[{$this->ProfissionalLog->tableSchema}].[{$this->ProfissionalLog->useTable}]",
					'alias' => 'ProfissionalLog',
					'type' => 'INNER',
					'conditions' => 'ProfissionalLog.codigo_profissional = Profissional.codigo'
					),
				array(
					'table' => "[{$this->Ficha->databaseTable}].[{$this->Ficha->tableSchema}].[{$this->Ficha->useTable}]",
					'alias' => 'Ficha',
					'type' => 'INNER',
					'conditions' => 'ProfissionalLog.codigo = Ficha.codigo_profissional_log'
					)
				)
			);

		if (!empty($codigo_cliente)) {
			$options['conditions']['Ficha.codigo_cliente'] = $codigo_cliente;
		}

		if ($retornaCodigo) {
			$options['fields'] = array(
				'Ficha.codigo'
				);
			$retorno = $this->find('all', $options);
			$retorno = end($retorno);
			if (!$retorno) {
				return $retorno;
			}
			return $retorno['Ficha']['codigo'];
		} else {
			$retorno = $this->find('count', $options);
			return $retorno > 0;
		}
	}

	public function possuiVinculo($codigo_profissional, $codigo_cliente, $codigo_produto, $tipo_profissional_carreteiro = false) {
		$this->carregarModelsMetodoPossuiFichaEmAnalise();
		$options = array(
			'recursive' => -1,
			'conditions' => array(
				'Profissional.codigo' => $codigo_profissional,
				'Ficha.codigo_produto' => $codigo_produto,

				),
			'joins' => array(
				array(
					'table' => "[{$this->ProfissionalLog->databaseTable}].[{$this->ProfissionalLog->tableSchema}].[{$this->ProfissionalLog->useTable}]",
					'alias' => 'ProfissionalLog',
					'type' => 'INNER',
					'conditions' => 'ProfissionalLog.codigo_profissional = Profissional.codigo'
					),
				array(
					'table' => "[{$this->Ficha->databaseTable}].[{$this->Ficha->tableSchema}].[{$this->Ficha->useTable}]",
					'alias' => 'Ficha',
					'type' => 'INNER',
					'conditions' => 'ProfissionalLog.codigo = Ficha.codigo_profissional_log'
					)
				)
			);

		if ($tipo_profissional_carreteiro) {
			array_push($options['conditions'], array('Ficha.codigo_profissional_tipo' => 1));
		} else {
			array_push($options['conditions'], array('Ficha.codigo_cliente' => $codigo_cliente));
			array_push($options['conditions'], array('Ficha.codigo_profissional_tipo <>' => 1));
		}

		array_push($options['conditions'], array('Ficha.data_validade >' => date('Y-m-d 00:00:00')));

		return $this->find('count', $options) > 0;
	}


	public function listaMotorista($palavra) {

		$conditions = array('Profissional.nome LIKE' => $palavra.'%');
		$fields 	= array('Profissional.codigo','Profissional.nome','Profissional.codigo_documento');
		$limit 		= 5;


		return $this->find('all', compact('conditions','fields','limit'));
	}

	public function listarNome(){
			$order = array('nome');
			//debug($this->find('list'));die();
			return $this->find('list');
		}

	public function listaMotoristaPorCPF($cpf) {

		$conditions = array('Profissional.codigo_documento' => $cpf);
		$fields 	= array('Profissional.codigo','Profissional.nome','Profissional.codigo_documento');
		$order		= array('Profissional.nome');
		$limit 		= 5;


		return $this->find('all', compact('conditions','fields','limit'));
	}


	public function buscaPorCodigo($codigo){
		$codigo = str_replace(array('.','-'), '', $codigo);
		$conditions = array('codigo' => $codigo);
		return $this->find('first',compact('conditions'));
	}

	public function buscaPorCPF($CPF){
		$CPF = str_replace(array('.','-'), '', $CPF);
		$conditions = array('codigo_documento' => $CPF);
		return $this->find('first',compact('conditions'));
	}

	public function buscaEspecificaPorCPF($cpf) {
		$this->ProfissionalContato = classRegistry::init('ProfissionalContato');
		$cpf = str_replace(array('.','-'), '', $cpf);
		$conditions = array(
			'codigo_documento' => $cpf,
			);

		$fields = array(
			'Profissional.codigo',
			'Profissional.nome',
			'Profissional.estrangeiro',
			'Profissional.rg',
			'Profissional.cnh',
			'Profissional.cnh_vencimento',
			'Profissional.codigo_documento',
			'ProfissionalTelefone.descricao',
			'ProfissionalCelular.descricao',
			'ProfissionalRadio.descricao'
			);

		$joins = array(
			array(
				'table' => "{$this->ProfissionalContato->databaseTable}.{$this->ProfissionalContato->tableSchema}.{$this->ProfissionalContato->useTable}",
				'alias' => 'ProfissionalTelefone',
				'type' => 'LEFT',
				'conditions' => 'ProfissionalTelefone.codigo_profissional = Profissional.codigo AND ProfissionalTelefone.codigo_tipo_retorno = 1'
				),
			array(
				'table' => "{$this->ProfissionalContato->databaseTable}.{$this->ProfissionalContato->tableSchema}.{$this->ProfissionalContato->useTable}",
				'alias' => 'ProfissionalCelular',
				'type' => 'LEFT',
				'conditions' => 'ProfissionalCelular.codigo_profissional = Profissional.codigo AND ProfissionalCelular.codigo_tipo_retorno = 5'
				),
			array(
				'table' => "{$this->ProfissionalContato->databaseTable}.{$this->ProfissionalContato->tableSchema}.{$this->ProfissionalContato->useTable}",
				'alias' => 'ProfissionalRadio',
				'type' => 'LEFT',
				'conditions' => 'ProfissionalRadio.codigo_profissional = Profissional.codigo AND ProfissionalRadio.codigo_tipo_retorno = 6'
				),
			);

		return $this->find('first',compact('conditions', 'joins', 'fields'));
	}

	public function incluir_profissional($data,$transacao = false){
		$TPessPessoa		=& ClassRegistry::Init('TPessPessoa');
		if($transacao){
			try{
				if($this->useDbConfig != 'test_suite')
					$this->query('begin transaction');
				$TPessPessoa->query('begin transaction');
				$this->chamada_inclusao($data);
				if($this->useDbConfig != 'test_suite')
					$this->commit();
				$TPessPessoa->commit();
				return true;
			} catch(Exception $ex){
				if($this->validationErrors){
					if(isset($this->validationErrors['nome']))
						$this->validationErrors['motorista_nome'] = $this->validationErrors['nome'];

					if(isset($this->validationErrors['codigo_documento']))
						$this->validationErrors['motorista_cpf'] = $this->validationErrors['codigo_documento'];
				}

				if($this->useDbConfig != 'test_suite')
					$this->rollback();
				$TPessPessoa->rollback();

				return array('erro' => $ex->getMessage());
			}

		} else {
			try{

				$this->chamada_inclusao($data);

				return true;

			} catch(Exception $ex){

				return array('erro' => $ex->getMessage());
			}
		}

	}

	public function chamada_inclusao(&$data){
		$ProfissionalContato =& ClassRegistry::Init('ProfissionalContato');
		$Motorista			 =& ClassRegistry::Init('Motorista');
		$TPessPessoa		 =& ClassRegistry::Init('TPessPessoa');
		$estrangeiro         = ( isset($data['estrangeiro']) && !empty($data['estrangeiro']) ) ? true : false;
		$params = array(
			'codigo_documento' 			=> strtoupper(str_replace(array('.','-','/'), '', $data['motorista_cpf'])),
			'nome' 						=> $data['motorista_nome'],
			'codigo_profissional_tipo'	=> 3,
			'codigo_modulo'				=> 2,
			'codigo_usuario_inclusao' 	=> 2,
			'data_inclusao'				=> date('Y-m-d H:i:s'),
		);
		if( $estrangeiro )
			$params['estrangeiro'] = 1;

		$this->incluirMotorista($params);
		$this->incluirMotorista($params);
		$TPessPessoa->incluirMotorista($params);
		$params = array(
			'CPF' 	=> strtoupper($data['motorista_cpf']),
			'Nome' 	=> $data['motorista_nome'],
			'Data'	=> date('Y-m-d H:i:s'),
		);
		if( $estrangeiro )
			$params['Nacionalidade'] = 'N';
		$Motorista->inserirMotoristaSM($params);
	}

	public function chamada_inclusao_motorista(&$data){
		$ProfissionalContato =& ClassRegistry::Init('ProfissionalContato');
		$Motorista			 =& ClassRegistry::Init('Motorista');
		$TPessPessoa		 =& ClassRegistry::Init('TPessPessoa');
		// Tabela ProfissionalContato
		$this->ProfissionalContato = ClassRegistry::init('ProfissionalContato');
		$estrangeiro         = ( isset($data['estrangeiro']) && !empty($data['estrangeiro']) ) ? false : true ;

		$this->query('begin transaction');
		try{

			$codigo = $this->incluirMotorista($data['Profissional']);
			$data['ProfissionalEndereco']['codigo_profissional'] = $codigo;
			// Tabela ProfissionalEndereco
			$this->ProfissionalEndereco = ClassRegistry::init('ProfissionalEndereco');
			if(!empty($data['ProfissionalEndereco']['codigo_endereco'])){
				if(!$this->ProfissionalEndereco->salvarProfissionalEndereco($data['ProfissionalEndereco'],$codigo, true))
					throw new Exception("Error Processing Request", 1);
			}
			//Codigo  Tabela Profissional
			if(!empty($data['ProfissionalContato']) and is_array($data['ProfissionalContato'])){
				foreach ($data['ProfissionalContato'] as $key => $profis) {
				 	$data['ProfissionalContato'][$key]['codigo_profissional'] = $codigo;
				}
				if(!$this->ProfissionalContato->salvarProfissionalContato($data['ProfissionalContato'], $codigo))
					throw new Exception("Error Processing Request", 1);
			}
				
				
			$Motorista->inserirMotoristaSM($data['Motorista']);
			//Tabela pess_pessoa
			$TPessPessoa->incluirMotorista($data);
			$this->commit();
		}catch(Exception $e){
			echo $e->getMessage();
			$this->rollback();
		}
	}

	public function incluirMotorista($data){
		$Documento =& ClassRegistry::Init('Documento');
			// Se não estiver no array da model, insere

		$data = isset($data['Profissional'])?$data:array('Profissional' => $data);

		if( !isset($data['Profissional']['estrangeiro']) ){

			if(!$Documento->isCPF($data['Profissional']['codigo_documento'])){

				$this->validationErrors['codigo_documento'] = 'Documento inválido';
				throw new Exception("CPF {$data['Profissional']['codigo_documento']} informado é inválido.");

			}
		}

		$profissional = $this->buscaPorCPF($data['Profissional']['codigo_documento']);

		if(!$profissional){

			$doc = $Documento->carregar(str_replace(array('.','-'), '', $data['Profissional']['codigo_documento']));
			//Inclui na Tabela publico.documento (dbBuonny)
			if(!$doc){
				$doc = array(
					'codigo' 		=> $data['Profissional']['codigo_documento'],
					'codigo_pais'	=> 1,
					'tipo'			=> 1,
					'data_inclusao' => date('Y-m-d H:i:s'),
					'codigo_usuario_inclusao' => 2,
					);

				$Documento->incluir($doc);
				if($Documento->validationErrors){
					if(isset($Documento->validationErrors['codigo']))
						$this->validationErrors['codigo_documento'] = $Documento->validationErrors['codigo'];

					throw new Exception('Erro ao salvar o documento do motorista');
				}
			}

			$this->create();

			if(!$this->save($data)) {
				throw new Exception('Erro ao salvar as informações do motorista no portal');
			}
			//Pega o ID do registro adicionado
			return  $this->getLastInsertId();
		}
		return $profissional['Profissional']['codigo'];

	}

	public function buscaContatoMotoristaPorCPF($cpf) {
		$TPfisPessoaFisica = ClassRegistry::init('TPfisPessoaFisica');
		$Motorista = ClassRegistry::init('Motorista');
		$this->ProfissionalContato = ClassRegistry::init('ProfissionalContato');
		$this->TipoRetorno = ClassRegistry::init('TipoRetorno');

			//$filtrosGuardian['pfis_cpf'] = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1$2$3-$4', $cpf);
		$filtrosGuardian['pfis_cpf'] = strtoupper($cpf);
			/*
			$queryDadosGuardian = $TPfisPessoaFisica->queryDadosGuardian($filtrosGuardian);
			$queryDadosGuardian = str_replace("'", "''", $queryDadosGuardian);
			$joins = array(
				array(
					'table' => "(SELECT * FROM OPENQUERY(LK_GUARDIAN, '{$queryDadosGuardian}'))",
					'alias' => 'TPfisPessoaFisica',
					'conditions' => 'TPfisPessoaFisica.pfis_cpf = Profissional.codigo_documento',
					'type' => 'INNER',
				),
				array(
					'table' => "{$Motorista->databaseTable}.{$Motorista->tableSchema}.{$Motorista->useTable}",
					'alias' => 'Motorista',
					'conditions' => 'REPLACE(REPLACE(Motorista.cpf,".",""),"-","") = Profissional.codigo_documento',
					'type' => 'INNER',
				),
			);
			*/

			//$this->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
			//$profissional = $this->find('first',compact('conditions', 'fields', 'joins', 'limit'));

	$conditions 	= array('codigo_documento' => $cpf);
	$fields			= array('codigo', 'codigo_documento', 'nome','estrangeiro');
	$profissional = $this->find('first',compact('conditions', 'fields', 'limit'));

	if(!$profissional)
		return array();

	$estrangeiro 		= $profissional['Profissional']['estrangeiro'];
	$codigo_profissional= $profissional['Profissional']['codigo'];
	$nome_profissional 	= $profissional['Profissional']['nome'];

	$celular = $this->ProfissionalContato->field('descricao', array('codigo_profissional'=>$codigo_profissional, 'codigo_tipo_retorno'=>TipoRetorno::TIPO_RETORNO_CELULAR_MOTORISTA));
	$celular = empty($celular) ? '' : $celular;
	$radio = $this->ProfissionalContato->field('descricao', array('codigo_profissional'=>$codigo_profissional, 'codigo_tipo_retorno'=>TipoRetorno::TIPO_RETORNO_RADIO));
	$radio = empty($radio) ? '' : $radio;


	return array('nome'=>$nome_profissional, 'telefone'=>$celular, 'radio'=>$radio,'codigo' => $codigo_profissional, 'estrangeiro' => $estrangeiro);
	}

	public function buscaEnderecoMotoristaPorCPF($cpf) {
		$this->ProfissionalEndereco = ClassRegistry::init('ProfissionalContato');
		$this->bindModel(
			Array(
				'hasOne' => Array(
					'ProfissionalEndereco' => Array(
						'className'	 => 'ProfissionalEndereco',
						'foreignKey' => 'codigo_profissional'
					),
					'VEndereco' => Array(
						'className'	 => 'VEndereco',
						'foreignKey' => false,
						'conditions' => Array(
							'ProfissionalEndereco.codigo_endereco = VEndereco.endereco_codigo' ,
						)
					),
				)
			)
		);
		$conditions 	= array('Profissional.codigo_documento' => $cpf);
		$fields			= array('VEndereco.*','ProfissionalEndereco.numero','ProfissionalEndereco.complemento');
		return $this->find('first',compact('fields','conditions'));
	}	

	function sincronizaMotorista(&$data){
		$this->TPessPessoa 			=& ClassRegistry::init("TPessPessoa");
		$this->ProfissionalContato 	=& ClassRegistry::init("ProfissionalContato");

			// PRE CADASTROS
		try{

			if($this->useDbConfig != 'test_suite')
				$this->query('begin transaction');
			$this->TPessPessoa->query('begin transaction');

			if($data['motorista_cpf'] && $data['motorista_cpf'] != 0){

					// INCLUI E ATUALIZA O MOTORISTA
				$retorno = $this->incluir_profissional($data);
				if(isset($retorno['erro']))
					throw new Exception($retorno['erro']);

					// ATUALIZA CONTATO DE RADIO E TELEFONE NO TELECONSULT
				$this->ProfissionalContato->atualizar_contato_motorista($data);
			}

			if($this->useDbConfig != 'test_suite')
				$this->commit();
			$this->TPessPessoa->commit();

			return TRUE;

		} catch (Exception $ex) {
			if($this->useDbConfig != 'test_suite')
				$this->rollback();
			$this->TPessPessoa->rollback();

			return FALSE;
		}
	}

	public function validarDadosFicha($data, $validar_dados_cnh = false){
		$this->validate = array(
			'codigo_documento' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Informe o documento do profissional'
					),
				'documentoValido' => array(
					'rule' => 'documentoValido',
					'message' => 'Documento informado inválido'
					),
				'isUnique' => array(
					'rule' => 'isUnique',
					'message' => 'Documento já cadastrado'
					),
	// 					'naoTemFichaEmAbertoParaCPF' => array(
	// 						'rule' => 'naoTemFichaEmAbertoParaCPF',
	// 						'message' => 'Esse CPF já tem uma ficha em aberto'
	// 					),
				),
			'nome' => array(
				'NotEmpty'=>array(
					'rule' => 'NotEmpty',
					'message' => 'Informe o nome do profissional'
					),
				//'regExp' => array(
				//	'rule' => '/^([a-zA-Z].{2,})\s([a-zA-Z].{2,})$/',
				//	'message' => 'Nome inválido',
				//	),
				),

			//'nome_pai' => array(
			//	'NotEmpty'=>array(
			//		'rule' => 'NotEmpty',
			//		'message' => 'Informe o nome do profissional'
			//		),
				//'regExp' => array(
				//	'rule' => '/^([a-zA-Z].{2,})\s([a-zA-Z].{2,})$/',
				//	'message' => 'Nome inválido',
				//	),
			//	),

			'nome_mae' => array(

				'NotEmpty'=>array(
					'rule' => 'NotEmpty',
					'message' => 'Informe o nome da mãe'
					),
				//'minLength' => array(
				//	'rule' => array('minLength', '2'),
				//	'message' => 'O  campo  Mãe deve ter no mínimo 2 caracteres',
				//	),
				//'regExp' => array(
				//	'rule' => '/^([a-zA-Z].{2,})\s([a-zA-Z].{2,})$/',
				//	'message' => 'Nome inválido',
				//	),
				),

			'rg' => array(
				'rule' => 'NotEmpty',
				'message' => 'Informe o RG'
				),
			'codigo_estado_rg' => array(
				'rule' => 'NotEmpty',
				'message' => 'Selecione a UF do RG'
				),
			'rg_data_emissao' => array(

				'notEmpty'=>array(
					'rule' => 'NotEmpty',
					'message' => 'Informe a data de emissão do RG'
					),

				'dataEmissaoMaiorQuedataNasc'=>array(
					'rule'=> array('dataEmissaoMaiorQuedataNasc'),
					'message'=>'Data de emissão não pode ser menor que data nasc'
					),

				'maiorQueHoje' => array(
					'rule' => array('dataMaiorQueHoje'),
					'message' => 'Data de emissão não pode ser maior que a data atual'
					),

				),
                'cidade_naturalidade_profissional' =>  array(
					'rule' => 'NotEmpty',
					'message' => 'Cidade Inválida.'
					)
			,'data_nascimento' => array(
				'date' => array(
					'rule' => array('date', 'dmy'),
					'message' => 'Data de nascimento inválida'
					),
				'notEmpty' => array(
					'rule' => 'NotEmpty',
					'message' => 'Informe a data de nascimento'
					),
				'maiorDe16Anos' => array(
					'rule' => 'maiorDe16Anos',
					'message' => 'O profissional deve ter mais de 16 anos'
					),
				),
				'possui_mopp' => array(
					'validaMopp' => array(
						'rule' => 'validaMopp',
						'message' => 'Informe a Data Início MOPP'
					),
				),			
			);

		if($validar_dados_cnh){
			$validate_dados_chn = array(
			'cnh' => array(
				'notEmpty' => array(
					'rule' => 'NotEmpty',
					'message' => 'Informe o número da CNH'
					),
				'Valida0000' => array(
					'rule' => '/^[0000]{0,4}[1-9][0-9]*$/',
					'message' => 'O número da CNH deve ter no máximo quatro 0 no início'
					),
				),
				'codigo_tipo_cnh' => array(
					'rule' => 'NotEmpty',
					'message' => 'Selecione a categoria da CNH'
				),
				'cnh_vencimento' => array(
					'date' => array(
						'rule' => array('date', 'dmy'),
						'message' => 'Data de vencimento inválida'
					),
					'notEmpty' => array(
						'rule' => 'NotEmpty',
						'message' => 'Informe da data de vencimento da CNH'
					),
					'vencida' => array(
						'rule' => array('cnhVencida'),
						'message' => 'CNH vencida'
					)
				),
				'codigo_endereco_estado_emissao_cnh' => array(
					'rule' => 'NotEmpty',
					'message' => 'Selecione a UF de emissão da CNH'
				),
				'data_primeira_cnh' => array(
					'date' => array(
						'rule' => array('date', 'dmy'),
						'message' => 'Data da primeira CNH inválida'
						),
					'notEmpty' => array(
						'rule' => 'NotEmpty',
						'message' => 'Informe a data da primeira CNH'
						),
					'maiorQueHoje' => array(
						'rule' => array('dataMaiorQueHoje'),
						'message' => 'Data da Primeira cnh não pode ser maior que a data atual'
						),
					'maiorDeIdade' => array(
						'rule' => array('maiorDeIdade'),
						'message' => 'Data da Primeira cnh não pode ser antes de o profissional completar 18 anos'
						)
					)
				,'codigo_seguranca_cnh' => array(
						'minLength' => array(
							'rule' => array('minLength', '11'),
							'message' => 'Códio de segurança precisa ter ao menos 11 caracteres'
						),
						'NotEmpty' => array(
							'rule' => 'NotEmpty',
							'message' => 'Informe a código de segurança da CNH'
						),
						
					),
			);
			//$this->validator()->add('cidade_naturalidade_profissional');
			$this->validate = array_merge($this->validate, $validate_dados_chn);
		}

	    $data['codigo_modulo'] = 8; //
	    if (!isset($data['codigo_documento'])) $data['codigo_documento'] = '';
	    $data['codigo_documento'] = str_replace('-','',$data['codigo_documento']);
	    $data['codigo_documento'] = str_replace('.','',$data['codigo_documento']);
	    $result = $this->saveall($data, array('validate' =>'only'));
		return $result;
	}

	public function validarDados($data, $validar_dados_cnh = false){

		$this->validate = array(
			'codigo_documento' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Informe o documento do profissional'
					),
				'documentoValido' => array(
					'rule' => 'documentoValido',
					'message' => 'Documento informado inválido'
					),
				'isUnique' => array(
					'rule' => 'isUnique',
					'message' => 'Documento já cadastrado'
					),
	// 					'naoTemFichaEmAbertoParaCPF' => array(
	// 						'rule' => 'naoTemFichaEmAbertoParaCPF',
	// 						'message' => 'Esse CPF já tem uma ficha em aberto'
	// 					),
				),
			'nome' => array(
				'NotEmpty'=>array(
					'rule' => 'NotEmpty',
					'message' => 'Informe o nome do profissional'
					),
				'regExp' => array(
					'rule' => '/^([a-zA-Z].{2,})\s([a-zA-Z].{2,})$/',
					'message' => 'Nome inválido',
					),
				),

			//'nome_pai' => array(
			//	'NotEmpty'=>array(
			//		'rule' => 'NotEmpty',
			//		'message' => 'Informe o nome do profissional'
			//		),
				//'regExp' => array(
				//	'rule' => '/^([a-zA-Z].{2,})\s([a-zA-Z].{2,})$/',
				//	'message' => 'Nome inválido',
				//	),
				//),

			'nome_mae' => array(

				'NotEmpty'=>array(
					'rule' => 'NotEmpty',
					'message' => 'Informe o nome da mãe'
					),
				'minLength' => array(
					'rule' => array('minLength', '2'),
					'message' => 'O  campo  Mãe deve ter no mínimo 2 caracteres',
					),
				'regExp' => array(
					'rule' => '/^([a-zA-Z].{2,})\s([a-zA-Z].{2,})$/',
					'message' => 'Nome inválido',
					),
				),

			'rg' => array(
				'rule' => 'NotEmpty',
				'message' => 'Informe o RG'
				),
			'codigo_estado_rg' => array(
				'rule' => 'NotEmpty',
				'message' => 'Selecione a UF do RG'
				),
			'rg_data_emissao' => array(

				'notEmpty'=>array(
					'rule' => 'NotEmpty',
					'message' => 'Informe a data de emissão do RG'
					),

				'dataEmissaoMaiorQuedataNasc'=>array(
					'rule'=> array('dataEmissaoMaiorQuedataNasc'),
					'message'=>'Data de emissão não pode ser menor que data nasc'
					),

				'maiorQueHoje' => array(
					'rule' => array('dataMaiorQueHoje'),
					'message' => 'Data de emissão não pode ser maior que a data atual'
					),

				),
                'cidade_naturalidade_profissional' =>  array(
					'rule' => 'NotEmpty',
					'message' => 'Cidade Inválida.'
					)
			,'data_nascimento' => array(
				'date' => array(
					'rule' => array('date', 'dmy'),
					'message' => 'Data de nascimento inválida'
					),
				'notEmpty' => array(
					'rule' => 'NotEmpty',
					'message' => 'Informe a data de nascimento'
					),
				'maiorDe16Anos' => array(
					'rule' => 'maiorDe16Anos',
					'message' => 'O profissional deve ter mais de 16 anos'
					),
				),
			);

		if($validar_dados_cnh){
			$validate_dados_chn = array(
			'cnh' => array(
				'notEmpty' => array(
					'rule' => 'NotEmpty',
					'message' => 'Informe o número da CNH'
					),
				'Valida0000' => array(
					'rule' => '/^[0000]{0,4}[1-9][0-9]*$/',
					'message' => 'O número da CNH deve ter no máximo quatro 0 no início'
					),
				),
				'codigo_tipo_cnh' => array(
					'rule' => 'NotEmpty',
					'message' => 'Selecione a categoria da CNH'
				),
				'cnh_vencimento' => array(
					'date' => array(
						'rule' => array('date', 'dmy'),
						'message' => 'Data de vencimento inválida'
					),
					'notEmpty' => array(
						'rule' => 'NotEmpty',
						'message' => 'Informe da data de vencimento da CNH'
					),
					'vencida' => array(
						'rule' => array('cnhVencida'),
						'message' => 'CNH vencida'
					)
				),
				'codigo_endereco_estado_emissao_cnh' => array(
					'rule' => 'NotEmpty',
					'message' => 'Selecione a UF de emissão da CNH'
				),
				'data_primeira_cnh' => array(
					'date' => array(
						'rule' => array('date', 'dmy'),
						'message' => 'Data da primeira CNH inválida'
						),
					'notEmpty' => array(
						'rule' => 'NotEmpty',
						'message' => 'Informe a data da primeira CNH'
						),
					'maiorQueHoje' => array(
						'rule' => array('dataMaiorQueHoje'),
						'message' => 'Data da Primeira cnh não pode ser maior que a data atual'
						),
					'maiorDeIdade' => array(
						'rule' => array('maiorDeIdade'),
						'message' => 'Data da Primeira cnh não pode ser antes de o profissional completar 18 anos'
						)
					)
				,'codigo_seguranca_cnh' => array(
					'rule' => 'NotEmpty',
					'message' => 'Informe a código de segurança da CNH'
					),
				);
			//$this->validator()->add('cidade_naturalidade_profissional');
			$this->validate = array_merge($this->validate, $validate_dados_chn);
		}

		return $this->saveAll($data, array('validate' => 'only'));
	}



	function cnhVencida(){ 
		if(empty($this->data['Profissional']['cnh_vencimento']))
			return true;
		$validade = $this->dateToDbDate($this->data['Profissional']['cnh_vencimento']);
		return $validade >= date('Ymd', strtotime('-30 days'));
	}

	function dataMaiorQueHoje($data){
		$data = current($data);
		if(empty($data))
			return true;
		$data = $this->dateToDbDate($data);
		return $data <= date('Ymd');
	}

	function dataEmissaoMaiorQuedataNasc(){

		$rg_data_emissao = date('Ymd',strtotime(str_replace("/","-",($this->data['Profissional']['rg_data_emissao']))));
		$data_nascimento = date('Ymd',strtotime(str_replace("/","-",($this->data['Profissional']['data_nascimento']))));

		if($rg_data_emissao >= $data_nascimento){
			return true;
		}
		if(empty($this->data['Profissional']['rg_data_emissao'])){
			return true;
		}

	}


	function maiorDeIdade($data){
		$data = current($data);
		if(empty($data))
			return true;
		$data_nascimento = date('Ymd', strtotime($this->dateToDbDate($this->data['Profissional']['data_nascimento']).' +18 years'));
		$data = $this->dateToDbDate($data);
		return $data >= $data_nascimento;
	}

	function maiorDe16Anos($data){
		$data = current($data);
		if(empty($data))
			return true;
		$data = $this->dateToDbDate($data);
		return $data < date('Ymd', strtotime('-16 years'));
	}

	function naoTemFichaEmAbertoParaCPF($cpf){
		$cpf = preg_replace('/\D/', '', current($cpf));
		ClassRegistry::init('FichaScorecardStatus');
		$this->FichaScorecard = ClassRegistry::init('FichaScorecard');
		$this->FichaScorecard->bindModel(array(
			'belongsTo'=>array(
				'ProfissionalLog'=>array('foreignKey'=>'codigo_profissional_log'),
				)
			));

		return $this->FichaScorecard->find('count', array('conditions'=>array('ProfissionalLog.codigo_documento'=>$cpf, 'FichaScorecard.codigo_status <>' => FichaScorecardStatus::FINALIZADA))) > 0 ? false : true;
	}

	function salvarProfissionalScorecard($data, $origem_portal=false){
		$data['Profissional']['codigo_documento'] = preg_replace('/\D/', '', $data['Profissional']['codigo_documento']);
		$codigo_profissional = $this->field('codigo', array('codigo_documento'=>$data['Profissional']['codigo_documento']));
		if(!empty($codigo_profissional)) {
			$data['Profissional']['codigo'] = $codigo_profissional;
			$data['Profissional']['codigo_modulo'] = 1;
		} else {
			$data['Profissional']['codigo_modulo'] = 8;
		}
		if ( $origem_portal ){
			$this->Behaviors->attach('Loggable', array('foreign_key' => 'codigo_profissional'));
			$this->Behaviors->attach('SincronizarCodigoDocumento');			
		}
		if($this->save($data['Profissional'], array('validate' => false))){
			$profissional_log['Profissional'] = $this->id;
			$this->ProfissionalLog = ClassRegistry::init('ProfissionalLog');
			$profissional_log['ProfissionalLog'] = $this->ProfissionalLog->id;

			$this->ProfissionalContato = ClassRegistry::init('ProfissionalContato');
			$profissional_contato_logs = $this->ProfissionalContato->salvarProfissionalContatoScorecard($data['ProfissionalContato'], $this->id);

			$this->ProfissionalEndereco = ClassRegistry::init('ProfissionalEndereco');
			$profissional_endereco_log = $this->ProfissionalEndereco->salvarProfissionalEnderecoScorecard($data['ProfissionalEndereco'], $this->id);
		}
		return array_merge($profissional_log, $profissional_contato_logs, $profissional_endereco_log);
	}

	public function carregarDadosCadastroProfissional( $codigo ){
		if( $codigo > 0 ){
			$this->ProfissionalContato  = ClassRegistry::init('ProfissionalContato');
			$this->ProfissionalEndereco = ClassRegistry::init('ProfissionalEndereco');
			$this->VEndereco            = ClassRegistry::init('VEndereco');
			$this->data = $this->carregar( $codigo );
			$contatos   = $this->ProfissionalContato->find('all', array('conditions'=>array('ProfissionalContato.codigo_profissional'=>$codigo), 'fields'=>array('nome', 'codigo_tipo_contato', 'codigo_tipo_retorno', 'descricao')));
			$endereco   = $this->ProfissionalEndereco->find('first', array('conditions'=>array('ProfissionalEndereco.codigo_profissional' => $codigo)));
			$this->data['ProfissionalEndereco'] = $endereco['ProfissionalEndereco'];
			$VEndereco  = $this->VEndereco->carregar( $this->data['ProfissionalEndereco']['codigo_endereco'] );
			$this->data['ProfissionalEndereco']['endereco_cep'] = $VEndereco['VEndereco']['endereco_cep'];
			$this->data['ProfissionalContato'] = Set::extract('/ProfissionalContato/.', $contatos);
			return $this->data;
		}else{
			return false;
		}
	}

	function incluir($data, $retorna_log = false) {
		$this->ProfissionalLog      = ClassRegistry::init('ProfissionalLog');
		$this->ProfissionalContato  = ClassRegistry::init('ProfissionalContato');
		$this->ProfissionalEndereco = ClassRegistry::init('ProfissionalEndereco');
		$data[$this->name]['codigo_documento'] = preg_replace('/\D/', '', $data['Profissional']['codigo_documento']);
		$data[$this->name]['codigo_modulo']    = 8;
		//$this->Behaviors->attach('Loggable', array('foreign_key' => 'codigo_profissional'));
		//$this->Behaviors->attach('SincronizarCodigoDocumento');
		//debug($data);
		parent::incluir($data);
		if( $this->id ){
			//debug('xxx');
			if ($retorna_log) {
				$profissional_log['Profissional'] = $this->id;
				$this->ProfissionalLog = ClassRegistry::init('ProfissionalLog');
				$profissional_log['ProfissionalLog'] = $this->ProfissionalLog->id;				
			}
			if( is_array($data['ProfissionalContato']) && count(($data['ProfissionalContato'])) > 0 )
				$profissional_contato_logs = $this->ProfissionalContato->salvarProfissionalContatoScorecard($data['ProfissionalContato'], $this->id);
			if( $data['ProfissionalEndereco']['codigo_endereco'] )
				$profissional_endereco_log = $this->ProfissionalEndereco->salvarProfissionalEnderecoScorecard($data['ProfissionalEndereco'], $this->id);

			if ($retorna_log) return array_merge($profissional_log, $profissional_contato_logs, $profissional_endereco_log);
			return $this->id;
		}
		return false;
	}

	function atualizar( $data , $retorna_log = false) {
		$this->ProfissionalLog      = ClassRegistry::init('ProfissionalLog');
		$this->ProfissionalContato  = ClassRegistry::init('ProfissionalContato');
		$this->ProfissionalEndereco = ClassRegistry::init('ProfissionalEndereco');
		$data[$this->name]['codigo_documento'] 		= preg_replace('/\D/', '', $data['Profissional']['codigo_documento']);
		$data[$this->name]['codigo_modulo']    		= 1;
		$this->id 									= $data[$this->name][$this->primaryKey];
		//$this->Behaviors->attach('Loggable', array('foreign_key' => 'codigo_profissional'));
		//$this->Behaviors->attach('SincronizarCodigoDocumento');
		$atualiza = parent::atualizar( $data );
		if( $atualiza ){
			if ($retorna_log) {
				$profissional_log['Profissional'] = $this->id;
				$this->ProfissionalLog = ClassRegistry::init('ProfissionalLog');
				$profissional_log['ProfissionalLog'] = $this->ProfissionalLog->id;				
			}

			if( is_array($data['ProfissionalContato']) && count(($data['ProfissionalContato'])) > 0 )
				$profissional_contato_logs  = $this->ProfissionalContato->salvarProfissionalContatoScorecard($data['ProfissionalContato'], $this->id);
			if( $data['ProfissionalEndereco']['codigo_endereco'] ) {
				$profissional_endereco_log  = $this->ProfissionalEndereco->salvarProfissionalEnderecoScorecard($data['ProfissionalEndereco'], $this->id);
			}
			
			if ($retorna_log) {
				return array_merge($profissional_log, $profissional_contato_logs, $profissional_endereco_log);
			}
			return true;
		}
		return false;
	}

	public function converteFiltroEmCondition( $data ) {
    	$conditions =array();
        if (!empty($data['codigo_documento']))
            $conditions['Profissional.codigo_documento like'] = preg_replace('/\D/', '', $data['codigo_documento']) . '%';
        if (!empty($data['nome']))
            $conditions['Profissional.nome like'] = '%' . $data['nome'] . '%';
        if (!empty($data['rg']))
            $conditions['Profissional.Rg'] = $data['rg'];

        return $conditions;
    }

	public function carregarContatos( $codigo ){
		if( $codigo > 0 ){
			$this->ProfissionalContato  = ClassRegistry::init('ProfissionalContato');
			$contatos = $this->ProfissionalContato->find('all', array('conditions'=>array('ProfissionalContato.codigo_profissional'=>$codigo), 'fields'=>array('nome', 'codigo_tipo_contato', 'codigo_tipo_retorno', 'descricao')));
			return Set::extract('/ProfissionalContato/.', $contatos);
		}
		return false;
	}

	function validaMopp( ){		
		if( !empty($this->data['Profissional']['possui_mopp']) ){
			if( !empty($this->data['Profissional']['data_inicio_mopp']) ){
				$data_inicio_mopp = strtotime(str_replace("/","-",($this->data['Profissional']['data_inicio_mopp'])));
				$data_atual       = strtotime(str_replace("/","-",(date('d/m/Y'))));
				if( $data_inicio_mopp > $data_atual ){
					$this->invalidate('data_inicio_mopp','Data não pode ser maior que a data atual.');
					return false;
				}
				if( date('Y', $data_inicio_mopp) < 1980 ){
					$this->invalidate('data_inicio_mopp','Data não pode ser menor que 01/01/1980.');
					return false;
				}
			} else {
				$this->invalidate('data_inicio_mopp','Informe a Data.');
				return false;
			}
		}
		return true;		
	}

}
