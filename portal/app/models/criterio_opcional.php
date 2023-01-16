<?php
class CriterioOpcional extends AppModel {
	var $name = 'CriterioOpcional';
	var $primaryKey = 'codigo';
	var $databaseTable = 'dbTeleconsult';
	var $tableSchema = 'informacoes';
	var $useTable = 'criterios_opcionais';
	var $actsAs = array('Secure');
	
	public function atualizarCriterio($opcional, $codigo_criterio, $codigo_cliente, $codigo_seguradora) {
		$ja_eh_opcional = $this->ehOpcional($codigo_criterio, $codigo_cliente, $codigo_seguradora);
		if($ja_eh_opcional && !$opcional)
			$this->deleteAll(compact('codigo_criterio', 'codigo_cliente', 'codigo_seguradora'));
		else if(!$ja_eh_opcional && $opcional)
			$this->save(compact('codigo_criterio', 'codigo_cliente', 'codigo_seguradora', 'opcional'));
	}
	
	public function ehOpcional($codigo_criterio, $codigo_cliente, $codigo_seguradora) {
		$qtd = $this->find('count', array('conditions'=>compact('codigo_criterio', 'codigo_cliente', 'codigo_seguradora')));
		return $qtd > 0 ? true : false;
	}
	
}