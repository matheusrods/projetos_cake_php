<div class="row-fluid inline">
	<?php echo $this->BForm->input('cliente_codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
	<?php echo $this->BForm->input('razao_social', array('class' => 'input-large', 'placeholder' => 'Razão Social', 'label' => false)) ?>
    <?php echo $this->BForm->input('nome_fantasia', array('class' => 'input-large', 'placeholder' => 'Nome Fantasia', 'label' => false)) ?>
    <?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium cnpj', 'placeholder' => 'CNPJ', 'label' => false)) ?>
</div>