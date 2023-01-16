<?php if ($this->Session->read('Message.flash.params.type') == MSGT_SUCCESS):
        echo $this->Javascript->codeBlock("
            close_dialog('{$this->Buonny->flash()}');
            atualizaListaClientesVipsTeleconsult();
        ");
    exit;
endif; ?>

<div id="cliente" class='well'>
	<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
	<strong>Razão Social: </strong><?= $cliente['Cliente']['razao_social'] ?>
</div>

<?php echo $bajax->form('ClienteProdutoVip', array('url' => array('controller' => 'clientes', 'action' => 'editar_clientes_vips'))); ?>
<div class="row-fluid inline">
    
	<?php 
	if(count($clientes_vips) > 0){
		$i=1;
		foreach ($clientes_vips as $item): ?>
		
		<?php echo $this->BForm->hidden('codigo_'.$i, array('value' => $item['ClienteProdutoVip']['codigo'])); ?>
		<?php echo $this->BForm->hidden('codigo_cliente', array('value' => $cliente['Cliente']['codigo'])); ?>
		<?php echo $this->BForm->hidden('codigo_usuario', array('value' => '')); ?>
		<?php echo $this->BForm->hidden('codigo_produto_'.$i, array('value' => $item['ClienteProdutoVip']['codigo_produto'])); ?>
		
	<?php $i++; endforeach; ?>
	
		<div class="control-group input">
			<label>Vip</label>
		</div>
		<div class="control-group input">
			<?php echo $this->BForm->checkbox('cliente_vip', array('checked' => ($item['ClienteProdutoVip']['cliente_vip'] == 1) ? true : false)) ?>
			<?php echo $this->BForm->hidden('produto', array('value' => 'Teleconsult')); ?>
		</div>
	
	<?php }else{ ?>
	
		<?php echo $this->BForm->hidden('codigo', array('value' => 0)); ?>
		<?php echo $this->BForm->hidden('codigo_cliente', array('value' => $cliente['Cliente']['codigo'])); ?>
		<?php echo $this->BForm->hidden('codigo_produto_1', array('value' => '1')); // Standard ?>
		<?php echo $this->BForm->hidden('codigo_produto_2', array('value' => '2')); // Plus ?>

		<div class="control-group input">
			<label>Vip</label>
		</div>
		<div class="control-group input">
			<?php echo $this->BForm->checkbox('cliente_vip') ?>
			<?php echo $this->BForm->hidden('produto', array('value' => 'Teleconsult')); ?>
		</div>
		
	<?php
	}
	?>

</div>

<div class="form-actions">
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?php echo $html->link('Voltar', '#', array('class' => 'btn closeDialog', 'onclick' => 'close_dialog();')); ?>
</div>
<?php echo $this->BForm->end(); ?>