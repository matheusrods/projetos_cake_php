<?php echo $this->Buonny->input_grupo_economico($this, 'FichaPsicossocial', $unidades, $setores, $cargos); ?>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo_pedido_exame', array('label' => false, 'placeholder' => 'Pedido do Exame', 'div' => array('class' => 'input-xlarge control-group input'))); ?>
</div>
<div class="row-fluid inline">
    <span class="label label-info">Período por:</span>
     <div id='agrupamento'>
        <?php echo $this->BForm->input('FichaPsicossocial.periodo_ficha', array('type' => 'radio', 'options' => array(
            'E' => 'Inclusão da Ficha'), 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall'))) ?>
    </div>      
    <?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
    <?php echo $this->BForm->input('data_fim', array('label' => false, 'placeholder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>        
</div>