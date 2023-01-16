<?php echo $this->Bajax->form('MatrizFilial', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'MatrizFilial', 'element_name' => 'matrizes_filiais'), 'divupdate' => '.form-procurar')) ?>
	<div class="row-fluid inline">
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_matriz', 'Matriz',false,'MatrizFilial') ?>
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_filial', 'Filial',false,'MatrizFilial') ?>
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_pagador', 'Pagador',false,'MatrizFilial') ?>		
	</div>
	<div class="row-fluid inline">	
		<?php echo $this->BForm->input('codigo_produto', array('type' => 'select', 'options' => $produtos, 'class' => 'input-large', 'label' => false, 'empty' => 'Todos Produtos')); ?>
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
<?php echo $this->BForm->end();?>

<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function() {
	atualizaListaMatrizesFiliais();
	jQuery("#limpar-filtro").click(function(){
		bloquearDiv(jQuery(".form-procurar"));
		jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:MatrizFilial/element_name:matrizes_filiais/" + Math.random())
	});
})') ?>