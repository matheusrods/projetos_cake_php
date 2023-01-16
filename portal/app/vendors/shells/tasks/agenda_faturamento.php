<?php
class AgendaFaturamentoTask extends Shell {
	
	var $uses =  array('RetornoNf', 'Notafis');
	
	public function __construct() {
		App::import('Component', array('StringView', 'Mailer.Scheduler'));
  		$this->stringView = new StringViewComponent();
		$this->scheduler = new SchedulerComponent();		
	}

    private function im_running() {
        $cmd = `ps aux | grep 'agenda_email'`;
        // 1 execução é a execução atual
        return substr_count($cmd, 'cake.php -working') > 1;
    }
	
	public function enviar_emails($data_faturamento){
	    $informe       = APP . 'tmp' . DS . 'ultimo_agenda_faturamento.txt';
	    $processados   = 0;
	    $enviados      = 0;

	    if (!$this->im_running()) {

	        $ano_mes = Date('Ym',strtotime(str_replace('/', '-', $data_faturamento)));
    	    $periodo = Comum::periodo($ano_mes);

    	    echo "Carregando Periodo \n";
    	    if (!$this->RetornoNf->carregarPeriodo($periodo)) {
                echo "Não tem nenhuma nota neste periodo! \n";
                exit;
            }
    	    echo "Obtendo Lista de NFs para enviar \n";
    		$retornos = $this->RetornoNf->listaParaEnviar();
    		if($retornos) {
    		    echo 'Notas Obtidas: '. count($retornos) .' agendados:'. $enviados . "\n";
                    foreach($retornos as $retorno) {
                        $links = $this->Notafis->linksFaturamento($retorno);
                        if (isset($links['links']['boleto']) && !empty($links['links']['boleto'])) {
                            $enviados++;
                            $this->scheduleMail($links);
                            $this->marcaEnvio($retorno, $links);
                            echo 'Notas Obtidas: '. count($retornos) .' / Agendamentos:'. $enviados . "\n";
                        }
                    }
    		}
    		echo 'Notas Obtidas: '. count($retornos) .' agendados:'. $enviados . (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? '' : ' ***** 123, testando! *****') . "\n";
    		
            if (file_exists($informe))
            	unlink($informe);

            $handle = fopen($informe, 'x+');
            fwrite($handle, 'Registros processados: '. count($retornos) .' agendados:'. $enviados . ' ' . Date('Ymd h:i:s'));
            fclose($handle);
    	}
        
	}

    public function enviar_emails_manual($data_faturamento){
        $processados = 0;
        $enviados = 0;

        $ano_mes = Date('Ym',strtotime(str_replace('/', '-', $data_faturamento)));
        $periodo = Comum::periodo($ano_mes);

        echo "Carregando Periodo \n";

        if (!$this->RetornoNf->carregarPeriodo($periodo)) {
            echo "Erro ao carregar periodo \n";
            exit;
        }

        echo "Obtendo Lista de NFs para enviar \n";
        $retornos = $this->RetornoNf->listaParaEnviar();
        if($retornos) {
            echo 'Notas Obtidas: '. count($retornos) .' agendados:'. $enviados . "\n";
            foreach($retornos as $retorno) {
                $links = $this->Notafis->linksFaturamento($retorno);
                if (isset($links['links']['boleto']) && !empty($links['links']['boleto'])) {
                    $enviados++;
                    $this->scheduleMail($links);
                    $this->marcaEnvio($retorno, $links);
                    echo 'Notas Obtidas: '. count($retornos) .' / Agendamentos:'. $enviados . "\n";
                }
            }
        }
        echo 'Notas Obtidas: '. count($retornos) .' agendados:'. $enviados . (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? '' : ' *** 123, testando! ***') . "\n";
    }
		
    private function scheduleMail($links) {
        $this->stringView->reset();
        $this->stringView->set('links', $links);
        $content = $this->stringView->renderMail('emails_faturamento');
        $this->scheduler->schedule($content, array(
                'from' => 'nfe@rhhealth.com.br',
                'to' => implode(';', $links['emails']),
                'subject' => 'Faturamento - ' . $links['NotaFiscal']['numnfe'] . ' emitido em ' . substr($links['NotaFiscal']['data_emissao'], 0, 10) . " - " . $links['Cliente']['codigo']
            ),
            $links['model'],
            $links['foreign_key']
        );
    }
	
	private function marcaEnvio($retorno, $links) {
	    $this->RetornoNf->marcaEnvio($retorno, $links);
	}
}
?>
 