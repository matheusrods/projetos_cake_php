<?php
class Diretoria extends AppModel {
	var $name          = 'Diretoria';
	var $tableSchema   = 'dbo';
	var $databaseTable = 'RHhealth';
	var $useTable      = 'diretoria';
	var $primaryKey    = 'codigo';
	var $displayField = 'descricao';
  	var $actsAs        = array('Secure');
  	

  	function converteFiltrosEmConditions($data){
  		$conditons = array();

  		if(isset($data['descricao']) && !empty($data['descricao'])){
  			$conditons['descricao LIKE'] = '%'.$data['descricao'].'%';
  		}

  		if(isset($data['ativo']) && !empty($data['ativo'])){
  			if($data['ativo'] == 2){
  				$conditons['ativo'] = 0;
  			}else{
  				$conditons['ativo'] = 1;
  			}
  		}

  		return $conditons;
  	}

    function atualizar_diretoria_usuario($data){
      $this->Usuario = ClassRegistry::init('Usuario');
      if(!$this->Usuario->atualizar($data)){
        return false;
      }
      return true;

    }
}
?>