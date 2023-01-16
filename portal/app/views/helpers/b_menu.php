<?php
class BMenuHelper extends AppHelper {

	var $helpers = array('Html');
	var $sistema = 'xmig';
	
	function __construct(){
		parent::__construct();
		
		App::import('Component', 'BAuth');
		$this->BAuth = new BAuthComponent();
		
	}

	function link($title, $url = null, $options = array(), $confirmMessage = false, $escapeTitle = true) {
		$temPermissao = $this->BAuth->temPermissao($_SESSION['Auth']['Usuario']['codigo_uperfil'], $url);
		if(!$temPermissao)
			return;

		$htmlAttributes = $options;
		unset($htmlAttributes['wrapper']);
		unset($htmlAttributes['aco']);
		
		$link = $this->Html->link($title, $url, $htmlAttributes, $confirmMessage, $escapeTitle);
		
		if(isset($options['wrapper'])) {
			$wrapper_options = null;
			if (isset($options['wrapper_class'])) {
				$wrapper_options = array('class' => $options['wrapper_class']);
			}
			$link = $this->Html->tag($options['wrapper'], $link, $wrapper_options);
		}
			
		return $link;
	}

	//Helper para verificar se link do menu lateral tem permissão para ser exibido
	//Utilizado em /views/elements/menu_lateral/*
	function permiteMenu($url = null)
    {
        $temPermissao = $this->BAuth->temPermissao($_SESSION['Auth']['Usuario']['codigo_uperfil'], $url);

        if(!$temPermissao)
            return false;

        return true;
    }
	
	function linkOnClick($title, $url = null, $options = array(), $confirmMessage = false, $escapeTitle = true) {
		$temPermissao = $this->BAuth->temPermissao($_SESSION['Auth']['Usuario']['codigo_uperfil'], $url);
		// if(!$temPermissao)
		// 	return;
		
		$htmlAttributes = $options;
		unset($htmlAttributes['wrapper']);
		unset($htmlAttributes['aco']);
		
		$link = $this->Html->link($title, $url, $htmlAttributes, $confirmMessage, $escapeTitle);
		
		if(isset($options['wrapper']))
			$link = $this->Html->tag($options['wrapper'], $link);
			
		return $link;
	}
}
?>
