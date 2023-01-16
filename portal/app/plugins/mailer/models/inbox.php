<?php
class Inbox extends MailerAppModel {

	var $name = 'Inbox';
	var $useTable = 'mailer_inboxes';
	var $tableSchema = 'publico';
	var $databaseTable = 'dbBuonny';
	
	public function findNotProcessed() {
		return $this->find('all', array('conditions'=>array('processed'=>false)));
	}
	
	public function saveMailMessage($message) {
		$data = array(
			'from' => $message->from,
			'to' => $message->to,
			'subject' => $message->subject,
			'content' => $message->getContent(),
			'received' => date('Y-m-d')
		);
		$this->create();
		return $this->save($data);
	}
	
	public function markProcessed($message) {
		$message['Inbox']['processed'] = true;
		return $this->save($message);
	}

}
?>	
