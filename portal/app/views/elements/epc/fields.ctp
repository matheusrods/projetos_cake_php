<div class='well'>
	<div class='row-fluid inline'>	
		<?php echo $this->BForm->input('nome', array('class' => 'input-xxlarge', 'placeholder' => 'Nome (*)', 'label' => false)) ?>
	</div>
	<?php if(isset($this->passedArgs[0])) : ?>
		<div class='row-fluid inline'>	
			<?php echo $this->BForm->input('ativo', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'Ativo', 'class' => 'input-xlarge')); ?>
		</div>	
	<?php endif; ?>
		
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('instalacao', array('type' => 'text', 'class' => 'input-large data', 'placeholder' => 'Data Instalação', 'label' => false)) ?>
	</div>
	<div class='row-fluid inline'>	
		<?php echo $this->BForm->input('revisao', array('type' => 'text', 'class' => 'input-large data', 'placeholder' => 'Data Revisão', 'label' => false)) ?>
	</div>
	<div class='row-fluid inline'>	
		<?php echo $this->BForm->input('validade_meses', array('class' => 'input-large just-number', 'placeholder' => 'Validade (Meses)', 'label' => false)) ?>
	</div>
	
	<div class='row-fluid inline'>	
		<?php echo $this->BForm->input('atenuacao_qtd', array('class' => 'input-small', 'placeholder' => 'Atenuação', 'label' => false)) ?>
		<?php echo $this->BForm->input('atenuacao_medida', array('class' => 'input-large', 'label' => false, 'options' => array('1' => 'Proporcional (%)', '2' => 'Unidade de medida de risco'))); ?>
	</div>	
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('metodo_avaliacao_atenuacao', array('type' => 'textarea', 'class' => 'input-xxlarge', 'placeholder' => 'Método de Avaliação de Atenuação', 'label' => false)) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('custo', array('class' => 'input-small moeda', 'placeholder' => 'Custo', 'label' => false)) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('quantidade', array('class' => 'input-xxlarge just-number', 'placeholder' => 'Quantidade', 'label' => false)) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('observacao', array('type' => 'textarea', 'class' => 'input-xxlarge', 'placeholder' => 'Observação', 'label' => false)) ?>
	</div>	
	<div class="clear"></div>
</div>

<?php if(isset($edit_mode) && $edit_mode == 1):?>
	<div id="epc_riscos" class="fieldset" style="display: block;">
		<?php echo $this->element('epc/riscos'); ?>
	</div>
<?php endif;?>

<div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras(); 
		setup_datepicker();
		
		atualizaListaRiscos();
		$(document).on("click", ".dialog_riscos", function(e) {
	        e.preventDefault();
	        open_dialog(this, "Riscos", 960);
    	});
		
	});

	function atualizaListaRiscos(){
        var div = jQuery("#epc_riscos-lista");
        bloquearDiv(div);
        div.load(baseUrl + "epc_riscos/listagem/'.$this->data['Epc']['codigo'].'/" + Math.random());
    }		
	
'); ?>