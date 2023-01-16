<?php echo $this->BForm->create('FormaPagto', array('url' => array('controller' => 'formas_pagto', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('formas_pagto/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>