<?php
class ModuloSgi extends AppModel {
	var $name = 'ModuloSgi';
	var $useTable = false;
	
	const ADMIN       = 1;
	const LOGISTICO   = 2;
	const TEMPERATURA = 3;
	const JORNADA     = 4;
	const GERENCIAL   = 5;
	const SEM_MODULO  = 9;
	const TRANSYSEG   = 10;
	
	function modulos(){
		return array(
			array(
				'nome' => 'Sistema',
				'url' => array('controller' => 'PainelSgi', 'action' => 'modulo_admin')
			), 
			array(
				'nome' => 'Logistico',
				'url' => array('controller' => 'PainelSgi', 'action' => 'modulo_logistico')
			), 
			array(
				'nome' => 'Temperatura',
				'url' => array('controller' => 'PainelSgi', 'action' => 'modulo_temperatura')
			), 
			array(
				'nome' => 'Jornada',
				'url' => array('controller' => 'PainelSgi', 'action' => 'modulo_jornada')
			), 
			array(
				'nome' => 'Gerecial',
				'url' => array('controller' => 'PainelSgi', 'action' => 'modulo_gerencial')
			),
			array(
				'nome' => '',
				'url' => array('controller' => 'PainelSgi', 'action' => 'sem_modulo')
			),
			array(
				'nome' => 'Transyseg',
				'url' => array('controller' => 'PainelSgi', 'action' => 'modulo_transyseg')
			), 
		);
	}
}
?>