<div class="row-fluid">
	<span class="span12 span-right">
			
	<?php 
	    echo $this->BMenu->linkOnClick('<i class="icon-plus icon-white"></i>', array('controller' => 'seguradoras_contatos', 'action' => 'incluir', $this->data['Seguradora']['codigo']), array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Incluir Contato', 'onclick' => "return open_dialog(this, 'Contato', 960)")); ?>
	</span>
	   
</div>
<div id="contatos-seguradoras" class="grupo"></div> 
<?= $this->Javascript->codeBlock("
    	$(function(){
    		carrega_contatos_seguradora('{$this->data['Seguradora']['codigo']}','#contatos-seguradoras');
    	});
    ");
?>