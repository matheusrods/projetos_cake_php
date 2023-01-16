<div class="row-fluid inline">
<?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'label' => 'Código', 'type' => 'text'));?>
<?php echo $this->BForm->input('razao_social', array('class' => 'input-xlarge', 'label' => 'Razão Social')); ?>  
<?php echo $this->BForm->input('nome_fantasia', array('class' => 'input-xlarge', 'label' => 'Nome Fantasia')); ?>  
<?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium', 'label' => 'CNPJ')); ?>  
<?php echo $this->BForm->input('codigo_status_multi_empresa', array('class' => 'input-small', 'options' => array('1' => 'Período Experimental', '2' => 'Ativos', '3' => 'Inativos'), 'empty' => 'Status', 'default' => ' ', 'label' => 'Status')); ?>
</div>        