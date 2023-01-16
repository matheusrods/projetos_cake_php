<?php echo $this->BForm->create('AparelhoAudiometrico', array('url' => array('controller' => 'cliente_aparelho_audiometrico', 'action' => 'editar', $codigo, $codigo_cliente, $codigo_apaudi_cliente), 'type' => 'post'));?>
<?php echo $this->element('aparelhos_audiometricos/fields', array('edit_mode' => true)); ?>

<?php echo $this->BForm->hidden('codigo_matriz', array('value' => $codigo_cliente));?>
<?php echo $this->BForm->hidden('codigo_capaudi_cliente', array('value' => $codigo_apaudi_cliente));?>

<div class="row fluid">
    <div class="span12">
        <span class="label label-success">Fornecedores/Prestadores: </span>
    </div>
</div>
<div class="row-fluid" style="margin-top: 7px;">
    <div class="span5">
        <?php echo $this->Form->input('from', 
            array(
                'label' => false, 
                'id' => 'multiselect', 
                'options' => '', 
                'class' => 'form-control',
                'multiple' => true, 
                'size' => '8', 
                'style' => 'width: 100%'
            )
        ); ?>
    </div>

    <div class="span2">
        <button type="button" id="multiselect_rightAll" class="btn btn-block"><i class="icon-forward"></i></button>
        <button type="button" id="multiselect_rightSelected" class="btn btn-block"><i class="icon-chevron-right"></i></button>
        <button type="button" id="multiselect_leftSelected" class="btn btn-block"><i class="icon-chevron-left"></i></button>
        <button type="button" id="multiselect_leftAll" class="btn btn-block"><i class="icon-backward"></i></button>
    </div>

    <div class="span5">
        <?php echo $this->Form->input('to', 
            array(
                'label' => false, 
                'id' => 'multiselect_to', 
                'class' => 'form-control valida-campos', 
                'options' => array(), 
                'multiple' => true, 
                'size' => '8', 
                'style' => 'width: 100%'
            )
        ); ?>
    </div>
</div>

<div class='form-actions'>
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', array('controller' => 'cliente_aparelho_audiometrico', 'action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php $this->addScript($this->Buonny->link_js('cliente_aparelho_audiometrico_edit.js')); ?>