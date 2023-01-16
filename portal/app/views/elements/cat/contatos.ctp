<div class="row-fluid">
	<span class="span12 span-right">
	    
	     <?php echo $this->BMenu->linkOnClick('<i class="icon-plus icon-white"></i>', array('controller' => 'corretoras_contatos', 'action' => 'incluir', $this->data['Corretora']['codigo']), array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Incluir Contato', 'onclick' => "return open_dialog(this, 'Contato', 960)")); ?>
	</span>
</div>
<div id="contatos-corretora" class="grupo"></div> 