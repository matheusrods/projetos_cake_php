<div class='row-fluid inline'>
	<?php  echo $this->BForm->hidden('codigo');?>
  <?php echo $this->BForm->input('nivel', array('label' => 'Nivel','class' => 'input-medium')) ?>
  <?php echo $this->BForm->input('pontos', array('label' => ' % de Pontos', 'class' => 'input-small numeric just-number','maxlength'=>'false' )); ?>    
    
  <?php echo $this->BForm->input('valor', array('label' => 'Valor', 'class' => 'input-small  numeric moeda','maxlength'=>'false', 'value'=> $this->Buonny->moeda($parametro['ParametroScore']['valor'], array('places'=>2)))) /*array('places'=>2) é a quantidade de casa decimal depois da virgula exemplo 0,00 para exibição  */?> 
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

  