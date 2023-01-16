<div class='row-fluid inline'>
    <?php echo $this->BForm->hidden('codigo'); ?>
    <?php echo $this->BForm->input('descricao', array('label' => false, 'placeHolder' => 'descrição', 'class' => 'input-xlarge')); ?>
</div>
<div class='form-actions'>
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>