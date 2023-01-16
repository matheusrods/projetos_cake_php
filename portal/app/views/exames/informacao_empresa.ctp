<div class='well'>
<?php echo $this->Form->create('Exame', array('url' => 'exportar_informacao_empresa')) ?>

<div class="row fluid  margin-top-15">
		<div class="span2">
			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'Exame', isset($codigo_cliente) ? $codigo_cliente : ''); ?>
		</div>
		<div class="span3 padding-top-30">
			<?php 
			echo $this->BForm->checkbox('todos_clientes', array('hiddenField' => false, 'class' => 'pull-left'));
			echo $this->BForm->label('todos_clientes', 'Selecionar todos clientes', array('class' => 'pull-left margin-left-10'));

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
			<label class="margin-top-10"><strong>Todos tipos de email:</strong></label>
			<?php echo $this->Form->input('todos_email', array('required' => false, 'legend' => false, 'type' => 'radio', 'options' => $todos_email)); ?>
		</div>
	</div>
	<div id="tipo_email" class="row-fluid" style="display:none;">
		<div class="span12">
			<label class="margin-top-10"><strong>Tipos de Email:</strong></label>
			<?php echo $this->Form->input('tipos_email', array('label' => false, 'multiple' => 'checkbox', 'options' => $tipo_contato)); ?>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<label class="margin-top-10"><strong>Exibição:</strong></label>
			<?php echo $this->Form->input('exibicao', array('required' => true, 'legend' => false, 'type' => 'radio', 'options' => $visualizacao)); ?>
		</div>
		<button id="gerarRelatorio" class="btn btn-primary margin-top-10" disabled>Gerar relatório</button>
		<?php echo $this->Html->link('Voltar', array('action' => 'exames_por_cliente'), array('class' => 'btn btn-default margin-top-10')); ?>
	</div>
	<?php echo $this->Form->end(); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	
	function disableGerarRelatorio(disable){
		document.getElementById(\'gerarRelatorio\').disabled = disable
	}

	document.getElementById("ExameTodosEmail1").onclick = function(){
		document.getElementById("tipo_email").style.display = "none";
	 }

	 document.getElementById("ExameTodosEmail2").onclick = function(){
		document.getElementById("tipo_email").style.display = "block";
	 }

	jQuery(document).ready(function($) {
		
		var multiselect = $("#multiselect").multiselectMulti();
		var multiselectTo = $("#multiselect_to");
		
		var multiselectLeftSelected = $("#multiselect_leftSelected");
		var multiselectRightSelected = $("#multiselect_rightSelected");
		var multiselectRightAll = $("#multiselect_rightAll");
		var multiselectLeftAll = $("#multiselect_leftAll");

		multiselectTo.on(\'keyup keypress blur change\', function(e) {
			disableGerarRelatorio($("#multiselect_to")[0].length === 0)
		});
		multiselectLeftSelected.on(\'keyup keypress blur change\', function(e) {
			disableGerarRelatorio($("#multiselect_to")[0].length === 0)
		});
		multiselectRightSelected.on(\'keyup keypress blur change\', function(e) {
			disableGerarRelatorio($("#multiselect_to")[0].length === 0)
		});
		multiselectRightAll.on(\'keyup keypress blur change\', function(e) {
			disableGerarRelatorio($("#multiselect_to")[0].length === 0)
		});
		multiselectLeftAll.on(\'keyup keypress blur change\', function(e) {
			disableGerarRelatorio($("#multiselect_to")[0].length === 0)
		});
		
		$("#ExameTodosClientes").click(function() {
			if($(this).is(\':checked\')) {
				$("#ExameCodigoCliente").val(\'\').attr(\'disabled\', true);
			} else {
				$("#ExameCodigoCliente").attr(\'disabled\', false);
			}
		});
	});
	'); ?>
