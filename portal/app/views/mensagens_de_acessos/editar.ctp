<?php echo $this->BForm->create('MensagemDeAcesso', array('url'=>array('controller'=>'mensagens_de_acessos', 'action'=>'editar')));?>
<?php echo $this->element('mensagens_de_acessos/fields'); ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>