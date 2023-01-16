<?php echo $this->BForm->create('Ocorrencia', array('url' => array('action' => 'incluir', $this->passedArgs[0]))); ?>
<?php echo $this->element('ocorrencias/fields'); ?>