<?php
class RemessaBancariaController extends AppController {
	
	public $name = 'RemessaBancaria';
	public $helpers = array('BForm', 'Html', 'Ajax');
	var $components = array('Mailer.Scheduler');
	
	var $uses = array(
		'Cliente', 
		'RemessaBancaria',
		'RemessaStatus',
		'RemessaRetorno',
		'Usuario',
		'ClienteEndereco',
		'VEndereco',
		'Banco'
		);
	var $arquivo = "";
	/**
	 * Metodo da pagina principal
	 */ 
	public function index() 
	{
        //titulo da pagina na view
		$this->pageTitle = 'Remessa/Retorno Bancário';
       	$this->data = $this->Filtros->controla_sessao($this->data, 'RemessaBancaria');
		$tipos_periodo = array(
			'I' => 'Inclusão',
			'E' => 'Emissão',
			'V' => 'Vencimento',
			'P' => 'Pagamento'
		);
		//bancos que estamos trabalhando
		$bancos = array(
			'341' => '341 - Itaú',
			'033' => '033 - Santander',
			'353' => '353 - Santander'
		);
		$tipo_arquivo = array(
			'REM' => 'Remessa',
			'RET' => 'Retorno'
		);
  		$codigo_cliente = isset($this->data['codigo_cliente']) ? $this->data['codigo_cliente'] : '';
		$filtros['data_inicio'] = (isset($this->data['data_inicio'])) ? $this->data['data_inicio'] : date('d/m/Y');
		$filtros['data_fim'] =  (isset($this->data['data_fim'])) ? $this->data['data_fim'] :  date('d/m/Y');
		$filtros['codigo_remessa_status'] =  (isset($this->data['codigo_remessa_status'])) ? $this->data['codigo_remessa_status'] :  '';
		$filtros['codigo_remessa_retorno'] =  (isset($this->data['codigo_remessa_retorno'])) ? $this->data['codigo_remessa_retorno'] :  '';
		// $filtros['codigo_remessa_retorno'] =  (isset($this->data['codigo_remessa_retorno'])) ? $this->data['codigo_remessa_retorno'] :  '';
		// $filtros['codigo_remessa_retorno'] =  (isset($this->data['codigo_remessa_retorno'])) ? $this->data['codigo_remessa_retorno'] :  '';
		$this->data['RemessaBancaria'] = $filtros;
		$filtros = $this->Filtros->controla_sessao($this->data, 'RemessaBancaria');
    	//pega as remessa carregadas
    	$status = $this->RemessaStatus->find('list');
    	
    	$this->RemessaRetorno->virtualFields = array('descricao' => 'concat(codigo_ocorrencia," - ",descricao)');
    	
    	$retorno = $this->RemessaRetorno->find('list',array('fields'=>array('codigo', 'codigo_ocorrencia', 'descricao')));

        $this->set(compact('status','tipos_periodo','retorno','tipo_arquivo', 'bancos'));
    }
    /**
	 * Metodo para buscar os pedidos que ainda não tem remessa de bancaria - boleto, para gerar o arquivo de remessa para o banco itau
	 * 
	 * @param: $deAte = data inicio e fim para listagem dos pedidos 
	 */ 
	public function listagem() 
	{
		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, $this->RemessaBancaria->name);
		if(!isset($filtros)){
			$filtros['data_inicio'] = date('d/m/Y');
			$filtros['data_fim'] =  date('d/m/Y');
		}
		$conditions = $this->RemessaBancaria->converteFiltroEmCondition($filtros);
		
		//metodo para pegar os relacionamentos e fields
		$relationsFields = $this->getRelationsFields();
    	
