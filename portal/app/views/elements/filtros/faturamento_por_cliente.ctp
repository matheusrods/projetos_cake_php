<div class='well'>
	<?php echo $this->Bajax->form('ClienteFaturamento', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteFaturamento', 'element_name' => 'faturamento_por_cliente'), 'divupdate' => '.form-procurar')) ?>
	<div class="row-fluid inline">
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false, 'ClienteFaturamento'); ?>
		<?php echo $this->BForm->input('mes_referencia', array('class' => 'input-medium', 'label' => false, 'options' => $meses)); ?>
		<?php echo $this->BForm->input('ano_referencia', array('class' => 'input-small', 'label' => false)); ?>
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $this->BForm->end();?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaFaturamentoPorCliente();
    });', false);
?>