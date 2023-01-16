<?php
class RenovacaoPesquisaShell extends Shell {

	public function main() {
		App::import('Component', array('StringView', 'Mailer.Mailer'));

		$this->PesquisaConfiguracao = & ClassRegistry::init('PesquisaConfiguracao');
		$this->FichaRetorno = & ClassRegistry::init('FichaRetorno');
		$inicio = isset($this->args[0]) ? $this->args[0] : null;
		$fim = isset($this->args[1]) ? $this->args[1] : null;
		$this->inicio($inicio, $fim);
	}
    
    public function insereCt($ficha){
        $fichaCt =& ClassRegistry::init('FichaCt');
        $fichaCt->insere($ficha);
    }

	public function inicio() {
		//Inicia Renovação automatica 
		
		$this->LogAplicacao =& ClassRegistry::init('LogAplicacao');
		$this->LogAplicacao->sistema = 'Renovacao Automatica';
		$this->LogAplicacao->incluirLog('Inicia processo Renovacao Automatica .PID(Linux) -'.getmypid());
		
 		// Setando data Atual
 		$dataAtual = date('Y-m-d');
 		$this->LogAplicacao->incluirLog('Setando data Atual:'.$dataAtual);
        
        $FichaPesquisa =& ClassRegistry::init('FichaPesquisa');
        //Obtendo Fichas para Renovar 
        $this->LogAplicacao->incluirLog('Obtendo Fichas para Renovar');
        try {
          $fichas = $FichaPesquisa->obterFichasParaRenovacao($dataAtual, true);
        }catch(Exception $e){
            $this->LogAplicacao->incluirLog('Serviço instavel.Verificar Excecao'.$e->getMessage());
        }
        //$this->LogAplicacao->incluirLog('Fichas para Renovação:'.print_r($fichas, true));
        $Ficha =& ClassRegistry::init('Ficha');
        
        $gravar = true;
        $qtdeOk = 0;
        $qtdeFichas = count($fichas);
        // Qtidade Fichas a Renovar :
        $this->LogAplicacao->incluirLog('Quantidade de Fichas a Renovar:'.$qtdeFichas); 
        $qtdeProcessados = 0;

        echo count($fichas)."\n";
        for ($k = 0; $k < count($fichas); $k++) {
        	$ficha = $fichas[$k];
        	
        	$qtdeProcessados++;
            if ($FichaPesquisa->disponivelParaPesquisaAutomatica($ficha, true)) {
                // Validando Ficha :
                $this->LogAplicacao->incluirLog('validando Ficha:'.print_r($ficha, true));
                try {
                   $arrStatusFicha = $this->PesquisaConfiguracao->validar($ficha, $gravar);
                }catch(Exception $e){
                    $this->LogAplicacao->incluirLog('Serviço instavel.Verificar Excecao'.$e->getMessage());
                }
                
                if ($arrStatusFicha === false) {
                    continue;
                }
                $validacoesNok = array_filter($arrStatusFicha, create_function('$validacao', 'return !is_null($validacao) && $validacao === false;'));
                if (count($validacoesNok) == 0) {
                    //AtualizandoStatus Ficha Adequado ao Risco 
                    try{
                    $fichaValidada = $this->PesquisaConfiguracao->atualizaStatusFichaAdequadoAoRisco($ficha);
                    }catch(Exception $e){
                      $this->LogAplicacao->incluirLog('Serviço instavel.Verificar Excecao'.$e->getMessage());
                    }
                    $this->LogAplicacao->incluirLog('Ficha Validada:'.print_r($ficha, true));
                    if ($fichaValidada) {
                        // Liberando Fichas
                        $Ficha->liberaFicha($fichaValidada, null);
                        $this->LogAplicacao->incluirLog('Liberando Fichas:'.print_r($ficha, true));
                        $this->insereCt($fichaValidada);
                        $this->LogAplicacao->incluirLog('CT gerada:'.print_r($ficha, true));
                        $qtdeOk++;
                    }
                }
            }
            // Resumo final 
            $res_final = str_pad($qtdeProcessados,5,0,STR_PAD_LEFT).'/'.str_pad($qtdeFichas,5,0,STR_PAD_LEFT).' '.str_pad($qtdeFichas,5,0,STR_PAD_LEFT)." ".str_pad($qtdeOk,5,0,STR_PAD_LEFT)."\r";
            echo str_pad($qtdeProcessados,5,0,STR_PAD_LEFT).'/'.str_pad($qtdeFichas,5,0,STR_PAD_LEFT).' '.str_pad($qtdeFichas,5,0,STR_PAD_LEFT)." ".str_pad($qtdeOk,5,0,STR_PAD_LEFT)."\r";
            $this->LogAplicacao->incluirLog('Resumo Final:'.print_r($res_final, true));
        }
       
        // Retorno 
        $retorno = array(
        	'total' => $qtdeFichas,
        	'validadas' => $qtdeOk
        );
         $this->LogAplicacao->incluirLog('Retorno Final:'.print_r($retorno, true));
         
        //Disparando Email de Alerta 
        try{
          $this->dispararEmailDeAlerta($retorno);
        }catch(Exception $e){
             $this->LogAplicacao->incluirLog('Serviço instavel.Verificar Excecao'.$e->getMessage());
        }
        $this->LogAplicacao->incluirLog('Disparando Email de Alerta:'.print_r($retorno, true));

	    $this->LogAplicacao->incluirLog('Finalizando Processo Renovação Automatica.PID(Linux) '.getmypid());
	}

    protected function dispararEmailDeAlerta($message) {
        App::import('Component', array('StringView', 'Mailer.Scheduler'));
        $StringView = new StringViewComponent();
        $Scheduler = new SchedulerComponent();        
        
        $StringView->reset();
        $StringView->set('total', $message['total']);
        $StringView->set('validadas', $message['validadas']);
        $content = $StringView->renderMail('email_retorno_pesquisa', 'default');
        $options = array(
            'from' => 'portal@rhhealth.com.br',
            'sent' => null,
            'to' => 'agregado.suporte@buonny.com.br;analise@buonny.com.br;janaina.silva@buonny.com.br',
            'cc' => null,
            'subject' => 'Retorno pesquisador automático',
        );
        $this->LogAplicacao =& ClassRegistry::init('LogAplicacao');
		$this->LogAplicacao->sistema = 'Renovação Automatica Teleconsult';
		$this->LogAplicacao->incluirLog($Scheduler->schedule($content, $options) ? 'Retorno Schedule :true': 'Retorno Schedule :false');
        return $Scheduler->schedule($content, $options) ? true: false;
    }
}
?>
