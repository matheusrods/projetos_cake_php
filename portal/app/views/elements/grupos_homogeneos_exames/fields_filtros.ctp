<div class="row-fluid inline">
<?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'label' => 'Código', 'type' => 'text')) ?>
<?php echo $this->BForm->hidden('codigo_cliente', array('value' => $codigo_cliente)) ?>
<?php echo $this->BForm->input('descricao', array('class' => 'input-xlarge', 'label' => 'Descrição')) ?>  
<?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => 'Status', 'options' => array('0' => 'Inativos', '1' => 'Ativos'), 'empty' => 'Selecione', 'default' => ' ')); ?>
</div>        