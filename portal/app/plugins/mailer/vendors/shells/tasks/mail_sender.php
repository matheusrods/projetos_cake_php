<?php
class MailSenderTask extends Shell {
	
	var $uses = array('Mailer.Outbox');
	
	public function __construct(){
		App::import('Component', 'Mailer.Mailer');
		$this->Mailer = new MailerComponent();
	}
	
	public function sendNext($limit) {
		$counter = array('sucesso'=>0, 'falha'=>0);
		$emails = $this->Outbox->findNextMailsToSend($limit);
		foreach($emails as $email){
			$enviado = $this->send($email);
			if (!$enviado && !empty($this->Mailer->Email->smtpError)) { 
				$this->log($this->Mailer->Email->smtpError); 
				$this->log('Outbox.id:'.$email['Outbox']['id']); 
			}
			$counter[$enviado ? 'sucesso' : 'falha'] += 1;
		}
		$nemails = count($emails);
		if($nemails > 0)
			echo "Total: {$nemails}. Sucesso: {$counter['sucesso']}. Falha: {$counter['falha']}. Fim: ".date("d/m/Y H:i:s")."\n";
	}
	
	private function send($email){
		$options = array_intersect_key($email['Outbox'], array_flip(array('to', 'subject', 'from', 'cc')));
		$options['to'] = Comum::validaEmail($options['to']);
		//Verificar se exite anexo
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
	
}
?>
