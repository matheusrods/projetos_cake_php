<?php //debug($this->validationErrors); ?>

<div class='well'>
	<?php if($edit_mode): ?>
		<?php echo $this->BForm->hidden('codigo'); ?>
	<?php endif; ?>

    <div class="row-fluid inline">
		<?php echo $this->BForm->input('risco', array('class' => 'input-large', 'placeholder' => 'Risco', 'label' => 'Risco (*)')) ?>

        <?php echo $this->BForm->input('codigo_esocial', array('class' => 'input-medim', 'placeholder' => 'Código e-Social', 'label' => 'Código e-Social (*)')) ?>

        <?php echo $this->BForm->input('codigo_grupo_risco', array('label' => 'Grupo do risco (*)','class' => 'input-xlarge', 'options'=> $combo_grupo_risco, 'empty' => 'Todos', 'default' => ' ')); ?>

	</div>

	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();
	});
'); ?>
