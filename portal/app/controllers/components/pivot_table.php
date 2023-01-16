<?php
class PivotTableComponent {
	
	var $coluna;
	var $linhas;
	var $valor;
	
	function PivotTableComponent($coluna, $linhas, $valor) {
		$this->coluna = $coluna;
		$this->linhas = $linhas;
		$this->valor = $valor;
	}
	
	function transforma($data) {
		$sum = array();
		foreach ($data as $item)
			$sum = $this->array_merge_sum($sum, $this->transforma_linha($item));
		return $sum;
	}
	
	function array_merge_sum($a1, $a2) {
		foreach ($a2 as $k=>$v) {
			if(is_array($v))
				$a1[$k] = $this->array_merge_sum(isset($a1[$k]) ? $a1[$k] : array(), $v);
			else
				$a1[$k] = isset($a1[$k]) ? $a1[$k] + $v : $v;
		}
		return $a1;
	}
	
	function transforma_linha($data, $linhas = null) {
		if (!isset($linhas)) $linhas = $this->linhas;
		return array(
			'Total' => array($data[$this->coluna] => $data[$this->valor], 'Total' => $data[$this->valor]),
			$data[$linhas[0]] => (sizeof($linhas) > 1) ?  $this->transforma_linha($data, array_slice($linhas, 1)) : array($data[$this->coluna] => $data[$this->valor], 'Total' => $data[$this->valor])
		);
	}
		
}
?>