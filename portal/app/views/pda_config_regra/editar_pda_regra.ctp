<?php 
echo $this->BForm->create('PdaConfigRegra', array('url' => array('controller' => 'pda_config_regra','action' => 'editar_pda_regra',$codigo))); 

	echo $this->BForm->hidden('codigo_cliente', array('value' => $this->data['PdaConfigRegra']['codigo_cliente'] ));
	echo $this->BForm->hidden('codigo', array('value' => $codigo));

	echo $this->element('pda_config_regra/fields', array('edit_mode' => true)); 

echo $this->BForm->end(); 
?>