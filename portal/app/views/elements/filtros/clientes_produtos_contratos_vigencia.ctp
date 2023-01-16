<div class='well'>
	<?php echo $bajax->form('ClienteProdutoContrato', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteProdutoContrato', 'element_name' => 'clientes_produtos_contratos_vigencia'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
    	<?php echo $this->BForm->input('codigo_cliente', array('class' => 'input-mini just-number', 'placeholder' => false, 'label' => 'Código Cliente', 'type' => 'text')) ?>
		<?php echo $this->BForm->input('data_contrato', array('placeholder' => false, 'class' => 'input-small data', 'type' => 'text', 'label' => 'Data Inicial')); ?>
		<?php echo $this->BForm->input('data_vigencia', array('placeholder' => false, 'class' => 'input-small data', 'type' => 'text', 'label' => 'Data Final')); ?>
		<?php echo $this->BForm->input('codigo_produto', array('label' => 'Produto', 'class' => 'input-medium', 'options' => $produtos, 'empty' => 'TODOS')); ?>
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
jQuery(document).ready(function(){
	var div = jQuery("div.lista");
	bloquearDiv(div);
	div.load(baseUrl + "clientes_produtos_contratos_vigencia/listagem/" + Math.random());
	setup_datepicker(); 
	setup_mascaras();
	jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ClienteProdutoContrato/element_name:clientes_produtos_contratos_vigencia/" + Math.random());
    });
});', false);
?>