<?php echo $this->Buonny->input_grupo_economico($this, 'Atestado', $unidades, $setores, $cargos); ?>
<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_medico($this, 'codigo_medico', 'Médico', false, 'Atestado'); ?>
	<?php echo $this->BForm->input('descricao_cid', array('label' => false, 'placeholder' => 'Descrição CID', 'class' => 'input-xlarge')); ?>
</div>
<div class="row-fluid inline">
	<span class="label label-info">Período por:</span>
	<div id='agrupamento'>
        <?php echo $this->BForm->input('tipo_periodo', array('type' => 'radio', 'options' => $tipos_periodo, 'default' => 5, 'legend' => false, 'label' => array('class' => 'radio inline input-small'))) ?>
    </div>
    <?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
	<?php echo $this->BForm->input('data_fim', array('label' => false, 'placeholder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
</div>