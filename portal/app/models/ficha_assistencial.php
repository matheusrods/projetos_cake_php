<?php
class FichaAssistencial extends AppModel
{

	public $name            = 'FichaAssistencial';
	public $tableSchema     = 'dbo';
	public $databaseTable   = 'RHHealth';
	public $useTable        = 'fichas_assistenciais';
	public $primaryKey      = 'codigo';
	public $actsAs		   	= array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_ficha_assistencial'));
	public $recursive       = -1;

	public $validate = array(
		'codigo_pedido_exame' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a codigo do pedido de exame.',
				'required' => true
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Ja existe uma ficha assistencial para este pedido de exame!',
				'on' => 'create',
			),
		),
		'codigo_medico' => array(
			'rule' => 'notEmpty',
			'message' => 'Este campo é obrigatório',
			'required' => true
		),
		'hora_inicio_atendimento' => array(
			'rule' => 'notEmpty',
			'message' => 'Este campo é obrigatório',
			'required' => true
		),
		'hora_fim_atendimento' => array(
			'rule' => 'notEmpty',
			'message' => 'Este campo é obrigatório',
			'required' => true
		)
	);

	public $hasMany = array(
		'FichaAssistencialResposta' => array(
			'className'    => 'FichaAssistencialResposta',
			'foreignKey'    => 'codigo_ficha_assistencial'
		),
		'FichaAssistencialFarmaco' => array(
			'className' => 'FichaAssistencialFarmaco',
			'foreignKey' => 'codigo_ficha_assistencial'
		)
	);

	public $belongsTo = array(
		'PedidoExame' => array(
			'ClassName' => 'PedidoExame',
			'foreignKey' => 'codigo_pedido_exame'
		),
		'Medico' => array(
			'ClassName' => 'Medico',
			'foreignKey' => 'codigo_medico'
		),
		'Atestado' => array(
			'ClassName' => 'Atestado',
			'foreignKey' => 'codigo_atestado'
		)
	);

	public function converteFiltroEmCondition($data)
	{
		$conditions = array();

		if (!empty($data['codigo_cliente']))
			$conditions['PedidoExame.codigo_cliente'] = $data['codigo_cliente'];

		if (!empty($data['codigo']))
			$conditions['FichaAssistencial.codigo'] = $data['codigo'];

		if (!empty($data['codigo_pedido_exame']))
			$conditions['FichaAssistencial.codigo_pedido_exame'] = $data['codigo_pedido_exame'];

		if (!empty($data['nome_funcionario']))
			$conditions['Funcionario.nome LIKE'] = '%' . $data['nome_funcionario'] . '%';

		if (!empty($data['nome_medico']))
			$conditions['Medico.nome LIKE'] = '%' . $data['nome_medico'] . '%';

		return $conditions;
	} //FINAL FUNCTION converteFiltroEmCondition

	public function converteFiltroPedidoExameEmCondition($data)
	{
		$conditions = array();

		if (!empty($data['codigo_fornecedor']))
			$conditions['ItemPedidoExame.codigo_fornecedor'] = $data['codigo_fornecedor'];

		if (!empty($data['codigo']))
			$conditions['PedidoExame.codigo'] = $data['codigo'];

		if (!empty($data['codigo_cliente']))
			$conditions['Cliente.codigo'] = $data['codigo_cliente'];

		if (!empty($data['codigo_funcionario']))
			$conditions['Funcionario.nome LIKE'] = '%' . $data['codigo_funcionario'] . '%';

		return $conditions;
	} //FINAL FUNCTION converteFiltroPedidoExameEmCondition

	public function carregar($codigo)
	{
		$dados = $this->find('first', array(
			'conditions' => array(
				$this->name . '.codigo' => $codigo
			)
		));
		return $dados;
	} //FINAL FUNCTION carregar

	public function montaQuestoes($dados_funcionario = array())
	{
		$options['conditions']['FichaAssistencialGQ.ativo'] = 1;
		$containConditions['FichaAssistencialQuestao.codigo_ficha_assistencial_questao'] =  array(NULL, '', 0);

		if (!empty($dados_funcionario['sexo'])) {
			$containConditions['FichaAssistencialQuestao.exibir_se_sexo'] = array($dados_funcionario['sexo'], NULL, '');
		}

		if (!empty($dados_funcionario['data_nascimento'])) {
			//Data do nascimento
			list($dia, $mes, $ano) = explode('/', $dados_funcionario['data_nascimento']);

			//Data atual
			$hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

			//calcula idade
			$nascimento = mktime(0, 0, 0, $mes, $dia, $ano);
			$idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);

			$containConditions['OR']['FichaAssistencialQuestao.exibir_se_idade_maior_que <'] = $idade;
			$containConditions['OR']['AND']['FichaAssistencialQuestao.exibir_se_idade_maior_que'] = NULL;

			$containConditions['OR']['FichaAssistencialQuestao.exibir_se_idade_menor_que >'] = $idade;
			$containConditions['OR']['AND']['FichaAssistencialQuestao.exibir_se_idade_menor_que'] = NULL;
		}

		$options['fields'] = array(
			'FichaAssistencialGQ.descricao',
		);

		// faz um select de forma recursiva
		$options['contain'] = array(
			'FichaAssistencialQuestao' => array(
				'conditions' => $containConditions,
				'fields' => array(
					'FichaAssistencialQuestao.codigo',
					'FichaAssistencialQuestao.tipo',
					'FichaAssistencialQuestao.campo_livre_label',
					'FichaAssistencialQuestao.observacao',
					'FichaAssistencialQuestao.obrigatorio',
					'FichaAssistencialQuestao.ajuda',
					'FichaAssistencialQuestao.span',
					'FichaAssistencialQuestao.label',
					'FichaAssistencialQuestao.conteudo',
					'FichaAssistencialQuestao.parentesco_ativo',
					'FichaAssistencialQuestao.quebra_linha',
					'FichaAssistencialQuestao.opcao_selecionada',
					'FichaAssistencialQuestao.opcao_abre_menu_escondido',
					'FichaAssistencialQuestao.farmaco_ativo',
					'FichaAssistencialQuestao.opcao_exibe_label',
					'FichaAssistencialQuestao.multiplas_cids_ativo'
				)
			),
			'FichaAssistencialQuestao.FichaAssistencialSubQuest'
		);

		$questoes = $this->FichaAssistencialResposta->FichaAssistencialQuestao->FichaAssistencialGQ->find('all', $options);

		// valida os campos obrigatorios ===============
		foreach ($questoes as $key => $grupoQuestao) {

			foreach ($grupoQuestao['FichaAssistencialQuestao'] as $key => $questao) {
				if ($questao['obrigatorio']) {
					$this->FichaAssistencialResposta->validate[$questao['codigo'] . '_resposta'] = array(
						'rule' => 'notEmpty',
						'message' => 'Este campo é obrigatório',
						'required' => true
					);
				}
				if (!empty($questao['FichaAssistencialSubQuest'])) {
					foreach ($questao['FichaAssistencialSubQuest'] as $key => $subquestao) {
						if ($subquestao['obrigatorio']) {
							$this->FichaAssistencialResposta->validate[$subquestao['codigo'] . '_resposta'] = array(
								'rule' => 'notEmpty',
								'message' => 'Este campo é obrigatório',
								'required' => true
							);
						}
					}
				}
			}
		} //FINAL FOREACH $questoes
		//===============================================

		return $questoes;
	} //FINAL FUNCTION montaQuestoes

	public function incluir($data)
	{
		// organiza a variavel para salvar recursivamente
		if ($retorno = $this->organizaVariavelRecursiva($data)) {
			$data['FichaAssistencialResposta'] = $retorno;
		} else {
			unset($data['FichaAssistencialResposta']);
		}
		//mata a validação antes de salvar (neste ponto todos os campos já foram validados)
		$this->FichaAssistencialResposta->validate = array();
		// salva de forma recursiva
		if (parent::incluirTodos($data)) {
			return true;
		} else {
			return false;
		}
	} //FINAL FUNCTION incluir

	public function editar($data)
	{

		// obtem os ids das questoes antigas para serem excluidas se o salvamento das novas retornar sucesso
		$codigoQuestoesExcluir = $this->FichaAssistencialResposta->find(
			'list',
			array(
				'conditions' => array(
					'FichaAssistencialResposta.codigo_ficha_assistencial' => $data['FichaAssistencial']['codigo']
				)
			)
		);

		// organiza a variavel para salvar recursivamente
		if ($retorno = $this->organizaVariavelRecursiva($data)) {
			$data['FichaAssistencialResposta'] = $retorno;
		} else {
			unset($data['FichaAssistencialResposta']);
		}

		//mata a validação antes de salvar (neste ponto todos os campos já foram validados)
		$this->FichaAssistencialResposta->validate = array();

		// salva de forma recursiva
		if (parent::atualizarTodos($data)) {

			// se salvamento for sucesso exclua as antigas respostas
			if (!empty($codigoQuestoesExcluir)) {
				if (!$this->FichaAssistencialResposta->deleteAll(array('FichaAssistencialResposta.codigo' => $codigoQuestoesExcluir))) {
					return false;
				}
			}

			return true;
		} else {
			return false;
		}
	} //FINAL FUNCTION editar

	public function jsonRemoveUnicodeSequences($struct)
	{
		return preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", json_encode($struct));
	} //FINAL FUNCTION jsonRemoveUnicodeSequences

	public function organizaVariavelRecursiva($data)
	{

		// separa os campos livres
		$camposLivres = $data['FichaAssistencialResposta']['campo_livre'];
		unset($data['FichaAssistencialResposta']['campo_livre']);
		// separa os campos de multiplas doenças
		$cid10 = $data['FichaAssistencialResposta']['cid10'];
		unset($data['FichaAssistencialResposta']['cid10']);

		$parentescos = $data['FichaAssistencialResposta']['parentesco'];
		unset($data['FichaAssistencialResposta']['parentesco']);

		$c = 0;
		//organiza a variavel para poder salvar recursivamente
		$valores = array();
		foreach ($data['FichaAssistencialResposta'] as $key => $resposta) {

			$codigoResposta = reset((explode('_', $key)));

			if (isset($data['FichaAssistencial']['codigo']) and !empty($data['FichaAssistencial']['codigo'])) {
				$deletar = array(
					'FichaAssistencialFarmaco.codigo_ficha_assistencial' => $data['FichaAssistencial']['codigo'],
					'FichaAssistencialFarmaco.codigo_ficha_assistencial_questao' => $codigoResposta
				);

				if (!$this->FichaAssistencialFarmaco->deleteAll($deletar)) {
					throw new Exception('Problema ao deletar Ficha Assistencial Farmaco');
				}
			}

			if ($resposta != '') {
				$valores[$c]['codigo_ficha_assistencial_questao'] = $codigoResposta;

				// preenche os "campos livres" do form na variavel
				if (!empty($camposLivres)) {
					foreach ($camposLivres as $key2 => $campoLivre) {
						if ($key2 == $codigoResposta) {
							if (is_array($campoLivre)) {
								$valores[$c]['campo_livre'] = json_encode($campoLivre);
							} else {
								$valores[$c]['campo_livre'] = $campoLivre;
							}
							break;
						} //FINAL SE $key2 É IGUAL A $codigoResposta
					} //FINAL FOREACH $camposLivres
				} //FINAL SE $camposLivres NÃO ESTÁ VAZIO


				// preenche as multiplas doenças
				if (!empty($cid10)) {
					foreach ($cid10 as $key3 => $val) {
						if ($key3 == $codigoResposta) {
							$valores[$c]['campo_livre'] = json_encode(array_values($val));
							break;
						} //FINAL IF $key == $codigoResposta
					} //FINAL FOREACH $cid10
				} //FINAL IF NOT EMPTY $cid10

				// preenche os parentescos
				if (!empty($parentescos)) {
					foreach ($parentescos as $key4 => $val) {
						if ($key4 == $codigoResposta) {
							$valores[$c]['parentesco'] = $val;
							break;
						}
					} //FINAL FOREACH $parentescos
				} //FINAL SE $parentescos É DIFERENTE DE VAZIO

				// trata as variaveis covertendo-as em json caso necessario
				if (is_array($resposta)) {
					if (count($resposta) < 2) {
						$valores[$c]['resposta'] = $resposta[key($resposta)];
					} else if (count($resposta) == 2) {
						$valores[$c]['observacao'] = $resposta['observacao'];
						$valores[$c]['resposta'] = $resposta['exibe'];
					} else {
						$valores[$c]['resposta'] = stripslashes(self::jsonRemoveUnicodeSequences($resposta));
					}
				} else {
					$valores[$c]['resposta'] = $resposta;
				}
			} //FINAL SE $resposta É DIFERENTE DE VAZIO

			$c++;
		} //FINAL FOREACH $data['FichaAssistencialResposta']

		//limpa campos para geração do relatorio pdf corretamente
		//verifica se está ativo os itens doenças do coração: 15, problemas respiratorios: 49, doenças nos rins: 61, doenças no figado: 70, doenças psiquiatricas: 126
		$parentes_codigos = array();
		foreach ($valores as $chaveVal => $valArray) {

			//verifica se existe algum desses indices com a resposta 0
			switch ($valArray["codigo_ficha_assistencial_questao"]) {
				case '35': //doenças do coração
				case '49': //problemas respiratorios
				case '61': //doenças nos rins
				case '70': //doenças no figado
				case '81': //eplilepsia
				case '109': //doenças do estomago
				case '117': //problemas de visão
				case '122': //problemas de audição
				case '122': //problemas de audição
				case '126': //doenças psiquiatricas
				case '137': //câncer

					$parentes_codigos = array();

					//verifica se a resposta esta como nao=0
					if ($valArray['resposta'] == '0') {
						//pega os filhos/parentes
						$parentes_codigos = $this->FichaAssistencialResposta->FichaAssistencialQuestao->find('list', array('conditions' => array('FichaAssistencialQuestao.codigo_ficha_assistencial_questao' => $valArray["codigo_ficha_assistencial_questao"])));
					} //fim verificacao da resposta

					break;
			} //fim switch
			//verifica se existe parentes codigos
			if (!empty($parentes_codigos)) {
				//verifica se existe o codigo que esta varrendo dentro do parentes codigos
				if (in_array($valArray["codigo_ficha_assistencial_questao"], $parentes_codigos)) {
					//mata a chave inteira
					unset($valores[$chaveVal]);
				}
			}
		} //fim foreach

		if (!empty($valores)) {
			return $valores;
		} else {
			return false;
		}
	} //FINAL FUNCTION organizaVariavelRecursiva

	public function obtemDadosComplementares($codigoPedidoExame)
	{
		$options['conditions'] = array(
			'PedidoExame.codigo' => $codigoPedidoExame
		);

		//esta query obtem todos os medicos disponiveis de todos os fornecedores utilizados no pedido de exame formando um unico grupo
		$medicos = $this->query('
			SELECT Medico.codigo, Medico.nome 
				FROM medicos Medico 
			WHERE Medico.ativo = 1 
				AND Medico.codigo IN (
						SELECT FornecedorMedico.codigo_medico 
							FROM fornecedores_medicos FornecedorMedico 
						WHERE FornecedorMedico.codigo_fornecedor IN (
								SELECT ItemPedidoExame.codigo_fornecedor 
									FROM itens_pedidos_exames ItemPedidoExame 
								WHERE ItemPedidoExame.codigo_pedidos_exames = ' . $codigoPedidoExame . '
								)
				) 
		');

		$values = array();
		foreach ($medicos as $key => $medico) {
			$values[$medico[0]['codigo']] = $medico[0]['nome'];
		}
		//===================================================

		$this->PedidoExame->virtualFields = array(
			'tipo_pedido_exame' => 'CASE 
			WHEN exame_admissional = 1 THEN \'Exame admissional\'
			WHEN exame_periodico = 1 THEN \'Exame pediódico\'
			WHEN exame_demissional = 1 THEN \'Exame demissional\'
			WHEN exame_retorno = 1 THEN \'Retorno\'
			WHEN exame_mudanca = 1 THEN \'Mudança de riscos ocupacionais\'
			WHEN exame_monitoracao = 1 THEN \'Monitoração Pontual\'
			WHEN qualidade_vida = 1 THEN \'Qualidade de vida\'
			END',
			'setor' => "(SELECT descricao FROM RHHealth.dbo.setores where codigo = (SELECT TOP 1 codigo_setor FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo AND PedidoExame.codigo_func_setor_cargo = codigo  ORDER BY 1 DESC))",
			'cargo' => "(SELECT descricao FROM RHHealth.dbo.cargos where codigo = (SELECT TOP 1 codigo_cargo FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo  AND PedidoExame.codigo_func_setor_cargo = codigo ORDER BY 1 DESC))"
		);

		$options['joins'][] = array(
			'table' => 'cliente_funcionario',
			'alias' => 'ClienteFuncionario',
			'type' => 'INNER',
			'conditions' => array(
				'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario'
			)
		);
		$options['joins'][] = array(
			'table' => 'grupos_economicos_clientes',
			'alias' => 'GrupoEconomicoCliente',
			'type' => 'INNER',
			'conditions' => array(
				'GrupoEconomicoCliente.codigo_cliente = ClienteFuncionario.codigo_cliente_matricula'
			)
		);
		$options['joins'][] = array(
			'table' => 'cliente',
			'alias' => 'Unidade',
			'type' => 'INNER',
			'conditions' => array(
				'Unidade.codigo = GrupoEconomicoCliente.codigo_cliente'
			)
		);
		$options['joins'][] = array(
			'table' => 'grupos_economicos',
			'alias' => 'GrupoEconomico',
			'type' => 'INNER',
			'conditions' => array(
				'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
			)
		);
		$options['joins'][] = array(
			'table' => 'cliente',
			'alias' => 'Empresa',
			'type' => 'INNER',
			'conditions' => array(
				'Empresa.codigo = GrupoEconomico.codigo_cliente'
			)
		);
		$options['joins'][] = array(
			'table' => 'funcionarios',
			'alias' => 'Funcionario',
			'type' => 'INNER',
			'conditions' => array(
				'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
			)
		);

		$options['fields'] = array(
			'PedidoExame.codigo',
			'PedidoExame.tipo_pedido_exame',
			'(SELECT FLOOR(DATEDIFF(DAY, Funcionario.data_nascimento, GETDATE()) / 365.25)) AS idade',
			'(CASE Funcionario.sexo WHEN \'F\' THEN \'Feminino\' ELSE \'Masculino\' END) AS sexo',
			'Funcionario.sexo',
			'Funcionario.nome',
			'Funcionario.cpf',
			'Funcionario.data_nascimento',
			'Funcionario.codigo',
			'ClienteFuncionario.codigo',
			'ClienteFuncionario.codigo_cliente_matricula',
			'ClienteFuncionario.admissao',
			'GrupoEconomicoCliente.codigo',
			'GrupoEconomicoCliente.codigo_cliente',
			'Empresa.razao_social',
			'GrupoEconomico.codigo',
			'GrupoEconomico.codigo_cliente',
			'Unidade.razao_social',
			//'Setor.descricao',
			//'Cargo.descricao'
			'setor',
			'cargo'
		);

		$dados = $this->PedidoExame->find('first', $options);
		$dados['Medico'] = $values;
		unset($values);

		return $dados;
	} //FINAL FUNCTION obtemDadosComplementares

	public function montaRespostas($codigo = null)
	{

		// organiza as respostas em um array no padrão que a view necessita para se relacionar com $this->data
		$respostas = $this->FichaAssistencialResposta->find(
			'all',
			array(
				'conditions' => array(
					'FichaAssistencialResposta.codigo_ficha_assistencial' => $codigo
				)
			)
		);

		$dados = array();
		foreach ($respostas as $key => $value) {
			if ($this->isJson($value['FichaAssistencialResposta']['resposta'])) {
				$value['FichaAssistencialResposta']['resposta'] = (array)json_decode($value['FichaAssistencialResposta']['resposta']);
				if (count($value['FichaAssistencialResposta']['resposta']) == 1 && empty($value['FichaAssistencialResposta']['observacao'])) {
					$value['FichaAssistencialResposta']['resposta'] = $value['FichaAssistencialResposta']['resposta'][key($value['FichaAssistencialResposta']['resposta'])];
				}
			}

			$dados['FichaAssistencialResposta'][$value['FichaAssistencialResposta']['codigo_ficha_assistencial_questao'] . '_resposta'] = $value['FichaAssistencialResposta']['resposta'];

			if (isset($value['FichaAssistencialResposta']['resposta'][0])) {
				$dados['FichaAssistencialResposta'][$value['FichaAssistencialResposta']['codigo_ficha_assistencial_questao'] . '_resposta']['exibe'] = $value['FichaAssistencialResposta']['resposta'][0];
			}

			if (isset($value['FichaAssistencialResposta']['observacao'])) {
				$dados['FichaAssistencialResposta'][$value['FichaAssistencialResposta']['codigo_ficha_assistencial_questao'] . '_resposta']['observacao'] = $value['FichaAssistencialResposta']['observacao'];
			}

			if (!empty($value['FichaAssistencialResposta']['campo_livre'])) {
				if ($this->isJson($value['FichaAssistencialResposta']['campo_livre'])) {
					$dados['FichaAssistencialResposta']['campo_livre'][$value['FichaAssistencialResposta']['codigo_ficha_assistencial_questao']] = (array)$this->jsonToArray($value['FichaAssistencialResposta']['campo_livre']);
				} else {
					$dados['FichaAssistencialResposta']['campo_livre'][$value['FichaAssistencialResposta']['codigo_ficha_assistencial_questao']] = $value['FichaAssistencialResposta']['campo_livre'];
				}
			}

			if (!empty($value['FichaAssistencialResposta']['parentesco'])) {

				$dados['FichaAssistencialResposta']['parentesco'][$value['FichaAssistencialResposta']['codigo_ficha_assistencial_questao']] = $value['FichaAssistencialResposta']['parentesco'];
			}
		} //FINAL FOREACH respostas

		return $dados;
	} //FINAL FUNCTION montaRespostas

	public function verificaParecer($codigo_pedido_exame = null)
	{
		$return = 0;
		if (!is_null($codigo_pedido_exame)) {
			$return = $this->query('
				SELECT
				CASE WHEN 
				(
				SELECT count(pe.codigo) FROM pedidos_exames pe
				INNER JOIN itens_pedidos_exames ipe
				ON (ipe.codigo_pedidos_exames = pe.codigo)
				WHERE pe.codigo = ' . $codigo_pedido_exame . '
				) > ( 
				SELECT count(pe.codigo) FROM pedidos_exames pe
				INNER JOIN itens_pedidos_exames ipe
				ON (ipe.codigo_pedidos_exames = pe.codigo)
				INNER JOIN itens_pedidos_exames_baixa ipeb
				ON (ipeb.codigo_itens_pedidos_exames = ipe.codigo)
				WHERE pe.codigo = ' . $codigo_pedido_exame . '
				)
				THEN 0
				WHEN 
				(
				SELECT count(pe.codigo) FROM pedidos_exames pe
				INNER JOIN itens_pedidos_exames ipe
				ON (ipe.codigo_pedidos_exames = pe.codigo)
				WHERE pe.codigo = ' . $codigo_pedido_exame . '
				) = ( 
				SELECT count(pe.codigo) FROM pedidos_exames pe
				INNER JOIN itens_pedidos_exames ipe
				ON (ipe.codigo_pedidos_exames = pe.codigo)
				INNER JOIN itens_pedidos_exames_baixa ipeb
				ON (ipeb.codigo_itens_pedidos_exames = ipe.codigo)
				WHERE pe.codigo = ' . $codigo_pedido_exame . '
				)
				THEN 1
				END
				AS todos_pedidos_baixados,

				CASE WHEN (SELECT	ri.risco_caracterizado_por_altura
						FROM rhhealth.dbo.pedidos_exames pe
							INNER JOIN rhhealth.dbo.funcionario_setores_cargos fsc ON fsc.codigo = pe.codigo_func_setor_cargo
							INNER JOIN rhhealth.dbo.cliente_funcionario cf ON cf.codigo = fsc.codigo_cliente_funcionario
							INNER JOIN RHHealth.dbo.cliente c ON c.codigo = cf.codigo_cliente_matricula
							INNER JOIN RHHealth.dbo.cargos cg ON cg.codigo = fsc.codigo_cargo
							INNER JOIN RHHealth.dbo.setores st ON st.codigo = fsc.codigo_setor
							INNER JOIN RHHealth.dbo.clientes_setores cs ON (cs.codigo_setor = st.codigo )
							INNER JOIN RHHealth.dbo.grupo_exposicao ge  ON (ge.codigo_cargo = cg.codigo AND ge.codigo_cliente_setor = cs.codigo)
							INNER JOIN RHHealth.dbo.grupos_exposicao_risco ger ON (ger.codigo_grupo_exposicao = ge.codigo)
							INNER JOIN RHHealth.dbo.riscos ri ON (ri.codigo = ger.codigo_risco)
						WHERE pe.codigo = ' . $codigo_pedido_exame . ' and ri.risco_caracterizado_por_altura is not null and ri.risco_caracterizado_por_altura <> 0) = 1 THEN \'S\' ELSE \'N\' END AS risco_por_altura,
					
				CASE WHEN (SELECT	ri.risco_caracterizado_por_trabalho_confinado 
						FROM rhhealth.dbo.pedidos_exames pe
							INNER JOIN rhhealth.dbo.funcionario_setores_cargos fsc ON fsc.codigo = pe.codigo_func_setor_cargo
							INNER JOIN rhhealth.dbo.cliente_funcionario cf ON cf.codigo = fsc.codigo_cliente_funcionario
							INNER JOIN RHHealth.dbo.cliente c ON c.codigo = cf.codigo_cliente_matricula
							INNER JOIN RHHealth.dbo.cargos cg ON cg.codigo = fsc.codigo_cargo
							INNER JOIN RHHealth.dbo.setores st ON st.codigo = fsc.codigo_setor
							INNER JOIN RHHealth.dbo.clientes_setores cs ON (cs.codigo_setor = st.codigo )
							INNER JOIN RHHealth.dbo.grupo_exposicao ge  ON (ge.codigo_cargo = cg.codigo AND ge.codigo_cliente_setor = cs.codigo)
							INNER JOIN RHHealth.dbo.grupos_exposicao_risco ger ON (ger.codigo_grupo_exposicao = ge.codigo)
							INNER JOIN RHHealth.dbo.riscos ri ON (ri.codigo = ger.codigo_risco)
						WHERE pe.codigo = ' . $codigo_pedido_exame . ' and ri.risco_caracterizado_por_trabalho_confinado is not null and ri.risco_caracterizado_por_trabalho_confinado <> 0) = 1 then \'S\' ELSE \'N\' END  AS risco_por_confinamento
				');
			$return = $return[0][0];
		}
		return $return;
	} //FINAL FUNCTION verificaParecer

	private function isJson($json)
	{
		json_decode($json);
		return (json_last_error() == JSON_ERROR_NONE);
	} //FINAL FUNCTION isJson

	private function jsonToArray($data = null)
	{
		if (!is_null($data)) {
			$json = (array)json_decode($data);
			foreach ($json as $key => $value) {
				if (is_object($value)) {
					$json[$key] = (array)$value;
				} else {
					$json[$key] = $value;
				}
			}
			$data = $json;
		}
		return $data;
	} //FINAL FUNCTION jsonToArray


	/**
	 * [criaTabelaTemporariaReceitaMedica description]
	 * 
	 * para colocar os registros na tabela como temporarios
	 * é usado para leitura no jasper
	 * 
	 * @param  [type] $codigo_ficha_assistencial [description]
	 * @return [type]                            [description]
	 */
	public function criaTabelaTemporariaReceitaMedica($codigo_ficha_assistencial)
	{

		//pega o codigo do usuario inclusao da ficha assisntencial
		$ficha = $this->find('first', array('conditions' => array('codigo' => $codigo_ficha_assistencial)));
		$codigo_usuario = (!empty($ficha['FichaAssistencial']['codigo_usuario_inclusao'])) ? $ficha['FichaAssistencial']['codigo_usuario_inclusao'] : 1;


		//deleta todos os registros da ficha com o grupo codigo questa 10 da tabela fichas_assistencias_farmacos
		$deleteFarmaco = 'DELETE FROM fichas_assistenciais_farmacos WHERE codigo_ficha_assistencial_questao = 177 AND codigo_ficha_assistencial = ' . $codigo_ficha_assistencial;
		$this->query($deleteFarmaco);

		//busca a resposta da receita
		$resposta = $this->FichaAssistencialResposta->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'codigo_ficha_assistencial_questao' => '177',
				'codigo_ficha_assistencial' => $codigo_ficha_assistencial
			)
		));
		//trata o json gravado no campo livre
		$resposta_json 	= json_decode($resposta['FichaAssistencialResposta']['campo_livre']);
		$resposta_obs	= $resposta['FichaAssistencialResposta']['observacao'];

		//variavel farmaco
		$farmaco = array();

		// grava os dados na tabela fichas_assistencias_farmacos
		foreach ($resposta_json as $key => $dados) {
			$resposta_observacao = '';
			if (isset($dados->observacao)) {
				if (!empty($dados->observacao)) {
					$resposta_observacao =  $dados->observacao;
				}
			}

			//seta os dados para a farmaco			
			$farmaco[$key]['FichaAssistencialFarmaco']['codigo_ficha_assistencial'] = $codigo_ficha_assistencial;
			$farmaco[$key]['FichaAssistencialFarmaco']['codigo_ficha_assistencial_resposta'] = $resposta['FichaAssistencialResposta']['codigo'];
			$farmaco[$key]['FichaAssistencialFarmaco']['codigo_ficha_assistencial_questao'] = '177';
			//pega os dados da tabela que voltou com objeto
			$farmaco[$key]['FichaAssistencialFarmaco']['farmaco'] = (isset($dados->farmaco)) ? $dados->farmaco : null;
			$farmaco[$key]['FichaAssistencialFarmaco']['posologia'] = (isset($dados->posologia)) ? $dados->posologia : null;
			$farmaco[$key]['FichaAssistencialFarmaco']['dose_diaria'] = (isset($dados->dose_diaria)) ? $dados->dose_diaria : null;
			$farmaco[$key]['FichaAssistencialFarmaco']['duracao'] = (isset($dados->duracao)) ? $dados->duracao : null;
			$farmaco[$key]['FichaAssistencialFarmaco']['tipo_uso'] = (isset($dados->tipo_uso)) ? $dados->tipo_uso : null;
			$farmaco[$key]['FichaAssistencialFarmaco']['prescricao'] = (isset($dados->prescricao)) ? $dados->prescricao : null;
			$farmaco[$key]['FichaAssistencialFarmaco']['aprazamento'] = (isset($dados->aprazamento)) ? $dados->aprazamento : null;
			$farmaco[$key]['FichaAssistencialFarmaco']['observacao'] = $resposta_observacao;

			$farmaco[$key]['FichaAssistencialFarmaco']['codigo_usuario_inclusao'] = $codigo_usuario;
		} //fim foreach

		// debug($farmaco);exit;

		//grava os dados na ficha assistencial farmaco para leitura do relatorio
		return $this->FichaAssistencialFarmaco->incluirTodos($farmaco);
	} //fim criaTabelaTemporariaReceitaMedica


	public function criaTabelaTemporariaFichaAssistencial($codigo_ficha_assistencial)
	{
		//verifica se tem usuario logado
		$ficha = $this->find('first', array('conditions' => array('codigo' => $codigo_ficha_assistencial)));
		$codigo_usuario = $ficha['FichaAssistencial']['codigo_usuario_inclusao'];

		########################### EXCLUI DADOS ANTIGOS QUE NÃO SEJAM DAS QUESTOES 177 QUE É PRESCRIÇÃO ##########################		
		$deleteFarmaco = 'DELETE FROM fichas_assistenciais_farmacos 
						WHERE codigo_ficha_assistencial_questao <> 177
							AND codigo_ficha_assistencial = ' . $codigo_ficha_assistencial;
		$this->query($deleteFarmaco);

		// OBTEM OS DADOS PARA SALVAR NA TABELA TEMPORÁRIA, O CONTEUO SERIALIZADO
		// $dados = $this->FichaAssistencialResposta->find('sql', array(
		// 	'recursive' => -1,
		// 	'joins' => array(
		// 		array(
		// 			'table' => 'fichas_assistenciais_questoes',
		// 			'alias' => 'FichaAssistencialQuestao',
		// 			'type' => 'INNER',
		// 			'conditions' => array(
		// 				'FichaAssistencialQuestao.codigo = FichaAssistencialResposta.codigo_ficha_assistencial_questao'
		// 				) 
		// 			)
		// 		),
		// 	'conditions' => array(
		// 		'FichaAssistencialResposta.codigo_ficha_assistencial' => $codigo_ficha_assistencial,
		// 		'OR' => array(
		// 			'FichaAssistencialResposta.campo_livre NOT' => null,
		// 			'AND' => array(
		// 				array(
		// 					'FichaAssistencialResposta.resposta LIKE ? ESCAPE ?' => array('\\[%', '\\')
		// 					),
		// 				array(
		// 					'FichaAssistencialResposta.resposta LIKE ? ESCAPE ?' => array('%\\]', '\\')
		// 					)
		// 				)
		// 			)
		// 		),
		// 	'fields' => array(
		// 		'FichaAssistencialQuestao.codigo',
		// 		'FichaAssistencialResposta.codigo',
		// 		'FichaAssistencialResposta.codigo_ficha_assistencial',
		// 		'FichaAssistencialResposta.campo_livre',
		// 		'FichaAssistencialResposta.resposta'
		// 		)
		// 	)
		// );

		$query = "SELECT [FichaAssistencialQuestao].[codigo] AS codigo_fa_questao
					    , [FichaAssistencialResposta].[codigo] AS codigo_fa_resposta
					    , [FichaAssistencialResposta].[codigo_ficha_assistencial] AS codigo_ficha_assistencial
					    , (CONVERT(TEXT, [FichaAssistencialResposta].[campo_livre])) AS campo_livre
					    , (CONVERT(TEXT, [FichaAssistencialResposta].[resposta])) AS resposta
					FROM RHHealth.dbo.[fichas_assistenciais_respostas] AS [FichaAssistencialResposta]
					INNER JOIN [fichas_assistenciais_questoes] AS [FichaAssistencialQuestao]
					    ON ([FichaAssistencialQuestao].[codigo] = [FichaAssistencialResposta].[codigo_ficha_assistencial_questao])
					WHERE [FichaAssistencialResposta].[codigo_ficha_assistencial] = {$codigo_ficha_assistencial}
					    AND (
					        (NOT ([FichaAssistencialResposta].[campo_livre] IS NULL))
					        OR (
					            (
					                ([FichaAssistencialResposta].[resposta] LIKE '\[%' ESCAPE '\')
					                AND ([FichaAssistencialResposta].[resposta] LIKE '%\]' ESCAPE '\')
					                )
					            )
					        );";

		$dados = $this->query($query);

		// debug($dados);exit;

		// MONTA OS DADOS PARA SALVAR NA TABELA TEMPORÁRIA
		$inserir = array();
		foreach ($dados as $key => $dado) {
			// VERIFICA SE O CONTEÚDO É SERIALIZADO
			if (!is_null($dado[0]['campo_livre']) && $this->isJson($dado[0]['campo_livre'])) {

				$jsonToArray = $this->jsonToArray($dado[0]['campo_livre']);

				if (is_int(key($jsonToArray))) { // VERIFICA SE O CONTEÚDO É UM ARRAY COM MULTIPLOS DADOS (FAZ UM LAÇO)

					foreach ($jsonToArray as $key => $value) {

						$inserir[]['FichaAssistencialFarmaco'] = array(
							'codigo_ficha_assistencial' 			=> $dado[0]['codigo_ficha_assistencial'],
							'codigo_ficha_assistencial_resposta' 	=> $dado[0]['codigo_fa_resposta'],
							'doenca' 								=> (isset($value['doenca'])) ? $value['doenca'] : null,
							'farmaco'								=> (isset($value['farmaco'])) ? $value['farmaco'] : null,
							'posologia'								=> (isset($value['posologia'])) ? $value['posologia'] : null,
							'dose_diaria'							=> (isset($value['dose_diaria'])) ? $value['dose_diaria'] : null,
							'duracao'								=> (isset($value['aprazamento'])) ? $value['aprazamento'] : null,
							'codigo_ficha_assistencial_questao'		=> $dado[0]['codigo_fa_questao'],
							'codigo_usuario_inclusao'		=> $codigo_usuario,
						);
					}
				} else { // SE FOR UM ARRAY COM DADO ÚNICO

					$inserir[]['FichaAssistencialFarmaco'] = array(
						'codigo_ficha_assistencial' 			=> $dado[0]['codigo_ficha_assistencial'],
						'codigo_ficha_assistencial_resposta' => $dado[0]['codigo_fa_resposta'],
						'doenca' 						=> null,
						'farmaco'						=> (isset($jsonToArray['farmaco'])) ? $jsonToArray['farmaco'] : null,
						'posologia'						=> (isset($jsonToArray['posologia'])) ? $jsonToArray['posologia'] : null,
						'dose_diaria'					=> (isset($jsonToArray['dose_diaria'])) ? $jsonToArray['dose_diaria'] : null,
						'duracao'						=> (isset($jsonToArray['aprazamento'])) ? $jsonToArray['aprazamento'] : null,
						'codigo_ficha_assistencial_questao'	=> $dado[0]['codigo_fa_questao'],
						'codigo_usuario_inclusao'		=> $codigo_usuario,
					);
				}
			} elseif (!is_null($dado[0]['resposta']) && $this->isJson($dado[0]['resposta'])) {
				$jsonToArray = $this->jsonToArray($dado[0]['resposta']);
				if (is_int(key($jsonToArray))) { // VERIFICA SE O CONTEÚDO É UM ARRAY COM MULTIPLOS DADOS (FAZ UM LAÇO)
					foreach ($jsonToArray as $key => $value) {
						$inserir[]['FichaAssistencialFarmaco'] = array(
							'codigo_ficha_assistencial' 			=> $dado[0]['codigo_ficha_assistencial'],
							'codigo_ficha_assistencial_resposta' => $dado[0]['codigo_fa_resposta'],
							'resposta'   					=> $value,
							'codigo_ficha_assistencial_questao'	=> $dado[0]['codigo_fa_questao'],
							'codigo_usuario_inclusao'		=> $codigo_usuario,
						);
					}
				} else { // SE FOR UM ARRAY COM DADO ÚNICO
					$inserir[]['FichaAssistencialFarmaco'] = array(
						'codigo_ficha_assistencial' 			=> $dado[0]['codigo_ficha_assistencial'],
						'codigo_ficha_assistencial_resposta' => $dado[0]['codigo_fa_resposta'],
						'resposta'						=> $value,
						'codigo_ficha_assistencial_questao'	=> $dado[0]['codigo_fa_questao'],
						'codigo_usuario_inclusao'		=> $codigo_usuario,
					);
				}
			}
		}

		//verifica o inserir para nao inserir dados em branco
		foreach ($inserir as $keyI => $valI) {
			if (empty($valI['FichaAssistencialFarmaco']['doenca']) && empty($valI['FichaAssistencialFarmaco']['farmaco'])) {
				unset($inserir[$keyI]);
			}
		}

		// SALVA OS DADOS NA TABELA TEMPORÁRIA
		return $this->FichaAssistencialFarmaco->incluirTodos($inserir);
	} //FINAL FUNCTION criaTabelaTemporariaFichaAssistencial

}//FINAL CLASS FichaAssistencial