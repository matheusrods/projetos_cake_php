<?php
App::import('Helper', 'Html');
class QuestoesController extends AppController {

	public $name = 'Questoes';
	public $uses = array('Questao');

	public function index($codigo_questionario) {
		$this->pageTitle = 'Questões';
		$this->set(compact('codigo_questionario'));

		//valida se o questionario selecionado é realmente da empresa
		$questionario = $this->Questao->Questionario->find('count', array('conditions' => array('Questionario.codigo' => $codigo_questionario, 'Questionario.codigo_empresa' => $this->BAuth->user('codigo_empresa'))));
		if(empty($questionario)) {
			return $this->redirect(array('controller' => 'questionarios', 'action' => 'index'));
		}

		//identifica a primeira questão do formulario
		$conditions = array(
			'Questao.codigo_questionario' => $codigo_questionario,
			'Questao.codigo_questao' => NULL,
			// '(SELECT TOP 1 q.codigo from questoes q WHERE q.codigo_proxima_questao = Questao.codigo)' => NULL
			);

		$this->paginate['Questao'] = array(
			'conditions' => $conditions,
			'limit' => 1, //foi alterado de 50 para 1 pois não estava trazendo corretamente o questionario
			'order' => array('Questao.codigo')
			);

		// debug($this->Questao->find('all',$this->paginate['Questao']));

		$questoes = $this->paginate();
		// $questoes = $this->Questao->find('all',$this->paginate['Questao']);

		// debug($questoes);exit;

		$this->set(compact('questoes'));
		$this->set(compact('codigo_questionario'));
		$this->Filtros->limpa_sessao($this->Questao->name);
	}

	public function incluir($metodo, $codigo_questionario, $codigo_questao_resposta = null)
	{	
		$this->pageTitle = 'Incluir questão';
		switch ($metodo) {
			//identifica se é uma questao e parametriza dados
			case 'questao':
			$this->pageTitle = 'Incluir questão';
			$resposta = false;
			$label = 'Pergunta';
			break;
			
			//identifica se é uma resposta e parametriza dados
			case 'resposta':
			$this->pageTitle = 'Incluir resposta';
			$resposta = true;
			$label = 'Resposta';
			$this->Questao->validate['codigo_questao'] = array(
				'rule' => 'notEmpty',
				'message' => 'Este campo é obrigatório',
				'allowEmpty' => false
				);
			break;
		}

		if($this->RequestHandler->isPost()) {

			if($this->Questao->incluir($metodo, $this->data)) {
				$validate = true;
				if(!empty($this->data['Questao']['codigo_resposta']) && $metodo == 'questao') {
					if(!$this->Questao->atualizaCodigoProximaQuestao($this->Questao->id, $this->data['Questao']['codigo_resposta'])) $validate = false;
				}
				if($validate) {
					$this->BSession->setFlash('save_success');
					return $this->redirect(array('action' => 'index', $codigo_questionario));
				} else {
					$this->BSession->setFlash('save_error');
				}
			} else {
				$this->BSession->setFlash('save_error');
			}
		}

		// busca as questoes para serem escolhidas como pai (se tratando de uma resposta)
		$questoes = array();
		if($metodo == 'resposta') {
			$questoes = $this->Questao->find('list', array(
				'conditions' => array(
					'Questao.codigo_questionario' => $codigo_questionario,
					'Questao.codigo_questao' => NULL,
					),
				'joins' => array(
					array(
						'table' => 'label_questoes',
						'alias' => 'LabelQuestao',
						'type' => 'INNER',
						'conditions' => 'LabelQuestao.codigo = Questao.codigo_label_questao'
						)
					),
				'fields' => array(
					'Questao.codigo',
					'LabelQuestao.label'
					)		
				)
			);
		}
		$this->set(compact('questoes', 'resposta', 'label', 'codigo_questionario', 'codigo_questao_resposta', 'metodo'));

	}

