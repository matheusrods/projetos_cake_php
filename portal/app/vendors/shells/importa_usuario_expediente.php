<?php
class ImportaUsuarioExpedienteShell extends Shell {
	function main() {
		echo "==================================================\n";
		echo "* IMPORTACAO DE EXPEDIENTE: \n";
		echo "* \n";
		echo "==================================================\n\n";		
	}

	function run(){
		$this->Usuario = ClassRegistry::init('Usuario');		
		$this->UsuarioExpediente = ClassRegistry::init('UsuarioExpediente');
		echo date('Ymd H:i:s');
		
		$this->expedienteSegSex();

		echo "\n\nFIM:". date("Ymd H:i:s");
	}

	//Seg - Qui 08:00 - 18:00 | Sex 08:00 - 17:00
	function expedienteSegSex(){
		$crachas = array(
			12409, 10185, 14048, 12675, 12816, 12869, 15009, 10177, 12497, 12912, 13027, 
			10711, 12360, 12647, 12822, 12424, 12371, 12549, 11020, 12420, 12251, 11873, 
			13004, 12550, 11872, 12742, 15040, 14024, 10627, 11896, 15010
		);
		$usuarios = $this->Usuario->find('all', array('fields'=>array('codigo'), 'conditions'=> array('cracha'=>$crachas)));
		$usuarios = set::extract($usuarios, '/Usuario/codigo');
		foreach ($usuarios as $codigo_usuario ) {
			$entrada = "08:00:00";
			$saida   = "18:00:00";
			for ($dia_semana=1; $dia_semana <=7 ; $dia_semana++) { 
				if( $dia_semana > 5 ){
					$saida = NULL;
					$entrada = NULL;
				}
				if( $dia_semana == 5 )
					$saida = "17:00:00";
				$data = array(
					'codigo_usuario' 			=> $codigo_usuario,
					'dia_semana'     			=> $dia_semana,
					'entrada' 		 			=> $entrada,
					'saida'     	 			=> $saida,
					'codigo_usuario_inclusao' 	=> 2
				);
				$this->UsuarioExpediente->incluir( $data );
			}
		}


	}

}
?>