<?php
class Caracteristica extends AppModel {
	public $name = 'Caracteristica';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'caracteristicas';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

	public function salvar($dados)
	{
		$this->Questionario = ClassRegistry::init('Questionario');
		$this->CaracteristicaQuestionario = ClassRegistry::init('CaracteristicaQuestionario');
		$this->CaracteristicaQuestao = ClassRegistry::init('CaracteristicaQuestao');
		$dados['Caracteristica']['questionarios_selecionados'] = $this->Questionario->Questao->find('list', array(
			'conditions' => array(
				'Questao.codigo' => $dados['Caracteristica']['respostas']
				),
			'fields' => array(
				'Questao.codigo_questionario',
				'Questao.codigo_questionario'
				),
			'group' => array(
				'Questao.codigo_questionario'
				)
			)
		);

		if(!empty($dados['Caracteristica']['codigo'])) {
			$caracteristicas_questionarios_excluir = $this->CaracteristicaQuestionario->find('list', array(
				'conditions' => array(
					'CaracteristicaQuestionario.codigo_caracteristica' => $dados['Caracteristica']['codigo']
					),
				'fields' => array(
					'CaracteristicaQuestionario.codigo',
					'CaracteristicaQuestionario.codigo'
					)		
				)
			);
			$caracteristicas_questoes_excluir = $this->CaracteristicaQuestao->find('list', array(
				'conditions' => array(
					'CaracteristicaQuestao.codigo_caracteristica' => $dados['Caracteristica']['codigo']
					),
				'fields' => array(
					'CaracteristicaQuestao.codigo',
					'CaracteristicaQuestao.codigo'
					)		
				)
			);
		}

		try {
			$this->query('BEGIN TRANSACTION');

			//INCLUI A CARACTERISTICA
			if(!empty($dados['Caracteristica']['codigo'])) {
				$this->id = $dados['Caracteristica']['codigo'];
				if(!parent::atualizar($dados)) {
					throw new Exception('Não incluiu a característica!');
				}
			} else {
				if(!parent::incluir($dados)) {
					throw new Exception('Não incluiu a característica!');
				}	
			}

			// ASSOCIA A CARACTERISTICA COM O QUESTIONARIO
			foreach ($dados['Caracteristica']['questionarios_selecionados'] as $key => $value) {
				$dados_questionarios[]['CaracteristicaQuestionario'] = array('codigo_caracteristica' => $this->id, 'codigo_questionario' => $value);
			}
			if(!empty($dados_questionarios)) {
				if(!$this->CaracteristicaQuestionario->incluirTodos($dados_questionarios)) {
					throw new Exception('Não incluiu a característica!');
				}
			}

			// ASSOCIA AS RESPOSTAS COM AS CARACTERISTICAS
			foreach ($dados['Caracteristica']['respostas'] as $key => $value) {
				$dados_questoes[]['CaracteristicaQuestao'] = array('codigo_caracteristica' => $this->id, 'codigo_questao' => $value);
			}
			if(!empty($dados_questoes)) {
				if(!$this->CaracteristicaQuestao->incluirTodos($dados_questoes)) {
					throw new Exception('Não incluiu a característica!');
				}
			}

			// SALVA OS DADOS
			$this->commit();

			//EXCLUI OS DADOS ANTIGOS
			if(isset($caracteristicas_questionarios_excluir)) $this->CaracteristicaQuestionario->deleteAll(array('CaracteristicaQuestionario.codigo' => $caracteristicas_questionarios_excluir));
			if(isset($caracteristicas_questoes_excluir)) $this->CaracteristicaQuestao->deleteAll(array('CaracteristicaQuestao.codigo' => $caracteristicas_questoes_excluir));
			return true;

		} catch(Exception $e) {

			// DESFAZ E RETORNA FALSO
			$this->rollback();
			return false;			
		}
	}


	public function monta_respostas($codigo = null)
	{	
		if(is_null($codigo)) {
			return false;
		}
		$dados = $this->findByCodigo($codigo);
		$this->CaracteristicaQuestao = ClassRegistry::init('CaracteristicaQuestao');
		$respostas = $this->CaracteristicaQuestao->find('list', array(
			'conditions' => array(
				'CaracteristicaQuestao.codigo_caracteristica' =>  $codigo
				),
			'fields' => array(
				'CaracteristicaQuestao.codigo_questao',
				'CaracteristicaQuestao.codigo_questao'
				)
			)
		);
		if(!empty($respostas)) {
			foreach ($respostas as $key => $resposta) {
				$dados['Caracteristica']['respostas'][$resposta] = $resposta;
			}
		}
		return $dados;
	}

	public function excluir($codigo = null)
	{
		$codigo = (int)$codigo;
		if(is_null($codigo)) {
			return false;
		}
		$this->CaracteristicaQuestionario = ClassRegistry::init('CaracteristicaQuestionario');
		$this->CaracteristicaQuestao = ClassRegistry::init('CaracteristicaQuestao');

		try {
			$this->query('BEGIN TRANSACTION');

			if(!$this->query('DELETE FROM caracteristicas_questoes WHERE codigo_caracteristica = '.$codigo)) {
				throw new Exception('Falha no processo 1');
			}

			if(!$this->query('DELETE FROM caracteristicas_questionarios WHERE codigo_caracteristica = '.$codigo)) {
				throw new Exception('Falha no processo 2');
			}

			if(!$this->query('DELETE FROM caracteristicas WHERE codigo = '.$codigo)) {
				throw new Exception('Falha no processo 3');
			}

			// SALVA OS DADOS
			$this->commit();
			return true;			
		} catch(Exception $e) {

			// DESFAZ E RETORNA FALSO
			$this->rollback();
			return false;			
		}
	}


}