	public function alterar($codigo_questionario, $codigo = null)
	{
		$this->pageTitle = 'Alterar questão';
		if($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {

			// salva as alterações
			if($this->Questao->alterar($this->data)) {
				$this->BSession->setFlash('save_success');
				return $this->redirect(array('action' => 'index', $codigo_questionario));
			} else {
				$this->BSession->setFlash('save_error');
			}

		} else {
			// fornece os dados para o fomulario (mo caso de editar)
			$this->Questao->contain('LabelQuestao');
			$this->data = $this->Questao->findByCodigo($codigo);
		}

		// busca a questao inicial do questionario
		$questaoInicial = $this->Questao->find('list', array(
			'conditions' => array(
				'Questao.codigo_questao' => NULL,
				'(SELECT TOP 1 q.codigo from questoes q WHERE q.codigo_proxima_questao = Questao.codigo)' => NULL,
				'Questao.codigo_questionario' => $codigo_questionario
				),
			'fields' => array(
				'Questao.codigo',		
				)
			)
		);
		$questaoInicial = $questaoInicial[key($questaoInicial)];

		//  caso o objeto atual a ser editado seja a questao inicial, setar variael como zero
		if($codigo == $questaoInicial) {
			$this->data['Questao']['codigo_questao'] = 0;
		}

		$questoes = array();
		//identifica se é uma resposta e parametriza dados
		if(isset($this->data['Questao']['codigo_questao']) && $this->data['Questao']['codigo_questao'] > 0) {
			$this->pageTitle = 'Alterar resposta';
			$label = 'Resposta';
			$resposta = true;

			// define a posição de cada item localizado no banco
			$this->Questao->virtualFields = array(
				'posicao' => '(SELECT count(q.codigo) FROM questoes q WHERE q.codigo_label_questao = Questao.codigo_label_questao AND q.codigo <> Questao.codigo AND q.codigo < Questao.codigo) + 1'
				);

			// busca as questoes para serem escolhidas como pai (se tratando de uma resposta)
			$questoes = $this->Questao->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'Questao.codigo_questionario' => $codigo_questionario,
					'Questao.codigo_questao' => NULL,
					'(SELECT TOP 1 count(q.codigo) from questoes q WHERE q.codigo_proxima_questao = Questao.codigo) IS NOT' => NULL,
					),
				'joins' => array(
					array(
						'table' => 'label_questoes',
						'alias' => 'LabelQuestao',
						'type' => 'INNER',
						'conditions' => 'LabelQuestao.codigo = Questao.codigo_label_questao'
						)
					),
				'order' => 'Questao.codigo, Questao.posicao',
				'fields' => array(
					'Questao.codigo',
					'LabelQuestao.label',
					'Questao.posicao'
					)		
				)
			);

			// organiza o array para montar o select
			$values = array();
			foreach ($questoes as $key => $questao) {
				$values[$questao['Questao']['codigo']] = $questao['LabelQuestao']['label'].(($questao['Questao']['posicao'] > 1)? ' ('.$questao['Questao']['posicao'].')' : '');
			}
			$questoes = $values;
			unset($values);

