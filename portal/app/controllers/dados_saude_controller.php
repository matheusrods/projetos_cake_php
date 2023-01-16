<?php
class DadosSaudeController extends AppController {
	public $name = 'DadosSaude';
	public $helpers = array('BForm', 'Html', 'Ajax');

	var $uses = array( 
		'Usuario',
		'Funcionario',
		'UsuariosDados',
		'UsuariosImc',
		'Etnia',
		'GrauEscolaridade', 
		'UsuariosColesterol',
		'UsuariosAbdominal',
		'UsuariosPsa',
		'UsuariosPressaoArterial',
		'UsuariosGlicose',
		'Questionario',
		'UsuariosQuestionario',
		'UsuariosMedicamento',
		'Medicamento',
		'UsuariosPlanoSaude',
		'UsuariosMedico',
		'Especialidade',
		'Questao',
		'Resposta',
		'Resultado'
		);

	var $components = array('RequestHandler', 'Session');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow('index','dashboard','dados','grava_imc','grava_info','grava_colesterol','grava_psa','grava_abdominal','grava_pressao','grava_glicose','grava_plano_saude','grava_medicamento','grava_medico','upload_avatar','remove_medicamento','reseta_questionario',
			'remove_medicamento', 'busca_medicamento', 'carrega_medicamentos', 'grava_pressao_arterial', 'reseta_questionario', 'resultado_questionario','valida_cpf_dados');
	}  

	function index() {
		$this->redirect(array('action' => 'dashboard'));
	}

	function dashboard() {
		$usuario_info = $this->BAuth->user();
		
		// verifica se funcionario existe! 
		//if($usuario_info['Usuario']['codigo_funcionario']) {

			// $funcionario_info = $this->Funcionario->find('first', array('conditions' => array('codigo' => $usuario_info['Usuario']['codigo_funcionario'])));
		$Usuarios_info = $this->UsuariosDados->find('first', array('conditions' => array('codigo_usuario' => $usuario_info['Usuario']['codigo'])));
		$todos_questionarios = $this->_controle_questionarios($usuario_info['Usuario']['codigo'], $Usuarios_info['UsuariosDados']['sexo']);
		$Usuarios_imc = $this->UsuariosImc->find('all', array('conditions' => array('codigo_usuario' => $usuario_info['Usuario']['codigo']), 'order' => array('UsuariosImc.codigo DESC')));
		$Usuarios_colesterol = $this->UsuariosColesterol->find('all', array('conditions' => array('codigo_usuario' => $usuario_info['Usuario']['codigo']), 'order' => array('UsuariosColesterol.codigo DESC')));
		$Usuarios_abdominal = $this->UsuariosAbdominal->find('all', array('conditions' => array('codigo_usuario' => $usuario_info['Usuario']['codigo']), 'order' => array('UsuariosAbdominal.codigo DESC')));
		$Usuarios_psa = $this->UsuariosPsa->find('all', array('conditions' => array('codigo_usuario' => $usuario_info['Usuario']['codigo']), 'order' => array('UsuariosPsa.codigo DESC')));
		$Usuarios_pressao_arterial = $this->UsuariosPressaoArterial->find('all', array('conditions' => array('codigo_usuario' => $usuario_info['Usuario']['codigo']), 'order' => array('UsuariosPressaoArterial.codigo DESC')));
		$Usuarios_glicose = $this->UsuariosGlicose->find('all', array('conditions' => array('codigo_usuario' => $usuario_info['Usuario']['codigo']), 'order' => array('UsuariosGlicose.codigo DESC')));
		$Usuarios_medicamento = $this->UsuariosMedicamento->find('all', array('conditions' => array('codigo_usuario' => $usuario_info['Usuario']['codigo']), 'order' => array('UsuariosMedicamento.codigo DESC')));
		$Usuarios_plano_saude = $this->UsuariosPlanoSaude->find('all', array('conditions' => array('codigo_usuario' => $usuario_info['Usuario']['codigo']), 'order' => array('UsuariosPlanoSaude.codigo DESC')));
		$Usuarios_medico = $this->UsuariosMedico->find('all', array('conditions' => array('codigo_usuario' => $usuario_info['Usuario']['codigo']), 'order' => array('UsuariosMedico.codigo DESC')));

			// $Usuarios_questionarios = $this->UsuariosQuestionario->find()


			// verifica se foi preenchido questionario prévio
		if(!$Usuarios_info) {
			$this->redirect(array('action' => 'dados'));
		} else {
			$Usuarios_info['UsuariosDados']['idade'] = $this->_retorna_idade($Usuarios_info);
		}

		//} 
		// else {
		// 	$this->redirect(array('controller' => 'usuarios', 'action' => 'logout'));
		// }
		
		$this->_carrega_combo();
		$this->set(compact('usuario_info', 'Usuarios_info', 'Usuarios_imc', 'Usuarios_colesterol', 'Usuarios_abdominal', 'Usuarios_pressao_arterial', 'Usuarios_psa', 'Usuarios_glicose', 'todos_questionarios', 'Usuarios_medicamento', 'Usuarios_plano_saude', 'Usuarios_medico'));
	}
	
	function reseta_questionario($codigo_questionario) {
		$this->autoRender = false;
		$usuario_info = $this->BAuth->user();
		
		$dados_funcionario_questionario = $this->UsuariosQuestionario->find('first', array('conditions' => array('codigo_usuario' => $usuario_info['Usuario']['codigo'], 'codigo_questionario' => $codigo_questionario)));	
		if($dados_funcionario_questionario) {
			if($this->UsuariosQuestionario->delete( $dados_funcionario_questionario['UsuariosQuestionario']['codigo'] )) {
				$todos_questoes = $this->Questao->find('all', array('conditions' => array('codigo_questionario' => $codigo_questionario)));
				foreach($todos_questoes as $k => $campo) {
					$resposta = $this->Resposta->find('first', array('conditions' => array('codigo_usuario' => $usuario_info['Usuario']['codigo'], 'codigo_questao' => $campo['Questao']['codigo'])));
					if($resposta) {
						$this->Resposta->delete($resposta['Resposta']['codigo']);
					}
				}				
			}
		}
		$this->redirect(array('controller' => 'questionarios', 'action' => 'responder_questionario', $codigo_questionario));
		
	}
	
	function _controle_questionarios($codigo_usuario, $sexo = null) {
		$conditions['status'] = 1;
		if(!empty($sexo) && !is_null($sexo)) {
			$conditions['OR'][]['aplicacao_sexo'] = 'A';
			$conditions['OR'][]['aplicacao_sexo'] = $sexo;
		} 		
		$todos_questionarios = $this->Questionario->find('all', array('conditions' => $conditions));
		$qtd_questionarios = count($todos_questionarios);
		$qtd_respondidos = 0;
		
		$marca_primeiro = false;
		foreach($todos_questionarios as $key => $questionario) {
			
			if($this->UsuariosQuestionario->find('first', array('conditions' => array('codigo_usuario' => $codigo_usuario, 'codigo_questionario' => $questionario['Questionario']['codigo'])))) {
				// unset($todos_questionarios[$key]);
				
				$todos_questionarios[$key]['Questionario']['respondido'] = 1;
				$qtd_respondidos++;
				
			} else {
				
				$todos_questionarios[$key]['Questionario']['respondido'] = 0;
				if(!$marca_primeiro) {
					$todos_questionarios[$key]['Questionario']['habilitado'] = 1;
					$marca_primeiro = true;
				} else {
					$todos_questionarios[$key]['Questionario']['habilitado'] = 0;
				}
			}				
		}

		//ve($todos_questionarios);
		
		return array('questionarios' => $todos_questionarios, 'qtd_questionarios' => $qtd_questionarios, 'qtd_respondidos' => $qtd_respondidos);
	}
	
	function dados() {
		
		$usuario = $this->BAuth->user();

		// caso ja exista dados pré preenchidos, exiba-os
		$this->loadModel('UsuariosDados');
		$dados_existentes = $this->UsuariosDados->findByCodigoUsuario($usuario['Usuario']['codigo']);
		if(!empty($dados_existentes['UsuariosDados']['cpf'])) $this->data['UsuariosDados']['cpf'] = $dados_existentes['UsuariosDados']['cpf'];
		//================================================

		$usuario_info = $this->UsuariosDados->find('first', array('conditions' => array('codigo_usuario' => $usuario['Usuario']['codigo'])));
		$usuario_imc = $this->UsuariosImc->find('first', array('conditions' => array('codigo_usuario' => $usuario['Usuario']['codigo'])));

		$this->_carrega_combo();
		$this->set(compact('usuario', 'usuario_info', 'usuario_imc'));
	}
	
	function _carrega_combo() {
		$etnia = array('' => 'Selecione') + $this->Etnia->find('list', array('fields' => array('codigo', 'descricao')));
		$grau_escolaridade = array('' => 'Selecione') + $this->GrauEscolaridade->find('list', array('fields' => array('codigo', 'descricao')));
		$especialidades = array('' => 'Selecione') + $this->Especialidade->find('list', array('fields' => array('codigo', 'descricao')));
		
		$this->set(compact('etnia', 'grau_escolaridade', 'especialidades'));
	}
	
	function _retorna_idade($funcionario_info) {
		
		// calcula idade
		if($funcionario_info['UsuariosDados']['data_nascimento']) {
			$data = explode("/", $funcionario_info['UsuariosDados']['data_nascimento']);

			$data_nascimento = new DateTime( $data[2] . "-" . $data[1] . "-" . $data[0] );
			$interval = $data_nascimento->diff( new DateTime( date('Y-m-d') ) );
			$idade = $interval->format( '%Y anos' );
		} else {
			$idade = '';
		}		
		
		return $idade;
	}
	
	function valida_cpf_dados(){
		$this->autoRender = false;

		$usuario_info = $this->BAuth->user();

		$this->loadModel('UsuariosDados');
		if(!empty($this->params['form']['cpf'])){
			$cpf = Comum::soNumero($this->params['form']['cpf']);
			//Se o cpf informado já é utilizado por um outro usuário
			if($this->UsuariosDados->find('first', array('conditions' => array('codigo_usuario <>' => $usuario_info['Usuario']['codigo'], 'cpf' => $cpf )))) {
				return 0;
			} else {
				return 1;
			}
		} else {
			return 0;
		}
	}

	function grava_info() {
		$this->autoRender = false;

		$this->loadModel('UsuariosDados');

		$retorno = array('resultado' => 0);

		$usuario_info = $this->BAuth->user();
		$dadosUsuario = $this->UsuariosDados->find('first', array('conditions' => array('codigo_usuario' => $usuario_info['Usuario']['codigo'])));		

		//Verifica se o usuário possui registro na usuario dados
		$existe_dados_usuario = $this->UsuariosDados->find('first', array('conditions' => array('codigo_usuario' => $usuario_info['Usuario']['codigo'])));

		$cpf = Comum::soNumero($this->params['data']['UsuariosDados']['cpf']);
		//Dados do usuário na tabela usuario_dados
		$usuario_dados = array(
			'codigo_usuario' => $usuario_info['Usuario']['codigo'],
			'cpf' => $cpf,
			'data_nascimento' => $this->params['data']['UsuariosDados']['data_nascimento'],
			'sexo' => $this->params['data']['UsuariosDados']['sexo']
		);

			
		//Se o cpf não é utilizado por outro usuário
		if(!$this->UsuariosDados->find('first', array('conditions' => array('codigo_usuario <>' => $usuario_info['Usuario']['codigo'], 'cpf' => $cpf)))) {
			
			if(!empty($this->params['data']['UsuariosDados']['sexo']) && !empty($this->params['data']['UsuariosDados']['data_nascimento']) && !empty($this->params['data']['UsuariosDados']['cpf'])){
				
					//Se o CPF é válido
					if(Comum::validarCPF($cpf)){
						if(empty($existe_dados_usuario)){
							$this->UsuariosDados->incluir($usuario_dados);
						} elseif($existe_dados_usuario['UsuariosDados']['cpf'] != $cpf) {

							$usuario_dados['codigo'] = $existe_dados_usuario['UsuariosDados']['codigo'];

							$this->UsuariosDados->atualizar(array('UsuariosDados' => $usuario_dados));
						}
					} else {

						$retorno['resultado'] = 0;
						$retorno['erro'] = array("campo" => "cpf",
												"mensagem" => "CPF inválido");
						return json_encode($retorno);
					}
			}

		} else {
			$retorno['resultado'] = 0;
			$retorno['erro'] = array("campo" => "cpf",
									"mensagem" => "CPF já utilizado");
			return json_encode($retorno);

		}
				
				
		$array_dados = array(
			'codigo_usuario' => $usuario_info['Usuario']['codigo'],
			'data_nascimento' => $this->params['data']['UsuariosDados']['data_nascimento'],
			'sexo' => $this->params['data']['UsuariosDados']['sexo'],
		);

		// verifica se inclui ou atualiza informações
		if($dadosUsuario) {
			$array_dados['codigo'] = $dadosUsuario['UsuariosDados']['codigo'];
			if($this->UsuariosDados->atualizar(array('UsuariosDados' => $array_dados))) {
				
				$retorno['resultado'] = 1;

			} else {
				$retorno['resultado'] = 0;
			}
		} else {
			if($this->UsuariosDados->incluir($array_dados)) {
				$retorno['resultado'] = 1;
			} else {
				$retorno['resultado'] = 0;
			}
		}
		
		return json_encode($retorno);
	}
	
	function grava_imc() {

		$usuario_info = $this->BAuth->user();
		
		$array_dados = array(
			'codigo_usuario' => $usuario_info['Usuario']['codigo'],
			'altura' => $this->params['data']['UsuariosImc']['altura'],
			'peso' => $this->params['data']['UsuariosImc']['peso'],
			'data_medicao' => date('Y-m-d')
			);
		
		// inclui
		if($this->UsuariosImc->incluir($array_dados)) {
			print "1";
		} else {
			print "0";
		}
		
		exit;
	}	
	
	function grava_colesterol() {
		
		$usuario_info = $this->BAuth->user();
		
		$array_dados = array(
			'codigo_usuario' => $usuario_info['Usuario']['codigo'],
			'total' => $this->params['data']['UsuariosColesterol']['total'],
			'hdl' => $this->params['data']['UsuariosColesterol']['hdl'],
			'ldl' => $this->params['data']['UsuariosColesterol']['ldl'],
			'triglicerideos' => $this->params['data']['UsuariosColesterol']['triglicerideos'],
			'data_medicao' => date('Y-m-d')
			);
		
		// inclui
		if($this->UsuariosColesterol->incluir($array_dados)) {
			print "1";
		} else {
			print "0";
		}
		
		exit;
	}	
	
	function grava_abdominal() {

		$usuario_info = $this->BAuth->user();

		$array_dados = array(
			'codigo_usuario' => $usuario_info['Usuario']['codigo'],
			'largura' => $this->params['data']['UsuariosAbdominal']['largura'],
			'data_medicao' => date('Y-m-d')
			);

		// inclui
		if($this->UsuariosAbdominal->incluir($array_dados)) {
			print "1";
		} else {
			print "0";
		}

		exit;
	}
	
	function grava_psa() {
		$usuario_info = $this->BAuth->user();

		$array_dados = array(
			'codigo_usuario' => $usuario_info['Usuario']['codigo'],
			'psa_total' => $this->params['data']['UsuariosPsa']['psa_total'],
			'psa_livre' => $this->params['data']['UsuariosPsa']['psa_livre'],
			'data_medicao' => date('Y-m-d')
			);

		// inclui
		if($this->UsuariosPsa->incluir($array_dados)) {
			print "1";
		} else {
			print "0";
		}

		exit;
	}
	
	function grava_pressao_arterial() {
		$usuario_info = $this->BAuth->user();

		$array_dados = array(
			'codigo_usuario' => $usuario_info['Usuario']['codigo'],
			'frequencia_cardiaca' => $this->params['data']['UsuariosPressaoArterial']['frequencia_cardiaca'],
			'pressao_arterial_auto' => $this->params['data']['UsuariosPressaoArterial']['pressao_arterial_auto'],
			'pressao_arterial_baixo' => $this->params['data']['UsuariosPressaoArterial']['pressao_arterial_baixo'],
			'data_medicao' => date('Y-m-d')
			);

		// inclui
		if($this->UsuariosPressaoArterial->incluir($array_dados)) {
			print "1";
		} else {
			print "0";
		}

		exit;
	}
	
	function grava_glicose() {
		$usuario_info = $this->BAuth->user();

		$array_dados = array(
			'codigo_usuario' => $usuario_info['Usuario']['codigo'],
			'glicose' => $this->params['data']['UsuariosGlicose']['glicose'],
			'hemoglobina_glicada' => $this->params['data']['UsuariosGlicose']['hemoglobina_glicada'],
			'data_medicao' => date('Y-m-d'),
			'tempo_jejum' => 0
			);
		
		// inclui
		if($this->UsuariosGlicose->incluir($array_dados)) {
			print "1";
		} else {
			print "0";
		}

		exit;
	}
	
	function grava_plano_saude() {
		$usuario_info = $this->BAuth->user();

		$array_dados = array(
			'codigo_usuario' => $usuario_info['Usuario']['codigo'],
			'descricao' => $this->params['data']['UsuariosPlanoSaude']['descricao'],
			'titular' => $this->params['data']['UsuariosPlanoSaude']['titular'],
			'cpf_titular' => Comum::soNumero($this->params['data']['UsuariosPlanoSaude']['cpf_titular']),
			'data_medicao' => date('Y-m-d')
			);

		// inclui
		if($this->UsuariosPlanoSaude->incluir($array_dados)) {
			print "1";
		} else {
			print "0";
		}

		exit;
	}	
	
	function grava_medico() {
		$usuario_info = $this->BAuth->user();

		$array_dados = array(
			'codigo_usuario' => $usuario_info['Usuario']['codigo'],
			'nome_medico' => $this->params['data']['UsuariosMedico']['nome_medico'],
			'codigo_especialidade' => $this->params['data']['UsuariosMedico']['codigo_especialidade'],
			'crm' => $this->params['data']['UsuariosMedico']['crm'],
			'email' => $this->params['data']['UsuariosMedico']['email'],
			'telefone' => $this->params['data']['UsuariosMedico']['telefone'],
			'data_medicao' => date('Y-m-d')
			);

		// inclui
		if($this->UsuariosMedico->incluir($array_dados)) {
			print "1";
		} else {
			print "0";
		}

		exit;
	}	

	function grava_medicamento(){
		$usuario_info = $this->BAuth->user();

		$array_dados = array(
			'codigo_usuario' => $usuario_info['Usuario']['codigo'],
			'codigo_medicamento' => $this->params['data']
		);

		if($this->UsuariosMedicamento->incluir($array_dados)) {
			print "1";
		} else {
			print "0";
		}

		exit;
	}

	function busca_medicamento() {
		$this->autoRender = false;
		$html = false;
		if($this->RequestHandler->isPost()) {
			$medicamentos = $this->Medicamento->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'OR' => array(
						'Medicamento.descricao LIKE' => '%'.$_POST['string'].'%',
						'Medicamento.principio_ativo LIKE' => '%'.$_POST['string'].'%',
						)
					),
				'limit' => 6,
				'order' => 'Medicamento.descricao ASC'
				)
			);
			if(!empty($medicamentos)) {
				$html = '<table class="table">';
				
				$html .= '<tr>';
				$html .= '<td>Descrição</td>';
				$html .= '<td>Princípio Ativo</td>';
				$html .= '<td>Posologia</td>';
				$html .= '<td>Ação</td>';
				$html .= '</tr>';
				foreach ($medicamentos as $key => $medicamento) {
					$html .= '<tr class="js-click" data-codigo="' . $medicamento['Medicamento']['codigo'] . '">';

					$html .= '<td>';
					$html .= $medicamento['Medicamento']['descricao'];
					$html .= '</td>';

					$html .= '<td>';
					$html .= $medicamento['Medicamento']['principio_ativo'];
					$html .= '</td>';
					
					$html .= '<td>';
					$html .= $medicamento['Medicamento']['posologia'];
					$html .= '</td>';

					$html .= '<td>';
					$html .= '<a href="javascript:void(0);" id="med_'.$medicamento['Medicamento']['codigo'].'" class="label label-info seleciona"><span class="glyphicon glyphicon-share"></span></a>';
					$html .= '</td>';

					$html .= '</tr>';
				}
				$html .= '</table>';
			}
		}
		return json_encode($html);
		
	}
	
	function carrega_medicamentos() {
		
		$usuario_info = $this->BAuth->user();
		
		$array_dados = array(
			'codigo_usuario' => $usuario_info['Usuario']['codigo'],
			'codigo_medicamento' => $this->params['form']['medicamento']
			);
		
		$this->UsuariosMedicamento->incluir($array_dados);
		$funcionario_medicamento = $this->UsuariosMedicamento->find('all', array('conditions' => array('codigo_usuario' => $usuario_info['Usuario']['codigo']), 'order' => array('UsuariosMedicamento.codigo DESC')));
		
		$this->set(compact('funcionario_medicamento'));
	}
	
	function remove_medicamento() {
		$usuario_info = $this->BAuth->user();
		$codigo_medicamento = $this->params['form']['codigo_medicamento'];

		$info = $this->UsuariosMedicamento->find('first', array('conditions' => array('codigo_medicamento' => $codigo_medicamento, 'codigo_usuario' => $usuario_info['Usuario']['codigo'])));
		if(isset($info['UsuariosMedicamento']['codigo']) && $info['UsuariosMedicamento']['codigo']) {
			if ($this->UsuariosMedicamento->delete($info['UsuariosMedicamento']['codigo']))
				exit('1');
			else
				exit('0');
		} else {
			exit('0');
		}
	}	
	
	function upload_avatar() {
		$usuario_info = $this->BAuth->user();
		
		if($this->RequestHandler->isPost()) {
			$retorno = $this->_upload($this->data['UsuariosDados']['avatar'], $usuario_info['Usuario']['codigo']);
		}

		$this->redirect(array('action' => 'dashboard'));
	}
	
	function _upload($file, $cod_usuario) {
		require_once APP . 'vendors' . DS . 'class.upload.php';

		$imagem = new Upload($file);

		if ($imagem->uploaded) {

			// save uploaded image with no changes
			$imagem->Process($_SERVER['DOCUMENT_ROOT'] . '/portal/app/webroot/files/avatar/');

			if ($imagem->processed) {

				$imagem->file_new_name_body = 'avatar-' . $cod_usuario . '-grande';

				$imagem->Process($_SERVER['DOCUMENT_ROOT'] . '/portal/app/webroot/files/avatar/');
				if ($imagem->processed) {

					// resized to 100px wide
					$imagem->file_new_name_body = 'avatar-' . $cod_usuario . '-pequena';
					$imagem->image_resize = true;
					$imagem->image_x = 200;
					$imagem->image_ratio_y = true;

					$imagem->Process($_SERVER['DOCUMENT_ROOT'] . '/portal/app/webroot/files/avatar/');

					if ($imagem->processed) {
						$imagem->Clean();

						$infoFuncionario = $this->UsuariosDados->find('first', array('conditions' => array('codigo_usuario' => $cod_usuario)));
						$infoFuncionario['UsuariosDados']['avatar'] = $imagem->file_dst_name;

						if($this->UsuariosDados->save($infoFuncionario)) {
							return array('upload' => true, 'msg' => 'Deu Boa!', 'imagem' => $infoFuncionario['UsuariosDados']['avatar']);
						} else {
							return array('upload' => false, 'msg' => 'Deu erro!');
						}
					} else {
						return array('upload' => false, 'msg' => 'Deu erro!');
					}
				} else {
					return array('upload' => false, 'msg' => 'Deu erro!');
				}
			} else {
				return array('upload' => false, 'msg' => 'Deu erro!');
			}
		}
	}	
	
	function resultado_questionario(){
		$this->layout = false;
		
		$codigo_questionario = $this->params['form']['codigo_questionario'];

		$usuario_info = $this->BAuth->user();

		$this->loadModel('UsuariosQuestionario');

		$codigo_usuario_questionario = $this->UsuariosQuestionario->find('first', array('conditions' => array('UsuariosQuestionario.codigo_usuario' => $usuario_info['Usuario']['codigo'], 'UsuariosQuestionario.codigo_questionario' => $codigo_questionario), 'order' => 'UsuariosQuestionario.codigo DESC', 'fields' => 'UsuariosQuestionario.codigo'));

		$joins  = array(
			array(
				'table' => $this->Questao->databaseTable.'.'.$this->Questao->tableSchema.'.'.$this->Questao->useTable,
				'alias' => 'Questao',
				'type' => 'LEFT',
				'conditions' => 'Questao.codigo = Resposta.codigo_questao',
				),
			array(
				'table' => $this->Questionario->databaseTable.'.'.$this->Questionario->tableSchema.'.'.$this->Questionario->useTable,
				'alias' => 'Questionario',
				'type' => 'LEFT',
				'conditions' => 'Questionario.codigo = Questao.codigo_questionario AND Questionario.codigo=Resposta.codigo_questionario',
				),
			array(
				'table' => $this->UsuariosQuestionario->databaseTable.'.'.$this->UsuariosQuestionario->tableSchema.'.'.$this->UsuariosQuestionario->useTable,
				'alias' => 'UsuariosQuestionario',
				'type' => 'LEFT',
				'conditions' => 'UsuariosQuestionario.codigo_usuario = Resposta.codigo_usuario AND UsuariosQuestionario.codigo_questionario = Questionario.codigo',
				),
			);
		$conditions = array(
			'Resposta.codigo_questionario' => $codigo_questionario, 
			'Resposta.codigo_usuario' => $usuario_info['Usuario']['codigo'],
			'Resposta.codigo_historico_resposta' => $codigo_usuario_questionario['UsuariosQuestionario']['codigo'],
			'UsuariosQuestionario.codigo' => $codigo_usuario_questionario['UsuariosQuestionario']['codigo']
			);

		$fields = array(
			'Questionario.codigo',
			'Questionario.descricao',
			'Questao.codigo',
			'Questao.label',
			'UsuariosQuestionario.codigo',
			'UsuariosQuestionario.codigo_usuario',
			'UsuariosQuestionario.codigo_questionario',
			'Resposta.codigo',
			'Resposta.codigo_funcionario',
			'Resposta.codigo_questao',
			'Resposta.codigo_resposta',
			'Resposta.pontos',
			'Resposta.label',
			'Resposta.label_questao',
			'Resposta.codigo_questionario',
			'Resposta.codigo_usuario'
			);	

		$group = array(
			'Questionario.codigo',
			'Questionario.descricao',
			'Questao.codigo',
			'Questao.label',
			'UsuariosQuestionario.codigo',
			'UsuariosQuestionario.codigo_usuario',
			'UsuariosQuestionario.codigo_questionario',
			'Resposta.codigo',
			'Resposta.codigo_funcionario',
			'Resposta.codigo_questao',
			'Resposta.codigo_resposta',
			'Resposta.pontos',
			'Resposta.label',
			'Resposta.label_questao',
			'Resposta.codigo_questionario',
			'Resposta.codigo_usuario'
			);

		$dados_questionarios = $this->Resposta->find('all', compact('conditions', 'joins', 'fields', 'group'));

		if(!empty($dados_questionarios)){
			$checkups = $dados_questionarios[0]['Questionario']['descricao'];

			$pontuacao = $this->Resposta->find('first', array('conditions' => $conditions, 'joins' => $joins, 'fields' => 'sum(Resposta.pontos) as pontos'));
			if(!empty($pontuacao)){
				$pontos = $pontuacao[0]['pontos'];
				$descricao = $this->Resultado->find('first', array('conditions' => array('codigo_questionario' => $dados_questionarios[0]['Questionario']['codigo'], 'valor >= '.$pontos )));;
				$resultado = $pontos.' - '.$descricao['Resultado']['descricao'];
				$this->set(compact('pontos', 'resultado'));
			}
			
			$this->set(compact('checkups'));
		}

		$this->set(compact('dados_questionarios'));
	}
	
}

?>
