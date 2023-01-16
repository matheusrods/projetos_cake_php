<div class="row-fluid inline">
    <?php if ($this->name == 'ReguladoresEnderecos'): ?>
        <?php $options = array('class' => 'input-medium', 'label' => 'Contato', 'options' => $tipos_contato) ?>
        <?php if ($this->action != 'incluir'): ?>
            <?php $options = array_merge($options, array('disabled' => true)) ?>
        <?php endif; ?>
        <?php echo $this->BForm->input('ReguladorEndereco.codigo_tipo_contato', $options); ?>
    <?php endif; ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('ReguladorEndereco.codigo'); ?>
    <?php echo $this->BForm->input('VEndereco.endereco_cep', array('class' => 'evt-endereco-cep input-small', 'label' => 'CEP')); ?>
    <?php echo $this->BForm->input('ReguladorEndereco.codigo_endereco', array('label' => 'Endereço', 'class' => 'input-xxlarge evt-endereco-codigo', 'options' => $enderecos, 'empty' => 'Selecione um endereço..')); ?>
    <div class="clear">
        <?php echo $this->BForm->input('ReguladorEndereco.numero', array('class' => 'input-mini evt-endereco-numero', 'size' => 6, 'label' => 'Número')); ?>
        <?php echo $this->BForm->input('ReguladorEndereco.complemento', array('class' => 'input-medium complemento', 'label' => 'Complemento')); ?>
    </div>
</div>