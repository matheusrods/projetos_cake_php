<?php
define('COOKIE_LOCAL', $_SERVER['DOCUMENT_ROOT'].'/portal/app/webroot/cookies/cnpj_receita/');
define('HTTP_COOKIE_LOCAL', 'http://'.$_SERVER['SERVER_NAME'].'/portal/cookies/cnpj_receita/');
		
class MultiEmpresasController extends AppController {
    public $name = 'MultiEmpresas';
    public $helpers = array('BForm', 'Html', 'Ajax');

    var $uses = array(
    	'MultiEmpresa',
    	'MultiEmpresaEndereco',
    	'StatusMultiEmpresa',
    	'EnderecoEstado',
    	'Usuario',
    	'EnderecoCidade',
    	'EnderecoEstado',
    	'UsuarioMultiEmpresa'
   	);
   	
    var $components = array('RequestHandler', 'Session', 'Filtros', 'ExportCsv', 'Upload');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(
			array(
				'experimente',
				'verifica_cnpj',
				'getcaptcha',
				'retorno_receita',
				'limpa_cookie',
				'limpa_empresa',
				'_transforma_html_em_array',
				'_pega_o_que_interessa',
				'_retorno_html_receita',
				'selecionar_empresa',
				'selecionar_empresa_listagem',
				'mudar_empresa',
                'incluir'
			)
		);
	}

    function index() {
        $this->pageTitle = 'Empresa';

        //limpa sessao
        $_SESSION['Auth']['Usuario']['codigo_empresa'] = NULL;
        unset($_SESSION['Auth']['Usuario']['nome_empresa']);
        unset($_SESSION['Auth']['Usuario']['cor_menu']);
        unset($_SESSION['Auth']['Usuario']['logomarca']);
    }
    
    function listagem() {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->MultiEmpresa->name);
        
        $conditions = $this->MultiEmpresa->converteFiltroEmCondition($filtros);
        $fields = array('MultiEmpresa.codigo', 'MultiEmpresa.razao_social','MultiEmpresa.nome_fantasia','MultiEmpresa.codigo_documento', 'MultiEmpresa.codigo_status_multi_empresa');
        $order = 'MultiEmpresa.razao_social';
        
        $this->paginate['MultiEmpresa'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
        );
       
        $empresas = $this->paginate('MultiEmpresa');
        $this->set(compact('empresas'));
    }
    
    function selecionar_empresa() {
		
        if(isset($this->authUsuario)) {
    		if(empty($this->authUsuario['Usuario']['usuario_multi_empresa']) && !empty($this->authUsuario['Usuario']['codigo_empresa'])) {
    			$this->redirect('/painel/modulo_administrativo');
    		}
    		 
    		$this->pageTitle = 'Selecione a Empresa para Visualizar seus Cadastros';    		
    	} else {
    		$this->redirect('/portal');
    	}
    }
    
    function selecionar_empresa_listagem() {
    	$this->layout = 'ajax';
    	$filtros = $this->Filtros->controla_sessao($this->data, $this->MultiEmpresa->name);
    	 
    	$empresas_liberadas = $this->UsuarioMultiEmpresa->find('list', array('conditions' => array('codigo_usuario' => $this->BAuth->user('codigo')), 'fields' => array('codigo_multi_empresa', 'codigo_usuario')));
    	
    	if(count($empresas_liberadas)) {
    		$filtros['empresas_liberadas'] = $empresas_liberadas;
    	}
    	
    	$conditions = $this->MultiEmpresa->converteFiltroEmCondition($filtros);
    	$fields = array('MultiEmpresa.codigo', 'MultiEmpresa.razao_social','MultiEmpresa.nome_fantasia','MultiEmpresa.codigo_documento', 'MultiEmpresa.codigo_status_multi_empresa');
    	$order = 'MultiEmpresa.codigo';
    	 
    	$this->paginate['MultiEmpresa'] = array(
    		'fields' => $fields,
    		'conditions' => $conditions,
    		'limit' => 50,
    		'order' => $order,
    	);
    	
    	$empresas = $this->paginate('MultiEmpresa');
    	$this->set(compact('empresas'));    	
    }
    
    function mudar_empresa($codigo) {
    	
    	if(!empty($this->authUsuario['Usuario']['usuario_multi_empresa']) || ($this->authUsuario['Usuario']['codigo_uperfil'] == Uperfil::ADMIN)) {
    		if($codigo) {
    			$infoEmpresa = $this->MultiEmpresa->find('first', array('conditions' => array('codigo' => $codigo)));    			
    			if($infoEmpresa) {
    				$_SESSION['Auth']['Usuario']['codigo_empresa'] = $codigo;
    				$_SESSION['Auth']['Usuario']['nome_empresa'] = $infoEmpresa['MultiEmpresa']['razao_social'];
    				$_SESSION['Auth']['Usuario']['logomarca'] = $infoEmpresa['MultiEmpresa']['logomarca'];
    				$_SESSION['Auth']['Usuario']['cor_menu'] = $infoEmpresa['MultiEmpresa']['cor_menu'];
                    $_SESSION['Auth']['Usuario']['integrar_com_naveg'] = $infoEmpresa['MultiEmpresa']['integrar_com_naveg'];
    				
    				$this->BSession->setFlash('save_success');
    			} else {
    				$this->BSession->setFlash('save_error');
    			}
    		} else {
    			$this->BSession->setFlash('save_error');
    		}
    	} else {
    		$this->BSession->setFlash('perfil_sem_permissao_para_emular');
    	}
    	
    	$this->redirect('/multi_empresas/selecionar_empresa');
    }
    
    function limpa_empresa() {
    	
    	if($_SESSION['Auth']['Usuario']['codigo_empresa']) {
    		$_SESSION['Auth']['Usuario']['codigo_empresa'] = NULL;
    		
			unset($_SESSION['Auth']['Usuario']['nome_empresa']);
			unset($_SESSION['Auth']['Usuario']['cor_menu']);
			unset($_SESSION['Auth']['Usuario']['logomarca']);
			
			$this->BSession->setFlash('save_success');
    	} else {
    		$this->BSession->setFlash('save_success');
    	}
    	 
    	$this->redirect($_SERVER['HTTP_REFERER']);
    }
    
    function incluir() {


         try {

             $this->pageTitle = 'Incluir Empresa';

            if($this->RequestHandler->isPost()) {

                $this->MultiEmpresa->query('begin transaction');

                $this->data['MultiEmpresa']['razao_social']                     = strtoupper($this->data['MultiEmpresa']['razao_social']);
                $this->data['MultiEmpresa']['nome_fantasia']                    = strtoupper($this->data['MultiEmpresa']['nome_fantasia']);
                $this->data['MultiEmpresa']['codigo_status_multi_empresa']      = 1;

                //verifica e insere na tabela multiempresa
                if ($this->MultiEmpresa->incluir($this->data)) {
                    $this->data['MultiEmpresaEndereco']['codigo_multi_empresa'] = $this->MultiEmpresa->id;
                    
                    //verifica s insere na tabela multiempresaendereco
                    if($this->MultiEmpresaEndereco->incluir($this->data)) {

                        //limpa sessao
                        $_SESSION['Auth']['Usuario']['codigo_empresa'] = NULL;
                        unset($_SESSION['Auth']['Usuario']['nome_empresa']);
                        unset($_SESSION['Auth']['Usuario']['cor_menu']);
                        unset($_SESSION['Auth']['Usuario']['logomarca']);

                        $this->MultiEmpresa->commit();

                        $this->BSession->setFlash('save_success');
                        $this->redirect(array('action' => 'index', 'controller' => 'multi_empresas'));
                    } else {
                        $this->BSession->setFlash('save_error');
                        $this->MultiEmpresa->rollback(); 
                    }
                } 
                else {

                    $this->MultiEmpresa->rollback();
                    debug($this->MultiEmpresa->validationErrors);
                    $this->BSession->setFlash('save_error');
                }
            }//fim if post
            
        } catch(Exception $e) {            
            $this->MultiEmpresa->rollback();
            $this->BSession->setFlash('save_error');
        }

        $lista_estados = $this->EnderecoEstado->find('list', array('conditions' => array('codigo_endereco_pais' => 1), 'fields' => array('codigo', 'descricao')));
        $lista_estados[''] = 'UF';
        ksort($lista_estados);

        $_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
        
        $this->set('estados', $lista_estados);
        $this->set('cidades', $this->EnderecoCidade->combo($this->data['MultiEmpresaEndereco']['codigo_estado_endereco']));
       
    }
    
    function editar($codigo) {
        $this->pageTitle = 'Editar Empresa'; 
        
        if($this->RequestHandler->isPost()) {

            $this->data['MultiEmpresa']['razao_social'] = strtoupper($this->data['MultiEmpresa']['razao_social']);
            $this->data['MultiEmpresa']['nome_fantasia'] = strtoupper($this->data['MultiEmpresa']['nome_fantasia']);
           
            try {
            	
            	$this->MultiEmpresa->query('begin transaction');
            	
            	if ($this->MultiEmpresa->atualizar($this->data)) {
            		
            		$this->data['MultiEmpresaEndereco']['codigo_multi_empresa'] = $this->data['MultiEmpresa']['codigo'];
            		
            		if($this->MultiEmpresaEndereco->atualizar($this->data)) {
            			
            			$this->MultiEmpresa->commit();
            			
            			$this->BSession->setFlash('save_success');
            			$this->redirect(array('action' => 'index', 'controller' => 'multi_empresas'));
            		} else {
            			
            			$this->BSession->setFlash('save_error');
            			$this->MultiEmpresa->rollback();
            		}
            	} else {
            		
            		
            		$this->MultiEmpresa->rollback();
            	}
            } catch(Exception $e) {
            	
            	$this->MultiEmpresa->rollback();
            	$this->BSession->setFlash('save_error');
            }
        }

        if (!empty($codigo)) {
            $this->data = $this->MultiEmpresa->read(null, $codigo);
            $this->data += $this->MultiEmpresaEndereco->find('first', array('conditions' => array('codigo_multi_empresa' => $codigo)));
            
            $lista_estados = $this->EnderecoEstado->find('list', array('conditions' => array('codigo_endereco_pais' => 1), 'fields' => array('codigo', 'descricao')));
            $lista_estados[''] = 'UF';
            ksort($lista_estados);
            
            $this->set('estados', $lista_estados);
            $this->set('cidades', $this->EnderecoCidade->combo($this->data['MultiEmpresaEndereco']['codigo_estado_endereco']));
        }
        
        $this->set(compact('codigo'));
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['MultiEmpresa']['codigo'] = $codigo;
        if($status == 1)
            $novo = 2;
        elseif($status == 2)
            $novo = 3;
        else
            $novo = 1;
        
        $this->data['MultiEmpresa']['codigo_status_multi_empresa'] = $novo;

        if ($this->MultiEmpresa->atualizar($this->data, false)) {   
            print 1;
        } else {
            print 0;
        }

        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }

	/**
	 * Ação que inclui os dados da Empresa e Login (cadastro rapido)
	 * @author: Danilo Borges Pereira
	 */
	public function experimente() {
		
	    $this->pageTitle = 'Experimente o nosso Sistema Multi Empresa';
    	$this->layout = 'default';
    	
    	// verifica se o formulario foi submetido!
        if ($this->RequestHandler->isPost()) {

        	if($this->MultiEmpresa->incluir_experimente($this->data)) {
        		$this->BSession->setFlash('multi_empresa_success');  	
				$this->redirect('/');
        	} else {
        		$this->BSession->setFlash('save_error');
        		$this->set('cidades', $this->EnderecoCidade->combo($this->data['MultiEmpresaEndereco']['codigo_estado_endereco']));
        	}
        } else {
        	$this->set('cidades', array('' => 'Cidade (Selecione Primeiro o Estado)'));
        }
        
		$lista_estados = $this->EnderecoEstado->find('list', array('conditions' => array('codigo_endereco_pais' => 1), 'fields' => array('codigo', 'descricao')));
		$lista_estados[''] = 'UF';
		ksort($lista_estados);	
		
        $this->set('menu', false);
        $this->set('estados', $lista_estados);
	}
	
	/**
	 * Ação chamada via Ajax (p/ verificar se CNPJ ja esta cadastrado na Base)
	 * 
	 * @author: Danilo Borges Pereira
	 */
    public function verifica_cnpj() {
        $model_documento = & ClassRegistry::init('Documento');
        
        if($model_documento->isCNPJ($this->params['form']['cnpj']) == false) {
        	echo json_encode(array('resultado' => 0, 'valido' => 0));
        } else {
	    	$conditions = array(
	    		'fields' => array('StatusMultiEmpresa.descricao', 'MultiEmpresa.codigo', 'MultiEmpresa.codigo_status_multi_empresa', 'MultiEmpresa.nome_fantasia'),
	    		'joins' => array(
	    			array(
						'table' => 'status_multi_empresa',
						'alias' => 'StatusMultiEmpresa',
						'type' => 'INNER',
						'conditions' => array (
							'StatusMultiEmpresa.codigo = MultiEmpresa.codigo_status_multi_empresa'
						)    			
	    			)
				)
	    	);
	    	
	    	// tem codigo da proposta?
    		$conditions['conditions'] = array('MultiEmpresa.codigo_documento' => Comum::soNumero($this->params['form']['cnpj']));
	    	
	    	// retorna info proposta
	    	$info = $this->MultiEmpresa->find('first', $conditions);
	    	
		    if(!$info) {
	    		echo json_encode(array('resultado' => 0, 'valido' => 1));
	    	} else {
	    		
	    		echo json_encode(array(
		    		'resultado' => 1,
	    			'valido' => 1,
		    		'codigo' => base64_encode($info['MultiEmpresa']['codigo']),
		    		'codigo_status' => $info['MultiEmpresa']['codigo_status_multi_empresa'],
	    			'nome_fantasia' => $info['MultiEmpresa']['nome_fantasia'],
		    		'status_descricao' => $info['StatusMultiEmpresa']['descricao']
		    	));
	    	}
        }
    	exit;
    }
    
	/**
	 * Ação busca codigo captcha em consulta de cnpj na receita e salva em cookie
	 * 
	 * @author: Danilo Borges Pereira
	 * <daniloborgespereira@gmail.com>
	 */    
    
    public function getcaptcha() {
    	
	    if(!ini_get('date.timezone'))
	    	date_default_timezone_set('GMT');
		
		$cookieFile = COOKIE_LOCAL.session_id();
		
		// cria arquivo onde sera salva a sessão com a receita
		if(!file_exists($cookieFile)) {
			$file = fopen($cookieFile, 'w');
			fclose($file);
		}
			
		$ch = curl_init('http://servicos.receita.fazenda.gov.br/Servicos/cnpjreva/captcha/gerarCaptcha.asp');
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 5000);
		
		// não utilizar returntransfer , este script replica imagem captcha da receita
		$imgsource = curl_exec($ch);
		curl_close($ch);

		if(!empty($imgsource)) {
			$img = @imagecreatefromstring($imgsource);
			header('Content-type: image/jpg');
			imagejpeg($img);
			imagedestroy($img);
		}
		
		$this->autoRender = false;
    }
    
	/**
	 * Ação (via ajax) retorna HTML da consulta de CNPJ na Receita e retorna array com informações da emprsa
	 * 
	 * @author: Danilo Borges Pereira
	 */
        
    public function retorno_receita() {
    	$this->autoRender = false;
    	$html = $this->_retorno_html_receita($this->params);
    	
    	if($html) {
    		$resultado = $this->_transforma_html_em_array($html);
    	} else {
    		$resultado = array('status' => 'CNPJ ou Imagem não encontrado, tente novamente!');
    	}
    	
    	echo json_encode($resultado);
    }
    
    /**
	 * Ação limpa cookies gerado na busca de cnpj na receita (via AJAX)
	 * 
	 * @author: Danilo Borges Pereira
     */
    
    public function limpa_cookie() {
    	$cookieFile = COOKIE_LOCAL . session_id();
    	
    	echo @unlink($cookieFile);
    	exit;
    }
    
	/**
	 * Ação transforma html de retorna da tela de informAção da empresa (CNPJ busca Receita)
	 * 
	 * @author: Danilo Borges Pereira
	 */
        
    public function _transforma_html_em_array($html) {

		// respostas que interessam
		$campos = array (
			'NÚMERO DE INSCRIÇÃO',
			'DATA DE ABERTURA',
			'NOME EMPRESARIAL',
			'TÍTULO DO ESTABELECIMENTO (NOME DE FANTASIA)',
			'CÓDIGO E DESCRIÇÃO DA ATIVIDADE ECONÔMICA PRINCIPAL',
			'CÓDIGO E DESCRIÇÃO DAS ATIVIDADES ECONÔMICAS SECUNDÁRIAS',
			'CÓDIGO E DESCRIÇÃO DA NATUREZA JURÍDICA',
			'LOGRADOURO',
			'NÚMERO',
			'COMPLEMENTO',
			'CEP',
			'BAIRRO/DISTRITO',
			'MUNICÍPIO',
			'UF',
			'ENDEREÇO ELETRÔNICO',
			'TELEFONE',
			'ENTE FEDERATIVO RESPONSÁVEL (EFR)',
			'SITUAÇÃO CADASTRAL',
			'DATA DA SITUAÇÃO CADASTRAL',
			'MOTIVO DE SITUAÇÃO CADASTRAL',
			'SITUAÇÃO ESPECIAL',
			'DATA DA SITUAÇÃO ESPECIAL'
		);
	
		// caracteres que devem ser eliminados da resposta
		$caract_especiais = array(
			chr(9),
			chr(10),
			chr(13),
			'&nbsp;',
			'</b>',
			'  ',
			'<b>MATRIZ<br>',
			'<b>FILIAL<br>'
		 );
			
			// prepara a resposta para extrair os dados
		$html = str_replace ( '<br><b>', '<b>', str_replace ( $caract_especiais, '', strip_tags ( $html, '<b><br>' ) ) );
		
		$html3 = $html;
		
		// faz a extrAção
		for($i = 0; $i < count($campos); $i++) {		
			$html2 = strstr($html,$campos[$i]);
			$resultado[] = trim($this->_pega_o_que_interessa($campos[$i].'<b>','<br>',$html2));
			$html = $html2;
		}
	
		// extrai os CNAEs secundarios , quando forem mais de um
		if(strstr($resultado[5],'<b>')) {
			$cnae_secundarios = explode('<b>',$resultado[5]);
			$resultado[5] = $cnae_secundarios;
			unset($cnae_secundarios);
		}
		
		// devolve STATUS da consulta correto
		if(!$resultado[0]) {
			if(strstr($html3, utf8_decode('O número do CNPJ não é válido')))
				$resultado['status'] = 'CNPJ incorreto ou não existe';
			else
				$resultado['status'] = 'Imagem digitada incorretamente';
		} else {
			$resultado['status'] = 'OK';
		}
		
		if(isset($resultado[10])) {
			$resultado[10] = Comum::soNumero($resultado[10]);
		}
		
		return $resultado;
    }
    
	// função para pegar o que interessa
	public function _pega_o_que_interessa($inicio,$fim,$total) {
		$interesse = str_replace($inicio, '', str_replace(strstr(strstr($total,$inicio),$fim),'',strstr($total,$inicio)));
		return($interesse);
	}    
    
	
	public function atualiza_cor_menu() {
		$this->autoRender = false;
		
		$infoEmpresa = $this->MultiEmpresa->read(null, $this->authUsuario['Usuario']['codigo_empresa']);
		$infoEmpresa['MultiEmpresa']['cor_menu'] = $_POST['cor'];
		
		if($this->MultiEmpresa->save($infoEmpresa)) {
			$_SESSION['Auth']['Usuario']['cor_menu'] = $_POST['cor'];
			
			print '1';
		} else {
			print '0';
		}
		
		exit;
	}
	/**
	 * Ação transforma retorna html da tela de informação da consulta de empresa (CNPJ busca na Receita)
	 * 
	 * @author: Danilo Borges Pereira
	 */
    
    public function _retorno_html_receita($params) {
    	
	    $cookieFile = COOKIE_LOCAL.session_id();
		$cookieFile_fopen = HTTP_COOKIE_LOCAL.session_id();
		
	    if(!file_exists($cookieFile)) {
	        return false;
	    } else {
	    	
			// pega os dados de sessão gerados na visualizAção do captcha dentro do cookie
			$file = fopen($cookieFile, 'r');
			$conteudo = "";
			while (!feof($file)) {
				$conteudo .= fread($file, 1024);
			}
			fclose ($file);
	
			$explodir = explode(chr(9),$conteudo);
			
			$sessionName = trim($explodir[count($explodir)-2]);
			$sessionId = trim($explodir[count($explodir)-1]);
		}
		
		// dados que serão submetidos a consulta por post
	    $post = array (
			'submit1'						=> 'Consultar',
			'origem'						=> 'comprovante',
			'cnpj' 							=> Comum::soNumero($params['form']['cnpj']), 
			'txtTexto_captcha_serpro_gov_br'=> $params['form']['captcha'],
			'search_type'					=> 'cnpj'
	    );
	    
	    $post = http_build_query($post, NULL, '&');

		// prepara a variavel de session
		$cookie = $sessionName.'='.$sessionId;	
		$cookie_post = http_build_query(array ( 'flag' => 1, $sessionName => $sessionId ), NULL, '&');
		
	    $ch = curl_init('http://www.receita.fazenda.gov.br/pessoajuridica/cnpj/cnpjreva/valida.asp');
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);// aqui estão os campos de formulário
	    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);	// dados do arquivo de cookie
	    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);	// dados do arquivo de cookie
	    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:8.0) Gecko/20100101 Firefox/8.0');
	    curl_setopt($ch, CURLOPT_COOKIE, $cookie_post);	    // dados de sessão e flag=1
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
	    curl_setopt($ch, CURLOPT_REFERER, 'http://www.receita.fazenda.gov.br/pessoajuridica/cnpj/cnpjreva/Cnpjreva_Solicitacao2.asp?cnpj=' . $post['cnpj']);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 30000);

	    $html = curl_exec($ch);
	    curl_close($ch);
	    return utf8_encode($html);
    }
    
    function logomarca() {
    	$this->layout = 'ajax';
    	
    	if($this->RequestHandler->isPost()) {
            //dados do formulario
            $post_params = isset($this->RequestHandler->params['form']) && !empty($this->RequestHandler->params['form']) ? $this->RequestHandler->params['form'] : null ;
            // se vier vazio os dados do formulario
            if(empty($post_params)){
                $data = array('error' => 'Dados do formulário não encontrado');
                return $this->responseJson($data);
            }
            //upload para o file server
            $retorno = $this->Upload->fileServer( $post_params );
            // se ocorreu algum erro de comunicação com o fileserver
            if (isset($retorno['error']) && !empty($retorno['error']) ){
                $chave = utf8_decode(key($retorno['error']));
                $msg['erro'] = $retorno['error'][$chave]; 
                return $this->responseJson($msg);
            }

            if(!isset($retorno['data']) && !isset($retorno['data'][$post_params['file']['name']])){
                $chave = utf8_decode(key($retorno['error']));
                $msg['erro'] = $retorno['error'][$chave]; 
                return $this->responseJson($msg);
            }

            $retorno_imagem = $retorno['data'][$post_params['file']['name']];

            $multi_empresa_dados['MultiEmpresa']['logomarca'] = $retorno_imagem['path'];
            $multi_empresa_dados['MultiEmpresa']['codigo'] = $this->authUsuario['Usuario']['codigo_empresa'];

            if($this->MultiEmpresa->atualizar($multi_empresa_dados)) {
                $data = array('data' => array(
                    'path'=> $retorno_imagem['path'],
                    'url'=> $retorno_imagem['path_url'],
                    'message'=>'Imagem salva com sucesso'),
                    'status' => true
                );
                return $this->responseJson($data);
            } else {
                $data = array('erro' => 'Não foi possível salvar imagem no banco de dados');
                return $this->responseJson($data);
            }
    	}
    	//carrega o this->data
    	$info_empresa = $this->MultiEmpresa->find('first', array('conditions' => array('codigo' => $this->authUsuario['Usuario']['codigo_empresa'])));
    	$this->set('info_empresa', $info_empresa);
    }
    
    function _upload($file, $empresa, $novo_nome) {
    	require_once APP . 'vendors' . DS . 'class.upload.php';
    	
    	$imagem = new Upload($file);
    	
    	if ($imagem->uploaded) {
    		
    		// save uploaded image with no changes
    		$imagem->Process($_SERVER['DOCUMENT_ROOT'] . '/portal/app/webroot/files/logomarcas/');
    		
    		if ($imagem->processed) {
    			
    			$imagem->file_new_name_body = 'logomarca-' . $this->authUsuario['Usuario']['codigo_empresa'] . '-grande';
    			
    			$imagem->Process($_SERVER['DOCUMENT_ROOT'] . '/portal/app/webroot/files/logomarcas/');
    			if ($imagem->processed) {
    				
    				// resized to 100px wide
    				$imagem->file_new_name_body = 'logomarca-' . $this->authUsuario['Usuario']['codigo_empresa'] . '-pequena';
    				$imagem->image_resize = true;
    				$imagem->image_y = 30;
    				$imagem->image_ratio_x = true;
    				
    				$imagem->Process($_SERVER['DOCUMENT_ROOT'] . '/portal/app/webroot/files/logomarcas/');
    				
    				if ($imagem->processed) {
    					$imagem->Clean();
    					
    					$infoEmpresa = $this->MultiEmpresa->read(null, $this->authUsuario['Usuario']['codigo_empresa']);
    					$infoEmpresa['MultiEmpresa']['logomarca'] = $imagem->file_dst_name;
    					
    					if($this->MultiEmpresa->save($infoEmpresa)) {
    						return array('upload' => true, 'msg' => 'Deu Boa!', 'imagem' => $infoEmpresa['MultiEmpresa']['logomarca']);
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
}