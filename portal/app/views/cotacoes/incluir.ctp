<?php echo $this->BForm->create('Cotacao', array('url' => array('controller' => 'cotacoes', 'action' => 'incluir', $passo, $codigo))); ?>
<?php echo $this->element('cotacoes/fields'.(!empty($passo)? '_'.$passo : ''), array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>