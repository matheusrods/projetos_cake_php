<div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this); ?>
</div>
 <div class="row-fluid inline">
            <?php echo $this->Buonny->input_unidades($this,"AnexoDigitalizacao",$unidades); ?>
</div>
<div class="row-fluid inline">
    <span class="label label-info">Agrupamento para o período:</span>
    <div id='agrupamento'>
        <?php echo $this->BForm->input('tipo_periodo', array('type' => 'radio', 'options' => $tipos_periodo, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall'))) ?>
    </div>
    <?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
    <?php echo $this->BForm->input('data_fim', array('label' => false, 'placeholder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>        
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo_tipo_digitalizacao', array('options' => $tipos_digitalizacao, 'empty' => 'Todos', 'class' => 'input-small', 'label' => 'Tipos Digitalizações')); ?>
</div>

