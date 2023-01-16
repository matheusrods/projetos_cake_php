<div class='row-fluid inline'>
	<?php  echo $this->BForm->hidden('codigo');?>
  <?php echo $this->BForm->input('codigo_parametro_pai', array('label' => 'Categoria','options' =>$categorias ,'class' => 'input-medium', 'empty' => '' )) ?>
  <?php echo $this->BForm->input('descricao', array('label' => 'Descrição','class' => 'input-xxlarge')) ?>
  
</div>
<div class='form-actions'>
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary'));?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
	 jQuery(document).ready(function(){
   		setup_mascaras();
   	});', 
    false
  ); 
?>

  