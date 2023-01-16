<?php
class MensageriaEsocialController extends AppController 
{
	public $name = 'MensageriaEsocial';
	public $helpers = array('BForm', 'Html', 'Ajax');

	/**
	 * [$uses description]
	 * 
	 * atributo para instanciar as classes models
	 * 
	 * @var array
	 */
	var $uses = array(
		'Cliente',
		'Configuracao',
		'ClienteProduto',
		'ClienteProdutoServico2',
		'IntEsocialCertificado',
		'IntEsocialCertUnidade',
		'IntEsocialStatus',
		'IntEsocialTipoEvento',
		'IntEsocialCampoObrigatorio',
		'IntEsocialEventos',
		'TermoUso',
		'GrupoEconomicoCliente',
		'MensageriaEsocial',
		'Esocial',
		'OcorrenciaIntEsocialEvento'
	);

	var $codigo_termo_uso = 7;

	/**
	 * [beforeFilter description]
	 * 
	 * liberando os metodo para acessar precisar estar logado
	 * 
	 * @return [type] [description]
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array('*'));
	}//FINAL FUNCTION beforeFilter

	/**
	 * [get_servico_assinatura verifica se tem assinatura configurada para mensageria]
	 * @return [type] [description]
	 */
	public function get_servico_assinatura($codigo_cliente)
	{
		$this->layout = false;

		$return = false;
		$assinatura = $this->MensageriaEsocial->get_servico_assinatura($codigo_cliente);
		if(!empty($assinatura)) {
			$return = true;
		}

		return ;

	}//fim get_servico_assinatura

	/**
	 * [index_certificado metodo para filtrar os certificados importados]
	 * @return [type] [description]
	 */
	public function index_certificado() 
	{

		$this->pageTitle = 'Certificado Digital';
		$filtros = $this->Filtros->controla_sessao($this->data, 'MensageriaEsocial');
		
		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}
		
		$this->data['MensageriaEsocial'] = $filtros;

