<div class = 'form-procurar'>
	<?= $this->element('/filtros/fontes_geradoras') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'fontes_geradoras', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novas Fontes Geradoras'));?>
</div>
<div class='lista'></div>
