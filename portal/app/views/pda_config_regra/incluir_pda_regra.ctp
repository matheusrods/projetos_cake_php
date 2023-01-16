<?php 
echo $this->BForm->create('PdaConfigRegra', array('url' => array('controller' => 'pda_config_regra','action' => 'incluir_pda_regra', $codigo_cliente))); 

	echo $this->BForm->hidden('codigo_cliente', array('value' => $codigo_cliente));
	
	echo $this->element('pda_config_regra/fields', array('edit_mode' => false)); 

echo $this->BForm->end(); 
?>
