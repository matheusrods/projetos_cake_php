<div class = 'form-procurar'>
	<?= $this->element('/filtros/mercadorias') ?> 
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>Incluir', array('controller' => $this->name, 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Mercadoria'));?>
</div>
<div class='lista'></div>
<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>
