<?php echo $this->BForm->create('TRegiRegiao', array('url' => array('controller' => 'Regioes','action' => 'alterar',$cliente['Cliente']['codigo'],$this->data['TRegiRegiao']['regi_codigo'])));?>
	<div class='row-fluid inline'>
		<div id="cliente" class='well'>
			<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
			<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
		</div>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('regi_descricao', array('label' => 'Descrição', 'type' => 'text','class' => 'input-xxlarge', 'placeholder' => 'Descrição')) ?>
	</div>
	<?php echo $this->BForm->hidden('regi_codigo') ?>

	<div class="form-actions">
		  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
		  <?php echo $html->link('Voltar',array('controller' => 'Regioes', 'action' => 'index'), array('class' => 'btn')) ;?>
	</div>

<?php echo $this->BForm->end(); ?>