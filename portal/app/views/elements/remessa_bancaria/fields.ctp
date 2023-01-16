
<div class='well'>
	<div class='row-fluid inline'>
		<h5>Dados do Título</h5>
		<?php echo $this->BForm->input('nome_pagador',array('value' =>  !empty($this->data['RemessaBancaria']['nome_pagador'])? $this->data['RemessaBancaria']['nome_pagador'] : '', 'label' => 'Nome', 'placeholder' => 'Nome', 'type' => 'text', 'readonly' => true,'class' => 'input-xlarge form-control')); ?> 

		<?php echo $this->BForm->input('nosso_numero',array('value' =>  $this->data['RemessaBancaria']['nosso_numero'], 'label' => 'Nosso Numero', 'placeholder' => 'Nosso Numero', 'type' => 'text', 'readonly' => true,'class' => 'input-large form-control')); ?> 

		<?php echo $this->BForm->input('data_vencimento',array('value' =>  $this->data['RemessaBancaria']['data_vencimento'], 'label' => 'Data Vencimento', 'placeholder' => 'Data Vencimento', 'type' => 'text', 'readonly' => true,'class' => 'input-large form-control')); ?> 

		<?php echo $this->BForm->input('data_pagamento',array('value' =>  $this->data['RemessaBancaria']['data_pagamento'], 'label' => 'Data Pagamento', 'placeholder' => 'Data Pagamento', 'type' => 'text', 'readonly' => true,'class' => 'input-large form-control')); ?> 

		<?php echo $this->BForm->input('status',array('value' =>  $this->data['RemessaStatus']['descricao'], 'label' => 'Status', 'placeholder' => 'Status', 'type' => 'text', 'readonly' => true,'class' => 'input-xxlarge form-control')); ?> 

		<?php echo $this->BForm->input('retorno',array('value' =>  $this->data['RemessaRetorno']['codigo']."-".$this->data['RemessaRetorno']['descricao'], 'label' => 'Status Retorno', 'placeholder' => 'Status', 'type' => 'text', 'readonly' => true,'class' => 'input-xxlarge form-control')); ?> 

		<?php echo $this->BForm->input('usuario_remessa',array('value' =>  $this->data['UsuarioRemessa']['nome'], 'label' => 'Usuário Remessa', 'placeholder' => 'Usuário Remessa', 'type' => 'text', 'readonly' => true,'class' => 'input-large form-control')); ?> 
		<?php echo $this->BForm->input('usuario_retorno',array('value' =>  $this->data['UsuarioRetorno']['nome'], 'label' => 'Usuário Retorno', 'placeholder' => 'Usuário Retorno', 'type' => 'text', 'readonly' => true,'class' => 'input-large form-control')); ?> 

		<?php echo $this->BForm->input('valor_juros',array('value' => $buonny->moeda((!empty($this->data['RemessaBancaria']['valor_juros'])) ? $this->data['RemessaBancaria']['valor_juros'] : '0.00'), 'label' => 'Valor Juros', 'placeholder' => 'Valor Juros', 'type' => 'text', 'readonly' => true,'class' => 'input-large form-control')); ?> 

		<?php echo $this->BForm->input('valor_pago',array('value' =>  $buonny->moeda($this->data['RemessaBancaria']['valor_pago']), 'label' => 'Valor Pago', 'placeholder' => 'Valor Pago', 'type' => 'text', 'readonly' => true,'class' => 'input-large form-control')); ?> 

		<?php echo $this->BForm->input('valor',array('value' => $buonny->moeda($this->data['RemessaBancaria']['valor']), 'label' => 'Valor Principal', 'placeholder' => 'Valor Principal', 'type' => 'text', 'readonly' => true,'class' => 'input-large form-control')); ?> 
		
	</div>

</div>

 <div class='row-fluid inline'>
 	<?php echo $this->BForm->hidden('codigo', array('value' =>  !empty($this->data['RemessaBancaria']['codigo'])? $this->data['RemessaBancaria']['codigo'] : '')); ?>
	<?php echo $this->BForm->hidden('nome_pagador', array('value' =>  !empty($this->data['RemessaBancaria']['nome_pagador'])? $this->data['RemessaBancaria']['nome_pagador'] : '')); ?>
	
	
	<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'RemessaBancaria'); ?>
	<?php echo $this->BForm->input('data_emissao', array('label' => false, 'placeholder' => 'Emissão', 'type' => 'text', 'class' => 'datepicker data date input-small form-control')); ?> 
</div>
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'remessa_bancaria', 'action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php 
echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){
        setup_mascaras(); setup_time(); setup_datepicker();
	});
"); ?>