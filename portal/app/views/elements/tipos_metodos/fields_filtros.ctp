<div class="row-fluid inline">
    <?php if(!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])): ?>
        <?php echo $this->BForm->input('name_cliente', array('class' => 'input-xlarge', 'value' => $nome_cliente, 'label' => 'Cliente', 'type' => 'text','readonly' => true)); ?>
        <?php echo $this->BForm->hidden('codigo_cliente', array('value' => $_SESSION['Auth']['Usuario']['codigo_cliente']));?>
    <?php endif; ?>               
               
    <?php

    if($this->Buonny->seUsuarioForMulticliente()) { 
        echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'MetodosTipo'); 
    }
    else{
        echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'MetodosTipo', isset($codigo_cliente) ? $codigo_cliente : '');
    }

    ?>
	<?php echo $this->BForm->input('descricao', array('class' => 'input-xlarge', 'placeholder' => 'Descrição', 'label' => 'Descrição')) ?>  
</div>