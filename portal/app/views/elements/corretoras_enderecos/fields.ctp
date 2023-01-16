<div class="row-fluid inline">
    <?php if ($this->name == 'CorretorasEnderecos'): ?>
        <?php $options = array('class' => 'input-medium', 'label' => 'Contato', 'options' => $tipos_contato) ?>
        <?php if ($this->action != 'incluir'): ?>
            <?php $options = array_merge($options, array('disabled' => true)) ?>
        <?php endif; ?>
        <?php echo $this->BForm->input('CorretoraEndereco.codigo_tipo_contato', $options); ?>
    <?php endif; ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('CorretoraEndereco.codigo'); ?>
    <?php echo $this->BForm->input('CorretoraEndereco.cep', array('class' => 'evt-endereco-cep input-small', 'label' => 'CEP')); ?>
</div>
<div class="row-fluid inline">
    <div >
    <?php   echo $this->BForm->input(   'CorretoraEndereco.estado_descricao', 
                                    array(  'class' => 'input-mini evt-endereco-estado', 
                                            'label' => 'UF',
                                            'type' => 'select',
                                            'options' => $estados)); ?>
    </div>
    <div >
    <?php                                                                     
        echo $this->BForm->input(   'CorretoraEndereco.cidade',
                                    array(  'class' => 'input evt-endereco-cidade', 
                                            'size' => 60,
                                            'label' => 'Cidade')); 

    ?>
    </div>
    <div >
    <?php                                                                     
        echo $this->BForm->input(   'CorretoraEndereco.bairro',
                                    array(  'class' => 'input-meduim evt-endereco-bairro', 
                                            'size' => 60,
                                            'label' => 'Bairro')); 

    ?>
    </div>
</div>
<div class="row-fluid inline">
    <div >
    <?php                                                                     
        echo $this->BForm->input(   'CorretoraEndereco.logradouro',
                                    array(  'class' => 'input-max evt-endereco-lagradouro', 
                                            'size' => 60,
                                            'label' => 'Logradouro')); 

    ?>
    </div>
    <div class="clear">
        <?php echo $this->BForm->input('CorretoraEndereco.numero', array('class' => 'input-mini evt-endereco-numero', 'size' => 6, 'label' => 'Número')); ?>
        <?php echo $this->BForm->input('CorretoraEndereco.complemento', array('class' => 'input-medium complemento', 'label' => 'Complemento')); ?>
    </div>
</div>