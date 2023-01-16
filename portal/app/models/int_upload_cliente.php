<?php
class IntUploadCliente extends AppModel
{

	public $name          = 'IntUploadCliente';
	public $tableSchema   = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable      = 'int_upload_cliente';
	public $primaryKey    = 'codigo';
	public $actsAs        = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_upload_cliente'));

	protected $filtrosValidos = array(
		'codigo_cliente' => "",
		'codigo_status_transferencia' => "",
		'nome_arquivo' => "LIKE",
		'apelido' => "LIKE"
	);

	// public $status = array(
	// 	1 => "Arquivo transferido",
	// 	2 => "Arquivo transferencia falhou",
	// 	3 => "Arquivo pronto",
	// 	4 => "Importacao Estrutura incluindo",
	// 	5 => "Importacao Estrutura pronto",
	// 	6 => "Importacao Estrutura falhou",
	// 	8 => "Importacao estrutura processado",
	// 	9 => "Arquivo sendo processado"
	// );

	/**
	 * Á quantas linhas processadas deve se chamar o callback para atualizar o layout
	 */
	protected $lineCountTap = 10;

	public function getStatus($key)
	{

		$this->StatusTransferencia =& ClassRegistry::init('StatusTransferencia');
		$status_dados = $this->StatusTransferencia->find('first', array('conditions' => array('codigo' => $key)));

		// return $this->status[$key];
		return $status_dados['StatusTransferencia']['descricao'];
	}

	public function configure($lineCount) {
		$this->lineCountTap = $lineCount;
	}

	/**
	 * @param array $filtros
	 * @return array
	 */
	public function converteFiltroEmCondition($filtros)
	{
		$conditions = array();
		$filtrosValidos = array_keys($this->filtrosValidos);
		$_filtros = is_array($filtros) ? $filtros : array();
		foreach ($_filtros as $key => $filtro) {
			if (empty($filtro) || in_array($key, $filtrosValidos) == false) {
				continue;
			}
			$operator = $this->filtrosValidos[$key];

			$conditions["{$this->name}.{$key} {$operator}"] = $operator == "LIKE" ? "%{$filtro}%" : $filtro;
		}

		return $conditions;
	}


	/**
	 * Lê uma linha e corresponde ao seu map layout detalhe
	 * 
	 * @param array $base
	 * @param array $mapLayoutDetalhes
	 * @return array
	 */
	private function parseLine($base, $mapLayoutDetalhes)
	{

		$sorted = array();
		foreach ($base as $key => $value) {
			
			$value = str_replace('<0x81>', '', $value);

			$mapDetalhe = null;
			foreach ($mapLayoutDetalhes as $detalhe) {
				if ($detalhe['posicao'] != ($key + 1)) {
					continue;
				}
				$mapDetalhe = $detalhe;
				break;
			}
			if (!$mapDetalhe) {
				continue;
			}

			$sorted[$mapDetalhe['tabela']][$mapDetalhe['campo_saida']] = "'".trim(str_replace("'", " ", $value))."'";
			//tratamento para o campo data quando vier ? como valor isso da erro na insercao
			switch($mapDetalhe['campo_saida']) {
				case "data_inicio_afastamento":
				case "data_fim_afastamento":
					$sorted[$mapDetalhe['tabela']][$mapDetalhe['campo_saida']] = str_replace("?","",$sorted[$mapDetalhe['tabela']][$mapDetalhe['campo_saida']]);
					break;
				case "data_demissao":
					$sorted[$mapDetalhe['tabela']][$mapDetalhe['campo_saida']] = ($sorted[$mapDetalhe['tabela']][$mapDetalhe['campo_saida']] == "''") ? "null" : $sorted[$mapDetalhe['tabela']][$mapDetalhe['campo_saida']];
					break;
			}

		}

		// debug($sorted);
		// var_dump($sorted);
		// exit;

		return $sorted;
	}

	/**
	 * Atualiza status
	 * 
	 * @param int $status
	 * @para array $intUploadCliente
	 * @return boolean
	 */
	public function troca_status($status, &$intUploadCliente) {
		return $this->atualiza_atributo($intUploadCliente, 'codigo_status_transferencia', $status);
	}

	public function atualiza_atributo(&$intUploadCliente, $att, $value) {
		$intUploadCliente['IntUploadCliente'][$att] = $value;
		return $this->atualizar($intUploadCliente);
	}

