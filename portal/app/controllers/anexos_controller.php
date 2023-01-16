<?php

App::import('Core', 'Sanitize');
App::import('Core', 'Validation');

class AnexosController extends AppController {
	public $name = 'Anexos';
	public $helpers = array('Ithealth');
	public $components = array('Filtros', 'RequestHandler','ExportCsv', 'Upload');
	var $uses = array(
		'Cliente',
		'PedidoExame',
		'AnexoExame',
		'ItemPedidoExame',
		'AnexoFichaClinica',
		'ItemPedidoExame',
		'AuditoriaExame',
		'Fornecedor',
		'Configuracao'
	);

	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(
			'clientes',
			'listagem_anexos_reprovados_clientes',
			'anexos_reprovados',
			'listagem_reprovados',
			'modal_reprovados',
			'upload_exame',
			'upload_ficha_clinica'
		);
	}

    public function index()
    {
		$this->pageTitle = 'Anexos Reprovados';
        //pega os filtro do controla sessao
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		$codigo_fornecedor = (isset($_SESSION['Auth']['Usuario']['codigo_fornecedor']) ? $_SESSION['Auth']['Usuario']['codigo_fornecedor'] : (isset($this->data['Cliente']['codigo_fornecedor']) ? $this->data['Cliente']['codigo_fornecedor'] : ''));

        $fornecedor = $this->Fornecedor->find('first',array('conditions' => array('codigo' => $codigo_fornecedor), 'recursive' => -1));
		$nome_fornecedor = $this->nome_fornecedor($codigo_fornecedor);

		$codigo_fornecedor = $fornecedor['Fornecedor']['codigo'];
		$filtros['codigo_fornecedor'] = $codigo_fornecedor;
		$this->set(compact('nome_fornecedor'));

        $this->set(compact('codigo_fornecedor', 'nome_fornecedor'));
    }

	public function nome_fornecedor($codigo_fornecedor)
	{
		$this->loadModel("Fornecedor");

		$fornecedor = $this->Fornecedor->find('first',array('conditions' => array('codigo' => $codigo_fornecedor)));
		$nome_fornecedor = $fornecedor['Fornecedor']['razao_social'];

		return $nome_fornecedor;
	}

	public function listagem_anexos_reprovados_clientes()
    {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
		// debug($filtros);

        // INICIO - 
		$codigo_fornecedor= '';
		if (isset($filtros['codigo_fornecedor']) && !empty($filtros['codigo_fornecedor'])) {
			$codigo_fornecedor = $filtros['codigo_fornecedor'];
		}
        
		$fornecedor = $this->Fornecedor->find('first',array('conditions' => array('codigo' => $codigo_fornecedor), 'recursive' => -1));
	
        $this->set(compact('codigo_fornecedor', 'fornecedor'));
    }

    public function listagem_reprovados($codigo_fornecedor = null, $export = null)
    {
        $this->layout = 'ajax';
		$this->loadModel("AnexoExame");


		$filtros = $this->Filtros->controla_sessao($this->data, $this->PedidoExame->name);
		
		$query = $this->PedidoExame->getListaAnexoExameImagens($codigo_fornecedor, $filtros);

		$this->paginate['PedidoExame'] = $query;

		if($export) {
			$this->exames_reprovados_export($query);
		}

		$query['limit'] = 50;

		$anexos = $this->paginate('PedidoExame');

        $this->set(compact('anexos', 'codigo_fornecedor'));
    }

	public function anexos_reprovados($codigo_fornecedor)
    {
        $this->pageTitle = 'Anexos Reprovados';
        //pega os filtro do controla sessao
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
		$filtros['codigo_fornecedor'] = $codigo_fornecedor;

		$fornecedor = $this->Fornecedor->find('first',array('conditions' => array('codigo' => $codigo_fornecedor), 'recursive' => -1));
		$nome_fornecedor = $this->nome_fornecedor($codigo_fornecedor);
		
        $this->set(compact('codigo_fornecedor', 'nome_fornecedor'));
    }

	public function cliente_nome($codigo_cliente)
    {
        $this->loadModel("Cliente");

        if (!empty($codigo_cliente)) {
            $nome_fantasia = $this->Cliente->find('first', array(
                'fields' => array(
                    'nome_fantasia'
                ),
                'conditions' => array(
                    'codigo' => $codigo_cliente
                )
            ));
            return $nome_fantasia['Cliente']['nome_fantasia'];
        } else {
            return '';
        }
    }

	public function modal_reprovados($codigo_item_pedido_exame, $codigo_exame, $codigo_status_auditoria_imagem, $codigo_cliente, $codigo_pedido_exame, $caminho_arquivo, $codigo_ficha_clinica, $caminho_ficha_clinica = null)
	{
		//$this->layout = 'ajax';
		$anexos_ficha_clinica = 0;

		$caminho_arquivo = base64_decode($caminho_arquivo);
		$caminho_ficha_clinica = base64_decode($caminho_ficha_clinica);

		if ($codigo_exame == $this->Configuracao->getChave('INSERE_EXAME_CLINICO')) {
			$filtros = array(
				"codigo_pedido_exame" => $codigo_pedido_exame,
				"codigo_item_pedido_exame" => $codigo_item_pedido_exame
			);
		
			$this->paginate['PedidoExame'] = $this->PedidoExame->getListaAnexoExameImagens($codigo_cliente, $filtros);
	
			$anexos = $this->paginate('PedidoExame');

			if (!empty($anexos[0]['AnexosFichasClinicas']['caminho_arquivo'])) {
				$anexos_ficha_clinica = 1;
			} else {
				$anexos_ficha_clinica = 0;
			}
		}


		$this->set(compact('codigo_item_pedido_exame', 'codigo_exame', 'codigo_status_auditoria_imagem', 'anexos_ficha_clinica', 'codigo_pedido_exame', 'codigo_cliente', 'caminho_arquivo', 'codigo_ficha_clinica', 'caminho_ficha_clinica'));

	}

	public function upload_exame($codigo_item_pedido_exame, $codigo_exame, $codigo_status_auditoria_imagem, $codigo_cliente, $codigo_pedido_exame)
	{
		$this->layout = 'ajax';

		// pr($_POST);
		// pr($_FILES);

		$nome_arquivo =  strtolower($_FILES['data']['name']['AnexoExame']['anexo_exame']);
		preg_match("/(\..*){1}$/i", $nome_arquivo, $ext);

		if (strpos($nome_arquivo, ".pdf") > 0 || strpos($nome_arquivo, ".jpg") > 0 || strpos($nome_arquivo, ".png") > 0) {
			$nome_arquivo = $this->data['AnexoExame']['anexo_exame']['name'];                
			$this->Upload->setOption('field_name', 'anexo_exame');            
			$this->Upload->setOption('accept_extensions', array('pdf','jpg','jpeg', 'png'));
			$this->Upload->setOption('accept_extensions_message', 'Arquivo inválido! Favor escolher arquivo Pdf, jpg, jpeg ou png');
			$this->Upload->setOption('size_max', 5242880);
			$this->Upload->setOption('size_max_message', 'Tamanho máximo excedido! Só é permitido arquivos de até 5MB');
			$retorno = $this->Upload->fileServer($this->data['AnexoExame']);
			// se ocorreu algum erro de comunicação com o fileserver
			if (isset($retorno['error']) && !empty($retorno['error']) ){                    
				$chave = key($retorno['error']);
				// $this->BSession->setFlash(array(MSGT_ERROR, $retorno['error'][$chave]));   /////////////////////////////////                 
				$this->redirect(false);
			} else {
				$anexo = $this->AnexoExame->find('first',array('conditions' => array('codigo_item_pedido_exame' => $codigo_item_pedido_exame)));
				$status = 1;
				if(!empty($this->authUsuario['Usuario']['codigo_fornecedor'])){
					$status = 2;
				}
				if(empty($anexo)){
					$dados['AnexoExame'] = array(
						'codigo_item_pedido_exame' => $codigo_item_pedido_exame,
						'caminho_arquivo' =>  $retorno['data'][$nome_arquivo]['path_url'], //$codigo_item_pedido.DS.'anexo_item_exame_'.$codigo_item_pedido. $ext[0],
						'status' => $status
					);
					if($this->AnexoExame->incluir($dados)){
						// $this->BSession->setFlash('save_success');
						// $this->redirect(array('action' => 'index2'));
						//$this->redirect(false);
						$auditoriaExame = $this->AuditoriaExame->find("first", array("conditions" => array('codigo_item_pedido_exame' => $codigo_item_pedido_exame)));

						if (!empty($auditoriaExame)) {

							$auditoriaExame['AuditoriaExame']['codigo_status_auditoria_imagem'] = 1;
							
							if ($this->AuditoriaExame->atualizar($auditoriaExame)) {

							}

						}

					} else {
						// $this->BSession->setFlash('save_error');
						// $this->redirect(array('action' => 'index2'));
						//$this->redirect(false);
					}
				} else {
					$anexo['AnexoExame']['caminho_arquivo'] =  $retorno['data'][$nome_arquivo]['path_url']; // $codigo_item_pedido.DS.'anexo_item_exame_'.$codigo_item_pedido. $ext[0];
					$anexo['AnexoExame']['codigo_usuario_inclusao'] = $_SESSION['Auth']['Usuario']['codigo'];
					$anexo['AnexoExame']['data_inclusao'] = date("Y-m-d H:i:s");
					$anexo['AnexoExame']['status'] = $status;
				
					if($this->AnexoExame->atualizar($anexo)){				
						$auditoriaExame = $this->AuditoriaExame->find("first", array("conditions" => array('codigo_item_pedido_exame' => $codigo_item_pedido_exame)));

						if (!empty($auditoriaExame)) {

							$auditoriaExame['AuditoriaExame']['codigo_status_auditoria_imagem'] = 1;
							
							if ($this->AuditoriaExame->atualizar($auditoriaExame)) {
								echo " Atualizou AuditoriaExame 1 ";
							} else {
								echo " Não Atualizou AuditoriaExame 1 ";
							}
						}
					} else {
						echo "Não Atualizou";
					}
				}
			}
		} 

		exit;
	}

	public function upload_ficha_clinica($codigo_item_pedido, $codigo_ficha_clinica){
		$this->loadModel('ItemPedidoExame');

		$this->layout = 'ajax';

		//pr($_FILES['data']);
        if($this->RequestHandler->isPost()) {
           
			$nome_arquivo =  strtolower($_FILES['data']['name']['AnexoFichaClinica']['ficha_clinica']);
            preg_match("/(\..*){1}$/i", $nome_arquivo, $ext);
           
			if (strpos($nome_arquivo, ".pdf") > 0 || strpos($nome_arquivo, ".jpg") > 0 || strpos($nome_arquivo, ".png") > 0){
                if(!is_dir(DIR_ANEXOS.$codigo_item_pedido.DS))
                    mkdir(DIR_ANEXOS.$codigo_item_pedido.DS);
                $destino = DIR_ANEXOS.DS.$codigo_item_pedido.DS.'anexo_ficha_clinica_'.$codigo_item_pedido. $ext[0];
                $caminho_completo = end(glob(DIR_ANEXOS.$codigo_item_pedido.DS.'anexo_ficha_clinica_'.$codigo_item_pedido.'.*'));
                if (is_file($caminho_completo))
                    unlink($caminho_completo);
                if(!move_uploaded_file($_FILES['data']['tmp_name']['AnexoFichaClinica']['ficha_clinica'],$destino)){
                    $this->BSession->setFlash('save_error');
                   // $this->redirect(array('action' => 'index2'));
                } else {
                    $anexo = $this->AnexoFichaClinica->find('first',array('conditions' => array('codigo_ficha_clinica' => $codigo_ficha_clinica)));
                    if(empty($anexo)){
                        $dados['AnexoFichaClinica'] = array(
                            'codigo_ficha_clinica' => $codigo_ficha_clinica,
                            'caminho_arquivo' => $codigo_item_pedido.DS.'anexo_ficha_clinica_'.$codigo_item_pedido. $ext[0],
                        );
                        if($this->AnexoFichaClinica->incluir($dados)){
                            // $this->BSession->setFlash('save_success');
                            // $this->redirect(array('action' => 'index2'));
                        } else {
                            // $this->BSession->setFlash('save_error');
                            // $this->redirect(array('action' => 'index2'));
                        }
                    } else {
                        $anexo['AnexoFichaClinica']['caminho_arquivo'] = $codigo_item_pedido.DS.'anexo_ficha_clinica_'.$codigo_item_pedido. $ext[0];
                        $anexo['AnexoFichaClinica']['codigo_usuario_inclusao'] = $_SESSION['Auth']['Usuario']['codigo'];
                        $anexo['AnexoFichaClinica']['data_inclusao'] = date("Y-m-d H:i:s");
                      
						if($this->AnexoFichaClinica->atualizar($anexo)){
                            // $this->BSession->setFlash('save_success');
                            // $this->redirect(array('action' => 'index2'));

							$auditoriaExame = $this->AuditoriaExame->find("first", array("conditions" => array('codigo_item_pedido_exame' => $codigo_item_pedido)));

							if (!empty($auditoriaExame)) {

								$auditoriaExame['AuditoriaExame']['codigo_status_auditoria_imagem'] = 1;
								
								if ($this->AuditoriaExame->atualizar($auditoriaExame)) {
									echo " Atualizou AuditoriaExame 2 ";
								} else {
									echo " Não Atualizou AuditoriaExame 2 ";
								}
							}
                        } else {
							echo "Não Atualizou";
						}
                    }
                }
            }
        }
		
        exit;
    }

	public function exames_reprovados_export($query)
    {

        $dbo = $this->Fornecedor->getDataSource();
		
		$rawQueryData = $this->PedidoExame->find('sql', $query);
        $dbo->results = $dbo->rawQuery($rawQueryData);

        ob_clean();

        //$relatorio_padrao_encoding =  'UTF-8';   // UTF funciona, mas exigiu conversão UTF pelo programa usado LibreOffice
        $relatorio_padrao_encoding =  'ISO-8859-1'; // conforme importação padrão sugerida no LibreOffice ISO-8859-1 funcionou bem para 
                                                    // Windows 1252/WinLatin 1 
                                                    // Windows 1250/WinLatin 2
                                                    // ISO-8859-15/EURO
                                                    // ISO-8859-14
                                                    // ASCII/Inglês Norte Americano
                                                    // Europa oriental ISO 8859-2
                                                    // Turco (ISO 8859-9)
                                                    // Turco (Windows-1254)
                                                    // Vietnamita (Windows-1258)
                                                    // Sistema, Caso o sistema operacional seja Português Brasil


        header('Content-Encoding: '.$relatorio_padrao_encoding);
        header("Content-Type: application/force-download;charset=".$relatorio_padrao_encoding);
        header('Content-Disposition: attachment; filename="anexos_reprovados'.date('YmdHis').'.csv"');
        header('Pragma: no-cache');

        //cabecalho do arquivo
        echo Comum::converterEncodingPara('"Cód. do pedido";"Cód. credenciado";"Nome Fantasia";"Funcionário";"Unidade";"Exame";"Data realização exame";"Usuário anexo";"Data anexo";"Motivo";"Observação";')."\n";
        

        while ($value = $dbo->fetchRow()) {

			$linha  = '';

			$linha .= $value['PedidoExame']['codigo'].';';
			$linha .= $value['Fornecedor']['codigo'].';';
			$linha .= Comum::converterEncodingPara(trim($value['Fornecedor']['nome']), $relatorio_padrao_encoding).';';
			$linha .= Comum::converterEncodingPara(trim($value['Funcionario']['nome']), $relatorio_padrao_encoding).';';
			$linha .= Comum::converterEncodingPara(trim($value['Clientes']['nome_fantasia']), $relatorio_padrao_encoding).';';
			$linha .= Comum::converterEncodingPara(trim($value['Exame']['descricao']), $relatorio_padrao_encoding).';';
			$linha .= Comum::formataData($value['ItemPedidoExame']['data_realizacao_exame'],'ymd','dmy').';';
			$linha .= Comum::converterEncodingPara(trim($value['Usuario']['nome']), $relatorio_padrao_encoding).';';
			$linha .= Comum::formataData($value['AuditoriaExame']['data_inclusao'],'mssql','dmyhms').';';
			$linha .= Comum::converterEncodingPara(trim($value['TipoGlosas']['descricao']), $relatorio_padrao_encoding).';';
			$linha .= Comum::converterEncodingPara(trim($value['Glosas']['motivo_glosa']), $relatorio_padrao_encoding).';';

			$linha .= "\n";
            
            echo $linha;
            
        }
        die();
	}
}