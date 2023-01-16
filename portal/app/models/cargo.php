<?php

class Cargo extends AppModel
{

	public $name = 'Cargo';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'cargos';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_cargos'));
	public $displayField = 'descricao';

	public $validate = array(
		'descricao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a descrição do Cargo.',
				'required' => true
			),
			'UnicaPorCliente' => array(
				'rule' => 'validaCargoDescricao',
				'message' => 'Cargo já existe.',
			),
		),
		'ativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Status do Cargo',
			'required' => true
		),
		'codigo_cliente' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Cliente do Cargo',
			'required' => true
		),
		'descricao_cargo' => array(			
				'rule' => 'notEmpty',
				'message' => 'Informe a descrição de atividades.',
				'required' => true
			)

	);

	function converteFiltroEmCondition($data)
	{
		$conditions = array();
		if (!empty($data['codigo']))
			$conditions['Cargo.codigo'] = $data['codigo'];

		if (!empty($data['descricao']))
			$conditions['Cargo.descricao LIKE'] = '%' . $data['descricao'] . '%';

		if (!empty($data['codigo_cliente']))
			$conditions['Cargo.codigo_cliente'] = $data['codigo_cliente'];

		if (isset($data['ativo'])) {
			if ($data['ativo'] === '0')
				$conditions[] = '(Cargo.ativo = ' . $data['ativo'] . ' OR Cargo.ativo IS NULL)';
			else if ($data['ativo'] == '1')
				$conditions['Cargo.ativo'] = $data['ativo'];
		}

		if (!empty($data['codigo_cbo']))
			$conditions['Cargo.codigo_cbo'] = $data['codigo_cbo'];

		return $conditions;
	}

	function carregar($codigo)
	{
		$dados = $this->find('first', array(
			'conditions' => array(
				$this->name . '.codigo' => $codigo
			)
		));
		return $dados;
	}

	function validaCargoDescricao()
	{

		//ajuste realizada PARA NAO DEIXAR CADASTRAR DUPLICADO, POIS Ó é diferente de ó, no banco de dados
		$desc_cargo_maiusculo = mb_strtoupper($this->data['Cargo']['descricao'], 'UTF-8');
		$desc_cargo_minusculo = mb_strtolower($this->data['Cargo']['descricao'], 'UTF-8');
		$descricoes = array($desc_cargo_maiusculo, $desc_cargo_minusculo, $this->data['Cargo']['descricao']);

		//query de verificacao
		$dados = $this->find('first', array('conditions' =>  array('descricao' => $descricoes, 'codigo_cliente' => $this->data['Cargo']['codigo_cliente'])));

		// pr($dados);
		// exit;

		//VERIFICO SE ENCONTROU UM REGISTRO COM A DESCRICAO E CODIGO_CLIENTE IGUAL
		if (!empty($dados)) {
			//VERIFICO SE É UM INSERT OU UPDATE.
			//SE O CODIGO QUE ESTA NA TELA FOR ENVIADO = UPDATE.
			if (!empty($this->data['Cargo']['codigo'])) {
				//SE O CODIGO DE RETORNO DO BD FOR DIFERENTE DO CODIGO ENVIADO EM TELA
				if ($dados['Cargo']['codigo'] != $this->data['Cargo']['codigo']) {
					return false;
				} else {
					return true;
				}
			} //SE O CODIGO QUE ESTA NA TELA NÃO FOR ENVIADO = INSERT.
			else {
				return false;
			}
		} else {
			return true;
		}
	}

	function lista_por_cliente($codigo_cliente, $bloqueado = false)
	{ //retorna um array com os cargos do cliente

		$GrupoEconomico = &ClassRegistry::Init('GrupoEconomico'); //inicia a classe GrupoEconomico
		$GrupoEconomicoCliente = &ClassRegistry::Init('GrupoEconomicoCliente'); //inicia a classe GrupoEconomicoCliente

		$conditions = array('Cargo.ativo' => 1); //condicoes padrao

		if (is_array($codigo_cliente)) {
			$conditions[] = "GrupoEconomico.codigo_cliente " . $this->rawsql_codigo_cliente($codigo_cliente); //condicao para o cliente
		} else {
			$retorna_matriz = $GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente); //obtem o codigo da matriz do cliente
			$conditions[] = "GrupoEconomico.codigo_cliente = " . $retorna_matriz; //condicao para o cliente
		}

		$fields = array('Cargo.codigo', 'Cargo.descricao'); //campos que serao retornados

		$order = array('Cargo.descricao ASC'); //ordenacao

		$joins 	= array( //joins
			array(
				'table'	=> $GrupoEconomicoCliente->databaseTable . '.' . $GrupoEconomicoCliente->tableSchema . '.' . $GrupoEconomicoCliente->useTable,
				'alias'	=> 'GrupoEconomicoCliente',
				'conditions' => 'Cargo.codigo_cliente = GrupoEconomicoCliente.codigo_cliente',
			),
			array(
				'table'	=> $GrupoEconomico->databaseTable . '.' . $GrupoEconomico->tableSchema . '.' . $GrupoEconomico->useTable,
				'alias'	=> 'GrupoEconomico',
				'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
			),
		);
		if ($bloqueado) { //se for para listar os bloqueados
			$ClienteSetorCargo = &ClassRegistry::Init('ClienteSetorCargo');
			$joins[] = array(
				'table'	=> $ClienteSetorCargo->databaseTable . '.' . $ClienteSetorCargo->tableSchema . '.' . $ClienteSetorCargo->useTable,
				'alias'	=> 'ClienteSetorCargo',
				'conditions' => 'ClienteSetorCargo.codigo_cargo = Cargo.codigo',
			);
		}

		// pr($this->find('sql', compact('conditions', 'fields','order', 'joins')));//debug

		$dados = $this->find('list', compact('conditions', 'fields', 'order', 'joins')); //executa a query

		return $dados; //retorna os dados
	}

	function lista_cargo_por_cliente_setor($codigo_cliente, $setor, $bloqueado = false, $codigo_cargo = null)
	{
		if ($bloqueado) {
			$this->FuncionarioSetorCargo = &ClassRegistry::init('FuncionarioSetorCargo');

			$conditions = array(
				'FuncionarioSetorCargo.codigo_cliente' => $codigo_cliente,
				'FuncionarioSetorCargo.codigo_setor'   => $setor,
				'Cargo.ativo' => 1
			);

			if (!is_null($conditions)) {
				$conditions['Cargo.codigo'] = $codigo_cargo;
			}

			$cargos = $this->FuncionarioSetorCargo->find(
				'list',
				array(
					'recursive' => -1,
					'joins' => array(
						array(
							'table' => 'cargos',
							'alias' => 'Cargo',
							'type' => 'INNER',
							'conditions' => array(
								'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo'
							)
						)
					),
					'conditions' => $conditions,
					'fields' => array(
						'Cargo.codigo',
						'Cargo.descricao'
					),
					'order' => 'Cargo.descricao'
				)
			);
		} else {
			$this->GrupoEconomico = &ClassRegistry::init('GrupoEconomico');
			$matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
			// $cargos = $this->find('list', array('fields' => array('codigo', 'descricao')));

			$conditions = array('Cargo.codigo_cliente' => $matriz, 'Cargo.ativo' => 1);

			if (!is_null($conditions)) {
				$conditions['Cargo.codigo'] = $codigo_cargo;
			}

			$cargos = $this->find('list', array('conditions' => $conditions, 'fields' => array('codigo', 'descricao'), 'order' => 'Cargo.descricao'));
		}
		if (is_null($cargos)) $cargos = array();
		return $cargos;
	}

	function importacao_cargo_unidade($dados)
	{

		$retorno = '';
		$dados['Cargo']['ativo'] = 1;

		if (!isset($dados['Cargo']['codigo']) && empty($dados['Cargo']['codigo'])) {
			if (!parent::incluir($dados)) {
				$retorno['Cargo'] = $this->validationErrors;
			}
		} else {
			if (!parent::atualizar($dados)) {
				$retorno['Cargo'] = $this->validationErrors;
			}
		}
		return $retorno;
	}

	function localiza_cargo_importacao($data, $bloqueado = null)
	{
		$retorno = '';
		$codigo_cliente_grupo_economico = $data['codigo_cliente_grupo_economico'];
		//trunca a descricao do cargo em 50 caracteres conforme esta na tabela setores
		$descricao 						= substr(trim($data['cargo_descricao']), 0, 50);
		//verificação do grupo economico enviado da pagina de importacao
		if (!empty($codigo_cliente_grupo_economico)) {
			//verificação da descricao do cargo enviada da planilha
			if (!empty($descricao)) {
				//verifica se o cargo já está cadastrado/ativo na base
				$conditions = array(
					"Cargo.codigo_cliente" => $codigo_cliente_grupo_economico,
					"(Cargo.descricao = '" . ($descricao) . "')",
					"Cargo.ativo" => 1
				);
				$fields = array(
					'Cargo.codigo', 'Cargo.descricao', 'Cargo.codigo_cliente'
				);
				$recursive = -1;
				$dados = $this->find('first', compact('conditions', 'fields', 'recursive'));
				if (empty($dados)) { //caso nao encontre o nome exato, busca sem caracteres especiais.
					$conditions = array(
						"Cargo.codigo_cliente" => $codigo_cliente_grupo_economico,
						"Cargo.descricao = '" . Comum::trata_nome(($descricao)) . "'",
						"Cargo.ativo" => 1
					);
					$dados = $this->find('first', compact('conditions', 'fields', 'recursive'));
				}
				if (!empty($dados)) {
					//se o cargo já existe na base, retorna os dados dele para a validação principal
					$retorno['Dados'] = $dados;
				} else {
					// se o cargo não existe na base, o mesmo será cadastrado SE a unidade não se encontra bloqueada
					if (!$bloqueado) {
						//monta array para incluir cargo
						$dados_cargo = array(
							'Cargo' => array(
								'descricao' => $descricao,
								'codigo_cliente' => $data['codigo_cliente_grupo_economico']
							)
						);
						// inclui/atualiza cargo
						$retorno_unidade_cargo = $this->importacao_cargo_unidade($dados_cargo);
						// se não existe retorno então não incluiu/atualizou
						if (!empty($retorno_unidade_cargo)) {
							if (empty($retorno_unidade_cargo['Cargo'])) {
								$retorno['Erro'] = array('Cargo' => array('codigo_cargo' => utf8_decode('Nao foi possivel cadastrar o cargo: ') . $descricao));
							} else {
								foreach ($retorno_unidade_cargo['Cargo'] as $campo => $erro) {
									$retorno_erro['Cargo'][$campo] = utf8_decode($erro);
								}
								$retorno['Erro'] = $retorno_erro;
							}
						} else {
							// se incluiu/atualizou, faz o find denovo e retorna os dados do cargo para a validação principal
							$dados_cargo = $this->find('first', compact('conditions', 'fields'));
							$retorno['Dados'] = $dados_cargo;
						}
					} else {
						// se a unidade se encontra bloqueada, retorna erro e não cadastra o cargo
						$retorno['Erro'] = array('Cargo' => array('codigo_cargo' => utf8_decode('A unidade encontra-se bloqueada, logo nao foi possivel incluir o novo cargo.')));
					}
				}
			} else {
				$retorno['Erro'] = array('Cargo' => array('descricao' => utf8_decode('Descricao do cargo nao foi enviada!')));
			}
		} else {
			$retorno['Erro'] = array('Cargo' => array('codigo_cliente' => utf8_decode('O codigo do grupo economico nao consta na importacao!')));
		}
		return $retorno;
	}

	public function lista($codigo_cliente)
	{
		$conditions = array('codigo_cliente' => $codigo_cliente, 'ativo' => 1);
		$order = array('descricao');
		return $this->find('list', compact('conditions', 'order'));
	}

	/**
	 * Retorno da lista de cargos a partir do(s) codigo_cliente fornecido(s)
	 * 
	 * @param array $codigo_cliente
	 * @return array
	 * 
	 *	ex. resposta
	 *
	 *		"79667": [{
	 *			"codigo": 270736,
	 *			"descricao": "ALMOXARIFE I - 900563"
	 *		}, {
	 *			"codigo": 270674,
	 *			"descricao": "ANALISTA ADMINISTRATIVO PL - 12329"
	 *		}, {
	 *			"codigo": 270707,
	 *			"descricao": "ANALISTA CADASTRO MATERIAIS - 13501"
	 *		}, {
	 *			"codigo": 270671,
	 *			"descricao": "ANALISTA COMPRAS JR - 12337"
	 *		}
	 *
	 */
	public function obterLista($codigo_cliente = array())
	{
		$conditions = array('codigo_cliente' => $codigo_cliente, 'ativo' => 1);
		$order = array('descricao');
		$fields = array('codigo', 'descricao', 'codigo_cliente');
		$list = $this->find('all', compact('conditions', 'fields', 'order'));

		$dados = array();
		if (is_array($list)) {
			foreach ($list as $key => $value) {

				$linha = $list[$key]['Cargo'];
				$cargo = $linha;

				if (isset($cargo['codigo_cliente']))
					unset($cargo['codigo_cliente']);

				$dados[$linha['codigo_cliente']][] = $cargo;
			}
		}

		return $dados;
	} //FINAL FUNCTION obterLista		
}
