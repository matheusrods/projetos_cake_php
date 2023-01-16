<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
	<?php echo $this->BForm->input('nome', array('class' => 'input-large', 'placeholder' => 'Nome Fantasia', 'label' => false)) ?>
	<?php echo $this->BForm->input('razao_social', array('class' => 'input-large', 'placeholder' => 'Razão Social', 'label' => false)) ?>
	<?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium', 'placeholder' => 'CNPJ', 'label' => false)) ?>
	<?php echo $this->BForm->hidden('searcher', array('value' => !empty($searcher)? $searcher : $this->data['Fornecedor']['searcher'])); ?>
	<?php echo $this->BForm->hidden('display', array('value' => !empty($display)? $display : $this->data['Fornecedor']['display'])); ?>

</div>        
<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-fornecedores', 'class' => 'btn')) ;?>