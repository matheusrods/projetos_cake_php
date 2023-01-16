<?php echo $this->BForm->create('AparelhoAudiometrico', array('url' => array('controller' => 'aparelhos_audiometricos','action' => 'incluir'))); ?>
<?php echo $this->element('aparelhos_audiometricos/fields', array('edit_mode' => false)); ?>
<div class='form-actions'>
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', array('controller' => 'aparelhos_audiometricos', 'action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>