<div class="row-fluid inline">
    <?php echo $this->Buonny->input_periodo($this, 'ModIvrPesquisa', 'startq', 'endq', true) ?>
    <?php echo $this->BForm->input('agtext',array('class' => 'input-mini', 'label' => 'Ramal')); ?>
	<?php echo $this->BForm->input('oani',  array('class' => 'input-small ', 'label' => 'Telefone Origem','type' => 'text')) ?>
    <?php echo $this->BForm->input('otrkid',array('class' => 'input-mini', 'label' => 'Tronco')); ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('status',array('class' => 'input-large', 'label' => 'Status', 'options' => $status, 'empty' => 'Status')); ?>
    <?php echo $this->BForm->input('score', array('class' => 'input-large', 'label' => 'Pontuação', 'options' =>$pontuacao, 'empty' => 'Pontuação')); ?>
    <?php echo $this->BForm->input('departamento', array('class' => 'input-large', 'label' => 'Departamento', 'options' =>$departamento, 'empty' => 'Todos')); ?>
</div>