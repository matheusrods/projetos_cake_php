<?php echo $this->BForm->create('ExameFuncao', array('url' => array('controller' => 'exames_funcoes', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('exames_funcoes/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>