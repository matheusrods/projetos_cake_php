<?php
App::import('Model', 'ClienteContato');
class ClienteContatoTestCase extends CakeTestCase {
	public $fixtures = array(
		'app.cliente_contato',
		'app.tipo_contato',
		'app.tipo_retorno',
		'app.cliente',
		'app.cliente_contato_log'
		);

	public function startTest() {
		$this->ClienteContato = & ClassRegistry::init('ClienteContato');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}

	public function testBindLazy() {
		$this->ClienteContato->bindLazy();
	}

	public function testContatosDoCliente() {
		$validacao = array (
			0 => 
			array (
				'ClienteContato' => 
				array (
					'codigo' => 258,
					'codigo_cliente' => 2302,
					'codigo_tipo_contato' => 6,
					'codigo_tipo_retorno' => 3,
					'ddi' => NULL,
					'ddd' => 11,
					'ramal' => NULL,
					'codigo_usuario_inclusao' => 66988,
					'codigo_empresa' => 1,
					'data_inclusao' => '05/09/2016 10:15:44',
					'descricao' => '34349595',
					'nome' => 'TIO PATINHAS',
					),
				'TipoContato' => 
				array (
					'codigo' => 6,
					'codigo_usuario_inclusao' => 2,
					'data_inclusao' => '27/11/2015 08:21:32',
					'descricao' => 'REPRESENTANTE',
					),
				'TipoRetorno' => 
				array (
					'codigo' => 3,
					'codigo_usuario_inclusao' => 2,
					'cliente' => 0,
					'proprietario' => 0,
					'profissional' => 0,
					'usuario_interno' => 0,
					'data_inclusao' => '27/11/2015 08:22:36',
					'descricao' => 'FAX',
					),
				),
			1 => 
			array (
				'ClienteContato' => 
				array (
					'codigo' => 257,
					'codigo_cliente' => 2302,
					'codigo_tipo_contato' => 3,
					'codigo_tipo_retorno' => 2,
					'ddi' => NULL,
					'ddd' => NULL,
					'ramal' => NULL,
					'codigo_usuario_inclusao' => 66988,
					'codigo_empresa' => 1,
					'data_inclusao' => '05/09/2016 10:15:07',
					'descricao' => 'GCC@UOL.COM.BR',
					'nome' => 'GABRIELA CRAVO E CANELA',
					),
				'TipoContato' => 
				array (
					'codigo' => 3,
					'codigo_usuario_inclusao' => 2,
					'data_inclusao' => '27/11/2015 08:21:32',
					'descricao' => 'FINANCEIRO',
					),
				'TipoRetorno' => 
				array (
					'codigo' => 2,
					'codigo_usuario_inclusao' => 2,
					'cliente' => 1,
					'proprietario' => 1,
					'profissional' => 0,
					'usuario_interno' => 0,
					'data_inclusao' => '27/11/2015 08:22:36',
					'descricao' => 'E-MAIL',
					),
				),
			2 => 
			array (
				'ClienteContato' => 
				array (
					'codigo' => 256,
					'codigo_cliente' => 2302,
					'codigo_tipo_contato' => 3,
					'codigo_tipo_retorno' => 4,
					'ddi' => NULL,
					'ddd' => NULL,
					'ramal' => NULL,
					'codigo_usuario_inclusao' => 66988,
					'codigo_empresa' => 1,
					'data_inclusao' => '05/09/2016 10:15:06',
					'descricao' => '3225144',
					'nome' => 'GABRIELA CRAVO E CANELA',
					),
				'TipoContato' => 
				array (
					'codigo' => 3,
					'codigo_usuario_inclusao' => 2,
					'data_inclusao' => '27/11/2015 08:21:32',
					'descricao' => 'FINANCEIRO',
					),
				'TipoRetorno' => 
				array (
					'codigo' => 4,
					'codigo_usuario_inclusao' => 2,
					'cliente' => 1,
					'proprietario' => 1,
					'profissional' => 0,
					'usuario_interno' => 0,
					'data_inclusao' => '27/11/2015 08:22:36',
					'descricao' => '0800',
					),
				),
			);

		$retorno = $this->ClienteContato->contatosDoCliente(2302, null);
		$this->assertEqual($retorno, $validacao);

		$validacao = array (
			0 => 
			array (
				'ClienteContato' => 
				array (
					'codigo' => 257,
					'codigo_cliente' => 2302,
					'codigo_tipo_contato' => 3,
					'codigo_tipo_retorno' => 2,
					'ddi' => NULL,
					'ddd' => NULL,
					'ramal' => NULL,
					'codigo_usuario_inclusao' => 66988,
					'codigo_empresa' => 1,
					'data_inclusao' => '05/09/2016 10:15:07',
					'descricao' => 'GCC@UOL.COM.BR',
					'nome' => 'GABRIELA CRAVO E CANELA',
					),
				'TipoContato' => 
				array (
					'codigo' => 3,
					'codigo_usuario_inclusao' => 2,
					'data_inclusao' => '27/11/2015 08:21:32',
					'descricao' => 'FINANCEIRO',
					),
				'TipoRetorno' => 
				array (
					'codigo' => 2,
					'codigo_usuario_inclusao' => 2,
					'cliente' => 1,
					'proprietario' => 1,
					'profissional' => 0,
					'usuario_interno' => 0,
					'data_inclusao' => '27/11/2015 08:22:36',
					'descricao' => 'E-MAIL',
					),
				),
			);

		$retorno = $this->ClienteContato->contatosDoCliente(2302, 2);
		$this->assertEqual($retorno, $validacao);
	}

