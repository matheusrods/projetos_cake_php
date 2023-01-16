<?php echo $this->BForm->create('AtendimentoSm', array('url' => array('action' => 'incluir', $this->passedArgs[0]))); ?>
<?php echo $this->element('atendimentos_sms/fields'); ?>