<?php
App::import('Model', 'RemessaBancaria');
class RemessaBancariaTestCase extends CakeTestCase {
	public $arquivoRem = "";
	public $arquivoRet = "";
	public $fixtures = array(
		'app.multi_empresa',
		'app.remessa_bancaria',
		'app.remessa_status',
		'app.remessa_retorno',
		'app.cliente',
		'app.cliente_endereco',
		'app.uvw_endereco',
		'app.cliente_implantacao',
		'app.grupo_economico',
		'app.grupo_economico_cliente',
		'app.documento',
		'app.tipo_contato',
		'app.cliente_log',
		'app.endereco',
		'app.endereco_cidade',
		'app.endereco_estado',
		'app.endereco_cep',
		'app.endereco_bairro',
		'app.endereco_tipo',
		'app.log_apigoogle',
		'app.cliente_endereco_log',
		'app.pedidos',
		'app.logs_integracoes',
		'app.carga_tipo',
		'app.bairro',
		'app.configuracao',
		'app.produto_servico',
		'app.produto',
		'app.servico',
		'app.item_pedido',
		'app.detalhe_item_pedido_manual',
		'app.carga_tipo',
		'app.last_id'
		);
	public function startTest() 
	{
		$this->RemessaBancaria =& ClassRegistry::init('RemessaBancaria');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}
	public function testLerRemessa() 
	{
		$this->ler_remessa();
		$results = $this->RemessaBancaria->find('list', array('fields' => array('nosso_numero','nome_pagador'), 'conditions' => array('nosso_numero'=>'00010430')));
		$expected = array('00010430' => 'ROSANGELA C. B. DA CONCEICAO');
		$this->assertEqual($results, $expected);
	}//fim testLerRemessa
	public function ler_remessa()
	{
		$this->RemessaBancaria->query("IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[remover_acentos]') AND type in (N'FN', N'IF', N'TF', N'FS', N'FT')) DROP FUNCTION [dbo].[remover_acentos]");
		$this->RemessaBancaria->query("	CREATE FUNCTION [dbo].[remover_acentos] (@valor VARCHAR(MAX))
										RETURNS VARCHAR(MAX)
										AS
										BEGIN
										  RETURN @valor COLLATE sql_latin1_general_cp1251_ci_as
										END");
		//caminho onde será gerado o arquivo para realizar o download
		$path = TMP;
		$this->arquivoRem = $path."REMESSA_TEST.REM";
		//copia o arquivo
		copy(APP.'/tests/fixtures/arquivos/C170609A.TXT', $this->arquivoRem);
		//executa a remessa bancaria para inserir na tabela corretamente
		$this->RemessaBancaria->lerArquivo($this->arquivoRem);
	}
	public function testLerRetorno()
	{
		$this->ler_remessa();
		//caminho onde será gerado o arquivo para realizar o download
		$path = TMP;
		$this->arquivoRet = $path."RETORNO_TEST.RET";
		//copia o arquivo
		copy(APP.'/tests/fixtures/arquivos/CN09067A.RET', $this->arquivoRet);
		//executa o processo de retorno
		$this->RemessaBancaria->lerArquivo($this->arquivoRet);
		//results de registro da remessa bancaria
		$results = $this->RemessaBancaria->find('list', array('fields' => array('nosso_numero','nome_pagador','codigo_remessa_retorno'), 'conditions' => array('nosso_numero'=>'00009607')));
		$expected = array('2' => array('00009607' => 'Marcio Jose da Silva'));
		$this->assertEqual($results, $expected);
		//results de registro da remessa bancaria
		$results = $this->RemessaBancaria->find('list', array('fields' => array('nosso_numero','nome_pagador','codigo_remessa_retorno'), 'conditions' => array('nosso_numero'=>'00010430')));
		// pr($results);
		$expected = array('6' => array('00010430' => 'ROSANGELA C. B. DA CONCEICAO'));
		$this->assertEqual($results, $expected);
	} //fim testLerRetorno
	public function endTest() {
		$this->RemessaBancaria->query("IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[remover_acentos]') AND type in (N'FN', N'IF', N'TF', N'FS', N'FT')) DROP FUNCTION [dbo].[remover_acentos]");
		unset($this->arquivoRem);
		unset($this->arquivoRet);
		unset($this->RemessaBancaria);
		ClassRegistry::flush();
	}
}