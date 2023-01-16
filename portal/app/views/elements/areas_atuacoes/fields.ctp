<div class='row-fluid inline'>
    <?php echo $this->BForm->hidden('aatu_codigo'); ?>
    <?php echo $this->BForm->input('aatu_descricao', array('label' => false, 'label' => 'Descrição', 'class' => 'input-xxlarge')); ?>
</div>
<div class='form-actions'>
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>