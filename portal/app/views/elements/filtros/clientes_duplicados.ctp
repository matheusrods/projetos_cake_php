<?php echo $this->Bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => 'clientes_duplicados'), 'divupdate' => '.form-procurar')) ?>
	<div class="row-fluid inline">
		<?php echo $this->Buonny->input_codigo_cliente($this,'codigo_cliente_pagador','Pagador') ?>
	</div>

	<div class="row-fluid inline">
      <?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
      <?php echo $this->BForm->input('razao_social', array('class' => 'input-medium', 'placeholder' => 'Nome', 'label' => false)) ?>
      <?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium', 'placeholder' => 'CNPJ/CPF', 'label' => false)) ?>
      <?php echo $this->BForm->input('inscricao_estadual', array('class' => 'input-medium', 'placeholder' => 'RG/Inscrição Estadual', 'label' => false)) ?>
      <?php echo $this->BForm->input('ativo', array('label' => false, 'class' => 'input-small', 'options' => array('Inativos', 'Ativos'), 'empty' => 'Todos')); ?>
    </div>        

	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
<?php echo $this->BForm->end();?>

<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function() {
	atualizaListaClientesDuplicados();
	jQuery("#limpar-filtro").click(function(){
		bloquearDiv(jQuery(".form-procurar"));
		jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cliente/element_name:clientes_duplicados/" + Math.random())
	});
})') ?>