	/**
	 * Abre arquivo e monta cada que uma linha representa(baseando-se no layout correspondente)
	 * 
	 * @param string $path
	 * @param int $mapLayoutDetalhes
	 * @param int $codigoCliente
	 * @param array $integrationsModels
	 * @throws Exception
	 * @return array
	 */
	public function open($path, $ignoraPrimeiraLinha = true, $mapLayoutDetalhes, $codigoCliente = null, $codigoEmpresa = null, $codigoUsuario = null, $integrationsModels, $intUploadClienteCodigo = null, $maxLines = 1, $onLineProceed = null)
	{

		if (!is_file($path)) {
			throw new Exception(
				"O path: {$path} não é um arquivo"
			);
		}

		if (!is_array($mapLayoutDetalhes)) {
			throw new Exception(
				"Map layout detalhe: {$mapLayoutDetalhes} não é valido."
			);
		}

		$delimiter = ";";
		$stream = fopen($path, "r");
		$line = 0;
		$tapCount = 0;
		
		$modelOf = array();

		//pega os campos e a tabela
		$campos_insert = array();
		$tabela_insert = "";
		

		foreach($mapLayoutDetalhes AS $mapDet) {
			$campos_insert[] = $mapDet['campo_saida'];
			$tabela_insert = $mapDet['tabela'];
		}
		$campos_insert[] = 'codigo_cliente';	
		if (!in_array('ativo',$campos_insert)) {
			$campos_insert[] = 'ativo';
		}
		$campos_insert[] = 'codigo_int_upload_cliente';
		$campos_insert[] = 'codigo_usuario_inclusao';
		$campos_insert[] = 'codigo_status_transferencia';
		$campos_insert[] = 'codigo_empresa';

		$total_colums_insert = count($campos_insert);

		$campos_insert = implode(",", $campos_insert);
		//monta o modelo para insercao
		$query_base_insert = "INSERT INTO RHHealth.dbo.{$tabela_insert} ({$campos_insert}) VALUES ";
		$query_insert = '';
		
		// debug($query_base_insert);exit;

		while (!feof($stream)) {
			$line++;
			$tapCount++;
			
			$row = utf8_encode(trim(fgets($stream)));
			if (trim($row) == "") {
				continue;
			}
			if ($ignoraPrimeiraLinha == true && $line == 1) {
				continue;
			} // ignore first line

			$row = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $row);
			$row = explode($delimiter, $row);
			$rowInserts = $this->parseLine($row, $mapLayoutDetalhes);
			
			// $this->log(print_r($rowInserts,true),'debug');
			// debug($rowInserts);exit;
			
			if (empty($rowInserts) == true) {
				continue;
			}
			foreach ($rowInserts as $table => $columns) {
				
				if(empty($modelOf)) {
					foreach ($integrationsModels as $model) {
						if ($model->useTable != $table) {
							continue;
						}
						$modelOf = $model;
						break;
					}
					if (empty($modelOf)) {
						continue;
					}
				}

				$columns['codigo_cliente'] = $codigoCliente;
				if (!isset($columns['ativo'])) {
					$columns['ativo'] = 1;
				}
				else {
					
					if(strtolower($columns['ativo']) == 'ativo') {
						$columns['ativo'] = 1;
					}
					else if(strtolower($columns['ativo']) == "'ativo'") {
						$columns['ativo'] = 1;
					}
					else if(strtolower($columns['ativo']) == "'inativo'") {
						$columns['ativo'] = 0;
					}
					else if(strtolower($columns['ativo']) == 'inativo') {
						$columns['ativo'] = 0;
					}
				}

				$columns['codigo_int_upload_cliente'] = $intUploadClienteCodigo;
				$columns['codigo_usuario_inclusao'] = $codigoUsuario;
				$columns['codigo_status_transferencia'] = 3;
				$columns['codigo_empresa'] = $codigoEmpresa;

				//verifica se o total de colunas de insercao é diferente de registros para gerar erro
				$total_colums_reg = count($columns);
				if($total_colums_reg != $total_colums_insert) {
					//monta a query para logar
					$query_erro = $query_base_insert . "(".implode(",",$columns).");\n";
					//gera o log de erro
					$this->log("Codigo Uploado Cliente: {$intUploadClienteCodigo}, query: ".$query_erro,'debug');
					//continua o processo para não travar
					continue;
				}// fim verificacao colunas com registros

				// $column[$line] = $columns;
				$query_insert .= $query_base_insert . "(".implode(",",$columns).");\n";
				
				#// //$this->log(print_r($modelOf,1),'debug');
				#// $saved = $modelOf->incluir($columns);
				#// if (!$saved) {
				#// 	#//verificar os erros e retornar a planilha de erros
				#// 	throw new Exception(
				#// 		"Falha ao salvar registros, por favor tente novamente mais tarde"
				#// 	);
				#// }

			}//fim foreach

			if($tapCount == $this->lineCountTap) {
				$tapCount = 0;
				// $this->log($query_insert, 'folha_pagto_stage');
				$this->query($query_insert);
				$query_insert = '';
				$this->log("Inserindo " . $this->lineCountTap, 'debug');
				
				if(is_null($onLineProceed) == false) {
					$onLineProceed($line);
				}

				// debug($query_insert);
				// exit;
			}


		}//fim while
		// $this->log($query_insert,"debug");
		// exit;
		// debug($query_insert);exit;
		fclose($stream);

