<?php

class OutboxFixture extends CakeTestFixture {
	var $name = 'MailerOutbox';
	var $table = 'mailer_outbox';
	var $fields = array(
		'id' => array('type'=>'integer', 'default' => NULL,   'key' => 'primary'),
		'to' => array('type'=>'string', 'default' => NULL,  ),
		'subject' => array('type'=>'string', 'default' => NULL,  ),
		'content' => array('type'=>'string', 'default' => NULL,  ),
		'sent' => array('type'=>'datetime', 'null' => true,  ),
		'liberar_envio_em' => array('type'=>'datetime', 'null' => true, 'default' => NULL,  ),
		'created' => array('type'=>'datetime', 'default' => NULL,  ),
		'modified' => array('type'=>'datetime', 'default' => NULL,  ),
		'from' => array('type'=>'string', 'default' => NULL,  ),
		'cc' => array('type'=>'string', 'null' => true,  ),
		'model' => array('type'=>'string', 'null' => true,  ),
		'foreign_key' => array('type'=>'string', 'null' => true,  ),
		'indexes' => array('0' => array())
	);
	
	var $records = array(
		array(
			'to' => 'lancamentosautomaticos@buonny.com.br',
			'subject' => 'DOC enviado acima de um valor',
			'content' => 'texto',
			'sent' => '2010-04-08 12:05:10',
			'liberar_envio_em' => null,
			'created' => '2010-04-27 10:07:18',
			'modified' => '2010-04-27 10:07:18',
			'from' => 'buonny@buonny.com.br',
			'cc' => null,
			'model' => null,
			'foreign_key' => null,
		),
		array(
			'to' => 'lancamentosautomaticos@buonny.com.br',
			'subject' => 'DOC enviado acima de um valor',
			'content' => 'texto',
			'sent' => null,
			'liberar_envio_em' => null,
			'created' => '2010-04-27 10:07:18',
			'modified' => '2010-04-27 10:07:18',
			'from' => 'buonny@buonny.com.br',
			'cc' => null,
			'model' => null,
			'foreign_key' => null,
		),
		array(
			'to' => 'nfe@cliente.com.br',
			'subject' => 'Faturamento',
			'content' => 'texto',
			'sent' => '2010-04-27 10:07:18',
			'liberar_envio_em' => null,
			'created' => '2010-04-27 10:07:18',
			'modified' => '2010-04-27 10:07:18',
			'from' => 'faturamento@buonny.com.br',
			'cc' => null,
			'model' => 'RetornoNf',
			'foreign_key' => 5,
		),
                array(
			'to' => 'melao.nfe@teste.com',
			'subject' => 'Faturamento',
			'content' => 'texto',
			'sent' => '2010-04-27 10:07:18',
			'liberar_envio_em' => null,
			'created' => '2010-04-27 10:07:18',
			'modified' => '2010-04-27 10:07:18',
			'from' => 'faturamento@buonny.com.br',
			'cc' => null,
			'model' => 'RetornoNf',
			'foreign_key' => 5,
		)
	);
}

?>