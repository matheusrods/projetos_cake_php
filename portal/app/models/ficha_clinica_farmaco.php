<?php
class FichaClinicaFarmaco extends AppModel {

	public $name            = 'FichaClinicaFarmaco';
	public $tableSchema     = 'dbo';
	public $databaseTable   = 'RHHealth';
	public $useTable        = 'fichas_clinicas_farmacos';
	public $primaryKey      = 'codigo';
	public $actsAs          = array('Secure', 'Containable');
	public $recursive 		= -1;

	public $belongsTo = array(
        'FichaClinica' => array(
            'className' => 'FichaClinica',
            'foreignKey' => 'codigo_ficha_clinica'
        )
    );

// 	function incluir_campo_chave($data){
		

// 		foreach ($data as $key => $dados) {
// 			foreach ($dados as $chave => $item) {
// 				foreach ($item as $campo => $valor) {

// if($item['codigo_ficha_clinica_resposta']==4178){
	
	
// }
// 					if(strstr($campo, 'doenca')){
// 						$doenca = $valor;
// 					}

// 					if(strstr($campo, 'farmaco')){
// 						$farmaco = $valor;
// 					}

// 					if(strstr($campo, 'posologia')){
// 						$posologia = $valor;
// 					}

// 					if(strstr($campo, 'dose_diaria')){
// 						$dose_diaria = $valor;
// 					}
// 				}
					
// 					$incluir_dados = array(
// 						'codigo_ficha_clinica' => $item['codigo_ficha_clinica'],
// 						'codigo_ficha_clinica_resposta' => $item['codigo_ficha_clinica_resposta'],
// 						'doenca' => (empty($doenca)? '': $doenca),
// 						'farmaco' => $farmaco,
// 						'posologia' => $posologia,
// 						'dose_diaria' => $dose_diaria
// 					);
					

// 					if(!$this->incluir($incluir_dados)){
// 						debug($this->validationErrors);
// 					}
// 			}
// 		}
// 		if(empty($this->validationErrors)){
// 			return true;
// 		}
// 		else{
// 			return false;
// 		}
// 	}

}
