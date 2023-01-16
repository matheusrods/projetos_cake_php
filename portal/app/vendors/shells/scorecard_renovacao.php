<?php
class ScorecardRenovacaoShell extends Shell {
	var $uses = array('FichaScorecard','LogAtendimento','LogFaturamentoTeleconsult','Status','Produto','Veiculo','ProfissionalLog','ClienteProduto');
	
   public function incluir_log_faturamento_renovacao($codigo_ficha)
     {
        $ClienteProdutoServico2     	=& ClassRegistry::init('ClienteProdutoServico2');
        $data = $this->FichaScorecard->carregar($codigo_ficha);
        $data['Ficha'] = $data['FichaScorecard']; 
	    unset($data['FichaScorecard']);

	    $data['codigo_usuario_inclusao']= 159;
	    $data['Ficha']['codigo_status'] =   1; 
	    $data['Ficha']['ativo']= 1; 
	    $data['Ficha']['codigo_cliente_pagador'] =$data['Ficha']['codigo_cliente'];
	    $data['Ficha']['codigo_produto'] = Produto::SCORECARD; 
	    $data['codigo_tipo_operacao'] = 75;

	    $clienteProdutoServico = ClassRegistry::init('Ficha')->findClienteProdutoServico($data['Ficha']);
	    
	    $codigo_ficha_scorecard2 = $this->FichaScorecard->busca_ultima_ficha_cliente($data['Ficha']['codigo_cliente']);

	    $codigo_ficha_scorecard= $codigo_ficha;
	    $profissional = $this->ProfissionalLog->obterProfissionalPeloCodigoProfissionalLog($data['Ficha']['codigo_profissional_log']);
	    
	    $faturamento['codigo_profissional']  = $profissional['Profissional']['codigo'];
	    $profissional_veiculo = $this->FichaScorecard->buscarDadosEmailResultado($codigo_ficha);
	    
	    $codigos_veiculo = array();
	    if(isset($profissional_veiculo['Veiculo']['placa'])) {
		    $data['FichaScorecardVeiculo']['0']['Veiculo']['placa'] = $profissional_veiculo['Veiculo']['placa']; // - veiculo
		    $veiculo_codigo = $this->Veiculo->bucaVeiculoPorPlaca($data['FichaScorecardVeiculo']['0']['Veiculo']['placa']);
		    $codigos_veiculo[0] = $veiculo_codigo['Veiculo']['codigo'];
	    }

	    if(isset($profissional_veiculo['Carreta']['placa'])) {
	    	$data['FichaScorecardVeiculo']['1']['Veiculo']['placa'] = $profissional_veiculo['Carreta']['placa']; // - carreta
	    	$carreta_codigo = $this->Veiculo->bucaVeiculoPorPlaca($data['FichaScorecardVeiculo']['1']['Veiculo']['placa']);
	    	$codigos_veiculo[1] = $carreta_codigo['Veiculo']['codigo'];   
	    }


        $codigo_profissional = $profissional['Profissional']['codigo'];
        $profissionalExisteNoBanco = !empty($codigo_profissional);

        $this->LogFaturamentoTeleconsult->gerarFaturamentoFichaScorecard(
	            			$data, 
	            			$clienteProdutoServico, 
	            			$codigo_ficha_scorecard, 
	            			$codigo_profissional, 
	            			$codigos_veiculo, 
	            			true
	            		);


    }
    private function im_running($tipo) {
		$cmd = "ps aux | grep '{$tipo}'";
		return substr_count(shell_exec($cmd), 'cake.php -working') > 1;
	}


	public function main() { 
		echo "\nuse scorecard_renovacao processar [dt_ini] [dt_fim]\n\n\n";
	}

	public function processar() {
		// if(!$this->im_running('scorecard_renovacao')) {
			$codigo_cliente = null;
	    	//$dt_ini = (isset($this->args[0]) ? $this->args[0] :  date('Y-m-01')) . ' 00:00:00';
	    	//$dt_fim = (isset($this->args[1]) ? $this->args[1] :  date('Y-m-d')) . ' 23:59:59';

	    	$d1 = explode(' ',$dt_ini);
	    	$d2 = explode(' ',$dt_fim);

	    	list($ano_atual,$mes_atual,$dia_atual) = explode('-',$d1[0]);

			//$mes_atual = date('m');
			$data_pesquisa = date('Y-m',mktime(0, 0, 0, $mes_atual-1, 1, $ano_atual));
	    	list($ano,$mes) = explode('-',$data_pesquisa);
			$total_registros = $this->FichaScorecard->verificarRenovacaoMes($codigo_cliente,$mes,$ano);
			if($total_registros == 0)
				$this->FichaScorecard->gravar_renovacao(NULL,NULL,NULL,NULL,$codigo_cliente);//Grava na renovação automática as fichas que forem vencer no mes atual
	        $profissionais_a_renovar = $this->FichaScorecard->profissionaisARenovar($codigo_cliente, $dt_ini,$dt_fim);
	        if($profissionais_a_renovar){
		        foreach($profissionais_a_renovar as $ficha) {
		            if($codigo_ficha_renovada = $this->FichaScorecard->renovarFicha($codigo_cliente,$ficha[0]['codigo_profissional'],$ficha[0]['codigo_profissional_tipo'],$ficha[0]['ficha'])) {
		            	  $this->incluir_log_faturamento_renovacao($codigo_ficha_renovada);
		            	  $this->out("Ficha renovada: " . $codigo_ficha_renovada);
		             }
		        }
		        $this->FichaScorecard->finalizaRenovacao($codigo_cliente,$dt_ini,$dt_fim);
		    }else{
		    	echo 'Nao tem fichas para renovar';
		    	exit;
		    }

		// } else {
		// 	echo "Já tem uma instância em andamento, tente novamente mais tarde.";
		// }
	}

}
?>
