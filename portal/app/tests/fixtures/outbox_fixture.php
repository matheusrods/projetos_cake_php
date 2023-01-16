<?php
class OutboxFixture extends CakeTestFixture {
    public $name = 'Outbox';
    public $table = 'mailer_outbox';
    public $fields = array(
        'id' => array('type' => 'integer', 'null' => true,'key' => 'primary'),
        'to' => array('type' => 'string', 'null' => true),
        'subject' => array('type' => 'string', 'null' => true),
        'content' => array('type' => 'string', 'null' => true),
        'sent' => array('type' => 'datetime'),
        'created' => array('type' => 'datetime', 'default' => '(getdate())'),
        'modified' => array('type' => 'datetime'),
        'liberar_envio_em' => array('type' => 'datetime')
        );
    
    public $records = array();
}