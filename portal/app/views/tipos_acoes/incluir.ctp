<?php echo $this->BForm->create('TipoAcao', array('url' => array('controller' => 'tipos_acoes', 'action' => 'store'))); ?>

	<?php echo $this->element('tipos_acoes/fields'); ?>
	
    <div class="form-actions">
      <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
      <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
    </div>
    
<?php echo $this->BForm->end(); ?>