<div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this); ?>
</div>
 <div class="row-fluid inline">
            <?php echo $this->Buonny->input_unidades($this,"FichaClinica",$unidades); ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo', array('class' => 'input-small just-number', 'label' => 'Código Ficha', 'type' => 'text')); ?>
	<?php echo $this->BForm->input('codigo_pedido_exame', array('class' => 'input-small just-number', 'label' => 'Código Pedido', 'type'=> 'text')); ?>
	<?php echo $this->BForm->input('nome_funcionario', array('class' => 'input-large', 'label' => 'Funcionário', 'type' => 'text')); ?>
	<?php echo $this->BForm->input('nome_medico', array('class' => 'input-large', 'label' => 'Médico', 'type' => 'text')); ?>
</div>
<div class="row-fluid inline">
    <span class="label label-info">Agrupamento para o período:</span>
    <div id='agrupamento'>
        <?php echo $this->BForm->input('tipo_periodo', array('type' => 'radio', 'options' => $tipos_periodo, 'default' => 6, 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall'))) ?>
    </div>
    <?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
    <?php echo $this->BForm->input('data_fim', array('label' => false, 'placeholder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>        
</div>
<?php 
	echo $this->Javascript->codeBlock("	
		setup_mascaras();
	");
?>