<div class='well'>
	<?php
	if(!empty($resultado)){
		echo "<h1>{$resultado}</h1>";
	}
	?>
    <div class="row-fluid inline">
        <?php echo $this->BForm->create('Sistema', array('url' => array('controller' => 'sistemas', 'action' => 'azure_push'))) ?>
            <?php echo $this->BForm->input('regIds', array('class' => 'input-medium', 'placeholder' => 'id celular', 'label' => false, 'type' => 'text')) ?>
            <?php echo $this->BForm->input('titulo', array('class' => 'input-medium', 'placeholder' => 'titulo', 'label' => false, 'type' => 'text', 'value'=> 'Teste push')) ?>
            <?php echo $this->BForm->input('mensagem', array('class' => 'input-medium', 'placeholder' => 'msg', 'label' => false, 'type' => 'text', 'value'=> 'mensagem push')) ?>
            <?php echo $this->BForm->input('platform', array('class' => 'input-medium', 'placeholder' => 'plataforma', 'label' => false, 'type' => 'text', 'value'=> 'android')) ?>
            <?php echo $this->BForm->submit('enviar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $this->BForm->end() ?>
    </div>  
</div>