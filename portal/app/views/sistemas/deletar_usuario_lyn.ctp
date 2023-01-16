<div class='well'>
	<?php
	if(!empty($msg)){
		echo "<h1>{$msg}</h1>";
	}
	?>
    <div class="row-fluid inline">
        <?php echo $this->BForm->create('Sistema', array('url' => array('controller' => 'sistemas', 'action' => 'deletar_usuario_lyn'))) ?>
            <?php echo $this->BForm->input('cpf', array('class' => 'input-medium', 'placeholder' => 'CPF', 'label' => false, 'type' => 'text')) ?>
            <?php echo $this->BForm->submit('Zerar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $this->BForm->end() ?>
    </div>  
</div>