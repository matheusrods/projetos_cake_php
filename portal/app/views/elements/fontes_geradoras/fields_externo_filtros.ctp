<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false)) ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo', array('class' => 'input-medium', 'label' => 'Código - Fonte Geradora', 'type' => 'text')) ?>
	<?php echo $this->BForm->input('descricao', array('class' => 'input-xxlarge', 'label' => 'Descrição')) ?>  
	<?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => 'Status', 'options' => array('0' => 'Inativos', '1' => 'Ativos'), 'empty' => 'Selecione', 'default' => ' ')); ?>
	<?php echo $this->BForm->input('FonteGeradoraExterno.codigo_externo', array('class' => 'input-small', 'label' => 'Código Externo')); ?>
</div>