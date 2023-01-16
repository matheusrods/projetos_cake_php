<?php
class StatusCriterio extends AppModel {
    
    const DISTRIBUIDOR_FORENSE_CONSTA_NAO_ESCLARECIDO = 5;
    const SERASA_MOTORISTA_ATE_LIMITE                 = 12;
    const SERASA_PROPRIETARIO_ATE_LIMITE              = 97;
    const TELECHEQUE_MOTORISTA_ATE_LIMITE             = 7;
    const TELECHEQUE_PROPRIETARIO_ATE_LIMITE          = 92;
    const CNH_SUSPENSA                                = 18;
    const VEICULO_SEM_IMPEDIMENTOS                    = 23;
    const VEICULO_RESTRICAO_DETRANS_ESCLARECER        = 29;
    const VIAGENS_BSAT_24_MESES_ACIMA_DE_10_VIAGENS   = 53;
    const VIAGENS_BSAT_24_MESES_DE_6_A_10_VIAGENS     = 54;
	const VIAGENS_BSAT_24_MESES_ATE_5_VIAGENS         = 55;
	const VIAGENS_BSAT_24_MESES_SEM_VIAGENS           = 56;
	const EXP_BD_TLC_MAIS_DE_24_MESES                 = 40;
	const EXP_BD_TLC_DE_13_A_24_MESES                 = 41;
	const EXP_BD_TLC_DE_07_A_12_MESES                 = 42;
	const EXP_BD_TLC_ATE_06_MESES                     = 43; 
	const EXP_BD_TLC_INICIANTE                        = 44;


    
	var $name = 'StatusCriterio';
	var $primaryKey = 'codigo';
	var $displayField = 'descricao';
	var $databaseTable = 'dbTeleconsult';
	var $tableSchema = 'informacoes';
	var $useTable = 'status_criterios';
	var $actsAs = array('Secure');
	var $validate = array(
	    'descricao' => array(
	    	'notEmpty' =>array(
               'rule' => array('notEmpty'),
               'message' => 'Informe o Status ',
            ),
		    'menssage' => array( 
		    	'rule' => array('valida_status_criterio'),
		    	'message' => ' Status não Preenchido ou já existente ',
		    	'on' => 'create'
		    )
		    //'rule' => 'valida_status_criterio',
	        //'message' => ' Status não Preenchido ou já existente ',
	   		
	    )
	);


public function valida_status_criterio($check) {
		if (!isset($this->data[$this->name]['descricao']) || $this->data[$this->name]['descricao'] == NULL )
			return false;
		$codigo_criterio = $this->data[$this->name]['codigo_criterio'];
		$descricao = $check['descricao'];
		return !$this->find('all',array('conditions'=> array('codigo_criterio'=> $codigo_criterio,'descricao'=>$descricao)));
	}



function lista_status_criterio(){
	$order 	= array('Criterio.codigo');
	$return = $this->find('all',compact('order'));
	return $return;
}




	function bindCriterio() {
		$this->bindModel(array(
		   'belongsTo' => array(
			   'Criterio' => array(
				   'class' => 'Criterio',
				   'foreignKey' => 'codigo_criterio'
			   )
		   )
		));
	}
}
