 <div class="well">
    <div class='row-fluid inline'>
      <?php echo $this->BForm->hidden('codigo') ?>
      <?php echo $this->BForm->input('descricao', array('label' => 'Descrição', 'class' => 'input-xlarge')); ?>
      

      <?php 
      	if($this->data['GrupoEconomico']['editar']){
      		echo $this->BForm->input('codigo_cliente', array('label' => 'Cliente', 'class' => 'input-xlarge', 'readonly' => 'readonly')); 
      	}else{
      		echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente','GrupoEconomico');
      	}
      	?>     
    </div>  
</div>    
<div class='form-actions'>
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('controller' => 'grupos_economicos', 'action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>