<?php
class Outbox extends MailerAppModel {

    var $name = 'Outbox';
    var $useTable = 'mailer_outbox';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';

    public function findNextMailsToSend($limit){
        $conditions = array('Outbox.sent'=>null, 'or' => array('Outbox.liberar_envio_em' => null, 'Outbox.liberar_envio_em <=' => date('Ymd H:i:s')));
        return $this->find('all', array('conditions'=>$conditions, 'limit'=>$limit));
    }

    public function marcaEnviado($id){
        $this->id = $id;
        $this->saveField('sent', date('Ymd H:i:s'));
    }

    public function cancelaEnvio($id){
        $this->id = $id;
        $this->saveField('sent', date('19500101 00:00:00'));
    }
}
?>