<?php echo $this->BForm->create('MotivosAfastamentoExterno', array(
    'url' => array(
        'controller' => 'motivos_afastamento',
        'action' => 'salvar_externo'
    ))); ?>
<?php echo $this->element('motivos_afastamento/fields_externo', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>