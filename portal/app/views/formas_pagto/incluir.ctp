<?php echo $this->BForm->create('FormaPagto', array('url' => array('controller' => 'formas_pagto','action' => 'incluir'))); ?>
<?php echo $this->element('formas_pagto/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>