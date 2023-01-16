<?php
class ImportarSinistrosShell extends Shell {

	var $tasks = array('ImportarSinistro');	
	

	public function importar(){
		
		echo $this->ImportarSinistro->importar();
	}

}
?>
