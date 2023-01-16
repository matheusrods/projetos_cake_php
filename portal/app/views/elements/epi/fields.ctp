 <div class="well">
	<div class='row-fluid inline'>	
		<?php echo $this->BForm->hidden('codigo');?>
		<?php echo $this->BForm->input('nome', array('label' => 'Nome (*)', 'class' => 'input-xxlarge')); ?>
		
		<?php if(empty($this->passedArgs[0])): ?>
			<?php echo $this->BForm->hidden('ativo', array('value' => 1)); ?>
		<?php else: ?>
			<?php echo $this->BForm->input('ativo', array('label' => 'Status (*)', 'class' => 'input-medium', 'default' => '', 'empty' => 'Status', 'options' => array(1 => 'Ativo', 0 => 'Inativo'))); ?>
		<?php endif; ?>
	</div>
	<div class='row-fluid inline'>	
		<?php echo $this->BForm->input('especificacoes', array('type'=>'textarea', 'label' => 'Especificações', 'class' => 'input-xxlarge', 'style' => 'height: 80px')); ?>
		<?php echo $this->BForm->input('uso', array('type'=>'textarea','label' => 'Uso', 'class' => 'input-xxlarge', 'style' => 'height: 80px')); ?>
	</div>
	<div class='row-fluid inline'>	
		<?php echo $this->BForm->input('higienizacao', array('type'=>'textarea', 'label' => 'Higienização', 'class' => 'input-xxlarge', 'style' => 'height: 80px')); ?>
	 	<?php echo $this->BForm->input('conservacao', array('type'=>'textarea', 'label' => 'Conservação', 'class' => 'input-xxlarge', 'style' => 'height: 80px')); ?>
	</div>	
	<div class='row-fluid inline'>
		  <?php echo $this->BForm->input('atenuacao_qtd', array('type'=>'text', 'label' => 'Atenuação', 'class' => 'input-small just-number')); ?>
		 <?php echo $this->BForm->input('atenuacao_medida', array('options' => array('1' => 'Proporcional (%)',	'2' => 'Unidade de Medida do Risco'),  'empty' => 'Selecione', 'label' => 'Unidade', 'class' => 'input-xlarge')); ?>
	</div>	
	<div class='row-fluid inline'>
 		<?php echo $this->BForm->input('metodo_avaliacao_atenuacao', array('type'=>'textarea', 'label' => 'Metodo Avaliação de Atenuação', 'class' => 'input-xxlarge', 'style' => 'height: 80px')); ?>
		 <?php echo $this->BForm->input('fornecimento', array('type'=>'textarea', 'label' => 'Fornecimento', 'class' => 'input-xxlarge', 'style' => 'height: 80px')); ?>
	</div>	
	<div class='row-fluid inline'>	
		<?php echo $this->BForm->input('substituicao', array('label' => 'Substituição', 'class' => 'input-xlarge')); ?>
		<?php echo $this->BForm->input('reposicao_qtd', array('type'=>'text', 'label' => 'Reposição', 'class' => 'input-small just-number')); ?>
		<?php echo $this->BForm->input('reposicao_medida_prazo', array('options' => array('1' => 'Em Meses', '2' => 'Em Dias'), 'empty' => 'Selecione', 'label' => 'Periodo', 'class' => 'input-small')); ?>
		 <?php echo $this->BForm->input('custo', array('type'=>'text', 'label' => 'Custo (R$)', 'class' => 'input-small moeda')); ?>
		 <?php echo $this->BForm->input('fabricante', array('label' => 'Fabricante', 'class' => 'input-xlarge', 'style' => 'width: 490px;')); ?>
	</div>	
	<div class='row-fluid inline'>	 
		 <?php echo $this->BForm->input('data_fabricacao_crf', array('type'=>'text', 'label' => 'Data Fabricação CRF', 'class' => 'input-small  data')); ?>
		 <?php echo $this->BForm->input('descricao_crf', array('label' => 'Descrição CRF', 'class' => 'input-xlarge')); ?>
	 
		 <?php echo $this->BForm->input('data_importacao_cri', array('type'=>'text', 'label' => 'Data Importação CRI', 'class' => 'input-small  data')); ?>
		 <?php echo $this->BForm->input('descricao_cri', array('label' => 'Descrição CRI', 'class' => 'input-xlarge')); ?>
	</div>	
	<div class='row-fluid inline'>
		 <?php echo $this->BForm->input('numero_ca', array('type'=>'text', 'label' => 'Número CA', 'class' => 'input-medium  just-number')); ?>
		 <?php echo $this->BForm->input('data_validade_ca', array('type'=>'text', 'label' => 'Data Validade CA', 'class' => 'input-small  data')); ?>
		<?php echo $this->BForm->input('descricao_ca', array('label' => 'Descrição CA', 'class' => 'input-xxlarge')); ?>
	</div>
	<div class='row-fluid inline'>
		 <?php echo $this->BForm->input('tamanho_epi_funcionario', array('type'=>'checkbox', 'label' => false, 'div' => true, 'label' => 'Cadastra tamanho EPI por funcionário', 'class' => 'input-xlarge', 'checked' => 'checked')); ?>
	</div>
</div>

<?php if(isset($edit_mode) && $edit_mode == 1):?>
	<div id="epi_riscos" class="fieldset" style="display: block;">
		<?php echo $this->element('epi/riscos'); ?>
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
		setup_time();
		
		atualizaListaRiscos();
		$(document).on("click", ".dialog_riscos", function(e) {
	        e.preventDefault();
	        open_dialog(this, "Riscos", 700);
    	});
		
	});
	
	function atualizaListaRiscos(){
        var div = jQuery("#epi_riscos-lista");
        bloquearDiv(div);
        div.load(baseUrl + "epi_riscos/listagem/'.$this->data['Epi']['codigo'].'/" + Math.random());
    }
		
'); ?>