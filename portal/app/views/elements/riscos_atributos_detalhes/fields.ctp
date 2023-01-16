<div class='well'>
	<div class='row-fluid inline'>	
		<?php echo $this->BForm->input('descricao', array('class' => 'input-xxlarge', 'placeholder' => 'Descrição (*)', 'label' => false)) ?>
	</div>

	</div>	
	<div class="clear"></div>
</div>

<div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras(); 
		setup_datepicker();
		
		$(document).on("click", ".dialog_riscos", function(e) {
	        e.preventDefault();
	        open_dialog(this, "Riscos", 960);
    	});
		
	});
	
'); ?>