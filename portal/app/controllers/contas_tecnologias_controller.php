<?php
class ContasTecnologiasController extends appController {
	var $name = 'ContasTecnologias';
	var $uses = array('TCtecContaTecnologia');

	function consultar_placa() {
		$this->pageTitle = 'Consultar Conta de Tecnologia por Placa';
		if ($this->RequestHandler->isPost()) {
			$dados = $this->TCtecContaTecnologia->contaPorPlaca($this->data['TCtecContaTecnologia']['veic_placa']);
			$this->set(compact('dados'));
		}
	}
}
