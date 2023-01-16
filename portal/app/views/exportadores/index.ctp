<div class='form-procurar well'>
	<?php echo $this->BForm->create('Exportador', array('autocomplete' => 'off', 'url' => array('controller' => 'exportadores', 'action' => 'index'))); ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('tipo_exportacao', array('label' => false, 'class' => 'input-small', 'options' => array('1' => 'Ace', '2' => 'Aig'))); ?>
			<?php echo $this->BForm->input('quantidade', array('label' => false, 'class' => 'input-small numeric', 'placeholder' => 'Quantidade')); ?>
			<?php if (isset($error_message)): ?>
				<p class='text-error'><?= $error_message ?></p>
			<?php endif ?>
		</div>
		<?php echo $this->BForm->submit('Exportar', array('div' => false)); ?>
	<?php echo $this->BForm->end(); ?>
</div>
<div>
	<?php foreach ($arquivos as $arquivo): ?>
		<?php $nome_arquivo = substr($arquivo,strrpos($arquivo,DS)+1) ?>
		<div><?= $this->Html->link($nome_arquivo, '/files/export/'.$nome_arquivo) ?></div>
	<?php endforeach ?>
</div>