    	//pega os registros
		$this->paginate['RemessaBancaria'] = array(
									                'fields' => $relationsFields['fields'],
									                'conditions' => $conditions,
									                'joins' => $relationsFields['joins'],
									                'limit' => 50,
									                'order' => 'Cliente.nome_fantasia'
									        );
        $remessa_bancaria = $this->paginate('RemessaBancaria');
		//pega o valor total 
		$fieldsTotal = array('SUM(RemessaBancaria.valor_pago) as total_pago',
							'SUM(RemessaBancaria.valor_tarifa) as total_tarifa',
							'SUM(RemessaBancaria.valor_juros) as total_juros',
							'SUM(RemessaBancaria.valor) as total');
		$remessa_bancaria_total = $this->RemessaBancaria->find('first', array('joins' => $relationsFields['joins'],
																			'conditions' => $conditions,
																			'fields' => $fieldsTotal
																		)
																	);
		//seta as variaveis para pegar no html
		$this->set(compact('remessa_bancaria','status', 'remessa_bancaria_total'));
		
	}//fim listagem
	/**
	 * Metodo para pegar os relacionamento e os campos para apresentacao
	 */ 
	public function getRelationsFields()
	{
		//pega as remessa carregadas
    	$arrayRelationFields['joins'] = array(
    				array(
						'table' => "{$this->RemessaStatus->databaseTable}.{$this->RemessaStatus->tableSchema}.{$this->RemessaStatus->useTable}",
						'alias' => 'RemessaStatus',
						'conditions' => 'RemessaStatus.codigo = RemessaBancaria.codigo_remessa_status',
						'type' => 'INNER',
					),
					array(
						'table' => "{$this->RemessaRetorno->databaseTable}.{$this->RemessaRetorno->tableSchema}.{$this->RemessaRetorno->useTable}",
						'alias' => 'RemessaRetorno',
						'conditions' => 'RemessaRetorno.codigo = RemessaBancaria.codigo_remessa_retorno',
						'type' => 'LEFT',
					),
    				array(
						'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
						'alias' => 'Cliente',
						'conditions' => 'Cliente.codigo = RemessaBancaria.codigo_cliente',
						'type' => 'LEFT',
					),
					array(
						'table' => "{$this->Usuario->databaseTable}.{$this->Usuario->tableSchema}.{$this->Usuario->useTable}",
						'alias' => 'UsuarioRemessa',
						'conditions' => 'UsuarioRemessa.codigo = RemessaBancaria.codigo_usuario_inclusao',
						'type' => 'LEFT',
					),
					array(
						'table' => "{$this->Usuario->databaseTable}.{$this->Usuario->tableSchema}.{$this->Usuario->useTable}",
						'alias' => 'UsuarioRetorno',
						'conditions' => 'UsuarioRetorno.codigo = RemessaBancaria.codigo_usuario_retorno',
						'type' => 'LEFT',
					),
    			);
    	$arrayRelationFields['fields'] = array(
    			'RemessaBancaria.codigo',
    			'Cliente.codigo',
    			'Cliente.codigo_documento',
    			'Cliente.nome_fantasia',
    			'RemessaBancaria.codigo_pedido',
    			'RemessaBancaria.nosso_numero',
    			'RemessaBancaria.data_emissao',
    			'RemessaBancaria.data_vencimento',
    			'RemessaStatus.descricao',
    			'RemessaRetorno.codigo',
    			'RemessaRetorno.codigo_ocorrencia',
    			'RemessaRetorno.descricao',
    			'RemessaBancaria.valor',
    			'RemessaBancaria.data_pagamento',
    			'RemessaBancaria.valor_pago',
    			'RemessaBancaria.valor_tarifa',
    			'RemessaBancaria.valor_juros',
    			'UsuarioRemessa.nome',
    			'UsuarioRetorno.nome',
    			'RemessaBancaria.numero_inscricao',
    			'RemessaBancaria.nome_pagador',
    			'RemessaBancaria.codigo_banco',
    			
    		);
    	return $arrayRelationFields;
	}
	/**
	 * Metodo para editar os registros com retornos que não tem remessas
	 */ 
    public function editar() {
        $this->pageTitle = 'Editar Remessa/Retorno Bancária'; 
        if($this->RequestHandler->isPost()) {
    		$dados["RemessaBancaria"]['codigo'] = $this->data['RemessaBancaria']['codigo'];
    		$dados["RemessaBancaria"]['codigo_cliente'] = $this->data["RemessaBancaria"]['codigo_cliente'];
    		$dados["RemessaBancaria"]['data_emissao'] = $this->data["RemessaBancaria"]['data_emissao'];
    		//pago
    		$status = 1; //aguardando pagamento
    		$remessa = $this->RemessaBancaria->find('first',array('conditions' => array('RemessaBancaria.codigo' => $this->data['RemessaBancaria']['codigo'])));
    		//verifica qual o codigo de ocorrencia
    		if($remessa['RemessaBancaria']['codigo_remessa_retorno'] == '05' || $remessa['RemessaBancaria']['codigo_remessa_retorno'] == '06' || 
				$remessa['RemessaBancaria']['codigo_remessa_retorno'] == '07' || $remessa['RemessaBancaria']['codigo_remessa_retorno'] == '08' || 
				$remessa['RemessaBancaria']['codigo_remessa_retorno'] == '09') {
				$status = 2; //pago
			} else if($remessa['RemessaBancaria']['codigo_remessa_retorno'] == '03' || $remessa['RemessaBancaria']['codigo_remessa_retorno'] == '15' || 
				$remessa['RemessaBancaria']['codigo_remessa_retorno'] == '16' || $remessa['RemessaBancaria']['codigo_remessa_retorno'] == '17' || 
				$remessa['RemessaBancaria']['codigo_remessa_retorno'] == '18') { //cancelada
				$status = 3; //cancelado
			}
			//seta o status
    		$dados["RemessaBancaria"]['codigo_remessa_status'] = $status;//passa o titulo para pago
    		//valida se os campos estao vazios
    		$this->RemessaBancaria->validates();
    		if ($retorno = $this->RemessaBancaria->atualizar($dados)) {
    			//gera o pedido quando editar e o status for igual a pago
    			if($status == 2) { //pago
    				//gera o pedido
    				$this->RemessaBancaria->gerarPedido($this->data['RemessaBancaria']['codigo'], "EDICAO");
    			}//fim status
                $this->BSession->setFlash('save_success','Títulos atualizados com sucesso!');
            } 
            else {
                $this->BSession->setFlash('save_error');
                $this->redirect(array('action' => 'editar', 'controller' => 'remessa_bancaria', $this->data['RemessaBancaria']['codigo']));
            }
        	$this->redirect(array('action' => 'index', 'controller' => 'remessa_bancaria'));
			        	
        }
        if (isset($this->passedArgs[0])) {
        	
        	//metodo para pegar os relacionamentos e fields
			$relationsFields = $this->getRelationsFields();
            $this->data = $this->RemessaBancaria->find('first', 
            											array(	'joins' => $relationsFields['joins'],
            													'fields' => $relationsFields['fields'],
	            												'conditions' => 
	            														array('RemessaBancaria.codigo' => $this->passedArgs[0])
	            												)
            											);
        }//fim passedArgs
    }//fim editar
	/**
	 * Metodo para buscar os pedidos que ainda não tem remessa de bancaria - boleto, para gerar o arquivo de remessa para o banco itau
	 * 
	 * @param: $deAte = data inicio e fim para listagem dos pedidos 
	 */ 
	public function listagem_gerar_remessa() 
	{
		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, $this->RemessaBancaria->name);
		
    	//pega os pedidos
		$pedidos = $this->Pedido->getPedidosRemessaBancaria(null, $filtros['data_inicio'],$filtros['data_fim']);
		$meses   = Comum::listMeses();
		//seta as variaveis para pegar no html
		$this->set(compact('pedidos', 'meses'));
		
	}
	/**
	 * Metodo para gerar as remessas
	 */ 
	public function gerar_remessa()
	{
		//pega os pedidos para gerar a remessa
		$pedidosRemessas = $this->data['RemessaBancariaPedidos'];
		//gera os dados gravando na tabela e depois, gerando o arquivo
		$arquivo = $this->RemessaBancaria->gerarRemessa($pedidosRemessas);
		//implementar header location para baixar o arquivo
        $arquivo = basename( $arquivo );
        $path = TMP.DS;
        header("Content-Type: application/force-download");
        header("Content-type: application/octet-stream;");
    	header("Content-Length: " . filesize( $path ) );
    	header("Content-disposition: attachment; filename=" . $arquivo );
    	header("Pragma: no-cache");
    	header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
    	header("Expires: 0");
    	readfile( $path );
    	flush();
		
		$this->index();
	} //fim gerar_remessa
	/**
	 * Metodo para importar o arquivo de remessa para dentro do rhhealth
	 */ 
	public function importar_remessa()
	{
		//titulo da pagina na view
		$this->pageTitle = 'Remessa/Retorno Bancário';
		$this->RemessaBancaria->query('BEGIN TRANSACTION');
		try {
			//chama a tela de importacao
			if($this->RequestHandler->isPost()) {
				$remessa = $this->_upload($this->data['RemessaBancaria']['remessa']);
				//verifica se o retorno subiu
				if($remessa) {
					//varre o arquivo
					$mensagens = $this->RemessaBancaria->lerArquivo($this->arquivo->file_dst_pathname,null,'remessa');
					
					$this->RemessaBancaria->commit();
					
					$this->BSession->setFlash(array('alert alert-success', 'Arquivo de remessa carregado com sucesso.'));
					$this->set(compact('mensagens'));
					unlink($this->arquivo->file_dst_pathname);


				} else {
					$this->BSession->setFlash(array('alert alert-error', 'Erro ao carregar o arquivo.'));
				}
				// $this->redirect(array('action' => 'index'));
			}
		}
		catch(Exception $e) {
			// print "vish: ".$e->getMessage();
			$this->log('REMESSA_BANCARIA (IMPORTACAO): ' . $e->getMessage(), 'debug');
			
			$this->RemessaBancaria->rollback();
			
			unlink($this->arquivo->file_dst_pathname);
			

			$this->BSession->setFlash(array('alert alert-error', $e->getMessage()));
			// $this->redirect(array('action' => 'index'));
			
		}
		
	} //fim importar_remessa
	/**
	 * Metodo para importar o arquivo de retorno para dentro do rhhealth
	 */ 
	public function importar_retorno()
	{
		//titulo da pagina na view
		$this->pageTitle = 'Remessa/Retorno Bancário';

		//pega todos os bancos do banco de dados NavegNatec
		$naveg_banco = $this->Banco->find('all', array('order' => array('codigo','banco','agencia','conta','descricao')));
		$banco = array();
		//arruma o combo
		foreach($naveg_banco as $bco) {

			//monta a descricao
			$descricao_banco = $bco['Banco']['codigo'] .' - '. $bco['Banco']['banco'];

			$agencia = trim($bco['Banco']['agencia']);
			if(!empty($agencia) && $agencia != "") {
				$descricao_banco .= ' - '. $agencia;
			}

			$conta = trim($bco['Banco']['conta']);
			if(!empty($conta) && $conta != "") {
				$descricao_banco .= ' - '. $conta;
			}

			$descricao_banco .= ' - '. $bco['Banco']['descricao'];

			//monta ao combo
			$banco[$bco['Banco']['codigo']] = $descricao_banco;

		}//fim foreach

		$this->set(compact('banco'));

		//chama a tela de importacao
		try {
			
			$this->RemessaBancaria->query('BEGIN TRANSACTION');

			//chama a tela de importacao
			if($this->RequestHandler->isPost()) {
				//pega o codigo do banco que foi setado na tela de importacao
				$codigo_banco = $this->data['RemessaBancaria']['codigo_banco'];

				if(empty($codigo_banco)) {
					$this->BSession->setFlash(array('alert alert-error', 'Favor selecionar um banco.'));
				} 
				else {
					$retorno = $this->_upload($this->data['RemessaBancaria']['retorno']);
					//verifica se o retorno subiu
					if($retorno) {

						//varre o arquivo
						$mensagens = $this->RemessaBancaria->lerArquivo($this->arquivo->file_dst_pathname, $codigo_banco, 'retorno');
						
						$this->RemessaBancaria->commit();

						$this->BSession->setFlash(array('alert alert-success', 'Arquivo de retorno carregado com sucesso.'));
						$this->set(compact('mensagens'));
						unlink($this->arquivo->file_dst_pathname);


					} else {					
						$this->BSession->setFlash(array('alert alert-error', 'Erro ao carregar o arquivo.'));
					}
					
				}//fim empty codigo_banco

			} //fim handler post


		}catch(Exception $e) {
				
			$this->log('RETORNO_BANCARIO (IMPORTACAO): ' . $e->getMessage(), 'debug');
			
			$this->RemessaBancaria->rollback();

			unlink($this->arquivo->file_dst_pathname);

			$this->BSession->setFlash(array('alert alert-error', $e->getMessage()));
		}
	} //fim importar_retorno
	/**
	 * Funcao para carregar o arquivo
	 */ 
	function _upload($file) {
		require_once APP . 'vendors' . DS . 'class.upload.php';
		$this->arquivo = new Upload($file);
		if ($this->arquivo->uploaded) {
			// save uploaded image with no changes
			$this->arquivo->Process($_SERVER['DOCUMENT_ROOT'] . '/portal/app/webroot/files/arquivos/');
			if ($this->arquivo->processed) {
				$this->arquivo->file_new_name_body = $file["name"];
				$this->arquivo->Process($_SERVER['DOCUMENT_ROOT'] . '/portal/app/webroot/files/arquivos/');
				if ($this->arquivo->processed) {
					$this->arquivo->Clean();
					
					return true;
				} else {
					return false;
				}//fim imagem->processed
				
			} else {
				return false;
			}//fim imagem->processed
		}//fim imagem->uploaded
		return false;
	}//fim upload	
	/**
	 * Metodo para exportar os dados de remessa bancaria 
	 */ 
	public function exportar_dados()
	{ 
		
		//pega os filtros
		$this->data['RemessaBancaria'] = $this->Filtros->controla_sessao($this->data, 'RemessaBancaria');
		//verifica se pelo menos a data de inicio e fim estao preenchidas
        if(!empty($this->data['RemessaBancaria']['data_inicio']) && !empty($this->data['RemessaBancaria']['data_fim'])) {
			//monta os filtros
        	$conditions = $this->RemessaBancaria->converteFiltroEmCondition($this->data['RemessaBancaria']);
			//metodo para pegar os relacionamentos e fields
			$relationsFields = $this->getRelationsFields();
	    	//pega os registros
			$remessa_bancaria = $this->RemessaBancaria->find('all',array(	'fields' => $relationsFields['fields'],
																			'conditions' => $conditions,
																			'joins' => $relationsFields['joins'],
																			'order' => 'Cliente.nome_fantasia'
																		));
			//mota o nome do arquivo e o header para baixar o arquivo
            $nome_arquivo = date('YmdHis').'exportar_remessa_retorno_dados.csv';
            ob_clean();
            header('Content-Encoding: UTF-8');
            header('Content-type: text/csv; charset=UTF-8');
            header(sprintf('Content-Disposition: attachment; filename="%s"', $nome_arquivo));
            header('Pragma: no-cache');
            echo utf8_decode('"Matricula do Funcionario";"CPF/CNPJ";"Nome do Funcionario";"Banco";"Nosso Numero";"Data da Emissão";"Data do Vencimento";"Data do Pagamento";"Status";"Status Retorno";"Usuário Remessa";"Usuário Retorno";"Valor Juros/Multa";"Valor Tarifa";"Valor Pago";"Valor Principal";'."\n");
            //verifica se tem registro para exportar
            if(!empty($remessa_bancaria)) {
            	//varre os dados para montar o arquivo 
	            foreach ($remessa_bancaria as $key => $dado) {
	            	//monta a linha para inserir no arquivo
	            	//pega os dados do arquivo
	            	//dados do cliente
	            	$linha  = $dado["Cliente"]["codigo"].";";
	            	$linha .= $dado["Cliente"]["codigo_documento"].";";
	            	$linha .= $dado["Cliente"]["nome_fantasia"].";";
	            	$linha .= "'".$dado["RemessaBancaria"]["codigo_banco"]."';";
	            	$linha .= $dado["RemessaBancaria"]["nosso_numero"].";";
	            	$linha .= $dado["RemessaBancaria"]["data_emissao"].";";
	            	$linha .= $dado["RemessaBancaria"]["data_vencimento"].";";
	            	$linha .= $dado["RemessaBancaria"]["data_pagamento"].";";
	            	$linha .= $dado["RemessaStatus"]["descricao"].";";
	            	$linha .= $dado["RemessaRetorno"]["codigo_ocorrencia"]."-".$dado["RemessaRetorno"]["descricao"].";";
	            	$linha .= $dado["UsuarioRemessa"]["nome"].";";
	            	$linha .= $dado["UsuarioRetorno"]["nome"].";";
	            	$linha .= $dado["RemessaBancaria"]["valor_juros"].";";
	            	$linha .= $dado["RemessaBancaria"]["valor_tarifa"].";";
	            	$linha .= $dado["RemessaBancaria"]["valor_pago"].";";
	            	$linha .= $dado["RemessaBancaria"]["valor"].";";
	            	
	                $linha .= "\n";
	                echo utf8_decode($linha);
	            }//fim foreach
	        }//fim verificacao empty
	        exit; 
	    }  //verifica se tem pelo menos o filtro de data inicio/fim
	       
	} //fim exportar_dados
	
}//fim class