	public function testEmailsFinanceirosPorCliente($value='') {
		$validacao = array (
			0 => 'bsat_rota90@buoony.com.br',
			);
		$retorno = $this->ClienteContato->emailsFinanceirosPorCliente(2298);
		$this->assertEqual($retorno, $validacao);

		$validacao = array (
			0 => 'cobranca@rhhealth.com.br',
			);

		$retorno = $this->ClienteContato->emailsFinanceirosPorCliente(2297, true);
		$this->assertEqual($retorno, $validacao);
	}

	public function testRetornaTodosEmailsFinanceirosPorCliente() {
		$validacao = array (
			0 => 
			array (
				'ClienteContato' => 
				array (
					'codigo' => 250,
					'codigo_cliente' => 2298,
					'nome' => 'Rota 90',
					'descricao' => 'bsat_rota90@buoony.com.br',
					'data_inclusao' => '17/08/2016 08:51:48',
					),
				),
			);
		$retorno = $this->ClienteContato->retornaTodosEmailsFinanceirosPorCliente(2298);
		$this->assertEqual($retorno, $validacao);
	}

	public function testIncluirContato() {
		$dados = array (
			0 => 
			array (
				'ClienteContato' => 
				array (
					'codigo' => '',
					'codigo_cliente' => '2297',
					'nome' => 'Teste',
					'codigo_tipo_contato' => 
					array (
						0 => '2',
						1 => '1',
						),
					'codigo_tipo_retorno' => '1',
					'descricao' => '12341234',
					'ddd' => '11',
					),
				),
			1 => 
			array (
				'ClienteContato' => 
				array (
					'codigo' => '',
					'codigo_cliente' => '2297',
					'nome' => 'Teste 2',
					'codigo_tipo_contato' => 
					array (
						0 => '2',
						1 => '1',
						),
					'codigo_tipo_retorno' => '1',
					'descricao' => '12341234',
					'ddd' => '11',
					),
				),
			);

		$this->assertTrue($this->ClienteContato->incluirContato($dados));
	}

	public function endTest() {
		unset($this->ClienteContato);
		ClassRegistry::flush();
	}
}
?>