<div class='well'>
	<?php echo $this->BForm->create('RemessaBancaria', array('type' => 'file' ,'url' => array('controller' => 'remessa_bancaria', 'action' => 'importar_remessa')));?>
		<div class="col-sm-8">
			<h4 class="modal-title" id="gridSystemModalLabel"> UPLOAD DO ARQUIVO DE REMESSA</h4>
		</div>
		<div class="col-sm-8">
			<?php echo $this->BForm->input('RemessaBancaria.remessa', array('type' => 'file', 'class' => 'input-xlarge', 'label' => 'Upload do Arquivo:')); ?>
		</div>
		<div class='form-actions'>
			<?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
			<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
			
		</div>
	<?php echo $this->BForm->end(); ?>
</div>

<?php if(isset($mensagens)):?>
<div class='well'>
	<div class="col-sm-8">
		<h4 class="modal-title" id="gridSystemModalLabel"> <?php echo $mensagens;?> </h4>
	</div>
</div>
<?php endif; ?>