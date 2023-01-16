<div class="row-fluid">
	<span class="span12 span-right">
    <?= $this->BMenu->linkOnClick('<i class="icon-plus icon-white"></i>', 
		array('controller' => 'clientes_contatos', 'action' => 'incluir', $this->data['Cliente']['codigo'] ), 
		array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Incluir Contato', 'onclick' => "return open_dialog(this, 'Contato', 960)")
	)?>
	</span>
</div>
<div id="contatos-cliente" class="grupo"></div> 