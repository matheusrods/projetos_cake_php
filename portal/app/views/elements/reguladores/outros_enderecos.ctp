<div class="row-fluid">
	<span class="span12 span-right">
		<?php echo $html->link('<i class="icon-plus icon-white"></i>', 
		array(
			'controller' => 'reguladores_enderecos',
			'action' 	 => 'incluir', 
			$this->data['Regulador']['codigo']
		), array('escape' => false, 
		'class' => 'btn btn-success dialog', 'title' => 'Incluir Endereço')) ?>
	</span>
</div>
<div id="endereco-regulador" class="grupo"></div>