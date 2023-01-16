<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo_cbo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => 'Código', 'type' => 'text')) ?>
	<?php echo $this->BForm->input('descricao_cbo', array('class' => 'input-xlarge', 'placeholder' => 'Descrição CBO', 'label' => 'Descrição CBO')) ?>
</div>  
<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>