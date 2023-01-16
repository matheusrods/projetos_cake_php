<?php echo  $this->BForm->create('TVmbaViagemModeloBasico',array('url' => array('controller' => 'ViagensModelosBasicos','action' =>'editar', $this->passedArgs[0],$this->passedArgs[1])));?>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('placa', array('class' => 'placa-veiculo input-mini', 'placeholder' => 'Placa', 'label' => 'Placa')); ?>
	<?php echo $this->BForm->input('cpf', array('label' => 'CPF', 'class' => 'input-small cpf')) ?>
	<?php echo $this->BForm->input('vmba_ativo', array('label' => 'Status', 'empty' => 'Status','class' => 'input-small', 'options' =>array(1=>'ATIVO',0=>'INATIVO'))) ?>
</div>

<div class='row-fluid inline'>
	<?php echo $this->BForm->hidden('codigo_cliente'); ?>
	<?php echo $this->BForm->hidden('vmba_codigo'); ?>
	<?php echo $this->BForm->hidden('vmbd_codigo');?>
	<?php echo $this->Buonny->input_referencia($this, '#TVmbaViagemModeloBasicoCodigoCliente', 'TVmbaViagemModeloBasico', 'vmba_refe_codigo_origem', FALSE, 'Origem', TRUE) ?>	
	<?php echo $this->Buonny->input_referencia($this, '#TVmbaViagemModeloBasicoCodigoCliente', 'TVmbdViagemModeloBasicoDest', 'vmbd_refe_codigo', FALSE, 'Destino', TRUE) ?>	
</div>

<div class="form-actions">
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
	<?php echo $html->link('Voltar', 'index', array('id' => 'limpar-filtro', 'class' => 'btn') );?>
	<?php echo $this->BForm->end() ?>
</div>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php echo $this->Javascript->codeBlock('
	$(function(){
		setup_mascaras();

	});', false);
?>