<?php echo $this->BForm->create('Usuario', array('action' => 'incluir')); ?>

<?php echo $this->element('usuarios/fields'); ?>

<div class="form-actions">
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->BForm->end(); ?>

<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?>
