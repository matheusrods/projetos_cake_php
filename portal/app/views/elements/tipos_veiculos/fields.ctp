 <div class="well">
    <div class='row-fluid inline'>
      <?php if(empty($this->data['TTveiTipoVeiculo']['tvei_codigo'])): ?>
      	<?php echo $this->BForm->hidden('tvei_codigo') ?>
      <?php else: ?>
      	<?php echo $this->BForm->input('tvei_codigo', array('label' => 'Código', 'class' => 'input-xlarge', 'readonly'=>true)) ?>
      <?php endif; ?>
	  <?php echo $this->BForm->input('tvei_descricao', array('label' => 'Descrição', 'class' => 'input-xlarge')); ?>
    </div>  
    
<div class='form-actions'>
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?php echo $html->link('Voltar', array('controller' => 'tipos_veiculos', 'action' => 'index'), array('class' => 'btn')); ?>
</div>