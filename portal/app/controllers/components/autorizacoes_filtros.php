<?php
class AutorizacoesFiltrosComponent {
	var $name = 'AutorizacoesFiltros';
	var $components = array('BAuth');

	public function initialize(&$controller, $settings = array()) {        
		// saving the controller reference for later use
		$this->controller =& $controller;    
	}

	public function defineVisualizacaoFiltroConfiguracao() {
        $authUser = $this->BAuth->user();
        $this->controller->set('visualiza_por_configuracao', $this->BAuth->temPermissao($authUser['Usuario']['codigo_uperfil'], 'obj_visualiza_filtro_configuracao'));
    }
}