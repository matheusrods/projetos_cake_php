<?php

class PreFaturamentoController extends AppController {
	public $name = 'PreFaturamento';
	public $components = array('Filtros', 'RequestHandler', 'Upload');
	public $helpers = array('Html', 'Ajax', 'Buonny');
	public $uses = array(
		'Cliente', 
		'Alerta',
		'GrupoEconomico',
		'GrupoEconomicoCliente',
		'PreFaturamento',
		'ClienteValidador',
		'PreFaturamentoAnexos',
		'Usuario'
	) ;
	//var $services = array('EstatisticaServicos', 'NumeroConsultas');

	/**
     * beforeFilter callback
     *
     * @return void
     */
	function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array());
	}//FINAL FUNCTION beforeFilter   

	public function alterar(){
		
		//seta que não vai ter layout ctp
		//$this->layout = false;
		$this->layout = 'ajax';

		$authUsuario = $this->BAuth->user();
		$codigo = $this->params['form']['codigo'];

		//$find = $this->PreFaturamento->find('first', array('conditions' => array('codigo'=>$codigo)));
		//debug($find);
		$dados['PreFaturamento']['codigo_usuario_alteracao'] = $authUsuario['Usuario']['codigo'];
		$dados['PreFaturamento']['data_alteracao'] = date("Y-m-d H:i:s");
		$dados['PreFaturamento']['status'] = $this->params['form']['status'];

		if($this->PreFaturamento->atualizar($dados)){
			return true;
		}else{
			return false;
		}

	}

	public function gestao(){
		//seta o titulo da pagina
		$this->pageTitle = 'Gestão Pré Faturamento';		

		//pega os filtro do controla sessao
		$filtros = $this->Filtros->controla_sessao($this->data, $this->PreFaturamento->name);
		
		if(isset($this->params['url']['key'])){
			$filtros['codigo_cliente'] = $this->params['url']['key'];
		}

		//debug($filtros);

		//carrega combos
		$this->gestao_filtros($filtros);
		
	}

	public function gestao_filtros($thisData=null){			

		$status = array('Selecione...', 'Não Aprovado', 'Em Análise');

		$this->data['PreFaturamento'] = $thisData;

		$meses = Comum::listMeses();
		$meses[0] = "Selecione...";
		ksort($meses);

		$this->set(compact('status', 'meses'));

	}

	public function gestao_listagem(){

		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, $this->PreFaturamento->name);
		
		if ($filtros['status'] == 1){
			$filtros['status'] = "Não Aprovado";
		}else if ($filtros['status'] == 2){
			$filtros['status'] = "Em Análise";
		}
		
		if($filtros){
			$this->data['PreFaturamento'] = $filtros;

			$listagem = array();
			foreach($this->PreFaturamento->listar($filtros) as $key => $v){
				$v = $v['PreFaturamento'];

				$nome_cliente = $this->Cliente->buscaPorCodigo($v['codigo_cliente']);
				$v['nome_cliente'] = $nome_cliente['Cliente']['razao_social'];

				$nome_usuario = $this->Usuario->find('first', array('fields'=>array('apelido'), 'conditions' => array('codigo'=>$v['codigo_usuario_inclusao'])));
				//debug( $this->Usuario->find('sql', array('fields'=>array('apelido'), 'conditions' => array('codigo'=>$v['codigo_usuario_inclusao']))));
				$v['nome_usuario'] = $nome_usuario['Usuario']['apelido'];

				$listagem[] = $v; 
			}
			//debug($listagem);
			$authUsuario = $this->BAuth->user();		
			$this->set(compact('listagem', 'authUsuario'));
		}
	}

	public function modal_analise($codigo){
		
		$status = array('Pendente de Aprovação', 'Cancelado');

		$this->set(compact('codigo','status'));

		if($this->RequestHandler->isPost()){
			
			if ($this->data['PreFaturamento']['status'] == 0){
				$dados['PreFaturamento']['status'] = "Pendente de Aprovação";
			}else if ($this->data['PreFaturamento']['status'] == 1){
				$dados['PreFaturamento']['status'] = "Cancelado";
			}

			$authUsuario = $this->BAuth->user();
			$dados['PreFaturamento']['analise_descritivo'] = $this->data['PreFaturamento']['analise_descritivo'];
			$dados['PreFaturamento']['codigo'] = $codigo;
			$dados['PreFaturamento']['codigo_usuario_alteracao'] = $authUsuario['Usuario']['codigo'];
			$dados['PreFaturamento']['data_alteracao'] = date("Y-m-d H:i:s");

			$this->PreFaturamento->atualizar($dados);

			//se vier dados do upload
			$url_file_server = "https://api.rhhealth.com.br";

			if(!empty($this->data['PreFaturamentoAnexos']['anexo']['name'])){ 

				$post_params = isset($this->data['PreFaturamentoAnexos']['anexo']) && !empty($this->data['PreFaturamentoAnexos']['anexo']) ? $this->data['PreFaturamentoAnexos']['anexo'] : null ;
				
				$this->Upload->setOption('field_name', 'anexo');            
				$this->Upload->setOption('accept_extensions', array('pdf','jpg','jpeg', 'png'));
				$this->Upload->setOption('accept_extensions_message', 'Arquivo inválido! Favor escolher arquivo Pdf, jpg, jpeg ou png');
				$this->Upload->setOption('size_max', 5242880);
				$this->Upload->setOption('size_max_message', 'Tamanho máximo excedido! Só é permitido arquivos de até 5MB');

				$retorno = $this->Upload->fileServer($this->data['PreFaturamentoAnexos']);

				if (isset($retorno['error']) && !empty($retorno['error']) ){
					$chave = key($retorno['error']);
					$this->BSession->setFlash(array(MSGT_ERROR, $retorno['error'][$chave]));
				} else {

					$nome_arquivo = $this->data['PreFaturamentoAnexos']['anexo']['name'];

					unset($this->data['PreFaturamentoAnexos']['anexo']);

					$this->data['PreFaturamentoAnexos']['anexo'] = $retorno['data'][$nome_arquivo]['path'];
					
					$dados_usuario_imagem['PreFaturamentoAnexos']['anexo'] = $url_file_server.$this->data['PreFaturamentoAnexos']['anexo'];
					$dados_usuario_imagem['PreFaturamentoAnexos']['codigo_pre_faturamento'] = $codigo;
					
					if(!$this->PreFaturamentoAnexos->incluir($dados_usuario_imagem)){
						$errors = $this->PreFaturamentoAnexos->validationErrors;
						//debug($errors);exit();
					}
				}
			}

			//envia alerta
			$find = $this->PreFaturamento->find('first', array('conditions' => array('codigo'=>$codigo)));
			//debug($find);exit;
			$codigo_cliente = $find['PreFaturamento']['codigo_cliente'];
			$codigo_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

			$this->alertaParaValidador($codigo_cliente, $codigo_matriz);

			$this->redirect('/pre_faturamento/gestao');
			
		}
	}	

	/**
	 * [alertaParaAnalisador description]
	 * 
	 * Método para gerar alerta de notificcao quando existir algum exame não aprovado
	 * pegando todo os usuarios internos que sejam analisadores
	 * 
	 * @param  [type] $codigo_cliente        [codigo do cliente que acabou de ser inserido]
	 * @param  [type] $codigo_cliente_matriz [codigo do cliente matriz do usuario logado]
	 * @return [type]                        [description]
	 */
	/*public function alertaParaAnalisador($codigo_cliente, $codigo_cliente_matriz) 
	{

		//monta o email para ser enviado
		App::import('Component', array('StringView', 'Mailer.Scheduler'));
		$this->StringView = new StringViewComponent();

		//dados da matriz
		//$cliente_matriz = $this->Cliente->find('first', array('conditions' => array('Cliente.codigo' => $codigo_cliente_matriz)));

		//seta os dados para o email
		$this->StringView->reset();
		$this->StringView->set('codigo_cliente', $codigo_cliente);
		//$this->StringView->set('Matriz', $cliente_matriz);
		$content = $this->StringView->renderMail('email_validacao_pre_faturamento', 'default');

		$assunto = "Exames que requerem análise";

		//dados para gravar no alerta
		$alerta_dados['Alerta']['codigo_cliente'] 		= $codigo_cliente;
		$alerta_dados['Alerta']['descricao'] 			= $assunto;
		$alerta_dados['Alerta']['email_agendados'] 		= '0';
		$alerta_dados['Alerta']['sms_agendados'] 		= '0';
		$alerta_dados['Alerta']['codigo_alerta_tipo'] 	= '42';
		$alerta_dados['Alerta']['descricao_email'] 		= $content;
		$alerta_dados['Alerta']['model'] 				= 'Cliente';
		$alerta_dados['Alerta']['foreign_key']			= $codigo_cliente_matriz;
		$alerta_dados['Alerta']['assunto'] 				= $assunto;

		if(!$this->Alerta->incluir($alerta_dados)){
			return false;
		}

	}//fim alertaParaAnalisador
*/
	/**
	 * [alertaParaValidador description]
	 * 
	 * Método para gerar alerta de notificação quando é feita a análise
	 * pegando todo os usuarios internos que sejam validadores
	 * 
	 * @param  [type] $codigo_cliente        [codigo do cliente que acabou de ser inserido]
	 * @param  [type] $codigo_cliente_matriz [codigo do cliente matriz do usuario logado]
	 * @return [type]                        [description]
	 */
	public function alertaParaValidador($codigo_cliente, $codigo_cliente_matriz) 
	{

		//monta o email para ser enviado
		App::import('Component', array('StringView', 'Mailer.Scheduler'));
		$this->StringView = new StringViewComponent();

		//seta os dados para o email
		$this->StringView->reset();
		//$this->StringView->set('codigo_cliente', $codigo_cliente);
		//$this->StringView->set('Matriz', $cliente_matriz);

		# Montagem do link #
		
		//monta o hash para colocar no link
		$hash_codigo_cliente = "'{$codigo_cliente}'";        
        $hash = Comum:: encriptarLink($hash_codigo_cliente);
		
		//monta o host
        $host = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? "portal.rhhealth.com.br" : (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO ? "tstportal.rhhealth.com.br" : "portal.localhost"));
		
		$link = "http://{$host}/portal/clientes/pre_faturamento?key=".urlencode($hash);
		$this->StringView->set('link', $link);
		
		# Montagem do link #

		$content = $this->StringView->renderMail('email_pre_faturamento_para_validador', 'default');

		$assunto = "Exames analisados";

		//dados para gravar no alerta
		$alerta_dados['Alerta']['codigo_cliente'] 		= $codigo_cliente;
		$alerta_dados['Alerta']['descricao'] 			= $assunto;
		$alerta_dados['Alerta']['email_agendados'] 		= '0';
		$alerta_dados['Alerta']['sms_agendados'] 		= '0';
		$alerta_dados['Alerta']['codigo_alerta_tipo'] 	= '43';
		$alerta_dados['Alerta']['descricao_email'] 		= $content;
		$alerta_dados['Alerta']['model'] 				= 'PreFaturamento';
		$alerta_dados['Alerta']['foreign_key']			= $codigo_cliente_matriz;
		$alerta_dados['Alerta']['assunto'] 				= $assunto;

		if(!$this->Alerta->incluir($alerta_dados)){
			return false;
		}

	}//fim alertaParaValidador

}//FINAL CLASS ClientesController