<?php
class Questionario extends AppModel {
	
	public $name = 'Questionario';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'questionarios';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_questionario'));
	public $displayField = 'descricao';

	public $hasMany = array(
		'Questao' => array(
			'className' => 'Questao',
			'foreignKey' => 'codigo_questionario',
			'dependent' => true,
			'conditions' => array('codigo_questao' => NULL)
			),
		'Resultado' => array(
			'className' => 'Resultado',
			'foreignKey' => 'codigo_questionario',
			'dependent' => false,
			)
		);

	public $validate = array(
		'descricao' => array(
			'rule' => 'notEmpty',
			'message' => 'Este campo é obrigatório',
			'allowEmpty' => false
			)
		);

	public function converteFiltroEmCondition($data) 
	{
		$conditions = array();
		if (!empty($data['codigo']))
			$conditions['Questionario.codigo'] = $data['codigo'];

		if (!empty($data['descricao']))
			$conditions['Questionario.descricao LIKE'] = '%'.$data['descricao'].'%';

		return $conditions;
	}

	public function gravar($data) // funcao encapsulada - salva e edita
	{
		// sempre joga o novo dado como primeiro na ordem
		$data[$this->name]['ordem'] = end(($this->find('list', array('order' => 'ordem DESC', 'limit' => 1, 'fields' => 'ordem')))) + 1;
		
		//se existir codigo entao edite, senao salve um novo dado
		if(isset($data[$this->name]['codigo']) && $data[$this->name]['codigo']) {
			return parent::atualizar($data);
		} else {
			return parent::incluir($data);
		}
	}

	//ATENCAO ESTA FUNCAO EM CASCATA EXCLUI O ELEMENTO E TODOS OS SEUS DEPENDENTES
	public function excluiEmCascata($codigo)
	{
		return $this->delete($codigo, true);
	}

