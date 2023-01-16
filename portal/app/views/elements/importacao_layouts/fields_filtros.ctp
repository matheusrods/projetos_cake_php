<div class='row-fluid inline'>
	<?php
		$statuses = array(
			1 => "Arquivo transferido",
			2 => "Arquivo transferencia falhou",
			3 => "Arquivo pronto",
			4 => "Importacao Estrutura incluindo",
			5 => "Importacao Estrutura pronto",
			6 => "Importacao Estrutura falhou",
			8 => "Importacao estrutura processado",
		);
		if($this->Buonny->seUsuarioForMulticliente()) {
				echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Código cliente', null, 'IntUploadCliente');
		}else{
				echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código cliente', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'IntUploadCliente');
		}
		echo $this->BForm->input('nome', array('class' => 'input-medium', 'label' => 'Nome'));
		echo $this->BForm->input('codigo_status_transferencia', array('label' => "Status", 'class' => 'input-medium','empty' => "Selecione um status",  'options' => $statuses));
	?>
</div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
</div>