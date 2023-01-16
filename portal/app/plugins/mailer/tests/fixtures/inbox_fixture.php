<?php

class InboxFixture extends CakeTestFixture {
	var $name = 'MailerInbox';
	//var $import = array('table'=>'mailer_inbox');
	var $fields = array(
		'id' => array('type'=>'integer', 'default' => NULL,   'key' => 'primary'),
		'from' => array('type'=>'string', 'default' => NULL,  ),
		'to' => array('type'=>'string', 'default' => NULL,  ),
		'subject' => array('type'=>'string', 'default' => NULL,  ),
		'content' => array('type'=>'string', 'default' => NULL,  ),
		'processed' => array('type'=>'boolean', 'default' => 0,  ),
		'received' => array('type'=>'datetime', 'default' => NULL,  ),
		'created' => array('type'=>'datetime', 'default' => NULL,  ),
		'modified' => array('type'=>'datetime', 'default' => NULL,  ),
		'indexes' => array('0' => array())
	);
	
	var $records = array(
		array(
			'from' => 'Nelson Ota <nelson.ota@buonny.com.br>',
			'to' => 'lancamentosautomaticos@buonny.com.br',
			'subject' => 'DOC enviado acima de um valor',
			'content' => 'texto',
			'processed' => 0,
			'received' => '2010-04-08 12:05:10',
			'created' => '2010-04-27 10:07:18',
			'modified' => '2010-04-27 10:07:18',
		),
		array(
			'from' => 'Nelson Ota <nelson.ota@buonny.com.br>',
			'to' => 'lancamentosautomaticos@buonny.com.br',
			'subject' => 'Pagamento on-line de titulos, agua, luz, telefone e gas acim',
			'content' => 'texto2',
			'processed' => 0,
			'received' => '2010-08-10 09:23:40',
			'created' => '2010-04-27 10:07:19',
			'modified' => '2010-04-27 10:07:19',
		),
		array(
			'from' => 'Nelson Ota <nelson.ota@buonny.com.br>',
			'to' => 'lancamentosautomaticos@buonny.com.br',
			'subject' => 'Compra com cartao de debito acima de valor',
			'content' => 'texto3',
			'processed' => 0,
			'received' => '2009-04-26 10:07:19',
			'created' => '2010-04-27 10:07:19',
			'modified' => '2010-04-27 10:07:19',
		),
		array(
			'from' => 'Henrique Santos <henrique.santos@buonny.com.br>',
			'to' => 'lancamentosautomaticos@buonny.com.br',
			'subject' => 'DOC enviado acima de um valor',
			'content' => 'texto4',
			'processed' => 0,
			'received' => '2010-02-23 10:07:19',
			'created' => '2010-04-27 10:07:20',
			'modified' => '2010-04-27 10:07:20',
		),
		array(
			'from' => 'Henrique Santos <henrique.santos@buonny.com.br>',
			'to' => 'lancamentosautomaticos@buonny.com.br',
			'subject' => 'Compra com cartao de debito acima de valor',
			'content' => 'texto5',
			'processed' => 0,
			'received' => '2010-05-06 10:07:19',
			'created' => '2010-05-06 10:07:20',
			'modified' => '2010-05-07 10:07:20',
		),
		
		
	);
}

?>