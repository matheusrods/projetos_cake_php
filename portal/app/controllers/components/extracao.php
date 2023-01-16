<?php
class ExtracaoComponent { 

	var $name = 'Extracao'; 
	
	var $server_host = 'http://gol.local.buonny:9292';

	public function denatranCnh($cpf, $registro, $seguranca) {
	    if (empty($cpf) || empty($registro) || empty($seguranca))
	        return null;
		$url = "{$this->server_host}/denatran/cnh/{$cpf}/{$registro}/{$seguranca}";
		return json_decode(file_get_contents($url));
	}

	public function denatranVeiculo($cpf, $renavam) {
	    if (empty($cpf) || empty($renavam))
	        return null;
		$url = "{$this->server_host}/denatran/veiculo/{$cpf}/{$renavam}";
		return json_decode(file_get_contents($url));
	}
	
	public function stj($nome) {
	    if (empty($nome))
	        return null;
		$nome = rawurlencode($nome);
		$url = "{$this->server_host}/stj/{$nome}";
		return json_decode(file_get_contents($url));
	}
	
}
?>