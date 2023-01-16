
<?php echo $this->BForm->create('TViagViagem', array('type' => 'post','url' => array('controller' => 'Viagens','action' => 'alterar_pedido_cliente',$cliente['Cliente']['codigo'],$this->data['TViagViagem']['viag_codigo'])));?>
<div class='row-fluid inline'>
	<div id="cliente" class='well'>
		<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
		<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
	</div>

	<div id="destino" class='well'>
		<strong>SM: </strong><?php echo $this->Buonny->codigo_sm($this->data['TViagViagem']['viag_codigo_sm']) ?>
	</div>
	<div class="row-fluid inline" >
		<?php echo $this->BForm->hidden('viag_codigo') ?>
		<?php echo $this->BForm->hidden('viag_codigo_sm') ?>
		<?php echo $this->BForm->input('viag_pedido_cliente', array('class' => 'input-medium', 'label' => 'Pedido Cliente')) ?>
	</div>
</div>
<div class="form-actions">
	  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
	  <?php echo $html->link('Voltar', 'itinerarios', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
</div>

<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
		setup_datepicker();
		setup_time();
	});', false);
?>