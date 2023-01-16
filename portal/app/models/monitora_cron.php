<?php
class MonitoraCron extends AppModel {
	var $name          = 'MonitoraCron';
	var $tableSchema   = 'dbo';
	var $databaseTable = 'dbMonitora';
	var $useTable      = 'monitora_cron';
	var $primaryKey    = 'codigo';

	/*
		dia_processamento => tabela
		Caso o mesmo seja null,significa que será verificado todos os dias
	*/

	const CRON_RATEIO_TRANPAG = 1;
	const CRON_NOTAFIS_INCLUIR_COMPLEMENTO = 2;
	const TRANREC = 3;
	const PESQUISA_SATISFACAO = 4;
	const ATUALIZA_PARADAS_VEICULOS = 5;
	const CRON_CALCULA_DIAS_SEM_VIAGEM_VEICULO = 6;
	

 	private	function retorna_constante_shell($shell){
 		switch ($shell) {
 			case 'cron_rateio_tranpag':
 				$codigo = self::CRON_RATEIO_TRANPAG;
 				break;
 			case 'cron_notafis_incluir_complemento':
 				$codigo = self::CRON_NOTAFIS_INCLUIR_COMPLEMENTO;
 				break;
 			case 'tranrec':
 				$codigo = self::TRANREC;
 				break;
 			case 'pesquisa_satisfacao':
 				$codigo = self::PESQUISA_SATISFACAO;
 				break;
 			case 'atualiza_paradas_veiculos':
 				$codigo = self::ATUALIZA_PARADAS_VEICULOS;
 				break;
 			case 'cron_calcula_dias_sem_viagem_veiculo':
 				$codigo = self::CRON_CALCULA_DIAS_SEM_VIAGEM_VEICULO;
 				break;				

 			default:
 				$codigo = false;
 				break;
 			return $codigo;	
 		}

 		return $codigo;
 	}


	function execucao($shell){
		$codigo = $this->retorna_constante_shell($shell);
		if(!$codigo){
			return false;
		}
		$dado_cron['MonitoraCron'] = array(
    		'codigo' => $codigo,
    		'data_ultima_execucao' => date('Ymd H:i:s')
		);

		return $this->atualizar($dado_cron);
	}

}
?>