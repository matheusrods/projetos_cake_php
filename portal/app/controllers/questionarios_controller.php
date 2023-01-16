<?php
class QuestionariosController extends AppController {
	
	public $helpers = array('BForm', 'Html', 'Ajax');

	var $uses = array(  
		'Questionario',
		'ClienteQuestionarios',
		'QuestionarioTipo'
	);

	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow('index','responder_questionario','responder_questionario_index','salva_ajax','listagem_resultados','voltar_questao', 'permissao', 'lista_permissoes', 'salvar_permissoes', 'salvar_permissao_ajax', 'deletar_permissao_ajax','feedback_vermelho_covid','retira_permissao','listagem_retira_permissoes','salvar_retira_permissoes','salvar_retira_permissao_ajax','deletar_retira_permissao_ajax');
	} 

	public function index() {
		$this->pageTitle = 'Questionários';
	}

	public function incluir()
	{
		$this->pageTitle = 'Incluir questionário';

		if($this->RequestHandler->isPost()) {
			if(isset($this->data['Questionario']['background']['error']) && $this->data['Questionario']['background']['error'] == '0') {
				$nome = str_replace(' ', '-', Comum::tirarAcentos($this->data['Questionario']['descricao']));
				$path_background = $this->_upload($this->data['Questionario']['background'], 'background', $nome, '1200');
				if ( !empty($path_background['error']) ){
					$this->Questionario->invalidate('background', $path_background['error']);
					$this->BSession->setFlash('save_error');
				} else {
					$this->data['Questionario']['background'] = $path_background['path'];
				}
			} else {
				if(isset($this->data['Questionario']['background'])) {
					unset($this->data['Questionario']['background']);
				}
			}

			if(isset($this->data['Questionario']['icone']['error']) && $this->data['Questionario']['icone']['error'] == '0') {
				$nome = str_replace(' ', '-', Comum::tirarAcentos($this->data['Questionario']['descricao']));
				$path_icone = $this->_upload($this->data['Questionario']['icone'], 'icone', $nome, '170');
				if ( !empty($path_icone['error']) ){
					$this->Questionario->invalidate('icone', $path_icone['error']);
					$this->BSession->setFlash('save_error');
				} else {
					$this->data['Questionario']['icone'] = $path_icone['path'];
				}
			} else {
				if(isset($this->data['Questionario']['icone'])) {
					unset($this->data['Questionario']['icone']);
				}
			}

            if(isset($this->data['Questionario']['img_app']['error']) && $this->data['Questionario']['img_app']['error'] == '0') {
                $nome = str_replace(' ', '-', Comum::tirarAcentos($this->data['Questionario']['descricao']));
                $path_icone = $this->_upload($this->data['Questionario']['img_app'], 'icone', $nome, '100');
                if ( !empty($path_icone['error']) ){
                    $this->Questionario->invalidate('img_app', $path_icone['error']);
                    $this->BSession->setFlash('save_error');
                } else {
                    $this->data['Questionario']['img_app'] = $path_icone['path'];
                }
            } else {
                if(isset($this->data['Questionario']['img_app'])) {
                    unset($this->data['Questionario']['img_app']);
                }
            }

			if ( empty($path_background['error']) && empty($path_icone['error']) ){
				if($this->Questionario->gravar($this->data)) {
					$this->BSession->setFlash('save_success');
					return $this->redirect(array('action' => 'index'));
				} else {
					$this->BSession->setFlash('save_error');
				}
			} else {
				unset($this->data['Questionario']['background']);
				unset($this->data['Questionario']['icone']);
			}
		}

		$questionario_tipo = $this->QuestionarioTipo->find('list',array('fields' => array('codigo','descricao')));
		$this->set(compact('questionario_tipo'));

		// $this->render('gravar');
	}

	public function editar($codigo = null)
	{
		$this->pageTitle = 'Editar questionário';

		if($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {
			
			if(isset($this->data['Questionario']['background']['error']) && $this->data['Questionario']['background']['error'] == '0') {
				$nome = str_replace(' ', '-', Comum::tirarAcentos($this->data['Questionario']['descricao']));
				$path_background = $this->_upload($this->data['Questionario']['background'], 'background', $nome, '1200');
				if ( !empty($path_background['error']) ){
					$this->Questionario->invalidate('background', $path_background['error']);
					$this->BSession->setFlash('save_error');
				} else {
					$this->data['Questionario']['background'] = $path_background['path'];
				}
			} else {
				if(isset($this->data['Questionario']['background'])) {
					unset($this->data['Questionario']['background']);
				}
			}

			if(isset($this->data['Questionario']['icone']['error']) && $this->data['Questionario']['icone']['error'] == '0') {
				$nome = str_replace(' ', '-', Comum::tirarAcentos($this->data['Questionario']['descricao']));
				$path_icone = $this->_upload($this->data['Questionario']['icone'], 'icone', $nome, '170');
				if ( !empty($path_icone['error']) ){
					$this->Questionario->invalidate('icone', $path_icone['error']);
					$this->BSession->setFlash('save_error');
				} else {
					$this->data['Questionario']['icone'] = $path_icone['path'];
				}
			} else {
				if(isset($this->data['Questionario']['icone'])) {
					unset($this->data['Questionario']['icone']);
				}
			}

			if(isset($this->data['Questionario']['img_app']['error']) && $this->data['Questionario']['img_app']['error'] == '0') {
				$nome = str_replace(' ', '-', Comum::tirarAcentos($this->data['Questionario']['descricao']));
				$path_icone = $this->_upload($this->data['Questionario']['img_app'], 'icone', $nome, '100');
				if ( !empty($path_icone['error']) ){
					$this->Questionario->invalidate('img_app', $path_icone['error']);
					$this->BSession->setFlash('save_error');
				} else {
					$this->data['Questionario']['img_app'] = $path_icone['path'];
				}
			} else {
				if(isset($this->data['Questionario']['img_app'])) {
					unset($this->data['Questionario']['img_app']);
				}
			}

			if ( empty($path_background['error']) && empty($path_icone['error']) ){
				if($this->Questionario->gravar($this->data)) {
					$this->BSession->setFlash('save_success');
					//return $this->redirect(array('action' => 'index'));
				} else {
					$this->BSession->setFlash('save_error');
				}
			} else {
				unset($this->data['Questionario']['background']);
				unset($this->data['Questionario']['icone']);
			}
			// debug($this->data['Questionario']);
		}

		$questionario_tipo = $this->QuestionarioTipo->find('list',array('fields' => array('codigo','descricao')));
		$this->set(compact('questionario_tipo'));

		$this->data = $this->Questionario->findByCodigo($codigo);
		// debug($this->data);
		// $this->render('gravar');
	}

	public function listagem() 
	{
		$this->layout = 'ajax'; 
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Questionario->name);
		$conditions = $this->Questionario->converteFiltroEmCondition($filtros);
		$conditions['Questionario.codigo_empresa'] = $this->BAuth->user('codigo_empresa');
		$order = 'Questionario.codigo';

		$this->paginate['Questionario'] = array(
			'conditions' => $conditions,
			'limit' => 50, 
			'order' => $order
			);

		$questionarios = $this->paginate();
		$this->set(compact('questionarios'));
		$this->Filtros->limpa_sessao($this->Questionario->name);
	}

	public function excluir($codigo = null) {
		if(is_null($codigo)) {	
			$this->BSession->setFlash('erro_delete');
			return $this->redirect(array('action' => 'index'));
		}
		if($this->Questionario->excluiEmCascata($codigo)) {
			$this->BSession->setFlash('save_success');
		} else {
			$this->BSession->setFlash('save_error');
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function responder_questionario_index()
	{
		$this->pageTitle = 'Responder questionário';
		$conditions['Questionario.codigo_empresa'] = $this->BAuth->user('codigo_empresa');
		// exibe questionario somente se existir ao menos uma questao cadastrada
		$conditions['(SELECT TOP 1 count(q.codigo) FROM questoes q WHERE q.codigo_questionario = Questionario.codigo) >'] = '0';

		$fields = array(
			'*',
			'(SELECT count(r.codigo) 
			FROM respostas r
			WHERE r.codigo_resposta IN (SELECT q.codigo FROM questoes q WHERE q.codigo_questionario = Questionario.codigo AND q.codigo_questao IS NOT NULL AND q.codigo_proxima_questao IS NULL)
			) as LastAnswer'
			);
		$order = 'Questionario.ordem DESC';

		$this->paginate['Questionario'] = array(
			'recursive' => -1,
			'conditions' => $conditions,
			'limit' => 50, 
			'order' => $order,
			'fields' => $fields
			);

		$questionarios = $this->paginate();
		
		$this->set(compact('questionarios'));
		$this->Filtros->limpa_sessao($this->Questionario->name);
	}

	public function responder_questionario($codigo_questionario)
	{
		$this->layout = 'default_questionario';
		
		$this->Questionario->recursive = -1;
		$questionario = $this->Questionario->findByCodigo($codigo_questionario);
		$this->pageTitle = $questionario['Questionario']['descricao'];

		$this->Resposta =& ClassRegistry::init('Resposta');
		$questaoIniciada = $this->Resposta->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'Questao.codigo_questionario' => $codigo_questionario,
				'Resposta.codigo_usuario' => $this->BAuth->user('codigo'),
				'UsuariosQuestionario.finalizado' => NULL,
				),
			'joins' => array(
				array(
					'table' => 'questoes',
					'alias' => 'Questao',
					'type' => 'INNER',
					'conditions' => 'Questao.codigo = Resposta.codigo_resposta'
					),
				array(
					'table' => 'usuarios_questionarios',
					'alias' => 'UsuariosQuestionario',
					'type' => 'INNER',
					'conditions' => 'UsuariosQuestionario.codigo = Resposta.codigo_historico_resposta'
					)
				),
			'order' => 'Resposta.codigo DESC',
			'fields' => array(
				'Questao.codigo_proxima_questao',
				)
			)
		);

		$conditions = array(
			'Questao.codigo_questionario' => $codigo_questionario
			);

		if(empty($questaoIniciada['Questao']['codigo_proxima_questao'])) {
			$primeira_questao = true;
			$conditions['Questao.codigo_questao'] = NULL;
			$conditions['(SELECT TOP 1 q.codigo from questoes q WHERE q.codigo_proxima_questao = Questao.codigo)'] = NULL;
		} else {
			$primeira_questao = false;
			$conditions['Questao.codigo'] = $questaoIniciada['Questao']['codigo_proxima_questao'];
		}

		$questaoInicial = $this->Questionario->Questao->find('first', array(
			'conditions' => $conditions
			)
		);

		$pular_questao = $this->Questionario->pula_questao_respondida($questaoInicial['Questao']['codigo'], $this->BAuth->user('codigo'));
		$this->set(compact('questionario', 'questaoInicial', 'primeira_questao', 'codigo_questionario', 'pular_questao'));
		
	}

	public function listagem_resultados($codigo_questionario)
	{
		$this->pageTitle = 'Questões respondidas';
		$this->Resposta =& ClassRegistry::init('Resposta');
		$questoes_respondidas = $this->Resposta->find('all', array(
			'conditions' => array(
				'Resposta.codigo_usuario_inclusao' => $this->BAuth->user('codigo'),
				'Resposta.codigo_questionario' => $codigo_questionario
				) ,
			'order' => 'Resposta.codigo ASC'
			)
		);
		$this->set(compact('questoes_respondidas'));
	}

	public function salva_ajax($codigo_resposta = null, $codigo_questao = null, $is_recursive = false)
	{
		$this->autoRender = false;
		$Usuario =& ClassRegistry::init('Usuario');
		$Resposta =& ClassRegistry::init('Resposta');
		$this->UsuariosQuestionario =& ClassRegistry::init('UsuariosQuestionario');
		if(is_null($codigo_resposta)) $codigo_resposta = $_POST['codigo_resposta'];
		if(is_null($codigo_questao)) $codigo_questao = $_POST['codigo_questao'];

		$questao 	= $this->Questionario->Questao->findByCodigo($codigo_questao);
		$resposta 	= $this->Questionario->Questao->findByCodigo($codigo_resposta);

		$codigo_historico = $this->UsuariosQuestionario->verificaExistenciaHistoricoAtivo($this->BAuth->user('codigo'), $this->BAuth->user('codigo_empresa'), $resposta);
		
		$dado = array();
		$dado['Resposta']['codigo_usuario'] 		= $this->BAuth->user('codigo');
		$dado['Resposta']['codigo_questao'] 		= $questao['Questao']['codigo'];
		$dado['Resposta']['codigo_resposta'] 		= $resposta['Questao']['codigo'];
		$dado['Resposta']['codigo_label_questao'] 	= $resposta['Questao']['codigo_label_questao'];
		$dado['Resposta']['pontos'] 				= $resposta['Questao']['pontos'];
		$dado['Resposta']['label'] 					= $resposta['LabelQuestao']['label'];
		$dado['Resposta']['label_questao'] 			= $questao['LabelQuestao']['label'];
		$dado['Resposta']['codigo_questionario'] 	= $resposta['Questao']['codigo_questionario'];
		$dado['Resposta']['codigo_historico_resposta'] 	= $codigo_historico;

		//setar o codigo da empresa caso seja um usuario por fora		
		if($this->BAuth->user('codigo_empresa') == "") {
			//necessário para gravar na tabela
		 	$dado['Resposta']['codigo_empresa'] = 1;
		}

		$html = false;
		$finalizado = false;
		$retorno = false;

		if($Resposta->incluir($dado)) {			
			$html = '';
			
			$proxima_questao = $this->Questionario->Questao->findByCodigo($resposta['Questao']['codigo_proxima_questao']);
			$retorno = $this->Questionario->pula_questao_respondida($proxima_questao['Questao']['codigo'], $this->BAuth->user('codigo'));
			if(!empty($retorno)) {
				return $this->salva_ajax($retorno[0][0]['codigo'], $retorno[0][0]['codigo_questao'], true);
			}

			if(!empty($resposta['Questao']['codigo_proxima_questao'])) {

				// monta o html da questao a ser respondida
				$html .= '<div class="margin-top-30">';
				$html .= 		'<div class="row-fluid">';
				$html .= 			'<div class="theme-dark titleH2">';
				$html .= 				'<h2 id="question" style="font-size: 40px;">'.$proxima_questao['LabelQuestao']['label'].'</h2>';
				$html .= 			'</div>';
				$html .= 		'</div>';
				$html .= '</div>';
				$html .= '<div class="application" data_codigo_questao="'.$proxima_questao['Questao']['codigo'].'">';
				$html .= '<div class="row-fluid respostas">';
				$html .= '<div class="width: 70%; float: right;">';

				// monta as respostas da questao
				foreach ($proxima_questao['Respostas'] as $key => $resposta) { 
					$html .= '<div class="check">';
					$html .= 	'<div class="caixa">';
					$html .=	 	'<div class="col-md-10">';
					$html .=			$resposta['Respostas'][0]['label'];					
					$html .=	 	'</div>';
					$html .=	 	'<div class="col-md-2">';
					$html .=			'<input type="radio" name="resposta" value="'.$resposta['codigo'].'">';
					$html .=			'<div class="js-radio"></div>';					
					$html .=	 	'</div>';
					$html .=     	'<div style="clear: both;"></div>';					
					$html .=  	'</div>';
					$html .=  '</div>';
				} 

				$html .= '</div>';
				$html .= '</div>';
				$html .= '<div style="clear: both;"><br /><br /><br /></div>';
				$html .= '<div class="row-fluid avancar"><div class="span12">';
				$html .= '<div class="js-botao-voltar">Voltar</div>';
				$html .= '<div class="js-botao-avancar">Avançar</div>';
				$html .= '</div></div>';
				$html .= '</div>';

			} else {

				//Traz a quantidade de pontos total do usuario para o questionario

				$this->Resposta =& ClassRegistry::init('Resposta');
				$pontos = $this->Resposta->find('first', array(
					'conditions' => array(
						'Resposta.codigo_questionario' => $resposta['Questao']['codigo_questionario'],
						'Resposta.codigo_usuario' => $this->BAuth->user('codigo'),
						'Resposta.codigo_historico_resposta' => $codigo_historico
						),
					'fields' => array(
						'sum(Resposta.pontos) as soma_pontos'
						)
					));

				//Como estou no CONTROLLER e nao no model preciso invocar o model do controller pra chamar o outro
				$descricao = $this->Questionario->Resultado->find('first', array(
					'recursive'  => -1,
					'conditions' => array(
						'Resultado.valor >=' => $pontos[0]['soma_pontos'],
						'codigo_questionario' => $questao['Questionario']['codigo']
						),
					'fields' => array(
						'Resultado.descricao'
						),
					'order' => 'Resultado.valor asc'
					)
				);

				// inclui vinculo funcionario questionario (checa como concluido)
				$this->UsuariosQuestionario->atualizar(array(
					'UsuariosQuestionario' => array(
						'codigo' => $codigo_historico,
						'codigo_usuario' => $this->BAuth->user('codigo'),
						'codigo_questionario' => $questao['Questionario']['codigo'],
						'finalizado' => 1,
						'concluido' => date('Y-m-d')
						)
					)
				);
				
				if(!empty($descricao)){
					$descricao_resultado = $descricao['Resultado']['descricao']; 
				}else{
					$descricao_resultado = 'Risco não mensurado';
				}
				
				$pontos_user 		 = $pontos[0]['soma_pontos'];
				
				// monta o html de termino de formulario
				$html .= '<div class="row-fluid">';
				$html .= '	<div class="span12 text-center margin-top-30 bordered">';
				$html .= '		<h4>Pontuação: ' . $pontos_user . ' - ' . $descricao_resultado.'</h4>';
				$html .= '	</div>';
				$html .= '</div>';
				
				$finalizado = true;
			}

		}
		return json_encode(array('retorno' => $retorno, 'html' => $html, 'finalizado' => $finalizado));
	}

	public function voltar_questao()
	{
		$this->autoRender = false;
		$this->Resposta =& ClassRegistry::init('Resposta');
		$toDelete = $this->Resposta->find('list', array(
			'conditions' => array(
				'Resposta.codigo_usuario_inclusao' => $this->BAuth->user('codigo'),
				'Resposta.codigo_empresa' => $this->BAuth->user('codigo_empresa'),
				'Resposta.codigo_questionario' => $_POST['codigo_questionario']
				),
			'limit' => 1,
			'order' => 'Resposta.codigo DESC',
			'fields' => array(
				'Resposta.codigo'
				)
			)
		);
		return $this->Resposta->delete($toDelete);
	}

	function _upload($file, $pasta, $nome, $tamanho_y){
		if( !preg_match('@\.(jpg|png|jpeg)$@i', $file['name']) ) {
			return array('error' => 'Arquivo inválido! Favor escolher arquivo jpg, jpeg ou png.');
		}
		if ($file['size'] >= 2200000){
			return array('error' => 'Tamanho máximo excedido!');
		}

		$array_path_arquivo = explode(DS, $file['tmp_name']);
		array_pop($array_path_arquivo);
		$array_path_arquivo[] = $file['name'];
		$novo_path_arquivo = implode(DS, $array_path_arquivo);

		if (copy($file['tmp_name'],$novo_path_arquivo)){
			$url_imagem = AppModel::sendFileToServer('@'.$novo_path_arquivo);			
			return array('path' => $url_imagem->{'response'}->{'path'});
		}
		return false;
	}

	public function permissao($codigo_questionario)
	{
		//titulo
		$this->pageTitle = 'Permissões';
		$this->codigo_questionario = $codigo_questionario;
		$this->set(compact('codigo_questionario'));
	}

	public function lista_permissoes($codigo_questionario){		

		$this->layout = 'ajax';
		$this->loadModel('GrupoEconomico');
		$this->loadModel('GrupoEconomicoCliente');
		$codigo_cliente="";

		$filtros = $this->Filtros->controla_sessao($this->data, $this->GrupoEconomico->name);


		 //trazer o codigo_cliente do usuario
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $ge = $this->GrupoEconomicoCliente->find('first', array('conditions' => array("GrupoEconomicoCliente.codigo_cliente" => $filtros['codigo_cliente'])));

        //setando a model para os filtros
        $this->data['GrupoEconomico'] = $filtros;

        //condicao adicionada para ajudar na busca
        $conditions[] = array('Unidade.ativo' => 1, 'Unidade.e_tomador !=' => 1, 'GrupoEconomicoCliente.codigo_grupo_economico' => $ge['GrupoEconomicoCliente']['codigo_grupo_economico']);
        //debug($conditions);exit;

        //fields
        $fields = array(            
            'Unidade.codigo',
            'Unidade.razao_social',
            'Unidade.nome_fantasia',
            'Unidade.tipo_unidade',
            'Unidade.e_tomador'
        );

        //joins
        $joins = array(
            array(
                'table' => 'cliente',
                'alias' => 'Unidade',
                'type' => 'INNER',
                'conditions' => 'Unidade.codigo = GrupoEconomicoCliente.codigo_cliente'
            )
        );

        //buscando todas as unidades do cliente
        $lista_clientes_grupo = $this->GrupoEconomicoCliente->find('all', array('fields' => $fields,'conditions' => $conditions,'joins' => $joins, 'order' => array('Unidade.codigo','Unidade.nome_fantasia')));

        //varre os dados da lista
        $lista_unidades_combo = array();
        $lista_unidades_combo_ge = array();
        //verifica se tem lista de clientes grupo
        if(!empty($lista_clientes_grupo)) {
        	//varre a lista de clientes
        	foreach($lista_clientes_grupo as $unidades){
        		//verifica se é o grupo economico que está ativo para tirar
        		if($codigo_cliente == $unidades['Unidade']['codigo']) {
        			continue;
        		}

        		//verifica se a empresa é fiscal para gerar o grupo economico
        		if($unidades['Unidade']['tipo_unidade'] == 'F' && $unidades['Unidade']['e_tomador'] == 0) {
        			$lista_unidades_combo_ge[$unidades['Unidade']['codigo']] = $unidades['Unidade']['codigo'] ." - ". $unidades['Unidade']['nome_fantasia'];
        		}
        		
        		//combo das unidades que pode ser todas
        		$lista_unidades_combo[$unidades['Unidade']['codigo']] = $unidades['Unidade']['codigo'] ." - ". $unidades['Unidade']['nome_fantasia'];
	        }
	    }

	    //buscando todas as permissoes deste questionario
	    $this->loadModel('ClienteQuestionarios');
	    $lista_permissoes = $this->ClienteQuestionarios->find('all', array('fields' => array('codigo_cliente'),'conditions' => array("ativo"=>1, 'codigo_questionario' => $codigo_questionario)));
	    $permissoes = array();
	    foreach($lista_permissoes as $v){
	    	$permissoes[] = $v['ClienteQuestionarios']['codigo_cliente'];
	    }

        $this->set(compact('lista_clientes_grupo', 'codigo_cliente', 'lista_unidades_combo', 'lista_unidades_combo_ge', 'codigo_questionario', 'permissoes'));
	}

	/**
     * [salvar_permissoes método para gravar os clientes com permissão para o questionário]
     * @return [type] [description]
     */
    public function salvar_permissoes($codigo_questionario)
    {
    	
		//seta que não vai ter layout ctp
		$this->layout = false;

		$dados = array();
		foreach($this->data['UnidadeCodigo'] as $v){
			$dados = array(
				"codigo_cliente" => $v['codigo'],
				"codigo_questionario" => $codigo_questionario,
				"ativo" => 1
			);

			$busca = $this->ClienteQuestionarios->find('list',array('conditions' => $dados));
			if(!$busca){
				$this->ClienteQuestionarios->incluir($dados);
			}   
			
		}

		$this->BSession->setFlash('save_success');
		return $this->redirect(array('action' => 'index'));

    }

    public function salvar_permissao_ajax($codigo_questionario, $codigo_cliente)
    {
    	
		$this->autoRender = false;

    	$this->loadModel('ClienteQuestionarios');

    	$dados = array(
			"codigo_cliente" => $codigo_cliente,
			"codigo_questionario" => $codigo_questionario,
			"ativo" => 1
		);

    	$msg_erro = '';

		//seta que vamos trabalhar com transacao
		$this->ClienteQuestionarios->query('begin transaction');

		if(!$this->ClienteQuestionarios->incluir($dados)){
			//desfaz o que foi feito no banco de dados
        	$this->ClienteQuestionarios->rollback();
			$retorno = false;	             
        }else{
        	$this->ClienteQuestionarios->commit();
        	$retorno = true;
        }

		return json_encode(array('retorno' => $retorno));           	
        
    }

    public function deletar_permissao_ajax($codigo_questionario, $codigo_cliente)
    {
    	
		$this->autoRender = false;

    	$this->loadModel('ClienteQuestionarios');

    	//buscar id
    	$codigo_cliente_questionarios = $this->ClienteQuestionarios->find('list',array('fields' => array('codigo'),'conditions' => array("ativo"=>1, 'codigo_questionario' => $codigo_questionario, 'codigo_cliente' => $codigo_cliente)));   	
    	if($codigo_cliente_questionarios){

    		$id = array_shift(array_values($codigo_cliente_questionarios));

    		//seta que vamos trabalhar com transacao
			$this->ClienteQuestionarios->query('begin transaction');

			if(!$this->ClienteQuestionarios->excluir($id)){
				//desfaz o que foi feito no banco de dados
	        	$this->ClienteQuestionarios->rollback();
				$retorno = false;	             
	        }else{
	        	$this->ClienteQuestionarios->commit();
	        	$retorno = true;
	        }

			return json_encode(array('retorno' => $retorno));  
	    	
	    }
    }

    /**
     * [feedback_vermelho_covid metodo para inserir qual o feedback quando o resultado for vermelho em tela do app]
     * @param  [type] $codigo_questionario [codigo do questionaroi que deverá retornar a descrição]
     * @return [type]                      [description]
     */
    public function feedback_vermelho_covid($codigo_cliente,$codigo_questionario)
    {

    	$this->pageTitle = 'Editar Feedback Vermelho Covid';

		if($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {
			
			//verifica se tem dados
			if(!empty($this->data['ClienteQuestionarios']['feedback_vermelho_covid'])) {

				// debug($this->data);exit;

				if ($this->ClienteQuestionarios->atualizar($this->data)) {

	                $this->BSession->setFlash('save_success');
	                $this->redirect(array('controller' => 'questionarios', 'action' => 'permissao', $codigo_questionario));
	            } 
	            else {
	                $this->BSession->setFlash('save_error');
	            }        

			}//fim feedback

		}//fim post

		$dados = $this->ClienteQuestionarios->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente, 'codigo_questionario' => $codigo_questionario)));

		// debug($dados);exit;
		 
		$this->set(compact('dados'));
    }// fim feedback_vermelho_covid

    /**
     * [retira_permissao metodo para retirar a permissao do questionario para o cliente]
     * @param  [type] $codigo_questionario [description]
     * @return [type]                      [description]
     */
    public function retira_permissao($codigo_questionario)
	{
		//titulo
		$this->pageTitle = 'Retirar Permissões';
		$this->codigo_questionario = $codigo_questionario;
		$this->set(compact('codigo_questionario'));
	}//fim retira_permissao

	/**
	 * [listagem_retira_permissoes listagem dos clientes que ira retirar a permissao]
	 * @param  [type] $codigo_questionario [description]
	 * @return [type]                      [description]
	 */
	public function listagem_retira_permissoes($codigo_questionario)
	{

		$this->layout = 'ajax';
		$this->loadModel('GrupoEconomico');
		$this->loadModel('GrupoEconomicoCliente');
		$codigo_cliente="";

		$filtros = $this->Filtros->controla_sessao($this->data, $this->GrupoEconomico->name);


		 //trazer o codigo_cliente do usuario
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $ge = $this->GrupoEconomicoCliente->find('first', array('conditions' => array("GrupoEconomicoCliente.codigo_cliente" => $filtros['codigo_cliente'])));

        //setando a model para os filtros
        $this->data['GrupoEconomico'] = $filtros;

        //condicao adicionada para ajudar na busca
        $conditions[] = array('Unidade.ativo' => 1, 'Unidade.e_tomador !=' => 1, 'GrupoEconomicoCliente.codigo_grupo_economico' => $ge['GrupoEconomicoCliente']['codigo_grupo_economico']);
        //debug($conditions);exit;

        //fields
        $fields = array(            
            'Unidade.codigo',
            'Unidade.razao_social',
            'Unidade.nome_fantasia',
            'Unidade.tipo_unidade',
            'Unidade.e_tomador'
        );

        //joins
        $joins = array(
            array(
                'table' => 'cliente',
                'alias' => 'Unidade',
                'type' => 'INNER',
                'conditions' => 'Unidade.codigo = GrupoEconomicoCliente.codigo_cliente'
            )
        );

        //buscando todas as unidades do cliente
        $lista_clientes_grupo = $this->GrupoEconomicoCliente->find('all', array('fields' => $fields,'conditions' => $conditions,'joins' => $joins, 'order' => array('Unidade.codigo','Unidade.nome_fantasia')));

        //varre os dados da lista
        $lista_unidades_combo = array();
        $lista_unidades_combo_ge = array();
        //verifica se tem lista de clientes grupo
        if(!empty($lista_clientes_grupo)) {
        	//varre a lista de clientes
        	foreach($lista_clientes_grupo as $unidades){
        		//verifica se é o grupo economico que está ativo para tirar
        		if($codigo_cliente == $unidades['Unidade']['codigo']) {
        			continue;
        		}

        		//verifica se a empresa é fiscal para gerar o grupo economico
        		if($unidades['Unidade']['tipo_unidade'] == 'F' && $unidades['Unidade']['e_tomador'] == 0) {
        			$lista_unidades_combo_ge[$unidades['Unidade']['codigo']] = $unidades['Unidade']['codigo'] ." - ". $unidades['Unidade']['nome_fantasia'];
        		}
        		
        		//combo das unidades que pode ser todas
        		$lista_unidades_combo[$unidades['Unidade']['codigo']] = $unidades['Unidade']['codigo'] ." - ". $unidades['Unidade']['nome_fantasia'];
	        }
	    }

	    //buscando todas as permissoes deste questionario
	    $this->loadModel('ClienteQuestionarios');
	    $lista_permissoes = $this->ClienteQuestionarios->find('all', array('fields' => array('codigo_cliente'),'conditions' => array("ativo"=>1, 'codigo_questionario' => $codigo_questionario)));
	    $permissoes = array();
	    foreach($lista_permissoes as $v){
	    	$permissoes[] = $v['ClienteQuestionarios']['codigo_cliente'];
	    }

        $this->set(compact('lista_clientes_grupo', 'codigo_cliente', 'lista_unidades_combo', 'lista_unidades_combo_ge', 'codigo_questionario', 'permissoes'));
	}//fim listagem_retira_permissao

	/**
     * [salvar_permissoes método para gravar os clientes com permissão para o questionário]
     * @return [type] [description]
     */
    public function salvar_retira_permissoes($codigo_questionario)
    {
    	
		//seta que não vai ter layout ctp
		$this->layout = false;

		$dados = array();
		// debug($this->data['UnidadeCodigo']);exit;
		foreach($this->data['UnidadeCodigo'] as $v){
			$dados = array(
				"codigo_cliente" => $v['codigo'],
				"codigo_questionario" => $codigo_questionario,
				"inativar_cliente" => 1,
				"ativo" => 1
			);

			$busca = $this->ClienteQuestionarios->find('list',array('conditions' => $dados));
			if(!$busca){
				$this->ClienteQuestionarios->incluir($dados);
			}   
			
		}

		$this->BSession->setFlash('save_success');
		return $this->redirect(array('action' => 'index'));

    }//fim salvar_permissoes

    /**
     * [salvar_permissao_ajax salvar ]
     * @param  [type] $codigo_questionario [description]
     * @param  [type] $codigo_cliente      [description]
     * @return [type]                      [description]
     */
    public function salvar_retira_permissao_ajax($codigo_questionario, $codigo_cliente)
    {
    	
		$this->autoRender = false;

    	$this->loadModel('ClienteQuestionarios');

    	$dados = array(
			"codigo_cliente" => $codigo_cliente,
			"codigo_questionario" => $codigo_questionario,
			"inativar_cliente" => 1,
			"ativo" => 1
		);

    	$msg_erro = '';

		//seta que vamos trabalhar com transacao
		$this->ClienteQuestionarios->query('begin transaction');

		if(!$this->ClienteQuestionarios->incluir($dados)){
			//desfaz o que foi feito no banco de dados
        	$this->ClienteQuestionarios->rollback();
			$retorno = false;	             
        }else{
        	$this->ClienteQuestionarios->commit();
        	$retorno = true;
        }

		return json_encode(array('retorno' => $retorno));           	
        
    }//fim salvar_retira_permissao_ajax

    /**
     * [deletar_retira_permissao_ajax deltar retirar permissao ajax]
     * @param  [type] $codigo_questionario [description]
     * @param  [type] $codigo_cliente      [description]
     * @return [type]                      [description]
     */
    public function deletar_retira_permissao_ajax($codigo_questionario, $codigo_cliente)
    {
    	
		$this->autoRender = false;

    	$this->loadModel('ClienteQuestionarios');

    	//buscar id
    	$codigo_cliente_questionarios = $this->ClienteQuestionarios->find('list',array('fields' => array('codigo'),'conditions' => array("ativo"=>1, 'codigo_questionario' => $codigo_questionario, 'codigo_cliente' => $codigo_cliente)));   	
    	if($codigo_cliente_questionarios){

    		$id = array_shift(array_values($codigo_cliente_questionarios));

    		//seta que vamos trabalhar com transacao
			$this->ClienteQuestionarios->query('begin transaction');

			if(!$this->ClienteQuestionarios->excluir($id)){
				//desfaz o que foi feito no banco de dados
	        	$this->ClienteQuestionarios->rollback();
				$retorno = false;	             
	        }else{
	        	$this->ClienteQuestionarios->commit();
	        	$retorno = true;
	        }

			return json_encode(array('retorno' => $retorno));  
	    	
	    }
    }//fim deletar retira permissao ajax

}