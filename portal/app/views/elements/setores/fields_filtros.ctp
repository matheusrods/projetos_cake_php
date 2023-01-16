<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo', array('class' => 'input-mini just-number', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
	<?php echo $this->BForm->input('descricao', array('class' => 'input-xxlarge', 'placeholder' => 'Descrição', 'label' => false)) ?>  
	<?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => false, 'options' => array('0' => 'Inativos', '1' => 'Ativos'), 'empty' => 'Status', 'default' => ' ')); ?>
	<?php echo $this->BForm->hidden('codigo_cliente', array('value' => $this->data['Cliente']['codigo'])); ?>
 </div>

 