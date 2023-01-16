<?php

class ImportacaoFolhaPagtoShell extends Shell {
	var $uses = array(
		'IntUploadCliente',
		'IntClienteEmpresa',
		'IntClienteSetores',
		'IntClienteCargos',
		'IntClienteCentroResultado',
		'IntClienteFuncionarios',
		'IntClienteFe'
	);
	var $arquivo;

	function main() {
		
		if ($this->im_running()) {
            echo "Já existe importação folha de pagto em andamento"."\n";
            exit;
        }

		echo "*******************************************************************\n";
		echo "* Importação de Arquivos da folha de pagamento \n";
		echo "*******************************************************************\n";


		echo "PEGANDO OS ARQUIVOS CARREGADOS DE EMPRESAS \n";
		$this->int_cliente_empresa();

		echo "PEGANDO OS ARQUIVOS CARREGADOS DE SETORES \n";
		$this->int_cliente_setores();

		echo "PEGANDO OS ARQUIVOS CARREGADOS DE CARGOS \n";
		$this->int_cliente_cargos();

		echo "PEGANDO OS ARQUIVOS CARREGADOS DE CENTRO RESULTADO \n";
		$this->int_cliente_centro_resultado();

		echo "PEGANDO OS ARQUIVOS CARREGADOS DE FUNCIONARIOS \n";
		$this->int_cliente_funcionarios();

		echo "PEGANDO OS ARQUIVOS CARREGADOS DE MATRICULA \n";
		$this->int_cliente_funcionarios_empresa();

	}

	private function im_running() {
		if (PHP_OS!='WINNT') {
			$cmd = shell_exec("ps aux | grep 'importacao_folha_pagto'");
			$ret = substr_count($cmd, 'cake.php -working') > 1;
		} else {
			$cmd = `tasklist /v | findstr /R /C:"importacao_folha_pagto"`;
			$ret = substr_count($cmd, 'cake\console\cake') > 1;
		}
		
	}


	/**
	 * [int_cliente_empresa metodo para pegar os dados que precisam ser carregados como clientes]
	 * @return [type] [description]
	 */
	public function int_cliente_empresa()
	{
		echo "INICIANDO PROCESSO DE EMPRESAS\n";
		
		$codigo_int_upload_cliente = (isset($this->args[0])) ? $this->args[0] : null;
		$codigo_int_cliente_empresa = (isset($this->args[1])) ? $this->args[1] : null;



		//pega os arquivos que devem ser processados
		$arquivos = $this->IntUploadCliente->buscaArquivos('int_cliente_empresa',$codigo_int_upload_cliente);
		// debug($arquivos);exit;

		if(!empty($arquivos)) {

			foreach($arquivos AS $arq) {

				$codigo_int_upload_cliente = $arq[0]['codigo_int_upload_cliente'];

				//atualiza o codigo do arquivo na tabela upload 
				$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
				$this->IntUploadCliente->troca_status(4, $dados_int_upload_cliente);//incluindo estrutura


				echo "CODIGO UPLOAD CLIENTE: " . $codigo_int_upload_cliente . "\n";
				echo "CODIGO CLIENT EMPRESA: " . $codigo_int_cliente_empresa . "\n";

				$retorno_erros = $this->IntClienteEmpresa->set_clientes($codigo_int_upload_cliente,$codigo_int_cliente_empresa);

				if(!empty($retorno_erros)) {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->IntUploadCliente->troca_status(6, $dados_int_upload_cliente);//Importacao Estrutura falhou
				}
				else {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->IntUploadCliente->troca_status(8, $dados_int_upload_cliente);//importacao estrutura processado
				}

			}

		}

		echo "FINALIZANDO PROCESSO DE EMPRESAS \n";

	}//fim int_cliente_empresa

	/**
	 * [int_cliente_setores metodo para pegar os dados que precisam ser carregados]
	 * @return [type] [description]
	 */
	public function int_cliente_setores()
	{
		echo "INICIANDO PROCESSO DE SETORES\n";
		
		$codigo_int_upload_cliente = (isset($this->args[0])) ? $this->args[0] : null;
		$codigo_int_cliente_setores = (isset($this->args[1])) ? $this->args[1] : null;


		//pega os arquivos que devem ser processados
		$arquivos = $this->IntUploadCliente->buscaArquivos('int_cliente_setores',$codigo_int_upload_cliente);
		// debug($arquivos);exit;

		if(!empty($arquivos)) {

			foreach($arquivos AS $arq) {

				$codigo_int_upload_cliente = $arq[0]['codigo_int_upload_cliente'];

				//atualiza o codigo do arquivo na tabela upload 
				$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
				$this->IntUploadCliente->troca_status(4, $dados_int_upload_cliente);//incluindo estrutura


				echo "CODIGO UPLOAD CLIENTE: " . $codigo_int_upload_cliente . "\n";
				echo "CODIGO CLIENT SETOR: " . $codigo_int_cliente_setores . "\n";

				//importa a estrtutra
				$retorno_erros = $this->IntClienteSetores->set_setor($codigo_int_upload_cliente,$codigo_int_cliente_setores);

				if(!empty($retorno_erros)) {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->IntUploadCliente->troca_status(6, $dados_int_upload_cliente);//Importacao Estrutura falhou
				}
				else {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->IntUploadCliente->troca_status(8, $dados_int_upload_cliente);//importacao estrutura processado
				}

			}

		}

		echo "FINALIZANDO PROCESSO DE SETORES \n";

	}//fim int_cliente_setores


