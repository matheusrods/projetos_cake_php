<?php
class MailerCase extends CakeTestCase {
	
	public function startCase() {
		App::import('Component', 'Mailer.Mailer');
		App::import('Component', 'Email');
		Mock::generate('EmailComponent');
	}
	
	public function startTest($method){
		$this->Mailer = new MailerComponent();
	}
	
	public function testEnviarEmailComOpcoesPadroes(){
		$this->Mailer->Email = new MockEmailComponent();
		
		$this->Mailer->Email->setReturnValue('send', true);
		$this->Mailer->Email->expectOnce('send');
		
		$this->assertTrue($this->Mailer->send('', array()));
	}
	
	public function testConteudoComOpcoesPadroes(){
		$myMockSession = $this->mockEmailComponent();
		
		$this->Mailer->send('Email de teste', array());
		
		$this->assertTrue(strpos($myMockSession->flash, 'To: ti.desenv@buonny.com.br') !== false);
		$this->assertTrue(strpos($myMockSession->flash, 'Email de teste') !== false);
	}
	
	public function testConfiguracoes(){
		$myMockSession = $this->mockEmailComponent();
		$this->Mailer->send('Email de teste', array(
			'to' => 'tid@ithealth.com.br',
			'cc' => 'tid@ithealth.com.br',
			'subject' => '[[assunto]]'
		));
		$this->assertTrue(strpos($myMockSession->flash, 'To: ti.desenv@buonny.com.br') !== false);
		$this->assertTrue(strpos($myMockSession->flash, "Subject: {$this->Mailer->defaultOptions['subject_prefix']}[[assunto]]") !== false);
	}
	
	private function mockEmailComponent(){
		Mock::generate('Ambiente');
		$this->Mailer->ambiente = new MockAmbiente();
		$this->Mailer->ambiente->setReturnValue('getServidor', Ambiente::SUITE_TESTE);
		
		$myMockSession = new MockSession();
		$this->Mailer->Email->Controller->Session = $myMockSession;
		return $myMockSession;
	}
	
}

class MockSession{
	var $flash = null;
	public function setFlash($flash){
		$this->flash = $flash;
	}
}