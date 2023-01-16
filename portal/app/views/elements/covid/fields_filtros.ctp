<?php echo $this->Buonny->input_grupo_economico_cidade_estado($this, 'UsuarioGca', $unidades, $setores, $cargos,null, $cidade_unidade,$estado_unidade,$cidade_credenciado,$estado_credenciado,1); ?>

<div class="row-fluid inline">
	<span class="label label-info">Resultado:</span>
	<div id='agrupamento'>
        <?php echo $this->BForm->input('tipo_periodo', array('label' => false, 'options' => $tipos_periodo, 'legend' => false, 'class' => 'input-small')) ?>
    </div>
    <?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
	<?php echo $this->BForm->input('data_fim', array('label' => false, 'placeholder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
</div>
