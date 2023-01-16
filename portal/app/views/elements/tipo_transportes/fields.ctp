 <div class="well">
       
    <div class='row-fluid inline'>
      <?php echo $this->BForm->hidden('ttra_codigo') ?>
      <?php echo $this->BForm->input('ttra_descricao', array('label' => 'Descrição', 'class' => 'input-xlarge')); ?>
    </div>  
    
<div class='form-actions'>
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('controller' => 'tipo_transportes', 'action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>