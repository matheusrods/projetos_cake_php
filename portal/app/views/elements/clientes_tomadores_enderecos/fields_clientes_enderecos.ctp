<?$edit_mode=isset($edit_mode)?$edit_mode:NULL;?>
<div class="row-fluid inline">
    <?php
        $exibir_repetir_endereco = ($this->params['controller'] == strtolower('clientes') && $this->params['action'] == strtolower('incluir'));
        $options = array('class' => 'input-medium', 'label' => 'Contato', 'options' => $tipos_contato);
    ?>

    <?php if ($exibir_repetir_endereco == true): ?>
        <?php $options = array_merge($options, array('disabled' => true)) ?>
    <?php endif; ?>

    <?php if ($this->name == 'ClientesEnderecos'): ?>
        <?php echo $this->BForm->input('ClienteEndereco.codigo_tipo_contato', $options); ?>
    <?php endif; ?>

    <?php if ($exibir_repetir_endereco == true): ?>
        <?php echo $this->BForm->input('Outros.repetir_para', array('class' => 'checkbox inline', 'options' => $tipos_contato, 'multiple' => 'checkbox', 'label' => 'Repetir endereço para')); ?>
    <?php endif; ?>

</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('ClienteEndereco.codigo'); ?>
    <?php echo $this->BForm->hidden('ClienteEndereco.formulario', array('value' => 'ClienteEndereco')); ?>
    <?php echo $this->BForm->input('ClienteEndereco.cep', array('class' => 'endereco-cep input-small', 'label' => 'CEP')); ?>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('ClienteEndereco.logradouro', array('label' => 'Logradouro', 'class' => 'input-large endereco-logradouro')); ?>
        <?php echo $this->BForm->input('ClienteEndereco.bairro', array('label' => 'Bairro', 'class' => 'input-medium endereco-bairro')); ?>
        <?php echo $this->BForm->input('ClienteEndereco.cidade', array('label' => 'Cidade', 'class' => 'input-medium endereco-cidade')); ?>
        <?php echo $this->BForm->input('ClienteEndereco.estado_descricao', array('label' => 'Estado', 'class' => 'input-small endereco-estado')); ?>
        <?php echo $this->BForm->input('ClienteEndereco.estado_abreviacao', array('label' => 'Estado Abreviação', 'class' => 'input-small endereco-estado-abreviacao')); ?>
    </div>
</div>
<div class="row-fluid inline">
        <?php echo $this->BForm->input('ClienteEndereco.numero', array('class' => 'input-mini evt-endereco-numero', 'size' => 6, 'maxlength' => 6, 'label' => 'Número')); ?>
        <?php echo $this->BForm->input('ClienteEndereco.complemento', array('class' => 'input-medium complemento', 'label' => 'Complemento')); ?>
</div>
<div class="form-actions">
      <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
      <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>  
