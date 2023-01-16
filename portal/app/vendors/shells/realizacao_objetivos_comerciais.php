<?php
class RealizacaoObjetivosComerciaisShell extends Shell {
	var $uses = array(
		'ObjetivoComercial',
		'ObjetivoComercialCliente',
		'Cliente',
		'ClienteProduto',
		'VtigerCrmentity',
		'VtigerAccount',
		'VtigerAccountscf',
		'VtigerSeactivityrel',
		'VtigerActivity',
		'VtigerUser',
		'ObjetivoComercialExcecao',
		'ObjetivoExcecaoFaturamento',
	);
    //Ex: 02-2015

    var $mes_ano_trava = false;

	function main() {
		echo "===== inserir_objetivos_comerciais_realizado =======\n\n";		
		echo "====================================================\n\n";		
		echo "===========inserir_faturamento_excecao==============\n\n";		

	}

	function run() {
        $this->mes_ano_trava = isset($this->args[0]) ? $this->args[0] : false;
		if (!$this->im_running('realizacao_objetivos_comerciais')){
        	$this->inserir_objetivos_comerciais_realizado();
        	$this->inserir_faturamento_excecao();
        }    
    }

	private function im_running($tipo) {
		if (PHP_OS!='WINNT') {
			$cmd = shell_exec("ps aux | grep '{$tipo}'");
			// 1 execução é a execução atual
			return substr_count($cmd, 'cake.php -working') > 1;
		} else {
			$cmd = `tasklist /v | findstr /R /C:"{$tipo}"`;
			$ret = substr_count($cmd, 'cake\console\cake') > 1;			
		}
	}

    function inserir_objetivos_comerciais_realizado(){
        $retorno = $this->ObjetivoComercialCliente->inserir_objetivos_clientes(false,false,$this->mes_ano_trava);
        $cont_analitico_inc = $retorno['cont_analitico_inc'];
        $cont_analitico_alt = $retorno['cont_analitico_alt'];
        echo "\n\n";
        echo "---------------------------------\n\n";
        echo "Inserido $cont_analitico_inc registros\n\n";
        echo "Alterado $cont_analitico_alt registros\n\n";
        echo "---------------------------------\n\n";

    }

    function inserir_faturamento_excecao(){
    	echo "Inicio excecao de faturamento\n\n";
        $mes = NULL;
        $ano = NULL;

        if($this->mes_ano_trava){
            $dados = explode('-', $this->mes_ano_trava);
            $mes = $dados[0];
            $ano = $dados[1];
        }
  
    	$dados = $this->ObjetivoExcecaoFaturamento->verificaFaturamentoExcecaoRealizado($mes,$ano);
    	$this->ObjetivoExcecaoFaturamento->inserirFaturamentoExcecao($dados);	
    	$exc_count = count($dados);
    	echo "----------------------------------------------\n\n";
    	echo "Localizado $exc_count excecoes de faturamento \n\n";
    	echo "----------------------------------------------\n\n";
    }
}