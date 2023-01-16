<div class='well'>
<?php echo $this->Form->create('Fornecedor', array('url' => 'exportar_informacao_credenciado')) ?>

<div class="row fluid  margin-top-15">
		<div class="span2">
			<?php echo $this->Buonny->input_codigo_fornecedor2($this, 'codigo_fornecedor', 'Código credenciado(*)', 'Código credenciado(*)', 'Fornecedor', isset($codigo_fornecedor) ? $codigo_fornecedor : ''); ?>
		</div>
		<div class="span3 padding-top-30">
			<?php 
			echo $this->BForm->checkbox('todos_fornecedores', array('hiddenField' => false, 'class' => 'pull-left'));
			echo $this->BForm->label('todos_fornecedores', 'Selecionar todos fornecedores', array('class' => 'pull-left margin-left-10'));

			 ?>
		</div>
	</div>

	<div class="row fluid">
		<div class="span12">
			<label><strong>Selecione os campos que deverão ser exibidos no relatório: </strong></label>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span5">
			<?php echo $this->Form->input('from', array('label' => false, 'id' => 'multiselect', 'options' => $campos, 'class' => 'form-control', 'multiple' => true, 'size' => '8', 'style' => 'width: 100%')); ?>
		</div>

		<div class="span2">
			<button type="button" id="multiselect_rightAll" class="btn btn-block"><i class="icon-forward"></i></button>
			<button type="button" id="multiselect_rightSelected" class="btn btn-block"><i class="icon-chevron-right"></i></button>
			<button type="button" id="multiselect_leftSelected" class="btn btn-block"><i class="icon-chevron-left"></i></button>
			<button type="button" id="multiselect_leftAll" class="btn btn-block"><i class="icon-backward"></i></button>
		</div>

		<div class="span5">
			<?php echo $this->Form->input('to', array('label' => false, 'id' => 'multiselect_to', 'class' => 'form-control', 'options' => array(), 'multiple' => true, 'size' => '8', 'style' => 'width: 100%')); ?>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<label class="margin-top-10"><strong>Exibição:</strong></label>
			<?php echo $this->Form->input('exibicao', array('required' => true, 'legend' => false, 'type' => 'radio', 'options' => $visualizacao)); ?>
		</div>
		<button class="btn btn-primary margin-top-10">Gerar relatório</button>		
	</div>
	<?php echo $this->Form->end(); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function($) {
		$("#multiselect").multiselectMulti();
		$("#FornecedorTodosClientes").click(function() {
			if($(this).is(\':checked\')) {
				$("#FornecedorCodigoFornecedor").val(\'\').attr(\'disabled\', true);
			} else {
				$("#FornecedorCodigoFornecedor").attr(\'disabled\', false);
			}
		});
	});
	'); ?>
