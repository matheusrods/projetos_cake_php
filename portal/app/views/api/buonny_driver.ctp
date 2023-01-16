<div class="well">
	<?php echo $this->Html->link('Atualizar',array('controller' => 'api','action' => 'buonny_driver'), array('class' => 'btn btn-primary', 'title' => 'Atualizar Tela')); ?>
	<?php echo $this->Html->link('Apagar',array('controller' => 'api','action' => 'buonny_driver', 1), array('class' => 'btn', 'title' => 'Limpar Arquivo'), 'Confirma exclusão?'); ?>
</div>
<?php pr($contents); ?>
<div class="well">
	<?php echo $this->Html->link('Atualizar',array('controller' => 'api','action' => 'buonny_driver'), array('class' => 'btn btn-primary', 'title' => 'Atualizar Tela')); ?>
	<?php echo $this->Html->link('Apagar',array('controller' => 'api','action' => 'buonny_driver', 1), array('class' => 'btn', 'title' => 'Limpar Arquivo'), 'Confirma exclusão?'); ?>
</div>
<?php /*echo $this->Javascript->codeBlock("
  setTimeout(function(){
     window.location.reload(1);
  }, 2000);
")*/ ?>