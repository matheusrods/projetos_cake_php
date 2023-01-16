<div class="row-fluid inline">
    <?php //echo $this->BForm->input('codigo', array('label' => false, 'class' => 'input-mini', 'placeholder' => 'Código', 'type'=>'text')); ?>
    <?php echo $this->BForm->input('nome', array('label' => false, 'class' => 'input-large', 'placeholder' => 'Nome', 'type'=>'text')); ?>
    <?php echo $this->BForm->input('cpf', array('label' => false, 'placeholder' => 'CPF', 'class' => 'input-medium cpf')); ?>
    <?php echo $this->BForm->input('codigo_matricula', array('label' => false, 'placeholder' => 'Código Matricula', 'class' => 'input-medium ')); ?>
    <?php echo $this->BForm->input('matricula', array('label' => false, 'placeholder' => 'Matricula', 'class' => 'input-medium ')); ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('codigo_unidade', array('label' => false, 'options' => $unidades, 'empty' => 'Unidade', 'class' => 'input-medium')); ?>
    <?php echo $this->BForm->input('codigo_setor', array('label' => false, 'options' => $setores, 'empty' => 'Setor', 'class' => 'input-medium')); ?>
    <?php echo $this->BForm->input('codigo_cargo', array('label' => false, 'options' => $cargos, 'empty' => 'Cargo', 'class' => 'input-medium')); ?>
    <?php echo $this->BForm->input('codigo_pagador', array('label' => false, 'options' => $pagador, 'empty' => 'Pagador', 'class' => 'input-medium')); ?>

    <?php echo $this->BForm->hidden('codigo_cliente', array('value' => $this->data['Cliente']['codigo'])); ?>

</div>
