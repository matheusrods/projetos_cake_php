<?php
App::import('Model', 'Exame');
class PosicaoExameTestCase extends CakeTestCase {
	public $fixtures = array(
		'app.exame',
		'app.aplicacao_exame',
		'app.grupo_economico',
		'app.grupo_economico_cliente',
		'app.cliente',
		'app.setor',
		'app.cargo',
		'app.funcionario_setor_cargo',
		'app.cliente_funcionario',
		'app.funcionario',
		'app.cliente_setor',
		'app.item_pedido_exame',
		'app.item_pedido_exame_baixa',
		'app.grupo_exposicao',
		'app.grupo_exposicao_risco',
		'app.risco',
		'app.pedido_exame'
		);

	public function startTest() {
		$this->Exame = & ClassRegistry::init('Exame');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}

	/**
	 * Roda todos os tests para ficar mais rápido 
	 **/
	public function testAll()
	{
		//metodo para testar os funcionarios que não tem alteração de riscos e exames na nova função
		$this->semAlteracaoRiscoFuncao();

		//Mudança de risco para ausência de risco / Mudança de exames para exame clínico
		$this->mudancaAusenciaRiscoNovaFuncao();

		//Mudança de risco e/ou mudança de exames
		$this->mudancaRsicoExame();

	}

	
	// Sem alterações de riscos e exames na nova função
	public function semAlteracaoRiscoFuncao() {
		
		$dados['conditions'][] = " analitico.cpf = '19651584041' ";
		$retorno = $this->Exame->posicao_exames_analitico('', $dados);


		pr($retorno);exit;

		if( count( $retorno ) ){

			$validacao[] =	array 	( 	"codigo_fsc" 	=> 	65511,
											"fscx"			=>	65508,
											"codigo_exame"	=>  41 
										) ;

			$validacao[] = 	 array 	( 	"codigo_fsc" 	=> 	65511,
											"fscx"			=>	65508,
											"codigo_exame"	=>  52 
										) ;

			$validacao[] =	array 	( 	"codigo_fsc" 	=> 	65511,
											"fscx"			=>	65508,
											"codigo_exame"	=>  66 
										) ;

			$validacao[] =	array 	( 	"codigo_fsc" 	=> 	65511,
											"fscx"			=>	65508,
											"codigo_exame"	=>  84 
										) ;

			$validacao[] =	array 	( 	"codigo_fsc" 	=> 	65511,
											"fscx"			=>	65508,
											"codigo_exame"	=>  87 
										) ;

			foreach( $retorno as $i => $r  ){
				$ret[]	= 	array 	( 		"codigo_fsc" 	=> 	$retorno[$i][0]['codigo_fsc'],
											"fscx"			=>	$retorno[$i][0]['fscx'],
											"codigo_exame"	=>  $retorno[$i][0]['codigo_exame']
										);
			}

			$this->assertEqual( $ret , $validacao);
		}		
	}
		
	// Mudança de risco para ausência de risco / Mudança de exames para exame clínico
	public function mudancaAusenciaRiscoNovaFuncao() 
	{
		
		$dados['conditions'][] = " analitico.cpf = '93528459000' ";
		$retorno = $this->Exame->posicao_exames_analitico('', $dados);

		if( count( $retorno ) ){

			$validacao[] = 	 array 	( 		"codigo_fsc" 	=> 	65512,
											"fscx"			=>	65509,
											"codigo_exame"	=>  52 ,
											"ultimo_pedido" => '2018-06-05',
											"codigo_pedido" => 29009
										) ;

			foreach( $retorno as $i => $r  ){
				$ret[]	= 	array 	( 		"codigo_fsc" 	=> 	$retorno[$i][0]['codigo_fsc'],
											"fscx"			=>	$retorno[$i][0]['fscx'],
											"codigo_exame"	=>  $retorno[$i][0]['codigo_exame'],
											"ultimo_pedido" =>  $retorno[$i][0]['ultimo_pedido'],
											"codigo_pedido" =>  $retorno[$i][0]['codigo_pedido']
										);
			}

			$this->assertEqual( $ret , $validacao);
		}		
	}

	// Mudança de risco e/ou mudança de exames
	public function mudancaRsicoExame() 
	{
		
		$dados['conditions'][] = " analitico.cpf = '19485014066' ";
		$retorno = $this->Exame->posicao_exames_analitico('', $dados);


		if( count( $retorno ) ){

			$validacao[] =	array 	( 	"codigo_fsc" 	=> 	65513,
											"fscx"			=>	65513,
											"codigo_exame"	=>  41 
										) ;

			$validacao[] = 	 array 	( 	"codigo_fsc" 	=> 	65513,
											"fscx"			=>	65513,
											"codigo_exame"	=>  52 
										) ;

			$validacao[] =	array 	( 	"codigo_fsc" 	=> 	65513,
											"fscx"			=>	65513,
											"codigo_exame"	=>  66 
										) ;

			$validacao[] =	array 	( 	"codigo_fsc" 	=> 	65513,
											"fscx"			=>	65513,
											"codigo_exame"	=>  84 
										) ;


			foreach( $retorno as $i => $r  ){
				$ret[]	= 	array 	( 		"codigo_fsc" 	=> 	$retorno[$i][0]['codigo_fsc'],
											"fscx"			=>	$retorno[$i][0]['fscx'],
											"codigo_exame"	=>  $retorno[$i][0]['codigo_exame']
										);
			}

			$this->assertEqual( $ret , $validacao);
		}		
	}


	public function endTest() {
		unset($this->Exame);
		ClassRegistry::flush();
	}
}