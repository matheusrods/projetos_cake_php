<div class='form-procurar well'>
	<?php echo $this->BForm->create('SmsOutbox', array('type' => 'file', 'autocomplete' => 'off', 'url' => array('controller' => 'sms', 'action' => 'importar'))); ?>
		<div class="row-fluid inline">	
			<?php echo $this->BForm->input('arquivo', array('type'=>'file', 'label' => false)); ?>
			<?php echo $this->Html->link('<i class="icon-file"></i>Documentação SMS', $this->webroot.'../../arquivos/modelo_importacao_sms.csv', array('escape' => false, 'target' => '_blank',  'title' => 'Visualizar documentação da integração')); ?>
		</div>
		<?php echo $this->BForm->submit('Importar', array('div' => false, 'class' => 'btn btn-primary')); ?>
		<?= $html->link('Voltar', array('controller'=>'sms','action' => 'index'), array('class' => 'btn')); ?>					
	<?php echo $this->BForm->end(); ?>
</div>

