<?php echo  $this->BForm->create('TVmbaViagemModeloBasico',array('url' => array('controller' => 'ViagensModelosBasicos','action' =>'incluir', $this->passedArgs[0], $this->passedArgs[1])));?>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('placa', array('class' => 'placa-veiculo input-mini', 'placeholder' => 'Placa', 'label' => 'Placa')); ?>
	<?php echo $this->BForm->input('cpf', array('label' => 'CPF', 'class' => 'input-small cpf')) ?>
</div>
<div class='row-fluid inline'>	
	<?php echo $this->BForm->hidden('codigo_cliente'); ?>
	<?php echo $this->BForm->hidden('cliente_guardian'); ?>
	<?php echo $this->Buonny->input_referencia($this, '#TVmbaViagemModeloBasicoCodigoCliente', 'TVmbaViagemModeloBasico', 'vmba_refe_codigo_origem', FALSE, 'Origem', TRUE) ?>	
	<?php echo $this->Buonny->input_referencia($this, '#TVmbaViagemModeloBasicoCodigoCliente', 'TVmbdViagemModeloBasicoDest', 'vmbd_refe_codigo', FALSE, 'Destino', TRUE) ?>	
</div>

<div class="form-actions">
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
	<?php echo $html->link('Voltar', 'index', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){ 
        setup_mascaras();
        
    });', false); 
?>