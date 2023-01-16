<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('codigo');?>
    <?php echo $this->BForm->hidden('codigo_cliente'); ?>
    <?php echo $this->BForm->input('codigo_representante', array('label' => 'Representantes', 'options' => $representantes, 'empty' => 'selecione um representante', 'class' => 'input-xxlarge')); ?>
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Vincular Representante', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php  echo $this->BForm->end() ?>