<div class="form-procurar">
	<?php echo $this->element('/filtros/motivos_cancelamentos'); ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('Incluir', array('action' => 'incluir', rand()), array('title' => 'Incluir', 'class' => 'btn btn-success'));?>
</div>

<div id="lista" class="lista">

</div>