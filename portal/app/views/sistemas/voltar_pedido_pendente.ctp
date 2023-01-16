<div class='well'>
	<?php
	if(!empty($msg)){
		echo "<h1>{$msg}</h1>";
	}
	?>
    <div class="row-fluid inline">
        <?php echo $this->BForm->create('Sistema', array('url' => array('controller' => 'sistemas', 'action' => 'voltar_pedido_pendente'))) ?>
            <?php echo $this->BForm->input('codigos', array('class' => 'input-medium','style' => 'width:500px', 'placeholder' => 'Codigo do Pedido separado por virgula (,)', 'label' => false, 'type' => 'text')) ?>
            <?php echo $this->BForm->submit('Executar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $this->BForm->end() ?>
    </div>  
</div>