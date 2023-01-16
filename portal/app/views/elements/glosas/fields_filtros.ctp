<div class="row-fluid inline">
	<?php echo $this->BForm->input('numero_nota_fiscal', array('class' => 'input-medium', 'label' => 'Número Nota Fiscal')) ?>
	<div class="span1" style="padding-top: 29px;margin-left: 4px;">
		<span class="label label-success">Periodo:</span>
	</div>
	<div class="span2" style="margin-left: 0%;padding-top: 19px;" >
		<?php echo $this->BForm->input('data_inicio', array('label' => false, 'place-holder' => 'Afastamento', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
	</div>
	<div class="span1" style="padding-top: 26px;margin-left: -4%">
		até
	</div>
	<div class="span2" style="margin-left: -3%;padding-top: 19px;" >
		<?php echo $this->BForm->input('data_fim', array('label' => false, 'place-holder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
	</div>
</div>
<div class="row-fluid inline">	
	<?php echo $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor', 'Código','Credenciado','Glosas'); ?>
	<?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium', 'placeholder' => false, 'label' => 'CNPJ')) ?>	
	<?php echo $this->BForm->input('razao_social', array('class' => 'input-xlarge', 'placeholder' => 'Razão Social', 'label' => 'Razão Social')) ?>						  
	<?php echo $this->BForm->input('nome_fantasia', array('class' => 'input-xlarge', 'placeholder' => 'Nome Fantasia', 'label' => 'Nome Fantasia')) ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('tipo_glosa', array('options' => $tipos_glosas, 'empty' => 'Todos', 'class' => 'input-xlarge bselect2', 'label' => 'Tipo de Glosa')); ?>
	<?php echo $this->BForm->input('ativo', array('label' => 'Status', 'class' => 'input-small', 'default' => 1,'options' => array(0 => 'Inativo', 1 => 'Ativo'))); ?>
</div>	
<?php 
	echo $this->Javascript->codeBlock("	
		setup_mascaras();
	");
?>