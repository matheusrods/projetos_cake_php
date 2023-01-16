<?php
class Criterio extends AppModel {
    
    const DISTRIBUIDOR_FORENSE = 1;
    const SERASA_MOTORISTA = 3;
    const SERASA_PROPRIETARIO = 23;
    const TELECHEQUE_MOTORISTA = 2;
    const TELECHEQUE_PROPRIETARIO = 22;
    const EXP_BANCO_DADOS_TLC = 9;
    const CONSULTAS_OK_TLC_24_MESES = 11;
    const ATUALIZACOES_RENOVACOES_AUTOMATICAS = 24;
    const VIAGENS_BSAT_24_MESES = 12;
    const RMA_ULTIMOS_12_MESES = 14;
    const CNH = 4;
    const VEICULO = 5;
    const IDADE_PROFISSIONAL = 13;
    
	var $name = 'Criterio';
	var $primaryKey = 'codigo';
	var $displayField = 'descricao';
	var $databaseTable = 'dbTeleconsult';
	var $tableSchema = 'informacoes';
	var $useTable = 'criterios';
	var $actsAs = array('Secure');
	var $validate = array(
	    'descricao' => array(
		    'rule' => 'valida_criterio',
	        'message' => 'Critério já existente ou não Preenchido',
	   		'on' => 'create' 
	   		/* validate fica valido apenas para  criar  se for  'on '=> 'upadte' será 
	   		validate para o  edit*/ 
	    ),
	);

	public function valida_criterio($check) {
			if (!isset($this->data[$this->name]['descricao']) || $this->data[$this->name]['descricao'] == NULL )
				return false;
			//$codigo = $this->data[$this->name]['codigo'];
			$descricao = $check['descricao'];
			return !$this->find('all',array('conditions'=> array('descricao'=>$descricao)));
		}
	function exibir_criterio(){
		$return	= $this->find('all',array('order'=>'descricao'));
	    return $return;
	}	
		
	function lista_criterio(){
		$return	= $this->find('list');
	    return $return;
	}

	function buscaPorNome($nome_criterio){
		$return	= $this->find('first',array('conditions' => array('descricao' => $nome_criterio)));
	    return $return['Criterio']['codigo'];
	}


}
