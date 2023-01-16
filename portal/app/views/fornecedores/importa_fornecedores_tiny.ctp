

<?php echo $this->BForm->create('Fornecedor', array('url' => array('controller' => 'fornecedores'), 'type' => 'file')); ?>
<h4>Importar fornecedores do sistema Tiny</h4>
<?php echo $this->Form->input('documento', array('type' => 'file')); ?>
<div>&nbsp;</div>
<?php echo $this->Form->submit('Importar', array('class' => 'btn btn-primary')); ?>
<?php echo $this->Form->end(); ?>