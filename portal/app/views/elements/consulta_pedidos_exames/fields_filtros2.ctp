<?php echo $this->Buonny->input_grupo_economico_cidade_estado($this, 'PedidoExame', $unidades, $setores, $cargos,null, $cidade_unidade,$estado_unidade,$cidade_credenciado,$estado_credenciado); ?>

<div class="row-fluid inline">
	<span class="label label-info">Período por:</span>
	<div id='agrupamento'>
        <?php echo $this->BForm->input('tipo_periodo', array('type' => 'radio', 'options' => $tipos_periodo, 'default' => 6, 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall'))) ?>
    </div>
    <?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
	<?php echo $this->BForm->input('data_fim', array('label' => false, 'placeholder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
</div>
