<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'UsuarioHistorico') ?>
	<?php echo $this->BForm->input('codigo_documento_cliente', array('class' => 'input-medium', 'placeholder' => 'Cnpj Cliente', 'label' => 'CNPJ/CPF')) ?>
	<?php echo $this->BForm->input('razao_social_cliente', array('class' => 'input-xlarge', 'placeholder' => 'Razão Social', 'label' => 'Razão Social')) ?>
	<?php echo $this->BForm->input('nome_fantasia_cliente', array('class' => 'input-xlarge', 'placeholder' => 'Nome Fantasia', 'label' => 'Nome Fantasia')) ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor', 'Código','Fornecedor','UsuarioHistorico');?>
	<?php echo $this->BForm->input('codigo_documento_fornecedor', array('class' => 'input-medium', 'placeholder' => 'Cnpj Prestador', 'label' => 'CNPJ/CPF')) ?>
	<?php echo $this->BForm->input('razao_social_fornecedor', array('class' => 'input-xlarge', 'placeholder' => 'Razão Social', 'label' => 'Razão Social')) ?>						  
	<?php echo $this->BForm->input('nome_fantasia_fornecedor', array('class' => 'input-xlarge', 'placeholder' => 'Nome Fantasia', 'label' => 'Nome Fantasia')) ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo_uperfil', array('class' => 'input-medium', 'label' => 'Perfil de Acesso', 'options' => $u_perfis, 'empty' => 'Selecione')); ?>
	<?php echo $this->BForm->input('tipo_usuario', array('class' => 'input-medium', 'label' => 'Tipo de Usuário', 'options' => $tipo_user, 'empty' => 'Selecione')); ?>
	<?php echo $this->BForm->input('login', array('class' => 'input-medium', 'label' => 'Login')) ?>
</div>
<div class="row fluid">	
	<div class="span1" style="padding-top: 5px">
		<span class="label label-success">Periodo:</span>
	</div>
	<div class="span2" style="margin-left: 0%" >
		<?php echo $this->BForm->input('data_inicio', array('label' => false, 'place-holder' => 'Afastamento', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
	</div>
	<div class="span1" style="padding-top: 6px;margin-left: -4%">
		até
	</div>
	<div class="span2" style="margin-left: -3%" >
		<?php echo $this->BForm->input('data_fim', array('label' => false, 'place-holder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
	</div>
</div>
<?php echo $this->Javascript->codeBlock('$(document).ready(function() {setup_mascaras();});');
?>