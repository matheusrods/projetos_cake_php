<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $this->Session->delete('Message.flash');
        //echo $javascript->codeBlock("atualiza_informacoes_prestadores(".$pagina.");close_dialog();");
        echo $javascript->codeBlock("close_dialog(); inicioViagem();");
        exit;
    }else{
        echo $this->Buonny->flash();
        $this->Session->delete('Message.flash');
    }
?>

<?php echo $this->Bajax->form('Recebsm', array('autocomplete' => 'off', 'url' => array('controller' => 'Viagens', 'action' => 'copia_viagem', $codigo_cliente, $viag_codigo_sm))) ?>
	<h4>Origem</h4>
	<div class="well">
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('Recebsm.refe_codigo_origem_visual', array('label' => 'Local Origem', 'class' => 'input-xlarge','readonly'=>true)) ?>
			<div class="control-group input text">
				<?php echo $this->BForm->input('Recebsm.dta_inc', array('label' => 'Data Inicio', 'class' => 'data input-small')) ?>
				<?php echo $this->BForm->input('Recebsm.hora_inc', array('label' => 'Hora', 'class' => 'hora input-mini')) ?>
				<?php echo $this->BForm->error('Recebsm.dta_hora_inc', null, array('style'=>"color:#b94a48; clear:both; margin-top: 60px; position: absolute;")) ?>
			</div>
			<div class="control-group input">
				<label>&nbsp;</label>
				<?php echo $this->BForm->input('Recebsm.monitorar_retorno', array('label' => 'Monitorar retorno', 'type' => 'checkbox', 'disabled'=>true)) ?>	
			</div>
			<div class="control-group input text" id="MonitorarRetorno" style="display: <?=(!empty($this->data['Recebsm']['monitorar_retorno']) ? "" : "none")?>">
				<?php echo $this->BForm->input('Recebsm.dta_fim', array('label' => 'Data Fim', 'class' => 'data input-small')) ?>
				<?php echo $this->BForm->input('Recebsm.hora_fim', array('label' => 'Hora', 'class' => 'hora input-mini')) ?>
				<?php echo $this->BForm->error('Recebsm.dta_hora_fim', null, array('style'=>"color:#b94a48; clear:both; margin-top: 60px; position: absolute;")) ?>
			</div>
		</div>
	</div>
	<h4>Itinerário</h4>
	<div class="well">
		<?php if (isset($this->data['RecebsmAlvoDestino'])): ?>
			<?php foreach ($this->data['RecebsmAlvoDestino'] as $key => $destino): ?>
				<div class="row-fluid inline">
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.refe_codigo_visual", array('label' => 'Itinerario Alvo', 'class' => 'input-xlarge','readonly'=>true)) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.dataFinal", array('label' => 'Previsão Chegada', 'class' => 'data input-small')) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.horaFinal", array('label' => 'Hora', 'class' => 'hora input-mini')) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.janela_inicio", array('label' => 'Janela Inicio', 'class' => 'hora input-mini')) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.janela_fim", array('label' => 'Janela Fim', 'class' => 'hora input-mini')) ?>				    
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.tipo_parada", array('label' => 'Tipo Itinerario', 'class' => 'input-medium', 'options' => $tipo_parada, 'empty' => 'Selecione um Tipo', 'disabled'=>true)) ?>
				</div>
				
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<div class="row-fluid inline">
		<?= $this->BForm->submit('Gerar SM', array('div' => false, 'class' => 'btn btn-success', 'id' => 'GerarSm', 'name' => 'data[Acao][tipo]')); ?>
	</div>


<?php echo $this->BForm->end(); ?>

<?php echo $this->Buonny->link_js('solicitacoes_monitoramento'); ?>
<?php
	echo $this->Javascript->codeBlock("
		$(document).ready(function(){
			setup_mascaras();
			setup_datepicker();
			setup_date();
			setup_time();
			$('#RecebsmMonitorarRetorno').change(function(){
				if($(this).is(':checked'))
					$('#MonitorarRetorno').show();
				else
					$('#MonitorarRetorno').hide();

			});

		});
	");
?>
