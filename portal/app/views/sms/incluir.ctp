<?php echo $this->BForm->create('SmsOutbox', array('autocomplete' => 'off', 'url' => array('controller' => 'sms', 'action' => 'incluir'))); ?>	
	<div class="well">
		<div class='row-fluid inline'>
		    <?php echo $this->BForm->input('fone_de', array('class' => 'input',  'label' => 'MODEM','options' => $modem, 'empty' => 'QUALQUER', 'default' => '')) ?>
		    <?php echo $this->BForm->input('fone_para', array('class' => 'input-medium telefone',  'label' => 'Celular')) ?>  
		    <?php echo $this->BForm->input('liberar_envio_em', array('class' => 'input-small data', 'label' => 'Liberar Envio', 'type' => 'text')) ?>  
		</div>	
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('mensagem', array('class' => 'input-xxlarge', 'label' => 'Mensagem', 'type' => 'textarea', 'maxlength' => '176')) ?>
		</div>	
	</div>
		<div class='form-actions'>
    	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    	<?= $html->link('Voltar', array('controller'=>'sms','action' => 'index'), array('class' => 'btn')); ?>
	</div>
<?php echo $this->BForm->end(); ?>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		setup_datepicker();       
		setup_mascaras();
    });', false);
?>