<? if (!isset($readonly)) $readonly = false; ?>
<div class='row-fluid inline'>  
    <?php echo $this->BForm->hidden('espa_codigo'); ?>    
    <?php echo $this->BForm->input('espa_descricao', array('label' => 'Evento', 'placeHolder' => false, 'class' => 'input-xxlarge', 'readonly'=>$readonly)); ?>
    <?php echo $this->BForm->input('espa_flag_telao', array('label' => 'Mostrar no Telão', 'options' => array( 'S' => 'SIM', 'N' => 'Não' ), 'empty' => 'Selecione um status', 'class' => 'input-large', 'disabled'=>$readonly)); ?>
</div>

<div class='row-fluid inline'>      
    <?php echo $this->BForm->input('espa_sla', array('label' => 'SLA', 'placeHolder' => false, 'class' => 'input-small just-number', 'readonly'=>$readonly)); ?>
    <?php echo $this->BForm->input('espa_alerta_nivel_2', array('label' => 'SLA Nível 2', 'placeHolder' => false, 'class' => 'input-small just-number', 'readonly'=>$readonly)); ?>
    <?php echo $this->BForm->input('espa_alerta_nivel_3', array('label' => 'SLA Nível 3', 'placeHolder' => false, 'class' => 'input-small just-number', 'readonly'=>$readonly)); ?>
</div>
<div class='row-fluid inline'>          
    <?php echo $this->BForm->input('espa_tipo_ocorrencia', array('label' => 'Prioridade da Ocorrência', 'options' => array( 1 => 'BAIXA', 2 => 'MÉDIA', 3 => 'ALTA', 4 => 'EVENTO SEM PRIORIDADE' ), 'empty' => 'Selecione a prioridade da ocorrência', 'class' => 'input-large', 'disabled'=>$readonly)); ?>
    <?php echo $this->BForm->input('espa_ocorrencia_autorizada', array('label' => 'Ocorrência Automatizada', 'options' => array( 'S' => 'SIM', 'N' => 'Não' ), 'empty' => 'Selecione', 'class' => 'input-large', 'disabled'=>$readonly)); ?>
    <?php echo $this->BForm->input('espa_tipo_evento', array('label' => 'Evento Conforme', 'options' => array( 'S' => 'SIM', 'N' => 'Não' ), 'empty' => 'Selecione', 'class' => 'input-large', 'disabled'=>$readonly)); ?>
    <?php echo $this->BForm->input('espa_prioridade_alerta', array('label' => 'Prioridade do Alerta', 'placeHolder' => false, 'class' => 'input-mini numeric just-number', 'readonly'=>$readonly)); ?>
</div>
<? if (!$readonly): ?>
  <div class="form-actions">
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
  </div>
<? endif; ?>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		setup_mascaras();
	});
');
?>