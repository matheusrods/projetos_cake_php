<?php echo $this->BForm->create('AlertaTipo', array('url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AlertaTipo', 'return' => 'alertas_tipos'), 'type' => 'POST')) ?>
<div class="row-fluid inline">
  	<?php echo $this->BForm->input('descricao', array('class' => 'input-medium', 'placeholder' => 'Descrição', 'label' => false)) ?>
</div>        
<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
<?php echo $this->BForm->end(); ?>