<div class="row-fluid">
	<span class="span12 span-right">
		<?php echo $html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'corretoras_enderecos','action' => 'incluir', $this->data['Corretora']['codigo'],1), array('escape' => false, 'class' => 'btn btn-success dialog', 'title' => 'Incluir Endereço')) ?>
	</span>
</div>
<div id="endereco-corretora" class="grupo"></div>