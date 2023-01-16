<div class="row-fluid inline">
    <?php echo $this->Buonny->input_codigo_cliente($this); ?>
</div>
 <div class="row-fluid inline">
    <?php echo $this->Buonny->input_unidades($this,"Cliente",$unidades); ?>
</div> <div class="row-fluid inline">
    <?php echo $this->BForm->input('login', array('label' => 'Login', 'placeholder' => false, 'class' => 'input-medium')); ?>
    <?php echo $this->BForm->input('nome_usuario', array('label' => 'Nome Usuário', 'placeholder' => false, 'class' => 'input-medium')); ?>
</div>