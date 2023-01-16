<div class="row-fluid inline">
	<div style=<?php echo (empty($authUsuario['Usuario']['codigo_cliente']) ? "''": "'display:none'") ?> >
		<?php echo $this->Buonny->input_codigo_cliente($this,'codigo_cliente','Cliente',true,'TIpcpInformacaoPcp'); ?>
	</div>
	<?php echo $this->Buonny->input_periodo($this,'TIpcpInformacaoPcp','data_inicial', 'data_final', TRUE) ?>
	<?php echo $this->BForm->input('sm', array('class' => 'input-small', 'label' => 'SM')) ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->hidden('sm_atendida') ?>
	<?php echo $this->BForm->input('status', array('class' => 'input-small', 'label' => 'Status','options' => $status, 'empty' => 'Selecione o status')); ?>

	<?php echo $this->BForm->input('motivo', array('class' => 'input-small', 'label' => 'Motivo','options' => $motivo, 'empty' => 'Selecione o motivo')); ?>
	<?php echo $this->BForm->input('rota', array('class' => 'input-small', 'label' => 'Rota')) ?>
	<?php echo $this->BForm->input('tipo_carga', array('class' => 'input-medium', 'label' => 'Tipo da carga')) ?>
	<?php echo $this->BForm->input('status_viagem', array('label' => 'Status da Viagem', 'multiple' => 'multiple', 'class' => 'input-medium multiselect-status_viagem', 'options'=> $listaStatus, 'style' => 'display:none')); ?>
</div>
<div class="row-fluid inline" id="div-tipo-alvo">
	<?= $this->Buonny->input_alvos_bandeiras_regioes($this, array_merge($alvos_bandeiras_regioes, array('div' => '#div-tipo-alvo', 'force_model' => 'TIpcpInformacaoPcp', 'input_codigo_cliente' => 'codigo_cliente')))?>	
</div>
<?php echo $this->Javascript->codeBlock("
	$(document).ready(function(){
		 $('.multiselect-status_viagem').multiselect({
					maxHeight: 300,
					nonSelectedText: 'Status Viagem',
					numberDisplayed: 1,
					includeSelectAllOption: true
				});
	});
"); ?>