<?php
App::import('Component', array('StringView', 'Mailer.Scheduler'));
class PontoEletronicoController extends AppController {
	public $name = 'PontoEletronico';
	public $uses = array('Usuario', 'PontoEletronico','TipoPontoEletronico','AutorizacaoHoraExtra','Usuario');
	public $components = array('RequestHandler');
	public $autoRender = false;


	function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow('digital', 'ponto','ponto2', 'digitais','historico_listagem');
		$this->Session->delete('Config');
	}

	function carregaCombos(){
		$authUsuario = $this->BAuth->user();
		$this->data['PontoEletronico']['codigo_gestor'] = $authUsuario['Usuario']['codigo'];
		$lista = $this->Usuario->listaUsuariosDepartamento($authUsuario['Usuario']['codigo_departamento']);
		$this->set(compact('lista'));
	}

	function index(){
		$this->autoRender = true;
		$this->pageTitle = "Permissão de Hora Extra";
		$this->carregaCombos();
		
		$authUsuario = $this->BAuth->user();
		$gestor = FALSE;
		if($authUsuario['Usuario']['gestor']){
			$gestor = TRUE;
		}	
		$this->data['PontoEletronico']['gestor'] = $gestor;
        $this->data['AutorizacaoHoraExtra'] = $this->Filtros->controla_sessao($this->data, "PontoEletronico");
	}

	function listagem(){
		$this->autoRender = true;
 		$this->data['AutorizacaoHoraExtra'] = $this->Filtros->controla_sessao($this->data, "PontoEletronico");		
 		$permite = $this->data['AutorizacaoHoraExtra']['gestor'];
 		$this->AutorizacaoHoraExtra->bindAutorizacaoHoraExtra();
		unset($this->data['AutorizacaoHoraExtra']['codigo_gestor']);
		$conditions = $this->AutorizacaoHoraExtra->converteFiltrosEmConditions($this->data);
		$this->paginate['AutorizacaoHoraExtra'] = array(
           'conditions' => $conditions,
            'limit' => 50,
            'order' => 'AutorizacaoHoraExtra.codigo DESC',	
       	);
   		
        $listagem = $this->paginate('AutorizacaoHoraExtra');
		$this->set(compact('permite','listagem','gestor'));
	}

	function incluir(){
		$this->autoRender = true;
		$this->pageTitle = "Incluir Permissão de Hora Extra";

		if($this->RequestHandler->isPost()) {	
			$data['AutorizacaoHoraExtra'] = $this->data['PontoEletronico'];
			$data['AutorizacaoHoraExtra']['data_de_inclusao'] = date('Y-m-d H:i:s');

			$retorno = $this->AutorizacaoHoraExtra->incluir_hora_extra($data);

			if($retorno){
				$erro = $retorno['erro'];
				if(isset($retorno['sucesso'])){
					if(isset($retorno['erro'])){
						$this->BSession->setFlash(array(MSGT_WARNING, "Existem registros que não foram salvos.Motivo: $erro"));
						$this->redirect(array('action' => 'index'));	
					}else{
						$this->BSession->setFlash('save_success');
						$this->redirect(array('action' => 'index'));
					}	
				}else{
					$this->BSession->setFlash(array(MSGT_ERROR, "$erro"));
				}
			}else{
				$this->BSession->setFlash('save_error');
			}
		}	

		$this->carregaCombos();
		$this->set(compact('lista'));
	
	}

	function editar($codigo = null) {
        $this->autoRender = true;
		$this->pageTitle = "Editar Permissão de Hora Extra";
		$authUsuario = $this->BAuth->user();
        
        if (!empty($this->data)) {
        	$data['AutorizacaoHoraExtra'] = $this->data['PontoEletronico'];
        	$validate = $this->AutorizacaoHoraExtra->validacoes($data,TRUE);
			if($validate === TRUE){
				$data['AutorizacaoHoraExtra']['codigo_gestor'] = $authUsuario['Usuario']['codigo'];
		
				
	            if ($this->AutorizacaoHoraExtra->atualizar($data)) {
	                $this->BSession->setFlash('save_success');
	                $this->redirect(array('action' => 'index'));
	            }else{
	                $this->BSession->setFlash('save_error');
	            }
	        }
	        $this->BSession->setFlash(array(MSGT_ERROR, "$validate"));    
        }else{
            $this->data = $this->AutorizacaoHoraExtra->read(null, $codigo);
            $this->data['PontoEletronico'] = $this->data['AutorizacaoHoraExtra'];
        }

        $lista = $this->Usuario->listaUsuariosDepartamento($authUsuario['Usuario']['codigo_departamento']);
		$this->set(compact('lista'));
    }

	function excluir($codigo){
        if($codigo){
            if($this->AutorizacaoHoraExtra->excluir($codigo)){
                $this->BSession->setFlash('save_success');
            }else{
                $this->BSession->setFlash('delete_error');
            }
            $this->redirect(array('action' => 'index'));
        }
    }
	
	function historico() {
		$this->pageTitle = "Histórico de Pontos";
		$this->autoRender = true; 

		$usuarios = $this->Usuario->listaComCracha();
		$this->data['PontoEletronico'] = $this->Filtros->controla_sessao($this->data, "PontoEletronico");
		if($this->RequestHandler->isPost()){
			$this->data['PontoEletronico']['filtrado'] = TRUE;
		}

		$this->set(compact('usuarios'));
		$this->autoRender = true;    
		
		$usuarios = $this->Usuario->listaComCracha();
		$this->data['PontoEletronico']['filtrado'] = FALSE;
		if($this->RequestHandler->isPost()){
			if(empty($this->data['PontoEletronico']['data_inicial'])){
	        	$this->PontoEletronico->invalidate('data_inicial','Informe a data inicial.');
	        	$this->PontoEletronico->invalidate('data_final','Informe a data final.');
        	}else{	
				$this->data['PontoEletronico']['filtrado'] = TRUE;
			}	
		}
		
		$this->data['PontoEletronico'] = $this->Filtros->controla_sessao($this->data, "PontoEletronico");		
		$this->set(compact('usuarios'));

	}
	function historico_listagem(){
		$this->autoRender = true;
		$authUsuario = $this->BAuth->user();
		$this->data['PontoEletronico'] = $this->Filtros->controla_sessao($this->data, "PontoEletronico");
		$usuarios = $this->Usuario->listaComCracha();       	        	

      	$conditions = $this->PontoEletronico->convertFiltrosEmConditions($this->data['PontoEletronico']);

		$this->paginate['PontoEletronico'] = array(
			'fields' => array(
				'*',
				'TipoPontoEletronico.descricao_ponto_eletronico',
				"CONVERT(varchar,DATEADD(HOUR,3,DATEADD(HOUR,fuso_horario,created)) ,120) as hora_ponto"
         	),
			'conditions' => $conditions,
			'limit' => 50,
			'order' => 'PontoEletronico.data_ponto DESC',
		);
		$dados = $this->paginate('PontoEletronico');
		$this->set(compact('usuarios','dados'));
	}

	
	function historico_exportar($data_inicial, $data_final,$usuario = null) {
	    header(sprintf('Content-Disposition: attachment; filename="%s"', basename("ponto_eletronico.txt")));
	    header('Pragma: no-cache');
	    $filtros['data_inicial'] = base64_decode($data_inicial);
	    $filtros['data_final'] = base64_decode($data_final);
	    $filtros['codigo_usuario'] = $usuario;
	    $dados = $this->PontoEletronico->filtradoComCracha($filtros);
	
	    foreach ($dados as $dado){
	        echo $dado['Usuario']['cracha'].substr(str_replace(array('/', ':', ' '), '', AppModel::dbDateToDate($dado['0']['hora_ponto'])), 0, -2)."\r\n";
	    }
	}

	function digital() {
	    if ($this->RequestHandler->isPost()) {
    		$data = $this->data['Usuario'];
    		if ($this->Usuario->atualizaDigital($data['codigo'], $data['digital']))
    			$this->header('HTTP/1.1 200: OK');
    		else
    			$this->header('HTTP/1.1 400: BAD REQUEST');
	    }
	}
	
	function ponto() {
	    if ($this->RequestHandler->isPost()) {
    	    $data = $this->data['Ponto'];
    	    $retorno = $this->PontoEletronico->cadastrar($data['codigo_usuario'], $_SERVER['REMOTE_ADDR']);
    		if ($retorno === true)
    			$this->header('HTTP/1.1 200: OK');
    		else
    			echo $retorno;
        }
       
	}

	function ponto2() {
	    if ($this->RequestHandler->isPost()) {
    	    $data = $this->data['PontoEletronico']; 	    					
    	    if(!empty($data['codigo_tipo_ponto_eletronico'])){
	    	    $ponto_eletronico_codigo = $this->PontoEletronico->cadastrar($data['codigo_usuario'], $_SERVER['REMOTE_ADDR'],$data['codigo_tipo_ponto_eletronico']);    		
	    		if (is_numeric($ponto_eletronico_codigo)){                  	
           	      	$ponto_eletronico = $this->PontoEletronico->carregar($ponto_eletronico_codigo);
		    	  	$this->StringView = new StringViewComponent();
           		  	$this->AutorizacaoHoraExtra = ClassRegistry::init('AutorizacaoHoraExtra');         		  	
					//.:: verifica se o usuário pode fazer hora extra ::.
					if (!$this->PontoEletronico->validaHorarioPontoEletronico($ponto_eletronico)){
	           		  	if (!$this->AutorizacaoHoraExtra->permissaoHoraExtra($data['codigo_usuario'])) {
                            $config_horario_trabalho = $this->PontoEletronico->obtemHoraConfigurada($ponto_eletronico);
	                		$this->Alerta = ClassRegistry::init('Alerta');
		    	  			$this->StringView->set(compact('ponto_eletronico', 'config_horario_trabalho'));
	                		$content = $this->StringView->renderMail('email_hora_extra_nao_autorizada','default');
	                		$alerta = array(
	                        	'Alerta' => array(
                            		'descricao' => 'Hora Extra nao autorizada',
                                    'descricao_email' => $content,
                                    'codigo_alerta_tipo' => 52,
                                    'model' => 'Usuario',
                                    'foreign_key' => $data['codigo_usuario'],
								),
							);                                     
	           				$this->Alerta->incluir($alerta);            
	           			}
           		    }
	    			$this->BSession->setFlash('save_success');
		    		$this->redirect(array('controller'=>'PontoEletronico', 'action'=>'registrar_ponto'));
	    		}else{
	    			$this->BSession->setFlash(array(MSGT_ERROR, $ponto_eletronico_codigo));
	    			$this->redirect(array('controller'=>'PontoEletronico', 'action'=>'registrar_ponto'));
	    		}
	    	}else{
	    		$this->BSession->setFlash(array(MSGT_ERROR, 'Informe o tipo do registro'));
	    		$this->redirect(array('controller'=>'PontoEletronico', 'action'=>'registrar_ponto'));		    		
    		}    		
        }
       
	}
	
	function digitais() {
		$usuarios = $this->Usuario->usuariosComDigital();
		echo json_encode($usuarios);
		exit;
	}
	
	function registrar_ponto() {
		$this->pageTitle = false;
		$this->autoRender = true;

		$authUsuario = $this->BAuth->user();
		$this->data['PontoEletronico']['codigo_usuario'] = $authUsuario['Usuario']['codigo'];
		$esta_configurado = $this->PontoEletronico->verificaUsuarioConfigurado($this->data['PontoEletronico']['codigo_usuario']);
		
		if(!$esta_configurado){
			$this->BSession->setFlash(array(MSGT_ERROR, 'Você não possui configuração para registro de ponto,Por favor entrar em contato com o RH'));
		}
		$conditions = array();

		$permite_hora_extra = $this->AutorizacaoHoraExtra->permissaoHoraExtra($this->data['PontoEletronico']['codigo_usuario']);
		if(!$permite_hora_extra){
			$conditions[] = array('codigo NOT IN (5,6)');
		}			

		$tipos_ponto_eletronico = $this->TipoPontoEletronico->find('list',array(
			'fields' => 'descricao_ponto_eletronico',
			'conditions' => $conditions,
			)
		);
		if($this->AutorizacaoHoraExtra->isMobile()){
			$mobile = TRUE;
		}else{
			$mobile = FALSE;
		}

		$registros_de_ponto = $this->PontoEletronico->find(
			'all',array(
				'conditions' => array('codigo_usuario' => $this->data['PontoEletronico']['codigo_usuario']),
				'limit' => 15,
				'order' => 'PontoEletronico.codigo DESC'
			)
		);
		$usuarios = $this->Usuario->listaComCracha();
		$this->set(compact('esta_configurado','tipos_ponto_eletronico','registros_de_ponto','usuarios','mobile'));

	}
}