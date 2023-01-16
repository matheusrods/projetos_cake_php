<div class="row-fluid">
	<span class="span12 span-right">			
	<?php 
	    echo $this->BMenu->linkOnClick('<i class="icon-plus icon-white"></i>', array('controller' => 'reguladores_contatos', 'action' => 'incluir', $this->data['Regulador']['codigo']), array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Incluir Contato', 'onclick' => "return open_dialog(this, 'Contato', 960)")); ?>
	</span>	   
</div>
<div id="contatos-reguladores" class="grupo"></div> 
<?= $this->Javascript->codeBlock("
	$(function(){
		var codigo_regulador = {$this->data['Regulador']['codigo']};
		var element_div      = '#contatos-reguladores'
	    var div = jQuery(element_div);
	    bloquearDiv(div);
	    div.load(baseUrl + 'reguladores_contatos/contatos_por_regulador/' + codigo_regulador + '/' + Math.random() );
	});");?>