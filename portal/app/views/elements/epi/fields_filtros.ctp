<div class="row-fluid inline">
<?php echo $this->BForm->input('nome', array('class' => 'input-xxlarge', 'placeholder' => 'Nome', 'label' => false)) ?>  
<?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => false, 'options' => array('0' => 'Inativos', '1' => 'Ativos'), 'value' => '1', 'empty' => 'Status', 'default' => ' ')); ?>
</div>        