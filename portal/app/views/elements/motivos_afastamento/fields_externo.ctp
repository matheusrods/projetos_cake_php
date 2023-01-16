<div class="container">
<div class='row-fluid inline'>
	
	<?php echo $this->BForm->input('MotivoAfastamento.descricao', array('label' => 'Descrição (*)', 'class' => 'input-xxlarge', 'readonly' => true,)); ?>
	
	<?php echo $this->BForm->input('MotivoAfastamento.ativo', array('label' => 'Status (*)', 'class' => 'input', 'readonly' => true, 'empty' => 'Status', 'options' => array(1 => 'Ativo', 0 => 'Inativo'))); ?>
	
	<?php echo $this->BForm->input('MotivoAfastamentoExterno.codigo_externo', array('label' => 'Código Externo (*)', 'class' => 'input-large' )); ?>
</div>  

<?php echo $this->BForm->hidden('codigo'); ?>  
<?php echo $this->BForm->hidden('codigo_motivos_afastamento', array('value' => $this->data['MotivoAfastamento']['codigo'])); ?>
<?php echo $this->BForm->hidden('codigo_cliente',array('value'=>$codigo_cliente)); ?>

 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'motivos_afastamento', 'action' => 'index_externo'), array('class' => 'btn')); ?>
</div>
</div>

