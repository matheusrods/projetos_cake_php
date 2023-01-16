<?php
class UltimosRecebimentosPerifericosController extends appController {
	var $name = 'UltimosRecebimentosPerifericos';
	var $uses = array('TUrpeUltimoRecPeriferico');

	function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow('*');
	}

	function por_placa($placa) {
		$dados = $this->TUrpeUltimoRecPeriferico->porPlaca($placa);
		$this->set(compact('dados'));
	}
}