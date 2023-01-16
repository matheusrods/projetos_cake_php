 <div class="well">
    <div class='row-fluid inline'>
      <?php echo $this->BForm->hidden('codigo') ?>
      <?php echo $this->BForm->input('codigo_status_proposta_credenciamento', array('label' => false, 'class' => 'input-xxlarge', 'default' => 1,'options' => $array_status)); ?>
      <?php echo $this->BForm->input('ativo', array('label' => false, 'class' => 'input-large', 'default' => '','options' => $array_cadastro)); ?>
    </div>  
</div>    
<div class='form-actions'>
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('controller' => 'propostas_credenciamento', 'action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>