	/**
	 * [int_cliente_cargos metodo para pegar os dados que precisam ser carregados]
	 * @return [type] [description]
	 */
	public function int_cliente_cargos()
	{
		echo "INICIANDO PROCESSO DE CARGOS\n";
		
		$codigo_int_upload_cliente = (isset($this->args[0])) ? $this->args[0] : null;
		$codigo_int_cliente_cargos = (isset($this->args[1])) ? $this->args[1] : null;

		//pega os arquivos que devem ser processados
		$arquivos = $this->IntUploadCliente->buscaArquivos('int_cliente_cargos',$codigo_int_upload_cliente);
		// debug($arquivos);exit;

		if(!empty($arquivos)) {

			foreach($arquivos AS $arq) {

				$codigo_int_upload_cliente = $arq[0]['codigo_int_upload_cliente'];

				//atualiza o codigo do arquivo na tabela upload 
				$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
				$this->IntUploadCliente->troca_status(4, $dados_int_upload_cliente);//incluindo estrutura


				echo "CODIGO UPLOAD CLIENTE: " . $codigo_int_upload_cliente . "\n";
				echo "CODIGO CLIENT CARGO: " . $codigo_int_cliente_cargos . "\n";

				$retorno_erros = $this->IntClienteCargos->set_cargo($codigo_int_upload_cliente,$codigo_int_cliente_cargos);

				if(!empty($retorno_erros)) {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->IntUploadCliente->troca_status(6, $dados_int_upload_cliente);//Importacao Estrutura falhou
				}
				else {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->IntUploadCliente->troca_status(8, $dados_int_upload_cliente);//importacao estrutura processado
				}

			}

		}

		echo "FINALIZANDO PROCESSO DE CARGOS \n";

	}//fim int_cliente_cargos

	/**
	 * [int_cliente_centro_resultado metodo para pegar os dados que precisam ser carregados]
	 * @return [type] [description]
	 */
	public function int_cliente_centro_resultado()
	{
		echo "INICIANDO PROCESSO DE Centro de Resultado\n";
		
		$codigo_int_upload_cliente = (isset($this->args[0])) ? $this->args[0] : null;
		$codigo_int_cliente_centro_resultado = (isset($this->args[1])) ? $this->args[1] : null;

		//pega os arquivos que devem ser processados
		$arquivos = $this->IntUploadCliente->buscaArquivos('int_cliente_centro_resultado',$codigo_int_upload_cliente);
		// debug($arquivos);exit;

		if(!empty($arquivos)) {

			foreach($arquivos AS $arq) {

				$codigo_int_upload_cliente = $arq[0]['codigo_int_upload_cliente'];

				//atualiza o codigo do arquivo na tabela upload 
				$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
				$this->IntUploadCliente->troca_status(4, $dados_int_upload_cliente);//incluindo estrutura


				echo "CODIGO UPLOAD CLIENTE: " . $codigo_int_upload_cliente . "\n";
				echo "CODIGO CLIENT Centro de Resultado: " . $codigo_int_cliente_centro_resultado . "\n";

				$retorno_erros = $this->IntClienteCentroResultado->set_centro_resultado($codigo_int_upload_cliente,$codigo_int_cliente_centro_resultado);

				if(!empty($retorno_erros)) {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->IntUploadCliente->troca_status(6, $dados_int_upload_cliente);//Importacao Estrutura falhou
				}
				else {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->IntUploadCliente->troca_status(8, $dados_int_upload_cliente);//importacao estrutura processado
				}

			}

		}

		echo "FINALIZANDO PROCESSO DE Centro de Resultado \n";

	}//fim int_cliente_centro_resultado