	public function obtemQuestionarioeRespostasPorCliente($conditions)
	{
		$joins = array(
			array(
				'table' => 'questoes',
				'alias' => 'Questao',
				'type' => 'INNER',
				'conditions' => array('Questao.codigo_questionario = Questionario.codigo')
				),
			array(
				'table' => 'respostas',
				'alias' => 'Resposta',
				'type' => 'INNER',
				'conditions' => array('Resposta.codigo_questao = Questao.codigo')
				),
			array(
				'table' => 'usuarios_dados',
				'alias' => 'UsuariosDados',
				'type' => 'INNER',
				'conditions' => array(
					'UsuariosDados.codigo_usuario = Resposta.codigo_usuario'
					)
				),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array(
					'Funcionario.cpf = UsuariosDados.cpf'
					)
				),
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => array(
					'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
					)
				),
            array(
                'table' => 'funcionario_setores_cargos',
                'alias' => 'FuncionarioSetorCargo',
                'type' => 'INNER',
                'conditions' => array (
                    "FuncionarioSetorCargo.codigo = (Select TOP 1 codigo from funcionario_setores_cargos Where codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER by codigo DESC)"
                    )
                ),
			array(
				'table' => 'grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomicoCliente.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao'
					)
				),
			array(
				'table' => 'grupos_economicos',
				'alias' => 'GrupoEconomico',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
					)
				),
			);
		$fields = array(
			'Questionario.*',
			'Resposta.codigo_usuario',
			'Resposta.pontos'
			);

		return $this->find('all', array('recursive' => -1, 'conditions' => $conditions, 'joins' => $joins, 'fields' => $fields));
	}

	//MONTA AS QUESTOES EM CASCATA
	public function monta_questoes_em_cascata()
	{
		$conditions['Questionario.status'] = 1;
		$questionarios = $this->find('all', array('conditions' => $conditions));
		foreach ($questionarios as $key => $questionario) {
			foreach ($questionario['Questao'] as $key2 => $questao) {
				$respostas = $this->Questao->find('list', array('recursive' => -1, 'conditions' => array('Questao.codigo_questao' => $questao['codigo'], 'Questao.status' => 1), 'fields' => array('Questao.codigo', 'Questao.label')));
				$questionarios[$key]['Questao'][$key2]['Respostas'] = $respostas;
			}
		}
		return $questionarios;
	}

	public function pula_questao_respondida($proxima_questao = null, $usuario)
	{	
		// VALIDA SE JÁ EXISTE UMA QUESTAO RESPONDIDA COM MESMA RESPOSTA E DEVOLVE A INFORMAÇÃO
		if(!is_null($proxima_questao)) {
			$query = 'WITH CodigoLabelResposta AS (
			SELECT
			DISTINCT(Resposta.codigo_label_questao) AS codigo_label_resposta
			FROM respostas Resposta
			INNER JOIN questoes Questao
			ON(Questao.codigo = Resposta.codigo_questao)
			INNER JOIN questoes Respostas
			ON(Respostas.codigo_questao = Questao.codigo AND Respostas.codigo_label_questao in (
			SELECT 
			Resposta.codigo_label_questao
			from questoes Questao
			inner join questoes Resposta
			on(Resposta.codigo_questao = Questao.codigo)
			where Questao.codigo = '.$proxima_questao.'
			))
			where Resposta.codigo_label_questao in (
			SELECT 
			Resposta2.codigo_label_questao
			from questoes Questao2
			inner join questoes Resposta2
			on(Resposta2.codigo_questao = Questao2.codigo)
			where Questao2.codigo = '.$proxima_questao.'
			AND Questao2.codigo_questionario != Resposta.codigo_questionario
			) 
			AND Questao.codigo_label_questao in (
			SELECT 
			Questao2.codigo_label_questao
			from questoes Questao2
			inner join questoes Resposta2
			on(Resposta2.codigo_questao = Questao2.codigo)
			where Questao2.codigo = '.$proxima_questao.'
			AND Questao2.codigo_questionario != Resposta.codigo_questionario
			)
			AND Resposta.data_inclusao >= DATEADD(day, -30, getdate()) 
			AND [Resposta].[codigo_usuario] = '.$usuario.'  

			--AND (SELECT COUNT(*) FROM (
			--SELECT
			--Resposta.codigo_label_questao as "1",
			--Questao.codigo_label_questao as "2",
			--Questao.label as "3",
			--Respostas.codigo_label_questao as "4",
			--Respostas.label as "5"
			--FROM respostas Resposta
			--INNER JOIN questoes Questao
			--ON(Questao.codigo = Resposta.codigo_questao)
			--INNER JOIN questoes Respostas
			--ON(Respostas.codigo_questao = Questao.codigo AND Respostas.codigo_label_questao in (
			--select 
			--Resposta2.codigo_label_questao
			--from questoes Questao2
			--inner join questoes Resposta2
			--on(Resposta2.codigo_questao = Questao2.codigo)
			--where Questao2.codigo = '.$proxima_questao.'
			--AND Questao2.codigo_questionario != Resposta.codigo_questionario
			--))
			--where Resposta.codigo_label_questao in (
			--select 
			--Resposta2.codigo_label_questao
			--from questoes Questao2
			--inner join questoes Resposta2
			--on(Resposta2.codigo_questao = Questao2.codigo)
			--where Questao2.codigo = '.$proxima_questao.'
			--AND Questao2.codigo_questionario != Resposta.codigo_questionario
			--) 
			--AND Questao.codigo_label_questao in (
			--select 
			--Questao2.codigo_label_questao
			--from questoes Questao2
			--inner join questoes Resposta2
			--on(Resposta2.codigo_questao = Questao2.codigo)
			--where Questao2.codigo = '.$proxima_questao.'
			--AND Questao2.codigo_questionario != Resposta.codigo_questionario
			--)
			--AND Resposta.data_inclusao >= DATEADD(day, -30, getdate()) 
			--AND [Resposta].[codigo_usuario] = '.$usuario.'  
			--group by 
			--Resposta.codigo_label_questao,
			--Questao.codigo_label_questao,
			--Questao.label,
			--Respostas.codigo_label_questao,
			--Respostas.label
			--) as counter) = (
			--SELECT 
			--COUNT(*)
			--from questoes Questao2
			--inner join questoes Resposta2
			--on(Resposta2.codigo_questao = Questao2.codigo)
			--where Questao2.codigo = '.$proxima_questao.'
			--AND Questao2.codigo_questionario != Resposta.codigo_questionario)

			GROUP BY 
			Resposta.codigo_label_questao,
			Questao.codigo_label_questao,
			Questao.label,
			Respostas.codigo_label_questao,
			Respostas.label
			)
			select 
			Resposta.codigo, 
			Resposta.codigo_questao
			--Resposta.codigo_label_questao,
			--Resposta.pontos,
			--LabelResposta.label,
			--LabelQuestao.label,
			--Resposta.codigo_questionario
			from questoes Resposta
			--inner join label_questoes LabelResposta 
			--on(LabelResposta.codigo = Resposta.codigo_label_questao)
			--inner join questoes Questao
			--on(Questao.codigo = Resposta.codigo_questao)
			--inner join label_questoes LabelQuestao
			--on(LabelQuestao.codigo = Questao.codigo_label_questao)
			where Resposta.codigo_questao = '.$proxima_questao.'
			AND Resposta.codigo_label_questao = (select codigo_label_resposta from CodigoLabelResposta);';
			$return = $this->query($query);
		} else {
			$return	= false;
		}
		return $return;

	}

}
