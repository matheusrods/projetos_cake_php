<div class="row-fluid inline">       
    <?php echo $this->BForm->input('codigo_unidade', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
    <?php echo $this->BForm->input('nome_fantasia', array('class' => 'input-xlarge', 'placeholder' => 'Nome fantasia', 'label' => false)) ?>
    <?php echo $this->BForm->input('razao_social', array('class' => 'input-xlarge', 'placeholder' => 'Razão social', 'label' => false)) ?>
    <?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium', 'placeholder' => 'CNPJ/CPF', 'label' => false)) ?>
    <?php echo $this->BForm->input('estado', array('class' => 'input-small', 'label' => false, 'options' => $estados, 'empty' => 'UF', 'default' => '')) ?>
    <?php echo $this->BForm->input('ativo', array('label' => false, 'class' => 'input-small', 'options' => array('Inativos', 'Ativos'), 'empty' => 'Todos')); ?>

    <?php echo $this->BForm->hidden('codigo_cliente', array('value' => $cliente_principal)); ?>
    <?php echo $this->BForm->hidden('referencia', array('value' => $referencia)); ?>
    <?php echo $this->BForm->hidden('referencia_modulo', array('value' => $referencia_modulo)); ?>
</div>