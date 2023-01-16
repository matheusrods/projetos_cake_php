<?php
class AnexaRelatorioEmailShell extends Shell {	
	var $uses = array('Mailer.Outbox');	
	
	public function main() {
		echo "cake\console\cake anexa_relatorio_email run\n";		
	}

	public function run(){
		if (!$this->im_running()) {
			$this->RelatorioEmail = ClassRegistry::init('RelatorioEmail');
			App::import('Component', array('Mailer.Scheduler'));
			App::import('Component', array('RelatorioExportacao'));
			$conditions  = array('RelatorioEmail.processado IS NULL');
			$limit  = 1;			
			$relatorios  = $this->RelatorioEmail->find('all', compact('conditions', 'limit'));
			if( $relatorios ){
				foreach ($relatorios as $key => $dados ) {
					$this->RelatorioExportacao = new RelatorioExportacaoComponent();
					$conditions = unserialize($dados['RelatorioEmail']['conditions']);
					$email      = $dados['RelatorioEmail']['email'];
					$metodo     = $dados['RelatorioEmail']['metodo'];
					$anexo_nome = '/tmp/'.$dados['RelatorioEmail']['anexo_nome'];
					$arqCsv 	= $anexo_nome.'.csv';
					$this->Scheduler = new SchedulerComponent();					
					$relatorio  = $this->RelatorioExportacao->$metodo( $conditions, FALSE, $arqCsv );
					$nome_zip 	= $anexo_nome.'.zip';
					$zip 		= new ZipArchive();
					$zip->open( $nome_zip, ZipArchive::CREATE );
					$zip->addFile($arqCsv, "{$dados['RelatorioEmail']['anexo_nome']}.csv" );
					$zip->close();
					unlink( $arqCsv );
			        $content = '';
			        $options = array(
						'attachments' => "{$dados['RelatorioEmail']['anexo_nome']}.zip",
			            'from' => 'portal@rhhealth.com.br',
			            'sent' => null,
			            'to'   => $email,
			            'subject' => 'Relatorio',
			        );
					if( $this->Scheduler->schedule($content, $options) ){						
						$dados['RelatorioEmail']['processado'] = date("Y-m-d H:i:s");
						$this->RelatorioEmail->atualizar( $dados );
						$enviado = $this->send( $this->Scheduler->Outbox->id );
						debug( $enviado );
					}
				}
			}
		} else {
			echo "Já em execução";
		}
	}	

	private function send( $codigo_outbox ){
		App::import('Component', 'Mailer.Mailer');
		$this->Mailer = new MailerComponent();
		$email = $this->Outbox->find( 'first', array('conditions'=>array('id'=>$codigo_outbox )) );
		$options = array_intersect_key($email['Outbox'], array_flip(array('to', 'subject', 'from', 'cc')));
		$options['to'] = Comum::validaEmail($options['to']);
		// //Verificar se exite anexo
		if( !empty($email['Outbox']['attachments']) ){
			$options['attachments'] = $email['Outbox']['attachments'];
		}
		if (!$options['to']) {
		    $options['subject'] = $options['to'].' - '.$options['subject'];
		    $options['to'] = $options['from'];
		    $options['cc'] = $options['cc'];
		    $enviado = $this->Mailer->send($email['Outbox']['content'], $options);
		    $enviado = false;
		    $this->Outbox->cancelaEnvio($email['Outbox']['id']);
		} else {
			if (mb_detect_encoding($options['subject'],'UTF-8','ISO-8859-1')!='UTF-8') {
				$options['subject'] = utf8_encode($options['subject']);
			}			
    		$enviado = $this->Mailer->send($email['Outbox']['content'], $options);
    		if($enviado)
    			$this->Outbox->marcaEnviado($email['Outbox']['id']);
    	}
		return $enviado;
	}

	private function im_running() {
		$cmd = shell_exec("ps aux | grep 'anexa_relatorio_email'");
		// 1 execução é a execução atual
		return substr_count($cmd, 'cake.php -working') > 1;
	}
}
?> 