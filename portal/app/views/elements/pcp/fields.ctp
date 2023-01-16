<div class="row-fluid inline">
	<?php echo $this->BForm->hidden('ipcp_codigo'); ?>
	<?php echo $this->BForm->input('ipcp_rota', array('class' => 'input-small just-number', 'label' => 'Rota')); ?>
	<?php echo $this->BForm->input('ipcp_loja', array('class' => 'input-small just-number', 'label' => 'Loja')); ?>
	<?php echo $this->BForm->input('ipcp_tipo_carga', array('class' => 'input-medium', 'label' => 'Tipo de Carga')); ?>
	<?php echo $this->BForm->input('ipcp_cd', array('class' => 'input-small', 'label' => 'CD')); ?>
	<?php echo $this->BForm->input('ipcp_tipo_veiculo', array('class' => 'input-medium', 'label' => 'Tipo de Veículo')); ?>
	<?php echo $this->BForm->input('ipcp_tipo_veiculo_geral', array('class' => 'input-medium', 'label' => 'Tipo de Veículo Geral')); ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('ipcp_paradas', array('class' => 'input-mini', 'label' => 'Paradas')); ?>
	<?php echo $this->BForm->input('ipcp_peso_bruto_total', array('class' => 'input-small', 'label' => 'Peso Bruto Total')); ?>
	<?php echo $this->BForm->input('ipcp_volume_bruto_total', array('class' => 'input-small', 'label' => 'Volume Bruto Total')); ?>
	<?php echo $this->BForm->input('ipcp_peso_utilizacao', array('class' => 'input-small', 'label' => 'Utilização do Peso')); ?>
	<?php echo $this->BForm->input('ipcp_volume_utilizacao', array('class' => 'input-small', 'label' => 'Utilização do volume')); ?>
	<?php echo $this->BForm->input('ipcp_peso', array('class' => 'input-small', 'label' => 'Peso')); ?>
	<?php echo $this->BForm->input('ipcp_volume', array('class' => 'input-small', 'label' => 'Volume')); ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('ipcp_bandeira', array('class' => 'input-mini', 'label' => 'Bandeira')); ?>
	<?php echo $this->BForm->input('ipcp_percurso_hora', array('class' => 'input-mini hora', 'label' => 'Percurso', 'placeholder' => 'Hora')); ?>
	<?php echo $this->BForm->input('ipcp_lead_time', array('class' => 'input-mini', 'label' => 'Lead Time')); ?>
	<?php echo $this->BForm->input('ipcp_hora_inicial_hora', array('class' => 'input-mini hora', 'label' => 'Hora Inicial', 'placeholder' => 'Hora')); ?>
	<?php echo $this->BForm->input('ipcp_hora_final_hora', array('class' => 'input-mini hora', 'label' => 'Hora Final', 'placeholder' => 'Hora')); ?>
</div>
<div class="row-fluid inline">
	<h6>Limite Expedição</h6>
	<?php echo $this->BForm->input('ipcp_limite_expedicao_inicial_data', array('class' => 'input-small data', 'label' => 'Inicial', 'placeholder' => 'Data')); ?>
	<?php echo $this->BForm->input('ipcp_limite_expedicao_inicial_hora', array('class' => 'input-mini hora', 'label' => '&nbsp;', 'placeholder' => 'Hora')); ?>
	<?php echo $this->BForm->input('ipcp_limite_expedicao_intermediario_data', array('class' => 'input-small data', 'label' => 'Intermediário', 'placeholder' => 'Data')); ?>
	<?php echo $this->BForm->input('ipcp_limite_expedicao_intermediario_hora', array('class' => 'input-mini hora', 'label' => '&nbsp;', 'placeholder' => 'Hora')); ?>
	<?php echo $this->BForm->input('ipcp_limite_expedicao_final_data', array('class' => 'input-small data', 'label' => 'Final', 'placeholder' => 'Data')); ?>
	<?php echo $this->BForm->input('ipcp_limite_expedicao_final_hora', array('class' => 'input-mini hora', 'label' => '&nbsp;', 'placeholder' => 'Hora')); ?>
</div>
<div class="row-fluid inline">
	<h6>Janela</h6>
	<?php echo $this->BForm->input('ipcp_janela_inicial_data', array('class' => 'input-small data', 'label' => 'Inicial', 'placeholder' => 'Data')); ?>
	<?php echo $this->BForm->input('ipcp_janela_inicial_hora', array('class' => 'input-mini hora', 'label' => '&nbsp;', 'placeholder' => 'Hora')); ?>
	<?php echo $this->BForm->input('ipcp_janela_intermediaria_data', array('class' => 'input-small data', 'label' => 'Intermediária', 'placeholder' => 'Data')); ?>
	<?php echo $this->BForm->input('ipcp_janela_intermediaria_hora', array('class' => 'input-mini hora', 'label' => '&nbsp;', 'placeholder' => 'Hora')); ?>
	<?php echo $this->BForm->input('ipcp_janela_final_data', array('class' => 'input-small data', 'label' => 'Final', 'placeholder' => 'Data')); ?>
	<?php echo $this->BForm->input('ipcp_janela_final_hora', array('class' => 'input-mini hora', 'label' => '&nbsp;', 'placeholder' => 'Hora')); ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('ipcp_data_remessa_data', array('class' => 'input-small data', 'label' => 'Data da Remessa', 'placeholder' => 'Data')); ?>
	<?php echo $this->BForm->input('ipcp_valor_total', array('class' => 'input-small', 'label' => 'Valor Declarado Total')); ?>
	<?php echo $this->BForm->input('ipcp_estado_destino', array('class' => 'input-mini', 'label' => 'Província de Destino')); ?>
</div>

<div class="form-actions">
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?= $html->link('Voltar', array('action' => 'listar_pcp'), array('class' => 'btn')); ?>
</div>


<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
		setup_mascaras();
		setup_time();
		setup_datepicker();
	});', false);
?>
