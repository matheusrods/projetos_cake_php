<?php
App::import('Model', 'ImportacaoAtestados');
class ImportacaoAtestadosTestCase extends CakeTestCase {

	public $fixtures = array(
		'app.atestado',
		'app.atestado_cid',
		'app.cid',
		'app.conselho_profissional',
		'app.cliente',
		'app.funcionario',
		'app.funcionario_cliente',
		'app.funcionario_setor_cargo',
		'app.grupo_economico',
		'app.medico',
		'app.motivos_afastamento',
		'app.multi_empresa',
		'app.setor',
		'app.tipos_afastamentos',
		);

	public function startTest()	{
		$this->ImportacaoAtestados =& ClassRegistry::init('ImportacaoAtestados');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}


	public function endTest() {
		unset($this->ImportacaoAtestados);
		ClassRegistry::flush();
	}

}