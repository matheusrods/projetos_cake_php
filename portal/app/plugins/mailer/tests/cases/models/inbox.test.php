<?php
class InboxCase extends CakeTestCase {
	var $fixtures = array('plugin.mailer.inbox', 'app.usuario');
	
	function startTest() {
		$this->Inbox =& ClassRegistry::init('Mailer.Inbox');
	}
	
	public function testSaveMailMessage(){
		$mail = new MailMock();
		$result = $this->Inbox->saveMailMessage($mail);
		$this->assertTrue($result);
	}
	
}

class MailMock {
	var $from = 'itau@itau.com.br';
	var $to = 'teste@teste.com.br';
	var $subject = 'Mensagem mock';
	var $received = '12/10/2010';
	function getContent() {
		return 'Conteúdo da mensagem.';
	}
}
?>