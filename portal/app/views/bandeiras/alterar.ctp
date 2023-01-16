<?php echo $this->BForm->create('TBandBandeira', array('url' => array('controller' => 'Bandeiras','action' => 'alterar',$cliente['Cliente']['codigo'],$this->data['TBandBandeira']['band_codigo'])));?>
	<div class='row-fluid inline'>
		<div id="cliente" class='well'>
			<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
			<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
		</div>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('band_descricao', array('label' => 'Descrição', 'type' => 'text','class' => 'input-xxlarge', 'placeholder' => 'Descrição')) ?>
	</div>
	<?php echo $this->BForm->hidden('band_codigo') ?>

	<div class="form-actions">
		  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
		  <?php echo $html->link('Voltar',array('controller' => 'Bandeiras', 'action' => 'index'), array('class' => 'btn')) ;?>
	</div>

<?php echo $this->BForm->end(); ?>