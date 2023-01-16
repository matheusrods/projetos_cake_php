<?php
class ImportacaoTransysegTask extends Shell {
	var $uses =  array('Tpecas','Tveiculos');
	
   	public function importar(){   		
		$pasta = DS.'home'.DS.'transyseg'.DS.'transyseg'.DS.'enviado';
		$diretorio = dir($pasta);		
		while($arquivo = $diretorio->read()){
			if($arquivo != '.' && $arquivo != '..'){
				$model = null;
								
				if(stripos(comum::trata_nome($arquivo),'Pecas') !== FALSE)
					$model = 'Tpecas';						
				else if(stripos(comum::trata_nome($arquivo),'Veiculos') !== FALSE)
					$model = 'Tveiculos';
				if(!is_null($model)){
					if($this->$model->importar($pasta,$arquivo))				
						$this->$model->mover_arquivo($pasta, $arquivo);
				}
			}			
		}		
	}
}
?>