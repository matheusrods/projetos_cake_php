<h3>Riscos</h3>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'riscos', 'action' => 'buscar_epi_riscos', $this->data['Epi']['codigo']), array('escape' => false, 'class' => 'btn btn-success dialog_riscos', 'title' =>'Cadastrar Riscos'));?>
</div>
<div id="epi_riscos-lista" class="grupo"></div>
		