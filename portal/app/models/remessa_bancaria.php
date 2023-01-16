<?php
class RemessaBancaria extends AppModel {
	var $name		  = 'RemessaBancaria';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable	  = 'remessa_bancaria';
	var $primaryKey	= 'codigo';
	var $actsAs		= array('Secure');
	var $validate = array(
		'codigo_cliente' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Código do Cliente!',
			'required' => true
			),	
		'data_emissao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Data de Emissão!',
			'required' => true
			)
		);
    //atributos da class
    public $linha_header = '';
    public $linha_detalhes = '';
    public $linha_trailler = '';
    public $brancos = '';
    public $zeros = 0;
    public $agencia = "367";
	public $conta = "11441";
	public $digito = "0";
    public $numero_seq = 0;
    public $numero_arquivo = '';
    public $mensagens = '';
    public $cont_titulo_existente = 0;
	public $cont_novo_titulo = 0;
	public $cont_cliente_erro = 0;
	public $cont_titulo_baixado = 0;
	public $cont_titulo_cancelado = 0;
	public $cont_titulo_nao_encontrados = 0;
	public $cont_titulo_atualizado = 0;
	public $cont_pedido_criado = 0;
	public $cont_pedido_erro = 0;
	public $cont_pedido_ja_existente = 0;
	public $linha_pedido_erro = "";
	public $linha_pedido_existe = "";
	public $codigos_remessas_bancarias = array();
	public $codigo_banco = null;
	public $array_remessa = array();
	public $linhasPQR = "";
	public $linhasTU = "";
	public $sequencial = "";
	public $naveg_codigo_banco = null;

	function converteFiltroEmCondition($data) {
		$conditions = array();
		// if (!empty($data['data_inicial'])) {
		// 	$conditions['RemessaBancaria.data_inclusao'] =  $data['data_inclusao'];
		// }
		// pr($data);exit;
		if(!isset($data["tipos_periodo"])) {
			$data["tipos_periodo"] = "I";
		}
		if(!empty($data["data_inicio"])) {
			switch ($data["tipos_periodo"]) {
				case 'I'://data de insercao
					$conditions['RemessaBancaria.data_inclusao >= '] = AppModel::dateToDbDate($data["data_inicio"]);	
					break;
				case 'E'://data de emissao
					$conditions['RemessaBancaria.data_emissao >= '] = AppModel::dateToDbDate($data["data_inicio"]);	
					break;
				case 'V'://data de vencimento
					$conditions['RemessaBancaria.data_vencimento >= '] = AppModel::dateToDbDate($data["data_inicio"]);	
					break;
				case 'P'://data de pagamento
					$conditions['RemessaBancaria.data_pagamento >= '] = AppModel::dateToDbDate($data["data_inicio"]);	
					break;
			}//switch
		}//fim if
		if(!empty($data["data_fim"])) {
			switch ($data["tipos_periodo"]) {
				case 'I'://data de insercao
					$conditions['RemessaBancaria.data_inclusao <= '] = AppModel::dateToDbDate($data["data_fim"]);	
					break;
				case 'E'://data de emissao
					$conditions['RemessaBancaria.data_emissao <= '] = AppModel::dateToDbDate($data["data_fim"]);	
				break;
				case 'V'://data de vencimento
					$conditions['RemessaBancaria.data_vencimento <= '] = AppModel::dateToDbDate($data["data_fim"]);	
					break;
				case 'P'://data de pagamento
					$conditions['RemessaBancaria.data_pagamento <= '] = AppModel::dateToDbDate($data["data_fim"]);	
					break;
			}//switch
		}
		if(!empty($data["codigo_remessa_status"])) {
			$conditions['RemessaBancaria.codigo_remessa_status'] = $data['codigo_remessa_status'];
		}
		if(!empty($data["codigo_remessa_retorno"])) {
			$conditions['RemessaBancaria.codigo_remessa_retorno'] = $data['codigo_remessa_retorno'];
		}
		if(!empty($data["codigo_cliente"])) {
			$conditions['RemessaBancaria.codigo_cliente'] = $data['codigo_cliente'];
		}
		if(!empty($data["codigo_banco"])) {
			$conditions['RemessaBancaria.codigo_banco'] = $data['codigo_banco'];
		}
		if(!empty($data["tipo_arquivo"])) {
			if($data['tipo_arquivo'] == 'REM') {
				$conditions['not']['RemessaBancaria.linha_remessa'] = null;
			} else if($data['tipo_arquivo'] == 'RET') {
				$conditions['not']['RemessaBancaria.linha_retorno'] = null;
			}
		}
        
		return $conditions;
	}
	/**
	 * Metodo para gerar as remessas
	 */ 
	public function gerarRemessa($pedidosRemessas = array())
	{	
		//verifica se existe registro
		if(!empty($pedidosRemessas)) {
			//armazena os pedidos
			$array_codigo_pedidos = array();
			//varre as remessas
			foreach ($pedidosRemessas as $codigo_pedido => $val){
				//verifica
				if($codigo_pedido == "todos_select"){
					continue;
				} 
				if($codigo_pedido == "data_vencimento") {
					continue;
				}
				//monta a conditions
				$array_codigo_pedidos[] = $codigo_pedido;
				
			}//fim foreach
			//dados da remessa
			$Pedido	= &classRegistry::init('Pedido');
			$dadosRemessa = $Pedido->getDadosRemessa($array_codigo_pedidos);
			
			//pega a data de vencimento e formata
			$dataFormatada = explode("/",$pedidosRemessas["data_vencimento"]);
			$dadosRemessa["data_vencimento"] = $dataFormatada[0].$dataFormatada[1].substr($dataFormatada[2], 2);
			//montar o header do arquivo
			$this->gerarHeader();
			//monta os detalhes
			$this->gerarDetalhesBoleto($dadosRemessa);
			//monta o trailler
			$this->gerarTrailler();
			//monta o arquivo;
			$arquivo = $this->gerarArquivo();
			return $arquivo;
		} //FIM if empty
		
	} //fim gerar_remessa
	/**
	 * metod para gerar o numero sequencial do arquivo
	 */
	public function geraNumeroSeq()
	{	
		
		//gera numero sequencial do arquivo
		$this->numero_seq++;
	}//fim geraNumeroSeqHeader
	/**
	 * Metodo para montar o header da remessa
	 * 
	 * @param: array com os dados da remessa 
	 */ 
	private function gerarHeader($dados = array())
	{
		$dados = 1;
		//verifica se dados esta vazio
		if(!empty($dados)) {
			
			
			$nome_empresa = 'RH HEALTH';
			//monta a linha do header seta os valores
			//tipo de registro 		1-1
			$this->linha_header .= "0";
			//operação 				2-2
			$this->linha_header .= "1";
			//literal de remessa 	3-9
			$this->linha_header .= "REMESSA";
			//codigo do servico 	10-11
			$this->linha_header .= "01";
			//litaral de servico 	12-26
			$this->linha_header .= str_pad('COBRANCA', 15);
			//agencia 27-30
			$this->linha_header .= str_pad($this->agencia, 4, 0, STR_PAD_LEFT);
			//brancos 31-32
			$this->linha_header .= str_pad("0", 2, 0, STR_PAD_LEFT);
			//conta 33-37
			$this->linha_header .= str_pad($this->conta, 5, 0, STR_PAD_LEFT);
			//dac 38-38 (digito de conferencia da conta)
			$this->linha_header .= str_pad($this->digito, 1, 0, STR_PAD_LEFT);
			//brancos 39-46
			$this->linha_header .= str_pad($this->brancos, 8);
			//nome da empresa 47-76
			$this->linha_header .= str_pad($nome_empresa, 30);
			//codigo do banco 77-79
			$this->linha_header .= "341";
			//nome do banco 80-94
			$this->linha_header .= str_pad("BANCO ITAU SA", 15);
			//data da geracao 95-100
			$this->linha_header .= str_pad(date('dmy'), 6,' ');
			//brancos 101-394
			$this->linha_header .= str_pad($this->brancos, 294,' ');
			//numero sequencial 395-400
			$this->geraNumeroSeq();
			$this->linha_header .= str_pad($this->numero_seq, 6,0, STR_PAD_LEFT);
		} //fim valor vazio do header
	} //fim gerarHeader
		
	/**
	 * Metodo para gerar os detalhes do boleto para enviar para o banco
	 */ 
	private function gerarDetalhesBoleto($dados)
	{
		//seta a data de vencimento
		$data_vencimento = $dados["data_vencimento"];
		//pega o proximo numero do arquivo
		$this->numero_arquivo = $this->getProximaRemessa();
		//varre os dados para montar a linha
		foreach ($dados as $value) {
			//seta o novo valor
			$dado = $value[0];
			
			if(!is_array($dado)) {
				continue;
			}
			//monta a linha dos detalhes de remessa
			
			//tipo de registro 1-1
			$this->linha_detalhes .= "1";
			
			//codigo de inscricao 2-3
			$this->linha_detalhes .= str_pad('02', 2,0, STR_PAD_LEFT);
			//numero de inscricao 4-17
			$this->linha_detalhes .= str_pad('20183726000114', 14,0, STR_PAD_LEFT);
			//agencia mantedora da conta 18-21
			$this->linha_detalhes .= str_pad($this->agencia, 4,0, STR_PAD_LEFT);
			//zeros complementos 22-23
			$this->linha_detalhes .= str_pad($this->zeros, 2,0, STR_PAD_LEFT);
			//conta mantedora 24-28
			$this->linha_detalhes .= str_pad($this->conta, 5,0, STR_PAD_LEFT);
			//dac 29-29
			$this->linha_detalhes .= str_pad($this->digito, 1,0, STR_PAD_LEFT);
			//brancos 30-33
			$this->linha_detalhes .= str_pad($this->brancos, 4);
			//instrucao/alegação 34-37
			$this->linha_detalhes .= str_pad($this->zeros, 4,0, STR_PAD_LEFT);
			
			//uso da empresa 38-62
			$uso_empresa = $this->brancos;
			$this->linha_detalhes .= str_pad($uso_empresa, 25);
			//nosso numero 63-70
			$nosso_numero = substr($dado["codigo_pedido"],0,8);
			$this->linha_detalhes .= str_pad($nosso_numero, 8,0,STR_PAD_LEFT);
			//qtd de moeda 71-83
			$this->linha_detalhes .= str_pad($this->zeros, 13,0,STR_PAD_LEFT);
			//numero da carteira 84-86
			$carteira = '109';
			$this->linha_detalhes .= str_pad($carteira, 3,0,STR_PAD_LEFT);
			//USO DO BANCO 87-107
			$this->linha_detalhes .= str_pad($this->brancos, 21);
			//CARTEIRA 108-108
			$this->linha_detalhes .= str_pad("I", 1);
			//CÓD. DE OCORRÊNCIA 109-110
			$this->linha_detalhes .= str_pad('01', 2,0,STR_PAD_LEFT);
			//Nº DO DOCUMENTO 111-120
			/****
			AQUI DEVE SER A NOTA FISCAL
			****/
			$numero_doc = $dado['codigo_pedido'];
			$this->linha_detalhes .= str_pad($numero_doc,10);
			//VENCIMENTO 121-126
			$this->linha_detalhes .= str_pad($data_vencimento, 6,0,STR_PAD_LEFT);
			
			//trata o valor
			$valor_total = str_replace(".", "", $dado['valor_total']);
			//valor do  127-139
			$this->linha_detalhes .= str_pad($valor_total, 13,0,STR_PAD_LEFT);
			//CÓDIGO DO BANCO 140-142
			$this->linha_detalhes .= str_pad('341', 3,0,STR_PAD_LEFT);
			//AGÊNCIA COBRADORA 143-147
			$this->linha_detalhes .= str_pad($this->zeros, 5,0,STR_PAD_LEFT);
			//ESPÉCIE 148-149
			$especie = '06';
			$this->linha_detalhes .= str_pad('02', 2);
			//aceite 150-150 nao aceite
			$this->linha_detalhes .= str_pad('N', 1);
			//DATA DE EMISSÃO 151-156
			$this->linha_detalhes .= str_pad(date('dmy'), 6, 0, STR_PAD_LEFT);
			//INSTRUÇÃO 1 157-158
			$instrucao1 = '52';
			$this->linha_detalhes .= str_pad($instrucao1, 2);
			//INSTRUÇÃO 2 159-160
			$instrucao2 = '00';
			$this->linha_detalhes .= str_pad($instrucao2, 2);
			//JUROS DE 1 DIA 161-173
			$juros = '35';
			$this->linha_detalhes .= str_pad($juros, 13, 0, STR_PAD_LEFT);
			//DESCONTO ATÉ 174-179
			$this->linha_detalhes .= str_pad($this->zeros, 6, 0, STR_PAD_LEFT);
			//VALOR DO DESCONTO 180-192
			$this->linha_detalhes .= str_pad($this->zeros, 13, 0, STR_PAD_LEFT);
			//VALOR DO I.O.F. 193-205
			$this->linha_detalhes .= str_pad($this->zeros, 13, 0, STR_PAD_LEFT);
			//ABATIMENTO 206-218
			$this->linha_detalhes .= str_pad($this->zeros, 13, 0, STR_PAD_LEFT);
			//verificar o numero da inscricao
			$tipo = "02";
			if(comum::validarCPF($dado["cpf_cnpj"])) {
				$tipo = "01";
			}
			//codigo de inscricao 219-220
			$this->linha_detalhes .= str_pad($tipo, 2,0, STR_PAD_LEFT);
			
			//numero de inscricao 221-234
			$this->linha_detalhes .= str_pad(substr($dado["cpf_cnpj"],0,14), 14,0, STR_PAD_LEFT);
			//nome do pagador 
			$nome = comum::tirarAcentos(substr($dado["nome"],0,30));
			$this->linha_detalhes .= str_pad($nome, 30);
			//brancos
			$this->linha_detalhes .= str_pad($this->brancos, 10);
			//logradouro 
			$endereco = comum::tirarAcentos(substr($dado["endereco"].", ".$dado['numero']." ".$dado['complemento'],0,40));
			$this->linha_detalhes .= str_pad($endereco, 40);
			//BAIRRO 
			$bairro = comum::tirarAcentos(substr($dado["bairro"],0,12));
			$this->linha_detalhes .= str_pad($bairro, 12);
			//CEP  
			$this->linha_detalhes .= str_pad(substr($dado["cep"],0,8), 8,0, STR_PAD_LEFT);
			//CIDADE   
			$cidade = comum::tirarAcentos(substr($dado["cidade"],0,15));
			$this->linha_detalhes .= str_pad($cidade, 15);
			//ESTADO    
			$this->linha_detalhes .= str_pad(substr($dado["estado"],0,2), 2);
			//sacador avalista
			$this->linha_detalhes .= str_pad($this->brancos, 30);
			//brancos
			$this->linha_detalhes .= str_pad($this->brancos, 4);
			//data de mora
			$this->linha_detalhes .= str_pad($this->zeros, 6,0, STR_PAD_LEFT);
			//prazo
			$this->linha_detalhes .= str_pad($this->zeros, 2,0, STR_PAD_LEFT);
			//brancos
			$this->linha_detalhes .= str_pad($this->brancos, 1);
			//numero sequencial 395-400
			$this->geraNumeroSeq();
			$this->linha_detalhes .= str_pad($this->numero_seq, 6,0, STR_PAD_LEFT);
			$this->linha_detalhes .= "\n";
			
			//grava os dados no banco na remessa_bancaria
			$remessaBancaria['codigo_pedido'] 			= $dado["codigo_pedido"]; //codigo do pedido
			$remessaBancaria['codigo_remessa_status'] 	= '1'; //aguardando retorno
			$remessaBancaria['numero_remessa'] 			= $this->numero_arquivo; //numero da remessa
			$remessaBancaria['uso_empresa'] 			= $uso_empresa;
			$remessaBancaria['nosso_numero'] 			= $nosso_numero;
			$remessaBancaria['numero_carteira'] 		= $carteira;
			$remessaBancaria['numero_documento'] 		= trim($numero_doc);
			$remessaBancaria['data_vencimento'] 		= $dados["data_vencimento"];
			$remessaBancaria['valor'] 					= $dado['valor_total'];
			$remessaBancaria['instrucao_1'] 			= $instrucao1;
			$remessaBancaria['instrucao_2'] 			= $instrucao2;
			$remessaBancaria['juros_1_dia'] 			= $juros;
			$remessaBancaria['tipo_inscricao'] 			= $tipo;
			$remessaBancaria['numero_inscricao'] 		= $dado["cpf_cnpj"];
			$remessaBancaria['nome_pagador'] 			= $nome;
			$remessaBancaria['numero_sequencial'] 		= $this->numero_seq;
			//seta a remessabancaria na base de dados
			$this->gravaRemessaBancaria($remessaBancaria);
		}//fim foreach
		// print $this->linha_detalhes."<br>";
		// exit;
	}//gerarDetalhesBoleto
	/**
	 * Metod para gravar os dados na tabela
	 * 
	 */
	public function gravaRemessaBancaria($remessa)
	{	
		//verifica se ja existe o registro
		$rem = $this->find('first',
									array('conditions' => 
										array('codigo_pedido' => $remessa["codigo_pedido"])
									)
							);
		//verifica se existe o codigo cadastrado
		if(empty($rem)) {
			//insere no banco
			parent::incluir($remessa);	
		}
		
	} //fim gravarBoleto
	/**
	 * Metodo para gerar o trailler do arquivo
	 */ 
	private function gerarTrailler()
	{
		
		//monta com os dados
		//tipo de registro 1-1
		$this->linha_trailler .= "9";
		//brancos 2-394
		$this->linha_trailler .= str_pad($this->brancos, 393,' ');
		//numero sequencial 395-400
		$this->geraNumeroSeq();
		$this->linha_trailler .= str_pad($this->numero_seq, 6,0, STR_PAD_LEFT);
	} //fim gerarTrailler
	/**
	 * Gera a proxima remessa
	 */ 
	public function getProximaRemessa()
	{
		//vai na base e pega o proximo codigo
		 $result = $this->find('first',
										array( 
											'fields' => array('COALESCE(MAX(RemessaBancaria.numero_remessa), 0) + 1 as proximo'),
										) 
								);
		return $result[0]["proximo"];
	} //fim getProximaRemessa
	
	/**
	 * Metodo para gerar arquivo
	 */ 
	private function gerarArquivo()
	{
		//caminho onde será gerado o arquivo para realizar o download
		$path = TMP.DS;
		$arquivo = $path.'REMESSA_'.$this->numero_arquivo.".REM";
		
		//monta o arquivo
		$dados = "";
		$dados .= $this->linha_header."\n";
		$dados .= $this->linha_detalhes;
		$dados .= $this->linha_trailler."\n";
		//escreve os dados em um arquivo
		file_put_contents($arquivo, $dados);
		return $arquivo;
	} //fim gerarArquivo
	/**
	 * Metodo para pegar os pedidos que ainda nao foram emitidos uma remessa
	 */
	public function getPedidosRemessaBancaria($cliente = null, $de = null,$ate = null)
	{
		//pega os relacinamentos para fazer a query
		$joins = $this->getQueryJoinsPedidoRemessa();
		//trabalha o resultado
		$result = $this->find('all', array(
			'fields' => array(
				'Pedido.codigo',
				'Pedido.codigo_cliente_pagador',
				'Pedido.mes_referencia',
				'Pedido.ano_referencia',
				'Pedido.data_inclusao',
				'CondPag.descricao',
				'sum(ItemPedido.valor_total) as valor_total',
			),
			'joins' => $joins,
			'conditions' => array(	'Pedido.manual'=>1,
									// 'Pedido.codigo_cliente_pagador'=>$cliente,
									'Endereco.descricao IS NOT NULL' ,
									'Pedido.data_inclusao >=' => AppModel::dateToDbDate($de),
									'Pedido.data_inclusao <=' => AppModel::dateToDbDate($ate)
								),
			'group' => 'Pedido.codigo, 
						Pedido.codigo_cliente_pagador,
						Pedido.mes_referencia,
						Pedido.ano_referencia,
						Pedido.data_inclusao,
						CondPag.descricao'
		));
		
		//retorna o resultado
		return $result;
	}//fim getPedidosRemessaBancaria
	/**
	 * Metodo para ler o arquivo
	 */ 
	public function lerArquivo($arquivo, $cod_bco_naveg = null, $tipo=null) 
	{
		//tempo ilimitado
		ini_set('max_execution_time',0);

		//verifica se nao esta nulo o parametro
		if(!is_null($cod_bco_naveg)) {
			//seta o codigo do banco naveg selecionadod
			$this->naveg_codigo_banco = $cod_bco_naveg;
		} //fim codigo_bacno naveg


		//abre o arquivo para leitura
		$file = fopen($arquivo,"r");
		//tipo de operação remessa =1, ou retorno=2
		$operacao 	= "";
		$header 	= "";
		$trailler 	= "";
		$contadorLinha = 1;
		//para validar qual tipo do arquivo
		$arquivoRemessa=false;
		$arquivoRetorno=false;

		$tamanho_linha = false;

		//limpa a variavel
		$retorno_seg_p = "";

		//varre todo o arquivo
		while(!feof($file)) {
			//le as linhas do arquivo
			$linha = fgets($file, 4096);
			
			if(empty($linha)) {
				continue;
			}

			//valida o tamanho da linha
			//implementacao para leitura do arquivo de 400 e 240 posições
			if(strlen($linha) == '402') {

				$tamanho_linha = strlen($linha);

				//tipo da linha para o arquivo de 400 posições
				$tipo_registro = substr($linha,0,1);
				//para saber qual é o bloco 
				switch ($tipo_registro) {
					case '0': //header
						//ve se é remessa ou retorno
						$operacao = substr($linha,1,1);
						//pega qual o banco
						$arquivo_codigo_banco = substr($linha, 76, 3); //77-79 codigo do banco 341/033
						$this->codigo_banco = $arquivo_codigo_banco; //seta o codigo que esta no banco de dados
						//seta o header para armazenar
						//$header = $linha;
						break;
					case '1': //detalhe
						//verifica qual é o tipo de operacao
						if($operacao == 1) { //remessa

							if($tipo != 'remessa') {
								throw new Exception('IMPORTE SOMENTE ARQUIVOS DE RETORNO.');
							}

							$this->lerRemessa($linha, $arquivo);
							$arquivoRemessa=true;
						} else if($operacao == 2) { //retorno

							if($tipo != 'retorno') {
								throw new Exception('IMPORTE SOMENTE ARQUIVOS DE REMESSA.');
							}
							
							if(is_null($cod_bco_naveg)){
								throw new Exception('FAVOR SELECIONE UM BANCO PARA INTEGRAR CORRETAMENTE COM O NAVEG.');
							}


							$this->lerRetorno($linha, $arquivo);
							$arquivoRetorno=true;
						}
						break;
				}//fim switch

			} else if(strlen($linha) == '242') {

				$tamanho_linha = strlen($linha);

				//tipo da linha para o arquivo de 240 posições
				$tipo_registro = substr($linha,7,1); //08-08 tipo da linha sendo lida
				
				//para saber qual é o bloco 
				switch ($tipo_registro) {
					case '0': //header
						//ve se é remessa ou retorno
						$operacao = substr($linha,142,1); //143-143 se é remessa 1 ou retorno 2
						break;

					case '1': //header de lote
						
						//pega qual o banco
						$arquivo_codigo_banco = substr($linha, 0, 3); //1-3 codigo do banco 033
						$this->codigo_banco = $arquivo_codigo_banco; //seta o codigo que esta no banco de dados

						break;

					case '3': //detalhe						
						//verifica qual é o tipo de operacao
						if($operacao == 1) { //remessa

							if($tipo != 'remessa') {
								throw new Exception('IMPORTE SOMENTE ARQUIVOS DE RETORNO.');
							}

							//verificar qual segmento esta lendo
							$segmento = substr($linha, 13,1);//14-14 tipo do segmento P/Q/R/S
							
							//verifica qual o segmento para disparar o metodo corretamente
							if($segmento == 'P') {
								//limpa a linha que vai guardar no banco de dados
								$this->linhasPQR = "";
								//metodo para buscar os dados do segmento P
								$retorno_seg_p = $this->setSegP($linha,$arquivo);
							} else if($segmento == 'Q') {
								
								//verifica se existe o nosso numero na base de dados
								if($retorno_seg_p != 'titulo_exitente') {
									//metodo para buscar os dados do segmento Q
									$this->setSegQ($linha,$arquivo);
								}

							} else if($segmento == 'R') {
								
								//verifica se existe o nosso numero na base de dados
								if($retorno_seg_p != 'titulo_exitente') {
									//metodo para buscar os dados do segmento Q
									$this->setSegR($linha,$arquivo);
								}

								$this->gravaRemessa240($arquivo);

							//} else if($segmento == 'S') {
							} //fim verificacao do segmento

							$arquivoRemessa=true;

							// $this->gravaRemessa240($arquivo);

						} 
						else if($operacao == 2) { //retorno

							if($tipo != 'retorno') {
								throw new Exception('IMPORTE SOMENTE ARQUIVOS DE REMESSA.');
							}
							
							if(is_null($cod_bco_naveg)){
								throw new Exception('FAVOR SELECIONE UM BANCO PARA INTEGRAR CORRETAMENTE COM O NAVEG.');
							}

							//verificar qual segmento esta lendo
							$segmento = substr($linha, 13,1);//14-14 tipo do segmento T/U

							//verifica qual o segmento para disparar o metodo corretamente
							if($segmento == 'T') {
								
								//limpa a linha que vai guardar no banco de dados
								$this->linhasTU = "";
								
								//metodo para buscar os dados do segmento T
								$this->setSegT($linha,$arquivo);

							} else if($segmento == 'U') {
								

								//metodo para buscar os dados do segmento Q
								$this->setSegU($linha,$arquivo);

								$this->gravaRetorno240($arquivo);

							}//fim segmento retorno

							$arquivoRetorno=true;
						}
						
						break;
					
				}//fim switch

			} else {
				throw new Exception('Arquivo fora dos Layouts preparados para realizar a carga. Linha '.$contadorLinha.' diferente de 400 ou 240 posições!');
			} //fim tamanho da linha
			$contadorLinha++;

		}//fim while arquivo

		if(!empty($this->codigos_remessas_bancarias)) {
			//varres os ids
			foreach($this->codigos_remessas_bancarias as $codigo_remessa => $arquivo){				
				$this->gerarPedido($codigo_remessa,$arquivo);
			}
		}

		$totalLinha = ($contadorLinha-3);		
		

		//verifica se é o santander
		if($this->codigo_banco == "033") {
			if($operacao == 1) {
				/*
				quando for remessa
				subtrai as linhas de header, header de lote, trailler de lote, trailler e a ultima que é em branco
				divide por 3 pois geralmente tem 3 segmentos.
				*/
				$totalLinha = ($contadorLinha-5) / 3; 
			} else {
				/*
				quando for remessa
				subtrai as linhas de header, header de lote, trailler de lote, trailler e a ultima que é em branco
				divide por 2 pois geralmente tem 2 segmentos.
				*/
				$totalLinha = ($contadorLinha-5) / 2; 
			}
		}
		$this->mensagens = "Lido ".$totalLinha." títulos do arquivo.<br>";
		
		if($arquivoRemessa) {
			$this->mensagens .= "Total de ".$this->cont_novo_titulo." novos títulos cadastrados.<br>";

			if(!empty($this->cont_titulo_existente)) {
				$this->mensagens .= "Total de ".$this->cont_titulo_existente." títulos já carregados.<br>";
			}

			if(!empty($this->cont_cliente_erro)) {
				$this->mensagens .= "Total de ".$this->cont_cliente_erro." clientes e títulos não cadastrados, pode haver CPF/CNPJ inválido(s).<br>";
			}
		} 
		if($arquivoRetorno) {
			
			//$this->mensagens .= "Total de ".$this->cont_novo_titulo." novos títulos que 'Possui Pendência'.<br>";
			$this->mensagens .= "Total de ".$this->cont_titulo_baixado." títulos baixados.<br>";
			$this->mensagens .= "Total de ".$this->cont_titulo_atualizado." títulos atualizados.<br>";
			$this->mensagens .= "Total de ".$this->cont_titulo_nao_encontrados." títulos de RETORNOS não encontrados.<br>";
			$this->mensagens .= "Total de ".$this->cont_pedido_criado." Pedidos criados.<br>";
			$this->mensagens .= "Total de ".$this->cont_pedido_ja_existente." Pedidos já vinculados a uma remessa.<br>";
			$this->mensagens .= "Total de ".$this->cont_pedido_erro." ERROs ao criar Pedidos.<br>";
			$this->mensagens .= "Total de ".$this->cont_titulo_cancelado." títulos cancelados.<br>";
		}
				
		//fecha o arquivo
		fclose($file);
		//retorna as mensagens de carregamento do arquivo
		return $this->mensagens;
	} //fim ler_arquivo

	/**
	 * Metodo para ler a remessa e inserir no banco de dados
	 */
	public function lerRemessa($linha, $arquivo)
	{
		$nosso_numero = substr($linha,62,8); //63-70
		//verifica se ja carregou o titulo
		$remessa_bancaria = $this->find('first', array('conditions' => array('RemessaBancaria.nosso_numero' => $nosso_numero, 'RemessaBancaria.codigo_banco' => $this->codigo_banco)));
		//verifica se a linha ja foi carregada
		if(empty($remessa_bancaria)) {
			//pega os dados
			$remessa['RemessaBancaria']['codigo_banco']			= $this->codigo_banco; //codigo do banco de dados da tabela banco
			$remessa['RemessaBancaria']['codigo_remessa_status']= 1; //aguardando retorno
			$remessa['RemessaBancaria']["nosso_numero"] 		= $nosso_numero;
			$remessa['RemessaBancaria']["numero_carteira"] 		= substr($linha,83,3); //84-86
			$remessa['RemessaBancaria']["numero_documento"] 	= substr($linha,110,10); //111-120
			$remessa['RemessaBancaria']["data_vencimento"] 		= substr($linha,120,6); //121-126
			$remessa['RemessaBancaria']["valor"]			 	= substr($linha,126,13); //127-139
			$remessa['RemessaBancaria']["data_emissao"]	 		= substr($linha,150,6); //151-156
			$remessa['RemessaBancaria']["instrucao_1"]	 		= substr($linha,156,2); //157-158
			$remessa['RemessaBancaria']["instrucao_2"]	 		= substr($linha,158,2); //159-160
			$remessa['RemessaBancaria']["juros_1_dia"]	 		= substr($linha,160,13); //161-173
			$remessa['RemessaBancaria']["tipo_inscricao"]	 	= substr($linha,218,2); //219-220
			$remessa['RemessaBancaria']["numero_inscricao"] 	= substr($linha,220,14); //221-234
			$remessa['RemessaBancaria']["nome_pagador"] 		= trim(substr($linha,234,30)); //235-264
			$remessa['RemessaBancaria']["linha_remessa"] 		= $linha;
			$remessa['RemessaBancaria']["codigo_usuario_remessa"]=$_SESSION['Auth']['Usuario']['codigo'];
			//formatação dos campos
			$remessa['RemessaBancaria']["data_vencimento"] 		= comum::formatarDataCnab($remessa['RemessaBancaria']["data_vencimento"]);
			$remessa['RemessaBancaria']["data_emissao"] 		= comum::formatarDataCnab($remessa['RemessaBancaria']["data_emissao"]);
			$remessa['RemessaBancaria']["valor"] 				= comum::formatarValorCnab($remessa['RemessaBancaria']["valor"]);
			$cpf_cnpj = ltrim($remessa['RemessaBancaria']["numero_inscricao"],0);
			if(strlen($cpf_cnpj) > 11) {
				$cpf_cnpj = str_pad($cpf_cnpj, 14, '0', STR_PAD_LEFT);
			} else {
				$cpf_cnpj = str_pad($cpf_cnpj, 11, '0', STR_PAD_LEFT);
			}
			$remessa['RemessaBancaria']["numero_inscricao"] 	= $cpf_cnpj;
			//verifica se tem o cliente cadastrado na base dados
			$this->bindModel(array('belongsTo' => array('Cliente' => array('foreignKey' => 'codigo_cliente'))), false);
			$cliente = $this->Cliente->find('first', 
										array('conditions' => array(
													'codigo_documento' => $remessa['RemessaBancaria']["numero_inscricao"],
												)
											)
									);
			//seta o codigo do cliente
			$remessa['RemessaBancaria']["codigo_cliente"] = $cliente["Cliente"]["codigo"];
			//caso nao exista o cliente cadastrar
			if(empty($cliente)) {
				//monta o array para cadastrar o cliente
				$cliente["codigo_documento"] 	= $remessa['RemessaBancaria']["numero_inscricao"];
				$cliente["nome_pagador"] 		= $remessa['RemessaBancaria']["nome_pagador"];
				$cliente["cep"] 				= substr($linha, 326,8);//327-334
				
				//para pegar o numero e complemento
				$logradouro 					= substr($linha, 274,40);//275-314
				$arrLog 						= explode(",", $logradouro);
				//para setar o numero do endereco do cliente pq a tabela cliente_endereco nao aceita nulo e nem alfanumerico
				$cliente["numero"] = '1';
				if(isset($arrLog[1])) {
					if(!empty($arrLog[1])) {
						$cliente["numero"] = comum::soNumero(trim($arrLog[1]));
						if(empty($cliente["numero"])) {
							$cliente["numero"] = '1';
						}
					}
				}
				$cliente["complemento"] 		= (isset($arrLog[2])) ? trim($arrLog[2]) : "";
				//pega os dados do endereco
				$endereco["logradouro"] = $arrLog[0];
				$endereco["bairro"] = trim(substr($linha,314,12));
				$endereco["cidade"] = trim(substr($linha,334,15));
				$endereco["estado"] = trim(substr($linha,349,2));
				//insere os clientes
				// print "cliente";
				$this->Cliente->bindModel(array('hasOne' => array(
					'ClienteEndereco' => array('foreignKey' => 'codigo_cliente'),
				)), false);
				$remessa['RemessaBancaria']["codigo_cliente"] = $this->insereClienteRemessa($cliente, $endereco);
			} else {
				//data do reprocessamento
				$log_integracao['LogIntegracao']['reprocessado'] = date('Y-m-d H:i:s');
			}//fim if cliente
			//grava os dados na tabela remessabancaria
			if(!parent::incluir($remessa)) {
				throw new Exception("341 Nao foi possivel incluir o titulo: " . $this->remessa['RemessaBancaria']['nosso_numero']);
			}

			//pega o nome do arquivo
			$arq = explode(DS, $arquivo);
			$arq = end($arq);
			//seta os valores
			$log_integracao['LogIntegracao']['arquivo'] = substr($arq,0,50);
			$log_integracao['LogIntegracao']['codigo_cliente'] = $remessa['RemessaBancaria']["codigo_cliente"];
			$log_integracao['LogIntegracao']['conteudo'] = $linha;
			$log_integracao['LogIntegracao']['retorno'] = $linha;
			$log_integracao['LogIntegracao']['sistema_origem'] = "REMESSA_BANCARIA_REMESSA";
			$log_integracao['LogIntegracao']['descricao'] = "SUCESSO";
			$log_integracao['LogIntegracao']['data_arquivo'] = date('Y-m-d H:i:s');
			$log_integracao['LogIntegracao']['status'] = '1'; //integrado
			$log_integracao['LogIntegracao']['tipo_operacao'] = 'I'; //nao integrado
			//inclui na tabela
			//joga na log_integracao
			$this->bindModel(array('belongsTo' => array('LogIntegracao' => array('foreignKey' => false))));
			$this->LogIntegracao->incluir($log_integracao);
			$this->cont_novo_titulo++;
		} else {
			$this->cont_titulo_existente++;
		}//fim verificacao se existe na base a linha
	} //fim leitura da remessa

	/**
	* Metodo para inserir cliente do arquivo cnab
	*/ 
	public function insereClienteRemessa($dados, $dados_endereco)
	{
		//seta os dados para insercao
		$cliente['Cliente']["codigo_documento"] 		= $dados["codigo_documento"];
		$cliente['Cliente']["razao_social"] 			= $dados["nome_pagador"];
		$cliente['Cliente']["nome_fantasia"] 			= $dados["nome_pagador"];
		$cliente['Cliente']["obrigar_loadplan"] 		= 0;
		$cliente['Cliente']["iniciar_por_checklist"] 	= 0;
		$cliente['Cliente']["monitorar_retorno"] 		= 0;
		$cliente['Cliente']['inscricao_estadual']		= 'ISENTO';
		$cliente['Cliente']['ccm']						= '1';
		$cliente['Cliente']['codigo_regime_tributario']	= '3';
		$cliente['Cliente']['ativo']					= 1;
		$cliente['Cliente']['codigo_externo']			= '';
		$cliente['Cliente']['tipo_unidade']				= 'F';
		
		$cliente['ClienteEndereco']['logradouro']		= $dados_endereco['logradouro'];
		$cliente['ClienteEndereco']['cep']				= $dados["cep"];
		$cliente['ClienteEndereco']['bairro']			= $dados_endereco["bairro"];
		$cliente['ClienteEndereco']['estado_abreviacao']= $dados_endereco["estado"];
		$cliente['ClienteEndereco']['estado_descricao']	= $dados_endereco["estado"];
		$cliente['ClienteEndereco']['cidade']			= $dados_endereco["cidade"];
		$cliente['ClienteEndereco']['numero']			= $dados["numero"];
		$cliente['ClienteEndereco']['complemento']		= $dados["complemento"];

		//grava os dados do cliente, e não vincula a grupo economico
		$cli = $this->Cliente->incluir($cliente, 1);

		if(!$this->Cliente->id) {
			$this->cont_cliente_erro++;
			return false;
		}

		return $this->Cliente->id;
	} //fim insere cliente

	/**
	 * Metodo para ler o arquivo de retorno e atualizar o status do titulo
	 */ 
	public function lerRetorno($linha, $arquivo)
	{
		//joga na log_integracao/ codigos das ocorrencias
		$this->bindModel(array('belongsTo' => array('LogIntegracao' => array('foreignKey' => false))));
		
		$sequencial = substr($linha,395);
		//campos para serem carregados na tabela
		$nosso_numero 			= substr($linha,62,8); //63-70
		
		//pega o valor cobrado para ver se teve desconto/abatimento
		$valor_cobrado 			= trim(substr($linha, 152,13),'0'); //153-165
		//campos
		$valor_tarifa			= trim(substr($linha, 175,13),'0'); //176-188
		$valor_iof				= trim(substr($linha, 214,13),'0'); //215-227
		$valor_abatimento		= trim(substr($linha, 227,13),'0'); //228-240
		$valor_principal		= trim(substr($linha, 253,13),'0'); //254-266
		$valor_pago 			= $valor_principal;
		$valor_juros			= ltrim(substr($linha, 266,13),'0'); //267-279
				
		$codigo_usuario_retorno	= $_SESSION['Auth']['Usuario']['codigo'];
		$data_credito 			= trim(substr($linha, 295,6)); // 296-301 data que vai ser creditado na conta
		$data_vencimento 		= trim(substr($linha,146,6)); //147-152 data do vencimento
		$data_pagamento 		= trim(substr($linha,110,6)); //111-116 data da ocorrencia
		
		$valor_pago 			= ($valor_pago != '') ? comum::formatarValorCnab($valor_pago) : NULL;
		$valor_tarifa			= ($valor_tarifa != '') ? comum::formatarValorCnab($valor_tarifa) : NULL;
		$valor_iof				= ($valor_iof != '') ? comum::formatarValorCnab($valor_iof) : NULL;
		$valor_abatimento		= ($valor_abatimento != '') ? comum::formatarValorCnab($valor_abatimento) : NULL;
		$valor_principal		= ($valor_principal != '') ? comum::formatarValorCnab($valor_principal) : NULL;
		$valor_juros			= ($valor_juros != '') ? comum::formatarValorCnab($valor_juros) : NULL;
		//verifica se ja carregou o titulo
		$codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];
		$remessa = $this->find('first', array(
											'recursive' => -1,
											'conditions' => array('RemessaBancaria.nosso_numero' => $nosso_numero, 
																	'RemessaBancaria.codigo_banco' => $this->codigo_banco)											
											));
		if(!empty($remessa["RemessaBancaria"]["codigo"])) {
			$codigo_remessa = $remessa["RemessaBancaria"]["codigo"];
		}
		$status = 1; //aguardando pagamento
		//pega a ocorrencia para atualizar a tabela
		$ocorrencia = substr($linha,108,2); //109-110
		$this->bindModel(array('belongsTo' => array('RemessaRetorno' => array('foreignKey' => 'codigo_remessa_retorno'))), false);
		//pega na tabela remessa_retorno o codigo da ocorrencia
		$remessa_retorno = $this->RemessaRetorno->find('first', array(
																	'conditions' => array(	'RemessaRetorno.codigo_ocorrencia' => $ocorrencia, 
																							'RemessaRetorno.codigo_banco' => $this->codigo_banco)
																));
		
		// print $linha."<br>";
		//verifica se a linha ja foi carregada 
		if(!empty($remessa)) {
			//seta o nova ocorrencia
			$remessa["RemessaBancaria"]["codigo_remessa_retorno"] 			= $remessa_retorno['RemessaRetorno']['codigo'];
			$remessa["RemessaBancaria"]["linha_retorno"] 					= $linha;			
			$remessa["RemessaBancaria"]["valor_pago"] 						= $valor_pago;
			$remessa["RemessaBancaria"]["valor_tarifa"] 					= $valor_tarifa;
			$remessa["RemessaBancaria"]["valor_iof"] 						= $valor_iof;
			$remessa["RemessaBancaria"]["valor_abatimento"] 				= $valor_abatimento;
			$remessa["RemessaBancaria"]["valor_principal"] 					= $valor_principal;
			$remessa["RemessaBancaria"]["codigo_usuario_retorno"] 			= $codigo_usuario_retorno;
			$remessa["RemessaBancaria"]["valor_juros"]			 			= $valor_juros;

			$dt_credito = (!empty($remessa["RemessaBancaria"]["data_credito"])) ? $remessa["RemessaBancaria"]["data_credito"] : null;
			$remessa["RemessaBancaria"]["data_credito"] 					= (Validation::date($data_credito)) ? comum::formatarDataCnab($data_credito) : $dt_credito;
			$dt_vencimento = (!empty($remessa['RemessaBancaria']["data_vencimento"])) ? $remessa['RemessaBancaria']["data_vencimento"] : null;
			$remessa['RemessaBancaria']["data_vencimento"] 					= (Validation::date($data_vencimento)) ? comum::formatarDataCnab($data_vencimento) : $dt_vencimento;

			// $remessa["RemessaBancaria"]["data_pagamento"] = null;
			if(empty($remessa["RemessaBancaria"]["data_inclusao"])) {
				$remessa["RemessaBancaria"]["data_inclusao"] = date('Y-m-d');
			}

			//seta o codigo do banco naveg
			$remessa['RemessaBancaria']['codigo_banco_naveg'] = $this->naveg_codigo_banco;
			
			//verifica se existe o codigo de retorno, se existir significa que esta sendo reprocessado
			if(empty($remessa["RemessaBancaria"]["codigo_remessa_retorno"])) {
				//pega o status da ocorrencia
				$status = $this->getStatus($ocorrencia);
				// $remessa['RemessaBancaria']['codigo'] = $remessa_bancaria['RemessaBancaria']['codigo'];
				$remessa["RemessaBancaria"]["codigo_remessa_status"] = $status;
				//verifica foi pago
				if($status == 2) {
					
					//seta a data do pagamento quando houver pagamento
					$remessa["RemessaBancaria"]["data_pagamento"] = comum::formatarDataCnab($data_pagamento);
				}
				// pr("atualizar_retorno_1");
				// pr($remessa['RemessaBancaria']['data_emissao']);
				//atualiza
				if(!parent::atualizar($remessa)) {
					throw new Exception("341 Nao foi possivel atualizar o titulo com o codigo de retorno: " . $this->remessa['RemessaBancaria']['nosso_numero']);
				}

				$log_integracao['LogIntegracao']['codigo_cliente'] = $remessa['RemessaBancaria']["codigo_cliente"];
				$log_integracao['LogIntegracao']['descricao'] = "SUCESSO";
				if($status == 2) {
					$this->cont_titulo_baixado++;
				} else if($status == 3) {
					$this->cont_titulo_cancelado++;
				}
			} else {
				$log_integracao['LogIntegracao']['descricao'] = "ATUALIZADO";

				//pega o status da ocorrencia
				$status = $this->getStatus($ocorrencia);

				//verifica para atualiziar o status para pago
				if($status == 2) {
					//$status = 4; //possui pendencia
					//verifica se tem a data e o codigo cliente para colocar o status de pago
					if(isset($remessa['RemessaBancaria']['codigo_cliente']) && isset($remessa['RemessaBancaria']['data_emissao'])) {
						if(!is_null($remessa['RemessaBancaria']['codigo_cliente']) && !is_null($remessa['RemessaBancaria']['data_emissao'])) {
							
							//seta o status
							$remessa["RemessaBancaria"]["codigo_remessa_status"] = $status; //pago
							
							//seta a data do pagamento quando houver pagamento
							$remessa["RemessaBancaria"]["data_pagamento"] = comum::formatarDataCnab($data_pagamento);
							$log_integracao['LogIntegracao']['codigo_cliente'] = $remessa['RemessaBancaria']["codigo_cliente"];
							$this->cont_titulo_baixado++;
						} //fim if	
					} 
					else {
						$this->cont_titulo_atualizado++;
					}//fim codigo cliente e data de emissao
				} 
				else {
					$log_integracao['LogIntegracao']['descricao'] = "Linha Sequencial " . $sequencial . "do Retorno sendo reprocessada, porém não foi pago!";
					$this->cont_titulo_atualizado++;
				}//fim ocorrencias como pago

				// pr("atualizar_retorno_2");
				// pr($remessa['RemessaBancaria']['data_emissao']);

				//atualiza
				if(!parent::atualizar($remessa)) {
					throw new Exception("341 Nao foi possivel atualizar o titulo: " . $remessa['RemessaBancaria']['nosso_numero']);
				}
			}
		} 
		else {
			$log_integracao['LogIntegracao']['descricao'] = "Linha Sequencial " . $sequencial . " do Retorno não foi encontrado o nosso numero!";
			$this->cont_titulo_nao_encontrados++;
		}//fim verifica se tem remessa
		##########################
		####15/07/2017 - FOI COMENTADO PARA NÃO CARREGAR O TITULO QUE VEIO DO RETORNO SEM TER REMESSA ENVIADA PARA ESTA EMPRESAS
		##########################
		/*
		else { //casso nao exista irá carregar a linha
			if($ocorrencia == '10') {
				$data_pagamento = trim(comum::formatarDataCnab(substr($linha, 110,6)));//data da ocorrencia 111-116
			}
			if(!empty($data_pagamento)) {
				//verifica para atualziar o status para pago
				if($ocorrencia == '05' || $ocorrencia == '06' || $ocorrencia == '07' || $ocorrencia == '08' 
					|| $ocorrencia == '09' || $ocorrencia == '10') {
					//verifica se não encontrou o nosso numero colcoar como possui pendência
					$status = 4;//possui pendencia
				}
			}
			//seta o nova ocorrencia
			$remessa["RemessaBancaria"]["codigo_remessa_retorno"] 			= $ocorrencia;
			$remessa["RemessaBancaria"]["linha_retorno"] 					= $linha;
			$remessa["RemessaBancaria"]["data_pagamento"] 					= $data_pagamento;
			$remessa["RemessaBancaria"]["valor_pago"] 						= $valor_pago;
			$remessa["RemessaBancaria"]["valor_tarifa"] 					= $valor_tarifa;
			$remessa["RemessaBancaria"]["valor_iof"] 						= $valor_iof;
			$remessa["RemessaBancaria"]["valor_abatimento"] 				= $valor_abatimento;
			$remessa["RemessaBancaria"]["valor_principal"] 					= $valor_principal;
			$remessa["RemessaBancaria"]["data_credito"] 					= $data_credito;
			$remessa["RemessaBancaria"]["codigo_usuario_retorno"] 			= $codigo_usuario_retorno;
			
			//cadastrar o registro mesmo sem ter remessa, para depois editar o cliente e relacionar o seu id
			$remessa["RemessaBancaria"]['nome_pagador'] 			= trim(substr($linha, 324,30));
			$remessa['RemessaBancaria']["nosso_numero"] 			= $nosso_numero;
			$remessa['RemessaBancaria']["numero_carteira"] 			= substr($linha,82,3); //84-86
			$remessa['RemessaBancaria']["numero_documento"] 		= substr($linha,116,10); //111-120
			$remessa['RemessaBancaria']["data_vencimento"] 			= substr($linha,146,6); //121-126
			$remessa['RemessaBancaria']["valor"]					= substr($linha,152,13); //127-139
			$remessa["RemessaBancaria"]["codigo_remessa_status"]	= $status; //aguardando pagamento / pendencia
			
			//formatação dos campos
			$remessa['RemessaBancaria']["data_vencimento"] 			= comum::formatarDataCnab($remessa['RemessaBancaria']["data_vencimento"]);
			$remessa['RemessaBancaria']["valor"] 					= comum::formatarValorCnab($remessa['RemessaBancaria']["valor"]);
			// pr($remessa);
			//verifica se o nome do pagador existe no arquivo
			if(!empty($remessa['RemessaBancaria']['nome_pagador'])) {
				//grava os dados na tabela remessabancaria
				parent::incluir($remessa,false);
				//seta o codigo da remessa
				$codigo_remessa = $this->id;
				// file_put_contents(TMP.DS."arquivo.txt",$codigo_remessa."--".print_r($remessa,1),FILE_APPEND);
				// exit;
				$log_integracao['LogIntegracao']['descricao'] = "INCLUIDO ".$remessa["RemessaBancaria"]['nome_pagador']." através do arquivo de retorno.";
				if($status == 4) {
					$this->cont_novo_titulo++;
				} else {
					$this->cont_titulo_naoexistente++;
				}
				
			} else {
				$log_integracao['LogIntegracao']['descricao'] = "Linha Sequencial " . $sequencial . "do Retorno sendo reprocessada! Não existe nome do pagador no arquivo de retorno.";
				$this->cont_titulo_naoexistente++;
			}
		}//fim verificacao se existe na base a linha
		*/
		//pega o nome do arquivo
		$arq = explode(DS, $arquivo);
		$arq = end($arq);
		//seta os valores
		$log_integracao['LogIntegracao']['arquivo'] 		= $arq;
		$log_integracao['LogIntegracao']['conteudo'] 		= $linha;
		$log_integracao['LogIntegracao']['retorno'] 		= $linha;
		$log_integracao['LogIntegracao']['sistema_origem'] 	= "REMESSA_BANCARIA_RETORNO";		
		$log_integracao['LogIntegracao']['data_arquivo'] 	= date('Y-m-d H:i:s');
		$log_integracao['LogIntegracao']['status'] 			= '0'; //nao integrado
		$log_integracao['LogIntegracao']['tipo_operacao'] 	= 'I'; //inserido
		//inclui na tabela
		$this->LogIntegracao->incluir($log_integracao);
		//gera os pedidos para integracao com o sistema financeiro
		if($status == 2) {
			//pega os ids dos retornos gravados
			$this->codigos_remessas_bancarias[$codigo_remessa] = $arq;
		}//fim status pago
	} //fim ler_retorno

	/**
	 * Metodo para processar o tipo de ocorrencia que está vindo por tipo de banco
	 * 
	 */ 
	public function getStatus($ocorrencia)
	{
		$status = 1; //aguardando pagamento
		//pega o codigo do banco que esta sendo processado
		switch ($this->codigo_banco) {
			case '341':
				//pago
				if($ocorrencia == '05' || $ocorrencia == '06' || $ocorrencia == '07' || $ocorrencia == '08' || $ocorrencia == '10') {
					$status = 2; //pago
				} else if($ocorrencia == '03' || $ocorrencia == '15' || $ocorrencia == '16' || $ocorrencia == '17' || $ocorrencia == '18') { //cancelada
					$status = 3; //cancelado
				}
				
				break;
			case '033':
				//pago
				if($ocorrencia == '06' || $ocorrencia == '17') {
					$status = 2; //pago
				} else if($ocorrencia == '03' || $ocorrencia == '26' || $ocorrencia == '30') { //cancelada
					$status = 3; //cancelado
				}
				break;
			/*case '353':
				
				//pago
				if($ocorrencia == '06' || $ocorrencia == '07' || $ocorrencia == '08' 
					|| $ocorrencia == '09' || $ocorrencia == '10') {
					$status = 2; //pago
				} else if($ocorrencia == '03' || $ocorrencia == '15' || $ocorrencia == '16' 
					|| $ocorrencia == '17') { //cancelada
					$status = 3; //cancelado
				}
				break;*/
		} //fim switch

		return $status;
	} //fim getStatus

	/**
	 * Pega o faturamento por cliente
	 */ 
	public function getFaturamento($codigo_cliente=null)
	{
		$this->bindModel(array('belongsTo' => array('RemessaStatus' => array('foreignKey' => 'codigo_remessa_status'))), false);
		$this->bindModel(array('belongsTo' => array('RemessaRetorno' => array('foreignKey' => 'codigo_remessa_retorno'))), false);
		/**********FATURAMENTO REMESSA BANCARIA********/
    	$fieldsRemessa = array(
    			'RemessaBancaria.nosso_numero',
    			'RemessaBancaria.data_emissao',
    			'RemessaBancaria.data_vencimento',
    			'RemessaStatus.descricao',
    			'RemessaRetorno.codigo',
    			'RemessaRetorno.descricao',
    			'RemessaBancaria.valor',
    			'RemessaBancaria.data_pagamento',
    			'RemessaBancaria.valor_pago',
    			'RemessaBancaria.valor_tarifa',
    			'RemessaBancaria.valor_juros',
    		);
    	$conditionsRemessa = array('codigo_cliente' => $codigo_cliente);
    	//pega os registros
		$remessa_bancaria = $this->find('all', array(
													//'joins' => $joinsRemessa,
													'conditions' => $conditionsRemessa,
													'fields' => $fieldsRemessa,
													)
										);
		return $remessa_bancaria;
	} //fim getFaturamento

	/**
	 * Metodo para gerar o pedido pelo arquivo de retorno que veio e está pago
	 * e vincula o novo pedido na remessa
	 *  
	 */ 
	public function gerarPedido($codigo, $arq)
	{
		// $conditions = array('codigo' => $codigo_remessa,
		// 					'codigo_cliente' => '',
		// 					'codigo_status' => 2
		// 				);
		//pega os dados da remessa bancaria
		$remessa = $this->find('first', array('conditions' => array('RemessaBancaria.codigo' => $codigo)));
		// file_put_contents(TMP.DS."arquivo.txt",print_r($remessa,1),FILE_APPEND);
		//mota o pedido para inputar os dados
		//VERIFICA SE JA TEM UM PEDIDO VINCULADO
		if(empty($remessa['RemessaBancaria']['codigo_pedido'])) {
			//VERIFICA SE TEM CLIENTE VINCULADO
			if(!empty($remessa['RemessaBancaria']['codigo_cliente'])) {
				//separa mes e ano de referencia que foi pago para lancar no pedido
				$arrayDt = explode('/',$remessa['RemessaBancaria']['data_vencimento']);
				if(!empty($arrayDt)) {
					$mes = $arrayDt[1];
					$ano = substr($arrayDt[2],0,4);
				} 
				else {
					$arrayDt = explode('-',$remessa['RemessaBancaria']['data_vencimento']);
					$mes = $arrayDt[1];
					$ano = substr($arrayDt[0],0,4);
				}
				//mota o array para inserir o pedido
				$pedido['Pedido']['codigo_cliente_pagador'] 		= $remessa['RemessaBancaria']['codigo_cliente'];
				$pedido['Pedido']['mes_referencia'] 				= $mes;
				$pedido['Pedido']['ano_referencia'] 				= $ano;
				$pedido['Pedido']['manual'] 						= 1;
				$pedido['Pedido']['codigo_condicao_pagamento'] 		= '001';
				//seta a variavel codigo_pedido
				$codigo_pedido = "";
				//chama a model pedido
				$this->bindModel(array('belongsTo' => array('Pedido' => array('foreignKey' => false))));	
				if( $this->Pedido->incluir($pedido['Pedido']) ) {
					//seta o codigo do pedido
					$codigo_pedido = $this->Pedido->id;
					//declara o codigo do item pedido
					$codigo_item_pedido = "";
					//pega o codigo do produto/servico na tabela configuracao
					$this->bindModel(array('belongsTo' => array('Configuracao' => array('foreignKey' => false))));
					$configs = $this->Configuracao->find('list', 
													array('conditions' => 
															array('chave' => array('CODIGO_SERVICO_INTEGRACAO_FINANCEIRA') 
															),
															'fields' => array('chave', 'valor'),
														));
					//pega o produto pelo servico
					$this->bindModel(array('belongsTo' => array('ProdutoServico' => array('foreignKey' => false))));
					$produto_servico = $this->ProdutoServico->find('first', array('conditions' => array('ProdutoServico.codigo_servico' => $configs['CODIGO_SERVICO_INTEGRACAO_FINANCEIRA'])));
					$codigo_produto = $produto_servico['ProdutoServico']['codigo_produto'];
					$codigo_servico = $configs['CODIGO_SERVICO_INTEGRACAO_FINANCEIRA'];
					//dados da tabela itens pedido
					$itemPedido['ItemPedido']['codigo_pedido'] 		= $codigo_pedido;
					$itemPedido['ItemPedido']['codigo_produto'] 	= $codigo_produto;
					$itemPedido['ItemPedido']['quantidade'] 		= 1;
					$itemPedido['ItemPedido']['valor_total'] 		= $remessa['RemessaBancaria']['valor'];
					$this->bindModel(array('belongsTo' => array('ItemPedido' => array('foreignKey' => false))));
					if( $this->ItemPedido->incluir($itemPedido) ) {
						
						$codigo_item_pedido = $this->ItemPedido->id;
						//seta o servico na tabela detalhes_itens_pedidos_manuais						
						$detalheItem['DetalheItemPedidoManual']['codigo_item_pedido'] = $codigo_item_pedido;
						$detalheItem['DetalheItemPedidoManual']['valor'] = $remessa['RemessaBancaria']['valor'];
						$detalheItem['DetalheItemPedidoManual']['codigo_servico'] = $codigo_servico;
						$detalheItem['DetalheItemPedidoManual']['quantidade'] = 1;
						
						$this->bindModel(array('belongsTo' => array('DetalheItemPedidoManual' => array('foreignKey' =>false))));
						
						if(!$this->DetalheItemPedidoManual->incluir($detalheItem,false)) {
							$this->linha_pedido_erro .= "DETALHE_PEDIDO_MANUAL :" . $remessa['RemessaBancaria']['linha_retorno']."<br>";
						}
					}
					else {
						$this->linha_pedido_erro .= "ITEM_PEDIDO :" . $remessa['RemessaBancaria']['linha_retorno']."<br>";
					}
					//seta na remessa o pedido que foi inserido
					$remessa['RemessaBancaria']['codigo_pedido'] 	= $codigo_pedido;
					
					if(!parent::atualizar($remessa)) {
						throw new Exception("PEDIDO Nao foi possivel atualizar a remessa bancaria");
					}

					$this->cont_pedido_criado++;
				} 
				else {
					//log de erro para inserir o pedido
					$log_integracao['LogIntegracao']['descricao'] = "ERRO Ao incluir Pedido no arquivo de retorno.";
					$this->cont_pedido_erro++;
					$this->linha_pedido_erro .= "PEDIDO :" . $remessa['RemessaBancaria']['linha_retorno']."<br>";
				}//fim pedido incluir
			} 
			else {
				//log de erro pois nao tem cliente vinculado
				$log_integracao['LogIntegracao']['descricao'] = "ERRO Não tem cliente Criado/vinculado ao título.";
				$this->cont_pedido_erro++;
				$this->linha_pedido_erro .= "CODIGO_CLIENTE :" . $remessa['RemessaBancaria']['linha_retorno']."<br>";
			}//fim if codigo_cliente
		} 
		else {
			//gera log que ja tem pedido cadastrado
			$log_integracao['LogIntegracao']['descricao'] = "Pedido ja cadastrado/vinculado.";
			$this->cont_pedido_ja_existente++;
			
			// $this->linha_pedido_existe .= "PEDIDO JA EXISTE: " $remessa['RemessaBancaria']['linha_retorno']."<br>";
			
		}//fim if codigo_pedido
		//joga na log_integracao
		$this->bindModel(array('belongsTo' => array('LogIntegracao' => array('foreignKey' => false))));
		//seta os valores
		$log_integracao['LogIntegracao']['arquivo'] 		= $arq;
		$log_integracao['LogIntegracao']['conteudo'] 		= $remessa['RemessaBancaria']['linha_retorno'];
		$log_integracao['LogIntegracao']['retorno'] 		= $remessa['RemessaBancaria']['linha_retorno'];
		$log_integracao['LogIntegracao']['sistema_origem'] 	= "REMESSA_BANCARIA_GERAR_PEDIDO";		
		$log_integracao['LogIntegracao']['data_arquivo'] 	= date('Y-m-d H:i:s');
		$log_integracao['LogIntegracao']['status'] 			= '0'; //nao integrado
		$log_integracao['LogIntegracao']['tipo_operacao'] 	= 'I'; //inserido
		//inclui na tabela
		$this->LogIntegracao->incluir($log_integracao);

		if(!empty($this->linha_pedido_erro)) {
			throw new Exception("PEDIDO Erros: " . $this->linha_pedido_erro);
		}
		
	} //fim geraPedido

	/**
	 * Metodo para ler a linha do segmento P e separa os dados pertinentes para o sistema
	 */
	public function setSegP($linha,$arquivo) 
	{
		$nosso_numero = substr($linha,44,13); //045 –057 nosso numero
		//verifica se ja carregou o titulo
		$remessa_bancaria = $this->find('first', array('conditions' => array('RemessaBancaria.nosso_numero' => $nosso_numero, 'RemessaBancaria.codigo_banco' => $this->codigo_banco)));

		//verifica se a linha ja foi carregada
		if(empty($remessa_bancaria)) {

			//seta a linha do segmento p
			$this->linhasPQR .= $linha;
			$this->array_remessa['RemessaBancaria']["codigo_usuario_remessa"]= $_SESSION['Auth']['Usuario']['codigo'];

			//pega os dados
			$this->array_remessa['RemessaBancaria']['codigo_banco']			= $this->codigo_banco; //codigo do banco de dados da tabela banco
			$this->array_remessa['RemessaBancaria']['codigo_remessa_status']= 1; //aguardando retorno
			$this->array_remessa['RemessaBancaria']["nosso_numero"] 		= $nosso_numero;
			$this->array_remessa['RemessaBancaria']["numero_carteira"] 		= ""; //tera informacoes no retorno
			$this->array_remessa['RemessaBancaria']["numero_documento"] 	= substr($linha,62,15); //063 - 077 numero do documento ou seu numero
			$this->array_remessa['RemessaBancaria']["data_vencimento"] 		= comum::formatarDataCnab240(substr($linha,77,8)); //078 - 085 data de vencimento do titulo DDMMAAAA
			$this->array_remessa['RemessaBancaria']["valor"]			 	= comum::formatarValorCnab(substr($linha,85,15)); //086 - 100 valor nominal do titulo
			$this->array_remessa['RemessaBancaria']["data_emissao"]	 		= comum::formatarDataCnab240(substr($linha,109,8)); //110 - 117 data da emissao do titulo DDMMAAAA
			
			$this->array_remessa['RemessaBancaria']["instrucao_1"]	 		= '';
			$this->array_remessa['RemessaBancaria']["instrucao_2"]	 		= '';

			//pega qual é o codigo do juros 1,2,3,4,5,6
			$codigo_juros = substr($linha, 117,1);//118-118 codigo do juros
			if($codigo_juros == '1') {
				$this->array_remessa['RemessaBancaria']["juros_1_dia"]		= comum::formatarValorCnab(substr($linha,126,15)); //127 - 141 valor juros
			}
			
		} else {

			//contabiliza o titulo
			$this->cont_titulo_existente++;

			//retorna como titulo existente
			return "titulo_exitente";

		}//fim verificacao se existe na base a linha

	} //fim setSegP

	/**
	 * Metodo para ler a linha do segmento Q e separa os dados pertinentes para o sistema
	 */
	public function setSegQ($linha,$arquivo) {

		//verifica se existe 
		if(!empty($this->array_remessa)) {

			//seta a linha do segmento q
			$this->linhasPQR .= $linha;
			
			$this->array_remessa['RemessaBancaria']["tipo_inscricao"]	 	= substr($linha,17,1); //18-18 tipo inscricao
			$this->array_remessa['RemessaBancaria']["numero_inscricao"] 	= substr($linha,18,15); //019 - 033 Número de inscrição do Pagador

			$this->array_remessa['RemessaBancaria']["nome_pagador"] 		= trim(substr($linha,33,40)); //034 - 073 Nome Pagador

			//retira os espacos em branco e ou 0s
			$cpf_cnpj = ltrim($this->array_remessa['RemessaBancaria']["numero_inscricao"],0);

			if(strlen($cpf_cnpj) > 11) {
				$cpf_cnpj = str_pad($cpf_cnpj, 14, '0', STR_PAD_LEFT);
			} else {
				$cpf_cnpj = str_pad($cpf_cnpj, 11, '0', STR_PAD_LEFT);
			}
			$this->array_remessa['RemessaBancaria']["numero_inscricao"] 	= $cpf_cnpj;

			//verifica se tem o cliente cadastrado na base dados
			$this->bindModel(array('belongsTo' => array('Cliente' => array('foreignKey' => 'codigo_cliente'))), false);
			$cliente = $this->Cliente->find('first', 
										array('conditions' => array(
													'codigo_documento' => $this->array_remessa['RemessaBancaria']["numero_inscricao"],
												)
											)
									);
			//seta o codigo do cliente
			$this->array_remessa['RemessaBancaria']["codigo_cliente"] = $cliente["Cliente"]["codigo"];

			//caso nao exista o cliente cadastrar
			if(empty($cliente)) {
				//monta o array para cadastrar o cliente
				$cliente["codigo_documento"] 	= $this->array_remessa['RemessaBancaria']["numero_inscricao"];
				$cliente["nome_pagador"] 		= $this->array_remessa['RemessaBancaria']["nome_pagador"];
				$cliente["cep"] 				= substr($linha, 128,8); //129 - 133 Cep Pagador + 134 - 136 Sufixo do Cep do Pagador
				
				//para pegar o numero e complemento
				$logradouro 					= substr($linha, 73,40); //074 - 113 Endereço Pagador
				$arrLog 						= explode(",", $logradouro);

				//para setar o numero do endereco do cliente pq a tabela cliente_endereco nao aceita nulo e nem alfanumerico
				$cliente["numero"] = '1';
				$complemento = "";
				if(isset($arrLog[1])) {
					if(!empty($arrLog[1])) {

						//seta o complemento
						$comp = explode(' ', $arrLog[1]);						
						if(isset($comp[1])) {//verifica se existe o indice
							if(!empty($comp[1])) { //verifica se nao esta vazio
								unset($comp[0]);//retira o numero
								$complemento = implode(" ", $comp); //seta o complemento
							}
						}

						$cliente["numero"] = comum::soNumero(trim($arrLog[1]));
						if(empty($cliente["numero"])) {
							$cliente["numero"] = '1';
						}
					}
				}
				$cliente["complemento"] 		= $complemento;
				//pega os dados do endereco
				$endereco["logradouro"] = $arrLog[0];

				$endereco["bairro"] = trim(substr($linha,113,15)); // 114 - 128 Bairro Pagador
				$endereco["cidade"] = trim(substr($linha,136,15)); // 137 - 151 Cidade do Pagador
				$endereco["estado"] = trim(substr($linha,151,2)); //152 - 153 Unidade da Federação do Pagador

				//insere os clientes
				// print "cliente";
				$this->Cliente->bindModel(array('hasOne' => array('ClienteEndereco' => array('foreignKey' => 'codigo_cliente'),	)), false);

				$this->array_remessa['RemessaBancaria']["codigo_cliente"] = $this->insereClienteRemessa($cliente, $endereco);
			} else {
				//data do reprocessamento
				$this->array_remessa['log_reprocessando'] = date('Y-m-d H:i:s');
			}//fim if cliente

		} //fim this->array_remessa

	} //fim setSegQ

	/**
	 * Metodo para ler a linha do segmento R e separa os dados pertinentes para o sistema
	 */
	public function setSegR($linha,$arquivo) {

		//verifica se existe 
		if(!empty($this->array_remessa)) {

			//seta a linha do segmento q
			$this->linhasPQR .= $linha;

		} //fim empty array remessa

	} //fim setSegR

	/**
	 * Metodo para ler a remessa do arquivo de 240 posicoes e inserir no banco de dados
	 */
	public function gravaRemessa240($arquivo)
	{
		
		//verifica se existe valor para ser cadastrado
		if(!empty($this->array_remessa)) {

			$this->array_remessa["RemessaBancaria"]['linha_remessa'] = $this->linhasPQR;

			// $this->log(print_r($this->array_remessa,1),'debug');

			//grava os dados na tabela remessabancaria
			if(!$this->incluir($this->array_remessa)){
				throw new Exception("033 - Nao foi possivel incluir o arquivo. Verificar o titulo: " . $this->array_remessa['RemessaBancaria']['nosso_numero']);
			}

			//pega o nome do arquivo
			$arq = explode(DS, $arquivo);
			$arq = end($arq);

			//seta os valores
			$log_integracao['LogIntegracao']['arquivo'] = substr($arq,0,50);
			$log_integracao['LogIntegracao']['codigo_cliente'] = $this->array_remessa['RemessaBancaria']["codigo_cliente"];
			$log_integracao['LogIntegracao']['conteudo'] = $this->linhasPQR;
			$log_integracao['LogIntegracao']['retorno'] = $this->linhasPQR;
			$log_integracao['LogIntegracao']['sistema_origem'] = "REMESSA_BANCARIA_GRAVA_240";
			$log_integracao['LogIntegracao']['descricao'] = "SUCESSO";
			$log_integracao['LogIntegracao']['data_arquivo'] = date('Y-m-d H:i:s');
			$log_integracao['LogIntegracao']['status'] = '1'; //integrado
			$log_integracao['LogIntegracao']['tipo_operacao'] = 'I'; //nao integrado

			//verifica se existe este indice
			if(isset($this->array_remessa['log_reprocessando'])) {
				$log_integracao['LogIntegracao']['reprocessado'] = $this->array_remessa['log_reprocessando'];
			}

			//inclui na tabela
			//joga na log_integracao
			$this->bindModel(array('belongsTo' => array('LogIntegracao' => array('foreignKey' => false))));
			$this->LogIntegracao->incluir($log_integracao);
			
			if(!empty($this->array_remessa['RemessaBancaria']["codigo_cliente"])) {
				$this->cont_novo_titulo++;
			}

		} //fim $this->array_remessa
	
	} //fim leitura da remessa 240


	/**
	 * Metodo para leitura da linha do segmento t
	 *
	 */
	public function setSegT($linha,$arquivo)
	{
		
		$this->sequencial = substr($linha,8,13); //009 - 013 N sequencial do registro no lote

		//campos para serem carregados na tabela
		$nosso_numero 			= substr($linha,40,13); //041 –053 Identificação do título no Banco
		
		//pega o valor cobrado para ver se teve desconto/abatimento
		$valor_cobrado 			= comum::formatarValorCnab(substr($linha, 77,15)); //078 – 092 Valor nominal do título

		//campos
		$valor_tarifa			= comum::formatarValorCnab(substr($linha, 193,15)); //194 – 208 Valor da Tarifa/Custas
		$data_vencimento 		= comum::formatarDataCnab240(trim(substr($linha,69,8))); //070 – 077 Data do vencimento do título
		$codigo_usuario_retorno	= $_SESSION['Auth']['Usuario']['codigo'];


		//verifica se ja carregou o titulo
		$codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];
		$this->array_remessa = $this->find('first', array(
											'recursive' => -1,
											'conditions' => array('RemessaBancaria.nosso_numero' => $nosso_numero, 
																	'RemessaBancaria.codigo_banco' => $this->codigo_banco)
											));

		if(!empty($this->array_remessa["RemessaBancaria"]["codigo"])) {
			$codigo_remessa = $this->array_remessa["RemessaBancaria"]["codigo"];
		}

		

		//pega a ocorrencia para atualizar a tabela
		$ocorrencia = substr($linha,15,2); //016 - 017 Código de movimento (ocorrência)

		$this->bindModel(array('belongsTo' => array('RemessaRetorno' => array('foreignKey' => 'codigo_remessa_retorno'))), false);
		//pega na tabela remessa_retorno o codigo da ocorrencia
		$remessa_retorno = $this->RemessaRetorno->find('first', array(
																	'conditions' => array(	'RemessaRetorno.codigo_ocorrencia' => $ocorrencia, 
																							'RemessaRetorno.codigo_banco' => $this->codigo_banco)
																));
		
		if(!empty($this->array_remessa)) {

			$this->array_remessa["ocorrencia"] = $ocorrencia;

			//seta o nova ocorrencia
			$this->linhasTU = $linha;
			$this->array_remessa["RemessaBancaria"]["codigo_remessa_retorno"] 			= $remessa_retorno['RemessaRetorno']['codigo'];			
			$this->array_remessa["RemessaBancaria"]["valor_tarifa"] 					= $valor_tarifa;
			$this->array_remessa["RemessaBancaria"]["codigo_usuario_retorno"]			= $codigo_usuario_retorno;
			$this->array_remessa['RemessaBancaria']["data_vencimento"]					= $data_vencimento;

		}

	} //fim setSegT($linha,$arquivo);

	/**
	 * Metodo para leitura da linha do segmento u
	 *
	 */
	public function setSegU($linha,$arquivo)
	{
		
		//verifica se a linha ja foi carregada 
		if(!empty($this->array_remessa)) {
			$this->linhasTU .= $linha;

			$this->array_remessa["RemessaBancaria"]["valor_abatimento"]		= comum::formatarValorCnab(substr($linha, 47,15)); //048 - 062 Valor do Abatimento Concedido/Cancelado
			$this->array_remessa["RemessaBancaria"]["valor_iof"]			= comum::formatarValorCnab(substr($linha, 62,15)); //063 - 077Valor do IOF recolhido
			$this->array_remessa["RemessaBancaria"]["valor_principal"]		= comum::formatarValorCnab(substr($linha, 77,15)); //078 - 092 Valor pago pelo Pagador
			$this->array_remessa["RemessaBancaria"]["valor_pago"] 			= $this->array_remessa["RemessaBancaria"]["valor_principal"];
			$this->array_remessa["RemessaBancaria"]["valor_juros"]			= comum::formatarValorCnab(substr($linha, 17,15)); //018 - 032 Juros / Multa / Encargos
			$this->array_remessa["RemessaBancaria"]["data_credito"]			= comum::formatarDataCnab240(trim(substr($linha, 145,8))); // 146 - 153 Data da efetivação do crédito
			
			//setado com outro indice pois existe uma validacao se foi realizado o pagamento
			$this->array_remessa["data_pagamento"]							= comum::formatarDataCnab240(trim(substr($linha,137,8))); //138 - 145 Data da ocorrência
		}


	} //fim setSegU($linha,$arquivo);

	/**
	 * metodo para gravar o retorno do arquivo 240
	 *
	 */

	public function gravaRetorno240($arquivo)
	{

		//joga na log_integracao/ codigos das ocorrencias
		$this->bindModel(array('belongsTo' => array('LogIntegracao' => array('foreignKey' => false))));

		$status = 1; //aguardando pagamento

		// print $linha."<br>";
		//verifica se a linha ja foi carregada 
		if(!empty($this->array_remessa)) {


			$this->array_remessa["RemessaBancaria"]["linha_retorno"] = $this->linhasTU;

			if(empty($this->array_remessa["RemessaBancaria"]["data_inclusao"])) {
				$this->array_remessa["RemessaBancaria"]["data_inclusao"] = date('Y-m-d');
			}
			
			//seta o codigo do banco naveg
			$this->array_remessa['RemessaBancaria']['codigo_banco_naveg'] = $this->naveg_codigo_banco;

			//verifica se existe o codigo de retorno, se existir significa que esta sendo reprocessado
			if(empty($this->array_remessa["RemessaBancaria"]["codigo_remessa_retorno"])) {

				//pega o status da ocorrencia
				$status = $this->getStatus($this->array_remessa["ocorrencia"]);

				// $remessa['RemessaBancaria']['codigo'] = $remessa_bancaria['RemessaBancaria']['codigo'];
				$this->array_remessa["RemessaBancaria"]["codigo_remessa_status"] = $status;

				//verifica foi pago
				if($status == 2) {
					//seta a data do pagamento quando houver pagamento
					$this->array_remessa["RemessaBancaria"]["data_pagamento"] = $this->array_remessa["data_pagamento"];
				}

				//atualiza
				if(!parent::atualizar($this->array_remessa)){
					throw new Exception("033 - Nao foi possivel atualizar o titulo: " . $this->array_remessa['RemessaBancaria']['nosso_numero']);
				}

				$log_integracao['LogIntegracao']['codigo_cliente'] = $remessa['RemessaBancaria']["codigo_cliente"];
				$log_integracao['LogIntegracao']['descricao'] = "SUCESSO";

				//setado com pago
				if($status == 2) {
					$this->cont_titulo_baixado++;
				} else if($status == 3) { //setado como cancelado
					$this->cont_titulo_cancelado++;
				}

			} else {
				$log_integracao['LogIntegracao']['descricao'] = "ATUALIZADO";

				//pega o status da ocorrencia
				$status = $this->getStatus($this->array_remessa["ocorrencia"]);

				//verifica para atualiziar o status para pago
				if($status == 2) {
					//$status = 4; //possui pendencia
					//verifica se tem a data e o codigo cliente para colocar o status de pago
					if(isset($this->array_remessa['RemessaBancaria']['codigo_cliente']) && isset($this->array_remessa['RemessaBancaria']['data_emissao'])) {
						if(!is_null($this->array_remessa['RemessaBancaria']['codigo_cliente']) && !is_null($this->array_remessa['RemessaBancaria']['data_emissao'])) {
							
							//seta o status
							$this->array_remessa["RemessaBancaria"]["codigo_remessa_status"] = $status; //pago
							
							//seta a data do pagamento quando houver pagamento
							$this->array_remessa["RemessaBancaria"]["data_pagamento"] = $this->array_remessa["data_pagamento"];
							$log_integracao['LogIntegracao']['codigo_cliente'] = $this->array_remessa['RemessaBancaria']["codigo_cliente"];
							$this->cont_titulo_baixado++;
						} //fim if	
					} 
					else {
						$this->cont_titulo_atualizado++;
					}//fim codigo cliente e data de emissao
				} 
				else {
					$log_integracao['LogIntegracao']['descricao'] = "Linha Sequencial " . $this->sequencial . "do Retorno sendo reprocessada, porém não foi pago!";
					$this->cont_titulo_atualizado++;
				}//fim ocorrencias como pago

				// pr("atualizar_retorno_2");
				// pr($remessa['RemessaBancaria']['data_emissao']);

				//atualiza				
				if(!parent::atualizar($this->array_remessa)){
					throw new Exception("033 - Nao foi possivel atualizar o titulo: " . $this->array_remessa['RemessaBancaria']['nosso_numero']);
				}
			}
		} 
		else {
			$log_integracao['LogIntegracao']['descricao'] = "Linha Sequencial " . $this->sequencial . " do Retorno não foi encontrado o nosso numero!";
			$this->cont_titulo_nao_encontrados++;
		}//fim verifica se tem remessa
		
		//pega o nome do arquivo
		$arq = explode(DS, $arquivo);
		$arq = end($arq);
		//seta os valores
		$log_integracao['LogIntegracao']['arquivo'] 		= $arq;
		$log_integracao['LogIntegracao']['conteudo'] 		= $this->linhasTU;
		$log_integracao['LogIntegracao']['retorno'] 		= $this->linhasTU;
		$log_integracao['LogIntegracao']['sistema_origem'] 	= "REMESSA_BANCARIA_GRAVA_RETORNO_240";		
		$log_integracao['LogIntegracao']['data_arquivo'] 	= date('Y-m-d H:i:s');
		$log_integracao['LogIntegracao']['status'] 			= '0'; //nao integrado
		$log_integracao['LogIntegracao']['tipo_operacao'] 	= 'I'; //inserido
		//inclui na tabela
		$this->LogIntegracao->incluir($log_integracao);
		//gera os pedidos para integracao com o sistema financeiro
		if($status == 2) {
			//pega os ids dos retornos gravados
			$this->codigos_remessas_bancarias[$this->array_remessa["RemessaBancaria"]["codigo"]] = $arq;
		}//fim status pago

	}//fim 


} //fim RemessaBancaria