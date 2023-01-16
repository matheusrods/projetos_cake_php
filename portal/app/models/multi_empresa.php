<?php
class MultiEmpresa extends AppModel {

    var $name = 'MultiEmpresa';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'multi_empresa';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
	var $validate = array(
		'razao_social' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a Razão Social!'
			),	
		'nome_fantasia' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Nome Fantasia!'
			),
		'codigo_documento' => array(
		    'notEmpty' => array(
		        'rule' => 'notEmpty',
		        'message' => 'Informe o CNPJ!',
		    ),
		    'documentoValido' => array(
		        'rule' => 'documentoValido',
		        'message' => 'CNPJ inválido, verifique!',
		    ),
		    'isUnique' => array(
					'rule' => 'isUnique',
					'message' => 'CNPJ já existente na base',
					'on' => 'create'
			),
		)
	);

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if(!empty($data['empresas_liberadas'])) {
			$liberados = implode(array_keys($data['empresas_liberadas']), ",");
        	$conditions[] = array("MultiEmpresa.codigo IN ({$liberados})");
        }
        
        if (!empty($data['codigo']))
            $conditions[] = array('MultiEmpresa.codigo' => $data['codigo']);

        if (!empty($data['razao_social']))
        	$conditions[] = array('MultiEmpresa.razao_social LIKE' => '%' . $data['razao_social'] . '%');
		
		if (!empty($data['nome_fantasia']))
			$conditions[] = array('MultiEmpresa.nome_fantasia LIKE' => '%' . $data['nome_fantasia'] . '%');

		if (!empty($data['codigo_documento']))
			$conditions[] = array('MultiEmpresa.codigo_documento' => $data['codigo_documento']);

		if (isset( $data['codigo_status_multi_empresa'])) {
			if ($data['codigo_status_multi_empresa'] == '3')
				$conditions[] = '(MultiEmpresa.codigo_status_multi_empresa = ' . $data['codigo_status_multi_empresa'] . ' OR MultiEmpresa.codigo_status_multi_empresa IS NULL)';
			else if (($data['codigo_status_multi_empresa'] == '1') || ($data['codigo_status_multi_empresa'] == '2'))
				$conditions[] = array('MultiEmpresa.codigo_status_multi_empresa' => $data['codigo_status_multi_empresa']);
        }
        
        return $conditions;
    }

    function documentoValido() {
        $model_documento = & ClassRegistry::init('Documento');
        $codigo_documento = $this->data[$this->name]['codigo_documento'];
        
		if($codigo_documento) {
	        if($model_documento->isCNPJ($codigo_documento) == false)
	            return false;
	        else
	            return true;        	
        } else {
        	return true;
        }
    }
    
    function unicoCNPJ() {
    	$codigo_documento = $this->data[$this->name]['codigo_documento'];
    	
    	if($codigo_documento) {
    		if($this->find('all', array('conditions' => array('codigo_documento' => $codigo_documento)))) {
    			return false;	
    		} else {
    			return true;
    		}
    	} else {
    		return true;
    	}
    }    
    
    function incluir_experimente($dados) {

    	// MODELS
    	$model_MultiEmpresaEndereco = & ClassRegistry::init('MultiEmpresaEndereco');

    	// tira maskara formatacao do cnpj
        if(isset($dados['MultiEmpresa']['codigo_documento']) && $dados['MultiEmpresa']['codigo_documento']) {
        	$dados['MultiEmpresa']['codigo_documento'] = Comum::soNumero($dados['MultiEmpresa']['codigo_documento']);
        }
        
        $invalidadeFields = array();
        
		try {
			
			$success = false;
			
            $this->query('begin transaction');
            $dados['MultiEmpresa']['codigo_status_multi_empresa'] = '1';
            
            if(!parent::incluir($dados)) {
            	throw new Exception('Não gravou!');
            }
            
			if(isset($dados['MultiEmpresaEndereco'])) {
				// inclui no array de endereco a ser inserido, o id da proposta!
		        $dados['MultiEmpresaEndereco']['cep'] = Comum::soNumero($dados['MultiEmpresaEndereco']['cep']);
		        $dados['MultiEmpresaEndereco']['codigo_multi_empresa'] = $this->getInsertID();
		        
	        	// inclui endereco
	            if (!$model_MultiEmpresaEndereco->incluir($dados['MultiEmpresaEndereco'])) {
					$invalidadeFields += $this->trata_erros('MultiEmpresaEndereco', $model_MultiEmpresaEndereco->validationErrors);
	            } else {
	            	if($this->_enviaSenha($dados, $this->getInsertID())) {
	            		$success = true;
	            	}
	            }
			}
			
			if(count($invalidadeFields))
				$model_MultiEmpresaEndereco->validationErrors = $invalidadeFields;
			
			if(!(count($this->validationErrors) || count($model_MultiEmpresaEndereco->validationErrors)) && $success) {
				
				$this->commit();
	            return true;
			} else {
				
				return false;
			} 

        } catch (Exception $ex) {
            $this->rollback();
            return false;
        }
    }
    
    private function trata_erros($model, $erros) {
    	$retorno = array();
    	foreach($erros as $campo => $mensagem)
			$retorno[$campo] = $mensagem;
			
    	return $retorno;
    }
	
    /**
     * Função cria (login e senha) e envia p/ e-mail do credenciado!
     *
     * @author: Danilo Borges Pereira
     */
    
    public function _enviaSenha($dados, $codigo_empresa) {
    	$model_Usuario = & ClassRegistry::init('Usuario');
    	
    	$invalidadeFields = array();

    	// verifica se usuario ja existe
    	if(! $model_Usuario->find('first', array('conditions' => array('codigo_empresa' => $codigo_empresa)))) {
    			
    		$dados_user['Usuario']['senha'] = str_pad ( ( string ) mt_rand ( 0, 999999 ), 6, '0', STR_PAD_LEFT );
    		$dados_user['Usuario']['nome'] = $dados['MultiEmpresa']['nome_fantasia'];
    		
    		$apelido = explode('@', $dados['MultiEmpresa']['email']);
    		$user_info = $model_Usuario->find('all', array('conditions' => array('apelido' => $apelido[0])));
    			
    		$dados['MultiEmpresa']['email'] = explode(";", $dados['MultiEmpresa']['email']);
    		$dados['MultiEmpresa']['email'] = current($dados['MultiEmpresa']['email']);
    		
    		$dados_user['Usuario']['apelido'] = !count($user_info) ? $apelido[0] : $apelido[0] . $codigo_empresa;
    		$dados_user['Usuario']['email'] = $dados['MultiEmpresa']['email'];
    		$dados_user['Usuario']['ativo'] = true;
    		$dados_user['Usuario']['codigo_uperfil'] = 1;
    		$dados_user['Usuario']['codigo_departamento'] = 1;
    		$dados_user['Usuario']['codigo_empresa'] = $codigo_empresa;
    		$dados_user['Usuario']['codigo_usuario_inclusao'] = isset($this->authUsuario['Usuario']['codigo']) ? $this->authUsuario['Usuario']['codigo'] : 0;

    		try {
    			$model_Usuario->query('BEGIN TRANSACTION');
    
    			if ($model_Usuario->incluir($dados_user)) {
   				
    				$template = 'envio_usuario_senha_multiempresa';
    				
    				App::import('Component', array('StringView', 'Mailer.Scheduler'));
    				
    				$this->stringView = new StringViewComponent();
    				$this->scheduler = new SchedulerComponent();
    				$this->stringView->reset();
    				$this->stringView->set('dados', $dados_user);
    				
    				if($codigo_empresa) {
    					$this->stringView->set('codigo', $codigo_empresa);
    				}
    					
    				$this->scheduler->schedule($this->stringView->renderMail($template), array (
    						'from' => 'portal@rhhealth.com.br',
    						'to' => (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO) ? $dados_user['Usuario']['email'] : 'tid@ithealth.com.br',
    						'subject' => 'Dados de Acesso - Sistema Multi Empresa (RHhealth)'
    					)
    				);
    			} else {
    				
    				$invalidadeFields += $this->trata_erros('Usuario', $model_Usuario->validationErrors);
    			}
    			
    			if(count($invalidadeFields))
					$this->validationErrors = $invalidadeFields;
				
				if(count($this->validationErrors)) {
					return false;
				} else {
					$model_Usuario->commit();
					return true;
				}      			
    
    		} catch(Exception $e) {
    			$model_Usuario->rollback();
    			return false;
    		}
    			
    	} else {
    		return true;
    	}
    }

    //busca url da logomarca
    public function urlLogomarca($codigo){

				$url = '';

        // se existir um codigo empresa buscar a empresa e a url da imagem
        if(!empty($codigo)){

            App::Import('Component',array('FileServer'));
            $this->FileServer = new FileServerComponent();
            //get multi empresa
            $caminho_logomarca = $this->find('first', array('conditions' => array('codigo' => $codigo)));
            
            if(isset($caminho_logomarca['MultiEmpresa']) 
                && isset($caminho_logomarca['MultiEmpresa']['logomarca']) 
                && trim($caminho_logomarca['MultiEmpresa']['logomarca']) != ''
                ) {
                //url direto do fileserver  
                $url = $this->FileServer->getUrl($caminho_logomarca['MultiEmpresa']['logomarca']); 
            }
            return $url;            
        }
    }// fim
}

?>