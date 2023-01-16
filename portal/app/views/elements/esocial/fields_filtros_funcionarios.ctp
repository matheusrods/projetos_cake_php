<?php echo $this->Buonny->input_grupo_economico($this, 'Esocial', $unidades, $setores, $cargos); ?>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('matricula', array('label' => false,'placeholder' => 'Matrícula', 'class' => 'input-medium ', 'type' => 'text')); ?>
	<?php echo $this->BForm->input('codigo_pedido_exame', array('label' => false,'placeholder' => 'Número Pedido Exame', 'class' => 'input-medium ', 'type' => 'text')); ?>
</div>
 <div class="row-fluid inline">
		<span class="label label-info">Período por:</span>
		<div id='agrupamento'>
            <?php echo $this->BForm->input('tipo_periodo', array('type' => 'radio', 'options' => array('I' => 'Data da Baixa', 'C' => 'Data de Conclusão do Exame'), 'default' => 6, 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall'))) ?>
        </div>
        <?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
		<?php echo $this->BForm->input('data_fim', array('label' => false, 'placeholder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
		<?php echo $this->BForm->input('status_xml', array('label' => false, 'class' => 'input-medium','options' => array('D' => 'Disponível para download', 'X' => 'XML com insconsistência'), 'empty' => 'Status')); ?>
 </div>