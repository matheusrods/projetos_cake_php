<?php

class Documento extends AppModel {

	var $name = 'Documento';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'documento';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $validate = array(
		'codigo' => array(
			'rule' => array('limitaDuplicados', 1),
			'message' => 'Documento ja cadastrado.',
		)
	);
	

    const PESSOA_FISICA = 1;
    const PESSOA_JURIDICA = 0;

	function isCPF($cpf){
		//$c = str_split(preg_replace('/\D/', '', $cpf));
		$c = str_split(preg_replace('/[^A-Za-z0-9]/', '', $cpf));
		if (count($c) != 11) return false;
		if (preg_match('/^'.substr($cpf,1,1).'{11}$/',$cpf)) return false;
		for($s = 10, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--);
		if($c[9] != ((($n %= 11) < 2) ? 0 : 11 - $n)) return false;
		for($s = 11, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--);
		if($c[10] != ((($n %= 11) < 2) ? 0 : 11 - $n)) return false;
		return true;
	}

	function isCNPJ($cnpj){
		$b = array(6,5,4,3,2,9,8,7,6,5,4,3,2);
		$c = str_split(preg_replace('/\D/', '', $cnpj));
		if(count($c) != 14) return false;
		for($i = 0, $n = 0; $i < 12; $n += $c[$i] * $b[++$i]);
		if($c[12] != ((($n %= 11) < 2) ? 0 : 11 - $n)) return false;
		for($i = 0, $n = 0; $i <= 12; $n += $c[$i] * $b[$i++]);
		if($c[13] != ((($n %= 11) < 2) ? 0 : 11 - $n)) return false;
		return true;
	}

	function existeCadastro($documento){
		return ($this->find('count', array('conditions' => array('codigo' => $documento))) > 0);
	}

	function limitaDuplicados($check, $limit){
		$existing_promo_count = $this->find( 'count', array('conditions' => $check, 'recursive' => -1) );
		return $existing_promo_count < $limit;
	}

	function incluir($data) {
		$this->create();
		return $this->save($data);
	}
}

?>