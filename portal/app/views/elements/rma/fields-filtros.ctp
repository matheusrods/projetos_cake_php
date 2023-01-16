<div class="row-fluid inline">
	<div id = 'DivPeriodoInicioFim'>
    	<?php echo $this->Buonny->input_periodo($this, 'TOrmaOcorrenciaRma','data_inicial','data_final',false,60); ?>
    	<!--<?php echo $this->BForm->input('TOrmaOcorrenciaRma.tipo_data', array('type' => 'radio', 'options' => array(1 => 'Dt. RMA',2 => 'Dt. Viagem'), 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-small'))) ?>-->
	    <?php echo $this->Buonny->input_embarcador_transportador($this, $embarcadores, $transportadores, 'codigo_cliente', 'Cliente', false, 'TOrmaOcorrenciaRma'); ?>
    </div>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('grma_codigo', array('options' => $geradores_ocorrencia, 'class' => 'input-small', 'label' => false, 'empty' => 'Gerador')); ?>
	<?php echo $this->BForm->input('trma_codigo', array('options' => $tipos_ocorrencia, 'class' => 'input-xlarge', 'label' => false, 'empty' => 'Tipo')); ?>
	<?php echo $this->BForm->input('orma_flg_auto', array('options' => $automatico, 'class' => 'input-small', 'label' => false, 'empty' => 'Automatico')); ?>
	<?php echo $this->BForm->input('pfis_cpf', array('class' => 'input-small', 'label' => false, 'placeholder' => 'CPF Motorista')); ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('viag_codigo_sm', array('class' => 'input-small', 'label' => false, 'placeholder' => 'SM', 'maxlength' => null)); ?>
	<div id="div-tipo-alvo">
		<?=$this->Buonny->input_alvos_bandeiras_regioes($this, array_merge($alvos, array('div' => '#div-tipo-alvo', 'force_model' => 'TOrmaOcorrenciaRma', 'input_codigo_cliente' => 'codigo_cliente', 'exibe_label' => false, 'exibe_classes' => false, 'exibe_veiculo' => false, 'exibe_transportador' => false, 'exibe_bandeira' => false, 'exibe_regiao' => false, 'exibe_loja'=> false)));?>
	</div>
    <?php echo $this->BForm->input("tecn_codigo", array('label' => false, 'empty' => 'Tecnologia','class' => 'input-medium', 'options' => $tecnologias)) ?>
	<?php echo $this->BForm->input('veic_placa', array('class' => 'input-small placa-veiculo', 'label' => false, 'placeholder' => 'Placa', 'readonly' => $this->layout == 'new_window')) ?>
	<?php echo $this->BForm->input('trma_prioridade', array(
	'options' => array(
		'I' => 'Informativo',
		'M' => 'Médio',
		'G' => 'Grave'
	), 'class' => 'input-medium', 'label' => false, 'empty' => 'Grau de Risco')); ?>
</div>
<div class="row-fluid inline">
	<span class="label label-info">Status das Viagens</span>
	 <span class='pull-right'>
        <?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("status_viagem")')) ?>
        <?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("status_viagem")')) ?>
    </span>
    <div id='status_viagem'>
		<?php echo $this->BForm->input('codigo_status_viagem', array('label'=>false, 'options'=>$status_viagens, 'multiple'=>'checkbox', 'class' => 'checkbox inline input-xlarge')); ?>
	</div>
</div>