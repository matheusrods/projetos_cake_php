<?php echo $this->Buonny->input_grupo_economico($this, 'Esocial', $unidades, $setores, $cargos); ?>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo_atestado', array('label' => false,'placeholder' => 'Código Atestado', 'class' => 'input-medium ', 'type' => 'text')); ?>
	<div class="span1" style="padding-top: 11px;margin-left: 4px;">
		<span class="label label-success">Periodo:</span>
	</div>
	<div class="span2" style="margin-left: 0%;padding-top: 1px;" >
		<?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
	</div>
	<div class="span1" style="padding-top: 17px;margin-left: -4%">
		até
	</div>
	<div class="span2" style="margin-left: -3%;padding-top: 1px;" >
    	<?php echo $this->BForm->input('data_fim', array('label' => false, 'placeholder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
    </div>
     <?php echo $this->BForm->input('status_xml', array('label' => false, 'style' => 'margin-left: -50px;', 'class' => 'input-xlarge','options' => array('D' => 'Disponível para download', 'X' => 'XML com insconsistência'), 'empty' => 'Status')); ?>
</div>