		//inserindo o que resta do arquivo
		// $this->log($query_insert, 'folha_pagto_stage');
		$this->query($query_insert);
		$query_insert = '';
		$this->log("Inserindo " . $tapCount, 'debug');

		if(is_null($onLineProceed) == false) {
			$onLineProceed($line);
		}

		return true;
	}

	/**
	 * [buscaArquivos busca os arquivos da tabela, e caso tenha um codigo filtra por ele]
	 * @param  [type] $tabela [description]
	 * @param  [type] $codigo [description]
	 * @return [type]         [description]
	 */
	public function buscaArquivos($tabela, $codigo_int_upload_cliente = null)
	{
		$where = '';
		if(!empty($codigo_int_upload_cliente)) {
			$where = " AND t.codigo_int_upload_cliente = {$codigo_int_upload_cliente} ";
		}
		
		$query = "SELECT t.codigo_int_upload_cliente FROM {$tabela} t inner join int_upload_cliente up on t.codigo_int_upload_cliente = up.codigo WHERE t.codigo_status_transferencia = 3 AND t.ativo = 1 AND up.ativo = 1 {$where} GROUP BY t.codigo_int_upload_cliente;";
		// debug($query);
		$dados = $this->query($query);

		return $dados;

	}//fim buscaArquivos

	public function getDadosErros($codigo_int_upload_cliente, $tabela)
	{
		
		$query = "SELECT *
				  FROM {$tabela} 
				  WHERE codigo_status_transferencia = 6 
				  	AND ativo = 1 
				  	AND codigo_int_upload_cliente = {$codigo_int_upload_cliente}";

		$dados = $this->query($query);
		return $dados;
	}//fim getDadosErros


	/**
	 * [int_cliente_empresa metodo para pegar os dados que precisam ser carregados como clientes]
	 * @return [type] [description]
	 */
	public function int_cliente_empresa($codigo_int_upload_cliente = null, $codigo_int_cliente_empresa = null)
	{
		
		//$this->log("INICIANDO PROCESSO DE EMPRESAS","debug");

		//pega os arquivos que devem ser processados
		$arquivos = $this->buscaArquivos('int_cliente_empresa',$codigo_int_upload_cliente);
		
		if(!empty($arquivos)) {

			$this->IntClienteEmpresa =& ClassRegistry::init('IntClienteEmpresa');

			foreach($arquivos AS $arq) {

				$codigo_int_upload_cliente = $arq[0]['codigo_int_upload_cliente'];

				//atualiza o codigo do arquivo na tabela upload 
				$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
				$this->troca_status(4, $dados_int_upload_cliente);//incluindo estrutura


				//$this->log("CODIGO UPLOAD CLIENTE: " . $codigo_int_upload_cliente . "","debug");
				//$this->log("CODIGO CLIENT EMPRESA: " . $codigo_int_cliente_empresa . "","debug");

				$retorno_erros = $this->IntClienteEmpresa->set_clientes($codigo_int_upload_cliente,$codigo_int_cliente_empresa);

				if(!empty($retorno_erros)) {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->troca_status(6, $dados_int_upload_cliente);//Importacao Estrutura falhou
				}
				else {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->troca_status(8, $dados_int_upload_cliente);//importacao estrutura processado
				}

			}

		}

		//$this->log("FINALIZANDO PROCESSO DE EMPRESAS ","debug");

	}//fim int_cliente_empresa

	/**
	 * [int_cliente_setores metodo para pegar os dados que precisam ser carregados]
	 * @return [type] [description]
	 */
	public function int_cliente_setores($codigo_int_upload_cliente = null,$codigo_int_cliente_setores = null)
	{
		//$this->log("INICIANDO PROCESSO DE SETORES","debug");
		
		//pega os arquivos que devem ser processados
		$arquivos = $this->buscaArquivos('int_cliente_setores',$codigo_int_upload_cliente);
		// debug($arquivos);exit;

		if(!empty($arquivos)) {

			$this->IntClienteSetores =& ClassRegistry::init('IntClienteSetores');

			foreach($arquivos AS $arq) {

				$codigo_int_upload_cliente = $arq[0]['codigo_int_upload_cliente'];

				//atualiza o codigo do arquivo na tabela upload 
				$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
				$this->troca_status(4, $dados_int_upload_cliente);//incluindo estrutura


				//$this->log("CODIGO UPLOAD CLIENTE: " . $codigo_int_upload_cliente . "","debug");
				//$this->log("CODIGO CLIENT SETOR: " . $codigo_int_cliente_setores . "","debug");

				//importa a estrtutra
				$retorno_erros = $this->IntClienteSetores->set_setor($codigo_int_upload_cliente,$codigo_int_cliente_setores);

				if(!empty($retorno_erros)) {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->troca_status(6, $dados_int_upload_cliente);//Importacao Estrutura falhou
				}
				else {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->troca_status(8, $dados_int_upload_cliente);//importacao estrutura processado
				}

			}

		}

		//$this->log("FINALIZANDO PROCESSO DE SETORES ","debug");

	}//fim int_cliente_setores


	/**
	 * [int_cliente_cargos metodo para pegar os dados que precisam ser carregados]
	 * @return [type] [description]
	 */
	public function int_cliente_cargos($codigo_int_upload_cliente = null, $codigo_int_cliente_cargos = null)
	{
		//$this->log("INICIANDO PROCESSO DE CARGOS","debug");

		//pega os arquivos que devem ser processados
		$arquivos = $this->buscaArquivos('int_cliente_cargos',$codigo_int_upload_cliente);
		// debug($arquivos);exit;

		if(!empty($arquivos)) {

			$this->IntClienteCargos =& ClassRegistry::init('IntClienteCargos');

			foreach($arquivos AS $arq) {

				$codigo_int_upload_cliente = $arq[0]['codigo_int_upload_cliente'];

				//atualiza o codigo do arquivo na tabela upload 
				$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
				$this->troca_status(4, $dados_int_upload_cliente);//incluindo estrutura


				//$this->log("CODIGO UPLOAD CLIENTE: " . $codigo_int_upload_cliente . "","debug");
				//$this->log("CODIGO CLIENT CARGO: " . $codigo_int_cliente_cargos . "","debug");

				$retorno_erros = $this->IntClienteCargos->set_cargo($codigo_int_upload_cliente,$codigo_int_cliente_cargos);

				if(!empty($retorno_erros)) {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->troca_status(6, $dados_int_upload_cliente);//Importacao Estrutura falhou
				}
				else {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->troca_status(8, $dados_int_upload_cliente);//importacao estrutura processado
				}

			}

		}

		//$this->log("FINALIZANDO PROCESSO DE CARGOS ","debug");

	}//fim int_cliente_cargos

	/**
	 * [int_cliente_centro_resultado metodo para pegar os dados que precisam ser carregados]
	 * @return [type] [description]
	 */
	public function int_cliente_centro_resultado($codigo_int_upload_cliente = null,$codigo_int_cliente_centro_resultado = null)
	{
		//$this->log("INICIANDO PROCESSO DE Centro de Resultado","debug");
		
		//pega os arquivos que devem ser processados
		$arquivos = $this->buscaArquivos('int_cliente_centro_resultado',$codigo_int_upload_cliente);
		// debug($arquivos);exit;

		if(!empty($arquivos)) {

			$this->IntClienteCentroResultado =& ClassRegistry::init('IntClienteCentroResultado');

			foreach($arquivos AS $arq) {

				$codigo_int_upload_cliente = $arq[0]['codigo_int_upload_cliente'];

				//atualiza o codigo do arquivo na tabela upload 
				$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
				$this->troca_status(4, $dados_int_upload_cliente);//incluindo estrutura


				//$this->log("CODIGO UPLOAD CLIENTE: " . $codigo_int_upload_cliente . "","debug");
				//$this->log("CODIGO CLIENT Centro de Resultado: " . $codigo_int_cliente_centro_resultado . "","debug");

				$retorno_erros = $this->IntClienteCentroResultado->set_centro_resultado($codigo_int_upload_cliente,$codigo_int_cliente_centro_resultado);

				if(!empty($retorno_erros)) {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->troca_status(6, $dados_int_upload_cliente);//Importacao Estrutura falhou
				}
				else {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->troca_status(8, $dados_int_upload_cliente);//importacao estrutura processado
				}

			}

		}

		//$this->log("FINALIZANDO PROCESSO DE Centro de Resultado ","debug");

	}//fim int_cliente_centro_resultado

	/**
	 * [int_cliente_funcionario metodo para pegar os dados que precisam ser carregados]
	 * @return [type] [description]
	 */
	public function int_cliente_funcionarios($codigo_int_upload_cliente = null, $codigo_int_cliente_funcionario = null)
	{
		//$this->log("INICIANDO PROCESSO DE FUNCIONARIOS","debug");

		//pega os arquivos que devem ser processados
		$arquivos = $this->buscaArquivos('int_cliente_funcionarios',$codigo_int_upload_cliente);
		// debug($arquivos);exit;

		if(!empty($arquivos)) {
			$this->IntClienteFuncionarios =& ClassRegistry::init('IntClienteFuncionarios');

			foreach($arquivos AS $arq) {

				$codigo_int_upload_cliente = $arq[0]['codigo_int_upload_cliente'];

				//atualiza o codigo do arquivo na tabela upload 
				$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
				$this->troca_status(4, $dados_int_upload_cliente);//incluindo estrutura


				//$this->log("CODIGO UPLOAD CLIENTE: " . $codigo_int_upload_cliente . "","debug");
				//$this->log("CODIGO CLIENT FUNCIONARIOS: " . $codigo_int_cliente_funcionario . "","debug");

				$retorno_erros = $this->IntClienteFuncionarios->set_funcionario($codigo_int_upload_cliente,$codigo_int_cliente_funcionario);
				
				if(!empty($retorno_erros)) {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->troca_status(6, $dados_int_upload_cliente);//Importacao Estrutura falhou
				}
				else {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->troca_status(8, $dados_int_upload_cliente);//importacao estrutura processado
				}

			}

		}

		//$this->log("FINALIZANDO PROCESSO DE FUNCIONARIOS ","debug");

	}//fim int_cliente_funcionario

	/**
	 * [int_cliente_funcionario_empresa metodo para pegar os dados que precisam ser carregados]
	 * @return [type] [description]
	 */
	public function int_cliente_funcionarios_empresa($codigo_int_upload_cliente =null, $codigo_int_cliente_funcionario_empresa = null)
	{
		//$this->log("INICIANDO PROCESSO DE FUNCIONARIOSxEMPRESA","debug");
		 
		//pega os arquivos que devem ser processados
		$arquivos = $this->buscaArquivos('int_cliente_funcionarios_empresa',$codigo_int_upload_cliente);
		// debug($arquivos);exit;

		if(!empty($arquivos)) {

			$this->IntClienteFe =& ClassRegistry::init('IntClienteFe');

			foreach($arquivos AS $arq) {

				$codigo_int_upload_cliente = $arq[0]['codigo_int_upload_cliente'];

				//atualiza o codigo do arquivo na tabela upload 
				$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
				$this->troca_status(4, $dados_int_upload_cliente);//incluindo estrutura

				//$this->log("CODIGO UPLOAD CLIENTE: " . $codigo_int_upload_cliente . "","debug");
				//$this->log("CODIGO CLIENT FUNCIONARIOSxEMPRESA: " . $codigo_int_cliente_funcionario_empresa . "","debug");

				$retorno_erros = $this->IntClienteFe->set_funcionario_empresa($codigo_int_upload_cliente,$codigo_int_cliente_funcionario_empresa);
				
				if(!empty($retorno_erros)) {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->troca_status(6, $dados_int_upload_cliente);//Importacao Estrutura falhou
				}
				else {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->troca_status(8, $dados_int_upload_cliente);//importacao estrutura processado
				}

			}

		}

		//$this->log("FINALIZANDO PROCESSO DE FUNCIONARIOSXEMPRESA","debug");

	}//fim int_cliente_funcionario_empresa

}
