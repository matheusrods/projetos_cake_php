<?php 
class Questao extends AppModel {
	
	public $name = 'Questao';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'questoes';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure', 'Containable');
	public $displayField = 'label';

	public $hasMany = array(
		'Respostas' => array(
			'className' => 'Questao',
			'foreignKey' => 'codigo_questao',
			'depentent' => false,
			'fields' => array('*', '(SELECT label FROM label_questoes WHERE label_questoes.codigo = Respostas.codigo_label_questao) as label'),
			'order'	 => array('label') //para ordernar pela pergunta e resposta.
			)
		);

	public $belongsTo = array(
		'Questionario' => array(
			'className' => 'Questionario',
			'foreignKey' => 'codigo_questionario',
			),
		'LabelQuestao' => array(
			'className' => 'LabelQuestao',
			'foreignKey' => 'codigo_label_questao'
			)
		);

	public function incluir($metodo, $data)
	{	

		if($metodo == 'resposta') {
			$labelData['LabelQuestao']['type'] = 'R';
		} else {
			$labelData['LabelQuestao']['type'] = 'Q';
		}

		//caso não exista questao pronta vinculada entao:
		if(empty($data[$this->name]['codigo_label_questao'])) {

			// localiza se já existe uma questao no banco de questoes prontas igual a que esta sendo criada
			$questao = $this->LabelQuestao->find('first', array(
				'conditions' => array(
					'LabelQuestao.label' => $data[$this->name]['label'],
					'LabelQuestao.type' => $labelData['LabelQuestao']['type']
					),
				'fields' => array('LabelQuestao.codigo')
				)
			);

			// caso exista vincule-a
			if(!empty($questao)) {
				$data['Questao']['codigo_label_questao'] = $questao['LabelQuestao']['codigo'];

			// do contrario, crie-a
			} else {
				$this->LabelQuestao->create();
				$labelData['LabelQuestao']['label'] = $data[$this->name]['label'];
				$this->LabelQuestao->save($labelData);
				$data['Questao']['codigo_label_questao'] = $this->LabelQuestao->id;
			}
		}

		// salve a nova questao
		return parent::incluir($data);
	}

	public function atualizaCodigoProximaQuestao($codigo_questao, $codigo_resposta)
	{
		return parent::atualizar(array('Questao' => array('codigo' => $codigo_resposta, 'codigo_proxima_questao' => $codigo_questao)));
	}

	public function alterar($data)
	{

		if(isset($data['Questao']['codigo_questao']) && $data['Questao']['codigo_questao'] > 0) {
			$labelData['LabelQuestao']['type'] = 'R';
		} else {
			$labelData['LabelQuestao']['type'] = 'Q';
		}

		//caso não exista questao pronta vinculada entao:
		if(empty($data[$this->name]['codigo_label_questao'])) {

			// localiza se já existe uma questao no banco de questoes prntas igual a que esta sendo criada
			$questao = $this->LabelQuestao->find('first', array(
				'conditions' => array(
					'LabelQuestao.label' => $data['LabelQuestao']['label']
					),
				'fields' => array('LabelQuestao.codigo')
				)
			);

			// caso exista vincule-a
			if(!empty($questao)) {
				$data['Questao']['codigo_label_questao'] = $questao['LabelQuestao']['codigo'];

			// do contrario, crie-a
			} else {
				$this->LabelQuestao->create();
				$labelData['LabelQuestao']['label'] = $data['LabelQuestao']['label'];
				$this->LabelQuestao->save($labelData);
				$data['Questao']['codigo_label_questao'] = $this->LabelQuestao->id;
			}
		}

		// atualiza a nova questao
		return parent::atualizar($data);
	}

	public function excluir($codigo)
	{
		return $this->delete($codigo, true);
	}

	public function limpaProximaQuestao($codigo)
	{
		return $this->query('UPDATE questoes SET codigo_proxima_questao = NULL WHERE codigo_proxima_questao = '.$codigo);
	}

}
