<?php echo $this->BForm->create('ExameFuncao', array('url' => array('controller' => 'exames_funcoes','action' => 'incluir'))); ?>
<?php echo $this->element('exames_funcoes/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>