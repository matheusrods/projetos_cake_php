<div class="row-fluid inline">
<?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
<?php echo $this->BForm->input('codigo_risco', array('label' => false, 'class' => 'input', 'default' => '', 'empty' => 'Agente', 'options' => $riscos)); ?>
<?php echo $this->BForm->input('codigo_exame', array('label' => false, 'class' => 'input', 'default' => '', 'empty' => 'Exames', 'options' => $exames)); ?>
<?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => false, 'options' => array('0' => 'Inativos', '1' => 'Ativos'), 'empty' => 'Status', 'default' => ' ')); ?>
<?php echo $this->BForm->hidden('codigo_cliente', array('value' => $this->data['RiscoExame']['codigo_cliente'])); ?>
</div>        
