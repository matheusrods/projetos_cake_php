<div class='row-fluid inline'>
	
	<h4>Origem</h4>
	<table data-index="0">
		<tr>
			<?php $localizador_cliente2 = ($this->data['Recebsm']['codigo_cliente'] == $this->data['Recebsm']['embarcador'] ? '#RecebsmTransportador' : '#RecebsmEmbarcador') ?>
			<td>
				<?php echo $this->Buonny->input_referencia($this, '#RecebsmCodigoCliente', 'Recebsm', 'refe_codigo_origem', false, 'Local Origem', true, true, $localizador_cliente2) ?>
				<div class="control-group input text">
					<?php echo $this->BForm->input('Recebsm.dta_inc', array('label' => 'Data Inicio', 'class' => 'data input-small')) ?>
					<?php echo $this->BForm->input('Recebsm.hora_inc', array('label' => 'Hora', 'class' => 'hora input-mini')) ?>
					<?php echo $this->BForm->error('Recebsm.dta_hora_inc', null, array('style'=>"color:#b94a48; clear:both; margin-top: 60px; position: absolute;")) ?>
				</div>
				<div class="control-group input">
					<label>&nbsp;</label>
					<?php echo $this->BForm->input('Recebsm.monitorar_retorno', array('label' => 'Monitorar retorno', 'type' => 'checkbox')) ?>	
				</div>
			</td>

		</tr>
		<tr>
			<td>
				<?php echo $this->Buonny->input_referencia_endereco($this, 'Recebsm', 'refe_codigo_origem', false ); ?>
			</td>
		</tr>
		
		<tr id="MonitorarRetorno" style="display:<?php echo ($this->data['Recebsm']['monitorar_retorno'])?'block':'none' ?>" >
			<?php $localizador_cliente2 = ($this->data['Recebsm']['codigo_cliente'] == $this->data['Recebsm']['embarcador'] ? '#RecebsmTransportador' : '#RecebsmEmbarcador') ?>
			<td>
				<div class="control-group input text">
					<?php echo $this->BForm->input('Recebsm.dta_fim', array('label' => 'Data Fim', 'class' => 'data input-small')) ?>
					<?php echo $this->BForm->input('Recebsm.hora_fim', array('label' => 'Hora', 'class' => 'hora input-mini')) ?>
					<?php echo $this->BForm->error('Recebsm.dta_hora_fim', null, array('style'=>"color:#b94a48; clear:both; margin-top: 60px; position: absolute;")) ?>
				</div>
			</td>
		</tr>

	</table>
	
</div>
<?php echo $this->Javascript->codeBlock('	
    $(function(){
		$("#RecebsmMonitorarRetorno").change(function(){
			if($(this).is(":checked"))
				$("#MonitorarRetorno").show();
			else
				$("#MonitorarRetorno").hide();

		});
	});
');
?>