		// $this->set(compact('incio','fim'));
		// $this->carrega_combos_grupo_economico();

	}//fim index_certificado

	/**
	 * [carrega_combos_grupo_economico description]
	 * 
	 * metodo para carregar os combos 
	 * 
	 * @param  [type] $model [description]
	 * @return [type]        [description]
	 */
	public function carrega_combos_grupo_economico() 
	{

		$codigo_cliente = "";
		$cargos 		= "";
		$unidades 		= "";
		$setores 		= "";

		if(isset($this->data['MensageriaEsocial']['codigo_cliente'])) {

			$this->loadModel('Cargo');
			$this->loadModel('Setor');
			$this->loadModel('GrupoEconomico');

			$codigo_cliente = $this->data['MensageriaEsocial']['codigo_cliente'];

	    	if(!empty($codigo_cliente)){
				$codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
	    	}

			$unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
			$setores = $this->Setor->lista($codigo_cliente);
			$cargos = $this->Cargo->lista($codigo_cliente);

		}

		$this->set(compact('unidades', 'setores', 'cargos'));

	}//fim carrega_combos_grupo_economico


	/**
	 * [listagem_certificado metodo para listar os certificados do cliente importados no sistema]
	 * @return [type] [description]
	 */
	public function listagem_certificado() 
	{

		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, 'MensageriaEsocial');
		// debug($filtros);exit;

		$authUsuario = $this->BAuth->user();
		if(!empty($this->authUsuario['Usuario']['codigo_cliente']) && is_array($this->authUsuario['Usuario']['codigo_cliente']) && count($this->authUsuario['Usuario']['codigo_cliente'] > 0)) {
			$filtros['codigo_cliente'] = implode(',',$this->authUsuario['Usuario']['codigo_cliente']);
		} 
		else if(!empty($this->authUsuario['Usuario']['codigo_cliente'])){
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}


		$codigo_cliente = $filtros['codigo_cliente'];
		$listagem = array();
		if (!empty($filtros['codigo_cliente'])) {
			
			//sempre tem que pegar os pedidos que foram todo baixado
			$conditions[] = "IntEsocialCertificado.codigo_cliente IN (" .$filtros['codigo_cliente'] .")";
			$order = array('IntEsocialCertificado.codigo');

			//get da query
			$this->paginate['IntEsocialCertificado'] = array(
				'conditions' => $conditions,
				'limit' => 50,
				'order' => $order,
			);

			// pr($this->IntEsocialCertificado->find('sql', $this->paginate['IntEsocialCertificado']));

			//carrega nela o paginate para popular na view
			$listagem = $this->paginate('IntEsocialCertificado');
		} //fim if codigo_cliente

		$ambiente_esocial = array(
			'2' => "Pré-Produção",
			'1' => "Produção",
		);

		//seta para a view
		$this->set(compact('listagem','codigo_cliente','ambiente_esocial'));

	}//fim listagem_certificado

	public function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['IntEsocialCertificado']['codigo'] = $codigo;
        $this->data['IntEsocialCertificado']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->IntEsocialCertificado->atualizar($this->data, false)) {   
            print 1;
        } else {
            print 0;
        }
        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }

	public function importacao_certificado($codigo_cliente,$codigo_int_esocial_certificado = null)
	{

		$this->pageTitle = 'Configuração Certificado Digital';

		//verifica se tem o codigo do cliente
		if(empty($codigo_cliente)) {
			$this->BSession->setFlash(array(MSGT_ERROR, 'Necessário ter o coóigo de um cliente. Tente Novamente.'));
			$this->redirect(array('controller' => 'mensageria_esocial', 'action' => 'index_certificado'));
		}
		
		$dir_certificados = APP."tmp".DS."certificados".DS.$codigo_cliente.DS;

		if($this->RequestHandler->isPost()) {
			// debug($_FILES);
			// debug($this->data);
			// exit;

			//verifica se é alteracao 
			if(!empty($codigo_int_esocial_certificado)) {

				//necessário gravar os dados do arquivo
				$dados_certificado = array('IntEsocialCertificado' => array(
					'codigo_cliente' => $codigo_cliente,
					'senha_certificado' => $this->data['MensageriaEsocial']['senha_certificado'],
					'email_responsavel' => $this->data['MensageriaEsocial']['email_responsavel'],
					'razao_social' => $this->data['MensageriaEsocial']['razao_social'],
					'aceite_termo_responsabilidade' => $this->data['MensageriaEsocial']['aceite_termo_responsabilidade'],
					'codigo_usuario_aceite_termo' => $this->authUsuario['Usuario']['codigo'],
					'ativo' => '1',
					'ambiente_esocial' => $this->data['MensageriaEsocial']['ambiente_esocial'],
					'ip_usuario_aceite_termo' => $this->data['MensageriaEsocial']['ip_usuario_aceite_termo'],
					'fuso_horario' => $this->data['MensageriaEsocial']['fuso_horario'],
				));

				if(!empty($_FILES['data']['name']['MensageriaEsocial']['certificado'])){
					$nome_arquivo = $_FILES['data']['name']['MensageriaEsocial']['certificado'];

					//verifica a extensao do arquivo
					if (strpos($nome_arquivo, ".pfx") > 0 ){

						//Se nenhum arquivo foi anexado
						if(empty($_FILES['data']['name']['MensageriaEsocial']['certificado'])){
							$this->BSession->setFlash(array(MSGT_ERROR, 'Não foi encontrado o nome do arquivo. Tente Novamente.'));
							$this->redirect(array('controller' => 'mensageria_esocial', 'action' => 'importacao_certificado',$codigo_cliente));
						}

						 //Cria o diretório do atestado se não existe
				        if(!is_dir($dir_certificados)){
				        	//cria o diretorio no servidor
				        	mkdir($dir_certificados);
				        }  

				        preg_match("/(\..*){1}$/i", $nome_arquivo, $ext);
				        $arquivo_anexo = 'certificado_'.$codigo_int_esocial_certificado.'_'.date('dmYHi').$ext[0];
				        $destino = $dir_certificados.$arquivo_anexo;

				        $caminho_completo = end(glob($dir_certificados.'certificado_'.$codigo_int_esocial_certificado.'*'));

				        //Apaga os arquivos existentes
				        if (is_file($caminho_completo)){
				           unlink($caminho_completo);
				        } 
				          
				        if(move_uploaded_file($_FILES['data']['tmp_name']['MensageriaEsocial']['certificado'],$destino)){
				        	$dados_certificado['IntEsocialCertificado']['nome_arquivo'] = $nome_arquivo;
							$dados_certificado['IntEsocialCertificado']['caminho_arquivo'] = $destino;
				        }//if arquivo movido corretamente
					}
				}

				$dados_certificado['IntEsocialCertificado']['codigo'] = $codigo_int_esocial_certificado;

				if(!$this->IntEsocialCertificado->atualizar($dados_certificado)) {
					$this->BSession->setFlash(array(MSGT_ERROR, 'Não foi possivel alterar os dados. Tente Novamente.'));
					$this->redirect(array('controller' => 'mensageria_esocial', 'action' => 'importacao_certificado',$codigo_cliente,$codigo_int_esocial_certificado));
				}

				$this->BSession->setFlash('save_success');
				$this->redirect(array('controller' => 'mensageria_esocial', 'action' => 'index_certificado'));

			}
			else {

				if(!empty($_FILES['data']['name']['MensageriaEsocial']['certificado'])){
					$nome_arquivo = $_FILES['data']['name']['MensageriaEsocial']['certificado'];

					//verifica a extensao do arquivo
					if (strpos($nome_arquivo, ".pfx") > 0 ){

						//Se nenhum arquivo foi anexado
						if(empty($_FILES['data']['name']['MensageriaEsocial']['certificado'])){
							$this->BSession->setFlash(array(MSGT_ERROR, 'Não foi encontrado o nome do arquivo. Tente Novamente.'));
							$this->redirect(array('controller' => 'mensageria_esocial', 'action' => 'importacao_certificado',$codigo_cliente));
						}

						//necessário gravar os dados do arquivo
						$dados_certificado = array('IntEsocialCertificado' => array(
							'codigo_cliente' => $codigo_cliente,
							'nome_arquivo' => $nome_arquivo,
							'senha_certificado' => $this->data['MensageriaEsocial']['senha_certificado'],
							'email_responsavel' => $this->data['MensageriaEsocial']['email_responsavel'],
							'razao_social' => $this->data['MensageriaEsocial']['razao_social'],
							'aceite_termo_responsabilidade' => $this->data['MensageriaEsocial']['aceite_termo_responsabilidade'],
							'codigo_termo_uso' => $this->codigo_termo_uso,
							'codigo_usuario_aceite_termo' => $this->authUsuario['Usuario']['codigo'],
							'caminho_arquivo' => $dir_certificados,
							'ativo' => '1',
							'ambiente_esocial' => $this->data['MensageriaEsocial']['ambiente_esocial'],
							'ip_usuario_aceite_termo' => $this->data['MensageriaEsocial']['ip_usuario_aceite_termo'],
							'fuso_horario' => $this->data['MensageriaEsocial']['fuso_horario'],
						));

						if(!empty($codigo_int_esocial_certificado)) {
							$dados_certificado['IntEsocialCertificado']['codigo'] = $codigo_int_esocial_certificado;

							if(!$this->IntEsocialCertificado->atualizar($dados_certificado)) {
								$this->BSession->setFlash(array(MSGT_ERROR, 'Não foi possivel alterar os dados. Tente Novamente.'));
								$this->redirect(array('controller' => 'mensageria_esocial', 'action' => 'importacao_certificado',$codigo_cliente,$codigo_int_esocial_certificado));
							}
						}
						else {

							// debug($dados_certificado);

							if(!$this->IntEsocialCertificado->incluir($dados_certificado)) {
								
								$this->log(print_r($this->IntEsocialCertificado->validationErrors,1),'debug');
								// debug($this->IntEsocialCertificado->validationErrors);exit;

								$this->BSession->setFlash(array(MSGT_ERROR, 'Não foi possivel incluir os dados. Tente Novamente.'));
								$this->redirect(array('controller' => 'mensageria_esocial', 'action' => 'importacao_certificado',$codigo_cliente));
							}

							$codigo_int_esocial_certificado = $this->IntEsocialCertificado->id;
						}
					    
				        //Cria o diretório do atestado se não existe
				        if(!is_dir($dir_certificados)){
				        	//cria o diretorio no servidor
				        	mkdir($dir_certificados);
				        }  

				        preg_match("/(\..*){1}$/i", $nome_arquivo, $ext);
				        $arquivo_anexo = 'certificado_'.$codigo_int_esocial_certificado.'_'.date('dmYHi').$ext[0];
				        $destino = $dir_certificados.$arquivo_anexo;

				        $caminho_completo = end(glob($dir_certificados.'certificado_'.$codigo_int_esocial_certificado.'*'));

				        //Apaga os arquivos existentes
				        if (is_file($caminho_completo)){
				           unlink($caminho_completo);
				        } 
				          
				        if(move_uploaded_file($_FILES['data']['tmp_name']['MensageriaEsocial']['certificado'],$destino)){
							//Se já existe registro, atualiza
							if(!empty($codigo_int_esocial_certificado)){
								$dados_anexo['IntEsocialCertificado']['codigo'] = $codigo_int_esocial_certificado;
								$dados_anexo['IntEsocialCertificado']['caminho_arquivo'] = $destino;

								if(!$this->IntEsocialCertificado->atualizar($dados_anexo)) {
									$this->BSession->setFlash(array(MSGT_ERROR, 'Não foi possivel guardar o certificado corretamente. Tente Novamente.'));
									$this->redirect(array('controller' => 'mensageria_esocial', 'action' => 'importacao_certificado',$codigo_cliente,$codigo_int_esocial_certificado));
								}

								$this->BSession->setFlash('save_success');
								$this->redirect(array('controller' => 'mensageria_esocial', 'action' => 'index_certificado'));
								
							}
				        }//if arquivo movido corretamente
					    

					} 
					else{
						//Se o arquivo não possui a extensão correta
						$this->BSession->setFlash(array(MSGT_ERROR, 'Somente as seguintes extensões são permitidas: PFX. Tente Novamente.'));
						$this->redirect(array('controller' => 'mensageria_esocial', 'action' => 'importacao_certificado',$codigo_cliente,$codigo_int_esocial_certificado));
					}//if valida extensão

				}//if valida arquivo anexo
				else {
					$this->BSession->setFlash(array(MSGT_ERROR, 'Não foi encontrado o arquivo. Tente Novamente.'));
					$this->redirect(array('controller' => 'mensageria_esocial', 'action' => 'importacao_certificado',$codigo_cliente,$codigo_int_esocial_certificado));
				}
			}//fim alteracao

		} 

		//ambiente para o esocial
		$ambiente_esocial = array(
			'2' => "Pré-Produção",
			'1' => "Produção",
		);

		//para edicao buscar os dados
		$dados_certificado = array();
		$email_responsavel = '';
		$razao_social = '';
		// $usuario_certificado='';
		$senha_certificado = '';

		if(!empty($codigo_int_esocial_certificado)) {
			//busca os dados do certificado
			$dados_certificado = $this->IntEsocialCertificado->find('first',array('conditions' => array('codigo' => $codigo_int_esocial_certificado)));
			
			// $usuario_certificado = $dados_certificado['IntEsocialCertificado']['usuario_certificado'];
			$senha_certificado = $dados_certificado['IntEsocialCertificado']['senha_certificado'];
			$email_responsavel = $dados_certificado['IntEsocialCertificado']['email_responsavel'];
			$razao_social = $dados_certificado['IntEsocialCertificado']['razao_social'];
			
		}
		else {
			//pega a razao social do cliente caso nao tenha ja cadastrado
			$dados_cliente = $this->Cliente->find('first',array('conditions' => array('Cliente.codigo' => $codigo_cliente)));
			$razao_social = $dados_cliente['Cliente']['razao_social'];
		}

		$this->set(compact('codigo_int_esocial_certificado','codigo_cliente','dir_certificados','dados_certificado','codigo_termo_uso','email_responsavel','razao_social','usuario_certificado','senha_certificado','ambiente_esocial'));

	}//fim importacao_certificado

	public function status_certificado() {}

	/**
	 * [rel_cliente_certificado pega os dados de unidades e multiclientes]
	 * @return [type] [description]
	 */
	public function rel_cliente_certificado($codigo_cliente, $codigo_certificado) 
	{

        
		//pega os multiclientes do usuario
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {

        	$codigos_clientes = $this->authUsuario['Usuario']['codigo_cliente'];

        	foreach($codigos_clientes AS $codigo_cliente_multi) {

        		// $dados_unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente_multi,true);
				$grupo_economico = $this->GrupoEconomicoCliente->find('first',array( 'fields' => array('GrupoEconomicoCliente.codigo_grupo_economico'),'conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente_multi),'recursive' => -1));
				//pega as unidades do cliente passado
        		$dados_unidades = $this->GrupoEconomicoCliente->retorna_unidades_grupo_economico($grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico']);

        		$todas_unidades[$codigo_cliente_multi] = $dados_unidades;
        	}

        }//fim authUsuario
        else {
        	
			$grupo_economico = $this->GrupoEconomicoCliente->find('first',array( 'fields' => array('GrupoEconomicoCliente.codigo_grupo_economico'),'conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente),'recursive' => -1));

			//unidades
	        $unidades = $this->GrupoEconomicoCliente->retorna_unidades_grupo_economico($grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico']);
	        $todas_unidades[$codigo_cliente] = $unidades;
        }

        //verifica se tem dados para o certificado importado
        $dados_unidades_certificados = $this->IntEsocialCertUnidade->find('all', array('conditions' => array('codigo_int_esocial_certificado' => $codigo_certificado,'ativo' => 1)));
        //veifica se tem dados de unidades para o certificado
        if(!empty($dados_unidades_certificados)) {

        	//varre as unidades ja selecionadas
        	foreach($dados_unidades_certificados AS $duc) {

        		$this->data['codigo_unidade_certificado_'.$duc['IntEsocialCertUnidade']['codigo_cliente']] = $duc['IntEsocialCertUnidade']['codigo_cliente'];

        	}//fim foreach

        }//fim verificacao unidades

		$this->set(compact('codigo_cliente','codigo_certificado','todas_unidades'));

	}// rel_cliente_certificado

	public function salvar_rel_cliente_certificado()
	{
		$this->layout = 'ajax';

        // debug($this->params['form']);exit;
        
        //parametros passados
        $codigo_cliente = $this->params['form']['codigo_cliente'];
        $codigo_certificado = $this->params['form']['codigo_certificado'];

        $arr_obj_unidades = (isset($this->params['form']['arr_obj_unidades'])) ? $this->params['form']['arr_obj_unidades'] : null;

        $return = 0;
        if(!empty($codigo_certificado) && !empty($codigo_cliente)) {
            //verifica se tem arr_obj_unidades
            if(!empty($arr_obj_unidades)) {

            	//deleta todos os relacionamentos de clientes unidades com esse certificado
            	$this->IntEsocialCertUnidade->deleteTodosRelUnidades($codigo_certificado);
                //monta array para trabalhar com os ids checkados
                $arr_unidades = array();
                // varre os codigos de unidades
                foreach($arr_obj_unidades AS $val_unidades) {

		            $dados_int_esocial_certificado_unidade = array(
		            	'IntEsocialCertUnidade' => array(
		                    'codigo_cliente' => $val_unidades['id'],
		                    'codigo_int_esocial_certificado' => $codigo_certificado,
		                    'ativo' => 1,
		                )
		            );
                    
                	if(!$this->IntEsocialCertUnidade->incluir($dados_int_esocial_certificado_unidade)) {
                		// debug($this->IntEsocialCertUnidade->validationErrors);
                	}

                }//fim foreach objetos unidades

                $return = 1;


                $integracao_certificado = $this->integracao_certificado($codigo_cliente,$codigo_certificado);


            }//fim if unidades

        }//fim validacao codigo e codigo_cliente

        echo $return;
        exit;

	}// fim salvar_rel_cliente_certificado


	/**
	 * [get_termo_certificado metodo para pegar o termo do certificado]
	 * @return [type] [description]
	 */
	public function get_termo_certificado() 
	{

		//pega um determinado termo de uso para o certificados digitais
		$termo = $this->TermoUso->find('first',array('conditions' => array('codigo' => $this->codigo_termo_uso)));
		$this->set(compact('termo'));

	}//dim get_termo_Certificado

	/**
	 * [integracao_certificado metodo para integrar o certificado digital as empresas que irá usar com a tecnospeed]
	 * @return [type] [description]
	 */
	public function integracao_certificado($codigo_cliente,$codigo_certificado) 
	{

		// $this->layout = 'ajax';

		$return = 0;

		//pega os cnpjs do certificado		
		$dados_unidades_certificado = $this->IntEsocialCertUnidade->getCertificadosUnidades($codigo_certificado);

		//verifica se tem algum resultado
		if(!empty($dados_unidades_certificado)) {

			$cnpjs = array();

			//varre os cnpjs das unidades
			foreach($dados_unidades_certificado AS $unidades) {

				//pega os cnpjs
				$codigo_documento = (trim($unidades['Cliente']['codigo_documento_real']) <> '') ? $unidades['Cliente']['codigo_documento_real'] : $unidades['Cliente']['codigo_documento'];

				//verifica se tem dados no clientereal
				$cnpjs[$codigo_documento] = $codigo_documento;			

			}//fim foreach

			//busca os dados do certificado
			$dados_certificado = $this->IntEsocialCertificado->find('first',array('conditions' => array('codigo' => $codigo_certificado)));

			$params['certificado'] = $dados_certificado['IntEsocialCertificado']['caminho_arquivo'];
			$params['senha'] = $dados_certificado['IntEsocialCertificado']['senha_certificado'];
			$params['cpfCnpjEmpregador'] = implode(',',$cnpjs);
			$params['email'] = $dados_certificado['IntEsocialCertificado']['email_responsavel'];
			$params['razaoSocial'] = $dados_certificado['IntEsocialCertificado']['razao_social'];

			//importa o certificado na tecnospeed
			if($codigo_retorno = $this->MensageriaEsocial->tecnospeed_envia_certificado($codigo_cliente,$this->authUsuario['Usuario']['codigo'],$codigo_certificado,$params)) {
				//atualiza no certificado a data de integracao com a tecnospeed
				$dados_certificado['IntEsocialCertificado']['data_integracao'] = date('Y-m-d H:i:s');
				$dados_certificado['IntEsocialCertificado']['codigo_retorno_tecnospeed'] =$codigo_retorno;

				$this->IntEsocialCertificado->atualizar($dados_certificado);

				$return = 1;
			}
		}

		return $return;

		// $this->render(false,false);

		// echo $return;
		// exit;

	}//fim integracao_certificado

	public function teste_get_certificado()
	{
		$certificados = $this->MensageriaEsocial->tecnospeed_get_certificados($this->authUsuario['Usuario']['codigo']);
		debug($certificados);
		exit;
	}


	public function index_eventos() 
	{

		$this->pageTitle = 'Esocial Eventos';
		$filtros = $this->Filtros->controla_sessao($this->data, 'MensageriaEsocial');
		
		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		//verifica para seta a data do começo do mes padrao
		if(empty($this->data['MensageriaEsocial']['data_inicio'])) {
			//seta as datas
			$filtros['data_inicio'] = '01'.date('m/Y');
			$filtros['data_fim'] = date('d/m/Y');
		}
		
		$this->data['MensageriaEsocial'] = $filtros;

		$this->carrega_combos_grupo_economico();

		$tipos_eventos = $this->IntEsocialTipoEvento->find('list',array('fields' => array('codigo','descricao'),'conditions' => array('ativo' => 1)));

		$this->set(compact('tipos_eventos'));

	}

	public function listagem_eventos($export = false) 
	{
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, 'MensageriaEsocial');
		$authUsuario = $this->BAuth->user();
		
		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}
		
		$mensageria = false;
		$listagem = array();
		
		if (!empty($filtros['codigo_cliente'])) {

			//verifica se tem permissao para mensageria
			$mensageria = $this->MensageriaEsocial->get_servico_assinatura($filtros['codigo_cliente']);
			
			// debug($filtros);
			$conditions = $this->IntEsocialEventos->convertFiltroEmCondition($filtros);

			if($export){
				$query = $this->IntEsocialEventos->getAll($conditions, false, 'sql', true);
				$this->export_integracao_eventos($query);
			} else {
				$this->paginate['IntEsocialEventos'] = $this->IntEsocialEventos->getAll($conditions, true);
				// pr($this->IntEsocialEventos->find('sql', $this->paginate['IntEsocialEventos']));
				$listagem = $this->paginate('IntEsocialEventos');
			}
		}

		$this->set(compact('listagem', 'mensageria'));
	}

	public function integracao_eventos($codigo_evento) 
	{
		$this->layout = false;

		$processado = false;
		if(!empty($codigo_evento)) {
			//pega os dados do evento
			$dados = $this->IntEsocialEventos->find('first', array('conditions' => array('codigo' => $codigo_evento)));
			$processado = $this->MensageriaEsocial->tecnospeed_integrar_txt2($dados);
		}//fim verificacao do codigo evento

		print $processado;
		exit;

		if(!$processado) {
			$this->BSession->setFlash(array(MSGT_ERROR, 'Necessário ter o coóigo de um cliente. Tente Novamente.'));
		}
		else {
			$this->BSession->setFlash('save_success');
		}

		$this->redirect(array('controller' => 'mensageria_esocial', 'action' => 'index_eventos'));

	}//fim processado

	public function integracao_eventos_all() 
	{
		$this->layout = false;

		debug($this->data);

		echo "integracao_eventos_all";
		exit;
	}

	public function modal_ocorrencia_esocial($codigo_esocial_evento){

		$dados = array();

		$dados_ocorrencia = $this->OcorrenciaIntEsocialEvento->find('all', array('conditions' => array('codigo_int_esocial_evento' => $codigo_esocial_evento)));

		if(!empty($dados_ocorrencia)){
			$dados = $dados_ocorrencia;
		}

		$this->set(compact('codigo_esocial_evento','dados'));
	}

	public function getXmlEvento($codigo_evento)
	{

		$this->layout = false;

		if(!empty($codigo_evento)) {
			$conditions = array('IntEsocialEventos.codigo' => $codigo_evento);
			$evento = $this->IntEsocialEventos->getAll($conditions, false, 'first');
			// debug($evento);exit;
			if(!empty($evento['IntEsocialEventos']['dados_evento'])) {

				$opcoes['FILE_NAME'] = "Evento_".$evento['IntEsocialTipoEvento']['descricao']."_".date('YmdHis').".xml";
				
				header(sprintf('Content-Disposition: attachment; filename="%s"', $opcoes['FILE_NAME']));
				header('Pragma: no-cache');
				header("Content-type: text/xml");

				echo $evento['IntEsocialEventos']['dados_evento'];
				

			}//fim validadacao dados evento

		}//fim validadacao codigo_evento

		exit;
	}

	private function export_integracao_eventos($query){

		$dados = $this->IntEsocialEventos->query($query);

		//headers
        ob_clean();
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="esocial_eventos_export_'.date('YmdHis').'.csv"');
        header('Pragma: no-cache');

		//cabecalho do arquivo
		echo utf8_decode('"Nome Fantasia";"CPNJ";"Funcionário";"CPF";"Matricula";"Tipo Evento";"Codigo Registro";"Status";"Data Integração";"Recibo";"Retorno Esocial";"Data Retorno Esocial";"Ocorrência - Retorno";')."\n";
		$linha = '';

		if(!empty($dados)){

			foreach($dados AS $value) {

				$linha .= $value['Cliente']['nome_fantasia'].';';
				$linha .= Comum::formatarDocumento($value['Cliente']['codigo_documento']).';';
				$linha .= $value['Funcionario']['nome'].';';
				$linha .= Comum::formatarDocumento($value['Funcionario']['cpf']).';';
				$linha .= $value['ClienteFuncionario']['matricula'].';';
				$linha .= $value['IntEsocialTipoEvento']['descricao'].';';
				$linha .= $value['IntEsocialEventos']['codigo_registro_sistema'].';';
				$linha .= $value['IntEsocialStatus']['descricao'].';';
				$linha .= $value['IntEsocialEventos']['data_integracao'].';';
				$linha .= $value['IntEsocialEventos']['codigo_recibo'].';';
				$linha .= $value['IntEsocialEventos']['mensagem_retorno_integradora'].';';
				$linha .= $value['IntEsocialEventos']['data_retorno_integradora'].';';
				$linha .= str_replace("\n", " ", str_replace(";", " ", $value['OcorrenciaIntEsocialEvento']['descricao_ocorrencia'])).';';

				$linha .= "\n";


			}
		}

		echo Comum::converterEncodingPara($linha, 'ISO-8859-1');
		//mata o metodo
        die();
	}

	 /**
     * [setIntegS3000 metodo excluir o evento do esocial]
     */
    public function setIntegS3000()
    {
    	$this->layout = 'ajax';

    	//verifica se tem os dados necessários para integracao
    	$codigo_evento = $this->params['form']['codigo_evento'];
    	
    	if(empty($codigo_evento)) {
    		$retorno = array('retorno' => 'false', 'mensagem' => "Dados enviados inválidos");

    		echo json_encode($retorno);
    		exit;
    	}

    	//variaveis de retorno
    	$retorno = 'true';
    	$mensagem = '';


    	//pega o codigo do tipo de evendo
    	$int_esocial_tipo_evento = $this->IntEsocialTipoEvento->find('first',array('fields' => array('codigo'),'conditions' => array('apelido_descricao' => 's3000') ));
    	//verifica se tem o dado cadastrado
    	if(!empty($int_esocial_tipo_evento)) {
    		$codigo_tipo_evento = $int_esocial_tipo_evento['IntEsocialTipoEvento']['codigo'];

	        //verifica se é atualizacao ou inclusao
	        $evento = $this->IntEsocialEventos->find('first',array('conditions' => array('codigo' => $codigo_evento)));
	        $codigo_registro_sistema = $evento['IntEsocialEventos']['codigo_registro_sistema'];
	        $codigo_cliente = $evento['IntEsocialEventos']['codigo_cliente'];

	        // debug($codigo_registro_sistema);
	        // debug($evento);exit;
	        
	        //verifica se ja existe o registro no banco
	        if(!empty($evento)) {
	    		
    			//pelo codigo do cliente pegar o codigo do certificado ativo
    			$int_esocial_certificao = $this->IntEsocialCertificado->getCertificadosCliente($codigo_cliente);

    			// debug($int_esocial_certificao);exit;
    			if(empty($int_esocial_certificao)) {
    				$retorno = 'false';
    				$mensagem .= "O codigo: ". $codigo_registro_sistema." não tem certificado configurado corretamente. Favor verificar com o administrador!\n";
    				continue;
    			}

    			//seta o codigo do certificado
    			$codigo_int_esocial_certificado = $int_esocial_certificao['IntEsocialCertificado']['codigo'];
    			$codigo_int_esocial_status = 1; //pendente de envio para o esocial
    			$ambiente = $int_esocial_certificao['IntEsocialCertificado']['ambiente_esocial'];//pega o ambiente configurado

    			//pega os dados do evento
    			$method = "gerar_s3000";
    			$dados_evento = $this->Esocial->{$method}($codigo_evento);
		        
		        $dados_evento = str_replace("<?xml version='1.0' encoding='UTF-8'?>", "", $dados_evento);
		        // debug($dados_evento);exit;
		        

		        //monta o array para inserir os eventos
		        $dados_int_esocial_eventos = array(
		        	'IntEsocialEventos' => array(
		        		'codigo_cliente' => $evento['IntEsocialEventos']['codigo_cliente'],
		        		'codigo_int_esocial_certificado' => $codigo_int_esocial_certificado,
		        		'codigo_int_esocial_tipo_evento' => $codigo_tipo_evento,
		        		'codigo_int_esocial_status' => $codigo_int_esocial_status,
		        		'codigo_registro_sistema' => $evento['IntEsocialEventos']['codigo_registro_sistema'],
		        		'dados_evento' => $dados_evento,
		        		'ativo' => 1,
		        		'codigo_cliente_funcionario' => $evento['IntEsocialEventos']['codigo_cliente_funcionario'],
		        		'codigo_funcionario_setor_cargo' => $evento['IntEsocialEventos']['codigo_funcionario_setor_cargo'],
		        		'codigo_funcionario' => $evento['IntEsocialEventos']['codigo_funcionario'],
		        		'codigo_setor' => $evento['IntEsocialEventos']['codigo_setor'],
		        		'codigo_cargo' => $evento['IntEsocialEventos']['codigo_cargo'],
		        		'codigo_cliente_matriz' => $evento['IntEsocialEventos']['codigo_cliente_matriz'],
		        		'codigo_cliente_alocacao' => $evento['IntEsocialEventos']['codigo_cliente_alocacao'],			        		
		        		'matricula' => $evento['IntEsocialEventos']['matricula'],
		        		'codigo_int_esocial_eventos_s3000' => $codigo_evento,
		        	)
		        );
	        	
	        	// $this->log(print_r($dados_int_esocial_eventos,1),"debug");
	        	// continue;
		        
		        if(!$this->IntEsocialEventos->incluir($dados_int_esocial_eventos)) {
					
					// debug("erro ".$codigo_registro_sistema);

		        	//seta o erro da insercao
		      //   	$retorno = 'false';
    				// $mensagem .= "Ocorreu um erro ao gravar o codigo: ". $codigo_registro_sistema.". Favor verificar com o administrador!\n";

    				$retorno[$codigo_registro_sistema] = 'false';
					// $retorno = 'false';
					$mensagem[$codigo_registro_sistema] = "Ocorreu um erro ao gravar o codigo: ". $codigo_registro_sistema.". Favor verificar com o administrador!";

    				continue;
				}
				$codigo_int_esocial = $this->IntEsocialEventos->id;
	        	
				//envia para a tecnospeed
				$mensageria = $this->MensageriaEsocial->tecnospeed_evento_enviar_xml($codigo_int_esocial);

				// debug($mensageria);exit;
				if(empty($mensageria)) {
					$retorno[$codigo_registro_sistema] = 'false';
					// $retorno = 'false';
					$mensagem[$codigo_registro_sistema] = "Ocorreu um erro na integração com o E-Social!";
				}
				else {
					$retorno[$codigo_registro_sistema] = 'true';
					// $retorno = 'true';
					$mensagem[$codigo_registro_sistema] = $mensageria;
				}
				
	        }//fim empty evento

    		
    	}//fim verificacao do tipo de evento

    	// debug($retorno);
    	// debug($mensagem);

    	// debug($this->params['form']);exit;

		$return = array('retorno' => $retorno, 'mensagem' => $mensagem);
		echo json_encode($return);
		exit;


    }//fim setIntegS3000

}// FINAL CLASS MensageriaEsocialController