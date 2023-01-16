<?php echo $this->BForm->create('AlertaAgrupamento', array('url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AlertaAgrupamento', 'return' => 'alertas_agrupamentos'), 'type' => 'POST')) ?>
<div class="row-fluid inline">
  	<?php echo $this->BForm->input('descricao', array('class' => 'input-medium', 'placeholder' => 'Descrição', 'label' => false)) ?>
</div>        
<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
<?php echo $this->BForm->end(); ?>