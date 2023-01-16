<?php
class ControleAlerta extends AppModel {
	var $name = 'ControleAlerta';
    var $tableSchema = 'monitora';
    var $databaseTable = 'dbMonitora';
    var $useTable = 'controles_alertas';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_controle_alerta'));
    CONST CONSULTA_EM_EXECUCAO = 1;

    CONST INICIO_ALERTA = 1;
    CONST FIM_ALERTA = 2;

    function lista_alerta_por_codigo($codigo){
    	switch ($codigo) {
    		case self::CONSULTA_EM_EXECUCAO:
    			$descricao = 'Consulta em execução'; 
    			break;    		
    		default:
    			$descricao = 'Alerta não encontrado';
    			break;
    	}

        return $descricao;
    }

    function incluir_consulta_execucao(){   	
		$dados['ControleAlerta']['codigo'] = self::CONSULTA_EM_EXECUCAO;		
		$dados['ControleAlerta']['em_alerta'] =  1;
		$dados['ControleAlerta']['data_inicio_ultimo_alerta'] = date('Y-m-d H:i:s');
		$dados['ControleAlerta']['data_fim_ultimo_alerta'] =  NULL;    	
		parent::atualizar($dados);   		   		
    }

    function consulta_execucao($consulta){
    	$conditions = array(
    		'codigo' => $consulta,
    		'em_alerta' => 1
    	);
    	if($this->find('count',array('conditions' => $conditions)) > 0){
    		return TRUE;
    	}
    	return FALSE;
    }


    function alertaFinalizado($codigo_notificacao){
    	if($this->consulta_execucao($codigo_notificacao)){
	    	$dados['ControleAlerta']['codigo'] = self::CONSULTA_EM_EXECUCAO;			
			$dados['ControleAlerta']['em_alerta'] = 0;
			$dados['ControleAlerta']['data_fim_ultimo_alerta'] =  date('Y-m-d H:i:s'); 	
    		parent::atualizar($dados);
    		return TRUE;
    	}
    	return FALSE;
    }
}
?>

