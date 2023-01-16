<?php 
class SmsController extends AppController {
	public $name = 'Sms';
	public $uses = array('SmsOutbox', 'SmsInbox');
	public $components = array('RequestHandler');
	public $autoRender = false;

	function add() {
		$raw_post = trim(file_get_contents('php://input'));
		$data = json_decode($raw_post, true);
		
		if ($this->SmsOutbox->agendar(
				isset($data['fone_para']) ? $data['fone_para'] : null,
				isset($data['mensagem']) ? $data['mensagem'] : null
			)) {
			$this->header('HTTP/1.1 201: CREATED');
			$data = 'ok';
		} else {
			$this->header('HTTP/1.1 400: BAD REQUEST');
			$data = $this->SmsOutbox->validationErrors;
		}
		
		$this->set(compact('data'));
		$this->render('/elements/json');
	}
	
	function latest($toNumber, $sinceId = 0) {
		$this->layout = 'ajax';
		$data = $this->SmsInbox->latest($toNumber, $sinceId);
		$data = Set::extract('/SmsInbox/.', $data);
		$this->set(compact('data'));
		$this->render('/elements/json');
	}

    function index() {
        $this->autoRender = true;
        $this->pageTitle = 'Listagem de SMS';
        $this->carrega_combos();
        $this->data['SmsOutbox'] = $this->Filtros->controla_sessao($this->data, "SmsOutbox");
    }


    function listagem_sms() {
    	$this->autoRender = true;    	
		$filtros = $this->Filtros->controla_sessao($this->data, "SmsOutbox");
        $conditions = $this->SmsOutbox->converteFiltroEmConditions($filtros);       

        $fields = array('SmsOutbox.codigo',
                        'SmsOutbox.fone_de',            
                        'SmsOutbox.fone_para',
                        'SmsOutbox.mensagem',                        
                        'SmsOutbox.data_envio',
                        'SmsOutbox.sistema_origem',        
                        'SmsOutbox.data_inclusao',                                                
                        'SmsOutbox.liberar_envio_em');
        $order = 'data_inclusao desc';
        $limit = '50';    
        
      	$this->paginate['SmsOutbox']  = array(
      		'conditions'=>$conditions,
      		'order'=>$order,
      		'limit'=>$limit,
      		'fields'=>$fields
      	);

        $dados = $this->paginate('SmsOutbox');
        $this->set(compact('dados'));    	
    }

    function incluir() {
    	$this->autoRender = true;
    	 $this->carrega_combos();

    	if (isset($this->data['SmsOutbox'])) {
    		if (!empty($this->data['SmsOutbox']['fone_para'])) {
    		  $this->data['SmsOutbox']['fone_para'] = COMUM::soNumero($this->data['SmsOutbox']['fone_para']);
    		}
    		$this->data['SmsOutbox']['sistema_origem'] = SmsOutbox::MANUAL;

			if ($this->SmsOutbox->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }    		
    	}
    	
    }

    function importar($nome_arquivo_retorno = NULL) {
        $this->autoRender = true;
        $this->pageTitle = 'Importar SMS'; 
            
        if(!empty($nome_arquivo_retorno)) {
            
          $arquivo = APP.DS.'tmp'.DS.$nome_arquivo_retorno.'.txt';
          if( file_get_contents($arquivo) ){                               
            Configure::write('debug',0);
            header("Content-Type: application/force-download");
            header('Content-Disposition: attachment; filename="importacao_sms.txt"');
            echo file_get_contents($arquivo);   
            unlink($arquivo);
            die();
            }
          }
        else{
            if (!empty($this->data)) {
            $this->arq_erros_importacao = APP.DS.'tmp'.DS.date('YmdHis').'.txt';            
            $nome_arquivo = date('YmdHis').'.txt';

            if ( $this->data['SmsOutbox']['arquivo']['name'] != NULL ) {
                $type = strtolower(end(explode('.', $this->data['SmsOutbox']['arquivo']['name'])));
                $max_size = (1024*1024)*5;
                if ( $type === "csv" && $this->data['SmsOutbox']['arquivo']['size'] < $max_size ) {
                    $destino = APP.DS.'tmp'.DS.date('YmdHis').$this->data['SmsOutbox']['arquivo']['name'];
                    if ( move_uploaded_file($this->data['SmsOutbox']['arquivo']['tmp_name'], $destino) == TRUE ) {
                        $arquivo = fopen($destino, "r");
                        if ($arquivo) {
                            $i=0;
                            $fp = fopen($this->arq_erros_importacao, "a+");
                            while (!feof($arquivo)) {
                                $linha = trim( fgets($arquivo, 4096) );
                                if( $i > 0 && $linha!="" ){
                                    $data_sms = array();
                                    $dados_sms = explode(';', $linha );     

                                    $data_sms['SmsOutbox']['sistema_origem'] = SmsOutbox::PLANILHA;
                                    $data_sms['SmsOutbox']['fone_para'] = COMUM::soNumero($dados_sms[0]);
                                    $data_sms['SmsOutbox']['mensagem'] = $dados_sms[1];
                                    if (!empty($dados_sms[2])) {
                                        $data_sms['SmsOutbox']['liberar_envio_em'] = $dados_sms[2];
                                    }
                                    $sucesso = 'sucesso';
                                    if (!$this->SmsOutbox->incluir($data_sms)) {
                                        $sucesso = 'erro';
                                    }
                                    fwrite($fp, $sucesso.';'.$linha."\r\n");
                                    $erro = $this->SmsOutbox->invalidFields();                                    
                                }    
                                ++$i;
                            }

                            fclose($fp);
                            fclose($arquivo);
                            unlink($destino); 
                            $this->redirect(array('action' => 'importar',$nome_arquivo));
                        }
                    }
                }            
            }
        }
        }
    }

    function carrega_combos() {
    	$modem = array(1 =>'MODEM 1', 2 =>'MODEM 2', 3 => 'MODEM 3', 4 =>'MODEM 4');
    	$sistema_origem = array(SmsOutbox::MANUAL => SmsOutbox::MANUAL, SmsOutbox::PLANILHA =>SmsOutbox::PLANILHA);
    	$this->set(compact('modem','sistema_origem')); 		
    }

}