		//identifica se é uma questao e parametriza dados
		} else {
			$this->pageTitle = 'Alterar questão';
			$label = 'Pergunta';
			$resposta = false;
			$this->data['Questao']['codigo_questao_resposta'] = null;
		}

		$this->set(compact('questoes', 'codigo', 'questaoInicial', 'label', 'resposta', 'codigo_questionario'));
	}

	public function excluir($codigo_questionario, $codigo = null)
	{
		$questao_pendente = $this->Questao->find('count', array(
			'conditions' => array(
				'Questao.codigo' => $codigo,
				'Questao.codigo_proxima_questao NOT' => NULL
				)
			)
		);
		$resposta_pendente = $this->Questao->find('count', array(
			'conditions' => array(
				'Questao.codigo' => $codigo
				),
			'joins' => array(
				array(
					'table' => 'questoes',
					'alias' => 'Resposta',
					'type' => 'INNER',
					'conditions' => 'Resposta.codigo_questao = Questao.codigo'
					)
				)
			)
		);

		if($questao_pendente > 0) {
			$this->BSession->setFlash('erro_resposta_pendente');
			return $this->redirect(array('action' => 'index', $codigo_questionario));
		}

		if($resposta_pendente > 0) {
			$this->BSession->setFlash('erro_questao_pendente');
			return $this->redirect(array('action' => 'index', $codigo_questionario));
		}

		if(!is_null($codigo) && $this->Questao->excluir($codigo)) {
			$this->Questao->limpaProximaQuestao($codigo);
			$this->BSession->setFlash('save_success');
		} else {
			$this->BSession->setFlash('save_error');
		}
		return $this->redirect(array('action' => 'index', $codigo_questionario));
	}

	public function busca_pergunta()
	{
		$this->autoRender = false;

		$codigo 	= $_POST['codigo'];
		$padding 	= $_POST['padding'];
		$nivel 		= $_POST['nivel'];
		$contaC 	= $_POST['contaC'];

		$this->Questao->virtualFields = array(
			'posicao' => '(SELECT count(q.codigo) FROM questoes q WHERE q.codigo_label_questao = Questao.codigo_label_questao AND q.codigo <> Questao.codigo AND q.codigo < Questao.codigo) + 1'
			);

		// busca pergunta por codigo
		$perguntas = $this->Questao->find('all', array(
			'conditions' => array(
				'Questao.codigo' => $codigo
				)
			)
		);

		$html = false;
		if(!empty($perguntas)) {

			// monta o html para o ajax (recursivo)
			$Html = new HtmlHelper(); 
			$html = '';
			foreach ($perguntas as $key => $pergunta) {

				// monta as questoes
				$html .= '<tr data-codigo="'.$pergunta['Questao']['codigo'].'" class="sub-question questions data-src" data-nivel="'.($nivel+1).'" data-src="'.str_replace('.', '', $contaC).($key+1).'">';
				$html .= '<td class=""><div class="adjust-padding" style="padding-left: '.($padding+12).'px">';

				if(!empty($pergunta['Respostas'])) {
					$html .= '<i class="icon-chevron-down"></i> ';
				} else {
					$html .= '<div style="width: 18px;" class="pull-left">&nbsp;</div>';
				}

				$html .= '<strong>Pergunta:</strong> '.$pergunta['LabelQuestao']['label'].' '.(($pergunta['Questao']['posicao'] >1)? '<span class="color-red">('.$pergunta['Questao']['posicao'].')</span>' : '' ).'</div></td>';
				$html .= '<td class="js-actions">';

				$html .= $Html->link('', array('action' => 'alterar', $pergunta['Questao']['codigo_questionario'], $pergunta['Questao']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')).'&nbsp;&nbsp;&nbsp;';
				$html .= $Html->link('', array('action' => 'incluir', 'resposta', $pergunta['Questao']['codigo_questionario'], $pergunta['Questao']['codigo']), array('class' => 'icon-plus ', 'title' => 'Incluir resposta')).'&nbsp;&nbsp;&nbsp;';
				$html .= $Html->link('', array('action' => 'excluir', $pergunta['Questao']['codigo_questionario'], $pergunta['Questao']['codigo']), array('class' => 'icon-trash delete-confirm', 'title' => 'Excluir', 'data-title' => 'Tem certeza?', 'data-text' => 'Esta operação também exclui as questões vinculadas a este questionário')).' ';

				$html .= '</td>';
				$html .= '</td>';	
				$html .= '</tr>';

				if(!empty($pergunta['Respostas'])) {
					foreach ($pergunta['Respostas'] as $key2 => $resposta) {

						// monta as respostas
						$html .= '<tr data-codigo="'.$resposta['codigo_proxima_questao'].'" class="answers open-questions data-src" data-nivel="'.($nivel+1).'" data-src="'.$contaC.($key+1).'" data-contaC="'.$contaC.($key+1).($key2+1).'">';
						$html .= '<td class=""><div class="padding-adjust" style="padding-left: '.($padding+26).'px">';

						if($resposta['codigo_proxima_questao']) {
							$html .= '<i class="icon-chevron-right"></i> ';
						} else {
							$html .= '<div style="width: 18px;" class="pull-left">&nbsp;</div>';
						}

						$html .= '<strong>Resposta:</strong> '.$resposta['Respostas'][0]['label'].' ';
						if($resposta['pontos'] > 1) {
							$html .= '('.$resposta['pontos'].' pontos)';
						} elseif($resposta['pontos'] == 1) {
							$html .= '('.$resposta['pontos'].' ponto)';
						} 

						$html .= '</div></td>';
						$html .= '<td class="js-actions">';

						$html .= $Html->link('', array('action' => 'alterar', $resposta['codigo_questionario'], $resposta['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')).'&nbsp;&nbsp;&nbsp;';

						if(empty($resposta['codigo_proxima_questao'])) {
							$html .= $Html->link('', array('action' => 'incluir', 'questao', $resposta['codigo_questionario'], $resposta['codigo']), array('class' => 'icon-plus ', 'title' => 'Incluir questão')).'&nbsp;&nbsp;&nbsp;';
						}

						$html .= $Html->link('', array('action' => 'excluir', $resposta['codigo_questionario'], $resposta['codigo']), array('class' => 'icon-trash delete-confirm', 'title' => 'Excluir', 'data-title' => 'Tem certeza?', 'data-text' => 'Esta operação também exclui as questões vinculadas a este questionário')).'&nbsp;';

						$html .= '</td>';
						$html .= '</td>';	
						$html .= '</tr>';
					}
				}
			}
			// fim
		}
		echo json_encode($html);
	}

	public function buscar_codigo() 
	{
		$this->layout = 'ajax_placeholder';
		$input_id = $this->passedArgs['searcher'];
		$type = $this->passedArgs['type'];
		$this->loadModel('LabelQuestao');
		$this->data['LabelQuestao'] = $this->Filtros->controla_sessao($this->data, $this->Questao->name);
		$this->set(compact('input_id', 'type'));
	}

	public function listagem($type) 
	{
		$this->layout = 'ajax'; 
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Questao->name);
		$conditions = $this->Questao->LabelQuestao->converteFiltroEmCondition($filtros);
		$conditions['LabelQuestao.type'] = $type;
		$order = 'LabelQuestao.label ASC';

		$this->paginate['LabelQuestao'] = array(
			'conditions' => $conditions,
			'limit' => 50, 
			'order' => $order
			);

		$questoes = $this->paginate('LabelQuestao');
		$this->set(compact('questoes'));
		$this->Filtros->limpa_sessao($this->Questao->LabelQuestao->name);
	}

}