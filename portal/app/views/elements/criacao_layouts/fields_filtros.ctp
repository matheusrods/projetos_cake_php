<div class='row-fluid inline'>
	<?php
	if($this->Buonny->seUsuarioForMulticliente()) {
				echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Código cliente', null, 'MapLayout');
	}else{
			echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código cliente', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'MapLayout');
	}
	echo $this->BForm->input('nome', array('class' => 'input-medium', 'label' => 'Nome'));
	echo $this->BForm->input('dsname', array('class' => 'input-medium', 'label' => 'Dsname'));
	echo $this->BForm->input('apelido', array('class' => 'input-medium', 'label' => 'Apelido'));
	?>
</div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
</div>