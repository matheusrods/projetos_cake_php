<?php
class Setor extends AppModel
{
	public $name = 'Setor';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'setores';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_setores'));
	public $displayField = 'descricao';
	public $validate = array(
		'descricao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a descrição do Setor.',
				'required' => true
			),
			'UnicaPorCliente' => array(
				'rule' => 'validaSetorDescricao',
				'message' => 'Setor já existe.',
			),
		),
		'ativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Status do Setor',
			'required' => true
		),
		'codigo_cliente' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Cliente do Setor',
			'required' => true
		),
	);
	function converteFiltroEmCondition($data)
	{
		$conditions = array();
		if (!empty($data['codigo']))
			$conditions['Setor.codigo'] = $data['codigo'];
		if (!empty($data['descricao']))
			$conditions['Setor.descricao LIKE'] = '%' . $data['descricao'] . '%';
		if (!empty($data['codigo_cliente']))
			$conditions['Setor.codigo_cliente'] = $data['codigo_cliente'];
		if (isset($data['ativo'])) {
			if ($data['ativo'] === '0')
				$conditions[] = '(Setor.ativo = ' . $data['ativo'] . ' OR Setor.ativo IS NULL)';
			else if ($data['ativo'] == '1')
				$conditions['Setor.ativo'] = $data['ativo'];
		}
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
	function validaSetorDescricao()
	{
		//ajuste realizada PARA NAO DEIXAR CADASTRAR DUPLICADO, POIS Ó é diferente de ó, no banco de dados
		$desc_setor_maiusculo = mb_strtoupper($this->data['Setor']['descricao'], 'UTF-8');
		$desc_setor_minusculo = mb_strtolower($this->data['Setor']['descricao'], 'UTF-8');
		$descricoes = array($desc_setor_maiusculo, $desc_setor_minusculo, $this->data['Setor']['descricao']);
		// filtros para verificacao
		$conditions = array(
			'codigo_cliente' => $this->data['Setor']['codigo_cliente'],
			'descricao' => $descricoes
		);
		$dados = $this->find('first', array('conditions' =>  $conditions));

		//VERIFICO SE ENCONTROU UM REGISTRO COM A DESCRICAO E CODIGO_CLIENTE IGUAL
		if (!empty($dados)) {
			//VERIFICO SE É UM INSERT OU UPDATE.
			//SE O CODIGO QUE ESTA NA TELA FOR ENVIADO = UPDATE.
			if (!empty($this->data['Setor']['codigo'])) {
				//SE O CODIGO DE RETORNO DO BD FOR DIFERENTE DO CODIGO ENVIADO EM TELA
				if ($dados['Setor']['codigo'] != $this->data['Setor']['codigo']) {
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
	function lista_por_cliente($codigo_cliente, $bloqueado = false, $codigo_setor = null, $busca_por_alocacao = false)
	{

		$GrupoEconomico = &ClassRegistry::Init('GrupoEconomico'); //inicia a classe GrupoEconomico
		$GrupoEconomicoCliente = &ClassRegistry::Init('GrupoEconomicoCliente'); //inicia a classe GrupoEconomicoCliente

		$conditions = array('Setor.ativo' => 1, 'GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente); //condicoes para buscar os grupos econômicos do cliente

		if (!is_null($codigo_setor)) { //se o codigo do setor for enviado
			$conditions['Setor.codigo'] = $codigo_setor; //adiciona na condicao o codigo do setor
		}

		$fields = array('Setor.codigo', 'Setor.descricao'); //campos para buscar

		$order = array('Setor.descricao ASC'); //ordenação

		$joins 	= array( //joins para buscar
			array(
				'table'	=> $GrupoEconomico->databaseTable . '.' . $GrupoEconomico->tableSchema . '.' . $GrupoEconomico->useTable,
				'alias'	=> 'GrupoEconomico',
				'conditions' => 'GrupoEconomico.codigo_cliente = Setor.codigo_cliente',
			),
			array(
				'table'	=> $GrupoEconomicoCliente->databaseTable . '.' . $GrupoEconomicoCliente->tableSchema . '.' . $GrupoEconomicoCliente->useTable,
				'alias'	=> 'GrupoEconomicoCliente',
				'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
			),
		);

		if ($bloqueado) { //se for para buscar os setores bloqueados

			$ClienteSetorCargo = &ClassRegistry::Init('ClienteSetorCargo'); //inicia a classe ClienteSetorCargo

			$joins[] = array( //joins para buscar
				'table'	=> $ClienteSetorCargo->databaseTable . '.' . $ClienteSetorCargo->tableSchema . '.' . $ClienteSetorCargo->useTable,
				'alias'	=> 'ClienteSetorCargo',
				'conditions' => 'ClienteSetorCargo.codigo_setor = Setor.codigo AND ClienteSetorCargo.' . ($busca_por_alocacao ? 'codigo_cliente_alocacao' : 'codigo_cliente') . ' = ' . $codigo_cliente,
			);

			$conditions[] = '(ClienteSetorCargo.ativo = 1 OR ClienteSetorCargo.ativo IS NULL)'; //condicoes para buscar os setores bloqueados
		}

		// pr($this->find('sql', compact('conditions', 'fields','order', 'joins')));//imprime o sql

		$dados = $this->find('list', compact('conditions', 'fields', 'order', 'joins')); //busca os setores

		return $dados; //retorna os setores
	}
	function importacao_setor_unidade($dados)
	{
		$retorno = '';
		$dados['Setor']['ativo'] = 1;

		if (!isset($dados['Setor']['codigo']) && empty($dados['Setor']['codigo'])) {
			if (!$this->incluir($dados, false)) {
				$retorno['Setor'] = $this->validationErrors;
			}
		} else {
			if (!parent::atualizar($dados, false)) {
				$retorno['Setor'] = $this->validationErrors;
			}
		}
		return $retorno;
	}
	function localiza_setor_importacao($data, $bloqueado = null)
	{
		$retorno = '';
		$codigo_cliente_grupo_economico = $data['codigo_cliente_grupo_economico'];
		//trunca a descricao do setor em 50 caracteres conforme esta na tabela setores
		$descricao 						= substr(trim($data['setor_descricao']), 0, 50);
		//verificação do grupo economico enviado da pagina de importacao
		if (!empty($codigo_cliente_grupo_economico)) {
			//verificação da descricao do setor enviada da planilha
			if (!empty($descricao)) {
				//verifica se o setor já está cadastrado/ativo na base
				$conditions = array(
					"Setor.codigo_cliente" => $codigo_cliente_grupo_economico,
					"(Setor.descricao = '" . ($descricao) . "')",
					"Setor.ativo" => 1
				);
				$fields = array(
					'Setor.codigo', 'Setor.descricao', 'Setor.codigo_cliente'
				);
				$recursive = -1;
				$dados = $this->find('first', compact('conditions', 'fields', 'recursive'));
				if (empty($dados)) {
					$conditions = array(
						"Setor.codigo_cliente" => $codigo_cliente_grupo_economico,
						"(Setor.descricao = '" . Comum::trata_nome(($descricao)) . "')",
						"Setor.ativo" => 1
					);
					$dados = $this->find('first', compact('conditions', 'fields', 'recursive'));
				}
				if (!empty($dados)) {
					//se o setor já existe na base, retorna os dados dele para a validação principal
					$retorno['Dados'] = $dados;
				} else {
					// se o setor não existe na base, o mesmo será cadastrado SE a unidade não se encontra bloqueada
					if (!$bloqueado) {
						//monta array para incluir setor
						$dados_setor = array(
							'Setor' => array(
								'descricao' => $descricao,
								'codigo_cliente' => $data['codigo_cliente_grupo_economico']
							)
						);
						// inclui/atualiza setor
						$retorno_unidade_setor = $this->importacao_setor_unidade($dados_setor);
						// se não existe retorno então não incluiu/atualizou
						if (!empty($retorno_unidade_setor)) {
							if (empty($retorno_unidade_setor['Setor'])) {
								$retorno['Erro'] = array('Setor' => array('codigo_setor' => utf8_decode('Nao foi possivel cadastrar o setor: ') . $descricao));
							} else {
								foreach ($retorno_unidade_setor['Setor'] as $campo => $erro) {
									$retorno_erro['Setor'][$campo] = utf8_decode($erro);
								}
								$retorno['Erro'] = $retorno_erro;
							}
						} else {
							// se incluiu/atualizou, faz o find denovo e retorna os dados do setor para a validação principal
							$dados_setor = $this->find('first', compact('conditions', 'fields'));
							$retorno['Dados'] = $dados_setor;
						}
					} else {
						// se a unidade se encontra bloqueada, retorna erro e não cadastra o setor
						$retorno['Erro'] = array('Setor' => array('codigo_setor' => utf8_decode('A unidade encontra-se bloqueada, logo nao foi possivel incluir o novo setor.')));
					}
				}
			} else {
				$retorno['Erro'] = array('Setor' => array('descricao' => utf8_decode('Descricao do setor nao foi enviada!')));
			}
		} else {
			$retorno['Erro'] = array('Setor' => array('codigo_cliente' => utf8_decode('O codigo do grupo economico nao consta na importacao!')));
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
	 * Retorno da lista de setores a partir do(s) codigo_cliente fornecido(s)
	 * 
	 * @param array $codigo_cliente
	 * @return array
	 * 
	 *	ex. resposta
	 *
	 *     "79928": [{
	 *				"codigo": 606999,
	 *				"descricao": "83000100-ADMINISTRATIVO-474.0001"
	 *			}, {
	 *				"codigo": 606990,
	 *				"descricao": "83000100-BELO HORIZONTE-VENDAS-474.0001"
	 * 			}, {
	 * 				"codigo": 606998,
	 *				"descricao": "83000100-SAO PAULO-VENDAS-474.0001"
	 *			}
	 *		}]
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

				$linha = $list[$key]['Setor'];
				$setor = $linha;

				if (isset($setor['codigo_cliente']))
					unset($setor['codigo_cliente']);

				$dados[$linha['codigo_cliente']][] = $setor;
			}
		}

		return $dados;
	} //FINAL FUNCTION obterLista	

}
