 <div class="row-fluid inline">
    <?php echo $this->Buonny->input_codigo_cliente($this); ?>
</div>
 <div class="row-fluid inline">
    <?php echo $this->Buonny->input_posicao_exames($this,"Exame",$unidades, $setores, $exames); ?>
</div>

<div class="row-fluid inline">
    <span class="label label-info">Agrupar por:</span>
    <div id='agrupamento'>
        <?php echo $this->BForm->input('Exame.tipo_agrupamento', array('type' => 'radio', 'options' => $tipo_agrupamento, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall'))) ?>
    </div>
    <?php echo $this->BForm->input('Exame.data_inicio', array('label' => false, 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
    <?php echo $this->BForm->input('Exame.data_fim', array('label' => false, 'placeholder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
    
    <?php echo $this->BForm->input('Exame.tipo_exame', array('label' => false, 'class' => 'input-large','options' => $tipo_exame, 'empty' => 'Selecione o Tipo de Exame')); ?>
</div>

<style type="text/css">
	.error-message{
		color: red;
	}
</style>