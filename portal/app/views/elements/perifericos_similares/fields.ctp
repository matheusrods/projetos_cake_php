<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('id'); ?>
    <?php echo $this->BForm->input('pesi_ppad_codigo', array('class' => 'input-xlarge', 'label' => 'Periférico', 'options' => $ppad_codigos, 'empty' => 'Selecione')); ?>
    <?php echo $this->BForm->input('pesi_ppad_codigo_similar', array('class' => 'input-xlarge', 'label' => 'Similar', 'options' => $ppad_codigos, 'empty' => 'Selecione')); ?>
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>    
<?php echo $this->BForm->end(); ?>