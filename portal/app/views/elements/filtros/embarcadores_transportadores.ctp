<?php echo $this->Bajax->form('EmbarcadorTransportador', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'EmbarcadorTransportador', 'element_name' => 'embarcadores_transportadores'), 'divupdate' => '.form-procurar')) ?>
	<div class="row-fluid inline">
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_embarcador', 'Embarcador',false,'EmbarcadorTransportador') ?>
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_transportador', 'Transportador',false,'EmbarcadorTransportador') ?>
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_pagador', 'Pagador',false,'EmbarcadorTransportador') ?>		
	</div>
	<div class="row-fluid inline">	
		<?php echo $this->BForm->input('codigo_produto', array('type' => 'select', 'options' => $produtos, 'class' => 'input-large', 'label' => false, 'empty' => 'Todos Produtos')); ?>
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
<?php echo $this->BForm->end();?>

<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function() {
	atualizaListaEmbarcadoresTransportadores();
	jQuery("#limpar-filtro").click(function(){
		bloquearDiv(jQuery(".form-procurar"));
		jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:EmbarcadorTransportador/element_name:embarcadores_transportadores/" + Math.random())
	});
})') ?>