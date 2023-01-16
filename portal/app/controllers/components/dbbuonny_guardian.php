<?php
class DbbuonnyGuardianComponent {
	var $name = 'DbbuonnyGuardian';
	
	function initialize(&$controller, $settings = array()) {        
		// saving the controller reference for later use        
		$this->controller =& $controller;    
	}
        
	function converteClienteGuardianEmBuonny($pess_oras_codigos) {
        $this->TPjurPessoaJuridica =& ClassRegistry::init('TPjurPessoaJuridica');
        $this->Cliente =& ClassRegistry::init('Cliente');
        $guardian = $this->TPjurPessoaJuridica->read(null, $pess_oras_codigos);
        $codigo_cliente = $this->Cliente->porCNPJ($guardian['TPjurPessoaJuridica']['pjur_cnpj']);
        if ($codigo_cliente)
        	return key($codigo_cliente);
        return false;
    }
        
	function converteClienteBuonnyEmGuardian($codigo_cliente, $base_cnpj = false) {
        $this->TPjurPessoaJuridica =& ClassRegistry::init('TPjurPessoaJuridica');
        $this->Cliente =& ClassRegistry::init('Cliente');
        $buonny = $this->Cliente->carregar($codigo_cliente);
        $pess_oras_codigos = $this->TPjurPessoaJuridica->codigosPorCnpj($buonny['Cliente']['codigo_documento'], $base_cnpj);
        if ($pess_oras_codigos)
        	return $pess_oras_codigos;
        return false;
    }
    
    function converteClienteBuonnyEmGuardianComCentralizador($codigo_cliente, $base_cnpj = false) {
    	$pess_oras_codigos = $this->converteClienteBuonnyEmGuardian($codigo_cliente, $base_cnpj);
    	$this->TPjurPessoaJuridica =& ClassRegistry::init('TPjurPessoaJuridica');
    	$cliente = $this->TPjurPessoaJuridica->buscaClienteCentralizador($codigo_cliente);
    	$pess_oras_codigo_centralizador = $cliente['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
    	return array($pess_oras_codigo_centralizador, $pess_oras_codigos);
    }
}