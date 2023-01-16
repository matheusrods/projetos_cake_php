<?php echo $this->Html->scriptBlock('var baseUrl = "'.$this->webroot.'";'); ?>
<?php if (isset($tituloPago) && $tituloPago== TRUE): ?>
	<div class="row-fluid inline">	
		<h5><i>Prezado Cliente,</i></h5>
		<p style="font-size:12px;text-align:justify;">
			Boleto já está pago. 
		</p>
	</div>
<?php else: ?>
	<?php echo $this->BForm->create('Cliente', array('url' => array('controller' => 'clientes', 'action' => 'mensagem_vencimento'))); ?>
	<div id="dialog-confirm" title="Data de Pagamento" >
		<div class="row-fluid inline">	
			<h5><i>Prezado Cliente,</i></h5>
			<p style="font-size:12px;text-align:justify;">
				Boleto já vencido informe nova data para pagamento 
			</p>

			<?php echo $this->BForm->input('data_vencimento', array('label' => 'Data de Pagamento', 'type' => 'text', 'class' => 'data input-small'));
			?>
		</div>	
		<?php if(isset($helpblockerrormessage)): ?>
		 <div class="help-block error-message" style="color: #B94A48;font-family:Helvetica Neue,Helvetica,Arial,sans-serif;">(11)3443-2517. <br/>(11)3443-2587. <br/>(11)3443-2601.
		 </div>
		<?php endif?>
		<div class='actionbar-lefth'>
			<?php echo $this->BForm->submit('Ok', array('div' => false, 'class' => 'btn btn-success')); ?>
		<div>
		<div class="row-fluid inline">
			<p style="font-size:12px;text-align:justify;">	
			<h5>Atualização de Boleto:</h5>
				Esta função serve exclusivamente para atualização de boleto vencido.
				<br />
				Não se trata de uma prorrogação do pagamento.
				<br />
				Estes títulos continuam sujeitos as ações de cobrança até sua liquidação.
				<br />
				Em caso de dúvidas:
				<br />
				Entrar em Contato com o Departamento Financeiro através dos telefones:
				<br />
				(11) 3443-2517.
				<br />
				(11) 3443-2587.
				<br />
				(11) 3443-2601.
			</p>
				
			
		</div>	
		<?php echo $this->BForm->end() ?>
	</div>
	<?php echo $this->Javascript->codeBlock('$(document).ready(function() { setup_datepicker(); });', false); ?>
<?php endif ?>