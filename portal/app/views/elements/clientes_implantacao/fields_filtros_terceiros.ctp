<div class="row-fluid inline">
<?php

	if($this->Buonny->seUsuarioForMulticliente()) { 
		echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'ClienteImplantacao');

	} else if(!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])) {
		echo $this->BForm->input('name_cliente', array('class' => 'input-xlarge', 'value' => $nome_cliente, 'label' => 'Cliente', 'type' => 'text','readonly' => true)); 
		echo $this->BForm->hidden('codigo_cliente', array('value' => $_SESSION['Auth']['Usuario']['codigo_cliente']));
	} else{
		echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'ClienteImplantacao', isset($codigo_cliente) ? $codigo_cliente : '');
	}
?>
</div>    
