<div class='row-fluid inline'>
	
	
	<?php echo $this->BForm->input('codigo_criterio', array('options' =>$criterios, 'empty' => '--- Selecione ---', 'label' => 'Criterios', 'class' => 'input-xlarge')); ?>
	<?php echo $this->BForm->input('descricao',array('label'=>'Status'));?>
	<?php echo $this->BForm->input('intervalo_minimo',array('label'=>'Invervalo mínimo', 'class'=>'just-number'));?>

</div>
<div class='form-actions'>
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function(){
	setup_mascaras();
})")?>