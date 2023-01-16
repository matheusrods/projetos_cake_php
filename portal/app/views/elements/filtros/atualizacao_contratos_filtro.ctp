<div class='well'>
	<?php echo $this->Bajax->form('ClienteProdutoContrato', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteProdutoContrato', 'element_name' => 'atualizacao_contratos_filtro'), 'divupdate' => '.form-procurar')) ?>
	<div class="row-fluid inline">
		<?php echo $this->Buonny->input_periodo($this, 'ClienteProdutoContrato', 'data_inicial', 'data_final', true) ?>
		<?php echo $this->BForm->input('codigo_produto', array('label' => 'Produto', 'options' => $produtos)); ?>
        <?php echo $this->BForm->input('igpm', array('placeholder' => 'IGPM', 'class' => 'input-small', 'label' => 'Índice acumulado nos últimos 12 meses (em %)')); ?>
    </div>    
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $this->BForm->end();?>
</div>
<?php echo $this->Javascript->codeBlock('
		$(function(){
			atualizaListaAtualizarContratos();
		});', false);
?>