<?php echo $this->BForm->create('LiberacaoProvisoria', array('url' => array('action' => 'incluir'))); ?>
<?php echo $this->element('liberacoes_provisorias/fields', array('edit_mode' => false)); ?>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?php echo $this->Html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>   
<?php echo $this->BForm->end(); ?>