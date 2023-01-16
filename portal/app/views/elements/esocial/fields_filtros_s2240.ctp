<?php echo $this->Buonny->input_grupo_economico($this, 'Esocial', $unidades, $setores, $cargos); ?>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo_grupo_exposicao', array('label' => false,'placeholder' => 'Código Grupo Exposição', 'class' => 'input-medium ', 'type' => 'text')); ?>
	<?php echo $this->BForm->input('status_xml', array('label' => false, 'class' => 'input-medium','options' => array('D' => 'Disponível para download', 'X' => 'XML com insconsistência'), 'empty' => 'Status')); ?>
</div>
 <div class="row-fluid inline">
		<span class="label label-info">Período por:</span>
		<div id='agrupamento'>
            <?php echo $this->BForm->input('tipo_periodo', array('type' => 'radio', 'options' => array('I' => 'Data Inicio Vigência', 'D' => 'Data de inicio do Grupo Exposição', 'F' => 'Funcionários Inativos'), 'default' => 6, 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall'))) ?>
        </div>
        <?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
		<?php echo $this->BForm->input('data_fim', array('label' => false, 'placeholder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
 </div>