<div class = 'form-procurar'>
	<?= $this->element('/filtros/sms') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->Link('<span class="icon-download-alt"></span>&nbsp;Importar SMS&nbsp;', array('action' => 'importar'), array('class'  => 'button', 'escape' => false,));?>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('controller' => 'Sms', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar SMS'));?>
</div>
<div class='lista'></div>
