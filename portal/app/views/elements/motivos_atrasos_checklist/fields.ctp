 <div class="well">
    <div class='row-fluid inline'>
      <?php if(empty($this->data['TCmatChecklistMotivoAtraso']['cmat_codigo'])): ?>
      	<?php echo $this->BForm->hidden('cmat_codigo') ?>
      <?php else: ?>
      	<?php echo $this->BForm->input('cmat_codigo', array('label' => 'Código', 'class' => 'input-xlarge', 'readonly'=>true)) ?>
      <?php endif; ?>
	  <?php echo $this->BForm->input('cmat_descricao', array('label' => 'Descrição', 'class' => 'input-xlarge')); ?>
  </div>  
</div>
<div class='form-actions'>
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?php echo $html->link('Voltar', array('controller' => 'motivos_atrasos_checklist', 'action' => 'index'), array('class' => 'btn')); ?>
</div>