<?php echo $this->BForm->create('Pmps', array('url' => array('controller' => 'pmps', 'action' => 'store'))); ?>

	<?php echo $this->element('pmps/fields'); ?>
	
    <div class="form-actions">
      <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
      <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
    </div>
    
<?php echo $this->BForm->end(); ?>