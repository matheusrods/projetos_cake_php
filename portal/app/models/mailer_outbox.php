<?php
class MailerOutbox extends AppModel {

	var $name = 'MailerOutbox';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'mailer_outbox';
	var $primaryKey = 'id';
	var $actsAs = array('Secure');
    
   public function enviaEmail($dados, $assunto, $template, $to, $attachment = null) {
		if(Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO) {
			$to = 'tid@ithealth.com.br';
		}

		App::import('Component', array('StringView', 'Mailer.Scheduler'));

		$this->stringView = new StringViewComponent();
		$this->scheduler = new SchedulerComponent();
		$this->stringView->reset();
		$this->stringView->set('dados', $dados);
		$content = $this->stringView->renderMail($template);
		return $this->scheduler->schedule($content, array (
			'from' => 'portal@rhhealth.com.br',
			'to' => $to,
			'subject' => $assunto,
			'attachments' => $attachment
		));
	}

}
