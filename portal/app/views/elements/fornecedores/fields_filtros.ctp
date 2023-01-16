<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
	<?php echo $this->BForm->input('razao_social', array('class' => 'input-large', 'placeholder' => 'Razão Social', 'label' => false)) ?>
	<?php echo $this->BForm->hidden('input_id', array('value' => !empty($input_id)? $input_id : $this->data['Fornecedor']['input_id'])); ?>
	<?php echo $this->BForm->hidden('input_display', array('value' => !empty($input_display)? $input_display : $this->data['Fornecedor']['input_display'])); ?>

</div>        
<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-fornecedores', 'class' => 'btn')) ;?>