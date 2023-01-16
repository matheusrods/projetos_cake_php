<?php
App::import('Model', 'Cliente');
class ClienteTestCase extends CakeTestCase {
	public $fixtures = array(
		'app.cliente',
		'app.grupo_economico',
		'app.grupo_economico_cliente',
		'app.cliente_implantacao',
		'app.documento',
		'app.vendereco',
		'app.tipo_contato',
		'app.endereco',
		'app.cliente_endereco',
		'app.endereco_regiao',
		'app.usuario',
		'app.usuarios_dados',
		'app.cliente_log',
		'app.endereco_cidade',
		'app.endereco_estado',
		'app.endereco_cep',
		'app.endereco_bairro',
		'app.endereco_tipo',
		'app.log_apigoogle',
		'app.cliente_endereco_log',
		'app.last_id',
		'app.grupo_economico_log',
		'app.grupo_economico_cliente_log'
		);

	public function startTest() {

		$this->Cliente =& ClassRegistry::init('Cliente');
		$this->VEndereco =& ClassRegistry::init('VEndereco');
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		$this->GrupoEconomicoCliente =& ClassRegistry::init('GrupoEconomicoCliente');
		$this->ClienteImplantacao =& ClassRegistry::init('ClienteImplantacao');
		$this->TipoContato =& ClassRegistry::init('TipoContato');
		$this->Endereco =& ClassRegistry::init('Endereco');
		$this->ClienteEndereco =& ClassRegistry::init('ClienteEndereco');

		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_usuario_inclusao'] = 1;
	}

	public function testInclusaoCliente() {
		$qtd_cliente = $this->Cliente->find('count');
		$qtd_cliente_implantacao_antes = $this->ClienteImplantacao->find('count');
		$qtd_grupo_economico_antes = $this->GrupoEconomico->find('count');

		$dados = array(
			    'Cliente' => array(
			            'razao_social' => 'TME Ltda',
			            'nome_fantasia' => 'TME',
			            'codigo_documento' => '62658512000105',
			            'inscricao_estadual' => 'ISENTO',
			            'ccm' => 'ISENTO',
			            'codigo_regime_tributario' => 1,
			            'tipo_unidade' => 'F',
			            'regiao_tipo_faturamento' => 1
			        ),
			    'ClienteEndereco' => array(
			            'codigo_endereco' => 5,
			            'numero' => 11,
			            'raio' => 150,
			        ),
			    'VEndereco' => array(
            		'endereco_cep' => 60731000
        		)
			);
		       

		$this->Cliente->incluir($dados);
		$qtd_cliente_depois = $this->Cliente->find('count');
		$qtd_cliente_implantacao_depois = $this->ClienteImplantacao->find('count');
		$qtd_grupo_economico_depois = $this->GrupoEconomico->find('count');
		$this->assertEqual($qtd_cliente +1, $qtd_cliente_depois);
		$this->assertEqual($qtd_cliente_implantacao_antes +1, $qtd_cliente_implantacao_depois);
		$this->assertEqual($qtd_grupo_economico_antes +1, $qtd_grupo_economico_depois);
	}	

	public function testInclusaoClienteUnidade() {
		$qtd_cliente = $this->Cliente->find('count');
		$qtd_cliente_implantacao_antes = $this->ClienteImplantacao->find('count');
		$qtd_grupo_economico_antes = $this->GrupoEconomico->find('count');
		$qtd_grupo_economico_cliente_antes = $this->GrupoEconomicoCliente->find('count');

		$dados = array(
			    'Cliente' => array(
			    		'codigo_grupo' => 346911,
			            'razao_social' => 'TME Ltda',
			            'nome_fantasia' => 'TME',
			            'codigo_documento' => '62658512000105',
			            'inscricao_estadual' => 'ISENTO',
			            'ccm' => 'ISENTO',
			            'codigo_regime_tributario' => 1,
			            'tipo_unidade' => 'F',
			            'regiao_tipo_faturamento' => 1
			        ),
			    'GrupoEconomicoCliente' => array(
			    		'codigo_grupo_economico' => 1064 
			    	),
			    'ClienteEndereco' => array(
			            'codigo_endereco' => 5,
			            'numero' => 11,
			            'raio' => 150,
			        ),
			    'VEndereco' => array(
            		'endereco_cep' => 60731000
        		)
			);
		       

		$this->Cliente->incluir($dados);
		$qtd_cliente_depois = $this->Cliente->find('count');
		$qtd_cliente_implantacao_depois = $this->ClienteImplantacao->find('count');
		$qtd_grupo_economico_depois = $this->GrupoEconomico->find('count');
		$qtd_grupo_economico_cliente_depois = $this->GrupoEconomicoCliente->find('count');
		$this->assertEqual($qtd_cliente +1, $qtd_cliente_depois);
		$this->assertEqual($qtd_cliente_implantacao_antes, $qtd_cliente_implantacao_depois);
		$this->assertEqual($qtd_grupo_economico_antes, $qtd_grupo_economico_depois);
		$this->assertEqual($qtd_grupo_economico_cliente_antes + 1, $qtd_grupo_economico_cliente_depois);
	}

	public function endTest() {
		unset($this->Cliente);
		unset($this->GrupoEconomico);
		unset($this->GrupoEconomicoCliente);
		unset($this->ClienteImplantacao);
		unset($this->TipoContato);
		unset($this->Endereco);
		unset($this->ClienteEndereco);
		ClassRegistry::flush();
	}
}