	/**
	 * [int_cliente_funcionario metodo para pegar os dados que precisam ser carregados]
	 * @return [type] [description]
	 */
	public function int_cliente_funcionarios()
	{
		echo "INICIANDO PROCESSO DE FUNCIONARIOS\n";
		
		$codigo_int_upload_cliente = (isset($this->args[0])) ? $this->args[0] : null;
		$codigo_int_cliente_funcionario = (isset($this->args[1])) ? $this->args[1] : null;

		//pega os arquivos que devem ser processados
		$arquivos = $this->IntUploadCliente->buscaArquivos('int_cliente_funcionarios',$codigo_int_upload_cliente);
		// debug($arquivos);exit;

		if(!empty($arquivos)) {

			foreach($arquivos AS $arq) {

				$codigo_int_upload_cliente = $arq[0]['codigo_int_upload_cliente'];

				//atualiza o codigo do arquivo na tabela upload 
				$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
				$this->IntUploadCliente->troca_status(4, $dados_int_upload_cliente);//incluindo estrutura


				echo "CODIGO UPLOAD CLIENTE: " . $codigo_int_upload_cliente . "\n";
				echo "CODIGO CLIENT FUNCIONARIOS: " . $codigo_int_cliente_funcionario . "\n";

				$retorno_erros = $this->IntClienteFuncionarios->set_funcionario($codigo_int_upload_cliente,$codigo_int_cliente_funcionario);
				
				if(!empty($retorno_erros)) {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->IntUploadCliente->troca_status(6, $dados_int_upload_cliente);//Importacao Estrutura falhou
				}
				else {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->IntUploadCliente->troca_status(8, $dados_int_upload_cliente);//importacao estrutura processado
				}

			}

		}

		echo "FINALIZANDO PROCESSO DE FUNCIONARIOS \n";

	}//fim int_cliente_funcionario

	/**
	 * [int_cliente_funcionario_empresa metodo para pegar os dados que precisam ser carregados]
	 * @return [type] [description]
	 */
	public function int_cliente_funcionarios_empresa()
	{
		echo "INICIANDO PROCESSO DE FUNCIONARIOSxEMPRESA\n";
		
		$codigo_int_upload_cliente = (isset($this->args[0])) ? $this->args[0] : null;
		$codigo_int_cliente_funcionario_empresa = (isset($this->args[1])) ? $this->args[1] : null;

		//pega os arquivos que devem ser processados
		$arquivos = $this->IntUploadCliente->buscaArquivos('int_cliente_funcionarios_empresa',$codigo_int_upload_cliente);
		// debug($arquivos);exit;

		if(!empty($arquivos)) {

			foreach($arquivos AS $arq) {

				$codigo_int_upload_cliente = $arq[0]['codigo_int_upload_cliente'];

				//atualiza o codigo do arquivo na tabela upload 
				$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
				$this->IntUploadCliente->troca_status(4, $dados_int_upload_cliente);//incluindo estrutura

				echo "CODIGO UPLOAD CLIENTE: " . $codigo_int_upload_cliente . "\n";
				echo "CODIGO CLIENT FUNCIONARIOSxEMPRESA: " . $codigo_int_cliente_funcionario_empresa . "\n";

				$retorno_erros = $this->IntClienteFe->set_funcionario_empresa($codigo_int_upload_cliente,$codigo_int_cliente_funcionario_empresa);
				
				if(!empty($retorno_erros)) {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->IntUploadCliente->troca_status(6, $dados_int_upload_cliente);//Importacao Estrutura falhou
				}
				else {
					//atualiza o codigo do arquivo na tabela upload 
					$dados_int_upload_cliente['IntUploadCliente']['codigo'] = $codigo_int_upload_cliente;
					$this->IntUploadCliente->troca_status(8, $dados_int_upload_cliente);//importacao estrutura processado
				}

			}

		}

		echo "FINALIZANDO PROCESSO DE FUNCIONARIOSXEMPRESA\n";

	}//fim int_cliente_funcionario_empresa


	#### logica para processamento
		//implementar lógica para ordenação de processamento e aguardar todos os anteriores para processar o transacional
		//usar o status e data para controlar dentro do foreach da linha 144
		####
	/**
	 * [processing_flow return by customer which process is it in, and which is the next]
	 * @return [array] [return array('current' => 'int_cliente_empresa','next' => 'int_cliente_setores')]
	 */
	public function processing_flow($codigo_cliente = false)
	{

		$return_data = array();

		//order processing flow
		$order_process = array(
			'1'=> array(
				'table' => 'int_cliente_empresa',
				'parallel' => true
			),
			'2'=> array(
				'table' => 'int_cliente_setores',
				'required' => false
			),
			'3'=> array(
				'table' => 'int_cliente_cargos',
				'required' => false
			),
			'4'=> array(
				'table' => 'int_cliente_centro_resultado',
				'required' => false
			),
			'5'=> array(
				'table' => 'int_cliente_funcionarios',
				'required' => false
			),
			'6'=> array(
				'table' => 'int_cliente_funcionarios_empresa',
				'required' => true
			),
		);

		//verify the codigo_cliente
		$conditions = array();
		if($codigo_cliente) {
			$conditions['IntUploadCliente.codigo_cliente'] = $codigo_cliente;
		}//end if codigo_cliente

		//set the conditions in query
		$conditions['IntUploadCliente.ativo'] = 1;
		$conditions['IntUploadCliente.codigo_status_transferencia'] = array(1,4);

		$datas = $this->IntUploadCliente->find('all',array('conditions' => $conditions));

		if(!empty($datas)) {

			


			debug($datas);
		}


		return $return_data;


	}//end processing_flow
}
?>
