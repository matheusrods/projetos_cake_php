<?php
class OutboxCase extends CakeTestCase {
	var $fixtures = array(
            'plugin.mailer.outbox',
            'app.usuario',
            'app.cliente',
            'app.cliente_tipo',
            'app.cliente_contato',
            'app.tipo_contato',
            'app.tipo_retorno',
            'app.notafis',
            'app.retorno_nf',
            'app.retorno_nf_link',
            'app.cliente_sub_tipo'
        );
	
	function startTest() {
            $this->Outbox           = ClassRegistry::init('Mailer.Outbox');
            $this->Cliente          = ClassRegistry::init('Cliente');
            $this->ClienteContato   = ClassRegistry::init('ClienteContato');
            $this->RetornoNf        = ClassRegistry::init('RetornoNf');
	}
	
	function testFindNextMailsToSend() {
	    $results = $this->Outbox->findNextMailsToSend(60);
	    $this->assertEqual(1, count($results));
	}
	
	function testFindNextMailsToSendAgendado(){
	    $data = array(
	        'Outbox' => array(
	            'to' => 'teste@teste.com',
	            'from' => 'ronaldo@buonny.com.br',
	            'cc' => null,
	            'subject' => 'teste',
	            'content' => 'teste',
	            'liberar_envio_em' => Date('Y-m-d H:i:s', strtotime('+10 minute', mktime()))
	        )
	    );
	    $this->Outbox->create();
	    $this->assertTrue($this->Outbox->save($data));
	    $results = $this->Outbox->findNextMailsToSend(60);
	    $this->assertEqual(1, count($results));
	    
	    $data = array(
	        'Outbox' => array(
	            'to' => 'teste@teste.com',
	            'from' => 'ronaldo@buonny.com.br',
	            'cc' => null,
	            'subject' => 'teste',
	            'content' => 'teste',
	            'liberar_envio_em' => Date('Y-m-d H:i:s', strtotime('-1 minute', mktime()))
	        )
	    );
	    
	    $this->Outbox->create();
	    $this->assertTrue($this->Outbox->save($data));	    
	    $results = $this->Outbox->findNextMailsToSend(60);
	    $this->assertEqual(2, count($results));
	}
        
        function endTest() {
	    unset($this->Outbox);
	    unset($this->Cliente);
	    unset($this->ClienteContato);
	}
	
}
?>