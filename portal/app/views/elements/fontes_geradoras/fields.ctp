<div class='well'>
	<div class='row-fluid inline'>	
		<?php echo $this->BForm->hidden('FonteGeradora.codigo');?>
		<?php echo $this->BForm->input('FonteGeradora.nome', array('class' => 'input-xxlarge',  'label' => 'Nome (*)')); ?>

		<?php echo $this->BForm->input('FonteGeradora.local', array('class' => 'input', 'label' => 'Local'));?>

	<?php if(empty($this->passedArgs[0])): ?>
		<?php echo $this->BForm->hidden('FonteGeradora.ativo', array('value' => 1)); ?>
	<?php else: ?>
		<?php echo $this->BForm->input('FonteGeradora.ativo', array('label' => 'Status (*)', 'class' => 'input-medium', 'default' => '', 'empty' => 'Status', 'options' => array(1 => 'Ativo', 0 => 'Inativo'))); ?>
	<?php endif; ?>

	</div>
	<div class='row-fluid inline'>	
		<?php echo $this->BForm->input('FonteGeradora.observacao', array('type' => 'textarea', 'class' => 'input-xxlarge', 'label' => 'Observação', 'style' => 'height: 80px; width: 900px;')) ?>
	</div>				
</div>	
<?php if(isset($edit_mode) && $edit_mode == 1):?>
	<div id="fontes_geradoras_riscos" class="fieldset" style="display: block;">
		<?php echo $this->element('fontes_geradoras/riscos'); ?>
	</div>
<?php endif;?>

<div class="form-actions">
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock("    
	$(document).ready(function(){
		setup_mascaras();
		atualizaListaRiscos();

		$(document).on('click', '.dialog_riscos', function(e) {
	        e.preventDefault();
	        open_dialog(this, 'Riscos', 880);
    	});
	});

	function atualizaListaRiscos(){
        var div = jQuery('#fontes_geradoras_riscos-lista');
        bloquearDiv(div);
        div.load(baseUrl + 'fontes_geradoras_riscos/listagem/".$this->data['FonteGeradora']['codigo']."/' + Math.random());
    }
	
"); ?>