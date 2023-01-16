<div class='form-procurar well'>
	<?php echo $this->BForm->create('Sistema', array('type' => 'file', 'autocomplete' => 'off', 'url' => array('controller' => 'sistemas', 'action' => 'upload_planilha_ramais'))); ?>
		<div class="row-fluid inline">
			<?php 
			 
			 if ($modulo_selecionado==Modulo::RH) {
			  	echo $this->BForm->input('tipo_arquivo', array('label' => false, 'class' => 'input-medium', 'options' => array('1' => 'Ramais Líder Brasil', '2' => 'Ramais Prédio 102', '3' => 'Ramais Prédio 191'))); 
			  }
			  elseif ($usuario['Usuario']['codigo_uperfil'] == Uperfil::ADMIN){
			 	echo $this->BForm->input('tipo_arquivo', array('label' => false, 'class' => 'input-medium', 'options' => array('4' => 'Ramais TI'))); 
			 }
			  
			  ?>
		</div>
		<?php echo $this->BForm->input('arquivo', array('type'=>'file', 'label' => false)); ?>
		<?php echo $this->BForm->submit('Enviar', array('div' => false)); ?>
	<?php echo $this->BForm->end(); ?>
</div>