<?php
class BDebugComponent extends Object{
	var $name = 'BDebug';
	var $components = array('Session');
	var $controller;
	var $action;

	function initialize($controller, $settings = array()) {
		// salvando a referência do controller para uso posterior
		$this->controller = $controller->name;
		$this->action = $controller->action;
	}
	
	public function dump( $dados = null, $extrainfo = '' ) {
		if(Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO){
			$extra = !(empty($extrainfo)) ? ' >> ' .$extrainfo .' :: ' : ' :: ';
			$this->log($this->controller.' >> '.$this->action.$extra.print_r($dados, true), 'debug');
		}
	}
}
?>