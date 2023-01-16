<div class="row-fluid inline">
    <?php echo $this->BForm->input('codigo', array('label' => false, 'class' => 'input-mini', 'placeholder' => 'Código', 'type'=>'text')); ?>
    <?php echo $this->BForm->input('nome', array('label' => false, 'class' => 'input-large', 'placeholder' => 'Nome', 'type'=>'text')); ?>
    <?php echo $this->BForm->input('rg', array('label' => false, 'placeholder' => 'RG', 'class' => 'input-medium ')); ?>    
    <?php echo $this->BForm->input('cpf', array('label' => false, 'placeholder' => 'CPF', 'class' => 'input-medium cpf')); ?>
    <?php echo $this->BForm->input('sexo', array('label' => false, 'options' => array('M' => 'Masculino', 'F' => 'Feminino'), 'empty' => 'Sexo', 'class' => 'input-small')); ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('codigo_setor', array('label' => false, 'options' => $setores, 'empty' => 'Setor', 'class' => 'input-medium')); ?>
    <?php echo $this->BForm->input('codigo_cargo', array('label' => false, 'options' => $cargos, 'empty' => 'Cargo', 'class' => 'input-medium')); ?>
    <?php echo $this->BForm->input('status', array('class' => 'input-small', 'label' => false, 'options' => array('0' => 'Inativos', '1' => 'Ativos'), 'empty' => 'Status', 'default' => ' ')); ?>
    <?php echo $this->BForm->hidden('codigo_cliente', array('value' => $this->data['Cliente']['codigo'])); ?>
</div>
