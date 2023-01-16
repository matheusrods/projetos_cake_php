<div class="row-fluid">
	<span class="span12 span-right">			
	<?php 
	    echo $this->BMenu->linkOnClick('<i class="icon-plus icon-white"></i>', array('controller' => 'prestadores_contatos', 'action' => 'incluir', $this->data['Prestador']['codigo']), array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Incluir Contato', 'onclick' => "return open_dialog(this, 'Contato', 960)")); ?>
	</span>	   
</div>
<div id="contatos-prestadores" class="grupo"></div> 
<?= $this->Javascript->codeBlock("
	$(function(){
		var codigo_prestador = {$this->data['Prestador']['codigo']};
		var element_div      = '#contatos-prestadores'
	    var div = jQuery(element_div);
	    bloquearDiv(div);
	    div.load(baseUrl + 'prestadores_contatos/contatos_por_prestador/' + codigo_prestador + '/' + Math.random() );
	});");?>