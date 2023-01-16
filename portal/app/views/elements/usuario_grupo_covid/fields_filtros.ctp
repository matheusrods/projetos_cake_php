<div class="row-fluid inline">
    <?php echo $this->BForm->input('matricula', array('label' => false, 'placeholder' => 'Matrícula', 'class' => 'input-medium ')); ?>    
    <?php echo $this->BForm->input('cpf', array('label' => false, 'placeholder' => 'CPF', 'class' => 'input-medium cpf')); ?>
    <?php echo $this->BForm->input('grupo', array('class' => 'input-small', 'label' => false, 'options' => $grupo_covid, 'empty' => 'Grupos', 'default' => ' '));?>
    <?php echo $this->BForm->input('passaporte', array('class' => 'input-medium', 'label' => false, 'options' => $passaporte, 'empty' => 'Passaporte', 'default' => ' '));?>
</div>

