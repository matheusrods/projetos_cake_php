<?php

class GeraPracPerifericosRacsShell extends Shell {
	var $uses = array('TRacsRegraAceiteSm', 'TPracPerifericoRacs', 'TPpadPerifericoPadrao');
	function main() {
		echo "**********************************************\n";
		echo "$ \n";
		echo "$ GERA PGR PERIFERICOS \n";
		echo "$ \n";
		echo "**********************************************\n\n";
		echo "\n\n";
	}

	function run(  ){
		$prac_ppad_codigos = $this->TPpadPerifericoPadrao->find('all', array(
			'conditions' => array( 'ppad_ativo' => 'S'),
			'fields'     => array( 'ppad_codigo'),
		));
		$this->prac_ppad_codigos = Set::extract('/TPpadPerifericoPadrao/ppad_codigo', $prac_ppad_codigos );
		$this->TRacsRegraAceiteSm->unbindModel(array('belongsTo' => array('TEstaEstado', 'TTtraTipoTransporte', 'TProdProduto')));
		$lista_racs = $this->TRacsRegraAceiteSm->find('all', array('conditions'=>array('racs_verificar_checklist'=>1), 'limit'=> 2 ));
		foreach ($lista_racs as $key => $rac ) {
			$this->verificarPGRPeriferico( $rac['TRacsRegraAceiteSm']['racs_codigo'] );
		}
	}

	function verificarPGRPeriferico( $racs_codigo ){
		$pgr_perifericos = $this->TPracPerifericoRacs->find('count', array('conditions'=>array('prac_racs_codigo'=>$racs_codigo)));
		if( $pgr_perifericos == 0 ) {
			$this->TPracPerifericoRacs->incluirMultiplo( $racs_codigo, $this->prac_ppad_codigos );
		}
	}
}
?>
