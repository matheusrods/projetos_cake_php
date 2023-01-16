<?php echo $this->Buonny->flash(); ?>
<?php echo $this->Bajax->form('TVeicVeiculo', array('autocomplete' => 'off', 'url' => array('controller' => 'veiculos_ocorrencias', 'action' => 'tratar_veiculo_ocorrencia',$this->data['TOveiOcorrenciaVeiculo']['ovei_codigo']), 'callback'=>'close_dialog_ocorrencia_veiculo')) ?>
    <?php echo $this->BForm->input('TOveiOcorrenciaVeiculo.ovei_codigo', array('type' => 'hidden')) ?>
    <?php echo $this->BForm->input('TTermTerminal.term_codigo', array('type' => 'hidden')) ?>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('TTecnTecnologia.tecn_codigo', array('label' => 'Tecnologia', 'empty' => 'Tecnologia', 'options' => $tecnologias)) ?>
	<?php echo $this->BForm->input('TVtecVersaoTecnologia.vtec_codigo', array('label' => 'Versão da Tecnologia', 'empty' => 'Versão da Tecnologia', 'options' => $versoes)) ?>
</div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('TTermTerminal.term_numero_terminal', array('label' => 'Numero do Terminal', 'class' => 'input-medium','maxlength' => 15)) ?>
</div>
<div class='row-fluid inline'>
	<?php echo $html->link('Testar terminal', 'javascript:testar_terminal()', array('class' => 'btn')); ?>
	<?php echo $html->link('Sem Conta ADE', 'javascript:sem_conta_ade()', array('class' => 'btn sem_conta_ade', 'style' => 'display:none')); ?>
</div>
<?php if(!empty($this->data['TOveiOcorrenciaVeiculo']['ovei_usuario_tratamento'])): ?>
	<div class='row-fluid inline'>
		<strong>Placa:</strong> <?php echo COMUM::formatarPlaca($placa[0]['TVeicVeiculo']['veic_placa']); ?><BR>
		<strong>Último usuário tratamento:</strong> <?php echo $this->data['TOveiOcorrenciaVeiculo']['ovei_usuario_tratamento']; ?><BR>
		<strong>Data do tratamento:</strong> <?php echo $this->data['TOveiOcorrenciaVeiculo']['ovei_data_alteracao']; ?>
	</div>
<?php endif; ?>
<div class='form-actions'>
    <?php echo $this->BForm->submit('Finalizar ocorrência', array('div' => false, 'class' => 'btn btn-success')); ?>
    <?php echo $html->link('Deixar pendente', 'javascript:deixar_pendente_ocorrencia_veiculo('.$this->data['TOveiOcorrenciaVeiculo']['ovei_codigo'].')', array('class' => 'btn')); ?>
    <?php echo $html->link('Enviar RMA Veículo Sem Sinal', 'javascript:enviar_rma('.$this->data['TOveiOcorrenciaVeiculo']['ovei_codigo'].')', array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
	$(function(){
		$("#TTecnTecnologiaTecnCodigo").change(function(){
			verificaTecnologia();
			buscar_t_versao("#TTecnTecnologiaTecnCodigo", "#TVtecVersaoTecnologiaVtecCodigo");
		});

		function verificaTecnologia(){
			if($("#TTecnTecnologiaTecnCodigo").val() == 8){
				$(".sem_conta_ade").show();
			}else{
				$(".sem_conta_ade").hide();
			}
		}

		verificaTecnologia();
	});', false);
?>