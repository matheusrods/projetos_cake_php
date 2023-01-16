<?php $options = isset($options) ? $options : array(); ?>
<div class="row-fluid inline">
    <?php
        $enderecos = '1';
        $exibir_repetir_endereco = ($this->params['controller'] == strtolower('proprietarios') && $this->params['action'] == strtolower('incluir'));        
    ?>
    <?php if ($exibir_repetir_endereco == true): ?>
        <?php $options = array_merge($options, array('disabled' => true)) ?>
    <?php endif; ?>

    <?php if ($this->name == 'ProprietariosEnderecos'): ?>
        <?php echo $this->BForm->input('ProprietarioEndereco.codigo_tipo_contato', $options); ?>
    <?php endif; ?>
</div>
<div class="row-fluid inline cep-data">
    <h5>Endereço</h5>
    <?php echo $this->Buonny->input_cep_endereco($this, array(), $combo, true, false, 'ProprietarioEndereco'); ?>
</div>
